<?php
// ------------------- ERROR MESSAGES -------------------

class ErrorMessages
{
    public const MESSAGES = [
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
        'abd_missing' => '%s: %s --- %s:::Raumparameter - Abdunkelbarkeit-> %s Element präsent, aber Raumparameter=0!<br>',
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
}

// ------------------- UTILITIES -------------------

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

class RoomParameterChecks
{
    private array $roomParams;
    private array $messages = [];

    public function __construct(array $roomParams, array &$messages)
    {
        $this->roomParams = $roomParams;
        $this->messages = &$messages; // reference for adding messages
    }


    public function checkDependencyNonZero(string $p1, string $p2): void
    {
        if (($this->roomParams[$p2] ?? 0) > 0 && (($this->roomParams[$p1] ?? 0) < 1)) {
            $this->messages[] = sprintf(
                ErrorMessages::MESSAGES['dependency_non_zero'],
                $this->roomParams['Raumbezeichnung'], $this->roomParams['Raumnr'], $this->roomParams['idTABELLE_Räume'],
                $p2, $p1, $this->roomParams[$p1] ?? 'n/a', $p2, $this->roomParams[$p2]
            );
        }
    }

    public function checkMaxValue(string $param, float $max): void
    {
        if (($this->roomParams[$param] ?? 0) > $max) {
            $this->messages[] = sprintf(
                ErrorMessages::MESSAGES['max_value'],
                $this->roomParams['Raumbezeichnung'], $this->roomParams['Raumnr'], $this->roomParams['idTABELLE_Räume'],
                $param, $this->roomParams[$param], $max
            );
        }
    }

    public function checkMaxValueRev(string $param, float $max, string $extra = ""): void
    {
        if (!isset($this->roomParams[$param]) || $this->roomParams[$param] < $max) {
            $this->messages[] = sprintf(
                ErrorMessages::MESSAGES['max_value_rev'],
                $this->roomParams['Raumbezeichnung'], $this->roomParams['Raumnr'], $this->roomParams['idTABELLE_Räume'],
                $extra, $max, $this->roomParams[$param] ?? 'n/a'
            );
        }
    }

    public function checkRG(): void
    {
        $rg = $this->roomParams['Anwendungsgruppe'] ?? null;
        $sv = $this->roomParams['SV'] ?? null;
        $zsv = $this->roomParams['ZSV'] ?? null;
        $fb = $this->roomParams['Fussboden OENORM B5220'] ?? null;

        if ($rg !== null) {
            if ($rg >= 1 && $sv != 1)
                $this->messages[] = sprintf(ErrorMessages::MESSAGES['rg_sv'],
                    $this->roomParams['Raumbezeichnung'], $this->roomParams['Raumnr'], $this->roomParams['idTABELLE_Räume'], $sv, $rg);
            if ($rg == 2 && $zsv != 1)
                $this->messages[] = sprintf(ErrorMessages::MESSAGES['rg_zsv'],
                    $this->roomParams['Raumbezeichnung'], $this->roomParams['Raumnr'], $this->roomParams['idTABELLE_Räume'], $zsv, $rg);
            if ($rg == 2 && $fb != "Klasse 1")
                $this->messages[] = sprintf(ErrorMessages::MESSAGES['rg_floor'],
                    $this->roomParams['Raumbezeichnung'], $this->roomParams['Raumnr'], $this->roomParams['idTABELLE_Räume'], $fb);
            if ($rg != 2 && $fb === "Klasse 1")
                $this->messages[] = sprintf(ErrorMessages::MESSAGES['rg_floor2'],
                    $this->roomParams['Raumbezeichnung'], $this->roomParams['Raumnr'], $this->roomParams['idTABELLE_Räume'], $fb);
        }
    }

    public function checkSummeLeistungen(): void
    {
        $sum = array_sum([
            intval($this->roomParams['ET_Anschlussleistung_AV_W'] ?? 0),
            intval($this->roomParams['ET_Anschlussleistung_SV_W'] ?? 0),
            intval($this->roomParams['ET_Anschlussleistung_ZSV_W'] ?? 0),
            intval($this->roomParams['ET_Anschlussleistung_USV_W'] ?? 0)
        ]);
        $gesamt = intval($this->roomParams['ET_Anschlussleistung_W'] ?? 0);
        if ($sum != $gesamt) {
            $this->messages[] = sprintf(
                ErrorMessages::MESSAGES['sum_leistung'],
                $this->roomParams['Raumbezeichnung'], $this->roomParams['Raumnr'], $this->roomParams['idTABELLE_Räume'],
                $gesamt, $sum,
                implode("+", [
                    intval($this->roomParams['ET_Anschlussleistung_AV_W'] ?? 0),
                    intval($this->roomParams['ET_Anschlussleistung_SV_W'] ?? 0),
                    intval($this->roomParams['ET_Anschlussleistung_ZSV_W'] ?? 0),
                    intval($this->roomParams['ET_Anschlussleistung_USV_W'] ?? 0)
                ])
            );
        }
    }
}

// ------------------- ELEMENT CHECKS -------------------

class ElementChecks
{
    private array $roomParams;
    private array $messages = [];

