<?php
require_once '_utils/_utils.php';
init_page_serversides();
$mysqli = utils_connect_sql();
$roomId = isset($_SESSION['roomID']) ? (int)$_SESSION['roomID'] : 0;
$sql = "SELECT tabelle_räume.`Anmerkung FunktionBO`
        FROM tabelle_räume
        WHERE tabelle_räume.idTABELLE_Räume = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $roomId);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    echo "
                <div class='row mt-4'>
                    <div class='col-xxl-4'>
                        <div class='card card-default m-2'>
                            <div class='card-header'>
                                <h4 class='m-b-2 text-dark'><i class='far fa-comment'></i> Anmerkungen</h4>
                            </div>            
                            <div class='card-body'>
                                <h4 class='m-t-2 text-dark'>" . htmlspecialchars($row["Anmerkung FunktionBO"]?? "") . "</h4>
                            </div>
                        </div>
                    </div>                    
                </div>
                ";
}
$stmt->close();
$mysqli->close();
?>



