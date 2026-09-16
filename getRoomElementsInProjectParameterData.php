<?php
// 25 FX
ini_set('max_execution_time', '120');
ini_set('memory_limit', '512M'); // Sicherheitsnetz; kann durch Host/PHP-FPM gedeckelt sein
require_once 'utils/_utils.php';
check_login();

// 🔐 Input Validation
// Accept K2Return from both GET and POST
$K2Return = $_POST['K2Return'] ?? $_GET['K2Return'] ?? '[]';
$K2Ret = json_decode($K2Return, true); // true = associative array
$projectID = (int)$_SESSION["projectID"] ?? null;

if (!$projectID || !is_array($K2Ret)) {
    http_response_code(400);
    die(json_encode(["error" => "Ungültige Eingabeparameter."]));
}

// If all categories deselected → return empty result immediately
if (count($K2Ret) === 0) {
    header('Content-Type: application/json');
    echo json_encode(['paramMeta' => [], 'rooms' => []]);
    exit;
}

$mysqli = utils_connect_sql();

// 1️⃣ Get all rooms for the project
$stmt = $mysqli->prepare("
    SELECT 
        idTABELLE_Räume,
        TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen,
        `MT-relevant`,
        Raumbezeichnung,
        Raumnr,
        `Raumbereich Nutzer`,
        Geschoss,
        Bauetappe,
        Bauabschnitt
    FROM tabelle_räume 
    WHERE tabelle_projekte_idTABELLE_Projekte = ? AND `MT-relevant` = 1
");
$stmt->bind_param("i", $projectID);
$stmt->execute();
$rooms = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Build a room lookup map (roomID → room row) for fast metadata access
$roomsByID = [];
foreach ($rooms as $room) {
    $roomsByID[(int)$room['idTABELLE_Räume']] = $room;
}
$roomIDs = array_keys($roomsByID);

// No rooms → nothing to do
if (count($roomIDs) === 0) {
    header('Content-Type: application/json');
    echo json_encode(['paramMeta' => [], 'rooms' => []]);
    exit;
}

// 2️⃣ Load project parameters and categories (once for all rooms)
// Returns both Abkuerzung (used as data key) AND full Bezeichnung (used as column header)
$stmt = $mysqli->prepare("
    SELECT 
        k.idTABELLE_Parameter_Kategorie AS KategorieID,
        k.Kategorie,
        p.Abkuerzung,
        p.Bezeichnung AS FullBezeichnung,
        p.idTABELLE_Parameter AS ParamID
    FROM tabelle_parameter_kategorie k
    INNER JOIN tabelle_parameter p 
        ON k.idTABELLE_Parameter_Kategorie = p.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie
    INNER JOIN tabelle_projekt_elementparameter pep 
        ON p.idTABELLE_Parameter = pep.tabelle_parameter_idTABELLE_Parameter
    WHERE pep.tabelle_projekte_idTABELLE_Projekte = ? 
  #       AND p.`Bauangaben relevant` = 1
    GROUP BY p.idTABELLE_Parameter
    ORDER BY k.Kategorie, p.Bezeichnung
");
$stmt->bind_param("i", $projectID);
$stmt->execute();
$kategorieResult = $stmt->get_result();

$paramInfos = [];
while ($row = $kategorieResult->fetch_assoc()) {
    if (empty($K2Ret) || in_array($row['KategorieID'], $K2Ret)) {
        // Use Abkuerzung as data key; fall back to FullBezeichnung if empty/null
        $dataKey = (isset($row['Abkuerzung']) && trim($row['Abkuerzung']) !== '')
            ? trim($row['Abkuerzung'])
            : trim($row['FullBezeichnung']);
        $paramInfos[$row['ParamID']] = [
            'KategorieID'     => $row['KategorieID'],
            'ParamID'         => $row['ParamID'],
            'DataKey'         => $dataKey,
            'FullBezeichnung' => $row['FullBezeichnung'],
            'Kategorie'       => $row['Kategorie'],
        ];
    }
}

// 3️⃣ Load all element and variant parameters (once for all rooms)
//    → indexed into a hash map for O(1) lookup instead of a full scan per element.
//    Nur der ZULETZT gelesene Wert je Kombination wird behalten (spart Speicher,
//    entspricht dem bisherigen end()-Verhalten).
$stmt = $mysqli->prepare("
    SELECT 
        pep.tabelle_elemente_idTABELLE_Elemente, 
        pep.Wert, 
        pep.Einheit, 
        pep.tabelle_Varianten_idtabelle_Varianten,
        p.idTABELLE_Parameter AS ParamID
    FROM tabelle_parameter_kategorie k
    INNER JOIN tabelle_parameter p 
        ON k.idTABELLE_Parameter_Kategorie = p.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie
    INNER JOIN tabelle_projekt_elementparameter pep 
        ON p.idTABELLE_Parameter = pep.tabelle_parameter_idTABELLE_Parameter
    WHERE pep.tabelle_projekte_idTABELLE_Projekte = ? 
 #        AND p.`Bauangaben relevant` = 1
    ORDER BY k.Kategorie, p.Abkuerzung
");
$stmt->bind_param("i", $projectID);
$stmt->execute();
$tabelle_projekt_elementparameter = $stmt->get_result();

// Key: "elementID|variantenID|ParamID" → {Wert, Einheit}   (nur letzter Wert)
$elementParamMap = [];
while ($row = $tabelle_projekt_elementparameter->fetch_assoc()) {
    $mapKey = $row['tabelle_elemente_idTABELLE_Elemente'] . '|'
        . $row['tabelle_Varianten_idtabelle_Varianten'] . '|'
        . $row['ParamID'];
    $elementParamMap[$mapKey] = [
        'Wert'    => $row['Wert'],
        'Einheit' => $row['Einheit'],
    ];
}

// 4️⃣ Prepare paramMeta so JS knows DataKey → FullBezeichnung mapping
$paramMeta = [];
foreach ($paramInfos as $p) {
    $paramMeta[] = [
        'key'   => $p['DataKey'],         // unique data key (Abkuerzung or FullBezeichnung)
        'label' => $p['FullBezeichnung'], // full name for column header / Excel
    ];
}

// 5️⃣ Get elements for ALL rooms in a single query (ordered by room)
$placeholders = implode(',', array_fill(0, count($roomIDs), '?'));
$types        = str_repeat('i', count($roomIDs));

$stmt = $mysqli->prepare("
    SELECT 
        rhe.TABELLE_Räume_idTABELLE_Räume AS roomID,
        e.ElementID, e.Bezeichnung, v.Variante,
        SUM(rhe.Anzahl) AS SummevonAnzahl,
        rhe.`Neu/Bestand`, 
        rhe.Standort,
        rhe.TABELLE_Elemente_idTABELLE_Elemente, 
        rhe.tabelle_Varianten_idtabelle_Varianten
    FROM tabelle_varianten v
    INNER JOIN tabelle_räume_has_tabelle_elemente rhe 
        ON v.idtabelle_Varianten = rhe.tabelle_Varianten_idtabelle_Varianten
    INNER JOIN tabelle_elemente e 
        ON rhe.TABELLE_Elemente_idTABELLE_Elemente = e.idTABELLE_Elemente
    WHERE rhe.Verwendung = 1 
        AND rhe.Standort = 1 
        AND rhe.TABELLE_Räume_idTABELLE_Räume IN ($placeholders)
    GROUP BY 
        rhe.TABELLE_Räume_idTABELLE_Räume,
        e.ElementID, e.Bezeichnung, v.Variante, 
        rhe.`Neu/Bestand`, 
        rhe.TABELLE_Elemente_idTABELLE_Elemente, 
        rhe.tabelle_Varianten_idtabelle_Varianten
    HAVING SummevonAnzahl > 0
    ORDER BY rhe.TABELLE_Räume_idTABELLE_Räume, e.ElementID, v.Variante
");
$stmt->bind_param($types, ...$roomIDs);
$stmt->execute();
$allElements = $stmt->get_result();

// 6️⃣ Stream output: JSON Raum für Raum ausgeben, statt alles im RAM zu sammeln.
//    So liegt immer nur ein Element gleichzeitig im Speicher.
header('Content-Type: application/json');

echo '{"paramMeta":' . json_encode($paramMeta) . ',"rooms":[';

$firstRoom     = true;
$firstElement  = true;
$currentRoomID = null;
$seenRoomIDs   = [];

while ($row = $allElements->fetch_assoc()) {
    $roomID = (int)$row['roomID'];

    // Neuer Raum? → vorherigen abschließen, neuen öffnen
    if ($roomID !== $currentRoomID) {
        if ($currentRoomID !== null) {
            echo ']}'; // close previous room's "elements" + object
        }
        echo $firstRoom ? '' : ',';
        $firstRoom = false;
        echo '{"roomID":' . $roomID . ',"elements":[';
        $currentRoomID        = $roomID;
        $seenRoomIDs[$roomID] = true;
        $firstElement         = true;
    }

    $room = $roomsByID[$roomID];

    $elementData = $row;
    $elementData['Raumbezeichnung']        = $room['Raumbezeichnung'];
    $elementData['Raumnr']                 = $room['Raumnr'];
    $elementData['MTrelevant']             = $room['MT-relevant'];
    $elementData['Bauabschnitt']           = $room['Bauabschnitt'];
    $elementData['Geschoss']               = $room['Geschoss'];
    $elementData['FunktionsteilstellenID'] = $room['TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen'];
    $elementData['Raumbereich']            = $room['Raumbereich Nutzer'];
    $elementData['Bauetappe']              = $room['Bauetappe'];

    $elemID    = $row['TABELLE_Elemente_idTABELLE_Elemente'];
    $variantID = $row['tabelle_Varianten_idtabelle_Varianten'];

    foreach ($paramInfos as $paramInfo) {
        $key       = $paramInfo['DataKey'];
        $lookupKey = $elemID . '|' . $variantID . '|' . $paramInfo['ParamID'];

        if (!isset($elementParamMap[$lookupKey])) {
            $elementData[$key]           = '';
            $elementData[$key . '__num'] = null;
            continue;
        }

        $wert    = $elementParamMap[$lookupKey]['Wert'];
        $einheit = $elementParamMap[$lookupKey]['Einheit'];

        $elementData[$key]           = $wert . $einheit;
        $elementData[$key . '__num'] = is_numeric($wert) ? (float)$wert : null;
    }

    echo $firstElement ? '' : ',';
    $firstElement = false;
    echo json_encode($elementData);

    unset($elementData); // Speicher sofort wieder freigeben
}

// Letzten offenen Raum abschließen
if ($currentRoomID !== null) {
    echo ']}';
}

// Räume ohne Elemente anhängen (damit sie wie bisher im Ergebnis auftauchen)
foreach ($roomIDs as $rid) {
    if (isset($seenRoomIDs[$rid])) {
        continue;
    }
    echo $firstRoom ? '' : ',';
    $firstRoom = false;
    echo '{"roomID":' . (int)$rid . ',"elements":[]}';
}

echo ']}';

$mysqli->close();