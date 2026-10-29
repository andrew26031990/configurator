<?php
require_once __DIR__ . '/../functions.php';
require_admin();

$nodes = array();

// Иконки по уровню: level=3 — тип товара, отличается от сборок/групп.
$levelIcons = array(
    0 => 'fas fa-home text-secondary',
    1 => 'fas fa-desktop text-primary',
    2 => 'fas fa-folder text-warning',
    3 => 'fas fa-microchip text-info',
);

foreach (db_rows($mysqli, 'SELECT id, parent_id, name, level FROM `tree`') as $row) {
    $lvl = (int) $row['level'];

    $nodes[] = array(
        'id'     => $row['id'],
        // jsTree ждёт '#' в качестве родителя для корня.
        'parent' => ((int) $row['parent_id'] === 0) ? '#' : $row['parent_id'],
        'text'   => $row['name'],
        'icon'   => isset($levelIcons[$lvl]) ? $levelIcons[$lvl] : 'fas fa-circle text-muted',
    );
}

json_out($nodes);
