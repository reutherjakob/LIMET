<?php
#2025done
require_once '../utils/_utils.php';
check_login();

require_once('../TCPDF-main/TCPDF-main/tcpdf.php');
include "pdf_createBericht_MYPDFclass_A4_Raumbuch.php";
include "_pdf_createBericht_utils.php";

$marginTop = 20; // https://tcpdf.org/docs/srcdoc/TCPDF/files-config-tcpdf-config/
$marginBTM = 10;

$_SESSION["PDFTITEL"] = "Raumbuch";
$_SESSION["PDFHeaderSubtext"] = "";//; "Raumbuch Klinik Hitzing";
$_SESSION["PDFTITELBLATT"] = "KHI Quest"; // 9.12.25

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false, true);
$pdf = init_pdf_attributes($pdf, PDF_MARGIN_LEFT, $marginTop, $marginBTM, "A4", "Raumbuch");
$mysqli = utils_connect_sql();

$roomIDs = filter_input(INPUT_GET, 'roomID');
$teile = explode(",", $roomIDs);

$outputparameter =
    [
        ["name" => "Standort", "fetch_data" => $_SESSION["projectName"]],  #ALTERNATIV SQL Projektname
        ["name" => "Funktionsstelle", "fetch_data" => "sql", "dataname" => "Raumbereich Nutzer"],
        ["name" => "Funktionsbereich", "fetch_data" => "mappingFunktionsstelle"], // looks like its the same for every Raumbereich Nutzer
        ["name" => "Raumnummer", "fetch_data" => "sql", "dataname" => "Raumnr"],
        ["name" => "Raumbezeichnung", "fetch_data" => "sql", "dataname" => "Raumbezeichnung"],
        ["name" => "Raumklasse H6020", "fetch_data" => "sql", "dataname" => "H6020"],
        ["name" => "Nutzfläche", "fetch_data" => "sql", "dataname" => "Nutzfläche"],

        ["name" => "Bauetappe", "fetch_data" => "sql", "dataname" => "Bauetappe"], // oder ["name" => "Bauabschnitt", "fetch_data" => "sql", "dataname" => "Bauabschnitt"],
        ["name" => "Geschoss", "fetch_data" => "sql", "dataname" => "Geschoss"],
        ["name" => "Anwendungsgruppe", "fetch_data" => "sql", "dataname" => "Anwendungsgruppe"],
        ["name" => "Bodenbelag", "fetch_data" => "sql", "dataname" => "Fussboden"],
        ["name" => "Ableitfähiger Boden", "fetch_data" => "sql", "dataname" => "Fussboden OENORM B5220"], # render yes/ no

        #BO ANGABEN
        ["name" => "MitarbeiterInnen", "fetch_data" => "sql", "dataname" => "AR_AP_permanent"],
        //  ["name" => "PatientInnen", "fetch_data" => "sql", "dataname" => "AR_AnwesendePersonen"],
        //  ["name" => "BesucherInnen", "fetch_data" => "sql", "dataname" => "AR_AnwesendePersonen"],
        ["name" => "Funktion", "fetch_data" => "sql", "dataname" => "`Anmerkung FunktionBO`"],

        #Raumangaben
        ["name" => "`HT_Luftmenge m3/h`", "fetch_data" => "sql", "dataname" => "`HT_Luftmenge m3/h`"],
        ["name" => "`HT_Luftwechsel 1/h`", "fetch_data" => "sql", "dataname" => "`HT_Luftwechsel 1/h`"],
        ["name" => "`HT_Raumtemp Sommer °C`", "fetch_data" => "sql", "dataname" => "`HT_Raumtemp Sommer °C`"],
        ["name" => "`HT_Raumtemp Winter °C`", "fetch_data" => "sql", "dataname" => "`HT_Raumtemp Winter °C`"],
        ["name" => "EL_Beleuchtungsstaerke", "fetch_data" => "sql", "dataname" => "EL_Beleuchtungsstaerke"],

    ];

