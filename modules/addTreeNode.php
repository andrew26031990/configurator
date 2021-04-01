<?php
include '../functions.php';
include 'strtr.php';

$name = $_POST['name'];
$parent_id = $_POST['parent_id'];
$level = $_POST['level'];
$sort = $_POST['sort'];
$enabled = $_POST['enabled'];

$link = translit($name);
$group_id = translit($name);
$translit = translit($name);

$sql = "INSERT INTO `tree` (`name`, `parent_id`, `level`, `group_id`, `link`, `translit`, `sort`, `enabled`) VALUES ('$name', '$parent_id', '$level', '$group_id', '$link', '', '$sort', '$enabled')";

if ($mysqli->query($sql) === TRUE) {
    echo 'Узел '.$level.' уровня успешно записан в базу';
} else {
    echo "Error: " + mysqli_error($mysqli);
}
