<?php
/**
 * Удаление пользователя. Только users.manage.
 *
 * Связи в user_roles удаляются каскадом (FK ON DELETE CASCADE).
 * Запрещено удалять самого себя и оставлять систему без активного
 * пользователя с правом users.manage.
 */
require_once __DIR__ . '/../../functions.php';
require_admin();
require_capability('users.manage');

$id = post_int('id');

if ($id === null || $id <= 0) {
    json_out(array('success' => false, 'error' => 'Некорректный id'));
    return;
}

$me = current_user();
if ($me && (int) $me['id'] === $id) {
    json_out(array('success' => false, 'error' => 'Нельзя удалить собственную учётную запись'));
    return;
}

if (!getUserById($mysqli, $id)) {
    json_out(array('success' => false, 'error' => 'Пользователь не найден'));
    return;
}

$mysqli->begin_transaction();

try {
    db_exec($mysqli, 'DELETE FROM users WHERE id = ?', array($id));

    if (countActiveManagers($mysqli) < 1) {
        $mysqli->rollback();
        json_out(array(
            'success' => false,
            'error'   => 'Нельзя: это последний активный пользователь с правом управления пользователями.',
        ));
        return;
    }

    $mysqli->commit();
    json_out(array('success' => true));
} catch (Throwable $e) {
    $mysqli->rollback();
    error_log('deleteUser: ' . $e->getMessage());
    http_response_code(500);
    json_out(array('success' => false, 'error' => 'Ошибка удаления пользователя'));
}
