<?php
/**
 * ============================================================================
 *  DATENLOGIK  –  REINE DATENBESCHAFFUNG FÜR DEN CUSTOM-BERICHT
 * ============================================================================
 *
 *  Diese Datei enthält AUSSCHLIESSLICH Datenlogik:
 *    - SQL-Abfragen (Raumdaten, Med.-tech.-Elemente, Parameter, Änderungen)
 *    - Spaltenauflösung (welche SQL-Spalten müssen geladen werden)
 *    - Änderungserkennung
 *
 *  KEINE dieser Funktionen ruft TCPDF auf. Damit ist die Datenbeschaffung
 *  unabhängig vom Layout und einzeln testbar/änderbar.
 *
 *  Abhängigkeiten:
 *    - report_all_params() / report_sql_columns_for()  (paramDefinitions.php)
 *    - filter_old_equal_new()                           (gemeinsame Utils)
 *    - $mapping / $mp2                                  (constDefinitions)
 * ============================================================================
 */


// ----------------------------------------------------------------------------
//  SPALTEN-AUFLÖSUNG
// ----------------------------------------------------------------------------
/**
 * Sammelt alle in der Header-Konfiguration referenzierten Spaltennamen
 * (Platzhalter {Spalte}), damit sie ins SELECT aufgenommen werden.
 */
function report_header_columns(array $headerFields): array
{
    $cols = [];
    foreach ($headerFields as $f) {
        if (preg_match_all('/\{([^}]+)\}/', $f['value'] ?? '', $mm)) {
            foreach ($mm[1] as $c) {
                $cols[] = trim($c);
            }
        }
    }
    return array_values(array_unique($cols));
}

/**
 * Liefert die vollständige, deduplizierte Spaltenliste für eine Raum-Abfrage:
 * ID + alle Header-Spalten + alle gewählten (echten) Parameter-Spalten.
 */
function report_room_columns(array $headerFields, array $selectedKeys): array
{
    $base = array_merge(['idTABELLE_Räume'], report_header_columns($headerFields));
    $sel  = report_sql_columns_for($selectedKeys);
    return array_values(array_unique(array_merge($base, $sel)));
}


// ----------------------------------------------------------------------------
//  RAUMDATEN
// ----------------------------------------------------------------------------
/**
 * Lädt eine Raum-Zeile mit genau den benötigten Spalten.
 * Spaltennamen werden gebacktickt; Bindung erfolgt nur für die ID.
 *
 * @return array|null  assoziative Zeile oder null
 */
function report_fetch_room($mysqli, int $roomID, array $columns): ?array
{
    $colSql = implode(', ', array_map(fn($c) => "tabelle_räume.`$c`", $columns));
    $stmt   = $mysqli->prepare("SELECT $colSql FROM tabelle_räume WHERE tabelle_räume.idTABELLE_Räume = ?");
    $stmt->bind_param("i", $roomID);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    return $row ?: null;
}


// ----------------------------------------------------------------------------
//  ÄNDERUNGSERKENNUNG (Raumparameter)
// ----------------------------------------------------------------------------
/**
 * Ermittelt, welche Raumparameter sich seit $sinceDate effektiv geändert haben.
 * Vergleicht je Mapping-Paar den ersten "alten" mit dem letzten "neuen" Wert.
 *
 * @return string[]  Liste der geänderten (neuen) Parameter-Keys
 */
function report_detect_room_changes($mysqli, int $roomID, ?string $sinceDate, array $mapping, array $mp2): array
{
    if ($sinceDate === null || $sinceDate === '') {
        return [];
    }

    $stmt = $mysqli->prepare("SELECT * FROM `tabelle_raeume_aenderungen` WHERE `raum_id`= ? AND `Timestamp` > ?");
    $stmt->bind_param("is", $roomID, $sinceDate);
    $stmt->execute();
    $result = $stmt->get_result();

    $rows = [];
    while ($r = $result->fetch_assoc()) {
        $rows[] = $r;
    }

    $changed = [];
    foreach ($mapping as $oldK => $newK) {
        $entries = [];
        foreach ($rows as $entry) {
            if ($entry[$oldK] !== $entry[$newK]) {
                $entries[] = [
                    'timestamp' => $entry['Timestamp'],
                    'oldValue'  => $entry[$oldK],
                    $mp2[$newK] => $entry[$newK],
                ];
            }
        }
        if (!empty($entries)) {
            usort($entries, fn($a, $b) => $a['timestamp'] <=> $b['timestamp']);
            if (end($entries)[$mp2[$newK]] !== reset($entries)['oldValue']) {
                $changed[] = $mp2[$newK];
            }
        }
    }
    return $changed;
}


