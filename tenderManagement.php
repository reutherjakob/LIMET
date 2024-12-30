<?php
    session_start();
    $_SESSION["dbAdmin"]="0";
    include '_utils.php';
    init_page_serversides()
?>

<!DOCTYPE html>
<html lang="de" xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Ausschreibungsverwaltung</title>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="style.css" type="text/css" media="screen" />
<link rel="icon" href="iphone_favicon.png">

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css" integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>


<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.18/b-1.5.2/b-html5-1.5.2/sl-1.2.6/datatables.min.css"/>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.18/b-1.5.2/b-html5-1.5.2/sl-1.2.6/datatables.min.js"></script>

<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/datatables.mark.js/2.0.0/datatables.mark.min.css"/>
<script type="text/javascript" src="https://cdn.datatables.net/plug-ins/1.10.13/features/mark.js/datatables.mark.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/mark.js/8.6.0/jquery.mark.min.js"></script>

<!--DATEPICKER -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/css/bootstrap-datepicker3.min.css">
<script type='text/javascript' src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/js/bootstrap-datepicker.min.js"></script>
</head>

<body style="height:100%">

<div class="container-fluid" >
    <div id="limet-navbar"></div>
    <div class='row mt-4 '>
        <div class='col-sm-12'>
            <div class="card">
                <div class="card-header"><h4>Neuer Eintrag</h4></div>
                <div class="card-body">
                    <form role='form'>
                        <div class='form-group row'>
                            <label class='control-label col-sm-2' for='select_los'>Gewerk: </label>
                                <?php
                                    $mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');

                                    /* change character set to utf8 */
                                    if (!$mysqli->set_charset("utf8")) {
                                        printf("Error loading character set utf8: %s\n", $mysqli->error);
                                        exit();
                                    }
                                    $sql = "SELECT tabelle_projekte.Interne_Nr, tabelle_projekte.Projektname, tabelle_lose_extern.LosNr_Extern, tabelle_lose_extern.LosBezeichnung_Extern, 
                                                tabelle_lose_extern.idtabelle_Lose_Extern
                                            FROM tabelle_lose_extern INNER JOIN tabelle_projekte ON tabelle_lose_extern.tabelle_projekte_idTABELLE_Projekte = tabelle_projekte.idTABELLE_Projekte
                                            WHERE ((Not (tabelle_projekte.idTABELLE_Projekte)=4))
                                            ORDER BY tabelle_projekte.Interne_Nr DESC , tabelle_lose_extern.LosNr_Extern;";

                                    $result = $mysqli->query($sql);
                                ?>
                            <div class='col-sm-4'>
                                <select class='form-control form-control-sm' id='select_los' name='select_los'>
                                <?php
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<option value=" . $row["idtabelle_Lose_Extern"] . ">" . $row["Interne_Nr"] . " " .$row["Projektname"]." - " . $row["LosNr_Extern"] . " ".$row["LosBezeichnung_Extern"]."</option>";
                                    }
                                ?>
                                </select>
                            </div>
                        </div>
                        <div class='form-group row'>
                            <label class='control-label col-sm-2' for='select_element'>Element: </label>
                            <?php
                                $sql = "SELECT tabelle_elemente.idTABELLE_Elemente, tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung
                                        FROM tabelle_elemente
                                        ORDER BY tabelle_elemente.ElementID;";
                                $result = $mysqli->query($sql);
                            ?>
                            <div class='col-sm-4'>
                                <select class='form-control form-control-sm' id='select_element' name='select_element'>
                                    <?php
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<option value=" . $row["idTABELLE_Elemente"] . ">" . $row["ElementID"] . " " .$row["Bezeichnung"]."</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class='form-group row'>
                            <label class='control-label col-sm-2' for='datum'>Datum:</label>
                            <div class='col-sm-4'>
                                <input type='text' class='form-control form-control-sm' id='datum' placeholder='jjjj-mm-tt'/>
                            </div>
                        </div>
                        <div class='form-group row'>
                            <label class='control-label col-sm-2'  for='input_todo'>Todo/Info/Frage:</label>
                            <div class='col-sm-4'>
                                <textarea class="form-control form-control-sm" rows="15" id="input_todo" style="font-size:10pt" placeholder="Text eingeben..."></textarea>
                                <input type='button' id='button_add_los_todo' class='btn btn-outline-dark btn-sm' value='Hinzufügen'>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class='row mt-4 '>
        <div class='col-sm-8'>
            <div class="card">
                <div class="card-header"><h4>Ausschreibungs-Einträge</h4>
                </div>
                <div class="card-body">
                    <?php

                        $sql = "SELECT tabelle_lose_ToDos.id_tabelle_lose_ToDos, tabelle_projekte.Interne_Nr, tabelle_projekte.Projektname, 
                                        tabelle_lose_extern.LosNr_Extern, tabelle_lose_extern.LosBezeichnung_Extern, tabelle_lose_extern.Vergabe_abgeschlossen, 
                                        tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_lose_ToDos.Datum, tabelle_lose_ToDos.Ersteller
                                FROM tabelle_projekte INNER JOIN (tabelle_elemente INNER JOIN (tabelle_lose_ToDos INNER JOIN tabelle_lose_extern ON tabelle_lose_ToDos.id_tabelle_lose_extern = tabelle_lose_extern.idtabelle_Lose_Extern) ON tabelle_elemente.idTABELLE_Elemente = tabelle_lose_ToDos.id_tabelle_element) ON tabelle_projekte.idTABELLE_Projekte = tabelle_lose_extern.tabelle_projekte_idTABELLE_Projekte
                                ORDER BY tabelle_projekte.Interne_Nr, tabelle_lose_extern.LosNr_Extern, tabelle_elemente.ElementID;";
                        $result = $mysqli->query($sql);

                        echo "<table class='table table-striped table-bordered table-sm' id='tableAusschreibungsTodos'>
                        <thead><tr>
                        <th>ID</th>
                        <th>Projekt#</th>
                        <th>Projekt</th>
                        <th>Los#</th>
                        <th>Los</th>
                        <th>Abgeschlossen</th>
                        <th>ElementID</th>   
                        <th>Element</th>  
                        <th>Datum</th>
                        <th>Ersteller</th>
                        </tr>
                        </thead>
                        <tbody>";

                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>".$row["id_tabelle_lose_ToDos"]."</td>";
                            echo "<td>".$row["Interne_Nr"]."</td>";
                            echo "<td>".$row["Projektname"]."</td>";
                            echo "<td>".$row["LosNr_Extern"]."</td>";
                            echo "<td>".$row["LosBezeichnung_Extern"]."</td>";
                            echo "<td style='text-align: center'>";
                                switch ($row["Vergabe_abgeschlossen"]) {
                                    case 0:
                                        echo "<span class='badge badge-pill badge-danger'>Offen</span>";
                                        break;
                                    case 1:
                                        echo "<span class='badge badge-pill badge-success'>Fertig</span>";
                                        break;
                                    case 2:
                                        echo "<span class='badge badge-pill badge-primary'>Wartend</span>";
                                        break;
                                }
                            echo "</td>";
                            echo "<td>".$row["ElementID"]."</td>";
                            echo "<td>".$row["Bezeichnung"]."</td>";
                            echo "<td>".$row["Datum"]."</td>";
                            echo "<td>".$row["Ersteller"]."</td>";
                            echo "</tr>";
                        }
                        echo "</tbody></table>";
                ?>
                </div>
            </div>
        </div>
        <div class='col-sm-4'>
            <div class="card">
                <div class="card-header"><h4>Details</h4>
                </div>
                <div class="card-body">
                    <label for='todo'>Todo/Info/Frage:</label>
                    <textarea class="form-control form-control-sm" rows="15" id="todo" style="font-size:10pt"></textarea>
                </div>
            </div>
    </div>
