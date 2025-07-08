<?php
session_start();
?> 


<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>RB-Projekte</title>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="style.css" type="text/css" media="screen" />
<link rel="icon" href="iphone_favicon.png">
 
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css" integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>
  
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
  
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.18/b-1.5.2/b-html5-1.5.2/sl-1.2.6/datatables.min.css"/>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.18/b-1.5.2/b-html5-1.5.2/sl-1.2.6/datatables.min.js"></script>

<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/datatables.mark.js/2.0.0/datatables.mark.min.css"/>
<script type="text/javascript" src="https://cdn.datatables.net/plug-ins/1.10.13/features/mark.js/datatables.mark.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/mark.js/8.6.0/jquery.mark.min.js"></script>


 <!--
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4-4.0.0/jq-3.2.1/dt-1.10.16/datatables.min.css"/>
 <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css" integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous">
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4-4.0.0/jq-3.2.1/dt-1.10.16/datatables.min.js"></script>
 -->

 <style>

.btn-sm {
  height: 22px;
  padding: 2px 5px;
  font-size: 12px;
  line-height: 1.5; /* If Placeholder of the input is moved up, rem/modify this. */
  border-radius: 3px;
}

</style>
 
</head>
<body>
<?php
if(!isset($_SESSION["username"]))
{
    echo "Bitte erst <a href=\"index.php\">einloggen</a>";
    exit;
}

?>
    
