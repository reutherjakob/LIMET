<?php
session_start();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
	
	$sql = "SELECT tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung, tabelle_räume.Geschoss, tabelle_räume.`Raumbereich Nutzer`, tabelle_varianten.Variante, tabelle_verwendungselemente.id_Standortelement
                FROM tabelle_verwendungselemente INNER JOIN (tabelle_varianten INNER JOIN (tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) ON tabelle_varianten.idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) ON tabelle_verwendungselemente.id_Standortelement = tabelle_räume_has_tabelle_elemente.id
                WHERE (((tabelle_verwendungselemente.id_Verwendungselement)=".$_GET["id"]."));";    
        
	$result = $mysqli->query($sql);
	
	echo "<div class='table-responsive'><table class='table table-striped table-sm' id='tableElementStandortdaten' cellspacing='0'>
	<thead><tr>
        <th></th>
        <th>Variante</th>
	<th>Raumnr</th>
	<th>Raumbezeichnung</th>
	<th>Geschoss</th>
	<th>Raumbereich Nutzer</th>
	</tr></thead>
	<tbody>";
	
	while($row = $result->fetch_assoc()) {
	    echo "<tr>";           
            echo "<td><button type='button' id='".$row["id_Standortelement"]."' class='btn btn-danger btn-xs' value='deleteStandortElement'><i class='fas fa-minus-circle'></i></button></td>";
            echo "<td>".$row["Variante"]."</td>";
	    echo "<td>".$row["Raumnr"]."</td>";
	    echo "<td>".$row["Raumbezeichnung"]."</td>";
            echo "<td>".$row["Geschoss"]."</td>";
            echo "<td>".$row["Raumbereich Nutzer"]."</td>";
	    echo "</tr>";
	    
	}	
	echo "</tbody></table></div>";
        //echo "<input type='button' id='addStandortElementModalButton' class='btn btn-success btn-sm' value='Standortelement hinzufügen' data-toggle='modal' data-target='#addStandortElementModal'></input>";
        echo "<button type='button' id='addStandortElementModalButton' class='btn ml-4 mt-2 btn-success btn-xs' value='Standortelement hinzufügen' data-toggle='modal' data-target='#addStandortElementModal'><i class='fas fa-plus-square'></i></button>";
	
        
        ?>
        <!-- Modal zum Hinzufügen eines Standortelements -->
	  <div class='modal fade' id='addStandortElementModal' role='dialog'>
	    <div class='modal-dialog modal-md'>
	    
	      <!-- Modal content-->
	      <div class='modal-content'>
	        <div class='modal-header'>
	          <h4 class='modal-title'>Standortelement hinzufügen</h4>
                    <button type='button' class='close' data-dismiss='modal'>&times;</button>   	          
	        </div>
	        <div class='modal-body' id='mbody'>
                    <form role="form">
                        <?php          

                                $sql = "SELECT tabelle_räume_has_tabelle_elemente.id, tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung, tabelle_räume.Geschoss, tabelle_räume.`Raumbereich Nutzer`, tabelle_räume_has_tabelle_elemente.Anzahl, tabelle_varianten.Variante, tabelle_bestandsdaten.Inventarnummer
                                        FROM tabelle_bestandsdaten RIGHT JOIN (tabelle_varianten INNER JOIN (tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) ON tabelle_varianten.idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) ON tabelle_bestandsdaten.tabelle_räume_has_tabelle_elemente_id = tabelle_räume_has_tabelle_elemente.id
                                        WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"].") AND ((tabelle_räume_has_tabelle_elemente.Standort)=1) AND ((tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente)=".filter_input(INPUT_GET, 'elementID')."));";
                                
                                $result = $mysqli->query($sql);

                                echo "<div class='form-group'>
                                                <label for='standortElement'>Standortelement:</label>									
                                                        <select class='form-control form-control-sm' id='standortElement'>";
                                                            echo "<option value='0' selected>Standortelement auswählen!</option>";
                                                            while($row = $result->fetch_assoc()) {
                                                                    echo "<option value=".$row["id"].">Raumbereich: ".$row["Raumbereich Nutzer"]." - Raumnr:".$row["Raumnr"]." - Raum:".$row["Raumbezeichnung"]." - Stk:".$row["Anzahl"]." - Variante:".$row["Variante"]." - Inventarnummer:".$row["Inventarnummer"]."</option>";
                                                            }	
                                                        echo "</select>	

                                        </div>";
                        ?>	        			        			        			        	
                    </form>
                </div>
	        <div class='modal-footer'>
	        	<input type='button' id='addStandortElement' class='btn btn-success btn-sm' value='Hinzufügen'></input>                        
	          	<button type='button' class='btn btn-default btn-sm' data-dismiss='modal'>Abbrechen</button>
	        </div>
	      </div>
	      
	    </div>
	  </div>
        <?php
	$mysqli ->close();
	?>
	
        
<script>
   
   var id = <?php echo filter_input(INPUT_GET, 'id') ?>;
   var elementID = <?php echo filter_input(INPUT_GET, 'elementID') ?>;
	    
   $("#tableElementStandortdaten").DataTable( {
		"paging": false,
		"searching": false,
		"info": false,
                "columnDefs": [
                            {
                                "targets": [ 0 ],                            
                                "searchable": false,
                                "sortable":false
                            }
                ],
        //"pagingType": "simple_numbers",
        //"lengthMenu": [[5,10, 25, 50, -1], [5,10, 25, 50, "All"]],
        "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"},
        "scrollY":        '20vh',
    	"scrollCollapse": true   		     
    } );
    
    //Standortelement hinzufügen
    $("#addStandortElement").click(function(){
            var standortElement = $("#standortElement").val();
            
            if(standortElement !== "0"){                
                $.ajax({
                    url : "addStandortElement.php",
                    data:{"standortElement":standortElement,"id":id},
                    type: "GET",	        
                    success: function(data){
                        $('#addStandortElementModal').modal('hide');
                        alert(data);
                        
                        $.ajax({
                            url : "getElementStandort.php",
                            data:{"id":id,"elementID":elementID},
                            type: "GET",
                            success: function(data){
                                $("#elementVerwendung").html(data);
                            }
                        } );			    	 			        
                    }
                });			    
            }
            else{
                    alert("Bitte Standortelement auswählen!");
            }  
            
    });
    
    //Standortelement löschen
	$("button[value='deleteStandortElement']").click(function(){
	    var standortID =this.id; 
	    if(standortID !== ""){			 
	        $.ajax({
		        url : "deleteStandortElement.php",
		        data:{"standortID":standortID,"verwendungID":id},
		        type: "GET",
		        success: function(data){
		        	alert(data);
		        	$.ajax({
				        url : "getElementStandort.php",
                                        data:{"id":id,"elementID":elementID},
				        type: "GET",
				        success: function(data){
				        	$("#elementVerwendung").html(data);
				        } 
			        }); 

		        } 
	        }); 
	    }
    });


</script>

</body>
</html>