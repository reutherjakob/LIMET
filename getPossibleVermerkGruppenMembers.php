<!DOCTYPE html >
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <title></title></head>
<body>

<?php
require_once 'utils/_utils.php';
check_login();
$mysqli = utils_connect_sql();
$sql = "SELECT tabelle_ansprechpersonen.idTABELLE_Ansprechpersonen, tabelle_ansprechpersonen.Name, tabelle_ansprechpersonen.Vorname
                FROM tabelle_projekte_has_tabelle_ansprechpersonen INNER JOIN tabelle_ansprechpersonen ON tabelle_projekte_has_tabelle_ansprechpersonen.TABELLE_Ansprechpersonen_idTABELLE_Ansprechpersonen = tabelle_ansprechpersonen.idTABELLE_Ansprechpersonen
                WHERE (((tabelle_projekte_has_tabelle_ansprechpersonen.TABELLE_Projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . ")
                AND tabelle_ansprechpersonen.idTABELLE_Ansprechpersonen NOT IN (
                SELECT tabelle_ansprechpersonen.idTABELLE_Ansprechpersonen
                                FROM tabelle_ansprechpersonen INNER JOIN tabelle_Vermerkgruppe_has_tabelle_ansprechpersonen ON tabelle_ansprechpersonen.idTABELLE_Ansprechpersonen = tabelle_Vermerkgruppe_has_tabelle_ansprechpersonen.tabelle_ansprechpersonen_idTABELLE_Ansprechpersonen
                                WHERE (((tabelle_Vermerkgruppe_has_tabelle_ansprechpersonen.tabelle_Vermerkgruppe_idtabelle_Vermerkgruppe)=" . filter_input(INPUT_GET, 'gruppenID') . "))
                ));";
$result = $mysqli->query($sql);

echo "<table id='tablePossibleVermerkGroupMembers' class='table table-striped table-bordered table-sm table-hover border border-light border-5'  >
        <thead><tr>
        <th></th>
        <th>Name</th>
        <th>Vorname</th>
        </tr></thead>
        <tbody>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td><button type='button' id='" . $row["idTABELLE_Ansprechpersonen"] . "' class='btn btn-outline-success btn-sm' value='addVermerkGroupMember'><i class='fas fa-plus'></i></button></td>";
    echo "<td>" . $row["Name"] . "</td>";
    echo "<td>" . $row["Vorname"] . "</td>";
    echo "</tr>";

}

echo "</tbody></table>";

$mysqli->close();
?>

<script>

    $('#tablePossibleVermerkGroupMembers').DataTable({
        paging: false,
        searching: true,
        info: false,
        order: [[1, "asc"]],
        columnDefs: [
            {
                targets: [0],
                visible: true,
                searchable: false,
                orderable: false // Use 'orderable' instead of 'sortable' for DataTables
            }
        ],
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json"
        },
        scrollY: '20vh',
        scrollCollapse: true
    });

    $("button[value='addVermerkGroupMember']").click(function () {
        let id = this.id;
        let groupID = "<?php echo filter_input(INPUT_GET, 'gruppenID') ?>";
        if (id !== "") {
            $.ajax({
                url: "/addPersonToVermerkGroup.php",
                data: {"ansprechpersonenID": id, "groupID": groupID},
                type: "GET",
                success: function (data) {
                    makeToaster(data,true);
                    document.getElementById('pdfPreview').src += '';
                    $.ajax({
                        url: "/getVermerkgruppenMembers.php",
                        type: "GET",
                        data: {"gruppenID": groupID},
                        success: function (data) {
                            $("#vermerkGroupMembers").html(data);
                            $.ajax({
                                url: "/getPossibleVermerkGruppenMembers.php",
                                type: "GET",
                                data: {"gruppenID": groupID},
                                success: function (data) {
                                    $("#possibleVermerkGroupMembers").html(data);
                                }
                            });

                        }
                    });
                }
            });
        }


    });


</script>

</body>
</html>