<?php

if (!function_exists('utils_connect_sql')) {
    include "_utils.php";
}
include 'pdf_createBericht_MYPDFclass_1.php';
include 'pdf_createBericht_utils.php';

session_start();
check_login();

function cheat($str)
{
    $outsr = $str;
    if ($outsr === "Röntgenaufnahmetisch") {
        $outsr = "Patiententisch";
    }
    if ($outsr === "Angiographieanlage - Radiologisch" || $outsr === "Angiographieanlage - Kardiologisch - 1 Ebene") {
        $outsr = "Angiographieanlage";
    }
    if ($outsr === "Unterkonstruktion Angiographieanlage - Kardiologisch" ) {
        $outsr = "Deckenschiene Röntgenanlage";
    }
    if ($outsr === "Laufband - Gewichtsentlastung - Luftpolster") {
        $outsr = "Roboterunterstützes Gehtherapiesystem ";
    }
    return $outsr;
}

function tabelle_header($pdf, $fill, $spaces, $rowHeight)
{

    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->MultiCell($spaces[0], $rowHeight, 'Raumnr./-name', 'RB', 'L', $fill, 0);
    $pdf->MultiCell($spaces[1], $rowHeight, 'Element', 'BRL', 'L', $fill, 0);
    $pdf->MultiCell($spaces[2], $rowHeight, 'Größtes Paket', 'BL', 'L', $fill, 0);
    $pdf->MultiCell($spaces[3], $rowHeight, 'Wert' . " & " . "Einheit", 'B', 'L', $fill, 1);
    $pdf->SetFont('helvetica', '', 8);
}

function parseBezeichnung($bezeichnung)
{
    // Definiere das Mapping-Array
    $mapping = [
        'Einbringweg_Breite' => 'Breite',
        'Einbringweg_Breite_2' => 'Breite 2',
        'Einbringweg_Flächenlast' => 'Flächenlast',
        'Einbringweg_Gewicht' => 'Gewicht',
        'Einbringweg_Höhe' => 'Höhe',
        'Einbringweg_Höhe_2' => 'Höhe 2',
        'Einbringweg_Tiefe' => 'Tiefe',
        'Einbringweg_Tiefe_2' => 'Tiefe 2'
    ];

    // Gib die lesbare Form zurück, falls sie im Mapping-Array existiert
    return $mapping[$bezeichnung] ?? $bezeichnung;
}

//GET DATA 
$mysqli = utils_connect_sql();
$stmt = "SELECT tabelle_räume.tabelle_projekte_idTABELLE_Projekte, tabelle_parameter.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie,
    tabelle_projekt_elementparameter.tabelle_projekte_idTABELLE_Projekte, tabelle_räume.Raumnr, 
    tabelle_räume.Raumbezeichnung, tabelle_elemente.ElementID, tabelle_varianten.Variante,
    tabelle_elemente.Bezeichnung as el_Bez, tabelle_parameter.Bezeichnung, tabelle_projekt_elementparameter.Wert, tabelle_projekt_elementparameter.Einheit
    FROM (tabelle_projekt_elementparameter INNER JOIN (tabelle_varianten INNER JOIN (tabelle_räume INNER JOIN (tabelle_elemente INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_elemente.idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) ON tabelle_varianten.idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) ON (tabelle_projekt_elementparameter.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) AND (tabelle_projekt_elementparameter.tabelle_elemente_idTABELLE_Elemente = tabelle_elemente.idTABELLE_Elemente)) INNER JOIN tabelle_parameter ON tabelle_projekt_elementparameter.tabelle_parameter_idTABELLE_Parameter = tabelle_parameter.idTABELLE_Parameter
    WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . ")"
    . " AND ((tabelle_parameter.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie)=18) "
    . "AND ((tabelle_projekt_elementparameter.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . "))"
    . "ORDER BY Raumbezeichnung, ElementID, Bezeichnung DESC";
$result_Einbring_elemente = $mysqli->query($stmt);
$mysqli->close();
$rooms = array();
while ($row = $result_Einbring_elemente->fetch_assoc()) {
    if (!in_array($row["Raumnr"], $rooms)) {
        $rooms[] = $row["Raumnr"];
    }
    $data_array[] = $row;
}


