<?php
/**
 * Общий слой безопасности. Подключается первым во всех точках входа.
 *
 *   require_once __DIR__ . '/security.php';    // из корня
 *   require_once __DIR__ . '/../security.php'; // из modules/
 *
 * Ничего не выводит, работает в PHP 7.4+ / 8.x.
 */

if (defined('APP_SECURITY_LOADED')) {
    return;
}
define('APP_SECURITY_LOADED', true);

define('APP_ROOT', __DIR__);

/* ------------------------------------------------------------------ */
/*  Конфиг                                                             */
/* ------------------------------------------------------------------ */

/**
 * @return array<string,mixed>
 */
function app_config()
{
    static $config = null;

    if ($config === null) {
        $file = APP_ROOT . '/config.local.php';
        if (!is_readable($file)) {
            http_response_code(500);
            exit('Конфигурация не найдена. Скопируйте config.local.example.php в config.local.php.');
        }
        $config = require $file;
    }

    return $config;
}

/**
 * Значение из конфига по пути 'db.host'.
 *
 * @param  string $path
 * @param  mixed  $default
 * @return mixed
 */
function config_get($path, $default = null)
{
    $value = app_config();
    foreach (explode('.', $path) as $key) {
        if (!is_array($value) || !array_key_exists($key, $value)) {
            return $default;
        }
        $value = $value[$key];
    }
    return $value;
}

/* ------------------------------------------------------------------ */
/*  Режим отображения ошибок                                           */
/* ------------------------------------------------------------------ */

if (config_get('debug', false)) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    // На проде текст ошибки не должен попадать в браузер: в нём светятся
    // SQL-запросы, пути и имена таблиц.
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
}

/* ------------------------------------------------------------------ */
/*  Заглушки mbstring                                                  */
/* ------------------------------------------------------------------ */

/*
 * mbstring есть почти везде, но на части shared-хостингов его отключают.
 * Без заглушек любой эндпоинт падал бы с "Call to undefined function
 * mb_substr" — а обрезка по байтам в худшем случае испортит один символ
 * на границе. Лучше так, чем мёртвый сайт.
 */

if (!function_exists('mb_substr')) {
    function mb_substr($string, $start, $length = null, $encoding = null)
    {
        return $length === null
            ? substr((string) $string, $start)
            : substr((string) $string, $start, $length);
    }
}

if (!function_exists('mb_strlen')) {
    function mb_strlen($string, $encoding = null)
    {
        return strlen((string) $string);
    }
}

/* ------------------------------------------------------------------ */
/*  Сессия                                                             */
/* ------------------------------------------------------------------ */

function request_is_https()
{
    if (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') {
        return true;
    }
    if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
        return true;
    }
    return isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443;
}

/**
 * Стартует сессию с httponly + SameSite=Lax.
 *
 * SameSite=Lax здесь работает как защита от CSRF: браузер не отправит
 * cookie сессии с чужого сайта в POST-запросе, а все админские действия —
 * это POST. Полноценные CSRF-токены появятся в laravel-версии.
 */
