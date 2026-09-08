<?php
/**
 * Запуск: php tests/mail_test.php
 *
 * Проверка рендера письма и настройки PHPMailer БЕЗ реальной отправки.
 */

require_once __DIR__ . '/../mail/mailer.php';

ini_set('display_errors', '1');
error_reporting(E_ALL);

$pass = 0;
$fail = 0;

function check($label, $actual, $expected)
{
    global $pass, $fail;
    if ($actual === $expected) { $pass++; echo "  OK   $label\n"; return; }
    $fail++;
    echo "  FAIL $label — ожидалось " . var_export($expected, true)
        . ", получено " . var_export($actual, true) . "\n";
}

/* --- рендер шаблона --------------------------------------------------- */

$order = array(
    'name'       => 'Андрей <script>alert(1)</script>',
    'email'      => 'client@example.com',
    'phone'      => '+998 90 000-00-00',
    'sborka'     => 'Собрать ПК на Intel',
    'config'     => '<dt>Процессор</dt><dd>Core i5-12400</dd>',
    'amount'     => '12 500 000',
    'sborkaLink' => 'https://configurator.pcmarket.uz/?cat=play-profy&ozu=305(2)',
);

$html = render_mail_template('order', $order);

check('шаблон отрендерился',        $html !== '', true);
check('это HTML-письмо',            strpos($html, '<!DOCTYPE html') === 0, true);
check('нет остатков PHP',           strpos($html, '<?') !== false, false);
check('телефон подставлен',         strpos($html, '+998 90 000-00-00') !== false, true);
check('сумма подставлена',          strpos($html, '12 500 000') !== false, true);
check('сборка подставлена',         strpos($html, 'Собрать ПК на Intel') !== false, true);
check('конфигурация как разметка',  strpos($html, '<dd>Core i5-12400</dd>') !== false, true);
check('ссылка на сборку есть',      strpos($html, 'configurator.pcmarket.uz') !== false, true);

// XSS: имя клиента не должно попасть в письмо исполняемым тегом
check('скрипт в имени экранирован', strpos($html, '<script>alert(1)</script>') !== false, false);
check('имя видно как текст',        strpos($html, '&lt;script&gt;') !== false, true);

// javascript: в ссылке отсекается e_url()
$evil = $order;
$evil['sborkaLink'] = 'javascript:alert(1)';
$evilHtml = render_mail_template('order', $evil);
check('javascript: в ссылке отсечён', strpos($evilHtml, 'javascript:alert') !== false, false);

check('несуществующий шаблон → пусто', render_mail_template('нет-такого', $order), '');

/* --- отправка выключена ----------------------------------------------- */

check('mail_enabled() = false в локальном конфиге', mail_enabled(), false);
check('SendOrderMail при enabled=false не шлёт',    SendOrderMail($order), false);

/* --- настройка PHPMailer из конфига ----------------------------------- */

$m = make_mailer();
check('PHPMailer создан',    $m !== null, true);

if ($m !== null) {
    check('host из конфига',     $m->Host, (string) config_get('mail.host'));
    check('порт из конфига',     $m->Port, (int) config_get('mail.port'));
    check('шифрование',          $m->SMTPSecure, (string) config_get('mail.encryption'));
    check('логин из конфига',    $m->Username, (string) config_get('mail.username'));
    check('кодировка UTF-8',     $m->CharSet, 'UTF-8');
    check('SMTP-авторизация вкл', $m->SMTPAuth, true);
}

echo "\nразмер письма: " . strlen($html) . " байт\n";
echo 'PHP ' . PHP_VERSION . " — успешно: $pass, провалено: $fail\n";

exit($fail === 0 ? 0 : 1);
