<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>RB-Projekte</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="stylesheet" href="style.css" type="text/css" media="screen"/>
    <link rel="icon" href="iphone_favicon.png"/>
    <!-- 13.2.25: Reworked -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css"
          integrity="sha512-q3eWabyZPc1XTCmF+8/LuE1ozpg5xxn7iO89yfSOd5/oKvyqLngoNGsx8jq92Y8eXJ/IRxQbEC+FGSYxtk2oiw=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.1/css/dataTables.dataTables.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.2.1/af-2.7.0/b-3.2.1/b-colvis-3.2.1/b-html5-3.2.1/b-print-3.2.1/cr-2.0.4/date-1.5.5/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.1/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-3.0.0/sr-1.4.1/datatables.min.css"
          rel="stylesheet">
    <script src="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.2.1/af-2.7.0/b-3.2.1/b-colvis-3.2.1/b-html5-3.2.1/b-print-3.2.1/cr-2.0.4/date-1.5.5/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.1/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-3.0.0/sr-1.4.1/datatables.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>

</head>
<body>
<div id="limet-navbar"></div>

<div class="container-fluid bg-white">


    <?php
    if (!function_exists('utils_connect_sql')) {
        include "_utils.php";
    }
    init_page_serversides("No Redirect");
    include 'projects_changeProjectModal.html';
    ?>


    <div class='row'>
        <div class='col-xxl-10'>
            <div class="card mt-2">
                <div class="card-header" id="PRCardHeader">
                    <div class="row">
                        <div class="col-xxl-6"><b>Projekte</b></div>
                        <div class="col-xxl-6 d-inline-flex justify-content-end text-nowrap align-items-center"
                             id="STH">
                            <div class="form-check form-check-inline align-items-center float-end">
                                <input class="form-check-input" type="checkbox" id="filter_ActiveProjects" checked>
                                <label class="form-check-label" for="filter_ActiveProjects">
                                    Nur aktive Projekte
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body table-responsive px-1 py-1">

                    <table id='tableProjects'
                           class='table table-sm compact table-hover table-striped border border-light border-5'>
                        <thead>
                        <tr>
                            <th>ID</th><!-- invis -->
                            <th></th>
                            <th>Interne_Nr</th>
                            <th>Projektname</th>
                            <th>Aktiv</th>
                            <th>Neubau</th>
                            <th>Bettenanzahl</th>
                            <th>BGF</th>
                            <th>NF</th>
                            <th>Bearbeitung</th>
                            <th>Planungsphase</th>
                            <th>PlanungsphasenID</th> <!-- invis -->
                            <th>Preisbasis</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $mysqli = utils_connect_sql();
                        $sql = "SELECT tabelle_projekte.idTABELLE_Projekte, tabelle_projekte.Interne_Nr, tabelle_projekte.Projektname,"
                            . " tabelle_projekte.Aktiv, tabelle_projekte.Neubau, tabelle_projekte.Bettenanzahl,"
                            . " tabelle_projekte.BGF, tabelle_projekte.NF, tabelle_projekte.Ausfuehrung,tabelle_projekte.Preisbasis,"
                            . " tabelle_planungsphasen.Bezeichnung, tabelle_planungsphasen.idTABELLE_Planungsphasen"
                            . " FROM tabelle_projekte INNER JOIN tabelle_planungsphasen ON tabelle_projekte.TABELLE_Planungsphasen_idTABELLE_Planungsphasen = tabelle_planungsphasen.idTABELLE_Planungsphasen INNER JOIN tabelle_users_have_projects ON tabelle_projekte.idTABELLE_Projekte = tabelle_users_have_projects.tabelle_projekte_idTABELLE_Projekte WHERE tabelle_users_have_projects.User = '" . $_SESSION['username'] . "' ORDER BY tabelle_projekte.Interne_Nr;";
                        $result = $mysqli->query($sql);
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row["idTABELLE_Projekte"] . "</td>";
                            echo "<td> <button type='button' id='" . $row["idTABELLE_Projekte"] . "' class='btn btn-outline-dark btn-sm' value='changeProject' data-bs-toggle='modal' data-bs-target='#changeProjectModal'><i class='fas fa-pencil-alt'></i></button></td>";
                            echo "<td>" . $row["Interne_Nr"] . "</td>";
                            if ($row["Projektname"] == "BBE") {
                                echo "<td><b> <i class='fas fa-pray me-2'></i>" . $row["Projektname"] . " </b></td>";
                            } else if ($row["Projektname"] == "GCP") {
                                echo "<td><b> <i class='fas fa-toilet-paper me-2'></i>" . $row["Projektname"] . " </b></td>";
                            } else if ($row["Projektname"] == "Test") {
                                echo "<td><b> <i class='fas fa-bug me-2'></i>" . $row["Projektname"] . " </b></td>";
                            } else if ($row["Projektname"] == "VS Bertha von Suttner Zahnambulatorium") {
                                echo "<td><b> <i class='fas fa-tooth me-2'></i>" . $row["Projektname"] . " </b></td>";
                            } else if ($row["Projektname"] == "Cino 2.1") {
                                echo "<td><b> <i class='fas fa-smoking me-2'></i>" . $row["Projektname"] . " </b></td>";
                            }  else {
                                echo "<td><b>" . $row["Projektname"] . "</b></td>";
                            }

                            echo "<td>";
                            if ($row["Aktiv"] == 1) {
                                echo "Ja";
                            } else {
                                echo "Nein";
                            }
                            echo "</td>";
                            echo "<td>";
                            if ($row["Neubau"] == 1) {
                                echo "Ja";
                            } else {
                                echo "Nein";
                            }
                            echo "</td>";
                            echo "<td>" . $row["Bettenanzahl"] . "</td>";
                            echo "<td>" . $row["BGF"] . "</td>";
                            echo "<td>" . $row["NF"] . "</td>";
                            echo "<td>" . $row["Ausfuehrung"] . "</td>";
                            echo "<td>" . $row["Bezeichnung"] . "</td>";
                            echo "<td>" . $row["idTABELLE_Planungsphasen"] . "</td>";
                            echo "<td>" . $row["Preisbasis"] . "</td>";
                            echo "</tr>";
                        } ?>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>

        <div class='col-xxl-2'>
            <div class='card mt-2'>
                <div class='card-header'>Quick-Check
                </div>
                <div class='card-body' id='quickCheckDashboard'>
                </div>
            </div>
            <div class='card mt-2'>
                <div class='card-header'> Updates
                </div>
                <div class='card-body'>
                    Falls Mensch ebenso ein Projekt Icon setzen will,
                    <a href="https://fontawesome.com/v5/search?q=%20&o=r&ic=free" target="_blank">hier</a>
                    auswählen.
                </div>
            </div>
        </div>

    </div>
    <div class='mt-2 row'>
        <div class='col-xxl-10'>
            <div class='card'>
                <div class='card-header d-inline-flex' id='vermerkPanelHead'>
                    <div class='col-10'>
                        <form class='form-check form-check-inline'>
                            <label class='form-check-label' for='vermerkeFilter'>Vermerke im Projekt</label>
                            <select class='form-check-inline' id='vermerkeFilter'
                                <?php if ($_SESSION["projectName"] == "") {
                                    echo " style='display:none'";
                                } ?>
                            >
                                <option value=0 selected>Alle Vermerke</option>
                                <option value=1>Bearbeitung offen</option>

                            </select>
                        </form>
                    </div>
                    <div class='col-2'>
                        <div id='newSearchLocation' class='d-flex justify-content-end'></div>
                    </div>
                </div>
                <div class='card-body px-1 py-1' id='vermerke'>
                    <div class='row' id='projectVermerke'></div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>

