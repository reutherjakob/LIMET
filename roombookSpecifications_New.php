<?php
// V2.0: 2024-11-29, Reuther & Fux
include '_utils.php';
init_page_serversides();
include 'roombookSpecifications_addRoomModal.php';
include 'roombookSpecifications_HelpModal.php';
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de"></html>
<head>
    <title>RB-Bauangaben</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="stylesheet" href="style.css" type="text/css" media="screen"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"/>

    <!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous"/>-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css"
          integrity="sha512-q3eWabyZPc1XTCmF+8/LuE1ozpg5xxn7iO89yfSOd5/oKvyqLngoNGsx8jq92Y8eXJ/IRxQbEC+FGSYxtk2oiw=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link href="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.0.5/af-2.7.0/b-3.0.2/b-colvis-3.0.2/b-html5-3.0.2/b-print-3.0.2/cr-2.0.1/date-1.5.2/fc-5.0.0/fh-4.0.1/kt-2.12.0/r-3.0.2/rg-1.5.0/rr-1.5.0/sc-2.4.1/sb-1.7.1/sp-2.3.1/sl-2.0.1/sr-1.4.1/datatables.min.css"
          rel="stylesheet"/>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.9.2/umd/popper.min.js"
            integrity="sha512-2rNj2KJ+D8s1ceNasTIex6z4HWyOnEYLVC3FigGOmyQCZc2eBXKgOxQmo3oKLHyfcj53uz4QMsRCWNbLd32Q1g=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
            integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy"
            crossorigin="anonymous"></script>
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

        .btn, .fix_size, .btn-group .btn {
            padding: 0.2vw 0.2vw !important;
            margin: 0 -1px !important;
            height: 30px !important;
        }

        .fix_size_search {
            height: 30px;
            width: 150px;
        }

        .table > thead > tr > th {
            background-color: rgba(100, 140, 25, 0.1);
        }

        .form-check-inputz, .form-check-inputz:checked {
            width: 20px !important;
            height: 30px !important;
        }

        .form-check-input:checked {
            background-color: rgba(100, 140, 25, 0.75) !important;
        }

        .card-body {
            padding: 5px;
        }
    </style>
</head>
<body>
<div id="limet-navbar"></div>

<main class="container-fluid">

    <section class="mt-1 card">
        <header class="card-header d-flex border-light" style="height: 1vh; font-size: 1vh;" id="btnLabelz">
            <div class="col-md-4"><strong>Edit & Filter</strong></div>
            <div class="col-md-1 d-flex justify-content-end "><strong>Auswahl</strong></div>
            <div class="col-md-1"></div>
            <div class="col-md-3"><strong>Sichtbarkeit</strong></div>
            <div class="col-md-1 d-flex justify-content-end align-items-right "><strong>Neu & Output</strong></div>
            <div class="col-md-2 d-flex justify-content-end align-items-right"><strong style="float: right;">Check&Settings</strong>
            </div>
        </header>
        <div class="card-header container-fluid d-flex align-items-start border-dark">
            <div class="col-sm-4 d-flex justify-content-left align-items-left" id='TableCardHeader'></div>
            <div class="col-md-1 d-flex justify-content-end align-items-center" id="TableCardHeaderX"></div>
            <div class="col-md-4 d-flex justify-content-center align-items-center" id="TableCardHeader2">

            </div>
            <div class="col-md-1 d-flex justify-content-end align-items-right" id='TableCardHeader3'></div>
            <div class="col-md-2 d-flex justify-content-end align-items-right" id='TableCardHeader4'></div>
        </div>
        <div class="card-body" id="table_container_div">
            <table class="table display compact table-responsive table-striped table-bordered table-sm sticky"
                   style="width:100%"
                   id="table_rooms">
                <thead>
                <tr></tr>
                </thead>
                <tbody>
                <tr>
                    <td></td>
                </tr>
                </tbody>
            </table>
        </div>
    </section>
    <section class='d-flex bd-highlight'>
        <div class='mt-4 mr-2 card flex-grow-1'>
            <header class="card-header fix_size"><b>Bauangaben</b></header>
            <div class="card-body" id="bauangaben"></div>
        </div>
        <div class="mt-4 card">
            <div class="card">
                <header class="card-header" id="CardHEaderElemntsInRoom">
                    <button type="button" class="btn btn-outline-dark fix_size" id="showRoomElements"><i
                                class="fas fa-caret-left"></i></button>
                    <label for="diy_searcher"></label><input type="text" class="pull-right fix_size" id="diy_searcher"
                                                             placeholder="Search...">
                </header>
                <div class="card-body" id="additionalInfo">
                    <p id="roomElements"></p>
                    <p id="elementParameters"></p>
                </div>
            </div>
        </div>
    </section>
