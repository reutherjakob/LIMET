<?php

// CONST DEFINITIONS !!!!
$strahlenIDs = array(60, 167, 168, 170, 171, 175, 189, 259, 260, 282, 313, 317, 484, 489, 579, 580, 707, 1182, 1388, 1390, 1461, 1462, 1660, 1680);
$CEE_IDs = array(60, 167);

//UTILITY METHODS
function abcTo123($char) {
    $char = strtolower($char);
    return ord($char) - ord('a') + 1;
}

//METHODS ON ROOM PARAMETER BASIS
function check_dependency_non_zero(&$messages, $roomParams, $param1, $param2) {
    if (isset($roomParams[$param2]) && $roomParams[$param2] > 0) {
        if (!isset($roomParams[$param1]) || $roomParams[$param1] < 1) {
            $messages[] = $roomParams['Raumbezeichnung'] . ": " . $roomParams['Raumnr'] . " --- " . $roomParams['idTABELLE_Räume'] . ":::Raumparameter-> " . $param1 . " ist  " . $roomParams[$param1] . ", aber " . $param2 . " ist " . $roomParams[$param2] . "<br>";
        }
    }
}

function check_max_value(&$messages, $roomParams, $param, $max_value) {
    if (isset($roomParams[$param]) && $roomParams[$param] > $max_value) {
        $messages[] = $roomParams['Raumbezeichnung'] . ": " . $roomParams['Raumnr'] . " --- " . $roomParams['idTABELLE_Räume'] . ":::LeistungZSV-> " . $param . "..." . $roomParams[$param] . "übersteigt max=" . $max_value . ".<br>";
    }
}

function check_max_value_rev(&$messages, $roomParams, $param, $max_value) {     //TODO-> check out what happens if param eint set. 
    if (!isset($roomParams[$param]) || $roomParams[$param] < $max_value) {
        $messages[] = $roomParams['Raumbezeichnung'] . ": " . $roomParams['Raumnr'] . " --- " . $roomParams['idTABELLE_Räume'] . ":::Abwärme-> " . $max_value . " übersteigt Raumangabe=" . $roomParams[$param] . ".<br>";
    }
}

function check_awg(&$messages, $roomParams) {
    if (isset($roomParams['Anwendungsgruppe'])) {
        if ($roomParams['Anwendungsgruppe'] >= 1 && (!isset($roomParams['SV']) || $roomParams['SV'] != 1)) {
            $messages[] = $roomParams['Raumbezeichnung'] . ": " . $roomParams['Raumnr'] . " --- " . $roomParams['idTABELLE_Räume'] . ":::AWG-> SV=" . $roomParams['SV'] . ",aber AWG=" . $roomParams['Anwendungsgruppe'] . ".<br>";
        } elseif ($roomParams['Anwendungsgruppe'] == 2 && (!isset($roomParams['ZSV']) || $roomParams['ZSV'] != 1)) {
            $messages[] = $roomParams['Raumbezeichnung'] . ": " . $roomParams['Raumnr'] . " --- " . $roomParams['idTABELLE_Räume'] . ":::AWG-> ZSV=" . $roomParams['ZSV'] . ",aber AWG=" . $roomParams['Anwendungsgruppe'] . ".<br>";
        }
    }
}

function check_summe_leistungen(&$messages, $roomParams) {
    $summe = (intval($roomParams['ET_Anschlussleistung_AV_W']) + intval($roomParams['ET_Anschlussleistung_SV_W']) + intval($roomParams['ET_Anschlussleistung_ZSV_W']) + intval($roomParams['ET_Anschlussleistung_USV_W']));
    if ($summe > intval($roomParams['ET_Anschlussleistung_W'])) {
        $messages[] = $roomParams['Raumbezeichnung'] . ": " . $roomParams['Raumnr'] . " --- " . $roomParams['idTABELLE_Räume'] . ":::Leistung ∑-> " . $roomParams['ET_Anschlussleistung_W'] . "= ∑Anschlussleistung(Raum) < ∑P je Netzart! (" . (intval($roomParams['ET_Anschlussleistung_AV_W']) . "/" . intval($roomParams['ET_Anschlussleistung_SV_W']) . "/" . intval($roomParams['ET_Anschlussleistung_ZSV_W']) . "/" . intval($roomParams['ET_Anschlussleistung_USV_W'])) . ")<br>  ";
    }
}

