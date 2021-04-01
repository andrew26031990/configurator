<?php
include '../functions.php';
require '../mail/PHPMailerAutoload.php';
setlocale(LC_MONETARY, 'uz_UZ');// money_format('%i ', $_POST['amount']);
header('Content-Type: text/html; charset=utf-8');

$name = '';
$phone = '';
$config = '';
$amount = '';
$sborka = '';
$sborkaLink = $_POST['sborkaLink'];
$email = '';
$email = $_POST['email'];

if(isset($_POST['name']) && isset($_POST['phone']) && isset($_POST['config']) && isset($_POST['amount'])){
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $config = $_POST['config'];
    $sborka = $_POST['sborka'];
    $amount = $_POST['amount'];// money_format('%i ', $_POST['amount']);
}else{
    $name = 'no_name';
    $phone = 'phone';
}

$config = str_replace('p', 'dt', $config);
$config = str_replace('li', 'dd', $config);
$amount = substr($amount,0,3).''.substr($amount,3,strlen($amount));

$mail = new PHPMailer;

//$mail->SMTPDebug = 3;                               // Включить подробный вывод отладки

$mail->isSMTP();                                       // Указываем что используем SMTP
$mail->Host = 'smtp.yandex.ru';               //  Адрес вашего SMTP Сервера был отправлен вместе с доступами к cPanel
$mail->SMTPAuth = true;                          // Включение проверки подлинности SMTP
$mail->Username = 'rabatavictor@yandex.ru';                 // Логин почтового ящика
$mail->Password = '5850058R';                           // Пароль 
$mail->SMTPSecure = 'ssl';                            // Указываем какое подключение используем TLS или SSL в нашем случае SSL
$mail->Port = 465;                                    // Порт для SSL - 465, TLS 587.

$mail->setFrom('rabatavictor@yandex.ru', 'Конфигуратор pcmarket.uz');
$mail->addAddress('sale@pcmarket.uz');
//$mail->addAddress('mandrew3601@gmail.com'); 
//$mail->addAddress('viktor.batsatsenko@yandex.ru'); // Добавить получателяviktor.batsatsenko@yandex.ru
$mail->addAddress($email); 

              // Дальше все понятно.
//$mail->addReplyTo('pcmarket@pcmarket.uz', 'Information');
//$mail->addCC('cc@example.com');
//$mail->addBCC('bcc@example.com');

//$mail->addAttachment('/var/tmp/file.tar.gz');         // Добавим вложения если нет, то просто комментируем
//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Тоже самое
$mail->isHTML(true);                                  // Разрешаем передавать HTML
$mail->CharSet = 'UTF-8';                          // Разрешаем символы на кириллице


$mail->Subject = 'Конфигурация';
$mail->Body    = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">


