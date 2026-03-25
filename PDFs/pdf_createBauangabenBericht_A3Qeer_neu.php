<?php
/**
 * Bauangaben Bericht – A3 Landscape
 *
 * Refactored for clarity and performance:
 *  - All DB queries extracted into dedicated functions
 *  - Room data fetched in a single batch query instead of one per room
 *  - Change-detection logic isolated in its own function
 *  - Rendering sections kept as small, focused helpers
 */

global $mapping, $mp2;
ob_start();

require_once '../utils/_utils.php';
check_login();

include 'pdf_createBericht_MYPDFclass_A3Queer.php';
include '_pdf_createBericht_utils.php';
include 'pdf_createMTTabelle_neu.php';
include 'pdf_createBauangabenBericht_constDefinitions.php';


// ---------------------------------------------------------------------------
// DB QUERY FUNCTIONS
// ---------------------------------------------------------------------------

/**
 * Fetch raw change rows for a single room since $since.
 */
function db_fetch_room_changes(mysqli $db, int $roomId, string $since): array
{
    $stmt = $db->prepare(
        "SELECT * FROM `tabelle_raeume_aenderungen`
         WHERE `raum_id` = ? AND `Timestamp` > ?"
    );
    $stmt->bind_param("is", $roomId, $since);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    $stmt->close();
    return $rows;
}

/**
 * Fetch full room data for a list of room IDs in one query.
 * Returns an associative array keyed by room ID.
 */
function db_fetch_rooms_batch(mysqli $db, array $roomIds): array
{
    if (empty($roomIds)) {
        return [];
    }

    $placeholders = implode(',', array_fill(0, count($roomIds), '?'));
    $types        = str_repeat('i', count($roomIds));

    $sql = "SELECT
        r.idTABELLE_Räume,
        r.Raumnr, r.Raumbezeichnung, r.`Raumbereich Nutzer`, r.Geschoss,
        r.Bauetappe, r.Bauabschnitt, r.`Fussboden OENORM B5220`, r.Fussboden,
        r.`Allgemeine Hygieneklasse`, r.Nutzfläche, r.Abdunkelbarkeit,
        r.Strahlenanwendung, r.Laseranwendung, r.H6020, r.GMP, r.ISO,
        r.`1 Kreis O2`, r.`2 Kreis O2`, r.`1 Kreis Va`, r.`2 Kreis Va`,
        r.`1 Kreis DL-5`, r.`2 Kreis DL-5`, r.`DL-10`, r.`DL-tech`,
        r.CO2, r.NGA, r.N2O, r.AV, r.SV, r.ZSV, r.USV,
        r.Anwendungsgruppe,
        r.`Anmerkung MedGas`, r.`Anmerkung Elektro`,
        r.`Anmerkung HKLS`, r.`Anmerkung Geräte`,
        r.`Anmerkung FunktionBO`, r.`Anmerkung BauStatik`,
        r.HT_Waermeabgabe_W, r.`IT Anbindung`,
        ROUND(r.Umfang, 2) AS Umfang,
        ROUND(r.Volumen, 2) AS Volumen,
        r.Raumhoehe, r.`Raumhoehe 2`, r.Belichtungsfläche,
        r.ET_Anschlussleistung_W,
        r.ET_Anschlussleistung_AV_W, r.ET_Anschlussleistung_SV_W,
        r.ET_Anschlussleistung_ZSV_W, r.ET_Anschlussleistung_USV_W,
        r.`EL_AV Steckdosen Stk`, r.`EL_SV Steckdosen Stk`,
        r.`EL_ZSV Steckdosen Stk`, r.`EL_USV Steckdosen Stk`,
        r.`ET_RJ45-Ports`,
        r.`EL_Roentgen 16A CEE Stk`, r.`EL_Laser 16A CEE Stk`,
        r.HT_Abluft_Digestorium_Stk, r.HT_Notdusche, r.VE_Wasser,
        r.ET_16A_3Phasig_Einzelanschluss,
        r.HT_Punktabsaugung_Stk,
        r.HT_Abluft_Sicherheitsschrank_Unterbau_Stk,
        r.HT_Abluft_Sicherheitsschrank_Stk,
        r.`EL_Einzel-Datendose Stk`, r.`EL_Doppeldatendose Stk`,
        r.`EL_Bodendose Typ`, r.`EL_Bodendose Stk`,
        r.`EL_Beleuchtung 1 Typ`, r.`EL_Beleuchtung 2 Typ`,
        r.`EL_Beleuchtung 3 Typ`, r.`EL_Beleuchtung 4 Typ`,
        r.`EL_Beleuchtung 5 Typ`,
        r.`EL_Beleuchtung 1 Stk`, r.`EL_Beleuchtung 2 Stk`,
        r.`EL_Beleuchtung 3 Stk`, r.`EL_Beleuchtung 4 Stk`,
        r.`EL_Beleuchtung 5 Stk`,
        r.`EL_Lichtschaltung BWM JA/NEIN`,
        r.`EL_Beleuchtung dimmbar JA/NEIN`,
        r.`EL_Brandmelder Decke JA/NEIN`,
        r.`EL_Brandmelder ZwDecke JA/NEIN`,
        r.`EL_Kamera Stk`, r.`EL_Lautsprecher Stk`,
        r.`EL_Uhr - Wand Stk`, r.`EL_Uhr - Decke Stk`,
        r.`EL_Lichtruf - Terminal Stk`, r.`EL_Lichtruf - Steckmodul Stk`,
        r.`EL_Lichtfarbe K`,
        r.`EL_Notlicht RZL Stk`, r.`EL_Notlicht SL Stk`,
        r.`EL_Jalousie JA/NEIN`,
        r.`HT_Luftmenge m3/h`,
        CAST(REPLACE(r.`HT_Luftwechsel 1/h`, ',', '.') AS DECIMAL(10,2)) AS HT_Luftwechsel,
        r.`HT_Kühlung Lueftung W`, r.`HT_Heizlast W`, r.`HT_Kühllast W`,
        r.`HT_Fussbodenkühlung W`, r.`HT_Kühldecke W`, r.`HT_Fancoil W`,
        r.`HT_Summe Kühlung W`,
        r.`HT_Raumtemp Sommer °C`, r.`HT_Raumtemp Winter °C`,
        r.AR_Ausstattung, r.Aufenthaltsraum,
        p.Projektname,
        pp.Bezeichnung AS Planungsphase
    FROM tabelle_planungsphasen pp
        INNER JOIN tabelle_projekte p
            ON pp.idTABELLE_Planungsphasen = p.TABELLE_Planungsphasen_idTABELLE_Planungsphasen
        INNER JOIN tabelle_räume r
            ON p.idTABELLE_Projekte = r.tabelle_projekte_idTABELLE_Projekte
    WHERE r.idTABELLE_Räume IN ($placeholders)";

    $stmt = $db->prepare($sql);
    $stmt->bind_param($types, ...$roomIds);
    $stmt->execute();
    $result = $stmt->get_result();

    $rooms = [];
    while ($row = $result->fetch_assoc()) {
        $rooms[(int)$row['idTABELLE_Räume']] = $row;
    }
    $stmt->close();
    return $rooms;
}