$SB = 210;
$w = [15, 40, 5, 40, 10, 40, 5, 40, 15];
$ln = 4;
foreach ($teile as $valueOfRoomID) {
    $pdf->AddPage('P', 'A4');

    $sql = "SELECT 
    tabelle_räume.idTABELLE_Räume,
    tabelle_räume.Raumnr,
    tabelle_räume.Raumbezeichnung,
    tabelle_räume.`Raumbereich Nutzer`,
    tabelle_räume.Geschoss,
    tabelle_räume.Bauetappe,
    tabelle_räume.Bauabschnitt,
    tabelle_räume.Nutzfläche,
    tabelle_räume.H6020,
    tabelle_räume.Anwendungsgruppe, 
    tabelle_räume.Fussboden, 
    tabelle_räume.`Fussboden OENORM B5220`,
    tabelle_projekte.Projektname, 
    tabelle_räume.AR_AnwesendePersonen,
    tabelle_räume.AR_AP_permanent,
    tabelle_räume.`Anmerkung FunktionBO`,
    tabelle_räume.`HT_Luftmenge m3/h`,
    tabelle_räume.`HT_Luftwechsel 1/h`,
    tabelle_räume.`HT_Raumtemp Sommer °C`,
    tabelle_räume.`HT_Raumtemp Winter °C`,
    tabelle_räume.EL_Beleuchtungsstaerke,
        tabelle_räume.`Anmerkung Geräte`,
    tabelle_funktionsteilstellen.Bezeichnung AS Funktionsteilstelle_Bez,
    tabelle_funktionsteilstellen.Nummer      AS Funktionsteilstelle_Nr,
    tabelle_funktionsstellen.Bezeichnung     AS Funktionsstelle_Bez,
    tabelle_funktionsstellen.Nummer          AS Funktionsstelle_Nr

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

    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('i', $valueOfRoomID);
    $stmt->execute();
    $result2 = $stmt->get_result();

    while ($row = $result2->fetch_assoc()) {
        $pdf->Ln(8);

        $pdf->SetFont('helvetica', '', 8);
        $pdf->MultiCell($w[1] + $w[2] + $w[3], 6, "Standort: " . $_SESSION["projectName"], 0, 'L', 0, 0);
        $pdf->MultiCell($w[4], 6, "", 0, 'L', 0, 0);
        $pdf->MultiCell($w[1] + $w[2] + $w[3], 6, "Bauteil: " . $row['Bauabschnitt'], 0, 'L', 0, 0);
        $pdf->Ln($ln);

        $pdf->MultiCell($w[1] + $w[2] + $w[3], 6, "Funktionsstelle: " . $row['Funktionsteilstelle_Bez'], 0, 'L', 0, 0);
        $pdf->MultiCell($w[4], 6, "", 0, 'L', 0, 0);
        $pdf->MultiCell($w[1] + $w[2] + $w[3], 6, "Funktionsbereich: " . $row['Raumbereich Nutzer'], 0, 'L', 0, 0);
        $pdf->Ln($ln);

        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->MultiCell($w[1] + $w[2] + $w[3], 6, "Nummer: " . $row['Raumnr'], 0, 'L', 0, 0);
        $pdf->SetFont('helvetica', '', 8);
        $pdf->MultiCell($w[4], 6, "", 0, 'L', 0, 0);
        $pdf->MultiCell($w[1] + $w[2] + $w[3], 6, "Ebene: " . $row['Geschoss'], 0, 'L', 0, 0);
        $pdf->Ln($ln);

        $pdf->MultiCell($w[1] + $w[2] + $w[3], 6, "Raum: " . $row['Raumbezeichnung'], 0, 'L', 0, 0);
        $pdf->Ln($ln);

        $pdf->MultiCell($w[1] + $w[2] + $w[3], 6, "Raumklasse: " . $row['H6020'], 0, 'L', 0, 0);
        $pdf->MultiCell($w[4], 6, "", 0, 'L', 0, 0);
        $pdf->MultiCell($w[1] + $w[2] + $w[3], 6, "Anwendungsgruppe: " . $row['Anwendungsgruppe'], 0, 'L', 0, 0);
        $pdf->Ln(4);
        $pdf->SetFont('helvetica', '', 5);
        $pdf->MultiCell($w[1] + $w[2] + $w[3], 6, "(gemäß ÖNorm H6020)", 0, 'L', 0, 0);
        $pdf->MultiCell($w[4], 6, "", 0, 'L', 0, 0);
        $pdf->MultiCell($w[1] + $w[2] + $w[3], 6, "(gemäß OVE E 8101)", 0, 'L', 0, 0);
        $pdf->Ln($ln);
        $pdf->SetFont('helvetica', '', 8);

        $pdf->MultiCell($w[1] + $w[2] + $w[3], 6, "Nutzfläche: " . $row['Nutzfläche'], 0, 'L', 0, 0);
        $pdf->MultiCell($w[4], 6, "", 0, 'L', 0, 0);
        $pdf->MultiCell($w[1] + $w[2] + $w[3], 6, "Bodenbelag: " . $row['Fussboden'], 0, 'L', 0, 0);
        $pdf->Ln($ln);

        $pdf->MultiCell($w[1] + $w[2] + $w[3], 6, "", 0, 'L', 0, 0);
        $pdf->MultiCell($w[4], 6, "", 0, 'L', 0, 0);
        $pdf->MultiCell($w[1] + $w[2] + $w[3], 6, "Ableitfähig: " . $row['Fussboden OENORM B5220'], 0, 'L', 0, 0);
        $pdf->Ln($ln);

        $pdf->cell(0, 0, '', 'B', 0, 'L');
        $pdf->Ln($ln);
        $pdf->Ln($ln);

        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell($w[1] + $w[2] + $w[3], 6, "BO-Angaben:", 0, 'L', 0, 0);
        $pdf->Ln(4);
        $pdf->SetFont('helvetica', '', 5);
        $pdf->MultiCell($w[1] + $w[2] + $w[3], 6, "(BDO Health Care Consultancy GmbH)", 0, 'L', 0, 0);
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Ln($ln);


        $pdf->MultiCell($w[1], 6, "Personen:", 0, 'L', 0, 0);
        if ($row["AR_AP_permanent"] > 0) {
            $pdf->MultiCell($w[2], 6, "x", 0, 'L', 0, 0);
        } else {
            $pdf->MultiCell($w[2], 6, "-", 0, 'L', 0, 0);
        }
        $pdf->MultiCell($w[3], 6, "MitarbeiterInnen", 0, 'L', 0, 0);
        $pdf->Ln($ln);

        $pdf->MultiCell($w[1], 6, "", 0, 'L', 0, 0);
        if (false) {
            $pdf->MultiCell($w[2], 6, "x", 0, 'L', 0, 0);
        } else {
            $pdf->MultiCell($w[2], 6, "-", 0, 'L', 0, 0);
        }
        $pdf->MultiCell($w[3], 6, "PatientInnen", 0, 'L', 0, 0);
        $pdf->Ln($ln);

        $pdf->MultiCell($w[1], 6, "", 0, 'L', 0, 0);
        if (false) {
            $pdf->MultiCell($w[2], 6, "x", 0, 'L', 0, 0);
        } else {
            $pdf->MultiCell($w[2], 6, "-", 0, 'L', 0, 0);
        }
        $pdf->MultiCell($w[3], 6, "BesucherInnen", 0, 'L', 0, 0);
        $pdf->Ln($ln);
        $pdf->Ln($ln);


        $pdf->MultiCell($w[1], 6, "Funktion:", 0, 'L', 0, 0);
        $pdf->MultiCell($w[4] + $w[2] + $w[3] + $w[5] + $w[6] + $w[7], 6, $row["BO"], 0, 'L', 0, 0);

        $pdf->Ln($ln);
        $pdf->cell(0, 0, '', 'B', 0, 'L');
        $pdf->Ln($ln);
        $pdf->Ln($ln);


        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell($w[1] + $w[2] + $w[3], 6, "Raumangaben:", 0, 'L', 0, 0);
        $pdf->Ln($ln);
        $pdf->Ln($ln);
        $pdf->SetFont('helvetica', '', 8);

        $pdf->MultiCell($w[1], 6, "HT:", 0, 'L', 0, 0);
        $pdf->MultiCell($w[2] + $w[3], 6, "Luftwechselrate [1/h]: ", 0, 'L', 0, 0);
        $pdf->MultiCell($w[4] + $w[5], 6, $row['HT_Luftwechsel 1/h'], 0, 'L', 0, 0);
        $pdf->Ln($ln);
        $pdf->MultiCell($w[1], 6, "", 0, 'L', 0, 0);
        $pdf->MultiCell($w[2] + $w[3], 6, "Luftmenge [m3/h]: ", 0, 'L', 0, 0);
        $pdf->MultiCell($w[4] + $w[5], 6, $row['HT_Luftmenge m3/h'], 0, 'L', 0, 0);
        $pdf->Ln($ln);
        $pdf->MultiCell($w[1], 6, "", 0, 'L', 0, 0);
        $pdf->MultiCell($w[2] + $w[3], 6, "Raumtemperatur Winter [°C]: ", 0, 'L', 0, 0);
        $pdf->MultiCell($w[4] + $w[5], 6, $row['HT_Raumtemp Winter °C'], 0, 'L', 0, 0);
        $pdf->Ln($ln);
        $pdf->MultiCell($w[1], 6, "", 0, 'L', 0, 0);
        $pdf->MultiCell($w[2] + $w[3], 6, "Raumtemperatur Sommer [°C]:", 0, 'L', 0, 0);
        $pdf->MultiCell($w[4] + $w[5], 6, $row['HT_Raumtemp Sommer °C'], 0, 'L', 0, 0);
        $pdf->Ln($ln);
        $pdf->Ln($ln);
        $pdf->MultiCell($w[1], 6, "ET:", 0, 'L', 0, 0);
        $pdf->MultiCell($w[2] + $w[3], 6, "Beleuchtungsstärke [lx]: ", 0, 'L', 0, 0);
        $pdf->MultiCell($w[4] + $w[5], 6, $row['EL_Beleuchtungsstaerke'], 0, 'L', 0, 0);
        $pdf->Ln($ln);
        $pdf->Ln($ln);
        $pdf->MultiCell($w[1], 6, "MT:", 0, 'L', 0, 0);
        $pdf->MultiCell($w[2] + $w[3] + $w[4] + $w[5] + $w[6] + $w[7]  , 6, $row['Anmerkung Geräte'], 0, 'L', 0, 0);
    }
}
$mysqli->close();
ob_end_clean();
$pdf->Output(getFileName('Raumbuch'), 'I');
$_SESSION["PDFHeaderSubtext"] = "";
