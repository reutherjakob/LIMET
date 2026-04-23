<?php
/**
 * api_compare_elements.php
 * ══════════════════════════════════════════════════════════════════
 * Vergleicht Excel-Einträge eines Raums mit den DB-Elementen.
 *
 * POST-Body (JSON):
 *   raum_id   int   – idTABELLE_Räume
 *   familien  array – [{familie, laenge, variante, params}]
 *
 * Response (JSON):
 *   vergleich, unmapped_familien, parameter_mapping
 * ══════════════════════════════════════════════════════════════════
 */

ob_start();
if (!function_exists('utils_connect_sql')) {
    include "../utils/_utils.php";
}
check_login();
ob_clean();
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/import_2_db_config.php';

// ──────────────────────────────────────────────────────────────────
// Helpers
// ──────────────────────────────────────────────────────────────────

function json_error(string $msg, int $code = 400): never
{
    http_response_code($code);
    echo json_encode(['error' => $msg], JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Parst einen Dimensions-String zu cm (int).
 * Unterstützt: "1,20 m", "1.20", "120 cm", "120", "0,90"
 */
function parse_dim_cm(string $raw): int
{
    $raw = trim(mb_strtolower($raw));

    if (str_contains($raw, 'cm')) {
        return (int)round((float)str_replace([',', ' cm', 'cm'], ['.', '', ''], $raw));
    }

    if (str_contains($raw, 'm')) {
        $v = (float)str_replace([',', ' m', 'm'], ['.', '', ''], $raw);
        return (int)round($v * 100);
    }

    $v = (float)str_replace(',', '.', $raw);
    return $v <= 10 ? (int)round($v * 100) : (int)round($v);
}

/**
 * Nächstes Standardmaß + Abweichung.
 */
function nearest_std(int $cm): array
{
    $best = MZ_STANDARD_LAENGEN[0];
    $diff = PHP_INT_MAX;
    foreach (MZ_STANDARD_LAENGEN as $s) {
        $d = abs($cm - $s);
        if ($d < $diff) {
            $diff = $d;
            $best = $s;
        }
    }
    return ['nearest' => $best, 'diff' => $diff];
}

/**
 * Normalisiert Excel-Rohwert: Ja/Nein → 1/0, Rest unverändert.
 */
function norm(string $raw): string
{
    if (in_array($raw, ['Ja', 'ja', 'Yes', '1', 'true'], true)) return '1';
    if (in_array($raw, ['Nein', 'nein', 'No', '0', 'false'], true)) return '0';
    return $raw;
}

/**
 * Liest Parameter-IDs aus einer Liste von Spaltenname → aus params_raw.
 * Gibt [param_id => ['wert', 'einheit', 'bezeichnung']] zurück.
 * Leere Werte werden übersprungen.
 */
function extract_params(array $col_names, array $params_raw): array
{
    $out = [];
    foreach ($col_names as $col) {
        $cfg = PARAMETER_MAPPING[$col] ?? null;
        if (!$cfg) continue;
        $raw = $params_raw[$col] ?? '';
        if ($raw === '') continue;
        $out[$cfg['id']] = [
            'wert' => norm((string)$raw),
            'einheit' => $cfg['einheit'],
            'bezeichnung' => $cfg['bezeichnung'],
        ];
    }
    return $out;
}

/**
 * Liefert ElementID, variante_params, info_params und Debug-Info.
 *
 * @return array{
 *   element_id: string|null,
 *   variante_params: string[],
 *   info_params: string[],
 *   debug: string,
 *   laenge_cm: int|null,
 *   is_sondermass: bool
 * }
 */
function resolve(string $familie, string $laenge, array $params_raw): array
{
    $r = [
        'element_id' => null,
        'variante_params' => [],
        'info_params' => [],
        'debug' => '',
        'laenge_cm' => null,
        'is_sondermass' => false,
    ];

    // ── MZ_FAMILIE_MAPPING: Substring-Match ───────────────────────
    foreach (MZ_FAMILIE_MAPPING as $key => $mz) {
        if (!str_contains($familie, $key)) continue;

        $r['variante_params'] = $mz['variante_params'] ?? [];
        $r['info_params'] = $mz['info_params'] ?? [];

        if ($mz['typ'] === 'tisch') {
            $raw_b = $params_raw['MT_LIMET_Breite'] ?? '';
            $raw_t = $params_raw['MT_LIMET_Tiefe'] ?? '';
            if ($raw_b !== '' && $raw_t !== '') {
                $b = parse_dim_cm($raw_b);
                $t = parse_dim_cm($raw_t);
                $r['laenge_cm'] = $b;
                $r['debug'] = "Tisch {$b}×{$t}cm";
                $k = "{$b}x{$t}";
                if (isset($mz['breite_tiefe'][$k])) {
                    $r['element_id'] = $mz['breite_tiefe'][$k];
                } else {
                    $r['element_id'] = $mz['sondermass'];
                    $r['is_sondermass'] = true;
                    $r['debug'] .= ' → Sondermaß';
                }
            } else {
                $r['element_id'] = $mz['sondermass'];
                $r['is_sondermass'] = true;
                $r['debug'] = 'Breite/Tiefe fehlen → Sondermaß';
            }
            return $r;
        }

        // Längen-Matching
        if ($laenge !== '') {
            $cm = parse_dim_cm($laenge);
            $nearest = nearest_std($cm);
            $r['laenge_cm'] = $nearest['nearest'];
            $r['debug'] = "{$cm}cm → {$nearest['nearest']}cm";
            if (isset($mz['laengen'][$nearest['nearest']])) {
                $r['element_id'] = $mz['laengen'][$nearest['nearest']];
                if ($nearest['diff'] >= MZ_LAENGE_WARN_DIFF_CM)
                    $r['debug'] .= " ⚠ Abweichung {$nearest['diff']}cm";
            } else {
                $r['element_id'] = $mz['sondermass'];
                $r['is_sondermass'] = true;
                $r['debug'] .= ' → Sondermaß';
            }
        } else {
            $r['element_id'] = $mz['sondermass'];
            $r['is_sondermass'] = true;
            $r['debug'] = 'kein Längenwert → Sondermaß';
        }
        return $r;
    }

    // ── FAMILIE_MAPPING: exakter Name ─────────────────────────────
    if (isset(FAMILIE_MAPPING[$familie])) {
        $fm = FAMILIE_MAPPING[$familie];
        $r['element_id'] = $fm['element_id'];
        $r['variante_params'] = $fm['variante_params'] ?? [];
        $r['info_params'] = $fm['info_params'] ?? [];
        $r['debug'] = 'direktes Mapping';
        return $r;
    }

    return $r; // nicht gemappt
}

/**
 * Prüft ob die variante_params aus Excel mit den DB-Parametern übereinstimmen.
 * Nur die in $excel_params enthaltenen IDs werden geprüft —
 * zusätzliche DB-Parameter werden ignoriert.
 */
function params_match(array $excel_params, array $db_params): bool
{
    foreach ($excel_params as $pid => $info) {
        if ((string)($db_params[$pid] ?? null) !== (string)$info['wert']) return false;
    }
    return true;
}

/**
 * Lesbares Label aus Parameter-Array.
 */
function variante_label(array $params): string
{
    $parts = [];
    foreach ($params as $info) {
        if (($info['wert'] ?? '') === '' || $info['wert'] === '0') continue;
        $parts[] = $info['bezeichnung'] . ': ' . $info['wert']
            . ($info['einheit'] ? ' ' . $info['einheit'] : '');
    }
    return implode(' · ', $parts) ?: '—';
}

// ──────────────────────────────────────────────────────────────────
// Request
// ──────────────────────────────────────────────────────────────────

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_error('Method not allowed', 405);
}
$body = json_decode(file_get_contents('php://input'), true);
if (!isset($body['raum_id'], $body['familien'])) {
    json_error('raum_id oder familien fehlt');
}
if (!isset($_SESSION['projectID'])) {
    json_error('Kein Projekt in Session');
}

$projekt_id = (int)$_SESSION['projectID'];
$planungsphase = (int)($_SESSION['projectPlanungsphase'] ?? 1);
$raum_id = (int)$body['raum_id'];
$familien = array_filter((array)$body['familien'], fn($f) => !empty($f['familie'] ?? $f));
$mysqli = utils_connect_sql();

// Sicherheitscheck
$chk = $mysqli->prepare("SELECT 1 FROM tabelle_räume WHERE idTABELLE_Räume=? AND tabelle_projekte_idTABELLE_Projekte=? LIMIT 1");
$chk->bind_param('ii', $raum_id, $projekt_id);
$chk->execute();
if (!$chk->get_result()->fetch_row()) {
    json_error('Raum gehört nicht zum Projekt', 403);
}
$chk->close();

// ──────────────────────────────────────────────────────────────────
// DB-Elemente + Parameter laden
// ──────────────────────────────────────────────────────────────────
$stmt = $mysqli->prepare("
    SELECT rhe.id, rhe.Anzahl,
           rhe.tabelle_Varianten_idtabelle_Varianten AS variante_id,
           e.idTABELLE_Elemente, e.ElementID, e.Bezeichnung
    FROM tabelle_räume_has_tabelle_elemente rhe
    JOIN tabelle_elemente e ON e.idTABELLE_Elemente = rhe.`TABELLE_Elemente_idTABELLE_Elemente`
    WHERE rhe.`TABELLE_Räume_idTABELLE_Räume` = ? AND rhe.Anzahl > 0
    ORDER BY e.Bezeichnung
");
$stmt->bind_param('i', $raum_id);
$stmt->execute();
$db_elemente = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$existing_params = []; // [elem_id][variante_id][param_id] = wert
if (!empty($db_elemente)) {
    $elem_ids = array_unique(array_column($db_elemente, 'idTABELLE_Elemente'));
    $ph = implode(',', array_fill(0, count($elem_ids), '?'));
    $types = 'i' . str_repeat('i', count($elem_ids));
    $vals = array_merge([$projekt_id], $elem_ids);
    $refs = [&$types];
    foreach ($vals as $k => $_) {
        $refs[] = &$vals[$k];
    }
    $s2 = $mysqli->prepare("
        SELECT tabelle_elemente_idTABELLE_Elemente AS elem_id,
               tabelle_Varianten_idtabelle_Varianten AS variante_id,
               tabelle_parameter_idTABELLE_Parameter AS param_id, Wert
        FROM tabelle_projekt_elementparameter
        WHERE tabelle_projekte_idTABELLE_Projekte=? AND tabelle_elemente_idTABELLE_Elemente IN ($ph)
    ");
    call_user_func_array([$s2, 'bind_param'], $refs);
    $s2->execute();
    foreach ($s2->get_result()->fetch_all(MYSQLI_ASSOC) as $p) {
        $existing_params[$p['elem_id']][$p['variante_id']][$p['param_id']] = $p['Wert'];
    }
    $s2->close();
}

$db_by_eid = [];
foreach ($db_elemente as $el) {
    $db_by_eid[$el['ElementID']][] = $el;
}

// ──────────────────────────────────────────────────────────────────
// Excel-Einträge auflösen
// ──────────────────────────────────────────────────────────────────
$excel_entries = []; // [element_id] => [{variante_key, anzahl, variante_params, all_params, ...}]
$unmapped_familien = [];

foreach ($familien as $eintrag) {
    $familie = trim($eintrag['familie'] ?? '');
    $laenge = trim($eintrag['laenge'] ?? '');
    $params_raw = $eintrag['params'] ?? [];
    $variante_raw = trim($eintrag['variante'] ?? '');
    if (!$familie) continue;

    $res = resolve($familie, $laenge, $params_raw);
    if (!$res['element_id']) {
        $key = $familie . '||' . $laenge;
        $unmapped_familien[$key] ??= ['familie' => $familie, 'laenge' => $laenge, 'anzahl' => 0];
        $unmapped_familien[$key]['anzahl']++;
        continue;
    }

    // Variante-bildende Parameter (für Fingerprint + DB-Schreiben)
    $vparams = extract_params($res['variante_params'], $params_raw);

    // Info-Parameter (für Anzeige, werden NICHT für Matching genutzt)
    $iparams = extract_params($res['info_params'], $params_raw);

    // Fingerprint aus variante_params
    $sorted = $vparams;
    ksort($sorted);
    $variante_key = $variante_raw ?: md5(json_encode($sorted));

    $eid = $res['element_id'];
    $excel_entries[$eid] ??= [];
    $found = false;
    foreach ($excel_entries[$eid] as &$entry) {
        if ($entry['variante_key'] === $variante_key) {
            $entry['anzahl']++;
            $found = true;
            break;
        }
    }
    unset($entry);
    if (!$found) {
        $excel_entries[$eid][] = [
            'variante_key' => $variante_key,
            'anzahl' => 1,
            'variante_params' => $vparams,   // für Matching + DB
            'all_params' => $vparams + $iparams, // für Anzeige
            'familie' => $familie,
            'laenge' => $laenge,
            'laenge_cm' => $res['laenge_cm'],
            'debug' => $res['debug'],
            'is_sondermass' => $res['is_sondermass'],
        ];
    }
}

// ──────────────────────────────────────────────────────────────────
// Gegenüberstellung Excel ↔ DB
// ──────────────────────────────────────────────────────────────────
$vergleich = [];
$all_eids = array_unique(array_merge(array_keys($db_by_eid), array_keys($excel_entries)));

foreach ($all_eids as $eid) {
    $in_db = isset($db_by_eid[$eid]);
    $in_excel = isset($excel_entries[$eid]);

    if ($in_excel) {
        foreach ($excel_entries[$eid] as $ex) {
            $matched_db = null;
            $matched_vid = null;

            if ($in_db) {
                foreach ($db_by_eid[$eid] as $db_row) {
                    $vid = (int)$db_row['variante_id'];
                    $db_eid_i = (int)$db_row['idTABELLE_Elemente'];
                    $db_params = $existing_params[$db_eid_i][$vid] ?? [];
                    // Match nur auf variante_params (nicht info_params)
                    if (params_match($ex['variante_params'], $db_params)) {
                        $matched_db = $db_row;
                        $matched_vid = $vid;
                        break;
                    }
                }
            }

            $base = [
                'element_id' => $eid,
                'bezeichnung' => $matched_db
                    ? $matched_db['Bezeichnung']
                    : ($in_db ? $db_by_eid[$eid][0]['Bezeichnung'] : '(noch nicht in DB)'),
                'excel_anzahl' => $ex['anzahl'],
                'variante_label' => variante_label($ex['all_params']),
                'familie' => $ex['familie'],
                'laenge_raw' => $ex['laenge'],
                'laenge_cm' => $ex['laenge_cm'],
                'debug' => $ex['debug'],
                'is_sondermass' => $ex['is_sondermass'],
                // variante_params wird an den Sync übergeben
                'params' => $ex['variante_params'],
            ];

            if ($matched_db) {
                $vergleich[] = $base + [
                        'status' => (int)$matched_db['Anzahl'] === $ex['anzahl'] ? 'match' : 'diff_anzahl',
                        'db_anzahl' => (int)$matched_db['Anzahl'],
                        'rhe_id' => (int)$matched_db['id'],
                        'db_element_id' => (int)$matched_db['idTABELLE_Elemente'],
                        'variante_id' => $matched_vid,
                        'needs_new_variante' => false,
                    ];
            } else {
                $vergleich[] = $base + [
                        'status' => 'nur_excel',
                        'db_anzahl' => 0,
                        'rhe_id' => null,
                        'db_element_id' => $in_db ? (int)$db_by_eid[$eid][0]['idTABELLE_Elemente'] : null,
                        'variante_id' => null,
                        'needs_new_variante' => true,
                    ];
            }
        }
    }

    // DB-Einträge die in Excel fehlen
    if ($in_db) {
        foreach ($db_by_eid[$eid] as $db_row) {
            $vid = (int)$db_row['variante_id'];
            $db_eid_i = (int)$db_row['idTABELLE_Elemente'];
            $db_params = $existing_params[$db_eid_i][$vid] ?? [];

            // Bereits in Excel gematcht?
            $found = false;
            if ($in_excel) {
                foreach ($excel_entries[$eid] as $ex) {
                    if (params_match($ex['variante_params'], $db_params)) {
                        $found = true;
                        break;
                    }
                }
            }
            if ($found) continue;

            // Label für DB-Params aufbauen
            $db_labeled = [];
            foreach ($db_params as $pid => $wert) {
                foreach (PARAMETER_MAPPING as $cfg) {
                    if ($cfg['id'] === (int)$pid) {
                        $db_labeled[$pid] = ['bezeichnung' => $cfg['bezeichnung'], 'wert' => $wert, 'einheit' => $cfg['einheit']];
                        break;
                    }
                }
            }

            $vergleich[] = [
                'element_id' => $eid,
                'bezeichnung' => $db_row['Bezeichnung'],
                'status' => 'nur_db',
                'db_anzahl' => (int)$db_row['Anzahl'],
                'excel_anzahl' => 0,
                'rhe_id' => (int)$db_row['id'],
                'db_element_id' => $db_eid_i,
                'variante_id' => $vid,
                'variante_label' => variante_label($db_labeled),
                'needs_new_variante' => false,
                'familie' => null,
                'laenge_raw' => null,
                'laenge_cm' => null,
                'debug' => null,
                'is_sondermass' => false,
                'params' => [],
            ];
        }
    }
}

$sort_order = ['diff_anzahl' => 0, 'nur_excel' => 1, 'nur_db' => 2, 'match' => 3];
usort($vergleich, fn($a, $b) => ($sort_order[$a['status']] ?? 9) - ($sort_order[$b['status']] ?? 9));

echo json_encode([
    'raum_id' => $raum_id,
    'vergleich' => $vergleich,
    'unmapped_familien' => array_values($unmapped_familien),
    'parameter_mapping' => array_map(fn($v) => $v['bezeichnung'], PARAMETER_MAPPING),
], JSON_UNESCAPED_UNICODE);