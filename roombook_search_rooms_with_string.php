<?php
session_start();
include '_utils.php';
init_page_serversides();
?> 

<html data-bs-theme="dark" xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>RB.RaumSuche</title>
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
        <meta name="viewport" content="width=device-width, initial-scale=1"/>
        <link rel="stylesheet" href="style.css" type="text/css" media="screen" />
        <link rel="icon" href="iphone_favicon.png"/> 
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous"/>
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css" integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous"/>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script> 
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
        <link href="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.0.5/af-2.7.0/b-3.0.2/b-colvis-3.0.2/b-html5-3.0.2/b-print-3.0.2/cr-2.0.1/date-1.5.2/fc-5.0.0/fh-4.0.1/kt-2.12.0/r-3.0.2/rg-1.5.0/rr-1.5.0/sc-2.4.1/sb-1.7.1/sp-2.3.1/sl-2.0.1/sr-1.4.1/datatables.min.css" rel="stylesheet"/>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.0.5/af-2.7.0/b-3.0.2/b-colvis-3.0.2/b-html5-3.0.2/b-print-3.0.2/cr-2.0.1/date-1.5.2/fc-5.0.0/fh-4.0.1/kt-2.12.0/r-3.0.2/rg-1.5.0/rr-1.5.0/sc-2.4.1/sb-1.7.1/sp-2.3.1/sl-2.0.1/sr-1.4.1/datatables.min.js"></script>
        <script src="roombookSpecifications_constDeclarations.js"></script>
        <style>
            /* Add any custom styles here */
        </style>
    </head>   
    <body style="height:100%">
        <div id="limet-navbar" class='bla'></div>  
        <div id='ContainerRaumsuche' class='container-fluid'> 
            <div class="card">    
                <div class="card-header d-flex align-items-center" id='searchDbCardHeader0'>Raumsuche</div>
                <div class="card-header d-flex align-items-center" id='searchDbCardHeader'>
                    <label for="fieldSelect">Select Field:</label>
                    <select id="fieldSelect" class="form-select"> 
                    </select>

                    <label for="searchInput"> Search String:</label>
                    <input type="text" id="searchInput" class="form-control">

                        <button id="searchButton" class="btn btn-primary mt-1">Search</button>
                </div>
                <div class="card-body" id="cardx">
                    <table id="table_rooms" class="display" style="width:100%">
                        <thead>
                            <tr>
                                <!-- Table headers will be dynamically generated -->
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Table data will be dynamically generated -->
                        </tbody>
                    </table>
                </div>
            </div>      
        </div>

        <script>
            const fields = [
                {value: 'Raumbezeichnung', text: 'Raumbezeichnung'}
            ];

            const fieldSelect = document.getElementById('fieldSelect');
            fields.forEach(field => {
                const option = document.createElement('option');
                option.value = field.value;
                option.text = field.text;
                fieldSelect.appendChild(option);
            });

            function init_dt() {
                table = new DataTable('#table_rooms', {
                    ajax: {
                        url: 'get_rooms_via_namesearch.php',
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
                            stateSave: false,
                        }
                    },
                    keys: true,
                    order: [[3, 'asc']],
                    stateSave: false,
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

            document.getElementById('searchButton').addEventListener('click', function () {
                const selectedField = document.getElementById('fieldSelect').value;
                const searchString = document.getElementById('searchInput').value;

                if ($.fn.DataTable.isDataTable('#table_rooms')) {
                    $('#table_rooms').DataTable().destroy();
                    $('#table_rooms tbody').empty();
                }

                $.ajax({
                    url: 'get_rooms_via_namesearch.php',
                    type: 'GET',
                    data: {
                        field: selectedField,
                        search: searchString
                    },
                    success: function (response) {
                        $('#table_rooms').DataTable({
                            data: response,
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
                                    stateSave: false,
                                }
                            },
                            keys: true,
                            order: [[3, 'asc']],
                            stateSave: false,
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
                    },
                    error: function (xhr, status, error) {
                        console.error('Error: ' + error);
                    }
                });
            });
        </script> 
    </body>

