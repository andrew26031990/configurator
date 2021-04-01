<?php

//Устанавливаем кодировку и вывод всех ошибок
header('Content-Type: text/html; charset=UTF-8');
error_reporting(E_ALL);

//Объектно-ориентированный стиль
//$mysqli = new mysqli('localhost', 'victors90_config', '1Z*vIdRE', 'victors90_config');
$mysqli = new mysqli('localhost', 'victors90_confi2', '8h%MAdRC', 'victors90_confi2'); //real config

//Устанавливаем кодировку utf8
$mysqli->query("SET NAMES 'utf8'");
$mysqli->set_charset("utf8");

/*
 * Это "официальный" объектно-ориентированный способ сделать это
 * однако $connect_error не работал вплоть до версий PHP 5.2.9 и 5.3.0.
 */
if ($mysqli->connect_error) {
    die('Ошибка подключения (' . $mysqli->connect_errno . ') '
            . $mysqli->connect_error);
} else {
    //echo 'OK';
}

/*
 * Если нужно быть уверенным в совместимости с версиями до 5.2.9,
 * лучше использовать такой код
 */
if (mysqli_connect_error()) {
    die('Ошибка подключения (' . mysqli_connect_errno() . ') '
            . mysqli_connect_error());
}else{
    //echo 'OK';
}

//////////////////////////////////////////
/////////CONFIGURATOR////////////////////
/////////////////////////////////////////

function getRootNodesZero($mysqli){
    $sql = "SELECT * FROM `tree` where parent_id = 1 and enabled = '1'";
    $res = $mysqli->query($sql);

    //Создаем масив где ключ массива является ID меню
    $cat = array();
    while($row = $res->fetch_assoc()){
        $cat[$row['id']] = $row;
    }
    return $cat;
}

function getTitle($mysqli, $link){
    $sql = "SELECT * FROM `tree` where link = '$link'";
    $res = $mysqli->query($sql);

    //Создаем масив где ключ массива является ID меню
    $cat = array();
    while($row = $res->fetch_assoc()){
        $cat[$row['id']] = $row;
    }
    return $cat;
}

function getRootNodesFirst($mysqli, $cat){
    $sql = "SELECT * FROM `tree` where parent_id = (select id from tree where link = '$cat') and enabled = 1";
    $res = $mysqli->query($sql);

    //Создаем масив где ключ массива является ID меню
    $cat = array();
    while($row = $res->fetch_assoc()){
        $cat[$row['id']] = $row;
    }
    return $cat;
}

function getRootNodesSecond($mysqli, $id){
    $sql = "select id, name, translit, image from `tree` where parent_id = $id and enabled = 1  ORDER BY sort";
    $res = $mysqli->query($sql);
    //Создаем масив где ключ массива является ID меню
    $cat = array();
    while($row = $res->fetch_assoc()){
        $cat[$row['id']] = $row;
    }
    return $cat;

}

function getAllRootNodesSecond($mysqli, $id){
    $sql = "select id, name from `tree` where parent_id = $id";
    $res = $mysqli->query($sql);
    //Создаем масив где ключ массива является ID меню
    $cat = array();
    while($row = $res->fetch_assoc()){
        $cat[$row['id']] = $row;
    }
    return $cat;
}

function getAllChildNodes($mysqli, $cat){
    $sql = "SELECT * FROM `tree` where group_id = '$cat' and level = '3' and enabled = '1' ORDER BY sort";
    $res = $mysqli->query($sql);
    //Создаем масив где ключ массива является ID меню
    $cat = array();
    while($row = $res->fetch_assoc()){
        $cat[$row['id']] = $row;
    }
    return $cat;
}

function getAllProductsByTreeId($mysqli, $id){
    $sql = "select t.id, t.name, t.price, t.description, t.image from tree i join tree_prod tp on i.id=tp.tree_id join products t on t.id=tp.prod_id WHERE tp.tree_id = '$id' ORDER BY t.price";
    $res = $mysqli->query($sql);
    //Создаем масив где ключ массива является ID меню
    $cat = array();
    while($row = $res->fetch_assoc()){
        $cat[$row['id']] = $row;
    }
    return $cat;
}

function getFiltersByTreeId($mysqli, $id){
    $sql = "select f.* from tree i join tree_filter tf on i.id=tf.tree_id join filters f on f.id=tf.filter_id WHERE tf.tree_id = '$id'";
    $res = $mysqli->query($sql);
    //Создаем масив где ключ массива является ID меню
    $cat = array();
    while($row = $res->fetch_assoc()){
        $cat[$row['id']] = $row;
    }
    return $cat;
}

