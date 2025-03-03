<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>RB-Lose-Elemente</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style.css" type="text/css" media="screen"/>
    <link rel="icon" href="iphone_favicon.png">

    <!-- Rework 2025 CDNs -->
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


    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
    <!--DATEPICKER -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/css/bootstrap-datepicker3.min.css">
    <script type='text/javascript'
            src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/js/bootstrap-datepicker.min.js"></script>

</head>
<body style="height:100%">
<div id="limet-navbar"></div> <!-- Container für Navbar -->
<div class="container-fluid">
    <div class="mt-4 card">
        <div class="card-header">
            <div class="row">
                <div class="col-6"><b>Elemente im Projekt</b></div>
                <div id="ElInPrCardHeader" class="col-6 d-flex justify-content-end"></div>
            </div>
        </div>
        <div class="card-body" id="elementLots">

            <?php
            include '_utils.php';
            init_page_serversides();
            include "_format.php";
            $mysqli = utils_connect_sql();
            $sql = "SELECT Sum(tabelle_räume_has_tabelle_elemente.Anzahl) AS SummevonAnzahl, tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_varianten.Variante, tabelle_räume.`Raumbereich Nutzer`, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, tabelle_projekt_varianten_kosten.Kosten, tabelle_projekt_varianten_kosten.Kosten*Sum(tabelle_räume_has_tabelle_elemente.Anzahl) AS PP, tabelle_lose_extern.LosNr_Extern, tabelle_lose_extern.LosBezeichnung_Extern, tabelle_lose_extern.Ausführungsbeginn, tabelle_lose_extern.idtabelle_Lose_Extern, tabelle_lose_extern.Vergabe_abgeschlossen, tabelle_varianten.idtabelle_Varianten, tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, 
                                                                        tabelle_auftraggeber_gewerke.Gewerke_Nr, tabelle_projektbudgets.Budgetnummer
                                                                        FROM tabelle_projekt_varianten_kosten 
                                                                        INNER JOIN (tabelle_varianten 
                                                                                                INNER JOIN (tabelle_lose_extern 
                                                                                                                        RIGHT JOIN ((tabelle_räume_has_tabelle_elemente 
                                                                                                                                                INNER JOIN tabelle_räume ON tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume = tabelle_räume.idTABELLE_Räume) 
                                                                                                            INNER JOIN tabelle_elemente ON tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente = tabelle_elemente.idTABELLE_Elemente) ON tabelle_lose_extern.idtabelle_Lose_Extern = tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern) ON tabelle_varianten.idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) ON (tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte) AND (tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) AND (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente)
                                                                                                            LEFT JOIN tabelle_projekt_element_gewerk ON tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte=tabelle_räume.tabelle_projekte_idTABELLE_Projekte AND tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente=tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente
                                                                                                            LEFT JOIN tabelle_auftraggeber_gewerke ON tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke = tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke
                                                                                                            LEFT JOIN tabelle_projektbudgets ON tabelle_räume_has_tabelle_elemente.tabelle_projektbudgets_idtabelle_projektbudgets = tabelle_projektbudgets.idtabelle_projektbudgets
                                                                        WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . ") AND ((tabelle_räume_has_tabelle_elemente.Standort)=1))
                                                                        GROUP BY tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_varianten.Variante, tabelle_räume.`Raumbereich Nutzer`, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, tabelle_projekt_varianten_kosten.Kosten, tabelle_lose_extern.LosNr_Extern, tabelle_lose_extern.LosBezeichnung_Extern, tabelle_lose_extern.Ausführungsbeginn, tabelle_lose_extern.idtabelle_Lose_Extern, tabelle_varianten.idtabelle_Varianten, tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, tabelle_projektbudgets.Budgetnummer
                                                                        ORDER BY tabelle_elemente.ElementID;";

            $result = $mysqli->query($sql);

            echo "<table class='table table-sm table-striped table-hover border border-light border-5' id='tableElementsInProject' >
									<thead><tr>
                                        <th></th>
										<th>ID-Element</th>
										<th>ID-Variante</th>
										<th>ID-Los</th>
										<th>Bestand-Wert</th>
										<th>Anzahl</th>
										<th>ID</th>
										<th>Element</th>
										<th>Variante</th>
										<th>Raumbereich</th>
										<th>Bestand</th>                                                                              									
										<th>EP</th>
										<th>PP</th>
										<th>Los-Nr</th>
										<th>Los</th>
										<th>Ausführungsbeginn</th>
                                                        <th>Gewerk</th>
                                                        <th>Budget</th>
                                                        <th>Abgeschlossen</th> 
                                        </tr><tr>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th><b>Stk >0 <input type='checkbox' id='filter_count'></b></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th><select id='filter_bestand'>
                                                    <option value='2'></option>
                                                    <option value='1'>Ja</option>
                                                    <option value='0'>Nein</option>
                                                </select>
                                            </th>                                                                              									
                                            <th></th>
                                            <th></th>
                                            <th><input type='checkbox' id='filter_lot'></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
									</tr>
									</thead><tbody>";

            /**
             * @param array $row
             * @return void
             */
            function extracted(array $row): void
            {
                echo "<td>" . $row["TABELLE_Elemente_idTABELLE_Elemente"] . "</td>";
                echo "<td>" . $row["idtabelle_Varianten"] . "</td>";
                echo "<td>" . $row["idtabelle_Lose_Extern"] . "</td>";
                echo "<td>" . $row["Neu/Bestand"] . "</td>";
                echo "<td>" . $row["SummevonAnzahl"] . "</td>";
                echo "<td>" . $row["ElementID"] . "</td>";
                echo "<td>" . $row["Bezeichnung"] . "</td>";
                echo "<td>" . $row["Variante"] . "</td>";
                echo "<td>" . $row["Raumbereich Nutzer"] . "</td>";
                if ($row["Neu/Bestand"] == 1) {
                    echo "<td>Nein</td>";
                } else {
                    echo "<td>Ja</td>";
                }
                echo "<td>" . format_money($row["Kosten"]) . "</td>";
            }

            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td></td>";
                extracted($row);
                echo "<td>" . format_money($row["PP"]) . "</td>";
                echo "<td>" . $row["LosNr_Extern"] . "</td>";
                echo "<td>" . $row["LosBezeichnung_Extern"] . "</td>";
                echo "<td>" . $row["Ausführungsbeginn"] . "</td>";
                echo "<td>" . $row["Gewerke_Nr"] . "</td>";
                echo "<td>" . $row["Budgetnummer"] . "</td>";
                echo "<td>";
                switch ($row["Vergabe_abgeschlossen"]) {
                    case 0:
                        //echo "<b><font color='red'>&#10007;</font></b>";
                        echo "<span class='badge badge-pill bg-danger'>Offen</span>";
                        break;
                    case 1:
                        //echo "<b><font color='green'>&#10003;</font></b>";
                        echo "<span class='badge badge-pill bg-success'>Fertig</span>";
                        break;
                    case 2:
                        //echo "<b><font color='blue'>&#8776;</font></b>";
                        echo "<span class='badge badge-pill bg-primary'>Wartend</span>";
                        break;
                }
                echo "</td>";
                echo "</tr>";

            }
            echo "</tbody></table>";
            $mysqli->close();

            ?>
        </div>
    </div>
    <!-- Räume mit Element -->

    <div class="row">
        <div class="col-lg-8">
            <div class="mt-4 card">
                <div class="card-header" id="roomsWithElementCardHeader">Räume mit Element</div>
                <div class="card-body" id="roomsWithElement"></div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="mt-4 card">
                <div class="card-header">Variantenparameter</div>
                <div class="card-body" id="variantenParameter"></div>
            </div>
            <div class="mt-4 card">
                <div class="card-header">Bestandsdaten</div>
                <div class="card-body" id="elementBestand"></div>
            </div>
        </div>
    </div>
