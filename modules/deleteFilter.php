<?php
include '../functions.php';
//$mask = "../images/products/rTZQZ2NIYSg.jpg";
//if (file_exists($mask)) {unlink($mask); echo "OK";}else{echo $mask;}

  $filter_id = $_POST['filter_id'];
 $sql = "DELETE FROM filters WHERE id=$filter_id";
  $QR = $mysqli->query($sql);
  if($QR){
      echo 'Фильтр успешно удален';   
  }  
  else {
      echo 'Ошибка: '.$mysqli->error;
  }