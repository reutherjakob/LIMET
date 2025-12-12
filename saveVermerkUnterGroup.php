<?php
// 25 FX
require_once "utils/_utils.php";
check_login();

$mysqli = utils_connect_sql();

$untergruppenName = getPostString("untergruppenName");
$untergruppenNummer = getPostString("untergruppenNummer");
$untergruppenID = getPostInt("untergruppenID");

$stmt = $mysqli->prepare("
    UPDATE `LIMET_RB`.`tabelle_Vermerkuntergruppe`
    SET `Untergruppenname` = ?,
        `Untergruppennummer` = ?
    WHERE `idtabelle_Vermerkuntergruppe` = ? ");
if (!$stmt) {
    die("Prepare failed: " . $mysqli->error);
}
$stmt->bind_param("ssi", $untergruppenName, $untergruppenNummer, $untergruppenID);
if ($stmt->execute()) {
    echo "Vermerkuntergruppe aktualisiert!";
} else {
    echo "Error: " . $stmt->error;
}
$stmt->close();
$mysqli->close();
?>
