<?php
#2025done

if (!function_exists('utils_connect_sql')) {
    include "../utils/_utils.php";
}
require_once('TCPDF-main/TCPDF-main/tcpdf.php');
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
    if ($outsr === "Unterkonstruktion Angiographieanlage - Kardiologisch" || $outsr === "Gerätetrageschiene 400cm") {
        $outsr = "Deckenschiene Röntgenanlage";
    }
    if ($outsr === "Laufband - Gewichtsentlastung - Luftpolster") {
        $outsr = "Roboterunterstützes Gehtherapiesystem ";
    }
    return $outsr;
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

$parameters_order = ['Einbringweg_Breite', 'Einbringweg_Tiefe', 'Einbringweg_Höhe', 'Einbringweg_Gewicht', 'Einbringweg_Flächenlast'];
//GET DATA
$mysqli = utils_connect_sql();
$stmt = "SELECT tabelle_räume.tabelle_projekte_idTABELLE_Projekte, tabelle_parameter.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie,
    tabelle_projekt_elementparameter.tabelle_projekte_idTABELLE_Projekte, tabelle_räume.Raumnr, 
    tabelle_räume.Raumbezeichnung, tabelle_elemente.ElementID, tabelle_varianten.Variante,
    tabelle_elemente.Bezeichnung as el_Bez, tabelle_parameter.Bezeichnung, tabelle_projekt_elementparameter.Wert, tabelle_projekt_elementparameter.Einheit
    FROM (tabelle_projekt_elementparameter INNER JOIN (tabelle_varianten INNER JOIN (tabelle_räume INNER JOIN (tabelle_elemente INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_elemente.idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) ON tabelle_varianten.idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) ON (tabelle_projekt_elementparameter.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) AND (tabelle_projekt_elementparameter.tabelle_elemente_idTABELLE_Elemente = tabelle_elemente.idTABELLE_Elemente)) INNER JOIN tabelle_parameter ON tabelle_projekt_elementparameter.tabelle_parameter_idTABELLE_Parameter = tabelle_parameter.idTABELLE_Parameter
    WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . ")  "
    . " AND ((tabelle_parameter.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie)=18) "
    . "AND ((tabelle_projekt_elementparameter.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . "))"
    . "ORDER BY el_Bez, Raumnr, Bezeichnung DESC";
$result_Einbring_elemente = $mysqli->query($stmt);


// Organisiere Daten nach Elementen
$elements = array();
while ($row = $result_Einbring_elemente->fetch_assoc()) {
    if (!isset($elements[$row['el_Bez']])) {
        $elements[$row['el_Bez']] = array(
            'parameters' => array(),
            'rooms' => array(),
            'roomDescriptions' => array(),
            'values' => array()
        );
    }

    // Parameter hinzufügen
    if (!in_array($row['Bezeichnung'], $elements[$row['el_Bez']]['parameters'])) {
        $elements[$row['el_Bez']]['parameters'][] = $row['Bezeichnung'];
    }

    // Räume hinzufügen
    if (!in_array($row['Raumnr'], $elements[$row['el_Bez']]['rooms'])) {
        $elements[$row['el_Bez']]['rooms'][] = $row['Raumnr'];
        $elements[$row['el_Bez']]['roomDescriptions'][] = $row['Raumbezeichnung'];
    }

    // Werte und Einheiten hinzufügen
    $elements[$row['el_Bez']]['values'][$row['Bezeichnung']] = array(
        'Wert' => $row['Wert'],
        'Einheit' => $row['Einheit']
    );
}

//     -----   FORMATTING VARIABLES    -----
$marginTop = 20;
$marginBTM = 10;
$SB = 210 - 2 * PDF_MARGIN_LEFT;
$SH = 297 - $marginTop - $marginBTM;
$rowHeight = 4;
$font_size = 8;

$colour_line = array(199 - 100, 215 - 100, 169 - 100);
$style_dashed = array('width' => 0.1, 'cap' => 'round', 'join' => 'round', 'dash' => 4, 'color' => $colour_line);
$style_normal = array('width' => 0.3, 'cap' => 'round', 'join' => 'round', 'dash' => 0, 'color' => $colour_line);

$fill = false;
$proportions = array(0.6, 0.4); // Verhältnis der Spaltenbreiten
$spaces = array();
foreach ($proportions as $prop) {
    $spaces[] = $SB * $prop;
}

