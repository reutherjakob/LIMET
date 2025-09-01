<?php
include "../../utils/_utils.php";
init_page_serversides("", "x");

$projectID = $_SESSION["projectID"];
$conn = utils_connect_sql();
$raumbereichOptions = [];
$sql = "SELECT DISTINCT `Raumbereich Nutzer` FROM tabelle_räume WHERE tabelle_projekte_idTABELLE_Projekte = ?";
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


$sql = "SELECT idTABELLE_Räume AS id, 
               CONCAT(Raumnr, ' - ', Raumbezeichnung, ' - ', `Raumbereich Nutzer`) AS text
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

$sql = "SELECT tabelle_elemente.idTABELLE_Elemente as id,  
            CONCAT(ElementID,' ', Bezeichnung) as Bez
  		    FROM tabelle_elemente";

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
        .form-switch .form-check-input:checked {
            background-color: #000;
            border-color: #000;
        }

        .embed-responsive-item {
            width: 100% !important;
            height: 100% !important;
            border: none;
            display: block;
        }


    </style>
</head>

<body>

<div id="limet-navbar"></div>
<div class="container-fluid">
    <div class="row row-cols-2">

        <div class="col-lg-2 mx-auto mb-4" id="filterCardCol">
            <div class="card mb-2">
                <div class="card-header d-inline-flex align-items-center ">


                    <button type="button" class="btn btn-outline-success" id="createMeetingBtn"
                            data-bs-toggle="modal" data-bs-target="#createMeetingModal">
                        <i class="fa fa-plus me-1"></i> Neu
                    </button>
                    <button type="button" class="btn btn-outline-success" id="openMeetingBtn"
                            data-bs-toggle="modal" data-bs-target="#besprechungSelectModal">
                        <i class="fas fa-folder-open me-1"></i> Öffnen
                    </button>

                    <span id="currentMeetingName" class="ms-2 me-2 fw-bold btn-success"></span>

                    <button type="reset" class="btn btn-outline-dark" title="Reset" id="ResetBesprechung">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>

            </div>


            <div class="card mb-2">
                <form id="filterForm">
                    <div class="card-header d-flex flex-nowrap">
                        <label for="raumbereich" class="form-label"></label>
                        <select id="raumbereich" name="raumbereich[]" class="form-select" style="width:95%" multiple>
                            <?php foreach ($raumbereichOptions as $option): ?>
                                <option value="<?= htmlspecialchars($option) ?>"><?= htmlspecialchars($option) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <span class="badge rounded-pill bg-light text-dark p-2 "
                              data-bs-toggle="popover"
                              data-bs-content="Hier werden ALLE Raumbereiche des Projektes angezeigt, basierend auf der textuellen Bezeichnung Raumbereich Nutzer und unabhängig davon, ob diese z.B. MT-relevante Räume haben.">
                                <i class="fas fa-info-circle"></i>
                            </span>
                    </div>

                    <div class="card-body">
                        <div class=" d-flex flex-nowrap mb-2">
                            <select id="zusatzRaeume" name="zusatzRaeume[]" class="form-select" style="width:95%"
                                    multiple>
                                <?php foreach ($raeume as $raum): ?>
                                    <option value="<?= htmlspecialchars($raum['id'] ?? ''
                                    ) ?>">
                                        <?= htmlspecialchars($raum['text'] ?? '') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <span class="badge rounded-pill bg-light text-dark p-2  "
                                  data-bs-toggle="popover"
                                  data-bs-content="Hier werden ALLE Räume des Projektes angezeigt.
                                                    Sollten diese dann in der Tabelle fehlen, sind die gewählten Räume ggf. durch unten angeführte Filter ausgeschieden.">
                                <i class="fas fa-info-circle"></i>
                            </span>
                        </div>
                        <div class=" d-flex flex-nowrap mb-2">
                            <select id="zusatzElemente" name="zusatzElemente[]" class="form-select" style="width:95%"
                                    multiple>
                                <?php foreach ($elemente as $element): ?>
                                    <option value="<?= htmlspecialchars($element['id']) ?>">
                                        <?= htmlspecialchars($element['Bez']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <span class="badge rounded-pill bg-light text-dark p-2  "
                                  data-bs-toggle="popover"
                                  data-bs-content="Hier werden ALLE Elemente des Projektes angezeigt.
                                                    Sollten diese dann in der Tabelle fehlen, sind  ggf. Elemente Stk<1 ausgeblendet. ">
                                <i class="fas fa-info-circle"></i>
                            </span>
                        </div>
                        <div class="form-check form-switch ">
                            <input class="form-check-input btn btn-outline-dark" type="checkbox" id="mtRelevant"
                                   name="mtRelevant" checked>
                            <label class="form-check-label" for="mtRelevant">Nur MT-relevante Räume</label>
                        </div>
                        <div class="form-check  form-switch">
                            <input class="form-check-input" type="checkbox" id="entfallen" name="entfallen" checked>
                            <label class="form-check-label " for="entfallen">Entfallene Räume ausblenden</label>
                        </div>
                        <div class="form-check  form-switch">
                            <input class="form-check-input" type="checkbox" id="nurMitElementen" name="nurMitElementen"
                                   checked>
                            <label class="form-check-label" for="nurMitElementen">Nur Räume mit Elementen</label>
                        </div>
                        <div class="form-check  form-switch">
                            <input class="form-check-input" type="checkbox" id="ohneLeereElemente"
                                   name="ohneLeereElemente" checked>
                            <label class="form-check-label" for="ohneLeereElemente">
                                Elemente Stk<1 ausblenden
                            </label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="isTransposed" name="isTransposed">
                            <label class="form-check-label" for="isTransposed" id="isTransposedLabel">
                                Elemente als Zeilen
                            </label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="hideZeros" name="hideZeros">
                            <label class="form-check-label" for="hideZeros">
                                Nullen ausblenden
                            </label>
                        </div>
                        <div>
                            <button type="submit" class="btn btn-success w-100">Anzeigen</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-10 mx-auto" id="tableCardCol">
            <div class="card">
                <div class="card-header d-flex align-items-start" style=" height: 55px; ">
                    <button class="btn btn-outline-dark fa fa-arrow-left" id="ToggleCard"></button>
                    <div class="row d-inline-flex align-items-start w-100">
                        <div class=" col-6   d-flex                   align-items-start"
                             id="CardHeaderHoldingDatatableManipulators"></div>
                        <div class=" col-6 d-flex justify-content-end  align-items-start"
                             id="CardHeaderHoldingDatatableManipulators2"></div>
                    </div>
                    <button class="btn btn-outline-dark fa fa-arrow-left" id="PDFframebtn"></button>
                </div>
                <div class="card-body p-1">
                    <div id="pivotTableContainer">
                        <!-- Die Pivot-Tabelle wird hier per AJAX geladen -->
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 card-body p-1" id="PDFframe" style="display: none; height: 70vh; ">
            <iframe class="embed-responsive-item" id="pdfPreview"></iframe>
        </div>

    </div>
</div>

<?php
include "newBesprechungModal.html";
include "openBesprechungModal.html";
include "editElementModal.html";
?>

<script src="../js/Besprechung.js"></script>
<script src="../../utils/_utils.js"></script>
<script src="../js/editablePivot.js"></script>
<script>
    let besprechung = new Besprechung({});
    let excelfilename;

    function consolidateMultipleElementsperRoom() {
             let selectedRaumbereiche = $('#raumbereich').val();
             if (!selectedRaumbereiche || selectedRaumbereiche.length === 0) {
                 alert("Bitte mindestens einen Raumbereich wählen.");
                 return;
             }

        $.ajax({
            url: '../controllers/consolidateMultipleElementsperRoomsperRoomarea.php', // Pfad zum Backend-Skript für Konsolidierung
            method: 'POST',
            data: {
                raumbereiche: selectedRaumbereiche
            },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    console.log("Konsolidierung erfolgreich:", response.message);
                } else {
                    alert("Fehler bei Konsolidierung: " + (response.message || "Unbekannter Fehler"));
                }
            },
            error: function (xhr, status, error) {
                console.error("AJAX-Fehler bei Konsolidierung:", status, error);
                alert("Serverfehler bei der Konsolidierung der Elemente.");
            }
        });
    }




    $(document).ready(function () {
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

        $('#filterForm').on('submit', function (e) {
            e.preventDefault();
            consolidateMultipleElementsperRoom();
            loadPivotTable();
            addUntergruppePerRaumbereich();
            addDefaultVermerkeForRaumbereiche(besprechung.id, $('#raumbereich').val());
            $('#pdfPreview').attr('src', '../../PDFs/pdf_createVermerkGroupPDF.php?gruppenID=' + besprechung.id);
        });

        $('#createMeetingForm').on('submit', function (e) {
                e.preventDefault();
                besprechung.id = 1;
                besprechung.action = "new";
                besprechung.name = $("#meetingName").val();
                besprechung.datum = $("#meetingDatum").val();
                besprechung.startzeit = $("#meetingUhrzeitStart").val();
                besprechung.endzeit = $("#meetingUhrzeitEnde").val();
                besprechung.ort = $("#meetingOrt").val();
                besprechung.verfasser = $("#meetingVerfasser").val();
                besprechung.art = "Protokoll Besprechung";

                if (besprechung.name && besprechung.verfasser && besprechung.datum && besprechung.startzeit) {
                    $.ajax({
                        url: "../controllers/BesprechungController.php", // Controller endpoint
                        type: "POST",                  // Change from GET to POST
                        data: besprechung.toPayload(),// Send data in POST body
                        success: function (response) {
                            if (response.success) {
                                besprechung.id = response.insertId;
                                console.log("Besprechung angelegt", besprechung.toPayload());

                                $('#createMeetingModal').modal('hide');
                                $('#createMeetingForm')[0].reset();
                                $('#pdfPreview').attr('src', '../../PDFs/pdf_createVermerkGroupPDF.php?gruppenID=' + besprechung.id);

                                makeToaster("Besprechung erfolgreich angelegt! - ID:" + besprechung.id, true);
                                updateFilterFormState();
                            } else {
                                makeToaster("Fehler: " + (response.message || "Unbekannter Fehler"), false);
                            }
                        },
                        error: function (xhr) {
                            const errorMsg = xhr.responseJSON?.errors?.join(', ') || xhr.responseText || "Fehler beim Anlegen";
                            alert(errorMsg);
                        }
                    });
                } else {
                    makeToaster("Bitte alle Pflichtfelder ausfüllen!", false);
                }
            }
        );

        $('#besprechungSelectModal').on('shown.bs.modal', function (e) {
            if ($.fn.DataTable.isDataTable('#besprechungTable')) {
                $('#besprechungTable').DataTable().destroy();
            }
            let table = $('#besprechungTable').DataTable({
                ajax: {
                    url: '../controllers/BesprechungController.php',
                    type: 'POST',
                    data: {action: 'getProtokollBesprechungen'},
                    dataSrc: function (json) {
                        if (!json.success) {
                            $('#besprechungLoading').text('Fehler: ' + json.message);
                            return [];
                        }
                        $('#besprechungLoading').text('');
                        return json.data;
                    },
                    error: function (xhr, error, thrown) {
                        $('#besprechungLoading').text('Serverfehler: ' + thrown);
                    }
                },
                columns: [
                    {data: 'idtabelle_Vermerkgruppe', title: "id", visible: false},
                    {data: 'Gruppenname', title: "Name"},
                    {data: 'Gruppenart', title: "Art"},
                    {data: 'Ort', title: "Ort"},
                    {data: 'Verfasser', title: "Verfasser"},
                    {data: 'Startzeit', title: "Startzeit"},
                    {data: 'Endzeit', title: "Endzeit"},
                    {data: 'Datum', title: "Datum"}
                ],
                searching: true,
                paging: true,
                info: false,
                lengthChange: false,
                language: {url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json'},
                rowId: 'id',
                createdRow: function (row, data) {
                    $(row).off('click').on('click', function () {
                        $('#besprechungTable tbody tr').removeClass('selected');
                        $(this).addClass('selected');

                        besprechung.id = data.idtabelle_Vermerkgruppe;
                        besprechung.action = "opened";
                        besprechung.name = data.Gruppenname;
                        besprechung.datum = data.Datum;
                        besprechung.startzeit = data.Startzeit;
                        besprechung.endzeit = data.Endzeit;
                        besprechung.ort = data.Ort;
                        besprechung.verfasser = data.Verfasser;
                        besprechung.art = "Protokoll Besprechung";
                        besprechung.projektID = data.tabelle_projekte_idTABELLE_Projekte;

                        console.log("Raw: ", data);
                        console.log(besprechung.toPayload());

                        $('#pdfPreview').attr('src', '../../PDFs/pdf_createVermerkGroupPDF.php?gruppenID=' + data.idtabelle_Vermerkgruppe);

                        setTimeout(() => {
                            updateFilterFormState();
                            $('#besprechungSelectModal').modal("hide");
                            $('#besprechungTable').DataTable().destroy();
                            makeToaster("Besprechung geöffnet " + besprechung.id, true);
                            loadRaumbereiche(besprechung.id);
                        }, 100);

                    });
                }
            });
        });

        $('#ResetBesprechung').on('click', function () {
            $('#filterForm')[0].reset();
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
            besprechung = new Besprechung({});
            updateFilterFormState();
            setTimeout(() => {
                makeToaster("Besprechung Geschlossen", true);
                updateFilterFormState();
            }, 100)

        });

        $('#PDFframebtn').on('click', function () {
            $('#PDFframe').toggle();             // toggle right PDF card
            updateTableCardColClass();
        });

        $('#ToggleCard').on('click', function () {
            $('#filterCardCol').toggle();        // toggle left card
            updateTableCardColClass();
        });

        let popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        popoverTriggerList.forEach(function (popoverTriggerEl) {
            new bootstrap.Popover(popoverTriggerEl);
        });

    }); // doc ready


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
        const filterVisible = $('#filterCardCol').is(':visible');
        const PDFVisible = $('#PDFframe').is(':visible');
        $('#tableCardCol').removeClass('col-12 col-lg-10 col-lg-8 col-lg-6');
        if (!filterVisible && !PDFVisible) {
            $('#tableCardCol').addClass('col-12');
        } else if (filterVisible && !PDFVisible) {
            $('#tableCardCol').addClass('col-lg-10');
        } else if (!filterVisible && PDFVisible) {
            $('#tableCardCol').addClass('col-lg-8');
        } else if (filterVisible && PDFVisible) {
            $('#tableCardCol').addClass('col-lg-6');
        }
        const leftBtn = $('#ToggleCard');
        leftBtn.removeClass('fa-arrow-left fa-arrow-right');
        leftBtn.addClass(filterVisible ? 'fa-arrow-left' : 'fa-arrow-right');
        const rightBtn = $('#PDFframebtn');
        rightBtn.removeClass('fa-arrow-left fa-arrow-right');
        rightBtn.addClass(PDFVisible ? 'fa-arrow-right' : 'fa-arrow-left');
    }

    function addUntergruppePerRaumbereich() {
        const selectedRaumbereiche = $('#raumbereich').val();
        if (!selectedRaumbereiche || selectedRaumbereiche.length === 0 || besprechung.id === 0) return;
        console.log("Besprechung ist geöffnet", selectedRaumbereiche, besprechung.id);
        $.ajax({
            url: '../controllers/VermerkuntergruppeController.php',
            method: 'POST',
            data: {
                vermerkgruppe_id: besprechung.id, // pass only vermerkgruppe ID as needed
                raumbereiche: selectedRaumbereiche,  // array of names
                action: "addUntergruppen"
            },
            success: function (response) {
                if (response.success) {
                    if (response.created.length > 0) {
                        makeToaster("Neue Untergruppe(n) erstellt: " + response.created.map(c => c.name).join(", "), true);
                    }
                    if (response.skipped.length > 0) {
                        if (response.created.length === 0) {
                            makeToaster("Gruppe(n) '" + response.skipped.join(", ") + "' existiert/ieren bereits. Erstelle keine Duplikate.", true);
                        } else {
                            console.log("Einige Gruppen existierten bereits und wurden nicht dupliziert:", response.skipped);
                        }
                    }
                } else {
                    alert("Fehler beim Erstellen der Untergruppe: " + (response.message || "Unbekannter Fehler"));
                }
            },
            error: function () {
                alert("Serverfehler bei der Untergruppenerstellung.");
            }
        });
    }

    function addDefaultVermerkeForRaumbereiche(vermerkgruppeId, raumbereiche) {
        if (!besprechung.id || !Array.isArray(raumbereiche) || raumbereiche.length === 0) {
            makeToaster("Bitte Vermerkgruppe und mindestens einen Raumbereich wählen .  " + besprechung.id + "   " + raumbereiche, true);
            return;
        }

        $.ajax({
            url: '../controllers/createVermerkeForEachRoom.php',
            method: 'POST',
            data: {
                vermerkgruppe_id: besprechung.id,
                raumbereiche: raumbereiche
            },
            success: function (response) {
                if (response.success) {
                    if (response.addedVermerke.length > 0) {
                        alert("Vermerke wurden für Raumbereiche erstellt:\n" +
                            [...new Set(response.addedVermerke.map(v => v.raumbereich))].join(", "));
                    }
                    if (response.errors.length > 0) {
                        console.warn("Fehler:", response.errors.join("\n"));
                    }
                } else {
                    alert("Fehler: " + (response.message || "Unbekannter Fehler"));
                }
            },
            error: function () {
                alert("Serverfehler beim Erstellen der Vermerke.");
            }
        });
    }

    function updateFilterFormState() {
        if (typeof besprechung === "object" && besprechung !== null && besprechung.id && besprechung.id > 0) {
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

    function table_click() {
        $('#pivotTable').off('click', 'td').on('click', 'td', function () {
            const cell = $(this);
            const table = $('#pivotTable').DataTable();            // DataTable cell/row/col index
            const cellIdx = table.cell(this).index();            // Row and column indices (zero-based)
            const rowIdx = cellIdx.row;
            const colIdx = cellIdx.column;            // Get raw data for this row and column
            const cellData = table.cell(cell).data();
            const rowData = table.row(rowIdx).data();            // Get the header text for this column
            const headerText = $(table.column(colIdx).header()).text().trim();

            console.log('Cell Value:', cellData);
            console.log('Column:', colIdx, '(', headerText, ')');
            console.log('Row:', rowIdx, rowData);

            const dataRoomId = cell.data('roomid');
            const dataElementId = cell.data('elementid');
            const idTABELLE_Räume_has_tabelle_Elemente = cell.data('roomhaselementid')

            if (dataRoomId) console.log('Room ID:', dataRoomId);
            if (dataElementId) console.log('Element ID:', dataElementId);
            if (idTABELLE_Räume_has_tabelle_Elemente) console.log('Element ID:', idTABELLE_Räume_has_tabelle_Elemente); //TODO validate
        });


    }

    function updateTransposeLabel() {
        $('#isTransposedLabel').text(
            $('#isTransposed').is(':checked') ? 'Räume als Zeilen' : 'Elemente als Zeilen'
        );
    }

    function loadPivotTable() {
        let raumbereich = $('#raumbereich').val(); // This is now an array
        let mtRelevant = $('#mtRelevant').is(':checked') ? 1 : 0;
        let entfallen = $('#entfallen').is(':checked') ? 1 : 0;
        let nurMitElementen = $('#nurMitElementen').is(':checked') ? 1 : 0;
        let ohneLeereElemente = $('#ohneLeereElemente').is(':checked') ? 1 : 0;
        let transponiert = $('#isTransposed').is(':checked') ? 1 : 0;
        if (!raumbereich || raumbereich.length === 0) {
            $('#pivotTableContainer').html('<div class="alert alert-info">Bitte wählen Sie mindestens einen Raumbereich.</div>');
            return;
        }
        let hideZeros = $('#hideZeros').is(':checked');
        let zusatzRaeume = $('#zusatzRaeume').val();
        let zusatzElemente = $('#zusatzElemente').val();

        $.ajax({
            url: '../controllers/PivotTableController.php',
            method: 'POST',
            data: {
                'raumbereich[]': raumbereich, // This will be sent as an array
                'zusatzRaeume[]': zusatzRaeume,
                'zusatzElemente[]': zusatzElemente,
                mtRelevant,
                entfallen,
                nurMitElementen,
                ohneLeereElemente,
                transponiert
            },
            traditional: true,
            success: function (data) {
                //let raumbereichJoined = raumbereich
                //    .map(r => r.replace(/ /g, '_'))
                //    .join('_');
                //  getExcelFilename('Elemente-je-Raumbereich_' + raumbereichJoined)    // so that the datatable is initited with the correct filename
                //   .then(filename => {
                $('#pivotTableContainer').html(data);
                let colCount = $('#pivotTable thead th').length;
                let columns = [];
                for (let i = 0; i < colCount; i++) {
                    if (i === 0) {   // First column: Element or Raum, don't change rendering
                        columns.push(null);
                    } else if (hideZeros) {
                        columns.push({              // For all other columns, hide zeros
                            render: function (data) {
                                return (data === "0" || data === 0) ? "" : data;
                            }
                        });
                    } else {
                        columns.push(null);
                    }
                }

                $('#pivotTable').DataTable({
                    language: {
                        url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json",
                        search: "",
                        searchPlaceholder: "Suche...",
                        lengthMenu: '_MENU_ ',
                        info: "_START_-_END_ von _TOTAL_ ",
                        infoEmpty: "Keine Einträge",
                        infoFiltered: "(von _MAX_) ",
                    },
                    scrollX: true,
                    scrollCollapse: true,
                    fixedColumns: {start: 1},
                    fixedHeader: true,
                    select: true,

                    paging: true,
                    pagingType: "full",

                    searching: true,
                    ordering: true,
                    info: true,
                    lengthChange: true,
                    pageLength: 10,
                    lengthMenu: [[10, 20, 50, -1], ['10 rows', '20 rows', '50 rows', 'All']],
                    responsive: false,
                    autoWidth: true,
                    columns: columns,
                    layout: {
                        topStart: 'buttons',
                        topEnd: 'search',
                        bottomStart: 'info',
                        bottomEnd: ['pageLength', 'paging']
                    },
                    buttons: [
                        {
                            extend: 'excelHtml5',
                            text: '<i class="fas fa-file-excel"></i> Excel',
                            className: 'btn btn-success btn-sm',
                            title: "whatever" //filename
                        }
                    ],
                    initComplete: function () {
                        $('#CardHeaderHoldingDatatableManipulators').empty();
                        $('#CardHeaderHoldingDatatableManipulators2').empty();
                        $('#pivotTable_wrapper .dt-buttons').appendTo('#CardHeaderHoldingDatatableManipulators');
                        $('#pivotTable_wrapper .dt-search').appendTo('#CardHeaderHoldingDatatableManipulators');
                        $('#pivotTable_wrapper .dt-length').appendTo('#CardHeaderHoldingDatatableManipulators2');
                        $('#pivotTable_wrapper .dt-info').addClass("btn btn-sm").appendTo('#CardHeaderHoldingDatatableManipulators2');
                        $('#pivotTable_wrapper .dt-paging').addClass("btn btn-sm").appendTo('#CardHeaderHoldingDatatableManipulators2');
                        $('.dt-search label').remove();
                        $('.dt-search').children().removeClass("form-control form-control-sm").addClass("btn btn-sm btn-outline-dark");
                        table_click();
                    }
                });
                //})´.            catch                (error => {
                //    console.error('Failed to generate filename:', error);
                // });
            }
        });
    }



    //   <!-- div id="raumListe" class=" card mt-3 col-2">
    //                <div class="card-body">
    //                    <button class="btn btn-primary" onclick="loadRoomsByRaumbereiche()">Räume laden</button>
    //                </div>
    //            </div-->
    //
    // function loadRoomsByRaumbereiche() {
    //     let selectedRaumbereiche = $('#raumbereich').val();
    //     if (!selectedRaumbereiche || selectedRaumbereiche.length === 0) {
    //         alert("Bitte mindestens einen Raumbereich wählen.");
    //         return;
    //     }
    //
    //     $.ajax({
    //         url: '../controllers/getRoomsByRaumbereiche.php',  // Pfad zum Backend-Skript
    //         method: 'POST',
    //         data: { raumbereiche: selectedRaumbereiche },
    //         dataType: 'json',
    //         success: function(response) {
    //             if (response.success) {
    //                 console.log("Gefundene Räume:", response.data);
    //
    //                 // Beispielausgabe in einer Tabelle unter einem Container mit id="raumListe"
    //                 let html = '<table class="table table-sm table-bordered"><thead><tr><th>ID</th><th>Raumnr</th><th>Bezeichnung</th><th>Raumbereich</th></tr></thead><tbody>';
    //                 response.data.forEach(room => {
    //                     html += `<tr>
    //                             <td>${room.id}</td>
    //                             <td>${room.Raumnr}</td>
    //                             <td>${room.Raumbezeichnung}</td>
    //                             <td>${room["Raumbereich Nutzer"]}</td>
    //                          </tr>`;
    //                 });
    //                 html += '</tbody></table>';
    //                 $('#raumListe').html(html);
    //
    //             } else {
    //                 alert("Fehler: " + (response.message || "Keine Räume gefunden"));
    //             }
    //         },
    //         error: function(xhr, status, error) {
    //             alert("Serverfehler: " + error);
    //         }
    //     });
    // }





</script>
</body>
</html>
