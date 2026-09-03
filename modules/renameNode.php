<?php
require_once __DIR__ . '/../functions.php';
require_once __DIR__ . '/strtr.php';
require_admin();

$id   = post_int('node_id');
$name = post_str('nodeName', 200);

if ($id === null || $id <= 0 || $name === '') {
    http_response_code(400);
    exit('Некорректные данные узла');
}

try {
    db_exec(
        $mysqli,
        'UPDATE `tree` SET name = ?, translit = ? WHERE id = ?',
        array($name, translit($name), $id)
    );
} catch (Throwable $e) {
    error_log('renameNode: ' . $e->getMessage());
    http_response_code(500);
    echo 'Ошибка при переименовании узла';
}
