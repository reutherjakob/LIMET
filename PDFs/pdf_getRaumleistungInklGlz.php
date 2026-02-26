<?php
// ✅ pdf_getRaumleistungInklGlz.php - KORRIGIERT MIT NEUER GLZ-Logik + AKKU-MAPPING
global $block_header_height, $block_header_w, $SB, $pdf, $mysqli, $valueOfRoomID, $row, $parameter_changes_t_räume,
       $e_C, $font_size, $e_C_2_3rd, $e_C_3rd, $horizontalSpacerLN2;

$raumID = $valueOfRoomID;
$projectID = $_SESSION["projectID"];

// ✅ GLZ-FIX: CAST(REPLACE(pep_gleich.Wert, ',', '.') AS DECIMAL(5,2)) + AKKU-MAPPING
$sql = "SELECT 
    mapped_netzart AS netzart,
    ROUND(SUM(gesamt_leistung_w), 0) AS gesamtleistung_w,
    COUNT(*) AS elemente_anzahl
FROM (
    SELECT 
        re.TABELLE_Räume_idTABELLE_Räume AS raum_id,
        e.Bezeichnung AS element,
        v.Variante,
        SUM(re.Anzahl) AS anzahl,

        COALESCE(CAST(NULLIF(pep_leistung.Wert, '') AS DECIMAL(10,2)), 0) AS leistung_raw,
        pep_leistung.Einheit,

        CASE
            WHEN pep_leistung.Einheit = 'kW' THEN COALESCE(CAST(NULLIF(pep_leistung.Wert, '') AS DECIMAL(10,2)), 0) * 1000
            WHEN pep_leistung.Einheit = 'MW' THEN COALESCE(CAST(NULLIF(pep_leistung.Wert, '') AS DECIMAL(10,2)), 0) * 1000000
            ELSE COALESCE(CAST(NULLIF(pep_leistung.Wert, '') AS DECIMAL(10,2)), 0)
        END AS leistung_w,
 
        CASE 
            WHEN pep_netz.Wert IN ('AV', 'Akku', '/Akku','AV/Akku') THEN 'AV'
            WHEN pep_netz.Wert IN ('SV', 'SV/Akku') THEN 'SV'
            WHEN pep_netz.Wert IN ('ZSV','ZSV/Akku') THEN 'ZSV'
            WHEN pep_netz.Wert IN ('USV', 'USV/Akku') THEN 'USV'
            ELSE 'Unbekannt'
        END AS mapped_netzart,

        SUM(re.Anzahl) * 
        CASE
            WHEN pep_leistung.Einheit = 'kW' THEN COALESCE(CAST(NULLIF(pep_leistung.Wert, '') AS DECIMAL(10,2)), 0) * 1000
            WHEN pep_leistung.Einheit = 'MW' THEN COALESCE(CAST(NULLIF(pep_leistung.Wert, '') AS DECIMAL(10,2)), 0) * 1000000
            ELSE COALESCE(CAST(NULLIF(pep_leistung.Wert, '') AS DECIMAL(10,2)), 0)
        END *
        COALESCE(
            CASE 
                WHEN pep_gleich.Wert IS NULL OR pep_gleich.Wert = '' OR pep_gleich.Wert = '1' THEN 1.0
                WHEN pep_gleich.Wert = '0' THEN 0.0
                ELSE CAST(REPLACE(pep_gleich.Wert, ',', '.') AS DECIMAL(5,2))  
            END, 
            1.0
        ) AS gesamt_leistung_w

    FROM tabelle_räume_has_tabelle_elemente re
        INNER JOIN tabelle_elemente e ON re.TABELLE_Elemente_idTABELLE_Elemente = e.idTABELLE_Elemente
        INNER JOIN tabelle_varianten v ON re.tabelle_Varianten_idtabelle_Varianten = v.idtabelle_Varianten

        LEFT JOIN tabelle_projekt_elementparameter pep_leistung ON 
            pep_leistung.tabelle_Varianten_idtabelle_Varianten = v.idtabelle_Varianten
            AND pep_leistung.tabelle_elemente_idTABELLE_Elemente = e.idTABELLE_Elemente
            AND pep_leistung.tabelle_projekte_idTABELLE_Projekte = ?
            AND pep_leistung.tabelle_parameter_idTABELLE_Parameter = 18

        LEFT JOIN tabelle_projekt_elementparameter pep_gleich ON 
            pep_gleich.tabelle_Varianten_idtabelle_Varianten = v.idtabelle_Varianten
            AND pep_gleich.tabelle_elemente_idTABELLE_Elemente = e.idTABELLE_Elemente
            AND pep_gleich.tabelle_projekte_idTABELLE_Projekte = ?
            AND pep_gleich.tabelle_parameter_idTABELLE_Parameter = 133

        LEFT JOIN tabelle_projekt_elementparameter pep_netz ON 
            pep_netz.tabelle_Varianten_idtabelle_Varianten = v.idtabelle_Varianten
            AND pep_netz.tabelle_elemente_idTABELLE_Elemente = e.idTABELLE_Elemente
            AND pep_netz.tabelle_projekte_idTABELLE_Projekte = ?
            AND pep_netz.tabelle_parameter_idTABELLE_Parameter = 82

    WHERE re.TABELLE_Räume_idTABELLE_Räume = ?
        AND re.Verwendung = 1
        AND re.Anzahl > 0
        AND COALESCE(CAST(NULLIF(pep_leistung.Wert, '') AS DECIMAL(10,2)), 0) > 0

    GROUP BY 
        re.TABELLE_Räume_idTABELLE_Räume, 
        e.idTABELLE_Elemente, 
        v.idtabelle_Varianten,
        pep_leistung.Wert, 
        pep_leistung.Einheit, 
        pep_gleich.Wert,
        pep_netz.Wert  
    HAVING leistung_w > 0
) AS element_leistungen
GROUP BY mapped_netzart  
ORDER BY 
    CASE netzart 
        WHEN 'AV' THEN 1 
        WHEN 'SV' THEN 2 
        WHEN 'ZSV' THEN 3 
        WHEN 'USV' THEN 4 
        ELSE 5 
    END";

