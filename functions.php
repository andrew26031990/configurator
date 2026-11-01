<?php
/**
 * Подключение к базе и запросы к ней.
 *
 * Все запросы, принимающие внешние данные, идут через prepared statements
 * (db_query / db_rows / db_rows_by_id). Конкатенация строк в SQL здесь
 * больше не используется.
 */

require_once __DIR__ . '/security.php';
require_once __DIR__ . '/permissions.php';
require_once __DIR__ . '/order.php';
require_once __DIR__ . '/block_sort.php';
require_once __DIR__ . '/compat.php';
require_once __DIR__ . '/analytics.php';

header('Content-Type: text/html; charset=UTF-8');

/* ------------------------------------------------------------------ */
/*  Соединение                                                         */
/* ------------------------------------------------------------------ */

/**
 * @return mysqli
 */
function db()
{
    static $connection = null;

    if ($connection instanceof mysqli) {
        return $connection;
    }

    // Ошибки mysqli как исключения: без этого неудачный запрос молча
    // возвращает false и код идёт дальше с пустыми данными.
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    try {
        // db.port задаётся только там, где база не на штатном 3306
        // (например, локальная MariaDB на 3307). На проде ключа нет —
        // передаём null, и mysqli берёт порт по умолчанию.
        $port = config_get('db.port');

        $connection = new mysqli(
            (string) config_get('db.host', 'localhost'),
            (string) config_get('db.user'),
            (string) config_get('db.pass'),
            (string) config_get('db.name'),
            $port === null ? null : (int) $port
        );
        $connection->set_charset((string) config_get('db.charset', 'utf8'));
    } catch (Throwable $e) {
        error_log('DB connect failed: ' . $e->getMessage());
        http_response_code(503);
        exit('Сервис временно недоступен');
    }

    return $connection;
}

// Обратная совместимость: старый код повсюду обращается к $mysqli.
$mysqli = db();

/* ------------------------------------------------------------------ */
/*  Хелперы запросов                                                   */
/* ------------------------------------------------------------------ */

/**
 * @param  array<int,mixed> $params
 * @return string
 */
function db_types(array $params)
{
    $types = '';

    foreach ($params as $param) {
        if (is_int($param)) {
            $types .= 'i';
        } elseif (is_float($param)) {
            $types .= 'd';
        } else {
            $types .= 's';
        }
    }

    return $types;
}

/**
 * Выполняет запрос с параметрами.
 *
 * @param  mysqli           $db
 * @param  string           $sql
 * @param  array<int,mixed> $params
 * @return mysqli_stmt
 */
function db_query($db, $sql, array $params = array())
{
    $stmt = $db->prepare($sql);

    if ($params) {
        $stmt->bind_param(db_types($params), ...$params);
    }

    $stmt->execute();

    return $stmt;
}

/**
 * @param  mysqli           $db
 * @param  string           $sql
 * @param  array<int,mixed> $params
 * @return array<int,array<string,mixed>>
 */
function db_rows($db, $sql, array $params = array())
{
    $stmt   = db_query($db, $sql, $params);
    $result = $stmt->get_result();
    $rows   = $result ? $result->fetch_all(MYSQLI_ASSOC) : array();

    $stmt->close();

    return $rows;
}

/**
 * Тот же db_rows, но массив индексируется значением колонки $key —
 * так исторически возвращали данные все функции ниже.
 *
 * @param  mysqli           $db
 * @param  string           $sql
 * @param  array<int,mixed> $params
 * @param  string           $key
 * @return array<int|string,array<string,mixed>>
 */
function db_rows_by_id($db, $sql, array $params = array(), $key = 'id')
{
    $out = array();

    foreach (db_rows($db, $sql, $params) as $row) {
        if (array_key_exists($key, $row)) {
            $out[$row[$key]] = $row;
        } else {
            $out[] = $row;
        }
    }

    return $out;
}

/**
 * @param  mysqli           $db
 * @param  string           $sql
 * @param  array<int,mixed> $params
 * @return array<string,mixed>|null
 */
function db_row($db, $sql, array $params = array())
{
    $rows = db_rows($db, $sql, $params);

    return $rows ? $rows[0] : null;
}

/**
 * @param  mysqli           $db
 * @param  string           $sql
 * @param  array<int,mixed> $params
 * @return int число затронутых строк
 */
function db_exec($db, $sql, array $params = array())
{
    $stmt     = db_query($db, $sql, $params);
    $affected = $stmt->affected_rows;

    $stmt->close();

    return $affected;
}

/**
 * @param  mysqli           $db
 * @param  string           $sql
 * @param  array<int,mixed> $params
 * @return int
 */
function db_count($db, $sql, array $params = array())
{
    $row = db_row($db, $sql, $params);

    return $row ? (int) reset($row) : 0;
}

/* ------------------------------------------------------------------ */
/*  CONFIGURATOR                                                       */
/* ------------------------------------------------------------------ */

function getRootNodesZero($mysqli)
{
    return db_rows_by_id($mysqli, 'SELECT * FROM `tree` WHERE parent_id = 1 AND enabled = 1');
}

function getTitle($mysqli, $link)
{
    return db_rows_by_id($mysqli, 'SELECT * FROM `tree` WHERE link = ?', array((string) $link));
}

function getRootNodesFirst($mysqli, $cat)
{
    return db_rows_by_id(
        $mysqli,
        'SELECT child.* FROM `tree` AS child
           JOIN `tree` AS parent ON parent.id = child.parent_id
          WHERE parent.link = ? AND child.enabled = 1',
        array((string) $cat)
    );
}

