<?php
include "_utils.php";
include "_format.php";
check_login();

$mysqli = utils_connect_sql();

if ($_GET["bestandInkl"] == 1) {
    /*$sql="SELECT tabelle_räume.Geschoss, tabelle_räume.Bauetappe, tabelle_räume.Bauabschnitt, tabelle_projekt_varianten_kosten.Kosten, Sum(tabelle_räume_has_tabelle_elemente.Anzahl) AS SummevonAnzahl, Sum(`Kosten`*`Anzahl`) AS Ausdr1, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, tabelle_auftraggeber_gewerke.Gewerke_Nr, tabelle_auftraggeber_ghg.GHG, tabelle_auftraggeberg_gug.GUG, tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung
            FROM tabelle_elemente INNER JOIN (tabelle_auftraggeberg_gug RIGHT JOIN (tabelle_auftraggeber_ghg RIGHT JOIN (tabelle_auftraggeber_gewerke RIGHT JOIN (tabelle_projekt_element_gewerk RIGHT JOIN (tabelle_projekt_varianten_kosten INNER JOIN (tabelle_räume_has_tabelle_elemente INNER JOIN tabelle_räume ON tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume = tabelle_räume.idTABELLE_Räume) ON (tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte) AND (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten)) ON (tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte = tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte) AND (tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente = tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente)) ON tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke = tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke) ON tabelle_auftraggeber_ghg.idtabelle_auftraggeber_GHG = tabelle_projekt_element_gewerk.tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG) ON tabelle_auftraggeberg_gug.idtabelle_auftraggeberg_GUG = tabelle_projekt_element_gewerk.tabelle_auftraggeberg_gug_idtabelle_auftraggeberg_GUG) ON tabelle_elemente.idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente
            WHERE (((tabelle_räume_has_tabelle_elemente.Standort)=1))
            GROUP BY tabelle_räume.`Raumbereich Nutzer`, tabelle_räume.tabelle_projekte_idTABELLE_Projekte, tabelle_räume.Geschoss, tabelle_räume.Bauetappe, tabelle_räume.Bauabschnitt, tabelle_projekt_varianten_kosten.Kosten, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, tabelle_auftraggeber_gewerke.Gewerke_Nr, tabelle_auftraggeber_ghg.GHG, tabelle_auftraggeberg_gug.GUG, tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung
            HAVING (((tabelle_räume.`Raumbereich Nutzer`)='".$_GET["roomArea"]."') AND ((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"]."))
            ORDER BY tabelle_auftraggeber_gewerke.Gewerke_Nr, tabelle_auftraggeber_ghg.GHG, tabelle_auftraggeberg_gug.GUG;";
            */
    $sql = "SELECT tabelle_projekt_varianten_kosten.Kosten, Sum(tabelle_räume_has_tabelle_elemente.Anzahl) AS SummevonAnzahl, Sum(`Kosten`*`Anzahl`) AS Ausdr1, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, tabelle_auftraggeber_gewerke.Gewerke_Nr, tabelle_auftraggeber_ghg.GHG, tabelle_auftraggeberg_gug.GUG, tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung
                        FROM tabelle_elemente INNER JOIN (tabelle_auftraggeberg_gug RIGHT JOIN (tabelle_auftraggeber_ghg RIGHT JOIN (tabelle_auftraggeber_gewerke RIGHT JOIN (tabelle_projekt_element_gewerk RIGHT JOIN (tabelle_projekt_varianten_kosten INNER JOIN (tabelle_räume_has_tabelle_elemente INNER JOIN tabelle_räume ON tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume = tabelle_räume.idTABELLE_Räume) ON (tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) AND (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte)) ON (tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente = tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente) AND (tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte = tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte)) ON tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke = tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke) ON tabelle_auftraggeber_ghg.idtabelle_auftraggeber_GHG = tabelle_projekt_element_gewerk.tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG) ON tabelle_auftraggeberg_gug.idtabelle_auftraggeberg_GUG = tabelle_projekt_element_gewerk.tabelle_auftraggeberg_gug_idtabelle_auftraggeberg_GUG) ON tabelle_elemente.idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente
                        WHERE (((tabelle_räume_has_tabelle_elemente.Standort)=1))
                        GROUP BY tabelle_projekt_varianten_kosten.Kosten, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, tabelle_auftraggeber_gewerke.Gewerke_Nr, tabelle_auftraggeber_ghg.GHG, tabelle_auftraggeberg_gug.GUG, tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_räume.`Raumbereich Nutzer`, tabelle_räume.tabelle_projekte_idTABELLE_Projekte
                        HAVING (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . ") AND (((tabelle_räume.`Raumbereich Nutzer`))='" . $_GET["roomArea"] . "'))
                        ORDER BY tabelle_auftraggeber_gewerke.Gewerke_Nr, tabelle_auftraggeber_ghg.GHG, tabelle_auftraggeberg_gug.GUG;";
} else {
    /*$sql="SELECT tabelle_räume.Geschoss, tabelle_räume.Bauetappe, tabelle_räume.Bauabschnitt, tabelle_projekt_varianten_kosten.Kosten, Sum(tabelle_räume_has_tabelle_elemente.Anzahl) AS SummevonAnzahl, Sum(`Kosten`*`Anzahl`) AS Ausdr1, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, tabelle_auftraggeber_gewerke.Gewerke_Nr, tabelle_auftraggeber_ghg.GHG, tabelle_auftraggeberg_gug.GUG, tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung
            FROM tabelle_elemente INNER JOIN (tabelle_auftraggeberg_gug RIGHT JOIN (tabelle_auftraggeber_ghg RIGHT JOIN (tabelle_auftraggeber_gewerke RIGHT JOIN (tabelle_projekt_element_gewerk RIGHT JOIN (tabelle_projekt_varianten_kosten INNER JOIN (tabelle_räume_has_tabelle_elemente INNER JOIN tabelle_räume ON tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume = tabelle_räume.idTABELLE_Räume) ON (tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte) AND (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten)) ON (tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte = tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte) AND (tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente = tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente)) ON tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke = tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke) ON tabelle_auftraggeber_ghg.idtabelle_auftraggeber_GHG = tabelle_projekt_element_gewerk.tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG) ON tabelle_auftraggeberg_gug.idtabelle_auftraggeberg_GUG = tabelle_projekt_element_gewerk.tabelle_auftraggeberg_gug_idtabelle_auftraggeberg_GUG) ON tabelle_elemente.idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente
            WHERE (((tabelle_räume_has_tabelle_elemente.Standort)=1))
            GROUP BY tabelle_räume.`Raumbereich Nutzer`, tabelle_räume.tabelle_projekte_idTABELLE_Projekte, tabelle_räume.Geschoss, tabelle_räume.Bauetappe, tabelle_räume.Bauabschnitt, tabelle_projekt_varianten_kosten.Kosten, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, tabelle_auftraggeber_gewerke.Gewerke_Nr, tabelle_auftraggeber_ghg.GHG, tabelle_auftraggeberg_gug.GUG, tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung
            HAVING (((tabelle_räume.`Raumbereich Nutzer`)='".$_GET["roomArea"]."') AND ((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"]." AND ((tabelle_räume_has_tabelle_elemente.`Neu/Bestand`)=1)))
            ORDER BY tabelle_auftraggeber_gewerke.Gewerke_Nr, tabelle_auftraggeber_ghg.GHG, tabelle_auftraggeberg_gug.GUG;";*/
    $sql = "SELECT tabelle_projekt_varianten_kosten.Kosten, Sum(tabelle_räume_has_tabelle_elemente.Anzahl) AS SummevonAnzahl, Sum(`Kosten`*`Anzahl`) AS Ausdr1, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, tabelle_auftraggeber_gewerke.Gewerke_Nr, tabelle_auftraggeber_ghg.GHG, tabelle_auftraggeberg_gug.GUG, tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung
                        FROM tabelle_elemente INNER JOIN (tabelle_auftraggeberg_gug RIGHT JOIN (tabelle_auftraggeber_ghg RIGHT JOIN (tabelle_auftraggeber_gewerke RIGHT JOIN (tabelle_projekt_element_gewerk RIGHT JOIN (tabelle_projekt_varianten_kosten INNER JOIN (tabelle_räume_has_tabelle_elemente INNER JOIN tabelle_räume ON tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume = tabelle_räume.idTABELLE_Räume) ON (tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) AND (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND (tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte)) ON (tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente = tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente) AND (tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte = tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte)) ON tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke = tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke) ON tabelle_auftraggeber_ghg.idtabelle_auftraggeber_GHG = tabelle_projekt_element_gewerk.tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG) ON tabelle_auftraggeberg_gug.idtabelle_auftraggeberg_GUG = tabelle_projekt_element_gewerk.tabelle_auftraggeberg_gug_idtabelle_auftraggeberg_GUG) ON tabelle_elemente.idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente
                        WHERE (((tabelle_räume_has_tabelle_elemente.Standort)=1))
                        GROUP BY tabelle_projekt_varianten_kosten.Kosten, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, tabelle_auftraggeber_gewerke.Gewerke_Nr, tabelle_auftraggeber_ghg.GHG, tabelle_auftraggeberg_gug.GUG, tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_räume.`Raumbereich Nutzer`, tabelle_räume.tabelle_projekte_idTABELLE_Projekte
                        HAVING (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . ") AND (((tabelle_räume.`Raumbereich Nutzer`))='" . $_GET["roomArea"] . "') AND (((tabelle_räume_has_tabelle_elemente.`Neu/Bestand`))=1))
                        ORDER BY tabelle_auftraggeber_gewerke.Gewerke_Nr, tabelle_auftraggeber_ghg.GHG, tabelle_auftraggeberg_gug.GUG;";

}


$result = $mysqli->query($sql);

echo "<table class='table table-striped table-bordered table-sm' id='tableRoomAreaCosts' cellspacing='0' width='100%'>
	<thead><tr>
	<th>Element</th>
	<th>Bestand</th>
	<th>Stk/lfm</th>
	<th>EP</th>
	<th>PP</th>
	<th>EP</th>
	<th>PP</th>
	<th>Gewerk</th>
	<th>GHG</th>
	<th>GUG</th>
	</tr></thead>
        <tfoot>
            <tr>
                <th colspan='4' style='text-align:right'>Summe:</th>
                <th></th>
            </tr>
        </tfoot><tbody>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row["ElementID"] . " - " . $row["Bezeichnung"] . "</td>";
    if ($row["Neu/Bestand"] == 0) {
        echo "<td>Ja</td>";
    } else {
        echo "<td>Nein</td>";
    }
    echo "<td>" . $row["SummevonAnzahl"] . "</td>";
    echo "<td>" . format_money($row["Kosten"]) . "</td>";
    echo "<td>" . format_money($row["Ausdr1"]) . "</td>";

    echo "<td>" . ($row["Kosten"]) . "</td>";
    echo "<td>" . ($row["Ausdr1"]) . "</td>";

    echo "<td>" . $row["Gewerke_Nr"] . "</td>";
    echo "<td>" . $row["GHG"] . "</td>";
    echo "<td>" . $row["GUG"] . "</td>";
    echo "</tr>";
}
echo "</tbody></table>";
$mysqli->close();
?>

<script>

    $(document).ready(function () {
        $('#tableRoomAreaCosts').DataTable({
            "dom": 'Bti',
            "paging": false,
            "searching": false,
            "info": true,
            "order": [[0, "asc"]],
            'language': {
                'url': '//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json',
                "decimal": ",",
                "thousands": "."
            },
            "columnDefs": [
                {
                    "targets": [5,6],
                    "visible": false,
                    "searchable": false
                }
            ],
            "buttons": [
                {
                    extend: 'excel',
                    exportOptions: {
                        columns: function (idx) {
                            return idx !== 3 && idx !== 4;
                        }
                    }
                }
            ],

            "footerCallback": function (row, data, start, end, display) {
                var api = this.api();
                var intVal = function (i) {
                    return typeof i === 'string' ?
                        i.replace(/[$,]/g, '').replace(/\./g, '').replace(/,/, '.') * 1 :
                        typeof i === 'number' ?
                            i : 0;
                };
                // Total over all pages
                var total = api
                    .column(4)
                    .data()
                    .reduce(function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                // Total over this page
                var pageTotal = api
                    .column(4, {page: 'current'})
                    .data()
                    .reduce(function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                // Format the total
                const formattedNumber = new Intl.NumberFormat('de-DE', {
                    style: 'currency',
                    currency: 'EUR'
                }).format(pageTotal);
                $(api.column(4).footer()).html(formattedNumber);
            }
        });
    });
</script>
