<?php
//
//Reutherer & Fux. LAst Update 14.5.24 
session_start();
include '_utils.php';
//check_login();
//
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
        
        $Grosgerateraum = (strpos($row["Anmerkung FunktionBO"], "Großgerät") !== false || strpos($row["Anmerkung Geräte"], "Großgerät") !== false);
        $GroßgeräteVorlagen = [
            "FunktionBO" => " ",
            "Geräte" => " ",
            "BauStatik" => "Gewicht im Raum: kg. \n\nBoden: \n maximale Punktlast statisch: kN. (Während des Transports bzw.
                während der Aufstellungsphase kann es zu Erhöhungen kommen (2 oder 3- Punktstand). Max. Punktlast dynamisch: ca. +/-  kN. \n vibrationsfreie Installation im Beereich der Bildgebenden Systeme. "
            . " Betonfundamentqualität: ; Alternativ: Sylodamp \n Bodenmontierter Tisch:. \n Schwingungstoleranz:  \n\nDecke: \n Deckenmontiertes Stativ: Maximum Zugkraft = ca.kN. Moment: kNm.  ",
            "Elektro" => " Leistung: \n Spitzenwert: kW \n Anschlusswert: kW \n Spannung:  \n maximaler Netzinnenwiederstand: mOhm. \n Anschlussklemmen: ; \n Fußboden leitfähig im Anlagenbereich",
            "HKLS" => " Variante Luftkühlung: Wärmeabgabe an Raum ca. kW
                Variante Wasserkühlung: Wärmeabgabe an Raum ca. kW + bis zu kW an Kühlwasserkreislauf.
                Wassertemp: . Durchfluss abhängig von
                T(H20):  l/h. Temperaturgradient(H2O): max.  K/min.: \n",
            "MedGas" => " "
        ];

        echo "<form class='form-horizontal'>";
        foreach ($anmerkungen as $key => $label) {
            if (!$Grosgerateraum) {
                echo "
                <div class='form-group row'>
                    <label class='control-label col-md-12' for='$key'></label>
                    <div class='col-md-12 hr-sect'><b> $label </b></div>
                    <div class='col-md-12'>
                        <textarea class='form-control form-control-sm' rows='5' id='$key'>" . br2nl($row["Anmerkung " . ucfirst($key)]) . "</textarea>
                    </div>
                </div>";
            } else {
                echo "
                <div class='form-group row'>
                    <label class='control-label col-md-12' for='$key'></label> 
                    <div class='col-md-12 hr-sect'><b> $label </b></div>
                        
                    <div class='col-md-2'>
                        <textarea readonly class='form-control form-control-sm' rows='5' id='$key'>" . $GroßgeräteVorlagen[ucfirst($key)] . "</textarea>
                    </div>
                    <div class='col-md-10'>
                        <textarea class='form-control form-control-sm' rows='5' id='$key'>" . br2nl($row["Anmerkung " . ucfirst($key)]) . "</textarea>
                    </div>
                </div>";
            }
        }
        echo "
            <div class='well well-sm'>
                <input type='button' id='saveBauangaben' class='btn btn-success btn-sm' value='Bauangaben speichern'>
                <input type='button' class='btn btn-info btn-sm' value='Bauangaben kopieren exkl. BO' id='" . $_SESSION["roomID"] . "' data-toggle='modal' data-target='#myModal'>
            </div>      </form>";

        echo " <!-- Modal zum kopieren der Bauangaben -->
        <div class='modal fade' id='myModal' role='dialog'>
          <div class='modal-dialog modal-lg'>
            <!-- Modal content--> 
            <div class='modal-content'>
              <div class='modal-header'>                                  
                <h4 class='modal-title'>Bauangaben kopieren</h4>
                <button type='button' class='close' data-dismiss='modal'>&times;</button>
              </div>
              <div class='modal-body' id='mbody2'>
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
                console.log("File: getRbSpecs2.ph M:BauangabenKopieren RID: ", ID);
                $.ajax({
                    url: "getRoomsToCopy.php",
                    type: "GET",
                    data: {"id": ID},
                    success: function (data) {
//                        alert(data); 
                        console.log("Sucessfully opened getRoomsToCopy.php", data);
                        $("#mbody2").html(data);
                    }
                });
            });

        </script> 

    </body>
</html> 