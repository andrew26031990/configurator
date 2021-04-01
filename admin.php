<?php

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
<html lang="en">

<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">


    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>PCMARKET - Dashboard</title>

    <!-- Custom fonts for this template-->
    <link href="admin/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/themes/default/style.min.css" />
    <!--<script src="//cdn.ckeditor.com/4.11.1/standard/ckeditor.js"></script>-->
    <script src="https://code.jquery.com/jquery-latest.min.js"></script>
    <!-- Custom styles for this template-->
    <link href="admin/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="admin/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">

</head>

<body id="page-top">
<div class="preloader2" style="position: fixed; left: 0; top: 0; z-index: 999; width: 100%; height: 100%; overflow: visible; background: #333 url('images/preloader.gif') no-repeat center center;"></div>
<?php //backup_tables('localhost', 'victors90_config', '5850058R', 'victors90_config', '*'); ?>
<!-- Page Wrapper -->
<div id="wrapper">

    <!-- Sidebar -->
    <?php include($_SERVER['DOCUMENT_ROOT'].'/admin/pages/sidebar.php'); ?>
    <!-- End of Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

        <!-- Main Content -->
        <div id="content">

            <!-- Topbar -->
            <?php include($_SERVER['DOCUMENT_ROOT'].'/admin/pages/topbar.php'); ?>
            <!-- End of Topbar -->

            <!-- Begin Page Content -->
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
            <!-- /.container-fluid -->

        </div>
        <!-- End of Main Content -->

        <!-- Footer -->
        <?php include($_SERVER['DOCUMENT_ROOT'].'/admin/pages/footer.php'); ?>
        <!-- End of Footer -->

    </div>
    <!-- End of Content Wrapper -->

</div>
<!-- End of Page Wrapper -->

<!-- Scroll to Top Button-->
<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

<!-- Modals-->
<?php include($_SERVER['DOCUMENT_ROOT'].'/admin/modals/modals.php'); ?>
<!-- End Modals-->
<?php include($_SERVER['DOCUMENT_ROOT'].'/admin/pages/scripts.php'); ?>


</body>

</html>
