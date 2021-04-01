<?php
ini_set('display_errors', 1); 
error_reporting(E_ALL);
include '../../functions.php';
$param = '';
if (isset($_POST['tree_id'])) {
   $param = $_POST['tree_id'];
}

$sql = "select f.id, f.f_name from tree i join tree_filter tf on i.id=tf.tree_id join filters f on f.id=tf.filter_id WHERE tf.tree_id = '$param'";
$res = $mysqli->query($sql);
//Создаем масив где ключ массива является ID меню
$array = array();

while($row = $res->fetch_assoc()){
    $id = $row['id'];
    $name = $row['f_name'];
    $array[] = array("id" => $id,
                    "name" => $name);
}
echo json_encode($array);
?>