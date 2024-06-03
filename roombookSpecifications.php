<?php
session_start();
include '_utils.php';
init_page_serversides();
?> 


<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>RB-Bauangaben</title>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="style.css" type="text/css" media="screen" />
<link rel="icon" href="iphone_favicon.png">
 
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css" integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>
  
 
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/b-1.5.2/b-html5-1.5.2/sl-1.2.6/datatables.min.css"/>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/b-1.5.2/b-html5-1.5.2/sl-1.2.6/datatables.min.js"></script>

<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/datatables.mark.js/2.0.0/datatables.mark.min.css"/>
<script type="text/javascript" src="https://cdn.datatables.net/plug-ins/1.10.13/features/mark.js/datatables.mark.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/mark.js/8.6.0/jquery.mark.min.js"></script>

  
<!--- https://cdn.datatables.net/2.0.3/css/dataTables.dataTables.css --->
  
 <style>

.btn-xs {
  height: 22px;
  padding: 2px 5px;
  font-size: 12px;
  line-height: 1.5; /* If Placeholder of the input is moved up, rem/modify this. */
  border-radius: 3px;
}

</style>
 
</head>
  
<body style="height:100%">
 
<div class="container-fluid" >

    
    <div id="limet-navbar"></div> <!-- Container für Navbar -->		

    <div class="mt-4 card">
                <div class="card-header">Räume im Projekt</div>
                <div class="card-body">
                    <?php
                        echo "<button type='button' id='addRoomButton' class='btn btn-success btn-sm mb-2' value='addRoom' data-toggle='modal' data-target='#changeRoomModal'>Raum hinzufügen <i class='far fa-plus-square'></i></button>";
                            
                            $mysqli = utils_connect_sql();
                            $sql = "SELECT tabelle_räume.idTABELLE_Räume, tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung, tabelle_räume.`Raumbereich Nutzer`, tabelle_räume.Nutzfläche, tabelle_räume.Raumhoehe, tabelle_räume.Geschoss, tabelle_räume.Bauetappe, 
                                    tabelle_räume.Bauabschnitt, tabelle_räume.Abdunkelbarkeit, tabelle_räume.Strahlenanwendung, tabelle_räume.Laseranwendung, tabelle_räume.H6020, tabelle_räume.GMP, tabelle_räume.ISO, 
                                    tabelle_räume.`1 Kreis O2`, tabelle_räume.`2 Kreis O2`, tabelle_räume.`1 Kreis Va`, tabelle_räume.`2 Kreis Va`, tabelle_räume.`1 Kreis DL-5`, tabelle_räume.`2 Kreis DL-5`, 
                                    tabelle_räume.`DL-10`, tabelle_räume.`DL-tech`, tabelle_räume.CO2, tabelle_räume.NGA, tabelle_räume.N2O, tabelle_räume.AV, tabelle_räume.SV, tabelle_räume.ZSV, tabelle_räume.USV, tabelle_räume.`IT Anbindung`, tabelle_räume.`Fussboden OENORM B5220`,
                                    tabelle_räume.Anwendungsgruppe, tabelle_räume.`Allgemeine Hygieneklasse`, tabelle_räume.`MT-relevant`, tabelle_räume.`TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen`, tabelle_räume.RaumNr_Bestand, tabelle_räume.Gebaeude_Bestand, tabelle_räume.AR_Schwingungsklasse,
                                    tabelle_räume.HT_Notdusche, tabelle_räume.`ET_EMV_ja-nein`
                                    FROM tabelle_räume
                                    WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"]."))
                                    ORDER BY tabelle_räume.Raumnr;";

                            $result = $mysqli->query($sql);

                            echo "<table class='table table-striped table-bordered table-sm' id='tableRooms'  cellspacing='0' width='100%'>
                            <thead><tr>
                            <th>ID</th>
                            <th></th>
                            <th>Raumnr</th>
                            <th>Name</th>
                            <th>NF-Ist</th>
                            <th>RH</th>
                            <th>Raumbereich</th>
                            <th>Geschoss</th>
                            <th>BE</th>
                            <th>BT</th>                                                
                            <th>AWG</th>
                            <th>AV</th>
                            <th>SV</th>
                            <th>ZSV</th>
                            <th>USV</th>
                            <th>IT</th>
                            <th>FB ÖNORM B5220</th>
                            <th>Einteilung nach</th>
                            <th>H6020</th>
                            <th>ISO</th>
                            <th>GMP</th>
                            <th>Strahlen</th>
                            <th>Abd</th>
                            <th>Laser</th>
                            <th>VC</th>
                            <th>Nr-Best</th>
                            <th>Geb-Best</th>                            
                            <th>1.O2</th>
                            <th>2.O2</th>
                            <th>1.VA</th>
                            <th>2.VA</th>
                            <th>1.DL</th>
                            <th>2.DL</th>
                            <th>DL10</th>
                            <th>DLt</th>
                            <th>CO2</th>
                            <th>NGA</th>
                            <th>N2O</th>
                            <th>Notdusche</th>
                            <th>MT-rel</th>
                            <th>FunktionsteilstellenID</th>
                            <th>EMV</th>
                            </tr>
                            <tr>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>                                                
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>                            
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th><select id='filter_MTrelevant'>
                                    <option value='2'></option>
                                    <option value='1'>Ja</option>
                                    <option value='0'>Nein</option>
                                </select></th>
                            <th></th>
                            <th></th>
                            </tr>
                            </thead>
                            <tbody>";
                            
                            
                            
                            while($row = $result->fetch_assoc()) {
                                echo "<tr>";
                               // echo "<td class='col-md-1'><input type='button' id='".$row["idTABELLE_Räume"]."' class='btn btn-success btn-xs' value='Raum auswählen'></td>";
                                echo "<td>".$row["idTABELLE_Räume"]."</td>";                                                       
                                echo "<td><button type='button' id='".$row["idTABELLE_Räume"]."' class='btn btn-outline-dark btn-xs' value='changeRoom' data-toggle='modal' data-target='#changeRoomModal'><i class='fa fa-edit'></i></button></td>";
                                echo "<td>".$row["Raumnr"]."</td>";
                                echo "<td>".$row["Raumbezeichnung"]."</td>";
                                echo "<td>".$row["Nutzfläche"]."</td>";
                                echo "<td>".$row["Raumhoehe"]."</td>";
                                echo "<td>".$row["Raumbereich Nutzer"]."</td>";
                                echo "<td>".$row["Geschoss"]."</td>";
                                echo "<td>".$row["Bauetappe"]."</td>";
                                echo "<td>".$row["Bauabschnitt"]."</td>";						    
                                echo "<td>".$row["Anwendungsgruppe"]."</td>";
                                echo "<td>".$row["AV"]."</td>";
                                echo "<td>".$row["SV"]."</td>";
                                echo "<td>".$row["ZSV"]."</td>";
                                echo "<td>".$row["USV"]."</td>";
                                echo "<td>".$row["IT Anbindung"]."</td>";
                                echo "<td>".$row["Fussboden OENORM B5220"]."</td>";
                                echo "<td>".$row["Allgemeine Hygieneklasse"]."</td>";
                                echo "<td>".$row["H6020"]."</td>";
                                echo "<td>".$row["ISO"]."</td>";
                                echo "<td>".$row["GMP"]."</td>";
                                echo "<td>".$row["Strahlenanwendung"]."</td>";
                                echo "<td>".$row["Abdunkelbarkeit"]."</td>";
                                echo "<td>".$row["Laseranwendung"]."</td>";
                                echo "<td>".$row["AR_Schwingungsklasse"]."</td>";
                                echo "<td>".$row["RaumNr_Bestand"]."</td>";
                                echo "<td>".$row["Gebaeude_Bestand"]."</td>";
                                echo "<td>".$row["1 Kreis O2"]."</td>";
                                echo "<td>".$row["2 Kreis O2"]."</td>";
                                echo "<td>".$row["1 Kreis Va"]."</td>";
                                echo "<td>".$row["2 Kreis Va"]."</td>";
                                echo "<td>".$row["1 Kreis DL-5"]."</td>";
                                echo "<td>".$row["2 Kreis DL-5"]."</td>";
                                echo "<td>".$row["DL-10"]."</td>";
                                echo "<td>".$row["DL-tech"]."</td>";
                                echo "<td>".$row["CO2"]."</td>";
                                echo "<td>".$row["NGA"]."</td>";
                                echo "<td>".$row["N2O"]."</td>";
                                echo "<td>".$row["HT_Notdusche"]."</td>";
                                echo "<td>".$row["MT-relevant"]."</td>";
                                echo "<td>".$row["TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen"]."</td>";
                                echo "<td>".$row["ET_EMV_ja-nein"]."</td>";
                                echo "</tr>";

                            }
                            echo "</tbody></table>";                
                            
                            // Abfragen der Funktionsteilstellen
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

                    ?>	
            </div>
    </div>
    <div class='d-flex bd-highlight'>
        <div class='mt-4 mr-2 card flex-grow-1'>
                <div class="card-header"><b>Bauangaben</b></div>
                <div class="card-body" id="bauangaben"></div>
        </div>
        <div class="mt-4 card">
            <div class="card">
                <div class="card-header"><button type="button" class="btn btn-outline-dark btn-xs" id="showRoomElements"><i class="fas fa-caret-left"></i></button></div>
                <div class="card-body" id ="additionalInfo">
                    <p id="roomElements">
                    <p id="elementParameters"></div>
                </div> 
            </div>
            
        </div>
    </div>
            
