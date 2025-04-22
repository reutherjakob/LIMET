<?php
if (!function_exists('utils_connect_sql')) {  include "_utils.php"; }
include "_format.php";
init_page_serversides();
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>RB-Bestand</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style.css" type="text/css" media="screen"/>
    <link rel="icon" href="iphone_favicon.png">

    <!-- Rework 2025 CDNs -->
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

</head>

<body style="height:100%">
<div class="container-fluid bg-light" >
    <div id="limet-navbar"></div>
    <div class="mt-4 card">
        <?php
        $mysqli = utils_connect_sql();
        $sql = "SELECT 
            tabelle_elemente.ElementID, 
            tabelle_elemente.Bezeichnung, 
            tabelle_räume_has_tabelle_elemente.id, 
            tabelle_räume_has_tabelle_elemente.Kurzbeschreibung, 
            tabelle_bestandsdaten.Inventarnummer, 
            tabelle_bestandsdaten.Seriennummer, 
            tabelle_bestandsdaten.Anschaffungsjahr, 
            tabelle_bestandsdaten.`Aktueller Ort`, 
            tabelle_geraete.Typ, 
            tabelle_hersteller.Hersteller, 
            tabelle_räume.Raumnr, 
            tabelle_räume.Raumbezeichnung, 
            tabelle_räume.`Raumbereich Nutzer`,
            costs.Kosten
        FROM tabelle_hersteller 
        RIGHT JOIN (tabelle_geraete 
        RIGHT JOIN (tabelle_bestandsdaten 
        INNER JOIN (tabelle_elemente 
        INNER JOIN (tabelle_räume 
        INNER JOIN tabelle_räume_has_tabelle_elemente 
        ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) 
        ON tabelle_elemente.idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) 
        ON tabelle_bestandsdaten.tabelle_räume_has_tabelle_elemente_id = tabelle_räume_has_tabelle_elemente.id) 
        ON tabelle_geraete.idTABELLE_Geraete = tabelle_bestandsdaten.tabelle_geraete_idTABELLE_Geraete) 
        ON tabelle_hersteller.idtabelle_hersteller = tabelle_geraete.tabelle_hersteller_idtabelle_hersteller
        LEFT JOIN (
            SELECT 
                tabelle_projekt_varianten_kosten.Kosten,
                tabelle_räume_has_tabelle_elemente.id AS element_id
            FROM tabelle_projekt_varianten_kosten
            INNER JOIN tabelle_räume_has_tabelle_elemente
            ON tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten
            AND tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente
            WHERE tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte =  " . $_SESSION["projectID"] . "
        ) AS costs
        ON tabelle_räume_has_tabelle_elemente.id = costs.element_id
        WHERE tabelle_räume.tabelle_projekte_idTABELLE_Projekte = " . $_SESSION["projectID"] . "
        AND tabelle_räume_has_tabelle_elemente.`Neu/Bestand` = 0 
        AND tabelle_räume_has_tabelle_elemente.Standort = 1
        ORDER BY tabelle_räume.`Raumbereich Nutzer`, tabelle_räume.Raumnr;";

        $result = $mysqli->query($sql);

        echo "  <div class='card-header'>
                <div class='row'> 
                    <div class='col-xxl-6'> <b>Elemente im Bestand</b> </div>
                    <div class='col-xxl-6 d-flex flex-nowrap  justify-content-end' id='CardHeader'> 
                ";
        if ($result->num_rows > 0) {
            echo "<button type='button' class='ml-4 btn btn-outline-dark btn-sm' value='createBestandsPDF'><i class='far fa-file-pdf'></i> Bestands-PDF</button>";
            echo "<button  class='ml-4 btn btn-outline-dark btn-sm' onclick=\"window.location.href='out_bestands_csv.php'\">Download CSV</button>";
        }
        echo "</div> </div> </div> <div class='card-body'>";
        echo "<table class='table table-striped table-bordered table-sm table-hover border border-light border-5'' id='tableBestandsElemente'>
            <thead><tr>
            <th>ID</th>
            <th>ElementID</th>
            <th>Element</th>
            <th>Inventarnr</th>
            <th>Seriennr</th>
            <th>Anschaffungsjahr</th>
            <th>Gerät</th>
            <th>Raumnr</th>
            <th>Raum</th>
            <th>Standort aktuell</th>
            <th>Kosten</th>
            <th>Kosten</th><!-- unformatiert -->
            <th>Kommentar</th>                                                    
            </tr></thead>
            <tbody>";

        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row["id"] . "</td>";
            echo "<td>" . $row["ElementID"] . "</td>";
            echo "<td>" . $row["Bezeichnung"] . "</td>";
            echo "<td>" . $row["Inventarnummer"] . "</td>";
            echo "<td>" . $row["Seriennummer"] . "</td>";
            echo "<td>" . $row["Anschaffungsjahr"] . "</td>";
            echo "<td>" . $row["Hersteller"] . "-" . $row["Typ"] . "</td>";
            echo "<td>" . $row["Raumnr"] . "</td>";
            echo "<td>" . $row["Raumbezeichnung"] . "</td>";
            echo "<td>" . $row["Aktueller Ort"] . "</td>";
            echo "<td>" . format_money($row["Kosten"]) . "</td>";
            echo "<td>" . (float)$row["Kosten"] . "</td>";
            if (null != ($row["Kurzbeschreibung"])) {
                echo "<td><button type='button' class='btn btn-sm btn-outline-dark' 
    data-bs-toggle='popover' 
    data-bs-placement='top' 
    data-bs-content='" . htmlspecialchars($row["Kurzbeschreibung"]) . "' 
    title='Kommentar'>
    <i class='fa fa-comment'></i></button></td>";
            } else {
                echo "<td> </td>";
            }
            echo "</tr>";
        }
        echo "</tbody></table>";
        ?>
    </div>
