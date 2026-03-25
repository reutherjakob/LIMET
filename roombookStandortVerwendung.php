<?php
// 25 FX
require_once 'utils/_utils.php';
init_page_serversides();
$mysqli = utils_connect_sql();

$projectID = (int)($_SESSION['projectID'] ?? 0);
if ($projectID <= 0) {
    die("Ungültige Projekt-ID in der Session.");
}

/*
 * Zeigt alle Elemente, bei denen in einem Raum Standort und Verwendung
 * NICHT beide auf 1 gesetzt sind – also:
 *   - Standort=1 aber kein passender Verwendungs-Eintrag im selben Raum, ODER
 *   - Verwendung=1 aber kein passender Standort-Eintrag im selben Raum
 *
 * Technisch: alle (Raum, Element)-Kombinationen aus tabelle_räume_has_tabelle_elemente
 * im Projekt, bei denen nicht BEIDE Flags (Standort=1 UND Verwendung=1) existieren.
 */

$sql = "
SELECT
    te.ElementID,
    te.Bezeichnung                          AS Element_Name,
    r.Raumnr,
    r.Raumbezeichnung,
    MAX(trhe.Standort)                      AS hat_Standort,
    MAX(trhe.Verwendung)                    AS hat_Verwendung
FROM tabelle_räume_has_tabelle_elemente trhe
INNER JOIN tabelle_elemente te
    ON trhe.TABELLE_Elemente_idTABELLE_Elemente = te.idTABELLE_Elemente
INNER JOIN tabelle_räume r
    ON trhe.TABELLE_Räume_idTABELLE_Räume = r.idTABELLE_Räume
WHERE r.tabelle_projekte_idTABELLE_Projekte = ?
GROUP BY
    r.idTABELLE_Räume,
    te.idTABELLE_Elemente,
    te.ElementID,
    te.Bezeichnung,
    r.Raumnr,
    r.Raumbezeichnung
HAVING NOT (MAX(trhe.Standort) = 1 AND MAX(trhe.Verwendung) = 1)
ORDER BY te.Bezeichnung, r.Raumnr
";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $projectID);
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
    <title>Elemente: Standort/Verwendung unvollständig</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen"/>
    <link rel="icon" href="Logo/iphone_favicon.png">

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
                    Elemente: Standort/Verwendung im Raum unvollständig
                    <small class="text-muted ms-2">(weder beides Ja, noch korrekt verknüpft)</small>
                </div>
                <div class="col-6 d-inline-flex justify-content-end" id="cardHeader"></div>
            </div>
        </div>
        <div class="card-body py-0">
            <table id="elements-table" class="table table-striped table-sm px-2 py-2" style="width:100%">
                <thead class="table table-striped table-sm table-hover table-bordered border border-light border-5">
                <tr>
                    <th>ElementID</th>
                    <th>Bezeichnung</th>
                    <th>Raum-Nr</th>
                    <th>Raumbezeichnung</th>
                    <th>Standort</th>
                    <th>Verwendung</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($elements as $el): ?>
                    <tr>
                        <td><?= htmlspecialchars($el['ElementID']) ?></td>
                        <td><?= htmlspecialchars($el['Element_Name']) ?></td>
                        <td><?= htmlspecialchars($el['Raumnr']) ?></td>
                        <td><?= htmlspecialchars($el['Raumbezeichnung']) ?></td>
                        <td>
                            <?php if ($el['hat_Standort']): ?>
                                <span class="badge bg-success"><i class="fas fa-check"></i> Ja</span>
                            <?php else: ?>
                                <span class="badge bg-danger"><i class="fas fa-times"></i> Nein</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($el['hat_Verwendung']): ?>
                                <span class="badge bg-success"><i class="fas fa-check"></i> Ja</span>
                            <?php else: ?>
                                <span class="badge bg-danger"><i class="fas fa-times"></i> Nein</span>
                            <?php endif; ?>
                        </td>
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
            select: true,
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
                $('.dt-search').children()
                    .removeClass("form-control form-control-sm")
                    .addClass("btn btn-sm btn-outline-dark")
                    .appendTo('#cardHeader');
                $('.dt-buttons').children().addClass("btn-sm ms-1 me-1").appendTo('#cardHeader');
            }
        });
    });
</script>

</body>
</html>