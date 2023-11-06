<?php
session_start();
?>

<!DOCTYPE html>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<html>
<head>
    <style>
        
        
    </style>
</head>
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
					
        
        $sql = "SELECT tabelle_räume.`Anmerkung FunktionBO` FROM tabelle_räume WHERE (((tabelle_räume.idTABELLE_Räume)=".$_SESSION["roomID"]."));";
        
	$result = $mysqli->query($sql);
	while($row = $result->fetch_assoc()) {
            echo "
                <div class='row mt-4'>
                    <div class='col-sm-4'>
                        <div class='card card-default m-2'>
                            <div class='card-header'>
                                <h4 class='m-b-2 text-dark'><i class='far fa-comment'></i> Anmerkungen</h4>
                            </div>            
                            <div class='card-body'>
                                <h4 class='m-t-2 text-dark'>".$row["Anmerkung FunktionBO"]."</h4>";
                                echo "
                            </div>
                        </div>
                    </div>                    
                </div>
                ";
        }        
         
	$mysqli ->close();
?>
<script>
</script> 

</body>
</html>