<?php
include "../../utils/_utils.php";
init_page_serversides("", "x");

$projectID = $_SESSION["projectID"];
$conn = utils_connect_sql();
$raumbereichOptions = [];
$sql = "SELECT DISTINCT `Raumbereich Nutzer` 
        FROM tabelle_räume 
        WHERE tabelle_projekte_idTABELLE_Projekte = ? 
        ORDER BY `Raumbereich Nutzer`";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $projectID);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    if (!empty($row["Raumbereich Nutzer"])) {
        $raumbereichOptions[] = $row["Raumbereich Nutzer"];
    }
}
$stmt->close();

$sql = "    SELECT idTABELLE_Räume AS id,  CONCAT(Raumnr, ' - ', Raumbezeichnung, ' - ', `Raumbereich Nutzer`) AS text
            FROM tabelle_räume
            WHERE tabelle_projekte_idTABELLE_Projekte = ? AND Entfallen =0 
            ORDER BY Raumnr";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $projectID);
$stmt->execute();
$result = $stmt->get_result();

$raeume = [];
while ($row = $result->fetch_assoc()) {
    $raeume[] = $row;
}
$stmt->close();

$sql = "SELECT tabelle_elemente.idTABELLE_Elemente as id, CONCAT(ElementID,' ', Bezeichnung) as Bez
  		FROM tabelle_elemente 
  		ORDER BY Bez";

