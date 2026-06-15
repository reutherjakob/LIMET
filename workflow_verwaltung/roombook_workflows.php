<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>RB-Projekte – Workflow-Verwaltung</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../css/style.css" type="text/css" media="screen"/>
    <link rel="icon" href="../Logo/iphone_favicon.png">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css"
          integrity="sha512-q3eWabyZPc1XTCmF+8/LuE1ozpg5xxn7iO89yfSOd5/oKvyqLngoNGsx8jq92Y8eXJ/IRxQbEC+FGSYxtk2oiw=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
</head>

<?php
require_once '../utils/_utils.php';
init_page_serversides("No Redirect");
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

<?php
include 'modale_workflow.php';
?>


<script src="../utils/_utils.js"></script>
<script src="workflowManagement.js"></script>
</body>
</html>