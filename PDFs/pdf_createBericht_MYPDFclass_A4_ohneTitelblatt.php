<?php
session_start();
require_once('TCPDF-main/TCPDF-main/tcpdf.php');
include "pdf_createBericht_LOGO.php";

class MYPDF extends TCPDF
{
    public function Header()
    {
        get_header_logo($this);
        $this->SetFont('helvetica', '', 8);
        $this->cell(0, 0, '', 0, 0, 'L');
        $this->Ln();
        if ($_SESSION["PDFTITEL"] != null) {
            $this->Cell(0, 0, $_SESSION["PDFTITEL"], 0, false, 'R', 0, '', 0, false, 'B', 'B');
        } else {
            $this->Cell(0, 0, '', 0, false, 'R', 0, '', 0, false, 'B', 'B');
        }
        $this->Ln(1);//
        if (!empty(str_replace(" ", "", $_SESSION["PDFHeaderSubtext"] ?? ""))) {
            $this->Cell(0, 0, $_SESSION["PDFHeaderSubtext"], 'B', false, 'R', 0, '', 0, false, 'B', 'B');
            $this->Ln();
        } else {
            $this->cell(0, 0, '', 'B', 0, 'L');
            $this->Ln();
        }
        $this->Ln();  $this->Ln();  $this->Ln();
       // if("Medizintechnische Elementliste"== $_SESSION["PDFTITEL"]) {
       //     $this->SetY($this->GetY() + 20);
       // }
    }

    // Page footer
    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->cell(0, 0, '', 'T', 0, 'L');
        $this->Ln();
        $tDate = date('Y-m-d');
        if (isset($_SESSION["PDFdatum"]) && $_SESSION["PDFdatum"] != null) {
            $this->Cell(0, 0, $_SESSION["PDFdatum"], 0, false, 'L', 0, '', 0, false, 'T', 'M');
        } else {
            $this->Cell(0, 0, $tDate, 0, false, 'L', 0, '', 0, false, 'T', 'M');
        }

        $this->Cell(0, 0, 'Seite ' . $this->getAliasNumPage() . ' von ' . $this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
    }
}