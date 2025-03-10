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
if(!isset($_SESSION["username"]))
   {
   echo "Bitte erst <a href=\"index.php\">einloggen</a>";
   exit;
   }
   
   
   function br2nl($string){
$return= str_replace(array("<br/>"), "\n", $string);
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
	
	$sql="SELECT tabelle_räume.`Anmerkung FunktionBO`, tabelle_räume.`Anmerkung Geräte`, tabelle_räume.`Anmerkung BauStatik`, tabelle_räume.`Anmerkung Elektro`, ";
	$sql.="tabelle_räume.`Anmerkung MedGas`, tabelle_räume.`Anmerkung HKLS`, tabelle_räume.Abdunkelbarkeit, tabelle_räume.Strahlenanwendung, tabelle_räume.Laseranwendung, ";
	$sql.="tabelle_räume.Anwendungsgruppe, tabelle_räume.AV, tabelle_räume.SV, tabelle_räume.ZSV, tabelle_räume.USV, tabelle_räume.H6020, tabelle_räume.ISO, tabelle_räume.GMP, ";
	$sql.="tabelle_räume.`1 Kreis O2`, tabelle_räume.`2 Kreis O2`, tabelle_räume.`1 Kreis Va`, tabelle_räume.`2 Kreis Va`, tabelle_räume.`1 Kreis DL-5`, tabelle_räume.`2 Kreis DL-5`, tabelle_räume.`DL-10`, ";
	$sql.="tabelle_räume.`DL-tech`, tabelle_räume.CO2, tabelle_räume.NGA, tabelle_räume.N2O FROM tabelle_räume WHERE (((tabelle_räume.idTABELLE_Räume)=".$_SESSION["roomID"]."));";
	
				
	$result = $mysqli->query($sql);
	
	$row = $result->fetch_assoc();
	
	
	echo "<form class='form-horizontal'>
	  			<div class='form-group form-group-sm'>
				  	<label class='control-label col-xxl-1' for='funktionBO'>FunktionBO</label>
					  <div class='col-xxl-2'>
					  	<textarea class='form-control col-xxl-2' rows='3' id='funktionBO'>".br2nl($row["Anmerkung FunktionBO"])."</textarea>
					  </div> 
					  <label class='control-label col-xxl-1' for='Elektro'>Elektro</label>
					  <div class='col-xxl-2'>
					  	<textarea class='form-control' rows='3' id='Elektro'>".br2nl($row["Anmerkung Elektro"])."</textarea>
					  </div> 
					  <div class='form-group'>
							  <label class='control-label col-xxl-1' for='awg'>AWG</label>
		  			 			<div class='col-xxl-1'>
			  			 			<select class='form-control input-sm' id='awg'>";
								    if($row["Anwendungsgruppe"]==0){
							   	    		echo "<option selected>0</option>
								        		  <option>1</option>
								        		  <option>2</option>";
							   	    	}
							   	    	else{
							   	    		if($row["Anwendungsgruppe"]==1){
								   	    		echo "<option>0</option>
									        		  <option selected>1</option>
									        		  <option>2</option>";
									       	}
									       	else{
									       		echo "<option>0</option>
									        		  <option>1</option>
									        		  <option selected>2</option>";
									        }
							   	    	}					 
								    echo "</select>						
								</div>
								<label class='control-label col-xxl-1' for='av'>AV</label>
		  			 			<div class='col-xxl-1'>
			  			 			<select class='form-control input-sm' id='av'>";
								    if($row["AV"]==1){
							   	    		echo "<option selected>Ja</option>
								        		  <option>Nein</option>";
							   	    	}
							   	    	else{
							   	    		echo "<option>Ja</option>
								        		  <option selected>Nein</option>";
		
							   	    	}					 
								    echo "</select>	
								</div>
								<label class='control-label col-xxl-1' for='sv'>SV</label>
		  			 			<div class='col-xxl-1'>
			  			 			<select class='form-control input-sm' id='sv'>";
								    if($row["SV"]==1){
							   	    		echo "<option selected>Ja</option>
								        		  <option>Nein</option>";
							   	    	}
							   	    	else{
							   	    		echo "<option>Ja</option>
								        		  <option selected>Nein</option>";
		
							   	    	}					 
								    echo "</select>	
								</div>
						
								<label class='control-label col-xxl-1' for='zsv'>ZSV</label>
		  			 			<div class='col-xxl-1'>
			  			 			<select class='form-control input-sm' id='zsv'>";
								    if($row["ZSV"]==1){
							   	    		echo "<option selected>Ja</option>
								        		  <option>Nein</option>";
							   	    	}
							   	    	else{
							   	    		echo "<option>Ja</option>
								        		  <option selected>Nein</option>";
		
							   	    	}					 
								    echo "</select>	
			
								</div>		
								<label class='control-label col-xxl-1' for='usv'>USV</label>
		  			 			<div class='col-xxl-1'>
			  			 			<select class='form-control input-sm' id='usv'>";
								    if($row["USV"]==1){
							   	    		echo "<option selected>Ja</option>
								        		  <option>Nein</option>";
							   	    	}
							   	    	else{
							   	    		echo "<option>Ja</option>
								        		  <option selected>Nein</option>";
		
							   	    	}					 
								    echo "</select>	
								</div>				  			 											 
		  			 	</div>
				 </div>
				 <div class='form-group'>
					  <label class='control-label col-xxl-1' for='geraete'>Geräte</label>
					  <div class='col-xxl-2'>
					  	<textarea class='form-control' rows='3' id='geraete'>".br2nl($row["Anmerkung Geräte"])."</textarea>
					  </div> 
					  <label class='control-label col-xxl-1' for='medgas'>Medgas</label>
					  <div class='col-xxl-2'>
					  	<textarea class='form-control' rows='3' id='medgas'>".br2nl($row["Anmerkung MedGas"])."</textarea>
					  </div> 
						  <div class='form-group'>
	  			 			<label class='control-label col-xxl-1' for='h6020'>Raumklasse H6020</label>
	  			 			<div class='col-xxl-1'>
		  			 			<select class='form-control input-sm' id='h6020'>";
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
	  			 			<label class='control-label col-xxl-1' for='iso'>Raumklasse ISO</label>
	  			 			<div class='col-xxl-1'>
		  			 			<select class='form-control input-sm' id='iso'>";
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
							<label class='control-label col-xxl-1' for='gmp'>Raumklasse GMP</label>
	  			 			<div class='col-xxl-1'>
		  			 			<select class='form-control input-sm' id='gmp'>";
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
	  			 	</div>
				 </div>
				 <div class='form-group'>
					  <label class='control-label col-xxl-1' for='baustatik'>Bau/Statik</label>
					  <div class='col-xxl-2'>
					  	<textarea class='form-control' rows='3' id='baustatik'>".br2nl($row["Anmerkung BauStatik"])."</textarea>
					  </div> 
					  <label class='control-label col-xxl-1' for='hkls'>HKLS</label>
					  <div class='col-xxl-2'>
					  	<textarea class='form-control' rows='3' id='hkls'>".br2nl($row["Anmerkung HKLS"])."</textarea>
					  </div> 
					  <div class='form-group'>
						  <label class='control-label col-xxl-1' for='abdunkelbarkeit'>Abdunkelbarkeit</label>
	  			 			<div class='col-xxl-1'>
		  			 			<select class='form-control input-sm' id='abdunkelbarkeit'>";
		  			 				if($row["Abdunkelbarkeit"]==1){
						   	    		echo "<option selected>Ja</option>
							        		  <option>Nein</option>";
						   	    	}
						   	    	else{
						   	    		echo "<option>Ja</option>
							        		  <option selected>Nein</option>";
	
						   	    	}					 
							    echo "</select>	
							</div>
						
							<label class='control-label col-xxl-1' for='strahlenanwendung'>Strahlenanwendung</label>
	  			 			<div class='col-xxl-1'>
		  			 			<select class='form-control input-sm' id='strahlenanwendung'>";
							    if($row["Strahlenanwendung"]==1){
						   	    		echo "<option selected>Ja</option>
							        		  <option>Nein</option>
							        		  <option>Quasi Sationär</option>";
						   	    	}
						   	    	else{
						   	    		if($row["Strahlenanwendung"]==0){

							   	    		echo "<option>Ja</option>
								        		  <option selected>Nein</option>
								        		  <option>Quasi Sationär</option>";
								        }
								        else{
								        	echo "<option>Ja</option>
								        		  <option>Nein</option>
								        		  <option selected>Quasi Sationär</option>";
										}
	
						   	    	}					 
							    echo "</select>	
							</div>
							<label class='control-label col-xxl-1' for='laseranwendung'>Laseranwendung</label>
	  			 			<div class='col-xxl-1'>
		  			 			<select class='form-control input-sm' id='laseranwendung'>";
							     if($row["Laseranwendung"]==1){
						   	    		echo "<option selected>Ja</option>
							        		  <option>Nein</option>";
						   	    	}
						   	    	else{
						   	    		echo "<option>Ja</option>
							        		  <option selected>Nein</option>";
	
						   	    	}					 
							    echo "</select>	
							</div>	
						</div>	
				 </div>
  			 		
  			 	<div class='form-group'>
  			 			<label class='control-label col-xxl-1' for='1kreiso2'>1 Kreis O2</label>
  			 			<div class='col-xxl-1'>
	  			 			<select class='form-control input-sm' id='1kreiso2'>";
						    if($row["1 Kreis O2"]==1){
					   	    		echo "<option selected>Ja</option>
						        		  <option>Nein</option>";
					   	    	}
					   	    	else{
					   	    		echo "<option>Ja</option>
						        		  <option selected>Nein</option>";

					   	    	}					 
						    echo "</select>	
						</div>	
  			 			<label class='control-label col-xxl-1' for='2kreiso2'>2 Kreis O2</label>
  			 			<div class='col-xxl-1'>
	  			 			<select class='form-control input-sm' id='2kreiso2'>";
						    if($row["2 Kreis O2"]==1){
					   	    		echo "<option selected>Ja</option>
						        		  <option>Nein</option>";
					   	    	}
					   	    	else{
					   	    		echo "<option>Ja</option>
						        		  <option selected>Nein</option>";

					   	    	}					 
						    echo "</select>	
						</div>	
						<label class='control-label col-xxl-1' for='1kreisva'>1 Kreis VA</label>
  			 			<div class='col-xxl-1'>
	  			 			<select class='form-control input-sm' id='1kreisva'>";
						    if($row["1 Kreis Va"]==1){
					   	    		echo "<option selected>Ja</option>
						        		  <option>Nein</option>";
					   	    	}
					   	    	else{
					   	    		echo "<option>Ja</option>
						        		  <option selected>Nein</option>";

					   	    	}					 
						    echo "</select>	
						</div>	
  			 			<label class='control-label col-xxl-1' for='2kreisva'>2 Kreis VA</label>
  			 			<div class='col-xxl-1'>
	  			 			<select class='form-control input-sm' id='2kreisva'>";
						    if($row["2 Kreis Va"]==1){
					   	    		echo "<option selected>Ja</option>
						        		  <option>Nein</option>";
					   	    	}
					   	    	else{
					   	    		echo "<option>Ja</option>
						        		  <option selected>Nein</option>";

					   	    	}					 
						    echo "</select>	

	
						</div>	
						<label class='control-label col-xxl-1' for='1kreisdl5'>1 Kreis DL-5</label>
  			 			<div class='col-xxl-1'>
	  			 			<select class='form-control input-sm' id='1kreisdl5'>";
						    if($row["1 Kreis DL-5"]==1){
					   	    		echo "<option selected>Ja</option>
						        		  <option>Nein</option>";
					   	    	}
					   	    	else{
					   	    		echo "<option>Ja</option>
						        		  <option selected>Nein</option>";

					   	    	}					 
						    echo "</select>	

	
						</div>	
  			 			<label class='control-label col-xxl-1' for='2kreisdl5'>2 Kreis DL-5</label>
  			 			<div class='col-xxl-1'>
	  			 			<select class='form-control input-sm' id='2kreisdl5'>";
						    if($row["2 Kreis DL-5"]==1){
					   	    		echo "<option selected>Ja</option>
						        		  <option>Nein</option>";
					   	    	}
					   	    	else{
					   	    		echo "<option>Ja</option>
						        		  <option selected>Nein</option>";

					   	    	}					 
						    echo "</select>	

						</div>	  			 											 
  			 	</div>	
				<div class='form-group'>
  			 			<label class='control-label col-xxl-1' for='dl10'>DL-10</label>
  			 			<div class='col-xxl-1'>
	  			 			<select class='form-control input-sm' id='dl10'>";
						    if($row["DL-10"]==1){
					   	    		echo "<option selected>Ja</option>
						        		  <option>Nein</option>";
					   	    	}
					   	    	else{
					   	    		echo "<option>Ja</option>
						        		  <option selected>Nein</option>";

					   	    	}					 
						    echo "</select>	

	
						</div>	
  			 			<label class='control-label col-xxl-1' for='dltech'>DL-Tech</label>
  			 			<div class='col-xxl-1'>
	  			 			<select class='form-control input-sm' id='dltech'>";
						    if($row["DL-tech"]==1){
					   	    		echo "<option selected>Ja</option>
						        		  <option>Nein</option>";
					   	    	}
					   	    	else{
					   	    		echo "<option>Ja</option>
						        		  <option selected>Nein</option>";

					   	    	}					 
						    echo "</select>	

						</div>	
						<label class='control-label col-xxl-1' for='co2'>CO2</label>
  			 			<div class='col-xxl-1'>
	  			 			<select class='form-control input-sm' id='co2'>";
						    if($row["CO2"]==1){
					   	    		echo "<option selected>Ja</option>
						        		  <option>Nein</option>";
					   	    	}
					   	    	else{
					   	    		echo "<option>Ja</option>
						        		  <option selected>Nein</option>";

					   	    	}					 
						    echo "</select>	
	
						</div>	
  			 			<label class='control-label col-xxl-1' for='nga'>NGA</label>
  			 			<div class='col-xxl-1'>
	  			 			<select class='form-control input-sm' id='nga'>";
						    if($row["NGA"]==1){
					   	    		echo "<option selected>Ja</option>
						        		  <option>Nein</option>";
					   	    	}
					   	    	else{
					   	    		echo "<option>Ja</option>
						        		  <option selected>Nein</option>";

					   	    	}					 
						    echo "</select>	

						</div>	
						<label class='control-label col-xxl-1' for='n2o'>N2O</label>
  			 			<div class='col-xxl-1'>
	  			 			<select class='form-control input-sm' id='n2o'>";
						    if($row["N2O"]==1){
					   	    		echo "<option selected>Ja</option>
						        		  <option>Nein</option>";
					   	    	}
					   	    	else{
					   	    		echo "<option>Ja</option>
						        		  <option selected>Nein</option>";

					   	    	}					 
						    echo "</select>	
	
						</div>	 			 											 
  			 	</div>	
  			 	<div class='well well-sm'><input type='button' id='saveBauangaben' class='btn btn-success btn-sm' value='Bauangaben speichern'></input></div>
			</form>";
	
		
		$mysqli ->close();
?>

<script>

	//Bauangaben speichern
	$("input[value='Bauangaben speichern']").click(function(){
	    var funktionBO = $("#funktionBO").val();
	    var Elektro= $("#Elektro").val();
		var geraete	= $("#geraete").val();
		var medgas= $("#medgas").val();
		var baustatik= $("#baustatik").val();
		var hkls= $("#hkls").val();
		var abdunkelbarkeit= ($("#abdunkelbarkeit").val() == 'Ja') ? '1' : '0';
		var strahlenanwendung= ($("#strahlenanwendung").val() == 'Ja') ? '1' : ($("#strahlenanwendung").val() == 'Nein') ? '0' : '2';
		var laseranwendung= ($("#laseranwendung").val() == 'Ja') ? '1' : '0';
		var awg= $("#awg").val();
		var av= ($("#av").val() == 'Ja') ? '1' : '0';
		var sv= ($("#sv").val() == 'Ja') ? '1' : '0';
		var zsv= ($("#zsv").val() == 'Ja') ? '1' : '0';
		var usv= ($("#usv").val() == 'Ja') ? '1' : '0';
		var h6020= $("#h6020").val();
		var iso= $("#iso").val();
		var gmp= $("#gmp").val();
		var kreiso2_1 = ($("#1kreiso2").val() == 'Ja') ? '1' : '0';
		var kreiso2_2 = ($("#2kreiso2").val() == 'Ja') ? '1' : '0';
		var kreisva_1 = ($("#1kreisva").val() == 'Ja') ? '1' : '0';
		var kreisva_2 = ($("#2kreisva").val() == 'Ja') ? '1' : '0';
		var kreisdl5_1 = ($("#1kreisdl5").val() == 'Ja') ? '1' : '0';
		var kreisdl5_2 = ($("#2kreisdl5").val() == 'Ja') ? '1' : '0';
		var dl10 = ($("#dl10").val() == 'Ja') ? '1' : '0';
		var dltech = ($("#dltech").val() == 'Ja') ? '1' : '0';
		var co2 = ($("#co2").val() == 'Ja') ? '1' : '0';
		var nga = ($("#nga").val() == 'Ja') ? '1' : '0';
		var n2o = ($("#n2o").val() == 'Ja') ? '1' : '0';


		$.ajax({
	        url : "saveRoomSpecifications.php",
	        data:{"funktionBO":funktionBO,"Elektro":Elektro,"geraete":geraete,"medgas":medgas,"baustatik":baustatik,"hkls":hkls,"abdunkelbarkeit":abdunkelbarkeit,"strahlenanwendung":strahlenanwendung,"laseranwendung":laseranwendung,"awg":awg,"av":av,"sv":sv,"zsv":zsv,"usv":usv,"h6020":h6020,"iso":iso,"gmp":gmp,"kreiso2_1":kreiso2_1,"kreiso2_2":kreiso2_2,"kreisva_1":kreisva_1,"kreisva_2":kreisva_2,"kreisdl5_1":kreisdl5_1,"kreisdl5_2":kreisdl5_2,"dl10":dl10,"dltech":dltech,"co2":co2 ,"nga":nga,"n2o":n2o},
	        type: "GET",
	        success: function(data){
	        	alert(data);
	        } 
        }); 
		     
    });


</script> 

</body>
</html>