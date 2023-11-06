<?php
    session_start();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
    
    $sql = "SELECT tabelle_bauphasen.idtabelle_bauphasen, tabelle_bauphasen.bauphase, tabelle_bauphasen.datum_beginn, tabelle_bauphasen.datum_fertigstellung
            FROM (tabelle_räume_has_tabelle_elemente INNER JOIN tabelle_räume ON tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume = tabelle_räume.idTABELLE_Räume) LEFT JOIN tabelle_bauphasen ON tabelle_räume.tabelle_bauphasen_idtabelle_bauphasen = tabelle_bauphasen.idtabelle_bauphasen
            WHERE (((tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern)=".filter_input(INPUT_GET, 'lotID')."))
            GROUP BY tabelle_bauphasen.bauphase, tabelle_bauphasen.datum_beginn, tabelle_bauphasen.datum_fertigstellung
            ORDER BY tabelle_bauphasen.datum_beginn;";
    
    $result = $mysqli->query($sql);

    echo "<table class='table table-striped table-bordered table-sm' id='tableBauphasen'  cellspacing='0' width='100%'>
    <thead><tr>  
    <th>id</th>
    <th>Bauphase</th>
    <th>Beginn</th>
    <th>Fertigstellung</th> 
    </tr></thead><tbody>";

    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>".$row["idtabelle_bauphasen"]."</td>";
        echo "<td>".$row["bauphase"]."</td>";
        echo "<td>".$row["datum_beginn"]."</td>";
        echo "<td>".$row["datum_fertigstellung"]."</td>";        
        echo "</tr>";

    }
    echo "</tbody></table>";
    $mysqli ->close();
?>
    
    
<script>
    $(document).ready(function(){	                        
        $('#tableBauphasen').DataTable( {
            "select": true,
            "searching": false,
            "paging": false,
            "pagingType": "simple",
            "lengthChange": false,
            "pageLength": 10,
            "order": [[ 1, "asc" ]],
            "orderMulti": false,
            "language": {"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"},
            "columnDefs": [
                {
                    "targets": [ 0 ],
                    "visible": false,
                    "searchable": false
                }
            ]
        } );                       
    });                  
    
</script>
</body>
</html>