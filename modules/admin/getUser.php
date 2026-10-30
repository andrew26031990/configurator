<?php
/**
 * Данные пользователя для формы редактирования. Только users.manage.
 */
require_once __DIR__ . '/../../functions.php';
require_admin();
require_capability('users.manage');

$id = post_int('id');

if ($id === null || $id <= 0) {
    json_out(array('success' => false, 'error' => 'Некорректный id'));
    return;
}

$user = getUserById($mysqli, $id);

if (!$user) {
    json_out(array('success' => false, 'error' => 'Пользователь не найден'));
    return;
}

json_out(array(
    'success'   => true,
    'id'        => (int) $user['id'],
    'username'  => $user['username'],
    'is_active' => (int) $user['is_active'],
    'roles'     => getUserRoleIds($mysqli, (int) $user['id']),
));
