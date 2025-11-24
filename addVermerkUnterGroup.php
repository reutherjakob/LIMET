<?php
// 25 FX
require_once "utils/_utils.php";
check_login();
$mysqli = utils_connect_sql();

$untergruppenName   = getPostString('untergruppenName', '');
$untergruppenNummer = getPostString('untergruppenNummer', '');
$gruppenID          = getPostInt('gruppenID', 0);

if ($gruppenID ===0) {
    die("Ungültige Gruppen-ID!");
}

$stmt = $mysqli->prepare("
    INSERT INTO `LIMET_RB`.`tabelle_Vermerkuntergruppe`
        (`Untergruppenname`, `Untergruppennummer`, `tabelle_Vermerkgruppe_idtabelle_Vermerkgruppe`)
    VALUES (?, ?, ?)
");

if (!$stmt) {
    die("Prepare failed: " . $mysqli->error);
}

$stmt->bind_param("ssi", $untergruppenName, $untergruppenNummer, $gruppenID);

if ($stmt->execute()) {
    echo "Vermerkuntergruppe hinzugefügt!";
} else {
    echo "Error: " . $stmt->error;
}
$stmt->close();
$mysqli->close();