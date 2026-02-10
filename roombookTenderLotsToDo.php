<?php
require_once "utils/_utils.php";
init_page_serversides();
?>

<!DOCTYPE html>
<html lang="de" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Ausschreibungsverwaltung - Los ToDos</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="Logo/iphone_favicon.png"/>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-datepicker@1.10.0/dist/js/bootstrap-datepicker.min.js"></script>
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-datepicker@1.10.0/dist/css/bootstrap-datepicker.min.css">
    <script src="utils/_utils.js"></script>
    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen"/>
</head>

<body>
<div class="container-fluid">
    <div id="limet-navbar"></div>
    <!--div class="row mt-4">
        <div class="col-md-3">
            <div class="card border-danger border-start border-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h6 class="text-uppercase text-muted mb-1">Offen</h6>
                            <h2 class="mb-0" id="stat-offen">-</h2>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-circle fa-3x text-danger opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-primary border-start border-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h6 class="text-uppercase text-muted mb-1">Wartend</h6>
                            <h2 class="mb-0" id="stat-wartend">-</h2>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-3x text-primary opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-success border-start border-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h6 class="text-uppercase text-muted mb-1">Fertig</h6>
                            <h2 class="mb-0" id="stat-fertig">-</h2>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-3x text-success opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-secondary border-start border-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h6 class="text-uppercase text-muted mb-1">Total</h6>
                            <h2 class="mb-0" id="stat-total">-</h2>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-list fa-3x text-secondary opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div-->


    <div class="row mt-4">
        <div class="col-16">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="row">
                        <div class="col-4">
                            <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Neuer Todo-Eintrag</h5></div>
                        <div class="col-8">
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-success btn-sm me-3">
                                    <i class='fas fa-plus'> </i> Hinzufügen
                                </button>
                                <button type="reset" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-times"></i> Zurücksetzen
                                </button>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="card-body">
                    <form id="form-add-todo">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="select_los" class="form-label invisible"> <span class="text-danger"></span></label>
                                <select class="form-select form-select-sm" id="select_los" name="select_los" required>
                                    <option value="">Gewerk/Los wählen...</option>
                                    <?php
                                    $mysqli = utils_connect_sql();
                                    $sql = "SELECT p.Interne_Nr, p.Projektname, 
                                                   l.LosNr_Extern, l.LosBezeichnung_Extern, 
                                                   l.idtabelle_Lose_Extern,
                                                   l.Vergabe_abgeschlossen
                                            FROM tabelle_lose_extern l 
                                            INNER JOIN tabelle_projekte p 
                                                ON l.tabelle_projekte_idTABELLE_Projekte = p.idTABELLE_Projekte
                                            WHERE p.idTABELLE_Projekte != 4
                                            ORDER BY p.Interne_Nr DESC, l.LosNr_Extern";
                                    $result = $mysqli->query($sql);
                                    while ($row = $result->fetch_assoc()) {
                                        $statusIcon = '';
                                        switch ($row["Vergabe_abgeschlossen"]) {
                                            case 0:
                                                $statusIcon = '🔴';
                                                break;
                                            case 1:
                                                $statusIcon = '✅';
                                                break;
                                            case 2:
                                                $statusIcon = '🔵';
                                                break;
                                        }
                                        echo "<option value='" . h($row["idtabelle_Lose_Extern"]) . "'>"
                                            . $statusIcon . " "
                                            . h($row["Interne_Nr"]) . " - "
                                            . h($row["LosNr_Extern"]) . " "
                                            . h($row["LosBezeichnung_Extern"]) . "</option>";
                                    }
                                    ?>
                                </select>
                                <label for="select_element" class="form-label"></label>
                                <select class="form-select form-select-sm" id="select_element" name="select_element"
                                        required>
                                    <option value="">Element wählen...</option>
                                    <?php
                                    $sql = "SELECT idTABELLE_Elemente, ElementID, Bezeichnung
                                            FROM tabelle_elemente
                                            ORDER BY ElementID";
                                    $result = $mysqli->query($sql);
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<option value='" . h($row["idTABELLE_Elemente"]) . "'>"
                                            . h($row["ElementID"]) . " - "
                                            . h($row["Bezeichnung"]) . "</option>";
                                    }
                                    ?>
                                </select>

                                <div class="row mt-2 ms-1">
                                    <div class="col-4">
                                        <label for="datum" class="form-label">Datum</label>
                                    </div>
                                    <div class="col-8">
                                        <input type="text" class="form-control form-control-sm" id="datum"
                                               placeholder="TT.MM.JJJJ" required autocomplete="off">
                                    </div>
                                </div>

                            </div>

                            <div class="col-md-9">
                                <label for="input_todo" class="form-label"> </label>
                                <textarea class="form-control form-control-sm"
                                          rows="5"
                                          id="input_todo"
                                          placeholder="Todo/Info/Frage  eingeben..."
                                          required></textarea>


                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <!-- Main Table -->
    <div class="row mt-2">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-10">
                            <i class="fas fa-list me-2"></i>Ausschreibungs-Einträge
                        </div>
                        <div class="col-1">
                            <label for="filter_status" class="invisible"></label>
                            <select class="form-select form-select-sm " id="filter_status">
                                <option value="">Status Filter</option>
                                <option value="0">Offen</option>
                                <option value="2">Wartend</option>
                                <option value="1">Fertig</option>
                            </select>
                        </div>

                        <div class="col-1 d-flex justify-content-end" id="losEinträge"></div>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-striped table-bordered table-hover table-sm"
                           id="tableAusschreibungsTodos" style="width:100%">
                        <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Projekt#</th>
                            <th>Projekt</th>
                            <th>Los#</th>
                            <th>Los</th>
                            <th>Status</th>
                            <th>ElementID</th>
                            <th>Element</th>
                            <th>Datum</th>
                            <th>Ersteller</th>
                            <th>Todo Preview</th>
                            <th>Aktionen</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Todo bearbeiten</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="form-edit-todo">
                    <input type="hidden" id="edit_id" name="edit_id">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Projekt:</label>
                            <p id="edit_projekt" class="mb-0"></p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Los:</label>
                            <p id="edit_los" class="mb-0"></p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Element:</label>
                            <p id="edit_element" class="mb-0"></p>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_datum" class="form-label">Datum</label>
                            <input type="text" class="form-control form-control-sm" id="edit_datum" autocomplete="off">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Ersteller:</label>
                            <p id="edit_ersteller" class="mb-0"></p>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_status" class="form-label">Status ändern</label>
                            <select class="form-select form-select-sm" id="edit_status">
                                <option value="0">Offen</option>
                                <option value="2">Wartend</option>
                                <option value="1">Fertig</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="edit_todo_text" class="form-label">Todo/Info/Frage</label>
                        <textarea class="form-control" rows="8" id="edit_todo_text" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Abbrechen
                </button>
                <button type="button" class="btn btn-primary btn-sm" id="btn-save-edit">
                    <i class="fas fa-save me-1"></i>Speichern
                </button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="viewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-eye me-2"></i>Todo Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Projekt:</label>
                        <p id="view_projekt" class="mb-0"></p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Los:</label>
                        <p id="view_los" class="mb-0"></p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Element:</label>
                        <p id="view_element" class="mb-0"></p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Datum:</label>
                        <p id="view_datum" class="mb-0"></p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Ersteller:</label>
                        <p id="view_ersteller" class="mb-0"></p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Status:</label>
                        <p id="view_status" class="mb-0"></p>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Todo/Info/Frage:</label>
                    <div class="border p-3 bg-light rounded">
                        <p id="view_todo_text" class="mb-0"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Schließen
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let tableTodos;
    let currentEditId = null;

    $(document).ready(function () {

        $('#datum, #edit_datum').datepicker({
            format: 'dd.mm.yyyy',
            language: 'de',
            autoclose: true,
            todayHighlight: true,
            todayBtn: 'linked'
        });
        $('#datum').datepicker('setDate', new Date());
        initializeTable();
        loadStatistics();
        $('#form-add-todo').on('submit', function (e) {
            e.preventDefault();
            addTodo();
        });
        $('#filter_status').on('change', function () {
            applyFilters();
        });
        $('#btn-save-edit').on('click', function () {
            saveTodoEdit();
        });
        //$('#btn-export-excel').on('click', function () {
        //    tableTodos.button('.buttons-excel').trigger();
        // });
    });

    function initializeTable() {
        tableTodos = $('#tableAusschreibungsTodos').DataTable({
            ajax: {
                url: 'api_los_todos.php',
                type: 'POST',
                data: {action: 'get_all_todos'},
                dataSrc: function (json) {
                    return json.success ? json.data : [];
                }
            },
            columns: [
                {data: 'id_tabelle_lose_ToDos'},
                {data: 'Interne_Nr'},
                {data: 'Projektname'},
                {data: 'LosNr_Extern'},
                {data: 'LosBezeichnung_Extern'},
                {
                    data: 'Vergabe_abgeschlossen',
                    render: function (data) {
                        switch (parseInt(data)) {
                            case 0:
                                return '<span class="badge bg-danger">Offen</span>';
                            case 1:
                                return '<span class="badge bg-success">Fertig</span>';
                            case 2:
                                return '<span class="badge bg-primary">Wartend</span>';
                            default:
                                return '<span class="badge bg-secondary">Unbekannt</span>';
                        }
                    }
                },
                {data: 'ElementID'},
                {data: 'Bezeichnung'},
                {data: 'Datum'},
                {data: 'Ersteller'},
                {
                    data: 'ToDo',
                    render: function (data) {
                        if (data && data.length > 50) {
                            return '<span title="' + data + '">' + data.substr(0, 50) + '...</span>';
                        }
                        return data;
                    }
                },
                {
                    data: null,
                    orderable: false,
                    render: function (data, type, row) {
                        return `
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-dark btn-view" data-id="${row.id_tabelle_lose_ToDos}" title="Ansehen">
                                <i class="fas fa-info-circle"></i>
                            </button>
                            <button class="btn btn-success btn-edit" data-id="${row.id_tabelle_lose_ToDos}" title="Bearbeiten">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-delete" data-id="${row.id_tabelle_lose_ToDos}" title="Löschen">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    `;
                    }
                }
            ],
            columnDefs: [
                {targets: [0], visible: false, searchable: false}
            ],
            order: [[1, 'desc'], [3, 'asc']],
            pageLength: 25,
            responsive: true,
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excel',
                    text: 'Excel',
                    className: 'btn btn-success btn-sm d-none',
                    exportOptions: {columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]}
                }
            ],
            language: {
                url: '//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json',
                search: '_INPUT_',
                searchPlaceholder: 'Suche...'
            },
            initComplete: function () {
                $('.dt-search label').remove();
                $('.dt-search').children().removeClass("form-control form-control-sm").addClass("btn btn-sm btn-outline-dark").appendTo('#losEinträge');
            }
        });

        // Event delegation for action buttons
        $('#tableAusschreibungsTodos tbody').on('click', '.btn-view', function () {
            viewTodo($(this).data('id'));
        });

        $('#tableAusschreibungsTodos tbody').on('click', '.btn-edit', function () {
            editTodo($(this).data('id'));
        });

        $('#tableAusschreibungsTodos tbody').on('click', '.btn-delete', function () {
            deleteTodo($(this).data('id'));
        });
    }

    function loadStatistics() {
        $.post('api_los_todos.php', {action: 'get_statistics'}, function (response) {
            if (response.success) {
                $('#stat-total').text(response.data.total || 0);
                $('#stat-offen').text(response.data.offen || 0);
                $('#stat-wartend').text(response.data.wartend || 0);
                $('#stat-fertig').text(response.data.fertig || 0);
            }
        });
    }

    function addTodo() {
        const datumVal = $('#datum').val();
        const dateParts = datumVal.split('.');
        const datumISO = dateParts.length === 3 ? `${dateParts[2]}-${dateParts[1]}-${dateParts[0]}` : '';

        const data = {
            action: 'add_todo',
            los_id: $('#select_los').val(),
            element_id: $('#select_element').val(),
            datum: datumISO,
            todo_text: $('#input_todo').val()
        };

        $.post('api_los_todos.php', data, function (response) {
            if (response.success) {
                makeToaster(response.message, true);
                $('#form-add-todo')[0].reset();
                $('#datum').datepicker('setDate', new Date());
                tableTodos.ajax.reload();
                loadStatistics();
            } else {
                makeToaster(response.message, false);
            }
        });
    }

    function viewTodo(id) {
        const rowData = tableTodos.rows().data().toArray().find(row => row.id_tabelle_lose_ToDos == id);

        if (rowData) {
            $('#view_projekt').text(rowData.Interne_Nr + ' - ' + rowData.Projektname);
            $('#view_los').text(rowData.LosNr_Extern + ' - ' + rowData.LosBezeichnung_Extern);
            $('#view_element').text(rowData.ElementID + ' - ' + rowData.Bezeichnung);
            $('#view_datum').text(rowData.Datum);
            $('#view_ersteller').text(rowData.Ersteller);

            let statusText = '';
            switch (parseInt(rowData.Vergabe_abgeschlossen)) {
                case 0:
                    statusText = '🔴 Offen';
                    break;
                case 1:
                    statusText = '✅ Fertig';
                    break;
                case 2:
                    statusText = '🔵 Wartend';
                    break;
            }
            $('#view_status').html('<span class="badge bg-secondary">' + statusText + '</span>');
            $('#view_todo_text').text(rowData.ToDo);

            $('#viewModal').modal('show');
        }
    }

    function editTodo(id) {
        const rowData = tableTodos.rows().data().toArray().find(row => row.id_tabelle_lose_ToDos == id);

        if (rowData) {
            currentEditId = id;
            $('#edit_id').val(id);
            $('#edit_projekt').text(rowData.Interne_Nr + ' - ' + rowData.Projektname);
            $('#edit_los').text(rowData.LosNr_Extern + ' - ' + rowData.LosBezeichnung_Extern);
            $('#edit_element').text(rowData.ElementID + ' - ' + rowData.Bezeichnung);

            // Convert YYYY-MM-DD to DD.MM.YYYY for datepicker
            const dateParts = rowData.Datum.split('-');
            const datumDE = dateParts.length === 3 ? `${dateParts[2]}.${dateParts[1]}.${dateParts[0]}` : rowData.Datum;
            $('#edit_datum').val(datumDE);

            $('#edit_ersteller').text(rowData.Ersteller);
            $('#edit_status').val(rowData.Vergabe_abgeschlossen);
            $('#edit_todo_text').val(rowData.ToDo);

            $('#editModal').modal('show');
        }
    }

    function saveTodoEdit() {
        const datumVal = $('#edit_datum').val();
        const dateParts = datumVal.split('.');
        const datumISO = dateParts.length === 3 ? `${dateParts[2]}-${dateParts[1]}-${dateParts[0]}` : '';

        const data = {
            action: 'update_todo',
            id: currentEditId,
            todo_text: $('#edit_todo_text').val(),
            datum: datumISO
        };

        $.post('api_los_todos.php', data, function (response) {
            if (response.success) {
                makeToaster('Todo erfolgreich aktualisiert', true);
                $('#editModal').modal('hide');
                tableTodos.ajax.reload();
                loadStatistics();

                // const newStatus = $('#edit_status').val();
                // const statusData = {
                //     action: 'update_status',
                //     id: currentEditId,
                //     status: newStatus
                // };
                // $.post('api_los_todos.php', statusData, function () {
                // });

            } else {
                makeToaster(response.message, false);
            }
        });
    }

    function deleteTodo(id) {
        if (confirm('Möchten Sie diesen Todo-Eintrag wirklich löschen?')) {
            $.post('api_los_todos.php', {action: 'delete_todo', id: id}, function (response) {
                if (response.success) {
                    makeToaster(response.message, true);
                    tableTodos.ajax.reload();
                    loadStatistics();
                } else {
                    makeToaster(response.message, false);
                }
            });
        }
    }

    function applyFilters() {
        const status = $('#filter_status').val();
        if (status === '') {
            resetFilters();
            return;
        }
        tableTodos.destroy();
        tableTodos = $('#tableAusschreibungsTodos').DataTable({
            ajax: {
                url: 'api_los_todos.php',
                type: 'POST',
                data: status !== '' ? {action: 'filter_todos', status: status} : {action: 'get_all_todos'},
                dataSrc: function (json) {
                    return json.success ? json.data : [];
                }
            },
            columns: tableTodos.settings()[0].aoColumns.map(col => ({
                data: col.mData,
                render: col.mRender,
                orderable: col.bSortable
            })),
            columnDefs: [{targets: [0], visible: false, searchable: false}],
            order: [[1, 'desc'], [3, 'asc']],
            pageLength: 25,
            language: {url: '//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json'}
        });

        // Re-attach event handlers
        $('#tableAusschreibungsTodos tbody').off('click').on('click', '.btn-view', function () {
            viewTodo($(this).data('id'));
        }).on('click', '.btn-edit', function () {
            editTodo($(this).data('id'));
        }).on('click', '.btn-delete', function () {
            deleteTodo($(this).data('id'));
        });
    }


</script>

</body>
</html>