//////////////////////////////////////////
/////////CONFIGURATOR////////////////////
/////////////////////////////////////////


//////////////////////////////////////////
/////////ADMIN////////////////////
/////////////////////////////////////////

function getAllProducts($mysqli){
    $sql = "select prod.id, prod.name, prod.description, prod.price, prod.image, filter.f_name from `products` as prod JOIN filters as filter on prod.f_id = filter.id";
    $res = $mysqli->query($sql);
    //Создаем масив где ключ массива является ID меню
    $cat = array();
    while($row = $res->fetch_assoc()){
        $cat[$row['id']] = $row;
    }
    return $cat;
}

function getAllFilters($mysqli){
    $sql = "select * from filters";
    $res = $mysqli->query($sql);
    //Создаем масив где ключ массива является ID меню
    $cat = array();
    while($row = $res->fetch_assoc()){
        $cat[$row['id']] = $row;
    }
    return $cat;
}

function getNodes($mysqli, $level){
    $sql = "select * from tree where level = '$level'";
    $res = $mysqli->query($sql);
    //Создаем масив где ключ массива является ID меню
    $cat = array();
    while($row = $res->fetch_assoc()){
        $cat[$row['id']] = $row;
    }
    return $cat;
}

function getTreeFilterRelations($mysqli){ 
    $sql = "SELECT tf.id, (SELECT name from `tree` where link = t.group_id) as sborka, t.name, f.f_name, tf.tree_id, tf.filter_id FROM `tree_filter` as tf join `tree` as t on tf.tree_id = t.id join `filters` as f on tf.filter_id = f.id";
    $res = $mysqli->query($sql);
    //Создаем масив где ключ массива является ID меню
    $cat = array();
    while($row = $res->fetch_assoc()){
        $cat[$row['id']] = $row;
    }
    return $cat;
}

function getTreeProdRelations($mysqli){ 
    $sql = "SELECT tp.id, (SELECT name from `tree` where link = t.group_id) as sborka, t.name, p.name as product, tp.tree_id, tp.prod_id FROM `tree_prod` as tp join `tree` as t on tp.tree_id = t.id join `products` as p on tp.prod_id = p.id";
    $res = $mysqli->query($sql);
    //Создаем масив где ключ массива является ID меню
    $cat = array();
    while($row = $res->fetch_assoc()){
        $cat[$row['id']] = $row;
    }
    return $cat;
}

function CountProducts($mysqli)
{
    $row = 0;
  $res = $mysqli->query("select * from products");  
  if ($res) 
    { 
        $row = mysqli_num_rows($res); 
    }
    return $row;
}

function CountFilters($mysqli)
{
  $row = 0;
  $res = $mysqli->query("select * from filters");  
  if ($res) 
    { 
        $row = mysqli_num_rows($res); 
    }
    return $row;
}

function CountSborka($mysqli)
{
  $row = 0;
  $res = $mysqli->query("select * from tree where level = '1'");  
  if ($res) 
    { 
        $row = mysqli_num_rows($res); 
    }
    return $row;
}

function CheckLoginPass($mysqli, $login, $pass)
{
  $sql = "SELECT id FROM users WHERE username = '$login' and password = '$pass'";
  $result = $mysqli->query($sql);
  $count = mysqli_num_rows($result);
  $row = mysqli_fetch_array($result,MYSQLI_ASSOC);
  $id = $row['id'];
  $date = date("d.m.Y/h:m:sa", strtotime('+2 hours'));
  if($count > 0){
      $mysqli->query("UPDATE users set last_login = '$date' WHERE id = '$id'");
      return true;
  }else{
      return false;
  }
}

function LastLoginsUpdate($mysqli, $login, $pass)
{
  $sql = "SELECT id FROM users WHERE username = '$login' and password = '$pass'";
  $result = $mysqli->query($sql);
  $count = mysqli_num_rows($result);
  if($count > 0){
      $mysqli->query("");
      return true;
  }else{
      return false;
  }
}

function getAllUsers($mysqli){
    $sql = "SELECT * FROM `users`";
    $res = $mysqli->query($sql);

    //Создаем масив где ключ массива является ID меню
    $users = array();
    while($row = $res->fetch_assoc()){
        $users[$row['id']] = $row;
    }
    return $users;
}

function exit_cab(){
    unset($_SESSION['username']);
}
//select t.name from tree i join tree_prod tp on i.id=tp.tree_id join products t on t.id=tp.prod_id WHERE tp.tree_id = 4


    
    