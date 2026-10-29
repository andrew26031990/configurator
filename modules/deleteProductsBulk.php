<?php

include '../functions.php';
header('Content-Type: application/json; charset=UTF-8');

$items = isset($_POST['items']) ? (array)$_POST['items'] : array();
$deleted = 0;

foreach ($items as $it) {
    $parts = explode(':', $it, 2);
    $id = (int)$parts[0];
    $img = isset($parts[1]) ? $parts[1] : '';
    if ($id <= 0) { continue; }

    if ($mysqli->query("DELETE FROM products WHERE id = $id")) {
        $mysqli->query("DELETE FROM tree_prod WHERE prod_id = $id");
        if ($img !== '') {
            $mask = "../configurator/images/products/" . basename($img);
            if (file_exists($mask)) { @unlink($mask); }
        }
        $deleted++;
    }
}

echo json_encode(array('deleted' => $deleted));