</div>
    
    <!-- Modal zum Ändern des Raumes -->
    <div class='modal fade' id='changeRoomModal' role='dialog'>
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
                    <div class="form-group">
                      <label for="flaeche">Fläche:</label>
                      <input type="text"  class="form-control form-control-sm" id="flaeche"/>
                    </div>
                    <div class="form-group">
                      <label for="raumbereich">Raumbereich-Nutzer:</label>
                      <input type="text"  class="form-control form-control-sm" id="raumbereich"/>
                    </div>
                    <div class="form-group">
                      <label for="geschoss">Geschoss:</label>
                      <input type="text"  class="form-control form-control-sm" id="geschoss"/>
                    </div>
                    <div class="form-group">
                      <label for="bauetappe">Bauetappe:</label>
                      <input type="text"  class="form-control form-control-sm" id="bauetappe"/>
                    </div>
                    <div class="form-group">
                      <label for="bauteil">Bauteil:</label>
                      <input type="text"  class="form-control form-control-sm" id="bauteil"/>
                    </div>
                    <div class='form-group'>
                        <label for='funktionsstelle'>Funktionsstelle wählen:</label>
                            <select class='form-control form-control-sm' id='funktionsstelle'>
                                <option value=0 selected>Funktionsstelle wählen</option>
                                   <?php                                              
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
                  <input type='button' id='addRoom' class='btn btn-success btn-sm' value='Hinzufügen'></input>
                  <input type='button' id='saveRoom' class='btn btn-warning btn-sm' value='Speichern'></input>
                  <button type='button' class='btn btn-default btn-sm' data-dismiss='modal'>Abbrechen</button>
            </div>
        </div>
      </div>
    </div>

