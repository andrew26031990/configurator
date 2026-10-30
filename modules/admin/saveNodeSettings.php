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
    db_exec(
        $mysqli,
        'UPDATE tree SET uncheck_btn = ?, onLoadcountable = ? WHERE id = ?',
        array($uncheck, $onload, $treeId)
    );
    json_out(array('success' => true));
} catch (Throwable $e) {
    error_log('saveNodeSettings: ' . $e->getMessage());
    http_response_code(500);
    json_out(array('success' => false, 'error' => 'Ошибка сохранения'));
}
