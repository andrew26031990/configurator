<?php

include '../functions.php';
include 'strtr.php';

$nodeId = $_POST['node_id'];
$upcomingNodeLevel = $_POST['upcomingNodeLevel'];
$nodeName = $_POST['nodeName'];
//$sql = "";

if($upcomingNodeLevel == 3 ){
    $translit = translit($nodeName);
    if($mysqli->query("INSERT INTO `tree` (name, parent_id, level, group_id, link, translit, sort, enabled) 
    VALUES ('$nodeName', '$nodeId', '$upcomingNodeLevel', (SELECT t.group_id FROM `tree` as t WHERE t.id = '$nodeId'), '','$translit', 0, 1)")){
        echo 'Узел добавлен';
    }else{
        echo $mysqli->error;
    }
}else if($upcomingNodeLevel == 2){
    if($mysqli->query("INSERT INTO `tree` (name, parent_id, level, group_id, link, translit, sort, enabled) 
    VALUES ('$nodeName', '$nodeId', '$upcomingNodeLevel', (SELECT t.group_id FROM `tree` as t WHERE t.id = '$nodeId'), '','', 0, 1)")){
        echo 'Узел добавлен';
    }else{
        echo $mysqli->error;
    }
}else if($upcomingNodeLevel == 1){
    $translit = translit($nodeName);
    if($mysqli->query("INSERT INTO `tree` (name, parent_id, level, group_id, link, translit, sort, enabled) 
    VALUES ('$nodeName', '$nodeId', '$upcomingNodeLevel', '$translit', '$translit','', 0, 1)")){
        echo 'Узел добавлен';
    }else{
        echo $mysqli->error;
    }
}



