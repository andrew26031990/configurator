<?php

include '../../functions.php';
header('Content-Type: application/json; charset=UTF-8');

$id = isset($_POST['tree_id']) ? (int)$_POST['tree_id'] : 0;
$uncheck = (isset($_POST['uncheck_btn']) && ($_POST['uncheck_btn'] == '1' || $_POST['uncheck_btn'] === 'true')) ? 1 : 0;
$onload  = (isset($_POST['onLoadcountable']) && ($_POST['onLoadcountable'] == '1' || $_POST['onLoadcountable'] === 'true')) ? 1 : 0;

if ($id > 0) {
    $ok = $mysqli->query("UPDATE `tree` SET uncheck_btn = $uncheck, onLoadcountable = $onload WHERE id = $id");
    echo json_encode(array("success" => (bool)$ok, "error" => $mysqli->error));
} else {
    echo json_encode(array("success" => false, "error" => "Некорректный id ноды"));
}
