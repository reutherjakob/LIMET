<?php
session_start();
include '_utils.php';
//check_login();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    </head>
    <body>

        <button id="selectAllRows">Select All Rows</button>
        <button id="selectVisibleRows">Select Visible Rows</button>
        <button id="DeselectRows">Deselect Rows</button>


        <?php
        $mysqli = utils_connect_sql();
        //Elemente im Raum abfragen
        $sql = "SELECT tabelle_räume.Raumnr,
            tabelle_räume.Raumbezeichnung,
            tabelle_räume.`Raumbereich Nutzer`,
            tabelle_räume.Nutzfläche,
            tabelle_räume.Geschoss,
            tabelle_räume.Bauetappe,
            tabelle_räume.Bauabschnitt,
            tabelle_räume.idTABELLE_Räume
            FROM tabelle_räume INNER JOIN tabelle_projekte ON tabelle_räume.tabelle_projekte_idTABELLE_Projekte = tabelle_projekte.idTABELLE_Projekte
            WHERE ((Not (tabelle_räume.idTABELLE_Räume)=" . $_GET["id"] . ") AND ((tabelle_projekte.idTABELLE_Projekte)=" . $_SESSION["projectID"] . "));";
        $result = $mysqli->query($sql);
        $mysqli->close();

        echo "<table class='table table-striped table-bordered table-sm' id='tableRoomsToCopy' cellspacing='0' width='100%'>
	<thead><tr>
	<th>ID</th>
	<th>Raumnr</th>
	<th>Raumbezeichnung</th>
	<th>Nutzfläche</th>
	<th>Raumbereich Nutzer</th>
	<th>Geschoss</th>
	<th>Bauetappe</th>
	<th>Bauteil</th>
	</tr></thead> 
	<tbody>";

        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td >" . $row["idTABELLE_Räume"] . "</td>";
            echo "<td >" . $row["Raumnr"] . "</td>";
            echo "<td >" . $row["Raumbezeichnung"] . "</td>";
            echo "<td >" . $row["Nutzfläche"] . "</td>";
            echo "<td >" . $row["Raumbereich Nutzer"] . "</td>";
            echo "<td >" . $row["Geschoss"] . "</td>";
            echo "<td >" . $row["Bauetappe"] . "</td>";
            echo "<td >" . $row["Bauabschnitt"] . "</td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
        ?>


        <script>
            var roomIDs = [];
            $(document).ready(function () {
                console.log("getRooms2Copy.php Document Ready function");
                var table2 = $("#tableRoomsToCopy").DataTable({
                    "select": {
                        "style": "multi"
                    },
                    "columnDefs": [
                        {
                            "targets": [0],
                            "visible": false,
                            "searchable": false
                        }
                    ],
                    "paging": false,
                    "searching": true,
                    "info": false,
                    "order": [[1, "asc"]],
                    "scrollY": '50vh',
                    "scrollCollapse": true
                });

                $('#tableRoomsToCopy tbody').on('click', 'tr', function () {
                    $(this).toggleClass('selected');
                    var id = table2.row($(this)).data()[0];
                    if ($(this).hasClass('selected')) {
                        roomIDs.push(id);
                    } else {
                        roomIDs = roomIDs.filter(function (value) {
                            return value !== id;
                        });
                    }
                    console.log("Tbody Click ", roomIDs);
                });

                $("#selectAllRows").click(function () {
                    table2.rows().select();
                    roomIDs = table2.rows().data().toArray().map(function (row) {
                        return row[0];
                    });
                    console.log("Select All Rows ", roomIDs);
                });

                $("#selectVisibleRows").click(function () {
                    table2.rows({search: 'applied'}).select();
                    roomIDs = table2.rows({search: 'applied'}).data().toArray().map(function (row) {
                        return row[0];
                    });
                    console.log("Select Visible Rows ", roomIDs);
                });
                $("#DeselectRows").click(function () {
                    table2.rows().deselect();
                    roomIDs = [];
                    console.log("Deselect All Rows ", roomIDs);
                });

            });

            //Bauangaben kopieren
            $("input[value='Bauangaben kopieren']").click(function () {
                console.log("getRoomsToCopy.php -> Bauang. Kopieren Btn. IDS:", roomIDs);
                if (roomIDs.length === 0) {
                    alert("Kein Raum ausgewählt!");
                } else {
                    $.ajax({
                        url: "copyRoomSpecifications_1.php",
                        type: "POST",
                        data: {
                            rooms: JSON.stringify(roomIDs),
                            columns: JSON.stringify(columnsDefinition)
                        },
                        success: function (data) {
                            console.log(data);
                            alert(data);
                            location.reload(true); // if(confirm("Raum erfolgreich Aktualisiert! :) \nUm Änderungen anzuzeigen, muss Seite Neu laden. Jetzt neu laden? \n",data)) { location.reload(true);}               
                        }
                    });
                }
            });

            //Rauminhalt kopieren
            $("input[value='Elemente kopieren']").click(function () {
                if (roomIDs.length === 0) {
                    alert("Kein Raum ausgewählt!");
                } else {
                    $.ajax({
                        url: "copyRoomElements.php",
                        type: "GET",
                        data: {"rooms": roomIDs},
                        success: function (data) {
                            alert(data);
                        }
                    });
                }
            });

        </script>
    </body>
</html>
