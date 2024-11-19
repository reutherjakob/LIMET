<?php
include '_utils.php';
init_page_serversides("No Redirect");
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
    <head>
        <title>RB-Projekte</title>
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
        <meta name="viewport" content="width=device-width, initial-scale=1"/>
        <link rel="stylesheet" href="style.css" type="text/css" media="screen" />
        <link rel="icon" href="iphone_favicon.png"/>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css"/>
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css" integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous"/>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
        <link href="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.0.5/af-2.7.0/b-3.0.2/b-colvis-3.0.2/b-html5-3.0.2/b-print-3.0.2/cr-2.0.1/date-1.5.2/fc-5.0.0/fh-4.0.1/kt-2.12.0/r-3.0.2/rg-1.5.0/rr-1.5.0/sc-2.4.1/sb-1.7.1/sp-2.3.1/sl-2.0.1/sr-1.4.1/datatables.min.css" rel="stylesheet"/>
        <script src="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.0.5/af-2.7.0/b-3.0.2/b-colvis-3.0.2/b-html5-3.0.2/b-print-3.0.2/cr-2.0.1/date-1.5.2/fc-5.0.0/fh-4.0.1/kt-2.12.0/r-3.0.2/rg-1.5.0/rr-1.5.0/sc-2.4.1/sb-1.7.1/sp-2.3.1/sl-2.0.1/sr-1.4.1/datatables.min.js"></script>

        <style>
            .dt-input{
                float:right;
            }
        </style>
    </head>
    <body>
        <div class="container-fluid">
            <div id="limet-navbar"></div>
            <div class='mt-1 row'>
                <div class='col-md-10'>
                    <div class="mt-1 card">
                        <div class="card-header  d-inline-flex" id="PRCardHeader" > <b>Projekte </b>
                            <div class="col " id ="STH"> </div>
                            <label class="float-right">
                                Nur aktive Projekte: <input type="checkbox" id="filter_ActiveProjects">
                            </label>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <?php
                                $mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');
                                // Abfrage aller RÃ¤ume im Projekt
                                //$sql="SELECT view_Projekte.idTABELLE_Projekte, view_Projekte.Interne_Nr, view_Projekte.Projektname, view_Projekte.Aktiv, view_Projekte.Neubau, view_Projekte.Bettenanzahl, view_Projekte.BGF, view_Projekte.NF, view_Projekte.Ausfuehrung, tabelle_planungsphasen.Bezeichnung, tabelle_planungsphasen.idTABELLE_Planungsphasen FROM view_Projekte INNER JOIN tabelle_planungsphasen ON view_Projekte.TABELLE_Planungsphasen_idTABELLE_Planungsphasen = tabelle_planungsphasen.idTABELLE_Planungsphasen ORDER BY view_Projekte.Interne_Nr";						
                                $sql = "SELECT tabelle_projekte.idTABELLE_Projekte, tabelle_projekte.Interne_Nr, tabelle_projekte.Projektname,"
                                        . " tabelle_projekte.Aktiv, tabelle_projekte.Neubau, tabelle_projekte.Bettenanzahl,"
                                        . " tabelle_projekte.BGF, tabelle_projekte.NF, tabelle_projekte.Ausfuehrung,tabelle_projekte.Preisbasis,"
                                        . " tabelle_planungsphasen.Bezeichnung, tabelle_planungsphasen.idTABELLE_Planungsphasen"
                                        . " FROM tabelle_projekte INNER JOIN tabelle_planungsphasen ON tabelle_projekte.TABELLE_Planungsphasen_idTABELLE_Planungsphasen = tabelle_planungsphasen.idTABELLE_Planungsphasen INNER JOIN tabelle_users_have_projects ON tabelle_projekte.idTABELLE_Projekte = tabelle_users_have_projects.tabelle_projekte_idTABELLE_Projekte WHERE tabelle_users_have_projects.User = '" . $_SESSION['username'] . "' ORDER BY tabelle_projekte.Interne_Nr;";
                                $result = $mysqli->query($sql);

                                echo "<table id='tableProjects' class='table display compact table-striped table-bordered table-sm'>
                                                    <thead><tr>
                                                        <th>ID</th>
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
                                                        <th>PlanungsphasenID</th>
                                                        <th>Preisbasis</th>
                                                    </tr></thead>
                                                    <tbody>";

                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . $row["idTABELLE_Projekte"] . "</td>";
                                    echo "<td><button type='button' id='" . $row["idTABELLE_Projekte"] . "' class='btn btn-outline-dark btn-xs' value='changeProject' data-toggle='modal' data-target='#changeProjectModal'><i class='fas fa-pencil-alt'></i></button></td>";
                                    echo "<td>" . $row["Interne_Nr"] . "</td>";
                                    echo "<td>" . $row["Projektname"] . "</td>";
                                    echo "<td>";
                                    if ($row["Aktiv"] == 1) {
                                        echo "Ja";
                                    } else {
                                        echo "Nein";
                                    }
                                    echo"</td>";
                                    echo "<td>";
                                    if ($row["Neubau"] == 1) {
                                        echo "Ja";
                                    } else {
                                        echo "Nein";
                                    }
                                    echo"</td>";
                                    echo "<td>" . $row["Bettenanzahl"] . "</td>";
                                    echo "<td>" . $row["BGF"] . "</td>";
                                    echo "<td>" . $row["NF"] . "</td>";
                                    echo "<td>" . $row["Ausfuehrung"] . "</td>";
                                    echo "<td>" . $row["Bezeichnung"] . "</td>";
                                    echo "<td>" . $row["idTABELLE_Planungsphasen"] . "</td>";
                                    echo "<td>" . $row["Preisbasis"] . "</td>";
                                    echo "</tr>";
                                }
                                echo "</tbody></table>";
                                ?>	
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                if ($_SESSION["ext"] == 0) {

                    // Projektprüfungs-Dashboard anzeigen------------------------------
                    echo "
                        <div class='col-md-2'>
                            <div class='mt-1 card'>
                                    <div class='card-header'>Quick-Check                                
                                    </div>
                                    <div class='card-body' id='quickCheckDashboard'>       
                                    
                                    </div>
                            </div>
                        </div> </div> ";
                }

                if ($_SESSION["ext"] == 0) {

                    echo "              <div class='mt-4 row'>    
                                            <div class='col-md-12'>
                                                <div class='card'>
                                                        <div class='card-header d-inline-flex' id='vermerkPanelHead'>
                                                            <div class='col-10'>
                                                                <form class='form-inline'>
                                                                <label class='m-1 comapct' for='vermerkeFilter'>Vermerke im Projekt</label>
                                                               <select class='form-control form-control-sm dt-input' id='vermerkeFilter'";
                                                                if ($_SESSION["projectName"] == "") {
                                                                    echo " style='display:none'";
                                                                }
                                                                echo ">
                                                                    <option value=0 selected>Alle Vermerke</option>   
                                                                    <option value=1>Bearbeitung offen</option>  
                                                                    <!--<option value=2>Eigene Vermerke</option>  -->
                                                                </select>
                                                            </div>
                                                            <div class='col-2'> <div id='newSearchLocation'  class='d-flex justify-content-end'>    </div>	
                                                            </div> 
                                                        

                                                                                                 
                                                             
                                                               
                                                            </form>   
                                                        </div> 
                                                        <div class='card-body'  id='vermerke'>
                                                            <div class='row' id='projectVermerke'></div>
                                                        </div>
                                                </div>
                                            </div>
                                        </div>";

                    // Vergabesumme zu Projekt darstellen------------------------------
                    /*
                      echo "
                      <div class='mt-4 row'>
                      <div class='col-md-12'>
                      <div class='card'>
                      <div class='card-header'>
                      <label class='m-1' for='vergabeKostenPrognose'>Vergabekosten/Vergabekostenprognose</label>
                      </div>
                      <div class='card-body'  id='vergabeKostenPrognose'>
                      <div class='col-md-3'><canvas id='chartCanvas' width='auto' height='auto'></canvas></div>
                      </div>
                      </div>
                      </div>
                      </div>";
                     * 
                     */
                }
                ?>



                <!-- Modal zum Ändern des Projekts -->
                <div class='modal fade' id='changeProjectModal' role='dialog'>
                    <div class='modal-dialog modal-md'>

                        <!-- Modal content-->
                        <div class='modal-content'>
                            <div class='modal-header'>            
                                <h4 class='modal-title'>Projekt Ãndern</h4>
                                <button type='button' class='close' data-dismiss='modal'>&times;</button>
                            </div>
                            <div class='modal-body' id='mbody'>
                                <form role="form">      
                                    <div class="form-group">
                                        <label for="active">Aktiv:</label>
                                        <select class='form-control form-control-sm' id='active' name='active'>  
                                            <option value="1">Ja</option> 
                                            <option value="0">Nein</option>                                    	
                                        </select>	
                                    </div>
                                    <div class="form-group">
                                        <label for="neubau">Neubau:</label>
                                        <select class='form-control form-control-sm' id='neubau' name='neubau'>  
                                            <option value="1">Ja</option> 
                                            <option value="0">Nein</option>                                    	
                                        </select>	
                                    </div>
                                    <div class="form-group">
                                        <label for="betten">Bettenanzahl:</label>
                                        <input type="text" class="form-control form-control-sm" id="betten" name="betten" />
                                    </div>
                                    <div class="form-group">
                                        <label for="bgf">BGF:</label>
                                        <input type="text" class="form-control form-control-sm" id="bgf" name="bgf" />
                                    </div>
                                    <div class="form-group">
                                        <label for="nf">NF:</label>
                                        <input type="text" class="form-control form-control-sm" id="nf" name="nf" />
                                    </div>
                                    <div class="form-group">
                                        <label for="bearbeitung">Bearbeitung:</label>
                                        <select class='form-control form-control-sm' id='bearbeitung' name='bearbeitung'>  
                                            <option value="LIMET">LIMET</option> 
                                            <option value="MADER">MADER</option>    
                                            <option value="LIMET-MADER">LIMET-MADER</option>      
                                        </select>	
                                    </div>
                                    <div class="form-group">
                                        <label for="planungsphase">Planungsphase:</label>
                                        <select class='form-control form-control-sm' id='planungsphase' name='planungsphase'>  
                                            <option value="1">Vorentwurf</option> 
                                            <option value="2">Entwurf</option>    
                                            <option value="4">Einreichung</option>     
                                            <option value="3">AusfÃ¼hrungsplanung</option> 
                                        </select>	
                                    </div>
                                    <div class="form-group"> 
                                        <label for="dateSelect">Preisbasis:</label>
                                        <input type="date" id="dateSelect" name="dateSelect">
                                    </div>

                                </form>
                            </div>
                            <div class='modal-footer'>
                                <input type='button' id='saveProject' class='btn btn-warning btn-sm' value='Speichern'>
                                <button type='button' class='btn btn-default btn-sm' data-dismiss='modal'>Abbrechen</button>
                            </div>
                        </div>

                    </div>
                </div>    

            </div>
    </body>
    <script>
        const ext = "<?php echo $_SESSION["ext"] ?>";
        //var table;

        $.fn.dataTable.ext.search.push(
                function (settings, data) {
                    /*var min = parseInt( $('#min').val(), 10 );
                     var max = parseInt( $('#max').val(), 10 );
                     var age = parseFloat( data[6] ) || 0; // use data for the age column
                     
                     if ( ( isNaN( min ) && isNaN( max ) ) ||
                     ( isNaN( min ) && age <= max ) ||
                     ( min <= age   && isNaN( max ) ) ||
                     ( min <= age   && age <= max ) )
                     */
                    if (settings.nTable.id !== 'tableProjects') {
                        return true;
                    }

                    if ($("#filter_ActiveProjects").is(':checked')) {
                        return data [4] === "Ja";
                    } else {
                        return true;
                    }
                }
        );

        function move_dt_search(inp, location) {
            const move = $(inp);
            $(location).prepend(move);
        }

        let searchCounter = 1;
        function replace_dt_searcher(location) { //#newSearchLocation
            let oldSearch = `#dt-search-${searchCounter}`;
            let newSearch = `#dt-search-${searchCounter + 1}`; 
            $(oldSearch).remove();
            const move = $(newSearch);
            $(location).prepend(move);
            searchCounter++;
        }

        // Tabelle formatieren
        $(document).ready(function () {
            let doooooooom = 'ft';
            if (ext === '0') {
                $('#tableProjects').DataTable({
                    "columnDefs": [
                        {
                            "targets": [0, 11],
                            "visible": false,
                            "searchable": false
                        },
                        {
                            "targets": [1],
                            "visible": true,
                            "searchable": false,
                            "sortable": false
                        }
                    ],
                    dom: doooooooom,
                    "select": true,
                    "paging": false,
                    "searching": true,

                    "info": false,
                    "order": [[2, "asc"]],
                    "pagingType": "simple_numbers",
                    "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                    "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json", "search": ""},
                    "mark": true
                });
            } else {
                $('#tableProjects').DataTable({
                    "columnDefs": [
                        {
                            "targets": [0, 1, 5, 6, 7, 8, 11],
                            "visible": false,
                            "searchable": false
                        }
                    ],
                    dom: doooooooom,
                    "select": true,
                    "paging": false,
                    "searching": true,
                    "info": false,
                    "order": [[2, "asc"]],
                    "pagingType": "simple_numbers",
                    "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                    "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json", "search": ""}
                });
            }

            var table = $("#tableProjects").DataTable();
            $('#tableProjects tbody').on('click', 'tr', function () {
                if ($(this).hasClass('info')) {

                } else {
                    table.$('tr.info').removeClass('info');
                    $(this).addClass('info');
                    var id = table.row($(this)).data()[0];
                    var projectName = table.row($(this)).data()[3];
                    var projectAusfuehrung = table.row($(this)).data()[9];
                    var projectPlanungsphase = table.row($(this)).data()[10];

                    document.getElementById("betten").value = table.row($(this)).data()[6];
                    document.getElementById("bgf").value = table.row($(this)).data()[7];
                    document.getElementById("nf").value = table.row($(this)).data()[8];
                    document.getElementById("bearbeitung").value = table.row($(this)).data()[9];
                    document.getElementById("planungsphase").value = table.row($(this)).data()[11];
                    document.getElementById("dateSelect").value = table.row($(this)).data()[12];

                    if (ext === '0') {
                        document.getElementById("vermerkeFilter").value = 0;
                    }

                    if (table.row($(this)).data()[4] === "Ja") {
                        document.getElementById("active").value = 1;
                    } else {
                        document.getElementById("active").value = 0;
                    }
                    if (table.row($(this)).data()[5] === 'Ja') {
                        document.getElementById("neubau").value = 1;
                    } else {
                        document.getElementById("neubau").value = 0;
                    }

                    $.ajax({
                        url: "setSessionVariables.php",
                        data: {"projectID": id, "projectName": projectName, "projectAusfuehrung": projectAusfuehrung, "projectPlanungsphase": projectPlanungsphase},
                        type: "GET",
                        success: function () {
                            $("#projectSelected").text("Aktuelles Projekt: " + projectName);
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
                                                            setTimeout(function () {

                                                                replace_dt_searcher('#newSearchLocation');
                                                            }, 200); //#newSearchLocation
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
                    });
                }
            });

            // Event listener to the two range filtering inputs to redraw on input
            $('#filter_ActiveProjects').change(function () {
                table.draw();
            });

            // Wenn Seite geladen, dann Inhalte dazu laden
            $.ajax({
                url: "getProjectVermerke.php",
                type: "GET",
                success: function (data) {
                    $("#projectVermerke").html(data);
                }
            });

            // Wenn Seite geladen, dann Project Quick-Check laden
            $.ajax({
                url: "getProjectCheck.php",
                type: "GET",
                success: function (data) {
                    $("#quickCheckDashboard").html(data);
                }
            });

            setTimeout(function () {
                move_dt_search('#dt-search-0', '#STH');
                move_dt_search('#dt-search-1', '#newSearchLocation');

            }, 200);
        });
        function getDate() {
            var date = new Date($("#dateSelect").val());//                                    console.log("Date: ", date);
            var day = date.getDate();//                                    console.log("Day: ", day);
            var month = date.getMonth() + 1; // Months are zero based//                                    console.log("Month: ", month);
            var year = date.getFullYear();//                                    console.log("Year: ", year);
            day = ('0' + day).slice(-2);//                                    console.log(" Formatted Day: ", day);
            month = ('0' + month).slice(-2);//                                    console.log(" Formatted Month: ", month);
            var formattedDate = day + '-' + month + '-' + year;
            console.log("Formatted Date: ", formattedDate);
            return formattedDate;
        }

        // ProjektÃ¤nderungen aus Modal speichern
        $("#saveProject").click(function () {
            var date = new Date($("#dateSelect").val());
            var year = date.getFullYear();
            var PBdate = year + '-' + (date.getMonth() + 1) + '-' + date.getDate(); //Preisbasis
            console.log(PBdate);
            var betten = $("#betten").val();
            var bgf = $("#bgf").val();
            var nf = $("#nf").val();
            var bearbeitung = $("#bearbeitung").val();
            var planungsphase = $("#planungsphase").val();
            var active = $("#active").val();
            var neubau = $("#neubau").val();
            if (active !== "" && neubau !== "" && bearbeitung !== "" && planungsphase !== "" && !isNaN(betten) && !isNaN(bgf) && !isNaN(nf)) {
                $('#changeProjectModal').modal('hide');
                if (isNaN(year)) {
                    PBdate = "0000-00-00";
                }
                $.ajax({
                    url: "saveProject.php",
                    data: {"active": active, "neubau": neubau, "bearbeitung": bearbeitung, "planungsphase": planungsphase, "betten": betten, "bgf": bgf, "nf": nf, "PBdate": PBdate},
                    type: "GET",
                    success: function (data) {
                        alert(data);
                        location.reload();
                    }
                });
            } else {
                alert("Bitte alle Felder korrekt ausfÃ¼llen!");
            }
        });
        // Filter-Änderung
        $('#vermerkeFilter').change(function () {
            var filterValue = this.value;
            $.ajax({
                url: "getProjectVermerke.php",
                data: {"filterValue": filterValue},
                type: "GET",
                success: function (data) {
                    $("#projectVermerke").html(data);
                }
            });
        });



        //document.getElementById("checkGewerke").innerHTML = "<span class='badge badge-success'>Gewerke zugeteilt</span>";

    </script>
</html> 
