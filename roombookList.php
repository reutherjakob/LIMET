<?php
// 25 FX
require_once 'utils/_utils.php';
include "utils/_format.php";
init_page_serversides();
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>RB-Liste</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">


    <link rel="icon" href="/Logo/iphone_favicon.png">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.2.1/af-2.7.0/b-3.2.1/b-colvis-3.2.1/b-html5-3.2.1/b-print-3.2.1/cr-2.0.4/date-1.5.5/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.1/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-3.0.0/sr-1.4.1/datatables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css"
          integrity="sha512-q3eWabyZPc1XTCmF+8/LuE1ozpg5xxn7iO89yfSOd5/oKvyqLngoNGsx8jq92Y8eXJ/IRxQbEC+FGSYxtk2oiw=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link href="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.2.1/af-2.7.0/b-3.2.1/b-colvis-3.2.1/b-html5-3.2.1/b-print-3.2.1/cr-2.0.4/date-1.5.5/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.1/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-3.0.0/sr-1.4.1/datatables.min.css"
          rel="stylesheet">

    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen"/>
    <?php //include_theme_css(); ?>

</head>

<body style="height:100%">
<div class="container-fluid">
    <div id="limet-navbar"></div>
    <div class="mt-2 card">
        <div class="card-header">
            <div class=" row">
                <div class="col-4">
                    <b>Elemente im Projekt</b>
                </div>
                <div class="col-8 d-flex align-items-center justify-content-end" id="dt-header-container"></div>
            </div>
        </div>
        <div class="card-body" id="elementLots">
            <?php
            $mysqli = utils_connect_sql();
            $sql = "SELECT  tabelle_räume.Raumnr,
                            tabelle_räume.idTABELLE_Räume,
                            tabelle_räume.Raumbezeichnung,
                            tabelle_räume.`Raumbereich Nutzer`,
                            tabelle_räume.Raumnummer_Nutzer,
                            tabelle_räume.Geschoss,
                            tabelle_räume.Bauetappe,
                            tabelle_räume.Bauabschnitt,
                            tabelle_räume_has_tabelle_elemente.id as rhe_id,
                            tabelle_räume_has_tabelle_elemente.Anzahl,
                            tabelle_räume_has_tabelle_elemente.`Neu/Bestand`,
                            tabelle_räume_has_tabelle_elemente.Standort,
                            tabelle_räume_has_tabelle_elemente.Verwendung,
                            tabelle_projekt_varianten_kosten.Kosten AS EP,
                            tabelle_elemente.ElementID,
                            tabelle_elemente.Bezeichnung,
                            tabelle_varianten.Variante,
                            tabelle_varianten.idtabelle_Varianten, 
                            tabelle_projektbudgets.Budgetnummer,
                            tabelle_lose_extern.LosNr_Extern,
                            tabelle_lose_extern.LosBezeichnung_Extern,
                            tabelle_auftraggeber_gewerke.Gewerke_Nr,
                            tabelle_auftraggeber_ghg.GHG,
                            tabelle_räume_has_tabelle_elemente.Kurzbeschreibung
                FROM (((tabelle_projekt_element_gewerk RIGHT JOIN (tabelle_projektbudgets RIGHT JOIN (tabelle_lose_extern RIGHT JOIN 
                (tabelle_varianten INNER JOIN (tabelle_elemente INNER JOIN ((tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente
                ON tabelle_räume.idTABELLE_Räume =
                tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) INNER JOIN tabelle_projekt_varianten_kosten
                ON (tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten =
                tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten) AND
                (tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte =
                tabelle_räume.tabelle_projekte_idTABELLE_Projekte) AND
                (tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente =
                tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente))
                ON tabelle_elemente.idTABELLE_Elemente =
                tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente)
                ON tabelle_varianten.idtabelle_Varianten =
                tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten)
                ON tabelle_lose_extern.idtabelle_Lose_Extern =
                tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern)
                ON tabelle_projektbudgets.idtabelle_projektbudgets =
                tabelle_räume_has_tabelle_elemente.tabelle_projektbudgets_idtabelle_projektbudgets)
                ON (tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente =
                tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente) AND
                (tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte =
                tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte)) LEFT JOIN tabelle_auftraggeber_gewerke
                ON tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke =
                tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke) LEFT JOIN tabelle_auftraggeber_ghg
                ON tabelle_projekt_element_gewerk.tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG =
                tabelle_auftraggeber_ghg.idtabelle_auftraggeber_GHG)
                LEFT JOIN tabelle_auftraggeberg_gug
                ON tabelle_projekt_element_gewerk.tabelle_auftraggeberg_gug_idtabelle_auftraggeberg_GUG =
                tabelle_auftraggeberg_gug.idtabelle_auftraggeberg_GUG
                WHERE tabelle_räume.tabelle_projekte_idTABELLE_Projekte = ? 
                AND Anzahl <>0";

            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param('i', $_SESSION["projectID"]);
            $stmt->execute();
            $result = $stmt->get_result();

            echo "<table class='table table-striped table-hover compact table-bordered' id='tableRoombookList'>
            <thead><tr>
                <th>Raumnr</th>
                <th>Raum</th>
                <th>Raumbereich</th>
                <th>Geschoss</th>
                <th>BE</th>
                <th>BA</th>
                <th> <div class='d-flex justify-content-center align-items-center' data-bs-toggle='tooltip' title='ID'><i class='fas fa-fingerprint'></i></div> </th>
                <th>Element</th>
                <th>Stk</th>
                <th>Variante</th>  
                <th>Bestand</th>
                <th> <div class='d-flex justify-content-center align-items-center' data-bs-toggle='tooltip' title='Standort'> <i class='fab fa-periscope '></i></div> </th>
                <th> <div class='d-flex justify-content-center align-items-center' data-bs-toggle='tooltip' title='Verwendung'> <i class='fas fa-cogs'></i></div> </th>
                <th> <div class='d-flex justify-content-center align-items-center' data-bs-toggle='tooltip' title='Einheitspreis'> <i class='fas fa-euro-sign'></i> </div></th>
                <th>EP-Excel</th>                                                            
                <th>Los-Nr</th>
                <th>Budget</th>                                                                
                <th>Gewerk</th>
                <th>GHG</th>
                <th>Los Bezeichnung</th>  
                <th> <div class='d-flex justify-content-centeralign-items-center' data-bs-toggle='tooltip' title='Kommentar'><i class='far fa-comments'></i></div></th>
                <th>   <i class='fas fa-edit'></i> </th>
            </tr>
            </thead>";
            echo "<tbody>";

            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                if ($_SESSION["projectName"] === "GCP") {
                    echo "<td>" . $row["Raumnummer_Nutzer"] . "</td>";
                } else {
                    echo "<td>" . $row["Raumnr"] . "</td>";
                }
                echo "<td>" . $row["Raumbezeichnung"] . "</td>";
                echo "<td>" . $row["Raumbereich Nutzer"] . "</td>";
                echo "<td>" . $row["Geschoss"] . "</td>";
                echo "<td>" . $row["Bauetappe"] . "</td>";
                echo "<td>" . $row["Bauabschnitt"] . "</td>";
                echo "<td>" . $row["ElementID"] . "</td>";
                echo "<td>" . $row["Bezeichnung"] . "</td>";
                echo "<td>" . $row["Anzahl"] . "</td>";
                echo "<td>" . $row["Variante"] . "</td>";
                echo "<td>" . ($row["Neu/Bestand"] == 0 ? "Ja" : "Nein") . "</td>";
                echo "<td>" . $row["Standort"] . "</td>";
                echo "<td>" . $row["Verwendung"] . "</td>";

                $ep = ($row["Standort"] == 0) ? 0 : $row["EP"];
                echo "<td>" . format_money($ep) . "</td>";
                echo "<td>" . $ep . "</td>";

                echo "<td>" . $row["LosNr_Extern"] . "</td>";
                echo "<td>" . $row["Budgetnummer"] . "</td>";
                echo "<td>" . $row["Gewerke_Nr"] . "</td>";
                echo "<td>" . $row["GHG"] . "</td>";
                echo "<td>" . $row["LosBezeichnung_Extern"] . "</td>";
                if (!empty($row["Kurzbeschreibung"])) {
                    echo "<td><button type='button' class='btn btn-sm btn-outline-dark' 
                        data-bs-toggle='popover' 
                        data-bs-placement='top' 
                        data-bs-content='" . htmlspecialchars($row["Kurzbeschreibung"]) . "' 
                        title='Kommentar'>
                        <i class='fa fa-comment'></i></button></td>";
                } else {
                    echo "<td></td>";
                }

                echo "<td>
    <button type='button' class='btn btn-sm btn-outline-secondary edit-entry-btn'
        data-id='" . $row["rhe_id"] . "'
        data-variantenid='" . $row["idtabelle_Varianten"] . "'
        data-anzahl='" . $row["Anzahl"] . "'
        data-bestand='" . $row["Neu/Bestand"] . "'
        data-standort='" . $row["Standort"] . "'
        data-verwendung='" . $row["Verwendung"] . "'
        data-ep='" . $row["EP"] . "'
        data-comment='" . htmlspecialchars($row["Kurzbeschreibung"] ?? '', ENT_QUOTES) . "'>
        <i class='fas fa-edit'></i>
    </button>