    public function __construct(array $roomParams, array &$messages)
    {
        $this->roomParams = $roomParams;
        $this->messages = &$messages;
    }

    public function checkRoomForParametersCauseElementParamCategory(int $paramID, array $row): void
    {
        $map = [
            117 => '1 Kreis O2', 121 => '1 Kreis DL-5', 122 => '1 Kreis Va', 123 => 'DL-10',
            124 => 'NGA', 125 => 'N2O', 126 => 'CO2', 127 => 'ET_RJ45-Ports'
        ];
        $name = $map[$paramID] ?? null;
        if ($name && (($this->roomParams[$name] ?? 0) < 1)) {
            $this->messages[] = sprintf(
                ErrorMessages::MESSAGES['element_param'],
                $this->roomParams['Raumbezeichnung'], $this->roomParams['Raumnr'], $this->roomParams['idTABELLE_Räume'],
                $name, $row['Bezeichnung'], $name, $this->roomParams[$name] ?? 'n/a'
            );
        }
    }

    public function checkRoomForNa(array $NAs): void
    {
        foreach ($NAs as $na) {
            if (($this->roomParams[$na] ?? 0) < 1) {
                $this->messages[] = sprintf(
                    ErrorMessages::MESSAGES['na_missing'],
                    $this->roomParams['Raumbezeichnung'], $this->roomParams['Raumnr'], $this->roomParams['idTABELLE_Räume'], $na
                );
            }
        }
    }

    public function check4RoomParam(string $param, array $row): void
    {
        if (($this->roomParams[$param] ?? 0) < 1) {
            $this->messages[] = sprintf(
                ErrorMessages::MESSAGES['element_param4'],
                $this->roomParams['Raumbezeichnung'], $this->roomParams['Raumnr'], $this->roomParams['idTABELLE_Räume'],
                $row['Bezeichnung'], $param, $this->roomParams[$param] ?? 'n/a'
            );
        }
    }

    public function check4RoomParamz(string $p1, string $p2, array $row): void
    {
        if ((($this->roomParams[$p1] ?? 0) < 1) && (($this->roomParams[$p2] ?? 0) < 1)) {
            $this->messages[] = sprintf(
                ErrorMessages::MESSAGES['element_param4z'],
                $this->roomParams['Raumbezeichnung'], $this->roomParams['Raumnr'], $this->roomParams['idTABELLE_Räume'],
                $row['Bezeichnung'], $p1, $p2, $this->roomParams[$p1] ?? 'n/a'
            );
        }
    }

    public function check4Vorabsperr(array $elements_in_room): void
    {
        if (intval($this->roomParams["1 Kreis DL-5"] ?? 0) == 0) {
            $this->messages[] = sprintf(
                ErrorMessages::MESSAGES['stativ_dl5'],
                $this->roomParams['Raumbezeichnung'], $this->roomParams['Raumnr'], $this->roomParams['idTABELLE_Räume']
            );
        }
        $found = false;
        foreach ($elements_in_room as $el) {
            if ($el["idTABELLE_Elemente"] == 664) {
                $found = true;
                break;
            }
        }
        if (!$found) {
            $this->messages[] = sprintf(
                ErrorMessages::MESSAGES['stativ_vorabsperr'],
                $this->roomParams['Raumbezeichnung'], $this->roomParams['Raumnr'], $this->roomParams['idTABELLE_Räume']
            );
        }
    }

