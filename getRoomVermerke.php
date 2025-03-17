<?php
if (!function_exists('utils_connect_sql')) {  include "_utils.php"; }
check_login();
?>

<!DOCTYPE html>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
<html>
<head>
    <style>


    </style>
</head>
<body>
<?php

$mysqli = utils_connect_sql();


$sql = "SELECT tabelle_Vermerkgruppe.Gruppenname, tabelle_Vermerkgruppe.Gruppenart, tabelle_Vermerkgruppe.Ort, tabelle_Vermerkgruppe.Datum, tabelle_lose_extern.LosNr_Extern, tabelle_lose_extern.LosBezeichnung_Extern, tabelle_ansprechpersonen.Name, tabelle_ansprechpersonen.Vorname, tabelle_Vermerke.Faelligkeit, tabelle_Vermerke.Vermerkart, tabelle_Vermerke.Bearbeitungsstatus, tabelle_Vermerke.Vermerktext, tabelle_Vermerke.Erstellungszeit, tabelle_Vermerke.idtabelle_Vermerke
                FROM (((tabelle_Vermerke LEFT JOIN (tabelle_ansprechpersonen RIGHT JOIN tabelle_Vermerke_has_tabelle_ansprechpersonen ON tabelle_ansprechpersonen.idTABELLE_Ansprechpersonen = tabelle_Vermerke_has_tabelle_ansprechpersonen.tabelle_ansprechpersonen_idTABELLE_Ansprechpersonen) ON tabelle_Vermerke.idtabelle_Vermerke = tabelle_Vermerke_has_tabelle_ansprechpersonen.tabelle_Vermerke_idtabelle_Vermerke) INNER JOIN (tabelle_Vermerkgruppe INNER JOIN tabelle_Vermerkuntergruppe ON tabelle_Vermerkgruppe.idtabelle_Vermerkgruppe = tabelle_Vermerkuntergruppe.tabelle_Vermerkgruppe_idtabelle_Vermerkgruppe) ON tabelle_Vermerke.tabelle_Vermerkuntergruppe_idtabelle_Vermerkuntergruppe = tabelle_Vermerkuntergruppe.idtabelle_Vermerkuntergruppe) LEFT JOIN tabelle_räume ON tabelle_Vermerke.tabelle_räume_idTABELLE_Räume = tabelle_räume.idTABELLE_Räume) LEFT JOIN tabelle_lose_extern ON tabelle_Vermerke.tabelle_lose_extern_idtabelle_Lose_Extern = tabelle_lose_extern.idtabelle_Lose_Extern
                WHERE (((tabelle_Vermerkgruppe.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . ") AND ((tabelle_Vermerke.tabelle_räume_idTABELLE_Räume)=" . $_SESSION["roomID"] . "))
                ORDER BY tabelle_Vermerkgruppe.Datum DESC , tabelle_Vermerke.Erstellungszeit DESC;";

$result = $mysqli->query($sql);

echo "<div class='table-responsive'><table class='table table-striped table-bordered table-sm table-hover border border-light border-5' id='tableRoomVermerke'  >
	<thead><tr>
	<th>ID</th> 
        <th>Art</th>
        <th>Name</th>	
        <th>Status</th>
        <th>Datum</th>
        <th>Vermerk</th>
	<th>Typ</th>
	<th>Zuständig</th>
	<th>Fälligkeit</th>        
	<th>Los</th>	        
        <th>Status</th>        
	</tr></thead><tbody>";


while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row["idtabelle_Vermerke"] . "</td>";
    echo "<td>" . $row["Gruppenart"] . "</td>";
    echo "<td>" . $row["Gruppenname"] . "</td>";
    echo "<td>";
    if ($row["Vermerkart"] != "Info") {
        if ($row["Bearbeitungsstatus"] == "0") {
            echo "<div class='form-check form-check-inline'><label class='form-check-label' for='" . $row["idtabelle_Vermerke"] . "'><input type='checkbox' class='form-check-input' id='" . $row["idtabelle_Vermerke"] . "' value='statusCheck'></label></div>";
        } else {
            echo "<div class='form-check form-check-inline'><label class='form-check-label' for='" . $row["idtabelle_Vermerke"] . "'><input type='checkbox' class='form-check-input' id='" . $row["idtabelle_Vermerke"] . "' value='statusCheck' checked='true'></label></div>";
        }
    }
    echo "</td>";
    echo "<td>" . $row["Datum"] . "</td>";
    echo "<td><button type='button' class='btn btn-sm btn-outline-dark' data-bs-toggle='popover' title='Vermerk' data-placement='left' data-bs-content='" . $row["Vermerktext"] . "'><i class='fa fa-comment'></i></button></td>";
    echo "<td>" . $row["Vermerkart"] . "</td>";
    echo "<td>" . $row["Name"] . "</td>";
    echo "<td>";
    if ($row["Vermerkart"] != "Info") {
        echo $row["Faelligkeit"];
    }
    echo "</td>";
    echo "<td>" . $row["LosNr_Extern"] . "</td>";
    echo "<td>" . $row["Bearbeitungsstatus"] . "</td>";

    echo "</tr>";
}
echo "</tbody></table></div>";
$mysqli->close();
?>
<script>

    $(document).ready(function () {
        $('#tableRoomVermerke').DataTable({
            "columnDefs": [
                {
                    "targets": [0, 6, 9, 10],
                    "visible": false,
                    "searchable": false
                }
            ],
            "paging": true,
            "pagingType": "simple",
            "lengthChange": false,
            "pageLength": 10,
            "searching": true,
            "info": true,
            "order": [[4, "desc"]],
            'language': {'url': 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json'},
            "fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                //Farbe bestimmen
                if (aData[6] === "Bearbeitung") {
                    if (aData[10] === "0") {
                        $('td', nRow).css('background-color', '#ff8080');
                    } else {
                        $('td', nRow).css('background-color', '#b8dc6f');
                    }
                } else {

                    $('td', nRow).css('background-color', '#d3edf8');
                }
            }
        });


        $("input[value='statusCheck']").change(function () {

            if ($(this).prop('checked') === true) {
                var vermerkStatus = 1;
            } else {
                var vermerkStatus = 0;
            }
            var vermerkID = this.id;

            if (vermerkStatus !== "" && vermerkID !== "") {
                $.ajax({
                    url: "saveVermerkStatus.php",
                    data: {"vermerkID": vermerkID, "vermerkStatus": vermerkStatus},
                    type: "GET",
                    success: function (data) {
                        alert(data);
                        $.ajax({
                            url: "getRoomVermerke.php",
                            type: "GET",
                            success: function (data) {
                                $("#roomVermerke").html(data);
                            }
                        });
                    }
                });
            } else {
                alert("Vermerkstatus nicht lesbar!");
            }

        });
        // Popover for Vermerk	TODO
        $(function () {
            $('[data-bs-toggle="popover"]').popover();
        });


    });


</script>

</body>
</html>