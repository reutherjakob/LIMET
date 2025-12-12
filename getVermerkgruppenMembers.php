<?php
// 25 FX
require_once 'utils/_utils.php';
check_login();

$mysqli = utils_connect_sql();
$gruppenID = getPostInt('gruppenID', 0);
if (0 === $gruppenID) {
    echo "Ungültige Gruppen-ID.";
    exit;
}

$sql = "SELECT tabelle_ansprechpersonen.idTABELLE_Ansprechpersonen, tabelle_ansprechpersonen.Name, tabelle_ansprechpersonen.Vorname, tabelle_Vermerkgruppe_has_tabelle_ansprechpersonen.Anwesenheit, tabelle_Vermerkgruppe_has_tabelle_ansprechpersonen.Verteiler
                FROM tabelle_ansprechpersonen INNER JOIN tabelle_Vermerkgruppe_has_tabelle_ansprechpersonen ON tabelle_ansprechpersonen.idTABELLE_Ansprechpersonen = tabelle_Vermerkgruppe_has_tabelle_ansprechpersonen.tabelle_ansprechpersonen_idTABELLE_Ansprechpersonen
                WHERE (((tabelle_Vermerkgruppe_has_tabelle_ansprechpersonen.tabelle_Vermerkgruppe_idtabelle_Vermerkgruppe)=?));";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param('i', $gruppenID);
$stmt->execute();
$result = $stmt->get_result();

echo "<table class='table table-striped table-bordered table-sm table-hover border border-light border-5' id='tableVermerkGroupMembers'  >
        <thead><tr>
        <th></th>
        <th>Name</th>
        <th>Vorname</th>
        <th>Anwesenheit</th>
        <th>Verteiler</th>
        </tr></thead>
        <tbody>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td><button type='button' id='" . $row["idTABELLE_Ansprechpersonen"] . "' class='btn btn-outline-danger btn-sm' value='deleteVermerkGroupMember'><i class='fas fa-minus'></i></button></td>";
    echo "<td>" . $row["Name"] . "</td>";
    echo "<td>" . $row["Vorname"] . "</td>";
    echo "<td><div class='form-check'>";
    if ($row["Anwesenheit"] == "0") {
        echo "<input type='checkbox' class='form-check-input' id='" . $row["idTABELLE_Ansprechpersonen"] . "' value='anwesenheitCheck'>";
    } else {
        echo "<input type='checkbox' class='form-check-input' id='" . $row["idTABELLE_Ansprechpersonen"] . "' value='anwesenheitCheck' checked='true'>";
    }
    echo "</div></td>";
    echo "<td><div class='form-check'>";
    if ($row["Verteiler"] == "0") {
        echo "<input type='checkbox' class='form-check-input' id='" . $row["idTABELLE_Ansprechpersonen"] . "' value='verteilerCheck'>";
    } else {
        echo "<input type='checkbox' class='form-check-input' id='" . $row["idTABELLE_Ansprechpersonen"] . "' value='verteilerCheck' checked='true'>";
    }
    echo "</div></td>";
    echo "</tr>";
}
echo "</tbody></table>";
$mysqli->close();
?>


<script>
    $('#tableVermerkGroupMembers').DataTable({
        paging: false,
        searching: true,
        info: false,
        order: [[1, 'asc']],
        columnDefs: [
            {
                targets: 0,
                visible: true,
                searchable: false,
                orderable: false
            }
        ],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.5/i18n/de-DE.json'
        },
        scrollY: '20vh',
        scrollCollapse: true
    });


    $("button[value='deleteVermerkGroupMember']").click(function () {
        let id = this.id;
        let groupID = "<?php echo $gruppenID ?>";
        if (id !== "" && groupID !== "") {
            $.ajax({
                url: "deletePersonFromVermerkGroup.php",
                data: {"ansprechpersonenID": id, "groupID": groupID},
                type: "POST",
                success: function (data) {
                    makeToaster(data, true);
                    document.getElementById('pdfPreview').src += '';
                    $.ajax({
                        url: "getVermerkgruppenMembers.php",
                        type: "POST",
                        data: {"gruppenID": groupID},
                        success: function (data) {
                            $("#vermerkGroupMembers").html(data);
                            $.ajax({
                                url: "getPossibleVermerkGruppenMembers.php",
                                type: "POST",
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


    $("input[value='anwesenheitCheck']").change(function () {
        let anwesenheit
        if ($(this).prop('checked') === true) {
            anwesenheit = 1;
        } else {
            anwesenheit = 0;
        }
        let ansprechpersonenID = this.id;
        let groupID = "<?php echo $gruppenID ?>";
        if (anwesenheit !== "" && ansprechpersonenID !== "" && groupID !== "") {
            $.ajax({
                url: "saveVermerkgruppenPersonenanwesenheit.php",
                data: {"ansprechpersonenID": ansprechpersonenID, "anwesenheit": anwesenheit, "groupID": groupID},
                type: "POST",
                success: function (data) {
                    alert(data);
                    $.ajax({
                        url: "getVermerkgruppenMembers.php",
                        type: "POST",
                        success: function (data) {
                            $("#vermerkGroupMembers").html(data);
                        }
                    });
                }
            });
        } else {
            alert("Anwesenheit nicht lesbar!");
        }
    });

    $("input[value='verteilerCheck']").change(function () {
        let verteiler;
        if ($(this).prop('checked') === true) {
            verteiler = 1;
        } else {
            verteiler = 0;
        }
        let ansprechpersonenID = this.id;
        let groupID = "<?php echo $gruppenID ?>";
        if (verteiler !== "" && ansprechpersonenID !== "" && groupID !== "") {
            $.ajax({
                url: "saveVermerkgruppenPersonenverteiler.php",
                data: {"ansprechpersonenID": ansprechpersonenID, "verteiler": verteiler, "groupID": groupID},
                type: "POST",
                success: function (data) {
                    alert(data);
                    $.ajax({
                        url: "getVermerkgruppenMembers.php",
                        type: "POST",
                        success: function (data) {
                            $("#vermerkGroupMembers").html(data);
                        }
                    });
                }
            });
        } else {
            alert("VertEeiler nicht lesbar!");
        }
    });
</script>
