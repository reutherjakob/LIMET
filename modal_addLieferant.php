
<div class='modal fade' id='addLieferantModal' role='dialog' tabindex="-1">
    <div class='modal-dialog modal-md'>
        <div class='modal-content'>
            <div class='modal-header'>
                <h4 class='modal-title'>Lieferant zu Gerät hinzufügen</h4>
                <button type='button' class='close' data-bs-dismiss='modal'>&times;</button>
            </div>
            <div class='modal-body' id='mbody'>
                <form role="form">
                    <?php
                    global $mysqli;
                    $deviceID = getPostInt("deviceID", (int)$_SESSION["deviceID"]);
                    $stmt = $mysqli->prepare("
        SELECT tabelle_lieferant.idTABELLE_Lieferant, tabelle_lieferant.Lieferant, 
               tabelle_lieferant.Land, tabelle_lieferant.Ort
        FROM tabelle_lieferant 
        WHERE tabelle_lieferant.idTABELLE_Lieferant NOT IN (
            SELECT tabelle_geraete_has_tabelle_lieferant.tabelle_lieferant_idTABELLE_Lieferant
            FROM tabelle_geraete_has_tabelle_lieferant
            WHERE tabelle_geraete_has_tabelle_lieferant.tabelle_geraete_idTABELLE_Geraete = ?
        )
        ORDER BY tabelle_lieferant.Lieferant");
                    $stmt->bind_param("i", $deviceID);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    echo "<div class='form-group'>
            <label for='Lieferant'>Lieferant:</label>                           
            <select class='form-control input-sm' id='idlieferant' name='lieferant'>
                <option value=0>Lieferant auswählen</option>
                <option value='new'>Nicht dabei? - Neu Anlegen!</option>";

                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='" . htmlspecialchars($row["idTABELLE_Lieferant"]) . "'>" .
                            htmlspecialchars($row["Lieferant"]) . " - " .
                            htmlspecialchars($row["Land"]) . " " .
                            htmlspecialchars($row["Ort"]) . "</option>";
                    }
                    echo "</select></div>";
                    ?>
                </form>

            </div>
            <div class='modal-footer'>
                <input type='button' id='addLieferant' class='btn btn-success btn-sm' value='Hinzufügen'
                       data-bs-dismiss='modal'>
                <button type='button' class='btn btn-warning btn-sm' data-bs-dismiss='modal'>Abbrechen</button>
            </div>
        </div>
    </div>
</div>