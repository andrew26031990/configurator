<?php
/**
 * Заявка из конфигуратора: проверки против ботов.
 *
 * Чистые функции (без базы и без $_SERVER) — проверяются tests/order_test.php.
 * Подключается из modules/mail.php и configurator/configuration.php.
 */

/**
 * Телефон в виде 998XXXXXXXXX или '' если номер некорректный.
 * Принимает «+(998) 90 123-45-67», «998901234567», «90 123 45 67».
 *
 * @return string
 */
function order_normalize_phone($phone)
{
    $digits = preg_replace('/\D+/', '', (string) $phone);

    if (strlen($digits) === 9) {
        $digits = '998' . $digits;
    }

    return preg_match('/^998\d{9}$/', $digits) ? $digits : '';
}

/**
 * «998901234567» → «+998 90 123-45-67».
 *
 * @return string
 */
function order_format_phone($digits)
{
    return '+' . substr($digits, 0, 3) . ' ' . substr($digits, 3, 2) . ' '
        . substr($digits, 5, 3) . '-' . substr($digits, 8, 2) . '-' . substr($digits, 10, 2);
}

/**
 * Имя похоже на имя: есть буква, нет ссылок и разметки.
 *
 * @return bool
 */
function order_name_ok($name)
{
    $name = (string) $name;
    $len  = function_exists('mb_strlen') ? mb_strlen($name, 'UTF-8') : strlen($name);

    if ($len < 2 || $len > 100) {
        return false;
    }
    if (!preg_match('/\p{L}/u', $name)) {
        return false;
    }

    return !preg_match('~https?:|www\.|[<>{}\[\]]|\.(ru|com|net|org|uz|xyz|top)\b~iu', $name);
}

/**
 * Товары из ссылки на сборку: «…?cat=x&materinskaya_plata=2785(1)&ozu=2632(2)».
 *
 * @return array<int,array{type:string,id:int,qty:int}>
 */
function order_parse_items($link)
{
    $query = (string) parse_url((string) $link, PHP_URL_QUERY);
    $items = array();

    foreach (explode('&', $query) as $pair) {
        if ($pair === '' || strpos($pair, '=') === false) {
            continue;
        }
        list($type, $value) = explode('=', $pair, 2);
        $type  = rawurldecode($type);
        $value = rawurldecode($value);

        if ($type === 'cat' || !preg_match('/^[a-z0-9_]{1,64}$/i', $type)) {
            continue;
        }
        if (!preg_match('/^(\d{1,9})(?:\((\d{1,2})\))?$/', $value, $m)) {
            continue;
        }

        $qty = isset($m[2]) && $m[2] !== '' ? (int) $m[2] : 1;
        $items[] = array('type' => $type, 'id' => (int) $m[1], 'qty' => max(1, min(6, $qty)));
    }

    return $items;
}

/**
 * Подписанная метка времени открытия страницы: «время.подпись».
 *
 * @return string
 */
function order_token_make($secret, $now)
{
    $ts = (string) (int) $now;

    return $ts . '.' . substr(hash_hmac('sha256', 'order|' . $ts, (string) $secret), 0, 32);
}

/**
 * Проверка метки: подпись верна, с открытия страницы прошло не меньше
 * $minAge секунд (человек не заполняет форму мгновенно) и не больше $maxAge.
 *
 * @return string 'ok' | 'invalid' | 'too_fast' | 'expired'
 */
function order_token_check($token, $secret, $now, $minAge = 3, $maxAge = 86400)
{
    if (!preg_match('/^(\d{9,11})\.([0-9a-f]{32})$/', (string) $token, $m)) {
        return 'invalid';
    }

    $expected = substr(hash_hmac('sha256', 'order|' . $m[1], (string) $secret), 0, 32);
    if (!hash_equals($expected, $m[2])) {
        return 'invalid';
    }

    $age = (int) $now - (int) $m[1];
    if ($age < $minAge) {
        return 'too_fast';
    }
    if ($age > $maxAge) {
        return 'expired';
    }

    return 'ok';
}

/**
 * Лимит заявок по ключу клиента (хеш IP): не чаще $minInterval секунд и не
 * больше $perDay за сутки. Возвращает новое состояние хранилища и решение.
 *
 * @param  array<string,array{last:int,day:string,count:int}> $data
 * @return array{0: bool, 1: array<string,array{last:int,day:string,count:int}>}
 */
function order_rate_check(array $data, $key, $now, $minInterval, $perDay)
{
    $day = date('Y-m-d', (int) $now);

    // Чистим вчерашние записи, чтобы файл не рос.
    foreach ($data as $k => $row) {
        if (!isset($row['day']) || $row['day'] !== $day) {
            unset($data[$k]);
        }
    }

    $row = isset($data[$key]) ? $data[$key] : array('last' => 0, 'day' => $day, 'count' => 0);

    if ((int) $now - (int) $row['last'] < $minInterval || (int) $row['count'] >= $perDay) {
        return array(false, $data);
    }

    $data[$key] = array('last' => (int) $now, 'day' => $day, 'count' => (int) $row['count'] + 1);

    return array(true, $data);
}
