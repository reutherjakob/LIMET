<?php
// 25 FX
include "utils/_utils.php";
check_login();
$mysqli = utils_connect_sql();

$type = getPostString('type', '');
$kurzbeschreibung = getPostString('kurzbeschreibung', '');
$hersteller = getPostInt('hersteller', 0);
$deviceID = getPostInt('deviceID', 0);

if ($deviceID < 1) {
    die("Ungültige Geräte-ID");
}

$stmt = $mysqli->prepare(
    "UPDATE `LIMET_RB`.`tabelle_geraete`
     SET `Typ` = ?, `Kurzbeschreibung` = ?, `Änderung` = ?, `tabelle_hersteller_idtabelle_hersteller` = ?
     WHERE `idTABELLE_Geraete` = ?"
);

$datum = date('Y-m-d');
$stmt->bind_param('sssii', $type, $kurzbeschreibung, $datum, $hersteller, $deviceID);

if ($stmt->execute()) {
    echo "Gerät gespeichert!";
} else {
    echo "Fehler: " . $stmt->error;
}

$stmt->close();
$mysqli->close();


?>
