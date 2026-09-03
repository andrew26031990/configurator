<?php
/**
 * Вход в админку. Публичный эндпоинт.
 */

require_once __DIR__ . '/../functions.php';

header('Content-Type: text/plain; charset=UTF-8');

secure_session_start();

/* --- Простой брутфорс-лимит: 5 попыток, затем пауза 5 минут ---------- */

$attempts = isset($_SESSION['login_attempts']) ? (int) $_SESSION['login_attempts'] : 0;
$blocked  = isset($_SESSION['login_blocked_until']) ? (int) $_SESSION['login_blocked_until'] : 0;

if ($blocked > time()) {
    http_response_code(429);
    exit('Слишком много попыток входа. Попробуйте позже.');
}

/* --- reCAPTCHA -------------------------------------------------------- */

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

/* --- Проверка логина и пароля ----------------------------------------- */

$login    = post_str('login', 100);
$password = isset($_POST['password']) && is_scalar($_POST['password']) ? (string) $_POST['password'] : '';

if ($login === '' || $password === '') {
    exit('False');
}

if (!CheckLoginPass($mysqli, $login, $password)) {
    $_SESSION['login_attempts'] = $attempts + 1;

    if ($_SESSION['login_attempts'] >= 5) {
        $_SESSION['login_blocked_until'] = time() + 300;
        $_SESSION['login_attempts']      = 0;
    }

    exit('False');
}

// Смена идентификатора сессии после входа — защита от session fixation.
session_regenerate_id(true);

unset($_SESSION['login_attempts'], $_SESSION['login_blocked_until']);

$_SESSION['username'] = $login;

echo 'Login';