function getRootNodesSecond($mysqli, $id)
{
    return db_rows_by_id(
        $mysqli,
        'SELECT id, name, translit, image FROM `tree` WHERE parent_id = ? AND enabled = 1 ORDER BY sort',
        array((int) $id)
    );
}

function getAllRootNodesSecond($mysqli, $id)
{
    return db_rows_by_id($mysqli, 'SELECT id, name FROM `tree` WHERE parent_id = ?', array((int) $id));
}

function getAllChildNodes($mysqli, $cat)
{
    return db_rows_by_id(
        $mysqli,
        "SELECT * FROM `tree` WHERE group_id = ? AND level = 3 AND enabled = 1 ORDER BY sort",
        array((string) $cat)
    );
}

function getAllProductsByTreeId($mysqli, $id)
{
    return db_rows_by_id(
        $mysqli,
        'SELECT p.id, p.name, p.price, p.description, p.image, p.socket, p.mem_type
           FROM tree t
           JOIN tree_prod tp ON t.id = tp.tree_id
           JOIN products  p  ON p.id = tp.prod_id
          WHERE tp.tree_id = ?
          ORDER BY tp.sort_order, p.price',
        array((int) $id)
    );
}

function getFiltersByTreeId($mysqli, $id)
{
    return db_rows_by_id(
        $mysqli,
        'SELECT f.* FROM tree t
           JOIN tree_filter tf ON t.id = tf.tree_id
           JOIN filters     f  ON f.id = tf.filter_id
          WHERE tf.tree_id = ?',
        array((int) $id)
    );
}

/* ------------------------------------------------------------------ */
/*  ADMIN                                                              */
/* ------------------------------------------------------------------ */

function getAllProducts($mysqli)
{
    return db_rows_by_id(
        $mysqli,
        'SELECT prod.id, prod.name, prod.description, prod.price, prod.image, filter.f_name
           FROM `products` AS prod
           JOIN filters AS filter ON prod.f_id = filter.id'
    );
}

function getAllConstructors($mysqli)
{
    return db_rows_by_id(
        $mysqli,
        'SELECT id, title, link, image, price, description FROM `sborki`'
    );
}

function getAllFilters($mysqli)
{
    return db_rows_by_id($mysqli, 'SELECT * FROM filters');
}

function getAllFiltersByType($mysqli, $type)
{
    return db_rows_by_id(
        $mysqli,
        'SELECT filters.id AS f_id, filters.f_name
           FROM tree_filter
           JOIN filters ON tree_filter.filter_id = filters.id
          WHERE tree_filter.tree_id = ?',
        array((int) $type),
        'f_id'
    );
}

function getNodes($mysqli, $level)
{
    return db_rows_by_id($mysqli, 'SELECT * FROM tree WHERE level = ?', array((int) $level));
}

function getTreeFilterRelations($mysqli)
{
    return db_rows_by_id(
        $mysqli,
        'SELECT tf.id,
                (SELECT name FROM `tree` WHERE link = t.group_id LIMIT 1) AS sborka,
                t.name, f.f_name, tf.tree_id, tf.filter_id
           FROM `tree_filter` AS tf
           JOIN `tree`    AS t ON tf.tree_id = t.id
           JOIN `filters` AS f ON tf.filter_id = f.id'
    );
}

function getTreeProdRelations($mysqli)
{
    return db_rows_by_id(
        $mysqli,
        'SELECT tp.id,
                (SELECT name FROM `tree` WHERE link = t.group_id LIMIT 1) AS sborka,
                t.name, p.name AS product, tp.tree_id, tp.prod_id
           FROM `tree_prod` AS tp
           JOIN `tree`     AS t ON tp.tree_id = t.id
           JOIN `products` AS p ON tp.prod_id = p.id'
    );
}

function CountProducts($mysqli)
{
    return db_count($mysqli, 'SELECT COUNT(*) FROM products');
}

function CountFilters($mysqli)
{
    return db_count($mysqli, 'SELECT COUNT(*) FROM filters');
}

function CountSborka($mysqli)
{
    return db_count($mysqli, 'SELECT COUNT(*) FROM sborki');
}

/**
 * Товаров реально привязано к существующим нодам дерева (разложено по категориям).
 */
function CountProductsInCategories($mysqli)
{
    return db_count(
        $mysqli,
        'SELECT COUNT(DISTINCT tp.prod_id)
           FROM tree_prod tp
           JOIN products p ON p.id = tp.prod_id
           JOIN tree t ON t.id = tp.tree_id'
    );
}

/**
 * Товаров без категории (не привязаны ни к одной существующей ноде дерева).
 */
function CountProductsWithoutCategory($mysqli)
{
    return db_count(
        $mysqli,
        'SELECT COUNT(*) FROM products p
          WHERE NOT EXISTS (
              SELECT 1 FROM tree_prod tp
                JOIN tree t ON t.id = tp.tree_id
               WHERE tp.prod_id = p.id
          )'
    );
}

/**
 * Список товаров без категории (для отчёта).
 */
function getProductsWithoutCategory($mysqli)
{
    return db_rows(
        $mysqli,
        'SELECT p.id, p.name, p.price, p.image, p.description, f.f_name
           FROM products p
           LEFT JOIN filters f ON f.id = p.f_id
          WHERE NOT EXISTS (
              SELECT 1 FROM tree_prod tp
                JOIN tree t ON t.id = tp.tree_id
               WHERE tp.prod_id = p.id
          )
          ORDER BY p.id'
    );
}

/**
 * Типы товара (level 3), сгруппированные по сборкам (level 1) — для <optgroup> в мультиселекте.
 * Структура: [ ['build' => '...', 'types' => [ ['id'=>, 'name'=>], ... ] ], ... ]
 */
