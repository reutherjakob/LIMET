<?php

include 'pdf_createBericht_MYPDFclass.php'; //require_once('TCPDF-main/TCPDF-main/tcpdf.php'); is in class file
include 'pdf_createBericht_utils.php';
if (!function_exists('utils_connect_sql')) {  include "_utils.php"; }
include 'pdf_createMTTabelle.php';

check_login();

$roomIDs = filter_input(INPUT_GET, 'roomID');
$roomIDsArray = explode(",", $roomIDs);
$Änderungsdatum = getValidatedDateFromURL();


$mapping = array("raum_nr_alt" => "raum_nr_neu",
    "raumbezeichnung_alt" => "raumbezeichnung_neu",
    "funktionelle_raum_nr_alt" => "funktionelle_raum_nr_neu",
    "funktionsteilstelle_alt" => "funktionsteilstelle_neu",
    "raumbereich_nutzer_alt" => "raumbereich_nutzer_neu",
    "anmerkung_allg_alt" => "anmerkung_allg_neu",
    "nutzflaeche_alt" => "nutzflaeche_neu",
    "abdunkel_alt" => "abdunkel_neu",
    "strahlen_alt" => "strahlen_neu",
    "laser_alt" => "laser_neu",
    "h620_alt" => "h620_neu",
    "gmp_alt" => "gmp_neu",
    "iso_alt" => "iso_neu",
    "1 Kreis O2" => "1 Kreis O2_copy1",
    "2 Kreis O2" => "2 Kreis O2_copy1",
    "1 Kreis Va" => "1 Kreis Va_copy1",
    "2 Kreis Va" => "2 Kreis Va_copy1",
    "1 Kreis DL-5" => "1 Kreis DL-5_copy1",
    "2 Kreis DL-5" => "2 Kreis DL-5_copy1",
    "DL-10" => "DL-10_copy1",
    "DL-tech" => "DL-tech_copy1",
    "CO2" => "CO2_copy1",
    "NGA" => "NGA_copy1",
    "N2O" => "N2O_copy1",
    "AV" => "AV_copy1",
    "SV" => "SV_copy1",
    "ZSV" => "ZSV_copy1",
    "USV" => "USV_copy1",
    "Anwendungsgruppe" => "Anwendungsgruppe_copy1",
    "Anmerkung MedGas" => "Anmerkung MedGas_copy1",
    "Anmerkung Elektro" => "Anmerkung Elektro_copy1",
    "Anmerkung HKLS" => "Anmerkung HKLS_copy1",
    "Anmerkung Geräte" => "Anmerkung Geräte_copy1",
    "Anmerkung FunktionBO" => "Anmerkung FunktionBO_copy1",
    "Anmerkung BauStatik" => "Anmerkung BauStatik_copy1",
    "Raumhoehe_alt" => "Raumhoehe_neu",
    "Allgemeine Hygieneklasse_alt" => "Allgemeine Hygieneklasse_neu",
    "IT-Anbindung_alt" => "IT-Anbindung_neu",
    "FB OENORM B5220_alt" => "FB OENORM B5220_neu",
    "bauphase alt" => "bauphase neu",
    "Raumhoehe 2 alt" => "Raumhoehe 2 neu",
    "Belichtungsfläche alt" => "Belichtungsfläche neu",
    "Umfang alt" => "Umfang neu",
    "Volumen alt" => "Volumen neu",
    "Aufenthaltsraum alt" => "Aufenthaltsraum neu",
    "EL_Beleuchtung 1 Typ alt" => "EL_Beleuchtung 1 Typ neu",
    "EL_Beleuchtung 2 Typ alt" => "EL_Beleuchtung 2 Typ neu",
    "EL_Beleuchtung 3 Typ alt" => "EL_Beleuchtung 3 Typ neu",
    "EL_Beleuchtung 4 Typ alt" => "EL_Beleuchtung 4 Typ neu",
    "EL_Beleuchtung 5 Typ alt" => "EL_Beleuchtung 5 Typ neu",
    "EL_Beleuchtung 1 Stk alt" => "EL_Beleuchtung 1 Stk neu",
    "EL_Beleuchtung 2 Stk alt" => "EL_Beleuchtung 2 Stk neu",
    "EL_Beleuchtung 3 Stk alt" => "EL_Beleuchtung 3 Stk neu",
    "EL_Beleuchtung 4 Stk alt" => "EL_Beleuchtung 4 Stk neu",
    "EL_Beleuchtung 5 Stk alt" => "EL_Beleuchtung 5 Stk neu",
    "EL_Lichtschaltung BWM alt" => "EL_Lichtschaltung BWM neu",
    "EL_Beleuchtung dimmbar alt" => "EL_Beleuchtung dimmbar neu",
    "EL_Brandmelder Decke alt" => "EL_Brandmelder Decke neu",
    "EL_Brandmelder ZwDecke alt" => "EL_Brandmelder ZwDecke neu",
    "EL_AV Steckdosen Stk alt" => "EL_AV Steckdosen Stk neu",
    "EL_SV Steckdosen Stk alt" => "EL_SV Steckdosen Stk neu",
    "EL_USV Steckdosen Stk alt" => "EL_USV Steckdosen Stk neu",
    "EL_ZSV Steckdosen Stk alt" => "EL_ZSV Steckdosen Stk neu",
    "EL_Roentgen 16A Stk alt" => "EL_Roentgen 16A Stk neu",
    "EL_Laser 16A Stk alt" => "EL_Laser 16A Stk neu",
    "ET_RJ45-Ports alt" => "ET_RJ45-Ports neu",
    "EL_Jalousien alt" => "EL_Jalousien neu",
    "EL_Doppeldatendose Stk alt" => "EL_Doppeldatendose Stk neu",
    "EL_Einzel-Datendose Stk alt" => "EL_Einzel-Datendose Stk neu",
    "EL_Bodendose Typ alt" => "EL_Bodendose Typ neu",
    "EL_Bodendose Stk alt" => "EL_Bodendose Stk neu",
    "EL_Kamera Stk alt" => "EL_Kamera Stk neu",
    "EL_Lautsprecher Stk alt" => "EL_Lautpsrecher Stk neu",
    "EL_Uhr - Wand Stk alt" => "EL_Uhr - Wand Stk neu",
    "EL_Uhr - Decke Stk alt" => "EL_Uhr - Decke Stk neu",
    "EL_Notlicht RZL Stk alt" => "EL_Notlicht RZL Stk neu",
    "EL_Notlicht SL Stk alt" => "EL_Notlicht SL Stk neu",
    "EL_Lichtruf-Terminal Stk alt" => "EL_Lichtruf-Terminal Stk neu",
    "EL_Lichtruf-Steckmodul Stk alt" => "EL_Lichtruf-Steckmodul Stk neu",
    "EL_Lichtfarbe K alt" => "EL_Lichtfarbe K neu",
    "EL_Leistungsbedarf W/m2 alt" => "EL_Leistungsbedarf W/m2 neu",
    "ET_Anschlussleistung_W_alt" => "ET_Anschlussleistung_W_neu",
    "ET_Anschlussleistung_AV_W_alt" => "ET_Anschlussleistung_AV_W_neu",
    "ET_Anschlussleistung_SV_W_alt" => "ET_Anschlussleistung_SV_W_neu",
    "ET_Anschlussleistung_ZSV_W_alt" => "ET_Anschlussleistung_ZSV_W_neu",
    "ET_Anschlussleistung_USV_W_alt" => "ET_Anschlussleistung_USV_W_neu",
    "HT_Summe Kühlung W alt" => "HT_Summe Kühlung W neu",
    "HT_Luftmenge m3/h alt" => "HT_Luftmenge m3/h neu",
    "HT_Luftwechsel 1/h alt" => "HT_Luftwechsel 1/h neu",
    "HT_Kühlung Lüftung W alt" => "HT_Kühlung Lüftung W neu",
    "HT_Heizlast W alt" => "HT_Heizlast W neu",
    "HT_Kühllast W alt" => "HT_Kühllast W neu",
    "HT_Fussbodenkühlung W alt" => "HT_Fussbodenkühlung W neu",
    "HT_Kühldecke W alt" => "HT_Kühldecke W neu",
    "HT_Fancoil W alt" => "HT_Fancoil W neu",
    "HT_Raumtemp Sommer °C alt" => "HT_Raumtemp Sommer °C neu",
    "HT_Raumtemp Winter °C alt" => "HT_Raumtemp Winter °C neu",
    "HT_Notdusche alt" => "HT_Notdusche neu",
    "HT_Waermeabgabe_W_alt" => "HT_Waermeabgabe_W_neu",
    "HT_Geraeteabluft m3/h alt" => "HT_Geraeteabluft m3/h neu",
    "HT_Kühlwasserleistung W alt" => "HT_Kühlwasserleistung W neu",
    "AR_Ausstattung alt" => "AR_Ausstattung neu",
    "AR_APs alt" => "AR_APs neu",
    "Raumtyp BH alt" => "Raumtyp BH neu",
    "AR_Belichtung-nat alt" => "AR_Belichtung-nat neu",
    "AR_Schwingungsklasse alt" => "AR_Schwingungsklasse neu",
    "ET_EMV alt" => "ET_EMV neu",
    "ET_EMV_ja-nein alt" => "ET_EMV_ja-nein neu",
    "AR_Akustik alt" => "AR_Akustik neu",
    "NF_Soll alt" => "NF_Soll neu",
    "O2 alt" => "O2 neu",
    "VA alt" => "VA neu",
    "DL-5 alt" => "DL-5 neu",
    "H2 alt" => "H2 neu",
    "He alt" => "He neu",
    "He-RF alt" => "He-RF neu",
    "Ar alt" => "Ar neu",
    "N2 alt" => "N2 neu",
    "Raumhoehe_Soll alt" => "Raumhoehe_Soll neu",
    "AR_AnwesendePers alt" => "AR_AnwesendePers neu",
    "RaumnrBestand alt" => "RaumnrBestand neu",
    "GebaeudeBestand alt" => "GebaeudeBestand neu");

