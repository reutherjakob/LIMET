<?php
//UTILITY METHODS
function abcTo123($char)
{
    $char = strtolower($char);
    return ord($char) - ord('a') + 1;
}

//METHODS ON ROOM PARAMETER BASIS
function check_dependency_non_zero(&$messages, $roomParams, $param1, $param2)
{
    if (isset($roomParams[$param2]) && $roomParams[$param2] > 0) {
        if (!isset($roomParams[$param1]) || $roomParams[$param1] < 1) {
            $messages[] = $roomParams['Raumbezeichnung'] . ": " . $roomParams['Raumnr'] . " --- " . $roomParams['idTABELLE_Räume'] . ":::Raumparameter-> " . $param1 . " ist  " . $roomParams[$param1] . ", aber " . $param2 . " ist " . $roomParams[$param2] . "<br>";
        }
    }
}

function check_max_value(&$messages, $roomParams, $param, $max_value)
{
    if (isset($roomParams[$param]) && $roomParams[$param] > $max_value) {
        $messages[] = $roomParams['Raumbezeichnung'] . ": " . $roomParams['Raumnr'] . " --- " . $roomParams['idTABELLE_Räume'] . ":::LeistungZSV-> " . $param . "..." . $roomParams[$param] . "übersteigt max=" . $max_value . ".<br>";
    }
}

function check_max_value_rev(&$messages, $roomParams, $param, $max_value, $extraInp = "")
{     //TODO-> check out what happens if param aint set.
    if (!isset($roomParams[$param]) || $roomParams[$param] < $max_value) {
        $messages[] = $roomParams['Raumbezeichnung'] . ": " . $roomParams['Raumnr'] . " --- " . $roomParams['idTABELLE_Räume'] . ":::Abwärme" . $extraInp . "-> " . $max_value . " übersteigt Raumangabe=" . $roomParams[$param] . ".<br>";
    }
}

function check_RG(&$messages, $roomParams)
{
    if (isset($roomParams['Anwendungsgruppe'])) {
        if ($roomParams['Anwendungsgruppe'] >= 1 && (!isset($roomParams['SV']) || $roomParams['SV'] != 1)) {
            $messages[] = $roomParams['Raumbezeichnung'] . ": " . $roomParams['Raumnr'] . " --- " . $roomParams['idTABELLE_Räume'] . ":::RG -> SV=" . $roomParams['SV'] . ", aber RG =" . $roomParams['Anwendungsgruppe'] . ".<br>";
        }
        if ($roomParams['Anwendungsgruppe'] == 2 && (!isset($roomParams['ZSV']) || $roomParams['ZSV'] != 1)) {
            $messages[] = $roomParams['Raumbezeichnung'] . ": " . $roomParams['Raumnr'] . " --- " . $roomParams['idTABELLE_Räume'] . ":::RG = 2 -> ZSV=" . $roomParams['ZSV'] . ", aber RG =" . $roomParams['Anwendungsgruppe'] . ".<br>";
        }
        if ($roomParams['Anwendungsgruppe'] == 2 && (!isset($roomParams['Fussboden OENORM B5220']) || $roomParams['Fussboden OENORM B5220'] != "Klasse 1")) {
            $messages[] = $roomParams['Raumbezeichnung'] . ": " . $roomParams['Raumnr'] . " --- " . $roomParams['idTABELLE_Räume'] . ":::RG = 2 -> Fußboden muss Klasse 1 sein, ist aber " . $roomParams['Fussboden OENORM B5220'] . ".<br>";
        }
        if ($roomParams['Anwendungsgruppe'] != 2 && $roomParams['Fussboden OENORM B5220'] === "Klasse 1") {
            $messages[] = $roomParams['Raumbezeichnung'] . ": " . $roomParams['Raumnr'] . " --- " . $roomParams['idTABELLE_Räume'] . ":::RG is nicht 2 -> Muss der Fussboden OENORM B5220 hier Klasse 1 sein? " . $roomParams['Fussboden OENORM B5220'] . ".<br>";
        }
    }
}

