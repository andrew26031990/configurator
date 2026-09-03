<?php

$valid_extensions = array('jpeg', 'jpg', 'png', 'gif', 'bmp'); // valid extensions
$path = '../configurator/images/sborki/'; // upload directory

if (!file_exists($_FILES['edit_image']['tmp_name']) || !is_uploaded_file($_FILES['edit_image']['tmp_name']))
{
    if($_POST['edit_link'] !='' && $_POST['edit_price'] != '' && $_POST['edit_description'] != '' && $_POST['edit_title'] != ''){
        include_once '../functions.php';
        try{
            $id = $_POST['idSborki'];
            $title = $_POST['edit_title'];
            $price = $_POST['edit_price'];
            $description = $_POST['edit_description'];
            $link = $_POST['edit_link'];
            $category = strtoupper($_POST['edit_category']);

            $insert = $mysqli->query("UPDATE sborki SET title = '$title', description = '$description', price = '$price', link = '$link', category = '$category' WHERE id = '$id'");
            echo 'Данные сборки были успешно изменены';
        }catch(Exception $ex){
            echo $e->getMessage();
        }
    }else{
        echo 'Не все поля заполнены';
    }
}else{
    if($_POST['edit_link'] !='' && $_POST['edit_price'] != '' && $_POST['edit_description'] != '' && $_POST['edit_title'] != '')
    {
        $img = $_FILES['edit_image']['name'];
        $tmp = $_FILES['edit_image']['tmp_name'];
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
                $id = $_POST['idSborki'];
                $oldImg = $_POST['oldImg'];
                $title = $_POST['edit_title'];
                $price = $_POST['edit_price'];
                $description = $_POST['edit_description'];
                $link = $_POST['edit_link'];
                $category = strtoupper($_POST['edit_category']);
                $final_image = strtolower($final_image);
                //include database configuration file
                include_once '../functions.php';
                //insert form data in the database
                try{
                    $insert = $mysqli->query("UPDATE sborki SET title = '$title', description = '$description', price = '$price', image = '$final_image', link = '$link', category = '$category' WHERE id = '$id'");
                    $mask = "..".$oldImg;
                    if (file_exists($mask)) {unlink($mask);}
                    //$insert = $mysqli->query("INSERT INTO products (name, description, price, image, f_id) VALUES ('".$name."','".$description."','".$price."','".$final_image."','".$filter."')");
                    echo 'Сборка была успешно отредактирована';
                }catch(Exception $ex){
                    echo $e->getMessage();
                }

                //echo $insert?'ok':'err';
            }else{
                echo 'Не удалось загрузить файл';
            }
        }
        else
        {
            echo 'Неверное расширение картинки';
        }
    }else{
        echo 'Не все поля заполнены';
    }
}


