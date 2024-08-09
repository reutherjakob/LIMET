<?php

session_start();
require_once('TCPDF-master/TCPDF-master/tcpdf.php');

class MYPDF extends TCPDF {

    public function Header() {
        if ($this->numpages > 1) {
            $image_file = 'LIMET_web.png';
            $this->Image($image_file, 15, 5, 20, 10, 'PNG', '', 'M', false, 300, '', false, false, 0, false, false, false);
            $this->SetFont('helvetica', '', 8);
            $this->Cell(0, 0, 'Großgeräte Parameter Einbringung', 0, false, 'R', 0, '', 0, false, 'B', 'B');
            $this->Ln();
            $this->cell(0, 0, '', 'B', 0, 'L');
        } else { // Titelblatt
            $Einzug = 10;
            $this->SetFont('helvetica', 'B', 15);
            $this->SetY(60);
            $this->Cell($Einzug, 0, "", 0, false, 'L', 0, '', 0, false, 'B', 'B');
            $this->Cell(0, 0, "KHI", 0, false, 'L', 0, '', 0, false, 'B', 'B');
            $this->Ln();
            $this->Cell($Einzug, 0, "", 0, false, 'L', 0, '', 0, false, 'B', 'B');
            $this->Cell(0, 0, "Vorentwurf ", 0, false, 'L', 0, '', 0, false, 'B', 'B');
            $this->Ln(100);
            $this->Cell($Einzug, 0, "", 0, false, 'L', 0, '', 0, false, 'B', 'B');
            $this->Cell(0, 0, 'Anforderung für die Einbringung' . "", 0, false, 'L', 0, '', 0, false, 'B', 'B');
            $this->Ln();
            $this->Cell($Einzug, 0, "", 0, false, 'L', 0, '', 0, false, 'B', 'B');
            $this->Cell(0, 0, "von medizinischen Großgeräten", 0, false, 'L', 0, '', 0, false, 'B', 'B');
            $this->Ln(30);

            $this->SetFont('helvetica', '', 9);
            $Disclaimer_txt = "Die beschriebenen Komponenten weisen die jeweils größten Abmessungen "
                    . "und/oder Gewichtslasten je Anlage auf. Die vollständigen Systeme bestehen aus"
                    . " mehreren, hier nicht angeführten Elementen, die jedoch kleiner und/oder leichter als"
                    . " das größte bzw. schwerste Einzelteil sind. Die angegebenen Werte sind produktneutrale"
                    . " Maximalspezifikationen, was bedeutet, dass beispielsweise das Gewicht von Leitfabrikat "
                    . "A und die Abmessungen von Leitfabrikat B verwendet wurden. Die hier angeführten Parameter"
                    . " dienen exklusiv der Bestimmung der Einbringwege.";

            $this->SetY(280 - ($this->getStringHeight(180, $Disclaimer_txt, 0, false, 'L', 0, '', 0, false, '', '')));
            $this->Multicell(180, 0, $Disclaimer_txt, 0, 'L', 0, 0);
            $this->SetFont('helvetica', '', 6);
            $image_file = 'LIMET_web.png';
            $this->Image($image_file, 150, 40, 30, 15, 'PNG', '', 'M', false, 300, '', false, false, 0, false, false, false);
        }
    }

    public function Footer() {  // Page footer
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->cell(0, 0, '', 'T', 0, 'L');
        $this->Ln();
        $tDate = date('Y-m-d');
        $this->Cell(0, 0, $tDate, 0, false, 'L', 0, '', 0, false, 'T', 'M');
        $this->Cell(0, 0, 'Seite ' . $this->getAliasNumPage() . ' von ' . $this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
    }
}