/**
 * Fetch elements (MT items) for a single room.
 * Returns the mysqli_result directly so the caller can iterate or count.
 */
function db_fetch_room_elements(mysqli $db, int $roomId): mysqli_result
{
    $sql = "SELECT
        e.ElementID, e.Bezeichnung,
        v.Variante,
        SUM(rhe.Anzahl) AS SummevonAnzahl,
        rhe.`Neu/Bestand`,
        rhe.TABELLE_Elemente_idTABELLE_Elemente,
        rhe.tabelle_Varianten_idtabelle_Varianten,
        rhe.Standort, rhe.Verwendung
    FROM tabelle_varianten v
        INNER JOIN tabelle_räume_has_tabelle_elemente rhe
            ON v.idtabelle_Varianten = rhe.tabelle_Varianten_idtabelle_Varianten
        INNER JOIN tabelle_elemente e
            ON rhe.TABELLE_Elemente_idTABELLE_Elemente = e.idTABELLE_Elemente
    GROUP BY
        e.ElementID, e.Bezeichnung, v.Variante, rhe.`Neu/Bestand`,
        rhe.TABELLE_Elemente_idTABELLE_Elemente,
        rhe.tabelle_Varianten_idtabelle_Varianten,
        rhe.TABELLE_Räume_idTABELLE_Räume
    HAVING rhe.TABELLE_Räume_idTABELLE_Räume = $roomId
       AND SummevonAnzahl > 0
    ORDER BY e.ElementID, v.Variante";

    return $db->query($sql);
}

/**
 * Fetch project-level parameter definitions (for MT table header).
 */
function db_fetch_project_params(mysqli $db, int $projectId): mysqli_result
{
    $sql = "SELECT
        pk.Kategorie, p.Abkuerzung, p.Bezeichnung,
        p.idTABELLE_Parameter,
        pk.idTABELLE_Parameter_Kategorie
    FROM tabelle_parameter_kategorie pk
        INNER JOIN tabelle_parameter p
            ON pk.idTABELLE_Parameter_Kategorie = p.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie
        INNER JOIN tabelle_projekt_elementparameter pep
            ON p.idTABELLE_Parameter = pep.tabelle_parameter_idTABELLE_Parameter
    WHERE pep.tabelle_projekte_idTABELLE_Projekte = $projectId
      AND p.`Bauangaben relevant` = 1
      AND pk.idTABELLE_Parameter_Kategorie != 18
    GROUP BY pk.Kategorie, p.Bezeichnung
    ORDER BY pk.Kategorie, p.Bezeichnung";

    return $db->query($sql);
}

/**
 * Fetch all element parameter values for the project (for MT table cells).
 */
function db_fetch_element_params(mysqli $db, int $projectId): mysqli_result
{
    $sql = "SELECT
        pep.Wert, pep.Einheit,
        pep.tabelle_Varianten_idtabelle_Varianten,
        pep.tabelle_elemente_idTABELLE_Elemente,
        p.Bezeichnung,
        pk.Kategorie,
        pk.idTABELLE_Parameter_Kategorie,
        p.idTABELLE_Parameter
    FROM tabelle_parameter_kategorie pk
        INNER JOIN tabelle_parameter p
            ON pk.idTABELLE_Parameter_Kategorie = p.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie
        INNER JOIN tabelle_projekt_elementparameter pep
            ON p.idTABELLE_Parameter = pep.tabelle_parameter_idTABELLE_Parameter
    WHERE pep.tabelle_projekte_idTABELLE_Projekte = $projectId
      AND p.`Bauangaben relevant` = 1
      AND pk.idTABELLE_Parameter_Kategorie != 18
    ORDER BY pk.Kategorie, p.Bezeichnung";

    return $db->query($sql);
}

