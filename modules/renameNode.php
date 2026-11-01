<?php
require_once __DIR__ . '/../functions.php';
require_once __DIR__ . '/strtr.php';
require_admin();
require_capability('categories.manage');

$id   = post_int('node_id');
$name = post_str('nodeName', 200);

if ($id === null || $id <= 0 || $name === '') {
    http_response_code(400);
    exit('Некорректные данные узла');
}

try {
    $old = db_row($mysqli, 'SELECT name FROM `tree` WHERE id = ?', array($id));
    db_exec(
        $mysqli,
        'UPDATE `tree` SET name = ?, translit = ? WHERE id = ?',
        array($name, translit($name), $id)
    );
    $oldName = $old ? $old['name'] : '';
    audit_log($mysqli, 'node.rename', "Категория «{$oldName}» переименована в «{$name}» (id {$id})", array(
        'ids'     => array($id),
        'tree_id' => $id,
        'details' => array('changes' => audit_diff(array('name' => $oldName), array('name' => $name))),
    ));
} catch (Throwable $e) {
    error_log('renameNode: ' . $e->getMessage());
    http_response_code(500);
    echo 'Ошибка при переименовании узла';
}
