<?php
// pdf_getRaumleistungInklGlz.php
global $block_header_height, $block_header_w, $SB, $pdf, $mysqli, $valueOfRoomID, $row, $parameter_changes_t_räume,
       $e_C, $font_size, $e_C_2_3rd, $e_C_3rd, $horizontalSpacerLN2; // Globale Variablen nutzen

$raumID = $valueOfRoomID;  // Aus der Hauptschleife
$projectID = $_SESSION["projectID"];

 $sql = "SELECT 
    netzart,
    ROUND(SUM(gesamt_leistung_w), 2) AS gesamtleistung_w,
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
        END AS leistung,  -- always in W

        COALESCE(CAST(NULLIF(pep_gleich.Wert, '') AS DECIMAL(10,2)), 100) AS gleichzeitigkeit,
        COALESCE(pep_netz.Wert, 'Unbekannt') AS netzart,

        SUM(re.Anzahl)
        * CASE
                WHEN pep_leistung.Einheit = 'kW' THEN COALESCE(CAST(NULLIF(pep_leistung.Wert, '') AS DECIMAL(10,2)), 0) * 1000
                WHEN pep_leistung.Einheit = 'MW' THEN COALESCE(CAST(NULLIF(pep_leistung.Wert, '') AS DECIMAL(10,2)), 0) * 1000000
                ELSE COALESCE(CAST(NULLIF(pep_leistung.Wert, '') AS DECIMAL(10,2)), 0)
              END
              * COALESCE(CAST(NULLIF(pep_gleich.Wert, '') AS DECIMAL(10,2)), 100)/100
        AS gesamt_leistung_w

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
    GROUP BY 
        re.TABELLE_Räume_idTABELLE_Räume, e.idTABELLE_Elemente, v.idtabelle_Varianten,
        pep_leistung.Wert, pep_leistung.Einheit, pep_gleich.Wert, pep_netz.Wert
    HAVING anzahl > 0 AND leistung > 0
) AS element_leistungen
GROUP BY netzart
ORDER BY 
    CASE netzart 
        WHEN 'AV' THEN 1 
        WHEN 'SV' THEN 2 
        WHEN 'ZSV' THEN 3 
        WHEN 'USV' THEN 4 
        ELSE 5 
    END;
";



$stmt = $mysqli->prepare($sql);
$stmt->bind_param('iiii', $projectID, $projectID, $projectID, $raumID);
$stmt->execute();
$resultElementleistung = $stmt->get_result();

$leistungen = ['AV' => 0, 'SV' => 0, 'ZSV' => 0, 'USV' => 0, 'Unbekannt' => 0];
if (isset($resultElementleistung) && $resultElementleistung instanceof mysqli_result) {
    $resultElementleistung->data_seek(0); // Reset pointer
    while ($leisRow = $resultElementleistung->fetch_assoc()) {
        $net = $leisRow['netzart'];
        $sum = (float)$leisRow['gesamtleistung_w'];
        if (array_key_exists($net, $leistungen)) {
            $leistungen[$net] += $sum;
        } else {
            $leistungen['Unbekannt'] += $sum;
        }
    }
}

// Netzart-Leistungen ausgeben als Multicells im Elektro-Block   Leistungen nach Netzart inkl. Gleichzeitigkeit


multicell_text_hightlight($pdf, $e_C*2 +10, $font_size, 'MT Leistung AV', "Leistungen nach Netzart inkl. Gleichzeitigkeit -  AV: ", $parameter_changes_t_räume);
multicell_with_nr($pdf, round($leistungen['AV'], 0), "W", $font_size, $e_C_3rd + 10);

multicell_text_hightlight($pdf, $e_C_2_3rd, $font_size, ' SV', "SV: ", $parameter_changes_t_räume);
multicell_with_nr($pdf, round($leistungen['SV'], 0), "W", $font_size, $e_C_3rd + 10);

multicell_text_hightlight($pdf, $e_C_2_3rd, $font_size, 'MT Leistung ZSV', "ZSV: ", $parameter_changes_t_räume);
multicell_with_nr($pdf, round($leistungen['ZSV'], 0), "W", $font_size, $e_C_3rd + 10);

multicell_text_hightlight($pdf, $e_C_2_3rd, $font_size, 'MT Leistung USV', "USV: ", $parameter_changes_t_räume);
multicell_with_nr($pdf, round($leistungen['USV'], 0), "W", $font_size, $e_C_3rd + 10);

multicell_text_hightlight($pdf, $e_C_2_3rd, $font_size, 'MT Leistung Unbekannt', "Ohne NA:", $parameter_changes_t_räume);
multicell_with_nr($pdf, round($leistungen['Unbekannt'], 0), "W", $font_size, $e_C_3rd + 10);

