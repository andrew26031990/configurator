<?php
/**
 * Сортировка товаров внутри блока (тип-ноды дерева) + журнал действий админки.
 *
 * Порядок хранится в связке tree_prod, а не в products: один и тот же товар
 * может лежать в нескольких блоках («Игровые → Видеокарты» и «Офисные →
 * Видеокарты») и стоять там на разных местах.
 *
 * Начальный порядок — как витрина показывала до сих пор (по цене), так что
 * после накатывания на сайте ничего не сдвинется.
 */

return [
    'up' => function (mysqli $db) {
        if (!migrate_column_exists($db, 'tree_prod', 'sort_order')) {
            db_exec($db, "ALTER TABLE tree_prod ADD COLUMN sort_order INT NOT NULL DEFAULT 0");
            db_exec($db, "ALTER TABLE tree_prod ADD KEY idx_tree_prod_sort (tree_id, sort_order)");
        }

        db_exec($db, "
            UPDATE tree_prod tp
              JOIN (
                    SELECT tp2.tree_id, tp2.prod_id,
                           ROW_NUMBER() OVER (PARTITION BY tp2.tree_id ORDER BY p.price, p.id) AS rn
                      FROM tree_prod tp2
                      JOIN products p ON p.id = tp2.prod_id
                   ) x ON x.tree_id = tp.tree_id AND x.prod_id = tp.prod_id
               SET tp.sort_order = x.rn
        ");

        db_exec($db, "
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
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3
        ");
    },

    'down' => function (mysqli $db) {
        db_exec($db, "DROP TABLE IF EXISTS admin_audit_log");

        if (migrate_column_exists($db, 'tree_prod', 'sort_order')) {
            db_exec($db, "ALTER TABLE tree_prod DROP KEY idx_tree_prod_sort");
            db_exec($db, "ALTER TABLE tree_prod DROP COLUMN sort_order");
        }
    },
];
