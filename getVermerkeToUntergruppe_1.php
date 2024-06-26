<?php
session_start();
?>

<!DOCTYPE html>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<html>
<head>
    <style>
        
        
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

<?php
	$mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');
		
	/* change character set to utf8 */
	if (!$mysqli->set_charset("utf8")) {
	    printf("Error loading character set utf8: %s\n", $mysqli->error);
	    exit();
	} 
        				                                            
        
        $sql = "SELECT tabelle_Vermerke.idtabelle_Vermerke, tabelle_Vermerke.tabelle_räume_idTABELLE_Räume, tabelle_Vermerke.tabelle_lose_extern_idtabelle_Lose_Extern, tabelle_Vermerke.Ersteller, tabelle_Vermerke.Erstellungszeit, tabelle_Vermerke.Vermerktext, tabelle_Vermerke.Bearbeitungsstatus, tabelle_Vermerke.Vermerkart, tabelle_Vermerke.Faelligkeit, tabelle_räume.Raumnr, tabelle_lose_extern.LosNr_Extern
                FROM tabelle_lose_extern RIGHT JOIN (tabelle_räume RIGHT JOIN tabelle_Vermerke ON tabelle_räume.idTABELLE_Räume = tabelle_Vermerke.tabelle_räume_idTABELLE_Räume) ON tabelle_lose_extern.idtabelle_Lose_Extern = tabelle_Vermerke.tabelle_lose_extern_idtabelle_Lose_Extern
                WHERE (((tabelle_Vermerke.tabelle_Vermerkuntergruppe_idtabelle_Vermerkuntergruppe)=".filter_input(INPUT_GET, 'vermerkUntergruppenID')."))
                ORDER BY tabelle_Vermerke.Erstellungszeit;";
        
        $result = $mysqli->query($sql);
        
                                                            
        echo "<table class='table table-striped table-bordered table-sm' id='tableVermerke'  cellspacing='0' width='100%'>
                <thead><tr>
                <th>ID</th>
                <th></th>
                <th>Vermerk</th>
                <th>Ersteller</th>
                <th>Fälligkeit</th>
                <th>Erstellt am</th>
                <th>Status</th>
                <th>Vermerkart</th>
                <th>Zuständigkeit</th>
                <th>LosID</th>
                <th>RaumID</th>
                <th>Los</th>
                <th>Raum</th>
                </tr></thead><tbody>";   

        while ($row = $result->fetch_assoc()) {                                                    
            echo "<tr>";
            echo "<td>".$row['idtabelle_Vermerke']."</td>";
            echo "<td><button type='button' id='".$row['idtabelle_Vermerke']."' class='btn btn-outline-dark btn-xs' value='changeVermerk'><i class='fas fa-pencil-alt'></i></button></td>";
            echo "<td id='vermerktText".$row["idtabelle_Vermerke"]."' value ='".$row['Vermerktext']."'>".$row['Vermerktext']."</td>";
            echo "<td>".$row['Ersteller']."</td>";               
                if($row["Vermerkart"]!="Info"){
                    echo "<td id='faelligkeit".$row["idtabelle_Vermerke"]."' value ='".$row['Faelligkeit']."'>".$row['Faelligkeit']."</td>"; 
                }
                else{
                    echo "<td>";
                    echo "</td>";
                }                          
            echo "<td>".$row['Erstellungszeit']."</td>";  
            echo "<td id='bearbeitungsstatus".$row["idtabelle_Vermerke"]."' value ='".$row['Bearbeitungsstatus']."'>".$row['Bearbeitungsstatus']."</td>";  
            echo "<td id='vermerkTyp".$row["idtabelle_Vermerke"]."' value ='".$row['Vermerkart']."'>".$row['Vermerkart']."</td>";  
            echo "<td><button type='button' id=",$row['idtabelle_Vermerke'], " class='btn btn-outline-dark btn-xs' value='showVermerkZustaendigkeit' data-toggle='modal' data-target='#showVermerkZustaendigkeitModal'><i class='fas fa-users'></i></button></td>";
            echo "<td id='lot".$row["idtabelle_Vermerke"]."' value ='".$row['tabelle_lose_extern_idtabelle_Lose_Extern']."'>".$row['tabelle_lose_extern_idtabelle_Lose_Extern']."</td>";   
            echo "<td id='room".$row["idtabelle_Vermerke"]."' value ='".$row['tabelle_räume_idTABELLE_Räume']."'>".$row['tabelle_räume_idTABELLE_Räume']."</td>";                        
            echo "<td>".$row['LosNr_Extern']."</td>";
            echo "<td>".$row['Raumnr']."</td>";
            echo "</tr>";
        }
        echo "</tbody></table>";

       // echo "<button type='button' id='".filter_input(INPUT_GET, 'vermerkUntergruppenID')."' class='btn btn-success btn-sm' value='Neuer Vermerk'>Neuer Vermerk</button>";	
	
