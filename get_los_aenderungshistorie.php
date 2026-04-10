<?php
ob_start();
require_once "utils/_utils.php";
init_page_serversides();

$mysqli = utils_connect_sql();
$losID = getPostInt('losID');

if ($losID <= 0) {
    ob_end_clean();
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['data' => []]);
    exit;
}

try {
    $sql = "
        SELECT
            a.idtabelle_rb_aenderung,           -- 0
            a.Timestamp,                         -- 1
            a.user,                              -- 2

            -- Los
            COALESCE(le.LosBezeichnung_Extern,  CONCAT('Los #', a.tabelle_Lose_Extern_idtabelle_Lose_Extern_copy1)) AS los_bezeichnung_neu, -- 3
            COALESCE(le_alt.LosBezeichnung_Extern, CONCAT('Los #', a.tabelle_Lose_Extern_idtabelle_Lose_Extern))    AS los_bezeichnung_alt, -- 4
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
            a.idtabelle_auftraggeberg_GUG,       -- 35
            a.idtabelle_auftraggeberg_GUG_copy1, -- 36
            a.idTABELLE_Auftraggeber_Gewerke,    -- 37
            a.idTABELLE_Auftraggeber_Gewerke_copy1, -- 38

            -- Varianten
            a.tabelle_Varianten_idtabelle_Varianten,            -- 39
            a.tabelle_Varianten_idtabelle_Varianten_copy1       -- 40

        FROM tabelle_rb_aenderung a
        LEFT JOIN tabelle_lose_extern le
            ON le.idtabelle_Lose_Extern = a.tabelle_Lose_Extern_idtabelle_Lose_Extern_copy1
        LEFT JOIN tabelle_lose_extern le_alt
            ON le_alt.idtabelle_Lose_Extern = a.tabelle_Lose_Extern_idtabelle_Lose_Extern
        LEFT JOIN tabelle_elemente el
            ON el.idTABELLE_Elemente = COALESCE(a.elementID_neu, a.elementID_alt)
        LEFT JOIN tabelle_räume r
            ON r.idTABELLE_Räume = COALESCE(a.raumID_neu, a.raumID_alt)

        WHERE (
            a.tabelle_Lose_Extern_idtabelle_Lose_Extern       = ? OR
            a.tabelle_Lose_Extern_idtabelle_Lose_Extern_copy1 = ?
        )
        ORDER BY a.Timestamp DESC
    ";

    $stmt = $mysqli->prepare($sql);
    if (!$stmt) throw new Exception("Prepare failed: " . $mysqli->error);

    $stmt->bind_param("ii", $losID, $losID);
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

        // Skip rows where nothing actually changed
        $fieldPairs = [
            [3,4],[7,8],[10,11],[13,14],[15,16],[17,18],
            [19,20],[21,22],[23,24],[25,26],[27,28],[29,30],
            [31,32],[33,34],[35,36],[37,38],[39,40]
        ];
        $anyChange = false;
        foreach ($fieldPairs as [$alt, $neu]) {
            if ((string)($row[$alt] ?? '') !== (string)($row[$neu] ?? '')) {
                $anyChange = true;
                break;
            }
        }
        if (!$anyChange) continue;   // ← drop pure no-op saves

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