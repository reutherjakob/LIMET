<?php
/**
 * roombook_befu.php
 * -----------------------------------------------------------------------------
 * MT-Raumbuch als Word-Dokument.
 *
 * Zwei Modi in einer Datei:
 *   1) Normalaufruf  -> Bootstrap-Seite mit Vorschau + Download-Button
 *   2) ?export=word  -> streamt das Word-Dokument (.doc) und beendet.
 *
 * Darstellung der Elemente je Raum:
 *   - mode=tabelle  (Default): Elemente in EINER Tabelle (Kat + Element)
 *   - mode=liste             : Elemente aufgezählt, je Gewerk eine Subüberschrift
 *
 * Filter (GET):
 *   - ohne Parameter "f"  -> alle Räume (Default, alles angehakt)
 *   - mit  f=1            -> nur Räume, deren Werte in den übergebenen Listen
 *                            stehen, z.B. f=1&bauabschnitt[]=A&bauetappe[]=1&raumbereich[]=OP
 *   Leere Werte werden als "(leer)" angeboten und ebenfalls filterbar.
 *
 * Je Raum(gruppe): "Nutzung:" (BO-Text, Spalte `Anmerkung FunktionBO`),
 * Handwaschplatz (Spalte Nutzwasser), danach die Elemente. Elemente können
 * laut rb_zusammenfassungen() zu einem Textelement zusammengefasst werden.
 * Je Raumbereich: Lokalisierung der Notfallwagen (RB_NOTFALLWAGEN_IDS).
 * Gelb markiert = bitte prüfen (abweichende/fehlende Angaben).
 *
 * Das <style> im Word-Export ist Dokumentformatierung fuer Word.
 * -----------------------------------------------------------------------------
 */

require_once '_utils/_utils.php';
init_page_serversides();   // Session, Login-Check, Navbar-Setup (wie documentationV2.php)

$projectID = (int)($_SESSION['projectID'] ?? 0);

// =============================================================================
//  KONFIGURATION
// =============================================================================

/**
 * Gewerke kommen aus der Projekt-Zuordnung (tabelle_projekt_element_gewerk ->
 * tabelle_auftraggeber_gewerke), also genau das, was die Nutzer pro Element
 * gesetzt haben (saveElementGewerk.php) – gleiche Quelle wie die Kosten-PDFs.
 * Elemente ohne Zuordnung landen unter RB_OHNE_GEWERK.
 */
const RB_OHNE_GEWERK = '';
const RB_OHNE_GEWERK_LABEL = 'ohne Gewerkzuordnung';

/** Sortierung der Gewerke-Nummern (natürlich, "ohne Gewerk" zuletzt). */
function mt_cmp_gewerk(string $a, string $b): int
{
    if ($a === $b) return 0;
    if ($a === RB_OHNE_GEWERK) return 1;
    if ($b === RB_OHNE_GEWERK) return -1;
    return strnatcasecmp($a, $b);
}

/** Überschrift eines Gewerks in der Auflistung. */
function mt_gewerk_titel(string $nr, string $bez): string
{
    if ($nr === RB_OHNE_GEWERK) {
        return RB_OHNE_GEWERK_LABEL;
    }
    return $bez !== '' ? $nr . ' – ' . $bez : $nr;
}

/** Natürliche Sortierung von ElementIDs ("1.9" vor "1.10"). */
function mt_cmp_elementid(string $a, string $b): int
{
    $pa = array_map('intval', explode('.', $a));
    $pb = array_map('intval', explode('.', $b));
    $n = max(count($pa), count($pb));
    for ($i = 0; $i < $n; $i++) {
        $x = $pa[$i] ?? -1;
        $y = $pb[$i] ?? -1;
        if ($x !== $y) return $x <=> $y;
    }
    return 0;
}


// =============================================================================
//  ZUSAMMENFASSUNG VON ELEMENTEN (nur Darstellung)
// =============================================================================

/**
 * Regeln, nach denen mehrere Elemente eines Raumes zu EINEM textlichen
 * Element zusammengefasst werden. Neue Regel = neuer Eintrag im Array,
 * neues Element = ID in die passende 'ids'-Liste.
 *
 *   'ids'         : idTABELLE_Elemente der Elemente, die zur Regel gehören
 *   'label'       : Ausgabetext; Platzhalter {teile} und {anzahl}
 *   'teil_regex'  : (optional) Regex auf die Bezeichnung, Gruppe 1 = Teil für
 *                   {teile} (z.B. "O2" aus "O2 Wandentnahmestelle").
 *                   Ohne Treffer wird die ganze Bezeichnung verwendet.
 *   'reihenfolge' : (optional) Sortierung der Teile; Unbekannte danach
 *   'min_elemente': erst ab so vielen verschiedenen Elementen zusammenfassen
 *
 * Zusammengefasst wird je Raum und Gewerk. Nicht gelistete Elemente
 * (z.B. Lagerschrank Endoskope, Rollcontainer) bleiben einzeln stehen.
 */
const RB_GASE_REIHENFOLGE = ['O2', 'VA', 'DL-5', 'DL-10', 'NGA', 'CO2', 'N2O'];

function rb_zusammenfassungen(): array
{
    return [
        // 1
        'metallmoebel' => [
            'label' => 'Metallmöbelverbau',
            'ids' => [
                714, 1977, 1992, 1069, 1976, 1972, 1980, 1981, 1274, 2084,   // 4.5.x  ISO pulverbeschichtet
                1377, 1557, 1558, 1991, 1990,                               // 4.10.x ISO CrNi
                716, 1978, 1952, 1953, 2037, 1961, 1070, 1967,              // 4.15.x DIN pulverbeschichtet
                857, 1271, 1272, 809, 905, 810, 811, 1966, 817, 1230,       // 4.20.x DIN CrNi
                368, 543, 581, 1555, 116,                                   // 4.40 / 4.65 / 4.70
            ],
        ],
        // 2
        'labormoebel' => [
            'label' => 'Labormöbelverbau',
            'ids' => [
                1971, 1963,                                                 // Arbeitsplatten PP
                504, 505, 507, 894, 498, 499, 500, 501, 502,                // Labortische
                1610, 1611, 1612, 1614, 1624, 1432,                         // Lagerregale / Hochschränke
                508, 1096, 1097, 1439, 901, 1568, 1569, 1570, 1571, 1572,   // Medienzellen / -kanäle
                1594, 1595, 902, 1711, 903, 970,
                1713, 1215, 897, 900, 974, 1712,                            // Ablagen / Hängeregale
                1583, 1584, 1590, 1575,                                     // Medienzellen inkl. Tisch
            ],
        ],
        // 3
        'holzmoebel' => [
            'label' => 'Holzmöbelverbau',
            'ids' => [1994, 1995, 1996],
        ],
        // 4
        'geraetetrageschiene' => [
            'label' => 'Gerätetrageschiene',
            'ids' => [74, 523, 524, 525, 527, 529],
        ],
        // 5
        'mve_wand' => [
            'label' => 'MVE - Wandmontiert',
            'ids' => [310, 512, 2115],
        ],
        // 6
        'mve_decke' => [
            'label' => 'MVE - Deckenmontiert',
            'ids' => [139, 1907],
        ],
        // 7
        'wandentnahmestelle' => [
            'label' => 'Wandentnahmestelle ({teile})',
            'ids' => [67, 68, 69, 162, 163, 342],
            'teil_regex' => '/^(\S+)\s+Wandentnahmestelle/iu',
            'reihenfolge' => RB_GASE_REIHENFOLGE,
        ],
        // 8
        'gase_mve' => [
            'label' => 'MVE ({teile})',
            'ids' => [75, 76, 77],
            'teil_regex' => '/^(\S+)\s+in\s+MVE/iu',
            'reihenfolge' => RB_GASE_REIHENFOLGE,
        ],
        // 9
        'gase_dve' => [
            'label' => 'DVE ({teile})',
            'ids' => [64, 289, 65, 66, 202, 288],
            'teil_regex' => '/^(\S+)\s+in\s+DVE/iu',
            'reihenfolge' => RB_GASE_REIHENFOLGE,
        ],
    ];
}

