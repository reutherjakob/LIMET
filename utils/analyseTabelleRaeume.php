<?php
include "_utils.php";
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

// Gesamtbefüllung je Feld (Projekte > 40)
$countsOverall = [];
foreach ($columns as $col) {
    $sqlCount = "SELECT COUNT(*) AS c FROM tabelle_räume 
                 WHERE `$col` IS NOT NULL AND `$col` != '' AND tabelle_projekte_idTABELLE_Projekte > 40";
    $resCount = $mysqli->query($sqlCount);
    $rowCount = $resCount->fetch_assoc();
    $countsOverall[$col] = $rowCount['c'];
}

// Befüllung je Feld je Projekt (Projekte > 40)
$fieldCountsProject = [];
foreach ($columns as $col) {
    $sql = "SELECT tabelle_projekte_idTABELLE_Projekte AS projekt_id, COUNT(*) AS count_filled 
            FROM tabelle_räume 
            WHERE `$col` IS NOT NULL AND `$col` != '' AND tabelle_projekte_idTABELLE_Projekte > 40
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

// Projekte mit ID > 40 laden
$resProjects = $mysqli->query("SELECT idTABELLE_Projekte, Projektname FROM tabelle_projekte WHERE idTABELLE_Projekte > 40 ORDER BY Projektname");
$projectNames = [];
while ($row = $resProjects->fetch_assoc()) {
    $projectNames[$row['idTABELLE_Projekte']] = $row['Projektname'];
}

// Parameter nur in einem Projekt verwendet, für Tabelle 3
$uniqueParams = [];
foreach ($fieldCountsProject as $col => $projs) {
    if (count($projs) === 1) {
        $onlyProjectId = key($projs);
        $uniqueParams[$col] = [
            'projekt_id' => $onlyProjectId,
            'projekt_name' => $projectNames[$onlyProjectId] ?? 'Unbekannt',
            'count' => reset($projs)
        ];
    }
}

$unusedParams = [];
foreach ($countsOverall as $field => $count) {
    if ($count == 0) {
        $unusedParams[] = $field;
    }
}

// DataTables JS/CSS und Buttons CDN URLs werden benutzt
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8"/>
    <title>Raum Parameter Befüllungsanalyse</title>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.2.1/af-2.7.0/b-3.2.1/b-colvis-3.2.1/b-html5-3.2.1/b-print-3.2.1/cr-2.0.4/date-1.5.5/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.1/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-3.0.0/sr-1.4.1/datatables.min.js"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css"
          integrity="sha512-q3eWabyZPc1XTCmF+8/LuE1ozpg5xxn7iO89yfSOd5/oKvyqLngoNGsx8jq92Y8eXJ/IRxQbEC+FGSYxtk2oiw=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link href="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.2.1/af-2.7.0/b-3.2.1/b-colvis-3.2.1/b-html5-3.2.1/b-print-3.2.1/cr-2.0.4/date-1.5.5/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.1/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-3.0.0/sr-1.4.1/datatables.min.css"
          rel="stylesheet">

    <style>table {
            font-size: 0.9rem
        }

        th, td {
            white-space: nowrap;
        }

        .container {
            max-width: 98vw;
            overflow-x: auto;
        }</style>
</head>
<body>
<div class="container mt-3">
    <h1 class="mb-4">Analyse der Befüllung von Raum-Parametern (Projekte &gt; 40)</h1>

    <div class="card">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" id="analysisTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="overall-tab" data-bs-toggle="tab" data-bs-target="#overall"
                            type="button" role="tab" aria-controls="overall" aria-selected="true">Gesamtbefüllung je
                        Feld
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="project-tab" data-bs-toggle="tab" data-bs-target="#project"
                            type="button" role="tab" aria-controls="project" aria-selected="false">Befüllung je Feld pro
                        Projekt
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="unique-tab" data-bs-toggle="tab" data-bs-target="#unique" type="button"
                            role="tab" aria-controls="unique" aria-selected="false">Parameter nur in einem Projekt
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="unused-tab" data-bs-toggle="tab" data-bs-target="#unused" type="button" role="tab" aria-controls="unused" aria-selected="false">Ungenutzte Parameter</button>
                </li>
            </ul>
        </div>
        <div class="card-body tab-content" id="analysisTabsContent" style="overflow-x:auto;">
            <div class="tab-pane fade show active" id="overall" role="tabpanel" aria-labelledby="overall-tab">
                <table id="tableOverall" class="table table-striped table-bordered w-100">
                    <thead>
                    <tr>
                        <th>Parameter</th>
                        <th>Anzahl Befüllungen</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($countsOverall as $field => $count): ?>
                        <?php if ($count > 0): ?>
                            <tr>
                                <td><?= htmlspecialchars($field) ?></td>
                                <td><?= $count ?></td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="tab-pane fade" id="unused" role="tabpanel" aria-labelledby="unused-tab">
                <table id="tableUnused" class="table table-striped table-bordered w-100">
                    <thead>
                    <tr>
                        <th>Parameter</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($unusedParams as $param): ?>
                        <tr>
                            <td><?= htmlspecialchars($param) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="tab-pane fade" id="project" role="tabpanel" aria-labelledby="project-tab">
                <table id="tableProject" class="table table-striped table-bordered w-100 text-center">
                    <thead>
                    <tr>
                        <th>Feld / Projekt</th>
                        <?php foreach ($projectNames as $pid => $pname): ?>
                            <th title="<?= htmlspecialchars($pname) ?>"><?= htmlspecialchars($pname) ?></th>
                        <?php endforeach; ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($fieldCountsProject as $field => $projectCounts): ?>
                        <tr>
                            <td><?= htmlspecialchars($field) ?></td>
                            <?php foreach ($projectNames as $pid => $pname): ?>
                                <td><?= isset($projectCounts[$pid]) ? $projectCounts[$pid] : '0' ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                    <?php
                    // Parameter nur in einem Projekt einfügen, wenn nicht in fieldCountsProject
                    foreach ($uniqueParams as $field => $info):
                        if (!isset($fieldCountsProject[$field])): ?>
                            <tr class="table-warning">
                                <td><?= htmlspecialchars($field) ?></td>
                                <?php foreach ($projectNames as $pid => $pname): ?>
                                    <td><?= ($pid == $info['projekt_id']) ? $info['count'] : '0' ?></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endif; endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="tab-pane fade" id="unique" role="tabpanel" aria-labelledby="unique-tab">
                <table id="tableUnique" class="table table-striped table-bordered w-100">
                    <thead>
                    <tr>
                        <th>Parameter</th>
                        <th>Projekt ID</th>
                        <th>Projektname</th>
                        <th>Anzahl Befüllungen</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($uniqueParams as $param => $info): ?>
                        <tr>
                            <td><?= htmlspecialchars($param) ?></td>
                            <td><?= htmlspecialchars($info['projekt_id']) ?></td>
                            <td><?= htmlspecialchars($info['projekt_name']) ?></td>
                            <td><?= htmlspecialchars($info['count']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<script>
    // DataTables Konfiguration mit Buttons und Footer-Panels
    $(document).ready(function () {
        $('#tableOverall, #tableProject, #tableUnique, #tableUnused').DataTable({
            layout: {
                topStart: null,
                topEnd: null,
                bottomStart: ['info', 'search'],
                bottomEnd: ['paging', 'pageLength', "buttons"]
            },
            buttons: [
                'excel',
            ],
            paging: true,
            lengthChange: true,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
            searching: true,
            ordering: true,
            info: true,
            autoWidth: false,
            scrollX: true,
            order: []
        });
    });
</script>
</body>
</html>
