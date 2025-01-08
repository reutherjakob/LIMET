<?php

session_start();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
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
	
	
	// Abfrage der externen Lose
	$sql = "SELECT `tabelle_lose_extern`.`idtabelle_Lose_Extern`,
			    `tabelle_lose_extern`.`LosNr_Extern`,
			    `tabelle_lose_extern`.`LosBezeichnung_Extern`,
			    `tabelle_lose_extern`.`Ausführungsbeginn`
			FROM `LIMET_RB`.`tabelle_lose_extern`
			WHERE `tabelle_lose_extern`.`tabelle_projekte_idTABELLE_Projekte`=".$_SESSION["projectID"]."
			ORDER BY `tabelle_lose_extern`.`LosNr_Extern`;";
	
	$result = $mysqli->query($sql);
				
	$lotsInProject = array();
	while ($row = $result->fetch_assoc()) {
	    $lotsInProject[$row['idtabelle_Lose_Extern']]['LosNr_Extern'] = $row['LosNr_Extern'];
	    $lotsInProject[$row['idtabelle_Lose_Extern']]['idtabelle_Lose_Extern'] = $row['idtabelle_Lose_Extern'];
	    $lotsInProject[$row['idtabelle_Lose_Extern']]['LosBezeichnung_Extern'] = $row['LosBezeichnung_Extern'];
	}
	
