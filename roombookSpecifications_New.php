<?php
session_start();
include '_utils.php';
init_page_serversides();
include 'roombookSpecifications_New_modal_addRoom.php';
?> 

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml"/>
<head>
    <title>RB-Bauangaben</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
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
    <style>
        .btn_vis, .btn_invis {
            color: black;
            box-shadow: 0 2px 2px 0 rgba(100, 140, 25, 0.2), 0 2px 2px 0 rgba(100, 140, 25, 0.2);
        }
        .btn_vis {
            background-color: rgba(100, 140, 25, 0.1) !important;
            font-weight: 600;
        }
        .btn_invis {
            background-color: rgba(100, 0, 25, 0.1) !important;
            font-weight: 400;
        }
        .btn-right{
            float: right !important;
        }
        .btn, .fix_size {
            padding: 0.1vw 0.2vw !important;
            margin: 0 1px !important;
            height: 35px !important;
        }
        .fix_size {
            padding: 0.2vw 0.3vw !important;
            margin: 0 0.05vw !important;
        }
        .fix_size_search{
            height: 35px;
            width: 105px;
        }
        .table>thead>tr>th {
            background-color: rgba(100, 140, 25, 0.1);
        }
        .form-check-inputz, .form-check-input:checked {
            width: 20px !important;
            height: 32px !important;
        }
        .form-check-input:checked {
            background-color: rgba(100, 140, 25, 0.75) !important;
        }
        .card-body{
            padding: 5px;
        }
    </style> 
</head> 
<body style="height:100%">  
    <div id="limet-navbar"></div>
    <main class="container-fluid">
        <section class="mt-1 card">
            <header class="card-header d-flex border-light" style="height: 1vh; font-size: 1vh;" id="btnLabelz">
                <div class="col-md-3"><strong>Edit & Filter</strong></div>
                <div class="col-md-1"><strong>Auswahl</strong></div>
                <div class="col-md-6"><strong>Sichtbarkeit</strong></div>
                <div class="col-md-1"><strong>Neu & Output</strong></div>
                <div class="col-md-1"><strong style="float: right;">Check&Settings</strong></div>
            </header>
            <div class="card-header container-fluid d-flex align-items-start border-dark">
                <div class="col-md-3 d-flex align-items-left" id='TableCardHeader'></div>
                <div class="col-md-1 d-flex align-items-center" id="TableCardHeaderX"></div>
                <div class="col-md-6 d-flex align-items-center" id="TableCardHeader2"></div>
                <div class="col-md-1 d-flex align-items-right" id='TableCardHeader3'></div>
                <div class="col-md-1 align-items-right" id='TableCardHeader4'></div>
            </div>
            <div class="card-body" id="table_container_div">
                <table class="table display compact table-responsive table-striped table-bordered table-sm sticky" width="100%" id="table_rooms">
                    <thead><tr></tr></thead>
                    <tbody><td></td></tbody>
                </table>
            </div>
        </section>
        <section class='d-flex bd-highlight'>
            <div class='mt-4 mr-2 card flex-grow-1'>
                <header class="card-header card_header_size"><b>Bauangaben</b></header>
                <div class="card-body" id="bauangaben"></div>
            </div>
            <div class="mt-4 card">
                <div class="card d-inline-flex">
                    <header class="card-header card_header_size">
                        <button type="button" class="btn btn-outline-dark" id="showRoomElements"><i class="fas fa-caret-left"></i></button>
                        <input type="text" class="pull-right fix_size" id="diy_searcher" placeholder="Search...">
                    </header>
                    <div class="card-body" id="additionalInfo">
                        <p id="roomElements"></p>
                        <p id="elementParameters"></p>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <div class="modal fade" id="einstellungModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <header class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Settings</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </header>
                <div class="modal-body">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="settings_save_state_4all_projects">
                            <label class="form-check-label" for="settings_save_state_4all_projects">Save Table State (all projects)</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="settings_save_state">
                            <label class="form-check-label" for="settings_save_state">Save Table State (current project)</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="settings_save_edit_cbx">
                            <label class="form-check-label" for="settings_save_edit_cbx">Initiate Editable</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="settings_show_btn_grp_labels">
                            <label class="form-check-label" for="settings_show_btn_grp_labels">Show Labels above Button</label>
                    </div>
                    <div class="form-check">
                        <button class="form-check-button" id="settings_toggle_btn_texts">Show Button Texts</button>
                    </div>
                </div>
                <footer class="modal-footer">
                    <button type="button" class="btn btn-warning" onclick="restoreDefaults()">Restore Default</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" onclick="saveSettings()">Save changes</button>
                </footer>
            </div>
        </div>
    </div>
