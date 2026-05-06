<?php
require_once 'utils/_utils.php';
header('Content-Type: application/json; charset=utf-8');
$mysqli = utils_connect_sql();
$projectID = (int)$_SESSION["projectID"];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['budgetID'])) {
    $budgetID = (int)$_POST['budgetID'];
    $status   = (int)$_POST['status'];
    $stmt = $mysqli->prepare("UPDATE tabelle_projektbudgets SET status=? WHERE idtabelle_projektbudgets=?");
    $stmt->bind_param('ii', $status, $budgetID);
    $stmt->execute();
    echo json_encode(['success' => true]);
    exit;
}

$stmt = $mysqli->prepare("SELECT idtabelle_projektbudgets, Budgetnummer, Budgetname, status 
                           FROM tabelle_projektbudgets 
                           WHERE tabelle_projekte_idTABELLE_Projekte = ? 
                           ORDER BY Budgetnummer");
$stmt->bind_param('i', $projectID);
$stmt->execute();
$result = $stmt->get_result();
$budgets = [];
while ($row = $result->fetch_assoc()) $budgets[] = $row;
echo json_encode($budgets);