<?php

// CONST DEFINITIONS !!!!
$strahlenIDs = array(60, 167, 168, 170, 171, 175, 189, 259, 260, 282, 313, 317, 484, 489, 579, 580, 707, 1182, 1388, 1390, 1461, 1462, 1660, 1680);
$CEE_IDs = array(60, 167);

//
//UTILITY METHODS
function echorow($row) {
    echo '<pre>';
    print_r($row);
    echo '- </pre>';
}

function abcTo123($char) {
    $char = strtolower($char);
    return ord($char) - ord('a') + 1;
}

//METHODS ON ROOM PARAMETER BASIS
function check_dependency_non_zero(&$messages, $roomParams, $param1, $param2) {
    if (isset($roomParams[$param2]) && $roomParams[$param2] > 0) {
        if (!isset($roomParams[$param1]) || $roomParams[$param1] < 1) {
            $messages[] =  $roomParams['Raumbezeichnung'] . ": " . $roomParams['Raumnr'] . ":::\t\t " . $param1 . " is  " . $roomParams[$param1] . " while " . $param2 . " is " . $roomParams[$param2] . "<br>";
        }
    }
}

function check_max_value(&$messages, $roomParams, $param, $max_value) {
    if (isset($roomParams[$param]) && $roomParams[$param] > $max_value) {
        $messages[] =  $roomParams['Raumbezeichnung'] . ": " . $roomParams['Raumnr'] . ":::\t\t " . $param . ":" . $roomParams[$param] . " exceeds the maximum of " . $max_value . ".<br>";
    }
}

function check_max_value_rev(&$messages, $roomParams, $param, $max_value) {     //TODO-> check out what happens if param eint set. 
    if (!isset($roomParams[$param]) || $roomParams[$param] < $max_value) {
        $messages[] =  $roomParams['Raumbezeichnung'] . ": " . $roomParams['Raumnr'] . ":::\t\t " . $max_value . " exceeds the room param of " . $param . ":" . $roomParams[$param] . ".<br>";
    }
}

function check_awg(&$messages, $roomParams) {
    if (isset($roomParams['Anwendungsgruppe'])) {
        if ($roomParams['Anwendungsgruppe'] == 0) {
            
        } elseif ($roomParams['Anwendungsgruppe'] >= 1 && (!isset($roomParams['SV']) || $roomParams['SV'] != 1)) {
            $messages[] =  $roomParams['Raumbezeichnung'] . ": " . $roomParams['Raumnr'] . ":::\t\t SV=" . $roomParams['SV'] . "while Anwendungsgruppe is " . $roomParams['Anwendungsgruppe'] . ".<br>";
        } elseif ($roomParams['Anwendungsgruppe'] == 2 && (!isset($roomParams['ZSV']) || $roomParams['ZSV'] != 1)) {
            $messages[] =  $roomParams['Raumbezeichnung'] . ": " . $roomParams['Raumnr'] . ":::\t\t ZSV=" . $roomParams['ZSV'] . " while Anwendungsgruppe is " . $roomParams['Anwendungsgruppe'] . ".<br>";
        }
    }
}

function check_summe_leistungen(&$messages, $roomParams) {
    $summe = (intval($roomParams['ET_Anschlussleistung_AV_W']) + intval($roomParams['ET_Anschlussleistung_SV_W']) + intval($roomParams['ET_Anschlussleistung_ZSV_W']) + intval($roomParams['ET_Anschlussleistung_USV_W']));
    if ($summe > intval($roomParams['ET_Anschlussleistung_W'])) {
        $messages[] =  $roomParams['Raumbezeichnung'] . ": " . $roomParams['Raumnr'] . ":::\t\t " . $roomParams['ET_Anschlussleistung_W'] . "::   SUMME Anschlussleistung kleiner als die Summe der Anschlussleistungen je Netzart!.<br>  ";
    }
}

