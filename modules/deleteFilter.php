<?php
require_once __DIR__ . '/../functions.php';
require_admin();
require_capability('filters.manage');

$id = post_int('filter_id');

if ($id === null || $id <= 0) {
    http_response_code(400);
    exit('Некорректный идентификатор фильтра');
}

try {
    $filter = db_row($mysqli, 'SELECT f_name FROM filters WHERE id = ?', array($id));
    db_exec($mysqli, 'DELETE FROM filters WHERE id = ?', array($id));
    if ($filter) {
        audit_log($mysqli, 'filter.delete', "Удалён фильтр «{$filter['f_name']}» (id {$id})", array('ids' => array($id)));
    }
    echo 'Фильтр успешно удален';
} catch (Throwable $e) {
    error_log('deleteFilter: ' . $e->getMessage());
    http_response_code(500);
    echo 'Ошибка при удалении фильтра';
}
