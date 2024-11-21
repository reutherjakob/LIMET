<?php
session_start();
include '_utils.php';
init_page_serversides();
?>  

<html  xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>RB-Raumvergleich</title>
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
        <meta name="viewport" content="width=device-width, initial-scale=1"/>
        <link rel="stylesheet" href="style.css" type="text/css" media="screen" />
        <link rel="icon" href="iphone_favicon.png"/>  
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
        <!--<script src="https://kit.fontawesome.com/acb563d9db.js" crossorigin="anonymous"></script>-->
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css" integrity="sha512-q3eWabyZPc1XTCmF+8/LuE1ozpg5xxn7iO89yfSOd5/oKvyqLngoNGsx8jq92Y8eXJ/IRxQbEC+FGSYxtk2oiw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
            <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
            <script src="https://unpkg.com/@popperjs/core@2"></script> 

            <link href="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.0.5/af-2.7.0/b-3.0.2/b-colvis-3.0.2/b-html5-3.0.2/b-print-3.0.2/cr-2.0.1/date-1.5.2/fc-5.0.0/fh-4.0.1/kt-2.12.0/r-3.0.2/rg-1.5.0/rr-1.5.0/sc-2.4.1/sb-1.7.1/sp-2.3.1/sl-2.0.1/sr-1.4.1/datatables.min.css" rel="stylesheet"/>
            <script src="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.0.5/af-2.7.0/b-3.0.2/b-colvis-3.0.2/b-html5-3.0.2/b-print-3.0.2/cr-2.0.1/date-1.5.2/fc-5.0.0/fh-4.0.1/kt-2.12.0/r-3.0.2/rg-1.5.0/rr-1.5.0/sc-2.4.1/sb-1.7.1/sp-2.3.1/sl-2.0.1/sr-1.4.1/datatables.min.js"></script> 

            <style>
                .table,.table > th {
                    width: 100% !important;
                }
            </style>
    </head>            

    <body style="height:100%">
        <div id="limet-navbar" class=''> </div>  
        <div class="container-fluid"> 
            <div class="card" id="">
                <div class="card-header d-flex justify-content-center align-items-end"   style=" border-top-color:  rgb(246, 247, 247); padding: 2px;  " >
                    <div class="col-4 d-flex justify-content-start align-items-center" id ="Header1"></div>      
                    <div class="col-4 d-flex justify-content-center align-items-center" id="subheaderM">
                        <!-- <button class="btn btn-link" id="toggleLeft"> 
                          <i class="fa fa-arrow-left"></i>
                      </button>
                      <button class="btn btn-link" id="toggleCardBody"> 
                          <i class="fa fa-arrow-up"></i>
                      </button> -->
                    </div>
                    <div class="col-3 d-flex justify-content-end align-items-center"></div>
                    <div class="col-1 d-flex justify-content-end align-items-center">
                        <!--                        <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#settingsModal">
                                                    <i class="fas fa-cog"></i>
                                                </button>-->
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">  
                            <div class="card border-dark" id="subCard1">  
                                <div class="card-header d-inline-flex justify-content-start align-items-center border-dark" id="subheaderL">  </div>
                                <div class="card-body">
                                    <table class="table table-striped table-hover  compact responsive" id="table_rooms"></table> 
                                </div>                  
                            </div>
                        </div>
                        <div class="col-6">  
                            <div class="card border-dark" id="subCard2">
                                <div class="card-header d-inline-flex justify-content-end align-items-center border-dark"  id="subheaderR">  </div>
                                <div class="card-body">
                                    <table class="table table-striped table-hover  compact responsive" id="table_rooms_vgl"></table> 
                                </div>
                            </div>
                        </div>
                    </div>
                </div>          
            </div>

            <div class="card" id="MTcard">
                <div class="card-header d-flex justify-content-center align-items-end"   style=" border-top-color:  rgb(246, 247, 247); padding: 2px;  " >
                    <div class="col-4 d-flex justify-content-start align-items-center" id =""></div>      
                    <div class="col-4 d-flex justify-content-center align-items-center" id="">
                        <!-- <button class="btn btn-link" id="toggleLeft"> 
                          <i class="fa fa-arrow-left"></i>
                      </button>
                      <button class="btn btn-link" id="toggleCardBody"> 
                          <i class="fa fa-arrow-up"></i>
                      </button> -->
                    </div>
                    <div class="col-3 d-flex justify-content-end align-items-center"></div>
                    <div class="col-1 d-flex justify-content-end align-items-center">
                        <!--                        <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#settingsModal">
                                                    <i class="fas fa-cog"></i>
                                                </button>-->
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">  
                            <div class="card border-dark">  
                                <div class="card-header d-inline-flex justify-content-start align-items-center border-dark" id="">  </div>
                                <div class="card-body" id="subCard3">
                                    <!--<table class="table table-striped table-hover  compact responsive" id=""></table>--> 
                                </div>                  
                            </div>
                        </div>
                        <div class="col-6">  
                            <div class="card border-dark" ">
                                <div class="card-header d-inline-flex justify-content-end align-items-center border-dark"  id="">  </div>
                                <div class="card-body"id="subCard4">
                                    <!--<table class="table table-striped table-hover  compact responsive" id=""></table>-->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>          
            </div>



            <!-- anzahl karte -->
            <!--            <div class="card mt-3" id="EAcard">
                            <div class="card-header">
                                <h5 class="mb-0"> Anzahl Card</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6" id="subCard5">
                                        <table class="table compact table-responsive table-striped table-lg" id="table_el_anzahl"></table> </div>
                                    <div class="col-6" id="subCard6"> </div>
                                </div>
                            </div>
                        </div>-->


            <!-- Settings Modal -->
            <!--            <div class="modal fade" id="settingsModal" tabindex="-1" aria-labelledby="settingsModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="settingsModalLabel">Settings</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="" id="settings_save_state">
                                                <label class="form-check-label" for="settings_save_state"> Tabelle Räume in aktuellen Projekt: Konfiguration speichern </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="" id="showMTCard">
                                                <label class="form-check-label" for="showMTCard">Elemente im Raum KArte ANzeigen</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="" id="showEACard">
                                                <label class="form-check-label" for="showEACard">Anzahl der Elemente je Funktionsstelle</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="" id="toggleSubCards">
                                                <label class="form-check-label" for="toggleSubCards">Show Sub Cards Beneath Each Other</label>
                                        </div>
            
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="button" class="btn btn-primary" id="saveSettings">Save changes</button>
                                    </div>
                                </div>
                            </div>
                        </div>-->


            <script src="roombookSpecifications_constDeclarations.js"></script> 
            <script>
                // Global Variables
