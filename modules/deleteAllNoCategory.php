<?php
require_once __DIR__ . '/../functions.php';
require_admin();
require_capability('products.delete');

$uploadDir = APP_ROOT . '/configurator/images/products';

// Защита: клиент должен прислать подтверждающую фразу "удалить все".
$confirm = post_str('confirm', 50);
$norm    = function_exists('mb_strtolower') ? mb_strtolower($confirm, 'UTF-8') : strtolower($confirm);

if ($norm !== 'удалить все') {
    json_out(array('success' => false, 'error' => 'Подтверждение не совпадает'));
    return;
}

$rows    = getProductsWithoutCategory($mysqli);
$deleted = 0;

foreach ($rows as $row) {
    $id = (int) $row['id'];

    if ($id <= 0) {
        continue;
    }

    try {
        db_exec($mysqli, 'DELETE FROM products WHERE id = ?', array($id));
        db_exec($mysqli, 'DELETE FROM tree_prod WHERE prod_id = ?', array($id));

        if (!empty($row['image'])) {
            delete_upload($uploadDir, $row['image']);
        }

        $deleted++;
    } catch (Throwable $e) {
        error_log('deleteAllNoCategory: ' . $e->getMessage());
    }
}

json_out(array('success' => true, 'deleted' => $deleted));
