<?php
// getElementParameterChanges.php
require_once 'utils/_utils.php';
check_login();
ini_set('display_errors', 0);
error_reporting(0);

$mysqli    = utils_connect_sql();
$projectID = (int)($_SESSION['projectID'] ?? 0);

// COALESCE(x,'') treats NULL same as '' → only genuine changes pass through
$sql = "
    SELECT
        a.idtabelle_projekt_elementparameter_aenderungen AS id,
        a.projekt,
        a.element,
        a.geraet,
        a.parameter,
        a.variante,
        a.wert_alt,
        a.wert_neu,
        a.einheit_alt,
        a.einheit_neu,
        a.timestamp,
        a.user,
        e.ElementID,
        e.Bezeichnung   AS element_bezeichnung,
        ep.Bezeichnung  AS parameter_bezeichnung,
        v.Variante      AS variante_bez
    FROM tabelle_projekt_elementparameter_aenderungen a
    INNER JOIN tabelle_elemente e
        ON e.idTABELLE_Elemente = a.element
    LEFT JOIN tabelle_parameter ep
        ON ep.idTABELLE_Parameter = a.parameter
    LEFT JOIN tabelle_varianten v
        ON v.idtabelle_Varianten = a.variante
    WHERE a.projekt = ?
      AND (
            COALESCE(a.wert_alt,    '') <> COALESCE(a.wert_neu,    '')
         OR COALESCE(a.einheit_alt, '') <> COALESCE(a.einheit_neu, '')
      )
    ORDER BY a.timestamp DESC
";

$stmt = $mysqli->prepare($sql);
if (!$stmt) {
    header('Content-Type: application/json');
    echo json_encode(['data' => [], 'error' => $mysqli->error]);
    exit;
}

$stmt->bind_param('i', $projectID);
$stmt->execute();
$result = $stmt->get_result();

$rows = [];
while ($row = $result->fetch_assoc()) {
    $wertAlt    = (string)($row['wert_alt']    ?? '');
    $wertNeu    = (string)($row['wert_neu']    ?? '');
    $einheitAlt = (string)($row['einheit_alt'] ?? '');
    $einheitNeu = (string)($row['einheit_neu'] ?? '');

    $wertChanged    = ($wertAlt    !== $wertNeu);
    $einheitChanged = ($einheitAlt !== $einheitNeu);

    $rows[] = [
        (int)$row['id'],
        $row['timestamp'],
        $row['user'],
        $row['ElementID'] . ' ' . $row['element_bezeichnung'],
        $row['parameter_bezeichnung'] ?? ('Param #' . $row['parameter']),
        $row['variante_bez'] ?? $row['variante'],
        $wertAlt,
        $wertNeu,
        $einheitAlt,
        $einheitNeu,
        $wertChanged    ? 1 : 0,
        $einheitChanged ? 1 : 0,
    ];
}

$stmt->close();
$mysqli->close();

header('Content-Type: application/json');
echo json_encode(['data' => $rows]);