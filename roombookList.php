<!-- 19.2.25: Reworked -->

<?php
if (!function_exists('utils_connect_sql')) {
    include "_utils.php";
}
include "_format.php";
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
<div class="container-fluid bg-light">
    <div id="limet-navbar"></div>
    <div class="mt-4 card">
        <div class="card-header"><b>Elemente im Projekt</b></div>
        <div class="card-body" id="elementLots">
            <?php
            $mysqli = utils_connect_sql();
            $sql = "SELECT tabelle_räume.Raumnr,
       tabelle_räume.idTABELLE_Räume,
       tabelle_räume.Raumbezeichnung,
       tabelle_räume.`Raumbereich Nutzer`,
       tabelle_räume.Raumnummer_Nutzer,
       tabelle_räume.Geschoss,
       tabelle_räume.Bauetappe,
       tabelle_räume.Bauabschnitt,
       tabelle_räume_has_tabelle_elemente.Anzahl,
       tabelle_räume_has_tabelle_elemente.`Neu/Bestand`,
       tabelle_räume_has_tabelle_elemente.Standort,
       tabelle_projekt_varianten_kosten.Kosten AS EP,
       tabelle_elemente.ElementID,
       tabelle_elemente.Bezeichnung,
       tabelle_varianten.Variante,
       tabelle_projektbudgets.Budgetnummer,
       tabelle_lose_extern.LosNr_Extern,
       tabelle_auftraggeber_gewerke.Gewerke_Nr,
       tabelle_auftraggeber_ghg.GHG,
       tabelle_räume_has_tabelle_elemente.Kurzbeschreibung
FROM (((tabelle_projekt_element_gewerk RIGHT JOIN (tabelle_projektbudgets RIGHT JOIN (tabelle_lose_extern RIGHT JOIN (tabelle_varianten INNER JOIN (tabelle_elemente INNER JOIN ((tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente
                                                                                                                                                                                  ON tabelle_räume.idTABELLE_Räume =
                                                                                                                                                                                     tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) INNER JOIN tabelle_projekt_varianten_kosten
                                                                                                                                                                                 ON (tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten =
                                                                                                                                                                                     tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten) AND
                                                                                                                                                                                    (tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte =
                                                                                                                                                                                     tabelle_räume.tabelle_projekte_idTABELLE_Projekte) AND
                                                                                                                                                                                    (tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente =
                                                                                                                                                                                     tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente))
                                                                                                                                                    ON tabelle_elemente.idTABELLE_Elemente =
                                                                                                                                                       tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente)
                                                                                                                      ON tabelle_varianten.idtabelle_Varianten =
                                                                                                                         tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten)
                                                                                      ON tabelle_lose_extern.idtabelle_Lose_Extern =
                                                                                         tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern)
                                                   ON tabelle_projektbudgets.idtabelle_projektbudgets =
                                                      tabelle_räume_has_tabelle_elemente.tabelle_projektbudgets_idtabelle_projektbudgets)
        ON (tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente =
            tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente) AND
           (tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte =
            tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte)) LEFT JOIN tabelle_auftraggeber_gewerke
       ON tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke =
          tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke) LEFT JOIN tabelle_auftraggeber_ghg
      ON tabelle_projekt_element_gewerk.tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG =
         tabelle_auftraggeber_ghg.idtabelle_auftraggeber_GHG)
         LEFT JOIN tabelle_auftraggeberg_gug
                   ON tabelle_projekt_element_gewerk.tabelle_auftraggeberg_gug_idtabelle_auftraggeberg_GUG =
                      tabelle_auftraggeberg_gug.idtabelle_auftraggeberg_GUG
WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte) = " . $_SESSION["projectID"] . "));";

            $result = $mysqli->query($sql);
            echo "<table class='table table-striped table-hover compact table-bordered' id='tableRoombookList'>
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
                                                             <th>EP-Excel</th>                                                            
                                                            <th>Los-Nr</th>
                                                            <th>Budget</th>                                                                
                                                            <th>Gewerk</th>
                                                            <th>GHG</th>
                                                            <!--th>RAUM-ID</th-->
                                                        </tr>
                                                        </thead>";
            echo "<tbody>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                if ($_SESSION["projectName"] === "GCP") {
                    echo "<td>" . $row["Raumnummer_Nutzer"] . "</td>";
                } else {
                    echo "<td>" . $row["Raumnr"] . "</td>";
                }
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
                echo "<td>" . format_money( $row["EP"]) . "</td>";
                echo "<td>" . $row["EP"] . "</td>";
                echo "<td>" . $row["LosNr_Extern"] . "</td>";
                echo "<td>" . $row["Budgetnummer"] . "</td>";
                echo "<td>" . $row["Gewerke_Nr"] . "</td>";
                echo "<td>" . $row["GHG"] . "</td>";
              //  echo "<td>" . $row["idTABELLE_Räume"] . "</td>";
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
        new DataTable('#tableRoombookList', {
            select: true,
            layout: {
                topStart: 'buttons',
                topEnd: ['search','info'],
                bottomStart: null,
                bottomEnd: null
            },
            order: [[2, "asc"]],
            columnDefs: [
                {
                    targets: [13],
                    visible: false,
                    searchable: false
                }
            ],
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json",
                search: ""
            },
            buttons: [
                {
                    extend: 'excelHtml5',
                    exportOptions: {
                        columns: ':not(:eq(12))' // Exclude column 12 (index 11)
                    }
                },
                'searchBuilder'
            ],
            paging: false
        });
    });

</script>
</body>
</html>