<!--suppress ES6ConvertVarToLetConst -->
<script charset="utf-8">
    let searchCounter = 1;
    var table;
    $(document).ready(function () {
        table = $('#tableProjects').DataTable({
            columnDefs: [
                {
                    targets: [0, 11],
                    visible: false,
                    searchable: false
                },
                {
                    targets: [1],
                    visible: true,
                    searchable: false,
                    orderable: false
                }
            ],
            hover: true,
            dom: 'ft',
            select: true,
            paging: false,
            searching: true,
            info: false,
            order: [[2, "asc"]],
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json",
                search: ""
            },
            mark: true,
            search: {
                custom: function (data) {
                    if ($("#filter_ActiveProjects").is(':checked')) {
                        return data[4] === "Ja";
                    } else {
                        return true;
                    }
                }
            },
            initComplete: function () {
                $('.dt-search label').remove();
                $('.dt-search').children().removeClass("form-control form-control-sm").addClass("btn btn-sm btn-outline-dark").appendTo('#STH');
                table.column(4).search($('#filter_ActiveProjects').is(':checked') ? 'Ja' : '').draw();
            }
        });

        $('#filter_ActiveProjects').change(function () {
            table.column(4).search($(this).is(':checked') ? 'Ja' : '').draw();
        });

        $('#tableProjects tbody').on('click', 'tr', function () {
            let id = table.row($(this)).data()[0];
            let projectName = $(table.row($(this)).data()[3]).text();
            let projectAusfuehrung = table.row($(this)).data()[9];
            let projectPlanungsphase = table.row($(this)).data()[10];
            document.getElementById("betten").value = table.row($(this)).data()[6];
            document.getElementById("bgf").value = table.row($(this)).data()[7];
            document.getElementById("nf").value = table.row($(this)).data()[8];
            document.getElementById("bearbeitung").value = table.row($(this)).data()[9];
            document.getElementById("planungsphase").value = table.row($(this)).data()[11];
            document.getElementById("dateSelect").value = table.row($(this)).data()[12]
            document.getElementById("vermerkeFilter").value = 0;
            document.getElementById("active").value = table.row($(this)).data()[4] === "Ja" ? 1 : 0;
            document.getElementById("neubau").value = table.row($(this)).data()[5] === 'Ja' ? 1 : 0;

            $.ajax({
                url: "setSessionVariables.php",
                data: {
                    "projectID": id,
                    "projectName": projectName,
                    "projectAusfuehrung": projectAusfuehrung,
                    "projectPlanungsphase": projectPlanungsphase
                },
                type: "GET",
                success: function () {
                    $("#projectSelected").text(projectName);
                    $.ajax({
                        url: "getPersonsOfProject.php",
                        type: "GET",
                        success: function (data) {
                            $("#personsInProject").html(data);

                            $.ajax({
                                url: "getPersonsNotInProject.php",
                                type: "GET",
                                success: function (data) {
                                    $("#personsNotInProject").html(data);

                                    $.ajax({
                                        url: "getPersonToProjectField.php",
                                        type: "GET",
                                        success: function (data) {
                                            $("#addPersonToProject").html(data);

                                            $.ajax({
                                                url: "getProjectVermerke.php",
                                                type: "GET",
                                                success: function (data) {
                                                    $("#projectVermerke").html(data);
                                                    $("#vermerkeFilter").show();
                                                    $.ajax({
                                                        url: "getProjectCheck.php",
                                                        type: "GET",
                                                        success: function (data) {
                                                            $("#quickCheckDashboard").html(data);

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
            })
        });

        // Event listener to the two range filtering inputs to redraw on input
        $('#filter_ActiveProjects').change(function () {
            table.draw();
        });

        $("#saveProject").click(function () {
            let date = new Date($("#dateSelect").val());
            let year = date.getFullYear();
            let PBdate = year + '-' + (date.getMonth() + 1) + '-' + date.getDate();
            let betten = $("#betten").val();
            let bgf = $("#bgf").val();
            let nf = $("#nf").val();
            let bearbeitung = $("#bearbeitung").val();
            let planungsphase = $("#planungsphase").val();
            let active = $("#active").val();
            let neubau = $("#neubau").val();
            if (active !== "" && neubau !== "" && bearbeitung !== "" && planungsphase !== "" && !isNaN(betten) && !isNaN(bgf) && !isNaN(nf)) {
                $('#changeProjectModal').modal('hide');
                if (isNaN(year)) {
                    PBdate = "0000-00-00";
                }
                $.ajax({
                    url: "saveProject.php",
                    data: {
                        "active": active,
                        "neubau": neubau,
                        "bearbeitung": bearbeitung,
                        "planungsphase": planungsphase,
                        "betten": betten,
                        "bgf": bgf,
                        "nf": nf,
                        "PBdate": PBdate
                    },
                    type: "GET",
                    success: function (data) {
                        alert(data);
                        location.reload();
                    }
                });
            } else {
                alert("Bitte alle Felder korrekt ausfüllen!");
            }
        });

        $('#vermerkeFilter').change(function () {
            let filterValue = this.value;
            $.ajax({
                url: "getProjectVermerke.php",
                data: {"filterValue": filterValue},
                type: "GET",
                success: function (data) {
                    $("#projectVermerke").html(data);
                }
            });
        });

    }); // Document ready
</script>
</html> 