function getProductTypesGrouped($mysqli)
{
    $groups = array();
    $builds = db_rows($mysqli, 'SELECT id, name FROM tree WHERE parent_id = 1 ORDER BY sort');

    foreach ($builds as $b) {
        $types = db_rows(
            $mysqli,
            'SELECT id, name FROM tree
              WHERE parent_id IN (SELECT id FROM tree WHERE parent_id = ?)
              ORDER BY sort',
            array((int) $b['id'])
        );

        if ($types) {
            $groups[] = array('build' => $b['name'], 'types' => $types);
        }
    }

    return $groups;
}

/**
 * Массив tree_id (узлов типа товара), к которым привязан товар.
 *
 * @return array<int,int>
 */
function getProductTreeIds($mysqli, $prodId)
{
    $ids = array();

    foreach (db_rows($mysqli, 'SELECT tree_id FROM tree_prod WHERE prod_id = ?', array((int) $prodId)) as $r) {
        $ids[] = (int) $r['tree_id'];
    }

    return $ids;
}

/**
 * Массив tree_id (узлов типа товара), к которым привязан фильтр.
 *
 * @return array<int,int>
 */
function getFilterTreeIds($mysqli, $filterId)
{
    $ids = array();

    foreach (db_rows($mysqli, 'SELECT tree_id FROM tree_filter WHERE filter_id = ?', array((int) $filterId)) as $r) {
        $ids[] = (int) $r['tree_id'];
    }

    return $ids;
}

/**
 * Кол-во товаров по сборкам (level-1 узлы) — для графика на дашборде.
 * Ветка "Готовые сборки" (link = kompyutery) наполняется из таблицы sborki,
 * а не из tree_prod, поэтому для неё берём CountSborka.
 */
function getProductsPerBuild($mysqli)
{
    $out    = array();
    $builds = db_rows($mysqli, 'SELECT id, name, link FROM tree WHERE parent_id = 1 ORDER BY sort');

    foreach ($builds as $b) {
        if ($b['link'] === 'kompyutery') {
            $out[] = array('name' => $b['name'], 'count' => CountSborka($mysqli));
            continue;
        }

        $count = db_count(
            $mysqli,
            'WITH RECURSIVE sub AS (
                 SELECT id FROM tree WHERE id = ?
                 UNION ALL
                 SELECT t.id FROM tree t JOIN sub ON t.parent_id = sub.id
             )
             SELECT COUNT(DISTINCT tp.prod_id)
               FROM tree_prod tp
               JOIN products p ON p.id = tp.prod_id
              WHERE tp.tree_id IN (SELECT id FROM sub)',
            array((int) $b['id'])
        );

        $out[] = array('name' => $b['name'], 'count' => $count);
    }

    return $out;
}

/* ------------------------------------------------------------------ */
/*  Аутентификация                                                     */
/* ------------------------------------------------------------------ */

/**
 * Проверяет логин и пароль.
 *
 * Принимает пароль В ОТКРЫТОМ ВИДЕ (раньше вызывающий код передавал
 * sha1(md5($pass))). Поддерживает старый формат хеша ради существующих
 * учёток и при первом успешном входе молча перезаписывает его на bcrypt.
 *
 * @param  mysqli $mysqli
 * @param  string $login
 * @param  string $plainPassword
 * @return bool
 */
function CheckLoginPass($mysqli, $login, $plainPassword)
{
    $user = db_row(
        $mysqli,
        'SELECT id, password, is_active FROM users WHERE username = ? LIMIT 1',
        array((string) $login)
    );

    if (!$user) {
        // Считаем «пустой» хеш, чтобы время ответа не выдавало
        // существование пользователя.
        password_verify($plainPassword, '$2y$10$usesomesillystringfore7hnbRJHxXVLeakoG8K30oukPsA.ztMG');
        return false;
    }

    $stored = (string) $user['password'];
    $legacy = sha1(md5($plainPassword));

    if (password_verify($plainPassword, $stored)) {
        $ok = true;
    } elseif (hash_equals($stored, $legacy)) {
        $ok = true;
        // Переводим на нормальный хеш прямо при входе.
        db_exec(
            $mysqli,
            'UPDATE users SET password = ? WHERE id = ?',
            array(password_hash($plainPassword, PASSWORD_DEFAULT), (int) $user['id'])
        );
    } else {
        $ok = false;
    }

    if (!$ok) {
        return false;
    }

    // Отключённый аккаунт в систему не пускаем.
    if ((int) $user['is_active'] !== 1) {
        return false;
    }

    db_exec(
        $mysqli,
        'UPDATE users SET last_login = ? WHERE id = ?',
        array(date('d.m.Y H:i:s'), (int) $user['id'])
    );

    // Возвращаем id пользователя — он нужен для сессии (RBAC).
    return (int) $user['id'];
}

function getAllUsers($mysqli)
{
    return db_rows_by_id($mysqli, 'SELECT id, username, last_login FROM `users`');
}

/* ------------------------------------------------------------------ */
/*  Пользователи и роли (RBAC)                                         */
/* ------------------------------------------------------------------ */

/**
 * Список пользователей с ролями (через запятую) и статусом.
 */
function getAllUsersWithRoles($mysqli)
{
    return db_rows(
        $mysqli,
        "SELECT u.id, u.username, u.last_login, u.is_active,
                COALESCE(GROUP_CONCAT(r.label ORDER BY r.id SEPARATOR ', '), '') AS roles
           FROM users u
           LEFT JOIN user_roles ur ON ur.user_id = u.id
           LEFT JOIN roles r       ON r.id = ur.role_id
          GROUP BY u.id, u.username, u.last_login, u.is_active
          ORDER BY u.username"
    );
}

