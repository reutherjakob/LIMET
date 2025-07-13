<?php
if (!function_exists('utils_connect_sql')) {
    include "utils/_utils.php";
}
check_login();
$mysqli = utils_connect_sql();// ... after connecting to DB ...

$roomID = filter_input(INPUT_GET, 'room');
$roomID = ($roomID == '0') ? NULL : $roomID;

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
        `tabelle_räume_idTABELLE_Räume` = ?,
        `tabelle_lose_extern_idtabelle_Lose_Extern` = ?,
        `Vermerktext` = ?,
        `Bearbeitungsstatus` = ?,
        `Vermerkart` = ?,
        `Faelligkeit` = ?,
        `tabelle_Vermerkuntergruppe_idtabelle_Vermerkuntergruppe` = ?
        WHERE `idtabelle_Vermerke` = ?";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("iissssii",
    $roomID,
    $losID,
    $vermerkText,
    $vermerkStatus,
    $vermerkTyp,
    $faelligkeitDatum,
    $untergruppenID,
    $vermerkID
);

// echo '<pre>';
// var_dump($stmt);
// echo '</pre>';
// echo "Prepared SQL: " . $sql . "<br>";
// echo "With values:<br>";
// echo "roomID: " . var_export($roomID, true) . "<br>";
// echo "losID: " . var_export($losID, true) . "<br>";
// echo "vermerkText: " . var_export($vermerkText, true) . "<br>";
// echo "vermerkStatus: " . var_export($vermerkStatus, true) . "<br>";
// echo "vermerkTyp: " . var_export($vermerkTyp, true) . "<br>";
// echo "faelligkeitDatum: " . var_export($faelligkeitDatum, true) . "<br>";
// echo "untergruppenID: " . var_export($untergruppenID, true) . "<br>";
// echo "vermerkID: " . var_export($vermerkID, true) . "<br>";
 
if ($stmt->execute()) {
    echo "Vermerk aktualisiert!";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$mysqli->close();
