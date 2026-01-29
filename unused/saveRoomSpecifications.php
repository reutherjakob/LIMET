<?php
function br2nl($string)
{
    $return = str_replace(array("\r\n", "\n\r", "\r", "\n"), "<br/>", $string);
    return $return;
}

require_once 'utils/_utils.php';
check_login();

$mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

/* change character set to utf8 */
if (!$mysqli->set_charset("utf8")) {
    echo "Error loading character set utf8: " . $mysqli->error;
    exit();
}

//$sql = "UPDATE view_Raeume_has_Elemente SET view_Raeume_has_Elemente.Kurzbeschreibung = '".$_GET["comment"]."', view_Raeume_has_Elemente.Anzahl = '".$_GET["amount"]."' WHERE (((view_Raeume_has_Elemente.id)=".$_GET["id"]."))";
$sql = "UPDATE tabelle_räume SET tabelle_räume.`Anmerkung FunktionBO` = '" . br2nl($_GET["funktionBO"]) . "', tabelle_räume.`Anmerkung Geräte` = '" . br2nl($_GET["geraete"]) . "', tabelle_räume.`Anmerkung BauStatik` = '" . br2nl($_GET["baustatik"]) . "', ";
$sql .= "tabelle_räume.`Anmerkung Elektro` = '" . br2nl($_GET["Elektro"]) . "', tabelle_räume.`Anmerkung MedGas` = '" . br2nl($_GET["medgas"]) . "', tabelle_räume.`Anmerkung HKLS` = '" . br2nl($_GET["hkls"]) . "', tabelle_räume.Abdunkelbarkeit = '" . $_GET["abdunkelbarkeit"] . "', ";
$sql .= "tabelle_räume.Strahlenanwendung = '" . $_GET["strahlenanwendung"] . "', tabelle_räume.Laseranwendung = '" . $_GET["laseranwendung"] . "', tabelle_räume.Anwendungsgruppe = '" . $_GET["awg"] . "', tabelle_räume.AV = '" . $_GET["av"] . "', tabelle_räume.SV = '" . $_GET["sv"] . "', ";
$sql .= "tabelle_räume.ZSV = '" . $_GET["zsv"] . "', tabelle_räume.USV = '" . $_GET["usv"] . "', tabelle_räume.H6020 = '" . $_GET["h6020"] . "', tabelle_räume.ISO = '" . $_GET["iso"] . "', tabelle_räume.GMP = '" . $_GET["gmp"] . "', tabelle_räume.`Allgemeine Hygieneklasse` = '" . $_GET["hygieneklasse"] . "', tabelle_räume.`1 Kreis O2` = '" . $_GET["kreiso2_1"] . "', ";
$sql .= "tabelle_räume.`2 Kreis O2` = '" . $_GET["kreiso2_2"] . "', tabelle_räume.`1 Kreis Va` = '" . $_GET["kreisva_1"] . "', tabelle_räume.`2 Kreis Va` = '" . $_GET["kreisva_2"] . "', tabelle_räume.`1 Kreis DL-5` = '" . $_GET["kreisdl5_1"] . "', tabelle_räume.`2 Kreis DL-5` = '" . $_GET["kreisdl5_2"] . "', ";
$sql .= "tabelle_räume.`DL-10` = '" . $_GET["dl10"] . "', tabelle_räume.`DL-tech` = '" . $_GET["dltech"] . "', tabelle_räume.CO2 = '" . $_GET["co2"] . "', tabelle_räume.NGA = '" . $_GET["nga"] . "', tabelle_räume.N2O = '" . $_GET["n2o"] . "', tabelle_räume.`Fussboden OENORM B5220` = '" . $_GET["fussbodenklasse"] . "', tabelle_räume.`IT Anbindung` = '" . $_GET["it"] . "', ";
$sql .= "tabelle_räume.`Gebaeude_Bestand` = '" . $_GET["bestandGeb"] . "', tabelle_räume.`RaumNr_Bestand` = '" . $_GET["bestandNr"] . "', tabelle_räume.`AR_Schwingungsklasse` = '" . $_GET["schwingungsklasse"] . "', tabelle_räume.`HT_Notdusche` = '" . $_GET["notdusche"] . "', ";
$sql .= "tabelle_räume.`Raumtyp BH` = '" . $_GET["raumTypBH"] . "', tabelle_räume.`ET_EMV_ja-nein` = '" . $_GET["emv"] . "', tabelle_räume.`EL_Leistungsbedarf_W_pro_m2` = '" . $_GET["leistungET"] . "', tabelle_räume.`HT_Waermeabgabe` = '" . $_GET["waermeHT"] . "', tabelle_räume.`HT_Luftwechsel 1/h` = '" . $_GET["lwr"] . "', ";
$sql .= "tabelle_räume.`H2` = '" . $_GET["H2"] . "', tabelle_räume.`He` = '" . $_GET["He"] . "', tabelle_räume.`He-RF` = '" . $_GET["HeRF"] . "', tabelle_räume.`Ar` = '" . $_GET["Ar"] . "', tabelle_räume.`N2` = '" . $_GET["N2"] . "', tabelle_räume.`HT_Geraeteabluft m3/h` = '" . $_GET["gereateAbluftHT"] . "', tabelle_räume.`HT_Kühlwasserleistung_W` = '" . $_GET["kuehlwasserLeistungHT"] . "' ";
$sql .= "WHERE (((tabelle_räume.idTABELLE_Räume)=" . $_SESSION["roomID"] . "));";


if ($mysqli->query($sql) === TRUE) {
    echo "Raum erfolgreich aktualisiert!";
} else {
    echo "Error: " . $sql . "<br>" . $mysqli->error;
}

$mysqli->close();

?>
