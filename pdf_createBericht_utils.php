<?php

///------------------  DATA FUNCTIONS  ------------------
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

//  ------------------  PDF FUNCTIONS ------------------
function text_black_bg_white($pdf) {
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFillColor(255, 255, 255);
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
    $string = preg_replace("/\s+\n/", "\n", $string); // Remove spaces before \n
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

function check_4_new_page($pdf, $height, $format = "") {
    $y = $pdf->GetY();     // Wenn Seitenende? Überprüfen und neue Seite anfangen
    $pagelength = 270;
    if ($format === "A3") {
        $pagelength = 290;
    }
    if (($y + $height) >= $pagelength) {
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

function block_label($pdf, $block_label, $block_height = 12, $SB = 180) {
    $pdf->SetFont('helvetica', 'B', $block_height);
    $pdf->MultiCell($SB, $block_height, $block_label, "T", 'C', 0, 1);
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

function anmA3($pdf, $inp_text, $SB, $block_header_w) {
//    if($pdf->GetX() > 60 ) {$pdf->Ln(10);} 
    if ($inp_text != "keine Angaben MT" && $inp_text != "") {
        $outstr = "Anm.: " . format_text(clean_string(br2nl($inp_text)));
        if (strlen($outstr) > 0 && is_not_no_comment($outstr)) {
            
        } else {
            $outstr = "Keine Anmerkung";
        }
        $rowHeightComment = $pdf->getStringHeight($SB, $outstr, false, true, '', 1);
        $pdf->MultiCell($block_header_w, $rowHeightComment, "", 0, 'R', 0, 0); //        if ($rowHeightComment < 25) {  //Cool, but wonky
        $pdf->MultiCell($SB - $block_header_w, $rowHeightComment, $outstr, 0, 'L', 0, 1);
//        } else { $columnWidth = ($SB - $block_header_w - 10) / 2; list($leftText, $rightText) = splitText($pdf, $outstr, $columnWidth);
//            writeTwoColumns($pdf, $columnWidth, $leftText, $rightText); }
    }
}

/* function splitText($pdf, $inp_text, $columnWidth) {
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
  //} */

function splitText($pdf, $inp_text, $columnWidth) {
    $lines = explode("\n", $inp_text);
    $middle = floor(count($lines) / 2);
    $leftLines = array_slice($lines, 0, $middle);
    $rightLines = array_slice($lines, $middle);
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
    $xBefore = $pdf->GetX();
    $yBefore = $pdf->GetY();
    $pdf->MultiCell($columnWidth, 0, $leftText, 0, 'L', false, 1);
    $leftHeight = $pdf->GetY() - $yBefore;
    $pdf->SetXY($xBefore + $columnWidth + 10, $yBefore);
    $pdf->MultiCell($columnWidth, 0, $rightText, 0, 'L', false, 1);
    $rightHeight = $pdf->GetY() - $yBefore;
    $pdf->SetY(max($leftHeight, $rightHeight) + $yBefore);
}

function raum_header($pdf, $ln_spacer, $SB, $Raumbezeichnung, $Raumnr, $RaumbereichNutzer, $Geschoss, $Bauetappe, $Bauabschnitt, $format = "", $parameter_changes_t_räume = 0) {
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
            $pdf->SetFillColor(200, 200, 200);
            $pdf->MultiCell($SB, 5, "", 'B', 'L', 0, 1);
            $pdf->MultiCell($SB, 1, "", 'B', 'L', 0.5, 1);
            $pdf->SetFillColor(255, 255, 255);
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
        multicell_text_hightlight($pdf, $SB * $qot + $incr, $ln_spacer, "Raumbezeichnung", "Raum: " . $Raumbezeichnung, $parameter_changes_t_räume, "L");
        $incr2 = 0;
        $Height = $pdf->getStringHeight($SB * $qot, "Nummer: " . $Raumnr, false, true, '', 1);
        while ($Height > $ln_spacer) {
            $incr2 += 5; //           
            $Height = $pdf->getStringHeight($SB * $qot + $incr2, "Nummer: " . $Raumnr, false, true, '', 1);
        }
        multicell_text_hightlight($pdf, $SB * ($qot) + $incr2, $ln_spacer, "Raumnr", "Nummer: " . $Raumnr, $parameter_changes_t_räume, "L");
        $decrement = ($incr + $incr2) / 4;
        multicell_text_hightlight($pdf, $SB * ($qot) - $decrement, $ln_spacer, "Raumbereich Nutzer", "Bereich: " . $RaumbereichNutzer, $parameter_changes_t_räume, "L");
        multicell_text_hightlight($pdf, $SB * ($qot) - $decrement, $ln_spacer, "Geschoss", "Geschoss: " . $Geschoss, $parameter_changes_t_räume, "L");
        multicell_text_hightlight($pdf, $SB * ($qot) - $decrement, $ln_spacer, "Bauetappe", "Bauetappe: " . $Bauetappe, $parameter_changes_t_räume, "L");
        multicell_text_hightlight($pdf, $SB * ($qot) - $decrement, $ln_spacer, "Bauabschnitt", "Bauteil: " . $Bauabschnitt, $parameter_changes_t_räume, "L");
        $pdf->Ln($ln_spacer);
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

function multicell_with_nr($pdf, $NR, $unit, $schriftgr, $einzug) {
    $originalFontSize = $pdf->getFontSizePt();
    if ($NR > 0) {
        $pdf->MultiCell($einzug, $schriftgr, $NR . $unit, 0, 'L', 1, 0);
    } else {
        $pdf->MultiCell($einzug, $schriftgr, " - ", 0, 'L', 1, 0);
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
//        if ($booooool && ($c_even % 2 == 0)) {
            $borders = 'LT';
//        }
        $pdf->MultiCell($spaces[0], $rowHeightMainLine, $row['ElementID'], $borders, 'C', $fill, 0);
        $borders = 'T';
        $pdf->MultiCell($spaces[1], $rowHeightMainLine, $row['Variante'], $borders, 'C', $fill, 0);
        $pdf->MultiCell($spaces[2], $rowHeightMainLine, $row['SummevonAnzahl'], $borders, 'C', $fill, 0);
        $pdf->MultiCell($spaces[3], $rowHeightMainLine, translateBestand($row['Neu/Bestand']), $borders, 'C', $fill, 0);
//        if ($booooool && ($c_even % 2 == 1)) {
            $borders = 'RT';
//        }
        $pdf->MultiCell($spaces[4], $rowHeightMainLine, $row['Bezeichnung'], $borders, 'L', $fill, 0);
        if ($booooool && ($c_even % 2 == 0) || !$booooool) {
            $pdf->Ln();
            $fill = !$fill;
        }
    }$pdf->Ln();
    $pdf->Line(15+ $block_header_w, $pdf->GetY(), $SB + 15 , $pdf->GetY(),$style_dashed);
    
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

            // Translate 'Neu/Bestand' values
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

/*

function el_in_room_html_table($pdf, $result, $init_einzug, $format = "", $SB = 0) {
    $pdf->MultiCell($init_einzug, 10, "", 0, "C", 0, 0);
    $columnWidthPercentages = array(10, 10, 10, 10, 60);
    $headers = array('ElementID', 'Variante', 'Anzahl', 'Neu/Bestand', 'Bezeichnung');
    $pdf->SetFont('helvetica', 'B', 12);
    $html = '<table border="0">';
    $html .= '<tr>';

    foreach ($columnWidthPercentages as $index => $widthPercentage) {
        $alignStyle = ($headers[$index] == 'Neu/Bestand' || $headers[$index] == 'Variante' || $headers[$index] == 'Anzahl') ? 'text-align: center;' : '';
        $tablelabel = $headers[$index] == 'Neu/Bestand' ? 'Bestand' : ($headers[$index] == 'Variante' ? 'Var' : $headers[$index]);
        $html .= '<th width="' . $widthPercentage . '%" style="' . $alignStyle . '">' . $tablelabel . '</th>';
    }
    $html .= '</tr>';
    $pdf->SetFont('helvetica', '', 10);

    $rowCount = 0;
    $splitTable = $format === "A3" && count($result) > 5;

    while ($row = $result->fetch_assoc()) {
        if ($splitTable && $rowCount == ceil(count($result) / 2)) {
            $html .= '</table><table border="0">';
        }
        $html .= '<tr>';
        foreach ($columnWidthPercentages as $index => $widthPercentage) {
            $columnName = $headers[$index] === "Anzahl" && $format === "A3" ? "SummevonAnzahl" : $headers[$index];
            $cellValue = $row[$columnName] ?? '';
            if ($columnName == 'Neu/Bestand') {
                $cellValue = translateBestand($cellValue);
            }
            $alignStyle = ($columnName === 'Neu/Bestand' || $columnName === 'Variante' || $columnName === 'Anzahl' ||  $columnName === "SummevonAnzahl" ) ? 'text-align: center;' : '';
            $html .= '<td width="' . $widthPercentage . '%" style="' . $alignStyle . '">' . $cellValue . '</td>';
        }
        $html .= '</tr>';
        $rowCount++;
    }
    $html .= '</table>';

    $pdf->writeHTML($html);
}



function make_mt_list() {
    
}
