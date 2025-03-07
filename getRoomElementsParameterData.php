<?php

session_start();
if (!function_exists('utils_connect_sql')) {  include "_utils.php"; }
check_login();

$roomID = filter_input(INPUT_GET, 'roomID');
//$K2R = filter_input(INPUT_GET, 'K2Return');
//$K2Ret = explode(",", $K2R);  //WORKS if not json.stringify

$K2Return = $_GET['K2Return'];
$K2Ret = json_decode($K2Return); 

$mysqli = utils_connect_sql();
$sql = "SELECT tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_varianten.Variante, Sum(tabelle_räume_has_tabelle_elemente.Anzahl) AS SummevonAnzahl,
            tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten
            FROM tabelle_varianten INNER JOIN (tabelle_räume_has_tabelle_elemente INNER JOIN tabelle_elemente ON 
            tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente = tabelle_elemente.idTABELLE_Elemente) ON
            tabelle_varianten.idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten
            WHERE (((tabelle_räume_has_tabelle_elemente.Verwendung)=1))
            GROUP BY tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_varianten.Variante, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, 
            tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten, tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume
            HAVING (((tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume)=" . $roomID . ") AND SummevonAnzahl > 0)
            ORDER BY tabelle_elemente.ElementID, tabelle_varianten.Variante;";
$tabelle_elemente = $mysqli->query($sql);
// -----------------Projekt Elementparameter/Variantenparameter laden----------------------------
$sql = "SELECT tabelle_parameter_kategorie.Kategorie,tabelle_parameter.Abkuerzung, tabelle_parameter.Bezeichnung, tabelle_parameter.idTABELLE_Parameter, tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie, tabelle_parameter.Abkuerzung
            FROM tabelle_parameter_kategorie INNER JOIN (tabelle_parameter INNER JOIN tabelle_projekt_elementparameter ON tabelle_parameter.idTABELLE_Parameter = tabelle_projekt_elementparameter.tabelle_parameter_idTABELLE_Parameter) 
            ON tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie = tabelle_parameter.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie
            WHERE (((tabelle_projekt_elementparameter.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . ") AND tabelle_parameter.`Bauangaben relevant` = 1)
            GROUP BY tabelle_parameter_kategorie.Kategorie, tabelle_parameter.Bezeichnung
            ORDER BY tabelle_parameter_kategorie.Kategorie, tabelle_parameter.Bezeichnung;";
$tabelle_parameter_kategorie = $mysqli->query($sql);  
$paramInfos = array();
while ($row = $tabelle_parameter_kategorie->fetch_assoc()) { 
    $CheckKategorie = $row['idTABELLE_Parameter_Kategorie'];
    if (in_array($CheckKategorie, $K2Ret) || $K2Ret == "") {
        $paramInfos[$row['idTABELLE_Parameter']]['ParamID'] = $row['idTABELLE_Parameter'];
        $paramInfos[$row['idTABELLE_Parameter']]['KategorieID'] = $CheckKategorie; 
        $paramInfos[$row['idTABELLE_Parameter']]['Bezeichnung'] = $row['Abkuerzung'];
        $paramInfos[$row['idTABELLE_Parameter']]['Kategorie'] = $row['Kategorie'];
    }
}
// -------------------------Elemente parameter ------------------------- 
$sql = "SELECT tabelle_projekt_elementparameter.tabelle_elemente_idTABELLE_Elemente, tabelle_projekt_elementparameter.Wert, tabelle_projekt_elementparameter.Einheit, tabelle_projekt_elementparameter.tabelle_Varianten_idtabelle_Varianten, 
            tabelle_parameter.Bezeichnung, tabelle_parameter_kategorie.Kategorie, tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie, tabelle_parameter.idTABELLE_Parameter 
            FROM tabelle_parameter_kategorie INNER JOIN (tabelle_parameter INNER JOIN tabelle_projekt_elementparameter ON tabelle_parameter.idTABELLE_Parameter = tabelle_projekt_elementparameter.tabelle_parameter_idTABELLE_Parameter) 
            ON tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie = tabelle_parameter.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie
            WHERE (((tabelle_projekt_elementparameter.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . ") AND tabelle_parameter.`Bauangaben relevant` = 1)
            ORDER BY tabelle_parameter_kategorie.Kategorie, tabelle_parameter.Bezeichnung;";
$tabelle_projekt_elementparameter = $mysqli->query($sql);   // $elementParamInfos
$mysqli->close();

$elementParamInfos = array();
$elementParamInfosCounter = 0;
while ($row = $tabelle_projekt_elementparameter->fetch_assoc()) {
    $elementParamInfos[$elementParamInfosCounter]['KategorieID'] = $row['idTABELLE_Parameter_Kategorie'];
    $elementParamInfos[$elementParamInfosCounter]['ParamID'] = $row['idTABELLE_Parameter'];
    $elementParamInfos[$elementParamInfosCounter]['elementID'] = $row['tabelle_elemente_idTABELLE_Elemente'];
    $elementParamInfos[$elementParamInfosCounter]['variantenID'] = $row['tabelle_Varianten_idtabelle_Varianten'];
    $elementParamInfos[$elementParamInfosCounter]['Wert'] = $row['Wert'];
    $elementParamInfos[$elementParamInfosCounter]['Einheit'] = $row['Einheit'];
    $elementParamInfosCounter = $elementParamInfosCounter + 1;
}

$result = array();
while ($row = $tabelle_elemente->fetch_assoc()) {
    $elementData = array();
    foreach ($row as $key => $value) {
        $elementData[$key] = $value;
    }
    foreach ($paramInfos as $paramInfo) {
        $values = array();
        foreach ($elementParamInfos as $elementParamInfo) {
            if ($elementParamInfo['ParamID'] == $paramInfo['ParamID'] && $elementParamInfo['elementID'] == $row['TABELLE_Elemente_idTABELLE_Elemente'] && $elementParamInfo['variantenID'] == $row['tabelle_Varianten_idtabelle_Varianten']) {
                $values[] = $elementParamInfo['Wert'] . "" . $elementParamInfo['Einheit'];
            }
        }
        // If the values array is empty, replace it with an empty string
        // If the values array contains a single value, replace it with that value
        // If the values array contains multiple values, replace it with the last value
        if (empty($values)) {
            $values = "";
        } elseif (count($values) == 1) {
            $values = $values[0];
        } else {
            $values = end($values);
        }
        $elementData[$paramInfo['Bezeichnung']] = $values;
    }
    $result[] = $elementData;
}
$json_result = json_encode($result, JSON_PRETTY_PRINT);
header('Content-Type: application/json');
echo $json_result;

