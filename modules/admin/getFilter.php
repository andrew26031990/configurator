<?php
require_once __DIR__ . '/../../functions.php';
require_admin();
require_capability('filters.manage');

$id = post_int('filter_id');

if ($id === null || $id <= 0) {
    json_out(null);
    return;
}

$filter = db_row($mysqli, 'SELECT id, f_name FROM filters WHERE id = ?', array($id));

if ($filter) {
    $filter['trees'] = getFilterTreeIds($mysqli, $id);
}

json_out($filter);
