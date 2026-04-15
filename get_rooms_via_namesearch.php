<?php
// 25 FX
require_once 'utils/_utils.php';
check_login();

function getProjectName($projectID) {
    $mysqli = utils_connect_sql();
    // SQL query to fetch the project name
    $sql = "SELECT Projektname FROM tabelle_projekte WHERE idTABELLE_Projekte = ?";
    // Prepare and execute the statement
    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("i", $projectID);
        $stmt->execute();
        $stmt->bind_result($projectName);
        $stmt->fetch();
        $stmt->close();
    } else {
        // Handle errors
        return "Error: " . $mysqli->error;
    }
    $mysqli->close();
    return $projectName;
}

$mysqli = utils_connect_sql();
$table = "tabelle_räume";
$field = filter_input(INPUT_POST, 'field', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? 'Raumbezeichnung';
$search = filter_input(INPUT_POST, 'search', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? 'None';

$projectID = (int)($_SESSION['projectID'] ?? 0);

$sql = "SELECT r.*,
    COALESCE((
        SELECT SUM(pk.Kosten * rhe.Anzahl)
        FROM tabelle_räume_has_tabelle_elemente rhe
        INNER JOIN tabelle_projekt_varianten_kosten pk
            ON pk.tabelle_elemente_idTABELLE_Elemente = rhe.TABELLE_Elemente_idTABELLE_Elemente
           AND pk.tabelle_Varianten_idtabelle_Varianten = rhe.tabelle_Varianten_idtabelle_Varianten
           AND pk.tabelle_projekte_idTABELLE_Projekte = r.tabelle_projekte_idTABELLE_Projekte
        WHERE rhe.TABELLE_Räume_idTABELLE_Räume = r.idTABELLE_Räume
    ), 0) AS Gesamtkosten
FROM $table r WHERE r.$field LIKE ?";
$stmt = $mysqli->prepare($sql);
$searchParam = "%$search%";
$stmt->bind_param("s", $searchParam);

$stmt->execute();
$result = $stmt->get_result();

$data = array();
while ($row = $result->fetch_assoc()) {
    // Fetch and translate the project ID to project name
    if (isset($row['tabelle_projekte_idTABELLE_Projekte'])) {
        $row['tabelle_projekte_idTABELLE_Projekte'] = getProjectName($row['tabelle_projekte_idTABELLE_Projekte']);
    }
    $data[] = $row;
}

$stmt->close();
$mysqli->close();
header('Content-Type: application/json');
echo json_encode($data);