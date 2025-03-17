<?php
// V2.0: 2024-11-29, Reuther & Fux
if (!function_exists('utils_connect_sql')) {
    include "_utils.php";
}
include "_format.php";
check_login();

$mysqli = utils_connect_sql();

// SQL Queries
$sql_new = "SELECT 
    Sum(tabelle_räume_has_tabelle_elemente.Anzahl * tabelle_projekt_varianten_kosten.Kosten) AS Summe_Neu,
    tabelle_elemente.ElementID
FROM 
    tabelle_räume_has_tabelle_elemente 
INNER JOIN 
    tabelle_projekt_varianten_kosten 
    ON (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) 
    AND (tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten = tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten)
INNER JOIN
    tabelle_elemente
    ON tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente = tabelle_elemente.idTABELLE_Elemente
WHERE 
    tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume = ? 
    AND tabelle_räume_has_tabelle_elemente.Standort = 1 
    AND tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte = ? 
    AND tabelle_räume_has_tabelle_elemente.`Neu/Bestand` = 1
GROUP BY tabelle_elemente.ElementID;";

$sql_existing = "SELECT 
    Sum(tabelle_räume_has_tabelle_elemente.Anzahl * tabelle_projekt_varianten_kosten.Kosten) AS Summe_Bestand,
    tabelle_elemente.ElementID
FROM 
    tabelle_räume_has_tabelle_elemente 
INNER JOIN 
    tabelle_projekt_varianten_kosten 
    ON (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) 
    AND (tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten = tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten)
INNER JOIN
    tabelle_elemente
    ON tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente = tabelle_elemente.idTABELLE_Elemente
WHERE 
    tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume = ?
    AND tabelle_räume_has_tabelle_elemente.Standort = 1 
    AND tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte = ?
    AND tabelle_räume_has_tabelle_elemente.`Neu/Bestand` = 0
GROUP BY tabelle_elemente.ElementID;";

$sql_room_elements = "SELECT tabelle_räume_has_tabelle_elemente.id, tabelle_räume_has_tabelle_elemente.TABELLE_Geraete_idTABELLE_Geraete,
       tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente,
       tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten, tabelle_räume_has_tabelle_elemente.Anzahl, 
       tabelle_elemente.ElementID, tabelle_elemente.Kurzbeschreibung As `Elementbeschreibung`, tabelle_varianten.Variante, 
       tabelle_elemente.Bezeichnung, tabelle_geraete.GeraeteID, tabelle_hersteller.Hersteller, tabelle_geraete.Typ, 
       tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, tabelle_räume_has_tabelle_elemente.Standort,  
       tabelle_räume_has_tabelle_elemente.Verwendung, tabelle_räume_has_tabelle_elemente.Kurzbeschreibung, 
       tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, 
       tabelle_räume_has_tabelle_elemente.TABELLE_Geraete_idTABELLE_Geraete
FROM tabelle_varianten INNER JOIN (tabelle_hersteller RIGHT JOIN ((tabelle_räume_has_tabelle_elemente LEFT JOIN tabelle_geraete ON tabelle_räume_has_tabelle_elemente.TABELLE_Geraete_idTABELLE_Geraete = tabelle_geraete.idTABELLE_Geraete) INNER JOIN tabelle_elemente ON tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente = tabelle_elemente.idTABELLE_Elemente) ON tabelle_hersteller.idtabelle_hersteller = tabelle_geraete.tabelle_hersteller_idtabelle_hersteller) ON tabelle_varianten.idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten
WHERE (((tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume)=?))
ORDER BY   tabelle_räume_has_tabelle_elemente.Anzahl DESC ;";

