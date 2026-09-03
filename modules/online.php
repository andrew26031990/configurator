<?php
/**
 * Счётчик посетителей онлайн.
 *
 * Раньше здесь было второе подключение к MySQL с отдельными кредами
 * прямо в коде. Теперь используется общее соединение из functions.php.
 */

require_once __DIR__ . '/../functions.php';

/**
 * @param  int $window сколько секунд считать посетителя активным
 * @return string
 */
function on_line($window = 300)
{
    $mysqli = db();
    $ip     = isset($_SERVER['REMOTE_ADDR']) ? (string) $_SERVER['REMOTE_ADDR'] : '';

    try {
        db_exec(
            $mysqli,
            'DELETE FROM `online` WHERE `unix` + ? < ? OR `ip` = ?',
            array((int) $window, time(), $ip)
        );

        db_exec(
            $mysqli,
            'INSERT INTO `online` (`ip`, `unix`) VALUES (?, ?)',
            array($ip, (string) time())
        );

        $count = db_count($mysqli, 'SELECT COUNT(*) FROM `online`');
    } catch (Throwable $e) {
        error_log('on_line: ' . $e->getMessage());
        return '';
    }

    return 'На сайте <strong>' . $count . '</strong> ' . pluralizeVisitors($count);
}

/**
 * @param  int $count
 * @return string
 */
function pluralizeVisitors($count)
{
    $mod100 = $count % 100;
    $mod10  = $count % 10;

    if ($mod100 >= 11 && $mod100 <= 14) {
        return 'человек';
    }

    if ($mod10 >= 2 && $mod10 <= 4) {
        return 'человека';
    }

    return 'человек';
}
