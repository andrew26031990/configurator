<?php
require_once __DIR__ . '/../../functions.php';
require_admin();

$treeId = post_int('tree_id');

if ($treeId === null || $treeId <= 0) {
    json_out(null);
    return;
}

json_out(db_row(
    $mysqli,
    'SELECT id, name, uncheck_btn, onLoadcountable FROM tree WHERE id = ?',
    array($treeId)
));