// ----------------------------------------------------------------------------
//  MED.-TECH.-ELEMENTE
// ----------------------------------------------------------------------------
/**
 * Lädt die medizintechnischen Elemente eines Raumes (gruppiert, mit Stückzahl).
 * Liefert das mysqli-Result UND die Zeilenanzahl, damit der Renderer nicht
 * selbst zählen muss. Der Result-Zeiger steht am Anfang.
 *
 * @return array{result:mysqli_result, count:int}
 */
function report_fetch_mt_elements($mysqli, int $roomID): array
{
    $sql = "SELECT tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_varianten.Variante,
                   Sum(tabelle_räume_has_tabelle_elemente.Anzahl) AS SummevonAnzahl,
                   tabelle_räume_has_tabelle_elemente.`Neu/Bestand`,
                   tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente,
                   tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten,
                   tabelle_räume_has_tabelle_elemente.Standort,
                   tabelle_räume_has_tabelle_elemente.Verwendung
            FROM tabelle_varianten INNER JOIN (tabelle_räume_has_tabelle_elemente INNER JOIN tabelle_elemente ON
                 tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente = tabelle_elemente.idTABELLE_Elemente) ON
                 tabelle_varianten.idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten
            GROUP BY tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_varianten.Variante,
                     tabelle_räume_has_tabelle_elemente.`Neu/Bestand`,
                     tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente,
                     tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten,
                     tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume
            HAVING (((tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) = ?) AND SummevonAnzahl > 0)
            ORDER BY tabelle_elemente.ElementID, tabelle_varianten.Variante;";

    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $roomID);
    $stmt->execute();
    $result = $stmt->get_result();

    $count = $result->num_rows;
    $result->data_seek(0);

    return ['result' => $result, 'count' => $count];
}

/**
 * Lädt die für die Detail-Tabelle benötigten Parameter-Definitionen
 * (Abkürzungen/Kategorien), die Parameter-Werte je Element/Variante und die
 * Änderungen seit $sinceDate – alles für das aktuelle Projekt.
 *
 * @return array{defs:mysqli_result, values:mysqli_result, changes:array}
 */
function report_fetch_mt_param_data($mysqli, int $projectID, ?string $sinceDate): array
{
    // Parameter-Definitionen (Abkürzung + Kategorie)
    $defsSql = "SELECT tabelle_parameter_kategorie.Kategorie, tabelle_parameter.Abkuerzung,
                       tabelle_parameter.Bezeichnung, tabelle_parameter.idTABELLE_Parameter,
                       tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie
                FROM tabelle_parameter_kategorie INNER JOIN
                    (tabelle_parameter INNER JOIN tabelle_projekt_elementparameter
                     ON tabelle_parameter.idTABELLE_Parameter = tabelle_projekt_elementparameter.tabelle_parameter_idTABELLE_Parameter)
                    ON tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie = tabelle_parameter.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie
                WHERE ((tabelle_projekt_elementparameter.tabelle_projekte_idTABELLE_Projekte = ?)
                       AND (tabelle_parameter.`Bauangaben relevant` = 1)
                       AND NOT (tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie = 18))
                GROUP BY tabelle_parameter_kategorie.Kategorie, tabelle_parameter.Bezeichnung
                ORDER BY tabelle_parameter_kategorie.Kategorie, tabelle_parameter.Bezeichnung;";

    // Parameter-Werte je Element/Variante
    $valuesSql = "SELECT tabelle_projekt_elementparameter.Wert, tabelle_projekt_elementparameter.Einheit,
                         tabelle_projekt_elementparameter.tabelle_Varianten_idtabelle_Varianten,
                         tabelle_projekt_elementparameter.tabelle_elemente_idTABELLE_Elemente,
                         tabelle_parameter.Bezeichnung, tabelle_parameter_kategorie.Kategorie,
                         tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie, tabelle_parameter.idTABELLE_Parameter
                  FROM tabelle_parameter_kategorie INNER JOIN (tabelle_parameter
                       INNER JOIN tabelle_projekt_elementparameter
                       ON tabelle_parameter.idTABELLE_Parameter = tabelle_projekt_elementparameter.tabelle_parameter_idTABELLE_Parameter)
                      ON tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie = tabelle_parameter.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie
                  WHERE ((tabelle_projekt_elementparameter.tabelle_projekte_idTABELLE_Projekte = ?)
                         AND (tabelle_parameter.`Bauangaben relevant` = 1)
                         AND NOT (tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie = 18))
                  ORDER BY tabelle_parameter_kategorie.Kategorie, tabelle_parameter.Bezeichnung;";

    $defs   = report_prepared_query($mysqli, $defsSql, "i", [$projectID]);
    $values = report_prepared_query($mysqli, $valuesSql, "i", [$projectID]);

    return [
        'defs'    => $defs,
        'values'  => $values,
        'changes' => report_fetch_mt_changes($mysqli, $projectID, $sinceDate),
    ];
}

