<?php
/**
 * HTML-шаблон письма с заявкой из конфигуратора.
 *
 * Подключается из mail/mailer.php через render_mail_template().
 * Доступные переменные: $name, $email, $phone, $sborka, $config,
 * $amount, $sborkaLink, а также $shopPhone и $shopEmail для подвала
 * (подставляются из settings.ini).
 *
 * $config приходит уже пропущенным через strip_tags() и содержит
 * разметку <dt>/<dd>, поэтому выводится без экранирования; все
 * остальные поля экранируются.
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">


<html xmlns="http://www.w3.org/1999/xhtml">
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
											<img src="https://configurator.pcmarket.uz/configurator/images/logo.png" width="100" border="0" alt="" / >
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
  
							  Имя клиента: <?= e($name); ?>.<br>Email клиента: <?= e($email); ?><br>Телефон клиента: <?= e($phone); ?>.<br>Сборка: <?= e($sborka); ?>.<br><br>Запрашиваемая конфигурация:<br><br><br><dl class="dl-horizontal"><?= $config; ?></dl><br>Общая сумма:<br><?= e($amount); ?>
							  <br><br>
							  Сохраненная сборка: <?= e_url($sborkaLink); ?>
                            </td>
						 </tr>	
					
					<!--Footer-->
					<tr>
						<td class="footer" bgcolor="#44525f" style="padding: 20px 30px 15px 30px;">
							<table width="100%" border="0" cellspacing="0" cellpadding="0">
								<tr>
									<td align="center" style="font-family: sans-serif; font-size: 14px; color: #ffffff;">
										&reg; PCMARKET<br/>
										<a href="https://pcmarket.uz" style="color: #ffffff; text-decoration: underline;" target="_blank">pcmarket.uz</a>
<?php if (!empty($shopPhone)) { ?>
										<br/><?= e($shopPhone); ?>
<?php } ?>
<?php if (!empty($shopEmail)) { ?>
										<br/><a href="mailto:<?= e($shopEmail); ?>" style="color: #ffffff; text-decoration: underline;"><?= e($shopEmail); ?></a>
<?php } ?>
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
</html>
