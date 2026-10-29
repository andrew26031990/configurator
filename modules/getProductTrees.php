<?php
require_once __DIR__ . '/../functions.php';
require_admin();

$id = post_int('prod_id');

json_out($id === null || $id <= 0 ? array() : getProductTreeIds($mysqli, $id));
