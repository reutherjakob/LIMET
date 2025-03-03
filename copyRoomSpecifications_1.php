<?php

session_start();
include '_utils.php';
check_login();
$mysqli = utils_connect_sql();

// Decode JSON data
$roomIDs = json_decode($_POST["rooms"], true);
$columnsDefinition = json_decode($_POST["columns"], true);
$ausgabe = "";

$excludeColumns = [// Define columns to exclude
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
$excludedTitles = array_map(function ($column) use ($columnsDefinition) {
    foreach ($columnsDefinition as $colDef) {
        if ($colDef['data'] === $column) {
            return $colDef['title'];
        }
    }
    return $column;
}, array_slice($excludeColumns, 3)); // Dont show the user the IDS 

// Abfrage der zu kopierenden Raumdaten
$columns = array_filter($columnsDefinition, function ($column) use ($excludeColumns) {
    return !in_array($column['data'], $excludeColumns);
});
$columns = array_map(function ($column) {
    return "`" . $column['data'] . "`";
}, $columns);
$columnsList = implode(", ", $columns);

$sql = "SELECT $columnsList, `Anwendungsgruppe`, `Anmerkung MedGas`, `Anmerkung Elektro`, `Anmerkung HKLS`, `Anmerkung Geräte`, `Anmerkung BauStatik` FROM tabelle_räume WHERE ((tabelle_räume.idTABELLE_Räume)=" . $_SESSION["roomID"] . ");";
$result = $mysqli->query($sql);
$row = $result->fetch_assoc();

// Create a list of copied column names
$copiedColumns = array_map(function ($column) use ($columnsDefinition) {
    foreach ($columnsDefinition as $colDef) {
        if ($colDef['data'] === trim($column, '`')) {
            return $colDef['title'];
        }
    }
    return trim($column, '`');
}, $columns);

// Add additional columns to the copied columns list
$additionalColumns = ['Anwendungsgruppe', 'Anmerkung MedGas', 'Anmerkung Elektro', 'Anmerkung HKLS', 'Anmerkung Geräte', 'Anmerkung BauStatik'];
$copiedColumns = array_merge($copiedColumns, $additionalColumns);

foreach ($roomIDs as $valueOfRoomID) {
    $update_sql = "UPDATE tabelle_räume SET ";
    foreach ($columns as $columnName) {
        $columnName = trim($columnName, '`');
        if (isset($row[$columnName]) && $columnName !== "") {
            $update_sql .= "`$columnName` = '{$row[$columnName]}', ";
        }
    }
    // Include the additional columns
    $update_sql .= "`Anwendungsgruppe` = '{$row['Anwendungsgruppe']}', ";
    $update_sql .= "`Anmerkung MedGas` = '{$row['Anmerkung MedGas']}', ";
    $update_sql .= "`Anmerkung Elektro` = '{$row['Anmerkung Elektro']}', ";
    $update_sql .= "`Anmerkung HKLS` = '{$row['Anmerkung HKLS']}', ";
    $update_sql .= "`Anmerkung Geräte` = '{$row['Anmerkung Geräte']}', ";
    $update_sql .= "`Anmerkung BauStatik` = '{$row['Anmerkung BauStatik']}' ";

    $update_sql = rtrim($update_sql, ', ');
    $update_sql .= " WHERE idTABELLE_Räume = $valueOfRoomID";

    if ($mysqli->query($update_sql) === TRUE) {
        $ausgabe .= "Raum $valueOfRoomID erfolgreich aktualisiert! \n";
    } else {
        $ausgabe .= "Error: " . $update_sql . "<br>" . $mysqli->error;
    }
}

// Add the list of copied columns to the ausgabe
$ausgabe .= "\nKopierte Spalten: " . implode(", ", $copiedColumns) . "\n";
$ausgabe .= "Ausgeschlossene Spalten: " . implode(", ", $excludedTitles) . "\n";
$ausgabe .= "\n Änderungswünsche? Gerne :)";
$mysqli->close();
echo $ausgabe;
?>
