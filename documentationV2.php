<!-- 13.2.25: Reworked -->
<?php
require_once 'utils/_utils.php';
init_page_serversides();
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>RB-Dokumentation</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen"/>
    <link rel="icon" href="Logo/iphone_favicon.png">

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css"
          integrity="sha512-q3eWabyZPc1XTCmF+8/LuE1ozpg5xxn7iO89yfSOd5/oKvyqLngoNGsx8jq92Y8eXJ/IRxQbEC+FGSYxtk2oiw=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.1/css/dataTables.dataTables.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.2.1/af-2.7.0/b-3.2.1/b-colvis-3.2.1/b-html5-3.2.1/b-print-3.2.1/cr-2.0.4/date-1.5.5/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.1/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-3.0.0/sr-1.4.1/datatables.min.css"
          rel="stylesheet">
    <script src="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.2.1/af-2.7.0/b-3.2.1/b-colvis-3.2.1/b-html5-3.2.1/b-print-3.2.1/cr-2.0.4/date-1.5.5/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.1/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-3.0.0/sr-1.4.1/datatables.min.js"></script>


    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>

    <!--DATEPICKER -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/css/bootstrap-datepicker3.min.css">
    <script type='text/javascript'
            src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/js/bootstrap-datepicker.min.js"></script>

    <style>
        .card-body {
            overflow: auto;
        }

        .card-body iframe {
            top: 0;
            left: 0;
            width: 100%;
            height: 85vh;
        }

    </style>