// Function to execute query and calculate costs
function calculateCosts($mysqli, $sql, $roomID, $projectID)
{
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("ii", $roomID, $projectID);
    $stmt->execute();
    $result = $stmt->get_result();

    $sum = 0;
    $costs = ['ortsfest' => 0, 'ortsveränderlich' => 0];

    while ($row = $result->fetch_assoc()) {
        $summe = isset($row["Summe_Neu"]) ? (float)$row["Summe_Neu"] : (float)$row["Summe_Bestand"];
        $sum += $summe;
        if (str_starts_with($row["ElementID"] ?? '', '1') || str_starts_with($row["ElementID"] ?? '', '4')) {
            $costs['ortsfest'] += $summe;
        } else {
            $costs['ortsveränderlich'] += $summe;
        }

    }

    return ['sum' => $sum, 'costs' => $costs];
}

// Calculate costs
$new_costs = calculateCosts($mysqli, $sql_new, $_SESSION["roomID"], $_SESSION["projectID"]);
$existing_costs = calculateCosts($mysqli, $sql_existing, $_SESSION["roomID"], $_SESSION["projectID"]);

$SummeNeu = $new_costs['sum'];
$SummeBestand = $existing_costs['sum'];
$SummeGesamt = $SummeNeu + $SummeBestand;
$Kosten_ortsfest = $new_costs['costs']['ortsfest'] + $existing_costs['costs']['ortsfest'];
$Kosten_ortsveränderlich = $new_costs['costs']['ortsveränderlich'] + $existing_costs['costs']['ortsveränderlich'];

// Format money values
$formattedNumberGesamt = format_money_report($SummeGesamt);
$formattedNumberNeu = format_money_report($SummeNeu);
$formattedNumberBestand = format_money_report($SummeBestand);
$formattedKostenOrtsfest = format_money_report($Kosten_ortsfest);
$formattedKostenOrtsveränderlich = format_money_report($Kosten_ortsveränderlich);

// Fetch room elements
$stmt_room_elements = $mysqli->prepare($sql_room_elements);
$stmt_room_elements->bind_param("i", $_SESSION["roomID"]);
$stmt_room_elements->execute();
$result_room_elements = $stmt_room_elements->get_result();

$mysqli->close();
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <title>Room Elements Detailed</title>
</head>
<body>
<div class="d-flex align-items-center justify-content-between w-100">
    <?php
    $cost_fields = [
        'kosten_gesamt' => ['label' => 'Raumkosten', 'value' => $formattedNumberGesamt],
        'kosten_neu' => ['label' => 'Neu', 'value' => $formattedNumberNeu],
        'kosten_bestand' => ['label' => 'Bestand', 'value' => $formattedNumberBestand],
        'kosten_ortsfest' => ['label' => ' OF', 'value' => $formattedKostenOrtsfest],
        'kosten_ortsveränderlich' => ['label' => ' OV', 'value' => $formattedKostenOrtsveränderlich]
    ]; ?>

    <div class="d-flex flex-wrap justify-content-between">
        <?php foreach ($cost_fields as $id => $field): ?>
            <span class="badge rounded-pill bg-light text-dark m-1 p-2">
        <span class="fw-normal"><?php echo $field['label']; ?>:</span>
        <span class="fw-bold"><?php echo $field['value']; ?></span>
    </span>
        <?php endforeach; ?>
    </div>


    <?php if ($result_room_elements->num_rows > 0): ?>
        <div id="room-action-buttons" class="d-inline-flex text-nowrap align-items-center">
            <button type="button" class="btn btn-outline-dark " id="<?php echo $_SESSION["roomID"]; ?>"
                    data-bs-toggle="modal" data-bs-target="#copyRoomElementsModal" value="Rauminhalt kopieren">Inhalt
                kopieren
            </button>
            <button type="button" class="btn btn-outline-dark " id="<?php echo $_SESSION["roomID"]; ?>"
                    value="createRoombookPDF"><i class="far fa-file-pdf"></i> RB-PDF
            </button>
            <button type="button" class="btn btn-outline-dark" id="<?php echo $_SESSION["roomID"]; ?>"
                    value="createRoombookPDFCosts"><i class="far fa-file-pdf"></i> RB-Kosten-PDF
            </button>
            <div class=" btn btn-outline-dark">
                <input class="form-check-input" type="checkbox" id="hideZeroRows">
                <label class="form-check-label" for="hideZeroRows">
                    Hide 0
                </label>
            </div>
        </div>
    <?php endif; ?>
