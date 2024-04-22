<?php
    session_start();
    $_SESSION["dbAdmin"]="0";
    include '_utils.php';
init_page_serversides();
?> 


<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>RB - Ausführung</title>
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

<!--Bootstrap Toggle -->
<link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>

 
 
</head>
<body style="height:100%">
 
<div class="container-fluid" >
    <div id="limet-navbar"></div> <!-- Container für Navbar -->		

    <div class='row mt-4 '>
        <div class='col-sm-4'>  
            <div class="card">
                <div class="card-header"><h4>Gewerke</h4>
                </div>
                <div class="card-body">
                    <?php
                        $mysqli = utils_connect_sql();
                        
                        $sql = "SELECT tabelle_lose_extern.idtabelle_Lose_Extern, tabelle_lose_extern.LosNr_Extern, tabelle_lose_extern.LosBezeichnung_Extern, tabelle_lieferant.Lieferant, tabelle_lose_extern.Vorleistungspruefung
                                FROM (tabelle_lose_extern LEFT JOIN tabelle_lieferant ON tabelle_lose_extern.tabelle_lieferant_idTABELLE_Lieferant = tabelle_lieferant.idTABELLE_Lieferant)
                                WHERE (((tabelle_lose_extern.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"]."))
                                ORDER BY tabelle_lose_extern.LosNr_Extern;";
                        
                        $result = $mysqli->query($sql);

                        echo "<table class='table table-striped table-bordered table-sm' id='tableGewerke'  cellspacing='0' width='100%'>
                        <thead><tr>
                        <th>ID</th>
                        <th>Nummer</th>
                        <th>Gewerk</th>
                        <th>Lieferant</th>
                        <th>Vorleistungsprüfung</th>
                        </tr></thead><tbody>";

                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>".$row["idtabelle_Lose_Extern"]."</td>";
                            echo "<td>".$row["LosNr_Extern"]."</td>";
                            echo "<td>".$row["LosBezeichnung_Extern"]."</td>";
                            echo "<td>".$row["Lieferant"]."</td>";
                            echo "<td style='text-align:center'>";
                                if($row["Vorleistungspruefung"] === '0'){
                                    echo "<span class='badge badge-pill badge-warning'> Nein </span>";
                                }
                                else{
                                    echo "<span class='badge badge-pill badge-success'> Ja </span>";
                                }
                            echo "</td>";
                            echo "</tr>";

                        }
                        echo "</tbody></table>";
                ?>
                </div>
            </div>
        </div>
        <div class='col-sm-3'>  
            <div class="card">
                <div class="card-header"><h4>Infos</h4>                     
                </div>
                <div class="card-body" id="div_Infos_Body"></div>
            </div>
        </div>        
        <!-- Darstellung PDF -->
        <div class="col-sm-5">
            <div class="card">
                <div class="card-header"><h4>Vorschau-PDF</h4></div>
                <div class="card-body embed-responsive embed-responsive-1by1" >
                    <iframe class="embed-responsive-item" id="pdfPreview" ></iframe>                    
                </div>
            </div>
        </div>
        <!-- ----------------------------->
    </div>
</div>      
<script>   
	// Tabellen formatieren
	$(document).ready(function(){	                        
            var table = $('#tableGewerke').DataTable( {
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
            
            $('#tableGewerke tbody').on( 'click', 'tr', function () {
                if ( $(this).hasClass('info') ) {
                }
                else {
                    table.$('tr.info').removeClass('info');
                    $(this).addClass('info');
                    $.ajax({
                        url : "getVermerkgruppenToGewerk.php",
                        data:{"vermerkGruppenID":table.row( $(this) ).data()[0]},
                        type: "GET",
                        success: function(data){
                            $("#div_Infos_Body").html(data); 
                        } 
                    });
                }
            });
            
	});        

</script>

</body>

</html>
