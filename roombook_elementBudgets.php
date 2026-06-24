<?php
// 25 FX
require_once 'utils/_utils.php';
include "utils/_format.php";
init_page_serversides();
$mysqli = utils_connect_sql();
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>RB-Budgets</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
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
<div class="container-fluid">
    <div id="limet-navbar"></div>
    <div class="row">

        <div class="col-8 ps-3" id="elements_budgets_card_col">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6"><b>Elemente</b></div>
                        <div class="col-6 d-flex flex-nowrap align-items-center justify-content-end"
                             id="cardHeader"></div>
                    </div>
                </div>
                <div class="card-body p-2" id="elementBudgets">
                    <table class="table table-striped table-bordered table-hover table-sm"
                           id="tableElementsInProjectForBudget">
                        <thead class="">
                        <tr>
                            <th>id</th>
                            <th>idBudget</th>
                            <th>Raum</th>
                            <th>Raumbereich</th>
                            <th>
                                    <span data-bs-toggle="tooltip" data-bs-placement="top" title="Anzahl">
                                        <i class="fas fa-hashtag"></i>
                                    </span>
                            </th>
                            <th>
                                    <span data-bs-toggle="tooltip" data-bs-placement="top" title="Element ID">
                                        <i class="fas fa-fingerprint"></i>
                                    </span>
                            </th>
                            <th>Element</th>
                            <th>Bestand</th>
                            <th>
                                    <span data-bs-toggle="tooltip" data-bs-placement="top" title="Variante">
                                        <i class="fas fa-sitemap"></i>
                                    </span>
                            </th>
                            <th>EP</th>
                            <th>PP</th>
                            <th>Budget</th>
                            <th>Budget Nr</th>
                            <th>Budget Bezeichnung</th>
                            <th>
                                    <span data-bs-toggle="tooltip" data-bs-placement="top" title="Budget Status">
                                        <i class="fas fa-traffic-light"></i>
                                    </span>
                            </th>

                            <th>EP</th>
                            <th>PP</th>

                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-4 pe-3 ps-0" id="budgets_card_col">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2" id="budget_card_title">
                        <span>Projektbudgets</span>
                        <button type="button" class="btn btn-sm btn-outline-success" id="addBudget">
                            <i class="fas fa-plus-square"></i>
                        </button>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-dark" id="toggle_budget_card">
                        <i class="fas fa-caret-right"></i>
                    </button>
                </div>
                <div class="card-body p-2" id="">
                </div>
            </div>
        </div>
    </div>


    <!-- Modal: Budget hinzufügen -->
    <div class="modal fade" id="addBudgetModal" tabindex="-1" aria-labelledby="addBudgetModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addBudgetModalLabel">Neues Budget</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Schließen"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="inputBudgetnummer" class="form-label">Budgetnummer</label>
                        <input type="text" class="form-control" id="inputBudgetnummer" maxlength="45">
                    </div>
                    <div class="mb-3">
                        <label for="inputBudgetname" class="form-label">Budgetname</label>
                        <input type="text" class="form-control" id="inputBudgetname" maxlength="45">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Abbrechen</button>
                    <button type="button" class="btn btn-success" id="saveBudgetBtn">Speichern</button>
                </div>
            </div>
        </div>
    </div>


    <script src="utils/_utils.js"></script>
    <!--suppress EqualityComparisonWithCoercionJS -->
    <script>

        const addBudgetModal = new bootstrap.Modal('#addBudgetModal');

        const STATUS = {
            0: {label: 'Offen', cls: 'warning', icon: 'fa-minus'},
            1: {label: 'Freigegeben', cls: 'success', icon: 'fa-check'},
            2: {label: 'Abgelehnt', cls: 'danger', icon: 'fa-times'},
        };

        function statusBadge(s) {
            const st = STATUS[s] ?? {label: '?', cls: 'secondary', icon: 'fa-question'};
            return `<span class="badge bg-${st.cls}"
                  style="width:1.8rem; display:inline-block;"
                  title="${st.label}"
                  data-bs-toggle="tooltip"
                  data-bs-placement="top">
                <i class="fas ${st.icon}"></i>
            </span>`;
        }

        // Budgetliste für das clientseitig gebaute <select> in der Elemente-Tabelle
        let budgetList = [];

        function loadBudgetList() {
            return $.get('getBudgets.php', function (data) {
                budgetList = data;

                const $body = $('#budgets_card_col .card-body').empty();
                if (!data.length) {
                    $body.html('<p class="text-muted p-2">Keine Budgets</p>');
                    return;
                }

                const $table = $(`
                    <table class="table table-sm table-striped table-bordered">
                        <thead class="">
                            <tr>
                                <th>Nr.</th>
                                <th>Name</th>
                                <th>
                                    <span data-bs-toggle="tooltip" data-bs-placement="top" title="Budget Status">
                                        <i class="fas fa-traffic-light"></i>
                                    </span>
                                </th>
                                <th><i class="fas fa-edit"></i></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                `).appendTo($body);
                const $tbody = $table.find('tbody');
                data.forEach(b => {
                    const $row = $(`
                <tr>
                    <td>${b.Budgetnummer}</td>
                    <td>${b.Budgetname}</td>
                    <td>${statusBadge(b.status)}</td>
                    <td>
                        <select class="form-select form-select-sm budget-status-select" data-id="${b.idtabelle_projektbudgets}">
                            <option value="0" ${b.status == 0 ? 'selected' : ''}>Offen</option>
                            <option value="1" ${b.status == 1 ? 'selected' : ''}>Freigegeben</option>
                            <option value="2" ${b.status == 2 ? 'selected' : ''}>Abgelehnt</option>
                        </select>
                    </td>
                </tr>
            `);
                    $tbody.append($row);
                });
            }, 'json');
        }

        // baut das <select> für eine Element-Zeile aus der globalen budgetList
        function renderBudgetSelect(rowID, selectedBudgetID) {
            let html = `<select class="form-control form-control-sm" id="${rowID}">`;
            html += `<option value="0"${selectedBudgetID == 0 ? ' selected' : ''}>0-Budget wählen</option>`;
            budgetList.forEach(b => {
                const id = b.idtabelle_projektbudgets;
                const selected = (id == selectedBudgetID) ? ' selected' : '';
                html += `<option value="${id}"${selected}>${b.Budgetnummer}-${b.Budgetname}</option>`;
            });
            return html + '</select>';
        }

        $('#addBudget').click(function () {
            $('#inputBudgetnummer').val('');
            $('#inputBudgetname').val('');
            addBudgetModal.show();
        });

        $('#saveBudgetBtn').click(function () {
            const Budgetnummer = $('#inputBudgetnummer').val().trim();
            const Budgetname = $('#inputBudgetname').val().trim();

            if (!Budgetnummer || !Budgetname) {
                makeToaster('Bitte Budgetnummer und Budgetname ausfüllen');
                return;
            }

            $.post('addBudget.php', {Budgetnummer, Budgetname}, function (res) {
                if (res.status === 'ok') {
                    makeToaster(res.msg, true);
                    addBudgetModal.hide();
                    // erst Budgetliste aktualisieren (für das <select>), dann Tabelle neu laden
                    loadBudgetList().done(function () {
                        $('#tableElementsInProjectForBudget').DataTable().ajax.reload(null, false);
                    });
                } else {
                    makeToaster(res.msg || 'Fehler beim Anlegen');
                }
            }, 'json');
        });


        $(document).on('change', '.budget-status-select', function () {
            const budgetID = $(this).data('id');
            const status = $(this).val();
            $.post('getBudgets.php', {budgetID, status}, function () {
                makeToaster('Status gespeichert', true);
                loadBudgetList().done(function () {
                    $('#tableElementsInProjectForBudget').DataTable().ajax.reload(null, false);
                });
            }, 'json');
        });


        function initElementsTable() {
            $('#tableElementsInProjectForBudget').DataTable({
                deferRender: true,
                ajax: {
                    url: 'get_elementBudgetsData.php',
                    dataSrc: '',
                    error: function (xhr) {
                        console.log('RAW RESPONSE:', xhr.responseText.substring(0, 500));
                        alert(xhr.responseText.substring(0, 500));
                    }
                },
                columns: [
                    {data: 'id', visible: false, searchable: false},
                    {data: 'idtabelle_projektbudgets', visible: false, searchable: false},
                    {data: 'RaumFull'},
                    {data: 'Ausdr1'},
                    {data: 'Anzahl'},
                    {data: 'ElementID'},
                    {data: 'Bezeichnung'},
                    {data: 'Ausdr2'},
                    {data: 'Variante'},
                    {data: 'Kosten'},
                    {data: 'PP'},
                    {
                        data: 'BudgetSelect',
                        orderable: true,
                        searchable: false,
                        orderData: 12,                      // sortiert über die Spalte 'BudgetID'
                        render: function (data, type, row) {
                            if (type !== 'display') return data;   // sortieren/filtern auf der Zahl
                            return renderBudgetSelect(row.id, data);
                        }
                    },
                    {data: 'BudgetID', visible: false, searchable: false},
                    {data: 'BudgetBezeichnung', visible: false, searchable: false},
                    {
                        data: 'BudgetStatus',
                        searchable: false,
                        orderable: true,
                        render: function (data) {
                            if (data === -1 || data === null) return '<span class="badge bg-secondary">-</span>';
                            return statusBadge(data);
                        }
                    },

                    {data: 'KostenRaw', visible: false, searchable: false},
                    {data: 'PPRaw', visible: false, searchable: false},

                ],
                paging: true,
                order: [[3, "asc"]],
                lengthChange: true,
                pageLength: 25,
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json",
                    decimal: ",",
                    thousands: ".",
                    search: "",
                    searchPlaceholder: "Suche..."
                },
                mark: true,
                layout: {
                    bottomStart: null, topStart: null,
                    bottomEnd: ['info', "pageLength", "paging"],
                    topEnd: ["search", 'buttons']
                },
                buttons: [
                    {
                        extend: 'searchBuilder',
                        config: {columns: [2, 3, 4, 5, 6, 7, 8, 9, 10]},
                        className: 'btn-sm btn-outline-dark btn-light',
                    },
                    {
                        extend: 'excel',
                        text: '<i class="fas fa-file-excel"></i> Excel',
                        className: 'btn-sm btn-outline-dark btn-light',
                        exportOptions: {
                            modifier: {
                                search: 'applied',
                                order: 'applied',
                                page: 'all'
                            },
                            columns: [2, 3, 4, 5, 6, 7, 8, 12, 13, 14, 15, 16],
                            format: {
                                header: function (data, columnIdx) {
                                    const iconHeaders = {
                                        2: 'Anzahl',
                                        3: 'Element ID',
                                        6: 'Variante',
                                        12: 'Budget Status'
                                    };
                                    if (iconHeaders[columnIdx] !== undefined) {
                                        return iconHeaders[columnIdx];
                                    }
                                    return data.replace(/<[^>]*>/g, '').trim();
                                },
                                body: function (data, row, column, node) {
                                    if (node && node.querySelector && node.querySelector('select')) {
                                        const selected = node.querySelector('select option:checked');
                                        return selected ? selected.textContent.trim() : '';
                                    }
                                    return typeof data === 'string'
                                        ? data.replace(/<[^>]*>/g, '').trim()
                                        : data;
                                }
                            }
                        }
                    }
                ],
                initComplete: function () {
                    $('.dt-search label').remove();
                    $('.dt-search').children().removeClass("form-control form-control-sm").addClass("btn btn-sm btn-outline-dark").appendTo('#cardHeader');
                    $('.dt-buttons').children().addClass("btn-sm ms-1 me-1").appendTo('#cardHeader');

                    $(document).on('mouseenter', '[data-bs-toggle="tooltip"]', function () {
                        const t = bootstrap.Tooltip.getInstance(this) || new bootstrap.Tooltip(this);
                        t.show();
                    });
                    $(document).on('mouseleave', '[data-bs-toggle="tooltip"]', function () {
                        bootstrap.Tooltip.getInstance(this)?.hide();
                    });
                }
            });

            $('#tableElementsInProjectForBudget').on('change', 'tbody select', function () {
                let roombookID = this.id;
                let budgetID = this.value;
                let BudgetBezeichnung = $(this).find('option:selected').text();
                $.ajax({
                    url: "saveRoombookBudget.php",
                    data: {"roombookID": roombookID, "budgetID": budgetID},
                    type: "POST",
                    success: function (response) {
                        makeToaster(response, true);
                    },
                    error: function () {
                        makeToaster("Fehler beim Speichern des Budgets");
                    }
                });
                let table = $('#tableElementsInProjectForBudget').DataTable();
                let row = table.row($('#' + roombookID).closest('tr'));
                if (row.length) {
                    let rowData = row.data();
                    rowData.BudgetID = budgetID;
                    rowData.BudgetBezeichnung = BudgetBezeichnung;
                }
            });
        }


        $(document).ready(function () {
            // Tabelle erst initialisieren, wenn die Budgetliste geladen ist,
            // damit die <select> beim ersten Rendern korrekt befüllt sind
            loadBudgetList().done(initElementsTable);

            $('#toggle_budget_card').click(function () {

                if ($('#budgets_card_col').hasClass('collapsed')) {
                    $(this).html("<i class='fas fa-caret-right'></i>");
                    $('#budgets_card_col').find('.card-body').removeClass('d-none');
                    $('#budgets_card_col').removeClass('col-auto collapsed').addClass('col-4');
                    $('#elements_budgets_card_col').removeClass('col').addClass('col-8');
                    $('#budget_card_title').removeClass('d-none');

                } else {
                    $(this).html("<i class='fas fa-caret-left'></i>");
                    $('#budgets_card_col').find('.card-body').addClass('d-none');
                    $('#budgets_card_col').removeClass('col-4').addClass('col-auto collapsed');
                    $('#elements_budgets_card_col').removeClass('col-8').addClass('col');
                    $('#budget_card_title').addClass('d-none');
                }
            });
        });

    </script>
</body>
</html>