<?php
require_once 'utils/_utils.php';
include "utils/_format.php";
check_login();
header('Content-Type: application/json');

$datum = $_POST['datum'] ?? '2024-01-01';

function getVerfahrenBadgeClass($verfahren): string {
    switch ($verfahren) {
        case 'Direktvergabe': return 'bg-secondary';
        case 'Direktvergabe mit vorheriger Bekanntmachung': return 'bg-info';
        case 'Verhandlungsverfahren ohne Bekanntmachung': return 'bg-warning';
        case 'Nicht offenes Verfahren ohne Bekanntmachung': return 'bg-primary';
        case 'Nicht offenes Verfahren mit Bekanntmachung': return 'bg-success';
        case 'Offenes Verfahren': case 'MKF': return 'bg-danger';
        default: return 'bg-dark';
    }
}

$mysqli = utils_connect_sql();

$sql = "
SELECT 
    tabelle_lose_extern.idtabelle_Lose_Extern,
    tabelle_lose_extern.LosNr_Extern, 
    tabelle_lose_extern.LosBezeichnung_Extern, 
    tabelle_lose_extern.Versand_LV, 
    tabelle_lose_extern.Ausführungsbeginn, 
    tabelle_lose_extern.Verfahren, 
    tabelle_lose_extern.mkf_von_los,
    tabelle_lose_extern.Vergabesumme, 
    tabelle_lose_extern.Vergabe_abgeschlossen, 
    tabelle_lose_extern.Kostenanschlag, 
    tabelle_lieferant.Lieferant, 
    tabelle_lieferant.idTABELLE_Lieferant,
    tabelle_projekte.Projektname,
    tabelle_projekte.idTABELLE_Projekte,
    mkf_los.LosNr_Extern AS mkf_losnummer
FROM tabelle_lieferant 
RIGHT JOIN tabelle_lose_extern ON tabelle_lieferant.idTABELLE_Lieferant = tabelle_lose_extern.tabelle_lieferant_idTABELLE_Lieferant
LEFT JOIN (
    SELECT tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern AS id, 
           Sum(tabelle_räume_has_tabelle_elemente.`Anzahl`*tabelle_projekt_varianten_kosten.`Kosten`) AS Summe,
           tabelle_räume.tabelle_projekte_idTABELLE_Projekte
    FROM tabelle_räume 
    INNER JOIN (tabelle_projekt_varianten_kosten 
        INNER JOIN tabelle_räume_has_tabelle_elemente 
        ON tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten 
        AND tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente)
    ON tabelle_räume.tabelle_projekte_idTABELLE_Projekte = tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte 
    AND tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume
    WHERE tabelle_räume_has_tabelle_elemente.`Neu/Bestand`=1 
      AND tabelle_räume_has_tabelle_elemente.Standort=1 
    GROUP BY tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern,
             tabelle_räume.tabelle_projekte_idTABELLE_Projekte
) AS losschaetzsumme ON tabelle_lose_extern.idtabelle_Lose_Extern = losschaetzsumme.id
LEFT JOIN (
    SELECT tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern AS id, 
           Sum(tabelle_räume_has_tabelle_elemente.`Anzahl`*tabelle_projekt_varianten_kosten.`Kosten`) AS SummeBestand,
           tabelle_räume.tabelle_projekte_idTABELLE_Projekte
    FROM tabelle_räume 
    INNER JOIN (tabelle_projekt_varianten_kosten 
        INNER JOIN tabelle_räume_has_tabelle_elemente 
        ON tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten 
        AND tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente)
    ON tabelle_räume.tabelle_projekte_idTABELLE_Projekte = tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte 
    AND tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume
    WHERE tabelle_räume_has_tabelle_elemente.`Neu/Bestand`=0 
      AND tabelle_räume_has_tabelle_elemente.Standort=1 
    GROUP BY tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern,
             tabelle_räume.tabelle_projekte_idTABELLE_Projekte
) AS losbestandschaetzsumme ON tabelle_lose_extern.idtabelle_Lose_Extern = losbestandschaetzsumme.id
LEFT JOIN tabelle_projekte ON tabelle_projekte.idTABELLE_Projekte = COALESCE(losschaetzsumme.tabelle_projekte_idTABELLE_Projekte, losbestandschaetzsumme.tabelle_projekte_idTABELLE_Projekte)
LEFT JOIN tabelle_lose_extern AS mkf_los ON tabelle_lose_extern.mkf_von_los = mkf_los.idtabelle_Lose_Extern
WHERE tabelle_lose_extern.Versand_LV >= ? AND idTABELLE_Projekte <> 4 AND idTABELLE_Projekte <> 1
ORDER BY tabelle_projekte.Projektname, tabelle_lose_extern.LosNr_Extern
";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $datum);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    if (empty($row["Projektname"]) || $row["Projektname"] === "Test_Projekt" ||
        stripos($row["LosBezeichnung_Extern"] ?? "", "löschen") !== false ||
        stripos($row["LosBezeichnung_Extern"] ?? "", "ENTFÄLLT") !== false ||
        stripos($row["LosBezeichnung_Extern"] ?? "", "Entfallen") !== false ||
        empty($row["Verfahren"])) continue;

    $status = match((int)$row["Vergabe_abgeschlossen"]) {
        0 => "<span class='badge bg-danger'>Offen</span>",
        1 => "<span class='badge bg-success'>Fertig</span>",
        2 => "<span class='badge bg-primary'>Wartend</span>",
        default => ""
    };

    $data[] = [
        $row["idtabelle_Lose_Extern"],
        $row["Projektname"],
        $row["LosNr_Extern"],
        $row["LosBezeichnung_Extern"],
        $row["Versand_LV"],
        $row["Ausführungsbeginn"],
        "<span class='badge rounded-pill " . getVerfahrenBadgeClass($row['Verfahren']) . "'>" . htmlspecialchars($row['Verfahren']) . "</span>",
        $status,
        format_money($row["Vergabesumme"]),
        $row["Vergabesumme"],
        $row["Lieferant"],
        $row["mkf_losnummer"],
        "<button type='button' id='{$row["idtabelle_Lose_Extern"]}' class='btn btn-sm btn-outline-secondary' value='LotWorkflow' data-bs-toggle='modal' data-bs-target='#workflowDataModal'><i class='fas fa-history'></i></button>"
    ];
}

echo json_encode(['data' => $data]);
$mysqli->close();
?>
