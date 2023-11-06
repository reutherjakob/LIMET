<?php
session_start();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">

<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />


<!--DATEPICKER -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker3.min.css">
<script type='text/javascript' src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/js/bootstrap-datepicker.min.js"></script>

</head>
<body>
<?php
if(!isset($_SESSION["username"]))
   {
   echo "Bitte erst <a href=\"index.php\">einloggen</a>";
   exit;
   }
?>

<?php
	$mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');
	
	
	/* change character set to utf8 */
	if (!$mysqli->set_charset("utf8")) {
	    printf("Error loading character set utf8: %s\n", $mysqli->error);
	    exit();
	} 
	if($_GET["deviceID"] != ""){
		$_SESSION["deviceID"]=$_GET["deviceID"];    
	}
			
        $sql = "SELECT tabelle_wartungspreise.Datum, tabelle_wartungspreise.Info, tabelle_wartungspreise.Menge, tabelle_wartungspreise.Wartungsart, tabelle_wartungspreise.WartungspreisProJahr, tabelle_projekte.Projektname, tabelle_lieferant.Lieferant, tabelle_wartungspreise.idtabelle_wartungspreise
                FROM (tabelle_wartungspreise LEFT JOIN tabelle_lieferant ON tabelle_wartungspreise.tabelle_lieferant_idTABELLE_Lieferant = tabelle_lieferant.idTABELLE_Lieferant) LEFT JOIN tabelle_projekte ON tabelle_wartungspreise.tabelle_projekte_idTABELLE_Projekte = tabelle_projekte.idTABELLE_Projekte
                 WHERE (((tabelle_wartungspreise.tabelle_geraete_idTABELLE_Geraete)=".$_SESSION["deviceID"]."));";
        
        
	$result = $mysqli->query($sql);
	setlocale(LC_MONETARY,"de_DE");
	
	echo "<table class='table table-striped table-sm' id='tableDeviceServicePrices' cellspacing='0'>
	<thead><tr>";
        echo "<th>Datum</th>
		<th>Info</th>
		<th>Menge</th>
		<th>Wartungsart</th>
		<th>Preis/Jahr</th>
                <th>Projekt</th>
                <th>Lieferant</th>
	</tr></thead><tbody>";
	
	while($row = $result->fetch_assoc()) {
	    echo "<tr>";
	    $date = date_create($row["Datum"]);
	    echo "<td>".date_format($date, 'Y-m-d')."</td>";
	    echo "<td>".$row["Info"]."</td>";
            echo "<td>".$row["Menge"]."</td>";
            if($row["Wartungsart"] === "0"){
                echo "<td>Betriebswartung</td>";
            }
            else{
                echo "<td>Vollwartung</td>";
            }
            echo "<td>".money_format("%i", $row["WartungspreisProJahr"])."</td>";
            echo "<td>".$row["Projektname"]."</td>";
            echo "<td>".$row["Lieferant"]."</td>";
	    echo "</tr>";
	}
	
	echo "</tbody></table>";
	
	if($_SESSION["dbAdmin"]=="1"){
		echo "<input type='button' id='addServicePriceModalButton' class='btn btn-success btn-sm' value='Wartungspreis hinzufügen' data-toggle='modal' data-target='#addServicePriceModal'></input>";
	}
	
	
	?>
	
	<!-- Modal zum Anlegen eines Preises -->
	  <div class='modal fade' id='addServicePriceModal' role='dialog'>
	    <div class='modal-dialog modal-md'>	    
	      <!-- Modal content-->
	      <div class='modal-content'>
	        <div class='modal-header'>
                    <h4 class='modal-title'>Wartungspreis hinzufügen</h4>
	          <button type='button' class='close' data-dismiss='modal'>&times;</button>	          
	        </div>
	        <div class='modal-body' id='mbody'>
	        		<form role="form">
                                        <div class="form-group">
                                          <label for="dateService">Datum:</label>
                                          <input type="text" class="form-control" id="dateService" placeholder="jjjj.mm.tt"/>
                                        </div>
                                        <div class="form-group">
                                          <label for="infoService">Info:</label>
                                          <input type="text" class="form-control" id="infoService" placeholder="Verfahrensart, Anmerkung,..."/>
                                        </div>	        	
                                        <div class="form-group">
                                          <label for="mengeService">Menge:</label>
                                          <input type="text" class="form-control" id="mengeService"/>
                                        </div>
                                        <div class="form-group">
                                            <label for="wartungsart">Wartungsart:</label>
                                            <select class="form-control input-sm" id="wartungsart" name="wartungsart">
                                                <option value="0" selected>Betriebswartung</option>
                                                <option value="1">Vollwartung</option>	
                                            </select>                                          
                                        </div>
                                        <div class="form-group">
                                          <label for="wartungspreis">Durchschnittlicher Wartungspreis für 1 Jahr:</label>
                                          <input type="text" class="form-control" id="wartungspreis" placeholder="Komma ."/>
                                        </div>     
                                    
                                    <?php
                                                $sql = "SELECT tabelle_projekte.idTABELLE_Projekte, tabelle_projekte.Interne_Nr, tabelle_projekte.Projektname"
                                                        . " FROM tabelle_projekte ORDER BY tabelle_projekte.Interne_Nr;";
																																				
                                                $result1 = $mysqli->query($sql);	        				        	
                                                
	        				echo "<div class='form-group'>
                                                    <label for='projectService'>Projekt:</label>									
                                                    <select class='form-control input-sm' id='projectService' name='projectService'>
                                                            <option value=0>Kein Projekt</option>";                                                            
                                                            while($row = $result1->fetch_assoc()) {
                                                                  echo "<option value=".$row["idTABELLE_Projekte"].">".$row["Interne_Nr"]."-".$row["Projektname"]."</option>";
                                                            }	
                                                    echo "</select>										
                                                </div>";
                                                    
						$sql = "SELECT tabelle_lieferant.Lieferant, tabelle_lieferant.idTABELLE_Lieferant
                                                        FROM tabelle_lieferant INNER JOIN tabelle_geraete_has_tabelle_lieferant ON tabelle_lieferant.idTABELLE_Lieferant = tabelle_geraete_has_tabelle_lieferant.tabelle_lieferant_idTABELLE_Lieferant
                                                        WHERE (((tabelle_geraete_has_tabelle_lieferant.tabelle_geraete_idTABELLE_Geraete)=".$_SESSION["deviceID"]."));";
                                                
                                                $result1 = $mysqli->query($sql);	        				        	
                                                
	        				echo "<div class='form-group'>
                                                    <label for='lieferantService'>Lieferant:</label>									
                                                    <select class='form-control input-sm' id='lieferantService' name='lieferantService'>
                                                            <option value=0>Lieferant auswählen</option>";
                                                            while($row = $result1->fetch_assoc()) {
                                                                  echo "<option value=".$row["idTABELLE_Lieferant"].">".$row["Lieferant"]."</option>";
                                                            }	
                                                    echo "</select>										
                                                </div>";
                                                $mysqli ->close();
	        			?>	
                              </form>
			</div>
	        <div class='modal-footer'>
	        	<input type='button' id='addServicePrice' class='btn btn-success btn-sm' value='Speichern' data-dismiss='modal'></input>
	          	<button type='button' class='btn btn-danger btn-sm' data-dismiss='modal'>Abbrechen</button>
	        </div>
	      </div>
	      
	    </div>
	  </div>

	
