<?php
global $mysqli;
require_once "../Nutzerlogin/_utils.php";

if (!function_exists('loadEnv')) {
    include "../Nutzerlogin/db.php";
}

$role = init_page(["internal_rb_user", "spargelfeld_admin", "spargelfeld_view"]);
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');

$projektid = 95;

require_once "form_fields_forNutzergruppe1.php"; // $formFields (Basis)
require_once "raumtyp_resolver.php";             // getRaumtypById / applyRaumtypOverrides
require_once "../Nutzerumfrage/raumtypen.php";   // lädt $labortypen

$baseFormFields = $formFields;

// Felder, die von Haus aus versteckt sind (Meta-Felder) -> sollen NICHT zu "–" werden
$baseHidden = [];
foreach ($baseFormFields as $f) {
    if (($f['type'] ?? '') === 'texthidden' && isset($f['name'])) {
        $baseHidden[$f['name']] = true;
    }
}

$hiddenCache = [];
/**
 * Liefert die Felder, die der Resolver für diesen Raumtyp NICHT erfragt hat.
 * (genau die Felder, die durch applyRaumtypOverrides zu 'texthidden' werden)
 */
function hiddenFieldsForRaumtyp($raumtypId): array
{
    global $hiddenCache, $labortypen, $baseFormFields, $baseHidden;
    $key = (string)$raumtypId;
    if (isset($hiddenCache[$key])) return $hiddenCache[$key];

    $rt = getRaumtypById($labortypen, $key);
    $resolved = applyRaumtypOverrides($baseFormFields, $rt);

    $hidden = [];
    foreach ($resolved as $f) {
        $name = $f['name'] ?? null;
        if (!$name) continue;
        if (($f['type'] ?? '') === 'texthidden' && empty($baseHidden[$name])) {
            $hidden[$name] = true;
        }
    }
    $hiddenCache[$key] = $hidden;
    return $hidden;
}

/*
 * Spalten-Konfiguration der Ergebnis-Tabelle.
 * type: 'text'  -> 1:1 ausgeben
 *       'bool'  -> Ja / Nein / — (bei NULL)
 *       'num'   -> numerisch
 *       'date'  -> Datum/Zeit
 * Zum Anpassen einfach Zeilen auskommentieren / umsortieren / Label ändern.
 */
$columns = [
    ['raumnr', 'Raumnr', 'text'],
    ['roomname', 'Raumtyp', 'text'],
    ['rb', 'Raumbereich', 'text'],
    ['Bauabschnitt', 'Trakt', 'text'],
    ['Geschoss', 'Ebene', 'text'],
    ['nf', 'NF (m²)', 'num'],
    ['raumkategorieAbfrage', 'Raumkategorie', 'text'],
    ['doppelfluegeltuer', 'Doppelflügeltür', 'bool'],
    ['vibrationsempfindlich_bodenstehend', 'Vibrationsempf.', 'bool'],
    ['vibrationsempfindlich_bodenstehend_kommentar', 'Vibration – Kommentar', 'text'],
    ['explosionsschutz', 'Ex-Schutz', 'bool'],
    ['abluftwaescher', 'Abluftwäscher', 'bool'],
    ['abluftwaescher_kommentar', 'Abluftwäscher – Komm.', 'text'],
    ['spezialgas', 'Spezialgas', 'text'],
    ['spezialgas_kommentar', 'Spezialgas – Komm.', 'text'],
    ['raumabluft_besonders', 'Raumabluft bes.', 'text'],
    ['raumzuluft_besonders', 'Raumzuluft bes.', 'text'],
    ['nutzwasser', 'Nutzwasser', 'bool'],
    ['nutzwasser_kommentar', 'Nutzwasser – Komm.', 'text'],
    ['spezialabwasser', 'Spezialabwasser', 'text'],
    ['spezialabwasser_kommentar', 'Spezialabw. – Komm.', 'text'],
    ['DL', 'Druckluft', 'bool'],
    ['N2', 'N2', 'bool'],
    ['Vakuum', 'Vakuum', 'bool'],
    ['kuehlwasser', 'Kühlwasser', 'bool'],
    ['kuehlwasser_kommentar', 'Kühlwasser – Komm.', 'text'],
    ['raumtemp', 'Raumtemp. bes.', 'bool'],
    ['raumtemp_kommentar', 'Raumtemp. – Komm.', 'text'],
    ['luftf', 'Luftfeuchte bes.', 'bool'],
    ['luftf_kommentar', 'Luftfeuchte – Komm.', 'text'],
    ['allgemeiner_kommentar', 'Allg. Kommentar', 'text'],
    // ['username',                                    'Bearbeitet von',         'text'],
    //['created_at',                                  'Bearbeitet am',          'date'],
];

$sql = "SELECT t.*,
               r.Geschoss,
               r.Bauabschnitt,
               r.`Raumtyp BH` AS raumtyp_id,
               r.`Raumbereich Nutzer` AS rb
        FROM tabelle_room_requirements_from_user t
        JOIN tabelle_räume r ON t.roomID = r.idTABELLE_Räume
        WHERE r.tabelle_projekte_idTABELLE_Projekte = ?
            AND  r.`Raumtyp BH` <> 34
            AND  r.`Raumtyp BH` <> 35
        ORDER BY t.created_at DESC";