</div>

<!--suppress JSUnusedLocalSymbols, ES6ConvertVarToLetConst -->
<script>
    var table;
    $.fn.dataTable.ext.search.push(
        function (settings, data, dataIndex) {
            if (settings.nTable.id !== 'tableElementsInProject') {
                return true;
            }

            if ($("#filter_bestand").val() === '1') {
                if ($("#filter_count").is(':checked')) {
                    if ($("#filter_lot").is(':checked')) {
                        return data [10] === "Ja" && Number(data [5]) > 0 && data[13].length > 0;
                    } else {
                        return data [10] === "Ja" && Number(data [5]) > 0;
                    }
                } else {
                    if ($("#filter_lot").is(':checked')) {
                        return data [10] === "Ja" && data[13].length > 0;
                    } else {
                        return data [10] === "Ja" && Number(data [5]) > 0;
                    }
                }
            } else {
                if ($("#filter_bestand").val() === '0') {
                    if ($("#filter_count").is(':checked')) {
                        if ($("#filter_lot").is(':checked')) {
                            return data [10] === "Nein" && Number(data [5]) > 0 && data[13].length > 0;
                        } else {
                            return data [10] === "Nein" && Number(data [5]) > 0;
                        }
                    } else {
                        if ($("#filter_lot").is(':checked')) {
                            return data [10] === "Nein" && data[13].length > 0;
                        } else {
                            return data [10] === "Nein" && Number(data [5]) > 0;
                        }
                    }
                } else {
                    if ($("#filter_count").is(':checked')) {
                        if ($("#filter_lot").is(':checked')) {
                            return Number(data [5]) > 0 && data[13].length > 0;
                        } else {
                            return Number(data [5]) > 0;
                        }
                    } else {
                        if ($("#filter_lot").is(':checked')) {
                            return data[13].length > 0;
                        } else {
                            return true;
                        }
                    }
                }
            }
        }
    );

    $('#filter_bestand').change(function () {
        table.draw();
    });
    $('#filter_count').change(function () {
        table.draw();
    });
    $('#filter_lot').change(function () {
        table.draw();
    });

    $(document).ready(function () {
        table = new DataTable('#tableElementsInProject', {
            paging: true,
            select: true,
            order: [[6, 'asc']],
            columnDefs: [
                {
                    targets: [0, 1, 2, 3, 4, 15],
                    visible: false,
                    searchable: false
                }
            ],
            orderCellsTop: true,
            pagingType: 'simple',
            lengthChange: false,
            pageLength: 10,
            language: {
                url: '//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json',
                decimal: ',',
                thousands: '.',
                search: ""
            },
            mark: true,
            layout: {
                topEnd: 'search',
                topStart: null,
                bottomStart: 'info',
                bottomEnd: ['pageLength', 'paging']
            }, initComplete: function () {
                $('.dt-search').children().removeClass('form-control form-control-sm').addClass("bg-white").appendTo('#ElInPrCardHeader');

            }
        });

        $('#tableElementsInProject tbody').on('click', 'tr', function () {
            let elementID = table.row($(this)).data()[1];
            let variantenID = table.row($(this)).data()[2];
            let losID = table.row($(this)).data()[3];
            let bestand = table.row($(this)).data()[4];
            let raumbereich = table.row($(this)).data()[9];
            $.ajax({
                url: "getRoomsWithElementTenderLots.php",
                data: {
                    "losID": losID,
                    "variantenID": variantenID,
                    "elementID": elementID,
                    "bestand": bestand,
                    "raumbereich": raumbereich
                },
                type: "GET",
                success: function (data) {
                    $("#roomsWithElement").html(data);
                    $("#elementBestand").hide();
                    $.ajax({
                        url: "getVariantenParameters.php",
                        data: {"variantenID": variantenID, "elementID": elementID},
                        type: "GET",
                        success: function (data) {
                            $("#variantenParameter").html(data);
                        }
                    });
                }
            });


        });
    });

</script>


</body>

</html>
