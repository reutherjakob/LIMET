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
	
	$sql = "SELECT tabelle_projekt_varianten_kosten.Kosten
			FROM tabelle_projekt_varianten_kosten INNER JOIN tabelle_räume_has_tabelle_elemente ON (tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) AND (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente)
			WHERE (((tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"].") AND ((tabelle_räume_has_tabelle_elemente.id)=".$_GET["id"]."));";
		    
	$result = $mysqli->query($sql);
	$row = $result->fetch_assoc();
	setlocale(LC_MONETARY,"de_DE");
        
	echo "<form>
		 <div class='form-group row'>
                    <label class='ml-4 col-sm-5 col-form-label col-form-label-sm' for='price'>Kosten</label>
                    <div class='col-sm-6'>
                        <input type='text' class='form-control form-control-sm' id='price' value=".money_format("%i", $row["Kosten"])." disabled='disabled'></input>
                    </div>						  			 											 						 			
	 	</div>	
            </form>";

	$mysqli ->close();
	?>

    
<script>
    
	    

</script>

</body>
</html>