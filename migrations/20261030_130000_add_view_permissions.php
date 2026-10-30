<?php
/**
 * Права ПРОСМОТРА для фильтров и сборок (filters.view, sborki.view) + выдача их
 * всем ролям. Управление (filters.manage / sborki.manage) остаётся
 * разграниченным — так viewer видит списки, но не редактирует их.
 */

return [
    'up' => function (mysqli $db) {
        $db->begin_transaction();
        try {
            db_exec($db, "INSERT INTO permissions (name, label) VALUES ('filters.view', 'Просмотр фильтров')");
            db_exec($db, "INSERT INTO permissions (name, label) VALUES ('sborki.view', 'Просмотр сборок')");

            db_exec($db, "
                INSERT INTO role_permissions (role_id, permission_id)
                SELECT r.id, p.id
                  FROM roles r CROSS JOIN permissions p
                 WHERE p.name IN ('filters.view', 'sborki.view')
                   AND r.name IN ('superadmin', 'manager', 'editor', 'viewer')
            ");

            $db->commit();
        } catch (Throwable $e) {
            $db->rollback();
            throw $e;
        }
    },

    'down' => function (mysqli $db) {
        // role_permissions подчистит каскад при удалении permissions.
        db_exec($db, "DELETE FROM permissions WHERE name IN ('filters.view', 'sborki.view')");
    },
];
