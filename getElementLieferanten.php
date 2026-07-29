<?php
require_once 'utils/_utils.php';
check_login();
header('Content-Type: application/json');

$mysqli = utils_connect_sql();

/* ---------------------------------------------------------------------------
 * Zwei Quellen für potenzielle Auftragnehmer je Element:
 *
 *  A) KATALOG   – gepflegte Zuordnung Gerät -> Lieferant
 *  B) HISTORIE  – Lieferant, der am Los eingetragen ist, das dieses Element enthielt
 *
 * Die Auswertung sagt nur aus, OB eine Verbindung besteht. Stückzahlen,
 * Losanzahlen und Vergabesummen werden bewusst nicht mehr ermittelt.
 *
 * ---------------------------------------------------------------------------
 * EMPFOHLENE INDIZES:
 *
 *   ALTER TABLE tabelle_räume_has_tabelle_elemente
 *     ADD INDEX ix_rhe_element  (TABELLE_Elemente_idTABELLE_Elemente),
 *     ADD INDEX ix_rhe_raum     (TABELLE_Räume_idTABELLE_Räume),
 *     ADD INDEX ix_rhe_geraet   (TABELLE_Geraete_idTABELLE_Geraete),
 *     ADD INDEX ix_rhe_los      (tabelle_Lose_Extern_idtabelle_Lose_Extern, Standort);
 *
 *   ALTER TABLE tabelle_bestandsdaten
 *     ADD INDEX ix_bd_rhe       (tabelle_räume_has_tabelle_elemente_id, tabelle_geraete_idTABELLE_Geraete);
 *
 *   ALTER TABLE tabelle_geraete_has_tabelle_lieferant
 *     ADD INDEX ix_gl_geraet    (tabelle_geraete_idTABELLE_Geraete, tabelle_lieferant_idTABELLE_Lieferant);
 *
 *   ALTER TABLE tabelle_räume
 *     ADD INDEX ix_raum_projekt (tabelle_projekte_idTABELLE_Projekte, idTABELLE_Räume);
 *
 *   ALTER TABLE tabelle_lose_extern
 *     ADD INDEX ix_le_versand   (Versand_LV, tabelle_lieferant_idTABELLE_Lieferant);
 * ------------------------------------------------------------------------- */

$datum     = $_POST['datum'] ?? '2020-01-01';
$projektID = (int)($_POST['projektID'] ?? 0);   // von der Seite derzeit nicht gesetzt
$quelle    = $_POST['quelle'] ?? 'alle';        // alle | katalog | historie

$rows = [];   // key: elementIntID|lieferantID

function &rowRef(array &$rows, int $eid, string $elementID, string $element,
                 int $lid, array $lief): array
{
    $key = $eid . '|' . $lid;
    if (!isset($rows[$key])) {
        $rows[$key] = [
            "elementID"   => $elementID,
            "element"     => $element,
            "lieferant"   => $lief["Lieferant"] ?? '',
            "anschrift"   => $lief["Anschrift"] ?? '',
            "plz"         => $lief["PLZ"] ?? '',
            "land"        => $lief["Land"] ?? '',
            "katalog"     => 0,
            "historie"    => 0,
            "_geraete"    => [],
            "_hersteller" => [],
            "_projekte"   => [],
            "_verfahren"  => [],
            "_lose"       => [],
        ];
    }
    return $rows[$key];
}

/* --------------------------- A) KATALOG ----------------------------------
 * Der Weg Element -> Gerät führt über zwei Spalten. Als IN(a,b) in der
 * Join-Bedingung ist das nicht indexfähig, deshalb UNION zweier Zweige;
 * die UNION dedupliziert bereits, ein SELECT DISTINCT erübrigt sich. Die
 * Element-/Geräte-Paare werden zuerst reduziert und erst danach an
 * Lieferant und Hersteller gejoint. -------------------------------------- */
