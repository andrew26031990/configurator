<?php
/**
 * Генерация PDF со спецификацией сборки.
 * Публичный эндпоинт: список позиций приходит из браузера в $_GET['list'].
 */

require_once __DIR__ . '/../security.php';

/*
 * tFPDF образца 2015 года пользуется utf8_encode() и динамическими
 * свойствами — в PHP 8.2+ это Deprecated. При включённом display_errors
 * такие уведомления печатаются раньше самого PDF, и файл не отдаётся:
 * "Some data has already been output, can't send PDF file".
 *
 * Поэтому глушим только E_DEPRECATED и заворачиваем генерацию в буфер,
 * который отбрасываем перед выдачей файла.
 */
error_reporting(error_reporting() & ~E_DEPRECATED);

require_once __DIR__ . '/tfpdf/tfpdf.php';

$outputLevel = ob_get_level();
ob_start();

/** Больше позиций в одной сборке быть не может. */
const PDF_MAX_ROWS = 200;

$list = isset($_GET['list']) && is_array($_GET['list']) ? $_GET['list'] : array();

if (!$list) {
    http_response_code(400);
    header('Content-Type: text/plain; charset=UTF-8');
    exit('Список комплектующих пуст');
}

$list = array_slice($list, 0, PDF_MAX_ROWS);

/**
 * Форматирование суммы. ext-intl есть не на каждом хостинге,
 * поэтому предусмотрен запасной вариант.
 *
 * @param  float $amount
 * @return string
 */
function formatSum($amount)
{
    static $formatter = null;

    if ($formatter === null) {
        $formatter = class_exists('NumberFormatter')
            ? new NumberFormatter('uz_UZ', NumberFormatter::CURRENCY)
            : false;
    }

    if ($formatter instanceof NumberFormatter) {
        return $formatter->formatCurrency($amount, 'UZS');
    }

    return number_format($amount, 0, '.', ' ') . ' UZS';
}

$pdf = new tFPDF('P', 'pt', 'Letter');
$pdf->AddPage();
$pdf->AddFont('DejaVu', '', 'DejaVuSansCondensed.ttf', true);
$pdf->SetFont('DejaVu', '', 14);
$pdf->SetX(80);
$pdf->SetY(80);
$pdf->SetAutoPageBreak(true, 40);
$pdf->SetDrawColor(260, 30, 35);
$pdf->SetTextColor(150, 30, 35);
$pdf->SetTitle('Config');

$pdf->Image(__DIR__ . '/images/pdf.jpg', 0, 0, 612);

$sum = 0;

foreach ($list as $row) {
    if (!is_array($row)) {
        continue;
    }

    $label    = isset($row['label']) && is_scalar($row['label']) ? (string) $row['label'] : '';
    $quantity = isset($row['quantity']) ? (int) $row['quantity'] : 1;
    $price    = isset($row['price']) ? (float) $row['price'] : 0;

    if ($label === '') {
        continue;
    }

    // mb_substr вместо substr: обрезка по байтам ломала кириллицу.
    $label = mb_substr($label, 0, 55, 'UTF-8');
    if (mb_strlen($label, 'UTF-8') === 55) {
        $label .= '...';
    }

    $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell(400, 20, $label . '(' . $quantity . ')', 1, 0, 'L', false);
    $pdf->Cell(150, 20, formatSum($price), 1, 0, 'R', false);

    $sum += $price;

    $pdf->Ln();
}

$pdf->SetY(700);
$pdf->SetFont('DejaVu', '', 20);
$pdf->Cell(0, 10, 'ИТОГО: ' . formatSum($sum));

// Всё, что успело попасть в вывод при сборке PDF, отбрасываем: иначе
// FPDF откажется отдавать файл.
while (ob_get_level() > $outputLevel) {
    ob_end_clean();
}

$pdf->Output('D', 'config.pdf');
