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
	
	$sql = "SELECT `tabelle_bestandsdaten`.`Inventarnummer`,
			    `tabelle_bestandsdaten`.`Seriennummer`,
			    `tabelle_bestandsdaten`.`Anschaffungsjahr`
			FROM `LIMET_RB`.`tabelle_bestandsdaten`
			WHERE `tabelle_bestandsdaten`.`idtabelle_bestandsdaten`=".$_GET["bestandID"].";";
	
	$result = $mysqli->query($sql);
	$row = $result->fetch_assoc();

	
	echo "<form role='form'>        			        			        		
			  <div class='form-group'>
			    <label for='invNr'>Inventarnummer:</label>
			    <input type='text' class='form-control' id='invNr' placeholder='Inventarnummer' value='".$row["Inventarnummer"]."'/>
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
	
	echo "Test";
	$mysqli ->close();
	?>
	
<script>
    
	    

</script>

</body>
</html>