<?php
include '../functions.php';

/*$post = $_POST['cat'];
$tr = translit($post);
$sql = "DELETE FROM categories WHERE ";

if ($mysqli->query($sql) === TRUE) {
    //echo $tr;
    $arr = array('link' => $tr, 'name' => $post);
    echo json_encode($arr);
} else {
    echo "Error: " . $sql . "<br>" . $mysqli->error;
}*/

  $id = $_POST['id'];
  //var_dump($id);
  $QR = $mysqli->query("delete from categories where catID='$id'");
  //$QR = mysqli_query($mysqli, "delete * from categories where id=$id");  
  if($QR) {echo 'Категория успешно удалена';   }  
  else {echo 'Ошибка';}


