<?php

// V30-11-23 13:50 // Für A4 
// TODO: QuasiStationär bei strahlenanw. 
//require_once('TCPDF-master/TCPDF-master/tcpdf.php'); //called within TCPDF class 
//            $marginTop = 17; // https://tcpdf.org/docs/srcdoc/TCPDF/files-config-tcpdf-config/ 
//            $SB = 210 - 2 * PDF_MARGIN_LEFT;  // A4: seitenbreite minus die lr einzüge = 180
//            $SH = 297-$marginTop-PDF_MARGIN_FOOTER;
//            $horizontalSpacerLN = 4;
//            $horizontalSpacerLN2 = 5;
//            $horizontalSpacerLN3 = 8;
//
//            // A: Seite Halbieren; B: Seite dritteln; C:Seite/4; E=1/5; F = 1/6
//            $e_A = $SB / 2;
//            $e_A_3rd = $e_A / 3;
//            $e_A_2_3rd = $e_A - $e_A_3rd;
//            $e_B = $SB / 3;
//            $e_B_3rd = $e_B / 3;
//            $e_B_2_3rd = $e_B - $e_B_3rd;
//            $e_C = $SB / 4;
//            $e_C_3rd = $e_C / 3;
//            $e_C_2_3rd = $e_C - $e_C_3rd;
//            $e_D = $SB / 5;
//            $e_D_3rd = $e_D / 3;
//            $e_D_2_3rd = $e_D - $e_D_3rd;
// 
//            $e_E = $SB / 6; //=30
//            $e_E_3rd = $e_E / 3;// = 10 
//            $e_E_2_3rd = $e_E - $e_E_3rd; //= 20
//
//            $hackerl_Zellgröße = $e_E_3rd; //=10
//            $hackerl_schriftgröße = $e_E_3rd;
//
//            $block_header_height = 10;   //
//            $blockSpaceNeededX = 100; //


function raum_header($pdf, $ln_spacer, $SB, $Raumbezeichnung, $Raumnr, $RaumbereichNutzer, $Geschoss, $Bauetappe, $Bauabschnitt, $format = "") {
    if ($format == "") {
        $qot = 5 / 9;
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->MultiCell($SB * $qot, $ln_spacer, "Raum: " . $Raumbezeichnung, 0, 'L', 0, 0);
        $pdf->MultiCell($SB * (1 - $qot), $ln_spacer, "Nummer: " . $Raumnr, 0, 'L', 0, 0); 
        $pdf->Ln($ln_spacer);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell($SB * $qot, $ln_spacer, "Bereich: " . $RaumbereichNutzer, 0, 'L', 0, 0);
        $pdf->MultiCell($SB * (1 - $qot), $ln_spacer, "Geschoss: " . $Geschoss, 0, 'L', 0, 0);
        $pdf->Ln($ln_spacer);
        $pdf->MultiCell($SB * $qot, $ln_spacer, "Bauetappe: " . $Bauetappe, 'B', 'L', 0, 0);
        $pdf->MultiCell($SB * (1 - $qot), $ln_spacer, "Bauteil: " . $Bauabschnitt, 'B', 'L', 0, 1);
    } else {// A3 Queetr
        $pdf->SetFont('helvetica', 'B', 10);
        $extra_space_underneath_header=2;
        $pdf->Ln($extra_space_underneath_header);
        $ln_spacer = $ln_spacer-$extra_space_underneath_header; 
        $qot = 1/6; 
        
        $incr=1;
        $Height = $pdf->getStringHeight($SB * $qot, "Raum: " . $Raumbezeichnung , false, true, '', 1);
        if($Height > $ln_spacer) {  
            $incr +=5; //           
             $Height = $pdf->getStringHeight($SB * $qot + $incr, "Raum: " . $Raumbezeichnung , false, true, '', 1);
            }
            
        $pdf->MultiCell($SB * $qot + $incr, $ln_spacer, "Raum: " . $Raumbezeichnung, 'B', 'L', 0, 0);
        $pdf->MultiCell($SB * ($qot) - $incr, $ln_spacer, "Nummer: " . $Raumnr, 'B', 'L', 0, 0);
        $pdf->MultiCell($SB * $qot, $ln_spacer, "Bereich: " . $RaumbereichNutzer, 'B', 'L', 0, 0);
        $pdf->MultiCell($SB * ($qot), $ln_spacer, "Geschoss: " . $Geschoss, 'B', 'L', 0, 0);
        $pdf->MultiCell($SB * $qot, $ln_spacer, "Bauetappe: " . $Bauetappe, 'B', 'L', 0, 0);
        $pdf->MultiCell($SB * ($qot), $ln_spacer, "Bauteil: " . $Bauabschnitt, 'B', 'L', 0, 1);
        $pdf->SetFont('helvetica', '', 10);
    }
}

