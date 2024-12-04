<?php
include '_utils.php';
$conn = utils_connect_sql();
$sql = "SELECT REPLACE(tabelle_projekt_varianten_kosten.Kosten, '.', ',') AS modified_kosten
        FROM tabelle_projekt_varianten_kosten
        WHERE tabelle_projekt_varianten_kosten.Kosten LIKE '%.%'";

$result = $conn->query($sql);
////include '_utils.php';
//init_page_serversides("x", "x");
//$mysqli = utils_connect_sql();$externIds = [1425];
//$workflowteilIds = [5, 31, 32, 33, 34, 25, 26, 27];
//foreach ($externIds as $externId) {
//    foreach ($workflowteilIds as $workflowteil) {
//
//        $sql = "INSERT INTO LIMET_RB.tabelle_lot_workflow (tabelle_lose_extern_idtabelle_Lose_Extern,
//                                           tabelle_workflow_idtabelle_workflow,
//                                           tabelle_wofklowteil_idtabelle_wofklowteil)
//    VALUES ($externId, 17, $workflowteil)";
//        if ($mysqli->query($sql) === TRUE) {
//            echo "New records created successfully for externId $externId and workflowteil $workflowteil\n";
//        } else {
//            echo "Error: " . $sql . "\n" . $mysqli->error;
//        }
//    }
//}
//
