<?php
    session_start();
?>

<!DOCTYPE html >
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
</head>
<body>
    
<?php
    if(!isset($_SESSION["username"]))
    {
        echo "Bitte erst <a href=\"index.php\">einloggen</a>";
        exit;
    }
    
    $mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');

    /* change character set to utf8 */
    if (!$mysqli->set_charset("utf8")) {
        printf("Error loading character set utf8: %s\n", $mysqli->error);
        exit();
    } 

    //Neue Elemente im Raum abfragen
    $sql = "SELECT tabelle_räume_has_tabelle_elemente.Anzahl, tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_lose_extern.LosNr_Extern, tabelle_lieferant.Lieferant, tabelle_räume_has_tabelle_elemente.Lieferdatum, tabelle_lose_extern.tabelle_lieferant_idTABELLE_Lieferant, tabelle_räume_has_tabelle_elemente.id, tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, tabelle_varianten.Variante
            FROM tabelle_lieferant RIGHT JOIN (((tabelle_räume_has_tabelle_elemente INNER JOIN tabelle_elemente ON tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente = tabelle_elemente.idTABELLE_Elemente) INNER JOIN tabelle_varianten ON tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten = tabelle_varianten.idtabelle_Varianten) LEFT JOIN tabelle_lose_extern ON tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern = tabelle_lose_extern.idtabelle_Lose_Extern) ON tabelle_lieferant.idTABELLE_Lieferant = tabelle_lose_extern.tabelle_lieferant_idTABELLE_Lieferant
            WHERE (((tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume)=".filter_input(INPUT_GET, 'roomID').") AND ((tabelle_räume_has_tabelle_elemente.`Neu/Bestand`)=1) AND tabelle_räume_has_tabelle_elemente.Standort = 1);";
    
    $result = $mysqli->query($sql);

    echo "<table class='table table-striped table-bordered table-sm' id='tableNewElements'   >
    <thead><tr>    
    <th>Stück</th>
    <th>Element</th>
    <th>Variante</th> 
    <th>Gewerk</th>
    <th>Lieferant</th>
    <th>Lieferdatum</th> 
    </tr></thead><tbody>";

    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>".$row["Anzahl"]."</td>";
        echo "<td>".$row["ElementID"]." ".$row["Bezeichnung"]."</td>";
        echo "<td>".$row["Variante"]."</td>"; 
        echo "<td>".$row["LosNr_Extern"]."</td>"; 
        echo "<td>".$row["Lieferant"]."</td>"; 
        echo "<td>".$row["Lieferdatum"]."</td>"; 
        echo "</tr>";

    }
    echo "</tbody></table>";
    $mysqli ->close();
?>
<script>
    $(document).ready(function(){	                        
        $('#tableNewElements').DataTable( {
            "select":false,
            "paging": true,
            "pagingType": "simple",
            "lengthChange": false,
            "pageLength": 10,
            "order": [[ 1, "asc" ]],
            "orderMulti": true,
            "language": {"url": "https://cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json"}
        } );
    });
</script>
</body>
</html>