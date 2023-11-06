<?php
session_start();
?>

<!DOCTYPE html>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />

<html>
<head>
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
	// Check connection
	if ($mysqli->connect_error) {
	    die("Connection failed: " . $mysqli->connect_error);
	}
	else{
		// Abfrage der Lieferanten-Kontakte
                $sql="SELECT tabelle_ansprechpersonen.idTABELLE_Ansprechpersonen, tabelle_ansprechpersonen.Name, tabelle_ansprechpersonen.Vorname, tabelle_ansprechpersonen.Tel, tabelle_ansprechpersonen.Adresse, tabelle_ansprechpersonen.PLZ, tabelle_ansprechpersonen.Ort, tabelle_ansprechpersonen.Land, tabelle_ansprechpersonen.Mail, tabelle_lieferant.Lieferant, tabelle_abteilung.Abteilung,
                         tabelle_lieferant.idTABELLE_Lieferant, tabelle_abteilung.idtabelle_abteilung, tabelle_ansprechpersonen.Gebietsbereich
                        FROM tabelle_abteilung INNER JOIN (tabelle_lieferant INNER JOIN tabelle_ansprechpersonen ON tabelle_lieferant.idTABELLE_Lieferant = tabelle_ansprechpersonen.tabelle_lieferant_idTABELLE_Lieferant) ON tabelle_abteilung.idtabelle_abteilung = tabelle_ansprechpersonen.tabelle_abteilung_idtabelle_abteilung;";						
                $result = $mysqli->query($sql);

                echo "<table class='table table-striped table-bordered nowrap table-condensed' id='tableLieferanten'  cellspacing='0' width='100%'>
                <thead><tr>
                <th>ID</th>
                <th>Name</th>
                <th>Vorname</th>
                <th>Tel</th>
                <th>Mail</th>
                <th>Adresse</th>
                <th>PLZ</th>
                <th>Ort</th>
                <th>Land</th>
                <th>Lieferant</th>
                <th>Abteilung</th>
                <th>Gebiet</th>
                <th></th>
                <th></th>
                <th></th>
                </tr></thead><tbody>";


                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>".$row["idTABELLE_Ansprechpersonen"]."</td>";
                    echo "<td>".$row["Name"]."</td>";
                    echo "<td>".$row["Vorname"]."</td>";
                    echo "<td>".$row["Tel"]."</td>";
                    echo "<td>".$row["Mail"]."</td>";
                    echo "<td>".$row["Adresse"]."</td>";
                    echo "<td>".$row["PLZ"]."</td>";
                    echo "<td>".$row["Ort"]."</td>";
                    echo "<td>".$row["Land"]."</td>";
                    echo "<td>".$row["Lieferant"]."</td>";
                    echo "<td>".$row["Abteilung"]."</td>";
                    echo "<td>".$row["Gebietsbereich"]."</td>";
                    echo "<td><button type='button' id='".$row["idTABELLE_Ansprechpersonen"]."' class='btn btn-default btn-xs' value='changeContact' data-toggle='modal' data-target='#addContactModal'><span class='glyphicon glyphicon-pencil'></span></button></td>";
                    echo "<td>".$row["idTABELLE_Lieferant"]."</td>";
                    echo "<td>".$row["idtabelle_abteilung"]."</td>";
                    echo "</tr>";

                }
                echo "</tbody></table>";	
                echo "<input type='button' id='addContactModalButton' class='btn btn-success btn-sm' value='Lieferantenkontakt hinzufügen' data-toggle='modal' data-target='#addContactModal'></input>";
	}
	
	
