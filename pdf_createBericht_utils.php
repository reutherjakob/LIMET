<?php

// Für A4 
$einzugLR=15;
$SB = 210 - 2* $einzugLR ; // seitenbreite minus die lr einzüge
$hackerl_Zellgröße=5; // set var in method below to same value!
$hackerl_schriftgröße= 8;
$einzugC1 = 40; 
$einzugC2 = 60 - $einzugC1;
$einzugE = 30;
$ln_spacer1 = 5;


function br2nl($string){
    $return= str_replace(array("<br/>"), "\n", $string);
    return $return;
}

function format_text($string){
    $spacer ="; "; 
    $return= str_replace("\n", $spacer, $string);
    return $return;
}

function clean_string ($dirty_str){
   $clean_string = preg_replace('/[^äüö\n(\x20-\x7F)]*/u','', $dirty_str);
   return $clean_string;
}


function newpage_or_spacer($pdf, $next_block_size){
    $y = $pdf->GetY();    
    if (($y +$next_block_size) >= 270) {
        $pdf->AddPage();
    } else {        
        if($y< 20){} // header size
        else{
            block_spacer($pdf);          
        }
    }
}

function block_spacer($pdf) {
        //$pdf->Ln(4); 
        //$pdf->MultiCell(180, 6, " ",'B', 'L', 0, 0);
        $pdf->Ln(8); 
}
function check_4_new_page($pdf, $height){
    $y = $pdf->GetY();     // Wenn Seitenende? Überprüfen und neue Seite anfangen
    if (($y + $height) >= 270) {
        $pdf->AddPage();
    }
}

function dashed_line($pdf, $offset){
    $pdf->SetLineStyle(array('dash' => 2, 'color' => array(0, 0, 0)));
        $y = $pdf-> GetY()+$offset ;
        $pdf->Line(25, $y, 185, $y);
        $pdf->SetLineStyle(array('dash' => 0, 'color' => array(0, 0, 0)));
}

function translateBestand($value) {
    return ($value == 0) ? 'Ja' : 'Nein';
}

function el_in_room_html_table($pdf, $result, $init_einzug) {
    $pdf ->MultiCell($init_einzug,10,"",0,"C",0,0);
    // Set column widths as percentages and headers 
    $columnWidthPercentages = array(10, 10, 8, 13, 59);
    $headers = array('ElementID',  'Variante', 'Anzahl', 'Neu/Bestand' ,'Bezeichnung'); // 'Standort', 'Verwendung',
    $pdf->SetFont('helvetica', 'B', 12);
    // Add table headers with dynamically set widths
    $html = '<table border="0">';
    $html .= '<tr>';
    foreach ($columnWidthPercentages as $index => $widthPercentage) {
        $alignStyle = ($headers[$index] == 'Neu/Bestand'|| $headers[$index] ==  'Variante'|| $headers[$index] ==  'Anzahl') ? 'text-align: center;' : ''; // Add this line for centering
        if($headers[$index] == 'Neu/Bestand'){
            $tablelabel = 'Bestand';
        } 
        else if($headers[$index] == 'Variante'){
            $tablelabel = 'Var';
        } 
        else{
            $tablelabel = $headers[$index];
        }
        $html .= '<th width="' . $widthPercentage . '%" style="' . $alignStyle . '">' . $tablelabel . '</th>';
    }
    $html .= '</tr>';
    // Set font for the data
    $pdf->SetFont('helvetica', '', 10);
    // Loop through the query results and add rows to the HTML table
    while ($row = $result->fetch_assoc()) {
        // Add data row
        $html .= '<tr>';
        foreach ($columnWidthPercentages as $index => $widthPercentage) {
            $columnName = $headers[$index];
            $cellValue = $row[$columnName] ?? '';

            // Translate 'Neu/Bestand' values
            if ($columnName == 'Neu/Bestand') {
                $cellValue = translateBestand($cellValue);
            }
            $alignStyle = ($columnName == 'Neu/Bestand' ||$columnName ==  'Variante'||$columnName ==  'Anzahl') ? 'text-align: center;' : ''; // Add this line for centering
            $html .= '<td width="' . $widthPercentage . '%" style="' . $alignStyle . '">' . $cellValue . '</td>';
        }
        $html .= '</tr>';
    }
    $html .= '</table>';

    $pdf->writeHTML($html);
}

