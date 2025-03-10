<?php
if (!function_exists('utils_connect_sql')) {  include "_utils.php"; }
check_login();
?>

<!DOCTYPE html >
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
<head>
    <title>GetRoomsWithElementsAndBestand</title>
</head>
<body>

<?php
$mysqli = utils_connect_sql();

$sql = "SELECT tabelle_räume_has_tabelle_elemente.id, tabelle_räume.idTABELLE_Räume, tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung, tabelle_räume.`Raumbereich Nutzer`, tabelle_räume_has_tabelle_elemente.Anzahl, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, tabelle_räume_has_tabelle_elemente.Standort, tabelle_räume_has_tabelle_elemente.Verwendung, tabelle_räume_has_tabelle_elemente.Kurzbeschreibung, tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten
			FROM tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume
			WHERE ( ((tabelle_räume_has_tabelle_elemente.`Neu/Bestand`)=" . filter_input(INPUT_GET, 'bestand') . ") AND ((tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten)=" . filter_input(INPUT_GET, 'variantenID') . ") AND ((tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente)=" . filter_input(INPUT_GET, 'elementID') . ") AND ((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . "))
			ORDER BY tabelle_räume.Raumnr;";

$result = $mysqli->query($sql);
echo "<table class='table table-striped table-bordered table-sm table-hover border border-light border-5' id='tableRoomsWithElement' >
	<thead><tr>
        <th>ID</th>
	<th>Anzahl</th>
	<th>Variante</th>
	<th>Raumnummer</th>
	<th>Raumbezeichnung</th>
	<th>Raumbereich</th>
	<th>Bestand</th>
	<th>Standort</th>
	<th>Verwendung</th>
	<th></th>
        <th></th>
        <th>Excel Variante</th>
        <th>Excel Standort</th>
        <th>Excel Verwendung</th>
        <th>Excel Bestand</th>
        <th>Excel Anzahl</th>
	</tr></thead><tbody>";

while ($row = $result->fetch_assoc()) {
    if (intval($row["Anzahl"]) > 0) {
        echo "<tr>";
        echo "<td>" . $row["id"] . "</td>";
        echo "<td><input class='form-control form-control-sm' type='text' id='amount" . $row["id"] . "' value='" . $row["Anzahl"] . "' size='2'></input></td>";

        echo "<td>
    <select class='form-control form-control-sm' id='variante" . $row["id"] . "'>";
        $options = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
        foreach ($options as $index => $option) {
            $value = $index + 1;
            $selected = ($row["tabelle_Varianten_idtabelle_Varianten"] == $value) ? "selected" : "";
            echo "<option value='$value' $selected>$option</option>";
        }
        echo "</select></td>";


        echo "<td>" . $row["Raumnr"] . "</td>";
        echo "<td>" . $row["Raumbezeichnung"] . "</td>";
        echo "<td>" . $row["Raumbereich Nutzer"] . "</td>";

        echo "<td>
	    	<select class='form-control form-control-sm'' id='bestand" . $row["id"] . "'>";
        if ($row["Neu/Bestand"] == "0") {
            echo "<option value=0 selected>Ja</option>";
            echo "<option value=1>Nein</option>";
        } else {
            echo "<option value=0> Ja</option>";
            echo "<option value=1 selected>Nein</option>";
        }
        echo "</select></td>";
        echo "<td>   	
                <select class='form-control form-control-sm'' id='Standort" . $row["id"] . "'>";
        if ($row["Standort"] == "0") {
            echo "<option value=0 selected>Nein</option>";
            echo "<option value=1>Ja</option>";
        } else {
            echo "<option value=0>Nein</option>";
            echo "<option value=1 selected>Ja</option>";
        }
        echo "</select></td>";
        echo "<td>   	    	
                        <select class='form-control form-control-sm'' id='Verwendung" . $row["id"] . "'>";
        if ($row["Verwendung"] == "0") {
            echo "<option value=0 selected>Nein</option>";
            echo "<option value=1>Ja</option>";
        } else {
            echo "<option value=0>Nein</option>";
            echo "<option value=1 selected>Ja</option>";
        }
        echo "</select></td>";
        if (null != ($row["Kurzbeschreibung"])) {
            echo "<td><button type='button' class='btn btn-sm btn-outline-dark' 
    data-bs-toggle='popover' 
    data-bs-placement='top' 
    data-bs-content='" . htmlspecialchars($row["Kurzbeschreibung"]) . "' 
    title='Kommentar'>
    <i class='fa fa-comment'></i></button></td>";
        } else {
            echo "<td> </td>";
        }
        echo "<td><button type='button' id='" . $row["id"] . "' class='btn btn-warning btn-sm' value='saveElement'><i class='far fa-save'></i></button></td>";
        echo "<td>" . $row["tabelle_Varianten_idtabelle_Varianten"] . "</td>";
        echo "<td>" . $row["Standort"] . "</td>";
        echo "<td>" . $row["Verwendung"] . "</td>";
        echo "<td>" . $row["Neu/Bestand"] . "</td>";
        echo "<td>" . $row["Anzahl"] . "</td>";
        echo "</tr>";
    }
}
echo "</tbody></table>";
$mysqli->close();

echo "
<div class='modal fade' id='myModal' role='dialog'>
  <div class='modal-dialog'>
    <div class='modal-content'>
      <div class='modal-header'>
        <button type='button' class='close' data-bs-dismiss='modal'>&times;</button>
        <h4 class='modal-title'>Kommentar</h4>
      </div>
      <div class='modal-body' id='mbody'>
              <div class='modal-body-inner'></div>
      </div>
      <div class='modal-footer'>
        <button type='button' class='btn btn-default' data-bs-dismiss='modal'>Close</button>
      </div>
    </div>

  </div>
</div>";
?>

<script src="_utils.js"></script>
<script>
    var tableRoomsWithElement;
    $(document).ready(function () {
        let tableRoomsWithElement = new DataTable('#tableRoomsWithElement', {
            columnDefs: [
                {
                    targets: [0, 11, 12, 13, 14, 15],
                    visible: false,
                    searchable: false
                },
                {
                    targets: 1, // Assuming "Anzahl" is in the second column (index 1)
                    orderDataType: "dom-text-numeric"
                }
            ],
            paging: false,
            searching: true,
            info: false,
            order: [[1, "asc"]],
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json",
                search: ""
            },
            layout: {
                topStart: 'pageLength',
                topEnd: 'search',
                bottomStart: null,
                bottomEnd: null
            },
            buttons: [
                {
                    extend: 'excel',
                    exportOptions: {
                        columns: [15, 11, 3, 4, 5, 12, 14]
                    }
                }
            ],
            select: true,
            initComplete: function (settings, json) {
                // Your initComplete function here
            }
        });



        $('#tableRoomsWithElement tbody').on('click', 'tr', function () {
            let id = tableRoomsWithElement.row($(this)).data()[0];
            let stk = $("#amount" + id).val();
            $.ajax({
                url: "getElementBestand.php",
                data: {"id": id, "stk": stk},
                type: "GET",
                success: function (data) {
                    $("#elementBestand").html(data);
                }
            });
        });

        let popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
        let popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl, {
                trigger: 'click',
                html: true
            })
        })

        // Close popover when clicking outside
        $(document).on('click', function (e) {
            if (!$(e.target).closest('[data-bs-toggle="popover"]').length &&
                !$(e.target).closest('.popover').length) {
                $('[data-bs-toggle="popover"]').popover('hide');
            }
        });




        $("button[value='reloadBestand']").click(function () {
            $("#elementBestand").html("");
            $.ajax({
                url: "getElementBestand.php",
                type: "GET",
                success: function (data) {
                    makeToaster("Reloaded!", true);
                    $("#elementBestand").html(data);
                }
            });
        });


        $("button[value='saveElement']").click(function () {
            let id = this.id;
            let comment = $("#buttonComment" + id).val();
            let amount = $("#amount" + id).val();
            let variantenID = $("#variante" + id).val();
            let bestand = $("#bestand" + id).val();
            let standort = $("#Standort" + id).val();
            let verwendung = $("#Verwendung" + id).val();
            if (standort === '0' && verwendung === '0') {
                alert("Standort und Verwendung kann nicht Nein sein!");
            } else {
                $.ajax({
                    url: "saveRoombookEntry.php",
                    data: {
                        "comment": comment,
                        "id": id,
                        "amount": amount,
                        "variantenID": variantenID,
                        "bestand": bestand,
                        "standort": standort,
                        "verwendung": verwendung
                    },
                    type: "GET",
                    success: function (data) {
                        makeToaster(data.trim(), true);
                    }
                });
            }
        });

    });


</script>
</body>
</html>