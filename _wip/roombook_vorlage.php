<?php
/* =====================================================================
 *  SEITEN-VORLAGE – Stand 2026
 * =====================================================================
 *
 *  KONVENTIONEN:
 *
 *  0. Pfade
 *     - PHP-Includes immer über $PHP_ROOT (absoluter Dateisystempfad).
 *     - Browser-Pfade (CSS, JS, Bilder, JSON) immer über $WEB_ROOT.
 *     - Liegt die Seite woanders, NUR $WEB_ROOT anpassen
 *       (Projektroot: '' | in utils/: '../' | zwei Ebenen tief: '../../').
 *
 *  1. Styling
 *     - KEIN eigenes CSS. Nur Bootstrap 5.2.3 Utility-/Komponentenklassen.
 *     - Icons: Font Awesome 5.9 (z.B. <i class="fas fa-save"></i>).
 *
 *  2. Serverseite
 *     - Jede Seite beginnt mit init_page_serversides() (Login-Check + DB).
 *       (Altform: check_login(); $mysqli = utils_connect_sql();)
 *     - Alles wird per POST über die utils-Funktionen gesendet/geholt.
 *     - POST-Endpunkte (eigene .php-Dateien) beginnen mit:
 *           require_once __DIR__ . '/_utils.php';     // bzw. Pfad zu utils/
 *           check_login();
 *           require_once __DIR__ . '/csrf.php';
 *           require_csrf();
 *     - Ausgaben in HTML immer mit h() escapen.
 *
 *  3. Layout
 *     - Body startet IMMER mit:  <div id="limet-navbar"></div>
 *     - Danach container-fluid > row > col-* > card (class="card", NICHT id="card").
 *     - IDs nur einmal pro Seite vergeben.
 *
 *  4. Tabellen
 *     - class="table table-sm table-striped table-hover border border-light border-5"
 *     - DataTables: language url = WEB_ROOT + 'utils/de-DE.json'
 *     - Such-/Button-Controls in den Card-Header verschieben:
 *           relocateControls(this.api(), '#XyCardHeader');   (in initComplete)
 *       Alternativ manuell:
 *           $('.dt-search label').remove();
 *           $('.dt-search').children()
 *               .removeClass('form-control form-control-sm')
 *               .addClass('btn btn-sm btn-outline-dark')
 *               .appendTo('#XyCardHeader');
 *     - Excel-Export: filename aus PHP $excelfilename (buildExcelFilename()).
 *
 *  5. Scripts
 *     - Eigene <script>-Blöcke IMMER innerhalb von <body> (vor </body>).
 *     - jQuery als ERSTES laden, utils/_utils.js als LETZTES.
 * ===================================================================== */

// ===== PFADE =====
$PHP_ROOT = dirname(__DIR__);   // Projektroot im Dateisystem (diese Datei liegt in /utils)
$WEB_ROOT = '../';              // relativer URL-Pfad von dieser Seite zum Projektroot

// ===== SERVERSEITE =====
require_once $PHP_ROOT . '/_utils/_utils.php';
init_page_serversides();
require_once $PHP_ROOT . '/_utils/csrf.php';

