<?php
// setTenderLot.php - FIXED VERSION
require_once 'utils/_utils.php';
check_login();

$mysqli = utils_connect_sql();

$lotID = getPostInt('lotID', 0);
if ($lotID <= 0) {
    die("Ungültige Lot-ID.");
}

$losNr = $_POST['losNr'] ?? '';
$losName = $_POST['losName'] ?? '';
$losDatum = $_POST['losDatum'] ?? '';
$lotSum = $_POST['lotSum'] ?? '0';
$lotVergabe = $_POST['lotVergabe'] ?? '0';
$lotNotice = $_POST['lotNotice'] ?? '';
$lotAuftragnehmer = $_POST['lotAuftragnehmer'] ?? '0';
$lotLVSend = $_POST['lotLVSend'] ?? '';
$lotVerfahren = $_POST['lotVerfahren'] ?? '';
$lotLVBearbeiter = $_POST['lotLVBearbeiter'] ?? '';
$kostenanschlag = $_POST['kostenanschlag'] ?? '0';
$budget = $_POST['budget'] ?? '0';
$includeSupplier = ($lotAuftragnehmer != '0' && $lotAuftragnehmer != '');


if ($includeSupplier) {
    $sql = "UPDATE `LIMET_RB`.`tabelle_lose_extern` SET 
        `LosNr_Extern` = ?, `LosBezeichnung_Extern` = ?, `Ausführungsbeginn` = ?,
        `Vergabesumme` = ?, `Vergabe_abgeschlossen` = ?, `Notiz` = ?,
        `tabelle_lieferant_idTABELLE_Lieferant` = ?, `Versand_LV` = ?,
        `Verfahren` = ?, `Bearbeiter` = ?, `Kostenanschlag` = ?, `Budget` = ?
    WHERE `idtabelle_Lose_Extern` = ?";

    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param(
        'sssississsddi',
        $losNr, $losName, $losDatum, $lotSum, $lotVergabe, $lotNotice, $lotAuftragnehmer, $lotLVSend, $lotVerfahren, $lotLVBearbeiter,
        $kostenanschlag, $budget, $lotID
    );
} else {
    $sql = "UPDATE `LIMET_RB`.`tabelle_lose_extern` SET 
        `LosNr_Extern` = ?, `LosBezeichnung_Extern` = ?, `Ausführungsbeginn` = ?,
        `Vergabesumme` = ?, `Vergabe_abgeschlossen` = ?, `Notiz` = ?,
        `tabelle_lieferant_idTABELLE_Lieferant` = NULL, `Versand_LV` = ?,
        `Verfahren` = ?, `Bearbeiter` = ?, `Kostenanschlag` = ?, `Budget` = ?
    WHERE `idtabelle_Lose_Extern` = ?";

    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param(
        'sssisssssddi',   #Summe als float?
        $losNr, $losName, $losDatum, $lotSum, $lotVergabe, $lotNotice,
        $lotLVSend, $lotVerfahren, $lotLVBearbeiter, $kostenanschlag, $budget, $lotID
    );
}

if ($stmt->execute()) {
    echo "Los erfolgreich aktualisiert! " ;
} else {
    echo "Fehler beim Ausführen: " . $stmt->error;
}

$stmt->close();
$mysqli->close();
?>
