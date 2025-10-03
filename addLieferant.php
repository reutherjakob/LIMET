<?php
// 10-2025 FX
require_once 'utils/_utils.php';
check_login();

$Name = getPostString('Name');
$Vorname = getPostString('Vorname');
$Tel = getPostString('Tel');

if ($Name !== '' && $Vorname !== '' && $Tel !== '') {
    $Adresse = getPostString('Adresse');
    $PLZ = getPostString('PLZ');
    $Ort = getPostString('Ort');
    $Land = getPostString('Land');
    $Email = getPostString('Email');
    $gebiet = getPostString('gebiet');
    $abteilung = getPostInt('abteilung', 0);
    $lieferant = getPostInt('lieferant', 0);

    $mysqli = utils_connect_sql();
    $stmt = $mysqli->prepare("
        INSERT INTO tabelle_ansprechpersonen
        (Name, Vorname, Tel, Adresse, PLZ, Ort, Land, Mail, Gebietsbereich, tabelle_abteilung_idtabelle_abteilung, tabelle_lieferant_idTABELLE_Lieferant)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param(
        "ssssssssiii",
        $Name,
        $Vorname,
        $Tel,
        $Adresse,
        $PLZ,
        $Ort,
        $Land,
        $Email,
        $gebiet,
        $abteilung,
        $lieferant
    );

    if ($stmt->execute()) {
        echo "Kontaktperson hinzugefügt!";
        $id = $mysqli->insert_id;
    } else {
        echo "Fehler: " . $stmt->error;
    }
    $stmt->close();
    $mysqli->close();
} else {
    echo "Fehler bei der Eingabe: Name, Vorname und Tel sind Pflicht!";
}
?>
