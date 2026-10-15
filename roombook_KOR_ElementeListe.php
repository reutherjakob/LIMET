<?php
require_once '_utils/_utils.php';
include "_utils/_format_money.php";
init_page_serversides();

require_once '_utils/_csrf.php';


?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>KOR_Elementliste</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf" content="<?= h(csrf_token()) ?>">

    <link rel="stylesheet" href="_utils/style.css" type="text/css" media="screen"/>
    <link rel="icon" href="Logos/iphone_favicon.png">
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

<body style="height:100%">
<?php
function varianteToLetter(?int $id): string
{
    return ($id !== null && $id > 0) ? chr(64 + $id) : '—';
}

$mysqli = utils_connect_sql();

$dummyIds = ["8.20.10.1", "8.20.10.2", "8.20.10.3", "8.20.10.4"];
$dummyIdList = implode("', '", $dummyIds);

// Alle Elemente laden – auch aus nicht freigegebenen Budgets.
// Ob diese angezeigt werden, entscheidet der Switch im Frontend.
$sql = "SELECT
            tabelle_räume.Raumnr,
            tabelle_räume.Raumnummer_Nutzer,
            tabelle_räume.Raumbezeichnung,
            tabelle_räume.`Raumbereich Nutzer`,
            tabelle_räume.Geschoss,
            tabelle_elemente.ElementID,
            tabelle_elemente.Bezeichnung AS ElementBezeichnung,
            tabelle_räume_has_tabelle_elemente.Anzahl,
            CASE 
                WHEN tabelle_elemente.ElementID IN ('{$dummyIdList}') 
                THEN 1
                WHEN TRIM(LOWER(tabelle_räume.Raumbezeichnung)) IN ( 
                    'gerätelager', 
                    'lager rollstühle',
                    'liegenlager rein',
                    'geräteraum',
                    'anästhesie-geräteraum',
                    'anästhesiegeräte rüstraum',
                    'gerätelager, lager gehbehelfe', 
                    'lager/geräte', 'lager geräte'
                ) 
                THEN 0 
                ELSE 1 
            END AS Gleichzeitigkeit,
            tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten,
            tpb.Budgetnummer,
            tpb.Budgetname,
            CASE
                WHEN tpb.idtabelle_projektbudgets IS NULL OR tpb.status = 1 THEN 1
                ELSE 0
            END AS BudgetFreigegeben
        FROM tabelle_räume
            INNER JOIN tabelle_räume_has_tabelle_elemente
                ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume
            INNER JOIN tabelle_elemente
                ON tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente = tabelle_elemente.idTABELLE_Elemente
            LEFT JOIN tabelle_projektbudgets tpb
                ON tpb.idtabelle_projektbudgets = tabelle_räume_has_tabelle_elemente.tabelle_projektbudgets_idtabelle_projektbudgets
        WHERE tabelle_räume.tabelle_projekte_idTABELLE_Projekte = ?
            AND tabelle_räume_has_tabelle_elemente.Anzahl <> 0
        ORDER BY tabelle_räume.Raumnr, tabelle_elemente.ElementID";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param('i', $_SESSION["projectID"]);
$stmt->execute();
$result = $stmt->get_result();

$rows = [];
$anzahlNichtFreigegeben = 0;
while ($row = $result->fetch_assoc()) {
    if ((int)$row["BudgetFreigegeben"] === 0) {
        $anzahlNichtFreigegeben++;
    }
    $rows[] = $row;
}
$mysqli->close();
?>