function init_pdf_attributes($pdf, $einzugLR, $marginTop, $marginBTM, $format = "") {
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
    $pdf->SetHeaderMargin($marginTop);
    $pdf->SetFooterMargin($marginBTM); //10
    $pdf->SetAutoPageBreak(FALSE, PDF_MARGIN_BOTTOM);
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) { // set some language-dependent strings (optional)
        require_once(dirname(__FILE__) . '/lang/eng.php');
        $pdf->setLanguageArray($l);
    }
    $pdf->SetFont('helvetica', '', 10);
    if ($format == "A3") {
        $pdf->AddPage('L', 'A3');
    } else {
        $pdf->AddPage('P', 'A4');
    }
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
    $spacer = ". ";
    $string = preg_replace("/\s+\n/", "\n", $string); // Remove spaces before \n
    $return = str_replace("\n", $spacer, $string);
    return $return;
}


function clean_string($dirty_str) {
    $clean_string = preg_replace('/[^äüö\n(\x20-\x7F)]*/u', '', $dirty_str);
    return $clean_string;
}

function newpage_or_spacer($pdf, $next_block_size, $LN = 8) {
    $y = $pdf->GetY();
    if (($y + $next_block_size) >= 270) {
        $pdf->AddPage();
    } else {
        if ($y < 20) {
        } 
        else {
            $pdf->Ln($LN);
        }
    }
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

function multicell_with_str($pdf, $STR, $einzug, $Unit, $schriftgr=6) {
    $originalFontSize = $pdf->getFontSizePt();
    if (strlen($STR) > 0) {
        $pdf->MultiCell($einzug, $schriftgr, $STR . " " . $Unit, 0, 'L', 0, 0);
    } else {
        $pdf->MultiCell($einzug, $schriftgr, " - ", 0, 'L', 0, 0);
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
    } else {
        $pdf->SetTextColor(255, 0, 0);
        $pdf->MultiCell($hackerlcellgröße, $hackerl_schriftgr, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
    }
    $pdf->SetFont('helvetica', '', $originalFontSize);
    $pdf->SetTextColor(0, 0, 0);
}

function block_label_queer($block_header_w, $pdf, $block_label, $block_height = 12, $SB =390) {
    $pdf->SetFont('helvetica', 'B', $block_height ); 
    $pdf->MultiCell($SB, 1 ,"" , 'T', 'L', 0, 0);  
    $pdf->Ln(1); 
    $pdf->MultiCell($block_header_w, $block_height , $block_label, 0, 'L', 0, 0);    
    $pdf->SetFont('helvetica', '', 10);
}

function block_label($pdf, $block_label, $block_height = 12, $SB= 180 ) {
    $pdf->SetFont('helvetica', 'B', $block_height ); 
    $pdf->MultiCell($SB, $block_height , $block_label, "T", 'C', 0, 1);    
    $pdf->SetFont('helvetica', '', 10);
}


// depricated: . . . . . .
function block_label_sidew($pdf, $block_label, $block_height) {
    
    
    $pdf->Ln(); 
    $startX = $pdf->GetX();
    
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->StartTransform();
    $pdf->Rotate(90);
    $pdf->MultiCell($block_height+10, 40, $block_label, 0, 'L', 0, 0, $startX - $block_height);
    $pdf->StopTransform(); 
    $pdf->SetFont('helvetica', '', 10);
}

function el_in_room_html_table($pdf, $result, $init_einzug) {
    $pdf->MultiCell($init_einzug, 10, "", 0, "C", 0, 0);
    $columnWidthPercentages = array(10, 10, 8, 13, 59);
    $headers = array('ElementID', 'Variante', 'Anzahl', 'Neu/Bestand', 'Bezeichnung'); // 'Standort', 'Verwendung',
    $pdf->SetFont('helvetica', 'B', 12);
    $html = '<table border="0">';
    $html .= '<tr>';
    foreach ($columnWidthPercentages as $index => $widthPercentage) {
        $alignStyle = ($headers[$index] == 'Neu/Bestand' || $headers[$index] == 'Variante' || $headers[$index] == 'Anzahl') ? 'text-align: center;' : ''; // Add this line for centering
        if ($headers[$index] == 'Neu/Bestand') {
            $tablelabel = 'Bestand';
        } else if ($headers[$index] == 'Variante') {
            $tablelabel = 'Var';
        } else {
            $tablelabel = $headers[$index];
        }
        $html .= '<th width="' . $widthPercentage . '%" style="' . $alignStyle . '">' . $tablelabel . '</th>';
    }
    $html .= '</tr>';
    $pdf->SetFont('helvetica', '', 10);

    while ($row = $result->fetch_assoc()) {
        $html .= '<tr>';
        foreach ($columnWidthPercentages as $index => $widthPercentage) {
            $columnName = $headers[$index];
            $cellValue = $row[$columnName] ?? '';

            // Translate 'Neu/Bestand' values
            if ($columnName == 'Neu/Bestand') {
                $cellValue = translateBestand($cellValue);
            }
            $alignStyle = ($columnName == 'Neu/Bestand' || $columnName == 'Variante' || $columnName == 'Anzahl') ? 'text-align: center;' : ''; // Add this line for centering
            $html .= '<td width="' . $widthPercentage . '%" style="' . $alignStyle . '">' . $cellValue . '</td>';
        }
        $html .= '</tr>';
    }
    $html .= '</table>';

    $pdf->writeHTML($html);
}
