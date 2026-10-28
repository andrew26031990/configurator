<?php

include '../../functions.php';
header('Content-Type: application/json; charset=UTF-8');

$id = isset($_POST['tree_id']) ? (int)$_POST['tree_id'] : 0;

$products = array();

if ($id > 0) {
    // Собираем саму ноду и всех её потомков (рекурсивно), затем товары, привязанные к любой из них.
    $sql = "
        WITH RECURSIVE sub AS (
            SELECT id FROM `tree` WHERE id = $id
            UNION ALL
            SELECT t.id FROM `tree` t JOIN sub ON t.parent_id = sub.id
        )
        SELECT DISTINCT p.id, p.name, p.price, p.image, f.f_name
        FROM `products` p
        JOIN `tree_prod` tp ON tp.prod_id = p.id
        LEFT JOIN `filters` f ON f.id = p.f_id
        WHERE tp.tree_id IN (SELECT id FROM sub)
        ORDER BY p.name
    ";
    $res = $mysqli->query($sql);
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $products[] = array(
                "id"     => $row['id'],
                "name"   => $row['name'],
                "price"  => $row['price'],
                "image"  => $row['image'],
                "f_name" => $row['f_name']
            );
        }
    }
}

echo json_encode(array(
    "count"    => count($products),
    "products" => $products
), JSON_UNESCAPED_UNICODE);
