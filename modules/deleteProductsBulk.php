<?php
require_once __DIR__ . '/../functions.php';
require_admin();
require_capability('products.delete');

$uploadDir = APP_ROOT . '/configurator/images/products';

// admin.js присылает items[] — каждый элемент "<id>:<имя файла картинки>".
$items   = isset($_POST['items']) && is_array($_POST['items']) ? $_POST['items'] : array();
$deleted = 0;

foreach ($items as $item) {
    if (!is_scalar($item)) {
        continue;
    }

    $pieces = explode(':', (string) $item, 2);
    $id     = filter_var($pieces[0], FILTER_VALIDATE_INT);
    $image  = isset($pieces[1]) ? $pieces[1] : '';

    if ($id === false || $id <= 0) {
        continue;
    }

    try {
        db_exec($mysqli, 'DELETE FROM products WHERE id = ?', array($id));
        db_exec($mysqli, 'DELETE FROM tree_prod WHERE prod_id = ?', array($id));

        if ($image !== '') {
            delete_upload($uploadDir, $image);
        }

        $deleted++;
    } catch (Throwable $e) {
        error_log('deleteProductsBulk: ' . $e->getMessage());
    }
}

json_out(array('deleted' => $deleted));
