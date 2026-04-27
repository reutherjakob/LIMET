<?php
/**
 * api_compare_elements.php
 * ══════════════════════════════════════════════════════════════════
 * Vergleicht Excel-Einträge eines Raums mit der DB.
 *
 * "Ignore-Parameter" werden NICHT im Config definiert.
 * Stattdessen gilt: jeder Parameter der in der DB gespeichert ist,
 * aber weder in variante_params noch in element_params vorkommt,
 * wird automatisch ignoriert (nicht gelöscht, nicht verglichen,
 * kann aber "ambiguous" auslösen, wenn er Varianten unterscheidet).
 *
 * Comparison-Status:
 *   match        – identisch
 *   diff_anzahl  – gleiche Variante, andere Anzahl
 *   nur_excel    – neu hinzufügen
 *   nur_db       – Element ist managed → auf 0 setzen
 *   not_managed  – ElementID nicht im Config → nicht angepasst
 *   ambiguous    – mehrere Varianten passen (nur ignore-params
 *                  unterschiedlich) → User muss wählen
 * ══════════════════════════════════════════════════════════════════
 */

ob_start();
if (!function_exists('utils_connect_sql')) { include "../utils/_utils.php"; }
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

function parse_dim_cm(string $raw): int
{
    $raw = trim(mb_strtolower($raw));
    if (str_contains($raw, 'cm')) return (int)round((float)str_replace([',', ' cm', 'cm'], ['.', '', ''], $raw));
    if (str_contains($raw, 'm'))  { $v = (float)str_replace([',', ' m', 'm'], ['.', '', ''], $raw); return (int)round($v * 100); }
    $v = (float)str_replace(',', '.', $raw);
    return $v <= 10 ? (int)round($v * 100) : (int)round($v);
}

function nearest_std(int $cm): array
{
    $best = MZ_STANDARD_LAENGEN[0]; $diff = PHP_INT_MAX;
    foreach (MZ_STANDARD_LAENGEN as $s) { $d = abs($cm - $s); if ($d < $diff) { $diff = $d; $best = $s; } }
    return ['nearest' => $best, 'diff' => $diff];
}

function norm(string $raw): string
{
    if (in_array($raw, ['Ja', 'ja', 'Yes', '1', 'true'],     true)) return '1';
    if (in_array($raw, ['Nein', 'nein', 'No', '0', 'false'], true)) return '0';
    return $raw;
}

function extract_params(array $col_names, array $params_raw): array
{
    $out = [];
    foreach ($col_names as $col) {
        $cfg = PARAMETER_MAPPING[$col] ?? null;
        if (!$cfg) continue;
        $raw = $params_raw[$col] ?? '';
        if ($raw === '') continue;
        $out[$cfg['id']] = ['wert' => norm((string)$raw), 'einheit' => $cfg['einheit'], 'bezeichnung' => $cfg['bezeichnung']];
    }
    return $out;
}

/** Collect all known param IDs from PARAMETER_MAPPING (= "managed" param IDs). */
function all_known_param_ids(): array
{
    return array_map(fn($cfg) => $cfg['id'], array_values(PARAMETER_MAPPING));
}