</body> 

<script src="roombookSpecifications_constDeclarations.js"></script> 
<script>

                        var projectID = <?php echo json_encode($_SESSION["projectID"]); ?>;
                        var table;
                        let toastCounter = 0;
                        var cellText = "";
                        var currentRowInd = 0;
                        var currentColInd = 0;
                        let current_edit = false; //variable keeps track if the input field to ediot the cells is open

                        $(document).ready(function () {
                            loadSettings();
                            init_dt();
                            init_editable_checkbox();
                            add_MT_rel_filter('#TableCardHeader');
                            move_dt_search();
                            init_showRoomElements_btn();
                            init_btn_4_dt();
                            init_visibilities();
                            table_click();
                            event_table_keyz();

                            $('#settings_show_btn_grp_labels').change(function () {
                                change_top_label_visibility($(this).is(':checked'));
                            });
                        });

                        function change_top_label_visibility(x) {
                            if (x) {
                                $('#btnLabelz').attr("style", "font-size: 1vh !important; height: 1vh !important; display: flex !important; ");

                            } else {
                                $('#btnLabelz').attr("style", "display: none !important");
                            }
                        }

                        let buttons_group_selct = [
                            {
                                text: '',
                                className: 'btn fas fa-check',
                                titleAttr: "Select All",
                                action: function () {
                                    table.rows().select();
                                }
                            }, {
                                text: '',
                                className: 'btn fas fa-eye',
                                titleAttr: "Select Visible",
                                action: function () {
                                    table.rows(':visible').select();
                                }
                            }, {
                                text: '',
                                titleAttr: "Deselect All Rows",
                                action: function () {
                                    table.rows().deselect();
                                },
                                className: 'btn fas fa-times'
                            }
                        ];

                        let btn_grp_new_out = [
                            {
                                text: '',
                                className: 'btn fas fa-plus-square',
                                titleAttr: "Add Room",
                                action: function () {
                                    $('#addRoomModal').modal('show');
                                }
                            }, {
                                text: '',
                                className: "btn fas fa-window-restore",
                                titleAttr: "Copy Selected Row",
                                action: function () {
                                    copySelectedRow();
                                }
                            }, {
                                extend: 'excelHtml5',
                                exportOptions: {
                                    columns: ':visible'
                                },
                                className: 'btn fa fa-download',
                                text: "",
                                titleAttr: "Download as Excel"
                            }
                        ];

                        let btn_grp_settings = [{
                                text: "",
                                className: 'btn btn-right fas fa-cogs',
                                titleAttr: "Open Settings",
                                action: function () {
                                    open_einstellung_modal();
                                }
                            }, {
                                text: "",
                                titleAttr: "Bauangaben Check",
                                className: "btn btn-right fas fa-check",
                                action: function () {
                                    check_angaben();
                                }
                            }
                        ];

                        let buttonsGroupcolumnVisbilities = [{
                                extend: 'colvis',
                                text: 'Vis',
                                columns: ':gt(5)',
                                collectionLayout: 'fixed columns',
                                className: 'btn'
                            }].concat(buttonRanges.map(button => ({
                                text: button.name,
                                className: 'btn btnx btn_vis',
//                                    longName: button.longName,
                                action: function (e, dt, node, config) {
                                    toggleColumns(dt, button.start, button.end, button.name);
                                    updateButtonClass(node, dt, button.start, button.end);
                                }
                            })));

                        let searchbuilder = [{
                                extend: 'searchBuilder',
                                className: "btn fas fa-search",
                                text: " ",
                                titleAttr: "Suche konfigurieren"
                            }];

                        function init_btn_4_dt() {
                            new $.fn.dataTable.Buttons(table, {buttons: searchbuilder}).container().appendTo($('#TableCardHeader'));
                            new $.fn.dataTable.Buttons(table, {buttons: buttons_group_selct}).container().appendTo($('#TableCardHeaderX'));
                            new $.fn.dataTable.Buttons(table, {buttons: buttonsGroupcolumnVisbilities}).container().appendTo($('#TableCardHeader2'));
                            new $.fn.dataTable.Buttons(table, {buttons: btn_grp_new_out}).container().appendTo($('#TableCardHeader3'));
                            new $.fn.dataTable.Buttons(table, {buttons: btn_grp_settings}).container().appendTo($('#TableCardHeader4'));
                        }

                        function toggleButtonTexts() {
                            const buttonGroups = [
                                buttons_group_selct,
                                btn_grp_new_out,
                                btn_grp_settings
                            ];
                            buttonGroups.forEach(group => {
                                group.forEach(button => {
                                    const buttonElement = document.querySelector(`button[title="${button.titleAttr}"]`);
                                    if (buttonElement && !buttonElement.classList.contains('btnx')) {
                                        if (buttonElement.textContent) {
                                            buttonElement.textContent = '';
                                        } else {
                                            buttonElement.textContent = button.longText || button.titleAttr;
                                        }
                                    }
                                });
                            });
                        }

                        function handleButtonClick() {
                            const button = document.getElementById('settings_toggle_btn_texts');
                            button.addEventListener('click', function () {
                                toggleButtonTexts();
                            });
                        }

                        document.addEventListener('DOMContentLoaded', function () {
                            handleButtonClick();
                        });

