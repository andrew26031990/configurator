-- ============================================================================
--  Журнал действий: описание действия, индексы, право audit.view (superadmin).
--  (прод без CLI, phpMyAdmin → SQL). Эквивалент миграции
--  20261101_100000_audit_log_page. Выполнять ОДИН раз, ПОСЛЕ
--  schema/block_sort_and_audit_log.sql.
-- ============================================================================

ALTER TABLE admin_audit_log ADD COLUMN summary VARCHAR(500) NOT NULL DEFAULT '' AFTER action;
ALTER TABLE admin_audit_log ADD KEY idx_audit_user_created (user_id, created_at);
ALTER TABLE admin_audit_log ADD KEY idx_audit_created (created_at);

INSERT INTO permissions (name, label) VALUES ('audit.view', 'Просмотр журнала действий');

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
  FROM roles r CROSS JOIN permissions p
 WHERE p.name = 'audit.view' AND r.name = 'superadmin';

INSERT INTO schema_migrations (version, applied_at)
VALUES ('20261101_100000_audit_log_page', NOW());
