<?php
session_start();
include '_utils.php';
init_page_serversides();
include '_scrollUpBtn.php';
?> 

<!DOCTYPE html>
<html data-bs-theme="dark" xmlns="http://www.w3.org/1999/xhtml" >
    <head>
        <title>RB-Raumvergleich</title>
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
            <link rel="stylesheet" href="style.css" type="text/css" media="screen" />
            <!--            <link rel="icon" href="iphone_favicon.png">
                        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">-->
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

                <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css" integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous">
                    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
                    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script> 
                    <link href="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.0.5/af-2.7.0/b-3.0.2/b-colvis-3.0.2/b-html5-3.0.2/b-print-3.0.2/cr-2.0.1/date-1.5.2/fc-5.0.0/fh-4.0.1/kt-2.12.0/r-3.0.2/rg-1.5.0/rr-1.5.0/sc-2.4.1/sb-1.7.1/sp-2.3.1/sl-2.0.1/sr-1.4.1/datatables.min.css" rel="stylesheet">

                        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
                        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
                        <script src="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.0.5/af-2.7.0/b-3.0.2/b-colvis-3.0.2/b-html5-3.0.2/b-print-3.0.2/cr-2.0.1/date-1.5.2/fc-5.0.0/fh-4.0.1/kt-2.12.0/r-3.0.2/rg-1.5.0/rr-1.5.0/sc-2.4.1/sb-1.7.1/sp-2.3.1/sl-2.0.1/sr-1.4.1/datatables.min.js"></script>
                        <style>

                        </style>
                        </head> 
                        <body style="height:100%"> 
                            <div class="container-fluid">
                                <div id="limet-navbar" class=' '> </div> 

                                <div class="card col-md-12" >
                                    <div class="card-header"></div>
                                    
                                    <div class="card-body row col-md-12">
                                        <div class="mt-4 card col-md-6 border-success">    
                                            <div class="card-header d-flex justify-content-between col-md-12" id="TableCardHeader">
                                                <div class="col-8  col-md-12" id="SelectRoom4Comparison"> 
                                                    <button id="SelectRoomBtn" class="btn btn-success">Raum wählen</button> 
                                                </div>
                                                <div class="col-4 col-md-12" id=""> </div> 
                                            </div>

                                            <div class="card-body" id = "table_room_cb">    
                                                <table class="table display compact table-responsive table-striped table-bordered table-sm sticky" width ="100%" id="table_rooms" > 
                                                    <thead <tr></tr> </thead>
                                                    <tbody> <td></td>  </tbody>
                                                </table> 
                                            </div>
                                        </div>      

                                        <div class="mt-4 card col-md-6  border-info">
                                            <div class="card-header" >  
                                                <button type="button" class="btn btn-outline-dark btn-info" id="showVglRoomsBtn"> Vgl Raum <i class="fas fa-caret-up"></i></button> 
                                            </div>
                                            <div class="card-body" id ="table_vgl_cb">
                                                <table class="table display compact table-responsive table-striped table-bordered table-sm sticky" width ="100%" id="table_vgl_rooms" > 
                                                    <thead <tr></tr> </thead>
                                                    <tbody> <td></td>  </tbody>
                                                </table>  
                                            </div>
                                        </div>                                    
                                    </div>
                                </div>


                                <div class="mt-4 card border-light">
                                    <div class="card-group col-md-12" id="BtmCardZ" style="display:none"> 
                                        <div class="card col-md-6  border-success ">
                                            <div class="card-header border-success"> 
                                                <p id="ElememntsSelectedRoom"> Elements Selected Room  </p>
                                                <!--<button type="button" class="btn btn-outline-dark" id="showRoomElements"><i class="fas fa-caret-up"></i></button>--> 
                                            </div>
                                            <div class="card-body overflow-auto" id="roomElementsCB">   
                                                <p id="roomElements" class ="responsive"> </p>
                                            </div>
                                        </div>

                                        <div class="card col-md-6 border-info ">
                                            <div class="card-header border-info"> <p id="ElememntsVglRoom"> Elements Vgl Room  </p>  </div>
                                            <div class="card-body overflow-auto"> 
                                                <p id="vglRoomElements" class ="responsive"> </p>
                                            </div>
                                        </div>                                         
                                    </div>
                                </div> 


                                <script>
                                    var lastPosition = 0;

                                    // VARIABLES
                                    var table_rooms;
                                    var table_vgl_rooms;
                                    const columnsDefinitionShort = [
                                        {data: 'tabelle_projekte_idTABELLE_Projekte', title: 'Projek ID', visible: false, searchable: false},
                                        {data: 'idTABELLE_Räume', title: 'Raum ID', visible: false, searchable: false},
                                        {data: 'TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen', title: 'Funktionsstellen ID', visible: false, searchable: false},
                                        {data: 'MT-relevant', title: 'MT-rel.', name: 'MT-relevant', case: "bit", render: function (data) {
                                                return data === '1' ? 'Ja' : 'Nein';
                                            }},
                                        {data: 'Raumbezeichnung', title: 'Raumbez.'},
                                        {data: 'Raumnr', title: 'Raumnr'},
                                        {data: "Bezeichnung", title: "Funktionsstelle", case: "none-edit"},
                                        {data: 'Nummer', title: "DIN13080", case: "none-edit"}
                                    ];


                                    $(document).ready(function () {
                                        init_pr_rooms_dt();
                                        add_MT_rel_filter('#SelectRoom4Comparison');
                                        move_dt_search('SelectRoom4Comparison');

                                        selectRoomBtn();
                                        //                                    showRoomElementsBtn();
                                        selectVglRoomBtn();
                                    });

                                    $("#table_vgl_rooms").on('click', function () {
                                        var selectedRowData = table_vgl_rooms.row('.selected').data();
                                        if (selectedRowData)
                                            get_el_in_room_table(selectedRowData['idTABELLE_Räume'], "vglRoomElements");
                                    });

                                    function selectVglRoomBtn() {
                                        $('#showVglRoomsBtn').on('click', function () {
                                            var selectedRowData = table_vgl_rooms.row('.selected').data();
                                            if (selectedRowData) {
                                                if ($("#table_vgl_cb").is(":visible")) {
                                                    $('#showVglRoomsBtn').html("Vgl Raum  <i class='fas fa-caret-down'></i>");
                                                } else {
                                                    var newText = selectedRowData["Bezeichnung"] + " " + selectedRowData['TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen'] + " <i class='fas fa-caret-up´'></i>";
                                                    $('#showVglRoomsBtn').html(newText);
                                                }
                                                $("#table_vgl_cb").slideToggle();
                                                $('#table_vgl_container_cb').slideToggle(); // Use slideToggle for a roll-up animation 
                                                get_el_in_room_table(selectedRowData['idTABELLE_Räume'], "vglRoomElements");
                                            } else {

                                            }
                                        });
                                    }

                                    function selectRoomBtn() {
                                        $('#SelectRoomBtn').on('click', function () {
                                            var selectedRowData = table_rooms.row('.selected').data();
                                            if (selectedRowData) {
                                                if ($("#table_room_cb").is(":visible")) {
                                                    var newText = selectedRowData["Bezeichnung"] + " " + selectedRowData['TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen'] + " <i class='fas fa-caret-down'></i>";
                                                    $('#SelectRoomBtn').html(newText);
                                                } else {
                                                    $('#SelectRoomBtn').html("Raum Wählen  <i class='fas fa-caret-up '></i>");
                                                }
                                                $('#table_room_cb').slideToggle(); // Use slideToggle for a roll-up animation 
                                                $('#columnFilter').toggle();
                                                $('#dt-search-0').toggle();
                                                $('#BtmCardZ').toggle();

                                                key = "TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen";
                                                value = selectedRowData[key];
                                                console.log("GetRooms2Compare: " + key + " " + value);
                                                get_rooms_2_compare(key, value);
                                                get_el_in_room_table(selectedRowData['idTABELLE_Räume'], "roomElements");

                                            } else {
                                                alert("Raum auswählen");
                                            }
                                        });
                                    }

                                    function  get_rooms_2_compare(key, value) {
                                        if (table_vgl_rooms) {
                                            table_vgl_rooms.destroy();
                                            console.log("Destroyed");
                                        }
                                        table_vgl_rooms = new DataTable('#table_vgl_rooms', {
                                            ajax: {
                                                url: 'get_sql_data.php',
                                                data: {"key": key, "value": value},
                                                dataSrc: ''
                                            },
                                            columns: columnsDefinitionShort,
                                            dom: 'ti',
                                            language: {
                                                "search": ""},
                                            scrollY: true,
                                            scrollX: true,
                                            scrollCollapse: true,
                                            select: "single",
                                            keys: true,
                                            info: true,
                                            paging: false,
                                            pageLength: -1,
                                            compact: true
                                        });

                                    }

                                    //                                function showRoomElementsBtn() {
                                    //                                    $("#showRoomElements").click(function () {
                                    //                                        if ($("#roomElements").is(':hidden')) {
                                    //                                            $(this).html("Elements Selected Room  <i class='fas fa-caret-up'></i>");
                                    //                                            $("#roomElementsCB").slideToggle();
                                    //
                                    //                                        } else {
                                    //                                            $(this).html("Elements Selected Room  <i class='fas fa-caret-down'></i>");
                                    //                                            $("#roomElementsCB").slideToggle();
                                    //                                        }
                                    //                                    });
                                    //                                }

                                    function get_el_in_room_table(RaumID, targetDiv) {
                                        $.ajax({
                                            url: "setSessionVariables.php",
                                            data: {"roomID": RaumID},
                                            type: "GET",
                                            success: function (data) {
                                                $.ajax({
                                                    url: "get_RoomElementsTable.php",
                                                    type: "GET",
                                                    success: function (data) {
                                                        $("#" + targetDiv).html(data);
                                                    }
                                                });
                                            }
                                        });
                                    }


                                    function init_pr_rooms_dt() {
                                        table_rooms = new DataTable('#table_rooms', {
                                            ajax: {
                                                url: 'get_room_per_project_param_short.php',
                                                dataSrc: ''
                                            },
                                            columns: columnsDefinitionShort,
                                            dom: '<"TableCardHeader"f>t<"btm.d-flex justify-content-between"lip>   ',
                                            language: {
                                                "search": ""},
                                            scrollY: true,
                                            scrollX: true,
                                            scrollCollapse: true,
                                            select: "single",
                                            keys: true,
                                            info: true,
                                            paging: false,
                                            pageLength: -1,
                                            compact: true
                                        });
                                    }

                                    function add_MT_rel_filter(location) {
                                        var dropdownHtml = '<select class="form-control-sm fix_size" id="columnFilter">' + '<option value="">MT</option><option value="Ja">Ja</option>' + '<option value="Nein">Nein</option></select>';
                                        $(location).append(dropdownHtml);
                                        $('#columnFilter').change(function () {
                                            var filterValue = $(this).val();
                                            table_rooms.column('MT-relevant:name').search(filterValue).draw();
                                        });
                                    }

                                    function move_dt_search(id) {
                                        var dt_searcher = document.getElementById("dt-search-0");
                                        dt_searcher.parentNode.removeChild(dt_searcher);
                                        document.getElementById(id).appendChild(dt_searcher);
                                        dt_searcher.classList.add("fix_size");
                                    }
                                </script>
                        </body> 
                        </html>
