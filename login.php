<?php

session_start();
ini_set('display_errors', -1);

if (isset($_SESSION['username'])) {
    header("Location: /admin.php");
    exit();
}


?>
<!DOCTYPE html>
<html lang="en">

<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">

  
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>PCMARKET - Login</title>

  <!-- Custom fonts for this template-->
  <link href="admin/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

  <!-- Custom styles for this template-->
  <link href="admin/css/sb-admin-2.min.css" rel="stylesheet">
<script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js"></script>
</head>

<body class="bg-gradient-primary">

  <div class="container">

    <!-- Outer Row -->
    <div class="row justify-content-center">

      <div class="col-xl-10 col-lg-12 col-md-9">

        <div class="card o-hidden border-0 shadow-lg my-5">
          <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            <div class="row">
              <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>
              <div class="col-lg-6">
                <div class="p-5">
                  <div class="text-center">
                    <h1 class="h4 text-gray-900 mb-4">Welcome Back!</h1>
                  </div>
                  <form class="user" id="loginform" action="login.php" method="post">
                    <div class="form-group">
                      <input type="text" class="form-control form-control-user" id="exampleInputEmail" name="login" aria-describedby="emailHelp" placeholder="Enter username...">
                    </div>
                    <div class="form-group">
                      <input type="password" class="form-control form-control-user" id="exampleInputPassword" name="password" placeholder="Password">
                    </div>
                    <div class="checkbox mb-3" style="text-align: center;">
                        <div class="g-recaptcha" data-sitekey="6LfLXM0UAAAAAG34WmIzuvGAHeAgLz33y24i-6bq">
                      </div>
                    <button href="index.html" class="btn btn-primary btn-user btn-block" type="submit" name="enter" style="margin-top:15px;">
                      Login
                    </button>
                    <hr>
                    
                  </form>
                  <script type="text/javascript">
                      $(document).ready(function() {
                        $('#loginform').submit(function(e) {
                          e.preventDefault();
                          $.ajax({
                             type: "POST",
                             url: '/modules/auth.php',
                             data: $(this).serialize(),
                             success: function(data)
                             {
                                if (data === 'Login') {
                                  window.location = '/admin.php';
                                }else{
                                    alert('Вы робот' + data);
                                }
                             }
                         });
                       });
                      });
                  </script> 
                  <script src='https://www.google.com/recaptcha/api.js' async defer></script>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>

    </div>

  </div>

  <!-- Bootstrap core JavaScript-->
  <script src="admin/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- Core plugin JavaScript-->
  <script src="admin/vendor/jquery-easing/jquery.easing.min.js"></script>

  <!-- Custom scripts for all pages-->
  <script src="admin/js/sb-admin-2.min.js"></script>

</body>

</html>