// METHODS ON ELEMENT BASIS
function check_room_for_parameters_cause_elementParamKathegorie(&$messages, $roomParams, $element_parameter_id, $row) {
    $translation_array = array(//parameter 1d and corresponding room parameter
        117 => '1 Kreis O2',
        121 => '1 Kreis DL-5',
        122 => '1 Kreis Va',
        123 => 'DL-10',
        124 => 'NGA',
        125 => 'N2O',
        126 => 'CO2',
        127 => 'ET_RJ45-Ports'
    );
    $param_name = $translation_array[$element_parameter_id];
    if (!isset($roomParams[$param_name]) || $roomParams[$param_name] < 1) {
        $messages[] = $roomParams['Raumbezeichnung'] . ": " . $roomParams['Raumnr'] . " --- " . $roomParams['idTABELLE_Räume'] . ":::ElementPort-> " . $param_name . " Element " . $row['Bezeichnung'] . " präsent, aber Raumparameter=" . $roomParams[$param_name] . "! <br>";
    }
}

function getComponents($input) {
    $validComponents = array("AV", "SV", "ZSV", "USV");
    $components = explode("/", $input);
    $result = array();
    foreach ($components as $component) {
        if (in_array($component, $validComponents)) {
            array_push($result, $component);
        }
    }
    return $result;
}

function getUniqueComponents($newComponents, $existingComponents) {
    foreach ($newComponents as $component) {
        if (!in_array($component, $existingComponents)) {
            array_push($existingComponents, $component);
        }
    }
    return $existingComponents;
}

function check_room_for_na(&$messages, $roomParams, $NAimRaum) {
    foreach ($NAimRaum as $na) {
        if (!isset($roomParams[$na]) || $roomParams[$na] < 1) {
            $messages[] = $roomParams['Raumbezeichnung'] . ": " . $roomParams['Raumnr'] . " --- " . $roomParams['idTABELLE_Räume'] . ":::Netzarten-> " . $na . " in  Element präsent, aber Raumparameter=0!<br>";
        }
    }
}

function check_4_room_param(&$messages, $roomParams, $theonetobechecked4, $row) { // Strahlenanwendung
    if (!isset($roomParams[$theonetobechecked4]) || $roomParams[$theonetobechecked4] < 1) {
        $messages[] = $roomParams['Raumbezeichnung'] . ": " . $roomParams['Raumnr'] . " --- " . $roomParams['idTABELLE_Räume'] . ":::Raumparameter->" . $theonetobechecked4 . " Element " . $row['Bezeichnung'] . " präsent, aber Raumparameter=0! <br>";
    }
}

function check4vorabsperr(&$messages, $roomParams, $elements_in_room, $stativ_präsent) {
    if ($stativ_präsent) {
        $found = false;
        foreach ($elements_in_room as $el) {
            if ($el["idTABELLE_Elemente"] == 664) {
                $found = true;
                break;
            }
        }
        if (!$found) {
            $messages[] = $roomParams['Raumbezeichnung'] . ": " . $roomParams['Raumnr'] . " --- " . $roomParams['idTABELLE_Räume'] . ":::ElementPort-> Gasanschluss Vorabsperrkasten! <br>";
        }
    }
}

function check_room_Leistungssumme(&$messages, $roomParams, $P) {
    $mapping = array("NoNA", "AV", "SV", "ZSV", "USV");  //Structure i chose for the array of power within room
    $summe = 0;
    foreach ($P as $index => $P_jeNA) {
        if ($index > 0 && $index < 5) {
            if ($P_jeNA > $roomParams['ET_Anschlussleistung_' . $mapping[$index] . '_W']) {
                $messages[] = $roomParams['Raumbezeichnung'] . ": " . $roomParams['Raumnr'] . " --- " . $roomParams['idTABELLE_Räume'] . ":::Leistung ∑-> ∑P[" . $mapping[$index] . "](Elemente) =" . $P_jeNA . " > " . $mapping[$index] . "_W=" . $roomParams['ET_Anschlussleistung_' . $mapping[$index] . '_W'] . " <br>";
            }
            if ($P_jeNA > 8000 && $index === 3) {
                $messages[] = $roomParams['Raumbezeichnung'] . ": " . $roomParams['Raumnr'] . " --- " . $roomParams['idTABELLE_Räume'] . ":::Leistung ∑-> P[" . $mapping[$index] . "](Elemente) > 8kW! <br>";
            }
        } else {
//            if ($P_jeNA > 0) {
//                $messages[] =  $roomParams['Raumbezeichnung'] . ": " . $roomParams['Raumnr'] . " --- ".$roomParams['idTABELLE_Räume'] . ":::Element without NA parameter!<br>";
//            } 
        }
        $summe += $P_jeNA;
//        echo "Aufsummieren :" . $summe;
    }
//    $messages[] = $roomParams['Raumbezeichnung'] . ": " . $roomParams['Raumnr'] . " --- ".$roomParams['idTABELLE_Räume'] . ":::LEISTUNG∑-> RAUM ∑P=" . $summe . "!<br>";
    if ($summe > $roomParams['ET_Anschlussleistung_W']) {
        $messages[] = $roomParams['Raumbezeichnung'] . ": " . $roomParams['Raumnr'] . " --- " . $roomParams['idTABELLE_Räume'] . ":::Leistung ∑->  ∑P=" . $summe . " > RaumAnschlussleistung= " . $roomParams['ET_Anschlussleistung_W'] . "<br>";
    }
}

