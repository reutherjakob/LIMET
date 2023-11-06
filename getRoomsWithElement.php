<?php
session_start();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
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

	$sql = "SELECT tabelle_räume_has_tabelle_elemente.id, tabelle_räume.idTABELLE_Räume, tabelle_varianten.Variante, tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung, tabelle_räume.`Raumbereich Nutzer`, tabelle_räume_has_tabelle_elemente.Anzahl, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, tabelle_räume_has_tabelle_elemente.Standort, tabelle_räume_has_tabelle_elemente.Verwendung, tabelle_räume_has_tabelle_elemente.Kurzbeschreibung, tabelle_räume.Geschoss
                FROM (tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) INNER JOIN tabelle_varianten ON tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten = tabelle_varianten.idtabelle_Varianten
                WHERE (((tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente)=".filter_input(INPUT_GET, 'elementID').") AND ((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"]."))
                ORDER BY tabelle_räume.Raumnr;";

	
	$result = $mysqli->query($sql);
	echo "<table class='table table-striped table-bordered table-sm' id='tableRoomsWithElements'  cellspacing='0' width='100%'>
	<thead><tr>
	<th>ID</th>
	<th>Var</th>
	<th>RaumNr</th>
	<th>Raum</th>
        <th>Ebene</th>
	<th>Bereich</th>
	<th>Stk</th>
	<th>Bestand</th>
	<th>Standort</th>
	<th>Verwendung</th>
	<th>Kommentar</th>
	<th>Speichern</th>
	</tr></thead><tbody>";
	
	
	while($row = $result->fetch_assoc()) {
	    echo "<tr>";
	    echo "<td>".$row["id"]."</td>";
	    echo "<td>".$row["Variante"]."</td>";
	    echo "<td>".$row["Raumnr"]."</td>";
	    echo "<td>".$row["Raumbezeichnung"]."</td>";
            echo "<td>".$row["Geschoss"]."</td>";
	    echo "<td>".$row["Raumbereich Nutzer"]."</td>";
            echo "<td><input class='form-control form-control-sm' type='text' id='amount".$row["id"]."' value='".$row["Anzahl"]."' size='2'></input></td>";
	   	echo "<td>";
   	    	if($row["Neu/Bestand"]==1){
   	    		echo "Nein";
   	    	}
   	    	else{
   	    		echo "Ja";
   	    	}
   	    echo "</td>";
	    echo "<td>";
   	    	if($row["Standort"]==1){
   	    		echo "Ja";
   	    	}
   	    	else{
   	    		echo "Nein";
   	    	}
   	    echo "</td>";
	    echo "<td>";
   	    	if($row["Verwendung"]==1){
   	    		echo "Ja";
   	    	}
   	    	else{
   	    		echo "Nein";
   	    	}
   	    echo "</td>";
            if(strlen($row["Kurzbeschreibung"])>0){
                echo "<td><button type='button' class='btn btn-xs btn-outline-dark' id='buttonComment".$row["id"]."' name='showComment' value='".$row["Kurzbeschreibung"]."' title='Kommentar'><i class='fa fa-comment'></i></button></td>";
            }
            else{
                echo "<td><button type='button' class='btn btn-xs btn-outline-dark' id='buttonComment".$row["id"]."' name='showComment' value='".$row["Kurzbeschreibung"]."' title='Kommentar'><i class='fa fa-comment-slash'></i></button></td>";
            }
            echo "<td><button type='button' id='".$row["id"]."' class='btn btn-warning btn-xs' value='saveElement'><i class='far fa-save'></i></button></td>";
	    echo "</tr>";
	}	
	echo "</tbody></table>";
	$mysqli ->close();
?>
<script>	    
    $(document).ready(function() {
            $('#tableRoomsWithElements').DataTable( {
                "paging": true,
                "searching": true,
                "info": false,
                "columnDefs": [
                {
                   "targets": [ 0 ],
                   "visible": false,
                   "searchable": false
                }
           ],
            "order": [[ 2, "asc" ]],
            "lengthMenu": [[5,10, 25, 50, -1], [5,10, 25, 50, "All"]],
           "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"},
           "scrollY":        '40vh',
           "scrollCollapse": true 	        
       } );

       var table = $('#tableRoomsWithElements').DataTable();

       $('#tableRoomsWithElements tbody').on( 'click', 'tr', function () {
           if ( $(this).hasClass('info') ) {
           }
           else {
               table.$('tr.info').removeClass('info');
               $(this).addClass('info');
           }
       } );
       
       // Popover for Comment            
        $("button[name='showComment']").popover({
            trigger : 'click',  
            placement : 'top', 
            html: true, 
            container : 'body',
            content: "<textarea class='popover-textarea'></textarea>",                                     		    
            template:"<div class='popover'>"+
                     "<h4 class='popover-header'></h4><div class='popover-body'>"+
                      "</div><div class='popover-footer'><button type='button' class='btn btn-xs btn-outline-dark popover-submit'><i class='fas fa-check'></i>"+
                      "</button>&nbsp;"+
                      "</div>"

            });

        $("button[name='showComment']").click(function(){                       
             //hide any visible comment-popover
             $("button[name='showComment']").not(this).popover('hide');
             var id = this.id;                     
             var val = document.getElementById(id).value;  
             //attach/link text
             $('.popover-textarea').val(val).focus(); 
             //update link text on submit    
             $('.popover-submit').click(function() {                              
                 document.getElementById(id).value = $('.popover-textarea').val();
                 $(this).parents(".popover").popover('hide');                     
             });
         });
         
         // Element speichern
        $("button[value='saveElement']").click(function(){
            var id=this.id; 
            var comment = $("#buttonComment"+id).val();
            var amount = Number($("#amount"+id).val());                        
            
            
            if(!Number.isInteger(amount)){
                alert("Stückzahl ist keine Zahl!");
            }
            else{         
                $.ajax({
                    url : "saveRoombookEntry2.php",
                    data:{"comment":comment,"id":id,"amount":amount},
                    type: "GET",
                    success: function(data){
                            alert(data);
                            location.reload(); 
                    } 
                });               
            }
        });

   } );
   
   

    

</script>

</body>
</html>