/**
 * Lädt + filtert die Element-Parameter-Änderungen (alt != neu) seit $sinceDate.
 *
 * @return array  bereinigte Änderungsdatensätze
 */
function report_fetch_mt_changes($mysqli, int $projectID, ?string $sinceDate): array
{
    if ($sinceDate === null || $sinceDate === '') {
        return [];
    }

    $sql = "SELECT projekt, element, parameter, variante, wert_alt, wert_neu,
                   einheit_alt, einheit_neu, timestamp, user
            FROM tabelle_projekt_elementparameter_aenderungen
            WHERE projekt = ? AND timestamp > ?
            ORDER BY timestamp DESC;";

    $res  = report_prepared_query($mysqli, $sql, "is", [$projectID, $sinceDate]);
    $rows = [];
    while ($r = $res->fetch_assoc()) {
        $rows[] = $r;
    }
    return filter_old_equal_new($rows);
}


// ----------------------------------------------------------------------------
//  RAUMLEISTUNG DER ELEMENTE INKL. GLEICHZEITIGKEIT (GLZ)
// ----------------------------------------------------------------------------
/**
 * Summiert die elektrische Leistung der Raum-Elemente je Netzart (AV/SV/ZSV/USV),
 * inkl. Gleichzeitigkeitsfaktor (GLZ) und Akku-Mapping. Leistung wird auf W
 * normiert (kW/MW werden umgerechnet).
 *
 *  Verwendete Element-Parameter (tabelle_projekt_elementparameter):
 *    - ID 18  = Leistung (mit Einheit)
 *    - ID 133 = Gleichzeitigkeit (Faktor; '', '1', NULL -> 1.0; '0' -> 0.0)
 *    - ID 82  = Netzart (AV/SV/ZSV/USV, inkl. /Akku-Varianten -> Basisnetz)
 *
 * @return array  ['AV'=>float,'SV'=>float,'ZSV'=>float,'USV'=>float,'Unbekannt'=>float]
 */
