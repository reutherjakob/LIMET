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

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

</head>
<body style="height:100%">
<div id="limet-navbar"></div> <!-- Container für Navbar -->
<div class="container-fluid">
    <div class="mt-1 card">


        <?php
        if (!function_exists('utils_connect_sql')) {
            include "_utils.php";
        }
        init_page_serversides();
        include "_format.php";


        function makeTable($result): void
        {
            $headers = [
                '', 'ID-Element', 'ID-Variante', 'ID-Los', 'Bestand-Wert', 'Anzahl', 'ID', 'Element', 'Var', 'Raumbereich', 'Bauabschnitt',
                'Bestand', 'EP', 'PP', 'Los-Nr', 'Los', 'Ausführungsbeginn', 'Gewerk', 'Budget', 'Abgeschlossen'
            ];

            $filters = [
                '', '', '', '', '',
                "<b>Stk >0 <input type='checkbox' id='filter_count'></b>",
                '', '', '', '', '',
                "<select id='filter_bestand'>
            <option value='2'></option> 
            <option value='1'>Ja</option>
            <option value='0'>Nein</option>
        </select>",
                '', '',
                "<input type='checkbox' id='filter_lot'>",
                '', '', '', '', ''
            ];

            $statusBadges = [
                0 => "<span class='badge badge-pill bg-danger'>Offen</span>",
                1 => "<span class='badge badge-pill bg-success'>Fertig</span>",
                2 => "<span class='badge badge-pill bg-primary'>Wartend</span>"
            ];

            echo "<table class='table table-sm table-striped table-hover border border-light border-5' id='tableElementsInProject'>";
            echo "<thead>";
            echo "<tr>" . implode('', array_map(fn($h) => "<th>$h</th>", $headers)) . "</tr>";
            echo "<tr>" . implode('', array_map(fn($f) => "<th>$f</th>", $filters)) . "</tr>";
            echo "</thead><tbody>";

            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td></td>";
                echo "<td>{$row['TABELLE_Elemente_idTABELLE_Elemente']}</td>";
                echo "<td>{$row['idtabelle_Varianten']}</td>";
                echo "<td>{$row['idtabelle_Lose_Extern']}</td>";
                echo "<td>{$row['Neu/Bestand']}</td>";
                echo "<td>{$row['SummevonAnzahl']}</td>";
                echo "<td>{$row['ElementID']}</td>";
                echo "<td>{$row['Bezeichnung']}</td>";
                echo "<td>{$row['Variante']}</td>";
                echo "<td>{$row['Raumbereich Nutzer']}</td>";
                echo "<td>{$row['Bauabschnitt']}</td>";
                echo "<td>" . ($row['Neu/Bestand'] == 1 ? 'Nein' : 'Ja') . "</td>";
                echo "<td>" . format_money($row['Kosten']) . "</td>";
                echo "<td>" . format_money($row['PP']) . "</td>";
                echo "<td>{$row['LosNr_Extern']}</td>";
                echo "<td>{$row['LosBezeichnung_Extern']}</td>";
                echo "<td>{$row['Ausführungsbeginn']}</td>";
                echo "<td>{$row['Gewerke_Nr']}</td>";
                echo "<td>{$row['Budgetnummer']}</td>";
                echo "<td>" . ($statusBadges[$row['Vergabe_abgeschlossen']] ?? '') . "</td>";
                echo "</tr>";
            }
            echo "</tbody></table>";
        }


        echo '<div class="card-header ">
                    <div class="row "> 
                        <div class="col-3">  <b>Elemente im Projekt</b>  </div>
                        <div class="col-3 d-flex justify-content-between">                               <!--div id="groupCheckboxes">  <b>Group Data By:</b>    </div -->
                        </div>
                        <div id="ElInPrCardHeader" class="col-xxl-6 d-inline-flex align-items-center justify-content-end"> (Änderungen nicht in Tabelle? - Reload!) &emsp;               </div>
                    </div>
                </div>';


        echo '<div class="card-body" id="elementLots">';


        $mysqli = utils_connect_sql();
        $sql = "SELECT SUM(tabelle_räume_has_tabelle_elemente.Anzahl) AS SummevonAnzahl, tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, 
                tabelle_varianten.Variante, tabelle_räume.`Raumbereich Nutzer`, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, tabelle_projekt_varianten_kosten.Kosten, 
                tabelle_projekt_varianten_kosten.Kosten*Sum(tabelle_räume_has_tabelle_elemente.Anzahl) AS PP, tabelle_lose_extern.LosNr_Extern, tabelle_lose_extern.LosBezeichnung_Extern, 
                tabelle_lose_extern.Ausführungsbeginn, tabelle_lose_extern.idtabelle_Lose_Extern, tabelle_lose_extern.Vergabe_abgeschlossen, 
                tabelle_varianten.idtabelle_Varianten, tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, 
                tabelle_auftraggeber_gewerke.Gewerke_Nr, tabelle_projektbudgets.Budgetnummer, tabelle_räume.Bauabschnitt
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
                GROUP BY 
                tabelle_elemente.ElementID, 
                tabelle_varianten.idtabelle_Varianten, 
                 tabelle_varianten.Variante,       
                tabelle_räume.`Raumbereich Nutzer`,
                tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, 
                tabelle_lose_extern.idtabelle_Lose_Extern,
                tabelle_projektbudgets.Budgetnummer
                ORDER BY tabelle_elemente.ElementID;";

        //GROUP BY       tabelle_lose_extern.LosNr_Extern,
        //                tabelle_lose_extern.LosBezeichnung_Extern,
        //                tabelle_lose_extern.Ausführungsbeginn,     tabelle_varianten.Variante,
        // tabelle_elemente.Bezeichnung,   tabelle_projekt_varianten_kosten.Kosten,        tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente,

        $result = $mysqli->query($sql);
        makeTable($result);
        $mysqli->close();
        ?>
    </div>
