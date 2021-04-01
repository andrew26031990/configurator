<?php

include '../functions.php';
$tree_id = 0;
$prod_id = 0;
if($_POST['tree_id'] != 0) $tree_id = $_POST['tree_id'];
if($_POST['prod_id'] != 0) $prod_id = $_POST['prod_id'];
//$sql = "";
if($tree_id != 0 && $prod_id != 0){
    if($mysqli->query("INSERT INTO `tree_prod` (`tree_id`, `prod_id`) VALUES ($tree_id,$prod_id)")){
        echo 'Связь успешно добавлена';
    }else{
        echo $mysqli->error.' Такая связь уже существует';
    }
}else{
    echo 'Не выбран товар или категория';
}
//$arr = $mysqli->query($sql);



