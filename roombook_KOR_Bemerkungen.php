<?php
ob_start();
require_once 'utils/_utils.php';
include "utils/_format.php";
init_page_serversides();

$projectID = (int)($_SESSION["projectID"] ?? 0);
if (!$projectID) {
    die('<div class="alert alert-danger m-3">Kein Projekt ausgewählt.</div>');
}

// ── Daten laden ─────────────────────────────────────────────────────────────
$mysqli = utils_connect_sql();

$sqlElements = "
    SELECT
        e.idTABELLE_Elemente,
        e.ElementID,
        e.Bezeichnung,
        rhe.tabelle_Varianten_idtabelle_Varianten AS VarianteID
    FROM tabelle_elemente e
    INNER JOIN tabelle_räume_has_tabelle_elemente rhe
        ON rhe.TABELLE_Elemente_idTABELLE_Elemente = e.idTABELLE_Elemente
    INNER JOIN tabelle_räume r
        ON r.idTABELLE_Räume = rhe.TABELLE_Räume_idTABELLE_Räume
    WHERE r.tabelle_projekte_idTABELLE_Projekte = ?
    GROUP BY e.idTABELLE_Elemente, e.ElementID, e.Bezeichnung,
             rhe.tabelle_Varianten_idtabelle_Varianten
    HAVING MAX(rhe.Anzahl) > 0
    ORDER BY e.ElementID, VarianteID
";
$stmtE = $mysqli->prepare($sqlElements);
$stmtE->bind_param("i", $projectID);
$stmtE->execute();
$elements = $stmtE->get_result()->fetch_all(MYSQLI_ASSOC);
$stmtE->close();

