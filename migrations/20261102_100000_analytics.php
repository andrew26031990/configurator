<?php
/**
 * Статистика посещаемости и воронка заказа.
 *
 * analytics_events — события витрины: просмотр страницы, выбор комплектующего,
 * сборка готова к заказу, открытие формы, отправка, ошибка формы, заявка
 * принята, ссылка/PDF. Посетитель (visitor_id, cookie на год) и сессия
 * (session_id, 30 минут без действий) — случайные идентификаторы, IP не храним.
 *
 * Право stats.view (страница «Статистика») — superadmin и manager.
 */

return [
    'up' => function (mysqli $db) {
        db_exec($db, "
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
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
        ");

        $db->begin_transaction();
        try {
            db_exec($db, "INSERT INTO permissions (name, label) VALUES ('stats.view', 'Просмотр статистики посещаемости')");
            db_exec($db, "
                INSERT INTO role_permissions (role_id, permission_id)
                SELECT r.id, p.id
                  FROM roles r CROSS JOIN permissions p
                 WHERE p.name = 'stats.view' AND r.name IN ('superadmin', 'manager')
            ");
            $db->commit();
        } catch (Throwable $e) {
            $db->rollback();
            throw $e;
        }
    },

    'down' => function (mysqli $db) {
        db_exec($db, "DELETE FROM permissions WHERE name = 'stats.view'");
        db_exec($db, "DROP TABLE IF EXISTS analytics_events");
    },
];
