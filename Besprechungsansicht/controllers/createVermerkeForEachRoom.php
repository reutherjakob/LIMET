<?php
include "../../utils/_utils.php";
session_start();

header('Content-Type: application/json; charset=utf-8');

$projectID = $_SESSION["projectID"] ?? null;
if (!$projectID) {
    echo json_encode(["success" => false, "message" => "Kein Projekt in der Session definiert"]);
    exit;
}

$conn = utils_connect_sql();
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "DB-Verbindung fehlgeschlagen"]);
    exit;
}

// Eingaben validieren
$vermerkgruppe_id = filter_input(INPUT_POST, 'vermerkgruppe_id', FILTER_VALIDATE_INT);
$raumbereiche = $_POST['raumbereiche'] ?? [];

if (!$vermerkgruppe_id) {
    echo json_encode(["success" => false, "message" => "Ungültige oder fehlende Vermerkgruppe-ID"]);
    exit;
}

if (!is_array($raumbereiche) || count($raumbereiche) === 0) {
    echo json_encode(["success" => false, "message" => "Keine Raumbereiche übermittelt"]);
    exit;
}

$username = $_SESSION['username'] ?? 'unknown';
$timestamp = date("Y-m-d H:i:s");

$response = ["success" => true, "addedVermerke" => [], "skipped" => [], "errors" => []];

// Prepared Statements
$stmtUntergruppe = $conn->prepare("
    SELECT idtabelle_Vermerkuntergruppe 
    FROM tabelle_Vermerkuntergruppe 
    WHERE tabelle_Vermerkgruppe_idtabelle_Vermerkgruppe = ? 
      AND Untergruppenname = ?
");

$stmtRaeume = $conn->prepare("
    SELECT idTABELLE_Räume 
    FROM tabelle_räume 
    WHERE `Raumbereich Nutzer` = ? 
      AND tabelle_projekte_idTABELLE_Projekte = ?
      AND Entfallen = 0
");

$stmtCheckVermerkExists = $conn->prepare("
    SELECT 1 FROM tabelle_vermerke_has_tabelle_räume vr
    JOIN tabelle_Vermerke v ON v.idtabelle_Vermerke = vr.tabelle_vermerke_idTabelle_vermerke
    WHERE v.tabelle_Vermerkuntergruppe_idtabelle_Vermerkuntergruppe = ? 
    AND vr.tabelle_räume_idTabelle_Räume = ?
    AND Vermerkart = ?
    LIMIT 1
");

$stmtInsertVermerk = $conn->prepare("
    INSERT INTO tabelle_Vermerke (
        tabelle_Vermerkuntergruppe_idtabelle_Vermerkuntergruppe,
        Ersteller,
        Erstellungszeit,
        Vermerktext,
        Bearbeitungsstatus,
        Vermerkart
    ) VALUES (?, ?, ?, ?, ?, ?)
");

$stmtInsertVermerkRaum = $conn->prepare("
    INSERT INTO tabelle_vermerke_has_tabelle_räume (
        tabelle_vermerke_idTabelle_vermerke,
        tabelle_räume_idTabelle_räume
    ) VALUES (?, ?)
");

$bearbeitungsstatus = "offen";
$vermerkart = "Bespr";
$vermerkText = "Besprechung";

foreach ($raumbereiche as $raumbereich) {
    $raumbereich = trim($raumbereich);
    if ($raumbereich === "") {
        continue;
    }

    // Vermerkuntergruppe mit Raumbereich-Namen suchen
    $stmtUntergruppe->bind_param("is", $vermerkgruppe_id, $raumbereich);
    $stmtUntergruppe->execute();
    $resultUG = $stmtUntergruppe->get_result();

    if ($resultUG->num_rows === 0) {
        $response["errors"][] = "Keine Vermerkuntergruppe für Raumbereich '$raumbereich' gefunden.";
        continue;
    }
    $untergruppenId = $resultUG->fetch_assoc()['idtabelle_Vermerkuntergruppe'];

    // Alle Räume für diesen Raumbereich im Projekt holen
    $stmtRaeume->bind_param("si", $raumbereich, $projectID);
    $stmtRaeume->execute();
    $resultRaeume = $stmtRaeume->get_result();

    if ($resultRaeume->num_rows === 0) {
        $response["errors"][] = "Keine Räume für Raumbereich '$raumbereich' gefunden.";
        continue;
    }

    // Für jeden Raum prüfen, ob schon Vermerk existiert
    while ($raum = $resultRaeume->fetch_assoc()) {
        $raumId = $raum['idTABELLE_Räume'];

        $stmtCheckVermerkExists->bind_param("iis", $untergruppenId, $raumId, $vermerkart  );
        $stmtCheckVermerkExists->execute();
        $checkRes = $stmtCheckVermerkExists->get_result();

        if ($checkRes->num_rows > 0) {
            // Vermerk bereits vorhanden -> überspringen
            $response["skipped"][] = [
                "untergruppenID" => $untergruppenId,
                "raumbereich" => $raumbereich,
                "raumID" => $raumId,
                "reason" => "Vermerk existiert bereits"
            ];
            continue;
        }

        // Neuen Vermerk anlegen
        $stmtInsertVermerk->bind_param(
            "isssss",
            $untergruppenId,
            $username,
            $timestamp,
            $vermerkText,
            $bearbeitungsstatus,
            $vermerkart
        );
        if ($stmtInsertVermerk->execute()) {
            $vermerkId = $stmtInsertVermerk->insert_id;

            // Vermerk mit Raum verknüpfen
            $stmtInsertVermerkRaum->bind_param("ii", $vermerkId, $raumId);
            if ($stmtInsertVermerkRaum->execute()) {
                $response["addedVermerke"][] = [
                    "vermerkID" => $vermerkId,
                    "untergruppenID" => $untergruppenId,
                    "raumbereich" => $raumbereich,
                    "raumID" => $raumId,
                    "text" => $vermerkText
                ];
            } else {
                $response["errors"][] = "Fehler bei Verknüpfung Vermerk mit Raum (RaumID $raumId): " . $stmtInsertVermerkRaum->error;
                // Optional: Vermerk löschen, wenn Relation fehlschlägt, um Inkonsistenz zu vermeiden
            }
        } else {
            $response["errors"][] = "Fehler beim Einfügen des Vermerks für RaumID $raumId: " . $stmtInsertVermerk->error;
        }
    }
}

$stmtUntergruppe->close();
$stmtRaeume->close();
$stmtCheckVermerkExists->close();
$stmtInsertVermerk->close();
$stmtInsertVermerkRaum->close();
$conn->close();

echo json_encode($response);
