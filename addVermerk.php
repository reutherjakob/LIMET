<?php
// 25 FX
require_once 'utils/_utils.php';
check_login();
$mysqli = utils_connect_sql();


$untergruppenID = getPostInt('untergruppenID', 0);
$losID = getPostInt('los', 0);
if ($losID === 0) {
    $losID = null;
}
$vermerkText = getPostString('vermerkText', '');
$vermerkStatus = getPostString('vermerkStatus', '');
$vermerkTyp = getPostString('vermerkTyp', '');
$faelligkeitDatum = getPostString('faelligkeitDatum', '');
$username = $_SESSION["username"] ?? '';
$timestamp = date("Y-m-d H:i:s");


$stmt = $mysqli->prepare("
    INSERT INTO tabelle_Vermerke (
        tabelle_Vermerkuntergruppe_idtabelle_Vermerkuntergruppe,
        tabelle_lose_extern_idtabelle_Lose_Extern,
        Ersteller,
        Erstellungszeit,
        Vermerktext,
        Bearbeitungsstatus,
        Faelligkeit,
        Vermerkart
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
");
$stmt->bind_param(
    'iissssss',
    $untergruppenID,
    $losID,
    $username,
    $timestamp,
    $vermerkText,
    $vermerkStatus,
    $faelligkeitDatum,
    $vermerkTyp
);

if ($stmt->execute()) {
    $vermerkID = $stmt->insert_id;
    $roomArray = filter_input(INPUT_POST, 'room', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY) ?? [];
    $roomStmt = $mysqli->prepare("
        INSERT INTO tabelle_vermerke_has_tabelle_räume (
            tabelle_vermerke_idTabelle_vermerke,
            tabelle_räume_idTabelle_räume
        ) VALUES (?, ?)
    ");
    foreach ($roomArray as $roomID) {
        $roomID = (int)$roomID;
        if ($roomID > 0) {
            $roomStmt->bind_param("ii", $vermerkID, $roomID);
            $roomStmt->execute();
        }
    }
    $roomStmt->close();
    echo "Vermerk hinzugefügt!";
} else {
    echo "Fehler beim Einfügen: " . $stmt->error;
}

$stmt->close();
$mysqli->close();
?>
