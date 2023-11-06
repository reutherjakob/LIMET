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
                          
        $sql = "SELECT `tabelle_Files`.`idtabelle_Files`, `tabelle_Files`.`Name`, `tabelle_Files_has_tabelle_Raeume`.`tabelle_idRaeume`
                FROM `tabelle_Files` 
                        INNER JOIN `tabelle_Files_has_tabelle_Raeume` ON `tabelle_Files_has_tabelle_Raeume`.`tabelle_idfFile` = `tabelle_Files`.`idtabelle_Files`
                WHERE `tabelle_Files_has_tabelle_Raeume`.`tabelle_idRaeume` = ".filter_input(INPUT_GET, 'roomID').";";
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
                            <button type='button' id='".$row["idtabelle_Files"]."' class='float-right btn btn-outline-danger btn-xs' value='removeImageFromRoom'><i class='fas fa-minus'></i></button>   
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
    $("button[value='removeImageFromRoom']").click(function(){                
        var imageID = this.id;
        
        $.ajax({
            url : "deleteImageFromRoom.php",
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