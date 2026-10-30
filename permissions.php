<?php
/**
 * RBAC — проверка прав текущего пользователя.
 *
 * Схема: users → user_roles → role_permissions → permissions.
 * Роль пользователя и его права перечитываются из БД на каждый запрос
 * (по user_id из сессии) — тогда смена роли или деактивация аккаунта
 * срабатывают сразу, без «протухшей» сессии. В пределах одного запроса
 * результат кэшируется в статических переменных.
 *
 * Слои применения:
 *   - require_admin()         — пользователь залогинен (см. security.php);
 *   - require_capability($c)  — у пользователя есть право $c (этот файл);
 *   - user_can($c)            — то же, но без прерывания (для UI и гейтов страниц).
 */

/**
 * Текущий пользователь из БД по user_id в сессии, либо null.
 * Возвращает null, если сессии нет, пользователь удалён или деактивирован.
 *
 * @return array<string,mixed>|null
 */
function current_user()
{
    static $user   = null;
    static $loaded = false;

    if ($loaded) {
        return $user;
    }
    $loaded = true;

    secure_session_start();

    $uid = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;
    if ($uid <= 0) {
        $user = null;
        return null;
    }

    $row = db_row(
        db(),
        'SELECT id, username, is_active FROM users WHERE id = ? LIMIT 1',
        array($uid)
    );

    if (!$row || (int) $row['is_active'] !== 1) {
        $user = null;
        return null;
    }

    $user = $row;
    return $user;
}

/**
 * Эффективные права текущего пользователя: ['products.edit' => true, ...].
 *
 * @return array<string,bool>
 */
function current_user_permissions()
{
    static $perms = null;

    if ($perms !== null) {
        return $perms;
    }

    $user = current_user();
    if (!$user) {
        $perms = array();
        return $perms;
    }

    $rows = db_rows(
        db(),
        'SELECT DISTINCT p.name
           FROM user_roles ur
           JOIN role_permissions rp ON rp.role_id = ur.role_id
           JOIN permissions p       ON p.id = rp.permission_id
          WHERE ur.user_id = ?',
        array((int) $user['id'])
    );

    $perms = array();
    foreach ($rows as $row) {
        $perms[(string) $row['name']] = true;
    }

    return $perms;
}

/**
 * Есть ли у текущего пользователя право.
 *
 * @param  string $capability например 'products.delete'
 * @return bool
 */
function user_can($capability)
{
    $perms = current_user_permissions();
    return isset($perms[(string) $capability]);
}

/**
 * Гард права для административных эндпоинтов. Вызывать ПОСЛЕ require_admin():
 * require_admin() проверяет, что пользователь залогинен, require_capability() —
 * что у него есть конкретное право. Fail-closed: нет права → 403.
 *
 * @param  string $capability
 * @return void
 */
function require_capability($capability)
{
    if (user_can($capability)) {
        return;
    }

    http_response_code(403);
    header('Content-Type: text/plain; charset=UTF-8');
    exit('Недостаточно прав для этого действия');
}
