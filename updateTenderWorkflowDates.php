<?php
// 25 FX
require_once 'utils/_utils.php';
check_login();
$mysqli = utils_connect_sql();
$lotID = getPostInt('lotID');

$sql = "SELECT tabelle_workflow_has_tabelle_wofklowteil.Reihenfolgennummer, DATE_FORMAT(DATE(tabelle_lot_workflow.Timestamp_Soll), '%Y-%m-%d') as Timestamp_Soll, tabelle_workflow_has_tabelle_wofklowteil.TageMinDanach, tabelle_lot_workflow.tabelle_lose_extern_idtabelle_Lose_Extern, tabelle_lot_workflow.tabelle_workflow_idtabelle_workflow, tabelle_lot_workflow.tabelle_wofklowteil_idtabelle_wofklowteil FROM tabelle_lot_workflow INNER JOIN tabelle_workflow_has_tabelle_wofklowteil ON tabelle_lot_workflow.tabelle_workflow_idtabelle_workflow = tabelle_workflow_has_tabelle_wofklowteil.tabelle_workflow_idtabelle_workflow AND tabelle_lot_workflow.tabelle_wofklowteil_idtabelle_wofklowteil = tabelle_workflow_has_tabelle_wofklowteil.tabelle_wofklowteil_idtabelle_wofklowteil WHERE tabelle_lot_workflow.tabelle_lose_extern_idtabelle_Lose_Extern = ? ORDER BY tabelle_workflow_has_tabelle_wofklowteil.Reihenfolgennummer DESC";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param('i', $lotID);
$stmt->execute();
$result = $stmt->get_result();
$workflowTeile = array();
$counter = 0;
while ($row = $result->fetch_assoc()) {
    $workflowTeile[$counter]['tabelle_workflow_idtabelle_workflow'] = $row['tabelle_workflow_idtabelle_workflow'];
    $workflowTeile[$counter]['tabelle_wofklowteil_idtabelle_wofklowteil'] = $row['tabelle_wofklowteil_idtabelle_wofklowteil'];
    $workflowTeile[$counter]['Timestamp_Soll'] = $row['Timestamp_Soll'];
    $workflowTeile[$counter]['TageMinDanach'] = $row['TageMinDanach'];
    $counter++;
}
$stmt->close();
$counter = 0;
$tageDanach = 0;
$oldDate = 0;
$ausgabe = "";
$updateSql = "UPDATE `LIMET_RB`.`tabelle_lot_workflow` SET `Timestamp_Soll` = ? WHERE `tabelle_lose_extern_idtabelle_Lose_Extern` = ? AND `tabelle_workflow_idtabelle_workflow` = ? AND `tabelle_wofklowteil_idtabelle_wofklowteil` = ?";
$updateStmt = $mysqli->prepare($updateSql);
foreach ($workflowTeile as $array) {
    if ($counter > 0) {
        $newDate = date('Y-m-d', strtotime(($oldDate . " - {$array['TageMinDanach']} days") ?? ''));
        $ausgabe = $ausgabe . " " . $oldDate . " " . $array['TageMinDanach'] . " " . date('N', strtotime($newDate ?? ''));
        $wochentag = date('N', strtotime($newDate ?? ''));
        if ($wochentag == 6) {
            $newDate = date('Y-m-d', strtotime(($newDate . " - 1 days") ?? ''));
        } else {
            if ($wochentag == 7) {
                $newDate = date('Y-m-d', strtotime(($newDate . " - 2 days") ?? ''));
            }
        }
        $ausgabe = $ausgabe . " " . $newDate;
        $tsSoll = date('Y-m-d', strtotime($newDate ?? ''));
        $workflowId = (int)$array['tabelle_workflow_idtabelle_workflow'];
        $workflowTeilId = (int)$array['tabelle_wofklowteil_idtabelle_wofklowteil'];
        $updateStmt->bind_param('siii', $tsSoll, $lotID, $workflowId, $workflowTeilId);
        if ($updateStmt->execute()) {
            $ausgabe = $ausgabe . " Workflowteil " . $workflowTeilId . " erfolgreich aktualisiert! \n";
        } else {
            $ausgabe = $ausgabe . " Error: " . $updateStmt->error;
        }
        $oldDate = $newDate;
    } else {
        $oldDate = $array['Timestamp_Soll'];
    }
    $counter++;
}
$updateStmt->close();
$mysqli->close();
echo $ausgabe;
?>