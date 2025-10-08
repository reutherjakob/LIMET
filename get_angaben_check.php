<?php


// --- CONSTS ---


$allElementMappings = [
    727 => ['param' => "HT_Abluft_Digestorium_Stk"],
    1212 => ['param' => "HT_Abluft_Digestorium_Stk"],
    1456 => ['param' => "HT_Abluft_Digestorium_Stk"],
    1600 => ['param' => "HT_Abluft_Digestorium_Stk"],
    1601 => ['param' => "HT_Abluft_Digestorium_Stk"],
    1602 => ['param' => "HT_Abluft_Digestorium_Stk"],
    1604 => ['param' => "HT_Abluft_Digestorium_Stk"],
    1603 => ['param' => "HT_Abluft_Digestorium_Stk"],
    1605 => ['param' => "HT_Abluft_Digestorium_Stk"],
    1606 => ['param' => "HT_Abluft_Digestorium_Stk"],
    1092 => ['param' => ["HT_Abluft_Sicherheitsschrank_Stk", "HT_Abluft_Sicherheitsschrank_Unterbau_Stk"]],
    1093 => ['param' => ["HT_Abluft_Sicherheitsschrank_Stk", "HT_Abluft_Sicherheitsschrank_Unterbau_Stk"]],
    1112 => ['param' => ["HT_Abluft_Sicherheitsschrank_Stk", "HT_Abluft_Sicherheitsschrank_Unterbau_Stk"]],
    1688 => ['param' => ["HT_Abluft_Sicherheitsschrank_Stk", "HT_Abluft_Sicherheitsschrank_Unterbau_Stk"]],
    570 => ['param' => "Laseranwendung"],
    778 => ['param' => "Laseranwendung"],
    890 => ['param' => "Laseranwendung"],
    1331 => ['param' => "Laseranwendung"],
    1332 => ['param' => "Laseranwendung"],
    989 => ['param' => "Laseranwendung"],
    1909 => ['param' => "Laseranwendung"],
    60 => ['param' => ['EL_Roentgen 16A CEE Stk', 'Strahlenanwendung']],
    167 => ['param' => ['EL_Roentgen 16A CEE Stk', 'Strahlenanwendung']],
    168 => ['param' => ['Strahlenanwendung']],
    170 => ['param' => 'Strahlenanwendung'],
    259 => ['param' => 'Strahlenanwendung'],
    317 => ['param' => 'Strahlenanwendung'],
    484 => ['param' => 'Strahlenanwendung'],
    489 => ['param' => 'Strahlenanwendung'],
    579 => ['param' => 'Strahlenanwendung'],
    580 => ['param' => 'Strahlenanwendung'],
    1182 => ['param' => 'Strahlenanwendung'],
    1388 => ['param' => 'Strahlenanwendung'],
    1390 => ['param' => 'Strahlenanwendung'],
    1417 => ['param' => 'Strahlenanwendung'],
    1461 => ['param' => 'Strahlenanwendung'],
    1462 => ['param' => 'Strahlenanwendung'],
    1660 => ['param' => 'Strahlenanwendung'],
    1680 => ['param' => 'Strahlenanwendung'],
    67 => ['param' => '1 Kreis O2'],
    68 => ['param' => '1 Kreis Va'],
    69 => ['param' => '1 Kreis DL-5'],
    161 => ['param' => 'N2O'],
    162 => ['param' => 'NGA'],
    163 => ['param' => 'DL-10'],
    342 => ['param' => 'CO2'],
    64 => ['param' => '1 Kreis O2'],
    65 => ['param' => '1 Kreis Va'],
    66 => ['param' => '1 Kreis DL-5'],
    75 => ['param' => '1 Kreis O2'],
    76 => ['param' => '1 Kreis Va'],
    77 => ['param' => '1 Kreis DL-5'],
    202 => ['param' => 'NGA'],
    203 => ['param' => 'N2O'],
    288 => ['param' => 'DL-10'],
    289 => ['param' => 'CO2'],
    1090 => ['param' => 'CO2'],
    1086 => ['param' => '1 Kreis O2'],
    1087 => ['param' => '1 Kreis Va'],
    1088 => ['param' => '1 Kreis DL-5'],
    1089 => ['param' => 'CO2'],
    1103 => ['param' => '1 Kreis O2'],
    1104 => ['param' => '1 Kreis Va'],
    1105 => ['param' => '1 Kreis DL-5'],
    1106 => ['param' => 'CO2'],
    1327 => ['param' => 'N2'],
    12 => ['param' => '1 Kreis DL-5'],
    485 => ['param' => '1 Kreis DL-5'],
    680 => ['param' => '1 Kreis DL-5'],
    907 => ['param' => '1 Kreis DL-5'],
    1001 => ['param' => '1 Kreis DL-5'],
    1074 => ['param' => '1 Kreis DL-5'],
    1553 => ['param' => '1 Kreis DL-5'],
    1654 => ['param' => '1 Kreis DL-5'],
    154 => ['param' => '1 Kreis DL-5'],
    155 => ['param' => '1 Kreis DL-5'],
    165 => ['param' => '1 Kreis DL-5'],
    194 => ['param' => '1 Kreis DL-5'],
    233 => ['param' => '1 Kreis DL-5'],
    286 => ['param' => '1 Kreis DL-5'],
    287 => ['param' => '1 Kreis DL-5'],
    393 => ['param' => '1 Kreis DL-5'],
    1076 => ['param' => '1 Kreis DL-5']
];


