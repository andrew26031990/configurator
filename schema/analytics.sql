-- ============================================================================
--  Статистика посещаемости и воронка заказа (прод без CLI, phpMyAdmin → SQL).
--  Эквивалент миграции 20261102_100000_analytics. Выполнять ОДИН раз,
--  после schema/product_compatibility.sql.
-- ============================================================================

CREATE TABLE IF NOT EXISTS analytics_events (
    id         BIGINT       NOT NULL AUTO_INCREMENT,
    created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    day        DATE         NOT NULL,
    visitor_id CHAR(16)     NOT NULL,
    session_id CHAR(16)     NOT NULL,
    event      VARCHAR(32)  NOT NULL,
    cat        VARCHAR(64)  NOT NULL DEFAULT '',
    detail     VARCHAR(100) NOT NULL DEFAULT '',
    device     VARCHAR(8)   NOT NULL DEFAULT '',
    is_new     TINYINT(1)   NOT NULL DEFAULT 0,
    referrer   VARCHAR(64)  NOT NULL DEFAULT '',
    PRIMARY KEY (id),
    KEY idx_ae_day_event (day, event),
    KEY idx_ae_session (session_id, event),
    KEY idx_ae_event_day (event, day)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

INSERT INTO permissions (name, label) VALUES ('stats.view', 'Просмотр статистики посещаемости');

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
  FROM roles r CROSS JOIN permissions p
 WHERE p.name = 'stats.view' AND r.name IN ('superadmin', 'manager');

INSERT INTO schema_migrations (version, applied_at)
VALUES ('20261102_100000_analytics', NOW());
