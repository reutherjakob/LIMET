<?php
if (!function_exists('utils_connect_sql')) {  include "_utils.php"; }
session_start();
check_login();
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
<head>
    <title>Get Rooms With Element</title></head>
<body>

<?php
$mysqli = utils_connect_sql();

$sql = "SELECT tabelle_räume_has_tabelle_elemente.id, tabelle_räume.idTABELLE_Räume, tabelle_varianten.Variante, tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung, tabelle_räume.`Raumbereich Nutzer`, tabelle_räume_has_tabelle_elemente.Anzahl, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, tabelle_räume_has_tabelle_elemente.Standort, tabelle_räume_has_tabelle_elemente.Verwendung, tabelle_räume_has_tabelle_elemente.Kurzbeschreibung, tabelle_räume.Geschoss
                FROM (tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) INNER JOIN tabelle_varianten ON tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten = tabelle_varianten.idtabelle_Varianten
                WHERE (((tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente)=" . filter_input(INPUT_GET, 'elementID') . ") AND ((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . "))
                ORDER BY tabelle_räume.Raumnr;";

$result = $mysqli->query($sql);
echo "<table class='table table-striped table-responsive table-hover table-bordered table-sm py-0' id='tableRoomsWithElements'>
	<thead><tr>
	<th>ID</th>
	<th>Var</th>
	<th>Raum Nr</th>
	<th>Raum</th>
        <th>Ebene</th>
	<th>Bereich</th>
	<th>Stk</th>
	<th>Bestand</th>
	<th>Standort</th>
	<th>Verw.</th>
	<th>Komm.</th>
	<th class='fa fa-save'> </th>
	</tr></thead><tbody>";

while ($row = $result->fetch_assoc()) {
    echo "<tr data-variant='" . $row["Variante"] . "' data-bestand='" . $row["Neu/Bestand"] . "' data-standort='" . $row["Standort"] . "' data-verwendung='" . $row["Verwendung"] . "'>";
    echo "<td>" . $row["id"] . "</td>";
    echo "<td>" . $row["Variante"] . "</td>";
    echo "<td>" . $row["Raumnr"] . "</td>";
    echo "<td>" . $row["Raumbezeichnung"] . "</td>";
    echo "<td>" . $row["Geschoss"] . "</td>";
    echo "<td>" . $row["Raumbereich Nutzer"] . "</td>";
    echo "<td><input class='form-control form-control-sm' type='text' id='amount" . $row["id"] . "' value='" . $row["Anzahl"] . "' size='2'></input></td>";
    echo "<td>";
    if ($row["Neu/Bestand"] == 1) {
        echo "Nein";
    } else {
        echo "Ja";
    }
    echo "</td>";
    echo "<td>";
    if ($row["Standort"] == 1) {
        echo "Ja";
    } else {
        echo "Nein";
    }
    echo "</td>";
    echo "<td>";
    if ($row["Verwendung"] == 1) {
        echo "Ja";
    } else {
        echo "Nein";
    }
    echo "</td>";
    if (null != ($row["Kurzbeschreibung"])) {
        echo "<td><button type='button' class='btn btn-sm btn-outline-dark comment-btn' id='" . $row["id"] . "' data-description='" . $row["Kurzbeschreibung"] . "' title='Kommentar'><i class='fa fa-comment'></i></button></td>";
    } else {
        echo "<td><button type='button' class='btn btn-sm btn-outline-dark comment-btn' id='" . $row["id"] . "' data-description='" . $row["Kurzbeschreibung"] . "' title='Kommentar'><i class='fa fa-comment-slash'></i></button></td>";
    }
    echo "<td><button type='button' id='" . $row["id"] . "' class='btn btn-warning btn-sm' value='saveElement'><i class='far fa-save'></i></button></td>";
    echo "</tr>";
}
echo "</tbody></table>";


$mysqli->close();
?>

<script src="_utils.js"></script>
<script charset="utf-8" type="module">
    import CustomPopover from './_popover.js';

    function translateVariant(variant) {
        const translationMap = {
            'A': 1, 'B': 2, 'C': 3, 'D': 4, 'E': 5, 'F': 6, 'G': 7, 'H': 8, 'I': 9, 'J': 10,
            'K': 11, 'L': 12, 'M': 13, 'N': 14, 'O': 15, 'P': 16, 'Q': 17, 'R': 18, 'S': 19,
            'T': 20, 'U': 21, 'V': 22, 'W': 23, 'X': 24, 'Y': 25, 'Z': 26
        };
        return translationMap[variant] || variant;
    }


    $(document).ready(function () {
        const tableRoomsWithElements = new DataTable('#tableRoomsWithElements', {
            paging: true,
            searching: true,
            info: false,
            columnDefs: [
                {
                    targets: [0],
                    visible: false,
                    searchable: false
                }
            ],
            order: [[2, "asc"]],
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
            language: {
                search: ""
            },
            scrollY: '40vh',
            scrollCollapse: true
        });


        // Popover for Comment
        CustomPopover.init('.comment-btn', {
            onSave: function (trigger, newText) {
                trigger.dataset.description = newText;
                let id = trigger.id;
                let comment = newText;
                let amount = $("#amount" + id).val();
                let $row = $(trigger).closest('tr');
                let variantLetter = $row.data('variant');
                let variantenID = translateVariant(variantLetter);
                let bestand = $row.data('bestand');
                let standort = $row.data('standort');
                let verwendung = $row.data('verwendung');
                console.log("id:", id);
                console.log("comment:", comment);
                console.log("amount:", amount);
                console.log("Variant:", variantenID);
                console.log("Bestand:", bestand);
                console.log("Standort:", standort);
                console.log("Verwendung:", verwendung);

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
                        makeToaster(data.trim(), data === "Raumbucheintrag erfolgreich aktualisiert!");
                    }
                });
            }
        });

        // Element speichern
        $("button[value='saveElement']").click(function () {
            let id = this.id;
            let comment = $("#buttonComment" + id).val();
            let amount = Number($("#amount" + id).val());
            if (!Number.isInteger(amount)) {
                alert("Stückzahl ist keine Zahl!");
            } else {
                $.ajax({
                    url: "saveRoombookEntry2.php",
                    data: {"comment": comment, "id": id, "amount": amount},
                    type: "GET",
                    success: function (data) {
                        // alert(data);  //location.reload();
                        makeToaster(data.trim(), true);
                    }
                });
            }
        });

    });


</script>

</body>
</html>