<?php
require_once 'utils/_utils.php';
check_login();
header('Content-Type: application/json');

$projectID = (int)($_POST['projectID'] ?? $_SESSION['projectID'] ?? 0);
$raumbereiche = $_POST['raumbereiche'] ?? [];
$distinct = (int)($_POST['distinct'] ?? 1);
if (!$projectID || empty($raumbereiche) || !is_array($raumbereiche)) {
    echo json_encode(['data' => []]);
    exit;
}

// Sanitize
$raumbereiche = array_filter($raumbereiche, fn($r) => is_string($r) && strlen($r) <= 100);
$raumbereiche = array_values($raumbereiche);

if (empty($raumbereiche)) {
    echo json_encode(['data' => []]);
    exit;
}

$mysqli = utils_connect_sql();

$placeholders = implode(',', array_fill(0, count($raumbereiche), '?'));
$types = 'i' . str_repeat('s', count($raumbereiche));
$params = array_merge([$projectID], $raumbereiche);


if ($distinct) {
    $sql = "
        SELECT
            e.ElementID,
            e.Bezeichnung,
            SUM(rhe.Anzahl) AS Anzahl
        FROM tabelle_räume r
        INNER JOIN tabelle_räume_has_tabelle_elemente rhe
            ON rhe.TABELLE_Räume_idTABELLE_Räume = r.idTABELLE_Räume
        INNER JOIN tabelle_elemente e
            ON e.idTABELLE_Elemente = rhe.TABELLE_Elemente_idTABELLE_Elemente
        WHERE r.tabelle_projekte_idTABELLE_Projekte = ?
          AND r.`Raumbereich Nutzer` IN ($placeholders)
          AND r.Entfallen = 0
        GROUP BY e.idTABELLE_Elemente, e.ElementID, e.Bezeichnung
        HAVING SUM(rhe.Anzahl) > 0
        ORDER BY e.ElementID
    ";
} else {
    $sql = "
        SELECT
            e.ElementID,
            e.Bezeichnung,
            r.`Raumbereich Nutzer`   AS Raumbereich,
            r.Raumnr,
            r.Raumbezeichnung,
            r.Geschoss,
            rhe.Anzahl,
            rhe.`Neu/Bestand`        AS NeuBestand,
            CONCAT(le.LosNr_Extern, ' ', le.LosBezeichnung_Extern) AS LosBezeichnung
        FROM tabelle_räume r
        INNER JOIN tabelle_räume_has_tabelle_elemente rhe
            ON rhe.TABELLE_Räume_idTABELLE_Räume = r.idTABELLE_Räume
        INNER JOIN tabelle_elemente e
            ON e.idTABELLE_Elemente = rhe.TABELLE_Elemente_idTABELLE_Elemente
        LEFT JOIN tabelle_lose_extern le
            ON le.idtabelle_Lose_Extern = rhe.tabelle_Lose_Extern_idtabelle_Lose_Extern
        WHERE r.tabelle_projekte_idTABELLE_Projekte = ?
          AND r.`Raumbereich Nutzer` IN ($placeholders)
          AND r.Entfallen = 0
            AND rhe.Anzahl>0
        ORDER BY e.ElementID, r.`Raumbereich Nutzer`, r.Raumnr
    ";
}

$stmt = $mysqli->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = [
        'ElementID'       => $row['ElementID'] ?? '',
        'Bezeichnung'     => $row['Bezeichnung'] ?? '',
        'Raumbereich'     => $row['Raumbereich'] ?? '',
        'Raumnr'          => $row['Raumnr'] ?? '',
        'Raumbezeichnung' => $row['Raumbezeichnung'] ?? '',
        'Geschoss'        => $row['Geschoss'] ?? '',
        'Anzahl'          => (int)$row['Anzahl'],
        'NeuBestand'      => (int)($row['NeuBestand'] ?? 0),
        'LosBezeichnung'  => $row['LosBezeichnung'] ?? ''
    ];
}

echo json_encode(['data' => $data]);
$stmt->close();
$mysqli->close();
?>