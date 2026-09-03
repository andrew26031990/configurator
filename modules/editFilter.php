<?php
require_once __DIR__ . '/../functions.php';
require_admin();

$id   = post_int('filter_id');
$name = post_str('f_name', 200);

if ($id === null || $id <= 0 || $name === '') {
    http_response_code(400);
    exit('Не все поля заполнены');
}

try {
    db_exec($mysqli, 'UPDATE filters SET f_name = ? WHERE id = ?', array($name, $id));
    echo 'Название фильтра обновлено';
} catch (Throwable $e) {
    error_log('editFilter: ' . $e->getMessage());
    http_response_code(500);
    echo 'Ошибка при обновлении фильтра';
}