//SETTINGS SECTION                             
                        function restoreDefaults() {
                            localStorage.clear();
                            table.state.clear();
                            location.reload();
                            localStorage.setItem('settings_show_btn_grp_labels', true);
                        }

                        function loadSettings() {
                            document.getElementById('settings_show_btn_grp_labels').checked = JSON.parse(localStorage.getItem('settings_show_btn_grp_labels')) || true;
                            change_top_label_visibility(JSON.parse(localStorage.getItem('settings_show_btn_grp_labels')) || true);
                            document.getElementById('settings_save_state_4all_projects').checked = JSON.parse(localStorage.getItem('settings_save_state_4all_projects')) || false;
                            document.getElementById('settings_save_state').checked = JSON.parse(localStorage.getItem('settings_save_state' + projectID)) || false;
                            document.getElementById('settings_save_edit_cbx').checked = JSON.parse(localStorage.getItem('settings_save_edit_cbx')) || false;
                            // document.getElementById('settings_save_search').checked = JSON.parse(localStorage.getItem('settings_save_search')) || false;
                            // document.getElementById('settings_show_btn_texts_all').checked = JSON.parse(localStorage.getItem('settings_show_btn_texts_all')) || false;

                        }

                        function saveSettings() {
                            localStorage.setItem('settings_show_btn_grp_labels', document.getElementById('settings_show_btn_grp_labels').checked);
                            localStorage.setItem('settings_save_state_4all_projects', document.getElementById('settings_save_state_4all_projects').checked);
                            localStorage.setItem('settings_save_state' + projectID, document.getElementById('settings_save_state').checked);
                            localStorage.setItem('settings_save_edit_cbx', document.getElementById('settings_save_edit_cbx').checked);
                            // localStorage.setItem('settings_save_search', document.getElementById('settings_save_search').checked);
                            // localStorage.setItem('settings_show_btn_texts_all', document.getElementById('settings_show_btn_texts_all').checked);

                            $('#einstellungModal').modal('hide');
                        }

                        function open_einstellung_modal() {
                            $('#einstellungModal').modal('show');
                        }

                        function check_angaben() {
                            var selectedRows = table.rows({selected: true}).data();
                            var roomIDs = [];
                            for (var i = 0; i < selectedRows.length; i++) {
                                roomIDs.push(selectedRows[i]['idTABELLE_Räume']);
                            }
                            if (roomIDs.length === 0) {
                                alert("Kein Raum ausgewählt!");
                            } else {
                                window.open('/roombookBauangabenCheck.php?roomID=' + roomIDs);
                            }
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
                                    if (rowIndex !== currentRowInd && !document.getElementById('checkbox_EditableTable').checked) {
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
                                "H6020": [" - ", "H1a", "H1b", "H2a", "H2b", "H2c", "H3", "H4", "ÖNORM S 5224"],
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
                            $(document).on('click', '#table_rooms tbody tr', function () {
                                var RaumID = table.row($(this)).data()['idTABELLE_Räume'];
                                if ($('#checkbox_EditableTable').is(':checked')) {
                                    var Raumbez = table.row($(this)).data()['Raumbezeichnung'];
                                    var rowIndex = $(this).closest('tr').index();
                                    var columnIndex = $(this).find('td').index(event.target);
                                    if (columnIndex === -1) {
                                        columnIndex = currentColInd;
                                    }
                                    var index_accounting_4_visibility = columnIndex;
                                    var visibleColumns = table.columns().visible();
                                    for (var i = 0; i <= index_accounting_4_visibility; i++) {
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
                                    if (getCase(dataIdentifier) !== "none-edit") {
                                        if (!current_edit) {
                                            cell.html(html_2_plug_into_edit_cell(dataIdentifier));
                                            table.keys.disable();
                                        }
                                        current_edit = true;
                                        cell.find('input, select').focus();
                                        table.keys.disable();
                                        cell.find('input, select').on('keydown blur', function (event) {
                                            if (event.keyCode === 13 && current_edit) {
                                                var newData = format_data_input($(this).val(), dataIdentifier);
                                                if (newData.trim() !== "") {
                                                    cellText = newData;
                                                    cell.html(newData);
                                                    current_edit = false;
                                                    table.keys.enable();
                                                    save_changes(RaumID, dataIdentifier, newData, Raumbez);
                                                    table.cell(cell.index()).select();
                                                }
                                            }
                                            if (event.keyCode === 27 || event.type === "blur" || event.keyCode === 9) {
                                                cell.html(cellText);
                                                current_edit = false;
                                                table.keys.enable();
                                                table.cell(cell.index()).select();
                                                initializeToaster("Changes NOT Saved", " - ", false);
                                            }
                                        });
                                    }
                                }
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
                                                        var tableX = $('#myTable').DataTable();
                                                        tableX.destroy();
                                                        if (!data || data.trim() === "") {
                                                            $("#roomElements").empty();
                                                        } else {
                                                            $("#roomElements").html(data);
                                                            $('#myTable').DataTable();
                                                        }
                                                        $('#elementParameters').empty();
                                                    },
                                                    error: function (jqXHR, textStatus, errorThrown) {
                                                        console.error("AJAX call failed: " + textStatus + ", " + errorThrown);
                                                    }
                                                });
                                            }
                                        });
                                    }
                                });
                            });


                            /* $('#table_rooms tbody').on('click', 'tr', function () {
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
                             console.log("Success loading MT table");
                             var tableX = $('#myTable').DataTable();
                             tableX.destroy();
                             if (!data || data.trim() === "") {
                             // If data is empty, clear the old content
                             $("#roomElements").empty();
                             } else {
                             $("#roomElements").html(data);
                             $('#myTable').DataTable();
                             }
                             $('#elementParameters').empty();
                             let debounceTimer;
                             $('#diy_searcher').on('keyup', function () {
                             clearTimeout(debounceTimer);
                             debounceTimer = setTimeout(() => {
                             try {
                             $('#tableRoomElements').DataTable().search(this.value).draw();
                             } catch (e) {
                             console.log(e);
                             alert("!", e);
                             }
                             }, 300); // Adjust the delay as needed
                             });
                             }, error: function (jqXHR, textStatus, errorThrown) {
                             console.error("AJAX call failed: " + textStatus + ", " + errorThrown);
                             }
                                 
                             });
                             }
                             });
                             }
                             });
                             }); */
                        }

                        function move_dt_search() {
                            var dt_searcher = document.getElementById("dt-search-0");
                            dt_searcher.parentNode.removeChild(dt_searcher);
                            document.getElementById("TableCardHeader").appendChild(dt_searcher);
                            dt_searcher.classList.add("fix_size_search");
                        }

                        function init_editable_checkbox() {
                            var checkbox = $('<input>', {
                                type: 'checkbox',
                                name: 'EditableTable',
                                id: 'checkbox_EditableTable',
                                checked: document.getElementById('settings_save_edit_cbx').checked,
                                class: 'form-check-inputz  form-check-input fix_size'
                            }).appendTo($('#TableCardHeader'));
                            //                                                    var label = $('<label>', {
                            //                                                        htmlFor: 'checkbox_EditableTable',
                            //                                                        class: 'rotated form-check-label inline',
                            //                                                        text: "-EDIT-"});
                            //                                                    var container = $('<span>').append(checkbox);
                            //                                                    $('#TableCardHeader').append(container).append(label);
                        }

                        function init_dt() {
                            let savestate = document.getElementById('settings_save_state').checked || document.getElementById('settings_save_state_4all_projects').checked;
                            console.log(savestate);
                            table = new DataTable('#table_rooms', {
                                ajax: {
                                    url: 'get_rb_specs_data.php',
                                    dataSrc: ''
                                },
                                columns: columnsDefinition,
                                dom: '  <"TableCardHeader"f>t<"btm.d-flex justify-content-between"lip>   ',
                                scrollX: true,
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
                                        stateSave: savestate
                                    }
                                },
                                keys: true,
                                order: [[3, 'asc']],
                                stateSave: savestate,
                                info: true,
                                paging: true,
                                pagingType: "simple_numbers",
                                pageLength: 10,
                                lengthMenu: [
                                    [5, 10, 20, 50],
                                    ['5 rows', '10 rows', '20 rows', '50 rows']
                                ],
                                compact: true
                            });
                        }

                        function add_MT_rel_filter(location) {
                            var dropdownHtml = '<select class=" fix_size" id="columnFilter">' + '<option value="">MT</option><option value="Ja">Ja</option>' + '<option value="Nein">Nein</option></select>';
                            $(location).append(dropdownHtml);
                            $('#columnFilter').change(function () {
                                var filterValue = $(this).val();
                                table.column('MT-relevant:name').search(filterValue).draw();
                            });
                        }


