<?php
require_once '../utils/_utils.php';
check_login();


// ---------------------------------------------------------------------------
// HELPERS
// ---------------------------------------------------------------------------

/**
 * Check whether a given element+parameter combination exists in the change log.
 */
function checkEntry(array $dataChanges, int $elementId, int $parameterId): bool
{
    foreach ($dataChanges as $entry) {
        if ($entry['element'] == $elementId && $entry['parameter'] == $parameterId) {
            return true;
        }
    }
    return false;
}

/**
 * Normalise certain unit strings for display.
 */
function checkAndManipulateString(string $input): string
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
 * Print the abbreviation legend below a table.
 */
function abk_vz(array $paramInfos, $pdf, float $f_size): void
{
    if (empty($paramInfos)) {
        return;
    }

    $pdf->MultiCell(20, $f_size, 'Abkürzungen: ', 0, 'L', 0, 0, '', '', true, 0, false, false, 0);

    foreach ($paramInfos as $entry) {
        $label = $entry['Bezeichnung'];

        // "Label-" part (bold)
        $w = $pdf->GetStringWidth($label . '-', 'courier', 'B', $f_size);
        if (($pdf->GetX() + $w) >= 400) {
            $pdf->Ln($f_size / 2);
        }
        $pdf->SetFont('courier', 'B', $f_size);
        $pdf->MultiCell($w + 3, $f_size, $label . '-', 0, 'R', 0, 0, '', '', true, 0, false, false, 0);

        // "Label;" part (normal)
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
 * Render the two-row column header (category grouping + abbreviation row).
 *
 * @param array  $fixedSizes      Widths for the fixed left columns.
 * @param float  $paramColWidth   Width of each parameter column.
 * @param float  $rowHeight       Row height.
 * @param array  $paramInfos      Ordered list of parameter info arrays.
 */
function render_mt_table_header($pdf, array $fixedSizes, float $paramColWidth, float $rowHeight, array $paramInfos): void
{
    $pdf->SetFont('courier', 'B', 6);

    // Remember starting position for the category-group header row above
    $catHeaderX = $pdf->GetX();
    $catHeaderY = $pdf->GetY();
    $lastCategory = '';

    // --- Fixed column labels ---
    $fixedLabels = ['ID', 'Element', 'Var', 'Stk', 'Bestand', 'Ort', 'Verw.'];
    foreach ($fixedLabels as $i => $label) {
        $pdf->MultiCell($fixedSizes[$i], $rowHeight, $label, 1, 'C', 0, 0);
    }

    // --- Parameter column labels + category grouping ---
    foreach ($paramInfos as $param) {
        if ($lastCategory !== $param['Kategorie']) {
            // Close previous category group cell
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

    // Close last category group cell
    $curX = $pdf->GetX();
    $curY = $pdf->GetY();
    $pdf->SetXY($catHeaderX, $catHeaderY);
    $pdf->MultiCell($curX - $catHeaderX, $rowHeight, $lastCategory, 1, 'C', 0, 0);
    $pdf->SetXY($curX, $curY);

    $pdf->Ln($rowHeight);
}


// ---------------------------------------------------------------------------
// PUBLIC: MAIN TABLE RENDERER
// ---------------------------------------------------------------------------

/**
 * Render the full MT details table for a room.
 *
 * @param $pdf           MYPDF instance
 * @param $result        Room elements result set  (from tabelle_räume_has_tabelle_elemente)
 * @param $result1       Parameter definitions     (abbreviations / categories)
 * @param $result3       Element parameter values
 * @param int    $SB     Usable page width
 * @param int    $SH     Usable page height
 * @param array  $dataChanges  Change log entries
 */
function make_MT_details_table($pdf, $result, $result1, $result3, int $SB, int $SH, array $dataChanges): void
{
    // ------------------------------------------------------------------
    // 1.  Load element parameter values into a lookup array
    // ------------------------------------------------------------------
    $elementParamValues = [];
    while ($row = $result3->fetch_assoc()) {
        $elementParamValues[] = [
            'KategorieID' => $row['idTABELLE_Parameter_Kategorie'],
            'ParamID'     => $row['idTABELLE_Parameter'],
            'elementID'   => $row['tabelle_elemente_idTABELLE_Elemente'],
            'variantenID' => $row['tabelle_Varianten_idtabelle_Varianten'],
            'Wert'        => $row['Wert'],
            'Einheit'     => $row['Einheit'],
        ];
    }

    // ------------------------------------------------------------------
    // 2.  Load parameter definitions (header metadata)
    // ------------------------------------------------------------------
    $allParamInfos = [];
    while ($row = $result1->fetch_assoc()) {
        $id = $row['idTABELLE_Parameter'];
        $allParamInfos[$id] = [
            'ParamID'     => $id,
            'KategorieID' => $row['idTABELLE_Parameter_Kategorie'],
            'Bezeichnung' => $row['Abkuerzung'],
            'Kategorie'   => $row['Kategorie'],
        ];
    }

    // ------------------------------------------------------------------
    // 3.  Only show parameters that have values in this room's elements
    // ------------------------------------------------------------------
    $result->data_seek(0);
    $roomElementIDs = [];
    while ($row = $result->fetch_assoc()) {
        $roomElementIDs[] = $row['TABELLE_Elemente_idTABELLE_Elemente'];
    }

    $activeParamIDs = [];
    foreach ($elementParamValues as $ep) {
        if (in_array($ep['elementID'], $roomElementIDs, true) && trim($ep['Wert']) !== '') {
            $activeParamIDs[$ep['ParamID']] = true;
        }
    }

    $paramInfos = array_values(array_filter(
        $allParamInfos,
        fn($p) => isset($activeParamIDs[$p['ParamID']])
    ));

    // ------------------------------------------------------------------
    // 4.  Compute column widths
    // ------------------------------------------------------------------
    $fixedSizes = [15, 42, 7, 7, 11, 7, 9];
    $paramCount = count($paramInfos);
    $paramColWidth = $paramCount > 0
        ? ($SB - array_sum($fixedSizes)) / $paramCount
        : 0;

    $rowHeight         = 5;
    $rowHeightData     = 7;
    $f_size            = 6;

    // ------------------------------------------------------------------
    // 5.  First header
    // ------------------------------------------------------------------
    $pdf->SetFillColor(244, 244, 244);
    $pdf->SetTextColor(0, 5, 0);
    $pdf->Ln($f_size);
    render_mt_table_header($pdf, $fixedSizes, $paramColWidth, $rowHeight, $paramInfos);

    // ------------------------------------------------------------------
    // 6.  Data rows
    // ------------------------------------------------------------------
    $isEvenRow = 0;
    $result->data_seek(0);

    while ($row = $result->fetch_assoc()) {

        // Page break check
        if ($pdf->GetY() >= $SH - 5) {
            abk_vz($paramInfos, $pdf, $f_size);
            $pdf->AddPage('L', 'A3');
            $pdf->SetFont('courier', 'B', $f_size);
            $pdf->Ln($rowHeight);
            render_mt_table_header($pdf, $fixedSizes, $paramColWidth, $rowHeight, $paramInfos);
        }

        // Alternating row background
        $isEvenRow = ($isEvenRow + 1) % 2;
        $pdf->SetFillColor($isEvenRow === 0 ? 240 : 255, $isEvenRow === 0 ? 240 : 255, $isEvenRow === 0 ? 235 : 255);

        // Fixed columns
        $pdf->SetFont('courier', '', $f_size);
        $pdf->MultiCell($fixedSizes[0], $rowHeightData, $row['ElementID'],         1, 'C', true, 0);
        $pdf->MultiCell($fixedSizes[1], $rowHeightData, $row['Bezeichnung'],        1, 'C', true, 0);
        $pdf->MultiCell($fixedSizes[2], $rowHeightData, $row['Variante'],           1, 'C', true, 0);
        $pdf->MultiCell($fixedSizes[3], $rowHeightData, $row['SummevonAnzahl'],     1, 'C', true, 0);
        $pdf->MultiCell($fixedSizes[4], $rowHeightData, $row['Neu/Bestand'] == 1 ? 'Nein' : 'Ja', 1, 'C', true, 0);
        $pdf->MultiCell($fixedSizes[5], $rowHeightData, $row['Standort'],           1, 'C', true, 0);
        $pdf->MultiCell($fixedSizes[6], $rowHeightData, $row['Verwendung'],         1, 'C', true, 0);

        // Parameter columns
        $widthOverflow = 0; // tracks extra width needed for long cell content
        foreach ($paramInfos as $param) {
            $paramId  = $param['ParamID'];
            $outputValue = '';
            $isChanged   = false;

            foreach ($elementParamValues as $ep) {
                if (
                    $ep['ParamID']     == $paramId &&
                    $ep['elementID']   == $row['TABELLE_Elemente_idTABELLE_Elemente'] &&
                    $ep['variantenID'] == $row['tabelle_Varianten_idtabelle_Varianten']
                ) {
                    $outputValue = $ep['Wert'] . checkAndManipulateString($ep['Einheit']);
                    $isChanged   = checkEntry($dataChanges, (int)$ep['elementID'], (int)$ep['ParamID']);
                    break;
                }
            }

            // Expand column width if text overflows row height
            $cellWidth = $paramColWidth + $widthOverflow;
            while ($pdf->getStringHeight($cellWidth, $outputValue, false, false, '', 1) > $rowHeightData) {
                $cellWidth++;
            }

            if ($isChanged) {
                $pdf->SetFillColor(220, 235, 190);
            }

            $pdf->MultiCell($cellWidth, $rowHeightData, $outputValue, 1, 'C', true, 0);
            $pdf->SetFillColor($isEvenRow === 0 ? 240 : 255, $isEvenRow === 0 ? 240 : 255, $isEvenRow === 0 ? 235 : 255);
            $widthOverflow = ($cellWidth > $paramColWidth) ? ($paramColWidth - $cellWidth) : 0;
        }

        $pdf->Ln();
    }

    abk_vz($paramInfos, $pdf, $f_size);
    $pdf->Ln();
}