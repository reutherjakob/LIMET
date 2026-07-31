<?php
require_once 'utils/_utils.php';
include "utils/_format.php";
check_login();
header('Content-Type: application/json');

$datum      = $_POST['datum']      ?? '2024-01-01';
$projektID  = (int)($_POST['projektID'] ?? 0);
$nurNeu     = (int)($_POST['nurNeu'] ?? 1);      // 1 = nur Neu (Neu/Bestand = 1)
$nurStandort= (int)($_POST['nurStandort'] ?? 1); // 1 = nur Standort = 1 (verhindert Doppelzählung)

function getVerfahrenBadgeClass($verfahren): string
{
    switch ($verfahren) {
        case 'Direktvergabe':                                   return 'bg-secondary';
        case 'Direktvergabe mit vorheriger Bekanntmachung':     return 'bg-info';
        case 'Verhandlungsverfahren ohne Bekanntmachung':       return 'bg-warning';
        case 'Nicht offenes Verfahren ohne Bekanntmachung':     return 'bg-primary';
        case 'Nicht offenes Verfahren mit Bekanntmachung':
        case 'RV':                                              return 'bg-success';
        case 'Offenes Verfahren':
        case 'MKF':                                             return 'bg-danger';
        default:                                                return 'bg-dark';
    }
}

$mysqli = utils_connect_sql();

$where  = " WHERE le.Versand_LV >= ?
            AND p.idTABELLE_Projekte NOT IN (1,4) ";
$types  = "s";
$params = [$datum];

if ($projektID > 0) {
    $where   .= " AND p.idTABELLE_Projekte = ? ";
    $types   .= "i";
    $params[] = $projektID;
}
if ($nurNeu === 1)      $where .= " AND rhe.`Neu/Bestand` = 1 ";
if ($nurStandort === 1) $where .= " AND rhe.Standort = 1 ";

$sql = "
SELECT
    p.idTABELLE_Projekte,
    p.Projektname,
    le.idtabelle_Lose_Extern,
    le.LosNr_Extern,
    le.LosBezeichnung_Extern,
    le.Versand_LV,
    le.Ausführungsbeginn,
    le.Verfahren,
    le.Vergabe_abgeschlossen,
    le.Vergabesumme,
    lief.Lieferant,
    e.idTABELLE_Elemente,
    e.ElementID,
    e.Bezeichnung                        AS Element,
    v.Variante,
    rhe.`Neu/Bestand`                    AS NeuBestand,
    SUM(rhe.Anzahl)                      AS Anzahl,
    COUNT(DISTINCT r.idTABELLE_Räume)    AS AnzRaeume,
    GROUP_CONCAT(DISTINCT r.Raumnr ORDER BY r.Raumnr SEPARATOR ', ') AS Raeume,
    MAX(k.Kosten)                        AS Einzelkosten,
    SUM(rhe.Anzahl * COALESCE(k.Kosten,0)) AS Schaetzsumme
FROM tabelle_lose_extern le
INNER JOIN tabelle_räume_has_tabelle_elemente rhe
        ON rhe.tabelle_Lose_Extern_idtabelle_Lose_Extern = le.idtabelle_Lose_Extern
INNER JOIN tabelle_räume r
        ON r.idTABELLE_Räume = rhe.TABELLE_Räume_idTABELLE_Räume
INNER JOIN tabelle_projekte p
        ON p.idTABELLE_Projekte = r.tabelle_projekte_idTABELLE_Projekte
INNER JOIN tabelle_elemente e
        ON e.idTABELLE_Elemente = rhe.TABELLE_Elemente_idTABELLE_Elemente
INNER JOIN tabelle_varianten v
        ON v.idtabelle_Varianten = rhe.tabelle_Varianten_idtabelle_Varianten
LEFT  JOIN tabelle_lieferant lief
        ON lief.idTABELLE_Lieferant = le.tabelle_lieferant_idTABELLE_Lieferant
LEFT  JOIN tabelle_projekt_varianten_kosten k
        ON k.tabelle_projekte_idTABELLE_Projekte  = r.tabelle_projekte_idTABELLE_Projekte
       AND k.tabelle_elemente_idTABELLE_Elemente  = rhe.TABELLE_Elemente_idTABELLE_Elemente
       AND k.tabelle_Varianten_idtabelle_Varianten = rhe.tabelle_Varianten_idtabelle_Varianten
$where
GROUP BY p.idTABELLE_Projekte, p.Projektname,
         le.idtabelle_Lose_Extern, le.LosNr_Extern, le.LosBezeichnung_Extern,
         le.Versand_LV, le.Ausführungsbeginn, le.Verfahren, le.Vergabe_abgeschlossen,
         le.Vergabesumme, lief.Lieferant,
         e.idTABELLE_Elemente, e.ElementID, e.Bezeichnung, v.Variante, rhe.`Neu/Bestand`
ORDER BY p.Projektname, le.LosNr_Extern, e.ElementID
";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$statusBadges = [
    0 => "<span class='badge bg-danger'>Offen</span>",
    1 => "<span class='badge bg-success'>Fertig</span>",
    2 => "<span class='badge bg-primary'>Wartend</span>",
];

$data = [];
while ($row = $result->fetch_assoc()) {

    // gleiche Ausschlüsse wie in der Losverwaltung
    $bez = $row["LosBezeichnung_Extern"] ?? "";
    if ($row["Projektname"] === "Test_Projekt"
        || stripos($bez, "löschen")  !== false
        || stripos($bez, "ENTFÄLLT") !== false
        || stripos($bez, "Entfallen")!== false
        || empty($row["Verfahren"])) continue;

    $losKey = $row["LosNr_Extern"] . " – " . $bez;

    $data[] = [
        "projektID"    => (int)$row["idTABELLE_Projekte"],
        "projekt"      => $row["Projektname"],
        "losID"        => (int)$row["idtabelle_Lose_Extern"],
        "losNr"        => $row["LosNr_Extern"],
        "losBez"       => $bez,
        "losKey"       => $row["Projektname"] . " | " . $losKey,
        "versand"      => $row["Versand_LV"],
        "beginn"       => $row["Ausführungsbeginn"],
        "verfahren"    => "<span class='badge rounded-pill " . getVerfahrenBadgeClass($row['Verfahren']) . "'>"
                          . htmlspecialchars($row['Verfahren']) . "</span>",
        "status"       => $statusBadges[(int)$row["Vergabe_abgeschlossen"]] ?? "",
        "auftragnehmer"=> $row["Lieferant"] ?: "<span class='text-muted fst-italic'>offen</span>",
        "vergabesumme" => (float)$row["Vergabesumme"],
        "elementID"    => $row["ElementID"],
        "element"      => htmlspecialchars($row["Element"] ?? ""),
        "variante"     => $row["Variante"],
        "neubestand"   => ((int)$row["NeuBestand"] === 1)
                          ? "<span class='badge bg-success'>Neu</span>"
                          : "<span class='badge bg-secondary'>Bestand</span>",
        "anzahl"       => (int)$row["Anzahl"],
        "anzRaeume"    => (int)$row["AnzRaeume"],
        "raeume"       => htmlspecialchars($row["Raeume"] ?? ""),
        "einzelkosten" => (float)$row["Einzelkosten"],
        "schaetzsumme" => (float)$row["Schaetzsumme"],
    ];
}

echo json_encode(['data' => $data]);
$stmt->close();
$mysqli->close();