function check_summe_leistungen(&$messages, $roomParams)
{
    $summe = (intval($roomParams['ET_Anschlussleistung_AV_W']) + intval($roomParams['ET_Anschlussleistung_SV_W']) + intval($roomParams['ET_Anschlussleistung_ZSV_W']) + intval($roomParams['ET_Anschlussleistung_USV_W']));
    if ($summe != intval($roomParams['ET_Anschlussleistung_W'])) {
        $messages[] = $roomParams['Raumbezeichnung'] . ": " . $roomParams['Raumnr'] . " --- " . $roomParams['idTABELLE_Räume'] . ":::Leistung ∑-> " . $roomParams['ET_Anschlussleistung_W'] . "= ∑Anschlussleistung(Raum) != ∑P je Netzart! (" . $summe . "=" . (intval($roomParams['ET_Anschlussleistung_AV_W']) . "+" . intval($roomParams['ET_Anschlussleistung_SV_W']) . "+" . intval($roomParams['ET_Anschlussleistung_ZSV_W']) . "+" . intval($roomParams['ET_Anschlussleistung_USV_W'])) . ")<br>  ";
    }
}

// METHODS ON ELEMENT BASIS
function check_room_for_parameters_cause_elementParamKathegorie(&$messages, $roomParams, $element_parameter_id, $row)
{
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
        $messages[] = $roomParams['Raumbezeichnung'] . ": " . $roomParams['Raumnr'] . " --- " . $roomParams['idTABELLE_Räume'] . ":::ElementPort-> " . $param_name . " Element " . $row['Bezeichnung'] . " präsent, aber Raumparameter=" . $param_name . " " . $roomParams[$param_name] . "! <br>";
    }
}

