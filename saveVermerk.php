<?php
require_once 'utils/_utils.php';
check_login();

$mysqli = utils_connect_sql();

$losID = getPostInt('losID');
$vermerkText = getPostString('vermerkText');
$vermerkStatus = getPostString('vermerkStatus');
$vermerkTyp = getPostString('vermerkTyp');
$untergruppenID = getPostInt('untergruppenID');
$vermerkID = getPostInt('vermerkID');
$faelligkeitDatum = getPostString('faelligkeitDatum', '');
if (empty($faelligkeitDatum) || $faelligkeitDatum === 'null' || $faelligkeitDatum === '0000-00-00') {
    $faelligkeitDatum = NULL;
}


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
    $mysqli->query("DELETE FROM tabelle_vermerke_has_tabelle_räume WHERE tabelle_vermerke_idTabelle_vermerke = $vermerkID");
    $roomArray = $_POST['room'] ?? [];
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
