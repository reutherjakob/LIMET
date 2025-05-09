<!DOCTYPE html  >
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <!--DATEPICKER -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.5.0/css/bootstrap-datepicker3.min.css"/>
    <script type='text/javascript'
            src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.5.0/js/bootstrap-datepicker.min.js"></script>
    <title></title>
</head>
<body>
<!-- Rework 2025 -->
<?php
if (!function_exists('utils_connect_sql')) {  include "_utils.php"; }
include "_format.php";
check_login();
$mysqli = utils_connect_sql();

if ($_GET["lieferantenID"] != "") {
    $_SESSION["lieferantenID"] = $_GET["lieferantenID"];
}


$sql = "SELECT tabelle_umsaetze.idtabelle_umsaetze, tabelle_umsaetze.umsatz, tabelle_umsaetze.geschaeftsbereich, tabelle_umsaetze.jahr
                FROM tabelle_umsaetze
                WHERE (((tabelle_umsaetze.tabelle_lieferant_idTABELLE_Lieferant)=" . $_SESSION["lieferantenID"] . "));";

$result = $mysqli->query($sql);

echo "<table class='table table-striped table-sm' id='tableLieferantenUmsaetze'  >
	<thead><tr>";
echo "<th>ID</th>
		<th>Umsatz</th>
		<th>Geschäftsbereich</th>
		<th>Jahr</th>
	</tr></thead><tbody>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row["idtabelle_umsaetze"] . "</td>";
    echo "<td>" . format_money($row["umsatz"]) . "</td>";
    echo "<td>" . $row["geschaeftsbereich"] . "</td>";
    echo "<td>" . $row["jahr"] . "</td>";
    echo "</tr>";
}

echo "</tbody></table>";
echo "<input type='button' id='addUmsatzModal' class='btn btn-success btn-sm' value='Umsatz hinzufügen'  data-bs-toggle='modal' data-bs-target='#addUmsatzToLieferantModal'>";

?>

<!-- Modal zum Anlegen eines Umsatzes -->
<div class='modal fade' id='addUmsatzToLieferantModal' role='dialog'>
    <div class='modal-dialog modal-md'>

        <!-- Modal content-->
        <div class='modal-content'>
            <div class='modal-header'>
                <button type='button' class='close' data-bs-dismiss='modal'>&times;</button>
                <h4 class='modal-title'>Umsatz hinzufügen</h4>
            </div>
            <div class='modal-body' id='mbody'>
                <form role="form">
                    <div class="form-group">
                        <label for="umsatz">Umsatz:</label>
                        <input type="Umsatz" class="form-control form-control-sm" id="umsatz" placeholder="Komma ."/>
                    </div>
                    <div class="form-group">
                        <label for="bereich">Geschäftsbereich:</label>
                        <input type="text" class="form-control form-control-sm" id="bereich"
                               placeholder="Geschäftsbereich"/>
                    </div>
                    <div class="form-group">
                        <label for="jahr">Jahr:</label>
                        <input type="text" class="form-control form-control-sm" id="jahr" placeholder="yyyy"/>
                    </div>
                </form>
            </div>
            <div class='modal-footer'>
                <input type='button' id='addUmsatz' class='btn btn-success btn-sm' value='Speichern'
                       data-bs-dismiss='modal'>
                <button type='button' class='btn btn-danger btn-sm' data-bs-dismiss='modal'>Abbrechen</button>
            </div>
        </div>

    </div>
</div>

<script>
    new DataTable('#tableLieferantenUmsaetze', {
        select: true,
        paging: false,
        searching: false,
        info: false,
        order: [[3, 'desc']],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json'
        },
        scrollY: '20vh',
        scrollCollapse: true,
        columnDefs: [
            {
                targets: [0],
                visible: false,
                searchable: false
            }
        ]
    });

    //Preis zu Geraet hinzufügen
    $("#addUmsatz").click(function () {
        let umsatz = $("#umsatz").val();
        let bereich = $("#bereich").val();
        let jahr = $("#jahr").val();
        if (umsatz !== "" && bereich !== "" && jahr !== "") {
            $.ajax({
                url: "addUmsatzToLieferant.php",
                data: {"umsatz": umsatz, "bereich": bereich, "jahr": jahr},
                type: "GET",
                success: function (data) {
                    alert(data);
                    $.ajax({
                        url: "getLieferantenUmsaetze.php",
                        type: "GET",
                        success: function (data) {
                            $("#lieferantenumsaetze").html(data);
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