/**
 * Fetch element-parameter change log for the project since $since.
 */
function db_fetch_element_param_changes(mysqli $db, int $projectId, string $since): array
{
    $sql = "SELECT
        idtabelle_projekt_elementparameter_aenderungen,
        projekt, element, parameter, variante,
        wert_alt, wert_neu, einheit_alt, einheit_neu,
        `timestamp`, user
    FROM tabelle_projekt_elementparameter_aenderungen
    WHERE projekt = $projectId
      AND `timestamp` > '$since'
    ORDER BY `timestamp` DESC";

    $result = $db->query($sql);
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    return $rows;
}


// ---------------------------------------------------------------------------
// CHANGE DETECTION
// ---------------------------------------------------------------------------

/**
 * Given raw change rows from tabelle_raeume_aenderungen, determine which
 * room-level fields actually changed (net effect old → newest).
 * Returns a flat array of field names (as used in tabelle_räume).
 */
function detect_room_field_changes(array $changeRows, array $mapping, array $mp2): array
{
    $changed = [];

    foreach ($mapping as $oldKey => $newKey) {
        if (!isset($mp2[$newKey]) || $mp2[$newKey] === '') {
            continue;
        }

        $fieldName = $mp2[$newKey];
        $entries   = [];

        foreach ($changeRows as $entry) {
            if ($entry[$oldKey] !== $entry[$newKey]) {
                $entries[] = [
                    'timestamp' => $entry['Timestamp'],
                    'oldValue'  => $entry[$oldKey],
                    'newValue'  => $entry[$newKey],
                ];
            }
        }

        if (empty($entries)) {
            continue;
        }

        usort($entries, fn($a, $b) => $a['timestamp'] <=> $b['timestamp']);

        $netOld = reset($entries)['oldValue'];
        $netNew = end($entries)['newValue'];

        if ($netNew !== $netOld) {
            $changed[] = $fieldName;
        }
    }

    return $changed;
}


// ---------------------------------------------------------------------------
// SECTION RENDERERS
// ---------------------------------------------------------------------------

/**
 * Render the "BO-Beschreibung" block if present.
 */
function render_bo_beschreibung(
    $pdf,
    array $row,
    int $block_header_w,
    int $block_header_height,
    int $SB,
    int $horizontalSpacerLN
): void {
    $text = trim($row['Anmerkung FunktionBO'] ?? '');
    if ($text === '') {
        return;
    }

    $outstr          = format_text(clean_string(br2nl($row['Anmerkung FunktionBO'])));
    $rowHeightComment = $pdf->getStringHeight($SB - 10, $outstr, false, true, '', 1);
    $spacer          = ($rowHeightComment > 6) ? $horizontalSpacerLN : 0;

    block_label_queer($block_header_w, $pdf, 'BO-Beschr.', $rowHeightComment + $spacer, $block_header_height, $SB);
    $pdf->SetFont('helvetica', 'I', 10);
    $pdf->MultiCell(10, $rowHeightComment, '', 0, 'L', 0, 0);
    $pdf->MultiCell($SB - 10, $rowHeightComment, $outstr, 0, 'L', 0, 1);
    $pdf->Ln($rowHeightComment > 6 ? $horizontalSpacerLN : 1);
}

/**
 * Render the "Allgemein" block, driven by $allgemeinParams.
 *
 * Supported param types:
 *   'str'      → multicell_with_str   (plain text value)
 *   'str_fb'   → multicell_with_str with fallback ' - ' when empty (used for Hygieneklasse)
 *   'nr'       → multicell_with_nr    (numeric + unit)
 *   'hackerl'  → hackerlA3 checkbox
 *   'strahlen' → strahlenanw special renderer
 */
function render_allgemein_block(
    $pdf,
    array $row,
    array $allgemeinParams,
    array $changes,
    int $font_size,
    int $block_header_w,
    int $block_header_height,
    int $SB,
    int $horizontalSpacerLN3
): void {
    block_label_queer($block_header_w, $pdf, 'Allgemein', $horizontalSpacerLN3 + 6, $block_header_height, $SB);

    $heightExceeds = false;

    foreach ($allgemeinParams as $param) {
        $key   = $param['key'];
        $value = $row[$key] ?? '';

        multicell_text_hightlight($pdf, $param['cell'], $font_size, $key, $param['label'], $changes);

        switch ($param['type']) {
            case 'str':
                multicell_with_str($pdf, $value, $param['str_cell'], $param['unit'] ?? '');
                break;

            case 'str_fb':
                // Track if this value is tall enough to need an extra spacer
                if ($value !== '') {
                    $heightExceeds = $pdf->getStringHeight($param['str_cell'], $value, false, true, '', 1) > 6;
                    multicell_with_str($pdf, $value, $param['str_cell'], $param['unit'] ?? '');
                } else {
                    multicell_with_str($pdf, ' - ', $param['str_cell'], '');
                }
                break;

            case 'nr':
                multicell_with_nr($pdf, $value, $param['unit'], 10, $param['str_cell']);
                break;

            case 'hackerl':
                hackerlA3($pdf, $font_size, $param['str_cell'], $value, 'JA');
                break;

            case 'strahlen':
                // Expand cell if text is tall
                $cellW = ($pdf->getStringHeight($param['str_cell'], $value) > 6)
                    ? $param['str_cell_wide']
                    : $param['str_cell'];
                strahlenanw($pdf, $value, $cellW, $font_size);
                break;
        }

        if (!empty($param['ln_after'])) {
            $pdf->Ln($param['ln_after']);
        }
    }

    $pdf->Ln($horizontalSpacerLN3);
    if ($heightExceeds) {
        $pdf->Ln(4);
    }
}