<div class="container-fluid bg-light">
    <div id="limet-navbar"></div>
    <div class="mt-2 card">
        <div class="card-header">
            <div class="row align-items-center g-2">
                <div class="col-lg-6 d-flex flex-wrap align-items-center gap-3">
                    <b>Elemente im Projekt – Gleichzeitigkeit</b>
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" role="switch" id="switchNichtFreigegeben">
                        <label class="form-check-label" for="switchNichtFreigegeben">
                            Nicht freigegebene Budgets einbeziehen
                            <span class="badge text-bg-danger ms-1"><?= (int)$anzahlNichtFreigegeben ?></span>
                        </label>
                    </div>
                </div>
                <div class="col-lg-6 d-flex flex-wrap justify-content-lg-end align-items-center gap-2"
                     id="dt-header-container"></div>
            </div>
        </div>
        <div class="card-body">
            <div class="alert alert-danger py-2 small d-none" id="hinweisNichtFreigegeben">
                <i class="fas fa-exclamation-triangle me-1"></i>
                Rot hinterlegte Zeilen liegen in noch <b>nicht freigegebenen Budgets</b> und sind nur der
                Vollständigkeit halber enthalten. Grün = freigegeben bzw. keinem Budget zugeordnet.
            </div>

            <table class="table table-striped table-hover compact table-bordered" id="tableGLZ">
                <thead>
                <tr>
                    <th>Raumnummer</th>
                    <th>Raumbezeichnung</th>
                    <th>Raumbereich</th>
                    <th>Geschoss</th>
                    <th>Bauteilelement ID</th>
                    <th>Bauteilelement Bezeichnung</th>
                    <th>Menge</th>
                    <th>Gleichzeitigkeit</th>
                    <th>Budget</th>
                    <th>Budgetstatus</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($rows as $row):
                    $freigegeben = (int)$row["BudgetFreigegeben"] === 1;
                    $budget = $row["Budgetnummer"] !== null
                        ? trim($row["Budgetnummer"] . ' ' . ($row["Budgetname"] ?? ''))
                        : '—';
                    $variante = $row["tabelle_Varianten_idtabelle_Varianten"];
                    ?>
                    <tr class="<?= $freigegeben ? '' : 'table-danger' ?>">
                        <td><?= h($row["Raumnr"]) ?></td>
                        <td><?= h($row["Raumbezeichnung"]) ?></td>
                        <td><?= h($row["Raumbereich Nutzer"]) ?></td>
                        <td><?= h($row["Geschoss"]) ?></td>
                        <td><?= h($row["ElementID"]) . h(varianteToLetter($variante !== null ? (int)$variante : null)) ?></td>
                        <td><?= h($row["ElementBezeichnung"]) ?></td>
                        <td><?= h($row["Anzahl"]) ?></td>
                        <td class="text-center"><?= h($row["Gleichzeitigkeit"] ?? '—') ?></td>
                        <td><?= h($budget) ?></td>
                        <td>
                            <?php if ($freigegeben): ?>
                                <span class="badge text-bg-success">freigegeben</span>
                            <?php else: ?>
                                <span class="badge text-bg-danger">nicht freigegeben</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="_utils/_utils.js"></script>
