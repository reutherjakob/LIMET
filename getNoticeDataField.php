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
	echo "<form>
			<div class='col-xxl-12'>
				<div class='form-group row'>
					<label class='control-label col-xxl-2' for='selectCategory'>Notizkategorie wählen:</label>
					<div class='col-xxl-4'>
						<select class='form-control input-sm' id='selectCategory' name='selectCategory'>
							<option selected>Medgas</option>
							<option>Elemente</option>
							<option>Elektro</option>		
						</select>
					</div>
				</div>
				
				<div class='form-group row'>
					<textarea class='form-control' id='notice".$_SESSION["roomID"]."' rows='3'></textarea>
				</div>
				<div class='form-group row'>
					<input type='button' id='".$_SESSION["roomID"]."' class='btn btn-warning btn-sm' value='Neue Notiz anlegen'></input>
					<!-- <input type='button' id='".$_SESSION["roomID"]."' class='btn btn-warning btn-sm' value='Vorhandene Notiz ändern'></input>
					<input type='button' id='".$_SESSION["roomID"]."' class='btn btn-danger btn-sm' value='Notiz löschen'></input> 
					-->
				</div>
			</div>
	  	</form>";



		/*			
	echo "<div class='row'>
			<div class='col-xxl-12' id='noticeCategory'>
				<label for='selectCategory'>Notizkategorie:</label>
				<select class='form-control' id='selectCategory' name='selectCategory'>
				<option selected>Medgas</option>
				<option>Elemente</option>
				<option>Elektro</option>		
				</select>
			</div>
		</div>
	  	<div class='row' style='height: 10%;'>
	  		<div class='col-xxl-12'><textarea class='form-control' id='notice".$_SESSION["roomID"]."' style='width:100%; height: 100%'></textarea></div>
		</div>
		<div class='row'>
			<div class='col-xxl-12'>
		  			<input type='button' id='".$_SESSION["roomID"]."' class='btn btn-info' value='Neu'></input>
					<input type='button' id='".$_SESSION["roomID"]."' class='btn btn-warning' value='Speichern'></input>
					<input type='button' id='".$_SESSION["roomID"]."' class='btn btn-danger' value='Löschen'></input>
			</div>
		</div>";  		
	*/
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
    
</script>

</body>
</html>