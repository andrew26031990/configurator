<?php
//die('Админка на техническом обслуживании');
include 'functions.php';
//include 'modules/backup.php';
session_start();
ini_set('display_errors', -1);
//unset($_SESSION['flag']);
if (!isset($_SESSION['username'])) {
    header("Location: /login.php");
    exit();
}

if (isset($_GET['view']) && ($_GET['view'] == 'exit_cab')) {
    exit_cab();
    header("Location: /login.php");
    exit();
}
//$catid = getCatId($mysqli, $_GET['cat']);

?>
<!DOCTYPE html>
<html lang="ru">

<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">


    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>PCMARKET - Dashboard</title>

    <!-- jQuery (нужен плагинам ниже) -->
    <script src="https://code.jquery.com/jquery-latest.min.js"></script>

    <!-- Font Awesome -->
    <link href="admin/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <!-- Google Font: Source Sans Pro (AdminLTE) -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- AdminLTE 3 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/css/adminlte.min.css">
    <!-- jsTree -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/themes/default/style.min.css" />
    <!-- DataTables (Bootstrap 4) -->
    <link href="admin/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">

</head>

<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <!-- Navbar / Topbar -->
    <?php include($_SERVER['DOCUMENT_ROOT'].'/admin/pages/topbar.php'); ?>
    <!-- /.navbar -->

    <!-- Sidebar -->
    <?php include($_SERVER['DOCUMENT_ROOT'].'/admin/pages/sidebar.php'); ?>
    <!-- /Sidebar -->

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <section class="content pt-3">
            <div class="container-fluid">
                <?php
                    if(!isset($_GET['view']))
                      {
                          include($_SERVER['DOCUMENT_ROOT'].'/admin/pages/dashboard.php');
                      }else {
                          include($_SERVER['DOCUMENT_ROOT'].'/admin/pages/'.$_GET['view'].'.php');
                      }
                ?>
            </div>
        </section>
    </div>
    <!-- /.content-wrapper -->

    <!-- Footer -->
    <?php include($_SERVER['DOCUMENT_ROOT'].'/admin/pages/footer.php'); ?>
    <!-- /Footer -->

</div>
<!-- ./wrapper -->

<!-- Modals-->
<?php include($_SERVER['DOCUMENT_ROOT'].'/admin/modals/modals.php'); ?>
<!-- End Modals-->
<?php include($_SERVER['DOCUMENT_ROOT'].'/admin/pages/scripts.php'); ?>


</body>

</html>
