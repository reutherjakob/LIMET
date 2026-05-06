<?php
/**
 * api_merge_variants.php
 * ══════════════════════════════════════════════════════════════════
 * Erkennt und konsolidiert doppelte Varianten innerhalb eines Projekts.
 *
 * Zwei Varianten gelten als "gleich", wenn sie für ein Element identische
 * Parameterwerte haben (für alle in tabelle_projekt_elementparameter
 * gespeicherten Parameter). Ignore-Parameter werden MITBERÜCKSICHTIGT —
 * nur wirklich identische Fingerprints zählen als Duplikat.
 *
 * Endpunkte (action= GET/POST-Parameter):
 *   GET  action=scan     → gibt alle Duplikat-Gruppen zurück (Preview)
 *   POST action=merge    → führt den Merge durch (body: {groups: [...]})
 *                          Jede Gruppe = [{element_id, keep_vid, drop_vids[]}]
 *
 * Merge-Schritte für jede "drop"-Variante:
 *   1. tabelle_räume_has_tabelle_elemente:
 *      - Zeilen die auf drop_vid zeigen → auf keep_vid umzeigen
 *      - Falls keep_vid für denselben Raum+Element schon existiert →
 *        Anzahlen addieren, drop-Zeile auf Anzahl=0 setzen
 *   2. tabelle_projekt_elementparameter:
 *      - drop_vid Parameter löschen
 *   3. tabelle_projekt_varianten_kosten:
 *      - drop_vid Einträge löschen (keep_vid hat bereits einen)
 * ══════════════════════════════════════════════════════════════════
 */

ob_start();
if (!function_exists('utils_connect_sql')) {
    include "../utils/_utils.php";
}
check_login();
ob_clean();
header('Content-Type: application/json; charset=utf-8');

function json_error(string $msg, int $code = 400): never {
    http_response_code($code);
    echo json_encode(['error' => $msg], JSON_UNESCAPED_UNICODE);
    exit;
}

if (!isset($_SESSION['projectID'])) json_error('Kein Projekt in Session');
$projekt_id = (int)$_SESSION['projectID'];
if ($projekt_id <= 0) json_error('projectID ungültig');

$action = $_GET['action'] ?? $_POST['action'] ?? (json_decode(file_get_contents('php://input'), true)['action'] ?? '');
$mysqli = utils_connect_sql();

