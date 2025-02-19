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
			
	
	
	echo "<table class='table table-striped table-condensed' id='tablePossibleRoomBOs' cellspacing='0'>
	<thead><tr>
	<th></th>
	<th>Gruppe
		<select class='form-control input-sm' id='filter_bo'>
			<option selected value='0'></option>";
			
			$sql="SELECT tabelle_BO_Hauptgruppe.Bezeichnung As HGBezeichnung, tabelle_BO_Hauptgruppe.Nummmer, tabelle_BO_Untergruppe.idtabelle_BO_Untergruppe, tabelle_BO_Untergruppe.Bezeichnung As UGBezeichnung, tabelle_BO_Untergruppe.Nummer
					FROM tabelle_BO_Hauptgruppe INNER JOIN tabelle_BO_Untergruppe ON tabelle_BO_Hauptgruppe.idtabelle_BO_Hauptgruppe = tabelle_BO_Untergruppe.tabelle_BO_Hauptgruppe_idtabelle_BO_Hauptgruppe 
					ORDER BY tabelle_BO_Hauptgruppe.Nummmer, tabelle_BO_Untergruppe.Nummer;";
			
			$result = $mysqli->query($sql);
			
			while($row = $result->fetch_assoc()) {
				if($_GET['filterValueBO'] == $row["idtabelle_BO_Untergruppe"]){
					echo "<option selected value=".$row["idtabelle_BO_Untergruppe"].">".$row["Nummer"]." - ".$row["HGBezeichnung"].".".$row["UGBezeichnung"]."</option>";	
				}
				else{
					echo "<option value=".$row["idtabelle_BO_Untergruppe"].">".$row["Nummer"]." - ".$row["HGBezeichnung"].".".$row["UGBezeichnung"]."</option>";	
				}
			}		
		echo "</select>
	</th>
	<th>Tätigkeit Deutsch</th>
	<th>Tätigkeit Englisch</th>
	<th></th>
	</tr></thead>
	<tbody>";
	
	/*
		$sql = "SELECT tabelle_BO_Taetigkeiten.idtabelle_BO_Taetigkeiten, tabelle_BO_Taetigkeiten.Taetigkeit_Deutsch, tabelle_BO_Taetigkeiten.Taetigkeit_Englisch, tabelle_BO_Untergruppe.idtabelle_BO_Untergruppe, tabelle_BO_Untergruppe.Bezeichnung As UGBezeichnung, tabelle_BO_Untergruppe.Nummer, tabelle_BO_Hauptgruppe.idtabelle_BO_Hauptgruppe, tabelle_BO_Hauptgruppe.Bezeichnung As HGBezeichnung, tabelle_BO_Hauptgruppe.Nummmer
				FROM tabelle_BO_Hauptgruppe INNER JOIN (tabelle_BO_Taetigkeiten INNER JOIN tabelle_BO_Untergruppe ON tabelle_BO_Taetigkeiten.tabelle_BO_Untergruppe_idtabelle_BO_Untergruppe = tabelle_BO_Untergruppe.idtabelle_BO_Untergruppe) ON tabelle_BO_Hauptgruppe.idtabelle_BO_Hauptgruppe = tabelle_BO_Untergruppe.tabelle_BO_Hauptgruppe_idtabelle_BO_Hauptgruppe	
				WHERE `tabelle_BO_Taetigkeiten`.`idtabelle_BO_Taetigkeiten` NOT IN 
				(SELECT tabelle_BO_Taetigkeiten.idtabelle_BO_Taetigkeiten
							FROM tabelle_BO_Taetigkeiten INNER JOIN tabelle_BO_Taetigkeiten_has_tabelle_räume ON tabelle_BO_Taetigkeiten.idtabelle_BO_Taetigkeiten = tabelle_BO_Taetigkeiten_has_tabelle_räume.tabelle_BO_Taetigkeiten_idtabelle_BO_Taetigkeiten
							WHERE (((tabelle_BO_Taetigkeiten_has_tabelle_räume.tabelle_räume_idTABELLE_Räume)=".$_SESSION["roomID"].")));";
	*/
	
	if($_GET['filterValueBO'] == 0){
		$sql = "SELECT tabelle_BO_Taetigkeiten.idtabelle_BO_Taetigkeiten, tabelle_BO_Taetigkeiten.Taetigkeit_Deutsch, tabelle_BO_Taetigkeiten.Taetigkeit_Englisch, tabelle_BO_Untergruppe.idtabelle_BO_Untergruppe, tabelle_BO_Untergruppe.Bezeichnung As UGBezeichnung, tabelle_BO_Untergruppe.Nummer, tabelle_BO_Hauptgruppe.idtabelle_BO_Hauptgruppe, tabelle_BO_Hauptgruppe.Bezeichnung As HGBezeichnung, tabelle_BO_Hauptgruppe.Nummmer
				FROM tabelle_BO_Hauptgruppe INNER JOIN (tabelle_BO_Taetigkeiten INNER JOIN tabelle_BO_Untergruppe ON tabelle_BO_Taetigkeiten.tabelle_BO_Untergruppe_idtabelle_BO_Untergruppe = tabelle_BO_Untergruppe.idtabelle_BO_Untergruppe) ON tabelle_BO_Hauptgruppe.idtabelle_BO_Hauptgruppe = tabelle_BO_Untergruppe.tabelle_BO_Hauptgruppe_idtabelle_BO_Hauptgruppe	
				WHERE `tabelle_BO_Taetigkeiten`.`idtabelle_BO_Taetigkeiten` NOT IN 
				(SELECT tabelle_BO_Taetigkeiten.idtabelle_BO_Taetigkeiten
							FROM tabelle_BO_Taetigkeiten INNER JOIN tabelle_BO_Taetigkeiten_has_tabelle_räume ON tabelle_BO_Taetigkeiten.idtabelle_BO_Taetigkeiten = tabelle_BO_Taetigkeiten_has_tabelle_räume.tabelle_BO_Taetigkeiten_idtabelle_BO_Taetigkeiten
							WHERE (((tabelle_BO_Taetigkeiten_has_tabelle_räume.tabelle_räume_idTABELLE_Räume)=".$_SESSION["roomID"].")));";
	}
	else{
		$sql = "SELECT tabelle_BO_Taetigkeiten.idtabelle_BO_Taetigkeiten, tabelle_BO_Taetigkeiten.Taetigkeit_Deutsch, tabelle_BO_Taetigkeiten.Taetigkeit_Englisch, tabelle_BO_Untergruppe.idtabelle_BO_Untergruppe, tabelle_BO_Untergruppe.Bezeichnung As UGBezeichnung, tabelle_BO_Untergruppe.Nummer, tabelle_BO_Hauptgruppe.idtabelle_BO_Hauptgruppe, tabelle_BO_Hauptgruppe.Bezeichnung As HGBezeichnung, tabelle_BO_Hauptgruppe.Nummmer
				FROM tabelle_BO_Hauptgruppe INNER JOIN (tabelle_BO_Taetigkeiten INNER JOIN tabelle_BO_Untergruppe ON tabelle_BO_Taetigkeiten.tabelle_BO_Untergruppe_idtabelle_BO_Untergruppe = tabelle_BO_Untergruppe.idtabelle_BO_Untergruppe) ON tabelle_BO_Hauptgruppe.idtabelle_BO_Hauptgruppe = tabelle_BO_Untergruppe.tabelle_BO_Hauptgruppe_idtabelle_BO_Hauptgruppe	
				WHERE tabelle_BO_Untergruppe.idtabelle_BO_Untergruppe= ".$_GET["filterValueBO"]." AND `tabelle_BO_Taetigkeiten`.`idtabelle_BO_Taetigkeiten` NOT IN 
				(SELECT tabelle_BO_Taetigkeiten.idtabelle_BO_Taetigkeiten
							FROM tabelle_BO_Taetigkeiten INNER JOIN tabelle_BO_Taetigkeiten_has_tabelle_räume ON tabelle_BO_Taetigkeiten.idtabelle_BO_Taetigkeiten = tabelle_BO_Taetigkeiten_has_tabelle_räume.tabelle_BO_Taetigkeiten_idtabelle_BO_Taetigkeiten
							WHERE (((tabelle_BO_Taetigkeiten_has_tabelle_räume.tabelle_räume_idTABELLE_Räume)=".$_SESSION["roomID"].")));";

	}

  
	$result = $mysqli->query($sql);
	
	while($row = $result->fetch_assoc()) {
	    echo "<tr>";
	    echo "<td><button type='button' id='".$row["idtabelle_BO_Taetigkeiten"]."' class='btn btn-success btn-sm' value='addBO'><span class='glyphicon glyphicon-plus'></span></button></td>";
	    echo "<td>".$row["Nummer"]." - ".$row["HGBezeichnung"].".".$row["UGBezeichnung"]."</td>";
	    echo "<td><input type='text' id='taetigkeit_deutsch".$row["idtabelle_BO_Taetigkeiten"]."' value='".$row["Taetigkeit_Deutsch"]."' size='50'></input></td>";
		echo "<td><input type='text' id='taetigkeit_englisch".$row["idtabelle_BO_Taetigkeiten"]."' value='".$row["Taetigkeit_Englisch"]."' size='50'></input></td>";
		echo "<td><button type='button' id='".$row["idtabelle_BO_Taetigkeiten"]."' class='btn btn-default btn-sm' value='saveBO'><span class='glyphicon glyphicon-floppy-disk'></span></button></td>";
	    echo "</tr>";
	}
	
	echo "</tbody></table>";
	
	$mysqli ->close();
	?>
	
