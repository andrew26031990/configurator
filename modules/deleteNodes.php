<?php
require_once __DIR__ . '/../functions.php';
require_admin();
require_capability('categories.manage');

$id = post_int('node_id');

if ($id === null || $id <= 0) {
    http_response_code(400);
    exit('Некорректный идентификатор узла');
}

/**
 * Рекурсивно удаляет узел дерева вместе с потомками.
 *
 * @param  mysqli $mysqli
 * @param  int    $id
 * @param  int    $depth страховка от зацикливания на битых parent_id
 * @return void
 */
function deleteTreeNode($mysqli, $id, $depth = 0)
{
    if ($depth > 20) {
        return;
    }

    $children = db_rows($mysqli, 'SELECT id FROM tree WHERE parent_id = ?', array($id));

    foreach ($children as $child) {
        deleteTreeNode($mysqli, (int) $child['id'], $depth + 1);
    }

    db_exec($mysqli, 'DELETE FROM tree_prod   WHERE tree_id = ?', array($id));
    db_exec($mysqli, 'DELETE FROM tree_filter WHERE tree_id = ?', array($id));
    db_exec($mysqli, 'DELETE FROM tree        WHERE id      = ?', array($id));
}

try {
    $node = db_row($mysqli, 'SELECT name, level FROM tree WHERE id = ?', array($id));
    deleteTreeNode($mysqli, $id);
    if ($node) {
        audit_log($mysqli, 'node.delete', "Удалена категория «{$node['name']}» (id {$id}) вместе с подкатегориями", array(
            'ids'     => array($id),
            'tree_id' => $id,
            'details' => array('level' => $node['level']),
        ));
    }
    echo 'Узел удалён';
} catch (Throwable $e) {
    error_log('deleteNodes: ' . $e->getMessage());
    http_response_code(500);
    echo 'Ошибка при удалении узла';
}
