<!DOCTYPE html >
<html xmlns="http://www.w3.org/1999/xhtml" lang="">
<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <title></title></head>
<body>
<?php
require_once 'utils/_utils.php';
include "utils/_format.php";
check_login();
$mysqli = utils_connect_sql();

// Check if required variables are set
if (isset($_SESSION["projectID"], $_GET["id"]) && is_numeric($_SESSION["projectID"]) && is_numeric($_GET["id"])) {

    $projectID = (int)$_SESSION["projectID"];
    $elementID = (int)$_GET["id"];

    // Use a prepared statement for security
    $sql = "SELECT tabelle_projekt_varianten_kosten.Kosten
            FROM tabelle_projekt_varianten_kosten
            INNER JOIN tabelle_räume_has_tabelle_elemente 
                ON tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten 
               AND tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente
            WHERE tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte = ? 
              AND tabelle_räume_has_tabelle_elemente.id = ?";

    $stmt = $mysqli->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ii", $projectID, $elementID);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            echo "
                <div class='d-flex flex-wrap justify-content-end'>
                    <span class='badge rounded-pill bg-light text-dark border-dark m-1 p-2'>     
                        <span class='fw-normal'>Kosten: </span>
                        <span class='fw-bold'>" . format_money($row["Kosten"]) . "</span>
                    </span>
                </div>";
        } else {
            echo "<div class='text-danger'>Keine Kosten gefunden.</div>";
        }

        $stmt->close();
    } else {
        echo "<div class='text-danger'>Datenbankfehler: " . $mysqli->error . "</div>";
    }

} else {
    echo "<div class='text-danger'>Fehlende oder ungültige Parameter.</div>";
}

$mysqli->close();
?>

    
	    

</script>

</body>
</html>