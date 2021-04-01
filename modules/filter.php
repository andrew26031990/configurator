<?php

include '../functions.php';

$filter_f = $_POST['filter'];
$component_f =  $_POST['component'];
$filter_id = $_POST['filterId'];
$data_id = $_POST['dataId'];
//echo $filter_f.$component_f;
if($filter_id == '0000'){
    $sql = "SELECT prod.id, prod.name, prod.price, prod.image, prod.description FROM `products` as prod JOIN `tree_prod` as tp on tp.prod_id = prod.id JOIN `tree` as tr on tr.id = tp.tree_id where tp.tree_id = $data_id ORDER BY prod.price";
}else{
    $sql = "select id, name, price, image, description from products where f_id = '$filter_id'";
    $sql = "SELECT prod.id, prod.name, prod.price, prod.image, prod.description FROM `products` as prod JOIN `tree_prod` as tp on tp.prod_id = prod.id JOIN `tree` as tr on tr.id = tp.tree_id where tp.tree_id = '$data_id' and prod.f_id = '$filter_id' ORDER BY prod.price";
}

$arr = $mysqli->query($sql);

$res = array();

while($row = $arr->fetch_assoc()){
    $id = $row['id'];
    $name = $row['name'];
    $price = $row['price'];
    $image = $row['image'];
    $description = $row['description'];
    $res[] = array("id" => $id,
                    "name" => $name,
                        "price" => $price,
                            "image" => $image,
                                "description" => $description);
}
echo json_encode($res);
