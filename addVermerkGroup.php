<?php
include_once "utils/_utils.php";
check_login();
$mysqli = utils_connect_sql();

if (filter_input(INPUT_GET, 'gruppenFortsetzung') != 0) {
    echo "Gruppenfortsetzung gewählt!";
} else {
// Assuming $mysqli is your mysqli connection object

// Prepare an SQL statement with placeholders
    $sql = "INSERT INTO `LIMET_RB`.`tabelle_Vermerkgruppe` 
        (`Gruppenname`, `Gruppenart`, `Ort`, `Verfasser`, `Startzeit`, `Endzeit`, `Datum`, `tabelle_projekte_idTABELLE_Projekte`)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

// Prepare the statement
    $stmt = $mysqli->prepare($sql);

    if (!$stmt) {
        die("Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error);
    }

// Retrieve and sanitize inputs
    $gruppenName = filter_input(INPUT_GET, 'gruppenName', FILTER_SANITIZE_STRING);
    $gruppenart = filter_input(INPUT_GET, 'gruppenart', FILTER_SANITIZE_STRING);
    $gruppenOrt = filter_input(INPUT_GET, 'gruppenOrt', FILTER_SANITIZE_STRING);
    $gruppenVerfasser = filter_input(INPUT_GET, 'gruppenVerfasser', FILTER_SANITIZE_STRING);
    $gruppenStart = filter_input(INPUT_GET, 'gruppenStart', FILTER_SANITIZE_STRING);  // Adjust filter if date/time format expected
    $gruppenEnde = filter_input(INPUT_GET, 'gruppenEnde', FILTER_SANITIZE_STRING);   // Adjust filter if date/time format expected
    $gruppenDatum = filter_input(INPUT_GET, 'gruppenDatum', FILTER_SANITIZE_STRING);  // Adjust filter if date format expected
    $projectID = $_SESSION["projectID"];

// Bind parameters to the statement (all are assumed strings except $projectID which may be int - adjust types accordingly)
// Types: s = string, i = integer
    $stmt->bind_param(
        "sssssssi",
        $gruppenName,
        $gruppenart,
        $gruppenOrt,
        $gruppenVerfasser,
        $gruppenStart,
        $gruppenEnde,
        $gruppenDatum,
        $projectID
    );

// Execute the statement
    if (!$stmt->execute()) {
        echo("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
    } else {
        echo "Vermerkgruppe hinzugefügt!\n".$sql;
    }

    $stmt->close();
    $mysqli->close();

}

