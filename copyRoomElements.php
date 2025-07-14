<?php
if (!function_exists('utils_connect_sql')) {
    include "utils/_utils.php";
}
check_login();

$mysqli = utils_connect_sql();

$roomIDs = $_GET["rooms"];
$ausgabe = "";
foreach ($roomIDs as $valueOfRoomID) {
    $sql = "INSERT INTO `tabelle_räume_has_tabelle_elemente`
                        (`TABELLE_Räume_idTABELLE_Räume`,
                        `TABELLE_Elemente_idTABELLE_Elemente`,
                        `Neu/Bestand`,
                        `Anzahl`,
                        `Standort`,
                        `Verwendung`,
                        `Timestamp`,
                        `tabelle_Varianten_idtabelle_Varianten`)
                        SELECT " . $valueOfRoomID . ",
                            `tabelle_räume_has_tabelle_elemente`.`TABELLE_Elemente_idTABELLE_Elemente`,
                            `tabelle_räume_has_tabelle_elemente`.`Neu/Bestand`,
                            `tabelle_räume_has_tabelle_elemente`.`Anzahl`,
                            `tabelle_räume_has_tabelle_elemente`.`Standort`,
                            `tabelle_räume_has_tabelle_elemente`.`Verwendung`,
                            '" . date("Y-m-d H:i:s") . "',    
                            `tabelle_räume_has_tabelle_elemente`.`tabelle_Varianten_idtabelle_Varianten`
                        FROM `LIMET_RB`.`tabelle_räume_has_tabelle_elemente`
                        WHERE `tabelle_räume_has_tabelle_elemente`.`TABELLE_Räume_idTABELLE_Räume` = " . $_SESSION["roomID"] . ";";

    if ($mysqli->query($sql) === TRUE) {
        $ausgabe = $ausgabe . "Raum " . $valueOfRoomID . " erfolgreich aktualisiert! \n";
    } else {
        $ausgabe = "Error: " . $sql . "<br>" . $mysqli->error;
    }
}
$mysqli->close();

echo $ausgabe;


?>