function secure_session_start()
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    ini_set('session.use_strict_mode', '1');

    $params = array(
        'lifetime' => 0,
        'path'     => '/',
        'domain'   => '',
        'secure'   => request_is_https(),
        'httponly' => true,
        'samesite' => 'Lax',
    );

    if (PHP_VERSION_ID >= 70300) {
        session_set_cookie_params($params);
    } else {
        session_set_cookie_params(
            $params['lifetime'],
            $params['path'] . '; samesite=Lax',
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    session_start();
}

/* ------------------------------------------------------------------ */
/*  Авторизация                                                        */
/* ------------------------------------------------------------------ */

/**
 * Запрещает показывать страницу внутри чужого iframe (кликджекинг).
 * Вызывается только на страницах админки: публичный конфигуратор,
 * наоборот, должен встраиваться на сайт магазина.
 *
 * @return void
 */
function deny_framing()
{
    if (headers_sent()) {
        return;
    }

    header('X-Frame-Options: DENY');
    header("Content-Security-Policy: frame-ancestors 'none'");
}

function is_admin()
{
    secure_session_start();
    return !empty($_SESSION['username']);
}

/**
 * Гард для всех административных эндпоинтов.
 * Раньше их не было вообще: любой мог удалить товар POST-запросом.
 */
function require_admin()
{
    if (is_admin()) {
        return;
    }

    http_response_code(403);
    header('Content-Type: text/plain; charset=UTF-8');
    exit('Доступ запрещён');
}

/* ------------------------------------------------------------------ */
/*  Вывод                                                              */
/* ------------------------------------------------------------------ */

/**
 * Экранирование для HTML. Использовать ВЕЗДЕ при выводе данных из базы.
 *
 * @param  mixed $value
 * @return string
 */
function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Для полей, где администратор намеренно пользуется разметкой:
 * заголовки и описания готовых сборок содержат <br> и цветные <span>,
 * и на витрине это должно отрисовываться, а не выводиться текстом.
 *
 * Оставляет только форматирующие теги — <a>, <img>, <script>, <iframe>
 * вырезаются — и дополнительно убирает обработчики событий.
 *
 * @param  mixed  $value
 * @param  string $allowedTags
 * @return string
 */
function e_html($value, $allowedTags = '<br><b><strong><i><em><u><span><p>')
{
    $html = strip_tags((string) $value, $allowedTags);

    // strip_tags сохраняет атрибуты, поэтому обработчики событий
    // (on*="...") убираем отдельно.
    $cleaned = preg_replace(
        '/\son[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i',
        '',
        $html
    );

    return $cleaned === null ? '' : $cleaned;
}

/**
 * Экранирование значения, которое подставляется в href/src.
 * Отсекает javascript: и прочие активные схемы.
 *
 * @param  mixed $value
 * @return string
 */
function e_url($value)
{
    $url = (string) $value;

    /*
     * Браузер выкидывает из URL пробельные и управляющие символы ещё до
     * разбора схемы, поэтому "java\tscript:alert(1)" для него — обычный
     * javascript:. Чистим строку до проверки схемы, иначе такая ссылка
     * проходит как относительная.
     */
    $url = preg_replace('/[\x00-\x20\x7F]/', '', $url);

    if ($url === null || $url === '') {
        return '';
    }

    $scheme  = strtolower((string) parse_url($url, PHP_URL_SCHEME));
    $allowed = array('', 'http', 'https', 'mailto', 'tel');

    if (!in_array($scheme, $allowed, true)) {
        return '';
    }

    return e($url);
}

/**
 * @param  mixed $data
 * @return void
 */
function json_out($data)
{
    header('Content-Type: application/json; charset=UTF-8');
    header('X-Content-Type-Options: nosniff');

    // HEX-флаги: если ответ где-то вставят в HTML как есть, символы < > & '
    // уедут в < и не превратятся в исполняемую разметку.
    echo json_encode(
        $data,
        JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
    );
}

/* ------------------------------------------------------------------ */
/*  Входные данные                                                     */
/* ------------------------------------------------------------------ */

/**
 * @param  string   $key
 * @param  int|null $default
 * @return int|null
 */
function post_int($key, $default = null)
{
    if (!isset($_POST[$key])) {
        return $default;
    }
    $value = filter_var($_POST[$key], FILTER_VALIDATE_INT);
    return $value === false ? $default : $value;
}

/**
 * @param  string   $key
 * @param  int|null $default
 * @return int|null
 */
function get_int($key, $default = null)
{
    if (!isset($_GET[$key])) {
        return $default;
    }
    $value = filter_var($_GET[$key], FILTER_VALIDATE_INT);
    return $value === false ? $default : $value;
}

/**
 * @param  string $key
 * @param  int    $maxLength
 * @param  string $default
 * @return string
 */
function post_str($key, $maxLength = 500, $default = '')
{
    if (!isset($_POST[$key]) || !is_scalar($_POST[$key])) {
        return $default;
    }

    $value = trim((string) $_POST[$key]);

    // Убираем управляющие символы, кроме перевода строки и табуляции.
    // На некорректном UTF-8 preg_replace с /u возвращает null — тогда
    // чистим побайтово, без модификатора.
    $cleaned = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $value);

    if ($cleaned === null) {
        $cleaned = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $value);
    }

    return mb_substr((string) $cleaned, 0, $maxLength, 'UTF-8');
}