function report_fetch_room_power_glz($mysqli, int $projectID, int $roomID): array
{
    $sql = "SELECT
        mapped_netzart AS netzart,
        ROUND(SUM(gesamt_leistung_w), 0) AS gesamtleistung_w,
        COUNT(*) AS elemente_anzahl
    FROM (
        SELECT
            re.TABELLE_Räume_idTABELLE_Räume AS raum_id,
            SUM(re.Anzahl) AS anzahl,

            CASE
                WHEN pep_netz.Wert IN ('AV', 'Akku', '/Akku','AV/Akku') THEN 'AV'
                WHEN pep_netz.Wert IN ('SV', 'SV/Akku') THEN 'SV'
                WHEN pep_netz.Wert IN ('ZSV','ZSV/Akku') THEN 'ZSV'
                WHEN pep_netz.Wert IN ('USV', 'USV/Akku') THEN 'USV'
                ELSE 'Unbekannt'
            END AS mapped_netzart,

            SUM(re.Anzahl) *
            CASE
                WHEN pep_leistung.Einheit = 'kW' THEN COALESCE(CAST(NULLIF(pep_leistung.Wert, '') AS DECIMAL(10,2)), 0) * 1000
                WHEN pep_leistung.Einheit = 'MW' THEN COALESCE(CAST(NULLIF(pep_leistung.Wert, '') AS DECIMAL(10,2)), 0) * 1000000
                ELSE COALESCE(CAST(NULLIF(pep_leistung.Wert, '') AS DECIMAL(10,2)), 0)
            END *
            COALESCE(
                CASE
                    WHEN pep_gleich.Wert IS NULL OR pep_gleich.Wert = '' OR pep_gleich.Wert = '1' THEN 1.0
                    WHEN pep_gleich.Wert = '0' THEN 0.0
                    ELSE CAST(REPLACE(pep_gleich.Wert, ',', '.') AS DECIMAL(5,2))
                END,
                1.0
            ) AS gesamt_leistung_w,

            CASE
                WHEN pep_leistung.Einheit = 'kW' THEN COALESCE(CAST(NULLIF(pep_leistung.Wert, '') AS DECIMAL(10,2)), 0) * 1000
                WHEN pep_leistung.Einheit = 'MW' THEN COALESCE(CAST(NULLIF(pep_leistung.Wert, '') AS DECIMAL(10,2)), 0) * 1000000
                ELSE COALESCE(CAST(NULLIF(pep_leistung.Wert, '') AS DECIMAL(10,2)), 0)
            END AS leistung_w

        FROM tabelle_räume_has_tabelle_elemente re
            INNER JOIN tabelle_elemente e ON re.TABELLE_Elemente_idTABELLE_Elemente = e.idTABELLE_Elemente
            INNER JOIN tabelle_varianten v ON re.tabelle_Varianten_idtabelle_Varianten = v.idtabelle_Varianten

            LEFT JOIN tabelle_projekt_elementparameter pep_leistung ON
                pep_leistung.tabelle_Varianten_idtabelle_Varianten = v.idtabelle_Varianten
                AND pep_leistung.tabelle_elemente_idTABELLE_Elemente = e.idTABELLE_Elemente
                AND pep_leistung.tabelle_projekte_idTABELLE_Projekte = ?
                AND pep_leistung.tabelle_parameter_idTABELLE_Parameter = 18

            LEFT JOIN tabelle_projekt_elementparameter pep_gleich ON
                pep_gleich.tabelle_Varianten_idtabelle_Varianten = v.idtabelle_Varianten
                AND pep_gleich.tabelle_elemente_idTABELLE_Elemente = e.idTABELLE_Elemente
                AND pep_gleich.tabelle_projekte_idTABELLE_Projekte = ?
                AND pep_gleich.tabelle_parameter_idTABELLE_Parameter = 133

            LEFT JOIN tabelle_projekt_elementparameter pep_netz ON
                pep_netz.tabelle_Varianten_idtabelle_Varianten = v.idtabelle_Varianten
                AND pep_netz.tabelle_elemente_idTABELLE_Elemente = e.idTABELLE_Elemente
                AND pep_netz.tabelle_projekte_idTABELLE_Projekte = ?
                AND pep_netz.tabelle_parameter_idTABELLE_Parameter = 82

        WHERE re.TABELLE_Räume_idTABELLE_Räume = ?
            AND re.Verwendung = 1
            AND re.Anzahl > 0
            AND COALESCE(CAST(NULLIF(pep_leistung.Wert, '') AS DECIMAL(10,2)), 0) > 0

        GROUP BY
            re.TABELLE_Räume_idTABELLE_Räume,
            e.idTABELLE_Elemente,
            v.idtabelle_Varianten,
            pep_leistung.Wert,
            pep_leistung.Einheit,
            pep_gleich.Wert,
            pep_netz.Wert
        HAVING leistung_w > 0
    ) AS element_leistungen
    GROUP BY mapped_netzart
    ORDER BY
        CASE netzart
            WHEN 'AV' THEN 1
            WHEN 'SV' THEN 2
            WHEN 'ZSV' THEN 3
            WHEN 'USV' THEN 4
            ELSE 5
        END";

    $res = report_prepared_query($mysqli, $sql, "iiii", [$projectID, $projectID, $projectID, $roomID]);

    $leistungen = ['AV' => 0, 'SV' => 0, 'ZSV' => 0, 'USV' => 0, 'Unbekannt' => 0];
    while ($r = $res->fetch_assoc()) {
        $net = $r['netzart'];
        $sum = (float)$r['gesamtleistung_w'];
        if (array_key_exists($net, $leistungen)) {
            $leistungen[$net] += $sum;
        } else {
            $leistungen['Unbekannt'] += $sum;
        }
    }
    return $leistungen;
}


// ----------------------------------------------------------------------------
//  KLEINER PREPARED-STATEMENT-HELFER
// ----------------------------------------------------------------------------
/**
 * Führt ein prepared SELECT aus und liefert das mysqli_result.
 * Vereinheitlicht die Parameter-Bindung (vermeidet SQL-Injection und
 * Konkatenation von $_SESSION-Werten in Strings).
 */
function report_prepared_query($mysqli, string $sql, string $types, array $params)
{
    $stmt = $mysqli->prepare($sql);
    if ($types !== '' && !empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    return $stmt->get_result();
}