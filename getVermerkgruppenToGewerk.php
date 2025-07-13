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

        $sql = "SELECT tabelle_Vermerkgruppe.idtabelle_Vermerkgruppe, tabelle_Vermerkgruppe.Gruppenname, tabelle_Vermerkgruppe.Gruppenart, tabelle_Vermerkgruppe.Ort, tabelle_Vermerkgruppe.Datum
                FROM tabelle_Vermerkgruppe INNER JOIN (tabelle_Vermerkuntergruppe INNER JOIN tabelle_Vermerke ON tabelle_Vermerkuntergruppe.idtabelle_Vermerkuntergruppe = tabelle_Vermerke.tabelle_Vermerkuntergruppe_idtabelle_Vermerkuntergruppe) ON tabelle_Vermerkgruppe.idtabelle_Vermerkgruppe = tabelle_Vermerkuntergruppe.tabelle_Vermerkgruppe_idtabelle_Vermerkgruppe
                WHERE (((tabelle_Vermerke.tabelle_lose_extern_idtabelle_Lose_Extern)=".filter_input(INPUT_GET, 'vermerkGruppenID')."))
                GROUP BY tabelle_Vermerkgruppe.idtabelle_Vermerkgruppe, tabelle_Vermerkgruppe.Gruppenname, tabelle_Vermerkgruppe.Gruppenart, tabelle_Vermerkgruppe.Ort, tabelle_Vermerkgruppe.Datum
                HAVING (((tabelle_Vermerkgruppe.Gruppenart)='ÖBA-Protokoll'))
                ORDER BY tabelle_Vermerkgruppe.Datum;";
        
        $result = $mysqli->query($sql);

        echo "<table class='table table-striped table-bordered table-sm table-hover border border-light border-5' id='tableVermerkgruppen'   >
                <thead><tr>
                <th>ID</th>                
                <th>Name</th>
                <th>Datum</th>                
                <th>Art</th>
                <th>Ort</th>                                
                </tr></thead><tbody>";   

        while ($row = $result->fetch_assoc()) {                                                    
            echo "<tr>";
            echo "<td>".$row['idtabelle_Vermerkgruppe']."</td>";
            echo "<td>".$row['Gruppenname']."</td>";
            echo "<td>".$row['Datum']."</td>";
            echo "<td>";
            switch ($row["Gruppenart"]) {
                case "Mailverkehr":
                  echo "<span class='badge badge-pill badge-info'> Mailverkehr </span>";
                  break;
                case "Telefonnotiz":
                  echo "<span class='badge badge-pill badge-dark'> Telefonnotiz </span>";
                  break;
                case "AV":
                  echo "<span class='badge badge-pill badge-warning'> AV </span>";
                  break;   
                case "Protokoll":
                  echo "<span class='badge badge-pill badge-primary'> Protokoll </span>";
                  break; 
                case "ÖBA-Protokoll":
                  echo "<span class='badge badge-pill badge-success'> ÖBA-Protokoll </span>";
                  break;
                default:
                  echo "Art unbekannt: ".$row['Gruppenart'];
              } 
            echo "</td>";
            echo "<td>".$row['Ort']."</td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
        				                                                    
        $mysqli ->close();
?>
<script>
    // Tabellen formatieren
    $(document).ready(function(){	                        
        var table = $('#tableVermerkgruppen').DataTable( {
            "select":true,
            "paging": false,
            "pagingType": "simple",
            "lengthChange": false,
            "pageLength": 20,
            "columnDefs": [
                {
                    "targets": [ 0 ],
                    "visible": false,
                    "searchable": false
                }
            ],
            "order": [[ 2, "asc" ]],
            "orderMulti": false,
            "language": {"url": "https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json"},
            "mark":true
        } );   

        $('#tableVermerkgruppen tbody').on( 'click', 'tr', function () {
            if ( $(this).hasClass('info') ) {
            }
            else {
                table.$('tr.info').removeClass('info');
                $(this).addClass('info');
                $('#pdfPreview').attr('src','PDFs/pdf_createVermerkGroupPDF.php?gruppenID='+table.row( $(this) ).data()[0]);
            }
        });
    });  
</script> 
</body>
</html>