/**
 * Render the "Elektro" block.
 */
function render_elektro_block(
    $pdf,
    array $row,
    array $elektroParams,
    array $changes,
    bool $isnotVorentwurf,
    int $font_size,
    int $block_header_w,
    int $block_header_height,
    int $SB,
    int $horizontalSpacerLN,
    int $horizontalSpacerLN2
): void {
    $blockHeight = 12 + $horizontalSpacerLN + getAnmHeight($pdf, $row['Anmerkung Elektro'], $SB) + $horizontalSpacerLN + $horizontalSpacerLN2;
    block_label_queer($block_header_w, $pdf, 'Elektro', $blockHeight, $block_header_height, $SB);

    // Keys that get kify + unit treatment
    $kifyKeys = [
        'Anwendungsgruppe', 'ET_Anschlussleistung_W',
        'ET_Anschlussleistung_AV_W', 'ET_Anschlussleistung_SV_W',
        'ET_Anschlussleistung_ZSV_W', 'ET_Anschlussleistung_USV_W',
    ];
    // Keys rendered with multicell_with_nr
    $nrKeys = ['ET_RJ45-Ports'];
    // Keys skipped entirely in Vorentwurf
    $vorentwurfSkip = ['ET_RJ45-Ports', 'EL_Laser 16A CEE Stk', 'EL_Roentgen 16A CEE Stk', 'RaumAnschlussLeistungInklGlz'];

    foreach ($elektroParams as $param) {
        $key = $param['key'];

        // Skip Vorentwurf-only params when in Vorentwurf
        if (!$isnotVorentwurf && in_array($key, $vorentwurfSkip)) {
            // Add line-break spacer after last power field in Vorentwurf
            if ($key === 'ET_Anschlussleistung_USV_W') {
                $pdf->Ln($horizontalSpacerLN2);
                $pdf->MultiCell($block_header_w, $block_header_height, '', 0, 'L', 0, 0);
            }
            continue;
        }

        if ($key === 'RaumAnschlussLeistungInklGlz') {
            include 'pdf_getRaumleistungInklGlz.php';
        } elseif (in_array($key, $kifyKeys)) {
            $val = ($row[$key] != '0') ? kify($row[$key]) . $param['unit'] : '-';
            multicell_text_hightlight($pdf, $param['cell'], $font_size, $key, $param['label'], $changes);
            multicell_with_str($pdf, $val, $param['str_cell'], '');
        } elseif (in_array($key, $nrKeys)) {
            multicell_text_hightlight($pdf, $param['cell'], $font_size, $key, $param['label'], $changes);
            multicell_with_nr($pdf, $row[$key], $param['unit'], $pdf->getFontSizePt(), $param['str_cell']);
        } else {
            multicell_text_hightlight($pdf, $param['cell'], $font_size, $key, $param['label'], $changes);
            if (in_array($key, ['EL_Laser 16A CEE Stk', 'EL_Roentgen 16A CEE Stk'])) {
                multicell_with_str($pdf, $row[$key], $param['str_cell'], $param['unit']);
            } else {
                hackerlA3($pdf, $font_size, $param['str_cell'], $row[$key], 'JA');
            }
        }

        if (!empty($param['ln_after'])) {
            $pdf->Ln($horizontalSpacerLN2);
            $pdf->MultiCell($block_header_w, $block_header_height, '', 0, 'L', 0, 0);
        }
    }

    anmA3($pdf, $row['Anmerkung Elektro'], $SB, $block_header_w);
    $pdf->Ln($horizontalSpacerLN);
}

/**
 * Render the "Haustechnik" block.
 */
