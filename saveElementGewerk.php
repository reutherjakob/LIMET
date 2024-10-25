<?php

session_start();
include '_utils.php';
check_login();

$mysqli = utils_connect_sql();
$sql = "SELECT tabelle_projekt_element_gewerk.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke, tabelle_projekt_element_gewerk.tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG, tabelle_projekt_element_gewerk.tabelle_auftraggeberg_gug_idtabelle_auftraggeberg_GUG
			FROM tabelle_projekt_element_gewerk
			WHERE (((tabelle_projekt_element_gewerk.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . ") AND ((tabelle_projekt_element_gewerk.tabelle_elemente_idTABELLE_Elemente)=" . $_SESSION["elementID"] . "));";

$result = $mysqli->query($sql);

$row_cnt = $result->num_rows;

$gewerkID = $_GET["gewerk"];
$ghgID = $_GET["ghg"];
$gugID = $_GET["gug"];

// Wenn Eintrag vorhanden UPDATE
if ($row_cnt > 0) {
    if ($ghgID == 0) {
        $sql = "UPDATE `LIMET_RB`.`tabelle_projekt_element_gewerk`
					SET
					`tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke` = " . $gewerkID . ",
					`tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG` = NULL,
					`tabelle_auftraggeberg_gug_idtabelle_auftraggeberg_GUG` = NULL
					WHERE `tabelle_projekte_idTABELLE_Projekte` = " . $_SESSION["projectID"] . " AND `tabelle_elemente_idTABELLE_Elemente` = " . $_SESSION["elementID"] . ";";
    } else {
        if ($gugID == 0) {
            $sql = "UPDATE `LIMET_RB`.`tabelle_projekt_element_gewerk`
					SET
					`tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke` = " . $gewerkID . ",
					`tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG` = " . $ghgID . ",
					`tabelle_auftraggeberg_gug_idtabelle_auftraggeberg_GUG` = NULL
					WHERE `tabelle_projekte_idTABELLE_Projekte` = " . $_SESSION["projectID"] . " AND `tabelle_elemente_idTABELLE_Elemente` = " . $_SESSION["elementID"] . ";";
        } else {
            $sql = "UPDATE `LIMET_RB`.`tabelle_projekt_element_gewerk`
					SET
					`tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke` = " . $gewerkID . ",
					`tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG` = " . $ghgID . ",
					`tabelle_auftraggeberg_gug_idtabelle_auftraggeberg_GUG` = " . $gugID . "
					WHERE `tabelle_projekte_idTABELLE_Projekte` = " . $_SESSION["projectID"] . " AND `tabelle_elemente_idTABELLE_Elemente` = " . $_SESSION["elementID"] . ";";
        }
    }
    // Query ausführen
    if ($mysqli->query($sql) === TRUE) {
        echo "Gewerk erfolgreich aktualisiert!";
    } else {
        echo "Error: " . $sql . "<br>" . $mysqli->error;
    }
}
// Neuer Eintrag INSERT
else {
    if ($ghgID == 0) {
        $sql = "INSERT INTO `LIMET_RB`.`tabelle_projekt_element_gewerk`
						(`tabelle_projekte_idTABELLE_Projekte`,
						`tabelle_elemente_idTABELLE_Elemente`,
						`tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke`,
						`tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG`,
						`tabelle_auftraggeberg_gug_idtabelle_auftraggeberg_GUG`)
						VALUES
						(" . $_SESSION["projectID"] . ",
						" . $_SESSION["elementID"] . ",
						" . $gewerkID . ",
						NULL,
						NULL);";
    } else {
        if ($gugID == 0) {
            $sql = "INSERT INTO `LIMET_RB`.`tabelle_projekt_element_gewerk`
						(`tabelle_projekte_idTABELLE_Projekte`,
						`tabelle_elemente_idTABELLE_Elemente`,
						`tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke`,
						`tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG`,
						`tabelle_auftraggeberg_gug_idtabelle_auftraggeberg_GUG`)
						VALUES
						(" . $_SESSION["projectID"] . ",
						" . $_SESSION["elementID"] . ",
						" . $gewerkID . ",
						" . $ghgID . ",
						NULL);";
        } else {
            $sql = "INSERT INTO `LIMET_RB`.`tabelle_projekt_element_gewerk`
						(`tabelle_projekte_idTABELLE_Projekte`,
						`tabelle_elemente_idTABELLE_Elemente`,
						`tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke`,
						`tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG`,
						`tabelle_auftraggeberg_gug_idtabelle_auftraggeberg_GUG`)
						VALUES
						(" . $_SESSION["projectID"] . ",
						" . $_SESSION["elementID"] . ",
						" . $gewerkID . ",
						" . $ghgID . ",
						" . $gugID . ");";
        }
    }
    // Query ausführen
    if ($mysqli->query($sql) === TRUE) {
        echo "Gewerk erfolgreich angefügt!";
    } else {
        echo "Error: " . $sql . "<br>" . $mysqli->error;
    }
}

$mysqli->close();
?>