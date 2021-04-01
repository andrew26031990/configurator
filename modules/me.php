<?php

include '../functions.php';
header('Content-Type: application/json; charset=UTF-8');

//Выборка из дерева
$sql = "SELECT * FROM products";
$res = $mysqli->query($sql);

$cat = array();
while($data = mysqli_fetch_assoc($res)){
    $thisref = &$refs[$data['id']];
    $thisref['id'] = $data['id']; // if id is 0  , it's a root node, replace with '#'
    $thisref['name'] = $data['name'];// if parent id is 0, it's a root node, replace with '#'
    $thisref['description'] = $data['description'];
    $thisref['image'] = $data['image'];
    $thisref['price'] = $data['price'];
    $thisref['edit'] = $data['id'];
    $cat[] = &$thisref; 
}
$json_data = array(
    "data" => $cat
);
echo json_encode($json_data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