function render_haustechnik_block(
    $pdf,
    array $row,
    array $haustechnikParams,
    array $changes,
    int $font_size,
    int $block_header_w,
    int $block_header_height,
    int $SB,
    int $horizontalSpacerLN,
    int $horizontalSpacerLN2
): void {
    $blockHeight = 6 + $horizontalSpacerLN2 + getAnmHeight($pdf, $row['Anmerkung HKLS'], $SB);
    block_label_queer($block_header_w, $pdf, 'Haustechnik', $blockHeight, $block_header_height, $SB);

    foreach ($haustechnikParams as $param) {
        multicell_text_hightlight($pdf, $param['cell'], $font_size, $param['key'], $param['label'], $changes);

        $value = match ($param['key']) {
            'HT_Waermeabgabe_W' => (
                $row['HT_Waermeabgabe_W'] === '0' ||
                $row['HT_Waermeabgabe_W'] == 0 ||
                $row['HT_Waermeabgabe_W'] == '-'
            ) ? 'keine Angabe' : kify($row['HT_Waermeabgabe_W']) . 'W',
            'VE_Wasser' => translate_1_to_yes($row['VE_Wasser']),
            default     => $row[$param['key']],
        };

        multicell_with_str($pdf, $value, $param['str_cell'], $param['unit']);

        if (!empty($param['ln_after'])) {
            $pdf->Ln($horizontalSpacerLN2);
            $pdf->MultiCell($block_header_w, 1, '', 0, 0, 0, 0);
        }
    }

    $pdf->Ln($horizontalSpacerLN2);
    anmA3($pdf, $row['Anmerkung HKLS'], $SB, $block_header_w);
    $pdf->Ln($horizontalSpacerLN);
}

/**
 * Render the "Med.-Gas" block, driven by $medgasParams.
 * All items are hackerl checkboxes; set 'ln_after' => true on a param to insert a row break after it.
 */
function render_medgas_block(
    $pdf,
    array $row,
    array $medgasParams,
    array $changes,
    int $font_size,
    int $block_header_w,
    int $block_header_height,
    int $SB,
    int $horizontalSpacerLN,
    int $horizontalSpacerLN2
): void {
    $blockHeight = 12 + $horizontalSpacerLN + getAnmHeight($pdf, $row['Anmerkung MedGas'], $SB);
    block_label_queer($block_header_w, $pdf, 'Med.-Gas', $blockHeight, $block_header_height, $SB);

    foreach ($medgasParams as $param) {
        multicell_text_hightlight($pdf, $param['cell'], $font_size, $param['key'], $param['label'], $changes);
        hackerlA3($pdf, $font_size, $param['str_cell'], $row[$param['key']], 1);

        if (!empty($param['ln_after'])) {
            $pdf->Ln($horizontalSpacerLN);
            $pdf->MultiCell($block_header_w, $block_header_height, '', 0, 'L', 0, 0);
        }
    }

    $pdf->Ln($horizontalSpacerLN2);
    anmA3($pdf, $row['Anmerkung MedGas'], $SB, $block_header_w);
}

/**
 * Render the "Baustatik" block if content is present.
 */
function render_baustatik_block(
    $pdf,
    array $row,
    int $block_header_w,
    int $block_header_height,
    int $SB,
    int $horizontalSpacerLN
): void {
    $text = $row['Anmerkung BauStatik'] ?? '';
    if ($text === '' || $text === 'keine Angaben MT') {
        return;
    }

    $pdf->Ln($horizontalSpacerLN);
    $blockHeight = getAnmHeight($pdf, $text, $SB);
    block_label_queer($block_header_w, $pdf, 'Baustatik', $blockHeight, $block_header_height, $SB);
    $pdf->Ln(1);
    anmA3($pdf, $text, $SB, $block_header_w);
    $pdf->Ln($horizontalSpacerLN);
}

/**
 * Render the MT element table or list depending on Planungsphase.
 */
function render_mt_block(
    $pdf,
    mysqli $db,
    int $roomId,
    bool $isnotVorentwurf,
    int $projectId,
    string $changeDate,
    int $block_header_w,
    int $block_header_height,
    int $SB,
    int $SH,
    array $style_normal,
    array $style_dashed
): void {
    $resultElements = db_fetch_room_elements($db, $roomId);
    $rowCount = $resultElements->num_rows;

    if ($rowCount === 0) {
        $pdf->Line(15, $pdf->GetY(), $SB + 15, $pdf->GetY(), $style_normal);
        block_label_queer($block_header_w, $pdf, 'Med.-tech.', 0, $block_header_height, $SB);
        $pdf->MultiCell(0, 0, 'Keine medizintechnische Ausstattung.', '', 'L', 0, 0);
        $pdf->Ln();
        return;
    }

    if ($isnotVorentwurf) {
        $result1      = db_fetch_project_params($db, $projectId);
        $result3      = db_fetch_element_params($db, $projectId);
        $rawChanges   = db_fetch_element_param_changes($db, $projectId, $changeDate);
        $dataChanges  = filter_old_equal_new($rawChanges);

        $blockSize = 10 + $rowCount * 5;
        block_label_queer($block_header_w, $pdf, 'Med.-tech.', $blockSize, $block_header_height, $SB);
        make_MT_details_table($pdf, $resultElements, $result1, $result3, $SB, $SH, $dataChanges);
    } else {
        $blockSize = 10 + (int)($rowCount / 2) * 5;
        block_label_queer($block_header_w, $pdf, 'Med.-tech.', $blockSize, $block_header_height, $SB);
        $pdf->Line(15 + $block_header_w, $pdf->GetY(), $SB + 15, $pdf->GetY(), $style_dashed);
        make_MT_list($pdf, $SB, $block_header_w, $rowCount, $resultElements, $style_normal, $style_dashed);
    }
}


