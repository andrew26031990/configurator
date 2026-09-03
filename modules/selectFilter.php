<?php
require_once __DIR__ . '/../functions.php';
require_admin();

$treeId = post_int('tip_tovara');

if ($treeId === null || $treeId <= 0) {
    json_out(array());
    return;
}

json_out(db_rows(
    $mysqli,
    'SELECT filters.id AS id, filters.f_name AS name
       FROM tree_filter
       JOIN filters ON tree_filter.filter_id = filters.id
      WHERE tree_filter.tree_id = ?',
    array($treeId)
));