$mp2 = array(//tabelle änderunge => tabelle_räume
    "funktionelle_raum_nr_neu" => "Funktionelle Raum Nr",
    "funktionsteilstelle_neu" => "Bezeichnung",
    "anmerkung_allg_neu" => "",
    "bauphase neu" => "",
    "EL_Jalousien neu" => "",
    "AR_Akustik neu" => "",
    "NF_Soll neu" => "",
    "EL_Leistungsbedarf W/m2 neu" => "",
    "HT_Notdusche neu" => "HT_Notdusche",
    "AR_Ausstattung neu" => "",
    "AR_APs neu" => "",
    "Raumtyp BH neu" => "",
    "AR_Belichtung-nat neu" => "",
    "AR_Schwingungsklasse neu" => "",
    "ET_EMV neu" => "",
    "ET_EMV_ja-nein neu" => "",
    "HT_Geraeteabluft m3/h neu" => "",
    "HT_Kühlwasserleistung W neu" => "",
    "Raumhoehe_Soll neu" => "",
    "AR_AnwesendePers neu" => "",
    "RaumnrBestand neu" => "",
    "GebaeudeBestand neu" => "",
    "HT_Luftwechsel 1/h neu" => "HT_Luftwechsel 1/h",
    "HT_Raumtemp Sommer °C neu" => "HT_Raumtemp Sommer °C",
    "HT_Raumtemp Winter °C neu" => "HT_Raumtemp Winter °C",
    "raum_nr_neu" => "Raumnr",
    "raumbezeichnung_neu" => "Raumbezeichnung",
    "raumbereich_nutzer_neu" => "Raumbereich Nutzer",
    "nutzflaeche_neu" => "Nutzfläche",
    "abdunkel_neu" => "Abdunkelbarkeit",
    "strahlen_neu" => "Strahlenanwendung",
    "laser_neu" => "Laseranwendung",
    "h620_neu" => "H6020",
    "gmp_neu" => "GMP",
    "iso_neu" => "ISO",
    "1 Kreis O2_copy1" => "1 Kreis O2",
    "2 Kreis O2_copy1" => "2 Kreis O2",
    "1 Kreis Va_copy1" => "1 Kreis Va",
    "2 Kreis Va_copy1" => "2 Kreis Va",
    "1 Kreis DL-5_copy1" => "1 Kreis DL-5",
    "2 Kreis DL-5_copy1" => "2 Kreis DL-5",
    "DL-10_copy1" => "DL-10",
    "DL-tech_copy1" => "DL-tech",
    "CO2_copy1" => "CO2",
    "NGA_copy1" => "NGA",
    "N2O_copy1" => "N2O",
    "AV_copy1" => "AV",
    "SV_copy1" => "SV",
    "ZSV_copy1" => "ZSV",
    "USV_copy1" => "USV",
    "Anwendungsgruppe_copy1" => "Anwendungsgruppe",
    "Anmerkung MedGas_copy1" => "Anmerkung MedGas",
    "Anmerkung Elektro_copy1" => "Anmerkung Elektro",
    "Anmerkung HKLS_copy1" => "Anmerkung HKLS",
    "Anmerkung Geräte_copy1" => "Anmerkung Geräte",
    "Anmerkung FunktionBO_copy1" => "Anmerkung FunktionBO",
    "Anmerkung BauStatik_copy1" => "Anmerkung BauStatik",
    "Raumhoehe_neu" => "Raumhoehe 1", "Raumhoehe 2 neu" => "Raumhoehe 2",
    "Allgemeine Hygieneklasse_neu" => "Allgemeine Hygieneklasse",
    "IT-Anbindung_neu" => "IT Anbindung",
    "FB OENORM B5220_neu" => "Fussboden OENORM B5220",
    "Belichtungsfläche neu" => "Belichtungsfläche",
    "Umfang neu" => "Umfang",
    "Volumen neu" => "Volumen",
    "Aufenthaltsraum neu" => "Aufenthaltsraum",
    "EL_Beleuchtung 1 Typ neu" => "EL_Beleuchtung 1 Typ",
    "EL_Beleuchtung 2 Typ neu" => "EL_Beleuchtung 2 Typ",
    "EL_Beleuchtung 3 Typ neu" => "EL_Beleuchtung 3 Typ",
    "EL_Beleuchtung 4 Typ neu" => "EL_Beleuchtung 4 Typ",
    "EL_Beleuchtung 5 Typ neu" => "EL_Beleuchtung 5 Typ",
    "EL_Beleuchtung 1 Stk neu" => "EL_Beleuchtung 1 Stk",
    "EL_Beleuchtung 2 Stk neu" => "EL_Beleuchtung 2 Stk",
    "EL_Beleuchtung 3 Stk neu" => "EL_Beleuchtung 3 Stk",
    "EL_Beleuchtung 4 Stk neu" => "EL_Beleuchtung 4 Stk",
    "EL_Beleuchtung 5 Stk neu" => "EL_Beleuchtung 5 Stk",
    "EL_Lichtschaltung BWM neu" => "EL_Lichtschaltung BWM JA/NEIN",
    "EL_Beleuchtung dimmbar neu" => "EL_Beleuchtung dimmbar JA/NEIN",
    "EL_Brandmelder Decke neu" => "EL_Brandmelder Decke JA/NEIN",
    "EL_Brandmelder ZwDecke neu" => "EL_Brandmelder ZwDecke JA/NEIN",
    "EL_AV Steckdosen Stk neu" => "EL_AV Steckdosen Stk",
    "EL_SV Steckdosen Stk neu" => "EL_SV Steckdosen Stk",
    "EL_USV Steckdosen Stk neu" => "EL_USV Steckdosen Stk",
    "EL_ZSV Steckdosen Stk neu" => "EL_ZSV Steckdosen Stk",
    "EL_Roentgen 16A Stk neu" => "EL_Roentgen 16A CEE Stk",
    "EL_Laser 16A Stk neu" => "EL_Laser 16A CEE Stk",
    "ET_RJ45-Ports neu" => "ET_RJ45-Ports",
    "ET_Anschlussleistung_W_neu" => "ET_Anschlussleistung_W",
    "ET_Anschlussleistung_AV_W_neu" => "ET_Anschlussleistung_AV_W",
    "ET_Anschlussleistung_SV_W_neu" => "ET_Anschlussleistung_SV_W",
    "ET_Anschlussleistung_ZSV_W_neu" => "ET_Anschlussleistung_ZSV_W",
    "ET_Anschlussleistung_USV_W_neu" => "ET_Anschlussleistung_USV_W",
    "EL_Doppeldatendose Stk neu" => "EL_Doppeldatendose Stk",
    "EL_Einzel-Datendose Stk neu" => "EL_Einzel-Datendose Stk",
    "EL_Bodendose Typ neu" => "EL_Bodendose Typ",
    "EL_Bodendose Stk neu" => "EL_Bodendose Stk",
    "EL_Kamera Stk neu" => "EL_Kamera Stk",
    "EL_Lautpsrecher Stk neu" => "EL_Kamera Stk",
    "EL_Uhr - Wand Stk neu" => "EL_Uhr - Wand Stk",
    "EL_Uhr - Decke Stk neu" => "EL_Uhr - Decke Stk",
    "EL_Notlicht RZL Stk neu" => "EL_Notlicht RZL Stk",
    "EL_Notlicht SL Stk neu" => "EL_Notlicht SL Stk",
    "EL_Lichtruf-Terminal Stk neu" => "EL_Lichtruf - Terminal Stk",
    "EL_Lichtruf-Steckmodul Stk neu" => "EL_Lichtruf - Steckmodul Stk",
    "EL_Lichtfarbe K neu" => "EL_Lichtfarbe K",
    "HT_Summe Kühlung W neu" => "HT_Summe Kühlung W",
    "HT_Luftmenge m3/h neu" => "HT_Luftmenge m3/h",
    "HT_Kühlung Lüftung W neu" => "HT_Kühlung Lueftung W",
    "HT_Heizlast W neu" => "HT_Heizlast W",
    "HT_Kühllast W neu" => "HT_Kühllast W",
    "HT_Fussbodenkühlung W neu" => "HT_Fussbodenkühlung W",
    "HT_Kühldecke W neu" => "HT_Kühldecke W",
    "HT_Fancoil W neu" => "HT_Fancoil",
    "HT_Waermeabgabe_W_neu" => "HT_Waermeabgabe_W",
    "O2 neu" => "O2",
    "VA neu" => "Va",
    "DL-5 neu" => "DL-5",
    "H2 neu" => "H2",
    "He neu" => "He",
    "He-RF neu" => "He-RF",
    "Ar neu" => "Ar",
    "N2 neu" => "N2");

