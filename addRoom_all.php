<?php
require_once "utils/_utils.php";
check_login();
$mysqli = utils_connect_sql();
$params = $_POST;

$allowedColumns = [
    'Raumnr', 'Raumbezeichnung', 'TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen',
    'TABELLE_Mindestraumgrößen_idTABELLE_Mindestraumgrößen', 'tabelle_projekte_idTABELLE_Projekte',
    'tabelle_pläne_idTABELLE_Pläne', 'Funktionelle Raum Nr', 'Raumnummer_Nutzer',
    'Raumbereich Nutzer', 'Geschoss', 'Bauetappe', 'Bauabschnitt', 'Anmerkung allgemein',
    'Nutzfläche', 'Nutzfläche_Soll', 'Fussboden', 'Fussboden OENORM B5220', 'Decke',
    'Abdunkelbarkeit', 'Strahlenanwendung', 'Laseranwendung', 'H6020', 'GMP', 'ISO',
    '1 Kreis O2', '2 Kreis O2', 'O2', '1 Kreis Va', '2 Kreis Va', 'VA',
    '1 Kreis DL-5', '2 Kreis DL-5', 'DL-5', 'DL-10', 'DL-tech', 'CO2',
    'H2', 'He', 'He-RF', 'Ar', 'N2', 'Kr', 'Ne', 'NH3', 'C2H2',
    'Propan_Butan', 'N2H2', 'Inertgas', 'Ar_CO2_Mix', 'ArCal15', 'NGA', 'N2O',
    'AV', 'SV', 'ZSV', 'USV', 'IT Anbindung', 'Anwendungsgruppe',
    'Anmerkung MedGas', 'Anmerkung Elektro', 'Anmerkung HKLS', 'Anmerkung Geräte',
    'Anmerkung FunktionBO', 'Taetigkeiten', 'Mindestgrösse', 'Timestamp',
    'Anmerkung BauStatik', 'Anmerkung Rohrpost', 'Anmerkung Kuechentechnik', 'Anmerkung AR',
    'Allgemeine Hygieneklasse', 'Raumhoehe', 'Raumhoehe_Soll', 'MT-relevant',
    'tabelle_bauphasen_idtabelle_bauphasen', 'Raumhoehe 2', 'Belichtungsfläche',
    'Umfang', 'Volumen', 'Aufenthaltsraum', 'EL_Beleuchtungsstaerke',
    'EL_Beleuchtung 1 Typ', 'EL_Beleuchtung 2 Typ', 'EL_Beleuchtung 3 Typ',
    'EL_Beleuchtung 4 Typ', 'EL_Beleuchtung 5 Typ', 'EL_Beleuchtung 1 Stk',
    'EL_Beleuchtung 2 Stk', 'EL_Beleuchtung 3 Stk', 'EL_Beleuchtung 4 Stk',
    'EL_Beleuchtung 5 Stk', 'EL_Lichtschaltung BWM JA/NEIN', 'EL_Beleuchtung dimmbar JA/NEIN',
    'EL_Brandmelder Decke JA/NEIN', 'EL_Brandmelder ZwDecke JA/NEIN',
    'EL_AV Steckdosen Stk', 'EL_SV Steckdosen Stk', 'EL_USV Steckdosen Stk',
    'EL_ZSV Steckdosen Stk', 'EL_Roentgen 16A CEE Stk', 'EL_Laser 16A CEE Stk',
    'EL_Laser 32A Stk', 'EL_Jalousie JA/NEIN', 'EL_Doppeldatendose Stk',
    'EL_Einzel-Datendose Stk', 'EL_Bodendose Typ', 'EL_Bodendose Stk',
    'EL_Kamera Stk', 'EL_Lautsprecher Stk', 'EL_Uhr - Wand Stk', 'EL_Uhr - Decke Stk',
    'EL_Notlicht RZL Stk', 'EL_Notlicht SL Stk', 'EL_Lichtruf - Terminal Stk',
    'EL_Lichtruf - Steckmodul Stk', 'EL_Lichtfarbe K', 'EL_Leistungsbedarf_W_pro_m2',
    'ET_Anschlussleistung_W', 'ET_Anschlussleistung_AV_W', 'ET_Anschlussleistung_SV_W',
    'ET_Anschlussleistung_USV_W', 'ET_Anschlussleistung_ZSV_W', 'EL_Not_Aus',
    'EL_Not_Aus_Funktion', 'EL_Signaleinrichtung', 'HT_Summe Kühlung W',
    'HT_Luftmenge m3/h', 'HT_Luftmenge Abluft m3/h', 'HT_Luftwechsel 1/h',
    'HT_Kühlung Lueftung W', 'HT_Heizlast W', 'HT_Kühllast W', 'HT_Fussbodenkühlung W',
    'HT_Kühldecke W', 'HT_Fancoil W', 'HT_Raumtemp Sommer °C', 'HT_Raumtemp Winter °C',
    'HT_Tempgradient_Ch', 'HT_Notdusche', 'HT_Waermeabgabe', 'HT_Waermeabgabe_W',
    'HT_Geraeteabluft m3/h', 'HT_Kühlwasserleistung_W', 'HT_Kaltwasser', 'HT_Warmwasser',
    'AR_Ausstattung', 'AR_APs', 'AR_AP_permanent', 'AR_AnwesendePersonen',
    'Raumtyp BH', 'AP_Gefaehrdung', 'AP_Geistige', 'AR_Belichtung-nat',
    'AR_Schwingungsklasse', 'ET_EMV', 'ET_EMV_ja-nein', 'AR_Akustik',
    'AR_Statik_relevant', 'AR_Flaechenlast_kgcm2', 'AR_Empf_Breite_cm',
    'AR_Empf_Tiefe_cm', 'AR_Empf_Hoehe_cm', 'AR_Empf_Tuerbreite_cm',
    'RaumNr_Bestand', 'Gebaeude_Bestand', 'Laserklasse', 'O2 Reinheit',
    'O2 l/min', 'CO2 Reinheit', 'CO2 l/min', 'VA l/min', 'DL l/min',
    'DL ISO 8573', 'H2 l/min', 'H2 Reinheit', 'He l/min', 'He Reinheit',
    'Ar l/min', 'Ar Reinheit', 'N2 l/min', 'N2 Reinheit', 'LN', 'LN l/Tag',
    'LHe', 'VE_Wasser', 'Wasser Qual 1', 'Wasser Qual 2', 'Wasser Qual 3',
    'Wasser Qual 1 l/Tag', 'Wasser Qual 2 l/Tag', 'Wasser Qual 3 l/min',
    'Abluft Anzahl', 'ET_5x10mm2_AV_Stk', 'ET_5x10mm2_SV_Stk', 'ET_5x10mm2_USV_Stk',
    'ET_5x10mm2_Digestorium_Stk', 'ET_Digestorium_MSR_230V_SV_Stk',
    'ET_16A_3Phasig_Einzelanschluss', 'ET_32A_3Phasig_Einzelanschluss',
    'ET_64A_3Phasig_Einzelanschluss', 'ET_RJ45-Ports', 'ET_PA_Stk',
    'CO2_Melder', 'O2_Mangel', 'NH3_Sensor', 'H2_Sensor', 'O2_Sensor',
    'Blitzleuchte', 'Acetylen_Melder', 'Spezialgase', 'Gaswarneinrichtung-Art',
    'HT_Kühlwasser', 'HT_Spuele_Stk', 'HT_Abwasser_Stk', 'HT_Abluft_Sicherheitsschrank_Stk',
    'HT_Abluft_Sicherheitsschrank_Unterbau_Stk', 'HT_Punktabsaugung_Stk',
    'HT_Abluft_Digestorium_Stk', 'HT_Abluft_Rauchgasabzug_Stk', 'HT_Abluft_Esse_Stk',
    'HT_Abluft_Schweissabsaugung_Stk', 'HT_Abluft_Vakuumpumpe', 'HT_Abluft_Geraete',
    'HT_Belueftung', 'HT_Entlueftung', 'HT_Kuehlung', 'HT_Kaelteabgabe_Typ',
    'HT_Heizung', 'HT_Waermeabgabe_Typ', 'PHY_Akustik_Typ', 'PHY_Akustik_T500',
    'PHY_Akustik_Schallgrad', 'PHY_LRV', 'VEXAT_Zone', 'Entfallen',
    'AR_Nutzung_ON1800', 'AR_Bodenaufbau', 'AR_Boden_Rutschfestigkeit'
];

