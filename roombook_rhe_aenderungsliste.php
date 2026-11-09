<?php
// 26 FX
require_once '_utils/_utils.php';
init_page_serversides();
require_once '_utils/_csrf.php';

$excelfilename = buildExcelFilename('Änderungsliste');
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>RB-Änderungsliste</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="csrf" content="<?= h(csrf_token()) ?>">

    <link rel="stylesheet" href="_utils/style.css" type="text/css" media="screen"/>
    <link rel="icon" href="Logos/favicon.icon"/>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css"
          integrity="sha512-q3eWabyZPc1XTCmF+8/LuE1ozpg5xxn7iO89yfSOd5/oKvyqLngoNGsx8jq92Y8eXJ/IRxQbEC+FGSYxtk2oiw=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.2.1/af-2.7.0/b-3.2.1/b-colvis-3.2.1/b-html5-3.2.1/b-print-3.2.1/cr-2.0.4/date-1.5.5/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.1/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-3.0.0/sr-1.4.1/datatables.min.css"
          rel="stylesheet">
    <script src="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.2.1/af-2.7.0/b-3.2.1/b-colvis-3.2.1/b-html5-3.2.1/b-print-3.2.1/cr-2.0.4/date-1.5.5/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.1/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-3.0.0/sr-1.4.1/datatables.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>
<style>
    .card-header {
        height: 45px;
    }

    .sb-standard {
        background-color: #f8d7da !important;
        border-color: #dc3545 !important;
        color: #842029 !important;
    }
</style>
<body>
<div id="limet-navbar"></div>
<div class="container-fluid py-3">
    <div class="row g-3">

        <!-- ============ Linke Spalte: Einstellungen + Zusammenfassung ============ -->
        <div id="colLeft" class="col-12 col-lg-4 col-xxl-3">
            <div class="card p-0">
                <div class="card-header d-flex align-items-center">
                    <button type="button" class="btn btn-outline-dark" id="hideLeftCardBtn">
                        <i class="fas fa-caret-left"></i>
                    </button>

                    <div class="ms-auto">
                        <button type="button" class="btn btn-sm btn-outline-dark bg-light me-2"
                                onclick="show_modal('InfoModal')">
                            <i class="fas fa-info-circle"> </i>nfo
                        </button>
                        <button type="button" id="btnLoad" class="btn btn-success btn-sm">
                                    <span class="spinner-border spinner-border-sm me-1 d-none"
                                          role="status" aria-hidden="true">
                                    </span>
                            <i class="fas fa-sync-alt me-1"></i>
                            Daten laden
                        </button>
                    </div>
                </div>
                <div class="card-body bg-secondary p-0">
                    <div class="card mb-2 mt-2">


                        <fieldset class="border rounded position-relative px-2 pt-1 pb-1 mt-2">
                            <legend class="float-none px-2 mb-0 small d-flex align-items-center justify-content-between gap-2">
                                <label for="inpStart" class="">Von</label>
                                <input type="date" id="inpStart" class="form-control form-control-sm w-auto">
                                <label for="inpEnd" class="text-nowrap">bis inkl.</label>
                                <input type="date" id="inpEnd" class="form-control form-control-sm w-auto">
                            </legend>
                        </fieldset>


                        <fieldset class="border rounded position-relative  pt-3 pb-2 mb-2 mt-2">
                            <legend class="float-none w-auto px-1 mb-0 small">
                                <span id="apHint" class="form-text text-dark">Wird geladen …</span>
                            </legend>
                            <div class="position-absolute top-0 end-0 translate-middle-y me-2 px-1 bg-white">
                                <button type="button" id="btnApAll" class="btn btn-sm btn-outline-secondary">
                                    Alle
                                </button>
                            </div>
                            <select id="selAP" class="form-select form-select-sm mt-6" multiple disabled></select>
                        </fieldset>


                        <fieldset class="border rounded position-relative  pt-3 pb-2 mb-2 mt-2">
                            <legend class="float-none w-auto px-1 mb-0 small">
                                <label id="budgetHint" for="selBudgetHide" class="form-text mb-0 text-dark">
                                    Elemente dieser Budgets ausblenden</label>
                            </legend>

                            <div class="position-absolute top-0 end-0 translate-middle-y me-2 px-1 bg-white">
                                <button type="button" id="btnBudAev" class="btn btn-sm btn-outline-secondary">
                                    AEV/AEG
                                </button>
                            </div>

                            <select id="selBudgetHide" class="form-select form-select-sm" multiple
                                    disabled></select>
                        </fieldset>


                        <fieldset class="border rounded position-relative px-2 pt-3 pb-2 mb-2 mt-2">
                            <legend class="float-none w-auto px-1 mb-0 small">
                                <span class="form-text text-dark">Budget-Status (Endbudget)</span>
                            </legend>
                            <div id="statusFilter" class=""></div>
                        </fieldset>


                        <div class="form-check form-switch ms-2">
                            <input class="form-check-input" type="checkbox" id="chkStandort" checked>
                            <label class="form-check-label small" for="chkStandort">Nur Standort-Elemente
                                (Standort = 1)</label>
                        </div>

                        <div class="form-check form-switch ms-2">
                            <input class="form-check-input" type="checkbox" id="chkOhneBestand" checked>
                            <label class="form-check-label small" for="chkOhneBestand">Bestand ausblenden (nur
                                Neu)</label>
                        </div>

                        <div id="msgArea" class="mt-3"></div>
                    </div>
                </div>
                <div class="card mb-2">
                    <div class="card-body">

                        <fieldset class="border rounded position-relative  pt-3 pb-2 mb-2 mt-2">
                            <legend class="float-none w-auto px-1 mb-0 small">
                                <span class="form-text text-dark">Änderungsarten Filtern</span>
                            </legend>
                            <div id="artFilter" class="d-flex flex-wrap gap-1 border border-light "></div>
                        </fieldset>


                        <fieldset class="border rounded position-relative  pt-3 pb-2 mb-2 mt-2">
                            <legend class="float-none w-auto px-1 mb-0 small">
                                <span class="form-text text-dark"> Preisänderungen ignorieren bis (± Einheitspreis)</span>
                            </legend>
                            <div class="input-group input-group-sm">
                                <input type="number" id="inpPreisSchwelle" class="form-control" value="0.50"
                                       min="0"
                                       step="0.10">
                                <span class="input-group-text">€</span>
                            </div>
                        </fieldset>


                        <fieldset class="border rounded position-relative  pt-3 pb-2 mb-2 mt-2">
                            <legend class="float-none w-auto px-1 mb-0 small">
                                <span class="form-text text-dark">Einheitspreise anpassen</span>
                            </legend>

                            <div class="position-absolute top-0 end-0 translate-middle-y me-2 px-1 bg-white">
                                <button type="button" id="btnFaktorReset"
                                        class="btn btn-sm btn-outline-secondary" disabled>
                                    Zurücksetzen
                                </button>
                            </div>

                            <div class="row g-2 align-items-center">
                                <label for="rngFaktorStart" class="col-3 small mb-0">Start</label>
                                <div class="col">
                                    <input type="range" class="form-range" id="rngFaktorStart" min="-50"
                                           max="50" step="1"
                                           value="0">
                                </div>
                                <span id="lblFaktorStart"
                                      class="col-2 small fw-semibold text-end text-nowrap">±0 %</span>
                            </div>

                            <div class="row g-2 align-items-center">
                                <label for="rngFaktorEnd" class="col-3 small mb-0">Ende</label>
                                <div class="col">
                                    <input type="range" class="form-range" id="rngFaktorEnd" min="-50" max="50"
                                           step="1"
                                           value="0">
                                </div>
                                <span id="lblFaktorEnd"
                                      class="col-2 small fw-semibold text-end text-nowrap">±0 %</span>
                            </div>
                        </fieldset>


                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="chkExcelErgebnis" checked>
                            <label class="form-check-label small" for="chkExcelErgebnis">Excel: Ergebniszeilen
                                ausgeben
                                (Raum, Arbeitspaket, Gesamt)</label>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <i class="fas fa-balance-scale me-2"></i>
                        <span class="fw-semibold">Zusammenfassung</span>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Σ Positionspreis Start</span><span id="sumStart" class="fw-semibold">–</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Σ Positionspreis Ende</span><span id="sumEnd" class="fw-semibold">–</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between bg-light">
                            <span>Differenz Ende − Start</span><span id="sumDiff" class="fw-bold">–</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Σ Stück Start / Ende</span><span id="sumStk">–</span>
                        </li>
                        <li class="list-group-item">
                            <div class="d-flex flex-wrap gap-1" id="artBadges">
                                <span class="text-muted small">Noch keine Daten geladen.</span>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

        </div>
        <!-- ============ Rechte Spalte: Änderungsliste ============ -->
        <div id="colRight" class="col-12 col-lg-8 col-xxl-9">
            <div class="card">
                <div class="card-header d-flex flex-wrap align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-outline-dark d-none" id="showLeftCardBtn"
                            title="Filter einblenden">
                        <i class="fas fa-caret-right"></i>
                    </button>
                    <i class="fas fa-exchange-alt"></i>
                    <span class="fw-semibold">Änderungsliste</span>
                    <span id="faktorBadge" class="badge text-bg-warning d-none"></span>
                    <span id="periodLabel" class="badge text-bg-light border">kein Zeitraum geladen</span>

                    <div id="CardHeaderAenderungsliste" class="ms-auto d-flex align-items-center gap-2"></div>
                </div>

                <div class="card-body p-0">
                    <div id="emptyState" class="text-center text-muted py-5">
                        <i class="fas fa-calendar-alt fa-2x mb-3"></i>
                        <p class="mb-0">Zeitraum links wählen und <strong>Daten laden</strong> klicken.</p>
                    </div>
                    <div id="tableWrap" class="d-none">
                        <table id="tblAenderungen" class="table table-sm table-striped table-hover align-middle w-100">
                            <thead class="table-light">
                            <tr>
                                <th>AP-Nr.</th>
                                <th>Arbeitspaket</th>
                                <th>Raumnr</th>
                                <th>Raumbezeichnung</th>
                                <th>Element-ID</th>
                                <th>Element</th>
                                <th>Variante</th>
                                <th>Änderungsart</th>
                                <th>Veränderung zum VE (Kommentar)</th>
                                <th>Budget Start</th>
                                <th>Budget Ende</th>
                                <th>Status Ende</th>
                                <th class="text-end">Stk Start</th>
                                <th class="text-end">Stk Ende</th>
                                <th class="text-end">EP Start</th>
                                <th class="text-end">EP Ende</th>
                                <th class="text-end">PP Start</th>
                                <th class="text-end">PP Ende</th>
                                <th class="text-end">Δ PP</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot class="table-light fw-bold">
                            <tr>
                                <th colspan="12" class="text-end">Gesamt (gefiltert)</th>
                                <th class="text-end"></th>
                                <th class="text-end"></th>
                                <th></th>
                                <th></th>
                                <th class="text-end"></th>
                                <th class="text-end"></th>
                                <th class="text-end"></th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include "modal_roombook_aenderungsliste.php" ?>

