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

function filter_old_equal_new($data) {
    $filteredData = array_filter($data, function ($item) {
        return $item['wert_neu'] != $item['wert_alt'];
    });

    // Re-index the array keys
    $out = array_values($filteredData);

    return $out;
}

function getValidatedDateFromURL() {
    if (isset($_GET['date'])) {
        $date = $_GET['date'];

        $date = filter_var($date, FILTER_SANITIZE_STRING);
        $format = 'd-m-Y';
        $d = DateTime::createFromFormat($format, $date);

        if ($d && $d->format($format) == $date) {
            return $d->format('Y-m-d');
        } else {
            return null;
        }
    } else {
        return null;
    }
}

function block_label_queer($block_header_w, $pdf, $block_label, $upcomming_block_size, $block_height = 12, $SB = 390) {
    newpageA3($pdf, $upcomming_block_size, 275);

    $pdf->SetFont('helvetica', 'B', $block_height);
    $pdf->MultiCell($SB, 1, "", 'T', 'L', 0, 0);
    $pdf->Ln(1);
    $pdf->MultiCell($block_header_w, $block_height, $block_label, 0, 'L', 0, 0);
    $pdf->SetFont('helvetica', '', 10);
}

function newpageA3($pdf, $next_block_size, $SH) {
    $y = $pdf->GetY();
    if (($y + $next_block_size) >= $SH) {
        $pdf->AddPage();
    }
}

function kify($input) {
    if (is_numeric($input)) {
        if ($input >= 1000) {
            $input = $input / 1000;
            $input = rtrim(number_format($input, 3, ',', ''), '0');
            $input = rtrim($input, ',') . 'k';
        }
    }
    return $input;
}

function getAnmHeight($pdf, $inp_text, $SB) {
    if ($inp_text != "keine Angaben MT" && $inp_text != "") {
        $outstr = "Anm.: " . format_text(clean_string(br2nl($inp_text)));
        if (!is_not_no_comment($outstr)) {
            $outstr = "Keine Anmerkung";
        }
        $rowHeightComment = $pdf->getStringHeight($SB, $outstr, false, true, '', 1);
        return $rowHeightComment;
    }
}

function anmA3($pdf, $inp_text, $SB, $block_header_w) {
//    if($pdf->GetX() > 60 ) {$pdf->Ln(10);} 
    if ($inp_text != "keine Angaben MT" && $inp_text != "") {
        $outstr = "Anm.: " . format_text(clean_string(br2nl($inp_text)));
        if (strlen($outstr) > 0 && is_not_no_comment($outstr)) {
            
        } else {
            $outstr = "Keine Anmerkung";
        }
        $rowHeightComment = $pdf->getStringHeight($SB, $outstr, false, true, '', 1);
        $block_header_w = 10;
        $pdf->MultiCell($block_header_w, $rowHeightComment, "", 0, 'R', 0, 0);
        if ($rowHeightComment < 25) {
            $pdf->MultiCell($SB - $block_header_w, $rowHeightComment, $outstr, 0, 'L', 0, 1);
        } else {
            $columnWidth = ($SB - $block_header_w - 10) / 2;
            list($leftText, $rightText) = splitText($pdf, $outstr, $columnWidth);
            writeTwoColumns($pdf, $columnWidth, $leftText, $rightText);
        }
    }
}
//
//function splitText($pdf, $inp_text, $columnWidth) {
//    $middle = floor(strlen($inp_text) / 2);
//    $lastNewlineBeforeMiddle = strrpos(substr($inp_text, 0, $middle), "\n");
//    
//    if ($lastNewlineBeforeMiddle !== false) {
//        $middle = $lastNewlineBeforeMiddle;
//    } else {
//        $middle = min(
//            strpos($inp_text, ' ', $middle),
//            strpos($inp_text, '-', $middle)
//        );
//    }
//
//    // Split the text into two parts
//    $leftText = substr($inp_text, 0, $middle);
//    $rightText = substr($inp_text, $middle);
//
//    return array($leftText, $rightText);
//}
function splitText($pdf, $inp_text, $columnWidth) {
    $lines = explode("\n", $inp_text);
    $middle = floor(count($lines) / 2);

    // Split the text into two parts
    $leftLines = array_slice($lines, 0, $middle);
    $rightLines = array_slice($lines, $middle);

    // Check the heights of the two columns and balance them
    while (abs(count($leftLines) - count($rightLines)) > 1) {
        if (count($leftLines) > count($rightLines)) {
            array_unshift($rightLines, array_pop($leftLines));
        } else {
            array_push($leftLines, array_shift($rightLines));
        }
    }

    return array(implode("\n", $leftLines), implode("\n", $rightLines));
}


function writeTwoColumns($pdf, $columnWidth, $leftText, $rightText) {


    // Save the current X and Y coordinates
    $xBefore = $pdf->GetX();
    $yBefore = $pdf->GetY();

    // Write the left column
    $pdf->MultiCell($columnWidth, 0, $leftText, 0, 'L', false, 1);

    // Get the height of the left column
    $leftHeight = $pdf->GetY() - $yBefore;

    // Set the X and Y coordinates to the start of the right column
    $pdf->SetXY($xBefore + $columnWidth + 10, $yBefore);

    // Write the right column
    $pdf->MultiCell($columnWidth, 0, $rightText, 0, 'L', false, 1);

    // Get the height of the right column
    $rightHeight = $pdf->GetY() - $yBefore;

    // Set the Y coordinate to the new line of the lowest point of this text input
    $pdf->SetY(max($leftHeight, $rightHeight) + $yBefore);
}

