<?php
require 'PHPMailerAutoload.php';

$mail = new PHPMailer;

//$mail->SMTPDebug = 3;                               // Включить подробный вывод отладки

$mail->isSMTP();                                      // Указываем что используем SMTP
$mail->Host = 'mail.ahost.uz';  // Укажите SMTP Сервер
$mail->SMTPAuth = true;                               // Включение проверки подлинности SMTP
$mail->Username = 'user@example.com';                 // Логин почтового ящика
$mail->Password = 'secret';                           // Пароль 
$mail->SMTPSecure = 'ssl';                            // Указываем какое подключение используем TLS или SSL в нашем случае SSL
$mail->Port = 465;                                    // Порт для SSL - 465, TLS 587.

$mail->setFrom('from@example.com', 'Mailer');
$mail->addAddress('joe@example.net', 'Joe User');     // Добавить получателя
$mail->addAddress('ellen@example.com');               // Дальше все понятно.
$mail->addReplyTo('info@example.com', 'Information');
$mail->addCC('cc@example.com');
$mail->addBCC('bcc@example.com');

$mail->addAttachment('/var/tmp/file.tar.gz');         // Добавим вложения если нет, то просто комментируем
$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Тоже самое
$mail->isHTML(true);                                  // Разрешаем передавать HTML

$mail->Subject = 'Here is the subject';
$mail->Body    = 'This is the HTML message body <b>in bold!</b>';
$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

if(!$mail->send()) {
    echo 'Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    echo 'Message has been sent';
}