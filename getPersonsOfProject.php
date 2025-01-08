<?php
include "_utils.php";
check_login();
$mysqli = utils_connect_sql();

$sql = "SELECT pz.Zuständigkeit, 
        a.idTABELLE_Ansprechpersonen, a.Name, a.Vorname, 
        a.Tel, a.Adresse, a.PLZ, a.Ort, a.Land, a.Mail, a.Raumnr, 
        o.Organisation
        FROM tabelle_ansprechpersonen a
        INNER JOIN tabelle_projekte_has_tabelle_ansprechpersonen pha ON a.idTABELLE_Ansprechpersonen = pha.TABELLE_Ansprechpersonen_idTABELLE_Ansprechpersonen
        INNER JOIN tabelle_projektzuständigkeiten pz ON pz.idTABELLE_Projektzuständigkeiten = pha.TABELLE_Projektzuständigkeiten_idTABELLE_Projektzuständigkeiten
        INNER JOIN tabelle_organisation o ON o.idtabelle_organisation = pha.tabelle_organisation_idtabelle_organisation
        WHERE pha.TABELLE_Projekte_idTABELLE_Projekte = ?";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $_SESSION["projectID"]);
$stmt->execute();
$result = $stmt->get_result();

echo "<table class='table table-striped table-bordered table-sm' id='tablePersons' cellspacing='0' width='100%'>
    <thead>
        <tr>
            <th>ID</th>
            <th>Zuständigkeit</th>
            <th>Name</th>
            <th>Vorname</th>
            <th>Tel</th>
            <th>Adresse</th>
            <th>PLZ</th>
            <th>Ort</th>
            <th>Land</th>
            <th>Mail</th>
            <th>Organisation</th>
            <th>Raumnr</th>
        </tr>
    </thead>
    <tbody>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row["idTABELLE_Ansprechpersonen"]?? '') . "</td>";
    echo "<td>" . htmlspecialchars($row["Zuständigkeit"]?? '') . "</td>";
    echo "<td>" . htmlspecialchars($row["Name"]?? '') . "</td>";
    echo "<td>" . htmlspecialchars($row["Vorname"]?? '') . "</td>";
    echo "<td>" . htmlspecialchars($row["Tel"]?? '') . "</td>";
    echo "<td>" . htmlspecialchars($row["Adresse"]?? '') . "</td>";
    echo "<td>" . htmlspecialchars($row["PLZ"]?? '') . "</td>";
    echo "<td>" . htmlspecialchars($row["Ort"]?? '') . "</td>";
    echo "<td>" . htmlspecialchars($row["Land"]?? '') . "</td>";
    echo "<td>" . htmlspecialchars($row["Mail"]?? '') . "</td>";
    echo "<td>" . htmlspecialchars($row["Organisation"]?? '') . "</td>";
    echo "<td>" . htmlspecialchars($row["Raumnr"]?? '') . "</td>";
    echo "</tr>";
}
echo "</tbody></table>";

$stmt->close();
$mysqli->close();
?>


<script>
    var personID;
    var tablePersons;
    $(document).ready(function () {
        tablePersons = new DataTable('#tablePersons', {
            columnDefs: [
                {
                    targets: [0],
                    visible: false,
                    searchable: false
                }
            ],
            select: true,
            paging: true,
            order: [[2, "asc"]],
            pagingType: "simple",
            lengthChange: false,
            pageLength: 10,
            language: {
                url: "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json",
                search: ""

            }
        });

        $('#tablePersons tbody').on('click', 'tr', function () {
            tablePersons.rows('.selected').deselect();
            tablePersons.row(this).select();
            personID = tablePersons.row(this).data()[0];
            $.ajax({
                url: "getChangePersonToProjectField.php",
                data: {"personID": personID},
                type: "GET",
                success: function (data) {
                    $("#addPersonToProject").html(data);
                }
            });
        });
    });
</script>