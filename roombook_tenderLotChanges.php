<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>RB-Los Änderungen Historie</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen">

    <!-- Bootstrap & DataTables -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.2.1/af-2.7.0/b-3.2.1/b-colvis-3.2.1/b-html5-3.2.1/b-print-3.2.1/cr-2.0.4/date-1.5.5/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.1/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-3.0.0/sr-1.4.1/datatables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link href="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.2.1/af-2.7.0/b-3.2.1/b-colvis-3.2.1/b-html5-3.2.1/b-print-3.2.1/cr-2.0.4/date-1.5.5/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.1/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-3.0.0/sr-1.4.1/datatables.min.css"
          rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css">

    <style>
        .dataTables_wrapper .dataTables_filter input {
            margin-left: 0.5em;
        }

        .badge {
            font-size: 0.75em;
        }

        .change-highlight {
            background-color: #fff3cd;
        }

        th, td {
            text-align: center;
            vertical-align: middle;
        }

        .table th {
            white-space: nowrap;
        }
    </style>
</head>
<body>
<?php require_once "utils/_utils.php";
include 'utils/_format.php';
init_page_serversides("x"); ?>

<div id="limet-navbar"></div>

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-history me-2"></i>RB-Los Änderungen Historie</h4>
                    <small class="text-white-50">Nur Einträge mit Änderungen bei Lose-Intern/Extern</small>
                </div>
                <div class="card-body p-0">
                    <table id="losAenderungenTable" class="table table-striped table-hover mb-0" style="width:100%">
                        <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>LOS-ID</th>
                            <th>Beschreibung</th>
                            <th>Los Intern<br>Änderung</th>
                            <th>Los Extern<br>Änderung</th>
                            <th>Anzahl</th>
                            <th>Anschaffung</th>
                            <th>Datum</th>
                            <th>Benutzer</th>
                            <th>Aktion</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Details Modal -->
<div class="modal fade" id="detailsModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Änderungsdetails</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailsContent">
                <!-- Details werden per AJAX geladen -->
            </div>
        </div>
    </div>
</div>

<script>$(document).ready(function () {
        const table = $('#losAenderungenTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: 'getLosAenderungen.php',
                type: 'POST'
            },
            pageLength: 25,
            order: [[7, 'desc']], // Datum absteigend
            columnDefs: [
                {targets: 0, visible: false}, // ID verstecken
                {targets: [3, 4], className: 'change-highlight'}, // Lose Spalten
                {targets: '_all', className: 'dt-center'}
            ],
            dom: 'Bfrtip',
            buttons: ['copy', 'csv', 'excel', 'pdf', 'print', 'colvis'],
            language: {url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/de-DE.json'},
            drawCallback: function () {
                // Details Button Events
                $('.view-details').off('click').on('click', function () {
                    const id = $(this).data('id');
                    console.log('ID:', id);
                    loadDetails(id);
                });
            }
        });
        window.loadDetails = function (id) {
            $.post('getLosAenderungenDetails.php', {id: id})
                .done(function (html) {
                    $('#detailsContent').html(html);
                })
                .fail(function () {
                    $('#detailsContent').html('<div class="alert alert-danger">Fehler!</div>');
                })
                .always(function () {
                    new bootstrap.Modal(document.getElementById('detailsModal')).show();
                });
        };
    });
</script>
</body>
</html>
