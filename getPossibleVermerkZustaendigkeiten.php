<?php
require_once 'utils/_utils.php';
check_login();
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <title>getPossibleVermerkZustaendigkeiten</title></head>
<body>

<?php
$mysqli = utils_connect_sql();
$sql = "SELECT tabelle_ansprechpersonen.idTABELLE_Ansprechpersonen, tabelle_ansprechpersonen.Name, tabelle_ansprechpersonen.Vorname
                FROM tabelle_projekte_has_tabelle_ansprechpersonen INNER JOIN tabelle_ansprechpersonen ON tabelle_projekte_has_tabelle_ansprechpersonen.TABELLE_Ansprechpersonen_idTABELLE_Ansprechpersonen = tabelle_ansprechpersonen.idTABELLE_Ansprechpersonen
                WHERE (((tabelle_projekte_has_tabelle_ansprechpersonen.TABELLE_Projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . ")
                AND tabelle_ansprechpersonen.idTABELLE_Ansprechpersonen NOT IN (
                SELECT tabelle_ansprechpersonen.idTABELLE_Ansprechpersonen
						FROM tabelle_Vermerke_has_tabelle_ansprechpersonen INNER JOIN tabelle_ansprechpersonen ON tabelle_Vermerke_has_tabelle_ansprechpersonen.tabelle_ansprechpersonen_idTABELLE_Ansprechpersonen = tabelle_ansprechpersonen.idTABELLE_Ansprechpersonen
						WHERE (((tabelle_Vermerke_has_tabelle_ansprechpersonen.tabelle_Vermerke_idtabelle_Vermerke)=" . filter_input(INPUT_GET, 'vermerkID') . "))
                ));";
$result = $mysqli->query($sql);

echo "<table class='table table-striped table-sm' id='tablepossibleVermerkZustaendigkeitMembers'  >
        <thead><tr>
        <th>ID</th>
        <th>Name</th>
        <th>Vorname</th>
        </tr></thead>
        <tbody>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td><button type='button' id='" . $row["idTABELLE_Ansprechpersonen"] . "' class='btn btn-success btn-sm' value='addVermerkZustaendigkeit'><i class='fas fa-plus-square'></i></button></td>";
    echo "<td>" . $row["Name"] . "</td>";
    echo "<td>" . $row["Vorname"] . "</td>";
    echo "</tr>";

}

echo "</tbody></table>";

$mysqli->close();
?>
</body>
<script>

    $(document).ready(function () {

        $('#tablepossibleVermerkZustaendigkeitMembers').DataTable({
            paging: false,
            searching: true,
            info: false,
            order: [[1, 'asc']],
            columnDefs: [
                {
                    targets: [0],
                    visible: true,
                    searchable: false,
                },
            ],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/de-DE.json',
            },
            scrollY: '20vh',
            scrollCollapse: true,
            initComplete: function () {
                console.log("tablepossibleVermerkZustaendigkeitMembers initcomplete");
                $('#possibleVermerkZustaendigkeitCH .xxx').remove();
                $('#possibleVermerkZustaendigkeit .dt-search label').remove();
                $('#possibleVermerkZustaendigkeit .dt-search').children().removeClass("form-control form-control-sm").addClass("xxx btn btn-sm btn-outline-dark").appendTo('#possibleVermerkZustaendigkeitCH');
            }
        });

        $("button[value='addVermerkZustaendigkeit']").click(function () {
            var id = this.id;
            var vermerkID = "<?php echo filter_input(INPUT_GET, 'vermerkID') ?>";

            if (id !== "") {
                $.ajax({
                    url: "addPersonToVermerkZustaendigkeit.php",
                    data: {"ansprechpersonenID": id, "vermerkID": vermerkID},
                    type: "GET",
                    success: function (data) {
                        alert(data);
                        $.ajax({
                            url: "getVermerkZustaendigkeiten.php",
                            type: "GET",
                            data: {"vermerkID": vermerkID},
                            success: function (data) {
                                $("#vermerkZustaendigkeit").html(data);
                                $.ajax({
                                    url: "getPossibleVermerkZustaendigkeiten.php",
                                    type: "GET",
                                    data: {"vermerkID": vermerkID},
                                    success: function (data) {
                                        $("#possibleVermerkZustaendigkeit").html(data);
                                        // Neu laden der PDF-Vorschau
                                        document.getElementById('pdfPreview').src += '';
                                    }
                                });

                            }
                        });
                    }
                });
            }
        });
    });
</script>
</html>