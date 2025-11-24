<?php
// 25 FX
require_once 'utils/_utils.php';
include "utils/_format.php";
header('Content-Type: application/json; charset=utf-8');

$mysqli = utils_connect_sql();
$projectID = (int)$_SESSION["projectID"];

$sqlBudgets = "SELECT idtabelle_projektbudgets, Budgetnummer, Budgetname
               FROM tabelle_projektbudgets
               WHERE tabelle_projekte_idTABELLE_Projekte = ?
               ORDER BY Budgetnummer";
$stmtBudgets = $mysqli->prepare($sqlBudgets);
$stmtBudgets->bind_param('i', $projectID);
$stmtBudgets->execute();
$resultBudgets = $stmtBudgets->get_result();

$projectBudgets = [];
while ($row = $resultBudgets->fetch_assoc()) {
    $projectBudgets[$row['idtabelle_projektbudgets']] = $row;
}

// Main query to get elements and join required data
$sql = "SELECT 
        tre_an.Anzahl, 
        te.ElementID, 
        te.Bezeichnung, 
        tv.Variante,
        tr.`Raumbereich Nutzer` AS Ausdr1, 
        tr.Raumnr, 
        tr.Raumbezeichnung, 
        tre_an.`Neu/Bestand` AS Ausdr2,
        tpk.Kosten, 
        tpk.Kosten * tre_an.Anzahl AS PP, 
        tpb.Budgetnummer,
        tre_an.id, 
        tpb.idtabelle_projektbudgets
    FROM tabelle_projektbudgets tpb
    RIGHT JOIN (
        tabelle_projekt_varianten_kosten tpk
        INNER JOIN (
            tabelle_varianten tv
            INNER JOIN (
                (tabelle_räume tr
                INNER JOIN tabelle_räume_has_tabelle_elemente tre_an
                    ON tr.idTABELLE_Räume = tre_an.TABELLE_Räume_idTABELLE_Räume)
            INNER JOIN tabelle_elemente te
                ON tre_an.TABELLE_Elemente_idTABELLE_Elemente = te.idTABELLE_Elemente
            )
            ON tv.idtabelle_Varianten = tre_an.tabelle_Varianten_idtabelle_Varianten
        )
        ON tpk.tabelle_projekte_idTABELLE_Projekte = tr.tabelle_projekte_idTABELLE_Projekte
           AND tpk.tabelle_elemente_idTABELLE_Elemente = tre_an.TABELLE_Elemente_idTABELLE_Elemente
           AND tpk.tabelle_Varianten_idtabelle_Varianten = tre_an.tabelle_Varianten_idtabelle_Varianten
    )
    ON tpb.idtabelle_projektbudgets = tre_an.tabelle_projektbudgets_idtabelle_projektbudgets
    WHERE tr.tabelle_projekte_idTABELLE_Projekte = ?
      AND tre_an.Standort = 1
      AND tre_an.Anzahl <> 0
    ORDER BY te.ElementID, tv.Variante";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param('i', $projectID);
$stmt->execute();
$result = $stmt->get_result();

$data = [];

function renderBudgetSelect($rowID, $selectedBudgetID, $budgets)
{
    $html = '<select class="form-control form-control-sm" id="' . htmlspecialchars($rowID) . '">';
    $html .= '<option value="0"' . ($selectedBudgetID == 0 ? ' selected' : '') . '>0-Budget wählen</option>';
    foreach ($budgets as $id => $budget) {
        $selected = ($id == $selectedBudgetID) ? ' selected' : '';
        $optionText = htmlspecialchars($budget['idtabelle_projektbudgets'] . "-" . $budget['Budgetnummer'] . "-" . $budget['Budgetname']);
        $html .= '<option value="' . htmlspecialchars($id) . '"' . $selected . '>' . $optionText . '</option>';
    }
    $html .= '</select>';
    return $html;
}

while ($row = $result->fetch_assoc()) {
    $selectedBudgetID = $row['idtabelle_projektbudgets'] ?? 0;
    $selectedBudgetText = '';
    if ($selectedBudgetID && isset($projectBudgets[$selectedBudgetID])) {
        $b = $projectBudgets[$selectedBudgetID];
        $selectedBudgetText = $b['idtabelle_projektbudgets'] . "-" . $b['Budgetnummer'] . "-" . $b['Budgetname'];
    }

    $rowData = [];
    $rowData['id'] = $row['id'];
    $rowData['idtabelle_projektbudgets'] = $row['idtabelle_projektbudgets'];
    $rowData['Anzahl'] = $row['Anzahl'];
    $rowData['ElementID'] = $row['ElementID'];
    $rowData['Bezeichnung'] = $row['Bezeichnung'];

    $rowData['Ausdr1'] = $row['Ausdr1'];
    $rowData['RaumFull'] = $row['Raumnr'] . "-" . $row['Raumbezeichnung'];
    $rowData['Ausdr2'] = ($row['Ausdr2'] == 1) ? 'Nein' : 'Ja';
    $rowData['Variante'] = $row['Variante'];
    $rowData['Kosten'] = format_money($row['Kosten']);
    $rowData['PP'] = format_money($row['PP']);
    //  $rowData['BudgetSelect'] = renderBudgetSelect($row['id'], $row['idtabelle_projektbudgets'] ?? 0, $projectBudgets);
    $rowData['BudgetSelect'] = renderBudgetSelect($row['id'], $selectedBudgetID, $projectBudgets);
    $rowData['BudgetID'] = $selectedBudgetID;
    $rowData['BudgetText'] = $selectedBudgetText;
    $data[] = $rowData;
}

$mysqli->close();

echo json_encode($data, JSON_UNESCAPED_UNICODE);
