<?php
/**
 * api_sync_elements.php
 * ══════════════════════════════════════════════════════════════════
 * Übernimmt Änderungen aus dem Excel-Abgleich in die DB.
 * Alle Aktionen laufen in einer Transaktion → entweder alles oder nichts.
 *
 * POST-Body (JSON):
 *   actions  array  – [{type: 'add'|'update'|'remove', ...}]
 *
 * Response (JSON):
 *   ok       bool
 *   total    int
 *   success  int
 *   errors   int
 *   results  array  – Detail-Ergebnis je Aktion
 * ══════════════════════════════════════════════════════════════════
 */

ob_start();
if (!function_exists('utils_connect_sql')) {
    include "../utils/_utils.php";
}
check_login();
ob_clean();
header('Content-Type: application/json; charset=utf-8');

// Konfiguration (für Varianten-Anlage brauchen wir mz_config nicht direkt,
// aber consistent include schadet nicht)
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
 * Sucht eine Variante deren Parameter (für die übergebenen IDs) mit $params übereinstimmen,
 * oder legt Parameter unter der nächsten freien Variante an.
 *
 * Match-Logik: Nur die in $params enthaltenen Parameter-IDs werden verglichen.
 * Zusätzliche DB-Parameter werden ignoriert (konsistent mit params_match() im Compare).
 *
 * tabelle_varianten wird NIEMALS beschrieben — nur gelesen.
 */
