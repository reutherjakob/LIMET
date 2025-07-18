<?php
session_start();
require_once 'utils/_utils.php';
//check_login();

$mysqli = utils_connect_sql();

$sql = "UPDATE tabelle_räume SET tabelle_räume.`Anmerkung FunktionBO` = '".br2nl($_GET["funktionBO"])."', tabelle_räume.`Anmerkung Geräte` = '".br2nl($_GET["geraete"])."', tabelle_räume.`Anmerkung BauStatik` = '".br2nl($_GET["baustatik"])."', ";
$sql.= "tabelle_räume.`Anmerkung Elektro` = '".br2nl($_GET["Elektro"])."', tabelle_räume.`Anmerkung MedGas` = '".br2nl($_GET["medgas"])."', tabelle_räume.`Anmerkung HKLS` = '".br2nl($_GET["hkls"])."' ";
$sql.= "WHERE (((tabelle_räume.idTABELLE_Räume)=".$_SESSION["roomID"]."));";

if ($mysqli ->query($sql) === TRUE) {
    echo "Raum erfolgreich aktualisiert!";
} else {
    echo "Error: " . $sql . "<br>" . $mysqli->error;
}

$mysqli ->close();	
					
?>