// 2. Filter and validate incoming data
$columns = [];
$values = [];

foreach ($params as $key => $value) {
    // Handle special column name mapping
    if ($key === "fk_TABELLE_Räume_TABELLE_Funktionsteilstellen1") {
        $key = "TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen";
    }

    // Skip these fields
    if (in_array($key, ["Bezeichnung", "Nummer", "element_mask"])) {
        continue;
    }

    // SECURITY: Only allow whitelisted columns
    if (!in_array($key, $allowedColumns)) {
        error_log("Rejected invalid column: $key");
        continue;
    }

    $columns[] = $key;
    $values[] = $value;
}

// 3. Check if we have any columns to insert
if (empty($columns)) {
    die("No valid data to insert");
}

// 4. Build the prepared statement
$columnNames = implode('`, `', $columns);
$placeholders = implode(', ', array_fill(0, count($columns), '?'));

$sql = "INSERT INTO `LIMET_RB`.`tabelle_räume` (`$columnNames`) VALUES ($placeholders)";

// 5. Prepare the statement
$stmt = $mysqli->prepare($sql);

if (!$stmt) {
    error_log("Prepare failed: " . $mysqli->error);
    die("Database error occurred");
}

// 6. Bind parameters dynamically with correct types
$types = '';
$typedValues = [];

