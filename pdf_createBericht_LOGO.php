<?php
function get_header_logo($pdf)
{
    //if ($pdf->numpages > 1) {
    if ($_SESSION["projectAusfuehrung"] === "LIMET-MADER") {
        $image_file = 'LIMET_web.png';
        $pdf->Image($image_file, 17, 4, 20, 10, 'PNG', '', 'M', false, 300, '', false, false, 0, false, false, false);
        //$image_file = 'Mader_Logo_neu.jpg';
        //$pdf->Image($image_file, 38, 5, 40, 10, 'JPG', '', 'M', false, 300, '', false, false, 0, false, false, false);
        $image_file = 'MADER_Logo.png';
        $pdf->Image($image_file, 38, 4, 14, 10, 'PNG', '', 'M', false, 300, '', false, false, 0, false, false, false);

    } else if ($_SESSION["projectAusfuehrung"] === "LIMET") {
        $image_file = 'LIMET_web.png';
        $pdf->Image($image_file, 15, 5, 20, 10, 'PNG', '', 'M', false, 300, '', false, false, 0, false, false, false);
    } else if ($_SESSION["projectAusfuehrung"] === "LIMET-CFM") {
        $image_file = 'ARGE_LIMET-CFM_Logo_03.png';
        $pdf->Image($image_file, 15, 5, 30, 10, 'PNG', '', 'M', false, 300, '', false, false, 0, false, false, false);
    }
}

function get_titelblatt_logo($pdf, $format = "A4")
{
    $spacer = 0;
    if ($format === "A3") {
        $spacer = 210;
    }
    if ($_SESSION["projectAusfuehrung"] === "LIMET-MADER") {
        $image_file = 'LIMET_web.png';
        $pdf->Image($image_file, 145 + $spacer, 40, 30, 13, 'PNG', '', 'M', false, 300, '', false, false, 0, false, false, false);
        $image_file = 'MADER_Logo.png';
        $pdf->Image($image_file, 178 + $spacer, 42, 15, 13, 'PNG', '', 'M', false, 300, '', false, false, 0, false, false, false);
        $pdf->SetY(60);
        $pdf->SetX(110);
        $pdf->SetFont('helvetica', '', 6);
        $pdf->Cell(0, 0, "ARGE LIMET-MADER", 0, false, 'R', 0, '', 0, false, 'B', 'B');
        $pdf->Ln();
        $pdf->Cell(0, 0, "Zwerggase 6/1", 0, false, 'R', 0, '', 0, false, 'B', 'B');
        $pdf->Ln();
        $pdf->Cell(0, 0, "8010 Graz", 0, false, 'R', 0, '', 0, false, 'B', 'B');
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Cell(0, 0, "Dipl.-Ing. Jens Liebmann, MBA", 0, false, 'R', 0, '', 0, false, 'B', 'B');
        $pdf->Ln();
        $pdf->Cell(0, 0, "Tel: +43 1 470 48 33", 0, false, 'R', 0, '', 0, false, 'B', 'B');
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Cell(0, 0, "Dipl.-Ing. Peter Mader", 0, false, 'R', 0, '', 0, false, 'B', 'B');
        $pdf->Ln();
        $pdf->Cell(0, 0, "Tel: +43 650 523 27 38", 0, false, 'R', 0, '', 0, false, 'B', 'B');
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Cell(0, 0, "UID ATU 69334945", 0, false, 'R', 0, '', 0, false, 'B', 'B');
        $pdf->Ln();
        $pdf->Cell(0, 0, "IBAN AT90 2081 5208 0067 8128", 0, false, 'R', 0, '', 0, false, 'B', 'B');
        $pdf->Ln();
        $pdf->Cell(0, 0, "BIC STSPAT2GXXX", 0, false, 'R', 0, '', 0, false, 'B', 'B');
    } else if ($_SESSION["projectAusfuehrung"] === "LIMET-CFM") {
        $image_file = 'ARGE_LIMET-CFM_Logo_03.png';
        $pdf->Image($image_file, 130 + $spacer, 35, 60, 20, 'PNG', '', 'M', false, 300, '', false, false, 0, false, false, false);
    } else if ($_SESSION["projectAusfuehrung"] === "LIMET") {
        $image_file = 'LIMET_web.png';
        $pdf->Image($image_file, 150 + $spacer, 40, 30, 15, 'PNG', '', 'M', false, 300, '', false, false, 0, false, false, false);
    }


}