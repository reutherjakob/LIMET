<?php
session_start();
?>

<!DOCTYPE html >
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" /></head>
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

	//$sql = "SELECT tabelle_ansprechpersonen.idTABELLE_Ansprechpersonen, tabelle_ansprechpersonen.Name, tabelle_ansprechpersonen.Vorname
         //       FROM tabelle_ansprechpersonen INNER JOIN tabelle_Vermerkgruppe_has_tabelle_ansprechpersonen ON tabelle_ansprechpersonen.idTABELLE_Ansprechpersonen = tabelle_Vermerkgruppe_has_tabelle_ansprechpersonen.tabelle_ansprechpersonen_idTABELLE_Ansprechpersonen
          //      WHERE (((tabelle_Vermerkgruppe_has_tabelle_ansprechpersonen.tabelle_Vermerkgruppe_idtabelle_Vermerkgruppe)=".filter_input(INPUT_GET, 'gruppenID')."));";
        
        $sql = "SELECT tabelle_ansprechpersonen.idTABELLE_Ansprechpersonen, tabelle_ansprechpersonen.Name, tabelle_ansprechpersonen.Vorname, tabelle_Vermerkgruppe_has_tabelle_ansprechpersonen.Anwesenheit, tabelle_Vermerkgruppe_has_tabelle_ansprechpersonen.Verteiler
                FROM tabelle_ansprechpersonen INNER JOIN tabelle_Vermerkgruppe_has_tabelle_ansprechpersonen ON tabelle_ansprechpersonen.idTABELLE_Ansprechpersonen = tabelle_Vermerkgruppe_has_tabelle_ansprechpersonen.tabelle_ansprechpersonen_idTABELLE_Ansprechpersonen
                WHERE (((tabelle_Vermerkgruppe_has_tabelle_ansprechpersonen.tabelle_Vermerkgruppe_idtabelle_Vermerkgruppe)=".filter_input(INPUT_GET, 'gruppenID')."));";
        
	$result = $mysqli->query($sql);
	
	echo "<table class='table table-striped table-bordered table-sm' id='tableVermerkGroupMembers' cellspacing='0' width='100%'>
        <thead><tr>
        <th></th>
        <th>Name</th>
        <th>Vorname</th>
        <th>Anwesenheit</th>
        <th>Verteiler</th>
        </tr></thead>
        <tbody>";
        
        
        while($row = $result->fetch_assoc()) {
            echo "<tr>";						 
            echo "<td><button type='button' id='".$row["idTABELLE_Ansprechpersonen"]."' class='btn btn-outline-danger btn-sm' value='deleteVermerkGroupMember'><i class='fas fa-minus'></i></button></td>";
            echo "<td>".$row["Name"]."</td>";
            echo "<td>".$row["Vorname"]."</td>";
            echo "<td><div class='form-check'>";
                if($row["Anwesenheit"]=="0"){
                        echo "<input type='checkbox' class='form-check-input' id='".$row["idTABELLE_Ansprechpersonen"]."' value='anwesenheitCheck'>";
                   }
                   else{
                       echo "<input type='checkbox' class='form-check-input' id='".$row["idTABELLE_Ansprechpersonen"]."' value='anwesenheitCheck' checked='true'>";
                   }
            echo "</div></td>";
            echo "<td><div class='form-check'>";
                if($row["Verteiler"]=="0"){
                        echo "<input type='checkbox' class='form-check-input' id='".$row["idTABELLE_Ansprechpersonen"]."' value='verteilerCheck'>";
                   }
                   else{
                       echo "<input type='checkbox' class='form-check-input' id='".$row["idTABELLE_Ansprechpersonen"]."' value='verteilerCheck' checked='true'>";
                   }
            echo "</div></td>";
            echo "</tr>";

        }
        echo "</tbody></table>";

        $mysqli ->close();
?>
	
<script>
    
    
    $('#tableVermerkGroupMembers').DataTable( {
            "paging": false,
            "searching": true,
            "info": false,
            "order": [[ 1, "asc" ]],
            "columnDefs": [
            {
                "targets": [ 0 ],
                "visible": true,
                "searchable": false,
                "sortable": false
            }
        ],
        "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"},
        "scrollY": '20vh',
        "scrollCollapse": true   	 
     } );  
     
    //Parameter von Gerät entfernen
    $("button[value='deleteVermerkGroupMember']").click(function(){
        var id = this.id;
        var groupID = "<?php echo filter_input(INPUT_GET, 'gruppenID') ?>";
        
        if(id !== "" && groupID !== ""){
            $.ajax({
                url : "deletePersonFromVermerkGroup.php",
                data:{"ansprechpersonenID":id,"groupID":groupID},
                type: "GET",
                success: function(data){
                    alert(data);
                    // Neu laden der PDF-Vorschau
                    document.getElementById('pdfPreview').src += '';
                    
                    $.ajax({
                        url : "getVermerkgruppenMembers.php",
                        type: "GET",
                        data:{"gruppenID":groupID},
                        success: function(data){
                            $("#vermerkGroupMembers").html(data);
                            $.ajax({
                                url : "getPossibleVermerkGruppenMembers.php",
                                type: "GET",
                                data:{"gruppenID":groupID},
                                success: function(data){
                                    $("#possibleVermerkGroupMembers").html(data);
                                } 
                            }); 
                            
                        } 
                    }); 
                } 
            });            
        }	
        
    }); 
    
    
    $("input[value='anwesenheitCheck']").change(function(){                 
        
        if($(this).prop('checked')===true){
            var anwesenheit = 1;
        }
        else{
            var anwesenheit = 0;
        }
        var ansprechpersonenID  = this.id;
        var groupID = "<?php echo filter_input(INPUT_GET, 'gruppenID') ?>";
        
        if(anwesenheit !== "" && ansprechpersonenID !== "" && groupID !== ""){                    
            $.ajax({
                url : "saveVermerkgruppenPersonenanwesenheit.php",
                data:{"ansprechpersonenID":ansprechpersonenID,"anwesenheit":anwesenheit,"groupID":groupID},
                type: "GET",	        
                success: function(data){
                    alert(data);
                    $.ajax({
                        url : "getVermerkgruppenMembers.php",
                        type: "GET",
                        success: function(data){
                            $("#vermerkGroupMembers").html(data);                                        
                        }
                    });
                }
            });	            
        }
        else{
                alert("Anwesenheit nicht lesbar!");
        } 

    });
    
    $("input[value='verteilerCheck']").change(function(){                 
        
        if($(this).prop('checked')===true){
            var verteiler = 1;
        }
        else{
            var verteiler = 0;
        }
        var ansprechpersonenID  = this.id;
        var groupID = "<?php echo filter_input(INPUT_GET, 'gruppenID') ?>";
        
        if(verteiler !== "" && ansprechpersonenID !== "" && groupID !== ""){                    
            $.ajax({
                url : "saveVermerkgruppenPersonenverteiler.php",
                data:{"ansprechpersonenID":ansprechpersonenID,"verteiler":verteiler,"groupID":groupID},
                type: "GET",	        
                success: function(data){
                    alert(data);
                    $.ajax({
                        url : "getVermerkgruppenMembers.php",
                        type: "GET",
                        success: function(data){
                            $("#vermerkGroupMembers").html(data);                                        
                        }
                    });
                }
            });	            
        }
        else{
                alert("Vertiler nicht lesbar!");
        } 

    });
    

</script>

</body>
</html>