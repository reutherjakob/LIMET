<?php
require_once 'utils/_utils.php';
check_login();

// 🔐 Input Validation
$roomID = filter_input(INPUT_GET, 'roomID', FILTER_VALIDATE_INT);
$K2Return = $_GET['K2Return'] ?? '[]';
$K2Ret = json_decode($K2Return, true); // true = associative array

$projectID = $_SESSION["projectID"] ?? null;

if (!$roomID || !$projectID || !is_array($K2Ret)) {
    http_response_code(400);
    die(json_encode(["error" => "Ungültige Eingabeparameter."]));
}

$mysqli = utils_connect_sql();

// 1️⃣ Elemente im Raum abrufen
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
    WHERE rhe.Verwendung = 1
    GROUP BY 
        e.ElementID, e.Bezeichnung, v.Variante, 
        rhe.`Neu/Bestand`, 
        rhe.TABELLE_Elemente_idTABELLE_Elemente, 
        rhe.tabelle_Varianten_idtabelle_Varianten, 
        rhe.TABELLE_Räume_idTABELLE_Räume
    HAVING rhe.TABELLE_Räume_idTABELLE_Räume = ? AND SummevonAnzahl > 0
    ORDER BY e.ElementID, v.Variante
");
$stmt->bind_param("i", $roomID);
$stmt->execute();
$tabelle_elemente = $stmt->get_result();

// 2️⃣ Projektparameter + zugehörige Kategorien laden (falls K2Return leer => alles verwenden)
$stmt = $mysqli->prepare("
    SELECT 
        k.idTABELLE_Parameter_Kategorie AS KategorieID,
        k.Kategorie,
        p.Abkuerzung AS Bezeichnung,
        p.idTABELLE_Parameter AS ParamID
    FROM tabelle_parameter_kategorie k
    INNER JOIN tabelle_parameter p 
        ON k.idTABELLE_Parameter_Kategorie = p.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie
    INNER JOIN tabelle_projekt_elementparameter pep 
        ON p.idTABELLE_Parameter = pep.tabelle_parameter_idTABELLE_Parameter
    WHERE pep.tabelle_projekte_idTABELLE_Projekte = ? 
        AND p.`Bauangaben relevant` = 1
    GROUP BY k.Kategorie, p.Bezeichnung
    ORDER BY k.Kategorie, p.Bezeichnung
");
$stmt->bind_param("i", $projectID);
$stmt->execute();
$kategorieResult = $stmt->get_result();

$paramInfos = [];
while ($row = $kategorieResult->fetch_assoc()) {
    if (empty($K2Ret) || in_array($row['KategorieID'], $K2Ret)) {
        $paramInfos[$row['ParamID']] = [
            'KategorieID' => $row['KategorieID'],
            'ParamID'     => $row['ParamID'],
            'Bezeichnung' => $row['Bezeichnung'],
            'Kategorie'   => $row['Kategorie'],
        ];
    }
}

// 3️⃣ Elementparameter + Variantenparameter laden
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
        AND p.`Bauangaben relevant` = 1
    ORDER BY k.Kategorie, p.Bezeichnung
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

$mysqli->close();

// 4️⃣ Kombination aus den Daten aufbauen
$result = [];
while ($row = $tabelle_elemente->fetch_assoc()) {
    $elementData = $row;

    foreach ($paramInfos as $paramInfo) {
        $values = [];

        foreach ($elementParamInfos as $elementParamInfo) {
            if (
                $elementParamInfo['ParamID'] == $paramInfo['ParamID'] &&
                $elementParamInfo['elementID'] == $row['TABELLE_Elemente_idTABELLE_Elemente'] &&
                $elementParamInfo['variantenID'] == $row['tabelle_Varianten_idtabelle_Varianten']
            ) {
                $values[] = $elementParamInfo['Wert'] . $elementParamInfo['Einheit'];
            }
        }

        $elementData[$paramInfo['Bezeichnung']] = empty($values)
            ? ''
            : (count($values) === 1 ? $values[0] : end($values));
    }

    $result[] = $elementData;
}

// 5️⃣ ZIP it to JSON
header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT);
