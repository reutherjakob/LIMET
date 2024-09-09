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
        
        $elementID = filter_input(INPUT_GET, 'elementID');
        
        $sql =	"SELECT tabelle_räume.idTABELLE_Räume, tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung, tabelle_räume.`Raumbereich Nutzer`
			FROM tabelle_räume 
                        WHERE (((tabelle_räume.idTABELLE_Räume) Not In 
                        (SELECT tabelle_räume.idTABELLE_Räume FROM (tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) 
                        WHERE (((tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente)=".$elementID.") AND ((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"]."))))
                        AND ((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"]."))
			ORDER BY tabelle_räume.Raumnr;";
    
	$result = $mysqli->query($sql);
	
	echo "<button type='button' class='btn btn-outline-success btn-xs mb-2' id='addElements' data-toggle='modal' data-target='#addElementsToRoomModal'><i class='fas fa-plus'></i></button>
        <table class='table table-striped table-bordered table-sm' id='tableRoomsWithoutElement' cellspacing='0' width='100%'>
	<thead><tr>
        <th>id</th>
	<th>Raumnummer</th>
	<th>Raumbezeichnung</th>
	<th>Raumbereich</th>
	</tr></thead><tbody>";
		
	while($row = $result->fetch_assoc()) {		
	    echo "<tr>";
            echo "<td>".$row["idTABELLE_Räume"]."</td>";
	    echo "<td>".$row["Raumnr"]."</td>";
	    echo "<td>".$row["Raumbezeichnung"]."</td>";
	    echo "<td>".$row["Raumbereich Nutzer"]."</td>";
	    echo "</tr>";
	    
	}	
	echo "</tbody></table>";	
	$mysqli ->close();	
        echo "<!-- Modal zum kopieren der Elemente -->
            <div class='modal fade' id='addElementsToRoomModal' role='dialog'>
              <div class='modal-dialog modal-md'>
                <!-- Modal content-->
                <div class='modal-content'>
                  <div class='modal-header'>                                  
                    <h4 class='modal-title'>Kommentar hinzufügen, Stückzahl angeben</h4>
                    <button type='button' class='close' data-dismiss='modal'>&times;</button>
                  </div>
                  <div class='modal-body' id='mbody'>
                      <label for='amount' class='form-label'>Stück: </label>
                      <input class='form-control form-control-sm' type='number' id='amount' value='1' size='2'></input>
                      <label for='amount' class='form-label'>Kommentar: </label>
                      <textarea class='form-control' id='comment' rows='2'></textarea>
                  </div>
                  <div class='modal-footer'>
                      <input type='button' id='addElementToRooms' class='btn btn-success btn-sm' value='Hinzufügen' data-dismiss='modal'></input>
                      <button type='button' class='btn btn-default btn-sm' data-dismiss='modal'>Schließen</button>
                  </div>
                </div>

              </div>
            </div>";
?>
<script>
    //RaumIDs zum kopieren speichern
    var table;
    var roomIDs = [];
    var elementID = <?php echo $elementID; ?>;
    $(document).ready(function() {
        $('#tableRoomsWithoutElement').DataTable( {
            
            "columnDefs": [
                {
                    "targets": [ 0 ],
                    "visible": false,
                    "searchable": false
                }], 
            "paging": true,
            "searching": true,
            "info": false,
            "order": [[ 1, "asc" ]],
            "lengthMenu": [[5,10, 25, 50, -1], [5,10, 25, 50, "All"]],
            "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"},
            "select": {
                "style": "multi"
            }
        } );
        table = $('#tableRoomsWithoutElement').DataTable();
        
        $('#tableRoomsWithoutElement tbody').on( 'click', 'tr', function () {
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
                $(this).addClass('info');
                roomIDs.push(table.row( $(this) ).data()[0]);	            
            }
        } );
    } );
    
    //Elemente einfügen
    $("#addElementToRooms").click(function(){
        if(roomIDs.length === 0){
            alert("Kein Raum ausgewählt!");
            //$('#addElementsToRoomModal').modal('hide');
        }
        else{
            $.ajax({                
                url : "addElementToMultipleRooms.php",
                type: "GET",
                data:{"elementID":elementID, "rooms":roomIDs, "amount":$("#amount").val(), "comment":$("#comment").val()},
                success: function(data){                        
                    alert(data);
                    //$('#addElementsToRoomModal').modal('hide');
                    $.ajax({
                        url : "getRoomsWithElement.php",
                        data:{"elementID":elementID},
                        type: "GET",
                        success: function(data){
                            $("#roomsWithElement").html(data);
                            $.ajax({
                                url : "getRoomsWithoutElement.php",
                                data:{"elementID":elementID},
                                type: "GET",
                                success: function(data){
                                    $("#roomsWithoutElement").html(data);
                                }
                            });
                        }
                    });
                } 
            });
        }
    });
</script>

</body>
</html>