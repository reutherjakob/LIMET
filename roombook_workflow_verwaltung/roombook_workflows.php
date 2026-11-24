<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>RB-Projekte – Workflow-Verwaltung</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../_utils/style.css" type="text/css" media="screen"/>
    <link rel="icon" href="../Logos/iphone_favicon.png">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css"
          integrity="sha512-q3eWabyZPc1XTCmF+8/LuE1ozpg5xxn7iO89yfSOd5/oKvyqLngoNGsx8jq92Y8eXJ/IRxQbEC+FGSYxtk2oiw=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
</head>

<?php
require_once '../_utils/_utils.php';
init_page_serversides();
$projectID = (int)$_SESSION["projectID"];
?>

<body>
<div id="limet-navbar"></div>

<div class="container-fluid py-3">
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h4 class="mb-0"><i class="fas fa-stream me-2"></i>Workflow-Verwaltung</h4>
            <small class="text-muted"><i class="fas fa-skull-crossbones"></i> Obacht! Bestehende Workflows zu ändern
                beeinflusst ggf. andere Projekte und bestehende Vergabekalender! <i
                        class="fas fa-skull-crossbones"></i> </small>

            <div class="btn-group btn-group-sm">
                <button type="button" id="wfAssignBtn" class="btn btn-outline-primary rounded-2 me-1 ">
                    <i class="fas fa-link me-1"></i> Bestehenden Workflow zu Projekt hinzufügen
                </button>
                <button type="button" id="wfNewBtn" class="btn btn-success rounded-2">
                    <i class="fas fa-plus me-1"></i> Neuen Workflow erstellen
                </button>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card px-0 mx-0">
                <div class="card-header">
                    <span class="fw-semibold"><i
                                class="fas fa-chart-bar me-2"></i>Workflows im Projekt (Klicken zum Aufklappen)</span>
                </div>
                <div id="wfEmptyHint" class="alert alert-info d-none">
                    Diesem Projekt sind noch keine Workflows zugeordnet. Lege über
                    „Neuer Workflow“ einen an oder füge über „Bestehenden hinzufügen“
                    einen vorhandenen hinzu.
                </div>
                <div id="wfContainer">
                    <div class="text-muted fst-italic">Lädt…</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Visualisierung / Übersicht aller Workflows des Projekts -->
    <div class="row mt-4 d-none" id="wfOverview">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <span class="fw-semibold"><i
                                class="fas fa-chart-bar me-2"></i>Workflow-Übersicht (alle Workflows)</span>
                    <div class="d-flex align-items-center flex-wrap gap-2">
                        <div class="btn-group btn-group-sm" role="group" id="wfOverviewFilter">
                            <input type="radio" class="btn-check" name="wfOvFilter" id="wfOvFilterAll" value="all"
                                   checked>
                            <label class="btn btn-outline-secondary" for="wfOvFilterAll">Alle <span
                                        class="badge bg-secondary" id="wfOvCntAll">0</span></label>
                            <input type="radio" class="btn-check" name="wfOvFilter" id="wfOvFilterIn" value="in">
                            <label class="btn btn-outline-secondary" for="wfOvFilterIn">Im Projekt <span
                                        class="badge bg-primary" id="wfOvCntIn">0</span></label>
                            <input type="radio" class="btn-check" name="wfOvFilter" id="wfOvFilterOut" value="out">
                            <label class="btn btn-outline-secondary" for="wfOvFilterOut">Nicht im Projekt <span
                                        class="badge bg-secondary" id="wfOvCntOut">0</span></label>
                        </div>
                        <input type="search" id="wfOverviewSearch" class="form-control form-control-sm w-auto"
                               placeholder="Suchen (Name / Typ / Projekt)…">
                    </div>
                </div>
                <div class="card-body">
                    <div id="wfOverviewKpis" class="row g-2 mb-3"></div>

                    <!-- Zeit-Diagramm -->
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                        <span class="small fw-semibold text-muted">
                            <i class="fas fa-chart-line me-1"></i>Zeitlicher Ablauf
                            <span class="fw-normal">– x: Tage ab Start, y: Workflowteil (Reihenfolgennummer)</span>
                        </span>
                        <select id="wfChartSelect" class="form-select form-select-sm w-auto">
                            <option value="all">Alle gefilterten Workflows (Vergleich)</option>
                        </select>
                    </div>
                    <div id="wfChartWrap" class="position-relative border rounded p-2 mb-1" style="height:420px">
                        <canvas id="wfChart"></canvas>
                    </div>
                    <div id="wfChartHint" class="small text-muted mb-3">
                        Legende anklicken blendet Workflows ein/aus. Tabellenzeile anklicken zeigt den Workflow als
                        Gantt-Diagramm.
                    </div>

                    <small class="text-muted d-block mb-1">
                        Balkenlänge = Mindestdauer (Summe „Tage danach“), maßstabsgleich über alle angezeigten
                        Workflows.
                    </small>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead class="table-light">
                            <tr>
                                <th>Workflow</th>
                                <th>Typ</th>
                                <th class="text-center">Schritte</th>
                                <th class="text-center text-nowrap">Dauer (Tage)</th>
                                <th class="w-25">Ablauf</th>
                                <th>Zugeordnete Projekte</th>
                                <th>Verwendet in Losen</th>
                            </tr>
                            </thead>
                            <tbody id="wfOverviewBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include 'modale_workflow.php';
?>


<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script src="../_utils/_utils.js"></script>
<script src="workflowManagement.js"></script>
</body>
</html>