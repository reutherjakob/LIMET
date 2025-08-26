<?php
require_once "utils/_utils.php";
check_login();
/* change character set to utf8 */
$mysqli = utils_connect_sql();

// GET-Parameter sicher abholen
$untergruppenName   = filter_input(INPUT_GET, 'untergruppenName', FILTER_SANITIZE_STRING);
$untergruppenNummer = filter_input(INPUT_GET, 'untergruppenNummer', FILTER_SANITIZE_STRING);
$gruppenID          = filter_input(INPUT_GET, 'gruppenID', FILTER_VALIDATE_INT);

// Validierung
if ($gruppenID === false || $gruppenID === null) {
    die("Ungültige Gruppen-ID!");
}

// Prepared Statement
$stmt = $mysqli->prepare("
    INSERT INTO `LIMET_RB`.`tabelle_Vermerkuntergruppe`
        (`Untergruppenname`, `Untergruppennummer`, `tabelle_Vermerkgruppe_idtabelle_Vermerkgruppe`)
    VALUES (?, ?, ?)
");

if (!$stmt) {
    die("Prepare failed: " . $mysqli->error);
}

// Typbindung: "ssi" → string, string, integer
$stmt->bind_param("ssi", $untergruppenName, $untergruppenNummer, $gruppenID);

if ($stmt->execute()) {
    echo "Vermerkuntergruppe hinzugefügt!";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$mysqli->close();