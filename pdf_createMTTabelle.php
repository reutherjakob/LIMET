<?php

session_start();
check_login();

function checkEntry($jsonArray, $elementId, $parameterId) {
    foreach ($jsonArray as $entry) {
        if ($entry['element'] == $elementId && $entry['parameter'] == $parameterId) {
            return true;
        }
    }
    return false;
}

function checkAndManipulateString($input) {
    // Check if the string contains '/min'
    if (strpos($input, '/min') !== false) {
        // Add a space in front of the string
        $input = ' ' . $input;
    }
    return $input;
}

function abk_vz($result4, $pdf, $f_size) {
    $result4->data_seek(0);
    while ($row1 = $result4->fetch_assoc()) {
        $text_width = $pdf->GetStringWidth($row1['Abkuerzung'] . "-", 'courier', 'B', $f_size);
        if (($pdf->GetX() + $text_width) >= 400) {
            $pdf->Ln($f_size / 2);
        }
        $pdf->SetFont('courier', 'B', $f_size);
        $pdf->MultiCell($text_width + 3, $f_size, $row1['Abkuerzung'] . "-", 0, 'R', 0, 0, '', '', true, 0, false, false, 0);

        $text_width = $pdf->GetStringWidth($row1['Bezeichnung'] . ";", 'courier', '', $f_size);
        if (( $pdf->GetX() + $text_width) >= 400) {
            $pdf->Ln($f_size / 2);
        }
        $pdf->SetFont('courier', '', $f_size);
        $pdf->MultiCell($text_width + 3, $f_size, $row1['Bezeichnung'] . ";", 0, 'L', 0, 0, '', '', true, 0, false, false, 0);
    } $pdf->SetFont('courier', 'B', $f_size);
}

