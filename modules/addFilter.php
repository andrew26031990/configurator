<?php

if($_POST['filter'] !='')
{
    $name = $_POST['filter'];
    include_once '../functions.php';
    //insert form data in the database
    try{
        $insert = $mysqli->query("INSERT INTO filters (f_name) VALUES ('".$name."')");
        echo 'Фильтр успешно добавлен в базу';
    }catch(Exception $ex){
        echo $e->getMessage();
    }
}else{
    echo 'Не все поля заполнены';
}
