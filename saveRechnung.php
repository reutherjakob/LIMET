<?php
// 25 FX
require_once 'utils/_utils.php';
check_login();

$mysqli = utils_connect_sql();

$rechnungId                = getPostInt('rechnungID');
$rechnungNr                = getPostInt('rechnungNr');
$teilRechnungNr            = getPostInt('teilRechnungNr');
$rechnungAusstellungsdatum = getPostDate('rechnungAusstellungsdatum');
$rechnungEingangsdatum     = getPostDate('rechnungEingangsdatum');
$rechnungSum               = getPostFloat('rechnungSum');
$rechnungBearbeiter        = getPostString('rechnungBearbeiter');
$rechnungSchlussrechnung   = getPostInt('rechnungSchlussrechnung');

$stmt = $mysqli->prepare("
    UPDATE `LIMET_RB`.`tabelle_rechnungen`
    SET
        `Nummer` = ?,
        `InterneNummer` = ?,
        `Ausstellungsdatum` = ?,
        `Eingangsdatum` = ?,
        `Rechnungssumme` = ?,
        `Bearbeiter` = ?,
        `Schlussrechnung` = ?
    WHERE `idtabelle_rechnungen` = ?
");

$stmt->bind_param(
    "iissdsii",
    $rechnungNr,
    $teilRechnungNr,
    $rechnungAusstellungsdatum,
    $rechnungEingangsdatum,
    $rechnungSum,
    $rechnungBearbeiter,
    $rechnungSchlussrechnung,
    $rechnungId
);

if ($stmt->execute()) {
    $ausgabe = "Rechnung erfolgreich aktualisiert! \n";
} else {
    $ausgabe = "Error: " . $stmt->error;
}

$stmt->close();
$mysqli->close();
echo $ausgabe;
