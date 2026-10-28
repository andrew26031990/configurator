<?php

include '../functions.php';
header('Content-Type: application/json; charset=UTF-8');

//Выборка из дерева
$sql = "SELECT id, parent_id as parent, name as text, level FROM `tree`";
$res = $mysqli->query($sql);

//Иконки по уровню (level=3 — тип товара, отличается от остальных)
$levelIcons = array(
    0 => 'fas fa-home text-secondary',
    1 => 'fas fa-desktop text-primary',
    2 => 'fas fa-folder text-warning',
    3 => 'fas fa-microchip text-info',
);

//Создаем масив где ключ массива является ID меню
$cat = array();
while($data = mysqli_fetch_assoc($res)) {
    $thisref = &$refs[$data['id']];
    $thisref['id'] = $data['id']; // if id is 0  , it's a root node, replace with '#'
    $thisref['parent'] = $data['parent'] == 0 ? '#' : $data['parent'];// if parent id is 0, it's a root node, replace with '#'
    $thisref['text'] = $data['text'];
    $lvl = (int)$data['level'];
    $thisref['icon'] = isset($levelIcons[$lvl]) ? $levelIcons[$lvl] : 'fas fa-circle text-muted';

    $cat[] = &$thisref;
}
//Выборка из дерева

//Выборка из товаров
//$sql2 = "SELECT prod.id as id, prod.name as text, tp.tree_id as parent FROM products as prod JOIN tree_prod as tp ON tp.prod_id = prod.id";
//$res2 = $mysqli->query($sql2);

//Создаем масив где ключ массива является ID меню
//$cat2 = array();
//while($data = mysqli_fetch_assoc($res2)) {
 //   $thisref = &$refs[$data['id']];
 //   $thisref['id'] = $data['id']; // if id is 0  , it's a root node, replace with '#'
 //   $thisref['parent'] = $data['parent'] == 0 ? '#' : $data['parent'];// if parent id is 0, it's a root node, replace with '#'
 //   $thisref['text'] = $data['text'];

 //   $cat2[] = &$thisref;        
//}

//array_walk_recursive($cat2, 'good_example');
//Выборка из товаров
//$cat3 = array_merge($cat,$cat2);
echo json_encode($cat, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