//                var showMTCard = localStorage.getItem('showMTCard') === 'true';
//                var showEACard = localStorage.getItem('showEACard') === 'true';
//                var settingsSaveState = localStorage.getItem('settings_save_state') === 'true';
//                var toggleSubCards = localStorage.getItem('toggleSubCards') === 'true';
//                var mtCard = document.getElementById('MTcard');
//                var eaCard = document.getElementById('EAcard');
//                var selectRoomCardBody = document.querySelector('#SelectRoomCard .card-body');
//                var subCard1 = document.getElementById('subCard1');
//                var subCard2 = document.getElementById('subCard2');
//                var toggleCardBodyIcon = document.querySelector('#toggleCardBody i');
//                var toggleLeftIcon = document.querySelector('#toggleLeft i');
//                // Initialize Settings
//                document.getElementById('settings_save_state').checked = settingsSaveState;
//                document.getElementById('showMTCard').checked = showMTCard;
//                document.getElementById('showEACard').checked = showEACard;
//                document.getElementById('toggleSubCards').checked = toggleSubCards;
//                mtCard.style.display = showMTCard ? 'block' : 'none';
//                eaCard.style.display = showEACard ? 'block' : 'none';
//                applySubCardSettings();
//                document.getElementById('saveSettings').addEventListener('click', saveSettings);

                // --- DATA SECTION --- 
                // Global data variables 
                var table_rooms;
                var table_rooms_vgl;
                let RID1;
                let RID2;
                var table_elemente;
                var table_elemente_vgl;
                var table_elemente_anzahl;
                var filter_added_vgl_rooms = false;

