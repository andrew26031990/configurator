<?php
include '../functions.php';

$node_id = $_POST['node_id'];
DeleteFromBase($mysqli, $node_id);

function DeleteFromBase($mysqli, $id)
{
  $mysqli->query("delete from tree where id=$id");  
  $QR=$mysqli->query("select * from tree where parent_id=$id");
  while ($Row=$QR->fetch_assoc())
  {
      //echo $Row['id'];
    DeleteFromBase($mysqli, $Row['id']);
  }
  $mysqli->query("delete from tree where parent_id=$id");
}


