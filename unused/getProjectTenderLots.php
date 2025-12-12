<?php
// 25 FX
require_once 'utils/_utils.php';
check_login();

$mysqli = utils_connect_sql();
$stmt = $mysqli->prepare("SELECT tabelle_lose_extern.idtabelle_Lose_Extern, tabelle_lose_extern.LosNr_Extern,
                                    tabelle_lose_extern.LosBezeichnung_Extern, tabelle_lose_extern.Versand_LV, tabelle_lose_extern.Ausführungsbeginn, 
                                    tabelle_lose_extern.Verfahren, tabelle_lose_extern.Bearbeiter, tabelle_lose_extern.Vergabesumme, 
                                    tabelle_lose_extern.Vergabe_abgeschlossen, tabelle_lose_extern.Versand_LV, tabelle_lose_extern.Notiz, 
                                    tabelle_lieferant.Lieferant
                                FROM tabelle_lieferant RIGHT JOIN tabelle_lose_extern 
                                ON tabelle_lieferant.idTABELLE_Lieferant = tabelle_lose_extern.tabelle_lieferant_idTABELLE_Lieferant
                                WHERE (tabelle_lose_extern.tabelle_projekte_idTABELLE_Projekte=?)");

$projectID = (int)$_SESSION["projectID"];
$stmt->bind_param("i", $projectID);
$stmt->execute();
$result = $stmt->get_result();


echo "<table class='table table-striped table-bordered nowrap table-condensed' id='tableTenderLots'   >
                        <thead><tr>
                        <th>ID</th>
                        <th>Los-Nummer</th>
                        <th>Bezeichnung</th>
                        <th></th>
                        <th>Versand LV</th>
                        <th>Liefertermin</th>
                        <th>Verfahren</th>
                        <th>Bearbeiter</th>
                        <th>Abgeschlossen</th>
                        <th>Vergabesumme</th>
                        <th>Auftragnehmer</th>							
                        </tr></thead>
                        <tfoot>
                    <tr>
                        <th colspan='9' style='text-align:right'>Gesamtsumme:</th>
                        <th colspan='2'></th>
                    </tr>
                </tfoot> <tbody>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row["idtabelle_Lose_Extern"] . "</td>";
    echo "<td>" . $row["LosNr_Extern"] . "</td>";
    echo "<td>" . $row["LosBezeichnung_Extern"] . "</td>";
    echo "<td>";
    if (null != $row["Notiz"]) {
        echo "<span class='glyphicon glyphicon-list-alt'>";
    }
    echo "</td>";
    echo "<td>" . $row["Versand_LV"] . "</td>";
    echo "<td>" . $row["Ausführungsbeginn"] . "</td>";
    echo "<td>" . $row["Verfahren"] . "</td>";
    echo "<td>" . $row["Bearbeiter"] . "</td>";
    echo "<td>";
    switch ($row["Vergabe_abgeschlossen"]) {
        case 0:
            echo "Nein";
            break;
        case 1:
            echo "Ja";
            break;
    }
    echo "</td>";
    echo "<td>" . $row["Vergabesumme"] . "</td>";
    echo "<td>" . $row["Lieferant"] . "</td>";
    echo "</tr>";
}
echo "</tbody></table>";

?>

<script>
    var searchV = '<?php echo getPostInt( "searchValue");?>';
    // Tabelle formatieren
    $(document).ready(function () {
        var table1 =   $('#tableTenderLots').DataTable({
             columnDefs: [
                {
                    "targets": [0],
                    "visible": false,
                    "searchable": false
                }
             ],
             paging: false,
             searching: true,
             info: true,
             order: [[1, "asc"]],
             pagingType: "simple_numbers",
             lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
             language: {"url": "https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json"},
             scrollY: '40vh',
             scrollCollapse: true,
             footerCallback: function (row, data, start, end, display) {
                var api = this.api(), data;

                // Remove the formatting to get integer data for summation
                var intVal = function (i) {
                    return typeof i === 'string' ?
                        i.replace(/[\$,]/g, '') * 1 :
                        typeof i === 'number' ?
                            i : 0;
                };

                // Total over all pages
                total = api
                    .column(9)
                    .data()
                    .reduce(function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                // Total over this page
                pageTotal = api
                    .column(9, {page: 'current'})
                    .data()
                    .reduce(function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                // Update footer
                $(api.column(9).footer()).html(
                    '€ ' + pageTotal + ' ( €' + total + ' total)'
                );
            },
            fnRowCallback: function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                if (aData[8] === "Nein") {
                    $('td', nRow).css('background-color', 'LightCoral');
                } else {
                    $('td', nRow).css('background-color', 'LightGreen');
                }
            },
            search: {search: searchV}
        });


        $('#tableTenderLots tbody').on('click', 'tr', function () {

            if ($(this).hasClass('info')) {
                //$(this).removeClass('info');
            } else {
                table1.$('tr.info').removeClass('info');
                $(this).addClass('info');
                var lotID = table1.row($(this)).data()[0];
                $.ajax({
                    url: "getTenderLotDetails.php",
                    data: {"lotID": lotID},
                    type: "POST",
                    success: function (data) {
                        $("#lotDetails").html(data);
                        $.ajax({
                            url: "getTenderLotElements.php",
                            data: {"lotID": lotID},
                            type: "POST",
                            success: function (data) {
                                $("#elementsInLot").html(data);
                                $("#elementBestand").hide();
                                $("#variantenParameter").hide();
                            }
                        });
                    }
                });
            }
        });
    });
</script>
</html>
