<?php
include "../utils/_utils.php";
check_login();
init_page_serversides("", "x");
$projectID = $_SESSION["projectID"];
$conn = utils_connect_sql();
$raumbereichOptions = [];
$sql = "SELECT DISTINCT `Raumbereich Nutzer` FROM tabelle_räume WHERE tabelle_projekte_idTABELLE_Projekte = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $projectID);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    if (!empty($row["Raumbereich Nutzer"])) {
        $raumbereichOptions[] = $row["Raumbereich Nutzer"];
    }
}
$stmt->close();


$sql = "SELECT idTABELLE_Räume AS id, 
               CONCAT(Raumnr, ' - ', Raumbezeichnung, ' - ', `Raumbereich Nutzer`) AS text
          FROM tabelle_räume
         WHERE tabelle_projekte_idTABELLE_Projekte = ? 
         
      ORDER BY Raumnr";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $projectID);
$stmt->execute();
$result = $stmt->get_result();

$raeume = [];
while ($row = $result->fetch_assoc()) {
    $raeume[] = $row;
}
$stmt->close();


$sql = "SELECT tabelle_elemente.idTABELLE_Elemente as id,  
            CONCAT(ElementID,' ', Bezeichnung) as Bez
  		    FROM tabelle_elemente
  	         WHERE idTABELLE_Elemente = ?";


$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $projectID);
$stmt->execute();
$result = $stmt->get_result();