// Integer columns
$intColumns = [
    'TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen',
    'TABELLE_Mindestraumgrößen_idTABELLE_Mindestraumgrößen',
    'tabelle_projekte_idTABELLE_Projekte',
    'tabelle_bauphasen_idtabelle_bauphasen',
    'Abdunkelbarkeit', 'Strahlenanwendung', 'Laseranwendung',
    '1 Kreis O2', '2 Kreis O2', 'O2', '1 Kreis Va', '2 Kreis Va', 'VA',
    '1 Kreis DL-5', '2 Kreis DL-5', 'DL-5', 'DL-10', 'DL-tech', 'CO2',
    'H2', 'He', 'He-RF', 'Ar', 'N2', 'Kr', 'Ne', 'NH3', 'C2H2',
    'Propan_Butan', 'N2H2', 'Inertgas', 'Ar_CO2_Mix', 'ArCal15', 'NGA', 'N2O',
    'AV', 'SV', 'ZSV', 'USV', 'IT Anbindung', 'MT-relevant', 'Aufenthaltsraum',
    'EL_AV Steckdosen Stk', 'EL_SV Steckdosen Stk', 'EL_USV Steckdosen Stk',
    'EL_ZSV Steckdosen Stk', 'EL_Roentgen 16A CEE Stk', 'EL_Laser 16A CEE Stk',
    'EL_Laser 32A Stk', 'EL_Leistungsbedarf_W_pro_m2',
    'ET_Anschlussleistung_W', 'ET_Anschlussleistung_AV_W', 'ET_Anschlussleistung_SV_W',
    'ET_Anschlussleistung_USV_W', 'ET_Anschlussleistung_ZSV_W',
    'EL_Not_Aus', 'EL_Signaleinrichtung',
    'HT_Luftmenge Abluft m3/h', 'HT_Tempgradient_Ch', 'HT_Notdusche',
    'HT_Waermeabgabe', 'HT_Waermeabgabe_W', 'HT_Geraeteabluft m3/h',
    'HT_Kühlwasserleistung_W', 'HT_Kaltwasser', 'HT_Warmwasser',
    'AR_APs', 'AR_AP_permanent', 'AR_AnwesendePersonen', 'AP_Gefaehrdung',
    'AP_Geistige', 'AR_Belichtung-nat', 'ET_EMV_ja-nein', 'AR_Statik_relevant',
    'AR_Empf_Breite_cm', 'AR_Empf_Tiefe_cm', 'AR_Empf_Hoehe_cm',
    'AR_Empf_Tuerbreite_cm', 'O2 l/min', 'CO2 l/min', 'VA l/min', 'DL l/min',
    'H2 l/min', 'He l/min', 'Ar l/min', 'N2 l/min', 'LN', 'LN l/Tag', 'LHe',
    'VE_Wasser', 'Wasser Qual 1', 'Wasser Qual 2', 'Wasser Qual 3',
    'Wasser Qual 1 l/Tag', 'Wasser Qual 2 l/Tag', 'Wasser Qual 3 l/min',
    'Abluft Anzahl', 'ET_5x10mm2_AV_Stk', 'ET_5x10mm2_SV_Stk', 'ET_5x10mm2_USV_Stk',
    'ET_5x10mm2_Digestorium_Stk', 'ET_Digestorium_MSR_230V_SV_Stk',
    'ET_16A_3Phasig_Einzelanschluss', 'ET_32A_3Phasig_Einzelanschluss',
    'ET_64A_3Phasig_Einzelanschluss', 'ET_RJ45-Ports', 'ET_PA_Stk',
    'CO2_Melder', 'O2_Mangel', 'NH3_Sensor', 'H2_Sensor', 'O2_Sensor',
    'Blitzleuchte', 'Acetylen_Melder', 'HT_Kühlwasser', 'HT_Spuele_Stk',
    'HT_Abwasser_Stk', 'HT_Abluft_Sicherheitsschrank_Stk',
    'HT_Abluft_Sicherheitsschrank_Unterbau_Stk', 'HT_Punktabsaugung_Stk',
    'HT_Abluft_Digestorium_Stk', 'HT_Abluft_Rauchgasabzug_Stk',
    'HT_Abluft_Esse_Stk', 'HT_Abluft_Schweissabsaugung_Stk',
    'HT_Abluft_Vakuumpumpe', 'HT_Abluft_Geraete', 'HT_Kuehlung', 'HT_Heizung',
    'Entfallen'
];

$floatColumns = [
    'Nutzfläche', 'Nutzfläche_Soll', 'Belichtungsfläche', 'Umfang', 'Volumen',
    'EL_Beleuchtungsstaerke', 'AR_Flaechenlast_kgcm2', 'PHY_Akustik_T500',
    'PHY_LRV'
];

foreach ($columns as $i => $col) {
    if (in_array($col, $intColumns)) {
        $types .= 'i';
        $typedValues[] = $values[$i] === '' || $values[$i] === null ? 0 : (int)$values[$i];
    } elseif (in_array($col, $floatColumns)) {
        $types .= 'd';
        $typedValues[] = $values[$i] === '' || $values[$i] === null ? 0.0 : (float)$values[$i];
    } else {
        $types .= 's';
        $typedValues[] = $values[$i];
    }
}

$stmt->bind_param($types, ...$typedValues);

// 7. Execute the statement
if ($stmt->execute()) {
    echo "Raum erfolgreich hinzugefügt!";
} else {
    error_log("Execute failed: " . $stmt->error);
    echo "Fehler beim Hinzufügen des Raums.";
}

// 8. Clean up
$stmt->close();
$mysqli->close();
?>