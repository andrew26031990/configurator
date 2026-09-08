<?php
/**
 * Отправка писем через SMTP (PHPMailer).
 *
 * Раньше этот код лежал в modules/mail.php одним закомментированным
 * блоком на 200 строк, вместе с паролем от почтового ящика прямо в
 * тексте. Теперь настройки берутся из config.local.php, а тело письма —
 * из mail/templates/order.php.
 */

require_once __DIR__ . '/../security.php';

/**
 * Включена ли отправка почты.
 *
 * @return bool
 */
function mail_enabled()
{
    return (bool) config_get('mail.enabled', false);
}

/**
 * Рендерит PHP-шаблон письма в строку.
 *
 * @param  string              $template имя файла в mail/templates
 * @param  array<string,mixed> $vars
 * @return string
 */
function render_mail_template($template, array $vars)
{
    $file = __DIR__ . '/templates/' . basename($template) . '.php';

    if (!is_file($file)) {
        error_log('mail: шаблон не найден — ' . $file);
        return '';
    }

    extract($vars, EXTR_SKIP);

    ob_start();
    include $file;

    return (string) ob_get_clean();
}

/**
 * Создаёт и настраивает PHPMailer по данным из конфига.
 *
 * @return PHPMailer|null null, если библиотека недоступна
 */
function make_mailer()
{
    if (!class_exists('PHPMailer')) {
        /*
         * PHPMailerAutoload.php намеренно не подключается: он объявляет
         * __autoload(), удалённую в PHP 8.0, и на боевом PHP 8.2 роняет
         * запрос фаталом. Классы грузим напрямую — class.smtp.php нужен
         * явно, потому что при isSMTP() библиотека рассчитывает достать
         * его тем же автозагрузчиком.
         */
        $phpmailer = __DIR__ . '/class.phpmailer.php';
        $smtp      = __DIR__ . '/class.smtp.php';

        if (!is_file($phpmailer) || !is_file($smtp)) {
            error_log('mail: PHPMailer не найден');
            return null;
        }

        require_once $phpmailer;
        require_once $smtp;
    }

    $mail = new PHPMailer();

    $mail->isSMTP();
    $mail->Host       = (string) config_get('mail.host');
    $mail->Port       = (int) config_get('mail.port', 465);
    $mail->SMTPAuth   = true;
    $mail->Username   = (string) config_get('mail.username');
    $mail->Password   = (string) config_get('mail.password');
    $mail->CharSet    = 'UTF-8';

    // '' — отправка без шифрования (для локальных заглушек вроде MailHog).
    $secure = (string) config_get('mail.encryption', 'ssl');

    if ($secure !== '') {
        $mail->SMTPSecure = $secure;
    } else {
        $mail->SMTPAutoTLS = false;
    }

    if (config_get('mail.debug', false)) {
        $mail->SMTPDebug = 2;
        $mail->Debugoutput = function ($str) {
            error_log('PHPMailer: ' . $str);
        };
    }

    return $mail;
}

/**
 * Письмо с заявкой: менеджеру магазина и, если указан, клиенту.
 *
 * Название по образцу SendMailToBot() из messages_to_telegram.php.
 *
 * @param  array<string,string> $order name, email, phone, sborka,
 *                                     config, amount, sborkaLink
 * @return bool                 true — письмо принято сервером
 */
function SendOrderMail(array $order)
{
    if (!mail_enabled()) {
        return false;
    }

    $mail = make_mailer();

    if ($mail === null) {
        return false;
    }

    $defaults = array(
        'name'       => '',
        'email'      => '',
        'phone'      => '',
        'sborka'     => '',
        'config'     => '',
        'amount'     => '',
        'sborkaLink' => '',
    );

    $order = array_merge($defaults, $order);

    // Контакты для подвала письма — из того же settings.ini, что и шапка сайта.
    $ini = @parse_ini_file(APP_ROOT . '/settings.ini');

    $order['shopPhone'] = is_array($ini) && isset($ini['phone']) ? (string) $ini['phone'] : '';
    $order['shopEmail'] = is_array($ini) && isset($ini['email']) ? (string) $ini['email'] : '';

    try {
        $mail->setFrom(
            (string) config_get('mail.from', config_get('mail.username')),
            (string) config_get('mail.from_name', 'Конфигуратор')
        );

        // Копия менеджеру магазина.
        $to = (string) config_get('mail.to', config_get('mail.username'));

        if ($to !== '') {
            $mail->addAddress($to);
        }

        // И клиенту, если он оставил корректный адрес.
        if ($order['email'] !== '' && filter_var($order['email'], FILTER_VALIDATE_EMAIL)) {
            $mail->addAddress($order['email']);
            $mail->addReplyTo($order['email'], $order['name'] !== '' ? $order['name'] : 'Клиент');
        }

        $mail->isHTML(true);
        $mail->Subject = (string) config_get('mail.subject', 'Конфигурация');
        $mail->Body    = render_mail_template('order', $order);
        $mail->AltBody = 'Имя: ' . $order['name'] . PHP_EOL
            . 'Телефон: ' . $order['phone'] . PHP_EOL
            . 'Email: ' . $order['email'] . PHP_EOL
            . 'Сборка: ' . $order['sborka'] . PHP_EOL
            . 'Сумма: ' . $order['amount'] . PHP_EOL
            . 'Ссылка: ' . $order['sborkaLink'];

        if (!$mail->send()) {
            error_log('mail: не отправлено — ' . $mail->ErrorInfo);
            return false;
        }

        return true;
    } catch (Throwable $e) {
        // Заявка уже ушла в Telegram, поэтому падение почты не должно
        // ломать ответ клиенту — только пишем в лог.
        error_log('mail: ' . $e->getMessage());
        return false;
    }
}
