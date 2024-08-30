<?php

///------------------ GET/Process DATA FUNCTIONS  ------------------
function filter_old_equal_new($data) {
    $groupedData = [];
    foreach ($data as $item) {
        $parameter = $item['parameter'];
        if (!isset($groupedData[$parameter])) {
            $groupedData[$parameter] = [];
        }
        $groupedData[$parameter][] = $item;
    }
    $filteredData = array_filter($groupedData, function ($group) {
        $firstEntry = reset($group);
        $lastEntry = end($group);
        return $firstEntry['wert_neu'] != $lastEntry['wert_alt'];
    });
    $out = [];
    foreach ($filteredData as $group) {
        foreach ($group as $item) {
            $out[] = $item;
        }
    }
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

// TEXT MANIPULATION 
function text_black_bg_white($pdf) {
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFillColor(255, 255, 255);
}

function format_text($string) {
    $return = preg_replace("/\s+\n/", "\n", $string); // Remove spaces before \n
    return $return;
}

function clean_string($dirty_str) {
    $clean_string = preg_replace('/[^äüö\n(\x20-\x7F)]*/u', '', $dirty_str);
    return $clean_string;
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

function is_not_no_comment($str) {
    if ($str == "keine Anmerkung" || $str == "keine Angaben" || $str == "") {
        return false;
    } else {
        return true;
    }
}

function translateBestand($value) {
    return ($value == 0) ? 'Ja' : 'Nein';
}

function translate_1_to_yes($value) {
    return ($value == 1) ? 'Ja' : 'Nein';
}

function not_zero_or_keineAngabe($str, $unit) {
    $_out = "";
    if ($str != 0 || $str != "-") {
        $_out = "ca. " . $str . $unit;
    } else {
        $_out = "keine Angabe";
    }
}

//  ------------------  PDF FUNCTIONS ------------------
// PAGING

function newpage_or_spacer($pdf, $next_block_size, $LN = 8) {
    $y = $pdf->GetY();
    if (($y + $next_block_size) >= 270) {
        $pdf->AddPage();
    } else {
        if ($y > 20) {
            $pdf->Ln($LN);
        }
    }
}

function newpageA3($pdf, $next_block_size, $SH) {
    $y = $pdf->GetY();
    if (($y + $next_block_size) >= $SH) {
        $pdf->AddPage();
    }
}

function check_4_new_page($pdf, $height, $format = "") {
    $y = $pdf->GetY();     // Wenn Seitenende? Überprüfen und neue Seite anfangen
    $pagelength = 270;
    if ($format === "A3") {
        $pagelength = 290;
    }
    if (($y + $height) >= $pagelength) {
        $pdf->AddPage();
        return true;
    } else {
        return false;
    }
}

function newpage_or_spacerA3($pdf, $next_block_size, $SH, $LN = 8) {
    $y = $pdf->GetY();
    if (($y + $next_block_size) >= 270 || ($y + $next_block_size) >= $SH) {
        $pdf->AddPage();
    } else {
        if ($y > 20) {
            $pdf->Ln($LN);
        }
    }
}

// STYLE 
function dashed_line($pdf, $offset) {
    $pdf->SetLineStyle(array('dash' => 2, 'color' => array(0, 0, 0)));
    $y = $pdf->GetY() + $offset;
    $pdf->Line(25, $y, 185, $y);
    $pdf->SetLineStyle(array('dash' => 0, 'color' => array(0, 0, 0)));
}

function balken($pdf, $horizontalSpacerLN, $SB) {
    $pdf->SetFillColor(200, 210, 200); 
        $pdf->SetFont('helvetica', '', 1);

    $pdf->MultiCell($SB, 2, "", "BT", 'L', 1, 1);
    $pdf->Ln($horizontalSpacerLN);
    $pdf->SetFillColor(00, 00, 00);   $pdf->SetFont('helvetica', '', 10);
    
}

// BAUSTEINE

function block_label_queer($block_header_w, $pdf, $block_label, $upcomming_block_size, $block_height = 12, $SB = 390) {
    if( $block_label != "Med.-tech.") {newpageA3($pdf, $upcomming_block_size, 275);}
    if( $block_label === "Med.-tech." && $upcomming_block_size < 275 ) { 
        newpageA3($pdf, $upcomming_block_size, 275);}
    $pdf->SetFont('helvetica', 'B', $block_height);
    $pdf->MultiCell($SB, 1, "", 'T', 'L', 0, 0);
    $pdf->Ln(1);
    $pdf->MultiCell($block_header_w, $block_height, $block_label, 0, 'L', 0, 0);
    $pdf->SetFont('helvetica', '', 10);
}

function block_label($pdf, $block_label, $block_height = 10, $SB = 180) {
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->MultiCell($SB, $block_height, $block_label, "T", 'L', 0, 0);
    $pdf->Ln($block_height);
    $pdf->SetFont('helvetica', '', 10);
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

function anm_txt($pdf, $inp_text, $SB, $block_header_w) {
    $outstr = (clean_string(br2nl($inp_text)));
    if (strlen($outstr) > 0 && is_not_no_comment($outstr)) {
        $outstr = str_replace('*', '', $outstr);
        $rowHeightComment = $pdf->getStringHeight($SB, $outstr, false, true, '', 1);
        $pdf->MultiCell($block_header_w, $rowHeightComment, "", 0, 'R', 0, 0); //        if ($rowHeightComment < 25) {  //Cool, but wonky
        $pdf->MultiCell($SB - $block_header_w, $rowHeightComment, $outstr, 0, 'L', 0, 1);
    }
}

function anmA3($pdf, $inp_text, $SB, $block_header_w) {
    if ($inp_text != "keine Angaben MT" && $inp_text != "") {
        $outstr = "Anm.: " . format_text(clean_string(br2nl($inp_text)));
        if (strlen($outstr) > 0 && is_not_no_comment($outstr)) {
            
        } else {
            $outstr = "Keine Anmerkung";
        }
        $rowHeightComment = $pdf->getStringHeight($SB, $outstr, false, true, '', 1);
        $pdf->MultiCell($block_header_w, $rowHeightComment, "", 0, 'R', 0, 0); //        if ($rowHeightComment < 25) {  //Cool, but wonky
        $pdf->MultiCell($SB - $block_header_w, $rowHeightComment, $outstr, 0, 'L', 0, 1);
    }
}

function separate_headerwdth_proportionally() {
    $result_widths = [];
    foreach ($output_pairs as $pair) {
        $one_liner = false;
        $attempts = 0;
        while (!$one_liner && $attempts < 30) {
            $text = $pair[1];
            $incr = 0;
            $Height = $pdf->getStringHeight($SB * $qot, $text, false, true, '', 1);
            while ($Height > $ln_spacer && $attempts < 30) {
                $incr += 5;
                $Height = $pdf->getStringHeight($SB * $qot + $incr, $text, false, true, '', 1);
                $attempts++;
            }

            if ($Height <= $ln_spacer) {
                $one_liner = true;
                $result_widths[] = $SB * $qot + $incr;
            } else {
                $decrement = $incr / 4;
                foreach ($output_pairs as $pairr) {
                    if ($text === $pairr[1]) {
                        continue;
                    } else {
                        $textt = $pairr[1];
                        $Height = $pdf->getStringHeight($SB * $qot, $textt, false, true, '', 1);
                        if ($Height <= $ln_spacer) {
                            $one_liner = true;
                            $result_widths[] = $SB * $qot;
                            break;
                        }
                    }
                }
            }
        }
    }
    return $result_widths;
}

function raum_header($pdf, $ln_spacer, $SB, $Raumbezeichnung, $Raumnr, $RaumbereichNutzer, $Geschoss, $Bauetappe, $Bauabschnitt, $format = "", $parameter_changes_t_räume = 0) {
    $pdf->SetFont('helvetica', 'B', 10);
    if ($format == "") {
        $qot = 5 / 9;
        $pdf->MultiCell($SB * $qot, $ln_spacer, "Raum: " . $Raumbezeichnung, 0, 'L', 0, 0);
        $pdf->MultiCell($SB * (1 - $qot), $ln_spacer, "Nummer: " . $Raumnr, 0, 'L', 0, 0);
        $pdf->Ln($ln_spacer);

        $pdf->MultiCell($SB * $qot, $ln_spacer, "Bereich: " . $RaumbereichNutzer, 0, 'L', 0, 0);
        $pdf->MultiCell($SB * (1 - $qot), $ln_spacer, "Geschoss: " . $Geschoss, 0, 'L', 0, 0);
        $pdf->Ln($ln_spacer);
        $pdf->MultiCell($SB * $qot, $ln_spacer, "Bauetappe: " . $Bauetappe, 'B', 'L', 0, 0);
        $pdf->MultiCell($SB * (1 - $qot), $ln_spacer, "Bauteil: " . $Bauabschnitt, 'B', 'L', 0, 1);
    } else if ($format == "Gr") {
        $qot = 0.5;
        $pdf->MultiCell($SB * $qot, $ln_spacer, "Raum: " . $Raumbezeichnung, 0, 'L', 0, 0);
        $pdf->MultiCell($SB * $qot, $ln_spacer, "Nummer: " . $Raumnr, 0, 'L', 0, 0);
        $pdf->Ln($ln_spacer);
        $pdf->MultiCell($SB * $qot, $ln_spacer, "Bereich: " . $RaumbereichNutzer, "B", 'L', 0, 0);
        $pdf->MultiCell($SB * $qot, $ln_spacer, "Bauetappe: " . $Bauetappe, 'B', 'L', 0, 0);
        $pdf->Ln();
    } else if ($format == "A3") {

        if (($pdf->GetY()) >= 180) {
            $pdf->AddPage();
        }
        if (($pdf->GetY()) >= 18) {
            balken($pdf, 1, $SB);
        } else {
            $pdf->Ln(1); 
        } 
        $spacer = "  -";
        $output_pairs = [
            ["Raumbezeichnung", "Raum: " . $Raumbezeichnung.$spacer],
            ["Raumnr", "Nummer: " . $Raumnr.$spacer],
            ["Raumbereich Nutzer", "Bereich: " . $RaumbereichNutzer.$spacer],
            ["Geschoss", "Geschoss: " . $Geschoss.$spacer],
            ["Bauetappe", "Bauetappe: " . $Bauetappe.$spacer],
            ["Bauabschnitt", "Bauteil: " . $Bauabschnitt.$spacer]
        ];
        $qot = 1 / 7;
        
        foreach ($output_pairs as $pair) {
            $incr = 0;
            $Height = $pdf->getStringHeight($SB * $qot, $pair[1], false, true, '', 1);
            while ($Height > $ln_spacer) {
                $incr += 5;
                $Height = $pdf->getStringHeight($SB * $qot + $incr, $pair[1], false, true, '', 1);
            }
            multicell_text_hightlight($pdf, $SB * $qot + $incr, $ln_spacer, $pair[0], $pair[1], $parameter_changes_t_räume, "L");
        }
        $pdf->Ln(7);
    }
    $pdf->SetFont('helvetica', '', 10);
}

function init_pdf_attributes($pdf, $einzugLR, $marginTop, $marginBTM, $format = "", $label) {
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('LIMET Consulting und Planung ZT GmbH');
    $pdf->SetTitle($label);
    $pdf->SetSubject($label);
    $pdf->SetKeywords($label);
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

function multicell_text_hightlight($pdf, $breite, $font_size, $parameter_sql_name, $pdf_text, $parameter_changes_t_räume, $side = "L") {
    if (in_array($parameter_sql_name, $parameter_changes_t_räume)) {
        $pdf->SetFillColor(220, 235, 190);
    } else {
        $pdf->SetFillColor(255, 255, 255);
    }
    $pdf->MultiCell($breite, $font_size, $pdf_text, 0, $side, 1, 0);
}

function multicell_with_stk($pdf, $NR, $einzug) {
    if ($NR > 0) {
        $pdf->MultiCell($einzug, 6, $NR . " Stk", 0, 'L', 1, 0);
    } else {
        $pdf->MultiCell($einzug, 6, " - ", 0, 'L', 1, 0);
    } $pdf->SetFillColor(255, 255, 255);
}

function multicell_with_nr($pdf, $NR, $unit, $schriftgr, $einzug, $Ausrichtung="L") {
    $originalFontSize = $pdf->getFontSizePt();
    if ($NR > 0) {
        $pdf->MultiCell($einzug, $schriftgr, $NR . $unit, 0, $Ausrichtung, 1, 0);
    } else {
        $pdf->MultiCell($einzug, $schriftgr, " - ", 0, $Ausrichtung, 1, 0);
    }
    $pdf->SetFontSize($originalFontSize);
    $pdf->SetFillColor(255, 255, 255);
}

function multicell_with_str($pdf, $STR, $einzug, $Unit, $schriftgr = 6) {
    $originalFontSize = $pdf->getFontSizePt();
    if (strlen($STR) > 0) {
        $pdf->MultiCell($einzug, $schriftgr, $STR . " " . $Unit, 0, 'L', 1, 0);
    } else {
        $pdf->MultiCell($einzug, $schriftgr, " - ", 0, 'L', 1, 0);
    }
    $pdf->SetFontSize($originalFontSize);
    $pdf->SetFillColor(255, 255, 255);
}

function hackerlA3($pdf, $hackerl_schriftgr, $hackerlcellgröße, $param, $comp_true) {
    $originalFontSize = $pdf->getFontSizePt();
    $pdf->SetFont('zapfdingbats', '', 10);
    if ($param == $comp_true || $param == "Ja" || $param == "ja" || 1 == $param || "1" === $param) {
        $pdf->MultiCell($hackerlcellgröße, $hackerl_schriftgr, TCPDF_FONTS::unichr(52), 0, 'L', 1, 0);
    } else {
        $pdf->MultiCell($hackerlcellgröße, $hackerl_schriftgr, TCPDF_FONTS::unichr(54), 0, 'L', 1, 0);
    }
    $pdf->SetFont('helvetica', '', $originalFontSize);
    $pdf->SetFillColor(255, 255, 255);
}

function hackerl($pdf, $hackerl_schriftgr, $hackerlcellgröße, $param, $comp_true) {
    $originalFontSize = $pdf->getFontSizePt();
    $pdf->SetFont('zapfdingbats', '', $hackerl_schriftgr);
    if ($param == $comp_true || $param == "Ja" || $param == "ja" || 1 == $param || "1" === $param) {
        $pdf->SetTextColor(0, 255, 0);
        $pdf->MultiCell($hackerlcellgröße, $hackerl_schriftgr, TCPDF_FONTS::unichr(52), 0, 'L', 1, 0);
    } else {
        $pdf->SetTextColor(255, 0, 0);
        $pdf->MultiCell($hackerlcellgröße, $hackerl_schriftgr, TCPDF_FONTS::unichr(54), 0, 'L', 1, 0);
    }
    $pdf->SetFont('helvetica', '', $originalFontSize);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFillColor(255, 255, 255);
}

function strahlenanw($pdf, $param, $cellsize, $gr) {
    $originalFontSize = $pdf->getFontSizePt();
    $pdf->SetFont('zapfdingbats', '', 10);
    if ($param === '0') {//        $pdf->SetTextColor(255, 0, 0);
        $pdf->MultiCell($cellsize, $gr, TCPDF_FONTS::unichr(54), 0, 'L', 1, 0);
    } else {
        if ($param === '1') {//            $pdf->SetTextColor(0, 255, 0);
            $pdf->MultiCell($cellsize, $gr, TCPDF_FONTS::unichr(52), 0, 'L', 1, 0);
        } else {
            $pdf->SetFont('helvetica', '', 10);
            $pdf->MultiCell($cellsize, $gr, "Quasi stationär", 0, 'L', 1, 0);
        }
    }
    text_black_bg_white($pdf);
    $pdf->SetFont('helvetica', '', $originalFontSize);
}

function make_MT_list2($pdf, $SB, $resultX) {
    $pdf->SetFont('helvetica', '', 10);
    $fill = 0;
    while ($row = $resultX->fetch_assoc()) {
        $fill++;
        $pdf->MultiCell($SB / 2, 5, " - " . $row['Bezeichnung'], "", 'L', 0, 0);
        if ($fill % 2 === 0) {
            $pdf->Ln();
        }
    }
        if ($fill % 2 === 1) {
            $pdf->Ln();
        }
}

function make_MT_list($pdf, $SB, $block_header_w, $booooool, $resultX, $style_normal, $style_dashed) {
    $pdf->SetLineStyle($style_dashed);
    $proportions = array(0.1, 0.1, 0.1, 0.1, 0.60);
    $spaces = array();
    foreach ($proportions as $prop) {
        $spaces[] = ($SB - $block_header_w ) * 0.5 * $prop;
    }
    $fill = 0;
    $pdf->SetFillColor(244, 244, 244);
    $rowHeightFirstLine = $pdf->getStringHeight(50, "ID", false, true, '', 1);
    $pdf->MultiCell($spaces[0], $rowHeightFirstLine, "ID", 'LB', 'C', 0, 0);
    $pdf->MultiCell($spaces[1], $rowHeightFirstLine, "Var", 'B', 'C', 0, 0);
    $pdf->MultiCell($spaces[2], $rowHeightFirstLine, "Stk", 'B', 'C', 0, 0);
    $pdf->MultiCell($spaces[3], $rowHeightFirstLine, "Bestand", 'B', 'C', 0, 0);
    $pdf->MultiCell($spaces[4], $rowHeightFirstLine, "Element", 'B', 'L', 0, 0);
    if ($booooool) {
        $pdf->MultiCell($spaces[0], $rowHeightFirstLine, "ID", 'LB', 'C', 0, 0);
        $pdf->MultiCell($spaces[1], $rowHeightFirstLine, "Var", 'B', 'C', 0, 0);
        $pdf->MultiCell($spaces[2], $rowHeightFirstLine, "Stk", 'B', 'C', 0, 0);
        $pdf->MultiCell($spaces[3], $rowHeightFirstLine, "Bestand", 'B', 'C', 0, 0);
        $pdf->MultiCell($spaces[4], $rowHeightFirstLine, "Element", 'BR', 'L', 0, 0);
    }
    $pdf->Ln();
    $c_even = 0;
    while ($row = $resultX->fetch_assoc()) {
        $borders = 'T';
        $pdf->SetFont('helvetica', '', 8);
        $rowHeightMainLine = $pdf->getStringHeight(50, $row['Bezeichnung'], false, true, '', 1);
        check_4_new_page($pdf, $rowHeightMainLine, "A3");
        if (!$booooool || ($booooool && $c_even % 2 == 0)) {
            $pdf->MultiCell($block_header_w, $rowHeightMainLine, "", "", 'R', "", 0);
        }
        $c_even++;
        $borders = 'LT';
        $pdf->MultiCell($spaces[0], $rowHeightMainLine, $row['ElementID'], $borders, 'C', $fill, 0);
        $borders = 'T';
        $pdf->MultiCell($spaces[1], $rowHeightMainLine, $row['Variante'], $borders, 'C', $fill, 0);
        $pdf->MultiCell($spaces[2], $rowHeightMainLine, $row['SummevonAnzahl'], $borders, 'C', $fill, 0);
        $pdf->MultiCell($spaces[3], $rowHeightMainLine, translateBestand($row['Neu/Bestand']), $borders, 'C', $fill, 0);
        $borders = 'RT';
        $pdf->MultiCell($spaces[4], $rowHeightMainLine, $row['Bezeichnung'], $borders, 'L', $fill, 0);
        if ($booooool && ($c_even % 2 == 0) || !$booooool) {
            $pdf->Ln();
            $fill = !$fill;
        }
    }
    $pdf->Ln();
    $pdf->Line(15 + $block_header_w, $pdf->GetY(), $SB + 15, $pdf->GetY(), $style_dashed);
    $pdf->SetLineStyle($style_normal);
    $pdf->Line(15, $pdf->GetY() + 1, $SB + 15, $pdf->GetY() + 1, $style_normal);
}

function el_in_room_html_table($pdf, $result, $init_einzug, $format = "", $SB = 0) {
    $pdf->MultiCell($init_einzug, 10, "", 0, "C", 0, 0);
    $columnWidthPercentages = array(10, 10, 10, 10, 60);
    $AnzahlKey = "Anzahl";

    if ($format === "A3" && $SB > 0) {
        $columnWidthPercentages = array_map(function ($x) {
            return $x / 2;
        }, $columnWidthPercentages);
    }
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
            if ($columnName === "Anzahl" && $format === "A3") {
                $columnName = "SummevonAnzahl";
            }
            $cellValue = $row[$columnName] ?? '';

            if ($columnName == 'Neu/Bestand') {
                $cellValue = translateBestand($cellValue);
            }
            $alignStyle = ($columnName === 'Neu/Bestand' || $columnName === 'Variante' || $columnName === 'Anzahl' || $columnName === "SummevonAnzahl" ) ? 'text-align: center;' : ''; // Add this line for centering
            $html .= '<td width="' . $widthPercentage . '%" style="' . $alignStyle . '">' . $cellValue . '</td>';
        }
        $html .= '</tr>';
    }
    $html .= '</table>';
    $pdf->writeHTML($html);
}
