<?php
session_start();
include '_utils.php';
init_page_serversides();
include 'roombookSpecifications_New_modal_addRoom.php';
?> 

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>

        <title>RB-ElemetsInRoom-ParameterTable</title>
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
                            .btn_vis{
                                background-color: rgba(100, 140, 25, 0.2)!important;
                                color: black;
                                box-shadow: 0 1px 1px 0 rgba(0,0,0,0.2), 0 0px 0px 0 rgba(0,0,0,0.10);
                            }
                            .btn_invis{
                                background-color: rgba(100, 0, 25, 0.2)!important;
                                color: black;
                                box-shadow: 0 1px 1px 0 rgba(0,0,0,0.2), 0 0px 0px 0 rgba(0,0,0,0.10);
                            }
                            .card-header_size {
                                height: 50px;
                                width: auto;
                            }
                            .table>thead>tr>th {
                                background-color: rgba(100, 140, 25, 0.15);
                            }

                            .fix_size{
                                height: 35px !important;
                                font-size: 15px;
                            }

                            .form-check-input:checked {
                                background-color: rgba(100, 140, 25, 0.75) !important;
                            }
                            .rotated {
                                writing-mode: vertical-lr;
                            }
                        </style>

                        </head> 
                        <body style="height:100%"> 
                            <div class="container-fluid ">
                                <div id="limet-navbar" class=' '> </div> 

                                <div class="mt-4 card">    
                                    <div class="card-header d-inline-flex" id='TableCardHeader'>  </div>

                                    <div class="card-body" id = "table_container_div">
                                        <table class="table display compact table-responsive table-striped table-bordered table-sm sticky" width ="100%" id="table_rooms" > 
                                            <thead <tr></tr> </thead>
                                            <tbody> <td></td>  </tbody>
                                        </table> 
                                    </div>
                                </div>      
                                <div class='mt-4 card  bd-highlight'>
                                    <div class="card-header card-header_size">  ELEMENT PARAMETERZ
                                        <!--<button type="button" class="btn btn-outline-dark btn-xs" id="showRoomElements"><i class="fas fa-caret-left"></i></button>--> 
                                        <!--<input type="text" class ="pull-right fix_size" id="diy_searcher" placeholder="Search...">-->
                                    </div>
                                    <div class="card-body " id ="additionalInfo">
                                        <p id="roomElements">
                                            <p id="elementParameters">
                                                </div>
                                                </div> 
                                                </div>
                                                <script src="roombookSpecifications_constDeclarations.js"></script> 
                                                <script>
                                                    var table;
                                                    let toastCounter = 0;

                                                    var cellText = "";
                                                    var currentRowInd = 0;
                                                    var currentColInd = 0;
                                                    let current_edit = false; //variable keeps track if the input field to ediot the cells is open

                                                    $(document).ready(function () {
                                                        init_dt();
//                                                        init_editable_checkbox();
                                                        add_MT_rel_filter('#TableCardHeader');
                                                        move_dt_search();
//                                                        init_showRoomElements_btn();
//                                                        init_btn_4_dt();
//                                                        init_visibilities();
                                                        table_click();
//                                                        event_table_keyz();
                                                    });

