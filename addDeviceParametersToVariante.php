<?php
// 25 FX
include "utils/_utils.php";
check_login();
$mysqli = utils_connect_sql();

// Prepare and execute deletion of existing variant parameters safely
$stmtDelete = $mysqli->prepare("
    DELETE FROM tabelle_projekt_elementparameter
    WHERE tabelle_projekte_idTABELLE_Projekte = ? 
      AND tabelle_elemente_idTABELLE_Elemente = ? 
      AND tabelle_Varianten_idtabelle_Varianten = ?
");
$stmtDelete->bind_param("iii", $_SESSION["projectID"], $_SESSION["elementID"], $_SESSION["variantenID"]);

if ($stmtDelete->execute()) {
    echo "Variantenparameter gelöscht!";
} else {
    echo "Error bei Löschen: " . $stmtDelete->error;
}
$stmtDelete->close();

// Select device parameters securely
$stmtSelect = $mysqli->prepare("
    SELECT TABELLE_Parameter_idTABELLE_Parameter, Wert, Einheit
    FROM tabelle_geraete_has_tabelle_parameter
    WHERE TABELLE_Geraete_idTABELLE_Geraete = ?
");
$stmtSelect->bind_param("i", $_SESSION["deviceID"]);
$stmtSelect->execute();
$result = $stmtSelect->get_result();

$deviceParameters = [];
while ($row = $result->fetch_assoc()) {
    $deviceParameters[] = $row;
}
$stmtSelect->close();

// Prepare insert statement for variant parameters
$stmtInsert = $mysqli->prepare("
    INSERT INTO tabelle_projekt_elementparameter 
        (tabelle_projekte_idTABELLE_Projekte, tabelle_elemente_idTABELLE_Elemente, tabelle_parameter_idTABELLE_Parameter, Wert, Einheit, tabelle_Varianten_idtabelle_Varianten, tabelle_planungsphasen_idTABELLE_Planungsphasen)
    VALUES (?, ?, ?, ?, ?, ?, 1)
");

foreach ($deviceParameters as $data) {
    $stmtInsert->bind_param(
        "iiissi",
        $_SESSION["projectID"],
        $_SESSION["elementID"],
        $data["TABELLE_Parameter_idTABELLE_Parameter"],
        $data["Wert"],
        $data["Einheit"],
        $_SESSION["variantenID"]
    );
    if ($stmtInsert->execute()) {
        echo "\nParameter " . htmlspecialchars($data['Wert'], ENT_QUOTES, 'UTF-8') . " " . htmlspecialchars($data['Einheit'], ENT_QUOTES, 'UTF-8') . " zu Variante hinzugefügt!";
    } else {
        echo "Error beim Einfügen: " . $stmtInsert->error;
    }
}

$stmtInsert->close();
$mysqli->close();
?>