</main>

<div class="modal fade" id="einstellungModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <header class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Einstellungen</h5>
                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
            </header>
            <div class="modal-body">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="settings_save_state_4all_projects">
                    <label class="form-check-label" for="settings_save_state_4all_projects">Tabellenzustand speichern (f. alle Projekte)</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="settings_save_state">
                    <label class="form-check-label" for="settings_save_state">Tabellenzustand speichern (f. aktuelles Projekte)</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="settings_save_edit_cbx">
                    <label class="form-check-label" for="settings_save_edit_cbx">Tabelle editierbar initiieren</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="settings_show_btn_grp_labels">
                    <label class="form-check-label" for="settings_show_btn_grp_labels">Labels über den Buttons anzeigen</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="settings_toggle_btn_texts">
                    <label class="form-check-label" for="settings_toggle_btn_texts">Button Texte anzeigen</label>
                </div>
            </div>
            <footer class="modal-footer">
                <button type="button" class="btn btn-warning" onclick="restoreDefaults()">Restore Default</button>
                &ensp;
                <button type="button" class="btn btn-secondary" data-bs-dismiss='modal'>Close</button>
                &ensp;
                <button type="button" class="btn btn-success" onclick="saveSettings()">Save changes</button>
            </footer>
        </div>
    </div>
</div>


</body>

<script src="roombookSpecifications_constDeclarations.js"></script>
<script src="_utils.js">

