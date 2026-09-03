<?php
require_once __DIR__ . '/../functions.php';
require_admin();

$nodes = array();

foreach (db_rows($mysqli, 'SELECT id, parent_id, name FROM `tree`') as $row) {
    $nodes[] = array(
        'id'     => $row['id'],
        // jsTree ждёт '#' в качестве родителя для корня.
        'parent' => ((int) $row['parent_id'] === 0) ? '#' : $row['parent_id'],
        'text'   => $row['name'],
        'icon'   => '../images/red-icon.png',
    );
}

json_out($nodes);
