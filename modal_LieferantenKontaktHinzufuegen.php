<?php
global $mysqli;
?>

<!-- Modal zum Anlegen eines Firmenkontakts -->
<div class='modal fade' id='addContactModal' role='dialog' tabindex="-1">
    <div class='modal-dialog modal-md'>
        <div class='modal-content'>
            <div class='modal-header'>
                <h4 class='modal-title'>Lieferantenkontakt hinzufügen</h4>
                <button type='button' class='close' data-bs-dismiss='modal'>&times;</button>
            </div>
            <div class='modal-body mb-2' id='mbody'>
                <form role="form">
                    <div class='form-group'>
                        <label for='lieferantenName'>Name</label>
                        <input type='text' class='form-control input-sm' id='lieferantenName'>
                    </div>
                    <div class='form-group'>
                        <label for='lieferantenVorname'>Vorname</label>
                        <input type='text' class='form-control input-sm' id='lieferantenVorname'>
                    </div>
                    <div class='form-group'>
                        <label class='control-label' for='lieferantenTel'>Tel</label>
                        <input type='text' class='form-control input-sm' id='lieferantenTel'>
                    </div>
                    <div class='form-group'>
                        <label class='control-label' for='lieferantenAdresse'>Adresse</label>
                        <input type='text' class='form-control input-sm' id='lieferantenAdresse'>
                    </div>
                    <div class='form-group'>
                        <label class='control-label' for='lieferantenPLZ'>PLZ</label>
                        <input type='text' class='form-control input-sm' id='lieferantenPLZ'>
                    </div>
                    <div class='form-group'>
                        <label class='control-label' for='lieferantenOrt'>Ort</label>
                        <input type='text' class='form-control input-sm' id='lieferantenOrt'>
                    </div>
                    <div class='form-group'>
                        <label class='control-label' for='lieferantenLand'>Land</label>
                        <input type='text' class='form-control input-sm' id='lieferantenLand'>
                    </div>
                    <div class='form-group'>
                        <label class='control-label' for='lieferantenGebiet'>Gebiet</label>
                        <input type='text' class='form-control input-sm' id='lieferantenGebiet'>
                    </div>
                    <div class='form-group'>
                        <label class='control-label' for='lieferantenEmail'>Email</label>
                        <input type='text' class='form-control input-sm' id='lieferantenEmail'>
                    </div>
                    <?php
                    $sql = "SELECT `tabelle_lieferant`.`idTABELLE_Lieferant`,
                                     `tabelle_lieferant`.`Lieferant`
                                 FROM `LIMET_RB`.`tabelle_lieferant` ORDER BY Lieferant;";
                    $result = $mysqli->query($sql);

                    echo "<div class='form-group'>
                            <label class='control-label' for='lieferant'>Lieferant</label>
                            <select class='form-control select2' id='lieferant' data-placeholder='Lieferant wählen'>";
                    echo "<option value='' disabled selected>Lieferant wählen</option>";
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='" . $row["idTABELLE_Lieferant"] . "'>" . htmlspecialchars($row["Lieferant"]) . "</option>";
                    }
                    echo "</select>
                        </div>";

                    $sql = "SELECT `tabelle_abteilung`.`idtabelle_abteilung`,
                                     `tabelle_abteilung`.`Abteilung`
                                 FROM `LIMET_RB`.`tabelle_abteilung` ORDER BY Abteilung;";
                    $result = $mysqli->query($sql);

                    echo "<div class='row'>
                        <div class='form-group'>
                            <label class='control-label'>Abteilung</label>
                            <div class='input-group flex-nowrap'>
                                <select class='form-control input-sm select2' id='abteilung' data-placeholder='Abteilung wählen'>
                                    <option value='' disabled selected>Abteilung wählen</option>";
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='" . $row["idtabelle_abteilung"] . "'>" . htmlspecialchars($row["Abteilung"]) . "</option>";
                    }
                    echo "</select> 
                            <button type='button' class='btn btn-sm btn-success' id='addAbteilungBtn' data-bs-toggle='modal' data-bs-target='#addAbteilungModal'>
                                 <i class='fa fa-plus'></i>
                             </button> 
                             </div> 
                              </div>";

                    echo "</div>";
                    $mysqli->close();
                    ?>
                </form>
            </div>
            <div class='modal-footer'>
                <div class="d-flex justify-content-center">
                    <input type='button' id='addLieferantenKontakt' class='btn btn-success btn-sm mt-4'
                           value='Hinzufügen'>
                    <input type='button' id='saveLieferantenKontakt' class='btn btn-warning btn-sm mt-4'
                           value='Speichern'>
                    <button type='button' class='btn btn-default btn-sm mt-4' data-bs-dismiss='modal'>Abbrechen</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal zum Hinzufügen einer neuen Abteilung -->
<div class='modal fade' id='addAbteilungModal' role='dialog' tabindex="-1">
    <div class='modal-dialog modal-sm'>
        <div class='modal-content'>
            <div class='modal-header'>
                <h4 class='modal-title'>Neue Abteilung hinzufügen</h4>
                <button type='button' class='close' data-bs-dismiss='modal'>&times;</button>
            </div>
            <div class='modal-body'>
                <form role="form">
                    <div class='form-group'>
                        <label for='newAbteilungName'>Abteilungsname</label>
                        <input type='text' class='form-control input-sm' id='newAbteilungName'
                               placeholder='Sicher, dass Abt. noch nicht gibt? '>
                    </div>
                </form>
            </div>
            <div class='modal-footer'>
                <button type='button' id='saveNewAbteilung' class='btn btn-success btn-sm'>Speichern</button>
                <button type='button' class='btn btn-default btn-sm' data-bs-dismiss='modal'>Abbrechen</button>
            </div>
        </div>
    </div>
</div>
