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
					
    
    $sql = "SELECT tabelle_notizen.idtabelle_notizen, tabelle_notizen.Datum, tabelle_notizen.Kategorie, tabelle_notizen.User, tabelle_notizen.Notiz_bearbeitet, tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung, tabelle_räume.`Raumbereich Nutzer`
			FROM tabelle_räume INNER JOIN tabelle_notizen ON tabelle_räume.idTABELLE_Räume = tabelle_notizen.tabelle_räume_idTABELLE_Räume
			WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"]."))
			ORDER BY tabelle_notizen.Datum DESC;";
    
	$result = $mysqli->query($sql);
	
	echo "<table class='table table-striped table-bordered table-condensed' id='tableProjectNotices'>
	<thead><tr>
	<th>ID</th>
	<th>Datum</th>
	<th>Status</th>
	<th>Kategorie</th>
	<th>User</th>
	<th>Raumbereich Nutzer</th>
	<th>Raumnr</th>
	<th>Raumbezeichnung</th>
	</tr></thead><tbody>";
	

	while($row = $result->fetch_assoc()) {
	    echo "<tr>";
	    echo "<td>".$row["idtabelle_notizen"]."</td>";
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
	    echo "<td>".$row["Raumbereich Nutzer"]."</td>";
		echo "<td>".$row["Raumnr"]."</td>";
		echo "<td>".$row["Raumbezeichnung"]."</td>";
	    echo "</tr>";
	}
	echo "</tbody></table>";
	$mysqli ->close();
?>

<script>
    
    $(document).ready(function(){  
    	$('#tableProjectNotices').DataTable( {
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
	        'language': {'url': '//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json'},
	        "scrollY":        '20vh',
	    	"scrollCollapse": true,
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
	    var table1 = $('#tableProjectNotices').DataTable();
 
	    $('#tableProjectNotices tbody').on( 'click', 'tr', function () {
			
	        if ( $(this).hasClass('info') ) {
	            //$(this).removeClass('info');
	        }
	        else {
	            table1.$('tr.info').removeClass('info');
	            $(this).addClass('info');
	            var noticeID = table1.row( $(this) ).data()[0];
	            
	            $.ajax({
			        url : 'getNoticeData.php',
			        data:{'noticeID':noticeID,"newNoticeButton":"0"},
			        type: 'GET',
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