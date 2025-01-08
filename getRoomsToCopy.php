<?php
include '_utils.php';
check_login();

$originRoomID = $_GET["originRoomID"] ?? null;
if ($originRoomID === null) {
    echo "<p>Error: Origin Room ID is missing.</p>";
    exit;
}

$mysqli = utils_connect_sql();


// Sanitize input
$originRoomID = mysqli_real_escape_string($mysqli, $originRoomID);

// Fetch rooms, excluding the current room
$sql = "SELECT tabelle_räume.Raumnr,
            tabelle_räume.Raumbezeichnung,
            tabelle_räume.`Raumbereich Nutzer`,
            tabelle_räume.Nutzfläche,
            tabelle_räume.Geschoss,
            tabelle_räume.Bauetappe,
            tabelle_räume.Bauabschnitt,
            tabelle_räume.idTABELLE_Räume
        FROM tabelle_räume 
        INNER JOIN tabelle_projekte ON tabelle_räume.tabelle_projekte_idTABELLE_Projekte = tabelle_projekte.idTABELLE_Projekte
        WHERE ((NOT (tabelle_räume.idTABELLE_Räume) = '$originRoomID') AND ((tabelle_projekte.idTABELLE_Projekte) = '" . $_SESSION["projectID"] . "'));";

$result = $mysqli->query($sql);

echo "<table class='table table-striped table-bordered table-sm' id='tableRoomsToCopy' >
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
    echo "<td>" . $row["idTABELLE_Räume"] . "</td>";
    echo "<td>" . $row["Raumnr"] . "</td>";
    echo "<td>" . $row["Raumbezeichnung"] . "</td>";
    echo "<td>" . $row["Nutzfläche"] . "</td>";
    echo "<td>" . $row["Raumbereich Nutzer"] . "</td>";
    echo "<td>" . $row["Geschoss"] . "</td>";
    echo "<td>" . $row["Bauetappe"] . "</td>";
    echo "<td>" . $row["Bauabschnitt"] . "</td>";
    echo "</tr>";
}
echo "</tbody></table>";

$mysqli->close();
?>

<button id="selectAllRows">Select All Rows</button>
<button id="selectVisibleRows">Select Visible Rows</button>
<button id="DeselectRows">Deselect Rows</button>
<!-- input type='button' id='copyRoomElements' class='btn btn-info btn-sm' value='Elemente kopieren'>
<input type='button' id='copyBauangaben' class='btn btn-info btn-sm' value='Bauangaben kopieren' -->

<script charset="utf-8">
    var roomIDs = []; // Array to store selected room IDs
    $(document).ready(function () {
        if (typeof columnsDefinition === 'undefined') { // TO GUArantee function of old Bauanagaben page
            const script = document.createElement('script');
            script.src = 'roombookSpecifications_constDeclarations.js';
            script.onload = () => console.log('columnsDefinition loaded');
            document.head.appendChild(script);
        }

        const table2 = new DataTable("#tableRoomsToCopy", {
            select: {
                style: "multi"
            },
            columnDefs: [
                {
                    targets: [0],
                    visible: false,
                    searchable: false
                }
            ],
            paging: false,
            searching: true,
            info: false,
            order: [[1, "asc"]],
            scrollY: '50vh',
            scrollCollapse: true,
            layout: {
                top: 'search',
                bottom: null
            }
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

        //Bauangaben kopieren
        $("#copySpecifications").click(function () {
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
        $("#copyRoomElements").click(function () {
            if (roomIDs.length === 0) {
                alert("Kein Raum ausgewählt!");
            } else {
                $.ajax({
                    url: "copyRoomElements.php",
                    type: "GET",
                    data: {"rooms": roomIDs},
                    success: function (data) {
                        makeToaster(data,true);
                        $("#mbodyCRE").modal('hide');
                    }
                });
            }
        });
    });

</script>