/**
 * Все роли (для чекбоксов в форме пользователя).
 */
function getAllRoles($mysqli)
{
    return db_rows($mysqli, 'SELECT id, name, label FROM roles ORDER BY id');
}

/**
 * Один пользователь по id (без пароля).
 *
 * @return array<string,mixed>|null
 */
function getUserById($mysqli, $id)
{
    return db_row(
        $mysqli,
        'SELECT id, username, is_active FROM users WHERE id = ? LIMIT 1',
        array((int) $id)
    );
}

/**
 * id ролей пользователя.
 *
 * @return array<int,int>
 */
function getUserRoleIds($mysqli, $id)
{
    $rows = db_rows($mysqli, 'SELECT role_id FROM user_roles WHERE user_id = ?', array((int) $id));
    $out  = array();
    foreach ($rows as $row) {
        $out[] = (int) $row['role_id'];
    }
    return $out;
}

/**
 * Занят ли логин (опционально исключая конкретный id — для редактирования).
 */
function usernameExists($mysqli, $username, $exceptId = 0)
{
    $row = db_row(
        $mysqli,
        'SELECT id FROM users WHERE username = ? AND id <> ? LIMIT 1',
        array((string) $username, (int) $exceptId)
    );
    return (bool) $row;
}

/**
 * Сколько активных пользователей имеют роль users.manage. Нужно, чтобы не
 * оставить систему без единого администратора (защита от самоблокировки).
 */
function countActiveManagers($mysqli)
{
    return db_count(
        $mysqli,
        "SELECT COUNT(DISTINCT u.id)
           FROM users u
           JOIN user_roles ur       ON ur.user_id = u.id
           JOIN role_permissions rp ON rp.role_id = ur.role_id
           JOIN permissions p       ON p.id = rp.permission_id
          WHERE u.is_active = 1 AND p.name = 'users.manage'"
    );
}

/**
 * Имеет ли пользователь право users.manage (через свои роли).
 */
function userHasManage($mysqli, $id)
{
    $n = db_count(
        $mysqli,
        "SELECT COUNT(*)
           FROM user_roles ur
           JOIN role_permissions rp ON rp.role_id = ur.role_id
           JOIN permissions p       ON p.id = rp.permission_id
          WHERE ur.user_id = ? AND p.name = 'users.manage'",
        array((int) $id)
    );
    return $n > 0;
}

function exit_cab()
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        $_SESSION = array();
        session_destroy();
    }
}

/* ------------------------------------------------------------------ */
/*  Заявки из конфигуратора                                            */
/* ------------------------------------------------------------------ */

/**
 * Секрет для подписи метки времени формы заказа.
 * order.secret в config.local.php; если не задан — производный от доступа к базе
 * (он и так секретный и на каждом окружении свой).
 */
function order_form_secret()
{
    $secret = (string) config_get('order.secret', '');
    if ($secret !== '' && $secret !== 'CHANGE_ME') {
        return $secret;
    }
    return hash('sha256', 'order-form|' . config_get('db.pass') . '|' . config_get('db.name') . '|' . APP_ROOT);
}

/**
 * Лимит заявок с одного IP (не по сессии: бот без cookie обошёл бы сессионный).
 * Хранится в storage/order_limits.json, сам IP — только хешем.
 */
function order_rate_allow()
{
    $ip  = isset($_SERVER['REMOTE_ADDR']) ? (string) $_SERVER['REMOTE_ADDR'] : '';
    $key = hash('sha256', 'order|' . $ip);

    $fh = @fopen(storage_path('order_limits.json'), 'c+');
    if (!$fh) {
        // Хранилище недоступно — не блокируем живых клиентов, но фиксируем проблему.
        error_log('order: storage/order_limits.json недоступен');
        return true;
    }

    flock($fh, LOCK_EX);
    $data = json_decode((string) stream_get_contents($fh), true);

    list($ok, $data) = order_rate_check(
        is_array($data) ? $data : array(),
        $key,
        time(),
        (int) config_get('order.min_interval', 30),
        (int) config_get('order.max_per_day', 5)
    );

    ftruncate($fh, 0);
    rewind($fh);
    fwrite($fh, json_encode($data));
    fflush($fh);
    flock($fh, LOCK_UN);
    fclose($fh);

    return $ok;
}

/* ------------------------------------------------------------------ */
/*  Блоки (тип-ноды level 3) и их товары                               */
/* ------------------------------------------------------------------ */

/**
 * Блок — тип-нода дерева (level 3): к ним и только к ним привязаны товары.
 *
 * @return array<string,mixed>|null
 */
function getBlock($mysqli, $treeId)
{
    return db_row($mysqli, 'SELECT id, name, level FROM tree WHERE id = ? AND level = 3', array((int) $treeId));
}

/**
 * Товары блока в порядке витрины.
 *
 * @return array<int,array<string,mixed>>
 */
function getBlockProducts($mysqli, $treeId)
{
    return db_rows(
        $mysqli,
        'SELECT p.id, p.name, p.price, p.image, f.f_name, tp.sort_order
           FROM tree_prod tp
           JOIN products p ON p.id = tp.prod_id
           LEFT JOIN filters f ON f.id = p.f_id
          WHERE tp.tree_id = ?
          ORDER BY tp.sort_order, p.price, p.id',
        array((int) $treeId)
    );
}

/**
 * id товаров блока в текущем порядке.
 *
 * @param  string $by  '' — текущий порядок; 'price' | 'name' | 'id' — сортировка по умолчанию
 * @return array<int,int>
 */
