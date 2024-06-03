<?php
session_start();
?>

<!DOCTYPE html>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<html>
    <head>
    </head>
    <body>
        <?php
        if (!isset($_SESSION["username"])) {
            echo "Bitte erst <a href=\"index.php\">einloggen</a>";
            exit;
        }

        function br2nl($string) {
            $return = str_replace(array("<br/>"), "\n", $string);
            return $return;
        }
        ?>

        <?php
        $mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');

        /* change character set to utf8 */
        if (!$mysqli->set_charset("utf8")) {
            printf("Error loading character set utf8: %s\n", $mysqli->error);
            exit();
        }

        $sql = "SELECT tabelle_räume.`Anmerkung FunktionBO`, tabelle_räume.`Anmerkung Geräte`, tabelle_räume.`Anmerkung BauStatik`, tabelle_räume.`Anmerkung Elektro`, tabelle_räume.`Raumtyp BH`, ";
        $sql .= "tabelle_räume.`Anmerkung MedGas`, tabelle_räume.`Anmerkung HKLS`, tabelle_räume.Abdunkelbarkeit, tabelle_räume.Strahlenanwendung, tabelle_räume.Laseranwendung, ";
        $sql .= "tabelle_räume.Anwendungsgruppe, tabelle_räume.AV, tabelle_räume.SV, tabelle_räume.ZSV, tabelle_räume.USV, tabelle_räume.`ET_EMV_ja-nein`, tabelle_räume.`ET_EMV`, tabelle_räume.`EL_Leistungsbedarf_W_pro_m2`, tabelle_räume.H6020, tabelle_räume.ISO, tabelle_räume.GMP, tabelle_räume.HT_Waermeabgabe, tabelle_räume.`HT_Luftwechsel 1/h`,   ";
        $sql .= "tabelle_räume.`1 Kreis O2`, tabelle_räume.`2 Kreis O2`, tabelle_räume.`1 Kreis Va`, tabelle_räume.`2 Kreis Va`, tabelle_räume.`1 Kreis DL-5`, tabelle_räume.`2 Kreis DL-5`, tabelle_räume.`DL-10`, ";
        $sql .= "tabelle_räume.`H2`, tabelle_räume.`He`, tabelle_räume.`He-RF`, tabelle_räume.`Ar`, tabelle_räume.`N2`, tabelle_räume.`HT_Geraeteabluft m3/h`, tabelle_räume.`HT_Kühlwasserleistung_W`,";
        $sql .= "tabelle_räume.`DL-tech`, tabelle_räume.CO2, tabelle_räume.NGA, tabelle_räume.N2O, tabelle_räume.`Allgemeine Hygieneklasse`, tabelle_räume.`IT Anbindung`, tabelle_räume.`Fussboden OENORM B5220`, tabelle_räume.RaumNr_Bestand, tabelle_räume.Gebaeude_Bestand, tabelle_räume.AR_Schwingungsklasse, tabelle_räume.HT_Notdusche FROM tabelle_räume WHERE (((tabelle_räume.idTABELLE_Räume)=" . $_SESSION["roomID"] . "));";

        $result = $mysqli->query($sql);

        $row = $result->fetch_assoc();

        echo "<form>
                    <div class='form-group row'>
                        <div class='col-md-1'>Allgemein</div>
                        <label class='control-label col-md-1' for='bestandsraumNr'>Raum-Nr Bestand:</label>
                        <div class='col-md-2'>
                               <input type='text' class=form-control form-control-xs' id='bestandsraumNr' value='" . $row["RaumNr_Bestand"] . "'>
                        </div>
                        <label class='control-label col-md-1' for='bestandsGeb'>Gebäude Bestand:</label>
                        <div class='col-md-2'>
                               <input type='text' class=form-control form-control-xs' id='bestandsGeb' value='" . $row["Gebaeude_Bestand"] . "'>
                        </div>
                        <label class='control-label col-md-1' for='raumTypBH'>Typ BH:</label>
                        <div class='col-md-2'>
                               <input type='text' class=form-control form-control-xs' id='raumTypBH' value='" . $row["Raumtyp BH"] . "'>
                        </div>
                    </div>
                        <div class='form-group row'>
                                <div class='col-md-1'></div>
                                <label class='control-label col-md-2' for='strahlenanwendung'>Strahlenanwendung</label>
                                <div class='col-md-1'>
                                        <select class='form-control form-control-sm' id='strahlenanwendung'>";
        if ($row["Strahlenanwendung"] == 1) {
            echo "<option selected>Ja</option>
                                                                  <option>Nein</option>
                                                                  <option>Quasi Stationär</option>";
        } else {
            if ($row["Strahlenanwendung"] == 0) {

                echo "<option>Ja</option>
                                                                          <option selected>Nein</option>
                                                                          <option>Quasi Stationär</option>";
            } else {
                echo "<option>Ja</option>
                                                                          <option>Nein</option>
                                                                          <option selected>Quasi Stationär</option>";
            }
        }
        echo "</select>	
                                </div>
                                <label class='checkbox-inline col-md-2'>";
        if ($row["Abdunkelbarkeit"] == 1) {
            echo "<input id='abdunkelbarkeit' checked data-toggle='toggle' data-on='Ja' data-off='Nein' data-onstyle='success' data-offstyle='danger' data-size='mini'  type='checkbox' ></input>Abdunkelbarkeit";
        } else {
            echo "<input id='abdunkelbarkeit' data-toggle='toggle' data-on='Ja' data-off='Nein' data-onstyle='success' data-offstyle='danger' data-size='mini'  type='checkbox' ></input>Abdunkelbarkeit";
        }
        echo "</label>
                        <label class='checkbox-inline col-md-2'>";
        if ($row["Laseranwendung"] == 1) {
            echo "<input id='laseranwendung' checked data-toggle='toggle' data-on='Ja' data-off='Nein' data-onstyle='success' data-offstyle='danger' data-size='mini'  type='checkbox' ></input>Laseranwendung";
        } else {
            echo "<input id='laseranwendung' data-toggle='toggle' data-on='Ja' data-off='Nein' data-onstyle='success' data-offstyle='danger' data-size='mini'  type='checkbox' ></input>Laseranwendung";
        }
        echo "</label>                       
                        
                        </div>
                        <hr>
                        <div class='form-group row'>
                                <label class='control-label col-md-1' for='funktionBO'>FunktionBO</label>
                                <div class='col-md-11'>
                                        <textarea class='form-control form-control-sm' rows='5' id='funktionBO'>" . br2nl($row["Anmerkung FunktionBO"]) . "</textarea>
                                </div>
                        </div>
                        <hr>
                        <div class='form-group row'>
                                 <label class='control-label col-md-1' for='geraete'>Geräte</label>
                                 <div class='col-md-11'>
                                        <textarea class='form-control form-control-sm' rows='5' id='geraete'>" . br2nl($row["Anmerkung Geräte"]) . "</textarea>
                                 </div>
                         </div>
                         <hr>                         
                         <div class='form-group row'>
                                 <label class='control-label col-md-1' for='baustatik'>Bau/Statik</label>
                                 <label class='control-label col-md-1' for='schwingungsklasse'>Schwingungsklasse</label>
                                <div class='col-md-1'>
                                        <select class='form-control form-control-sm' id='schwingungsklasse'>";
        switch ($row["AR_Schwingungsklasse"]) {
            case "VC-A":
                echo "<option></option>
                                                            <option selected>VC-A</option>
                                                            <option>VC-B</option>
                                                            <option>VC-C</option>
                                                            <option>VC-D</option>
                                                            <option>VC-E</option>";
                break;
            case "VC-B":
                echo "<option></option>
                                                            <option>VC-A</option>
                                                            <option selected>VC-B</option>
                                                            <option>VC-C</option>
                                                            <option>VC-D</option>
                                                            <option>VC-E</option>";
                break;
            case "VC-C":
                echo "<option></option>
                                                            <option>VC-A</option>
                                                            <option>VC-B</option>
                                                            <option selected>VC-C</option>
                                                            <option>VC-D</option>
                                                            <option>VC-E</option>";
                break;
            case "VC-D":
                echo "<option></option>
                                                            <option>VC-A</option>
                                                            <option>VC-B</option>
                                                            <option>VC-C</option>
                                                            <option selected>VC-D</option>
                                                            <option>VC-E</option>";
                break;
            case "VC-E":
                echo "<option></option>
                                                            <option>VC-A</option>
                                                            <option>VC-B</option>
                                                            <option>VC-C</option>
                                                            <option>VC-D</option>
                                                            <option selected>VC-E</option>";
                break;
            case "":
                echo "<option selected></option>
                                                            <option>VC-A</option>
                                                            <option>VC-B</option>
                                                            <option>VC-C</option>
                                                            <option>VC-D</option>
                                                            <option>VC-E</option>";
                break;
        }
        echo "</select>	
                                </div>	
                        </div>
                        <div class='form-group row'>
                                <div class='col-md-1'></div>
                                <div class='col-md-11'>
                                        <textarea class='form-control form-control-sm' rows='5' id='baustatik'>" . br2nl($row["Anmerkung BauStatik"]) . "</textarea>
                                 </div>
                         </div>
                         <hr>
                         <div class='form-group row'>
                            <div class='col-md-1'>Elektro</div>
                            <label class='control-label col-md-1' for='awg'>ÖVE E8101</label>
                            <div class='col-md-1'>
                                <select class='form-control form-control-sm' id='awg'>";
        switch ($row["Anwendungsgruppe"]) {
            case "-":
                echo "<option selected>-</option>
                                                <option>0</option>
                                                <option>1</option>
                                                <option>2</option>";
                break;
            case "":
                echo "<option selected>-</option>
                                                <option>0</option>
                                                <option>1</option>
                                                <option>2</option>";
                break;
            case "0":
                echo "<option>-</option>
                                                <option selected>0</option>
                                                <option>1</option>
                                                <option>2</option>";
                break;
            case "1":
                echo "<option>-</option>
                                                <option>0</option>
                                                <option selected>1</option>
                                                <option>2</option>";
                break;
            case "2":
                echo "<option>-</option>
                                                <option>0</option>
                                                <option>1</option>
                                                <option selected>2</option>";
                break;
        }
        echo "</select>						
                            </div>
                            
                                <label class='control-label col-md-1' for='fussbodenklasse'>B5220</label>
                                <div class='col-md-1'>
                                <select class='form-control form-control-sm' id='fussbodenklasse'>";
        switch ($row["Fussboden OENORM B5220"]) {
            case "kA":
                echo "<option selected>kA</option>
                                                <option>Klasse 1</option>
                                                <option>Klasse 2</option>
                                                <option>Klasse 3</option>";
                break;
            case "Klasse 1":
                echo "<option>kA</option>
                                                <option selected>Klasse 1</option>
                                                <option>Klasse 2</option>
                                                <option>Klasse 3</option>";

                break;
            case "Klasse 2":
                echo "<option>kA</option>
                                                <option>Klasse 1</option>
                                                <option selected>Klasse 2</option>
                                                <option>Klasse 3</option>";
                break;
            case "Klasse 3":
                echo "<option>kA</option>
                                                <option>Klasse 1</option>
                                                <option>Klasse 2</option>
                                                <option selected>Klasse 3</option>";
                break;
        }
        echo "</select>						
                            </div>
                            <label class='checkbox-inline col-md-1'>";
        if ($row["IT Anbindung"] == 1) {
            echo "<input id='it' checked data-toggle='toggle' data-on='Ja' data-off='Nein' data-onstyle='success' data-offstyle='danger' data-size='mini'  type='checkbox' ></input> IT";
        } else {
            echo "<input id='it' data-toggle='toggle' data-on='Ja' data-off='Nein' data-onstyle='success' data-offstyle='danger' data-size='mini'  type='checkbox' ></input> IT";
        }
        echo "</label>
                            <label class='checkbox-inline col-md-1'>";
        if ($row["AV"] == 1) {
            echo "<input id='av' checked data-toggle='toggle' data-on='Ja' data-off='Nein' data-onstyle='success' data-offstyle='danger' data-size='mini'  type='checkbox' ></input> AV";
        } else {
            echo "<input id='av' data-toggle='toggle' data-on='Ja' data-off='Nein' data-onstyle='success' data-offstyle='danger' data-size='mini'  type='checkbox' ></input> AV";
        }
        echo "</label>
                            <label class='checkbox-inline col-md-1'>";
        if ($row["SV"] == 1) {
            echo "<input id='sv' checked data-toggle='toggle' data-on='Ja' data-off='Nein' data-onstyle='success' data-offstyle='danger' data-size='mini'  type='checkbox' ></input> SV";
        } else {
            echo "<input id='sv' data-toggle='toggle' data-on='Ja' data-off='Nein' data-onstyle='success' data-offstyle='danger' data-size='mini'  type='checkbox' ></input> SV";
        }
        echo "</label>	
                            <label class='checkbox-inline col-md-1'>";
        if ($row["ZSV"] == 1) {
            echo "<input id='zsv' checked data-toggle='toggle' data-on='Ja' data-off='Nein' data-onstyle='success' data-offstyle='danger'  data-size='mini' type='checkbox' ></input> ZSV";
        } else {
            echo "<input id='zsv' data-toggle='toggle' data-on='Ja' data-off='Nein' data-onstyle='success' data-offstyle='danger'  data-size='mini' type='checkbox' ></input> ZSV";
        }
        echo "</label>	
                            <label class='checkbox-inline col-md-1'>";
        if ($row["USV"] == 1) {
            echo "<input id='usv' checked data-toggle='toggle' data-on='Ja' data-off='Nein' data-onstyle='success' data-offstyle='danger'  data-size='mini' type='checkbox' ></input> USV";
        } else {
            echo "<input id='usv' data-toggle='toggle' data-on='Ja' data-off='Nein' data-onstyle='success' data-offstyle='danger'  data-size='mini' type='checkbox' ></input> USV";
        }
        echo "</label>
                            <label class='checkbox-inline col-md-1'>";
        if ($row["ET_EMV_ja-nein"] == 1) {
            echo "<input id='emv' checked data-toggle='toggle' data-on='Ja' data-off='Nein' data-onstyle='success' data-offstyle='danger'  data-size='mini' type='checkbox' ></input> EMV";
        } else {
            echo "<input id='emv' data-toggle='toggle' data-on='Ja' data-off='Nein' data-onstyle='success' data-offstyle='danger'  data-size='mini' type='checkbox' ></input> EMV";
        }
        echo "</label>                                
                        </div>
                        <div class='form-group row'>  
                            <div class='col-md-1'></div>                            
                            <label class='control-label col-md-1' for='leistungsbedarfET'>Leistung W/m2:</label>
                            <div class='col-md-1'>
                                   <input type='number' class=form-control form-control-xs' id='leistungsbedarfET' value='" . $row["EL_Leistungsbedarf_W_pro_m2"] . "'>
                            </div>
                        </div>
                            <div class='form-group row'>
                                <label class='control-label col-md-1' for='Elektro'></label>
                                <div class='col-md-11'>
                                       <textarea class='form-control form-control-sm' rows='5' id='Elektro'>" . br2nl($row["Anmerkung Elektro"]) . "</textarea>
                                </div>
                        </div>
                        <hr>
                        <div class='form-group row'>
                                <div class='col-md-1'>HKLS</div>
                                <label class='control-label col-md-1' for='h6020'>Raumklasse H6020</label>
                                <div class='col-md-1'>
                                        <select class='form-control form-control-sm' id='h6020'>";
        switch ($row["H6020"]) {
            case "H1a":
                echo "<option selected>H1a</option>
                                                                        <option>H1b</option>
                                                                        <option>H2a</option>
                                                                        <option>H2b</option>
                                                                                <option>H2c</option>
                                                                                <option>H3</option>
                                                                                <option>H4</option>";
                break;
            case "H1b":
                echo "<option>H1a</option>
                                                                        <option selected>H1b</option>
                                                                        <option>H2a</option>
                                                                        <option>H2b</option>
                                                                                <option>H2c</option>
                                                                                <option>H3</option>
                                                                                <option>H4</option>";

                break;
            case "H2a":
                echo "<option>H1a</option>
                                                                        <option>H1b</option>
                                                                        <option selected>H2a</option>
                                                                        <option>H2b</option>
                                                                                <option>H2c</option>
                                                                                <option>H3</option>
                                                                                <option>H4</option>";

                break;
            case "H2b":
                echo "<option>H1a</option>
                                                                        <option>H1b</option>
                                                                        <option>H2a</option>
                                                                        <option selected>H2b</option>
                                                                                <option>H2c</option>
                                                                                <option>H3</option>
                                                                                <option>H4</option>";

                break;
            case "H2c":
                echo "<option>H1a</option>
                                                                        <option>H1b</option>
                                                                        <option>H2a</option>
                                                                        <option>H2b</option>
                                                                                <option selected>H2c</option>
                                                                                <option>H3</option>
                                                                                <option>H4</option>";

                break;
            case "H3":
                echo "<option>H1a</option>
                                                                        <option>H1b</option>
                                                                        <option>H2a</option>
                                                                        <option>H2b</option>
                                                                                <option>H2c</option>
                                                                                <option selected>H3</option>
                                                                                <option>H4</option>";

                break;
            case "H4":
                echo "<option>H1a</option>
                                                                        <option>H1b</option>
                                                                        <option>H2a</option>
                                                                        <option>H2b</option>
                                                                                <option>H2c</option>
                                                                                <option>H3</option>
                                                                                <option selected>H4</option>";

                break;
            case "":
                echo "<option data-hidden='true'></option>
                                                        <option>H1a</option>
                                                                        <option>H1b</option>
                                                                        <option>H2a</option>
                                                                        <option>H2b</option>
                                                                                <option>H2c</option>
                                                                                <option>H3</option>
                                                                                <option>H4</option>";

                break;
        }
        echo "</select>	
                                </div>	
                                <label class='control-label col-md-1' for='iso'>Raumklasse ISO</label>
                                <div class='col-md-1'>
                                        <select class='form-control form-control-sm' id='iso'>";
        switch ($row["ISO"]) {
            case "1":
                echo "<option selected>1</option>
                                                                        <option>2</option>
                                                                        <option>3</option>
                                                                        <option>4</option>
                                                                        <option>5</option>
                                                                        <option>6</option>
                                                                        <option>7</option>
                                                                        <option>8</option>
                                                                        <option>9</option>";
                break;
            case "2":
                echo "<option>1</option>
                                                                        <option selected>2</option>
                                                                        <option>3</option>
                                                                        <option>4</option>
                                                                        <option>5</option>
                                                                        <option>6</option>
                                                                        <option>7</option>
                                                                        <option>8</option>
                                                                        <option>9</option>";
                break;
            case "3":
                echo "<option>1</option>
                                                                        <option>2</option>
                                                                        <option selected>3</option>
                                                                        <option>4</option>
                                                                        <option>5</option>
                                                                        <option>6</option>
                                                                        <option>7</option>
                                                                        <option>8</option>
                                                                        <option>9</option>";
                break;
            case "4":
                echo "<option>1</option>
                                                                        <option>2</option>
                                                                        <option>3</option>
                                                                        <option selected>4</option>
                                                                        <option>5</option>
                                                                        <option>6</option>
                                                                        <option>7</option>
                                                                        <option>8</option>
                                                                        <option>9</option>";
                break;
            case "5":
                echo "<option>1</option>
                                                                        <option>2</option>
                                                                        <option>3</option>
                                                                        <option>4</option>
                                                                        <option selected>5</option>
                                                                        <option>6</option>
                                                                        <option>7</option>
                                                                        <option>8</option>
                                                                        <option>9</option>";
                break;
            case "6":
                echo "<option>1</option>
                                                                        <option>2</option>
                                                                        <option>3</option>
                                                                        <option>4</option>
                                                                        <option>5</option>
                                                                        <option selected>6</option>
                                                                        <option>7</option>
                                                                        <option>8</option>
                                                                        <option>9</option>";
                break;
            case "7":
                echo "<option>1</option>
                                                                        <option>2</option>
                                                                        <option>3</option>
                                                                        <option>4</option>
                                                                        <option>5</option>
                                                                        <option>6</option>
                                                                        <option selected>7</option>
                                                                        <option>8</option>
                                                                        <option>9</option>";
                break;
            case "8":
                echo "<option >1</option>
                                                                        <option>2</option>
                                                                        <option>3</option>
                                                                        <option>4</option>
                                                                        <option>5</option>
                                                                        <option>6</option>
                                                                        <option>7</option>
                                                                        <option selected>8</option>
                                                                        <option>9</option>";
                break;
            case "9":
                echo "<option>1</option>
                                                                        <option>2</option>
                                                                        <option>3</option>
                                                                        <option>4</option>
                                                                        <option>5</option>
                                                                        <option>6</option>
                                                                        <option>7</option>
                                                                        <option>8</option>
                                                                        <option selected>9</option>";
                break;
            case "":
                echo "<option data-hidden='true'></option>
                                                                        <option>1</option>
                                                                        <option>2</option>
                                                                        <option>3</option>
                                                                        <option>4</option>
                                                                        <option>5</option>
                                                                        <option>6</option>
                                                                        <option>7</option>
                                                                        <option>8</option>
                                                                        <option>9</option>";
                break;
        }

        echo "</select>	
                                </div>	
                                <label class='control-label col-md-1' for='gmp'>Raumklasse GMP</label>
                                <div class='col-md-1'>
                                        <select class='form-control form-control-sm' id='gmp'>";
        switch ($row["GMP"]) {
            case "A":
                echo "<option selected>A</option>
                                                                        <option>B</option>
                                                                        <option>C</option>
                                                                        <option>D</option>
                                                                        <option>E</option>";
                break;
            case "B":
                echo "<option>A</option>
                                                                        <option selected>B</option>
                                                                        <option>C</option>
                                                                        <option>D</option>
                                                                        <option>E</option>";
                break;
            case "C":
                echo "<option>A</option>
                                                                        <option>B</option>
                                                                        <option selected>C</option>
                                                                        <option>D</option>
                                                                        <option>E</option>";
                break;
            case "D":
                echo "<option>A</option>
                                                                        <option>B</option>
                                                                        <option>C</option>
                                                                        <option selected>D</option>
                                                                        <option>E</option>";
                break;
            case "E":
                echo "<option>A</option>
                                                                        <option>B</option>
                                                                        <option>C</option>
                                                                        <option>D</option>
                                                                        <option selected>E</option>";
                break;
            case "":
                echo "<option data-hidden='true'></option>
                                                                        <option>A</option>
                                                                        <option>B</option>
                                                                        <option>C</option>
                                                                        <option>D</option>
                                                                        <option>E</option>";
                break;
        }
        echo "</select>	
                                </div>	
                                <label class='control-label col-md-1' for='hygieneklasse'>Raumeinteilung nach</label>
                                <div class='col-md-2'>
                                        <select class='form-control form-control-sm' id='hygieneklasse'>";
        switch ($row["Allgemeine Hygieneklasse"]) {
            case "ÖAK - I - Ordination- und Behandlung":
                echo "<option selected>ÖAK - I - Ordination- und Behandlung</option>
                                                                        <option>ÖAK - II - klein Invasiv</option>
                                                                        <option>ÖAK - III - Eingriffsraum</option>
                                                                        <option>ÖAK - IV - OP</option>
                                                                        <option>MA 15 - LL 28 - OP</option>
                                                                        <option>MA 15 - LL 28 - Eingriffsraum</option>
                                                                        <option>MA 15 - LL 28 - Behandlungsraum invasiv</option>
                                                                        <option>Gentechnikgesetz - S1</option>
                                                                        <option>Gentechnikgesetz - S2</option>
                                                                        <option>Gentechnikgesetz - S3</option>
                                                                        <option>Gentechnikgesetz - S4</option>";
                break;
            case "ÖAK - II - klein Invasiv":
                echo "<option >ÖAK - I - Ordination- und Behandlung</option>
                                                                        <option selected>ÖAK - II - klein Invasiv</option>
                                                                        <option>ÖAK - III - Eingriffsraum</option>
                                                                        <option>ÖAK - IV - OP</option>
                                                                        <option>MA 15 - LL 28 - OP</option>
                                                                        <option>MA 15 - LL 28 - Eingriffsraum</option>
                                                                        <option>MA 15 - LL 28 - Behandlungsraum invasiv</option>
                                                                        <option>Gentechnikgesetz - S1</option>
                                                                        <option>Gentechnikgesetz - S2</option>
                                                                        <option>Gentechnikgesetz - S3</option>
                                                                        <option>Gentechnikgesetz - S4</option>";
                break;
            case "ÖAK - III - Eingriffsraum":
                echo "<option >ÖAK - I - Ordination- und Behandlung</option>
                                                                        <option>ÖAK - II - klein Invasiv</option>
                                                                        <option selected>ÖAK - III - Eingriffsraum</option>
                                                                        <option>ÖAK - IV - OP</option>
                                                                        <option>MA 15 - LL 28 - OP</option>
                                                                        <option>MA 15 - LL 28 - Eingriffsraum</option>
                                                                        <option>MA 15 - LL 28 - Behandlungsraum invasiv</option>
                                                                        <option>Gentechnikgesetz - S1</option>
                                                                        <option>Gentechnikgesetz - S2</option>
                                                                        <option>Gentechnikgesetz - S3</option>
                                                                        <option>Gentechnikgesetz - S4</option>";
                break;
            case "ÖAK - IV - OP":
                echo "<option >ÖAK - I - Ordination- und Behandlung</option>
                                                                        <option>ÖAK - II - klein Invasiv</option>
                                                                        <option>ÖAK - III - Eingriffsraum</option>
                                                                        <option selected>ÖAK - IV - OP</option>
                                                                        <option>MA 15 - LL 28 - OP</option>
                                                                        <option>MA 15 - LL 28 - Eingriffsraum</option>
                                                                        <option>MA 15 - LL 28 - Behandlungsraum invasiv</option>
                                                                        <option>Gentechnikgesetz - S1</option>
                                                                        <option>Gentechnikgesetz - S2</option>
                                                                        <option>Gentechnikgesetz - S3</option>
                                                                        <option>Gentechnikgesetz - S4</option>";
                break;
            case "MA 15 - LL 28 - OP":
                echo "<option >ÖAK - I - Ordination- und Behandlung</option>
                                                                        <option>ÖAK - II - klein Invasiv</option>
                                                                        <option>ÖAK - III - Eingriffsraum</option>
                                                                        <option>ÖAK - IV - OP</option>
                                                                        <option selected>MA 15 - LL 28 - OP</option>
                                                                        <option>MA 15 - LL 28 - Eingriffsraum</option>
                                                                        <option>MA 15 - LL 28 - Behandlungsraum invasiv</option>
                                                                        <option>Gentechnikgesetz - S1</option>
                                                                        <option>Gentechnikgesetz - S2</option>
                                                                        <option>Gentechnikgesetz - S3</option>
                                                                        <option>Gentechnikgesetz - S4</option>";
                break;
            case "MA 15 - LL 28 - Eingriffsraum":
                echo "<option >ÖAK - I - Ordination- und Behandlung</option>
                                                                        <option>ÖAK - II - klein Invasiv</option>
                                                                        <option>ÖAK - III - Eingriffsraum</option>
                                                                        <option>ÖAK - IV - OP</option>
                                                                        <option>MA 15 - LL 28 - OP</option>
                                                                        <option selected>MA 15 - LL 28 - Eingriffsraum</option>
                                                                        <option>MA 15 - LL 28 - Behandlungsraum invasiv</option>
                                                                        <option>Gentechnikgesetz - S1</option>
                                                                        <option>Gentechnikgesetz - S2</option>
                                                                        <option>Gentechnikgesetz - S3</option>
                                                                        <option>Gentechnikgesetz - S4</option>";
                break;
            case "MA 15 - LL 28 - Behandlungsraum invasiv":
                echo "<option >ÖAK - I - Ordination- und Behandlung</option>
                                                                        <option>ÖAK - II - klein Invasiv</option>
                                                                        <option>ÖAK - III - Eingriffsraum</option>
                                                                        <option>ÖAK - IV - OP</option>
                                                                        <option>MA 15 - LL 28 - OP</option>
                                                                        <option>MA 15 - LL 28 - Eingriffsraum</option>
                                                                        <option selected>MA 15 - LL 28 - Behandlungsraum invasiv</option>
                                                                        <option>Gentechnikgesetz - S1</option>
                                                                        <option>Gentechnikgesetz - S2</option>
                                                                        <option>Gentechnikgesetz - S3</option>
                                                                        <option>Gentechnikgesetz - S4</option>";
                break;
            case "Gentechnikgesetz - S1":
                echo "<option >ÖAK - I - Ordination- und Behandlung</option>
                                                                        <option>ÖAK - II - klein Invasiv</option>
                                                                        <option>ÖAK - III - Eingriffsraum</option>
                                                                        <option>ÖAK - IV - OP</option>
                                                                        <option>MA 15 - LL 28 - OP</option>
                                                                        <option>MA 15 - LL 28 - Eingriffsraum</option>
                                                                        <option>MA 15 - LL 28 - Behandlungsraum invasiv</option>
                                                                        <option selected>Gentechnikgesetz - S1</option>
                                                                        <option>Gentechnikgesetz - S2</option>
                                                                        <option>Gentechnikgesetz - S3</option>
                                                                        <option>Gentechnikgesetz - S4</option>";
                break;
            case "Gentechnikgesetz - S2":
                echo "<option >ÖAK - I - Ordination- und Behandlung</option>
                                                                        <option>ÖAK - II - klein Invasiv</option>
                                                                        <option>ÖAK - III - Eingriffsraum</option>
                                                                        <option>ÖAK - IV - OP</option>
                                                                        <option>MA 15 - LL 28 - OP</option>
                                                                        <option>MA 15 - LL 28 - Eingriffsraum</option>
                                                                        <option>MA 15 - LL 28 - Behandlungsraum invasiv</option>
                                                                        <option>Gentechnikgesetz - S1</option>
                                                                        <option selected>Gentechnikgesetz - S2</option>
                                                                        <option>Gentechnikgesetz - S3</option>
                                                                        <option>Gentechnikgesetz - S4</option>";
                break;
            case "Gentechnikgesetz - S3":
                echo "<option >ÖAK - I - Ordination- und Behandlung</option>
                                                                        <option>ÖAK - II - klein Invasiv</option>
                                                                        <option>ÖAK - III - Eingriffsraum</option>
                                                                        <option>ÖAK - IV - OP</option>
                                                                        <option>MA 15 - LL 28 - OP</option>
                                                                        <option>MA 15 - LL 28 - Eingriffsraum</option>
                                                                        <option>MA 15 - LL 28 - Behandlungsraum invasiv</option>
                                                                        <option>Gentechnikgesetz - S1</option>
                                                                        <option>Gentechnikgesetz - S2</option>
                                                                        <option selected>Gentechnikgesetz - S3</option>
                                                                        <option>Gentechnikgesetz - S4</option>";
                break;
            case "Gentechnikgesetz - S4":
                echo "<option >ÖAK - I - Ordination- und Behandlung</option>
                                                                        <option>ÖAK - II - klein Invasiv</option>
                                                                        <option>ÖAK - III - Eingriffsraum</option>
                                                                        <option>ÖAK - IV - OP</option>
                                                                        <option>MA 15 - LL 28 - OP</option>
                                                                        <option>MA 15 - LL 28 - Eingriffsraum</option>
                                                                        <option>MA 15 - LL 28 - Behandlungsraum invasiv</option>
                                                                        <option>Gentechnikgesetz - S1</option>
                                                                        <option>Gentechnikgesetz - S2</option>
                                                                        <option>Gentechnikgesetz - S3</option>
                                                                        <option selected>Gentechnikgesetz - S4</option>";
                break;
            case "":
                echo "<option data-hidden='true'></option>
                                                                        <option>ÖAK - I - Ordination- und Behandlung</option>
                                                                        <option>ÖAK - II - klein Invasiv</option>
                                                                        <option>ÖAK - III - Eingriffsraum</option>
                                                                        <option>ÖAK - IV - OP</option>
                                                                        <option>MA 15 - LL 28 - OP</option>
                                                                        <option>MA 15 - LL 28 - Eingriffsraum</option>
                                                                        <option>MA 15 - LL 28 - Behandlungsraum invasiv</option>
                                                                        <option>Gentechnikgesetz - S1</option>
                                                                        <option>Gentechnikgesetz - S2</option>
                                                                        <option>Gentechnikgesetz - S3</option>
                                                                        <option>Gentechnikgesetz - S4</option>";
                break;
        }
        echo "</select>	
                                </div>
                                <label class='control-label col-md-1' for='Notdusche'>Notdusche Stk:</label>
                                <div class='col-md-1'>
                                       <input type='number' class=form-control form-control-xs' id='Notdusche' value='" . $row["HT_Notdusche"] . "'>
                                </div>
                        </div>
                        <div class='form-group row'>  
                            <div class='col-md-1'></div>                            
                            <label class='control-label col-md-1' for='waermeabgabeHT'>Wärme [W/m2]:</label>
                            <div class='col-md-1'>
                                   <input type='number' class=form-control form-control-xs' id='waermeabgabeHT' value='" . $row["HT_Waermeabgabe"] . "'>
                            </div>                           
                            <label class='control-label col-md-1' for='luftwechselrateHT'>LWR [1/h]:</label>
                            <div class='col-md-1'>
                                   <input type='number' class=form-control form-control-xs' id='luftwechselrateHT' value='" . $row["HT_Luftwechsel 1/h"] . "'>
                            </div>
                            <label class='control-label col-md-1' for='gereateAbluftHT'>Geräteabluft [m3/h]:</label>
                            <div class='col-md-1'>
                                   <input type='number' class=form-control form-control-xs' id='gereateAbluftHT' value='" . $row["HT_Geraeteabluft m3/h"] . "'>
                            </div>
                            <label class='control-label col-md-1' for='kuehlwasserLeistungHT'>Kühlwasser [W]:</label>
                            <div class='col-md-1'>
                                   <input type='number' class=form-control form-control-xs' id='kuehlwasserLeistungHT' value='" . $row["HT_Kühlwasserleistung_W"] . "'>
                            </div>
                        </div>
                        
                        <div class='form-group row'>
                                 <label class='control-label col-md-1' for='hkls'></label>
                                 <div class='col-md-11'>
                                        <textarea class='form-control form-control-sm' rows='5' id='hkls'>" . br2nl($row["Anmerkung HKLS"]) . "</textarea>
                                 </div>
                         </div>
                         <hr>
                         <div class='form-group row'>
                                <div class='col-md-1'>MedGas</div>
                                <label class='checkbox-inline col-md-1'>";
        if ($row["1 Kreis O2"] == 1) {
            echo "<input id='1kreiso2' checked data-toggle='toggle' data-on='Ja' data-off='Nein' data-onstyle='success' data-offstyle='danger' data-size='mini'  type='checkbox' ></input>1 Kreis O2";
        } else {
            echo "<input id='1kreiso2' data-toggle='toggle' data-on='Ja' data-off='Nein' data-onstyle='success' data-offstyle='danger'  data-size='mini' type='checkbox' ></input>1 Kreis O2";
        }
        echo "</label>
                        <label class='checkbox-inline col-md-1'>";
        if ($row["1 Kreis Va"] == 1) {
            echo "<input id='1kreisva' checked data-toggle='toggle' data-on='Ja' data-off='Nein' data-onstyle='success' data-offstyle='danger'  data-size='mini' type='checkbox' ></input>1 Kreis VA";
        } else {
            echo "<input id='1kreisva' data-toggle='toggle' data-on='Ja' data-off='Nein' data-onstyle='success' data-offstyle='danger'  data-size='mini' type='checkbox' ></input>1 Kreis VA";
        }
        echo "</label>	
                                <label class='checkbox-inline col-md-1'>";
        if ($row["1 Kreis DL-5"] == 1) {
            echo "<input id='1kreisdl5' checked data-toggle='toggle' data-on='Ja' data-off='Nein' data-onstyle='success' data-offstyle='danger'  data-size='mini' type='checkbox' ></input>1 Kreis DL-5";
        } else {
            echo "<input id='1kreisdl5' data-toggle='toggle' data-on='Ja' data-off='Nein' data-onstyle='success' data-offstyle='danger'  data-size='mini' type='checkbox' ></input>1 Kreis DL-5";
        }
        echo "</label>	
                                <label class='checkbox-inline col-md-1'>";
        if ($row["DL-10"] == 1) {
            echo "<input id='dl10' checked data-toggle='toggle' data-on='Ja' data-off='Nein' data-onstyle='success' data-offstyle='danger'  data-size='mini' type='checkbox' ></input>DL-10";
        } else {
            echo "<input id='dl10' data-toggle='toggle' data-on='Ja' data-off='Nein' data-onstyle='success' data-offstyle='danger'  data-size='mini' type='checkbox' ></input>DL-10";
        }
        echo "</label>						  		
                          </div>

                         <div class='form-group row'>
                                <div class='col-md-1'></div>
                                <label class='checkbox-inline col-md-1'>";
        if ($row["2 Kreis O2"] == 1) {
            echo "<input id='2kreiso2' checked data-toggle='toggle' data-on='Ja' data-off='Nein' data-onstyle='success' data-offstyle='danger'  data-size='mini' type='checkbox' ></input>2 Kreis O2";
        } else {
            echo "<input id='2kreiso2' data-toggle='toggle' data-on='Ja' data-off='Nein' data-onstyle='success' data-offstyle='danger'  data-size='mini' type='checkbox' ></input>2 Kreis O2";
        }
        echo "</label>
                        <label class='checkbox-inline col-md-1'>";
        if ($row["2 Kreis Va"] == 1) {
            echo "<input id='2kreisva' checked data-toggle='toggle' data-on='Ja' data-off='Nein' data-onstyle='success' data-offstyle='danger'  data-size='mini' type='checkbox' ></input>2 Kreis VA";
        } else {
            echo "<input id='2kreisva' data-toggle='toggle' data-on='Ja' data-off='Nein' data-onstyle='success' data-offstyle='danger'  data-size='mini' type='checkbox' ></input>2 Kreis VA";
        }
        echo "</label>	
                                <label class='checkbox-inline col-md-1'>";
        if ($row["2 Kreis DL-5"] == 1) {
            echo "<input id='2kreisdl5' checked data-toggle='toggle' data-on='Ja' data-off='Nein' data-onstyle='success' data-offstyle='danger'  data-size='mini' type='checkbox' ></input>2 Kreis DL-5";
        } else {
            echo "<input id='2kreisdl5' data-toggle='toggle' data-on='Ja' data-off='Nein' data-onstyle='success' data-offstyle='danger'  data-size='mini' type='checkbox' ></input>2 Kreis DL-5";
        }
        echo "</label>	
                                <label class='checkbox-inline col-md-1'>";
        if ($row["DL-tech"] == 1) {
            echo "<input id='dltech' checked data-toggle='toggle' data-on='Ja' data-off='Nein' data-onstyle='success' data-offstyle='danger'  data-size='mini' type='checkbox' ></input>DL-Tech";
        } else {
            echo "<input id='dltech' data-toggle='toggle' data-on='Ja' data-off='Nein' data-onstyle='success' data-offstyle='danger'  data-size='mini' type='checkbox' ></input>DL-Tech";
        }
        echo "</label>

                          </div>
                          <div class='form-group row'>
                                <div class='col-md-1'></div>
                                <label class='checkbox-inline col-md-1'>";
        if ($row["NGA"] == 1) {
            echo "<input id='nga' checked data-toggle='toggle' data-on='Ja' data-off='Nein' data-onstyle='success' data-offstyle='danger'  data-size='mini' type='checkbox' ></input>NGA";
        } else {
            echo "<input id='nga' data-toggle='toggle' data-on='Ja' data-off='Nein' data-onstyle='success' data-offstyle='danger'  data-size='mini' type='checkbox' ></input>NGA";
        }
        echo "</label>
                                        <label class='checkbox-inline col-md-1'>";
        if ($row["N2O"] == 1) {
            echo "<input id='n2o' checked data-toggle='toggle' data-on='Ja' data-off='Nein' data-onstyle='success' data-offstyle='danger'  data-size='mini' type='checkbox' ></input>N2O";
        } else {
            echo "<input id='n2o' data-toggle='toggle' data-on='Ja' data-off='Nein' data-onstyle='success' data-offstyle='danger'  data-size='mini' type='checkbox' ></input>N2O";
        }
        echo "</label>
                                        <label class='checkbox-inline col-md-1'>";
        if ($row["CO2"] == 1) {
            echo "<input id='co2' checked data-toggle='toggle' data-on='Ja' data-off='Nein' data-onstyle='success' data-offstyle='danger'  data-size='mini' type='checkbox' ></input>CO2";
        } else {
            echo "<input id='co2' data-toggle='toggle' data-on='Ja' data-off='Nein' data-onstyle='success' data-offstyle='danger'  data-size='mini' type='checkbox' ></input>CO2";
        }
        echo "</label>
                                            <label class='checkbox-inline col-md-1'>";
        if ($row["H2"] == 1) {
            echo "<input id='H2' checked data-toggle='toggle' data-on='Ja' data-off='Nein' data-onstyle='success' data-offstyle='danger'  data-size='mini' type='checkbox' ></input>H2";
        } else {
            echo "<input id='H2' data-toggle='toggle' data-on='Ja' data-off='Nein' data-onstyle='success' data-offstyle='danger'  data-size='mini' type='checkbox' ></input>H2";
        }
        echo "</label>	
                                            <label class='checkbox-inline col-md-1'>";
        if ($row["He"] == 1) {
            echo "<input id='He' checked data-toggle='toggle' data-on='Ja' data-off='Nein' data-onstyle='success' data-offstyle='danger'  data-size='mini' type='checkbox' ></input>He";
        } else {
            echo "<input id='He' data-toggle='toggle' data-on='Ja' data-off='Nein' data-onstyle='success' data-offstyle='danger'  data-size='mini' type='checkbox' ></input>He";
        }
        echo "</label>	
                                            <label class='checkbox-inline col-md-1'>";
        if ($row["He-RF"] == 1) {
            echo "<input id='HeRF' checked data-toggle='toggle' data-on='Ja' data-off='Nein' data-onstyle='success' data-offstyle='danger'  data-size='mini' type='checkbox' ></input>He-RF";
        } else {
            echo "<input id='HeRF' data-toggle='toggle' data-on='Ja' data-off='Nein' data-onstyle='success' data-offstyle='danger'  data-size='mini' type='checkbox' ></input>He-RF";
        }
        echo "</label>	
                                            <label class='checkbox-inline col-md-1'>";
        if ($row["Ar"] == 1) {
            echo "<input id='Ar' checked data-toggle='toggle' data-on='Ja' data-off='Nein' data-onstyle='success' data-offstyle='danger'  data-size='mini' type='checkbox' ></input>Ar";
        } else {
            echo "<input id='Ar' data-toggle='toggle' data-on='Ja' data-off='Nein' data-onstyle='success' data-offstyle='danger'  data-size='mini' type='checkbox' ></input>Ar";
        }
        echo "</label>	
                                            <label class='checkbox-inline col-md-1'>";
        if ($row["N2"] == 1) {
            echo "<input id='N2' checked data-toggle='toggle' data-on='Ja' data-off='Nein' data-onstyle='success' data-offstyle='danger'  data-size='mini' type='checkbox' ></input>N2";
        } else {
            echo "<input id='N2' data-toggle='toggle' data-on='Ja' data-off='Nein' data-onstyle='success' data-offstyle='danger'  data-size='mini' type='checkbox' ></input>N2";
        }
        echo "</label>	
                          </div>
                        <div class='form-group row'>
                                 <label class='control-label col-md-1' for='medgas'></label>
                                 <div class='col-md-11'>
                                        <textarea class='form-control form-control-sm' rows='5' id='medgas'>" . br2nl($row["Anmerkung MedGas"]) . "</textarea>
                                 </div>
                         </div>
                        <div class='form-group row'>
                                <div class='col-md-1'></div>
                                <input type='button' id='saveBauangaben' class='btn btn-warning btn-sm' value='Bauangaben speichern'></input>




                                <!-- Button für Modal -->
                                <input type='button' class='btn btn-info btn-sm' value='Bauangaben kopieren exkl BO' id='" . $_SESSION["roomID"] . "' data-toggle='modal' data-target='#myModal'></input>


                        </div>
                </form>";

        $mysqli->close();

        echo "<!-- Modal zum kopieren der Bauangaben -->
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
                var abdunkelbarkeit = ($("#abdunkelbarkeit").prop('checked') === true) ? '1' : '0';
                var strahlenanwendung = ($("#strahlenanwendung").val() === 'Ja') ? '1' : ($("#strahlenanwendung").val() === 'Nein') ? '0' : '2';
                var laseranwendung = ($("#laseranwendung").prop('checked') === true) ? '1' : '0';
                var awg = $("#awg").val();
                var fussbodenklasse = $("#fussbodenklasse").val();
                var it = ($("#it").prop('checked') === true) ? '1' : '0';
                var av = ($("#av").prop('checked') === true) ? '1' : '0';
                var sv = ($("#sv").prop('checked') === true) ? '1' : '0';
                var zsv = ($("#zsv").prop('checked') === true) ? '1' : '0';
                var usv = ($("#usv").prop('checked') === true) ? '1' : '0';
                var h6020 = $("#h6020").val();
                var iso = $("#iso").val();
                var gmp = $("#gmp").val();
                var schwingungsklasse = $("#schwingungsklasse").val();
                var bestandNr = $("#bestandsraumNr").val();
                var bestandGeb = $("#bestandsGeb").val();
                var hygieneklasse = $("#hygieneklasse").val();
                var kreiso2_1 = ($("#1kreiso2").prop('checked') === true) ? '1' : '0';
                var kreiso2_2 = ($("#2kreiso2").prop('checked') === true) ? '1' : '0';
                var kreisva_1 = ($("#1kreisva").prop('checked') === true) ? '1' : '0';
                var kreisva_2 = ($("#2kreisva").prop('checked') === true) ? '1' : '0';
                var kreisdl5_1 = ($("#1kreisdl5").prop('checked') === true) ? '1' : '0';
                var kreisdl5_2 = ($("#2kreisdl5").prop('checked') === true) ? '1' : '0';
                var dl10 = ($("#dl10").prop('checked') === true) ? '1' : '0';
                var dltech = ($("#dltech").prop('checked') === true) ? '1' : '0';
                var co2 = ($("#co2").prop('checked') === true) ? '1' : '0';
                var nga = ($("#nga").prop('checked') === true) ? '1' : '0';
                var n2o = ($("#n2o").prop('checked') === true) ? '1' : '0';
                var H2 = ($("#H2").prop('checked') === true) ? '1' : '0';
                var He = ($("#He").prop('checked') === true) ? '1' : '0';
                var HeRF = ($("#HeRF").prop('checked') === true) ? '1' : '0';
                var Ar = ($("#Ar").prop('checked') === true) ? '1' : '0';
                var N2 = ($("#N2").prop('checked') === true) ? '1' : '0';
                var notdusche = $("#Notdusche").val();
                var lwr = $("#luftwechselrateHT").val();
                var waermeHT = $("#waermeabgabeHT").val();
                var leistungET = $("#leistungsbedarfET").val();
                var emv = ($("#emv").prop('checked') === true) ? '1' : '0';
                var raumTypBH = $("#raumTypBH").val();
                var gereateAbluftHT = $("#gereateAbluftHT").val();
                var kuehlwasserLeistungHT = $("#kuehlwasserLeistungHT").val();


                $.ajax({
                    url: "saveRoomSpecifications.php",
                    data: {"gereateAbluftHT": gereateAbluftHT, "kuehlwasserLeistungHT": kuehlwasserLeistungHT, "H2": H2, "He": He, "HeRF": HeRF, "Ar": Ar, "N2": N2, "raumTypBH": raumTypBH, "emv": emv, "leistungET": leistungET, "waermeHT": waermeHT, "lwr": lwr, "notdusche": notdusche, "bestandGeb": bestandGeb, "bestandNr": bestandNr, "schwingungsklasse": schwingungsklasse, "fussbodenklasse": fussbodenklasse, "it": it, "funktionBO": funktionBO, "Elektro": Elektro, "geraete": geraete, "medgas": medgas, "baustatik": baustatik, "hkls": hkls, "abdunkelbarkeit": abdunkelbarkeit, "strahlenanwendung": strahlenanwendung, "laseranwendung": laseranwendung, "awg": awg, "av": av, "sv": sv, "zsv": zsv, "usv": usv, "h6020": h6020, "iso": iso, "gmp": gmp, "hygieneklasse": hygieneklasse, "kreiso2_1": kreiso2_1, "kreiso2_2": kreiso2_2, "kreisva_1": kreisva_1, "kreisva_2": kreisva_2, "kreisdl5_1": kreisdl5_1, "kreisdl5_2": kreisdl5_2, "dl10": dl10, "dltech": dltech, "co2": co2, "nga": nga, "n2o": n2o},
                    type: "GET",
                    success: function (data) {
                        alert(data);
                    }
                });

            });

            //Bauangaben kopieren
            $("input[value='Bauangaben kopieren exkl BO']").click(function () {
                var ID = this.id;
                console.log("Bauangaben ID", ID);
                $.ajax({
                    url: "getRoomsToCopy.php",
                    type: "GET",
                    data: {"id": ID},
                    success: function (data) {
                        console.log("Sucessfully opened getRoomsToCopy.php", data);
                        $("#mbody").html(data);
                    }
                });
            });








        </script> 

    </body>
</html>