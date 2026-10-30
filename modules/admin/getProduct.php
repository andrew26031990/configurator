<?php
require_once __DIR__ . '/../../functions.php';
require_admin();
require_capability('products.view');

$id = post_int('prod_id');

if ($id === null || $id <= 0) {
    json_out(null);
    return;
}

$prod = db_row(
    $mysqli,
    'SELECT id, name, price, description, image, f_id FROM products WHERE id = ?',
    array($id)
);

if ($prod) {
    $prod['trees'] = getProductTreeIds($mysqli, $id);
}

json_out($prod);
