<?php
/**
 * Журнал действий в админке: человекочитаемое описание действия, индексы под
 * фильтры страницы «Журнал действий» и право audit.view (только superadmin).
 */

return [
    'up' => function (mysqli $db) {
        if (!migrate_column_exists($db, 'admin_audit_log', 'summary')) {
            db_exec($db, "ALTER TABLE admin_audit_log ADD COLUMN summary VARCHAR(500) NOT NULL DEFAULT '' AFTER action");
            db_exec($db, "ALTER TABLE admin_audit_log ADD KEY idx_audit_user_created (user_id, created_at)");
            db_exec($db, "ALTER TABLE admin_audit_log ADD KEY idx_audit_created (created_at)");
        }

        $db->begin_transaction();
        try {
            db_exec($db, "INSERT INTO permissions (name, label) VALUES ('audit.view', 'Просмотр журнала действий')");
            db_exec($db, "
                INSERT INTO role_permissions (role_id, permission_id)
                SELECT r.id, p.id
                  FROM roles r CROSS JOIN permissions p
                 WHERE p.name = 'audit.view' AND r.name = 'superadmin'
            ");
            $db->commit();
        } catch (Throwable $e) {
            $db->rollback();
            throw $e;
        }
    },

    'down' => function (mysqli $db) {
        // role_permissions подчистит каскад при удалении permissions.
        db_exec($db, "DELETE FROM permissions WHERE name = 'audit.view'");

        if (migrate_column_exists($db, 'admin_audit_log', 'summary')) {
            db_exec($db, "ALTER TABLE admin_audit_log DROP KEY idx_audit_created");
            db_exec($db, "ALTER TABLE admin_audit_log DROP KEY idx_audit_user_created");
            db_exec($db, "ALTER TABLE admin_audit_log DROP COLUMN summary");
        }
    },
];
