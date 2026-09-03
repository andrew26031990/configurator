<?php
require_once __DIR__ . '/../functions.php';
require_admin();

$id = post_int('relation_id');

if ($id === null || $id <= 0) {
    http_response_code(400);
    exit('Некорректный идентификатор связи');
}

try {
    db_exec($mysqli, 'DELETE FROM tree_prod WHERE id = ?', array($id));
    echo 'Связь товара с категорией разорвана';
} catch (Throwable $e) {
    error_log('deleteRelationTreeProduct: ' . $e->getMessage());
    http_response_code(500);
    echo 'Ошибка при удалении связи';
}