?>
    <!-- Modal zum Anlegen eines Firmenkontakts -->
	  <div class='modal fade' id='addContactModal' role='dialog'>
	    <div class='modal-dialog modal-md'>
	    
	      <!-- Modal content-->
	      <div class='modal-content'>
	        <div class='modal-header'>
	          <button type='button' class='close' data-dismiss='modal'>&times;</button>
	          <h4 class='modal-title'>Lieferantenkontakt hinzufügen</h4>
	        </div>
	        <div class='modal-body' id='mbody'>
                    <form role="form">                        
                        <div class='form-group'>
                            <label for='lieferantenName'>Name</label>
                            <input type='text' class='form-control input-sm' id='lieferantenName'></input>					  			 											 						 			
                        </div>		  			 		
                        <div class='form-group'>
                            <label for='lieferantenVorname'>Vorname</label>
                            <input type='text' class='form-control input-sm' id='lieferantenVorname'></input>	
                        </div>
                            <div class='form-group'>
                                            <label class='control-label' for='lieferantenTel'>Tel</label>
                                            <input type='text' class='form-control input-sm' id='lieferantenTel'></input>
                            </div>
                            <div class='form-group'>
                                            <label class='control-label' for='lieferantenAdresse'>Adresse</label>
                                             <input type='text' class='form-control input-sm' id='lieferantenAdresse'></input>	
                            </div>
                            <div class='form-group'>
                                            <label class='control-label' for='lieferantenPLZ'>PLZ</label>
                                            <input type='text' class='form-control input-sm' id='lieferantenPLZ'></input>	
                            </div>
                            <div class='form-group'>
                                            <label class='control-label' for='lieferantenOrt'>Ort</label>
                                            <input type='text' class='form-control input-sm' id='lieferantenOrt'></input>	
                            </div>
                            <div class='form-group'>
                                            <label class='control-label' for='lieferantenLand'>Land</label>
                                            <input type='text' class='form-control input-sm' id='lieferantenLand'></input>	
                            </div>
                            <div class='form-group'>
                                            <label class='control-label' for='lieferantenEmail'>Email</label>
                                            <input type='text' class='form-control input-sm' id='lieferantenEmail'></input>
                            </div>
                            <?php 
                                $sql = "SELECT `tabelle_lieferant`.`idTABELLE_Lieferant`,
                                             `tabelle_lieferant`.`Lieferant`
                                         FROM `LIMET_RB`.`tabelle_lieferant` ORDER BY Lieferant;"; 
                                 $result = $mysqli->query($sql);

                                echo "<div class='form-group'>
                                                <label class='control-label' for='lieferant'>Lieferant</label>
                                                        <select class='form-control input-sm' id='lieferant'>";
                                                                while($row = $result->fetch_assoc()) {
                                                                        echo "<option value=".$row["idTABELLE_Lieferant"].">".$row["Lieferant"]."</option>";		
                                                                }	
                                                        echo "</select>	
                                </div>";                                                        

                            $sql = "SELECT `tabelle_abteilung`.`idtabelle_abteilung`,
                                         `tabelle_abteilung`.`Abteilung`
                                     FROM `LIMET_RB`.`tabelle_abteilung` ORDER BY Abteilung;";
                            $result = $mysqli->query($sql);

                            echo "<div class='form-group'>
                                <label class='control-label' for='abteilung'>Abteilung</label>
                                        <select class='form-control input-sm' id='abteilung'>";
                                                while($row = $result->fetch_assoc()) {
                                                        echo "<option value=".$row["idtabelle_abteilung"].">".$row["Abteilung"]."</option>";		
                                                }
                                        echo "</select>
                                    </div>";
                                $mysqli ->close(); 
                            ?>
                        <div class='form-group'>
                                <label class='control-label' for='lieferantenGebiet'>Gebiet</label>
                                <input type='text' class='form-control input-sm' id='lieferantenGebiet'></input>
                            </div>
                    </form>
		</div>
	        <div class='modal-footer'>
                    <input type='button' id='addLieferantenKontakt' class='btn btn-success btn-sm' value='Hinzufügen'></input>
                    <input type='button' id='saveLieferantenKontakt' class='btn btn-warning btn-sm' value='Speichern'></input>
                    <button type='button' class='btn btn-default btn-sm' data-dismiss='modal'>Abbrechen</button>
	        </div>
	      </div>	      
	    </div>
	  </div>
    
    
