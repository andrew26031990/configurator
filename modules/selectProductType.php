<?php

include '../functions.php';

$filter_id = $_POST['filter'];
//echo $filter_f.$component_f;
$sql = "SELECT id, name FROM `tree` WHERE parent_id in (select id from `tree` where parent_id ='$filter_id')";


$arr = $mysqli->query($sql);

$res = array();

while($row = $arr->fetch_assoc()){
    $id = $row['id'];
    $name = $row['name'];
    $res[] = array("id" => $id,
                    "name" => $name);
}
echo json_encode($res);

