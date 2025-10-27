<?php
global $mysqli;
require_once "../Nutzerlogin/_utils.php";

if (!function_exists('loadEnv')) {
    include "../Nutzerlogin/db.php";
}

$role = init_page(["internal_rb_user", "spargefeld_admin"]);
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');

$projektid = 95;


$sqlSummary = "
    SELECT 
        r.`Raumbereich Nutzer` AS rb,
        SUM(CASE WHEN t.roomID IS NULL THEN 1 ELSE 0 END) AS open_rooms,
        SUM(CASE WHEN t.roomID IS NOT NULL THEN 1 ELSE 0 END) AS filled_rooms
    FROM tabelle_räume r
    LEFT JOIN tabelle_room_requirements_from_user t ON r.idTABELLE_Räume = t.roomID
    WHERE r.tabelle_projekte_idTABELLE_Projekte = ?
    GROUP BY r.`Raumbereich Nutzer`
    ORDER BY r.`Raumbereich Nutzer`;
";

$summaryData = [];
if ($stmt = $mysqli->prepare($sqlSummary)) {
    $stmt->bind_param("i", $projektid);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $summaryData[] = $row;
    }
    $stmt->close();
}


?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>

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
</head>

<body>
<div id="limet-navbar"></div>
<?php require_once "../Nutzerumfrage/_utils.php"; ?>
<div class="container-fluid mt-3">
    <div class="card">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" id="roomTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="stats-tab" data-bs-toggle="tab" data-bs-target="#stats"
                            type="button" role="tab" aria-controls="stats" aria-selected="true">Übersicht
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link " id="user-req-tab" data-bs-toggle="tab" data-bs-target="#user-req"
                            type="button" role="tab" aria-controls="user-req" aria-selected="false">
                        Bearbeitete Räume
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="open-rooms-tab" data-bs-toggle="tab" data-bs-target="#open-rooms"
                            type="button" role="tab" aria-controls="open-rooms" aria-selected="false">
                        Noch offene Räume
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body">
            <div class="tab-content" id="roomTabsContent">
                <div class="tab-pane fade show active" id="stats" role="tabpanel" aria-labelledby="stats-tab">
                    <canvas id="roomsChart" height="100"></canvas>
                    <div class="card-body">
                        <table class="table table-sm table-bordered">
                            <thead>
                            <tr>
                                <th>Raumbereich</th>
                                <th>Offen</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($summaryData as $row): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['rb']) ?></td>
                                    <td><?= intval($row['open_rooms']) ?> / <?= intval($row['open_rooms'] + $row['filled_rooms']) ?> </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>


                <div class="tab-pane fade show" id="user-req" role="tabpanel" aria-labelledby="user-req-tab">
                    <?php
                    $sqlUserReq = "SELECT r.idTABELLE_Räume as roomID, r.Raumbezeichnung as roomname, 
                                        r.`Raumbereich Nutzer` as rb, 
                                        r.Raumnr as raumnr, t.username, t.created_at
                                    FROM tabelle_room_requirements_from_user t
                                    JOIN tabelle_räume r ON t.roomID = r.idTABELLE_Räume
                                    WHERE r.tabelle_projekte_idTABELLE_Projekte = ?
                                    ORDER BY t.created_at DESC";

                    if ($stmt = $mysqli->prepare($sqlUserReq)) {
                        $stmt->bind_param("i", $projektid);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        ?>
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>Raumnr</th>
                                <th>Raumname</th>
                                <th>Raumbereich</th>
                                <th>Username</th>
                                <th>Zuletzt bearbeitet am</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php while ($row = $result->fetch_assoc()) { ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['raumnr']); ?></td>
                                    <td><?php echo htmlspecialchars($row['roomname']); ?></td>
                                    <td><?php echo htmlspecialchars($row['rb']); ?></td>
                                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                                    <td><?php echo htmlspecialchars($row['created_at']); ?></td>

                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                        <?php
                        $stmt->close();
                    } else {
                        echo "Fehler bei der Abfrage: " . $mysqli->error;
                    }
                    ?>
                </div>

                <div class="tab-pane fade" id="open-rooms" role="tabpanel" aria-labelledby="open-rooms-tab">
                    <?php
                    $sqlOpenRooms = "SELECT idTABELLE_Räume as roomID, Raumbezeichnung as roomname, Raumnr, Bauabschnitt, Nutzfläche, `Raumbereich Nutzer`as rb FROM tabelle_räume WHERE tabelle_projekte_idTABELLE_Projekte = ? AND idTABELLE_Räume NOT IN (SELECT roomID FROM tabelle_room_requirements_from_user) ORDER BY Raumbezeichnung";
                    if ($stmt2 = $mysqli->prepare($sqlOpenRooms)) {
                        $stmt2->bind_param("i", $projektid);
                        $stmt2->execute();
                        $result2 = $stmt2->get_result();
                        ?>
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>Raumnr</th>
                                <th>Raumname</th>
                                <th>Raumbereich</th>
                                <th>Bauabschnitt</th>
                                <th>Nutzfläche</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php while ($row2 = $result2->fetch_assoc()) { ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row2['Raumnr']); ?></td>
                                    <td><?php echo htmlspecialchars($row2['roomname']); ?></td>
                                    <td><?php echo htmlspecialchars($row2['rb']); ?></td>
                                    <td><?php echo htmlspecialchars($row2['Bauabschnitt']); ?></td>
                                    <td><?php echo htmlspecialchars($row2['Nutzfläche']); ?></td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                        <?php
                        $stmt2->close();
                    } else {
                        echo "Fehler bei der Abfrage der offenen Räume: " . $mysqli->error;
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
</body>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const data = <?= json_encode($summaryData, JSON_HEX_TAG | JSON_HEX_AMP); ?>;
        const labels = data.map(d => d.rb || 'Unbekannt');
        const open = data.map(d => parseInt(d.open_rooms));
        const filled = data.map(d => parseInt(d.filled_rooms));

        const ctx = document.getElementById('roomsChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Offene Räume',
                        backgroundColor: 'rgba(255, 99, 132, 0.6)',
                        data: open
                    },
                    {
                        label: 'Gefüllte Räume',
                        backgroundColor: 'rgba(75, 192, 192, 0.6)',
                        data: filled
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {display: true, text: 'Raumstatus nach Raumbereich Nutzer'},
                    legend: {position: 'bottom'}
                },
                scales: {
                    y: {beginAtZero: true, title: {display: true, text: 'Anzahl Räume'}}
                }
            }
        });
    });
</script>

</html>