function distribute($x, $P, $NAs) {
//    $P = $Pin;
    $mapping = ["AV" => 1, "SV" => 2, "ZSV" => 3, "USV" => 4];
    if ($x > 0) {
        if (empty($NAs)) {
            $P[0] += $x;
        } else {
            $value = $x / count($NAs);
            foreach ($NAs as $NA) {
                if (isset($mapping[$NA])) {
                    $P[$mapping[$NA]] += $value;
                } else {
                    echo "Internal error";
                    $P[0] += $x;
                }
            }
        }
    }
    return $P;
}

function unitMultiplier($text) {
    if (strpos($text, 'k') !== false) {
        return 1000;
    } else if (strpos($text, 'K') !== false) {
        return 1000;
    } else {
        return 1;
    }
}

//   -------------  MAIN  ---------------  


session_start();
include '_utils.php';
check_login();
$messages = array();

$roomIDsArray = array();
function getQueryParam($param) {
    return isset($_GET[$param]) ? $_GET[$param] : null; 
}

// Usage
$roomID = getQueryParam('roomID');
if ($roomID !== null) {
    $roomIDsArray = explode(',', $roomID); 
}
 
//echorow($roomIDsArray);


$mysqli = utils_connect_sql();
//foreach ($roomIDsArray as $valueOfRoomID) { 
//
//
$stmt = $mysqli->prepare("SELECT * FROM tabelle_räume
                INNER JOIN tabelle_funktionsteilstellen ON tabelle_räume.TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen = tabelle_funktionsteilstellen.idTABELLE_Funktionsteilstellen
                WHERE tabelle_räume.tabelle_projekte_idTABELLE_Projekte = ? 
                ORDER BY tabelle_räume.tabelle_projekte_idTABELLE_Projekte");  // AND tabelle_räume.idTABELLE_Räume= ?
$stmt->bind_param("i", $_SESSION["projectID"]);
$stmt->execute();
$result = $stmt->get_result();
$raumparameter = array();
while ($row = $result->fetch_assoc()) {
    $roomID = $row['idTABELLE_Räume'];
    $raumparameter[$roomID] = $row;
   
} 
//echorow($raumparameter);
//    echorow($stmt); 
$stmt = $mysqli->prepare("SELECT 
                tabelle_projekt_elementparameter.Wert, 
                tabelle_projekt_elementparameter.Einheit, 
                tabelle_projekt_elementparameter.tabelle_Varianten_idtabelle_Varianten, 
                tabelle_projekt_elementparameter.tabelle_elemente_idTABELLE_Elemente, 
                tabelle_parameter.Bezeichnung, 
                tabelle_parameter_kategorie.Kategorie, 
                tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie, 
                tabelle_parameter.idTABELLE_Parameter 
            FROM 
                tabelle_parameter_kategorie 
            INNER JOIN (
                tabelle_parameter 
                INNER JOIN tabelle_projekt_elementparameter ON tabelle_parameter.idTABELLE_Parameter = tabelle_projekt_elementparameter.tabelle_parameter_idTABELLE_Parameter
            ) 
            ON tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie = tabelle_parameter.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie
            WHERE 
                tabelle_projekt_elementparameter.tabelle_projekte_idTABELLE_Projekte = ? AND 
                tabelle_parameter.`Bauangaben relevant` = 1
            ORDER BY 
                tabelle_parameter_kategorie.Kategorie, 
                tabelle_projekt_elementparameter.tabelle_elemente_idTABELLE_Elemente,
                tabelle_parameter.Bezeichnung");   //$elementParamInfos 

$stmt->bind_param("i", $_SESSION["projectID"]);
$stmt->execute();
$result = $stmt->get_result();
//idTABELLE_Räume
$elementParamInfos = array();
$paramInfosCounter = 0;

while ($row = $result->fetch_assoc()) {
//    $elID = $row['tabelle_elemente_idTABELLE_Elemente']; 
    $elementParamInfos[$paramInfosCounter] = $row;
    $paramInfosCounter = $paramInfosCounter + 1; // echorow($row);
}


foreach ($raumparameter as $roomID => $roomParams) {
    if (!empty($roomIDsArray) && !in_array($roomID, $roomIDsArray)) {
        continue;
//        echo $roomID. "<br> "; 
    }
//        echo "RID: ".$roomID. "\n <br>";
    //////// AWG
    check_awg($messages, $roomParams);
    ////////ET 
    check_summe_leistungen($messages, $roomParams);
    check_dependency_non_zero($messages, $roomParams, 'IT Anbindung', 'ET_RJ45-Ports');
    check_dependency_non_zero($messages, $roomParams, 'ET_RJ45-Ports', 'IT Anbindung');

    check_max_value($messages, $roomParams, 'ET_Anschlussleistung_ZSV_W', 8000); // done within orthere 

    check_dependency_non_zero($messages, $roomParams, 'AV', 'ET_Anschlussleistung_AV_W');
    check_dependency_non_zero($messages, $roomParams, 'AV', 'EL_AV Steckdosen Stk');
    check_dependency_non_zero($messages, $roomParams, 'SV', 'ET_Anschlussleistung_SV_W');
    check_dependency_non_zero($messages, $roomParams, 'SV', 'EL_SV Steckdosen Stk');
    check_dependency_non_zero($messages, $roomParams, 'ZSV', 'ET_Anschlussleistung_ZSV_W');
    check_dependency_non_zero($messages, $roomParams, 'ZSV', 'EL_ZSV Steckdosen Stk');
    check_dependency_non_zero($messages, $roomParams, 'USV', 'ET_Anschlussleistung_USV_W');
    check_dependency_non_zero($messages, $roomParams, 'USV', 'EL_USV Steckdosen Stk');
    //////MEDGAS
    check_dependency_non_zero($messages, $roomParams, '1 Kreis O2', '2 Kreis O2');
    check_dependency_non_zero($messages, $roomParams, '1 Kreis Va', '2 Kreis Va');
    check_dependency_non_zero($messages, $roomParams, '1 Kreis DL-5', '2 Kreis DL-5');
    //STOP ROOM param CHECK  
///// ------ LOAD ELEMENTS IN ROOM ----        
    $stmt = $mysqli->prepare("SELECT tabelle_elemente.ElementID,
            tabelle_elemente.idTABELLE_Elemente, tabelle_elemente.Bezeichnung, 
            tabelle_varianten.Variante, Sum(tabelle_räume_has_tabelle_elemente.Anzahl) AS SummevonAnzahl
            FROM tabelle_varianten 
            INNER JOIN (tabelle_räume_has_tabelle_elemente 
            INNER JOIN tabelle_elemente ON tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente = tabelle_elemente.idTABELLE_Elemente) 
            ON tabelle_varianten.idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten
            WHERE tabelle_räume_has_tabelle_elemente.Verwendung = 1
            GROUP BY tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_varianten.Variante, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, 
            tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten, tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume
            HAVING tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume = ? AND SummevonAnzahl > 0
            ORDER BY tabelle_elemente.ElementID, tabelle_varianten.Variante");   //ELEMENTS IN THE ROOM 

    $stmt->bind_param("i", $roomID);
    $stmt->execute();
    $result = $stmt->get_result();

    $elements_in_room = array();
    $NetzArtenImRaum = array();
    $LeistungImRaum = array(0, 0, 0, 0, 0); // ALLGEMEIN / AV/SV/ZSV/USV
    $Abwärme = 0;
    $check4Vorabsperrkasten = false;
    $elements_counter = 0;

    while ($row = $result->fetch_assoc()) {  //ELEMENTS IN THE ROOM 
        $elements_in_room[$elements_counter] = $row;
        $elements_counter++;
//            echo abcTo123($elements_in_room[$roomID]['Variante']) . "</br> ";  
//            echorow($row);  //for FORENSICS  
//            echorow($elementParamInfos);  
        if (in_array($row['idTABELLE_Elemente'], $strahlenIDs)) {
            check_4_room_param($messages, $roomParams, "Strahlenanwendung", $row);
            if (in_array($row['idTABELLE_Elemente'], $CEE_IDs)) {
                check_4_room_param($messages, $roomParams, "EL_Roentgen 16A CEE Stk", $row);
            }
        }
        if (strpos($row['ElementID'], "2.34.19") === 0 || strpos($row['ElementID'], "2.56.16") === 0) { //Lasercheck 
            check_4_room_param($messages, $roomParams, "Laseranwendung", $row);
        }

        $temp_LeistungElement = 0;
        $tempNA_perElement = "";
        $temp_GLZ = 1.00;
        $Abwärme_el = 0;
        $AnzahlElImRaum = $row['SummevonAnzahl'];

        foreach ($elementParamInfos as $parameterInfo) {
            if ($parameterInfo["tabelle_Varianten_idtabelle_Varianten"] === abcTo123($row['Variante']) && $row['idTABELLE_Elemente'] === $parameterInfo["tabelle_elemente_idTABELLE_Elemente"]) {
                if ($parameterInfo['idTABELLE_Parameter_Kategorie'] === 2) { //ElektroTechnik                    
                    if ($parameterInfo['idTABELLE_Parameter'] === 127) {  //ELEMENT MIT RJ PORTS
                        check_room_for_parameters_cause_elementParamKathegorie($messages, $roomParams, $parameterInfo['idTABELLE_Parameter'], $row); //check for if room got that plug. 
                    }
                    if ($parameterInfo['idTABELLE_Parameter'] === 82) {
                        $tempNA_perElement = array_merge(getComponents($parameterInfo['Wert']), getComponents($parameterInfo['Einheit']));
                        $NetzArtenImRaum = getUniqueComponents($tempNA_perElement, $NetzArtenImRaum);
                    }
                    if ($parameterInfo['idTABELLE_Parameter'] === 18) { // element im raum hat Leistung 
                        $temp_LeistungElement = floatval(str_replace(",", ".", preg_replace("/[^0-9.,]/", "", $parameterInfo['Wert']))) * unitMultiplier($parameterInfo["Einheit"]);
                    }
                    if ($parameterInfo['idTABELLE_Parameter'] === 133) {//GLeichzeitigkeit ,1
                        $temp_GLZ = floatval(str_replace(",", ".", preg_replace("/[^0-9,.]/", "", $parameterInfo['Wert'])));
                    }
                }
                if ($parameterInfo['idTABELLE_Parameter_Kategorie'] === 3) {
                    $Abwärme_el = floatval(str_replace(",", ".", preg_replace("/[^0-9.,]/", "", $parameterInfo['Wert']))) * unitMultiplier($parameterInfo["Einheit"]);
                }
                if ($parameterInfo['idTABELLE_Parameter_Kategorie'] === 12) {  //MEDGAS
                    check_room_for_parameters_cause_elementParamKathegorie($messages, $roomParams, $parameterInfo['idTABELLE_Parameter'], $row);   //check for if room got that plug.  
                    if (stripos($row['Bezeichnung'], "stativ")) {
                        $check4Vorabsperrkasten = true;
                    }
                }
            }
        }// END OF PARAM PER ELEMENT LOOP 
        $Abwärme += $Abwärme_el * $temp_GLZ * $AnzahlElImRaum; //  
        $LeistungInklGLZ = $temp_LeistungElement * $temp_GLZ * $AnzahlElImRaum;
        $LeistungImRaum = distribute($LeistungInklGLZ, $LeistungImRaum, $tempNA_perElement); // LEISTUNg wird je nach Element Netzart aufgeteilt

        if ($temp_LeistungElement > 0) {
            if (empty($tempNA_perElement)) {
                $messages[] = $roomParams['Raumbezeichnung'] . ": " . $roomParams['Raumnr'] . " --- " . $roomParams['idTABELLE_Räume'] . ":::Netzarten->  " . $row['Bezeichnung'] . " hat Leistung aber keine Netzart!<br>";
            }
//                echo "ELEMENTS in ROOM: " . $roomID . " " . $roomParams['Raumbezeichnung'] . ": " . $roomParams['Raumnr'] . " --- ".$roomParams['idTABELLE_Räume'] . "\t\t\t<br>";
//                echorow($LeistungImRaum);
//                echo "El: " . $row['Bezeichnung'] . "- GLZ: " . $temp_GLZ . "- Leistung: " . $temp_LeistungElement . "- Anzahl: " . $AnzahlElImRaum . "<br> "; //"<br> Abw El" . $Abwärme_el . 
        }
    }// END OF ELEMENTS LOOP

    check4vorabsperr($messages, $roomParams, $elements_in_room, $check4Vorabsperrkasten);
    check_max_value_rev($messages, $roomParams, "HT_Waermeabgabe_W", $Abwärme);
    check_room_for_na($messages, $roomParams, $NetzArtenImRaum);
    check_room_Leistungssumme($messages, $roomParams, $LeistungImRaum);
}  //   eingefügt um ausgabe zum Codenn zu unterbinden 
//} //end of foreach Rooom
$mysqli->close();

foreach ($messages as $messages_out) {
    echo br2nl($messages_out);
//    echo $messages_out;
}                 


