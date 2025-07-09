<!-- 13.2.25: Reworked -->
<?php
if (!function_exists('utils_connect_sql')) {
    include "utils/_utils.php";
}
check_login();;
?>
<!DOCTYPE html>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
<html lang="de">
<head>
    <title>Get Vermerk Untergruppe to Gruppe </title></head>
<body>
<?php
$mysqli = utils_connect_sql();
$sql = "SELECT tabelle_Vermerkuntergruppe.idtabelle_Vermerkuntergruppe, tabelle_Vermerkuntergruppe.Untergruppenname, tabelle_Vermerkuntergruppe.Untergruppennummer
                FROM tabelle_Vermerkuntergruppe
                WHERE (((tabelle_Vermerkuntergruppe.tabelle_Vermerkgruppe_idtabelle_Vermerkgruppe)=" . filter_input(INPUT_GET, 'vermerkGruppenID') . "))
                ORDER BY Untergruppennummer;";
$result = $mysqli->query($sql);

echo "<table class='table responsive compact table-striped table-bordered table-sm table-hover  border border-light border-5' id='tableVermerkUnterGruppe'>
                <thead><tr>
                <th>ID</th>
                <th></th>
                <th>Nummer</th>
                <th>Name</th>
                <th>GruppenID</th>
                </tr></thead><tbody>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['idtabelle_Vermerkuntergruppe'] . "</td>";
    echo "<td><button type='button' id='" . $row['idtabelle_Vermerkuntergruppe'] . "' class='btn btn-outline-dark btn-sm' value='changeVermerkuntergruppe'><i class='fas fa-pencil-alt'></i></button></td>";
    echo "<td>" . $row['Untergruppennummer'] . "</td>";
    echo "<td>" . $row['Untergruppenname'] . "</td>";
    echo "<td>" . filter_input(INPUT_GET, 'vermerkGruppenID') . "</td>";
    echo "</tr>";
}
echo "</tbody></table>";
$mysqli->close();
?>

</body>

<!-- Modal zum Hinzufügen/Ändern einer UnterGruppe -->
<div class='modal fade' id='changeUnterGroupModal' role='dialog' tabindex="-1">
    <div class='modal-dialog modal-md'>
        <div class='modal-content'>
            <div class='modal-header'>
                <h4 class='modal-title'>Untergruppendaten</h4>
                <button type='button' class='close' data-bs-dismiss='modal'>&times;</button>
            </div>
            <div class='modal-body' id='untergruppenMbody'>
                <form role="form">
                    <div class="form-group">
                        <label for="unterGruppenNummer">Nummer:</label>
                        <input type="text" class="form-control form-control-sm" id="unterGruppenNummer"/>
                    </div>
                    <div class="form-group">
                        <label for="unterGruppenName">Name:</label>
                        <input type="text" class="form-control form-control-sm" id="unterGruppenName"/>
                    </div>
                </form>
            </div>
            <div class='modal-footer'>
                <input type='button' id='addUnterGroup' class='btn btn-success btn-sm' value='Hinzufügen'
                       data-bs-dismiss='modal'>
                <input type='button' id='saveUnterGroup' class='btn btn-warning btn-sm' value='Speichern'
                       data-bs-dismiss='modal'>
                <button type='button' class='btn btn-default btn-sm' data-bs-dismiss='modal'>Abbrechen</button>
            </div>
        </div>
    </div>
</div>

