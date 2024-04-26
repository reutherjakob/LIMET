<?php
// Abfragen der Funktionsteilstellen
                            
?>

<!-- Modal zum Ändern des Raumes -->
    <div class='modal fade' id='addRoomModal' role='dialog'>
      <div class='modal-dialog modal-md'>

        <!-- Modal content-->
        <div class='modal-content'>
          <div class='modal-header'>            
            <h4 class='modal-title'>Raum ändern</h4>
            <button type='button' class='close' data-dismiss='modal'>&times;</button>
          </div>
            <div class='modal-body' id='mbody'>
                <form role="form">       			        			        		
                    <div class="form-group">
                      <label for="nummer">Nummer:</label>
                      <input type="text" class="form-control form-control-sm" id="nummer"/>
                    </div>
                    <div class="form-group">
                      <label for="name">Name:</label>
                      <input type="text"  class="form-control form-control-sm" id="name"/>
                    </div>
                    
<!--                    <div class="form-group">
                      <label for="raumbereich">Raumbereich-Nutzer:</label>
                      <input type="text"  class="form-control form-control-sm" id="raumbereich"/>
                    </div>
                    <div class="form-group">
                      <label for="geschoss">Geschoss:</label>
                      <input type="text"  class="form-control form-control-sm" id="geschoss"/>
                    </div>-->
<!--                    <div class="form-group">
                      <label for="bauetappe">Bauetappe:</label>
                      <input type="text"  class="form-control form-control-sm" id="bauetappe"/>
                    </div>-->
<!--                    <div class="form-group">
                      <label for="bauteil">Bauteil:</label>
                      <input type="text"  class="form-control form-control-sm" id="bauteil"/>
                    </div>-->
                    <div class='form-group'>
                        <label for='funktionsstelle'>Funktionsstelle wählen:</label>
                            <select class='form-control form-control-sm' id='funktionsstelle'>
                                <option value=0 selected>Funktionsstelle wählen</option>
                                   <?php                                        
                                    $mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');	
                            
                            /* change character set to utf8 */
                            if (!$mysqli->set_charset("utf8")) {
                                printf("Error loading character set utf8: %s\n", $mysqli->error);
                                exit();
                            }     
                                   $funktionsTeilstellen = array();
                                    $sql = "SELECT tabelle_funktionsteilstellen.Nummer, tabelle_funktionsbereiche.Bezeichnung, tabelle_funktionsstellen.Bezeichnung, tabelle_funktionsteilstellen.Bezeichnung AS bez3, tabelle_funktionsteilstellen.idTABELLE_Funktionsteilstellen
                                            FROM (tabelle_funktionsteilstellen INNER JOIN tabelle_funktionsstellen ON tabelle_funktionsteilstellen.TABELLE_Funktionsstellen_idTABELLE_Funktionsstellen = tabelle_funktionsstellen.idTABELLE_Funktionsstellen) 
                                            INNER JOIN tabelle_funktionsbereiche ON tabelle_funktionsstellen.TABELLE_Funktionsbereiche_idTABELLE_Funktionsbereiche = tabelle_funktionsbereiche.idTABELLE_Funktionsbereiche;;";

                                    $result = $mysqli->query($sql);
                                    while($row = $result->fetch_assoc()) {
                                        $funktionsTeilstellen[$row['idTABELLE_Funktionsteilstellen']]['idTABELLE_Funktionsteilstellen'] = $row['idTABELLE_Funktionsteilstellen'];
                                        $funktionsTeilstellen[$row['idTABELLE_Funktionsteilstellen']]['Nummer'] = $row['Nummer'];
                                        $funktionsTeilstellen[$row['idTABELLE_Funktionsteilstellen']]['Name'] = $row['bez3'];
                                    }

                                    $mysqli ->close();
                                   
                                        foreach($funktionsTeilstellen as $array) {                                                                                                                            
                                            echo "<option value=".$array['idTABELLE_Funktionsteilstellen'].">".$array['Nummer']." - ".$array['Name']."</option>";                                                             		
                                        }
                                    ?>
                            </select>						
                    </div>
                    <div class="form-group">
                      <label for="mt-relevant">MT-relevant:</label>
                      <select class="form-control form-control-sm" id="mt-relevant">
                          <option value="0">Nein</option>
                          <option value="1">Ja</option>
                      </select>
                    </div>
                </form>
            </div>
            <div class='modal-footer'> 
                  <input type='button' id='saveNewRoom' class='btn btn-warning btn-sm' value='Speichern'></input>
                  <button type='button' class='btn btn-default btn-sm' data-dismiss='modal'>Abbrechen</button>
            </div>
        </div>
      </div>
    </div>