function raum_header($pdf, $ln_spacer, $SB, $Raumbezeichnung, $Raumnr, $RaumbereichNutzer, $Geschoss, $Bauetappe, $Bauabschnitt, $format = "", $Block_height = 0) {
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
        if (($pdf->GetY()) >= 180) {// || $Block_height > 275- ($pdf->GetY())) {// Unsauberes schnell schnell  #TODO
            $pdf->AddPage();
        } else if (($pdf->GetY()) >= 20) {
            $pdf->Ln();
            $pdf->MultiCell($SB, 12, "", 'B', 'L', 0, 1);
            $pdf->MultiCell($SB, 1, "", 'B', 'L', 0.5, 1);
        }
        $pdf->SetFont('helvetica', 'B', 10);
        $extra_space_underneath_header = 2;
        $pdf->Ln($extra_space_underneath_header);
        $ln_spacer = $ln_spacer - $extra_space_underneath_header;

        $qot = 1 / 6;
        $incr = 0;
        $Height = $pdf->getStringHeight($SB * $qot, "Raum: " . $Raumbezeichnung, false, true, '', 1);
        while ($Height > $ln_spacer) {
            $incr += 5; //           
            $Height = $pdf->getStringHeight($SB * $qot + $incr, "Raum: " . $Raumbezeichnung, false, true, '', 1);
        }
        $pdf->MultiCell($SB * $qot + $incr, $ln_spacer, "Raum: " . $Raumbezeichnung, 'B', 'L', 0, 0);

        $incr2 = 0;
        $Height = $pdf->getStringHeight($SB * $qot, "Nummer: " . $Raumnr, false, true, '', 1);
        while ($Height > $ln_spacer) {
            $incr2 += 5; //           
            $Height = $pdf->getStringHeight($SB * $qot + $incr2, "Nummer: " . $Raumnr, false, true, '', 1);
        }
        $pdf->MultiCell($SB * ($qot) + $incr2, $ln_spacer, "Nummer: " . $Raumnr, 'B', 'L', 0, 0);

        $decrement = ($incr + $incr2) / 4;

        $pdf->MultiCell($SB * $qot - $decrement, $ln_spacer, "Bereich: " . $RaumbereichNutzer, 'B', 'L', 0, 0);
        $pdf->MultiCell($SB * ($qot) - $decrement, $ln_spacer, "Geschoss: " . $Geschoss, 'B', 'L', 0, 0);
        $pdf->MultiCell($SB * $qot - $decrement, $ln_spacer, "Bauetappe: " . $Bauetappe, 'B', 'L', 0, 0);
        $pdf->MultiCell($SB * ($qot) - $decrement, $ln_spacer, "Bauteil: " . $Bauabschnitt, 'B', 'L', 0, 1);
        $pdf->SetFont('helvetica', '', 10);
    }
}

function init_pdf_attributes($pdf, $einzugLR, $marginTop, $marginBTM, $format = "") {
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('LIMET Consulting und Planung ZT GmbH');
    $pdf->SetTitle('Bauangaben');
    $pdf->SetSubject('Bauangaben');
    $pdf->SetKeywords('Bauangaben');
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

function not_zero_or_keineAngabe($str, $unit) {
    $_out = "";
    if ($str != 0 || $str != "-") {
        $_out = "ca. " . $str . $unit;
    } else {
        $_out = "keine Angabe";
    }
}

function is_not_no_comment($str) {
    if ($str == "keine Anmerkung" || $str == "keine Angaben" || $str == "") {
        return false;
    } else {
        return true;
    }
}

function format_text($string) {
//    $spacer = " --- ";
    $string = preg_replace("/\s+\n/", "\n", $string); // Remove spaces before \n
    //
    $string = preg_replace("/\n\n\n/", "\n", $string); // Remove spaces before \n
    $string = preg_replace("/\n\n/", "\n", $string); // Remove spaces before \n
//    $string = str_replace("\n", $spacer, $string);
//    $string = str_replace("..", ".", $string);
//    if (preg_match('/²/', $string)) {
//        // Replace the superscript 2 with <sup>2</sup>
//        $return = str_replace('²', '<sup>2</sup>', $string);
//    } else {
//        $return = $string;
//    }
    $return = $string; // str_replace("?", " ", $string);
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
            
        } else {
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
        $pdf->MultiCell($einzug, $schriftgr, $NR . $unit, 0, 'L', 0, 0);
    } else {
        $pdf->MultiCell($einzug, $schriftgr, " - ", 0, 'L', 0, 0);
    }
    $pdf->SetFontSize($originalFontSize);
}

function multicell_with_str($pdf, $STR, $einzug, $Unit, $schriftgr = 6) {
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

function block_label($pdf, $block_label, $block_height = 12, $SB = 180) {
    $pdf->SetFont('helvetica', 'B', $block_height);
    $pdf->MultiCell($SB, $block_height, $block_label, "T", 'C', 0, 1);
    $pdf->SetFont('helvetica', '', 10);
}

// depricated: . . . . . .
function block_label_sidew($pdf, $block_label, $block_height) {


    $pdf->Ln();
    $startX = $pdf->GetX();

    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->StartTransform();
    $pdf->Rotate(90);
    $pdf->MultiCell($block_height + 10, 40, $block_label, 0, 'L', 0, 0, $startX - $block_height);
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
