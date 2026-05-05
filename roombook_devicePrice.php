<?php
require_once 'utils/_utils.php';
include 'utils/_format.php';
init_page_serversides("No Redirect");

$mysqli = utils_connect_sql();

// ── Gerätepreise ──────────────────────────────────────────────────────────────
$sql_gp = "
    SELECT
        tp.idTABELLE_Preise,
        tp.Datum,
        tp.Quelle,
        tp.Menge,
        tp.Preis,
        tp.Nebenkosten,
        tp.Kommentar,
        tg.idTABELLE_Geraete,
        tg.GeraeteID,
        tg.Typ,
        tg.Kurzbeschreibung     AS Geraete_Kurzbeschreibung,
        th.Hersteller,
        tpr.idTABELLE_Projekte  AS projectID,
        tpr.Interne_Nr,
        tpr.Projektname,
        tl.idTABELLE_Lieferant  AS lieferantID,
        tl.Lieferant
    FROM tabelle_preise tp
        LEFT JOIN tabelle_geraete tg
            ON tp.TABELLE_Geraete_idTABELLE_Geraete = tg.idTABELLE_Geraete
        LEFT JOIN tabelle_hersteller th
            ON tg.tabelle_hersteller_idtabelle_hersteller = th.idtabelle_hersteller
        LEFT JOIN tabelle_projekte tpr
            ON tp.TABELLE_Projekte_idTABELLE_Projekte = tpr.idTABELLE_Projekte
        LEFT JOIN tabelle_lieferant tl
            ON tp.tabelle_lieferant_idTABELLE_Lieferant = tl.idTABELLE_Lieferant
    ORDER BY tp.Datum DESC
";
$stmt = $mysqli->prepare($sql_gp);
$stmt->execute();
$rows_gp = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// ── Wartungspreise ────────────────────────────────────────────────────────────
$sql_wp = "
    SELECT
        w.idtabelle_wartungspreise,
        w.WartungspreisProJahr,
        w.Menge,
        w.Wartungsart,
        w.Info,
        w.Datum,
        tg.idTABELLE_Geraete,
        tg.GeraeteID,
        tg.Typ,
        tg.Kurzbeschreibung                         AS Geraete_Kurzbeschreibung,
        th.Hersteller,
        tl.Lieferant,
        w.WartungspreisProJahr * w.Menge            AS Preis_Jahr_Menge
    FROM tabelle_wartungspreise w
        INNER JOIN tabelle_geraete tg
            ON w.tabelle_geraete_idTABELLE_Geraete = tg.idTABELLE_Geraete
        LEFT JOIN tabelle_hersteller th
            ON tg.tabelle_hersteller_idtabelle_hersteller = th.idtabelle_hersteller
        LEFT JOIN tabelle_lieferant tl
            ON w.tabelle_lieferant_idTABELLE_Lieferant = tl.idTABELLE_Lieferant
        LEFT JOIN tabelle_räume_has_tabelle_elemente rhe
            ON rhe.TABELLE_Geraete_idTABELLE_Geraete = tg.idTABELLE_Geraete
        LEFT JOIN tabelle_räume tr
            ON rhe.TABELLE_Räume_idTABELLE_Räume = tr.idTABELLE_Räume
        LEFT JOIN tabelle_elemente te
            ON rhe.TABELLE_Elemente_idTABELLE_Elemente = te.idTABELLE_Elemente
    GROUP BY
        w.idtabelle_wartungspreise,
        te.idTABELLE_Elemente
    ORDER BY w.Datum