function getBlockProductIds($mysqli, $treeId, $by = '')
{
    $orders = array(
        ''      => 'tp.sort_order, p.price, p.id',
        'price' => 'p.price, p.id',
        'name'  => 'p.name, p.id',
        'id'    => 'p.id',
    );
    $orderBy = isset($orders[$by]) ? $orders[$by] : $orders[''];

    $ids = array();
    foreach (db_rows(
        $mysqli,
        "SELECT p.id FROM tree_prod tp JOIN products p ON p.id = tp.prod_id
          WHERE tp.tree_id = ? ORDER BY {$orderBy}",
        array((int) $treeId)
    ) as $r) {
        $ids[] = (int) $r['id'];
    }

    return $ids;
}

/**
 * Записывает порядок товаров блока: номера подряд 1..N по $orderedIds.
 * Вызывать внутри транзакции.
 */
function saveBlockOrder($mysqli, $treeId, array $orderedIds)
{
    foreach (array_values($orderedIds) as $pos => $id) {
        db_exec(
            $mysqli,
            'UPDATE tree_prod SET sort_order = ? WHERE tree_id = ? AND prod_id = ?',
            array($pos + 1, (int) $treeId, (int) $id)
        );
    }
}

/**
 * Следующий свободный номер в блоке — новый товар встаёт в конец.
 */
function nextBlockSortOrder($mysqli, $treeId)
{
    return db_count($mysqli, 'SELECT COALESCE(MAX(sort_order), 0) + 1 FROM tree_prod WHERE tree_id = ?', array((int) $treeId));
}

/* ------------------------------------------------------------------ */
/*  Журнал действий админки                                            */
/* ------------------------------------------------------------------ */

/**
 * Каталог действий журнала: код => [подпись, цвет бейджа].
 *
 * @return array<string,array{0:string,1:string}>
 */
function audit_actions()
{
    return array(
        'auth.login'                  => array('Вход в админку', 'success'),
        'auth.login_failed'           => array('Неудачный вход', 'warning'),
        'auth.logout'                 => array('Выход', 'secondary'),
        'product.create'              => array('Товар: добавление', 'success'),
        'product.update'              => array('Товар: изменение', 'info'),
        'product.delete'              => array('Товар: удаление', 'danger'),
        'product.bulk_delete'         => array('Товары без категории: удаление', 'danger'),
        'product.delete_all_no_cat'   => array('Товары без категории: удалены все', 'danger'),
        'block.sort'                  => array('Блок: сортировка', 'primary'),
        'block.bulk_delete'           => array('Блок: массовое удаление', 'danger'),
        'filter.create'               => array('Фильтр: добавление', 'success'),
        'filter.update'               => array('Фильтр: изменение', 'info'),
        'filter.delete'               => array('Фильтр: удаление', 'danger'),
        'node.create'                 => array('Категория: добавление', 'success'),
        'node.rename'                 => array('Категория: переименование', 'info'),
        'node.settings'               => array('Категория: настройки', 'info'),
        'node.delete'                 => array('Категория: удаление', 'danger'),
        'sborka.create'               => array('Сборка: добавление', 'success'),
        'sborka.update'               => array('Сборка: изменение', 'info'),
        'sborka.delete'               => array('Сборка: удаление', 'danger'),
        'user.create'                 => array('Пользователь: создание', 'success'),
        'user.update'                 => array('Пользователь: изменение', 'info'),
        'user.delete'                 => array('Пользователь: удаление', 'danger'),
        'product.compat'              => array('Товар: совместимость', 'info'),
        'compat.autofill'             => array('Совместимость: автозаполнение', 'primary'),
    );
}

/**
 * Пишет запись в admin_audit_log: кто, когда, что сделал и над чем.
 *
 * $ctx:
 *   ids     — id затронутых объектов;
 *   tree_id — блок/узел дерева, если действие к нему относится;
 *   details — подробности (для изменений — 'changes' из audit_diff);
 *   actor   — ['id' => ?int, 'username' => string] вместо текущего
 *             пользователя (неудачный вход, когда сессии ещё нет).
 *
 * Ошибка записи журнала не должна ронять само действие — только error_log.
 *
 * @param array<string,mixed> $ctx
 */
function audit_log($mysqli, $action, $summary, array $ctx = array())
{
    $actor = isset($ctx['actor']) ? $ctx['actor'] : current_user();
    $ids   = isset($ctx['ids']) ? array_values(array_map('intval', $ctx['ids'])) : array();

    if (function_exists('mb_substr')) {
        $summary = mb_substr((string) $summary, 0, 500, 'UTF-8');
    }

    try {
        db_exec(
            $mysqli,
            'INSERT INTO admin_audit_log (user_id, username, action, summary, tree_id, item_ids, details, ip)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
            array(
                $actor && isset($actor['id']) ? (int) $actor['id'] : null,
                $actor ? (string) $actor['username'] : '',
                (string) $action,
                (string) $summary,
                isset($ctx['tree_id']) ? (int) $ctx['tree_id'] : null,
                json_encode($ids),
                isset($ctx['details']) ? json_encode($ctx['details'], JSON_UNESCAPED_UNICODE) : null,
                isset($_SERVER['REMOTE_ADDR']) ? (string) $_SERVER['REMOTE_ADDR'] : '',
            )
        );
    } catch (Throwable $e) {
        error_log('audit_log: ' . $e->getMessage());
    }
}

/**
 * Что поменялось: поле => ['было' => ..., 'стало' => ...] только для
 * отличающихся значений. Сравнение строковое: из базы всё приходит строками.
 *
 * @param  array<string,mixed> $before
 * @param  array<string,mixed> $after
 * @return array<string,array<string,mixed>>
 */
