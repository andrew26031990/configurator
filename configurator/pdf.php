<?php

header('Content-type: text/plain; charset=utf-8');
//setlocale('LC_ALL', 'uz');
require_once( "tfpdf/tfpdf.php" );

$pdf = new tFPDF( 'P','pt','Letter' );
$pdf->AddPage();
$pdf->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);
$pdf->SetFont('DejaVu','',14);
$pdf->SetX(80);
$pdf->SetY(80);
$pdf->SetAutoPageBreak(true, 40);
$pdf->SetDrawColor(260, 30, 35);
$pdf->SetTextColor(150, 30, 35);
$logoXPos = 0;
$logoYPos = 0;
$logoWidth = 612;
$fmt = new NumberFormatter( 'uz_UZ', NumberFormatter::CURRENCY ); //ext-intl
$pdf->Image( $_SERVER['DOCUMENT_ROOT'].'/configurator/images/pdf.jpg', $logoXPos, $logoYPos, $logoWidth );
$pdf->SetTitle("Config");
$sum = 0;
foreach($_GET['list'] as $row)
{
    if(strlen($row['label']) > 49){
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(400,20,substr($row['label'].'('.$row['quantity'].')', 0, 55).'...',1,0,'L',false);
    }else{
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(400,20,$row['label'].'('.$row['quantity'].')',1,0,'L',false);
    }
    $pdf->Cell(150,20,$fmt->formatCurrency($row['price'], "UZS"),1,0,'R',false);
    $sum += intval($row['price']);
    $pdf->Ln();
}

$pdf->SetY(700);
$pdf->SetFont('DejaVu','',20);
$pdf->Cell(0,10,'ИТОГО: '.$fmt->formatCurrency($sum, "UZS"));

$pdf->Output( "D", "config.pdf" );
