<?php

session_start();
include '_utils.php';
check_login();
$mysqli = utils_connect_sql();

//$stmt = $mysqli->prepare("UPDATE tabelle_räume SET Bauabschnitt = 'F' 
//                              WHERE tabelle_projekte_idTABELLE_Projekte = ? 
//                              AND (Bauabschnitt LIKE '%Haus F%' OR Bauabschnitt IN ('O', 'M', 'N', 'W'))");

$stmt = $mysqli->prepare("UPDATE tabelle_räume SET Geschoss = 'OG1' 
                              WHERE tabelle_projekte_idTABELLE_Projekte = ? 
                              AND `Raumbereich Nutzer` LIKE 'Chirurgische Tagesklinik'");
$BBE = 80; 
$KHI = 75; 
$stmt->bind_param('i', $KHI);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo "Column updated successfully.";
} else {
    echo "No rows updated.";
}

$stmt->close();
$mysqli->close();

/*<?php


function searchDatabase($dbName, $tableName, $fieldNames, $searchString) {
    $conn = new mysqli('localhost', 'username', 'password', $dbName);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $fields = $fieldNames ? implode(", ", $fieldNames) : '*';
    $conditions = [];
    foreach ($fieldNames as $field) {
        $conditions[] = "$field LIKE ?";
    }
    $sql = "SELECT $fields FROM $tableName WHERE " . implode(" OR ", $conditions);
    $stmt = $conn->prepare($sql);
    $searchTerm = "%$searchString%";
    $types = str_repeat('s', count($fieldNames));
    $stmt->bind_param($types, ...array_fill(0, count($fieldNames), $searchTerm));
    $stmt->execute();
    $result = $stmt->get_result();
    $data = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }
    $stmt->close();
    $conn->close();
    return $data;
}

function echoLastWord($string) {
    $words = explode(' ', trim($string));
    echo end($words);
}

function handleDuplicates($mysqli) {
    $data = loadDataFromSQL($mysqli);
    $originalIds = [];
    $uniqueIds = [];
    $updatedRows = [];

    foreach ($data as $row) {
        $raumnr = $row['Raumnr'];
        $counter = 1;
        $newId = $raumnr;
        while (in_array($newId, $uniqueIds)) {
            $newId = $raumnr . '_' . strval($counter);
            $counter++;
        }
        $uniqueIds[] = $newId;
        $updatedRows[] = ['id' => $row['idTABELLE_Räume'], 'newId' => $newId];
    }

    updateSQLTable($mysqli, $updatedRows);
    printValidation($originalIds, $uniqueIds);
}

function loadDataFromSQL($mysqli) {
    $stmt = $mysqli->prepare("SELECT idTABELLE_Räume, Raumnr, Raumnummer_Nutzer 
                              FROM tabelle_räume
                              WHERE tabelle_projekte_idTABELLE_Projekte = ? 
                              AND Bauabschnitt LIKE ?");
    $projectID = $_SESSION['projectID'];
    $bauabschnitt = '%Haus F%';
    $stmt->bind_param('is', $projectID, $bauabschnitt);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    $stmt->close();
    return $data;
}

function updateSQLTable($mysqli, $updatedRows) {
    foreach ($updatedRows as $row) {
        $stmt = $mysqli->prepare("UPDATE tabelle_räume SET Raumnummer_Nutzer = ? 
                                  WHERE idTABELLE_Räume = ? 
                                  AND tabelle_projekte_idTABELLE_Projekte = ?");
        $id2pass = substr($row['newId'], -10);
        $projectID = $_SESSION['projectID'];
        $stmt->bind_param('sii', $id2pass, $row['id'], $projectID);
        $stmt->execute();
        echo "Updated idTABELLE_Räume " . $row['id'] . " with new Raumnummer_Nutzer " . $row['newId'] . "<br>";
        $stmt->close();
    }
}

function printValidation($originalIds, $uniqueIds) {
    echo "Original IDs: " . implode(',<br> ', $originalIds) . "\n";
    echo "Unique IDs: " . implode(',<br> ', $uniqueIds) . "\n";
}

$mysqli = utils_connect_sql();
handleDuplicates($mysqli);
$mysqli->close();
?>
*/

?>