// ------------------- ERROR MESSAGES -------------------

const ERROR_MESSAGES = [
    'dependency_non_zero' => '%s: %s --- %s:::Raumparameter - %s -> %s ist %s, aber %s ist %s<br>',
    'max_value' => '%s: %s --- %s:::Raumparameter - LeistungZSV-> %s...%s übersteigt max=%s.<br>',
    'max_value_rev' => '%s: %s --- %s:::Raumparameter - Abwärme%s-> %s übersteigt Raumangabe=%s.<br>',
    'rg_sv' => '%s: %s --- %s:::RG -> SV=%s, aber RG =%s.<br>',
    'rg_zsv' => '%s: %s --- %s:::RG = 2 -> ZSV=%s, aber RG =%s.<br>',
    'rg_floor' => '%s: %s --- %s:::RG = 2 -> Fußboden muss Klasse 1 sein, ist aber %s.<br>',
    'rg_floor2' => '%s: %s --- %s:::RG is nicht 2 -> Muss der Fussboden OENORM B5220 hier Klasse 1 sein? %s.<br>',
    'sum_leistung' => '%s: %s --- %s:::Raumparameter - Leistung ∑-> %s= ∑Anschlussleistung(Raum) != ∑P je Netzart! (%s=%s)<br>',
    'element_param' => '%s: %s --- %s:::Raumparameter - ElementPort-> %s Element %s präsent, aber Raumparameter=%s %s! <br>',
    'na_missing' => '%s: %s --- %s:::Raumparameter - Netzarten-> %s in  Element präsent, aber Raumparameter=0!<br>',
    'element_param4' => '%s: %s --- %s:::Raumparameter - Elemente -> Element %s präsent, aber Raumparam %s= %s! <br>',
    'element_param4z' => '%s: %s --- %s:::Raumparameter -> Element %s präsent, aber Raumparam %s/%s= %s! <br>',
    'stativ_dl5' => '%s: %s --- %s:::Elementparameter - Stativ ->  Stativ, braucht Druckluft! <br>',
    'stativ_vorabsperr' => '%s: %s --- %s:::ElementPort -> Gasanschluss am Stativ, braucht Vorabsperrkasten! <br>',
    'leistung_na_missing' => '%s: %s --- %s:::Netzarten->  %s hat Leistung aber keine Netzart!<br>',
    'leistung_zero_na' => '%s: %s --- %s:::Leistung Elemente in Raum%s -> P[Elemente][%s]=0, aber Raumparameter=1? Element parametrisieren oder NA aus Raum hinterfragen <br>',
    'leistung_sum' => '%s: %s --- %s:::Raumparameter - Leistung ∑%s-> ∑P[%s](Elemente) =%s > %s_W=%s <br>',
    'leistung_8kW' => '%s: %s --- %s:::Raumparameter - Leistung ∑%s-> P[%s](Elemente) > 8kW! <br>',
    'no_errors' => '%s: %s --- %s:::INFO  ->  Keine Fehler gefunden.<br>',

];

