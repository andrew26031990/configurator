<?php
require 'PHPMailerAutoload.php';

$mail = new PHPMailer;

//$mail->SMTPDebug = 3;                               // �������� ��������� ����� �������

$mail->isSMTP();                                      // ��������� ��� ���������� SMTP
$mail->Host = 'mail.ahost.uz';  // ������� SMTP ������
$mail->SMTPAuth = true;                               // ��������� �������� ����������� SMTP
$mail->Username = 'user@example.com';                 // ����� ��������� �����
$mail->Password = 'secret';                           // ������ 
$mail->SMTPSecure = 'ssl';                            // ��������� ����� ����������� ���������� TLS ��� SSL � ����� ������ SSL
$mail->Port = 465;                                    // ���� ��� SSL - 465, TLS 587.

$mail->setFrom('from@example.com', 'Mailer');
$mail->addAddress('joe@example.net', 'Joe User');     // �������� ����������
$mail->addAddress('ellen@example.com');               // ������ ��� �������.
$mail->addReplyTo('info@example.com', 'Information');
$mail->addCC('cc@example.com');
$mail->addBCC('bcc@example.com');

$mail->addAttachment('/var/tmp/file.tar.gz');         // ������� �������� ���� ���, �� ������ ������������
$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // ���� �����
$mail->isHTML(true);                                  // ��������� ���������� HTML

$mail->Subject = 'Here is the subject';
$mail->Body    = 'This is the HTML message body <b>in bold!</b>';
$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

if(!$mail->send()) {
    echo 'Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    echo 'Message has been sent';
}