<?php
require_once __DIR__ . '/functions.php';

deny_framing();
secure_session_start();

// Уже авторизован (реальный активный пользователь) — на дашборд.
// Проверяем current_user(), а не is_admin(): протухшая сессия (username без
// user_id) иначе зациклила бы редирект login <-> admin.
if (current_user()) {
    header('Location: /admin');
    exit();
}
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>PCMARKET - Вход</title>

    <!-- Font Awesome -->
    <link href="admin/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- AdminLTE 3 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/css/adminlte.min.css">

    <script type="text/javascript" src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>

<body class="hold-transition login-page">
<div class="login-box">
    <div class="card card-outline card-primary">
        <div class="card-header text-center">
            <a href="/" class="h1"><b>PC</b>MARKET</a>
        </div>
        <div class="card-body login-card-body">
            <p class="login-box-msg">Вход в панель управления</p>

            <form class="user" id="loginform" action="/login" method="post">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" name="login" placeholder="Логин">
                    <div class="input-group-append">
                        <div class="input-group-text"><span class="fas fa-user"></span></div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" class="form-control" name="password" placeholder="Пароль">
                    <div class="input-group-append">
                        <div class="input-group-text"><span class="fas fa-lock"></span></div>
                    </div>
                </div>

                <?php if (recaptcha_enabled()) { ?>
                <div class="d-flex justify-content-center mb-3">
                    <div class="g-recaptcha" data-sitekey="<?= e(config_get('recaptcha.sitekey')); ?>"></div>
                </div>
                <?php } ?>

                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-block">Войти</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap 4 Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE 3 -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/js/adminlte.min.js"></script>
<?php if (recaptcha_enabled()) { ?>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<?php } ?>

<script type="text/javascript">
    $(document).ready(function () {
        $('#loginform').submit(function (e) {
            e.preventDefault();
            $.ajax({
                type: "POST",
                url: '/modules/auth.php',
                data: $(this).serialize(),
                success: function (data) {
                    if (data === 'Login') {
                        window.location = '/admin';
                    } else if (data === 'incorrectCaptcha') {
                        alert('Подтвердите, что вы не робот');
                        if (window.grecaptcha) { grecaptcha.reset(); }
                    } else {
                        alert('Неверный логин или пароль');
                        if (window.grecaptcha) { grecaptcha.reset(); }
                    }
                },
                error: function (xhr) {
                    alert(xhr.status === 429
                        ? 'Слишком много попыток входа. Попробуйте позже.'
                        : 'Не удалось выполнить вход');
                    if (window.grecaptcha) { grecaptcha.reset(); }
                }
            });
        });
    });
</script>
</body>

</html>
