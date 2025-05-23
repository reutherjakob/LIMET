<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>Element Admin</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style.css" type="text/css" media="screen"/>
    <link rel="icon" href="iphone_favicon.png">
    <!-- Rework 2025 CDNs -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.2.1/af-2.7.0/b-3.2.1/b-colvis-3.2.1/b-html5-3.2.1/b-print-3.2.1/cr-2.0.4/date-1.5.5/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.1/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-3.0.0/sr-1.4.1/datatables.min.js"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css"
          integrity="sha512-q3eWabyZPc1XTCmF+8/LuE1ozpg5xxn7iO89yfSOd5/oKvyqLngoNGsx8jq92Y8eXJ/IRxQbEC+FGSYxtk2oiw=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link href="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.2.1/af-2.7.0/b-3.2.1/b-colvis-3.2.1/b-html5-3.2.1/b-print-3.2.1/cr-2.0.4/date-1.5.5/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.1/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-3.0.0/sr-1.4.1/datatables.min.css"
          rel="stylesheet">
</head>
<?php
if (!function_exists('utils_connect_sql')) {
    include "_utils.php";
}
init_page_serversides("x"); ?>

<body style="height:100%">
<div id="limet-navbar"></div> <!-- Container für Navbar -->
<div class="container-fluid">
    <div class="mt-4 card">
        <div class="card-header">Elemente</div>
        <div class="card-body">
            <div class="row mt-1">
                <div class='col-xxl-6'>
                    <div class='mt-1 card'>
                        <div class='card-header' id="CardHeaderElementGruppen"><label>Elementgruppen</label>
                            <button type="reset" class="btn btn-sm float-end" title="Reset" id="ResetElementGroups">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                        <div class='card-body' id='elementGroups'>
                            <?php include "getElementgruppenCardContent.php"; ?>
                        </div>
                    </div>
                    <div class="mt-1 card">
                        <div class="card-header">
                            <div class="row d-flex align-items-center">
                                <div class="col-xxl-6 col-6">Elemente in DB</div>
                                <div class="col-xxl-6 col-6 d-flex justify-content-end" id="CardHeaderElementesInDb"></div>
                            </div>
                        </div>
                        <div class="card-body" id="elementsInDB">
                            <?php include "getElementsInDbCardBodyContent.php"; ?>
                        </div>
                    </div>
                </div>
                <div class='col-xxl-6'>
                    <div class="mt-1 card">
                        <div class="card-header"><label>Schätzkosten in Projekten</label></div>
                        <div class="card-body" id="elementPricesInOtherProjects"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <hr>
    <div class="mt-1 card">
        <div class="card-header">Geräte</div>
        <div class="card-body">
            <div class="row mt-1">
                <div class='col-xxl-4'>
                    <div class='mt-1 card'>
                        <div class='card-header'><label>Geräte zu Element</label></div>
                        <div class='card-body' id='devicesInDB'></div>
                    </div>
                </div>
                <div class='col-xxl-4'>
                    <div class='mt-1 card'>
                        <div class='card-header'><label>Geräteparameter</label></div>
                        <div class='card-body' id='deviceParametersInDB'></div>
                    </div>
                </div>
                <div class='col-xxl-4'>
                    <div class='mt-1 card'>
                        <div class='card-header'><label>Gerätepreise</label></div>
                        <div class='card-body' id='devicePrices'></div>
                    </div>
                    <div class='mt-1 card'>
                        <div class='card-header'><label>Wartungspreise</label></div>
                        <div class='card-body' id='deviceServicePrices'></div>
                    </div>
                    <div class='mt-1 card'>
                        <div class='card-header'><label>Lieferanten</label></div>
                        <div class='card-body' id='deviceLieferanten'></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

