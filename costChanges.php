<?php
if (!function_exists('utils_connect_sql')) {
    include "_utils.php";
}
init_page_serversides();
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>RB-Kostenänderungen</title>
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
</head>

<body>
<div class="container-fluid bg-light">
    <div id="limet-navbar"></div>
    <div class="mt-41 card">
        <div class="card-header">
            <div class="row">
                <div class="col-xxl-6">
                    <b>Raumbuchänderungen</b>
                </div>
                <div class="col-xxl-6 d-inline-flex justify-content-end align-items-center" id="CardHeader"> &emsp; <i
                            class='fas fa-hourglass-start'>=Vorher</i> &emsp; <i
                            class='fas fa-hourglass-end'>=Nachher</i> &emsp;
                </div>
            </div>


        </div>
        <div class="card-body">
            <?php
            $mysqli = utils_connect_sql();
            $sql = "SELECT tabelle_räume.`Raumbereich Nutzer`, tabelle_räume.Raumnr,tabelle_räume.Raumnummer_Nutzer, tabelle_räume.Raumnummer_Nutzer, tabelle_räume.Raumbezeichnung, tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_rb_aenderung.Timestamp, tabelle_rb_aenderung.User, tabelle_rb_aenderung.Anzahl, tabelle_rb_aenderung.Anzahl_copy1, tabelle_rb_aenderung.tabelle_Varianten_idtabelle_Varianten, tabelle_rb_aenderung.tabelle_Varianten_idtabelle_Varianten_copy1, tabelle_elemente.idTABELLE_Elemente, tabelle_varianten.Variante AS Var_Alt, tabelle_varianten_1.Variante AS Var_Neu, tabelle_rb_aenderung.`Neu/Bestand`, tabelle_rb_aenderung.`Neu/Bestand_copy1`, tabelle_rb_aenderung.Standort, tabelle_rb_aenderung.Standort_copy1
                                FROM tabelle_varianten AS tabelle_varianten_1 RIGHT JOIN (tabelle_varianten RIGHT JOIN (tabelle_elemente INNER JOIN (tabelle_rb_aenderung INNER JOIN (tabelle_räume_has_tabelle_elemente INNER JOIN tabelle_räume ON tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume = tabelle_räume.idTABELLE_Räume) ON tabelle_rb_aenderung.id = tabelle_räume_has_tabelle_elemente.id) ON tabelle_elemente.idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) ON tabelle_varianten.idtabelle_Varianten = tabelle_rb_aenderung.tabelle_Varianten_idtabelle_Varianten) ON tabelle_varianten_1.idtabelle_Varianten = tabelle_rb_aenderung.tabelle_Varianten_idtabelle_Varianten_copy1
                                WHERE (((Not (tabelle_rb_aenderung.Anzahl)=`tabelle_rb_aenderung`.`Anzahl_copy1`)) AND ((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . ")) OR (((tabelle_rb_aenderung.Anzahl) Is Null) AND ((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . ")) OR (((Not (tabelle_rb_aenderung.tabelle_Varianten_idtabelle_Varianten)=`tabelle_rb_aenderung`.`tabelle_Varianten_idtabelle_Varianten_copy1`)) AND ((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . ")) OR (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . ") AND ((Not (tabelle_rb_aenderung.`Neu/Bestand`)=`tabelle_rb_aenderung`.`Neu/Bestand_copy1`))) OR (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . ") AND ((Not (tabelle_rb_aenderung.Standort)=`tabelle_rb_aenderung`.`Standort_copy1`)))
                                ORDER BY tabelle_rb_aenderung.Timestamp DESC;";
            $result = $mysqli->query($sql);

            echo "<table class='table compact text-nowrap table-sm table-hover table-bordered border border-light border-5 table-striped' id='tableCostChanges'>
    <thead>
        <tr>
            <th class='text-center' colspan='3'>Raum</th>
            <th rowspan='2'>Element</th>
            <th rowspan='2'>Datum</th>
            <th rowspan='2'>Nutzer</th>
            <th colspan='2'>Stk</th>
            <th colspan='2'>Var</th>
            <th colspan='2'>Bestand</th>
            <th colspan='2'>Standort</th>
            <th rowspan='2'></th>
        </tr>
        <tr>
            <th>Bereich Nutzer</th>
            <th>Nr</th>
            <th>Bezeichnung</th>
            <th ><i class='fas fa-hourglass-start'></i></th>
            <th ><i class='fas fa-hourglass-end'></i></th>
            <th ><i class='fas fa-hourglass-start'></i></th>
            <th ><i class='fas fa-hourglass-end'></i></th>
            <th ><i class='fas fa-hourglass-start'></i></th>
            <th ><i class='fas fa-hourglass-end'></i></th>
            <th ><i class='fas fa-hourglass-start'></i></th>
            <th ><i class='fas fa-hourglass-end'></i></th>
        </tr>
    </thead>
    <tbody>";


            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td data-order='" . $row["Raumbereich Nutzer"] . "' >" . $row["Raumbereich Nutzer"] . "</td>";
                if ($_SESSION["projectName"] === "GCP") {
                    echo "<td data-order='" . $row["Raumnummer_Nutzer"] . "' >" . $row["Raumnummer_Nutzer"] . "</td>";
                } else {
                    echo "<td data-order='" . $row["Raumnr"] . "' >" . $row["Raumnr"] . "</td>";
                }
                echo "<td data-order='" . $row["Raumbezeichnung"] . "' >" . $row["Raumbezeichnung"] . "</td>";
                echo "<td  class='border-left border-dark-subtle' data-order='" . $row["ElementID"] . " - " . $row["Bezeichnung"] . "' >" . $row["ElementID"] . " - " . $row["Bezeichnung"] . "</td>";
                echo "<td data-order='" . $row["Timestamp"] . "' >" . $row["Timestamp"] . "</td>";
                echo "<td  class='border-left border-dark-subtle' data-order='" . $row["User"] . "' >" . $row["User"] . "</td>";
                echo "<td  class='border-left border-dark-subtle' data-order='" . $row["Anzahl"] . "' >" . $row["Anzahl"] . "</td>";
                echo "<td  class='border-left border-dark-subtle' data-order='" . $row["Anzahl_copy1"] . "' >" . $row["Anzahl_copy1"] . "</td>";
                echo "<td  class='border-left border-dark-subtle' data-order='" . $row["Var_Alt"] . "' >" . $row["Var_Alt"] . "</td>";
                echo "<td  class='border-left border-dark-subtle' data-order='" . $row["Var_Neu"] . "' >" . $row["Var_Neu"] . "</td>";
                echo "<td  class='border-left border-dark-subtle' data-order='" . $row["Neu/Bestand"] . "' >" . $row["Neu/Bestand"] . "</td>";
                echo "<td  class='border-left border-dark-subtle' data-order='" . $row["Neu/Bestand_copy1"] . "' >" . $row["Neu/Bestand_copy1"] . "</td>";
                echo "<td  class='border-left border-dark-subtle' data-order='" . $row["Standort"] . "' >" . $row["Standort"] . "</td>";
                echo "<td  class='border-left border-dark-subtle' data-order='" . $row["Standort_copy1"] . "' >" . $row["Standort_copy1"] . "</td>";
                echo "<td  class='border-left border-dark-subtle'><button type='button' id='" . $row["idTABELLE_Elemente"] . "' class='btn btn-outline-dark btn-sm' value='showVarianteCostChanges'  data-bs-toggle='modal' data-bs-target='#getElementPriceHistoryModal'> <i class='far fa-money-bill-alt'></i>-Änderungen </button></td>";
                echo "</tr>";
            }
            echo "</tbody></table>";
            ?>
        </div>
    </div>

    <!-- Modal zum Zeigen der Kostenänderungen -->
    <div class='modal fade' id='getElementPriceHistoryModal' role='dialog' tabindex="-1">
        <div class='modal-dialog modal-xl'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <h4 class='modal-title'>Varianten-Kostenänderungen</h4>
                    <button type='button' class='close' data-bs-dismiss='modal'>&times;</button>
                </div>
                <div class='modal-body' id='mbody'>
                </div>
                <div class='modal-footer'>
                    <button type='button' class='btn btn-danger btn-sm' data-bs-dismiss='modal'>Schließen</button>
                </div>
            </div>
        </div>
    </div>

    <script>

        // Tabellen formatieren
        $(document).ready(function () {
            new DataTable('#tableCostChanges', {
                paging: true,
                pagingType: "full_numbers",
                info: true,
                order: [[4, "desc"]],
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json",
                    search: "",
                    sSearchPlaceholder: "Suche.."
                },

                lengthChange: true,

                layout: {
                    topStart: null,
                    topEnd: null,
                    bottomEnd: 'paging',
                    bottomStart: ['info', 'buttons', 'pageLength', 'search']
                },
                buttons: [
                    'excel', 'copy', 'csv'
                ], initComplete: function () {
                    $('.dt-buttons button').addClass("me-1 ms-1 btn-sm btn-dark text-light");
                    $('.dt-buttons').appendTo('#CardHeader');
                    $('.dt-search input').addClass("btn btn-sm btn-outline-dark");
                    $('.dt-search').children().removeClass('form-control form-control-sm').addClass("d-flex align-items-center").appendTo('#CardHeader');
                }
            });
        });

        $("button[value='showVarianteCostChanges']").click(function () {
            let ID = this.id;
            $.ajax({
                url: "getVarianteCostChanges.php",
                type: "GET",
                data: {"elementID": ID},
                success: function (data) {
                    $("#mbody").html(data);
                }
            });
        });
    </script>
</body>
</html>
