<?php
require_once 'utils/_utils.php';
check_login();

$mysqli = utils_connect_sql();

$untergruppenID = filter_input(INPUT_GET, 'untergruppenID', FILTER_VALIDATE_INT);
$losParam = filter_input(INPUT_GET, 'los', FILTER_VALIDATE_INT);
$losID = ($losParam === 0 || is_null($losParam)) ? null : $losParam;

$vermerkText = filter_input(INPUT_GET, 'vermerkText', FILTER_UNSAFE_RAW);
$vermerkText = htmlspecialchars(trim($vermerkText), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

$vermerkStatus = filter_input(INPUT_GET, 'vermerkStatus', FILTER_UNSAFE_RAW);
$vermerkStatus = htmlspecialchars(trim($vermerkStatus), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

$vermerkTyp = filter_input(INPUT_GET, 'vermerkTyp', FILTER_UNSAFE_RAW);
$vermerkTyp = htmlspecialchars(trim($vermerkTyp), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

$faelligkeitDatum = filter_input(INPUT_GET, 'faelligkeitDatum', FILTER_UNSAFE_RAW);
$faelligkeitDatum = htmlspecialchars(trim($faelligkeitDatum), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

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
    $roomArray = filter_input(INPUT_GET, 'room', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY) ?? [];
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