</div>

<table class="table table-sm compact table-responsiv table-striped border border-light border-5" id="tableRoomElements">
    <thead>
    <tr>
        <th>ID</th>
        <th>Element</th>
        <th>Var</th>
        <th>Stk</th>
        <th>Best</th>
        <th>Stand</th>
        <th>Verw</th>
        <th>Kom</th>
        <th>Verlauf</th>
        <th></th>

    </tr>
    </thead>
    <tbody>
    <?php while ($row = $result_room_elements->fetch_assoc()): ?>
        <tr>
            <td data-order="<?php echo $row["id"]; ?>"><?php echo $row["id"]; ?></td>
            <td data-order="<?php echo $row["ElementID"] . " " . $row["Bezeichnung"]; ?>">
                <span id="ElementName<?php echo $row["id"]; ?>"><?php echo $row["ElementID"] . " " . $row["Bezeichnung"]; ?></span>
            </td>
            <td data-order="<?php echo $row["tabelle_Varianten_idtabelle_Varianten"]; ?>">
                <label for="variante<?php echo $row["id"]; ?>" style="display: none;"></label><select
                        class="form-control form-control-sm"
                        id="variante<?php echo $row["id"]; ?>">
                    <?php
                    $options = ['A' => 1, 'B' => 2, 'C' => 3, 'D' => 4, 'E' => 5, 'F' => 6, 'G' => 7];
                    foreach ($options as $label => $value) {
                        $selected = ($row["tabelle_Varianten_idtabelle_Varianten"] == $value) ? "selected" : "";
                        echo "<option value='$value' $selected>$label</option>";
                    }
                    ?>
                </select>
            </td>
            <td data-order="<?php echo $row["Anzahl"]; ?>"><label style="display: none;"
                                                                  for="amount<?php echo $row["id"]; ?>"></label><input
                        class="form-control form-control-sm" type="text"
                        id="amount<?php echo $row["id"]; ?>"
                        value="<?php echo $row["Anzahl"]; ?>" size="1"></td>
            <td data-order="<?php echo $row["Neu/Bestand"]; ?>">
                <label for="bestand<?php echo $row["id"]; ?>" style="display: none;"></label><select
                        class="form-control form-control-sm"
                        id="bestand<?php echo $row["id"]; ?>">
                    <option value="0" <?php echo $row["Neu/Bestand"] == "0" ? "selected" : ""; ?>>Ja</option>
                    <option value="1" <?php echo $row["Neu/Bestand"] == "1" ? "selected" : ""; ?>>Nein</option>
                </select>
            </td>
            <td data-order="<?php echo $row["Standort"]; ?>">
                <label for="Standort<?php echo $row["id"]; ?>" style="display: none;"></label><select
                        class="form-control form-control-sm"
                        id="Standort<?php echo $row["id"]; ?>">
                    <option value="0" <?php echo $row["Standort"] == "0" ? "selected" : ""; ?>>Nein</option>
                    <option value="1" <?php echo $row["Standort"] == "1" ? "selected" : ""; ?>>Ja</option>
                </select></td>
            <td data-order="<?php echo $row["Verwendung"]; ?>"><label for="Verwendung<?php echo $row["id"]; ?>"
                                                                      style="display: none;"></label><select
                        class="form-control form-control-sm"
                        id="Verwendung<?php echo $row["id"]; ?>">
                    <option value="0" <?php echo $row["Verwendung"] == "0" ? "selected" : ""; ?>>Nein</option>
                    <option value="1" <?php echo $row["Verwendung"] == "1" ? "selected" : ""; ?>>Ja</option>
                </select></td>
            <td>

                <?php
                $Kurzbeschreibung = trim($row["Kurzbeschreibung"] ?? "");
                $buttonClass = $Kurzbeschreibung === "" ? "btn-outline-secondary" : "btn-outline-dark";
                $iconClass = $Kurzbeschreibung === "" ? "fa fa-comment-slash" : "fa fa-comment";
                $dataAttr = $Kurzbeschreibung === "" ? "data-description= '' " : "data-description='" . htmlspecialchars($Kurzbeschreibung, ENT_QUOTES, 'UTF-8') . "'";
                ?>

                <button type="button"
                        class="btn btn-sm <?php echo $buttonClass; ?> comment-btn" <?php echo $dataAttr; ?>
                        id="<?php echo $row["id"]; ?>" title="Kommentar"><i class="<?php echo $iconClass; ?>"></i>
                </button>
            </td>
            <td data-order="history">
                <button type="button" id="<?php echo $row["id"]; ?>" class="btn btn-sm btn-outline-dark"
                        value="history"><i
                            class="fas fa-history"></i></button>
            </td>
            <td data-order="saveElement">
                <button type="button" id="<?php echo $row["id"]; ?>" class="btn btn-sm btn-warning" value="saveElement">
                    <i class="far fa-save"></i></button>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>

