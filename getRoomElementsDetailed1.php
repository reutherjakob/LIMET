<?php
// 25 FX
require_once 'utils/_utils.php';
include "utils/_format.php";
check_login();

$mysqli = utils_connect_sql();
$sql_room_elements = "SELECT tabelle_räume_has_tabelle_elemente.id, 
       tabelle_räume_has_tabelle_elemente.TABELLE_Geraete_idTABELLE_Geraete,
       tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente,
       tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten, 
       tabelle_räume_has_tabelle_elemente.Anzahl, 
       tabelle_elemente.ElementID, 
       tabelle_elemente.Kurzbeschreibung As `Elementbeschreibung`, 
       tabelle_varianten.Variante, 
       tabelle_elemente.Bezeichnung, tabelle_geraete.GeraeteID, tabelle_hersteller.Hersteller, tabelle_geraete.Typ, 
       tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, tabelle_räume_has_tabelle_elemente.Standort,  
       tabelle_räume_has_tabelle_elemente.Verwendung, tabelle_räume_has_tabelle_elemente.Kurzbeschreibung, 
       tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, 
       tabelle_räume_has_tabelle_elemente.TABELLE_Geraete_idTABELLE_Geraete
FROM tabelle_varianten INNER JOIN (tabelle_hersteller RIGHT JOIN ((tabelle_räume_has_tabelle_elemente LEFT JOIN tabelle_geraete ON tabelle_räume_has_tabelle_elemente.TABELLE_Geraete_idTABELLE_Geraete = tabelle_geraete.idTABELLE_Geraete) INNER JOIN tabelle_elemente ON tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente = tabelle_elemente.idTABELLE_Elemente) ON tabelle_hersteller.idtabelle_hersteller = tabelle_geraete.tabelle_hersteller_idtabelle_hersteller) ON tabelle_varianten.idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten
WHERE (((tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume)=?))
ORDER BY  tabelle_elemente.ElementID DESC;";

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
    <div id="kostenInfoPanel" class="collapse mt-2 border rounded p-3" style="background-color: #f8f9fa;">
        <?php include "getRoomCostsbyGewerke.php" ?>
    </div>

    <?php if ($result_room_elements->num_rows > 0): ?>
        <div id="room-action-buttons"
             class="d-inline-flex align-items-center text-nowrap btn-group-sm">
            <button type="button" class="btn btn-sm btn-outline-dark me-1" id="<?php echo $_SESSION["roomID"]; ?>"
                    data-bs-toggle="modal" data-bs-target="#copyRoomElementsModal" value="Rauminhalt kopieren">
                Elemente kopieren
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

            <button type="button" class="btn btn-sm btn-outline-dark" id="kostenInfoBtn">
                <i class="fas fa-coins"></i> Kosten Info
            </button>
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
        <th class='d-flex justify-content-center align-items-center' data-bs-toggle='tooltip' title='Standort'><i class='fab fa-periscope '></i></th>
        <th>Verw</th>
        <th class='d-flex justify-content-center align-items-center' data-bs-toggle='tooltip' title='Kommentar'><i class='far fa-comment'></i></th>
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
            <td data-order="<?php echo $row["Anzahl"]; ?>">
                <label style="display: none;" for="amount<?php echo $row["id"]; ?>"></label>
                <input class="form-control form-control-sm" type="text" id="amount<?php echo $row["id"]; ?>"
                       value="<?php echo $row["Anzahl"]; ?>" size="1">
            </td>
            <td data-order="<?php echo $row["Neu/Bestand"]; ?>">
                <label for="bestand<?php echo $row["id"]; ?>" style="display: none;"></label>
                <select class="form-control form-control-sm" id="bestand<?php echo $row["id"]; ?>">
                    <option value="0" <?php echo $row["Neu/Bestand"] == "0" ? "selected" : ""; ?>>Ja</option>
                    <option value="1" <?php echo $row["Neu/Bestand"] == "1" ? "selected" : ""; ?>>Nein</option>
                </select>
            </td>
            <td data-order="<?php echo $row["Standort"]; ?>">
                <label for="Standort<?php echo $row["id"]; ?>" style="display: none;"> </label>
                <select class="form-control form-control-sm" id="Standort<?php echo $row["id"]; ?>">
                    <option value="0" <?php echo $row["Standort"] == "0" ? "selected" : ""; ?>>Nein</option>
                    <option value="1" <?php echo $row["Standort"] == "1" ? "selected" : ""; ?>>Ja</option>
                </select>
            </td>
            <td data-order="<?php echo $row["Verwendung"]; ?>">
                <label for="Verwendung<?php echo $row["id"]; ?>" style="display: none;"></label>
                <select class="form-control form-control-sm" id="Verwendung<?php echo $row["id"]; ?>">
                    <option value="0" <?php echo $row["Verwendung"] == "0" ? "selected" : ""; ?>>Nein</option>
                    <option value="1" <?php echo $row["Verwendung"] == "1" ? "selected" : ""; ?>>Ja</option>
                </select>
            </td>
            <td>
                <?php
                $Kurzbeschreibung = trim($row["Kurzbeschreibung"] ?? "");
                $buttonClass = $Kurzbeschreibung === "" ? "btn-outline-secondary" : "btn-outline-dark";
                $iconClass = $Kurzbeschreibung === "" ? "fa fa-comment-slash" : "fa fa-comment";
                $dataAttr = $Kurzbeschreibung === "" ? "data-description= '' " : "data-description='" .
                    htmlspecialchars($Kurzbeschreibung, ENT_QUOTES, 'UTF-8') . "'";
                ?>
                <button type="button"
                        class="btn btn-sm <?php echo $buttonClass; ?> comment-btn" <?php echo $dataAttr; ?>
                        id="<?php echo $row["id"]; ?>" title="Kommentar"><i class="<?php echo $iconClass; ?>"></i>
                </button>
            </td>
            <td data-order="history">
                <button type="button" id="<?php echo $row["id"]; ?>" class="btn btn-sm btn-outline-dark"
                        value="history"><i class="fas fa-history"></i></button>
            </td>
            <td data-order="saveElement">
                <button type="button" id="<?php echo $row["id"]; ?>" class="btn btn-sm btn-warning" value="saveElement">
                    <i class="far fa-save"></i></button>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>

