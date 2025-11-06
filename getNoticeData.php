<?php
session_start();
$_SESSION["noticeID"] = $_GET["noticeID"];
?>

<!DOCTYPE html>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<link rel="stylesheet" href="css/style.css" type="text/css" media="screen" />
 <!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css"/>

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
	$mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');
	
	
	/* change character set to utf8 */
	if (!$mysqli->set_charset("utf8")) {
	    printf("Error loading character set utf8: %s\n", $mysqli->error);
	    exit();
	} 
					
	$sql="SELECT Notiz, Kategorie, Notiz_bearbeitet FROM tabelle_notizen WHERE idTABELLE_Notizen=".$_GET["noticeID"];
	$result = $mysqli->query($sql);
	$row = $result->fetch_assoc();
	/*
	echo "<form class='form-horizontal' role='form'>
						<div class='form-group'>
							<label class='control-label' for='selectCategory'>Notizkategorie wählen:</label>
							<div>
								<select class='form-control' id='selectCategory' name='selectCategory'>";
									if($row["Kategorie"] == "Medgas"){
										echo "<option selected>Medgas</option>
										<option>Elemente</option>
										<option>Elektro</option>";		
									}	
									else{
										if($row["Kategorie"] == "Elemente"){
											echo "<option>Medgas</option>
											<option selected>Elemente</option>
											<option>Elektro</option>";
										}
										else{
											if($row["Kategorie"] == "Elektro"){
												echo "<option>Medgas</option>
												<option>Elemente</option>
												<option selected>Elektro</option>";
											}
										}
										
									}
								echo "</select>									
							</div>
						</div>
						<div class='form-group'>
							<textarea class='form-control' id='notice".$_SESSION["roomID"]."' rows='3'>".$row["Notiz"]."</textarea>
						</div>
						<div class='form-group'>
							<input type='button' id='".$_SESSION["roomID"]."' class='btn btn-info btn-sm' value='Neue Notiz anlegen'></input>
							<!--
							<input type='button' id='".$_SESSION["roomID"]."' class='btn btn-warning btn-sm' value='Vorhandene Notiz ändern'></input>
							<input type='button' id='".$_SESSION["roomID"]."' class='btn btn-danger btn-sm' value='Notiz löschen'></input>
							-->
						</div>
				  	</form>";
				  	
	*/	  	
	echo "<form>
			<div class='col-xxl-12'>
				<div class='form-group row'>
					<label class='control-label col-xxl-3' for='selectCategory'>Notizkategorie wählen:</label>
					<div class='col-xxl-3'>
						<select class='form-control input-sm' id='selectCategory' name='selectCategory'>";
							if($row["Kategorie"] == "Medgas"){
										echo "<option selected>Medgas</option>
										<option>Elemente</option>
										<option>Elektro</option>";		
									}	
									else{
										if($row["Kategorie"] == "Elemente"){
											echo "<option>Medgas</option>
											<option selected>Elemente</option>
											<option>Elektro</option>";
										}
										else{
											if($row["Kategorie"] == "Elektro"){
												echo "<option>Medgas</option>
												<option>Elemente</option>
												<option selected>Elektro</option>";
											}
										}
										
									}
								echo "</select>	
					</div>
					<label class='control-label col-xxl-3' for='selectStatus'>Notizstatus:</label>
					<div class='col-xxl-3'>
						<select class='form-control input-sm' id='selectStatus' name='selectStatus'>";
							if($row["Notiz_bearbeitet"] == "0"){
										echo "<option value='0' selected>Offen</option>
										<option value='1'>Bearbeitet</option>
										<option value='2'>Info</option>";		
									}	
									else{
										if($row["Notiz_bearbeitet"] == "1"){
											echo "<option >Offen</option>
											<option selected>Bearbeitet</option>
											<option >Info</option>";										
										}
										else{
											if($row["Notiz_bearbeitet"] == "2"){
												echo "<option >Offen</option>
												<option>Bearbeitet</option>
												<option selected>Info</option>";											
											}
										}
										
									}
								echo "</select>	
					</div>

				</div>
				
				<div class='form-group row'>
					<textarea class='form-control' id='notice".$_SESSION["roomID"]."' rows='3'>".$row["Notiz"]."</textarea>
				</div>";
				if($_GET['newNoticeButton']==""){
					echo "<div class='form-group row'>
						<input type='button' id='".$_SESSION["roomID"]."' class='btn btn-warning btn-sm' value='Neue Notiz anlegen'></input>
					</div>";
				}
			echo "</div>
	  	</form>";

	$mysqli ->close();
	
?>
<script>
// Neue Notiz
$("input[value='Neue Notiz anlegen']").click(function(){
		var id=this.id; 
 		 var notice= $("#notice"+id).val();
	     var category= $("#selectCategory").val();
		 $.ajax({
	        url : "addNotice.php",
	        data:{"roomID":id,"Notiz":notice,"Kategorie":category},
	        type: "POST",
	        success: function(data){
	        	alert(data);
	            $.ajax({
			        url : "getRoomNotices.php",
			        type: "GET",
			        success: function(data){
			            $("#roomNotices").html(data);
			        } 
		        });
	        } 
        }); 
    });
    
	//Notizstatus
	$('#selectStatus').change(function(){
		var status = $('#selectStatus').val();
		
	    $.ajax({
	        url : "saveNoticeStatus.php",
	        data:{"status":status},
	        type: "GET",
	        success: function(data){
		    	alert(data);
		    	$.ajax({
			        url : "getRoomNotices.php",
			        type: "GET",
			        success: function(data){
			            $("#roomNotices").html(data);
			        }
	    		});	
	    		$.ajax({
			        url : "getProjectNotices.php",
			        type: "GET",
			        success: function(data){
			            $("#projectNotices").html(data);
			        }
	    		});		         
		            
			}
	    });
		
		
	});

</script>

</body>
</html>