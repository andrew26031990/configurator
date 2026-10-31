<?php
require_once __DIR__ . '/../../functions.php';
require_admin();
require_capability('categories.manage');

$treeId  = post_int('tree_id');
$uncheck = post_int('uncheck_btn') === 1 ? 1 : 0;
$onload  = post_int('onLoadcountable') === 1 ? 1 : 0;

if ($treeId === null || $treeId <= 0) {
    json_out(array('success' => false, 'error' => 'Некорректный id ноды'));
    return;
}

try {
    $before = db_row($mysqli, 'SELECT name, uncheck_btn, onLoadcountable FROM tree WHERE id = ?', array($treeId));
    db_exec(
        $mysqli,
        'UPDATE tree SET uncheck_btn = ?, onLoadcountable = ? WHERE id = ?',
        array($uncheck, $onload, $treeId)
    );
    if ($before) {
        $changes = audit_diff($before, array('uncheck_btn' => $uncheck, 'onLoadcountable' => $onload));
        if ($changes) {
            audit_log($mysqli, 'node.settings', "Изменены настройки категории «{$before['name']}» (id {$treeId})", array(
                'ids'     => array($treeId),
                'tree_id' => $treeId,
                'details' => array('changes' => $changes),
            ));
        }
    }
    json_out(array('success' => true));
} catch (Throwable $e) {
    error_log('saveNodeSettings: ' . $e->getMessage());
    http_response_code(500);
    json_out(array('success' => false, 'error' => 'Ошибка сохранения'));
}
