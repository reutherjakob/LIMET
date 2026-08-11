<?php
require_once '../utils/_utils.php';
check_login();

/**
 * ============================================================================
 *  SCHNELLE MT-DETAIL-TABELLE
 * ============================================================================
 *
 * Unterschied zur bisherigen make_MT_details_table():
 *  - Erwartet EINMALIG (projektweit, vor der Raumschleife) geladene Arrays
 *    statt mysqli-Results, die pro Raum neu abgefragt wurden.
 *  - Zellenwerte werden per O(1)-Hash-Lookup gefunden statt per linearer
 *    Suche durch alle Projekt-Elementparameter.
 *  - Änderungsmarkierung ebenfalls per O(1)-Lookup; leeres Array übergeben
 *    => keine Markierung (Variante "ohne Änderungsmarkierungen").
 *
 * Erwartete Datenstrukturen (Aufbau siehe Hauptskript):
 *
 *  $paramDefs      Geordnete Liste von Parameterdefinitionen:
 *                  [ ['ParamID'=>.., 'KategorieID'=>.., 'Bezeichnung'=>Abk.,
 *                     'Kategorie'=>..], ... ]
 *
 *  $valueLookup    [elementID][variantenID][paramID]
 *                      => ['Wert' => .., 'Einheit' => ..]
 *
 *  $changedLookup  [elementID][paramID] => true   (optional, default leer)
 *
 * Alle Funktionen hier tragen den Präfix mtf_ bzw. den Suffix _fast, damit
 * die Datei parallel zur alten pdf_createMTTabelle.php eingebunden werden
 * kann, ohne Redeklarations-Fehler auszulösen.
 * ============================================================================
 */


// ---------------------------------------------------------------------------
// HELPERS
// ---------------------------------------------------------------------------

/**
 * Normalisiert bestimmte Einheiten-Strings für die Anzeige.
 */
function mtf_checkAndManipulateString(string $input): string
{
    if ($input === "''" || $input === '"') {
        return '"';
    }
    if (str_contains($input, '/min')) {
        return ' ' . $input;
    }
    return $input;
}

/**
 * Abkürzungsverzeichnis unterhalb der Tabelle ausgeben.
 */
function mtf_abk_vz(array $paramInfos, $pdf, float $f_size): void
{
    if (empty($paramInfos)) {
        return;
    }

    $pdf->MultiCell(20, $f_size, 'Abkürzungen: ', 0, 'L', 0, 0, '', '', true, 0, false, false, 0);

    foreach ($paramInfos as $entry) {
        $label = $entry['Bezeichnung'];

        // "Label-" (fett)
        $w = $pdf->GetStringWidth($label . '-', 'courier', 'B', $f_size);
        if (($pdf->GetX() + $w) >= 400) {
            $pdf->Ln($f_size / 2);
        }
        $pdf->SetFont('courier', 'B', $f_size);
        $pdf->MultiCell($w + 3, $f_size, $label . '-', 0, 'R', 0, 0, '', '', true, 0, false, false, 0);

        // "Label;" (normal)
        $w = $pdf->GetStringWidth($label . ';', 'courier', '', $f_size);
        if (($pdf->GetX() + $w) >= 400) {
            $pdf->Ln($f_size / 2);
        }
        $pdf->SetFont('courier', '', $f_size);
        $pdf->MultiCell($w + 3, $f_size, $label . ';', 0, 'L', 0, 0, '', '', true, 0, false, false, 0);
    }

    $pdf->SetFont('courier', 'B', $f_size);
}


// ---------------------------------------------------------------------------
// INTERNAL: TABLE HEADER
// ---------------------------------------------------------------------------

/**
 * Zweizeiligen Spaltenkopf rendern (Kategorie-Gruppierung + Abkürzungszeile).
 */
