<?php
session_start();
include '_utils.php';
check_login();
?>
<!DOCTYPE html>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<html>
    <head>
    </head>
    <body>
        <?php
        $mysqli = utils_connect_sql();

        if ($_GET["lotID"] != "") {
            $_SESSION["lotID"] = $_GET["lotID"];
        } else {
            echo "Kein Los ausgewählt!";
        }

        $sql = "
SELECT 
    tabelle_räume_has_tabelle_elemente.id, 
    tabelle_räume_has_tabelle_elemente.Anzahl, 
    tabelle_elemente.ElementID, 
    tabelle_elemente.Bezeichnung AS ElementBezeichnung, 
    tabelle_varianten.Variante, 
    tabelle_räume_has_tabelle_elemente.Kurzbeschreibung, 
    tabelle_räume.Geschoss, 
    tabelle_räume.`Raumbereich Nutzer`,   
    tabelle_räume.Raumnr, 
    tabelle_räume.Raumbezeichnung, 
    tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, 
    tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten, 
    tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente
FROM 
    tabelle_varianten 
    INNER JOIN (
        (tabelle_räume_has_tabelle_elemente 
        INNER JOIN tabelle_räume 
        ON tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume = tabelle_räume.idTABELLE_Räume) 
        INNER JOIN tabelle_elemente 
        ON tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente = tabelle_elemente.idTABELLE_Elemente
    ) 
    ON tabelle_varianten.idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten
WHERE 
    tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern = " . $_SESSION["lotID"] . " 
    AND tabelle_räume_has_tabelle_elemente.Standort = 1
ORDER BY 
    tabelle_räume.Raumnr;
";

        $result = $mysqli->query($sql);
        echo "<table class='table table-striped table-bordered table-sm' id='tableLotElements1'  cellspacing='0' width='100%'>
            <thead><tr>
            <th>ID</th>
            <th>elementID</th>
            <th>variantenID</th>
            <th>Stk</th>
            <th>ID</th>
            <th>Element</th>
            <th>Variante</th>
            <th>Bestand</th>
            <th>Raumnr</th>
            <th>Raum</th>
             <th>Geschoss</th>
                          <th>Raumbereich Nutzer</th>

            <th>Kommentar</th>								
            </tr></thead>           
            <tbody>";

        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row["id"] . "</td>";
            echo "<td>" . $row["TABELLE_Elemente_idTABELLE_Elemente"] . "</td>";
            echo "<td>" . $row["tabelle_Varianten_idtabelle_Varianten"] . "</td>";
            echo "<td>" . $row["Anzahl"] . "</td>";
            echo "<td>" . $row["ElementID"] . "</td>";
            echo "<td>" . $row["ElementBezeichnung"] . "</td>";
            echo "<td>" . $row["Variante"] . "</td>";
            echo "<td>";
            switch ($row["Neu/Bestand"]) {
                case 0:
                    echo "Ja";
                    break;
                case 1:
                    echo "Nein";
                    break;
            }
            echo "</td>";
            echo "<td>" . $row["Raumnr"] . "</td>";
            echo "<td>" . $row["Raumbezeichnung"] . "</td>";
            echo "<td>" . $row["Geschoss"] . "</td>";
            echo "<td>" . $row["Raumbereich Nutzer"] . "</td>";

            echo "<td><textarea id='comment" . $row["id"] . "' rows='1' style='width: 100%;'>" . $row["Kurzbeschreibung"] . "</textarea></td>";
            echo "</tr>";
        }

        echo "</tbody></table>";
        $mysqli->close();
        ?>
        <script>
            $(document).ready(function () {
                $('#tableLotElements1').DataTable({
                    "paging": true,
                    "select": true,
                    "columnDefs": [
                        {
                            "targets": [0, 1, 2],
                            "visible": false,
                            "searchable": false
                        }
                    ],
                    "searching": true,
                    "info": true,
                    "order": [[3, "asc"]],
                    "pagingType": "simple",
                    "lengthChange": false,
                    "pageLength": 10,
                    "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"},
                    dom: '<"top"fB> tip',
                    "buttons": [
                        {
                            extend: 'excel',
                            text: 'Download Excel'
                        },
                        {
                            extend: 'excel',
                            text: 'Verortungsliste',
                            exportOptions: {
                                columns: [3, 4, 5, 6, 7, 8, 9, 10,11] // Specify the columns you want to include
                            },
                            customize: function (xlsx) {
                                var sheet = xlsx.xl.worksheets['sheet1.xml'];
                                 $('row:first', sheet).remove();
                                $('row', sheet).each(function () {
                                    var col3 = $('c[r^="A"]', this);
                                    var col7 = $('c[r^="E"]', this);  
                                    if (col3.text() == '0') {
                                        $(this).remove();  
                                    } 
                                     if (col7.text() == 'Ja' ) {
                                        $(this).remove();  
                                    } 
                                    
                   
                                });
                            }
                        }
                    ]
                });

                var table = $('#tableLotElements1').DataTable();

                $('#tableLotElements1 tbody').on('click', 'tr', function () {
                    if ($(this).hasClass('info')) {
                    } else {
                        table.$('tr.info').removeClass('info');
                        $(this).addClass('info');
                        var elementID = table.row($(this)).data()[1];
                        var variantenID = table.row($(this)).data()[2];
                        var id = table.row($(this)).data()[0];
                        var stk = table.row($(this)).data()[3];

                        $.ajax({
                            url: "getVariantenParameters.php",
                            data: {"variantenID": variantenID, "elementID": elementID},
                            type: "GET",
                            success: function (data) {
                                $("#elementsvariantenParameterInLot").html(data);
                                $("#elementsvariantenParameterInLot").show();
                                $.ajax({
                                    url: "getElementBestand.php",
                                    data: {"id": id, "stk": stk},
                                    type: "GET",
                                    success: function (data) {
                                        $("#elementelementBestandsInLot").html(data);
                                        $("#elementelementBestandsInLot").show();
                                    }
                                });
                            }
                        });
                    }
                });
            });

            // PDF erzeugen
            $('#createLotElementListPDF').click(function () {
                window.open('/pdf_createLotElementListPDF.php');
            });

        </script> 

    </body>
</html>