$_SESSION["PDFTITEL"] = "Einbringung von medizinischen Großgeräten";
$_SESSION["DisclaimerText"] = "Die beschriebenen Komponenten weisen die jeweils größten Abmessungen "
    . "und/oder Gewichtslasten je Anlage auf. Die vollständigen Systeme bestehen aus"
    . " mehreren, hier nicht angeführten Elementen, die jedoch kleiner und/oder leichter als"
    . " das größte bzw. schwerste Einzelteil sind. Die angegebenen Werte sind produktneutrale"
    . " Maximalspezifikationen, was bedeutet, dass beispielsweise das Gewicht von Leitfabrikat "
    . "A und die Abmessungen von Leitfabrikat B verwendet wurden. Die hier angeführten Parameter"
    . " dienen exklusiv der Bestimmung der Einbringwege.";

$pdf = new MYPDF('P', PDF_UNIT, "A4", true, 'UTF-8', false, true);
$pdf = init_pdf_attributes($pdf, PDF_MARGIN_LEFT, $marginTop, $marginBTM, "A4", "Großgeräte Einbringung");
$pdf->AddPage('P', 'A4');
$pdf->SetFont('helvetica', '', $font_size);
$pdf->SetLineStyle($style_dashed);
$pdf->SetFillColor(220, 230, 210);
$pdf->SetLineStyle($style_normal);


foreach ($elements as $element => $data) {

    if ($pdf->GetY() > $SH - 50) {
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', $font_size);
    }

    $pdf->SetFont('helvetica', 'B', 10);
    $element = cheat($element); // Verwenden Sie die cheat-Funktion
    $pdf->MultiCell($SB, 6, $element, 0, 'L', 0, 0);
    $pdf->Ln(6);

    // Parameter und Räume nebeneinander ausgeben
    $pdf->SetFont('helvetica', 'B', $font_size);
    $pdf->MultiCell($spaces[0] * $proportions[1], $rowHeight, 'Parameter', 'B', 'L', 0, 0);
    $pdf->MultiCell($spaces[0] * $proportions[0], $rowHeight, 'Wert/Einheit', 'B', 'L', 0, 0);
    $pdf->MultiCell($spaces[1], $rowHeight, 'Räume', 'B', 'L', 0, 1);

    $pdf->SetFont('helvetica', '', $font_size);
    $parameterIndex = 0;
    $roomIndex = 0;

    // Filter and sort parameters
    $sorted_parameters = [];
    foreach ($parameters_order as $param) {
        if (isset($data['values'][$param])) {
            $sorted_parameters[] = $param;
        }
    }

    while ($parameterIndex < count($sorted_parameters) || $roomIndex < count($data['rooms'])) {

        $y = $pdf->GetY();

        if ($parameterIndex < count($sorted_parameters)) {
            $parameter = $sorted_parameters[$parameterIndex];
            $pdf->SetXY($pdf->GetX(), $y);
            if (str_replace(' ', '',  $data['values'][$parameter]['Wert'] . $data['values'][$parameter]['Einheit']) === "") {
                $pdf->MultiCell($spaces[0], $rowHeight, "", 0, 'L', 0, 0);
            } else {
                $pdf->MultiCell($spaces[0] * $proportions[1], $rowHeight, parseBezeichnung($parameter), 0, 'L', 0, 0);
                $pdf->MultiCell($spaces[0] * $proportions[0], $rowHeight, $data['values'][$parameter]['Wert'] . " " . $data['values'][$parameter]['Einheit'], 0, 'L', 0, 0);
            }
            $parameterIndex++;
        } else {
            $pdf->SetXY($pdf->GetX(), $y);
            $pdf->MultiCell($spaces[0] * $proportions[1], $rowHeight, "", 0, 'L', 0, 0);
            $pdf->MultiCell($spaces[0] * $proportions[0], $rowHeight, "", 0, 'L', 0, 0);
        }

        // Räume ausgeben
        if ($roomIndex < count($data['rooms'])) {
            $pdf->MultiCell($spaces[1], $rowHeight, $data['rooms'][$roomIndex] . " - " . $data['roomDescriptions'][$roomIndex], 0, 'L', 0, 1);
            $roomIndex++;
        } else {
            $pdf->SetXY($pdf->GetX() + $spaces[0], $y);
            $pdf->MultiCell($spaces[1], $rowHeight, "", 0, 'L', 0, 1);
        }

        // Prüfen, ob eine neue Seite erforderlich ist
        if ($pdf->GetY() > $SH) {
            $pdf->AddPage();
            $pdf->SetFont('helvetica', '', $font_size);
        }
    }

    $pdf->Ln(10);
}

$mysqli->close();
ob_end_clean();
$pdf->Output(getFileName( 'Einbringwege'), 'I');

?>
