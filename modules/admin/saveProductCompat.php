<?php
/**
 * Сокет и тип памяти товара (страница «Совместимость»).
 *
 * POST prod_id, socket, mem_type[] (DDR4/DDR5…).
 */

require_once __DIR__ . '/../../functions.php';
require_admin();
require_capability('products.edit');

$id     = post_int('prod_id');
$socket = strtoupper(preg_replace('/\s+/', '', post_str('socket', 32)));
$mem    = compat_normalize_mem(isset($_POST['mem_type']) && is_array($_POST['mem_type']) ? $_POST['mem_type'] : array());

if ($id === null || $id <= 0) {
    http_response_code(400);
    json_out(array('success' => false, 'error' => 'Некорректный товар'));
    return;
}
if ($socket !== '' && !preg_match('/^[A-Z0-9-]{2,16}$/', $socket)) {
    http_response_code(400);
    json_out(array('success' => false, 'error' => 'Некорректный сокет'));
    return;
}

$before = db_row($mysqli, 'SELECT name, socket, mem_type FROM products WHERE id = ?', array($id));
if (!$before) {
    http_response_code(404);
    json_out(array('success' => false, 'error' => 'Товар не найден'));
    return;
}

try {
    db_exec($mysqli, 'UPDATE products SET socket = ?, mem_type = ? WHERE id = ?', array($socket, $mem, $id));
} catch (Throwable $e) {
    error_log('saveProductCompat: ' . $e->getMessage());
    http_response_code(500);
    json_out(array('success' => false, 'error' => 'Ошибка сохранения'));
    return;
}

$changes = audit_diff(
    array('socket' => $before['socket'], 'mem_type' => $before['mem_type']),
    array('socket' => $socket, 'mem_type' => $mem)
);
if ($changes) {
    audit_log($mysqli, 'product.compat', "Совместимость товара «{$before['name']}» (id {$id})", array(
        'ids'     => array($id),
        'details' => array('changes' => $changes),
    ));
}

json_out(array('success' => true, 'socket' => $socket, 'mem_type' => $mem));