</td>";
                echo "</tr>";

            }
            echo "</tbody></table>";
            $mysqli->close();
            ?>
        </div>
    </div>
</div>


<!-- Edit Entry Modal -->
<div class="modal fade" id="editEntryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Eintrag bearbeiten</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit_id">
                <div class="mb-3">
                    <label class="form-label">Variante</label>
                    <select class="form-select form-select-sm" id="edit_variantenID">
                        <option value="1">A</option>
                        <option value="2">B</option>
                        <option value="3">C</option>
                        <option value="4">D</option>
                        <option value="5">E</option>
                        <option value="6">F</option>
                        <option value="7">G</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Anzahl</label>
                    <input type="number" class="form-control form-control-sm" id="edit_amount">
                </div>
                <div class="mb-3">
                    <label class="form-label">Bestand (Neu?)</label>
                    <select class="form-select form-select-sm" id="edit_bestand">
                        <option value="0">Ja</option>
                        <option value="1">Nein</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Standort</label>
                    <select class="form-select form-select-sm" id="edit_standort">
                        <option value="0">Nein</option>
                        <option value="1">Ja</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Verwendung</label>
                    <select class="form-select form-select-sm" id="edit_verwendung">
                        <option value="0">Nein</option>
                        <option value="1">Ja</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Kommentar</label>
                    <textarea class="form-control form-control-sm" id="edit_comment" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Abbrechen</button>
                <button type="button" class="btn btn-sm btn-primary" id="saveEditEntry">Speichern</button>
            </div>
        </div>
    </div>