</div>

<script>
    var id = 0;
	// Tabellen formatieren
	$(document).ready(function(){
        $('#tableAusschreibungsTodos').DataTable( {
            "select":true,
            "paging": true,
            "pagingType": "simple",
            "lengthChange": false,
            "pageLength": 20,
            "columnDefs": [
                {
                    "targets": [ 0 ],
                    "visible": false,
                    "searchable": false
                }
            ],
            "order": [[ 1, "asc" ]],
                "orderMulti": true,
                "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"},
                "mark":true
        } );

        $('#datum').datepicker({
                format: "yyyy-mm-dd",
                calendarWeeks: true,
                autoclose: true,
                todayBtn: "linked"
        });

        var table = $('#tableAusschreibungsTodos').DataTable();
        $('#tableAusschreibungsTodos tbody').on( 'click', 'tr', function () {
	        if ( $(this).hasClass('info') ) {
	        }
	        else {
                // save id of row
	            id = table.row( $(this) ).data()[0];
                $.ajax({
                    url : "getLosToDo.php",
                    data:{"ID":id},
                    type: "GET",
                    success: function(data){
                        $("#todo").html(data);
                    }
                });

	        }
	    } );
	});

    $("#button_add_los_todo").click(function(){
        // IDs holen und prüfen
        let losID = $("#select_los").val();
        let elementID = $("#select_element").val();
        let datum = $("#datum").val();
        let todo_text = $("#input_todo").val();
        if(losID !== 0 && elementID !== 0 && todo_text.length > 0 && datum !== "") {
            $.ajax({
                url : "addLosToDo.php",
                type: "GET",
                data:{"losID":losID, "elementID":elementID, "datum":datum, "todo_text":todo_text},
                success: function(data){
                    alert(data);
                    //TODO reload der Tabelle
                }
            });
        }
        else {
            alert("Kontrollieren Sie die Eingaben!");
        }
    });

</script>

</body>

</html>
