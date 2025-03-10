<?php
if (!function_exists('utils_connect_sql')) {  include "_utils.php"; }
check_login();

$mysqli = utils_connect_sql();

$sql = "SELECT `tabelle_räume_has_tabelle_elemente`.`TABELLE_Elemente_idTABELLE_Elemente`
			FROM `LIMET_RB`.`tabelle_räume_has_tabelle_elemente`
			WHERE `tabelle_räume_has_tabelle_elemente`.`id`=" . $_GET["id"] . ";";

$result = $mysqli->query($sql);
$row = $result->fetch_assoc();
$elementID = $row["TABELLE_Elemente_idTABELLE_Elemente"];

$sql = "SELECT tabelle_projekt_varianten_kosten.Kosten
        FROM tabelle_projekt_varianten_kosten
        WHERE (((tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten)=" . $_GET["variantenID"] . ")
        AND ((tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente)=" . $elementID . ") 
        AND ((tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . "));";

$result = $mysqli->query($sql);
$row = $result->fetch_assoc();
if (null == $row["Kosten"]) {    //Variante noch nicht vorhanden
    $sql = "INSERT INTO `LIMET_RB`.`tabelle_projekt_varianten_kosten`
                            (`tabelle_projekte_idTABELLE_Projekte`,
                            `tabelle_elemente_idTABELLE_Elemente`,
                            `tabelle_Varianten_idtabelle_Varianten`,
                            `Kosten`)
                            VALUES
                            (" . $_SESSION["projectID"] . ",
                            " . $elementID . ",
                            " . $_GET["variantenID"] . ",
                            '0');";

    if ($mysqli->query($sql) === TRUE) {
        echo "Variante erfolgreich angelegt mit Kosten 0! ";
    } else {
        echo "Error: " . $sql . "<br>" . $mysqli->error;
    }
}

$sql = "UPDATE `LIMET_RB`.`tabelle_räume_has_tabelle_elemente`
			SET
                        `Standort` ='" . $_GET["standort"] . "',
                        `Verwendung`='" . $_GET["verwendung"] . "',
			`Neu/Bestand` = '" . $_GET["bestand"] . "',
			`Anzahl` = '" . $_GET["amount"] . "',
			`Kurzbeschreibung` = '" . $_GET["comment"] . "',
			`Timestamp` = '" . date("Y-m-d H:i:s") . "',
			`tabelle_Varianten_idtabelle_Varianten` = " . $_GET["variantenID"] . "
			WHERE `id` = " . $_GET["id"] . ";";

if ($mysqli->query($sql) === TRUE) {
    echo "Raumbucheintrag erfolgreich aktualisiert!";
} else {
    echo "Error: " . $sql . "<br>" . $mysqli->error;
}

$mysqli->close();
?>