<!-- Modal zum Ändern eines Elements -->
<div class='modal fade' id='changeElementModal' role='dialog'>
    <div class='modal-dialog modal-md'>
        <div class='modal-content'>
            <div class='modal-header'>
                <h4 class='modal-title'>Element ändern</h4>
                <button type='button' class='close' data-bs-dismiss='modal'>&times;</button>
            </div>
            <div class='modal-body' id='mbody'>
                <form role="form">
                    <div class="form-group">
                        <label for="bezeichnung">Bezeichnung:</label>
                        <input type="text" class="form-control" id="bezeichnung" placeholder="Type"/>
                    </div>
                    <div class="form-group">
                        <label for="kurzbeschreibungModal">Kurzbeschreibung:</label>
                        <textarea class="form-control" rows="5" id="kurzbeschreibungModal"></textarea>
                    </div>
                </form>
            </div>
            <div class='modal-footer'>
                <input type='button' id='saveElement' class='btn btn-warning btn-sm' value='Speichern'>
                <button type='button' class='btn btn-default btn-sm' data-bs-dismiss='modal'>Abbrechen</button>
            </div>
        </div>
    </div>
</div>

<script charset="utf-8" type="text/javascript">
    var table1;
    function init_table_elementsinDB() {
        $('#CardHeaderElementesInDb .xxx').remove();
        table1 = new DataTable('#tableElementsInDB', {
            paging: true,
            columnDefs: [
                {
                    targets: [0],
                    visible: false,
                    searchable: false
                },
                {
                    targets: [4],
                    visible: true,
                    searchable: false,
                    orderable: false
                }
            ],
            select: true,
            info: true,
            pagingType: 'simple',
            lengthChange: false,
            pageLength: 10,
            order: [[1, 'asc']],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json',
                search: "",
                searchPlaceholder: "Suche..."
            },
            layout: {
                topStart: null,
                topEnd: null,
                bottomStart: ['info', 'search'],
                bottomEnd: ['paging'],
            },
            initComplete: function () {
                $('#tableElementsInDB_wrapper .dt-search label').remove();
                $('#tableElementsInDB_wrapper .dt-search').children().removeClass("form-control form-control-sm").addClass("btn btn-sm btn-outline-dark xxx").appendTo('#CardHeaderElementesInDb');
            }
        });

        $('#tableElementsInDB tbody').on('click', 'tr', function () {
            $("#deviceParametersInDB").hide();
            $("#devicePrices").hide();
            $("#deviceLieferanten").hide();
            table1.$('tr.info').removeClass('info');
            $(this).addClass('info');

            let elementID = table1.row($(this)).data()[0];
            document.getElementById("bezeichnung").value = table1.row($(this)).data()[2];
            document.getElementById("kurzbeschreibungModal").value = table1.row($(this)).data()[3];

            $.ajax({
                url: "setSessionVariables.php",
                data: {"elementID": elementID},
                type: "GET",
                success: function () {
                    $.ajax({
                        url: "getStandardElementParameters.php",
                        data: {"elementID": elementID},
                        type: "GET",
                        success: function (data) {
                            $("#elementParametersInDB").html(data);
                            $.ajax({
                                url: "getElementPricesInDifferentProjects.php",
                                data: {"elementID": elementID},
                                type: "GET",
                                success: function (data) {
                                    $("#elementPricesInOtherProjects").html(data);
                                    $.ajax({
                                        url: "getDevicesToElement.php",
                                        data: {"elementID": elementID},
                                        type: "GET",
                                        success: function (data) {
                                            $("#devicesInDB").html(data);
                                        }
                                    });
                                }
                            });
                        }
                    });
                }
            });

        });

    }

    $(document).ready(function () {
        init_table_elementsinDB();
    });


    //Element speichern
    $("#saveElement").click(function () {
        let bezeichnung = $("#bezeichnung").val();
        let kurzbeschreibung = $("#kurzbeschreibungModal").val();
        if (bezeichnung !== "") {
            if (kurzbeschreibung !== "") {
                $.ajax({
                    url: "saveElement.php",
                    data: {"bezeichnung": bezeichnung, "kurzbeschreibung": kurzbeschreibung},
                    type: "GET",
                    success: function (data) {
                        alert(data);
                        $('#changeElementModal').modal('hide');
                        $.ajax({
                            url: "getElementsInDB.php",
                            type: "GET",
                            success: function (data) {
                                $("#elementsInDB").html(data);
                            }
                        });
                    }
                });
            } else {
                alert("Bitte alle Felder ausfüllen!");
            }
        } else {
            alert("Bitte alle Felder ausfüllen!");
        }
    });
</script>

</html>