/** Liefert den Aufzählungsteil, wenn das Element zur Regel gehört, sonst null. */
function rb_regel_teil(array $regel, array $e): ?string
{
    if (!in_array((int)($e['ElementDbID'] ?? 0), $regel['ids'] ?? [], true)) {
        return null;
    }
    $bez = trim((string)$e['Bezeichnung']);
    $m = [];
    if (!empty($regel['teil_regex']) && preg_match($regel['teil_regex'], $bez, $m) === 1
        && isset($m[1]) && trim($m[1]) !== '') {
        return trim($m[1]);
    }
    return $bez;
}

/**
 * Fasst die Elemente eines Raumes laut rb_zusammenfassungen() zusammen.
 * Das Sammel-Element steht an der Stelle des ersten zugehörigen Elements
 * (die Sortierung nach Gewerk/ElementID bleibt so erhalten).
 */
function rb_zusammenfassen(array $els): array
{
    $regeln = rb_zusammenfassungen();
    $slots = [];     // Liste von Listen (Platzhalter für Sammel-Elemente)
    $sammel = [];

    foreach ($els as $e) {
        $regelKey = null;
        $teil = null;
        foreach ($regeln as $key => $regel) {
            $teil = rb_regel_teil($regel, $e);
            if ($teil !== null) {
                $regelKey = $key;
                break;
            }
        }
        if ($regelKey === null) {
            $slots[] = [$e];
            continue;
        }
        $k = $regelKey . '|' . $e['GewerkNr'];
        if (!isset($sammel[$k])) {
            $sammel[$k] = ['pos' => count($slots), 'regel' => $regelKey, 'teile' => [], 'elemente' => []];
            $slots[] = [];
        }
        $sammel[$k]['teile'][$teil] = true;
        $sammel[$k]['elemente'][] = $e;
    }

    foreach ($sammel as $s) {
        $regel = $regeln[$s['regel']];
        if (count($s['elemente']) < (int)($regel['min_elemente'] ?? 1)) {
            $slots[$s['pos']] = $s['elemente'];
            continue;
        }
        $teile = array_keys($s['teile']);
        $ordnung = array_flip($regel['reihenfolge'] ?? []);
        usort($teile, function ($a, $b) use ($ordnung) {
            $ia = $ordnung[$a] ?? PHP_INT_MAX;
            $ib = $ordnung[$b] ?? PHP_INT_MAX;
            return $ia <=> $ib ?: strnatcasecmp($a, $b);
        });
        $erstes = $s['elemente'][0];
        $slots[$s['pos']] = [[
            'ElementDbID' => $erstes['ElementDbID'],
            'ElementID' => $erstes['ElementID'],
            'Bezeichnung' => strtr($regel['label'], [
                '{teile}' => implode(', ', $teile),
                '{anzahl}' => (string)array_sum(array_column($s['elemente'], 'Anzahl')),
            ]),
            'Anzahl' => array_sum(array_column($s['elemente'], 'Anzahl')),
            'GewerkNr' => $erstes['GewerkNr'],
            'GewerkBez' => $erstes['GewerkBez'],
            'Zusammengefasst' => true,
        ]];
    }

    return $slots ? array_merge(...$slots) : [];
}


// =============================================================================
//  NOTFALLWAGEN / NUTZUNG / HANDWASCHPLATZ
// =============================================================================

/**
 * Elemente, die als Notfallwagen zählen (idTABELLE_Elemente).
 *   88 Notfallwagen, 519 Notfallrucksack, 563 Notfallwagen MR
 * Zubehör zählt nur, wenn im Raum kein eigentlicher Notfallwagen steht
 * (sonst würde Wagen + Bestückung doppelt gezählt).
 *   89 Bestückung Notfallwagen
 */
const RB_NOTFALLWAGEN_IDS = [88, 519, 563];
const RB_NOTFALLWAGEN_ZUBEHOER_IDS = [89];
const RB_HWP_ELEMENT_IDS = [812, 1964, 1263];
/** Texte Handwaschplatz (Raumparameter Nutzwasser). '' = keine Ausgabe. */
const RB_TXT_HWP_JA = 'Medizinischer Handwaschplatz ist im Raum vorhanden.';
const RB_TXT_HWP_NEIN = '';


// =============================================================================
//  FILTER
// =============================================================================

/** Platzhalter fuer leere Werte (NULL / ''). */
const RB_LEER = '__leer__';

/**
 * Filterbare Raumparameter.
 *   GET-Name => [ 'spalte' => Schlüssel im Raum-Array, 'label' => Anzeige ]
 * Hinweis: "Bauteil" wird im Projekt (PDF-Berichte) aus der Spalte Bauabschnitt befüllt.
 */
function rb_filter_felder(): array
{
    return [
        'bauabschnitt' => ['spalte' => 'Bauabschnitt', 'label' => 'Bauteil / Bauabschnitt'],
        'bauetappe' => ['spalte' => 'Bauetappe', 'label' => 'Bauetappe'],
        'raumbereich' => ['spalte' => 'Raumbereich', 'label' => 'Raumbereich'],
    ];
}

/** Normierter Filterwert eines Raumes (leer -> RB_LEER). */
function rb_filter_wert(array $room, string $spalte): string
{
    $v = trim((string)($room[$spalte] ?? ''));
    return $v === '' ? RB_LEER : $v;
}