</head>
<body>
<div class="container-fluid bg-light">
    <div id="limet-navbar"></div>
    <div class="row">
        <div class="col-xxl-8">
            <div class="mt-1 card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-7">
                            <b>Vermerkgruppen</b></div>
                        <div class="col-5 d-inline-flex justify-content-end align-items-center"
                             id="CardHeaderVermerkGruppen">
                            <button type="button" class="btn btn-sm btn-outline-dark me-2" value="searchDocumentation"
                                    data-bs-toggle="modal" data-bs-target="#showSearchModal"><i
                                        class="fas fa-search"></i>
                                Vermerke durchsuchen
                            </button>
                            <button type='button' id='<?php echo $_SESSION["projectID"] ?>'
                                    class='btn btn-sm btn-outline-success me-2' value='Neue Vermerkgruppe'><i
                                        class='fas fa-plus'></i> Neu
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <?php
                    $mysqli = utils_connect_sql();
                    $projectID = $_SESSION["projectID"];

                    $sql = "SELECT 
            tabelle_Vermerkgruppe.Gruppenname, 
            tabelle_Vermerkgruppe.Gruppenart, 
            tabelle_Vermerkgruppe.Ort, 
            DATE_FORMAT(tabelle_Vermerkgruppe.Startzeit, '%h:%i') AS Startzeit, 
            DATE_FORMAT(tabelle_Vermerkgruppe.Endzeit, '%h:%i') AS Endzeit, 
            tabelle_Vermerkgruppe.Datum, 
            tabelle_Vermerkgruppe.idtabelle_Vermerkgruppe, 
            tabelle_Vermerkgruppe.Verfasser
        FROM tabelle_Vermerkgruppe
        WHERE tabelle_Vermerkgruppe.tabelle_projekte_idTABELLE_Projekte = ?
        ORDER BY tabelle_Vermerkgruppe.Datum DESC";

                    $stmt = $mysqli->prepare($sql);
                    $stmt->bind_param("i", $projectID);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    echo "<table class='table responsive compact table-striped table-bordered table-sm table-hover  border border-light border-5' id='tableVermerkGruppe'>
                                                        <thead><tr>
                                                        <th>ID</th>
                                                        <th></th>
                                                        <th>Name</th>
                                                        <th>Datum</th>
                                                        <th>Art-hidden</th>
                                                        <th>Art</th>
                                                        <th>Ort</th>
                                                        <th>Verfasser</th>
                                                        <th></th>
                                                        <th></th>
                                                        <th>Start</th>
                                                        <th>Ende</th>
                                                        </tr></thead><tbody>";
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['idtabelle_Vermerkgruppe'] . "</td>";
                        echo "<td><button type='button' id='" . $row['idtabelle_Vermerkgruppe'] . "' class='btn btn-outline-dark btn-sm' value='changeVermerkgruppe'><i class='fas fa-pencil-alt'></i></button></td>";
                        echo "<td>" . $row['Gruppenname'] . "</td>";
                        echo "<td>" . $row['Datum'] . "</td>";
                        echo "<td>" . $row['Gruppenart'] . "</td>";
                        echo "<td align='center'>";
                        echo match ($row["Gruppenart"]) {
                            "Mailverkehr" => "<span class='badge rounded-pill bg-info'> Mailverkehr </span>",
                            "Telefonnotiz" => "<span class='badge rounded-pill bg-dark'> Telefonnotiz </span>",
                            "AV" => "<span class='badge rounded-pill bg-warning text-dark'> AV </span>",
                            "Protokoll" => "<span class='badge rounded-pill bg-primary'> Protokoll </span>",
                            "ÖBA-Protokoll" => "<span class='badge rounded-pill bg-success'> ÖBA-Protokoll </span>",
                            "Protokoll Besprechung" => "<span class='badge rounded-pill bg-secondary'> Protokoll Besprechung</span>",
                            default => $row['Gruppenart'],
                        };
                        echo "</td>";
                        echo "<td>" . $row['Ort'] . "</td>";
                        echo "<td>" . $row['Verfasser'] . "</td>";
                        echo "<td><button type='button' id='" . $row['idtabelle_Vermerkgruppe'] . "' class='btn btn-outline-dark btn-sm' value='showGroupMembers'  data-bs-toggle='modal' data-bs-target='#showGroupMembersModal'><i class='fas fa-users'></i></button></td>";
                        echo "<td><button type='button' id='" . $row['idtabelle_Vermerkgruppe'] . "' class='btn btn-outline-dark btn-sm' value='createGroupPDF' onclick='myFunction()'><i class='fas fa-file-pdf'></i></button></td>";
                        echo "<td>" . $row['Startzeit'] . "</td>";
                        echo "<td>" . $row['Endzeit'] . "</td>";
                        echo "</tr>";
                    }
                    echo "</tbody></table>";
                    ?>
                </div>
            </div>

            <div class="mt-1 card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-xxl-6">
                            <b>Vermerk-Untergruppen</b>
                        </div>
                        <div class="col-xxl-6 d-flex justify-content-end" id="CardHeaderVermerUntergruppen">
                            <button type='button' id='buttonNewVermerkuntergruppe'
                                    class='btn  btn-sm btn-outline-success me-2'
                                    value='Neue Vermerkuntergruppe' style='visibility:hidden'><i
                                        class='fas fa-plus'></i>Neu
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body" id="vermerkUntergruppen"></div>
            </div>

            <div class="mt-1 card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-xxl-6">
                            <b>Vermerke</b>
                        </div>
                        <div class="col-xxl-6 d-flex justify-content-end align-items-center" id="CardHeaderVermerkE">
                            <button type='button' id='buttonNewVermerk' class='btn btn-outline-success btn-sm me-2'
                                    value='Neuer Vermerk' style='visibility:hidden'><i class='fas fa-plus'></i>
                                Neu
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body" id="vermerke"></div>
            </div>
        </div>


        <!-- div class="mt-1 card">    TODO - Bilder
            <div class="card-header"><b>Bilder</b>
                <label class="float-right" id="divImagesRightLabel">
                    <button type='button' id='addImage' class='btn btn-outline-success btn-sm'
                            value='Bild hinzufügen' style='visibility:hidden'><i class='fas fa-plus'></i> Bild
                        hinzufügen
                    </button>
                </label>
            </div>
            <div class="card-body" id="images"><img id="images_cb"></div>
        </div-->

        <!-- Darstellung PDF -->
        <div class="col-xxl-4">
            <div class="mt-1 card">
                <div class="card-header">Vorschau-PDF</div>
                <div class="card-body embed-responsive embed-responsive-3by2">
                    <iframe class="embed-responsive-item" id="pdfPreview"></iframe>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal zum HinzufÃ¼gen/Ã„ndern einer Gruppe -->
    <div class='modal fade' id='changeGroupModal' role='dialog' tabindex="-1">
        <div class='modal-dialog modal-md'>
            <!-- Modal content-->
            <div class='modal-content'>
                <div class='modal-header'>
                    <h4 class='modal-title'>Gruppendaten</h4>
                    <button type='button' class='close' data-bs-dismiss='modal'>&times;</button>

                </div>
                <div class='modal-body' id='mbody'>
                    <form role="form">
                        <div class="form-group">
                            <label for="gruppenart">Art:</label>
                            <select class='form-control form-control-sm' id='gruppenart' name='gruppenart'>
                                <option value="Mailverkehr">Mailverkehr</option>
                                <option value="Telefonnotiz">Telefonnotiz</option>
                                <option value="AV">AV</option>
                                <option value="Protokoll">Protokoll</option>
                                <option value="Protokoll Besprechung">Protokoll Besprechung</option>
                                <option value="ÖBA-Protokoll">ÖBA-Protokoll</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="gruppenName">Name:</label>
                            <input type="text" class="form-control form-control-sm" id="gruppenName"/>
                        </div>
                        <div class="form-group">
                            <label for="gruppenOrt">Ort:</label>
                            <input type="text" class="form-control form-control-sm" id="gruppenOrt"/>
                        </div>
                        <div class="form-group">
                            <label for="gruppenVerfasser">Verfasser:</label>
                            <input type="text" class="form-control form-control-sm" id="gruppenVerfasser"/>
                        </div>
                        <div class="form-group">
                            <label for="gruppenDatum">Datum:</label>
                            <input type="text" class="form-control form-control-sm" id="gruppenDatum"
                                   placeholder="jjjj.mm.tt"/>
                        </div>
                        <div class="row">
                            <div class="form-group col-6">
                                <label for="gruppenStart">Startzeit:</label>
                                <input type="time" class="form-control form-control-sm" id="gruppenStart"/>
                            </div>
                            <div class="form-group col-6">
                                <label for="gruppenEnde">Endzeit:</label>
                                <input type="time" class="form-control form-control-sm" id="gruppenEnde"/>
                            </div>
                        </div>
                </div>
                <div class='modal-footer'>
                    <input type='button' id='addGroup' class='btn btn-success btn-sm' value='Erstellen'
                           data-bs-dismiss='modal'>
                    <input type='button' id='saveGroup' class='btn btn-warning btn-sm' value='Speichern'
                           data-bs-dismiss='modal'>
                    <button type='button' class='btn btn-default btn-sm' data-bs-dismiss='modal'>Abbrechen
                    </button>
                </div>
            </div>

        </div>
    </div>


    <!-- Modal fÃ¼r Gruppenmitglieder -->
    <div class='modal fade' id='showGroupMembersModal' role='dialog' tabindex="-1">
        <div class='modal-dialog modal-lg'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <h4 class='modal-title'>Teilnehmer:</h4>
                    <button type='button' class='close' data-bs-dismiss='modal'>&times;</button>
                </div>
                <div class='modal-body' id='showGroupMembersModalBody'>
                    <div class="mt-1 card">
                        <div class="card-header">Teilnehmer:</div>
                        <div class="card-body" id='vermerkGroupMembers'>
                        </div>
                    </div>
                    <hr></hr>
                    <div class="mt-1 card">
                        <div class="card-header">Mögliche Teilnehmer:</div>
                        <div class="card-body" id='possibleVermerkGroupMembers'>
                        </div>
                    </div>
                </div>
                <div class='modal-footer'>
                    <button type='button' class='btn btn-default btn-sm' value='closeModal'
                            data-bs-dismiss='modal'>
                        Schließen
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Vermerk-Suche -->
    <div class='modal fade' id='showSearchModal' role='dialog' tabindex="-1">
        <div class='modal-dialog modal-lg'>
            <!-- Modal content-->
            <div class='modal-content'>
                <div class='modal-header'>
                    <h4 class='modal-title'>Vermerke durchsuchen:</h4>
                    <button type='button' class='close' data-bs-dismiss='modal'>&times;</button>
                </div>
                <div class='modal-body' id='showSearchModalBody'>
                    <?php
                    $sql = "SELECT tabelle_Vermerkgruppe.Gruppenname,
                        tabelle_Vermerkgruppe.Datum,
                        tabelle_Vermerkuntergruppe.Untergruppennummer,
                        tabelle_Vermerkuntergruppe.Untergruppenname,
                        tabelle_Vermerke.Vermerktext,
                        tabelle_Vermerke.idtabelle_Vermerke,
                        tabelle_räume.Raumnr,
                        tabelle_räume.Raumbezeichnung,
                        tabelle_lose_extern.LosNr_Extern,
                        tabelle_lose_extern.LosBezeichnung_Extern
                        FROM tabelle_lose_extern
                        RIGHT JOIN (
                        tabelle_räume
                        RIGHT JOIN (
                        (
                        tabelle_Vermerkgruppe
                        INNER JOIN tabelle_Vermerkuntergruppe
                        ON tabelle_Vermerkgruppe.idtabelle_Vermerkgruppe = tabelle_Vermerkuntergruppe.tabelle_Vermerkgruppe_idtabelle_Vermerkgruppe
                        )
                        INNER JOIN tabelle_Vermerke
                        ON tabelle_Vermerkuntergruppe.idtabelle_Vermerkuntergruppe = tabelle_Vermerke.tabelle_Vermerkuntergruppe_idtabelle_Vermerkuntergruppe
                        )
                        ON tabelle_räume.idTABELLE_Räume = tabelle_Vermerke.tabelle_räume_idTABELLE_Räume
                        )
                        ON tabelle_lose_extern.idtabelle_Lose_Extern = tabelle_Vermerke.tabelle_lose_extern_idtabelle_Lose_Extern
                        WHERE tabelle_Vermerkgruppe.tabelle_projekte_idTABELLE_Projekte = ?
                        ORDER BY tabelle_Vermerkgruppe.Datum DESC";
                    $stmt = $mysqli->prepare($sql);
                    $stmt->bind_param("i", $projectID);
                    $stmt->execute();
                    $result = $stmt->get_result();


                    echo "<table class='table table-striped table-bordered table-sm table-hover border border-light border-5' id='tableSearchVermerk'>
                                            <thead><tr>
                                            <th>ID</th>
                                            <th>Gruppe</th>
                                            <th>Datum</th>
                                            <th>Untergruppe</th>
                                            <th>Vermerk</th>
                                            <th>Raum</th>
                                            <th>Los</th>                                            
                                            </tr></thead><tbody>";

                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['idtabelle_Vermerke'] . "</td>";
                        echo "<td>" . $row['Gruppenname'] . "</td>";
                        echo "<td>" . $row['Datum'] . "</td>";
                        echo "<td>" . $row['Untergruppennummer'] . " " . $row['Untergruppenname'] . "</td>";
                        echo "<td>" . $row['Vermerktext'] . "</td>";
                        echo "<td>" . $row['Raumnr'] . "</td>";
                        echo "<td>" . $row['LosNr_Extern'] . "</td>";
                        echo "</tr>";
                    }
                    echo "</tbody></table>";
                    $mysqli->close();
                    ?>
                </div>
            </div>
            <div class='modal-footer'>
                <button type='button' class='btn btn-default btn-sm' value='closeModal' data-bs-dismiss='modal'>
                    Schließen
                </button>
            </div>
        </div>
    </div>