//                const columnsAnzahl = [
//                    {data: "AnzahlvonAnzahl"},
//                    {data: "SummevonAnzahl"},
//                    {data: "ElementID"},
//                    {data: "Bezeichnung"},
//                    {data: "Projektname"},
//                    {data: "TABELLE_Elemente_idTABELLE_Elemente", visible: false}];

                let dt_btn_colsvis = [
                    {
                        extend: 'colvis',
                        text: 'Vis',
                        columns: ':gt(5)',
                        collectionLayout: 'fixed columns',
                        className: 'btn'
                    }];

                let dt_btn_search = [
                    {extend: 'searchBuilder',
                        className: "btn fas fa-search",
                        text: "",
                        titleAttr: "Suche konfigurieren"
                    }];

                // TO DO -> let user decide initiation (all or short) 
                let columnsDefinitionShort = columnsDefinition.filter(column =>
                    ["idTABELLE_Räume", , 'TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen', 'tabelle_projekte_idTABELLE_Projekte', 'MT-relevant', 'Raumbezeichnung', 'Raumnr', "Bezeichnung", 'Raumbereich Nutzer', 'Nummer', 'Bauetappe'].includes(column.data)
                ).map(column => {
                    if (!["idTABELLE_Räume", 'TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen', 'tabelle_projekte_idTABELLE_Projekte', 'Nummer', 'Bauetappe'].includes(column.data)) {
                        column.visible = true;
                    } else {
                        column.visible = false;
                    }
                    return column;
                });

                $(document).ready(function () {
                    init_table_rooms();
                    add_MT_rel_filter("#Header1");
                    move_dt_search("dt-search-0", "subheaderL");

                    table_click("table_rooms", "subCard3");
                    new $.fn.dataTable.Buttons(table_rooms, {buttons: dt_btn_colsvis}).container().appendTo($('#subheaderL'));
                    new $.fn.dataTable.Buttons(table_rooms, {buttons: dt_btn_search}).container().appendTo($('#subheaderL'));
                    //                syncScroll();
                });


                function init_table_rooms() {
                    let savestate = false;//  settingsSaveState || false;  
                    table_rooms = new DataTable('#table_rooms', {
                        ajax: {
                            url: 'get_rb_specs_data.php',
                            dataSrc: ''
                        },
                        columns: columnsDefinitionShort,
                        dom: 'ft<"btm.d-flex justify-content-between"lip>',
                        language: {
                            "search": "", "info": "Total entries: _TOTAL_"
                        },
                        stateSave: savestate,
                        scrollY: false,
                        scrollX: true,
                        scrollCollapse: true,
                        select: {
                            style: "single",
                            info: false
                        },
                        keys: true,
                        paging: true,
                        pageLength: 10,
                        lengthMenu: [
                            [5, 10, 20, 50],
                            ['5 rows', '10 rows', '20 rows', '50 rows']
                        ],
                        compact: true
                    });
                }



                function init_vgl_rooms_table(value) {
                    let dom_var = 't<"btm.d-flex justify-content-between"lip>';
                    if (table_rooms_vgl) {
                        table_rooms_vgl.destroy();
                    }
                    if (!filter_added_vgl_rooms) {
                        dom_var = 'qft<"btm.d-flex justify-content-between"lip>';
                        let newEntry = {data: "Projektname", title: "Projekt", visible: true};
                        columnsDefinitionShort.push(newEntry);
                    }

//                    console.log("VISIBLE: ", getVisibleColumns());

                    table_rooms_vgl = new DataTable('#table_rooms_vgl', {
                        ajax: {
                            url: 'get_rooms_with_funktionsteilstelle.php',
                            data: {"value": value},
                            dataSrc: ''
                        },
                        columns: columnsDefinitionShort,
                        dom: dom_var,
                        language: {
                            "search": "", "info": "Total entries: _TOTAL_"},
                        scrollY: false,
                        scrollX: true,
                        scrollCollapse: true,
                        select: {
                            style: "single",
                            info: false
                        },
                        keys: true,
                        info: true,
                        paging: true,
                        pageLength: 10,
                        lengthMenu: [
                            [5, 10, 20, 50],
                            ['5 rows', '10 rows', '20 rows', '50 rows']
                        ],
                        compact: true,
                        initComplete: function () {
                            if (!filter_added_vgl_rooms) {
                                filter_added_vgl_rooms = true;
                                move_dt_search("dt-search-1", "subheaderR");
                            }
                            table_click("table_rooms_vgl", "subCard4");
                            new $.fn.dataTable.Buttons(table_rooms_vgl, {buttons: dt_btn_colsvis}).container().appendTo($('#subheaderR'));
                            new $.fn.dataTable.Buttons(table_rooms_vgl, {buttons: dt_btn_search}).container().appendTo($('#subheaderR'));

//                            table_rooms.on('column-visibility', function (e, settings, colIdx, visibility) {
//                                table_rooms_vgl.column(colIdx).visible(visibility);
//                            });
//
//                            table_rooms_vgl.on('column-visibility', function (e, settings, colIdx, visibility) {
//                                table_rooms.column(colIdx + 1).visible(visibility);
//                            });

                            //                        syncScroll();
                        }});
                }






                function add_MT_rel_filter(location) {
                    var dropdownHtml = '<select id="columnFilter1">' +
                            '<option value="">MT</option><option value="Ja">Ja</option>' +
                            '<option value="Nein">Nein</option></select>';
                    $(location).append(dropdownHtml);
                    $('#columnFilter1').change(applyFilters);
                }
                function move_dt_search(item2move_id, where2move_id) {
                    var dt_searcher = document.getElementById(item2move_id);
                    dt_searcher.parentNode.removeChild(dt_searcher);
                    document.getElementById(where2move_id).appendChild(dt_searcher);
                }









                function updateColumnsVisibility(columnsDefinition, visibleColumns) {
                    console.log(visibleColumns);
                    let updatedColumns = JSON.parse(JSON.stringify(columnsDefinition));
                    updatedColumns.forEach((column, index) => {
                        if (!visibleColumns.includes(index)) {
                            column.visible = false;
                        } else {
                            column.visible = true;
                            console.log("C data: ", column.data);
                        }

                    });
                    return updatedColumns;
                }
                function getVisibleColumns() {
                    var visibleColumns = [];
                    table_rooms.columns().every(function () {
                        if (this.visible()) {
                            visibleColumns.push(this.index());
                        }
                    });
                    return visibleColumns;
                }

                function table_click(table_id, target_id_4_elelemt_table) {
                    $(document).on('click', "#" + table_id + " tr", function () {
                        let selectedRowData;
                        if (table_id === "table_rooms") {

                            selectedRowData = table_rooms.row('.selected').data();
                            console.log("Klick", table_id, selectedRowData);
                            if (selectedRowData) {
                                newText = selectedRowData["Raumbezeichnung"] + ": " + selectedRowData["Bezeichnung"] + ": " + selectedRowData["TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen"] + ' <i class="fa fa-caret-up"> </i>';
                                RID1 = selectedRowData["idTABELLE_Räume"];
                                $('#SelectRoomBtn').html(newText);
                            }
                        }
                        if (table_id === "table_rooms_vgl") {
                            selectedRowData = table_rooms_vgl.row('.selected').data();
                            console.log(selectedRowData);
                            if (selectedRowData) {
                                RID2 = selectedRowData["idTABELLE_Räume"];
                                console.log("Currently save RID2", RID2);
                                newText = selectedRowData["Raumbezeichnung"] + ": " + selectedRowData["Bezeichnung"] + ": " + selectedRowData["TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen"] + ' <i class="fa fa-caret-up"> </i>';
                                $('#SelectVglRoomBtn').html(newText);
                            }
                        }
                        if (selectedRowData) {
                            value = selectedRowData["TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen"]
                            if (table_id === "table_rooms") {
                                init_vgl_rooms_table(value);
                                if (table_elemente_vgl) {
                                    table_elemente_vgl.destroy();
                                } else {
                                    $('#subCard4').empty();
                                }
                            }
                            //if (showMTCard) {
                            get_el_in_room_table(selectedRowData['idTABELLE_Räume'], target_id_4_elelemt_table);
                            //}
                            init_dt_anzahl(value);
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
                                    $("#" + targetDiv).html(data);
                                    if (targetDiv === "subCard3") {
                                        table_elemente = $("#tableRoomElements" + RaumID).DataTable();
                                    } else {
                                        table_elemente_vgl = $("#tableRoomElements" + RaumID).DataTable();
                                    }
                                }
                            });
                        }
                    });
                }

                function applyFilters() {
                    var filterValue = $('#columnFilter1').val();
                    table_rooms.column('MT-relevant:name').search(filterValue).draw();
                    table_rooms_vgl.column('MT-relevant:name').search(filterValue).draw();
                }



