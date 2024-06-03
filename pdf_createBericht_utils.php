<?php

// V30-11-23 13:50 // Für A4 
// TODO: QuasiStationär bei strahlenanw. 
//require_once('TCPDF-master/TCPDF-master/tcpdf.php'); //called within TCPDF class 

function raum_header($pdf,$ln_spacer2, $Raumbezeichnung, $Raumnr, $RaumbereichNutzer, $Geschoss, $Bauetappe, $Bauabschnitt){
    
    $pdf->SetFont('helvetica', 'B', 10);
        $pdf->MultiCell(100, 6, "Raum: " .$Raumbezeichnung, 0, 'L', 0, 0);
        $pdf->MultiCell(80, 6, "Nummer: " .$Raumnr, 0, 'L', 0, 0);
        $pdf->Ln($ln_spacer2);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(100, 6, "Bereich: " .$RaumbereichNutzer, 0, 'L', 0, 0);
        $pdf->MultiCell(80, 6, "Geschoss: " .$Geschoss, 0, 'L', 0, 0);
        $pdf->Ln($ln_spacer2);
        $pdf->MultiCell(100, 6, "Bauetappe: " .$Bauetappe, 'B', 'L', 0, 0);
        $pdf->MultiCell(80, 6, "Bauteil: " . $Bauabschnitt, 'B', 'L', 0, 1);
}

function init_pdf_attributes($pdf, $einzugLR, $marginTop) {
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('LIMET Consulting und Planung ZT GmbH');
    $pdf->SetTitle('Raumbuch');
    $pdf->SetSubject('Raumbuch');
    $pdf->SetKeywords('Raumbuch');
    $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING, array(0, 64, 255), array(0, 64, 128));
    $pdf->setFooterData(array(0, 64, 0), array(0, 64, 128));
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    $pdf->SetMargins($einzugLR, $marginTop, $einzugLR);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) { // set some language-dependent strings (optional)
        require_once(dirname(__FILE__) . '/lang/eng.php');
        $pdf->setLanguageArray($l);
    }
    $pdf->SetFont('helvetica', '', 10);
    $pdf->AddPage('P', 'A4');
    return $pdf;
}

function is_not_no_comment($str) {
    if ($str == "keine Anmerkung" || $str == "keine Angaben" || $str == "") {
        return false;
    } else {
        return true;
    }
}

function format_text($string) {
    $spacer = "; ";
    $return = str_replace("\n", $spacer, $string);
    return $return;
}

function clean_string($dirty_str) {
    $clean_string = preg_replace('/[^äüö\n(\x20-\x7F)]*/u', '', $dirty_str);
    return $clean_string;
}

function newpage_or_spacer($pdf, $next_block_size) {
    $y = $pdf->GetY();
    if (($y + $next_block_size) >= 270) {
        $pdf->AddPage();
    } else {
        if ($y < 20) {
            
        } // header size
        else {
            block_spacer($pdf);
        }
    }
}

function block_spacer($pdf) {
    $pdf->Ln(8);
}

function check_4_new_page($pdf, $height) {
    $y = $pdf->GetY();     // Wenn Seitenende? Überprüfen und neue Seite anfangen
    if (($y + $height) >= 270) {
        $pdf->AddPage();
    }
}

function dashed_line($pdf, $offset) {
    $pdf->SetLineStyle(array('dash' => 2, 'color' => array(0, 0, 0)));
    $y = $pdf->GetY() + $offset;
    $pdf->Line(25, $y, 185, $y);
    $pdf->SetLineStyle(array('dash' => 0, 'color' => array(0, 0, 0)));
}

function translateBestand($value) {
    return ($value == 0) ? 'Ja' : 'Nein';
}

function multicell_with_stk($pdf, $NR, $einzug) {
    if ($NR > 0) {
        $pdf->MultiCell($einzug, 6, $NR . " Stk", 0, 'L', 0, 0);
    } else {
        $pdf->MultiCell($einzug, 6, " - ", 0, 'L', 0, 0);
    }
}