function getComponents($input)
{
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

function getUniqueComponents($newComponents, $existingComponents)
{
    foreach ($newComponents as $component) {
        if (!in_array($component, $existingComponents)) {
            array_push($existingComponents, $component);
        }
    }
    return $existingComponents;
}

function check_room_for_na(&$messages, $roomParams, $NAimRaum)
{
    foreach ($NAimRaum as $na) {
        if (!isset($roomParams[$na]) || $roomParams[$na] < 1) {
            $messages[] = $roomParams['Raumbezeichnung'] . ": " . $roomParams['Raumnr'] . " --- " . $roomParams['idTABELLE_Räume'] . ":::Netzarten-> " . $na . " in  Element präsent, aber Raumparameter=0!<br>";
        }
    }
}

function check_4_room_param(&$messages, $roomParams, $theonetobechecked4, $row)
{ // Strahlenanwendung
    if (!isset($roomParams[$theonetobechecked4]) || $roomParams[$theonetobechecked4] < 1) {
        $messages[] = $roomParams['Raumbezeichnung'] . ": " . $roomParams['Raumnr'] . " --- " . $roomParams['idTABELLE_Räume'] . ":::Raumparameter-> Element " . $row['Bezeichnung'] . " präsent, aber Raumparam " . $theonetobechecked4 . "= " . $roomParams[$theonetobechecked4] . "! <br>";
    }
}

function check_4_room_paramz(&$messages, $roomParams, $theonetobechecked4, $the2ndtobechecked4, $row)
{ // Strahlenanwendung
    if ((!isset($roomParams[$theonetobechecked4]) || $roomParams[$theonetobechecked4] < 1) && (!isset($roomParams[$the2ndtobechecked4]) || $roomParams[$the2ndtobechecked4] < 1)) {
        $messages[] = $roomParams['Raumbezeichnung'] . ": " . $roomParams['Raumnr'] . " --- " . $roomParams['idTABELLE_Räume'] . ":::Raumparameter-> Element " . $row['Bezeichnung'] . " präsent, aber Raumparam " . $theonetobechecked4 . "/" . $the2ndtobechecked4 . "= " . $roomParams[$theonetobechecked4] . "! <br>";
    }
}

function check4vorabsperr(&$messages, $roomParams, $elements_in_room)
{
    //echorow( $roomParams);
    // stativ check > bREMSENß
    if (intval($roomParams["1 Kreis DL-5"]) == 0) {
        $messages[] = $roomParams['Raumbezeichnung'] . ": " . $roomParams['Raumnr'] . " --- " . $roomParams['idTABELLE_Räume'] . ":::Stativ ->  Stativ, braucht Druckluft! <br>";
    }

    $found = false;
    foreach ($elements_in_room as $el) {
        if ($el["idTABELLE_Elemente"] == 664) {
            $found = true;
            break;
        }
    }
    if (!$found) {
        $messages[] = $roomParams['Raumbezeichnung'] . ": " . $roomParams['Raumnr'] . " --- " . $roomParams['idTABELLE_Räume'] . ":::ElementPort -> Gasanschluss am Stativ, braucht Vorabsperrkasten! <br>";
    }

}

function check_room_Leistungssumme(&$messages, $roomParams, $P, $extraInp = "")
{
    $mapping = array("NoNA", "AV", "SV", "ZSV", "USV");  //Structure i chose for the array of power within room
    $summe = 0;
//    echorow($P);
    foreach ($P as $index => $P_jeNA) {
        if ($index > 0 && $index < 5) {
            if ($P_jeNA > $roomParams['ET_Anschlussleistung_' . $mapping[$index] . '_W']) {
                $messages[] = $roomParams['Raumbezeichnung'] . ": " . $roomParams['Raumnr'] . " --- " . $roomParams['idTABELLE_Räume'] . ":::Leistung ∑" . $extraInp . "-> ∑P[" . $mapping[$index] . "](Elemente) =" . $P_jeNA . " > " . $mapping[$index] . "_W=" . $roomParams['ET_Anschlussleistung_' . $mapping[$index] . '_W'] . " <br>";
            }
            if ($P_jeNA > 8000 && $index === 3) {
                $messages[] = $roomParams['Raumbezeichnung'] . ": " . $roomParams['Raumnr'] . " --- " . $roomParams['idTABELLE_Räume'] . ":::Leistung ∑" . $extraInp . "-> P[" . $mapping[$index] . "](Elemente) > 8kW! <br>";
            }
        } else {
            //if ($P_jeNA > 0) {
            //    $messages[] =  $roomParams['Raumbezeichnung'] . ": " . $roomParams['Raumnr'] . " --- ".$roomParams['idTABELLE_Räume'] . ":::Element without NA -> Set parameter!<br>";
            //}
        }
        $summe += $P_jeNA;
        if ($P_jeNA === 0 && $index > 0) {
            if ($roomParams[$mapping[$index]] === 1) {
                $messages[] = $roomParams['Raumbezeichnung'] . ": " . $roomParams['Raumnr'] . " --- " . $roomParams['idTABELLE_Räume'] . ":::Leistung der Elemente im Raum" . $extraInp . " -> P[Elemente][" . $mapping[$index] . "]=0, aber Raumparameter=1? Element parametrisieren oder NA aus Raum hinterfragen <br>";
            }
        }
    }
    //useless?? 8.1.25
    //    $messages[] = $roomParams['Raumbezeichnung'] . ": " . $roomParams['Raumnr'] . " --- ".$roomParams['idTABELLE_Räume'] . ":::LEISTUNG∑-> RAUM ∑P=" . $summe . "!<br>";
    //if ($summe > $roomParams['ET_Anschlussleistung_W']) {
    //    $messages[] = $roomParams['Raumbezeichnung'] . ": " . $roomParams['Raumnr'] . " --- " . $roomParams['idTABELLE_Räume'] . ":::Leistung ∑ ". $extraInp."->  ∑P(alle NA".$extraInp.") =" . $summe . " > Raum Anschlussleistung= " . $roomParams['ET_Anschlussleistung_W'] ."<br>";
    //}
}

function distribute($x, $P, $NAs)
{
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

function unitMultiplier($text)
{
    if (strpos($text, 'k') !== false) {
        return 1000;
    } else if (strpos($text, 'K') !== false) {
        return 1000;
    } else {
        return 1;
    }
}

function getQueryParam($param)
{
    return isset($_GET[$param]) ? $_GET[$param] : null;
}

//   -------------  MAIN  ---------------

if (!function_exists('utils_connect_sql')) {
    include "_utils.php";
}
check_login();
$messages = array();
$roomIDsArray = array();


$roomID = getQueryParam('roomID');
if ($roomID !== null) {
    $roomIDsArray = explode(',', $roomID);
}


$mysqli = utils_connect_sql();
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

// --- CONST DEFINITIONS ---
$strahlenIDs = array(60, // Röntgenaufnahmegerät digital - fahrbar,2.41.13.1
    167,    // C-Bogen - fahrbar,2.42.10.1
    168,    // Röntgenaufnahmesystem - Deckenstativ,1.41.12.1
    170,    // Röntgenaufnahmesystem - 3D Deckenstativ,1.41.12.2
    259,    // Röntgendiagnostik - System - Durchleuchtung,1.42.10.1
    317,    // SPECT,1.47.10.1
    484,    // Kontrastmittelinjektor CT - deckenmontiert,1.49.10.1
    489,    // SPECT/CT,1.47.15.1
    579,    // Angiographieanlage - Kardiologisch - 2 Ebenen, 1.42.13.1
    580,    // Angiographieanlage - Radiologisch, 1.42.12.1
    1182,   // Panoramaröntgensystem - Boden/Decke,1.41.12.3
    1388,   // Angiographieanlage - Kardiologisch - 1 Ebene,1.42.13.2
    1390,   // Röntgendetektorhalterung - fahrbar, 2.41.13.4
    1417,   // Mammographie - System,1.46.10.1
    1461,   // Röntgenraster Wandhalterung,1.41.10.2
    1462,   // Linearbeschleuniger-System,1.71.10.1
    1660,   // Unterkonstruktion Angiographieanlage - Kardiologisch,1.42.13.3
    1680);  // Uroskopie - System - Durchleuchtung,1.42.10.5

$CEE_IDs = array(60, 167);

$bezeichnungMappings = [
    "digestori" => "HT_Abluft_Digestorium_Stk",
    "sicherheitsschrank" => ["HT_Abluft_Sicherheitsschrank_Stk", "HT_Abluft_Sicherheitsschrank_Unterbau_Stk"]
];

$elementMappings = [     //based on unique sql Id idTABELLE_Elemente
    "67" => "1 Kreis O2",
    "68" => "1 Kreis Va",
    "69" => "1 Kreis DL-5",
    "161" => "N2O",
    "162" => "NGA",
    "163" => "DL-10",
    "342" => "CO2",
    "64" => "1 Kreis O2",
    "65" => "1 Kreis Va",
    "66" => "1 Kreis DL-5",
    "75" => "1 Kreis O2",
    "76" => "1 Kreis Va",
    "77" => "1 Kreis DL-5",
    "202" => "NGA",
    "203" => "N2O",
    "288" => "DL-10",
    "289" => "CO2",
    "1090" => "CO2",
    "1086" => "1 Kreis O2",
    "1087" => "1 Kreis Va",
    "1088" => "1 Kreis DL-5",
    "1089" => "CO2",
    "1103" => "1 Kreis O2",
    "1104" => "1 Kreis Va",
    "1105" => "1 Kreis DL-5",
    "1106" => "CO2",
    "1327" => "N2",

    "168" => "1 Kreis DL-5",  // Stative
    "170" => "1 Kreis DL-5",
    "12" => "1 Kreis DL-5",
    "485" => "1 Kreis DL-5",
    "680" => "1 Kreis DL-5",
    "907" => "1 Kreis DL-5",
    "1001" => "1 Kreis DL-5",
    "1074" => "1 Kreis DL-5",
    "1553" => "1 Kreis DL-5",
    "1654" => "1 Kreis DL-5",
    "154" => "1 Kreis DL-5",
    "155" => "1 Kreis DL-5",
    "165" => "1 Kreis DL-5",
    "194" => "1 Kreis DL-5",
    "233" => "1 Kreis DL-5",
    "286" => "1 Kreis DL-5",
    "287" => "1 Kreis DL-5",
    "393" => "1 Kreis DL-5",
    "1076" => "1 Kreis DL-5"
];

$elementMappingsElementID = [  //based on the initial numbers ElementID
    "2.34.19" => "Laseranwendung",
    "2.56.16" => "Laseranwendung"];


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
    //////// RG 
    check_RG($messages, $roomParams);
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
    $LeistungImRaumExkl = array(0, 0, 0, 0, 0); // ALLGEMEIN / AV/SV/ZSV/USV
    $Abwarme = 0;
    $AbwarmeExkl = 0;
    $check4Vorabsperrkasten = false;
    $elements_counter = 0;

    while ($row = $result->fetch_assoc()) {  //ELEMENTS IN THE ROOM 
        $elements_in_room[$elements_counter] = $row;
        $elements_counter++;
        // echo abcTo123($elements_in_room[$roomID]['Variante']) . "</br> ";
        //  echorow($row);  //for FORENSICS
        // echorow($elementParamInfos);

        if (in_array($row['idTABELLE_Elemente'], $strahlenIDs)) {
            check_4_room_param($messages, $roomParams, "Strahlenanwendung", $row);
            if (in_array($row['idTABELLE_Elemente'], $CEE_IDs)) {
                check_4_room_param($messages, $roomParams, "EL_Roentgen 16A CEE Stk", $row);
            }
        }

        foreach ($bezeichnungMappings as $keyword => $params) {  // Digestorium und Sicherheitsschrank Abluft
            if (stripos($row['Bezeichnung'], $keyword) !== false) {
                if (is_array($params)) {
                    check_4_room_paramz($messages, $roomParams, $params[0], $params[1], $row);
                } else {
                    check_4_room_param($messages, $roomParams, $params, $row);
                }
            }
        }

        foreach ($elementMappings as $prefix => $param) {  // Entnahmestellen
            if (intval($row['idTABELLE_Elemente']) == $prefix) {
                check_4_room_param($messages, $roomParams, $param, $row);
            }
        }

        foreach ($elementMappingsElementID as $prefix => $param) { // Laser check
            if (str_starts_with($row['ElementID'], $prefix)) {
                check_4_room_param($messages, $roomParams, $param, $row);
            }
        }

        $temp_LeistungElement = 0;
        $tempNA_perElement = "";
        $temp_GLZ = 1.00;
        $Abwarme_el = 0;
        $AnzahlElImRaum = $row['SummevonAnzahl'];

        foreach ($elementParamInfos as $parameterInfo) {
            if ($parameterInfo["tabelle_Varianten_idtabelle_Varianten"] === abcTo123($row['Variante']) && $row['idTABELLE_Elemente'] === $parameterInfo["tabelle_elemente_idTABELLE_Elemente"]) {

                if ($parameterInfo['idTABELLE_Parameter_Kategorie'] === 2) { //ElektroTechnik                    
                    if ($parameterInfo['idTABELLE_Parameter'] === 127) {
                        // Param: RJ-ports. (El. mit Rj im Raum)
                        check_room_for_parameters_cause_elementParamKathegorie($messages, $roomParams, $parameterInfo['idTABELLE_Parameter'], $row); //check for if room got that plug. 
                    }
                    if ($parameterInfo['idTABELLE_Parameter'] === 82) {
                        // Pram: Netzart
                        $tempNA_perElement = array_merge(getComponents($parameterInfo['Wert']), getComponents($parameterInfo['Einheit']));
                        $NetzArtenImRaum = getUniqueComponents($tempNA_perElement, $NetzArtenImRaum);
                    }
                    if ($parameterInfo['idTABELLE_Parameter'] === 18) {
                        // Pram: NennLeistung
                        $temp_LeistungElement = floatval(str_replace(",", ".", preg_replace("/[^0-9.,]/", "", $parameterInfo['Wert']))) * unitMultiplier($parameterInfo["Einheit"]);
                    }
                    if ($parameterInfo['idTABELLE_Parameter'] === 133) {
                        //GLeichzeitigkeit, default = 1
                        $temp_GLZ = floatval(str_replace(",", ".", preg_replace("/[^0-9,.]/", "", $parameterInfo['Wert'])));
                    }
                }
                if ($parameterInfo['idTABELLE_Parameter_Kategorie'] === 3) { //HKLS
                    if ($parameterInfo["idTABELLE_Parameter"] === 9) {
                        //Param: ABWÄRME
                        $Abwarme_el = floatval(str_replace(",", ".", preg_replace("/[^0-9.,]/", "", $parameterInfo['Wert']))) * unitMultiplier($parameterInfo["Einheit"]);
                    }
                }
                if ($parameterInfo['idTABELLE_Parameter_Kategorie'] === 12) {  //MEDGAS
                    check_room_for_parameters_cause_elementParamKathegorie($messages, $roomParams, $parameterInfo['idTABELLE_Parameter'], $row);   //check for if room got that plug.

                    if (stripos($row['Bezeichnung'], "stativ") && !$check4Vorabsperrkasten) {
                        $check4Vorabsperrkasten = true;
                      // echo "\n <br>" . $row['Bezeichnung'] . "\n <br>";
                    }
                }
            }
        }// END OF PARAM PER ELEMENT LOOP 
        $Abwarme += $Abwarme_el * $temp_GLZ * $AnzahlElImRaum; //
        $AbwarmeExkl += $Abwarme_el * $AnzahlElImRaum; // ohne GLZ
        $LeistungInklGLZ = $temp_LeistungElement * $temp_GLZ * $AnzahlElImRaum;
        $LeistungExklGLZ = $temp_LeistungElement * $AnzahlElImRaum;// ohne GLZ

        $LeistungImRaum = distribute($LeistungInklGLZ, $LeistungImRaum, $tempNA_perElement); // LEISTUNg wird je nach Element Netzart aufgeteilt

        $LeistungImRaumExkl = distribute($LeistungExklGLZ, $LeistungImRaumExkl, $tempNA_perElement); // LEISTUNg wird je nach Element Netzart aufgeteilt// ohne GLZ

        if ($temp_LeistungElement > 0) {
            if (empty($tempNA_perElement)) {
                $messages[] = $roomParams['Raumbezeichnung'] . ": " . $roomParams['Raumnr'] . " --- " . $roomParams['idTABELLE_Räume'] . ":::Netzarten->  " . $row['Bezeichnung'] . " hat Leistung aber keine Netzart!<br>";
            }
//            echo "ELEMENTS in ROOM: " . $roomID . " " . $roomParams['Raumbezeichnung'] . ": " . $roomParams['Raumnr'] . " --- " . $roomParams['idTABELLE_Räume'] . "\t\t\t<br>";
//            echorow($LeistungImRaum);
//            echo "El: " . $row['Bezeichnung'] . "- GLZ: " . $temp_GLZ . "- Leistung: " . $temp_LeistungElement . "- Anzahl: " . $AnzahlElImRaum . "<br> "; //"<br> Abw El" . $Abwarme_el . 
        }

    }// END OF ELEMENTS LOO

    if ($check4Vorabsperrkasten) {
        check4vorabsperr($messages, $roomParams, $elements_in_room);
    }
    check_max_value_rev($messages, $roomParams, "HT_Waermeabgabe_W", $Abwarme, " (INKL. GLZ)");
    check_max_value_rev($messages, $roomParams, "HT_Waermeabgabe_W", $AbwarmeExkl, " (EXKL. GLZ)");// ohne GLZ

//    echorow($NetzArtenImRaum);
    check_room_for_na($messages, $roomParams, $NetzArtenImRaum);

    check_room_Leistungssumme($messages, $roomParams, $LeistungImRaum, " (INKL. GLZ)");
    check_room_Leistungssumme($messages, $roomParams, $LeistungImRaumExkl, " (EXKL. GLZ)");

}   //end of foreach Rooom 
$mysqli->close();

foreach ($messages as $messages_out) {
    echo "\n";
    echo "<br>";
    echo br2nl($messages_out);
}                 


 