<script>
	
	// Tabelle formatieren
	$(document).ready(function(){		
		$('#tableLieferanten').DataTable( {
			"columnDefs": [
	            {
	                "targets": [ 0,13,14 ],
	                "visible": false,
	                "searchable": false
	            }
	        ],
			"paging": true,
                        "searching": true,
                        "info": true,
			"order": [[ 1, "asc" ]],
	        "pagingType": "simple_numbers",
	        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
	        "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"}		     
	    } );
	    
	    
	    var table1 = $('#tableLieferanten').DataTable();
 
	    $('#tableLieferanten tbody').on( 'click', 'tr', function () {
	        if ( $(this).hasClass('info') ) {

                    }
                    else {
                        table1.$('tr.info').removeClass('info');
                        $(this).addClass('info');	
                        ansprechID = table1.row( $(this) ).data()[0];                        
                        document.getElementById("lieferantenName").value = table1.row( $(this) ).data()[1];
                        document.getElementById("lieferantenVorname").value = table1.row( $(this) ).data()[2];
                        document.getElementById("lieferantenTel").value = table1.row( $(this) ).data()[3];
                        document.getElementById("lieferantenAdresse").value = table1.row( $(this) ).data()[5];
                        document.getElementById("lieferantenPLZ").value = table1.row( $(this) ).data()[6];
                        document.getElementById("lieferantenOrt").value = table1.row( $(this) ).data()[7];
                        document.getElementById("lieferantenLand").value = table1.row( $(this) ).data()[8];        
                        document.getElementById("lieferantenEmail").value = table1.row( $(this) ).data()[4];  
                        document.getElementById("lieferant").value = table1.row( $(this) ).data()[13]; 
                        document.getElementById("abteilung").value = table1.row( $(this) ).data()[14]; 
                        document.getElementById("lieferantenGebiet").value = table1.row( $(this) ).data()[11]; 
                    }
	    } );
		

	});
	
	$("#addLieferantenKontakt").click(function(){	    
		 var Name= $("#lieferantenName").val();
		 var Vorname= $("#lieferantenVorname").val();
		 var Tel= $("#lieferantenTel").val();
		 var Adresse= $("#lieferantenAdresse").val();
		 var PLZ= $("#lieferantenPLZ").val();
		 var Ort= $("#lieferantenOrt").val();
		 var Land=  $("#lieferantenLand").val();
		 var Email=  $("#lieferantenEmail").val();
		 var lieferant=  $("#lieferant").val();
		 var abteilung =  $("#abteilung").val();
                 var gebiet =  $("#lieferantenGebiet").val();
	 	
	 	 if(Name.length > 0 && Vorname.length > 0 && Tel.length > 0){
	 	 	 $('#addContactModal').modal('hide');
                    $.ajax({
		        url : "addLieferant.php",
		        data:{"Name":Name,"Vorname":Vorname,"Tel":Tel,"Adresse":Adresse,"PLZ":PLZ,"Ort":Ort,"Land":Land,"Email":Email,"lieferant":lieferant,"abteilung":abteilung,"gebiet":gebiet},
		        type: "GET",
		        success: function(data){
                            alert(data);                                
                            $.ajax({
                                url : "getLieferantenPersonen.php",
                                type: "GET",
                                success: function(data){
                                    $("#lieferanten").html(data); 
                                   
                                } 
                            });
                            
		        } 
		    });		    		   
	 	 }
	 	 else{
                    alert("Bitte überprüfen Sie Ihre Angaben! Name, Vorname und Tel ist Pflicht!");
	 	 }	 
        });
        
        $("#saveLieferantenKontakt").click(function(){	    
		 var Name= $("#lieferantenName").val();
		 var Vorname= $("#lieferantenVorname").val();
		 var Tel= $("#lieferantenTel").val();
		 var Adresse= $("#lieferantenAdresse").val();
		 var PLZ= $("#lieferantenPLZ").val();
		 var Ort= $("#lieferantenOrt").val();
		 var Land=  $("#lieferantenLand").val();
		 var Email=  $("#lieferantenEmail").val();
		 var lieferant=  $("#lieferant").val();
		 var abteilung =  $("#abteilung").val();
                 var gebiet =  $("#lieferantenGebiet").val();
	 	
	 	 if(Name.length > 0 && Vorname.length > 0 && Tel.length > 0){
	 	 	$('#addContactModal').modal('hide');
                    $.ajax({
		        url : "saveLieferantenKontakt.php",
		        data:{"ansprechID":ansprechID,"Name":Name,"Vorname":Vorname,"Tel":Tel,"Adresse":Adresse,"PLZ":PLZ,"Ort":Ort,"Land":Land,"Email":Email,"lieferant":lieferant,"abteilung":abteilung,"gebiet":gebiet},
		        type: "GET",
		        success: function(data){
                            alert(data);                                
                            $.ajax({
                                url : "getLieferantenPersonen.php",
                                type: "GET",
                                success: function(data){
                                    $("#lieferanten").html(data); 
                                    
                                } 
                            });
                            
		        } 
		    });		    		   
	 	 }
	 	 else{
                    alert("Bitte überprüfen Sie Ihre Angaben! Name, Vorname und Tel ist Pflicht!");
	 	 }	 
        });
        
        $("#addContactModalButton").click(function(){	    
            document.getElementById("lieferantenName").value = "";
            document.getElementById("lieferantenVorname").value = "";
            document.getElementById("lieferantenTel").value = "";
            document.getElementById("lieferantenAdresse").value = "";
            document.getElementById("lieferantenPLZ").value = "";
            document.getElementById("lieferantenOrt").value = "";
            document.getElementById("lieferantenLand").value = "";        
            document.getElementById("lieferantenEmail").value = "";  
            document.getElementById("lieferantenGebiet").value = "";
            // Buttons ein/ausblenden!
            document.getElementById("saveLieferantenKontakt").style.display = "none";
            document.getElementById("addLieferantenKontakt").style.display = "inline";
        });
        
        $("button[value='changeContact']").click(function(){	    
            // Buttons ein/ausblenden!
            document.getElementById("addLieferantenKontakt").style.display = "none";
            document.getElementById("saveLieferantenKontakt").style.display = "inline";
        });
			
</script>


</body>
</html>