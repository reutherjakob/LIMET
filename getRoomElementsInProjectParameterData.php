<?php
// 25 FX
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
    echo json_encode(['paramMeta' => [], 'rooms' => []], JSON_PRETTY_PRINT);
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
$stmt = $mysqli->prepare("
    SELECT 
        pep.tabelle_elemente_idTABELLE_Elemente, 
        pep.Wert, 
        pep.Einheit, 
        pep.tabelle_Varianten_idtabelle_Varianten,
        p.idTABELLE_Parameter AS ParamID,
        k.idTABELLE_Parameter_Kategorie AS KategorieID
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

$elementParamInfos = [];
while ($row = $tabelle_projekt_elementparameter->fetch_assoc()) {
    $elementParamInfos[] = [
        'KategorieID' => $row['KategorieID'],
        'ParamID'     => $row['ParamID'],
        'elementID'   => $row['tabelle_elemente_idTABELLE_Elemente'],
        'variantenID' => $row['tabelle_Varianten_idtabelle_Varianten'],
        'Wert'        => $row['Wert'],
        'Einheit'     => $row['Einheit'],
    ];
}

// 4️⃣ Collect data for each room
$result = [];

// Also send paramMeta so JS knows Abkuerzung → FullBezeichnung mapping
$paramMeta = [];
foreach ($paramInfos as $p) {
    $paramMeta[] = [
        'key'   => $p['DataKey'],         // unique data key (Abkuerzung or FullBezeichnung)
        'label' => $p['FullBezeichnung'], // full name for column header / Excel
    ];
}

foreach ($rooms as $room) {
    $roomID = $room['idTABELLE_Räume'];
    $roomData = ['roomID' => $roomID, 'elements' => []];

    // Get elements for this room
    $stmt = $mysqli->prepare("
        SELECT 
            e.ElementID, e.Bezeichnung, v.Variante,
            SUM(rhe.Anzahl) AS SummevonAnzahl,
            rhe.`Neu/Bestand`, 
            rhe.Standort,
            rhe.TABELLE_Elemente_idTABELLE_Elemente, 
            rhe.tabelle_Varianten_idtabelle_Varianten
        FROM tabelle_varianten v
        INNER JOIN (
            tabelle_räume_has_tabelle_elemente rhe
            INNER JOIN tabelle_elemente e 
            ON rhe.TABELLE_Elemente_idTABELLE_Elemente = e.idTABELLE_Elemente
        ) ON v.idtabelle_Varianten = rhe.tabelle_Varianten_idtabelle_Varianten
        WHERE rhe.Verwendung = 1 AND rhe.TABELLE_Räume_idTABELLE_Räume = ?
        AND rhe.Standort = 1 
        GROUP BY 
            e.ElementID, e.Bezeichnung, v.Variante, 
            rhe.`Neu/Bestand`, 
            rhe.TABELLE_Elemente_idTABELLE_Elemente, 
            rhe.tabelle_Varianten_idtabelle_Varianten
        Having SummevonAnzahl > 0
        ORDER BY e.ElementID, v.Variante
    ");
    $stmt->bind_param("i", $roomID);
    $stmt->execute();
    $tabelle_elemente = $stmt->get_result();

    while ($row = $tabelle_elemente->fetch_assoc()) {
        $elementData = $row;
        $elementData['Raumbezeichnung'] = $room['Raumbezeichnung'];
        $elementData['Raumnr']          = $room['Raumnr'];
        $elementData['MTrelevant']      = $room['MT-relevant'];
        $elementData['Bauabschnitt']    = $room['Bauabschnitt'];
        $elementData['Geschoss']        = $room['Geschoss'];
        $elementData['FunktionsteilstellenID'] = $room['TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen'];
        $elementData['Raumbereich']     = $room['Raumbereich Nutzer'];
        $elementData['Bauetappe']       = $room['Bauetappe'];

        foreach ($paramInfos as $paramInfo) {
            $values     = [];
            $numValues  = [];
            foreach ($elementParamInfos as $elementParamInfo) {
                if (
                    $elementParamInfo['ParamID']   == $paramInfo['ParamID'] &&
                    $elementParamInfo['elementID'] == $row['TABELLE_Elemente_idTABELLE_Elemente'] &&
                    $elementParamInfo['variantenID'] == $row['tabelle_Varianten_idtabelle_Varianten']
                ) {
                    $wert   = $elementParamInfo['Wert'];
                    $einheit = $elementParamInfo['Einheit'];
                    $values[]    = $wert . $einheit;
                    // Store numeric value separately (null if not numeric)
                    $numValues[] = is_numeric($wert) ? (float)$wert : null;
                }
            }

            $key = $paramInfo['DataKey'];
            if (empty($values)) {
                $elementData[$key] = '';
                $elementData[$key . '__num'] = null;
            } elseif (count($values) === 1) {
                $elementData[$key] = $values[0];
                $elementData[$key . '__num'] = $numValues[0];
            } else {
                $elementData[$key] = end($values);
                $elementData[$key . '__num'] = end($numValues);
            }
        }

        $roomData['elements'][] = $elementData;
    }
    $result[] = $roomData;
}
$mysqli->close();

// 5️⃣ Output as JSON — wrap in envelope so JS gets both paramMeta and rooms
header('Content-Type: application/json');
echo json_encode([
    'paramMeta' => $paramMeta,
    'rooms'     => $result,
], JSON_PRETTY_PRINT);