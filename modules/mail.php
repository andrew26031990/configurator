<?php
/**
 * Приём заявки из конфигуратора: отправка в Telegram-канал.
 * Публичный эндпоинт, авторизация не требуется.
 */

require_once __DIR__ . '/../security.php';
require_once __DIR__ . '/messages_to_telegram.php';

header('Content-Type: text/plain; charset=UTF-8');

if (!throttle_ok('order', 20)) {
    http_response_code(429);
    exit('Заявка уже отправлена, подождите немного');
}

$name       = post_str('name', 200);
$phone      = post_str('phone', 50);
$email      = post_str('email', 200);
$config     = strip_tags(post_str('config', 5000));
$sborka     = strip_tags(post_str('sborka', 200));
$amount     = post_str('amount', 50);
$sborkaLink = post_str('sborkaLink', 2000);

if ($name === '' || $phone === '') {
    http_response_code(400);
    exit('Не все поля заполнены');
}

if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    exit('Некорректный e-mail');
}

if ($sborkaLink !== '' && !filter_var($sborkaLink, FILTER_VALIDATE_URL)) {
    $sborkaLink = '';
}

$config = str_replace(array('p', 'li'), array('dt', 'dd'), $config);

$message = 'Имя клиента: ' . $name . PHP_EOL
    . 'Телефон клиента: ' . $phone . PHP_EOL
    . 'Email клиента: ' . $email . PHP_EOL
    . 'Ссылка на сборку: ' . $sborkaLink . PHP_EOL
    . 'Цена: ' . $amount . PHP_EOL
    . 'Сборка: ' . $sborka . PHP_EOL
    . $config . PHP_EOL;

SendMailToBot($message);

echo 'Ваша заявка принята. В ближайшее время с вами свяжутся сотрудники компании PCMARKET';
