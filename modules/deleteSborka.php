<?php
require_once __DIR__ . '/../functions.php';
require_admin();

$pieces = explode(':', post_str('prod_id', 300), 2);
$id     = filter_var($pieces[0], FILTER_VALIDATE_INT);
$image  = isset($pieces[1]) ? $pieces[1] : '';

if ($id === false || $id <= 0) {
    http_response_code(400);
    exit('Некорректный идентификатор сборки');
}

try {
    db_exec($mysqli, 'DELETE FROM sborki WHERE id = ?', array($id));
    delete_upload(APP_ROOT . '/configurator/images/sborki', $image);
    echo 'Сборка успешно удалена';
} catch (Throwable $e) {
    error_log('deleteSborka: ' . $e->getMessage());
    http_response_code(500);
    echo 'Ошибка при удалении сборки';
}
