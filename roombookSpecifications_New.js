let projectID;
fetch('get_project_id.php')
    .then(response => response.json())
    .then(data => {
        projectID = data.projectID;
        // Any code that needs to use projectID should be called here or in a function called from here
    })
    .catch(error => console.error('Error:', error));

var table;
let cellText = "";
let currentRowInd = 0;
let currentColInd = 0;
let current_edit = false; //keeps track if the input field to edit the cells is open
let Cookie_aktiv_tage = 90;
let previous_room_session = 0;
var dt_search_counter = 1;

$(document).ready(function () {
    loadSettings();
    init_dt();
    init_editable_checkbox();

    move_item("dt-search-0", "TableCardHeader");
    init_showRoomElements_btn();
    init_btn_4_dt();
    init_visibilities();
    table_click();

    init_filter();
    handleCheckboxChange();

    add_room_modal();
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
        {
            text: '',
            className: 'btn border-secondary btn-light fas fa-check',
            titleAttr: "Select All",
            action: () => table.rows().select()
        },
        {
            text: '',
            className: 'btn btn-light border-secondary fas fa-eye',
            titleAttr: "Select Visible",
            action: () => table.rows(':visible').select()
        },
        {
            text: '',
            className: 'btn btn-light border-secondary fas fa-times',
            titleAttr: "Deselect All Rows",
            action: () => table.rows().deselect()
        }
    ];
    const btn_grp_new_out = [
        {
            text: '',
            className: 'btn btn-light border-secondary fas fa-plus-square',
            titleAttr: "Add Room",
            action: () => $('#addRoomModal').modal('show')
        },
        {
            text: '',
            className: "btn btn-light border-secondary fas fa-window-restore",
            titleAttr: "Copy Selected Row",
            action: copySelectedRow
        },
        {
            extend: 'excelHtml5',
            exportOptions: {columns: ':visible'},
            className: 'btn btn-light border-secondary fa fa-download',
            text: "",
            titleAttr: "Download as Excel"
        }
    ];
    const btn_grp_settings = [
        {
            text: "",
            className: "btn btn-light border-secondary fas fa-vote-yea",
            titleAttr: "Bauangaben Check",
            action: check_angaben
        },
        {
            text: "",
            className: "btn btn-light  border-secondary fas fa-info-circle",
            titleAttr: "Help",
            action: () => $('#HelpModal').modal('show')
        },
        {
            text: "",
            className: 'btn btn-light border-secondary fas fa-cogs',
            titleAttr: "Open Settings",
            action: () => $('#einstellungModal').modal('show')
        }
    ];
    const buttonsGroupcolumnVisbilities = [
        {
            extend: 'colvis',
            text: 'Vis',
            columns: ':gt(5)',
            className: 'btn btn-light border-secondary',
            collectionLayout: 'fixed six-column',
            fade: 0,
            align: 'center'
            //,prefixButtons: [{
            //    extend: 'colvisRestore',
            //    text: 'Standard Wieder'
            // }]
        },
        ...buttonRanges.map(button => ({
            text: button.name, className: 'btn btnx btn_vis',
            action: (e, dt, node) => {
                toggleColumns(dt, button.start, button.end, button.name);
                updateButtonClass(node, dt, button.start, button.end);
            }
        })), {
            text: '<i class="fa fa-paper-plane"></i> R',
            className: 'btn btn-light border-secondary',
            action: toggleReportColumnsVisible
        }
    ];
    const savestate = document.getElementById('settings_save_state').checked || document.getElementById('settings_save_state_4all_projects').checked;
    const searchbuilder = [
        {
            extend: 'searchBuilder',
            text: "",
            className: "btn btn-light  fa fa-search",
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

    $('#settings_show_btn_grp_labels').change(function () {
        change_top_label_visibility($(this).is(':checked'));
    });

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


/*function event_table_keyz() {
table.on('key-focus', function (e, datatable, cell) {
    let rowIndex = cell.index().row;
    if (rowIndex !== currentRowInd && !document.getElementById('checkbox_EditableTable').checked) {
        currentRowInd = rowIndex;
    }
});
} */


/// EDIT TABLE
function getCase(dataIdentifier) {
    const column = columnsDefinition.find(column => column.data === dataIdentifier);
    if (column && column.case) {
        return column.case;
    } else {
        return 'no case';
    }
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
                    current_edit = true;
                }

                cell.find('input, select').focus();
                table.keys.disable();
                cell.find('input, select').on('keydown blur', function (event) {
                    if (event.keyCode === 13 && current_edit) {
                        let newData = format_data_input($(this).val(), dataIdentifier); //utils.js
                        if (newData.trim() !== "") {
                            cellText = newData;
                            cell.html(newData);
                            table.keys.enable();
                            table.cell(cell.index()).select();
                            save_changes(RaumID, dataIdentifier, newData, Raumbez);
                            current_edit = false;
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
                        if (previous_room_session !== RaumID) {
                            previous_room_session = RaumID;
                            $.ajax({
                                url: "getRoomElementsDetailed1.php",
                                type: "GET",
                                success: function (data) {
                                    $('#elementParameters').empty();
                                    if (!data || data.trim() === "") {
                                        $("#roomElements").empty();
                                    } else {
                                        $("#roomElements").html(data);
                                    }
                                    let $cardHeader1 = $("#CardHEaderElemntsInRoom1");
                                    let $cardHeader2 = $("#CardHEaderElemntsInRoom2");
                                    $cardHeader2.find('#room-action-buttons, #TableCardHeader').remove();
                                    $cardHeader1.find('[id^="dt-search-"]').remove();

                                    setTimeout(function () {
                                        //$cardHeader.append("&ensp;");
                                        move_item("dt-search-" + dt_search_counter, "CardHEaderElemntsInRoom1");
                                        // console.log(dt_search_counter, " -> ", dt_search_counter+1);
                                        dt_search_counter++;

                                        // attachButtonListeners();
                                        $cardHeader2.append($('#room-action-buttons'));
                                    }, 100)

                                },
                                error: function (jqXHR, textStatus, errorThrown) {
                                    console.error("AJAX call failed: " + textStatus + ", " + errorThrown);
                                }
                            });
                        }
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
        class: 'form-check-input dt-input'
    }).css({
        width: '20px',
        height: '30px'
    }).appendTo($(`#TableCardHeader`));

    $('<label>', {
        id: 'edit_cbx',
        text: 'Edit Table',
        class: 'form-check-label',
        css: {
            'display': 'none'
        }
    }).appendTo($('#TableCardHeader'));
}

//VISIBILITY
function init_visibilities() {
    const columns = table.columns().indexes();
    buttonRanges.forEach(button => {
        const isVisible = table.column(columns[button.start]).visible();
        const buttonElement = $(`.btn_vis:contains('${button.name}')`);
        if (!isVisible) {
            buttonElement.addClass('btn_invis');
        }
    });
}


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
        46, 47, 48, 49, 50, 51, 52, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 129, 133, 134, 135, 138]
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
        } else {
            $(this).html("<i class='fas fa-caret-left'></i>");
            $("#additionalInfo").hide();
        }
    });
}


//SAVEING/CPYNG Rooms
function save_changes(RaumID, ColumnName, newData, raumname) {
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
    table.ajax.reload(null, false);
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
    $.ajax({
        url: "addRoom_all.php",
        data: requestData,
        type: "GET",
        success: function (data) {
            alert(data);
            window.location.replace("roombookSpecifications_New.php");
        }
    });
}
