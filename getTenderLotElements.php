<?php
// reworked 25
if (!function_exists('utils_connect_sql')) {
    include "_utils.php";
}
check_login();
?>

<!DOCTYPE html>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
<html lang="de">
<head>
    <title>getTenderLotElements</title></head>
<body>

<?php
$mysqli = utils_connect_sql();
if ($_GET["lotID"] != "") {
    $_SESSION["lotID"] = $_GET["lotID"];
} else {
    echo "Kein Los ausgewählt!";
}

$sql = "
SELECT 
    tabelle_räume_has_tabelle_elemente.id, 
    tabelle_räume_has_tabelle_elemente.Anzahl, 
    tabelle_elemente.ElementID, 
    tabelle_elemente.Bezeichnung AS ElementBezeichnung, 
    tabelle_varianten.Variante, 
    tabelle_räume_has_tabelle_elemente.Kurzbeschreibung, 
    tabelle_räume.Geschoss, 
    tabelle_räume.`Raumbereich Nutzer`,   
    tabelle_räume.Raumnr, 
    tabelle_räume.Raumbezeichnung, 
    tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, 
    tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten, 
    tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente
FROM 
    tabelle_varianten 
    INNER JOIN (
        (tabelle_räume_has_tabelle_elemente 
        INNER JOIN tabelle_räume 
        ON tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume = tabelle_räume.idTABELLE_Räume) 
        INNER JOIN tabelle_elemente 
        ON tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente = tabelle_elemente.idTABELLE_Elemente
    ) 
    ON tabelle_varianten.idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten
WHERE 
    tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern = " . $_SESSION["lotID"] . " 
    AND tabelle_räume_has_tabelle_elemente.Standort = 1
ORDER BY 
    tabelle_räume.Raumnr;";

$result = $mysqli->query($sql);
echo "<table class='table table-striped table-bordered table-sm table-hover border border-light border-5' id='tableLotElements1'>
            <thead><tr>
            <th>ID</th>
            <th>elementID</th>
            <th>variantenID</th>
            <th>Stk</th>
            <th>ID</th>
            <th>Element</th>
            <th>Variante</th>
            <th>Bestand</th>
            <th>Raumnr</th>
            <th>Raum</th>
            <th>Geschoss</th>
            <th>Raumbereich Nutzer</th>
            <th>Kommentar</th>								
            </tr></thead>           
            <tbody>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row["id"] . "</td>";
    echo "<td>" . $row["TABELLE_Elemente_idTABELLE_Elemente"] . "</td>";
    echo "<td>" . $row["tabelle_Varianten_idtabelle_Varianten"] . "</td>";
    echo "<td>" . $row["Anzahl"] . "</td>";
    echo "<td>" . $row["ElementID"] . "</td>";
    echo "<td>" . $row["ElementBezeichnung"] . "</td>";
    echo "<td>" . $row["Variante"] . "</td>";
    echo "<td>";
    switch ($row["Neu/Bestand"]) {
        case 0:
            echo "Ja";
            break;
        case 1:
            echo "Nein";
            break;
    }
    echo "</td>";
    echo "<td>" . $row["Raumnr"] . "</td>";
    echo "<td>" . $row["Raumbezeichnung"] . "</td>";
    echo "<td>" . $row["Geschoss"] . "</td>";
    echo "<td>" . $row["Raumbereich Nutzer"] . "</td>         <td>";

    $Kurzbeschreibung = trim($row["Kurzbeschreibung"] ?? "");
    $buttonClass = $Kurzbeschreibung === "" ? "btn-outline-secondary" : "btn-outline-dark";
    $iconClass = $Kurzbeschreibung === "" ? "fa fa-comment-slash" : "fa fa-comment";
    $dataAttr = $Kurzbeschreibung === "" ? "data-description= '' " : "data-description='" . htmlspecialchars($Kurzbeschreibung, ENT_QUOTES, 'UTF-8') . "'";

    echo " <button type='button'
        class='btn btn-sm " . $buttonClass . "comment-btn'" . $dataAttr . " id='" . $row['id'] . "' title='Kommentar'><i class='" . $iconClass . " '></i> $Kurzbeschreibung
        </button></td>";

    //echo "<td><textarea id='comment" . $row["id"] . "' rows='1' style='width: 100%;'>" . $row["Kurzbeschreibung"] . "</textarea></td>";
    echo "</tr>";
}
echo "</tbody></table>";
$mysqli->close();
?>

