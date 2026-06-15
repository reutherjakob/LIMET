<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
<head>
    <title> Get Rooms with Element 1 </title>
</head>
<body>

<div class="btn-group" id="hide0Wrapper_RwE">
    <div class="btn-group" id="variantenMassenWrapper">
        <label class="btn btn-sm btn-outline-primary" for="massenVariante">
            Var:
        </label>
        <select class="form-control form-control-sm" id="massenVariante">
            <option value="1">A</option>
            <option value="2">B</option>
            <option value="3">C</option>
            <option value="4">D</option>
            <option value="5">E</option>
            <option value="6">F</option>
            <option value="7">G</option>
            <option value="8">H</option>
            <option value="9">I</option>
            <option value="10">J</option>
            <option value="11">K</option>
            <option value="12">L</option>
            <option value="13">M</option>
            <option value="14">N</option>
            <option value="15">O</option>
            <option value="16">P</option>
            <option value="17">Q</option>
            <option value="18">R</option>
            <option value="19">S</option>
            <option value="20">T</option>
            <option value="21">U</option>
            <option value="22">V</option>
            <option value="23">W</option>
            <option value="24">X</option>
            <option value="25">Y</option>
            <option value="26">Z</option>
        </select>
        <button type="button" id="massenVarianteSetzen" class="btn btn-sm btn-outline-primary">
            <i class="far fa-edit"></i> Alle ändern
        </button>
        <button type="button" id="massenVarianteSpeichern" class="btn btn-sm btn-outline-success">
            <i class="far fa-save"></i> Alle speichern
        </button>
    </div>
    <input class="btn-check btn-sm" type="checkbox" id="hideZeroRows_RwE">
    <label class="btn btn-sm btn-outline-dark" for="hideZeroRows_RwE">
        Hide 0
    </label>
</div>


<?php
// 25 FX
require_once 'utils/_utils.php';
check_login();

$elementID = getPostInt("elementID", 0);
$projectID = (int)$_SESSION["projectID"];

// read raw POST strings in addition
$rawBestand = $_POST['bestand'] ?? null;
$rawVariantenID = $_POST['variantenID'] ?? null;

// convert to int if present
$bestand = ($rawBestand === null || $rawBestand === '') ? null : (int)$rawBestand;
$variantenID = ($rawVariantenID === null || $rawVariantenID === '') ? null : (int)$rawVariantenID;

$where = [];
$where[] = "tabelle_räume.tabelle_projekte_idTABELLE_Projekte = " . intval($projectID);
$where[] = "tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente = " . intval($elementID);

if ($bestand !== null) {
    $where[] = "tabelle_räume_has_tabelle_elemente.`Neu/Bestand` = " . $bestand;
}
if ($variantenID !== null) {
    $_SESSION["variantenID"] = $variantenID;
    $where[] = "tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten = " . $variantenID;
}

$whereClause = implode(' AND ', $where);


$mysqli = utils_connect_sql();
$sql = "SELECT
    tabelle_räume_has_tabelle_elemente.id,
    tabelle_räume.idTABELLE_Räume,
    tabelle_räume.Raumnr,
    tabelle_räume.Raumnummer_Nutzer,
    tabelle_räume.Raumbezeichnung,
    tabelle_räume.`Raumbereich Nutzer`,
    tabelle_räume_has_tabelle_elemente.Anzahl,
    tabelle_räume_has_tabelle_elemente.`Neu/Bestand`,
    tabelle_räume_has_tabelle_elemente.Standort,
    tabelle_räume_has_tabelle_elemente.Verwendung,
    tabelle_räume_has_tabelle_elemente.Kurzbeschreibung,
    tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten,
    tabelle_räume.Geschoss,
    tabelle_räume.Bauetappe,
    tabelle_räume.Bauabschnitt,
    tabelle_elemente.ElementID,
    tabelle_elemente.Bezeichnung AS ElementName
FROM tabelle_räume
INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume
INNER JOIN tabelle_elemente ON tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente = tabelle_elemente.idTABELLE_Elemente
WHERE $whereClause
ORDER BY tabelle_räume.Raumnr;";

$result = $mysqli->query($sql);

