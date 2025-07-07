<?php
include "_utils.php";
check_login();
init_page_serversides("", "x");
$projectID = $_SESSION["projectID"] ?? 75; // Fallback für Demo

// Datenbankverbindung herstellen
$conn = utils_connect_sql();

// Raumbereiche für das aktuelle Projekt laden
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
    <!-- JS-Bibliotheken -->
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
    <div class="row">
        <div class="col-lg-2 mx-auto mb-4" id="filterCardCol">
            <form id="filterForm">
                <div class="card">
                    <div class="card-header d-flex flex-nowrap">
                        <label for="raumbereich" class="form-label"></label>
                        <select id="raumbereich" name="raumbereich[]" class="form-select" style="width:100%" multiple>

                            <?php foreach ($raumbereichOptions as $option): ?>
                                <option value="<?= htmlspecialchars($option) ?>"><?= htmlspecialchars($option) ?></option>
                            <?php endforeach; ?>
                        </select>


                    </div>
                    <div class="card-body">
                        <div class="form-check  form-switch mt-2">
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

                        <button type="submit" class="btn  btn-success w-100">Anzeigen</button>
            </form>

        </div>

    </div>
</div>

<div class="col-lg-10 mx-auto" id="tableCardCol">
    <div class="card">
        <div class="card-header d-flex align-items-center">
            <button class="btn btn-outline-dark fa fa-arrow-left" id="ToggleCard"></button>
            <div class="d-flex align-items-center justify-content-end float-end"
                 id="CardHeaderHoldingDatatableManipulators"></div>
        </div>

        <div class="card-body p-0">
            <div id="pivotTableContainer">
                <!-- Die Pivot-Tabelle wird hier per AJAX geladen -->
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#raumbereich').select2({placeholder: "Raumbereich wählen"});


        function updateTransposeLabel() {
            $('#isTransposedLabel').text(
                $('#isTransposed').is(':checked') ? 'Räume als Zeilen' : 'Elemente als Zeilen'
            );
        }

        $('#isTransposed').on('change', updateTransposeLabel);


        $('#filterForm').on('submit', function (e) {
            e.preventDefault();
            loadPivotTable();
        });

        let filterVisible = true;

        $('#ToggleCard').on('click', function () {
            if (filterVisible) {
                // Filter-Card ausblenden, Tabellen-Card auf volle Breite
                $('#filterCardCol').hide();
                $('#tableCardCol').removeClass('col-lg-10').addClass('col-12');
                $(this).removeClass('fa-arrow-left').addClass('fa-arrow-right');
            } else {
                // Filter-Card einblenden, Tabellen-Card wieder schmal
                $('#filterCardCol').show();
                $('#tableCardCol').removeClass('col-12').addClass('col-lg-10');
                $(this).removeClass('fa-arrow-right').addClass('fa-arrow-left');
            }
            filterVisible = !filterVisible;
        });

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
            }
            let hideZeros = $('#hideZeros').is(':checked');


            $.ajax({
                url: 'pivot_table_ajax.php',
                method: 'POST',
                data: {
                    raumbereich: raumbereich, // This will be sent as an array
                    mtRelevant,
                    entfallen,
                    nurMitElementen,
                    ohneLeereElemente,
                    transponiert
                },
                traditional: true, // Important for sending arrays with jQuery
                success: function (data) {
                    $('#pivotTableContainer').html(data);

                    let colCount = $('#pivotTable thead th').length;

                    // Build columns definition for DataTables
                    let columns = [];
                    for (let i = 0; i < colCount; i++) {
                        if (i === 0) {
                            // First column: Element or Raum, don't change rendering
                            columns.push(null);
                        } else if (hideZeros) {
                            // For all other columns, hide zeros
                            columns.push({
                                render: function (data, type, row, meta) {
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
                        },
                        paging: true,
                        searching: true,
                        ordering: true,
                        info: true,
                        lengthChange: true,
                        pageLength: 50,
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
                                className: 'btn btn-success btn-sm'
                            }
                        ],
                        initComplete: function () {
                            let api = this.api();
                            $('#CardHeaderHoldingDatatableManipulators').empty();
                            $(api.buttons().container()).appendTo($('#CardHeaderHoldingDatatableManipulators'));
                            $('.dt-search label').remove();
                            $('.dt-search').children().removeClass("form-control form-control-sm").addClass("btn btn-sm btn-outline-dark").appendTo('#CardHeaderHoldingDatatableManipulators');
                        }
                    });

                }
            });
        }


    });

</script>
</body>
</html>