//                           

//VISIBILITY 
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


                        function checkAndToggleColumnsVisibility() {
                            let singleButton = document.querySelector('.toggleDatalessColumnsButton');
                            if (singleButton.classList.contains('btn_vis')) {
                                singleButton.classList.add('btn_invis');
                                singleButton.classList.remove('btn_vis');
                            } else {
                                singleButton.classList.add('btn_vis');
                                singleButton.classList.remove('btn_invis');
                            }

                            table.columns().every(function () {
                                var hasNonEmptyCell = this.data().toArray().some(function (cellData) {
                                    return cellData !== null && cellData !== undefined && cellData !== '' && cellData !== '-' && cellData !== ' ' && cellData !== '  ' && cellData !== '   ' && cellData !== '.';
                                });
                                if (!hasNonEmptyCell) {
                                    this.visible(!this.visible());
                                }
                            });
                        }

                        function updateButtonClass(button, table, startColumn, endColumn) {
                            const columns = table.columns().indexes();
                            var vis = table.column(columns[endColumn]).visible();
                            if (vis) {
                                $(button).removeClass('btn_invis');
                                $(button).addClass('btn_vis');
                            } else {
                                $(button).removeClass('btn_vis');
                                $(button).addClass('btn_invis');
                            }
                        }

                        function toggleColumns(table, startColumn, endColumn, button_name) {
                            const columns = table.columns().indexes();
                            var vis = !table.column(columns[endColumn]).visible();
                            for (let i = startColumn; i <= endColumn; i++) {
                                table.column(columns[i]).visible(vis);
                            }
                            if (button_name === 'All') {
                                buttonRanges.forEach(button => {
                                    const btn = $(`.btn_vis:contains('${button.name}')`);
                                    if (vis) {
                                        btn.removeClass('btn_invis');
                                        btn.addClass('btn_vis');
                                    } else {
                                        btn.removeClass('btn_vis');
                                        btn.addClass('btn_invis');
                                    }
                                });
                            } else if (button_name === 'LAB') {
                                ['-GAS', '-ET', '-HT', '-H2O'].forEach(name => {
                                    const button = $(`.btn_vis:contains('${name}')`);
                                    if (vis) {
                                        button.removeClass('btn_invis');
                                        button.addClass('btn_vis');
                                    } else {
                                        button.removeClass('btn_vis');
                                        button.addClass('btn_invis');
                                    }
                                });
                            } else {
                                const button = $(`.btn_vis:contains('${button_name}')`);
                                if (vis) {
                                    button.removeClass('btn_invis');
                                    button.addClass('btn_vis');
                                } else {
                                    button.removeClass('btn_vis');
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

//SAVEING/CPYNG Rooms
                        function initializeToaster(headerText, subtext, success) {
                            // Check if the maximum number of toasts is reached
                            if (toastCounter >= 10) {
                                const oldestToast = document.querySelector('.toast');
                                if (oldestToast) {
                                    oldestToast.remove();
                                    toastCounter--;
                                }
                            }

                            const toast = document.createElement('div');
                            toast.classList.add('toast', 'fade', 'show');
                            toast.setAttribute('role', 'alert');
                            toast.style.position = 'fixed';
                            const topPosition = 10 + toastCounter * 50;
                            toast.style.top = `${topPosition}px`;
                            toast.style.right = '10px';
                            headerText = headerText.replace(/\n/g, '<br>'); // Replace \n with <br>
                            subtext = subtext.replace(/\n/g, '<br>'); // Replace \n with <br>
                            toast.innerHTML = `
                <div class="toast-header ${success ? "btn_vis" : "btn_invis"}">
                    <strong class="mr-auto">${headerText} ${subtext}</strong>
                </div>`;
                            document.body.appendChild(toast);
                            toastCounter++;
                            setTimeout(() => {
                                toast.classList.remove('show');
                                setTimeout(() => {
                                    toast.remove();
                                    toastCounter--;
                                }, 500); // Match this duration with the fadeOut animation duration
                            }, 2000 + toastCounter * 100);
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
                        $("#saveNewRoom").click(function () {
                            var nummer = $("#nummer").val();
                            var name = $("#name").val(); // var raumbereich = $("#raumbereich").val();
                            var funktionsteilstelle = $("#funktionsstelle").val();
                            var MTrelevant = $("#mt-relevant").val();
                            console.log(funktionsteilstelle);
                            save_new_room(nummer, name, funktionsteilstelle, MTrelevant);
                        });
                        function save_new_room(nummer, name, funktionsteilstelle, MTrelevant) {
                            if (nummer !== "" && name !== "" && MTrelevant !== "" && funktionsteilstelle !== "") {  //& flaeche  !== "" && geschoss !== "" && bauetappe  !== "" && bauteil  !== "" && funktionsteilstelle !== 0 
                                $.ajax({
                                    url: "addRoom_all.php", // "ID": raumID,
                                    data: {"tabelle_projekte_idTABELLE_Projekte": <?php echo $_SESSION["projectID"]; ?>, "Raumnr": nummer, "Raumbezeichnung": name, "TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen": funktionsteilstelle, "MT-relevant": MTrelevant},
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

                        function numsave_new_room_all(dataObject) {
                            $.ajax({
                                url: "addRoom_all.php",
                                data: dataObject,
                                type: "GET",
                                success: function (data) {
                                    alert(data);
                                    window.location.replace("roombookSpecifications_New.php");
                                }
                            });
                        }

                        function copySelectedRow() {
                            if (!confirm('Raum Kopieren??')) {
                                return 0;
                            }
                            let selectedRowData = table.row('.selected').data();
                            table.row.add(selectedRowData).draw();
                            let requestData = {};
                            columnsDefinition.forEach(column => {
                                let field = column.data;
                                let dbFieldName = field.replace(/[ ]/g, '+'); // Replace dots and spaces with underscores to match the field names in the PHP file
                                requestData[dbFieldName] = selectedRowData[field];
                            });
                            delete requestData.idTABELLE_Räume;
                            console.log("copySelectedRow:", requestData);
                            numsave_new_room_all(requestData);
                        }

                        //UTILITIES
//                    function logColumnValues() {
//                        var data = table.rows().data();
//                        console.log("logging coulumns");
//                        data.each(function (row) {
//                            console.log(row["Raumnr"]);
//                        });
//                    }
//
//                    function logDuplicateColumnValues() {
//                        var data = table.rows().data();
//                        console.log("Logging duplicate columns");
//                        var valueCounts = {};
//                        data.each(function (row) {
//                            var value = row["Raumnr"];
//                            if (valueCounts[value]) {
//                                valueCounts[value]++;
//                            } else {
//                                valueCounts[value] = 1;
//                            }
//                        });
//                        data.each(function (row) {
//                            var value = row["Raumnr"];
//                            if (valueCounts[value] > 1) {
//                                console.log(value);
//                            }
//                        });
//                    }

//                    function logAndSaveDuplicateColumnValues() {
//                        var data = table.rows().data();
//                        console.log("Logging duplicate columns");
//                        var valueCounts = {};
//                        data.each(function (row) {
//                            var value = row["Raumnr"];
//                            if (valueCounts[value]) {
//                                valueCounts[value]++;
//                            } else {
//                                valueCounts[value] = 1;
//                            }
//                        });
//                        var duplicates = [];
//                        data.each(function (row) {
//                            var value = row["Raumnr"];
//                            if (valueCounts[value] > 1) {
//                                console.log(value);
//                                duplicates.push(value);
//                            }
//                        });
//                        // Save duplicates to a CSV file
//                        var csvContent = "data:text/csv;charset=utf-8,Raumnr\n";
//                        duplicates.forEach(function (duplicate) {
//                            csvContent += duplicate + "\n";
//                        });
//                        var encodedUri = encodeURI(csvContent);
//                        var link = document.createElement("a");
//                        link.setAttribute("href", encodedUri);
//                        link.setAttribute("download", "duplicates.csv");
//                        document.body.appendChild(link);
//                        link.click();
//                        document.body.removeChild(link);
//                    }

</script>

</html>