/* ------------------------------------------------------------------ */
/*  Файлы                                                              */
/* ------------------------------------------------------------------ */

/**
 * Имя файла, пришедшее из формы, нельзя подставлять в путь как есть:
 * '../../functions.php' удалил бы исходник. Оставляем только имя.
 *
 * @param  string $name
 * @return string
 */
function safe_basename($name)
{
    $name = str_replace(array("\\", "\0"), array('/', ''), (string) $name);
    $name = basename($name);

    return ($name === '.' || $name === '..') ? '' : $name;
}

/**
 * Генерирует случайное имя для загружаемой картинки.
 * rand(1000, 1000000) в старом коде давал коллизии и был предсказуем.
 *
 * @param  string $extension
 * @return string
 */
function random_image_name($extension)
{
    $extension = strtolower(preg_replace('/[^a-z0-9]/i', '', (string) $extension));

    if (function_exists('random_bytes')) {
        $prefix = bin2hex(random_bytes(8));
    } else {
        $prefix = md5(uniqid((string) mt_rand(), true));
    }

    return $prefix . '.' . $extension;
}

/**
 * Проверяет, что загруженный файл — действительно картинка разрешённого
 * типа, а не php-скрипт с расширением .jpg.
 *
 * @param  string $tmpPath
 * @param  string $extension
 * @return bool
 */
function is_valid_image_upload($tmpPath, $extension)
{
    $allowed = array(
        'jpg'  => IMAGETYPE_JPEG,
        'jpeg' => IMAGETYPE_JPEG,
        'png'  => IMAGETYPE_PNG,
        'gif'  => IMAGETYPE_GIF,
        'bmp'  => IMAGETYPE_BMP,
        'webp' => IMAGETYPE_WEBP,
    );

    $extension = strtolower((string) $extension);

    if (!isset($allowed[$extension])) {
        return false;
    }

    if (!is_uploaded_file($tmpPath)) {
        return false;
    }

    $info = @getimagesize($tmpPath);

    if ($info === false || (int) $info[2] !== $allowed[$extension]) {
        return false;
    }

    /*
     * Разумный предел для фотографии товара. Полиглоты (настоящий
     * заголовок + payload) как раз и объявляют абсурдные размеры вроде
     * 16188x26736, чтобы не пройти пересборку и остаться на диске как есть.
     */
    if ((int) $info[0] > 12000 || (int) $info[1] > 12000
        || (int) $info[0] < 1 || (int) $info[1] < 1) {
        return false;
    }

    return true;
}

/**
 * Приводит уже сохранённую картинку в безопасный вид.
 *
 * Если GD на хостинге нет — пропускаем как есть, защита держится на
 * .htaccess в каталоге загрузок. Если GD есть, но пересобрать не вышло,
 * файл считается подозрительным: лучше отказать в загрузке, чем оставить
 * на диске то, что не удалось разобрать как картинку.
 *
 * @param  string $path
 * @param  string $extension
 * @return bool
 */
function harden_uploaded_image($path, $extension)
{
    if (!function_exists('imagecreatefrompng')) {
        return true;
    }

    return reencode_image($path, $extension);
}

