<?php

$valid_extensions = array('jpeg', 'jpg', 'png', 'gif', 'bmp'); // valid extensions
$path = '../configurator/images/sborki/'; // upload directory
if($_POST['name'] !='' && $_POST['price'] != '' && $_POST['description'] != '' && $_POST['link'] != '')
{
    $img = $_FILES['image']['name'];
    $tmp = $_FILES['image']['tmp_name'];
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
            $name = $_POST['name'];
            $price = $_POST['price'];
            $description = $_POST['description'];
            $link = $_POST['link'];
            $category = $_POST['category'];
            $categoryId = 97;
            //include database configuration file
            include_once '../functions.php';
            include_once 'messages_to_telegram.php';
            //insert form data in the database
            try{
                $insert = $mysqli->query("INSERT INTO sborki (title, description, price, image, link, category, category_id) VALUES ('".$name."','".$description."','".$price."','".strtolower($final_image)."','".$link."','".$category."','".$categoryId."')");
                //SendMessageToBot("<b>Название:</b> ".$name."\n<b>Цена:</b> ".$price."\n<b>Описание:</b>\n <i>".$description."</i>\n", $_SERVER['SERVER_NAME']."/images/products/".strtolower($final_image));
                echo 'Сборка успешно добавлена в базу';
            }catch(Exception $ex){
                echo $ex->getMessage();
            }

            //echo $insert?'ok':'err';
        }
    }
    else
    {
        echo 'Неверное расширение картинки или не выбрана картинка товара';
    }
}else{
    echo 'Не все поля заполнены';
}
