<?php
require_once 'utils/_utils.php';
check_login();
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Raumvermerke Übersicht</title>
</head>
<body>

<?php
$projectID = $_SESSION["projectID"] ?? null;
$roomID = $_SESSION["roomID"] ?? null;

if (!$projectID || !$roomID) {
    die("Projekt oder Raum nicht ausgewählt.");
}

$mysqli = utils_connect_sql();

$sql = "SELECT 
            vg.Gruppenname, 
            vg.Gruppenart, 
            vg.Ort, 
            vg.Datum, 
            le.LosNr_Extern, 
            le.LosBezeichnung_Extern, 
            ap.Name, 
            ap.Vorname, 
            v.Faelligkeit, 
            v.Vermerkart, 
            v.Bearbeitungsstatus, 
            v.Vermerktext, 
            v.Erstellungszeit, 
            v.idtabelle_Vermerke
        FROM tabelle_Vermerke v
        LEFT JOIN tabelle_Vermerke_has_tabelle_ansprechpersonen va
            ON v.idtabelle_Vermerke = va.tabelle_Vermerke_idtabelle_Vermerke
        LEFT JOIN tabelle_ansprechpersonen ap 
            ON ap.idTABELLE_Ansprechpersonen = va.tabelle_ansprechpersonen_idTABELLE_Ansprechpersonen
        INNER JOIN tabelle_Vermerkuntergruppe vu 
            ON vu.idtabelle_Vermerkuntergruppe = v.tabelle_Vermerkuntergruppe_idtabelle_Vermerkuntergruppe
        INNER JOIN tabelle_Vermerkgruppe vg 
            ON vu.tabelle_Vermerkgruppe_idtabelle_Vermerkgruppe = vg.idtabelle_Vermerkgruppe
        LEFT JOIN tabelle_lose_extern le 
            ON v.tabelle_lose_extern_idtabelle_Lose_Extern = le.idtabelle_Lose_Extern
        INNER JOIN tabelle_vermerke_has_tabelle_räume vr
            ON v.idtabelle_Vermerke = vr.tabelle_vermerke_idTabelle_vermerke
        INNER JOIN tabelle_räume r
            ON vr.tabelle_räume_idTabelle_räume = r.idTABELLE_Räume
        WHERE vg.tabelle_projekte_idTABELLE_Projekte = ?
          AND r.idTABELLE_Räume = ?
        ORDER BY vg.Datum DESC, v.Erstellungszeit DESC";

$stmt = $mysqli->prepare($sql);

if (!$stmt) die("Prepare failed: " . $mysqli->error);

$stmt->bind_param("ii", $projectID, $roomID);
$stmt->execute();
$result = $stmt->get_result();
$mysqli->close();
?>

<div class="table-responsive">
    <table class="table table-striped table-bordered table-sm table-hover border border-light border-5"
           id="tableRoomVermerke">
        <thead>
        <tr>
            <th>ID</th>
            <th>Art</th>
            <th>Name</th>
            <th>Status</th>
            <th>Datum</th>
            <th>Vermerk</th>
            <th>Typ</th>
            <th>Zuständig</th>
            <th>Fälligkeit</th>
            <th>Los</th>
            <th>Status-Wert</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['idtabelle_Vermerke'] ?></td>
                <td><?= htmlspecialchars($row['Gruppenart'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['Gruppenname'] ?? '') ?></td>
                <td>
                    <?php if ($row['Vermerkart'] !== 'Info'): ?>
                        <div class="form-check form-check-inline">
                            <label for="<?= $row['idtabelle_Vermerke'] ?>">

                            </label><input type="checkbox" class="form-check-input"
                                           id="<?= $row['idtabelle_Vermerke'] ?>"
                                           value="statusCheck"
                                <?= $row["Bearbeitungsstatus"] == "1" ? "checked" : "" ?>>
                        </div>
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($row['Datum'] ?? '') ?></td>
                <td>
                    <?php
                    $vermerktextSafe = htmlspecialchars($row['Vermerktext'] ?? '', ENT_QUOTES | ENT_HTML5);
                    ?>
                    <button type="button"
                            class="btn btn-sm btn-outline-dark"
                            data-bs-toggle="popover"
                            data-bs-placement="left"
                            data-bs-html="true"
                            title="Vermerk"
                            data-bs-content="<?= $vermerktextSafe ?>">
                        <i class="fa fa-comment"></i>
                    </button>
                </td>
                <td><?= htmlspecialchars($row['Vermerkart'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['Name'] ?? '') ?></td>
                <td><?= $row['Vermerkart'] !== 'Info' ? htmlspecialchars($row['Faelligkeit'] ?? '') : "" ?></td>
                <td><?= htmlspecialchars($row['LosNr_Extern'] ?? '') ?></td>
                <td><?= $row['Bearbeitungsstatus'] ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>


<script>
    $(document).ready(function () {

        $(function () {
            const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
            const popoverList = popoverTriggerList.map(function (el) {
                return new bootstrap.Popover(el, {
                    container: 'body',
                    trigger: 'focus',
                    html: true,
                    placement: 'left'
                });
            });

            // Hide popovers when clicking outside
            $(document).on('click', function (e) {
                $('[data-bs-toggle="popover"]').each(function () {
                    if (
                        !$(this).is(e.target) &&                           // Clicked element is NOT the trigger
                        $(this).has(e.target).length === 0 &&             // Not inside the trigger
                        $('.popover').has(e.target).length === 0          // Not inside the popover
                    ) {
                        $(this).popover('hide');
                    }
                });
            });
        });



        $('#tableRoomVermerke').DataTable({
            columnDefs: [
                {targets: [0, 6, 9, 10], visible: false, searchable: false}
            ],
            paging: true,
            pagingType: "simple",
            lengthChange: false,
            pageLength: 10,
            searching: true,
            info: true,
            order: [[4, 'desc']],
            language: {url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json'},
            rowCallback: function (row, data) {
                var vermerkTyp = data[6];
                var status = data[10];
                if (vermerkTyp === "Bearbeitung") {
                    row.style.backgroundColor = status === "0" ? '#ff8080' : '#b8dc6f';
                } else {
                    row.style.backgroundColor = '#d3edf8';
                }
            }
        });

        document.querySelectorAll("input[value='statusCheck']").forEach(function (input) {
            input.addEventListener("change", function () {
                var vermerkID = this.id;
                var status = this.checked ? 1 : 0;
                if (vermerkID) {
                    $.ajax({
                        url: "saveVermerkStatus.php",
                        method: "GET",
                        data: {vermerkID: vermerkID, vermerkStatus: status},
                        success: function (data) {
                            // You can replace with a user feedback system or toaster here
                            alert(data);
                            $.ajax({
                                url: "getRoomVermerke2.php",
                                method: "GET",
                                success: function (html) {
                                    $("#roomVermerke").html(html);
                                }
                            });
                        },
                        error: function () {
                            alert("Fehler beim Aktualisieren des Vermerkstatus.");
                        }
                    });
                }
            });
        });
    });

</script>
</body>
</html>
