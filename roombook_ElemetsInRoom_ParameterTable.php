<?php
session_start();
include '_utils.php';
init_page_serversides();
?> 

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>

        <title>Element Parameter Tabelle</title>
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
        <meta name="viewport" content="width=device-width, initial-scale=1">

            <link rel="stylesheet" href="style.css" type="text/css" media="screen" /> 
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

                <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css" integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous">
                    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
                    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>
                    <link href="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.0.5/af-2.7.0/b-3.0.2/b-colvis-3.0.2/b-html5-3.0.2/b-print-3.0.2/cr-2.0.1/date-1.5.2/fc-5.0.0/fh-4.0.1/kt-2.12.0/r-3.0.2/rg-1.5.0/rr-1.5.0/sc-2.4.1/sb-1.7.1/sp-2.3.1/sl-2.0.1/sr-1.4.1/datatables.min.css" rel="stylesheet">

                        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
                        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
                        <script src="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.0.5/af-2.7.0/b-3.0.2/b-colvis-3.0.2/b-html5-3.0.2/b-print-3.0.2/cr-2.0.1/date-1.5.2/fc-5.0.0/fh-4.0.1/kt-2.12.0/r-3.0.2/rg-1.5.0/rr-1.5.0/sc-2.4.1/sb-1.7.1/sp-2.3.1/sl-2.0.1/sr-1.4.1/datatables.min.js"></script>

                        <!-- js xls imports -->
                        <script src="https://unpkg.com/xlsx/dist/xlsx.full.min.js"></script>
                        <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/1.3.8/FileSaver.min.js"></script>
                        <style>
                            .card{
                                padding: 1px;
                            }
                            .card-header{
                                height: 50px;
                                 overflow: hidden; 
                                background-color: rgba(100, 140, 25, 0.05);
                            }
                            .centeriiiiino{
                                justify-content: center; /* Horizontally center the content */
                                align-items: center; /* Vertically center the content */
                            }
                            .form-check-input{
                                height: 10px !important;
                                width: 10px !important;
                                padding: 15px;
                            }
                            .form-check-input:checked {
                                background-color: rgba(100, 140, 25, 0.5) !important;
                                height: 10px !important;
                                width: 10px !important;

                            }
                            .form-check-label{
                                padding-left:  3px;
                                padding-right:  15px;
                                font-weight: bold;
                            }
                            .fix_size{
                                height: 30px !important;
                            } 

                        </style>
                        </head> 
                        <body style="height:100%"> 
                            <div class="container-fluid">  
                                <div id="limet-navbar" class=' '> </div> 

                                <div class="  d-flex">
                                    <div class="mt-2 card  border-success  col-9">    
                                        <div  style="height: 60px"  class="card-header d-flex align-items-center"  id='TableCardHeader' > 
                                            <label class="form-check-label"  > <u>RÄUME</u>  </label> </div>  
                                        <div class="card-body" id = "table_container_div">
                                            <table class="table display compact table-responsive table-striped table-bordered table-sm sticky" style= "width:100%" id="table_rooms" > 
                                                <thead   </thead> <tbody>  </tbody>
                                            </table> 
                                        </div>    
                                    </div>

                                    <div class=' mt-2 card  border-secondary  col-3'>
                                        <div style="height: 60px"  class="card-header d-flex align-items-center centeriiiiino" id ="makeXLScardHeader"> 
                                            <button class="btn btn-success responsive" id="addSheet">Add Sheet</button>
                                            <button class="btn btn-link"id="download">Download Excel</button>
                                            <button class="btn btn-danger" style="margin-right: 20px;" id="reset">Reset Excel</button>   
                                        </div> 
                                        
                                        <div class="card-body">
                                            <p style="text-align-last: center;">
                                                <label class="form-check-label"  > <u> XLS Composer > LOG </u>  </label>
                                             </p>
                                            <ul  style="text-align-last: center;" id="logx"> </ul>
                                        </div>
                                    </div> 
                                </div>

                                <div class=' mt-1 card  border-secondary  col-12'>
                                    <div style="height: 50px"  class="card-header d-inline-flex  align-content-start" id ="elemetsParamsTableCardHeader">    
                                        <label class="form-check-label"  > <u>  VORSCHAU [El.Param.Table] </u>  </label>
                                    </div>
                                    <div class="card-body " id ="elemetsParamsTableCard">
                                        <p id="elemetsParamsTable">
                                    </div>
                                </div> 
                            </div>


                            <script src="roombookSpecifications_constDeclarations.js"></script>
                            <script>
                                var columnsDefinitionShort  = columnsDefinition.filter(column =>
                                    ['tabelle_projekte_idTABELLE_Projekte', "idTABELLE_Räume", 'TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen', 'MT-relevant', 'Raumbezeichnung', 'Raumnr', "Bezeichnung", 'Funktionelle Raum Nr', 'Nummer', 'Raumbereich Nutzer', 'Geschoss', 'Bauetappe', 'Bauabschnitt'].includes(column.data)
                                );
                                var table;  //for roomas table // var table2; // for elements table  //in el table code defined
                                let RaumID;
                                var wb = XLSX.utils.book_new();
                                var sheetIndex = 1;
                                var selectedIDs = [];
                                var K2R = ["1", "2", "3", "12", "17"];

                                const checkboxData = [
                                    {label: 'ELEK', value: '2'},
                                    {label: 'GEOM', value: '1'},
                                    {label: 'HKLS', value: '3'},
                                    {label: 'MGAS', value: '12'},
                                    {label: 'MSR', value: '17'}
                                ];

                                $(document).ready(function () {
                                    init_dt();
                                    table_click();
                                    add_MT_rel_filter('#TableCardHeader');
                                    move_obj_to("dt-search-0", "TableCardHeader");
                                    init_btns("#TableCardHeader");
                                    init_xls_interface();
                                    init_checboxes4selectingKathegories();
                                });


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
                                        var selectedData = getSelectedData(table);
                                        if (!selectedData || selectedData.length === 0) {
                                            console.log('No valid selection');
                                            $('#logx').append('<li>No valid Data selection</li>');
                                            return;
                                        }
                                        selectedData.forEach(function (rowData) {
                                            var RaumID = rowData.id;
                                            var Raumbezeichnung = rowData.Raumbezeichnung;
                                            console.log(RaumID);
                                            $.ajax({
                                                url: 'getRoomElementsParameterData.php',
                                                method: 'GET',
                                                data: {"roomID": RaumID, "K2Return": JSON.stringify(K2R)},
                                                success: function (data) {
                                                    if (data && data.length > 0) {
                                                        var keysToRemove = ['tabelle_Varianten_idtabelle_Varianten', 'TABELLE_Elemente_idTABELLE_Elemente'];
                                                        data.forEach(function (item) {
                                                            keysToRemove.forEach(function (key) {
                                                                delete item[key];
                                                            });
                                                        });
                                                        /*                                                       var columnsToKeep = ["ElementID", 'PN', 'NA', 'PA'];
                                                         //                                                        var filteredData = data.map(function (row) {
                                                         //                                                            return columnsToKeep.reduce(function (obj, column) {
                                                         //                                                                obj[column] = row[column];
                                                         //                                                                return obj;
                                                         //                                                            }, {});
                                                         //                                                        });
                                                         //                                                        console.log(filteredData); */

                                                        var ws = XLSX.utils.json_to_sheet(data);
                                                        var sheetName = sanitizeSheetName(Raumbezeichnung);
                                                        XLSX.utils.book_append_sheet(wb, ws, sheetName);
                                                        $('#logx').append('Added ' + sheetName + '</br>');
                                                        sheetIndex++;
                                                    } else {
                                                        $('#logx').append('' + Raumbezeichnung + '-> nodata = no sheet </br>');
                                                    }
                                                },
                                                error: function (jqXHR, textStatus, errorThrown) {
                                                    console.log("ERR function2:  ", textStatus, errorThrown);
                                                }
                                            });
                                        });
                                    });


                                    $('#download').click(function () {
                                        var wbout = XLSX.write(wb, {bookType: 'xlsx', type: 'binary'});
                                        function s2ab(s) {
                                            var buf = new ArrayBuffer(s.length);
                                            var view = new Uint8Array(buf);
                                            for (var i = 0; i < s.length; i++)
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
                                    var selectedData = table.rows({selected: true}).data();
                                    var result = [];
                                    for (var i = 0; i < selectedData.length; i++) {
                                        var rowData = selectedData[i];
                                        result.push({
                                            id: rowData['idTABELLE_Räume'],
                                            Raumbezeichnung: rowData['Raumnr'] + " " + rowData['Raumbezeichnung']
                                        });
                                    }
                                    return result;
                                }

                                function sanitizeSheetName(name) {
                                    var invalidChars = [':', '\\', '/', '?', '*', '[', ']'];
                                    var sanitized = name;
                                    invalidChars.forEach(function (char) {
                                        var regex = new RegExp('\\' + char, 'g');
                                        sanitized = sanitized.replace(regex, '');
                                    });
                                    if (sanitized.length > 31) {// Ensure the sheet name is below 31 characters
                                        sanitized = sanitized.substring(0, 31);
                                    }
                                    return sanitized;
                                }

                                function getElementsParamTable(RaumID) {
                                    $.ajax({
                                        url: "setSessionVariables.php",
                                        data: {"roomID": RaumID},
                                        type: "GET",
                                        success: function (data) {
                                            $.ajax({
                                                url: "getElementsParamTable.php",
                                                data: {"roomID": RaumID, "K2Return": JSON.stringify(K2R)},
                                                type: "GET",
                                                success: function (data) {
                                                    $("#elemetsParamsTable").html(data);
                                                }
                                            });
                                        }}
                                    );
                                }

                                function table_click() {
                                    $('#table_rooms tbody ').on('click ', 'tr', function () {
                                        RaumID = table.row($(this)).data()['idTABELLE_Räume'];
                                        getElementsParamTable(RaumID);
                                    });

                                }
                                function init_dt() {   // $('#tableRooms').DataTable({ warum das nicht geht ist mir ein räsetl
                                    table = new DataTable('#table_rooms', {
                                        ajax: {
                                            url: 'get_rb_specs_data.php',
                                            dataSrc: ''
                                        },
                                        columns: columnsDefinitionShort,
                                        dom: 'ft<"btm.d-flex justify-content-between"lip>', //'<"TableCardHeader"f>t<"btm.d-flex justify-content-between"lip>   ',
                                        keys: true,
                                        order: [[3, 'asc']],
                                        select: {
                                            style: 'multi' //,blurable: false
                                        },
                                        paging: true,
                                        pagingType: "simple_numbers",
                                        pageLength: 5,
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
                                                stateSave: false
                                            }
                                        },
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

                                function move_obj_to(IDwhichObjToMove, wheretomoveit) {
                                    var obj = document.getElementById(IDwhichObjToMove);
                                    obj.parentNode.removeChild(obj);
                                    document.getElementById(wheretomoveit).appendChild(obj);
                                    obj.classList.add("fix_size");
                                }

                                function init_btns(location) {
                                    let spacer = {extend: 'spacer', style: 'bar'};
                                    new $.fn.dataTable.Buttons(table, {
                                        buttons: [
                                            spacer,
                                            {extend: 'searchBuilder', label: "Search",
                                                className: "bg-white"},
                                            {extend: 'spacer', text: "Select:", style: 'bar'},
                                            {
                                                text: 'All',
                                                action: function () {
                                                    table.rows().select();
                                                    displaySelectedData(table);
                                                },
                                                className: "bg-white"
                                            }, {
                                                text: 'Vis',
                                                action: function () {
                                                    table.rows(':visible').select();
                                                    displaySelectedData(table);
                                                },
                                                className: "bg-white"
                                            },
                                            {
                                                text: 'None',
                                                action: function () {
                                                    table.rows().deselect();
                                                    displaySelectedData(table);
                                                },
                                                className: "bg-white"
                                            }

                                        ]}).container().appendTo($(location));
                                }

                            </script>
                        </body> 
                        </html>

