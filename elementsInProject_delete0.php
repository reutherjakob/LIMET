<!DOCTYPE html>
<html lang="de">
<head>
    <title>RB-Elemente im Projekt</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen"/>
    <link rel="icon" href="Logo/iphone_favicon.png"/>

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
</head>

<style>

</style>

<body>
<!-- Rework 2025 -->
<div class="container-fluid bg-light">
    <div id="limet-navbar"></div>
    <div class="mt-2 card">
        <div class="card-header">
            <div class="row d-flex align-items-center justify-content-between">
                <div class="col-2"><strong> Elemente im Projekt</strong>
                </div>
                <div class="col-10 d-flex align-items-center justify-content-end" id="target_div">
                    <div class="me-4 d-flex " id="hide0Wrapper_ELiNpR">
                        <input class="btn-check btn-sm" type="checkbox" id="hideZeroRows_ELiNpR">
                        <label class="btn btn-sm btn-outline-dark" for="hideZeroRows_ELiNpR">
                            Hide 0
                        </label>
                    </div>

                    <div class="btn-group btn-group-sm" role="group" aria-label="PDF Generation Buttons">
                        <button type='button' class='btn btn-outline-dark me-1' id='createElementListPDF'>
                            <i class='far fa-file-pdf'></i> Elementliste
                        </button>
                        <button type='button' class='btn btn-outline-dark  me-1' id='createElementListWithPricePDF'>
                            <i class='far fa-file-pdf'></i> El.liste & Preis
                        </button>
                        <!--button type='button' class='btn btn-outline-dark  me-1' id='createElementEinbringwegePDF'>
                            <i class='far fa-file-pdf'></i> Einbringwege
                        </button>
                        <button type='button' class='btn btn-outline-dark  me-1' id='createElementEinbringwegePDF2'>
                            <i class='far fa-file-pdf'></i> Einbringwege2
                        </button-->

                    </div>
                    <div class="me-4 d-flex " id="sbdiv"></div>
                </div>
            </div>
        </div>

        <div class="card-body">
            <?php
            if (!function_exists('utils_connect_sql')) {
                include "utils/_utils.php";
            }
            init_page_serversides();
            include "utils/_format.php";
            $mysqli = utils_connect_sql();
            $sql = "SELECT Sum(tabelle_räume_has_tabelle_elemente.Anzahl) AS SummevonAnzahl,
                           tabelle_elemente.ElementID,
                           tabelle_elemente.Bezeichnung,
                           tabelle_varianten.Variante,
                           tabelle_varianten.idtabelle_Varianten,
                           tabelle_räume_has_tabelle_elemente.`Neu/Bestand`,
                           tabelle_projekt_varianten_kosten.Kosten,
                           tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente,
                           tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern,
                           tabelle_räume_has_tabelle_elemente.tabelle_Lose_Intern_idtabelle_Lose_Intern,
                           tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke,
                           tabelle_projekt_element_gewerk.tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG,
                           tabelle_projekt_element_gewerk.tabelle_auftraggeberg_gug_idtabelle_auftraggeberg_GUG,
                           tabelle_auftraggeber_gewerke.Gewerke_Nr,
                           tabelle_auftraggeber_ghg.GHG,
                           tabelle_auftraggeberg_gug.GUG
                                    FROM tabelle_auftraggeber_gewerke
                                    RIGHT JOIN (tabelle_auftraggeberg_gug RIGHT JOIN (tabelle_auftraggeber_ghg RIGHT JOIN (tabelle_projekt_element_gewerk RIGHT JOIN (tabelle_elemente INNER JOIN (tabelle_räume INNER JOIN (tabelle_varianten INNER JOIN (tabelle_projekt_varianten_kosten INNER JOIN tabelle_räume_has_tabelle_elemente
                                                                                                                                                                                                                                                ON (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente =
                                                                                                                                                                                                                                                    tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) AND
                                                                                                                                                                                                                                                   (tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten =
                                                                                                                                                                                                                                                    tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten))
                                                                                                                                                                                                                  ON tabelle_varianten.idtabelle_Varianten =
                                                                                                                                                                                                                     tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten)
                                                                                                                                                                                        ON (tabelle_räume.tabelle_projekte_idTABELLE_Projekte =
                                                                                                                                                                                            tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte) AND
                                                                                                                                                                                           (tabelle_räume.idTABELLE_Räume =
                                                                                                                                                                                            tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume))
                                                                                                                                                           ON tabelle_elemente.idTABELLE_Elemente =
                                                                                                                                                              tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente)
                                                                                                                ON (tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente =
                                                                                                                    tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente) AND
                                                                                                                   (tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte =
                                                                                                                    tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte))
                                                                           ON tabelle_auftraggeber_ghg.idtabelle_auftraggeber_GHG =
                                                                              tabelle_projekt_element_gewerk.tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG)
                                    ON tabelle_auftraggeberg_gug.idtabelle_auftraggeberg_GUG =
                                        tabelle_projekt_element_gewerk.tabelle_auftraggeberg_gug_idtabelle_auftraggeberg_GUG)
                                    ON tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke =
                                        tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke
                            WHERE (((tabelle_räume_has_tabelle_elemente.Standort) = 1) AND
                                   ((tabelle_räume.tabelle_projekte_idTABELLE_Projekte) = ?))
                            GROUP BY tabelle_elemente.ElementID,
                                     tabelle_varianten.Variante,
                                     tabelle_varianten.idtabelle_Varianten, 
                                     tabelle_räume_has_tabelle_elemente.`Neu/Bestand`,
                                     tabelle_projekt_varianten_kosten.Kosten,
                                     tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente,
                                     tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke,
                                     tabelle_projekt_element_gewerk.tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG,
                                     tabelle_projekt_element_gewerk.tabelle_auftraggeberg_gug_idtabelle_auftraggeberg_GUG
                            ORDER BY SummevonAnzahl DESC ;";

            $stmt = $mysqli->prepare($sql);
            if ($stmt === false) {
                die("Prepare failed: " . $mysqli->error);
            }

            $id = $_SESSION["projectID"] ?? null;
            if ($id === null) {
                die("Session projectID is not set.");
            }

            if (!$stmt->bind_param('i', $id)) {
                die("Bind param failed: " . $stmt->error);
            }

            if (!$stmt->execute()) {
                die("Execute failed: " . $stmt->error);
            }

            $result = $stmt->get_result();
            if ($result === false) {
                die("Getting result set failed: " . $stmt->error);
            }

            $stmt->close();

            echo "<table class='table table-striped table-bordered table-sm table-hover table-hover border border-light border-5' id='tableElementsInProject'>
                    <thead><tr>
                        <th>ID</th>
                        <th>Anzahl</th>
                        <th>ID</th>
                        <th>Element</th>
                        <th>Variante</th>
                        <th>VariantenID</th>
                        <th>Bestand</th>										
                        <th>Kosten </th> <!-- unformatiert -->
                        <th>Kosten</th>
                        <th>Gewerk</th> 
                        <th>GHG</th>
                        <th>GUG</th>
                        <th>Aktion</th>
                        </tr>
                    </thead>
                    <tbody>";

            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row["TABELLE_Elemente_idTABELLE_Elemente"] . "</td>";
                echo "<td id='amount " . $row['TABELLE_Elemente_idTABELLE_Elemente'] . " ' value='" . intval($row["SummevonAnzahl"]) . "' >" . $row["SummevonAnzahl"] . "</td>";
                echo "<td>" . $row["ElementID"] . "</td>";
                echo "<td>" . $row["Bezeichnung"] . "</td>";
                echo "<td>" . $row["Variante"] . "</td>";
                echo "<td>" . $row["idtabelle_Varianten"] . "</td>";
                if ($row["Neu/Bestand"] == 1) {
                    echo "<td>Nein</td>";
                } else {
                    echo "<td>Ja</td>";
                }
                echo "<td>" . (float)$row["Kosten"] . "</td>";
                echo "<td data-order=" . (float)$row["Kosten"] . ">" . format_money($row["Kosten"]) . "</td>";
                echo "<td>" . $row["Gewerke_Nr"] . "</td>";
                echo "<td>" . $row["GHG"] . "</td>";
                echo "<td>" . $row["GUG"] . "</td>";

                // Aktion: Löschen-Button nur bei Anzahl 0, sonst leere Zelle
                $anzahl = intval($row["SummevonAnzahl"]);
                if ($anzahl === 0) {
                    echo "<td class='text-center'>"
                        . "<button type='button' class='btn btn-outline-danger btn-sm deleteVarianteBtn'"
                        . " data-elementid='" . intval($row['TABELLE_Elemente_idTABELLE_Elemente']) . "'"
                        . " data-variantenid='" . intval($row['idtabelle_Varianten']) . "'"
                        . " data-variante='" . htmlspecialchars($row['Variante'] ?? '', ENT_QUOTES, 'UTF-8') . "'"
                        . " title='Variante aus Projekt löschen'>"
                        . "<i class='fas fa-trash'></i></button></td>";
                } else {
                    echo "<td></td>";
                }

                echo "</tr>";
            }
            echo "</tbody></table>";
            $mysqli->close();
            ?>
        </div>
    </div>

    <div class="mt-1 card">
        <div class="card-header  d-flex justify-content-start align-items-center">
            <button type="button" class="btn btn-outline-dark btn-sm me-2" id="showElementVariante"><i
                    class="fas fa-caret-right"></i></button>
            <label>Elementvarianten</label></div>
        <div class="card-body" id="elementInfo">
            <!-- div class="" id="elementGewerk"> within getElementVariante.php </div--->
            <div class="row" id="elementVarianten"></div>

        </div>
    </div>
    <div class="mt-1 card">
        <div class="card-header d-flex justify-content-start align-items-center">
            <button type="button" class="btn btn-outline-dark btn-sm me-2" id="showDBData"><i
                    class="fas fa-caret-right"></i>
            </button>
            <label>Datenbank-Vergleichsdaten</label></div>
        <div class="card-body" style="display:none" id="dbData">
            <div class="row">
                <div class='col-xxl-6'>
                    <div class='mt-1 card'>
                        <div class='card-header d-flex justify-content-between align-items-center'>
                            <label class="mb-0">DB-Elementparameter</label>
                            <button type='button' id='saveDBParamsToProject' class='btn btn-outline-dark btn-sm'>
                                <i class='fas fa-upload'></i> Ins Projekt übernehmen
                            </button>
                        </div>
                        <div class='card-body' id='elementDBParameter'></div>
                    </div>
                </div>
                <div class="col-xxl-6">
                    <div class="mt-1 card">
                        <div class="card-header"><label>Elementkosten in anderen Projekten</label></div>
                        <div class="card-body" id="elementPricesInOtherProjects"></div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class='col-xxl-4'>
                    <div class='mt-1 card'>
                        <div class='card-header'><label>Geräte zu Element</label></div>
                        <div class='card-body' id='devicesToElement'></div>
                    </div>
                </div>
                <div class='col-xxl-4'>
                    <div class='mt-1 card'>
                        <div class='card-header'><label>Geräteparameter</label></div>
                        <div class='card-body' id='deviceParametersInDB'></div>
                    </div>
                </div>
                <div class="col-xxl-4">
                    <div class="mt-1 card">
                        <div class="card-header"><label>Gerätepreise</label></div>
                        <div class="card-body" id="devicePrices"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Räume mit Element -->
    <div class="mt-1 card">
        <div class="card-header">
            <div class="row  d-flex flex-nowrap text-nowrap">
                <div class="col-xxl-6 col-6 d-flex justify-content-start align-items-center">
                    <button type="button" class="btn btn-outline-dark btn-sm me-2" id="showRoomsWithAndWithoutElement">
                        <i class="fas fa-caret-right"></i>
                    </button>
                    <label>Räume mit Element</label>
                </div>
                <div class="col-6 d-inline-flex align-items-center justify-content-end" id="CHRME">
                </div>
            </div>
        </div>
        <div class="card-body" id="roomsWithAndWithoutElements" style="display:none"></div>
    </div>
