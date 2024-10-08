<?php
session_start();
include '_utils.php';
check_login();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type" /></head>
    <body>
        <?php
        $mysqli = utils_connect_sql();
        $sql = "SELECT tabelle_räume_has_tabelle_elemente.id, tabelle_räume_has_tabelle_elemente.TABELLE_Geraete_idTABELLE_Geraete, tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten, tabelle_räume_has_tabelle_elemente.Anzahl, tabelle_elemente.ElementID, tabelle_elemente.Kurzbeschreibung As `Elementbeschreibung`, tabelle_varianten.Variante, tabelle_elemente.Bezeichnung, tabelle_geraete.GeraeteID, tabelle_hersteller.Hersteller, tabelle_geraete.Typ, tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, tabelle_räume_has_tabelle_elemente.Standort, tabelle_räume_has_tabelle_elemente.Verwendung, tabelle_räume_has_tabelle_elemente.Kurzbeschreibung, tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, tabelle_räume_has_tabelle_elemente.TABELLE_Geraete_idTABELLE_Geraete
			FROM tabelle_varianten INNER JOIN (tabelle_hersteller RIGHT JOIN ((tabelle_räume_has_tabelle_elemente LEFT JOIN tabelle_geraete ON tabelle_räume_has_tabelle_elemente.TABELLE_Geraete_idTABELLE_Geraete = tabelle_geraete.idTABELLE_Geraete) INNER JOIN tabelle_elemente ON tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente = tabelle_elemente.idTABELLE_Elemente) ON tabelle_hersteller.idtabelle_hersteller = tabelle_geraete.tabelle_hersteller_idtabelle_hersteller) ON tabelle_varianten.idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten
			WHERE (((tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume)=" . $_SESSION["roomID"] . "))
			ORDER BY tabelle_elemente.ElementID;";
        $result = $mysqli->query($sql);
        $mysqli->close(); 

//	<th>ID</th>
//	<th>Kommentar</th>
        echo"<table class='table table-responsive table-striped table-bordered table-sm' id='tableRoomElements" . $_SESSION["roomID"] . "' cellspacing='0' >
	<thead><tr>

	
	<th style='width:50%; '>Element</th>
        <th class='customIDCell'>ID</th>
        <th>Stück</th>
	<th>Var.</th>
	<th>Best.</th>
	<th>Ort</th> 
	<th>Verw.</th> 

	</tr></thead>
	<tbody>";
//<!--	<th>Geräte ID</th>-->
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
//            echo "<td>" . $row["id"] . "</td>";
            echo "<td>" . $row["Bezeichnung"] . "</td>";
            echo "<td>" . $row["ElementID"] . "</td>";           
      
            echo "<td>" . $row["Anzahl"] . "</td>";
            echo "<td>" . $row["Variante"] . "</td>";
            echo "<td>";
            if ($row["Neu/Bestand"] == 1) {
                echo "Nein";
            } else {
                echo "Ja";
            }
            echo "</td>";
            echo "<td>";
            if ($row["Standort"] == 1) {
                echo "Ja";
            } else {
                echo "Nein";
            }
            echo "</td>";
            echo "<td>";
            if ($row["Verwendung"] == 1) {
                echo "Ja";
            } else {
                echo "Nein";
            }
            echo "</td>";
//            echo "<td class='cols-md-2'><textarea id='comment" . $row["id"] . "' rows='1' style='width: 100%;'>" . $row["Kurzbeschreibung"] . "</textarea></td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
        ?>
        <script>
//            $(document).ready(function () {
//                var tablel = $("#tableRoomElements", $_SESSION["roomID"] ).DataTable({
//                    searching: true,
//                    info: true,
//                    responsive:true,
//                    select: true, 
//                    compact:true,
//                    order: [[1, "asc"]],
//                    lengthChange: false,
//                    columnDefs: [
//                        {"targets": [0,8 ], "visible": false, "searchable": false}
//                    ],
//                    paging: false, 
//                    pageLength: -1,
//                    sDom: "ti" 
//                }); 
//            });
        </script>
    </body>
</html>