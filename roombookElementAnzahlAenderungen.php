<?php
// 25 FX
if (!function_exists('utils_connect_sql')) {
    include "utils/_utils.php";
}
init_page_serversides("", "x");
$mysqli = utils_connect_sql();

$changes = $mysqli->query("
  SELECT 
    rb.idtabelle_rb_aenderung,
    rb.`Neu/Bestand`,
    rb.`Neu/Bestand_copy1`,
    rb.Anzahl,
    rb.Anzahl_copy1,
    rb.Standort,
    rb.Standort_copy1,
    rb.Verwendung,
    rb.Verwendung_copy1,
    rb.Anschaffung,
    rb.Anschaffung_copy1,
    rb.Kurzbeschreibung,
    rb.Kurzbeschreibung_copy1,
    rb.user,
    rb.Timestamp,
 
    r_neu.Raumnr AS raumnr_neu,
    r_neu.tabelle_projekte_idTABELLE_Projekte,
    r_neu.Raumbezeichnung AS raumname_neu,
    e.Bezeichnung AS elementname_neu, 
    e.ElementID as elementnr_neu,
    rb.projektBudgetID_alt,
    rb.projektBudgetID_neu,
    rb.lieferdatum_alt,
    rb.lieferdatum_neu
FROM tabelle_rb_aenderung rb
LEFT JOIN tabelle_räume r_neu ON rb.raumID_neu = r_neu.idTABELLE_Räume
LEFT JOIN tabelle_elemente e ON rb.elementID_neu = e.idTABELLE_Elemente 
WHERE r_neu.tabelle_projekte_idTABELLE_Projekte = ?
ORDER BY rb.Timestamp DESC
");

if (!function_exists('h')) {
    function h($var): string
    {
        return htmlspecialchars((string)($var ?? ''));
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <title>Element Anzahl Änderungen</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css"
          integrity="sha512-q3eWabyZPc1XTCmF+8/LuE1ozpg5xxn7iO89yfSOd5/oKvyqLngoNGsx8jq92Y8eXJ/IRxQbEC+FGSYxtk2oiw=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/v/bs5/dt-2.2.1/datatables.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
</head>
<body>

<div id="limet-navbar"></div>
<div class="card">
    <div class="card-header"> RB Änderungen </div>
    <div class="card-body">
        <table id="rbChangeTable" class="table table-striped table-hover border border-5">
            <thead>
            <tr>
                <th>Neu/Bestand</th>
                <th>Anzahl</th>
                <th>Anzahl Änderungen</th>
                <th>Standort</th>
                <th>Verwendung</th>
                <th>Anschaffung</th>
                <th>Kurzbeschreibung</th>
                <th>User</th>
                <th>Timestamp</th>
                <th>Raum ID</th>
                <th>Element ID</th>
                <th>Bezeichnung</th>
                <th>Budget (alt)</th>
                <th>Budget (neu)</th>
                <th>Lieferdatum (alt)</th>
                <th>Lieferdatum (neu)</th>
            </tr>
            </thead>
            <tbody>
            <?php while ($change = $changes->fetch_assoc()):
                $changedFields = [];
                $fieldsToCheck = [
                    'Neu/Bestand', 'Anzahl', 'Standort', 'Verwendung', 'Anschaffung', 'Kurzbeschreibung'
                ];

                foreach ($fieldsToCheck as $field) {
                    $copyField = $field . '_copy1';
                    $original = $change[$field] ?? null;
                    $copy = $change[$copyField] ?? null;
                    if ($original != $copy) {
                        $changedFields[] = $field;
                    }
                }

                $anzahlChanges = count($changedFields);

                $neuBestand = $change['Neu/Bestand'] === null ? '' : ($change['Neu/Bestand'] ? 'Neu' : 'Bestand');
                ?>
                <tr data-change-id="<?= h($change['idtabelle_rb_aenderung']) ?>">
                    <td><?= h($neuBestand) ?></td>
                    <td><?= h($change['Anzahl']) ?></td>
                    <td>
                        <?= $anzahlChanges ?>
                        <?php if ($anzahlChanges > 0): ?>
                            <button class="btn btn-sm btn-link p-0" type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#changedFields<?= h($change['idtabelle_rb_aenderung']) ?>"
                                    aria-expanded="false"
                                    aria-controls="changedFields<?= h($change['idtabelle_rb_aenderung']) ?>"
                                    title="Geänderte Felder anzeigen">
                                <i class="fas fa-info-circle"></i>
                            </button>
                            <div class="collapse" id="changedFields<?= h($change['idtabelle_rb_aenderung']) ?>">
                                <ul class="small mt-1 mb-0">
                                    <?php foreach ($changedFields as $field): ?>
                                        <li><?= h($field) ?>:
                                            <strong><?= h($change[$field]) ?></strong> &rarr;
                                            <em><?= h($change[$field . '_copy1']) ?></em>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td><?= $change['Standort'] === null ? '' : ($change['Standort'] ? 'Ja' : 'Nein') ?></td>
                    <td><?= $change['Verwendung'] === null ? '' : ($change['Verwendung'] ? 'Ja' : 'Nein') ?></td>
                    <td><?= h($change['Anschaffung']) ?></td>
                    <td><?= h($change['Kurzbeschreibung']) ?></td>
                    <td><?= h($change['user']) ?></td>
                    <td><?= date('d.m.Y H:i', strtotime($change['Timestamp'])) ?></td>
                    <td><?= h($change['raumnr_neu']) ?> <?= h($change['raumname_neu']) ?></td>
                    <td><?= h($change['elementnr_neu']) ?></td>
                    <td><?= h($change['elementname_neu']) ?></td>
                    <td><?= h($change['projektBudgetID_alt']) ?></td>
                    <td><?= h($change['projektBudgetID_neu']) ?></td>
                    <td><?= $change['lieferdatum_alt'] ? date('d.m.Y', strtotime($change['lieferdatum_alt'])) : '' ?></td>
                    <td><?= $change['lieferdatum_neu'] ? date('d.m.Y', strtotime($change['lieferdatum_neu'])) : '' ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/v/bs5/dt-2.2.1/datatables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function () {
        const rbChangeTable = $('#rbChangeTable').DataTable({
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json'
            },
            order: [[8, 'desc']], // sort descending by Timestamp column (index 8)
            select: "single",
        });

        $('#rbChangeTable tbody').on('click', 'tr', function () {
            $('#rbChangeTable tbody tr').removeClass('focusedRow');
            $(this).addClass('focusedRow');
            const changeId = $(this).data('change-id');
            $('#PlaceholderForChangeIdentification').text('Änderung ID: ' + changeId);
            loadChangeDetails(changeId);
            $('#rbChangeDetailsCard').collapse('show');
        });

        function loadChangeDetails(changeId) {
            $('#rbChangeDetailsContent').html('<div class="text-center my-4"><div class="spinner-border" role="status"></div></div>');
            let startDate = $('#start_date_rb_changes').val();
            let endDate = $('#end_date_rb_changes').val();
            $.ajax({
                type: 'POST',
                url: 'get_rb_change_details.php',
                data: {
                    changeId: changeId,
                    startDate: startDate,
                    endDate: endDate
                },
                success: function (response) {
                    $('#rbChangeDetailsContent').html(response);
                },
                error: function () {
                    $('#rbChangeDetailsContent').html('<div class="alert alert-danger">Fehler beim Laden der Änderungen</div>');
                }
            });
        }
        $('#start_date_rb_changes, #end_date_rb_changes').on('change', function () {
            const startDate = $('#start_date_rb_changes').val();
            const endDate = $('#end_date_rb_changes').val();

            if (startDate && endDate) {
                rbChangeTable.draw();
            } else {
                rbChangeTable.columns(8).search('').draw();
            }
        });
    });
</script>
</body>
</html>
