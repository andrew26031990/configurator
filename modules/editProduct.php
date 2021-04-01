<?php

$valid_extensions = array('jpeg', 'jpg', 'png', 'gif', 'bmp'); // valid extensions
$path = '../images/products/'; // upload directory

if (!file_exists($_FILES['picture']['tmp_name']) || !is_uploaded_file($_FILES['picture']['tmp_name'])) 
{
    if($_POST['name'] !='' && $_POST['price'] != '' && $_POST['description'] != '' && $_POST['filter'] != ''){
        include_once '../functions.php';
        try{
            $id = $_POST['idTovara'];
            $name = $_POST['name'];
            $price = $_POST['price'];
            $description = $_POST['description'];
            $filter = $_POST['filter'];
            $insert = $mysqli->query("UPDATE products SET name = '$name', description = '$description', price = '$price', f_id = '$filter' WHERE id = '$id'");
            echo 'Данные товара были успешно изменены';
        }catch(Exception $ex){
            echo $e->getMessage();
        }
    }else{
        echo 'Не все поля заполнены';
    }
}else{
    if($_POST['name'] !='' && $_POST['price'] != '' && $_POST['description'] != '' && $_POST['filter'] != '')
    {
        $img = $_FILES['picture']['name'];
        $tmp = $_FILES['picture']['tmp_name'];
        // get uploaded file's extension
        $ext = strtolower(pathinfo($img, PATHINFO_EXTENSION));
        // can upload same image using rand function
        $final_image = rand(1000,1000000).$img;
        // check's valid format
        if(in_array($ext, $valid_extensions)) 
        { 
            $path = $path.strtolower($final_image); 
            if(move_uploaded_file($tmp,$path)) 
            {
                //echo "<img src='$path' />";
                $id = $_POST['idTovara'];
                $oldImg = $_POST['oldImg'];
                $name = $_POST['name'];
                $price = $_POST['price'];
                $description = $_POST['description'];
                $filter = $_POST['filter'];
                $final_image = strtolower($final_image);
                //include database configuration file
                include_once '../functions.php';
                //insert form data in the database
                try{
                    $insert = $mysqli->query("UPDATE products SET name = '$name', description = '$description', price = '$price', image = '$final_image', f_id = '$filter' WHERE id = '$id'");
                    $mask = "../".$oldImg;
                    if (file_exists($mask)) {unlink($mask);}
                    //$insert = $mysqli->query("INSERT INTO products (name, description, price, image, f_id) VALUES ('".$name."','".$description."','".$price."','".$final_image."','".$filter."')");
                    echo 'Товар был успешно отредактирован';
                }catch(Exception $ex){
                    echo $e->getMessage();
                }

                //echo $insert?'ok':'err';
            }
        } 
        else 
        {
            echo 'Неверное расширение картинки'.$_POST['name'].'/'.$_POST['price'].'/'.$_POST['filter'].'/'.$_POST['description'];
        }
    }else{
            echo 'Не все поля заполнены';
    }
}    


