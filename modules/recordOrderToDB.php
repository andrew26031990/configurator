<?php

$name = $_POST['name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$sborka = $_POST['sborka'];
$amount = $_POST['amount'];
$today = date("Y-m-d");

include_once '../functions.php';
//insert form data in the database
try{
    $insert = $mysqli->query("INSERT INTO orders (name, email, phone, package_type, price, date) VALUES ('".$name."', '".$email."', '".$phone."', '".$sborka."', '".$amount."', '".$today."')");
    echo 'Заказ успешно добавлен в базу';
}catch(Exception $ex){
    echo $e->getMessage();
}
