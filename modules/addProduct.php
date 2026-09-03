<?php
require_once __DIR__ . '/../functions.php';
require_admin();

$uploadDir = APP_ROOT . '/configurator/images/products';

$name        = post_str('name', 200);
$description = post_str('description', 65535);
$price       = (int) preg_replace('/[^0-9]/', '', post_str('price', 30));
$filterId    = post_int('filter');
$treeId      = post_int('tip_tovar_tov');

if ($name === '' || $description === '' || $price <= 0 || $filterId === null || $treeId === null || $treeId <= 0) {
    http_response_code(400);
    exit('Не все поля заполнены');
}

if (!isset($_FILES['picture']) || $_FILES['picture']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    exit('Не выбрана картинка товара');
}

$extension = strtolower((string) pathinfo((string) $_FILES['picture']['name'], PATHINFO_EXTENSION));

// Проверяем не расширение в имени, а реальное содержимое файла.
if (!is_valid_image_upload($_FILES['picture']['tmp_name'], $extension)) {
    http_response_code(400);
    exit('Неверный формат картинки');
}

$fileName = random_image_name($extension);
$fullPath = $uploadDir . '/' . $fileName;

if (!move_uploaded_file($_FILES['picture']['tmp_name'], $fullPath)) {
    http_response_code(500);
    exit('Не удалось сохранить картинку');
}

// Пересобираем картинку, уничтожая возможную нагрузку (GIF-полиглоты и т.п.).
if (!harden_uploaded_image($fullPath, $extension)) {
    delete_upload($uploadDir, $fileName);
    http_response_code(400);
    exit('Не удалось обработать картинку');
}

try {
    $mysqli->begin_transaction();

    db_exec(
        $mysqli,
        'INSERT INTO products (name, description, price, image, f_id) VALUES (?, ?, ?, ?, ?)',
        array($name, $description, $price, $fileName, $filterId)
    );

    $prodId = (int) $mysqli->insert_id;

    db_exec(
        $mysqli,
        'INSERT INTO `tree_prod` (`tree_id`, `prod_id`) VALUES (?, ?)',
        array($treeId, $prodId)
    );

    $mysqli->commit();
    echo 'Товар успешно добавлен в базу, связь добавлена';
} catch (Throwable $e) {
    $mysqli->rollback();
    delete_upload($uploadDir, $fileName);
    error_log('addProduct: ' . $e->getMessage());
    http_response_code(500);
    echo 'Ошибка при добавлении товара';
}