<script src="utils/_utils.js"></script>
<script>
    $(document).ready(function () {
        $('#topDivSearch').remove();
        document.getElementById("buttonNewVermerkuntergruppe").style.visibility = "visible";
        let tableVermerkUnterGruppe = $('#tableVermerkUnterGruppe').DataTable({
            columnDefs: [
                {
                    "targets": [0, 4],
                    "visible": false,
                    "searchable": false
                }
            ],
            dom: '<"#topDiv.top-container d-flex"<"col-md-6 justify-content-start"><"#topDivSearch.col-md-6"f>>t<"bottom d-flex" <"col-md-6 justify-content-start"i><"col-md-6 d-flex align-items-center justify-content-end"lp>>',
            paging: true,
            pagingType: "simple",
            pageLength: 10,
            lengthChange: true,
            searching: true,
            info: true,
            order: [[1, "asc"]],
            select: true,
            responsive: true,
            language: {
                'url': 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json',
                search: "", searchPlaceholder: "Suche..."
            }, initComplete: function () {
                $('#topDivSearch label').remove();
                $('#topDivSearch').removeClass("col-md-6").children().children().removeClass("form-control form-control-sm");
                $('#topDivSearch').appendTo('#CardHeaderVermerkE').children().children().addClass("btn btn-sm btn-outline-dark");
            }
        });

        $('#tableVermerkUnterGruppe tbody').on('click', 'tr', function () {
            untergruppenID = tableVermerkUnterGruppe.row($(this)).data()[0];
            document.getElementById("unterGruppenNummer").value = tableVermerkUnterGruppe.row($(this)).data()[2];
            document.getElementById("unterGruppenName").value = tableVermerkUnterGruppe.row($(this)).data()[3];
            $("#vermerke").show();
            $.ajax({
                url: "getVermerkeToUntergruppe.php",
                data: {
                    "vermerkUntergruppenID": tableVermerkUnterGruppe.row($(this)).data()[0],
                    "vermerkGruppenID": tableVermerkUnterGruppe.row($(this)).data()[4]
                },
                type: "GET",
                success: function (data) {
                    $("#vermerke").html(data);
                }
            });
        });
    });

    $("button[value='changeVermerkuntergruppe']").click(function () {
        document.getElementById("saveUnterGroup").style.display = "inline";
        document.getElementById("addUnterGroup").style.display = "none";
        $('#changeUnterGroupModal').modal('show');
    });

    //$("button[value='Neue Vermerkuntergruppe']").click(function(){
    $("#buttonNewVermerkuntergruppe").click(function () {
        document.getElementById("saveUnterGroup").style.display = "none";
        document.getElementById("addUnterGroup").style.display = "inline";
        $('#changeUnterGroupModal').modal('show');
    });

    $("#addUnterGroup").click(function () {
        var untergruppenName = $("#unterGruppenName").val();
        var untergruppenNummer = $("#unterGruppenNummer").val();
        var id = <?php echo filter_input(INPUT_GET, 'vermerkGruppenID') ?>;
        if (untergruppenName !== "" && untergruppenNummer !== "") {
            $.ajax({
                url: "addVermerkUnterGroup.php",
                data: {"untergruppenName": untergruppenName, "untergruppenNummer": untergruppenNummer, "gruppenID": id},
                type: "GET",
                success: function (data) {
                    makeToaster(data,true);
                    $.ajax({
                        url: "getVermerkeuntergruppenToGruppe.php",
                        data: {"vermerkGruppenID": id},
                        type: "GET",
                        success: function (data) {
                            $("#vermerkUntergruppen").html(data);
                            // Neu laden der PDF-Vorschau
                            document.getElementById('pdfPreview').src += '';
                        }
                    });
                }
            });
        } else {
            makeToaster("Bitte alle Felder ausfüllen!", false);
        }
    });

    $("#saveUnterGroup").click(function () {
        var untergruppenName = $("#unterGruppenName").val();
        var untergruppenNummer = $("#unterGruppenNummer").val();
        var id = <?php echo filter_input(INPUT_GET, 'vermerkGruppenID') ?>;
        if (untergruppenName !== "" && untergruppenNummer !== "") {
            $.ajax({
                url: "saveVermerkUnterGroup.php",
                data: {
                    "untergruppenName": untergruppenName,
                    "untergruppenNummer": untergruppenNummer,
                    "untergruppenID": untergruppenID
                },
                type: "GET",
                success: function (data) {
                    makeToaster(data, true);
                    $.ajax({
                        url: "getVermerkeuntergruppenToGruppe.php",
                        data: {"vermerkGruppenID": id},
                        type: "GET",
                        success: function (data) {
                            $("#vermerkUntergruppen").html(data);
                            // Neu laden der PDF-Vorschau
                            document.getElementById('pdfPreview').src += '';
                        }
                    });
                }
            });
        } else {
            makeToaster("Bitte alle Felder ausfüllen!", false);
        }
    });
</script>

</html>