/** Anzeige eines Filterwertes. */
function rb_filter_anzeige(string $wert): string
{
    return $wert === RB_LEER ? '(leer)' : $wert;
}

/**
 * Liest die Filterauswahl aus $_GET.
 * null  = kein Filter aktiv (alles an)
 * array = je Feld die Liste der ANGEHAKTEN Werte
 */
function rb_filter_lesen(): ?array
{
    if (!isset($_GET['f'])) {
        return null;
    }
    $sel = [];
    foreach (rb_filter_felder() as $key => $cfg) {
        $vals = $_GET[$key] ?? [];
        if (!is_array($vals)) {
            $vals = [$vals];
        }
        $sel[$key] = array_values(array_unique(array_map('strval', $vals)));
    }
    return $sel;
}

/** Ist ein Wert angehakt? */
function rb_filter_ist_an(?array $sel, string $key, string $wert): bool
{
    return $sel === null || in_array($wert, $sel[$key] ?? [], true);
}

/** Besteht ein Raum den Filter? */
function rb_filter_passt(array $room, ?array $sel): bool
{
    if ($sel === null) {
        return true;
    }
    foreach (rb_filter_felder() as $key => $cfg) {
        if (!rb_filter_ist_an($sel, $key, rb_filter_wert($room, $cfg['spalte']))) {
            return false;
        }
    }
    return true;
}

/**
 * Auswahlmöglichkeiten je Feld inkl. Anzahl Räume (aus ALLEN MT-Räumen).
 * Rückgabe: [ key => [ wert => anzahl, ... ], ... ]  natürlich sortiert, (leer) am Ende.
 */
function rb_filter_optionen(array $alleRaeume): array
{
    $opt = [];
    foreach (rb_filter_felder() as $key => $cfg) {
        $opt[$key] = [];
        foreach ($alleRaeume as $room) {
            $w = rb_filter_wert($room, $cfg['spalte']);
            $opt[$key][$w] = ($opt[$key][$w] ?? 0) + 1;
        }
        uksort($opt[$key], function ($a, $b) {
            if ($a === RB_LEER) return 1;
            if ($b === RB_LEER) return -1;
            return strnatcasecmp($a, $b);
        });
    }
    return $opt;
}

/** Anzahl abgewählter Werte (fuer Badge am Filter-Button). */
function rb_filter_anzahl_ab(?array $sel, array $optionen): int
{
    if ($sel === null) {
        return 0;
    }
    $n = 0;
    foreach ($optionen as $key => $werte) {
        foreach (array_keys($werte) as $w) {
            if (!rb_filter_ist_an($sel, $key, (string)$w)) {
                $n++;
            }
        }
    }
    return $n;
}

/** Textuelle Beschreibung der aktiven Einschränkungen ('' = keine). */
function rb_filter_beschreibung(?array $sel, array $optionen): string
{
    if ($sel === null) {
        return '';
    }
    $teile = [];
    foreach (rb_filter_felder() as $key => $cfg) {
        $an = [];
        $alleAn = true;
        foreach (array_keys($optionen[$key] ?? []) as $w) {
            if (rb_filter_ist_an($sel, $key, (string)$w)) {
                $an[] = rb_filter_anzeige((string)$w);
            } else {
                $alleAn = false;
            }
        }
        if (!$alleAn) {
            $teile[] = $cfg['label'] . ': ' . ($an ? implode(', ', $an) : '–');
        }
    }
    return implode(' | ', $teile);
}

/** Query-String der Filterauswahl (fuer Links, z.B. Word-Export). */
function rb_filter_query(?array $sel): string
{
    return $sel === null ? '' : http_build_query(['f' => 1] + $sel);
}


// =============================================================================
//  DATEN LADEN
// =============================================================================

/**
 * Liest alle MT-relevanten Räume + ihre aggregierten Elemente aus der DB.
 * Rückgabe: [ 'uebersicht' => [...], 'bereiche' => [...], 'optionen' => [...], 'gesamt' => int ]
 */
