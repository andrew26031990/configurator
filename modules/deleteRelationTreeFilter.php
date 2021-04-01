<?php
include '../functions.php';
//$mask = "../images/products/rTZQZ2NIYSg.jpg";
//if (file_exists($mask)) {unlink($mask); echo "OK";}else{echo $mask;}

  $relation_id = $_POST['relation_id'];
  $sql = "DELETE FROM tree_filter WHERE id='$relation_id'";
  $QR = $mysqli->query($sql);
  if($QR){
      echo 'Связь фильтра с категорией разорвана';
  }  
  else {
      echo 'Ошибка: '.$mysqli->error;
  }