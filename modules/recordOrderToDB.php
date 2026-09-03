<?php
/**
 * Сохранение заявки в таблицу orders.
 * Публичный эндпоинт, вызывается из main.js сразу после mail.php.
 */

require_once __DIR__ . '/../functions.php';

header('Content-Type: text/plain; charset=UTF-8');

if (!throttle_ok('order_db', 20)) {
    http_response_code(429);
    exit('Заявка уже сохранена');
}

$name   = post_str('name', 200);
$email  = post_str('email', 200);
$phone  = post_str('phone', 200);
$sborka = post_str('sborka', 200);
$amount = post_str('amount', 200);

if ($name === '' || $phone === '') {
    http_response_code(400);
    exit('Не все поля заполнены');
}

if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $email = '';
}

try {
    db_exec(
        $mysqli,
        'INSERT INTO orders (name, email, phone, package_type, price, date) VALUES (?, ?, ?, ?, ?, ?)',
        array($name, $email, $phone, $sborka, $amount, date('Y-m-d'))
    );
    echo 'Заказ успешно добавлен в базу';
} catch (Throwable $e) {
    error_log('recordOrderToDB: ' . $e->getMessage());
    http_response_code(500);
    echo 'Не удалось сохранить заказ';
}
