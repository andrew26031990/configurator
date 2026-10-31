<?php
/**
 * «Заполнить из названий»: сокет и тип памяти для товаров с пустыми полями.
 * Ручные значения не перетираются.
 */

require_once __DIR__ . '/../../functions.php';
require_admin();
require_capability('products.edit');

try {
    $result = compat_autofill($mysqli, true);
} catch (Throwable $e) {
    error_log('compatAutofill: ' . $e->getMessage());
    http_response_code(500);
    json_out(array('success' => false, 'error' => 'Ошибка автозаполнения'));
    return;
}

$ids = array();
foreach ($result['filled'] as $f) {
    $ids[] = $f['id'];
}
if ($ids) {
    audit_log($mysqli, 'compat.autofill', 'Автозаполнение совместимости из названий: заполнено ' . count($ids), array(
        'ids'     => $ids,
        'details' => array('unresolved' => count($result['unresolved'])),
    ));
}

json_out(array('success' => true, 'filled' => count($ids), 'unresolved' => count($result['unresolved'])));
