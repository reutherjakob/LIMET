<?php
/**
 * workflow_helpers.php
 * Gemeinsame Helfer für die Workflow-Verwaltung.
 * Wird von der Hauptseite und allen AJAX-Endpoints eingebunden.
 *
 * Tabellen-/Spaltennamen, die nicht fix bekannt sind (Primärschlüssel,
 * Bezeichnungsspalte), werden zur Laufzeit aus dem DB-Schema ermittelt.
 * Diese Werte stammen aus dem Schema, nicht aus User-Input; bei Verwendung
 * werden sie zusätzlich mit Backticks gequotet.
 */

/** Primärschlüssel-Spalte einer Tabelle ermitteln. */
function wf_pk_column(mysqli $mysqli, string $table): string
{
    $t = str_replace('`', '', $table);
    $res = $mysqli->query("SHOW KEYS FROM `$t` WHERE Key_name = 'PRIMARY'");
    if ($res && ($row = $res->fetch_assoc())) {
        $col = $row['Column_name'];
        $res->free();
        return $col;
    }
    if ($res) $res->free();
    return 'id';
}

/**
 * Erste Text-/Varchar-Spalte einer Tabelle (außer dem Primärschlüssel) als
 * Anzeigename. Existiert keine Textspalte, wird der Primärschlüssel
 * zurückgegeben (Signal: "kein eigener Name").
 */
function wf_text_column(mysqli $mysqli, string $table, string $pkCol): string
{
    $t = str_replace('`', '', $table);
    $res = $mysqli->query("SHOW COLUMNS FROM `$t`");
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            if (strcasecmp($row['Field'], $pkCol) === 0) continue;
            if (preg_match('/char|text/i', $row['Type'])) {
                $res->free();
                return $row['Field'];
            }
        }
        $res->free();
    }
    return $pkCol;
}

/** Bezeichnungsspalte von tabelle_workflow (gecached). */
function wf_name_column(mysqli $mysqli): string
{
    static $cached = null;
    if ($cached !== null) return $cached;
    $cached = wf_text_column($mysqli, 'tabelle_workflow', 'idtabelle_workflow');
    return $cached;
}

/**
 * Prüft, ob der Workflow dem übergebenen Projekt zugeordnet ist.
 * Schutz: Schreiboperationen nur auf Workflows des aktiven Projekts.
 */
function wf_belongs_to_project(mysqli $mysqli, int $workflowID, int $projectID): bool
{
    $stmt = $mysqli->prepare("
        SELECT 1 FROM tabelle_workflow_has_tabelle_projekte
        WHERE tabelle_workflow_idtabelle_workflow = ?
          AND tabelle_projekte_idTABELLE_Projekte = ?
        LIMIT 1
    ");
    $stmt->bind_param('ii', $workflowID, $projectID);
    $stmt->execute();
    $ok = (bool)$stmt->get_result()->fetch_row();
    $stmt->close();
    return $ok;
}

/** Prüft, ob eine Tabelle im aktuellen Schema existiert. */
function wf_table_exists(mysqli $mysqli, string $table): bool
{
    $t = $mysqli->real_escape_string(str_replace('`', '', $table));
    $res = $mysqli->query("SHOW TABLES LIKE '$t'");
    $exists = $res && $res->num_rows > 0;
    if ($res) $res->free();
    return $exists;
}

/**
 * Prüft, ob ein Workflow "in Benutzung" ist.
 * Definition: Es existieren Lot-/Ausführungs-Einträge in ,
 * d. h. der Workflow wurde bereits auf Lose ausgerollt (projektübergreifend).
 *
 * Fehlt die Tabelle, gilt der Workflow als nicht in Benutzung.
 */

function wf_in_use_in_current_project(mysqli $mysqli, int $workflowID): bool
{
    $projectID = (int)$_SESSION['projectID'];

    $stmt = $mysqli->prepare("
        SELECT 1
        FROM tabelle_lot_workflow lw
        INNER JOIN tabelle_lose_extern le
            ON le.idtabelle_Lose_Extern = lw.tabelle_lose_extern_idtabelle_Lose_Extern
        WHERE lw.tabelle_workflow_idtabelle_workflow = ?
          AND le.tabelle_projekte_idTABELLE_Projekte = ?
        LIMIT 1
    ");

    $stmt->bind_param('ii', $workflowID, $projectID);
    $stmt->execute();

    $inUse = (bool)$stmt->get_result()->fetch_row();
    $stmt->close();

    return $inUse;
}

function wf_in_use(mysqli $mysqli, int $workflowID): bool
{
    // Irgendein LOS HAT DIESEN WF ZUGEWIESEN
    $stmt = $mysqli->prepare("
        SELECT 1 FROM tabelle_lot_workflow
        WHERE tabelle_workflow_idtabelle_workflow = ? 
        LIMIT 1
    ");
    $stmt->bind_param('i', $workflowID);
    $stmt->execute();
    $inUse = (bool)$stmt->get_result()->fetch_row();
    $stmt->close();
    return $inUse;
}

/**
 * Prüft, ob der Workflow außer dem aktuellen Projekt noch mindestens einem
 * WEITEREN Projekt zugeordnet ist. Da die Schritte am Workflow (nicht am
 * Projekt) hängen, würde eine Schritt-Änderung diese anderen Projekte mit
 * verändern – daher als Sperre für updateWorkflowStep gedacht.
 */
function wf_shared_with_other_project(mysqli $mysqli, int $workflowID, int $projectID): bool
{
    // Prüft: Wird dieser Workflow in einem Los eines ANDEREN Projekts verwendet?
    $stmt = $mysqli->prepare("
        SELECT 1
        FROM tabelle_lot_workflow lw
        INNER JOIN tabelle_lose_extern le
            ON le.idtabelle_Lose_Extern = lw.tabelle_lose_extern_idtabelle_Lose_Extern
        WHERE lw.tabelle_workflow_idtabelle_workflow = ?
          AND le.tabelle_projekte_idTABELLE_Projekte <> ?
        LIMIT 1
    ");

    $stmt->bind_param('ii', $workflowID, $projectID);
    $stmt->execute();

    $shared = (bool)$stmt->get_result()->fetch_row();
    $stmt->close();

    return $shared;
}