function mtf_render_table_header($pdf, array $fixedSizes, float $paramColWidth, float $rowHeight, array $paramInfos, float $catRowHeight = 6): void
{
    $pdf->SetFont('courier', 'B', 6);

    // WICHTIG: Startposition der Kategoriezeile VOR dem Zeilenumbruch merken.
    // Die Kategorie-Zellen werden später per SetXY() auf dieser (oberen) Höhe
    // gezeichnet, während die Abkürzungszeile eine Zeile tiefer läuft.
    $catHeaderX   = $pdf->GetX();
    $catHeaderY   = $pdf->GetY();
    $lastCategory = '';

    // Eigene Zeile für die Kategorien reservieren (Geometrie, Statik, Elektro, ...)
    $pdf->Ln($catRowHeight);

    // --- Fixe Spalten (Abkürzungszeile) ---
    $fixedLabels = ['ID', 'Element', 'Var', 'Stk', 'Bestand', 'Ort', 'Verw.'];
    foreach ($fixedLabels as $i => $label) {
        $pdf->MultiCell($fixedSizes[$i], $rowHeight, $label, 1, 'C', 0, 0);
    }

    // --- Parameterspalten + Kategoriegruppen ---
    foreach ($paramInfos as $param) {
        if ($lastCategory !== $param['Kategorie']) {
            $curX = $pdf->GetX();
            $curY = $pdf->GetY();
            $pdf->SetXY($catHeaderX, $catHeaderY);
            $pdf->MultiCell($curX - $catHeaderX, $rowHeight, 'MT ' . $lastCategory, 1, 'C', 0, 0);
            $catHeaderX = $pdf->GetX();
            $catHeaderY = $pdf->GetY();
            $lastCategory = $param['Kategorie'];
            $pdf->SetXY($curX, $curY);
        }
        $pdf->MultiCell($paramColWidth, $rowHeight, $param['Bezeichnung'], 1, 'C', 0, 0);
    }

    // Letzte Kategoriegruppe schließen
    $curX = $pdf->GetX();
    $curY = $pdf->GetY();
    $pdf->SetXY($catHeaderX, $catHeaderY);
    $pdf->MultiCell($curX - $catHeaderX, $rowHeight, $lastCategory, 1, 'C', 0, 0);
    $pdf->SetXY($curX, $curY);

    $pdf->Ln($rowHeight);
}


// ---------------------------------------------------------------------------
// PUBLIC: MAIN TABLE RENDERER (FAST)
// ---------------------------------------------------------------------------

/**
 * MT-Detail-Tabelle für einen Raum rendern.
 *
 * @param $pdf                MYPDF-Instanz
 * @param $result             Raum-Elemente-Resultset (tabelle_räume_has_tabelle_elemente)
 * @param array $paramDefs    Projektweite Parameterdefinitionen (geordnet)
 * @param array $valueLookup  [elementID][variantenID][paramID] => ['Wert','Einheit']
 * @param int   $SB           Nutzbare Seitenbreite
 * @param int   $SH           Nutzbare Seitenhöhe
 * @param array $changedLookup [elementID][paramID] => true; leer => keine Markierung
 */
