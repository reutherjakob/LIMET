<?php
session_start();
include '_utils.php';
init_page_serversides();
?> 


<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>RB-Kosten-Raumbereich</title>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="style.css" type="text/css" media="screen" />
<link rel="icon" href="iphone_favicon.png">
 
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css" integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>
  
 
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.18/b-1.5.2/b-html5-1.5.2/sl-1.2.6/datatables.min.css"/>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.18/b-1.5.2/b-html5-1.5.2/sl-1.2.6/datatables.min.js"></script>

 
</head>

<body style="height:100%">

<div id="limet-navbar"></div> <!-- Container für Navbar -->
<div class="container-fluid" >

    <div class="mt-4 card">
                <div class="card-header">
		  			<form class="form-inline">
					 		<label class="mr-sm-2" for='selectRoomArea'>Raumbereich</label>
							
								<select class='form-control form-control-sm' id='selectRoomArea' name='selectRoomArea'>
									<?php
										$mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');
										
										
										if (!$mysqli->set_charset("utf8")) {
										    printf("Error loading character set utf8: %s\n", $mysqli->error);
										    exit();
										} 
										
										$sql="SELECT tabelle_auftraggeber_gewerke.Gewerke_Nr, tabelle_auftraggeber_gewerke.Bezeichnung, tabelle_auftraggeber_gewerke.idTABELLE_Auftraggeber_Gewerke
												FROM tabelle_projekte INNER JOIN tabelle_auftraggeber_gewerke ON tabelle_projekte.TABELLE_Auftraggeber_Codes_idTABELLE_Auftraggeber_Codes = tabelle_auftraggeber_gewerke.TABELLE_Auftraggeber_Codes_idTABELLE_Auftraggeber_Codes
												WHERE (((tabelle_projekte.idTABELLE_Projekte)=".$_SESSION["projectID"]."))
												ORDER BY tabelle_auftraggeber_gewerke.Gewerke_Nr;";
										$sql = "SELECT tabelle_räume.`Raumbereich Nutzer`
												FROM tabelle_räume
												WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"]."))
												GROUP BY tabelle_räume.`Raumbereich Nutzer`
												ORDER BY tabelle_räume.`Raumbereich Nutzer`;";
												
										$result = $mysqli->query($sql);

										while($row = $result->fetch_assoc()) {
											echo "<option value=".$row["Raumbereich Nutzer"].">".$row["Raumbereich Nutzer"]."</option>";		
										}
									?>	
								</select>
								
							<label class="ml-sm-2 mr-sm-2" for='selectBestand'>inkl. Bestand</label>
							
								<select class='form-control form-control-sm' id='selectBestand' name='selectBestand'>
									<option value="1">Ja</option>
									<option value="0">Nein</option>
								</select>
							
							<button type='button' id='calculateCostsRoomArea' class='btn btn-outline-dark btn-sm' value='calculateCostsRoomArea'><i class="far fa-play-circle"></i> Berechnen</button>	
					</form>
				</div>
		  		<div class="card-body" id="costsRoomArea">
		  		</div>
			</div>
		</div>
	</div>
</div>

<script>
		
	// Kosten berechnen
	$("button[value='calculateCostsRoomArea']").click(function(){
		var bestandInkl = $('#selectBestand').val();
		var x = document.getElementById("selectRoomArea").selectedIndex;
                var y = document.getElementById("selectRoomArea").options;
                var roomArea = y[x].text;
		
                $.ajax({
                    url : "getRoomAreaCosts.php",
                    data:{"roomArea":roomArea ,"bestandInkl":bestandInkl},
                    type: "GET",
                    success: function(data){
                            $('#costsRoomArea').html(data);
                    }
                });	    
	});

	
</script>

</body>

</html>
