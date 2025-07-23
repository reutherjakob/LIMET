<?php
#2025done

require_once '../utils/_utils.php';
require_once('../TCPDF-main/TCPDF-main/tcpdf.php');
include 'pdf_createBericht_MYPDFclass_A4_Raumbuch.php';
include '_pdf_createBericht_utils.php';

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
    if ($outsr === "Unterkonstruktion Angiographieanlage - Kardiologisch") {
        $outsr = "Deckenschiene Röntgenanlage";
    }
    if ($outsr === "Laufband - Gewichtsentlastung - Luftpolster") {
        $outsr = "Roboterunterstützes Gehtherapiesystem ";
    }
    return $outsr;
}

function tabelle_header($pdf, $fill, $spaces, $rowHeight)
{
    // Draw transparent fill rectangles for header background
    $pdf->SetFillColor(104, 140, 3); // your header fill color
    $pdf->SetAlpha(0.1);
    $x = $pdf->GetX();
    $y = $pdf->GetY() + 5; // to match the Ln(5) offset
    $pdf->Rect($x, $y, $spaces[0], $rowHeight, 'F');
    $pdf->Rect($x + $spaces[0], $y, $spaces[1], $rowHeight, 'F');
    $pdf->Rect($x + $spaces[0] + $spaces[1], $y, $spaces[2], $rowHeight, 'F');
    $pdf->Rect($x + $spaces[0] + $spaces[1] + $spaces[2], $y, $spaces[3], $rowHeight, 'F');
    $pdf->SetAlpha(1); // reset for solid header text

    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Ln(5);
    $pdf->MultiCell($spaces[0], $rowHeight, 'Raumnr./-name', 'RB', 'L', 0, 0);
    $pdf->MultiCell($spaces[1], $rowHeight, 'Element', 'BRL', 'L', 0, 0);
    $pdf->MultiCell($spaces[2], $rowHeight, 'Größtes Paket', 'BL', 'L', 0, 0);
    $pdf->MultiCell($spaces[3], $rowHeight, 'Wert' . " & " . "Einheit", 'B', 'L', 0, 1);
    $pdf->SetFont('helvetica', '', 8);
}

function parseBezeichnung($bezeichnung)
{
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
    . " AND ((tabelle_räume_has_tabelle_elemente.Anzahl)<>0) "

    . "AND ((tabelle_projekt_elementparameter.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . "))"
    . "ORDER BY Raumbezeichnung, ElementID, Bezeichnung DESC";


$result_Einbring_elemente = $mysqli->query($stmt);

$rooms = array();
while ($row = $result_Einbring_elemente->fetch_assoc()) {
    if (!in_array($row["Raumnr"], $rooms)) {
        $rooms[] = $row["Raumnr"];
    }
    $data_array[] = $row;
}


//     -----   FORMATTING VARIABLES    -----
$marginTop = 17; // https://tcpdf.org/docs/srcdoc/TCPDF/files-config-tcpdf-config/
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

$_SESSION["PDFTITEL"] = "Einbringung von medizinischen Großgeräten";
$_SESSION["DisclaimerText"] = "Die beschriebenen Komponenten weisen die jeweils größten Abmessungen "
    . "und/oder Gewichtslasten je Anlage auf. Die vollständigen Systeme bestehen aus"
    . " mehreren, hier nicht angeführten Elementen, die jedoch kleiner und/oder leichter als"
    . " das größte bzw. schwerste Einzelteil sind. Die angegebenen Werte sind produktneutrale"
    . " Maximalspezifikationen, was bedeutet, dass beispielsweise das Gewicht von Leitfabrikat "
    . "A und die Abmessungen von Leitfabrikat B verwendet wurden. Die hier angeführten Parameter"
    . " dienen exklusiv der Bestimmung der Einbringwege.";
$_SESSION["PDFHeaderSubtext"] = "Projekt: " . $_SESSION["projectName"] . " - PPH: " . $_SESSION["projectPlanungsphase"];
$pdf = new MYPDF('P', PDF_UNIT, "A4", true, 'UTF-8', false, true);
$pdf = init_pdf_attributes($pdf, PDF_MARGIN_LEFT, $marginTop, $marginBTM, "A4", "Großgeräte Einbringung");

$pdf->AddPage('P', 'A4');
$pdf->SetFont('helvetica', '', $font_size);
$pdf->SetLineStyle($style_dashed);
$pdf->SetFillColor(100, 140, 0);
 $pdf->SetAlpha(0.1);
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

                // --- Calculate X and Y for the row ---
                $rowX = $pdf->GetX();
                $rowY = $pdf->GetY();

                // --- Draw row fill if $fill is true (alternating rows) ---
                if ($fill) {
                    $pdf->SetAlpha(0.15); // your desired opacity (0=transparent; 1=opaque)
                    $pdf->SetFillColor(100, 140, 0);
                    $pdf->Rect($rowX, $rowY, $SB, $rowHeight, 'F');
                    $pdf->SetAlpha(1); // reset alpha for text
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
                    $pdf->MultiCell($spaces[1], $rowHeight, $outstr, 'L', 'LT', 0, 0); // fill=0: never transparent text!
                } else {
                    $pdf->MultiCell($spaces[1], $rowHeight, "", 'L', 'L', 0, 0);
                }
                if ($entry['Wert'] <> "") {
                    $pdf->MultiCell($spaces[2], $rowHeight, parseBezeichnung($entry['Bezeichnung']) . ": ", "L", 'L', 0, 0);
                    $pdf->MultiCell($spaces[3], $rowHeight, $entry['Wert'] . " " . $entry["Einheit"], 0, 'L', 0, 1);
                }

                $first_data_entry = false;
                $fill = !$fill;
                $last_el_id = $entry['el_Bez'];
            }
        }
    }

}
$mysqli->close();
ob_end_clean(); // brauchts irgendwie.... ?
$pdf->Output(getFileName('Einbringwege'), 'I');
$_SESSION["PDFHeaderSubtext"] = "";

