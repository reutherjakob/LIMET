<!-- Modal zum Ändern des Raumes -->
<div class='modal fade' id='addRoomModal' role='dialog'>
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
                        <input type="text"  class="form-control form-control-sm" id="name"/>
                    </div>
                    <div class='form-group'>
                        <div class="dropdown">
                            <button onclick="myFunction(event)" class="dropbtn form-control form-control-sm">Funktionsstelle wählen</button>
                            <div id="myDropdown" class="dropdown-content">
                                <input type="text" placeholder="Search.." id="myInput" onkeyup="filterFunction()">
                                <?php
                                $mysqli = utils_connect_sql();
                                $funktionsTeilstellen = array();
                                $sql = "SELECT tabelle_funktionsteilstellen.Nummer, tabelle_funktionsteilstellen.Bezeichnung AS bez3, tabelle_funktionsteilstellen.idTABELLE_Funktionsteilstellen
                                                FROM (tabelle_funktionsteilstellen INNER JOIN tabelle_funktionsstellen ON tabelle_funktionsteilstellen.TABELLE_Funktionsstellen_idTABELLE_Funktionsstellen = tabelle_funktionsstellen.idTABELLE_Funktionsstellen) 
                                                INNER JOIN tabelle_funktionsbereiche ON tabelle_funktionsstellen.TABELLE_Funktionsbereiche_idTABELLE_Funktionsbereiche = tabelle_funktionsbereiche.idTABELLE_Funktionsbereiche
                                                ORDER BY Nummer;";

                                $result = $mysqli->query($sql);
                                while ($row = $result->fetch_assoc()) {
                                    $funktionsTeilstellen[$row['idTABELLE_Funktionsteilstellen']]['idTABELLE_Funktionsteilstellen'] = $row['idTABELLE_Funktionsteilstellen'];
                                    $funktionsTeilstellen[$row['idTABELLE_Funktionsteilstellen']]['Nummer'] = $row['Nummer'];
                                    $funktionsTeilstellen[$row['idTABELLE_Funktionsteilstellen']]['Name'] = $row['bez3'];
                                }

                                $mysqli->close();

                                foreach ($funktionsTeilstellen as $array) {
                                    echo "<div href='#' data-value='" . $array['idTABELLE_Funktionsteilstellen'] . "'>" . $array['Nummer'] . " - " . $array['Name'] . "</div>";
                                }
                                ?>
                            </div>
                        </div>
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
    function myFunction(event) {
        event.preventDefault();
        document.getElementById("myDropdown").classList.toggle("show");
    }

    function filterFunction() {
        const input = document.getElementById("myInput");
        const filter = input.value.toUpperCase();
        const div = document.getElementById("myDropdown");
        const a = div.getElementsByTagName("div");
        for (let i = 0; i < a.length; i++) {
            const txtValue = a[i].textContent || a[i].innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                a[i].style.display = "";
            } else {
                a[i].style.display = "none";
            }
        }
    }
</script>

<style>
    .dropbtn:hover, .dropbtn:focus {
        background-color: #3e8e41;
    }

    #myInput {
        box-sizing: border-box;
        background-image: url('searchicon.png');
        background-position: 14px 12px;
        background-repeat: no-repeat;
        font-size: 16px;
        padding: 14px 20px 12px 45px;
        border: none;
        border-bottom: 1px solid #ddd;
        width: 100%; /* Set width to 100% to match input fields */
    }

    #myInput:focus {
        outline: 3px solid #ddd;
    }

    .dropdown {
        position: relative;
        display: inline-block;
        width: 100%; /* Set width to 100% to match input fields */
    }

    .dropdown-content {
        display: none;
        position: absolute;
        background-color: #f6f6f6;
        min-width: 100%; /* Set min-width to 100% to match input fields */
        overflow: auto;
        border: 1px solid #ddd;
        z-index: 1;
        right: 0; /* Align to the right of the button */
    }

    .dropdown-content div {
        color: black;
        padding: 12px 16px;
        text-decoration: none;
        display: block;
    }

    .dropdown-content div:hover {
        background-color: #ddd;
    }

    .show {
        display: block;
    }

</style>
