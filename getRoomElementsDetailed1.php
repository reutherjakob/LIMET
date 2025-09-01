<?php
// V2.0: 2024-11-29, Reuther & Fux
require_once 'utils/_utils.php';
include "utils/_format.php";
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
ORDER BY  tabelle_elemente.ElementID DESC;";

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
        if (str_starts_with($row["ElementID"] ?? '', '1') || str_starts_with($row["ElementID"] ?? '', '3') || str_starts_with($row["ElementID"] ?? '', '4') || str_starts_with($row["ElementID"] ?? '', '5')) {
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
<div class="d-flex align-items-center w-100">
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
        <span class="badge rounded-pill bg-light text-dark m-1 p-2"
              data-bs-toggle="popover"
              title="Kostenberechnung Info"
              data-bs-content="Alle Elemente, deren ID mit 1, 3, 4 oder 5 beginnen, sind als ortsfest erfasst. Andere sind ortsveränderlich">
            <i class="fas fa-info-circle"></i><i class="fas fa-exclamation"></i>
        </span>
    </div>


    <?php if ($result_room_elements->num_rows > 0): ?>
        <div id="room-action-buttons"
             class="d-inline-flex align-items-center text-nowrap btn-group-sm">
            <button type="button" class="btn btn-sm btn-outline-dark me-1" id="<?php echo $_SESSION["roomID"]; ?>"
                    data-bs-toggle="modal" data-bs-target="#copyRoomElementsModal" value="Rauminhalt kopieren">Inhalt
                kopieren
            </button>
            <button type="button" class="btn btn-sm btn-outline-dark  me-1" id="<?php echo $_SESSION["roomID"]; ?>"
                    value="createRoombookPDF"><i class="far fa-file-pdf"></i> RB-PDF
            </button>
            <button type="button" class="btn btn-sm btn-outline-dark  me-1" id="<?php echo $_SESSION["roomID"]; ?>"
                    value="createRoombookPDFCosts"><i class="far fa-file-pdf"></i> RB-Kosten-PDF
            </button>

            <input class="btn-check" type="checkbox" id="hideZeroRows" checked>
            <label class="btn btn-outline-dark" for="hideZeroRows">
                <i id="hideZeroIcon" class="fa fa-eye-slash"></i> Hide 0
            </label>

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
        <th>Bestand</th>
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
                <span id="ElementName<?php echo $row["TABELLE_Elemente_idTABELLE_Elemente"]; ?>"><?php echo $row["ElementID"] . " " . $row["Bezeichnung"]; ?></span>
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
                $dataAttr = $Kurzbeschreibung === "" ? "data-description= '' " : "data-description='" . htmlspecialchars($Kurzbeschreibung ?? "", ENT_QUOTES, 'UTF-8') . "'";


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
<div class='modal fade' id='copyRoomElementsModal' aria-labelledby='copyRoomElementsModalLabel' tabindex="-1"
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
<div class='modal fade' id='historyModal' role='dialog' tabindex="-1">
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

<script src="utils/_utils.js"></script>
<script charset="utf-8" type="module">
    import CustomPopover from './utils/_popover.js';

    let rememberSorting = localStorage.getItem('rememberSorting') === 'true';
    let savedSort = null;
    if (rememberSorting) {
        try {
            savedSort = JSON.parse(localStorage.getItem('roomElementsSort'));
        } catch {
            savedSort = null;
        }
    }
    let currentSort = savedSort && typeof savedSort === 'object' && savedSort.column != null && savedSort.dir
        ? savedSort
        : {column: 1, dir: 'asc'};
    let tableRoomElements;
    CustomPopover.init('.comment-btn', {
        onSave: (trigger, newText) => {
            trigger.dataset.description = newText;
            const id = trigger.id;
            $.ajax({
                url: 'saveRoomElementComment.php',
                data: {comment: newText, id},
                type: 'GET',
                success(data) {
                    makeToaster(data.trim(), true);
                    const btn = $(`.comment-btn[id='${id}']`);
                    btn.removeClass('btn-outline-secondary').addClass('btn-outline-dark');
                    btn.find('i').removeClass('fa fa-comment-slash').addClass('fa fa-comment');
                    btn.attr('data-description', newText).data('description', newText);
                },
            });
        },
    });

    $(document).ready(() => {
        initHideZero();
        initDataTable();
        attachButtonListeners();
        initPopoverTips();
    });

    function initPopoverTips() {
        const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        popoverTriggerList.forEach(el => new bootstrap.Popover(el));

        document.addEventListener(
            'click',
            e => {
                document.querySelectorAll('[data-bs-toggle="popover"]').forEach(pop => {
                    const popover = bootstrap.Popover.getInstance(pop);
                    if (popover) {
                        if (!pop.contains(e.target) && !(popover.tip && popover.tip.contains(e.target))) {
                            popover.hide();
                        }
                    }
                });
            },
            true
        );
    }

    function initHideZero() {
        $.fn.dataTable.ext.search = $.fn.dataTable.ext.search.filter(fn => fn !== hideZeroFilter);
        if ($.fn.dataTable.isDataTable('#tableRoomElements')) {
            $('#tableRoomElements').DataTable().destroy(true);
            $('#tableRoomElements').empty();
        }
        const hideZero = localStorage.getItem('hideZeroSetting') === 'true';
        $('#hideZeroRows').prop('checked', hideZero);
        toggleHideZeroIcon(hideZero);
        $('#hideZeroRows').on('change', function () {
            localStorage.setItem('hideZeroSetting', this.checked ? 'true' : 'false');
            toggleHideZeroIcon(this.checked);
            if (tableRoomElements) tableRoomElements.draw();
        });
    }

    function toggleHideZeroIcon(hidden) {
        const icon = $('#hideZeroIcon');
        if (hidden) {
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    }

    function initDataTable() {
        let savedLength = localStorage.getItem('roomElementsPageLength');
        let orderOption = (rememberSorting && currentSort.column !== null && currentSort.dir)
            ? [[currentSort.column, currentSort.dir]]  // gespeicherte Sortierung
            : [[1, 'asc']]; // 3 = Stückzahl-Spalte (0-ID, 1-Element, 2-Var,

        console.log("ORDER ", orderOption);

        tableRoomElements = $('#tableRoomElements').DataTable({
            select: true,
            paging: true,
            pagingType: 'simple',
            lengthChange: true,
            pageLength: savedLength ? Number(savedLength) : 25,
            searching: true,
            info: true,
            hover: true,
            order: orderOption,
            columnDefs: [
                {
                    targets: [0],
                    visible: false,
                    searchable: false,
                    orderable: false,
                },
                {
                    targets: [3, 4, 5, 6, 7, 8, 9],
                    searchable: true,
                    orderable: true,
                },
            ],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json',
                search: '',
                searchPlaceholder: 'Suche...',
            },
            layout: {
                topStart: null,
                topEnd: null,
                bottomEnd: ['pageLength', 'paging'],
                bottomStart: ['search', 'info'],
            },
            initComplete() {
                $('#room-action-buttons .xxx').remove();
                $('#roomElements .dt-search label').remove();
                $('#roomElements .dt-search')
                    .children()
                    .removeClass('form-control form-control-sm')
                    .addClass('btn btn-sm btn-outline-dark xxx ms-1 me-1')
                    .appendTo('#room-action-buttons');
            },
        });

        $('#tableRoomElements').on('length.dt', (e, settings, len) => {
            localStorage.setItem('roomElementsPageLength', len);
        });

        tableRoomElements.on('order.dt', function () {
            if (rememberSorting) {
                const order = tableRoomElements.order(); // z.B. [[3, "asc"]]
                if (order.length > 0) {
                    currentSort = { column: order[0][0], dir: order[0][1] };
                    localStorage.setItem('roomElementsSort', JSON.stringify(currentSort));
                }
            }
        });

        // Row click ajax cascade
        $('#tableRoomElements tbody').on('click', 'tr', function () {
            const data = tableRoomElements.row(this).data();
            if (!data) return;

            // data[0] is id (hidden)
            const id = data[0];
            const stk = $(`#amount${id}`).val();
            const standort = $(`#Standort${id}`).val();
            const verwendung = $(`#Verwendung${id}`).val();

            // data[1] is HTML string with span id="ElementNameXXX"
            const elementID = (data[1].match(/id="ElementName(\d+)"/) || [])[1];

            $.ajax({
                url: 'getElementParameters.php',
                data: {id},
                type: 'GET',
                success(data) {
                    $('#elementParameters').html(data).show();

                    $.ajax({
                        url: 'getElementPrice.php',
                        data: {id},
                        type: 'GET',
                        success(data) {
                            $('#price').html(data);

                            $.ajax({
                                url: 'getElementBestand.php',
                                data: {id, stk},
                                type: 'GET',
                                success(data) {
                                    $('#elementBestand').html(data).show();

                                    if (verwendung === '1' && standort === '0') {
                                        $.ajax({
                                            url: 'getElementStandort.php',
                                            data: {id, elementID},
                                            type: 'GET',
                                            success(data) {
                                                $('#elementVerwendung').html(data).show();
                                            },
                                        });
                                    } else {
                                        $('#elementBestand').show();
                                        $.ajax({
                                            url: 'getElementVerwendung.php',
                                            data: {id},
                                            type: 'GET',
                                            success(data) {
                                                $('#elementVerwendung').html(data).show();
                                            },
                                        });
                                    }
                                },
                            });
                        },
                    });
                },
            });
        });
    }

    function attachButtonListeners() {
        $("button[value='createRoombookPDF']").click(function () {
            window.open('PDFs/pdf_createRoombookPDF.php?roomID=' + this.id);
        });

        $("button[value='createRoombookPDFCosts']").click(function () {
            window.open('PDFs/pdf_createRoombookPDFwithCosts.php?roomID=' + this.id);
        });

        $("button[value='Rauminhalt kopieren']").click(function () {
            $('#copyRoomElementsModal').modal('show');
            if (typeof dt_search_counter !== 'undefined' && dt_search_counter !== null) {
                dt_search_counter++;
            }
            const originRoomID = this.id;
            $.ajax({
                url: 'getRoomsToCopy.php',
                type: 'GET',
                data: {originRoomID},
                success(data) {
                    $('#mbodyCRE').html(data);
                },
            });
        });

        $("button[value='history']").click(function () {
            const roombookID = this.id;
            const elementName = $(`#ElementName${roombookID}`).text();
            $.ajax({
                url: 'getCommentHistory.php',
                type: 'GET',
                data: {roombookID},
                success(data) {
                    $('#ElementName4Header').text(elementName);
                    $('#mbodyHistory').html(data);
                    $('#historyModal').modal('show');
                },
            });
        });

        $("button[value='saveElement']").click(function () {
            const id = this.id;
            const comment = $(`.comment-btn[id='${id}']`).attr('data-description');
            const amount = $(`#amount${id}`).val();
            const variantenID = $(`#variante${id}`).val();
            const bestand = $(`#bestand${id}`).val();
            const standort = $(`#Standort${id}`).val();
            const verwendung = $(`#Verwendung${id}`).val();

            if (standort === '0' && verwendung === '0') {
                alert('Standort und Verwendung kann nicht Nein sein!');
                return;
            }

            $.ajax({
                url: 'saveRoombookEntry.php',
                type: 'GET',
                data: {comment, id, amount, variantenID, bestand, standort, verwendung},
                success(data) {
                    makeToaster(data.trim(), true);
                },
            });
        });

        // Add toggle checkbox to enable/disable remembering sorting state
        addRememberSortingControl();
    }

    function addRememberSortingControl() {
        const container = $(
            '<span class="badge rounded-pill bg-light text-dark m-1 p-2"></span>'
        ).appendTo($('.d-flex.flex-wrap.justify-content-between').first());

        const checkboxId = 'rememberSortingToggle';
        container.append(
            `<label for="${checkboxId}" class="me-1 fw-normal" style="user-select:none; cursor:pointer;">Sortierung merken</label>`
        );
        const checkbox = $(
            `<input type="checkbox" id="${checkboxId}" style="vertical-align:middle; cursor:pointer;">`
        ).appendTo(container);

        checkbox.prop('checked', rememberSorting);

        checkbox.on('change', function () {
            rememberSorting = this.checked;
            localStorage.setItem('rememberSorting', rememberSorting ? 'true' : 'false');

            // If disabled clear stored sorting, else save current if valid
            if (!rememberSorting) {
                localStorage.removeItem('roomElementsSort');
            } else if (currentSort.column !== null && currentSort.dir !== null) {
                localStorage.setItem('roomElementsSort', JSON.stringify(currentSort));
            }
        });
    }

    // Filter function for hide zero rows (assuming column 3 = Menge/Stk)
    function hideZeroFilter(settings, data, dataIndex) {
        if ($('#hideZeroRows').prop('checked')) {
            return parseInt(data[3], 10) !== 0;
        }
        return true;
    }
</script>


</body>