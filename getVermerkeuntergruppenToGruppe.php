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
        				
                                            
        $sql = "SELECT tabelle_Vermerkuntergruppe.idtabelle_Vermerkuntergruppe, tabelle_Vermerkuntergruppe.Untergruppenname, tabelle_Vermerkuntergruppe.Untergruppennummer
                FROM tabelle_Vermerkuntergruppe
                WHERE (((tabelle_Vermerkuntergruppe.tabelle_Vermerkgruppe_idtabelle_Vermerkgruppe)=".filter_input(INPUT_GET, 'vermerkGruppenID')."))
                ORDER BY Untergruppennummer ASC;";

        $result = $mysqli->query($sql);

        echo "<table class='table table-striped table-bordered table-sm' id='tableVermerkUnterGruppe'  cellspacing='0' width='100%'>
                <thead><tr>
                <th>ID</th>
                <th></th>
                <th>Nummer</th>
                <th>Name</th>
                <th>GruppenID</th>
                </tr></thead><tbody>";   

        while ($row = $result->fetch_assoc()) {                                                    
            echo "<tr>";
            echo "<td>".$row['idtabelle_Vermerkuntergruppe']."</td>";
            echo "<td><button type='button' id='".$row['idtabelle_Vermerkuntergruppe']."' class='btn btn-outline-dark btn-xs' value='changeVermerkuntergruppe'><i class='fas fa-pencil-alt'></i></button></td>";
            echo "<td>".$row['Untergruppennummer']."</td>";
            echo "<td>".$row['Untergruppenname']."</td>";    
            echo "<td>".filter_input(INPUT_GET, 'vermerkGruppenID')."</td>";   
            echo "</tr>";
        }
        echo "</tbody></table>";

        //echo "<button type='button' id='".filter_input(INPUT_GET, 'vermerkGruppenID')."' class='btn btn-success btn-sm' value='Neue Vermerkuntergruppe'>Neue Vermerkuntergruppe</button>";
        
	$mysqli ->close();
?>
    
      <!-- Modal zum Hinzufügen/Ändern einer UnterGruppe -->
	  <div class='modal fade' id='changeUnterGroupModal' role='dialog'>
	    <div class='modal-dialog modal-md'>
	    
	      <!-- Modal content-->
	      <div class='modal-content'>
	        <div class='modal-header'>
                    <h4 class='modal-title'>Untergruppendaten</h4>
	          <button type='button' class='close' data-dismiss='modal'>&times;</button>	          
	        </div>
	        <div class='modal-body' id='untergruppenMbody'>
                        <form role="form">    
                            <div class="form-group">
                              <label for="unterGruppenNummer">Nummer:</label>
                              <input type="text" class="form-control form-control-sm" id="unterGruppenNummer"/>
                            </div>
                            <div class="form-group">
                              <label for="unterGruppenName">Name:</label>
                              <input type="text" class="form-control form-control-sm" id="unterGruppenName"/>
                            </div>                            
                      </form>
                </div>
	        <div class='modal-footer'>
                        <input type='button' id='addUnterGroup' class='btn btn-success btn-sm' value='Hinzufügen' data-dismiss='modal'></input>
                        <input type='button' id='saveUnterGroup' class='btn btn-warning btn-sm' value='Speichern' data-dismiss='modal'></input>
	          	<button type='button' class='btn btn-default btn-sm' data-dismiss='modal'>Abbrechen</button>
	        </div>
	      </div>
	      
	    </div>
	  </div>

<script>
    var untergruppenID;    
    $(document).ready(function(){ 
        document.getElementById("buttonNewVermerkuntergruppe").style.visibility = "visible";
        
    	var table1 = $('#tableVermerkUnterGruppe').DataTable( {
    		"columnDefs": [
                        {
                            "targets": [ 0,4 ],
                            "visible": false,
                            "searchable": false
                        }
                ],
                "select": true,
                "paging": true,
                "pagingType": "simple",
                "lengthChange": false,
                "pageLength": 10,
                "searching": true,
                "info": true,
                "order": [[ 1, "asc" ]],
	        'language': {'url': '//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json'}	          
	    } );
            $('#tableVermerkUnterGruppe tbody').on( 'click', 'tr', function () {
            if ( $(this).hasClass('info') ) {

            }
            else {
                untergruppenID = table1.row( $(this) ).data()[0]; 
                document.getElementById("unterGruppenNummer").value = table1.row( $(this) ).data()[2]; 
                document.getElementById("unterGruppenName").value = table1.row( $(this) ).data()[3];
                
                $("#vermerke").show();
                table1.$('tr.info').removeClass('info');
                $(this).addClass('info');
                $.ajax({
                    url : "getVermerkeToUntergruppe.php",
                    data:{"vermerkUntergruppenID":table1.row( $(this) ).data()[0],"vermerkGruppenID":table1.row( $(this) ).data()[4]},
                    type: "GET",
                    success: function(data){
                        $("#vermerke").html(data); 
                    } 
                });
            }
        });
                                    
            
	 });
         
        $("button[value='changeVermerkuntergruppe']").click(function(){        
            // Buttons ein/ausblenden!
            document.getElementById("saveUnterGroup").style.display = "inline";
            document.getElementById("addUnterGroup").style.display = "none";
            $('#changeUnterGroupModal').modal('show');         
        });
            
        //$("button[value='Neue Vermerkuntergruppe']").click(function(){
        $("#buttonNewVermerkuntergruppe").click(function(){
            document.getElementById("saveUnterGroup").style.display = "none";
            document.getElementById("addUnterGroup").style.display = "inline";
            $('#changeUnterGroupModal').modal('show'); 	     
        });
    
         
         $("#addUnterGroup").click(function(){
            var untergruppenName = $("#unterGruppenName").val();
            var untergruppenNummer = $("#unterGruppenNummer").val();
            var id = <?php echo filter_input(INPUT_GET, 'vermerkGruppenID') ?>;
            
            if(untergruppenName !== "" && untergruppenNummer !== ""){

                $.ajax({
                    url : "addVermerkUnterGroup.php",
                    data:{"untergruppenName":untergruppenName,"untergruppenNummer":untergruppenNummer,"gruppenID":id},
                    type: "GET",	        
                    success: function(data){
                        alert(data);                     
                        $.ajax({
                            url : "getVermerkeuntergruppenToGruppe.php",
                            data:{"vermerkGruppenID":id},
                            type: "GET",
                            success: function(data){
                                $("#vermerkUntergruppen").html(data); 
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
        
        $("#saveUnterGroup").click(function(){
            var untergruppenName = $("#unterGruppenName").val();
            var untergruppenNummer = $("#unterGruppenNummer").val();
            var id = <?php echo filter_input(INPUT_GET, 'vermerkGruppenID') ?>;
            
            if(untergruppenName !== "" && untergruppenNummer !== ""){
                $.ajax({
                    url : "saveVermerkUnterGroup.php",
                    data:{"untergruppenName":untergruppenName,"untergruppenNummer":untergruppenNummer,"untergruppenID":untergruppenID},
                    type: "GET",	        
                    success: function(data){
                        alert(data);
                        $.ajax({
                            url : "getVermerkeuntergruppenToGruppe.php",
                            data:{"vermerkGruppenID":id},
                            type: "GET",
                            success: function(data){
                                $("#vermerkUntergruppen").html(data); 
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
        
        



</script> 

</body>
</html>