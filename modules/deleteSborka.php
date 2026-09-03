<?php
include '../functions.php';
//$mask = "../images/products/rTZQZ2NIYSg.jpg";
//if (file_exists($mask)) {unlink($mask); echo "OK";}else{echo $mask;}

$sborka_id = $_POST['prod_id'];
//echo $id;
$pieces = explode(":", $sborka_id);
$sql = "DELETE FROM sborki WHERE id=$pieces[0]";
$QR = $mysqli->query($sql);
if($QR){
    $mask = "../configurator/images/sborki/$pieces[1]";
    if (file_exists($mask)) {unlink($mask);}
    echo 'Сборка успешно удалена';
}
else {
    echo 'Ошибка: '.$mysqli->error;
}