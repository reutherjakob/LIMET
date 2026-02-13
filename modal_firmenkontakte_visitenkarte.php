<?php echo" 
<div class='modal fade' id='showAddressCard' role='dialog' tabindex='-1'>
    <div class='modal-dialog modal-sm'>
        <div class='modal-content'>
            <div class='modal-header'>
                <h4 class='modal-title'>Kontaktdaten</h4>
                <button type='button' class='close' data-bs-dismiss='modal'>&times;</button>
            </div>
            <div class='modal-body' id='mbody'>
                <address class='m-t-md'>
                    <strong><label class='control-label' id='cardName'></label></strong><br>
                    <label class='control-label' id='cardLieferant'></label><br>
                    <label class='control-label' id='cardAddress'></label><br>
                    <label class='control-label' id='cardPlace'></label><br>
                    <abbr title='Phone'>T: </abbr><label class='control-label' id='cardTel'></label><br>
                    <abbr title='Mail'>M: </abbr><label class='control-label' id='cardMail'></label><br>
                </address>
            </div>
            <div class='modal-footer'>
            </div>
        </div>
    </div>
</div>
";