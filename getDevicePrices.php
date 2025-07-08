<!DOCTYPE html >
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker3.min.css">
    <script type='text/javascript'
            src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/js/bootstrap-datepicker.min.js"></script>
    <title></title>
</head>
<body>
<?php
if (!function_exists('utils_connect_sql')) {
    include "_utils.php";
}
include "_format.php";
check_login();

$mysqli = utils_connect_sql();
if ($_GET["deviceID"] != "") {
    $_SESSION["deviceID"] = $_GET["deviceID"];
}
$sql = "SELECT tabelle_preise.Datum,
       tabelle_preise.Quelle,
       tabelle_preise.Menge,
       tabelle_preise.Preis,
       tabelle_preise.Nebenkosten,
       tabelle_projekte.Interne_Nr,
       tabelle_projekte.Projektname,
       tabelle_projekte.Preisbasis,
       tabelle_lieferant.Lieferant
FROM tabelle_lieferant
         RIGHT JOIN (tabelle_preise LEFT JOIN tabelle_projekte
                     ON tabelle_preise.TABELLE_Projekte_idTABELLE_Projekte = tabelle_projekte.idTABELLE_Projekte)
                    ON tabelle_lieferant.idTABELLE_Lieferant = tabelle_preise.tabelle_lieferant_idTABELLE_Lieferant
WHERE (((tabelle_preise.TABELLE_Geraete_idTABELLE_Geraete) = " . $_SESSION["deviceID"] . "));";

$result = $mysqli->query($sql);
echo "<table class='table table-striped table-sm' id='tableDevicePrices'>
	<thead><tr>";
echo "<th>Datum</th>
		<th>Info</th>
		<th>Menge</th>
		<th>EP</th>
		<th>NK/Stk</th>
                <th>Projekt</th>
                <th>Lieferant</th>
                <th>Preisbasis</th>
	</tr></thead><tbody>";
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    $date = date_create($row["Datum"]);
    echo "<td>" . date_format($date, 'Y-m-d') . "</td>";
    echo "<td>" . $row["Quelle"] . "</td>";
    echo "<td>" . $row["Menge"] . "</td>";
    echo "<td>" . format_money($row["Preis"]) . "</td>";
    echo "<td>" . format_money($row["Nebenkosten"]) . "</td>";
    echo "<td>" . $row["Projektname"] . "</td>";
    echo "<td>" . $row["Lieferant"] . "</td>";
    echo "<td>" . $row["Preisbasis"] . "</td>";
    echo "</tr>";
}
echo "</tbody></table>";
echo "<button type='button' id='addPriceModal' class='btn btn-success' value='Preis hinzufügen' data-bs-toggle='modal' data-bs-target='#addPriceToElementModal'> Preis hinzufügen</button>";
?>

