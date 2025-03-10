<?php
if (!function_exists('utils_connect_sql')) {  include "_utils.php"; }
check_login();
$mysqli = utils_connect_sql();

// Vorhandene Elementparameter löschen
$sqlDelete = "DELETE FROM tabelle_elemente_has_tabelle_parameter WHERE (((tabelle_elemente_has_tabelle_parameter.TABELLE_Elemente_idTABELLE_Elemente)=" . filter_input(INPUT_GET, 'elementID') . "));";

if ($mysqli->query($sqlDelete) === TRUE) {
    echo "Zentrale Elementparameter gelöscht!";
} else {
    echo "Error: " . $sqlDelete . "<br>" . $mysqli->error;
}


//Elementparameter aus Projekt laden
$sql = "SELECT tabelle_projekt_elementparameter.tabelle_parameter_idTABELLE_Parameter, tabelle_projekt_elementparameter.Wert, tabelle_projekt_elementparameter.Einheit
                FROM tabelle_projekt_elementparameter
                WHERE (((tabelle_projekt_elementparameter.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . ") AND "
    . "((tabelle_projekt_elementparameter.tabelle_elemente_idTABELLE_Elemente)=" . filter_input(INPUT_GET, 'elementID') . ") AND ((tabelle_projekt_elementparameter.tabelle_Varianten_idtabelle_Varianten)=" . filter_input(INPUT_GET, 'variantenID') . "));";

$result = $mysqli->query($sql);
$elementParameters = array();
while ($row = $result->fetch_assoc()) {
    $elementParameters[$row['tabelle_parameter_idTABELLE_Parameter']]['tabelle_parameter_idTABELLE_Parameter'] = $row['tabelle_parameter_idTABELLE_Parameter'];
    $elementParameters[$row['tabelle_parameter_idTABELLE_Parameter']]['Wert'] = $row['Wert'];
    $elementParameters[$row['tabelle_parameter_idTABELLE_Parameter']]['Einheit'] = $row['Einheit'];
}

// Elementparameter in zentrales Projekt speichern
foreach ($elementParameters as $data) {
    $sql = "INSERT INTO `LIMET_RB`.`tabelle_elemente_has_tabelle_parameter`
			(`TABELLE_Elemente_idTABELLE_Elemente`,
			`TABELLE_Parameter_idTABELLE_Parameter`,			
			`Wert`,
			`Einheit`,
                        `TABELLE_Planungsphasen_idTABELLE_Planungsphasen`)
			VALUES
			(" . filter_input(INPUT_GET, 'elementID') . ",
                         " . $data["tabelle_parameter_idTABELLE_Parameter"] . ",
                         '" . $data["Wert"] . "',
                         '" . $data["Einheit"] . "', 1);";

    if ($mysqli->query($sql) === TRUE) {
        echo "\nParameter " . $data['Wert'] . " " . $data['Einheit'] . " zu Variante hinzugefügt!";
    } else {
        echo "Error: " . $sql . "<br>" . $mysqli->error;
    }
}


$mysqli->close();
?>
