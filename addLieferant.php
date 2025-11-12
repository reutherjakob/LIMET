<?php
// 11-2025 FX
require_once 'utils/_utils.php';
check_login();

$name = getPostString('Name');
$vorname = getPostString('Vorname');
$tel = getPostString('Tel');

if ($name !== '' && $vorname !== '' && $tel !== '') {
    $adresse = getPostString('Adresse');
    $plz = getPostString('PLZ');
    $ort = getPostString('Ort');
    $land = getPostString('Land');
    $email = getPostString('Email');
    $gebiet = getPostString('gebiet');
    $abteilung = getPostInt('abteilung');
    $lieferant = getPostInt('lieferant');

    $mysqli = utils_connect_sql();

    $sql = "INSERT INTO tabelle_ansprechpersonen
        (Name, Vorname, Tel, Adresse, PLZ, Ort, Land, Mail, Gebietsbereich, tabelle_abteilung_idtabelle_abteilung, tabelle_lieferant_idTABELLE_Lieferant)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        echo "Fehler beim Vorbereiten der Anfrage: " . $mysqli->error;
        exit;
    }

    $stmt->bind_param('ssssssssiii',
        $name,
        $vorname,
        $tel,
        $adresse,
        $plz,
        $ort,
        $land,
        $email,
        $gebiet,
        $abteilung,
        $lieferant
    );

    if ($stmt->execute()) {
        echo "Kontaktperson hinzugefügt!";
        $id = $stmt->insert_id;
    } else {
        echo "Fehler bei der Ausführung: " . $stmt->error;
    }

    $stmt->close();
    $mysqli->close();
} else {
    echo "Fehler: Pflichtfelder Name, Vorname und Tel müssen ausgefüllt sein.";
}
?>
