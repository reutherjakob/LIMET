<?php
require_once 'utils/_utils.php';
init_page_serversides("");
?>


<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="">
<head>
    <title>ÖBA - Dashboard</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen"/>
    <link rel="icon" href="Logo/iphone_favicon.png">
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
<body  >
<div id="limet-navbar"></div> <!-- Container für Navbar -->
<div class="container-fluid">
    <div class='row mt-4 mb-4'>
        <div class='col-xxl-3'>
            <div class="card border-info">
                <div class="card-body">
                    <h4 class="card-subtitle text-muted">Vorleistungen kontrolliert</h4>
                    <?php
                    $mysqli = utils_connect_sql();

                    $sqlVorleistungen = "SELECT Count(tabelle_lose_extern.Vorleistungspruefung) AS AnzahlvonVorleistungspruefung, tabelle_lose_extern.Vorleistungspruefung
                                                FROM tabelle_lose_extern
                                                WHERE (((tabelle_lose_extern.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . "))
                                                GROUP BY tabelle_lose_extern.Vorleistungspruefung;";

                    $vorleistungGeprfueft = 0;
                    $vorleistungUngeprueft = 0;
                    $result = $mysqli->query($sqlVorleistungen);
                    while ($row = $result->fetch_assoc()) {
                        if ($row["Vorleistungspruefung"] == 0) {
                            $vorleistungUngeprueft = $row["AnzahlvonVorleistungspruefung"];
                        } else {
                            if ($row["Vorleistungspruefung"] == 1) {
                                $vorleistungGeprfueft = $row["AnzahlvonVorleistungspruefung"];
                            }
                        }
                    }
                    ?>
                    <h1 class="card-title text-info mt-2"><?php if (($vorleistungGeprfueft + $vorleistungUngeprueft) > 0) {
                            echo round($vorleistungGeprfueft / ($vorleistungGeprfueft + $vorleistungUngeprueft) * 100, 2);
                        } else {
                            echo "(Summe Vorleistungen)=0 ";
                        } ?> %</h1>
                    <a href="roombookVorleistungen.php" class="card-link">Festlegen ></a>
                </div>
            </div>
        </div>
        <div class='col-xxl-3'>
            <div class="card border-danger">
                <div class="card-body">
                    <h4 class="card-subtitle text-muted">Liefertermine fixiert</h4>
                    <?php
                    $sql1 = "SET @lieferDatumGesetzt = 
                                    (
                                    SELECT Count(tabelle_räume_has_tabelle_elemente.Lieferdatum) AS AnzahlvonLieferdatum
                                    FROM tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume
                                    WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . ") AND ((tabelle_räume_has_tabelle_elemente.Anzahl)>0) AND ((tabelle_räume_has_tabelle_elemente.Standort)=1))
                                    HAVING (((Count(tabelle_räume_has_tabelle_elemente.Lieferdatum)) Is Not Null))
                                    )";

                    $sql2 = "SET @gesamtElemente = 
                                    (
                                    SELECT Count(*) AS AnzahlvonLieferdatum
                                    FROM tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume
                                    WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . ") AND ((tabelle_räume_has_tabelle_elemente.Anzahl)>0) AND ((tabelle_räume_has_tabelle_elemente.Standort)=1))
                                    )";

                    $sql3 = "SELECT FORMAT(@lieferDatumGesetzt/@gesamtElemente, 4) AS ergebnis;";

                    $result1 = $mysqli->query($sql1);
                    $result2 = $mysqli->query($sql2);
                    $result3 = $mysqli->query($sql3);
                    while ($row = $result3->fetch_assoc()) {
                        $lieferDatumProzent = $row["ergebnis"];
                    }
                    ?>
                    <h1 class="card-title text-danger mt-2"><?php echo $lieferDatumProzent * 100; ?> %</h1>
                    <a href="roombookAusfuehrungLiefertermine.php" class="card-link">Festlegen ></a>
                </div>
            </div>
        </div>
        <div class='col-xxl-3'>
            <div class="card border-success">
                <div class="card-body">
                    <h4 class="card-subtitle text-muted">Abgerechnet</h4>
                    <?php
                    $sql = "SELECT Sum(tabelle_lose_extern.Vergabesumme) AS SummevonVergabesumme, Sum(tabelle_rechnungen.Rechnungssumme) AS SummevonRechnungssumme
                                    FROM tabelle_rechnungen RIGHT JOIN tabelle_lose_extern ON tabelle_rechnungen.tabelle_lose_extern_idtabelle_Lose_Extern = tabelle_lose_extern.idtabelle_Lose_Extern
                                    WHERE (((tabelle_lose_extern.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . "));";

                    $result = $mysqli->query($sql);
                    while ($row = $result->fetch_assoc()) {
                        $vergabesumme = $row["SummevonVergabesumme"];
                        $abrechngungssumme = $row["SummevonRechnungssumme"];
                    }
                    ?>
                    <h1 class="card-title text-success mt-2"><?php if (($vergabesumme) > 0) {
                            echo round($abrechngungssumme / $vergabesumme * 100, 2);
                        } else {
                            echo "Vergabesumme=0";
                        } ?> %</h1>
                    <a href="roombookAbrechnung.php" class="card-link">Festlegen ></a>
                </div>
            </div>
        </div>
        <div class='col-xxl-3'>
            <div class="card border-warning">
                <div class="card-body">
                    <h4 class="card-subtitle text-muted">Schlussgerechnet</h4>
                    <?php
                    $sql = "SELECT Count(tabelle_lose_extern.Schlussgerechnet) AS AnzahlvonLosen, tabelle_lose_extern.Schlussgerechnet
                                    FROM tabelle_lose_extern
                                    WHERE (((tabelle_lose_extern.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . "))
                                    GROUP BY tabelle_lose_extern.Schlussgerechnet;";

                    $result = $mysqli->query($sql);
                    $finishedLots = 0;
                    $notFinishedLots = 0;
                    while ($row = $result->fetch_assoc()) {
                        if ($row["Schlussgerechnet"] === "0") {
                            $notFinishedLots = $row["AnzahlvonLosen"];
                        }
                        if ($row["Schlussgerechnet"] === "1") {
                            $finishedLots = $row["AnzahlvonLosen"];
                        }
                    }
                    ?>
                    <h1 class="card-title text-warning mt-2"><?php if (($finishedLots + $notFinishedLots) > 0) {
                            echo round($finishedLots / ($finishedLots + $notFinishedLots) * 100, 2);
                        } else {
                            echo "0";
                        } ?> %</h1>
                    <a href="roombookAbrechnung.php" class="card-link">Festlegen ></a>
                </div>
            </div>
        </div>
    </div>
    <hr>
    <div class='row'>
        <div class='col-xxl-6'>
            <div class="mt-4 card">
                <div class="card-header"><h4>ToDo's</h4></div>
                <div class="card-body">
                    <div class="row">
                        <div class='col-xxl-12'>
                            <?php
                            $sql = "SELECT tabelle_Vermerkgruppe.Gruppenname, tabelle_Vermerkgruppe.Gruppenart, tabelle_Vermerkgruppe.Ort, tabelle_Vermerkgruppe.Datum, tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung, tabelle_lose_extern.LosNr_Extern, tabelle_lose_extern.LosBezeichnung_Extern, tabelle_ansprechpersonen.Name, tabelle_ansprechpersonen.Vorname, tabelle_Vermerke.Faelligkeit, tabelle_Vermerke.Vermerkart, tabelle_Vermerke.Bearbeitungsstatus, tabelle_Vermerke.Vermerktext, tabelle_Vermerke.idtabelle_Vermerke
                                        FROM (((tabelle_Vermerke LEFT JOIN (tabelle_ansprechpersonen RIGHT JOIN tabelle_Vermerke_has_tabelle_ansprechpersonen ON tabelle_ansprechpersonen.idTABELLE_Ansprechpersonen = tabelle_Vermerke_has_tabelle_ansprechpersonen.tabelle_ansprechpersonen_idTABELLE_Ansprechpersonen) ON tabelle_Vermerke.idtabelle_Vermerke = tabelle_Vermerke_has_tabelle_ansprechpersonen.tabelle_Vermerke_idtabelle_Vermerke) INNER JOIN (tabelle_Vermerkgruppe INNER JOIN tabelle_Vermerkuntergruppe ON tabelle_Vermerkgruppe.idtabelle_Vermerkgruppe = tabelle_Vermerkuntergruppe.tabelle_Vermerkgruppe_idtabelle_Vermerkgruppe) ON tabelle_Vermerke.tabelle_Vermerkuntergruppe_idtabelle_Vermerkuntergruppe = tabelle_Vermerkuntergruppe.idtabelle_Vermerkuntergruppe) LEFT JOIN tabelle_räume ON tabelle_Vermerke.tabelle_räume_idTABELLE_Räume = tabelle_räume.idTABELLE_Räume) LEFT JOIN tabelle_lose_extern ON tabelle_Vermerke.tabelle_lose_extern_idtabelle_Lose_Extern = tabelle_lose_extern.idtabelle_Lose_Extern
                                        WHERE (((tabelle_Vermerke.Vermerkart)='Bearbeitung') AND ((tabelle_Vermerkgruppe.Gruppenart)='ÖBA-Protokoll') AND ((tabelle_Vermerkgruppe.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . "))
                                        ORDER BY tabelle_Vermerkgruppe.Datum DESC;";

                            $result = $mysqli->query($sql);

                            echo "<div class='table-responsive'><table class='table table-striped table-bordered table-sm table-hover border border-light border-5'' id='tableOEBAVermerke' > 
                                        <thead><tr>
                                        <th>ID</th> 
                                        <th>Protokoll</th>
                                        <th>Gewerk</th>
                                        <th>Status</th>
                                        <th>Wer</th>
                                        <th>Fälligkeit</th>
                                        <th>Vermerk</th>
                                        <th>Raum</th>                                        	        
                                        <th>Status</th>
                                        </tr></thead><tbody>";
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row["idtabelle_Vermerke"] . "</td>";
                                echo "<td>" . $row["Gruppenname"] . "</td>";
                                echo "<td>" . $row["LosNr_Extern"] . "</td>";
                                echo "<td>";
                                if ($row["Bearbeitungsstatus"] == "0") {
                                    if ($row["Faelligkeit"] < date("Y-m-d")) {
                                        echo "<span class='badge badge-pill badge-danger'> Überfällig </span>";
                                    } else {
                                        echo "<span class='badge badge-pill badge-warning'> Offen </span>";
                                    }
                                } else {
                                    echo "<span class='badge badge-pill badge-success'> Abgeschlossen </span>";
                                }
                                echo "</td>";
                                echo "<td>" . $row["Name"] . " " . $row["Vorname"] . "</td>";
                                echo "<td>";
                                if ($row["Vermerkart"] != "Info") {
                                    echo $row["Faelligkeit"];
                                }
                                echo "</td>";
                                echo "<td><button type='button' class='btn btn-sm btn-light' data-bs-toggle='popover' title='Vermerk' data-placement='right' data-bs-content='" . $row["Vermerktext"] . "'><i class='far fa-comment'></i></button></td>";
                                echo "<td>" . $row["Raumnr"] . " " . $row["Raumbezeichnung"] . "</td>";
                                echo "<td>" . $row["Bearbeitungsstatus"] . "</td>";
                                echo "</tr>";
                            }
                            echo "</tbody></table></div>";
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class='col-xxl-6'>
            <div class="mt-4 card">
                <div class="card-body">
                    <div class="row">
                        <div class='col-xxl-12'>
                            <h4 class="card-subtitle text-muted">Kommende Termine</h4>
                            <?php
                            $sql = "SELECT WEEK(tabelle_räume_has_tabelle_elemente.Lieferdatum) as lieferWeek, tabelle_räume.`Raumbereich Nutzer`, tabelle_räume.Geschoss, tabelle_lose_extern.LosNr_Extern, tabelle_lose_extern.LosBezeichnung_Extern, WEEK(CURDATE()) as currentWeek, tabelle_lieferant.Lieferant
                                        FROM tabelle_lieferant
                                        RIGHT JOIN tabelle_lose_extern
                                        RIGHT JOIN (tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) ON tabelle_lose_extern.idtabelle_Lose_Extern = tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern
                                        ON tabelle_lieferant.idTABELLE_Lieferant = tabelle_lose_extern.tabelle_lieferant_idTABELLE_Lieferant
                                        WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . ") AND ((tabelle_räume_has_tabelle_elemente.Standort)=1) AND WEEK(tabelle_räume_has_tabelle_elemente.Lieferdatum) >= WEEK(CURDATE()) AND WEEK(tabelle_räume_has_tabelle_elemente.Lieferdatum) <= WEEK(CURDATE())+4 )
                                        GROUP BY lieferWeek, LosNr_Extern
                                        ORDER BY lieferWeek asc;";

                            $result = $mysqli->query($sql);
                            $currentWeek = 0;
                            while ($row = $result->fetch_assoc()) {
                                if ($row["lieferWeek"] !== $currentWeek) {
                                    if ($currentWeek > 0) {
                                        echo "</div></div>";
                                    }
                                    echo "<div class='mt-4 card'>
                                            <div class='card-header bg-info rounded'>
                                            <h4><span class='badge badge-light'>KW " . $row["lieferWeek"] . "</span></h4>
                                          </div>
                                          <div class='card-body'>
                                          <h4>" . $row["LosNr_Extern"] . "-" . $row["LosBezeichnung_Extern"] . ": " . $row["Lieferant"] . "</span></h4>";
                                    $currentWeek = $row["lieferWeek"];
                                } else {
                                    echo "<h4>" . $row["LosNr_Extern"] . "-" . $row["LosBezeichnung_Extern"] . ": " . $row["Lieferant"] . "</span></h4>";
                                }
                            }
                            $mysqli->close();
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    // Tabellen formatieren
    $(document).ready(function () {
        new DataTable('#tableOEBAVermerke', {
            select: false,
            paging: false,
            pagingType: 'simple',
            lengthChange: false,
            pageLength: 20,
            columns: [
                {visible: false, searchable: false},
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                {visible: false, searchable: false}
            ],
            order: [[5, 'asc']],
            orderMulti: false,
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json', search: "", searchPlaceholder: "Suche..."
            },
            layout: {
                topStart: null,
                topEnd: null,
                bottomStart: null,
                bottomEnd: null
            }
        });
    });

    // Popover for Vermerk
    $(function () {
        $('[data-bs-toggle="popover"]').popover();
    });

</script>
</body>
</html>
