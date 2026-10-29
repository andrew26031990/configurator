<?php

include '../functions.php';
header('Content-Type: application/json; charset=UTF-8');

// Защита: клиент должен прислать подтверждающую фразу "удалить все"
$confirm = isset($_POST['confirm']) ? trim($_POST['confirm']) : '';
$norm = function_exists('mb_strtolower') ? mb_strtolower($confirm, 'UTF-8') : strtolower($confirm);

if ($norm !== 'удалить все') {
    echo json_encode(array('success' => false, 'error' => 'Подтверждение не совпадает'));
    exit;
}

// Товары без категории: нет ни одной связи с существующей нодой дерева
$res = $mysqli->query("SELECT p.id, p.image FROM products p
    WHERE NOT EXISTS (
        SELECT 1 FROM tree_prod tp
        JOIN tree t ON t.id = tp.tree_id
        WHERE tp.prod_id = p.id
    )");

$rows = array();
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $rows[] = $row;
    }
}

$deleted = 0;
foreach ($rows as $row) {
    $id = (int)$row['id'];
    if ($id <= 0) { continue; }
    if ($mysqli->query("DELETE FROM products WHERE id = $id")) {
        $mysqli->query("DELETE FROM tree_prod WHERE prod_id = $id");
        $img = isset($row['image']) ? $row['image'] : '';
        if ($img !== '') {
            $mask = "../configurator/images/products/" . basename($img);
            if (file_exists($mask)) { @unlink($mask); }
        }
        $deleted++;
    }
}

echo json_encode(array('success' => true, 'deleted' => $deleted));
