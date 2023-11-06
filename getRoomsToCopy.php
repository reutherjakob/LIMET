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
	
	//Elemente im Raum abfragen
	$sql = "SELECT tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung, tabelle_räume.`Raumbereich Nutzer`, tabelle_räume.Nutzfläche, tabelle_räume.Geschoss, tabelle_räume.Bauetappe, tabelle_räume.Bauabschnitt, tabelle_räume.idTABELLE_Räume
										FROM tabelle_räume INNER JOIN tabelle_projekte ON tabelle_räume.tabelle_projekte_idTABELLE_Projekte = tabelle_projekte.idTABELLE_Projekte
										WHERE ((Not (tabelle_räume.idTABELLE_Räume)=".$_GET["id"].") AND ((tabelle_projekte.idTABELLE_Projekte)=".$_SESSION["projectID"]."));";    
	$result = $mysqli->query($sql);
	
	echo "<table class='table table-striped table-bordered table-sm' id='tableRoomsToCopy' cellspacing='0' width='100%'>
	<thead><tr>
	<th>ID</th>
	<th>Raumnr</th>
	<th>Raumbezeichnung</th>
	<th>Nutzfläche</th>
	<th>Raumbereich Nutzer</th>
	<th>Geschoss</th>
	<th>Bauetappe</th>
	<th>Bauteil</th>
	</tr></thead>
	<tbody>";
	
	
	
	while($row = $result->fetch_assoc()) {
	     echo "<tr>";
	     echo "<td >".$row["idTABELLE_Räume"]."</td>";
	     echo "<td >".$row["Raumnr"]."</td>";
	    echo "<td >".$row["Raumbezeichnung"]."</td>";
	    echo "<td >".$row["Nutzfläche"]."</td>";
	    echo "<td >".$row["Raumbereich Nutzer"]."</td>";
	    echo "<td >".$row["Geschoss"]."</td>";
	    echo "<td >".$row["Bauetappe"]."</td>";
	    echo "<td >".$row["Bauabschnitt"]."</td>";
	    echo "</tr>";
	    
	}
	echo "</tbody></table>";
	$mysqli ->close();
	?>
	
	
	
	
<script>
	
	//RaumIDs zum kopieren speichern
	var roomIDs = [];
   
    $(document).ready(function(){ 
  		
	   $("#tableRoomsToCopy").DataTable( {
                "select": {
                  "style": "multi"
                },
                "columnDefs": [
                    { 
                        "targets": [ 0 ], 
                        "visible": false, 
                        "searchable": false  
                    }
                ],
                "paging": false,
                "searching": true,
                "info": false,
                "order": [[ 1, "asc" ]],
                //"pagingType": "simple_numbers",
                //"lengthMenu": [[5,10, 25, 50, -1], [5,10, 25, 50, "All"]],
                "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"},
                "scrollY":        '50vh',
                "scrollCollapse": true 			
	    } );
	    
	    var table = $('#tableRoomsToCopy').DataTable();
             $('#tableRoomsToCopy tbody').on( 'click', 'tr', function () {
                $(this).toggleClass('selected');
            } );
            
	    $('#tableRoomsToCopy tbody').on( 'click', 'tr', function () {
	        $(this).toggleClass('selected');
                if ( $(this).hasClass('info') ) {
                    
	            $(this).removeClass('info');	            
	            for(var i = roomIDs.length - 1; i >= 0; i--) {
                        if(roomIDs[i] === table.row( $(this) ).data()[0]) {
                           roomIDs.splice(i, 1);
                        }
                    }	            
	        }
	        else {
	            //table.$('tr.info').removeClass('info');
                    
	            $(this).addClass('info');
	            roomIDs.push(table.row( $(this) ).data()[0]);	            
	        }
	    } );
	    
	    
	     

	 } ); 
	 
	 //Bauangaben kopieren
	$("input[value='Bauangaben kopieren']").click(function(){
	    if(roomIDs.length === 0){
	    	alert("Kein Raum ausgewählt!");
	    }
	    else{
		    $.ajax({
		    	//$('#myLoadingModal').modal('show');
		        url : "copyRoomSpecifications.php",
		        type: "GET",
		        data:{"rooms":roomIDs},
		        success: function(data){
		        	//$('#myLoadingModal').modal('hide');
		            alert(data);	            			 			
		        } 
	        });	
        }
    });
    
    //Bauangaben kopieren
	$("input[value='BO-Angaben kopieren']").click(function(){
	    if(roomIDs.length === 0){
	    	alert("Kein Raum ausgewählt!");
	    }
	    else{
		    //$.ajax({
		    	//$('#myLoadingModal').modal('show');
		    	/*
		        url : "copyRoomBOs.php",
		        type: "GET",
		        data:{"rooms":roomIDs},
		        success: function(data){
		        	//$('#myLoadingModal').modal('hide');
		            alert(data);	            			 			
		        } 
		        */
	        //});	
        }
    });
    
    //Rauminhalt kopieren
    $("input[value='Elemente kopieren']").click(function(){
        if(roomIDs.length === 0){
            alert("Kein Raum ausgewählt!");
        }
        else{
            $.ajax({
                url : "copyRoomElements.php",
                type: "GET",
                data:{"rooms":roomIDs},
                success: function(data){
                    alert(data);	            			 			
                } 
            });	
        }
    });

	 


</script>

</body>
</html>