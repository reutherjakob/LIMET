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
	
                
        //Abfrage der Gewerke
        $sql = "SELECT tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente
                FROM tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume
                WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"].") AND
                    TABELLE_Elemente_idTABELLE_Elemente NOT IN(
                                                                SELECT tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente
                                                                FROM tabelle_projekt_element_gewerk
                                                                WHERE (((tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"]."))
                )
                )
                GROUP BY tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente;";
            
	$result = $mysqli->query($sql);
        echo "<div class='m-1 row' id='checkGewerke'>";
        if($result->num_rows > 0){
            echo "<span class='badge badge-danger'>Gewerke zugeteilt</span>";
	}
        else{
            echo "<span class='badge badge-success'>Gewerke zugeteilt</span>";
        }
        echo "</div>";
        
        $sql = "SELECT tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten
                        FROM tabelle_projekt_varianten_kosten INNER JOIN (tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) ON (tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) AND (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente)
                        WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"].") AND ((tabelle_projekt_varianten_kosten.Kosten)='0') AND ((tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"]."))
                        GROUP BY tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten;
                        ";
        $result = $mysqli->query($sql);	
        echo "<div class='m-1 row' id='checkCosts'>";
        if($result->num_rows > 0){
            echo "<span class='badge badge-danger'>Kosten zugeordnet</span>";
	}
        else{
            echo "<span class='badge badge-success'>Kosten zugeordnet</span>";
        }
        echo "</div>";
        
        
        // Offene Protokollpunkte
        $sql = "SELECT tabelle_Vermerke.idtabelle_Vermerke
                FROM (tabelle_Vermerkuntergruppe INNER JOIN tabelle_Vermerkgruppe ON tabelle_Vermerkuntergruppe.tabelle_Vermerkgruppe_idtabelle_Vermerkgruppe = tabelle_Vermerkgruppe.idtabelle_Vermerkgruppe) INNER JOIN tabelle_Vermerke ON tabelle_Vermerkuntergruppe.idtabelle_Vermerkuntergruppe = tabelle_Vermerke.tabelle_Vermerkuntergruppe_idtabelle_Vermerkuntergruppe
                WHERE (((tabelle_Vermerkgruppe.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"].") AND ((tabelle_Vermerke.Vermerkart)='Bearbeitung') AND ((tabelle_Vermerke.Bearbeitungsstatus)=0));
                ";
        $result = $mysqli->query($sql);	
        echo "<div class='m-1 row' id='checkProtocols'>";
        if($result->num_rows > 0){
            echo "<span class='badge badge-danger'>Offene Protokollpunkte</span>";
	}
        else{
            echo "<span class='badge badge-success'>Offene Protokollpunkte</span>";
        }
        echo "</div>";
        
        // Elemente Losen zugeteilt?
        $sql = "SELECT tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern
                FROM tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume
                WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"].") AND ((tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern) Is Null));";
        $result = $mysqli->query($sql);	
        echo "<div class='m-1 row' id='checkLots'>";
        if($result->num_rows > 0){
            echo "<span class='badge badge-danger'>Elemente Losen zugeordnet</span>";
	}
        else{
            echo "<span class='badge badge-success'>Elemente Losen zugeordnet</span>";
        }
        echo "</div>";
        
	$mysqli ->close();
        
?>
<script>
</script> 

</body>
</html>