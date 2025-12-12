<?php
require_once "../../utils/_utils.php";
header('Content-Type: application/json; charset=utf-8');

$conn = utils_connect_sql();
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "DB-Verbindung fehlgeschlagen"]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'getRaumbereiche') {
    $vermerkgruppe_id = filter_input(INPUT_POST, 'vermerkgruppe_id', FILTER_VALIDATE_INT);
    if (!$vermerkgruppe_id) {
        echo json_encode(["success" => false, "message" => "Ungültige oder fehlende Vermerkgruppe-ID"]);
        exit;
    }

    $conn = utils_connect_sql(); // bestehende Verbindungsfunktion verwenden
    $sql = "SELECT DISTINCT Untergruppenname FROM tabelle_Vermerkuntergruppe WHERE tabelle_Vermerkgruppe_idtabelle_Vermerkgruppe = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $vermerkgruppe_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $raumbereiche = [];
    while ($row = $result->fetch_assoc()) {
        $raumbereiche[] = $row["Untergruppenname"];
    }
    $stmt->close();
    $conn->close();

    echo json_encode(["success" => true, "data" => $raumbereiche]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'addUntergruppen') {


    $vermerkgruppe_id = filter_input(INPUT_POST, 'vermerkgruppe_id', FILTER_VALIDATE_INT);
    $raumbereiche = $_POST['raumbereiche'] ?? [];

    if (!$vermerkgruppe_id) {
        echo json_encode(["success" => false, "message" => "Ungültige oder fehlende Vermerkgruppe-ID"]);
        exit;
    }

// Normalisieren: $raumbereiche kann String oder Array sein
    if (!is_array($raumbereiche)) {
        $raumbereiche = [$raumbereiche];
    }

    $response = ["success" => true, "created" => [], "skipped" => []];

// Hole die höchste bisherige Nummer in der Gruppe
    $stmtMax = $conn->prepare("
    SELECT MAX(Untergruppennummer) AS maxnum
    FROM tabelle_Vermerkuntergruppe
    WHERE tabelle_Vermerkgruppe_idtabelle_Vermerkgruppe = ?
");
    $stmtMax->bind_param("i", $vermerkgruppe_id);
    $stmtMax->execute();
    $resMax = $stmtMax->get_result();
    $rowMax = $resMax->fetch_assoc();
    $nextNum = ($rowMax && $rowMax["maxnum"] !== null) ? intval($rowMax["maxnum"]) + 1 : 1;
    $stmtMax->close();

// Prepared Statements für Insert und Duplikatsprüfung
    $checkStmt = $conn->prepare("
    SELECT idtabelle_Vermerkuntergruppe
    FROM tabelle_Vermerkuntergruppe
    WHERE tabelle_Vermerkgruppe_idtabelle_Vermerkgruppe = ? AND Untergruppenname = ?
");

    $insertStmt = $conn->prepare("
    INSERT INTO tabelle_Vermerkuntergruppe
        (Untergruppenname, Untergruppennummer, tabelle_Vermerkgruppe_idtabelle_Vermerkgruppe)
    VALUES (?, ?, ?)
");
    $newlyInsertedNames = [];

    foreach ($raumbereiche as $raum) {
        $raum = trim($raum);
        if ($raum === "") {
            continue;
        }

        // Prüfen, ob im Zwischenspeicher bereits
        if (in_array($raum, $newlyInsertedNames, true)) {
            $response["skipped"][] = $raum;
            continue;
        }

        // DB-Duplikatscheck
        $checkStmt->bind_param("is", $vermerkgruppe_id, $raum);
        $checkStmt->execute();
        $checkRes = $checkStmt->get_result();
        if ($checkRes->num_rows > 0) {
            $response["skipped"][] = $raum;
            continue;
        }

        // Insert
        $insertStmt->bind_param("sii", $raum, $nextNum, $vermerkgruppe_id);
        if ($insertStmt->execute()) {
            $response["created"][] = [
                "id" => $insertStmt->insert_id,
                "name" => $raum,
                "nummer" => $nextNum
            ];
            $nextNum++;
            $newlyInsertedNames[] = $raum; // In Zwischenspeicher speichern
        } else {
            $response["success"] = false;
            $response["message"] = "Fehler beim Einfügen: " . $insertStmt->error;
            echo json_encode($response);
            exit;
        }
    }

    $checkStmt->close();
    $insertStmt->close();
    $conn->close();

    echo json_encode($response);
}