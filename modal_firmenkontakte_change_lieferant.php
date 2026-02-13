<?php
echo "
<div class='modal fade' id='changeLieferantModal' role='dialog' tabindex='-1'>
    <div class='modal-dialog modal-md'>
        <!-- Modal content-->
        <div class='modal-content'>
            <div class='modal-header'>
                <h4 class='modal-title'>Lieferant</h4>
                <button type='button' class='close' data-bs-dismiss='modal'>&times;</button>
            </div>
            <form role='form' id='changeLieferantForm'>
                <div class='modal-body' id='mbody'>

                    <input type='hidden' id='lieferantID'>

                    <div class='form-group'>
                        <label for='firma'>Lieferant</label>
                        <input type='text' class='form-control form-control-sm' id='firma' required>
                    </div>
                    <div class='form-group'>
                        <label class='control-label' for='lieferantTel'>Tel</label>
                        <input type='text' class='form-control form-control-sm' id='lieferantTel' required>
                    </div>
                    <div class='form-group'>
                        <label class='control-label' for='lieferantAdresse'>Adresse</label>
                        <input type='text' class='form-control form-control-sm' id='lieferantAdresse' required>
                    </div>
                    <div class='form-group'>
                        <label class='control-label' for='lieferantPLZ'>PLZ</label>
                        <input type='text' class='form-control form-control-sm' id='lieferantPLZ' required>
                    </div>
                    <div class='form-group'>
                        <label class='control-label' for='lieferantOrt'>Ort</label>
                        <input type='text' class='form-control form-control-sm' id='lieferantOrt' required>
                    </div>
                    <div class='form-group'>
                        <label class='control-label' for='lieferantLand'>Land</label>
                        <input type='text' class='form-control form-control-sm' id='lieferantLand' required>
                    </div>

                </div>

                <div class='modal-footer'>
                    <input type='button' id='addLieferant' class='btn btn-success btn-sm me-1'
                           value='Hinzufügen'>
                    <input type='button' id='saveLieferant' class='btn btn-warning btn-sm me-1'
                           value='Speichern'>
                    <button type='button' class='btn btn-default btn-sm' data-bs-dismiss='modal'>Abbrechen
                    </button>
                </div>
            </form>
        </div>
    </div>
</div> ";