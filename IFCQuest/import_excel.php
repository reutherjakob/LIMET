<?php
if (!function_exists('utils_connect_sql')) {
    include "../utils/_utils.php";
}
init_page_serversides("No Redirect");
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>RB-Projekte – Excel Import</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="stylesheet" href="../css/style.css" type="text/css" media="screen"/>
    <link rel="icon" href="../Logo/iphone_favicon.png"/>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css"
          integrity="sha512-q3eWabyZPc1XTCmF+8/LuE1ozpg5xxn7iO89yfSOd5/oKvyqLngoNGsx8jq92Y8eXJ/IRxQbEC+FGSYxtk2oiw=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

    <style>
        /* ── Batch Compare Card ───────────────────────────────────── */
        .batch-room-row {
            display: grid;
            grid-template-columns: 32px 1fr auto auto;
            align-items: center;
            gap: .5rem;
            padding: .45rem .75rem;
            border-bottom: 1px solid #f0f0f0;
            transition: background .12s;
            cursor: default;
        }

        .batch-room-row:last-child {
            border-bottom: none;
        }

        .batch-room-row:hover {
            background: #f8f9fa;
        }

        .batch-room-row .room-label {
            font-size: .85rem;
        }

        .batch-room-row .room-nr {
            font-weight: 600;
            margin-right: .35rem;
        }

        .batch-room-row .room-bez {
            color: #6c757d;
            font-size: .8rem;
        }

        .batch-detail-panel {
            background: #f8f9fa;
            border-top: 1px solid #e9ecef;
            padding: .6rem .75rem;
        }

        /* mini comparison table inside batch panel */
        .batch-detail-panel .mini-compare-table th,
        .batch-detail-panel .mini-compare-table td {
            font-size: .76rem;
            padding: .2rem .4rem;
            vertical-align: middle;
        }

        .ambiguous-inline-wrap {
            background: #fff8e1;
            border: 1px solid #ffe082;
            border-radius: 6px;
            padding: .5rem .75rem;
            margin: .35rem 0;
            font-size: .78rem;
        }

        .ambiguous-inline-wrap .cand-btn {
            font-size: .72rem;
            padding: .1rem .4rem;
        }

        #batch-progress-wrap .progress {
            height: 6px;
            border-radius: 3px;
        }

        .status-pill-batch {
            display: inline-flex;
            align-items: center;
            gap: .25rem;
            font-size: .72rem;
            font-weight: 600;
            padding: .18rem .5rem;
            border-radius: 20px;
            white-space: nowrap;
        }

        .batch-toggle-btn {
            font-size: .72rem;
            padding: .1rem .45rem;
            white-space: nowrap;
        }
    </style>
