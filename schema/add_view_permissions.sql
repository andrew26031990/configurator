-- ============================================================================
--  Инкремент для УЖЕ установленного RBAC (прод без CLI, phpMyAdmin → SQL).
--  Добавляет права просмотра filters.view / sborki.view и выдаёт их всем ролям.
--  Эквивалент миграции 20261030_130000_add_view_permissions. Выполнять ОДИН раз.
--
--  Нужно только если базовый RBAC уже накачен (есть таблицы roles/permissions).
--  Для чистой установки используйте schema/rbac_manual_install.sql — там эти
--  права уже включены.
-- ============================================================================

INSERT INTO permissions (name, label) VALUES
    ('filters.view', 'Просмотр фильтров'),
    ('sborki.view',  'Просмотр сборок');

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
  FROM roles r CROSS JOIN permissions p
 WHERE p.name IN ('filters.view', 'sborki.view')
   AND r.name IN ('superadmin', 'manager', 'editor', 'viewer');

INSERT INTO schema_migrations (version, applied_at)
VALUES ('20261030_130000_add_view_permissions', NOW());
