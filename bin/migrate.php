<?php
/**
 * CLI-раннер миграций. Работает ТОЛЬКО из командной строки.
 *
 * Использование:
 *   php bin/migrate.php status            — что применено / что ожидает
 *   php bin/migrate.php up [N]            — накатить все (или N) ожидающих миграций
 *   php bin/migrate.php down [N]          — откатить последнюю (или N) применённых
 *   php bin/migrate.php create "описание" — создать пустой файл миграции
 *   php bin/migrate.php baseline <версия> — пометить миграцию применённой БЕЗ выполнения
 *
 * Миграция — это PHP-файл в migrations/, возвращающий массив с замыканиями
 * up/down, каждое получает mysqli-соединение:
 *
 *   <?php
 *   return [
 *       'up'   => function (mysqli $db) { db_exec($db, 'CREATE TABLE ...'); },
 *       'down' => function (mysqli $db) { db_exec($db, 'DROP TABLE ...'); },
 *   ];
 *
 * Версия миграции = имя файла без .php. Сортировка лексикографическая, поэтому
 * префикс-таймстамп (YYYYMMDD_HHMMSS) задаёт хронологический порядок.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('migrate.php доступен только из командной строки');
}

require_once __DIR__ . '/../functions.php';

define('MIGRATIONS_DIR', __DIR__ . '/../migrations');

$db = db();
migrate_ensure_table($db);

$command = isset($argv[1]) ? (string) $argv[1] : 'status';
$arg     = isset($argv[2]) ? (string) $argv[2] : '';

switch ($command) {
    case 'status':
        migrate_status($db);
        break;
    case 'up':
        migrate_up($db, $arg === '' ? PHP_INT_MAX : max(0, (int) $arg));
        break;
    case 'down':
        migrate_down($db, $arg === '' ? 1 : max(0, (int) $arg));
        break;
    case 'create':
        migrate_create($arg);
        break;
    case 'baseline':
        migrate_mark_baseline($db, $arg);
        break;
    default:
        fwrite(STDERR, "Неизвестная команда: {$command}\n");
        fwrite(STDERR, "Доступно: status | up [N] | down [N] | create \"имя\" | baseline <версия>\n");
        exit(1);
}

/* ------------------------------------------------------------------ */
/*  Служебная таблица и реестр                                         */
/* ------------------------------------------------------------------ */

