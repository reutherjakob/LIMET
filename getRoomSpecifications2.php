<?php
// V2.0: 2024-11-29, Reuther & Fux
session_start();
include '_utils.php';
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
                    <label class='control-label col-md-12' for='$key'></label>
                    <div class='col-md-12 hr-sect'><b> $label </b></div>
                    <div class='col-md-12'>
                        <textarea class='form-control form-control-sm' rows='5' id='$key'>" . br2nl($row["Anmerkung " . ucfirst($key)]) . "</textarea>
                    </div>
                </div>";
        }
        echo "
            <div class='well well-sm'>
                <input type='button' id='saveBauangaben' class='btn btn-success btn-sm' value='Bauangaben speichern'>
                <input type='button' class='btn btn-info btn-sm' value='Bauangaben kopieren exkl. BO' id='" . $_SESSION["roomID"] . "' data-bs-toggle='modal' data-bs-target='#myModal'>
            </div>      </form>";

        echo " <!-- Modal zum kopieren der Bauangaben -->
        <div class='modal fade' id='myModal' role='dialog'>
          <div class='modal-dialog modal-lg'>
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
                var funktionBO = $("#FunktionBO").val();
                var Elektro = $("#Elektro").val();
                var geraete = $("#Geräte").val();
                var medgas = $("#MedGas").val();
                var baustatik = $("#BauStatik").val();
                var hkls = $("#HKLS").val();
                $.ajax({
                    url: "saveRoomSpecifications2.php",
                    data: {"funktionBO": funktionBO, "Elektro": Elektro, "geraete": geraete, "medgas": medgas, "baustatik": baustatik, "hkls": hkls},
                    type: "GET",
                    success: function (data) {
                        alert(data);
                    }
                });

            });

            //Bauangaben kopieren
            $("input[value='Bauangaben kopieren exkl. BO']").click(function () {
                var ID = this.id;
                //console.log("File: getRbSpecs2.ph M:BauangabenKopieren RID: ", ID);
                $.ajax({
                    url: "getRoomsToCopy.php",
                    type: "GET",
                    data: {"originRoomID": ID},
                    success: function (data) {
                        //console.log("Sucessfully opened getRoomsToCopy.php");
                        $("#mbody2").html(data);
                        $('#myModal').modal('show');
                    }
                });
            });
        </script>
    </body>
</html> 