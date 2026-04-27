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


</head>
<body>
<div id="limet-navbar"></div>
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="row">
            <div class="col-xl-12">
                <input type="file" id="file-input" accept=".xlsx,.xls" style="display:none"/>
                <div class="card">
                    <div class="card-body text-center ">
                        <div id="drop-zone" onclick="$('#file-input').click()">
                            <div class="drop-icon"><i class="fas fa-cloud-upload-alt  me-2">
                                </i>Excel-Datei hierher ziehen oder klicken
                                <div id="file-name-display"
                                     class="text-primary fw-semibold small"
                                     style="display:none">
                                </div>
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
                                <span class="ms-auto text-muted small" id="preview-row-count"></span></div>
                            <div class="col-6 d-flex justify-content-end">
                                <button id="btn-check-rooms" class="btn btn-primary btn-sm" disabled>
                                    <i class="fas fa-search me-2"></i>Räume im Projekt prüfen
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div id="kern-spalten-rows" class="row"></div>
                        <div class=" mt-2">
                            <span class="fw-semibold ">MZ-Parameter Spalten zuordnen </span>
                            <span class="small">(werden nur ausgelesen wenn Spalte zugewiesen)</span>
                        </div>

                        <div class="row " id="mz-param-rows">
                            <!-- wird per JS befüllt -->
                        </div>

                        <div class="preview-wrap border-top mt-2"> <!-- Tabellenvorschau -->
                            <table class="table table-sm table-hover mb-0" id="preview-table">
                                <thead>
                                <tr id="preview-thead-row"></tr>
                                </thead>
                                <tbody id="preview-tbody"></tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card-footer bg-white border-top-0 text-muted small d-flex justify-content-end"
                         id="preview-footer">
                    </div>
                </div>


                <!-- ══ Card 3: Validierungsergebnis ══ -->
                <div id="validation-card" class="card" style="display:none">

                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <span class="fw-semibold">Raum-Prüfung</span>
                            </div>
                            <div class="d-flex align-items-centerflex-wrap">
                                <div id="validation-stats" class="d-flex flex-wrap "></div>
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
                <!-- ══ Card 4: Element-Abgleich ══════════════════════════════════════ -->

                <div id="compare-section" style="display:none">

                    <!-- Header card -->
                    <div class="card mb-0">
                        <div class="card-header">
                            <div class="row">
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
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Unmapped families -->
                    <div id="unmapped-section" class="card border-warning mb-2" style="display:none">
                        <div class="card-body py-2 px-3 d-flex align-items-start justify-content-between flex-wrap">
                            <div>
                                <small class="text-warning-emphasis fw-semibold">
                                    <i class="fas fa-exclamation-triangle me-1"></i>Nicht gemappte Familien:
                                </small>
                                <div id="unmapped-list" class="text-muted small mt-1"></div>
                            </div>
                            <button class="btn btn-outline-secondary btn-sm" onclick="copyUnmapped()" title="Kopieren">
                                <i class="fas fa-copy me-1"></i>Kopieren
                            </button>
                        </div>
                    </div>

                    <!-- Element blocks (one per element) rendered by JS -->
                    <div id="compare-blocks-container"></div>

                    <!-- ══ Sync card ════════════════════════════════════════════════ -->
                    <div class="card mt-2" id="sync-card" style="display:none">
                        <div class="card-header">
                            <span class="fw-semibold">Änderungen übernehmen</span>
                        </div>
                        <div class="card-body">
                            <div id="sync-summary" class="mb-3 small"></div>
                            <label class="form-label small fw-semibold">Kommentar für entfernte Elemente:</label>
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

</body>

<script src="utils.js"></script>
<script src="excel_reader.js"></script>
<script src="column_mapping.js"></script>
<script src="room_check.js"></script>
<script src="element_compare.js"></script>
</html>