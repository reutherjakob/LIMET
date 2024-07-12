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
	
	
	/* change character set to utf8 */
	if (!$mysqli->set_charset("utf8")) {
	    printf("Error loading character set utf8: %s\n", $mysqli->error);
	    exit();
	} 
	
        
        if(filter_input(INPUT_GET, 'filterValue') === '1'){
            $sql = "SELECT tabelle_Vermerkgruppe.Gruppenname, tabelle_Vermerkgruppe.Gruppenart, tabelle_Vermerkgruppe.Ort, tabelle_Vermerkgruppe.Datum, tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung, tabelle_lose_extern.LosNr_Extern, tabelle_lose_extern.LosBezeichnung_Extern, tabelle_ansprechpersonen.Name, tabelle_ansprechpersonen.Vorname, tabelle_Vermerke.Faelligkeit, tabelle_Vermerke.Vermerkart, tabelle_Vermerke.Bearbeitungsstatus, tabelle_Vermerke.Vermerktext, tabelle_Vermerke.Erstellungszeit, tabelle_Vermerke.idtabelle_Vermerke
                    FROM (((tabelle_Vermerke LEFT JOIN (tabelle_ansprechpersonen RIGHT JOIN tabelle_Vermerke_has_tabelle_ansprechpersonen ON tabelle_ansprechpersonen.idTABELLE_Ansprechpersonen = tabelle_Vermerke_has_tabelle_ansprechpersonen.tabelle_ansprechpersonen_idTABELLE_Ansprechpersonen) ON tabelle_Vermerke.idtabelle_Vermerke = tabelle_Vermerke_has_tabelle_ansprechpersonen.tabelle_Vermerke_idtabelle_Vermerke) INNER JOIN (tabelle_Vermerkgruppe INNER JOIN tabelle_Vermerkuntergruppe ON tabelle_Vermerkgruppe.idtabelle_Vermerkgruppe = tabelle_Vermerkuntergruppe.tabelle_Vermerkgruppe_idtabelle_Vermerkgruppe) ON tabelle_Vermerke.tabelle_Vermerkuntergruppe_idtabelle_Vermerkuntergruppe = tabelle_Vermerkuntergruppe.idtabelle_Vermerkuntergruppe) LEFT JOIN tabelle_räume ON tabelle_Vermerke.tabelle_räume_idTABELLE_Räume = tabelle_räume.idTABELLE_Räume) LEFT JOIN tabelle_lose_extern ON tabelle_Vermerke.tabelle_lose_extern_idtabelle_Lose_Extern = tabelle_lose_extern.idtabelle_Lose_Extern
                    WHERE (((tabelle_Vermerkgruppe.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"].") AND tabelle_Vermerke.Vermerkart='Bearbeitung' AND tabelle_Vermerke.Bearbeitungsstatus='0')
                    ORDER BY tabelle_Vermerkgruppe.Datum DESC , tabelle_Vermerke.Erstellungszeit DESC;";
        }
        else{
            $sql = "SELECT tabelle_Vermerkgruppe.Gruppenname, tabelle_Vermerkgruppe.Gruppenart, tabelle_Vermerkgruppe.Ort, tabelle_Vermerkgruppe.Datum, tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung, tabelle_lose_extern.LosNr_Extern, tabelle_lose_extern.LosBezeichnung_Extern, tabelle_ansprechpersonen.Name, tabelle_ansprechpersonen.Vorname, tabelle_Vermerke.Faelligkeit, tabelle_Vermerke.Vermerkart, tabelle_Vermerke.Bearbeitungsstatus, tabelle_Vermerke.Vermerktext, tabelle_Vermerke.Erstellungszeit, tabelle_Vermerke.idtabelle_Vermerke
                FROM (((tabelle_Vermerke LEFT JOIN (tabelle_ansprechpersonen RIGHT JOIN tabelle_Vermerke_has_tabelle_ansprechpersonen ON tabelle_ansprechpersonen.idTABELLE_Ansprechpersonen = tabelle_Vermerke_has_tabelle_ansprechpersonen.tabelle_ansprechpersonen_idTABELLE_Ansprechpersonen) ON tabelle_Vermerke.idtabelle_Vermerke = tabelle_Vermerke_has_tabelle_ansprechpersonen.tabelle_Vermerke_idtabelle_Vermerke) INNER JOIN (tabelle_Vermerkgruppe INNER JOIN tabelle_Vermerkuntergruppe ON tabelle_Vermerkgruppe.idtabelle_Vermerkgruppe = tabelle_Vermerkuntergruppe.tabelle_Vermerkgruppe_idtabelle_Vermerkgruppe) ON tabelle_Vermerke.tabelle_Vermerkuntergruppe_idtabelle_Vermerkuntergruppe = tabelle_Vermerkuntergruppe.idtabelle_Vermerkuntergruppe) LEFT JOIN tabelle_räume ON tabelle_Vermerke.tabelle_räume_idTABELLE_Räume = tabelle_räume.idTABELLE_Räume) LEFT JOIN tabelle_lose_extern ON tabelle_Vermerke.tabelle_lose_extern_idtabelle_Lose_Extern = tabelle_lose_extern.idtabelle_Lose_Extern
                WHERE (((tabelle_Vermerkgruppe.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"]."))
                ORDER BY tabelle_Vermerkgruppe.Datum DESC , tabelle_Vermerke.Erstellungszeit DESC;";
        }
        
    
	$result = $mysqli->query($sql);
	
	echo "<div class='table-responsive'><table class='table table-striped table-bordered table-sm' id='tableProjectVermerke' cellspacing='0' width='100%'> 
	<thead><tr>
	<th>ID</th> 
        <th>Art</th>
        <th>Name</th>
        <th>Status</th>
	<th>Datum</th>
	<th>Typ</th>
	<th>Zuständig</th>
	<th>Fälligkeit</th>
        <th>Vermerk</th>
	<th>Raum</th>
	<th>Los</th>	        
        <th>Status</th>
	</tr></thead><tbody>";
	

	while($row = $result->fetch_assoc()) {
	    echo "<tr>";
	    echo "<td>".$row["idtabelle_Vermerke"]."</td>";
	    echo "<td>".$row["Gruppenart"]."</td>";
            echo "<td>".$row["Gruppenname"]."</td>";
            echo "<td>";
                if($row["Vermerkart"]!="Info"){                                   
                    if($row["Bearbeitungsstatus"]=="0"){
                         echo "<div class='form-check form-check-inline'><label class='form-check-label' for='".$row["idtabelle_Vermerke"]."'><input type='checkbox' class='form-check-input' id='".$row["idtabelle_Vermerke"]."' value='statusCheck'></label></div>";
                    }
                    else{
                        echo "<div class='form-check form-check-inline'><label class='form-check-label' for='".$row["idtabelle_Vermerke"]."'><input type='checkbox' class='form-check-input' id='".$row["idtabelle_Vermerke"]."' value='statusCheck' checked='true'></label></div>";
                    }
                }
            echo "</td>";
            echo "<td>".$row["Datum"]."</td>";
            echo "<td>".$row["Vermerkart"]."</td>";
            echo "<td>".$row["Name"]." ".$row["Vorname"]."</td>";
            echo "<td>";
                if($row["Vermerkart"]!="Info"){
                    echo $row["Faelligkeit"];
                }
            echo "</td>"; 
            echo "<td><button type='button' class='btn btn-xs btn-light' data-toggle='popover' title='Vermerk' data-placement='right' data-content='".$row["Vermerktext"]."'><i class='far fa-comment'></i></button></td>";
            echo "<td>".$row["Raumnr"]." ".$row["Raumbezeichnung"]."</td>";
            echo "<td>".$row["LosNr_Extern"]."</td>";                       
            echo "<td>".$row["Bearbeitungsstatus"]."</td>";	 
	    echo "</tr>";
	}
	echo "</tbody></table></div>";
	$mysqli ->close();
?>
<script>
    
    $(document).ready(function(){  
    	$('#tableProjectVermerke').DataTable( {
    		"columnDefs": [
                        {
                            "targets": [ 0, 11 ],
                            "visible": false,
                            "searchable": false
                        }
                ],
                select: true,
                "paging": true,
                "pagingType": "simple",
                "lengthChange": false,
                "pageLength": 10,
                "searching": true,
                "info": true,
                "order": [[ 4, "desc" ]],
	        'language': {'url': '//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json',"search": ""},
	    	"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
                    if ( aData[5] === "Bearbeitung" )
                    {
                        if(aData[11] === "0"){
                            $('td', nRow).css('background-color', '#ff8080');
                        }
                        else{
                            $('td', nRow).css('background-color', '#b8dc6f');
                        }                        
                    }
                    else{
                        $('td', nRow).css('background-color', '#d3edf8');
                    }
                }	          
	    } );            
	
            
            
            // Popover for Vermerk	
            $(function () {
                $('[data-toggle="popover"]').popover();
              });
              
            // Vermerkstatus ändern  
            $("input[value='statusCheck']").change(function(){                 
        
                if($(this).prop('checked')===true){
                    var vermerkStatus = 1;
                }
                else{
                    var vermerkStatus = 0;
                }
                var vermerkID  = this.id;

                if(vermerkStatus !== "" && vermerkID !== ""){                    
                    $.ajax({
                        url : "saveVermerkStatus.php",
                        data:{"vermerkID":vermerkID,"vermerkStatus":vermerkStatus},
                        type: "GET",	        
                        success: function(data){
                            alert(data);
                            $.ajax({
                                url : "getProjectVermerke.php",
                                type: "GET",
                                success: function(data){
                                    $("#projectVermerke").html(data);                                        
                                }
                            });
                        }
                    });	            
                }
                else{
                        alert("Vermerkstatus nicht lesbar!");
                } 
                
            });
            
	 });



</script> 

</body>
</html>