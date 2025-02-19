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
					
        $sql = "SELECT tabelle_Vermerkgruppe.Gruppenname, tabelle_Vermerkgruppe.Gruppenart, tabelle_Vermerkgruppe.Ort, tabelle_Vermerkgruppe.Datum, tabelle_lose_extern.LosNr_Extern, tabelle_lose_extern.LosBezeichnung_Extern, tabelle_ansprechpersonen.Name, tabelle_ansprechpersonen.Vorname, tabelle_Vermerke.Faelligkeit, tabelle_Vermerke.Vermerkart, tabelle_Vermerke.Bearbeitungsstatus, tabelle_Vermerke.Vermerktext, tabelle_Vermerke.Erstellungszeit, tabelle_Vermerke.idtabelle_Vermerke, tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung
                FROM (((tabelle_Vermerke LEFT JOIN (tabelle_ansprechpersonen RIGHT JOIN tabelle_Vermerke_has_tabelle_ansprechpersonen ON tabelle_ansprechpersonen.idTABELLE_Ansprechpersonen = tabelle_Vermerke_has_tabelle_ansprechpersonen.tabelle_ansprechpersonen_idTABELLE_Ansprechpersonen) ON tabelle_Vermerke.idtabelle_Vermerke = tabelle_Vermerke_has_tabelle_ansprechpersonen.tabelle_Vermerke_idtabelle_Vermerke) INNER JOIN (tabelle_Vermerkgruppe INNER JOIN tabelle_Vermerkuntergruppe ON tabelle_Vermerkgruppe.idtabelle_Vermerkgruppe = tabelle_Vermerkuntergruppe.tabelle_Vermerkgruppe_idtabelle_Vermerkgruppe) ON tabelle_Vermerke.tabelle_Vermerkuntergruppe_idtabelle_Vermerkuntergruppe = tabelle_Vermerkuntergruppe.idtabelle_Vermerkuntergruppe) LEFT JOIN tabelle_räume ON tabelle_Vermerke.tabelle_räume_idTABELLE_Räume = tabelle_räume.idTABELLE_Räume) LEFT JOIN tabelle_lose_extern ON tabelle_Vermerke.tabelle_lose_extern_idtabelle_Lose_Extern = tabelle_lose_extern.idtabelle_Lose_Extern
                WHERE (((tabelle_Vermerkgruppe.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"].") AND ((tabelle_Vermerke.tabelle_lose_extern_idtabelle_Lose_Extern)=".filter_input(INPUT_GET, 'lotID')."))
                ORDER BY tabelle_Vermerkgruppe.Datum DESC , tabelle_Vermerke.Erstellungszeit DESC;";
        
	$result = $mysqli->query($sql);
        
        echo "<button type='button' class='btn btn-default btn-sm' value='createLotVermerkePDF' id='".filter_input(INPUT_GET, 'lotID')."'><i class='far fa-file-pdf'></i> Losvermerke - PDF</button>";
                
	echo "<table class='table table-striped table-bordered table-sm' id='tableLotVermerke' cellspacing='0' width='100%'>
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
        <th>Status</th>
	</tr></thead><tbody>";
	

        
	while($row = $result->fetch_assoc()) {
	    echo "<tr>";
	    echo "<td>".$row["idtabelle_Vermerke"]."</td>";
	    echo "<td>".$row["Gruppenart"]."</td>";
            echo "<td>".$row["Gruppenname"]."</td>";
            echo "<td><div class='form-check'>";
                if($row["Vermerkart"]!="Info"){                                   
                    if($row["Bearbeitungsstatus"]=="0"){
                         echo "<input type='checkbox' class='form-check-input' id='".$row["idtabelle_Vermerke"]."' value='statusCheck'>";
                    }
                    else{
                        echo "<input type='checkbox' class='form-check-input' id='".$row["idtabelle_Vermerke"]."' value='statusCheck' checked='true'>";
                    }
                }
            echo "</div></td>";
            echo "<td>".$row["Datum"]."</td>";
            echo "<td>".$row["Vermerkart"]."</td>";
            echo "<td>".$row["Name"]."</td>";
            echo "<td>";
                if($row["Vermerkart"]!="Info"){
                    echo $row["Faelligkeit"];
                }
            echo "</td>";
            echo "<td><button type='button' class='btn btn-default btn-sm' data-toggle='popover' title='Vermerk' data-placement='left' data-content='".$row["Vermerktext"]."'><i class='far fa-comment'></i></button></td>";
                                  
            echo "<td>".$row["Bearbeitungsstatus"]."</td>";	 
	    echo "</tr>";
	}
	echo "</tbody></table>";
	$mysqli ->close();
?>

<script>
    
    $(document).ready(function(){  
    	$('#tableLotVermerke').DataTable( {
    		"columnDefs": [
                        {
                            "targets": [ 0, 5, 9 ],
                            "visible": false,
                            "searchable": false
                        }
                ],
                "paging": true,
                "pagingType": "simple",
                "lengthChange": false,
                "pageLength": 10,
                "searching": true,
                "info": true,
                "order": [[ 4, "desc" ]],
	        'language': {'url': '//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json'},
	    	"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
                    if ( aData[5] === "Bearbeitung" )
                    {
                        if(aData[9] === "0"){
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
                                url : "getLotVermerke.php",
                                data:{"lotID":lotID},
                                type: "GET",
                                success: function(data){
                                    $("#lotVermerke").html(data);                                        
                                }
                            });
                        }
                    });	            
                }
                else{
                        alert("Vermerkstatus nicht lesbar!");
                } 
                
            });
                        
            // Popover for Vermerk	
                $(function () {
                    $('[data-toggle="popover"]').popover();
                  });
                  
            
	 });
         
         // PDF erzeugen
        $("button[value='createLotVermerkePDF']").click(function(){           
            window.open('/pdf_createLotVermerkePDF.php?losID='+this.id);
        });



</script> 

</body>
</html>