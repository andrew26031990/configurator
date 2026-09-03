<?php

//Устанавливаем кодировку и вывод всех ошибок
header('Content-Type: text/html; charset=UTF-8');
error_reporting(E_ALL);
$db = 'sanyap3b_confi2';
//Объектно-ориентированный стиль
//$mysqli = new mysqli('localhost', 'victors90_config', '1Z*vIdRE', 'victors90_config');
$mysqli = new mysqli('localhost', 'wp_user', 'uK2wi9eiEeghili9', 'configurator'); //real config

//Устанавливаем кодировку utf8
$mysqli->query("SET NAMES 'utf8'");
$mysqli->set_charset("utf8");

if ($mysqli->connect_error) {
    die('Ошибка подключения (' . $mysqli->connect_errno . ') '
        . $mysqli->connect_error);
} else {
    //echo 'OK';
}

// Поверка, есть ли GET запрос
if (isset($_GET['page'])) {
    // Если да то переменной $pageno присваиваем его
    $pageno = $_GET['page'];
} else { // Иначе
    // Присваиваем $pageno один
    $pageno = 1;
}

// Назначаем количество данных на одной странице
$size_page = 8;
// Вычисляем с какого объекта начать выводить
$offset = ($pageno - 1) * $size_page;

// SQL запрос для получения количества элементов
$count_sql = "SELECT COUNT(*) FROM `sborki`";

$cats = mysqli_query($mysqli, "SELECT DISTINCT category as cat FROM `sborki`");

// Отправляем запрос для получения количества элементов
$result = mysqli_query($mysqli, $count_sql);

// Получаем результат
$total_rows = mysqli_fetch_array($result)[0];

// Вычисляем количество страниц
$total_pages = ceil($total_rows / $size_page);

?>

<div class="container">
    <div class="row products">
        <!--- СБОРКА -->
        <?php
        $sql = "SELECT * FROM `sborki` ORDER BY price LIMIT $offset, $size_page";
        $res_data = mysqli_query($mysqli, $sql);

        while ($row = mysqli_fetch_array($res_data)) {
            ?>
            <div class="product">

                <div class="product__inner">
                    <a href="#" class="woocommerce-LoopProduct-link woocommerce-loop-product__link">

                            <span class="wrapped">
                                <img width="400" height="400"
                                     src="configurator/images/sborki/<?php echo $row['image']; ?>" alt=""
                                     loading="lazy">
                            </span>
                        <h2 class="woocommerce-loop-product__title"><?php echo $row['title']; ?></h2>
                        <span class="price"><del aria-hidden="true">Цена:
                                    <span class="woocommerce-Price-amount amount"><bdi><?php echo number_format($row['price'], 0, '', ' '); ?>
                                            <span class="woocommerce-Price-currencySymbol">сум</span>
                                        </bdi>

                                    </span>

                                <!--<span>сум</span>-->
                                </del>
                            <!--<ins>

                                    <span class="woocommerce-Price-amount amount">
                                        <bdi><?php /*echo number_format($row['price'], 0, '', ' ');  */ ?>
                                            <span class="woocommerce-Price-currencySymbol">
                                                сум</span>
                                        </bdi>
                                    </span>
                                    <span>сум</span>
                                </ins>-->
                            </span>
                    </a>

                    <!--<a href="<?php /*echo $row['link'];  */ ?>" class="button" target="_blank">Открыть сборку</a>-->

                    <a class="description" href="<?php echo $row['link']; ?>" target="_blank">
                            <span class="product-image">
                                <img width="400" height="400"
                                     src="configurator/images/sborki/<?php echo $row['image']; ?>"> </span>


                        <h5><?php echo $row['description']; ?>
                        </h5>
                        <a href="<?php echo $row['link']; ?>" class="button" target="_blank" style="z-index: 1;">Открыть
                            сборку</a>
                    </a>
                </div>


            </div>
            <!---- -->
        <?php }
        mysqli_close($mysqli);
        ?>

    </div>
    <ul class="pagination">
        <?php for ($i = 1; $i < $total_pages + 1; $i++) { ?>
            <?php if ($_GET['page'] != $i) { ?>
                <li class="active">
                    <a href="index.php?cat=<?php echo $_GET['cat'] ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
            <?php } else { ?>
                <li class="disabled">
                    <a href="#" style="pointer-events: none;opacity: 0.5;"><?php echo $i; ?></a>
                </li>
            <?php } ?>
        <?php } ?>
        <!--<li class="active">
            <span>1</span>
        </li>
        <li class="disabled">
            <a href="#">2</a>
        </li>
        <li class="">
            <a href="index.php?cat=sborki&amp;pageno=2">3</a>
        </li>
        <li><a href="?pageno=4">4</a></li>-->
    </ul>
</div>
