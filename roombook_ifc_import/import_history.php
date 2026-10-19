<?php
/**
 * import_history.php
 * ══════════════════════════════════════════════════════════════════
 * Import-Log für den Excel/IFC-Import – OHNE eigene Log-Tabelle.
 * Baut die Historie aus bestehenden Daten zusammen:
 *
 *   1. RB-Änderungstabelle (Trigger-Protokoll von tabelle_räume_has_tabelle_elemente)
 *      → Zeitstempel, User, Anzahl alt/neu, Kommentar alt/neu.
 *      Import-Änderungen werden am neuen Kommentar erkannt ("… via Excel-…") und
 *      über Zeitnähe + User zu einzelnen Import-Läufen gruppiert.
 *   2. Elementkommentare (Kurzbeschreibung) → ergänzen, was in 1 fehlt
 *      (z.B. neu angelegte Zeilen, falls der Trigger nur bei UPDATE feuert).
 *   3. tabelle_import_runs / tabelle_import_log → nur falls irgendwann angelegt (exakt).
 *
 * Tabelle und Spalten der Änderungstabelle werden automatisch erkannt.
 * Liegt die Erkennung daneben: CHANGE_TABLE_OVERRIDE unten ausfüllen.
 * ══════════════════════════════════════════════════════════════════
 */
if (!function_exists('utils_connect_sql')) {
    include "../_utils/_utils.php";
}
init_page_serversides();

// ──────────────────────────────────────────────────────────────────
// Konfiguration
// ──────────────────────────────────────────────────────────────────

// Änderungen desselben Users, die weniger als X Sekunden auseinander liegen = ein Import
const RUN_GAP_SECONDS = 180;

const CHANGE_TABLE_CANDIDATES = [
    'tabelle_rb_aenderung', 'tabelle_rb_aenderungen', 'tabelle_rb_änderung', 'tabelle_rb_änderungen',
];

// Nur ausfüllen, wenn die automatische Erkennung falsch liegt (Spaltennamen exakt wie in der DB)
const CHANGE_TABLE_OVERRIDE = [
    'table'   => null,  // z.B. 'tabelle_rb_aenderung'
    'rhe'     => null,  // Spalte mit der id aus tabelle_räume_has_tabelle_elemente
    'time'    => null,  // z.B. 'Timestamp'
    'user'    => null,  // z.B. 'user'
    'anz_alt' => null,  // z.B. 'Anzahl'
    'anz_neu' => null,  // z.B. 'Anzahl_copy1'
    'kom_alt' => null,  // z.B. 'Kurzbeschreibung'
    'kom_neu' => null,  // z.B. 'Kurzbeschreibung_copy1'
];

$mysqli     = utils_connect_sql();
$projekt_id = (int)$_SESSION['projectID'];

// ──────────────────────────────────────────────────────────────────
// Helpers
// ──────────────────────────────────────────────────────────────────

