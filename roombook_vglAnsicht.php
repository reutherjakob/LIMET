<<<<<<< Updated upstream
<?php
session_start();
include '_utils.php';
init_page_serversides();
?> 

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>RB-Vergleichsansicht</title>
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
        <meta name="viewport" content="width=device-width, initial-scale=1">

            <link rel="stylesheet" href="style.css" type="text/css" media="screen" />

            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous"/>
            <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css" integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous"/>

            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
            <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>
            <link href="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.0.5/af-2.7.0/b-3.0.2/b-colvis-3.0.2/b-html5-3.0.2/b-print-3.0.2/cr-2.0.1/date-1.5.2/fc-5.0.0/fh-4.0.1/kt-2.12.0/r-3.0.2/rg-1.5.0/rr-1.5.0/sc-2.4.1/sb-1.7.1/sp-2.3.1/sl-2.0.1/sr-1.4.1/datatables.min.css" rel="stylesheet"/>

            <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
            <script src="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.0.5/af-2.7.0/b-3.0.2/b-colvis-3.0.2/b-html5-3.0.2/b-print-3.0.2/cr-2.0.1/date-1.5.2/fc-5.0.0/fh-4.0.1/kt-2.12.0/r-3.0.2/rg-1.5.0/rr-1.5.0/sc-2.4.1/sb-1.7.1/sp-2.3.1/sl-2.0.1/sr-1.4.1/datatables.min.js"></script>

    </head>
    <body style="height:100%"> 
        <div class="container-fluid ">
            <div id="limet-navbar" class=' '> </div> 
            <div class="mt-4 card">    
                <div class="card-header d-inline-flex" style="flex-wrap:nowrap" id='TableCardHeader'>  </div>
                <!--<div class="card-header d-inline-flex" style="flex-wrap:nowrap" id='TableCardHeader2'>  </div>-->
                <div class="card-body" id = "table_container_div">
                    <table class="table display compact table-responsive table-striped table-bordered table-sm sticky" width ="100%" id="table_rooms" > 
                        <thead <tr></tr> </thead>
                        <tbody> <td></td>  </tbody>
                    </table> 
                </div>
            </div>      
            <div class='d-flex bd-highlight'>
                <div class='mt-4 mr-2 card flex-grow-1'>
                    <div class="card-header card-header_size"><b></b></div>
                    <div class="card-body" id="bauangaben"></div>
                </div>      
                <div class="mt-4 card">
                    <div class="card  d-inline-flex">
                        <div class="card-header card-header_size">
                            <button type="button" class="btn btn-outline-dark" id="showRoomElements"> <i class="fas fa-caret-left"></i></button> 

                        </div>
                        <div class="card-body " id ="additionalInfo"></div>
                    </div> 
                </div>         
            </div> 
        </div>
    </body>
</html>

<script src="roombookSpecifications_constDeclarations.js"></script> 
<script>
    var table;
    $(document).ready(function () {
        init_dt();
    });

    function init_dt() {
        table = new DataTable('#table_rooms', {
            ajax: {
                url: 'get_rb_specs_data.php',
                dataSrc: ''
            },
            columns: columnsDefinitionShort,
            dom: '  <"TableCardHeader"f>t<"btm.d-flex justify-content-between"lip>   ',
            scrollY: true,
            scrollCollapse: true,
            select: "os",
            fixedColumns: {
                start: 2
            },
            language: {
                "search": "",
                searchBuilder: {
                    title: null,
                    depthLimit: 2,
                    stateSave: false
                }
            },
            keys: true,
            order: [[3, 'asc']],
            stateSave: true,
            info: true,
            paging: true,
            pagingType: "simple_numbers",
            pageLength: 10,
            lengthMenu: [
                [10, 20, -1],
                ['10 rows', '20 rows', 'Show all']
            ],
            compact: true 
        });
    }