<div class="container-fluid">
  <nav class="navbar navbar-expand-lg bg-light navbar-light">	
      <a class="py-0 navbar-brand" href="#"><img src="LIMET_logo.png" alt="LIMETLOGO" height="40"/></a>
          <ul class="navbar-nav">
              <?php 
                    if($_SESSION["ext"]==0){
                        echo "<ul class='navbar-nav'>
                              <li class='nav-item'><a class='py-0 nav-link' href='dashboard.php'><i class='fa fa-tachometer-alt'></i> Dashboard</a></li>
                            </ul>";
                    }
                  ?>
            <li class="py-0 nav-item dropdown">
              <a class="py-0 nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#"><i class='fa fa-list-alt'></i> Projekte</a>
              <ul class="dropdown-menu">                  
                  <a class="dropdown-item" href="projects.php"><i class='fa fa-list-alt'></i> Projektauswahl</a> 
                  <?php 
                        if($_SESSION["ext"]==0){
                            echo "<a class='dropdown-item' href='projectParticipants.php'><i class='fa fa-users'></i> Projektbeteiligte</a>
                                  <a class='dropdown-item' href='documentationV2.php'><i class='fa fa-comments'></i> Dokumentation</a>";
                        }
                    ?>
              </ul>
            </li>
              <?php 
                    if($_SESSION["ext"]==0){
                        echo "<li class='nav-item dropdown'>
                                <a class=' py-0 nav-link dropdown-toggle' data-bs-toggle='dropdown' href='#'><i class='fa fa-book'></i> Raumbuch</a>              
                                <ul class='dropdown-menu'>
                                    <a class='dropdown-item' href='roombookSpecifications.php'>Raumbuch - Bauangaben</a>
                                    <a class='dropdown-item' href='roombookMeeting.php'>Raumbuch - Meeting</a>
                                    <a class='dropdown-item' href='roombookDetailed.php'>Raumbuch - Detail</a>
                                    <a class='dropdown-item' href='roombookBO.php'>Raumbuch - Betriebsorganisation</a>
                                    <a class='dropdown-item' href='roombookReports.php'>Raumbuch - Berichte</a>
                                    <a class='dropdown-item' href='elementsInProject.php'>Elemente im Projekt</a>
                                </ul>
                              </li>
                              <li class='nav-item dropdown'>
                                <a class='py-0 nav-link dropdown-toggle' data-bs-toggle='dropdown' href='#'><i class='fa fa-euro-sign'></i> Kosten</a>              
                                <ul class='dropdown-menu'>
                                    <a class='dropdown-item' href='costsOverall.php'>Kosten - Berichte</a> 
                                    <a class='dropdown-item' href='costsRoomArea.php'>Kosten - Raumbereich</a>
                                    <a class='dropdown-item' href='costChanges.php'>Kosten - Änderungen</a>
                                    <a class='dropdown-item' href='elementBudgets.php'>Kosten - Budgets</a>
                                </ul>
                              </li>";
                    }
                ?>            	                 
            <li class="py-0 nav-item dropdown">
              <a class="py-0 nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#"><i class='fa fa-recycle'></i> Bestand</a>
              <ul class="dropdown-menu">
                  <a class="dropdown-item" href="roombookBestand.php">Bestand - Raumbereich</a>	
                  <a class="dropdown-item" href="roombookBestandElements.php">Bestand - Gesamt</a>
              </ul>
            </li>
            <li class="py-0 nav-item dropdown">
              <a class="py-0 nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#"><i class='fa fa-tasks'></i> Ausschreibungen</a>
              <ul class="dropdown-menu">
                    <a class="dropdown-item" href="tenderLots.php">Los-Verwaltung</a>
                    <a class="dropdown-item" href="tenderCalendar.php">Vergabekalender</a>
                    <?php 
                        if($_SESSION["ext"]==0){
                            echo "<a class='dropdown-item' href='tenderCharts.php'>Vergabe-Diagramme</a>";
                        }
                    ?>
                    <a class="dropdown-item" href="elementLots.php">Element-Verwaltung</a>
              </ul>
            </li>
              <li class="py-0 nav-item dropdown">
              <a class="py-0 nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#"><i class='fas fa-wrench'></i> Ausführung-ÖBA</a>
              <ul class="dropdown-menu">
                <a class="dropdown-item" href="dashboardAusfuehrung.php"><i class='fas fa-tachometer-alt'></i> Dashboard</a>
                <a class="dropdown-item" href="roombookAusfuehrung.php"><i class='fas fa-building'></i> Räume</a>
                <a class="dropdown-item" href="roombookAusfuehrungLiefertermine.php"><i class='far fa-calendar-alt'></i> Liefertermine</a>
              </ul>
            </li>
             <li class="py-0 nav-item dropdown">
                <a class="py-0 nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#"><i class='fas fa-camera'></i> Fotos</a>
                <ul class="dropdown-menu">
                    <a class='dropdown-item active' href='imageGallery.php'><i class='fas fa-images'></i> Gesamt</a>
                    <a class='dropdown-item' href='imageRoomgallery.php'><i class='fas fa-sitemap'></i> Raumzuordnung</a>
                </ul>
            </li>
          
          <?php 
                if($_SESSION["ext"]==0){
                    echo "<li class='py-0 nav-item dropdown'>
                                <a class='py-0 nav-link dropdown-toggle' data-bs-toggle='dropdown' href='#'><i class='fa fa-buromobelexperte '></i> Datenbank-Verwaltung</a>              
                                <ul class='dropdown-menu'>
                                    <a class='dropdown-item' href='elementAdministration.php'>Elemente-Verwaltung</a>
                                    <a class='dropdown-item' href='elementeCAD.php'>Elemente-CAD</a>
                                </ul>
                           </li>    
                        <ul class='navbar-nav'>
                          <li class='nav-item'><a class='py-0 nav-link' href='firmenkontakte.php'><i class='fa fa-address-card'></i> Firmenkontakte</a></li>
                        </ul>";
                }
            ?>
              </ul>
          <ul class="navbar-nav ml-auto">
              <li class="py-0 nav-item "><a class="py-0 nav-link text-success disabled" id="projectSelected">Aktuelles Projekt: <?php  if ($_SESSION["projectName"] != ""){echo $_SESSION["projectName"];}else{echo "Kein Projekt ausgewählt!";}?></a></li>
              <li><a class="py-0 nav-link" href="logout.php"><i class="fa fa-sign-out-alt"></i>Logout</a></li>
          </ul>              
    </nav>
    <div class='mt-4 row'>  
        <div class='col-xxl-12'>
            <div class="mt-4 card">
                <div class="card-header"><b>Projektfotos </b>
                    <label class="float-right">
                        <button type='button' id='addImage' class='btn btn-outline-dark btn-sm' value='Bild hinzufügen' style='visibility:visible'><i class='fas fa-plus'></i> Bild hinzufügen</button>
                    </label>
                </div>
                <div class="card-body">                    
                    <?php
                        /* Abfragen der Fotos */
                        $mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');	

                        /* change character set to utf8 */
                        if (!$mysqli->set_charset("utf8")) {
                            printf("Error loading character set utf8: %s\n", $mysqli->error);
                            exit();
                        }

                        // Abfrage aller Bilddateien im Projekt                      
                        $sql = "SELECT `tabelle_Files`.`idtabelle_Files`,
                                `tabelle_Files`.`Name`
                                FROM `LIMET_RB`.`tabelle_Files`
                                WHERE `tabelle_Files`.`tabelle_projekte_idTABELLE_Projekte`= ".$_SESSION["projectID"]." AND `tabelle_Files`.`tabelle_filetype_id` = 1;";
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
                                            <button type='button' class='float-right btn btn-outline-dark btn-sm' value='deleteImage' id='".$row["idtabelle_Files"]."'><i class='fas fa-trash-alt'></i></button>                                            
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
                </div>
            </div>
        </div>
    </div> 