</div>

<!-- Variante-löschen Bestätigungs-Modal -->
<div class="modal fade" id="deleteVarianteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Variante aus Projekt löschen?</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Schließen"></button>
            </div>
            <div class="modal-body" id="deleteVarianteBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm" id="confirmDeleteVariante">
                    <i class="fas fa-trash"></i> Ja, löschen
                </button>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Abbrechen</button>
            </div>
        </div>
    </div>
</div>

<script src="utils/_utils.js"></script>
<script charset="utf-8">
    var tableElementsInProject;
    var tableRoomsWithElement; // for getRoomsWithElement1

    $(document).ready(function () {
        tableElementsInProject = new DataTable('#tableElementsInProject', {
            paging: true,
            select: true,
            pagingType: 'simple',
            lengthChange: true,
            pageLength: 10,
            order: [[1, 'asc']],
            columnDefs: [
                {
                    targets: [0, 5, 7],
                    visible: false,
                    searchable: false
                },
                {
                    targets: -1, // Aktions-Spalte: sichtbar, aber nicht sortier-/durchsuchbar
                    orderable: false,
                    searchable: false
                }
            ],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json',
                search: "",
                searchPlaceholder: "Suche...",
                searchBuilder: {
                    button: '(%d)'
                }
            },
            stateSave: false,
            layout: {
                topStart: null,
                topEnd: ['buttons', 'search'],
                bottomStart: ['info', 'pageLength'],
                bottomEnd: 'paging'
            },
            buttons: [
                {
                    extend: 'excel',
                    exportOptions: {
                        columns: ':not(:nth-child(6)):not(:nth-child(13))'
                    },
                    text: '<i class="fas fa-file-excel"></i> Excel',
                    className: 'btn-sm btn-light btn-outline-dark me-2'
                },
                {
                    extend: 'searchBuilder',
                    text: "Filter (%d)",
                    className: "btn btn-light btn-outline-secondary fas fa-search",
                    titleAttr: "Filter",
                }
            ],
            compact: true,
            initComplete: function () {
                $('.dt-search label').remove();
                $('.dt-search').children()
                    .removeClass('form-control form-control-sm')
                    .addClass("btn btn-sm btn-outline-dark")
                    .appendTo('#target_div');

                setTimeout(function () {
                    tableElementsInProject.buttons().container().appendTo('#target_div .btn-group');
                    // ← Remove the separate Buttons instantiation entirely
                }, 100);
            }
        });

        $('#tableElementsInProject tbody').on('click', 'tr', function (e) {
            // Klick auf den Löschen-Button darf NICHT das Zeilen-Laden auslösen
            if ($(e.target).closest('.deleteVarianteBtn').length) {
                return;
            }

            let elementID = tableElementsInProject.row($(this)).data()[0];
            let variantenID = tableElementsInProject.row($(this)).data()[5];
            let bestand = 1;
            if (tableElementsInProject.row($(this)).data()[6] === "Ja") {
                bestand = 0;
            }
            $.ajax({
                url: "getRoomsWithElement1.php",
                data: {"elementID": elementID, "variantenID": variantenID, "bestand": bestand},
                type: "POST",
                success: function (data) {
                    let $table = $('#tableRoomsWithElement');
                    if ($table.length && $.fn.DataTable && $.fn.DataTable.isDataTable) {
                        if ($.fn.DataTable.isDataTable($table)) {
                            $table.DataTable().destroy();
                        }
                    }
                    $("#roomsWithAndWithoutElements").html(data);
                    $.ajax({
                        url: "getElementVariante.php",
                        data: {"elementID": elementID, "variantenID": variantenID},
                        type: "POST",
                        success: function (data) {
                            $("#elementVarianten").html(data);
                            $.ajax({
                                url: "getStandardElementParameters.php",
                                data: {"elementID": elementID},
                                type: "POST",
                                success: function (data) {
                                    $("#elementDBParameter").html(data);
                                    $.ajax({
                                        url: "getElementPricesInDifferentProjects.php",
                                        data: {"elementID": elementID},
                                        type: "POST",
                                        success: function (data) {
                                            // console.log(data);
                                            $("#elementPricesInOtherProjects").html(data);
                                            $.ajax({
                                                url: "getDevicesToElement.php",
                                                data: {"elementID": elementID},
                                                type: "POST",
                                                success: function (data) {
                                                    $("#devicesToElement").html(data);
                                                    console.log(elementID);

                                                    $.ajax({
                                                        url: "getElementGewerke.php",
                                                        data: {"elementID": elementID},
                                                        type: "POST",
                                                        success: function (data) {
                                                            $("#elementGewerk").html(data);
                                                        }
                                                    });
                                                }
                                            });
                                        }
                                    });
                                }
                            });
                        }
                    });
                }
            });

        });

        // --- Variante aus dem Projekt löschen ---------------------------------
        let pendingDelete = {elementID: null, variantenID: null};

        // Delegiert an die Tabelle, damit auch nach Paging/Redraw gebunden bleibt
        $('#tableElementsInProject').on('click', '.deleteVarianteBtn', function (e) {
            e.stopPropagation(); // keine Zeilenauswahl / kein Zeilen-Laden
            pendingDelete.elementID = $(this).data('elementid');
            pendingDelete.variantenID = $(this).data('variantenid');
            let variante = $(this).data('variante');

            $('#deleteVarianteBody').html(
                "Variante <strong>" + variante + "</strong> (Element-ID " + pendingDelete.elementID + ") " +
                "wird für <u>dieses Projekt</u> unwiderruflich gelöscht – inklusive Raumzuordnungen, " +
                "Kosten und Variantenparameter.<br><br>Fortfahren?"
            );

            let modal = new bootstrap.Modal(document.getElementById('deleteVarianteModal'));
            modal.show();
        });

        $('#confirmDeleteVariante').on('click', function () {
            let $btn = $(this);
            $btn.prop('disabled', true).html("<i class='fas fa-spinner fa-spin'></i> Löscht...");

            $.ajax({
                url: "deleteVarianteFromProject.php",
                data: {
                    elementID: pendingDelete.elementID,
                    variantenID: pendingDelete.variantenID 
                },
                type: "POST"
            }).done(function (data) {
                makeToaster(data.trim(), true);
                let modalEl = document.getElementById('deleteVarianteModal');
                bootstrap.Modal.getInstance(modalEl).hide();
                // Sauberer Neustand: Tabelle inkl. abhängiger Panels neu laden
                location.reload();
            }).fail(function (xhr) {
                let msg = (xhr && xhr.responseText) ? xhr.responseText.trim() : "Fehler beim Löschen!";
                makeToaster(msg, false);
            }).always(function () {
                $btn.prop('disabled', false).html("<i class='fas fa-trash'></i> Ja, löschen");
            });
        });

        $('#deleteVarianteModal').on('keydown', function (e) {
            if (e.key === 'Enter' && !e.repeat) {
                e.preventDefault();
                const btn = document.getElementById('confirmDeleteVariante');
                if (!btn.disabled) btn.click();
            }
        });
        // ---------------------------------------------------------------------

        let filterIndex = $.fn.dataTable.ext.search.indexOf(hideZeroFilter_ELiNpR);
        if (filterIndex !== -1) {
            $.fn.dataTable.ext.search.splice(filterIndex, 1);
        }
        $.fn.dataTable.ext.search.push(hideZeroFilter_ELiNpR);

        $("#hideZeroRows_ELiNpR").on("change", function () {
            tableElementsInProject.draw();
        });

        function hideZeroFilter_ELiNpR(settings, data,) {
            if (settings.nTable.id !== 'tableElementsInProject') {
                return true;
            }
            let hideZero = $("#hideZeroRows_ELiNpR").is(":checked");
            let amount = parseInt(data[1]) || 0;
            return !(hideZero && (amount === 0));
        }


    });


    // ElementVariantenPanel einblenden
    $("#showElementVariante").click(function () {
        if ($("#elementInfo").is(':hidden')) {
            $(this).html("<i class='fas fa-caret-down'></i>");
            $("#elementInfo").show();
        } else {
            $(this).html("<i class='fas fa-caret-right'></i>");
            $("#elementInfo").hide();
        }
    });

    // DB Element/Gerätedaten einblenden
    $("#showDBData").click(function () {
        if ($("#dbData").is(':hidden')) {
            $(this).html("<i class='fas fa-caret-down'></i>");
            $("#dbData").show();
        } else {
            $(this).html("<i class='fas fa-caret-right'></i>");
            $("#dbData").hide();
        }
    });

    // Räume mit und ohne Element einblenden
    $("#showRoomsWithAndWithoutElement").click(function () {
        if ($("#roomsWithAndWithoutElements").is(':hidden')) {
            $(this).html("<i class='fas fa-caret-down'></i>");
            $("#roomsWithAndWithoutElements").show();
        } else {
            $(this).html("<i class='fas fa-caret-right'></i>");
            $("#roomsWithAndWithoutElements").hide();
        }
    });

    // PDF erzeugen
    $('#createElementListPDF').click(function () {
        window.open('PDFs/pdf_createElementListPDF.php');
    });

    $('#createElementListWithPricePDF').click(function () {
        window.open('PDFs/pdf_createElementListWithPricePDF.php');
    });

    $('#createElementEinbringwegePDF').click(function () {
        window.open('PDFs/pdf_createElementEinbringwegePDF.php');
    });
    $('#createElementEinbringwegePDF2').click(function () {
        window.open('PDFs/pdf_createElementEinbringwegePDF2.php');
    });


</script>
</body>
</html>