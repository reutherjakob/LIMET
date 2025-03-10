<?php
if (!function_exists('utils_connect_sql')) {  include "_utils.php"; }
check_login();
$mysqli = utils_connect_sql();

$sql = "SELECT a.* FROM tabelle_ansprechpersonen a
        WHERE a.idTABELLE_Ansprechpersonen NOT IN (
            SELECT ap.TABELLE_Ansprechpersonen_idTABELLE_Ansprechpersonen
            FROM tabelle_projekte_has_tabelle_ansprechpersonen ap
            WHERE ap.TABELLE_Projekte_idTABELLE_Projekte = ?
        )
        ORDER BY a.Name";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $_SESSION["projectID"]);
$stmt->execute();
$result = $stmt->get_result();

echo "<table class='table table-striped table-bordered table-sm' id='tablePersonsNotInProject'  >
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Vorname</th>
            <th>Tel</th>
            <th>Adresse</th>
            <th>PLZ</th>
            <th>Ort</th>
            <th>Land</th>
            <th>Mail</th>
        </tr>
    </thead>
    <tbody>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row["idTABELLE_Ansprechpersonen"]?? '' ) . "</td>";
    echo "<td>" . htmlspecialchars($row["Name"]?? '') . "</td>";
    echo "<td>" . htmlspecialchars($row["Vorname"]?? '') . "</td>";
    echo "<td>" . htmlspecialchars($row["Tel"]?? '') . "</td>";
    echo "<td>" . htmlspecialchars($row["Adresse"]?? '') . "</td>";
    echo "<td>" . htmlspecialchars($row["PLZ"]?? '') . "</td>";
    echo "<td>" . htmlspecialchars($row["Ort"]?? '') . "</td>";
    echo "<td>" . htmlspecialchars($row["Land"]?? '') . "</td>";
    echo "<td>" . htmlspecialchars($row["Mail"]?? '') . "</td>";
    echo "</tr>";
}
echo "</tbody></table>";

$stmt->close();
$mysqli->close();
?>

<script>
    $(document).ready(function() {
        const table = $('#tablePersonsNotInProject').DataTable({
            columnDefs: [
                {
                    targets: [0],
                    visible: false,
                    searchable: false
                }
            ],
            select: true,
            paging: true,
            order: [[1, "asc"]],
            pagingType: "simple",
            lengthChange: false,
            pageLength: 10,
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json",
                search: ""
            }
        });

        $('#tablePersonsNotInProject tbody').on('click', 'tr', function() {
            if ($(this).hasClass('selected')) {
                table.row(this).deselect();
            } else {
                table.rows('.selected').deselect();
                table.row(this).select();

                const personID = table.row(this).data()[0];
                $.ajax({
                    url: `getAddPersonToProjectField.php`,
                    data: { personID: personID },
                    type: 'GET',
                    success: function(data) {
                        $("#addPersonToProject").html(data);
                    }
                });
            }
        });
    });
</script>
