<?php
require_once __DIR__ . '/../functions.php';
require_admin();
require_capability('sborki.manage');

$uploadDir = APP_ROOT . '/configurator/images/sborki';

$id          = post_int('idSborki');
$title       = post_str('edit_title', 300);
$description = post_str('edit_description', 65535);
$price       = (float) str_replace(array(' ', ','), array('', '.'), post_str('edit_price', 30));
$link        = post_str('edit_link', 65535);
$category    = strtoupper(post_str('edit_category', 200));

if ($id === null || $id <= 0 || $title === '' || $description === '' || $price <= 0 || $link === '') {
    http_response_code(400);
    exit('Не все поля заполнены');
}

$hasNewImage = isset($_FILES['edit_image']) && $_FILES['edit_image']['error'] === UPLOAD_ERR_OK;

// Состояние до правки — для журнала действий.
$before = db_row($mysqli, 'SELECT title, description, price, link, category FROM sborki WHERE id = ?', array($id));
$before = $before ? $before : array();
$after  = array('title' => $title, 'description' => $description, 'price' => $price, 'link' => $link, 'category' => $category);

/**
 * Запись в журнал: что поменялось в сборке.
 */
function auditSborkaUpdate($mysqli, $id, array $before, array $after, $imageChanged)
{
    // price в базе float: «1500000» и 1500000.0 не должны считаться правкой.
    if (isset($before['price'])) {
        $before['price'] = (float) $before['price'];
    }
    $changes = audit_diff($before, $after);
    if ($imageChanged) {
        $changes['image'] = array('было' => '', 'стало' => 'новая картинка');
    }
    audit_log($mysqli, 'sborka.update', "Изменена сборка «{$after['title']}» (id {$id})", array(
        'ids'     => array($id),
        'details' => array('changes' => $changes),
    ));
}

if (!$hasNewImage) {
    try {
        db_exec(
            $mysqli,
            'UPDATE sborki SET title = ?, description = ?, price = ?, link = ?, category = ? WHERE id = ?',
            array($title, $description, $price, $link, $category, $id)
        );
        auditSborkaUpdate($mysqli, $id, $before, $after, false);
        echo 'Данные сборки были успешно изменены';
    } catch (Throwable $e) {
        error_log('editSborka: ' . $e->getMessage());
        http_response_code(500);
        echo 'Ошибка при сохранении сборки';
    }
    return;
}

$extension = strtolower((string) pathinfo((string) $_FILES['edit_image']['name'], PATHINFO_EXTENSION));

if (!is_valid_image_upload($_FILES['edit_image']['tmp_name'], $extension)) {
    http_response_code(400);
    exit('Неверный формат картинки');
}

$fileName = random_image_name($extension);

if (!move_uploaded_file($_FILES['edit_image']['tmp_name'], $uploadDir . '/' . $fileName)) {
    http_response_code(500);
    exit('Не удалось сохранить картинку');
}

if (!harden_uploaded_image($uploadDir . '/' . $fileName, $extension)) {
    delete_upload($uploadDir, $fileName);
    http_response_code(400);
    exit('Не удалось обработать картинку');
}

try {
    // Старое имя картинки берём из базы: поле oldImg из формы попадало
    // напрямую в unlink('..' . $oldImg) и позволяло удалить любой файл.
    $current = db_row($mysqli, 'SELECT image FROM sborki WHERE id = ?', array($id));

    db_exec(
        $mysqli,
        'UPDATE sborki SET title = ?, description = ?, price = ?, image = ?, link = ?, category = ? WHERE id = ?',
        array($title, $description, $price, $fileName, $link, $category, $id)
    );

    if ($current && $current['image'] !== '') {
        delete_upload($uploadDir, $current['image']);
    }

    auditSborkaUpdate($mysqli, $id, $before, $after, true);
    echo 'Сборка была успешно отредактирована';
} catch (Throwable $e) {
    delete_upload($uploadDir, $fileName);
    error_log('editSborka: ' . $e->getMessage());
    http_response_code(500);
    echo 'Ошибка при сохранении сборки';
}
