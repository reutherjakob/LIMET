<?php
require_once 'utils/_utils.php';
include "utils/_format.php";
init_page_serversides();
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>KOR_Elementliste</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen"/>
    <link rel="icon" href="Logo/iphone_favicon.png">
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

<body style="height:100%">
<div class="container-fluid bg-light">
    <div id="limet-navbar"></div>
    <div class="mt-2 card">
        <div class="card-header">
            <div class="row">
                <div class="col-6">
                    <b>Elemente im Projekt – Gleichzeitigkeit</b>
                </div>
                <div class="col-6 d-flex justify-content-end" id="dt-header-container"></div>
            </div>
        </div>
        <div class="card-body">
            <?php
            function varianteToLetter(int $id): string
            {
                return $id > 0 ? chr(64 + $id) : '—';
            }

            $mysqli = utils_connect_sql();

            $sql = "SELECT
                        tabelle_räume.Raumnr,
                        tabelle_räume.Raumnummer_Nutzer,
                        tabelle_räume.Raumbezeichnung,
                        tabelle_räume.`Raumbereich Nutzer`,
                        tabelle_räume.Geschoss,
                        tabelle_elemente.ElementID,
                        tabelle_elemente.Bezeichnung AS ElementBezeichnung,
                        tabelle_räume_has_tabelle_elemente.Anzahl,
                        CASE WHEN LOWER(tabelle_räume.Raumbezeichnung) IN ( 'gerätelager', 
                                                                            'lager rollstühle',
                                                                            'liegenlager rein',
                                                                            'Geräteraum',
                                                                            'Anästhesie-Geräteraum' ,
                                                                            'Anästhesiegeräte Rüstraum') 
                             THEN 0 
                             ELSE 1 
                        END AS Gleichzeitigkeit,
                         tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten
                    FROM tabelle_räume
                        INNER JOIN tabelle_räume_has_tabelle_elemente
                            ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume
                        INNER JOIN tabelle_elemente
                            ON tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente = tabelle_elemente.idTABELLE_Elemente

                    WHERE tabelle_räume.tabelle_projekte_idTABELLE_Projekte = ?
                        AND tabelle_räume_has_tabelle_elemente.Anzahl <> 0
                    ORDER BY tabelle_räume.Raumnr, tabelle_elemente.ElementID";

            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param('i', $_SESSION["projectID"]);
            $stmt->execute();
            $result = $stmt->get_result();

            echo "<table class='table table-striped table-hover compact table-bordered' id='tableGLZ'>
                <thead><tr>
                    <th>Raumnummer</th>
                    <th>Raumbezeichnung</th>
                    <th>Raumbereich</th>
                    <th>Geschoss</th>
                    <th>Bauteilelement ID</th>
                    <th>Bauteilelement Bezeichnung</th>
                    <th>Menge</th>
                    <th>Gleichzeitigkeit</th>
                </tr></thead>
                <tbody>";

            while ($row = $result->fetch_assoc()) {
                $glz = $row["Gleichzeitigkeit"] ?? '—';
                echo "<tr>";
                echo "<td>" . h($row["Raumnr"]) . "</td>";
                echo "<td>" . h($row["Raumbezeichnung"]) . "</td>";
                echo "<td>" . h($row["Raumbereich Nutzer"]) . "</td>";
                echo "<td>" . h($row["Geschoss"]) . "</td>";
                echo "<td>" . h($row["ElementID"]) . h(varianteToLetter($row["tabelle_Varianten_idtabelle_Varianten"])) . "</td>";
                echo "<td>" . h($row["ElementBezeichnung"]) . "</td>";
                echo "<td>" . h($row["Anzahl"]) . "</td>";
                echo "<td class='text-center'>" . h($glz) . "</td>";
                echo "</tr>";
            }
            echo "</tbody></table>";
            $mysqli->close();
            ?>
        </div>
    </div>
</div>

<script src="utils/_utils.js"></script>
<script>
    $(document).ready(function () {
        new DataTable('#tableGLZ', {
            dom: "<'dt-buttons'B><'dt-search'f><'dt-info'i>rt",
            order: [[0, "asc"]],
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json",
                search: ""
            },
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Excel',
                    className: 'fas fa-file-excel btn btn-sm btn-outline-success bg-white',
                    exportOptions: {
                        format: {
                            body: function (data, row, column) {
                                if (column === 7) { // Gleichzeitigkeit-Spalte
                                    // Komma durch Punkt ersetzen, damit Excel eine Zahl erkennt
                                    return data ? data.toString().replace(',', '.') : data;
                                }
                                return data;
                            }
                        }
                    }
                },
                {
                    extend: 'searchBuilder',
                    className: 'btn btn-sm bg-white btn-outline-dark'
                }
            ],
            paging: false,
            initComplete: function () {
                $('#tableGLZ_wrapper .dt-buttons').appendTo('#dt-header-container');
                $('#tableGLZ_wrapper .dt-search').appendTo('#dt-header-container');
                $('#tableGLZ_wrapper .dt-info').appendTo('#dt-header-container');
            }
        });
    });
</script>
</body>
</html>