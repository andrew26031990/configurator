-- ============================================================================
--  Сортировка товаров внутри блока + журнал действий админки
--  (прод без CLI, phpMyAdmin → SQL). Эквивалент миграции
--  20261031_120000_block_sort_and_audit_log. Выполнять ОДИН раз.
--
--  Начальный порядок в каждом блоке — по цене, как витрина показывала раньше.
-- ============================================================================

ALTER TABLE tree_prod ADD COLUMN sort_order INT NOT NULL DEFAULT 0;
ALTER TABLE tree_prod ADD KEY idx_tree_prod_sort (tree_id, sort_order);

UPDATE tree_prod tp
  JOIN (
        SELECT tp2.tree_id, tp2.prod_id,
               ROW_NUMBER() OVER (PARTITION BY tp2.tree_id ORDER BY p.price, p.id) AS rn
          FROM tree_prod tp2
          JOIN products p ON p.id = tp2.prod_id
       ) x ON x.tree_id = tp.tree_id AND x.prod_id = tp.prod_id
   SET tp.sort_order = x.rn;

CREATE TABLE IF NOT EXISTS admin_audit_log (
    id         INT          NOT NULL AUTO_INCREMENT,
    user_id    INT          NULL,
    username   VARCHAR(191) NOT NULL DEFAULT '',
    action     VARCHAR(64)  NOT NULL,
    tree_id    INT          NULL,
    item_ids   MEDIUMTEXT   NOT NULL,
    details    MEDIUMTEXT   NULL,
    ip         VARCHAR(45)  NOT NULL DEFAULT '',
    created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_audit_action_created (action, created_at),
    KEY idx_audit_tree (tree_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

INSERT INTO schema_migrations (version, applied_at)
VALUES ('20261031_120000_block_sort_and_audit_log', NOW());