function audit_diff(array $before, array $after)
{
    $changes = array();

    foreach ($after as $field => $new) {
        $old = array_key_exists($field, $before) ? $before[$field] : null;

        $a = is_array($old) ? json_encode($old) : (string) $old;
        $b = is_array($new) ? json_encode($new) : (string) $new;

        if ($a !== $b) {
            $changes[$field] = array('было' => $old, 'стало' => $new);
        }
    }

    return $changes;
}

/**
 * Записи журнала с фильтрами и пагинацией.
 *
 * $f: user_id, action, date_from, date_to (Y-m-d), q (поиск по описанию и логину).
 *
 * @param  array<string,mixed> $f
 * @return array{rows: array<int,array<string,mixed>>, total: int}
 */
function getAuditLog($mysqli, array $f, $limit, $offset)
{
    $where  = array();
    $params = array();

    if (!empty($f['user_id'])) {
        $where[]  = 'user_id = ?';
        $params[] = (int) $f['user_id'];
    }
    if (!empty($f['action'])) {
        $where[]  = 'action = ?';
        $params[] = (string) $f['action'];
    }
    if (!empty($f['date_from'])) {
        $where[]  = 'created_at >= ?';
        $params[] = $f['date_from'] . ' 00:00:00';
    }
    if (!empty($f['date_to'])) {
        $where[]  = 'created_at < DATE_ADD(?, INTERVAL 1 DAY)';
        $params[] = (string) $f['date_to'];
    }
    if (isset($f['q']) && $f['q'] !== '') {
        $like     = '%' . addcslashes((string) $f['q'], '%_\\') . '%';
        $where[]  = '(summary LIKE ? OR username LIKE ? OR item_ids LIKE ?)';
        $params[] = $like;
        $params[] = $like;
        $params[] = $like;
    }

    $sqlWhere = $where ? 'WHERE ' . implode(' AND ', $where) : '';

    $total = db_count($mysqli, "SELECT COUNT(*) FROM admin_audit_log {$sqlWhere}", $params);
    $rows  = db_rows(
        $mysqli,
        "SELECT id, user_id, username, action, summary, tree_id, item_ids, details, ip, created_at
           FROM admin_audit_log {$sqlWhere}
          ORDER BY id DESC
          LIMIT " . (int) $limit . ' OFFSET ' . (int) $offset,
        $params
    );

    return array('rows' => $rows, 'total' => $total);
}

/**
 * Логины, встречающиеся в журнале (включая удалённых пользователей) — для фильтра.
 *
 * @return array<int,array<string,mixed>>
 */
function getAuditUsers($mysqli)
{
    return db_rows(
        $mysqli,
        'SELECT user_id, MAX(username) AS username
           FROM admin_audit_log
          WHERE user_id IS NOT NULL
          GROUP BY user_id
          ORDER BY username'
    );
}

/* ------------------------------------------------------------------ */
/*  Совместимость (плата / процессор / ОЗУ)                            */
/* ------------------------------------------------------------------ */

/**
 * Товары блоков с ролью совместимости — для страницы «Совместимость».
 * Товар может лежать в нескольких блоках (Intel/AMD) — берём одну строку на
 * товар, роль — первая найденная.
 *
 * @param  string $role '' — все роли
 * @return array<int,array<string,mixed>>
 */
function getCompatProducts($mysqli, $role = '')
{
    $params = array();
    $where  = "t.compat_role <> ''";
    if ($role !== '') {
        $where   .= ' AND t.compat_role = ?';
        $params[] = (string) $role;
    }

    return db_rows(
        $mysqli,
        "SELECT p.id, p.name, p.socket, p.mem_type,
                MIN(t.compat_role) AS role,
                GROUP_CONCAT(DISTINCT CONCAT(COALESCE((SELECT b.name FROM tree b WHERE b.link = t.group_id LIMIT 1), '?'), ' → ', t.name)
                             ORDER BY t.id SEPARATOR '; ') AS blocks
           FROM products p
           JOIN tree_prod tp ON tp.prod_id = p.id
           JOIN tree t       ON t.id = tp.tree_id
          WHERE {$where}
          GROUP BY p.id, p.name, p.socket, p.mem_type
          ORDER BY role, p.name",
        $params
    );
}

/**
 * Автозаполнение сокета и типа памяти по названиям товаров.
 *
 * Заполняются только пустые поля — ручные правки не перетираются.
 * $apply = false — только отчёт, без записи.
 *
 * @return array{filled: array<int,array<string,mixed>>, unresolved: array<int,array<string,mixed>>}
 */
function compat_autofill($mysqli, $apply)
{
    $filled     = array();
    $unresolved = array();

    foreach (getCompatProducts($mysqli) as $p) {
        $guess = compat_guess($p['name'], $p['role']);
        $set   = array();

        if ($p['socket'] === '' && $guess['socket'] !== '') {
            $set['socket'] = $guess['socket'];
        }
        if ($p['mem_type'] === '' && $guess['mem_type'] !== '') {
            $set['mem_type'] = $guess['mem_type'];
        }

        if ($set) {
            if ($apply) {
                $cols = array();
                foreach (array_keys($set) as $c) {
                    $cols[] = "{$c} = ?";
                }
                db_exec(
                    $mysqli,
                    'UPDATE products SET ' . implode(', ', $cols) . ' WHERE id = ?',
                    array_merge(array_values($set), array((int) $p['id']))
                );
            }
            $filled[] = array('id' => (int) $p['id'], 'name' => $p['name'], 'role' => $p['role'], 'set' => $set);
        }

        $socket = isset($set['socket']) ? $set['socket'] : $p['socket'];
        $mem    = isset($set['mem_type']) ? $set['mem_type'] : $p['mem_type'];
        $needSocket = $p['role'] !== 'ram';

        if (($needSocket && $socket === '') || $mem === '') {
            $unresolved[] = array(
                'id'     => (int) $p['id'],
                'name'   => $p['name'],
                'role'   => $p['role'],
                'blocks' => $p['blocks'],
                'missing' => trim(($needSocket && $socket === '' ? 'сокет ' : '') . ($mem === '' ? 'тип памяти' : '')),
            );
        }
    }

    return array('filled' => $filled, 'unresolved' => $unresolved);
}

