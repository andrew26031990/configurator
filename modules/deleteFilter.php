<?php
require_once __DIR__ . '/../functions.php';
require_admin();

$id = post_int('filter_id');

if ($id === null || $id <= 0) {
    http_response_code(400);
    exit('Некорректный идентификатор фильтра');
}

try {
    db_exec($mysqli, 'DELETE FROM filters WHERE id = ?', array($id));
    echo 'Фильтр успешно удален';
} catch (Throwable $e) {
    error_log('deleteFilter: ' . $e->getMessage());
    http_response_code(500);
    echo 'Ошибка при удалении фильтра';
}
