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


$mysqli = utils_connect_sql();
$projectID = (int)$_SESSION["projectID"];  // Sanitize input
$sql = "
    SELECT 
        r.tabelle_projekte_idTABELLE_Projekte,
       par.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie,
        pep.tabelle_projekte_idTABELLE_Projekte,
        r.Raumnr, 
        r.Raumbezeichnung, 
        e.ElementID, 
        v.Variante,
        e.Bezeichnung AS el_Bez, 
        par.Bezeichnung, 
        pep.Wert, 
        pep.Einheit
    FROM 
        tabelle_projekt_elementparameter AS pep
    INNER JOIN 
        tabelle_parameter AS par ON pep.tabelle_parameter_idTABELLE_Parameter = par.idTABELLE_Parameter
    INNER JOIN  
        (
            tabelle_varianten AS v
        INNER JOIN 
            (
                tabelle_räume AS r
            INNER JOIN 
                (
                    tabelle_elemente AS e 
                    INNER JOIN tabelle_räume_has_tabelle_elemente AS re 
                    ON e.idTABELLE_Elemente = re.TABELLE_Elemente_idTABELLE_Elemente
                )
                ON r.idTABELLE_Räume = re.TABELLE_Räume_idTABELLE_Räume
            ) 
            ON v.idtabelle_Varianten = re.tabelle_Varianten_idtabelle_Varianten
        ) 
        ON pep.tabelle_Varianten_idtabelle_Varianten = re.tabelle_Varianten_idtabelle_Varianten 
        AND pep.tabelle_elemente_idTABELLE_Elemente = e.idTABELLE_Elemente
    WHERE 
        r.tabelle_projekte_idTABELLE_Projekte = ?
        AND par.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie = 18
        AND re.Anzahl <> 0
        AND pep.tabelle_projekte_idTABELLE_Projekte = ?
    ORDER BY 
        e.ElementID, 
        par.Bezeichnung DESC
";
if ($stmt = $mysqli->prepare($sql)) {
    $stmt->bind_param("ii", $projectID, $projectID);
    $stmt->execute();
    $result_Einbring_elemente = $stmt->get_result();
} else {
    die("Prepare failed: " . $mysqli->error);
}
$mysqli->close();
$stmt->close();


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

    // Step 1: Define parameter groups for combining
    $parameter_groups = [
        ['Einbringweg_Breite', 'Einbringweg_Breite_2'],
        ['Einbringweg_Tiefe', 'Einbringweg_Tiefe_2'],
        ['Einbringweg_Höhe', 'Einbringweg_Höhe_2'],
        ['Einbringweg_Gewicht'],
        ['Einbringweg_Flächenlast']
    ];

    $combined_parameters = [];
    foreach ($parameter_groups as $group) {
        $values = [];
        foreach ($group as $param) {
            if (isset($data['values'][$param])) {
                $values[] = $data['values'][$param]['Wert'] . (!empty($data['values'][$param]['Einheit']) ? " " . $data['values'][$param]['Einheit'] : "");
            }
        }
        if (count($values) > 0) {
            $label = parseBezeichnung($group[0]);
            // For first 3 groups use the "1/2" format if 2 values
            if (count($group) === 2) {
                $label .= (count($values) === 2) ? " 1/2" : "";
            }
            $combined_parameters[] = [
                'label' => $label . (count($values) > 1 ? ': ' : ''),
                'value' => implode(' / ', $values)
            ];
        }
    }

// Step 2: Output the rows

    $maxRows = max(count($combined_parameters), count($data['rooms']));
    for ($row = 0; $row < $maxRows; $row++) {
        // Parameter cell
        if ($row < count($combined_parameters)) {
            $pdf->MultiCell($spaces[0] * $proportions[1], $rowHeight, $combined_parameters[$row]['label'], 0, 'L', 0, 0);
            $pdf->MultiCell($spaces[0] * $proportions[0], $rowHeight, $combined_parameters[$row]['value'], 0, 'L', 0, 0);
        } else {
            $pdf->MultiCell($spaces[0] * $proportions[1], $rowHeight, "", 0, 'L', 0, 0);
            $pdf->MultiCell($spaces[0] * $proportions[0], $rowHeight, "", 0, 'L', 0, 0);
        }
        // Room cell
        if ($row < count($data['rooms'])) {
            $pdf->MultiCell($spaces[1], $rowHeight, $data['rooms'][$row] . " - " . $data['roomDescriptions'][$row], 0, 'L', 0, 1);
        } else {
            $pdf->MultiCell($spaces[1], $rowHeight, "", 0, 'L', 0, 1);
        }
        if ($pdf->GetY() > $SH) {
            $pdf->AddPage();
            $pdf->SetFont('helvetica', '', $font_size);
        }
    }


    $pdf->Ln(10);
}

ob_end_clean();
$pdf->Output(getFileName('Einbringwege'), 'I');

?>