if ($quelle !== 'historie') {

    $projK   = $projektID > 0 ? " AND r.tabelle_projekte_idTABELLE_Projekte = ? " : "";
    $typesK  = "";
    $paramsK = [];
    if ($projektID > 0) {
        $typesK  = "ii";
        $paramsK = [$projektID, $projektID];   // einmal je UNION-Zweig
    }

    $sqlK = "
    SELECT eg.idTABELLE_Elemente,
           e.ElementID,
           e.Bezeichnung        AS Element,
           eg.idTABELLE_Geraete,
           g.Typ,
           h.Hersteller,
           lief.idTABELLE_Lieferant,
           lief.Lieferant,
           lief.Anschrift,
           lief.PLZ,
           lief.Land
    FROM (
        /* Weg 1: Gerät direkt an der Raum-Element-Verknüpfung */
        SELECT rhe.TABELLE_Elemente_idTABELLE_Elemente AS idTABELLE_Elemente,
               rhe.TABELLE_Geraete_idTABELLE_Geraete   AS idTABELLE_Geraete
        FROM tabelle_räume_has_tabelle_elemente rhe
        INNER JOIN tabelle_räume r ON r.idTABELLE_Räume = rhe.TABELLE_Räume_idTABELLE_Räume
        WHERE rhe.TABELLE_Geraete_idTABELLE_Geraete IS NOT NULL
          AND r.tabelle_projekte_idTABELLE_Projekte NOT IN (1,4)
          $projK

        UNION

        /* Weg 2: Gerät über die Bestandsdaten */
        SELECT rhe.TABELLE_Elemente_idTABELLE_Elemente,
               bd.tabelle_geraete_idTABELLE_Geraete
        FROM tabelle_räume_has_tabelle_elemente rhe
        INNER JOIN tabelle_räume r ON r.idTABELLE_Räume = rhe.TABELLE_Räume_idTABELLE_Räume
        INNER JOIN tabelle_bestandsdaten bd
                ON bd.tabelle_räume_has_tabelle_elemente_id = rhe.id
        WHERE bd.tabelle_geraete_idTABELLE_Geraete IS NOT NULL
          AND r.tabelle_projekte_idTABELLE_Projekte NOT IN (1,4)
          $projK
    ) eg
    INNER JOIN tabelle_elemente e ON e.idTABELLE_Elemente = eg.idTABELLE_Elemente
    INNER JOIN tabelle_geraete  g ON g.idTABELLE_Geraete  = eg.idTABELLE_Geraete
    INNER JOIN tabelle_geraete_has_tabelle_lieferant gl
                    ON gl.tabelle_geraete_idTABELLE_Geraete = g.idTABELLE_Geraete
    INNER JOIN tabelle_lieferant lief
                    ON lief.idTABELLE_Lieferant = gl.tabelle_lieferant_idTABELLE_Lieferant
    LEFT  JOIN tabelle_hersteller h
                    ON h.idtabelle_hersteller = g.tabelle_hersteller_idtabelle_hersteller";

    $stmt = $mysqli->prepare($sqlK);
    if ($typesK !== "") $stmt->bind_param($typesK, ...$paramsK);
    $stmt->execute();
    $res = $stmt->get_result();

    while ($row = $res->fetch_assoc()) {
        $r = &rowRef($rows, (int)$row["idTABELLE_Elemente"], $row["ElementID"],
            $row["Element"] ?? '', (int)$row["idTABELLE_Lieferant"], $row);
        $r["katalog"] = 1;
        $gid = (int)$row["idTABELLE_Geraete"];
        if (!isset($r["_geraete"][$gid])) {
            $r["_geraete"][$gid] = trim(($row["Hersteller"] ?? '') . ' ' . ($row["Typ"] ?? ''));
        }
        if (!empty($row["Hersteller"])) $r["_hersteller"][$row["Hersteller"]] = true;
        unset($r);
    }
    $stmt->close();
}

/* --------------------------- B) HISTORIE ---------------------------------
 * Eine Zeile je Element + Lieferant + Los + Projekt. Das GROUP BY läuft nur
 * über Primärschlüssel; die übrigen Spalten sind davon funktional abhängig. */
