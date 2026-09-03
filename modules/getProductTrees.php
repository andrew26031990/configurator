<?php

include '../functions.php';

$prod_id = isset($_POST['prod_id']) ? (int)$_POST['prod_id'] : 0;

header('Content-Type: application/json');
echo json_encode(getProductTreeIds($mysqli, $prod_id));