function resolve(string $familie, string $laenge, string $tiefe, array $params_raw): array
{
    $r = ['element_id' => null, 'variante_params' => [], 'element_params' => [], 'debug' => '', 'laenge_cm' => null, 'is_sondermass' => false];

    foreach (MZ_FAMILIE_MAPPING as $key => $mz) {
        if (!str_contains($familie, $key)) continue;
        $r['variante_params'] = $mz['variante_params'] ?? [];
        $r['element_params']  = $mz['element_params']  ?? $mz['info_params'] ?? [];

        if ($mz['typ'] === 'tisch') {
            $raw_b = $laenge !== '' ? $laenge : ($params_raw['MT_LIMET_Breite'] ?? '');
            $raw_t = $tiefe  !== '' ? $tiefe  : ($params_raw['MT_LIMET_Tiefe']  ?? '');
            if ($raw_b !== '' && $raw_t !== '') {
                $b = parse_dim_cm($raw_b); $t = parse_dim_cm($raw_t);
                $r['laenge_cm'] = $b; $r['debug'] = "Tisch {$b}cm × {$t}cm";
                $k = "{$b}x{$t}";
                if (isset($mz['breite_tiefe'][$k])) { $r['element_id'] = $mz['breite_tiefe'][$k]; }
                else { $r['element_id'] = $mz['sondermass']; $r['is_sondermass'] = true; $r['debug'] .= ' → Sondermaß'; }
            } else { $r['element_id'] = $mz['sondermass']; $r['is_sondermass'] = true; $r['debug'] = 'Breite/Tiefe fehlen → Sondermaß'; }
            return $r;
        }

        if ($laenge !== '') {
            $cm = parse_dim_cm($laenge); $nearest = nearest_std($cm);
            $r['laenge_cm'] = $nearest['nearest']; $r['debug'] = "{$cm}cm → {$nearest['nearest']}cm";
            if (isset($mz['laengen'][$nearest['nearest']])) {
                $r['element_id'] = $mz['laengen'][$nearest['nearest']];
                if ($nearest['diff'] >= MZ_LAENGE_WARN_DIFF_CM) $r['debug'] .= " ⚠ Abw. {$nearest['diff']}cm";
            } else { $r['element_id'] = $mz['sondermass']; $r['is_sondermass'] = true; $r['debug'] .= ' → Sondermaß'; }
        } else { $r['element_id'] = $mz['sondermass']; $r['is_sondermass'] = true; $r['debug'] = 'kein Längenwert → Sondermaß'; }
        return $r;
    }

    if (isset(FAMILIE_MAPPING[$familie])) {
        $fm = FAMILIE_MAPPING[$familie];
        $r['element_id']     = $fm['element_id'];
        $r['variante_params'] = $fm['variante_params'] ?? [];
        $r['element_params']  = $fm['element_params']  ?? $fm['info_params'] ?? [];
        $r['debug'] = 'direktes Mapping';
        return $r;
    }
    return $r;
}

function fingerprint(array $map): string { ksort($map); return json_encode($map); }

function variante_label(array $params): string
{
    $parts = [];
    foreach ($params as $info) {
        if (($info['wert'] ?? '') === '' || $info['wert'] === '0') continue;
        $parts[] = $info['bezeichnung'] . ': ' . $info['wert'] . ($info['einheit'] ? ' ' . $info['einheit'] : '');
    }
    return implode(' · ', $parts) ?: '—';
}

// ──────────────────────────────────────────────────────────────────
// Request
// ──────────────────────────────────────────────────────────────────

if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_error('Method not allowed', 405);
$body = json_decode(file_get_contents('php://input'), true);
if (!isset($body['raum_id']) || !is_numeric($body['raum_id'])) json_error('raum_id fehlt');
if (!isset($body['familien']) || !is_array($body['familien']))  json_error('familien fehlt');
if (!isset($_SESSION['projectID']))                              json_error('Kein Projekt in Session');

$raum_id      = (int)$body['raum_id'];
$familien     = $body['familien'];
$projekt_id   = (int)$_SESSION['projectID'];
$user_choices = (array)($body['user_choices'] ?? []); // { "eid|fp" => variante_id }

$mysqli = utils_connect_sql();

$managed_element_ids = get_all_managed_element_ids();

// IDs of all params we know about from PARAMETER_MAPPING
// = params that can be variante_params or element_params
$known_param_ids = all_known_param_ids();

// ──────────────────────────────────────────────────────────────────
// Step 1: Room DB state
// ──────────────────────────────────────────────────────────────────

