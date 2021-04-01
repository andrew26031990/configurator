<?php
include '../functions.php';

$post = $_POST['cat'];
$tr = translit($post);
$sql = "INSERT INTO categories (name, link, status) VALUES ('$post', '$tr', 1)";

if ($mysqli->query($sql) === TRUE) {
    //echo $tr;
    $arr = array('link' => $tr, 'name' => $post);
    echo json_encode($arr);
} else {
    echo "Error: " . $sql . "<br>" . $mysqli->error;
}