/**
 * Заполняет пустые сокет/тип памяти одного товара по названию — вызывается
 * после добавления и правки товара. Роль — по блокам, к которым он привязан.
 */
function compat_autofill_product($mysqli, $prodId)
{
    $p = db_row(
        $mysqli,
        "SELECT p.name, p.socket, p.mem_type, MIN(t.compat_role) AS role
           FROM products p
           JOIN tree_prod tp ON tp.prod_id = p.id
           JOIN tree t ON t.id = tp.tree_id AND t.compat_role <> ''
          WHERE p.id = ?
          GROUP BY p.id, p.name, p.socket, p.mem_type",
        array((int) $prodId)
    );
    if (!$p) {
        return;
    }

    $guess  = compat_guess($p['name'], $p['role']);
    $socket = $p['socket'] !== '' ? $p['socket'] : $guess['socket'];
    $mem    = $p['mem_type'] !== '' ? $p['mem_type'] : $guess['mem_type'];

    if ($socket !== $p['socket'] || $mem !== $p['mem_type']) {
        db_exec($mysqli, 'UPDATE products SET socket = ?, mem_type = ? WHERE id = ?', array($socket, $mem, (int) $prodId));
    }
}

/* ------------------------------------------------------------------ */
/*  Статистика посещаемости и воронка                                  */
/* ------------------------------------------------------------------ */

/**
 * Записывает событие витрины.
 *
 * Посетитель и сессия — из cookie pcm_vid / pcm_sid, которые ставит
 * configurator/js/analytics.js. Нет cookie — значит, JS не выполнялся (бот),
 * событие не пишем. Роботов по User-Agent и самих админов тоже не считаем.
 *
 * @param  array<string,mixed> $extra is_new, referrer
 * @return bool записано ли событие
 */
function analytics_record($mysqli, $event, $cat = '', $detail = '', array $extra = array())
{
    $vid = isset($_COOKIE['pcm_vid']) ? (string) $_COOKIE['pcm_vid'] : '';
    $sid = isset($_COOKIE['pcm_sid']) ? (string) $_COOKIE['pcm_sid'] : '';
    $ua  = isset($_SERVER['HTTP_USER_AGENT']) ? (string) $_SERVER['HTTP_USER_AGENT'] : '';

    if (!analytics_id_ok($vid) || !analytics_id_ok($sid) || analytics_is_bot($ua)) {
        return false;
    }

    // Админ, зашедший на витрину, — не посетитель. Сессию не заводим, если её нет.
    if (!empty($_COOKIE[session_name()]) && is_admin()) {
        return false;
    }

    try {
        // Страховка от накрутки: не больше 500 событий на сессию.
        if (db_count($mysqli, 'SELECT COUNT(*) FROM analytics_events WHERE session_id = ?', array($sid)) >= 500) {
            return false;
        }

        db_exec(
            $mysqli,
            'INSERT INTO analytics_events (day, visitor_id, session_id, event, cat, detail, device, is_new, referrer)
             VALUES (CURDATE(), ?, ?, ?, ?, ?, ?, ?, ?)',
            array(
                $vid,
                $sid,
                (string) $event,
                analytics_clean_token($cat, 64),
                analytics_clean_token($detail, 100),
                analytics_device($ua),
                !empty($extra['is_new']) ? 1 : 0,
                isset($extra['referrer']) ? substr((string) $extra['referrer'], 0, 64) : '',
            )
        );
        return true;
    } catch (Throwable $e) {
        error_log('analytics_record: ' . $e->getMessage());
        return false;
    }
}

/**
 * WHERE для отчёта: период, конфигуратор, устройство.
 *
 * @param  array<string,string> $f from, to (Y-m-d), cat, device
 * @return array{0:string,1:array<int,mixed>}
 */
function analytics_where(array $f)
{
    $where  = array('day BETWEEN ? AND ?');
    $params = array($f['from'], $f['to']);

    if (!empty($f['cat'])) {
        $where[]  = 'cat = ?';
        $params[] = $f['cat'];
    }
    if (!empty($f['device'])) {
        $where[]  = 'device = ?';
        $params[] = $f['device'];
    }

    return array('WHERE ' . implode(' AND ', $where), $params);
}

/**
 * Итоги за период.
 *
 * @return array<string,int>
 */
function getAnalyticsTotals($mysqli, array $f)
{
    list($w, $p) = analytics_where($f);
    $row = db_row(
        $mysqli,
        "SELECT COUNT(DISTINCT visitor_id) AS visitors,
                COUNT(DISTINCT session_id) AS sessions,
                COUNT(DISTINCT CASE WHEN is_new = 1 THEN visitor_id END) AS new_visitors,
                SUM(event = 'page_view') AS page_views,
                SUM(event = 'order_success') AS orders
           FROM analytics_events {$w}",
        $p
    );
    $out = array();
    foreach (array('visitors', 'sessions', 'new_visitors', 'page_views', 'orders') as $k) {
        $out[$k] = $row ? (int) $row[$k] : 0;
    }
    return $out;
}