function h($s): string
{
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

function qi(string $name): string
{
    return '`' . str_replace('`', '``', $name) . '`';
}

function table_exists(mysqli $db, string $name): bool
{
    try {
        $r = $db->query("SHOW TABLES LIKE '" . $db->real_escape_string($name) . "'");
        return $r && $r->num_rows > 0;
    } catch (Throwable) {
        return false;
    }
}

function fmt_date(string $ts, bool $with_time = true): string
{
    $t = strtotime($ts);
    return $t ? date($with_time ? 'd.m.Y H:i' : 'd.m.Y', $t) : $ts;
}

function first_line(?string $s): string
{
    $s = trim((string)$s);
    if ($s === '') return '';
    return trim(preg_split('/\R/u', $s, 2)[0]);
}

function clean_user(?string $u): string
{
    return trim(preg_replace('/@.*$/', '', (string)$u));
}

/** Aktion aus einer Kommentarzeile ableiten. */
function classify_text(string $text): array
{
    if (preg_match('/Anzahl von (\d+) auf (\d+)/u', $text, $m)) {
        return ['action' => 'update', 'alt' => (int)$m[1], 'neu' => (int)$m[2]];
    }
    if (stripos($text, 'Hinzugefügt') !== false) {
        return ['action' => 'add', 'alt' => null, 'neu' => null];
    }
    return ['action' => 'remove', 'alt' => null, 'neu' => 0];
}

function delta_of(array $row): ?int
{
    return ($row['alt'] !== null && $row['neu'] !== null) ? $row['neu'] - $row['alt'] : null;
}

function render_change(?int $alt, ?int $neu): string
{
    $a = $alt === null ? '<span class="text-muted">?</span>' : $alt;
    $n = $neu === null ? '<span class="text-muted">?</span>' : '<strong>' . $neu . '</strong>';
    return $a . ' <i class="fas fa-long-arrow-alt-right text-muted mx-1"></i> ' . $n;
}

function render_delta(?int $d): string
{
    if ($d === null) return '<span class="text-muted">–</span>';
    if ($d > 0) return '<span class="text-success fw-semibold">+' . $d . '</span>';
    if ($d < 0) return '<span class="text-danger fw-semibold">' . $d . '</span>';
    return '<span class="text-muted">±0</span>';
}

const ACTION_UI = [
    'add'    => ['label' => 'Hinzugefügt', 'color' => 'success', 'icon' => 'fa-plus'],
    'update' => ['label' => 'Anzahl geändert', 'color' => 'warning', 'icon' => 'fa-exchange-alt'],
    'remove' => ['label' => 'Entfernt', 'color' => 'danger', 'icon' => 'fa-minus'],
];

function action_badge(string $action): string
{
    $ui = ACTION_UI[$action] ?? ['label' => $action, 'color' => 'secondary', 'icon' => 'fa-question'];
    $txt = $ui['color'] === 'warning' ? ' text-dark' : '';
    return '<span class="badge bg-' . $ui['color'] . $txt . '"><i class="fas ' . $ui['icon'] . ' me-1"></i>' . $ui['label'] . '</span>';
}

function count_badges(int $add, int $upd, int $rem): string
{
    $out = '';
    if ($add) $out .= '<span class="badge bg-success me-1" title="hinzugefügt">+' . $add . '</span>';
    if ($upd) $out .= '<span class="badge bg-warning text-dark me-1" title="Anzahl geändert">~' . $upd . '</span>';
    if ($rem) $out .= '<span class="badge bg-danger me-1" title="entfernt">−' . $rem . '</span>';
    return $out ?: '<span class="badge bg-light text-muted">keine Änderung</span>';
}

function make_row(array $meta, string $action, ?int $alt, ?int $neu, string $info, string $src): array
{
    return [
        'action'      => $action,
        'raum_id'     => (int)$meta['raum_id'],
        'raumnr'      => $meta['Raumnr'],
        'raumbez'     => $meta['Raumbezeichnung'],
        'geschoss'    => $meta['Geschoss'],
        'element'     => $meta['ElementID'],
        'element_bez' => $meta['element_bez'],
        'variante'    => $meta['Variante'] ?? '',
        'alt'         => $alt,
        'neu'         => $neu,
        'info'        => $info,
        'src'         => $src,
    ];
}

/** Erkennt die RB-Änderungstabelle und ihre Spalten. */
function detect_change_table(mysqli $db): array
{
    $o = CHANGE_TABLE_OVERRIDE;
    $table = $o['table'];
    if (!$table) {
        foreach (CHANGE_TABLE_CANDIDATES as $c) {
            if (table_exists($db, $c)) { $table = $c; break; }
        }
    }
    if (!$table) {
        try {
            $r = $db->query("SHOW TABLES LIKE 'tabelle\\_rb%'");
            while ($r && $row = $r->fetch_row()) {
                if (preg_match('/nderung/iu', $row[0])) { $table = $row[0]; break; }
            }
        } catch (Throwable) {
        }
    }
    if (!$table) {
        return ['ok' => false, 'table' => null, 'columns' => [], 'msg' => 'Keine RB-Änderungstabelle gefunden.'];
    }

    $cols = [];
    $pri = [];
    try {
        $r = $db->query("SHOW COLUMNS FROM " . qi($table));
        while ($r && $c = $r->fetch_assoc()) {
            $k = mb_strtolower($c['Field']);
            $cols[$k] = $c['Field'];
            if ($c['Key'] === 'PRI') $pri[$k] = true;
        }
    } catch (Throwable $e) {
        return ['ok' => false, 'table' => $table, 'columns' => [], 'msg' => 'Spalten nicht lesbar: ' . $e->getMessage()];
    }

    $pick = function (array $cands, bool $skip_pri = false) use ($cols, $pri): ?string {
        foreach ($cands as $c) {
            $k = mb_strtolower($c);
            if (isset($cols[$k]) && !($skip_pri && isset($pri[$k]))) return $cols[$k];
        }
        return null;
    };
    $pair = function (string $base) use ($cols): array {
        foreach ([['', '_copy1'], ['_alt', '_neu'], ['_old', '_new']] as [$sa, $sn]) {
            $a = mb_strtolower($base . $sa);
            $n = mb_strtolower($base . $sn);
            if (isset($cols[$a], $cols[$n])) return [$cols[$a], $cols[$n]];
        }
        return [null, null];
    };

    [$aa, $an] = $pair('Anzahl');
    [$ka, $kn] = $pair('Kurzbeschreibung');

    $m = [
        'table'   => $table,
        'columns' => array_values($cols),
        'rhe'     => $o['rhe'] ?? $pick(['tabelle_räume_has_tabelle_elemente_id', 'rhe_id', 'id_rhe',
                'idtabelle_räume_has_tabelle_elemente', 'raum_has_element_id', 'id'], true),
        'time'    => $o['time'] ?? $pick(['Timestamp', 'Zeitstempel', 'changed_at', 'created_at', 'Datum']),
        'user'    => $o['user'] ?? $pick(['user', 'username', 'Benutzer']),
        'anz_alt' => $o['anz_alt'] ?? $aa,
        'anz_neu' => $o['anz_neu'] ?? $an,
        'kom_alt' => $o['kom_alt'] ?? $ka,
        'kom_neu' => $o['kom_neu'] ?? $kn,
    ];
    if (!$m['kom_alt'] || !$m['kom_neu']) $m['kom_alt'] = $m['kom_neu'] = null; // nur als Paar nutzbar
    $m['ok'] = $m['rhe'] && $m['time'] && $m['anz_alt'] && $m['anz_neu'];
    $m['msg'] = $m['ok'] ? '' : 'Pflichtspalten nicht erkannt (Verweis auf Element-Zeile, Zeitstempel, Anzahl alt/neu).';
    return $m;
}

// ──────────────────────────────────────────────────────────────────
// 0) Element-Zeilen des Projekts + Kommentar-Index
// ──────────────────────────────────────────────────────────────────
$rhe_meta = [];      // rhe_id => Raum/Element-Infos
$comment_idx = [];   // rhe_id => [ {date, idx, bottom, text, action, alt, neu}, … ]

$s = $mysqli->prepare("
    SELECT rhe.id AS rhe_id, rhe.Anzahl, rhe.Timestamp, rhe.Kurzbeschreibung,
           r.idTABELLE_Räume AS raum_id, r.Raumnr, r.Raumbezeichnung, r.Geschoss,
           e.ElementID, e.Bezeichnung AS element_bez, v.Variante
    FROM tabelle_räume_has_tabelle_elemente rhe
    JOIN tabelle_räume r     ON r.idTABELLE_Räume = rhe.TABELLE_Räume_idTABELLE_Räume
    JOIN tabelle_elemente e  ON e.idTABELLE_Elemente = rhe.TABELLE_Elemente_idTABELLE_Elemente
    LEFT JOIN tabelle_varianten v ON v.idtabelle_Varianten = rhe.tabelle_Varianten_idtabelle_Varianten
    WHERE r.tabelle_projekte_idTABELLE_Projekte = ?
");
$s->bind_param('i', $projekt_id);
$s->execute();
$res = $s->get_result();
while ($row = $res->fetch_assoc()) {
    $rid = (int)$row['rhe_id'];
    $kb = (string)$row['Kurzbeschreibung'];
    unset($row['Kurzbeschreibung']);
    $rhe_meta[$rid] = $row;

    if ($kb === '' || stripos($kb, 'Excel') === false) continue;
    $lines = preg_split('/\R/u', trim($kb));
    $last = count($lines) - 1;
    foreach ($lines as $i => $line) {
        if (!preg_match('/^\s*(\d{2})\.(\d{2})\.(\d{4}):\s*(.+)$/u', $line, $m)) continue;
        $text = trim($m[4]);
        if (stripos($text, 'Excel') === false) continue;
        $comment_idx[$rid][] = ['date' => "$m[3]-$m[2]-$m[1]", 'idx' => $i, 'bottom' => $i === $last, 'text' => $text]
            + classify_text($text);
    }
}
$s->close();

// ──────────────────────────────────────────────────────────────────
// 1) Protokoll-Tabellen (nur falls vorhanden)
// ──────────────────────────────────────────────────────────────────
$runs = [];
$protocol_days = [];
$has_protocol = table_exists($mysqli, 'tabelle_import_runs') && table_exists($mysqli, 'tabelle_import_log');

if ($has_protocol) {
    $s = $mysqli->prepare("
        SELECT id, user_name, dateiname, n_add, n_update, n_remove, n_rooms, created_at
        FROM tabelle_import_runs WHERE tabelle_projekte_id = ? ORDER BY created_at DESC
    ");
    $s->bind_param('i', $projekt_id);
    $s->execute();
    foreach ($s->get_result()->fetch_all(MYSQLI_ASSOC) as $r) {
        $key = 'p' . $r['id'];
        $runs[$key] = [
            'key' => $key, 'source' => 'protokoll', 'run_id' => (int)$r['id'],
            'sort' => $r['created_at'], 'title' => fmt_date($r['created_at']),
            'user' => $r['user_name'], 'datei' => $r['dateiname'], 'rows' => null,
            'n_add' => (int)$r['n_add'], 'n_update' => (int)$r['n_update'],
            'n_remove' => (int)$r['n_remove'], 'n_rooms' => (int)$r['n_rooms'], 'n_komm' => 0,
        ];
        $protocol_days[substr($r['created_at'], 0, 10)] = true;
    }
    $s->close();
}

// ──────────────────────────────────────────────────────────────────
// 2) RB-Änderungstabelle → Events → Import-Läufe
// ──────────────────────────────────────────────────────────────────
$chg = detect_change_table($mysqli);
$events = [];
$events_by_rhe = [];

if ($chg['ok']) {
    try {
        $sel = "c." . qi($chg['rhe']) . " AS rhe_id, c." . qi($chg['time']) . " AS ts,"
            . " c." . qi($chg['anz_alt']) . " AS anz_alt, c." . qi($chg['anz_neu']) . " AS anz_neu"
            . ($chg['user'] ? ", c." . qi($chg['user']) . " AS usr" : ", '' AS usr")
            . ($chg['kom_neu'] ? ", c." . qi($chg['kom_alt']) . " AS kom_alt, c." . qi($chg['kom_neu']) . " AS kom_neu" : "");
        $sql = "SELECT $sel
                FROM " . qi($chg['table']) . " c
                JOIN tabelle_räume_has_tabelle_elemente rhe ON rhe.id = c." . qi($chg['rhe']) . "
                JOIN tabelle_räume r ON r.idTABELLE_Räume = rhe.TABELLE_Räume_idTABELLE_Räume
                WHERE r.tabelle_projekte_idTABELLE_Projekte = ?"
            . ($chg['kom_neu'] ? " AND c." . qi($chg['kom_neu']) . " LIKE '%Excel%'" : "")
            . " ORDER BY c." . qi($chg['time']);
        $s = $mysqli->prepare($sql);
        $s->bind_param('i', $projekt_id);
        $s->execute();
        $res = $s->get_result();
        while ($r = $res->fetch_assoc()) {
            $rid = (int)$r['rhe_id'];
            $t = strtotime((string)$r['ts']);
            if (!isset($rhe_meta[$rid]) || !$t) continue;
            $date = date('Y-m-d', $t);
            if (isset($protocol_days[$date])) continue;

            if ($chg['kom_neu']) {
                // Import erkennt man daran, dass oben eine neue "… Excel …"-Zeile dazukam
                $fl = first_line($r['kom_neu']);
                if (stripos($fl, 'Excel') === false || $fl === first_line($r['kom_alt'])) continue;
                $cls = classify_text($fl);
            } else {
                // Ohne Kommentarspalten: nur Änderungen, zu denen ein Import-Kommentar desselben Tages existiert
                $same_day = array_values(array_filter($comment_idx[$rid] ?? [], fn($c) => $c['date'] === $date));
                if (!$same_day) continue;
                $cls = $same_day[0];
            }

            $ev = [
                'rhe_id' => $rid, 't' => $t, 'date' => $date, 'user' => clean_user($r['usr']),
                'action' => $cls['action'],
                'alt'    => $r['anz_alt'] === null ? null : (int)$r['anz_alt'],
                'neu'    => $r['anz_neu'] === null ? null : (int)$r['anz_neu'],
            ];
            $events[] = $ev;
            $events_by_rhe[$rid][] = $ev;
        }
        $s->close();
    } catch (Throwable $e) {
        $chg['ok'] = false;
        $chg['msg'] = 'Abfrage fehlgeschlagen: ' . $e->getMessage();
        $events = $events_by_rhe = [];
    }
}

// Events zu Läufen clustern (gleicher User, Abstand < RUN_GAP_SECONDS)
usort($events, fn($a, $b) => $a['t'] <=> $b['t']);
$clusters = [];
$cur = null;
foreach ($events as $ev) {
    if ($cur === null || $ev['t'] - $cur['t_end'] > RUN_GAP_SECONDS || $ev['user'] !== $cur['user']) {
        if ($cur) $clusters[] = $cur;
        $cur = ['t_start' => $ev['t'], 't_end' => $ev['t'], 'user' => $ev['user'], 'events' => []];
    }
    $cur['events'][] = $ev;
    $cur['t_end'] = $ev['t'];
}
if ($cur) $clusters[] = $cur;

$covered = [];       // rhe_id => [date => true]
$runs_by_date = [];  // date => [run_key, …]

foreach ($clusters as $cl) {
    $key = 'a' . date('YmdHis', $cl['t_start']);
    while (isset($runs[$key])) $key .= 'x';

    // Mehrere Events derselben Zeile im selben Lauf zusammenfassen
    $agg = [];
    foreach ($cl['events'] as $ev) {
        $rid = $ev['rhe_id'];
        $covered[$rid][$ev['date']] = true;
        if (!isset($agg[$rid])) {
            $agg[$rid] = ['first' => $ev, 'last' => $ev, 'add' => $ev['action'] === 'add'];
        } else {
            $agg[$rid]['last'] = $ev;
            $agg[$rid]['add'] = $agg[$rid]['add'] || $ev['action'] === 'add';
        }
    }
    $rows = [];
    foreach ($agg as $rid => $a) {
        $action = $a['add'] ? 'add' : $a['last']['action'];
        $rows[] = make_row($rhe_meta[$rid], $action, $a['first']['alt'], $a['last']['neu'], '', 'aend');
    }

    $span = $cl['t_end'] - $cl['t_start'];
    $runs[$key] = [
        'key' => $key, 'source' => 'aenderung', 'sort' => date('Y-m-d H:i:s', $cl['t_start']),
        'title' => date('d.m.Y H:i', $cl['t_start']) . ($span >= 60 ? '–' . date('H:i', $cl['t_end']) : ''),
        'user' => $cl['user'], 'datei' => '', 'rows' => $rows, 't_start' => $cl['t_start'],
    ];
    $runs_by_date[date('Y-m-d', $cl['t_start'])][] = $key;
}

// ──────────────────────────────────────────────────────────────────
// 3) Kommentare: alles ergänzen, was im Änderungsprotokoll fehlt
// ──────────────────────────────────────────────────────────────────
$legacy = []; // date => rows[]

foreach ($comment_idx as $rid => $entries) {
    $meta = $rhe_meta[$rid];
    foreach ($entries as $en) {
        $date = $en['date'];
        if (isset($protocol_days[$date]) || isset($covered[$rid][$date])) continue;

        $alt = $en['alt'];
        $neu = $en['neu'];
        if ($en['action'] === 'add') {
            if ($en['bottom']) $alt = 0; // unterste Kommentarzeile → Zeile wurde vom Import angelegt
            if ($en['idx'] === 0) {
                $neu = (int)$meta['Anzahl']; // jüngste Zeile → aktueller Stand stammt aus diesem Import
            } else {
                foreach ($events_by_rhe[$rid] ?? [] as $ev) { // späterer Import kennt den Vorwert
                    if ($ev['date'] > $date && $ev['alt'] !== null) { $neu = $ev['alt']; break; }
                }
            }
        }
        $row = make_row($meta, $en['action'], $alt, $neu, 'aus Kommentar', 'komm');

        $cands = $runs_by_date[$date] ?? [];
        if (!$cands) {
            $legacy[$date][] = $row;
            continue;
        }
        // Passenden Lauf am selben Tag wählen: gleicher Raum > nächster zum Zeilen-Timestamp > letzter
        $target = null;
        foreach ($cands as $k) {
            foreach ($runs[$k]['rows'] as $rr) {
                if ($rr['raum_id'] === $row['raum_id']) { $target = $k; break 2; }
            }
        }
        if (!$target) {
            $ts = strtotime((string)$meta['Timestamp']);
            if ($ts && date('Y-m-d', $ts) === $date) {
                $best = PHP_INT_MAX;
                foreach ($cands as $k) {
                    $d = abs($runs[$k]['t_start'] - $ts);
                    if ($d < $best) { $best = $d; $target = $k; }
                }
            }
        }
        $runs[$target ?? end($cands)]['rows'][] = $row;
    }
}

foreach ($legacy as $date => $rows) {
    $key = 'k' . $date;
    $runs[$key] = [
        'key' => $key, 'source' => 'kommentar', 'sort' => $date . ' 00:00:00',
        'title' => fmt_date($date, false), 'user' => '', 'datei' => '', 'rows' => $rows,
    ];
}

// Kennzahlen für alle nicht-Protokoll-Läufe
foreach ($runs as &$run) {
    if ($run['source'] === 'protokoll') continue;
    $cnt = array_count_values(array_column($run['rows'], 'action'));
    $src = array_count_values(array_column($run['rows'], 'src'));
    $run['n_add'] = $cnt['add'] ?? 0;
    $run['n_update'] = $cnt['update'] ?? 0;
    $run['n_remove'] = $cnt['remove'] ?? 0;
    $run['n_rooms'] = count(array_unique(array_column($run['rows'], 'raum_id')));
    $run['n_komm'] = $src['komm'] ?? 0;
}
unset($run);

uasort($runs, fn($a, $b) => strcmp($b['sort'], $a['sort']));

// ──────────────────────────────────────────────────────────────────
// Ausgewählter Import → Detailzeilen
// ──────────────────────────────────────────────────────────────────
$sel_key = $_GET['run'] ?? array_key_first($runs);
$sel = $runs[$sel_key] ?? null;
$rows = [];

if ($sel && $sel['source'] === 'protokoll') {
    $s = $mysqli->prepare("
        SELECT l.action, l.raum_id, l.anzahl_alt, l.anzahl_neu, l.details,
               r.Raumnr, r.Raumbezeichnung, r.Geschoss,
               e.ElementID, e.Bezeichnung AS element_bez, v.Variante
        FROM tabelle_import_log l
        LEFT JOIN tabelle_räume r     ON r.idTABELLE_Räume = l.raum_id
        LEFT JOIN tabelle_elemente e  ON e.idTABELLE_Elemente = l.element_id
        LEFT JOIN tabelle_varianten v ON v.idtabelle_Varianten = l.variante_id
        WHERE l.run_id = ? ORDER BY l.id
    ");
    $s->bind_param('i', $sel['run_id']);
    $s->execute();
    foreach ($s->get_result()->fetch_all(MYSQLI_ASSOC) as $r) {
        $d = json_decode((string)$r['details'], true) ?: [];
        $info = [];
        if (!empty($d['variante_neu_angelegt'])) $info[] = 'neue Variante angelegt';
        if (!empty($d['warning'])) $info[] = $d['warning'];
        $r['raum_id'] = (int)$r['raum_id'];
        $rows[] = make_row($r, $r['action'],
            $r['anzahl_alt'] === null ? null : (int)$r['anzahl_alt'],
            $r['anzahl_neu'] === null ? null : (int)$r['anzahl_neu'],
            implode(' · ', $info), 'prot');
    }
    $s->close();
} elseif ($sel) {
    $rows = $sel['rows'];
}

// Gruppierung nach Raum
$by_room = [];
foreach ($rows as $r) {
    $rid = $r['raum_id'];
    $by_room[$rid] ??= ['nr' => $r['raumnr'], 'bez' => $r['raumbez'], 'geschoss' => $r['geschoss'],
        'rows' => [], 'add' => 0, 'update' => 0, 'remove' => 0];
    $by_room[$rid]['rows'][] = $r;
    $by_room[$rid][$r['action']]++;
}
uasort($by_room, fn($a, $b) => strnatcasecmp((string)$a['nr'], (string)$b['nr']));

// Gruppierung nach Element + Variante
$by_el = [];
foreach ($rows as $r) {
    $k = $r['element'] . '|' . $r['variante'];
    $by_el[$k] ??= ['element' => $r['element'], 'bez' => $r['element_bez'], 'variante' => $r['variante'],
        'rooms' => [], 'add' => 0, 'update' => 0, 'remove' => 0, 'delta' => 0, 'unknown' => 0];
    $by_el[$k]['rooms'][$r['raum_id']] = true;
    $by_el[$k][$r['action']]++;
    $d = delta_of($r);
    if ($d === null) $by_el[$k]['unknown']++; else $by_el[$k]['delta'] += $d;
}
uasort($by_el, fn($a, $b) => strnatcasecmp($a['element'] . $a['variante'], $b['element'] . $b['variante']));

$total_delta = 0;
$total_unknown = 0;
foreach ($rows as $r) {
    $d = delta_of($r);
    if ($d === null) $total_unknown++; else $total_delta += $d;
}

$n_runs_aend = count(array_filter($runs, fn($r) => $r['source'] === 'aenderung'));
$n_runs_komm = count(array_filter($runs, fn($r) => $r['source'] === 'kommentar'));
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>RB-Projekte – Import-Historie</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="stylesheet" href="../_utils/style.css" type="text/css" media="screen"/>
    <link rel="icon" href="../Logos/iphone_favicon.png"/>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css"
          integrity="sha512-q3eWabyZPc1XTCmF+8/LuE1ozpg5xxn7iO89yfSOd5/oKvyqLngoNGsx8jq92Y8eXJ/IRxQbEC+FGSYxtk2oiw=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<div id="limet-navbar"></div>
<div class="container-fluid">
    <div class="row mt-2">

        <!-- ══ Linke Spalte: Liste der Imports ══ -->
        <div class="col-xl-3 col-lg-4 mb-2">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="fw-semibold"><i class="fas fa-history me-1"></i>Imports
                        <span class="badge bg-secondary"><?= count($runs) ?></span></span>
                    <a href="import_excel.php" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-file-import me-1"></i>Neuer Import
                    </a>
                </div>

                <?php if (!$runs): ?>
                    <div class="card-body text-muted small">Für dieses Projekt wurden noch keine Imports gefunden.</div>
                <?php else: ?>
                    <div class="list-group list-group-flush overflow-auto" style="max-height: 75vh">
                        <?php foreach ($runs as $run):
                            $active = $sel && $run['key'] === $sel['key']; ?>
                            <a href="?run=<?= h($run['key']) ?>"
                               class="list-group-item list-group-item-action<?= $active ? ' active' : '' ?>">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-semibold"><?= h($run['title']) ?></span>
                                    <?php if ($run['source'] === 'kommentar'): ?>
                                        <span class="badge bg-secondary" title="nur aus Kommentaren rekonstruiert">Kommentar</span>
                                    <?php elseif ($run['source'] === 'protokoll'): ?>
                                        <span class="badge bg-info text-dark" title="exaktes Import-Protokoll">Protokoll</span>
                                    <?php endif; ?>
                                </div>
                                <div class="small<?= $active ? '' : ' text-muted' ?>">
                                    <?= $run['n_rooms'] ?> Räume
                                    <?= $run['user'] !== '' ? ' · <i class="fas fa-user mx-1"></i>' . h($run['user']) : '' ?>
                                    <?= $run['datei'] !== '' ? '<br><i class="fas fa-file-excel me-1"></i>' . h($run['datei']) : '' ?>
                                </div>
                                <div class="mt-1"><?= count_badges($run['n_add'], $run['n_update'], $run['n_remove']) ?></div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Datenquellen / Diagnose -->
                <div class="card-footer small">
                    <a class="text-muted text-decoration-none" data-bs-toggle="collapse" href="#src-info">
                        <i class="fas fa-database me-1"></i>Datenquellen
                        <?php if (!$chg['ok']): ?><i class="fas fa-exclamation-triangle text-warning ms-1"></i><?php endif; ?>
                    </a>
                    <div class="collapse<?= $chg['ok'] ? '' : ' show' ?>" id="src-info">
                        <ul class="list-unstyled mt-2 mb-0 text-muted">
                            <li class="mb-2">
                                <span class="fw-semibold text-body">RB-Änderungstabelle:</span>
                                <?php if ($chg['ok']): ?>
                                    <code><?= h($chg['table']) ?></code> ✓<br>
                                    Element-Zeile <code><?= h($chg['rhe']) ?></code>,
                                                                                    Zeit <code><?= h($chg['time']) ?></code>,
                                                                                                                            User <code><?= h($chg['user'] ?? '–') ?></code>,
                                                                                                                                                                           Anzahl <code><?= h($chg['anz_alt']) ?></code>→<code><?= h($chg['anz_neu']) ?></code>,
                                                                                                                                                                                                                                                               Kommentar <?= $chg['kom_neu']
                                        ? '<code>' . h($chg['kom_alt']) . '</code>→<code>' . h($chg['kom_neu']) . '</code>'
                                        : '<span class="text-warning">nicht gefunden</span>' ?><br>
                                    <?= count($events) ?> Import-Änderungen → <?= $n_runs_aend ?> Imports
                                <?php else: ?>
                                    <span class="text-warning"><?= h($chg['msg']) ?></span>
                                    <?php if (!empty($chg['table'])): ?>
                                        <br>Tabelle: <code><?= h($chg['table']) ?></code>
                                    <?php endif; ?>
                                    <?php if (!empty($chg['columns'])): ?>
                                        <br>Spalten: <code><?= h(implode(', ', $chg['columns'])) ?></code>
                                    <?php endif; ?>
                                    <br>→ ggf. <code>CHANGE_TABLE_OVERRIDE</code> in <code>import_history.php</code> setzen.
                                <?php endif; ?>
                            </li>
                            <li class="mb-2">
                                <span class="fw-semibold text-body">Elementkommentare:</span>
                                <?= count($comment_idx) ?> Element-Zeilen mit Import-Kommentar<?= $n_runs_komm ? ', ' . $n_runs_komm . ' Tage nur daraus' : '' ?>
                            </li>
                            <li>
                                <span class="fw-semibold text-body">Import-Protokoll-Tabelle:</span>
                                <?= $has_protocol ? 'vorhanden ✓' : 'nicht angelegt (optional)' ?>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- ══ Rechte Spalte: Details ══ -->
        <div class="col-xl-9 col-lg-8">
            <?php if (!$sel): ?>
                <div class="alert alert-light border">Links einen Import auswählen.</div>
            <?php else: ?>

                <div class="card mb-2">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <span class="fw-semibold">Import vom <?= h($sel['title']) ?></span>
                            <?php if ($sel['user'] !== ''): ?>
                                <span class="text-muted ms-2"><i class="fas fa-user me-1"></i><?= h($sel['user']) ?></span>
                            <?php endif; ?>
                            <?php if ($sel['datei'] !== ''): ?>
                                <span class="text-muted ms-2"><i class="fas fa-file-excel me-1"></i><?= h($sel['datei']) ?></span>
                            <?php endif; ?>
                        </div>
                        <button class="btn btn-outline-secondary btn-sm" id="btn-csv">
                            <i class="fas fa-download me-1"></i>CSV
                        </button>
                    </div>

                    <?php if ($sel['source'] === 'aenderung'): ?>
                        <div class="alert alert-light small rounded-0 border-0 border-bottom mb-0 py-2">
                            <i class="fas fa-info-circle me-1"></i>
                            Aus dem RB-Änderungsprotokoll: alle Import-Änderungen desselben Users mit weniger als
                            <?= RUN_GAP_SECONDS / 60 ?> Minuten Abstand gelten als ein Import.
                            <?php if ($sel['n_komm']): ?>
                                <br><?= $sel['n_komm'] ?> Zeile(n) mit <span class="badge bg-light text-muted border">aus Kommentar</span>
                                fehlen im Änderungsprotokoll (z.B. neu angelegte Elemente) und wurden über die
                                Elementkommentare desselben Tages zugeordnet.
                            <?php endif; ?>
                        </div>
                    <?php elseif ($sel['source'] === 'kommentar'): ?>
                        <div class="alert alert-secondary small rounded-0 border-0 border-bottom mb-0 py-2">
                            <i class="fas fa-info-circle me-1"></i>
                            Für diesen Tag gibt es keine Einträge im Änderungsprotokoll – nur aus den Elementkommentaren
                            rekonstruiert. Mehrere Imports am selben Tag erscheinen gemeinsam, Vorwerte sind teils
                            unbekannt (<span class="text-muted">?</span>).
                        </div>
                    <?php endif; ?>

                    <div class="card-body">
                        <div class="row g-2 text-center">
                            <div class="col-6 col-md">
                                <div class="border rounded p-2">
                                    <div class="fs-4 fw-semibold"><?= count($by_room) ?></div>
                                    <div class="small text-muted">Räume betroffen</div>
                                </div>
                            </div>
                            <div class="col-6 col-md">
                                <div class="border border-success rounded p-2">
                                    <div class="fs-4 fw-semibold text-success"><?= $sel['n_add'] ?></div>
                                    <div class="small text-muted">hinzugefügt</div>
                                </div>
                            </div>
                            <div class="col-6 col-md">
                                <div class="border border-warning rounded p-2">
                                    <div class="fs-4 fw-semibold text-warning"><?= $sel['n_update'] ?></div>
                                    <div class="small text-muted">Anzahl geändert</div>
                                </div>
                            </div>
                            <div class="col-6 col-md">
                                <div class="border border-danger rounded p-2">
                                    <div class="fs-4 fw-semibold text-danger"><?= $sel['n_remove'] ?></div>
                                    <div class="small text-muted">entfernt</div>
                                </div>
                            </div>
                            <div class="col-12 col-md">
                                <div class="border rounded p-2">
                                    <div class="fs-4"><?= render_delta($total_delta) ?></div>
                                    <div class="small text-muted">
                                        Stück netto<?= $total_unknown ? ' (' . $total_unknown . ' ohne Vorwert)' : '' ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header pb-0">
                        <div class="d-flex justify-content-between align-items-end flex-wrap gap-2">
                            <ul class="nav nav-tabs card-header-tabs" role="tablist">
                                <li class="nav-item">
                                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-room" type="button">
                                        <i class="fas fa-door-open me-1"></i>Nach Raum
                                        <span class="badge bg-secondary"><?= count($by_room) ?></span>
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-el" type="button">
                                        <i class="fas fa-cubes me-1"></i>Nach Element
                                        <span class="badge bg-secondary"><?= count($by_el) ?></span>
                                    </button>
                                </li>
                            </ul>
                            <div class="d-flex gap-2 align-items-center mb-2 flex-wrap">
                                <div class="btn-group btn-group-sm" role="group">
                                    <input type="radio" class="btn-check" name="act-filter" id="af-all" value="all" checked>
                                    <label class="btn btn-outline-secondary" for="af-all">Alle</label>
                                    <input type="radio" class="btn-check" name="act-filter" id="af-add" value="add">
                                    <label class="btn btn-outline-success" for="af-add" title="hinzugefügt"><i class="fas fa-plus"></i></label>
                                    <input type="radio" class="btn-check" name="act-filter" id="af-update" value="update">
                                    <label class="btn btn-outline-warning" for="af-update" title="Anzahl geändert"><i class="fas fa-exchange-alt"></i></label>
                                    <input type="radio" class="btn-check" name="act-filter" id="af-remove" value="remove">
                                    <label class="btn btn-outline-danger" for="af-remove" title="entfernt"><i class="fas fa-minus"></i></label>
                                </div>
                                <input type="search" id="hist-filter" class="form-control form-control-sm w-auto"
                                       placeholder="Raum / Element suchen …">
                                <button class="btn btn-outline-secondary btn-sm" id="btn-expand" title="Alle auf-/zuklappen">
                                    <i class="fas fa-expand-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="tab-content">
                        <!-- ── Tab: Nach Raum ── -->
                        <div class="tab-pane fade show active" id="tab-room">
                            <?php if (!$by_room): ?>
                                <div class="p-3 text-muted small">Keine Änderungen in diesem Import.</div>
                            <?php endif; ?>
                            <div class="accordion accordion-flush" id="acc-rooms">
                                <?php foreach ($by_room as $rid => $room): ?>
                                    <div class="accordion-item" data-room-item>
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed py-2" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#room-<?= $rid ?>">
                                                <span class="fw-semibold me-2"><?= h($room['nr']) ?></span>
                                                <span class="text-muted me-2"><?= h($room['bez']) ?></span>
                                                <?php if ($room['geschoss'] !== null && $room['geschoss'] !== ''): ?>
                                                    <span class="badge bg-light text-dark border me-3"><?= h($room['geschoss']) ?></span>
                                                <?php endif; ?>
                                                <span class="ms-auto me-2"><?= count_badges($room['add'], $room['update'], $room['remove']) ?></span>
                                            </button>
                                        </h2>
                                        <div id="room-<?= $rid ?>" class="accordion-collapse collapse">
                                            <div class="accordion-body p-0">
                                                <table class="table table-sm table-hover align-middle mb-0">
                                                    <thead class="table-light">
                                                    <tr>
                                                        <th class="ps-3">Aktion</th>
                                                        <th>Element</th>
                                                        <th>Bezeichnung</th>
                                                        <th class="text-center">Var.</th>
                                                        <th class="text-center">Anzahl</th>
                                                        <th class="text-center">Δ</th>
                                                        <th>Info</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php foreach ($room['rows'] as $r):
                                                        $search = mb_strtolower($room['nr'] . ' ' . $room['bez'] . ' ' . $r['element'] . ' ' . $r['element_bez']); ?>
                                                        <tr data-hist-row data-action="<?= h($r['action']) ?>" data-search="<?= h($search) ?>">
                                                            <td class="ps-3"><?= action_badge($r['action']) ?></td>
                                                            <td><code><?= h($r['element']) ?></code></td>
                                                            <td><?= h($r['element_bez']) ?></td>
                                                            <td class="text-center"><?= h($r['variante']) ?></td>
                                                            <td class="text-center text-nowrap"><?= render_change($r['alt'], $r['neu']) ?></td>
                                                            <td class="text-center"><?= render_delta(delta_of($r)) ?></td>
                                                            <td class="small text-muted">
                                                                <?php if ($r['src'] === 'komm' && $sel['source'] !== 'kommentar'): ?>
                                                                    <span class="badge bg-light text-muted border">aus Kommentar</span>
                                                                <?php elseif ($r['info'] !== '' && $r['src'] !== 'komm'): ?>
                                                                    <?= h($r['info']) ?>
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- ── Tab: Nach Element ── -->
                        <div class="tab-pane fade" id="tab-el">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover align-middle mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">Element</th>
                                        <th>Bezeichnung</th>
                                        <th class="text-center">Var.</th>
                                        <th class="text-center">Räume</th>
                                        <th>Aktionen</th>
                                        <th class="text-center">Δ Stück</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($by_el as $el):
                                        $acts = array_keys(array_filter(['add' => $el['add'], 'update' => $el['update'], 'remove' => $el['remove']]));
                                        $search = mb_strtolower($el['element'] . ' ' . $el['bez']); ?>
                                        <tr data-hist-row data-action="<?= h(implode(' ', $acts)) ?>" data-search="<?= h($search) ?>">
                                            <td class="ps-3"><code><?= h($el['element']) ?></code></td>
                                            <td><?= h($el['bez']) ?></td>
                                            <td class="text-center"><?= h($el['variante']) ?></td>
                                            <td class="text-center"><?= count($el['rooms']) ?></td>
                                            <td><?= count_badges($el['add'], $el['update'], $el['remove']) ?></td>
                                            <td class="text-center">
                                                <?= render_delta($el['delta']) ?>
                                                <?php if ($el['unknown']): ?>
                                                    <span class="text-muted small" title="Einträge ohne bekannte Vor-Anzahl">(+<?= $el['unknown'] ?> ?)</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    const HIST_ROWS = <?= json_encode($rows, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) ?>;
    const HIST_TITLE = <?= json_encode($sel['title'] ?? '', JSON_UNESCAPED_UNICODE) ?>;

    // ── Filter (Aktion + Freitext) ─────────────────────────────────
    function applyHistFilter() {
        const q = (document.getElementById('hist-filter')?.value || '').trim().toLowerCase();
        const act = document.querySelector('input[name="act-filter"]:checked')?.value || 'all';

        document.querySelectorAll('[data-hist-row]').forEach(tr => {
            const okAct = act === 'all' || tr.dataset.action.split(' ').includes(act);
            const okTxt = q === '' || tr.dataset.search.includes(q);
            tr.classList.toggle('d-none', !(okAct && okTxt));
        });

        document.querySelectorAll('[data-room-item]').forEach(item => {
            const visible = item.querySelector('[data-hist-row]:not(.d-none)') !== null;
            item.classList.toggle('d-none', !visible);
            const col = item.querySelector('.accordion-collapse');
            if (visible && (q !== '' || act !== 'all')) {
                bootstrap.Collapse.getOrCreateInstance(col, {toggle: false}).show();
            }
        });
    }

    document.getElementById('hist-filter')?.addEventListener('input', applyHistFilter);
    document.querySelectorAll('input[name="act-filter"]').forEach(r => r.addEventListener('change', applyHistFilter));

    // ── Alle auf-/zuklappen ────────────────────────────────────────
    let histExpanded = false;
    document.getElementById('btn-expand')?.addEventListener('click', () => {
        histExpanded = !histExpanded;
        document.querySelectorAll('#acc-rooms [data-room-item]:not(.d-none) .accordion-collapse').forEach(col => {
            const c = bootstrap.Collapse.getOrCreateInstance(col, {toggle: false});
            histExpanded ? c.show() : c.hide();
        });
    });

    // ── CSV-Export des gewählten Imports ───────────────────────────
    document.getElementById('btn-csv')?.addEventListener('click', () => {
        const SRC = {aend: 'Änderungsprotokoll', komm: 'Kommentar', prot: 'Import-Protokoll'};
        const cols = ['action', 'raumnr', 'raumbez', 'geschoss', 'element', 'element_bez', 'variante', 'alt', 'neu', 'src', 'info'];
        const head = ['Aktion', 'Raumnr', 'Raumbezeichnung', 'Geschoss', 'Element', 'Bezeichnung', 'Variante', 'Anzahl alt', 'Anzahl neu', 'Quelle', 'Info'];
        const cell = v => {
            const s = v === null || v === undefined ? '' : String(v);
            return /[;"\n]/.test(s) ? '"' + s.replace(/"/g, '""') + '"' : s;
        };
        const lines = [head.join(';')].concat(HIST_ROWS.map(r =>
            cols.map(c => cell(c === 'src' ? (SRC[r.src] || r.src) : r[c])).join(';')));
        const blob = new Blob(['﻿' + lines.join('\r\n')], {type: 'text/csv;charset=utf-8'});
        const a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'Import_' + HIST_TITLE.replace(/[^\d]/g, '') + '.csv';
        a.click();
        URL.revokeObjectURL(a.href);
    });
</script>
</body>
</html>