<script>	
    var raumID;
    var table;
    
    $.fn.dataTable.ext.search.push(
            function( settings, data, dataIndex ) {
                if ( settings.nTable.id !== 'tableRooms' ) {
                    return true;
                }                    
                               
                
                if($("#filter_MTrelevant").val()==='1'){
                    if (data [39] === "1")
                    {
                        return true;
                    }
                    else{
                        return false;
                    }
                }
                else{
                    if($("#filter_MTrelevant").val()==='0'){
                        if (data [39] === "0")
                        {
                            return true;
                        }
                        else{
                            return false;
                        }
                    }
                    else{
                        return true;
                    }
                }
            }
    );        

    $('#filter_MTrelevant').change( function() {
        table.draw();
    } );
    
	// Tabellen formatieren	
	$(document).ready(function(){		
		$('#tableRooms').DataTable( {
			"paging": true,
			"order": [[ 2, "asc" ]],
                        //"pagingType": "simple_numbers",
                        //"lengthMenu": [[5,10, 25, 50, -1], [5,10, 25, 50, "All"]],
                        "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"},
                        "columnDefs": [
                            { 
                                "targets": [ 0,40 ], 
                                "visible": false, 
                                "searchable": false  
                            },
                            { 
                                "targets": [ 1 ], 
                                "visible": true, 
                                "searchable": false,
                                "sortable": false
                            },
                            { 
                                "targets": [ 3,6 ], 
                                "width": "20%"                                
                            }
                        ],
                        "orderCellsTop": true,
                        "select": true,
                        "pagingType": "simple",
                        "lengthChange": false,
                        "pageLength": 10,
                        "info": true,
                        "dom": 'Bfrtip',
                        "buttons": [
                            'excel', 'copy', 'csv'
                        ],
                        "mark":true
                        
	    } );
	    
	    table = $('#tableRooms').DataTable();
 
	    $('#tableRooms tbody').on( 'click', 'tr', function () {
			
	        if ( $(this).hasClass('info') ) {
	            //$(this).removeClass('info');
	        }
	        else {
	            table.$('tr.info').removeClass('info');
	            $(this).addClass('info');
	            raumID = table.row( $(this) ).data()[0];
                    console.log("RaumID", raumID);
                    
                    document.getElementById("nummer").value = table.row( $(this) ).data()[2];
                    document.getElementById("name").value = table.row( $(this) ).data()[3];
                    document.getElementById("flaeche").value = table.row( $(this) ).data()[4];
                    document.getElementById("raumbereich").value = table.row( $(this) ).data()[6];
                    document.getElementById("geschoss").value = table.row( $(this) ).data()[7];
                    document.getElementById("bauetappe").value = table.row( $(this) ).data()[8];
                    document.getElementById("bauteil").value = table.row( $(this) ).data()[9];
                    document.getElementById("funktionsstelle").value = table.row( $(this) ).data()[40];
                    
                    if(table.row( $(this) ).data()[39] === '1'){
                        document.getElementById("mt-relevant").value = 1;
                    }
                    else{
                        document.getElementById("mt-relevant").value = 0;
                    }
                    
	            $.ajax({
			        url : "setSessionVariables.php",
			        data:{"roomID":raumID},
			        type: "GET",
			        success: function(data){
			            $("#RoomID").text(raumID);
			            //$.ajax({
					        //url : "getNoticeDataField.php",
					        //type: "GET",
					        //success: function(data){
					            //$("#addNotice1").html(data);
                                                        $.ajax({
                                                                url : "getRoomSpecifications1.php",
                                                                type: "GET",
                                                                success: function(data){
                                                                    $("#bauangaben").html(data);
                                                                   // $.ajax({
                                                                                //url : "getRoomNotices.php",
                                                                               // type: "GET",
                                                                                //success: function(data){
                                                                                  //  $("#roomNotices").html(data);
                                                                                    $.ajax({
                                                                                                url : "getRoomElementsDetailed.php",
                                                                                                type: "GET",
                                                                                                success: function(data){
                                                                                                    $("#roomElements").html(data);


                                                                                                } 
                                                                                        });

                                                                                //} 
                                                                    //    });

                                                               } 
                                                        });							   
					        //} 
				        //});
		
			        } 
		        });
				
	        }
	    } );
	    	    
	});
	
	
