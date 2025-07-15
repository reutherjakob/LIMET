<?php
if (!function_exists('utils_connect_sql')) {
    include "utils/_utils.php";
}
check_login();

$originRoomID = $_GET["originRoomID"] ?? null;
if ($originRoomID === null) {
    echo "<p>Error: Origin Room ID is missing.</p>";
    exit;
}

$mysqli = utils_connect_sql(); ?>

<div class="d-inline-flex align-items-center">

    <div id="mtrelevantfilter" class="d-flex col-10">
        <button id="selectAllRows">Select All Rows</button>
        <button id="selectVisibleRows">Select Visible Rows</button>
        <button id="DeselectRows">Deselect Rows</button>
    </div>
    <div id="Subdiv1" class="d-flex col-2"></div>
</div>

<?php
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
            tabelle_räume.idTABELLE_Räume,
            tabelle_räume.`MT-relevant`
        FROM tabelle_räume 
        INNER JOIN tabelle_projekte ON tabelle_räume.tabelle_projekte_idTABELLE_Projekte = tabelle_projekte.idTABELLE_Projekte
        WHERE ((NOT (tabelle_räume.idTABELLE_Räume) = '$originRoomID') AND ((tabelle_projekte.idTABELLE_Projekte) = '" . $_SESSION["projectID"] . "'));";

$result = $mysqli->query($sql);

echo "<table class='table table-striped table-bordered table-sm table-hover border border-light border-5' id='tableRoomsToCopy' >
    <thead><tr>
    <th>ID</th>   
     <th>MT-rel.</th>
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
    echo "<td>" . $row["MT-relevant"] . "</td>";
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


<!-- input type='button' id='copyRoomElements' class='btn btn-info btn-sm' value='Elemente kopieren'>
<input type='button' id='copyBauangaben' class='btn btn-info btn-sm' value='Bauangaben kopieren' -->

<script charset="utf-8">

    // ALL THE COMMENTED PARTS ARE NOW WITHIN THE FILE IMPORTING THIS GetRoomsToCopy
   var roomIDs = []; // Array to store selected room IDs
    // //Bauangaben kopieren

    //
    // //Rauminhalt kopieren
    // $("#copyRoomElements").click(function () {
    //     console.log(roomIDs);
    //     roomIDs = [...new Set(roomIDs)];
    //     console.log("Letzte log vorm kopieren", roomIDs);
    //     if (roomIDs.length === 0) {
    //         alert("Kein Raum ausgewählt!");
    //     } else {
    //         $.ajax({
    //             url: "copyRoomElements.php",
    //             type: "GET",
    //             data: {"rooms": roomIDs},
    //             success: function (data) {
    //                 makeToaster(data, true);
    //                 $("#mbodyCRE").modal('hide');
    //             }
    //         });
    //     }
    // });
    var tableRoomsToCopy;

    function add_MT_rel_filter(location, table) {
        let dropdownHtml = '<select class=" fix_size" id="columnFilter2">' + '<option value=" ">MT</option><option value="1">Ja</option>' + '<option value="0">Nein</option></select>';
        $(location).append(dropdownHtml);
        $('#columnFilter2').change(function () {
            let filterValue = $(this).val();
            table.column(1).search(filterValue).draw();

        });
        console.log(table.column(0));
    }

    $(document).ready(function () {


        if (typeof columnsDefinition === 'undefined') { // TO GUArantee function of old Bauanagaben page
            const script = document.createElement('script');
            script.src = 'roombookSpecifications_constDeclarations.js';
            //script.onload = () => console.log('columnsDefinition loaded');
            document.head.appendChild(script);
        }

        tableRoomsToCopy = new DataTable("#tableRoomsToCopy", {
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
                topEnd: 'search',
                bottomEnd: null
            },
            responsive: true,
            initComplete: function () {
                $('.dt-search label').remove();
                $('#tableRoomsToCopy_wrapper .dt-search').children().removeClass('form-control form-control-sm').appendTo('#Subdiv1');


            }
        });

        $('#tableRoomsToCopy tbody').on('click', 'tr', function () {
            $(this).toggleClass('selected');
            let id = tableRoomsToCopy.row($(this)).data()[0];
            if ($(this).hasClass('selected')) {
                roomIDs.push(id);
            } else {
                roomIDs = roomIDs.filter(function (value) {
                    return value !== id;
                });
            }

        });

        $("#selectAllRows").click(function () {
            tableRoomsToCopy.rows().select();
            roomIDs = tableRoomsToCopy.rows().data().toArray().map(function (row) {
                return row[0];
            });
            console.log("Select All Rows ", roomIDs);
        });

        $("#selectVisibleRows").click(function () {
            tableRoomsToCopy.rows({search: 'applied'}).select();
            roomIDs = tableRoomsToCopy.rows({search: 'applied'}).data().toArray().map(function (row) {
                return row[0];
            });
            console.log("Select Visible Rows ", roomIDs);
        });
        $("#DeselectRows").click(function () {
            tableRoomsToCopy.rows().deselect();
            roomIDs = [];
            console.log("Deselect All Rows ", roomIDs);
        });


        add_MT_rel_filter('#mtrelevantfilter', tableRoomsToCopy);
    });

</script>