</script> 
=======
<?php
session_start();
include '_utils.php';
init_page_serversides();
include 'roombookSpecifications_New_modal_addRoom.php';
?> 

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>

        <title>RB-Bauangaben</title>
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
                            <div class="container-fluid ">
                                <div id="limet-navbar" class=' '> </div> 
                                <div class="mt-4 card">    
                                    <div class="card-header d-flex justify-content-between" id="TableCardHeader">
                                        <div class="col-md-4" id="SelectRoom4Comparison">
                                            <button id="toggleCardBody" class="btn btn-success">Select Room</button>
                                        </div>
                                        <div class="col-md-4" >
                                        </div>
                                        <div class="col-md-4">
                                        </div>
                                    </div>


                                    <div class="card-body" id = "table_container_div">    
                                        <table class="table display compact table-responsive table-striped table-bordered table-sm sticky" width ="100%" id="table_rooms" > 
                                            <thead <tr></tr> </thead>
                                            <tbody> <td></td>  </tbody>
                                        </table> 
                                    </div>
                                </div>      


                                <div class="row" id="BtmCardZ" style="display:none">
                                    <div class="col-md-4">
                                        <div class="card">
                                            <div class="card-header">
                                                Elements in selected Room 
                                            </div>
                                            <div class="card-body">
                                                <p id="roomElements">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card">
                                            <div class="card-header">
                                                Card 2 Header
                                            </div>
                                            <div class="card-body">
                                                Card 2 Body
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card">
                                            <div class="card-header">
                                                Card 3 Header
                                            </div>
                                            <div class="card-body">
                                                Card 3 Body
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <script>
                                // VARIABLES
                                var table;
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
                                    init_dt();
                                    add_MT_rel_filter('#SelectRoom4Comparison');
                                    move_dt_search('SelectRoom4Comparison');

                                    $('#toggleCardBody').on('click', function () {
                                        var selectedRowData = table.row('.selected').data();
                                        if (selectedRowData) {
                                            $('#table_container_div').slideToggle(); // Use slideToggle for a roll-up animation 
                                            $('#columnFilter').toggle();
                                            $('#dt-search-0').toggle();
                                            $('#BtmCardZ').toggle();
                                            var newText = selectedRowData["Bezeichnung"] + " " + selectedRowData['Nummer'];
                                            $('#toggleCardBody').text(newText);
                                            get_el_in_room_table(selectedRowData['idTABELLE_Räume']);
                                        } else {
                                            alert("Raum auswählen");
                                        }
                                    });
                                });



                                // METHODSZZ
                                function get_el_in_room_table(RaumID) {
                                    $.ajax({
                                        url: "setSessionVariables.php",
                                        data: {"roomID": RaumID},
                                        type: "GET",
                                        success: function (data) {
                                            $.ajax({
                                                url: "getRoomElementsDetailed2.php",
                                                type: "GET",
                                                success: function (data) {
                                                    $("#roomElements").html(data);
                                                }
                                            });
                                        }
                                    });
                                }


                                function init_dt() {
                                    table = new DataTable('#table_rooms', {
                                        ajax: {
                                            url: 'get_rb_specs_data.php',
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
                                        table.column('MT-relevant:name').search(filterValue).draw();
                                    });
                                }

                                function move_dt_search(id) {
                                    var dt_searcher = document.getElementById("dt-search-0");
                                    dt_searcher.parentNode.removeChild(dt_searcher);
                                    document.getElementById(id).appendChild(dt_searcher);
                                    dt_searcher.classList.add("fix_size");
                                }











                                // AB HIER: RELIKTE 
//                                                                init_editable_checkbox();
//                                                                
//                                                                
//                                                                init_showRoomElements_btn();
//                                                                init_btn_4_dt();
//                                                                init_visibilities();
//                                                                table_click();
//                                                                event_table_keyz();
//
//                                                                populate_modal();
//                                                                init_vis_modal_functionality();







                                let toastCounter = 0;
                                var cellText = "";
                                var currentRowInd = 0;
                                var currentColInd = 0;
                                let current_edit = false; //variable keeps track if the input field to ediot the cells is open

                                function populate_modal() {
                                    var columnsPerRow = 4;
                                    var rows = Math.ceil(columnsDefinition.length - 5 / columnsPerRow);
                                    for (var i = 0; i < rows; i++) {
                                        var row = $('<div class="row"></div>');
                                        for (var j = 0; j < columnsPerRow; j++) {
                                            var index = i * columnsPerRow + j + 5;
                                            if (index < columnsDefinition.length) {
                                                var columnDiv = $('<div class="col-sm-3"><div class="checkbox"><label><input type="checkbox" value="' + index + '" checked>' + columnsDefinition[index].title + '</label></div></div>');
                                                row.append(columnDiv);
                                            }
                                        }
                                        $('#mbody .form-group').append(row);
                                    }
                                }

                                function init_vis_modal_functionality() {
                                    $('#VisModal').on('show.bs.modal', function () {
                                        console.log('Modal is being shown');
                                        $('#CBXs input:checkbox').each(function () {
                                            var column = table.column($(this).val());
                                            console.log('Checkbox value: ' + $(this).val() + ', column visibility: ' + column.visible());
                                            $(this).prop('checked', column.visible());
                                        });
                                    });
                                    $('#CBXs').on('click', 'input:checkbox', function () {
                                        console.log('Checkbox clicked. Value: ' + $(this).val() + ', checked: ' + $(this).prop('checked'));
                                        var column = table.column($(this).val());
                                        column.visible(!column.visible());
                                    });
                                }



                                function checkAndToggleColumnsVisibility() {
                                    table.columns().every(function () {
                                        var hasNonEmptyCell = this.data().toArray().some(function (cellData) {
                                            return cellData !== null && cellData !== undefined && cellData !== '' && cellData !== '-' && cellData !== ' ' && cellData !== '  ' && cellData !== '   ' && cellData !== '.';
                                        });
                                        if (!hasNonEmptyCell) {
                                            this.visible(!this.visible());
                                        }
                                    });
                                }

                                function check_angaben() {
                                    var selectedRows = table.rows({selected: true}).data();
                                    console.log(selectedRows);
                                    var roomIDs = [];
                                    for (var i = 0; i < selectedRows.length; i++) {
                                        roomIDs.push(selectedRows[i]['idTABELLE_Räume']);
                                    }
                                    console.log(roomIDs);
                                    if (roomIDs.length === 0) {
                                        alert("Kein Raum ausgewählt!");
                                    } else {
                                        window.open('/roombookBauangabenCheck.php?roomID=' + roomIDs);
                                    }
                                }


                                function translateBrToNewline(inputString) {
                                    const outputString = inputString.replace(/<br>/g, '\n').replace(/<\/br>/g, '\n');
                                    return outputString;
                                }

                                function change_search_b_btn_txt() {
                                    $('.dt-button').each(function () {
                                        if ($(this).text() === 'Search Builder') {
                                            $(this).text('S');
                                        }
                                    });
                                }

                                function getCase(dataIdentifier) {
                                    const column = columnsDefinition.find(column => column.data === dataIdentifier);
                                    if (column && column.case) {
                                        return column.case;
                                    } else {
                                        return 'no case';
                                    }
                                }

                                function format_data_input(newData, dataIdentifier) {
                                    switch (getCase(dataIdentifier)) {
                                        case "bit":
                                            newData = one_or_zero(newData);
                                        case "num":
                                            newData = formatNum(newData);
                                    }
                                    return newData;
                                }

                                function formatNum(newData) {
                                    newData = newData.replace(/[^0-9,.-]/g, ''); // Remove non-numeric characters (except for '.' and '-')
                                    newData = newData.replace(/,/g, '.'); // Replace ',' with '.' 
                                    return newData;
                                }

                                function one_or_zero(inp) {
                                    inp = inp.toLowerCase();
                                    if (inp === 'yes' || inp === '1' || inp === 'ja') {
                                        return "1";
                                    } else {
                                        return "0";
                                    }
                                }

                                function event_table_keyz() {
                                    table.on('key-focus', function (e, datatable, cell) {
                                        if (document.getElementById('checkbox_EditableTable').checked && !current_edit) {
                                            cell.node().click();
                                            table.keys.disable();
                                        } else {
                                            var rowIndex = cell.index().row;
//                                                                        table.rows().deselect();
                                            if (rowIndex !== currentRowInd && !document.getElementById('checkbox_EditableTable').checked) {
//                                                                            table.row(cell.index().row).select();
                                                currentRowInd = rowIndex;
                                            }
                                        }
                                    });
//                                                                table.on('key-blur', function (e, datatable, cell) {
//                                                                    table.cell(cell.index()).deselect();
//                                                                    cell.node().click();
//                                                                });
                                }

                                function html_2_plug_into_edit_cell(dataIdentifier) {
                                    const options = {
                                        "Allgemeine Hygieneklasse": [
                                            " - ",
                                            "ÖAK - I - Ordination- und Behandlung",
                                            "ÖAK - II - klein Invasiv",
                                            "ÖAK - III - Eingriffsraum",
                                            "ÖAK - IV - OP",
                                            "MA 15 - LL 28 - OP",
                                            "MA 15 - LL 28 - Eingriffsraum",
                                            "MA 15 - LL 28 - Behandlungsraum invasiv",
                                            "Gentechnikgesetz - S1",
                                            "Gentechnikgesetz - S2",
                                            "Gentechnikgesetz - S3",
                                            "Gentechnikgesetz - S4"],
                                        "H6020": [" - ", "H1a", "H1b", "H2a", "H2b", "H2c", "H3", "H4"],
                                        "Anwendungsgruppe": ["-", "0", "1", "2"],
                                        "Fussboden OENORM B5220": ["kA", "Klasse 1", "Klasse 2", "Klasse 3"]
                                    };
                                    if (options[dataIdentifier]) {
                                        const dropdownOptions = options[dataIdentifier]
                                                .map(option => `<option value="${option}"${cellText === option ? ' selected' : ''}>${option}</option>`)
                                                .join('\n');
                                        return `<select class="form-control form-control-sm" id="${dataIdentifier}_dropdowner">\n${dropdownOptions}\n</select>`;
                                    } else {
                                        return `<input id="CellInput" onclick="this.select()" type="text" value="${cellText}">`;
                                    }
                                }


                                function table_click() {
                                    $('#table_rooms tbody').on('click', 'tr', function () {
                                        var RaumID = table.row($(this)).data()['idTABELLE_Räume'];
                                        if (document.getElementById('checkbox_EditableTable').checked) {

                                            var Raumbez = $('#table_rooms').DataTable().row($(this)).data()['Raumbezeichnung'];
                                            var rowIndex = $(this).closest('tr').index();
                                            var columnIndex = -1;
                                            $(this).find('td').each(function (index) { //finding index like this only works for the first click, the it return -1
                                                if ($(this).is(event.target)) {
                                                    columnIndex = index;
                                                    return false;
                                                }
                                            });
                                            if (columnIndex === -1) {
                                                columnIndex = currentColInd; //lazy bugfix for -1 problem
                                            }
                                            var index_accounting_4_visibility = columnIndex;
                                            var visibleColumns = table.columns().visible();
                                            for (var i = 0; i <= index_accounting_4_visibility; i++) { //also count invisible ones, if u wanna use the ccolumn definition indexing
                                                if (!visibleColumns[i]) {
                                                    index_accounting_4_visibility++;
                                                }
                                            }
                                            var dataIdentifier = columnsDefinition[index_accounting_4_visibility]['data'];
                                            var cell = $(this).find('td').eq(columnIndex);
                                            if (currentRowInd !== rowIndex || currentColInd !== columnIndex) {
                                                cellText = cell.text().trim();
                                            }
                                            currentRowInd = rowIndex;
                                            currentColInd = columnIndex;
                                            //console.log('Debug TableClick: Column index:', columnIndex, "; Acc4Vis ", index_accounting_4_visibility, '; Row index:', rowIndex, '; Column name (data identifier):', dataIdentifier, "; idTABELLE_Räume: ", RaumID, " Raumbezeichnung: ", Raumbez);
                                            if (getCase(dataIdentifier) !== "none-edit") {  //dataIdentifier !== "Bezeichnung" && dataIdentifier !== "Nummer") {
                                                if (!current_edit) {
                                                    cell.html(html_2_plug_into_edit_cell(dataIdentifier));
                                                    table.keys.disable();
                                                    console.log(" Table keys should be off");
                                                }
                                                current_edit = true;
                                                cell.find('input, select').focus();
                                                table.keys.disable();
                                                cell.find('input, select').on('keydown blur', function (event) {
//                                                                                table.on('keydown keyup', function (event) {
//                                                                                    var ctrlPressed = event.ctrlKey; // Check if ctrl is pressed
//
//                                                                                    if (ctrlPressed && event.type === 'keydown') {
//                                                                                        console.log("CTRL on");
////                                                                                        cell.html(cellText);
////                                                                                        current_edit = false;
////                                                                                        table.keys.enable(); 
//                                                                                    } else if (!ctrlPressed && event.type === 'keyup') {
//                                                                                        console.log("CTRL off");
//                                                                                        table.keys.disable();
//                                                                                    }
//                                                                                });
                                                    if (event.keyCode === 13 && current_edit) { // Enter key pressed
                                                        //console.log("Enter Keydown: ", $(this).val());
                                                        var newData = format_data_input($(this).val(), dataIdentifier);
                                                        if (newData.trim() !== "") {
                                                            cellText = newData;
                                                            cell.html(newData);
                                                            current_edit = false;
                                                            table.keys.enable();
                                                            //console.log("Saving:", RaumID, dataIdentifier, newData);
                                                            save_changes(RaumID, dataIdentifier, newData, Raumbez);
                                                            table.cell(cell.index()).select();
                                                        }
                                                    } // else {alert("DatEmpty: Enter valid params"); }
                                                    if (event.keyCode === 27 || event.type === "blur" || event.keyCode === 9) {// (event.keyCode >= 37 && event.keyCode <= 40) ||
                                                        cell.html(cellText);
                                                        current_edit = false;
                                                        table.keys.enable();
                                                        table.cell(cell.index()).select();
                                                        initializeToaster("Changes NOT Saved", " - ", false);
                                                    }
                                                });
                                            }
                                        }
                                        console.log("RaumID", RaumID);
                                        $.ajax({
                                            url: "setSessionVariables.php",
                                            data: {"roomID": RaumID},
                                            type: "GET",
                                            success: function (data) {
                                                $("#RoomID").text(RaumID);
                                                $.ajax({
                                                    url: "getRoomSpecifications2.php",
                                                    type: "GET",
                                                    success: function (data) {
                                                        $("#bauangaben").html(data);
                                                        $.ajax({
                                                            url: "getRoomElementsDetailed2.php",
                                                            type: "GET",
                                                            success: function (data) {
                                                                $("#roomElements").html(data);
                                                                $('#diy_searcher').on('keyup', function () {
                                                                    try {
                                                                        ($('#tableRoomElements').DataTable().search(this.value).draw());
                                                                    } catch (e) {
                                                                        console.log(e);
                                                                        alert("!", e);
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


                                function save_changes(RaumID, ColumnName, newData, raumname) {
                                    console.log("SaveFunction: ", raumname, ColumnName, newData);
                                    $.ajax({
                                        url: "saveRoomProperties.php",
                                        data: {"roomID": RaumID, "column": ColumnName, "value": newData},
                                        type: "GET",
                                        success: function (data) {
                                            if (data === "Erfolgreich aktualisiert!") {
                                                initializeToaster("<b>SAVED</b>", raumname + ";  " + ColumnName + ";  " + newData + " ", true);
                                            } else {
                                                initializeToaster("<b>FAILED!!</b>" + data + "---", "", false);
                                            }
                                        }
                                    });
                                }

                                function init_editable_checkbox() {
                                    var checkbox = $('<input>', {
                                        type: 'checkbox',
                                        name: 'EditableTable',
                                        id: 'checkbox_EditableTable',
                                        checked: false,
                                        class: 'fix_size form-check-input inline-flex'
                                    }).appendTo($('#TableCardHeader'));
                                    var label = $('<label>', {
                                        htmlFor: 'checkbox_EditableTable',
                                        class: 'rotated form-check-label inline',
                                        text: "-EDIT-"});
                                    var container = $('<span>').append(checkbox);
                                    $('#TableCardHeader').append(container).append(label);
                                }

                                function init_checkbox_state() {
                                    const savedState = localStorage.getItem('#editableCheckboxState');
                                    if (savedState === 'true') {
                                        $('#editableCheckboxState').prop('checked', true);
                                        event_table_click();
                                        console.log("Table click initiated");
                                    }
                                }

                                function initializeToaster(headerText, subtext, success) {
                                    const toast = document.createElement('div');
                                    toast.classList.add('toast');
                                    toast.classList.add('show');
                                    toast.setAttribute('role', 'alert');
                                    toast.style.position = 'fixed';
                                    const topPosition = 10 + toastCounter * 50;
                                    toast.style.top = `${topPosition}px`;
                                    toast.style.right = '10px';
                                    toast.innerHTML = `
                                        <div class="toast-header ${success ? "btn_vis" : "btn_invis"}">
                                            <strong class="mr-auto">${headerText} ${subtext}</strong>
                                        </div>`;
                                    document.body.appendChild(toast);
                                    toast.style.display = 'block';
                                    toastCounter++;
                                    setTimeout(() => {
                                        toast.style.display = 'none';
                                        toastCounter--;
                                    }, 2000 + toastCounter * 100);
                                }




                                function save_new_room(nummer, name, funktionsteilstelle, MTrelevant) {
                                    if (nummer !== "" && name !== "" && MTrelevant !== "" && funktionsteilstelle !== "") {  //& flaeche  !== "" && geschoss !== "" && bauetappe  !== "" && bauteil  !== "" && funktionsteilstelle !== 0 
                                        $.ajax({
                                            url: "addRoom_1.php", // "ID": raumID,
                                            data: {"raumnummer": nummer, "raumbezeichnung": name, "funktionsteilstelle": funktionsteilstelle, "MTrelevant": MTrelevant},
                                            type: "GET",
                                            success: function (data) {
                                                $('#addRoomModal').modal('hide');
                                                alert(data);
                                                window.location.replace("roombookSpecifications_New.php");
                                            }
                                        });
                                    } else {
                                        alert("Bitte alle Felder ausfüllen!");
                                    }
                                }





                                function init_btn_4_dt() {
                                    let spacer = {extend: 'spacer', style: 'bar', className: "spacer"};
                                    new $.fn.dataTable.Buttons(table, {
                                        buttons: [
                                            spacer, {extend: 'searchBuilder'}, spacer,
                                            buttonRanges.map(button => ({
                                                    text: button.name,
                                                    className: 'btn_vis',
                                                    action: function (e, dt, node, config) {
                                                        toggleColumns(dt, button.start, button.end, button.name);
                                                    }
                                                })),
                                            spacer,
                                            {
                                                text: 'w/Data',
                                                className: '',
                                                id: 'toggleDatalessColumnsButton',
                                                action: function (e, dt, node, config) {
                                                    checkAndToggleColumnsVisibility(dt);
                                                }
                                            },
                                            {
                                                text: 'VIS',
                                                className: '',
                                                id: 'btn_spalten_ausblenden',
                                                action: function (e, dt, node, config) {
                                                    $('#VisModal').modal('show');
                                                }
                                            },
                                            {extend: 'spacer', text: "SELECT:", style: 'bar', className: "rotated"},
                                            {
                                                text: 'All',
                                                action: function () {
                                                    table.rows().select();
                                                }
                                            }, {
                                                text: 'Visible',
                                                action: function () {
                                                    table.rows(':visible').select();
                                                }
                                            },
                                            {
                                                text: 'None',
                                                action: function () {
                                                    table.rows().deselect();
                                                }
                                            },

                                            {
                                                text: 'Add',
                                                className: 'btn btn_vis far fa-plus-square',
                                                action: function (e, dt, node, config) {
                                                    //  find_current_max_roomID();
                                                    $('#addRoomModal').modal('show'); //imported from rbSpecifications_New_modal_addRoom
                                                }
                                            }, spacer,
                                            {
                                                text: "Cpy",
                                                className: "btn far fa-window-restore",
                                                action: function (e, dt, node, config)
                                                {
                                                    copySelectedRow();
                                                }
                                            }, spacer,
                                            {
                                                text: "",
                                                className: "btn fas fa-check", //far fa-solid fa-fire-extinguisher",
                                                action: function ()
                                                {
                                                    check_angaben();
                                                }
                                            }, spacer, 'copy', 'excel', 'csv'
                                        ]}).container().appendTo($('#TableCardHeader'));
                                }


                                function init_visibilities() {
                                    if ($("#roomElements").is(':hidden')) {
                                        $('#diy_searcher').hide();
                                    }
                                    const columns = table.columns().indexes();
                                    buttonRanges.forEach(button => {
                                        const isVisible = table.column(columns[button.start]).visible();
                                        const buttonElement = $(`.btn_vis:contains('${button.name}')`);
                                        if (!isVisible) {
                                            buttonElement.addClass('btn_invis');
                                        }
                                    });
                                }

                                function toggleColumns(table, startColumn, endColumn, button_name) {
                                    const columns = table.columns().indexes();
                                    var vis = !table.column(columns[endColumn]).visible();
                                    for (let i = startColumn; i <= endColumn; i++) {
                                        table.column(columns[i]).visible(vis);
                                    }

                                    if (button_name === 'Alle') {
                                        buttonRanges.forEach(button => {
                                            const btn = $(`.btn_vis:contains('${button.name}')`);
                                            if (vis) {
                                                btn.removeClass('btn_invis');
                                            } else {
                                                btn.addClass('btn_invis');
                                            }

                                        });
                                    } else if (button_name === 'LAB') {
                                        ['L-GAS', 'L-ET', 'L-HT', 'L-H2O'].forEach(name => {
                                            const button = $(`.btn_vis:contains('${name}')`);
                                            if (vis) {
                                                button.removeClass('btn_invis');
                                            } else {
                                                button.addClass('btn_invis');
                                            }
                                        });
                                    } else {
                                        const button = $(`.btn_vis:contains('${button_name}')`);
                                        if (vis) {
                                            button.removeClass('btn_invis');
                                        } else {
                                            button.addClass('btn_invis');
                                        }
                                    }
                                }



                                function init_showRoomElements_btn() {
                                    $("#showRoomElements").html("<i class='fa fa-caret-right'></i>");
                                    $("#showRoomElements").click(function () {
                                        if ($("#roomElements").is(':hidden')) {
                                            $(this).html("<i class='fas fa-caret-right'></i>");
                                            $("#additionalInfo").show();
                                            $('#diy_searcher').show();
                                        } else {
                                            $(this).html("<i class='fas fa-caret-left'></i>");
                                            $("#additionalInfo").hide();
                                            $('#diy_searcher').hide();
                                        }
                                    });
                                }

                            </script>
                        </body> 
                        </html>

>>>>>>> Stashed changes
