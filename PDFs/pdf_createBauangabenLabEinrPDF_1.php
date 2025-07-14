<?php
#2025done
if (!function_exists('utils_connect_sql')) {
    include "../utils/_utils.php";
}
check_login();

require_once('../TCPDF-main/TCPDF-main/tcpdf.php');
include "pdf_createBericht_MYPDFclass_A4_Raumbuch.php";
include "_pdf_createBericht_utils.php";

$marginTop = 17; // https://tcpdf.org/docs/srcdoc/TCPDF/files-config-tcpdf-config/
$marginBTM = 10;
$_SESSION["PDFTITEL"] = "Labor Raumangaben";
$pdf = new MYPDF('P', PDF_UNIT, "A4", true, 'UTF-8', false, true);
$pdf = init_pdf_attributes($pdf, PDF_MARGIN_LEFT, $marginTop, $marginBTM, "A4", "Raumangaben");

// ------------------ URsprünglioch am Titelblatt --------------------------
// //if ($raumInfos[0]['Projektname'] === "GCP") {
//     $this->Ln();
//     $this->SetFont('helvetica', '', 10);
//     $this->Cell(0, 0, "Vorgängerstände:", 0, false, 'L', 0, '', 0, false, 'T', 'M');
//     $this->Ln();
//     $this->Cell(0, 0, "Stand Überarbeitung nach Verhandlung: 2024-04-24", 0, false, 'L', 0, '', 0, false, 'T', 'M');
//     $this->Ln();
//     $this->Cell(0, 0, "Stand Einreichung: 2023-12-18", 0, false, 'L', 0, '', 0, false, 'T', 'M');
//     $this->Ln();
// }

// $GCP_bearbeit = [
//   12630,
//   11984,
//   11985,
//   12007,
//   12010,
//   12011,
//   12012,
//   12017,
//   12022,
//   12023,
//   12024,
//   12037,
//   12039,
//   12060,
//   12061,
//   12062,
//   12063,
//   12064,
//   12065,
//   12066,
//   12067,
//   12068,
//   12070,
//   12071,
//   12072,
//   12073,
//   12075,
//   12076,
//   12077,
//   12078,
//   12079,
//   12080,
//   12081,
//   12082,
//   12083,
//   12084,
//   12085,
//   12086,
//   12087,
//   12088,
//   12089,
//   12090,
//   12144,
//   12145,
//   12148,
//   12149,
//   12150,
//   12155,
//   12156,
//   12157,
//   12159,
//   12160,
//   12166,
//   12172,
//   12173,
//   12174,
//   12175,
//   12194,
//   12204,
//   12205,
//   12217,
//   12218,
//   12219,
//   12220,
//   12222,
//   12223,
//   12224,
//   12225,
//   12227,
//   12228,
//   12229,
//   12230,
//   12231,
//   12235,
//   12242,
//   12244,
//   12246,
//   12251,
//   12252,
//   12254,
//   12255,
//   12256,
//   12259,
//   12260,
//   12266,
//   12267,
//   12269,
//   12301,
//   12302,
//   12303,
//   12304,
//   12314,
//   12316,
//   12333,
//   12335,
//   12336,
//   12337,
//   12339,
//   12340,
//   12341,
//   12342,
//   12357,
//   12359,
//   12360,
//   12362,
//   12363,
//   12365,
//   12366,
//   12367,
//   12368,
//   12369,
//   12370,
//   12371,
//   12372,
//   12373,
//   12374,
//   12375,
//   12376,
//   12377,
//   12378,
//   12379,
//   12380,
//   12381,
//   12383,
//   12384,
//   12386,
//   12387,
//   12388,
//   12406,
//   12407,
//   12408,
//   12409,
//   12410,
//   12411,
//   12412,
//   12413,
//   12417,
//   12430,
//   12431,
//   12432,
//   12435,
//   12437,
//   12440,
//   12441,
//   12442,
//   12446,
//   12448,
//   12450,
//   12451,
//   12452,
//   12453,
//   12454,
//   12455,
//   12456,
//   12457,
//   12458,
//   12460,
//   12461,
//   12462,
//   12463,
//   12464,
//   12466,
//   12468,
//   12469,
//   12470,
//   12479,
//   12480,
//   12481,
//   12482,
//   12483,
//   12484,
//   12485,
//   12486,
//   12487,
//   12488,
//   12490,
//   12491,
//   12504,
//   12505,
//   12506,
//   12507,
//   12508,
//   12509,
//   12510,
//   12512,
//   12513,
//   12514,
//   12515,
//   12516,
//   12517,
//   12518,
//   12519,
//   12520,
//   12521,
//   12523,
//   12524,
//   12525,
//   12528,
//   12529,
//   12530,
//   12531,
//   12561,
//   12563,
//   12570,
//   12572,
//   12573,
//   12574,
//   12575,
//   12576,
//   12577,
//   12578,
//   12579,
//   12581,
//   12582,
//   12587,
//   12588,
//   12589,
//   12590,
//   12591,
//   12592,
//   12593,
//   12595,
//   12596,
//   12597,
//   12602,
//   12604,
//   12606,
//   12609,
//   12611,
//   12613,
//   12614,
//   12615,
//   12616,
//   12617,
//   12618,
//   12619,
//   12620,
//   12622,
//   12623,
//   12624,
//   12625,
//   12626,
//   12628,
//   12629,
//   12630,
//   12635,
//   12636,
//   12644,
//   12646,
//   12647,
//   12648,
//   12659,
//   12672,
//   12673,
//   12674,
//   12675,
//   12676,
//   12677,
//   12678,
//   12679,
//   12680,
//   12681,
//   12682,
//   12683,
//   12698,
//   12701,
//   12711,
//   12713,
//   12718,
//   12720,
//   12721,
//   14706,
//   14814,
//   14815,
//   14816,
//   14817,
//   14818,
//   14820,
//   14840,
//   14841,
//   15288,
//   15289,
//   15290,
//   15293,
//   15296,
//   15298,
//   15299,
//   15595,
//   26286,
//   26287,
//   26288,
//   26289,
//   26290,
//   26291,
//   26293,
//   26511,
//   26512,
//   26610,
//   26618,
//   26631,
//   26632,
//   28892,
//   28893,
//   50703,
//   50704,
//   105004
//;

