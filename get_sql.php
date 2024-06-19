<?php

session_start();

$mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli->connect_error;
} else {
    //echo "Connected successfully";
}

if (!$mysqli->set_charset("utf8")) {
    printf("Error loading character set utf8: %s\n", $mysqli->error);
    exit();
}

$sql = "SELECT `idtabelle_raeume_aenderungen`, `raum_id`, `raum_nr_alt`, `raum_nr_neu`, `raumbezeichnung_alt`, `raumbezeichnung_neu`, `funktionelle_raum_nr_alt`, `funktionelle_raum_nr_neu`, `funktionsteilstelle_alt`, `funktionsteilstelle_neu`, `raumbereich_nutzer_alt`, `raumbereich_nutzer_neu`, `anmerkung_allg_alt`, `anmerkung_allg_neu`, `nutzflaeche_alt`, `nutzflaeche_neu`, `abdunkel_alt`, `abdunkel_neu`, `strahlen_alt`, `strahlen_neu`, `laser_alt`, `laser_neu`, `h620_alt`, `h620_neu`, `gmp_alt`, `gmp_neu`, `iso_alt`, `iso_neu`, `1 Kreis O2`, `1 Kreis O2_copy1`, `2 Kreis O2`, `2 Kreis O2_copy1`, `1 Kreis Va`, `1 Kreis Va_copy1`, `2 Kreis Va`, `2 Kreis Va_copy1`, `1 Kreis DL-5`, `1 Kreis DL-5_copy1`, `2 Kreis DL-5`, `2 Kreis DL-5_copy1`, `DL-10`, `DL-10_copy1`, `DL-tech`, `DL-tech_copy1`, `CO2`, `CO2_copy1`, `NGA`, `NGA_copy1`, `N2O`, `N2O_copy1`, `AV`, `AV_copy1`, `SV`, `SV_copy1`, `ZSV`, `ZSV_copy1`, `USV`, `USV_copy1`, `Anwendungsgruppe`, `Anwendungsgruppe_copy1`, `Anmerkung MedGas`, `Anmerkung MedGas_copy1`, `Anmerkung Elektro`, `Anmerkung Elektro_copy1`, `Anmerkung HKLS`, `Anmerkung HKLS_copy1`, `Anmerkung Geräte`, `Anmerkung Geräte_copy1`, `Anmerkung FunktionBO`, `Anmerkung FunktionBO_copy1`, `Anmerkung BauStatik`, `Anmerkung BauStatik_copy1`, `Timestamp`, `user`, `Raumhoehe_alt`, `Raumhoehe_neu`, `Allgemeine Hygieneklasse_alt`, `Allgemeine Hygieneklasse_neu`, `IT-Anbindung_alt`, `IT-Anbindung_neu`, `FB OENORM B5220_alt`, `FB OENORM B5220_neu`, `bauphase alt`, `bauphase neu`, `Raumhoehe 2 alt`, `Raumhoehe 2 neu`, `Belichtungsfläche alt`, `Belichtungsfläche neu`, `Umfang alt`, `Umfang neu`, `Volumen alt`, `Volumen neu`, `Aufenthaltsraum alt`, `Aufenthaltsraum neu`, `EL_Beleuchtung 1 Typ alt`, `EL_Beleuchtung 1 Typ neu`, `EL_Beleuchtung 2 Typ alt`, `EL_Beleuchtung 2 Typ neu`, `EL_Beleuchtung 3 Typ alt`, `EL_Beleuchtung 3 Typ neu`, `EL_Beleuchtung 4 Typ alt`, `EL_Beleuchtung 4 Typ neu`, `EL_Beleuchtung 5 Typ alt`, `EL_Beleuchtung 5 Typ neu`, `EL_Beleuchtung 1 Stk alt`, `EL_Beleuchtung 1 Stk neu`, `EL_Beleuchtung 2 Stk alt`, `EL_Beleuchtung 2 Stk neu`, `EL_Beleuchtung 3 Stk alt`, `EL_Beleuchtung 3 Stk neu`, `EL_Beleuchtung 4 Stk alt`, `EL_Beleuchtung 4 Stk neu`, `EL_Beleuchtung 5 Stk alt`, `EL_Beleuchtung 5 Stk neu`, `EL_Lichtschaltung BWM alt`, `EL_Lichtschaltung BWM neu`, `EL_Beleuchtung dimmbar alt`, `EL_Beleuchtung dimmbar neu`, `EL_Brandmelder Decke alt`, `EL_Brandmelder Decke neu`, `EL_Brandmelder ZwDecke alt`, `EL_Brandmelder ZwDecke neu`, `EL_AV Steckdosen Stk alt`, `EL_AV Steckdosen Stk neu`, `EL_SV Steckdosen Stk alt`, `EL_SV Steckdosen Stk neu`, `EL_USV Steckdosen Stk alt`, `EL_USV Steckdosen Stk neu`, `EL_ZSV Steckdosen Stk alt`, `EL_ZSV Steckdosen Stk neu`, `EL_Roentgen 16A Stk alt`, `EL_Roentgen 16A Stk neu`, `EL_Laser 16A Stk alt`, `EL_Laser 16A Stk neu`, `ET_RJ45-Ports alt`, `ET_RJ45-Ports neu`, `EL_Jalousien alt`, `EL_Jalousien neu`, `EL_Doppeldatendose Stk alt`, `EL_Doppeldatendose Stk neu`, `EL_Einzel-Datendose Stk alt`, `EL_Einzel-Datendose Stk neu`, `EL_Bodendose Typ alt`, `EL_Bodendose Typ neu`, `EL_Bodendose Stk alt`, `EL_Bodendose Stk neu`, `EL_Kamera Stk alt`, `EL_Kamera Stk neu`, `EL_Lautsprecher Stk alt`, `EL_Lautpsrecher Stk neu`, `EL_Uhr - Wand Stk alt`, `EL_Uhr - Wand Stk neu`, `EL_Uhr - Decke Stk alt`, `EL_Uhr - Decke Stk neu`, `EL_Notlicht RZL Stk alt`, `EL_Notlicht RZL Stk neu`, `EL_Notlicht SL Stk alt`, `EL_Notlicht SL Stk neu`, `EL_Lichtruf-Terminal Stk alt`, `EL_Lichtruf-Terminal Stk neu`, `EL_Lichtruf-Steckmodul Stk alt`, `EL_Lichtruf-Steckmodul Stk neu`, `EL_Lichtfarbe K alt`, `EL_Lichtfarbe K neu`, `EL_Leistungsbedarf W/m2 alt`, `EL_Leistungsbedarf W/m2 neu`, `ET_Anschlussleistung_W_alt`, `ET_Anschlussleistung_W_neu`, `ET_Anschlussleistung_AV_W_alt`, `ET_Anschlussleistung_AV_W_neu`, `ET_Anschlussleistung_SV_W_alt`, `ET_Anschlussleistung_SV_W_neu`, `ET_Anschlussleistung_ZSV_W_alt`, `ET_Anschlussleistung_ZSV_W_neu`, `ET_Anschlussleistung_USV_W_alt`, `ET_Anschlussleistung_USV_W_neu`, `HT_Summe Kühlung W alt`, `HT_Summe Kühlung W neu`, `HT_Luftmenge m3/h alt`, `HT_Luftmenge m3/h neu`, `HT_Luftwechsel 1/h alt`, `HT_Luftwechsel 1/h neu`, `HT_Kühlung Lüftung W alt`, `HT_Kühlung Lüftung W neu`, `HT_Heizlast W alt`, `HT_Heizlast W neu`, `HT_Kühllast W alt`, `HT_Kühllast W neu`, `HT_Fussbodenkühlung W alt`, `HT_Fussbodenkühlung W neu`, `HT_Kühldecke W alt`, `HT_Kühldecke W neu`, `HT_Fancoil W alt`, `HT_Fancoil W neu`, `HT_Raumtemp Sommer °C alt`, `HT_Raumtemp Sommer °C neu`, `HT_Raumtemp Winter °C alt`, `HT_Raumtemp Winter °C neu`, `HT_Notdusche alt`, `HT_Notdusche neu`, `HT_Waermeabgabe alt`, `HT_Waermeabgabe neu`, `HT_Geraeteabluft m3/h alt`, `HT_Geraeteabluft m3/h neu`, `HT_Kühlwasserleistung W alt`, `HT_Kühlwasserleistung W neu`, `AR_Ausstattung alt`, `AR_Ausstattung neu`, `AR_APs alt`, `AR_APs neu`, `Raumtyp BH alt`, `Raumtyp BH neu`, `AR_Belichtung-nat alt`, `AR_Belichtung-nat neu`, `AR_Schwingungsklasse alt`, `AR_Schwingungsklasse neu`, `ET_EMV alt`, `ET_EMV neu`, `ET_EMV_ja-nein alt`, `ET_EMV_ja-nein neu`, `AR_Akustik alt`, `AR_Akustik neu`, `NF_Soll alt`, `NF_Soll neu`, `O2 alt`, `O2 neu`, `VA alt`, `VA neu`, `DL-5 alt`, `DL-5 neu`, `H2 alt`, `H2 neu`, `He alt`, `He neu`, `He-RF alt`, `He-RF neu`, `Ar alt`, `Ar neu`, `N2 alt`, `N2 neu`, `Raumhoehe_Soll alt`, `Raumhoehe_Soll neu`, `AR_AnwesendePers alt`, `AR_AnwesendePers neu`, `RaumnrBestand alt`, `RaumnrBestand neu`, `GebaeudeBestand alt`, `GebaeudeBestand neu` 
FROM `tabelle_raeume_aenderungen` 
WHERE `raum_id`=" .$_SESSION["roomID"];

if (!$mysqli->query($sql)) {
    echo "Error executing query: " . $mysqli->error;
} else {
    $result = $mysqli->query($sql); 
} 

$mysqli->close();

$data = array();
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

header('Content-Type: application/json');
echo json_encode($data);

