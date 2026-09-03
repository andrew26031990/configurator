<?php
require_once __DIR__ . '/../functions.php';
require_admin();

$name = post_str('filter', 200);

if ($name === '') {
    http_response_code(400);
    exit('Не все поля заполнены');
}

try {
    db_exec($mysqli, 'INSERT INTO filters (f_name) VALUES (?)', array($name));
    echo 'Фильтр успешно добавлен в базу';
} catch (Throwable $e) {
    error_log('addFilter: ' . $e->getMessage());
    http_response_code(500);
    echo 'Ошибка при добавлении фильтра';
}
