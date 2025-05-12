<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>RB-Elemente im Projekt</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="stylesheet" href="style.css" type="text/css" media="screen"/>
    <link rel="icon" href="iphone_favicon.png"/>

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

<body>
<!-- Rework 2025 -->
<div class="container-fluid bg-light">
    <div id="limet-navbar"></div>
    <div class="mt-4 card">
        <div class="card-header">
            <div class="row d-flex align-items-center justify-content-between">
                <div class="col-2"><strong> Elemente im Projekt</strong>
                </div>
                <div class="col-10 d-flex align-items-center justify-content-end" id="target_div">
                    <div class="me-4 d-flex " id="sbdiv"></div>
                    <div class="btn-group btn-group-sm" role="group" aria-label="PDF Generation Buttons">
                        <button type='button' class='btn btn-outline-dark me-1' id='createElementListPDF'>
                            <i class='far fa-file-pdf'></i> Elementliste
                        </button>
                        <button type='button' class='btn btn-outline-dark  me-1' id='createElementListWithPricePDF'>
                            <i class='far fa-file-pdf'></i> El.liste & Preis
                        </button>
                        <button type='button' class='btn btn-outline-dark  me-1' id='createElementEinbringwegePDF'>
                            <i class='far fa-file-pdf'></i> Einbringwege
                        </button>
                        <button type='button' class='btn btn-outline-dark  me-1' id='createElementEinbringwegePDF2'>
                            <i class='far fa-file-pdf'></i> Einbringwege2
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body">
            <?php
            if (!function_exists('utils_connect_sql')) {
                include "_utils.php";
            }
            init_page_serversides();
            include "_format.php";
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
       ((tabelle_räume.tabelle_projekte_idTABELLE_Projekte) = " . $_SESSION["projectID"] . "))
GROUP BY tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_varianten.Variante,
         tabelle_varianten.idtabelle_Varianten, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`,
         tabelle_projekt_varianten_kosten.Kosten,
         tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente,
         tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke,
         tabelle_projekt_element_gewerk.tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG,
         tabelle_projekt_element_gewerk.tabelle_auftraggeberg_gug_idtabelle_auftraggeberg_GUG
ORDER BY tabelle_elemente.ElementID;";
            $result = $mysqli->query($sql);
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
									</tr>
									</thead>
									<tbody>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row["TABELLE_Elemente_idTABELLE_Elemente"] . "</td>";
                echo "<td>" . $row["SummevonAnzahl"] . "</td>";
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
                echo "<td>" . format_money($row["Kosten"]) . "</td>";
                echo "<td>" . $row["Gewerke_Nr"] . "</td>";
                echo "<td>" . $row["GHG"] . "</td>";
                echo "<td>" . $row["GUG"] . "</td>";
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
        <div class="card-body" id="elementInfo" style="display:none">
            <div class="mt-1 row" id="elementGewerk"></div>
            <div class="mt-1 row" id="elementVarianten"></div>
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
                        <div class='card-header'><label>DB-Elementparameter</label></div>
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
        <div class="card-header ">
            <div class="row">
                <div class="col-xxl-8 d-flex justify-content-start align-items-center">
                    <button type="button" class="btn btn-outline-dark btn-sm me-2" id="showRoomsWithAndWithoutElement">
                        <i class="fas fa-caret-right"></i>
                    </button>
                    <label>Räume mit Element</label>
                </div>
            </div>
        </div>
        <div class="card-body" id="roomsWithAndWithoutElements" style="display:none"></div>
    </div>


    <script src="_utils.js"></script>
    <script charset="utf-8">

        var tableElementsInProject;
        var tableRoomsWithElement;
        const searchbuilder = [
            {
                extend: 'searchBuilder',
                text: "Filter",
                className: "btn btn-light btn-outline-secondary fas fa-search ",
                titleAttr: "Filter",
            }
        ];

        $(document).ready(function () {
            tableElementsInProject = new DataTable('#tableElementsInProject', {
                paging: true,
                select: true,
                pagingType: 'simple',
                lengthChange: true,
                pageLength: 10,
                order: [[2, 'asc']],
                columnDefs: [
                    {
                        targets: [0, 5, 7],
                        visible: false,
                        searchable: false
                    }
                ],
                language: {
                    search: "",
                    searchPlaceholder: "Suche...",
                    searchBuilder: {
                        button: '(%d)'
                    }
                },
                stateSave: false,
                layout: {
                    topStart: null,
                    topEnd: ['buttons', 'pageLength', 'search'],
                    bottomStart: 'info',
                    bottomEnd: 'paging'
                },
                buttons: [
                    {
                        extend: 'excel',
                        exportOptions: {
                            columns: ':not(:nth-child(6)):not(:nth-child(9))'
                        },
                        text: '<i class="fas fa-file-excel"></i> Excel',
                        className: 'btn-sm btn-light btn-outline-dark me-2'
                    }
                ],
                compact: true,
                initComplete: async function () {
                    $('.dt-search label').remove();
                    $('.dt-search').children().removeClass('form-control form-control-sm').addClass("btn btn-sm btn-outline-dark").appendTo('#target_div');
                    setTimeout(function () {
                        tableElementsInProject.buttons().container().appendTo('#target_div .btn-group');
                        new $.fn.dataTable.Buttons(tableElementsInProject, {buttons: searchbuilder}).container().appendTo('#sbdiv');
                    }, 200);

                }
            });

            $('#tableElementsInProject tbody').on('click', 'tr', function () {
                let elementID = tableElementsInProject.row($(this)).data()[0];
                let variantenID = tableElementsInProject.row($(this)).data()[5];
                let bestand = 1;
                if (tableElementsInProject.row($(this)).data()[6] === "Ja") {
                    bestand = 0;
                }

                $.ajax({
                    url: "getRoomsWithElement1.php",
                    data: {"elementID": elementID, "variantenID": variantenID, "bestand": bestand},
                    type: "GET",
                    success: function (data) {
                        if ($.fn.DataTable.isDataTable('#tableRoomsWithElement')) {
                            tableRoomsWithElement.destroy();
                        }
                        $("#roomsWithAndWithoutElements").html(data);
                        $.ajax({
                            url: "getElementVariante.php",
                            data: {"elementID": elementID, "variantenID": variantenID},
                            type: "GET",
                            success: function (data) {
                                $("#elementVarianten").html(data);
                                $.ajax({
                                    url: "getStandardElementParameters.php",
                                    data: {"elementID": elementID},
                                    type: "GET",
                                    success: function (data) {
                                        $("#elementDBParameter").html(data);
                                        $.ajax({
                                            url: "getElementPricesInDifferentProjects.php",
                                            data: {"elementID": elementID},
                                            type: "GET",
                                            success: function (data) {
                                                $("#elementPricesInOtherProjects").html(data);
                                                $.ajax({
                                                    url: "getDevicesToElement.php",
                                                    data: {"elementID": elementID},
                                                    type: "GET",
                                                    success: function (data) {
                                                        $("#devicesToElement").html(data);
                                                        $.ajax({
                                                            url: "getElementGewerke.php",
                                                            data: {"elementID": elementID},
                                                            type: "GET",
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
            window.open('/pdf_createElementListPDF.php');
        });

        $('#createElementListWithPricePDF').click(function () {
            window.open('/pdf_createElementListWithPricePDF.php');
        });

        $('#createElementEinbringwegePDF').click(function () {
            window.open('/pdf_createElementEinbringwegePDF.php');
        });
        $('#createElementEinbringwegePDF2').click(function () {
            window.open('/pdf_createElementEinbringwegePDFschöner.php');
        });
    </script>
</body>
</html>