// ---------------------------------------------------------------------------
// LAYOUT / FORMATTING CONSTANTS
// ---------------------------------------------------------------------------

$marginTop  = 17;
$marginBTM  = 10;
/** @noinspection PhpUndefinedConstantInspection */
$SB = 420 - 2 * PDF_MARGIN_LEFT;   // A3 landscape usable width
$SH = 297 - $marginTop - $marginBTM;

$horizontalSpacerLN  = 4;
$horizontalSpacerLN2 = 5;
$horizontalSpacerLN3 = 8;

$e_B       = $SB / 6;
$e_B_3rd   = $e_B / 3;
$e_B_2_3rd = $e_B - $e_B_3rd;
$e_C       = $SB / 8;
$e_C_3rd   = $e_C / 3;
$e_C_2_3rd = $e_C - $e_C_3rd;

$font_size          = 6;
$block_header_height = 10;
$block_header_w     = 25;

$colour_line  = [110, 150, 80];
$style_dashed = ['width' => 0.1, 'cap' => 'round', 'join' => 'round', 'dash' => 4, 'color' => $colour_line];
$style_normal = ['width' => 0.3, 'cap' => 'round', 'join' => 'round', 'dash' => 0, 'color' => $colour_line];


// ---------------------------------------------------------------------------
// PARAM DEFINITIONS  (started by caller — completed here)
// ---------------------------------------------------------------------------

$elektroParams = [
    ['key' => 'Anwendungsgruppe',            'label' => 'ÖVE E8101:',                    'unit' => '',    'cell' => $e_C,        'str_cell' => $e_C_3rd + 10, 'ln_after' => false, 'isnotVorentwurf' => false],
    ['key' => 'AV',                          'label' => 'AV: ',                           'unit' => '',    'cell' => $e_C_2_3rd,  'str_cell' => $e_C_3rd + 10, 'ln_after' => false, 'isnotVorentwurf' => false],
    ['key' => 'SV',                          'label' => 'SV: ',                           'unit' => '',    'cell' => $e_C_2_3rd,  'str_cell' => $e_C_3rd + 10, 'ln_after' => false, 'isnotVorentwurf' => false],
    ['key' => 'ZSV',                         'label' => 'ZSV: ',                          'unit' => '',    'cell' => $e_C_2_3rd,  'str_cell' => $e_C_3rd + 10, 'ln_after' => false, 'isnotVorentwurf' => false],
    ['key' => 'USV',                         'label' => 'USV: ',                          'unit' => '',    'cell' => $e_C_2_3rd,  'str_cell' => $e_C_3rd + 10, 'ln_after' => false, 'isnotVorentwurf' => false],
    ['key' => 'IT Anbindung',                'label' => 'IT Anschl.: ',                   'unit' => '',    'cell' => $e_C_2_3rd,  'str_cell' => $e_C_3rd + 10, 'ln_after' => true,  'isnotVorentwurf' => false],

    ['key' => 'ET_Anschlussleistung_W',      'label' => 'Raum Anschlussl. ohne Glz:',    'unit' => 'W',   'cell' => $e_C,        'str_cell' => $e_C_3rd + 10, 'ln_after' => false, 'isnotVorentwurf' => false],
    ['key' => 'ET_Anschlussleistung_AV_W',   'label' => 'AV(Rauml.): ',                  'unit' => 'W',   'cell' => $e_C_2_3rd,  'str_cell' => $e_C_3rd + 10, 'ln_after' => false, 'isnotVorentwurf' => false],
    ['key' => 'ET_Anschlussleistung_SV_W',   'label' => 'SV(Rauml.): ',                  'unit' => 'W',   'cell' => $e_C_2_3rd,  'str_cell' => $e_C_3rd + 10, 'ln_after' => false, 'isnotVorentwurf' => false],
    ['key' => 'ET_Anschlussleistung_ZSV_W',  'label' => 'ZSV(Rauml.): ',                 'unit' => 'W',   'cell' => $e_C_2_3rd,  'str_cell' => $e_C_3rd + 10, 'ln_after' => false, 'isnotVorentwurf' => false],
    ['key' => 'ET_Anschlussleistung_USV_W',  'label' => 'USV(Rauml.): ',                 'unit' => 'W',   'cell' => $e_C_2_3rd,  'str_cell' => $e_C_3rd + 10, 'ln_after' => false, 'isnotVorentwurf' => false],
    ['key' => 'ET_RJ45-Ports',               'label' => 'RJ45-Ports: ',                  'unit' => 'Stk', 'cell' => $e_C_2_3rd,  'str_cell' => $e_C_3rd,      'ln_after' => true,  'isnotVorentwurf' => true],

    ['key' => 'RaumAnschlussLeistungInklGlz','ln_after' => false,                                                                                                                   'isnotVorentwurf' => true],

    ['key' => 'EL_Laser 16A CEE Stk',        'label' => 'CEE16A Laser: ',                'unit' => 'Stk', 'cell' => $e_C,        'str_cell' => $e_C_3rd + 10, 'ln_after' => false, 'isnotVorentwurf' => true],
    ['key' => 'EL_Roentgen 16A CEE Stk',     'label' => 'CEE16A Röntgen',               'unit' => 'Stk', 'cell' => $e_C_2_3rd,  'str_cell' => $e_C_3rd,      'ln_after' => true,  'isnotVorentwurf' => true],
];