$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$elemente = [];
while ($row = $result->fetch_assoc()) {
    $elemente[] = $row;
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Besprechungsansicht</title>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="../../css/style.css" type="text/css" media="screen"/>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css"/>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
    <link href="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.2.1/af-2.7.0/b-3.2.1/b-colvis-3.2.1/b-html5-3.2.1/b-print-3.2.1/cr-2.0.4/date-1.5.5/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.1/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-3.0.0/sr-1.4.1/datatables.min.css"
          rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.2.1/af-2.7.0/b-3.2.1/b-colvis-3.2.1/b-html5-3.2.1/b-print-3.2.1/cr-2.0.4/date-1.5.5/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.1/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-3.0.0/sr-1.4.1/datatables.min.js"></script>

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/css/bootstrap-datepicker3.min.css">
    <script type='text/javascript'
            src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/js/bootstrap-datepicker.min.js"></script>
    <style>
        .embed-responsive-item {
            width: 100% !important;
            height: 100% !important;
            border: none;
            display: block;
        }

        .status-green {
            background-color: #d4edda !important;
        }

        .status-blue {
            background-color: #cce5ff !important;
        }

        .status-yellow {
            background-color: #fff3cd !important;
        }

        .status-red {
            background-color: #f8d7da !important;
        }
    </style>
</head>
<body>

<div id="limet-navbar"></div>
<div class="container-fluid">
    <!-- Nav tabs -->
    <ul class="nav nav-tabs mb-3" id="limetTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="tab1-tab" data-bs-toggle="tab" data-bs-target="#tab1-content"
                    type="button" role="tab" aria-controls="tab1-content" aria-selected="true">
                Übersicht
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab2-tab" data-bs-toggle="tab" data-bs-target="#tab2-content" type="button"
                    role="tab" aria-controls="tab2-content" aria-selected="false">
                PDF & Vermerke
            </button>
        </li>
    </ul>

    <!-- Tab content -->
    <div class="tab-content" id="limetTabContent">
        <!-- First tab with filters and table -->
        <div class="tab-pane fade show active" id="tab1-content" role="tabpanel" aria-labelledby="tab1-tab">
            <div class="row row-cols-2">
                <div class="col-lg-2 mx-auto mb-4" id="filterCardCol">
                    <!-- Filter card -->
                    <div class="card mb-2">
                        <div class="card-header d-inline-flex align-items-center ">
                            <button type="button" class="btn btn-sm btn-outline-success" id="createMeetingBtn"
                                    data-bs-toggle="modal" data-bs-target="#createMeetingModal">
                                <i class="fa fa-plus me-1"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-success" id="openMeetingBtn"
                                    data-bs-toggle="modal" data-bs-target="#besprechungSelectModal">
                                <i class="fas fa-folder-open me-1"></i>
                            </button>
                            <button type="reset" class="btn btn-sm btn-outline-dark" title="Reset"
                                    id="ResetBesprechung">
                                <i class="fas fa-sync-alt"></i>
                            </button>

                            <span class="badge rounded-pill bg-light text-dark p-2" data-bs-toggle="popover"
                                  data-bs-content="Jede Besprechung generiert ein eigenes Protokoll. Mehrere Einträge desselben Elments je Raum werden vor der Tabellen Anzeige konsolidiert.">
                                    <i class="fas fa-info-circle"></i>
                            </span>
                            <span id="currentMeetingName" class="fw-bold d-flex justify-content-center"></span>
                        </div>

                    </div>
                    <div class="card mb-2">
                        <form id="filterForm">
                            <div class="card-header d-flex flex-nowrap">
                                <label for="raumbereich" class="form-label"></label>
                                <select id="raumbereich" name="raumbereich[]" class="form-select" style="width:95%"
                                        multiple>
                                    <?php foreach ($raumbereichOptions as $option): ?>
                                        <option value="<?= htmlspecialchars($option) ?>"><?= htmlspecialchars($option) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <span class="badge rounded-pill bg-light text-dark p-2" data-bs-toggle="popover"
                                      data-bs-content="Hier werden ALLE Raumbereiche des Projektes angezeigt, basierend auf der textuellen Bezeichnung Raumbereich Nutzer und unabhängig davon, ob diese z.B. MT-relevante Räume haben.">
                  <i class="fas fa-info-circle"></i>
                </span>
                            </div>
                            <div class="card-body">
                                <!-- Additional filters as in original markup -->
                                <div class="d-flex flex-nowrap mb-2">
                                    <select id="zusatzRaeume" name="zusatzRaeume[]" class="form-select"
                                            style="width:95%" multiple>
                                        <?php foreach ($raeume as $raum): ?>
                                            <option value="<?= htmlspecialchars($raum['id'] ?? '') ?>"><?= htmlspecialchars($raum['text'] ?? '') ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <span class="badge rounded-pill bg-light text-dark p-2" data-bs-toggle="popover"
                                          data-bs-content="Hier werden ALLE Räume des Projektes angezeigt. Sollten diese dann in der Tabelle fehlen, sind die gewählten Räume ggf. durch unten angeführte Filter ausgeschieden.">
                    <i class="fas fa-info-circle"></i>
                  </span>
                                </div>
                                <div class="d-flex flex-nowrap mb-2">
                                    <select id="zusatzElemente" name="zusatzElemente[]" class="form-select"
                                            style="width:95%" multiple>
                                        <?php foreach ($elemente as $element): ?>
                                            <option value="<?= htmlspecialchars($element['id']) ?>"><?= htmlspecialchars($element['Bez']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <span class="badge rounded-pill bg-light text-dark p-2" data-bs-toggle="popover"
                                          data-bs-content="Hier werden ALLE Elemente des Projektes angezeigt. Sollten diese dann in der Tabelle fehlen, sind ggf. Elemente Stk<1 ausgeblendet. - Abgesehen der Zusätzlichen nur jenen Elemente angezeigt, die Ihnen Standort im Raum haben">
                    <i class="fas fa-info-circle"></i>
                  </span>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input btn btn-outline-dark" type="checkbox" id="mtRelevant"
                                           name="mtRelevant" checked>
                                    <label class="form-check-label" for="mtRelevant">Nur MT-relevante Räume</label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="entfallen" name="entfallen"
                                           checked>
                                    <label class="form-check-label" for="entfallen">Entfallene Räume ausblenden</label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="nurMitElementen"
                                           name="nurMitElementen" checked>
                                    <label class="form-check-label" for="nurMitElementen">Nur Räume mit
                                        Elementen</label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="ohneLeereElemente"
                                           name="ohneLeereElemente" checked>
                                    <label class="form-check-label" for="ohneLeereElemente">Elemente Stk=0
                                        ausblenden</label>
                                </div>
                                <!-- TODO div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="isTransposed"
                                           name="isTransposed" disabled>
                                    <label class="form-check-label" for="isTransposed" id="isTransposedLabel">Elemente
                                        als Zeilen</label>
                                </div-->
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="hideZeros" name="hideZeros">
                                    <label class="form-check-label" for="hideZeros">Nullen ausblenden</label>
                                </div>
                                <div>
                                    <button type="submit" class="btn btn-success w-100">Anzeigen</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-lg-10 mx-auto" id="tableCardCol">
                    <!-- Table card -->
                    <div class="card">
                        <div class="card-header d-inline-flex align-items-baseline" style="height: 60px;">
                            <button class="btn btn-sm btn-outline-dark fa fa-arrow-left" id="ToggleCard"></button>
                            <div class="row d-inline-flex align-items-baseline w-100 border-light">
                                <div class="col-6 d-flex" id="CardHeaderHoldingDatatableManipulators"></div>
                                <div class="col-6 d-flex justify-content-end"
                                     id="CardHeaderHoldingDatatableManipulators2"></div>
                            </div>
                            <button type="reset" class="btn btn-sm btn-outline-dark" title="ResetPivot" id="ResetPivot">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                            <span class="badge rounded-pill bg-light text-dark p-2" data-bs-toggle="popover"
                                  data-bs-content="
                                  Gelb: Nutzeranforderung.
                                  Grün: Freigegeben. ">
                              <i class="fas fa-info-circle"></i></span>
                        </div>
                        <div class="card-body p-1">
                            <div id="pivotTableContainer">
                                <!-- Die Pivot-Tabelle wird hier per AJAX geladen -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Second tab with PDF frame and vermerke -->
        <div class="tab-pane fade" id="tab2-content" role="tabpanel" aria-labelledby="tab2-tab">
            <div class="row">
                <div class="col-lg-6" id="PDFframe">
                    <div class="card" style="height: 90vh; ">
                        <div class="card-header d-flex justify-content-between">
                            <button type="reset" class="btn btn-outline-dark" title="ResetPDF" id="ResetPDF">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                            <button type="button" class="btn btn-success" id="freigebenAlleBtn">Freigeben</button>
                        </div>
                        <div class="card-body">
                            <iframe class="embed-responsive-item" id="pdfPreview"></iframe>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card mb-2">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <b>Vermerke</b>
                            <button type="button" id="buttonNewVermerk" class="btn btn-outline-success btn-sm me-2"
                                    value="Neuer Vermerk" style="visibility:hidden">
                                <i class="fas fa-plus"></i> Neu
                            </button>
                        </div>
                        <div class="card-body" id="vermerke"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<?php
include "newBesprechungModal.html";
include "openBesprechungModal.html";
include "editElementModal.html";
include "editVermerktextModal.html";
include "../../modal_elementHistory.html";
?>

<script src="../../utils/_utils.js"></script>
<script src="../js/pivotTableLoader.js"></script>
<script src="../js/editablePivot.js"></script>
<script src="../js/Besprechung.js"></script>
<script src="../js/Vermerke.js"></script>

<script>
    // let excelfilename;
    let besprechung;
    $(document).ready(function () {

        besprechung = new Besprechung({});

        besprechung.create(
            '#createMeetingForm',
            '#createMeetingModal',
            '#pdfPreview',
            makeToaster,
            updateFilterFormState
        );

        besprechung.bindModalShowHandler(
            '#besprechungSelectModal',
            '#besprechungTable',
            makeToaster,
            loadRaumbereiche
        );

        $('#meetingDatum').datepicker({
            format: "yyyy-mm-dd",
            calendarWeeks: true,
            autoclose: true,
            todayBtn: "linked",
            language: "de"
        });

        $('#raumbereich').select2({placeholder: "Raumbereich wählen"});
        $('#zusatzRaeume').select2({placeholder: "Zusätzliche Räume wählen"});
        $('#zusatzElemente').select2({placeholder: "Zusätzliche Elemente wählen"});
        $('#isTransposed').on('change', updateTransposeLabel);
        $('#filterForm :input').prop('disabled', true);
        $('#meetingDatum').val(new Date().toISOString().substring(0, 10));
        $('#createMeetingModal').on('close', function () {
            $('#createMeetingForm').reset();
        });

        $('#filterForm').on('submit', function (e) {
            e.preventDefault();
            //besprechung.consolidateMultipleElementsperRoom($('#raumbereich').val());
            loadPivotTable();              //pivotLoader.js
            addUntergruppePerRaumbereich(); //vermerke.js
            //console.log("FitlerFormSubmit: ", besprechung.toPayload());
            addDefaultVermerkeForEachRommInArea(besprechung.id, $('#raumbereich').val()); //vermerke.js
            refreshPDF();
            getVermerke();
        });

        $('#ResetBesprechung').on('click', function () {
            $('#vermerke').html('');
            $('#raumbereich').val(null).trigger('change');
            $('#zusatzRaeume').val(null).trigger('change');
            $('#zusatzElemente').val(null).trigger('change');
            $('#mtRelevant').prop('checked', true);
            $('#entfallen').prop('checked', true);
            $('#nurMitElementen').prop('checked', true);
            $('#ohneLeereElemente').prop('checked', true);
            $('#isTransposed').prop('checked', false);
            $('#hideZeros').prop('checked', false);
            $('#pivotTableContainer').empty();
            $('#pdfPreview').attr('src', '');
            besprechung.reset();
            makeToaster("Besprechung Geschlossen", true);
            updateFilterFormState();
        });

        $('#ResetPDF').on("click", function () {
            refreshPDF();//
            getVermerke();
        });
        $('#ResetPivot').on("click", function () {
            editablePivot.reloadPivotTable();
        });


        $('#ToggleCard').on('click', function () {
            $('#filterCardCol').toggle();        // toggle left card
            updateTableCardColClass();
        });

        let popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        popoverTriggerList.forEach(function (popoverTriggerEl) {
            new bootstrap.Popover(popoverTriggerEl);
        });

        $("#show-history-btn").click(function () {
            const roombookID = this.value;
            $.ajax({
                url: '../../getCommentHistory.php',
                type: 'POST',
                data: {"roombookID": roombookID},
                success(data) {

                    $('#mbodyHistory').html(data);
                },
            });
        });

        $('#freigebenAlleBtn').click(function () {
            let vermerkIDs = []; // Mit der aktuellen Besprechung alle relevanten VermerkIDs aus roomVermerkMap sammeln
            Object.values(besprechung.roomVermerkMap).forEach(ids => vermerkIDs.push(...ids));            //console.log("Handing over:", vermerkIDs);
            $.ajax({
                url: '../controllers/BesprechungController.php',
                type: 'POST',
                data: {
                    action: 'freigabeAlle',
                    vermerkIDs: vermerkIDs
                },
                success: function (response) {                    // console.log(response);
                    refreshPDF();
                    editablePivot.reloadPivotTable();
                }
            });
        });
    }); // doc ready

    function getVermerke() {
        //console.log("Getting Vermerke.");
        try {
            if ($.fn.dataTable.isDataTable('#vermerkeTable')) {
                vermerkeTable.ajax.reload();
            } else {
                vermerkeTable = $('#vermerke').html('<table id="vermerkeTable" class="table table-striped table-bordered" style="width:100%"></table>').find('table').DataTable({
                    ajax: {
                        url: "../controllers/VermerkeController.php",
                        type: "POST",
                        data: {
                            action: "getVermerkeToGruppe",
                            vermerkgruppe_id: besprechung.id
                        },
                        dataSrc: 'data'
                    },
                    columns: [
                        {title: "ID", data: "ID", visible: false},
                        {title: "R.Bez.", data: "RBZ"},
                        {
                            title: "Vermerktext",
                            data: "Vermerktext",
                            render: function (data) {
                                return data ? data.replace(/\n/g, '<br>') : '';
                            }
                        },
                        {
                            title: "Edit",
                            data: null,
                            orderable: false,
                            render: function (data, type, row) {
                                return `<button class="btn btn-sm btn-outline-primary editVermerkBtn" data-id="${row.ID}" data-text="${row.Vermerktext}"><i class="fas fa-edit"></i></button>`;
                            }
                        }
                    ],
                    responsive: true,
                    language: {
                        url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json'
                    },
                    lengthChange: false,
                    pageLength: -1,
                    searching: false,
                    info: false,
                    initComplete: function () {
                        setTimeout(() => {
                            $(document).on('click', '.editVermerkBtn', function () {
                                console.log("ataching editVermerkBtn bnt listener");
                                const id = $(this).data('id');
                                let text = $(this).data('text');
                                text = text ? text.replace(/<br\s*\/?>/gi, "\n") : '';
                                $('#editVermerkID').val(id);
                                $('#editVermerkText').val(text);
                                $('#editVermerkModal').modal('show');
                            });
                        }, 500);
                    }
                });
            }
        } catch (e) {
            console.log("GetVermerke(): ", e);
        }
    }

    function loadRaumbereiche(vermerkgruppeId) {        //console.log("ID ", vermerkgruppeId);
        $.ajax({
            url: '../controllers/VermerkuntergruppeController.php',
            method: 'POST',
            data: {action: 'getRaumbereiche', vermerkgruppe_id: vermerkgruppeId},
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    console.log(response);
                    $('#raumbereich').val(response.data).trigger('change.select2');
                } else {
                    console.error('Fehler:', response.message);
                }
            },
            error: function () {
                console.error('Ajax Fehler beim Laden der Raumbereiche');
            }
        });
    }

    function updateTableCardColClass() {
        const isTab1Active = $('#tab1-content').hasClass('show active');
        const filterVisible = isTab1Active && $('#filterCardCol').is(':visible');

        $('#tableCardCol').removeClass('col-12 col-lg-10 d-none');

        if (!isTab1Active) {
            $('#tableCardCol').addClass('d-none');
        } else {
            if (filterVisible) {
                $('#tableCardCol').addClass('col-lg-10');
            } else {
                $('#tableCardCol').addClass('col-12');
            }
        }

        const leftBtn = $('#ToggleCard');
        leftBtn.removeClass('fa-arrow-left fa-arrow-right');
        leftBtn.addClass(filterVisible ? 'fa-arrow-left' : 'fa-arrow-right');
    }


    function updateFilterFormState() {         //  console.log("updateFilterFormState: ", besprechung.id);
        if (besprechung.id > 0) {
            $('#filterForm :input').prop('disabled', false);
            $('#openMeetingBtn').prop('disabled', true);
            $('#createMeetingBtn').prop('disabled', true);
            $("#currentMeetingName").text(besprechung.name);
        } else {
            $('#filterForm :input').prop('disabled', true);
            $("#currentMeetingName").text("");
            $('#openMeetingBtn').prop('disabled', false);
            $('#createMeetingBtn').prop('disabled', false);
        }
    }

    function updateTransposeLabel() {
        $('#isTransposedLabel').text(
            $('#isTransposed').is(':checked') ? 'Räume als Zeilen' : 'Elemente als Zeilen'
        );
    }

    function refreshPDF() {
        $('#pdfPreview').attr('src', '');
        setTimeout(() => {
            $('#pdfPreview').attr('src', '../../PDFs/pdf_createVermerkGroupPDF.php?gruppenID=' + besprechung.id);
        }, 100);
    }


</script>
</body>
</html>
