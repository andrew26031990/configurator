<?php
require_once __DIR__ . '/../../functions.php';
require_admin();

$treeId = post_int('tree_id');

if ($treeId === null || $treeId <= 0) {
    json_out(array('count' => 0, 'products' => array()));
    return;
}

// Нода + все её потомки (рекурсивно), затем товары, привязанные к любой из них.
$products = db_rows(
    $mysqli,
    'WITH RECURSIVE sub AS (
         SELECT id FROM tree WHERE id = ?
         UNION ALL
         SELECT t.id FROM tree t JOIN sub ON t.parent_id = sub.id
     )
     SELECT DISTINCT p.id, p.name, p.price, p.image, f.f_name
       FROM products p
       JOIN tree_prod tp ON tp.prod_id = p.id
       LEFT JOIN filters f ON f.id = p.f_id
      WHERE tp.tree_id IN (SELECT id FROM sub)
      ORDER BY p.name',
    array($treeId)
);

json_out(array('count' => count($products), 'products' => $products));
