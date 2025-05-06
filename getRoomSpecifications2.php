<?php
// V2.0: 2024-11-29, Reuther & Fux
session_start();
if (!function_exists('utils_connect_sql')) {  include "_utils.php"; }
check_login();
?>

<!DOCTYPE html>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<html lang="de">
    <head>
        <style>
            .hr-sect {
                display: flex;
                flex-basis: 100%;
                align-items: center;
                color: rgba(0, 0, 0, 0.50);
                font-size: 12px;
                margin: 4px 0px;
            }
            .hr-sect::before,
            .hr-sect::after {
                content: "";
                flex-grow: 1;
                background: rgba(0, 0, 0, 0.35);
                height: 1px;
                font-size: 0px;
                line-height: 0px;
                margin: 0px 10px;
            }
        </style>
        <title>Get Roombook Specs</title>
    </head>
    <body>

        <?php
        $mysqli = utils_connect_sql();
        $sql = "SELECT `Anmerkung FunktionBO`, `Anmerkung Geräte`, `Anmerkung BauStatik`, `Anmerkung Elektro`, `Anmerkung MedGas`, `Anmerkung HKLS` 
                FROM tabelle_räume 
                WHERE idTABELLE_Räume = " . $_SESSION["roomID"];
        $result = $mysqli->query($sql);
        $row = $result->fetch_assoc();
        $mysqli->close();

        $anmerkungen = [
            "FunktionBO" => "Funktion BO",
            "Geräte" => "Geräte",
            "BauStatik" => "Bau/ Statik/ Schwingungsklasse",
            "Elektro" => "Elektro",
            "HKLS" => "HKLS",
            "MedGas" => "MedGas"
        ];

        echo "<form class='form-horizontal'>";
        foreach ($anmerkungen as $key => $label) {
            echo" <div class='form-group row'>
                    <label class='control-label col-xxl-12' for='$key'></label>
                    <div class='col-xxl-12 hr-sect'><b> $label </b></div>
                    <div class='col-xxl-12'>
                        <textarea class='form-control form-control-sm' rows='5' id='$key'>" . br2nl($row["Anmerkung " . ucfirst($key)]) . "</textarea>
                    </div>
                </div>";
        }
        echo "
            <div class='well well-sm'>
                <input type='button' id='saveBauangaben' class='btn btn-success btn-sm' value='Bauangaben speichern'>
                <input type='button' class='btn btn-info btn-sm' value='Bauangaben kopieren exkl. BO' id='" . $_SESSION["roomID"] . "' data-bs-toggle='modal' data-bs-target='#BauangabenModal'>
            </div>      </form>";

        echo " <!-- Modal zum Kopieren der Bauangaben -->
        <div class='modal fade' id='BauangabenModal' role='dialog'>
          <div class='modal-dialog modal-xl'>
            <!-- Modal content--> 
            <div class='modal-content'>
              <div class='modal-header'>                                  
                <h4 class='modal-title'>Bauangaben kopieren</h4>
                <button type='button' class='close' data-bs-dismiss='modal'>&times;</button>
              </div>
              <div class='modal-body' id='mbody2'>
              </div>
              <div class='modal-footer'>
                  <input type='button' id='copySpecifications' class='btn btn-success btn-sm' value='Bauangaben kopieren'></input>
                  <button type='button' class='btn btn-default btn-sm' data-bs-dismiss='modal'>Close</button>
              </div>
            </div>
          </div>
        </div>";
        ?>

        <script>
            //Bauangaben speichern
            $("input[value='Bauangaben speichern']").click(function () {
                let funktionBO = $("#FunktionBO").val();
                let Elektro = $("#Elektro").val();
                let geraete = $("#Geräte").val();
                let medgas = $("#MedGas").val();
                let baustatik = $("#BauStatik").val();
                let hkls = $("#HKLS").val();
                $.ajax({
                    url: "saveRoomSpecifications2.php",
                    data: {"funktionBO": funktionBO, "Elektro": Elektro, "geraete": geraete, "medgas": medgas, "baustatik": baustatik, "hkls": hkls},
                    type: "GET",
                    success: function (data) {
                        try {
                            makeToaster(data, data.substring(0,4) ==="Raum")
                        } catch (err) {
                            alert(data);
                        }

                    }
                });
            });

            //Bauangaben kopieren
            $("input[value='Bauangaben kopieren exkl. BO']").click(function () {
                let ID = this.id;
                //console.log("File: getRbSpecs2.ph M:BauangabenKopieren RID: ", ID);
                $.ajax({
                    url: "getRoomsToCopy.php",
                    type: "GET",
                    data: {"originRoomID": ID},
                    success: function (data) {
                        //console.log("Sucessfully opened getRoomsToCopy.php");
                        $("#mbody2").html(data);
                        $('#BauangabenModal').modal('show');
                    }
                });
            });
        </script>
    </body>
</html> 