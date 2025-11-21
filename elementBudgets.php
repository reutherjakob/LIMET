<?php
//"%FX
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

    <!-- Rework 2025 CDNs -->
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

    <div class="mt-4 card">
        <div class="card-header">

            <div class="row">
                <div class="col-8"><b>Elemente</b></div>
                <div class="col-4 d-flex flex-nowrap align-items-center justify-content-end" id="cardHeader"></div>
            </div>
        </div>
        <div class="card-body" id="elementBudgets">
            <table class="table table-striped table-bordered table-hover border border-light border-5 table-sm"
                   id="tableElementsInProjectForBudget">
                <thead>
                <tr>
                    <th>id</th>
                    <th>idBudget</th>
                    <th>Anzahl</th>
                    <th>ID</th>
                    <th>Element</th>

                    <th>Raumbereich</th>
                    <th>Raum</th>
                    <th>Bestand</th>
                    <th>Variante</th>
                    <th>EP</th>
                    <th>PP</th>
                    <th>Budget</th>
                    <th>BudgetNr</th>
                    <th>BudgetText</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>


<script>

    function makeToaster(headerText, success) {
        const existingToasts = Array.from(document.querySelectorAll('.toast'));
        const visibleToasts = existingToasts.filter(toast => toast.classList.contains('show'));
        const toast = document.createElement('div');
        toast.classList.add('toast', 'fade', 'show');
        toast.setAttribute('role', 'alert');
        toast.style.position = 'fixed';
        toast.style.right = '10px';
        headerText = headerText.replace(/\n/g, '<br>'); // Replace \n with <br>
        toast.innerHTML = `
        <div class="toast-header ${success ? "grün" : "rot"}">
            <strong class="mr-auto">${headerText}</strong>
        </div>`;
        document.body.appendChild(toast);

        const topPosition = 20 + visibleToasts.reduce((acc, t) => acc + t.offsetHeight + 10, 0);
        toast.style.top = `${topPosition}px`;

        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => {
                toast.remove();
                updateToastPositions();
            }, 50);
        }, 10000);
    }

    function updateToastPositions() {
        const visibleToasts = Array.from(document.querySelectorAll('.toast.show'));
        let topPosition = 10;
        visibleToasts.forEach(toast => {
            toast.style.top = `${topPosition}px`;
            topPosition += toast.offsetHeight + 10;
        });
    }

    $(document).ready(function () {
        $('#tableElementsInProjectForBudget').DataTable({
            ajax: {
                url: 'elementBudgetsData.php',
                dataSrc: ''
            },
            columns: [
                {data: 'id', visible: false, searchable: false},
                {data: 'idtabelle_projektbudgets', visible: false, searchable: false},
                {data: 'Anzahl'},
                {data: 'ElementID'},
                {data: 'Bezeichnung'},
                {data: 'Ausdr1'},
                {data: 'RaumFull'},
                {data: 'Ausdr2'},
                {data: 'Variante'},
                {data: 'Kosten'},
                {data: 'PP'},
                {
                    data: 'BudgetSelect',
                    orderable: true,   // aktivieren
                    searchable: false,
                    orderData: 12      // Gleiches Ergebnis wie Spalte 'BudgetID'
                },
                {data: 'BudgetID', visible: false, searchable: false},
                {data: 'BudgetText', visible: false, searchable: false}
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
                    config: {columns: [2, 3, 4, 5, 6, 7, 8, 9, 10]}
                },
                {
                    extend: 'excel',
                    text: 'Exportiere alle Zeilen',
                    exportOptions: {
                        modifier: {
                            search: 'applied', // berücksichtigt Filter
                            order: 'applied',  // respektiert Sortierung
                            page: 'all'        // exportiert alle Seiten, nicht nur die sichtbare
                        },
                        columns: [0,1,2,3,4,5,6,7,8,9,10,12,13], // Spalte 11 (BudgetSelect) ausschließen
                        format: {
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
            }
        });

        $('#tableElementsInProjectForBudget').on('change', 'tbody select', function () {
            let roombookID = this.id;
            let budgetID = this.value;
            let budgetText = $(this).find('option:selected').text();
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
                rowData.BudgetText = budgetText;
            }
        });

    });
</script>
</body>
</html>
