<?php
// 25 FX
require_once 'utils/_utils.php';
init_page_serversides();
$mysqli = utils_connect_sql();

$projectID = (int)($_SESSION['projectID'] ?? 0);
if ($projectID <= 0) {
    die("Ungültige Projekt-ID in der Session.");
}

$sql = "
SELECT 
    te.idTABELLE_Elemente,
    te.ElementID,
    te.Bezeichnung AS Element_Name,
    raum_verw.Raumnr AS Verwendung_Raumnr,
    raum_verw.Raumbezeichnung AS Verwendung_Raum,
    raum_standort.Raumnr AS Standort_Raumnr,
    raum_standort.Raumbezeichnung AS Standort_Raum
FROM tabelle_räume_has_tabelle_elemente trhe_verw
INNER JOIN tabelle_elemente te 
    ON trhe_verw.TABELLE_Elemente_idTABELLE_Elemente = te.idTABELLE_Elemente
INNER JOIN tabelle_räume raum_verw
    ON trhe_verw.TABELLE_Räume_idTABELLE_Räume = raum_verw.idTABELLE_Räume
INNER JOIN tabelle_verwendungselemente ve 
    ON ve.id_Verwendungselement = trhe_verw.id
INNER JOIN tabelle_räume_has_tabelle_elemente trhe_standort
    ON ve.id_Standortelement = trhe_standort.id
    AND trhe_standort.Standort = 1
INNER JOIN tabelle_räume raum_standort
    ON trhe_standort.TABELLE_Räume_idTABELLE_Räume = raum_standort.idTABELLE_Räume
WHERE 
    trhe_verw.Verwendung = 1
    AND trhe_verw.Standort = 0
    AND raum_verw.tabelle_projekte_idTABELLE_Projekte = ?
    AND raum_standort.tabelle_projekte_idTABELLE_Projekte = ?
ORDER BY te.Bezeichnung, Verwendung_Raum
";


$stmt = $mysqli->prepare($sql);
$stmt->bind_param("ii", $projectID, $projectID);

$stmt->execute();
$result = $stmt->get_result();
$elements = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$mysqli->close();
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8"/>
    <title>Projektübersicht: Elemente mit Verwendung aber ohne Standort</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen"/>
    <link rel="icon" href="Logo/iphone_favicon.png">

    <!-- CDN und Libraries -->
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
<div class="container-fluid bg-light">
    <div id="limet-navbar"></div>
    <div class="card shadow-sm">
        <div class="card-header">
            <div class="row flex-nowrap">
                <div class="col-6">
                    Elemente: Verwendung (ja) ohne Standort (nein)
                </div>
                <div class="col-6 d-inline-flex justify-content-end" id="cardHeader"></div>
            </div>
        </div>
        <div class="card-body py-0">

            <table id="elements-table" class="table table-striped table-sm px-2 py-2" style="width:100%">
                <thead class="table table-striped table-sm table-hover table-bordered border border-light border-5">
                <tr>
                    <th>ID</th>
                    <th>Bezeichnung</th>
                    <th>VerwendungsrRaum</th>
                    <th>Verwendungs <i class="fas fa-hashtag"></i></th>
                    <th><i class="fas fa-map-marked-alt"></i> Raum</th>
                    <th><i class="fas fa-map-marked-alt"></i> <i class="fas fa-hashtag"></i></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($elements as $el): ?>
                    <tr>
                        <td><?= htmlspecialchars($el['ElementID']) ?></td>
                        <td><?= htmlspecialchars($el['Element_Name']) ?></td>
                        <td><?= htmlspecialchars($el['Verwendung_Raum']) ?></td>
                        <td><?= htmlspecialchars($el['Verwendung_Raumnr']) ?></td>
                        <td><?= htmlspecialchars($el['Standort_Raum']) ?></td>
                        <td><?= htmlspecialchars($el['Standort_Raumnr']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#elements-table').DataTable({
            select:true,
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json',
                search: "",
                searchPlaceholder: "Suche...",
                lengthMenu: '_MENU_',
                info: "_START_-_END_ von _TOTAL_",
                infoEmpty: "Keine Einträge",
                infoFiltered: "(von _MAX_)",
            },
            buttons: [
                "excelHtml5"
            ],
            layout: {
                topStart: null,
                topEnd: null,
                bottomEnd: 'paging',
                bottomStart: ["buttons", "info", "pageLength", "search"]
            },
            pageLength: 50,
            lengthChange: true,
            initComplete: function () {
                $('.dt-length').appendTo('#cardHeader');
                $('.dt-search label').remove();
                $('.dt-search').children().removeClass("form-control form-control-sm").addClass("btn btn-sm btn-outline-dark").appendTo('#cardHeader');
                $('.dt-buttons').children().addClass("btn-sm ms-1 me-1").appendTo('#cardHeader');
            }
        });
    });
</script>

</body>
</html>
