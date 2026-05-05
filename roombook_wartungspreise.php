<?php
require_once 'utils/_utils.php';
init_page_serversides("No Redirect");

$mysqli = utils_connect_sql();
$sql = "
    SELECT
        w.idtabelle_wartungspreise,
        w.WartungspreisProJahr,
        w.Menge,
        w.Wartungsart,
        w.Info,
        w.Datum,
        -- Geräte
        tg.idTABELLE_Geraete,
        tg.GeraeteID,
        tg.Typ,
        tg.Kurzbeschreibung                         AS Geraete_Kurzbeschreibung,
        -- Hersteller 
        th.Hersteller,

        tl.Lieferant,
        -- Berechnung
        w.WartungspreisProJahr * w.Menge            AS Preis_Jahr_Menge
    FROM tabelle_wartungspreise w
        INNER JOIN tabelle_geraete tg
            ON w.tabelle_geraete_idTABELLE_Geraete = tg.idTABELLE_Geraete
        LEFT JOIN tabelle_hersteller th
            ON tg.tabelle_hersteller_idtabelle_hersteller = th.idtabelle_hersteller
        LEFT JOIN tabelle_lieferant tl
            ON w.tabelle_lieferant_idTABELLE_Lieferant = tl.idTABELLE_Lieferant
        -- Element: Gerät -> Raumverknüpfung -> Element
        LEFT JOIN tabelle_räume_has_tabelle_elemente rhe
            ON rhe.TABELLE_Geraete_idTABELLE_Geraete = tg.idTABELLE_Geraete
        LEFT JOIN tabelle_räume tr
            ON rhe.TABELLE_Räume_idTABELLE_Räume = tr.idTABELLE_Räume
        LEFT JOIN tabelle_elemente te
            ON rhe.TABELLE_Elemente_idTABELLE_Elemente = te.idTABELLE_Elemente
 
    GROUP BY
        w.idtabelle_wartungspreise,
        te.idTABELLE_Elemente
    ORDER BY w.Datum;
";

$stmt = $mysqli->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
$rows = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$mysqli->close();


?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>Wartungspreise je Gerät</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="icon" href="Logo/iphone_favicon.png"/>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css"
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
    <div id="" class="card-header d-flex justify-content-between">
        Wartungspreise
        <div id="CardHeader" class="ms-auto d-flex justify-content-end">
        </div>
    </div>
    <div class="card-body p-0">
        <!-- ── Tabelle ── -->
        <div class="table-wrapper p-0">
            <table id="tblWartung" class="table table-sm table-striped table-hover table-bordered p-0 ">
                <thead class="table-dark">
                <tr>
                    <th>Geräte-ID</th>              <!-- 0  -->
                    <th>Typ</th>                    <!-- 1  -->
                    <th>Hersteller</th>             <!-- 2  -->
                    <th>Gerät Beschreibung</th>     <!-- 3  (hidden) -->
                    <th>Wartungsart</th>            <!-- 9  -->
                    <th>Lieferant</th>              <!-- 10 (hidden) -->
                    <th>Verfahren Info</th>                   <!-- 11  -->
                    <th>Datum</th>                  <!-- 12 -->
                    <th class="money">Geräte Anzahl</th>         <!-- 13 -->
                    <th class="money">Preis / Jahr (für 1 Gerät)</th>  <!-- 14 -->
                    <!--th class="money">Preis / Jahr / Menge</th> < 15 -->
                </tr>
                </thead>
                <tbody>
                <?php foreach ($rows as $row):
                    $menge = (int)($row['Menge'] ?? 0);
                    $preis_jahr = (float)($row['WartungspreisProJahr'] ?? 0);
                    $preis_jahr_menge = (float)($row['Preis_Jahr_Menge'] ?? 0);

                    $datum = $row['Datum'] ? date('d.m.Y', strtotime($row['Datum'])) : '–';
                    $datum_order = $row['Datum'] ? strtotime($row['Datum']) : 0;

                    $art = htmlspecialchars($row["Wartungsart"] === "0" ? "Betriebswartung" : "Vollwartung", ENT_QUOTES, 'UTF-8');


                    $artLower = strtolower($row['Wartungsart'] ?? '');
                    $chipCls = match (true) {
                        str_contains($artLower, 'voll') => 'chip green',
                        str_contains($artLower, 'teil') => 'chip orange',
                        default => 'chip'
                    };
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($row['GeraeteID'] ?? '–', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['Typ'] ?? '–', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['Hersteller'] ?? '–', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="text-muted small"><?= htmlspecialchars($row['Geraete_Kurzbeschreibung'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <?= $art ? "<span class=\"$chipCls\">$art</span>" : '<span class="text-no-data">–</span>' ?>
                        </td>
                        <td><?= htmlspecialchars($row['Lieferant'] ?? '–', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="text-muted small"><?= htmlspecialchars($row['Info'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td data-order="<?= $datum_order ?>"><?= $datum ?></td>
                        <td class="money" data-order="<?= $menge ?>"><?= number_format($menge, 0, ',', '.') ?></td>
                        <td class="money" data-order="<?= $preis_jahr ?>"><?= number_format($preis_jahr, 2, ',', '.') ?>

                        </td>
                        <!--td class="money"
                            data-order="<?= $preis_jahr_menge ?>"><?= number_format($preis_jahr_menge, 2, ',', '.') ?>
                        </td-->
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>

<script>
    let table;

    $(document).ready(function () {
        table = $('#tblWartung').DataTable({
            buttons: [
                {
                    extend: 'excel',
                    className: 'buttons-excel bg-white btn-outline-dark',
                    title: 'Wartungspreise',
                    exportOptions: {columns: ':visible'}
                },
                {
                    extend: 'print',
                    className: 'buttons-print bg-white btn-outline-dark',
                    title: 'Wartungspreise je Gerät',
                    exportOptions: {columns: ':visible'}
                },
                {
                    extend: 'colvis',
                    className: 'buttons-colvis bg-white btn-outline-dark'
                }
            ],
            layout: {
                topStart: "buttons",
                topEnd: 'search',
                bottomStart: 'info',
                bottomEnd: ['pageLength', 'paging']
            },
            language: {
                url: 'https://cdn.datatables.net/plug-ins/2.2.1/i18n/de-DE.json'
            },

            pageLength: 25,
            lengthMenu: [25, 50, 100, 500, -1],
            order: [[0, 'asc']],
            columnDefs: [
                {targets: [3], visible: false},          // Beschreibungen hidden
                {targets: [8, 9], className: 'money'}  // Zahlen rechtsbündig
            ],
            initComplete: function () {
                $('.dt-search input').addClass("btn btn-sm btn-outline-dark");
                $('.dt-search label').remove();
                $('.dt-search').children().removeClass('form-control form-control-sm').addClass("d-flex align-items-center").appendTo('#CardHeader');
                $('.dt-buttons').addClass('btn-group btn-group-sm ms-1 me-1').appendTo('#CardHeader');
            }
        });

    });
</script>
</body>
</html>