function migrate_ensure_table(mysqli $db)
{
    db_exec($db, '
        CREATE TABLE IF NOT EXISTS schema_migrations (
            version    VARCHAR(255) NOT NULL,
            applied_at DATETIME     NOT NULL,
            PRIMARY KEY (version)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8
    ');
}

/**
 * @return array<int,string> версии из schema_migrations
 */
function migrate_applied(mysqli $db)
{
    $rows = db_rows($db, 'SELECT version FROM schema_migrations ORDER BY version');
    $out  = array();
    foreach ($rows as $row) {
        $out[] = (string) $row['version'];
    }
    return $out;
}

/**
 * @return array<string,string> версия => полный путь к файлу, отсортировано по версии
 */
function migrate_files()
{
    $files = glob(MIGRATIONS_DIR . '/*.php');
    if ($files === false) {
        $files = array();
    }
    sort($files, SORT_STRING);

    $out = array();
    foreach ($files as $path) {
        $out[basename($path, '.php')] = $path;
    }
    return $out;
}

/* ------------------------------------------------------------------ */
/*  Команды                                                            */
/* ------------------------------------------------------------------ */

function migrate_status(mysqli $db)
{
    $applied = migrate_applied($db);
    $files   = migrate_files();

    echo "Миграции:\n";
    foreach ($files as $version => $path) {
        $mark = in_array($version, $applied, true) ? '[x]' : '[ ]';
        echo "  {$mark} {$version}\n";
    }

    // Применённые версии, для которых нет файла (удалён/переименован).
    foreach ($applied as $version) {
        if (!isset($files[$version])) {
            echo "  [!] {$version} — применена, но файла нет\n";
        }
    }

    $pending = array_diff(array_keys($files), $applied);
    echo "\nПрименено: " . count($applied) . ", ожидает: " . count($pending) . "\n";
}

function migrate_up(mysqli $db, $limit)
{
    $applied = migrate_applied($db);
    $files   = migrate_files();

    $pending = array();
    foreach ($files as $version => $path) {
        if (!in_array($version, $applied, true)) {
            $pending[$version] = $path;
        }
    }

    if (!$pending) {
        echo "Нечего применять — все миграции уже накачены.\n";
        return;
    }

    $done = 0;
    foreach ($pending as $version => $path) {
        if ($done >= $limit) {
            break;
        }

        echo "→ up {$version} ... ";
        $migration = migrate_load($path);

        try {
            call_user_func($migration['up'], $db);
        } catch (Throwable $e) {
            echo "ОШИБКА\n";
            fwrite(STDERR, "Миграция {$version} упала: " . $e->getMessage() . "\n");
            fwrite(STDERR, "ВНИМАНИЕ: в MySQL DDL автокоммитится — миграция могла примениться частично. Проверьте вручную.\n");
            exit(1);
        }

        db_exec(
            $db,
            'INSERT INTO schema_migrations (version, applied_at) VALUES (?, ?)',
            array($version, date('Y-m-d H:i:s'))
        );

        echo "ок\n";
        $done++;
    }

    echo "Готово. Применено миграций: {$done}.\n";
}

function migrate_down(mysqli $db, $limit)
{
    $applied = migrate_applied($db);
    $files   = migrate_files();

    // Откатываем в обратном порядке применения.
    rsort($applied, SORT_STRING);

    if (!$applied) {
        echo "Нечего откатывать.\n";
        return;
    }

    $done = 0;
    foreach ($applied as $version) {
        if ($done >= $limit) {
            break;
        }

        if (!isset($files[$version])) {
            fwrite(STDERR, "Не могу откатить {$version}: файл миграции отсутствует.\n");
            exit(1);
        }

        echo "← down {$version} ... ";
        $migration = migrate_load($files[$version]);

        try {
            call_user_func($migration['down'], $db);
        } catch (Throwable $e) {
            echo "ОШИБКА\n";
            fwrite(STDERR, "Откат {$version} упал: " . $e->getMessage() . "\n");
            exit(1);
        }

        db_exec($db, 'DELETE FROM schema_migrations WHERE version = ?', array($version));

        echo "ок\n";
        $done++;
    }

    echo "Готово. Откачено миграций: {$done}.\n";
}

function migrate_create($name)
{
    if ($name === '') {
        fwrite(STDERR, "Укажите имя: php bin/migrate.php create \"create something\"\n");
        exit(1);
    }

    $slug    = preg_replace('/[^a-z0-9]+/', '_', strtolower($name));
    $slug    = trim($slug, '_');
    $version = date('Ymd_His') . '_' . $slug;
    $path    = MIGRATIONS_DIR . '/' . $version . '.php';

    if (!is_dir(MIGRATIONS_DIR)) {
        mkdir(MIGRATIONS_DIR, 0775, true);
    }

    $template = "<?php\n"
        . "/**\n"
        . " * {$name}\n"
        . " */\n\n"
        . "return [\n"
        . "    'up' => function (mysqli \$db) {\n"
        . "        // db_exec(\$db, 'CREATE TABLE ...');\n"
        . "    },\n"
        . "    'down' => function (mysqli \$db) {\n"
        . "        // db_exec(\$db, 'DROP TABLE ...');\n"
        . "    },\n"
        . "];\n";

    file_put_contents($path, $template);
    echo "Создан: migrations/{$version}.php\n";
}

function migrate_mark_baseline(mysqli $db, $version)
{
    if ($version === '') {
        fwrite(STDERR, "Укажите версию: php bin/migrate.php baseline <версия>\n");
        exit(1);
    }

    $files = migrate_files();
    if (!isset($files[$version])) {
        fwrite(STDERR, "Нет файла миграции с версией {$version}.\n");
        exit(1);
    }

    $applied = migrate_applied($db);
    if (in_array($version, $applied, true)) {
        echo "Версия {$version} уже помечена применённой.\n";
        return;
    }

    db_exec(
        $db,
        'INSERT INTO schema_migrations (version, applied_at) VALUES (?, ?)',
        array($version, date('Y-m-d H:i:s'))
    );

    echo "Помечена применённой БЕЗ выполнения: {$version}\n";
}

/* ------------------------------------------------------------------ */
/*  Вспомогательное                                                    */
/* ------------------------------------------------------------------ */

/**
 * @return array{up:callable,down:callable}
 */
function migrate_load($path)
{
    $migration = require $path;

    if (!is_array($migration) || !isset($migration['up']) || !is_callable($migration['up'])) {
        fwrite(STDERR, "Файл " . basename($path) . " должен возвращать массив с ключом 'up' (callable).\n");
        exit(1);
    }

    if (!isset($migration['down']) || !is_callable($migration['down'])) {
        // down не обязателен, но тогда откат невозможен.
        $migration['down'] = function () use ($path) {
            throw new RuntimeException('У миграции ' . basename($path) . ' нет down() — откат невозможен.');
        };
    }

    return $migration;
}

/**
 * Есть ли колонка в таблице текущей базы. Нужно, т.к. MySQL 8 не поддерживает
 * ALTER TABLE ... ADD COLUMN IF NOT EXISTS (это синтаксис MariaDB).
 */
function migrate_column_exists(mysqli $db, $table, $column)
{
    $rows = db_rows(
        $db,
        'SELECT 1 FROM information_schema.COLUMNS
          WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?
          LIMIT 1',
        array((string) $table, (string) $column)
    );

    return count($rows) > 0;
}

/**
 * Выполняет многооператорный SQL-файл (например дамп DDL baseline) через
 * multi_query. Это единственное место, где multi_query уместен: обычные
 * миграции делают по одному statement через db_exec.
 *
 * Вызывается из миграции: migrate_run_sql_file($db, __DIR__ . '/../schema/baseline.sql');
 */
function migrate_run_sql_file(mysqli $db, $sqlPath)
{
    if (!is_file($sqlPath)) {
        throw new RuntimeException("Нет SQL-файла: {$sqlPath}");
    }

    $sql = file_get_contents($sqlPath);
    if ($sql === false || trim($sql) === '') {
        throw new RuntimeException("Пустой или нечитаемый SQL-файл: {$sqlPath}");
    }

    // mysqli_report(EXCEPTION) включён в db(): ошибка любого statement бросит
    // исключение. Результаты всех операторов нужно вычитать до конца.
    $db->multi_query($sql);
    do {
        if ($result = $db->store_result()) {
            $result->free();
        }
    } while ($db->more_results() && $db->next_result());
}