</div>
<!-- Räume mit Element -->

<div class="row">
    <div class="col-xxl-8">
        <div class="mt-4 card">
            <div class="card-header d-flex align-items-center">
                <div class="col-6">Räume mit Element</div>
                <div class="col-6  d-flex align-items-center justify-content-end" id="roomsWithElementCardHeader"></div>
            </div>
            <div class="card-body px-1 py-1" id="roomsWithElement"></div>
        </div>
    </div>
    <div class="col-xxl-4">
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


<!--suppress JSUnusedLocalSymbols, ES6ConvertVarToLetConst -->
<script src="_utils.js"></script>
<script>
    var tableElementsInProject;

    const COLUMNS = {
        BESTAND: 11,
        COUNT: 5,
        LOT: 14
    };

    $.fn.dataTable.ext.search.push(function (settings, data) {
        if (settings.nTable.id !== 'tableElementsInProject') return true;

        const bestandVal = data[COLUMNS.BESTAND];
        const countVal = Number(data[COLUMNS.COUNT]);
        const lotVal = data[COLUMNS.LOT];
        const filterState = $("#filter_bestand").val();
        const checkCount = $("#filter_count").is(':checked');
        const checkLot = $("#filter_lot").is(':checked');

        // Bestand filter logic
        const bestandFilter = filterState === '1' ? "Ja" :
            filterState === '0' ? "Nein" : null;

        if (bestandFilter && bestandVal !== bestandFilter) return false;

        // Conditional checks
        const countCheck = checkCount ? countVal > 0 :
            !checkLot && bestandFilter ? countVal > 0 : true;

        const lotCheck = checkLot ? lotVal.length > 0 :
            !checkCount && bestandFilter ? true : true;

        return countCheck && lotCheck;
    });


    $('#filter_bestand').change(function () {
        tableElementsInProject.draw();
    });
    $('#filter_count').change(function () {
        tableElementsInProject.draw();
    });
    $('#filter_lot').change(function () {
        tableElementsInProject.draw();
    });

    $(document).ready(function () {

        tableElementsInProject = new DataTable('#tableElementsInProject', {
            paging: true,
            select: true,
            order: [[6, 'asc']],
            // columnDefs: [
            //     {
            //         targets: [0, 1, 2, 3, 4],
            //         visible: false,
            //         searchable: false
            //     }
            // ],
            orderCellsTop: true,
            pagingType: 'simple',
            lengthChange: true,
            pageLength: 10,
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json',
                decimal: ',',
                thousands: '.',
                search: "",
                searchPlaceholder: "Suche... "
            },
            mark: true,
            layout: {
                topEnd: 'search',
                topStart: null,
                bottomStart: 'info',
                bottomEnd: ['pageLength', 'paging']

            }, initComplete: function () {
                $('.dt-search input').addClass("btn btn-sm btn-outline-dark");
                $('.dt-search').children().removeClass('form-control form-control-sm').addClass("d-flex align-items-center").appendTo('#ElInPrCardHeader');
            }
        });

        const searchbuilder = [
            {
                extend: 'searchBuilder',
                text: " ",
                className: "fa fa-search",
                titleAttr: "searchBuilder"
            }
        ];

        new $.fn.dataTable.Buttons(tableElementsInProject, {buttons: searchbuilder}).container().appendTo($('#ElInPrCardHeader'));
        $('.dt-buttons').children().children().remove();

        $('#tableElementsInProject tbody').on('click', 'tr', function () {
            let elementID = tableElementsInProject.row($(this)).data()[1];
            let variante = tableElementsInProject.row($(this)).data()[8];
            let variantenID = letterToNumber(variante);
            let losID = tableElementsInProject.row($(this)).data()[3];
            let bestand = tableElementsInProject.row($(this)).data()[4];
            let raumbereich = decodeHtmlEntities(tableElementsInProject.row($(this)).data()[9]);
            let bauabschnitt = tableElementsInProject.row($(this)).data()[10];
            console.log(variantenID, losID, bestand, raumbereich);
            $.ajax({
                url: "getRoomsWithElementTenderLots.php",
                data: {
                    "losID": losID,
                    "variantenID": variantenID,
                    "elementID": elementID,
                    "bestand": bestand,
                    "raumbereich": raumbereich,
                    "bauabschnitt": bauabschnitt
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

    function decodeHtmlEntities(str) {
        let txt = document.createElement('textarea');
        txt.innerHTML = str;
        return txt.value;
    }

    function letterToNumber(letter) {
        if (typeof letter !== 'string' || letter.length !== 1 || !/[a-zA-Z]/.test(letter)) {
            return null;
        }
        let upper = letter.toUpperCase();
        return upper.charCodeAt(0) - 'A'.charCodeAt(0) + 1;
    }

</script>
</body>
</html>
