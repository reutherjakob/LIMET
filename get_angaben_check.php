<?php

//FUNCTIONS
function echorow($row) {
    echo '<pre>';
    print_r($row);
    echo '</pre>';
}

function check_dependency_non_zero(&$messages, $params, $param1, $param2) {
    if (isset($params[$param2]) && $params[$param2] > 0) {
        if (!isset($params[$param1]) || $params[$param1] < 1) {
            $messages[] = $params['Raumbezeichnung'] . ":" . $params['Raumnr'] . ", Warnung!: " . $param1 . " is  " . $params[$param1] . " while " . $param2 . " is " . $params[$param2] . "<br>";
        }
    }
}

function check_max_value(&$messages, $params, $param, $max_value) {
    if (isset($params[$param]) && $params[$param] > $max_value) {
        $messages[] = $params['Raumbezeichnung'] . ":" . $params['Raumnr'] . ", Warnung: " . $param . ":" . $params[$param] . " exceeds the trafo maximum of " . $max_value . ".<br>";
    }
}

function check_awg(&$messages, $params) {
    if (isset($params['Anwendungsgruppe'])) {
        if ($params['Anwendungsgruppe'] == 0) {
            
        } elseif ($params['Anwendungsgruppe'] >= 1 && (!isset($params['SV']) || $params['SV'] != 1)) {
            $messages[] = $params['Raumbezeichnung'] . ":" . $params['Raumnr'] . ":: Warnung!: SV=" . $params['SV'] . "while Anwendungsgruppe is " . $params['Anwendungsgruppe'] . ".<br>";
        } elseif ($params['Anwendungsgruppe'] == 2 && (!isset($params['ZSV']) || $params['ZSV'] != 1)) {
            $messages[] = $params['Raumbezeichnung'] . ":" . $params['Raumnr'] . ":: Warnung!: ZSV=" . $params['ZSV'] . " while Anwendungsgruppe is " . $params['Anwendungsgruppe'] . ".<br>";
        }
    }
}

function check_summe_leistungen(&$messages, $params) {
    $summe = (intval($params['ET_Anschlussleistung_AV_W']) + intval($params['ET_Anschlussleistung_SV_W']) + intval($params['ET_Anschlussleistung_ZSV_W']) + intval($params['ET_Anschlussleistung_USV_W']));
    if ($summe > intval($params['ET_Anschlussleistung_W'])) {
        $messages[] = $params['Raumbezeichnung'] . ":" . $params['Raumnr'] . ": " . $params['ET_Anschlussleistung_W'] . ":: Warnung SUMME Anschlussleistung kleiner als die Summe der Anschlussleistungen je Netzart!.<br>  ";
    }
}

//MAIN 
session_start();
include '_utils.php';
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


$messages = array();

