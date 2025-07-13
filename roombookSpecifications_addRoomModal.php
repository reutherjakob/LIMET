<div class='modal fade' id='addRoomModal' role='dialog' tabindex="-1">
    <div class='modal-dialog modal-md'>
        <div class='modal-content'>
            <div class='modal-header'>
                <h4 class='modal-title'>Raum hinzufügen &ensp;</h4>
                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
            </div>
            <div class='modal-body' id='mbody'>
                <form role="form">
                    <div class="form-group">
                        <label for="nummer">Nummer:</label>
                        <input type="text" class="form-control form-control-sm" id="nummer"/>
                    </div>
                    <div class="form-group">
                        <label for="name">Name:</label>
                        <input type="text" class="form-control form-control-sm" id="name"/>
                    </div>

                    <div class='form-group'>
                        <label for="funktionsstelle">Funktionsstelle:</label>
                        <select id="funktionsstelle" class="form-control form-control-sm">
                            <option value="">Funktionsstelle wählen</option>
                            <?php
                            $mysqli = utils_connect_sql();
                            $sql = "SELECT tabelle_funktionsteilstellen.Nummer, tabelle_funktionsteilstellen.Bezeichnung AS bez3, tabelle_funktionsteilstellen.idTABELLE_Funktionsteilstellen
                FROM (tabelle_funktionsteilstellen INNER JOIN tabelle_funktionsstellen ON tabelle_funktionsteilstellen.TABELLE_Funktionsstellen_idTABELLE_Funktionsstellen = tabelle_funktionsstellen.idTABELLE_Funktionsstellen) 
                INNER JOIN tabelle_funktionsbereiche ON tabelle_funktionsstellen.TABELLE_Funktionsbereiche_idTABELLE_Funktionsbereiche = tabelle_funktionsbereiche.idTABELLE_Funktionsbereiche
                ORDER BY Nummer;";
                            $result = $mysqli->query($sql);
                            while ($row = $result->fetch_assoc()) {
                                echo "<option value='" . $row['idTABELLE_Funktionsteilstellen'] . "'>" . $row['Nummer'] . " - " . $row['bez3'] . "</option>";
                            }
                            $mysqli->close();
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="mt-relevant">MT-relevant:</label>
                        <select class="form-control form-control-sm" id="mt-relevant">
                            <option value="0">Nein</option>
                            <option value="1">Ja</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class='modal-footer'>
                <input type='button' id='saveNewRoom' class='btn btn-warning btn-sm' value='Speichern'></input>
                <button type='button' class='btn btn-default btn-sm' data-bs-dismiss='modal'>Abbrechen</button>
            </div>
        </div>
    </div>
</div>



<script>
    function add_room_modal() {
        $('#funktionsstelle').select2({
            placeholder: "Funktionsstelle wählen",
            dropdownParent: $('#addRoomModal')
        });

        $('#addRoomModal').on('shown.bs.modal', function () {
            console.log('#addRoomModal');
            $('#funktionsstelle').select2({
                placeholder: "Funktionsstelle wählen",
                dropdownParent: $('#addRoomModal')
            });
        });

        $("#saveNewRoom").click(function () {
            let nummer = $("#nummer").val();
            let name = $("#name").val();
            let funktionsteilstelle = $("#funktionsstelle").val();
            let MTrelevant = $("#mt-relevant").val();
            console.log(funktionsteilstelle);
            save_new_room(nummer, name, funktionsteilstelle, MTrelevant);
        });

        function save_new_room(nummer, name, funktionsteilstelle, MTrelevant) {
            if (nummer !== "" && name !== "" && MTrelevant !== "" && funktionsteilstelle !== "") {
                $.ajax({
                    url: "addRoom_all.php",
                    data: {
                        "tabelle_projekte_idTABELLE_Projekte": <?php echo $_SESSION["projectID"]; ?>,
                        "Raumnr": nummer,
                        "Raumbezeichnung": name,
                        "TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen": funktionsteilstelle,
                        "MT-relevant": MTrelevant
                    },
                    type: "GET",
                    success: function (data) {
                        $('#addRoomModal').modal('hide');
                        alert(data);
                        window.location.replace("roombookSpecifications_New.php");
                    }
                });
            } else {
                alert("Bitte alle Felder ausfüllen!");
            }
        }
    }

</script>