function multicell_with_stk ($pdf, $NR, $einzug){
    if($NR>0){
            $pdf->MultiCell($einzug, 6,$NR." Stk" ,0, 'C', 0, 0);
        }else{
            $pdf->MultiCell($einzug, 6, " - " ,0, 'C', 0, 0);
        }    
}

function multicell_with_nr($pdf, $NR, $unit, $schriftgr, $einzug){
    $originalFontSize = $pdf->getFontSizePt();
    if($NR>0){
            $pdf->MultiCell($einzug, $schriftgr,$NR." ".$unit,0, 'C', 0, 0);
    }
    else{
        $pdf->MultiCell($einzug, $schriftgr, " - " ,0, 'C', 0, 0);     
        }
    $pdf->SetFontSize($originalFontSize);    
 }

function multicell_with_str($pdf, $STR, $einzug, $Unit){
    $originalFontSize = $pdf->getFontSizePt();
    if(strlen($STR) > 0){
        $pdf->MultiCell($einzug, 6, $STR." ".$Unit,0, 'C', 0, 0);
    }        
    else{
        $pdf->MultiCell($einzug, 6, " - " ,0, 'C', 0, 0);
    }
    $pdf->SetFontSize($originalFontSize);    
}

function strahlenanw($pdf, $param, $cellsize, $gr){
    $originalFontSize = $pdf->getFontSizePt();
    $pdf->SetFont(zapfdingbats, '', $gr);
    if($param==='0'){     
            $pdf->SetTextColor(255, 0, 0); 
            $pdf->MultiCell($cellsize, 6, TCPDF_FONTS::unichr(54),0, 'L', 0, 0);
    }
    else{
        if($param==='1'){
            $pdf->SetTextColor(0, 255, 0); 
            $pdf->MultiCell($cellsize, 6, TCPDF_FONTS::unichr(52),0, 'L', 0, 0);
        }
        else{
            $pdf->SetFont('helvetica', '', 10);$pdf->SetTextColor(0, 0, 0); 
            $pdf->MultiCell($cellsize, 6, "Quasi stationär",0, 'L', 0, 0);
            $pdf->Ln(1);
        }
    }
    $pdf->SetTextColor(0, 0, 0); 
    $pdf->SetFont('helvetica', '', $originalFontSize);
    
}

function hackerl($pdf, $hackerl_schriftgr, $param, $comp_true){
    $originalFontSize = $pdf->getFontSizePt();
    $hackerlcellgröße= 5; //same as global var
    $pdf->SetFont('zapfdingbats', '', $hackerl_schriftgr);
    if($param==$comp_true || $param == "Ja"|| $param == "ja"|| $param ==1){     
        $pdf->SetTextColor(0, 255, 0); 
        $pdf->MultiCell($hackerlcellgröße, $hackerl_schriftgr, TCPDF_FONTS::unichr(52),0, 'L', 0, 0);
    }
    else{
        $pdf->SetTextColor(255, 0, 0);
        $pdf->MultiCell($hackerlcellgröße, $hackerl_schriftgr, TCPDF_FONTS::unichr(54),0, 'L', 0, 0);
    }
    $pdf->SetFont('helvetica', '', $originalFontSize);
    $pdf->SetTextColor(0, 0, 0);
}
// STrahlenanwendung: else{$pdf->MultiCell(40, 6, "Quasi stationär",0, 'L', 0, 0);}

function block_label($pdf, $block_label){
    $size = 12;
    $pdf->Ln(1); 
    $pdf->SetFont('helvetica', 'B',  $size);
    $pdf->MultiCell(180,  1, "", 'T', 'C', 0, 0);
    $pdf->Ln(1);
    $pdf->MultiCell(180,  2*$size/3, $block_label, 0, 'C', 0, 1);
    //$x = $pdf->GetX();
    //$y = $pdf->GetY();
    //$style = array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => '4,2', 'color' => array(0, 0, 0));
    //$pdf->Line($x, $y , $x + $pdf->GetStringWidth($content), $y , $style);
    $pdf->SetFont('helvetica', '', 10);         
}

# generates sideways block name identifier
function block_label_sidew($pdf, $block_label, $block_height){
    $pdf->Ln(6);  
    $startX = $pdf->GetX();
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->StartTransform();
    $pdf->Rotate(90);
    $pdf->MultiCell($block_height, 10, $block_label,0, 'L',0, 0 , $startX-$block_height);
    $pdf->StopTransform();
    $pdf->Ln(6);  
    $pdf->SetFont('helvetica', '', 10);      
    
}

