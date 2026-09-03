<?php
/**
 * Проверка хелперов security.php. Базы не требует.
  * Запуск: php tests/security_test.php
 */

require_once __DIR__ . '/../security.php';

// security.php выключает display_errors для прода — для теста возвращаем.
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

/* --- e() ------------------------------------------------------------- */

check('e: тег', e('<script>alert(1)</script>'), '&lt;script&gt;alert(1)&lt;/script&gt;');
check('e: двойная кавычка', e('a" onload="x'), 'a&quot; onload=&quot;x');
check('e: одинарная кавычка', e("a' onload='x"), 'a&#039; onload=&#039;x');
check('e: null', e(null), '');

/* --- e_url() --------------------------------------------------------- */

check('e_url: https',        e_url('https://pcmarket.uz/x?a=1'), 'https://pcmarket.uz/x?a=1');
check('e_url: относительный', e_url('/catalog/item'),            '/catalog/item');
check('e_url: javascript',   e_url('javascript:alert(1)'),       '');
check('e_url: JaVaScRiPt',   e_url('JaVaScRiPt:alert(1)'),       '');
check('e_url: data',         e_url('data:text/html,<h1>x'),      '');
check('e_url: vbscript',     e_url('vbscript:msgbox(1)'),        '');
check('e_url: пустой',       e_url(''),                          '');
// Браузеры выкидывают TAB/CR/LF из URL, поэтому "java\tscript:" исполняется.
check('e_url: javascript с табом',   e_url("java\tscript:alert(1)"), '');
check('e_url: javascript с переводом строки', e_url("java\nscript:alert(1)"), '');
check('e_url: пробел в начале',      e_url('  javascript:alert(1)'), '');

/* --- safe_basename() -------------------------------------------------- */

check('basename: unix-обход',    safe_basename('../../functions.php'), 'functions.php');
check('basename: windows-обход', safe_basename('..\\..\\functions.php'), 'functions.php');
check('basename: абсолютный',    safe_basename('/etc/passwd'), 'passwd');
check('basename: точка',         safe_basename('.'), '');
check('basename: две точки',     safe_basename('..'), '');
check('basename: обычное имя',   safe_basename('a1b2.jpg'), 'a1b2.jpg');

/* --- delete_upload(): главное — не выйти за пределы каталога ---------- */

$base    = sys_get_temp_dir() . '/cfg_test_' . getmypid();
$uploads = $base . '/uploads';
@mkdir($uploads, 0777, true);

file_put_contents($base . '/ВАЖНЫЙ.txt', 'не трогать');
file_put_contents($uploads . '/pic.jpg', 'картинка');

check('delete_upload: обход ../',      delete_upload($uploads, '../ВАЖНЫЙ.txt'), false);
check('delete_upload: файл снаружи цел', file_exists($base . '/ВАЖНЫЙ.txt'), true);
check('delete_upload: обход ..\\',     delete_upload($uploads, '..\\ВАЖНЫЙ.txt'), false);
check('delete_upload: файл снаружи всё ещё цел', file_exists($base . '/ВАЖНЫЙ.txt'), true);
check('delete_upload: абсолютный путь', delete_upload($uploads, $base . '/ВАЖНЫЙ.txt'), false);
check('delete_upload: файл снаружи цел (3)', file_exists($base . '/ВАЖНЫЙ.txt'), true);
check('delete_upload: пустое имя',      delete_upload($uploads, ''), false);
check('delete_upload: несуществующий',  delete_upload($uploads, 'нет.jpg'), false);
check('delete_upload: свой файл',       delete_upload($uploads, 'pic.jpg'), true);
check('delete_upload: свой файл удалён', file_exists($uploads . '/pic.jpg'), false);

@unlink($base . '/ВАЖНЫЙ.txt');
@rmdir($uploads);
@rmdir($base);

/* --- post_str() / post_int() ------------------------------------------ */

$_POST = array(
    'ok'      => '  Гигабайт X299  ',
    'ctrl'    => "плохо\x00\x07байт",
    'long'    => str_repeat('я', 100),
    'badutf'  => "\xB0\xC1 хвост",
    'num'     => '42',
    'numbad'  => '12abc',
    'arr'     => array('a'),
);

check('post_str: trim',           post_str('ok'), 'Гигабайт X299');
check('post_str: контрольные',    post_str('ctrl'), 'плохобайт');
check('post_str: обрезка',        mb_strlen(post_str('long', 10), 'UTF-8'), 10);
check('post_str: нет ключа',      post_str('нет_такого'), '');
check('post_str: массив',         post_str('arr'), '');
check('post_str: битый utf-8 не падает', is_string(post_str('badutf')), true);

check('post_int: число',       post_int('num'), 42);
check('post_int: не число',    post_int('numbad'), null);
check('post_int: нет ключа',   post_int('нет_такого'), null);
check('post_int: default',     post_int('нет_такого', 7), 7);
check('post_int: массив',      post_int('arr'), null);

/* --- random_image_name() ---------------------------------------------- */

$n1 = random_image_name('jpg');
$n2 = random_image_name('jpg');
check('random_image_name: уникальность', $n1 === $n2, false);
// 16 hex — ветка random_bytes (PHP 7+), 32 hex — запасная ветка md5 на 5.6.
check('random_image_name: расширение', (bool) preg_match('/^[0-9a-f]{16}\.jpg$|^[0-9a-f]{32}\.jpg$/', $n1), true);
check('random_image_name: чистит расширение', random_image_name('php.jpg') !== '', true);
check('random_image_name: нет точек в расширении',
    substr_count(random_image_name('php.jpg'), '.'), 1);

/* --- translit() -------------------------------------------------------- */

require_once __DIR__ . '/../modules/strtr.php';

check('translit: кириллица', translit('Материнская плата'), 'materinskaya_plata');
check('translit: кавычки убраны', translit('ОЗУ "Corsair"'), 'ozu_Corsair');
// Публичный эндпоинт strtr.php дополнительно чистит вывод регуляркой,
// сама translit() угловые скобки не трогает — это ожидаемо.
check('translit: slug-safe после чистки',
    preg_replace('/[^A-Za-z0-9_-]/', '', translit('Видео карты <b>')),
    'video_karti_b');

echo "\n";
echo 'PHP ' . PHP_VERSION . ' — успешно: ' . $pass . ', провалено: ' . $fail . "\n";

exit($fail === 0 ? 0 : 1);