<!-- Modal zum Kopieren des Rauminhalts -->
<div class='modal fade' id='copyRoomElementsModal' tabindex='-1' aria-labelledby='copyRoomElementsModalLabel'
     aria-hidden='true'>
    <div class='modal-dialog modal-xl'>
        <div class='modal-content'>

            <div class='modal-header'>
                <h5 class='modal-title' id='copyRoomElementsModalLabel'>Rauminhalt kopieren</h5>
                <p class='mb-0 ms-3'>(Bisher im Raum verortete Elemente werden hierdurch NICHT verändert!)</p>
                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
            </div>

            <div class='modal-body' id='mbodyCRE'>
            </div>
            <div class='modal-footer'>
                <button type='button' id='copyRoomElements' class='btn btn-primary'>Elemente kopieren</button>
                <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal zum Darstellen des Verlaufs -->
<div class='modal fade' id='historyModal' role='dialog'>
    <div class='modal-dialog modal-lg'>
        <!-- Modal content-->
        <div class='modal-content'>
            <div class='modal-header'>
                <h4 class='modal-title'>Verlauf </h4>
                <div class='' id="ElementName4Header"></div>
                <button type='button' class='close' data-bs-dismiss='modal'>&times;</button>
            </div>
            <div class='modal-body' id='mbodyHistory'>
            </div>
            <div class='modal-footer'>
                <button type='button' class='btn btn-default' data-bs-dismiss='modal'>Close</button>
            </div>
        </div>
    </div>
</div>

