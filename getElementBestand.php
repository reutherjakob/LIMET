<?php
session_start();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<style>

.input-xs {
  height: 22px;
  padding: 2px 5px;
  font-size: 12px;
  line-height: 1.5; /* If Placeholder of the input is moved up, rem/modify this. */
  border-radius: 3px;
}

</style>

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
	
	if(filter_input(INPUT_GET, 'id') != ""){
		$_SESSION["roombookID"]=filter_input(INPUT_GET, 'id');
	}
        if(filter_input(INPUT_GET, 'stk') != ""){
		$_SESSION["stk"]=filter_input(INPUT_GET, 'stk');
	}

	
	/* change character set to utf8 */
	if (!$mysqli->set_charset("utf8")) {
	    printf("Error loading character set utf8: %s\n", $mysqli->error);
	    exit();
	} 
	
	
	// Abfrage der Element-Geräte
	$sql = "SELECT tabelle_geraete.idTABELLE_Geraete, tabelle_hersteller.Hersteller, tabelle_geraete.Typ
			FROM tabelle_räume_has_tabelle_elemente INNER JOIN (tabelle_hersteller INNER JOIN tabelle_geraete ON tabelle_hersteller.idtabelle_hersteller = tabelle_geraete.tabelle_hersteller_idtabelle_hersteller) ON tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente = tabelle_geraete.TABELLE_Elemente_idTABELLE_Elemente
			WHERE (((tabelle_räume_has_tabelle_elemente.id)=".$_SESSION["roombookID"]."))
			ORDER BY tabelle_hersteller.Hersteller;";
	
	$result = $mysqli->query($sql);
				
	$possibleDevices = array();
	while ($row = $result->fetch_assoc()) {
	    $possibleDevices[$row['idTABELLE_Geraete']]['Hersteller'] = $row['Hersteller'];
	    $possibleDevices[$row['idTABELLE_Geraete']]['Typ'] = $row['Typ'];
	    $possibleDevices[$row['idTABELLE_Geraete']]['idTABELLE_Geraete'] = $row['idTABELLE_Geraete'];

	}

	
	$sql = "SELECT `tabelle_bestandsdaten`.`idtabelle_bestandsdaten`, tabelle_bestandsdaten.Inventarnummer, tabelle_bestandsdaten.Seriennummer, tabelle_bestandsdaten.Anschaffungsjahr, tabelle_bestandsdaten.`Aktueller Ort`, tabelle_bestandsdaten.tabelle_geraete_idTABELLE_Geraete
			FROM tabelle_bestandsdaten
			WHERE tabelle_bestandsdaten.tabelle_räume_has_tabelle_elemente_id=".$_SESSION["roombookID"].";";				
	
		    
	$result = $mysqli->query($sql);
	$row_cnt = $result->num_rows;
        echo "<button type='button' id='addBestandsElement' class='btn ml-4 mt-2 btn-outline-success btn-xs' value='Hinzufügen' data-toggle='modal' data-target='#addBestandModal'><i class='fas fa-plus'></i></button>";
	echo "<div class='table-responsive'><table class='table table-striped table-bordered table-sm' id='tableElementBestandsdaten' cellspacing='0' width='100%'>
	<thead><tr>
	<th>ID</th>
	<th></th>
	<th>Inventarnummer</th>
	<th>Seriennummer</th>
	<th>Anschaffungsjahr</th>
	<th>Gerät</th>
        <th>Standort aktuell</th>
	<th></th>
        <th>Check ob genug bestand da</th>
	</tr></thead>
	<tbody>";
	
	while($row = $result->fetch_assoc()) {
	    echo "<tr>";
	    echo "<td>".$row["idtabelle_bestandsdaten"]."</td>";
	    echo "<td><button type='button' id='".$row["idtabelle_bestandsdaten"]."' class='btn btn-danger btn-xs' value='deleteBestand'><i class='fas fa-minus-circle'></i></button></td>";
	    echo "<td><input class='form-control form-control-sm' type='text' id='inventNr".$row["idtabelle_bestandsdaten"]."' value='".$row["Inventarnummer"]."' ></input></td>";
	    echo "<td><input class='form-control form-control-sm' type='text' id='serienNr".$row["idtabelle_bestandsdaten"]."' value='".$row["Seriennummer"]."' ></input></td>";
	    echo "<td><input class='form-control form-control-sm' type='text' id='yearNr".$row["idtabelle_bestandsdaten"]."' value='".$row["Anschaffungsjahr"]."' ></input></td>";
	    echo "<td><select class='form-control form-control-sm' id='gereatIDSelect".$row["idtabelle_bestandsdaten"]."'>";
                if($row["tabelle_geraete_idTABELLE_Geraete"] != ""){						
                        echo "<option value=0>Gerät wählen</option>";
                        foreach($possibleDevices as $array) {
                                if($array['idTABELLE_Geraete'] == $row["tabelle_geraete_idTABELLE_Geraete"]){
                                        echo "<option selected value=".$array['idTABELLE_Geraete'].">".$array['Hersteller']."-".$array['Typ']."</option>";
                                }
                                else{
                                        echo "<option value=".$array['idTABELLE_Geraete'].">".$array['Hersteller']."-".$array['Typ']."</option>";
                                }		
                        }
                }
                else{
                        echo "<option value=0 selected>Gerät wählen</option>";
                        foreach($possibleDevices as $array) {
                                echo "<option value=".$array['idTABELLE_Geraete'].">".$array['Hersteller']."-".$array['Typ']."</option>";									
                        }
                }
            echo "</select></td>";
            echo "<td><input class='form-control form-control-sm' type='text' id='currentPlace".$row["idtabelle_bestandsdaten"]."' value='".$row["Aktueller Ort"]."' ></input></td>";
            echo "<td><button type='button' id='".$row["idtabelle_bestandsdaten"]."' class='btn btn-warning btn-xs' value='saveBestand'><i class='far fa-save'></i></button></td>";
            echo "<td>";
                if($row_cnt==$_SESSION["stk"]){
                    echo "1";
                }
                else{
                    echo "0";
                }
            echo "</td>";
	    echo "</tr>";
	    
	}
	
	echo "</tbody></table></div>";
	//echo "<input type='button' id='addBestandsElement' class='btn btn-success btn-sm' value='Hinzufügen' data-toggle='modal' data-target='#addBestandModal'></input>";
        
        
	$mysqli ->close();
	?>
    
                    
	<!-- Modal zum Anlegen eines Bestands -->
	  <div class='modal fade' id='addBestandModal' role='dialog'>
	    <div class='modal-dialog modal-md'>	    
	      <!-- Modal content-->
	      <div class='modal-content'>
	        <div class='modal-header'>
                    <h4 class='modal-title'>Bestand hinzufügen</h4>
                    <button type='button' class='close' data-dismiss='modal'>&times;</button>
	          
	        </div>
	        <div class='modal-body' id='mbody'>
	        		<form role="form">        			        			        		
                                        <div class="form-group">
                                          <label for="invNr">Inventarnummer:</label>
                                          <input type="text" class="form-control form-control-sm" id="invNr" placeholder="Inventarnummer"/>
                                        </div>
                                        <div class="form-group">
                                          <label for="year">Anschaffungsjahr:</label>
                                          <input type="text" class="form-control form-control-sm" id="year" placeholder="Anschaffungsjahr"/>
                                        </div>
                                        <div class="form-group">
                                          <label for="serNr">Seriennummer:</label>
                                          <input type="text" class="form-control form-control-sm" id="serNr" placeholder="Seriennummer"/>
                                        </div>	
                                        <?php	        				        			
                                      echo "<div class='form-group'>
                                                      <label for='geraet'>Gerät:</label>									
                                                                      <select class='form-control form-control-sm' id='geraetNr' name='geraet'>
                                                                              <option value=0 selected>Gerät wählen</option>";
                                                                              foreach($possibleDevices as $array) {
                                                                                              echo "<option value=".$array['idTABELLE_Geraete'].">".$array['Hersteller']."-".$array['Typ']."</option>";	
                                                                              }
                                                                      echo "</select>										
                                              </div>";
                                      ?>
                                        <div class="form-group">
                                          <label for="currentPlace">Standort aktuell:</label>
                                          <input type="text" class="form-control form-control-sm" id="currentPlace" placeholder="Standort"/>
                                        </div>	
                              </form>
			</div>
	        <div class='modal-footer'>
	        	<input type='button' id='addBestand' class='btn btn-success btn-sm' value='Hinzufügen' data-dismiss='modal'></input>
	        	<input type='button' id='saveBestand' class='btn btn-warning btn-sm' value='Speichern' data-dismiss='modal'></input>
	          	<button type='button' class='btn btn-danger btn-sm' data-dismiss='modal'>Abbrechen</button>                       
	        </div>
	      </div>
	    </div>
	  </div>

	