function make_MT_details_table($pdf, $result, $result1, $result3, $SB, $SH, $dataChanges) {

    // $result4 = Abkürzungen
    // -------------------------Elemente parameter ------------------------- 
    $elementParamInfos = array();
    $elementParamInfosCounter = 0;
    while ($row = $result3->fetch_assoc()) {
        $elementParamInfos[$elementParamInfosCounter]['KategorieID'] = $row['idTABELLE_Parameter_Kategorie'];
        $elementParamInfos[$elementParamInfosCounter]['ParamID'] = $row['idTABELLE_Parameter'];
        $elementParamInfos[$elementParamInfosCounter]['elementID'] = $row['tabelle_elemente_idTABELLE_Elemente'];
        $elementParamInfos[$elementParamInfosCounter]['variantenID'] = $row['tabelle_Varianten_idtabelle_Varianten'];
        $elementParamInfos[$elementParamInfosCounter]['Wert'] = $row['Wert'];
        $elementParamInfos[$elementParamInfosCounter]['Einheit'] = $row['Einheit'];
        $elementParamInfosCounter = $elementParamInfosCounter + 1;
    }

    // -----------------Projekt Elementparameter/Variantenparameter laden----------------------------
    $paramInfos = array();
    $paramInfosCounter = 0;
    while ($row = $result1->fetch_assoc()) {
        $paramInfos[$row['idTABELLE_Parameter']]['ParamID'] = $row['idTABELLE_Parameter'];
        $paramInfos[$row['idTABELLE_Parameter']]['KategorieID'] = $row['idTABELLE_Parameter_Kategorie'];
        $paramInfos[$row['idTABELLE_Parameter']]['Bezeichnung'] = $row['Abkuerzung'];
        $paramInfos[$row['idTABELLE_Parameter']]['Kategorie'] = $row['Kategorie'];
        $paramInfosCounter = $paramInfosCounter + 1;
    }

    /// ---------- SIZE/FORMAT VARIABLES -----------

    $table_column_sizes = array(15, 42, 7, 7, 11);
    $text_width = ($SB - array_sum($table_column_sizes)) / $paramInfosCounter;
    $rowHeight = 5;
    $rowHeightMainLine = 7;
    $f_size = 6;

    $pdf->SetFillColor(244, 244, 244);
    $pdf->SetTextColor(0, 5, 0);
    $pdf->SetFont('courier', 'B', $f_size);
    $lastXCoordinateHeader = $pdf->GetX();
    $lastYCoordinateHeader = $pdf->GetY();
    $lastCategory = "";
    $pdf->Ln($f_size);

    //Titelzeile für Elemente im Raum
    $pdf->MultiCell($table_column_sizes[0], $rowHeight, "ID", 1, 'C', 0, 0);
    $pdf->MultiCell($table_column_sizes[1], $rowHeight, "Element", 1, 'C', 0, 0);
    $pdf->MultiCell($table_column_sizes[2], $rowHeight, "Var", 1, 'C', 0, 0);
    $pdf->MultiCell($table_column_sizes[3], $rowHeight, "Stk", 1, 'C', 0, 0);
    $pdf->MultiCell($table_column_sizes[4], $rowHeight, "Bestand", 1, 'C', 0, 0);

    // Kopfzeile der Tabelle ausgeben
    foreach ($paramInfos as $array) {
        if ($lastCategory != $array['Kategorie']) {
            $lastXCoordinate = $pdf->GetX();
            $lastYCoordinate = $pdf->GetY();
            $pdf->SetXY($lastXCoordinateHeader, $lastYCoordinateHeader);
            $pdf->MultiCell($lastXCoordinate - $lastXCoordinateHeader, $rowHeight, "MT " . $lastCategory . "", 1, 'C', 0, 0); // . $lastXCoordinate . " " . $lastXCoordinateHeader . " "
            $lastXCoordinateHeader = $pdf->GetX();
            $lastYCoordinateHeader = $pdf->GetY();
            $lastCategory = $array['Kategorie'];
            $pdf->SetXY($lastXCoordinate, $lastYCoordinate);
        }
        $Bezeichnung = $array['Bezeichnung'];
        $pdf->MultiCell($text_width, $rowHeight, $Bezeichnung . "", 1, 'C', 0, 0);
    }

    $lastXCoordinate = $pdf->GetX();
    $lastYCoordinate = $pdf->GetY();
    $pdf->SetXY($lastXCoordinateHeader, $lastYCoordinateHeader);

    $pdf->MultiCell($lastXCoordinate - $lastXCoordinateHeader, $rowHeight, $lastCategory, 1, 'C', 0, 0);

    $pdf->SetXY($lastXCoordinate, $lastYCoordinate);
    $pdf->Ln($rowHeight);
//    $pdf->SetFont('courier', '', $f_size);

    $is_even_row = 0;

    $result->data_seek(0);
    while ($row = $result->fetch_assoc()) {
        //---------------- Prüfen ob Seitenende---------------------------------------------------------
        $y = $pdf->GetY();
        if ($y >= $SH - 5) {
            abk_vz($result1, $pdf, $f_size);
            $pdf->AddPage('L', 'A3');
            $lastXCoordinateHeader = $pdf->GetX();
            $lastYCoordinateHeader = $pdf->GetY();
            $lastCategory = "";
            $pdf->Ln($rowHeight);

            $pdf->SetFont('courier', 'B', $f_size);
            //Titelzeile für Elemente im Raum
            $pdf->MultiCell($table_column_sizes[0], $rowHeight, "ID", 1, 'C', 0, 0);
            $pdf->MultiCell($table_column_sizes[1], $rowHeight, "Element", 1, 'C', 0, 0);
            $pdf->MultiCell($table_column_sizes[2], $rowHeight, "Var", 1, 'C', 0, 0);
            $pdf->MultiCell($table_column_sizes[3], $rowHeight, "Stk", 1, 'C', 0, 0);
            $pdf->MultiCell($table_column_sizes[4], $rowHeight, "Bestand", 1, 'C', 0, 0);

            // Kopfzeile der Tabelle ausgeben
            foreach ($paramInfos as $array) {
                if ($lastCategory != $array['Kategorie']) {
                    $lastXCoordinate = $pdf->GetX();
                    $lastYCoordinate = $pdf->GetY();
                    $pdf->SetXY($lastXCoordinateHeader, $lastYCoordinateHeader);
                    $pdf->MultiCell($lastXCoordinate - $lastXCoordinateHeader, $rowHeight, "MT " . $lastCategory, 1, 'C', 0, 0);
                    $lastXCoordinateHeader = $pdf->GetX();
                    $lastYCoordinateHeader = $pdf->GetY();
                    $lastCategory = $array['Kategorie'];
                    $pdf->SetXY($lastXCoordinate, $lastYCoordinate);
                }
                $tmp_txt = $array['Bezeichnung'];
                $pdf->MultiCell($text_width, $rowHeight, $tmp_txt . "", 1, 'C', 0, 0);
            }
            $lastXCoordinate = $pdf->GetX();
            $lastYCoordinate = $pdf->GetY();
            $pdf->SetXY($lastXCoordinateHeader, $lastYCoordinateHeader);
            $pdf->MultiCell($lastXCoordinate - $lastXCoordinateHeader, $rowHeight, $lastCategory, 1, 'C', 0, 0);
            $pdf->SetXY($lastXCoordinate, $lastYCoordinate);
//            $pdf->SetFont('courier', '', $f_size);
            $pdf->Ln($rowHeight + 0.2);
        }

//      --------------------------------------------------------------------------------
        $is_even_row = ($is_even_row + 1) % 2;
        if (($is_even_row % 2) === 0) {
            $pdf->SetFillColor(240, 240, 235); // RGB for grey            
        } else {
            $pdf->SetFillColor(255, 255, 255);
        }
//        $fill = $is_even_row / 2;

        if ($pdf->getStringHeight($table_column_sizes[1], $row['Bezeichnung'], false, true, '', 1) > $rowHeightMainLine) {
            while ($pdf->getStringHeight($table_column_sizes[1], $row['Bezeichnung'], false, true, '', 1) > $rowHeightMainLine) {
                $rowHeightMainLine = 1 + $rowHeightMainLine;
            }
        } else {
            $rowHeightMainLine = 7;
        }

        $pdf->MultiCell($table_column_sizes[0], $rowHeightMainLine, $row['ElementID'], 1, 'C', true, 0);
        $pdf->MultiCell($table_column_sizes[1], $rowHeightMainLine, $row['Bezeichnung'], 1, 'C', true, 0);
        $pdf->MultiCell($table_column_sizes[2], $rowHeightMainLine, $row['Variante'], 1, 'C', true, 0);
        $pdf->MultiCell($table_column_sizes[3], $rowHeightMainLine, $row['SummevonAnzahl'], 1, 'C', true, 0);
        if ($row['Neu/Bestand'] == 1) {
            $pdf->MultiCell($table_column_sizes[4], $rowHeightMainLine, "Nein", 1, 'C', true, 0);
        } else {
            $pdf->MultiCell($table_column_sizes[4], $rowHeightMainLine, "Ja", 1, 'C', true, 0);
        }

        // Parameter ausgeben  
        $temp_extracellspace_causeTextToBig = 0;
        foreach ($paramInfos as $array) {
            $tmp_txt = $array['Bezeichnung'];
            $tmp_parameterID = $array['ParamID'];
            $temp_width = $text_width;
            $outputValue = "";

            foreach ($elementParamInfos as $array1) {
                if ($array1['ParamID'] == $tmp_parameterID && $array1['elementID'] == $row['TABELLE_Elemente_idTABELLE_Elemente'] && $array1['variantenID'] == $row['tabelle_Varianten_idtabelle_Varianten']) {
                    if (checkEntry($dataChanges, $array1['elementID'], $array1['ParamID'])) {
                        $pdf->SetFillColor(220, 235, 190);
                    }
                    $outputValue = $array1['Wert'] . checkAndManipulateString($array1['Einheit']);
                    while ($pdf->getStringHeight($text_width + $temp_extracellspace_causeTextToBig, $outputValue, false, false, '', 1) > $rowHeightMainLine) {
                        $temp_extracellspace_causeTextToBig += 1;
                    }
                }
            }

            $pdf->MultiCell($text_width + $temp_extracellspace_causeTextToBig, $rowHeightMainLine, $outputValue, 1, 'C', true, 0);
                if (($is_even_row % 2) === 0) {
                    $pdf->SetFillColor(240, 240, 235);       
                } else {
                    $pdf->SetFillColor(255, 255, 255);
            }
            $text_width = $temp_width;
            if ($temp_extracellspace_causeTextToBig > 0) {
                $temp_extracellspace_causeTextToBig = $temp_extracellspace_causeTextToBig * (-1);
            } else {
                $temp_extracellspace_causeTextToBig = 0;
            }
        }
        $pdf->Ln();
    }
    abk_vz($result1, $pdf, $f_size);
}
