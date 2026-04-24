<?php
/**
 * api_compare_elements.php
 * ══════════════════════════════════════════════════════════════════
 * Compares the Excel entries of a room against the DB.
 *
 * Approach:
 *   1. Resolve each Excel row → element_id + variant fingerprint
 *   2. Load ALL variants that exist in this project for those elements
 *   3. Match Excel entries against existing variants
 *   4. Return a clear comparison: each DB row vs each Excel group
 *
 * POST-Body (JSON):
 *   raum_id   int   – idTABELLE_Räume
 *   familien  array – [{familie, laenge, variante, params}]
 *
 * Response (JSON):
 *   element_blocks   array  – one block per element_id, containing:
 *     element_id       string
 *     bezeichnung      string
 *     db_internal_id   int
 *     variante_params  string[]  – param-names that form the fingerprint
 *     db_variants      array     – all variants known in project
 *     excel_entries    array     – resolved Excel rows
 *     comparison       array     – matched/unmatched result rows
 *   unmapped_familien  array  – families that could not be resolved
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
 * Parses a dimension string to cm (int).
 * Handles: "1,20 m" | "1.20" | "120 cm" | "120" | "0,90"
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

/** Nearest standard length and deviation. */
function nearest_std(int $cm): array
{
    $best = MZ_STANDARD_LAENGEN[0];
    $diff = PHP_INT_MAX;
    foreach (MZ_STANDARD_LAENGEN as $s) {
        $d = abs($cm - $s);
        if ($d < $diff) { $diff = $d; $best = $s; }
    }
    return ['nearest' => $best, 'diff' => $diff];
}

/**
 * Normalises a raw Excel value: Ja/Nein → 1/0, rest unchanged.
 */
function norm(string $raw): string
{
    if (in_array($raw, ['Ja', 'ja', 'Yes', '1', 'true'], true)) return '1';
    if (in_array($raw, ['Nein', 'nein', 'No', '0', 'false'], true)) return '0';
    return $raw;
}

/**
 * Extracts a subset of parameters from raw Excel params.
 * Returns [param_id => ['wert', 'einheit', 'bezeichnung']]
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
            'wert'        => norm((string)$raw),
            'einheit'     => $cfg['einheit'],
            'bezeichnung' => $cfg['bezeichnung'],
        ];
    }
    return $out;
}

/**
 * Resolves a Revit family name + dimensions to an element_id.
 *
 * $laenge  – value from the "Länge/Breite" column (KERN_COLS key 'laenge')
 * $tiefe   – value from the "Tiefe" column       (KERN_COLS key 'tiefe')
 *
 * For tisch-type elements, Breite×Tiefe picks the element.
 * $laenge is used as Breite (primary dimension), $tiefe as Tiefe.
 * params_raw['MT_LIMET_Breite'/'Tiefe'] serve as fallback if the
 * kern columns are not mapped.
 *
 * Returns:
 *   element_id       string|null
 *   variante_params  string[]    – column names that form the fingerprint
 *   info_params      string[]    – column names for display only (not fingerprint)
 *   debug            string      – human-readable resolution trace
 *   laenge_cm        int|null
 *   is_sondermass    bool
 */
