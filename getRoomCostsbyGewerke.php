<?php
// 25 FX
require_once 'utils/_utils.php';
check_login();

$mysqli = utils_connect_sql();

$roomID = $_SESSION["roomID"];
$projectID = (int)$_SESSION["projectID"];

if ($roomID <= 0 || $projectID <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing or invalid roomID or projectID']);
    exit;
}

// Fetch Gewerke grouped costs, adapting your original SQL with room filter
$sql = "
SELECT 
    g.Gewerke_Nr,
    g.Bezeichnung AS GewerkBezeichnung,
    SUM(pk.Kosten * rr.Anzahl) AS Kosten
FROM
    tabelle_räume AS r
INNER JOIN 
    tabelle_räume_has_tabelle_elemente AS rr ON r.idTABELLE_Räume = rr.TABELLE_Räume_idTABELLE_Räume
INNER JOIN 
    tabelle_projekt_varianten_kosten AS pk ON pk.tabelle_elemente_idTABELLE_Elemente = rr.TABELLE_Elemente_idTABELLE_Elemente
    AND pk.tabelle_Varianten_idtabelle_Varianten = rr.tabelle_Varianten_idtabelle_Varianten
    AND pk.tabelle_projekte_idTABELLE_Projekte = r.tabelle_projekte_idTABELLE_Projekte
INNER JOIN 
    tabelle_projekt_element_gewerk AS peg ON peg.tabelle_elemente_idTABELLE_Elemente = rr.TABELLE_Elemente_idTABELLE_Elemente
    AND peg.tabelle_projekte_idTABELLE_Projekte = r.tabelle_projekte_idTABELLE_Projekte
LEFT JOIN 
    tabelle_auftraggeber_gewerke AS g ON peg.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke = g.idTABELLE_Auftraggeber_Gewerke
WHERE 
    r.idTABELLE_Räume = ?
    AND r.tabelle_projekte_idTABELLE_Projekte = ?
    AND rr.Standort = 1
GROUP BY 
    g.Gewerke_Nr, g.Bezeichnung
ORDER BY
    g.Gewerke_Nr
";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("ii", $roomID, $projectID);
$stmt->execute();
$result = $stmt->get_result();

$costsByGewerk = [];
$totalWithoutGewerk = 0.0;

while ($row = $result->fetch_assoc()) {
    if ($row['Gewerke_Nr'] === null) {
        $totalWithoutGewerk += (float)$row['Kosten'];
    } else {
        $costsByGewerk[] = [
            'gewerkeNr' => $row['Gewerke_Nr'],
            'bezeichnung' => $row['GewerkBezeichnung'],
            'kosten' => (float)$row['Kosten'],
        ];
    }
}
$stmt->close();

// Also check elements with no Gewerke (not assigned)
if ($totalWithoutGewerk == 0.0) {
    $sqlNoGewerk = "
    SELECT SUM(pk.Kosten * rr.Anzahl) AS Kosten
    FROM
        tabelle_räume AS r
    INNER JOIN 
        tabelle_räume_has_tabelle_elemente AS rr ON r.idTABELLE_Räume = rr.TABELLE_Räume_idTABELLE_Räume
    INNER JOIN 
        tabelle_projekt_varianten_kosten AS pk ON pk.tabelle_elemente_idTABELLE_Elemente = rr.TABELLE_Elemente_idTABELLE_Elemente
        AND pk.tabelle_Varianten_idtabelle_Varianten = rr.tabelle_Varianten_idtabelle_Varianten
        AND pk.tabelle_projekte_idTABELLE_Projekte = r.tabelle_projekte_idTABELLE_Projekte
    LEFT JOIN 
        tabelle_projekt_element_gewerk AS peg ON peg.tabelle_elemente_idTABELLE_Elemente = rr.TABELLE_Elemente_idTABELLE_Elemente
        AND peg.tabelle_projekte_idTABELLE_Projekte = r.tabelle_projekte_idTABELLE_Projekte
    WHERE 
        r.idTABELLE_Räume = ?
        AND r.tabelle_projekte_idTABELLE_Projekte = ?
        AND rr.Standort = 1
        AND peg.idtabelle_projekt_element_gewerk IS NULL
    ";
    $stmtNoGewerk = $mysqli->prepare($sqlNoGewerk);
    $stmtNoGewerk->bind_param("ii", $roomID, $projectID);
    $stmtNoGewerk->execute();
    $resNoGewerk = $stmtNoGewerk->get_result();
    if ($rowNoGewerk = $resNoGewerk->fetch_assoc()) {
        $totalWithoutGewerk = (float)$rowNoGewerk['Kosten'];
    }
    $stmtNoGewerk->close();
}

// Add "Ohne Gewerke" total if any
if ($totalWithoutGewerk > 0) {
    $costsByGewerk[] = [
        'gewerkeNr' => null,
        'bezeichnung' => 'Ohne Gewerke',
        'kosten' => $totalWithoutGewerk,
    ];
}

$mysqli->close();

// Sort Gewerke by GewerkeNr, null last
usort($costsByGewerk, function ($a, $b) {
    if ($a['gewerkeNr'] === null) return 1;
    if ($b['gewerkeNr'] === null) return -1;
    return strcmp($a['gewerkeNr'], $b['gewerkeNr']);
});


// Calculate total costs including Gewerke and Ohne Gewerke
$totalCosts = 0.0;
foreach ($costsByGewerk as $item) {
    $totalCosts += $item['kosten'];
}
echo '<ul class="list-unstyled mb-0 row">';

foreach ($costsByGewerk as $item) {
    $label = $item['gewerkeNr'] !== null ? $item['gewerkeNr'] . ' - ' . $item['bezeichnung'] : 'Ohne Gewerke';
    $costFormatted = number_format($item['kosten'], 2, ',', '.') . ' €';

    echo '<li class="col-12 d-flex justify-content-between">';
    echo '<strong>' . htmlspecialchars($label) . ':</strong>';
    echo '<span>' . htmlspecialchars($costFormatted) . '</span>';
    echo '</li>';
}

echo '</ul>';

// Add total costs below in its own row
echo '<div class="row mt-3 fw-bold">';
echo '<div class="col-6">';
echo 'Gesamtkosten:';
echo '</div>';
echo '<div class="col-6 text-end">';
echo number_format($totalCosts, 2, ',', '.') . ' €';
echo '</div>';
echo '</div>';
?>