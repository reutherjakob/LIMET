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
?>

<?php
	if($_GET["projectID"] != ""){
		$_SESSION["projectID"]=$_GET["projectID"];
	}
	if($_GET["roomID"] != ""){
		$_SESSION["roomID"]=$_GET["roomID"];
	}
	if($_GET["elementID"] != ""){
		$_SESSION["elementID"]=$_GET["elementID"];
	}
        if($_GET["projectName"] != ""){
		$_SESSION["projectName"]=$_GET["projectName"];
	}
        if($_GET["projectAusfuehrung"] != ""){
		$_SESSION["projectAusfuehrung"]=$_GET["projectAusfuehrung"];
	}
        if($_GET["projectPlanungsphase"] != ""){
		$_SESSION["projectPlanungsphase"]=$_GET["projectPlanungsphase"];
	}

?>
 

</body>
</html>