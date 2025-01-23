<?php
include '_utils.php';
init_page_serversides();
?>


<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>RB-Liste</title>
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
</head>


<body style="height:100%">
<div id="limet-navbar"></div>
<div class="container-fluid">

    <div class="mt-4 card">
        <div class="card-header"><b>Elemente im Projekt</b></div>
        <div class="card-body" id="elementLots">

            <?php
            $mysqli = utils_connect_sql();
            $sql = "SELECT tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung, tabelle_räume.`Raumbereich Nutzer`, tabelle_räume.Geschoss, tabelle_räume.Bauetappe, tabelle_räume.Bauabschnitt, 
                                                        tabelle_räume_has_tabelle_elemente.Anzahl, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, tabelle_räume_has_tabelle_elemente.Standort, tabelle_projekt_varianten_kosten.Kosten AS EP, 
                                                        tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_varianten.Variante, tabelle_projektbudgets.Budgetnummer, tabelle_lose_extern.LosNr_Extern, tabelle_auftraggeber_gewerke.Gewerke_Nr, tabelle_auftraggeber_ghg.GHG, tabelle_räume_has_tabelle_elemente.Kurzbeschreibung
                                                        FROM (((tabelle_projekt_element_gewerk RIGHT JOIN (tabelle_projektbudgets RIGHT JOIN (tabelle_lose_extern RIGHT JOIN (tabelle_varianten INNER JOIN (tabelle_elemente INNER JOIN ((tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) INNER JOIN tabelle_projekt_varianten_kosten ON (tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten = tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten) AND (tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte) AND (tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente = tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente)) ON tabelle_elemente.idTABELLE_Elemente = tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente) ON tabelle_varianten.idtabelle_Varianten = tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten) ON tabelle_lose_extern.idtabelle_Lose_Extern = tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern) ON tabelle_projektbudgets.idtabelle_projektbudgets = tabelle_räume_has_tabelle_elemente.tabelle_projektbudgets_idtabelle_projektbudgets) ON (tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente = tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente) AND (tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte = tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte)) LEFT JOIN tabelle_auftraggeber_gewerke ON tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke = tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke) LEFT JOIN tabelle_auftraggeber_ghg ON tabelle_projekt_element_gewerk.tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG = tabelle_auftraggeber_ghg.idtabelle_auftraggeber_GHG) LEFT JOIN tabelle_auftraggeberg_gug ON tabelle_projekt_element_gewerk.tabelle_auftraggeberg_gug_idtabelle_auftraggeberg_GUG = tabelle_auftraggeberg_gug.idtabelle_auftraggeberg_GUG
                                                        WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . "));";
            $result = $mysqli->query($sql);
            echo "<table class='table table-striped table-bordered table-sm' id='tableRoombookList'>
                                                        <thead><tr>
                                                            <th>Raumnr</th>
                                                            <th>Raum</th>
                                                            <th>Raumbereich</th>
                                                            <th>Geschoss</th>
                                                            <th>BE</th>
                                                            <th>BA</th>
                                                            <th>Stk</th>
                                                            <th>ID</th>
                                                            <th>Element</th>
                                                            <th>Variante</th>  
                                                            <th>Standort</th>  
                                                            <th>Bestand</th>                                                                              									
                                                            <th>EP</th>                                                                
                                                            <th>Los-Nr</th>
                                                            <th>Budget</th>                                                                
                                                            <th>Gewerk</th>
                                                            <th>GHG</th>
                                                        </tr>
                                                        </thead>";

            echo "<tbody>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row["Raumnr"] . "</td>";
                echo "<td>" . $row["Raumbezeichnung"] . "</td>";
                echo "<td>" . $row["Raumbereich Nutzer"] . "</td>";
                echo "<td>" . $row["Geschoss"] . "</td>";
                echo "<td>" . $row["Bauetappe"] . "</td>";
                echo "<td>" . $row["Bauabschnitt"] . "</td>";
                echo "<td>" . $row["Anzahl"] . "</td>";
                echo "<td>" . $row["ElementID"] . "</td>";
                echo "<td>" . $row["Bezeichnung"] . "</td>";
                echo "<td>" . $row["Variante"] . "</td>";
                echo "<td>" . $row["Standort"] . "</td>";
                echo "<td>" . $row["Neu/Bestand"] . "</td>";
                echo "<td>" . sprintf('%01.2f', $row["EP"]) . "</td>";
                echo "<td>" . $row["LosNr_Extern"] . "</td>";
                echo "<td>" . $row["Budgetnummer"] . "</td>";
                echo "<td>" . $row["Gewerke_Nr"] . "</td>";
                echo "<td>" . $row["GHG"] . "</td>";

                echo "</tr>";

            }
            echo "</tbody></table>";
            $mysqli->close();

            ?>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#tableRoombookList').DataTable({
            "paging": true,
            "select": true,
            "pagingType": "simple",
            "lengthChange": false,
            "pageLength": 10,
            "order": [[2, "asc"]],
            "columnDefs": [
                {
                    "targets": [0],
                    "visible": false,
                    "searchable": false
                },
                {
                    "targets": [5],
                    "visible": false,
                    "searchable": false
                }
            ],
            "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"},
            "stateSave": true,
            dom: 'Blfrtip',
            "buttons": [
                'excel'
            ],
            "mark": true
        });
    });

</script>


</body>

</html>
