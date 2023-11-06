<?php
session_start();
?>

<?php
if(!isset($_SESSION["username"]))
   {
   echo "Bitte erst <a href=\"index.php\">einloggen</a>";
   exit;
   }
?>

<?php
	$mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');
	if ($mysqli ->connect_error) {
	    die("Connection failed: " . $conn->connect_error);
	}
	
	/* change character set to utf8 */
	if (!$mysqli->set_charset("utf8")) {
	    echo "Error loading character set utf8: " . $mysqli->error;
	    exit();
	} 
	
        // Vorhandene Variantenparameter löschen
        $sqlDelete = "DELETE FROM `LIMET_RB`.`tabelle_projekt_elementparameter`
                     WHERE `tabelle_projekte_idTABELLE_Projekte`=".$_SESSION["projectID"]." AND `tabelle_elemente_idTABELLE_Elemente` =".$_SESSION["elementID"]." AND `tabelle_Varianten_idtabelle_Varianten` =".$_SESSION["variantenID"].";";
        	
        if ($mysqli ->query($sqlDelete) === TRUE) {
	    echo "Variantenparameter gelöscht!";
	} else {
	    echo "Error: " . $sql . "<br>" . $mysqli->error;
	}

        
        // data loading Ansprechpersonentabelle
        $sql = "SELECT tabelle_geraete_has_tabelle_parameter.TABELLE_Parameter_idTABELLE_Parameter, tabelle_geraete_has_tabelle_parameter.Wert, tabelle_geraete_has_tabelle_parameter.Einheit
                FROM tabelle_geraete_has_tabelle_parameter
                WHERE (((tabelle_geraete_has_tabelle_parameter.TABELLE_Geraete_idTABELLE_Geraete)=".$_SESSION["deviceID"]."));";
        
        $result = $mysqli->query($sql);
        $deviceParameters = array();
        while ($row = $result->fetch_assoc()) { 
            $deviceParameters[$row['TABELLE_Parameter_idTABELLE_Parameter']]['TABELLE_Parameter_idTABELLE_Parameter'] = $row['TABELLE_Parameter_idTABELLE_Parameter'];
            $deviceParameters[$row['TABELLE_Parameter_idTABELLE_Parameter']]['Wert'] = $row['Wert'];
            $deviceParameters[$row['TABELLE_Parameter_idTABELLE_Parameter']]['Einheit'] = $row['Einheit'];
            
        }
        
        foreach($deviceParameters as $data) {
            $sql = "INSERT INTO `LIMET_RB`.`tabelle_projekt_elementparameter`
			(`tabelle_projekte_idTABELLE_Projekte`,
			`tabelle_elemente_idTABELLE_Elemente`,
			`tabelle_parameter_idTABELLE_Parameter`,			
			`Wert`,
			`Einheit`,
                        `tabelle_Varianten_idtabelle_Varianten`,
                        `tabelle_planungsphasen_idTABELLE_Planungsphasen`)
			VALUES
			(".$_SESSION["projectID"].",
                         ".$_SESSION["elementID"].",
                         ".$data["TABELLE_Parameter_idTABELLE_Parameter"].",
                         '".$data["Wert"]."',
                         '".$data["Einheit"]."',                                               			
			".$_SESSION["variantenID"].",
			1);";
            if ($mysqli ->query($sql) === TRUE) {
                echo "\nParameter ".$data['Wert']." ".$data['Einheit']." zu Variante hinzugefügt!";
            } else {
                echo "Error: " . $sql . "<br>" . $mysqli->error;
            }
        }
        
	
	$mysqli ->close();
	
	
					
?>
