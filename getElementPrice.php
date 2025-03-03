<?php
include "_utils.php";
include "_format.php";
check_login();
?>

<!DOCTYPE html >
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" /></head>
<body>
<?php

	$mysqli =utils_connect_sql();
	$sql = "SELECT tabelle_projekt_varianten_kosten.Kosten
			FROM tabelle_projekt_varianten_kosten INNER JOIN tabelle_räume_has_tabelle_elemente ON (tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) AND (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente)
			WHERE (((tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"].") AND ((tabelle_räume_has_tabelle_elemente.id)=".$_GET["id"]."));";
		    
	$result = $mysqli->query($sql);
	$row = $result->fetch_assoc();
        
	echo "<form>
		 <div class='form-group row'>
                    <label class='ml-4 col-lg-5 col-form-label col-form-label-sm' for='price'>Kosten</label>
                    <div class='col-lg-6'>
                        <input type='text' class='form-control form-control-sm' id='price' value=". format_money( $row["Kosten"])." disabled='disabled'></input>
                    </div>						  			 											 						 			
	 	</div>	
            </form>";

	$mysqli ->close();
	?>

    
<script>
    
	    

</script>

</body>
</html>