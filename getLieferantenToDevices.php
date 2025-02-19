<?php
session_start();	
?>

<!DOCTYPE html >
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" /></head>
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
	    
			
	$sql = "SELECT tabelle_lieferant.idTABELLE_Lieferant, tabelle_lieferant.Lieferant, tabelle_lieferant.Land, tabelle_lieferant.Ort
                    FROM tabelle_geraete_has_tabelle_lieferant INNER JOIN tabelle_lieferant ON tabelle_geraete_has_tabelle_lieferant.tabelle_lieferant_idTABELLE_Lieferant = tabelle_lieferant.idTABELLE_Lieferant
                    WHERE (((tabelle_geraete_has_tabelle_lieferant.tabelle_geraete_idTABELLE_Geraete)=".$_SESSION["deviceID"]."))
                    ORDER BY tabelle_lieferant.Lieferant;";

	$result = $mysqli->query($sql);
	
	echo "<table class='table table-striped table-sm' id='tableDeviceLieferanten' cellspacing='0' width='100%'>
	<thead><tr>
	<th></th>
	<th>Lieferant</th>
	<th>Land</th>
	<th>Ort</th>
        <th></th>
	</tr></thead>
	<tbody>";
	
	while($row = $result->fetch_assoc()) {
	    echo "<tr>";
	    echo "<td><button type='button' id='".$row["idTABELLE_Lieferant"]."' class='btn btn-outline-danger btn-sm' value='deleteLieferant'><i class='fas fa-minus'></i></button></td>";
	    echo "<td>".$row["Lieferant"]."</td>";
            echo "<td>".$row["Land"]."</td>";
            echo "<td>".$row["Ort"]."</td>";
            echo "<td><button type='button' id='".$row["idTABELLE_Lieferant"]."' class='btn btn-outline-dark btn-sm' value='showLieferantContacts' data-toggle='modal' data-target='#showLieferantContactsModal'><i class='fas fa-users'></i></button></td>";
	    echo "</tr>";
	}
	
	echo "</tbody></table>";
        echo "<input type='button' id='addLieferantModalButton' class='btn btn-success btn-sm' value='Lieferant hinzufügen' data-toggle='modal' data-target='#addLieferantModal'></input>";
?>
	
	<!-- Modal zum Hinzufügen eines Lieferanten -->
	  <div class='modal fade' id='addLieferantModal' role='dialog'>
	    <div class='modal-dialog modal-md'>
	    
	      <!-- Modal content-->
	      <div class='modal-content'>
	        <div class='modal-header'>
                    <h4 class='modal-title'>Lieferant zu Gerät hinzufügen</h4>
	          <button type='button' class='close' data-dismiss='modal'>&times;</button>	          
	        </div>
	        <div class='modal-body' id='mbody'>
	        		<form role="form">
                                    <?php

                                            $sql = "SELECT tabelle_lieferant.idTABELLE_Lieferant, tabelle_lieferant.Lieferant, tabelle_lieferant.Land, tabelle_lieferant.Ort
                                                    FROM tabelle_lieferant WHERE tabelle_lieferant.idTABELLE_Lieferant NOT IN (SELECT tabelle_geraete_has_tabelle_lieferant.tabelle_lieferant_idTABELLE_Lieferant
                                                    FROM tabelle_geraete_has_tabelle_lieferant
                                                    WHERE ((tabelle_geraete_has_tabelle_lieferant.tabelle_geraete_idTABELLE_Geraete=".$_SESSION["deviceID"].")))
                                                    ORDER BY tabelle_lieferant.Lieferant;";
                                            $result = $mysqli->query($sql);

                                            echo "<div class='form-group'>
                                                            <label for='Lieferant'>Lieferant:</label>									
                                                                <select class='form-control input-sm' id='idlieferant' name='lieferant'>
                                                                        <option value=0>Lieferant auswählen</option>";
                                                                        while($row = $result->fetch_assoc()) {
                                                                                echo "<option value=".$row["idTABELLE_Lieferant"].">".$row["Lieferant"]." - ".$row["Land"]." ".$row["Ort"]."</option>";
                                                                        }	
                                                                echo "</select>	
                                                </div>";
                                    ?>	        			        			        			        	
                                    </form>
			</div>
	        <div class='modal-footer'>
	        	<input type='button' id='addLieferant' class='btn btn-success btn-sm' value='Hinzufügen' data-dismiss='modal'></input>
	          	<button type='button' class='btn btn-warning btn-sm' data-dismiss='modal'>Abbrechen</button>
	        </div>
	      </div>
	      
	    </div>
	  </div>
        
         <!-- Modal zum Anzeigen der Lieferantenmitarbeiter-->
	  <div class='modal fade' id='showLieferantContactsModal' role='dialog'>
	    <div class='modal-dialog modal-lg'>
	    
	      <!-- Modal content-->
	      <div class='modal-content'>
	        <div class='modal-header'>	          
	          <h4 class='modal-title'>Lieferantenkontakte</h4>
                  <button type='button' class='close' data-dismiss='modal'>&times;</button>
	        </div>
	        <div class='modal-body' id='mbody'>
                    <div id="data"></div>
                    
		</div>
	        <div class='modal-footer'>
                    <button type='button' class='btn btn-default btn-sm' data-dismiss='modal'>Schließen</button>
	        </div>
	      </div>	      
	    </div>
	  </div>
		
        <?php
                $mysqli ->close();
        ?>
	
<script>
    
	$(document).ready(function(){    
	   $("#tableDeviceLieferanten").DataTable( {
			"columnDefs": [
		            {
		                "targets": [ 0,4 ],
		                "searchable": false,
                                "sortable": false
		            }
		    ],
                    "paging": false,
                    "searching": false,
                    "info": false,
                    "order": [[ 1, "asc" ]],
	        //"pagingType": "simple_numbers",
	        //"lengthMenu": [[5,10, 25, 50, -1], [5,10, 25, 50, "All"]],
	        "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"} 		     
	    } );
	    	   
	} );
	
	//Lieferant zu Geraet hinzufügen
	$("#addLieferant").click(function(){
		var lieferantenID = $("#idlieferant").val();
   
		if(lieferantenID !== "0"){
		    $.ajax({
		        url : "addLieferantToDevice.php",
		        data:{"lieferantenID":lieferantenID},
		        type: "GET",	        
		        success: function(data){
                            alert(data);
                            $.ajax({
                                url : "getLieferantenToDevices.php",
                                type: "GET",
                                success: function(data){
                                    $("#deviceLieferanten").html(data);
                                }
                            } );			    	 			        
		        }
		    });			    
		}
		else{
                    alert("Kein Lieferant ausgewählt!");
		}    
        });
        
        //Lieferant von Gerät löschen
	$("button[value='deleteLieferant']").click(function(){
	    var id=this.id; 
	    if(id !== ""){			 
	        $.ajax({
		        url : "deleteLieferantFromDevice.php",
		        data:{"lieferantID":id},
		        type: "GET",
		        success: function(data){
                            alert(data);
                            $.ajax({
                                url : "getLieferantenToDevices.php",
                                type: "GET",
                                success: function(data){
                                    $("#deviceLieferanten").html(data);
                                }
                            } );
		        } 
	        }); 
	    }
        });

        //Lieferantenkontakte anzeigen
	$("button[value='showLieferantContacts']").click(function(){
	    var id=this.id; 
            
	    if(id !== ""){			 
	        $.ajax({
                    url : "getPersonsOfLieferant.php",
                    data:{"lieferantID":id},
                    type: "GET",
                    success: function(data){
                        $("#data").html(data);
                    } 
	        }); 
	    }
        });


</script>

</body>
</html>