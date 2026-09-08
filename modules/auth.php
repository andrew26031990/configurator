<?php
/**
 * Вход в админку. Публичный эндпоинт.
 */

require_once __DIR__ . '/../functions.php';

header('Content-Type: text/plain; charset=UTF-8');

secure_session_start();

/* --- Лимит попыток по IP --------------------------------------------- */

$blockedFor = login_blocked_seconds();

if ($blockedFor > 0) {
    http_response_code(429);
    exit('Слишком много попыток входа. Повторите через ' . ceil($blockedFor / 60) . ' мин.');
}

/* --- reCAPTCHA -------------------------------------------------------- */

/*
 * Капчу можно выключить в config.local.php (recaptcha.enabled = false).
 * Тогда единственной защитой формы остаётся лимит попыток выше,
 * поэтому режим задуман как временный.
 */
if (recaptcha_enabled()) {
    $captcha = post_str('g-recaptcha-response', 2000);

    if ($captcha === '') {
        exit('incorrectCaptcha');
    }

    $verify = @file_get_contents(
        'https://www.google.com/recaptcha/api/siteverify',
        false,
        stream_context_create(array(
            'http' => array(
                'method'        => 'POST',
                'header'        => "Content-Type: application/x-www-form-urlencoded\r\n",
                'content'       => http_build_query(array(
                    'secret'   => (string) config_get('recaptcha.secret'),
                    'response' => $captcha,
                    'remoteip' => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '',
                )),
                'timeout'       => 5,
                'ignore_errors' => true,
            ),
        ))
    );

    $captchaData = $verify === false ? null : json_decode($verify, true);

    if (!is_array($captchaData) || empty($captchaData['success'])) {
        exit('incorrectCaptcha');
    }
}

/* --- Проверка логина и пароля ----------------------------------------- */

$login    = post_str('login', 100);
$password = isset($_POST['password']) && is_scalar($_POST['password']) ? (string) $_POST['password'] : '';

if ($login === '' || $password === '') {
    exit('False');
}

if (!CheckLoginPass($mysqli, $login, $password)) {
    login_register_failure();
    exit('False');
}

// Смена идентификатора сессии после входа — защита от session fixation.
session_regenerate_id(true);

login_reset_failures();

$_SESSION['username'] = $login;

echo 'Login';
