<?php

include '../functions.php';

$tree_id = $_POST['tree_id'];
$filter_id = $_POST['filter_id'];
//$sql = "";

if($mysqli->query("INSERT INTO `tree_filter` (`tree_id`, `filter_id`) VALUES ($tree_id, $filter_id)")){
    echo 'Связь успешно добавлена';
}else{
    echo $mysqli->error.' Такая связь уже существует';
}

//$arr = $mysqli->query($sql);