function get_or_create_variante(mysqli $db, int $projekt_id, int $planungsphase, int $element_id, array $params): int
{
    // Bestehende Varianten für dieses Element+Projekt
    $stmt = $db->prepare("
        SELECT DISTINCT tabelle_Varianten_idtabelle_Varianten AS vid
        FROM tabelle_projekt_elementparameter
        WHERE tabelle_projekte_idTABELLE_Projekte = ?
          AND tabelle_elemente_idTABELLE_Elemente = ?
        ORDER BY vid
    ");
    $stmt->bind_param('ii', $projekt_id, $element_id);
    $stmt->execute();
    $existing_vids = array_column($stmt->get_result()->fetch_all(MYSQLI_ASSOC), 'vid');
    $stmt->close();

    // Passende Variante suchen
    foreach ($existing_vids as $vid) {
        $s2 = $db->prepare("
            SELECT tabelle_parameter_idTABELLE_Parameter AS pid, Wert
            FROM tabelle_projekt_elementparameter
            WHERE tabelle_projekte_idTABELLE_Projekte = ?
              AND tabelle_elemente_idTABELLE_Elemente = ?
              AND tabelle_Varianten_idtabelle_Varianten = ?
        ");
        $s2->bind_param('iii', $projekt_id, $element_id, $vid);
        $s2->execute();
        $db_params = array_column($s2->get_result()->fetch_all(MYSQLI_ASSOC), 'Wert', 'pid');
        $s2->close();

        $match = true;
        foreach ($params as $pid => $info) {
            if ((string)($db_params[$pid] ?? null) !== (string)$info['wert']) { $match = false; break; }
        }
        if ($match) return (int)$vid;
    }

    // Nächste freie Variante aus tabelle_varianten (kein INSERT!)
    $used = array_flip($existing_vids);
    $all  = $db->query("SELECT idtabelle_Varianten AS id FROM tabelle_varianten ORDER BY idtabelle_Varianten");
    $new_vid = null;
    foreach ($all->fetch_all(MYSQLI_ASSOC) as $row) {
        if (!isset($used[(int)$row['id']])) { $new_vid = (int)$row['id']; break; }
    }
    if (!$new_vid) {
        throw new \RuntimeException("Keine freie Variante für Element $element_id in Projekt $projekt_id.");
    }

    // Parameter schreiben
    foreach ($params as $pid => $info) {
        $wert    = $db->real_escape_string((string)$info['wert']);
        $einheit = $db->real_escape_string((string)($info['einheit'] ?? ''));
        $db->query("
            INSERT INTO tabelle_projekt_elementparameter
                (tabelle_projekte_idTABELLE_Projekte, tabelle_elemente_idTABELLE_Elemente,
                 tabelle_parameter_idTABELLE_Parameter, tabelle_Varianten_idtabelle_Varianten,
                 Wert, Einheit, tabelle_planungsphasen_idTABELLE_Planungsphasen)
            VALUES ($projekt_id, $element_id, $pid, $new_vid, '$wert', '$einheit', $planungsphase)
        ");
    }
    return $new_vid;
}


function audit_log(mysqli $db, int $projekt_id, int $user_id, string $action, array $details): void
{/**
 * Schreibt einen Audit-Log-Eintrag. TODO Tabelle in DB anlegen
 * Scheitert die Tabelle, bricht das nicht den Sync ab.
 */
    // $stmt = $db->prepare("
    //     INSERT IGNORE INTO tabelle_import_log
    //         (tabelle_projekte_id, user_id, action, details, created_at)
    //     VALUES (?, ?, ?, ?, NOW())
    // ");
    // if (!$stmt) return; // Tabelle existiert noch nicht → still ignorieren
    // $details_json = json_encode($details, JSON_UNESCAPED_UNICODE);
    // $stmt->bind_param('iiss', $projekt_id, $user_id, $action, $details_json);
    // $stmt->execute();
    // $stmt->close();
}

// ──────────────────────────────────────────────────────────────────
// Request validieren
// ──────────────────────────────────────────────────────────────────

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_error('Method not allowed', 405);
}
$body = json_decode(file_get_contents('php://input'), true);
if (empty($body['actions']) || !is_array($body['actions'])) {
    json_error('actions fehlt');
}
if (!isset($_SESSION['projectID'])) {
    json_error('Kein Projekt in Session');
}

$projekt_id = (int)$_SESSION['projectID'];
$pp = $_SESSION['projectPlanungsphase'] ?? '1';
$planungsphase = is_numeric($pp) ? (int)$pp : match (trim($pp)) {
    'Vorentwurf' => 1,
    'Entwurf' => 2,
    'Ausführungsplanung' => 3,
    'Einreichung' => 4,
    default => 0
};
if ($planungsphase <= 0) {
    json_error('Planungsphase unbekannt oder nicht gesetzt: ' . htmlspecialchars($pp));
}


$user_id = (int)($_SESSION['userID'] ?? 0);
$mysqli = utils_connect_sql();

// ──────────────────────────────────────────────────────────────────
// Sicherheitscheck: alle rhe_ids und raum_ids müssen zum Projekt gehören
// ──────────────────────────────────────────────────────────────────
$rhe_ids_to_check = [];
$raum_ids_to_check = [];
foreach ($body['actions'] as $action) {
    if (isset($action['rhe_id'])) $rhe_ids_to_check[] = (int)$action['rhe_id'];
    if (isset($action['raum_id'])) $raum_ids_to_check[] = (int)$action['raum_id'];
}
if (!empty($rhe_ids_to_check)) {
    $ph = implode(',', array_fill(0, count($rhe_ids_to_check), '?'));
    $types = str_repeat('i', count($rhe_ids_to_check)) . 'i';
    $vals = array_merge($rhe_ids_to_check, [$projekt_id]);
    $refs = [&$types];
    foreach ($vals as $k => $_) $refs[] = &$vals[$k];
    $chk = $mysqli->prepare("
        SELECT COUNT(*) AS cnt FROM tabelle_räume_has_tabelle_elemente rhe
        JOIN tabelle_räume r ON r.idTABELLE_Räume = rhe.TABELLE_Räume_idTABELLE_Räume
        WHERE rhe.id IN ($ph) AND r.tabelle_projekte_idTABELLE_Projekte = ?
    ");
    call_user_func_array([$chk, 'bind_param'], $refs);
    $chk->execute();
    $cnt = (int)$chk->get_result()->fetch_row()[0];
    $chk->close();
    if ($cnt !== count($rhe_ids_to_check)) {
        json_error('Mindestens eine rhe_id gehört nicht zum aktuellen Projekt', 403);
    }
}
if (!empty($raum_ids_to_check)) {
    $ph = implode(',', array_fill(0, count($raum_ids_to_check), '?'));
    $types = str_repeat('i', count($raum_ids_to_check)) . 'i';
    $vals = array_merge($raum_ids_to_check, [$projekt_id]);
    $refs = [&$types];
    foreach ($vals as $k => $_) $refs[] = &$vals[$k];
    $chk = $mysqli->prepare("
        SELECT COUNT(*) AS cnt FROM tabelle_räume
        WHERE idTABELLE_Räume IN ($ph) AND tabelle_projekte_idTABELLE_Projekte = ?
    ");
    call_user_func_array([$chk, 'bind_param'], $refs);
    $chk->execute();
    $cnt = (int)$chk->get_result()->fetch_row()[0];
    $chk->close();
    if ($cnt !== count(array_unique($raum_ids_to_check))) {
        json_error('Mindestens eine raum_id gehört nicht zum aktuellen Projekt', 403);
    }
}

// ──────────────────────────────────────────────────────────────────
// Transaktion starten — alle Aktionen als atomare Einheit
// ──────────────────────────────────────────────────────────────────
$mysqli->begin_transaction();

$results = [];
$had_error = false;

try {
    foreach ($body['actions'] as $action) {
        $type = $action['type'] ?? '';

        // ── REMOVE ────────────────────────────────────────────────
        if ($type === 'remove') {
            $rhe_id = (int)$action['rhe_id'];
            $kommentar = trim($action['kommentar'] ?? 'Entfernt via Excel-Abgleich');
            $ts = date('Y-m-d H:i:s');

            $stmt = $mysqli->prepare("
                UPDATE tabelle_räume_has_tabelle_elemente
                SET Anzahl = 0, Kurzbeschreibung = ?, Timestamp = ?
                WHERE id = ?
            ");
            $stmt->bind_param('ssi', $kommentar, $ts, $rhe_id);
            $stmt->execute();
            $ok = $stmt->affected_rows >= 0;
            $stmt->close();

            audit_log($mysqli, $projekt_id, $user_id, 'remove', ['rhe_id' => $rhe_id, 'kommentar' => $kommentar]);
            $results[] = ['type' => 'remove', 'rhe_id' => $rhe_id, 'ok' => $ok];

            // ── UPDATE ANZAHL ──────────────────────────────────────────
        } elseif ($type === 'update') {
            $rhe_id = (int)$action['rhe_id'];
            $anzahl = max(0, (int)$action['anzahl']);
            $ts = date('Y-m-d H:i:s');

            $stmt = $mysqli->prepare("
                UPDATE tabelle_räume_has_tabelle_elemente
                SET Anzahl = ?, Timestamp = ?
                WHERE id = ?
            ");
            $stmt->bind_param('isi', $anzahl, $ts, $rhe_id);
            $stmt->execute();
            $ok = $stmt->affected_rows >= 0;
            $stmt->close();

            audit_log($mysqli, $projekt_id, $user_id, 'update', ['rhe_id' => $rhe_id, 'anzahl' => $anzahl]);
            $results[] = ['type' => 'update', 'rhe_id' => $rhe_id, 'anzahl' => $anzahl, 'ok' => $ok];

            // ── ADD (mit Variante + Parameter) ─────────────────────────
        } elseif ($type === 'add') {
            $raum_id = (int)$action['raum_id'];
            $anzahl = max(1, (int)($action['anzahl'] ?? 1));
            $params = $action['params'] ?? [];

            // element_id auflösen (direkt oder per ElementID-Code)
            $element_id = (int)($action['element_id'] ?? 0);
            if (!$element_id && !empty($action['element_code'])) {
                $ec = $mysqli->real_escape_string(trim($action['element_code']));
                $r = $mysqli->query("SELECT idTABELLE_Elemente FROM tabelle_elemente WHERE ElementID='$ec' LIMIT 1");
                if ($r && $row = $r->fetch_assoc()) $element_id = (int)$row['idTABELLE_Elemente'];
            }
            if (!$element_id) {
                throw new RuntimeException('Element nicht gefunden: ' . ($action['element_code'] ?? '?'));
            }

            // Variante bestimmen oder neu anlegen
            $variante_id = (int)($action['variante_id'] ?? 0);
            if (!$variante_id) {
                $params_typed = [];
                foreach ($params as $pid => $info) {
                    $params_typed[(int)$pid] = is_array($info)
                        ? $info
                        : ['wert' => (string)$info, 'einheit' => ''];
                }
                $variante_id = get_or_create_variante($mysqli, $projekt_id, $planungsphase, $element_id, $params_typed);
            }

            $ts = date('Y-m-d H:i:s');
            $kommentar = 'Hinzugefügt via Excel-Import ' . date('d.m.Y');

            $stmt = $mysqli->prepare("
                INSERT INTO tabelle_räume_has_tabelle_elemente
                    (`TABELLE_Räume_idTABELLE_Räume`, `TABELLE_Elemente_idTABELLE_Elemente`,
                     `Neu/Bestand`, Anzahl, tabelle_Varianten_idtabelle_Varianten,
                     Kurzbeschreibung, Timestamp, Standort, Verwendung)
                VALUES (?, ?, 1, ?, ?, ?, ?,1,1)
            ");
            $stmt->bind_param('iiiiss', $raum_id, $element_id, $anzahl, $variante_id, $kommentar, $ts);
            $stmt->execute();
            $new_id = (int)$stmt->insert_id;
            $ok = $new_id > 0;
            $stmt->close();

            // Kosten-Eintrag anlegen falls noch nicht vorhanden (Kosten = 0)
            // Ohne diesen Eintrag fehlt das Element in allen Kostenberechnungen des RB
            $stmt_cost = $mysqli->prepare("
                SELECT 1 FROM tabelle_projekt_varianten_kosten
                WHERE tabelle_projekte_idTABELLE_Projekte = ?
                  AND tabelle_elemente_idTABELLE_Elemente = ?
                  AND tabelle_Varianten_idtabelle_Varianten = ?
                LIMIT 1
            ");
            $stmt_cost->bind_param('iii', $projekt_id, $element_id, $variante_id);
            $stmt_cost->execute();
            $cost_exists = (bool)$stmt_cost->get_result()->fetch_row();
            $stmt_cost->close();

            if (!$cost_exists) {
                $stmt_cost_ins = $mysqli->prepare("
                    INSERT INTO tabelle_projekt_varianten_kosten
                        (tabelle_projekte_idTABELLE_Projekte, tabelle_elemente_idTABELLE_Elemente,
                         tabelle_Varianten_idtabelle_Varianten, Kosten)
                    VALUES (?, ?, ?, 0)
                ");
                $stmt_cost_ins->bind_param('iii', $projekt_id, $element_id, $variante_id);
                $stmt_cost_ins->execute();
                $stmt_cost_ins->close();
            }

            audit_log($mysqli, $projekt_id, $user_id, 'add', [
                'raum_id' => $raum_id,
                'element_id' => $element_id,
                'variante_id' => $variante_id,
                'anzahl' => $anzahl,
            ]);
            $results[] = ['type' => 'add', 'raum_id' => $raum_id, 'element_id' => $element_id,
                'variante_id' => $variante_id, 'new_id' => $new_id, 'ok' => $ok];

            if (!$ok) $had_error = true;

        } else {
            throw new RuntimeException('Unbekannter action-type: ' . $type);
        }
    }

    $mysqli->commit();

} catch (Throwable $e) {
    $mysqli->rollback();
    json_error('Datenbankfehler: ' . $e->getMessage(), 500);
}

$ok_count = count(array_filter($results, fn($r) => $r['ok']));
$err_count = count($results) - $ok_count;

echo json_encode([
    'ok' => $err_count === 0,
    'total' => count($results),
    'success' => $ok_count,
    'errors' => $err_count,
    'results' => $results,
], JSON_UNESCAPED_UNICODE);