function multicell_with_nr($pdf, $NR, $unit, $schriftgr, $einzug) {
    $originalFontSize = $pdf->getFontSizePt();
    if ($NR > 0) {
        $pdf->MultiCell($einzug, $schriftgr, $NR . " " . $unit, 0, 'L', 0, 0);
    } else {
        $pdf->MultiCell($einzug, $schriftgr, " - ", 0, 'L', 0, 0);
    }
    $pdf->SetFontSize($originalFontSize);
}

function multicell_with_str($pdf, $STR, $einzug, $Unit) {
    $originalFontSize = $pdf->getFontSizePt();
    if (strlen($STR) > 0) {
        $pdf->MultiCell($einzug, 6, $STR . " " . $Unit, 0, 'L', 0, 0);
    } else {
        $pdf->MultiCell($einzug, 6, " - ", 0, 'L', 0, 0);
    }
    $pdf->SetFontSize($originalFontSize);
}

function strahlenanw($pdf, $param, $cellsize, $gr) {
    $originalFontSize = $pdf->getFontSizePt();
    $pdf->SetFont('zapfdingbats', '', $gr);
    if ($param === '0') {
        $pdf->SetTextColor(255, 0, 0);
        $pdf->MultiCell($cellsize, 6, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
    } else {
        if ($param === '1') {
            $pdf->SetTextColor(0, 255, 0);
            $pdf->MultiCell($cellsize, 6, TCPDF_FONTS::unichr(52), 0, 'L', 0, 0);
        } else {
            $pdf->SetFont('helvetica', '', 10);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->MultiCell($cellsize, 6, "Quasi stationär", 0, 'L', 0, 0);
            $pdf->Ln(1);
        }
    }
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('helvetica', '', $originalFontSize);
}

function hackerl($pdf, $hackerl_schriftgr, $zellgr, $param, $comp_true) {
    $originalFontSize = $pdf->getFontSizePt();
    $hackerlcellgröße = $zellgr; //same as global var
    $pdf->SetFont('zapfdingbats', '', $hackerl_schriftgr);
    if ($param == $comp_true || $param == "Ja" || $param == "ja" || 1 == $param || "1" === $param) {
        $pdf->SetTextColor(0, 255, 0);
        $pdf->MultiCell($hackerlcellgröße, $hackerl_schriftgr, TCPDF_FONTS::unichr(52), 0, 'L', 0, 0);
    } else if (false) {
        
    } else {
        $pdf->SetTextColor(255, 0, 0);
        $pdf->MultiCell($hackerlcellgröße, $hackerl_schriftgr, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
    }
    $pdf->SetFont('helvetica', '', $originalFontSize);
    $pdf->SetTextColor(0, 0, 0);
}

// STrahlenanwendung: else{$pdf->MultiCell(40, 6, "Quasi stationär",0, 'L', 0, 0);}

function block_label($pdf, $block_label, $blocksize = 12) {
    $pdf->SetFont('helvetica', 'B', $blocksize*2/3);
    $pdf->MultiCell(180, 1, "", 'T', 'C', 0, 0);
    $pdf->Ln(1);
    $pdf->MultiCell(180,  $blocksize-1 , $block_label, 0, 'C', 0, 1);
    //$x = $pdf->GetX();
    //$y = $pdf->GetY();
    //$style = array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => '4,2', 'color' => array(0, 0, 0));
    //$pdf->Line($x, $y , $x + $pdf->GetStringWidth($content), $y , $style);
    $pdf->SetFont('helvetica', '', 10);
}

function block_label_sidew($pdf, $block_label, $block_height) {
    $pdf->Ln(6);
    $startX = $pdf->GetX();
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->StartTransform();
    $pdf->Rotate(90);
    $pdf->MultiCell($block_height, 10, $block_label, 0, 'L', 0, 0, $startX - $block_height);
    $pdf->StopTransform();
    $pdf->Ln(6);
    $pdf->SetFont('helvetica', '', 10);
}
