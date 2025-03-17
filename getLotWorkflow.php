<!DOCTYPE html >
<html xmlns="http://www.w3.org/1999/xhtml" lang="">
<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <title></title>
</head>
<body>
<?php
session_start();
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

$sql = "SELECT tabelle_lot_workflow.tabelle_lose_extern_idtabelle_Lose_Extern,
       tabelle_workflow_has_tabelle_wofklowteil.Reihenfolgennummer, 
       tabelle_workflowteil.aufgabe, 
       tabelle_lot_workflow.tabelle_wofklowteil_idtabelle_wofklowteil, 
       tabelle_lot_workflow.Timestamp_Ist, 
       tabelle_lot_workflow.Timestamp_Soll, 
       tabelle_lot_workflow.Abgeschlossen, 
       tabelle_lot_workflow.user, 
       tabelle_lot_workflow.Kommentar
    FROM (tabelle_workflow_has_tabelle_wofklowteil INNER JOIN tabelle_workflowteil ON tabelle_workflow_has_tabelle_wofklowteil.tabelle_wofklowteil_idtabelle_wofklowteil = tabelle_workflowteil.idtabelle_wofklowteil) INNER JOIN tabelle_lot_workflow ON (tabelle_workflow_has_tabelle_wofklowteil.tabelle_workflow_idtabelle_workflow = tabelle_lot_workflow.tabelle_workflow_idtabelle_workflow) AND (tabelle_workflow_has_tabelle_wofklowteil.tabelle_wofklowteil_idtabelle_wofklowteil = tabelle_lot_workflow.tabelle_wofklowteil_idtabelle_wofklowteil)
    WHERE (((tabelle_lot_workflow.tabelle_lose_extern_idtabelle_Lose_Extern)=" . $_SESSION["lotID"] . "))
    ORDER BY tabelle_workflow_has_tabelle_wofklowteil.Reihenfolgennummer";

$result = $mysqli->query($sql);
if ($result->num_rows == 0) {
    echo "<button type='button' class='btn btn-outline-dark btn-sm btn-default' value='addWorkflowToLot'>Workflow hinzufügen</button>";
} else {
    echo "<table class='table table-sm' id='tableWorkflow'  >
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
        echo "<td><button type='button' id='" . $row["tabelle_wofklowteil_idtabelle_wofklowteil"] . "' class='btn btn-sm btn-warning' value='saveWorkflowteil'><i class='far fa-save'></i></button></td>";
        echo "</tr>";


    }
    echo "</tbody></table>";
}
$mysqli->close();
?>

<script>

    new DataTable('#tableWorkflow', {
        paging: false,
        searching: false,
        info: false,
        order: [[0, "asc"]],
        columnDefs: [
            {
                targets: [2, 3, 4, 5, 6],
                visible: true,
                searchable: false,
                orderable: false
            }
        ],
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json"
        },
        layout: {
            topStart: null,
            topEnd: null,
            bottomStart: null,
            bottomEnd: null
        }
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
        let workflowID = this.id;
        let date_Is = $("#workflowDateIs" + workflowID).val();
        let date_Should = $("#workflowDateShould" + workflowID).val();
        let comment = $("#workflowComment" + workflowID).val();
        let status = ($("#workflowStatus" + workflowID).prop('checked') === true) ? 1 : 0;

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