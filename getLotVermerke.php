<?php
require_once 'utils/_utils.php';
check_login();
?>

<!DOCTYPE html>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
<html lang="de">
<head>
    <title></title></head>
<body>

<?php
$mysqli = utils_connect_sql();

$sql = "SELECT tabelle_Vermerkgruppe.Gruppenname, tabelle_Vermerkgruppe.Gruppenart, tabelle_Vermerkgruppe.Ort, tabelle_Vermerkgruppe.Datum, tabelle_lose_extern.LosNr_Extern, tabelle_lose_extern.LosBezeichnung_Extern, tabelle_ansprechpersonen.Name, tabelle_ansprechpersonen.Vorname, tabelle_Vermerke.Faelligkeit, tabelle_Vermerke.Vermerkart, tabelle_Vermerke.Bearbeitungsstatus, tabelle_Vermerke.Vermerktext, tabelle_Vermerke.Erstellungszeit, tabelle_Vermerke.idtabelle_Vermerke, tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung
                FROM (((tabelle_Vermerke LEFT JOIN (tabelle_ansprechpersonen RIGHT JOIN tabelle_Vermerke_has_tabelle_ansprechpersonen ON tabelle_ansprechpersonen.idTABELLE_Ansprechpersonen = tabelle_Vermerke_has_tabelle_ansprechpersonen.tabelle_ansprechpersonen_idTABELLE_Ansprechpersonen) ON tabelle_Vermerke.idtabelle_Vermerke = tabelle_Vermerke_has_tabelle_ansprechpersonen.tabelle_Vermerke_idtabelle_Vermerke) INNER JOIN (tabelle_Vermerkgruppe INNER JOIN tabelle_Vermerkuntergruppe ON tabelle_Vermerkgruppe.idtabelle_Vermerkgruppe = tabelle_Vermerkuntergruppe.tabelle_Vermerkgruppe_idtabelle_Vermerkgruppe) ON tabelle_Vermerke.tabelle_Vermerkuntergruppe_idtabelle_Vermerkuntergruppe = tabelle_Vermerkuntergruppe.idtabelle_Vermerkuntergruppe) LEFT JOIN tabelle_räume ON tabelle_Vermerke.tabelle_räume_idTABELLE_Räume = tabelle_räume.idTABELLE_Räume) LEFT JOIN tabelle_lose_extern ON tabelle_Vermerke.tabelle_lose_extern_idtabelle_Lose_Extern = tabelle_lose_extern.idtabelle_Lose_Extern
                WHERE (((tabelle_Vermerkgruppe.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . ") AND ((tabelle_Vermerke.tabelle_lose_extern_idtabelle_Lose_Extern)=" . filter_input(INPUT_GET, 'lotID') . "))
                ORDER BY tabelle_Vermerkgruppe.Datum DESC , tabelle_Vermerke.Erstellungszeit DESC;";

$result = $mysqli->query($sql);

echo "<button type='button' class='btn btn-outline-dark btn-sm' value='createLotVermerkePDF' id='" . filter_input(INPUT_GET, 'lotID') . "'><i class='far fa-file-pdf'></i> Losvermerke - PDF</button>";

echo "<table class='table table-striped table-bordered table-sm table-hover border border-5 border-light' id='tableLotVermerke'>
	<thead><tr>
	<th>ID</th>
        <th>Art</th>
        <th>Name</th>
        <th>Status</th>
	<th>Datum</th>
	<th>Typ</th>
	<th>Zuständig</th>
	<th>Fälligkeit</th>
        <th>Vermerk</th>	  
        <th>Status</th>
	</tr></thead><tbody>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row["idtabelle_Vermerke"] . "</td>";
    echo "<td>" . $row["Gruppenart"] . "</td>";
    echo "<td>" . $row["Gruppenname"] . "</td>";
    echo "<td><div class='form-check'>";
    if ($row["Vermerkart"] != "Info") {
        if ($row["Bearbeitungsstatus"] == "0") {
            echo "<input type='checkbox' class='form-check-input' id='" . $row["idtabelle_Vermerke"] . "' value='statusCheck'>";
        } else {
            echo "<input type='checkbox' class='form-check-input' id='" . $row["idtabelle_Vermerke"] . "' value='statusCheck' checked='true'>";
        }
    }
    echo "</div></td>";
    echo "<td>" . $row["Datum"] . "</td>";
    echo "<td>" . $row["Vermerkart"] . "</td>";
    echo "<td>" . $row["Name"] . "</td>";
    echo "<td>";
    if ($row["Vermerkart"] != "Info") {
        echo $row["Faelligkeit"];
    }
    echo "</td>";
    echo "<td><button type='button' class='btn btn-default btn-sm' data-bs-toggle='popover' title='Vermerk' data-placement='left' data-bs-content='" . $row["Vermerktext"] . "'><i class='far fa-comment'></i></button></td>";
    echo "<td>" . $row["Bearbeitungsstatus"] . "</td>";
    echo "</tr>";
}
echo "</tbody></table>";
$mysqli->close();
?>

<script>

    $(document).ready(function () {
        new DataTable('#tableLotVermerke', {
            columns: [
                {visible: false, searchable: false}, // Column 0
                null, // Column 1
                null, // Column 2
                null, // Column 3
                null, // Column 4
                {visible: false, searchable: false}, // Column 5
                null, // Column 6
                null, // Column 7
                null, // Column 8
                {visible: false, searchable: false} // Column 9
            ],
            paging: true,
            pagingType: 'simple',
            lengthChange: false,
            pageLength: 10,
            searching: true,
            info: true,
            order: [[4, 'desc']],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json',
                search: "",

            },
            layout: {
                topStart: null,
                topEnd: null,
                bottomStart: "search",
                bottomEnd: ["info", "paging"]
            },
            rowCallback: function (row, data) {
                if (data[5] === "Bearbeitung") {
                    if (data[9] === "0") {
                        $(row).css('background-color', '#ff8080');
                    } else {
                        $(row).css('background-color', '#b8dc6f');
                    }
                } else {
                    $(row).css('background-color', '#d3edf8');
                }
            }
        });

        $("input[value='statusCheck']").change(function () {
            var vermerkStatus;
            if ($(this).prop('checked') === true) {
                vermerkStatus = 1;
            } else {
                vermerkStatus = 0;
            }
            let vermerkID = this.id;
            if (vermerkStatus !== "" && vermerkID !== "") {
                $.ajax({
                    url: "saveVermerkStatus.php",
                    data: {"vermerkID": vermerkID, "vermerkStatus": vermerkStatus},
                    type: "POST",
                    success: function (data) {
                        alert(data);
                        $.ajax({
                            url: "getLotVermerke.php",
                            data: {"lotID": lotID},
                            type: "POST",
                            success: function (data) {
                                $("#lotVermerke").html(data);
                            }
                        });
                    }
                });
            } else {
                alert("Vermerkstatus nicht lesbar!");
            }
        });

        $(function () {
            // Enable all popovers
            const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
            const popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
                return new bootstrap.Popover(popoverTriggerEl, {
                    container: 'body',
                    trigger: 'focus', // ensures popover closes when focus is lost
                    placement: 'left' // optional
                });
            });

            // Close any open popover when clicking outside
            $(document).on('click', function (e) {
                $('[data-bs-toggle="popover"]').each(function () {
                    if (
                        !$(this).is(e.target) &&                              // Not the clicked element
                        $(this).has(e.target).length === 0 &&                // Not inside the clicked element
                        $('.popover').has(e.target).length === 0             // Not inside the actual popover
                    ) {
                        $(this).popover('hide');                             // Hide it
                    }
                });
            });
        });

    });

    $("button[value='createLotVermerkePDF']").click(function () {
        window.open('PDFs/pdf_createLotVermerkePDF.php?losID=' + this.id);
    });

</script>
</body>
</html>