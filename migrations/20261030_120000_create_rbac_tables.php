<?php
/**
 * RBAC: таблицы ролей, прав и связей многие-ко-многим.
 *
 * Типы колонок: обычный signed INT — чтобы FK user_roles.user_id ссылался на
 * users.id (там int signed, InnoDB). Charset utf8mb3 — как в остальной базе.
 */

return [
    'up' => function (mysqli $db) {
        db_exec($db, "
            CREATE TABLE IF NOT EXISTS roles (
                id         INT          NOT NULL AUTO_INCREMENT,
                name       VARCHAR(64)  NOT NULL,
                label      VARCHAR(128) NOT NULL DEFAULT '',
                created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY uq_roles_name (name)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
        ");

        db_exec($db, "
            CREATE TABLE IF NOT EXISTS permissions (
                id         INT          NOT NULL AUTO_INCREMENT,
                name       VARCHAR(128) NOT NULL,
                label      VARCHAR(191) NOT NULL DEFAULT '',
                created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY uq_permissions_name (name)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
        ");

        db_exec($db, "
            CREATE TABLE IF NOT EXISTS role_permissions (
                role_id       INT NOT NULL,
                permission_id INT NOT NULL,
                PRIMARY KEY (role_id, permission_id),
                KEY idx_rp_permission (permission_id),
                CONSTRAINT fk_rp_role       FOREIGN KEY (role_id)       REFERENCES roles (id)       ON DELETE CASCADE,
                CONSTRAINT fk_rp_permission FOREIGN KEY (permission_id) REFERENCES permissions (id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
        ");

        db_exec($db, "
            CREATE TABLE IF NOT EXISTS user_roles (
                user_id INT NOT NULL,
                role_id INT NOT NULL,
                PRIMARY KEY (user_id, role_id),
                KEY idx_ur_role (role_id),
                CONSTRAINT fk_ur_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
                CONSTRAINT fk_ur_role FOREIGN KEY (role_id) REFERENCES roles (id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
        ");

        // users.is_active — отключение аккаунта без удаления.
        if (!migrate_column_exists($db, 'users', 'is_active')) {
            db_exec($db, "ALTER TABLE users ADD COLUMN is_active TINYINT(1) NOT NULL DEFAULT 1");
        }
    },

    'down' => function (mysqli $db) {
        // Сначала таблицы со ссылками, потом справочники.
        db_exec($db, 'DROP TABLE IF EXISTS user_roles');
        db_exec($db, 'DROP TABLE IF EXISTS role_permissions');
        db_exec($db, 'DROP TABLE IF EXISTS permissions');
        db_exec($db, 'DROP TABLE IF EXISTS roles');

        if (migrate_column_exists($db, 'users', 'is_active')) {
            db_exec($db, 'ALTER TABLE users DROP COLUMN is_active');
        }
    },
];
