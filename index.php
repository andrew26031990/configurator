<?php
/*if($_SERVER['REMOTE_ADDR'] != '62.209.150.52'){
    die('Na sayte vedutsya profilakticheskie raboti');
}*/
include 'functions.php';
include 'modules/online.php';
$ini = parse_ini_file('settings.ini');
setlocale(LC_MONETARY, 'uz_UZ');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Type" Content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="Игровые компьютеры для игр 2021 годы, Офисные компьютеры, Для видеомонтажа, для работы с графическими программами." content="">
    <meta name="Собрать ПК Онлайн,Конфигуратор ПК, Компьютер купить" content="">
    <link rel="shortcut icon" href="favicon.ico">
    <title>Конфигуратор компьютеров онлайн- Собрать компьютер в Ташкенте стало легко с Pc Market</title>
    <link rel="stylesheet" href="configurator/css/style.css">
    <link rel="stylesheet" href="configurator/css/magnify.css">
    <link rel="stylesheet" href="configurator/css/magnific-popup.min.css">
    <link rel="stylesheet" href="configurator/css/owl-carousel.min.css">
    <link rel="stylesheet" href="configurator/css/style.css">
</head>
<body>
<div class="preloader2" style="position: fixed; left: 0; top: 0; z-index: 999999; width: 100%; height: 100%; overflow: visible; background: #333 url('https://pcmarket.uz/wp-content/themes/pcmarket/images/spin.svg') no-repeat center center;background-size: 250px 250px;"></div>
<header class="header">
    <div class="container">
        <div class="row align-items-center">
            <div class="column_25">
                <a class="custom-logo-link" href="https://pcmarket.uz"><img class="custom-logo" src="/configurator/images/logo.png" /></a>
            </div>
            <div class="column_75">
                <div class="row text-right justify-content-end align-items-center">
                    <div class="widget_text widget widget_custom_html" id="custom_html-2">
                        <div class="textwidget custom-html-widget">
                            <p><img src="/configurator/images/mail.svg" alt="" />Напишите нам</p>
                            <strong><a href="mailto://<?php echo $ini['email']?>" style="color: white"><?php echo $ini['email']?></a></strong>
                        </div>
                    </div>
                    <div class="widget_text widget widget_custom_html" id="custom_html-3">
                        <div class="textwidget custom-html-widget">
                            <p><img src="/configurator/images/phone.svg" alt="" />Позвоните нам</p>
                            <strong><a href="tel://<?php echo $ini['phone']?>" style="color: white"><?php echo $ini['phone']?></a></strong>
                        </div>
                    </div>
                    <div class="widget_text widget widget_custom_html" id="custom_html-4">
                        <div class="textwidget custom-html-widget">
                            <p><img src="/configurator/images/time.svg" alt="" />Рабочее время</p>
                            <strong><?php echo $ini['work_time']?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php if(isset($_GET['cat'])) { ?>
            <nav>
                <ul class="row align-items-center main__menu">
                    <li><a href="/">Категории</a></li>
                    <li><?php echo "On-Line: ".on_line(); ?></li>
                </ul>
            </nav>
        <?php } ?>
    </div>
</header>
<section class="main">
    <?php
        if(isset($_GET["cat"])){ $cat = $_GET["cat"];
            include($_SERVER['DOCUMENT_ROOT'].'/configurator/configuration.php');
        }else {
            include($_SERVER['DOCUMENT_ROOT'].'/configurator/categories.php');
        }
    ?>
</section>
<?php include($_SERVER['DOCUMENT_ROOT'].'/configurator/scripts.php'); ?>
<!-- Yandex.Metrika counter -->
<script type="text/javascript" >
   (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
   m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
   (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

   ym(65798533, "init", {
        clickmap:true,
        trackLinks:true,
        accurateTrackBounce:true,
        webvisor:true
   });
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/65798533" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->

</script>
</body>
</html>