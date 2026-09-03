<?php
require_once __DIR__ . '/../functions.php';
require_admin();

$uploadDir = APP_ROOT . '/configurator/images/sborki';

// category_id = 97 — узел "Готовые сборки" в таблице tree.
const SBORKI_TREE_ID = 97;

$title       = post_str('name', 300);
$description = post_str('description', 65535);
$price       = (float) str_replace(array(' ', ','), array('', '.'), post_str('price', 30));
$link        = post_str('link', 65535);
$category    = strtoupper(post_str('category', 200));

if ($title === '' || $description === '' || $price <= 0 || $link === '') {
    http_response_code(400);
    exit('Не все поля заполнены');
}

if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    exit('Не выбрана картинка сборки');
}

$extension = strtolower((string) pathinfo((string) $_FILES['image']['name'], PATHINFO_EXTENSION));

if (!is_valid_image_upload($_FILES['image']['tmp_name'], $extension)) {
    http_response_code(400);
    exit('Неверный формат картинки');
}

$fileName = random_image_name($extension);

if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . '/' . $fileName)) {
    http_response_code(500);
    exit('Не удалось сохранить картинку');
}

if (!harden_uploaded_image($uploadDir . '/' . $fileName, $extension)) {
    delete_upload($uploadDir, $fileName);
    http_response_code(400);
    exit('Не удалось обработать картинку');
}

try {
    db_exec(
        $mysqli,
        'INSERT INTO sborki (title, description, price, image, link, category, category_id)
         VALUES (?, ?, ?, ?, ?, ?, ?)',
        array($title, $description, $price, $fileName, $link, $category, SBORKI_TREE_ID)
    );
    echo 'Сборка успешно добавлена в базу';
} catch (Throwable $e) {
    delete_upload($uploadDir, $fileName);
    error_log('addSborka: ' . $e->getMessage());
    http_response_code(500);
    echo 'Ошибка при добавлении сборки';
}