<html xmlns="http://www.w3.org/1999/xhtml>
    <head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        
        
		<title>HTML шаблон письма</title>
        
		<style type="text/css">
        @media only screen and (min-device-width: 601px) {.content {width: 600px !important;}}
		body[yahoo] .class {}
		.button {text-align: center; font-size: 18px; font-family: sans-serif; font-weight: bold; padding: 0 30px 0 30px;}
		.button a {color: #ffffff!important; text-decoration: none;}
		.button a:hover {text-decoration: underline;}

		@media only screen and (max-width: 550px), screen and (max-device-width: 550px) {}
		body[yahoo] .buttonwrapper {background-color: transparent!important;}
		body[yahoo] .button a {background-color: #e05443; padding: 15px 15px 13px!important; display: block!important;}
                dl{
    margin-top:0;
    margin-bottom:20px
}
dt,dd{
    line-height:1.42857143
}
dt{
    font-weight:700
}
dd{
    margin-left:0
}
            @media (min-width:768px){
    .dl-horizontal dt{
        float:left;
        width:160px;
        clear:left;
        text-align:right;
        overflow:hidden;
        text-overflow:ellipsis;
        white-space:nowrap
    }
    .dl-horizontal dd{
        margin-left:180px
    }
}
        </style>
    </head>
	
    <body yahoo bgcolor="#f6f8f1" style="margin: 0; padding: 0; min-width: 100%; background-color: #f6f8f1;">
     <!--[if (gte mso 9)|(IE)]>
	<table width="600" align="center" cellpadding="0" cellspacing="0" border="0">
    <tr> <td><![endif]-->
	<table class="content" align="center" cellpadding="0" cellspacing="0" border="0" style="width: 100%; max-width: 600px;">
					<!--Header-->
                        <tr>                         
                             <td bgcolor="#252525" style="padding: 40px 30px 20px 30px;">
							   
							   <!--LOGO-->
							   <table width="95" align="left" border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td height="70" style="padding: 0 20px 20px 0;">
								
								<!--ТУТ ССЫЛКА НА ЛОГО-->
											<img src="http://victors90.beget.tech/wp-content/themes/PC/images/logo.png" width="100" border="0" alt="" / >
										</td>
									</tr>
								</table><!--END-LOGO-->
								
								<!--Заглавие-->
								<!--[if (gte mso 9)|(IE)]>
								<table width="425" align="left" cellpadding="0" cellspacing="0" border="0">
									<tr>
										<td>
										<![endif]-->
           
								 <table class="col425" align="left" border="0" cellpadding="0" style="width: 100%; max-width: 400px;">
									<tr>
										<td height="70">
												<table width="100%" border="0" cellspacing="0">
												<tr>
													<td style="padding: 0 0 0 3px; font-size: 20px; color: #ffffff; font-family: sans-serif; letter-spacing: 5px; font-weight: bold;">
													<a href="http://pcmarket.uz">PCMARKET.UZ</a>
													</td>
												</tr>
												<tr>
													<td class="h1" style="padding: 5px 0 0 0; font-size: 33px; line-height: 38px; font-weight: bold; color: red; font-family: sans-serif;">
													Автоматическое письмо отправленное с конфигуратора PCMARKET
													</td>
												</tr>
											</table>
										</td>
									</tr>
								</table>
							<!--[if (gte mso 4)|(IE)]>
								</td>
							</tr>
							</table>
							<![endif]--><!--END-ЗАГЛАВИЕ-->

							</td>
						</tr>


					<!--ТЕЛО ПИСЬМА-->
						<tr>
							<td class="content" bgcolor="#ffffff" style="width: 100%; max-width: 600px; padding: 30px 30px 30px 30px; border-bottom: 1px solid #f2eeed;">
  
							  Имя клиента: '.$name.'.<br>Email клиента: '.$email.'<br>Телефон клиента: '.$phone.'.<br>Сборка: '.$sborka.'.<br><br>Запрашиваемая конфигурация:<br><br><br><dl class="dl-horizontal">'.$config.'</dl><br>Общая сумма:<br>'.$amount.'
							  <br><br>
							  Сохраненная сборка: '.$sborkaLink.'
                            </td>
						 </tr>	
					
					
					<!--Footer-->
         				<tr>
						 <td class="footer" bgcolor="#44525f" style="padding: 20px 30px 15px 30px;">
						 
						            <iframe src="https://bank.uz/iframe_converter" style="width: 320px; height: 65px;"></iframe>
									<table width="100%" border="0" cellspacing="0" cellpadding="0">
										<tr>
											<td align="center" style="font-family: sans-serif; font-size: 14px; color: #ffffff;">
												&reg;Все права защищены<br/>
												<a href="http://pribylwm.ru" style="color: #ffffff; text-decoration: underline;"target="_blank">Адрес вашего сайта</a>
												</td>
										</tr>
										<tr>
											<td align="center" style="padding: 20px 0 0 0;">
												<table border="0" cellspacing="0" cellpadding="0">
													<tr>
														<td width="37" style="text-align: center; padding: 0 10px 0 10px;">
															<a href="https://www.facebook.com/groups/1065415996857887/"target="_blank">
																<img src="http://pribylwm.ru/images/fb1.png" width="37" height="37" alt="Facebook" border="0" />
															</a>
														</td>
														<td width="37" style="text-align: center; padding: 0 10px 0 10px;">
															<a href="https://www.youtube.com/channel/UCJUI_PV1d-xVK6ee_VznKOQ"target="_blank">
																<img src="http://pribylwm.ru/images/youtube.png" width="37" height="37" alt="Facebook" border="0" />
															</a>
														</td>
														<td width="37" style="text-align: center; padding: 0 10px 0 10px;">
															<a href="https://twitter.com/pribylwm"target="_blank">
																<img src="http://pribylwm.ru/images/twitter.png" width="37" height="37" alt="Facebook" border="0" />
															</a>
														</td>	
														<td width="37" style="text-align: center; padding: 0 10px 0 10px;">
															<a href="http://vk.com/wpzarabotok"target="_blank">
																<img src="http://pribylwm.ru/images/vk1.png" width="37" height="37" alt="VK" border="0" />
															</a>
														</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
                <!--[if (gte mso 9)|(IE)]>
        </td>
    </tr>
</table>
<![endif]-->
    </body>
</html>';  //'Имя заказчика: '.$name.'.<br>Телефон заказчика: '.$phone.'.<br>Запрашиваемая конфигурация:<br>'.$config.'.<br>Общая сумма:<br>'.$amount;
$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

if(!$mail->send()) {
    echo 'Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    echo 'Ваша заявка принята. В ближайшее время с вами свяжутся сотрудники компании PCMARKET';
    include_once 'messages_to_telegram.php';
    SendMailToBot('Имя клиента: '.$name.'; '.'Email клиента : '.$email.'; '.'Цена: '.$amount.'; '.'Сборка: '.$sborka.$config.'; ');
}