<script src="_utils.js"></script>
<script charset="utf-8" type="module">
    var tableRoomElements;

    function attachButtonListeners() {
        $("button[value='createRoombookPDF']").click(function () {
            window.open('/pdf_createRoombookPDF.php?roomID=' + this.id);//there are many ways to do this
        });

        $("button[value='createRoombookPDFCosts']").click(function () {
            window.open('/pdf_createRoombookPDFwithCosts.php?roomID=' + this.id);//there are many ways to do this
        });

        $("button[value='Rauminhalt kopieren']").click(function () {
            $("#copyRoomElementsModal").modal('show');
            if (typeof dt_search_counter !== 'undefined' && dt_search_counter !== null) {
                dt_search_counter = dt_search_counter + 1;  // Or dt_search_counter++;
            }
            let originRoomID = this.id;  // The ID of the current room
            $.ajax({
                url: "getRoomsToCopy.php",
                type: "GET",
                data: {
                    "originRoomID": originRoomID
                },
                success: function (data) {
                    $("#mbodyCRE").html(data);
                }
            });
        });

        $("button[value='history']").click(function () {
            let roombookID = this.id;
            let elementName = $("#ElementName" + roombookID).text();
            $.ajax({
                url: "getCommentHistory.php",
                type: "GET",
                data: {"roombookID": roombookID},
                success: function (data) {
                    $('#ElementName4Header').text(elementName);
                    $("#mbodyHistory").html(data);
                    $("#historyModal").modal('show');

                }
            });
        });
    }

    import CustomPopover from './_popover.js';

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

    // var tableRoomElements;
    $("button[value='saveElement']").click(function () {
        let id = this.id;
        //console.log(id)
        let comment = $(".comment-btn[id='" + id + "']").attr('data-description');
        // console.log(comment);
        let amount = $("#amount" + id).val();
        let variantenID = $("#variante" + id).val();
        let bestand = $("#bestand" + id).val();
        let standort = $("#Standort" + id).val();
        let verwendung = $("#Verwendung" + id).val();
        if (standort === '0' && verwendung === '0') {
            alert("Standort und Verwendung kann nicht Nein sein!");
        } else {
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
                type: "GET",
                success: function (data) {
                    makeToaster(data.trim(), true);
                }
            });
        }
    });

    $(document).ready(function () {
        $.fn.dataTable.ext.search = [];

         tableRoomElements = $("#tableRoomElements").DataTable({
            select: true,
            paging: true,
            pagingType: "simple",
            lengthChange: true,
            pageLength: 25,
            searching: true,
            info: true,
            hover: true,

            columnDefs: [
                {
                    targets: [0],
                    visible: false,
                    searchable: false,
                    orderable: false
                },
                {
                    targets: [3, 4, 5, 6, 7, 8, 9],
                    searchable: true,
                    orderable: true
                }
            ],
            order: [[3, "desc"]],
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/de-DE.json",
                search: "",
                searchPlaceholder: "Search...",
            },
            layout: {
                topStart: null,
                topEnd: "search",
                bottomEnd: ['pageLength', 'paging'],
                bottomStart: ['info']
            }
        });

        // Custom search function to hide rows with amount 0


        $('#tableRoomElements tbody').on('click', 'tr', function () {
            let id = tableRoomElements.row($(this)).data()[0].display;
            let stk = $("#amount" + id).val();
            let standort = $("#Standort" + id).val();
            let verwendung = $("#Verwendung" + id).val();
            let elementID = tableRoomElements.row($(this)).data()[0]['display'];
            //console.log("ELID", elementID);
            $.ajax({
                url: "getElementParameters.php",
                data: {"id": id},
                type: "GET",
                success: function (data) {
                    $("#elementParameters").html(data);
                    $("#elementParameters").show();
                    $.ajax({
                        url: "getElementPrice.php",
                        data: {"id": id},
                        type: "GET",
                        success: function (data) {
                            $("#price").html(data);
                            $.ajax({
                                url: "getElementBestand.php",
                                data: {"id": id, "stk": stk},
                                type: "GET",
                                success: function (data) {
                                    $("#elementBestand").html(data);
                                    $("#elementBestand").show();
                                    if (verwendung === '1' && standort === '0') {
                                        $.ajax({
                                            url: "getElementStandort.php",
                                            data: {"id": id, "elementID": elementID},
                                            type: "GET",
                                            success: function (data) {
                                                $("#elementVerwendung").html(data);
                                                $("#elementVerwendung").show();
                                            }
                                        });
                                    } else {
                                        $("#elementBestand").show();
                                        $.ajax({
                                            url: "getElementVerwendung.php",
                                            data: {"id": id},
                                            type: "GET",
                                            success: function (data) {
                                                $("#elementVerwendung").html(data);
                                                $("#elementVerwendung").show();
                                            }
                                        });
                                    }
                                }
                            });
                        }
                    });
                }
            });
        });
        attachButtonListeners();

        $.fn.dataTable.ext.search.push(   //TODO. Its broken
            function (settings, data, dataIndex) {
                let hideZero = $("#hideZeroRows").is(":checked");
                let row = tableRoomElements.row(dataIndex).node();
                let amount = $(row).find('input[id^="amount"]').val();
                amount = parseInt(amount) || 0;
                console.log(dataIndex, amount, !(hideZero && (amount === 0)));
                return !(hideZero && (amount === 0));
            }
        );

        // Event handler for checkbox change
        $("#hideZeroRows").on("change", function () {
            tableRoomElements.draw();
        });

    });

</script>
</body>