$anmerkungen = [];
if (!empty($elements)) {
    $elementIDs   = array_unique(array_column($elements, 'idTABELLE_Elemente'));
    $placeholders = implode(',', array_fill(0, count($elementIDs), '?'));
    $types        = str_repeat('i', count($elementIDs)) . 'i';
    $params       = array_merge($elementIDs, [$projectID]);

    $stmtA = $mysqli->prepare("
        SELECT * FROM tabelle_Element_has_Anmerkung
        WHERE ElementID IN ($placeholders) AND ProjektID = ?
    ");
    $stmtA->bind_param($types, ...$params);
    $stmtA->execute();
    $rows = $stmtA->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmtA->close();

    foreach ($rows as $row) {
        $anmerkungen[$row['ElementID'] . '_' . $row['VariantenID']] = $row;
    }
}
$mysqli->close();

function varianteLetter(int $id): string {
    return $id > 0 ? chr(64 + $id) : '';
}

$bemerkungsFelder = [
    'Bemerkung_Allgemein' => ['label' => 'Allgemein',   'icon' => 'fa-comment',   'color' => 'secondary'],
    'Bemerkung_ET'        => ['label' => 'Elektro',     'icon' => 'fa-bolt',      'color' => 'warning'],
    'Bemerkung_MG'        => ['label' => 'Medizingas',  'icon' => 'fa-wind',      'color' => 'info'],
    'Bemerkung_Wasser'    => ['label' => 'Wasser',      'icon' => 'fa-tint',      'color' => 'primary'],
    'Bemerkung_Abwasser'  => ['label' => 'Abwasser',    'icon' => 'fa-water',     'color' => 'dark'],
    'Bemerkung_Lüftung'   => ['label' => 'Lüftung',     'icon' => 'fa-fan',       'color' => 'success'],
    'Bemerkung_GLT'       => ['label' => 'GLT',         'icon' => 'fa-microchip', 'color' => 'danger'],
    'Bemerkung_Kaelte'    => ['label' => 'Kälte',       'icon' => 'fa-snowflake', 'color' => 'info'],
    'Bemerkung_Arch'      => ['label' => 'Architektur', 'icon' => 'fa-building',  'color' => 'secondary'],
];
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>KOR – Element Bemerkungen</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="Logo/iphone_favicon.png">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.2.1/af-2.7.0/b-3.2.1/b-colvis-3.2.1/b-html5-3.2.1/b-print-3.2.1/cr-2.0.4/date-1.5.5/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.1/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-3.0.0/sr-1.4.1/datatables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css">
    <link href="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.2.1/af-2.7.0/b-3.2.1/b-colvis-3.2.1/b-html5-3.2.1/b-print-3.2.1/cr-2.0.4/date-1.5.5/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.1/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-3.0.0/sr-1.4.1/datatables.min.css" rel="stylesheet">
    <style>
        .bem-textarea {
            width: 100%;
            min-height: 55px;
            font-size: 0.78rem;
            resize: vertical;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 4px 6px;
            background: transparent;
            transition: background 0.15s, border-color 0.15s;
        }
        .bem-textarea:focus {
            outline: none;
            border-color: #86b7fe;
            background: #fff;
            box-shadow: 0 0 0 2px rgba(13,110,253,.15);
        }
        .bem-textarea.dirty {
            border-color: #ffc107;
            background: #fffbe6;
        }
        .bem-textarea.saved-ok {
            border-color: #198754;
            background: #d1e7dd;
        }
        #tableKorBem thead th { white-space: nowrap; font-size: 0.78rem; vertical-align: middle; }
        #tableKorBem tbody td { vertical-align: top; padding: 4px; font-size: 0.8rem; min-width: 150px; }
        .elem-code { font-family: monospace; white-space: nowrap; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div id="limet-navbar"></div>

    <div class="mt-2 card">
        <div class="card-header py-2">
            <div class="row align-items-center g-2">
                <div class="col-auto">
                    <b><i class="fas fa-comment-dots me-1"></i>KOR – Element Bemerkungen</b>
                </div>
                <div class="col-auto">
                    <a href="/roombook_KOR_Ausstattungskatalog.php" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Zurück
                    </a>
                </div>
                <div class="col-auto">
                    <button id="saveBemBtn" class="btn btn-sm btn-outline-success" disabled>
                        <i class="far fa-save me-1"></i>Speichern
                    </button>
                </div>
                <div class="col-auto">
                    <span id="saveStatus" class="text-muted small"></span>
                </div>
                <div class="col-auto ms-auto" id="dt-header-controls"></div>
            </div>
        </div>

        <div class="card-body p-1">
            <div style="overflow-x: auto;">
                <table class="table table-striped table-hover table-bordered table-sm" id="tableKorBem">
                    <thead>
                    <tr>
                        <th>Element ID</th>
                        <th>Bezeichnung</th>
                        <?php foreach ($bemerkungsFelder as $fieldKey => $meta): ?>
                            <th class="bem-col" data-field="<?= $fieldKey ?>">
                                <i class="fas <?= $meta['icon'] ?> text-<?= $meta['color'] ?> me-1"></i>
                                <?= htmlspecialchars($meta['label']) ?>
                            </th>
                        <?php endforeach; ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($elements as $elem):
                        $eID    = (int)$elem['idTABELLE_Elemente'];
                        $varID  = (int)($elem['VarianteID'] ?? 0);
                        $mapKey = $eID . '_' . $varID;
                        $anm    = $anmerkungen[$mapKey] ?? [];
                        $code   = htmlspecialchars($elem['ElementID'] . varianteLetter($varID), ENT_QUOTES, 'UTF-8');
                        $bez    = htmlspecialchars($elem['Bezeichnung'], ENT_QUOTES, 'UTF-8');
                        ?>
                        <tr>
                            <td class="elem-code"><?= $code ?></td>
                            <td><?= $bez ?></td>
                            <?php foreach ($bemerkungsFelder as $fieldKey => $meta):
                                $val    = htmlspecialchars($anm[$fieldKey] ?? '', ENT_QUOTES, 'UTF-8');
                                ?>
                                <td class="bem-col" data-field="<?= $fieldKey ?>">
                                    <textarea
                                            class="bem-textarea"
                                            data-element-id="<?= $eID ?>"
                                            data-variante-id="<?= $varID ?>"
                                            data-field="<?= $fieldKey ?>"
                                            data-original="<?= $val ?>"
                                            placeholder="<?= htmlspecialchars($meta['label']) ?>…"
                                    ><?= $val ?></textarea>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="utils/_utils.js"></script>
<script>
    $(document).ready(function () {

        // ── DataTable ────────────────────────────────────────────────────────────
        const dt = new DataTable('#tableKorBem', {
            fixedHeader: true,
            order: [[0, 'asc']],
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'Alle']],
            language: { url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json' },
            columnDefs: [{ targets: '_all', orderable: true }],
            buttons: [{ extend: 'colvis', text: '<i class="fas fa-eye"></i>', className: 'btn btn-sm btn-outline-dark bg-white' }],
            layout: {
                topStart: null, topEnd: null,
                bottomStart: ['pageLength', 'info'],
                bottomEnd: ['paging', 'search', 'buttons']
            },
            initComplete: function () {
                $('#tableKorBem_wrapper .dt-buttons').appendTo('#dt-header-controls');
                $('#tableKorBem_wrapper .dt-search label').remove();
                $('#tableKorBem_wrapper .dt-search').children()
                    .removeClass('form-control form-control-sm')
                    .addClass('btn btn-sm btn-outline-dark ms-1')
                    .appendTo('#dt-header-controls');
            }
        });

        // ── Dirty tracking ───────────────────────────────────────────────────────
        // Key: "eID_varID_field" → textarea DOM element
        const dirty = new Map();

        function updateBtn() {
            const n = dirty.size;
            const $btn = $('#saveBemBtn');
            if (n === 0) {
                $btn.prop('disabled', true)
                    .removeClass('btn-warning btn-success').addClass('btn-outline-success')
                    .html('<i class="far fa-save me-1"></i>Speichern');
                $('#saveStatus').text('');
            } else {
                $btn.prop('disabled', false)
                    .removeClass('btn-outline-success btn-success').addClass('btn-warning')
                    .html('<i class="far fa-save me-1"></i>Speichern (' + n + ')');
            }
        }

        $(document).on('input', '.bem-textarea', function () {
            const ta  = this;
            const key = ta.dataset.elementId + '_' + ta.dataset.varianteId + '_' + ta.dataset.field;
            if (ta.value.trim() !== ta.dataset.original.trim()) {
                dirty.set(key, ta);
                ta.classList.add('dirty');
                ta.classList.remove('saved-ok');
            } else {
                dirty.delete(key);
                ta.classList.remove('dirty');
            }
            updateBtn();
        });

        // ── Speichern ────────────────────────────────────────────────────────────
        $('#saveBemBtn').on('click', function () {
            if (dirty.size === 0) return;

            const $btn = $(this);
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Speichert...');
            $('#saveStatus').text('');

            // Snapshot: alle dirty entries aus DOM lesen
            const entries = [];
            dirty.forEach(function (ta, key) {
                entries.push({
                    elementID:  parseInt(ta.dataset.elementId),
                    varianteID: parseInt(ta.dataset.varianteId),
                    field:      ta.dataset.field,
                    value:      ta.value.trim()
                });
            });

            $.ajax({
                url:      'save_bemerkungen.php',
                method:   'POST',
                dataType: 'json',
                data:     { entries: JSON.stringify(entries) },
                success: function (resp) {
                    if (resp.ok) {
                        // Mark all as saved
                        dirty.forEach(function (ta) {
                            ta.dataset.original = ta.value.trim();
                            ta.classList.remove('dirty');
                            ta.classList.add('saved-ok');
                            setTimeout(() => ta.classList.remove('saved-ok'), 2000);
                        });
                        dirty.clear();
                        updateBtn();
                        $('#saveStatus').text('✓ ' + resp.saved + ' gespeichert').css('color', '#198754');
                    } else {
                        $btn.prop('disabled', false).html('<i class="far fa-save me-1"></i>Speichern (' + dirty.size + ')');
                        $('#saveStatus').text('Fehler: ' + JSON.stringify(resp.errors)).css('color', '#dc3545');
                        console.error('Save errors:', resp);
                    }
                },
                error: function (xhr) {
                    $btn.prop('disabled', false).html('<i class="far fa-save me-1"></i>Speichern (' + dirty.size + ')');
                    $('#saveStatus').text('HTTP Fehler ' + xhr.status + ': ' + xhr.responseText.substring(0, 200)).css('color', '#dc3545');
                    console.error('XHR error:', xhr.status, xhr.responseText);
                }
            });
        });

    });
</script>
</body>
</html>