foreach ($raumparameter as $roomID => $params) {
    if (true) { //START ROOM param CHECK  //if for forensics
//////// AWG
        check_awg($messages, $params);
////////ET 
        check_summe_leistungen($messages, $params);
        check_dependency_non_zero($messages, $params, 'IT Anbindung', 'ET_RJ45-Ports');
        check_dependency_non_zero($messages, $params, 'ET_RJ45-Ports', 'IT Anbindung');
        check_max_value($messages, $params, 'ET_Anschlussleistung_ZSV_W', 8000);
        check_dependency_non_zero($messages, $params, 'AV', 'ET_Anschlussleistung_AV_W');
        check_dependency_non_zero($messages, $params, 'AV', 'EL_AV Steckdosen Stk');
        check_dependency_non_zero($messages, $params, 'SV', 'ET_Anschlussleistung_SV_W');
        check_dependency_non_zero($messages, $params, 'SV', 'EL_SV Steckdosen Stk');
        check_dependency_non_zero($messages, $params, 'ZSV', 'ET_Anschlussleistung_ZSV_W');
        check_dependency_non_zero($messages, $params, 'ZSV', 'EL_ZSV Steckdosen Stk');
        check_dependency_non_zero($messages, $params, 'USV', 'ET_Anschlussleistung_USV_W');
        check_dependency_non_zero($messages, $params, 'USV', 'EL_USV Steckdosen Stk');
//////MEDGAS
        check_dependency_non_zero($messages, $params, '1 Kreis O2', 'O2');
        check_dependency_non_zero($messages, $params, '1 Kreis Va', 'VA');
        check_dependency_non_zero($messages, $params, '1 Kreis DL-5', 'DL-5');

        check_dependency_non_zero($messages, $params, 'O2', '1 Kreis O2');
        check_dependency_non_zero($messages, $params, 'VA', '1 Kreis Va');
        check_dependency_non_zero($messages, $params, 'DL-5', '1 Kreis DL-5');

        check_dependency_non_zero($messages, $params, '1 Kreis O2', '2 Kreis O2');
        check_dependency_non_zero($messages, $params, '1 Kreis Va', '2 Kreis Va');
        check_dependency_non_zero($messages, $params, '1 Kreis DL-5', '2 Kreis DL-5');
    }//STOP ROOM param CHECK  
    //
///// ------ LOAD ELEMENTS IN ROOM ----        
    if ($roomID === 28872) {
        echo $roomID . " " . $params['Raumbezeichnung'] . ":" . $params['Raumnr'] . "<br> "; //Forensiv    

        $stmt = $mysqli->prepare("SELECT tabelle_elemente.ElementID,  tabelle_elemente.idTABELLE_Elemente, tabelle_elemente.Bezeichnung, tabelle_varianten.Variante, Sum(tabelle_räume_has_tabelle_elemente.Anzahl) AS SummevonAnzahl
            FROM tabelle_varianten 
            INNER JOIN (tabelle_räume_has_tabelle_elemente 
            INNER JOIN tabelle_elemente ON tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente = tabelle_elemente.idTABELLE_Elemente) 
            ON tabelle_varianten.idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten
            WHERE tabelle_räume_has_tabelle_elemente.Verwendung = 1
            GROUP BY tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_varianten.Variante, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, 
            tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten, tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume
            HAVING tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume = ? AND SummevonAnzahl > 0
            ORDER BY tabelle_elemente.ElementID, tabelle_varianten.Variante");
        $stmt->bind_param("i", $roomID);
        $stmt->execute();
        $result = $stmt->get_result();
//    echo $roomID . " " . $params['Raumbezeichnung'] .":" . $params['Raumnr'] " " . $params['Raumnr']; //Forensi
        $elemets = array();
        echo "ELEMENTS <br>";
        while ($row = $result->fetch_assoc()) {
            $ElementID = $row['ElementID'];
            $elemets[$roomID] = $row;
        if(  $row['idTABELLE_Elemente'] == "954") {echo  $roomID . " " . $params['Raumbezeichnung'] .":" . $params['Raumnr']. " " . $params['Raumnr']." ".$row['Bezeichnung'];}
//            echorow($row);  //for FORENSICS 
        }
    }
}

//$stmt = $mysqli->prepare("SELECT *
//            FROM tabelle_parameter_kategorie 
//            INNER JOIN (tabelle_parameter 
//            INNER JOIN tabelle_projekt_elementparameter ON tabelle_parameter.idTABELLE_Parameter = tabelle_projekt_elementparameter.tabelle_parameter_idTABELLE_Parameter) 
//            ON tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie = tabelle_parameter.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie
//            WHERE tabelle_projekt_elementparameter.tabelle_projekte_idTABELLE_Projekte = ? AND tabelle_parameter.`Bauangaben relevant` = 1
//            GROUP BY tabelle_parameter_kategorie.Kategorie, tabelle_parameter.Bezeichnung
//            ORDER BY tabelle_parameter_kategorie.Kategorie, tabelle_parameter.Bezeichnung");
//$stmt->bind_param("i", $_SESSION["projectID"]);
//$stmt->execute();
//$result = $stmt->get_result();
//
//echo "PARAMETER KATHEGORIEN";
//$parameter_kategorien = array();
//while ($row = $result->fetch_assoc()) {
//    $parameterID = $row['idTABELLE_Parameter'];
//    $parameter_kategorien[$parameterID] = $row;
//    echorow($row);  //for FORENSICS
//}

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
                tabelle_parameter.Bezeichnung");

$stmt->bind_param("i", $_SESSION["projectID"]);
$stmt->execute();
$result = $stmt->get_result();

$elementparameter = array();
echo "ELEMENTSPARAMETERS";
while ($row = $result->fetch_assoc()) {
    $elID = $row['tabelle_elemente_idTABELLE_Elemente'];
    $elementparameter[$elID] = $row;
    if($elID=== 954){
//    echorow($row); //for FORENSICS        
    }
//            if (isset($row['Kategorie']) && $row['Kategorie'] == "MedGas" && $row['Wert'] != "") {
////            echorow($row);  //for FORENSICS
////            check_gasanschlüsse_based_on_elements_in_room($messages, $params, $row['Bezeichnung'] );
//            }
}

function check_gasanschlüsse_based_on_elements_in_room(&$messages, $params, $Bezeichnung) {
    $bezeichnungen_paramkeys = array(
        'O2 Anschluss' => 'O2',
        'CO2 Anschluss' => 'CO2',
        'DL-10 Anschluss' => 'DL-10',
        'DL-5 Anschluss' => 'DL-5',
        'N2O Anschluss' => '',
        'NGA Anschluss' => '',
        'O2 Anschluss' => '',
        'Stickstoff_Strom' => '',
        'VAC Anschluss' => '',
    );
    $paramkey = $bezeichnungen_paramkeys[$Bezeichnung];
    if (isset($params[$paramkey]) && $params[$paramkey] < 1) {
        $messages[] = $params['Raumbezeichnung'] . ":" . $params['Raumnr'] . ", Warnung: " . $Bezeichnung . " vorhanden, aber " . $paramkey . "= " . $params[$paramkey] . " <br>";
    }
}

$mysqli->close();
foreach ($messages as $messages_out) {
//    echo br2nl($messages_out);
    echo $messages_out;
}