function make_MT_details_table_fast($pdf, $result, array $paramDefs, array $valueLookup, int $SB, int $SH, array $changedLookup = []): void
{
    // ------------------------------------------------------------------
    // 1.  Element-IDs des Raums einsammeln
    // ------------------------------------------------------------------
    $result->data_seek(0);
    $roomElementIDs = [];
    while ($row = $result->fetch_assoc()) {
        $roomElementIDs[$row['TABELLE_Elemente_idTABELLE_Elemente']] = true;
    }

    // ------------------------------------------------------------------
    // 2.  Nur Parameter anzeigen, die in diesem Raum Werte haben
    //     (O(1)-Zugriff über den Lookup statt Filterung aller Projektwerte)
    // ------------------------------------------------------------------
    $activeParamIDs = [];
    foreach ($roomElementIDs as $elementId => $unused) {
        if (!isset($valueLookup[$elementId])) {
            continue;
        }
        foreach ($valueLookup[$elementId] as $variantValues) {
            foreach ($variantValues as $paramId => $cell) {
                if (trim((string)$cell['Wert']) !== '') {
                    $activeParamIDs[$paramId] = true;
                }
            }
        }
    }

    $paramInfos = array_values(array_filter(
        $paramDefs,
        fn($p) => isset($activeParamIDs[$p['ParamID']])
    ));

    // ------------------------------------------------------------------
    // 3.  Spaltenbreiten berechnen
    // ------------------------------------------------------------------
    $fixedSizes    = [15, 42, 7, 7, 11, 7, 9];
    $paramCount    = count($paramInfos);
    $paramColWidth = $paramCount > 0
        ? ($SB - array_sum($fixedSizes)) / $paramCount
        : 0;

    $rowHeight     = 5;
    $rowHeightData = 7;
    $f_size        = 6;

    // ------------------------------------------------------------------
    // 4.  Erster Tabellenkopf
    // ------------------------------------------------------------------
    $pdf->SetFillColor(244, 244, 244);
    $pdf->SetTextColor(0, 5, 0);
    // Kein Ln() mehr davor: die Header-Funktion reserviert die Kategoriezeile
    // selbst (Höhe = $f_size, wie im Original)
    mtf_render_table_header($pdf, $fixedSizes, $paramColWidth, $rowHeight, $paramInfos, $f_size);

    // ------------------------------------------------------------------
    // 5.  Datenzeilen
    // ------------------------------------------------------------------
    $markChanges = !empty($changedLookup);
    $isEvenRow   = 0;
    $result->data_seek(0);

    while ($row = $result->fetch_assoc()) {

        // Seitenumbruch prüfen
        if ($pdf->GetY() >= $SH - 5) {
            mtf_abk_vz($paramInfos, $pdf, $f_size);
            $pdf->AddPage('L', 'A3');
            // Header-Funktion reserviert die Kategoriezeile selbst
            // (Höhe = $rowHeight, wie im Original nach Seitenumbruch)
            mtf_render_table_header($pdf, $fixedSizes, $paramColWidth, $rowHeight, $paramInfos, $rowHeight);
        }

        // Alternierender Zeilenhintergrund
        $isEvenRow = ($isEvenRow + 1) % 2;
        $bgR = $isEvenRow === 0 ? 240 : 255;
        $bgG = $isEvenRow === 0 ? 240 : 255;
        $bgB = $isEvenRow === 0 ? 235 : 255;
        $pdf->SetFillColor($bgR, $bgG, $bgB);

        // Fixe Spalten
        $pdf->SetFont('courier', '', $f_size);
        $pdf->MultiCell($fixedSizes[0], $rowHeightData, $row['ElementID'],     1, 'C', true, 0);
        $pdf->MultiCell($fixedSizes[1], $rowHeightData, $row['Bezeichnung'],   1, 'C', true, 0);
        $pdf->MultiCell($fixedSizes[2], $rowHeightData, $row['Variante'],      1, 'C', true, 0);
        $pdf->MultiCell($fixedSizes[3], $rowHeightData, $row['SummevonAnzahl'],1, 'C', true, 0);
        $pdf->MultiCell($fixedSizes[4], $rowHeightData, $row['Neu/Bestand'] == 1 ? 'Nein' : 'Ja', 1, 'C', true, 0);
        $pdf->MultiCell($fixedSizes[5], $rowHeightData, $row['Standort'],      1, 'C', true, 0);
        $pdf->MultiCell($fixedSizes[6], $rowHeightData, $row['Verwendung'],    1, 'C', true, 0);

        // Parameterspalten: O(1)-Lookup statt linearer Suche
        $elementId = $row['TABELLE_Elemente_idTABELLE_Elemente'];
        $variantId = $row['tabelle_Varianten_idtabelle_Varianten'];
        $rowValues = $valueLookup[$elementId][$variantId] ?? [];

        $widthOverflow = 0; // Ausgleich, wenn eine Zelle verbreitert werden musste

        foreach ($paramInfos as $param) {
            $paramId     = $param['ParamID'];
            $outputValue = '';
            $isChanged   = false;

            if (isset($rowValues[$paramId])) {
                $cell        = $rowValues[$paramId];
                $outputValue = $cell['Wert'] . mtf_checkAndManipulateString((string)$cell['Einheit']);
                if ($markChanges) {
                    $isChanged = isset($changedLookup[$elementId][$paramId]);
                }
            }

            // Zelle bei Bedarf verbreitern, damit der Text in die Zeilenhöhe passt
            $cellWidth = $paramColWidth + $widthOverflow;
            if ($outputValue !== '') {
                while ($pdf->getStringHeight($cellWidth, $outputValue, false, false, '', 1) > $rowHeightData) {
                    $cellWidth++;
                }
            }

            if ($isChanged) {
                $pdf->SetFillColor(220, 235, 190);
            }

            $pdf->MultiCell($cellWidth, $rowHeightData, $outputValue, 1, 'C', true, 0);
            $pdf->SetFillColor($bgR, $bgG, $bgB);
            $widthOverflow = ($cellWidth > $paramColWidth) ? ($paramColWidth - $cellWidth) : 0;
        }

        $pdf->Ln();
    }

    mtf_abk_vz($paramInfos, $pdf, $f_size);
    $pdf->Ln();
}


// ---------------------------------------------------------------------------
// PUBLIC: LOOKUP-BUILDER (einmalig vor der Raumschleife aufrufen)
// ---------------------------------------------------------------------------

/**
 * Lädt projektweit alle Parameterdefinitionen und Elementparameter-Werte
 * in indexierte Arrays. Genau EINMAL vor der Raumschleife aufrufen.
 *
 * @return array{0: array, 1: array}  [$paramDefs, $valueLookup]
 */