</div>

<script src="utils/_utils.js"></script>
<script>
    $(document).ready(function () {

        const dt = new DataTable('#tableRoombookList', {
            select: true,
            dom: "<'dt-buttons'B><'dt-search'f><'dt-info' i>rtp",
            deferRender: true,
            scroller: true,
            scrollY: '85vh',       // Höhe des sichtbaren Bereichs
            scrollCollapse: true,  // schrumpft wenn weniger Zeilen
            paging: true,
            //pageLength: 50,
            // lengthMenu: [[25, 50, 100, 250, -1], ['25', '50', '100', '250', 'Alle']],

            columnDefs: [
                {
                    targets: [14],
                    visible: false,
                    searchable: false
                }
            ],
            order: [[2, "asc"]],

            language: {
                url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json",
                search: "",
                // info: "",
                //  infoEmpty: "",
                //  infoFiltered: "_TOTAL_ gefiltert von _MAX_; ",
                select: {

                    rows: {
                        _: "- %d ausgewählt",
                        0: "",
                        1: "- 1 ausgewählt"
                    },

                    columns: {
                        _: "",
                        0: "",
                        1: ""
                    },

                    cells: {
                        _: "",
                        0: "",
                        1: ""
                    },
                }
            },
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '',
                    className: 'fas fa-file-excel btn btn-sm btn-outline-success bg-white',
                    exportOptions: {
                        columns: ':not(:eq(12)):not(:eq(21))',
                        format: {
                            body: function (data, row, column) {
                                if (column === 14) {
                                    return "'" + data; // Apostroph voranstellen
                                }
                                if (column === 19) {
                                    var match = data.match(/data-bs-content='([^']*)'/);
                                    return match ? match[1] : '';
                                }
                                return data;
                            }
                        }
                    }
                }, {
                    extend: 'searchBuilder',
                    className: 'btn btn-sm bg-white btn-outline-dark' // add your btn-sm here with desired styles
                }
            ],

            initComplete: function () {
                $('#tableRoombookList_wrapper .dt-info').appendTo('#dt-header-container');
                $('#tableRoombookList_wrapper .dt-buttons').appendTo('#dt-header-container');
                $('#tableRoombookList_wrapper .dt-search').appendTo('#dt-header-container');
                //    $('#tableRoombookList_wrapper .dt-paging').appendTo('#dt-header-container');
            }
        });


        $(function () {
            // Enable all popovers
            const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
            const popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
                return new bootstrap.Popover(popoverTriggerEl, {
                    container: 'body',
                    trigger: 'focus', // ensures popover closes when focus is lost
                    placement: 'left' // optional
                });
            });
            // Close any open popover when clicking outside
            $(document).on('click', function (e) {
                $('[data-bs-toggle="popover"]').each(function () {
                    if (
                        !$(this).is(e.target) &&                              // Not the clicked element
                        $(this).has(e.target).length === 0 &&                // Not inside the clicked element
                        $('.popover').has(e.target).length === 0             // Not inside the actual popover
                    ) {
                        $(this).popover('hide');                             // Hide it
                    }
                });
            });
        });

        // Modal öffnen & befüllen
        $(document).on('click', '.edit-entry-btn', function () {
            const btn = $(this);
            $('#edit_id').val(btn.data('id'));
            $('#edit_amount').val(btn.data('anzahl'));
            $('#edit_bestand').val(String(btn.data('bestand')));
            $('#edit_standort').val(String(btn.data('standort')));
            $('#edit_verwendung').val(String(btn.data('verwendung')));
            $('#edit_variantenID').val(String(btn.data('variantenid')));
            $('#edit_comment').val(btn.data('comment'));
            $('#editEntryModal').modal('show');
        });

