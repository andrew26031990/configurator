<?php
/**
 * Проверки заявки из конфигуратора (защита от ботов). Базы не требует.
 * Запуск: php tests/order_test.php
 */

require_once __DIR__ . '/../order.php';

ini_set('display_errors', '1');
error_reporting(E_ALL);

$pass = 0;
$fail = 0;

function check($label, $actual, $expected)
{
    global $pass, $fail;

    if ($actual === $expected) {
        $pass++;
        return;
    }

    $fail++;
    echo "FAIL: $label\n";
    echo "      ожидалось: " . var_export($expected, true) . "\n";
    echo "      получено:  " . var_export($actual, true) . "\n";
}

/* --- Телефон --------------------------------------------------------- */

check('маска формы', order_normalize_phone('+(998) 90 123-45-67'), '998901234567');
check('без кода страны', order_normalize_phone('90 123 45 67'), '998901234567');
check('слитно', order_normalize_phone('998901234567'), '998901234567');
check('заглушка ботов «phone»', order_normalize_phone('phone'), '');
check('короткий', order_normalize_phone('12345'), '');
check('чужая страна', order_normalize_phone('+7 916 123-45-67'), '');
check('лишние цифры', order_normalize_phone('9989012345678'), '');
check('формат для сообщения', order_format_phone('998901234567'), '+998 90 123-45-67');

/* --- Имя ------------------------------------------------------------- */

check('обычное имя', order_name_ok('Азиз'), true);
check('латиница с пробелом', order_name_ok('John Smith'), true);
check('одна буква', order_name_ok('A'), false);
check('только цифры', order_name_ok('12345'), false);
check('ссылка', order_name_ok('Заработок http://spam.example'), false);
check('домен', order_name_ok('casino.xyz bonus'), false);
check('разметка', order_name_ok('<script>alert(1)</script>'), false);
check('слишком длинное', order_name_ok(str_repeat('а', 101)), false);

/* --- Состав сборки из ссылки ------------------------------------------ */

check(
    'ссылка «Поделиться»',
    order_parse_items('https://configurator.pcmarket.uz/?cat=play-profy&materinskaya_plata=2785(1)&ozu=2632(2)'),
    array(
        array('type' => 'materinskaya_plata', 'id' => 2785, 'qty' => 1),
        array('type' => 'ozu', 'id' => 2632, 'qty' => 2),
    )
);
check('без количества — 1', order_parse_items('/x?protsessor=10'), array(array('type' => 'protsessor', 'id' => 10, 'qty' => 1)));
check('количество ограничено 6', order_parse_items('/x?monitor=5(99)'), array(array('type' => 'monitor', 'id' => 5, 'qty' => 6)));
check('мусор отбрасывается', order_parse_items("/x?a=1;DROP&b=abc&c%27=1(1)&cat=play"), array());
check('пустая ссылка', order_parse_items(''), array());

/* --- Метка времени формы ---------------------------------------------- */

$secret = 'test-secret';
$t      = order_token_make($secret, 1700000000);
check('метка: ок', order_token_check($t, $secret, 1700000010), 'ok');
check('метка: слишком быстро', order_token_check($t, $secret, 1700000001), 'too_fast');
check('метка: устарела', order_token_check($t, $secret, 1700000000 + 90000), 'expired');
check('метка: чужая подпись', order_token_check($t, 'other', 1700000010), 'invalid');
check('метка: подделка времени', order_token_check('1600000000' . substr($t, 10), $secret, 1700000010), 'invalid');
check('метка: отсутствует', order_token_check('', $secret, 1700000010), 'invalid');

/* --- Лимит по IP ------------------------------------------------------ */

$now  = strtotime('2026-11-01 12:00:00');
$data = array();
list($ok, $data) = order_rate_check($data, 'k', $now, 30, 3);
check('первая заявка', $ok, true);
list($ok, $data) = order_rate_check($data, 'k', $now + 10, 30, 3);
check('через 10 с — рано', $ok, false);
list($ok, $data) = order_rate_check($data, 'k', $now + 40, 30, 3);
check('через 40 с — можно', $ok, true);
list($ok, $data) = order_rate_check($data, 'k', $now + 80, 30, 3);
check('третья за день', $ok, true);
list($ok, $data) = order_rate_check($data, 'k', $now + 200, 30, 3);
check('четвёртая за день — лимит', $ok, false);
list($ok, $data) = order_rate_check($data, 'other', $now + 200, 30, 3);
check('другой IP не страдает', $ok, true);
list($ok, $data) = order_rate_check($data, 'k', $now + 86400, 30, 3);
check('на следующий день снова можно', $ok, true);
check('вчерашние записи вычищены', array_keys($data), array('k'));

echo "\nПройдено: $pass, провалено: $fail\n";
exit($fail > 0 ? 1 : 0);
