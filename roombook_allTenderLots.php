<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>RB-Losverwaltung</title>
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
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
    <!--DATEPICKER -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/css/bootstrap-datepicker3.min.css">
    <script type='text/javascript'
            src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/js/bootstrap-datepicker.min.js"></script>
    <!--Bootstrap Toggle -->
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>

</head>


<body id="bodyTenderLots">
<?php require_once 'utils/_utils.php';
init_page_serversides("x"); ?>
<div id="limet-navbar"></div>
<div class="container-fluid">
    <div class='row'>
        <div class='col-xxl-12' id="mainCardColumn">
            <div class="mt-4 card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-4 d-flex align-items-top justify-content-start">
                            <span><strong>ÜBERSICHT ALLE LOSE</strong> &emsp;</span>
                            <div class="me-2">
                                <label for="dateSelect" class="visually-hidden">Änderungsdatum</label>
                                <input type="date" id="dateSelect" name="dateSelect"
                                       class="form-control form-control-sm w-auto"
                                       data-bs-toggle="tooltip"
                                       data-bs-title="Ab welchem Versanddatum Lose laden?"
                                       data-bs-custom-class="custom-tooltip"/>
                            </div>
                        </div>
                        <div class="col-8 d-flex align-items-top justify-content-end" id="LoseCardHeaderSub">
                        </div>
                    </div>
                </div>

                <div class="card-body p-0 py-0 m-0" id="projectLots">
                    <table id="tableTenderLots"
                           class="table table-sm table-responsive table-striped compact border border-light border-1 w-100">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Projekt</th>
                            <th>LosNummer</th>
                            <th>Bezeichnung</th>
                            <th>Versand</th>
                            <th>Liefertermin</th>
                            <th>Verfahren</th>
                            <th>Status</th>
                            <th>Vergabesumme</th>
                            <th>Vergabesumme</th>
                            <th>Auftragnehmer</th>
                            <th>MKF-von_Los</th>
                            <th><i data-bs-toggle="tooltip" data-bs-placement="top" title="Workflow"
                                   class="fas fa-code-branch"></i></th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php require "modal_showLotWorkflow.php"; ?>

    <script src="utils/_utils.js"></script>
    <script charset="utf-8">
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        $(document).ready(function () {
            $('#dateSelect').val('2024-01-01');
            var tableTenderLots = new DataTable('#tableTenderLots', {
                ajax: {
                    url: 'getFilteredLots.php',
                    type: 'POST',
                    data: function (d) {
                        d.datum = $('#dateSelect').val();
                        return d;
                    }
                },
                columnDefs: [
                    {targets: [0, 9], visible: false, searchable: false},
                    {targets: [5, 10, 11], visible: false},
                    {targets: [0], sortable: false}
                ],

                select: true,
                search: {search: ''},
                paging: true,
                searching: true,
                info: true,
                order: [[2, 'asc']],
                pagingType: 'full',
                lengthChange: true,
                pageLength: 22,

                lengthMenu: [[10, 22, 50, 100], [10, 22, 50, 100]],
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json',
                    decimal: ',', thousands: '.', searchPlaceholder: 'Suche..', search: "", lengthMenu: "_MENU_"
                },
                scrollY: '78vh',
                buttons: [
                    {
                        extend: 'colvis', className: "btn btn-success fas fa-eye me-2 ms-2",
                        text: 'Spaltensichtbarkeit',
                        columns: function (idx) {
                            return idx !== 0 && idx !== 9;
                        }
                    },
                    {
                        extend: 'excel', className: "btn btn-success fas fa-file-excel me-2 ms-2",
                        title: "Losliste",
                        exportOptions: {
                            columns: function (idx) {
                                return idx !== 0 && idx !== 8;
                            }
                        }
                    },
                    {extend: 'searchBuilder', className: "btn btn-success fas fa-filter me-2 ms-2"}
                ],
                layout: {
                    topStart: null, topEnd: null,
                    bottomStart: ['pageLength', 'info'],
                    bottomEnd: ['paging', 'search', 'buttons']
                },
                initComplete: function () {
                    let sourceElements = document.getElementsByClassName("dt-buttons");
                    let targetElement = document.getElementById("LoseCardHeaderSub");
                    Array.from(sourceElements).forEach(function (element) {
                        targetElement.appendChild(element);
                    });
                    const button = document.querySelector(".dt-buttons");
                    if (button) button.classList.remove("dt-buttons");
                    $('.dt-search label').remove();
                    $('.dt-search').children()
                        .removeClass('form-control form-control-sm')
                        .addClass("btn btn-sm btn-outline-secondary")
                        .appendTo('#LoseCardHeaderSub');
                    $('#dateSelect').on('change', debounce(function () {
                        console.log("Reloading after debounce");
                        if ($(this).val() !== $(this).data('oldValue')) { // Nur bei neuem Datum
                            $(this).data('oldValue', $(this).val());
                            tableTenderLots.ajax.reload(null, false);
                        }
                    }, 2000));
                }
            });
        });

        $(document).on("click", "button[value='LotWorkflow']", function () {
            var ID = this.id;
            $.ajax({
                url: "getLotWorkflow.php",
                type: "POST",
                data: {lotID: ID},
                success: function (data) {
                    $("#workflowModalBody").html(data);
                }
            });
        });


    </script>
</body>
</html>
