<?php
session_start();
include '_utils.php';
init_page_serversides();
?> 

<html data-bs-theme="dark" xmlns="http://www.w3.org/1999/xhtml" >
    <head>
        <title>RB-Raumvergleich</title>
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
        <meta name="viewport" content="width=device-width, initial-scale=1"/>
        <link rel="stylesheet" href="style.css" type="text/css" media="screen" />
        <link rel="icon" href="iphone_favicon.png"/> 
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous"/>
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css" integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous"/>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script> 
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
        <link href="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.0.5/af-2.7.0/b-3.0.2/b-colvis-3.0.2/b-html5-3.0.2/b-print-3.0.2/cr-2.0.1/date-1.5.2/fc-5.0.0/fh-4.0.1/kt-2.12.0/r-3.0.2/rg-1.5.0/rr-1.5.0/sc-2.4.1/sb-1.7.1/sp-2.3.1/sl-2.0.1/sr-1.4.1/datatables.min.css" rel="stylesheet"/>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.0.5/af-2.7.0/b-3.0.2/b-colvis-3.0.2/b-html5-3.0.2/b-print-3.0.2/cr-2.0.1/date-1.5.2/fc-5.0.0/fh-4.0.1/kt-2.12.0/r-3.0.2/rg-1.5.0/rr-1.5.0/sc-2.4.1/sb-1.7.1/sp-2.3.1/sl-2.0.1/sr-1.4.1/datatables.min.js"></script>
        <style>
            .highlighted-row {
                background-color: yellow; /* Change to your desired highlight color */
                font-weight: bold; /* Optional: Make the text bold */
            }
            .card {
                transition: transform 0.5s ease;
                border: 1px solid #ccc;
                overflow: hidden;
            }
            .resize-border {
                width: 2px; /* Width of the border area */
                /*cursor: col-resize;  Cursor style for resizing */
                z-index: 20;
                color:white;
                background-color:white;
            }
            .container {
                display: flex;
                overflow: hidden;
            }
            .flex-grow-1.visible {
                display: block;
                overflow: hidden;
            }
            .flex-grow-1.hidden {
                display: none;
                transform: translateX(-100%);
                transition: transform 0.3s ease, max-width 0.3s ease;
            }
            .btn {
                border: 1px grey;
                padding-right: 3px;
                padding-left: 3px;
            }
            .btn:hover {
                margin-top: 0;
                box-shadow: 0 0 4px 1px grey; /* Add a small drop shadow on hover */
            }
            .fix_size{
                height: 30px;
            }

            .table-container {
                position: sticky !important;
                overflow-y: scroll;
            }
        </style>
    </head>            
    <body style="height:100%">

        <div id="limet-navbar" class=''> </div>  

        <div class ="container-fluid" id="KONTÄNER">

            <div class="row justify-content-center">
                <div class="col-md-12">
                    <div class="card">           
                        <div class="card-header text-white"> 
                            <ul class="nav nav-tabs card-header-tabs" id="myTab" role="tablist">
<!--                                <li class="nav-item">
                                    <a class="nav-link" id="tab0-tab" data-bs-toggle="tab" href="#tab0" role="tab" aria-controls="tab0" aria-selected="false">DB Raumsuche </a>
                                </li>-->
                                <li class="nav-item">
                                    <a class="nav-link active" id="tab1-tab" data-bs-toggle="tab" href="#tab1" role="tab" aria-controls="tab1" aria-selected="true">Raumwahl & Elemente </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link disabled" id="tab2-tab" data-bs-toggle="tab" href="#tab2" role="tab" aria-controls="tab2" aria-selected="false">Bauangaben </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link disabled" id="tab3-tab" data-bs-toggle="tab" href="#tab3" role="tab" aria-controls="tab3" aria-selected="false">Anzahl Elemente </a>
                                </li>

                            </ul>
                        </div>

                        <div class="card-body">
                            <div class="tab-content" id="myTabContent">