<script>
    
	    
    $(document).ready(function() {
	    $("#tablePossibleRoomBOs").DataTable( {
			"paging": false,
			"searching": true,
			"info": false,
			"order": [[ 1, "asc" ]],
	        //"pagingType": "simple_numbers",
	        //"lengthMenu": [[5,10, 25, 50, -1], [5,10, 25, 50, "All"]],
	        "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"},
	        "scrollY":        '20vh',
	    	"scrollCollapse": true,
	    	"columnDefs": [
        		{ "targets": 0, "visible": true, "searchable": false  },
	            { "targets": 1, "orderDataType": "dom-text", "type": "string" },
	            { "targets": 2, "orderDataType": "dom-text", "type": "string" },
	            {
	                "targets": [ 3 ],
	                "visible": false,
	                "searchable": false
	            }
	        ]   		     
	    } );

	    var table = $('#tablePossibleRoomBOs').DataTable();
 
	    $('#tablePossibleRoomBOs tbody').on( 'click', 'tr', function () {
	        if ( $(this).hasClass('info') ) {
	            
	        }
	        else {
	            table.$('tr.info').removeClass('info');
	            $(this).addClass('info');	           
	        }
	    } );    	
	} );

	$("button[value='addBO']").click(function(){
	    var id=this.id; 
	    var filterBO = $('#filter_bo').val();
	    if(id !== ""){			 
	        $.ajax({
		        url : "addRoomBO.php",
		        data:{"boID":id},
		        type: "GET",
		        success: function(data){
		        	alert(data);
		        	$.ajax({
				        url : "getRoomBOs.php",
				        type: "GET",
				        success: function(data){
				        	$("#roomBOs").html(data);
				        	$.ajax({
						        url : "getPossibleRoomBOs.php",
						        type: "GET",
						        data:{"filterValueBO":filterBO},
						        success: function(data){
						        	$("#possibleRoomBOs").html(data);
						        } 
					        }); 

				        } 
			        }); 

		        } 
	        }); 
	    }

    });
    
    $("button[value='saveBO']").click(function(){
	    var id=this.id; 
	    var bo_deutsch= $("#taetigkeit_deutsch"+id).val();
    	var bo_englisch= $("#taetigkeit_englisch"+id).val();

	    if(id !== ""){
	    	$.ajax({
		        url : "updateBO.php",
		        data:{"boID":id,"bo_deutsch":bo_deutsch,"bo_englisch":bo_englisch},
		        type: "GET",
		        success: function(data){
		        	alert(data);
		        }
		    });		    
	    }
	});

	 //Filter BO Gruppe notwendig geändert
	$('#filter_bo').change(function(){
		var filterBO = $('#filter_bo').val();
	    $.ajax({
	        url : "getPossibleRoomBOsFiltered.php",
	        data:{"filterValueBO":filterBO},
	        type: "GET",
	        success: function(data){
		            $("#possibleRoomBOs").html(data);		            
			}
	    });
	});

</script>

</body>
</html>