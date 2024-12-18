<?php
include '_utils.php';
init_page_serversides();
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title> Geräteliste </title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css"
          integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="style.css" type="text/css" media="screen"/>
    <link href="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.0.5/af-2.7.0/b-3.0.2/b-colvis-3.0.2/b-html5-3.0.2/b-print-3.0.2/cr-2.0.1/date-1.5.2/fc-5.0.0/fh-4.0.1/kt-2.12.0/r-3.0.2/rg-1.5.0/rr-1.5.0/sc-2.4.1/sb-1.7.1/sp-2.3.1/sl-2.0.1/sr-1.4.1/datatables.min.css"
          rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.0.5/af-2.7.0/b-3.0.2/b-colvis-3.0.2/b-html5-3.0.2/b-print-3.0.2/cr-2.0.1/date-1.5.2/fc-5.0.0/fh-4.0.1/kt-2.12.0/r-3.0.2/rg-1.5.0/rr-1.5.0/sc-2.4.1/sb-1.7.1/sp-2.3.1/sl-2.0.1/sr-1.4.1/datatables.min.js"></script>

</head>

<body style="height:100%">
<div id="limet-navbar" class=' '></div>
<div class="container-fluid">
    <div class="mt-2 card responsive">
        <div class="card-header" id="CH1">Geräteliste
        </div>
        <div id="CB1" class="table-responsive">
            <?php
            $mysqli = utils_connect_sql();
            $sql = "SELECT *
            FROM tabelle_geraete";
            $result = $mysqli->query($sql);

            echo "<table class='table table-striped table-bordered table-sm' id='tableDevices' cellspacing='0' width='100%'>
                  <thead><tr>";
            $firstRow = $result->fetch_assoc();
            if ($firstRow) {
                foreach ($firstRow as $column => $value) {
                    echo "<th>" . ($column) . "</th>";
                }
                echo "</tr></thead><tbody>";
                echo "<tr>";
                foreach ($firstRow as $value) {
                    echo "<td>" . $value . "</td>";
                }
                echo "</tr>";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    foreach ($row as $value) {
                        echo "<td>" . ($value) . "</td>";
                    }
                    echo "</tr>";
                }
            }
            echo "</tbody></table>";
            ?>
        </div>
    </div>
</body>

<script src="_utils.js"></script>
<script>

    $(document).ready(function () {
        new DataTable('#tableDevices', {
            responsive: true,
            dom:'<"row"<"col-sm-12 col-md-6"f>> <"row"<"col-sm-12"tr>> <"row"<"col-md-2"i><"col-md-6"l><"col-md-4"p>>',
            columnDefs: [
                {
                    targets: [0, 4, 5, 7, 8, 9],
                    visible: false,
                    searchable: false
                }
            ],
            paging: true,
            searching: true,
            ordering: true,
            info: true,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search...",
                lengthMenu: "Show _MENU_ entries"
            },
            initComplete: function (settings, json) {
                $('#dt-search-0').appendTo('#CH1');
            }
        });
    });

</script>