$stmt = $mysqli->prepare($sql);
if (!$stmt) {
    error_log("Prepare failed: " . $mysqli->error);
    return;
}

$stmt->bind_param('iiii', $projectID, $projectID, $projectID, $raumID);
if (!$stmt->execute()) {
    error_log("Execute failed: " . $stmt->error);
    return;
}

$resultElementleistung = $stmt->get_result();

$leistungen = ['AV' => 0, 'SV' => 0, 'ZSV' => 0, 'USV' => 0, 'Unbekannt' => 0];
while ($leisRow = $resultElementleistung->fetch_assoc()) {
    $net = $leisRow['netzart'];
    $sum = (float)$leisRow['gesamtleistung_w'];
    if (array_key_exists($net, $leistungen)) {
        $leistungen[$net] += $sum;
    } else {
        $leistungen['Unbekannt'] += $sum;
    }
}
$stmt->close();

// PDF Output - UNVERÄNDERT
multicell_text_hightlight($pdf, $e_C*2 +10, $font_size, 'MT Leistung AV', "Leistungen nach Netzart inkl. Gleichzeitigkeit -  AV: ", $parameter_changes_t_räume);
multicell_with_nr($pdf, round($leistungen['AV'], 0), "W", $font_size, $e_C_3rd + 10);

multicell_text_hightlight($pdf, $e_C_2_3rd, $font_size, ' SV', "SV: ", $parameter_changes_t_räume);
multicell_with_nr($pdf, round($leistungen['SV'], 0), "W", $font_size, $e_C_3rd + 10);

multicell_text_hightlight($pdf, $e_C_2_3rd, $font_size, 'MT Leistung ZSV', "ZSV: ", $parameter_changes_t_räume);
multicell_with_nr($pdf, round($leistungen['ZSV'], 0), "W", $font_size, $e_C_3rd + 10);

multicell_text_hightlight($pdf, $e_C_2_3rd, $font_size, 'MT Leistung USV', "USV: ", $parameter_changes_t_räume);
multicell_with_nr($pdf, round($leistungen['USV'], 0), "W", $font_size, $e_C_3rd + 10);

multicell_text_hightlight($pdf, $e_C_2_3rd, $font_size, 'MT Leistung ohne Zuordnung', "Ohne NA:", $parameter_changes_t_räume);
multicell_with_nr($pdf, round($leistungen['Unbekannt'], 0), "W", $font_size, $e_C_3rd + 10);
?>
