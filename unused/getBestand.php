<?php
// 25 FX
require_once 'utils/_utils.php';
check_login();
$mysqli = utils_connect_sql();

$bestandID = getPostInt('bestandID', 0);

$stmt = $mysqli->prepare("SELECT `Inventarnummer`, `Seriennummer`, `Anschaffungsjahr` 
                          FROM `LIMET_RB`.`tabelle_bestandsdaten` 
                          WHERE `idtabelle_bestandsdaten` = ?");

$stmt->bind_param("i", $bestandID);
$stmt->execute();
$row = $stmt->get_result();


echo "<form role='form'>        			        			        		
			  <div class='form-group'>
			    <label for='invNr'>Inventarnummer:</label>
			    <input type='text' class='form-control' id='invNr' placeholder='Inventarnummer' value='" . $row["Inventarnummer"] . "'/>
			  </div>
			  <div class='form-group'>
			    <label for='year'>Anschaffungsjahr:</label>
			    <input type='text' class='form-control' id='year' placeholder='Anschaffungsjahr'/>
			  </div>
			  <div class='form-group'>
			    <label for='serNr'>Seriennummer:</label>
			    <input type='text' class='form-control' id='serNr' placeholder='Seriennummer'/>
			  </div>	        	
		</form>";

$mysqli->close();
?>