<script>
    const COL_GLZ = 7;
    const COL_STATUS = 9;
    const STATUS_FREI = 'freigegeben';
    const STATUS_NICHT_FREI = 'nicht freigegeben';
    const PROJEKT_NAME = <?= json_encode($_SESSION["projectName"] ?? '') ?>;

    // Excel-Farben (ARGB)
    const XL_FILL_RED = 'FFFFC7CE';   // hellrot

    // Legt einen eigenen Zellstil in styles.xml an und gibt dessen Index zurück
    function addExcelStyle(xlsx, opts) {
        const NS = 'http://schemas.openxmlformats.org/spreadsheetml/2006/main';
        const doc = xlsx.xl['styles.xml'];
        const el = (name, attrs = {}, children = []) => {
            const e = doc.createElementNS(NS, name);
            Object.entries(attrs).forEach(([k, v]) => e.setAttribute(k, v));
            children.forEach(c => e.appendChild(c));
            return e;
        };
        const fonts = doc.getElementsByTagName('fonts')[0];
        const fills = doc.getElementsByTagName('fills')[0];
        const xfs = doc.getElementsByTagName('cellXfs')[0];

        let fontId = 0;
        if (opts.bold) {
            fonts.appendChild(el('font', {}, [el('b'), el('sz', {val: '11'}), el('name', {val: 'Calibri'})]));
            fontId = fonts.getElementsByTagName('font').length - 1;
            fonts.setAttribute('count', fontId + 1);
        }

        let fillId = 0;
        if (opts.fill) {
            fills.appendChild(el('fill', {}, [
                el('patternFill', {patternType: 'solid'}, [
                    el('fgColor', {rgb: opts.fill}),
                    el('bgColor', {indexed: '64'})
                ])
            ]));
            fillId = fills.getElementsByTagName('fill').length - 1;
            fills.setAttribute('count', fillId + 1);
        }

        xfs.appendChild(el('xf', {
            numFmtId: '0', fontId: fontId, fillId: fillId, borderId: '0',
            applyFont: '1', applyFill: '1'
        }));
        const idx = xfs.getElementsByTagName('xf').length - 1;
        xfs.setAttribute('count', idx + 1);
        return idx;
    }

    function nichtFreigegebeneAnzeigen() {
        return $('#switchNichtFreigegeben').is(':checked');
    }

    // Filter: nicht freigegebene Zeilen nur zeigen, wenn Switch aktiv
    DataTable.ext.search.push(function (settings, data) {
        if (settings.nTable.id !== 'tableGLZ') return true;
        if (nichtFreigegebeneAnzeigen()) return true;
        return data[COL_STATUS].trim() !== STATUS_NICHT_FREI;
    });

    function xmlEsc(s) {
        return String(s ?? '')
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;').replace(/'/g, '&apos;');
    }

    // Fügt ein Blatt "Legende" als ERSTES Tabellenblatt in die Excel-Datei ein
    function addLegendSheet(xlsx, legendRows) {
        const NS_MAIN = 'http://schemas.openxmlformats.org/spreadsheetml/2006/main';
        const NS_R = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships';
        const NS_PKG = 'http://schemas.openxmlformats.org/package/2006/relationships';
        const NS_CT = 'http://schemas.openxmlformats.org/package/2006/content-types';

        let sheetData = '';
        legendRows.forEach(function (cells, i) {
            const r = i + 1;
            sheetData += '<row r="' + r + '">';
            cells.forEach(function (cell, j) {
                if (!cell) return;
                const ref = String.fromCharCode(65 + j) + r;
                sheetData += '<c t="inlineStr" r="' + ref + '" s="' + (cell.s || 0) + '">'
                    + '<is><t xml:space="preserve">' + xmlEsc(cell.t) + '</t></is></c>';
            });
            sheetData += '</row>';
        });

        const sheetXml =
            '<worksheet xmlns="' + NS_MAIN + '" xmlns:r="' + NS_R + '">'
            + '<sheetViews><sheetView workbookViewId="0" tabSelected="1"/></sheetViews>'
            + '<cols><col min="1" max="1" width="30" customWidth="1"/>'
            + '<col min="2" max="2" width="115" customWidth="1"/></cols>'
            + '<sheetData>' + sheetData + '</sheetData></worksheet>';

        xlsx.xl.worksheets['sheet2.xml'] = $.parseXML(sheetXml);

        // Datenblatt nicht mehr als aktiv markieren
        $('sheetView', xlsx.xl.worksheets['sheet1.xml']).removeAttr('tabSelected');

        // Relationship
        const rels = xlsx.xl._rels['workbook.xml.rels'];
        const rel = rels.createElementNS(NS_PKG, 'Relationship');
        rel.setAttribute('Id', 'rIdLegende');
        rel.setAttribute('Type', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet');
        rel.setAttribute('Target', 'worksheets/sheet2.xml');
        rels.documentElement.appendChild(rel);

        // Content-Type
        const ct = xlsx['[Content_Types].xml'];
        const ov = ct.createElementNS(NS_CT, 'Override');
        ov.setAttribute('PartName', '/xl/worksheets/sheet2.xml');
        ov.setAttribute('ContentType', 'application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml');
        ct.documentElement.appendChild(ov);

        // Blatt VOR dem Datenblatt einfügen -> erstes Tabellenblatt
        const wb = xlsx.xl['workbook.xml'];
        const sheets = wb.getElementsByTagName('sheets')[0];
        const sheet = wb.createElementNS(NS_MAIN, 'sheet');
        sheet.setAttribute('name', 'Legende');
        sheet.setAttribute('sheetId', '2');
        sheet.setAttributeNS(NS_R, 'r:id', 'rIdLegende');
        sheets.insertBefore(sheet, sheets.firstChild);
    }

    $(document).ready(function () {
        const table = new DataTable('#tableGLZ', {
            dom: "<'dt-buttons'B><'dt-search'f><'dt-info'i>rt",
            order: [[0, "asc"]],
            language: {
                url: "/_utils/de-DE.json",
                search: ""
            },
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: ' Excel',
                    className: 'fas fa-file-excel btn btn-sm btn-outline-success bg-white',
                    sheetName: 'Elemente',
                    exportOptions: {
                        format: {
                            body: function (data, row, column) {
                                // HTML entfernen (z.B. Badges), nur reinen Text exportieren
                                let text = $('<div>').html(data ?? '').text().trim();

                                if (column === COL_GLZ) {
                                    // Komma durch Punkt ersetzen, damit Excel eine Zahl erkennt
                                    return text.replace(',', '.');
                                }
                                return text;
                            }
                        }
                    },
                    customize: function (xlsx) {
                        // Eigene Styles anlegen
                        const S_RED = addExcelStyle(xlsx, {fill: XL_FILL_RED});
                        const S_BOLD = addExcelStyle(xlsx, {bold: true});

                        const sheet = xlsx.xl.worksheets['sheet1.xml'];
                        const statusCol = String.fromCharCode(65 + COL_STATUS);
                        let gesamt = 0, nichtFrei = 0;

                        // Nur nicht freigegebene Zeilen rot hinterlegen, alle anderen bleiben normal
                        $('row', sheet).each(function () {
                            const status = $(this).find('c[r^="' + statusCol + '"]').text().trim();
                            if (status === STATUS_NICHT_FREI) {
                                nichtFrei++;
                                gesamt++;
                                $(this).find('c').attr('s', S_RED);
                            } else if (status === STATUS_FREI) {
                                gesamt++;
                            }
                        });

                        const exportiert = new Date().toLocaleString('de-AT');
                        const mitNichtFrei = nichtFreigegebeneAnzeigen();

                        const legend = [
                            [{t: 'Legende – Elemente im Projekt (Gleichzeitigkeit)', s: S_BOLD}],
                            [{t: 'Projekt'}, {t: PROJEKT_NAME || '—'}],
                            [{t: 'Exportiert am'}, {t: exportiert}],
                            [],
                            [{t: 'Markierung', s: S_BOLD}, {t: 'Bedeutung', s: S_BOLD}],
                            [{t: 'keine Markierung'},
                                {t: 'Element liegt in einem freigegebenen Budget oder ist keinem Budget zugeordnet.'}],
                            [{t: STATUS_NICHT_FREI, s: S_RED},
                                {t: 'Element liegt in einem noch NICHT freigegebenen Budget. Nur der Vollständigkeit halber enthalten – Freigabe ausständig.'}],
                            [],
                            [{t: 'Hinweis', s: S_BOLD}],
                            [{t: 'Nicht freigegebene enthalten'}, {t: mitNichtFrei ? 'ja' : 'nein (nur freigegebene Budgets exportiert)'}],
                            [{t: 'Zeilen gesamt'}, {t: String(gesamt)}],
                            [{t: 'davon nicht freigegeben'}, {t: String(nichtFrei)}],
                            [],
                            [{t: 'Die Daten befinden sich im Tabellenblatt "Elemente".'}]
                        ];

                        try {
                            addLegendSheet(xlsx, legend);
                        } catch (e) {
                            console.error('Legende konnte nicht erstellt werden:', e);
                        }
                    }
                },
                {
                    extend: 'searchBuilder',
                    className: 'btn btn-sm bg-white btn-outline-dark fas fa-search'
                }
            ],
            paging: false,
            initComplete: function () {
                $('#tableGLZ_wrapper .dt-buttons').appendTo('#dt-header-container');
                $('#tableGLZ_wrapper .dt-search').appendTo('#dt-header-container');
                $('#tableGLZ_wrapper .dt-info').appendTo('#dt-header-container');
            }
        });

        $('#switchNichtFreigegeben').on('change', function () {
            $('#hinweisNichtFreigegeben').toggleClass('d-none', !this.checked);
            table.draw();
        });
    });
</script>
</body>
</html>