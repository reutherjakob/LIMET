<?php
//
//include '_utils.php';
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
                            $mysqli = utils_connect_sql();
                            $funktionsTeilstellen = array();
                            $sql = "SELECT tabelle_funktionsteilstellen.Nummer, tabelle_funktionsbereiche.Bezeichnung, tabelle_funktionsstellen.Bezeichnung, tabelle_funktionsteilstellen.Bezeichnung AS bez3, tabelle_funktionsteilstellen.idTABELLE_Funktionsteilstellen
                                            FROM (tabelle_funktionsteilstellen INNER JOIN tabelle_funktionsstellen ON tabelle_funktionsteilstellen.TABELLE_Funktionsstellen_idTABELLE_Funktionsstellen = tabelle_funktionsstellen.idTABELLE_Funktionsstellen) 
                                            INNER JOIN tabelle_funktionsbereiche ON tabelle_funktionsstellen.TABELLE_Funktionsbereiche_idTABELLE_Funktionsbereiche = tabelle_funktionsbereiche.idTABELLE_Funktionsbereiche;;";

                            $result = $mysqli->query($sql);
                            while ($row = $result->fetch_assoc()) {
                                $funktionsTeilstellen[$row['idTABELLE_Funktionsteilstellen']]['idTABELLE_Funktionsteilstellen'] = $row['idTABELLE_Funktionsteilstellen'];
                                $funktionsTeilstellen[$row['idTABELLE_Funktionsteilstellen']]['Nummer'] = $row['Nummer'];
                                $funktionsTeilstellen[$row['idTABELLE_Funktionsteilstellen']]['Name'] = $row['bez3'];
                            }

                            $mysqli->close();

                            foreach ($funktionsTeilstellen as $array) {
                                echo "<option value=" . $array['idTABELLE_Funktionsteilstellen'] . ">" . $array['Nummer'] . " - " . $array['Name'] . "</option>";
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




<!-- MODAL Visiblities--><!--
<div class='modal fade modal-lg' id='VisModal' role='dialog' tabindex='-1' role='dialog' aria-labelledby='exampleModalLabel' aria-hidden='true'>
    <div class='modal-dialog modal-lg modal-dialog-centered' role='document'>
        <div class='modal-content'>
            <div class='modal-header'>            
                <h4 class='modal-title'>Spalte aus-/einblenden</h4>
                <button type='button' class='close' data-dismiss='modal'>×</button>
            </div>
            <div class='modal-body' id='mbodyy'>
                <form role="form">       		
                    <div class="form-group" id ="CBXs"> 
                         populate MOdal Dynamically here
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>-->
<script>/*function populate_modal() {
var columnsPerRow = 4;
var rows = Math.ceil(columnsDefinition.length - 5 / columnsPerRow);
for (var i = 0; i < rows; i++) {
var row = $('<div class="row"></div>');
for (var j = 0; j < columnsPerRow; j++) {
var index = i * columnsPerRow + j + 5;
if (index < columnsDefinition.length) {
var columnDiv = $('<div class="col-sm-3"><div class="checkbox"><label><input type="checkbox" value="' + index + '" checked>' + columnsDefinition[index].title + '</label></div></div>');
row.append(columnDiv);
}
}
$('#mbodyy .form-group').append(row);
}
}

function init_vis_modal_functionality() {
$('#VisModal').on('show.bs.modal', function () {
console.log('Modal is being shown');
$('#CBXs input:checkbox').each(function () {
var column = table.column($(this).val());
console.log('Checkbox value: ' + $(this).val() + ', column visibility: ' + column.visible());
$(this).prop('checked', column.visible());
});
});
$('#CBXs').on('click', 'input:checkbox', function () {
console.log('Checkbox clicked. Value: ' + $(this).val() + ', checked: ' + $(this).prop('checked'));
var column = table.column($(this).val());
column.visible(!column.visible());
});
} */</script>

