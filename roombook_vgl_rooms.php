<?php
session_start();
include '_utils.php';
init_page_serversides();
?>  


<html data-bs-theme="dark" xmlns="http://www.w3.org/1999/xhtml">
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
            :root {
                --height: 35px;
            }
            .table {
                width: 100% !important;
            }
            .table > th {
                width: 100% !important;
            }
            .dt-button {
                height: var(--height) !important;
            }
            .fix_size {
                height: var(--height) !important;
            }

            .dt-input {
                height: var(--height);
                width: 65px;
            }
            .fa-search {
                font-size: 14px;
            }

        </style>
    </head>            

    <body style="height:100%">
        <div id="limet-navbar" class=''> </div>  
        <div class="container-fluid">
            <!-- SelectRoomCard -->
            <div class="card" id="SelectRoomCard">
                <div class="card-header d-flex justify-content-center align-items-start"  style="height: 45px;border-bottom-color:  rgb(246, 247, 247); padding: 0px; " >
                    <div class="col-3 d-flex justify-content-start align-items-center" id="CardHeader1"> 
                        <!--<button id="SelectRoomBtn" class="btn"  style=" background-color:  rgba(100, 140, 25, 0.1);float:right;">  <i class="fa fa-caret-up">  </i> Select a Room </button>-->  
                    </div>
                    <div class="col-6 d-flex justify-content-center align-items-center">
                        <button class="btn btn-link" id="toggleLeft">
                            <i class="fas fa-arrow-left"></i>
                        </button>
                        <button class="btn btn-link" id="toggleCardBody">
                            <i class="fas fa-arrow-up"></i>
                        </button>
                        <!--                        <button class="btn btn-link" id="toggleRight">
                                                    <i class="fas fa-arrow-right"></i>
                                                </button>-->
                    </div>
                    <div class="col-3 d-flex justify-content-end align-items-center">
                        <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#settingsModal">
                            <i class="fas fa-cog"></i>
                        </button>
                    </div>
                </div>

                <div class="card-header d-flex justify-content-center align-items-end"   style="height: 35px; border-top-color:  rgb(246, 247, 247); padding: 0px;  " >
                    <div class="col-4 d-flex justify-content-start align-items-top" id="subheaderL"></div>      
                    <div class="col-4 d-flex justify-content-center align-items-top" id="subheaderM"></div>
                    <div class="col-4 d-flex justify-content-end align-items-top" id="subheaderR"></div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="card col-6" id="subCard1">  
                            <table class="table compact table-responsive table-striped table-lg" id="table_rooms"></table> 
                        </div>
                        <div class="card col-6" id="subCard2">
                            <table class="table compact table-responsive table-striped table-lg" id="table_rooms_vgl"></table> 
                        </div>
                    </div>
                </div>
            </div>

            <!-- MTcard -->
            <div class="card mt-3" id="MTcard">
                <div class="card-header">
                    <h5 class="mb-0">MT Card</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6" id="subCard3"></div>
                        <div class="col-6" id="subCard4"></div>
                    </div>
                </div>
            </div>

            <!-- anzahl karte -->
            <div class="card mt-3" id="EAcard">
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
            </div>
        </div>

        <!-- Settings Modal -->
        <div class="modal fade" id="settingsModal" tabindex="-1" aria-labelledby="settingsModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="settingsModalLabel">Settings</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="settings_save_state">
                                <label class="form-check-label" for="settings_save_state"> Save Room table1 state </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="showMTCard">
                                <label class="form-check-label" for="showMTCard">Show MT Card</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="showEACard">
                                <label class="form-check-label" for="showEACard">Show Elemente Anazahl Card</label>
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
        </div>
        <script src="roombookSpecifications_constDeclarations.js"></script> 
        <script>
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
            /*            const newEntry = {
             //                data: 'Projektname',
             //                title: 'Projektname'
             //            };
             //
             //            columnsDefinition.shift();
             //            var X = columnsDefinition.shift();
             //            columnsDefinition.unshift(newEntry);
             //            columnsDefinition.unshift(X);
             //            let columnsDefinitionShort = columnsDefinition.filter(column =>
             //                ["idTABELLE_Räume", 'TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen', 'MT-relevant', 'Raumbezeichnung', 'Raumnr', "Bezeichnung", 'Nummer', 'Raumbereich Nutzer', 'Geschoss', 'Bauetappe'].includes(column.data)
             //            ).map(column => {
             //                if (!["idTABELLE_Räume", 'TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen', 'tabelle_projekte_idTABELLE_Projekte'].includes(column.data)) {
             //                    column.visible = true;
             //                }
             //                return column;
             //            });
             //            let columnsDefinitionShort_vgl = columnsDefinition.filter(column =>
             //                ['Projektname', "idTABELLE_Räume", 'TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen', 'MT-relevant',
             //                    'Raumbezeichnung', 'Raumnr', "Bezeichnung", 'Raumbereich Nutzer'].includes(column.data)); //, 'Geschoss', 'Bauetappe', 'Bauabschnitt'*/

            const columnsAnzahl = [
                {data: "AnzahlvonAnzahl"},
                {data: "SummevonAnzahl"},
                {data: "ElementID"},
                {data: "Bezeichnung"},
                {data: "Projektname"},
                {data: "TABELLE_Elemente_idTABELLE_Elemente", visible: false}];

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
                ["idTABELLE_Räume", 'TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen', 'MT-relevant', 'Raumbezeichnung', 'Raumnr', "Bezeichnung", 'Nummer', 'Raumbereich Nutzer', 'Bauetappe'].includes(column.data)
            ).map(column => {
                if (!["idTABELLE_Räume", 'TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen', 'tabelle_projekte_idTABELLE_Projekte'].includes(column.data)) {
                    column.visible = true;
                }
                return column;
            });


            // ---  FUNCTIONS  ---  
            $(document).ready(function () {
                init_table_rooms();
                add_MT_rel_filter("#subheaderL");
                move_dt_search("dt-search-0", "subheaderL");
                table_click("table_rooms", "subCard3");
                new $.fn.dataTable.Buttons(table_rooms, {buttons: dt_btn_colsvis}).container().appendTo($('#subheaderM'));
                new $.fn.dataTable.Buttons(table_rooms, {buttons: dt_btn_search}).container().appendTo($('#subheaderL'));
//                syncScroll();
            });

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

            function createColvisButton() {
                return {
                    extend: 'colvis',
                    text: 'Vis',
                    action: function (e, dt, node, config) {
                        var columns = dt.columns(':gt(6)');
                        columns.visible(!columns.visible()[0]);
                        table_rooms_vgl.columns(':gt(6)').visible(!table_rooms_vgl.columns(':gt(6)').visible()[0]);
                    },
                    className: 'btn'
                };
            }

