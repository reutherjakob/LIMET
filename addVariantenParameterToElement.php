<?php
// 25 FX
require_once 'utils/_utils.php';
check_login();
$mysqli = utils_connect_sql();

$elementID = getPostInt('elementID');
 //   filter_input(INPUT_GET, 'elementID', FILTER_VALIDATE_INT);
$variantenID =  getPostInt('variantID');
    //filter_input(INPUT_GET, 'variantenID', FILTER_VALIDATE_INT);
$projectID = $_SESSION["projectID"];

// Vorhandene Elementparameter löschen
$sqlDelete = "DELETE FROM tabelle_elemente_has_tabelle_parameter WHERE TABELLE_Elemente_idTABELLE_Elemente = ?";
$stmtDelete = $mysqli->prepare($sqlDelete);
$stmtDelete->bind_param("i", $elementID);

if ($stmtDelete->execute()) {
    echo "Zentrale Elementparameter gelöscht!";
} else {
    echo "Error: " . $stmtDelete->error;
}
$stmtDelete->close();

// Elementparameter aus Projekt laden, inklusive Bezeichnung
$sql = "
    SELECT pep.tabelle_parameter_idTABELLE_Parameter, pep.Wert, pep.Einheit, p.Bezeichnung
    FROM tabelle_projekt_elementparameter pep
    JOIN tabelle_parameter p ON pep.tabelle_parameter_idTABELLE_Parameter = p.idTABELLE_Parameter
    WHERE pep.tabelle_projekte_idTABELLE_Projekte = ?
      AND pep.tabelle_elemente_idTABELLE_Elemente = ?
      AND pep.tabelle_Varianten_idtabelle_Varianten = ?
";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("iii", $projectID, $elementID, $variantenID);
$stmt->execute();
$result = $stmt->get_result();

$elementParameters = array();
while ($row = $result->fetch_assoc()) {
    $elementParameters[] = $row;
}
$stmt->close();

$insertSql = "
    INSERT INTO LIMET_RB.tabelle_elemente_has_tabelle_parameter
    (TABELLE_Elemente_idTABELLE_Elemente, TABELLE_Parameter_idTABELLE_Parameter, Wert, Einheit, TABELLE_Planungsphasen_idTABELLE_Planungsphasen)
    VALUES (?, ?, ?, ?, 1)
";
$stmtInsert = $mysqli->prepare($insertSql);

foreach ($elementParameters as $data) {
    $stmtInsert->bind_param(
        "iiss",
        $elementID,
        $data['tabelle_parameter_idTABELLE_Parameter'],
        $data['Wert'],
        $data['Einheit']
    );

    if ($stmtInsert->execute()) {
        echo "\nParameter '{$data['Bezeichnung']}' ({$data['Wert']} {$data['Einheit']}) zu Element hinzugefügt!";
    } else {
        echo "Error: " . $stmtInsert->error;
    }
}
$stmtInsert->close();

$mysqli->close();
