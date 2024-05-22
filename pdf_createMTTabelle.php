<?php

session_start();

function make_MT_details_table($pdf, $mysqli, $valueOfRoomID, $block_header_height, $SB, $SH) {
    // -------------------------Elemente im Raum laden-------------------------- 
    $sql = "SELECT tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_varianten.Variante, Sum(tabelle_räume_has_tabelle_elemente.Anzahl) AS SummevonAnzahl, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten
            FROM tabelle_varianten INNER JOIN (tabelle_räume_has_tabelle_elemente INNER JOIN tabelle_elemente ON tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente = tabelle_elemente.idTABELLE_Elemente) ON tabelle_varianten.idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten
            WHERE (((tabelle_räume_has_tabelle_elemente.Verwendung)=1))
            GROUP BY tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_varianten.Variante, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten, tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume
            HAVING (((tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume)=" . $valueOfRoomID . ") AND SummevonAnzahl > 0)
            ORDER BY tabelle_elemente.ElementID, tabelle_varianten.Variante;";
    $result = $mysqli->query($sql);

    // -------------------------Elemente parameter ------------------------- 
    $sql = "SELECT tabelle_projekt_elementparameter.Wert, tabelle_projekt_elementparameter.Einheit, tabelle_projekt_elementparameter.tabelle_Varianten_idtabelle_Varianten, tabelle_projekt_elementparameter.tabelle_elemente_idTABELLE_Elemente, tabelle_parameter.Bezeichnung, tabelle_parameter_kategorie.Kategorie, tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie, tabelle_parameter.idTABELLE_Parameter 
    FROM tabelle_parameter_kategorie INNER JOIN (tabelle_parameter INNER JOIN tabelle_projekt_elementparameter ON tabelle_parameter.idTABELLE_Parameter = tabelle_projekt_elementparameter.tabelle_parameter_idTABELLE_Parameter) ON tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie = tabelle_parameter.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie
    WHERE (((tabelle_projekt_elementparameter.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . ") AND tabelle_parameter.`Bauangaben relevant` = 1)
    ORDER BY tabelle_parameter_kategorie.Kategorie, tabelle_parameter.Bezeichnung;";
    $result3 = $mysqli->query($sql);
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
    $sql = "SELECT tabelle_parameter_kategorie.Kategorie, tabelle_parameter.Bezeichnung, tabelle_parameter.idTABELLE_Parameter, tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie, tabelle_parameter.Abkuerzung
    FROM tabelle_parameter_kategorie INNER JOIN (tabelle_parameter INNER JOIN tabelle_projekt_elementparameter ON tabelle_parameter.idTABELLE_Parameter = tabelle_projekt_elementparameter.tabelle_parameter_idTABELLE_Parameter) ON tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie = tabelle_parameter.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie
    WHERE (((tabelle_projekt_elementparameter.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . ") AND tabelle_parameter.`Bauangaben relevant` = 1)
    GROUP BY tabelle_parameter_kategorie.Kategorie, tabelle_parameter.Bezeichnung
    ORDER BY tabelle_parameter_kategorie.Kategorie, tabelle_parameter.Bezeichnung;";
    $result1 = $mysqli->query($sql);
    $paramInfos = array();
    $paramInfosCounter = 0;
    while ($row = $result1->fetch_assoc()) {
        $paramInfos[$row['idTABELLE_Parameter']]['ParamID'] = $row['idTABELLE_Parameter'];
        $paramInfos[$row['idTABELLE_Parameter']]['KategorieID'] = $row['idTABELLE_Parameter_Kategorie'];
        $paramInfos[$row['idTABELLE_Parameter']]['Bezeichnung'] = $row['Abkuerzung'];
        $paramInfos[$row['idTABELLE_Parameter']]['Kategorie'] = $row['Kategorie'];
        $paramInfosCounter = $paramInfosCounter + 1;
    }
    $pdf->SetFillColor(244, 244, 244);

    /// ---------- SIZE/FORMAT VARIABLES ----- -----
    // $marginBTM = 10
    $text_width = ($SB - 82) / $paramInfosCounter;
    $rowHeight = 5;
    $rowHeightMainLine = 8;
    $f_size = 6;

    //
    //---------- FIND MAX SIZES; EMPTY COLUMNS; ETC----------
    //
//    while ($row = $result->fetch_assoc()) {
//        foreach ($paramInfos as $array) {
//            $tmp_txt = $array['Bezeichnung'];
//            $tmp_parameterID = $array['ParamID'];
//            $temp_width = $text_width;
//    $outputValue = "";
//    foreach ($elementParamInfos as $array1) {
////                if ($array1['ParamID'] == $tmp_parameterID && $array1['elementID'] == $row['TABELLE_Elemente_idTABELLE_Elemente'] && $array1['variantenID'] == $row['tabelle_Varianten_idtabelle_Varianten']) {
//        if ($array1['Wert'] !== "") {
//            
//        }
//        $outputValue = $array1['Wert'] . " " . $array1['Einheit'];
////              while ($pdf->getStringHeight($text_width, $outputValue, false, true, '', 1) > $rowHeightMainLine) {$text_width += 1; }
//    }
////            }
////        }
////    }



    $pdf->SetFont('courier', '', $f_size);
    $lastXCoordinateHeader = $pdf->GetX();
    $lastYCoordinateHeader = $pdf->GetY();
    $lastCategory = "";
    $pdf->Ln();
    //Titelzeile für Elemente im Raum
    $pdf->MultiCell(15, $rowHeight, "ID", 1, 'C', 0, 0);
    $pdf->MultiCell(40, $rowHeight, "Element", 1, 'C', 0, 0);
    $pdf->MultiCell(8, $rowHeight, "Var", 1, 'C', 0, 0);
    $pdf->MultiCell(8, $rowHeight, "Stk", 1, 'C', 0, 0);
    $pdf->MultiCell(11, $rowHeight, "Bestand", 1, 'C', 0, 0);

    // Kopfzeile der Tabelle ausgeben
    foreach ($paramInfos as $array) {
        if ($lastCategory != $array['Kategorie']) {
            $lastXCoordinate = $pdf->GetX();
            $lastYCoordinate = $pdf->GetY();

            $pdf->SetXY($lastXCoordinateHeader, $lastYCoordinateHeader);

            $pdf->MultiCell($lastXCoordinate - $lastXCoordinateHeader, $rowHeight, "MT " . $lastXCoordinate . " " . $lastXCoordinateHeader . " " . $lastCategory . "", 1, 'C', 0, 0);

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

    $pdf->MultiCell($lastXCoordinate - $lastXCoordinateHeader, $f_size, $lastCategory, 1, 'C', 0, 0);

    $pdf->SetXY($lastXCoordinate, $lastYCoordinate);
    $pdf->Ln($rowHeight);

    $is_even_row = 0;

    while ($row = $result->fetch_assoc()) {
        //---------------- Prüfen ob Seitenende---------------------------------------------------------
        $y = $pdf->GetY();
        if ($y >= $SH - 5) {
            abk_vz($mysqli, $pdf, $f_size);
            $pdf->AddPage('L', 'A3');
            $lastXCoordinateHeader = $pdf->GetX();
            $lastYCoordinateHeader = $pdf->GetY();
            $lastCategory = "";

            $pdf->Ln($rowHeight);

            $pdf->SetFont('courier', '', 6);
            //Titelzeile für Elemente im Raum
            $pdf->MultiCell(15, $rowHeight, "ID", 1, 'C', 0, 0);
            $pdf->MultiCell(40, $rowHeight, "Element", 1, 'C', 0, 0);
            $pdf->MultiCell(8, $rowHeight, "Var", 1, 'C', 0, 0);
            $pdf->MultiCell(8, $rowHeight, "Stk", 1, 'C', 0, 0);
            $pdf->MultiCell(11, $rowHeight, "Bestand", 1, 'C', 0, 0);

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
            $pdf->Ln($rowHeight +0.2);
        } 
//      --------------------------------------------------------------------------------
        $is_even_row = ($is_even_row + 1) % 2;
        if ($is_even_row == 0) {
            $fill = 0.5;
        } else {
            $fill = 0;
        }
//        $fill = 0.5;
        $pdf->MultiCell(15, $rowHeightMainLine, $row['ElementID'], 1, 'C', $fill, 0);
        $pdf->MultiCell(40, $rowHeightMainLine, $row['Bezeichnung'], 1, 'C', $fill, 0);
        $pdf->MultiCell(8, $rowHeightMainLine, $row['Variante'], 1, 'C', $fill, 0);
        $pdf->MultiCell(8, $rowHeightMainLine, $row['SummevonAnzahl'], 1, 'C', $fill, 0);
        if ($row['Neu/Bestand'] == 1) {
            $pdf->MultiCell(11, $rowHeightMainLine, "Nein", 1, 'C', $fill, 0);
        } else {
            $pdf->MultiCell(11, $rowHeightMainLine, "Ja", 1, 'C', $fill, 0);
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
                    $outputValue = $array1['Wert'] . " " . $array1['Einheit'];
                    while ($pdf->getStringHeight($text_width + $temp_extracellspace_causeTextToBig, $outputValue, false, false, '', 1) > $rowHeightMainLine) {
                        $temp_extracellspace_causeTextToBig += 1;
                    }
                }
            }
            $pdf->MultiCell($text_width + $temp_extracellspace_causeTextToBig, $rowHeightMainLine, $outputValue, 1, 'C', $fill, 0);
            $text_width = $temp_width;
            if ($temp_extracellspace_causeTextToBig > 0) {
                $temp_extracellspace_causeTextToBig = $temp_extracellspace_causeTextToBig * (-1);
            } else {
                $temp_extracellspace_causeTextToBig = 0;
            }
        }
        $pdf->Ln();
    }


//  ----------------------- Ausgabe Abkürzungen -----------------------
    abk_vz($mysqli, $pdf, $f_size);
}

function abk_vz($mysqli, $pdf, $f_size) {
    $sql = "SELECT tabelle_parameter_kategorie.Kategorie, tabelle_parameter.Abkuerzung, tabelle_parameter.Bezeichnung
            FROM (tabelle_projekt_elementparameter INNER JOIN tabelle_parameter ON tabelle_projekt_elementparameter.tabelle_parameter_idTABELLE_Parameter = tabelle_parameter.idTABELLE_Parameter) INNER JOIN tabelle_parameter_kategorie ON tabelle_parameter.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie = tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie
            WHERE (((tabelle_projekt_elementparameter.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . ") AND ((tabelle_parameter.`Bauangaben relevant`)=1))
            GROUP BY tabelle_parameter_kategorie.Kategorie, tabelle_parameter.Abkuerzung, tabelle_parameter.Bezeichnung
            ORDER BY tabelle_parameter_kategorie.Kategorie, tabelle_parameter.Bezeichnung;";
    $result4 = $mysqli->query($sql);

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
    }
}
