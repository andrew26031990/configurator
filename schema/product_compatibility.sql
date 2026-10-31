-- ============================================================================
--  Совместимость материнской платы, процессора и ОЗУ
--  (прод без CLI, phpMyAdmin → SQL). Эквивалент миграции
--  20261101_120000_product_compatibility. Выполнять ОДИН раз.
--
--  После выполнения: админка → «Совместимость» → «Заполнить из названий».
-- ============================================================================

ALTER TABLE products ADD COLUMN socket   VARCHAR(32) NOT NULL DEFAULT '';
ALTER TABLE products ADD COLUMN mem_type VARCHAR(64) NOT NULL DEFAULT '';
ALTER TABLE tree     ADD COLUMN compat_role VARCHAR(16) NOT NULL DEFAULT '';

UPDATE tree SET compat_role = 'motherboard' WHERE level = 3 AND translit = 'materinskaya_plata';
UPDATE tree SET compat_role = 'cpu'         WHERE level = 3 AND translit = 'protsessor';
UPDATE tree SET compat_role = 'ram'         WHERE level = 3 AND translit IN ('ozu', 'operativnaya_pamyatj');

INSERT INTO schema_migrations (version, applied_at)
VALUES ('20261101_120000_product_compatibility', NOW());
