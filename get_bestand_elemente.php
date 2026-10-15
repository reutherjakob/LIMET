<?php
/**
 * Backend f. roombookBestandElements.php
 * GET-Parameter:
 *   alle = 0 (default) -> nur Elemente MIT hinterlegten Bestandsdaten
 *   alle = 1           -> auch Bestands-Elemente OHNE Bestandsdaten
 *   neu  = 0 (default) -> nur Bestand (Neu/Bestand = 0)
 *   neu  = 1           -> zusätzlich Neuanschaffungen (Neu/Bestand = 1)
 *
 * Zeilen-Semantik:
 *   Standard (alle=0 UND neu=0):
 *     eine Zeile je Bestandsdatensatz (wie bisher), nur Anzahl <> 0.
 *   Voll-Modus (alle=1 ODER neu=1):
 *     GENAU Anzahl Zeilen pro Platzierung ("1 Zeile pro Stück").
 *     Vorhandene Bestandsdaten (Inventarnr/Seriennr/...) werden zeilenweise
 *     auf die ersten Stück-Zeilen verteilt, der Rest bleibt leer.
 *     -> Summe der Zeilen = SUM(Anzahl).
 *     Hinweis: hat eine Platzierung MEHR Inventarnummern als Anzahl, werden die
 *     überzähligen NICHT gezeigt (bewusst, damit die Zeilenzahl exakt SUM(Anzahl)
 *     bleibt). Solche Fälle findet die Diagnose-Query in der Konsole.
 *
 * Der costs-Subquery ist pro Platzierung gruppiert (GROUP BY id), damit doppelte
 * Kostensätze KEINE Zeilen mehr vervielfachen.
 */

// init_page_serversides() gibt selbst Navbar-<script>s / Whitespace aus.
// Das fangen wir ab und verwerfen es, sonst ist die JSON-Antwort zerschossen.
ob_start();

require_once '_utils/_utils.php';
require_once '_utils/_format_money.php';
init_page_serversides();

ob_end_clean();

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

function bestand_fehler(string $meldung): void
{
    http_response_code(500);
    error_log('api_bestand_elemente: ' . $meldung);
    echo json_encode(array('error' => $meldung));
    exit;
}

function bestand_geraet(?array $src): string
{
    if ($src === null) {
        return '';
    }
    $teile = array();
    if (($src['Hersteller'] ?? '') !== '') {
        $teile[] = $src['Hersteller'];
    }
    if (($src['Typ'] ?? '') !== '') {
        $teile[] = $src['Typ'];
    }
    return implode('-', $teile);
}

/**
 * Baut eine Ausgabe-Zeile aus Platzierungs-Basisdaten ($base) und
 * optional einem Bestandsdatensatz ($bd, kann null sein = leere Stück-Zeile).
 */
function bestand_zeile(array $base, ?array $bd): array
{
    return array(
        'id'                => (int)$base['id'],
        'ElementID'         => $base['ElementID'],
        'Bezeichnung'       => $base['Bezeichnung'],
        'Anzahl'            => $base['Anzahl'] !== null ? (int)$base['Anzahl'] : 0,
        'Inventarnummer'    => $bd['Inventarnummer'] ?? '',
        'Seriennummer'      => $bd['Seriennummer'] ?? '',
        'Anschaffungsjahr'  => $bd['Anschaffungsjahr'] ?? '',
        'Geraet'            => bestand_geraet($bd),
        'Raumnr'            => $base['Raumnr'],
        'Raumbezeichnung'   => $base['Raumbezeichnung'],
        'RaumbereichNutzer' => $base['Raumbereich Nutzer'],
        'AktuellerOrt'      => $bd['Aktueller Ort'] ?? '',
        'KostenFormatiert'  => format_money($base['Kosten']),
        'Kosten'            => $base['Kosten'] !== null ? (float)$base['Kosten'] : 0,
        'Kurzbeschreibung'  => $base['Kurzbeschreibung'],
        'hatBestandsdaten'  => $bd !== null ? 1 : 0,
        'istNeuanschaffung' => (int)$base['NeuBestand'] === 1 ? 1 : 0,
    );
}

