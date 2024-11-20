<?php
session_start();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />

<!--DATEPICKER -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.5.0/css/bootstrap-datepicker3.min.css"/>
<script type='text/javascript' src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.5.0/js/bootstrap-datepicker.min.js"></script>

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
        
        if($_GET["lieferantenID"] != ""){
		$_SESSION["lieferantenID"]=$_GET["lieferantenID"];    
	}
			
               
        $sql = "SELECT tabelle_umsaetze.idtabelle_umsaetze, tabelle_umsaetze.umsatz, tabelle_umsaetze.geschaeftsbereich, tabelle_umsaetze.jahr
                FROM tabelle_umsaetze
                WHERE (((tabelle_umsaetze.tabelle_lieferant_idTABELLE_Lieferant)=".$_SESSION["lieferantenID"]."));";
        
	$result = $mysqli->query($sql);
	setlocale(LC_MONETARY,"de_DE");
	
	echo "<table class='table table-striped table-sm' id='tableLieferantenUmsaetze' cellspacing='0'>
	<thead><tr>";
		echo "<th>ID</th>
		<th>Umsatz</th>
		<th>Geschäftsbereich</th>
		<th>Jahr</th>
	</tr></thead><tbody>";
	
	while($row = $result->fetch_assoc()) {
	    echo "<tr>";
	    echo "<td>".$row["idtabelle_umsaetze"]."</td>";
            echo "<td>".sprintf('%01.2f', $row["umsatz"])."</td>";
            echo "<td>".$row["geschaeftsbereich"]."</td>";
            echo "<td>".$row["jahr"]."</td>";
	    echo "</tr>";
	}
	
	echo "</tbody></table>";	
	echo "<input type='button' id='addUmsatzModal' class='btn btn-success btn-sm' value='Umsatz hinzufügen' data-toggle='modal' data-target='#addUmsatzToLieferantModal'></input>";
	
	?>
	
    <!-- Modal zum Anlegen eines Umsatzes -->
	  <div class='modal fade' id='addUmsatzToLieferantModal' role='dialog'>
	    <div class='modal-dialog modal-md'>
	    
	      <!-- Modal content-->
	      <div class='modal-content'>
	        <div class='modal-header'>
	          <button type='button' class='close' data-dismiss='modal'>&times;</button>
	          <h4 class='modal-title'>Umsatz hinzufügen</h4>
	        </div>
	        <div class='modal-body' id='mbody'>
	        		<form role="form">
                                        <div class="form-group">
                                          <label for="umsatz">Umsatz:</label>
                                          <input type="umsatz" class="form-control form-control-sm" id="umsatz" placeholder="Komma ."/>
                                        </div>
                                        <div class="form-group">
                                          <label for="bereich">Geschäftsbereich:</label>
                                          <input type="text" class="form-control form-control-sm" id="bereich" placeholder="Geschäftsbereich"/>
                                        </div>	        	
                                        <div class="form-group">
                                          <label for="jahr">Jahr:</label>
                                          <input type="text" class="form-control form-control-sm" id="jahr" placeholder="yyyy"/>
                                        </div>                                   	
                              </form>
			</div>
	        <div class='modal-footer'>
	        	<input type='button' id='addUmsatz' class='btn btn-success btn-sm' value='Speichern' data-dismiss='modal'></input>
	          	<button type='button' class='btn btn-danger btn-sm' data-dismiss='modal'>Abbrechen</button>
	        </div>
	      </div>
	      
	    </div>
	  </div>

	
<script>

           
	    
   $("#tableLieferantenUmsaetze").DataTable( {
        "select": true,
        "paging": false,
		"searching": false,
		"info": false,
		"order": [[ 3, "desc" ]],
        //"pagingType": "simple_numbers",
        //"lengthMenu": [[5,10, 25, 50, -1], [5,10, 25, 50, "All"]],
        "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"},
        "scrollY":        '20vh',
    	"scrollCollapse": true,
        "columnDefs": [
                        {
                            "targets": [ 0 ],
                            "visible": false,
                            "searchable": false
                        }
                    ],
    } );
    
    //Preis zu Geraet hinzufügen
    $("#addUmsatz").click(function(){
            var umsatz = $("#umsatz").val();
            var bereich = $("#bereich").val();
            var jahr  = $("#jahr").val();

            if(umsatz !== "" && bereich !== "" && jahr !== ""){
                $.ajax({
                    url : "addUmsatzToLieferant.php",
                    data:{"umsatz":umsatz,"bereich":bereich ,"jahr":jahr},
                    type: "GET",	        
                    success: function(data){
                        alert(data);
                        $.ajax({
                                url : "getLieferantenUmsaetze.php",
                                type: "GET",
                                success: function(data){
                                    $("#lieferantenumsaetze").html(data);
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