// ══════════════════════════════════════════════════════════════════
// SCAN — alle Duplikat-Gruppen finden
// ══════════════════════════════════════════════════════════════════
if ($action === 'scan') {

    // 1. Alle Parameter des Projekts laden
    $res = $mysqli->prepare("
        SELECT
            pep.tabelle_elemente_idTABELLE_Elemente  AS elem_id,
            e.ElementID                               AS elem_code,
            e.Bezeichnung                             AS elem_name,
            pep.tabelle_Varianten_idtabelle_Varianten AS vid,
            v.Variante                                AS vletter,
            pep.tabelle_parameter_idTABELLE_Parameter AS pid,
            p.Bezeichnung                             AS param_name,
            pep.Wert
        FROM tabelle_projekt_elementparameter pep
        JOIN tabelle_elemente  e ON e.idTABELLE_Elemente  = pep.tabelle_elemente_idTABELLE_Elemente
        JOIN tabelle_varianten v ON v.idtabelle_Varianten = pep.tabelle_Varianten_idtabelle_Varianten
        JOIN tabelle_parameter p ON p.idTABELLE_Parameter = pep.tabelle_parameter_idTABELLE_Parameter
        WHERE pep.tabelle_projekte_idTABELLE_Projekte = ?
        ORDER BY pep.tabelle_elemente_idTABELLE_Elemente,
                 pep.tabelle_Varianten_idtabelle_Varianten,
                 pep.tabelle_parameter_idTABELLE_Parameter
    ");
    $res->bind_param('i', $projekt_id);
    $res->execute();
    $rows = $res->get_result()->fetch_all(MYSQLI_ASSOC);
    $res->close();

    // 2. Nach Element gruppieren → Fingerprints bilden
    // Structure: $by_elem[$elem_id][$vid] = ['letter' => ..., 'params' => [...], 'fp' => ...]
    $by_elem   = [];
    $elem_meta = []; // elem_id → [code, name]
    foreach ($rows as $row) {
        $eid = (int)$row['elem_id'];
        $vid = (int)$row['vid'];
        $elem_meta[$eid] = ['code' => $row['elem_code'], 'name' => $row['elem_name']];
        $by_elem[$eid][$vid] ??= ['letter' => $row['vletter'], 'params' => []];
        $by_elem[$eid][$vid]['params'][$row['pid']] = ['name' => $row['param_name'], 'wert' => $row['Wert']];
    }

    // Fingerprint = sorted param_id:value pairs
    foreach ($by_elem as $eid => &$variants) {
        foreach ($variants as $vid => &$v) {
            ksort($v['params']);
            $v['fp'] = json_encode(array_map(fn($p) => $p['wert'], $v['params']));
        }
    }
    unset($variants, $v);

    // 3. Zeilen aus rhe laden (Anzahl je vid im Projekt)
    $rhe_res = $mysqli->prepare("
        SELECT rhe.TABELLE_Elemente_idTABELLE_Elemente AS elem_id,
               rhe.tabelle_Varianten_idtabelle_Varianten AS vid,
               SUM(rhe.Anzahl) AS total_anzahl,
               COUNT(*) AS room_count
        FROM tabelle_räume_has_tabelle_elemente rhe
        JOIN tabelle_räume r ON r.idTABELLE_Räume = rhe.TABELLE_Räume_idTABELLE_Räume
        WHERE r.tabelle_projekte_idTABELLE_Projekte = ?
          AND rhe.Anzahl > 0
        GROUP BY rhe.TABELLE_Elemente_idTABELLE_Elemente, rhe.tabelle_Varianten_idtabelle_Varianten
    ");
    $rhe_res->bind_param('i', $projekt_id);
    $rhe_res->execute();
    $rhe_rows = $rhe_res->get_result()->fetch_all(MYSQLI_ASSOC);
    $rhe_res->close();

    $rhe_by_elem_vid = [];
    foreach ($rhe_rows as $r) {
        $rhe_by_elem_vid[(int)$r['elem_id']][(int)$r['vid']] = [
            'total_anzahl' => (int)$r['total_anzahl'],
            'room_count'   => (int)$r['room_count'],
        ];
    }

    // 4. Duplikate finden
    $duplicate_groups = [];
    foreach ($by_elem as $eid => $variants) {
        // Fingerprint → liste von vids
        $fp_map = [];
        foreach ($variants as $vid => $v) {
            $fp_map[$v['fp']][] = $vid;
        }
        foreach ($fp_map as $fp => $vids) {
            if (count($vids) < 2) continue;

            // Sortieren: zuerst der vid mit den meisten aktiven Raum-Einträgen,
            // dann niedrigste vid → dieser wird "keep"
            usort($vids, function($a, $b) use ($rhe_by_elem_vid, $eid) {
                $a_active = $rhe_by_elem_vid[$eid][$a]['total_anzahl'] ?? 0;
                $b_active = $rhe_by_elem_vid[$eid][$b]['total_anzahl'] ?? 0;
                if ($b_active !== $a_active) return $b_active - $a_active; // mehr aktive zuerst
                return $a - $b; // sonst niedrigste vid
            });

            $keep_vid  = $vids[0];
            $drop_vids = array_slice($vids, 1);

            $params_display = [];
            foreach ($variants[$keep_vid]['params'] as $pid => $p) {
                $params_display[] = ['pid' => $pid, 'name' => $p['name'], 'wert' => $p['wert']];
            }

            $variant_info = [];
            foreach ($vids as $vid) {
                $variant_info[] = [
                    'vid'          => $vid,
                    'letter'       => $variants[$vid]['letter'],
                    'total_anzahl' => $rhe_by_elem_vid[$eid][$vid]['total_anzahl'] ?? 0,
                    'room_count'   => $rhe_by_elem_vid[$eid][$vid]['room_count']   ?? 0,
                    'is_keep'      => $vid === $keep_vid,
                ];
            }

            $duplicate_groups[] = [
                'elem_id'      => $eid,
                'elem_code'    => $elem_meta[$eid]['code'],
                'elem_name'    => $elem_meta[$eid]['name'],
                'keep_vid'     => $keep_vid,
                'keep_letter'  => $variants[$keep_vid]['letter'],
                'drop_vids'    => $drop_vids,
                'drop_letters' => array_map(fn($v) => $variants[$v]['letter'], $drop_vids),
                'params'       => $params_display,
                'variants'     => $variant_info,
                'fp'           => $fp,
            ];
        }
    }

    echo json_encode([
        'projekt_id'       => $projekt_id,
        'duplicate_groups' => $duplicate_groups,
        'total_duplicates' => count($duplicate_groups),
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// ══════════════════════════════════════════════════════════════════
// MERGE — Duplikate zusammenführen
// ══════════════════════════════════════════════════════════════════
if ($action === 'merge' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $body   = json_decode(file_get_contents('php://input'), true);
    $groups = $body['groups'] ?? [];
    if (empty($groups)) json_error('groups fehlt oder leer');

    $mysqli->begin_transaction();
    $merged  = 0;
    $errors  = [];

    try {
        foreach ($groups as $g) {
            $elem_id  = (int)($g['elem_id']  ?? 0);
            $keep_vid = (int)($g['keep_vid'] ?? 0);
            $drop_vids = array_map('intval', $g['drop_vids'] ?? []);

            if (!$elem_id || !$keep_vid || empty($drop_vids)) continue;

            foreach ($drop_vids as $drop_vid) {

                // ── 1. rhe-Zeilen die auf drop_vid zeigen ────────────────
                //    Je Raum: Prüfe ob keep_vid schon existiert
                $find = $mysqli->prepare("
                    SELECT rhe.id, rhe.TABELLE_Räume_idTABELLE_Räume AS raum_id, rhe.Anzahl
                    FROM tabelle_räume_has_tabelle_elemente rhe
                    JOIN tabelle_räume r ON r.idTABELLE_Räume = rhe.TABELLE_Räume_idTABELLE_Räume
                    WHERE r.tabelle_projekte_idTABELLE_Projekte = ?
                      AND rhe.TABELLE_Elemente_idTABELLE_Elemente = ?
                      AND rhe.tabelle_Varianten_idtabelle_Varianten = ?
                ");
                $find->bind_param('iii', $projekt_id, $elem_id, $drop_vid);
                $find->execute();
                $drop_rows = $find->get_result()->fetch_all(MYSQLI_ASSOC);
                $find->close();

                foreach ($drop_rows as $drop_row) {
                    $raum_id    = (int)$drop_row['raum_id'];
                    $drop_rhe_id = (int)$drop_row['id'];
                    $drop_anzahl = (int)$drop_row['Anzahl'];

                    // Prüfen ob keep_vid in diesem Raum schon existiert
                    $chk = $mysqli->prepare("
                        SELECT id, Anzahl FROM tabelle_räume_has_tabelle_elemente
                        WHERE TABELLE_Räume_idTABELLE_Räume = ?
                          AND TABELLE_Elemente_idTABELLE_Elemente = ?
                          AND tabelle_Varianten_idtabelle_Varianten = ?
                        LIMIT 1
                    ");
                    $chk->bind_param('iii', $raum_id, $elem_id, $keep_vid);
                    $chk->execute();
                    $keep_row = $chk->get_result()->fetch_assoc();
                    $chk->close();

                    if ($keep_row) {
                        // keep_vid existiert bereits → Anzahlen addieren
                        $new_anzahl  = (int)$keep_row['Anzahl'] + $drop_anzahl;
                        $keep_rhe_id = (int)$keep_row['id'];
                        $ts = date('Y-m-d H:i:s');
                        $upd = $mysqli->prepare("
                            UPDATE tabelle_räume_has_tabelle_elemente
                            SET Anzahl = ?, Timestamp = ? 
                            WHERE id = ?
                        ");
                        $upd->bind_param('isi', $new_anzahl, $ts, $keep_rhe_id);
                        $upd->execute();
                        $upd->close();

                        // drop-Zeile auf 0 setzen
                        $zero = $mysqli->prepare("
                            UPDATE tabelle_räume_has_tabelle_elemente
                            SET Anzahl = 0, Timestamp = ?
                            WHERE id = ?
                        ");
                        $zero->bind_param('si', $ts, $drop_rhe_id);
                        $zero->execute();
                        $zero->close();
                    } else {
                        // Nur vid umschreiben
                        $upd = $mysqli->prepare("
                            UPDATE tabelle_räume_has_tabelle_elemente
                            SET tabelle_Varianten_idtabelle_Varianten = ?
                            WHERE id = ?
                        ");
                        $upd->bind_param('ii', $keep_vid, $drop_rhe_id);
                        $upd->execute();
                        $upd->close();
                    }
                }

                // ── 2. Parameter löschen ──────────────────────────────────
                $del_params = $mysqli->prepare("
                    DELETE FROM tabelle_projekt_elementparameter
                    WHERE tabelle_projekte_idTABELLE_Projekte = ?
                      AND tabelle_elemente_idTABELLE_Elemente = ?
                      AND tabelle_Varianten_idtabelle_Varianten = ?
                ");
                $del_params->bind_param('iii', $projekt_id, $elem_id, $drop_vid);
                $del_params->execute();
                $del_params->close();

                // ── 3. Kosten-Einträge löschen ────────────────────────────
                $del_kosten = $mysqli->prepare("
                    DELETE FROM tabelle_projekt_varianten_kosten
                    WHERE tabelle_projekte_idTABELLE_Projekte = ?
                      AND tabelle_elemente_idTABELLE_Elemente = ?
                      AND tabelle_Varianten_idtabelle_Varianten = ?
                ");
                $del_kosten->bind_param('iii', $projekt_id, $elem_id, $drop_vid);
                $del_kosten->execute();
                $del_kosten->close();

                $merged++;
            }
        }

        $mysqli->commit();
    } catch (Throwable $e) {
        $mysqli->rollback();
        json_error('Datenbankfehler: ' . $e->getMessage(), 500);
    }

    echo json_encode([
        'ok'     => empty($errors),
        'merged' => $merged,
        'errors' => $errors,
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

json_error("Unbekannte action: $action");