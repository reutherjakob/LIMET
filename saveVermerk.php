<?php
require_once 'utils/_utils.php';
check_login();

$mysqli = utils_connect_sql();

$losID = filter_input(INPUT_GET, 'los');
$losID = ($losID == '0') ? NULL : $losID;

$vermerkText = filter_input(INPUT_GET, 'vermerkText');
$vermerkStatus = filter_input(INPUT_GET, 'vermerkStatus');
$vermerkTyp = filter_input(INPUT_GET, 'vermerkTyp');
$faelligkeitDatum = filter_input(INPUT_GET, 'faelligkeitDatum');
if (empty($faelligkeitDatum) || $faelligkeitDatum == 'null' || $faelligkeitDatum == '0000-00-00') {
    $faelligkeitDatum = NULL;
}
$untergruppenID = filter_input(INPUT_GET, 'untergruppenID');
$vermerkID = filter_input(INPUT_GET, 'vermerkID');

$sql = "UPDATE `LIMET_RB`.`tabelle_Vermerke`
        SET
        `tabelle_lose_extern_idtabelle_Lose_Extern` = ?,
        `Vermerktext` = ?,
        `Bearbeitungsstatus` = ?,
        `Vermerkart` = ?,
        `Faelligkeit` = ?,
        `tabelle_Vermerkuntergruppe_idtabelle_Vermerkuntergruppe` = ?
        WHERE `idtabelle_Vermerke` = ?";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("issssii",
    $losID,
    $vermerkText,
    $vermerkStatus,
    $vermerkTyp,
    $faelligkeitDatum,
    $untergruppenID,
    $vermerkID
);

if ($stmt->execute()) {
    // Remove previous room links
    $mysqli->query("DELETE FROM tabelle_vermerke_has_tabelle_räume WHERE tabelle_vermerke_idTabelle_vermerke = $vermerkID");
    // Add new links
    $roomArray = $_GET['room'] ?? [];
    foreach ($roomArray as $roomID) {
        if ($roomID != "0" && $roomID != "") {
            $sql_room = "INSERT INTO tabelle_vermerke_has_tabelle_räume
                         (tabelle_vermerke_idTabelle_vermerke, tabelle_räume_idTabelle_räume)
                         VALUES ($vermerkID, $roomID)";
            $mysqli->query($sql_room);
        }
    }
    echo "Vermerk aktualisiert!";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$mysqli->close();
?>
