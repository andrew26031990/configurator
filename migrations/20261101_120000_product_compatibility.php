<?php
/**
 * Совместимость материнской платы, процессора и ОЗУ.
 *
 *   products.socket   — сокет (плата, процессор): LGA1700, AM5 …
 *   products.mem_type — тип памяти: у ОЗУ один (DDR4), у платы и процессора —
 *                       поддерживаемые через запятую (DDR4,DDR5);
 *   tree.compat_role  — роль блока: motherboard | cpu | ram.
 *
 * Роли проставляются по translit существующих блоков. Сами поля товаров
 * заполняются отдельно: админка «Совместимость» → «Заполнить из названий»
 * или php bin/compat_autofill.php --apply.
 */

return [
    'up' => function (mysqli $db) {
        if (!migrate_column_exists($db, 'products', 'socket')) {
            db_exec($db, "ALTER TABLE products ADD COLUMN socket VARCHAR(32) NOT NULL DEFAULT ''");
        }
        if (!migrate_column_exists($db, 'products', 'mem_type')) {
            db_exec($db, "ALTER TABLE products ADD COLUMN mem_type VARCHAR(64) NOT NULL DEFAULT ''");
        }
        if (!migrate_column_exists($db, 'tree', 'compat_role')) {
            db_exec($db, "ALTER TABLE tree ADD COLUMN compat_role VARCHAR(16) NOT NULL DEFAULT ''");
        }

        db_exec($db, "UPDATE tree SET compat_role = 'motherboard' WHERE level = 3 AND translit = 'materinskaya_plata'");
        db_exec($db, "UPDATE tree SET compat_role = 'cpu'         WHERE level = 3 AND translit = 'protsessor'");
        db_exec($db, "UPDATE tree SET compat_role = 'ram'         WHERE level = 3 AND translit IN ('ozu', 'operativnaya_pamyatj')");
    },

    'down' => function (mysqli $db) {
        foreach (array(array('tree', 'compat_role'), array('products', 'mem_type'), array('products', 'socket')) as $tc) {
            if (migrate_column_exists($db, $tc[0], $tc[1])) {
                db_exec($db, "ALTER TABLE {$tc[0]} DROP COLUMN {$tc[1]}");
            }
        }
    },
];