//     -----   FORMATTING VARIABLES    -----     
$marginTop = 17; // https://tcpdf.org/docs/srcdoc/TCPDF/files-config-tcpdf-config/ 
$marginBTM = 10;
/** @noinspection PhpUndefinedConstantInspection */
$SB = 420 - 2 * PDF_MARGIN_LEFT;  // A4: 210 x 297 // A3: 297 x 420
$SH = 297 - $marginTop - $marginBTM; // PDF_MARGIN_FOOTER;
$horizontalSpacerLN = 4;
$horizontalSpacerLN2 = 5;
$horizontalSpacerLN3 = 8;
$e_B = $SB / 6;
$e_B_3rd = $e_B / 3;
$e_B_2_3rd = $e_B - $e_B_3rd;
$e_C = $SB / 8;
$e_C_3rd = $e_C / 3;
$e_C_2_3rd = $e_C - $e_C_3rd;
$font_size = 6;
$block_header_height = 10;
$block_header_w = 25;
$einzugPlus = 10; //um den text auf die Höhe der anderen Angaben zu shiften bei ANM BO

$colour_line = array(110, 150, 80);
$style_dashed = array('width' => 0.1, 'cap' => 'round', 'join' => 'round', 'dash' => 4, 'color' => $colour_line); //$pdf->SetLineStyle(array('width' => 0.1, 'cap' => 'round', 'join' => 'round', 'dash' => 6, 'color' => array(110, 150, 80)));
$style_normal = array('width' => 0.3, 'cap' => 'round', 'join' => 'round', 'dash' => 0, 'color' => $colour_line);

/** @noinspection PhpUndefinedConstantInspection */
$pdf = new MYPDF('L', PDF_UNIT, "A3", true, 'UTF-8', false, true);
/** @noinspection PhpUndefinedConstantInspection */
$pdf = init_pdf_attributes($pdf, PDF_MARGIN_LEFT, $marginTop, $marginBTM, "A3", "Bauangaben");
$pdf->AddPage('L', 'A3');
$pdf->SetFillColor(0, 0, 0, 0); //$pdf->SetFillColor(244, 244, 244); 
$pdf->SetFont('helvetica', '', $font_size);
$pdf->SetLineStyle($style_normal);


