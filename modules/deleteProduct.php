<?php
include '../functions.php';
//$mask = "../images/products/rTZQZ2NIYSg.jpg";
//if (file_exists($mask)) {unlink($mask); echo "OK";}else{echo $mask;}

  $prod_id = $_POST['prod_id'];
  //echo $id;
  $pieces = explode(":", $prod_id);
 $sql = "DELETE FROM products WHERE id=$pieces[0]";
  $QR = $mysqli->query($sql);
  if($QR){
      $mask = "../configurator/images/products/$pieces[1]";
      if (file_exists($mask)) {unlink($mask);}
      echo 'Товар успешно удален';   
  }  
  else {
      echo 'Ошибка: '.$mysqli->error;
  }