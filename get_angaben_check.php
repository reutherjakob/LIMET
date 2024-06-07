<?php
session_start();
include '_utils.php';
$mysqli= utils_connect_sql();
        
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





function check_dependency_non_zero(&$messages, $raumparameter, $param1, $param2) {
    foreach ($raumparameter as $roomID => $params) {
        if (isset($params[$param2]) && $params[$param2] > 0) {
            if (!isset($params[$param1]) || $params[$param1] != 1) {
                $messages[] = "Room ID: " . $roomID. " " .$params['Raumbezeichnung']. ", Error: " . $param1 . " is  ". $params[$param1]. " while " . $param2 . " is ".$params[$param2]."\n";
            } 
        }   
    }
}

function check_max_value(&$messages, $raumparameter, $param, $max_value) {
    foreach ($raumparameter as $roomID => $params) {
        if (isset($params[$param]) && $params[$param] > $max_value) {
            $messages[] = "Room ID: " . $roomID . " " .$params['Raumbezeichnung']. ", Warning: " . $param .":" .  $params[$param]. " exceeds the trafo maximum of " . $max_value . ".\n";
        }
    }
}

function check_awg(&$messages, $raumparameter) {
    foreach ($raumparameter as $roomID => $params) {
        if (isset($params['Anwendungsgruppe'])) {
            if ($params['Anwendungsgruppe'] == 0) {
                continue;
            } elseif ($params['Anwendungsgruppe'] == 1) {
                if (!isset($params['SV']) || $params['SV'] != 1 ) {
                    $messages[] = "Room ID: " . $roomID .  " " .$params['Raumbezeichnung'].", Error: SV=0 while Anwendungsgruppe is " . $params['Anwendungsgruppe'] . ".\n";
                }
            } elseif ($params['Anwendungsgruppe'] == 2) {
                if (!isset($params['ZSV']) || $params['ZSV'] != 1) {
                    $messages[] = "Room ID: " . $roomID .  " " .$params['Raumbezeichnung'].", Error: USV=0 while Anwendungsgruppe is " . $params['Anwendungsgruppe'] . ".\n";
                }
            }
        }
    }
}

$out_messages = array();
// AWG
check_awg($messages, $raumparameter);
//ET
check_dependency_non_zero($out_messages, $raumparameter, 'IT Anbindung', 'ET_RJ45-Ports');
 
check_max_value($out_messages, $raumparameter, 'ET_Anschlussleistung_ZSV_W', 8000);
check_max_value($out_messages, $raumparameter, 'ET_Anschlussleistung_USV_W', 8000);

check_dependency_non_zero($out_messages, $raumparameter, 'AV', 'ET_Anschlussleistung_AV_W');
check_dependency_non_zero($out_messages, $raumparameter, 'AV', 'EL_AV Steckdosen Stk');
check_dependency_non_zero($out_messages, $raumparameter, 'SV', 'ET_Anschlussleistung_SV_W');
check_dependency_non_zero($out_messages, $raumparameter, 'SV', 'EL_SV Steckdosen Stk');
check_dependency_non_zero($out_messages, $raumparameter, 'ZSV', 'ET_Anschlussleistung_ZSV_W');
check_dependency_non_zero($out_messages, $raumparameter, 'ZSV', 'EL_ZSV Steckdosen Stk');
check_dependency_non_zero($out_messages, $raumparameter, 'USV', 'ET_Anschlussleistung_USV_W');
check_dependency_non_zero($out_messages, $raumparameter, 'USV', 'EL_USV Steckdosen Stk');
//MEDGAS
check_dependency_non_zero($out_messages, $raumparameter, '1 Kreis O2', '2 Kreis O2');
check_dependency_non_zero($out_messages, $raumparameter, '1 Kreis Va', '2 Kreis Va');
check_dependency_non_zero($out_messages, $raumparameter, '1 Kreis DL-5', '2 Kreis DL-5');

 

//// -------------------------Elemente im Raum laden-------------------------- 
//$sql = "SELECT tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_varianten.Variante, Sum(tabelle_räume_has_tabelle_elemente.Anzahl) AS SummevonAnzahl,
//    tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten
//    FROM tabelle_varianten INNER JOIN (tabelle_räume_has_tabelle_elemente INNER JOIN tabelle_elemente ON 
//    tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente = tabelle_elemente.idTABELLE_Elemente) ON
//    tabelle_varianten.idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten
//    WHERE (((tabelle_räume_has_tabelle_elemente.Verwendung)=1))
//    GROUP BY tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_varianten.Variante, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, 
//    tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten, tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume
//    HAVING (((tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume)=" . $valueOfRoomID . ") AND SummevonAnzahl > 0)
//    ORDER BY tabelle_elemente.ElementID, tabelle_varianten.Variante;";
//$result2 = $mysqli->query($sql);
//// -----------------Projekt Elementparameter/Variantenparameter laden----------------------------
//$sql = "SELECT tabelle_parameter_kategorie.Kategorie, tabelle_parameter.Bezeichnung, tabelle_parameter.idTABELLE_Parameter, tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie, tabelle_parameter.Abkuerzung
//    FROM tabelle_parameter_kategorie INNER JOIN (tabelle_parameter INNER JOIN tabelle_projekt_elementparameter ON tabelle_parameter.idTABELLE_Parameter = tabelle_projekt_elementparameter.tabelle_parameter_idTABELLE_Parameter) 
//    ON tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie = tabelle_parameter.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie
//    WHERE (((tabelle_projekt_elementparameter.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . ") AND tabelle_parameter.`Bauangaben relevant` = 1)
//    GROUP BY tabelle_parameter_kategorie.Kategorie, tabelle_parameter.Bezeichnung
//    ORDER BY tabelle_parameter_kategorie.Kategorie, tabelle_parameter.Bezeichnung;";
//$result3 = $mysqli->query($sql);



$mysqli->close();
foreach ($out_messages as $message) {
    echo $out_messages;
}