$columns = [
    ["id", "ID"],
    ["Raumnr", "Raum Nr."],
    ["Raumbezeichnung", "Raumbez."],
    ["Raumbereich Nutzer", "Raumbereich"],
    ["Geschoss", "<i class='fas fa-layer-group'><label style='display: none;'>Geschoss</label></i>"],
    ["Bauetappe", "Bauetappe"],
    ["Bauabschnitt", "Bauabschnitt"],
    ["Anzahl", "Anzahl"],
    ["Variante", "Variante"],
    ["Neu/Bestand", "Bestand"],
    ["Standort", "Standort"],
    ["Verwendung", "Verwendung"],
    ["", ""],
    ["", ""],
    ["tabelle_Varianten_idtabelle_Varianten", "MT-Variante"],
    ["Standort", "MT-Standort"],
    ["Verwendung", "MT-Verwendung"],
    ["Neu/Bestand", "MT-Bestand"],
    ["Anzahl", "MT-Anzahl"],
    ["Kurzbeschreibung", "MT-Kommentar"],
    ["ElementID", "Element-ID"],
    ["ElementName", "Element-Bezeichnung"]
];

// Helper for select options
function selectOption($value, $selectedValue, $label): string
{
    $selected = ($value == $selectedValue) ? ' selected' : '';
    return "<option value='$value'$selected>$label</option>";
}

// Helper for safe output
function safe($str): string
{
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

echo "<table class='table table-striped table-bordered table-sm table-hover border border-light border-5' id='tableRoomsWithElement'><thead><tr>";
foreach ($columns as [$key, $label]) {
    if ($key === "Geschoss") {
        echo "<th data-bs-toggle='tooltip' title='Geschoss'>$label</th>";
    } else {
        echo "<th>" . safe($label) . "</th>";
    }
}

echo "</tr></thead><tbody>";

$options = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    // ID
    echo "<td data-order='" . safe($row["id"]) . "'>" . safe($row["id"]) . "</td>";

    // Raum Nr. (GCP project uses Raumnummer_Nutzer)
    $raumNr = ($_SESSION["projectName"] === "GCP") ? $row["Raumnummer_Nutzer"] : $row["Raumnr"];
    echo "<td data-order='" . safe($raumNr) . "'>" . safe($raumNr) . "</td>";

    // Raumbez.
    echo "<td data-order='" . safe($row["Raumbezeichnung"]) . "'>" . safe($row["Raumbezeichnung"]) . "</td>";
    // Raumbereich
    echo "<td data-order='" . safe($row["Raumbereich Nutzer"]) . "'>" . safe($row["Raumbereich Nutzer"]) . "</td>";
    // Geschoss
    echo "<td data-order='" . safe($row["Geschoss"]) . "'>" . safe($row["Geschoss"]) . "</td>";
    // Bauetappe
    echo "<td data-order='" . safe($row["Bauetappe"]) . "'>" . safe($row["Bauetappe"]) . "</td>";
    // Bauabschnitt
    echo "<td data-order='" . safe($row["Bauabschnitt"]) . "'>" . safe($row["Bauabschnitt"]) . "</td>";

    // Anzahl (editable)
    echo "<td data-order='" . intval($row["Anzahl"]) . "'><input class='form-control form-control-sm' type='text' id='amount" . safe($row["id"]) . "' value='" . intval($row["Anzahl"]) . "' size='2'></td>";

    // Variante (editable)
    echo "<td data-order='" . safe($row["tabelle_Varianten_idtabelle_Varianten"]) . "'><select class='form-control form-control-sm' id='variante" . safe($row["id"]) . "'>";
    $selected = $row["tabelle_Varianten_idtabelle_Varianten"];
    foreach ($options as $index => $option) {
        echo selectOption($index + 1, $selected, $option);
    }
    echo "</select></td>";

    // Bestand (editable)
    echo "<td data-order='" . safe($row["Neu/Bestand"]) . "'><select class='form-control form-control-sm' id='bestand" . safe($row["id"]) . "'>";
    echo selectOption(0, $row["Neu/Bestand"], "Ja");
    echo selectOption(1, $row["Neu/Bestand"], "Nein");
    echo "</select></td>";

    // Standort (editable)
    echo "<td data-order='" . safe($row["Standort"]) . "'><select class='form-control form-control-sm' id='Standort" . safe($row["id"]) . "'>";
    echo selectOption(0, $row["Standort"], "Nein");
    echo selectOption(1, $row["Standort"], "Ja");
    echo "</select></td>";

    // Verwendung (editable)
    echo "<td data-order='" . safe($row["Verwendung"]) . "'><select class='form-control form-control-sm' id='Verwendung" . safe($row["id"]) . "'>";
    echo selectOption(0, $row["Verwendung"], "Nein");
    echo selectOption(1, $row["Verwendung"], "Ja");
    echo "</select>";

    // Kommentar (popover)
    $Kurzbeschreibung = trim($row["Kurzbeschreibung"] ?? "");
    $buttonClass = $Kurzbeschreibung === "" ? "btn-outline-secondary" : "btn-outline-dark";
    $iconClass = $Kurzbeschreibung === "" ? "fa fa-comment-slash" : "fa fa-comment";
    $dataAttr = "data-description='" . safe($Kurzbeschreibung) . "'";
    ?>
    <td>
        <button type="button"
                class="btn btn-sm <?php echo $buttonClass; ?> comment-btn" <?php echo $dataAttr; ?>
                id="<?php echo $row["id"]; ?>" title="Kommentar"><i class="<?php echo $iconClass; ?>"></i>
        </button>
    </td>
    <?php

    // Save button
    echo "<td data-order=''><button type='button' id='" . safe($row["id"]) . "' class='btn btn-warning btn-sm' value='saveElement'><i class='far fa-save'></i></button></td>";

    // Additional MT columns and element info
    echo "<td data-order='" . safe($row["tabelle_Varianten_idtabelle_Varianten"]) . "'>" . safe($row["tabelle_Varianten_idtabelle_Varianten"]) . "</td>";
    echo "<td data-order='" . safe($row["Standort"]) . "'>" . safe($row["Standort"]) . "</td>";
    echo "<td data-order='" . safe($row["Verwendung"]) . "'>" . safe($row["Verwendung"]) . "</td>";
    echo "<td data-order='" . safe($row["Neu/Bestand"]) . "'>" . safe($row["Neu/Bestand"]) . "</td>";
    echo "<td data-order='" . intval($row["Anzahl"]) . "'>" . intval($row["Anzahl"]) . "</td>";
    echo "<td data-order='" . safe($row["Kurzbeschreibung"]) . "'>" . safe($row["Kurzbeschreibung"]) . "</td>";
    echo "<td data-order='" . safe($row["ElementID"]) . "'>" . safe($row["ElementID"]) . "</td>";
    echo "<td data-order='" . safe($row["ElementName"]) . "'>" . safe($row["ElementName"]) . "</td>";
    echo "</tr>";
}
echo "</tbody></table>";
$mysqli->close();
?>


