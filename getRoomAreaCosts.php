<?php
if (!function_exists('utils_connect_sql')) {  include "_utils.php"; }
include "_format.php";
check_login();


$bestandInkl = isset($_GET["bestandInkl"]) && $_GET["bestandInkl"] == 1;
$projectID = $_SESSION["projectID"] ?? 0;
$roomArea = $_GET["roomArea"] ?? '';

$sql = "SELECT 
    tvk.Kosten, 
    SUM(trte.Anzahl) AS SummevonAnzahl, 
    SUM(tvk.Kosten * trte.Anzahl) AS Ausdr1, 
    trte.`Neu/Bestand`, 
    tag.Gewerke_Nr, 
    tagh.GHG, 
    tagug.GUG, 
    te.ElementID, 
    te.Bezeichnung
FROM 
    tabelle_räume_has_tabelle_elemente trte
    INNER JOIN tabelle_räume tr ON trte.TABELLE_Räume_idTABELLE_Räume = tr.idTABELLE_Räume
    INNER JOIN tabelle_projekt_varianten_kosten tvk ON tvk.tabelle_Varianten_idtabelle_Varianten = trte.tabelle_Varianten_idtabelle_Varianten
        AND tvk.tabelle_elemente_idTABELLE_Elemente = trte.TABELLE_Elemente_idTABELLE_Elemente
        AND tvk.tabelle_projekte_idTABELLE_Projekte = tr.tabelle_projekte_idTABELLE_Projekte
    INNER JOIN tabelle_projekt_element_gewerk tpeg ON tpeg.tabelle_elemente_idTABELLE_Elemente = tvk.tabelle_elemente_idTABELLE_Elemente
        AND tpeg.tabelle_projekte_idTABELLE_Projekte = tvk.tabelle_projekte_idTABELLE_Projekte
    LEFT JOIN tabelle_auftraggeber_gewerke tag ON tag.idTABELLE_Auftraggeber_Gewerke = tpeg.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke
    LEFT JOIN tabelle_auftraggeber_ghg tagh ON tagh.idtabelle_auftraggeber_GHG = tpeg.tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG
    LEFT JOIN tabelle_auftraggeberg_gug tagug ON tagug.idtabelle_auftraggeberg_GUG = tpeg.tabelle_auftraggeberg_gug_idtabelle_auftraggeberg_GUG
    INNER JOIN tabelle_elemente te ON te.idTABELLE_Elemente = trte.TABELLE_Elemente_idTABELLE_Elemente
WHERE 
    trte.Standort = 1
    AND tr.tabelle_projekte_idTABELLE_Projekte = ?
    AND tr.`Raumbereich Nutzer` = ?
    " . ($bestandInkl ? "" : "AND trte.`Neu/Bestand` = 1") . "
GROUP BY 
    tvk.Kosten, trte.`Neu/Bestand`, tag.Gewerke_Nr, tagh.GHG, tagug.GUG, te.ElementID, te.Bezeichnung
ORDER BY 
    tag.Gewerke_Nr, tagh.GHG, tagug.GUG";

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

        <tfoot>
            <tr>
                <th colspan='4' style='text-align:right'>Summe:</th>
                <th></th>
            </tr>
        </tfoot>
</table>";
$mysqli->close();
?>

<script>
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
            ],
            buttons: [
                {
                    extend: 'excel',
                    exportOptions: {
                        columns: function (idx) {
                            return idx !== 3 && idx !== 4;
                        }
                    }
                }
            ],
            layout: {
                topStart: null,
                topEnd: null,
                bottomStart: 'info',
                bottomEnd: 'buttons'
            },
            footerCallback: function () {//row, data, start, end, display) {
                let api = this.api();
                let intVal = function (i) {
                    return typeof i === 'string' ?
                        i.replace(/[$,]/g, '').replace(/\./g, '').replace(/,/, '.') * 1 :
                        typeof i === 'number' ?
                            i : 0;
                };
                // Total over all pages
                let total = api
                    .column(4)
                    .data()
                    .reduce(function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                // Total over this page
                let pageTotal = api
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
