<?php
// 11-2025 FX
require_once 'utils/_utils.php';
check_login();
$mysqli = utils_connect_sql();

function logPostData(array $data): void
{
    $logfile = __DIR__ . '/addTenderLot_post_log.txt';
    $logEntry = "[" . date('Y-m-d H:i:s') . "] POST Data: " . json_encode($data, JSON_PRETTY_PRINT) . PHP_EOL;
    file_put_contents($logfile, $logEntry, FILE_APPEND);
}

function insertLos($mysqli, $data, $isMainLos = true): void
{
    $losNr = $mysqli->real_escape_string($data['losNr']);
    $losName = $mysqli->real_escape_string($data['losName']);
    $ausfBeginn = $mysqli->real_escape_string($data['losDatum']);
    $projektID = intval($_SESSION["projectID"]);
    $vergabeAbgeschlossen = intval($data['lotVergabe']);
    $versandLV = $mysqli->real_escape_string($data['lotLVSend']);
    $verfahren = $mysqli->real_escape_string($data['lotVerfahren']);
    $bearbeiter = $mysqli->real_escape_string($data['lotLVBearbeiter']);
    $notiz = $mysqli->real_escape_string($data['lotNotice']);
    $kostenanschlag = isset($data['kostenanschlag']) ? floatval(str_replace(',', '.', $data['kostenanschlag'])) : 0;
    $budget = isset($data['budget']) ? floatval(str_replace(',', '.', $data['budget'])) : 0;
    $vergabesumme = isset($data['lotSum']) ? floatval(str_replace(',', '.', $data['lotSum'])) : null;
    $lieferantID = (isset($data['lotAuftragnehmer']) && $data['lotAuftragnehmer'] != 0) ? intval($data['lotAuftragnehmer']) : null;

    $columns = [
        'LosNr_Extern', 'LosBezeichnung_Extern', 'Ausführungsbeginn', 'tabelle_projekte_idTABELLE_Projekte',
        'Vergabe_abgeschlossen', 'Versand_LV', 'Verfahren', 'Bearbeiter', 'Notiz', 'Kostenanschlag', 'Budget'
    ];
    $values = [
        $losNr, $losName, $ausfBeginn, $projektID, $vergabeAbgeschlossen, $versandLV, $verfahren, $bearbeiter, $notiz,
        $kostenanschlag, $budget
    ];

    if ($vergabesumme !== null) {
        $columns[] = 'Vergabesumme';
        $values[] = $vergabesumme;
    }

    if ($lieferantID !== null) {
        $columns[] = 'tabelle_lieferant_idTABELLE_Lieferant';
        $values[] = $lieferantID;
    }

    if (!$isMainLos) {
        $mkfVonLos = intval($data['lotMKFOf']);
        $mkfNr = intval($data['laufendeNr']);
        $columns[] = 'mkf_von_los';
        $columns[] = 'mkf_nr';
        $values[] = $mkfVonLos;
        $values[] = $mkfNr;
    }

    $escapedValues = array_map(function($val) use ($mysqli) {
        if (is_null($val)) return "NULL";
        if (is_numeric($val)) return $val;
        return "'" . $mysqli->real_escape_string($val) . "'";
    }, $values);

    $sql = "INSERT INTO `LIMET_RB`.`tabelle_lose_extern` (" . implode(", ", $columns) . ") VALUES (" . implode(", ", $escapedValues) . ")";

    if ($mysqli->query($sql) === TRUE) {
        echo "Los zu Projekt hinzugefügt!";
    } else {
        echo "Fehler: " . $mysqli->error;
    }
}

// After gathering POST data, log it
$data = [];
$data['losNr'] = getPostString('losNr');
$data['losName'] = getPostString('losName');
$data['losDatum'] = getPostDate('losDatum');
$data['lotVergabe'] = getPostInt('lotVergabe');
$data['lotLVSend'] = getPostDate('lotLVSend');
$data['lotVerfahren'] = getPostString('lotVerfahren');
$data['lotLVBearbeiter'] = getPostString('lotLVBearbeiter');
$data['lotNotice'] = getPostString('lotNotice');
$data['kostenanschlag'] = getPostString('kostenanschlag');
$data['budget'] = getPostString('budget');
$data['lotSum'] = getPostString('lotSum');
$data['lotAuftragnehmer'] = getPostInt('lotAuftragnehmer');
$data['lotMKFOf'] = getPostInt('lotMKFOf', 0);
$data['laufendeNr'] = 0;

logPostData($data);

if ($data['lotMKFOf'] === 0) {
    insertLos($mysqli, $data);
} else {
    $stmt = $mysqli->prepare("SELECT Max(mkf_nr) AS maxNr FROM tabelle_lose_extern WHERE mkf_von_los = ?");
    $stmt->bind_param("i", $data['lotMKFOf']);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    $laufendeNr = (($row['maxNr'] ?? 0) + 1);
    $stmt->close();

    $stmt = $mysqli->prepare("SELECT LosNr_Extern, LosBezeichnung_Extern FROM tabelle_lose_extern WHERE idtabelle_Lose_Extern = ?");
    $stmt->bind_param("i", $data['lotMKFOf']);
    $stmt->execute();
    $res = $stmt->get_result();
    $losDaten = $res->fetch_assoc();
    $stmt->close();

    $data['losNr'] = $losDaten['LosNr_Extern'] . "." . $laufendeNr;
    $data['losName'] = $losDaten['LosBezeichnung_Extern'];
    $data['laufendeNr'] = $laufendeNr;

    insertLos($mysqli, $data, false);
}

$mysqli->close();
?>
