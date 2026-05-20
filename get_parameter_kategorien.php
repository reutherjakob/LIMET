<?php
// Returns all parameter categories actually used in the current project
require_once 'utils/_utils.php';
check_login();

$projectID = (int)($_SESSION["projectID"] ?? 0);
if (!$projectID) {
    http_response_code(400);
    die(json_encode(["error" => "Kein Projekt."]));
}

$mysqli = utils_connect_sql();

$stmt = $mysqli->prepare("
    SELECT DISTINCT
        k.idTABELLE_Parameter_Kategorie AS id,
        k.Kategorie                      AS label
    FROM tabelle_parameter_kategorie k
    INNER JOIN tabelle_parameter p
        ON k.idTABELLE_Parameter_Kategorie = p.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie
    INNER JOIN tabelle_projekt_elementparameter pep
        ON p.idTABELLE_Parameter = pep.tabelle_parameter_idTABELLE_Parameter
    WHERE pep.tabelle_projekte_idTABELLE_Projekte = ?
    ORDER BY k.Kategorie
");
$stmt->bind_param("i", $projectID);
$stmt->execute();
$rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$mysqli->close();

header('Content-Type: application/json');
echo json_encode($rows, JSON_PRETTY_PRINT);