/**
 * Пересохраняет загруженную картинку средствами GD.
 *
 * getimagesize() принимает "полиглоты" вида GIF89a + php-код: заголовок
 * настоящий, а дальше payload. Исполниться в каталоге загрузок он не может
 * (там свой .htaccess), но надёжнее уничтожить нагрузку — пересборка
 * оставляет только пиксели и попутно выкидывает EXIF.
 *
 * Картинка читается типовой функцией GD по коду формата, а не через
 * imagecreatefromstring(file_get_contents(...)): последняя связка входит
 * в сигнатуры антивирусов как признак шелла, замаскированного под
 * картинку, и файл с ней уезжает в карантин на shared-хостинге.
 *
 * Если GD недоступен или картинка слишком большая для памяти — файл
 * остаётся как есть, защита при этом держится на .htaccess.
 *
 * @param  string $path      абсолютный путь к уже сохранённому файлу
 * @param  string $extension нормализованное расширение
 * @return bool              true, если файл пересобран
 */
function reencode_image($path, $extension)
{
    if (!function_exists('imagecreatefrompng') || !is_file($path)) {
        return false;
    }

    $info = @getimagesize($path);

    if ($info === false) {
        return false;
    }

    // ~4 байта на пиксель: не пытаемся разжать то, что не влезет в память.
    if (((int) $info[0] * (int) $info[1]) > 30000000) {
        return false;
    }

    switch ((int) $info[2]) {
        case IMAGETYPE_JPEG:
            $image = @imagecreatefromjpeg($path);
            break;
        case IMAGETYPE_PNG:
            $image = @imagecreatefrompng($path);
            break;
        case IMAGETYPE_GIF:
            $image = @imagecreatefromgif($path);
            break;
        case IMAGETYPE_WEBP:
            $image = function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : false;
            break;
        case IMAGETYPE_BMP:
            $image = function_exists('imagecreatefrombmp') ? @imagecreatefrombmp($path) : false;
            break;
        default:
            $image = false;
    }

    if ($image === false) {
        return false;
    }

    $tmp = $path . '.rebuild';

    switch (strtolower($extension)) {
        case 'jpg':
        case 'jpeg':
            $saved = @imagejpeg($image, $tmp, 90);
            break;
        case 'gif':
            $saved = @imagegif($image, $tmp);
            break;
        case 'bmp':
            $saved = function_exists('imagebmp') ? @imagebmp($image, $tmp) : false;
            break;
        case 'webp':
            $saved = function_exists('imagewebp') ? @imagewebp($image, $tmp) : false;
            break;
        default:
            imagealphablending($image, false);
            imagesavealpha($image, true);
            $saved = @imagepng($image, $tmp);
    }

    imagedestroy($image);

    if (!$saved || !is_file($tmp)) {
        @unlink($tmp);
        return false;
    }

    if (!@rename($tmp, $path)) {
        @unlink($tmp);
        return false;
    }

    return true;
}

/**
 * Удаляет файл строго внутри указанной директории.
 *
 * @param  string $directory абсолютный путь
 * @param  string $fileName  имя файла из формы или базы
 * @return bool
 */
function delete_upload($directory, $fileName)
{
    $fileName = safe_basename($fileName);

    if ($fileName === '') {
        return false;
    }

    $path = rtrim($directory, '/\\') . DIRECTORY_SEPARATOR . $fileName;

    if (!is_file($path)) {
        return false;
    }

    $real = realpath($path);
    $base = realpath($directory);

    if ($real === false || $base === false || strpos($real, $base) !== 0) {
        return false;
    }

    return @unlink($real);
}

/* ------------------------------------------------------------------ */
/*  Простой троттлинг публичных форм                                   */
/* ------------------------------------------------------------------ */

/**
 * Не даёт слать заявки чаще раза в $seconds секунд из одной сессии.
 *
 * @param  string $bucket
 * @param  int    $seconds
 * @return bool   true — можно продолжать
 */
function throttle_ok($bucket, $seconds = 20)
{
    secure_session_start();

    $key  = 'throttle_' . $bucket;
    $last = isset($_SESSION[$key]) ? (int) $_SESSION[$key] : 0;

    if (time() - $last < $seconds) {
        return false;
    }

    $_SESSION[$key] = time();

    return true;
}