function mtf_load_project_lookups(mysqli $mysqli, int $projectId): array
{
    // --- Parameterdefinitionen (Kopfzeile) ---
    $sql = "SELECT tabelle_parameter_kategorie.Kategorie,
                   tabelle_parameter.Abkuerzung,
                   tabelle_parameter.Bezeichnung,
                   tabelle_parameter.idTABELLE_Parameter,
                   tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie
            FROM tabelle_parameter_kategorie
            INNER JOIN (tabelle_parameter
                INNER JOIN tabelle_projekt_elementparameter
                    ON tabelle_parameter.idTABELLE_Parameter = tabelle_projekt_elementparameter.tabelle_parameter_idTABELLE_Parameter)
                ON tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie = tabelle_parameter.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie
            WHERE ((tabelle_projekt_elementparameter.tabelle_projekte_idTABELLE_Projekte = " . $projectId . ")
                AND (tabelle_parameter.`Bauangaben relevant` = 1)
                AND NOT (tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie = 18))
            GROUP BY tabelle_parameter_kategorie.Kategorie, tabelle_parameter.Bezeichnung
            ORDER BY tabelle_parameter_kategorie.Kategorie, tabelle_parameter.Bezeichnung;";

    $paramDefs = [];
    $res = $mysqli->query($sql);
    while ($row = $res->fetch_assoc()) {
        $paramDefs[] = [
            'ParamID'     => $row['idTABELLE_Parameter'],
            'KategorieID' => $row['idTABELLE_Parameter_Kategorie'],
            'Bezeichnung' => $row['Abkuerzung'],
            'Kategorie'   => $row['Kategorie'],
        ];
    }
    $res->free();

    // --- Elementparameter-Werte (Zellen) ---
    $sql = "SELECT tabelle_projekt_elementparameter.Wert,
                   tabelle_projekt_elementparameter.Einheit,
                   tabelle_projekt_elementparameter.tabelle_Varianten_idtabelle_Varianten,
                   tabelle_projekt_elementparameter.tabelle_elemente_idTABELLE_Elemente,
                   tabelle_parameter.idTABELLE_Parameter
            FROM tabelle_parameter_kategorie
            INNER JOIN (tabelle_parameter
                INNER JOIN tabelle_projekt_elementparameter
                    ON tabelle_parameter.idTABELLE_Parameter = tabelle_projekt_elementparameter.tabelle_parameter_idTABELLE_Parameter)
                ON tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie = tabelle_parameter.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie
            WHERE ((tabelle_projekt_elementparameter.tabelle_projekte_idTABELLE_Projekte = " . $projectId . ")
                AND (tabelle_parameter.`Bauangaben relevant` = 1)
                AND NOT (tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie = 18));";

    $valueLookup = [];
    $res = $mysqli->query($sql);
    while ($row = $res->fetch_assoc()) {
        $valueLookup[$row['tabelle_elemente_idTABELLE_Elemente']]
        [$row['tabelle_Varianten_idtabelle_Varianten']]
        [$row['idTABELLE_Parameter']] = [
            'Wert'    => $row['Wert'],
            'Einheit' => $row['Einheit'],
        ];
    }
    $res->free();

    return [$paramDefs, $valueLookup];
}

/**
 * Lädt das Änderungslog projektweit und indexiert es als
 * [elementID][paramID] => true. Nur aufrufen, wenn Markierung gewünscht ist.
 */
function mtf_load_changed_lookup(mysqli $mysqli, int $projectId, string $aenderungsdatum): array
{
    $sql = "SELECT tabelle_projekt_elementparameter_aenderungen.element,
                   tabelle_projekt_elementparameter_aenderungen.parameter,
                   tabelle_projekt_elementparameter_aenderungen.wert_alt,
                   tabelle_projekt_elementparameter_aenderungen.wert_neu,
                   tabelle_projekt_elementparameter_aenderungen.einheit_alt,
                   tabelle_projekt_elementparameter_aenderungen.einheit_neu,
                   tabelle_projekt_elementparameter_aenderungen.variante,
                   tabelle_projekt_elementparameter_aenderungen.timestamp
            FROM tabelle_projekt_elementparameter_aenderungen
            WHERE (((tabelle_projekt_elementparameter_aenderungen.projekt) = " . $projectId . "))
              AND tabelle_projekt_elementparameter_aenderungen.timestamp > '" . $mysqli->real_escape_string($aenderungsdatum) . "'
            ORDER BY tabelle_projekt_elementparameter_aenderungen.timestamp DESC;";

    $dataChanges = [];
    $res = $mysqli->query($sql);
    while ($row = $res->fetch_assoc()) {
        $dataChanges[] = $row;
    }
    $res->free();

    // Bestehende Utility weiterverwenden: Einträge entfernen, bei denen alt == neu
    $dataChanges = filter_old_equal_new($dataChanges);

    $changedLookup = [];
    foreach ($dataChanges as $c) {
        $changedLookup[$c['element']][$c['parameter']] = true;
    }
    return $changedLookup;
}