</div>


<!--suppress ES6ConvertVarToLetConst -->
<script>
    //       for getVermerkToUntergruppe.php
    var tableVermerkGruppe, gruppenID, vermerkID, tableVermerke, untergruppenID, vermerkGruppenID; //  for getVermerkUntergruppeToGruppe.php

    $(document).ready(function () {
        tableVermerkGruppe = new DataTable('#tableVermerkGruppe', {
            columnDefs: [
                {
                    targets: [0, 4, 10, 11],
                    visible: false,
                    searchable: false,
                    sortable: false
                }
            ],
            select: true,
            paging: true,
            searching: true,
            info: true,
            order: [[3, "desc"]],
            pagingType: "simple",
            lengthChange: false,
            pageLength: 10,
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json",
                search: "",
                searchPlaceholder: "Suche..."
            },
            mark: true,
            initComplete: function () {
                $('.dt-search label').remove();
                $('.dt-search').children().removeClass("form-control form-control-sm").addClass("btn btn-sm btn-outline-dark").appendTo('#CardHeaderVermerkGruppen');
            }
        });

        setTimeout(function () {
            new DataTable('#tableSearchVermerk', {
                columnDefs: [
                    {
                        targets: [0],
                        visible: false,
                        searchable: false,
                        sortable: false
                    }
                ],
                select: false,
                paging: true,
                searching: true,
                info: true,
                order: [[2, "desc"]],
                pagingType: "simple",
                lengthChange: false,
                pageLength: 10,
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json",
                    search: ""
                },
                mark: true,
                layout: {
                    topEnd: "search",
                    topStart: null,
                    bottomStart: "info",
                    bottomEnd: "paging",
                }
            });
        }, 500);

        $('#tableVermerkGruppe tbody').on('click', 'tr', function () {
            gruppenID = tableVermerkGruppe.row($(this)).data()[0];
            let art = tableVermerkGruppe.row($(this)).data()[4];
            document.getElementById("buttonNewVermerkuntergruppe").disabled = art === "Protokoll Besprechung";
            document.getElementById("gruppenart").value = art;
            document.getElementById("gruppenName").value = tableVermerkGruppe.row($(this)).data()[2];
            document.getElementById("gruppenOrt").value = tableVermerkGruppe.row($(this)).data()[6];
            document.getElementById("gruppenVerfasser").value = tableVermerkGruppe.row($(this)).data()[7];
            document.getElementById("gruppenDatum").value = tableVermerkGruppe.row($(this)).data()[3];
            document.getElementById("gruppenStart").value = tableVermerkGruppe.row($(this)).data()[10];
            document.getElementById("gruppenEnde").value = tableVermerkGruppe.row($(this)).data()[11];
            $("#vermerke").hide();

            $.ajax({
                url: "getVermerkeuntergruppenToGruppe.php",
                data: {
                    "vermerkGruppenID": gruppenID,
                    "art": art
                },
                type: "POST",
                success: function (data) {
                    $("#vermerkUntergruppen").html(data);
                }
            });
            $('#pdfPreview').attr('src', 'PDFs/pdf_createVermerkGroupPDF.php?gruppenID=' + gruppenID);

        });
    });

    $('#gruppenDatum').datepicker({
        format: "yyyy-mm-dd",
        calendarWeeks: true,
        autoclose: true,
        todayBtn: "linked",
        language: "de"
    });

    new DataTable('#tablePossibleGroupMembers', {
        paging: false,
        searching: true,
        info: false,
        order: [[1, "asc"]],
        columnDefs: [
            {
                targets: [0],
                visible: true,
                searchable: false
            }
        ],
        language: {url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json"},
        scrollY: '20vh',
        scrollCollapse: true
    });

    $("button[value='showGroupMembers']").click(function () {
        var id = this.id;
        $.ajax({
            url: "getVermerkgruppenMembers.php",
            type: "POST",
            data: {"gruppenID": id},
            success: function (data) {
                $("#vermerkGroupMembers").html(data);
                $.ajax({
                    url: "getPossibleVermerkGruppenMembers.php",
                    type: "POST",
                    data: {"gruppenID": id},
                    success: function (data) {
                        $("#possibleVermerkGroupMembers").html(data);
                    }
                });
            }
        });
    });

    $("button[value='changeVermerkgruppe']").click(function () {
        // Buttons ein/ausblenden!
        document.getElementById("saveGroup").style.display = "inline";
        document.getElementById("addGroup").style.display = "none";
        $('#changeGroupModal').modal('show');
    });

    $("button[value='Neue Vermerkgruppe']").click(function () {
        resetChangeGroupModalFields();
        document.getElementById("saveGroup").style.display = "none";
        document.getElementById("addGroup").style.display = "inline";
        $('#changeGroupModal').modal('show');
    });

    function resetChangeGroupModalFields() {
        const modal = document.getElementById('changeGroupModal');
        if (!modal) return;
        modal.querySelectorAll('input.form-control, select.form-control').forEach(input => {
            if (input.tagName.toLowerCase() === 'select') {
                input.selectedIndex = 0; // reset select dropdown to first option
            } else {
                input.value = ''; // clear text input
            }
        });
    }

    $("button[value='createGroupPDF']").click(function () {
        window.open('PDFs/pdf_createVermerkGroupPDF.php?gruppenID=' + this.id + '&');//there are many ways to do this
    });

    $("#addGroup").click(function () {
        var gruppenart = $("#gruppenart").val();
        var gruppenName = $("#gruppenName").val();
        var gruppenOrt = $("#gruppenOrt").val();
        var gruppenVerfasser = $("#gruppenVerfasser").val();
        var gruppenDatum = $("#gruppenDatum").val();//        console.log(gruppenDatum);
        var gruppenStart = $("#gruppenStart").val();       //     console.log(gruppenStart);
        var gruppenEnde = $("#gruppenEnde").val();  //      console.log(gruppenEnde);
        if (gruppenart !== "" && gruppenName !== "" && gruppenOrt !== "" && gruppenVerfasser !== "" && gruppenDatum !== "" && gruppenStart !== "" && gruppenEnde !== "") {
            $.ajax({
                url: "addVermerkGroup.php",
                data: {
                    "gruppenart": gruppenart,
                    "gruppenName": gruppenName,
                    "gruppenOrt": gruppenOrt,
                    "gruppenVerfasser": gruppenVerfasser,
                    "gruppenDatum": gruppenDatum,
                    "gruppenStart": gruppenStart,
                    "gruppenEnde": gruppenEnde
                },
                type: "POST",
                success: function (data) {
                    makeToaster(data,true);
                    location.reload();
                }
            });
        } else {
            alert("Bitte alle Felder ausfüllen!");
        }
    });

    $("#saveGroup").click(function () {
        var gruppenart = $("#gruppenart").val();
        var gruppenName = $("#gruppenName").val();
        var gruppenOrt = $("#gruppenOrt").val();
        var gruppenVerfasser = $("#gruppenVerfasser").val();
        var gruppenDatum = $("#gruppenDatum").val();
        var gruppenStart = $("#gruppenStart").val();
        var gruppenEnde = $("#gruppenEnde").val();
        if (gruppenart !== "" && gruppenName !== "" && gruppenOrt !== "" && gruppenVerfasser !== "" && gruppenDatum !== "" && gruppenStart !== "" && gruppenEnde !== "" && gruppenID !== "") {
            // $('#addDeviceModal').modal('hide');
            $.ajax({
                url: "saveVermerkGroup.php",
                data: {
                    "gruppenart": gruppenart,
                    "gruppenName": gruppenName,
                    "gruppenOrt": gruppenOrt,
                    "gruppenVerfasser": gruppenVerfasser,
                    "gruppenDatum": gruppenDatum,
                    "gruppenStart": gruppenStart,
                    "gruppenEnde": gruppenEnde,
                    "gruppenID": gruppenID
                },
                type: "POST",
                success: function (data) {

                    location.reload();
                    document.getElementById('pdfPreview').contentWindow.location.reload();
                    makeToaster(data,true);
                }
            });
        } else {
            alert("Bitte alle Felder ausfüllen!");
        }
    });
</script>
</body>
</html>
