<?php
session_start();
?>

<!DOCTYPE html>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
<html>
<head>
</head>
<body>
<?php
if (!isset($_SESSION["username"])) {
    echo "Bitte erst <a href=\"index.php\">einloggen</a>";
    exit;
}
?>

<?php
$mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');


/* change character set to utf8 */
if (!$mysqli->set_charset("utf8")) {
    printf("Error loading character set utf8: %s\n", $mysqli->error);
    exit();
}

if ($_GET["lotID"] != "") {
    $_SESSION["lotID"] = $_GET["lotID"];
}

$sql = "SELECT Sum(`Anzahl`*`Kosten`) AS proxSum
                FROM tabelle_projekt_varianten_kosten INNER JOIN tabelle_räume_has_tabelle_elemente ON (tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) AND (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente)
                WHERE (((tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern)=" . $_GET["lotID"] . ") AND ((tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . ") AND ((tabelle_räume_has_tabelle_elemente.`Neu/Bestand`)=1));";

$result1 = $mysqli->query($sql);
$row1 = $result1->fetch_assoc();
$proxSum = $row1["proxSum"];

// Abfrage der möglichen Lieferanten
$sql = "SELECT `tabelle_lieferant`.`idTABELLE_Lieferant`,
                `tabelle_lieferant`.`Lieferant`
            FROM `LIMET_RB`.`tabelle_lieferant`
            ORDER BY `Lieferant`;";

$result = $mysqli->query($sql);

$possibleAuftragnehmer = array();
while ($row = $result->fetch_assoc()) {
    $possibleAuftragnehmer[$row['idTABELLE_Lieferant']]['idTABELLE_Lieferant'] = $row['idTABELLE_Lieferant'];
    $possibleAuftragnehmer[$row['idTABELLE_Lieferant']]['Lieferant'] = $row['Lieferant'];
}

$sql = "SELECT tabelle_lose_extern.LosNr_Extern, tabelle_lose_extern.LosBezeichnung_Extern, tabelle_lose_extern.Versand_LV , tabelle_lose_extern.Verfahren , tabelle_lose_extern.Bearbeiter, tabelle_lose_extern.tabelle_lieferant_idTABELLE_Lieferant, tabelle_lose_extern.Ausführungsbeginn, tabelle_lose_extern.Vergabesumme, tabelle_lose_extern.Vergabe_abgeschlossen, tabelle_lose_extern.Versand_LV, tabelle_lose_extern.Notiz, tabelle_lieferant.Lieferant
            FROM tabelle_lieferant RIGHT JOIN tabelle_lose_extern ON tabelle_lieferant.idTABELLE_Lieferant = tabelle_lose_extern.tabelle_lieferant_idTABELLE_Lieferant
            WHERE (((tabelle_lose_extern.idtabelle_Lose_Extern)=" . $_GET["lotID"] . "));";

$result = $mysqli->query($sql);
$row = $result->fetch_assoc();

echo "<form role='form'>        			        			        		
		  <div class='form-group'>
		    <label for='lotNr'>Losnummer:</label>
		    <input type='text' class='form-control' id='lotNr' placeholder='Losnummer' value='" . $row["LosNr_Extern"] . "'/>
		  </div>
		  <div class='form-group'>
		    <label for='lotName'>Bezeichnung:</label>
		    <input type='text' class='form-control' id='lotName' placeholder='Losbezeichnung'/ value='" . $row["LosBezeichnung_Extern"] . "'>
		  </div>
                  <div class='form-group'>
		    <label for='lotLVSend'>Versand LV:</label>
		    <input type='text' class='form-control' id='lotLVSend' placeholder='jjjj-mm-tt'/ value='" . $row["Versand_LV"] . "'>
		  </div>
		  <div class='form-group'>
		    <label for='lotStart'>Ausführungsbeginn:</label>
		    <input type='text' class='form-control' id='lotStart' placeholder='jjjj-mm-tt'/ value='" . $row["Ausführungsbeginn"] . "'>
		  </div>
                  <div class='form-group'>
		    <label for='lotVerfahren'>Verfahren:</label>
		    <input type='text' class='form-control' id='lotVerfahren' placeholder='Verfahren'/ value='" . $row["Verfahren"] . "'>
		  </div>
                  <div class='form-group'>
		    <label for='lotLVBearbeiter'>Bearbeiter:</label>
		    <input type='text' class='form-control' id='lotLVBearbeiter' placeholder='Bearbeiter'/ value='" . $row["Bearbeiter"] . "'>
		  </div>
		  <div class='form-group'>
		    <label for='lotSum'>Vergabesumme: (.)</label>
		    <input type='text'  class='form-control' id='lotSum' placeholder='Vergabesumme'/ value='" . $row["Vergabesumme"] . "'>
		  </div>
                  <div class='form-group'>
		    <label for='lotProxSum'>Schätzsumme von Neu-Elementen:</label>
		    <input type='text'  class='form-control' id='lotProxSum' disabled='disabled' placeholder='Schätzsumme'/ value='" . $proxSum . "'>
		  </div>
		  <div class='form-group'>
		    <label for='lotVergabe'>Vergabe abgeschlossen:</label>
		    	<select class='form-control input-sm' id='lotVergabe'>";
if ($row["Vergabe_abgeschlossen"] == 0) {
    echo "<option value='0' selected>Nein</option>
					  		<option value='1'>Ja</option>";
} else {
    echo "<option value='0'>Nein</option>
					  		<option value='1' selected>Ja</option>";
}
echo "</select>						
		   </div>
                   <div class='form-group'>
                        <label for='lotAuftragnehmer'>Auftragnehmer:</label>
                            <select class='form-control input-sm' id='lotAuftragnehmer'>";
if ($row["tabelle_lieferant_idTABELLE_Lieferant"] != "") {
    echo "<option value=0>Auftragnehmer wählen</option>";
    foreach ($possibleAuftragnehmer as $array) {
        if ($array['idTABELLE_Lieferant'] == $row["tabelle_lieferant_idTABELLE_Lieferant"]) {
            echo "<option selected value=" . $array['idTABELLE_Lieferant'] . ">" . $array['Lieferant'] . "</option>";
        } else {
            echo "<option value=" . $array['idTABELLE_Lieferant'] . ">" . $array['Lieferant'] . "</option>";
        }
    }
} else {
    echo "<option value=0 selected>Auftragnehmer wählen</option>";
    foreach ($possibleAuftragnehmer as $array) {
        echo "<option value=" . $array['idTABELLE_Lieferant'] . ">" . $array['Lieferant'] . "</option>";
    }
}
echo "</select>						
                    </div>
		   <div class='form-group'>
		    <label for='lotNotice'>Notiz:</label>
		    <textarea class='form-control' rows='5' id='lotNotice' placeholder='Notiz'>" . $row["Notiz"] . "</textarea>
		  </div>
		  <input type='button' id='saveLot' class='btn btn-warning btn-sm' value='Los speichern'></input>
		  <input type='button' id='addLot' class='btn btn-success btn-sm' value='Los Hinzufügen'></input>	
	</form>";


$mysqli->close();
?>


<script>

    $('#lotLVSend').datepicker({
        format: "yyyy-mm-dd",
        calendarWeeks: true,
        autoclose: true,
        todayBtn: "linked"
    });

    $('#lotStart').datepicker({
        format: "yyyy-mm-dd",
        calendarWeeks: true,
        autoclose: true,
        todayBtn: "linked"
    });

    //Los hinzufügen
    $("#addLot").click(function () {
        var losNr = $("#lotNr").val();
        var losName = $("#lotName").val();
        var losDatum = $("#lotStart").val();
        var lotSum = $("#lotSum").val();
        var lotVergabe = $("#lotVergabe").val();
        var lotNotice = $("#lotNotice").val();
        var lotAuftragnehmer = $("#lotAuftragnehmer").val();
        var lotLVSend = $("#lotLVSend").val();
        var lotVerfahren = $("#lotVerfahren").val();
        var lotLVBearbeiter = $("#lotLVBearbeiter").val();

        if (losNr !== "" && losName !== "" && losDatum !== "" && lotLVSend !== "" && lotVerfahren !== "" && lotLVBearbeiter !== "") {
            $.ajax({
                url: "addTenderLot.php",
                data: {
                    "losNr": losNr,
                    "losName": losName,
                    "losDatum": losDatum,
                    "lotSum": lotSum,
                    "lotVergabe": lotVergabe,
                    "lotNotice": lotNotice,
                    "lotAuftragnehmer": lotAuftragnehmer,
                    "lotLVSend": lotLVSend,
                    "lotVerfahren": lotVerfahren,
                    "lotLVBearbeiter": lotLVBearbeiter
                },
                type: "GET",
                success: function (data) {
                    alert(data);
                    $.ajax({
                        url: "getProjectTenderLots.php",
                        type: "GET",
                        success: function (data) {
                            $("#projectLots").html(data);
                        }
                    });

                }
            });

        } else {
            alert("Bitte alle Felder außer der Vergabesumme/Auftragnehmer ausfüllen!");
        }
    });

    //Los speichern
    $("#saveLot").click(function () {
        let losNr = $("#lotNr").val();
        let losName = $("#lotName").val();
        let losDatum = $("#lotStart").val();
        let lotSum = $("#lotSum").val();
        let lotVergabe = $("#lotVergabe").val();
        let lotNotice = $("#lotNotice").val();
        let lotAuftragnehmer = $("#lotAuftragnehmer").val();
        let lotLVSend = $("#lotLVSend").val();
        let lotVerfahren = $("#lotVerfahren").val();
        let lotLVBearbeiter = $("#lotLVBearbeiter").val();


        if (losNr !== "" && losName !== "" && losDatum !== "" && lotLVSend !== "" && lotVerfahren !== "" && lotLVBearbeiter !== "") {
            $.ajax({
                url: "setTenderLot.php",
                data: {
                    "losNr": losNr,
                    "losName": losName,
                    "losDatum": losDatum,
                    "lotSum": lotSum,
                    "lotVergabe": lotVergabe,
                    "lotNotice": lotNotice,
                    "lotAuftragnehmer": lotAuftragnehmer,
                    "lotLVSend": lotLVSend,
                    "lotVerfahren": lotVerfahren,
                    "lotLVBearbeiter": lotLVBearbeiter
                },
                type: "GET",
                success: function (data) {
                    alert(data);
                    var searchVal = $('div.dataTables_filter input').val();
                    $.ajax({
                        url: "getProjectTenderLots.php",
                        data: {"searchValue": searchVal},
                        type: "GET",
                        success: function (data) {
                            $("#projectLots").html(data);
                        }
                    });

                }
            });

        } else {
            alert("Bitte alle Felder außer der Vergabesumme/Auftragnehmer ausfüllen!");
        }
    });

</script>

</body>
</html>