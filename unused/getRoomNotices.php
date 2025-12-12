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
					
	$sql="SELECT tabelle_notizen.idTABELLE_Notizen, tabelle_notizen.Datum, tabelle_notizen.Kategorie, tabelle_notizen.User, tabelle_notizen.Notiz_bearbeitet ";
    $sql .="FROM tabelle_notizen ";
    $sql .="WHERE (((tabelle_notizen.tabelle_räume_idTABELLE_Räume) = ".$_SESSION["roomID"].")) ";
    $sql .="ORDER BY tabelle_notizen.Datum DESC;";
	$result = $mysqli->query($sql);
	
	echo "<table class='table table-striped table-bordered table-condensed' id='tableRoomNotices'>
	<thead><tr>
	<th>ID</th>
	<th>Datum</th>
	<th>Status</th>
	<th>Kategorie</th>
	<th>User</th>
	</tr></thead><tbody>";
	

	while($row = $result->fetch_assoc()) {
	    echo "<tr>";
	    echo "<td>".$row["idTABELLE_Notizen"]."</td>";
	    //echo "<td><input type='button' id='".$row["idTABELLE_Notizen"]."' class='btn btn-success btn-sm' value='Notiz auswählen'></td>";
	    
	    echo "<td>".$row["Datum"]."</td>";
	    if($row["Notiz_bearbeitet"] == 0){
	    	echo "<td>Offen</td>";
	    }
	    else{
	    	if($row["Notiz_bearbeitet"] == 1){
		    	echo "<td>Bearbeitet</td>";
		    }
		    else{
		    	echo "<td>Info</td>";
		    }	    
	    }		
	    echo "<td>".$row["Kategorie"]."</td>";
	    echo "<td>".$row["User"]."</td>";
	    echo "</tr>";
	}
	echo "</tbody></table>";
	$mysqli ->close();
?>

<script>
	// Notiz auswählen
	/*
	$("input[value='Notiz auswählen']").click(function(){
	    var id=this.id; 
		
        $.ajax({
	        url : 'getNoticeData.php',
	        data:{'noticeID':id},
	        type: 'POST',
	        success: function(data){
	            $('#ProjektID').text(id);
	            $('#addNotice1').html(data);
	        } 
        });
          
    });
    */
    
    $(document).ready(function(){  
    	$('#tableRoomNotices').DataTable( {
    		"columnDefs": [
		            {
		                "targets": [ 0 ],
		                "visible": false,
		                "searchable": false
		            }
		    ],
			"paging": false,
			"searching": false,
			"info": false,
			"order": [[ 1, "desc" ]],
	        'language': {'url': 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json'},
	        "fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
	    			if ( aData[2] == "Offen" )
                    {
                        $('td', nRow).css('background-color', 'LightCoral');
                    }
                    else if ( aData[2] == "Bearbeitet" )
                    {
                        $('td', nRow).css('background-color', 'LightGreen');
                    }
                    else if ( aData[2] == "Info" )
                    {
                        $('td', nRow).css('background-color', 'DeepSkyBlue');
                    }
             }		        
	    } );
	
	 	// CLICK TABELLE Geräte IN DB
	    var table1 = $('#tableRoomNotices').DataTable();
 
	    $('#tableRoomNotices tbody').on( 'click', 'tr', function () {
			
	        if ( $(this).hasClass('info') ) {
	            //$(this).removeClass('info');
	        }
	        else {
	            table1.$('tr.info').removeClass('info');
	            $(this).addClass('info');
	            var noticeID = table1.row( $(this) ).data()[0];
	            $.ajax({
			        url : 'getNoticeData.php',
			        data:{'noticeID':noticeID},
			        type: 'POST',
			        success: function(data){
			            $('#addNotice1').html(data);
			        } 
		        });

	        }
	    });
	 });



</script> 

</body>
</html>