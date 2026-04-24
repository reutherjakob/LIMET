<?php
/**
 * api_compare_elements.php
 * ══════════════════════════════════════════════════════════════════
 * Compares the Excel entries of a room against the DB.
 *
 * Variant matching rules:
 *   variante_params: []  → no configurable variants. Match a DB variant
 *                          that has ZERO params stored for this element
 *                          in this project (true "clean Var A").
 *   variante_params: [x] → fingerprint match on those param IDs only.
 *
 * POST: { raum_id, familien: [{familie, laenge, tiefe, variante, params}] }
 * Response: { raum_id, element_blocks, unmapped_familien }
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
    if (in_array($raw, ['Ja', 'ja', 'Yes', '1', 'true'],  true)) return '1';
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

function resolve(string $familie, string $laenge, string $tiefe, array $params_raw): array
{
    $r = ['element_id' => null, 'variante_params' => [], 'info_params' => [], 'debug' => '', 'laenge_cm' => null, 'is_sondermass' => false];

    foreach (MZ_FAMILIE_MAPPING as $key => $mz) {
        if (!str_contains($familie, $key)) continue;
        $r['variante_params'] = $mz['variante_params'] ?? [];
        $r['info_params']     = $mz['info_params'] ?? [];

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
        $r['element_id'] = $fm['element_id'];
        $r['variante_params'] = $fm['variante_params'] ?? [];
        $r['info_params']     = $fm['info_params'] ?? [];
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

$raum_id    = (int)$body['raum_id'];
$familien   = $body['familien'];
$projekt_id = (int)$_SESSION['projectID'];
$mysqli     = utils_connect_sql();

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
// Step 2: Project variant params for room elements
// ──────────────────────────────────────────────────────────────────

$all_internal_ids = array_unique(array_column($db_room_rows, 'db_elem_internal_id'));
$all_proj_params  = []; // [elem_id][variante_id][param_id] = wert

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
    $iparams = extract_params($res['info_params'],     $params_raw);
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
            'all_params'          => $vparams + $iparams,
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
// Step 4 & 5: Resolve element_id strings → internal IDs, load extra params
// ──────────────────────────────────────────────────────────────────

$all_eids    = array_unique(array_merge(array_keys($db_by_eid), array_keys($excel_groups)));
$eid_to_dbid = []; $eid_bezeich = [];
foreach ($db_room_rows as $row) { $eid_to_dbid[$row['ElementID']] = (int)$row['db_elem_internal_id']; $eid_bezeich[$row['ElementID']] = $row['Bezeichnung']; }

$missing_eids = array_diff(array_keys($excel_groups), array_keys($eid_to_dbid));
if (!empty($missing_eids)) {
    $ph = implode(',', array_fill(0, count($missing_eids), '?'));
    $types = str_repeat('s', count($missing_eids));
    $refs = [&$types]; foreach ($missing_eids as $k => $_) $refs[] = &$missing_eids[$k];
    $s3 = $mysqli->prepare("SELECT idTABELLE_Elemente AS id, ElementID, Bezeichnung FROM tabelle_elemente WHERE ElementID IN ($ph)");
    call_user_func_array([$s3, 'bind_param'], $refs);
    $s3->execute();
    foreach ($s3->get_result()->fetch_all(MYSQLI_ASSOC) as $row) { $eid_to_dbid[$row['ElementID']] = (int)$row['id']; $eid_bezeich[$row['ElementID']] = $row['Bezeichnung']; }
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

// Load variant letters
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
// Step 6: Build element blocks
// ──────────────────────────────────────────────────────────────────

$element_blocks = [];

foreach ($all_eids as $eid) {
    $internal_id      = $eid_to_dbid[$eid] ?? null;
    $bezeichnung      = $eid_bezeich[$eid] ?? '(unbekannt)';
    $in_db_room       = isset($db_by_eid[$eid]);
    $in_excel         = isset($excel_groups[$eid]);
    $proj_elem_params = $internal_id ? ($all_proj_params[$internal_id] ?? []) : [];

    // What variante_params does this element type use?
    $has_variante_params  = false;
    $variante_param_ids   = [];
    $variante_param_names = [];
    if ($in_excel) {
        foreach ($excel_groups[$eid] as $ex) {
            if ($ex['has_variante_params']) {
                $has_variante_params = true;
                foreach ($ex['variante_params'] as $pid => $pinfo) {
                    if (!in_array((int)$pid, $variante_param_ids)) $variante_param_ids[] = (int)$pid;
                    if (!in_array($pinfo['bezeichnung'], $variante_param_names)) $variante_param_names[] = $pinfo['bezeichnung'];
                }
            }
        }
    }

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
            ];
        }
        $rhe_row = null;
        if ($in_db_room) foreach ($db_by_eid[$eid] as $rhe) { if ((int)$rhe['variante_id'] === $vid) { $rhe_row = $rhe; break; } }

        $db_variants[$vid] = [
            'variante_id'     => $vid,
            'variante_letter' => $variante_letters[$vid] ?? '?',
            'params'          => $params_labeled,
            'has_any_params'  => !empty($param_map),
            'label'           => variante_label($params_labeled),
            'in_room'         => $rhe_row !== null,
            'rhe_id'          => $rhe_row ? (int)$rhe_row['rhe_id'] : null,
            'db_anzahl'       => $rhe_row ? (int)$rhe_row['Anzahl'] : 0,
        ];
    }
    ksort($db_variants);

    // Match Excel entries → DB variants
    $comparison = []; $matched_vids = [];

    if ($in_excel) {
        foreach ($excel_groups[$eid] as $fp => $ex) {
            $matched_variant = null;

            if (!$ex['has_variante_params']) {
                // Parameterless type → find a variant with ZERO params stored
                // for THIS element in this project (true clean Var A)
                foreach ($db_variants as $vid => $dbv) {
                    if (!$dbv['has_any_params']) { $matched_variant = $dbv; $matched_vids[] = $vid; break; }
                }
            } else {
                // Fingerprint match on relevant param IDs only
                $ex_fp_map = array_map(fn($p) => $p['wert'], $ex['variante_params']);
                $ex_fp     = fingerprint($ex_fp_map);
                foreach ($db_variants as $vid => $dbv) {
                    $sub = [];
                    foreach ($ex_fp_map as $pid => $_) $sub[$pid] = $dbv['params'][$pid]['wert'] ?? null;
                    if (fingerprint($sub) === $ex_fp) { $matched_variant = $dbv; $matched_vids[] = $vid; break; }
                }
            }

            if ($matched_variant) {
                $comparison[] = [
                    'status'              => $matched_variant['db_anzahl'] === $ex['anzahl'] ? 'match' : 'diff_anzahl',
                    'variante_id'         => $matched_variant['variante_id'],
                    'variante_letter'     => $matched_variant['variante_letter'],
                    'variante_label'      => $matched_variant['label'],
                    'db_anzahl'           => $matched_variant['db_anzahl'],
                    'excel_anzahl'        => $ex['anzahl'],
                    'rhe_id'              => $matched_variant['rhe_id'],
                    'db_elem_id'          => $internal_id,
                    'familie'             => $ex['familie'],
                    'laenge_raw'          => $ex['laenge_raw'],
                    'tiefe_raw'           => $ex['tiefe_raw'],
                    'laenge_cm'           => $ex['laenge_cm'],
                    'debug'               => $ex['debug'],
                    'is_sondermass'       => $ex['is_sondermass'],
                    'excel_params'        => $ex['all_params'],
                    'needs_new_variante'  => false,
                    'new_variante_params' => [],
                ];
            } else {
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
                    'excel_params'        => $ex['all_params'],
                    'needs_new_variante'  => true,
                    'new_variante_params' => $ex['variante_params'],
                ];
            }
        }
    }

    if ($in_db_room) {
        foreach ($db_variants as $vid => $dbv) {
            if (!$dbv['in_room'] || in_array($vid, $matched_vids, true)) continue;
            $comparison[] = [
                'status'              => 'nur_db',
                'variante_id'         => $vid,
                'variante_letter'     => $dbv['variante_letter'],
                'variante_label'      => $dbv['label'],
                'db_anzahl'           => $dbv['db_anzahl'],
                'excel_anzahl'        => 0,
                'rhe_id'              => $dbv['rhe_id'],
                'db_elem_id'          => $internal_id,
                'familie'             => null,
                'laenge_raw'          => null,
                'tiefe_raw'           => null,
                'laenge_cm'           => null,
                'debug'               => null,
                'is_sondermass'       => false,
                'excel_params'        => [],
                'needs_new_variante'  => false,
                'new_variante_params' => [],
            ];
        }
    }

    $sort_order = ['diff_anzahl' => 0, 'nur_excel' => 1, 'nur_db' => 2, 'match' => 3];
    usort($comparison, fn($a, $b) => ($sort_order[$a['status']] ?? 9) - ($sort_order[$b['status']] ?? 9));

    $element_blocks[] = [
        'element_id'           => $eid,
        'bezeichnung'          => $bezeichnung,
        'db_internal_id'       => $internal_id,
        'has_variante_params'  => $has_variante_params,
        'variante_param_ids'   => $variante_param_ids,
        'variante_param_names' => $variante_param_names,
        'db_variants'          => array_values($db_variants),
        'comparison'           => $comparison,
    ];
}

usort($element_blocks, function ($a, $b) {
    $ac = count(array_filter($a['comparison'], fn($c) => $c['status'] !== 'match'));
    $bc = count(array_filter($b['comparison'], fn($c) => $c['status'] !== 'match'));
    return $bc - $ac;
});

echo json_encode(['raum_id' => $raum_id, 'element_blocks' => $element_blocks, 'unmapped_familien' => array_values($unmapped_familien)], JSON_UNESCAPED_UNICODE);