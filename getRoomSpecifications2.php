<?php
session_start();
include '_utils.php';
check_login();
?>

<!DOCTYPE html>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<html>
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
    </head>
<body>
    
    <?php
    $mysqli = utils_connect_sql();
    $sql = "SELECT tabelle_räume.`Anmerkung FunktionBO`, tabelle_räume.`Anmerkung Geräte`, tabelle_räume.`Anmerkung BauStatik`, tabelle_räume.`Anmerkung Elektro`,";
    $sql .= "tabelle_räume.`Anmerkung MedGas`, tabelle_räume.`Anmerkung HKLS` FROM tabelle_räume WHERE (((tabelle_räume.idTABELLE_Räume)=" . $_SESSION["roomID"] . "));";
    $result = $mysqli->query($sql);
    $row = $result->fetch_assoc();
    $mysqli->close();

    echo"
        <form class='form-horizontal'>
        <div class='form-group row'>
                <label class='control-label col-md-12' for='funktionBO'></label>
                <div class='col-md-12 hr-sect'>  <b> Funktion BO </b> </div>
                <div class='col-md-12'>
                        <textarea class='form-control form-control-sm' rows='5' id='funktionBO'>" . br2nl($row["Anmerkung FunktionBO"]) . "</textarea>
                </div>
        </div>

        <div class='form-group row'>
                <label class='control-label col-md-12' for='geraete'></label> 
                <div class='col-md-12 hr-sect'><b> Geräte </b></div>
                <div class='col-md-12'>
                       <textarea class='form-control form-control-sm' rows='5' id='geraete'>" . br2nl($row["Anmerkung Geräte"]) . "</textarea>
                </div>
        </div>

        <div class='form-group row'>
                <label class='control-label col-md-12' for='baustatik'></label>
                <div class='col-md-12 hr-sect'> <b> Bau/ Statik/ Schwingungsklasse </b> </div>
                <div class='col-md-12'>
                       <textarea class='form-control form-control-sm' rows='5' id='baustatik'>" . br2nl($row["Anmerkung BauStatik"]) . "</textarea>
                </div>
        </div>

        <div class='form-group row'>
                <label class='control-label col-md-12' for='Elektro'></label>
                <div class='col-md-12 hr-sect'> <b> Elektro </b></div>
                <div class='col-md-12'>
                       <textarea class='form-control form-control-sm' rows='5' id='Elektro'>" . br2nl($row["Anmerkung Elektro"]) . "</textarea>
                </div>
        </div>

        <div class='form-group row'>
                <label class='control-label col-md-12' for='hkls'></label>
                <div class=' col-md-12 hr-sect'> <b> HKLS </b></div>
                <div class='col-md-12'>
                       <textarea class='form-control form-control-sm' rows='5' id='hkls'>" . br2nl($row["Anmerkung HKLS"]) . "</textarea>
                </div>
        </div>

        <div class='form-group row'>
                <label class='control-label col-md-12' for='medgas'></label>
                <div class='col-md-12 hr-sect'> <b> Medgas </b> </div>
                <div class='col-md-12'>
                       <textarea class='form-control form-control-sm' rows='5' id='medgas'>" . br2nl($row["Anmerkung MedGas"]) . "</textarea>
                </div>
        </div>
        
       
        <div class='well well-sm'>
            <input type='button' id='saveBauangaben' class='btn btn-success btn-sm' value='Bauangaben speichern'></input>
            <!-- Button für Modal -->
            <input type='button' class='btn btn-info btn-sm' value='Bauangaben kopieren exkl. BO' id='" . $_SESSION["roomID"] . "' data-toggle='modal' data-target='#myModal'></input>
        </div>
    </form>
    
        <!-- Modal zum kopieren der Bauangaben -->
        <div class='modal fade' id='myModal' role='dialog'>
          <div class='modal-dialog modal-lg'>
            <!-- Modal content-->
            <div class='modal-content'>
              <div class='modal-header'>                                  
                <h4 class='modal-title'>Bauangaben kopieren</h4>
                <button type='button' class='close' data-dismiss='modal'>&times;</button>
              </div>
              <div class='modal-body' id='mbody'>
              </div>
              <div class='modal-footer'>
                  <input type='button' id='copySpecifications' class='btn btn-success btn-sm' value='Bauangaben kopieren'></input>
                  <button type='button' class='btn btn-default btn-sm' data-dismiss='modal'>Close</button>
              </div>
            </div>
          </div>
        </div>";
        ?>

        <script>

            //Bauangaben speichern
            $("input[value='Bauangaben speichern']").click(function () {
                var funktionBO = $("#funktionBO").val();
                var Elektro = $("#Elektro").val();
                var geraete = $("#geraete").val();
                var medgas = $("#medgas").val();
                var baustatik = $("#baustatik").val();
                var hkls = $("#hkls").val(); 
                $.ajax({
                    url: "saveRoomSpecifications2.php",
                    data: {"funktionBO": funktionBO, "Elektro": Elektro, "geraete": geraete, "medgas": medgas, "baustatik": baustatik, "hkls": hkls },
                    type: "GET",
                    success: function (data) {
                        alert(data);
                    } 
                });

            });

            //Bauangaben kopieren
            $("input[value='Bauangaben kopieren exkl BO']").click(function () {
                var ID = this.id;
                console.log("File: getRbSpecs2.ph M:BauangabenKopieren RID: ", ID);
                $.ajax({ 
                    url: "getRoomsToCopy.php",
                    type: "GET",
                    data: {"id": ID},
                    success: function (data) {
                        $("#mbody").html(data);
                    }
                });
            });

        </script> 

    </body>
</html>