<script>
    
    
	$(document).ready(function() {    
	   $("#tableElementBestandsdaten").DataTable( {
			"paging": false,
                        "sortable":false,
			"searching": false,
			"info": false,
			"columnDefs": [
                            {
                                "targets": [ 0,8 ],
                                "visible": false,
                                "searchable": false
                            },
                            {
                                "targets": [ 1,7 ],
                                "visible": true,
                                "searchable": false,
                                "sortable": false
                            }
	        ],
	        //"pagingType": "simple_numbers",
	        //"lengthMenu": [[5,10, 25, 50, -1], [5,10, 25, 50, "All"]],
	        "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"},
	        "scrollY":        '20vh',
	    	"scrollCollapse": true,              
	        "fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
	    	    if ( aData[8] === "0" )
                    {
                        $('td', nRow).css('background-color', 'LightCoral');
                    }
                    else
                    {
                        $('td', nRow).css('background-color', 'LightGreen');
                    }
                }    
	    } );
    
    	var table = $('#tableElementBestandsdaten').DataTable();
 
	    $('#tableElementBestandsdaten tbody').on( 'click', 'tr', function () {
	        if ( $(this).hasClass('info') ) {
	            //$(this).removeClass('info');
	        }
	        else {
	            table.$('tr.info').removeClass('info');
	            $(this).addClass('info');
	        }
	    } ); 
	    		
	} );

    
    //Bestand hinzufügen
	$("#addBestand").click(function(){
		var inventarNr = $("#invNr").val();
		var anschaffungsJahr = $("#year").val();
		var serienNr  = $("#serNr").val();
		var gereatID = $("#geraetNr").val();
                var currentPlace = $("#currentPlace").val();

		if(inventarNr !== ""){
		    $.ajax({
		        url : "addBestand.php",
		        data:{"inventarNr":inventarNr ,"anschaffungsJahr":anschaffungsJahr ,"serienNr":serienNr,"gereatID":gereatID,"currentPlace":currentPlace },
		        type: "GET",	        
		        success: function(data){
		        	alert(data);
		        	$.ajax({
				        url : "getElementBestand.php",
				        type: "GET",
				        success: function(data){
				            $("#elementBestand").html(data);				          
				        }
		    		} );			    	 			        
		        }
		    });	
		    
		}
		else{
			alert("Bitte Inventarnummer angeben!");
		}  
		  
    });
	
	//Bestand löschen
	$("button[value='deleteBestand']").click(function(){
	    var id=this.id; 
	    if(id !== ""){			 
	        $.ajax({
		        url : "deleteBestand.php",
		        data:{"bestandID":id},
		        type: "GET",
		        success: function(data){
		        	alert(data);
		        	$.ajax({
				        url : "getElementBestand.php",
				        type: "GET",
				        success: function(data){
				        	$("#elementBestand").html(data);
				        } 
			        }); 

		        } 
	        }); 
	    }
    });
    
    //Bestand ändern
	$("button[value='changeBestand']").click(function(){
	    var id=this.id;
            $("#saveBestand").show();
            $("#addBestand").hide(); 
            document.getElementById("invNr").value = invent_clicked;
            document.getElementById("year").value = anschaffung_clicked; 
            document.getElementById("serNr").value = serien_clicked;   
            $('#addBestandModal').modal("show");  
	});
	
	
	//Änderung speichern
	$("button[value='saveBestand']").click(function(){
            var ID  = this.id;
            var geraeteIDNeu = $("#gereatIDSelect"+ID).val();
            var inventNr = $("#inventNr"+ID).val();
            var serienNr= $("#serienNr"+ID).val();
            var yearNr = $("#yearNr"+ID).val();
            var currentPlace = $("#currentPlace"+ID).val();
	    
	    if(ID !== "" && inventNr !== ""){			 
	        $.ajax({
		        url : "saveBestand.php",
		        data:{"inventarNr":inventNr ,"anschaffungsJahr":yearNr ,"serienNr":serienNr,"bestandID":ID,"geraeteID":geraeteIDNeu,"currentPlace":currentPlace},
		        type: "GET",
		        success: function(data){
		        	alert(data);
		        	$.ajax({
				        url : "getElementBestand.php",
				        type: "GET",
				        success: function(data){
				        	$("#elementBestand").html(data);
				        } 
			        }); 
		        } 
	        }); 
	    }
	    else{
	    	alert("Keine Bestands-ID gefunden bzw. Keine Inventarnummer eingegeben!");
	    }
	    
    });

	
	//Bestand hinzufügen Buttons ein/ausblenden
	$("#addBestandsElement").click(function(){
	    var id=this.id;
	    $("#addBestand").show();
	    $("#saveBestand").hide();
	});




</script>

</body>
</html>