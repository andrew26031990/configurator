<?php

include '../functions.php';

$filter_id = $_POST['tip_tovara'];
//echo $filter_f.$component_f;
$sql = "select filters.id as id, filters.f_name as name from tree_filter join filters on tree_filter.filter_id = filters.id where tree_filter.tree_id = '$type'";


$arr = $mysqli->query($sql);

$res = array();

while($row = $arr->fetch_assoc()){
    $id = $row['id'];
    $name = $row['name'];
    $res[] = array("id" => $id,
                    "name" => $name);
}
echo json_encode($res);