//	//Bauangaben einblenden/ausblenden
//	$("#showBauangaben").click(function() {
//	  if($("#bauangaben").is(':hidden')){
//	    $(this).html("<span class='glyphicon glyphicon-menu-down'></span>");
//	    $("#bauangaben").show();
//	  }
//	  else {
//	  	$(this).html("<span class='glyphicon glyphicon-menu-right'></span>");
//	    $("#bauangaben").hide();
//	  }
//	});
//	
//	// Notizen einblenden/ausblenden
//	$("#showNotices").click(function() {
//	  if($("#notices").is(':hidden')){
//	    $(this).html("<span class='glyphicon glyphicon-menu-down'></span>");
//	    $("#notices").show();
//	  }
//	  else {
//	  	$(this).html("<span class='glyphicon glyphicon-menu-right'></span>");
//	    $("#notices").hide();
//	  }
//	});
        
        //Raum speichern
	$("#saveRoom").click(function(){
		var nummer = $("#nummer").val();
		var name = $("#name").val();
		var flaeche  = $("#flaeche").val();
                var raumbereich = $("#raumbereich").val();
		var geschoss = $("#geschoss").val();
		var bauetappe  = $("#bauetappe").val();
                var bauteil  = $("#bauteil").val();        
                var funktionsteilstelle  = $("#funktionsstelle").val(); 
                var MTrelevant  = $("#mt-relevant").val(); 
		
		if(nummer !== "" && name !== "" && flaeche  !== "" && raumbereich !== "" && geschoss !== "" && bauetappe  !== "" && bauteil  !== "" && funktionsteilstelle !== 0 && MTrelevant  !== ""){
                    
		    $.ajax({
		        url : "saveRoomData.php",
                        data:{"ID":raumID,"raumnummer":nummer,"raumbezeichnung":name,"geschoss":geschoss,"nutzflaeche":flaeche,"bauteil":bauteil,"bauetappe":bauetappe,"raumbereich":raumbereich,"funktionsteilstelle":funktionsteilstelle,"MTrelevant":MTrelevant},
		        type: "GET",	        
		        success: function(data){
                            $('#changeRoomModal').modal('hide');
                            alert(data);
                            window.location.replace("roombookSpecifications.php");
		        }
		    });			    
		}
		else{
			alert("Bitte alle Felder ausfüllen!");
		}    
        });
        
        //Raum hinzufügen
	$("#addRoom").click(function(){
            console.log("Add btn click");
		var nummer = $("#nummer").val();
		var name = $("#name").val();
		var flaeche  = $("#flaeche").val();
                var raumbereich = $("#raumbereich").val();
		var geschoss = $("#geschoss").val();
		var bauetappe  = $("#bauetappe").val();
                var bauteil  = $("#bauteil").val();        
                var funktionsteilstelle  = $("#funktionsstelle").val(); 
                var MTrelevant  = $("#mt-relevant").val(); 
		
		if(nummer !== "" && name !== "" && flaeche  !== "" && raumbereich !== "" && geschoss !== "" && bauetappe  !== "" && bauteil  !== "" && funktionsteilstelle !== 0 && MTrelevant  !== ""){
                    
		    $.ajax({
		        url : "addRoom.php",
                        data:{"ID":raumID,"raumnummer":nummer,"raumbezeichnung":name,"geschoss":geschoss,"nutzflaeche":flaeche,"bauteil":bauteil,"bauetappe":bauetappe,"raumbereich":raumbereich,"funktionsteilstelle":funktionsteilstelle,"MTrelevant":MTrelevant},
		        type: "GET",	        
		        success: function(data){
                            $('#changeRoomModal').modal('hide');
                            alert(data);
                            window.location.replace("roombookSpecifications.php");
		        }
		    });			    
		}
		else{
			alert("Bitte alle Felder ausfüllen!");
		}    
        });
        
        $("#addRoomButton").click(function(){	    
            document.getElementById("nummer").value = "";
            document.getElementById("name").value = "";
            document.getElementById("flaeche").value = "";
            document.getElementById("raumbereich").value = "";
            document.getElementById("geschoss").value = ""; 
            document.getElementById("bauetappe").value = "";
            document.getElementById("bauteil").value = "";
            document.getElementById("funktionsstelle").value = "0";
            document.getElementById("mt-relevant").value = "1";
 
            // Buttons ein/ausblenden!
            document.getElementById("saveRoom").style.display = "none";
            document.getElementById("addRoom").style.display = "inline";
        });
        
        $("button[value='changeRoom']").click(function(){           
            // Buttons ein/ausblenden!               
            console.log("button[value=changeRoom] clicked ");
            document.getElementById("addRoom").style.display = "none";
            document.getElementById("saveRoom").style.display = "inline";                
            $('#changeRoomModal').modal('show');            
        });
        
        $("#showRoomElements").click(function() {
            if($("#roomElements").is(':hidden')){
                $(this).html("<i class='fas fa-caret-left'></i>");
                $("#additionalInfo").show();
            }
            else {
                $(this).html("<i class='fas fa-caret-right'></i>");
                $("#additionalInfo").hide();
            }
	});
	
</script>

</body>

</html>