// METHODS ON ELEMENT BASIS
function check_room_for_parameters_cause_elementParamKathegorie(&$messages, $roomParams, $element_parameter_id) {
    $translation_array = array(//parameter 1d and corresponding room parameter
        117 => "O2",
        121 => 'DL-5',
        122 => "VA",
        123 => 'DL-10',
        124 => 'NGA',
        125 => 'N2O',
        126 => "CO2",
        127 => "ET_RJ45-Ports"
    );
    $param_name = $translation_array[$element_parameter_id];
    if (!isset($roomParams[$param_name]) || $roomParams[$param_name] < 1) {
        $messages[] =  $roomParams['Raumbezeichnung'] . ": " . $roomParams['Raumnr'] . ":::\t\t " . $param_name . " element present while room interface=0! <br>";
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
            $messages[] =  $roomParams['Raumbezeichnung'] . ": " . $roomParams['Raumnr'] . ":::\t\t " . $na . " element present while room interface=0! <br>";
        }
    }
}

function check_4_room_param(&$messages, $roomParams, $theonetobechecked4) { // Strahlenanwendung
    if (!isset($roomParams[$theonetobechecked4]) || $roomParams[$theonetobechecked4] < 1) {
        $messages[] =  $roomParams['Raumbezeichnung'] . ": " . $roomParams['Raumnr'] . ":::\t\t " . $theonetobechecked4 . " element present while roomparam=0! <br>";
    }
}

function check4vorabsperr(&$messages, $roomParams, $elements_in_room, $stativ_present) {
    if ($stativ_present) {
        $found = false;
        foreach ($elements_in_room as $el) {
            if ($el["idTABELLE_Elemente"] == 664) {
                $found = true;
                break;
            }
        }
        if (!$found) {
            $messages[] =  $roomParams['Raumbezeichnung'] . ": " . $roomParams['Raumnr'] . ":::\t\t Stativ with gas plug present but no Vorabsperrkasten! <br>";
        }
    }
}