$pageTitle     = 'Seitentitel';
$excelfilename = buildExcelFilename('');
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <!-- ================= META ================= -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="csrf" content="<?= h(csrf_token()) ?>">
    <title><?= h($pageTitle) ?></title>

    <!-- ================= FAVICON ================= -->
    <link rel="icon" href="<?= h($WEB_ROOT) ?>../Logo/iphone_favicon.png"/>

    <!-- ================= JQUERY (muss zuerst!) ================= -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <!-- ================= BOOTSTRAP 5.2.3 ================= -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- ================= FONT AWESOME 5.9 ================= -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css"
          integrity="sha512-q3eWabyZPc1XTCmF+8/LuE1ozpg5xxn7iO89yfSOd5/oKvyqLngoNGsx8jq92Y8eXJ/IRxQbEC+FGSYxtk2oiw=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>

    <!-- ================= DATATABLES 2.2.1 (BS5-Bundle inkl. Extensions + JSZip) ================= -->
    <link rel="stylesheet" href="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.2.1/af-2.7.0/b-3.2.1/b-colvis-3.2.1/b-html5-3.2.1/b-print-3.2.1/cr-2.0.4/date-1.5.5/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.1/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-3.0.0/sr-1.4.1/datatables.min.css"/>
    <script src="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.2.1/af-2.7.0/b-3.2.1/b-colvis-3.2.1/b-html5-3.2.1/b-print-3.2.1/cr-2.0.4/date-1.5.5/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.1/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-3.0.0/sr-1.4.1/datatables.min.js"></script>
    <!-- DataTables Standard-Theme (NICHT zusammen mit dem BS5-Bundle nutzen, nur bei Bedarf statt dessen):
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.1/css/dataTables.dataTables.min.css">
    -->

    <!-- ================= SELECT2 4.1.0 + BOOTSTRAP-5-THEME ================= -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- ================= BOOTSTRAP-DATEPICKER 1.10.0 (tenderCalender / ElementLots) ================= -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-datepicker@1.10.0/dist/css/bootstrap-datepicker.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-datepicker@1.10.0/dist/js/bootstrap-datepicker.min.js"></script>

    <!-- ================= CHART.JS 2.5.0 ================= -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>

    <!-- ================= VIEWER.JS 1.11.6 (Bildbetrachter) ================= -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/viewerjs/1.11.6/viewer.min.css"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/viewerjs/1.11.6/viewer.min.js"></script>

    <!-- ================= PROJEKT: GLOBALES CSS (nach den Libraries) ================= -->
    <link rel="stylesheet" href="<?= h($WEB_ROOT) ?>../css/style.css" type="text/css" media="screen"/>

    <!-- ================= PROJEKT: UTILS JS (muss zuletzt!) ================= -->
    <script src="<?= h($WEB_ROOT) ?>utils/_utils.js"></script>
</head>

<body>
<!-- ================= NAVBAR ================= -->
<div id="limet-navbar"></div>

<!-- ================= INHALT ================= -->
<div class="container-fluid">
    <div class="row g-2">

        <!-- ===== Hauptbereich (Tabelle) ===== -->
        <div class="col-12 col-xl-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center" id="MainCardHeader">
                    <span class="fw-bold">Titel</span>
                    <!-- DataTables-Controls landen hier -->
                </div>
                <div class="card-body">
                    <table id="MainTable"
                           class="table table-sm table-striped table-hover border border-light border-5">
                        <thead>
                        <tr>
                            <th>Spalte 1</th>
                            <th>Spalte 2</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ===== Seitenbereich 1 ===== -->
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card">
                <div class="card-header" id="SideCardHeader1">
                    <span class="fw-bold">Titel</span>
                </div>
                <div class="card-body">

                </div>
            </div>
        </div>

        <!-- ===== Seitenbereich 2 ===== -->
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card">
                <div class="card-header" id="SideCardHeader2">
                    <span class="fw-bold">Titel</span>
                </div>
                <div class="card-body">

                </div>
            </div>
        </div>

    </div>
</div>

<!-- ================= SEITEN-SCRIPT (immer innerhalb von <body>) ================= -->
<script charset="utf-8">
    // ----- Werte aus PHP -----
    const WEB_ROOT      = <?= json_encode($WEB_ROOT) ?>;
    const excelfilename = <?= json_encode($excelfilename) ?>;
    const csrfToken     = $('meta[name="csrf"]').attr('content');

    $(function () {

        // ----- DataTable -----
        $('#MainTable').DataTable({
            language: {url: WEB_ROOT + 'utils/de-DE.json'},
            pageLength: 25,
            layout: {
                topStart: 'buttons',
                topEnd: 'search',
                bottomStart: 'info',
                bottomEnd: 'paging'
            },
            buttons: [
                {
                    extend: 'excel',
                    filename: excelfilename,
                    className: 'btn btn-sm btn-outline-dark'
                }
            ],
            initComplete: function () {
                relocateControls(this.api(), '#MainCardHeader');
            }
        });

        // ----- Select2 -----
        $('.select2').select2({theme: 'bootstrap-5', width: '100%'});

        // ----- Datepicker -----
        $('.datepicker').datepicker({
            format: 'dd.mm.yyyy',
            autoclose: true,
            todayHighlight: true
        });

        // ----- Viewer.js (Bilder in einem Container) -----
        // const viewer = new Viewer(document.getElementById('ImageContainer'));

        // ----- Chart.js 2.x -----
        // new Chart(document.getElementById('MyChart'), {type: 'bar', data: {...}, options: {...}});

        // ----- Beispiel POST-Request -----
        // $.post(WEB_ROOT + 'utils/endpunkt.php', {id: 1, csrf: csrfToken}, function (resp) {
        //     console.log(resp);
        // });
    });
</script>
</body>
</html>