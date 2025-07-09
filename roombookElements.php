<!-- 17.2.25: Reworked -->
<?php
if (!function_exists('utils_connect_sql')) {
    include "utils/_utils.php";
}
init_page_serversides();
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>RB-Elemente</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen"/>
    <link rel="icon" href="Logo/iphone_favicon.png"/>

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

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <style>
        .card-header {
            min-height: 3vh !important;
        }
    </style>
</head>

<body>
<div id="limet-navbar"></div> <!-- Container für Navbar Aufruf über onLoad -->
<div class="container-fluid bg-secondary bg-opacity-10">

    <div class="row">
        <div class="col-xxl-6">
            <div class="card mt-1">
                <div class="card-header">
                    <div class="row">
                        <div class="col-xxl-6">Elemente in DB</div>
                        <div class="col-xxl-6 d-flex justify-content-end" id="CardHeaderElementesInDb"></div>
                    </div>
                </div>
                <div class="card-body" id="elementsInDB">
                    <?php include "getElementsInDbCardBodyContent.php"; ?>
                </div>
            </div>
        </div>

        <div class="col-xxl-2">
            <div class="card mt-1">
                <div class="card-header" id="CardHeaderElementGruppen">Elementgruppen
                    <button type="reset" class="btn btn-sm float-end" title="Reset" id="ResetElementGroups">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
                <div class="card-body" id="elementGroups">
                    <?php include "getElementgruppenCardContent.php"; ?>
                </div>
            </div>


        </div>

        <div class="col-xxl-4">
            <div class="card mt-1" id="devicesInDBCard" style="max-height: 500px;overflow-y: scroll;">
                <div class="card-header" id=""> Geräte zu Element
                </div>
                <div class="card-body" id="devicesInDB">
                </div>
            </div>
        </div>

    </div>


    <div class='row align-items-stretch'>
        <div class='col-xxl-7'>
            <div class="card mt-1 h-auto">
                <div class="card-header">
                    <div class="row">
                        <div class="col-xxl-6 d-inline-flex justify-content-start align-items-center">
                            <i class="fas fa-door-open fa-lg me-2"></i> mit Element
                        </div>
                        <div class="col-xxl-6 d-inline-flex justify-content-end align-items-center" id="CHRME"></div>
                    </div>
                </div>
                <div class="card-body flex-xxl-grow-1 h-auto" id="roomsWithElement"></div>
            </div>
        </div>
        <div class='col-xxl-5'>
            <div class="card mt-1 h-auto">
                <div class="card-header" id="RäumeOhneElCardHeader ">

                    <div class="row">
                        <div class="col-xxl-4 d-flex flex-nowrap text-nowrap align-items-center" id="CardHeaderRäumeOhneElement">
                            <i class="fas fa-door-open  fa-lg me-2"></i> ohne Element
                        </div>
                        <div class="col-xxl-8 d-flex flex-nowrap justify-content-end align-items-center">
                            <button type='button' class='btn btn-outline-success btn-sm ' id='addElements'
                                    data-bs-toggle='modal' data-bs-target='#addElementsToRoomModal' disabled><i
                                        class='fas fa-plus'></i> Element hinzufügen
                            </button>
                            <button type='button' id="selectAllRows"
                                    class="btn btn-outline-primary btn-sm me-2 ms-4 ">
                                <i class="fa fa-eye" aria-hidden="true"></i>
                                Sichtbare auswählen
                            </button>
                            <button type='button' id="deselectAllRows" class="btn btn-outline-danger btn-sm">
                                <i class="fas fa-times" aria-hidden="true"></i>
                                Keinen Raum auswählen
                            </button>
                        </div>

                    </div>
                </div>
                <div class="card-body h-auto" id="roomsWithoutElement"></div>
            </div>
        </div>
    </div>
</div>


<script>
    var elementBezeichnung; //var, beacause used to set Modal Title in getRoomsWithoutElement
    var selectedRooms = [];
    var tableElementsInDB;

    function init_table_elementsinDB() {
        $('#CardHeaderElementesInDb .xxx').remove();
        tableElementsInDB = new DataTable('#tableElementsInDB', {
            select: true,
            paging: true,
            pageLength: 10,
            lengthChange: true,
            info: false,
            order: [[1, 'asc']],
            columnDefs: [
                {
                    targets: [0],
                    visible: false,
                    searchable: false
                }
            ],
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json",
                search: "",
                searchPlaceholder: "Element Suche..."
            },
            layout: {
                bottomStart: 'pageLength',
                bottomEnd: 'paging',
                topStart: 'search',
                topEnd: null
            },
            initComplete: function () {
                $('#tableElementsInDB_wrapper .dt-search label').remove();
                $('#tableElementsInDB_wrapper .dt-search').children().removeClass("form-control form-control-sm").addClass("btn btn-sm btn-outline-dark xxx").appendTo('#CardHeaderElementesInDb');
            }
        });


        $('#tableElementsInDB tbody').on('click', 'tr', function () {
            document.getElementById("bezeichnung").value = tableElementsInDB.row($(this)).data()[2];
            document.getElementById("kurzbeschreibungModal").value = tableElementsInDB.row($(this)).data()[3];
            elementBezeichnung = tableElementsInDB.row($(this)).data()[2];
            $('#addElements').prop('disabled', true);
            let elementID = tableElementsInDB.row($(this)).data()[0];
            $.ajax({
                url: "setSessionVariables.php",
                data: {"elementID": elementID},
                type: "GET",
                success: function () {
                    $.ajax({
                        url: "getRoomsWithElement1.php",
                        data: {"elementID": elementID},
                        type: "GET",
                        success: function (data) {
                            $("#roomsWithElement").html(data);
                            $.ajax({
                                url: "getRoomsWithoutElement.php",
                                data: {"elementID": elementID},
                                type: "GET",
                                success: function (data) {
                                    $("#roomsWithoutElement").html(data);

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
        $('#selectAllRows').click(function () {
            $('#roomsWithoutElement table tbody tr:visible').each(function () {
                let row = tableRoomsWithoutElement.row(this);
                let data = row.data();
                let id = data[0];
                $(this).addClass('selected');
                if (!roomIDs.includes(id)) roomIDs.push(id);
            });
            updateSelectedRoomsDisplay();
        });


        $('#deselectAllRows').click(function () {
            $('#roomsWithoutElement table tbody tr:visible').each(function () {
                let row = tableRoomsWithoutElement.row(this);
                let data = row.data();
                let id = data[0];
                $(this).removeClass('selected');
                roomIDs = roomIDs.filter(rid => rid !== id);
            });
            updateSelectedRoomsDisplay();
        });


    });
</script>
</body>


</html>