function raumbuch_daten_laden(mysqli $mysqli, int $projectID, ?array $filter = null): array
{
    // ---- 1) Räume -----------------------------------------------------------
    $sqlRooms = "SELECT idTABELLE_Räume AS RaumID,
                        Raumnr,
                        Raumbezeichnung,
                        `Raumbereich Nutzer` AS Raumbereich,
                        Geschoss,
                        Bauabschnitt,
                        Bauetappe,
                        Nutzfläche,
                        Anwendungsgruppe,
                        H6020,
                        `Anmerkung FunktionBO` AS BO,
                        Nutzwasser
                 FROM tabelle_räume
                 WHERE tabelle_projekte_idTABELLE_Projekte = $projectID
                   AND `MT-relevant` = 1
                   AND Entfallen = 0
                 ORDER BY `Raumbereich Nutzer`, Anwendungsgruppe, Raumnr";

    $resRooms = $mysqli->query($sqlRooms);
    $alleRaeume = [];
    $uebersicht = [];
    $rooms = [];
    if ($resRooms) {
        while ($r = $resRooms->fetch_assoc()) {
            $r['RaumID'] = (int)$r['RaumID'];
            $alleRaeume[] = $r;
            if (!rb_filter_passt($r, $filter)) {
                continue;
            }
            $rooms[$r['RaumID']] = $r;
            $uebersicht[] = $r;
        }
    }

    $optionen = rb_filter_optionen($alleRaeume);

    // ---- 2) Elemente je Raum (Anzahl über Varianten summiert) ---------------
    $elementsByRoom = [];
    if (!empty($rooms)) {
        $DUMMY_ELEMENT_IDS = [2032, 2033, 2034, 2035, 2075];
        $dummyListe = implode(',', array_map('intval', $DUMMY_ELEMENT_IDS));
        $idListe = implode(',', array_map('intval', array_keys($rooms)));
        // Gewerk je Element über die Projekt-Zuordnung (LEFT JOIN: Elemente ohne
        // Zuordnung bleiben drin). Die Zuordnung ist je Projekt+Element eindeutig
        // (saveElementGewerk.php macht UPDATE statt zweitem INSERT); sie wird
        // trotzdem vorab je Element verdichtet, damit Anzahl nie doppelt zählt.
        $sqlEls = "SELECT rhe.TABELLE_Räume_idTABELLE_Räume AS RaumID,
                          e.idTABELLE_Elemente AS ElementDbID,
                          e.ElementID,
                          e.Bezeichnung,
                          g.Gewerke_Nr,
                          g.Bezeichnung AS GewerkBezeichnung,
                          SUM(rhe.Anzahl) AS Anzahl
                   FROM tabelle_räume_has_tabelle_elemente rhe
                   INNER JOIN tabelle_elemente e
                           ON e.idTABELLE_Elemente = rhe.TABELLE_Elemente_idTABELLE_Elemente
                   LEFT JOIN (
                        SELECT tabelle_elemente_idTABELLE_Elemente AS ElementPK,
                               MIN(tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke) AS GewerkPK
                        FROM tabelle_projekt_element_gewerk
                        WHERE tabelle_projekte_idTABELLE_Projekte = $projectID
                        GROUP BY tabelle_elemente_idTABELLE_Elemente
                   ) peg ON peg.ElementPK = e.idTABELLE_Elemente
                   LEFT JOIN tabelle_auftraggeber_gewerke g
                           ON g.idTABELLE_Auftraggeber_Gewerke = peg.GewerkPK
                   WHERE rhe.TABELLE_Räume_idTABELLE_Räume IN ($idListe)
                            AND e.idTABELLE_Elemente NOT IN ($dummyListe)
                   GROUP BY rhe.TABELLE_Räume_idTABELLE_Räume, e.idTABELLE_Elemente, e.ElementID, e.Bezeichnung,
                            g.Gewerke_Nr, g.Bezeichnung
                   HAVING SUM(rhe.Anzahl) <> 0";

        $resEls = $mysqli->query($sqlEls);
        if ($resEls) {
            while ($e = $resEls->fetch_assoc()) {
                $rid = (int)$e['RaumID'];
                $nr = trim((string)($e['Gewerke_Nr'] ?? ''));
                $bez = trim((string)($e['GewerkBezeichnung'] ?? ''));
                $elementsByRoom[$rid][] = [
                    'ElementDbID' => (int)$e['ElementDbID'],
                    'ElementID' => $e['ElementID'],
                    'Bezeichnung' => $e['Bezeichnung'],
                    'Anzahl' => (int)$e['Anzahl'],
                    'GewerkNr' => $nr,          // '' = ohne Zuordnung
                    'GewerkBez' => $bez,
                ];
            }
        }
        // je Raum: nach Gewerk, innerhalb des Gewerks nach ElementID
        foreach ($elementsByRoom as &$list) {
            usort($list, fn($a, $b) => mt_cmp_gewerk($a['GewerkNr'], $b['GewerkNr'])
                ?: mt_cmp_elementid($a['ElementID'], $b['ElementID']));
        }
        unset($list);
    }

    // In der Auswahl tatsächlich vorkommende Gewerke (fuer die Legende)
    $gewerke = [];
    foreach ($elementsByRoom as $list) {
        foreach ($list as $e) {
            $gewerke[$e['GewerkNr']] = $e['GewerkBez'];
        }
    }
    uksort($gewerke, fn($a, $b) => mt_cmp_gewerk((string)$a, (string)$b));

    // ---- 3) Detail: nach Raumbereich gruppieren, gleich ausgestattete -------
    //         Räume zusammenfassen
    $bereiche = [];
    foreach ($rooms as $rid => $room) {
        $bereich = trim((string)$room['Raumbereich']);
        if ($bereich === '') {
            $bereich = '(ohne Raumbereich)';
        }
        if (!isset($bereiche[$bereich])) {
            $bereiche[$bereich] = ['raeume' => [], 'gruppen' => [], 'notfallwagen' => []];
        }
        $bereiche[$bereich]['raeume'][] = $room;   // Raumliste je Bereich (Word-Übersicht)

        $els = $elementsByRoom[$rid] ?? [];

        // Notfallwagen in diesem Raum (vor dem Zusammenfassen gezählt)
        $nfw = 0;
        $nfwZubehoer = 0;
        foreach ($els as $e) {
            if ($e['Anzahl'] <= 0) {
                continue;
            }
            if (in_array($e['ElementDbID'], RB_NOTFALLWAGEN_IDS, true)) {
                $nfw += $e['Anzahl'];
            } elseif (in_array($e['ElementDbID'], RB_NOTFALLWAGEN_ZUBEHOER_IDS, true)) {
                $nfwZubehoer += $e['Anzahl'];
            }
        }
        if ($nfw === 0) {
            $nfw = $nfwZubehoer;
        }
        if ($nfw > 0) {
            $bereiche[$bereich]['notfallwagen'][] = [
                'Raumnr' => $room['Raumnr'],
                'Raumbezeichnung' => $room['Raumbezeichnung'],
                'Anzahl' => $nfw,
            ];
        }

        // Signatur = Menge der Elemente inkl. Anzahl. Leere Räume: eindeutig.
        if ($els === []) {
            $sig = '__leer__' . $rid;
        } else {
            $parts = [];
            foreach ($els as $e) {
                $parts[] = $e['ElementID'] . '#' . $e['Anzahl'];
            }
            $sig = implode('|', $parts);
        }

        if (!isset($bereiche[$bereich]['gruppen'][$sig])) {
            $bereiche[$bereich]['gruppen'][$sig] = ['rooms' => [],
                'elemente' => rb_zusammenfassen($els)];
        }

        // Handwaschplatz: Raumparameter Nutzwasser = 1 ODER HWP-Element im Raum
        $hwp = (int)($room['Nutzwasser'] ?? 0) === 1;
        if (!$hwp) {
            foreach ($els as $e) {
                if ($e['Anzahl'] > 0 && in_array($e['ElementDbID'], RB_HWP_ELEMENT_IDS, true)) {
                    $hwp = true;
                    break;
                }
            }
        }
        $bereiche[$bereich]['gruppen'][$sig]['rooms'][] = [
            'Raumnr' => $room['Raumnr'],
            'Raumbezeichnung' => $room['Raumbezeichnung'],
            'BO' => (string)($room['BO'] ?? ''),
            'Nutzwasser' => (int)($room['Nutzwasser'] ?? 0),
            'HWP' => $hwp

        ];
    }

    // ---- 4) je Gruppe: Überschrift bauen ------------------------------------
    foreach ($bereiche as $bereich => &$binfo) {
        foreach ($binfo['gruppen'] as $sig => &$gruppe) {
            $namen = array_map(fn($r) => mb_strtolower(trim($r['Raumbezeichnung'])), $gruppe['rooms']);
            $gruppe['sameName'] = (count(array_unique($namen)) === 1);

            if (count($gruppe['rooms']) === 1) {
                $r0 = $gruppe['rooms'][0];
                $gruppe['heading'] = trim($r0['Raumnr'] . ' - ' . $r0['Raumbezeichnung']);
            } elseif ($gruppe['sameName']) {
                $nummern = implode(', ', array_map(fn($r) => $r['Raumnr'], $gruppe['rooms']));
                $gruppe['heading'] = $nummern . ' - ' . $gruppe['rooms'][0]['Raumbezeichnung'];
            } else {
                $einzeln = array_map(fn($r) => trim($r['Raumnr'] . ' - ' . $r['Raumbezeichnung']), $gruppe['rooms']);
                $gruppe['heading'] = implode('  /  ', $einzeln);
            }
        }
        unset($gruppe);
        $binfo['gruppen'] = array_values($binfo['gruppen']);
    }
    unset($binfo);

    return [
        'uebersicht' => $uebersicht,
        'bereiche' => $bereiche,
        'optionen' => $optionen,
        'gewerke' => $gewerke,
        'gesamt' => count($alleRaeume),
    ];
}