// ------------------- UTILITY FUNCTIONS -------------------
function abcTo123($char)
{
    return ord(strtolower($char)) - ord('a') + 1;
}

function unitMultiplier($text)
{
    return stripos($text, 'k') !== false ? 1000 : 1;
}

function getQueryParam($param)
{
    return $_GET[$param] ?? null;
}

function getComponents($input)
{
    $valid = ["AV", "SV", "ZSV", "USV"];
    return array_values(array_filter(explode("/", $input), fn($c) => in_array($c, $valid)));
}

function getUniqueComponents($new, $existing)
{
    foreach ($new as $c) if (!in_array($c, $existing)) $existing[] = $c;
    return $existing;
}

// ------------------- ROOM PARAMETER CHECKS -------------------
function check_dependency_non_zero(&$msgs, $roomParams, $p1, $p2)
{
    if (($roomParams[$p2] ?? 0) > 0 && (($roomParams[$p1] ?? 0) < 1)) {
        $msgs[] = sprintf(ERROR_MESSAGES['dependency_non_zero'],
            $roomParams['Raumbezeichnung'], $roomParams['Raumnr'], $roomParams['idTABELLE_Räume'],
            $p2, $p1, $roomParams[$p1] ?? 'n/a', $p2, $roomParams[$p2]);
    }
}

function check_max_value(&$msgs, $roomParams, $param, $max)
{
    if (($roomParams[$param] ?? 0) > $max) {
        $msgs[] = sprintf(ERROR_MESSAGES['max_value'],
            $roomParams['Raumbezeichnung'], $roomParams['Raumnr'], $roomParams['idTABELLE_Räume'],
            $param, $roomParams[$param], $max);
    }
}

function check_max_value_rev(&$msgs, $roomParams, $param, $max, $extra = "")
{
    if (!isset($roomParams[$param]) || $roomParams[$param] < $max) {
        $msgs[] = sprintf(ERROR_MESSAGES['max_value_rev'],
            $roomParams['Raumbezeichnung'], $roomParams['Raumnr'], $roomParams['idTABELLE_Räume'],
            $extra, $max, $roomParams[$param] ?? 'n/a');
    }
}

function check_RG(&$msgs, $roomParams)
{
    $rg = $roomParams['Anwendungsgruppe'] ?? null;
    $sv = $roomParams['SV'] ?? null;
    $zsv = $roomParams['ZSV'] ?? null;
    $fb = $roomParams['Fussboden OENORM B5220'] ?? null;
    if ($rg !== null) {
        if ($rg >= 1 && $sv != 1)
            $msgs[] = sprintf(ERROR_MESSAGES['rg_sv'], $roomParams['Raumbezeichnung'], $roomParams['Raumnr'], $roomParams['idTABELLE_Räume'], $sv, $rg);
        if ($rg == 2 && $zsv != 1)
            $msgs[] = sprintf(ERROR_MESSAGES['rg_zsv'], $roomParams['Raumbezeichnung'], $roomParams['Raumnr'], $roomParams['idTABELLE_Räume'], $zsv, $rg);
        if ($rg == 2 && $fb != "Klasse 1")
            $msgs[] = sprintf(ERROR_MESSAGES['rg_floor'], $roomParams['Raumbezeichnung'], $roomParams['Raumnr'], $roomParams['idTABELLE_Räume'], $fb);
        if ($rg != 2 && $fb === "Klasse 1")
            $msgs[] = sprintf(ERROR_MESSAGES['rg_floor2'], $roomParams['Raumbezeichnung'], $roomParams['Raumnr'], $roomParams['idTABELLE_Räume'], $fb);
    }
}

