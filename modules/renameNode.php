<?php

include '../functions.php';
include 'strtr.php';

$nodeId = $_POST['node_id'];
$nodeName = $_POST['nodeName'];
$translit = translit($nodeName);
$sql = "UPDATE `tree` SET name = '$nodeName', translit = '$translit' WHERE id='$nodeId'";
  $QR = $mysqli->query($sql);
  if(!$QR){
     echo 'Ошибка: '.$mysqli->error;   
  }
