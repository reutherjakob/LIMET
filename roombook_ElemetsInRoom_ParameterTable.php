<!--  18.2.25: Reworked -->
<?php
session_start();
require_once 'utils/_utils.php';
init_page_serversides();
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>Element Parameter Tabelle</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen"/>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.2.1/af-2.7.0/b-3.2.1/b-colvis-3.2.1/b-html5-3.2.1/b-print-3.2.1/cr-2.0.4/date-1.5.5/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.1/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-3.0.0/sr-1.4.1/datatables.min.js"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css"
          integrity="sha512-q3eWabyZPc1XTCmF+8/LuE1ozpg5xxn7iO89yfSOd5/oKvyqLngoNGsx8jq92Y8eXJ/IRxQbEC+FGSYxtk2oiw=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link href="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.2.1/af-2.7.0/b-3.2.1/b-colvis-3.2.1/b-html5-3.2.1/b-print-3.2.1/cr-2.0.4/date-1.5.5/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.1/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-3.0.0/sr-1.4.1/datatables.min.css"
          rel="stylesheet">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/1.3.8/FileSaver.min.js"></script>
</head>
<body>
<div class="container-fluid">
    <div id="limet-navbar" class=' '></div>

    <div class="d-flex">
        <div class="mt-2 card col-8 ">
            <div class="card-header">
                <div class="row">
                    <div class="col-xxl-6 d-flex flex-nowrap overflow-auto" id='TableCardHeader1'></div>
                    <div class="col-xxl-6 d-inline-flex flex-nowrap overflow-auto justify-content-end"
                         id='TableCardHeader'></div>
                </div>
            </div>

            <div class="card-body" id="table_container_div">
                <table class="table display compact table-responsive table-striped table-bordered table-sm sticky"
                       style="width:100%" id="table_rooms">
                    <thead></thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

        <div class="mt-2 card col-4">
            <div class="card-header d-flex align-items-center justify-content-center"><b>  &ensp; </b>
                <button class="btn btn-success responsive" id="addSheet">Add Sheet</button> &ensp;
                <button class="btn btn-link border-dark" id="download">Download Excel</button> &ensp;
                <button class="btn btn-danger" style="margin-right: 20px;" id="reset">Reset Excel</button>
            </div>
            <div class="card-body">
                <ul style="text-align-last: center;" id="logx"></ul>
            </div>
        </div>
    </div>

    <div class=' mt-1 card col-12'>
        <div style="height: 50px" class="card-header d-inline-flex  align-content-start"
             id="elemetsParamsTableCardHeader">
            <label class="form-check-label"> <u> VORSCHAU </u> </label>
        </div>
        <div class="card-body " id="elemetsParamsTableCard">
            <p id="elemetsParamsTable">
        </div>
    </div>
</div>