function check_room_Leistungssumme(&$messages, $roomParams, $P) {
    $mapping = array("NoNA", "AV", "SV", "ZSV", "USV");  //Structure i chose for the array of power within room
    $summe = 0;
    foreach ($P as $index => $P_jeNA) {
        if ($index > 0 && $index < 5) {
            if ($P_jeNA > $roomParams['ET_Anschlussleistung_' . $mapping[$index] . '_W']) {
                $messages[] =  $roomParams['Raumbezeichnung'] . ": " . $roomParams['Raumnr'] . ":::\t\t P[" . $mapping[$index] . "]=" . $P_jeNA . " is greater than ET_Anschlussleistung_" . $mapping[$index] . "_W=" . $roomParams['ET_Anschlussleistung_' . $mapping[$index] . '_W'] . " <br>";
            }
            if ($P_jeNA > 8000 && $index === 3) {
                $messages[] =  $roomParams['Raumbezeichnung'] . ": " . $roomParams['Raumnr'] . ":::\t\t P[ZSV] greater 8kW! <br>";
            }
        } else {
            if ($P_jeNA > 0) {
                $messages[] =  $roomParams['Raumbezeichnung'] . ": " . $roomParams['Raumnr'] . ":::\t\t Element without NA parameter!<br>";
            } 
        }
        $summe += $P_jeNA;
    } 
    if ($summe > $roomParams['ET_Anschlussleistung_W']) {
        $messages[] =  $roomParams['Raumbezeichnung'] . ": " . $roomParams['Raumnr'] . ":::\t\t The sum of all P = " . $summe . " is greater than ET_Anschlussleistung_W = " . $roomParams['ET_Anschlussleistung_W'] . "<br>";
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
$mysqli = utils_connect_sql();

//// GET ROOMS in projekt and their params 
// Prepare SQL statement
$stmt = $mysqli->prepare("SELECT * FROM tabelle_räume
                INNER JOIN tabelle_funktionsteilstellen ON tabelle_räume.TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen = tabelle_funktionsteilstellen.idTABELLE_Funktionsteilstellen
                WHERE tabelle_räume.tabelle_projekte_idTABELLE_Projekte = ?
                ORDER BY tabelle_räume.tabelle_projekte_idTABELLE_Projekte"); // (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . "))
// Bind parameters
$stmt->bind_param("i", $_SESSION["projectID"]);
$stmt->execute();
$result = $stmt->get_result();

$raumparameter = array();
while ($row = $result->fetch_assoc()) {
    $roomID = $row['idTABELLE_Räume'];
    $raumparameter[$roomID] = $row;
}

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

$elementParamInfos = array();
$paramInfosCounter = 0;
//echo "ELEMENTS PARAMETERS Raumübergreifend";
while ($row = $result->fetch_assoc()) {
//    $elID = $row['tabelle_elemente_idTABELLE_Elemente']; 
    $elementParamInfos[$paramInfosCounter] = $row;
    $paramInfosCounter = $paramInfosCounter + 1; // echorow($row);
}

$messages = array();
foreach ($raumparameter as $roomID => $roomParams) {
    //////// AWG
    check_awg($messages, $roomParams);
    ////////ET 
    check_summe_leistungen($messages, $roomParams);
    check_dependency_non_zero($messages, $roomParams, 'IT Anbindung', 'ET_RJ45-Ports');
    check_dependency_non_zero($messages, $roomParams, 'ET_RJ45-Ports', 'IT Anbindung');
    check_max_value($messages, $roomParams, 'ET_Anschlussleistung_ZSV_W', 8000);
    check_dependency_non_zero($messages, $roomParams, 'AV', 'ET_Anschlussleistung_AV_W');
    check_dependency_non_zero($messages, $roomParams, 'AV', 'EL_AV Steckdosen Stk');
    check_dependency_non_zero($messages, $roomParams, 'SV', 'ET_Anschlussleistung_SV_W');
    check_dependency_non_zero($messages, $roomParams, 'SV', 'EL_SV Steckdosen Stk');
    check_dependency_non_zero($messages, $roomParams, 'ZSV', 'ET_Anschlussleistung_ZSV_W');
    check_dependency_non_zero($messages, $roomParams, 'ZSV', 'EL_ZSV Steckdosen Stk');
    check_dependency_non_zero($messages, $roomParams, 'USV', 'ET_Anschlussleistung_USV_W');
    check_dependency_non_zero($messages, $roomParams, 'USV', 'EL_USV Steckdosen Stk');
    //////MEDGAS
    check_dependency_non_zero($messages, $roomParams, '1 Kreis O2', 'O2');
    check_dependency_non_zero($messages, $roomParams, '1 Kreis Va', 'VA');
    check_dependency_non_zero($messages, $roomParams, '1 Kreis DL-5', 'DL-5');
    check_dependency_non_zero($messages, $roomParams, 'O2', '1 Kreis O2');
    check_dependency_non_zero($messages, $roomParams, 'VA', '1 Kreis Va');
    check_dependency_non_zero($messages, $roomParams, 'DL-5', '1 Kreis DL-5');
    check_dependency_non_zero($messages, $roomParams, '1 Kreis O2', '2 Kreis O2');
    check_dependency_non_zero($messages, $roomParams, '1 Kreis Va', '2 Kreis Va');
    check_dependency_non_zero($messages, $roomParams, '1 Kreis DL-5', '2 Kreis DL-5');
    //STOP ROOM param CHECK  
///// ------ LOAD ELEMENTS IN ROOM ----        
    $stmt = $mysqli->prepare("SELECT tabelle_elemente.ElementID,  tabelle_elemente.idTABELLE_Elemente, tabelle_elemente.Bezeichnung, tabelle_varianten.Variante, Sum(tabelle_räume_has_tabelle_elemente.Anzahl) AS SummevonAnzahl
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
//    echo "ELEMENTS in " . $roomID . " " .  $roomParams['Raumbezeichnung'] . ": " . $roomParams['Raumnr'] . "\t\t\t<br>";
    while ($row = $result->fetch_assoc()) {  //ELEMENTS IN THE ROOM 
        $elements_in_room[$elements_counter] = $row;
        $elements_counter++;
//            echo abcTo123($elements_in_room[$roomID]['Variante']) . "</br> ";  
//            echorow($row);  //for FORENSICS  
//            echorow($elementParamInfos);  
        if (in_array($row['idTABELLE_Elemente'], $strahlenIDs)) {
            check_4_room_param($messages, $roomParams, "Strahlenanwendung");
            if (in_array($row['idTABELLE_Elemente'], $CEE_IDs)) {
                check_4_room_param($messages, $roomParams, "EL_Roentgen 16A CEE Stk");
            }
        }
        if (strpos($row['ElementID'], "2.34.19") === 0 || strpos($row['ElementID'], "2.56.16") === 0) { //Lasercheck 
            check_4_room_param($messages, $roomParams, "Laseranwendung");
        }

        $temp_LeistungElement = 0;
        $tempNA_perElement = "";
        $temp_GLZ = 1;
        $Abwärme_el = 0;
        
        foreach ($elementParamInfos as $parameterInfo) {
            if ($parameterInfo["tabelle_Varianten_idtabelle_Varianten"] === abcTo123($row['Variante']) && $row['idTABELLE_Elemente'] === $parameterInfo["tabelle_elemente_idTABELLE_Elemente"]) {
                if ($parameterInfo['idTABELLE_Parameter_Kategorie'] === 2) { //ElektroTechnik                    
                    if ($parameterInfo['idTABELLE_Parameter'] === 127) {  //ELEMENT MIT RJ PORTS
                        check_room_for_parameters_cause_elementParamKathegorie($messages, $roomParams, $parameterInfo['idTABELLE_Parameter']); //check for if room got that plug. 
                    }
                    if ($parameterInfo['idTABELLE_Parameter'] === 82) {
                        $tempNA_perElement = array_merge(getComponents( preg_replace("/[^0-9.]/", "",$parameterInfo['Wert'])), getComponents($parameterInfo['Einheit']));
                        $NetzArtenImRaum = getUniqueComponents($tempNA_perElement, $NetzArtenImRaum);
                    }
                    if ($parameterInfo['idTABELLE_Parameter'] === 18) { // element im raum hat Leistung 
                        $temp_LeistungElement = floatval(str_replace(",", ".",  preg_replace("/[^0-9.]/", "",$parameterInfo['Wert']))) * unitMultiplier($parameterInfo["Einheit"]);
                    }
                    if ($parameterInfo['idTABELLE_Parameter'] === 133) {//GLeichzeitigkeit ,1
                        $temp_GLZ = str_replace(",", ".",  preg_replace("/[^0-9.]/", "",$parameterInfo['Wert']));
                    }
                }
                if ($parameterInfo['idTABELLE_Parameter_Kategorie'] === 3) { 
                    $Abwärme_el = str_replace(",", ".", preg_replace("/[^0-9.]/", "", preg_replace("/[^0-9.]/", "",$parameterInfo['Wert']))) * unitMultiplier($parameterInfo["Einheit"]);
                }
                if ($parameterInfo['idTABELLE_Parameter_Kategorie'] === 12) {  //MEDGAS
                    check_room_for_parameters_cause_elementParamKathegorie($messages, $roomParams, $parameterInfo['idTABELLE_Parameter']);   //check for if room got that plug.  
                    if (stripos($row['Bezeichnung'], "stativ")) {
                        $check4Vorabsperrkasten = true;
                    }
                }
            }
        }// END OF PARAM PER ELEMENT LOOP 
        $Abwärme += $Abwärme_el * $temp_GLZ;//  echo "GLZ El" . $temp_GLZ . "<br> Abw El" . $Abwärme_el . "<br>";
        $temp_LeistungElement = $temp_LeistungElement * $temp_GLZ;
        $LeistungImRaum = distribute($temp_LeistungElement, $LeistungImRaum, $tempNA_perElement); // LEISTUNg wird je nach Element Netzart aufgeteilt
    }// END OF ELEMENTS LOOP

    check4vorabsperr($messages, $roomParams, $elements_in_room, $check4Vorabsperrkasten);
    check_max_value_rev($messages, $roomParams, "HT_Waermeabgabe_W", $Abwärme);
    check_room_for_na($messages, $roomParams, $NetzArtenImRaum);
    check_room_Leistungssumme($messages, $roomParams, $LeistungImRaum);
}  //   eingefügt um ausgabe zum Codenn zu unterbinden 



$mysqli->close();
foreach ($messages as $messages_out) {
    echo br2nl($messages_out);
//    echo $messages_out;
}                 