/*	
	foreach($combinedResults as $array)
	{       
	   echo $array['idtabelle_Lose_Extern'].'<br />';
	   echo $array['LosNr_Extern'].'<br />';
	}
*/		
    $raumbereich = urldecode($_GET["raumbereich"]);

    if($_GET["losID"] != ""){
    	if($raumbereich != ""){
            $sql = "SELECT tabelle_räume_has_tabelle_elemente.id, tabelle_räume.idTABELLE_Räume, tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung, tabelle_räume.`Raumbereich Nutzer`, tabelle_räume_has_tabelle_elemente.Anzahl, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, tabelle_räume_has_tabelle_elemente.Standort, tabelle_räume_has_tabelle_elemente.Verwendung, tabelle_räume_has_tabelle_elemente.Kurzbeschreibung, tabelle_lose_extern.LosNr_Extern, tabelle_varianten.Variante, tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern
                            FROM tabelle_varianten INNER JOIN (tabelle_hersteller RIGHT JOIN (tabelle_geraete RIGHT JOIN (tabelle_lose_extern RIGHT JOIN (tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) ON tabelle_lose_extern.idtabelle_Lose_Extern = tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern) ON tabelle_geraete.idTABELLE_Geraete = tabelle_räume_has_tabelle_elemente.TABELLE_Geraete_idTABELLE_Geraete) ON tabelle_hersteller.idtabelle_hersteller = tabelle_geraete.tabelle_hersteller_idtabelle_hersteller) ON tabelle_varianten.idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten
                            WHERE ( ((tabelle_räume.`Raumbereich Nutzer`)='".$raumbereich."') AND ((tabelle_räume_has_tabelle_elemente.`Neu/Bestand`)=".$_GET["bestand"].") AND ((tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern)=".$_GET["losID"].") AND ((tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten)=".$_GET["variantenID"].") AND ((tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente)=".$_GET["elementID"].") AND ((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"]."))
                            ORDER BY tabelle_räume.Raumnr;";
            }
            else{
                    $sql = "SELECT tabelle_räume_has_tabelle_elemente.id, tabelle_räume.idTABELLE_Räume, tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung, tabelle_räume.`Raumbereich Nutzer`, tabelle_räume_has_tabelle_elemente.Anzahl, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, tabelle_räume_has_tabelle_elemente.Standort, tabelle_räume_has_tabelle_elemente.Verwendung, tabelle_räume_has_tabelle_elemente.Kurzbeschreibung, tabelle_lose_extern.LosNr_Extern, tabelle_varianten.Variante, tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern
                            FROM tabelle_varianten INNER JOIN (tabelle_hersteller RIGHT JOIN (tabelle_geraete RIGHT JOIN (tabelle_lose_extern RIGHT JOIN (tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) ON tabelle_lose_extern.idtabelle_Lose_Extern = tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern) ON tabelle_geraete.idTABELLE_Geraete = tabelle_räume_has_tabelle_elemente.TABELLE_Geraete_idTABELLE_Geraete) ON tabelle_hersteller.idtabelle_hersteller = tabelle_geraete.tabelle_hersteller_idtabelle_hersteller) ON tabelle_varianten.idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten
                            WHERE ( ((tabelle_räume_has_tabelle_elemente.`Neu/Bestand`)=".$_GET["bestand"].") AND ((tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern)=".$_GET["losID"].") AND ((tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten)=".$_GET["variantenID"].") AND ((tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente)=".$_GET["elementID"].") AND ((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"]."))
                            ORDER BY tabelle_räume.Raumnr;";
            }
	}
	else{
            if($raumbereich != ""){
                    $sql = "SELECT tabelle_räume_has_tabelle_elemente.id, tabelle_räume.idTABELLE_Räume, tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung, tabelle_räume.`Raumbereich Nutzer`, tabelle_räume_has_tabelle_elemente.Anzahl, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, tabelle_räume_has_tabelle_elemente.Standort, tabelle_räume_has_tabelle_elemente.Verwendung, tabelle_räume_has_tabelle_elemente.Kurzbeschreibung, tabelle_lose_extern.LosNr_Extern, tabelle_varianten.Variante, tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern
                                    FROM tabelle_varianten INNER JOIN (tabelle_hersteller RIGHT JOIN (tabelle_geraete RIGHT JOIN (tabelle_lose_extern RIGHT JOIN (tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) ON tabelle_lose_extern.idtabelle_Lose_Extern = tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern) ON tabelle_geraete.idTABELLE_Geraete = tabelle_räume_has_tabelle_elemente.TABELLE_Geraete_idTABELLE_Geraete) ON tabelle_hersteller.idtabelle_hersteller = tabelle_geraete.tabelle_hersteller_idtabelle_hersteller) ON tabelle_varianten.idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten
                                    WHERE ( ((tabelle_räume.`Raumbereich Nutzer`)='".$raumbereich."') AND ((tabelle_räume_has_tabelle_elemente.`Neu/Bestand`)=".$_GET["bestand"].") AND ((tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern) IS NULL) AND ((tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten)=".$_GET["variantenID"].") AND ((tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente)=".$_GET["elementID"].") AND ((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"]."))
                                    ORDER BY tabelle_räume.Raumnr;";
            }
            else{
                    $sql = "SELECT tabelle_räume_has_tabelle_elemente.id, tabelle_räume.idTABELLE_Räume, tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung, tabelle_räume.`Raumbereich Nutzer`, tabelle_räume_has_tabelle_elemente.Anzahl, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, tabelle_räume_has_tabelle_elemente.Standort, tabelle_räume_has_tabelle_elemente.Verwendung, tabelle_räume_has_tabelle_elemente.Kurzbeschreibung, tabelle_lose_extern.LosNr_Extern, tabelle_varianten.Variante, tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern
                                    FROM tabelle_varianten INNER JOIN (tabelle_hersteller RIGHT JOIN (tabelle_geraete RIGHT JOIN (tabelle_lose_extern RIGHT JOIN (tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) ON tabelle_lose_extern.idtabelle_Lose_Extern = tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern) ON tabelle_geraete.idTABELLE_Geraete = tabelle_räume_has_tabelle_elemente.TABELLE_Geraete_idTABELLE_Geraete) ON tabelle_hersteller.idtabelle_hersteller = tabelle_geraete.tabelle_hersteller_idtabelle_hersteller) ON tabelle_varianten.idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten
                                    WHERE ( ((tabelle_räume_has_tabelle_elemente.`Neu/Bestand`)=".$_GET["bestand"].") AND ((tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern) IS NULL) AND ((tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten)=".$_GET["variantenID"].") AND ((tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente)=".$_GET["elementID"].") AND ((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"]."))
                                    ORDER BY tabelle_räume.Raumnr;";

            }
	}

    
	$result = $mysqli->query($sql);
	
	echo "<table class='table table-striped table-bordered table-sm' id='tableRoomsWithElementTenderLots' cellspacing='0' width='100%'>
	<thead><tr>
	<th>ID</th>
        <th></th>
	<th>Anzahl</th>
	<th>Raumnummer</th>
	<th>Raumbezeichnung</th>
	<th>Raumbereich</th>
	<th>Best</th>
	<th>Stand</th>
	<th>Verw</th>
	<th>Komm</th>
	<th>LosNr</th>
	<th></th>
	</tr></thead><tbody>";
	

	
	while($row = $result->fetch_assoc()) {
		
	    echo "<tr>";
	    echo "<td>".$row["id"]."</td>";
            echo "<td></td>";
	    echo "<td><input type='text' id='amount".$row["id"]."' value='".$row["Anzahl"]."' size='4'></input></td>"; 	    
	    echo "<td>".$row["Raumnr"]."</td>";
	    echo "<td>".$row["Raumbezeichnung"]."</td>";
	    echo "<td>".$row["Raumbereich Nutzer"]."</td>";
		echo "<td>
	    	<select class='form-control form-control-sm' id='bestand".$row["id"]."'>";
					if($row["Neu/Bestand"] == "0"){
				  		echo "<option value=0 selected>Ja</option>";
				  		echo "<option value=1>Nein</option>";
					}
					else{
						echo "<option value=0>Ja</option>";
				  		echo "<option value=1 selected>Nein</option>";
					}
		  	echo "</select></td>";
   	    echo "<td>   	
                <select class='form-control form-control-sm' id='Standort".$row["id"]."'>";
					if($row["Standort"] == "0"){
				  		echo "<option value=0 selected>Nein</option>";
				  		echo "<option value=1>Ja</option>";
					}
					else{
						echo "<option value=0>Nein</option>";
				  		echo "<option value=1 selected>Ja</option>";
					}
		  	echo "</select></td>";
	    echo "<td>   	    	
                        <select class='form-control form-control-sm' id='Verwendung".$row["id"]."'>";
                                    if($row["Verwendung"] == "0"){
                                            echo "<option value=0 selected>Nein</option>";
                                            echo "<option value=1>Ja</option>";
                                    }
                                    else{
                                            echo "<option value=0>Nein</option>";
                                            echo "<option value=1 selected>Ja</option>";
                                    }
            echo "</select></td>";
		echo "<td><textarea id='comment".$row["id"]."' rows='1' style='width: 100%;'>".$row["Kurzbeschreibung"]."</textarea></td>";
		/*	
    	echo "<td>";
    		echo "<button type='button' id='".$row["id"]."' class='btn btn-default btn-sm' value='openComment' >Kommentar <span class='glyphicon glyphicon-list-alt'></span></button>";
	    echo "</td>";
	    */
	    echo "<td>
	    	<select class='form-control form-control-sm' id='losExtern".$row["id"]."'>";
					if($row["tabelle_Lose_Extern_idtabelle_Lose_Extern"] != ""){						
				  		echo "<option value=0>Los wählen</option>";
				  		foreach($lotsInProject as $array) {
				  			if($array['idtabelle_Lose_Extern'] == $row["tabelle_Lose_Extern_idtabelle_Lose_Extern"]){
								echo "<option selected value=".$array['idtabelle_Lose_Extern'].">".$array['LosNr_Extern']." - ".$array['LosBezeichnung_Extern']."</option>";
							}
							else{
								echo "<option value=".$array['idtabelle_Lose_Extern'].">".$array['LosNr_Extern']." - ".$array['LosBezeichnung_Extern']."</option>";
							}		
						}
					}
					else{
						echo "<option value=0 selected>Los wählen</option>";
				  		foreach($lotsInProject as $array) {
							echo "<option value=".$array['idtabelle_Lose_Extern'].">".$array['LosNr_Extern']." - ".$array['LosBezeichnung_Extern']."</option>";									
						}
					}
		  	echo "</select></td>";
	    	  
	    echo "<td><button type='button' id='".$row["id"]."' class='btn btn-warning btn-sm' value='saveElement'><i class='far fa-save'></i></button></td>";
		echo "</tr>";
	    
	}
	
	echo "</tbody></table>";
	
	$mysqli ->close();
	
	echo "<!-- Modal zum Anzeigen bzw Speichern des Kommentars -->
	  <div class='modal fade' id='commentModal' role='dialog'>
	    <div class='modal-dialog modal-md'>
	    
	      <!-- Modal content-->
	      <div class='modal-content'>
	        <div class='modal-header'>
	          <button type='button' class='close' data-dismiss='modal'>&times;</button>
	          <h4 class='modal-title'>Kommentar</h4>
	        </div>
	        <div class='modal-body' id='mbody'>
		        <form role='form'>
		    	  <div class='form-group'>		    	  	
					<textarea class='form-control' rows='5' id='modalKurzbeschreibung'></textarea>
				  </div>	        	
				</form>
	        </div>
	        <div class='modal-footer'>
	        	<button type='button' class='btn btn-warning btn-sm' value='saveComment'>Speichern<span class='glyphicon glyphicon-floppy-disk'></span></button>
	        </div>
	      </div>
	      
	    </div>
	  </div>";

		
?>
<script>
  	
	$(document).ready(function() {
		 $('#tableRoomsWithElementTenderLots').DataTable( {
			"paging": true,
			"columnDefs": [
                            {
                                "targets": [ 0 ],
                                "visible": false,
                                "searchable": false
                            },
                            {
                                className: 'control',
                                orderable: false,
                                targets:   1
                            },
                            {
                                "targets": [ 2,6,7,8,9,11 ],
                                "visible": true,
                                "searchable": false,
                                "sortable":false
                            }
                        ],
                        "responsive": {
                            details: {
                                type: 'column',
                                target: 1
                            }
                        },
			"searching": true,
			"info": true,
			"order": [[ 3, "asc" ]],
                        "pagingType": "simple",
                        "lengthChange": false,
                        "pageLength": 10,
                        "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"}
	    } ); 
	    
	    var table = $('#tableRoomsWithElementTenderLots').DataTable();
 
	    $('#tableRoomsWithElementTenderLots tbody').on( 'click', 'tr', function () {
	        if ( $(this).hasClass('info') ) {
	            //$(this).removeClass('info');
	        }
	        else {
	            table.$('tr.info').removeClass('info');
	            $(this).addClass('info');
	            var id = table.row( $(this) ).data()[0];	            	            	
                    var stk = $("#amount"+id).val();
	            $.ajax({
			        url : "getElementBestand.php",
			        data:{"id":id,"stk":stk},
			        type: "GET",
			        success: function(data){
			            $("#elementBestand").html(data);
			            $("#elementBestand").show();
			        } 
		        });
	        }
	    } ); 

	    		
	} );
	
	//Kommentar anzeigen 
	$("button[value='openComment']").click(function(){
	    var ID = this.id;

		$.ajax({
	        url : "getComment.php",
	        type: "GET",
	        data:{"commentID":ID},
	        success: function(data){
	            $("#modalKurzbeschreibung").html(data);	
	            $('#commentModal').modal('show');             			 			
	        } 
        });		     
    });
    
    //Eintrag speichern 
	$("button[value='saveElement']").click(function(){
		var ID = this.id;
		var amount = $("#amount"+ID).val();
		var bestand = $("#bestand"+ID).val();
		var losExtern = $("#losExtern"+ID).val();
		var comment = $("#comment"+ID).val();
                var standort = $("#Standort"+ID).val();
                var verwendung = $("#Verwendung"+ID).val();
                
                if(standort === '0' && verwendung === '0'){
                    alert("Standort und Verwendung kann nicht Nein sein!");
                }
                else{
                    $.ajax({
                        url : "saveRoombookTender.php",
                        type: "GET",
                        data:{"amount":amount,"bestand":bestand ,"losExtern":losExtern,"roombookID":ID,"comment":comment,"standort":standort,"verwendung":verwendung},
                        success: function(data){
                            alert(data);
                            
                            /* NEU LADEN DER Hauptseite
                            var searchVal = $('div.dataTables_filter input').val();
                            $.ajax({
			        url : "getElementLots2.php",
			        data:{"searchValue":searchVal},
			        type: "GET",
			        success: function(data){
			            $("#elementLots").html(data);			            
			        } 
                            });
                            */
                        } 
                    });
                }
        });		     
        
    
    //Kommentar speichern
	$("button[value='saveComment']").click(function(){
		var comment = $("#modalKurzbeschreibung").val();
		alert(comment);
		$.ajax({
	        url : "saveRoombookComment.php",
	        type: "GET",
	        data:{"comment":comment},
	        success: function(data){
	            alert(data);	
	            $('#commentModal').modal('hide');            			 			
	        } 
        });		     
    });

</script>

</body>
</html>