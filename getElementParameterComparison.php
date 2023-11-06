<?php
session_start();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />


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
	
        $sql1 = "SET @sql = NULL;SET group_concat_max_len=15000;";
        if($result1 = $mysqli->query($sql1)){

        }
        
        $sqlx = "SET group_concat_max_len=15000;";
        if($resultx = $mysqli->query($sqlx)){

        }
        
        $sql2 = "SELECT 
                    GROUP_CONCAT(DISTINCT
                          CONCAT(
                            'MAX(IF(tabelle_parameter.Bezeichnung = ''',
                            tabelle_parameter.Bezeichnung,
                            ''', CONCAT( tabelle_projekt_elementparameter.Wert, tabelle_projekt_elementparameter.Einheit), NULL)) AS ',
                            tabelle_parameter.Bezeichnung
                          )
                    ) INTO @sql
                  FROM tabelle_projekte RIGHT JOIN (tabelle_varianten RIGHT JOIN (tabelle_parameter_kategorie RIGHT JOIN (tabelle_parameter RIGHT JOIN (tabelle_projekt_elementparameter RIGHT JOIN tabelle_elemente ON tabelle_projekt_elementparameter.tabelle_elemente_idTABELLE_Elemente = tabelle_elemente.idTABELLE_Elemente) ON tabelle_parameter.idTABELLE_Parameter = tabelle_projekt_elementparameter.tabelle_parameter_idTABELLE_Parameter) ON tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie = tabelle_parameter.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie) ON tabelle_varianten.idtabelle_Varianten = tabelle_projekt_elementparameter.tabelle_Varianten_idtabelle_Varianten) ON tabelle_projekte.idTABELLE_Projekte = tabelle_projekt_elementparameter.tabelle_projekte_idTABELLE_Projekte
                  WHERE (((tabelle_elemente.idTABELLE_Elemente)=".$_GET["elementID"]."));";
        
        if($result2 = $mysqli->query($sql2)){

        }        
        
                
        $sql3 = "SET @sql = CONCAT('SELECT tabelle_projekte.Interne_Nr, ', @sql, ' 
                FROM tabelle_projekte RIGHT JOIN (tabelle_varianten RIGHT JOIN (tabelle_parameter_kategorie RIGHT JOIN (tabelle_parameter RIGHT JOIN (tabelle_projekt_elementparameter RIGHT JOIN tabelle_elemente ON tabelle_projekt_elementparameter.tabelle_elemente_idTABELLE_Elemente = tabelle_elemente.idTABELLE_Elemente) ON tabelle_parameter.idTABELLE_Parameter = tabelle_projekt_elementparameter.tabelle_parameter_idTABELLE_Parameter) ON tabelle_parameter_kategorie.idTABELLE_Parameter_Kategorie = tabelle_parameter.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie) ON tabelle_varianten.idtabelle_Varianten = tabelle_projekt_elementparameter.tabelle_Varianten_idtabelle_Varianten) ON tabelle_projekte.idTABELLE_Projekte = tabelle_projekt_elementparameter.tabelle_projekte_idTABELLE_Projekte
                WHERE (((tabelle_elemente.idTABELLE_Elemente)=".$_GET["elementID"].")) GROUP BY tabelle_projekte.idTABELLE_Projekte, tabelle_varianten.Variante');";
        
        
        if($resultX = $mysqli->query($sql3)){
            
        }
        
        $sql4 = "PREPARE stmt FROM @sql;";
        if($result4 = $mysqli->query($sql4)){
            
        } 
        
        $sql5 = "EXECUTE stmt;";
        if($result5 = $mysqli->query($sql5)){
            
            echo "<table class='table table-striped table-condensed' id='tableElementParameterComparison' cellspacing='0'>";
            echo "<thead><tr>";
            $finfo = $result5->fetch_fields();
            foreach ($finfo as $val) {
                echo "<th>".$val->name."</th>";    
                
            }
            echo "</tr></thead><tbody>";
            
            while ($row = $result5->fetch_assoc()) {
                echo "<tr>";                
                foreach ($finfo as $val) {
                    echo "<td>".$row[$val->name]."</td>";
                }
                echo "</tr>";
            }           
            echo "</tbody></table>";    
        } 
        
	$mysqli ->close();
	?>
	
<script>
       
   $("#tableElementParameterComparison").DataTable( {
		"paging": false,
		"searching": false,
		"info": false,
		"order": [[ 0, "desc" ]],
                //"pagingType": "simple_numbers",
                //"lengthMenu": [[5,10, 25, 50, -1], [5,10, 25, 50, "All"]],
                "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"},
                "scrollX": true
    } );

</script>

</body>
</html>