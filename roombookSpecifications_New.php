<?php
session_start();
include '_utils.php';
init_page_serversides();
include 'roombookSpecifications_New_modal_addRoom.php';

//$mysqli = connect_sql();  $mysqli->close();
?> 

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">   
    <head>
        <title>RB-Bauangaben</title>
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
        <meta name="viewport" content="width=device-width, initial-scale=1">

            <link rel="stylesheet" href="style.css" type="text/css" media="screen" />
            <link rel="icon" href="iphone_favicon.png">
                <!--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">-->
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
                                .none {
                                    background-color: #FFFFFF !important;
                                    height: 1px !important;
                                    width: 1px !important;
                                    z-index: -1;
                                }
                                .fix_size{
                                    height: 30px !important;
                                    font-size: 16px;
                                }

                                .form-check-input:checked {
                                    background-color: rgba(100, 140, 25, 0.75) !important;
                                }
                                .rotated {
                                    writing-mode: vertical-lr; /* Rotate text vertically */
                                    /*transform: rotate(180deg);  Flip the vertical text */
                                }
                            </style>

                            </head> 
                            <body style="height:100%">
                                <div class="container-fluid ">
                                    <div id="limet-navbar" class=' '> </div> 
                                    <div class="mt-4 card">    
                                        <div class="card-header d-inline-flex" id='TableCardHeader'>  </div>

                                        <div class="card-body" id = "table_container_div">
                                            <table class="table table-responsive table-striped table-bordered table-sm sticky" width ="100%" id="table_rooms" > 
                                                <thead <tr></tr> </thead>
                                                <tbody> <td></td>  </tbody>
                                            </table> 
                                        </div>
                                    </div>      
                                    <div class='d-flex bd-highlight'>
                                        <div class='mt-4 mr-2 card flex-grow-1'>
                                            <div class="card-header card-header_size"><b>Bauangaben</b></div>
                                            <div class="card-body" id="bauangaben"></div>
                                        </div>      
                                        <div class="mt-4 card">
                                            <div class="card">
                                                <div class="card-header card-header_size">
                                                    <button type="button" class="btn btn-outline-dark btn-xs" id="showRoomElements"><i class="fas fa-caret-left"></i></button> 
                                                    <input type="text" class ="pull-right" id="diy_searcher" placeholder="Search...">
                                                </div>
                                                <div class="card-body " id ="additionalInfo">
                                                    <p id="roomElements">
                                                        <p id="elementParameters"></div>
                                                            </div> 
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
                                                                    init_editable_checkbox();
                                                                    add_MT_rel_filter('#TableCardHeader');
                                                                    move_dt_search();
                                                                    init_showRoomElements_btn();
                                                                    init_btn_4_dt();
                                                                    init_visibilities();
                                                                    table_click();
                                                                    event_table_keyz();
                                                                });

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
                                                                    newData = newData.replace(/[^0-9,.-]/g, '');// Remove non-numeric characters (except for '.' and '-')
                                                                    newData = newData.replace(/,/g, '.');  // Replace ',' with '.' 
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
                                                                        }
                                                                    });
                                                                }

                                                                function html_2_plug_into_edit_cell(dataIdentifier) {
                                                                    const options = {
                                                                        "Allgemeine Hygieneklasse": [
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
                                                                        "H6020": ["H1a", "H1b", "H2a", "H2b", "H2c", "H3", "H4"],
                                                                        "Anwendungsgruppe": ["-", "0", "1", "2"],
                                                                        "Fussboden OENORM B5220": ["kA", "Klasse 1", "Klasse 2", "Klasse 3"],
                                                                    };
                                                                    if (options[dataIdentifier]) {
                                                                        const dropdownOptions = options[dataIdentifier]
                                                                                .map(option => `<option value="${option}"${cellText === option ? ' selected' : ''}>${option}</option>`)
                                                                                .join('\n');
                                                                        return `<select class="form-control form-control-sm" id="${dataIdentifier}_dropdowner">\n${dropdownOptions}\n</select>`;
                                                                    } else {
                                                                        return `<input id="CellInput" type="text" value="${cellText}">`;
                                                                    }
                                                                }


                                                                function table_click() {
                                                                    $('#table_rooms tbody').on('click', 'tr', function () {
                                                                        var RaumID = $('#table_rooms').DataTable().row($(this)).data()['idTABELLE_Räume'];
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
                                                                            console.log('Debug TableClick: Column index:', columnIndex, "; Acc4Vis ", index_accounting_4_visibility, '; Row index:', rowIndex, '; Column name (data identifier):', dataIdentifier, "; idTABELLE_Räume: ", RaumID, " Raumbezeichnung: ", Raumbez);

                                                                            if (getCase(dataIdentifier) !== "none-edit") {  //dataIdentifier !== "Bezeichnung" && dataIdentifier !== "Nummer") {
                                                                                if (!current_edit) {
                                                                                    cell.html(html_2_plug_into_edit_cell(dataIdentifier));
                                                                                }
                                                                                current_edit = true;
                                                                                cell.find('input, select').focus();
                                                                                cell.find('input, select').on('keydown blur', function (event) {
                                                                                    if (event.keyCode === 13 && current_edit) { // Enter key pressed
                                                                                        //console.log("Enter Keydown: ", $(this).val());
                                                                                        var newData = format_data_input($(this).val(), dataIdentifier);

                                                                                        if (newData.trim() !== "") {
                                                                                            cellText = newData;
                                                                                            cell.html(newData);
                                                                                            current_edit = false;
                                                                                            //console.log("Saving:", RaumID, dataIdentifier, newData);
                                                                                            save_changes(RaumID, dataIdentifier, newData, Raumbez);
                                                                                        }
                                                                                    } // else {alert("DatEmpty: Enter valid params"); }

                                                                                    if (event.keyCode === 27 || event.type === "blur" || (event.keyCode >= 37 && event.keyCode <= 40) || event.keyCode === 9) {
                                                                                        cell.html(cellText);
                                                                                        current_edit = false;
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

                                                                function move_dt_search() {
                                                                    var dt_searcher = document.getElementById("dt-search-0");
                                                                    dt_searcher.parentNode.removeChild(dt_searcher);
                                                                    document.getElementById("TableCardHeader").appendChild(dt_searcher);
                                                                    dt_searcher.classList.add("fix_size");
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
                                                                        class: 'form-check-input fix_size'
                                                                    }).appendTo($('#TableCardHeader'));
                                                                    var label = $('<label>', {
                                                                        htmlFor: 'checkbox_EditableTable',
                                                                        class: ' form-check-label rotated inline',
                                                                        text: "- EDIT -"});

                                                                    var container = $('<span>').append(checkbox);//.append(label);
                                                                    $('#TableCardHeader').append(container);
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

                                                                function copySelectedRow() {
                                                                    if (confirm('Raum Kopieren??')) {
                                                                    } else {
                                                                        return 0;
                                                                    }
                                                                    let selectedRowData = table.row('.selected').data();
                                                                    table.row.add(selectedRowData).draw();
                                                                    console.log("SelectedData: ", selectedRowData["Raumnr"], selectedRowData["Raumbezeichnung"], selectedRowData["TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen"], selectedRowData["MT-relevant"]);
                                                                    save_new_room_all(
                                                                            selectedRowData["Raumnr"],
                                                                            selectedRowData["Raumbezeichnung"],
                                                                            selectedRowData["TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen"],
                                                                            selectedRowData["MT-relevant"],
                                                                            selectedRowData["Funktionelle Raum Nr"],
                                                                            selectedRowData["Raumnummer_Nutzer"],
                                                                            selectedRowData["Raumbereich Nutzer"],
                                                                            selectedRowData["Geschoss"],
                                                                            selectedRowData["Bauetappe"],
                                                                            selectedRowData["Bauabschnitt"],
                                                                            selectedRowData["Nutzfläche"],
                                                                            selectedRowData["Abdunkelbarkeit"],
                                                                            selectedRowData["Strahlenanwendung"],
                                                                            selectedRowData["Laseranwendung"],
                                                                            selectedRowData["H6020"],
                                                                            selectedRowData["GMP"],
                                                                            selectedRowData["ISO"],
                                                                            selectedRowData["1 Kreis O2"],
                                                                            selectedRowData["2 Kreis O2"],
                                                                            selectedRowData["O2"],
                                                                            selectedRowData["1 Kreis Va"],
                                                                            selectedRowData["2 Kreis Va"],
                                                                            selectedRowData["VA"],
                                                                            selectedRowData["1 Kreis DL-5"],
                                                                            selectedRowData["2 Kreis DL-5"],
                                                                            selectedRowData["DL-5"],
                                                                            selectedRowData["DL-10"],
                                                                            selectedRowData["DL-tech"],
                                                                            selectedRowData["CO2"],
                                                                            selectedRowData["H2"],
                                                                            selectedRowData["He"],
                                                                            selectedRowData["He-RF"],
                                                                            selectedRowData["Ar"],
                                                                            selectedRowData["N2"],
                                                                            selectedRowData["NGA"],
                                                                            selectedRowData["N2O"],
                                                                            selectedRowData["AV"],
                                                                            selectedRowData["SV"],
                                                                            selectedRowData["ZSV"],
                                                                            selectedRowData["USV"],
                                                                            selectedRowData["IT Anbindung"],
                                                                            selectedRowData["Anwendungsgruppe"],
                                                                            selectedRowData["Allgemeine Hygieneklasse"],
                                                                            selectedRowData["Raumhoehe"],
                                                                            selectedRowData["Raumhoehe 2"],
                                                                            selectedRowData["Belichtungsfläche"],
                                                                            selectedRowData["Umfang"],
                                                                            selectedRowData["Volumen"],
                                                                            selectedRowData["ET_Anschlussleistung_W"],
                                                                            selectedRowData["HT_Waermeabgabe_W"],
                                                                            selectedRowData["VEXAT_Zone"],
                                                                            selectedRowData["HT_Abluft_Vakuumpumpe"],
                                                                            selectedRowData["HT_Abluft_Schweissabsaugung_Stk"],
                                                                            selectedRowData["HT_Abluft_Esse_Stk"],
                                                                            selectedRowData["HT_Abluft_Rauchgasabzug_Stk"],
                                                                            selectedRowData["HT_Abluft_Digestorium_Stk"],
                                                                            selectedRowData["HT_Punktabsaugung_Stk"],
                                                                            selectedRowData["HT_Abluft_Sicherheitsschrank_Unterbau_Stk"],
                                                                            selectedRowData["HT_Abluft_Sicherheitsschrank_Stk"],
                                                                            selectedRowData["HT_Spuele_Stk"],
                                                                            selectedRowData["HT_Kühlwasser"],
                                                                            selectedRowData["O2_Mangel"],
                                                                            selectedRowData["CO2_Melder"],
                                                                            selectedRowData["ET_RJ45-Ports"],
                                                                            selectedRowData["ET_64A_3Phasig_Einzelanschluss"],
                                                                            selectedRowData["ET_32A_3Phasig_Einzelanschluss"],
                                                                            selectedRowData["ET_16A_3Phasig_Einzelanschluss"],
                                                                            selectedRowData["ET_Digestorium_MSR_230V_SV_Stk"],
                                                                            selectedRowData["ET_5x10mm2_Digestorium_Stk"],
                                                                            selectedRowData["ET_5x10mm2_USV_Stk"],
                                                                            selectedRowData["ET_5x10mm2_SV_Stk"],
                                                                            selectedRowData["ET_5x10mm2_AV_Stk"],
                                                                            selectedRowData["Wasser Qual 3 l/min"],
                                                                            selectedRowData["Wasser Qual 2 l/Tag"],
                                                                            selectedRowData["Wasser Qual 1 l/Tag"],
                                                                            selectedRowData["Wasser Qual 3"],
                                                                            selectedRowData["Wasser Qual 2"],
                                                                            selectedRowData["Wasser Qual 1"],
                                                                            selectedRowData["LHe"],
                                                                            selectedRowData["LN l/Tag"],
                                                                            selectedRowData["LN"],
                                                                            selectedRowData["N2 Reinheit"],
                                                                            selectedRowData["N2 l/min"],
                                                                            selectedRowData["Ar Reinheit"],
                                                                            selectedRowData["Ar l/min"],
                                                                            selectedRowData["He Reinheit"],
                                                                            selectedRowData["He l/min"],
                                                                            selectedRowData["H2 Reinheit"],
                                                                            selectedRowData["H2 l/min"],
                                                                            selectedRowData["DL ISO 8573"],
                                                                            selectedRowData["DL l/min"],
                                                                            selectedRowData["VA l/min"],
                                                                            selectedRowData["CO2 l/min"],
                                                                            selectedRowData["CO2 Reinheit"],
                                                                            selectedRowData["O2 l/min"],
                                                                            selectedRowData["O2 Reinheit"],
                                                                            selectedRowData["Laserklasse"]
                                                                            );
                                                                }

                                                                function save_new_room_all(nummer, rbez, funktionsteilstelle, MTrelevant, funktionelleraumnr, raumnummernutzer, raumbereichnutzer,
                                                                        geschoss, bauetappe, bauabschnitt, nutzfläche, abdunkelbarkeit, strahlenanwendung, laseranwendung, h6020, gmp, iso, _1kreiso2, _2kreiso2,
                                                                        o2, _1kreisva, _2kreisva, va, _1kreisdl5, _2kreisdl5, dl5, dl10, dltech, co2, h2, he, herf, ar, n2, nga, n2o, av, sv, zsv, usv,
                                                                        itanbindung, anwendungsgruppe, allgemeinehygieneklasse, raumhoehe, raumhoehe2, belichtungsfläche, umfang, volumen, etanschlussleistungw,
                                                                        htwärmeabgabew, vexatzone, htabluftvakuumpumpe, htabluftschweissabsaugungstk, htabluftessestk, htabluftrauchgasabzugstk, htabluftdigestoriumstk,
                                                                        htpunktabsaugungstk, htabluftsicherheitsschrankunterbaustk, htabluftsicherheitsschrankstk, htspuelestk, htkühlwasser, o2mangel, co2melder, etrj45ports,
                                                                        et64a3phasigeinzelanschluss, et32a3phasigeinzelanschluss, et16a3phasigeinzelanschluss, etdigestoriummsr230vsvstk, et5x10mm2digestoriumstk, et5x10mm2usvstk,
                                                                        et5x10mm2svstk, et5x10mm2avstk, wasserqual3lmin, wasserqual2ltag, wasserqual1ltag, wasserqual3, wasserqual2, wasserqual1, lhe, lnltag, ln, n2reinheit, n2lmin,
                                                                        arreinheit, arlmin, hereinheit, helmin, h2reinheit, h2lmin, dliso8573, dllmin, valmin, co2lmin, co2reinheit, o2lmin, o2reinheit, laserklasse) {

                                                                    $.ajax({
                                                                        url: "addRoom_all.php",
                                                                        data: {
                                                                            "raumnummer": nummer,
                                                                            "raumbezeichnung": rbez,
                                                                            "funktionsteilstelle": funktionsteilstelle,
                                                                            "MTrelevant": MTrelevant,
                                                                            "funktionelleraumnr": funktionelleraumnr,
                                                                            "raumnummernutzer": raumnummernutzer,
                                                                            "raumbereichnutzer": raumbereichnutzer,
                                                                            "geschoss": geschoss,
                                                                            "bauetappe": bauetappe,
                                                                            "bauabschnitt": bauabschnitt,
                                                                            "nutzfläche": nutzfläche,
                                                                            "abdunkelbarkeit": abdunkelbarkeit,
                                                                            "strahlenanwendung": strahlenanwendung,
                                                                            "laseranwendung": laseranwendung,
                                                                            "h6020": h6020,
                                                                            "gmp": gmp,
                                                                            "iso": iso,
                                                                            "_1kreiso2": _1kreiso2,
                                                                            "_2kreiso2": _2kreiso2,
                                                                            "o2": o2,
                                                                            "_1kreisva": _1kreisva,
                                                                            "_2kreisva": _2kreisva,
                                                                            "va": va,
                                                                            "_1kreisdl5": _1kreisdl5,
                                                                            "_2kreisdl5": _2kreisdl5,
                                                                            "dl5": dl5,
                                                                            "dl10": dl10,
                                                                            "dltech": dltech,
                                                                            "co2": co2,
                                                                            "h2": h2,
                                                                            "he": he,
                                                                            "herf": herf,
                                                                            "ar": ar,
                                                                            "n2": n2,
                                                                            "nga": nga,
                                                                            "n2o": n2o,
                                                                            "av": av,
                                                                            "sv": sv,
                                                                            "zsv": zsv,
                                                                            "usv": usv,
                                                                            "itanbindung": itanbindung,
                                                                            "anwendungsgruppe": anwendungsgruppe,
                                                                            "allgemeinehygieneklasse": allgemeinehygieneklasse,
                                                                            "raumhoehe": raumhoehe,
                                                                            "raumhoehe2": raumhoehe2,
                                                                            "belichtungsfläche": belichtungsfläche,
                                                                            "umfang": umfang,
                                                                            "volumen": volumen,
                                                                            "etanschlussleistungw": etanschlussleistungw,
                                                                            "htwärmeabgabew": htwärmeabgabew,
                                                                            "vexatzone": vexatzone,
                                                                            "htabluftvakuumpumpe": htabluftvakuumpumpe,
                                                                            "htabluftschweissabsaugungstk": htabluftschweissabsaugungstk,
                                                                            "htabluftessestk": htabluftessestk,
                                                                            "htabluftrauchgasabzugstk": htabluftrauchgasabzugstk,
                                                                            "htabluftdigestoriumstk": htabluftdigestoriumstk,
                                                                            "htpunktabsaugungstk": htpunktabsaugungstk,
                                                                            "htabluftsicherheitsschrankunterbaustk": htabluftsicherheitsschrankunterbaustk,
                                                                            "htabluftsicherheitsschrankstk": htabluftsicherheitsschrankstk,
                                                                            "htspuelestk": htspuelestk,
                                                                            "htkühlwasser": htkühlwasser,
                                                                            "o2mangel": o2mangel,
                                                                            "co2melder": co2melder,
                                                                            "etrj45ports": etrj45ports,
                                                                            "et64a3phasigeinzelanschluss": et64a3phasigeinzelanschluss,
                                                                            "et32a3phasigeinzelanschluss": et32a3phasigeinzelanschluss,
                                                                            "et16a3phasigeinzelanschluss": et16a3phasigeinzelanschluss,
                                                                            "etdigestoriummsr230vsvstk": etdigestoriummsr230vsvstk,
                                                                            "et5x10mm2digestoriumstk": et5x10mm2digestoriumstk,
                                                                            "et5x10mm2usvstk": et5x10mm2usvstk,
                                                                            "et5x10mm2svstk": et5x10mm2svstk,
                                                                            "et5x10mm2avstk": et5x10mm2avstk,
                                                                            "wasserqual3lmin": wasserqual3lmin,
                                                                            "wasserqual2ltag": wasserqual2ltag,
                                                                            "wasserqual1ltag": wasserqual1ltag,
                                                                            "wasserqual3": wasserqual3,
                                                                            "wasserqual2": wasserqual2,
                                                                            "wasserqual1": wasserqual1,
                                                                            "lhe": lhe,
                                                                            "lnltag": lnltag,
                                                                            "ln": ln,
                                                                            "n2reinheit": n2reinheit,
                                                                            "n2lmin": n2lmin,
                                                                            "arreinheit": arreinheit,
                                                                            "arlmin": arlmin,
                                                                            "hereinheit": hereinheit,
                                                                            "helmin": helmin,
                                                                            "h2reinheit": h2reinheit,
                                                                            "h2lmin": h2lmin,
                                                                            "dliso8573": dliso8573,
                                                                            "dllmin": dllmin,
                                                                            "valmin": valmin,
                                                                            "co2lmin": co2lmin,
                                                                            "co2reinheit": co2reinheit,
                                                                            "o2lmin": o2lmin,
                                                                            "o2reinheit": o2reinheit,
                                                                            "laserklasse": laserklasse
                                                                        },
                                                                        type: "GET",
                                                                        success: function (data) {
                                                                            alert(data);
                                                                            window.location.replace("roombookSpecifications_New.php");
                                                                        }
                                                                    });
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

                                                                function init_btn_4_dt() {
                                                                    let spacer = {extend: 'spacer', style: 'bar'};
                                                                    new $.fn.dataTable.Buttons(table, {
                                                                        buttons: [
                                                                            spacer, {extend: 'searchBuilder', label: "Search B"}, spacer,

                                                                            buttonRanges.map(button => ({
                                                                                    text: button.name,
                                                                                    className: 'btn_vis',
                                                                                    action: function (e, dt, node, config) {
                                                                                        toggleColumns(dt, button.start, button.end, button.name); // -1 cause i deleted non working first column
                                                                                    }
                                                                                })),

                                                                            spacer,
                                                                            {
                                                                                text: 'w/ Data',
                                                                                className: 'btn_vis',
                                                                                id: 'tgl_vis_btn',
                                                                                action: function (e, dt, node, config) {
                                                                                    checkAndToggleColumnsVisibility(dt);
                                                                                }
                                                                            },
                                                                            spacer, 'copy', 'excel', 'csv', spacer, 'selectAll', 'selectNone',
                                                                            spacer, // spacer,
                                                                            {
                                                                                text: 'Raum',
                                                                                className: 'btn btn_vis far fa-plus-square',
                                                                                action: function (e, dt, node, config) {
                                                                                    //  find_current_max_roomID();
                                                                                    $('#addRoomModal').modal('show'); //imported from rbSpecifications_New_modal_addRoom
                                                                                }
                                                                            }, spacer,
                                                                            {
                                                                                text: "R.Kopieren",
                                                                                className: "btn far fa-plus-square",
                                                                                action: function (e, dt, node, config)
                                                                                {
                                                                                    copySelectedRow();
                                                                                }
                                                                            }, spacer
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
                                                                    const button = $(`.btn_vis:contains('${button_name}')`);
                                                                    if (vis) {
                                                                        button.removeClass('btn_invis');
                                                                    } else {
                                                                        button.addClass('btn_invis');
                                                                    }
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

                                                                function init_showRoomElements_btn() {
                                                                    $("#showRoomElements").html("<i class='fas fa-caret-right'></i>");
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

                                                                $("#saveNewRoom").click(function () {
                                                                    var nummer = $("#nummer").val();
                                                                    var name = $("#name").val(); // var raumbereich = $("#raumbereich").val();
                                                                    var funktionsteilstelle = $("#funktionsstelle").val();
                                                                    var MTrelevant = $("#mt-relevant").val();
                                                                    save_new_room(nummer, name, funktionsteilstelle, MTrelevant);
                                                                });


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

                                                            </script>
                                                            </body> 
                                                            </html>