//                                                    function getCase(dataIdentifier) {
//                                                        const column = columnsDefinition.find(column => column.data === dataIdentifier);
//                                                        if (column && column.case) {
//                                                            return column.case;
//                                                        } else {
//                                                            return 'no case';
//                                                        }
//                                                    }
//
//                                                    function format_data_input(newData, dataIdentifier) {
//                                                        switch (getCase(dataIdentifier)) {
//                                                            case "bit":
//                                                                newData = one_or_zero(newData);
//                                                            case "num":
//                                                                newData = formatNum(newData);
//                                                        }
//                                                        return newData;
//                                                    }
//
//                                                    function formatNum(newData) {
//                                                        newData = newData.replace(/[^0-9,.-]/g, '');// Remove non-numeric characters (except for '.' and '-')
//                                                        newData = newData.replace(/,/g, '.');  // Replace ',' with '.' 
//                                                        return newData;
//                                                    }
//
//                                                    function one_or_zero(inp) {
//                                                        inp = inp.toLowerCase();
//                                                        if (inp === 'yes' || inp === '1' || inp === 'ja') {
//                                                            return "1";
//                                                        } else {
//                                                            return "0";
//                                                        }
//                                                    }
//
//                                                    function event_table_keyz() {
//                                                        table.on('key-focus', function (e, datatable, cell) {
//                                                            if (document.getElementById('checkbox_EditableTable').checked && !current_edit) {
//                                                                cell.node().click();
////                                                                        table.keys.disable();
//                                                            } else {
//                                                                var rowIndex = cell.index().row;
//                                                                table.rows().deselect();
//                                                                if (rowIndex !== currentRowInd && !document.getElementById('checkbox_EditableTable').checked) {
//
//                                                                    table.row(cell.index().row).select();
//                                                                    cell.node().click();
//                                                                    currentRowInd = rowIndex;
//                                                                }
//                                                            }
//                                                        });
//                                                    }
//
//                                                    function html_2_plug_into_edit_cell(dataIdentifier) {
//                                                        const options = {
//                                                            "Allgemeine Hygieneklasse": [
//                                                                " - ",
//                                                                "ÖAK - I - Ordination- und Behandlung",
//                                                                "ÖAK - II - klein Invasiv",
//                                                                "ÖAK - III - Eingriffsraum",
//                                                                "ÖAK - IV - OP",
//                                                                "MA 15 - LL 28 - OP",
//                                                                "MA 15 - LL 28 - Eingriffsraum",
//                                                                "MA 15 - LL 28 - Behandlungsraum invasiv",
//                                                                "Gentechnikgesetz - S1",
//                                                                "Gentechnikgesetz - S2",
//                                                                "Gentechnikgesetz - S3",
//                                                                "Gentechnikgesetz - S4"],
//                                                            "H6020": [" - ", "H1a", "H1b", "H2a", "H2b", "H2c", "H3", "H4"],
//                                                            "Anwendungsgruppe": ["-", "0", "1", "2"],
//                                                            "Fussboden OENORM B5220": ["kA", "Klasse 1", "Klasse 2", "Klasse 3"]
//                                                        };
//                                                        if (options[dataIdentifier]) {
//                                                            const dropdownOptions = options[dataIdentifier]
//                                                                    .map(option => `<option value="${option}"${cellText === option ? ' selected' : ''}>${option}</option>`)
//                                                                    .join('\n');
//                                                            return `<select class="form-control form-control-sm" id="${dataIdentifier}_dropdowner">\n${dropdownOptions}\n</select>`;
//                                                        } else {
//                                                            return `<input id="CellInput" onclick="this.select()" type="text" value="${cellText}">`;
//                                                        }
//                                                    }


                                                    function table_click() {
                                                        $('#table_rooms tbody').on('click', 'tr', function () {
                                                            var RaumID = table.row($(this)).data()['idTABELLE_Räume'];
//                                                            if (document.getElementById('checkbox_EditableTable').checked) {
//
//                                                                var Raumbez = $('#table_rooms').DataTable().row($(this)).data()['Raumbezeichnung'];
//                                                                var rowIndex = $(this).closest('tr').index();
//                                                                var columnIndex = -1;
//                                                                $(this).find('td').each(function (index) { //finding index like this only works for the first click, the it return -1
//                                                                    if ($(this).is(event.target)) {
//                                                                        columnIndex = index;
//                                                                        return false;
//                                                                    }
//                                                                });
//                                                                if (columnIndex === -1) {
//                                                                    columnIndex = currentColInd; //lazy bugfix for -1 problem
//                                                                }
//                                                                var index_accounting_4_visibility = columnIndex;
//                                                                var visibleColumns = table.columns().visible();
//                                                                for (var i = 0; i <= index_accounting_4_visibility; i++) { //also count invisible ones, if u wanna use the ccolumn definition indexing
//                                                                    if (!visibleColumns[i]) {
//                                                                        index_accounting_4_visibility++;
//                                                                    }
//                                                                }
//                                                                var dataIdentifier = columnsDefinition[index_accounting_4_visibility]['data'];
//                                                                var cell = $(this).find('td').eq(columnIndex);
//                                                                if (currentRowInd !== rowIndex || currentColInd !== columnIndex) {
//                                                                    cellText = cell.text().trim();
//                                                                }
//                                                                currentRowInd = rowIndex;
//                                                                currentColInd = columnIndex;
//                                                                //console.log('Debug TableClick: Column index:', columnIndex, "; Acc4Vis ", index_accounting_4_visibility, '; Row index:', rowIndex, '; Column name (data identifier):', dataIdentifier, "; idTABELLE_Räume: ", RaumID, " Raumbezeichnung: ", Raumbez);
//
//                                                                if (getCase(dataIdentifier) !== "none-edit") {  //dataIdentifier !== "Bezeichnung" && dataIdentifier !== "Nummer") {
//                                                                    if (!current_edit) {
//                                                                        cell.html(html_2_plug_into_edit_cell(dataIdentifier));
//                                                                        table.keys.disable();
//                                                                        console.log(" Table keys should be off");
//                                                                    }
////                                                                                
//                                                                    current_edit = true;
//                                                                    cell.find('input, select').focus();
//                                                                    table.keys.disable();
//                                                                    cell.find('input, select').on('keydown blur', function (event) {
//                                                                        if (event.keyCode === 13 && current_edit) { // Enter key pressed
//                                                                            //console.log("Enter Keydown: ", $(this).val());
//                                                                            var newData = format_data_input($(this).val(), dataIdentifier);
//
//                                                                            if (newData.trim() !== "") {
//                                                                                cellText = newData;
//                                                                                cell.html(newData);
//                                                                                current_edit = false;
//                                                                                table.keys.enable();
//                                                                                //console.log("Saving:", RaumID, dataIdentifier, newData);
//                                                                                save_changes(RaumID, dataIdentifier, newData, Raumbez);
//                                                                            }
//                                                                        } // else {alert("DatEmpty: Enter valid params"); }
//
//                                                                        if (event.keyCode === 27 || event.type === "blur" || event.keyCode === 9) {// (event.keyCode >= 37 && event.keyCode <= 40) ||
//                                                                            cell.html(cellText);
//                                                                            current_edit = false;
//                                                                            table.keys.enable();
//                                                                            table.cell(cell.index()).select();
//
//                                                                            initializeToaster("Changes NOT Saved", " - ", false);
//                                                                        }
//                                                                    });
//                                                                }
//                                                            }
//                                                            

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

                                                    function move_dt_search() {
                                                        var dt_searcher = document.getElementById("dt-search-0");
                                                        dt_searcher.parentNode.removeChild(dt_searcher);
                                                        document.getElementById("TableCardHeader").appendChild(dt_searcher);
                                                        dt_searcher.classList.add("fix_size");
                                                    }

                                                    function init_dt() {
                                                        table = new DataTable('#table_rooms', {
                                                            ajax: {
                                                                url: 'get_rb_specs_data.php',
                                                                dataSrc: ''
                                                            },
                                                            columns: columnsDefinition,
                                                            dom: '<"TableCardHeader"f>t<"btm.d-flex justify-content-between"lip>   ',
                                                            scrollY: true,
                                                            scrollX: true,
                                                            scrollCollapse: true,
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
                                                            select: {
                                                                style: 'os'
                                                            },
                                                            paging: true,
                                                            pagingType: "simple_numbers",
                                                            pageLength: 10,
                                                            lengthMenu: [
                                                                [10, 20, -1],
                                                                ['10 rows', '20 rows', 'Show all']
                                                            ],
                                                            compact: true,
                                                            initComplete: function () {
                                                                $("#datatableit_filter").detach().appendTo('#TableCardHeader');
                                                            }
                                                        });
                                                    }

                                                    function add_MT_rel_filter(location) {
                                                        var dropdownHtml = '<select class="form-control-sm fix_size" id="columnFilter">' + '<option value="">MT</option><option value="Ja">Ja</option>' + '<option value="Nein">Nein</option></select>';
//                                                                    $('#table_rooms thead th:eq(4)').append(dropdownHtml); //to be set eqquivalent to the column index 
//                                                                    console.log("Init MT Rel Dropdown");
                                                        $(location).append(dropdownHtml);
                                                        $('#columnFilter').change(function () {
                                                            var filterValue = $(this).val();
                                                            table.column('MT-relevant:name').search(filterValue).draw();
                                                        });
                                                    }

//                                                    function init_btn_4_dt() {
//                                                        let spacer = {extend: 'spacer', style: 'bar'};
//                                                        new $.fn.dataTable.Buttons(table, {
//                                                            buttons: [
//                                                                spacer, {extend: 'searchBuilder', label: "Search B"}, spacer,
//                                                                buttonRanges.map(button => ({
//                                                                        text: button.name,
//                                                                        className: 'btn_vis',
//                                                                        action: function (e, dt, node, config) {
//                                                                            toggleColumns(dt, button.start, button.end, button.name);
////                                                                                        if((button.name)===buttonRanges[4].name){    for(let i = 4; i< buttonRanges.length; i++){
////                                                                                            toggleColumns(dt, button.start, button.end, buttonRanges[i].name); } }
//                                                                        }
//                                                                    })),
//                                                                spacer,
//                                                                {
//                                                                    text: 'w/ Data',
//                                                                    className: '',
//                                                                    id: 'toggleDatalessColumnsButton',
//                                                                    action: function (e, dt, node, config) {
//                                                                        checkAndToggleColumnsVisibility(dt);
//                                                                    }
//                                                                },
//                                                                spacer, 'copy', 'excel', 'csv', spacer, 'selectAll', 'selectNone',
//                                                                spacer, // spacer,
//                                                                {
//                                                                    text: ' Raum',
//                                                                    className: 'btn btn_vis far fa-plus-square',
//                                                                    action: function (e, dt, node, config) {
//                                                                        //  find_current_max_roomID();
//                                                                        $('#addRoomModal').modal('show'); //imported from rbSpecifications_New_modal_addRoom
//                                                                    }
//                                                                }, spacer,
//                                                                {
//                                                                    text: " R.Kopieren",
//                                                                    className: "btn far fa-window-restore",
//                                                                    action: function (e, dt, node, config)
//                                                                    {
//                                                                        copySelectedRow();
//                                                                    }
//                                                                }, spacer,
//                                                                {
//                                                                    text: "Check ",
//                                                                    className: "btn fa far fa-check",
//                                                                    action: function ()
//                                                                    {
//                                                                        check_angaben();
//                                                                    }
//                                                                }, spacer
//                                                            ]}).container().appendTo($('#TableCardHeader'));
//                                                    }

//                                                    function check_angaben() {
//
//                                                        $.ajax({
//                                                            url: "get_angaben_check.php", // "ID": raumID,
//                                                            data: {},
//                                                            type: "GET",
//                                                            success: function (data) {
//                                                                alert(data);
//                                                            }
//                                                        });
//
//
//                                                    }

//                                                    function init_visibilities() {
//                                                        if ($("#roomElements").is(':hidden')) {
//                                                            $('#diy_searcher').hide();
//                                                        }
//                                                        const columns = table.columns().indexes();
//                                                        buttonRanges.forEach(button => {
//                                                            const isVisible = table.column(columns[button.start]).visible();
//                                                            const buttonElement = $(`.btn_vis:contains('${button.name}')`);
//                                                            if (!isVisible) {
//                                                                buttonElement.addClass('btn_invis');
//                                                            }
//                                                        });
//                                                    }

//                                                    function toggleColumns(table, startColumn, endColumn, button_name) {
//                                                        const columns = table.columns().indexes();
//                                                        var vis = !table.column(columns[endColumn]).visible();
//
//                                                        for (let i = startColumn; i <= endColumn; i++) {
//                                                            table.column(columns[i]).visible(vis);
//                                                        }
//
//                                                        if (button_name === 'Alle') {
//                                                            buttonRanges.forEach(button => {
//                                                                const btn = $(`.btn_vis:contains('${button.name}')`);
//                                                                if (vis) {
//                                                                    btn.removeClass('btn_invis');
//                                                                } else {
//                                                                    btn.addClass('btn_invis');
//                                                                }
//
//                                                            });
//                                                        } else {
//                                                            const button = $(`.btn_vis:contains('${button_name}')`);
//                                                            if (vis) {
//                                                                button.removeClass('btn_invis');
//                                                            } else {
//                                                                button.addClass('btn_invis');
//                                                            }
//                                                        }
//                                                    }
//
//                                                    function checkAndToggleColumnsVisibility() {
//                                                        table.columns().every(function () {
//                                                            var hasNonEmptyCell = this.data().toArray().some(function (cellData) {
//                                                                return cellData !== null && cellData !== undefined && cellData !== '' && cellData !== '-' && cellData !== ' ' && cellData !== '  ' && cellData !== '   ' && cellData !== '.';
//                                                            });
//                                                            if (!hasNonEmptyCell) {
//                                                                this.visible(!this.visible());
//                                                            }
//                                                        });
//                                                    }

//                                                    function init_showRoomElements_btn() {
//                                                        $("#showRoomElements").html("<i class='fas fa-caret-down'></i>");
//                                                        $("#showRoomElements").click(function () {
//                                                            if ($("#roomElements").is(':hidden')) {
//                                                                $(this).html("<i class='fas fa-caret-down'></i>");
//                                                                $("#additionalInfo").show();
//                                                                $('#diy_searcher').show();
//                                                            } else {
//                                                                $(this).html("<i class='fas fa-caret-up'></i>");
//                                                                $("#additionalInfo").hide();
//                                                                $('#diy_searcher').hide();
//                                                            }
//                                                        });
//                                                    }

//                                                    $("#saveNewRoom").click(function () {
//                                                        var nummer = $("#nummer").val();
//                                                        var name = $("#name").val(); // var raumbereich = $("#raumbereich").val();
//                                                        var funktionsteilstelle = $("#funktionsstelle").val();
//                                                        var MTrelevant = $("#mt-relevant").val();
//                                                        save_new_room(nummer, name, funktionsteilstelle, MTrelevant);
//                                                    });

                                                    function hidethesecopmmentsintheHTMLcontentofthewebpage() {
                                                        //$('#tableRoomElemnts tbody').on("click", 'tr', function () {
                                                        /*
                                                         var elementID = table.row( $(this) ).data()[0];	
                                                         var variantenID = table.row( $(this) ).data()[5];	
                                                         var bestand = 1;
                                                         if(table.row( $(this) ).data()[6]==="Ja"){
                                                         bestand = 0;
                                                         }
                                                         $.ajax({
                                                         url : "getRoomsWithElement1.php",
                                                         data:{"elementID":elementID,"variantenID":variantenID,"bestand":bestand},
                                                         type: "GET",
                                                         success: function(data){
                                                         $("#roomsWithAndWithoutElements").html(data);
                                                         $.ajax({
                                                         url : "getElementVariante.php",
                                                         data:{"elementID":elementID,"variantenID":variantenID},
                                                         type: "GET",
                                                         success: function(data){
                                                         $("#elementVarianten").html(data);
                                                         $.ajax({
                                                         url : "getStandardElementParameters.php",
                                                         data:{"elementID":elementID},
                                                         type: "GET",
                                                         success: function(data){
                                                         $("#elementDBParameter").html(data);
                                                         $.ajax({
                                                         url : "getElementPricesInDifferentProjects.php",
                                                         data:{"elementID":elementID},
                                                         type: "GET",
                                                         success: function(data){
                                                         $("#elementPricesInOtherProjects").html(data);
                                                         $.ajax({
                                                         url : "getDevicesToElement.php",
                                                         data:{"elementID":elementID},
                                                         type: "GET",
                                                         success: function(data){
                                                         $("#devicesToElement").html(data);
                                                         $.ajax({
                                                         url : "getElementGewerke.php",
                                                         data:{"elementID":elementID},
                                                         type: "GET",
                                                         success: function(data){
                                                         $("#elementGewerk").html(data);
                                                         }
                                                         });
                                                         
                                                         }
                                                         });
                                                         
                                                         }
                                                         });
                                                         
                                                         }
                                                         });
                                                         }
                                                         });
                                                         }
                                                         });
                                                         
                                                         */
                                                        //});
                                                        /*function init_search_bar() {
                                                         const searchInput = $('<input>', {
                                                         type: 'text',
                                                         placeholder: 'Search...',
                                                         class: 'ml-auto p-2'
                                                         });
                                                         searchInput.on('keyup', function () {
                                                         const searchTerm = $(this).val();
                                                         table.search(searchTerm).draw();
                                                         });
                                                         $('#TableCardHeader').append(searchInput);
                                                         }*/
                                                        /*function init_table_click() {
                                                         $('#table_rooms tbody').on('click', 'tr', function () {
                                                         table.$('tr.info').removeClass('info');
                                                         var raummID = $('#table_rooms').DataTable().row($(this)).data()['idTABELLE_Räume'];
                                                         $('#diy_searcher').val('');
                                                         $.ajax({
                                                         url: "setSessionVariables.php",
                                                         data: {"roomID": raummID},
                                                         type: "GET",
                                                         success: function (data) {
                                                         $("#RoomID").text(raummID);
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
                                                         $('#tableRoomElements').DataTable().search(this.value).draw();
                                                         });
                                                         }
                                                         });
                                                         }
                                                         });
                                                         }
                                                         });
                                                         });
                                                         }*/
                                                        /*function print_dt_data() {
                                                         var data = table.data().toArray();
                                                         console.log('Data from DataTables:');
                                                         console.table(data);
                                                         }*/
                                                        /*    function event_table_click() {
                                                         $('#table_rooms').on('click', 'tbody td', function () {
                                                         var columnIndex = $(this).index();
                                                         var rowIndex = $(this).closest('tr').index();
                                                         var trueColumnIndex = $(this).index();
                                                         var visibleColumns = table.columns().visible();
                                                         for (var i = 0; i <= trueColumnIndex; i++) { //also count invisible ones, if u wanna use the ccolumn definition indexing
                                                         if (!visibleColumns[i]) {
                                                         trueColumnIndex++;
                                                         }
                                                         }
                                                         var columnName = columnsDefinition[trueColumnIndex].data;
                                                         //                                                                        var RaumID = table.cell({row:rowIndex, column:  2}).data();
                                                         var RaumID = table.row(rowIndex).data()['idTABELLE_Räume'];
                                                         //                                                                        var RaumID = table.row(rowIndex).data().idTABELLE_Räume;
                                                         
                                                         console.log('Debug TableClick: Column index:', columnIndex, '; Row index:', rowIndex, '; trueColumnIndex:', trueColumnIndex, '; Column name (data identifier):', columnName, "; idTABELLE_Räume: ", RaumID);
                                                         
                                                         if (currentRowInd !== rowIndex || currentColInd !== columnIndex) {
                                                         cellText = $(this).text();
                                                         }
                                                         currentRowInd = rowIndex;
                                                         currentColInd = columnIndex;
                                                         
                                                         
                                                         if (columnName !== "Bezeichnung" && columnName !== "Nummer") {
                                                         $(this).html('<input id="CellInput" type="text" value="' + cellText + '">');
                                                         $(this).find('input').focus();
                                                         $(this).find('input').on('keydown blur', function (event) {
                                                         if (event.keyCode === 13) { // Enter key pressed
                                                         var newData = $(this).val();
                                                         if (newData.trim() !== "") {
                                                         $(this).parent().html(newData);
                                                         console.log("Saving:", RaumID, columnName, newData);
                                                         save_changes(RaumID, columnName, newData);
                                                         initializeToaster("Changes Saved", table.row(rowIndex).data().Raumbezeichnung + ";  " + columnName + ";  " + newData + "   ", true);
                                                         }
                                                         }
                                                         if (event.keyCode === 27 || event.type === "blur") {
                                                         $(this).parent().html(oldT);
                                                         oldT = cellText;
                                                         initializeToaster("Changes NOT Saved", " - ", false);
                                                         }
                                                         });
                                                         }
                                                         });
                                                         }*/
                                                        /*                                                                    event_table_keyz();
                                                         table.on('keydown', function (e, cell) {
                                                         // Check if the pressed key is Enter (key code 13)
                                                         console.log("Doc on kd", cell);
                                                         if (e.key === "Enter") {
                                                         
                                                         
                                                         // Get the currently focused element
                                                         var focusedElement = document.activeElement;
                                                         console.log("KD = enter", focusedElemnt);
                                                         // Check if the focused element is a DataTable cell
                                                         if ($(focusedElement).hasClass('dataTables-cell')) {
                                                         // Click on the cell
                                                         $(focusedElement).click();
                                                         }
                                                         }
                                                         }); */
                                                        /* function make_checkbox() {
                                                         var checkbox = $('<input>', {
                                                         type: 'checkbox',
                                                         name: 'EditableTable',
                                                         id: 'save_State_cbx',
                                                         checked: false,
                                                         class: 'form-check-input'
                                                         }).appendTo($('#TableCardHeader'));
                                                         var label = $('<label>', {
                                                         htmlFor: 'save_State_cbx',
                                                         class: ' form-check-label'
                                                         }).text('StateSave');
                                                         var container = $('<span>').append(checkbox).append(label);
                                                         $('#TableCardHeader').append(container);
                                                         checkbox.on('change', function () {
                                                         localStorage.setItem('save_State_cbx', this.checked);
                                                         if (this.checked) {
                                                         
                                                         } else {
                                                         
                                                         }
                                                         });
                                                         }*/
                                                    }
                                                </script>
                                                </body> 
                                                </html>