function check_summe_leistungen(&$msgs, $roomParams)
{
    $sum = array_sum([
        intval($roomParams['ET_Anschlussleistung_AV_W'] ?? 0),
        intval($roomParams['ET_Anschlussleistung_SV_W'] ?? 0),
        intval($roomParams['ET_Anschlussleistung_ZSV_W'] ?? 0),
        intval($roomParams['ET_Anschlussleistung_USV_W'] ?? 0)
    ]);
    $gesamt = intval($roomParams['ET_Anschlussleistung_W'] ?? 0);
    if ($sum != $gesamt) {
        $msgs[] = sprintf(ERROR_MESSAGES['sum_leistung'],
            $roomParams['Raumbezeichnung'], $roomParams['Raumnr'], $roomParams['idTABELLE_Räume'],
            $roomParams['ET_Anschlussleistung_W'] ?? 'n/a', $sum,
            implode("+", [
                intval($roomParams['ET_Anschlussleistung_AV_W'] ?? 0),
                intval($roomParams['ET_Anschlussleistung_SV_W'] ?? 0),
                intval($roomParams['ET_Anschlussleistung_ZSV_W'] ?? 0),
                intval($roomParams['ET_Anschlussleistung_USV_W'] ?? 0)
            ])
        );
    }
}

// ------------------- ELEMENT CHECKS -------------------
function check_room_for_parameters_cause_elementParamKathegorie(&$msgs, $roomParams, $paramID, $row)
{
    $map = [
        117 => '1 Kreis O2', 121 => '1 Kreis DL-5', 122 => '1 Kreis Va', 123 => 'DL-10',
        124 => 'NGA', 125 => 'N2O', 126 => 'CO2', 127 => 'ET_RJ45-Ports'
    ];
    $name = $map[$paramID] ?? null;
    if ($name && (($roomParams[$name] ?? 0) < 1)) {
        $msgs[] = sprintf(ERROR_MESSAGES['element_param'],
            $roomParams['Raumbezeichnung'], $roomParams['Raumnr'], $roomParams['idTABELLE_Räume'],
            $name, $row['Bezeichnung'], $name, $roomParams[$name] ?? 'n/a');
    }
}

function check_room_for_na(&$msgs, $roomParams, $NAs)
{
    foreach ($NAs as $na) {
        if (($roomParams[$na] ?? 0) < 1) {
            $msgs[] = sprintf(ERROR_MESSAGES['na_missing'],
                $roomParams['Raumbezeichnung'], $roomParams['Raumnr'], $roomParams['idTABELLE_Räume'], $na);
        }
    }
}

function check_4_room_param(&$msgs, $roomParams, $param, $row)
{
    if (($roomParams[$param] ?? 0) < 1) {
        $msgs[] = sprintf(ERROR_MESSAGES['element_param4'],
            $roomParams['Raumbezeichnung'], $roomParams['Raumnr'], $roomParams['idTABELLE_Räume'],
            $row['Bezeichnung'], $param, $roomParams[$param] ?? 'n/a');
    }
}


function check4vorabsperr(&$msgs, $roomParams, $elements_in_room)
{
    if (intval($roomParams["1 Kreis DL-5"] ?? 0) == 0) {
        $msgs[] = sprintf(ERROR_MESSAGES['stativ_dl5'],
            $roomParams['Raumbezeichnung'], $roomParams['Raumnr'], $roomParams['idTABELLE_Räume']);
    }
    $found = false;
    foreach ($elements_in_room as $el) {
        if ($el["idTABELLE_Elemente"] == 664) {
            $found = true;
            break;
        }
    }
    if (!$found) {
        $msgs[] = sprintf(ERROR_MESSAGES['stativ_vorabsperr'],
            $roomParams['Raumbezeichnung'], $roomParams['Raumnr'], $roomParams['idTABELLE_Räume']);
    }
}