<script>
    $(document).ready(function(){   
        $('#dateService').datepicker({
            format: "yyyy-mm-dd",
            calendarWeeks: true,
            autoclose: true,
            todayBtn: "linked",
            language: "de"
        });
    });
            
	    
   $("#tableDeviceServicePrices").DataTable( {
        "paging": true,
        "pagingType": "simple",
        "lengthChange": false,
        "searching": false,
        "info": false,
        "order": [[ 0, "desc" ]],
        "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"} 		     
    } );

	
    //Wartungspreis zu Geraet hinzufügen
    $("#addServicePrice").click(function(){
        var date = $("#dateService").val();
        var info = $("#infoService").val();
        var menge  = $("#mengeService").val();
        var wartungsart = $("#wartungsart").val();
        var wartungspreis = $("#wartungspreis").val();
        var project = $("#projectService").val();
        var lieferant = $("#lieferantService").val();
        
        if(date !== "" && info !== "" && menge !== "" && wartungsart !== "" && wartungspreis !== "" && lieferant > 0){
            $.ajax({
                url : "addServicePriceToDevice.php",
                data:{"date":date,"info":info ,"menge":menge,"wartungsart":wartungsart,"wartungspreis":wartungspreis,"project":project,"lieferant":lieferant},
                type: "GET",	        
                success: function(data){
                    alert(data);
                    $.ajax({
                        url : "getDeviceServicePrices.php",
                        type: "GET",
                        success: function(data){
                            $("#deviceServicePrices").html(data);
                        }
                    } );		        
                }
            });	

        }
        else{
                alert("Bitte alle Felder ausfüllen!");
        }    
    });
    
    


</script>

</body>
</html>