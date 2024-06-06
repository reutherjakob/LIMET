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
<!--                        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>  -->

                        <style>
                        </style>

                        </head> 
                        <body style="height:100%"> 
                            <div class="container-fluid ">
                                <div id="limet-navbar" class=' '> </div> 

                                <div class="mt-4 card">    
                                    <div class="card-header d-inline-flex" id='TableCardHeader'>  </div>

                                    <div class="card-body" id = "table_container_div">
                                        <table class="table display compact table-responsive table-striped table-bordered table-sm sticky" width ="100%" id="table_rooms" > 
                                            <thead   </thead> <tbody>  </tbody>
                                        </table> 
                                    </div>
                                </div>      
                                <div class='mt-4 card  bd-highlight'>
                                    <div class="card-header d-inline-flex" id ="makeXLScardHeader" > XLS COMPOSER 

                                        <button class="btn-sm btn-success" id="addSheet">Add Sheet</button>
                                        <button class="btn-sm btn-link"id="download">Download Excel</button>
                                        <button class="btn-sm btn-danger" id="reset">Reset Excel</button></div>

                                    <div class="card-body">
                                        <div class="row"> 
                                            <div class="col">
                                                <p id="makeXLSparagraph">Selected Data:</p> 
                                            </div>

                                            <div class="col">
                                                <p>Log:</p>
                                                <ul id="logx"> 
                                                </ul>
                                            </div> 

                                        </div>
                                    </div>


                                </div> 
                                <div class='mt-4 card  bd-highlight'>
                                    <div class="card-header d-inline-flex" id ="elemetsParamsTableCardHeader" >  ELEMENT PARAMETER </div>
                                    <div class="card-body " id ="elemetsParamsTableCard">
                                        <p id="elemetsParamsTable">
                            <!--                                        <table class='table display compact table-striped table-bordered table-sm' id='roomElementsParamTable' cellspacing='0' width='100%'>
                                            <thead   </thead> <tbody>  </tbody>
                                        </table> -->
                                    </div>
                                </div> 
                            </div>

                            <script src="roombookSpecifications_constDeclarations.js"></script> 
                            <script>
                                var table;  //for roomas table // var table2; // for elements table  //in el table code defined
                                var wb = XLSX.utils.book_new();
                                var sheetIndex = 1;
                                var selectedIDs = [];



                                $(document).ready(function () {
                                    init_dt();
                                    table_click();
                                    add_MT_rel_filter('#TableCardHeader');
                                    move_obj_to("dt-search-0", "TableCardHeader");
                                    init_btns("#TableCardHeader");
                                    init_xls_interface();
                                });

                                function init_xls_interface() {
                                    $('#addSheet').click(function () {
                                        var selectedData = getSelectedData(table); // Get all selected data
                                        if (!selectedData || selectedData.length === 0) {
                                            console.log('No valid selection');
                                            $('#logx').append('<li>No valid selection</li>');
                                            return;

                                        }
                                        selectedData.forEach(function (rowData) { // Iterate over each selected row
                                            var RaumID = rowData.id;
                                            var Raumbezeichnung = rowData.Raumbezeichnung;
                                            console.log(RaumID);
//                                            for (var i = 0; i < selectedData.length; i++) {
//                                        var rowData = selectedData[i];
//                                        result.push({
//                                            id: rowData['idTABELLE_Räume'],
//                                            Raumbezeichnung: rowData['Raumnr'] + " " + rowData['Raumbezeichnung']
//                                        });
//                                            
//                                            $.ajax({
//                                                url: "setSessionVariables.php",
//                                                data: {"roomID": RaumID},
//                                                type: "GET",
//                                                success: function (data) {
                                            $.ajax({
                                                url: 'getRoomElementsParameterData.php',
                                                method: 'GET',
                                                data: {"roomID": RaumID},
//                                                        dataType: 'json',
                                                success: function (data) {
                                                    var ws = XLSX.utils.json_to_sheet(data);
                                                    var sheetName = sanitizeSheetName(Raumbezeichnung); // Use the Raumbezeichnung for the sheet name
                                                    XLSX.utils.book_append_sheet(wb, ws, sheetName);
                                                    $('#logx').append('<li>Added ' + sheetName + '</li>');
                                                    sheetIndex++;
                                                },
                                                error: function (jqXHR, textStatus, errorThrown) {
                                                    console.log("ERR function2:  ", textStatus, errorThrown);
                                                }
                                            });
//                                                }, error: function (jqXHR, textStatus, errorThrown) {
//                                                    console.log("ERR function1:  ", textStatus, errorThrown);
//                                                }
//                                            });
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
                                        saveAs(new Blob([s2ab(wbout)], {type: "application/octet-stream"}), 'data.xlsx');
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


                                function displaySelectedData(table) {
                                    var selectedData = getSelectedData(table);
                                    var ul = document.createElement('ul');
                                    for (var i = 0; i < selectedData.length; i++) {
                                        var li = document.createElement('li');
                                        li.textContent = 'ID: ' + selectedData[i].id + ', Room: ' + selectedData[i].Raumbezeichnung;
                                        ul.appendChild(li);
                                    }
                                    var paragraph = document.getElementById('makeXLSparagraph');
                                    paragraph.innerHTML = ''; // Clear the paragraph
                                    paragraph.appendChild(ul); // Append the list to the paragraph
                                }

                                function table_click() {
                                    $('#table_rooms tbody').on('click', 'tr', function () {
//                                        var data = getSelectedData(table);
//                                        console.log(data);
                                        displaySelectedData(table);
                                        var RaumID = table.row($(this)).data()['idTABELLE_Räume'];

                                        $.ajax({
                                            url: "setSessionVariables.php",
                                            data: {"roomID": RaumID},
                                            type: "GET",
                                            success: function (data) {
                                                $.ajax({
                                                    url: "getElementsParamTable.php",
                                                    data: {"roomID": RaumID},
                                                    type: "GET",
                                                    success: function (data) {
                                                        $("#elemetsParamsTable").html(data);
                                                    }
                                                });
                                                /*
                                                 //                                                $.ajax({    //somehow broken
                                                 //                                                    url: "exportXLS.php",
                                                 //                                                    type: "GET",
                                                 //                                                    success: function (data) {
                                                 //                                                        $("#makeXLSparagraph ").html(data);
                                                 //                                                    }
                                                 //                                                }); */
                                            }
                                        });
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
                                            style: 'os' //,blurable: false
                                        },
                                        paging: true,
                                        pagingType: "simple_numbers",
                                        pageLength: 20,
                                        lengthMenu: [
                                            [10, 20, -1],
                                            ['10 rows', '20 rows', 'Show all']
                                        ],
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
                                            {extend: 'searchBuilder', label: "Search"},
                                            {extend: 'spacer', text: "SELECT:", style: 'bar'},
                                            {
                                                text: 'All',
                                                action: function () {
                                                    table.rows().select();
                                                    displaySelectedData(table);
                                                }
                                            }, {
                                                text: 'Visible',
                                                action: function () {
                                                    table.rows(':visible').select();
                                                    displaySelectedData(table);
                                                }
                                            },
                                            {
                                                text: 'None',
                                                action: function () {
                                                    table.rows().deselect();
                                                    displaySelectedData(table);
                                                }
                                            }]}).container().appendTo($(location));
                                }

                            </script>
                        </body> 
                        </html>