function check_room_Leistungssumme(&$msgs, $roomParams, $P, $extra = "")
{
    $map = ["NoNA", "AV", "SV", "ZSV", "USV"];
    foreach ($P as $i => $val) {
        if ($i > 0 && $i < 5) {
            if ($val > ($roomParams['ET_Anschlussleistung_' . $map[$i] . '_W'] ?? 0)) {
                $msgs[] = sprintf(ERROR_MESSAGES['leistung_sum'],
                    $roomParams['Raumbezeichnung'], $roomParams['Raumnr'], $roomParams['idTABELLE_Räume'],
                    $extra, $map[$i], $val, $map[$i], $roomParams['ET_Anschlussleistung_' . $map[$i] . '_W'] ?? 'n/a');
            }
            if ($val > 8000 && $i === 3) {
                $msgs[] = sprintf(ERROR_MESSAGES['leistung_8kW'],
                    $roomParams['Raumbezeichnung'], $roomParams['Raumnr'], $roomParams['idTABELLE_Räume'],
                    $extra, $map[$i]);
            }
        }
        if ($val === 0 && $i > 0 && ($roomParams[$map[$i]] ?? 0) === 1) {
            $msgs[] = sprintf(ERROR_MESSAGES['leistung_zero_na'],
                $roomParams['Raumbezeichnung'], $roomParams['Raumnr'], $roomParams['idTABELLE_Räume'],
                $extra, $map[$i]);
        }
    }
}

function distribute($x, $P, $NAs)
{
    $map = ["AV" => 1, "SV" => 2, "ZSV" => 3, "USV" => 4];
    if ($x > 0) {
        if (empty($NAs)) $P[0] += $x;
        else foreach ($NAs as $NA) isset($map[$NA]) ? $P[$map[$NA]] += $x / count($NAs) : $P[0] += $x;
    }
    return $P;
}

// ------------------- MAIN -------------------
if (!function_exists('utils_connect_sql')) include "utils/_utils.php";
check_login();

$messages = [];
$roomIDsArray = [];

if (($roomID = getQueryParam('roomID')) !== null) $roomIDsArray = explode(',', $roomID);

$mysqli = utils_connect_sql();
$stmt = $mysqli->prepare(
    "SELECT * FROM tabelle_räume
     INNER JOIN tabelle_funktionsteilstellen ON tabelle_räume.TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen = tabelle_funktionsteilstellen.idTABELLE_Funktionsteilstellen
     WHERE tabelle_räume.tabelle_projekte_idTABELLE_Projekte = ? 
     ORDER BY tabelle_räume.tabelle_projekte_idTABELLE_Projekte"
);
$stmt->bind_param("i", $_SESSION["projectID"]);
$stmt->execute();
$result = $stmt->get_result();
$raumparameter = [];
while ($row = $result->fetch_assoc()) $raumparameter[$row['idTABELLE_Räume']] = $row;

$stmt = $mysqli->prepare(
    "SELECT tabelle_projekt_elementparameter.Wert, tabelle_projekt_elementparameter.Einheit, tabelle_projekt_elementparameter.tabelle_Varianten_idtabelle_Varianten, 
            tabelle_projekt_elementparameter.tabelle_elemente_idTABELLE_Elemente, tabelle_parameter.Bezeichnung, tabelle_parameter_kategorie.Kategorie, 
            tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie, tabelle_parameter.idTABELLE_Parameter 
     FROM tabelle_parameter_kategorie 
     INNER JOIN (
         tabelle_parameter 
         INNER JOIN tabelle_projekt_elementparameter ON tabelle_parameter.idTABELLE_Parameter = tabelle_projekt_elementparameter.tabelle_parameter_idTABELLE_Parameter
     ) 
     ON tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie = tabelle_parameter.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie
     WHERE tabelle_projekt_elementparameter.tabelle_projekte_idTABELLE_Projekte = ? AND tabelle_parameter.`Bauangaben relevant` = 1
     ORDER BY tabelle_parameter_kategorie.Kategorie, tabelle_projekt_elementparameter.tabelle_elemente_idTABELLE_Elemente, tabelle_parameter.Bezeichnung"
);
$stmt->bind_param("i", $_SESSION["projectID"]);
$stmt->execute();
$result = $stmt->get_result();
$elementParamInfos = [];
while ($row = $result->fetch_assoc()) $elementParamInfos[] = $row;


