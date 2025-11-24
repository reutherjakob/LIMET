<?php
require_once "utils/_utils.php";
check_login();
$mysqli= utils_connect_sql();

$sql_new = "SELECT 
    Sum(tabelle_räume_has_tabelle_elemente.Anzahl * tabelle_projekt_varianten_kosten.Kosten) AS Summe_Neu,
    tabelle_elemente.ElementID
FROM 
    tabelle_räume_has_tabelle_elemente 
INNER JOIN 
    tabelle_projekt_varianten_kosten 
    ON (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) 
    AND (tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten = tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten)
INNER JOIN
    tabelle_elemente
    ON tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente = tabelle_elemente.idTABELLE_Elemente
WHERE 
    tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume = ? 
    AND tabelle_räume_has_tabelle_elemente.Standort = 1 
    AND tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte = ? 
    AND tabelle_räume_has_tabelle_elemente.`Neu/Bestand` = 1
GROUP BY tabelle_elemente.ElementID;";

$sql_existing = "SELECT 
    Sum(tabelle_räume_has_tabelle_elemente.Anzahl * tabelle_projekt_varianten_kosten.Kosten) AS Summe_Bestand,
    tabelle_elemente.ElementID
FROM 
    tabelle_räume_has_tabelle_elemente 
INNER JOIN 
    tabelle_projekt_varianten_kosten 
    ON (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) 
    AND (tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten = tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten)
INNER JOIN
    tabelle_elemente
    ON tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente = tabelle_elemente.idTABELLE_Elemente
WHERE 
    tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume = ?
    AND tabelle_räume_has_tabelle_elemente.Standort = 1 
    AND tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte = ?
    AND tabelle_räume_has_tabelle_elemente.`Neu/Bestand` = 0
GROUP BY tabelle_elemente.ElementID;";
function calculateCosts($mysqli, $sql, $roomID, $projectID): array
{
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("ii", $roomID, $projectID);
    $stmt->execute();
    $result = $stmt->get_result();

    $sum = 0;
    $costs = ['ortsfest' => 0, 'ortsveränderlich' => 0];

    while ($row = $result->fetch_assoc()) {
        $summe = isset($row["Summe_Neu"]) ? (float)$row["Summe_Neu"] : (float)$row["Summe_Bestand"];
        $sum += $summe;
        if (str_starts_with($row["ElementID"] ?? '', '1') || str_starts_with($row["ElementID"] ?? '', '3') || str_starts_with($row["ElementID"] ?? '', '4') || str_starts_with($row["ElementID"] ?? '', '5')) {
            $costs['ortsfest'] += $summe;
        } else {
            $costs['ortsveränderlich'] += $summe;
        }

    }

    return ['sum' => $sum, 'costs' => $costs];
}

// Calculate costs
$new_costs = calculateCosts($mysqli, $sql_new, $_SESSION["roomID"], $_SESSION["projectID"]);
$existing_costs = calculateCosts($mysqli, $sql_existing, $_SESSION["roomID"], $_SESSION["projectID"]);

$SummeNeu = $new_costs['sum'];
$SummeBestand = $existing_costs['sum'];
$SummeGesamt = $SummeNeu + $SummeBestand;
$Kosten_ortsfest = $new_costs['costs']['ortsfest'] + $existing_costs['costs']['ortsfest'];
$Kosten_ortsveränderlich = $new_costs['costs']['ortsveränderlich'] + $existing_costs['costs']['ortsveränderlich'];

// Format money values
$formattedNumberGesamt = format_money_report($SummeGesamt);
$formattedNumberNeu = format_money_report($SummeNeu);
$formattedNumberBestand = format_money_report($SummeBestand);
$formattedKostenOrtsfest = format_money_report($Kosten_ortsfest);
$formattedKostenOrtsveränderlich = format_money_report($Kosten_ortsveränderlich);
$cost_fields = [
    'kosten_gesamt' => ['label' => 'Raumkosten', 'value' => $formattedNumberGesamt],
    'kosten_neu' => ['label' => 'Neu', 'value' => $formattedNumberNeu],
    'kosten_bestand' => ['label' => 'Bestand', 'value' => $formattedNumberBestand],
    'kosten_ortsfest' => ['label' => ' OF', 'value' => $formattedKostenOrtsfest],
    'kosten_ortsveränderlich' => ['label' => ' OV', 'value' => $formattedKostenOrtsveränderlich]
];
 Echo'<h5>Kosten Details</h5><small>
            Alle Elemente, deren ID mit 1, 3, 4 oder 5 beginnen, sind als ortsfest erfasst. Andere sind
            ortsveränderlich.</small>
        <ul class="list-unstyled mb-0">
            <?php foreach ($cost_fields as $field): ?>
                <li><strong><?php echo $field["label"]; ?>:</strong> <?php echo $field["value"]; ?></li>
            <?php endforeach; ?>
        </ul>     ';