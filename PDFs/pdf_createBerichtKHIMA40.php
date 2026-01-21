<?php
#2025done
require_once '../utils/_utils.php';
check_login();

require_once('../TCPDF-main/TCPDF-main/tcpdf.php');
include "pdf_createBericht_MYPDFclass_A4_ohneTitelblatt.php";
include "_pdf_createBericht_utils.php";

$marginTop = 20;
$marginBTM = 10;

$_SESSION["PDFTITEL"] = "Raumbuch";
$_SESSION["PDFHeaderSubtext"] = "Versorgungsgebäude BT0";
$_SESSION["PDFTITELBLATT"] = "KHI Quest";

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false, true);
$pdf = init_pdf_attributes($pdf, PDF_MARGIN_LEFT, $marginTop, $marginBTM, "A4", "Raumbuch");
$mysqli = utils_connect_sql();

$roomIDs = filter_input(INPUT_GET, 'roomID');
$teile = explode(",", $roomIDs);

function ensure_space_for_fullwidth_text($pdf, $section, $row)
{
    // Nur für fullwidth sections mit text-Feldern relevant
    if (empty($section['layout']) || $section['layout'] !== 'fullwidth') {
        return;
    }

    foreach ($section['fields'] as $field) {
        if (empty($field['type']) || $field['type'] !== 'text') {
            continue;
        }

        // Text-Wert holen
        $fieldValue = '';
        if ($field['fetch_data'] === 'sql') {
            $fieldValue = $row[$field['dataname']] ?? '';
        } elseif ($field['fetch_data'] === 'session') {
            $fieldValue = $_SESSION[$field['dataname']] ?? '';
        }

        if (empty($fieldValue)) {
            continue;
        }

        // Echte Höhe berechnen
        $h = $pdf->getStringHeight(180, $fieldValue); // 180 = Seitenbreite minus Margins

        // Platz prüfen
        $y = $pdf->GetY();
        $pageHeight = $pdf->getPageHeight();
        $margins = $pdf->getMargins();
        $bottom = $pageHeight - $margins['bottom'];

        if ($y + $h + 3 > $bottom) {
            $pdf->AddPage();
        }
    }
}


$sql_elemente = "SELECT  
    tabelle_elemente.idTABELLE_Elemente,
    tabelle_elemente.Bezeichnung,
    tabelle_räume_has_tabelle_elemente.Anzahl   
FROM tabelle_räume_has_tabelle_elemente
INNER JOIN tabelle_elemente ON tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente = 
                                tabelle_elemente.idTABELLE_Elemente
WHERE tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume = ?
GROUP BY tabelle_elemente.idTABELLE_Elemente, tabelle_elemente.Bezeichnung
ORDER BY tabelle_elemente.Bezeichnung ASC";

