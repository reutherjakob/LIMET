<?php

session_start();
if (!function_exists('utils_connect_sql')) {  include "_utils.php"; }
check_login();

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
    "HT_Notdusche neu" => "",
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
    "ET_Anschlussleistung_ZSV_W_neu" => "ET_Anschlussleistung_USV_W",
    "ET_Anschlussleistung_USV_W_neu" => "ET_Anschlussleistung_ZSV_W",
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
    "HT_Luftwechsel 1/h neu" => "HT_Luftwechsel 1/h",
    "HT_Kühlung Lüftung W neu" => "HT_Kühlung Lueftung W",
    "HT_Heizlast W neu" => "HT_Heizlast W",
    "HT_Kühllast W neu" => "HT_Kühllast W",
    "HT_Fussbodenkühlung W neu" => "HT_Fussbodenkühlung W",
    "HT_Kühldecke W neu" => "HT_Kühldecke W",
    "HT_Fancoil W neu" => "HT_Fancoil",
    "HT_Raumtemp Sommer °C neu" => "HT_Raumtemp Sommer °C",
    "HT_Raumtemp Winter °C neu" => "HT_Raumtemp Winter °C",
    "HT_Waermeabgabe_W_neu" => "HT_Waermeabgabe_W",
    "O2 neu" => "O2",
    "VA neu" => "Va",
    "DL-5 neu" => "DL-5",
    "H2 neu" => "H2",
    "He neu" => "He",
    "He-RF neu" => "He-RF",
    "Ar neu" => "Ar",
    "N2 neu" => "N2");

//// FETCH  DATA 
$selectedDate = '2024-01-01'; //load data only up to selcetd Date
$rID = $_SESSION["roomID"];
if (isset($_GET['date']) && !empty($_GET['date'])) {
    $selectedDate = $_GET['date'];
}
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $rID = $_GET['id'];
}

$mysqli = utils_connect_sql();
$stmt = $mysqli->prepare("SELECT * FROM `tabelle_raeume_aenderungen` WHERE `raum_id`= ?  AND `Timestamp` > ?"); // (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . "))
$stmt->bind_param("is", $rID, $selectedDate);
$stmt->execute();
$result = $stmt->get_result();

$changeSqlResult = array();
while ($row = $result->fetch_assoc()) {
    $changeSqlResult[] = $row;
}
echorow($changeSqlResult); 

$changedData = array();
$parameters_t_räume = array();

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
            $changedData[$newK] = end($entries);
            $parameters_t_räume[] = $mp2[$newK];
        }
    }
}
$mysqli->close();
header('Content-Type: application/json');
echo json_encode($parameters_t_räume);


/*$Änderungsdatum = "2024-06-01"  ;// "23-06-2024";
//$sql = "SELECT tabelle_projekt_elementparameter_aenderungen.idtabelle_projekt_elementparameter_aenderungen, tabelle_projekt_elementparameter_aenderungen.projekt, tabelle_projekt_elementparameter_aenderungen.element, tabelle_projekt_elementparameter_aenderungen.parameter, tabelle_projekt_elementparameter_aenderungen.variante, tabelle_projekt_elementparameter_aenderungen.wert_alt, tabelle_projekt_elementparameter_aenderungen.wert_neu, tabelle_projekt_elementparameter_aenderungen.einheit_alt, tabelle_projekt_elementparameter_aenderungen.einheit_neu, tabelle_projekt_elementparameter_aenderungen.timestamp, tabelle_projekt_elementparameter_aenderungen.user
//            FROM tabelle_projekt_elementparameter_aenderungen
//            WHERE (((tabelle_projekt_elementparameter_aenderungen.projekt)=" . $_SESSION["projectID"] . "))
//            AND tabelle_projekt_elementparameter_aenderungen.timestamp > '$Änderungsdatum'
//            ORDER BY tabelle_projekt_elementparameter_aenderungen.timestamp DESC;";
//$changes = $mysqli->query($sql);
//$dataChanges = array();
//while ($row = $changes->fetch_assoc()) {
//    $dataChanges[] = $row;
//}
//include '_pdf_createBericht_utils.php';
//$dataChanges = filter_old_equal_new($dataChanges); */

/*  $queryParts = array();
//  //foreach ($mapping as $old => $new) {
//  //    $queryParts[] = "`$old` <> `$new`";
//  //}
//  //$whereClause = implode(' OR ', $queryParts);
//  // $query= "SELECT * FROM `tabelle_raeume_aenderungen` WHERE `raum_id`=".$_SESSION["roomID"]." AND ". $whereClause ;
//  ////$stmt = $mysqli->prepare($query);
//  ////$stmt->bind_param("i", $_SESSION["roomID"]);
//  ////$stmt->execute();
//  ////$result = $stmt->get_result();
//  // $result = $mysqli ->query($query);
//  //while ($row = $result->fetch_assoc()) {
//  //    $changeSqlResult[] = $row;
//  //}
//  //echorow($changeSqlResult); */

/* $changedData = array();
  //foreach ($mapping as $oldK => $newK) {
  //    foreach ($changeSqlResult as $changeKey => $entry) {
  //        if ($entry[$oldK] !== $entry[$newK]) {
  //            // echo "ARRAY:". $changeKey.":" .$newK." ". $entry['Timestamp'] ."<br>";// " ---> " . $entry[$oldK] . " ||| " . $entry[$newK] . "<br>";
  //            $changedData[$newK][] = array(
  ////                'changeKey' => $changeKey,
  //                'timestamp' => $entry['Timestamp'],
  //                'oldValue' => $entry[$oldK],
  //                $mp2[$newK] => $entry[$newK]
  //            );
  //        }
  //    }
  //}
  //
  ////echo "Initial changed data:<br>";
  ////print_r($changedData);
  //
  //$parameters_t_räume= array();
  //
  //foreach ($changedData as $newK => $entries) {
  //    usort($entries, function ($a, $b) {
  //        return $a['timestamp'] <=> $b['timestamp'];
  //    });
  ////    echo "<br>Sorted entries for $newK:<br>";
  ////    print_r($entries);
  ////    echo "<br>";
  //    // If the earliest old and latest new value are the same, drop all entries
  //    if (end($entries)[$mp2[$newK]] === reset($entries)['oldValue']) {
  //        unset($changedData[$newK]);
  ////        echo "<br>Dropped all entries for $newK because the earliest old and latest new value are the same.<br>";
  //    } else {
  //        // Keep only the latest entry
  //        $changedData[$newK] = end($entries);
  ////        echo "<br>Kept only the latest entry for $newK:<br>";
  ////        print_r($changedData[$newK]);echo "<br>";
  //        $parameters_t_räume[] = $mp2[$newK];
  //    }
  //}


  //echo "<br> Final changed data:<br>";
  //echorow($changedData);

  //echo "<br> Just the changed SQL keys:<br>";
  //echorow($parameters_t_räume); */