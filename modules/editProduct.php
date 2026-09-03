<?php

$valid_extensions = array('jpeg', 'jpg', 'png', 'gif', 'bmp'); // valid extensions
$path = '../configurator/images/products/'; // upload directory

/**
 * Перезаписывает связи товара со сборками/типами (tree_prod) по выбору из мультиселекта.
 */
function syncProductTrees($mysqli, $prod_id, $postTrees){
    $prod_id = (int)$prod_id;
    if($prod_id <= 0) return;
    $trees = is_array($postTrees) ? $postTrees : array();
    $trees = array_values(array_unique(array_filter(array_map('intval', $trees), function($v){ return $v > 0; })));
    $mysqli->query("DELETE FROM `tree_prod` WHERE prod_id = $prod_id");
    foreach($trees as $tid){
        $mysqli->query("INSERT INTO `tree_prod` (`tree_id`, `prod_id`) VALUES ($tid, $prod_id)");
    }
}

if (!file_exists($_FILES['picture']['tmp_name']) || !is_uploaded_file($_FILES['picture']['tmp_name'])) 
{
    if($_POST['name'] !='' && $_POST['price'] != '' && $_POST['description'] != '' && $_POST['filter'] != ''){
        include_once '../functions.php';
        try{
            $id = mysqli_real_escape_string($mysqli, $_POST['idTovara']);
            $name = mysqli_real_escape_string($mysqli, $_POST['name']);
            $price = mysqli_real_escape_string($mysqli, $_POST['price']);
            $description = mysqli_real_escape_string($mysqli, $_POST['description']);
            $filter = mysqli_real_escape_string($mysqli, $_POST['filter']);
            $insert = $mysqli->query("UPDATE products SET name = '$name', description = '$description', price = '$price', f_id = '$filter' WHERE id = '$id'");
            syncProductTrees($mysqli, $_POST['idTovara'], isset($_POST['tip_tovar_tov']) ? $_POST['tip_tovar_tov'] : array());
            echo 'Данные товара были успешно изменены';
        }catch(Exception $ex){
            echo $ex->getMessage();
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
                $oldImg = $_POST['oldImg'];
                //include database configuration file
                include_once '../functions.php';
                $id = mysqli_real_escape_string($mysqli, $_POST['idTovara']);
                $name = mysqli_real_escape_string($mysqli, $_POST['name']);
                $price = mysqli_real_escape_string($mysqli, $_POST['price']);
                $description = mysqli_real_escape_string($mysqli, $_POST['description']);
                $filter = mysqli_real_escape_string($mysqli, $_POST['filter']);
                $final_image = mysqli_real_escape_string($mysqli, strtolower($final_image));
                //insert form data in the database
                try{
                    $insert = $mysqli->query("UPDATE products SET name = '$name', description = '$description', price = '$price', image = '$final_image', f_id = '$filter' WHERE id = '$id'");
                    syncProductTrees($mysqli, $_POST['idTovara'], isset($_POST['tip_tovar_tov']) ? $_POST['tip_tovar_tov'] : array());
                    $mask = "../".$oldImg;
                    if (file_exists($mask)) {unlink($mask);}
                    //$insert = $mysqli->query("INSERT INTO products (name, description, price, image, f_id) VALUES ('".$name."','".$description."','".$price."','".$final_image."','".$filter."')");
                    echo 'Товар был успешно отредактирован';
                }catch(Exception $ex){
                    echo $ex->getMessage();
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