//            function syncScroll() {
//                setTimeout(function () {
//                    console.log('Setting up scroll synchronization');
//                    var scrollBody1 = $('#subCard1.dataTables_scrollBody');
//                    var scrollBody2 = $('#subCard2.dataTables_scrollBody');
//
//                    console.log('Scroll bodies:', scrollBody1.length, scrollBody2.length);
//
//                    scrollBody1.on('scroll', function () {
//                        console.log('Table 1 scrolled');
//                        scrollBody2.scrollLeft($(this).scrollLeft());
//                    });
//                    scrollBody2.on('scroll', function () {
//                        console.log('Table 2 scrolled');
//                        scrollBody1.scrollLeft($(this).scrollLeft());
//                    });
//                }, 200);
//            }

            function init_dt_anzahl(value) {
                if (table_elemente_anzahl) {
                    table_elemente_anzahl.destroy();
                }
                table_elemente_anzahl = new DataTable('#table_el_anzahl', {
                    ajax: {
                        url: 'get_anzahl_elmente_per_funktionsstelle.php',
                        data: {"value": value},
                        dataSrc: ''
                    },
                    columns: columnsAnzahl,
                    paging: false,
                    search: false
                });
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
                        if (showMTCard) {
                            get_el_in_room_table(selectedRowData['idTABELLE_Räume'], target_id_4_elelemt_table);
                        }
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

            function add_MT_rel_filter(location) {
                var dropdownHtml = '<select class=" fix_size" id="columnFilter1">' +
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

            function init_table_rooms() {
                console.log(settingsSaveState);
                table_rooms = new DataTable('#table_rooms', {
                    ajax: {
                        url: 'get_rb_specs_data.php',
                        dataSrc: ''
                    },
                    columns: columnsDefinitionShort,
                    dom: 'ft<"btm.d-flex justify-content-between"lip>',
//                  
                    language: {
                        "search": "", "info": "Total entries: _TOTAL_"
                    },
                    stateSave: settingsSaveState,
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
                    dom_var = 'ft<"btm.d-flex justify-content-between"lip>';
                }

                console.log("VISIBLE: ", getVisibleColumns());
//                console.log(updateColumnsVisibility(columnsDefinition, getVisibleColumns()));
                let newEntry = {data: "Projektname", Title: "Projekt", visible: true};
                let visible_columns_table_rooms = updateColumnsVisibility(columnsDefinitionShort, getVisibleColumns());
                visible_columns_table_rooms.unshift(newEntry);

                table_rooms_vgl = new DataTable('#table_rooms_vgl', {
                    ajax: {
                        url: 'get_rooms_with_funktionsteilstelle.php',
                        data: {"value": value},
                        dataSrc: ''
                    },
                    columns: visible_columns_table_rooms,
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
                            table_click("table_rooms_vgl", "subCard4");
                            new $.fn.dataTable.Buttons(table_rooms_vgl, {buttons: dt_btn_colsvis});//.container().appendTo($('#subheaderR'));
                            new $.fn.dataTable.Buttons(table_rooms_vgl, {buttons: dt_btn_search}).container().appendTo($('#subheaderR'));

                            table_rooms.on('column-visibility', function (e, settings, colIdx, visibility) {
                                table_rooms_vgl.column(colIdx).visible(visibility);
                            });

                            table_rooms_vgl.on('column-visibility', function (e, settings, colIdx, visibility) {
                                table_rooms.column(colIdx).visible(visibility);
                            });
                        }
//                        syncScroll();

                    }});
            }


            //  --- DESIGN SECTION  --- 
            // Global Variables
            var showMTCard = localStorage.getItem('showMTCard') === 'true';
            var showEACard = localStorage.getItem('showEACard') === 'true';
            var settingsSaveState = localStorage.getItem('settings_save_state') === 'true';
            var toggleSubCards = localStorage.getItem('toggleSubCards') === 'true';
            var mtCard = document.getElementById('MTcard');
            var eaCard = document.getElementById('EAcard');
            var selectRoomCardBody = document.querySelector('#SelectRoomCard .card-body');
            var subCard1 = document.getElementById('subCard1');
            var subCard2 = document.getElementById('subCard2');
            var toggleCardBodyIcon = document.querySelector('#toggleCardBody i');
            var toggleLeftIcon = document.querySelector('#toggleLeft i');
//            var toggleRightIcon = document.querySelector('#toggleRight i');

            // Initialize Settings
            document.getElementById('settings_save_state').checked = settingsSaveState;
            document.getElementById('showMTCard').checked = showMTCard;
            document.getElementById('showEACard').checked = showEACard;
            document.getElementById('toggleSubCards').checked = toggleSubCards;
            mtCard.style.display = showMTCard ? 'block' : 'none';
            eaCard.style.display = showEACard ? 'block' : 'none';
            applySubCardSettings();
            document.getElementById('saveSettings').addEventListener('click', saveSettings);


            function saveSettings() {
                localStorage.setItem('showMTCard', $('#showMTCard').is(':checked'));
                localStorage.setItem('showEACard', $('#showEACard').is(':checked'));
                localStorage.setItem('toggleSubCards', $('#toggleSubCards').is(':checked'));
                localStorage.setItem('settings_save_state', $('#settings_save_state').is(':checked'));
                $('#mtCard').css('display', showMTCard ? 'block' : 'none');
                $('#eaCard').css('display', showEACard ? 'block' : 'none');
                applySubCardSettings();
                window.location.reload();
            }

// Updated JavaScript Code

            function applySubCardSettings() {
                if (toggleSubCards) {
                    subCard1.classList.add('col-12');
                    subCard2.classList.add('col-12');
                    subCard1.classList.remove('col-6');
                    subCard2.classList.remove('col-6');
                } else {
                    subCard1.classList.add('col-6');
                    subCard2.classList.add('col-6');
                    subCard1.classList.remove('col-12');
                    subCard2.classList.remove('col-12');
                }
            }

            document.getElementById('toggleCardBody').addEventListener('click', function () {
                toggleVisibility(selectRoomCardBody, toggleCardBodyIcon, 'fa-arrow-up', 'fa-arrow-down');
            });

            document.getElementById('toggleLeft').addEventListener('click', function () {
                toggleSubCard(subCard1, toggleLeftIcon, 'fa-arrow-left', 'fa-arrow-right');
            });

            function toggleVisibility(element, icon, addClass, removeClass) {
                element.style.display = element.style.display === 'none' ? 'block' : 'none';
                icon.classList.toggle(addClass);
                icon.classList.toggle(removeClass);
            }

            function toggleSubCard(element, icon, addClass, removeClass) {
                element.style.display = element.style.display === 'none' ? 'block' : 'none';
                icon.classList.toggle(addClass);
                icon.classList.toggle(removeClass);
                if(!toggleSubCards){
                if (subCard2.classList.contains('col-12')) {
                    subCard2.classList.remove('col-12');
                } else {
                    subCard2.classList.add('col-12');
                }}

            }



            /*
             function applySubCardSettings() {
             if (toggleSubCards) {
             document.querySelectorAll('#SelectRoomCard .card-body .row').forEach(row => {
             row.classList.remove('row');
             row.classList.add('d-flex', 'flex-column');
             });
             subCard1.classList.add('col-12');
             subCard2.classList.add('col-12');
             //                        document.getElementById('toggleLeft').style.display = 'none';
             //                        document.getElementById('toggleRight').style.display = 'none';
             } else {
             document.querySelectorAll('#SelectRoomCard .card-body .d-flex.flex-column').forEach(row => {
             row.classList.add('row');
             row.classList.remove('d-flex', 'flex-column');
             });
             subCard1.classList.remove('col-12');
             subCard2.classList.remove('col-12');
             subCard1.classList.add('col-6');
             subCard2.classList.add('col-6');
             //                        document.getElementById('toggleLeft').style.display = 'inline-block';
             //                        document.getElementById('toggleRight').style.display = 'inline-block';
             }
             }
             
             document.getElementById('toggleCardBody').addEventListener('click', function () {
             toggleVisibility(selectRoomCardBody, toggleCardBodyIcon, 'fa-arrow-up', 'fa-arrow-down');
             toggleArrows();
             });
             document.getElementById('toggleLeft').addEventListener('click', function () {
             toggleSubCard(subCard1, toggleLeftIcon, 'fa-arrow-left', 'fa-arrow-right');
             toggleArrows();
             });
             document.getElementById('toggleRight').addEventListener('click', function () {
             toggleSubCard(subCard2, toggleRightIcon, 'fa-arrow-right', 'fa-arrow-left');
             toggleArrows();
             });
             function toggleArrows() {
             const subCard1Visible = subCard1.style.display !== 'none';
             const subCard2Visible = subCard2.style.display !== 'none';
             const cardBodyVisible = selectRoomCardBody.style.display !== 'none';
             if (!cardBodyVisible) {
             subCard1.style.display = 'block';
             subCard2.style.display = 'block';
             subCard1.classList.add('col-6');
             subCard2.classList.add('col-6');
             }
             
             toggleCardBodyIcon.classList.toggle('fa-arrow-down', !subCard1Visible && !subCard2Visible);
             toggleCardBodyIcon.classList.toggle('fa-arrow-up', subCard1Visible || subCard2Visible);
             }
             
             function toggleVisibility(element, icon, addClass, removeClass) {
             element.style.display = element.style.display === 'none' ? 'block' : 'none';
             icon.classList.toggle(addClass);
             icon.classList.toggle(removeClass);
             toggleLeftIcon.classList.toggle('fa-arrow-right', element.style.display === 'none');
             toggleLeftIcon.classList.toggle('fa-arrow-left', element.style.display !== 'none');
             toggleRightIcon.classList.toggle('fa-arrow-left', element.style.display === 'none');
             toggleRightIcon.classList.toggle('fa-arrow-right', element.style.display !== 'none');
             }
             
             function toggleSubCard(element, icon, addClass, removeClass) {
             var otherSubCard = element === subCard1 ? subCard2 : subCard1;
             var subCard1Visible = subCard1.style.display !== 'none';
             var subCard2Visible = subCard2.style.display !== 'none';
             if (!subCard1Visible && !subCard2Visible) {
             selectRoomCardBody.style.display = 'block';
             element.style.display = 'block';
             element.classList.add('col-6');
             icon.classList.remove(addClass);
             icon.classList.add(removeClass);
             toggleCardBodyIcon.classList.remove('fa-arrow-down');
             toggleCardBodyIcon.classList.add('fa-arrow-up');
             } else {
             element.style.display = element.style.display === 'none' ? 'block' : 'none';
             icon.classList.toggle(addClass);
             icon.classList.toggle(removeClass);
             otherSubCard.classList.toggle('col-12', element.style.display === 'none');
             otherSubCard.classList.toggle('col-6', element.style.display !== 'none');
             subCard1Visible = subCard1.style.display !== 'none';
             subCard2Visible = subCard2.style.display !== 'none';
             selectRoomCardBody.style.display = subCard1Visible || subCard2Visible ? 'block' : 'none';
             toggleCardBodyIcon.classList.toggle('fa-arrow-down', !subCard1Visible && !subCard2Visible);
             toggleCardBodyIcon.classList.toggle('fa-arrow-up', subCard1Visible || subCard2Visible);
             }
             }*/

        </script>

    </body>

