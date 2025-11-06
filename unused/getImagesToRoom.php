<!DOCTYPE html>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
<html lang="">
<head>
    <title></title></head>
<body>
<?php

// 10-2025 FX - unused
require_once 'utils/_utils.php';
check_login();
$mysqli = utils_connect_sql();
$roomID = filter_input(INPUT_GET, 'roomID', FILTER_VALIDATE_INT);
if ($roomID === null || $roomID === false) {
    echo "Ungültige RaumID!";
    exit;
}

$stmt = $mysqli->prepare("
    SELECT tabelle_Files.idtabelle_Files, tabelle_Files.Name, 
           tabelle_Files_has_tabelle_Raeume.tabelle_idRaeume
    FROM tabelle_Files
    INNER JOIN tabelle_Files_has_tabelle_Raeume 
        ON tabelle_Files_has_tabelle_Raeume.tabelle_idfFile = tabelle_Files.idtabelle_Files
    WHERE tabelle_Files_has_tabelle_Raeume.tabelle_idRaeume = ?
");
$stmt->bind_param("i", $roomID);
$stmt->execute();
$result = $stmt->get_result();


$imageCounter = 0;
echo "<div class='row'>";
while ($row = $result->fetch_assoc()) {
    if ($imageCounter > 0 && fmod($imageCounter, 8) == 0) {
        echo "</div>";
        echo "<div class='row'>";
    }
    echo "<div class='m-1 card'>";
    echo "<div class='card-header'>                                               
                            <button type='button' id='" . $row["idtabelle_Files"] . "' class='float-right btn btn-outline-danger btn-sm' value='removeImageFromRoom'><i class='fas fa-minus'></i></button>   
                         </div>";
    echo "<div class='card-body'>";
    echo "<img src='https://limet-rb.com/Dokumente_RB/Images/" . $row['Name'] . "' height='200' width='200'>";
    echo "</div>";
    echo "</div>";
    $imageCounter++;
}
echo "</div>";
$mysqli->close();

?>

<script>
    var roomID = <?php echo filter_input(INPUT_GET, 'roomID') ?>
        // Bild zu Raum hinzufügen
        $("button[value='removeImageFromRoom']").click(function () {
            var imageID = this.id;

            $.ajax({
                url: "deleteImageFromRoom.php",
                data: {"imageID": imageID, "roomID": roomID},
                type: "GET",
                success: function (data) {
                    alert(data);
                    $.ajax({
                        url: "getImagesToRoom.php",
                        data: {"roomID": roomID},
                        type: "GET",
                        success: function (data) {
                            $("#roomImages").html(data);
                            $.ajax({
                                url: "getImagesNotInRoom.php",
                                data: {"roomID": roomID},
                                type: "GET",
                                success: function (data) {
                                    $("#projectImages").html(data);
                                }
                            });
                        }
                    });
                }
            });
        });
</script>
</body>
</html>