$stmt = $mysqli->prepare("
    SELECT rhe.id AS rhe_id, rhe.TABELLE_Elemente_idTABELLE_Elemente AS db_elem_internal_id,
           rhe.tabelle_Varianten_idtabelle_Varianten AS variante_id, rhe.Anzahl,
           e.ElementID, e.Bezeichnung, v.Variante AS variante_letter
    FROM   tabelle_räume_has_tabelle_elemente rhe
    JOIN   tabelle_elemente  e ON e.idTABELLE_Elemente  = rhe.TABELLE_Elemente_idTABELLE_Elemente
    JOIN   tabelle_varianten v ON v.idtabelle_Varianten = rhe.tabelle_Varianten_idtabelle_Varianten
    WHERE  rhe.TABELLE_Räume_idTABELLE_Räume = ? AND rhe.Anzahl > 0
    ORDER  BY e.ElementID, v.Variante
");
$stmt->bind_param('i', $raum_id);
$stmt->execute();
$db_room_rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$db_by_eid = [];
foreach ($db_room_rows as $row) $db_by_eid[$row['ElementID']][] = $row;

// ──────────────────────────────────────────────────────────────────
// Step 2: Project variant params for all elements in room
// ──────────────────────────────────────────────────────────────────

$all_internal_ids = array_unique(array_column($db_room_rows, 'db_elem_internal_id'));
$all_proj_params  = [];

if (!empty($all_internal_ids)) {
    $ph = implode(',', array_fill(0, count($all_internal_ids), '?'));
    $types = 'i' . str_repeat('i', count($all_internal_ids));
    $vals = array_merge([$projekt_id], $all_internal_ids);
    $refs = [&$types]; foreach ($vals as $k => $_) $refs[] = &$vals[$k];
    $s2 = $mysqli->prepare("SELECT tabelle_elemente_idTABELLE_Elemente AS elem_id,
               tabelle_Varianten_idtabelle_Varianten AS variante_id,
               tabelle_parameter_idTABELLE_Parameter AS param_id, Wert
        FROM   tabelle_projekt_elementparameter
        WHERE  tabelle_projekte_idTABELLE_Projekte = ? AND tabelle_elemente_idTABELLE_Elemente IN ($ph)");
    call_user_func_array([$s2, 'bind_param'], $refs);
    $s2->execute();
    foreach ($s2->get_result()->fetch_all(MYSQLI_ASSOC) as $p)
        $all_proj_params[$p['elem_id']][$p['variante_id']][$p['param_id']] = $p['Wert'];
    $s2->close();
}

// ──────────────────────────────────────────────────────────────────
// Step 3: Resolve Excel rows
// ──────────────────────────────────────────────────────────────────

$excel_groups = []; $unmapped_familien = [];

foreach ($familien as $e) {
    $familie    = trim($e['familie'] ?? '');
    $laenge     = trim($e['laenge']  ?? '');
    $tiefe      = trim($e['tiefe']   ?? '');
    $params_raw = $e['params'] ?? [];
    if (!$familie) continue;

    $res = resolve($familie, $laenge, $tiefe, $params_raw);
    if (!$res['element_id']) {
        $key = $familie . '||' . $laenge;
        $unmapped_familien[$key] ??= ['familie' => $familie, 'laenge' => $laenge, 'anzahl' => 0];
        $unmapped_familien[$key]['anzahl']++;
        continue;
    }

    $vparams = extract_params($res['variante_params'], $params_raw);
    $eparams = extract_params($res['element_params'],  $params_raw);
    $fp      = fingerprint(array_map(fn($p) => $p['wert'], $vparams));
    $eid     = $res['element_id'];

    $excel_groups[$eid] ??= [];
    if (isset($excel_groups[$eid][$fp])) {
        $excel_groups[$eid][$fp]['anzahl']++;
    } else {
        $excel_groups[$eid][$fp] = [
            'fingerprint'         => $fp,
            'anzahl'              => 1,
            'variante_params'     => $vparams,
            'element_params'      => $eparams,
            'familie'             => $familie,
            'laenge_raw'          => $laenge,
            'tiefe_raw'           => $tiefe,
            'laenge_cm'           => $res['laenge_cm'],
            'debug'               => $res['debug'],
            'is_sondermass'       => $res['is_sondermass'],
            'has_variante_params' => !empty($res['variante_params']),
        ];
    }
}

// ──────────────────────────────────────────────────────────────────
// Step 4: Resolve element_id strings → internal IDs + names
// ──────────────────────────────────────────────────────────────────

$all_eids    = array_unique(array_merge(array_keys($db_by_eid), array_keys($excel_groups)));
$eid_to_dbid = []; $eid_bezeich = [];
foreach ($db_room_rows as $row) {
    $eid_to_dbid[$row['ElementID']] = (int)$row['db_elem_internal_id'];
    $eid_bezeich[$row['ElementID']] = $row['Bezeichnung'];
}

$missing_eids = array_diff(array_keys($excel_groups), array_keys($eid_to_dbid));
if (!empty($missing_eids)) {
    $ph = implode(',', array_fill(0, count($missing_eids), '?'));
    $types = str_repeat('s', count($missing_eids));
    $refs = [&$types]; foreach ($missing_eids as $k => $_) $refs[] = &$missing_eids[$k];
    $s3 = $mysqli->prepare("SELECT idTABELLE_Elemente AS id, ElementID, Bezeichnung FROM tabelle_elemente WHERE ElementID IN ($ph)");
    call_user_func_array([$s3, 'bind_param'], $refs);
    $s3->execute();
    foreach ($s3->get_result()->fetch_all(MYSQLI_ASSOC) as $row) {
        $eid_to_dbid[$row['ElementID']] = (int)$row['id'];
        $eid_bezeich[$row['ElementID']] = $row['Bezeichnung'];
    }
    $s3->close();
}

$extra_ids = array_values(array_filter(array_map(fn($eid) => $eid_to_dbid[$eid] ?? null, $missing_eids)));
if (!empty($extra_ids)) {
    $ph = implode(',', array_fill(0, count($extra_ids), '?'));
    $types = 'i' . str_repeat('i', count($extra_ids));
    $vals = array_merge([$projekt_id], $extra_ids);
    $refs = [&$types]; foreach ($vals as $k => $_) $refs[] = &$vals[$k];
    $s4 = $mysqli->prepare("SELECT tabelle_elemente_idTABELLE_Elemente AS elem_id,
               tabelle_Varianten_idtabelle_Varianten AS variante_id,
               tabelle_parameter_idTABELLE_Parameter AS param_id, Wert
        FROM   tabelle_projekt_elementparameter
        WHERE  tabelle_projekte_idTABELLE_Projekte = ? AND tabelle_elemente_idTABELLE_Elemente IN ($ph)");
    call_user_func_array([$s4, 'bind_param'], $refs);
    $s4->execute();
    foreach ($s4->get_result()->fetch_all(MYSQLI_ASSOC) as $p)
        $all_proj_params[$p['elem_id']][$p['variante_id']][$p['param_id']] = $p['Wert'];
    $s4->close();
}

// Variant letters
$vid_needed = [];
foreach ($all_proj_params as $ep) foreach ($ep as $vid => $_) $vid_needed[] = (int)$vid;
foreach ($db_room_rows as $row) $vid_needed[] = (int)$row['variante_id'];
$vid_needed = array_unique($vid_needed);
$variante_letters = [];
if (!empty($vid_needed)) {
    $ph = implode(',', array_fill(0, count($vid_needed), '?'));
    $types = str_repeat('i', count($vid_needed));
    $refs = [&$types]; foreach ($vid_needed as $k => $_) $refs[] = &$vid_needed[$k];
    $s5 = $mysqli->prepare("SELECT idtabelle_Varianten AS id, Variante FROM tabelle_varianten WHERE idtabelle_Varianten IN ($ph)");
    call_user_func_array([$s5, 'bind_param'], $refs);
    $s5->execute();
    foreach ($s5->get_result()->fetch_all(MYSQLI_ASSOC) as $row) $variante_letters[(int)$row['id']] = $row['Variante'];
    $s5->close();
}

// ──────────────────────────────────────────────────────────────────
// Step 5: Build element blocks
// ──────────────────────────────────────────────────────────────────

$element_blocks = [];

foreach ($all_eids as $eid) {
    $internal_id      = $eid_to_dbid[$eid] ?? null;
    $bezeichnung      = $eid_bezeich[$eid] ?? '(unbekannt)';
    $in_db_room       = isset($db_by_eid[$eid]);
    $in_excel         = isset($excel_groups[$eid]);
    $proj_elem_params = $internal_id ? ($all_proj_params[$internal_id] ?? []) : [];
    $is_managed       = in_array($eid, $managed_element_ids, true);

    // Collect variante_param_ids and element_param_ids from Excel groups
    $has_variante_params  = false;
    $variante_param_ids   = [];
    $variante_param_names = [];
    $element_param_ids    = [];

    if ($in_excel) {
        foreach ($excel_groups[$eid] as $ex) {
            if ($ex['has_variante_params']) {
                $has_variante_params = true;
                foreach ($ex['variante_params'] as $pid => $pinfo) {
                    if (!in_array((int)$pid, $variante_param_ids)) {
                        $variante_param_ids[]   = (int)$pid;
                        $variante_param_names[] = $pinfo['bezeichnung'];
                    }
                }
            }
            foreach ($ex['element_params'] as $pid => $_) {
                if (!in_array((int)$pid, $element_param_ids)) $element_param_ids[] = (int)$pid;
            }
        }
    }

    // "Explicitly managed" param IDs = variante_params + element_params
    $explicit_param_ids = array_unique(array_merge($variante_param_ids, $element_param_ids));

    // Build db_variants
    $db_variants = [];
    if ($in_db_room) foreach ($db_by_eid[$eid] as $rhe) { $vid = (int)$rhe['variante_id']; $db_variants[$vid] ??= []; }
    foreach ($proj_elem_params as $vid => $_) $db_variants[(int)$vid] ??= [];

    foreach ($db_variants as $vid => $_) {
        $param_map = $proj_elem_params[$vid] ?? [];

        $params_labeled = [];
        foreach ($param_map as $pid => $wert) {
            $cfg = null;
            foreach (PARAMETER_MAPPING as $pcfg) { if ($pcfg['id'] === (int)$pid) { $cfg = $pcfg; break; } }
            $params_labeled[(int)$pid] = [
                'wert'        => $wert,
                'einheit'     => $cfg['einheit'] ?? '',
                'bezeichnung' => $cfg ? $cfg['bezeichnung'] : "Param #{$pid}",
                // Classify each param: variante | element | ignore
                'role'        => in_array((int)$pid, $variante_param_ids, true) ? 'variante'
                    : (in_array((int)$pid, $element_param_ids, true) ? 'element' : 'ignore'),
            ];
        }

        $rhe_row = null;
        if ($in_db_room) foreach ($db_by_eid[$eid] as $rhe) { if ((int)$rhe['variante_id'] === $vid) { $rhe_row = $rhe; break; } }

        // Variant fingerprint = ONLY variante_param_ids (ignore everything else)
        $fp_data = [];
        foreach ($variante_param_ids as $pid) $fp_data[$pid] = $params_labeled[$pid]['wert'] ?? null;

        // Ignore-param IDs = all stored params that are NOT explicitly managed
        $ignore_param_ids_for_var = array_values(array_filter(
            array_keys($params_labeled),
            fn($pid) => !in_array((int)$pid, $explicit_param_ids, true)
        ));

        $db_variants[$vid] = [
            'variante_id'      => $vid,
            'variante_letter'  => $variante_letters[$vid] ?? '?',
            'params'           => $params_labeled,
            'variante_fp'      => fingerprint($fp_data),
            'ignore_param_ids' => $ignore_param_ids_for_var,
            'has_only_ignored' => !empty($param_map) && empty(array_filter($params_labeled, fn($p) => $p['role'] !== 'ignore')),
            'in_room'          => $rhe_row !== null,
            'rhe_id'           => $rhe_row ? (int)$rhe_row['rhe_id'] : null,
            'db_anzahl'        => $rhe_row ? (int)$rhe_row['Anzahl'] : 0,
        ];
    }
    ksort($db_variants);

    // ── Match Excel → DB variants ──────────────────────────────────
    $comparison = []; $matched_vids = [];

    if ($in_excel) {
        foreach ($excel_groups[$eid] as $fp => $ex) {
            $choice_key = $eid . '|' . $fp;

            $ex_fp_map = array_map(fn($p) => $p['wert'], $ex['variante_params']);
            $ex_fp     = fingerprint($ex_fp_map);

            // Find candidates: variants that match on variante_params
            // (ignore everything else — that's the whole point)
            $candidates = [];
            foreach ($db_variants as $vid => $dbv) {
                if (!$ex['has_variante_params']) {
                    // Parameterless type: candidate must have NO variante_params stored.
                    // (ignore-params may exist — fine)
                    $has_variante_stored = !empty(array_filter(
                        $dbv['params'],
                        fn($p) => $p['role'] === 'variante'
                    ));
                    if (!$has_variante_stored) $candidates[] = $dbv;
                } else {
                    if ($dbv['variante_fp'] === $ex_fp) $candidates[] = $dbv;
                }
            }

            if (count($candidates) === 0) {
                $comparison[] = [
                    'status'              => 'nur_excel',
                    'variante_id'         => null,
                    'variante_letter'     => '(neu)',
                    'variante_label'      => variante_label($ex['variante_params']),
                    'db_anzahl'           => 0,
                    'excel_anzahl'        => $ex['anzahl'],
                    'rhe_id'              => null,
                    'db_elem_id'          => $internal_id,
                    'familie'             => $ex['familie'],
                    'laenge_raw'          => $ex['laenge_raw'],
                    'tiefe_raw'           => $ex['tiefe_raw'],
                    'laenge_cm'           => $ex['laenge_cm'],
                    'debug'               => $ex['debug'],
                    'is_sondermass'       => $ex['is_sondermass'],
                    'excel_params'        => $ex['variante_params'],
                    'element_params'      => $ex['element_params'],
                    'needs_new_variante'  => true,
                    'new_variante_params' => $ex['variante_params'],
                    'candidates'          => [],
                    'chosen_variante_id'  => null,
                    'choice_key'          => $choice_key,
                ];

            } elseif (count($candidates) === 1) {
                $matched = $candidates[0];
                $matched_vids[] = $matched['variante_id'];
                $db_anzahl = $matched['db_anzahl'];
                $comparison[] = [
                    'status' => $db_anzahl == 0
                        ? 'nur_excel'
                        : ($db_anzahl === $ex['anzahl'] ? 'match' : 'diff_anzahl'),
                    'variante_id'         => $matched['variante_id'],
                    'variante_letter'     => $matched['variante_letter'],
                    'variante_label'      => variante_label(array_filter($matched['params'], fn($p) => $p['role'] === 'variante')),
                    'db_anzahl'           => $db_anzahl,
                    'excel_anzahl'        => $ex['anzahl'],
                    'rhe_id'              => $matched['rhe_id'],
                    'db_elem_id'          => $internal_id,
                    'familie'             => $ex['familie'],
                    'laenge_raw'          => $ex['laenge_raw'],
                    'tiefe_raw'           => $ex['tiefe_raw'],
                    'laenge_cm'           => $ex['laenge_cm'],
                    'debug'               => $ex['debug'],
                    'is_sondermass'       => $ex['is_sondermass'],
                    'excel_params'        => $ex['variante_params'],
                    'element_params'      => $ex['element_params'],
                    'needs_new_variante'  => false,
                    'new_variante_params' => [],
                    'candidates'          => [],
                    'chosen_variante_id'  => null,
                    'choice_key'          => $choice_key,
                ];

            } else {
                // Ambiguous — check if user already chose
                $chosen_vid = isset($user_choices[$choice_key]) ? (int)$user_choices[$choice_key] : null;
                $chosen     = null;
                if ($chosen_vid) foreach ($candidates as $c) { if ($c['variante_id'] === $chosen_vid) { $chosen = $c; break; } }

                if ($chosen) {
                    $matched_vids[] = $chosen['variante_id'];
                    $db_anzahl = $chosen['db_anzahl'];
                    $comparison[] = [
                        'status'              => $db_anzahl === $ex['anzahl'] ? 'match' : 'diff_anzahl',
                        'variante_id'         => $chosen['variante_id'],
                        'variante_letter'     => $chosen['variante_letter'],
                        'variante_label'      => variante_label(array_filter($chosen['params'], fn($p) => $p['role'] === 'variante')),
                        'db_anzahl'           => $db_anzahl,
                        'excel_anzahl'        => $ex['anzahl'],
                        'rhe_id'              => $chosen['rhe_id'],
                        'db_elem_id'          => $internal_id,
                        'familie'             => $ex['familie'],
                        'laenge_raw'          => $ex['laenge_raw'],
                        'tiefe_raw'           => $ex['tiefe_raw'],
                        'laenge_cm'           => $ex['laenge_cm'],
                        'debug'               => $ex['debug'],
                        'is_sondermass'       => $ex['is_sondermass'],
                        'excel_params'        => $ex['variante_params'],
                        'element_params'      => $ex['element_params'],
                        'needs_new_variante'  => false,
                        'new_variante_params' => [],
                        'candidates'          => [],
                        'chosen_variante_id'  => $chosen_vid,
                        'choice_key'          => $choice_key,
                    ];
                } else {
                    $comparison[] = [
                        'status'              => 'ambiguous',
                        'variante_id'         => null,
                        'variante_letter'     => '?',
                        'variante_label'      => '— Auswahl nötig —',
                        'db_anzahl'           => 0,
                        'excel_anzahl'        => $ex['anzahl'],
                        'rhe_id'              => null,
                        'db_elem_id'          => $internal_id,
                        'familie'             => $ex['familie'],
                        'laenge_raw'          => $ex['laenge_raw'],
                        'tiefe_raw'           => $ex['tiefe_raw'],
                        'laenge_cm'           => $ex['laenge_cm'],
                        'debug'               => $ex['debug'],
                        'is_sondermass'       => $ex['is_sondermass'],
                        'excel_params'        => $ex['variante_params'],
                        'element_params'      => $ex['element_params'],
                        'needs_new_variante'  => false,
                        'new_variante_params' => [],
                        'candidates'          => array_values($candidates),
                        'chosen_variante_id'  => null,
                        'choice_key'          => $choice_key,
                    ];
                }
            }
        }
    }

    // DB variants in room not matched by Excel
    if ($in_db_room) {
        foreach ($db_variants as $vid => $dbv) {
            if (!$dbv['in_room'] || in_array($vid, $matched_vids, true)) continue;
            $status = $is_managed ? 'nur_db' : 'not_managed';
            $comparison[] = [
                'status'              => $status,
                'variante_id'         => $vid,
                'variante_letter'     => $dbv['variante_letter'],
                'variante_label'      => variante_label(array_filter($dbv['params'], fn($p) => $p['role'] === 'variante')),
                'db_anzahl'           => $dbv['db_anzahl'],
                'excel_anzahl'        => 0,
                'rhe_id'              => $dbv['rhe_id'],
                'db_elem_id'          => $internal_id,
                'familie'             => null, 'laenge_raw' => null, 'tiefe_raw' => null,
                'laenge_cm'           => null, 'debug' => null, 'is_sondermass' => false,
                'excel_params'        => [], 'element_params' => [],
                'needs_new_variante'  => false, 'new_variante_params' => [],
                'candidates'          => [], 'chosen_variante_id' => null, 'choice_key' => null,
            ];
        }
    }

    $sort_order = ['ambiguous' => 0, 'diff_anzahl' => 1, 'nur_excel' => 2, 'nur_db' => 3, 'match' => 4, 'not_managed' => 5];
    usort($comparison, fn($a, $b) => ($sort_order[$a['status']] ?? 9) - ($sort_order[$b['status']] ?? 9));

    $element_blocks[] = [
        'element_id'           => $eid,
        'bezeichnung'          => $bezeichnung,
        'db_internal_id'       => $internal_id,
        'is_managed'           => $is_managed,
        'has_variante_params'  => $has_variante_params,
        'variante_param_ids'   => $variante_param_ids,
        'variante_param_names' => $variante_param_names,
        'element_param_ids'    => $element_param_ids,
        'db_variants'          => array_values($db_variants),
        'comparison'           => $comparison,
    ];
}

// Blocks with most urgent status first
usort($element_blocks, function ($a, $b) {
    $priority = fn($block) => array_reduce($block['comparison'], function ($carry, $c) {
        $p = ['ambiguous' => 0, 'diff_anzahl' => 1, 'nur_excel' => 1, 'nur_db' => 1, 'match' => 3, 'not_managed' => 2];
        return min($carry, $p[$c['status']] ?? 9);
    }, 9);
    return $priority($a) - $priority($b);
});

echo json_encode([
    'raum_id'           => $raum_id,
    'element_blocks'    => $element_blocks,
    'unmapped_familien' => array_values($unmapped_familien),
], JSON_UNESCAPED_UNICODE);