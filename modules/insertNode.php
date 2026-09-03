<?php
require_once __DIR__ . '/../functions.php';
require_once __DIR__ . '/strtr.php';
require_admin();

$parentId = post_int('node_id');
$level    = post_int('upcomingNodeLevel');
$name     = post_str('nodeName', 200);

if ($parentId === null || $parentId <= 0 || $name === '' || !in_array($level, array(1, 2, 3), true)) {
    http_response_code(400);
    exit('Некорректные данные узла');
}

$slug = translit($name);

// Уровень 1 — корень категории конфигуратора, у него собственный group_id.
// Уровни 2 и 3 наследуют group_id родителя.
if ($level === 1) {
    $groupId  = $slug;
    $link     = $slug;
    $translit = '';
} else {
    $parent   = db_row($mysqli, 'SELECT group_id FROM `tree` WHERE id = ?', array($parentId));
    $groupId  = $parent ? (string) $parent['group_id'] : '';
    $link     = '';
    $translit = ($level === 3) ? $slug : '';
}

try {
    db_exec(
        $mysqli,
        'INSERT INTO `tree` (image, name, parent_id, level, group_id, link, translit, sort, enabled)
         VALUES (?, ?, ?, ?, ?, ?, ?, 0, 1)',
        array('', $name, $parentId, $level, $groupId, $link, $translit)
    );
    echo 'Узел добавлен';
} catch (Throwable $e) {
    error_log('insertNode: ' . $e->getMessage());
    http_response_code(500);
    echo 'Ошибка при добавлении узла';
}