// =============================================================================
//  RENDERING-HELFER
// =============================================================================

function h(?string $s): string
{
    return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Zellinhalt fuer Word in einen Null-Abstand-Absatz packen (Web: unverändert). */
function rb_cell(string $inner, bool $forWord): string
{
    return $forWord ? '<p class="cp">' . $inner . '</p>' : $inner;
}

/** Gelbe Markierung: Word per Highlight, Web per Bootstrap-<mark>. */
function rb_mark(string $inner, bool $forWord): string
{
    return $forWord
        ? '<span style="background:yellow;mso-highlight:yellow">' . $inner . '</span>'
        : '<mark>' . $inner . '</mark>';
}

/** Freitext (mehrzeilig) escapen. */
function rb_text_html(string $s): string
{
    return nl2br(h(trim($s)), false);
}

/** Vergleichsschlüssel für Texte (Leerraum normiert). */
function rb_text_norm(string $s): string
{
    return preg_replace('/\s+/u', ' ', trim($s));
}

/** Aufzählung "a, b und c". */
function rb_aufzaehlung(array $teile): string
{
    if (count($teile) <= 1) {
        return (string)($teile[0] ?? '');
    }
    $letzter = array_pop($teile);
    return implode(', ', $teile) . ' und ' . $letzter;
}

/**
 * Satz zur Lokalisierung der Notfallwagen eines Raumbereichs.
 * $liste: [ ['Raumnr','Raumbezeichnung','Anzahl'], ... ]
 */
function render_notfallwagen(string $bereich, array $liste, bool $forWord): string
{
    $pCls = $forWord ? '' : ' class="small mb-2"';
    if (!$liste) {
        return "<p$pCls>" . rb_mark('[Im Bereich ' . h($bereich) . ' ist kein Notfallwagen hinterlegt.]', $forWord) . '</p>';
    }
    $gesamt = (int)array_sum(array_column($liste, 'Anzahl'));
    $orte = array_map(function ($n) {
        return h(trim($n['Raumbezeichnung'])) . ' (' . h($n['Raumnr'])
            . ($n['Anzahl'] > 1 ? ', ' . (int)$n['Anzahl'] . ' Stk.' : '') . ')';
    }, $liste);
    $wo = (count($liste) === 1 ? 'im Raum ' : 'in den Räumen ') . rb_aufzaehlung($orte);

    $satz = $gesamt === 1
        ? 'Der Notfallwagen im Bereich ' . h($bereich) . ' befindet sich ' . $wo . '.'
        : 'Im Bereich ' . h($bereich) . ' befinden sich ' . $gesamt . ' Notfallwagen, ' . $wo . '.';
    return "<p$pCls>" . $satz . '</p>';
}

/**
 * "Nutzung: ..." je Raum/Gruppe aus der BO-Beschreibung.
 * Bei abweichenden Texten in einer Gruppe: alle Varianten mit Raumnummer, gelb markiert.
 */
function render_nutzung(array $rooms, bool $forWord): string
{
    $pCls = $forWord ? '' : ' class="mb-1"';
    $varianten = [];
    foreach ($rooms as $r) {
        $t = trim((string)($r['BO'] ?? ''));
        $k = rb_text_norm($t);
        if (!isset($varianten[$k])) {
            $varianten[$k] = ['text' => $t, 'raeume' => []];
        }
        $varianten[$k]['raeume'][] = $r['Raumnr'];
    }

    if (count($varianten) === 1) {
        $v = reset($varianten);
        $inner = $v['text'] !== ''
            ? rb_text_html($v['text'])
            : rb_mark('[keine BO-Beschreibung hinterlegt]', $forWord);
        return "<p$pCls><b>Nutzung:</b> " . $inner . '</p>';
    }

    $html = "<p$pCls><b>Nutzung:</b> "
        . rb_mark('[abweichende BO-Beschreibungen – bitte abgleichen]', $forWord) . '</p>';
    foreach ($varianten as $v) {
        $quelle = '<b>' . h(implode(', ', $v['raeume'])) . ':</b> ';
        $text = $v['text'] !== '' ? rb_text_html($v['text']) : '<i>(keine BO-Beschreibung)</i>';
        $html .= "<p$pCls>" . rb_mark($quelle . $text, $forWord) . '</p>';
    }
    return $html;
}

/** Medizinischer Handwaschplatz (Raumparameter Nutzwasser = 1). */
function render_handwaschplatz(array $rooms, bool $forWord): string
{
    $pCls = $forWord ? '' : ' class="mb-1"';
    $mit = [];
    $ohne = [];
    foreach ($rooms as $r) {
        if ((int)($r['HWP'] ?? 0) === 1) {
            $mit[] = $r['Raumnr'];
        } else {
            $ohne[] = $r['Raumnr'];
        }
    }
    if (!$ohne) {
        return RB_TXT_HWP_JA !== '' ? "<p$pCls>" . h(RB_TXT_HWP_JA) . '</p>' : '';
    }
    if (!$mit) {
        return RB_TXT_HWP_NEIN !== '' ? "<p$pCls>" . h(RB_TXT_HWP_NEIN) . '</p>' : '';
    }
    return "<p$pCls>" . rb_mark(
            'Medizinischer Handwaschplatz vorhanden in: ' . h(implode(', ', $mit))
            . '; nicht vorhanden in: ' . h(implode(', ', $ohne)) . '.',
            $forWord
        ) . '</p>';
}

function fmt_flaeche($v): string
{
    if ($v === null || $v === '') return '';
    return number_format((float)$v, 2, ',', '.') . ' m²';
}

/** Gewerke-Legende (Nr = Bezeichnung) der in der Auswahl vorkommenden Gewerke. */
function render_legende(array $gewerke, bool $forWord): string
{
    if (!$gewerke) {
        return '';
    }
    $paare = [];
    foreach ($gewerke as $nr => $bez) {
        $nr = (string)$nr;
        $paare[] = $nr === RB_OHNE_GEWERK
            ? '<b>–</b> = ' . h(RB_OHNE_GEWERK_LABEL)
            : '<b>' . h($nr) . '</b> = ' . h($bez);
    }
    $inner = implode(' &nbsp;·&nbsp; ', $paare);
    return $forWord
        ? '<p class="legende"><b>Legende Gewerke:</b> &nbsp;' . $inner . '</p>'
        : '<p class="text-muted small mb-3"><b>Legende Gewerke:</b> ' . $inner . '</p>';
}


/**
 * Raumtabelle. Word: je Raumbereich unter dessen Überschrift, daher ohne
 * Spalte "Raumbereich". Web: Gesamttabelle mit dieser Spalte.
 */
function render_uebersicht(array $uebersicht, bool $forWord): string
{
    if ($forWord) {
        $html = '<table class="rb-table">';
        $html .= '<colgroup>'
            . '<col style="width:12%">
               <col style="width:40%">'
            . '<col style="width:12%">
               <col style="width:12%">
               <col style="width:12%">
               <col style="width:12%">
               </colgroup>';
    } else {
        $html = '<table class="table table-sm table-bordered table-striped align-middle rb-tight">';
    }

    $html .= '<thead><tr>
            <th>Raumnr</th>
            <th>Raumbezeichnung</th>'
        . ($forWord ? '' : '<th>Raumbereich</th>') . '
            <th>Geschoss</th>
            <th>Nutzfläche</th>
            <th>RG</th>
            <th>H6020</th>
                </tr></thead><tbody>';

    foreach ($uebersicht as $r) {
        $html .= '<tr>'
            . '<td>' . rb_cell(h($r['Raumnr']), $forWord) . '</td>'
            . '<td>' . rb_cell(h($r['Raumbezeichnung']), $forWord) . '</td>'
            . ($forWord ? '' : '<td>' . h($r['Raumbereich']) . '</td>')
            . '<td>' . rb_cell(h($r['Geschoss']), $forWord) . '</td>'
            . '<td>' . rb_cell(h(fmt_flaeche($r['Nutzfläche'])), $forWord) . '</td>'
            . '<td>' . rb_cell(h($r['Anwendungsgruppe']), $forWord) . '</td>'
            . '<td>' . rb_cell(h($r['H6020']), $forWord) . '</td>'
            . '</tr>';
    }
    $html .= '</tbody></table>';
    return $html;
}

/** Elemente je Raum als Tabelle (Gewerk-Nr + Element). */
function render_raum_tabelle(array $elemente, bool $forWord): string
{
    if ($forWord) {
        $html = '<table class="rb-table">';
        $html .= '<colgroup>'
            . '<col style="width:10%"><col style="width:90%">'
            . '</colgroup>';
    } else {
        $html = '<table class="table table-sm table-bordered align-middle rb-tight">';
    }

    $html .= '<thead><tr> 
            <th>Gewerk</th>
            <!--th>ElementID</th-->
            <th>Element</th>
         <!--th class="text-center">Anzahl</th-->
            </tr></thead><tbody>';

    foreach ($elemente as $e) {
        $html .= '<tr>'
            . '<td>' . rb_cell(h($e['GewerkNr'] !== RB_OHNE_GEWERK ? $e['GewerkNr'] : '–'), $forWord) . '</td>'
            . '<td>' . rb_cell(h($e['Bezeichnung']), $forWord) . '</td>'
            . '</tr>';
    }
    $html .= '</tbody></table>';
    return $html;
}

/**
 * Elemente je Raum als Auflistung: je Gewerk eine Subüberschrift, darunter
 * die Elemente aufgezählt.
 */
function render_raum_liste(array $elemente, bool $forWord): string
{
    // nach Gewerk gruppieren (Elemente sind bereits nach Gewerk sortiert)
    $byGewerk = [];
    foreach ($elemente as $e) {
        $byGewerk[$e['GewerkNr']][] = $e;
    }

    $html = '';
    foreach ($byGewerk as $nr => $liste) {
        $titel = mt_gewerk_titel((string)$nr, $liste[0]['GewerkBez']);

        if ($forWord) {
            $html .= '<p class="gewerk">' . h($titel) . '</p>';
            $html .= '<ul class="rb-liste">';
        } else {
            $html .= '<h6 class="fw-bold text-secondary mt-2 mb-1">' . h($titel) . '</h6>';
            $html .= '<ul class="mb-2">';
        }

        foreach ($liste as $e) {
            $html .= '<li>' . h($e['Bezeichnung']) . '</li>';
        }
        $html .= '</ul>';
    }
    return $html;
}

/**
 * Detailteil. Überschriften:
 *   Word: h1 (Raumbereich) / h2 (Raum)  -> Navigationsbereich
 *   Web : h3 / h5
 *
 * Word: direkt unter jeder Raumbereichs-Überschrift stehen Einleitungssatz,
 * Tabellenbeschreibung und die Raumtabelle dieses Bereichs. Web: dort steht die Gesamttabelle in der linken Spalte.
 *
 * $mode: 'tabelle' (Default) oder 'liste'
 */
function render_detail(array $bereiche, array $gewerke, bool $forWord, string $mode = 'tabelle'): string
{
    $hBereich = $forWord ? 'h1' : 'h3';
    $hRaum = $forWord ? 'h2' : 'h5';

    $html = render_legende($gewerke, $forWord);

    foreach ($bereiche as $bereich => $binfo) {
        $cls = $forWord ? '' : ' class="mt-4 pt-2 border-top"';
        $html .= "<$hBereich$cls>" . h($bereich) . "</$hBereich>";

        if ($forWord) {
            $html .= '<p>Im vorliegenden Kapitel wird der Funktionsbereich ' . h($bereich) . ' beschrieben.</p>';
            $html .= render_notfallwagen($bereich, $binfo['notfallwagen'] ?? [], true);
            $html .= '<p>Details der medizintechnisch relevanten Räume im Bereich ' . h($bereich)
                . '. Klassifizierung nach OVE E 8101 (Raumgruppe RG) und ÖNORM H6020.</p>';
            $html .= render_uebersicht($binfo['raeume'], true);
        } else {
            $html .= render_notfallwagen($bereich, $binfo['notfallwagen'] ?? [], false);
        }

        foreach ($binfo['gruppen'] as $gruppe) {
            $cls = $forWord ? '' : ' class="mt-3"';
            $html .= "<$hRaum$cls>" . h($gruppe['heading']) . "</$hRaum>";

            $html .= render_nutzung($gruppe['rooms'], $forWord);
            $html .= render_handwaschplatz($gruppe['rooms'], $forWord);

            if (empty($gruppe['elemente'])) {
                $html .= $forWord
                    ? '<p><i>Keine Elemente hinterlegt.</i></p>'
                    : '<p class="text-muted fst-italic">Keine Elemente hinterlegt.</p>';
                continue;
            }

            $html .= ($mode === 'liste')
                ? render_raum_liste($gruppe['elemente'], $forWord)
                : render_raum_tabelle($gruppe['elemente'], $forWord);
        }
    }
    return $html;
}

/** Filter-Panel (Bootstrap, nur Web). */
function render_filter_panel(?array $sel, array $optionen, string $mode, bool $offen): string
{
    $html = '<div class="collapse' . ($offen ? ' show' : '') . ' mb-3" id="filterPanel">'
        . '<form method="get" class="border rounded bg-light p-3">'
        . '<input type="hidden" name="f" value="1">'
        . '<input type="hidden" name="mode" id="modeInput" value="' . h($mode) . '">'
        . '<div class="row g-3">';

    foreach (rb_filter_felder() as $key => $cfg) {
        $html .= '<div class="col-md-4">'
            . '<div class="border rounded bg-white p-2 h-100">'
            . '<div class="d-flex justify-content-between align-items-center border-bottom pb-1 mb-2">'
            . '<b class="small">' . h($cfg['label']) . '</b>'
            . '<div class="btn-group btn-group-sm">'
            . '<button type="button" class="btn btn-outline-secondary py-0" data-rb-set="' . h($key) . '" data-rb-val="1">Alle</button>'
            . '<button type="button" class="btn btn-outline-secondary py-0" data-rb-set="' . h($key) . '" data-rb-val="0">Keine</button>'
            . '</div></div>'
            . '<div class="d-flex flex-wrap column-gap-3">';

        $i = 0;
        foreach (($optionen[$key] ?? []) as $wert => $anzahl) {
            $wert = (string)$wert;
            $id = 'f_' . $key . '_' . $i++;
            $checked = rb_filter_ist_an($sel, $key, $wert) ? ' checked' : '';
            $labelCls = $wert === RB_LEER ? ' fst-italic text-muted' : '';
            $html .= '<div class="form-check">'
                . '<input class="form-check-input" type="checkbox" name="' . h($key) . '[]"'
                . ' value="' . h($wert) . '" id="' . h($id) . '" data-rb-key="' . h($key) . '"' . $checked . '>'
                . '<label class="form-check-label small' . $labelCls . '" for="' . h($id) . '">'
                . h(rb_filter_anzeige($wert))
                . ' <span class="text-muted">(' . (int)$anzahl . ')</span></label>'
                . '</div>';
        }
        if (empty($optionen[$key])) {
            $html .= '<span class="small text-muted fst-italic">keine Werte</span>';
        }
        $html .= '</div></div></div>';
    }

    $html .= '</div>'
        . '<div class="d-flex gap-2 mt-3">'
        . '<button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-filter"></i> Filter anwenden</button>'
        . '<a href="?mode=' . h($mode) . '" class="btn btn-sm btn-outline-secondary" id="filterResetBtn">'
        . '<i class="fas fa-undo"></i> Zurücksetzen</a>'
        . '</div>'
        . '</form></div>';

    return $html;
}


// =============================================================================
//  FILTER-AUSWAHL + MODUS (fuer beide Aufrufarten)
// =============================================================================

$filterSel = rb_filter_lesen();
$mode = (($_GET['mode'] ?? '') === 'liste') ? 'liste' : 'tabelle';


// =============================================================================
//  MODUS 2:  WORD-EXPORT
// =============================================================================

if (($_GET['export'] ?? '') === 'word') {

    if ($projectID <= 0) {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        header('Content-Type: text/plain; charset=utf-8');
        echo 'Kein Projekt ausgewählt.';
        exit;
    }

    $mysqli = utils_connect_sql();
    if (method_exists($mysqli, 'set_charset')) {
        @$mysqli->set_charset('utf8mb4');
    }
    $daten = raumbuch_daten_laden($mysqli, $projectID, $filterSel);
    $mysqli->close();

    $projektName = trim($_SESSION['projectName'] ?? '');
    $planungphase = trim($_SESSION['projectPlanungsphase'] ?? '');
    $datum = $_SESSION['PDFdatum'] ?? date('d.m.Y');
    $filterText = rb_filter_beschreibung($filterSel, $daten['optionen']);

    $safeName = preg_replace('/[^\p{L}\p{N}_\- ]/u', '', $projektName);
    $fileName = trim($safeName . '_GPMT_MT-Raumbuch_' . date('Y-m-d')) . '.doc';

    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    header('Content-Type: application/msword; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $fileName . '"');
    header('Cache-Control: max-age=0');

    $detailHtml = render_detail($daten['bereiche'], $daten['gewerke'], true, $mode);

    echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" '
        . 'xmlns:w="urn:schemas-microsoft-com:office:word" '
        . 'xmlns="http://www.w3.org/TR/REC-html40">';
    echo '<head><meta charset="utf-8">';
    echo '<!--[if gte mso 9]><xml><w:WordDocument><w:View>Print</w:View>'
        . '<w:Zoom>100</w:Zoom></w:WordDocument></xml><![endif]-->';
    // Dokumentformatierung fuer Word: schmale Ränder, feste Tabellenbreite,
    // enger Zeilenabstand (line-height 1), minimales Zellpadding.
    echo '<style>
        @page { size: 21.0cm 29.7cm; margin: 1.4cm 1.4cm; mso-page-orientation: portrait; }
        body  { font-family:"Calibri",sans-serif; font-size:10pt; line-height:1.0; color:#000; }
        p     { margin:0 0 4pt; line-height:1.0; mso-line-height-rule:exactly; }
        h1 { font-size:15pt; color:#1f3864; border-bottom:1pt solid #1f3864; margin:14pt 0 5pt; line-height:1.1; }
        h2 { font-size:12pt; color:#2e4a7d; margin:9pt 0 3pt; line-height:1.1; }
        table.rb-table { border-collapse:collapse; width:100%; table-layout:fixed;
                         margin:2pt 0 8pt; mso-table-lspace:0pt; mso-table-rspace:0pt; }
        table.rb-table th, table.rb-table td {
            border:0.5pt solid #999; padding:0.4pt 3pt; font-size:9pt;
            line-height:1.0; mso-line-height-rule:exactly; vertical-align:top;
                   mso-para-margin-top:0; mso-para-margin-bottom:0;   
            word-wrap:break-word; overflow-wrap:break-word; }
        table.rb-table th { background:#e7edf5; text-align:left; }
        .title   { font-size:18pt; font-weight:bold; color:#1f3864; }
        .subtle  { color:#666; font-size:9.5pt; }
        .legende { font-size:9pt; color:#333; }
        .text-center { text-align:center; }

        p.cp { margin:0; mso-para-margin-top:0; mso-para-margin-bottom:0;
               line-height:1.0; mso-line-height-rule:exactly; }
        p.MsoNormal, table.rb-table td p, table.rb-table th p {
               margin:0; mso-para-margin-top:0; mso-para-margin-bottom:0;
               line-height:1.0; mso-line-height-rule:exactly; }

        /* Auflistungs-Darstellung */
        p.gewerk { font-weight:bold; color:#2e4a7d; font-size:10pt;
                   margin:6pt 0 2pt; line-height:1.1; }
        ul.rb-liste { margin:0 0 6pt; padding-left:16pt; }
        ul.rb-liste li { font-size:9.5pt; margin:0;
                         line-height:1.0; mso-line-height-rule:exactly; }
    </style></head><body>';

    echo '<div class="title">Medizintechnisches Raumbuch</div>';
    echo '<p class="subtle">Projekt: ' . h($projektName)
        . ($planungphase !== '' ? ' &nbsp;–&nbsp; PPH: ' . h($planungphase) : '')
        . '<br>Stand: ' . h($datum)
        . ($filterText !== '' ? '<br>Auswahl: ' . h($filterText) : '')
        . '</p>';

    echo '<p class="subtle">RG = Raumgruppe Ö-Norm 8101</p>';

    echo $detailHtml;

    echo '</body></html>';
    exit;
}


// =============================================================================
//  MODUS 1:  BOOTSTRAP-SEITE (Vorschau + Download)
// =============================================================================

$mysqli = utils_connect_sql();
if (method_exists($mysqli, 'set_charset')) {
    @$mysqli->set_charset('utf8mb4');
}
$daten = ($projectID > 0)
    ? raumbuch_daten_laden($mysqli, $projectID, $filterSel)
    : ['uebersicht' => [], 'bereiche' => [], 'optionen' => [], 'gewerke' => [], 'gesamt' => 0];
$mysqli->close();

$anzahlRaeume = count($daten['uebersicht']);
$anzahlGesamt = (int)$daten['gesamt'];
$anzahlAb = rb_filter_anzahl_ab($filterSel, $daten['optionen']);
$filterText = rb_filter_beschreibung($filterSel, $daten['optionen']);
$filterQuery = rb_filter_query($filterSel);
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <title>BEFU Rohling</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="Logos/iphone_favicon.png">
    <link rel="stylesheet" href="_utils/style.css" type="text/css" media="screen"/>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css"
          integrity="sha512-q3eWabyZPc1XTCmF+8/LuE1ozpg5xxn7iO89yfSOd5/oKvyqLngoNGsx8jq92Y8eXJ/IRxQbEC+FGSYxtk2oiw=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
<div class="container-fluid bg-light pb-5">
    <div id="limet-navbar"></div>
    <div class="row justify-content-center">
        <div class="col-xxl-12">
            <div class="card mt-2">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <b>BEFU </b>
                        - Übersicht der
                        <?php if ($anzahlRaeume !== $anzahlGesamt): ?>
                            <?php echo $anzahlRaeume; ?> von <?php echo $anzahlGesamt; ?>
                        <?php else: ?>
                            <?php echo $anzahlRaeume; ?>
                        <?php endif; ?>
                        MT-relevanten Räume.
                        <p class="text-muted small mb-0"> (RG = Raum/-Anwendungsgruppe)</p>
                        <?php if ($filterText !== ''): ?>
                            <p class="small text-primary mb-0"><i
                                        class="fas fa-filter"></i> <?php echo h($filterText); ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <button class="btn btn-sm <?php echo $anzahlAb > 0 ? 'btn-warning' : 'btn-outline-secondary'; ?>"
                                type="button" data-bs-toggle="collapse" data-bs-target="#filterPanel"
                                aria-expanded="false" aria-controls="filterPanel">
                            <i class="fas fa-filter"></i> Filter
                            <?php if ($anzahlAb > 0): ?>
                                <span class="badge bg-dark ms-1"><?php echo $anzahlAb; ?> ab</span>
                            <?php endif; ?>
                        </button>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" role="switch" id="modeSwitch"
                                <?php echo $mode === 'liste' ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="modeSwitch" id="modeSwitchLabel">Tabelle</label>
                        </div>
                        <a href="?export=word&amp;mode=tabelle" id="downloadBtn"
                           class="btn btn-sm btn-primary<?php echo $anzahlRaeume === 0 ? ' disabled' : ''; ?>">
                            <i class="fas fa-file-word"></i> Word-Dokument herunterladen
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <?php
                    if ($anzahlGesamt > 0) {
                        echo render_filter_panel($filterSel, $daten['optionen'], $mode, $anzahlRaeume === 0);
                    }
                    ?>

                    <?php if ($anzahlGesamt === 0): ?>
                        <div class="alert alert-info mb-0">
                            Für dieses Projekt wurden keine MT-relevanten Räume gefunden.
                        </div>
                    <?php elseif ($anzahlRaeume === 0): ?>
                        <div class="alert alert-warning mb-0">
                            Keine Räume entsprechen der aktuellen Filterauswahl.
                        </div>
                    <?php else: ?>
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <?php echo render_uebersicht($daten['uebersicht'], false); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div id="detail-tabelle">
                                            <?php echo render_detail($daten['bereiche'], $daten['gewerke'], false, 'tabelle'); ?>
                                        </div>
                                        <div id="detail-liste" class="d-none">
                                            <?php echo render_detail($daten['bereiche'], $daten['gewerke'], false, 'liste'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var filterQuery = <?php echo json_encode($filterQuery); ?>;

        // --- Alle / Keine je Filtergruppe ----------------------------------
        document.querySelectorAll('[data-rb-set]').forEach(function (b) {
            b.addEventListener('click', function () {
                var key = b.getAttribute('data-rb-set');
                var val = b.getAttribute('data-rb-val') === '1';
                document.querySelectorAll('input[data-rb-key="' + key + '"]').forEach(function (cb) {
                    cb.checked = val;
                });
            });
        });

        // --- Darstellungs-Umschalter ---------------------------------------
        var sw = document.getElementById('modeSwitch');
        if (!sw) return;
        var lbl = document.getElementById('modeSwitchLabel');
        var btn = document.getElementById('downloadBtn');
        var tab = document.getElementById('detail-tabelle');
        var lst = document.getElementById('detail-liste');
        var modeInput = document.getElementById('modeInput');
        var resetBtn = document.getElementById('filterResetBtn');

        function apply() {
            var liste = sw.checked;
            var mode = liste ? 'liste' : 'tabelle';
            if (lbl) lbl.textContent = liste ? 'Auflistung' : 'Tabelle';
            if (btn) btn.href = '?' + (filterQuery ? filterQuery + '&' : '') + 'export=word&mode=' + mode;
            if (tab) tab.classList.toggle('d-none', liste);
            if (lst) lst.classList.toggle('d-none', !liste);
            if (modeInput) modeInput.value = mode;           // Modus bleibt beim Filtern erhalten
            if (resetBtn) resetBtn.href = '?mode=' + mode;
        }

        sw.addEventListener('change', apply);
        apply();
    });
</script>
</body>
</html>