$rows = [];
if ($stmt = $mysqli->prepare($sql)) {
    $stmt->bind_param("i", $projektid);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    $stmt->close();
}

/* Helper: Ausgabe je nach Spaltentyp */
function renderCell($val, $type)
{
    switch ($type) {
        case 'bool':
            if ($val === null || $val === '') return '<span class="text-muted">—</span>';
            return ((int)$val === 1) ? 'Ja' : 'Nein';
        case 'num':
            return ($val === null || $val === '') ? '' : htmlspecialchars($val);
        case 'date':
            return htmlspecialchars((string)$val);
        case 'text':
        default:
            return htmlspecialchars((string)$val);
    }
}

?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Umfrage-Ergebnisse</title>

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
<div id="limet-navbar"></div>
<?php require_once "../Nutzerumfrage/_utils.php"; ?>

<div class="container-fluid mt-3">
    <div class="card">
        <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div class="d-flex align-items-center gap-2">
                <strong><i class="fa fa-table"></i> Umfrage-Ergebnisse</strong>
                <span class="badge bg-secondary"><?= count($rows) ?> Räume</span>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2" id="dtToolbar">
                <span class="toolbar-buttons"></span>
                <span class="toolbar-search"></span>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-sm table-striped table-hover" id="ergebnisseTable" style="width:100%">
                <thead class="table-light">
                <tr>
                    <?php foreach ($columns as $col): ?>
                        <th><?= htmlspecialchars($col[1]) ?></th>
                    <?php endforeach; ?>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($rows as $row):
                    $hidden = hiddenFieldsForRaumtyp($row['raumtyp_id'] ?? '');
                    ?>
                    <tr>
                        <?php foreach ($columns as $col):
                            $field = $col[0];
                            $type = $col[2];
                            ?>
                            <?php if (isset($hidden[$field])): ?>
                            <td><span class="text-muted" title="nicht erfragt">–</span></td>
                        <?php else: ?>
                            <td><?= renderCell($row[$field] ?? null, $type) ?></td>
                        <?php endif; ?>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
                </tbody>
                <tfoot class="table-light">
                <tr>
                    <?php foreach ($columns as $col): ?>
                        <th></th>
                    <?php endforeach; ?>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
</body>

<script>
    const colTypes = <?= json_encode(array_map(fn($c) => $c[2], $columns)) ?>;

    document.addEventListener("DOMContentLoaded", function () {
        $('#ergebnisseTable').DataTable({
            language: {url: '//cdn.datatables.net/plug-ins/2.2.1/i18n/de-DE.json'},
            order: [],            // behält die SQL-Reihenfolge (created_at DESC)
            scrollX: true,
            pageLength: 17,
            layout: {
                topStart: 'buttons',
                topEnd: 'search',
                bottomStart: 'info',
                bottomEnd: ['pageLength', 'paging']
            },
            buttons: [
                {
                    extend: 'colvis',
                    text: '<i class="fas fa-columns"></i> Spaltensichtbarkeit',
                    titleAttr: 'Spalten ein-/ausblenden',
                    className: 'btn btn-sm btn-outline-dark bg-white'
                },
                {
                    extend: 'excelHtml5',
                    text: '<i class="fas fa-file-excel"></i> Excel',
                    className: 'btn btn-success btn-sm',
                    title: "Export"
                }
            ],

            // Summenzeile: bool -> Anzahl "Ja", num -> Summe
            footerCallback: function (row, data, start, end, display) {
                const api = this.api();
                const txt = html => $('<div>').html(html).text().trim();

                colTypes.forEach(function (type, i) {
                    let out = '';
                    if (type === 'bool') {
                        let ja = 0;
                        api.column(i, {search: 'applied'}).data().each(function (d) {
                            if (txt(d) === 'Ja') ja++;
                        });
                        out = 'Ja: ' + ja;
                    } else if (type === 'num') {
                        let sum = 0;
                        api.column(i, {search: 'applied'}).data().each(function (d) {
                            const v = parseFloat(txt(d).replace(',', '.'));
                            if (!isNaN(v)) sum += v;
                        });
                        out = sum ? ('Σ ' + sum.toLocaleString('de-DE', {maximumFractionDigits: 1})) : '';
                    }
                    $(api.column(i).footer()).html(out);
                });

                // erste Spalte als Label
                $(api.column(0).footer()).html('<strong>Summe</strong>');
            },

            // Buttons + Suche in den Card-Header verschieben, Such-Label -> Placeholder
            initComplete: function () {
                const api = this.api();
                const wrap = $(api.table().container());
                const $buttons = wrap.find('.dt-buttons');
                const $search = wrap.find('.dt-search');
                const $topRow = $buttons.closest('.dt-layout-row');

                $search.find('label').remove();
                $search.find('input')
                    .attr('placeholder', 'Suche')
                    .addClass('form-control form-control-sm');

                $('#dtToolbar .toolbar-buttons').append($buttons);
                $('#dtToolbar .toolbar-search').append($search);
                $topRow.remove(); // leere obere Steuerzeile entfernen
            }
        });
    });
</script>
</html>