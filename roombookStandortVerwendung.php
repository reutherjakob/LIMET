<?php
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
    te.Bezeichnung AS Element_Name,
    st.Raumnr AS Standort_Raumnr,
    st.Raumbezeichnung AS Standort_Raum,
    GROUP_CONCAT(DISTINCT v.Raumbezeichnung ORDER BY v.Raumbezeichnung SEPARATOR ', ') AS Verwendung_Raeume
FROM tabelle_elemente te

INNER JOIN tabelle_räume_has_tabelle_elemente trhe_standort 
  ON trhe_standort.TABELLE_Elemente_idTABELLE_Elemente = te.idTABELLE_Elemente 
  AND trhe_standort.Standort = 1

INNER JOIN tabelle_räume st 
  ON trhe_standort.TABELLE_Räume_idTABELLE_Räume = st.idTABELLE_Räume

LEFT JOIN (
    SELECT DISTINCT trhe_verw.TABELLE_Elemente_idTABELLE_Elemente, tre_verw.Raumbezeichnung 
    FROM tabelle_verwendungselemente ve 
    INNER JOIN tabelle_räume_has_tabelle_elemente trhe_st 
      ON ve.id_Standortelement = trhe_st.id
    INNER JOIN tabelle_räume tre_st 
      ON trhe_st.TABELLE_Räume_idTABELLE_Räume = tre_st.idTABELLE_Räume
    INNER JOIN tabelle_räume_has_tabelle_elemente trhe_verw 
      ON ve.id_Verwendungselement = trhe_verw.id AND trhe_verw.Standort = 0
    INNER JOIN tabelle_räume tre_verw 
      ON trhe_verw.TABELLE_Räume_idTABELLE_Räume = tre_verw.idTABELLE_Räume
    WHERE tre_st.tabelle_projekte_idTABELLE_Projekte = ?
) v ON v.TABELLE_Elemente_idTABELLE_Elemente = te.idTABELLE_Elemente

WHERE st.tabelle_projekte_idTABELLE_Projekte = ?

GROUP BY te.idTABELLE_Elemente, te.Bezeichnung, st.Raumnr, st.Raumbezeichnung

HAVING 
    Verwendung_Raeume IS NOT NULL
    AND FIND_IN_SET(Standort_Raum, REPLACE(Verwendung_Raeume, ', ', ',')) = 0

ORDER BY te.Bezeichnung
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
    <title>Projektübersicht: Elemente mit Standort & Verwendung</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen"/>
    <link rel="icon" href="Logo/iphone_favicon.png">

    <!-- Your provided CDNs -->
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
<body class="p-4">
<div class="container-fluid bg-light">
    <div id="limet-navbar"></div>

    <h1 class="mb-4">Elementübersicht: Standort und Verwendung</h1>
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Alle Elemente mit Standort- und Verwendungsräumen</h5>
        </div>
        <div class="card-body p-0">
            <table id="elements-table" class="table table-striped table-sm mb-0" style="width:100%">
                <thead class="table-light">
                <tr>
                    <th>Element</th>
                    <th>Standort Raum</th>
                    <th>Standort Raumnr</th>
                    <th>Verwendung in Räumen</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($elements as $el): ?>
                    <tr>
                        <td><?= htmlspecialchars($el['Element_Name'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($el['Standort_Raum'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($el['Standort_Raumnr'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($el['Verwendung_Raeume'] ?? '-') ?></td>
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
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.13.5/i18n/de-DE.json"
            },
            pageLength: 25,
            lengthChange: true
        });
    });
</script>

</body>
</html>
