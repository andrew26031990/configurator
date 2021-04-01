<?php
include '../functions.php';

$filter_id = $_POST['filter_id'];
$f_name = $_POST['f_name'];

$sql = "UPDATE filters SET f_name = '$f_name' where id='$filter_id'";
$QR = $mysqli->query($sql);
if($QR){
    echo 'Название фильтра обновлено';
}  
else {
    echo 'Ошибка: '.$mysqli->error;
}

