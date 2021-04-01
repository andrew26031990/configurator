<?php
header('Content-Type: application/json');

include '../functions.php';

$sqlQuery = "SELECT COUNT(id) as quantity, date FROM `orders` where date in (select date from orders) GROUP by date";

$result = mysqli_query($mysqli, $sqlQuery);


$data = array();
foreach ($result as $row) {
	$data[] = $row;
}

mysqli_close($conn);

echo json_encode($data);
?>