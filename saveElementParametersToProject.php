<?php
require_once 'utils/_utils.php';
check_login();

$mysqli = utils_connect_sql();

$elementID = getPostInt('elementID', 0);
$variantenID = getPostInt('variantenID', 0);
$projectID = (int)($_SESSION['projectID'] ?? 0);

if ($elementID === 0 || $variantenID === 0 || $projectID === 0) {
    echo "Fehler: Ungültige Parameter (Element / Variante / Projekt).";
    $mysqli->close();
    exit;
}

// Planungsphase des Projekts aus der DB holen (zuverlässig, nicht aus Session-Text)
// $stmtPhase = $mysqli->prepare("
//     SELECT TABELLE_Planungsphasen_idTABELLE_Planungsphasen
//     FROM tabelle_projekte
//     WHERE idTABELLE_Projekte = ?
// ");
// $stmtPhase->bind_param('i', $projectID);
// $stmtPhase->execute();
// $stmtPhase->bind_result($phaseID);
// if (!$stmtPhase->fetch() || !$phaseID) {
//     $stmtPhase->close();
//     $mysqli->close();
//     echo "Fehler: Planungsphase des Projekts nicht gefunden.";
//     exit;
// }
// $stmtPhase->close();


$phaseID = 1;
// Zentrale Parameter des Elements laden
$stmtSel = $mysqli->prepare("
    SELECT TABELLE_Parameter_idTABELLE_Parameter AS pid, Wert, Einheit
    FROM tabelle_elemente_has_tabelle_parameter
    WHERE TABELLE_Elemente_idTABELLE_Elemente = ?
");
$stmtSel->bind_param('i', $elementID);
$stmtSel->execute();
$params = $stmtSel->get_result()->fetch_all(MYSQLI_ASSOC);
$stmtSel->close();

if (empty($params)) {
    $mysqli->close();
    echo "Keine zentralen Parameter vorhanden – nichts übernommen.";
    exit;
}

// Transaktion: erst alte Projektparameter dieser Variante+Phase löschen, dann neu einfügen
$mysqli->begin_transaction();
$ok = true;
$err = '';

$stmtDel = $mysqli->prepare("
    DELETE FROM tabelle_projekt_elementparameter
    WHERE tabelle_projekte_idTABELLE_Projekte = ?
      AND tabelle_elemente_idTABELLE_Elemente = ?
      AND tabelle_Varianten_idtabelle_Varianten = ?
      AND tabelle_planungsphasen_idTABELLE_Planungsphasen = ?
");
$stmtDel->bind_param('iiii', $projectID, $elementID, $variantenID, $phaseID);
if (!$stmtDel->execute()) {
    $ok = false;
    $err = $stmtDel->error;
}
$stmtDel->close();

if ($ok) {
    $stmtIns = $mysqli->prepare("
        INSERT INTO tabelle_projekt_elementparameter
            (tabelle_projekte_idTABELLE_Projekte, tabelle_elemente_idTABELLE_Elemente,
             tabelle_Varianten_idtabelle_Varianten, tabelle_parameter_idTABELLE_Parameter,
             Wert, Einheit, tabelle_planungsphasen_idTABELLE_Planungsphasen)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    foreach ($params as $p) {
        $stmtIns->bind_param('iiiissi',
            $projectID, $elementID, $variantenID, $p['pid'], $p['Wert'], $p['Einheit'], $phaseID);
        if (!$stmtIns->execute()) {
            $ok = false;
            $err = $stmtIns->error;
            break;
        }
    }
    $stmtIns->close();
}

if ($ok) {
    $mysqli->commit();
    echo count($params) . " Parameter ins Projekt übernommen (Variante " . $variantenID . ", Phase " . $phaseID . ").";
} else {
    $mysqli->rollback();
    echo "Fehler – nichts geändert: " . $err;
}

$mysqli->close();
?>