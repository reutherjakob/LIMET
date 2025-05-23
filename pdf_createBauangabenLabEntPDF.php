<?php
#2025done

if (!function_exists('utils_connect_sql')) {
    include "_utils.php";
}
check_login();
require_once('TCPDF-main/TCPDF-main/tcpdf.php');
include "pdf_createBericht_MYPDFclass_A4_Raumbuch.php";
include "_pdf_createBericht_utils.php";

$marginTop = 17; // https://tcpdf.org/docs/srcdoc/TCPDF/files-config-tcpdf-config/
$marginBTM = 10;
if ($_SESSION["projectPlanungsphase"] == "Vorentwurf") {
    $_SESSION["PDFTITEL"] = "Labortechnische Vorbemessungsangaben";
} else {
    $_SESSION["PDFTITEL"] = "Labortechnische Bauangaben";
}

$pdf = new MYPDF('P', PDF_UNIT, "A4", true, 'UTF-8', false, true);
$pdf = init_pdf_attributes($pdf, PDF_MARGIN_LEFT, $marginTop, $marginBTM, "A4", $_SESSION["PDFTITEL"]);

// Daten laden
$mysqli = utils_connect_sql();

// -----------------Variantenparameter Info laden----------------------------
$sql = "SELECT tabelle_projekt_elementparameter.Wert, tabelle_projekt_elementparameter.Einheit, tabelle_projekt_elementparameter.tabelle_Varianten_idtabelle_Varianten, tabelle_projekt_elementparameter.tabelle_elemente_idTABELLE_Elemente, tabelle_parameter.Bezeichnung, tabelle_parameter_kategorie.Kategorie
FROM tabelle_parameter_kategorie INNER JOIN (tabelle_parameter INNER JOIN tabelle_projekt_elementparameter ON tabelle_parameter.idTABELLE_Parameter = tabelle_projekt_elementparameter.tabelle_parameter_idTABELLE_Parameter) ON tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie = tabelle_parameter.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie
WHERE (((tabelle_projekt_elementparameter.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . "))
ORDER BY tabelle_parameter.Bezeichnung, tabelle_parameter_kategorie.Kategorie;";

$result1 = $mysqli->query($sql);
$variantenInfos = array();
$variantenInfosCounter = 0;
while ($row = $result1->fetch_assoc()) {
    $variantenInfos[$variantenInfosCounter]['VarID'] = $row['tabelle_Varianten_idtabelle_Varianten'];
    $variantenInfos[$variantenInfosCounter]['elementID'] = $row['tabelle_elemente_idTABELLE_Elemente'];
    $variantenInfos[$variantenInfosCounter]['Wert'] = $row['Wert'];
    $variantenInfos[$variantenInfosCounter]['Einheit'] = $row['Einheit'];
    $variantenInfos[$variantenInfosCounter]['Kategorie'] = $row['Kategorie'];
    $variantenInfos[$variantenInfosCounter]['Bezeichnung'] = $row['Bezeichnung'];
    $variantenInfosCounter = $variantenInfosCounter + 1;
}


// RaumIDs laden über GET
$roomIDs = filter_input(INPUT_GET, 'roomID');
$teile = explode(",", $roomIDs);


foreach ($teile as $valueOfRoomID) {
    $pdf->AddPage('P', 'A4');
    // Raumdaten laden ----------------------------------

    $sql = "SELECT tabelle_räume.idTABELLE_Räume, tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung, tabelle_räume.`Raumbereich Nutzer`, tabelle_räume.Geschoss, tabelle_räume.Bauetappe, tabelle_räume.`Funktionelle Raum Nr`, tabelle_räume.`Raumtyp BH`, tabelle_räume.Raumhoehe, tabelle_räume.`Raumhoehe 2`, tabelle_räume.`Raumhoehe_Soll`,
            tabelle_räume.Bauabschnitt, tabelle_räume.Nutzfläche, tabelle_räume.Nutzfläche_Soll, tabelle_räume.Abdunkelbarkeit, tabelle_räume.Strahlenanwendung, tabelle_räume.Laseranwendung, tabelle_räume.Laserklasse, tabelle_räume.H6020, tabelle_räume.GMP, 
            tabelle_räume.ISO, tabelle_räume.`O2`, tabelle_räume.`VA`, tabelle_räume.`DL-10`, tabelle_räume.`DL-5`, tabelle_räume.CO2, tabelle_räume.N2, tabelle_räume.Ar, tabelle_räume.He, tabelle_räume.`He-RF`, tabelle_räume.H2,   
            tabelle_räume.AV, tabelle_räume.SV, tabelle_räume.ZSV, tabelle_räume.USV, tabelle_räume.Anwendungsgruppe, tabelle_räume.`AR_Akustik`, tabelle_räume.`ET_EMV`, tabelle_räume.`AR_AnwesendePersonen`,
            tabelle_räume.`Anmerkung MedGas`, tabelle_räume.`Anmerkung Elektro`, tabelle_räume.`Anmerkung HKLS`, tabelle_räume.`Anmerkung Geräte`, tabelle_räume.`Anmerkung FunktionBO`, tabelle_räume.`Anmerkung BauStatik`, 
            tabelle_räume.`IT Anbindung`, tabelle_räume.`Fussboden OENORM B5220`, tabelle_projekte.Projektname, tabelle_planungsphasen.Bezeichnung, tabelle_räume.AR_Schwingungsklasse, tabelle_räume.`AR_APs`, tabelle_räume.`AR_Belichtung-nat`,
            tabelle_räume.RaumNr_Bestand, tabelle_räume.Gebaeude_Bestand, tabelle_räume.`ET_EMV_ja-nein`, tabelle_räume.`EL_Leistungsbedarf_W_pro_m2`, tabelle_räume.`HT_Waermeabgabe`, tabelle_räume.`HT_Luftwechsel 1/h`, tabelle_räume.`HT_Abluft_Sicherheitsschrank_Stk`, tabelle_räume.`HT_Kühlwasserleistung_W`,
            tabelle_räume.`Allgemeine Hygieneklasse`, tabelle_räume.`O2_Mangel`, tabelle_räume.`CO2_Melder`, tabelle_räume.`ET_Digestorium_MSR_230V_SV_Stk`, tabelle_räume.`HT_Spuele_Stk`, tabelle_räume.`HT_Notdusche`,
            tabelle_räume.`Wasser Qual 3`, tabelle_räume.`Wasser Qual 3 l/min`, tabelle_räume.`Wasser Qual 2`, tabelle_räume.`Wasser Qual 2 l/Tag`, tabelle_räume.`Wasser Qual 1`, tabelle_räume.`Wasser Qual 1 l/Tag`,
            tabelle_räume.`HT_Abluft_Digestorium_Stk`, tabelle_räume.`HT_Abluft_Sicherheitsschrank_Unterbau_Stk`, tabelle_räume.`HT_Punktabsaugung_Stk`, tabelle_räume.`HT_Abluft_Vakuumpumpe`, tabelle_räume.`HT_Abluft_Rauchgasabzug_Stk`,
            tabelle_räume.`HT_Abluft_Esse_Stk`, tabelle_räume.`HT_Abluft_Schweissabsaugung_Stk`, tabelle_räume.`DL ISO 8573`, tabelle_räume.`DL l/min`, tabelle_räume.`O2 l/min`, tabelle_räume.`O2 Reinheit`, tabelle_räume.`CO2 l/min`, tabelle_räume.`CO2 Reinheit`, 
            tabelle_räume.`N2 Reinheit`, tabelle_räume.`N2 l/min`, tabelle_räume.`Ar Reinheit`, tabelle_räume.`Ar l/min`,
            tabelle_räume.`H2 Reinheit`, tabelle_räume.`H2 l/min`, tabelle_räume.`He Reinheit`, tabelle_räume.`He l/min`, tabelle_räume.`LN`, tabelle_räume.`LN l/Tag`,
            tabelle_räume.`ET_RJ45-Ports`, tabelle_räume.`ET_5x10mm2_USV_Stk`, tabelle_räume.`ET_32A_3Phasig_Einzelanschluss`, tabelle_räume.`ET_64A_3Phasig_Einzelanschluss`, tabelle_räume.`ET_16A_3Phasig_Einzelanschluss`, tabelle_räume.`ET_5x10mm2_AV_Stk`,
            tabelle_räume.`ET_5x10mm2_Digestorium_Stk`, tabelle_räume.`DL-tech`
            FROM tabelle_planungsphasen INNER JOIN (tabelle_projekte INNER JOIN tabelle_räume ON tabelle_projekte.idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte) ON tabelle_planungsphasen.idTABELLE_Planungsphasen = tabelle_projekte.TABELLE_Planungsphasen_idTABELLE_Planungsphasen
            WHERE (((tabelle_räume.idTABELLE_Räume)=" . $valueOfRoomID . "));";


    $result2 = $mysqli->query($sql);
    while ($row = $result2->fetch_assoc()) {
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Ln();
        $pdf->MultiCell(100, 6, "Projekt: " . $row['Projektname'], 0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->MultiCell(100, 6, "Raum: " . $row['Raumbezeichnung'], 0, 'L', 0, 0);
        $pdf->MultiCell(80, 6, "Nummer: " . $row['Raumnr'], 0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(100, 6, "Raumtyp-Nutzer: " . $row['Raumtyp BH'], 0, 'L', 0, 0);
        $pdf->MultiCell(80, 6, "Funktionale-RaumNr: " . $row['Funktionelle Raum Nr'], 0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->MultiCell(100, 6, "Cluster: " . $row['Raumbereich Nutzer'], 0, 'L', 0, 0);
        $pdf->MultiCell(80, 6, "Geschoss: " . $row['Geschoss'], 0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->MultiCell(100, 6, "Raumfläche: Ist: " . $row['Nutzfläche'] . " m2", 0, 'L', 0, 0);
        $pdf->MultiCell(80, 6, "Raumhöhe Ist: " . $row['Raumhoehe'] . " m", 0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->MultiCell(100, 6, "Projektstatus: " . $row['Bezeichnung'], 'B', 'L', 0, 0);
        $pdf->MultiCell(80, 6, "Bauteil: " . $row['Bauetappe'], 'B', 'L', 0, 0);
        $pdf->Ln();
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->MultiCell(22, 8, "Allgemein: ", 0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetTextColor(0);
        $pdf->MultiCell(40, 6, "Strahlenanwendung: ", 0, 'R', 0, 0);
        if ($row['Strahlenanwendung'] === '0') {
            $pdf->SetFont('zapfdingbats', '', 10);
            $pdf->MultiCell(40, 6, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
        } else {
            if ($row['Strahlenanwendung'] === '1') {
                $pdf->SetFont('zapfdingbats', '', 10);
                //grün
                $pdf->SetTextColor(0, 255, 0);
                $pdf->MultiCell(40, 6, TCPDF_FONTS::unichr(52), 0, 'L', 0, 0);
            } else {
                $pdf->MultiCell(40, 6, "Quasi stationär", 0, 'L', 0, 0);
            }
        }
        //schwarz 
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(40, 6, "Schwingungsklasse: ", 0, 'R', 0, 0);
        $pdf->MultiCell(40, 6, $row['AR_Schwingungsklasse'], 0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->MultiCell(40, 6, "Laseranwendung: ", 0, 'R', 0, 0);
        if ($row['Laseranwendung'] === '0') {
            $pdf->SetFont('zapfdingbats', '', 10);
            $pdf->MultiCell(40, 6, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
        } else {
            $pdf->SetFont('zapfdingbats', '', 10);
            //grün
            $pdf->SetTextColor(0, 255, 0);
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(52), 0, 'L', 0, 0);
            $pdf->SetTextColor(0);
            $pdf->SetFont('helvetica', '', 10);
            $pdf->MultiCell(30, 6, " Klasse: " . $row['Laserklasse'], 0, 'L', 0, 0);
        }
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(40, 6, "FB ÖNORM B5220:", 0, 'R', 0, 0);
        $pdf->MultiCell(20, 6, " " . $row['Fussboden OENORM B5220'], 0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->MultiCell(40, 6, "Abdunkelbarkeit: ", 0, 'R', 0, 0);
        if ($row['Abdunkelbarkeit'] === '0') {
            $pdf->SetFont('zapfdingbats', '', 10);
            $pdf->MultiCell(40, 6, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
        } else {
            if ($row['Abdunkelbarkeit'] === '1') {
                $pdf->SetTextColor(0);
                $pdf->SetFont('helvetica', '', 10);
                $pdf->MultiCell(40, 6, "Vollverdunkelbar", 0, 'L', 0, 0);
            } else {
                $pdf->SetTextColor(0);
                $pdf->SetFont('helvetica', '', 10);
                $pdf->MultiCell(40, 6, "Abdunkelbar", 0, 'L', 0, 0);
            }
        }
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Ln();

        $pdf->MultiCell(40, 6, "EMV: ", 0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 10);
        if ($row['ET_EMV_ja-nein'] === '0') {
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
        } else {
            //grün
            $pdf->SetTextColor(0, 255, 0);
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(52), 0, 'L', 0, 0);
        }
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->MultiCell(60, 6, "EMV-Maßnahme: ", 0, 'R', 0, 0);
        $pdf->MultiCell(40, 6, $row['ET_EMV'], 0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->MultiCell(180, 8, "Elektro: ", 'T', 'L', 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Ln();
        $pdf->MultiCell(40, 8, "Leistung: ", 0, 'R', 0, 0);
        $pdf->MultiCell(50, 6, $row['EL_Leistungsbedarf_W_pro_m2'] * $row['Nutzfläche'] . "W", 0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(40, 6, "Netzarten: ", 0, 'R', 0, 0);
        $pdf->MultiCell(20, 6, "AV: ", 0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 10);
        if ($row['AV'] === '0') {
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
        } else {
            //grün
            $pdf->SetTextColor(0, 255, 0);
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(52), 0, 'L', 0, 0);
        }
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(40, 6, "SV: ", 0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 10);
        if ($row['SV'] === '0') {
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
        } else {
            //grün
            $pdf->SetTextColor(0, 255, 0);
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(52), 0, 'L', 0, 0);
        }
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(40, 6, "USV: ", 0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 10);
        if ($row['USV'] === '0') {
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
        } else {
            //grün
            $pdf->SetTextColor(0, 255, 0);
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(52), 0, 'L', 0, 0);
        }
        $pdf->Ln();
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(40, 6, "Zuleitungen: ", 0, 'R', 0, 0);
        $pdf->MultiCell(40, 6, "AV 5x10mm2: " . $row['ET_5x10mm2_AV_Stk'] . " Stk", 0, 'R', 0, 0);
        $pdf->MultiCell(85, 6, "USV 5x10mm2: " . $row['ET_5x10mm2_USV_Stk'] . " Stk", 0, 'R', 0, 0);
        $pdf->Ln();
        $pdf->MultiCell(80, 6, "16A 3Phasig: " . $row['ET_16A_3Phasig_Einzelanschluss'] . " Stk", 0, 'R', 0, 0);
        $pdf->Ln();
        $pdf->MultiCell(80, 6, "32A 3Phasig: " . $row['ET_32A_3Phasig_Einzelanschluss'] . " Stk", 0, 'R', 0, 0);
        $pdf->Ln();
        $pdf->MultiCell(80, 6, "64A 3Phasig: " . $row['ET_64A_3Phasig_Einzelanschluss'] . " Stk", 0, 'R', 0, 0);
        $pdf->Ln();
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(40, 6, "Netzwerk: ", 0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 10);
        if ($row['IT Anbindung'] === '0') {
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
        } else {
            //grün
            $pdf->SetTextColor(0, 255, 0);
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(52), 0, 'L', 0, 0);
        }
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(20, 8, "Ports: ", 0, 'R', 0, 0);
        $pdf->MultiCell(50, 6, " " . $row['ET_RJ45-Ports'] . " Stk", 0, 'L', 0, 0);
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln();
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(40, 8, "MSR-Digestorium: ", 0, 'R', 0, 0);
        $pdf->MultiCell(20, 6, " " . $row['ET_Digestorium_MSR_230V_SV_Stk'] . " Stk", 0, 'L', 0, 0);
        $pdf->MultiCell(50, 8, "Zuleitung Digest.: ", 0, 'R', 0, 0);
        $pdf->MultiCell(50, 6, " " . $row['ET_5x10mm2_Digestorium_Stk'] . " Stk", 0, 'L', 0, 0);
        $pdf->Ln();
        $y = $pdf->GetY();
        if (($y + 30) >= 270) {
            $pdf->AddPage();
        }
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->MultiCell(180, 8, "Haustechnik: ", 'T', 'L', 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Ln();
        $pdf->MultiCell(40, 8, "Wärme: ", 0, 'R', 0, 0);
        $pdf->MultiCell(40, 6, $row['HT_Waermeabgabe'] * $row['Nutzfläche'] . "W", 0, 'L', 0, 0);
        $pdf->MultiCell(80, 8, "Luftwechsel: ", 0, 'R', 0, 0);
        $pdf->MultiCell(20, 6, " " . $row['HT_Luftwechsel 1/h'] . " /h", 0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->MultiCell(40, 8, "Sicherhetsschränke: ", 0, 'R', 0, 0);
        $pdf->MultiCell(20, 6, " " . $row['HT_Abluft_Sicherheitsschrank_Stk'] . " Stk", 0, 'L', 0, 0);
        $pdf->MultiCell(40, 8, "Digestorien: ", 0, 'R', 0, 0);
        $pdf->MultiCell(20, 6, " " . $row['HT_Abluft_Digestorium_Stk'] . " Stk", 0, 'L', 0, 0);
        $pdf->MultiCell(40, 8, "Sicherheitsschr. UB: ", 0, 'R', 0, 0);
        $pdf->MultiCell(20, 6, " " . $row['HT_Abluft_Sicherheitsschrank_Unterbau_Stk'] . " Stk", 0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->MultiCell(40, 8, "Punktabsaugung: ", 0, 'R', 0, 0);
        $pdf->MultiCell(20, 6, " " . $row['HT_Punktabsaugung_Stk'] . " Stk", 0, 'L', 0, 0);
        $pdf->MultiCell(40, 8, "Abluft Vakuum: ", 0, 'R', 0, 0);
        $pdf->MultiCell(20, 6, " " . $row['HT_Abluft_Vakuumpumpe'] . " Stk", 0, 'L', 0, 0);
        $pdf->MultiCell(40, 8, "Rauchgasabzug: ", 0, 'R', 0, 0);
        $pdf->MultiCell(20, 6, " " . $row['HT_Abluft_Rauchgasabzug_Stk'] . " Stk", 0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->MultiCell(40, 8, "Esse: ", 0, 'R', 0, 0);
        $pdf->MultiCell(20, 6, " " . $row['HT_Abluft_Esse_Stk'] . " Stk", 0, 'L', 0, 0);
        $pdf->MultiCell(40, 8, "Schweissabsaug: ", 0, 'R', 0, 0);
        $pdf->MultiCell(20, 6, " " . $row['HT_Abluft_Schweissabsaugung_Stk'] . " Stk", 0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->MultiCell(40, 8, "Kühlwasserleistung: ", 0, 'R', 0, 0);
        $pdf->MultiCell(20, 6, " " . $row['HT_Kühlwasserleistung_W'] . " W", 0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->MultiCell(40, 8, "Spülen: ", 0, 'R', 0, 0);
        $pdf->MultiCell(20, 6, " " . $row['HT_Spuele_Stk'] . " Stk", 0, 'L', 0, 0);
        $pdf->MultiCell(40, 8, "Notdusche: ", 0, 'R', 0, 0);
        $pdf->MultiCell(20, 6, " " . $row['HT_Notdusche'] . " Stk", 0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->MultiCell(40, 8, "Wasser Qual 3: ", 0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 10);
        if ($row['Wasser Qual 3'] === '0') {
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
        } else {
            //grün
            $pdf->SetTextColor(0, 255, 0);
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(52), 0, 'L', 0, 0);
            $pdf->SetFont('helvetica', '', 10);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->MultiCell(20, 6, " " . $row['Wasser Qual 3 l/min'] . " l/min", 0, 'L', 0, 0);
        }
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln();
        $pdf->MultiCell(40, 8, "Wasser Qual 2: ", 0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 10);
        if ($row['Wasser Qual 2'] === '0') {
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
        } else {
            //grün
            $pdf->SetTextColor(0, 255, 0);
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(52), 0, 'L', 0, 0);
            $pdf->SetFont('helvetica', '', 10);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->MultiCell(20, 6, " " . $row['Wasser Qual 2 l/Tag'] . " l/Tag", 0, 'L', 0, 0);
        }
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Ln();
        $pdf->MultiCell(40, 8, "Wasser Qual 1: ", 0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 10);
        if ($row['Wasser Qual 1'] === '0') {
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
        } else {
            //grün
            $pdf->SetTextColor(0, 255, 0);
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(52), 0, 'L', 0, 0);
            $pdf->SetFont('helvetica', '', 10);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->MultiCell(20, 6, " " . $row['Wasser Qual 1 l/Tag'] . " l/Tag", 0, 'L', 0, 0);
        }
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Ln();
        $pdf->SetFont('helvetica', '', 10);
        //$pdf->MultiCell(180, 6, "",'B', 'L', 0, 0);              
        // Wenn Seitenende? Überprüfen und neue Seite anfangen
        $y = $pdf->GetY();
        if (($y + 8) >= 270) {
            $pdf->AddPage();
        }
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->MultiCell(20, 8, "Gase: ", 'T', 'L', 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(160, 8, "Zentrale Gasversorgung: ", 'T', 'L', 0, 0);
        $pdf->Ln();
        $pdf->MultiCell(40, 6, "DL: ", 0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 10);
        if ($row['DL-tech'] === '0') {
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
        } else {
            //grün
            $pdf->SetTextColor(0, 255, 0);
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(52), 0, 'L', 0, 0);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('helvetica', '', 10);
            $pdf->MultiCell(25, 6, " " . $row['DL ISO 8573'], 0, 'L', 0, 0);
        }
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Ln();
        $pdf->MultiCell(20, 6, "", 0, 'R', 0, 0);
        $pdf->MultiCell(160, 8, "Dezentrale Gasversorgung: ", 0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->MultiCell(40, 6, "O2: ", 0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 10);
        if ($row['O2'] === '0') {
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
            $pdf->MultiCell(20, 6, "", 0, 'L', 0, 0);
        } else {
            //grün
            $pdf->SetTextColor(0, 255, 0);
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(52), 0, 'L', 0, 0);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('helvetica', '', 10);
            $pdf->MultiCell(20, 6, " " . $row['O2 Reinheit'], 0, 'L', 0, 0);
        }
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(20, 6, "VA: ", 0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 10);
        if ($row['VA'] === '0') {
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
            $pdf->MultiCell(20, 6, "", 0, 'L', 0, 0);
        } else {
            //grün
            $pdf->SetTextColor(0, 255, 0);
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(52), 0, 'L', 0, 0);
            $pdf->MultiCell(20, 6, "", 0, 'L', 0, 0);
        }
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(20, 6, "CO2: ", 0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 10);
        if ($row['CO2'] === '0') {
            $pdf->MultiCell(8, 6, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
            $pdf->MultiCell(20, 6, "", 0, 'L', 0, 0);
        } else {
            //grün
            $pdf->SetTextColor(0, 255, 0);
            $pdf->MultiCell(8, 6, TCPDF_FONTS::unichr(52), 0, 'L', 0, 0);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('helvetica', '', 10);
            $pdf->MultiCell(20, 6, " " . $row['CO2 Reinheit'], 0, 'L', 0, 0);
        }
        $pdf->Ln();
        //schwarz
        // Wenn Seitenende? Überprüfen und neue Seite anfangen
        $y = $pdf->GetY();
        if (($y + 6) >= 270) {
            $pdf->AddPage();
        }
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(40, 6, "N2: ", 0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 10);
        if ($row['N2'] === '0') {
            $pdf->MultiCell(8, 6, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
            $pdf->MultiCell(20, 6, "", 0, 'L', 0, 0);
        } else {
            //grün
            $pdf->SetTextColor(0, 255, 0);
            $pdf->MultiCell(8, 6, TCPDF_FONTS::unichr(52), 0, 'L', 0, 0);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('helvetica', '', 10);
            $pdf->MultiCell(20, 6, " " . $row['N2 Reinheit'], 0, 'L', 0, 0);
        }
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(20, 6, "Ar: ", 0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 10);
        if ($row['Ar'] === '0') {
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
            $pdf->MultiCell(20, 6, "", 0, 'L', 0, 0);
        } else {
            //grün
            $pdf->SetTextColor(0, 255, 0);
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(52), 0, 'L', 0, 0);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('helvetica', '', 10);
            $pdf->MultiCell(20, 6, " " . $row['Ar Reinheit'], 0, 'L', 0, 0);
        }
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(20, 6, "H2: ", 0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 10);
        if ($row['H2'] === '0') {
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
            $pdf->MultiCell(20, 6, "", 0, 'L', 0, 0);
        } else {
            //grün
            $pdf->SetTextColor(0, 255, 0);
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(52), 0, 'L', 0, 0);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('helvetica', '', 10);
            $pdf->MultiCell(20, 6, " " . $row['´H2 Reinheit'], 0, 'L', 0, 0);
        }
        $pdf->Ln();
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(40, 6, "He: ", 0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 10);
        if ($row['He'] === '0') {
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
            $pdf->MultiCell(20, 6, "", 0, 'L', 0, 0);
        } else {
            //grün+
            $pdf->SetTextColor(0, 255, 0);
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(52), 0, 'L', 0, 0);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('helvetica', '', 10);
            $pdf->MultiCell(20, 6, " " . $row['He Reinheit'], 0, 'L', 0, 0);
        }
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(20, 6, "He-RF: ", 0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 10);
        if ($row['He-RF'] === '0') {
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
            $pdf->MultiCell(20, 6, "", 0, 'L', 0, 0);
        } else {
            //grün
            $pdf->SetTextColor(0, 255, 0);
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(52), 0, 'L', 0, 0);
            $pdf->MultiCell(20, 6, "", 0, 'L', 0, 0);
        }
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(20, 6, "LN: ", 0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 10);
        if ($row['LN'] === '0') {
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
            $pdf->MultiCell(20, 6, "", 0, 'L', 0, 0);
        } else {
            //grün
            $pdf->SetTextColor(0, 255, 0);
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(52), 0, 'L', 0, 0);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('helvetica', '', 10);
            $pdf->MultiCell(20, 6, " " . $row['LN l/Tag'] . "l/Tag", 0, 'L', 0, 0);
        }
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Ln();
        $pdf->MultiCell(20, 6, "", 0, 'R', 0, 0);
        $pdf->MultiCell(160, 8, "Sicherheitseinrichtungen: ", 0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->MultiCell(90, 6, "O2-Mangel: ", 0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 10);
        if ($row['O2_Mangel'] === '0') {
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
        } else {
            //grün
            $pdf->SetTextColor(0, 255, 0);
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(52), 0, 'L', 0, 0);
        }
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(40, 6, "CO2-Melder: ", 0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 10);
        if ($row['CO2_Melder'] === '0') {
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
        } else {
            //grün
            $pdf->SetTextColor(0, 255, 0);
            $pdf->MultiCell(10, 6, TCPDF_FONTS::unichr(52), 0, 'L', 0, 0);
        }
        $pdf->SetTextColor(0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->AddPage();
    }
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->MultiCell(180, 8, "Elemente im Raum: ", 0, 'L', 0, 0);
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Ln();
    $y = $pdf->GetY();
    if (($y + 8) >= 270) {
        $pdf->AddPage();
    }
    $rowHeightFirstLine = $pdf->getStringHeight(50, "ElementID", false, true, '', 1);
    $pdf->MultiCell(20, $rowHeightFirstLine, "Gewerk", 'B', 'C', 0, 0);
    $pdf->MultiCell(20, $rowHeightFirstLine, "GHG", 'B', 'C', 0, 0);
    $pdf->MultiCell(20, $rowHeightFirstLine, "ElementID", 'B', 'C', 0, 0);
    $pdf->MultiCell(20, $rowHeightFirstLine, "Var", 'B', 'C', 0, 0);
    $pdf->MultiCell(50, $rowHeightFirstLine, "Element", 'B', 'L', 0, 0);
    $pdf->MultiCell(20, $rowHeightFirstLine, "Stk", 'B', 'C', 0, 0);
    $pdf->MultiCell(30, $rowHeightFirstLine, "Bestand", 'B', 'C', 0, 0);
    $pdf->Ln();

    // -------------------------Elemente im Raum laden--------------------------
    $sql = "SELECT tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_varianten.Variante, tabelle_räume_has_tabelle_elemente.Anzahl, tabelle_räume_has_tabelle_elemente.Standort, tabelle_räume_has_tabelle_elemente.Verwendung, tabelle_räume_has_tabelle_elemente.Kurzbeschreibung, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, tabelle_auftraggeber_gewerke.Gewerke_Nr, tabelle_auftraggeber_ghg.GHG, tabelle_auftraggeberg_gug.GUG, tabelle_projekt_varianten_kosten.Kosten, tabelle_räume_has_tabelle_elemente.id, tabelle_bestandsdaten.Inventarnummer, tabelle_bestandsdaten.Seriennummer, tabelle_bestandsdaten.Anschaffungsjahr, tabelle_hersteller.Hersteller, tabelle_geraete.Typ, tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten, tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente
    FROM tabelle_hersteller RIGHT JOIN (tabelle_geraete RIGHT JOIN (tabelle_bestandsdaten RIGHT JOIN (tabelle_auftraggeber_gewerke RIGHT JOIN (tabelle_auftraggeber_ghg RIGHT JOIN (tabelle_auftraggeberg_gug RIGHT JOIN (tabelle_projekt_element_gewerk RIGHT JOIN (tabelle_projekt_varianten_kosten INNER JOIN (tabelle_räume INNER JOIN (tabelle_varianten INNER JOIN (tabelle_elemente INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_elemente.idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) ON tabelle_varianten.idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) ON (tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte) AND (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten)) ON (tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte = tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte) AND (tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente = tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente)) ON tabelle_auftraggeberg_gug.idtabelle_auftraggeberg_GUG = tabelle_projekt_element_gewerk.tabelle_auftraggeberg_gug_idtabelle_auftraggeberg_GUG) ON tabelle_auftraggeber_ghg.idtabelle_auftraggeber_GHG = tabelle_projekt_element_gewerk.tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG) ON tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke = tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke) ON tabelle_bestandsdaten.tabelle_räume_has_tabelle_elemente_id = tabelle_räume_has_tabelle_elemente.id) ON tabelle_geraete.idTABELLE_Geraete = tabelle_bestandsdaten.tabelle_geraete_idTABELLE_Geraete) ON tabelle_hersteller.idtabelle_hersteller = tabelle_geraete.tabelle_hersteller_idtabelle_hersteller
    WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . ") AND ((tabelle_räume.idTABELLE_Räume)=" . $valueOfRoomID . ") AND tabelle_räume_has_tabelle_elemente.Anzahl > 0)
    ORDER BY tabelle_auftraggeber_gewerke.Gewerke_Nr, tabelle_auftraggeber_ghg.GHG, tabelle_elemente.ElementID, tabelle_varianten.Variante, tabelle_räume_has_tabelle_elemente.id;";

    $result = $mysqli->query($sql);

    $fill = 0;
    $pdf->SetFillColor(244, 244, 244);
    $idRoombookEntry = 0;
    $bestandsCounter = 1;
    while ($row = $result->fetch_assoc()) {
        if ($idRoombookEntry != $row['id']) {
            $fill = !$fill;
            $bestandsCounter = 1;
            $pdf->SetFont('helvetica', '', 8);
            $rowHeightMainLine = $pdf->getStringHeight(50, $row['Bezeichnung'], false, true, '', 1);
            // Wenn Seitenende? Überprüfen und neue Seite anfange
            $y = $pdf->GetY();
            if (($y + $rowHeightMainLine) >= 270) {
                $pdf->AddPage();
            }
            $pdf->MultiCell(20, $rowHeightMainLine, $row['Gewerke_Nr'], 0, 'C', $fill, 0);
            $pdf->MultiCell(20, $rowHeightMainLine, $row['GHG'], 0, 'C', $fill, 0);
            $pdf->MultiCell(20, $rowHeightMainLine, $row['ElementID'], 0, 'C', $fill, 0);
            $pdf->MultiCell(20, $rowHeightMainLine, $row['Variante'], 0, 'C', $fill, 0);
            $pdf->MultiCell(50, $rowHeightMainLine, $row['Bezeichnung'], 0, 'L', $fill, 0);
            $pdf->MultiCell(20, $rowHeightMainLine, $row['Anzahl'], 0, 'C', $fill, 0);
            if ($row['Neu/Bestand'] == 1) {
                $pdf->MultiCell(30, $rowHeightMainLine, "Nein", 0, 'C', $fill, 0);
            } else {
                $pdf->MultiCell(30, $rowHeightMainLine, "Ja", 0, 'C', $fill, 0);
            }
            $idRoombookEntry = $row['id'];
        }
        $pdf->Ln();
    }
}

$mysqli->close();
ob_end_clean();
$pdf->Output(getFileName('Raumbuch'), 'I');

//============================================================+
// END OF FILE
//============================================================+
