<?php
require_once __DIR__ . '/../functions.php';
require_admin();

json_out(array(
    'data' => array_map(
        function (array $row) {
            return array(
                'id'          => $row['id'],
                'name'        => $row['name'],
                'description' => $row['description'],
                'image'       => $row['image'],
                'price'       => $row['price'],
                'edit'        => $row['id'],
            );
        },
        db_rows($mysqli, 'SELECT id, name, description, image, price FROM products')
    ),
));