function resolve(string $familie, string $laenge, string $tiefe, array $params_raw): array
{
    $r = [
        'element_id'      => null,
        'variante_params' => [],
        'info_params'     => [],
        'debug'           => '',
        'laenge_cm'       => null,
        'is_sondermass'   => false,
    ];

    // ── MZ_FAMILIE_MAPPING: substring match, first hit wins ───────
    foreach (MZ_FAMILIE_MAPPING as $key => $mz) {
        if (!str_contains($familie, $key)) continue;

        $r['variante_params'] = $mz['variante_params'] ?? [];
        $r['info_params']     = $mz['info_params'] ?? [];

        if ($mz['typ'] === 'tisch') {
            // Breite: prefer the kern 'laenge' column, fall back to params_raw
            $raw_b = $laenge !== '' ? $laenge : ($params_raw['MT_LIMET_Breite'] ?? '');
            // Tiefe:  prefer the kern 'tiefe' column, fall back to params_raw
            $raw_t = $tiefe  !== '' ? $tiefe  : ($params_raw['MT_LIMET_Tiefe']  ?? '');

            if ($raw_b !== '' && $raw_t !== '') {
                $b = parse_dim_cm($raw_b);
                $t = parse_dim_cm($raw_t);
                $r['laenge_cm'] = $b;
                $key_bt = "{$b}x{$t}";
                $r['debug'] = "Tisch {$b}×{$t}cm";
                if (isset($mz['breite_tiefe'][$key_bt])) {
                    $r['element_id'] = $mz['breite_tiefe'][$key_bt];
                } else {
                    $r['element_id']    = $mz['sondermass'];
                    $r['is_sondermass'] = true;
                    $r['debug']        .= ' → Sondermaß';
                }
            } else {
                $r['element_id']    = $mz['sondermass'];
                $r['is_sondermass'] = true;
                $r['debug']         = 'Breite/Tiefe fehlen → Sondermaß';
            }
            return $r;
        }

        // Längen-Matching
        if ($laenge !== '') {
            $cm      = parse_dim_cm($laenge);
            $nearest = nearest_std($cm);
            $r['laenge_cm'] = $nearest['nearest'];
            $r['debug']     = "{$cm}cm → {$nearest['nearest']}cm";
            if (isset($mz['laengen'][$nearest['nearest']])) {
                $r['element_id'] = $mz['laengen'][$nearest['nearest']];
                if ($nearest['diff'] >= MZ_LAENGE_WARN_DIFF_CM) {
                    $r['debug'] .= " ⚠ Abweichung {$nearest['diff']}cm";
                }
            } else {
                $r['element_id']    = $mz['sondermass'];
                $r['is_sondermass'] = true;
                $r['debug']        .= ' → Sondermaß';
            }
        } else {
            $r['element_id']    = $mz['sondermass'];
            $r['is_sondermass'] = true;
            $r['debug']         = 'kein Längenwert → Sondermaß';
        }
        return $r;
    }

    // ── FAMILIE_MAPPING: exact name ───────────────────────────────
    if (isset(FAMILIE_MAPPING[$familie])) {
        $fm                   = FAMILIE_MAPPING[$familie];
        $r['element_id']      = $fm['element_id'];
        $r['variante_params'] = $fm['variante_params'] ?? [];
        $r['info_params']     = $fm['info_params'] ?? [];
        $r['debug']           = 'direktes Mapping';
        return $r;
    }

    return $r; // unmapped
}

/**
 * Builds a stable fingerprint string from a [param_id => wert] map.
 * Used to compare Excel entries and DB variants.
 */
function fingerprint(array $param_id_to_wert): string
{
    ksort($param_id_to_wert);
    return json_encode($param_id_to_wert);
}

/**
 * Builds a human-readable label from [param_id => ['wert','einheit','bezeichnung']].
 */
function variante_label(array $params): string
{
    $parts = [];
    foreach ($params as $info) {
        if (($info['wert'] ?? '') === '' || $info['wert'] === '0') continue;
        $parts[] = $info['bezeichnung'] . ': ' . $info['wert']
            . ($info['einheit'] ? ' ' . $info['einheit'] : '');
    }
    return implode(' · ', $parts) ?: '(keine Parameter = Var A)';
}

// ──────────────────────────────────────────────────────────────────
// Request validation
// ──────────────────────────────────────────────────────────────────

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_error('Method not allowed', 405);
}
$body = json_decode(file_get_contents('php://input'), true);
if (!isset($body['raum_id']) || !is_numeric($body['raum_id'])) {
    json_error('raum_id fehlt');
}
if (!isset($body['familien']) || !is_array($body['familien'])) {
    json_error('familien fehlt');
}
if (!isset($_SESSION['projectID'])) {
    json_error('Kein Projekt in Session');
}

