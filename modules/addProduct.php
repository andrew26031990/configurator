<?php

$valid_extensions = array('jpeg', 'jpg', 'png', 'gif', 'bmp'); // valid extensions
$path = '../configurator/images/products/'; // upload directory

if($_POST['name'] !='' && $_POST['price'] != '' && $_POST['description'] != '' && $_POST['filter'] != '' && $_POST['tip_tovar_tov'] != '')
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
            $name = $_POST['name'];
            $price = $_POST['price'];
            $description = $_POST['description'];
            $filter = $_POST['filter'];
            //include database configuration file
            include_once '../functions.php';
            include_once 'messages_to_telegram.php';
            //insert form data in the database
            try{
                $mysqli->begin_transaction();

                $insert = $mysqli->query("INSERT INTO products (name, description, price, image, f_id) VALUES ('".$name."','".$description."','".$price."','".strtolower($final_image)."','".$filter."')");

                if(!$insert){
                    throw new Exception('Ошибка добавления товара: '.$mysqli->error);
                }
                $prod_id = $mysqli->insert_id;

                $tree_id = 0;
                if($_POST['tip_tovar_tov'] != 0) $tree_id = $_POST['tip_tovar_tov'];

                if($tree_id == 0){
                    throw new Exception('Не выбран товар или категория');
                }

                if(!$mysqli->query("INSERT INTO `tree_prod` (`tree_id`, `prod_id`) VALUES ($tree_id,$prod_id)")){
                    throw new Exception('Ошибка добавления связи: '.$mysqli->error);
                }

                $mysqli->commit();
                echo 'Товар успешно добавлен в базу, связь добавлена';

            }catch(Exception $ex){
                $mysqli->rollback();
                @unlink($path);
                echo $ex->getMessage();
            }
//            try{
//                $insert = $mysqli->query("INSERT INTO products (name, description, price, image, f_id) VALUES ('".$name."','".$description."','".$price."','".strtolower($final_image)."','".$filter."')");
//
//                if(!$insert){
//                    @unlink($path);
//                    echo 'Ошибка добавления товара: '.$mysqli->error;
//                    return;
//                }
//                $prod_id = $mysqli->insert_id;
//
//                $tree_id = 0;
//                if($_POST['tip_tovar_tov'] != 0) $tree_id = $_POST['tip_tovar_tov'];
////$sql = "";
//                if($tree_id != 0 && $prod_id != 0){
//                    if($mysqli->query("INSERT INTO `tree_prod` (`tree_id`, `prod_id`) VALUES ($tree_id,$prod_id)")){
//                        echo 'Связь успешно добавлена';
//                    }else{
//                        echo $mysqli->error.' Такая связь уже существует';
//                    }
//                }else{
//                    echo 'Не выбран товар или категория';
//                }
//                //SendMessageToBot("<b>Название:</b> ".$name."\n<b>Цена:</b> ".$price."\n<b>Описание:</b>\n <i>".$description."</i>\n", $_SERVER['SERVER_NAME']."/images/products/".strtolower($final_image));
//                echo 'Товар успешно добавлен в базу';
//            }catch(Exception $ex){
//                echo $ex->getMessage();
//            }
            
            //echo $insert?'ok':'err';
        }else{
            echo 'Не удалось сохранить картинку';
        }
    } 
    else 
    {
        echo 'Неверное расширение картинки или не выбрана картинка товара';
    }
}else{
        echo 'Не все поля заполнены';
}