";
$stmt = $mysqli->prepare($sql_wp);
$stmt->execute();
$rows_wp = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$mysqli->close();
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>Preise</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="icon" href="Logo/iphone_favicon.png"/>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"/>

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css"
          integrity="sha512-q3eWabyZPc1XTCmF+8/LuE1ozpg5xxn7iO89yfSOd5/oKvyqLngoNGsx8jq92Y8eXJ/IRxQbEC+FGSYxtk2oiw=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>

    <link href="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.2.1/af-2.7.0/b-3.2.1/b-colvis-3.2.1/b-html5-3.2.1/b-print-3.2.1/cr-2.0.4/date-1.5.5/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.1/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-3.0.0/sr-1.4.1/datatables.min.css"
          rel="stylesheet">
    <script src="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.2.1/af-2.7.0/b-3.2.1/b-colvis-3.2.1/b-html5-3.2.1/b-print-3.2.1/cr-2.0.4/date-1.5.5/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.1/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-3.0.0/sr-1.4.1/datatables.min.js"></script>

    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen"/>
</head>
<body>

<div class="container-fluid">
    <div id="limet-navbar"></div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">

            <!-- Bootstrap Nav Tabs inside card-header -->
            <ul class="nav nav-tabs card-header-tabs mb-0" id="preisTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="tab-geraete-btn"
                            data-bs-toggle="tab" data-bs-target="#tab-geraete"
                            type="button" role="tab">
                        <i class="fas fa-tag me-1"></i>Gerätepreise
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-wartung-btn"
                            data-bs-toggle="tab" data-bs-target="#tab-wartung"
                            type="button" role="tab">
                        <i class="fas fa-wrench me-1"></i>Wartungspreise
                    </button>
                </li>
            </ul>

            <!-- Dynamic toolbar area – filled by each tab's DataTable initComplete -->
            <div id="CardHeader" class="ms-auto d-flex justify-content-end align-items-center"></div>
        </div>

        <div class="card-body p-0">
            <div class="tab-content">

                <!-- ═══════════════════════════════════════════════
                     TAB 1 – Gerätepreise
                ═══════════════════════════════════════════════ -->
                <div class="tab-pane fade show active p-2" id="tab-geraete" role="tabpanel">
                    <div class="table-responsive">
                        <table id="tblGeraetepreise"
                               class="table table-sm table-striped table-hover table-bordered p-0">
                            <thead class="table-dark">
                            <tr>
                                <th>Geräte-ID</th>           <!-- 0  -->
                                <th>Typ</th>                  <!-- 1  -->
                                <th>Hersteller</th>           <!-- 2  -->
                                <th>Gerät Beschreibung</th>   <!-- 3 hidden -->
                                <th>Datum</th>                <!-- 4  -->
                                <th>Verfahren</th>            <!-- 5  -->
                                <th class="text-end">Menge</th>         <!-- 6  -->
                                <th class="text-end">EP</th>            <!-- 7  -->
                                <th class="text-end">NK/Stk</th>        <!-- 8  -->
                                <th>Projekt</th>              <!-- 9  -->
                                <th>Lieferant</th>            <!-- 10 -->
                                <th>Kommentar</th>            <!-- 11 -->
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($rows_gp as $row):
                                $datum        = $row['Datum'] ? date('d.m.Y', strtotime($row['Datum'])) : '–';
                                $datum_order  = $row['Datum'] ? strtotime($row['Datum']) : 0;
                                $ep           = (float)($row['Preis']       ?? 0);
                                $nk           = (float)($row['Nebenkosten'] ?? 0);
                                $menge        = (int)($row['Menge']         ?? 0);
                                $projekt      = trim(($row['Interne_Nr'] ?? '') . ' ' . ($row['Projektname'] ?? ''));
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['GeraeteID']               ?? '–', ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($row['Typ']                      ?? '–', ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($row['Hersteller']               ?? '–', ENT_QUOTES, 'UTF-8') ?></td>
                                    <td class="text-muted small"><?= htmlspecialchars($row['Geraete_Kurzbeschreibung'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                                    <td data-order="<?= $datum_order ?>"><?= $datum ?></td>
                                    <td><?= htmlspecialchars($row['Quelle']                   ?? '–', ENT_QUOTES, 'UTF-8') ?></td>
                                    <td class="text-end" data-order="<?= $menge ?>"><?= number_format($menge, 0, ',', '.') ?></td>
                                    <td class="text-end" data-order="<?= $ep ?>"><?= number_format($ep,    2, ',', '.') ?></td>
                                    <td class="text-end" data-order="<?= $nk ?>"><?= number_format($nk,    2, ',', '.') ?></td>
                                    <td><?= htmlspecialchars($projekt ?: '–',                       ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($row['Lieferant']                ?? '–', ENT_QUOTES, 'UTF-8') ?></td>
                                    <td class="text-muted small"><?= htmlspecialchars($row['Kommentar'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div><!-- /tab-geraete -->

                <!-- ═══════════════════════════════════════════════
                     TAB 2 – Wartungspreise
                ═══════════════════════════════════════════════ -->
                <div class="tab-pane fade p-2" id="tab-wartung" role="tabpanel">
                    <div class="table-responsive">
                        <table id="tblWartung"
                               class="table table-sm table-striped table-hover table-bordered p-0">
                            <thead class="table-dark">
                            <tr>
                                <th>Geräte-ID</th>                          <!-- 0  -->
                                <th>Typ</th>                                <!-- 1  -->
                                <th>Hersteller</th>                         <!-- 2  -->
                                <th>Gerät Beschreibung</th>                 <!-- 3 hidden -->

                                <th>Lieferant</th>                          <!-- 5  -->
                                <th>Verfahren Info</th>                     <!-- 6  -->
                                <th>Datum</th>                              <!-- 7  -->
                                <th>Wartungsart</th>                        <!-- 4  -->
                                <th class="text-end">Geräte Anzahl</th>     <!-- 8  -->
                                <th class="text-end">Preis / Jahr (1 Stk)</th> <!-- 9  -->
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($rows_wp as $row):
                                $menge            = (int)($row['Menge']                ?? 0);
                                $preis_jahr       = (float)($row['WartungspreisProJahr'] ?? 0);
                                $datum            = $row['Datum'] ? date('d.m.Y', strtotime($row['Datum'])) : '–';
                                $datum_order      = $row['Datum'] ? strtotime($row['Datum']) : 0;

                                $artRaw   = $row['Wartungsart'] ?? '';
                                $art      = htmlspecialchars(
                                    $artRaw === '0' ? 'Betriebswartung' : 'Vollwartung',
                                    ENT_QUOTES, 'UTF-8'
                                );
                                $artLower = strtolower($artRaw);

                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['GeraeteID']               ?? '–', ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($row['Typ']                      ?? '–', ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($row['Hersteller']               ?? '–', ENT_QUOTES, 'UTF-8') ?></td>
                                    <td class="text-muted small"><?= htmlspecialchars($row['Geraete_Kurzbeschreibung'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>

                                    <td><?= htmlspecialchars($row['Lieferant']                ?? '–', ENT_QUOTES, 'UTF-8') ?></td>
                                    <td class="text-muted small"><?= htmlspecialchars($row['Info']   ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                                    <td data-order="<?= $datum_order ?>"><?= $datum ?></td>
                                    <td><span class=""><?= $art ?></span></td>
                                    <td class="text-end" data-order="<?= $menge ?>"><?= number_format($menge,      0, ',', '.') ?></td>
                                    <td class="text-end" data-order="<?= $preis_jahr ?>"><?= number_format($preis_jahr, 2, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div><!-- /tab-wartung -->

            </div><!-- /tab-content -->
        </div><!-- /card-body -->
    </div><!-- /card -->
</div><!-- /container-fluid -->

<script>
    $(document).ready(function () {

        // ── helper: move DT toolbar into card-header ──────────────────────────
        function moveToolbar() {
            $('#CardHeader').empty();

            const activePane = $('.tab-pane.active').attr('id');

            if (activePane === 'tab-geraete') {
                const wrapper = $('#tblGeraetepreise_wrapper');
                wrapper.find('.dt-search input').addClass('btn btn-sm btn-outline-dark');
                wrapper.find('.dt-search label').remove();
                wrapper.find('.dt-search').children()
                    .removeClass('form-control form-control-sm')
                    .addClass('d-flex align-items-center')
                    .appendTo('#CardHeader');
                wrapper.find('.dt-buttons')
                    .addClass('btn-group btn-group-sm ms-1 me-1')
                    .appendTo('#CardHeader');
            } else {
                const wrapper = $('#tblWartung_wrapper');
                wrapper.find('.dt-search input').addClass('btn btn-sm btn-outline-dark');
                wrapper.find('.dt-search label').remove();
                wrapper.find('.dt-search').children()
                    .removeClass('form-control form-control-sm')
                    .addClass('d-flex align-items-center')
                    .appendTo('#CardHeader');
                wrapper.find('.dt-buttons')
                    .addClass('btn-group btn-group-sm ms-1 me-1')
                    .appendTo('#CardHeader');
            }
        }

        // ── Gerätepreise DataTable ────────────────────────────────────────────
        $('#tblGeraetepreise').DataTable({
            buttons: [
                {extend: 'excel',  className: 'btn btn-sm btn-outline-dark bg-white', title: 'Gerätepreise',  exportOptions: {columns: ':visible'}},
                {extend: 'print',  className: 'btn btn-sm btn-outline-dark bg-white', title: 'Gerätepreise',  exportOptions: {columns: ':visible'}},
                {extend: 'colvis', className: 'btn btn-sm btn-outline-dark bg-white'}
            ],
            layout: {
                topStart:    'buttons',
                topEnd:      'search',
                bottomStart: 'info',
                bottomEnd:   ['pageLength', 'paging']
            },
            language:   {url: 'https://cdn.datatables.net/plug-ins/2.2.1/i18n/de-DE.json'},
            pageLength:  25,
            lengthMenu: [25, 50, 100, 500, -1],
            order:       [[4, 'desc']],
            columnDefs: [
                {targets: [3],       visible: false},
                {targets: [6, 7, 8], className: 'text-end'}
            ],
            initComplete: function () { moveToolbar(); }
        });

        // ── Wartungspreise DataTable ──────────────────────────────────────────
        $('#tblWartung').DataTable({
            buttons: [
                {extend: 'excel',  className: 'btn btn-sm btn-outline-dark bg-white', title: 'Wartungspreise',       exportOptions: {columns: ':visible'}},
                {extend: 'print',  className: 'btn btn-sm btn-outline-dark bg-white', title: 'Wartungspreise je Gerät', exportOptions: {columns: ':visible'}},
                {extend: 'colvis', className: 'btn btn-sm btn-outline-dark bg-white'}
            ],
            layout: {
                topStart:    'buttons',
                topEnd:      'search',
                bottomStart: 'info',
                bottomEnd:   ['pageLength', 'paging']
            },
            language:   {url: 'https://cdn.datatables.net/plug-ins/2.2.1/i18n/de-DE.json'},
            pageLength:  25,
            lengthMenu: [25, 50, 100, 500, -1],
            order:       [[0, 'asc']],
            columnDefs: [
                {targets: [3],    visible: false},
                {targets: [8, 9], className: 'text-end'}
            ],
            initComplete: function () { /* toolbar moved on tab-show */ }
        });

        // ── Swap toolbar when switching tabs ──────────────────────────────────
        $('#preisTabs button[data-bs-toggle="tab"]').on('shown.bs.tab', function () {
            moveToolbar();

            // DataTables hidden in inactive tabs need a redraw on first show
            const target = $(this).data('bs-target');
            if (target === '#tab-wartung') {
                $('#tblWartung').DataTable().columns.adjust().draw(false);
            } else {
                $('#tblGeraetepreise').DataTable().columns.adjust().draw(false);
            }
        });
    });
</script>
</body>
</html>