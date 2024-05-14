<?php

session_start();

function make_MT_details_table($pdf, $mysqli, $valueOfRoomID, $block_header_height) {
    $text_width = 7; //$pdf->GetStringWidth($tmp_txt,'courier', '', 6);
    $rowHeight = 6; //$pdf->getStringHeight($text_width+3,$tmp_txt,false,true,'',1);
    //
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
    }

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

    block_label($pdf, "Medizintechnik", $block_header_height);
    $lastXCoordinateHeader = $pdf->GetX();
    $lastYCoordinateHeader = $pdf->GetY();
    $lastCategory = "";

    $pdf->Ln();
    $pdf->SetFont('courier', '', 6);

    //Titelzeile für Elemente im Raum
    $pdf->MultiCell(15, 6, "ID", 1, 'C', 0, 0);
    $pdf->MultiCell(40, 6, "Element", 1, 'C', 0, 0);
    $pdf->MultiCell(8, 6, "Var", 1, 'C', 0, 0);
    $pdf->MultiCell(8, 6, "Stk", 1, 'C', 0, 0);
    $pdf->MultiCell(11, 6, "Bestand", 1, 'C', 0, 0);

// Kopfzeile der Tabelle ausgeben
    foreach ($paramInfos as $array) {
        if ($lastCategory != $array['Kategorie']) {
            $lastXCoordinate = $pdf->GetX();
            $lastYCoordinate = $pdf->GetY();
            $pdf->SetXY($lastXCoordinateHeader, $lastYCoordinateHeader);
            $pdf->MultiCell($lastXCoordinate - $lastXCoordinateHeader, 6, $lastCategory, 1, 'C', 0, 0);
            $lastXCoordinateHeader = $pdf->GetX();
            $lastYCoordinateHeader = $pdf->GetY();
            $lastCategory = $array['Kategorie'];
            $pdf->SetXY($lastXCoordinate, $lastYCoordinate);
        }
        $Bezeichnung = $array['Bezeichnung'];
        $pdf->MultiCell($text_width, $rowHeight, $Bezeichnung, 1, 'C', 0, 0);
    }

    $lastXCoordinate = $pdf->GetX();
    $lastYCoordinate = $pdf->GetY();
    $pdf->SetXY($lastXCoordinateHeader, $lastYCoordinateHeader);
    $pdf->MultiCell($lastXCoordinate - $lastXCoordinateHeader, 6, $lastCategory, 1, 'C', 0, 0);
    $pdf->SetXY($lastXCoordinate, $lastYCoordinate);
    $pdf->Ln();

