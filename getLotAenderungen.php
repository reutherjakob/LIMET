<?php
ob_start();
require_once "utils/_utils.php";
init_page_serversides();

$mysqli = utils_connect_sql();

$dateFrom = isset($_POST['dateFrom']) ? $_POST['dateFrom'] : '2000-01-01';
$dateTo   = isset($_POST['dateTo'])   ? $_POST['dateTo']   : date('Y-m-d');

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateFrom)) $dateFrom = '2000-01-01';
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateTo))   $dateTo   = date('Y-m-d');

$dateToFull = $dateTo . ' 23:59:59';

try {
    $sql = "
        SELECT
            a.idtabelle_rb_aenderung,           -- 0
            a.Timestamp,                         -- 1
            a.user,                              -- 2

            -- Los
            COALESCE(le.LosBezeichnung_Extern, CONCAT('Los #', a.tabelle_Lose_Extern_idtabelle_Lose_Extern_copy1)) AS los_bezeichnung_neu, -- 3
            COALESCE(le_alt.LosBezeichnung_Extern, CONCAT('Los #', a.tabelle_Lose_Extern_idtabelle_Lose_Extern)) AS los_bezeichnung_alt,   -- 4
            a.tabelle_Lose_Extern_idtabelle_Lose_Extern,        -- 5
            a.tabelle_Lose_Extern_idtabelle_Lose_Extern_copy1,  -- 6

            -- Element
            a.elementID_alt,                     -- 7
            a.elementID_neu,                     -- 8
            COALESCE(el.Bezeichnung, '')          AS element_bezeichnung, -- 9

            -- Raum
            a.raumID_alt,                        -- 10
            a.raumID_neu,                        -- 11
            COALESCE(CONCAT(r.Raumnr, ' – ', r.Raumbezeichnung), '') AS raum_bezeichnung, -- 12

            -- Status
            a.status_alt,                        -- 13
            a.status_neu,                        -- 14

            -- Lieferdatum
            a.lieferdatum_alt,                   -- 15
            a.lieferdatum_neu,                   -- 16

            -- Budget
            a.projektBudgetID_alt,               -- 17
            a.projektBudgetID_neu,               -- 18

            -- Anzahl
            a.Anzahl,                            -- 19
            a.Anzahl_copy1,                      -- 20

            -- Kurzbeschreibung
            a.Kurzbeschreibung,                  -- 21
            a.Kurzbeschreibung_copy1,            -- 22

            -- Neu/Bestand
            a.`Neu/Bestand`,                     -- 23
            a.`Neu/Bestand_copy1`,               -- 24

            -- Standort
            a.Standort,                          -- 25
            a.Standort_copy1,                    -- 26

            -- Verwendung
            a.Verwendung,                        -- 27
            a.Verwendung_copy1,                  -- 28

            -- Anschaffung
            a.Anschaffung,                       -- 29
            a.Anschaffung_copy1,                 -- 30

            -- Internes Los
            a.tabelle_Lose_Intern_idtabelle_Lose_Intern,        -- 31
            a.tabelle_Lose_Intern_idtabelle_Lose_Intern_copy1,  -- 32

            -- Auftraggeber / Gewerke
            a.idtabelle_auftraggeber_GHG,        -- 33
            a.idtabelle_auftraggeber_GHG_copy1,  -- 34
            a.idtabelle_auftraggeberg_GUG,        -- 35
            a.idtabelle_auftraggeberg_GUG_copy1,  -- 36
            a.idTABELLE_Auftraggeber_Gewerke,    -- 37
            a.idTABELLE_Auftraggeber_Gewerke_copy1, -- 38

            -- Varianten
            a.tabelle_Varianten_idtabelle_Varianten,       -- 39
            a.tabelle_Varianten_idtabelle_Varianten_copy1 -- 40
 

        FROM tabelle_rb_aenderung a
        LEFT JOIN tabelle_lose_extern le
            ON le.idtabelle_Lose_Extern = a.tabelle_Lose_Extern_idtabelle_Lose_Extern_copy1
        LEFT JOIN tabelle_lose_extern le_alt
            ON le_alt.idtabelle_Lose_Extern = a.tabelle_Lose_Extern_idtabelle_Lose_Extern
        LEFT JOIN tabelle_elemente el
            ON el.idTABELLE_Elemente = COALESCE(a.elementID_neu, a.elementID_alt)
        LEFT JOIN tabelle_räume r
            ON r.idTABELLE_Räume = COALESCE(a.raumID_neu, a.raumID_alt)

        WHERE
            (a.tabelle_Lose_Extern_idtabelle_Lose_Extern IS NOT NULL OR tabelle_Lose_Extern_idtabelle_Lose_Extern_copy1 IS NOT NULL)
            AND a.Timestamp > ?
            AND a.Timestamp <= ?
            AND (
                NOT (a.elementID_alt           <=> a.elementID_neu)           OR
                NOT (a.raumID_alt              <=> a.raumID_neu)              OR
                NOT (a.status_alt              <=> a.status_neu)              OR
                NOT (a.lieferdatum_alt         <=> a.lieferdatum_neu)         OR
                NOT (a.projektBudgetID_alt     <=> a.projektBudgetID_neu)     OR
                NOT (a.Anzahl                  <=> a.Anzahl_copy1)            OR
              --  NOT (a.Kurzbeschreibung        <=> a.Kurzbeschreibung_copy1)  OR
                NOT (a.`Neu/Bestand`           <=> a.`Neu/Bestand_copy1`)     OR
                NOT (a.Standort                <=> a.Standort_copy1)          OR
                NOT (a.Verwendung              <=> a.Verwendung_copy1)        OR
                NOT (a.Anschaffung             <=> a.Anschaffung_copy1)       OR
                NOT (a.tabelle_Lose_Extern_idtabelle_Lose_Extern <=> a.tabelle_Lose_Extern_idtabelle_Lose_Extern_copy1) OR
                NOT (a.idtabelle_auftraggeber_GHG    <=> a.idtabelle_auftraggeber_GHG_copy1)    OR
                NOT (a.idtabelle_auftraggeberg_GUG   <=> a.idtabelle_auftraggeberg_GUG_copy1)   OR
                NOT (a.idTABELLE_Auftraggeber_Gewerke <=> a.idTABELLE_Auftraggeber_Gewerke_copy1) OR
                NOT (a.tabelle_Varianten_idtabelle_Varianten <=> a.tabelle_Varianten_idtabelle_Varianten_copy1)
            )
        AND r.tabelle_projekte_idTABELLE_Projekte =?
        ORDER BY a.Timestamp DESC
    ";

    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $mysqli->error);
    }

    $stmt->bind_param("ssi", $dateFrom, $dateToFull, $_SESSION["projectID"]);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_row()) {
        if ($row[1]) {
            $dt = new DateTime($row[1]);
            $row[1] = $dt->format('Y-m-d H:i');
        }
        if ($row[15]) $row[15] = date('d.m.Y', strtotime($row[15]));
        if ($row[16]) $row[16] = date('d.m.Y', strtotime($row[16]));

        $data[] = $row;
    }

    $stmt->close();
    $mysqli->close();

    ob_end_clean();
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['data' => $data]);

} catch (Exception $e) {
    ob_end_clean();
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage(), 'data' => []]);
}