$outputparameter = [
    "basic" => [
        "title" => "Raum",
        "layout" => "columns",
        "fields" => [
            ["name" => "Raumbezeichnung", "fetch_data" => "sql", "dataname" => "Raumbezeichnung"],
            ["name" => "Topograf. Raumnummer", "fetch_data" => "sql", "dataname" => "Raumnummer_Nutzer"],
            ["name" => "Raumbezeichnung Zusatz", "fetch_data" => "sql", "dataname" => "Anmerkung AR"],
            ["name" => "Funktionale Raumnummer", "fetch_data" => "sql", "dataname" => "Funktionale Raumnummer Raum Nr"],
            ["name" => "Standort", "fetch_data" => "session", "dataname" => "projectName"],
            ["name" => "Raumzone Nr.", "fetch_data" => "sql", "dataname" => "Raumzone_Nr"],
            ["name" => "Bauteil", "fetch_data" => "sql", "dataname" => "Bauabschnitt"],
            ["name" => "Funktionsteilstelle Nr.", "fetch_data" => "sql", "dataname" => "Funktionsteilstelle_Nr"],

            ["name" => "Ebene", "fetch_data" => "sql", "dataname" => "Geschoss"],
            ["name" => "Funktionsstelle", "fetch_data" => "sql", "dataname" => "Raumbereich Nutzer"],
            ["name" => "Raumzone", "fetch_data" => "sql", "dataname" => "Anmerkung allgemein"],

        ]
    ],
    "functional" => [
        "title" => "Raumdaten Architektur",
        "layout" => "columns",
        "fields" => [
            ["name" => "Nutzfläche [m2]", "fetch_data" => "sql", "dataname" => "Nutzfläche"],
            ["name" => "Umfang [m]", "fetch_data" => "sql", "dataname" => "Umfang"],
            ["name" => "Lichte Raumhöhe [m]", "fetch_data" => "sql", "dataname" => "Raumhoehe"],
            ["name" => "Rohbauhöhe [m]", "fetch_data" => "sql", "dataname" => "Raumhoehe 2"],
            ["name" => "Deckenaufbau", "fetch_data" => "sql", "dataname" => "Decke"],
            ["name" => "Bodenbelag", "fetch_data" => "sql", "dataname" => "Fussboden"],
            ["name" => "Nutzungsbereich ON 1800", "fetch_data" => "sql", "dataname" => "AR_Nutzung_ON1800"],
            ["name" => "Bodenaufbau", "fetch_data" => "sql", "dataname" => "AR_Bodenaufbau"],
            ["name" => "Rutschfestigkeit Boden", "fetch_data" => "sql", "dataname" => "AR_Boden_Rutschfestigkeit"],
            ["name" => "Arbeitsplätze", "fetch_data" => "sql", "dataname" => "AR_AP_permanent"],
            ["name" => "Funktion", "fetch_data" => "sql", "dataname" => "Anmerkung FunktionBO", "type" => "text"],
        ]
    ],


    "HT" => [
        "title" => "Haustechnik",
        "layout" => "columns",
        "fields" => [
            ["name" => "Raumklasse H6020", "fetch_data" => "sql", "dataname" => "H6020"],
            ["name" => "Luftwechselrate [1/h]", "fetch_data" => "sql", "dataname" => "HT_Luftwechsel 1/h"],
            ["name" => "Reinraumklasse ISO 14644", "fetch_data" => "sql", "dataname" => "GMP"],
            ["name" => "Luftmenge Zuluft [m3/h]", "fetch_data" => "sql", "dataname" => "HT_Luftmenge m3/h"],
            ["name" => "Luftmenge Abluft [m3/h]", "fetch_data" => "sql", "dataname" => "HT_Luftmenge Abluft m3/h"],
            ["name" => "Raumtemperatur Sommer [°C]", "fetch_data" => "sql", "dataname" => "HT_Raumtemp Sommer °C"],
            ["name" => "Raumtemperatur Winter [°C]", "fetch_data" => "sql", "dataname" => "HT_Raumtemp Winter °C"],
        ]
    ],

    "electrical" => [
        "title" => "Elektrotechnik",
        "layout" => "columns",
        "fields" => [
            ["name" => "Beleuchtungsstärke [lx]", "fetch_data" => "sql", "dataname" => "EL_Beleuchtungsstaerke"],
            ["name" => "Ableitfähiger Boden", "fetch_data" => "sql", "dataname" => "Fussboden OENORM B5220"],
        ]
    ],

// Medizintechnik – mit Elementenliste
    "technical" => [
        "title" => "Medizintechnik",
        "layout" => "elements_list",  // Neuer Layout-Typ
        "sql_query" => $sql_elemente,  // Separate SQL Query
        "fields" => [ ]
    ],


    // Küchentechnik – nur Text
    "technical2" => [
        "title" => "Küchentechnik",
        "layout" => "fullwidth",
        "fields" => [
            ["name" => "Küchentechnik", "fetch_data" => "sql", "dataname" => "Anmerkung Kuechentechnik", "type" => "text"]
        ]
    ],

    // FTS und Rohrpost – nur Text
    "technical3" => [
        "title" => "FTS und Rohrpost",
        "layout" => "fullwidth",
        "fields" => [
            ["name" => "FTS und Rohrpost", "fetch_data" => "sql", "dataname" => "Anmerkung Rohrpost", "type" => "text"]
        ]
    ],
];

// ============================================================================
// SQL QUERY - SELECT ALL FIELDS
// ============================================================================
$sql = "SELECT 
    tabelle_räume.idTABELLE_Räume,
    tabelle_räume.Raumnummer_Nutzer,
    tabelle_räume.Raumbezeichnung,
    tabelle_räume.`Anmerkung AR`,
    tabelle_projekte.Projektname, 
    tabelle_räume.Bauabschnitt,
    tabelle_räume.`Raumbereich Nutzer`,
    tabelle_räume.`Anmerkung allgemein`,
    tabelle_räume.`Funktionelle Raum Nr`,
    tabelle_räume.Geschoss,
    tabelle_räume.GMP,    
    SUBSTRING_INDEX(tabelle_räume.`Funktionelle Raum Nr`, '.', 3) AS Funktionsteilstelle_Nr,
    -- Raumzone Nr. = x.yy.zz.ww
    SUBSTRING_INDEX(tabelle_räume.`Funktionelle Raum Nr`, '.', 4) AS Raumzone_Nr, 
    tabelle_räume.Fussboden,
    tabelle_räume.Decke,
    tabelle_räume.Nutzfläche,
    tabelle_räume.Umfang,
    tabelle_räume.Raumhoehe,
    tabelle_räume.`Raumhoehe 2`,
    tabelle_räume.AR_Nutzung_ON1800,
    tabelle_räume.AR_Bodenaufbau,
    tabelle_räume.`Fussboden OENORM B5220`,
    tabelle_räume.AR_Boden_Rutschfestigkeit,
    tabelle_räume.AR_AP_permanent,
    tabelle_räume.H6020,
    tabelle_räume.`HT_Luftwechsel 1/h`,
    tabelle_räume.`HT_Luftmenge m3/h`,
    tabelle_räume.`HT_Luftmenge Abluft m3/h`,
    tabelle_räume.`HT_Raumtemp Sommer °C`,
    tabelle_räume.`HT_Raumtemp Winter °C`,
    tabelle_räume.EL_Beleuchtungsstaerke,
    tabelle_räume.`Anmerkung Geräte`,
    tabelle_räume.`Anmerkung Elektro`,
    tabelle_räume.`Anmerkung HKLS`,
    tabelle_räume.`Anmerkung FunktionBO`,
    tabelle_räume.`Anmerkung Kuechentechnik`,
    tabelle_räume.`Anmerkung Rohrpost`
