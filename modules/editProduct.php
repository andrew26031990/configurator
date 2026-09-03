<?php
require_once __DIR__ . '/../functions.php';
require_admin();

// В старом коде здесь было '../images/products/' без configurator/,
// из-за чего замена картинки товара молча не работала.
$uploadDir = APP_ROOT . '/configurator/images/products';

$id          = post_int('idTovara');
$name        = post_str('name', 200);
$description = post_str('description', 65535);
$price       = (int) preg_replace('/[^0-9]/', '', post_str('price', 30));
$filterId    = post_int('filter');

if ($id === null || $id <= 0 || $name === '' || $description === '' || $price <= 0 || $filterId === null) {
    http_response_code(400);
    exit('Не все поля заполнены');
}

$hasNewImage = isset($_FILES['picture']) && $_FILES['picture']['error'] === UPLOAD_ERR_OK;

if (!$hasNewImage) {
    try {
        db_exec(
            $mysqli,
            'UPDATE products SET name = ?, description = ?, price = ?, f_id = ? WHERE id = ?',
            array($name, $description, $price, $filterId, $id)
        );
        echo 'Данные товара были успешно изменены';
    } catch (Throwable $e) {
        error_log('editProduct: ' . $e->getMessage());
        http_response_code(500);
        echo 'Ошибка при сохранении товара';
    }
    return;
}

$extension = strtolower((string) pathinfo((string) $_FILES['picture']['name'], PATHINFO_EXTENSION));

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

if (!harden_uploaded_image($fullPath, $extension)) {
    delete_upload($uploadDir, $fileName);
    http_response_code(400);
    exit('Не удалось обработать картинку');
}

try {
    // Старое имя берём из базы, а не из формы: значение из POST раньше
    // подставлялось прямо в unlink('../' . $oldImg).
    $current = db_row($mysqli, 'SELECT image FROM products WHERE id = ?', array($id));

    db_exec(
        $mysqli,
        'UPDATE products SET name = ?, description = ?, price = ?, image = ?, f_id = ? WHERE id = ?',
        array($name, $description, $price, $fileName, $filterId, $id)
    );

    if ($current && $current['image'] !== '') {
        delete_upload($uploadDir, $current['image']);
    }

    echo 'Товар был успешно отредактирован';
} catch (Throwable $e) {
    delete_upload($uploadDir, $fileName);
    error_log('editProduct: ' . $e->getMessage());
    http_response_code(500);
    echo 'Ошибка при сохранении товара';
}
