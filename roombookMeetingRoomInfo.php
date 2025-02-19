<?php
session_start();
include '_utils.php';
init_page_serversides();
?>

<!DOCTYPE html>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<html>
<head> </head>
 

<?php
$mysqli= utils_connect_sql();
					
        
        $sql = "SELECT tabelle_räume.Nutzfläche, tabelle_räume.Raumhoehe, tabelle_räume.`Anmerkung FunktionBO`, tabelle_räume.`Anmerkung Geräte`, tabelle_räume.`Anmerkung BauStatik`, tabelle_räume.`Anmerkung Elektro`, 
	tabelle_räume.`Anmerkung MedGas`, tabelle_räume.`Anmerkung HKLS`, tabelle_räume.Abdunkelbarkeit, tabelle_räume.Strahlenanwendung, tabelle_räume.Laseranwendung,
	tabelle_räume.Anwendungsgruppe, tabelle_räume.AV, tabelle_räume.SV, tabelle_räume.ZSV, tabelle_räume.USV, tabelle_räume.H6020, tabelle_räume.ISO, tabelle_räume.GMP, 
	tabelle_räume.`1 Kreis O2`, tabelle_räume.`2 Kreis O2`, tabelle_räume.`1 Kreis Va`, tabelle_räume.`2 Kreis Va`, tabelle_räume.`1 Kreis DL-5`, tabelle_räume.`2 Kreis DL-5`, tabelle_räume.`DL-10`, 
	tabelle_räume.`DL-tech`, tabelle_räume.CO2, tabelle_räume.NGA, tabelle_räume.N2O, tabelle_räume.`Allgemeine Hygieneklasse` FROM tabelle_räume WHERE (((tabelle_räume.idTABELLE_Räume)=".$_SESSION["roomID"]."));";
        
	$result = $mysqli->query($sql);
	while($row = $result->fetch_assoc()) {
            echo "
                <div class='row mt-4'>
                    <div class='col-sm-4'>
                        <div class='card card-default m-2'>
                            <div class='card-header'>
                                <h4 class='m-b-2 text-dark'><i class='fas fa-arrows-alt'></i> Architektur</h4>
                            </div>            
                            <div class='card-body'>
                                <h4 class='m-t-2 text-dark'><i class='fas fa-arrows-alt-v'></i> Höhe: ".$row["Raumhoehe"]." m</h4>
                                <h4 class='m-t-2 text-dark'><i class='far fa-square'></i> Fläche: ".$row["Nutzfläche"]." m2</h4>";
                                if($row["Abdunkelbarkeit"] == "0"){
                                    echo "<h4 class='m-t-2 text-dark'><i class='fas fa-moon'></i> Abdunkelbarkeit: <i class='fas fa-times'></i></h4>";
                                }
                                else{
                                    echo "<h4 class='m-t-2 text-dark'><i class='fas fa-moon'></i> Abdunkelbarkeit: <i class='fas fa-check'></i></h4>";
                                }
                                echo "
                            </div>
                        </div>
                    </div>
                    <div class='col-sm-4'>                    
                        <div class='card card-default m-2'>
                            <div class='card-header'>
                                <h4 class='m-b-2 text-danger'><i class='fas fa-bolt'></i> Elektro</h4>
                            </div>            
                            <div class='card-body'>
                                <h4 class='m-t-2 text-dark'>AWG: ".$row["Anwendungsgruppe"]."</h4>";
                                if($row["AV"] == "0"){
                                    echo "<h4 class='m-t-2 text-dark'>AV <i class='fas fa-times'></i></h4>";
                                }
                                else{
                                    echo "<h4 class='m-t-2 text-dark'>AV <i class='fas fa-check'></i></h4>";
                                }
                                if($row["SV"] == "0"){
                                    echo "<h4 class='m-t-2 text-dark'>SV <i class='fas fa-times'></i></h4>";
                                }
                                else{
                                    echo "<h4 class='m-t-2 text-dark'>SV <i class='fas fa-check'></i></h4>";
                                }
                                if($row["ZSV"] == "0"){
                                    echo "<h4 class='m-t-2 text-dark'>ZSV <i class='fas fa-times'></i></h4>";
                                }
                                else{
                                    echo "<h4 class='m-t-2 text-dark'>ZSV <i class='fas fa-check'></i></h4>";
                                }
                                if($row["USV"] == "0"){
                                    echo "<h4 class='m-t-2 text-dark'>USV <i class='fas fa-times'></i></h4>";
                                }
                                else{
                                    echo "<h4 class='m-t-2 text-darkUSVAV <i class='fas fa-check'></i></h4>";
                                }
                            echo "
                            </div>
                        </div>
                    </div>
                    <div class='col-sm-4'>                    
                        <div class='card card-default m-2'>
                            <div class='card-header'>
                                <h4 class='m-b-2 text-dark'>Gase</h4>
                            </div>            
                            <div class='card-body'>";
                                if($row["1 Kreis O2"] == "1"){
                                    echo "<h4 class='m-t-2 text-dark bg-white'><i class='far fa-circle'></i> 1 Kreis O2 : <i class='fas fa-check'></i></h4>";
                                }
                                else{
                                    echo "<h4 class='m-t-2 text-dark bg-white'><i class='far fa-circle'></i> 1 Kreis O2 : <i class='fas fa-times'></i></h4>";
                                }
                                if($row["2 Kreis O2"] == "1"){
                                    echo "<h4 class='m-t-2 text-dark bg-white'><i class='far fa-circle'></i> 2 Kreis O2 : <i class='fas fa-check'></i></h4>";
                                }
                                else{
                                    echo "<h4 class='m-t-2 text-dark bg-white'><i class='far fa-circle'></i> 2 Kreis O2 : <i class='fas fa-times'></i></h4>";
                                }
                                if($row["1 Kreis Va"] == "1"){
                                    echo "<h4 class='m-t-2 text-dark '><i class='far fa-circle bg-warning'></i> 1 Kreis VA : <i class='fas fa-check'></i></h4>";
                                }
                                else{
                                    echo "<h4 class='m-t-2 text-dark'><i class='far fa-circle bg-warning'></i> 1 Kreis VA : <i class='fas fa-times'></i></h4>";
                                }
                                if($row["2 Kreis Va"] == "1"){
                                    echo "<h4 class='m-t-2 text-dark  '><i class='far fa-circle bg-warning'></i> 2 Kreis VA : <i class='fas fa-check'></i></h4>";
                                }
                                else{
                                    echo "<h4 class='m-t-2 text-dark '><i class='far fa-circle bg-warning '></i> 2 Kreis VA : <i class='fas fa-times'></i></h4>";
                                }
                                if($row["1 Kreis DL-5"] == "1"){
                                    echo "<h4 class='m-t-2  text-dark'><i class='far fa-circle bg-dark text-white'></i> 1 Kreis DL-5 : <i class='fas fa-check'></i></h4>";
                                }
                                else{
                                    echo "<h4 class='m-t-2  text-dark'><i class='far fa-circle bg-dark text-white'></i> 1 Kreis DL-5 : <i class='fas fa-times'></i></h4>";
                                }
                                if($row["2 Kreis DL-5"] == "1"){
                                    echo "<h4 class='m-t-2 text-dark'><i class='far fa-circle bg-dark text-white'></i> 2 Kreis DL-5 : <i class='fas fa-check'></i></h4>";
                                }
                                else{
                                    echo "<h4 class='m-t-2 text-dark'><i class='far fa-circle bg-dark text-white'></i> 2 Kreis DL-5 : <i class='fas fa-times'></i></h4>";
                                }
                                if($row["DL-10"] == "1"){
                                    echo "<h4 class='m-t-2 text-dark'><i class='far fa-circle bg-dark text-white'></i> DL-10 : <i class='fas fa-check'></i></h4>";
                                }
                                else{
                                    echo "<h4 class='m-t-2 text-dark'><i class='far fa-circle bg-dark text-white'></i> DL-10 : <i class='fas fa-times'></i></h4>";
                                }
                                if($row["DL-tech"] == "1"){
                                    echo "<h4 class='m-t-2 text-dark'><i class='far fa-circle bg-dark text-white'></i> DL-tech : <i class='fas fa-check'></i></h4>";
                                }
                                else{
                                    echo "<h4 class='m-t-2  text-dark'><i class='far fa-circle bg-dark text-white'></i> DL-tech : <i class='fas fa-times'></i></h4>";
                                }
                                if($row["CO2"] == "1"){
                                    echo "<h4 class='m-t-2 text-dark'><i class='far fa-circle bg-secondary text-white'></i> CO2 : <i class='fas fa-check'></i></h4>";
                                }
                                else{
                                    echo "<h4 class='m-t-2 text-dark'><i class='far fa-circle bg-secondary text-white'></i> CO2 : <i class='fas fa-times'></i></h4>";
                                }
                                if($row["NGA"] == "1"){
                                    echo "<h4 class='m-t-2 text-dark'><i class='far fa-circle bg-danger text-white'></i> NGA : <i class='fas fa-check'></i></h4>";
                                }
                                else{
                                    echo "<h4 class='m-t-2 text-dark'><i class='far fa-circle bg-danger text-white'></i> NGA : <i class='fas fa-times'></i></h4>";
                                }
                                if($row["N2O"] == "1"){
                                    echo "<h4 class='m-t-2  text-dark'><i class='far fa-circle bg-primary text-white'></i> N2O : <i class='fas fa-check'></i></h4>";
                                }
                                else{
                                    echo "<h4 class='m-t-2  text-dark'><i class='far fa-circle bg-primary text-white'></i> N2O : <i class='fas fa-times'></i></h4>";
                                }
                            echo "
                            </div>
                        </div>
                    </div>
                </div>
                <div class='row mt-4'>
                    <div class='col-sm-4'>                    
                        <div class='card card-default m-2'>
                            <div class='card-header'>
                                <h4 class='m-b-2 text-dark'><i class='far fa-hospital'></i> Raumklasse/Lüftung</h4>
                            </div>            
                            <div class='card-body'>
                                <h4 class='m-t-2 text-dark'>ISO: ".$row["ISO"]."</h4>
                                <h4 class='m-t-2 text-dark'>GMP: ".$row["GMP"]."</h4>
                                <h4 class='m-t-2 text-dark'>H6020: ".$row["H6020"]."</h4>
                                <h4 class='m-t-2 text-dark'>Allgemeine Hygieneklasse: ".$row["Allgemeine Hygieneklasse"]."</h4>
                            </div>
                        </div>
                    </div>
                    <div class='col-sm-4'>                                        
                        <div class='card card-default m-2'>
                            <div class='card-header'>
                                <h4 class='m-b-2 text-dark'><i class='fas fa-exclamation-triangle'></i> Laser/Strahlen</h4>
                            </div>            
                            <div class='card-body'>";
                                if($row["Strahlenanwendung"] == "0"){
                                    echo "<h4 class='m-t-2 text-dark'>Strahlenanwendung: <i class='fas fa-times'></i></h4>";
                                }
                                else{
                                    if($row["Strahlenanwendung"] == "1"){
                                        echo "<h4 class='m-t-2 text-dark'>Strahlenanwendung: <i class='fas fa-check'></i></h4>";
                                    }
                                    else{
                                        echo "<h4 class='m-t-2 text-dark'>Strahlenanwendung: Quasi stationär</h4>";
                                    }
                                    
                                }
                                if($row["Laseranwendung"] == "0"){
                                    echo "<h4 class='m-t-2 text-dark'>Laseranwendung: <i class='fas fa-times'></i></h4>";
                                }
                                else{
                                    echo "<h4 class='m-t-2 text-dark'>Laseranwendung: <i class='fas fa-check'></i></h4>";
                                }
                                echo "
                            </div>
                        </div>
                    </div>
                </div>
                ";
        }
        /*

	while($row = $result->fetch_assoc()) {
	    echo "<tr>";
	    echo "<td>".$row["idtabelle_Vermerke"]."</td>";
            echo "<td></td>";
	    echo "<td>".$row["Gruppenart"]."</td>";
            echo "<td>".$row["Gruppenname"]."</td>";

                    
            echo "<td>";
                if($row["Vermerkart"]!="Info"){                                   
                    if($row["Bearbeitungsstatus"]=="0"){
                         echo "<div class='form-check form-check-inline'><label class='form-check-label' for='".$row["idtabelle_Vermerke"]."'><input type='checkbox' class='form-check-input' id='".$row["idtabelle_Vermerke"]."' value='statusCheck'></label></div>";
                    }
                    else{
                        echo "<div class='form-check form-check-inline'><label class='form-check-label' for='".$row["idtabelle_Vermerke"]."'><input type='checkbox' class='form-check-input' id='".$row["idtabelle_Vermerke"]."' value='statusCheck' checked='true'></label></div>";
                    }
                }
            echo "</td>";
            echo "<td>".$row["Datum"]."</td>";
            echo "<td><button type='button' class='btn btn-sm btn-light' data-toggle='popover' title='Vermerk' data-placement='bottom' data-content='".$row["Vermerktext"]."'><i class='fa fa-comment'></i></button></td>";
            echo "<td>".$row["Vermerkart"]."</td>";
            echo "<td>".$row["Name"]."</td>";
            echo "<td>";
                if($row["Vermerkart"]!="Info"){
                    echo $row["Faelligkeit"];
                }
            echo "</td>";           
            echo "<td>".$row["LosNr_Extern"]."</td>";                       
            echo "<td>".$row["Bearbeitungsstatus"]."</td>";
            
	    echo "</tr>";
	}
	echo "</tbody></table></div>";
         * */
         
	$mysqli ->close();
?>
<script>
</script> 

</body>
</html>