<?php
/**
 * Baseline — вся легаси-схема проекта.
 *
 * up() исполняет schema/baseline.sql (снимок `mysqldump --no-data` с заменой
 * CREATE TABLE -> CREATE TABLE IF NOT EXISTS). На уже существующей базе все
 * CREATE являются no-op, миграция просто помечается применённой. На чистой базе
 * — создаёт всю структуру.
 *
 * ВАЖНО: baseline НЕ сверяет структуру уже существующих таблиц (IF NOT EXISTS
 * пропускает их целиком). Любое изменение легаси-таблицы — отдельная forward-
 * миграция с ALTER, baseline задним числом не редактируем.
 *
 * Откат запрещён: down() снёс бы всю схему проекта.
 */

return [
    'up' => function (mysqli $db) {
        // migrate_run_sql_file объявлена в bin/migrate.php (миграции запускаются
        // только через раннер).
        migrate_run_sql_file($db, __DIR__ . '/../schema/baseline.sql');
    },
    'down' => function (mysqli $db) {
        throw new RuntimeException(
            'baseline откатывать нельзя — это снесло бы всю легаси-схему проекта.'
        );
    },
];