$mysqli = utils_connect_sql();
$isnotVorentwurf = $_SESSION["projectPlanungsphase"] !== "Vorentwurf";

foreach ($roomIDsArray as $valueOfRoomID) {

    $stmt = $mysqli->prepare("SELECT * FROM `tabelle_raeume_aenderungen` WHERE `raum_id`= ?  AND `Timestamp` > ?"); // (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . "))
    $stmt->bind_param("is", $valueOfRoomID, $Änderungsdatum);
    $stmt->execute();
    $result = $stmt->get_result();
    $changeSqlResult = array();
    while ($row = $result->fetch_assoc()) {
        $changeSqlResult[] = $row;
    }
    $parameter_changes_t_räume = array();
    foreach ($mapping as $oldK => $newK) {
        $entries = array();
        foreach ($changeSqlResult as $changeKey => $entry) {
            if ($entry[$oldK] !== $entry[$newK]) {
                $entries[] = array(
                    'timestamp' => $entry['Timestamp'],
                    'oldValue' => $entry[$oldK],
                    $mp2[$newK] => $entry[$newK]
                );
            }
        }
        if (!empty($entries)) {
            usort($entries, function ($a, $b) {
                return $a['timestamp'] <=> $b['timestamp'];
            });
            if (end($entries)[$mp2[$newK]] !== reset($entries)['oldValue']) {
                $parameter_changes_t_räume[] = $mp2[$newK];
            }
        }
    }

    $sql = "SELECT tabelle_räume.idTABELLE_Räume, tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung, tabelle_räume.`Raumbereich Nutzer`, tabelle_räume.Geschoss, tabelle_räume.Bauetappe, tabelle_räume.`Fussboden OENORM B5220`, 
    tabelle_räume.`Allgemeine Hygieneklasse`, tabelle_räume.Bauabschnitt, tabelle_räume.Nutzfläche, tabelle_räume.Abdunkelbarkeit, tabelle_räume.Strahlenanwendung, tabelle_räume.Laseranwendung, tabelle_räume.H6020, 
    tabelle_räume.GMP, tabelle_räume.ISO, tabelle_räume.`1 Kreis O2`, tabelle_räume.`2 Kreis O2`, tabelle_räume.`1 Kreis Va`, tabelle_räume.`2 Kreis Va`, tabelle_räume.`1 Kreis DL-5`, tabelle_räume.`2 Kreis DL-5`, 
    tabelle_räume.`DL-10`, tabelle_räume.`DL-tech`, tabelle_räume.CO2, tabelle_räume.NGA, tabelle_räume.N2O, tabelle_räume.AV, tabelle_räume.SV, tabelle_räume.ZSV, tabelle_räume.USV, tabelle_räume.Anwendungsgruppe, 
    tabelle_räume.`Anmerkung MedGas`, tabelle_räume.`Anmerkung Elektro`, tabelle_räume.`Anmerkung HKLS`, tabelle_räume.`Anmerkung Geräte`, tabelle_räume.`Anmerkung FunktionBO`, tabelle_räume.`Anmerkung BauStatik`,
    tabelle_räume.HT_Waermeabgabe_W, tabelle_räume.`IT Anbindung`, tabelle_räume.`Fussboden OENORM B5220`, tabelle_räume.`Fussboden`, ROUND(tabelle_räume.`Umfang`,2) AS Umfang, ROUND(tabelle_räume.`Volumen`,2) AS Volumen,
    tabelle_räume.`Raumhoehe`, tabelle_räume.`Raumhoehe 2`, tabelle_räume.`Belichtungsfläche`, tabelle_projekte.Projektname, tabelle_planungsphasen.Bezeichnung, tabelle_räume.ET_Anschlussleistung_W, tabelle_räume.ET_Anschlussleistung_AV_W, 
    tabelle_räume.ET_Anschlussleistung_SV_W, tabelle_räume.ET_Anschlussleistung_ZSV_W, tabelle_räume.ET_Anschlussleistung_USV_W, tabelle_räume.`EL_AV Steckdosen Stk`, tabelle_räume.`EL_SV Steckdosen Stk`, tabelle_räume.`EL_ZSV Steckdosen Stk`, 
    tabelle_räume.`EL_USV Steckdosen Stk`, tabelle_räume.`ET_RJ45-Ports`, "
        . "tabelle_räume.`EL_Roentgen 16A CEE Stk`,tabelle_räume.GMP, tabelle_räume.HT_Abluft_Digestorium_Stk,tabelle_räume.HT_Notdusche, tabelle_räume.VE_Wasser, tabelle_räume.ET_16A_3Phasig_Einzelanschluss, "
        . "tabelle_räume.HT_Punktabsaugung_Stk, tabelle_räume.HT_Abluft_Sicherheitsschrank_Unterbau_Stk , tabelle_räume.HT_Abluft_Sicherheitsschrank_Stk, "
        . " tabelle_räume.`EL_Laser 16A CEE Stk`, tabelle_räume.`EL_Einzel-Datendose Stk`, tabelle_räume.`EL_Doppeldatendose Stk`, tabelle_räume.`EL_Bodendose Typ`, tabelle_räume.`EL_Bodendose Stk`, tabelle_räume.`EL_Beleuchtung 1 Typ`, tabelle_räume.`EL_Beleuchtung 2 Typ`, tabelle_räume.`EL_Beleuchtung 3 Typ`, tabelle_räume.`EL_Beleuchtung 4 Typ`, tabelle_räume.`EL_Beleuchtung 5 Typ`, tabelle_räume.`EL_Beleuchtung 1 Stk`, tabelle_räume.`EL_Beleuchtung 2 Stk`, tabelle_räume.`EL_Beleuchtung 3 Stk`, tabelle_räume.`EL_Beleuchtung 4 Stk`, tabelle_räume.`EL_Beleuchtung 5 Stk`, tabelle_räume.`EL_Lichtschaltung BWM JA/NEIN`, tabelle_räume.`EL_Beleuchtung dimmbar JA/NEIN`, tabelle_räume.`EL_Brandmelder Decke JA/NEIN`, tabelle_räume.`EL_Brandmelder ZwDecke JA/NEIN`, tabelle_räume.`EL_Kamera Stk`, tabelle_räume.`EL_Lautsprecher Stk`, tabelle_räume.`EL_Uhr - Wand Stk`, tabelle_räume.`EL_Uhr - Decke Stk`, tabelle_räume.`EL_Lichtruf - Terminal Stk`, tabelle_räume.`EL_Lichtruf - Steckmodul Stk`, tabelle_räume.`EL_Lichtfarbe K`, tabelle_räume.`EL_Notlicht RZL Stk`, tabelle_räume.`EL_Notlicht SL Stk`, tabelle_räume.`EL_Jalousie JA/NEIN`, tabelle_räume.`HT_Luftmenge m3/h`, CAST(REPLACE(tabelle_räume.`HT_Luftwechsel 1/h`,',','.') as decimal(10,2)) AS `HT_Luftwechsel`, tabelle_räume.`HT_Kühlung Lueftung W`, tabelle_räume.`HT_Heizlast W`, tabelle_räume.`HT_Kühllast W`, tabelle_räume.`HT_Fussbodenkühlung W`, tabelle_räume.`HT_Kühldecke W`, tabelle_räume.`HT_Fancoil W`, tabelle_räume.`HT_Summe Kühlung W`, tabelle_räume.`HT_Raumtemp Sommer °C`, tabelle_räume.`HT_Raumtemp Winter °C`, tabelle_räume.`AR_Ausstattung`, tabelle_räume.`Aufenthaltsraum` "
        . "FROM tabelle_planungsphasen INNER JOIN (tabelle_projekte INNER JOIN tabelle_räume ON tabelle_projekte.idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte) ON tabelle_planungsphasen.idTABELLE_Planungsphasen = tabelle_projekte.TABELLE_Planungsphasen_idTABELLE_Planungsphasen WHERE (((tabelle_räume.idTABELLE_Räume)=" . $valueOfRoomID . "))";

    $result_rooms = $mysqli->query($sql);
    while ($row = $result_rooms->fetch_assoc()) {

        $pdf->SetFillColor(255, 255, 255);
        raum_header($pdf, $horizontalSpacerLN3, $SB, $row['Raumbezeichnung'], $row['Raumnr'], $row['Raumbereich Nutzer'], $row['Geschoss'], $row['Bauetappe'], $row['Bauabschnitt'], "A3", $parameter_changes_t_räume); //utils function   

        if (null != ($row['Anmerkung FunktionBO'])) {
            $outstr = format_text(clean_string(br2nl($row['Anmerkung FunktionBO'])));
            $rowHeightComment = $pdf->getStringHeight($SB - $einzugPlus, $outstr, false, true, '', 1);
            $i = ($rowHeightComment > 6) ? $horizontalSpacerLN : 0;

            block_label_queer($block_header_w, $pdf, "BO-Beschr.", $rowHeightComment + $i, $block_header_height, $SB);
            $pdf->SetFont('helvetica', 'I', 10);
            $pdf->MultiCell($einzugPlus, $rowHeightComment, "", 0, 'L', 0, 0);
            $pdf->MultiCell($SB - $einzugPlus, $rowHeightComment, $outstr, 0, 'L', 0, 1);
            if ($rowHeightComment > 6) {
                $pdf->Ln($horizontalSpacerLN);
            } else {
                $pdf->Ln(1);
            }
        }

//   ---------- ALLGEMEIN   ----------
//
        block_label_queer($block_header_w, $pdf, "Allgemein", $horizontalSpacerLN3 + 6, $block_header_height, $SB);

        multicell_text_hightlight($pdf, $e_C_2_3rd, $font_size, 'Fussboden OENORM B5220', "Ö NORM B5220: ", $parameter_changes_t_räume);
        multicell_with_str($pdf, $row['Fussboden OENORM B5220'], $e_C_3rd, "");
        $heightExceeds = false;
        multicell_text_hightlight($pdf, $e_C_2_3rd, $font_size, "Allgemeine Hygieneklasse", "Hygieneklasse: ", $parameter_changes_t_räume);
        if ($row['Allgemeine Hygieneklasse'] != "") {

            $heightExceeds = $pdf->getStringHeight($e_C_3rd * 4, $row['Allgemeine Hygieneklasse'], false, true, '', 1) > 6 ? true : false;
            multicell_with_str($pdf, $row['Allgemeine Hygieneklasse'], $e_C_3rd * 4, "");

        } else {
            multicell_with_str($pdf, " - ", $e_C_3rd, "");
        }

        multicell_text_hightlight($pdf, $e_C_2_3rd, $font_size, 'Strahlenanwendung', "Strahlenanw.: ", $parameter_changes_t_räume);
        if (($pdf->getStringHeight($e_C_3rd, $row['Strahlenanwendung'])) > 6) {
            strahlenanw($pdf, $row['Strahlenanwendung'], 4 * $e_C_3rd, $font_size);
        } else {
            strahlenanw($pdf, $row['Strahlenanwendung'], $e_C_3rd, $font_size);
        }

        multicell_text_hightlight($pdf, $e_C_2_3rd, $font_size, "Laseranwendung", "Laseranw.: ", $parameter_changes_t_räume);
        hackerlA3($pdf, $font_size, $e_C_3rd, $row['Laseranwendung'], "JA");

        multicell_text_hightlight($pdf, $e_C_2_3rd, $font_size, "Abdunkelbarkeit", "Abdunkelbarkeit: ", $parameter_changes_t_räume);
        hackerlA3($pdf, $font_size, $e_C_3rd, $row['Abdunkelbarkeit'], "JA");

        multicell_text_hightlight($pdf, $e_C_2_3rd, $font_size, "Nutzfläche", "Fläche: ", $parameter_changes_t_räume);
        multicell_with_nr($pdf, $row['Nutzfläche'], "m2", 10, 4 * $e_C_3rd);
        $pdf->Ln($horizontalSpacerLN3);
        if ($heightExceeds) {
            $pdf->Ln($horizontalSpacerLN);
        }

//       ---------- ELEKTRO -----------

        $i = 12 + $horizontalSpacerLN + $horizontalSpacerLN2;

        $Block_height = 6 + $horizontalSpacerLN + getAnmHeight($pdf, $row['Anmerkung Elektro'], $SB) + $i;
        block_label_queer($block_header_w, $pdf, "Elektro", $Block_height, $block_header_height, $SB);

        multicell_text_hightlight($pdf, $e_C, $font_size, "Anwendungsgruppe", "ÖVE E8101:", $parameter_changes_t_räume);
        multicell_with_str($pdf, $row['Anwendungsgruppe'], $e_C_3rd+10, "");


        $electricalItems = [
            ['AV', 'AV: '],
            ['SV', 'SV: '],
            ['ZSV', 'ZSV: '],
            ['USV', 'USV: '],
            ['IT Anbindung', 'IT Anschl.: ']
        ];

        foreach ($electricalItems as $item) {
            multicell_text_hightlight($pdf, $e_C_2_3rd, $font_size, $item[0], $item[1], $parameter_changes_t_räume);
            hackerlA3($pdf, $font_size, $e_C_3rd, $row[$item[0]], "JA");
        }

        if ($isnotVorentwurf) {
            multicell_text_hightlight($pdf, $e_C_2_3rd, $font_size, 'EL_Roentgen 16A CEE Stk', "CEE16A Röntgen", $parameter_changes_t_räume);
            multicell_with_str($pdf, $row['EL_Roentgen 16A CEE Stk'], $e_C_3rd, "Stk");
        }
        $pdf->Ln($horizontalSpacerLN);
        $pdf->MultiCell($block_header_w, $block_header_height, "", 0, 'L', 0, 0);
        $outsr = "";


        $powerItems = [
                ['ET_Anschlussleistung_W', 'Raum Anschlussl. ohne Glz:', $e_C],
            ['ET_Anschlussleistung_AV_W', 'AV(Rauml.): ', $e_C_2_3rd],
            ['ET_Anschlussleistung_SV_W', 'SV(Rauml.): ', $e_C_2_3rd],
            ['ET_Anschlussleistung_ZSV_W', 'ZSV(Rauml.): ', $e_C_2_3rd],
            ['ET_Anschlussleistung_USV_W', 'USV(Rauml.): ', $e_C_2_3rd]
        ];

        foreach ($powerItems as $item) {
            multicell_text_hightlight($pdf, $item[2], $font_size, $item[0], $item[1], $parameter_changes_t_räume);

            $outsr = ($row[$item[0]] != "0") ? kify($row[$item[0]]) . "W" : "-";
            $space = $e_C_3rd;
            if($item[2]==$e_C){$space = $e_C_3rd +10; }
            multicell_with_str($pdf, $outsr, $space, "");
        }


        if ($isnotVorentwurf) {
            multicell_text_hightlight($pdf, $e_C_2_3rd, $font_size, 'ET_RJ45-Ports', "RJ45-Ports: ", $parameter_changes_t_räume);
            multicell_with_nr($pdf, $row['ET_RJ45-Ports'], "Stk", $pdf->getFontSizePt(), $e_C_3rd);

            multicell_text_hightlight($pdf, $e_C_2_3rd, $font_size, 'EL_Laser 16A CEE Stk', "CEE16A Laser: ", $parameter_changes_t_räume);
            multicell_with_str($pdf, $row['EL_Laser 16A CEE Stk'], $e_C_3rd, "Stk");
            $pdf->Ln($horizontalSpacerLN);
        } else {
            $pdf->MultiCell($e_C*2, $block_header_height, "", 0, 'L', 0, 0);
        }


        $pdf->MultiCell($block_header_w, $block_header_height, "", 0, 'L', 0, 0);
        if ($isnotVorentwurf) {
            $SQL = "SELECT tpep.Wert,
                       tpep.Einheit,
                       tpep.tabelle_Varianten_idtabelle_Varianten,
                       tpep.tabelle_elemente_idTABELLE_Elemente,
                       tp.Bezeichnung,
                       tpk.Kategorie,
                       tpk.idTABELLE_Parameter_Kategorie,
                       tp.idTABELLE_Parameter,
                       tre.Anzahl
                FROM tabelle_parameter_kategorie tpk
                         INNER JOIN
                     tabelle_parameter tp
                     ON tpk.idTABELLE_Parameter_Kategorie = tp.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie
                         INNER JOIN
                     tabelle_projekt_elementparameter tpep ON tp.idTABELLE_Parameter = tpep.tabelle_parameter_idTABELLE_Parameter
                         INNER JOIN
                     tabelle_räume_has_tabelle_elemente tre
                     ON tpep.tabelle_elemente_idTABELLE_Elemente = tre.tabelle_elemente_idTABELLE_Elemente
                WHERE tpep.tabelle_projekte_idTABELLE_Projekte =  " . $_SESSION['projectID'] . " 
                  AND tp.`Bauangaben relevant` = 1 
                  AND tre.tabelle_räume_idTABELLE_Räume = " . $valueOfRoomID . "
                  AND tpk.Kategorie = 'Elektro'
                  AND tpep.tabelle_Varianten_idtabelle_Varianten = tre.tabelle_Varianten_idtabelle_Varianten
                ORDER BY tp.idTABELLE_Parameter DESC;";

            $resultE = $mysqli->query($SQL);
            $netzart_options = ["AV", "SV", "ZSV", "USV"];
            $netzart_dict = [];
            $leistung_dict = array_fill_keys($netzart_options, 0);
            $gleichzeitigkeit = [];
            $errors = [];

            foreach ($resultE as $key => $rowE) {
                $wert = $rowE['Wert'];
                $einheit = $rowE['Einheit'];
                $variante_id = $rowE['tabelle_Varianten_idtabelle_Varianten'];
                $element_id = $rowE['tabelle_elemente_idTABELLE_Elemente'];
                $bezeichnung = $rowE['Bezeichnung'];
                $kategorie = $rowE['Kategorie'];
                $parameter_kategorie_id = $rowE['idTABELLE_Parameter_Kategorie'];
                $parameter_id = $rowE['idTABELLE_Parameter'];
                $anzahl = $rowE['Anzahl'];
                //echo "Parameter " . $bezeichnung . ";<br>";
                // Check for empty values in parameters 118 and 82
                if (($parameter_id == 118 || $parameter_id == 82) && $wert === "") {
                    $errors[] = "Error: Parameter ID $parameter_id is empty for Element ID $element_id";
                }
                // Process Netzart
                // echo "parameterID: " . $parameter_id . "<br>";
                if ($parameter_id == 82) {
                    if (!in_array($wert, $netzart_options)) {
                        $errors[] = "Error: Invalid Netzart for Element ID $element_id: $wert";
                    } else {
                        $netzart_dict[$element_id] = $wert;
                    }
                }
                // Store Gleichzeitigkeit and Leistung
                if ($parameter_id == 133) {
                    $gleichzeitigkeit[$element_id] = $wert === "" ? 1 : floatval(str_replace(',', '.', $wert));
                    // echo "Wert: " .  $wert . "<br>" ."Gleichzeitigkeit: ". $gleichzeitigkeit[$element_id]. "<br>";

                } elseif ($parameter_id == 18) {
                    $leistung = floatval($wert) * getUnitMultiplier($einheit) * $anzahl;
                    //echo $leistung . "<br> ";
                    if (isset($netzart_dict[$element_id]) && isset($gleichzeitigkeit[$element_id])) {
                        $netzart = $netzart_dict[$element_id];
                        $gleichzeitigkeit_wert = $gleichzeitigkeit[$element_id];
                        $leistung_dict[$netzart] += $gleichzeitigkeit_wert * $leistung;
                    }
                }
            }
            $total_sum = array_sum($leistung_dict);
            //echo "Processed Data:<br>";
            //echo "Netzart Dictionary: " . print_r($netzart_dict, true) . "<br>";
            //echo "Leistung Dictionary: " . print_r($leistung_dict, true) . "<br>";
            //echo "Gleichzeitigkeit: " . print_r($gleichzeitigkeit, true) . "<br>";
            //echo "Errors: " . print_r($errors, true) . "<br>";
            //echo "Total sum of all netzten leistungen: $total_sum <br>";
            //echo "<br>" . $leistung_dict['AV'];
            //echo "<br>" . $leistung_dict['SV'];


            $pdf->MultiCell($e_C, $block_header_height, "Elemente Leistung inkl. Glz:", 0, 'L', 0, 0);
            multicell_with_nr($pdf, kify($total_sum), "W", $font_size, $e_C_3rd+10);
            foreach ($netzart_options as $NA) {
                $pdf->MultiCell($e_C_2_3rd, $block_header_height, $NA . "(El.& Glz.):", 0, 'L', 0, 0);
                if ($leistung_dict[$NA] != 0) {
                    $outsr = kify($leistung_dict[$NA]) . "W";
                } else {
                    $outsr = "-";
                }
                multicell_with_str($pdf, $outsr, $e_C_3rd, "");
            }


            $pdf->MultiCell($e_C, $block_header_height, "", 0, 'L', 0, 0);
            multicell_text_hightlight($pdf, $e_C_2_3rd, $font_size, 'ET_16A_3Phasig_Einzelanschluss', "Einzelanschl. 16A: ", $parameter_changes_t_räume);
            multicell_with_str($pdf, $row['ET_16A_3Phasig_Einzelanschluss'], $e_C_3rd, "Stk");

            $pdf->Ln($horizontalSpacerLN);
            $pdf->MultiCell($block_header_w + $e_C + $e_C_3rd +10 , $block_header_height, "", 0, 'L', 0, 0);

            $electricalItems = [
                ['EL_AV Steckdosen Stk', 'AV SSD: '],
                ['EL_SV Steckdosen Stk', 'SV SSD: '],
                ['EL_ZSV Steckdosen Stk', 'ZSV SSD: '],
                ['EL_USV Steckdosen Stk', 'USV SSD: ']
            ];

            foreach ($electricalItems as $item) {
                multicell_text_hightlight($pdf, $e_C_2_3rd, $font_size, $item[0], $item[1], $parameter_changes_t_räume);

                if ($item[0] === 'EL_AV Steckdosen Stk') {
                    multicell_with_nr($pdf, $row[$item[0]], "Stk", $font_size, $e_C_3rd);
                } else {
                    multicell_with_str($pdf, $row[$item[0]], $e_C_3rd, "Stk");
                }
            }

        }

        $pdf->Ln($horizontalSpacerLN2);
        anmA3($pdf, $row['Anmerkung Elektro'], $SB, $block_header_w);
        $pdf->Ln($horizontalSpacerLN);

// 
//// ---------- HAUSTEK ---------
//
        $Block_height = 6 + $horizontalSpacerLN2 + getAnmHeight($pdf, $row['Anmerkung HKLS'], $SB);
        block_label_queer($block_header_w, $pdf, "Haustechnik", $Block_height, $block_header_height, $SB);

        $haustechnikItems = [
            ['H6020', 'H6020: ', ''],
            ['HT_Abluft_Digestorium_Stk', 'Abluft Digestorium:', 'Stk'],
            ['HT_Punktabsaugung_Stk', 'Punktabsaugung:', 'Stk'],
            ['HT_Abluft_Sicherheitsschrank_Stk', 'Abluft Sicherheitsschrank:', 'Stk'],
            ['HT_Abluft_Sicherheitsschrank_Unterbau_Stk', 'Abluft Sicherheitsschrank Unterbau:', 'Stk']
        ];

        foreach ($haustechnikItems as $item) {
            if ($item[0] === 'HT_Abluft_Sicherheitsschrank_Unterbau_Stk') {
                multicell_text_hightlight($pdf, $e_C + $e_C_3rd, $font_size, $item[0], $item[1], $parameter_changes_t_räume);
            } else if ($item[0] === 'H6020') {

                multicell_text_hightlight($pdf, $e_C_2_3rd, $font_size, $item[0], $item[1], $parameter_changes_t_räume);

            } else {
                multicell_text_hightlight($pdf, $e_C, $font_size, $item[0], $item[1], $parameter_changes_t_räume);
            }
            $value = ($item[0] === 'VE_Wasser') ? translate_1_to_yes($row[$item[0]]) : $row[$item[0]];

            multicell_with_str($pdf, $value, $e_C_3rd, $item[2]);
        }

        $pdf->Ln($horizontalSpacerLN2);
        $pdf->Multicell($block_header_w, 1, "", 0, 0, 0, 0);

        multicell_text_hightlight($pdf, $e_C_2_3rd, $font_size, "HT_Waermeabgabe_W", "Abwärme MT: ", $parameter_changes_t_räume);
        $abwrem_out = ($row['HT_Waermeabgabe_W'] === "0" || $row['HT_Waermeabgabe_W'] == 0 || $row['HT_Waermeabgabe_W'] == "-")
            ? "keine Angabe"
            : kify($row['HT_Waermeabgabe_W']) . "W";
        multicell_with_str($pdf, $abwrem_out, $e_C_3rd, "");

        $additionalItems = [
            ['VE_Wasser', 'Voll entsalztes Wasser:', ''],
            ['HT_Notdusche', 'Notdusche:', '']
        ];

        foreach ($additionalItems as $item) {
            multicell_text_hightlight($pdf, $e_C, $font_size, $item[0], $item[1], $parameter_changes_t_räume);
            multicell_with_str($pdf, $row[$item[0]], $e_C_3rd, $item[2]);
        }

        $pdf->Ln($horizontalSpacerLN2);
        anmA3($pdf, $row['Anmerkung HKLS'], $SB, $block_header_w);
        $pdf->Ln($horizontalSpacerLN);


/// ----------- MEDGAS -----------
//
        $Block_height = 12 + $horizontalSpacerLN + getAnmHeight($pdf, $row['Anmerkung MedGas'], $SB);
        block_label_queer($block_header_w, $pdf, "Med.-Gas", $Block_height, $block_header_height, $SB);

        $medGasItems = [
            '1 Kreis O2', '1 Kreis Va', '1 Kreis DL-5', 'NGA', 'N2O', 'CO2', 'DL-10',
            '2 Kreis O2', '2 Kreis Va', '2 Kreis DL-5', 'DL-tech'
        ];

        foreach ($medGasItems as $item) {
            $label = str_replace(['1 Kreis ', '2 Kreis ', '-'], ['1 Kreis   ', '2 Kreise ', ''], $item);
            multicell_text_hightlight($pdf, $e_C_2_3rd, $font_size, $item, "$label: ", $parameter_changes_t_räume);
            hackerlA3($pdf, $font_size, $e_C_3rd, $row[$item], 1);

            if ($item === 'DL-10') {
                $pdf->Ln($horizontalSpacerLN);
                $pdf->MultiCell($block_header_w, $block_header_height, "", 0, 'L', 0, 0);
            }
        }

        $pdf->Ln($horizontalSpacerLN2);
        anmA3($pdf, $row['Anmerkung MedGas'], $SB, $block_header_w);


////     ------- BauStatik ---------
        if ("" != $row['Anmerkung BauStatik'] && $row['Anmerkung BauStatik'] != "keine Angaben MT") {
            $pdf->Ln($horizontalSpacerLN);
            $Block_height = getAnmHeight($pdf, $row['Anmerkung BauStatik'], $SB);
            block_label_queer($block_header_w, $pdf, "Baustatik", $Block_height, $block_header_height, $SB);
            $pdf->Ln(1);
            anmA3($pdf, $row['Anmerkung BauStatik'], $SB, $block_header_w);
            $pdf->Ln($horizontalSpacerLN);
        }
//
////     ------- MT Tabelle  ---------
//
        // -------------------------Elemente im Raum laden--------------------------
        $sql = "SELECT tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_varianten.Variante, Sum(tabelle_räume_has_tabelle_elemente.Anzahl) AS SummevonAnzahl,
            tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten
            FROM tabelle_varianten INNER JOIN (tabelle_räume_has_tabelle_elemente INNER JOIN tabelle_elemente ON 
            tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente = tabelle_elemente.idTABELLE_Elemente) ON
            tabelle_varianten.idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten
            WHERE (((tabelle_räume_has_tabelle_elemente.Verwendung)=1))
            GROUP BY tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_varianten.Variante, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, 
            tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten, tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume
            HAVING (((tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume)=" . $valueOfRoomID . ") AND SummevonAnzahl > 0)
            ORDER BY tabelle_elemente.ElementID, tabelle_varianten.Variante;";
        $resultX = $mysqli->query($sql);
        $rowcounter = 0;
        while ($row2 = $resultX->fetch_assoc()) {
            $rowcounter++;
        }
        $resultX->data_seek(0);

        if ($isnotVorentwurf && $rowcounter > 0) {
            // -----------------Projekt Elementparameter/Variantenparameter laden----------------------------
            $sql = "SELECT tabelle_parameter_kategorie.Kategorie,tabelle_parameter.Abkuerzung, tabelle_parameter.Bezeichnung, tabelle_parameter.idTABELLE_Parameter, tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie, tabelle_parameter.Abkuerzung
                FROM tabelle_parameter_kategorie INNER JOIN (tabelle_parameter INNER JOIN tabelle_projekt_elementparameter ON tabelle_parameter.idTABELLE_Parameter = tabelle_projekt_elementparameter.tabelle_parameter_idTABELLE_Parameter) 
                ON tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie = tabelle_parameter.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie
                WHERE ((tabelle_projekt_elementparameter.tabelle_projekte_idTABELLE_Projekte=" . $_SESSION["projectID"] . ") AND (tabelle_parameter.`Bauangaben relevant` = 1) 
                AND NOT (tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie = 18 )) 
                GROUP BY tabelle_parameter_kategorie.Kategorie, tabelle_parameter.Bezeichnung
                ORDER BY tabelle_parameter_kategorie.Kategorie, tabelle_parameter.Bezeichnung;";
            $result1 = $mysqli->query($sql);

            // -------------------------Elemente parameter -------------------------
            $sql = "SELECT tabelle_projekt_elementparameter.Wert, tabelle_projekt_elementparameter.Einheit, tabelle_projekt_elementparameter.tabelle_Varianten_idtabelle_Varianten, 
                tabelle_projekt_elementparameter.tabelle_elemente_idTABELLE_Elemente, tabelle_parameter.Bezeichnung, tabelle_parameter_kategorie.Kategorie, tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie, tabelle_parameter.idTABELLE_Parameter 
                FROM tabelle_parameter_kategorie INNER JOIN (tabelle_parameter INNER JOIN tabelle_projekt_elementparameter ON tabelle_parameter.idTABELLE_Parameter = tabelle_projekt_elementparameter.tabelle_parameter_idTABELLE_Parameter) 
                ON tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie = tabelle_parameter.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie
                WHERE ((tabelle_projekt_elementparameter.tabelle_projekte_idTABELLE_Projekte=" . $_SESSION["projectID"] . ") AND (tabelle_parameter.`Bauangaben relevant` = 1)
                AND NOT (tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie = 18 ))
                ORDER BY tabelle_parameter_kategorie.Kategorie, tabelle_parameter.Bezeichnung;";
            $result3 = $mysqli->query($sql);

            //while ($row = $result3->fetch_assoc()) {
            //    echorow($row);
            //} echo "\n --------------------------------- \n ";

            $sql = "SELECT tabelle_projekt_elementparameter_aenderungen.idtabelle_projekt_elementparameter_aenderungen, tabelle_projekt_elementparameter_aenderungen.projekt, tabelle_projekt_elementparameter_aenderungen.element, tabelle_projekt_elementparameter_aenderungen.parameter, tabelle_projekt_elementparameter_aenderungen.variante, tabelle_projekt_elementparameter_aenderungen.wert_alt, tabelle_projekt_elementparameter_aenderungen.wert_neu, tabelle_projekt_elementparameter_aenderungen.einheit_alt, tabelle_projekt_elementparameter_aenderungen.einheit_neu, tabelle_projekt_elementparameter_aenderungen.timestamp, tabelle_projekt_elementparameter_aenderungen.user
                FROM tabelle_projekt_elementparameter_aenderungen
                WHERE (((tabelle_projekt_elementparameter_aenderungen.projekt)=" . $_SESSION["projectID"] . "))
                AND tabelle_projekt_elementparameter_aenderungen.timestamp > '$Änderungsdatum'
                ORDER BY tabelle_projekt_elementparameter_aenderungen.timestamp DESC;";
            $changes = $mysqli->query($sql);
            $dataChanges = array();
            while ($row3 = $changes->fetch_assoc()) {
                $dataChanges[] = $row3;
            }
            $dataChanges = filter_old_equal_new($dataChanges);

            $upcmn_blck_size = 10 + $rowcounter * 5;
            block_label_queer($block_header_w, $pdf, "Med.-tech.", $upcmn_blck_size, $block_header_height, $SB);
            make_MT_details_table($pdf, $resultX, $result1, $result3, $SB, $SH, $dataChanges);
        } else if ($rowcounter > 0) {
            $upcmn_blck_size = 10 + $rowcounter / 2 * 5;
            block_label_queer($block_header_w, $pdf, "Med.-tech.", $upcmn_blck_size, $block_header_height, $SB); //el_in_room_html_table($pdf, $resultX, 1, "A3", $SB-$block_header_w);
            $pdf->Line(15 + $block_header_w, $pdf->GetY(), $SB + 15, $pdf->GetY(), $style_dashed);
            make_MT_list($pdf, $SB, $block_header_w, $rowcounter, $resultX, $style_normal, $style_dashed);
        } else {
            $pdf->Line(15, $pdf->GetY(), $SB + 15, $pdf->GetY(), $style_normal);
            block_label_queer($block_header_w, $pdf, "Med.-tech.", $upcmn_blck_size, $block_header_height, $SB); //el_in_room_html_table($pdf, $resultX, 1, "A3", $SB-$block_header_w);
            $pdf->Multicell(0, 0, "Keine Medizintechnische Ausstattung.", "", "L", 0, 0);
            $pdf->Ln();
        }
    } //sql:fetch-assoc
}// for every room 

$mysqli->close();
ob_end_clean();
$pdf->Output('BAUANGABEN-MT.pdf', 'I');