<script src="utils/_utils.js"></script>
<script charset="utf-8" type="module">
    import CustomPopover from './utils/_popover.js';

    $(document).ready(function () {
        window.tableRoomsWithElement = new DataTable('#tableRoomsWithElement', {
            columnDefs: [
                {
                    targets: [0, 14, 15, 16, 17, 18, 19, 20, 21],
                    visible: false,
                    searchable: false
                }
            ],
            paging: false,
            searching: true,
            info: true,
            select: true,
            order: [[1, 'asc']],
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'All']],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json',
                search: ""
            },
            layout: {
                topStart: null,
                topEnd: ['buttons', 'search'],
                bottomStart: ["info"],
                bottomEnd: []
            },
            buttons: [
                {
                    extend: 'excel',
                    exportOptions: {
                        columns: [1, 2, 3, 4, 5, 6, 14, 15, 16, 17, 18, 19, 20, 21]// 12,
                    },
                    text: '<i class="fas fa-file-excel me-2"></i> Excel', // Add Font Awesome icon
                    className: 'btn btn-sm btn-outline-success bg-white', // Bootstrap small
                }
            ],
            initComplete: function () {
                $("#CHRME").html("");
                $('#CHRME').append($('#hide0Wrapper_RwE'));
                if (typeof tableRoomsWithElement.buttons === 'function') {
                    tableRoomsWithElement.buttons().container().appendTo($('#CHRME'));
                }
                $('#tableRoomsWithElement_wrapper .dt-search label').remove();
                $('#tableRoomsWithElement_wrapper .dt-search').children().removeClass("form-control form-control-sm").addClass("btn btn-sm btn-outline-dark").appendTo('#CHRME');

                let filterIndex = $.fn.dataTable.ext.search.indexOf(hideZeroFilter_RwE);
                if (filterIndex !== -1) {
                    $.fn.dataTable.ext.search.splice(filterIndex, 1);
                }
                $.fn.dataTable.ext.search.push(hideZeroFilter_RwE);

                $("#hideZeroRows_RwE").on("change", function () {
                    tableRoomsWithElement.draw();
                });

                function hideZeroFilter_RwE(settings, data, dataIndex) {
                    if (settings.nTable.id !== 'tableRoomsWithElement') {
                        return true;
                    }        //console.log(data);
                    let hideZero = $("#hideZeroRows_RwE").is(":checked");
                    let row = tableRoomsWithElement.row(dataIndex).node();
                    let amount = $(row).find('input[id^="amount"]').val();
                    //  let name = $(row).find('span[id^="ElementName"').val();
                    amount = parseInt(amount) || 0;
                    return !(hideZero && (amount === 0));
                }
            }
        });


        CustomPopover.init('.comment-btn', {
            onSave: function (trigger, newText) {
                // console.log("Custompopover: ", newText);
                trigger.dataset.description = newText;
                let id = trigger.id;
                $.ajax({
                    url: "saveRoomElementComment.php",
                    data: {
                        "comment": newText,
                        "id": id
                    },
                    type: "POST",
                    success: function (data) {
                        makeToaster(data.trim(), true);
                        $(".comment-btn[id='" + id + "']").attr('data-description', newText).data('description', newText);
                        if (newText !== "") {
                            $(".comment-btn[id='" + id + "']").removeClass('btn-outline-secondary');
                            $(".comment-btn[id='" + id + "']").addClass('btn-outline-dark');
                            $(".comment-btn[id='" + id + "']").find('i').removeClass('fa fa-comment-slash');
                            $(".comment-btn[id='" + id + "']").find('i').addClass('fa fa-comment');
                        } else {
                            $(".comment-btn[id='" + id + "']").removeClass('btn-outline-dark');
                            $(".comment-btn[id='" + id + "']").addClass('btn-outline-secondary');
                            $(".comment-btn[id='" + id + "']").find('i').removeClass('fa fa-comment');
                            $(".comment-btn[id='" + id + "']").find('i').addClass('fa fa-comment-slash');
                        }
                    }
                });
            }
        });

        // === MASSENBEARBEITUNG VARIANTEN ===

        // Alle Varianten auf neue Wert setzen
        $("#massenVarianteSetzen").click(function () {
            console.log("massenVarianteSezen");
            let neueVariante = $("#massenVariante").val();
            if (!neueVariante) {
                alert("Bitte eine neue Variante auswählen!");
                return;
            }

            // Alle Variant-Selects in der Tabelle aktualisieren
            $("#tableRoomsWithElement select[id^='variante']").each(function () {
                $(this).val(neueVariante);
            });

            let variantName = $("#massenVariante").find("option:selected").text();
            makeToaster("Alle Varianten auf '" + variantName + "' gesetzt!", true);
        });

        // Alle Varianten massenhaft speichern
        $("#massenVarianteSpeichern").click(function () {
            let rows = [];

            // Alle Reihen durchgehen und aktuelle Werte sammeln
            $("#tableRoomsWithElement select[id^='variante']").each(function () {
                let id = $(this).attr('id').replace('variante', '');
                let amount = $("#amount" + id).val();
                let variantenID = $(this).val();
                let bestand = $("#bestand" + id).val();
                let standort = $("#Standort" + id).val();
                let verwendung = $("#Verwendung" + id).val();
                let comment = $(".comment-btn[id='" + id + "']").attr('data-description') || "";

                if (standort === '0' && verwendung === '0') {
                    alert("Standort und Verwendung kann nicht Nein sein!");
                    return false;
                }

                rows.push({
                    id: id,
                    comment: comment,
                    amount: amount,
                    variantenID: variantenID,
                    bestand: bestand,
                    standort: standort,
                    verwendung: verwendung
                });
            });

            if (rows.length === 0) {
                makeToaster("Keine Einträge zu speichern!", true);
                return;
            }

            // AJAX-Anfragen für alle Reihen (seriell)
            let promiseChain = Promise.resolve();

            rows.forEach(function (row, index) {
                promiseChain = promiseChain.then(function () {
                    return $.ajax({
                        url: "saveRoombookEntry.php",
                        data: row,
                        type: "POST"
                    });
                });
            });

            promiseChain.then(function () {
                makeToaster("Alle " + rows.length + " Einträge erfolgreich gespeichert!", true);
            }).catch(function (error) {
                makeToaster("Fehler beim Speichern!", false);
            });
        });
    });

    $("button[value='saveElement']").click(function () {
        let id = this.id;
        let comment = $(".comment-btn[id='" + id + "']").attr('data-description');
        let amount = $("#amount" + id).val();
        let variantenID = $("#variante" + id).val();
        let bestand = $("#bestand" + id).val();
        let standort = $("#Standort" + id).val();
        let verwendung = $("#Verwendung" + id).val();
        if (standort === '0' && verwendung === '0') {
            alert("Standort und Verwendung kann nicht Nein sein!");
        } else {
            console.log(id, typeof (id));
            $.ajax({
                url: "saveRoombookEntry.php",
                data: {
                    "comment": comment,
                    "id": id,
                    "amount": amount,
                    "variantenID": variantenID,
                    "bestand": bestand,
                    "standort": standort,
                    "verwendung": verwendung
                },
                type: "POST",
                success: function (data) {
                    makeToaster(data.trim(), true);
                }
            });
        }
    });
</script>
</body>
</html>