</div>
</body>
<script>
    var table1;
    $(document).ready(function () {
        new DataTable('#tableBestandsElemente', {
            columns: [
                {visible: false, searchable: false}, // Column 0
                null, // Column 1
                null, // Column 2
                null, // Column 3
                null, // Column 4
                null, // Column 5
                null, // Column 6
                null, // Column 7
                null, // Column 8
                null, // Column 9
                null, // Column 10
                null, // Column 
                null
            ],
            paging: true,
            pagingType: 'simple',
            lengthChange: true,
            pageLength: 25,
            searching: true,
            info: true,
            order: [[1, 'asc']],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json',
                decimal: ',',
                thousands: '.',
                search: "",
                searchPlaceholder: ""
            },
            buttons: [
                {
                    extend: 'excel',
                    exportOptions: {
                        columns: function (idx) {
                            return idx !== 0 && idx !== 10;
                        }
                    }
                }
            ],
            mark: true,
            layout: {
                topStart: "buttons",
                topEnd: "search",
                bottomStart: "info",
                bottomEnd: ["pageLength", "paging"]
            },
            rowCallback: function (row, data) {
            }, initComplete: function () {
                $('.dt-buttons').children().addClass("btn-sm").appendTo('#CardHeader');
                $('.dt-search label').remove();
                $('.dt-search').children().removeClass("form-control form-control-sm").addClass("btn btn-sm btn-outline-dark").appendTo('#CardHeader');

            }
        });

        let popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
        let popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl, {
                trigger: 'click',
                html: true
            })
        })

        $(document).on('click', function (e) {
            if (!$(e.target).closest('[data-bs-toggle="popover"]').length &&
                !$(e.target).closest('.popover').length) {
                $('[data-bs-toggle="popover"]').popover('hide');
            }
        });

        $("button[value='createBestandsPDF']").click(function () {
            window.open('/pdf_createBestandPDF.php');//there are many ways to do this
        });
    });


</script>


</html>