<!--                                <div class="tab-pane fade" id="tab0" role="tabpane0" aria-labelledby="tab0-tab">
                                    <div class="card">    
                                        <div class="card-header d-flex align-items-center"id='searchDbCardHeader'> ok ok ok </div>
                                        <div class="card-body" id = "searchDbCardBody"> 
                                        </div>
                                    </div>      
                                </div>-->
 
                                <div class="tab-pane fade show active" id="tab1" role="tabpanel" aria-labelledby="tab1-tab">
                                    <div class="card responsive" style="width: 100%;"> 
                                        <div class="card-header row">
                                            <div class="col-sm-1"> </div>   
                                            <div class="col-sm-5">  <button id="SelectRoomBtn" class="btn"  style=" background-color:  rgba(100, 140, 25, 0.1);float:right;"> <i class="fa fa-caret-up"> </i>Select a Room </button> </div>
                                            <div class="col-sm-5">  <button id="SelectVglRoomBtn" class="btn" style=" background-color: rgba(13, 202, 240, 0.1);"> <i class="fa fa-caret-up"> </i>Select Vgl room </button> </div>
                                            <div class="col-sm-1"> </div>  
                                        </div> 

                                        <div class=" d-inline-flex" id="table_room_cb">   
                                            <div class="card   border-success flex-grow-1" style="width: 10vw" >  
                                                <div class="card-header d-inline-flex justify-content-center" style=" background-color:  rgba(100, 140, 25, 0.1);" >Room-Elements</div> 
                                                <div class="card-body"  id="RoomElements"> </div>
                                            </div>                                            <!--<div class="resize-border"></div>-->
                                            <div class="card  border-success flex-grow-1"  style="width: 40vw;" id="card_SelectRoom4Comparison">
                                                <div class="card-header d-inline-flex justify-content-center" id="SelectRoom4Comparison" style=" background-color:  rgba(100, 140, 25, 0.1);" >  </div> 
                                                <div class="card-body " id="cardbody_SelectRoom4Comparison">   
                                                    <div class="table-container">
                                                        <table class="table  compact table-responsive table-striped table-lg" id="table_rooms"> <!-- <thead ><tr></tr></thead><tbody><td></td></tbody> -->
                                                        </table>  
                                                    </div> 
                                                </div>                                                               
                                            </div> <!--                                            <div class="resize-border"></div>-->
                                            <div class="card  border-info flex-grow-1"  style="width: 40vw" id="card_vgl_room">  
                                                <div class="card-header d-inline-flex justify-content-center" id="card_header_vgl_room"  style=" background-color: rgba(13, 202, 240, 0.1)"> </div> 
                                                <div class="card-body " id ="card_body_vgl_room">
                                                    <div class="table-container">
                                                        <table class="table  compact table-responsive table-striped table-lg" id="table_vgl_rooms" > 
                                                            <thead ><tr></tr></thead>
                                                            <tbody><td></td></tbody>
                                                        </table>  
                                                    </div>   
                                                </div>
                                            </div>  <!--   <div class="resize-border"></div>-->
                                            <div class="card  border-info flex-grow-1" style="width: 10vw" >  
                                                <div class="card-header d-inline-flex justify-content-center" style=" background-color: rgba(13, 202, 240, 0.1)"> Vgl-Room-Elements </div> 
                                                <div class="card-body responsive" id ="">  
                                                    <p id="ElememntsVglRoom" > </p> 
                                                </div>

                                            </div> 
                                        </div> 
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="tab2" role="tabpanel" aria-labelledby="tab2-tab">
                                    <div class="card">    
                                        <div class="card-header d-flex align-items-center" style="height: 2.5vw;" id='TableCardHeaderBauangaben'></div>
                                        <table class="table display compact table-responsive table-striped table-bordered table-sm sticky" style= "width :100%" id="table_vgl_room_bauangaben" > 
                                            <thead <tr></tr> </thead>                                                                    <tbody> <td></td>  </tbody>                                                                       
                                        </table> 
                                        <div class="card-body" id = "table_container_div">
                                            <table class="table display compact table-responsive table-striped table-bordered table-sm sticky" style= "width :100%" id="table_rooms_bauangaben" > 
                                                <thead <tr></tr> </thead>                                                                    <tbody> <td></td>  </tbody>                                                                       
                                            </table> 
                                        </div>
                                    </div>      
                                </div>

                                <div class="tab-pane fade" id="tab3" role="tabpanel" aria-labelledby="tab3-tab">
                                    <table class="table display compact table-responsive table-striped table-bordered table-sm sticky" style= "width :100%" id="table_el_frequency" >
                                        <thead <tr></tr> </thead>                                                            
                                        <tbody> <td></td>  </tbody>                
                                    </table> 
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>  
        </div>

        <script src="roombookSpecifications_constDeclarations.js"></script> 

        <script>
            var table_rooms;
            var table_bauangaben;
            var RID1; // Holds id of room 2 compare 2 

            var table_vgl_rooms;
            var RID2;// Holds id of rooms 4 comparing them to roomID1 

            var tabelle_anzahl;
            let both_hidden = false;
            var newText = "";

            var filter_added = false;
            var filter_added2 = false;

            const newEntry = {
                data: 'Projektname',
                title: 'Projektname'
            };
            columnsDefinition.shift();
            var X = columnsDefinition.shift();
            columnsDefinition.unshift(newEntry);
            columnsDefinition.unshift(X);

            let columnsDefinitionShort = columnsDefinition.filter(column =>
                ["idTABELLE_Räume", 'TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen', 'MT-relevant', 'Raumbezeichnung', 'Raumnr', "Bezeichnung", 'Nummer', 'Raumbereich Nutzer', 'Geschoss', 'Bauetappe', 'Bauabschnitt'].includes(column.data)
            ).map(column => {
                if (!["idTABELLE_Räume", 'TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen', 'tabelle_projekte_idTABELLE_Projekte'].includes(column.data)) {
                    column.visible = true;
                }
                return column;
            });

            let columnsDefinitionShort_vgl = columnsDefinition.filter(column =>
                ['Projektname', "idTABELLE_Räume", 'TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen', 'MT-relevant',
                    'Raumbezeichnung', 'Raumnr', "Bezeichnung", 'Raumbereich Nutzer'].includes(column.data)
            );//, 'Geschoss', 'Bauetappe', 'Bauabschnitt'

            const columnsAnzahl = [
                {data: "AnzahlvonAnzahl"},
                {data: "SummevonAnzahl"},
                {data: "ElementID"},
                {data: "Bezeichnung"},
                {data: "Projektname"},
                {data: "TABELLE_Elemente_idTABELLE_Elemente", visible: false}];

            $(document).ready(function () {
                init_rooms_current_project_table();
                add_MT_rel_filter('#SelectRoom4Comparison', table_rooms);
                move_dt_search("dt-search-0", 'SelectRoom4Comparison');
                selectRoomBtn(); //showRoomElementsBtn(); selectVglRoomBtn();
                table_click("table_rooms", "RoomElements");
                table_click("table_vgl_rooms", "ElememntsVglRoom");
            });


            function init_dt_anzahl(value) {
                if (tabelle_anzahl) {
                    tabelle_anzahl.destroy();
                }
                tabelle_anzahl = new DataTable('#table_el_frequency', {
                    ajax: {
                        url: 'get_anzahl_elmente_per_funktionsstelle.php',
                        data: {"value": value},
                        dataSrc: ''
                    },
                    columns: columnsAnzahl
                });
            }

            function init_dt_BAUANGABEN(value) {
                dom_var = "tli";
                if (!filter_added2) {
                    dom_var = "ftli ";
                }
                if (table_bauangaben) {
                    table_bauangaben.destroy();
                }
                table_bauangaben = new DataTable('#table_rooms_bauangaben', {
                    ajax: {
                        url: 'get_rooms_with_funktionsteilstelle.php',
                        data: {"value": value},
                        dataSrc: ''
                    },
                    columns: columnsDefinition,
                    dom: dom_var,
                    scrollX: true,
                    scrollCollapse: true,
                    select: "os",
                    language: {
                        "search": "",
                        searchBuilder: {
                            title: null,
                            depthLimit: 2,
                            stateSave: false
                        }
                    },
                    keys: true,
                    stateSave: true,
                    info: true,
                    paging: true,
                    pagingType: "simple_numbers",
                    pageLength: 10,
                    lengthMenu: [
                        [10, 20, -1],
                        ['10 rows', '20 rows', 'Show all']
                    ],
                    compact: true,
                    fnRowCallback: function (nRow, aData, iDisplayIndex) {
                        console.log("RowCallback", RID1, aData[0]);
                        if (aData[0] === RID1)
                        {
                            $('td', nRow).css('background-color', 'Red');
                        }
                    },
                    initComplete: function () {
                        if (!filter_added2) {
                            filter_added2 = true;
                            add_MT_rel_filter("#TableCardHeaderBauangaben", table_bauangaben);
                            move_dt_search("dt-search-2", 'TableCardHeaderBauangaben');
                            console.log("T Bauang init only 1nce");
                        }
                        console.log("T Bauang init ");
                    }
                });
            }

            function table_click(table_id, target_id_4_elelemt_table) {
                $("#" + table_id).on('click', "tr", function () {
                    var selectedRowData;
                    if (table_id === "table_rooms") {
                        selectedRowData = table_rooms.row('.selected').data();
                        if (selectedRowData) {
                            newText = selectedRowData["Raumbezeichnung"] + ": " + selectedRowData["Bezeichnung"] + ": " + selectedRowData["TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen"] + ' <i class="fa fa-caret-up"> </i>';
                            RID1 = selectedRowData["idTABELLE_Räume"];
                            console.log("Currently save RID1", RID1);
                            $('#SelectRoomBtn').html(newText);
                        }
                    }
                    if (table_id === "table_vgl_rooms") {
                        selectedRowData = table_vgl_rooms.row('.selected').data();
                        console.log(selectedRowData);
                        if (selectedRowData) {
                            RID2 = selectedRowData["idTABELLE_Räume"];
                            console.log("Currently save RID2", RID2);
                            newText = selectedRowData["Raumbezeichnung"] + ": " + selectedRowData["Bezeichnung"] + ": " + selectedRowData["TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen"] + ' <i class="fa fa-caret-up"> </i>';
                            $('#SelectVglRoomBtn').html(newText);
                        }
                    }
                    if (selectedRowData) {
                        if (table_id === "table_rooms") {
                            value = selectedRowData["TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen"];
                            init_vgl_rooms_table(value);
                        }
                        get_el_in_room_table(selectedRowData['idTABELLE_Räume'], target_id_4_elelemt_table);
                    }
                });
            }


            function  init_vgl_rooms_table(value) {
                let dom_var = "tlip";
                if (table_vgl_rooms) {
                    table_vgl_rooms.destroy();
//                                        console.log("Existing Table Dstroyed.... ");
                }
                if (!filter_added) {
                    dom_var = "ftlip ";
                }
                table_vgl_rooms = new DataTable('#table_vgl_rooms', {
                    ajax: {
                        url: 'get_rooms_with_funktionsteilstelle.php',
                        data: {"value": value},
                        dataSrc: ''
                    },
                    columns: columnsDefinitionShort_vgl,
                    dom: dom_var,
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
                    compact: true,

                    initComplete: function () {
                        if (!filter_added) {
                            add_MT_rel_filter("#card_header_vgl_room", table_vgl_rooms);
                            move_dt_search("dt-search-1", "card_header_vgl_room");
                            filter_added = true;
                        }
                        console.log("T Vgl initiated");

                    }
                });
            }

            function init_rooms_current_project_table() {
                table_rooms = new DataTable('#table_rooms', {
                    ajax: {
                        url: 'get_rb_specs_data.php', 
                        dataSrc: ''
                    },
                    columns: columnsDefinitionShort,
                    dom: 'ftlip',
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

            function selectRoomBtn() {
                $('#SelectRoomBtn').on('click', function () {
                    var selectedRowData = table_rooms.row('.selected').data();
                    if (selectedRowData) {
                        let v = selectedRowData['TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen'];
                        console.log("SelectRoomBtn KICK", v);
                        init_dt_BAUANGABEN(v);
                        init_dt_anzahl(v);
                        if ($("#cardbody_SelectRoom4Comparison").is(":visible")) {
                            $('#cardbody_SelectRoom4Comparison').hide();
                            $('#SelectRoom4Comparison').hide();
                            $('#card_SelectRoom4Comparison').hide();
                            newText = selectedRowData["Raumbezeichnung"] + ": " + selectedRowData["Bezeichnung"] + ' <i class="fa fa-caret-down"> </i>';
                            $('#SelectRoomBtn').html(newText);
                            $('#tab2-tab').removeClass('disabled');
                            $('#tab3-tab').removeClass('disabled');

                            if (!$("#card_body_vgl_room").is(":visible")) {
                                console.log("both hidden, RID1: " + RID1);
                                both_hidden = true;
                            }
                        } else {
                            both_hidden = false;
                            $('#cardbody_SelectRoom4Comparison').show();
                            $('#SelectRoom4Comparison').show();
                            $('#card_SelectRoom4Comparison').show();
                            newText = selectedRowData["Raumbezeichnung"] + ": " + selectedRowData["Bezeichnung"] + ' <i class="fa fa-caret-up"> </i>';
                            $('#SelectRoomBtn').html(newText);
                        }
                    }
                });

                $('#SelectVglRoomBtn').on('click', function () {
                    var selectedRowData = table_vgl_rooms.row('.selected').data();
                    if ($("#card_body_vgl_room").is(":visible")) {
                        if (selectedRowData) {
                            $('#card_body_vgl_room').hide();
                            $('#card_header_vgl_room').hide();
                            $('#card_vgl_room').hide();
                            newText = selectedRowData["Raumbezeichnung"] + ": " + selectedRowData["Bezeichnung"] + ' <i class="fa fa-caret-down"> </i>';
                            $('#SelectVglRoomBtn').html(newText);
                            console.log("both hidden, RID2: " + RID2);
                            both_hidden = true;
                        }
                    } else {
                        both_hidden = false;
                        $('#card_body_vgl_room').show();
                        $('#card_header_vgl_room').show();
                        $('#card_vgl_room').show();
                        if (selectedRowData) {
                            newText = selectedRowData["Raumbezeichnung"] + ": " + selectedRowData["Bezeichnung"] + ' <i class="fa fa-caret-up"> </i>';
                            $('#SelectVglRoomBtn').html(newText);
                        }
                    }
                });
            }

            function get_el_in_room_table(RaumID, targetDiv) {
//                console.log("Get Elements in Room Table");
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

            function add_MT_rel_filter(location, table) {
                var dropdownHtml = '<select class="form-control-sm fix_size" id="columnFilter">' + '<option value="">MT</option><option value="Ja">Ja</option>' + '<option value="Nein">Nein</option></select>';
                $(location).append(dropdownHtml);
                $('#columnFilter').change(function () {
                    var filterValue = $(this).val();
                    table.column('MT-relevant:name').search(filterValue).draw();
                });
            }

            function move_dt_search(item2move_id, where2move_id) {
                var dt_searcher = document.getElementById(item2move_id);
                dt_searcher.parentNode.removeChild(dt_searcher);
                document.getElementById(where2move_id).appendChild(dt_searcher);
                dt_searcher.classList.add("fix_size");
            }
 
        </script>
    </body> 
</html>