// Speichern
        $('#saveEditEntry').on('click', function () {
            const standort = $('#edit_standort').val();
            const verwendung = $('#edit_verwendung').val();
            if (standort === '0' && verwendung === '0') {
                alert('Standort und Verwendung kann nicht beide Nein sein!');
                return;
            }
            $.ajax({
                url: 'saveRoombookEntry.php',
                type: 'POST',
                data: {
                    id: $('#edit_id').val(),
                    variantenID: $('#edit_variantenID').val(),
                    amount: $('#edit_amount').val(),
                    bestand: $('#edit_bestand').val(),
                    standort: standort,
                    verwendung: verwendung,
                    comment: $('#edit_comment').val()
                },
                success(data) {
                    $('#editEntryModal').modal('hide');
                    makeToaster(data.trim(), true);

                    const id       = $('#edit_id').val();
                    const anzahl   = $('#edit_amount').val();
                    const bestand  = $('#edit_bestand').val();
                    const standort = $('#edit_standort').val();
                    const verwendung = $('#edit_verwendung').val();
                    const variantenID = $('#edit_variantenID').val();
                    const comment  = $('#edit_comment').val();
                    const varianteText = $('#edit_variantenID option:selected').text();

                    // Zeile anhand der rhe_id finden (Edit-Button mit data-id)
                    const btn = $(`.edit-entry-btn[data-id='${id}']`);
                    const row = dt.row(btn.closest('tr'));

                    // data-* Attribute am Button aktualisieren für nächstes Öffnen
                    btn.data('anzahl',     anzahl)
                        .data('bestand',    bestand)
                        .data('standort',   standort)
                        .data('verwendung', verwendung)
                        .data('variantenid', variantenID)
                        .data('comment',    comment);

                    // Zellen direkt im DOM updaten (Scroller rendert DOM-Nodes direkt)
                    const cells = btn.closest('tr').find('td');
                    cells.eq(8).text(anzahl);                                          // Stk
                    cells.eq(9).text(varianteText);                                    // Variante
                    cells.eq(10).text(bestand  === '0' ? 'Ja' : 'Nein');              // Bestand
                    cells.eq(11).text(standort);                                       // Standort
                    cells.eq(12).text(verwendung);                                     // Verwendung
                    // Zeile 13 (Anzeige): muss formatiert sein wie format_money()
// Zeile 14 (Excel):   muss "," als Dezimaltrenner haben

                    const epRaw = btn.data('ep');  // z.B. 1234.89 (JS float)
// Für Anzeige (col 13):
                    const epFormatted = standort === '0' ? '0,00'
                        : parseFloat(epRaw).toLocaleString('de-DE', {minimumFractionDigits: 2, maximumFractionDigits: 2});
// Für Excel (col 14):
                    const epExcel = standort === '0' ? '0'
                        : parseFloat(epRaw).toFixed(2).replace('.', ',');

                    cells.eq(13).text(epFormatted);
                    cells.eq(14).text(epExcel);
                }
            });
        });


    });

</script>
</body>
</html>

