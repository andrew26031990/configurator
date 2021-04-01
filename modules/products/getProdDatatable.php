<?php

include '../../functions.php';

header('Content-Type: application/json; charset=UTF-8');

//Выборка из дерева
$sql = "SELECT id, name, price FROM `products`";
$res = $mysqli->query($sql);

$totalData=mysqli_num_rows($res);

$totalFilter=$totalData;

//Создаем масив где ключ массива является ID меню
$data = array();
while($row = mysqli_fetch_array($res)) {
    $subdata=array();
    $subdata[]=$row[0]; //id
    $subdata[]=$row[1]; //name
    $subdata[]=$row[2]; //salary          //create event on click in button edit in cell datatable for display modal dialog           $row[0] is id in table on database
    $subdata[]='<button type="button" id="getEdit" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#myModal" data-id="'.$row[0].'"><i class="glyphicon glyphicon-pencil">&nbsp;</i>Edit</button>
                <a href="index.php?delete='.$row[0].'" onclick="return confirm(\'Are You Sure ?\')" class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-trash">&nbsp;</i>Delete</a>';
    $data[]=$subdata;       
}

$json_data=array(
    "draw"              =>  intval('draw'),
    "recordsTotal"      =>  intval($totalData),
    "recordsFiltered"   =>  intval($totalFilter),
    "data"              =>  $data
);

echo json_encode($json_data);

