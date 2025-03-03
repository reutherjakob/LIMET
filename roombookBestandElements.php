<?php
include '_utils.php';
include "_format.php";
init_page_serversides();
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>RB-Bestand</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style.css" type="text/css" media="screen"/>
    <link rel="icon" href="iphone_favicon.png">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css"
          integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>

    <link rel="stylesheet" type="text/css"
          href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/b-1.5.2/b-html5-1.5.2/sl-1.2.6/datatables.min.css"/>
    <script type="text/javascript"
            src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/b-1.5.2/b-html5-1.5.2/sl-1.2.6/datatables.min.js"></script>
    <link rel="stylesheet" type="text/css"
          href="https://cdn.jsdelivr.net/datatables.mark.js/2.0.0/datatables.mark.min.css"/>
    <script type="text/javascript"
            src="https://cdn.datatables.net/plug-ins/1.10.13/features/mark.js/datatables.mark.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/mark.js/8.6.0/jquery.mark.min.js"></script>
    <style>

        .popover-content {
            height: 200px;
            width: 200px;
        }

        textarea.popover-textarea {
            border: 1px;
            margin: 0px;
            width: 100%;
            height: 200px;
            padding: 0px;
            box-shadow: none;
        }

        .popover-footer {
            margin: 0;
            padding: 8px 14px;
            font-size: 14px;
            font-weight: 400;
            line-height: 18px;
            background-color: #F7F7F7;
            border-bottom: 1px solid #EBEBEB;
            border-radius: 5px 5px 0 0;
        }

        .input-xs {
            height: 22px;
            padding: 2px 5px;
            font-size: 12px;
            line-height: 1.5; /* If Placeholder of the input is moved up, rem/modify this. */
            border-radius: 3px;
        }
        .bottom {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .top {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

    </style>
</head>

<body style="height:100%">
<div id="limet-navbar"></div>
<div class="container-fluid">
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

        echo "  <div class='card-header'>Elemente im Bestand";
        if ($result->num_rows > 0) {
            echo "<button type='button' class='ml-4 btn btn-outline-dark btn-sm' value='createBestandsPDF'><i class='far fa-file-pdf'></i> Bestands-PDF</button>";
            echo "<button  class='ml-4 btn btn-outline-dark btn-sm' onclick=\"window.location.href='out_bestands_csv.php'\">Download CSV</button>";
        }
        echo "</div> <div class='card-body'>";
        echo "<table class='table table-striped table-bordered table-sm' id='tableBestandsElemente'  cellspacing='0' width='100%'>
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
                echo "<td><button type='button' class='btn btn-sm btn-outline-dark' id='buttonComment" . $row["id"] . "' name='showComment' value='" . $row["Kurzbeschreibung"] . "' title='Kommentar'><i class='fa fa-comment'></i></button></td>";
            } else {
                echo "<td><button type='button' class='btn btn-sm btn-outline-dark' id='buttonComment" . $row["id"] . "' name='showComment' value='" . $row["Kurzbeschreibung"] . "' title='Kommentar'><i class='fa fa-comment-slash'></i></button></td>";
            }


            echo "</tr>";
        }
        echo "</tbody></table>";
        ?>
    </div>
</div>
</body>
<script>

    // Tabelle formatieren
    $(document).ready(function () {
        $('#tableBestandsElemente').DataTable({
            "columnDefs": [
                {
                    "targets": [0],
                    "visible": false,
                    "searchable": false
                }
            ],
            "paging": true,
            "searching": true,
            "info": true,
            "order": [[1, "asc"]],
            "pagingType": "simple",
            "lengthChange": false,
            "pageLength": 10,
            'language': {
                'url': '//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json',
                "decimal": ",",
                "thousands": "."
            },
            "dom": '<"top"Blf>rt<"bottom"ip><"clear">',
            "buttons": [
                {
                    extend: 'excel',
                    exportOptions: {
                        columns: function (idx) {
                            return idx !== 0 && idx!==10 ;
                        }
                    }
                }
            ],
            "mark": true
        });


        // CLICK TABELLE
        var table1 = $('#tableBestandsElemente').DataTable();

        $('#tableBestandsElemente tbody').on('click', 'tr', function () {
            if ($(this).hasClass('info')) {
            } else {
                table1.$('tr.info').removeClass('info');
                $(this).addClass('info');
            }
        });

        $("button[name='showComment']").popover({
            trigger: 'click',
            placement: 'right',
            html: true,
            container: 'body',
            content: "<textarea class='popover-textarea'></textarea>",
            template: "<div class='popover'>" +
                "<h4 class='popover-header'></h4><div class='popover-body'>" +
                "</div><div class='popover-footer'><button type='button' class='btn btn-sm btn-outline-dark popover-submit'><i class='fas fa-check'></i>" +
                "</button>&nbsp;" +
                "</div>"

        });

        $("button[name='showComment']").click(function () {
            //hide any visible comment-popover
            $("button[name='showComment']").not(this).popover('hide');
            var id = this.id;
            var val = document.getElementById(id).value;
            //attach/link text
            $('.popover-textarea').val(val).focus();
            //update link text on submit
            $('.popover-submit').click(function () {
                document.getElementById(id).value = $('.popover-textarea').val();
                $(this).parents(".popover").popover('hide');
            });
        });
    });

    $("button[value='createBestandsPDF']").click(function () {
        window.open('/pdf_createBestandPDF.php');//there are many ways to do this
    });
</script>


</html>
