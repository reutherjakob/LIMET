<?php
// 25 FX
require_once 'utils/_utils.php';
include "utils/_format.php";
check_login();

$bestandInkl = isset($_POST["bestandInkl"]) && $_POST["bestandInkl"] == 1;
$projectID = (int)$_SESSION["projectID"] ?? 0;
$roomArea = $_POST["roomArea"] ?? '';

$sql = "SELECT tabelle_projekt_varianten_kosten.Kosten,
       Sum(tabelle_räume_has_tabelle_elemente.Anzahl) AS SummevonAnzahl,
       Sum(`Kosten` * `Anzahl`)                       AS Ausdr1,
       tabelle_räume_has_tabelle_elemente.`Neu/Bestand`,
       tabelle_auftraggeber_gewerke.Gewerke_Nr,
       tabelle_auftraggeber_ghg.GHG,
       tabelle_auftraggeberg_gug.GUG,
       tabelle_elemente.ElementID,
       tabelle_elemente.Bezeichnung
FROM tabelle_elemente
         INNER JOIN (tabelle_auftraggeberg_gug RIGHT JOIN (tabelle_auftraggeber_ghg RIGHT JOIN (tabelle_auftraggeber_gewerke RIGHT JOIN (tabelle_projekt_element_gewerk RIGHT JOIN (tabelle_projekt_varianten_kosten INNER JOIN (tabelle_räume_has_tabelle_elemente INNER JOIN tabelle_räume
                                                                                                                                                                                                                                 ON tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume =
                                                                                                                                                                                                                                    tabelle_räume.idTABELLE_Räume)
                                                                                                                                                                                    ON (tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten =
                                                                                                                                                                                        tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) AND
                                                                                                                                                                                       (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente =
                                                                                                                                                                                        tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND
                                                                                                                                                                                       (tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte =
                                                                                                                                                                                        tabelle_räume.tabelle_projekte_idTABELLE_Projekte))
                                                                                                                                         ON (tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente =
                                                                                                                                             tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente) AND
                                                                                                                                            (tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte =
                                                                                                                                             tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte))
                                                                                                ON tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke =
                                                                                                   tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke)
                                                           ON tabelle_auftraggeber_ghg.idtabelle_auftraggeber_GHG =
                                                              tabelle_projekt_element_gewerk.tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG)
                     ON tabelle_auftraggeberg_gug.idtabelle_auftraggeberg_GUG =
                        tabelle_projekt_element_gewerk.tabelle_auftraggeberg_gug_idtabelle_auftraggeberg_GUG)
                    ON tabelle_elemente.idTABELLE_Elemente =
                       tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente
WHERE (tabelle_räume_has_tabelle_elemente.Standort = 1
AND tabelle_räume.tabelle_projekte_idTABELLE_Projekte = ? 
AND tabelle_räume.`Raumbereich Nutzer`= ?
 " . ($bestandInkl ? "" : "AND tabelle_räume_has_tabelle_elemente.`Neu/Bestand` = 1") . " )  
GROUP BY tabelle_projekt_varianten_kosten.Kosten, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`,
         tabelle_auftraggeber_gewerke.Gewerke_Nr, tabelle_auftraggeber_ghg.GHG, tabelle_auftraggeberg_gug.GUG,
         tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_räume.`Raumbereich Nutzer` ";

$mysqli = utils_connect_sql();
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("is", $projectID, $roomArea);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

echo "<table class='table table-striped table-bordered border border-light border-5 table-sm' id='tableRoomAreaCosts' > 
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
	
    <tbody>";

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
    echo "<td>" . (float)($row["Kosten"]) . "</td>";
    echo "<td>" . (float)($row["Ausdr1"]) . "</td>";
    echo "<td>" . $row["Gewerke_Nr"] . "</td>";
    echo "<td>" . $row["GHG"] . "</td>";
    echo "<td>" . $row["GUG"] . "</td>";
    echo "</tr>";
}
echo "</tbody>
</table>";
$mysqli->close();
?>
<div class="row">
    <div class="col-10">
        <div class="d-flex align-items-center justify-content-end">
            <label class="badge bg-secondary"> SUMME PPs:
                <input class="bg-secondary-subtle border-light text-dark text-md-center fs-5" disabled id="SUMME">
            </label>
        </div>
    </div>
</div>

<script>
    function formatGermanCurrency(amount) {
        return new Intl.NumberFormat('de-DE', {
            style: 'currency',
            currency: 'EUR',
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        }).format(amount);
    }

    $(document).ready(function () {
        new DataTable('#tableRoomAreaCosts', {
            paging: false,
            searching: false,
            info: true,
            order: [[0, 'asc']],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json',
                decimal: ',',
                thousands: '.'
            },
            columnDefs: [
                {
                    targets: [5, 6],
                    visible: false,
                    searchable: false
                }
            ], layout: {
                topStart: "buttons",
                topEnd: null,
                bottomStart: 'info',
                bottomEnd: null
            },
            buttons: [
                {
                    extend: 'excelHtml5',
                    filename: 'Kostenübersicht_Raumbereich_' + '<?php echo $roomArea; ?>' + '_' + new Date().toISOString().slice(0, 10),
                    footer: false,
                    exportOptions: {
                        columns: [0, 1, 2, 5, 6, 7, 8, 9],
                    }, text: '<i class="fas fa-file-excel"></i> Excel', // Add Font Awesome icon
                    className: 'btn btn-sm btn-light btn-outline-success' // Bootstrap small
                }
            ],

            footerCallback: function () {//row, data, start, end, display) {
                let api = this.api();
                let intVal = function (i) {
                    return typeof i === 'string' ?
                        i.replace(/[$,]/g, '').replace(/\./g, '').replace(/,/, '.') * 1 :
                        typeof i === 'number' ?
                            i : 0;
                };
                let total = api
                    .column(6)
                    .data()
                    .reduce(function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);
                $("#SUMME").val(formatGermanCurrency(total));
            }
        });
    });
</script>