</head>
<body>
<div id="limet-navbar"></div>
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="row">
            <div class="col-xl-12">
                <input type="file" id="file-input" accept=".xlsx,.xls" style="display:none"/>

                <!-- ══ Card 1: Drop Zone ══ -->
                <div class="row">
                    <div class="col-xl-9">
                        <div class="card mb-2 mt-2">
                            <div class="card-body text-center">
                                <div id="drop-zone" onclick="$('#file-input').click()">
                                    <div class="drop-icon">
                                        <i class="fas fa-cloud-upload-alt me-2"></i>Excel-Datei hierher ziehen oder
                                        klicken
                                        <div id="file-name-display" class="text-primary fw-semibold small"
                                             style="display:none"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3">
                        <div class="card mb-2 mt-2">
                            <div class="card-body text-center">

                            </div>
                        </div>
                    </div>
                </div>

                <!-- ══ Card 2: Mapping + Vorschau ══ -->
                <div id="mapping-card" class="card" style="display:none">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-6">
                                <span class="fw-semibold">Spalten festlegen</span>
                                <span class="ms-auto text-muted small" id="preview-row-count"></span>
                            </div>
                            <div class="col-6 d-flex justify-content-end">
                                <button id="btn-check-rooms" class="btn btn-primary btn-sm" disabled>
                                    <i class="fas fa-search me-2"></i>Räume im Projekt prüfen
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="kern-spalten-rows" class="row"></div>
                        <div class="mt-2">
                            <span class="fw-semibold">MZ-Parameter Spalten zuordnen </span>
                            <span class="small">(werden nur ausgelesen wenn Spalte zugewiesen)</span>
                        </div>
                        <div class="row" id="mz-param-rows"></div>
                        <div class="card">
                            <div class="card-header py-2"
                                 style="cursor:pointer; user-select:none"
                                 data-bs-toggle="collapse"
                                 data-bs-target="#preview-collapse">
                                <span class="fw-semibold small"><i class="fas fa-table me-1"></i>Tabellenvorschau</span>
                                <i class="fas fa-chevron-down float-end mt-1 small"></i>
                            </div>
                            <div id="preview-collapse" class="collapse show">
                                <div class="card-body p-0">
                                    <div style="overflow-x: auto; max-height: 350px; overflow-y: auto;">
                                        <table class="table table-sm table-hover mb-0" id="preview-table">
                                            <thead class="sticky-top table-light">
                                            <tr id="preview-thead-row"></tr>
                                            </thead>
                                            <tbody id="preview-tbody"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-top-0 text-muted small d-flex justify-content-end"
                         id="preview-footer"></div>
                </div>

                <!-- ══ Card 3: Validierungsergebnis ══ -->
                <div id="validation-card" class="card" style="display:none">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <span class="fw-semibold">Raum-Prüfung</span>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <div id="validation-stats" class="d-flex flex-wrap gap-1"></div>
                                <button id="btn-compare-all" class="btn btn-success btn-sm" style="display:none">
                                    <i class="fas fa-layer-group me-1"></i>Alle Räume abgleichen
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="validation-wrap">
                            <table id="validation-table" class="table table-sm table-hover">
                                <thead>
                                <tr>
                                    <th style="width:36px"></th>
                                    <th>Raumnummer<br><small class="fw-normal text-muted">(aus Excel)</small></th>
                                    <th>Raumbezeichnung<br><small class="fw-normal text-muted">(aus DB)</small></th>
                                    <th>Geschoss</th>
                                    <th>Elemente in DB</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody id="validation-tbody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- ══ Card 4: Batch-Abgleich ══════════════════════════════ -->
                <div id="batch-compare-card" class="card mt-2" style="display:none">
                    <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="fw-semibold">Gesamt-Abgleich</span>
                            <span id="batch-stats-pills" class="d-flex gap-1 flex-wrap"></span>
                        </div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <!-- Progress (shown while loading) -->
                            <span id="batch-progress-wrap" style="display:none; min-width:180px">
                                <div class="d-flex justify-content-between small text-muted mb-1">
                                    <span id="batch-progress-label">Lade…</span>
                                    <span id="batch-progress-count"></span>
                                </div>
                                <div class="progress"><div id="batch-progress-bar"
                                                           class="progress-bar bg-primary progress-bar-striped progress-bar-animated"
                                                           style="width:0%"></div></div>
                            </span>
                            <!-- Global sync button (shown when ready) -->
                            <button id="btn-batch-sync" class="btn btn-success btn-sm" style="display:none" disabled>
                                <i class="fas fa-database me-1"></i>Alle Änderungen übernehmen
                            </button>
                        </div>
                    </div>

                    <!-- Kommentar-Zeile -->
                    <div id="batch-sync-kommentar-wrap" class="border-bottom px-3 py-2" style="display:none">
                        <div class="d-flex align-items-center gap-2">
                            <label class="form-label mb-0 small fw-semibold text-nowrap">Kommentar (entfernte
                                Elemente):</label>
                            <input type="text" id="batch-sync-kommentar" class="form-control form-control-sm"
                                   value="Entfernt via Excel-Abgleich" style="max-width:340px"/>
                        </div>
                    </div>

                    <!-- Result banner after sync -->
                    <div id="batch-sync-result" class="mx-3 mt-2" style="display:none"></div>

                    <!-- One row per room -->
                    <div id="batch-rooms-container"></div>
                </div>

                <!-- ══ Card 5: Einzel-Element-Abgleich (bleibt, aber versteckt) ══ -->
                <div id="compare-section" style="display:none">
                    <div class="card mb-0">
                        <div class="card-header"
                             style="cursor:pointer; user-select:none"
                             data-bs-toggle="collapse"
                             data-bs-target="#compare-collapse">
                            <div class="row align-items-center">
                                <div class="col-6 d-flex align-items-center">
                                    <span class="fw-semibold">Element-Abgleich</span>
                                    <span class="ms-2 text-muted" id="compare-room-label"></span>
                                </div>
                                <div class="col-6 d-flex justify-content-end align-items-center gap-2">
                                    <span class="stat-pill bg-success bg-opacity-10 text-success"><i
                                                class="fas fa-check fa-xs"></i> match</span>
                                    <span class="stat-pill bg-warning bg-opacity-10 text-warning-emphasis"><i
                                                class="fas fa-not-equal fa-xs"></i> Anzahl diff</span>
                                    <span class="stat-pill bg-primary bg-opacity-10 text-primary"><i
                                                class="fas fa-plus fa-xs"></i> nur Excel</span>
                                    <span class="stat-pill bg-danger bg-opacity-10 text-danger"><i
                                                class="fas fa-minus fa-xs"></i> nur DB</span>
                                    <i class="fas fa-chevron-down small ms-2 compare-chevron"></i>
                                </div>
                            </div>
                        </div>
                        <div id="compare-collapse" class="collapse show">
                            <div id="unmapped-section" class="card border-warning mb-2" style="display:none">
                                <div class="card-body py-2 px-3 d-flex align-items-start justify-content-between flex-wrap">
                                    <div>
                                        <small class="text-warning-emphasis fw-semibold">
                                            <i class="fas fa-exclamation-triangle me-1"></i>Nicht gemappte Familien:
                                        </small>
                                        <div id="unmapped-list" class="text-muted small mt-1"></div>
                                    </div>
                                    <button class="btn btn-outline-secondary btn-sm" onclick="copyUnmapped()"
                                            title="Kopieren">
                                        <i class="fas fa-copy me-1"></i>Kopieren
                                    </button>
                                </div>
                            </div>
                            <div id="compare-blocks-container"></div>
                            <div class="card mt-2" id="sync-card" style="display:none">
                                <div class="card-header"><span class="fw-semibold">Änderungen übernehmen</span></div>
                                <div class="card-body">
                                    <div id="sync-summary" class="mb-3 small"></div>
                                    <label class="form-label small fw-semibold">Kommentar für entfernte
                                        Elemente:</label>
                                    <label for="sync-kommentar"></label>
                                    <input type="text" id="sync-kommentar" class="form-control form-control-sm mb-3"
                                           value="Entfernt via Excel-Abgleich"/>
                                    <div class="d-flex align-items-center gap-2">
                                        <button id="btn-sync" class="btn btn-success btn-sm">
                                            <i class="fas fa-database me-2"></i>Änderungen in DB übernehmen
                                        </button>
                                    </div>
                                    <div id="sync-result" class="mt-3" style="display:none"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

</body>

<script src="utils.js"></script>
<script src="excel_reader.js"></script>
<script src="column_mapping.js"></script>
<script src="room_check.js"></script>
<script src="element_compare.js"></script>
</html>