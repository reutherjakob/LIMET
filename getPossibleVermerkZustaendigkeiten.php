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
	    		
        
	$sql = "SELECT tabelle_ansprechpersonen.idTABELLE_Ansprechpersonen, tabelle_ansprechpersonen.Name, tabelle_ansprechpersonen.Vorname
                FROM tabelle_projekte_has_tabelle_ansprechpersonen INNER JOIN tabelle_ansprechpersonen ON tabelle_projekte_has_tabelle_ansprechpersonen.TABELLE_Ansprechpersonen_idTABELLE_Ansprechpersonen = tabelle_ansprechpersonen.idTABELLE_Ansprechpersonen
                WHERE (((tabelle_projekte_has_tabelle_ansprechpersonen.TABELLE_Projekte_idTABELLE_Projekte)=".$_SESSION["projectID"].")
                AND tabelle_ansprechpersonen.idTABELLE_Ansprechpersonen NOT IN (
                SELECT tabelle_ansprechpersonen.idTABELLE_Ansprechpersonen
						FROM tabelle_Vermerke_has_tabelle_ansprechpersonen INNER JOIN tabelle_ansprechpersonen ON tabelle_Vermerke_has_tabelle_ansprechpersonen.tabelle_ansprechpersonen_idTABELLE_Ansprechpersonen = tabelle_ansprechpersonen.idTABELLE_Ansprechpersonen
						WHERE (((tabelle_Vermerke_has_tabelle_ansprechpersonen.tabelle_Vermerke_idtabelle_Vermerke)=".filter_input(INPUT_GET, 'vermerkID')."))
                ));";					
        $result = $mysqli->query($sql);

        echo "<table class='table table-striped table-sm' id='tablepossibleVermerkZustaendigkeitMembers' cellspacing='0'>
        <thead><tr>
        <th>ID</th>
        <th>Name</th>
        <th>Vorname</th>
        </tr></thead>
        <tbody>";

        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td><button type='button' id='".$row["idTABELLE_Ansprechpersonen"]."' class='btn btn-success btn-sm' value='addVermerkZustaendigkeit'><i class='fas fa-plus-square'></i></button></td>";
            echo "<td>".$row["Name"]."</td>";
            echo "<td>".$row["Vorname"]."</td>";
            echo "</tr>";

        }

        echo "</tbody></table>";

        $mysqli ->close();
?>
	
<script>
    
    
    $('#tablepossibleVermerkZustaendigkeitMembers').DataTable( {
            "paging": false,
            "searching": true,
            "info": false,
            "order": [[ 1, "asc" ]],
            "columnDefs": [
            {
                "targets": [ 0 ],
                "visible": true,
                "searchable": false
            }
        ],
        "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"},
        "scrollY": '20vh',
        "scrollCollapse": true   	 
     } );  


    $("button[value='addVermerkZustaendigkeit']").click(function(){
        var id = this.id;
        var vermerkID = "<?php echo filter_input(INPUT_GET, 'vermerkID') ?>";

        if(id !== ""){
            $.ajax({
                url : "addPersonToVermerkZustaendigkeit.php",
                data:{"ansprechpersonenID":id,"vermerkID":vermerkID},
                type: "GET",
                success: function(data){
                    alert(data);
                    $.ajax({
                        url : "getVermerkZustaendigkeiten.php",
                        type: "GET",
                        data:{"vermerkID":vermerkID},
                        success: function(data){
                            $("#vermerkZustaendigkeit").html(data);
                            $.ajax({
                                url : "getPossibleVermerkZustaendigkeiten.php",
                                type: "GET",
                                data:{"vermerkID":vermerkID},
                                success: function(data){
                                    $("#possibleVermerkZustaendigkeit").html(data); 
                                    // Neu laden der PDF-Vorschau
                                    document.getElementById('pdfPreview').src += '';
                                }                                 
                            }); 
                            
                        } 
                    }); 
                } 
            }); 
        }
        
        
    });   
     

</script>

</body>
</html>