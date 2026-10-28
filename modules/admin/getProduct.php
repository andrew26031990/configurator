<?php

include '../../functions.php';
header('Content-Type: application/json; charset=UTF-8');

$id = isset($_POST['prod_id']) ? (int)$_POST['prod_id'] : 0;

$prod = null;
if ($id > 0) {
    $res = $mysqli->query("SELECT id, name, price, description, image, f_id FROM `products` WHERE id = $id");
    $prod = $res ? $res->fetch_assoc() : null;
    if ($prod) {
        $prod['trees'] = getProductTreeIds($mysqli, $id);
    }
}

echo json_encode($prod, JSON_UNESCAPED_UNICODE);
