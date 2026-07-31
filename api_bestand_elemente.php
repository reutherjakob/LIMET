<?php
/**
 * Backend / JSON-Endpoint für rb_bestand.php
 *
 * GET-Parameter:
 *   alle  = 0 (default) -> nur Elemente MIT hinterlegten Bestandsdaten
 *   alle  = 1           -> ALLE Elemente mit Neu/Bestand = 0 (auch ohne Bestandsdaten)
 *   debug = 1           -> Fehler im Klartext ausgeben (zum Testen im Browser)
 *
 * Aufruf zum Testen:  api_bestand_elemente.php?alle=1&debug=1
 */

$debug = isset($_GET['debug']) && $_GET['debug'] === '1';

// Alles, was die Includes evtl. ausgeben (Whitespace, Notices, HTML), abfangen -
// sonst ist die JSON-Antwort zerschossen.
ob_start();

require_once 'utils/_utils.php';
require_once 'utils/_format.php';
init_page_serversides();

$muell = ob_get_clean();

if (!$debug) {
    header('Content-Type: application/json; charset=utf-8');
} else {
    header('Content-Type: text/plain; charset=utf-8');
    if ($muell !== '') {
        echo "HINWEIS - die Includes haben etwas ausgegeben:\n" . $muell . "\n\n";
    }
}
header('Cache-Control: no-store');

/**
 * Fehlerausgabe
 */
function bestand_fehler($meldung, $debug)
{
    http_response_code(500);
    if ($debug) {
        echo "FEHLER: " . $meldung;
    } else {
        error_log('api_bestand_elemente: ' . $meldung);
        echo json_encode(array('error' => $meldung));
    }
    exit;
}

if (!isset($_SESSION['projectID'])) {
    bestand_fehler('Kein projectID in der Session.', $debug);
}

$projectID = (int)$_SESSION['projectID'];
$alle      = isset($_GET['alle']) && $_GET['alle'] === '1';

/*
 * Der EINZIGE Unterschied zwischen den beiden Modi:
 *   INNER JOIN -> nur Elemente, zu denen ein Bestandsdatensatz existiert
 *   RIGHT JOIN -> der rechts stehende Elemente-/Räume-Block bleibt vollständig,
 *                 die Bestandsdaten werden optional
 */
$joinBestandsdaten = $alle ? 'RIGHT JOIN' : 'INNER JOIN';

$mysqli = utils_connect_sql();

$sql = "SELECT 
    tabelle_elemente.ElementID, 
    tabelle_elemente.Bezeichnung, 
    tabelle_räume_has_tabelle_elemente.id, 
    tabelle_räume_has_tabelle_elemente.Kurzbeschreibung, 
    tabelle_bestandsdaten.Inventarnummer, 
    tabelle_bestandsdaten.Seriennummer, 
    tabelle_bestandsdaten.Anschaffungsjahr, 
    tabelle_bestandsdaten.`Aktueller Ort`, 
    tabelle_bestandsdaten.tabelle_räume_has_tabelle_elemente_id AS bestandRef,
    tabelle_geraete.Typ, 
    tabelle_hersteller.Hersteller, 
    tabelle_räume.Raumnr, 
    tabelle_räume.Raumbezeichnung, 
    tabelle_räume.`Raumbereich Nutzer`,
    costs.Kosten
FROM tabelle_hersteller 
RIGHT JOIN (tabelle_geraete 
RIGHT JOIN (tabelle_bestandsdaten 
$joinBestandsdaten (tabelle_elemente 
INNER JOIN (tabelle_räume 
INNER JOIN tabelle_räume_has_tabelle_elemente 
ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) 
ON tabelle_elemente.idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) 
ON tabelle_bestandsdaten.tabelle_räume_has_tabelle_elemente_id = tabelle_räume_has_tabelle_elemente.id) 
ON tabelle_geraete.idTABELLE_Geraete = tabelle_bestandsdaten.tabelle_geraete_idTABELLE_Geraete) 
ON tabelle_hersteller.idtabelle_hersteller = tabelle_geraete.tabelle_hersteller_idtabelle_hersteller
LEFT JOIN (
    SELECT 
        tabelle_projekt_varianten_kosten.Kosten,
        tabelle_räume_has_tabelle_elemente.id AS element_id
    FROM tabelle_projekt_varianten_kosten
    INNER JOIN tabelle_räume_has_tabelle_elemente
    ON tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten
    AND tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente
    WHERE tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte = ?
) AS costs
ON tabelle_räume_has_tabelle_elemente.id = costs.element_id
WHERE tabelle_räume.tabelle_projekte_idTABELLE_Projekte = ?
AND tabelle_räume_has_tabelle_elemente.`Neu/Bestand` = 0 
AND tabelle_räume_has_tabelle_elemente.Standort = 1
AND tabelle_räume_has_tabelle_elemente.Anzahl <>0
ORDER BY tabelle_räume.`Raumbereich Nutzer`, tabelle_räume.Raumnr;";

$stmt = $mysqli->prepare($sql);
if ($stmt === false) {
    bestand_fehler('SQL-prepare fehlgeschlagen: ' . $mysqli->error, $debug);
}

$stmt->bind_param("ii", $projectID, $projectID);

if (!$stmt->execute()) {
    bestand_fehler('SQL-execute fehlgeschlagen: ' . $stmt->error, $debug);
}

$result = $stmt->get_result();

$data = array();
while ($row = $result->fetch_assoc()) {

    // "Hersteller-Typ", aber ohne einsamen Bindestrich wenn kein Gerät hinterlegt ist
    $teile = array();
    if ($row['Hersteller'] !== null && $row['Hersteller'] !== '') {
        $teile[] = $row['Hersteller'];
    }
    if ($row['Typ'] !== null && $row['Typ'] !== '') {
        $teile[] = $row['Typ'];
    }

    $data[] = array(
        'id'                => (int)$row['id'],
        'ElementID'         => $row['ElementID'],
        'Bezeichnung'       => $row['Bezeichnung'],
        'Inventarnummer'    => $row['Inventarnummer'],
        'Seriennummer'      => $row['Seriennummer'],
        'Anschaffungsjahr'  => $row['Anschaffungsjahr'],
        'Geraet'            => implode('-', $teile),
        'Raumnr'            => $row['Raumnr'],
        'Raumbezeichnung'   => $row['Raumbezeichnung'],
        'RaumbereichNutzer' => $row['Raumbereich Nutzer'],
        'AktuellerOrt'      => $row['Aktueller Ort'],
        'KostenFormatiert'  => format_money($row['Kosten']),
        'Kosten'            => $row['Kosten'] !== null ? (float)$row['Kosten'] : 0,
        'Kurzbeschreibung'  => $row['Kurzbeschreibung'],
        'hatBestandsdaten'  => $row['bestandRef'] !== null ? 1 : 0,
    );
}
$stmt->close();

if ($debug) {
    echo "OK - " . count($data) . " Zeilen (Modus: " . ($alle ? 'alle' : 'nur mit Bestandsdaten') . ")\n\n";
    print_r(array_slice($data, 0, 3));
    exit;
}

echo json_encode(array('data' => $data), JSON_UNESCAPED_UNICODE);