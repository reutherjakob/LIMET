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
   
   
   function br2nl($string){
$return= str_replace(array("<br/>"), "\n", $string);
return $return;
}

?>

<?php
	//$_SESSION["elementID"]=$_GET["elementID"];
	//$_SESSION["variantenID"]=$_GET["variantenID"];
        
        if(filter_input(INPUT_GET, 'elementID') != ""){
            $_SESSION["elementID"]=filter_input(INPUT_GET, 'elementID');
        }
        if(filter_input(INPUT_GET, 'variantenID') != ""){
            $_SESSION["variantenID"]=filter_input(INPUT_GET, 'variantenID');
        }
	
	$mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');
	
	
	if (!$mysqli->set_charset("utf8")) {
	    printf("Error loading character set utf8: %s\n", $mysqli->error);
	    exit();
	} 
	$sql = "SELECT tabelle_projekt_varianten_kosten.Kosten
			FROM tabelle_projekt_varianten_kosten
			WHERE (((tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten)=".$_SESSION["variantenID"].") AND ((tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente)=".$_SESSION["elementID"].") AND ((tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"]."));";
		
				
	$result = $mysqli->query($sql);
	
	$row = $result->fetch_assoc();
	setlocale(LC_MONETARY,"de_DE");
	
        echo "
            <div class='col-md-12'>
                <div class='card'>
                        <div class='ml-4 mt-4 card-title'><form class='form-inline'>
                    <label class='m-1' for='variante'>Variante</label>
                            <select class='form-control form-control-sm' id='variante'>";
                                  switch ($_SESSION["variantenID"]) {
                                              case "1":
                                                  echo "<option value='1' selected>A</option>
                                                                      <option value='2'>B</option>
                                                                      <option value='3'>C</option>
                                                                      <option value='4'>D</option>
                                                                      <option value='5'>E</option>
                                                                      <option value='6'>F</option>
                                                                      <option value='7'>G</option>";
                                                  break;
                                                  case "2":
                                                  echo "<option value='1'>A</option>
                                                                      <option value='2' selected>B</option>
                                                                      <option value='3'>C</option>
                                                                      <option value='4'>D</option>
                                                                      <option value='5'>E</option>
                                                                      <option value='6'>F</option>
                                                                      <option value='7'>G</option>";
                                                  break;
                                               case "3":
                                                  echo "<option value='1'>A</option>
                                                                      <option value='2'>B</option>
                                                                      <option value='3' selected>C</option>
                                                                      <option value='4'>D</option>
                                                                      <option value='5'>E</option>
                                                                      <option value='6'>F</option>
                                                                      <option value='7'>G</option>";
                                                  break;
                                                  case "4":
                                                  echo "<option value='1'>A</option>
                                                                      <option value='2'>B</option>
                                                                      <option value='3'>C</option>
                                                                      <option value='4' selected>D</option>
                                                                      <option value='5'>E</option>
                                                                      <option value='6'>F</option>
                                                                      <option value='7'>G</option>";
                                                  break;
                                                  case "5":
                                                  echo "<option value='1'>A</option>
                                                                      <option value='2'>B</option>
                                                                      <option value='3'>C</option>
                                                                      <option value='4'>D</option>
                                                                      <option value='5' selected>E</option>
                                                                      <option value='6'>F</option>
                                                                      <option value='7'>G</option>";
                                                  break;
                                                  case "6":
                                                  echo "<option value='1'>A</option>
                                                                      <option value='2'>B</option>
                                                                      <option value='3'>C</option>
                                                                      <option value='4'>D</option>
                                                                      <option value='5'>E</option>
                                                                      <option value='6' selected>F</option>
                                                                      <option value='7'>G</option>";
                                                  break;
                                                  case "7":
                                                  echo "<option value='1'>A</option>
                                                                      <option value='2'>B</option>
                                                                      <option value='3'>C</option>
                                                                      <option value='4'>D</option>
                                                                      <option value='5'>E</option>
                                                                      <option value='6'>F</option>
                                                                      <option value='7' selected>G</option>";
                                                  break;

                             } 
                            echo "</select>
				  <label class='m-1' for='kosten'>Kosten</label>
				  <input type='text' class='form-control form-control-sm' id='kosten' value=".$row["Kosten"]."></input>				  	
				  <button type='button' id='saveVariantePrice' class='btn btn-outline-dark btn-sm m-1' value='saveVariantePrice'><i class='far fa-save'></i> Kosten speichern</button>
				  <button type='button' id='getElementPriceHistory' class='btn btn-outline-dark btn-sm m-1' value='getElementPriceHistory'  data-toggle='modal' data-target='#getElementPriceHistoryModal'><i class='far fa-clock'></i> Kosten Änderungsverlauf</button>				
                            </form> </div>
                        <div class='card-body'>
                            <div class='row'>
                            <div class='col-md-6'>                            
                                <div class='card'>
                                    <div class='card-header'>
                                        Variantenparameter                                        
                                        <button type='button' id='addVariantenParameters' class='btn btn-outline-dark btn-sm m-1' value='addVariantenParameters' data-toggle='modal' data-target='#addVariantenParameterToElementModal'><i class='fas fa-upload'></i> Variantenparameter übernehmen</button>
                                    </div>
                                    <div class='card-body' id='variantenParameter'>";

                                                    $sql = "SELECT tabelle_parameter.Bezeichnung, tabelle_projekt_elementparameter.Wert, tabelle_projekt_elementparameter.Einheit, tabelle_parameter_kategorie.Kategorie, tabelle_projekt_elementparameter.tabelle_parameter_idTABELLE_Parameter
                                                                    FROM tabelle_parameter_kategorie INNER JOIN (tabelle_parameter INNER JOIN tabelle_projekt_elementparameter ON tabelle_parameter.idTABELLE_Parameter = tabelle_projekt_elementparameter.tabelle_parameter_idTABELLE_Parameter) ON tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie = tabelle_parameter.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie
                                                                    WHERE (((tabelle_projekt_elementparameter.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"].") AND ((tabelle_projekt_elementparameter.tabelle_elemente_idTABELLE_Elemente)=".$_SESSION["elementID"].") AND ((tabelle_projekt_elementparameter.tabelle_Varianten_idtabelle_Varianten)=".$_SESSION["variantenID"]."))
                                                                    ORDER BY tabelle_parameter_kategorie.Kategorie ASC, tabelle_parameter.Bezeichnung ASC;";						

                                                    $result = $mysqli->query($sql);

                                                    echo "<table class='table table-striped table-sm' id='tableElementParameters' cellspacing='0' width='100%'>
                                                    <thead><tr>
                                                    <th></th>
                                                    <th>Kategorie</th>
                                                    <th>Parameter</th>
                                                    <th>Wert</th>
                                                    <th>Einheit</th>
                                                    <th></th>
                                                    </tr></thead>
                                                    <tbody>";

                                                    while($row = $result->fetch_assoc()) {
                                                        echo "<tr>";						 
                                                        echo "<td><button type='button' id='".$row["tabelle_parameter_idTABELLE_Parameter"]."' class='btn btn-outline-danger btn-xs' value='deleteParameter'><i class='fas fa-minus'></i></button></td>";
                                                        echo "<td>".$row["Kategorie"]."</td>";
                                                        echo "<td>".$row["Bezeichnung"]."</td>";
                                                        echo "<td><input type='text' id='wert".$row["tabelle_parameter_idTABELLE_Parameter"]."' value='".$row["Wert"]."' size='20'></input></td>";
                                                            echo "<td><input type='text' id='einheit".$row["tabelle_parameter_idTABELLE_Parameter"]."' value='".$row["Einheit"]."' size='45'></input></td>";
                                                            echo "<td><button type='button' id='".$row["tabelle_parameter_idTABELLE_Parameter"]."' class='btn btn-warning btn-sm' value='saveParameter'><i class='far fa-save'></i></button></td>";
                                                        echo "</tr>";

                                                    }

                                                    echo "</tbody></table>

                                    </div>
                                </div>	
                            </div>
                            <div class='col-md-6'>
                                <div class='card'>
                                    <div class='card-header'>Mögliche Parameter</div>
                                    <div class='card-body' id='possibleVariantenParameter'>";

                                                    $sql = "SELECT tabelle_parameter.idTABELLE_Parameter, tabelle_parameter.Bezeichnung, tabelle_parameter_kategorie.Kategorie 
                                                                    FROM tabelle_parameter, tabelle_parameter_kategorie 
                                                                    WHERE tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie = tabelle_parameter.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie 
                                                                    AND tabelle_parameter.idTABELLE_Parameter NOT IN 
                                                                    (SELECT tabelle_parameter.idTABELLE_Parameter 
                                                                    FROM tabelle_parameter INNER JOIN tabelle_projekt_elementparameter ON tabelle_parameter.idTABELLE_Parameter = tabelle_projekt_elementparameter.TABELLE_Parameter_idTABELLE_Parameter 
                                                                    WHERE tabelle_projekt_elementparameter.TABELLE_Elemente_idTABELLE_Elemente = ".$_SESSION["elementID"]." AND tabelle_projekt_elementparameter.tabelle_projekte_idTABELLE_Projekte = ".$_SESSION["projectID"]." AND tabelle_projekt_elementparameter.tabelle_Varianten_idtabelle_Varianten = ".$_SESSION["variantenID"].") 
                                                                    ORDER BY tabelle_parameter_kategorie.Kategorie ASC, tabelle_parameter.Bezeichnung ASC;";	

                                                    $result = $mysqli->query($sql);

                                                    echo "<table class='table table-striped table-sm' id='tablePossibleElementParameters' cellspacing='0' width='100%'>
                                                    <thead><tr>
                                                    <th></th>
                                                    <th>Kategorie</th>
                                                    <th>Parameter</th>
                                                    </tr></thead>
                                                    <tbody>";

                                                    while($row = $result->fetch_assoc()) {
                                                        echo "<tr>";
                                                        echo "<td><button type='button' id='".$row["idTABELLE_Parameter"]."' class='btn btn-outline-success btn-xs' value='addParameter'><i class='fas fa-plus'></i></button></td>";
                                                        echo "<td>".$row["Kategorie"]."</td>";
                                                        echo "<td>".$row["Bezeichnung"]."</td>";
                                                        echo "</tr>";

                                                    }

                                                    echo "</tbody></table>
                                    </div>
                                </div>	
                            </div>
                        </div>	
</div>                        
                </div>
             </div>";
        
	echo "<!--<div class='mt-4 card'>
                    <div class='card-body'>
                    
                <form class='form-inline'>
                    <label class='m-4' for='variante'>Variante</label>
                            <select class='form-control form-control-sm' id='variante'>";
                                  switch ($_SESSION["variantenID"]) {
                                              case "1":
                                                  echo "<option value='1' selected>A</option>
                                                                      <option value='2'>B</option>
                                                                      <option value='3'>C</option>
                                                                      <option value='4'>D</option>
                                                                      <option value='5'>E</option>
                                                                      <option value='6'>F</option>
                                                                      <option value='7'>G</option>";
                                                  break;
                                                  case "2":
                                                  echo "<option value='1'>A</option>
                                                                      <option value='2' selected>B</option>
                                                                      <option value='3'>C</option>
                                                                      <option value='4'>D</option>
                                                                      <option value='5'>E</option>
                                                                      <option value='6'>F</option>
                                                                      <option value='7'>G</option>";
                                                  break;
                                               case "3":
                                                  echo "<option value='1'>A</option>
                                                                      <option value='2'>B</option>
                                                                      <option value='3' selected>C</option>
                                                                      <option value='4'>D</option>
                                                                      <option value='5'>E</option>
                                                                      <option value='6'>F</option>
                                                                      <option value='7'>G</option>";
                                                  break;
                                                  case "4":
                                                  echo "<option value='1'>A</option>
                                                                      <option value='2'>B</option>
                                                                      <option value='3'>C</option>
                                                                      <option value='4' selected>D</option>
                                                                      <option value='5'>E</option>
                                                                      <option value='6'>F</option>
                                                                      <option value='7'>G</option>";
                                                  break;
                                                  case "5":
                                                  echo "<option value='1'>A</option>
                                                                      <option value='2'>B</option>
                                                                      <option value='3'>C</option>
                                                                      <option value='4'>D</option>
                                                                      <option value='5' selected>E</option>
                                                                      <option value='6'>F</option>
                                                                      <option value='7'>G</option>";
                                                  break;
                                                  case "6":
                                                  echo "<option value='1'>A</option>
                                                                      <option value='2'>B</option>
                                                                      <option value='3'>C</option>
                                                                      <option value='4'>D</option>
                                                                      <option value='5'>E</option>
                                                                      <option value='6' selected>F</option>
                                                                      <option value='7'>G</option>";
                                                  break;
                                                  case "7":
                                                  echo "<option value='1'>A</option>
                                                                      <option value='2'>B</option>
                                                                      <option value='3'>C</option>
                                                                      <option value='4'>D</option>
                                                                      <option value='5'>E</option>
                                                                      <option value='6'>F</option>
                                                                      <option value='7' selected>G</option>";
                                                  break;

                             } 
                            echo "</select>
				  <label class='m-4' for='kosten'>Kosten</label>
				  <input type='text' class='form-control form-control-sm' id='kosten' value=".$row["Kosten"]."></input>				  	
				  <button type='button' id='saveVariantePrice' class='btn btn-outline-dark btn-sm m-1' value='saveVariantePrice'><i class='far fa-save'></i> Kosten speichern</button>
				  <button type='button' id='getElementPriceHistory' class='btn btn-outline-dark btn-sm m-1' value='getElementPriceHistory'  data-toggle='modal' data-target='#getElementPriceHistoryModal'><i class='far fa-clock'></i> Kosten Änderungsverlauf</button>				
                    </form> 
                    </div>
                </div>
                
		<div class='mt-4 card'>
                    <div class='card-body'>
                  
			<div class='col-md-6'>
                            <div class='mt-4 card'>
                                <div class='card-header'>Variantenparameter</div>
                                <div class='card-body' id='variantenParameter'>";
			  				
						$sql = "SELECT tabelle_parameter.Bezeichnung, tabelle_projekt_elementparameter.Wert, tabelle_projekt_elementparameter.Einheit, tabelle_parameter_kategorie.Kategorie, tabelle_projekt_elementparameter.tabelle_parameter_idTABELLE_Parameter
								FROM tabelle_parameter_kategorie INNER JOIN (tabelle_parameter INNER JOIN tabelle_projekt_elementparameter ON tabelle_parameter.idTABELLE_Parameter = tabelle_projekt_elementparameter.tabelle_parameter_idTABELLE_Parameter) ON tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie = tabelle_parameter.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie
								WHERE (((tabelle_projekt_elementparameter.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"].") AND ((tabelle_projekt_elementparameter.tabelle_elemente_idTABELLE_Elemente)=".$_SESSION["elementID"].") AND ((tabelle_projekt_elementparameter.tabelle_Varianten_idtabelle_Varianten)=".$_SESSION["variantenID"]."))
								ORDER BY tabelle_parameter_kategorie.Kategorie;";						
	    
						$result = $mysqli->query($sql);
						
						echo "<table class='table table-striped table-sm' id='tableElementParameters' cellspacing='0' width='100%'>
						<thead><tr>
						<th></th>
						<th>Kategorie</th>
						<th>Parameter</th>
						<th>Wert</th>
						<th>Einheit</th>
						<th></th>
						</tr></thead>
						<tbody>";
						
						while($row = $result->fetch_assoc()) {
						    echo "<tr>";						 
						    echo "<td><button type='button' id='".$row["tabelle_parameter_idTABELLE_Parameter"]."' class='btn btn-danger btn-xs' value='deleteParameter'><span class='glyphicon glyphicon-minus'></span></button></td>";
						    echo "<td>".$row["Kategorie"]."</td>";
						    echo "<td>".$row["Bezeichnung"]."</td>";
						    echo "<td><input type='text' id='wert".$row["tabelle_parameter_idTABELLE_Parameter"]."' value='".$row["Wert"]."' size='20'></input></td>";
							echo "<td><input type='text' id='einheit".$row["tabelle_parameter_idTABELLE_Parameter"]."' value='".$row["Einheit"]."' size='45'></input></td>";
							echo "<td><button type='button' id='".$row["tabelle_parameter_idTABELLE_Parameter"]."' class='btn btn-default btn-sm' value='saveParameter'><span class='glyphicon glyphicon-floppy-disk'></span></button></td>";
						    echo "</tr>";
						    
						}
						
						echo "</tbody></table>
						
                                </div>
                            </div>	
			</div>
			<div class='col-md-6'>
                            <div class='mt-4 card'>
                                <div class='card-header'>Mögliche Parameter</div>
                                <div class='card-body' id='possibleVariantenParameter'>";
			  			
			  			$sql = "SELECT tabelle_parameter.idTABELLE_Parameter, tabelle_parameter.Bezeichnung, tabelle_parameter_kategorie.Kategorie 
			  					FROM tabelle_parameter, tabelle_parameter_kategorie 
			  					WHERE tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie = tabelle_parameter.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie 
								AND tabelle_parameter.idTABELLE_Parameter NOT IN 
								(SELECT tabelle_parameter.idTABELLE_Parameter 
								FROM tabelle_parameter INNER JOIN tabelle_projekt_elementparameter ON tabelle_parameter.idTABELLE_Parameter = tabelle_projekt_elementparameter.TABELLE_Parameter_idTABELLE_Parameter 
								WHERE tabelle_projekt_elementparameter.TABELLE_Elemente_idTABELLE_Elemente = ".$_SESSION["elementID"]." AND tabelle_projekt_elementparameter.tabelle_projekte_idTABELLE_Projekte = ".$_SESSION["projectID"]." AND tabelle_projekt_elementparameter.tabelle_Varianten_idtabelle_Varianten = ".$_SESSION["variantenID"].") 
								ORDER BY tabelle_parameter_kategorie.Kategorie;";	
						
						$result = $mysqli->query($sql);
						
						echo "<table class='table table-striped table-sm' id='tablePossibleElementParameters' cellspacing='0' width='100%'>
						<thead><tr>
						<th></th>
						<th>Kategorie</th>
						<th>Parameter</th>
						</tr></thead>
						<tbody>";
						
						while($row = $result->fetch_assoc()) {
						    echo "<tr>";
						    echo "<td><button type='button' id='".$row["idTABELLE_Parameter"]."' class='btn btn-success btn-xs' value='addParameter'><span class='glyphicon glyphicon-plus'></span></button></td>";
						    echo "<td>".$row["Kategorie"]."</td>";
						    echo "<td>".$row["Bezeichnung"]."</td>";
						    echo "</tr>";
						    
						}
						
						echo "</tbody></table>
                                </div>
                            </div>	
			</div>
                        
                    </div>
		</div>-->";
		
		
		

?>
	<!-- Modal zum Zeigen der Kostenänderungen -->
	  <div class='modal fade' id='getElementPriceHistoryModal' role='dialog'>
	    <div class='modal-dialog modal-lg'>
	    
	      <!-- Modal content-->
	      <div class='modal-content'>
	        <div class='modal-header'>
                    <h4 class='modal-title'>Kostenänderungen</h4>
	          <button type='button' class='close' data-dismiss='modal'>&times;</button>	          
	        </div>
	        <div class='modal-body' id='mbody'>
                        	          <?php
	          		$sql = "SELECT tabelle_varianten.Variante, tabelle_projekt_varianten_kosten_aenderung.kosten_alt, tabelle_projekt_varianten_kosten_aenderung.kosten_neu, tabelle_projekt_varianten_kosten_aenderung.timestamp, tabelle_projekt_varianten_kosten_aenderung.user
							FROM tabelle_varianten INNER JOIN tabelle_projekt_varianten_kosten_aenderung ON tabelle_varianten.idtabelle_Varianten = tabelle_projekt_varianten_kosten_aenderung.variante
							WHERE (((tabelle_projekt_varianten_kosten_aenderung.projekt)=".$_SESSION["projectID"].") AND ((tabelle_projekt_varianten_kosten_aenderung.element)=".$_SESSION["elementID"]."))
							ORDER BY tabelle_projekt_varianten_kosten_aenderung.timestamp DESC;";	
														
						$result = $mysqli->query($sql);
						
						echo "<table class='table table-striped table-sm' id='tableVariantenCostsOverTime' cellspacing='0' width='100%'>
						<thead><tr>
						<th>Variante</th>
						<th>Kosten vorher</th>
						<th>Kosten nachher</th>						
						<th>User</th>
						<th>Datum</th>
						</tr></thead>
						<tbody>";
						
						while($row = $result->fetch_assoc()) {
						    echo "<tr>";
						    echo "<td>".$row["Variante"]."</td>";
						    echo "<td>".$row["kosten_alt"]."</td>";
						    echo "<td>".$row["kosten_neu"]."</td>";
						    echo "<td>".$row["user"]."</td>";
						    echo "<td>".$row["timestamp"]."</td>";
						    echo "</tr>";
						    
						}
						
						echo "</tbody></table>";

	          ?>
	    	</div>
	        <div class='modal-footer'>
	          	<button type='button' class='btn btn-danger btn-sm' data-dismiss='modal'>Schließen</button>
	        </div>
	      </div>	      
	    </div>
	  </div>
         <!-- ALERT Modal -->
        <div class="modal fade" id="alertModal" role="dialog">
          <div class="modal-dialog modal-sm">
            <!-- Modal content-->
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><span class='glyphicon glyphicon-info-sign'></span> Info</h4>
              </div>
              <div class="modal-body">
                <p id="error"></p>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              </div>
            </div>
          </div>
        </div>
         <!-- Variantenparameter übernehmen Modal -->
         <div class='modal fade' id='addVariantenParameterToElementModal' role='dialog'>
	    <div class='modal-dialog modal-sm'>	    
	      <!-- Modal content-->
	      <div class='modal-content'>
	        <div class='modal-header'>	          
	          <h4 class='modal-title'>Elemtparameter übernehm</h4>
                  <button type='button' class='close' data-dismiss='modal'>&times;</button>
	        </div>
	        <div class='modal-body' id='mbody'>Wollen Sie die zentralen Elementparameter überschreiben?</div>
	        <div class='modal-footer'>
                    <input type='button' id='addVariantenParameterToElement' class='btn btn-success btn-sm' value='Ja' data-dismiss='modal'></input>
                    <button type='button' class='btn btn-danger btn-sm' data-dismiss='modal'>Nein</button>
	        </div>
	      </div>
	      
	    </div>
	  </div>
         
         
	  
<?php
	$mysqli ->close();
?>
        
        
       
<script>

	$(document).ready(function() {
		$('#tableElementParameters').DataTable( {
			//"paging": true,
                        "select":true,
			"searching": true,
			"info": true,
			"order": [[ 1, "asc" ]],
			"columnDefs": [
                            {
                                "targets": [ 0 ],
                                "visible": true,
                                "searchable": false,
                                "sortable": false
                            }
                        ],
                        "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json",
                        "scrollX": true} 	 
	     } ); 
                
                $('#tablePossibleElementParameters').DataTable( {
			//"paging": true,
                        "select":true,
			"searching": true,
			"info": true,
			"order": [[ 1, "asc" ]],
			"columnDefs": [
                            {
                                "targets": [ 0 ],
                                "visible": true,
                                "searchable": false,
                                "sortable": false
                            }
                        ],
                        "scrollX": true,
                        "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"} 	 
	     } );  
	     
		$('#tableVariantenCostsOverTime').DataTable( {
			//"paging": false,
                        "select":true,
			"searching": true,
			"info": false,
			"order": [[ 1, "asc" ]],
			"columnDefs": [
                            {
                                "targets": [ 0 ],
                                "visible": true,
                                "searchable": true,
                                "sortable": true
                            }
                        ],
                        "scrollX": true,
                        "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"} 	 
                } );  	     

	    		     
	 } );
	 
	 // Variante auswählen/geändert
	$('#variante').change(function(){
	    var variantenID = this.value;
	    $.ajax({
	        url : "getVariantePrice.php",
	        data:{"variantenID":variantenID},
	        type: "GET",
	        success: function(data){
	            if(data.length === 2){
                        $("#error").html("Variante noch nicht vorhanden! Zum Anlegen Kosten eingeben und Speichern!");
                        $('#alertModal').modal("show");
	            	$("#kosten").val("");
	            	$("#possibleVariantenParameter").hide();
	            	$("#variantenParameter").hide();
	            }
	            else{
	            	$("#kosten").val(data);
	            	$("#possibleVariantenParameter").show();
                        $("#variantenParameter").show();
                        $.ajax({
                            url : "getVarianteParameters.php",
                            data:{"variantenID":variantenID},
                            type: "GET",
                            success: function(data){
                                $("#variantenParameter").html(data);
                                $.ajax({
                                    url : "getPossibleVarianteParameters.php",
                                    data:{"variantenID":variantenID},
                                    type: "GET",
                                    success: function(data){
                                        $("#possibleVariantenParameter").html(data);
                                    }
                                });
                            }
                        });
	            }
	        }
	    });

	});
	
	// Kosten für Variante speichern
	$("button[value='saveVariantePrice']").click(function(){
            
            if($('#kosten').val() !== ''){
                $.ajax({
                    url : "saveVariantePrice.php",
                    data:{"kosten":$('#kosten').val()},
                    type: "GET",
                    success: function(data){
                        alert(data);
                        $("#possibleVariantenParameter").show();
                        $("#variantenParameter").show();
                        var variantenID = $("#variante").val();
                        $.ajax({
                            url : "getVarianteParameters.php",
                            data:{"variantenID":variantenID},
                            type: "GET",
                            success: function(data){
                                $("#variantenParameter").html(data);
                                $.ajax({
                                    url : "getPossibleVarianteParameters.php",
                                    data:{"variantenID":variantenID},
                                    type: "GET",
                                    success: function(data){
                                        $("#possibleVariantenParameter").html(data);
                                    }
                                });
                            }                        
                        });                  
                    }
                });
            }
            else{
                alert("Kosten eingeben!");
            }
	});
	
	
	//Parameter zu Variante hinzufügen
	$("button[value='addParameter']").click(function(){
	    var variantenID = $('#variante').val(); 
	    var id = this.id;
	    
	    if(id !== ""){			 
	        $.ajax({
		        url : "addParameterToVariante.php",
		        data:{"parameterID":id,"variantenID":variantenID},
		        type: "GET",
		        success: function(data){
		        	alert(data);
		        	$.ajax({
				        url : "getVarianteParameters.php",
				        data:{"variantenID":variantenID},
				        type: "GET",
				        success: function(data){
				        	$("#variantenParameter").html(data);
				        	$.ajax({
						        url : "getPossibleVarianteParameters.php",
						        data:{"variantenID":variantenID},
						        type: "GET",
						        success: function(data){
						        	$("#possibleVariantenParameter").html(data);
						        } 
					        }); 

				        } 
			        }); 

		        } 
	        }); 
	    }
		
    });
    
    //Parameter von Variante entfernen
	$("button[value='deleteParameter']").click(function(){
	    var variantenID = $('#variante').val(); 
	    var id = this.id;
	    
	    if(id !== ""){			 
	        $.ajax({
		        url : "deleteParameterFromVariante.php",
		        data:{"parameterID":id,"variantenID":variantenID},
		        type: "GET",
		        success: function(data){
		        	alert(data);
		        	$.ajax({
				        url : "getVarianteParameters.php",
				        data:{"variantenID":variantenID},
				        type: "GET",
				        success: function(data){
				        	$("#variantenParameter").html(data);
				        	$.ajax({
						        url : "getPossibleVarianteParameters.php",
						        data:{"variantenID":variantenID},
						        type: "GET",
						        success: function(data){
						        	$("#possibleVariantenParameter").html(data);
						        } 
					        }); 

				        } 
			        }); 

		        } 
	        }); 
	    }
		
    });
	
    // Parameter ändern bzw speichern
    $("button[value='saveParameter']").click(function(){
        var id=this.id; 
        var wert = $("#wert"+id).val();
        var einheit = $("#einheit"+id).val();
        var variantenID = $('#variante').val();

        if(id !== ""){
            $.ajax({
                    url : "updateParameter.php",
                    data:{"parameterID":id,"wert":wert,"einheit":einheit,"variantenID":variantenID},
                    type: "GET",
                    success: function(data){
                            alert(data);
                    }
                });		    
        }

    });
    
    // Variantenparameter übernehmen in zentrales Element   
    $("#addVariantenParameterToElement").click(function(){
	const elementID = <?php echo $_SESSION["elementID"]  ?>;
        const variantenID = <?php echo $_SESSION["variantenID"]  ?>;
          
        $.ajax({
            url : "addVariantenParameterToElement.php",
            data:{"elementID":elementID,"variantenID":variantenID},
	    type: "GET",
	    success: function(data){
                alert(data);
                $.ajax({
                    url : "getStandardElementParameters.php",
                    data:{"elementID":elementID},
                    type: "GET",
                    success: function(data){
                        $("#elementDBParameter").html(data);
                    }
                });
            }
        });                        
    });



</script> 

</body>
</html>