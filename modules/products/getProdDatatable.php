<?php
require_once __DIR__ . '/../../functions.php';
require_admin();

$rows = db_rows($mysqli, 'SELECT id, name, price FROM `products`');
$data = array();

foreach ($rows as $row) {
    $id = (int) $row['id'];

    $actions = '<button type="button" id="getEdit" class="btn btn-primary btn-xs" data-toggle="modal"'
        . ' data-target="#myModal" data-id="' . $id . '">'
        . '<i class="glyphicon glyphicon-pencil">&nbsp;</i>Edit</button>'
        . ' <a href="index.php?delete=' . $id . '" onclick="return confirm(&#39;Are You Sure ?&#39;)"'
        . ' class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-trash">&nbsp;</i>Delete</a>';

    $data[] = array($id, e($row['name']), $row['price'], $actions);
}

json_out(array(
    'draw'            => post_int('draw', 0),
    'recordsTotal'    => count($data),
    'recordsFiltered' => count($data),
    'data'            => $data,
));
