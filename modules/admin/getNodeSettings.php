<?php

include '../../functions.php';
header('Content-Type: application/json; charset=UTF-8');

$id = isset($_POST['tree_id']) ? (int)$_POST['tree_id'] : 0;

$row = null;
if ($id > 0) {
    $res = $mysqli->query("SELECT id, name, uncheck_btn, onLoadcountable FROM `tree` WHERE id = $id");
    if ($res) {
        $row = $res->fetch_assoc();
    }
}

echo json_encode($row, JSON_UNESCAPED_UNICODE);
