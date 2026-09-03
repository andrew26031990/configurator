<?php
/**
 * Подключение к базе и запросы к ней.
 *
 * Все запросы, принимающие внешние данные, идут через prepared statements
 * (db_query / db_rows / db_rows_by_id). Конкатенация строк в SQL здесь
 * больше не используется.
 */

require_once __DIR__ . '/security.php';

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
        $connection = new mysqli(
            (string) config_get('db.host', 'localhost'),
            (string) config_get('db.user'),
            (string) config_get('db.pass'),
            (string) config_get('db.name')
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
        'SELECT p.id, p.name, p.price, p.description, p.image
           FROM tree t
           JOIN tree_prod tp ON t.id = tp.tree_id
           JOIN products  p  ON p.id = tp.prod_id
          WHERE tp.tree_id = ?
          ORDER BY p.price',
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
    return db_count($mysqli, 'SELECT COUNT(*) FROM tree WHERE level = 0');
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
        'SELECT id, password FROM users WHERE username = ? LIMIT 1',
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

    db_exec(
        $mysqli,
        'UPDATE users SET last_login = ? WHERE id = ?',
        array(date('d.m.Y H:i:s'), (int) $user['id'])
    );

    return true;
}

function getAllUsers($mysqli)
{
    return db_rows_by_id($mysqli, 'SELECT id, username, last_login FROM `users`');
}

function exit_cab()
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        $_SESSION = array();
        session_destroy();
    }
}
