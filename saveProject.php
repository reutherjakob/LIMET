<?php
// 25 FX
require_once "utils/_utils.php";
check_login();
$mysqli = utils_connect_sql();
$betten = getPostInt('betten');
$bgf = getPostFloat('bgf');
$nf = getPostFloat('nf');
$aktiv = getPostInt('active');
$neubau = getPostInt('neubau');
$planungsphase = getPostInt('planungsphase');
$ausfuehrung = getPostString('bearbeitung');
$preisbasis = getPostDate('PBdate');

$sql = "UPDATE `LIMET_RB`.`tabelle_projekte` SET `Bettenanzahl` = ?, `BGF` = ?, `NF` = ?, `Aktiv` = ?, `Neubau` = ?, `TABELLE_Planungsphasen_idTABELLE_Planungsphasen` = ?, `Ausfuehrung` = ?, `Preisbasis` = ? WHERE `idTABELLE_Projekte` = ?";
$stmt = $mysqli->prepare($sql);

$stmt->bind_param("iddiiissi", $betten, $bgf, $nf, $aktiv, $neubau, $planungsphase, $ausfuehrung, $preisbasis, $_SESSION["projectID"]);
if ($stmt->execute()) {
    echo "Projekt aktualisiert!";
} else {
    echo "Error: " . $stmt->error;
}
$stmt->close();
$mysqli->close(); ?>