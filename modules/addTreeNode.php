<?php
require_once __DIR__ . '/../functions.php';
require_once __DIR__ . '/strtr.php';
require_admin();

$name     = post_str('name', 200);
$parentId = post_int('parent_id');
$level    = post_int('level');
$sort     = post_int('sort', 0);
$enabled  = post_int('enabled', 1);

if ($name === '' || $parentId === null || $level === null) {
    http_response_code(400);
    exit('Не все поля заполнены');
}

$slug = translit($name);

try {
    db_exec(
        $mysqli,
        'INSERT INTO `tree` (`name`, `parent_id`, `level`, `group_id`, `link`, `translit`, `sort`, `enabled`)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
        array($name, $parentId, $level, $slug, $slug, '', $sort, $enabled ? 1 : 0)
    );
    echo 'Узел ' . $level . ' уровня успешно записан в базу';
} catch (Throwable $e) {
    error_log('addTreeNode: ' . $e->getMessage());
    http_response_code(500);
    echo 'Ошибка при добавлении узла';
}
