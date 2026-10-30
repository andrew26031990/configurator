<?php
/**
 * Создание или обновление пользователя + синхронизация ролей. Только users.manage.
 *
 * Поля POST:
 *   id        — 0/пусто = создать, иначе обновить
 *   username  — логин
 *   password  — при создании обязателен; при редактировании пусто = не менять
 *   is_active — 1/0
 *   roles[]   — id ролей (чекбоксы)
 *
 * Защита от самоблокировки: изменение применяется в транзакции, после чего
 * проверяется инвариант «в системе остаётся хотя бы один активный пользователь
 * с правом users.manage». Если нарушён — откат.
 */
require_once __DIR__ . '/../../functions.php';
require_admin();
require_capability('users.manage');

$id       = post_int('id');
$id       = ($id === null || $id < 0) ? 0 : $id;
$username = trim(post_str('username', 100));
$password = isset($_POST['password']) && is_scalar($_POST['password']) ? (string) $_POST['password'] : '';
$isActive = post_int('is_active') === 1 ? 1 : 0;

$roles = array();
if (isset($_POST['roles']) && is_array($_POST['roles'])) {
    foreach ($_POST['roles'] as $roleId) {
        $roleId = (int) $roleId;
        if ($roleId > 0) {
            $roles[$roleId] = $roleId;
        }
    }
}

if ($username === '') {
    json_out(array('success' => false, 'error' => 'Укажите логин'));
    return;
}

if (usernameExists($mysqli, $username, $id)) {
    json_out(array('success' => false, 'error' => 'Такой логин уже занят'));
    return;
}

if ($id === 0 && $password === '') {
    json_out(array('success' => false, 'error' => 'Для нового пользователя нужен пароль'));
    return;
}

if ($id > 0 && !getUserById($mysqli, $id)) {
    json_out(array('success' => false, 'error' => 'Пользователь не найден'));
    return;
}

$mysqli->begin_transaction();

try {
    if ($id === 0) {
        db_exec(
            $mysqli,
            'INSERT INTO users (username, password, last_login, is_active) VALUES (?, ?, ?, ?)',
            array($username, password_hash($password, PASSWORD_DEFAULT), '', $isActive)
        );
        $id = (int) $mysqli->insert_id;
    } else {
        db_exec(
            $mysqli,
            'UPDATE users SET username = ?, is_active = ? WHERE id = ?',
            array($username, $isActive, $id)
        );
        if ($password !== '') {
            db_exec(
                $mysqli,
                'UPDATE users SET password = ? WHERE id = ?',
                array(password_hash($password, PASSWORD_DEFAULT), $id)
            );
        }
    }

    // Пересобираем роли: сначала чистим, потом вставляем выбранные.
    db_exec($mysqli, 'DELETE FROM user_roles WHERE user_id = ?', array($id));
    foreach ($roles as $roleId) {
        db_exec(
            $mysqli,
            'INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)',
            array($id, $roleId)
        );
    }

    // Инвариант: не остаться без единого активного администратора.
    if (countActiveManagers($mysqli) < 1) {
        $mysqli->rollback();
        json_out(array(
            'success' => false,
            'error'   => 'Нельзя: в системе не останется ни одного активного пользователя с правом управления пользователями.',
        ));
        return;
    }

    $mysqli->commit();
    json_out(array('success' => true, 'id' => $id));
} catch (Throwable $e) {
    $mysqli->rollback();
    error_log('saveUser: ' . $e->getMessage());
    http_response_code(500);
    json_out(array('success' => false, 'error' => 'Ошибка сохранения пользователя'));
}