FROM tabelle_räume
INNER JOIN tabelle_projekte
    ON tabelle_räume.tabelle_projekte_idTABELLE_Projekte = 
       tabelle_projekte.idTABELLE_Projekte
INNER JOIN tabelle_funktionsteilstellen
    ON tabelle_räume.TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen =
       tabelle_funktionsteilstellen.idTABELLE_Funktionsteilstellen
INNER JOIN tabelle_funktionsstellen
    ON tabelle_funktionsteilstellen.TABELLE_Funktionsstellen_idTABELLE_Funktionsstellen =
       tabelle_funktionsstellen.idTABELLE_Funktionsstellen
INNER JOIN tabelle_planungsphasen
    ON tabelle_projekte.TABELLE_Planungsphasen_idTABELLE_Planungsphasen =
       tabelle_planungsphasen.idTABELLE_Planungsphasen
WHERE tabelle_räume.idTABELLE_Räume = ?
";

$text2big = false;
function get_multicell_height($pdf, $width, $text)
{
    // Calculate actual height of text when rendered in MultiCell
    // Returns number of lines the text will occupy
    $h = $pdf->getStringHeight($width, $text);
    // Standard line height is typically 5mm, so divide to get line count
    $lineHeight = 5;
    $lines = ceil($h / $lineHeight);
    return $lines > 1;
}

