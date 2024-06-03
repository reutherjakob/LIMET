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
                                    <div class="card-header card-header_size">  ELEMENT PARAMETERZ </div>
                                    <div class="card-body " id ="elemetsParamsTableCard">
                                        <p id="roomElements">
                                            <p id="elementParameters">
                                                </div>
                                                </div> 
                                                </div>
                                                <script src="roombookSpecifications_constDeclarations.js"></script> 
                                                <script>
                                                    var table;
                                                    let current_edit = false; //variable keeps track if the input field to ediot the cells is open

                                                    $(document).ready(function () {
                                                        init_dt();
                                                        add_MT_rel_filter('#TableCardHeader');
                                                        move_dt_search();
                                                        table_click();
                                                    });

                                                    function table_click() {
                                                        $('#table_rooms tbody').on('click', 'tr', function () {
                                                            var RaumID = table.row($(this)).data()['idTABELLE_Räume'];
                                                            console.log("RaumID", RaumID);
                                                            $.ajax({
                                                                url: "setSessionVariables.php",
                                                                data: {"roomID": RaumID},
                                                                type: "GET",
                                                                success: function (data) {
                                                                    $("#RoomID").text(RaumID);
                                                                    $.ajax({
                                                                        url: "getRoomElementsParameterTableData.php",
                                                                        type: "GET",
                                                                        success: function (data) {
                                                                            $("#elemetsParamsTableCard").html(data);
                                                                        }
                                                                    });
                                                                }
                                                            });
                                                        });
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
                                                        $(location).append(dropdownHtml);
                                                        $('#columnFilter').change(function () {
                                                            var filterValue = $(this).val();
                                                            table.column('MT-relevant:name').search(filterValue).draw();
                                                        });
                                                    }
                                                    function move_dt_search() {
                                                        var dt_searcher = document.getElementById("dt-search-0");
                                                        dt_searcher.parentNode.removeChild(dt_searcher);
                                                        document.getElementById("TableCardHeader").appendChild(dt_searcher);
                                                        dt_searcher.classList.add("fix_size");
                                                    }

                                                </script>
                                                </body> 
                                                </html>

