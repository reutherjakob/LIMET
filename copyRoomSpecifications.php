<?php
session_start();

function br2nl($string){
$return= str_replace(array("\r\n", "\n\r", "\r", "\n"), "<br/>", $string);
return $return;
}

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
	
	
	// Abfrage der zugehörigen Raumdaten
	$sql = "SELECT tabelle_räume.idTABELLE_Räume, tabelle_räume.Abdunkelbarkeit, tabelle_räume.Strahlenanwendung, tabelle_räume.Laseranwendung, tabelle_räume.H6020
			, tabelle_räume.GMP, tabelle_räume.ISO, tabelle_räume.`1 Kreis O2`, tabelle_räume.`2 Kreis O2`, tabelle_räume.`1 Kreis Va`, 
			tabelle_räume.`2 Kreis Va`, tabelle_räume.`1 Kreis DL-5`, tabelle_räume.`2 Kreis DL-5`, tabelle_räume.`DL-10`, tabelle_räume.`DL-tech`, tabelle_räume.CO2, tabelle_räume.NGA, tabelle_räume.N2O, tabelle_räume.AV, tabelle_räume.SV, tabelle_räume.ZSV, tabelle_räume.USV, tabelle_räume.Anwendungsgruppe, 
			tabelle_räume.`Anmerkung MedGas`, tabelle_räume.`Anmerkung Elektro`, tabelle_räume.`Anmerkung HKLS`, tabelle_räume.`Anmerkung Geräte`, tabelle_räume.`Anmerkung FunktionBO`, tabelle_räume.`Anmerkung BauStatik`, tabelle_räume.`Allgemeine Hygieneklasse`,
                        tabelle_räume.`Fussboden OENORM B5220`, tabelle_räume.`IT Anbindung`
			FROM tabelle_räume
			WHERE (((tabelle_räume.idTABELLE_Räume)=".$_SESSION["roomID"]."));";
			
	$result = $mysqli->query($sql);
	
	while($row = $result->fetch_assoc()) {
		$abdunkelbarkeit = $row["Abdunkelbarkeit"];
		$Strahlenanwendung = $row["Strahlenanwendung"];
		$Laseranwendung = $row["Laseranwendung"];
		$H6020 = $row["H6020"];
		$GMP = $row["GMP"];
		$ISO = $row["ISO"];
                $HYGIENEKLASSE = $row["Allgemeine Hygieneklasse"];
		
		$Kreis1O2 = $row["1 Kreis O2"];
		$Kreis2O2 = $row["2 Kreis O2"];
		
		$Kreis1Va = $row["1 Kreis Va"];
		$Kreis2Va = $row["2 Kreis Va"];
		$Kreis1DL5 = $row["1 Kreis DL-5"];
		$Kreis2DL5 = $row["2 Kreis DL-5"];
		
		$DL10 = $row["DL-10"];
		$DLtech = $row["DL-tech"];
		$CO2 = $row["CO2"];
		$NGA = $row["NGA"];
		$N2O = $row["N2O"];
		$AV = $row["AV"];
		$SV = $row["SV"];
		$ZSV = $row["ZSV"];
		$USV = $row["USV"];
		
		$Anwendungsgruppe = $row["Anwendungsgruppe"];
		$AnmerkungMedGas = $row["Anmerkung MedGas"];
		$AnmerkungElektro = $row["Anmerkung Elektro"];
		$AnmerkungHKLS = $row["Anmerkung HKLS"];
		$AnmerkungGeräte = $row["Anmerkung Geräte"];
		$AnmerkungBauStatik = $row["Anmerkung BauStatik"];
                
                $fussbodenklasse = $row["Fussboden OENORM B5220"];
                $it = $row["IT Anbindung"];
		
	}
	//$mysqli ->close();
	
	//Raumdaten updaten
	$roomIDs = $_GET["rooms"];
	$ausgabe = "";
	foreach ($roomIDs as $valueOfRoomID) {
		$sql = "UPDATE `LIMET_RB`.`tabelle_räume`
				SET
				`Abdunkelbarkeit` = '".$abdunkelbarkeit."',
				`Strahlenanwendung` = '".$Strahlenanwendung."',
				`Laseranwendung` = '".$Laseranwendung."',
				`H6020` = '".$H6020."',
				`GMP` = '".$GMP."',
				`ISO` = '".$ISO."',
                                `Allgemeine Hygieneklasse` = '".$HYGIENEKLASSE."',
				`1 Kreis O2` = '".$Kreis1O2."',
				`2 Kreis O2` = '".$Kreis2O2."',
				`1 Kreis Va` = '".$Kreis1Va."',
				`2 Kreis Va` = '".$Kreis2Va."',
				`1 Kreis DL-5` = '".$Kreis1DL5."',
				`2 Kreis DL-5` = '".$Kreis2DL5."',
				`DL-10` = '".$DL10."',
				`DL-tech` = '".$DLtech."',
				`CO2` = '".$CO2."',
				`NGA` = '".$NGA."',
				`N2O` = '".$N2O."',
				`AV` = '".$AV."',
				`SV` = '".$SV."',
				`ZSV` = '".$ZSV."',
				`USV` = '".$USV."',
				`Anwendungsgruppe` = '".$Anwendungsgruppe."',
				`Anmerkung MedGas` = '".$AnmerkungMedGas."',
				`Anmerkung Elektro` = '".$AnmerkungElektro."',
				`Anmerkung HKLS` = '".$AnmerkungHKLS."',
				`Anmerkung Geräte` = '".$AnmerkungGeräte."',				
				`Anmerkung BauStatik` = '".$AnmerkungBauStatik."',
                                `Fussboden OENORM B5220`  = '".$fussbodenklasse."',  
                                `IT Anbindung`  = '".$it."' 
				WHERE `idTABELLE_Räume` = ".$valueOfRoomID.";";                       	
	
		if ($mysqli ->query($sql) === TRUE) {
		    $ausgabe = $ausgabe . "Raum ".$valueOfRoomID." erfolgreich aktualisiert! \n";
		} else {
		    $ausgabe = "Error: " . $sql . "<br>" . $mysqli->error;
		}
	}
	$mysqli ->close();	
	
	echo $ausgabe;
	
					
?>