//                function init_dt_anzahl(value) {
//                    if (table_elemente_anzahl) {
//                        table_elemente_anzahl.destroy();
//                    }
//                    table_elemente_anzahl = new DataTable('#table_el_anzahl', {
//                        ajax: {
//                            url: 'get_anzahl_elmente_per_funktionsstelle.php',
//                            data: {"value": value},
//                            dataSrc: ''
//                        },
//                        columns: columnsAnzahl,
//                        paging: false,
//                        search: false
//                    });
//                }

                //  --- DESIGN SECTION  --- 
//                function saveSettings() {
//                    localStorage.setItem('showMTCard', $('#showMTCard').is(':checked'));
//                    localStorage.setItem('showEACard', $('#showEACard').is(':checked'));
//                    localStorage.setItem('toggleSubCards', $('#toggleSubCards').is(':checked'));
//                    localStorage.setItem('settings_save_state', $('#settings_save_state').is(':checked'));
//                    $('#mtCard').css('display', showMTCard ? 'block' : 'none');
//                    $('#eaCard').css('display', showEACard ? 'block' : 'none');
//                    applySubCardSettings();
//                    window.location.reload(true);
//
//                }
//                function applySubCardSettings() {
//                    if (toggleSubCards) {
//                        subCard1.classList.add('col-12');
//                        subCard2.classList.add('col-12');
//                        subCard1.classList.remove('col-6');
//                        subCard2.classList.remove('col-6');
//                    } else {
//                        subCard1.classList.add('col-6');
//                        subCard2.classList.add('col-6');
//                        subCard1.classList.remove('col-12');
//                        subCard2.classList.remove('col-12');
//                    }
//                }
//                document.getElementById('toggleCardBody').addEventListener('click', function () {
//                    toggleVisibility(selectRoomCardBody, toggleCardBodyIcon, 'fa-arrow-up', 'fa-arrow-down');
//                });
//                document.getElementById('toggleLeft').addEventListener('click', function () {
//                    toggleSubCard(subCard1, toggleLeftIcon, 'fa-arrow-left', 'fa-arrow-right');
//                });
//                function toggleVisibility(element, icon, addClass, removeClass) {
//                    element.style.display = element.style.display === 'none' ? 'block' : 'none';
//                    icon.classList.toggle(addClass);
//                    icon.classList.toggle(removeClass);
//                }
//                function toggleSubCard(element, icon, addClass, removeClass) {
//                    element.style.display = element.style.display === 'none' ? 'block' : 'none';
//                    icon.classList.toggle(addClass);
//                    icon.classList.toggle(removeClass);
//                    if (!toggleSubCards) {
//                        if (subCard2.classList.contains('col-12')) {
//                            subCard2.classList.remove('col-12');
//                        } else {
//                            subCard2.classList.add('col-12');
//                        }
//                    }
//                }
            </script>
    </body>