$raum_id    = (int)$body['raum_id'];
$familien   = $body['familien'];
$projekt_id = (int)$_SESSION['projectID'];

$mysqli = utils_connect_sql();

// ──────────────────────────────────────────────────────────────────
// Step 1: Load current DB state for this room
//   → which elements (with variant + anzahl) are in the room?
// ──────────────────────────────────────────────────────────────────

$stmt = $mysqli->prepare("
    SELECT rhe.id                                          AS rhe_id,
           rhe.TABELLE_Elemente_idTABELLE_Elemente         AS db_elem_internal_id,
           rhe.tabelle_Varianten_idtabelle_Varianten        AS variante_id,
           rhe.Anzahl,
           e.ElementID,
           e.Bezeichnung,
           v.Variante                                      AS variante_letter
    FROM   tabelle_räume_has_tabelle_elemente rhe
    JOIN   tabelle_elemente e   ON e.idTABELLE_Elemente      = rhe.TABELLE_Elemente_idTABELLE_Elemente
    JOIN   tabelle_varianten v  ON v.idtabelle_Varianten      = rhe.tabelle_Varianten_idtabelle_Varianten
    WHERE  rhe.TABELLE_Räume_idTABELLE_Räume = ?
      AND  rhe.Anzahl > 0
    ORDER  BY e.ElementID, v.Variante
");
$stmt->bind_param('i', $raum_id);
$stmt->execute();
$db_room_rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Index: ElementID → list of rhe rows
$db_by_eid = [];
foreach ($db_room_rows as $row) {
    $db_by_eid[$row['ElementID']][] = $row;
}

// ──────────────────────────────────────────────────────────────────
// Step 2: Load ALL variant parameter definitions for the elements
//   present in this room (from the whole project, not just this room)
// ──────────────────────────────────────────────────────────────────

$all_internal_ids = array_unique(array_column($db_room_rows, 'db_elem_internal_id'));
$all_proj_params  = []; // [db_elem_internal_id][variante_id][param_id] = wert

if (!empty($all_internal_ids)) {
    $ph    = implode(',', array_fill(0, count($all_internal_ids), '?'));
    $types = 'i' . str_repeat('i', count($all_internal_ids));
    $vals  = array_merge([$projekt_id], $all_internal_ids);
    $refs  = [&$types];
    foreach ($vals as $k => $_) $refs[] = &$vals[$k];

    $s2 = $mysqli->prepare("
        SELECT pep.tabelle_elemente_idTABELLE_Elemente  AS elem_id,
               pep.tabelle_Varianten_idtabelle_Varianten AS variante_id,
               pep.tabelle_parameter_idTABELLE_Parameter AS param_id,
               pep.Wert
        FROM   tabelle_projekt_elementparameter pep
        WHERE  pep.tabelle_projekte_idTABELLE_Projekte = ?
          AND  pep.tabelle_elemente_idTABELLE_Elemente IN ($ph)
    ");
    call_user_func_array([$s2, 'bind_param'], $refs);
    $s2->execute();
    foreach ($s2->get_result()->fetch_all(MYSQLI_ASSOC) as $p) {
        $all_proj_params[$p['elem_id']][$p['variante_id']][$p['param_id']] = $p['Wert'];
    }
    $s2->close();
}

// ──────────────────────────────────────────────────────────────────
// Step 3: Resolve Excel rows → element groups
//
//  Each Excel row resolves to:
//    - an element_id (string like "4.35.25.5")
//    - a set of variante_params (param_ids → wert) = the fingerprint
//    - a set of info_params (for display only, not fingerprint)
//
//  Rows that resolve to the same element_id + fingerprint are
//  counted as a single "excel entry" (anzahl++).
// ──────────────────────────────────────────────────────────────────

$excel_groups   = []; // [element_id][fingerprint] => {anzahl, variante_params, all_params, meta}
$unmapped_familien = [];

foreach ($familien as $eintrag) {
    $familie    = trim($eintrag['familie'] ?? '');
    $laenge     = trim($eintrag['laenge']  ?? '');
    $tiefe      = trim($eintrag['tiefe']   ?? '');
    $params_raw = $eintrag['params'] ?? [];

    if (!$familie) continue;

    $res = resolve($familie, $laenge, $tiefe, $params_raw);

    if (!$res['element_id']) {
        $key = $familie . '||' . $laenge;
        $unmapped_familien[$key] ??= ['familie' => $familie, 'laenge' => $laenge, 'anzahl' => 0];
        $unmapped_familien[$key]['anzahl']++;
        continue;
    }

    // Parameters that form the variant fingerprint
    $vparams = extract_params($res['variante_params'], $params_raw);
    // Parameters for display only
    $iparams = extract_params($res['info_params'], $params_raw);

    // Fingerprint: only param_id → wert (no labels, deterministic sort)
    $fp_data = array_map(fn($p) => $p['wert'], $vparams);
    $fp      = fingerprint($fp_data);

    $eid = $res['element_id'];
    $excel_groups[$eid] ??= [];

    if (isset($excel_groups[$eid][$fp])) {
        $excel_groups[$eid][$fp]['anzahl']++;
    } else {
        $excel_groups[$eid][$fp] = [
            'fingerprint'    => $fp,
            'anzahl'         => 1,
            'variante_params'=> $vparams,    // [param_id => {wert, einheit, bezeichnung}]
            'all_params'     => $vparams + $iparams, // for display
            'familie'        => $familie,
            'laenge_raw'     => $laenge,
            'laenge_cm'      => $res['laenge_cm'],
            'debug'          => $res['debug'],
            'is_sondermass'  => $res['is_sondermass'],
            'variante_param_names' => $res['variante_params'], // original column names
        ];
    }
}

// ──────────────────────────────────────────────────────────────────
// Step 4: For each resolved element_id, look up the internal DB id
//   so we can load its project variants.
// ──────────────────────────────────────────────────────────────────

$all_eids      = array_unique(array_merge(array_keys($db_by_eid), array_keys($excel_groups)));
$eid_to_dbid   = []; // ElementID string → idTABELLE_Elemente int
$eid_bezeich   = []; // ElementID string → Bezeichnung

// From room rows we already have some
foreach ($db_room_rows as $row) {
    $eid_to_dbid[$row['ElementID']]  = (int)$row['db_elem_internal_id'];
    $eid_bezeich[$row['ElementID']]  = $row['Bezeichnung'];
}

// For Excel-only elements (not yet in this room) we need a DB lookup
$missing_eids = array_diff(array_keys($excel_groups), array_keys($eid_to_dbid));
if (!empty($missing_eids)) {
    $ph    = implode(',', array_fill(0, count($missing_eids), '?'));
    $types = str_repeat('s', count($missing_eids));
    $refs  = [&$types];
    foreach ($missing_eids as $k => $_) $refs[] = &$missing_eids[$k];
    $s3 = $mysqli->prepare("
        SELECT idTABELLE_Elemente AS internal_id, ElementID, Bezeichnung
        FROM tabelle_elemente WHERE ElementID IN ($ph)
    ");
    call_user_func_array([$s3, 'bind_param'], $refs);
    $s3->execute();
    foreach ($s3->get_result()->fetch_all(MYSQLI_ASSOC) as $row) {
        $eid_to_dbid[$row['ElementID']] = (int)$row['internal_id'];
        $eid_bezeich[$row['ElementID']] = $row['Bezeichnung'];
    }
    $s3->close();
}

// ──────────────────────────────────────────────────────────────────
// Step 5: Load variant fingerprints for ALL relevant elements
//   from the project (not just this room) so we can match and
//   show the user what variants exist.
// ──────────────────────────────────────────────────────────────────

// Build a lookup of internal_id → all project variants with their params
// [internal_id][variante_id] = [param_id => wert]
$proj_variants = []; // filled from $all_proj_params already loaded above

// Also need variants for Excel-only elements (not in this room yet)
$extra_internal_ids = [];
foreach ($missing_eids as $eid) {
    if (isset($eid_to_dbid[$eid])) {
        $extra_internal_ids[] = $eid_to_dbid[$eid];
    }
}
if (!empty($extra_internal_ids)) {
    $ph    = implode(',', array_fill(0, count($extra_internal_ids), '?'));
    $types = 'i' . str_repeat('i', count($extra_internal_ids));
    $vals  = array_merge([$projekt_id], $extra_internal_ids);
    $refs  = [&$types];
    foreach ($vals as $k => $_) $refs[] = &$vals[$k];
    $s4 = $mysqli->prepare("
        SELECT tabelle_elemente_idTABELLE_Elemente  AS elem_id,
               tabelle_Varianten_idtabelle_Varianten AS variante_id,
               tabelle_parameter_idTABELLE_Parameter AS param_id,
               Wert
        FROM   tabelle_projekt_elementparameter
        WHERE  tabelle_projekte_idTABELLE_Projekte = ?
          AND  tabelle_elemente_idTABELLE_Elemente IN ($ph)
    ");
    call_user_func_array([$s4, 'bind_param'], $refs);
    $s4->execute();
    foreach ($s4->get_result()->fetch_all(MYSQLI_ASSOC) as $p) {
        $all_proj_params[$p['elem_id']][$p['variante_id']][$p['param_id']] = $p['Wert'];
    }
    $s4->close();
}

// Load variant letter labels for all variant IDs we'll encounter
$all_variant_ids_needed = [];
foreach ($all_proj_params as $e_params) {
    foreach ($e_params as $vid => $_) {
        $all_variant_ids_needed[] = (int)$vid;
    }
}
foreach ($db_room_rows as $row) {
    $all_variant_ids_needed[] = (int)$row['variante_id'];
}
$all_variant_ids_needed = array_unique($all_variant_ids_needed);

$variante_letters = []; // variante_id → letter (A/B/C/…)
if (!empty($all_variant_ids_needed)) {
    $ph    = implode(',', array_fill(0, count($all_variant_ids_needed), '?'));
    $types = str_repeat('i', count($all_variant_ids_needed));
    $refs  = [&$types];
    foreach ($all_variant_ids_needed as $k => $_) $refs[] = &$all_variant_ids_needed[$k];
    $s5 = $mysqli->prepare("SELECT idtabelle_Varianten AS id, Variante FROM tabelle_varianten WHERE idtabelle_Varianten IN ($ph)");
    call_user_func_array([$s5, 'bind_param'], $refs);
    $s5->execute();
    foreach ($s5->get_result()->fetch_all(MYSQLI_ASSOC) as $row) {
        $variante_letters[(int)$row['id']] = $row['Variante'];
    }
    $s5->close();
}

// ──────────────────────────────────────────────────────────────────
// Step 6: Build the comparison — one block per element
// ──────────────────────────────────────────────────────────────────

$element_blocks = [];

foreach ($all_eids as $eid) {
    $internal_id     = $eid_to_dbid[$eid]  ?? null;
    $bezeichnung     = $eid_bezeich[$eid]  ?? '(unbekannt)';
    $in_db_room      = isset($db_by_eid[$eid]);
    $in_excel        = isset($excel_groups[$eid]);

    // ── All project variants for this element ─────────────────────
    // A variant with no parameters = Var A (base)
    // Each unique param fingerprint = its own variant letter
    $proj_elem_params = $internal_id ? ($all_proj_params[$internal_id] ?? []) : [];

    // Build the db_variants list: one entry per variante_id known in project
    $db_variants = [];
    // Include all variants found in room rows for this element
    if ($in_db_room) {
        foreach ($db_by_eid[$eid] as $rhe) {
            $vid = (int)$rhe['variante_id'];
            $db_variants[$vid] ??= [];
        }
    }
    // Also include any variants found in project params (may not be in this room)
    foreach ($proj_elem_params as $vid => $_) {
        $db_variants[(int)$vid] ??= [];
    }

    foreach ($db_variants as $vid => $_) {
        $param_map    = $proj_elem_params[$vid] ?? []; // [param_id => wert]
        $fp           = fingerprint($param_map);

        // Enrich param_map with labels from PARAMETER_MAPPING
        $params_labeled = [];
        foreach ($param_map as $pid => $wert) {
            $cfg = null;
            foreach (PARAMETER_MAPPING as $pname => $pcfg) {
                if ($pcfg['id'] === (int)$pid) { $cfg = $pcfg; break; }
            }
            $params_labeled[(int)$pid] = [
                'wert'        => $wert,
                'einheit'     => $cfg['einheit'] ?? '',
                'bezeichnung' => $cfg ? $cfg['bezeichnung'] : "Param {$pid}",
            ];
        }

        // Find room row for this variant (may not exist if variant exists only in project)
        $rhe_row = null;
        if ($in_db_room) {
            foreach ($db_by_eid[$eid] as $rhe) {
                if ((int)$rhe['variante_id'] === $vid) { $rhe_row = $rhe; break; }
            }
        }

        $db_variants[$vid] = [
            'variante_id'     => $vid,
            'variante_letter' => $variante_letters[$vid] ?? '?',
            'fingerprint'     => $fp,
            'params'          => $params_labeled,
            'label'           => variante_label($params_labeled),
            'in_room'         => $rhe_row !== null,
            'rhe_id'          => $rhe_row ? (int)$rhe_row['rhe_id'] : null,
            'db_anzahl'       => $rhe_row ? (int)$rhe_row['Anzahl'] : 0,
        ];
    }
    ksort($db_variants); // sort by variante_id (= alphabetical A, B, C…)

    // ── Match Excel entries to DB variants ────────────────────────
    // Each Excel entry has a fingerprint (from variante_params).
    // We look for a DB variant whose param_map contains exactly those params.
    // "Variant A" (no params in DB) matches an Excel entry with no variante_params.

    $comparison = [];

    // Track which db_variant IDs have been matched
    $matched_vids = [];

    if ($in_excel) {
        foreach ($excel_groups[$eid] as $fp => $ex) {
            // Excel fingerprint: param_id → wert (only variante_params)
            $ex_fp_map = array_map(fn($p) => $p['wert'], $ex['variante_params']);
            $ex_fp     = fingerprint($ex_fp_map);

            // Find matching DB variant
            $matched_variant = null;
            // ── Match Excel entry against DB variants ─────────────────
            // When variante_params is empty (no fingerprint), the element
            // has no configurable variants → always match Var A (lowest vid).
            // When variante_params is non-empty, match by param subset fingerprint.
            $no_variante_params = empty($ex['variante_params']);

            foreach ($db_variants as $vid => $dbv) {
                if ($no_variante_params) {
                    // No fingerprint params → first DB variant = Var A
                    $matched_variant = $dbv;
                    $matched_vids[]  = $vid;
                    break;
                }

                // Build DB fingerprint subset — only the param_ids relevant for this element type
                $db_fp_subset = [];
                foreach ($ex_fp_map as $pid => $_) {
                    $db_fp_subset[$pid] = $dbv['params'][$pid]['wert'] ?? null;
                }
                $db_fp_cmp = fingerprint($db_fp_subset);

                if ($db_fp_cmp === $ex_fp) {
                    $matched_variant = $dbv;
                    $matched_vids[]  = $vid;
                    break;
                }
            }

            if ($matched_variant) {
                $db_anzahl = $matched_variant['db_anzahl'];
                $comparison[] = [
                    'status'          => $db_anzahl === $ex['anzahl'] ? 'match' : 'diff_anzahl',
                    'variante_id'     => $matched_variant['variante_id'],
                    'variante_letter' => $matched_variant['variante_letter'],
                    'variante_label'  => $matched_variant['label'],
                    'db_anzahl'       => $db_anzahl,
                    'excel_anzahl'    => $ex['anzahl'],
                    'rhe_id'          => $matched_variant['rhe_id'],
                    'db_elem_id'      => $internal_id,
                    'familie'         => $ex['familie'],
                    'laenge_raw'      => $ex['laenge_raw'],
                    'laenge_cm'       => $ex['laenge_cm'],
                    'debug'           => $ex['debug'],
                    'is_sondermass'   => $ex['is_sondermass'],
                    'excel_params'    => $ex['all_params'],
                    'needs_new_variante' => false,
                    'new_variante_params' => [],
                ];
            } else {
                // No matching DB variant → will need a new one
                $comparison[] = [
                    'status'          => 'nur_excel',
                    'variante_id'     => null,
                    'variante_letter' => '(neu)',
                    'variante_label'  => variante_label($ex['variante_params']),
                    'db_anzahl'       => 0,
                    'excel_anzahl'    => $ex['anzahl'],
                    'rhe_id'          => null,
                    'db_elem_id'      => $internal_id,
                    'familie'         => $ex['familie'],
                    'laenge_raw'      => $ex['laenge_raw'],
                    'laenge_cm'       => $ex['laenge_cm'],
                    'debug'           => $ex['debug'],
                    'is_sondermass'   => $ex['is_sondermass'],
                    'excel_params'    => $ex['all_params'],
                    'needs_new_variante' => true,
                    'new_variante_params' => $ex['variante_params'],
                ];
            }
        }
    }

    // DB variants that exist in this room but have no Excel match → remove
    if ($in_db_room) {
        foreach ($db_variants as $vid => $dbv) {
            if (!$dbv['in_room']) continue;              // not in room, skip
            if (in_array($vid, $matched_vids, true)) continue; // already matched

            $comparison[] = [
                'status'          => 'nur_db',
                'variante_id'     => $vid,
                'variante_letter' => $dbv['variante_letter'],
                'variante_label'  => $dbv['label'],
                'db_anzahl'       => $dbv['db_anzahl'],
                'excel_anzahl'    => 0,
                'rhe_id'          => $dbv['rhe_id'],
                'db_elem_id'      => $internal_id,
                'familie'         => null,
                'laenge_raw'      => null,
                'laenge_cm'       => null,
                'debug'           => null,
                'is_sondermass'   => false,
                'excel_params'    => [],
                'needs_new_variante' => false,
                'new_variante_params' => [],
            ];
        }
    }

    // Sort: diff_anzahl first, then nur_excel, nur_db, match
    $sort_order = ['diff_anzahl' => 0, 'nur_excel' => 1, 'nur_db' => 2, 'match' => 3];
    usort($comparison, fn($a, $b) =>
        ($sort_order[$a['status']] ?? 9) - ($sort_order[$b['status']] ?? 9)
    );

    // Collect which param_ids are relevant for variant fingerprint for this element
    // (from the config, via the Excel groups that were resolved)
    $variante_param_ids = [];
    if ($in_excel) {
        foreach ($excel_groups[$eid] as $ex) {
            foreach (array_keys($ex['variante_params']) as $pid) {
                $variante_param_ids[] = (int)$pid;
            }
        }
        $variante_param_ids = array_unique($variante_param_ids);
    }

    $element_blocks[] = [
        'element_id'         => $eid,
        'bezeichnung'        => $bezeichnung,
        'db_internal_id'     => $internal_id,
        'variante_param_ids' => $variante_param_ids, // which params define variants for this element
        'db_variants'        => array_values($db_variants),
        'comparison'         => $comparison,
    ];
}

// Sort blocks: those with changes first
usort($element_blocks, function ($a, $b) {
    $a_has_change = (int)(count(array_filter($a['comparison'], fn($c) => $c['status'] !== 'match')) > 0);
    $b_has_change = (int)(count(array_filter($b['comparison'], fn($c) => $c['status'] !== 'match')) > 0);
    return $b_has_change - $a_has_change; // changed first
});

echo json_encode([
    'raum_id'          => $raum_id,
    'element_blocks'   => $element_blocks,
    'unmapped_familien'=> array_values($unmapped_familien),
], JSON_UNESCAPED_UNICODE);