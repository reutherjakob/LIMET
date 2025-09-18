<?php

require_once '_utils.php';

$mysqli = utils_connect_sql();
$old_ids = [
];

$new_ids = [

];

$columns = [
    'TABELLE_Elemente_idTABELLE_Elemente',
    'Neu/Bestand',
    'Anzahl',
    'Standort',
    'Verwendung',
    'Anschaffung',
    'Kurzbeschreibung',
    'TABELLE_Geraete_idTABELLE_Geraete',
    'tabelle_Lose_Intern_idtabelle_Lose_Intern',
    'tabelle_Lose_Extern_idtabelle_Lose_Extern',
    'idtabelle_auftraggeber_GHG',
    'idtabelle_auftraggeberg_GUG',
    'idTABELLE_Auftraggeber_Gewerke',
    'Timestamp',
    'Lieferdatum',
    'tabelle_Varianten_idtabelle_Varianten',
    'tabelle_projektbudgets_idtabelle_projektbudgets',
    'status'
];

function run_copy($mysqli, $old_id, $new_id, $columns)
{
    $cols_insert = implode(', ', array_map(fn($c) => "`$c`", $columns));
    $cols_select = implode(', ', array_map(fn($c) => "e.`$c`", $columns));


    $sql = "INSERT INTO tabelle_räume_has_tabelle_elemente (TABELLE_Räume_idTABELLE_Räume, $cols_insert)
            SELECT ?, $cols_select FROM tabelle_räume_has_tabelle_elemente e WHERE e.TABELLE_Räume_idTABELLE_Räume = ?";

    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        echo "Prepare Fehler: " . $mysqli->error . "<br>";
        return false;
    }
    $stmt->bind_param('ii', $new_id, $old_id);
    if (!$stmt->execute()) {
        echo "Execute Fehler bei $old_id -> $new_id: " . $stmt->error . "<br>";
        $stmt->close();
        return false;
    }
    echo "Kopiert: $old_id → $new_id, eingefügte Zeilen: " . $stmt->affected_rows . "<br>";
    $stmt->close();
    return true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    for ($i = 0; $i < count($old_ids); $i++) {
        run_copy($mysqli, $old_ids[$i], $new_ids[$i], $columns);
    }
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8"/>
    <title>Räume Has Elemente kopieren</title>
</head>
<body>
<h1>Kopie der Räume Has Elemente ausführen</h1>
<form method="post">
    <button type="submit">Kopieren starten</button>
</form>
</body>
</html>
