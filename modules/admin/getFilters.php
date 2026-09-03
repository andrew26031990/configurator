<?php
require_once __DIR__ . '/../../functions.php';
require_admin();

$treeId = post_int('tree_id');

if ($treeId === null || $treeId <= 0) {
    json_out(array());
    return;
}

json_out(db_rows(
    $mysqli,
    'SELECT f.id, f.f_name
       FROM tree t
       JOIN tree_filter tf ON t.id = tf.tree_id
       JOIN filters     f  ON f.id = tf.filter_id
      WHERE tf.tree_id = ?',
    array($treeId)
));
