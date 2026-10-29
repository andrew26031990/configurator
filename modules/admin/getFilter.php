<?php

include '../../functions.php';
header('Content-Type: application/json; charset=UTF-8');

$id = isset($_POST['filter_id']) ? (int)$_POST['filter_id'] : 0;

$filter = null;
if ($id > 0) {
    $res = $mysqli->query("SELECT id, f_name FROM `filters` WHERE id = $id");
    $filter = $res ? $res->fetch_assoc() : null;
    if ($filter) {
        $filter['trees'] = getFilterTreeIds($mysqli, $id);
    }
}

echo json_encode($filter, JSON_UNESCAPED_UNICODE);