// ------------------- MAIN ROOM LOOP -------------------
foreach ($raumparameter as $roomID => $roomParams) {
    if (!empty($roomIDsArray) && !in_array($roomID, $roomIDsArray)) continue;
    check_RG($messages, $roomParams);
    check_summe_leistungen($messages, $roomParams);
    check_dependency_non_zero($messages, $roomParams, 'IT Anbindung', 'ET_RJ45-Ports');
    check_dependency_non_zero($messages, $roomParams, 'ET_RJ45-Ports', 'IT Anbindung');
    check_max_value($messages, $roomParams, 'ET_Anschlussleistung_ZSV_W', 8000);
    foreach (['AV', 'SV', 'ZSV', 'USV'] as $na) {
        check_dependency_non_zero($messages, $roomParams, $na, "ET_Anschlussleistung_{$na}_W");
        check_dependency_non_zero($messages, $roomParams, $na, "EL_{$na} Steckdosen Stk");
    }
    foreach (['1 Kreis O2' => '2 Kreis O2', '1 Kreis Va' => '2 Kreis Va', '1 Kreis DL-5' => '2 Kreis DL-5'] as $p1 => $p2)
        check_dependency_non_zero($messages, $roomParams, $p1, $p2);

    // --- ELEMENTS IN ROOM ---
    $stmt = $mysqli->prepare(
        "SELECT tabelle_elemente.ElementID, tabelle_elemente.idTABELLE_Elemente, tabelle_elemente.Bezeichnung, tabelle_varianten.Variante, 
                Sum(tabelle_räume_has_tabelle_elemente.Anzahl) AS SummevonAnzahl
                     FROM tabelle_varianten 
                     INNER JOIN (tabelle_räume_has_tabelle_elemente 
                     INNER JOIN tabelle_elemente ON tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente = tabelle_elemente.idTABELLE_Elemente) 
                     ON tabelle_varianten.idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten
                     WHERE tabelle_räume_has_tabelle_elemente.Verwendung = 1
                     GROUP BY tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_varianten.Variante, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, 
                              tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten, 
                              tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume
                     HAVING tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume = ? 
                        AND SummevonAnzahl > 0
                     ORDER BY tabelle_elemente.ElementID, tabelle_varianten.Variante");

    $stmt->bind_param("i", $roomID);
    $stmt->execute();
    $result = $stmt->get_result();

    $elements_in_room = [];
    $NetzArtenImRaum = [];
    $LeistungImRaum = $LeistungImRaumExkl = array_fill(0, 5, 0);
    $Abwarme = $AbwarmeExkl = 0;
    $check4Vorabsperrkasten = false;

    while ($row = $result->fetch_assoc()) {
        $elements_in_room[] = $row;

        foreach ($allElementMappings as $id => $info) {
            if (intval($row['idTABELLE_Elemente']) == $id) {
                $params = is_array($info['param']) ? $info['param'] : [$info['param']];
                foreach ($params as $param) {
                    check_4_room_param($messages, $roomParams, $param, $row);
                }
            }
        }

        $temp_LeistungElement = 0;
        $tempNA_perElement = [];
        $temp_GLZ = 1.0;
        $Abwarme_el = 0;
        $AnzahlElImRaum = $row['SummevonAnzahl'];

        foreach ($elementParamInfos as $parameterInfo) {
            if ($parameterInfo["tabelle_Varianten_idtabelle_Varianten"] === abcTo123($row['Variante'])
                && $row['idTABELLE_Elemente'] === $parameterInfo["tabelle_elemente_idTABELLE_Elemente"]) {
                switch ($parameterInfo['idTABELLE_Parameter_Kategorie']) {
                    case 2: // ElektroTechnik
                        if ($parameterInfo['idTABELLE_Parameter'] === 127)
                            check_room_for_parameters_cause_elementParamKathegorie($messages, $roomParams, $parameterInfo['idTABELLE_Parameter'], $row);
                        if ($parameterInfo['idTABELLE_Parameter'] === 82) {
                            $tempNA_perElement = array_merge(getComponents($parameterInfo['Wert']), getComponents($parameterInfo['Einheit']));
                            $NetzArtenImRaum = getUniqueComponents($tempNA_perElement, $NetzArtenImRaum);
                        }
                        if ($parameterInfo['idTABELLE_Parameter'] === 18)
                            $temp_LeistungElement = floatval(str_replace(",", ".", preg_replace("/[^0-9.,]/", "", $parameterInfo['Wert']))) * unitMultiplier($parameterInfo["Einheit"]);
                        if ($parameterInfo['idTABELLE_Parameter'] === 133)
                            $temp_GLZ = floatval(str_replace(",", ".", preg_replace("/[^0-9,.]/", "", $parameterInfo['Wert'])));
                        break;
                    case 3: // HKLS
                        if ($parameterInfo["idTABELLE_Parameter"] === 9)
                            $Abwarme_el = floatval(str_replace(",", ".", preg_replace("/[^0-9.,]/", "", $parameterInfo['Wert']))) * unitMultiplier($parameterInfo["Einheit"]);
                        break;
                    case 12: // MEDGAS
                        check_room_for_parameters_cause_elementParamKathegorie($messages, $roomParams, $parameterInfo['idTABELLE_Parameter'], $row);
                        if (stripos($row['Bezeichnung'], "stativ") && !$check4Vorabsperrkasten)
                            $check4Vorabsperrkasten = true;
                        break;
                }
            }
        }
        $Abwarme += $Abwarme_el * $temp_GLZ * $AnzahlElImRaum;
        $AbwarmeExkl += $Abwarme_el * $AnzahlElImRaum;
        $LeistungImRaum = distribute($temp_LeistungElement * $temp_GLZ * $AnzahlElImRaum, $LeistungImRaum, $tempNA_perElement);
        $LeistungImRaumExkl = distribute($temp_LeistungElement * $AnzahlElImRaum, $LeistungImRaumExkl, $tempNA_perElement);

        if ($temp_LeistungElement > 0 && empty($tempNA_perElement)) {
            $messages[] = sprintf(ERROR_MESSAGES['leistung_na_missing'],
                $roomParams['Raumbezeichnung'], $roomParams['Raumnr'], $roomParams['idTABELLE_Räume'], $row['Bezeichnung']);
        }
    }
    if ($check4Vorabsperrkasten) check4vorabsperr($messages, $roomParams, $elements_in_room);
    check_max_value_rev($messages, $roomParams, "HT_Waermeabgabe_W", $Abwarme, " (INKL. GLZ)");
    check_max_value_rev($messages, $roomParams, "HT_Waermeabgabe_W", $AbwarmeExkl, " (EXKL. GLZ)");
    check_room_for_na($messages, $roomParams, $NetzArtenImRaum);
    check_room_Leistungssumme($messages, $roomParams, $LeistungImRaum, " (INKL. GLZ)");
    check_room_Leistungssumme($messages, $roomParams, $LeistungImRaumExkl, " (EXKL. GLZ)");
}
$mysqli->close();
if (empty($messages)) {
    echo sprintf(ERROR_MESSAGES['no_errors'], 'Alle', 'ausgewählten', 'n/a');
} else {
    foreach ($messages as $msg) echo br2nl($msg);
}
?>
