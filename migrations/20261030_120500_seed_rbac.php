<?php
/**
 * Сид RBAC: роли, каталог прав, маппинг роль→права и выдача роли superadmin
 * всем существующим пользователям (чтобы никого не залочить, когда появятся
 * серверные гейты; понизить роль можно будет через UI управления пользователями).
 *
 * Это чистый DML — оборачиваем в транзакцию.
 */

return [
    'up' => function (mysqli $db) {
        $db->begin_transaction();
        try {
            // --- Роли -------------------------------------------------------
            $roles = array(
                array('superadmin', 'Суперадмин'),
                array('manager',    'Менеджер'),
                array('editor',     'Редактор'),
                array('viewer',     'Наблюдатель'),
            );
            foreach ($roles as $role) {
                db_exec($db, 'INSERT INTO roles (name, label) VALUES (?, ?)', $role);
            }

            // --- Каталог прав ----------------------------------------------
            $permissions = array(
                array('dashboard.view',     'Просмотр дашборда'),
                array('products.view',      'Просмотр товаров'),
                array('products.edit',      'Создание и редактирование товаров'),
                array('products.delete',    'Удаление товаров'),
                array('categories.manage',  'Управление деревом категорий'),
                array('filters.manage',     'Управление фильтрами'),
                array('sborki.manage',      'Управление сборками'),
                array('users.manage',       'Управление пользователями'),
            );
            foreach ($permissions as $permission) {
                db_exec($db, 'INSERT INTO permissions (name, label) VALUES (?, ?)', $permission);
            }

            // --- Маппинг роль → права --------------------------------------
            // superadmin — все права.
            db_exec($db, "
                INSERT INTO role_permissions (role_id, permission_id)
                SELECT r.id, p.id
                  FROM roles r CROSS JOIN permissions p
                 WHERE r.name = 'superadmin'
            ");

            // manager — всё, кроме управления пользователями.
            db_exec($db, "
                INSERT INTO role_permissions (role_id, permission_id)
                SELECT r.id, p.id
                  FROM roles r CROSS JOIN permissions p
                 WHERE r.name = 'manager'
                   AND p.name IN (
                       'dashboard.view','products.view','products.edit','products.delete',
                       'categories.manage','filters.manage','sborki.manage'
                   )
            ");

            // editor — правка товаров и фильтров, без удаления и без категорий.
            db_exec($db, "
                INSERT INTO role_permissions (role_id, permission_id)
                SELECT r.id, p.id
                  FROM roles r CROSS JOIN permissions p
                 WHERE r.name = 'editor'
                   AND p.name IN ('dashboard.view','products.view','products.edit','filters.manage')
            ");

            // viewer — только чтение.
            db_exec($db, "
                INSERT INTO role_permissions (role_id, permission_id)
                SELECT r.id, p.id
                  FROM roles r CROSS JOIN permissions p
                 WHERE r.name = 'viewer'
                   AND p.name IN ('dashboard.view','products.view')
            ");

            // --- Существующие пользователи → superadmin --------------------
            db_exec($db, "
                INSERT INTO user_roles (user_id, role_id)
                SELECT u.id, r.id
                  FROM users u CROSS JOIN roles r
                 WHERE r.name = 'superadmin'
            ");

            $db->commit();
        } catch (Throwable $e) {
            $db->rollback();
            throw $e;
        }
    },

    'down' => function (mysqli $db) {
        $db->begin_transaction();
        try {
            // FK ON DELETE CASCADE подчистит связи, но чистим явно и в порядке.
            db_exec($db, 'DELETE FROM user_roles');
            db_exec($db, 'DELETE FROM role_permissions');
            db_exec($db, 'DELETE FROM permissions');
            db_exec($db, 'DELETE FROM roles');
            $db->commit();
        } catch (Throwable $e) {
            $db->rollback();
            throw $e;
        }
    },
];
