<?php
require_once('../TCPDF-main/TCPDF-main/tcpdf.php');

// Setup
$pdf = new TCPDF('L', PDF_UNIT, 'A4', true, 'UTF-8', false);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetMargins(15, 15, 15);
$pdf->AddPage();

// Set test sentence (includes special chars and emoji)
$test_sentence = "The quick brown fox jumps over the lazy dog. Straße, Übergröße, Emoji: 🙂";


$pdf->setFont($font_family = 'vectoraltstdroman', $font_variant = '', $font_size = 10);
$pdf->Cell(0, 10, "Font: $font_family", 0, 1);
$pdf->MultiCell(0, 10, $test_sentence . " " . $font_family, 0, 'L', false, 1);
$pdf->Ln(4);

$pdf->setFont($font_family = 'vectoraltstdlighti', $font_variant = '', $font_size = 10);
$pdf->Cell(0, 10, "Font: $font_family$font_family", 0, 1);
$pdf->MultiCell(0, 10, $test_sentence . " " . $font_family, 0, 'L', false, 1);
$pdf->Ln(4);

$pdf->setFont($font_family = 'vectoraltstdlight', $font_variant = '', $font_size = 10);
$pdf->Cell(0, 10, "Font: $font_family", 0, 1);
$pdf->MultiCell(0, 10, $test_sentence . " " . $font_family, 0, 'L', false, 1);
$pdf->Ln(4);

$pdf->setFont($font_family = 'vectoraltstdi', $font_variant = '', $font_size = 10);
$pdf->Cell(0, 10, "Font: $font_family", 0, 1);
$pdf->MultiCell(0, 10, $test_sentence . " " . $font_family, 0, 'L', false, 1);
$pdf->Ln(4);

$pdf->setFont($font_family = 'vectoraltstdbi', $font_variant = '', $font_size = 10);
$pdf->Cell(0, 10, "Font: $font_family", 0, 1);
$pdf->MultiCell(0, 10, $test_sentence . " " . $font_family, 0, 'L', false, 1);
$pdf->Ln(4);

$pdf->setFont($font_family = 'vectoraltstdb', $font_variant = '', $font_size = 10);
$pdf->Cell(0, 10, "Font: $font_family", 0, 1);
$pdf->MultiCell(0, 10, $test_sentence . " " . $font_family, 0, 'L', false, 1);
$pdf->Ln(4);

$pdf->setFont($font_family = 'vectoraltstdblacki', $font_variant = '', $font_size = 10);
$pdf->Cell(0, 10, "Font: $font_family", 0, 1);
$pdf->MultiCell(0, 10, $test_sentence . " " . $font_family, 0, 'L', false, 1);
$pdf->Ln(4);

$pdf->setFont($font_family = 'vectoraltstdblack', $font_variant = '', $font_size = 10);
$pdf->Cell(0, 10, "Font: $font_family", 0, 1);
$pdf->MultiCell(0, 10, $test_sentence . " " . $font_family, 0, 'L', false, 1);
$pdf->Ln(4);


// Detect font directory (K_PATH_FONTS is the constant TCPDF uses)
$fonts_dir = defined('K_PATH_FONTS') ? K_PATH_FONTS : '../TCPDF-main/TCPDF-main/fonts/';

foreach (glob($fonts_dir . "*.php") as $font_file) {
    $basename = basename($font_file, '.php');
    // Ignore non-usable fonts or helpers if needed
    if (in_array($basename, ['uni2cid_ac15', 'uni2cid_ag15', 'uni2cid_aj16', 'uni2cid_ak12', 'index', 'cid0cs', 'cid0ct', 'cid0jp', 'cid0kr', 'cid0sg', 'cid0zh'])) continue;

    // Use font
    $pdf->SetFont($basename, '', 14);
    $pdf->Cell(0, 10, "Font: $basename", 0, 1);
    $pdf->MultiCell(0, 10, $test_sentence, 0, 'L', false, 1);
    $pdf->Ln(4);
}



$pdf->Output('all_fonts_test.pdf', 'I');

?>
