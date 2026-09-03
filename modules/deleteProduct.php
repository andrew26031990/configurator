<?php
require_once __DIR__ . '/../functions.php';
require_admin();

// admin.js присылает строку вида "<id>:<имя файла картинки>".
$pieces = explode(':', post_str('prod_id', 300), 2);
$id     = filter_var($pieces[0], FILTER_VALIDATE_INT);
$image  = isset($pieces[1]) ? $pieces[1] : '';

if ($id === false || $id <= 0) {
    http_response_code(400);
    exit('Некорректный идентификатор товара');
}

try {
    db_exec($mysqli, 'DELETE FROM products WHERE id = ?', array($id));
    // delete_upload не выпустит за пределы каталога картинок:
    // раньше сюда можно было передать "../../functions.php".
    delete_upload(APP_ROOT . '/configurator/images/products', $image);
    echo 'Товар успешно удален';
} catch (Throwable $e) {
    error_log('deleteProduct: ' . $e->getMessage());
    http_response_code(500);
    echo 'Ошибка при удалении товара';
}
