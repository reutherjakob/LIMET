<?php
// 25 FX
require_once 'utils/_utils.php';
check_login();

$id = getPostInt("id");
$mysqli = utils_connect_sql();

$sql = "SELECT tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung, tabelle_räume.Geschoss, tabelle_räume.`Raumbereich Nutzer`
        FROM tabelle_räume
        INNER JOIN (
            tabelle_verwendungselemente
            INNER JOIN tabelle_räume_has_tabelle_elemente
            ON tabelle_verwendungselemente.id_Verwendungselement = tabelle_räume_has_tabelle_elemente.id
        )
        ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume
        WHERE tabelle_verwendungselemente.id_Standortelement = ?";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

echo "<div class='table-responsive'>
        <table class='table table-striped table-sm' id='tableElementVerwendungsdaten'>
            <thead>
                <tr>
                    <th>Raumnr</th>
                    <th>Raumbezeichnung</th>
                    <th>Geschoss</th>
                    <th>Raumbereich Nutzer</th>
                </tr>
            </thead>
            <tbody>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row["Raumnr"]) . "</td>";
    echo "<td>" . htmlspecialchars($row["Raumbezeichnung"]) . "</td>";
    echo "<td>" . htmlspecialchars($row["Geschoss"]) . "</td>";
    echo "<td>" . htmlspecialchars($row["Raumbereich Nutzer"]) . "</td>";
    echo "</tr>";
}

echo "</tbody></table></div>";

$stmt->close();
$mysqli->close();
?>

<script>
    $("#tableElementVerwendungsdaten").DataTable({
        paging: false,
        searching: false,
        info: false,
        language: { url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json" },
        scrollY: '20vh',
        scrollCollapse: true
    });
</script>
