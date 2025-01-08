<?php
session_start();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN""http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
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

if (filter_input(INPUT_GET, 'lotID') != "") {
    $_SESSION["lotID"] = filter_input(INPUT_GET, 'lotID');
}

$sql = "SELECT tabelle_lot_workflow.tabelle_lose_extern_idtabelle_Lose_Extern, tabelle_workflow_has_tabelle_wofklowteil.Reihenfolgennummer, tabelle_workflowteil.aufgabe, tabelle_lot_workflow.tabelle_wofklowteil_idtabelle_wofklowteil, tabelle_lot_workflow.Timestamp_Ist, tabelle_lot_workflow.Timestamp_Soll, tabelle_lot_workflow.Abgeschlossen, tabelle_lot_workflow.user, tabelle_lot_workflow.Kommentar
                FROM (tabelle_workflow_has_tabelle_wofklowteil INNER JOIN tabelle_workflowteil ON tabelle_workflow_has_tabelle_wofklowteil.tabelle_wofklowteil_idtabelle_wofklowteil = tabelle_workflowteil.idtabelle_wofklowteil) INNER JOIN tabelle_lot_workflow ON (tabelle_workflow_has_tabelle_wofklowteil.tabelle_workflow_idtabelle_workflow = tabelle_lot_workflow.tabelle_workflow_idtabelle_workflow) AND (tabelle_workflow_has_tabelle_wofklowteil.tabelle_wofklowteil_idtabelle_wofklowteil = tabelle_lot_workflow.tabelle_wofklowteil_idtabelle_wofklowteil)
                WHERE (((tabelle_lot_workflow.tabelle_lose_extern_idtabelle_Lose_Extern)=" . $_SESSION["lotID"] . "))
                ORDER BY tabelle_workflow_has_tabelle_wofklowteil.Reihenfolgennummer;";

$result = $mysqli->query($sql);
if ($result->num_rows == 0) {
    echo "<button type='button' class='btn btn-outline-dark btn-sm btn-default' value='addWorkflowToLot'>Workflow hinzufügen</button>";
} else {
    echo "<table class='table table-sm' id='tableWorkflow' cellspacing='0' width='100%'>
                <thead><tr>
                <th>Nr</th>
                <th>Aufgabe</th>
                <th>Status</th>	
                <th>Datum-Soll</th>
                <th>Datum-Ist</th>
                <th>Anmerkung</th>
                <th>Benutzer</th>
                <th></th>
                </tr></thead>
                <tbody>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row["Reihenfolgennummer"] . "</td>";
        echo "<td>" . $row["aufgabe"] . "</td>";
        echo "<td><div class='form-check'>";
        if ($row["Abgeschlossen"] == "0") {
            echo "<input type='checkbox' class='form-check-input' id='workflowStatus" . $row["tabelle_wofklowteil_idtabelle_wofklowteil"] . "' value='workflowStatus'>";
        } else {
            echo "<input type='checkbox' class='form-check-input' id='workflowStatus" . $row["tabelle_wofklowteil_idtabelle_wofklowteil"] . "' value='workflowStatus' checked='true'>";
        }
        echo "</div></td>";
        echo "<td>";
        if ($row["Abgeschlossen"] == "0") {
            echo "<input class='form-control form-control-sm' type='date' id='workflowDateShould" . $row["tabelle_wofklowteil_idtabelle_wofklowteil"] . "' value=" . $row["Timestamp_Soll"] . "></input>";
        } else {
            echo "<input class='form-control form-control-sm' type='date' id='workflowDateShould" . $row["tabelle_wofklowteil_idtabelle_wofklowteil"] . "' value=" . $row["Timestamp_Soll"] . " disabled ></input>";
        }
        echo "</td>";
        echo "<td>";
        if ($row["Abgeschlossen"] == "0") {
            echo "<input class='form-control form-control-sm' type='date' id='workflowDateIs" . $row["tabelle_wofklowteil_idtabelle_wofklowteil"] . "' value=" . $row["Timestamp_Ist"] . "></input>";
        } else {
            echo "<input class='form-control form-control-sm' type='date' id='workflowDateIs" . $row["tabelle_wofklowteil_idtabelle_wofklowteil"] . "' value=" . $row["Timestamp_Ist"] . " disabled ></input>";
        }
        echo "</td>";
        echo "<td>";
        if ($row["Abgeschlossen"] == "0") {
            echo "<textarea class='form-control form-control-sm' rows='1' id='workflowComment" . $row["tabelle_wofklowteil_idtabelle_wofklowteil"] . "' placeholder='Anmerkungen'>" . $row["Kommentar"] . "</textarea>";
        } else {
            echo "<textarea class='form-control form-control-sm' rows='1' id='workflowComment" . $row["tabelle_wofklowteil_idtabelle_wofklowteil"] . "' placeholder='Anmerkungen' disabled>" . $row["Kommentar"] . "</textarea>";
        }
        echo "</td>";
        echo "<td>" . $row["user"] . "</td>";
        echo "<td><button type='button' id='" . $row["tabelle_wofklowteil_idtabelle_wofklowteil"] . "' class='btn btn-xs btn-default' value='saveWorkflowteil'><i class='far fa-save'></i></button></td>";
        echo "</tr>";


    }
    echo "</tbody></table>";
}
$mysqli->close();
?>

<script>


    $("#tableWorkflow").DataTable({
        "paging": false,
        "searching": false,
        "info": false,
        "order": [[0, "asc"]],
        "columnDefs": [
            {
                "targets": [2, 3, 4, 5, 6],
                "visible": true,
                "searchable": false,
                "sortable": false
            }
        ],
        //"pagingType": "simple_numbers",
        //"lengthMenu": [[5,10, 25, 50, -1], [5,10, 25, 50, "All"]],
        "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"}
    });


    $("input[value='workflowStatus']").change(function () {
        var workflowID = this.id.substr(14, 10);
        if ($(this).prop('checked') === true) {
            //Sperren
            document.getElementById("workflowDate" + workflowID).disabled = true;
            document.getElementById("workflowComment" + workflowID).disabled = true;
        } else {
            //Freigeben
            document.getElementById("workflowDate" + workflowID).disabled = false;
            document.getElementById("workflowComment" + workflowID).disabled = false;
        }
    });

    // Workfloweintrag speichern
    $("button[value='saveWorkflowteil']").click(function () {
        var workflowID = this.id;
        var date_Is = $("#workflowDateIs" + workflowID).val();
        var date_Should = $("#workflowDateShould" + workflowID).val();
        var comment = $("#workflowComment" + workflowID).val();
        var status = ($("#workflowStatus" + workflowID).prop('checked') === true) ? 1 : 0;

        if (workflowID === "") {
            alert("Keinen Workflowteilgefunden!");
        } else {
            $.ajax({
                url: "saveWorkflowPart.php",
                data: {
                    "workflowID": workflowID,
                    "dateIs": date_Is,
                    "dateShould": date_Should,
                    "comment": comment,
                    "status": status
                },
                type: "GET",
                success: function (data) {
                    $("#infoBody").html(data);
                    $('#infoModal').modal('show');
                }
            });
        }
    });

    // Workfloweintrag speichern
    $("button[value='addWorkflowToLot']").click(function () {
        $.ajax({
            url: "getProjectWorkflows.php",
            type: "GET",
            success: function (data) {
                $("#workflowModalBody").html(data);
            }
        });
    });

</script>

</body>
</html>