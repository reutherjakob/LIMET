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

$sql = "SELECT * FROM $table WHERE $field LIKE ?";
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
