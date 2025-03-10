<?php
session_start();
?>

<!DOCTYPE html >
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<link rel="stylesheet" href="style.css" type="text/css" media="screen" />
<title>LIMET - Raumbuch - Logout</title>
<link rel="icon" href="iphone_favicon.png"></link>
<script src="sorttable.js"></script>
 <!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css"/>

<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script type="text/javascript" src="/js/jquery.tablesorter.js"></script>
<style>
.navbar-brand {
  padding: 0px;
}
.navbar-brand>img {
  height: 100%;
  width: auto;
}


</style>

<!-- Latest compiled JavaScript -->
<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<script>

</script>
</head>

<body style="height:100%">
<?php
if(!isset($_SESSION["username"]))
   {
   echo "Bitte erst <a href=\"index.php\">einloggen</a>";
   exit;
   }

?>
<div class="container-fluid" style="height:100%">
	<nav class="navbar navbar-default" style="height:4%">
		  
		    <div class="navbar-header">
		      <a class="navbar-brand"><img src="LIMET_logo.png" alt="LIMETLOGO"/></a>
		    </div>
		    <div>
		      <ul class="nav navbar-nav">
		        <li><a href="index.php">Login</a></li>
		      </ul>
		    </div>	  
	</nav>
	<div class="row" style="min-height: 0%; max-height:90%; overflow:auto">
		  <div class="col-xxl-12" >
		  	<?php
				
				// Initialize the session.
				// If you are using session_name("something"), don't forget it now!
				session_start();
				
				// Unset all of the session variables.
				$_SESSION = array();
				
				
				// Finally, destroy the session.
				session_destroy();										
			?>	
                        Logout erfolgreich!
		  </div>
	</div>	
</div>

</body>

</html>
