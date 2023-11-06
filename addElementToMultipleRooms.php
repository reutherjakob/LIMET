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
	       
	$sql = "SELECT tabelle_projekt_varianten_kosten.Kosten
			FROM tabelle_projekt_varianten_kosten
			WHERE (((tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten)=1) AND ((tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente)=".filter_input(INPUT_GET, 'elementID').") AND ((tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"]."));";		    
			
        $result = $mysqli->query($sql);
	$row = $result->fetch_assoc();
        $ausgabe = "";
        $roomIDs = $_GET["rooms"];

        
	if(strlen($row["Kosten"]) == 0){
            $sql = "INSERT INTO `LIMET_RB`.`tabelle_projekt_varianten_kosten`
				(`tabelle_projekte_idTABELLE_Projekte`,
				`tabelle_elemente_idTABELLE_Elemente`,
				`tabelle_Varianten_idtabelle_Varianten`,
				`Kosten`)
				VALUES
				(".$_SESSION["projectID"].",
				".filter_input(INPUT_GET, 'elementID').",
				1,
				'0');";

            if ($mysqli ->query($sql) === TRUE) {
                $ausgabe = $ausgabe. "Variante erfolgreich angelegt! \n";   
                foreach ($roomIDs as $valueOfRoomID) {
                    $sql = "INSERT INTO `tabelle_räume_has_tabelle_elemente`
                                (`TABELLE_Räume_idTABELLE_Räume`,
                                `TABELLE_Elemente_idTABELLE_Elemente`,
                                `Neu/Bestand`,
                                `Anzahl`,
                                `Standort`,
                                `Verwendung`,
                                `Timestamp`,
                                `tabelle_Varianten_idtabelle_Varianten`,
                                `Kurzbeschreibung`)
                                VALUES
                                (".$valueOfRoomID.",
                                ".filter_input(INPUT_GET, 'elementID').",
                                '1',
                                ".filter_input(INPUT_GET, 'amount').",
                                '1',
                                '1',
                                '".date("Y-m-d H:i:s")."',
                                1,
                                '".filter_input(INPUT_GET, 'comment')."');";                  	

                        if ($mysqli ->query($sql) === TRUE) {
                            $ausgabe = $ausgabe . "Raum ".$valueOfRoomID." Element hinzugefügt! \n";
                        } else {
                            $ausgabe = "Error: " . $sql . "<br>" . $mysqli->error;
                        }
                }
            } 
            else {
                echo "Error: " . $sql . "<br>" . $mysqli->error;
            }
	}
        else{
            foreach ($roomIDs as $valueOfRoomID) {
                    $sql = "INSERT INTO `tabelle_räume_has_tabelle_elemente`
                                (`TABELLE_Räume_idTABELLE_Räume`,
                                `TABELLE_Elemente_idTABELLE_Elemente`,
                                `Neu/Bestand`,
                                `Anzahl`,
                                `Standort`,
                                `Verwendung`,
                                `Timestamp`,
                                `tabelle_Varianten_idtabelle_Varianten`,
                                `Kurzbeschreibung`)
                                VALUES
                                (".$valueOfRoomID.",
                                ".filter_input(INPUT_GET, 'elementID').",
                                '1',
                                ".filter_input(INPUT_GET, 'amount').",
                                '1',
                                '1',
                                '".date("Y-m-d H:i:s")."',
                                1,
                                '".filter_input(INPUT_GET, 'comment')."');";                  	

                        if ($mysqli ->query($sql) === TRUE) {
                            $ausgabe = $ausgabe . "Raum ".$valueOfRoomID." Element hinzugefügt! \n";
                        } else {
                            $ausgabe = "Error: " . $sql . "<br>" . $mysqli->error;
                        }
            }
        }	           
	$mysqli ->close();		
	echo $ausgabe;	
?>