</script>
<script charset=utf-8>

    let projectID = <?php echo json_encode($_SESSION["projectID"]); ?>;
    var table;
    let cellText = "";
    let currentRowInd = 0;
    let currentColInd = 0;
    let current_edit = false; //keeps track if the input field to edit the cells is open
    let Cookie_aktiv_tage = 90;

    $(document).ready(function () {
        loadSettings();
        init_dt();
        console.log("Document ready, setting loaded, dt initiated ");
        init_editable_checkbox();

        move_item("dt-search-0", "TableCardHeader");
        init_showRoomElements_btn();
        init_btn_4_dt();
        init_visibilities();
        table_click();
        event_table_keyz();

        init_filter();
        handleCheckboxChange();

        $('#settings_show_btn_grp_labels').change(function () {
            change_top_label_visibility($(this).is(':checked'));
        });

    });

    function add_MT_rel_filter(location, table) {
        let dropdownHtml = '<select class=" fix_size" id="columnFilter">' + '<option value="">MT</option><option value="Ja">Ja</option>' + '<option value="Nein">Nein</option></select>';
        $(location).append(dropdownHtml);
        $('#columnFilter').change(function () {
            let filterValue = $(this).val();
            table.column('MT-relevant:name').search(filterValue).draw();
        });
    }

    function add_entfallen_filter(location, table) {
        let dropdownHtml2 = '<select class="fix_size" id="EntfallenFilter">' + '<option value="">Entf</option><option value="1">1</option>' + '<option selected ="selected" value="0">0</option></select>';
        $(location).append(dropdownHtml2);
        $('#EntfallenFilter').change(function () {
            let filterValue = $(this).val();
            table.column('Entfallen:name').search(filterValue).draw();
        });
        table.column('Entfallen:name').search(0).draw();
    }

    function init_filter() {
        add_MT_rel_filter('#TableCardHeader', table);
        add_entfallen_filter('#TableCardHeader', table);
        setTimeout(function () {
            if ((document.getElementById('settings_save_state').checked || document.getElementById('settings_save_state_4all_projects').checked) && table.state.loaded()) {
                let columnData = table.column("MT-relevant:name", {
                    search: 'applied',
                    order: 'applied',
                    visible: true
                }).data().toArray();
                if (!(columnData.length === 0)) {
                    let uniqueValues = [...new Set(columnData)];
                    if (uniqueValues.length === 1 && (uniqueValues[0] === '1' || uniqueValues[0] === '0')) {
                        $('#columnFilter').val(uniqueValues[0] === '1' ? 'Ja' : 'Nein').change();
                    }
                }
            }
        }, 100);
    }

    function handleCheckboxChange() {
        const checkbox = document.getElementById('settings_toggle_btn_texts');
        checkbox.addEventListener('change', function () {
            toggleButtonTexts();
        });
    }

    function change_top_label_visibility(x) {
        if (x) {
            $('#btnLabelz').attr("style", "font-size: 1vh !important; height: 1vh !important; display: flex !important; ");
        } else {
            $('#btnLabelz').attr("style", "display: none !important");
        }
    }


    function init_btn_4_dt() {
        const buttons_group_selct = [
            {text: '', className: 'btn fas fa-check', titleAttr: "Select All", action: () => table.rows().select()},
            {
                text: '',
                className: 'btn fas fa-eye',
                titleAttr: "Select Visible",
                action: () => table.rows(':visible').select()
            },
            {
                text: '',
                className: 'btn fas fa-times',
                titleAttr: "Deselect All Rows",
                action: () => table.rows().deselect()
            }
        ];
        const btn_grp_new_out = [
            {
                text: '',
                className: 'btn btn-light fas fa-plus-square',
                titleAttr: "Add Room",
                action: () => $('#addRoomModal').modal('show')
            },
            {
                text: '',
                className: "btn btn-light fas fa-window-restore",
                titleAttr: "Copy Selected Row",
                action: copySelectedRow
            },
            {
                extend: 'excelHtml5',
                exportOptions: {columns: ':visible'},
                className: 'btn fa fa-download',
                text: "",
                titleAttr: "Download as Excel"
            }
        ];
        const btn_grp_settings = [
            {
                text: "",
                className: "btn btn-light fas fa-vote-yea",
                titleAttr: "Bauangaben Check",
                action: check_angaben
            },
            {
                text: "",
                className: "btn btn-light fas fa-info-circle",
                titleAttr: "Help",
                action: () => $('#HelpModal').modal('show')
            },
            {
                text: "",
                className: 'btn btn-light fas fa-cogs',
                titleAttr: "Open Settings",
                action: open_einstellung_modal
            }
        ];
        const buttonsGroupcolumnVisbilities = [
            {extend: 'colvis', text: 'Vis', columns: ':gt(5)', collectionLayout: 'fixed columns', className: 'btn'},
            ...buttonRanges.map(button => ({
                text: button.name, className: 'btn btnx btn_vis',
                action: (e, dt, node) => {
                    toggleColumns(dt, button.start, button.end, button.name);
                    updateButtonClass(node, dt, button.start, button.end);
                }
            })), {
                text: '<i class="fa fa-paper-plane"></i> Report',
                className: 'btn',
                action: toggleReportColumnsVisible
            }
        ];
        const savestate = document.getElementById('settings_save_state').checked || document.getElementById('settings_save_state_4all_projects').checked;
        const searchbuilder = [
            {
                extend: 'searchBuilder',
                text: "",
                className: "btn fas fa-search",
                titleAttr: "Suche konfigurieren",
                stateSave: savestate
            }
        ];
        new $.fn.dataTable.Buttons(table, {buttons: searchbuilder}).container().appendTo($('#TableCardHeader'));
        new $.fn.dataTable.Buttons(table, {buttons: buttons_group_selct}).container().appendTo($('<div class="btn-group"></div>').appendTo($('#TableCardHeaderX')));
        new $.fn.dataTable.Buttons(table, {buttons: buttonsGroupcolumnVisbilities}).container().appendTo($('<div class="btn-group" role="group"></div>').appendTo($('#TableCardHeader2')));
        new $.fn.dataTable.Buttons(table, {buttons: btn_grp_new_out}).container().appendTo($('<div class="btn-group"></div>').appendTo($('#TableCardHeader3')));
        new $.fn.dataTable.Buttons(table, {buttons: btn_grp_settings}).container().appendTo($('<div class="btn-group"></div>').appendTo($('#TableCardHeader4')));

    }

    function init_dt() {
        const savestate = document.getElementById('settings_save_state').checked || document.getElementById('settings_save_state_4all_projects').checked;
        table = new DataTable('#table_rooms', {
                ajax: {url: 'get_rb_specs_data.php', dataSrc: ''},
                columns: columnsDefinition,
                layout: {topStart: null, top: null, bottomStart: ['pageLength', 'info'], bottomEnd: 'paging'},
                scrollX: true,
                scrollCollapse: true,
                language: {
                    search: "", searchBuilder: {
                        button: '(%d)'
                    }
                },
                select: "os",
                fixedColumns: {start: 2},
                fixedHeader: true,
                keys: true,
                order: [{
                    name: 'Raumbezeichnung',
                    dir: 'asc'
                }, {name: 'Nummer', dir: 'asc'}],
                stateSave: savestate,
                pageLength: 10,
                lengthMenu: [[5, 10, 20, 50, -1], ['5 rows', '10 rows', '20 rows', '50 rows', 'All']],
                compact: true,

            }
        );
    }


    function toggleButtonTexts() {
        $('#checkbox_EditableTable').next('label').toggle();
        let buttons_group_selct = [{titleAttr: "Select All"}, {titleAttr: "Select Visible"}, {titleAttr: "Deselect All Rows"}];
        let btn_grp_new_out = [{titleAttr: "Add Room"}, {titleAttr: "Copy Selected Row"}, {titleAttr: "Download as Excel"}];
        let btn_grp_settings = [{titleAttr: "Open Settings"}, {titleAttr: "Bauangaben Check"}];
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

    // --- ---  SETTINGS --- ---
    function restoreDefaults() {
        setCookie('settings_show_btn_grp_labels', "true", Cookie_aktiv_tage);
        setCookie('settings_save_state_4all_projects', "false", Cookie_aktiv_tage);
        setCookie('settings_save_state' + projectID, "false", Cookie_aktiv_tage);
        setCookie('settings_save_edit_cbx', "false", Cookie_aktiv_tage);
        table.state.clear();
        location.reload();
    }

    function ModalInvisible() {
        $("#einstellungModal").modal('hide');
    }

    function setCookie(name, value, days) {
        let expires = "";
        if (days) {
            let date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "") + expires + "; path=/";
    }

    function getCookie(name) {
        let nameEQ = name + "=";
        let ca = document.cookie.split(';');
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) === ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }

    function eraseCookie(name) {
        document.cookie = name + '=; Max-Age=-99999999;';
    }

    function loadSettings() {
        function getCookieValue(name) {
            let value = getCookie(name);
            return value ? JSON.parse(value) : false;
        }

        document.getElementById('settings_show_btn_grp_labels').checked = getCookieValue('settings_show_btn_grp_labels');
        change_top_label_visibility(getCookieValue('settings_show_btn_grp_labels'));
        document.getElementById('settings_save_state_4all_projects').checked = getCookieValue('settings_save_state_4all_projects');
        document.getElementById('settings_save_state').checked = getCookieValue('settings_save_state' + projectID);
        document.getElementById('settings_save_edit_cbx').checked = getCookieValue('settings_save_edit_cbx');
    }

    function saveSettings() {
        const showBtnGrpLabels = document.getElementById('settings_show_btn_grp_labels').checked;
        const saveState4AllProjects = document.getElementById('settings_save_state_4all_projects').checked;
        const saveState = document.getElementById('settings_save_state').checked;
        const saveEditCbx = document.getElementById('settings_save_edit_cbx').checked;

        const prevSaveState4AllProjects = getCookie('settings_save_state_4all_projects') === 'true';
        const prevSaveState = getCookie('settings_save_state' + projectID) === 'true';

        setCookie('settings_show_btn_grp_labels', showBtnGrpLabels, Cookie_aktiv_tage);
        setCookie('settings_save_state_4all_projects', saveState4AllProjects, Cookie_aktiv_tage);
        setCookie('settings_save_state' + projectID, saveState, Cookie_aktiv_tage);
        setCookie('settings_save_edit_cbx', saveEditCbx, Cookie_aktiv_tage);

        if (saveState4AllProjects !== prevSaveState4AllProjects || saveState !== prevSaveState) {
            if (confirm("Um die geänderten Einstellungen wirksam zu machen, muss diese Seite neu geladen werden. Neu Laden?")) {
                location.reload();
            } else {
                $('#einstellungModal').modal('hide');
            }
        } else {
            $('#einstellungModal').modal('hide');
        }
    }

    function open_einstellung_modal() {
        $('#einstellungModal').modal('show');
    }

    // --- --- ANGABEN CHECK --- ---
    function check_angaben() {
        let selectedRows = table.rows({selected: true}).data();
        let roomIDs = [];
        for (let i = 0; i < selectedRows.length; i++) {
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


    function event_table_keyz() {
        table.on('key-focus', function (e, datatable, cell) {
            if (document.getElementById('checkbox_EditableTable').checked && !current_edit) {
                cell.node().click();
                table.keys.disable();
            } else {
                let rowIndex = cell.index().row;
                if (rowIndex !== currentRowInd && !document.getElementById('checkbox_EditableTable').checked) {
                    currentRowInd = rowIndex;
                }
            }
        });
    }

    function html_2_plug_into_edit_cell(dataIdentifier) {
        const options = {
            "Allgemeine Hygieneklasse": [
                " - ",
                "ÖAK - I - Ordination- und Behandlung",
                "ÖAK - II - klein Invasiv",
                "ÖAK - III - Eingriffsraum",
                "ÖAK - IV - OP",
                "MA15- LL28 - OP",
                "MA15- LL28 - Eingriffsraum",
                "MA15- LL28 - Behandlungsr. invasiv",
                "Gentechnikgesetz - S1",
                "Gentechnikgesetz - S2",
                "Gentechnikgesetz - S3",
                "Gentechnikgesetz - S4"
            ],
            "H6020": [" - ", "H1a", "H1b", "H1c", "H2a", "H2b", "H2c", "H3", "H4", "ÖNORM S 5224"],
            "Anwendungsgruppe": ["-", "0", "1", "2"],
            "Fussboden OENORM B5220": ["kA", "Klasse 1", "Klasse 2", "Klasse 3"]
        };
        if (options[dataIdentifier]) {
            const dropdownOptions = options[dataIdentifier]
                .map(option => `<option value="${option}"${cellText === option ? ' selected' : ''}>${option}</option>`)
                .join('\n');
            return `<select class="form-control form-control-sm" id="${dataIdentifier}_dropdowner">\n${dropdownOptions}\n</select>`;
        } else if (getCase(dataIdentifier) === "bit") {
            return `
                                    <select class="form-control form-control-sm" id="${dataIdentifier}_dropdowner">
                                        <option value="0"${cellText === '0' ? ' selected' : ''}>0</option>
                                        <option value="1"${cellText === '1' ? ' selected' : ''}>1</option>
                                    </select>
                                `;
        } else if (getCase(dataIdentifier) === "abd") {
            return `
                                    <select class="form-control form-control-sm" id="${dataIdentifier}_dropdowner">
                                        <option value="0"${cellText === '0' ? ' selected' : ''}> kein Anspruch </option>
                                        <option value="1"${cellText === '1' ? ' selected' : ''}> abdunkelbar </option>
                                        <option value="2"${cellText === '2' ? ' selected' : ''}> vollverdunkelbar </option>
                                    </select>
                                `;
        } else {
            return `<input class="form-control form-control-sm" id="CellInput" onclick="this.select()" type="text" value="${cellText}">`;
        }
    }


    function table_click() {
        $(document).on('click', '#table_rooms tbody tr', function () {
            let RaumID = table.row($(this)).data()['idTABELLE_Räume'];
            if ($('#checkbox_EditableTable').is(':checked')) {
                let Raumbez = table.row($(this)).data()['Raumbezeichnung'];
                let rowIndex = $(this).closest('tr').index();
                let columnIndex = $(this).find('td').index(event.target);
                if (columnIndex === -1) {
                    columnIndex = currentColInd;
                }
                let index_accounting_4_visibility = columnIndex;
                let visibleColumns = table.columns().visible();
                for (let i = 0; i <= index_accounting_4_visibility; i++) {
                    if (!visibleColumns[i]) {
                        index_accounting_4_visibility++;
                    }
                }
                let dataIdentifier = columnsDefinition[index_accounting_4_visibility]['data'];
                let cell = $(this).find('td').eq(columnIndex);
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
                            let newData = format_data_input($(this).val(), dataIdentifier); //utils.js
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
                            if (current_edit) {
                                makeToaster("Changes NOT Saved", false);
                            }
                            current_edit = false;
                            table.keys.enable();
                            table.cell(cell.index()).select();
                        }
                    });
                }
            }
            $.ajax({
                url: "setSessionVariables.php",
                data: {"roomID": RaumID},
                type: "GET",
                success: function () {
                    $.ajax({
                        url: "getRoomSpecifications2.php",
                        type: "GET",
                        success: function (data) {
                            $("#bauangaben").html(data);
                            $.ajax({
                                url: "getRoomElementsDetailed2.php",
                                type: "GET",
                                success: function (data) {
                                    let tableX = $('#myTable').DataTable();
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
    }

    function init_editable_checkbox() {
        $('<input>', {
            type: 'checkbox',
            name: 'EditableTable',
            id: 'checkbox_EditableTable',
            checked: document.getElementById('settings_save_edit_cbx').checked,
            class: 'form-check-inputz  form-check-input fix_size'
        }).appendTo($(`#TableCardHeader`));
        $('<label>', {
            id: 'edit_cbx',
            text: 'Edit Table',
            class: 'form-check-label ',
            css: {
                'display': 'none'
            }
        }).appendTo($('#TableCardHeader'));
    }


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


    /*function checkAndToggleColumnsVisibility() {
        let singleButton = document.querySelector('.toggleDatalessColumnsButton');
        if (singleButton.classList.contains('btn_vis')) {
            singleButton.classList.add('btn_invis');
            singleButton.classList.remove('btn_vis');
        } else {
            singleButton.classList.add('btn_vis');
            singleButton.classList.remove('btn_invis');
        }

        table.columns().every(function () {
            let hasNonEmptyCell = this.data().toArray().some(function (cellData) {
                return cellData !== null && cellData !== undefined && cellData !== '' && cellData !== '-' && cellData !== ' ' && cellData !== '  ' && cellData !== '   ' && cellData !== '.';
            });
            if (!hasNonEmptyCell) {
                this.visible(!this.visible());
            }
        });
    }*/

    function updateButtonClass(button, table, startColumn, endColumn) {
        const columns = table.columns().indexes();
        let vis = table.column(columns[endColumn]).visible();
        if (vis) {
            $(button).removeClass('btn_invis');
            $(button).addClass('btn_vis');
        } else {
            $(button).removeClass('btn_vis');
            $(button).addClass('btn_invis');
        }
    }

    function toggleReportColumnsVisible() {
        const columns = table.columns().indexes();
        for (let i = 5; i <= 146; i++) {
            table.column(columns[i]).visible(false);
        }
        const reportColumns = [4, 5, 11, 12, 13, 14, 15, 16, 17, 18, 19, 25, 28, 35, 36, 37, 38, 39, 40,
            46, 47, 48, 49, 50, 51, 52, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 128, 132, 133, 134, 137]
        for (let i = 0; i < reportColumns.length; i++) {
            table.column(columns[reportColumns[i]]).visible(true);
        }
    }

    function toggleColumns(table, startColumn, endColumn, button_name) {
        const columns = table.columns().indexes();
        let vis = !table.column(columns[endColumn]).visible();
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
    function save_changes(RaumID, ColumnName, newData, raumname) {
        // console.log("SaveFunction: ", raumname, ColumnName, newData);
        $.ajax({
            url: "saveRoomProperties.php",
            data: {"roomID": RaumID, "column": ColumnName, "value": newData},
            type: "GET",
            success: function (data) {
                if (data === "Erfolgreich aktualisiert!") {
                    makeToaster("SAVED</b>" + raumname + ";  " + ColumnName + ";  " + newData + " ", true);
                } else {
                    makeToaster("FAILED!!</b>" + data + "---", false);
                }
            }
        });
    }

    $("#saveNewRoom").click(function () {
        let nummer = $("#nummer").val();
        let name = $("#name").val(); // let raumbereich = $("#raumbereich").val();
        let funktionsteilstelle = $("#funktionsstelle").val();
        let MTrelevant = $("#mt-relevant").val();
        console.log(funktionsteilstelle);
        save_new_room(nummer, name, funktionsteilstelle, MTrelevant);
    });

    function save_new_room(nummer, name, funktionsteilstelle, MTrelevant) {
        if (nummer !== "" && name !== "" && MTrelevant !== "" && funktionsteilstelle !== "") {  //& flaeche  !== "" && geschoss !== "" && bauetappe  !== "" && bauteil  !== "" && funktionsteilstelle !== 0
            $.ajax({
                url: "addRoom_all.php", // "ID": raumID,
                data: {
                    "tabelle_projekte_idTABELLE_Projekte": <?php echo $_SESSION["projectID"]; ?>,
                    "Raumnr": nummer,
                    "Raumbezeichnung": name,
                    "TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen": funktionsteilstelle,
                    "MT-relevant": MTrelevant
                },
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
            let dbFieldName = field.replace(/[ ,.]/g, '+'); // Replace dots and spaces with underscores to match the field names in the PHP file
            requestData[dbFieldName] = selectedRowData[field];
        });
        delete requestData.idTABELLE_Räume;
        console.log("copySelectedRow:", requestData);
        numsave_new_room_all(requestData);
    }

</script>



