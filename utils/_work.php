<?php
include "_utils.php"; // fills navbar, lots of support functions
check_login();

$mysqli = utils_connect_sql();
$res = $mysqli->query("SHOW COLUMNS FROM tabelle_räume");
$columns = [];
$ignoreFields = ['idTABELLE_Räume', 'tabelle_projekte_idTABELLE_Projekte'];
while ($row = $res->fetch_assoc()) {
    $field = $row['Field'];
    if (!in_array($field, $ignoreFields)) {
        $columns[] = $field;
    }
}

// 1. Allgemeine Befüllungshäufigkeit pro Feld
$countsOverall = [];
foreach ($columns as $col) {
    $sqlCount = "SELECT COUNT(*) AS c FROM tabelle_räume WHERE `$col` IS NOT NULL AND `$col` != ''";
    $resCount = $mysqli->query($sqlCount);
    $rowCount = $resCount->fetch_assoc();
    $countsOverall[$col] = $rowCount['c'];
}

// 2. Befüllungshäufigkeit pro Feld je Projekt
$fieldCountsProject = [];
foreach ($columns as $col) {
    $sql = "SELECT tabelle_projekte_idTABELLE_Projekte AS projekt_id, COUNT(*) AS count_filled 
            FROM tabelle_räume 
            WHERE `$col` IS NOT NULL AND `$col` != '' 
            GROUP BY tabelle_projekte_idTABELLE_Projekte";
    $res = $mysqli->query($sql);
    while ($row = $res->fetch_assoc()) {
        $projekt = $row['projekt_id'];
        $count = $row['count_filled'];
        if (!isset($fieldCountsProject[$col])) {
            $fieldCountsProject[$col] = [];
        }
        $fieldCountsProject[$col][$projekt] = $count;
    }
}

// Tabelle aller Projekte ermitteln für Anzeige (sortiert)
$resProjects = $mysqli->query("SELECT idTABELLE_Projekte, Projektname FROM tabelle_projekte ORDER BY idTABELLE_Projekte");
$projectNames = [];
while ($row = $resProjects->fetch_assoc()) {
    $projectNames[$row['idTABELLE_Projekte']] = $row['Projektname'];
}

// Ausgabe HTML
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8"/>
    <title>Raum Parameter Befüllungsanalyse</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <style>
        table {
            font-size: 0.9rem;
        }

        th, td {
            white-space: nowrap;
        }

        .container {
            max-width: 98vw;
            overflow-x: auto;
        }
    </style>
</head>
<body>
<div class="container mt-3">
    <h1>Analyse der Befüllung von Raum-Parametern</h1>
    <h2>Insgesamt befüllte Werte je Feld</h2>
    <table class="table table-striped table-bordered">
        <thead>
        <tr>
            <th>Parameter</th>
            <th>Anzahl Befüllungen</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($countsOverall as $field => $count): ?>
            <tr>
                <td><?= htmlspecialchars($field) ?></td>
                <td><?= $count ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Befüllung je Feld pro Projekt</h2>
    <table class="table table-sm table-striped table-bordered text-center">
        <thead>
        <tr>
            <th>Feld / Projekt</th>
            <?php foreach ($projectNames as $pid => $pname): ?>
                <th title="<?= htmlspecialchars($pname) ?>"><?= htmlspecialchars($pid) ?></th>
            <?php endforeach; ?>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($fieldCountsProject as $field => $projectCounts): ?>
            <tr>
                <td><?= htmlspecialchars($field) ?></td>
                <?php
                foreach ($projectNames as $pid => $pname) {
                    echo '<td>' . (isset($projectCounts[$pid]) ? $projectCounts[$pid] : '0') . '</td>';
                }
                ?>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