if (!isset($_SESSION['projectID'])) {
    bestand_fehler('Kein projectID in der Session.');
}

$projectID = (int)$_SESSION['projectID'];
$alle      = isset($_GET['alle']) && $_GET['alle'] === '1';
$neu       = isset($_GET['neu'])  && $_GET['neu']  === '1';

// Voll-Modus: sobald einer der Switches an ist -> auffächern nach Stück
$expand = ($alle || $neu);

$joinBestandsdaten = $expand ? 'RIGHT JOIN' : 'INNER JOIN';

$neuBestandFilter = $neu ? '' : 'AND tabelle_räume_has_tabelle_elemente.`Neu/Bestand` = 0';

// Anzahl-Filter nur im Standard; im Voll-Modus erledigt die Auffächerung das
// (Anzahl 0/NULL -> 0 Zeilen).
$anzahlFilter = $expand ? '' : 'AND tabelle_räume_has_tabelle_elemente.Anzahl <> 0';

$mysqli = utils_connect_sql();

$sql = "SELECT 
    tabelle_elemente.ElementID, 
    tabelle_elemente.Bezeichnung, 
    tabelle_räume_has_tabelle_elemente.id, 
    tabelle_räume_has_tabelle_elemente.Anzahl AS Anzahl,
    tabelle_räume_has_tabelle_elemente.Kurzbeschreibung, 
    tabelle_räume_has_tabelle_elemente.`Neu/Bestand` AS NeuBestand,
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
        MAX(tabelle_projekt_varianten_kosten.Kosten) AS Kosten,
        tabelle_räume_has_tabelle_elemente.id AS element_id
    FROM tabelle_projekt_varianten_kosten
    INNER JOIN tabelle_räume_has_tabelle_elemente
    ON tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten
    AND tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente
    WHERE tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte = ?
    GROUP BY tabelle_räume_has_tabelle_elemente.id
) AS costs
ON tabelle_räume_has_tabelle_elemente.id = costs.element_id
WHERE tabelle_räume.tabelle_projekte_idTABELLE_Projekte = ?
$neuBestandFilter
AND tabelle_räume_has_tabelle_elemente.Standort = 1
$anzahlFilter
ORDER BY tabelle_räume.`Raumbereich Nutzer`, tabelle_räume.Raumnr;";

$stmt = $mysqli->prepare($sql);
if ($stmt === false) {
    bestand_fehler('SQL-prepare fehlgeschlagen: ' . $mysqli->error);
}

$stmt->bind_param("ii", $projectID, $projectID);

if (!$stmt->execute()) {
    bestand_fehler('SQL-execute fehlgeschlagen: ' . $stmt->error);
}

$result = $stmt->get_result();

$data = array();

if (!$expand) {
    // Standard: jede Query-Zeile ist eine Bestandsdaten-Zeile
    while ($row = $result->fetch_assoc()) {
        $data[] = bestand_zeile($row, $row);
    }
} else {
    // Voll-Modus: nach Platzierung gruppieren, Bestandsdatensätze sammeln
    $platzierungen = array();
    while ($row = $result->fetch_assoc()) {
        $pid = (int)$row['id'];
        if (!isset($platzierungen[$pid])) {
            $platzierungen[$pid] = array('base' => $row, 'bd' => array());
        }
        if ($row['bestandRef'] !== null) {
            $platzierungen[$pid]['bd'][] = $row;
        }
    }

    // je Platzierung GENAU Anzahl Zeilen (1 Zeile pro Stück)
    foreach ($platzierungen as $p) {
        $anzahl = $p['base']['Anzahl'] !== null ? (int)$p['base']['Anzahl'] : 0;
        for ($i = 0; $i < $anzahl; $i++) {
            $bd = $p['bd'][$i] ?? null;   // Bestandsdaten für dieses Stück, sonst leer
            $data[] = bestand_zeile($p['base'], $bd);
        }
    }
}

$stmt->close();

echo json_encode(array('data' => $data), JSON_UNESCAPED_UNICODE);