$haustechnikParams = [
    ['key' => 'H6020',                                    'label' => 'H6020: ',                             'unit' => '',    'cell' => $e_C_2_3rd,       'str_cell' => $e_C_2_3rd,  'ln_after' => false],
    ['key' => 'HT_Abluft_Digestorium_Stk',                'label' => 'Abluft Digestor/Werkbank:',           'unit' => 'Stk', 'cell' => $e_C,             'str_cell' => $e_C_3rd,    'ln_after' => false],
    ['key' => 'HT_Abluft_Sicherheitsschrank_Stk',         'label' => 'Abluft Sicherheitsschrank:',          'unit' => 'Stk', 'cell' => $e_C,             'str_cell' => $e_C_3rd,    'ln_after' => false],
    ['key' => 'HT_Abluft_Sicherheitsschrank_Unterbau_Stk','label' => 'Abluft Sicherheitsschrank Unterbau:','unit' => 'Stk', 'cell' => $e_C + $e_C_3rd, 'str_cell' => $e_C_3rd,    'ln_after' => false],
    ['key' => 'HT_Punktabsaugung_Stk',                    'label' => 'Punktabsaugung:',                     'unit' => 'Stk', 'cell' => $e_C,             'str_cell' => $e_C_3rd,    'ln_after' => true],
    ['key' => 'HT_Waermeabgabe_W',                        'label' => 'Abwärme MT: ',                       'unit' => '',    'cell' => $e_C_2_3rd,       'str_cell' => $e_C_2_3rd,  'ln_after' => false],
    ['key' => 'VE_Wasser',                                'label' => 'VE Wasser:',                          'unit' => '',    'cell' => $e_C_2_3rd,       'str_cell' => $e_C_2_3rd,  'ln_after' => false],
    ['key' => 'HT_Notdusche',                             'label' => 'Notdusche:',                          'unit' => '',    'cell' => $e_C_2_3rd,       'str_cell' => $e_C_2_3rd,  'ln_after' => false],
    ['key' => 'HT_Raumtemp Sommer °C',                    'label' => 'Max. Raumtemp.',                     'unit' => '°C',  'cell' => $e_C_2_3rd,       'str_cell' => $e_C,        'ln_after' => false],
    ['key' => 'HT_Raumtemp Winter °C',                    'label' => 'Min. Raumtemp.',                     'unit' => '°C',  'cell' => $e_C_2_3rd,       'str_cell' => $e_C_2_3rd,  'ln_after' => false],
];

$allgemeinParams = [
    // type 'str'     → multicell_with_str (plain text)
    // type 'str_fb'  → multicell_with_str with ' - ' fallback when empty; also tracks height for extra spacer
    // type 'strahlen'→ strahlenanw renderer; str_cell_wide used when text overflows
    // type 'hackerl' → hackerlA3 checkbox
    // type 'nr'      → multicell_with_nr (numeric + unit)
    ['key' => 'Fussboden OENORM B5220', 'label' => 'Ö NORM B5220: ',   'type' => 'str',     'cell' => $e_C,       'str_cell' => $e_C_3rd + 10,              'unit' => '',   'ln_after' => false],
    ['key' => 'Allgemeine Hygieneklasse','label' => 'Hygieneklasse: ',  'type' => 'str_fb',  'cell' => $e_C_2_3rd, 'str_cell' => $e_C_3rd * 4,               'unit' => '',   'ln_after' => false],
    ['key' => 'Strahlenanwendung',       'label' => 'Strahlenanw.: ',   'type' => 'strahlen','cell' => $e_C_2_3rd, 'str_cell' => $e_C_3rd + 10, 'str_cell_wide' => $e_C_3rd * 4, 'ln_after' => false],
    ['key' => 'Laseranwendung',          'label' => 'Laseranw.: ',      'type' => 'hackerl', 'cell' => $e_C_2_3rd, 'str_cell' => $e_C_3rd + 10,              'ln_after' => false],
    ['key' => 'Abdunkelbarkeit',         'label' => 'Abdunkelbarkeit: ','type' => 'hackerl', 'cell' => $e_C_2_3rd, 'str_cell' => $e_C_3rd + 10,              'ln_after' => false],
    ['key' => 'Nutzfläche',              'label' => 'Fläche: ',         'type' => 'nr',      'cell' => $e_C_2_3rd, 'str_cell' => $e_C_3rd * 4,               'unit' => 'm2', 'ln_after' => false],
];

