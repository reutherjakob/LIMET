<?php
session_start();
include '_utils.php';
init_page_serversides();
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

            <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css" integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous">
                <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
                <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>
                <link href="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.0.5/af-2.7.0/b-3.0.2/b-colvis-3.0.2/b-html5-3.0.2/b-print-3.0.2/cr-2.0.1/date-1.5.2/fc-5.0.0/fh-4.0.1/kt-2.12.0/r-3.0.2/rg-1.5.0/rr-1.5.0/sc-2.4.1/sb-1.7.1/sp-2.3.1/sl-2.0.1/sr-1.4.1/datatables.min.css" rel="stylesheet">

                    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
                    <script src="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.0.5/af-2.7.0/b-3.0.2/b-colvis-3.0.2/b-html5-3.0.2/b-print-3.0.2/cr-2.0.1/date-1.5.2/fc-5.0.0/fh-4.0.1/kt-2.12.0/r-3.0.2/rg-1.5.0/rr-1.5.0/sc-2.4.1/sb-1.7.1/sp-2.3.1/sl-2.0.1/sr-1.4.1/datatables.min.js"></script>
                    </head>
                    <body>
                        <div class="card-body " id ="elemetsParamsTableCard">
                            <table class='table display compact table-striped table-bordered table-sm' id='roomElementsParamTable' cellspacing='0' width='100%'>
                                <thead><tr></tr></thead><tbody><td></td></tbody></table>
                        </div>
                    </body>
                    </html>
                    <script>
                        var table2;
                        $(document).ready(function () {
                            make_table();
                        });

                        function make_table() {                           
                            $.ajax({
                                url: 'getRoomElementsParameterTableData.php',  
                                method: 'GET',
                                dataType: 'json',
                                success: function (data) {
                                    if (!data || data.length === 0) {
//                                        console.log('getElementsParamTable -> ajax: getRoomElementsParameterTableData -> No valid data returned');
                                        return;
                                    }
                                    var titleMapping = {
                                        'Varianate': 'Var',
                                        'SummevonAnzahl': '#',
                                        'jsonParam2': 'New Title 2'
                                    };
                                    var columns = Object.keys(data[0]).map(function (key) {
                                        var title = titleMapping[key] ? titleMapping[key] : key;
                                        return {title: title, data: key};
                                    });

                                    var keysToRemove = ['tabelle_Varianten_idtabelle_Varianten', 'TABELLE_Elemente_idTABELLE_Elemente'];
                                    columns = columns.filter(function (column) {
                                        return !keysToRemove.includes(column.data);
                                    });

                                    table2 = new DataTable('#roomElementsParamTable', {
                                        data: data,
                                        columns: columns,
                                        dom: 'tip',
                                        buttons: [
                                            'excel'
                                        ],
                                        scrollX: true,
                                        paging: true,
                                        pageLength: 30,

//                                        columnDefs: [
//                                            {targets: [5, 6], visible: false}
//                                        ],
                                        language: {
                                            search: "_INPUT_",
                                            searchPlaceholder: "Search..."
                                        }
                                    });
                                    // table.button('.buttons-excel').trigger();
                                },
                                error: function (jqXHR, textStatus, errorThrown) {
                                    console.log(textStatus, errorThrown);
                                }
                            });
                        }
                    </script>