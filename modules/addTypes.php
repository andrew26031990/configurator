<?php
include '../functions.php';

$post = $_POST['cat'];
$sql = "INSERT INTO types (name) VALUES ('$post')";

if ($mysqli->query($sql) === TRUE) {
    //echo $tr;
    $arr = array('name' => $post);
    echo json_encode($arr);
} else {
    echo "Error: " . $sql . "<br>" . $mysqli->error;
}