// -------------------------Elemente im Raum laden-------------------------- 
    $sql = "SELECT tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_varianten.Variante, Sum(tabelle_räume_has_tabelle_elemente.Anzahl) AS SummevonAnzahl, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten
            FROM tabelle_varianten INNER JOIN (tabelle_räume_has_tabelle_elemente INNER JOIN tabelle_elemente ON tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente = tabelle_elemente.idTABELLE_Elemente) ON tabelle_varianten.idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten
            WHERE (((tabelle_räume_has_tabelle_elemente.Verwendung)=1))
            GROUP BY tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_varianten.Variante, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten, tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume
            HAVING (((tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume)=" . $valueOfRoomID . ") AND SummevonAnzahl > 0)
            ORDER BY tabelle_elemente.ElementID, tabelle_varianten.Variante;";

    $result = $mysqli->query($sql);

    $fill = 0;
    $pdf->SetFillColor(244, 244, 244);
    while ($row = $result->fetch_assoc()) {
        $rowHeightMainLine = 10;
        $rowHeight = 6;
        // Prüfen ob Seitenende---------------------------------------------------------
        $y = $pdf->GetY();
        if ($y >= 200) {
            $pdf->AddPage('B', 'A4');
            $pdf->Ln();
            $lastXCoordinateHeader = $pdf->GetX();
            $lastYCoordinateHeader = $pdf->GetY();
            $lastCategory = "";

            $pdf->Ln();

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
                    $pdf->MultiCell($lastXCoordinate - $lastXCoordinateHeader, $rowHeight, $lastCategory, 1, 'C', 0, 0);
                    $lastXCoordinateHeader = $pdf->GetX();
                    $lastYCoordinateHeader = $pdf->GetY();
                    $lastCategory = $array['Kategorie'];
                    $pdf->SetXY($lastXCoordinate, $lastYCoordinate);
                }
                $tmp_txt = $array['Bezeichnung'];
                $text_width = 7; //$pdf->GetStringWidth($tmp_txt,'courier', '', 6);
                $pdf->MultiCell($text_width, $rowHeight, $tmp_txt, 1, 'C', 0, 0);
            }
            $lastXCoordinate = $pdf->GetX();
            $lastYCoordinate = $pdf->GetY();
            $pdf->SetXY($lastXCoordinateHeader, $lastYCoordinateHeader);
            $pdf->MultiCell($lastXCoordinate - $lastXCoordinateHeader, $rowHeight, $lastCategory, 1, 'C', 0, 0);
            $pdf->SetXY($lastXCoordinate, $lastYCoordinate);
            $pdf->Ln();
        }
        //--------------------------------------------------------------------------------


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
        foreach ($paramInfos as $array) {
            $tmp_txt = $array['Bezeichnung'];
            $tmp_parameterID = $array['ParamID'];
            //$text_width = $pdf->GetStringWidth($tmp_txt,'courier', '', 6);                 
            $outputValue = "";
            foreach ($elementParamInfos as $array1) {
                if ($array1['ParamID'] == $tmp_parameterID && $array1['elementID'] == $row['TABELLE_Elemente_idTABELLE_Elemente'] && $array1['variantenID'] == $row['tabelle_Varianten_idtabelle_Varianten']) {
                    $outputValue = $array1['Wert'] . "" . $array1['Einheit'];
                }
            }
            $pdf->MultiCell($text_width, $rowHeightMainLine, $outputValue, 1, 'C', 0, 0);
        }
        $pdf->Ln();
    }

    $pdf->Ln();
//Ausgabe Abkürzungen
    $sql = "SELECT tabelle_parameter_kategorie.Kategorie, tabelle_parameter.Abkuerzung, tabelle_parameter.Bezeichnung
            FROM (tabelle_projekt_elementparameter INNER JOIN tabelle_parameter ON tabelle_projekt_elementparameter.tabelle_parameter_idTABELLE_Parameter = tabelle_parameter.idTABELLE_Parameter) INNER JOIN tabelle_parameter_kategorie ON tabelle_parameter.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie = tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie
            WHERE (((tabelle_projekt_elementparameter.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . ") AND ((tabelle_parameter.`Bauangaben relevant`)=1))
            GROUP BY tabelle_parameter_kategorie.Kategorie, tabelle_parameter.Abkuerzung, tabelle_parameter.Bezeichnung
            ORDER BY tabelle_parameter_kategorie.Kategorie, tabelle_parameter.Bezeichnung;";

    $result4 = $mysqli->query($sql);
    while ($row1 = $result4->fetch_assoc()) {
        $text_width = $pdf->GetStringWidth($row1['Abkuerzung'] . "-", 'courier', 'B', 6);
        $x = $pdf->GetX();
        if (($x + $text_width + 3) >= 400) {
            $pdf->Ln();
        }
        $pdf->SetFont('courier', 'B', 6);
        $pdf->MultiCell($text_width + 3, 6, $row1['Abkuerzung'] . "-", 0, 'R', 0, 0, '', '', true, 0, false, false, 0);
        $text_width = $pdf->GetStringWidth($row1['Bezeichnung'] . ";", 'courier', '', 6);
        $x = $pdf->GetX();
        if (($x + $text_width + 3) >= 400) {
            $pdf->Ln();
        }
        $pdf->SetFont('courier', '', 6);
        $pdf->MultiCell($text_width + 3, 6, $row1['Bezeichnung'] . ";", 0, 'L', 0, 0, '', '', true, 0, false, false, 0);
    }
}
