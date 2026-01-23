<!DOCTYPE html>
<html lang="de">
<head>
    <title>Element Anzahl Änderungen</title>
    <meta charset="utf-8">
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
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>

</head>
<body>

<div id="limet-navbar"></div>
<?php require_once 'utils/_utils.php';
init_page_serversides("x"); ?>
<div class="container-fluid">
    <div class="row row-cols-2">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-4 d-flex ">
                            <span><strong>ÜBERSICHT RB Änderungen</strong> &emsp;</span>
                            <div class="me-2">
                                <label for="dateSelect" class="visually-hidden">Änderungsdatum</label>
                                <input type="date" id="dateSelect" name="dateSelect"
                                       class="form-control form-control-sm w-auto"
                                       data-bs-toggle="tooltip"
                                       data-bs-title="Änderungen ab welchem Datum laden?"
                                       data-bs-custom-class="custom-tooltip"/>
                            </div>
                        </div>
                        <div class="col-8 d-flex align-items-top justify-content-end" id="CardHeaderSub">
                        </div>
                    </div>
                </div>
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

                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>




<script>

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
        const rbChangeTable = $('#rbChangeTable').DataTable({
            ajax: {
                url: 'getFilteredElementChanges.php',
                type: 'POST',
                data: function (d) {
                    d.datum = $('#dateSelect').val();
                    return d;
                }
            },

            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json'
            },
            order: [[8, 'desc']], // sort descending by Timestamp column (index 8)
            select: "single",
            scrollY: '80vh',
            paging: true,
            pageLength: 25,
            lengthMenu: [[ 10, 25, 50, -1], ['10 rows', '20 rows', '50 rows', 'All']],
            layout: {
                topStart: null,
                topEnd: null,
                bottomStart: ['info', 'search'],
                bottomEnd: ['pageLength', 'paging']
            },
            initComplete: function () {
                $('.dt-search label').remove();
                $('.dt-search').children()
                    .removeClass('form-control form-control-sm')
                    .addClass("btn btn-sm btn-outline-secondary")
                    .appendTo('#CardHeaderSub');

                $('#dateSelect').on('change', debounce(function () {
                    console.log("Reloading table with date:", $(this).val());
                    if ($(this).val() !== $(this).data('oldValue')) {
                        $(this).data('oldValue', $(this).val());
                        rbChangeTable.ajax.reload(null, false);
                    }
                }, 1000));

            }
        });
    });
</script>
</body>
</html>
