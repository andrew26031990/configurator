<?php
require_once __DIR__ . '/../functions.php';
require_admin();

$parentId = post_int('filter');

if ($parentId === null || $parentId <= 0) {
    json_out(array());
    return;
}

json_out(db_rows(
    $mysqli,
    'SELECT child.id, child.name
       FROM `tree` AS child
       JOIN `tree` AS mid ON mid.id = child.parent_id
      WHERE mid.parent_id = ?',
    array($parentId)
));