$elemente = [];
while ($row = $result->fetch_assoc()) {
    $elemente[] = $row;
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Besprechungsansicht</title>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css"/>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
    <link href="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.2.1/af-2.7.0/b-3.2.1/b-colvis-3.2.1/b-html5-3.2.1/b-print-3.2.1/cr-2.0.4/date-1.5.5/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.1/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-3.0.0/sr-1.4.1/datatables.min.css"
          rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.2.1/af-2.7.0/b-3.2.1/b-colvis-3.2.1/b-html5-3.2.1/b-print-3.2.1/cr-2.0.4/date-1.5.5/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.1/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-3.0.0/sr-1.4.1/datatables.min.js"></script>
    <style>
        .form-switch .form-check-input:checked {
            background-color: #000;
            border-color: #000;
        }

    </style>
</head>

<body>

<div id="limet-navbar"></div>
<div class="container-fluid">
    <div class="row row-cols-2">

        <div class="col-lg-2 mx-auto mb-4" id="filterCardCol">
            <div class="card mb-2">
                <div class="card-header d-inline-flex align-items-center justify-content-between">


                    <div>
                        <button type="button" class="btn btn-outline-success" id="createMeetingBtn"
                                data-bs-toggle="modal" data-bs-target="#createMeetingModal">
                            <i class="fa fa-plus"></i> Besprechung anlegen
                        </button>
                    </div>

                </div>

            </div>


            <div class="card mb-2">
                <form id="filterForm">
                    <div class="card-header d-flex flex-nowrap">
                        <label for="raumbereich" class="form-label"></label>
                        <select id="raumbereich" name="raumbereich[]" class="form-select" style="width:95%" multiple>
                            <?php foreach ($raumbereichOptions as $option): ?>
                                <option value="<?= htmlspecialchars($option) ?>"><?= htmlspecialchars($option) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <span class="badge rounded-pill bg-light text-dark p-2 "
                              data-bs-toggle="popover"
                              data-bs-content="Hier werden ALLE Raumbereiche des Projektes angezeigt, basierend auf der textuellen Bezeichnung Raumbereich Nutzer und unabhängig davon, ob diese z.B. MT-relevante Räume haben.">
                                <i class="fas fa-info-circle"></i>
                            </span>
                    </div>

                    <div class="card-body">
                        <div class=" d-flex flex-nowrap mb-2">
                            <select id="zusatzRaeume" name="zusatzRaeume[]" class="form-select" style="width:95%"
                                    multiple>
                                <?php foreach ($raeume as $raum): ?>
                                    <option value="<?= htmlspecialchars($raum['id']) ?>">
                                        <?= htmlspecialchars($raum['text']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <span class="badge rounded-pill bg-light text-dark p-2  "
                                  data-bs-toggle="popover"
                                  data-bs-content="Hier werden ALLE Räume des Projektes angezeigt.
                                                    Sollten diese dann in der Tabelle fehlen, sind die gewählten Räume ggf. durch unten angeführte Filter ausgeschieden.">
                                <i class="fas fa-info-circle"></i>
                            </span>
                        </div>
                        <div class=" d-flex flex-nowrap mb-2">
                            <select id="zusatzElemente" name="zusatzElemente[]" class="form-select" style="width:95%"
                                    multiple>
                                <?php foreach ($elemente as $element): ?>
                                    <option value="<?= htmlspecialchars($element['id']) ?>">
                                        <?= htmlspecialchars($element['Bez']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <span class="badge rounded-pill bg-light text-dark p-2  "
                                  data-bs-toggle="popover"
                                  data-bs-content="Hier werden ALLE Räume des Projektes angezeigt.
                                                    Sollten diese dann in der Tabelle fehlen, sind die gewählten Räume ggf. durch unten angeführte Filter ausgeschieden.">
                                <i class="fas fa-info-circle"></i>
                            </span>
                        </div>
                        <div class="form-check form-switch ">
                            <input class="form-check-input btn btn-outline-dark" type="checkbox" id="mtRelevant"
                                   name="mtRelevant" checked>
                            <label class="form-check-label" for="mtRelevant">Nur MT-relevante Räume</label>
                        </div>
                        <div class="form-check  form-switch">
                            <input class="form-check-input" type="checkbox" id="entfallen" name="entfallen" checked>
                            <label class="form-check-label " for="entfallen">Entfallene Räume ausblenden</label>
                        </div>
                        <div class="form-check  form-switch">
                            <input class="form-check-input" type="checkbox" id="nurMitElementen" name="nurMitElementen"
                                   checked>
                            <label class="form-check-label" for="nurMitElementen">Nur Räume mit Elementen</label>
                        </div>
                        <div class="form-check  form-switch">
                            <input class="form-check-input" type="checkbox" id="ohneLeereElemente"
                                   name="ohneLeereElemente" checked>
                            <label class="form-check-label" for="ohneLeereElemente">
                                Elemente Stk<1 ausblenden
                            </label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="isTransposed" name="isTransposed">
                            <label class="form-check-label" for="isTransposed" id="isTransposedLabel">
                                Elemente als Zeilen
                            </label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="hideZeros" name="hideZeros">
                            <label class="form-check-label" for="hideZeros">
                                Nullen ausblenden
                            </label>
                        </div>


                        <div>
                            <button type="submit" class="btn btn-success w-100">Anzeigen</button>
                        </div>
                    </div>

                </form>
            </div>


            <div class="card mb-2">
                <div class="card-header d-inline-flex align-items-center justify-content-between">
                    Protokoll
                    <span class="badge rounded-pill bg-light text-dark m-1 p-2 float-end"
                          data-bs-toggle="popover"
                          title="Info"
                          data-bs-content="">
                    <i class="fas fa-info-circle"></i>
                       </span>
                </div>
                <div class="card-body">
                    Automatisch generiertes Protokoll. Hier soll man aber Vermerke noch Zusätzliches annotieren können
                </div>
            </div>


        </div>


        <div class="col-lg-10 mx-auto" id="tableCardCol">
            <div class="card">
                <div class="card-header d-flex align-items-start" style=" height: 55px; ">
                    <button class="btn btn-outline-dark fa fa-arrow-left" id="ToggleCard"></button>
                    <div class="row d-inline-flex align-items-start w-100">
                        <div class=" col-6   d-flex                   align-items-start"
                             id="CardHeaderHoldingDatatableManipulators"></div>
                        <div class=" col-6 d-flex justify-content-end  align-items-start"
                             id="CardHeaderHoldingDatatableManipulators2"></div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div id="pivotTableContainer">
                        <!-- Die Pivot-Tabelle wird hier per AJAX geladen -->
                    </div>
                </div>
            </div>
        </div>


    </div>
</div>


<!-- Besprechung anlegen Modal -->
<div class="modal fade" id="createMeetingModal" tabindex="-1" aria-labelledby="createMeetingLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <form id="createMeetingForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="createMeetingLabel">Neue Besprechung anlegen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Schließen"></button>
                </div>
                <div class="modal-body">

                    <div class="mb-2  ">
                        <label for="meetingDatum" class="form-label">Datum</label>
                        <input type="date" class="form-control" id="meetingDatum" name="datum" required
                               placeholder="*required">
                    </div>
                    <div class="mb-2  ">
                        <label for="meetingUhrzeitStart" class="form-label">Uhrzeit [Start] </label>
                        <input type="time" class="form-control" id="meetingUhrzeitStart" name="uhrzeit" required>
                    </div>
                    <div class="mb-2  ">
                        <label for="meetingUhrzeitEnde" class="form-label">Uhrzeit [Ende] </label>
                        <input type="time" class="form-control" id="meetingUhrzeitEnde" name="uhrzeit">
                    </div>
                    <div class="mb-2  ">
                        <label for="meetingOrt" class="form-label">Ort</label>
                        <input type="text" class="form-control" id="meetingOrt" name="ort">
                    </div>
                    <div class="mb-2  ">
                        <label for="meetingName" class="form-label">Besprechungsname</label>
                        <input type="text" class="form-control" id="meetingName" name="name" required>
                    </div>
                    <div class="mb-2  ">
                        <label for="meetingVerfasser" class="form-label">Verfasser</label>
                        <input type="text" class="form-control" id="meetingVerfasser" name="verfasser" required>
                    </div>
                    <div class="mb-2  ">
                        <label for="meetingKommentar" class="form-label">Kommentar</label>
                        <textarea class="form-control" id="meetingKommentar" name="kommentar" rfows="2"></textarea>
                    </div>
                    <div class="mb-2  ">
                        <div class="badge rounded-pill bg-light text-dark m-1 p-2 float-end"
                             data-bs-toggle="popover"
                             title="INFO"
                             data-bs-content="Vorerst können hier nur Pfade als txt gespeichertt werden.">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <label for="meetingDokumente" class="form-label">relevante Dokumente</label>
                        <input type="text" class="form-control" id="meetingDokumente" name="name">
                    </div>

                    <!--- div class="mb-3"> TODO 4 later: Get Projektbeteiligte  & use exisiting structure for saving files
                        <label for="meetingBeteiligte" class="form-label">Beteiligte</label>
                        <input type="text" class="form-control" id="meetingBeteiligte" name="beteiligte" placeholder="Namen kommasepariert">
                    </div>
                    <div class="mb-3">
                        <label for="meetingDokumente" class="form-label">Relevante Dokumente</label>
                        <input type="file" class="form-control" id="meetingDokumente" name="dokumente[]" multiple>
                    </div --->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Abbrechen</button>
                    <button type="submit" class="btn btn-success">Besprechung anlegen</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script src="../utils/_utils.js"></script>
<script>
    $(document).ready(function () {
        let excelfilename;
        let filterVisible = true;

        $('#raumbereich').select2({placeholder: "Raumbereich wählen"});
        $('#zusatzRaeume').select2({placeholder: "Zusätzliche Räume wählen"});
        $('#zusatzElemente').select2({placeholder: "Zusätzliche Elemente wählen"});


        $('#isTransposed').on('change', updateTransposeLabel);
        $('#filterForm').on('submit', function (e) {
            e.preventDefault();

            loadPivotTable();
        });


        $('#ToggleCard').on('click', function () {
            if (filterVisible) {                // Filter-Card ausblenden, Tabellen-Card auf volle Breite
                $('#filterCardCol').hide();
                $('#tableCardCol').removeClass('col-lg-10').addClass('col-12');
                $(this).removeClass('fa-arrow-left').addClass('fa-arrow-right');
            } else {                // Filter-Card einblenden, Tabellen-Card wieder schmal
                $('#filterCardCol').show();
                $('#tableCardCol').removeClass('col-12').addClass('col-lg-10');
                $(this).removeClass('fa-arrow-right').addClass('fa-arrow-left');
            }
            filterVisible = !filterVisible;
        });

        let popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        popoverTriggerList.forEach(function (popoverTriggerEl) {
            new bootstrap.Popover(popoverTriggerEl);
        });


        $('#createMeetingForm').on('submit', function (e) {
            e.preventDefault();
            // Sammle Daten oder sende per AJAX ab
            const formData = new FormData(this);
            // Beispiel-Logik:
            alert('Besprechung wird angelegt!\n\n' +
                'Titel: ' + formData.get('name') + '\n' +
                'Datum: ' + formData.get('datum') + '\n' +
                'Uhrzeit: ' + formData.get('uhrzeit'));
            // Modal schließen (optional):
            $('#createMeetingModal').modal('hide');
            // TODO: Send to backend & handle UI update
        });


    });

    function table_click() {
        $('#pivotTable').off('click', 'td').on('click', 'td', function () {
            const cell = $(this);
            const table = $('#pivotTable').DataTable();

            // DataTable cell/row/col index
            const cellIdx = table.cell(this).index();

            // Row and column indices (zero-based)
            const rowIdx = cellIdx.row;
            const colIdx = cellIdx.column;

            // Get raw data for this row and column
            const cellData = table.cell(cell).data();

            const rowData = table.row(rowIdx).data();

            // Get the header text for this column
            const headerText = $(table.column(colIdx).header()).text().trim();

            // Log all relevant info
            console.log('Cell Value:', cellData);
            console.log('Column:', colIdx, '(', headerText, ')');
            console.log('Row:', rowIdx, rowData);


            // Optionally: log any IDs stored as data attributes, e.g.
            // <td data-roomid="123" data-elementid="55">
            const dataRoomId = cell.data('roomid');
            const dataElementId = cell.data('elementid');
            const idTABELLE_Räume_has_tabelle_Elemente = cell.data('roomhaselementid')

            if (dataRoomId) console.log('Room ID:', dataRoomId);
            if (dataElementId) console.log('Element ID:', dataElementId);
            if (idTABELLE_Räume_has_tabelle_Elemente) console.log('Element ID:', idTABELLE_Räume_has_tabelle_Elemente); //TODO validate
        });


    }

    function updateTransposeLabel() {
        $('#isTransposedLabel').text(
            $('#isTransposed').is(':checked') ? 'Räume als Zeilen' : 'Elemente als Zeilen'
        );
    }

    function loadPivotTable() {
        let raumbereich = $('#raumbereich').val(); // This is now an array
        let mtRelevant = $('#mtRelevant').is(':checked') ? 1 : 0;
        let entfallen = $('#entfallen').is(':checked') ? 1 : 0;
        let nurMitElementen = $('#nurMitElementen').is(':checked') ? 1 : 0;
        let ohneLeereElemente = $('#ohneLeereElemente').is(':checked') ? 1 : 0;
        let transponiert = $('#isTransposed').is(':checked') ? 1 : 0;

        if (!raumbereich || raumbereich.length === 0) {
            $('#pivotTableContainer').html('<div class="alert alert-info">Bitte wählen Sie mindestens einen Raumbereich.</div>');
            return;
        }             // console.log(raumbereich);
        let hideZeros = $('#hideZeros').is(':checked');

        let zusatzRaeume = $('#zusatzRaeume').val();

        let zusatzElemente = $('#zusatzElemente').val();
        console.log(zusatzElemente);

        $.ajax({
            url: '../getElementeJeRäumePivotTable.php',
            method: 'POST',
            data: {
                'raumbereich[]': raumbereich, // This will be sent as an array
                'zusatzRaeume[]': zusatzRaeume,
                'zusatzElemente[]': zusatzElemente,
                mtRelevant,
                entfallen,
                nurMitElementen,
                ohneLeereElemente,
                transponiert
            },
            traditional: true, // Important for sending arrays with jQuery
            success: function (data) {
                let raumbereichJoined = raumbereich
                    .map(r => r.replace(/ /g, '_'))
                    .join('_');
                getExcelFilename('Elemente-je-Raumbereich_' + raumbereichJoined)
                    .then(filename => {
                        //console.log('Generated filename:', filename);
                        $('#pivotTableContainer').html(data);
                        let colCount = $('#pivotTable thead th').length;
                        let columns = [];
                        for (let i = 0; i < colCount; i++) {
                            if (i === 0) {
                                // First column: Element or Raum, don't change rendering
                                columns.push(null);
                            } else if (hideZeros) {
                                // For all other columns, hide zeros
                                columns.push({
                                    render: function (data) {
                                        return (data === "0" || data === 0) ? "" : data;
                                    }
                                });
                            } else {
                                columns.push(null);
                            }
                        }

                        $('#pivotTable').DataTable({
                            language: {
                                url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json",
                                search: "",
                                searchPlaceholder: "Suche...",
                                lengthMenu: '_MENU_',
                                info: "_START_-_END_ von _TOTAL_",
                                infoEmpty: "Keine Einträge",
                                infoFiltered: "(von _MAX_)",
                            },
                            scrollX: true,
                            scrollCollapse: true,
                            fixedColumns: {start: 1},
                            fixedHeader: true,
                            select: true,

                            paging: true,
                            pagingType: "full",

                            searching: true,
                            ordering: true,
                            info: true,
                            lengthChange: true,
                            pageLength: 10,
                            lengthMenu: [[10, 20, 50, -1], ['10 rows', '20 rows', '50 rows', 'All']],
                            responsive: false,
                            autoWidth: true,
                            columns: columns,
                            layout: {
                                topStart: 'buttons',
                                topEnd: 'search',
                                bottomStart: 'info',
                                bottomEnd: ['pageLength', 'paging']
                            },
                            buttons: [
                                {
                                    extend: 'excelHtml5',
                                    text: '<i class="fas fa-file-excel"></i> Excel',
                                    className: 'btn btn-success btn-sm',
                                    title: filename
                                }
                            ],
                            initComplete: function () {
                                $('#CardHeaderHoldingDatatableManipulators').empty();
                                $('#CardHeaderHoldingDatatableManipulators2').empty();
                                $('#pivotTable_wrapper .dt-buttons').appendTo('#CardHeaderHoldingDatatableManipulators');
                                $('#pivotTable_wrapper .dt-search').appendTo('#CardHeaderHoldingDatatableManipulators');
                                $('#pivotTable_wrapper .dt-length').appendTo('#CardHeaderHoldingDatatableManipulators2');
                                $('#pivotTable_wrapper .dt-info').addClass("btn btn-sm").appendTo('#CardHeaderHoldingDatatableManipulators2');
                                $('#pivotTable_wrapper .dt-paging').addClass("btn btn-sm").appendTo('#CardHeaderHoldingDatatableManipulators2');
                                $('.dt-search label').remove();
                                $('.dt-search').children().removeClass("form-control form-control-sm").addClass("btn btn-sm btn-outline-dark");
                                table_click();
                            }
                        });


                    })
                    .catch(error => {
                        console.error('Failed to generate filename:', error);
                    });


            }
        });
    }


</script>
</body>
</html>
