<?php

$valid_extensions = array('jpeg', 'jpg', 'png', 'gif', 'bmp'); // valid extensions
$path = '../configurator/images/products/'; // upload directory

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
            //include database configuration file
            include_once '../functions.php';
            include_once 'messages_to_telegram.php';
            $name = mysqli_real_escape_string($mysqli, $_POST['name']);
            $price = mysqli_real_escape_string($mysqli, $_POST['price']);
            $description = mysqli_real_escape_string($mysqli, $_POST['description']);
            $filter = mysqli_real_escape_string($mysqli, $_POST['filter']);
            $final_image_db = mysqli_real_escape_string($mysqli, strtolower($final_image));

            // Список выбранных сборок/типов товара (мультиселект tip_tovar_tov[])
            $trees = isset($_POST['tip_tovar_tov']) ? (array)$_POST['tip_tovar_tov'] : array();
            $trees = array_values(array_unique(array_filter(array_map('intval', $trees), function($v){ return $v > 0; })));

            //insert form data in the database
            try{
                $mysqli->begin_transaction();

                $insert = $mysqli->query("INSERT INTO products (name, description, price, image, f_id) VALUES ('".$name."','".$description."','".$price."','".$final_image_db."','".$filter."')");

                if(!$insert){
                    throw new Exception('Ошибка добавления товара: '.$mysqli->error);
                }
                $prod_id = $mysqli->insert_id;

                if(count($trees) == 0){
                    throw new Exception('Не выбрана ни одна сборка/тип товара');
                }

                foreach($trees as $tree_id){
                    if(!$mysqli->query("INSERT INTO `tree_prod` (`tree_id`, `prod_id`) VALUES ($tree_id,$prod_id)")){
                        throw new Exception('Ошибка добавления связи: '.$mysqli->error);
                    }
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