/**
 * По дням: посетители, сессии, заявки. Дни без событий — нулями.
 *
 * @return array<string,array<string,int>> 'Y-m-d' => [...]
 */
function getAnalyticsDaily($mysqli, array $f)
{
    list($w, $p) = analytics_where($f);
    $rows = db_rows(
        $mysqli,
        "SELECT day,
                COUNT(DISTINCT visitor_id) AS visitors,
                COUNT(DISTINCT session_id) AS sessions,
                SUM(event = 'order_success') AS orders
           FROM analytics_events {$w}
          GROUP BY day",
        $p
    );
    $byDay = array();
    foreach ($rows as $r) {
        $byDay[$r['day']] = $r;
    }

    $out = array();
    for ($t = strtotime($f['from']); $t <= strtotime($f['to']); $t = strtotime('+1 day', $t)) {
        $d = date('Y-m-d', $t);
        $out[$d] = array(
            'visitors' => isset($byDay[$d]) ? (int) $byDay[$d]['visitors'] : 0,
            'sessions' => isset($byDay[$d]) ? (int) $byDay[$d]['sessions'] : 0,
            'orders'   => isset($byDay[$d]) ? (int) $byDay[$d]['orders'] : 0,
        );
    }
    return $out;
}

/**
 * По месяцам за последние $months месяцев (с учётом фильтров конфигуратора и устройства).
 *
 * @return array<int,array<string,mixed>>
 */
function getAnalyticsMonthly($mysqli, array $f, $months = 12)
{
    $range = $f;
    $range['from'] = date('Y-m-01', strtotime('-' . ((int) $months - 1) . ' months', strtotime(date('Y-m-01'))));
    $range['to']   = date('Y-m-d');
    list($w, $p) = analytics_where($range);

    return db_rows(
        $mysqli,
        "SELECT DATE_FORMAT(day, '%Y-%m') AS month,
                COUNT(DISTINCT visitor_id) AS visitors,
                COUNT(DISTINCT session_id) AS sessions,
                COUNT(DISTINCT CASE WHEN is_new = 1 THEN visitor_id END) AS new_visitors,
                SUM(event = 'order_success') AS orders
           FROM analytics_events {$w}
          GROUP BY month
          ORDER BY month DESC",
        $p
    );
}

/**
 * Воронка: для каждой сессии — максимальный шаг (analytics_event_step), затем analytics_funnel().
 * Плюс «где бросили сборку»: последний блок, в котором выбирали товар, у сессий,
 * которые начали собирать (шаг 3–4), но до формы заказа не дошли.
 *
 * @return array{funnel: array<int,array<string,mixed>>, abandon: array<string,int>}
 */
function getAnalyticsFunnel($mysqli, array $f)
{
    list($w, $p) = analytics_where($f);
    $rows = db_rows(
        $mysqli,
        "SELECT session_id, event, cat, detail FROM analytics_events {$w} ORDER BY id",
        $p
    );

    $maxStep   = array();
    $lastBlock = array();
    foreach ($rows as $r) {
        $sid  = $r['session_id'];
        $step = analytics_event_step($r['event'], $r['cat']);
        if (!isset($maxStep[$sid]) || $step > $maxStep[$sid]) {
            $maxStep[$sid] = $step;
        }
        if ($r['event'] === 'component_select' && $r['detail'] !== '') {
            $lastBlock[$sid] = $r['detail'];
        }
    }

    $byStep = array();
    foreach ($maxStep as $step) {
        if ($step > 0) {
            $byStep[$step] = (isset($byStep[$step]) ? $byStep[$step] : 0) + 1;
        }
    }

    $abandon = array();
    foreach ($maxStep as $sid => $step) {
        if (($step === 3 || $step === 4) && isset($lastBlock[$sid])) {
            $abandon[$lastBlock[$sid]] = (isset($abandon[$lastBlock[$sid]]) ? $abandon[$lastBlock[$sid]] : 0) + 1;
        }
    }
    arsort($abandon);

    return array('funnel' => analytics_funnel($byStep), 'abandon' => $abandon);
}

/**
 * Число сессий по значению поля для события: источники, устройства, ошибки формы, блоки.
 *
 * @param  string $field detail | referrer | device | cat
 * @return array<string,int>
 */
function getAnalyticsBreakdown($mysqli, array $f, $event, $field, $onlyDetail = null)
{
    if (!in_array($field, array('detail', 'referrer', 'device', 'cat'), true)) {
        return array();
    }
    list($w, $p) = analytics_where($f);
    $w .= ' AND event = ?';
    $p[] = (string) $event;
    if ($onlyDetail !== null) {
        $w .= ' AND detail = ?';
        $p[] = (string) $onlyDetail;
    }

    $out = array();
    foreach (db_rows(
        $mysqli,
        "SELECT {$field} AS k, COUNT(DISTINCT session_id) AS n FROM analytics_events {$w} GROUP BY {$field} ORDER BY n DESC LIMIT 20",
        $p
    ) as $r) {
        $out[(string) $r['k']] = (int) $r['n'];
    }
    return $out;
}

/**
 * Названия блоков по translit (для отчёта «где бросили сборку»).
 *
 * @return array<string,string>
 */
function getBlockNamesByTranslit($mysqli)
{
    $out = array();
    foreach (db_rows($mysqli, "SELECT translit, MIN(name) AS name FROM tree WHERE level = 3 AND translit <> '' GROUP BY translit") as $r) {
        $out[$r['translit']] = trim($r['name']);
    }
    return $out;
}
