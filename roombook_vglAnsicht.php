<?php
session_start();
include '_utils.php';
init_page_serversides();
?> 

<!DOCTYPE html>
<html data-bs-theme="dark" xmlns="http://www.w3.org/1999/xhtml" >
    <head>
        <title>RB-Raumvergleich</title>
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
            <link rel="stylesheet" href="style.css" type="text/css" media="screen" />
            <!--  <link rel="icon" href="iphone_favicon.png">
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
                            .card {
                                transition: transform 0.5s ease;
                                /*flex: 1;  Initial equal width for all cards */
                                border: 1px solid #ccc;
                                /*padding: 10px;*/
                            }
                            .card-header{
                                height: 50px;
                            }
                            .resize-border {
                                width: 5px; /* Width of the border area */
                                cursor: col-resize; /* Cursor style for resizing */
                                z-index: 99;
                                color:white;
                                background-color:white;
                            }
                            .container {
                                display: flex;
                                overflow: hidden;
                            }

                            .flex-grow-1.visible {
                                display: block;
                                /*width: 24vw;*/
                                overflow: hidden;
                            }
                            .flex-grow-1.hidden {
                                display: none;
                                transform: translateX(-100%);
                                /*max-width: 1vw;*/
                                transition: transform 0.3s ease, max-width 0.3s ease;
                            }
                            .btn {
                                border: 1px grey;
                                padding-right: 3px;
                                padding-left: 3px;
                                /* Other button styles (background color, padding, etc.) */
                            }
                            .btn:hover {
                                margin-top: 0;
                                box-shadow: 0 0 4px 1px grey; /* Add a small drop shadow on hover */
                            }
                            .fix_size{
                                height: 30px;
                            }
                            .table {
                                border: 1px solid grey ;
                                width: 100% !important;
                            }
                            .table-container {
                                position: sticky;
                                top: 0;
                                max-height: 80vh;
                                overflow-y: auto;
                            }
                        </style>
                        </head> 
                        <body style="height:100%">  <div class ="container-fluid" id="KONTÄNER">
                                <div id="limet-navbar" class=' '> </div>  
                                <div class="card responsive border-dark"> 
                                    <div class="card-header border-success d-inline-flex" id="TableCardHeader"> 
                                        <div class="col-sm-2 align-items-end" >  
                                        </div>
                                        <div class="col-sm-4 d-inline-flex align-items-end justify-content-end"> 
                                            <button id="SelectRoomBtn" class="btn"  style=" background-color:  rgba(100, 140, 25, 0.1);"> Select room </button> 
                                        </div>
                                        <!--   <select class="fix_size" id="data-key-select">
                                       <option value="TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen">Funktionsstelle</option>
                                             <option value="Raumbezeichnung">Raumbezeichnung</option>
                                             <option value="Raumnr">Raumnummer</option>
                                       <option value="Nummer">DIN13080s</option> 
                                        </select>
                                        <select class ="fix_size" id="table2-select">
                                       <option value="TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen"> Vergleichs Räume</option>
                                          <option value="Raumbezeichnung">Raumbezeichnung</option>
                                          <option value="Raumnr">Raumnummer</option>
                                          <option value="Nummer">DIN13080</option> 
                                        </select></div>  -->

                                        <div class="col-sm-4 d-inline-flex  align-items-end justify-content-start" >  
                                            <button id="SelectVglRoomBtn" class="btn" style=" background-color: rgba(13, 202, 240, 0.1); float: right;">Select Vgl room </button> 
                                        </div>  <div class="col-sm-2x align-items-end" > 
                                        </div>
                                    </div>

                                    <div class=" d-inline-flex" id="table_room_cb">   
                                        <div class="card  cardx border-success flex-grow-1" style="width: 10vw" >  
                                            <div class="card-header d-inline-flex justify-content-center" style=" background-color:  rgba(100, 140, 25, 0.1);" >ROOM ELEMENTs</div> 
                                            <div class="card-body"  id="RoomElements"> </div>
                                        </div>

                                        <div class="resize-border"></div>

                                        <div class="card cardx border-success flex-grow-1"  style="width: 40vw;" id="card_SelectRoom4Comparison">
                                            <div class="card-header d-inline-flex justify-content-center" id="SelectRoom4Comparison" style=" background-color:  rgba(100, 140, 25, 0.1);" >  </div> 
                                            <div class="card-body " id="cardbody_SelectRoom4Comparison">  
                                                <!--<div class="table-container">-->
                                                <table class="table  compact table-responsive table-striped table-lg" id="table_rooms"> 
                                                    <thead><tr></tr></thead>
                                                    <tbody><td></td></tbody> 
                                                </table> 
                                                <!--</div>-->    
                                            </div> 
                                        </div> 

                                        <div class="resize-border"></div>

                                        <div class="card cardx border-info flex-grow-1"  style="width: 40vw" id="card_vgl_room">  
                                            <div class="card-header d-inline-flex justify-content-center" id="card_header_vgl_room"  style=" background-color: rgba(13, 202, 240, 0.1)">  VGL ROOMs</div> 
                                            <div class="card-body " id ="card_body_vgl_room">
                                                <div class="table-container">
                                                    <table class="table  compact table-responsive table-striped table-lg" id="table_vgl_rooms" > 
                                                        <thead><tr></tr></thead>
                                                        <tbody><td></td></tbody>
                                                    </table>  
                                                </div>   
                                            </div>

                                        </div>  

                                        <div class="resize-border"></div>

                                        <div class="card cardx border-info flex-grow-1" style="width: 10vw" >  
                                            <div class="card-header d-inline-flex justify-content-center" style=" background-color: rgba(13, 202, 240, 0.1)"> VGL ROOM ELEMENTs </div> 
                                            <div class="card-body responsive" id ="">  
                                                <p id="ElememntsVglRoom" > </p> 
                                            </div>
                                        </div> 
                                    </div> 
                                </div>
                            </div>

                            <script>




                                $(document).ready(function () {
                                    init_rooms_current_project_table();
                                    add_MT_rel_filter('#SelectRoom4Comparison');
                                    move_dt_search("dt-search-0", 'SelectRoom4Comparison');
                                    selectRoomBtn(); //showRoomElementsBtn(); selectVglRoomBtn();
                                    table_click("table_rooms", "RoomElements");
                                    table_click("table_vgl_rooms", "ElememntsVglRoom");
                                });

                                const container = document.querySelector('#KONTÄNER');
                                const cards = document.querySelectorAll('.cardx');
                                const resizeBorders = document.querySelectorAll('.resize-border');
                                let isResizing = false;
                                let startX, startWidth;
                                function startResize(e) {
                                    isResizing = true;
                                    startX = e.clientX;
                                    startWidth = parseFloat(getComputedStyle(cards[1]).width);
                                }

                                function resize(e) {
                                    if (!isResizing)
                                        return;
                                    const deltaX = e.clientX - startX;
                                    const newWidth = startWidth + deltaX;

                                    cards[1].style.width = `${newWidth}px`;
                                    const adjacentWidth = (container.clientWidth - newWidth) / 3;
                                    cards[0].style.width = `${adjacentWidth}px`;
                                    cards[2].style.width = `${adjacentWidth}px`;
                                    cards[3].style.width = `${adjacentWidth}px`;
                                }

                                function stopResize() {
                                    isResizing = false;
                                }

                                resizeBorders.forEach(border => {
                                    border.addEventListener('mousedown', startResize);
                                });
                                window.addEventListener('mousemove', resize);
                                window.addEventListener('mouseup', stopResize);

                                var table_rooms;
                                var RID1;
                                var table_vgl_rooms;
                                var RID2;
                                let both_hidden = false;
                                let selectedDataKey = 'TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen';
                                //   dataKeySelect.addEventListener('change', function () {
                                //  selectedDataKey = dataKeySelect.value;
                                //  console.log('Selected Data Key:', selectedDataKey);
                                //   }); 
                                //   const dataKeySelect = 'TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen';// document.getElementById('data-key-select');
                                var newText = "";
                                var selectedRowData = 0;
                                var filter_added = false;
                                const columnsDefinitionShort = [
                                    {data: 'tabelle_projekte_idTABELLE_Projekte', title: 'Projek ID', visible: false, searchable: false},
                                    {data: 'idTABELLE_Räume', title: 'Raum ID', visible: false, searchable: false},
                                    {data: 'TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen', title: 'Funktionsstellen ID', visible: false, searchable: false},
                                    {data: 'MT-relevant', title: 'MT', name: 'MT-relevant', case: "bit", render: function (data) {
                                            return data === '1' ? 'Ja' : 'Nein';
                                        }},
                                    {data: 'Raumbezeichnung', title: 'Raumbez.'},
                                    {data: 'Raumnr', title: 'Raumnr'},
                                    {data: "Bezeichnung", title: "Funktionsstelle", case: "none-edit"},
                                    {data: 'Nummer', title: "DIN13080", case: "none-edit"}
                                ];
                                const columnsDefinitionShort2 = [
                                    {data: 'tabelle_projekte_idTABELLE_Projekte', title: 'Projek ID', visible: false, searchable: false},
                                    {data: 'idTABELLE_Räume', title: 'Raum ID', visible: false, searchable: false},
                                    {data: 'TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen', title: 'Funktionsstellen ID', visible: false, searchable: false},
                                    {data: 'Raumbezeichnung', title: 'Raumbez.'},
                                    {data: 'Raumnr', title: 'Raumnr'},
                                    {data: 'Nummer', title: "DIN13080", case: "none-edit"},
                                    {data: 'MT-relevant', title: 'MT', name: 'MT-relevant', case: "bit", render: function (data) {
                                            return data === '1' ? 'Ja' : 'Nein';
                                        }}
                                ];

                                function table_click(table_id, target_id_4_elelemt_table) {
                                    $("#" + table_id).on('click', "tr", function () {
                                        if (table_id === "table_rooms") {
                                            var selectedRowData = table_rooms.row('.selected').data();
                                            if (selectedRowData) {
                                                newText = selectedRowData["Raumbezeichnung"] + ": " + selectedRowData["Bezeichnung"] + ' <i class="fa fa-caret-up"> </i>';
                                                RID1 = selectedRowData["idTABELLE_Räume"];
                                                $('#SelectRoomBtn').html(newText);
                                            }
                                        }
                                        if (table_id === "table_vgl_rooms") {
                                            selectedRowData = table_vgl_rooms.row('.selected').data();
                                            RID2 = selectedRowData["idTABELLE_Räume"];
                                        }
                                        if (selectedRowData) {
                                            get_el_in_room_table(selectedRowData['idTABELLE_Räume'], target_id_4_elelemt_table);
                                            if (table_id === "table_rooms") {
                                                value = selectedRowData[selectedDataKey];
                                                init_vgl_rooms_table(selectedDataKey, value);
                                            }
                                        }
                                    });
                                }

                                function selectRoomBtn() {
                                    $('#SelectRoomBtn').on('click', function () {
                                        var selectedRowData = table_rooms.row('.selected').data();
                                        if (selectedRowData) {
                                            if ($("#cardbody_SelectRoom4Comparison").is(":visible")) {
                                                $('#cardbody_SelectRoom4Comparison').hide();
                                                $('#SelectRoom4Comparison').hide();
                                                $('#card_SelectRoom4Comparison').hide();
                                                newText = selectedRowData["Raumbezeichnung"] + ": " + selectedRowData["Bezeichnung"] + ' <i class="fa fa-caret-down"> </i>';
                                                $('#SelectRoomBtn').html(newText);
                                                if (!$("#card_body_vgl_room").is(":visible")) {
                                                    console.log("both hidden, RID1: " + RID1);
                                                    both_hidden = true;
                                                    
                                                    $('#tableRoomElements' + RID1 + ' tr').each(function () {
                                                        var customerId = $(this).find('.customIDCell').html();
                                                        console.log(customerId);
                                                    });

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
                                        if (selectedRowData) {
                                            if ($("#card_body_vgl_room").is(":visible")) {
                                                $('#card_body_vgl_room').hide();
                                                $('#card_header_vgl_room').hide();
                                                $('#card_vgl_room').hide();
                                                newText = selectedRowData["Raumbezeichnung"] + ": " + selectedRowData["Bezeichnung"] + ' <i class="fa fa-caret-down"> </i>';
                                                $('#SelectVglRoomBtn').html(newText);
                                                if (!$("#cardbody_SelectRoom4Comparison").is(":visible")) {
                                                    console.log("both hidden, RID2: " + RID2);
                                                    both_hidden = true;
                                                }
                                            } else {
                                                both_hidden = false;
                                                $('#card_body_vgl_room').show();
                                                $('#card_header_vgl_room').show();
                                                $('#card_vgl_room').show();
                                                newText = selectedRowData["Raumbezeichnung"] + ": " + selectedRowData["Bezeichnung"] + ' <i class="fa fa-caret-up"> </i>';
                                                $('#SelectVglRoomBtn').html(newText);
                                            }
                                        }
                                    });
                                }

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
                                                    //    $("#" + targetDiv).html();
                                                    $("#" + targetDiv).html(data);
                                                }
                                            });
                                        }
                                    });
                                }

                                function  init_vgl_rooms_table(key, value) {
                                    let dom_var = "ti";
                                    if (table_vgl_rooms) {
                                        table_vgl_rooms.destroy();
//                                        console.log("Existing Table Dstroyed.... ");
                                    }
                                    if (!filter_added) {
                                        dom_var = "fti ";
                                    }
                                    table_vgl_rooms = new DataTable('#table_vgl_rooms', {
                                        ajax: {
                                            url: 'get_rooms_with_param.php',
                                            data: {"key": key, "value": value},
                                            dataSrc: ''
                                        },
                                        columns: columnsDefinitionShort2,
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
                                        compact: true
                                    });
//                                    console.log(" -> VGL Räume TABLE LOADED! ");
                                    if (!filter_added) {
                                        add_MT_rel_filter2("#card_header_vgl_room");
                                        move_dt_search("dt-search-1", "card_header_vgl_room");
                                        filter_added = true;
                                    }
                                }

                                function init_rooms_current_project_table() {
                                    table_rooms = new DataTable('#table_rooms', {
                                        ajax: {
                                            url: 'get_room_per_project_param_short.php',
                                            dataSrc: ''
                                        },
                                        columns: columnsDefinitionShort,
                                        dom: '<"TableCardHeader"f>t<"btm.d-flex justify-content-between"lip> ',
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

                                function add_MT_rel_filter2(location) {
                                    var dropdownHtml = '<select class="form-control-sm fix_size" id="columnFilter">' + '<option value="">MT</option><option value="Ja">Ja</option>' + '<option value="Nein">Nein</option></select>';
                                    $(location).append(dropdownHtml);
                                    $('#columnFilter').change(function () {
                                        var filterValue = $(this).val();
                                        table_vgl_rooms.column('MT-relevant:name').search(filterValue).draw();
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

                                function move_dt_search(item2move_id, where2move_id) {
                                    var dt_searcher = document.getElementById(item2move_id);
                                    dt_searcher.parentNode.removeChild(dt_searcher);
                                    document.getElementById(where2move_id).appendChild(dt_searcher);
                                    dt_searcher.classList.add("fix_size");
                                }
                            </script>
                        </body> 
                        </html>
