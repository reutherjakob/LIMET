<?php

// Include necessary utility functions
if (!function_exists('utils_connect_sql')) {
    include "utils/_utils.php";
}

// Establish database connection
$mysqli = utils_connect_sql();

// Define input fields and their corresponding database columns
$fields = [
    'LosNr_Extern' => 'losNr',
    'LosBezeichnung_Extern' => 'losName',
    'Ausführungsbeginn' => 'losDatum',
    'Vergabesumme' => 'lotSum',
    'Vergabe_abgeschlossen' => 'lotVergabe',
    'Versand_LV' => 'lotLVSend',
    'Verfahren' => 'lotVerfahren',
    'Bearbeiter' => 'lotLVBearbeiter',
    'Notiz' => 'lotNotice',
    'Kostenanschlag' => 'kostenanschlag',
    'Budget' => 'budget',
    'tabelle_lieferant_idTABELLE_Lieferant' => 'lotAuftragnehmer'
];

// Set lotID from GET request if provided
if (!empty($_GET["lotID"])) {
    $_SESSION["lotID"] = $_GET["lotID"];
}

// Prepare SQL query
$queryFields = [];
foreach ($fields as $column => $inputField) {
    if ($inputField == 'losDatum' || $inputField == 'lotLVSend') {
        $value = date("Y-m-d", strtotime($_GET[$inputField]) ?? '');
    } elseif ($inputField == 'kostenanschlag') {
        $value = $_GET[$inputField];
    } else {
        $value = $_GET[$inputField] ?? '';
    }

    // Skip empty fields to avoid overwriting existing data
    if (!empty($value) || $inputField == 'kostenanschlag') {
        if ($column == 'tabelle_lieferant_idTABELLE_Lieferant') {
            $value = filter_input(INPUT_GET, 'lotAuftragnehmer');
            if ($value == 0) {
                continue; // Skip if lotAuftragnehmer is 0
            }
        }
        $queryFields[] = "`$column` = '$value'";
    }
}

// Add specific conditions based on input
if (filter_input(INPUT_GET, 'mkf') == 0) {
    // Add additional fields if mkf is 0
    if (!empty($_GET["lotSum"])) {
        $queryFields[] = "`Vergabesumme` = '" . $_GET["lotSum"] . "'";
    }
} else {
    // Handle mkf != 0
    if (!empty($_GET["lotSum"])) {
        $queryFields[] = "`Vergabesumme` = '" . $_GET["lotSum"] . "'";
    }
}

// Construct SQL query
if (!empty($queryFields)) {
    $sql = "UPDATE `LIMET_RB`.`tabelle_lose_extern` SET " . implode(', ', $queryFields) . " WHERE `idtabelle_Lose_Extern` = " . $_SESSION["lotID"] . ";";
    //echo $sql;

    // Execute query
    if ($mysqli->query($sql) === TRUE) {
        echo "Los erfolgreich aktualisiert!";
    } else {
        echo "Error: " . $sql . "<br>" . $mysqli->error;
    }
} else {
    echo "No fields to update.";
}

$mysqli->close();