//     -----   FORMATTING VARIABLES    -----     
$marginTop = 20; // https://tcpdf.org/docs/srcdoc/TCPDF/files-config-tcpdf-config/ 
$marginBTM = 10;
$SB = 210 - 2 * PDF_MARGIN_LEFT;  // A4: 210 x 297 // A3: 297 x 420
$SH = 297 - $marginTop - $marginBTM; // PDF_MARGIN_FOOTER;
$rowHeight = 4;
$font_size = 8;

$colour_line = array(199 - 100, 215 - 100, 169 - 100);
$style_dashed = array('width' => 0.1, 'cap' => 'round', 'join' => 'round', 'dash' => 4, 'color' => $colour_line); //$pdf->SetLineStyle(array('width' => 0.1, 'cap' => 'round', 'join' => 'round', 'dash' => 6, 'color' => array(110, 150, 80)));
$style_normal = array('width' => 0.3, 'cap' => 'round', 'join' => 'round', 'dash' => 0, 'color' => $colour_line);

$fill = false;
$proportions = array(0.2, 0.3, 0.25, 0.25);
$spaces = array();
foreach ($proportions as $prop) {
    $spaces[] = $SB * $prop;
}
$counter = 0;
$last_el_id = "";
//
//     -----     MAKE PDF    -----     
$pdf = new MYPDF('P', PDF_UNIT, "A4", true, 'UTF-8', false, true);
$pdf = init_pdf_attributes($pdf, PDF_MARGIN_LEFT, $marginTop, $marginBTM, "A4", "Einbringwege");
$pdf->AddPage('P', 'A4');

$pdf->SetFont('helvetica', '', $font_size);
$pdf->SetLineStyle($style_dashed);
$pdf->SetFillColor(220, 230, 210);
$pdf->SetLineStyle($style_normal);

tabelle_header($pdf, $fill, $spaces, $rowHeight);
foreach ($rooms as $roomnr) {
    $first_data_entry = true;
    $fill = false;
    $roomEmpty = false;

    foreach ($data_array as $key => $entry) {
        if ($entry['Raumnr'] === $roomnr && $roomnr != "x.x.x.") {

            if ($entry['Wert']) {
                if (check_4_new_page($pdf, -8)) {
                    $fill = false;
                    $first_data_entry = true;
                    tabelle_header($pdf, $fill, $spaces, $rowHeight);
                }


                if ($last_el_id != $entry['el_Bez']) {
                    $pdf->MultiCell($spaces[0], 1, "", 0, 'L', 0, 0);
                    $pdf->MultiCell($SB - $spaces[0], 1, "", "T", 'B', 0, 0);
                    $pdf->Ln(0);
                    if (check_4_new_page($pdf, 20)) {
                        $fill = false;
                        tabelle_header($pdf, $fill, $spaces, $rowHeight);
                        $first_data_entry = true;
                    }
                }

                if ($first_data_entry) {
                    $pdf->MultiCell($spaces[0], $rowHeight, $entry['Raumnr'] . " \n" . $entry['Raumbezeichnung'], "T", 'L', 0, 0);
                } else {
                    $pdf->MultiCell($spaces[0], $rowHeight, "", 0, 'L', 0, 0);
                }

                $outstr = cheat($entry['el_Bez']);
                if ($last_el_id != $entry['el_Bez']) {
                    $pdf->MultiCell($spaces[1], $rowHeight, $outstr, 'L', 'LT', $fill, 0); // $entry['ElementID'] . " " . 
                } else {
                    $pdf->MultiCell($spaces[1], $rowHeight, "", 'L', 'L', $fill, 0);
                }
                if( $entry['Wert'] <> ""){
                $pdf->MultiCell($spaces[2], $rowHeight, parseBezeichnung($entry['Bezeichnung']) . ": ", "L", 'L', $fill, 0);
                $pdf->MultiCell($spaces[3], $rowHeight, $entry['Wert'] . " " . $entry["Einheit"], 0, 'L', $fill, 1);}

                $first_data_entry = false;
                $fill = !$fill;
                $last_el_id = $entry['el_Bez'];
            }
        }
    }
}

ob_end_clean(); // brauchts irgendwie.... ?  
$pdf->Output('Einbringwege-MT.pdf', 'I');