// ============================================================================
// PDF GENERATION LOOP
// ============================================================================
foreach ($teile as $valueOfRoomID) {
    $pdf->AddPage('P', 'A4');

    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('i', $valueOfRoomID);
    $stmt->execute();
    $result = $stmt->get_result();


    if ($row = $result->fetch_assoc()) {

        // ====================================================================
        // ITERATE THROUGH SECTIONS AND RENDER FIELDS
        // ====================================================================
        foreach ($outputparameter as $sectionKey => $section) {
            if ($pdf->GetY() > 275) {
                $pdf->AddPage('P', 'A4');
            }
            ensure_space_for_fullwidth_text($pdf, $section, $row);
            $pdf->SetFont('helvetica', 'B', 11);
            $pdf->SetFillColor(215, 222, 197);
            $pdf->MultiCell(0, 7, $section['title'], 0, 1, 'L', true);
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetFont('helvetica', '', 8);


            // ================================================================
            // RENDER FIELDS IN SECTION (layout aware)
            // ================================================================
            // TWO-COLUMN LAYOUT
            if (!empty($section['layout']) && $section['layout'] === 'columns') {
                $pdf->Ln(2);

                $pdf->SetFont('helvetica', '', 8);
                $fieldCount = count($section['fields']);
                $fieldsPerCol = ceil($fieldCount / 2);

                foreach ($section['fields'] as $index => $field) {
                    $fieldName = $field['name'];
                    $fieldValue = '';

                    if ($field['fetch_data'] === 'sql') {
                        $dataname = $field['dataname'];
                        $fieldValue = $row[$dataname] ?? '';
                    } elseif ($field['fetch_data'] === 'session') {
                        $dataname = $field['dataname'];
                        $fieldValue = trim($_SESSION[$dataname] ?? '');
                        $fieldValue = str_replace(["\r\n", "\r", "\n"], '', $fieldValue);
                    }

                    if (!empty($field['type']) && $field['type'] === 'text') {
                        if (empty($fieldValue) || $fieldValue === '') {
                            $fieldValue = "Keine Angaben hinterlegt.";
                        }
                        $pdf->Ln();
                        $pdf->MultiCell(15, 5, $fieldName . ':', 0, 0, 'L', 0); // 1 Spalte Label
                        $pdf->MultiCell(165, 5, $fieldValue, 0, 0, 'L', 0);    // 3 Spalten Text
                        $pdf->Ln(1);

                    } else {
                        if (empty($fieldValue) || $fieldValue === '') {
                            $fieldValue = "-";
                        }
                        $text2big = get_multicell_height($pdf, 45, $fieldValue) || $text2big;
                        $pdf->MultiCell(45, 5, $fieldName . ':', 0, 0, 'L', 0);
                        $pdf->MultiCell(45, 5, $fieldValue, 0, 0, 'L', 0);
                        $pdf->MultiCell(5, 5, "", 0, 0, 'L', 0);


                        if ($index % 2 > 0 &&isset($section['fields'][$index + 1])) {
                            $pdf->Ln();
                            if ($text2big) {
                                $pdf->Ln(5);
                                $text2big = false;
                            }

                        }
                    }
                }
                $pdf->Ln();
                $pdf->SetLineWidth(0.1);
                $pdf->Line(15, $pdf->GetY(), 195, $pdf->GetY());
                $pdf->SetLineWidth(0.4);

            } elseif (!empty($section['layout']) && $section['layout'] === 'fullwidth') {
                $pdf->Ln(2);

                foreach ($section['fields'] as $field) {
                    $fieldValue = '';
                    if ($field['fetch_data'] === 'sql') {
                        $dataname = $field['dataname'];
                        $fieldValue = $row[$dataname] ?? '';
                    } elseif ($field['fetch_data'] === 'session') {
                        $dataname = $field['dataname'];
                        $fieldValue = $_SESSION[$dataname] ?? '';
                    }

                    if (!empty($field['type']) && $field['type'] === 'text') {
                        $pdf->SetFont('helvetica', '', 8);
                        if ($fieldValue === '') {
                            $fieldValue = 'Keine Daten für diesen Raum hinterlegt.';
                        }
                        $pdf->MultiCell(0, 5, $fieldValue, 0, 'L');
                    }
                }
                $pdf->Ln(1);
                $pdf->SetLineWidth(0.1);
                $pdf->Line(15, $pdf->GetY(), 195, $pdf->GetY());
                $pdf->SetLineWidth(0.4);

            } elseif (!empty($section['layout']) && $section['layout'] === 'elements_list') {
                $pdf->Ln(0);

                $stmt_elem = $mysqli->prepare($section['sql_query']);
                $stmt_elem->bind_param('i', $valueOfRoomID);
                $stmt_elem->execute();
                $result_elem = $stmt_elem->get_result();
                $elemente = [];
                while ($row_elem = $result_elem->fetch_assoc()) {
                    $elemente[] = $row_elem;
                }
                if (!empty($elemente)) {
                    $pdf->MultiCell(15, 5, 'Anzahl', "B", 'L', 0, 0);
                    $pdf->MultiCell(75, 5, 'Element', "B", 'L', 0, 0);
                    $pdf->MultiCell(15, 5, 'Anzahl', "B", 'L', 0, 0);
                    $pdf->MultiCell(75, 5, 'Element', "B", 'L', 0, 0);
                    $pdf->Ln();
                    $counter = 0;
                    foreach ($elemente as $elem) {
                        if ($elem['Anzahl'] > 0) {
                            if ($pdf->GetY() + 5 > 275) {
                                $pdf->AddPage('P', 'A4');
                                $pdf->MultiCell(15, 5, 'Anzahl', "B", 'L', 0, 0);
                                $pdf->MultiCell(75, 5, 'Element', "B", 'L', 0, 0);
                                $pdf->MultiCell(15, 5, 'Anzahl', "B", 'L', 0, 0);
                                $pdf->MultiCell(75, 5, 'Element', "B", 'L', 0, 0);
                                $pdf->Ln();
                            }
                            $pdf->MultiCell(5, 5, $elem['Anzahl'], 0, 'C', 0, 0);
                            $pdf->MultiCell(85, 5, $elem['Bezeichnung'], 0, 'L', 0, 0);
                            if ($counter % 2 > 0) {
                                $pdf->Ln();
                            }
                            $counter++;
                        }
                    }
                } else {
                    $pdf->SetFont('helvetica', '', 8);
                    $pdf->MultiCell(0, 5, 'Keine Elemente für diesen Raum erfasst.', 0, 'L', 0,0);
                }
                $pdf->Ln();
                $pdf->SetLineWidth(0.1);
                $pdf->Line(15, $pdf->GetY(), 195, $pdf->GetY());
                $pdf->SetLineWidth(0.4);
            }
        }
    }
}

$mysqli->close();
ob_end_clean();
$pdf->Output(getFileName('Raumbuch'), 'I');
$_SESSION["PDFHeaderSubtext"] = "";

?>