if ($quelle !== 'katalog') {

    $whereH  = " WHERE le.Versand_LV >= ?
                 AND p.idTABELLE_Projekte NOT IN (1,4)
                 AND p.Projektname <> 'Test_Projekt'
                 AND le.tabelle_lieferant_idTABELLE_Lieferant IS NOT NULL
                 AND rhe.Standort = 1
                 AND le.Verfahren IS NOT NULL AND le.Verfahren <> '' ";
    $typesH  = "s";
    $paramsH = [$datum];
    if ($projektID > 0) {
        $whereH   .= " AND p.idTABELLE_Projekte = ? ";
        $typesH   .= "i";
        $paramsH[] = $projektID;
    }

    $sqlH = "
    SELECT e.idTABELLE_Elemente,
           e.ElementID,
           e.Bezeichnung        AS Element,
           lief.idTABELLE_Lieferant,
           lief.Lieferant, lief.Anschrift, lief.PLZ, lief.Land,
           le.LosNr_Extern,
           le.Verfahren,
           p.idTABELLE_Projekte,
           p.Projektname
    FROM tabelle_räume_has_tabelle_elemente rhe
    INNER JOIN tabelle_lose_extern le  ON le.idtabelle_Lose_Extern = rhe.tabelle_Lose_Extern_idtabelle_Lose_Extern
    INNER JOIN tabelle_lieferant  lief ON lief.idTABELLE_Lieferant = le.tabelle_lieferant_idTABELLE_Lieferant
    INNER JOIN tabelle_räume      r    ON r.idTABELLE_Räume = rhe.TABELLE_Räume_idTABELLE_Räume
    INNER JOIN tabelle_projekte   p    ON p.idTABELLE_Projekte = r.tabelle_projekte_idTABELLE_Projekte
    INNER JOIN tabelle_elemente   e    ON e.idTABELLE_Elemente = rhe.TABELLE_Elemente_idTABELLE_Elemente
    $whereH
    GROUP BY e.idTABELLE_Elemente,
             lief.idTABELLE_Lieferant,
             le.idtabelle_Lose_Extern,
             p.idTABELLE_Projekte";

    $stmt = $mysqli->prepare($sqlH);
    $stmt->bind_param($typesH, ...$paramsH);
    $stmt->execute();
    $res = $stmt->get_result();

    while ($row = $res->fetch_assoc()) {
        $r = &rowRef($rows, (int)$row["idTABELLE_Elemente"], $row["ElementID"],
            $row["Element"] ?? '', (int)$row["idTABELLE_Lieferant"], $row);
        $r["historie"] = 1;
        $r["_projekte"][$row["idTABELLE_Projekte"]] = $row["Projektname"];
        $r["_verfahren"][$row["Verfahren"]] = true;
        $r["_lose"][$row["LosNr_Extern"]] = true;
        unset($r);
    }
    $stmt->close();
}

/* --------------------------- Ausgabe ------------------------------------- */
$data = [];
foreach ($rows as $r) {

    if ($r["katalog"] && $r["historie"]) { $quelleTxt = 'beides';   $rank = 0; }
    elseif ($r["katalog"])               { $quelleTxt = 'Katalog';  $rank = 1; }
    else                                 { $quelleTxt = 'Historie'; $rank = 2; }

    $geraete = array_values(array_filter($r["_geraete"]));

    $data[] = [
        "elementID"  => $r["elementID"],
        "element"    => htmlspecialchars($r["element"]),
        "elementKey" => $r["elementID"] . " – " . $r["element"],
        "lieferant"  => htmlspecialchars($r["lieferant"]),
        "quelle"     => $quelleTxt,
        "quelleRank" => $rank,
        "geraete"    => htmlspecialchars(implode(', ', array_slice($geraete, 0, 8))),
        "hersteller" => htmlspecialchars(implode(', ', array_keys($r["_hersteller"]))),
        "projekte"   => htmlspecialchars(implode(', ', array_values($r["_projekte"]))),
        "lose"       => htmlspecialchars(implode(', ', array_keys($r["_lose"]))),
        "verfahren"  => htmlspecialchars(implode(', ', array_keys($r["_verfahren"]))),
        "anschrift"  => htmlspecialchars($r["anschrift"] ?? ''),
        "plz"        => htmlspecialchars($r["plz"] ?? ''),
        "land"       => htmlspecialchars($r["land"] ?? ''),
    ];
}

echo json_encode(['data' => $data]);
$mysqli->close();