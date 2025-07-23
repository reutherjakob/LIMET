<!DOCTYPE html >
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <title>getVermerkZustaendigkeiten</title>
</head>
<body>

<?php
require_once 'utils/_utils.php';
check_login();

$mysqli = utils_connect_sql();

$vermerkID = filter_input(INPUT_GET, 'vermerkID', FILTER_VALIDATE_INT);

if (!$vermerkID) {
    echo "Ungültige Vermerk-ID.";
    exit;
}

$stmt = $mysqli->prepare("
    SELECT 
        a.idTABELLE_Ansprechpersonen, 
        a.Name, 
        a.Vorname
    FROM 
        tabelle_Vermerke_has_tabelle_ansprechpersonen vhtap
    INNER JOIN 
        tabelle_ansprechpersonen a 
        ON vhtap.tabelle_ansprechpersonen_idTABELLE_Ansprechpersonen = a.idTABELLE_Ansprechpersonen
    WHERE 
        vhtap.tabelle_Vermerke_idtabelle_Vermerke = ?
");

$stmt->bind_param('i', $vermerkID);
$stmt->execute();
$result = $stmt->get_result();

echo "<table class='table table-striped table-sm' id='tableVermerkZustaendigkeitMembers'>
        <thead><tr>
        <th>ID</th>
        <th>Name</th>
        <th>Vorname</th>
        </tr></thead>
        <tbody>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td><button type='button' id='" . $row["idTABELLE_Ansprechpersonen"] . "' class='btn btn-danger btn-sm' value='deleteVermerkZustaendigkeit'><i class='fa fa-minus-circle'></i></button></td>";
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
        $("button[value='deleteVermerkZustaendigkeit']").click(function () {
            let id = this.id;
            console.log("btn pressed");
            let vermerkID = "<?php echo filter_input(INPUT_GET, 'vermerkID') ?>";
            if (id !== "" && vermerkID !== "") {
                $.ajax({
                    url: "deletePersonFromVermerkZustaendigkeit.php",
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

        $('#tableVermerkZustaendigkeitMembers').DataTable({
            paging: false,
            searching: true,
            info: false,
            order: [[1, 'asc']],
            columnDefs: [
                {
                    targets: [0],
                    visible: true,
                    searchable: false
                }
            ],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/2.0.0/i18n/de-DE.json'
            },
            scrollY: '20vh',
            scrollCollapse: true,
            initComplete: function () {
                $('#vermerkZustaendigkeitCH .xxx').remove();
                $('#vermerkZustaendigkeit .dt-search label').remove();
                $('#vermerkZustaendigkeit .dt-search').children().removeClass("form-control form-control-sm").addClass("xxx btn btn-sm btn-outline-dark").appendTo('#vermerkZustaendigkeitCH');
            }
        });
    });

</script>
</html>