    public function checkRoomLeistungssumme(array $P, string $extra = ""): void
    {
        $map = ["NoNA", "AV", "SV", "ZSV", "USV"];
        foreach ($P as $i => $val) {
            if ($i > 0 && $i < 5) {
                if ($val > ($this->roomParams['ET_Anschlussleistung_' . $map[$i] . '_W'] ?? 0)) {
                    $this->messages[] = sprintf(
                        ErrorMessages::MESSAGES['leistung_sum'],
                        $this->roomParams['Raumbezeichnung'], $this->roomParams['Raumnr'], $this->roomParams['idTABELLE_Räume'],
                        $extra, $map[$i], $val, $map[$i], $this->roomParams['ET_Anschlussleistung_' . $map[$i] . '_W'] ?? 'n/a'
                    );
                }
                if ($val > 8000 && $i === 3) {
                    $this->messages[] = sprintf(
                        ErrorMessages::MESSAGES['leistung_8kW'],
                        $this->roomParams['Raumbezeichnung'], $this->roomParams['Raumnr'], $this->roomParams['idTABELLE_Räume'],
                        $extra, $map[$i]
                    );
                }
            }
            if ($val === 0 && $i > 0 && ($this->roomParams[$map[$i]] ?? 0) === 1) {
                $this->messages[] = sprintf(
                    ErrorMessages::MESSAGES['leistung_zero_na'],
                    $this->roomParams['Raumbezeichnung'], $this->roomParams['Raumnr'], $this->roomParams['idTABELLE_Räume'],
                    $extra, $map[$i]
                );
            }
        }
    }
}

// ------------------- HELPER FUNCTION -------------------

function distribute(float $x, array $P, array $NAs): array
{
    $map = ["AV" => 1, "SV" => 2, "ZSV" => 3, "USV" => 4];
    if ($x > 0) {
        if (empty($NAs)) {
            $P[0] += $x;
        } else {
            foreach ($NAs as $NA) {
                if (isset($map[$NA])) {
                    $P[$map[$NA]] += $x / count($NAs);
                } else {
                    $P[0] += $x;
                }
            }
        }
    }
    return $P;
}

// ------------------- MAIN EXECUTION -------------------

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

$strahlenIDs = [60, 167, 168, 170, 259, 317, 484, 489, 579, 580, 1182, 1388, 1390, 1417, 1461, 1462, 1660, 1680];
$CEE_IDs = [60, 167];
$abdunkelIds = [
    431, 118, 119, 120, 122, 153, 253, 256, 268, 312, 573, 594, 615, 637, 638, 658, 693, 770, 874, 1042, 1083, 1368, 1498, 1503, 1525, 1892, 620, 624, 1362, 1686
];

$bezeichnungMappings = [
    "digestori" => "HT_Abluft_Digestorium_Stk",
    "sicherheitsschrank" => ["HT_Abluft_Sicherheitsschrank_Stk", "HT_Abluft_Sicherheitsschrank_Unterbau_Stk"]
];
$elementMappings = [
    "67" => "1 Kreis O2", "68" => "1 Kreis Va", "69" => "1 Kreis DL-5", "161" => "N2O", "162" => "NGA", "163" => "DL-10", "342" => "CO2",
    "64" => "1 Kreis O2", "65" => "1 Kreis Va", "66" => "1 Kreis DL-5", "75" => "1 Kreis O2", "76" => "1 Kreis Va", "77" => "1 Kreis DL-5",
    "202" => "NGA", "203" => "N2O", "288" => "DL-10", "289" => "CO2", "1090" => "CO2", "1086" => "1 Kreis O2", "1087" => "1 Kreis Va",
    "1088" => "1 Kreis DL-5", "1089" => "CO2", "1103" => "1 Kreis O2", "1104" => "1 Kreis Va", "1105" => "1 Kreis DL-5", "1106" => "CO2",
    "1327" => "N2", "168" => "1 Kreis DL-5", "170" => "1 Kreis DL-5", "12" => "1 Kreis DL-5", "485" => "1 Kreis DL-5", "680" => "1 Kreis DL-5",
    "907" => "1 Kreis DL-5", "1001" => "1 Kreis DL-5", "1074" => "1 Kreis DL-5", "1553" => "1 Kreis DL-5", "1654" => "1 Kreis DL-5",
    "154" => "1 Kreis DL-5", "155" => "1 Kreis DL-5", "165" => "1 Kreis DL-5", "194" => "1 Kreis DL-5", "233" => "1 Kreis DL-5",
    "286" => "1 Kreis DL-5", "287" => "1 Kreis DL-5", "393" => "1 Kreis DL-5", "1076" => "1 Kreis DL-5"
];
$elementMappingsElementID = ["2.34.19" => "Laseranwendung", "2.56.16" => "Laseranwendung"];

foreach ($raumparameter as $roomID => $roomParams) {
    if (!empty($roomIDsArray) && !in_array($roomID, $roomIDsArray)) continue;

    $roomChecker = new RoomParameterChecks($roomParams, $messages);
    $elementChecker = new ElementChecks($roomParams, $messages);

    // Room parameter checks
    $roomChecker->checkRG();
    $roomChecker->checkSummeLeistungen();
    $roomChecker->checkDependencyNonZero('IT Anbindung', 'ET_RJ45-Ports');
    $roomChecker->checkDependencyNonZero('ET_RJ45-Ports', 'IT Anbindung');
    $roomChecker->checkMaxValue('ET_Anschlussleistung_ZSV_W', 8000);

    foreach (['AV', 'SV', 'ZSV', 'USV'] as $na) {
        $roomChecker->checkDependencyNonZero($na, "ET_Anschlussleistung_{$na}_W");
        $roomChecker->checkDependencyNonZero($na, "EL_{$na} Steckdosen Stk");
    }

    foreach (['1 Kreis O2' => '2 Kreis O2', '1 Kreis Va' => '2 Kreis Va', '1 Kreis DL-5' => '2 Kreis DL-5'] as $p1 => $p2) {
        $roomChecker->checkDependencyNonZero($p1, $p2);
    }

    // Elements in room
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
         HAVING tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume = ? AND SummevonAnzahl > 0
         ORDER BY tabelle_elemente.ElementID, tabelle_varianten.Variante"
    );
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
        if (in_array($row['idTABELLE_Elemente'], $strahlenIDs)) {
            $elementChecker->check4RoomParam("Strahlenanwendung", $row);
            if (in_array($row['idTABELLE_Elemente'], $CEE_IDs))
                $elementChecker->check4RoomParam("EL_Roentgen 16A CEE Stk", $row);
        }
        if (in_array($row['idTABELLE_Elemente'], $abdunkelIds)) {
            $elementChecker->check4RoomParam("Abdunkelbarkeit", $row);
        }
        foreach ($bezeichnungMappings as $kw => $params) {
            if (stripos($row['Bezeichnung'], $kw) !== false) {
                if (is_array($params)) {
                    $elementChecker->check4RoomParamz($params[0], $params[1], $row);
                } else {
                    $elementChecker->check4RoomParam($params, $row);
                }
            }
        }
        foreach ($elementMappings as $prefix => $param) {
            if (intval($row['idTABELLE_Elemente']) == $prefix)
                $elementChecker->check4RoomParam($param, $row);
        }
        foreach ($elementMappingsElementID as $prefix => $param) {
            if (str_starts_with($row['ElementID'], $prefix))
                $elementChecker->check4RoomParam($param, $row);
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
                            $elementChecker->checkRoomForParametersCauseElementParamCategory($parameterInfo['idTABELLE_Parameter'], $row);
                        if ($parameterInfo['idTABELLE_Parameter'] === 82) {
                            $tempNA_perElement = array_merge(
                                getComponents($parameterInfo['Wert']),
                                getComponents($parameterInfo['Einheit'])
                            );
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
                        $elementChecker->checkRoomForParametersCauseElementParamCategory($parameterInfo['idTABELLE_Parameter'], $row);
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
            $messages[] = sprintf(
                ErrorMessages::MESSAGES['leistung_na_missing'],
                $roomParams['Raumbezeichnung'], $roomParams['Raumnr'], $roomParams['idTABELLE_Räume'], $row['Bezeichnung']
            );
        }
    }

    if ($check4Vorabsperrkasten) $elementChecker->check4Vorabsperr($elements_in_room);
    $roomChecker->checkMaxValueRev("HT_Waermeabgabe_W", $Abwarme, " (INKL. GLZ)");
    $roomChecker->checkMaxValueRev("HT_Waermeabgabe_W", $AbwarmeExkl, " (EXKL. GLZ)");
    $elementChecker->checkRoomForNa($NetzArtenImRaum);
    $elementChecker->checkRoomLeistungssumme($LeistungImRaum, " (INKL. GLZ)");
    $elementChecker->checkRoomLeistungssumme($LeistungImRaumExkl, " (EXKL. GLZ)");

}


$mysqli->close();

if (empty($messages)) {
    echo sprintf(ErrorMessages::MESSAGES['no_errors'], 'Alle', 'ausgewählten', 'n/a');
} else {
    foreach ($messages as $msg) echo br2nl($msg);
}
?>