// MedGas: all items are hackerl checkboxes; ln_after inserts a row-break (used between Kreis 1 and Kreis 2)
$medgasParams = [
    ['key' => '1 Kreis O2',  'label' => '1 Kreis   O2: ', 'cell' => $e_C_2_3rd, 'str_cell' => $e_C_3rd, 'ln_after' => false],
    ['key' => '1 Kreis Va',  'label' => '1 Kreis   Va: ', 'cell' => $e_C_2_3rd, 'str_cell' => $e_C_3rd, 'ln_after' => false],
    ['key' => '1 Kreis DL-5','label' => '1 Kreis   DL5: ','cell' => $e_C_2_3rd, 'str_cell' => $e_C_3rd, 'ln_after' => false],
    ['key' => 'NGA',         'label' => 'NGA: ',           'cell' => $e_C_2_3rd, 'str_cell' => $e_C_3rd, 'ln_after' => false],
    ['key' => 'N2O',         'label' => 'N2O: ',           'cell' => $e_C_2_3rd, 'str_cell' => $e_C_3rd, 'ln_after' => false],
    ['key' => 'CO2',         'label' => 'CO2: ',           'cell' => $e_C_2_3rd, 'str_cell' => $e_C_3rd, 'ln_after' => false],
    ['key' => 'DL-10',       'label' => 'DL10: ',          'cell' => $e_C_2_3rd, 'str_cell' => $e_C_3rd, 'ln_after' => true],  // row break after Kreis 1 group
    ['key' => '2 Kreis O2',  'label' => '2 Kreise O2: ',  'cell' => $e_C_2_3rd, 'str_cell' => $e_C_3rd, 'ln_after' => false],
    ['key' => '2 Kreis Va',  'label' => '2 Kreise Va: ',  'cell' => $e_C_2_3rd, 'str_cell' => $e_C_3rd, 'ln_after' => false],
    ['key' => '2 Kreis DL-5','label' => '2 Kreise DL5: ', 'cell' => $e_C_2_3rd, 'str_cell' => $e_C_3rd, 'ln_after' => false],
    ['key' => 'DL-tech',     'label' => 'DL-tech: ',       'cell' => $e_C_2_3rd, 'str_cell' => $e_C_3rd, 'ln_after' => false],
];


// ---------------------------------------------------------------------------
// PDF SETUP
// ---------------------------------------------------------------------------

/** @noinspection PhpUndefinedConstantInspection */
$pdf = new MYPDF('L', PDF_UNIT, 'A3', true, 'UTF-8', false, true);
$pdf = init_pdf_attributes($pdf, PDF_MARGIN_LEFT, $marginTop, $marginBTM, 'A3', 'Bauangaben');
$pdf->AddPage('L', 'A3');
$pdf->SetFillColor(0, 0, 0, 0);
$pdf->SetFont('helvetica', '', $font_size);
$pdf->SetLineStyle($style_normal);


// ---------------------------------------------------------------------------
// MAIN LOOP
// ---------------------------------------------------------------------------

$roomIDs      = filter_input(INPUT_GET, 'roomID');
$roomIDsArray = array_map('intval', explode(',', $roomIDs));
$changeDate   = getValidatedDateFromURL();
$projectId    = (int)$_SESSION['projectID'];
$isnotVorentwurf = $_SESSION['projectPlanungsphase'] !== 'Vorentwurf';

$mysqli = utils_connect_sql();

// Fetch all room data in a single query
$allRooms = db_fetch_rooms_batch($mysqli, $roomIDsArray);

foreach ($roomIDsArray as $roomId) {
    // --- Change detection for room-level fields ---
    $rawRoomChanges = db_fetch_room_changes($mysqli, $roomId, $changeDate);
    $roomFieldChanges = detect_room_field_changes($rawRoomChanges, $mapping, $mp2);

    $row = $allRooms[$roomId] ?? null;
    if ($row === null) {
        continue; // Room not found (shouldn't happen)
    }

    // --- Room header ---
    $pdf->Ln(8);
    $pdf->SetFillColor(255, 255, 255);
    raum_header(
        $pdf, $horizontalSpacerLN3, $SB,
        $row['Raumbezeichnung'], $row['Raumnr'],
        $row['Raumbereich Nutzer'], $row['Geschoss'],
        $row['Bauetappe'], $row['Bauabschnitt'],
        'A3', $roomFieldChanges
    );

    // --- Content blocks ---
    render_bo_beschreibung($pdf, $row, $block_header_w, $block_header_height, $SB, $horizontalSpacerLN);

    render_allgemein_block(
        $pdf, $row, $allgemeinParams, $roomFieldChanges, $font_size,
        $block_header_w, $block_header_height, $SB,
        $horizontalSpacerLN3
    );

    render_elektro_block(
        $pdf, $row, $elektroParams, $roomFieldChanges, $isnotVorentwurf,
        $font_size, $block_header_w, $block_header_height, $SB,
        $horizontalSpacerLN, $horizontalSpacerLN2
    );

    render_haustechnik_block(
        $pdf, $row, $haustechnikParams, $roomFieldChanges,
        $font_size, $block_header_w, $block_header_height, $SB,
        $horizontalSpacerLN, $horizontalSpacerLN2
    );

    render_medgas_block(
        $pdf, $row, $medgasParams, $roomFieldChanges, $font_size,
        $block_header_w, $block_header_height, $SB,
        $horizontalSpacerLN, $horizontalSpacerLN2
    );

    render_baustatik_block($pdf, $row, $block_header_w, $block_header_height, $SB, $horizontalSpacerLN);

    render_mt_block(
        $pdf, $mysqli, $roomId, $isnotVorentwurf, $projectId, $changeDate,
        $block_header_w, $block_header_height, $SB, $SH,
        $style_normal, $style_dashed
    );
}

$mysqli->close();
ob_end_clean();
$pdf->Output(getFileName('BAUANGABEN'), 'I');