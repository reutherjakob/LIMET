<!DOCTYPE html>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
<html lang="">
<head>
    <title></title></head>
<body>

<?php

// 25 FX - unused
require_once 'utils/_utils.php';
check_login();
$mysqli = utils_connect_sql();


$sql = "SELECT f.`idtabelle_Files`, f.`Name` FROM `tabelle_Files` f WHERE f.`idtabelle_Files`
                NOT IN (SELECT `tabelle_Files`.`idtabelle_Files`
                    FROM `LIMET_RB`.`tabelle_Files` INNER JOIN `tabelle_Files_has_tabelle_Raeume` ON `tabelle_Files_has_tabelle_Raeume`.`tabelle_idfFile` = `tabelle_Files`.`idtabelle_Files`
                    WHERE `tabelle_Files`.`tabelle_projekte_idTABELLE_Projekte`= " . $_SESSION["projectID"] . " AND `tabelle_Files`.`tabelle_filetype_id` = 1 AND `tabelle_Files_has_tabelle_Raeume`.`tabelle_idRaeume` = " . filter_input(INPUT_POST, 'roomID') . ")
                AND f.`tabelle_projekte_idTABELLE_Projekte`= " . $_SESSION["projectID"] . ";";

$result = $mysqli->query($sql);

$imageCounter = 0;
echo "<div class='row'>";
while ($row = $result->fetch_assoc()) {
    if ($imageCounter > 0 && fmod($imageCounter, 8) == 0) {
        echo "</div>";
        echo "<div class='row'>";
    }
    echo "<div class='m-1 card'>";
    echo "<div class='card-header'>   
                            <button type='button' id='" . $row["idtabelle_Files"] . "' class='float-right btn btn-outline-success btn-sm' value='addImageToRoom'><i class='fas fa-plus'></i></button>                                           
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
    var roomID = <?php echo filter_input(INPUT_POST, 'roomID') ?>
        // Bild zu Raum hinzufügen
        $("button[value='addImageToRoom']").click(function () {
            var imageID = this.id;

            $.ajax({
                url: "addImageToRoom.php",
                data: {"imageID": imageID, "roomID": roomID},
                type: "POST",
                success: function (data) {
                    alert(data);
                    $.ajax({
                        url: "getImagesToRoom.php",
                        data: {"roomID": roomID},
                        type: "POST",
                        success: function (data) {
                            $("#roomImages").html(data);
                            $.ajax({
                                url: "getImagesNotInRoom.php",
                                data: {"roomID": roomID},
                                type: "POST",
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