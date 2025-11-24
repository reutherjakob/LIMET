<?php

// 25 FX
session_start();
require_once 'utils/_utils.php';
check_login();

$mysqli = utils_connect_sql();

// Sanitize and validate posted JSON inputs
$roomIDs = json_decode($_POST["rooms"], true);
if (!is_array($roomIDs)) {
    die("Invalid rooms data");
}

// Sanitize roomIDs array as integers
$roomIDs = array_filter($roomIDs, function ($id) {
    return filter_var($id, FILTER_VALIDATE_INT) !== false;
});

if (empty($roomIDs)) {
    die("No valid room IDs found");
}

$columnsDefinition = json_decode($_POST["columns"], true);
if (!is_array($columnsDefinition)) {
    die("Invalid columns data");
}

// Columns to exclude from user visibility and copying
$excludeColumns = [
    'TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen',
    'tabelle_projekte_idTABELLE_Projekte',
    'idTABELLE_Räume',
    'Nummer',
    'Bezeichnung',
    'Raumbezeichnung',
    'Raumnr',
    'Funktionelle Raum Nr',
    'Raumnummer_Nutzer',
    'Raumbereich Nutzer',
    'Geschoss',
    'Bauetappe',
    'Bauabschnitt',
    'Nutzfläche',
    'Raumhoehe',
    'Raumhoehe 2',
    'Belichtungsfläche',
    'Umfang',
    'Volumen'
];

// Translate excluded columns to user-friendly titles omitting ID columns at front 3 indices
$excludedTitles = array_map(function ($column) use ($columnsDefinition) {
    foreach ($columnsDefinition as $colDef) {
        if ($colDef['data'] === $column) {
            return $colDef['title'];
        }
    }
    return $column;
}, array_slice($excludeColumns, 3));

// Filter columns to copy: exclude those in $excludeColumns and prepare DB field list for query
$columns = array_filter($columnsDefinition, function ($column) use ($excludeColumns) {
    return !in_array($column['data'], $excludeColumns);
});
$columns = array_map(function ($column) {
    return "`" . $column['data'] . "`";
}, $columns);
$columnsList = implode(", ", $columns);

// Securely prepare SQL to fetch values for those columns for the current session room
$sql = "SELECT $columnsList, `Anwendungsgruppe`, `Anmerkung MedGas`, `Anmerkung Elektro`, `Anmerkung HKLS`, `Anmerkung Geräte`, `Anmerkung BauStatik`
        FROM tabelle_räume WHERE idTABELLE_Räume = ?";

$stmt = $mysqli->prepare($sql);
$sessionRoomID = filter_var($_SESSION["roomID"] ?? null, FILTER_VALIDATE_INT);
if ($sessionRoomID === false || $sessionRoomID === null) {
    die("Invalid session room ID");
}
$stmt->bind_param("i", $sessionRoomID);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    die("Room data not found");
}
$stmt->close();

// Columns user sees copied for info output
$copiedColumns = array_map(function ($column) use ($columnsDefinition) {
    foreach ($columnsDefinition as $colDef) {
        if ($colDef['data'] === trim($column, '`')) {
            return $colDef['title'];
        }
    }
    return trim($column, '`');
}, $columns);

// Append additional special columns
$additionalColumns = ['Anwendungsgruppe', 'Anmerkung MedGas', 'Anmerkung Elektro', 'Anmerkung HKLS', 'Anmerkung Geräte', 'Anmerkung BauStatik'];
$copiedColumns = array_merge($copiedColumns, $additionalColumns);

$ausgabe = "";

// Prepare update statement skeleton with placeholders for all columns + WHERE condition
$updateSetParts = [];
$bindTypes = "";
$bindValues = [];

foreach ($columns as $column) {
    $name = trim($column, '`');
    $updateSetParts[] = "`$name` = ?";
    $bindTypes .= "s";
    $bindValues[] = $row[$name] ?? null;
}

// Append additional columns to update list
foreach ($additionalColumns as $col) {
    $updateSetParts[] = "`$col` = ?";
    $bindTypes .= "s";
    $bindValues[] = $row[$col] ?? null;
}

$updateSet = implode(", ", $updateSetParts);
$updateSql = "UPDATE tabelle_räume SET $updateSet WHERE idTABELLE_Räume = ?";

$updStmt = $mysqli->prepare($updateSql);

foreach ($roomIDs as $valueOfRoomID) {
    // Bind parameters dynamically (add the roomID at the end)
    $bindParams = array_merge($bindValues, [$valueOfRoomID]);
    // Use references
    $bindParamsRefs = [];
    foreach ($bindParams as $key => $value) {
        $bindParamsRefs[$key] = &$bindParams[$key];
    }
    // Add integer type for roomID
    $fullBindTypes = $bindTypes . "i";

    // Bind params dynamically
    array_unshift($bindParamsRefs, $fullBindTypes);
    call_user_func_array([$updStmt, 'bind_param'], $bindParamsRefs);

    if ($updStmt->execute()) {
        $ausgabe .= "Raum " . htmlspecialchars($valueOfRoomID) . " erfolgreich aktualisiert!<br>";
    } else {
        $ausgabe .= "Error (Raum " . htmlspecialchars($valueOfRoomID) . "): " . htmlspecialchars($updStmt->error) . "<br>";
    }
}

$updStmt->close();
$mysqli->close();

$ausgabe .= "\nKopierte Spalten: " . implode(", ", $copiedColumns);
$ausgabe .= "\nAusgeschlossene Spalten: " . implode(", ", $excludedTitles);
$ausgabe .= "\nÄnderungswünsche? Gerne :)";

echo $ausgabe;
?>
