<?php
if (!function_exists('utils_connect_sql')) {  include "_utils.php"; }
check_login();
$mysqli = utils_connect_sql();

function insertLos($mysqli, $data, $isMainLos = true) {
    $commonFields = [
        'LosNr_Extern' => $data['losNr'],
        'LosBezeichnung_Extern' => $data['losName'],
        'Ausführungsbeginn' => date("Y-m-d", strtotime($data['losDatum']) ?? ''),
        'tabelle_projekte_idTABELLE_Projekte' => $_SESSION["projectID"],
        'Vergabe_abgeschlossen' => $data['lotVergabe'],
        'Versand_LV' => date("Y-m-d", strtotime($data['lotLVSend']) ?? ''),
        'Verfahren' => $data['lotVerfahren'],
        'Bearbeiter' => $data['lotLVBearbeiter'],
        'Notiz' => $data['lotNotice'],
        'Kostenanschlag' => $data['kostenanschlag'],
        'Budget' => $data['budget']
    ];

    if (!$isMainLos) {
        $commonFields['mkf_von_los'] = $data['lotMKFOf'];
        $commonFields['mkf_nr'] = $data['laufendeNr'];
    }

    if (isset($data['lotSum'])) {
        $commonFields['Vergabesumme'] = $data['lotSum'];
    }

    if ($data['lotAuftragnehmer'] != 0) {
        $commonFields['tabelle_lieferant_idTABELLE_Lieferant'] = $data['lotAuftragnehmer'];
    }

    $columns = implode(", ", array_keys($commonFields));
    $values = "'" . implode("', '", array_values($commonFields)) . "'";

    $sql = "INSERT INTO `LIMET_RB`.`tabelle_lose_extern` ($columns) VALUES ($values)";

    if ($mysqli->query($sql) === TRUE) {
        echo "Los zu Projekt hinzugefügt!";
    } else {
        echo "Error: " . $sql . "<br>" . $mysqli->error;
    }
}

$data = $_GET;
$data['lotAuftragnehmer'] = filter_input(INPUT_GET, 'lotAuftragnehmer', FILTER_VALIDATE_INT);
$data['lotMKFOf'] = filter_input(INPUT_GET, 'lotMKFOf', FILTER_VALIDATE_INT);

if ($data['lotMKFOf'] == 0) {
    insertLos($mysqli, $data);
} else {
    // MKF anlegen
    $sqlMKF = "SELECT Max(mkf_nr) AS Maxvonmkf_nr FROM tabelle_lose_extern WHERE mkf_von_los = " . $data['lotMKFOf'];
    $resultMKFNr = $mysqli->query($sqlMKF);
    $laufendeNr = ($resultMKFNr->fetch_assoc()['Maxvonmkf_nr'] ?? 0) + 1;

    $sqlLosDaten = "SELECT LosNr_Extern, LosBezeichnung_Extern FROM tabelle_lose_extern WHERE idtabelle_Lose_Extern = " . $data['lotMKFOf'];
    $resultLosDaten = $mysqli->query($sqlLosDaten);
    $losDaten = $resultLosDaten->fetch_assoc();

    $data['losNr'] = $losDaten['LosNr_Extern'] . "." . $laufendeNr;
    $data['losName'] = $losDaten['LosBezeichnung_Extern'];
    $data['laufendeNr'] = $laufendeNr;

    insertLos($mysqli, $data, false);
}

$mysqli->close();
?>
