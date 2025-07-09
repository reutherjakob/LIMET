<?php
if (!function_exists('utils_connect_sql')) {
    include "utils/_utils.php";
}
init_page_serversides();
?>

<!DOCTYPE html>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
<html lang="de">
<head>
    <style>


    </style>
    <title></title>
</head>

<body>
<?php
$mysqli = utils_connect_sql();

$sql = "SELECT tabelle_räume.`Anmerkung FunktionBO` FROM tabelle_räume WHERE (((tabelle_räume.idTABELLE_Räume)=" . $_SESSION["roomID"] . "));";

$result = $mysqli->query($sql);
while ($row = $result->fetch_assoc()) {
    echo "
                <div class='row mt-4'>
                    <div class='col-xxl-4'>
                        <div class='card card-default m-2'>
                            <div class='card-header'>
                                <h4 class='m-b-2 text-dark'><i class='far fa-comment'></i> Anmerkungen</h4>
                            </div>            
                            <div class='card-body'>
                                <h4 class='m-t-2 text-dark'>" . $row["Anmerkung FunktionBO"] . "</h4>";
    echo "
                            </div>
                        </div>
                    </div>                    
                </div>
                ";
}

$mysqli->close();
?>


</body>
</html>