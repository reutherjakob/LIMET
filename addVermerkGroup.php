<?php
// 26 FX - unchecked
require_once '_utils/_utils.php';
check_login();
require_once '_utils/_csrf.php';
require_csrf();



$gruppenName      = getPostString('gruppenName');
$gruppenart      = getPostString('gruppenart');
$gruppenOrt      = getPostString('gruppenOrt');
$gruppenVerfasser = getPostString('gruppenVerfasser');;
$gruppenStart    = getPostString('gruppenStart');
$gruppenEnde     = getPostString('gruppenEnde');
$gruppenDatum    = getPostString('gruppenDatum');
$projectID = (int)$_SESSION["projectID"];

$mysqli = utils_connect_sql();
$sql = "INSERT INTO `LIMET_RB`.`tabelle_Vermerkgruppe` 
        (`Gruppenname`, `Gruppenart`, `Ort`, `Verfasser`, `Startzeit`, `Endzeit`, `Datum`, `tabelle_projekte_idTABELLE_Projekte`)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $mysqli->prepare($sql);
if (!$stmt) {
    die("Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error);
}
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

if (!$stmt->execute()) {
    echo("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
} else {
    echo "Vermerkgruppe hinzugefügt!\n" . $sql;
}

$stmt->close();
$mysqli->close();