$colour = array(132, 164, 76);
$pdf->SetFillColor(...$colour);

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

    $sql = "SELECT 
       tabelle_räume.Raumnr,
       tabelle_räume.Raumbezeichnung,
       tabelle_räume.`Raumbereich Nutzer`,
       tabelle_räume.Geschoss,
       tabelle_räume.Bauetappe,
       tabelle_räume.`Funktionelle Raum Nr`,
       tabelle_räume.`Raumtyp BH`,
       tabelle_räume.Raumhoehe,
       tabelle_räume.`Raumhoehe 2`,
       tabelle_räume.`Raumhoehe_Soll`,
       tabelle_räume.Bauabschnitt,
       tabelle_räume.Nutzfläche,
       tabelle_räume.Nutzfläche_Soll,
       tabelle_räume.Abdunkelbarkeit,
       tabelle_räume.Strahlenanwendung,
       tabelle_räume.Laseranwendung,
       tabelle_räume.Laserklasse,
       tabelle_räume.H6020,
       tabelle_räume.GMP,
       tabelle_räume.ISO,
       tabelle_räume.`O2`,
       tabelle_räume.`VA`,
       tabelle_räume.`DL-10`,
       tabelle_räume.`DL-5`,
       tabelle_räume.CO2,
       tabelle_räume.N2,
       tabelle_räume.Ar,
       tabelle_räume.He,
       tabelle_räume.`He-RF`,
       tabelle_räume.H2,
       tabelle_räume.AV,
       tabelle_räume.SV,
       tabelle_räume.ZSV,
       tabelle_räume.USV,
       tabelle_räume.Anwendungsgruppe,
       tabelle_räume.`AR_Akustik`,
       tabelle_räume.`ET_EMV`,
       tabelle_räume.`AR_AnwesendePersonen`,
       tabelle_räume.`Anmerkung MedGas`,
       tabelle_räume.`Anmerkung Elektro`,
       tabelle_räume.`Anmerkung HKLS`,
       tabelle_räume.`Anmerkung Geräte`,
       tabelle_räume.`Anmerkung FunktionBO`,
       tabelle_räume.`Anmerkung BauStatik`,
       tabelle_räume.`IT Anbindung`,
       tabelle_räume.`Fussboden OENORM B5220`,
       tabelle_projekte.Projektname,
       tabelle_planungsphasen.Bezeichnung,
       tabelle_räume.AR_Schwingungsklasse,
       tabelle_räume.`AR_APs`,
       tabelle_räume.`AR_Belichtung-nat`,
       tabelle_räume.RaumNr_Bestand,
       tabelle_räume.Gebaeude_Bestand,
       tabelle_räume.`ET_EMV_ja-nein`,
       tabelle_räume.`EL_Leistungsbedarf_W_pro_m2`,
       tabelle_räume.`HT_Waermeabgabe`,
       CAST(replace(tabelle_räume.`HT_Luftwechsel 1/h`, ',', '.') AS DECIMAL(14, 2)) AS `HT_Luftwechsel 1/h`,
       tabelle_räume.`HT_Abluft_Sicherheitsschrank_Stk`,
       tabelle_räume.`HT_Kühlwasserleistung_W`,
       tabelle_räume.`Allgemeine Hygieneklasse`,
       tabelle_räume.`O2_Mangel`,
       tabelle_räume.`CO2_Melder`,
       tabelle_räume.`ET_Digestorium_MSR_230V_SV_Stk`,
       tabelle_räume.`HT_Spuele_Stk`,
       tabelle_räume.`HT_Notdusche`,
       tabelle_räume.`Wasser Qual 3`,
       tabelle_räume.`Wasser Qual 3 l/min`,
       tabelle_räume.`Wasser Qual 2`,
       tabelle_räume.`Wasser Qual 2 l/Tag`,
       tabelle_räume.`Wasser Qual 1`,
       tabelle_räume.`Wasser Qual 1 l/Tag`,
       tabelle_räume.`HT_Abluft_Digestorium_Stk`,
       tabelle_räume.`HT_Abluft_Sicherheitsschrank_Unterbau_Stk`,
       tabelle_räume.`HT_Punktabsaugung_Stk`,
       tabelle_räume.`HT_Abluft_Vakuumpumpe`,
       tabelle_räume.`HT_Abluft_Rauchgasabzug_Stk`,
       tabelle_räume.`HT_Abluft_Esse_Stk`,
       tabelle_räume.`HT_Abluft_Schweissabsaugung_Stk`,
       tabelle_räume.`DL ISO 8573`,
       tabelle_räume.`DL l/min`,
       tabelle_räume.`O2 l/min`,
       tabelle_räume.`O2 Reinheit`,
       tabelle_räume.`CO2 l/min`,
       tabelle_räume.`CO2 Reinheit`,
       tabelle_räume.`N2 Reinheit`,
       tabelle_räume.`N2 l/min`,
       tabelle_räume.`Ar Reinheit`,
       tabelle_räume.`Ar l/min`,
       tabelle_räume.`H2 Reinheit`,
       tabelle_räume.`H2 l/min`,
       tabelle_räume.`He Reinheit`,
       tabelle_räume.`He l/min`,
       tabelle_räume.`LN`,
       tabelle_räume.`LN l/Tag`,
       tabelle_räume.`ET_RJ45-Ports`,
       tabelle_räume.`ET_5x10mm2_USV_Stk`,
       tabelle_räume.`ET_32A_3Phasig_Einzelanschluss`,
       tabelle_räume.`ET_64A_3Phasig_Einzelanschluss`,
       tabelle_räume.`ET_16A_3Phasig_Einzelanschluss`,
       tabelle_räume.`ET_5x10mm2_AV_Stk`,
       tabelle_räume.`ET_5x10mm2_Digestorium_Stk`,
       tabelle_räume.`DL-tech`,
       tabelle_räume.`Raumnummer_Nutzer`,
       tabelle_räume.`Volumen`,
       tabelle_räume.`Fussboden`,
       tabelle_räume.`Decke`,
       tabelle_räume.`AR_APs`,
       tabelle_räume.`Anmerkung AR`,
       tabelle_räume.`Belichtungsfläche`,
       tabelle_räume.`AP_Gefaehrdung`,
       tabelle_räume.`EL_Beleuchtungsstaerke`,
       tabelle_räume.`EL_Not_Aus`,
       tabelle_räume.`HT_Belueftung`,
       tabelle_räume.`HT_Entlueftung`,
       tabelle_räume.`HT_Kuehlung`,
       tabelle_räume.`HT_Kaelteabgabe_Typ`,
       tabelle_räume.`HT_Heizung`,
       tabelle_räume.`HT_Waermeabgabe_Typ`,
       tabelle_räume.`PHY_Akustik_T500`,
       tabelle_räume.`PHY_Akustik_Schallgrad`,
       tabelle_räume.`Taetigkeiten`,
       tabelle_räume.`AP_Geistige`,
       tabelle_räume.`VEXAT_Zone`,
       tabelle_räume.`HT_Raumtemp Sommer °C`,
       tabelle_räume.`HT_Raumtemp Winter °C`,
       tabelle_räume.`LHe`,
       tabelle_räume.`EL_Not_Aus_Funktion`,
       tabelle_räume.`EL_Signaleinrichtung`,
       tabelle_räume.`Spezialgase`,
       tabelle_räume.`Gaswarneinrichtung-Art`,
       tabelle_räume.`HT_Luftmenge m3/h`