<script src="_utils.js"></script>
<script charset="utf-8" type="module">
    import CustomPopover from './_popover.js'; //TODO: Find out why this doesnt work, but others witht he same code do work??

    CustomPopover.init('.comment-btn', {
        onSave: function (trigger, newText) {
            trigger.dataset.description = newText;
            // send an AJAX request to save the new text
            let id = trigger.id;   // = tabelle_räume_has_tabelle_elemente.id
            $.ajax({
                url: "saveRoomElementComment.php",
                data: {
                    "comment": newText,
                    "id": id
                },
                type: "GET",
                success: function (data) {
                    makeToaster(data.trim(), true);
                    $(".comment-btn[id='" + id + "']").removeClass('btn-outline-secondary');
                    $(".comment-btn[id='" + id + "']").addClass('btn-outline-dark');
                    $(".comment-btn[id='" + id + "']").find('i').removeClass('fa fa-comment-slash');
                    $(".comment-btn[id='" + id + "']").find('i').addClass('fa fa-comment');
                    $(".comment-btn[id='" + id + "']").attr('data-description', newText).data('description', newText);
                }
            });
        }
    });


    var tableLotElements1;
    $(document).ready(function () {


        tableLotElements1 = new DataTable('#tableLotElements1', {
            dom: '<"#topDiv.top-container d-flex"<"col-md-6 justify-content-start"B><"#topDivSearch2.col-md-6"f>>t<"bottom d-flex" <"col-md-6 justify-content-start"i><"col-md-6 d-flex align-items-center justify-content-end"lp>>',
            columnDefs: [
                {
                    targets: [0, 1, 2],
                    visible: false,
                    searchable: false
                }
            ],
            searching: true,
            info: true,
            paging: true,
            select: true,
            order: [[3, 'asc']],
            pagingType: 'simple',
            lengthChange: false,
            pageLength: 10,
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json',
                search: "",
                searchPlaceholder: "Suche..."
            },
            buttons: [
                {
                    extend: 'excel',
                    text: ' Excel',
                    className: "btn btn-md bg-white btn-outline-secondary fas fa-file-excel me-1 ms-1",
                },
                {
                    extend: 'excel',
                    text: ' Verortungsliste',
                    exportOptions: {
                        columns: [3, 4, 5, 6, 7, 8, 9, 10, 11]
                    },
                    className: "btn btn-md bg-white btn-outline-secondary fas fa-file-excel me-1 ms-1",
                    customize: function (xlsx) {
                        var sheet = xlsx.xl.worksheets['sheet1.xml'];
                        $('row:first', sheet).remove();
                        $('row', sheet).each(function () {
                            var col3 = $('c[r^="A"]', this).text();
                            var col7 = $('c[r^="E"]', this).text();
                            if (col3 === '0' || col7 === 'Ja') {
                                $(this).remove();
                            }
                        });
                    }

                },
                {
                    text: ' Elementliste PDF',
                    className: "btn btn-md bg-white btn-outline-secondary fas fa-file-pdf me-1 ms-1",
                    action: function () {
                        window.open('/pdf_createLotElementListPDF.php');
                    }
                }
            ],
            initComplete: function () {

            }
        });


        $('#tableLotElements1 tbody').on('click', 'tr', function () {
            let elementID = tableLotElements1.row($(this)).data()[1];
            let variantenID = tableLotElements1.row($(this)).data()[2];
            let id = tableLotElements1.row($(this)).data()[0];
            let stk = tableLotElements1.row($(this)).data()[3];
            //console.log("elementID, variantenID, id, stk: ", elementID, variantenID, id, stk);
            $.ajax({
                url: "getVariantenParameters.php",
                data: {"variantenID": variantenID, "elementID": elementID},
                type: "GET",
                success: function (data) {
                    $("#elementsvariantenParameterInLot").html(data);
                    $("#elementsvariantenParameterInLot").show();
                    $.ajax({
                        url: "getElementBestand.php",
                        data: {"id": id, "stk": stk},
                        type: "GET",
                        success: function (data) {
                            $("#elementBestand").html(data);
                            $("#elementBestand").show();
                        }
                    });
                }
            });
        });


    });

    $('#createLotElementListPDF').click(function () {
        window.open('/pdf_createLotElementListPDF.php');
    });

</script>
</body>
</html>