<?php
include '_utils.php';
init_page_serversides();
?>


<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>RB-Dokumentation</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style.css" type="text/css" media="screen"/>
    <link rel="icon" href="iphone_favicon.png">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css"
          integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>

    <link rel="stylesheet" type="text/css"
          href="https://cdn.datatables.net/v/bs4/dt-1.10.18/b-1.5.2/b-html5-1.5.2/sl-1.2.6/datatables.min.css"/>
    <script type="text/javascript"
            src="https://cdn.datatables.net/v/bs4/dt-1.10.18/b-1.5.2/b-html5-1.5.2/sl-1.2.6/datatables.min.js"></script>

    <link rel="stylesheet" type="text/css"
          href="https://cdn.jsdelivr.net/datatables.mark.js/2.0.0/datatables.mark.min.css"/>
    <script type="text/javascript"
            src="https://cdn.datatables.net/plug-ins/1.10.13/features/mark.js/datatables.mark.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/mark.js/8.6.0/jquery.mark.min.js"></script>

    <!--DATEPICKER -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/css/bootstrap-datepicker3.min.css">
    <script type='text/javascript'
            src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/js/bootstrap-datepicker.min.js"></script>

</head>


<body style="height:100%">
<div id="limet-navbar"></div>
<div class="container-fluid" style="height:100%">

    <div class="row">
        <div class="col-md-6">
            <div class="mt-4 card">
                <div class="card-header"><b>Vermerkgruppen</b>
                    <label class="float-right">
                        <button type='button' id='<?php echo $_SESSION["projectID"] ?>'
                                class='btn btn-outline-success btn-sm' value='Neue Vermerkgruppe'><i
                                    class='fas fa-plus'></i> Neu
                        </button>
                        <button type="button" class="btn btn-outline-dark btn-sm" value="searchDocumentation"
                                data-toggle="modal" data-target="#showSearchModal"><i class="fas fa-search"></i> Suche
                        </button>
                    </label>
                </div>
                <div class="card-body">
                    <?php

                    $mysqli = utils_connect_sql();

                    $sql = "SELECT tabelle_Vermerkgruppe.Gruppenname, tabelle_Vermerkgruppe.Gruppenart, tabelle_Vermerkgruppe.Ort, date_format(tabelle_Vermerkgruppe.Startzeit, '%h:%i') Startzeit, date_format(tabelle_Vermerkgruppe.Endzeit, '%h:%i') Endzeit, tabelle_Vermerkgruppe.Datum, tabelle_Vermerkgruppe.idtabelle_Vermerkgruppe, tabelle_Vermerkgruppe.Verfasser
                                                        FROM tabelle_Vermerkgruppe
                                                        WHERE (((tabelle_Vermerkgruppe.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . "))
                                                        ORDER BY tabelle_Vermerkgruppe.Datum DESC;";

                    $result = $mysqli->query($sql);

                    echo "<table class='table table-striped table-bordered table-sm' id='tableVermerkGruppe'>
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
                        echo "<td><button type='button' id='" . $row['idtabelle_Vermerkgruppe'] . "' class='btn btn-outline-dark btn-xs' value='changeVermerkgruppe'><i class='fas fa-pencil-alt'></i></button></td>";
                        echo "<td>" . $row['Gruppenname'] . "</td>";
                        echo "<td>" . $row['Datum'] . "</td>";
                        echo "<td>" . $row['Gruppenart'] . "</td>";
                        echo "<td align='center'>";
                        switch ($row["Gruppenart"]) {
                            case "Mailverkehr":
                                echo "<span class='badge badge-pill badge-info'> Mailverkehr </span>";
                                break;
                            case "Telefonnotiz":
                                echo "<span class='badge badge-pill badge-dark'> Telefonnotiz </span>";
                                break;
                            case "AV":
                                echo "<span class='badge badge-pill badge-warning'> AV </span>";
                                break;
                            case "Protokoll":
                                echo "<span class='badge badge-pill badge-primary'> Protokoll </span>";
                                break;
                            case "ÖBA-Protokoll":
                                echo "<span class='badge badge-pill badge-success'> ÖBA-Protokoll </span>";
                                break;
                            default:
                                echo "Art unbekannt: " . $row['Gruppenart'];
                        }
                        echo "</td>";
                        echo "<td>" . $row['Ort'] . "</td>";
                        echo "<td>" . $row['Verfasser'] . "</td>";
                        echo "<td><button type='button' id='" . $row['idtabelle_Vermerkgruppe'] . "' class='btn btn-outline-dark btn-xs' value='showGroupMembers' data-toggle='modal' data-target='#showGroupMembersModal'><i class='fas fa-users'></i></button></td>";
                        echo "<td><button type='button' id='" . $row['idtabelle_Vermerkgruppe'] . "' class='btn btn-outline-dark btn-xs' value='createGroupPDF' onclick='myFunction()'><i class='fas fa-file-pdf'></i></button></td>";
                        echo "<td>" . $row['Startzeit'] . "</td>";
                        echo "<td>" . $row['Endzeit'] . "</td>";
                        echo "</tr>";
                    }
                    echo "</tbody></table>";

                    // echo "<button type='button' id='".$_SESSION["projectID"]."' class='btn btn-success btn-sm' value='Neue Vermerkgruppe'>Neue Vermerkgruppe</button>";

                    ?>
                </div>
            </div>
            <div class="mt-4 card">
                <div class="card-header"><b>Vermerkuntergruppen</b>
                    <label class="float-right" id="divUntergruppeRightLabel">
                        <button type='button' id='buttonNewVermerkuntergruppe' class='btn btn-outline-success btn-sm'
                                value='Neue Vermerkuntergruppe' style='visibility:hidden'><i class='fas fa-plus'></i>
                            Neu
                        </button>
                    </label>
                </div>
                <div class="card-body" id="vermerkUntergruppen"></div>
            </div>
            <div class="mt-4 card">
                <div class="card-header"><b>Vermerke</b>
                    <label class="float-right" id="divVermerkeRightLabel">
                        <button type='button' id='buttonNewVermerk' class='btn btn-outline-success btn-sm'
                                value='Neuer Vermerk' style='visibility:hidden'><i class='fas fa-plus'></i> Neu
                        </button>
                    </label>
                </div>
                <div class="card-body" id="vermerke"></div>
            </div>
            <div class="mt-4 card">
                <div class="card-header"><b>Bilder</b>
                    <label class="float-right" id="divImagesRightLabel">
                        <button type='button' id='addImage' class='btn btn-outline-success btn-sm'
                                value='Bild hinzufügen' style='visibility:hidden'><i class='fas fa-plus'></i> Bild
                            hinzufügen
                        </button>
                    </label>
                </div>
                <div class="card-body" id="images"><img id="images_cb"></div>
            </div>
        </div>
        <!-- Darstellung PDF -->
        <div class="col-md-6">
            <div class="mt-4 card">
                <div class="card-header">Vorschau-PDF</div>
                <div class="card-body embed-responsive embed-responsive-1by1">
                    <iframe class="embed-responsive-item" id="pdfPreview"></iframe>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal zum HinzufÃ¼gen/Ã„ndern einer Gruppe -->
    <div class='modal fade' id='changeGroupModal' role='dialog'>
        <div class='modal-dialog modal-md'>
            <!-- Modal content-->
            <div class='modal-content'>
                <div class='modal-header'>
                    <h4 class='modal-title'>Gruppendaten</h4>
                    <button type='button' class='close' data-dismiss='modal'>&times;</button>

                </div>
                <div class='modal-body' id='mbody'>
                    <form role="form">
                        <div class="form-group">
                            <label for="gruppenart">Art:</label>
                            <select class='form-control form-control-sm' id='gruppenart' name='gruppenart'>
                                <option value="Mailverkehr">Mailverkehr</option>
                                <option value="Telefonnotiz">Telefonnotiz</option>
                                <option value="AV">AV</option>
                                <!--<option value="Wandabwicklung">Wandabwicklung</option>-->
                                <option value="Protokoll">Protokoll</option>
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
                        <div class="form-group">
                            <label for="gruppenStart">Startzeit:</label>
                            <input type="text" class="form-control form-control-sm" id="gruppenStart"
                                   placeholder="hh:mm"/>
                        </div>
                        <div class="form-group">
                            <label for="gruppenEnde">Endzeit:</label>
                            <input type="text" class="form-control form-control-sm" id="gruppenEnde"
                                   placeholder="hh:mm"/>
                        </div>
                </div>
                <div class='modal-footer'>
                    <input type='button' id='addGroup' class='btn btn-success btn-sm' value='HinzufÃ¼gen'
                           data-dismiss='modal'></input>
                    <input type='button' id='saveGroup' class='btn btn-warning btn-sm' value='Speichern'
                           data-dismiss='modal'></input>
                    <button type='button' class='btn btn-default btn-sm' data-dismiss='modal'>Abbrechen</button>
                </div>
            </div>

        </div>
    </div>


    <!-- Modal fÃ¼r Gruppenmitglieder -->
    <div class='modal fade' id='showGroupMembersModal' role='dialog'>
        <div class='modal-dialog modal-lg'>

            <!-- Modal content-->
            <div class='modal-content'>
                <div class='modal-header'>
                    <h4 class='modal-title'>Teilnehmer:</h4>
                    <button type='button' class='close' data-dismiss='modal'>&times;</button>
                </div>
                <div class='modal-body' id='showGroupMembersModalBody'>
                    <div class="mt-4 card">
                        <div class="card-header">Teilnehmer:</div>
                        <div class="card-body" id='vermerkGroupMembers'>
                        </div>
                    </div>
                    <hr></hr>
                    <div class="mt-4 card">
                        <div class="card-header">Mögliche Teilnehmer:</div>
                        <div class="card-body" id='possibleVermerkGroupMembers'>
                        </div>
                    </div>
                </div>
                <div class='modal-footer'>
                    <button type='button' class='btn btn-default btn-sm' value='closeModal' data-dismiss='modal'>
                        Schließen
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Vermerk-Suche -->
    <div class='modal fade' id='showSearchModal' role='dialog'>
        <div class='modal-dialog modal-lg'>
            <!-- Modal content-->
            <div class='modal-content'>
                <div class='modal-header'>
                    <h4 class='modal-title'>Vermerke durchsuchen:</h4>
                    <button type='button' class='close' data-dismiss='modal'>&times;</button>
                </div>
                <div class='modal-body' id='showSearchModalBody'>
                    <?php
                    $sql = "SELECT tabelle_Vermerkgruppe.Gruppenname, tabelle_Vermerkgruppe.Datum, tabelle_Vermerkuntergruppe.Untergruppennummer, tabelle_Vermerkuntergruppe.Untergruppenname, tabelle_Vermerke.Vermerktext, tabelle_Vermerke.idtabelle_Vermerke, tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung, tabelle_lose_extern.LosNr_Extern, tabelle_lose_extern.LosBezeichnung_Extern
                                            FROM tabelle_lose_extern RIGHT JOIN (tabelle_räume RIGHT JOIN ((tabelle_Vermerkgruppe INNER JOIN tabelle_Vermerkuntergruppe ON tabelle_Vermerkgruppe.idtabelle_Vermerkgruppe = tabelle_Vermerkuntergruppe.tabelle_Vermerkgruppe_idtabelle_Vermerkgruppe) INNER JOIN tabelle_Vermerke ON tabelle_Vermerkuntergruppe.idtabelle_Vermerkuntergruppe = tabelle_Vermerke.tabelle_Vermerkuntergruppe_idtabelle_Vermerkuntergruppe) ON tabelle_räume.idTABELLE_Räume = tabelle_Vermerke.tabelle_räume_idTABELLE_Räume) ON tabelle_lose_extern.idtabelle_Lose_Extern = tabelle_Vermerke.tabelle_lose_extern_idtabelle_Lose_Extern
                                            WHERE (((tabelle_Vermerkgruppe.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . "))
                                            ORDER BY tabelle_Vermerkgruppe.Datum DESC;";

                    $result = $mysqli->query($sql);

                    echo "<table class='table table-striped table-bordered table-sm' id='tableSearchVermerk'  cellspacing='0' width='100%'>
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

                    ?>
                </div>
            </div>
            <div class='modal-footer'>
                <button type='button' class='btn btn-default btn-sm' value='closeModal' data-dismiss='modal'>Schließen
                </button>
            </div>
        </div>
    </div>
</div>
</div>
<?php
$mysqli->close();
?>

<script>
    var gruppenID;
    var vermerkID;

    $(document).ready(function () {
        var table = $('#tableVermerkGruppe').DataTable({
            "columnDefs": [
                {
                    "targets": [0, 4, 10, 11],
                    "visible": false,
                    "searchable": false,
                    "sortable": false
                }
            ],
            "select": true,
            "paging": true,
            "searching": true,
            "info": true,
            "order": [[3, "desc"]],
            "pagingType": "simple",
            "lengthChange": false,
            "pageLength": 10,
            "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"},
            "mark": true
        });

        $('#tableSearchVermerk').DataTable({
            "columnDefs": [
                {
                    "targets": [0],
                    "visible": false,
                    "searchable": false,
                    "sortable": false
                }
            ],
            "select": false,
            "paging": true,
            "searching": true,
            "info": true,
            "order": [[2, "desc"]],
            "pagingType": "simple",
            "lengthChange": false,
            "pageLength": 10,
            "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"},
            "mark": true
        });

        $('#tableVermerkGruppe tbody').on('click', 'tr', function () {
            if ($(this).hasClass('info')) {

            } else {
                gruppenID = table.row($(this)).data()[0];
                document.getElementById("gruppenart").value = table.row($(this)).data()[4];
                document.getElementById("gruppenName").value = table.row($(this)).data()[2];
                document.getElementById("gruppenOrt").value = table.row($(this)).data()[6];
                document.getElementById("gruppenVerfasser").value = table.row($(this)).data()[7];
                document.getElementById("gruppenDatum").value = table.row($(this)).data()[3];
                document.getElementById("gruppenStart").value = table.row($(this)).data()[10];
                document.getElementById("gruppenEnde").value = table.row($(this)).data()[11];

                $("#vermerke").hide();
                table.$('tr.info').removeClass('info');
                $(this).addClass('info');
                $.ajax({
                    url: "getVermerkeuntergruppenToGruppe.php",
                    data: {"vermerkGruppenID": table.row($(this)).data()[0]},
                    type: "GET",
                    success: function (data) {
                        $("#vermerkUntergruppen").html(data);

                    }
                });
                $('#pdfPreview').attr('src', '/pdf_createVermerkGroupPDF.php?gruppenID=' + gruppenID);
            }
        });
    });


    $('#gruppenDatum').datepicker({
        format: "yyyy-mm-dd",
        calendarWeeks: true,
        autoclose: true,
        todayBtn: "linked",
        language: "de"
    });


    $('#tablePossibleGroupMembers').DataTable({
        "paging": false,
        "searching": true,
        "info": false,
        "order": [[1, "asc"]],
        "columnDefs": [
            {
                "targets": [0],
                "visible": true,
                "searchable": false
            }
        ],
        "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"},
        "scrollY": '20vh',
        "scrollCollapse": true
    });

    $("button[value='showGroupMembers']").click(function () {
        var id = this.id;
        $.ajax({
            url: "getVermerkgruppenMembers.php",
            type: "GET",
            data: {"gruppenID": id},
            success: function (data) {
                $("#vermerkGroupMembers").html(data);
                $.ajax({
                    url: "getPossibleVermerkGruppenMembers.php",
                    type: "GET",
                    data: {"gruppenID": id},
                    success: function (data) {
                        $("#possibleVermerkGroupMembers").html(data);
                    }
                });

            }
        });
        //$('#showGroupMembersModal').modal('show'); 	     
    });

    $("button[value='changeVermerkgruppe']").click(function () {
        // Buttons ein/ausblenden!
        document.getElementById("saveGroup").style.display = "inline";
        document.getElementById("addGroup").style.display = "none";
        $('#changeGroupModal').modal('show');
    });

    $("button[value='Neue Vermerkgruppe']").click(function () {
        var id = this.id;
        document.getElementById("saveGroup").style.display = "none";
        document.getElementById("addGroup").style.display = "inline";
        $('#changeGroupModal').modal('show');
    });


    $("button[value='createGroupPDF']").click(function () {
        window.open('/pdf_createVermerkGroupPDF.php?gruppenID=' + this.id + '&');//there are many ways to do this
    });


    $("#addGroup").click(function () {
        var gruppenart = $("#gruppenart").val();
        var gruppenName = $("#gruppenName").val();
        var gruppenOrt = $("#gruppenOrt").val();
        var gruppenVerfasser = $("#gruppenVerfasser").val();
        var gruppenDatum = $("#gruppenDatum").val();
        var gruppenStart = $("#gruppenStart").val();
        var gruppenEnde = $("#gruppenEnde").val();
        // var gruppenFortsetzung  = $("#gruppenFortsetzung").val();

        if (gruppenart !== "" && gruppenName !== "" && gruppenOrt !== "" && gruppenVerfasser !== "" && gruppenDatum !== "" && gruppenStart !== "" && gruppenEnde !== "") {
            // $('#addDeviceModal').modal('hide');

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
                type: "GET",
                success: function (data) {
                    alert(data);
                    // Neu Laden der Vermerkliste
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
        //var gruppenFortsetzung  = $("#gruppenFortsetzung").val();

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
                type: "GET",
                success: function (data) {
                    alert(data);
                    location.reload();
                    document.getElementById('pdfPreview').contentWindow.location.reload();
                }
            });

        } else {
            alert("Bitte alle Felder ausfüllen!");
        }
    });

</script>
</body>
</html>