<?php
include "modal_copyRoomElements.php";
include "modal_elementHistory.html";
?>

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
                type: 'POST',
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
        addRememberSortingControl();
        initPopoverTips();
    });

    function initPopoverTips() {
        const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        popoverTriggerList.forEach(el => new bootstrap.Popover(el));
        document.addEventListener('click', e => {
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

    function hideZeroFilter(settings, data, dataIndex) {
        let api = new $.fn.dataTable.Api(settings);
        let row = api.row(dataIndex).node();
        let $input = $(row).find("input[id^='amount']");
        let inputVal = $input.val();
        let parsedVal = parseInt(inputVal, 10);
        if ($('#hideZeroRows').prop('checked')) {
            return parsedVal !== 0;
        }
        return true;
    }


    function initHideZero() {
        $.fn.dataTable.ext.search = $.fn.dataTable.ext.search.filter(fn => fn !== hideZeroFilter);
        if ($.fn.dataTable.isDataTable('#tableRoomElements')) {
            $('#tableRoomElements').DataTable().destroy(true);
            $('#tableRoomElements').empty();
        }
        $.fn.dataTable.ext.search.push(hideZeroFilter);
        const hideZero = localStorage.getItem('hideZeroSetting') === 'true';
        $('#hideZeroRows').prop('checked', hideZero);
        toggleHideZeroIcon(hideZero);
        $('#hideZeroRows').on('change', function () {
            localStorage.setItem('hideZeroSetting', this.checked ? 'true' : 'false');
            toggleHideZeroIcon(this.checked);
            if (tableRoomElements) {
                tableRoomElements.draw();
            }
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

                $('#tableRoomElements tbody').on('click', 'tr', function () {
                    const data = tableRoomElements.row(this).data();
                    if (!data) return;
                    const id = data[0].display;      // data[0] is id (hidden)
                    console.log(data, id);
                    const stk = $(`#amount${id}`).val();
                    const standort = $(`#Standort${id}`).val();
                    const verwendung = $(`#Verwendung${id}`).val();
                    const elementHTML = data[1].display;
                    const elementID = (elementHTML.match(/id="ElementName(\d+)"/) || [])[1];
//                     console.log("tableRoomElements Klick");
                    $.ajax({
                        url: 'getElementParameters.php',
                        data: {id},
                        type: 'POST',
                        success(data) {
                            $('#elementParameters').html(data).show();
                            $.ajax({
                                url: 'getElementPrice.php',
                                data: {id},
                                type: 'POST',
                                success(data) {
                                    $('#price').html(data);
                                    $.ajax({
                                        url: 'getElementBestand.php',
                                        data: {id, stk},
                                        type: 'POST',
                                        success(data) {
                                            $('#elementBestand').html(data).show();

                                            if (verwendung === '1' && standort === '0') {
                                                $.ajax({
                                                    url: 'getElementStandort.php',
                                                    data: {id, elementID},
                                                    type: 'POST',
                                                    success(data) {
                                                        $('#elementVerwendung').html(data).show();
                                                    },
                                                });
                                            } else {
                                                $('#elementBestand').show();
                                                $.ajax({
                                                    url: 'getElementVerwendung.php',
                                                    data: {id},
                                                    type: 'POST',
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
            },
        });

        $('#tableRoomElements').on('length.dt', (e, settings, len) => {
            localStorage.setItem('roomElementsPageLength', len);
        });

        tableRoomElements.on('order.dt', function () {
            if (rememberSorting) {
                const order = tableRoomElements.order(); // z.B. [[3, "asc"]]
                if (order.length > 0) {
                    currentSort = {column: order[0][0], dir: order[0][1]};
                    localStorage.setItem('roomElementsSort', JSON.stringify(currentSort));
                }
            }
        });
    }

    function attachButtonListeners() {
        $('#kostenInfoBtn').click(function () {
            $('#kostenInfoPanel').collapse('toggle');
        });

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
                type: 'POST',
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
                type: 'POST',
                data: {"roombookID": roombookID},
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
                type: 'POST',
                data: {comment, id, amount, variantenID, bestand, standort, verwendung},
                success(data) {
                    makeToaster(data.trim(), true);
                },
            });
        });
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
            if (!rememberSorting) {
                localStorage.removeItem('roomElementsSort');
            } else if (currentSort.column !== null && currentSort.dir !== null) {
                localStorage.setItem('roomElementsSort', JSON.stringify(currentSort));
            }
        });
    }

</script>
</body>