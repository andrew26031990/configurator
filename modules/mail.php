<?php
/**
 * Приём заявки из конфигуратора: база (orders), Telegram-канал и письмо.
 * Публичный эндпоинт, авторизация не требуется.
 *
 * Защита от ботов:
 *   - только POST: открытие адреса сканером/роботом больше не шлёт заявку;
 *   - скрытое поле-ловушка website: его заполняют только боты;
 *   - подписанная метка времени открытия страницы (order_token): форма,
 *     отправленная быстрее чем через 3 секунды, — бот;
 *   - имя и телефон (+998 XX XXX-XX-XX) проверяются по формату;
 *   - состав и сумма сборки берутся из базы по ссылке на сборку, а не из
 *     присланного текста: заявка без реальных товаров не принимается;
 *   - лимит по IP: не чаще раза в 30 секунд и не больше 5 заявок в сутки.
 *
 * Ловушку и «слишком быструю» форму не выдаём: бот получает обычный ответ
 * об успехе, но заявка никуда не уходит — только запись в error_log.
 */

require_once __DIR__ . '/../functions.php';
require_once __DIR__ . '/../order.php';
require_once __DIR__ . '/messages_to_telegram.php';
require_once __DIR__ . '/../mail/mailer.php';

header('Content-Type: text/plain; charset=UTF-8');

const ORDER_OK_MESSAGE = 'Ваша заявка принята. В ближайшее время с вами свяжутся сотрудники компании PCMARKET';

/**
 * Отказ без отправки. $silent — ответить «успехом», чтобы бот не подбирал обход.
 */
function order_reject($reason, $status, $message, $silent = false)
{
    $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
    error_log('order rejected (' . $reason . ') ip=' . $ip);

    if ($silent) {
        exit(ORDER_OK_MESSAGE);
    }

    http_response_code($status);
    exit($message);
}

if (!isset($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    order_reject('method', 405, 'Method Not Allowed');
}

/* --- Ловушка и метка времени ------------------------------------------ */

if (post_str('website', 200) !== '') {
    order_reject('honeypot', 200, '', true);
}

switch (order_token_check(post_str('order_token', 64), order_form_secret(), time())) {
    case 'ok':
        break;
    case 'too_fast':
        order_reject('too_fast', 200, '', true);
        break;
    case 'expired':
        order_reject('expired', 400, 'Страница была открыта слишком давно. Обновите её и отправьте заявку ещё раз.');
        break;
    default:
        order_reject('token', 400, 'Обновите страницу и отправьте заявку ещё раз.');
}

/* --- Поля формы -------------------------------------------------------- */

$name       = post_str('name', 200);
$phone      = order_normalize_phone(post_str('phone', 50));
$email      = post_str('email', 200);
$sborka     = strip_tags(post_str('sborka', 200));
$sborkaLink = post_str('sborkaLink', 2000);

if (!order_name_ok($name)) {
    order_reject('name', 400, 'Укажите имя');
}

if ($phone === '') {
    order_reject('phone', 400, 'Укажите телефон в формате +998 XX XXX-XX-XX');
}

if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    order_reject('email', 400, 'Некорректный e-mail');
}

// Ссылка на сборку — только на этот же сайт.
$linkHost = (string) parse_url($sborkaLink, PHP_URL_HOST);
$ourHost  = isset($_SERVER['HTTP_HOST']) ? preg_replace('/:\d+$/', '', (string) $_SERVER['HTTP_HOST']) : '';

if (!filter_var($sborkaLink, FILTER_VALIDATE_URL) || strcasecmp($linkHost, $ourHost) !== 0) {
    order_reject('link', 400, 'Не удалось прочитать сборку. Обновите страницу и попробуйте ещё раз.');
}

/* --- Состав и сумма — из базы ------------------------------------------ */

$items = order_parse_items($sborkaLink);
$lines = array();
$html  = '';
$sum   = 0;

if ($items) {
    $ids = array();
    foreach ($items as $it) {
        $ids[$it['id']] = $it['id'];
    }
    $ids      = array_values($ids);
    $products = db_rows_by_id(
        $mysqli,
        'SELECT id, name, price FROM products WHERE id IN (' . implode(', ', array_fill(0, count($ids), '?')) . ')',
        $ids
    );

    foreach ($items as $it) {
        if (!isset($products[$it['id']])) {
            continue;
        }
        $p     = $products[$it['id']];
        $block = db_row($mysqli, 'SELECT name FROM tree WHERE translit = ? AND level = 3 LIMIT 1', array($it['type']));
        $type  = $block ? trim($block['name']) : $it['type'];

        $sum    += (int) $p['price'] * $it['qty'];
        $lines[] = $type . ': ' . $it['qty'] . ' x ' . $p['name'];
        $html   .= '<dt>' . e($type) . '</dt><dd>' . (int) $it['qty'] . ' x ' . e($p['name']) . '</dd>';
    }
}

if (!$lines) {
    order_reject('items', 400, 'В сборке нет товаров');
}

$amount = number_format($sum, 0, '', '.') . ' UZS';

/* --- Лимит по IP --------------------------------------------------------- */

if (!order_rate_allow()) {
    order_reject('rate', 429, 'Заявка уже отправлена. Если нужно что-то изменить — позвоните нам или попробуйте позже.');
}

/* --- Сохранение и отправка ---------------------------------------------- */

$phoneText = order_format_phone($phone);

try {
    db_exec(
        $mysqli,
        'INSERT INTO orders (name, email, phone, package_type, price, date) VALUES (?, ?, ?, ?, ?, ?)',
        array($name, $email, $phoneText, $sborka, $amount, date('Y-m-d'))
    );
} catch (Throwable $e) {
    // Заявка всё равно уходит в Telegram — менеджер её увидит.
    error_log('order: запись в orders не удалась — ' . $e->getMessage());
}

SendMailToBot(
    'Имя клиента: ' . $name . PHP_EOL
    . 'Телефон клиента: ' . $phoneText . PHP_EOL
    . 'Email клиента: ' . $email . PHP_EOL
    . 'Ссылка на сборку: ' . $sborkaLink . PHP_EOL
    . 'Цена: ' . $amount . PHP_EOL
    . 'Сборка: ' . $sborka . PHP_EOL
    . implode(PHP_EOL, $lines) . PHP_EOL
);

/*
 * Письмо — дополнительный канал к Telegram. Если SMTP недоступен,
 * SendOrderMail() пишет в лог и возвращает false: заявка уже принята,
 * и клиент не должен видеть ошибку почты.
 */
SendOrderMail(array(
    'name'       => $name,
    'email'      => $email,
    'phone'      => $phoneText,
    'sborka'     => $sborka,
    'config'     => $html,
    'amount'     => $amount,
    'sborkaLink' => $sborkaLink,
));

echo ORDER_OK_MESSAGE;
