<?php
include "_utils.php";
check_login();

$mysqli = utils_connect_sql();
// Personendaten im Projekt laden
$sql = "SELECT tabelle_projekte_has_tabelle_ansprechpersonen.TABELLE_Projekte_idTABELLE_Projekte, 
			tabelle_projekte_has_tabelle_ansprechpersonen.TABELLE_Ansprechpersonen_idTABELLE_Ansprechpersonen, 
			tabelle_ansprechpersonen.Name, 
			tabelle_ansprechpersonen.Vorname, 
			tabelle_ansprechpersonen.Tel, 
			tabelle_ansprechpersonen.Adresse, 
			tabelle_ansprechpersonen.PLZ, 
			tabelle_ansprechpersonen.Ort, 
			tabelle_ansprechpersonen.Land, 
			tabelle_ansprechpersonen.Mail, 
                        tabelle_ansprechpersonen.Raumnr,
			tabelle_organisation.idtabelle_organisation, 
			tabelle_projektzuständigkeiten.idTABELLE_Projektzuständigkeiten
			FROM tabelle_ansprechpersonen INNER JOIN (tabelle_organisation INNER JOIN (tabelle_projektzuständigkeiten INNER JOIN tabelle_projekte_has_tabelle_ansprechpersonen ON tabelle_projektzuständigkeiten.idTABELLE_Projektzuständigkeiten = tabelle_projekte_has_tabelle_ansprechpersonen.TABELLE_Projektzuständigkeiten_idTABELLE_Projektzuständigkeiten) ON tabelle_organisation.idtabelle_organisation = tabelle_projekte_has_tabelle_ansprechpersonen.tabelle_organisation_idtabelle_organisation) ON tabelle_ansprechpersonen.idTABELLE_Ansprechpersonen = tabelle_projekte_has_tabelle_ansprechpersonen.TABELLE_Ansprechpersonen_idTABELLE_Ansprechpersonen
			WHERE (((tabelle_projekte_has_tabelle_ansprechpersonen.TABELLE_Projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . ") AND ((tabelle_projekte_has_tabelle_ansprechpersonen.TABELLE_Ansprechpersonen_idTABELLE_Ansprechpersonen)=" . $_GET["personID"] . "));";

$result = $mysqli->query($sql);
$row = $result->fetch_assoc();

$name = $row["Name"];
$vorname = $row["Vorname"];
$tel = $row["Tel"];
$adresse = $row["Adresse"];
$plz = $row["PLZ"];
$ort = $row["Ort"];
$land = $row["Land"];
$mail = $row["Mail"];
$id_organisation = $row["idtabelle_organisation"];
$id_zustaendigkeit = $row["idTABELLE_Projektzuständigkeiten"];
$raumNr = $row["Raumnr"];


echo "<form class='form-horizontal' role='form'>
		 <div class='form-group row'>
	 			<label class='control-label col-md-2' for='Name'>Name</label>
	 			<div class='col-md-8'>
	 				<input type='text' class='form-control form-control-sm' id='Name' value='" . $name . "'></input>
				</div>						  			 											 						 			
	 	</div>		  			 		
	 	<div class='form-group row'>
	 			<label class='control-label col-md-2' for='Vorname'>Vorname</label>
	 			<div class='col-md-8'>
	 				<input type='text' class='form-control form-control-sm' id='Vorname' value='" . $vorname . "'></input>
				</div>	
		</div>
		<div class='form-group row'>
	 			<label class='control-label col-md-2' for='Tel'>Tel</label>
	 			<div class='col-md-8'>
	 				<input type='text' class='form-control form-control-sm' id='Tel' value='" . $tel . "'></input>
				</div>	
		</div>
		<div class='form-group row'>
	 			<label class='control-label col-md-2' for='Adresse'>Adresse</label>
	 			<div class='col-md-8'>
	 				<input type='text' class='form-control form-control-sm' id='Adresse' value='" . $adresse . "'></input>
				</div>	
		</div>
		<div class='form-group row'>
	 			<label class='control-label col-md-2' for='PLZ'>PLZ</label>
	 			<div class='col-md-8'>
	 				<input type='text' class='form-control form-control-sm' id='PLZ' value='" . $plz . "'></input>
				</div>	
		</div>
		<div class='form-group row'>
	 			<label class='control-label col-md-2' for='Ort'>Ort</label>
	 			<div class='col-md-8'>
	 				<input type='text' class='form-control form-control-sm' id='Ort' value='" . $ort . "'></input>
				</div>	
		</div>
		<div class='form-group row'>
	 			<label class='control-label col-md-2' for='Land'>Land</label>
	 			<div class='col-md-8'>
	 				<input type='text' class='form-control form-control-sm' id='Land' value='" . $land . "'></input>
				</div>	
		</div>
		<div class='form-group row'>
	 			<label class='control-label col-md-2' for='Email'>Email</label>
	 			<div class='col-md-8'>
	 				<input type='text' class='form-control form-control-sm' id='Email' value='" . $mail . "'></input>
				</div>	
		</div>";

$sql = "SELECT tabelle_projektzuständigkeiten.idTABELLE_Projektzuständigkeiten, tabelle_projektzuständigkeiten.Zuständigkeit FROM tabelle_projektzuständigkeiten ORDER BY Zuständigkeit;";
$result = $mysqli->query($sql);


echo "<div class='form-group row'>
	 			<label class='control-label col-md-2' for='zustaendigkeit'>Zuständigkeit</label>
				<div class='col-md-8'>
					<select class='form-control form-control-sm' id='zustaendigkeit' name='selectCategory'>";
while ($row = $result->fetch_assoc()) {
    if ($id_zustaendigkeit == $row["idTABELLE_Projektzuständigkeiten"]) {
        echo "<option value=" . $row["idTABELLE_Projektzuständigkeiten"] . " selected>" . $row["Zuständigkeit"] . "</option>";
    } else {
        echo "<option value=" . $row["idTABELLE_Projektzuständigkeiten"] . ">" . $row["Zuständigkeit"] . "</option>";
    }
}
echo "</select></div></div>";

$sql = "SELECT tabelle_organisation.idtabelle_organisation, tabelle_organisation.Organisation FROM tabelle_organisation ORDER BY Organisation;";
$result = $mysqli->query($sql);

echo "<div class='form-group row'>
	 			<label class='control-label col-md-2' for='organisation'>Organisation</label>
				<div class='col-md-8'>
					<select class='form-control form-control-sm' id='organisation' name='organisation'>";
while ($row = $result->fetch_assoc()) {
    if ($id_organisation == $row["idtabelle_organisation"]) {
        echo "<option value=" . $row["idtabelle_organisation"] . " selected>" . $row["Organisation"] . "</option>";
    } else {
        echo "<option value=" . $row["idtabelle_organisation"] . ">" . $row["Organisation"] . "</option>";
    }
}
echo "</select>	
				</div>
		</div>
                <div class='form-group row'>
	 			<label class='control-label col-md-2' for='Raumnr'>Raumnr</label>
	 			<div class='col-md-8'>
	 				<input type='text' class='form-control form-control-sm' id='Raumnr' value='" . $raumNr . "'>
				</div>	
		</div>
	 	<div class='form-check-inline'>
	 	<!-- <button type='button' id='" . $_GET["personID"] . "' class='btn btn-warning btn-sm' value='Personendaten ändern'> <i class='fas fa-save' ></i> </button>
			<button type='button' id='addPersonToProjectButton' class='btn btn-success btn-sm' value='Person zu Projekt hinzufügen'> <i class='fas fa-plus'></i> </button>
	 		<button type='button' id='" . $_GET["personID"] . "' class='btn btn-danger btn-sm' value='Person von Projekt entfernen'> <i class='fas fa-minus'></i> </button>
	 		  <i class='fas fa-user-minus'></i> <i class='fas fa-user-slash'></i>   
	 		  
	 	<button type='button' id='addPersonToProjectButton' class='btn btn-success btn-sm' data-bs-toggle='tooltip' data-bs-placement='top' title='Person zu Projekt hinzufügen'>    <i class='fas fa-plus'></i>
        </button>  	 -->
	    <button type='button' id='" . $_GET["personID"] . "' class='btn btn-warning btn-sm' data-bs-toggle='tooltip' data-bs-placement='top' title='Personendaten ändern'>       <i class='fas fa-edit'></i>
        </button>
        <button type='button' id='" . $_GET["personID"] . "' class='btn btn-danger btn-sm' data-bs-toggle='tooltip' data-bs-placement='top' title='Person von Projekt entfernen' >    <i class='fas fa-minus'></i>
        </button>
	 	</div>			  
	</form>";
$mysqli->close();
?>

<script charset="utf-8">

    document.addEventListener("DOMContentLoaded", function () {
        let tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        let tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });


    /* $("button[title='Person zu Projekt hinzufügen']").click(function () {
       let Name = $("#Name").val();
       let Vorname = $("#Vorname").val();
       let Tel = $("#Tel").val();
       let Adresse = $("#Adresse").val();
       let PLZ = $("#PLZ").val();
       let Ort = $("#Ort").val();
       let Land = $("#Land").val();
       let Email = $("#Email").val();
       let zustaendigkeit = $("#zustaendigkeit").val();
       let organisation = $("#organisation").val();
       let Raumnr = $("#Raumnr").val();

       if (Name.length > 0 && Vorname.length > 0 && Tel.length > 0) {
           $.ajax({
               url: "addPersonToProject.php",
               data: {
                   "Name": Name,
                   "Vorname": Vorname,
                   "Tel": Tel,
                   "Adresse": Adresse,
                   "PLZ": PLZ,
                   "Ort": Ort,
                   "Land": Land,
                   "Email": Email,
                   "zustaendigkeit": zustaendigkeit,
                   "organisation": organisation,
                   "Raumnr": Raumnr
               },
               type: "GET",
               success: function (data) {
                   alert(data);
                   $.ajax({
                       url: "getPersonsOfProject.php",
                       type: "GET",
                       success: function (data) {
                           $("#personsInProject").html(data);
                       }
                   });
               }
           });
       } else {
           alert("Bitte überprüfen Sie Ihre Angaben");
       }
   }); */

    // Personendaten ändern
    $("button[title='Personendaten ändern']").click(function () {
        let Name = $("#Name").val();
        let Vorname = $("#Vorname").val();
        let Tel = $("#Tel").val();
        let Adresse = $("#Adresse").val();
        let PLZ = $("#PLZ").val();
        let Ort = $("#Ort").val();
        let Land = $("#Land").val();
        let Email = $("#Email").val();
        let zustaendigkeit = $("#zustaendigkeit").val();
        let organisation = $("#organisation").val();
        let Raumnr = $("#Raumnr").val();
        let personID = this.id;

        if (Name.length > 0 && Vorname.length > 0 && Tel.length > 0) {
            $.ajax({
                url: "savePersonProjectData.php",
                data: {
                    "Name": Name,
                    "Vorname": Vorname,
                    "Tel": Tel,
                    "Adresse": Adresse,
                    "PLZ": PLZ,
                    "Ort": Ort,
                    "Land": Land,
                    "Email": Email,
                    "personID": personID,
                    "zustaendigkeit": zustaendigkeit,
                    "organisation": organisation,
                    "Raumnr": Raumnr
                },
                type: "GET",
                success: function (data) {
                    alert(data);
                    $.ajax({
                        url: "getPersonsOfProject.php",
                        type: "GET",
                        success: function (data) {
                            $("#personsInProject").html(data);
                        }
                    });
                }
            });
        } else {
            alert("Bitte überprüfen Sie Ihre Angaben");
        }
    });

    // Person von Projekt entfernen
    $("button[title='Person von Projekt entfernen']").click(function (message) {
        let personID = this.id;
            $.ajax({
                url: "deletePersonFromProject.php",
                data: {"personID": personID},
                type: "GET",
                success: function (data) {
                    alert(data);
                    $.ajax({
                        url: "getPersonsOfProject.php",
                        type: "GET",
                        success: function (data) {
                            $("#personsInProject").html(data);

                            $.ajax({
                                url: "getPersonsNotInProject.php",
                                type: "GET",
                                success: function (data) {
                                    $("#personsNotInProject").html(data);
                                }
                            });

                        }
                    });
                },
                error: function (data) {
                    alert("Frag N Jakob, lol");
                }
            });
    });


</script>
