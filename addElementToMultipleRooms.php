<?php
// 25 FX
require_once 'utils/_utils.php';
check_login();

$mysqli = utils_connect_sql();
$elementID = getPostInt('elementID');
$amount = getPostInt('amount', 0);
$comment = getPostString('comment', '');
$roomIDs = isset($_POST['rooms']) && is_array($_POST['rooms']) ? $_POST['rooms'] : [];

if ($elementID === 0 || empty($roomIDs)) {
    echo "Ungültige Eingaben!";
    exit;
}

// Check if variant cost exists
$stmt = $mysqli->prepare("
    SELECT Kosten 
    FROM tabelle_projekt_varianten_kosten
    WHERE tabelle_Varianten_idtabelle_Varianten = 1 
      AND tabelle_elemente_idTABELLE_Elemente = ? 
      AND tabelle_projekte_idTABELLE_Projekte = ?
");
$stmt->bind_param("ii", $elementID, $_SESSION["projectID"]);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

$output = "";

if ($row === null || $row["Kosten"] === null) {
    // Insert variant with cost 0
    $stmtInsertVar = $mysqli->prepare("
        INSERT INTO tabelle_projekt_varianten_kosten 
        (tabelle_projekte_idTABELLE_Projekte, tabelle_elemente_idTABELLE_Elemente, tabelle_Varianten_idtabelle_Varianten, Kosten) 
        VALUES (?, ?, 1, 0)
    ");
    $stmtInsertVar->bind_param("ii", $_SESSION["projectID"], $elementID);

    if ($stmtInsertVar->execute()) {
        $output .= "Variante erfolgreich angelegt! \n";
        $stmtInsertVar->close();

        // Insert elements into rooms
        $stmtInsertRoom = $mysqli->prepare("
            INSERT INTO tabelle_räume_has_tabelle_elemente
            (TABELLE_Räume_idTABELLE_Räume, TABELLE_Elemente_idTABELLE_Elemente, `Neu/Bestand`, Anzahl, Standort, Verwendung, Timestamp, tabelle_Varianten_idtabelle_Varianten, Kurzbeschreibung)
            VALUES (?, ?, '1', ?, '1', '1', ?, 1, ?)
        ");
        $timestamp = date("Y-m-d H:i:s");

        foreach ($roomIDs as $roomID) {
            $roomID = (int)$roomID; // cast for safety
            $stmtInsertRoom->bind_param("iiiss", $roomID, $elementID, $amount, $timestamp, $comment);
            if ($stmtInsertRoom->execute()) {
                $output .= "Raum $roomID Element hinzugefügt! \n";
            } else {
                $output .= "Error beim Einfügen in Raum $roomID: " . $stmtInsertRoom->error . "\n";
            }
        }
        $stmtInsertRoom->close();
    } else {
        echo "Fehler beim Anlegen der Variante: " . $stmtInsertVar->error;
        $stmtInsertVar->close();
        $mysqli->close();
        exit;
    }
} else {
    // Variant cost exists, just insert elements into rooms
    $stmtInsertRoom = $mysqli->prepare("
        INSERT INTO tabelle_räume_has_tabelle_elemente
        (TABELLE_Räume_idTABELLE_Räume, TABELLE_Elemente_idTABELLE_Elemente, `Neu/Bestand`, Anzahl, Standort, Verwendung, Timestamp, tabelle_Varianten_idtabelle_Varianten, Kurzbeschreibung)
        VALUES (?, ?, '1', ?, '1', '1', ?, 1, ?)
    ");
    $timestamp = date("Y-m-d H:i:s");

    foreach ($roomIDs as $roomID) {
        $roomID = (int)$roomID;
        $stmtInsertRoom->bind_param("iiiss", $roomID, $elementID, $amount, $timestamp, $comment);
        if ($stmtInsertRoom->execute()) {
            $output .= "Raum $roomID Element hinzugefügt! \n";
        } else {
            $output .= "Error beim Einfügen in Raum $roomID: " . $stmtInsertRoom->error . "\n";
        }
    }
    $stmtInsertRoom->close();
}

$mysqli->close();
echo nl2br(htmlspecialchars($output, ENT_QUOTES, 'UTF-8'));
?>
