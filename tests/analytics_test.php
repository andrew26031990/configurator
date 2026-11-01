<?php
/**
 * Статистика и воронка: чистые функции. Базы не требует.
 * Запуск: php tests/analytics_test.php
 */

require_once __DIR__ . '/../analytics.php';

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

/* --- Шаги ------------------------------------------------------------ */

check('главная', analytics_event_step('page_view', ''), 1);
check('готовые сборки', analytics_event_step('page_view', 'kompyutery'), 1);
check('конфигуратор', analytics_event_step('page_view', 'play-profy'), 2);
check('выбор', analytics_event_step('component_select', 'AMD'), 3);
check('заявка', analytics_event_step('order_success', 'AMD'), 7);
check('отправка', analytics_event_step('order_submit', 'AMD'), 6);
check('ошибка формы — не шаг', analytics_event_step('order_error', 'AMD'), 0);
check('PDF — не шаг', analytics_event_step('pdf_download', 'AMD'), 0);

/* --- Воронка --------------------------------------------------------- */

// 100 сессий: 30 ушли с главной, 40 — со страницы конфигуратора, 15 выбрали
// товар, 5 собрали минимум, 4 открыли форму, 1 нажал «Отправить», 5 заявок.
$f = analytics_funnel(array(1 => 30, 2 => 40, 3 => 15, 4 => 5, 5 => 4, 6 => 1, 7 => 5));
check('шагов 7', count($f), 7);
check('шаг 1 — все', $f[0]['sessions'], 100);
check('шаг 2', $f[1]['sessions'], 70);
check('шаг 2 потеряли', $f[1]['lost'], 30);
check('шаг 2 от предыдущего', $f[1]['from_prev'], 70.0);
check('шаг 3', $f[2]['sessions'], 30);
check('шаг 7', $f[6]['sessions'], 5);
check('шаг 7 от первого', $f[6]['from_first'], 5.0);
check('шаг 6 → 7', $f[6]['from_prev'], 83.3);
check('у первого шага нет «от предыдущего»', $f[0]['from_prev'], null);

$empty = analytics_funnel(array());
check('пусто: нули без деления на ноль', array($empty[0]['sessions'], $empty[3]['from_prev'], $empty[3]['from_first']), array(0, 0.0, 0.0));

// Заявка по ссылке без кликов — проходит все шаги.
$direct = analytics_funnel(array(7 => 1));
check('прямая заявка проходит все шаги', $direct[2]['sessions'], 1);

/* --- Проверка входных данных ------------------------------------------ */

check('id ок', analytics_id_ok('a1b2c3d4e5f6a7b8'), true);
check('id короткий', analytics_id_ok('abc'), false);
check('id с мусором', analytics_id_ok("a1b2c3d4e5f6a7b'"), false);
check('id не строка', analytics_id_ok(array()), false);
check('cat ок', analytics_clean_token('play-profy', 64), 'play-profy');
check('cat с инъекцией', analytics_clean_token("x' OR 1=1", 64), '');
check('detail длинный', analytics_clean_token(str_repeat('a', 101), 100), '');

/* --- Устройство, боты, источник ------------------------------------- */

check('iPhone', analytics_device('Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X)'), 'mobile');
check('Android', analytics_device('Mozilla/5.0 (Linux; Android 14; SM-S918B) Mobile'), 'mobile');
check('Windows', analytics_device('Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/130'), 'desktop');
check('Googlebot', analytics_is_bot('Mozilla/5.0 (compatible; Googlebot/2.1)'), true);
check('HeadlessChrome', analytics_is_bot('Mozilla/5.0 HeadlessChrome/130.0'), true);
check('пустой UA', analytics_is_bot(''), true);
check('обычный Chrome', analytics_is_bot('Mozilla/5.0 (Windows NT 10.0) Chrome/130.0 Safari/537.36'), false);

check('google.com', analytics_referrer('https://www.google.com/search?q=pc', 'configurator.pcmarket.uz'), 'google');
check('google.co.uz', analytics_referrer('https://www.google.co.uz/', 'configurator.pcmarket.uz'), 'google');
check('instagram', analytics_referrer('https://l.instagram.com/?u=x', 'configurator.pcmarket.uz'), 'instagram');
check('telegram', analytics_referrer('https://t.me/pcmarket', 'configurator.pcmarket.uz'), 'telegram');
check('основной сайт', analytics_referrer('https://pcmarket.uz/catalog', 'configurator.pcmarket.uz'), 'pcmarket.uz');
check('внутренний переход', analytics_referrer('https://configurator.pcmarket.uz/', 'configurator.pcmarket.uz:443'), '');
check('прямой заход', analytics_referrer('', 'configurator.pcmarket.uz'), '');
check('прочий сайт', analytics_referrer('https://olx.uz/item', 'configurator.pcmarket.uz'), 'olx.uz');

echo "\nПройдено: $pass, провалено: $fail\n";
exit($fail > 0 ? 1 : 0);