FROM tabelle_planungsphasen
         INNER JOIN (tabelle_projekte INNER JOIN tabelle_räume
                     ON tabelle_projekte.idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte)
                    ON tabelle_planungsphasen.idTABELLE_Planungsphasen =
                       tabelle_projekte.TABELLE_Planungsphasen_idTABELLE_Planungsphasen
WHERE (((tabelle_räume.idTABELLE_Räume) = " . $valueOfRoomID . "));";

    $result2 = $mysqli->query($sql);
    while ($row = $result2->fetch_assoc()) {

        $pdf->SetY(20);
        $pdf->SetFont('helvetica', 'B', 10);

         $pdf->MultiCell(100, 6, "Projekt: " . $row['Projektname'], 0, 'L', 0, 0);
        $pdf->MultiCell(80, 6, "Nummer: " . $row['Raumnr'], 0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->MultiCell(100, 6, "Raum: " . $row['Raumbezeichnung'], 0, 'L', 0, 0);
        $pdf->MultiCell(100, 6, "RaumNr-Nutzer: " . $row['Raumnummer_Nutzer'], 0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(100, 5, "Cluster: " . $row['Raumbereich Nutzer'], 0, 'L', 0, 0);
        $pdf->MultiCell(80, 5, "Geschoss: " . $row['Geschoss'], 0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->SetFont('helvetica', 'B', 10);
        // set color for background
        $pdf->SetFillColor(...$colour);
        $pdf->MultiCell(180, 5, 'Allgemein', 'T', 'L', 1, 0, '', '', true);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->Ln();
        $pdf->MultiCell(40, 5, "Raumfläche: ", 0, 'R', 0, 0);
        $pdf->MultiCell(40, 5, number_format((float)$row['Nutzfläche'], 2, ',', '') . " m2", 0, 'L', 0, 0);
        $pdf->MultiCell(40, 5, "Raumhöhe: ", 0, 'R', 0, 0);
        $pdf->MultiCell(80, 5, $row['Raumhoehe'] . " m", 0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->MultiCell(40, 5, "Volumen: ", 0, 'R', 0, 0);
        $pdf->MultiCell(40, 5, number_format((float)$row['Volumen'], 2, ',', '') . " m3", 0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->MultiCell(40, 5, "Bodenbelag: ", 0, 'R', 0, 0);
        $pdf->MultiCell(50, 5, $row['Fussboden'], 0, 'L', 0, 0);
        $pdf->MultiCell(30, 5, "Decke: ", 0, 'R', 0, 0);
        $pdf->MultiCell(80, 5, $row['Decke'], 0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->MultiCell(40, 5, "Anmerkung: ", 0, 'R', 0, 0);
        $pdf->MultiCell(140, 5, $row['Anmerkung AR'], 0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->MultiCell(40, 5, "Tätigkeiten: ", 0, 'R', 0, 0);
        $pdf->MultiCell(140, 5, $row['Taetigkeiten'], 0, 'L', 0, 0);

        $pdf->Ln();
        $pdf->SetTextColor(0);
        $pdf->MultiCell(40, 5, "Strahlenanwendung: ", 0, 'R', 0, 0);
        if ($row['Strahlenanwendung'] === '0') {
            $pdf->SetFont('zapfdingbats', '', 10);
            $pdf->MultiCell(40, 5, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
        } else {
            if ($row['Strahlenanwendung'] === '1') {
                $pdf->SetFont('zapfdingbats', '', 10);
                //grün
                $pdf->SetTextColor(...$colour);
                $pdf->MultiCell(40, 5, TCPDF_FONTS::unichr(52), 0, 'L', 0, 0);
            } else {
                $pdf->MultiCell(40, 5, "Quasi stationär", 0, 'L', 0, 0);
            }
        }
        //schwarz 
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->MultiCell(40, 5, "Schwingungsklasse: ", 0, 'R', 0, 0);
        $pdf->MultiCell(40, 5, $row['AR_Schwingungsklasse'], 0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->MultiCell(40, 5, "Laseranwendung: ", 0, 'R', 0, 0);
        if ($row['Laseranwendung'] === '0') {
            $pdf->SetFont('zapfdingbats', '', 10);
            $pdf->MultiCell(40, 5, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
        } else {
            $pdf->SetFont('zapfdingbats', '', 10);
            $pdf->SetTextColor(...$colour);
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(52), 0, 'L', 0, 0);
            $pdf->SetTextColor(0);
            $pdf->SetFont('helvetica', '', 9);
            $pdf->MultiCell(30, 5, " Klasse: " . $row['Laserklasse'], 0, 'L', 0, 0);
        }
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->MultiCell(40, 5, "FB ÖNORM B5220:", 0, 'R', 0, 0);
        $pdf->MultiCell(20, 5, " " . $row['Fussboden OENORM B5220'], 0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->MultiCell(40, 5, "Abdunkelbarkeit: ", 0, 'R', 0, 0);
        if ($row['Abdunkelbarkeit'] === '0') {
            $pdf->SetFont('zapfdingbats', '', 10);
            $pdf->MultiCell(40, 5, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
        } else {
            if ($row['Abdunkelbarkeit'] === '1') {
                $pdf->SetTextColor(0);
                $pdf->SetFont('helvetica', '', 9);
                $pdf->MultiCell(40, 5, "Vollverdunkelbar", 0, 'L', 0, 0);
            } else {
                $pdf->SetTextColor(0);
                $pdf->SetFont('helvetica', '', 9);
                $pdf->MultiCell(40, 5, "Abdunkelung", 0, 'L', 0, 0);
            }
        }
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->Ln();

        $pdf->MultiCell(40, 5, "EMV: ", 0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 9);
        if ($row['ET_EMV_ja-nein'] === '0') {
            $pdf->MultiCell(40, 5, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
        } else {
            //grün
            $pdf->SetTextColor(...$colour);
            $pdf->MultiCell(40, 5, TCPDF_FONTS::unichr(52), 0, 'L', 0, 0);
        }
        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->MultiCell(40, 5, "EMV-Maßnahme: ", 0, 'R', 0, 0);
        $pdf->MultiCell(40, 5, $row['ET_EMV'], 0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->MultiCell(180, 5, 'ASTV', 'T', 'L', 1, 0, '', '', true);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->Ln();
        $pdf->MultiCell(40, 5, "Arbeitsplätze: ", 0, 'R', 0, 0);
        $pdf->MultiCell(40, 5, $row['AR_APs'], 0, 'L', 0, 0);
        $pdf->MultiCell(80, 5, "Arbeitsplatz mit bes. Gefährdung: ", 0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 9);
        if ($row['AP_Gefaehrdung'] === '0') {
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
        } else {
            //grün
            $pdf->SetTextColor(...$colour);
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(52), 0, 'L', 0, 0);
        }
        //schwarz
        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln();
        $pdf->MultiCell(40, 5, "Belichtungsfläche: ", 0, 'R', 0, 0);
        $pdf->MultiCell(40, 5, number_format((float)$row['Belichtungsfläche'], 2, ',', '') . "%", 0, 'L', 0, 0);
        $pdf->MultiCell(80, 5, "Raum für uberwiegend geistige Tätigkeiten: ", 0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 9);
        if ($row['AP_Geistige'] === '0') {
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
        } else {
            //grün
            $pdf->SetTextColor(...$colour);
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(52), 0, 'L', 0, 0);
        }
        $pdf->Ln();
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->MultiCell(180, 5, 'Bauphysik', 'T', 'L', 1, 0, '', '', true);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->Ln();
        $pdf->MultiCell(40, 5, "Akusitk_T(500Hz)±20%: ", 0, 'R', 0, 0);
        $pdf->MultiCell(40, 5, $row['PHY_Akustik_T500'], 0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->MultiCell(80, 5, "Akusitk_mitt.Schallgrad (250/500/1K/2K/4K): ", 0, 'R', 0, 0);
        $pdf->MultiCell(120, 5, $row['PHY_Akustik_Schallgrad'], 0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->MultiCell(180, 5, 'Elektro', 'T', 'L', 1, 0, '', '', true);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->Ln();
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->MultiCell(40, 5, "Not-Aus: " . $row['EL_Not_Aus'], 0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 9);
        if ($row['EL_Not_Aus'] === '0') {
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
        } else {
            //grün
            $pdf->SetTextColor(...$colour);
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(52), 0, 'L', 0, 0);
        }
        //schwarz
        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->MultiCell(40, 5, "Not-Aus-Funktion: ", 0, 'R', 0, 0);
        $pdf->MultiCell(80, 5, $row['EL_Not_Aus_Funktion'], 0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->MultiCell(40, 5, "Beleuchtungsstärke: ", 0, 'R', 0, 0);
        $pdf->MultiCell(40, 5, $row['EL_Beleuchtungsstaerke'] . " lx", 0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->MultiCell(40, 5, "Netzarten: ", 0, 'R', 0, 0);
        $pdf->MultiCell(20, 5, "AV: ", 0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 9);
        if ($row['AV'] === '0') {
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
        } else {
            //grün
            $pdf->SetTextColor(...$colour);
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(52), 0, 'L', 0, 0);
        }
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->MultiCell(40, 5, "SV: ", 0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 9);
        if ($row['SV'] === '0') {
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
        } else {
            //grün
            $pdf->SetTextColor(...$colour);
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(52), 0, 'L', 0, 0);
        }
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->MultiCell(40, 5, "USV: ", 0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 9);
        if ($row['USV'] === '0') {
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
        } else {
            //grün
            $pdf->SetTextColor(...$colour);
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(52), 0, 'L', 0, 0);
        }
        $pdf->Ln();
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->MultiCell(40, 5, "", 0, 'R', 0, 0);
        $pdf->MultiCell(40, 5, "16A 3Phasig: " . $row['ET_16A_3Phasig_Einzelanschluss'] . " Stk", 0, 'R', 0, 0);
        $pdf->MultiCell(40, 5, "32A 3Phasig: " . $row['ET_32A_3Phasig_Einzelanschluss'] . " Stk", 0, 'R', 0, 0);
        $pdf->MultiCell(40, 5, "64A 3Phasig: " . $row['ET_64A_3Phasig_Einzelanschluss'] . " Stk", 0, 'R', 0, 0);
        $pdf->Ln();
        $pdf->MultiCell(40, 5, "Signaleinrichtungen: ", 0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 9);
        if ($row['EL_Signaleinrichtung'] === '0') {

            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
        } else {
            $pdf->SetTextColor(...$colour);
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(52), 0, 'L', 0, 0);
        }
        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln();

        check4newpage($pdf, 30);
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->MultiCell(180, 5, 'Haustechnik', 'T', 'L', 1, 0, '', '', true);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->Ln();
        $pdf->MultiCell(40, 8, "Belüftung: ", 0, 'R', 0, 0);
        $pdf->MultiCell(40, 5, $row['HT_Belueftung'], 0, 'L', 0, 0);
        $pdf->MultiCell(40, 8, "Entlüftung: ", 0, 'R', 0, 0);
        $pdf->MultiCell(40, 5, $row['HT_Entlueftung'], 0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->MultiCell(40, 8, "Luftmenge: ", 0, 'R', 0, 0);
        $text = "";
        if (is_numeric($row['HT_Luftmenge m3/h'])) {
            $text = $row['HT_Luftmenge m3/h'] . " m3/h";
        } else {
            $text = $row['HT_Luftmenge m3/h'];
        }

        $pdf->MultiCell(40, 5, $text, 0, 'L', 0, 0);
        if ($pdf->getStringHeight(40, $text, false, true, '', 1) > 8) {
            $pdf->Ln();
            $pdf->MultiCell(80, 5, "", 0, 'L', 0, 0);
        }

        $pdf->MultiCell(40, 8, "Luftwechsel auf 3m: ", 0, 'R', 0, 0);
        $pdf->MultiCell(80, 5, $row['HT_Luftwechsel 1/h'] . "/h", 0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->MultiCell(40, 5, "Kühlung: ", 0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 9);
        if ($row['HT_Kuehlung'] === '0') {
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
        } else {
            //grün
            $pdf->SetTextColor(...$colour);
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(52), 0, 'L', 0, 0);
        }
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 9);


        $pdf->MultiCell(30, 8, "Kälteabgabe: ", 0, 'R', 0, 0);
        $pdf->MultiCell(60, 5, $row['HT_Kaelteabgabe_Typ'], 0, 'L', 0, 0);
        if ($pdf->getStringHeight(30, $row['HT_Kaelteabgabe_Typ'], false, true, '', 1) > 8) {
            $pdf->Ln();
        }

        $pdf->MultiCell(30, 8, "Temp. Kühlung: ", 0, 'R', 0, 0);
        $pdf->MultiCell(15, 5, $row['HT_Raumtemp Sommer °C'], 0, 'L', 0, 0);


        $pdf->Ln();
        $pdf->MultiCell(40, 5, "Heizung: ", 0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 9);
        if ($row['HT_Heizung'] === '0') {
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
        } else {
            //grün
            $pdf->SetTextColor(...$colour);
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(52), 0, 'L', 0, 0);
        }
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 9);

        $pdf->MultiCell(30, 8, "Wärmeabgabe: ", 0, 'R', 0, 0);

        $pdf->MultiCell(60, 5, $row['HT_Waermeabgabe_Typ'], 0, 'L', 0, 0);

        $pdf->MultiCell(30, 8, "Temp. Heizen: ", 0, 'R', 0, 0);
        $pdf->MultiCell(15, 5, $row['HT_Raumtemp Winter °C'], 0, 'L', 0, 0);

        if ($pdf->getStringHeight(30, $row['HT_Waermeabgabe_Typ'], false, true, '', 1) > 8) {
            $pdf->Ln();

        }

        $pdf->Ln();
        $pdf->MultiCell(40, 8, "Notdusche: ", 0, 'R', 0, 0);
        $pdf->MultiCell(20, 6, " " . $row['HT_Notdusche'] . " Stk", 0, 'L', 0, 0);
        $pdf->MultiCell(60, 5, "Abluft Sicherheitsschränke: ", 0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 9);
        if ($row['HT_Abluft_Sicherheitsschrank_Unterbau_Stk'] + $row['HT_Abluft_Sicherheitsschrank_Stk'] <= 0) {
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
        } else {
            //grün
            $pdf->SetTextColor(...$colour);
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(52), 0, 'L', 0, 0);
        }
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->Ln();
        //$pdf->MultiCell(180, 5, "",'B', 'L', 0, 0);              
        // Wenn Seitenende? Überprüfen und neue Seite anfangen
        $y = $pdf->GetY();
        if (($y + 8) >= 270) {
            $pdf->AddPage();
        }
        $pdf->SetFont('helvetica', 'B', 10);
        // set color for background
        $pdf->SetFillColor(...$colour);
        $pdf->MultiCell(180, 5, 'Gase', 'T', 'L', 1, 0, '', '', true);
        $pdf->Ln();
        $pdf->SetFont('helvetica', '', 9);
        $pdf->MultiCell(160, 5, "Zentrale Gasversorgung: ", 0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->MultiCell(40, 5, "DL: ", 0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 9);
        if ($row['DL-tech'] === '0') {
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
        } else {
            //grün
            $pdf->SetTextColor(...$colour);
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(52), 0, 'L', 0, 0);
        }
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->MultiCell(20, 5, "He-RF: ", 0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 9);
        if ($row['He-RF'] === '0') {
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
            $pdf->MultiCell(20, 5, "", 0, 'L', 0, 0);
        } else {
            //grün
            $pdf->SetTextColor(...$colour);
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(52), 0, 'L', 0, 0);
            $pdf->MultiCell(20, 5, "", 0, 'L', 0, 0);
        }
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->Ln();
        $pdf->MultiCell(160, 5, "Dezentrale Gasversorgung: ", 0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->MultiCell(40, 5, "O2: ", 0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 9);
        if ($row['O2'] === '0') {
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
        } else {
            //grün
            $pdf->SetTextColor(...$colour);
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(52), 0, 'L', 0, 0);
        }
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->MultiCell(20, 5, "VA: ", 0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 9);
        if ($row['VA'] === '0') {
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
        } else {
            //grün
            $pdf->SetTextColor(...$colour);
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(52), 0, 'L', 0, 0);
        }
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->MultiCell(20, 5, "CO2: ", 0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 9);
        if ($row['CO2'] === '0') {
            $pdf->MultiCell(8, 5, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
        } else {
            //grün
            $pdf->SetTextColor(...$colour);
            $pdf->MultiCell(8, 5, TCPDF_FONTS::unichr(52), 0, 'L', 0, 0);
        }
        $pdf->SetFont('helvetica', '', 9);
        $pdf->MultiCell(22, 5, "N2: ", 0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 9);
        if ($row['N2'] === '0') {
            $pdf->MultiCell(8, 5, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
        } else {
            //grün
            $pdf->SetTextColor(...$colour);
            $pdf->MultiCell(8, 5, TCPDF_FONTS::unichr(52), 0, 'L', 0, 0);
        }
        $pdf->Ln();
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->MultiCell(40, 5, "Ar: ", 0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 9);
        if ($row['Ar'] === '0') {
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
        } else {
            //grün
            $pdf->SetTextColor(...$colour);
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(52), 0, 'L', 0, 0);
        }
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->MultiCell(20, 5, "H2: ", 0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 9);
        if ($row['H2'] === '0') {
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
        } else {
            //grün
            $pdf->SetTextColor(...$colour);
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(52), 0, 'L', 0, 0);
        }
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->MultiCell(20, 5, "He: ", 0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 9);
        if ($row['He'] === '0') {
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
        } else {
            //grün+
            $pdf->SetTextColor(...$colour);
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(52), 0, 'L', 0, 0);
        }
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->MultiCell(20, 5, "LHe: ", 0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 9);
        if ($row['LHe'] === '0') {
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
        } else {
            //grün
            $pdf->SetTextColor(...$colour);
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(52), 0, 'L', 0, 0);
        }
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->MultiCell(20, 5, "LN: ", 0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 9);
        if ($row['LN'] === '0') {
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
        } else {
            //grün
            $pdf->SetTextColor(...$colour);
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(52), 0, 'L', 0, 0);
        }
        //schwarz
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->Ln();
        $pdf->MultiCell(40, 5, "Spezialgase: ", 0, 'R', 0, 0);
        $pdf->MultiCell(40, 5, $row['Spezialgase'], 0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->MultiCell(160, 5, "Sicherheitseinrichtungen: ", 0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->MultiCell(40, 5, "O2-Mangel: ", 0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 9);
        if ($row['O2_Mangel'] === '0') {
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
        } else {
            //grün
            $pdf->SetTextColor(...$colour);
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(52), 0, 'L', 0, 0);
        }
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->MultiCell(30, 5, "CO2-Melder: ", 0, 'R', 0, 0);
        $pdf->SetFont('zapfdingbats', '', 9);
        if ($row['CO2_Melder'] === '0') {
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(54), 0, 'L', 0, 0);
        } else {
            //grün
            $pdf->SetTextColor(...$colour);
            $pdf->MultiCell(10, 5, TCPDF_FONTS::unichr(52), 0, 'L', 0, 0);
        }
        $pdf->Ln();
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->MultiCell(40, 5, "Gaswarneinrichtung-Art: ", 0, 'R', 0, 0);
        $pdf->MultiCell(40, 5, $row['Gaswarneinrichtung-Art'], 0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->SetFont('helvetica', 'B', 10);
        // set color for background
        $pdf->SetFillColor(...$colour);
        check4newpage($pdf, 15);
        $pdf->MultiCell(180, 5, 'VEXAT', 'T', 'L', 1, 0, '', '', true);
        $pdf->Ln();
        $pdf->SetFont('helvetica', '', 9);
        $pdf->MultiCell(40, 8, "Zonen: ", 0, 'R', 0, 0);
        $rowHeightVEXAT = $pdf->getStringHeight(100, $row['VEXAT_Zone'], false, true, '', 1);
        $pdf->MultiCell(100, $rowHeightVEXAT, $row['VEXAT_Zone'], 0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->MultiCell(50, 5, "Anzahl Sicherheitsschränke: ", 0, 'R', 0, 0);
        $pdf->MultiCell(40, 5, $row['HT_Abluft_Sicherheitsschrank_Unterbau_Stk'] + $row['HT_Abluft_Sicherheitsschrank_Stk'], 0, 'L', 0, 0);
        $pdf->Ln();
        $pdf->SetTextColor(0);
        $pdf->SetFont('helvetica', '', 9);

    }


    // -------------------------Elemente im Raum laden--------------------------
    $sql = "SELECT tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_varianten.Variante, tabelle_räume_has_tabelle_elemente.Anzahl, tabelle_räume_has_tabelle_elemente.Standort, tabelle_räume_has_tabelle_elemente.Verwendung, tabelle_räume_has_tabelle_elemente.Kurzbeschreibung, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, tabelle_auftraggeber_gewerke.Gewerke_Nr, tabelle_auftraggeber_ghg.GHG, tabelle_auftraggeberg_gug.GUG, tabelle_projekt_varianten_kosten.Kosten, tabelle_räume_has_tabelle_elemente.id, tabelle_bestandsdaten.Inventarnummer, tabelle_bestandsdaten.Seriennummer, tabelle_bestandsdaten.Anschaffungsjahr, tabelle_hersteller.Hersteller, tabelle_geraete.Typ, tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten, tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente
    FROM tabelle_hersteller RIGHT JOIN (tabelle_geraete RIGHT JOIN (tabelle_bestandsdaten RIGHT JOIN (tabelle_auftraggeber_gewerke RIGHT JOIN (tabelle_auftraggeber_ghg RIGHT JOIN (tabelle_auftraggeberg_gug RIGHT JOIN (tabelle_projekt_element_gewerk RIGHT JOIN (tabelle_projekt_varianten_kosten INNER JOIN (tabelle_räume INNER JOIN (tabelle_varianten INNER JOIN (tabelle_elemente INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_elemente.idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) ON tabelle_varianten.idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) ON (tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte) AND (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten)) ON (tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte = tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte) AND (tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente = tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente)) ON tabelle_auftraggeberg_gug.idtabelle_auftraggeberg_GUG = tabelle_projekt_element_gewerk.tabelle_auftraggeberg_gug_idtabelle_auftraggeberg_GUG) ON tabelle_auftraggeber_ghg.idtabelle_auftraggeber_GHG = tabelle_projekt_element_gewerk.tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG) ON tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke = tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke) ON tabelle_bestandsdaten.tabelle_räume_has_tabelle_elemente_id = tabelle_räume_has_tabelle_elemente.id) ON tabelle_geraete.idTABELLE_Geraete = tabelle_bestandsdaten.tabelle_geraete_idTABELLE_Geraete) ON tabelle_hersteller.idtabelle_hersteller = tabelle_geraete.tabelle_hersteller_idtabelle_hersteller
    WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . ") AND ((tabelle_räume.idTABELLE_Räume)=" . $valueOfRoomID . ") AND tabelle_räume_has_tabelle_elemente.Anzahl > 0)
    ORDER BY tabelle_auftraggeber_gewerke.Gewerke_Nr, tabelle_auftraggeber_ghg.GHG, tabelle_elemente.ElementID, tabelle_varianten.Variante, tabelle_räume_has_tabelle_elemente.id;";

    $result = $mysqli->query($sql);
    $num_rows = mysqli_num_rows($result);
    if ($num_rows > 0) {
        $pdf->AddPage();
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->MultiCell(180, 8, "Labortechnik Einrichtung: ", 0, 'L', 0, 0);
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Ln();
        $y = $pdf->GetY();
        if (($y + 8) >= 270) {
            $pdf->AddPage();
        }
        $rowHeightFirstLine = $pdf->getStringHeight(50, "ElementID", false, true, '', 1);
        $pdf->MultiCell(20, $rowHeightFirstLine, "ElementID", 'B', 'C', 0, 0);
        $pdf->MultiCell(20, $rowHeightFirstLine, "Var", 'B', 'C', 0, 0);
        $pdf->MultiCell(120, $rowHeightFirstLine, "Element", 'B', 'L', 0, 0);
        $pdf->MultiCell(20, $rowHeightFirstLine, "Stk", 'B', 'C', 0, 0);
        $pdf->Ln();
    }
    $fill = 0;
    $pdf->SetFillColor(244, 244, 244);
    $idRoombookEntry = 0;
    $bestandsCounter = 1;

    while ($row = $result->fetch_assoc()) {
        if ($idRoombookEntry != $row['id']) {
            $fill = !$fill;
            $bestandsCounter = 1;
            $pdf->SetFont('helvetica', '', 8);
            $rowHeightMainLine = $pdf->getStringHeight(120, $row['Bezeichnung'], false, true, '', 1);
            // Wenn Seitenende? Überprüfen und neue Seite anfangen
            $y = $pdf->GetY();
            if (($y + $rowHeightMainLine) >= 270) {
                $pdf->AddPage();
            }
            $pdf->MultiCell(20, $rowHeightMainLine, $row['ElementID'], 0, 'C', $fill, 0);
            $pdf->MultiCell(20, $rowHeightMainLine, $row['Variante'], 0, 'C', $fill, 0);
            $pdf->MultiCell(120, $rowHeightMainLine, $row['Bezeichnung'], 0, 'L', $fill, 0);
            $pdf->MultiCell(20, $rowHeightMainLine, $row['Anzahl'], 0, 'C', $fill, 0);
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