<div class='modal fade' id='addPriceToElementModal' role='dialog' tabindex="-1">
    <div class='modal-dialog modal-md'>
        <div class='modal-content'>
            <div class='modal-header'>
                <h4 class='modal-title'>Preis hinzufügen</h4>
                <button type='button' class='close' data-bs-dismiss='modal'>&times;</button>
            </div>
            <div class='modal-body' id='mbody'>
                <form role="form">
                    <div class="form-group">
                        <label for="date">Datum:</label>
                        <input type="text" class="form-control" id="date" placeholder="jjjj.mm.tt"/>
                    </div>
                    <div class="form-group">
                        <label for="quelle">Info:</label>
                        <input type="text" class="form-control" id="quelle" placeholder="Verfahrensart, Anmerkung,..."/>
                    </div>
                    <div class="form-group">
                        <label for="menge">Menge:</label>
                        <input type="text" class="form-control" id="menge"/>
                    </div>
                    <div class="form-group">
                        <label for="ep">EP:</label>
                        <input type="text" class="form-control" id="ep" placeholder="Komma ."/>
                    </div>
                    <div class="form-group">
                        <label for="nk">NK/Stk:</label>
                        <input type="text" class="form-control" id="nk" placeholder="Komma ."/>
                    </div>

                    <?php

                    $sql = "SELECT tabelle_projekte.idTABELLE_Projekte, tabelle_projekte.Interne_Nr, tabelle_projekte.Projektname"
                        . " FROM tabelle_projekte ORDER BY tabelle_projekte.Interne_Nr;";

                    $result1 = $mysqli->query($sql);

                    echo "<div class='form-group'>
                                                    <label for='project'>Projekt:</label>									
                                                    <select class='form-control input-sm' id='project' name='project'>
                                                            <option value=0>Kein Projekt</option>";

                    while ($row = $result1->fetch_assoc()) {
                        echo "<option value=" . $row["idTABELLE_Projekte"] . ">" . $row["Interne_Nr"] . "-" . $row["Projektname"] . "</option>";
                    }
                    echo "</select>										
                                                </div>";

                    $sql = "SELECT tabelle_lieferant.Lieferant, tabelle_lieferant.idTABELLE_Lieferant
                                                        FROM tabelle_lieferant INNER JOIN tabelle_geraete_has_tabelle_lieferant ON tabelle_lieferant.idTABELLE_Lieferant = tabelle_geraete_has_tabelle_lieferant.tabelle_lieferant_idTABELLE_Lieferant
                                                        WHERE (((tabelle_geraete_has_tabelle_lieferant.tabelle_geraete_idTABELLE_Geraete)=" . $_SESSION["deviceID"] . "));";

                    $result1 = $mysqli->query($sql);

                    echo "<div class='form-group'>
                                                    <label for='project'>Lieferant:</label>									
                                                    <select class='form-control input-sm' id='lieferant' name='lieferant'>
                                                            <option value='0'>Lieferant auswählen</option>";
                    while ($row = $result1->fetch_assoc()) {
                        echo "<option value=" . $row["idTABELLE_Lieferant"] . ">" . $row["Lieferant"] . "</option>";
                    }
                    echo "</select>										
                                                </div>";
                    $mysqli->close();
                    ?>
                </form>
            </div>
            <div class='modal-footer'>
                <input type='button' id='addPrice' class='btn btn-success btn-sm' value='Speichern'
                       data-bs-dismiss='modal'></input>
                <button type='button' class='btn btn-danger btn-sm' data-bs-dismiss='modal'>Abbrechen</button>
            </div>
        </div>

    </div>
</div>


<script>
    $(document).ready(function () {
        $('#date').datepicker({
            format: "yyyy-mm-dd",
            calendarWeeks: true,
            autoclose: true,
            todayBtn: "linked",
            language: "de"
        });
    });


    $("#tableDevicePrices").DataTable({
        "paging": false,
        "searching": false,
        "info": false,
        "order": [[0, "desc"]],
        //"pagingType": "simple_numbers",
        //"lengthMenu": [[5,10, 25, 50, -1], [5,10, 25, 50, "All"]],
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json",
            "decimal": ",",
            "thousands": "."
        },
        "scrollY": '20vh',
        "scrollCollapse": true
    });


    //Preis zu Geraet hinzufügen
    $("#addPrice").click(function () {  // TODO test preis hinzufügen...
        var date = $("#date").val();
        var quelle = $("#quelle").val();
        var menge = $("#menge").val();

        var nk = $("#nk").val();
        if (nk.toLowerCase().endsWith('k')) {
            nk = nk.slice(0, -1) + '000';
        }
        nk.replace(/,/g, '.').replace(/[^0-9.]/g, '');

        var project = $("#project").val();
        var lieferant = $("#lieferant").val();

        let ep = $("#ep").val();
        if (ep.toLowerCase().endsWith('k')) {
            ep = ep.slice(0, -1) + '000';
        }
        ep.replace(/,/g, '.').replace(/[^0-9.]/g, '');

        if (date !== "" && quelle !== "" && menge !== "" && ep !== "" && nk !== "" && lieferant > 0) {
            $.ajax({
                url: "addPriceToDevice.php",
                data: {
                    "date": date,
                    "quelle": quelle,
                    "menge": menge,
                    "ep": ep,
                    "nk": nk,
                    "project": project,
                    "lieferant": lieferant
                },
                type: "GET",
                success: function (data) {
                    alert(data);
                    $.ajax({
                        url: "getDevicePrices.php",
                        type: "GET",
                        success: function (data) {
                            $("#devicePrices").html(data);
                        }
                    });
                }
            });

        } else {
            alert("Bitte alle Felder ausfüllen!");
        }
    });


</script>

</body>
</html>