</div>
<!-- MODALS SECTION -->
<!-- Modal für Bild-Upload -->
<div class='modal fade' id='uploadImageModal' role='dialog' tabindex="-1">
    <div class='modal-dialog modal-md'>
        <!-- Modal content-->
        <div class='modal-content'>
            <div class='modal-header'>	          
              <h4 class='modal-title'><i class='fas fa-upload'></i> Bild uploaden</h4>
              <button type='button' class='close' data-bs-dismiss='modal'>&times;</button>
            </div>
            <div class='modal-body' id='mbody'>
              <form role='form' id="uploadForm" enctype="multipart/form-data">   
                  <div class='form-group'>
                      <label for='imageUpload'>Bild auswählen(.jpeg):</label>
                      <input type="file" name="imageUpload" id="imageUpload"> <br>
                      <img id="image">
                  </div>                         
              </form>              
            </div>
            <div class='modal-footer'>
              <input type='button' id='uploadImageButton' class='btn btn-outline-dark btn-sm' value='Upload' data-bs-dismiss='modal'></input>
            </div>
        </div>
    </div>
</div>    

</body>        
<script>       
    $("#addImage").click(function(){                                            
        $('#uploadImageModal').modal('show'); 
    });
    
    $("#uploadImageButton").click(function(){
        //get selected Image
        //var input = document.getElementById("imageUpload").files;
        var file = document.querySelector('#imageUpload').files[0]; 
        if (!file) {
            alert("Bitte Datei auswählen!");
        } 
        else {
            //define the width to resize -> 1000px
            var resize_width = 800;//without px
            //create a FileReader
            var reader = new FileReader();
            //image turned to base64-encoded Data URI.
            reader.readAsDataURL(file);
            reader.name = file.name;//get the image's name
            reader.size = file.size; //get the image's size

            //Resize the image
            reader.onload = function(event) {
                var imageResized = new Image();//create a image
                imageResized.src = event.target.result;//result is base64-encoded Data URI
                imageResized.name = event.target.name;//set name (optional)
                imageResized.size = event.target.size;//set size (optional)
                imageResized.onload = function(el) {
                    var elem = document.createElement('canvas');//create a canvas
                    //scale the image and keep aspect ratio
                    var scaleFactor = resize_width / el.target.width;
                    elem.width = resize_width;
                    elem.height = el.target.height * scaleFactor;
                    //draw in canvas
                    var ctx = elem.getContext('2d');
                    ctx.drawImage(el.target, 0, 0, elem.width, elem.height);
                    //get the base64-encoded Data URI from the resize image
                    var srcEncoded = ctx.canvas.toDataURL('image/jpeg', 1);
                    //assign it to thumb src
                    document.querySelector('#image').src = srcEncoded;

                    /*Now you can send "srcEncoded" to the server and
                    convert it to a png o jpg. Also can send
                    "el.target.name" that is the file's name.*/
                    var resized = document.querySelector('#image').src;
                    //var resized = document.getElementById("image").files;

                    var formData = new FormData();
                    //formData.append("fileUpload", files[0]);
                    formData.append("fileUpload", resized);
                    //formData.append("vermerkID",vermerkID);

                    var xhttp = new XMLHttpRequest();

                    // Set POST method and ajax file path
                    xhttp.open("POST", "uploadFileImage.php", true);

                    // call on request changes state
                    xhttp.onreadystatechange = function() {
                       if (this.readyState == 4 && this.status == 200) {
                           alert(this.responseText);
                       }
                    };
                    // Send request with data
                    xhttp.send(formData);                     
                }
            } 
        }      
    });
    
    
    // Bild löschen
    $("button[value='deleteImage']").click(function(){                
        var imageID = this.id;
        
        $.ajax({
            url : "deleteImage.php",
            data:{"imageID":imageID},
            type: "GET",	        
            success: function(data){
                alert(data);
                // Neu Laden der Vermerkliste
                window.location.replace("imageGallery.php");
            }
        });	  
    });
</script>
</html> 
