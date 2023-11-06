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
	$mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');
                          
        $sql = "SELECT f.`idtabelle_Files`, f.`Name` FROM `tabelle_Files` f WHERE f.`idtabelle_Files`
                NOT IN (SELECT `tabelle_Files`.`idtabelle_Files`
                    FROM `LIMET_RB`.`tabelle_Files` INNER JOIN `tabelle_Files_has_tabelle_Raeume` ON `tabelle_Files_has_tabelle_Raeume`.`tabelle_idfFile` = `tabelle_Files`.`idtabelle_Files`
                    WHERE `tabelle_Files`.`tabelle_projekte_idTABELLE_Projekte`= ".$_SESSION["projectID"]." AND `tabelle_Files`.`tabelle_filetype_id` = 1 AND `tabelle_Files_has_tabelle_Raeume`.`tabelle_idRaeume` = ".filter_input(INPUT_GET, 'roomID').")
                AND f.`tabelle_projekte_idTABELLE_Projekte`= ".$_SESSION["projectID"].";";      
        
        $result = $mysqli->query($sql);

        $imageCounter = 0;
        echo "<div class='row'>"; 
        while($row = $result->fetch_assoc()) {
            if($imageCounter > 0 && fmod($imageCounter, 8) == 0){
                echo "</div>";
                echo "<div class='row'>";                                 
            }         
                echo "<div class='m-1 card'>";
                    echo "<div class='card-header'>   
                            <button type='button' id='".$row["idtabelle_Files"]."' class='float-right btn btn-outline-success btn-xs' value='addImageToRoom'><i class='fas fa-plus'></i></button>                                           
                         </div>";
                    echo "<div class='card-body'>";
                        echo "<img src='https://limet-rb.com/Dokumente_RB/Images/".$row['Name']."' height='200' width='200'>";
                    echo "</div>";
                echo "</div>";
            $imageCounter++;
        }
        echo "</div>";
        $mysqli ->close();
                                               	
?>

<script>
    var roomID = <?php echo filter_input(INPUT_GET, 'roomID') ?>
    // Bild zu Raum hinzufügen
    $("button[value='addImageToRoom']").click(function(){                
        var imageID = this.id;
        
        $.ajax({
            url : "addImageToRoom.php",
            data:{"imageID":imageID,"roomID":roomID},
            type: "GET",	        
            success: function(data){
                alert(data);
                $.ajax({
                    url : "getImagesToRoom.php",
                    data:{"roomID":roomID},
                    type: "GET",
                    success: function(data){			            
                        $("#roomImages").html(data); 
                        $.ajax({
                            url : "getImagesNotInRoom.php",
                            data:{"roomID":roomID},
                            type: "GET",
                            success: function(data){			            
                                $("#projectImages").html(data);    
                            } 
                        });
                    } 
                });
            }
        });	  
    });
</script>
</body>
</html>