<script src="roombookSpecifications_constDeclarations.js"></script>
<script>
    var columnsDefinitionShort = columnsDefinition.filter(column =>
        ['tabelle_projekte_idTABELLE_Projekte', "idTABELLE_Räume", 'TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen', 'MT-relevant', 'Raumbezeichnung', 'Raumnr', "Bezeichnung", 'Funktionelle Raum Nr', 'Nummer', 'Raumbereich Nutzer', 'Geschoss', 'Bauetappe', 'Bauabschnitt'].includes(column.data)
    );
    var table, RaumID, Raumbezeichnung, sheetcounter = 1;  //for rooms: table // var table2:for elements table  //in el table code defined
    var wb = XLSX.utils.book_new();
    var sheetIndex = 1;
    var selectedIDs = [];
    var K2R = ["1", "2", "3", "12", "17"];


    const checkboxData = [
        {label: ' ELEK', value: '2'},
        {label: ' GEOM', value: '1'},
        {label: ' HKLS', value: '3'},
        {label: ' MGAS', value: '12'},
        {label: ' MSR', value: '17'}
    ];

    $(document).ready(function () {
        init_dt();
        table_click();
        add_MT_rel_filter('#TableCardHeader1');
        move_obj_to("dt-search-0", "TableCardHeader1");
        init_btns("#TableCardHeader");
        init_xls_interface();
        init_checboxes4selectingKathegories();
    });

    function getUniqueSheetname(baseName) {
        let sheetName = `${baseName} (${sheetcounter})`;
        sheetcounter++;
        return sheetName;
    }

    function sanitizeSheetName(name) {
        let invalidChars = [':', '\\', '/', '?', '*', '[', ']'];
        let sanitized = name;
        invalidChars.forEach(function (char) {
            let regex = new RegExp('\\' + char, 'g');
            sanitized = sanitized.replace(regex, '');
        });
        if (sanitized.length > 31) {// Ensure the sheet name is below 31 characters
            sanitized = sanitized.substring(0, 31);
        }
        return sanitized;
    }

    function init_checboxes4selectingKathegories() {
        checkboxData.forEach((data, index) => {
            let checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.id = 'checkbox' + index;
            checkbox.value = data.value;
            checkbox.className = 'form-check-input';
            checkbox.checked = true;
            let label = document.createElement('label');
            label.htmlFor = checkbox.id;
            label.className = 'form-check-label';
            label.appendChild(document.createTextNode(data.label));
            let div = document.querySelector('#elemetsParamsTableCardHeader');
            div.appendChild(checkbox);
            div.appendChild(label);
            checkbox.addEventListener('change', function () {
                if (this.checked) {
                    K2R.push(this.value);
                } else {
                    K2R = K2R.filter(value => value !== this.value);
                }
                getElementsParamTable(RaumID);
            });
        });
    }


    function init_xls_interface() {
        $('#addSheet').click(function () {
            let selectedData = getSelectedData(table);
            if (!selectedData || selectedData.length === 0) {
                $('#logx').append('<li>No valid Data selection</li>');
                return;
            }
            selectedData.forEach(function (rowData) {
                ////console.log("Row Data", rowData);
                RaumID = rowData.id;
                Raumbezeichnung = rowData.Raumbezeichnung;
                //console.log( Raumbezeichnung);
                $.ajax({
                    url: 'getRoomElementsParameterData.php',
                    method: 'GET',
                    data: {"roomID": RaumID, "K2Return": JSON.stringify(K2R)},
                    success: function (data) {
                        if (data && data.length > 0) {
                            let keysToRemove = ['tabelle_Varianten_idtabelle_Varianten', 'TABELLE_Elemente_idTABELLE_Elemente'];
                            data.forEach(function (item) {
                                keysToRemove.forEach(function (key) {
                                    delete item[key];
                                });
                            });
                            let ws = XLSX.utils.json_to_sheet(data);
                            let sheetName = sanitizeSheetName(rowData.Raumbezeichnung);
                            //console.log("Sheetname: ",(sheetName));
                            try {
                                XLSX.utils.book_append_sheet(wb, ws, sheetName);
                            } catch {
                                sheetName = getUniqueSheetname(sheetName)
                                ////console.log((sheetName));
                                XLSX.utils.book_append_sheet(wb, ws, sheetName);
                            }
                            $('#logx').append('Added ' + sheetName + '</br>');
                            sheetIndex++;
                        } else {
                            $('#logx').append('' + Raumbezeichnung + '-> nodata = no sheet </br>');
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        //console.log("ERR function2:  ", textStatus, errorThrown);
                    }
                });
            });
        });

        $('#download').click(function () {
            let wbout = XLSX.write(wb, {bookType: 'xlsx', type: 'binary'});
            function s2ab(s) {
                let buf = new ArrayBuffer(s.length);
                let view = new Uint8Array(buf);
                for (let i = 0; i < s.length; i++)
                    view[i] = s.charCodeAt(i) & 0xFF;
                return buf;
            }
            saveAs(new Blob([s2ab(wbout)], {type: "application/octet-stream"}), 'Elemetparameter' + " <?php echo $_SESSION["projectName"]; ?> " + '.xlsx');
        });

        $('#reset').click(function () {
            wb = XLSX.utils.book_new();
            sheetIndex = 1;
            $('#logx').empty();
        });
    }

    function getRowDataByRoomID(roomID) {
        var allData = table.rows().data();
        var rowData;
        for (var i = 0; i < allData.length; i++) {
            if (allData[i]['idTABELLE_Räume'] === roomID) {
                rowData = allData[i];
                break;
            }
        }
        return rowData;
    }

    function getSelectedData(table) {
        let selectedData = table.rows({selected: true}).data();
        //console.log(selectedData);
        //console.log(selectedData[1]);
        //console.log(selectedData[1].Raumbezeichnung);
        let result = [];
        for (let i = 0; i < selectedData.length; i++) {
            let rowData = selectedData[i];
            result.push({
                id: rowData.idTABELLE_Räume,
                Raumbezeichnung: rowData.Raumnr + " " + rowData.Raumbezeichnung
            });
        }
        //console.log(result);
        return result;
    }

    function getElementsParamTable(RaumID) {
        $.ajax({
                url: "setSessionVariables.php",
                data: {"roomID": RaumID},
                type: "GET",
                success: function () {
                    $.ajax({
                        url: "getElementsParamTable.php",
                        data: {"roomID": RaumID, "K2Return": JSON.stringify(K2R)},
                        type: "GET",
                        success: function (data) {
                            $("#elemetsParamsTable").html(data);
                        }
                    });
                }
            }
        );
    }

    function table_click() {
        $('#table_rooms tbody ').on('click ', 'tr', function () {
            RaumID = table.row($(this)).data()['idTABELLE_Räume'];
            getElementsParamTable(RaumID);
        });
    }

    function init_dt() {
        table = new DataTable('#table_rooms', {
            ajax: {
                url: 'get_rb_specs_data.php',
                dataSrc: ''
            },
            columns: columnsDefinitionShort,
            dom: 'ft<"btm.d-flex justify-content-between"lip>',
            keys: true,
            order: [[3, 'asc']],
            select: {
                style: 'multi'
            },
            paging: true,
            pagingType: "simple_numbers",
            pageLength: 15,
            lengthMenu: [
                [5, 15, 30, -1],
                [5, '15 rows', '30 rows', 'Show all']
            ],
            scrollY: true,
            scrollX: true,
            scrollCollapse: true,
            language: {
                "search": "",
                searchBuilder: {
                    title: null,
                    depthLimit: 2,
                    stateSave: false,
                    class: "btn btn-sm"
                }
            },
            compact: true
        });
    }

    function add_MT_rel_filter(location) {
        var dropdownHtml = '<select class="form-control-sm fix_size" id="columnFilter">' + '<option value="">MT</option><option value="Ja">Ja</option>' + '<option value="Nein">Nein</option></select>';
        $(location).append(dropdownHtml);
        $('#columnFilter').change(function () {
            let filterValue = $(this).val();
            table.column('MT-relevant:name').search(filterValue).draw();
        });
    }

    function move_obj_to(IDwhichObjToMove, wheretomoveit) {
        let obj = document.getElementById(IDwhichObjToMove);
        obj.parentNode.removeChild(obj);
        document.getElementById(wheretomoveit).appendChild(obj);
        obj.classList.add("fix_size");
    }

    function init_btns(location) {
        let spacer = {extend: 'spacer', style: 'bar'};
        new $.fn.dataTable.Buttons(table, {
            buttons: [
                {
                    extend: 'searchBuilder', label: "Search",
                    className: "btn"
                },
                {extend: 'spacer', text: "Select", style: 'bar'},
                {
                    text: 'All',
                    action: function () {
                        table.rows().select();
                        //displaySelectedData(table);
                    },
                    className: "btn"
                }, {
                    text: 'Vis',
                    action: function () {
                        table.rows(':visible').select();
                        //displaySelectedData(table);
                    },
                    className: "btn btn-dark"
                },
                {
                    text: 'None',
                    action: function () {
                        table.rows().deselect();
                        //displaySelectedData(table);
                    },
                    className: "btn"
                }

            ]
        }).container().appendTo($(location));
    }

</script>
</body>
</html>