<script src="_utils/_utils.js"></script>
<script charset="utf-8">
    const API = 'api_aenderungsliste.php';
    const CSRF = $('meta[name="csrf"]').attr('content');
    const EXCELJS_URL = 'https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.4.0/exceljs.min.js';
    const eur = new Intl.NumberFormat('de-DE', {style: 'currency', currency: 'EUR'});
    const COL = {stkStart: 12, stkEnd: 13, epStart: 14, epEnd: 15, ppStart: 16, ppEnd: 17, ppDiff: 18};
    const DEFAULT_START = '2025-12-20';

    // Budgets, deren Nummer so beginnt, werden standardmäßig ausgeblendet (Änderungsevidenzen)
    const BUDGET_HIDE_DEFAULT = /^AE[VG]/i;
    const BUDGET_HIDE_EXCLUDE_IDS = new Set(
        Array.from({length: 152 - 112 + 1}, (_, i) => 112 + i)   // IDs 112–152
    );

    // Budget-Status (wie auf der Budget-Seite)
    const STATUS = {
        0: {label: 'Offen', cls: 'warning', icon: 'fa-minus', on: false},
        1: {label: 'Freigegeben', cls: 'success', icon: 'fa-check', on: true},
        2: {label: 'Abgelehnt', cls: 'danger', icon: 'fa-times', on: false},
        3: {label: 'Planfortschreibung', cls: 'info', icon: 'fa-map', on: true},
    };
    // Änderungsart -> Badge-Klasse, Button-Klasse, standardmäßig sichtbar?
    const ARTEN = {
        'Zusatz': {badge: 'text-bg-success', btn: 'btn-outline-success', on: true},
        'Entfall': {badge: 'text-bg-danger', btn: 'btn-outline-danger', on: true},
        'Mengenänderung': {badge: 'text-bg-warning', btn: 'btn-outline-warning', on: true},
        'Budgetänderung': {badge: 'text-bg-info', btn: 'btn-outline-info', on: true},
        'Preisänderung': {badge: 'text-bg-secondary', btn: 'btn-outline-secondary', on: false},
        'unverändert': {badge: 'text-bg-light border', btn: 'btn-outline-dark', on: false}
    };
    // Budget Ende leer ODER (Art != Zusatz UND Budget Ende nicht leer
    //                        UND (Status enthält Freigegeben ODER Planfortschreibung))
    const sbCrit = (origData, data, condition, value = []) =>
        ({condition, data, origData, type: 'string', value});

    const SB_STANDARDFILTER = {
        logic: 'OR',
        criteria: [
            sbCrit('budgetEnd', 'Budget Ende', 'null'),
            {
                logic: 'AND',
                criteria: [
                    sbCrit('art', 'Änderungsart', '!=', ['Zusatz']),
                    sbCrit('budgetEnd', 'Budget Ende', '!null'),
                    {
                        logic: 'OR',
                        criteria: [
                            sbCrit('statusEnd', 'Status Ende', 'contains', ['Freigegeben']),
                            sbCrit('statusEnd', 'Status Ende', 'contains', ['Planfortschreibung'])
                        ]
                    }
                ]
            }
        ]
    };

    /** Reduziert eine Filterdefinition auf das Wesentliche, damit sie vergleichbar ist. */
    function sbKey(d) {
        if (d?.criteria) return [d.logic, d.criteria.map(sbKey)];
        return [d?.origData, d?.condition, String(d?.value ?? '')];
    }

    /** Text und Farbe des Filter-Buttons je nach aktivem Filter setzen. */
    function updateSbButton(dt) {
        const aktiv = JSON.stringify(sbKey(dt.searchBuilder.getDetails()))
            === JSON.stringify(sbKey(SB_STANDARDFILTER));
        const btn = dt.button('sbFilter:name');

        if (aktiv) btn.text('Standardfilter eingestellt');
        $(btn.node()).toggleClass('sb-standard', aktiv).toggleClass('bg-white', !aktiv);
    }


    let table = null;
    let lastMeta = null;
    let lastRange = null;
    let rawRows = [];     // Zeilen wie von der API geliefert (+ Status Ende), vor Schwellwert
    let budgetMap = {};   // id -> {id, nr, name, status}

    const $excelfilename = <?= json_encode($excelfilename) ?>;

    // ---------- Hilfsfunktionen ----------
    function pad(n) {
        return String(n).padStart(2, '0');
    }

    function toLocalInput(d) {
        return d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate());
    }

    function addDays(isoDate, days) {
        const d = new Date(isoDate + 'T00:00:00');
        d.setDate(d.getDate() + days);
        return toLocalInput(d);
    }

    function fmtDateDE(isoDate) {
        return new Date(isoDate + 'T00:00:00').toLocaleDateString('de-DE', {dateStyle: 'medium'});
    }

    function setDefaultRange() {
        $('#inpStart').val(DEFAULT_START);
        $('#inpEnd').val(toLocalInput(new Date()));
    }

    function setRange(days) {
        const end = new Date();
        const start = new Date();
        start.setDate(start.getDate() - days);
        $('#inpStart').val(toLocalInput(start));
        $('#inpEnd').val(toLocalInput(end));
    }

    function esc(v) {
        return String(v ?? '').replace(/[&<>"']/g, c => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
        }[c]));
    }

    function showMsg(text, type = 'danger') {
        $('#msgArea').html(
            '<div class="alert alert-' + type + ' alert-dismissible fade show small mb-0" role="alert">'
            + esc(text)
            + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Schließen"></button></div>'
        );
    }

    function setLoading(on) {
        $('#btnLoad').prop('disabled', on)
            .find('.spinner-border').toggleClass('d-none', !on).end()
            .find('.fa-sync-alt').toggleClass('d-none', on);
    }

    function money(d, type) {
        if (type !== 'display') return d ?? 0;
        return d === null ? '<span class="text-muted">–</span>' : eur.format(d);
    }

    function signedMoney(v) {
        const cls = v > 0 ? 'text-danger' : (v < 0 ? 'text-success' : 'text-muted');
        return '<span class="' + cls + '">' + (v > 0 ? '+' : '') + eur.format(v) + '</span>';
    }

    // ---------- Schwellwert Preisänderung ----------
    function getSchwelle() {
        const v = parseFloat(String($('#inpPreisSchwelle').val()).replace(',', '.'));
        return isNaN(v) || v < 0 ? 0 : v;
    }

    /** Wendet den Schwellwert auf die API-Zeilen an (nur Art wird umklassifiziert, Beträge bleiben echt). */
    function applySchwelle(rows) {
        const thr = getSchwelle();
        return rows.map(r => {
            const row = Object.assign({}, r);
            if (row.art === 'Preisänderung' && row.epStart !== null && row.epEnd !== null
                && Math.abs(row.epEnd - row.epStart) <= thr + 1e-9) {
                row.art = 'unverändert';
            }
            return row;
        });
    }

    // ---------- Preisanpassung in Prozent ----------
    function getFaktor(sel) {
        const v = parseInt($(sel).val(), 10);
        return isNaN(v) ? 0 : v;
    }

    function fmtPct(v) {
        return (v > 0 ? '+' : v < 0 ? '−' : '±') + Math.abs(v) + ' %';
    }

    function round2(v) {
        return Math.round(v * 100) / 100;
    }

    /** Anzeige der Slider-Werte, Reset-Button und Hinweis-Badge aktualisieren. */
    function updateFaktorLabels() {
        const fs = getFaktor('#rngFaktorStart'), fe = getFaktor('#rngFaktorEnd');
        $('#lblFaktorStart').text(fmtPct(fs)).toggleClass('text-danger', fs > 0).toggleClass('text-success', fs < 0);
        $('#lblFaktorEnd').text(fmtPct(fe)).toggleClass('text-danger', fe > 0).toggleClass('text-success', fe < 0);
        $('#btnFaktorReset').prop('disabled', fs === 0 && fe === 0);
        $('#faktorBadge')
            .toggleClass('d-none', fs === 0 && fe === 0)
            .text('Preise angepasst: Start ' + fmtPct(fs) + ' / Ende ' + fmtPct(fe));
    }

    /** Einheitspreise um x % ändern und Positionspreise neu berechnen (auf Kopien der Zeilen). */
    function applyFaktoren(rows) {
        const fs = 1 + getFaktor('#rngFaktorStart') / 100;
        const fe = 1 + getFaktor('#rngFaktorEnd') / 100;
        if (fs === 1 && fe === 1) return rows;
        return rows.map(row => {
            const r = Object.assign({}, row);
            r.epStart = r.epStart === null ? null : round2(r.epStart * fs);
            r.epEnd = r.epEnd === null ? null : round2(r.epEnd * fe);
            r.ppStart = round2((Number(r.stkStart) || 0) * (row.epStart ?? 0) * fs);
            r.ppEnd = round2((Number(r.stkEnd) || 0) * (row.epEnd ?? 0) * fe);
            r.ppDiff = round2(r.ppEnd - r.ppStart);
            return r;
        });
    }

    // ---------- Budgets ausblenden (Dropdown) + Status-Filter ----------
    // Ausgeblendet wird serverseitig pro Raumbuch-Zeile anhand ihres Endbudgets.

    function setBudgetMap(list) {
        budgetMap = {};
        (list || []).forEach(b => budgetMap[b.id] = b);
    }

    function hiddenBudgets() {
        return ($('#selBudgetHide').val() || []).map(Number);
    }

    function hiddenStatus() {
        return $('#statusFilter input:not(:checked)').map((i, el) => Number(el.value)).get();
    }

    function statusBadge(s) {
        const st = STATUS[s] ?? {label: 'unbekannt', cls: 'secondary', icon: 'fa-question'};
        return '<span class="badge text-bg-' + st.cls + ' me-1" title="' + st.label + '">'
            + '<i class="fas ' + st.icon + ' me-1"></i>' + st.label + '</span>';
    }

    function buildStatusFilter() {
        const $w = $('#statusFilter').empty();
        Object.entries(STATUS).forEach(([s, st]) => {
            const id = 'chkStatus' + s;
            $w.append($('<div class="form-check form-switch mb-1">').append(
                $('<input class="form-check-input" type="checkbox">').attr('id', id).val(s).prop('checked', st.on),
                $('<label class="form-check-label small">').attr('for', id).append(
                    $('<span class="badge">').addClass('text-bg-' + st.cls)
                        .append($('<i class="fas me-1">').addClass(st.icon), document.createTextNode(st.label)),
                    document.createTextNode(' anzeigen')
                )
            ));
        });
    }

    function updateBudgetHint() {
        const n = hiddenBudgets().length, ns = hiddenStatus().length;
        let t = n ? n + ' Budget' + (n === 1 ? '' : 's') + ' ausgeblendet. ' : 'Kein Budget ausgeblendet. ';
        if (ns) t += ns + ' Status ausgeblendet. ';
        if (lastMeta && (n || ns)) t += (lastMeta.hiddenRows || 0) + ' Raumbuch-Einträge ausgeblendet.';
        $('#budgetHint').text(t);
    }

    function selectDefaultBudgets() {
        $('#selBudgetHide option').each(function () {
            const id = Number(this.value);
            this.selected = !BUDGET_HIDE_EXCLUDE_IDS.has(id)
                && BUDGET_HIDE_DEFAULT.test($(this).data('nr') || '');
        });
        $('#selBudgetHide').trigger('change');
    }

    // Budget-/Status-Filter wirken serverseitig -> nach Änderung (entprellt) neu laden, sofern schon geladen
    let reloadTimer = null;

    function scheduleReload() {
        updateBudgetHint();
        if (!lastRange) return;
        clearTimeout(reloadTimer);
        reloadTimer = setTimeout(loadData, 500);
    }

    /** Status der Endbudgets je Zeile ergänzen. */
    function withStatusEnd(rows) {
        return rows.map(r => {
            const s = [...new Set((r.budgetIdsEnd || []).map(id => budgetMap[id]?.status)
                .filter(v => v !== undefined && v !== null))].sort();
            return Object.assign({}, r, {
                statusEndIds: s,
                statusEnd: s.map(v => STATUS[v]?.label ?? '?').join(', ')
            });
        });
    }

    // ---------- Filter Änderungsarten ----------
    function buildArtFilter() {
        const $wrap = $('#artFilter').empty();
        Object.entries(ARTEN).forEach(([art, cfg], i) => {
            const id = 'artChk' + i;
            $wrap.append(
                $('<input type="checkbox" class="btn-check" autocomplete="off">')
                    .attr('id', id).val(art).prop('checked', cfg.on),
                $('<label class="btn btn-sm">').addClass(cfg.btn).attr('for', id)
                    .append($('<span>').text(art), ' ', $('<span class="badge text-bg-light ms-1 art-count">').text('0'))
            );
        });
    }

    function activeArts() {
        return new Set($('#artFilter input:checked').map((i, el) => el.value).get());
    }

    function updateArtCounts(rows) {
        const counts = {};
        rows.forEach(r => counts[r.art] = (counts[r.art] || 0) + 1);
        $('#artFilter input').each(function () {
            $(this).next('label').find('.art-count').text(counts[this.value] || 0);
        });
    }

    DataTable.ext.search.push(function (settings, data, idx, row) {
        if (settings.nTable.id !== 'tblAenderungen') return true;
        return activeArts().has(row.art);
    });

    // ======================================================================
    // Excel-Export (ExcelJS) – Aufbau wie die Original-Änderungsliste
    // ======================================================================
    const XL_MONEY = '"€"\\ #,##0.00';
    const XL_GREY = 'FFD9D9D9';
    const XL_COLUMNS = [
        {header: 'Arbeitspaket - Nr.', key: 'apNr', width: 14},
        {header: 'Arbeitspaket - Bezeichnung', key: 'apName', width: 28},
        {header: 'Raumnr', key: 'raumnr', width: 22},
        {header: 'Raumbezeichnung', key: 'raumbez', width: 38},
        {header: 'Element', key: 'element', width: 44},
        {header: 'Variante', key: 'variante', width: 9},
        {header: 'VE Budget', key: 'veBudget', width: 11},
        {header: 'ENT Budget', key: 'entBudget', width: 11},
        {header: 'ENT Budget Status', key: 'entStatus', width: 18},
        {header: 'Zusatz/ Entfall', key: 'art', width: 16},
        {header: 'Veränderung zum VE auf Grund Nutzerabstimmung', key: 'kommentar', width: 50},
        {header: 'Σ VE Stk', key: 'veStk', width: 9},
        {header: 'Σ VE Positionspreis', key: 'vePP', width: 16},
        {header: 'Σ ENT Stk', key: 'entStk', width: 9},
        {header: 'Σ ENT Positionspreis', key: 'entPP', width: 16}
    ];

    let excelJsPromise = null;

    function loadExcelJs() {
        if (window.ExcelJS) return Promise.resolve();
        if (!excelJsPromise) {
            excelJsPromise = new Promise((resolve, reject) => {
                const s = document.createElement('script');
                s.src = EXCELJS_URL;
                s.onload = resolve;
                s.onerror = () => {
                    excelJsPromise = null;
                    reject(new Error('Excel-Bibliothek konnte nicht geladen werden.'));
                };
                document.head.appendChild(s);
            });
        }
        return excelJsPromise;
    }

    function xlZero() {
        return {veStk: 0, vePP: 0, entStk: 0, entPP: 0};
    }

    function xlAdd(acc, r) {
        acc.veStk += Number(r.stkStart) || 0;
        acc.vePP += Number(r.ppStart) || 0;
        acc.entStk += Number(r.stkEnd) || 0;
        acc.entPP += Number(r.ppEnd) || 0;
    }

    function xlStyleRow(row, {grey = false, bold = false} = {}) {
        for (let c = 1; c <= XL_COLUMNS.length; c++) {
            const cell = row.getCell(c);
            if (grey) cell.fill = {type: 'pattern', pattern: 'solid', fgColor: {argb: XL_GREY}};
            if (bold) cell.font = {bold: true};
            cell.border = {bottom: {style: 'hair', color: {argb: 'FFBFBFBF'}}};
            cell.alignment = {vertical: 'top', wrapText: c === 11 || c === 4 || c === 5};
        }
        row.getCell('vePP').numFmt = XL_MONEY;
        row.getCell('entPP').numFmt = XL_MONEY;
        row.getCell('veStk').numFmt = '0';
        row.getCell('entStk').numFmt = '0';
    }

    // Gruppenebenen für Ergebniszeilen (außen -> innen)
    const XL_LEVELS = [
        {
            id: r => r.apNr,
            skip: r => !r.apNr,
            label: r => ({apNr: r.apNr + ' Ergebnis'})
        },
        {
            id: r => r.apNr + '\u0001' + r.apName,
            skip: r => !r.apName,
            label: r => ({apNr: r.apNr, apName: r.apName + ' Ergebnis'})
        },
        {
            id: r => r.apNr + '\u0001' + r.apName + '\u0001' + r.raumnr,
            skip: () => false,
            label: r => ({apNr: r.apNr, apName: r.apName, raumnr: r.raumnr + ' Ergebnis'})
        }
    ];

    async function exportExcel() {
        if (!table) return;
        try {
            await loadExcelJs();
        } catch (e) {
            showMsg(e.message);
            return;
        }

        const rows = table.rows({search: 'applied', order: 'applied'}).data().toArray();
        if (!rows.length) {
            showMsg('Keine Positionen zum Exportieren.', 'warning');
            return;
        }
        const withTotals = $('#chkExcelErgebnis').is(':checked');

        const wb = new ExcelJS.Workbook();
        const ws = wb.addWorksheet('Änderungsliste', {
            views: [{state: 'frozen', ySplit: 1}],
            pageSetup: {
                paperSize: 8, orientation: 'landscape', fitToPage: true, fitToWidth: 1, fitToHeight: 0,
                printTitlesRow: '1:1'
            }
        });
        ws.columns = XL_COLUMNS.map(c => ({header: c.header, key: c.key, width: c.width}));

        const head = ws.getRow(1);
        xlStyleRow(head, {grey: true, bold: true});
        head.eachCell(cell => cell.alignment = {vertical: 'bottom', wrapText: true});
        head.height = 32;
        ws.autoFilter = {from: 'A1', to: {row: 1, column: XL_COLUMNS.length}};

        const writeTotal = (labels, acc) => {
            const row = ws.addRow(Object.assign({}, labels, acc));
            xlStyleRow(row, {grey: true, bold: true});
        };

        const open = [];
        const total = xlZero();
        const flush = (fromLevel) => {
            for (let i = XL_LEVELS.length - 1; i >= fromLevel; i--) {
                if (open[i]) {
                    if (withTotals && !XL_LEVELS[i].skip(open[i].row)) {
                        writeTotal(XL_LEVELS[i].label(open[i].row), open[i].acc);
                    }
                    open[i] = null;
                }
            }
        };

        rows.forEach(r => {
            const changed = XL_LEVELS.findIndex((L, i) => !open[i] || open[i].id !== L.id(r));
            if (changed !== -1) {
                flush(changed);
                for (let i = changed; i < XL_LEVELS.length; i++) {
                    open[i] = {id: XL_LEVELS[i].id(r), row: r, acc: xlZero()};
                }
            }

            const row = ws.addRow({
                apNr: r.apNr,
                apName: r.apName,
                raumnr: r.raumnr,
                raumbez: r.raumbez,
                element: r.element,
                variante: r.variante,
                veBudget: r.budgetStart || (r.stkStart > 0 ? 'NULL' : '--'),
                entBudget: r.budgetEnd || (r.stkEnd > 0 ? 'NULL' : '--'),
                entStatus: r.statusEnd || (r.stkEnd > 0 ? '' : '--'),
                art: r.art,
                kommentar: r.kommentar || '',
                veStk: Number(r.stkStart) || 0,
                vePP: Number(r.ppStart) || 0,
                entStk: Number(r.stkEnd) || 0,
                entPP: Number(r.ppEnd) || 0
            });
            xlStyleRow(row);

            open.forEach(o => xlAdd(o.acc, r));
            xlAdd(total, r);
        });
        flush(0);

        if (withTotals) {
            writeTotal({apNr: 'Gesamtergebnis'}, total);
            ws.addRow([]);
            const diff = ws.addRow({kommentar: 'Differenz zwischen ENT und VE', entPP: total.entPP - total.vePP});
            diff.getCell('kommentar').font = {bold: true};
            diff.getCell('kommentar').alignment = {horizontal: 'right'};
            diff.getCell('entPP').font = {bold: true};
            diff.getCell('entPP').numFmt = XL_MONEY;
        }

        // Info-Blatt mit den Einstellungen des Exports
        const info = wb.addWorksheet('Info');
        info.columns = [{width: 34}, {width: 60}];
        [
            ['Projekt', typeof currentP !== 'undefined' ? currentP : ''],
            ['Zeitraum', lastRange ? fmtDateDE(lastRange.von) + ' bis ' + fmtDateDE(lastRange.bis) : ''],
            ['Stichzeit', 'jeweils 01:00 Uhr (Änderungen am Bis-Tag enthalten)'],
            ['Nur Standort-Elemente', lastMeta && lastMeta.onlyStandort ? 'ja' : 'nein'],
            ['Bestand ausgeblendet', lastMeta && lastMeta.hideBestand ? 'ja' : 'nein'],
            ['Preisänderungen ignoriert bis', eur.format(getSchwelle()) + ' je Einheit'],
            ['Preisanpassung Start (VE)', fmtPct(getFaktor('#rngFaktorStart')) + ' auf alle Einheitspreise'],
            ['Preisanpassung Ende (ENT)', fmtPct(getFaktor('#rngFaktorEnd')) + ' auf alle Einheitspreise'],
            ['Ausgeblendete Budgets (Endbudget)',
                $('#selBudgetHide option:selected').map((i, o) => $(o).data('nr')).get().join(', ') || '–'],
            ['Ausgeblendete Budget-Status (Endbudget)',
                hiddenStatus().map(s => STATUS[s].label).join(', ') || '–'],
            ['Angezeigte Änderungsarten', Array.from(activeArts()).join(', ')],
            ['Exportiert am', new Date().toLocaleString('de-DE')]
        ].forEach(v => {
            const row = info.addRow(v);
            row.getCell(1).font = {bold: true};
        });

        const buf = await wb.xlsx.writeBuffer();
        const blob = new Blob([buf], {type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'});
        const a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = $excelfilename + (lastRange ? '_' + lastRange.von + '_' + lastRange.bis : '') + '.xlsx';
        document.body.appendChild(a);
        a.click();
        a.remove();
        setTimeout(() => URL.revokeObjectURL(a.href), 2000);
    }

    // ---------- Tabelle ----------
    function initTable(rows) {
        const txt = DataTable.render.text();
        table = new DataTable('#tblAenderungen', {
            data: rows,
            language: {url: '/utils/de-DE.json'},
            scrollX: true,
            pageLength: 100,
            lengthMenu: [25, 50, 100, 250, {label: 'Alle', value: -1}],
            orderFixed: {pre: [[0, 'asc'], [2, 'asc']]},
            order: [[5, 'asc'], [6, 'asc']],
            rowGroup: {
                dataSrc: 'raumnr',
                startRender: function (rows, group) {
                    const r = rows.data()[0];
                    const ap = r.apNr ? ' (' + r.apNr + ')' : '';
                    return $('<span>').text(group + '  ' + r.raumbez + ap).prop('outerHTML');
                },
                endRender: function (rows, group) {
                    const d = rows.data().toArray();
                    const s = (k) => d.reduce((a, r) => a + (Number(r[k]) || 0), 0);
                    return 'Σ ' + esc(group) + ': Stk ' + s('stkStart') + ' → ' + s('stkEnd')
                        + ' | PP ' + eur.format(s('ppStart')) + ' → ' + eur.format(s('ppEnd'))
                        + ' | Δ ' + signedMoney(s('ppEnd') - s('ppStart'));
                }
            },
            layout: {
                topStart: {
                    buttons: [
                        {
                            text: '<i class="fas fa-file-excel me-1"></i> Excel',
                            className: 'btn btn-sm btn-success',
                            action: function (e, dt, node) {
                                $(node).prop('disabled', true);
                                exportExcel().finally(() => $(node).prop('disabled', false));
                            }
                        },
                        {
                            extend: 'searchBuilder',
                            name: 'sbFilter',
                            text: 'Filter',
                            className: 'btn btn-sm btn-outline-dark bg-white fas fa-filter'
                        },
                        {
                            extend: 'colvis',
                            text: '<i class="fas fa-columns me-1"></i> Spalten',
                            className: 'btn btn-sm btn-outline-dark bg-light'
                        },
                        {
                            text: '<i class="fas fa-magic me-1"></i> Standardfilter',
                            className: 'btn btn-sm btn-outline-dark bg-light',
                            action: (e, dt) => {
                                dt.searchBuilder.rebuild(structuredClone(SB_STANDARDFILTER));
                                updateSbButton(dt);
                            }
                        },
                    ]
                },
                topEnd: 'search',
                bottomStart: ['pageLength', 'info'],
                bottomEnd: 'paging'
            },
            columns: [
                {data: 'apNr', render: txt},
                {data: 'apName', render: txt},
                {data: 'raumnr', render: txt},
                {data: 'raumbez', render: txt},
                {data: 'elementId', render: txt},
                {
                    data: 'element',
                    render: function (d, type, row) {
                        if (type !== 'display') return d;
                        const warn = row.ohnePreis
                            ? ' <i class="fas fa-exclamation-triangle text-warning" title="Kein Preis hinterlegt"></i>' : '';
                        return esc(d) + warn;
                    }
                },
                {data: 'variante', render: txt, className: 'text-center'},
                {
                    data: 'art',
                    render: function (d, type, row) {
                        if (type !== 'display') return d;
                        const cls = (ARTEN[d] || {}).badge || 'text-bg-light';
                        const tip = row.epStart !== row.epEnd && row.epStart !== null && row.epEnd !== null
                            ? ' title="EP ' + eur.format(row.epStart) + ' → ' + eur.format(row.epEnd) + '"' : '';
                        return '<span class="badge ' + cls + '"' + tip + '>' + d + '</span>';
                    }
                },
                {data: 'kommentar', render: txt, className: 'small'},
                {data: 'budgetStart', render: txt},
                {
                    data: 'budgetEnd',
                    render: function (d, type, row) {
                        if (type !== 'display') return d;
                        const t = esc(d);
                        return d !== row.budgetStart ? '<strong class="text-primary">' + t + '</strong>' : t;
                    }
                },
                {
                    data: 'statusEnd',
                    className: 'text-nowrap',
                    render: function (d, type, row) {
                        if (type !== 'display') return d;
                        return row.statusEndIds.length
                            ? row.statusEndIds.map(statusBadge).join('')
                            : '<span class="text-muted">–</span>';
                    }
                },
                {data: 'stkStart', className: 'text-end'},
                {
                    data: 'stkEnd', className: 'text-end',
                    render: (d, type, row) => type === 'display' && d !== row.stkStart ? '<strong>' + d + '</strong>' : d
                },
                {data: 'epStart', className: 'text-end text-nowrap', render: money},
                {
                    data: 'epEnd', className: 'text-end text-nowrap',
                    render: function (d, type, row) {
                        if (type !== 'display' || d === null || row.epStart === null
                            || Math.abs(d - row.epStart) < 0.005) {
                            return money(d, type);
                        }
                        return '<strong>' + eur.format(d) + '</strong>';
                    }
                },
                {data: 'ppStart', className: 'text-end text-nowrap', render: money},
                {data: 'ppEnd', className: 'text-end text-nowrap', render: money},
                {
                    data: 'ppDiff', className: 'text-end text-nowrap',
                    render: (d, type) => type === 'display' ? signedMoney(d) : d
                }
            ],
            initComplete: function () {
                const api = this.api();
                api.columns.adjust();
                relocateControls(api, '#CardHeaderAenderungsliste');
                api.on('draw', () => updateSbButton(api));
            },

            footerCallback: function () {
                const api = this.api();
                const sum = (c) => api.column(c, {search: 'applied'}).data().toArray()
                    .reduce((a, b) => a + (Number(b) || 0), 0);
                const st = sum(COL.stkStart), se = sum(COL.stkEnd);
                const ps = sum(COL.ppStart), pe = sum(COL.ppEnd);

                $(api.column(COL.stkStart).footer()).text(st);
                $(api.column(COL.stkEnd).footer()).text(se);
                $(api.column(COL.ppStart).footer()).text(eur.format(ps));
                $(api.column(COL.ppEnd).footer()).text(eur.format(pe));
                $(api.column(COL.ppDiff).footer()).html(signedMoney(pe - ps));

                updateSummary(api, st, se, ps, pe);
            }
        });
    }

    function updateSummary(api, st, se, ps, pe) {
        $('#sumStart').text(eur.format(ps));
        $('#sumEnd').text(eur.format(pe));
        $('#sumDiff').html(signedMoney(pe - ps));
        $('#sumStk').text(st + ' / ' + se);

        const counts = {};
        api.rows({search: 'applied'}).data().each(r => counts[r.art] = (counts[r.art] || 0) + 1);
        const html = Object.keys(ARTEN)
            .filter(k => counts[k])
            .map(k => '<span class="badge ' + ARTEN[k].badge + '">' + k + ' ' + counts[k] + '</span>')
            .join('');
        $('#artBadges').html(html || '<span class="text-muted small">Keine Positionen im Filter.</span>');
    }

    /** Schwellwert (auf Originalpreise) und Preisanpassung anwenden, dann Tabelle (neu) befüllen. */
    function renderRows() {
        const rows = applyFaktoren(applySchwelle(rawRows));
        updateBudgetHint();
        updateArtCounts(rows);
        if (!table) {
            initTable(rows);
        } else {
            table.clear().rows.add(rows).draw();
        }
    }

    // ---------- Arbeitspaket-Auswahl ----------
    const AP_NONE = '__ohne__';   // Platzhalter für Räume ohne Arbeitspaket

    // AP-Bezeichnungen (nur Anzeige). In der DB steht nur die Nummer (`Anmerkung allgemein`).
    // Liefert die API irgendwann selbst einen Namen (AP_NAME_COL), hat dieser Vorrang.
    const AP_NAMEN = {
        '4.1.1': 'ZNA',
        '4.1.2': 'EVA',
        '4.1.3': 'Räume Diagnostik, Behandlung und ambulante Bereiche übergeordnet',
        '4.1.4': 'Ambulanzgruppe 1 (ACH, URO, PCH)',
        '4.1.5': 'Ambulanzgruppe 2 (OTR, AN)',
        '4.1.6': 'Ambulanzgruppe 3 (2., 3., 6. Med)',
        '4.1.7': 'Ambulanzgruppe 4 (4., 5. Med, NEU, Konsil)',
        '4.1.8': 'Räume Pflege- und Tagesklinikbereiche übergeordnet',
        '4.1.9': 'Intensivmedizin',
        '4.1.10': 'Interventionelles Gefäßzentrum',
        '4.1.11': 'Endoskopie',
        '4.1.12': 'Chirurgische Tagesklinik',
        '4.1.13': 'Z-OP',
        '4.1.14': 'Allgemeine Räume übergeordnet',
        '4.1.15': 'Forschung',
        '4.1.16': 'Hygiene',
        '4.2.1': '1. MED - Onkologie, Hämatologie und Palliativmedizin',
        '4.2.2': '2. MED - Pneumologie',
        '4.2.3': '3. MED - Kardiologie',
        '4.2.4': '4. MED Gastroenterologie und Hepatologie',
        '4.2.5': '5. MED Endokrinologie, Rheumatologie und Akutgeriatrie',
        '4.2.6': '6. MED Nephrologie',
        '4.2.7': 'Neurologie',
        '4.2.8': 'Anästhesie, Intensiv- und Schmerzmedizin',
        '4.2.9': 'Allgemein- Viszeral- und Tumorchirurgie',
        '4.2.10': 'Orthopädie und Traumatologie',
        '4.2.11': 'Plastische, Ästhetische und Rekonstruktive Chirurgie',
        '4.2.12': 'Urologie',
        '4.2.13': 'Diagnostische und Interventionelle Radiologie',
        '4.2.14': 'Brustgesundheitszentrum',
        '4.2.15': 'Radioonkologie',
        '4.2.16': 'Nuklearmedizin',
        '4.2.17': 'Physikalische Medizin',
        '4.2.18': 'Zentrallaboratorium',
        '4.2.19': 'Pathologie und Mikrobiologie',
        '4.2.20': 'Apotheke',
        '5.1.1': 'Übergeordnete TPL - Koordination KB/NKB',
        '5.1.2': 'NKB admin. Bereiche',
        '5.1.3': 'Allgemeine Räume nicht-klinisch',
        '5.1.4': 'Klinisch-Administrativer-Dienst',
        '5.1.5': 'Bettenaufbereitung/-lagerung',
        '5.1.6': 'Speisenversorgung',
        '5.1.7': 'Wäscheversorgung',
        '5.1.8': 'Sterilgutversorgung',
        '5.1.9': 'Materialversorgung',
        '5.1.10': 'Transport Inhouse (intrahouse und interhouse)',
        '5.1.11': 'Abfallwirtschaft',
        '5.1.12': 'Reinigung',
        '5.1.13': 'Verkehr und Stellplätze',
        '5.1.14': 'Sonderfunktionen',
        '5.1.15': 'Fremdvermietete Flächen',
        '5.2.2': 'Werkstätten',
    };

    /** Name zu einer AP-Nummer; bei Unter-APs (z. B. 4.1.17.D) wird auf die übergeordnete Nummer zurückgegriffen. */
    function apNameFor(nr, fromApi = '') {
        if (fromApi) return fromApi;
        let key = String(nr ?? '').trim();
        while (key) {
            if (AP_NAMEN[key]) return AP_NAMEN[key];
            const cut = key.lastIndexOf('.');
            if (cut < 0) break;
            key = key.slice(0, cut);
        }
        return '';
    }

    function apSelection() {
        const all = $('#selAP option').length;
        const sel = $('#selAP').val() || [];
        return {values: sel, all: all > 0 && sel.length === all, none: all > 0 && sel.length === 0};
    }

    function updateApHint() {
        const total = $('#selAP option').length;
        if (!total) {
            $('#apHint').text('Noch keinem Raum ein Arbeitspaket zugeordnet.');
            return;
        }
        const s = apSelection();
        $('#apHint').text(s.all ? 'Alle Arbeitspakete gewählt.'
            : s.none ? 'Kein Arbeitspaket gewählt – bitte mindestens eines auswählen.'
                : s.values.length + ' von ' + total + ' gewählt.');
    }

    function setAllAps(on) {
        $('#selAP option').prop('selected', on);
        $('#selAP').trigger('change');
    }

    // ---------- Daten ----------
    function loadFilters() {
        $.post(API, {action: 'filters', csrf: CSRF}, function (res) {
            if (!res.ok) {
                showMsg(res.error);
                return;
            }
            const $sel = $('#selAP').empty();
            if (!res.hasAp) {
                $('#apHint').text('Arbeitspaket-Spalte in tabelle_räume nicht gefunden.');
            } else {
                res.arbeitspakete.forEach(ap => {
                    const name = apNameFor(ap.nr, ap.name);
                    const label = ap.nr + (name ? ' – ' + name : '') + ' (' + ap.raeume + ' Räume)';
                    $sel.append(new Option(label, ap.nr, true, true));
                });
                if (res.ohneAp > 0) {
                    $sel.append(new Option('— ohne Arbeitspaket — (' + res.ohneAp + ' Räume)', AP_NONE, true, true));
                }
                $sel.prop('disabled', !$sel.find('option').length).trigger('change');
                updateApHint();
            }

            // Budgets: AEV/AEG standardmäßig ausgeblendet
            setBudgetMap(res.budgets);
            const $bud = $('#selBudgetHide').empty();
            (res.budgets || []).forEach(b => {
                const st = STATUS[b.status];
                const label = b.nr + (b.name ? ' – ' + b.name : '') + (st ? ' (' + st.label + ')' : '');
                $bud.append($(new Option(label, b.id, false, false)).data('nr', b.nr));
            });
            $bud.prop('disabled', !$bud.find('option').length);
            updateBudgetHint();
//            selectDefaultBudgets();
        }, 'json').fail(xhr => showMsg(xhr.responseJSON?.error || 'Filter konnten nicht geladen werden.'));
    }

    function loadData() {
        if ($('#btnLoad').prop('disabled')) {   // läuft gerade -> später nochmal
            scheduleReload();
            return;
        }
        const von = $('#inpStart').val();
        const bis = $('#inpEnd').val();
        if (!von || !bis) {
            showMsg('Bitte Von- und Bis-Tag wählen.', 'warning');
            return;
        }
        if (von > bis) {
            showMsg('Der Von-Tag darf nicht nach dem Bis-Tag liegen.', 'warning');
            return;
        }
        const apSel = apSelection();
        if (apSel.none) {
            showMsg('Bitte mindestens ein Arbeitspaket wählen (oder „Alle“).', 'warning');
            return;
        }
        const start = von + ' 01:00:00';
        const end = addDays(bis, 1) + ' 01:00:00';

        $('#msgArea').empty();
        setLoading(true);
        $.post(API, {
            action: 'load',
            csrf: CSRF,
            start: start,
            end: end,
            apAll: apSel.all || $('#selAP option').length === 0 ? '1' : '0',
            ap: apSel.values,
            standort: $('#chkStandort').is(':checked') ? '1' : '0',
            ohneBestand: $('#chkOhneBestand').is(':checked') ? '1' : '0',
            hideBudgets: hiddenBudgets(),
            hideStatus: hiddenStatus()
        }, function (res) {
            if (!res.ok) {
                showMsg(res.error);
                return;
            }
            lastMeta = res.meta;
            lastRange = {von: von, bis: bis};
            setBudgetMap(res.budgets);              // aktuelle Status, falls inzwischen geändert
            rawRows = withStatusEnd(res.rows)
                .map(r => Object.assign(r, {apName: apNameFor(r.apNr, r.apName)}));
            $('#emptyState').addClass('d-none');
            $('#tableWrap').removeClass('d-none');
            renderRows();
            $('#periodLabel').text(fmtDateDE(von) + ' bis ' + fmtDateDE(bis));
            showMsg(res.rows.length + ' Positionen aus ' + res.meta.logEntries + ' Raumbuch- und '
                + res.meta.costEntries + ' Kostenänderungen ermittelt.', 'success');
        }, 'json')
            .fail(xhr => showMsg(xhr.responseJSON?.error || 'Fehler beim Laden der Daten.'))
            .always(() => setLoading(false));
    }

    // ---------- Linke Spalte ein-/ausblenden ----------
    function toggleLeftCol(show) {
        $('#colLeft').toggleClass('d-none', !show);
        $('#colRight')
            .toggleClass('col-lg-8 col-xxl-9', show)   // neben der linken Spalte
            .toggleClass('col-lg-12', !show);          // volle Breite
        $('#showLeftCardBtn').toggleClass('d-none', show);
        if (table) table.columns.adjust();             // DataTable an neue Breite anpassen
    }

    function show_modal(modal_id) {
        $('#' + modal_id).modal('show');
    }

    // ---------- Init ----------
    $(function () {
        setDefaultRange();
        buildArtFilter();
        buildStatusFilter();

        $('#selAP').select2({
            width: '100%',
            placeholder: 'Kein Arbeitspaket gewählt',
            allowClear: true,
            closeOnSelect: false
        }).on('change', updateApHint);

        $('#selBudgetHide').select2({
            width: '100%',
            placeholder: 'Kein Budget ausgeblendet',
            allowClear: true,
            closeOnSelect: false
        }).on('change', scheduleReload);

        $('#statusFilter').on('change', 'input', scheduleReload);
        $('#btnBudAev').on('click', selectDefaultBudgets);
        $('#btnApAll').on('click', () => setAllAps(true));
        loadFilters();

        $('[data-days]').on('click', function () {
            setRange(Number($(this).data('days')));
        });
        $('#btnLoad').on('click', loadData);
        $('#hideLeftCardBtn').on('click', () => toggleLeftCol(false));
        $('#showLeftCardBtn').on('click', () => toggleLeftCol(true));
        $('#artFilter').on('change', 'input', () => table && table.draw());
        $('#inpPreisSchwelle').on('input change', () => {
            if (rawRows.length) renderRows();
        });

        // Slider: Beschriftung live beim Ziehen, Tabelle erst beim Loslassen neu berechnen
        $('#rngFaktorStart, #rngFaktorEnd')
            .on('input', updateFaktorLabels)
            .on('change', () => {
                updateFaktorLabels();
                if (rawRows.length) renderRows();
            });
        $('#btnFaktorReset').on('click', () => {
            $('#rngFaktorStart, #rngFaktorEnd').val(0);
            updateFaktorLabels();
            if (rawRows.length) renderRows();
        });
        updateFaktorLabels();
    });
</script>
</body>
</html>