?>
    
        <!-- Modal zum Hinzufügen/Ändern eines Vermerks -->
	  <div class='modal fade' id='changeVermerkModal' role='dialog'>
	    <div class='modal-dialog modal-lg'>
	    
	      <!-- Modal content-->
	      <div class='modal-content'>
	        <div class='modal-header'>
                    <h4 class='modal-title'>Vermerkdaten</h4>
	          <button type='button' class='close' data-dismiss='modal'>&times;</button>
	          
	        </div>
	        <div class='modal-body' id='vermerkMbody'>
                        <form role="form">                                
                            <?php
                                    $sql = "SELECT tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung, tabelle_räume.idTABELLE_Räume, tabelle_räume.`Raumbereich Nutzer`
                                            FROM tabelle_räume
                                            WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"]."))
                                            ORDER BY tabelle_räume.Raumnr, tabelle_räume.`Raumbereich Nutzer`, tabelle_räume.Raumbezeichnung;";

                                    $result1 = $mysqli->query($sql);	        				        	

                                    echo "<div class='form-group'>
                                        <label for='room'>Raum:</label>									
                                        <select class='form-control form-control-sm' id='room' name='room'>
                                                <option value=0>Kein Raum</option>";
                                                while($row = $result1->fetch_assoc()) {
                                                      echo "<option value=".$row["idTABELLE_Räume"].">".$row["Raumnr"]." - ".$row["Raumbereich Nutzer"]." - ".$row["Raumbezeichnung"]."</option>";
                                                }	
                                        echo "</select>										
                                    </div>";
                                        
                                    
                                    $sql = "SELECT tabelle_lose_extern.idtabelle_Lose_Extern, tabelle_lose_extern.LosNr_Extern, tabelle_lose_extern.LosBezeichnung_Extern, tabelle_lieferant.Lieferant
                                            FROM tabelle_lose_extern LEFT JOIN tabelle_lieferant ON tabelle_lose_extern.tabelle_lieferant_idTABELLE_Lieferant = tabelle_lieferant.idTABELLE_Lieferant
                                            WHERE (((tabelle_lose_extern.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"]."))
                                            ORDER BY tabelle_lose_extern.LosNr_Extern;";

                                    $result1 = $mysqli->query($sql);	        				        	

                                    echo "<div class='form-group'>
                                        <label for='los'>Los:</label>									
                                        <select class='form-control form-control-sm' id='los' name='los'>
                                                <option value=0>Kein Los</option>";
                                                while($row = $result1->fetch_assoc()) {
                                                      echo "<option value=".$row["idtabelle_Lose_Extern"].">".$row["LosNr_Extern"]." - ".$row["LosBezeichnung_Extern"]." - ".$row["Lieferant"]."</option>";
                                                }	
                                        echo "</select>										
                                    </div>";
                                       
                                    // Untergruppen-Abfrage für Änderung der Untergruppe
                                    $sql = "SELECT tabelle_Vermerkuntergruppe.idtabelle_Vermerkuntergruppe, tabelle_Vermerkuntergruppe.Untergruppenname, tabelle_Vermerkuntergruppe.Untergruppennummer
                                            FROM tabelle_Vermerkuntergruppe
                                            WHERE (((tabelle_Vermerkuntergruppe.tabelle_Vermerkgruppe_idtabelle_Vermerkgruppe)=".filter_input(INPUT_GET, 'vermerkGruppenID')."))
                                            ORDER BY Untergruppennummer ASC;";

                                    $result1 = $mysqli->query($sql);	        				        	

                                    echo "<div class='form-group'>
                                        <label for='untergruppe'>Untergruppe:</label>									
                                        <select class='form-control form-control-sm' id='untergruppe' name='untergruppe'>";                                    
                                            while($row = $result1->fetch_assoc()) {
                                                if($row["idtabelle_Vermerkuntergruppe"]==filter_input(INPUT_GET, 'vermerkUntergruppenID')){
                                                    echo "<option value=".$row["idtabelle_Vermerkuntergruppe"]." selected>".$row["Untergruppennummer"]." - ".$row["Untergruppenname"]."</option>";
                                                }
                                                else{
                                                    echo "<option value=".$row["idtabelle_Vermerkuntergruppe"].">".$row["Untergruppennummer"]." - ".$row["Untergruppenname"]."</option>";
                                                }
                                            }	                                                                          
                                        echo "</select>										
                                    </div>";
                                        
                                    $mysqli ->close();
                            ?>
                            <div class='form-group'>
                                <label for='vermerkStatus'>Status:</label>									
                                    <select class='form-control form-control-sm' id='vermerkStatus' name='vermerkStatus'>
                                        <option value=0 selected>Offen</option>                                                  
                                        <option value=1>Erledigt</option>                                            
                                    </select>	
                            </div>
                            <div class='form-group'>
                                <label for='vermerkTyp'>Vermerktyp:</label>									
                                    <select class='form-control form-control-sm' id='vermerkTyp' name='vermerkTyp'>
                                        <option value='Info' selected>Info</option>                                                  
                                        <option value='Bearbeitung'>Bearbeitung</option>                                            
                                    </select>	
                            </div>
                            <div class="form-group">
                              <label for="faelligkeit">Fällig am:</label>
                              <input type="text" class="form-control form-control-sm" id="faelligkeit" placeholder="jjjj.mm.tt" disabled/>
                            </div>
                            <div class="form-group">
                                <label for="vermerkText">Text:</label>
                                <textarea class="form-control form-control-sm" rows="15" id="vermerkText" style="font-size:10pt"></textarea>
                            </div>
                            
                            <div class="form-group">
                                <div  class="form-control form-control-sm " rows="15" id="speech_result"> Sprach Transkript...</div>
                            </div>
                      </form>
                </div>
                  
	        <div class='modal-footer form-inline"'> 
                    <div  class="col-8" >
                        <input class="form-check-input" type="checkbox"  id="ContinousRecordCheckbox" > </input>
                        <label for="ContinousRecordCheckbox" class="form-check-label">continous </label>
                        
                        
                        <button id="recordBtn" class="btn btn-success ">Start Recording</button> 
                        <select id="languageSelect" class="form-select form-select-sm">
                            <option value="de-DE">German</option>
                            <option value="en-US">English</option> 
                        </select>
                    </div> 
                    
<script >
var isRecording;
var timer;

$('#recordBtn').on('click', function () {
    if ($('#recordBtn').text() === 'Start Recording') {
        startRecording();
    } else {
        stopRecording();
    }
});

function check_if_recording(){
    if ((isRecording && $('#recordBtn').text() !== "Recording..."  )||($('#recordBtn').text() === "Recording..."   &&  !isRecording)){
        console.log("Timer Stoped Record");
        stopRecording(); //reset btn iuf it automatically stops, which it does nd is annoying
    } else {
        console.log("SetInterval timer ", isRecording, speechRecognizer); 
    }
}

function startRecording() {
    if ("webkitSpeechRecognition"  in window || 'SpeechRecognition' in window  ) {
        speechRecognizer = new webkitSpeechRecognition() || new SpeechRecognition();
        speechRecognizer.continuous = true;
        speechRecognizer.interimResults = true;
        speechRecognizer.lang = "de-DE";
        speechRecognizer.start();     
        //we could add some words here, weight them, whatnot; 
        //const speechRecognitionList = new SpeechGrammarList();   
        isRecording = true;
        timer = setInterval(check_if_recording, 2000);
        $('#recordBtn').addClass('btn-danger').text('Recording...');
        
        var finalTranscripts = "";
        speechRecognizer.onresult = function (event) {
            var interimTranscripts = "";
            for (var i = event.resultIndex; i < event.results.length; i++) {
                var transcript = event.results[i][0].transcript;
                transcript.replace("\n", "<br>"); 
                if (event.results[i].isFinal) {
                    finalTranscripts += transcript;
                    $('#vermerkText').val(function (index, currentValue) {return currentValue + finalTranscripts + '\n';});
                    
                    stopRecording(); 
                    console.log(`Final Result. Confidence: ${event.results[0][0].confidence}`);   
                    if ($('#ContinousRecordCheckbox').prop('checked')) {
                        console.log("Re-Starting");
                        startRecording();
                        
                    } else{ 
                        stopRecording(); 
                    }
                } else {
                    interimTranscripts += transcript;
                } 
                document.getElementById("speech_result").innerHTML = finalTranscripts + '<span style="color: #999;">' + interimTranscripts + '</span>';
            }
        };
        speechRecognizer.onspeechend= function (event){ 
//            stopRecording();
            console.log("Speech has stopped being detected. isRecording:", isRecording);
//            if ($('#ContinousRecordCheckbox').prop('checked')) {
//                console.log("Re-Starting on speechend"); 
//                startRecording();}
        };
        
        speechRecognizer.onerror = function (event) {};
        
    } else {
        document.getElementById("speech_result").innerHTML = "Your browser does not support that.";
    }
}

function stopRecording() {
    $('#recordBtn').removeClass('btn-danger').text('Start Recording');
    if(speechRecognizer){
        speechRecognizer.stop();
        speechRecognizer = null;
    }
    clearInterval(timer);
    timer = false;
    console.log("RECORDING OFF!");
}
</script>
                    <div>
                        <input type='button' id='addVermerk' class='btn btn-success btn-sm' value='Hinzufügen' data-dismiss='modal'></input>
                        <input type='button' id='saveVermerk' class='btn btn-warning btn-sm' value='Speichern' data-dismiss='modal'></input>
                        <input type='button' id='deleteVermerk' class='btn btn-danger btn-sm' value='Löschen' data-dismiss='modal'></input>
                        <button type='button' class='btn btn-default btn-sm' data-dismiss='modal' onclick="stopRecording()" >Abbrechen</button>
                    </div>    
	        </div>
	      </div>
	      
	    </div>
	  </div> 
        
        <!-- Modal für Zustaendigkeit-->
	  <div class='modal fade' id='showVermerkZustaendigkeitModal' role='dialog'>
	    <div class='modal-dialog modal-lg'>
	    
	      <!-- Modal content-->
	      <div class='modal-content'>
	        <div class='modal-header'>
                    <h4 class='modal-title'>Zustaendigkeiten:</h4>
	          <button type='button' class='close' data-dismiss='modal'>&times;</button>
	          
	        </div>
	        <div class='modal-body' id='showZustaendigkeitenModalBody'>               
                    <div class="mt-4 card">
                        <div class="card-header">Eingetragene Zuständigkeit:</div>
                        <div class="card-body" id='vermerkZustaendigkeit'>    
                        </div>
                    </div>
                    <div class="mt-4 card">
                        <div class="card-header">Mögliche Personen:</div>
                        <div class="card-body" id='possibleVermerkZustaendigkeit'>    
                        </div>
                    </div>
                </div>
                <div class='modal-footer'>
                    <button type='button' class='btn btn-default btn-sm'  value='closeModal' data-dismiss='modal'>Schließen</button>
                </div>
	      </div>	  
	    </div>
	  </div>
        
        <!-- Modal zum Löschen eines Vermerks-->
	  <div class='modal fade' id='deleteVermerkModal' role='dialog'>
	    <div class='modal-dialog modal-sm'>	    
	      <!-- Modal content-->
	      <div class='modal-content'>
	        <div class='modal-header'>	          
	          <h4 class='modal-title'>Vermerk löschen</h4>
                  <button type='button' class='close' data-dismiss='modal'>&times;</button>
	        </div>
	        <div class='modal-body' id='mbody'>Wollen Sie den Vermerk wirklich löschen? Sämtliche Informationen gehen verloren.
	        </div>
	        <div class='modal-footer'>
	        	<input type='button' id='deleteVermerkExecute' class='btn btn-danger btn-sm' value='Ja' data-dismiss='modal'></input>
	          	<button type='button' class='btn btn-success btn-sm' data-dismiss='modal'>Nein</button>
	        </div>
	      </div>	      
	    </div>
	  </div>
        
        <!-- Modal für Bild-Upload -->
    <div class='modal fade' id='uploadImageModal' role='dialog'>
      <div class='modal-dialog modal-sm'>
        <!-- Modal content-->
        <div class='modal-content'>
          <div class='modal-header'>	          
            <h4 class='modal-title'>Bild uploaden</h4>
            <button type='button' class='close' data-dismiss='modal'>&times;</button>
          </div>
          <div class='modal-body' id='mbody'>
            <form role='form' id="uploadForm" enctype="multipart/form-data">   
                <div class='form-group'>
                    <input type='hidden' id='vermerkID'/>
                </div>
                <div class='form-group'>
                    <label for='imageUpload'>Bild (.jpeg):</label>
                    <input type="file" name="imageUpload" id="imageUpload"> <br>
                    <img id="image">
                </div>                         
            </form>              
          </div>
          <div class='modal-footer'>
            <input type='button' id='uploadImageButton' class='btn btn-outline-dark btn-sm' value='Upload' data-dismiss='modal'></input> 
          </div>
        </div>
      </div>
    </div>
        



<script>
    var vermerkID;
    var vermerkGruppenID = <?php echo filter_input(INPUT_GET, 'vermerkGruppenID') ?>  ;
    
    $(document).ready(function(){  
        document.getElementById("buttonNewVermerk").style.visibility = "visible";
        document.getElementById("buttonNewVermerkuntergruppe").style.visibility = "visible";        
        
    	$('#tableVermerke').DataTable( {
    		"columnDefs": [
                        {
                            "targets": [ 0,6,9,10 ],
                            "visible": false,
                            "searchable": false
                        },
                        {
                            "targets": [ 1 ],
                            "visible": true,
                            "searchable": false,
                            "sortable": false
                        }
                ],
                
                "paging": true,
                "pagingType": "simple",
                "lengthChange": false,
                "pageLength": 10,
                "searching": true,
                "info": true,
                "order": [[ 5, "asc" ]],
	        'language': {'url': '//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json'},
                "fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
                    if ( aData[7] === "Bearbeitung" )
                    {
                        if(aData[6] === "0"){
                            $('td', nRow).css('background-color', '#ff8080');
                        }
                        else{
                            $('td', nRow).css('background-color', '#b8dc6f');
                        }                        
                    }
                    else{
                        $('td', nRow).css('background-color', '#d3edf8');
                    }
                }, 
//                compact:true,
//                responsive:true,
                "scrollCollapse": true,
                "scrollY": '20vh'
                
        });
        
        // CLICK TABELLE
        var table2 = $('#tableVermerke').DataTable();

        $('#tableVermerke tbody').on( 'click', 'tr', function () {

            if ( $(this).hasClass('info') ) {
                //$(this).removeClass('info');
            }
            else {
                table2.$('tr.info').removeClass('info');
                $(this).addClass('info');
                vermerkID = table2.row( $(this) ).data()[0]; 
                document.getElementById("vermerkStatus").value = table2.row( $(this) ).data()[6]; 
                document.getElementById("vermerkText").value = table2.row( $(this) ).data()[2]; 
                document.getElementById("faelligkeit").value = table2.row( $(this) ).data()[4]; 
                document.getElementById("vermerkTyp").value = table2.row( $(this) ).data()[7];

                if(table2.row( $(this) ).data()[9] === ''){
                    document.getElementById("los").value = 0;
                }
                else{
                    document.getElementById("los").value = table2.row( $(this) ).data()[9];
                }
                if(table2.row( $(this) ).data()[10] === ''){
                    document.getElementById("room").value = 0;
                }
                else{
                    document.getElementById("room").value = table2.row( $(this) ).data()[10]; 
                }
                if(table2.row( $(this) ).data()[7] === "Bearbeitung"){
                    $("#faelligkeit").prop('disabled', false);
                }	
                else{
                    $("#faelligkeit").prop('disabled', true);
                }
                document.getElementById("addImage").style.visibility = "visible";
            }
        } );
        
        $('#faelligkeit').datepicker({
        format: "yyyy-mm-dd",
        calendarWeeks: true,
        autoclose: true,
        todayBtn: "linked",
        language: "de"
    });
        
        
    });                                               
            
    //$("button[value='Neuer Vermerk']").click(function(){     
    $("#buttonNewVermerk").click(function(){     
        document.getElementById("saveVermerk").style.display = "none";
        document.getElementById("deleteVermerk").style.display = "none";
        $("#untergruppe").prop('disabled', true);
        document.getElementById("addVermerk").style.display = "inline";
        $('#changeVermerkModal').modal('show'); 
    });
    
    $("#addVermerk").click(function(){
        stopRecording();
        var room = $("#room").val();
        var los = $("#los").val();        
        var vermerkStatus  = $("#vermerkStatus").val();
        var vermerkTyp = $("#vermerkTyp").val();
        var vermerkText = $("#vermerkText").val();        
        var faelligkeitDatum = $("#faelligkeit").val();
        
        if(vermerkTyp === "Info"){
            faelligkeitDatum = null;
        }
        var vermerkUntergruppenID = <?php echo filter_input(INPUT_GET, 'vermerkUntergruppenID') ?>;
                        
        if(room !== "" && los !== "" && vermerkStatus !== "" && vermerkTyp !== "" && vermerkText !== ""){
            $('#changeVermerkModal').modal('hide');
            $.ajax({
                url : "addVermerk.php",
                data:{"untergruppenID":vermerkUntergruppenID,"room":room,"los":los,"vermerkStatus":vermerkStatus,"vermerkTyp":vermerkTyp,"vermerkText":vermerkText,"faelligkeitDatum":faelligkeitDatum},
                type: "GET",	        
                success: function(data){
                    alert(data);
                    // Neu Laden der Vermerkliste
                    $.ajax({
                            url : "getVermerkeToUntergruppe.php",
                            data:{"vermerkUntergruppenID":vermerkUntergruppenID,"vermerkGruppenID":vermerkGruppenID},
                            type: "GET",
                            success: function(data){
                                $("#vermerke").html(data); 
                                // Neu laden der PDF-Vorschau
                                document.getElementById('pdfPreview').src += '';
                            } 
                        });  
                }
            });	            
        }
        else{
                alert("Bitte alle Felder ausfüllen!");
        }          
    });
    
    $('#vermerkTyp').change(function(){
	var typ = $('#vermerkTyp').val();
	if(typ === "Bearbeitung"){
            $("#faelligkeit").prop('disabled', false);
        }	
        else{
            $("#faelligkeit").prop('disabled', true);
        }
    });
    
    $("button[value='changeVermerk']").click(function(){
        // Buttons ein/ausblenden!
        document.getElementById("saveVermerk").style.display = "inline";
        document.getElementById("deleteVermerk").style.display = "inline";
        $("#untergruppe").prop('disabled', false);
        document.getElementById("addVermerk").style.display = "none";
        $('#changeVermerkModal').modal('show');         
    });
    
    // Vermerk ändern/speichern
    $("#saveVermerk").click(function(){        
        stopRecording();
        
        var room = $("#room").val();
        var los = $("#los").val();
        var vermerkStatus  = $("#vermerkStatus").val();
        var vermerkTyp = $("#vermerkTyp").val();
        var vermerkText = $("#vermerkText").val();        
        var faelligkeitDatum = $("#faelligkeit").val();
        var untergruppenID = $("#untergruppe").val();
        
        if(vermerkTyp === "Info"){
            faelligkeitDatum = null;
        }
        var vermerkUntergruppenID = <?php echo filter_input(INPUT_GET, 'vermerkUntergruppenID') ?>;
        
        if(room !== "" && los !== "" && vermerkStatus !== "" && vermerkTyp !== "" && vermerkText !== ""){
            $('#changeVermerkModal').modal('hide');
            $.ajax({
                url : "saveVermerk.php",
                data:{"vermerkID":vermerkID,"room":room,"los":los,"vermerkStatus":vermerkStatus,"vermerkTyp":vermerkTyp,"vermerkText":vermerkText,"faelligkeitDatum":faelligkeitDatum,"untergruppenID":untergruppenID},
                type: "GET",	        
                success: function(data){
                    alert(data);
                    $.ajax({
                            url : "getVermerkeToUntergruppe.php",
                            data:{"vermerkUntergruppenID":vermerkUntergruppenID,"vermerkGruppenID":vermerkGruppenID},
                            type: "GET",
                            success: function(data){
                                $("#vermerke").html(data);
                                // Neu laden der PDF-Vorschau
                                document.getElementById('pdfPreview').src += '';
                            } 
                        });
                }
            });	            
        }
        else{
                alert("Bitte alle Felder ausfüllen!");
        }          
    });
    
    
    // Vermerk lösdeleteVermerkchen -> Modal öffnen
    $("#deleteVermerk").click(function(){   
        stopRecording();
        $('#deleteVermerkModal').modal('show');
    });
    
    // Vermerk löschen
    $("#deleteVermerkExecute").click(function(){       
        stopRecording();
        var vermerkUntergruppenID = <?php echo filter_input(INPUT_GET, 'vermerkUntergruppenID') ?>;
        
        $.ajax({
            url : "deleteVermerk.php",
            data:{"vermerkID":vermerkID},
            type: "GET",	        
            success: function(data){
                alert(data);
                // Neu Laden der Vermerkliste
                $.ajax({
                        url : "getVermerkeToUntergruppe.php",
                        data:{"vermerkUntergruppenID":vermerkUntergruppenID,"vermerkGruppenID":vermerkGruppenID},
                        type: "GET",
                        success: function(data){
                            $("#vermerke").html(data); 
                            // Neu laden der PDF-Vorschau
                            document.getElementById('pdfPreview').src += '';
                        } 
                    });  
            }
        });	   
    });
    
    $("button[value='showVermerkZustaendigkeit']").click(function(){
        var id = this.id;       
        $.ajax({
            url : "getVermerkZustaendigkeiten.php",
            type: "GET",
            data:{"vermerkID":id},
            success: function(data){
                $("#vermerkZustaendigkeit").html(data);                
                $.ajax({
                    url : "getPossibleVermerkZustaendigkeiten.php",
                    type: "GET",
                    data:{"vermerkID":id},
                    success: function(data){
                        $("#possibleVermerkZustaendigkeit").html(data);
                        $('#showVermerkZustaendigkeitModal').modal('show'); 
                    } 
                }); 
                
            } 
        });
        	     
    });
    
    $("#addImage").click(function(){                                            
        $('#uploadImageModal').modal('show'); 
    });
    
    $("#uploadImageButton").click(function(){
        // get selected Image
        //var input = document.getElementById("imageUpload").files;
        var file = document.querySelector('#imageUpload').files[0]; 
        if (!file) {
            alert("Bitte Datei auswählen");
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
                    formData.append("vermerkID",vermerkID);

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
        



</script> 

</body>
</html>