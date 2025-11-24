<?php
// 25 FX
session_start();
require_once 'utils/_utils.php';
check_login();

$mysqli = utils_connect_sql();

$projectID = $_SESSION["projectID"];
$elementID = $_SESSION["elementID"];
$gewerkID = getPostInt('gewerk');
$ghgID = getPostInt('ghg');
$gugID = getPostInt('gug');

$sql = "SELECT tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke, tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG, tabelle_auftraggeberg_gug_idtabelle_auftraggeberg_GUG
        FROM tabelle_projekt_element_gewerk
        WHERE tabelle_projekte_idTABELLE_Projekte = ? AND tabelle_elemente_idTABELLE_Elemente = ?";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("ii", $projectID, $elementID);
$stmt->execute();
$result = $stmt->get_result();
$row_cnt = $result->num_rows;
$stmt->close();

if ($row_cnt > 0) {
    if ($ghgID == 0) {
        $sql = "UPDATE LIMET_RB.tabelle_projekt_element_gewerk
                SET tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke = ?, 
                    tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG = NULL,
                    tabelle_auftraggeberg_gug_idtabelle_auftraggeberg_GUG = NULL
                WHERE tabelle_projekte_idTABELLE_Projekte = ? AND tabelle_elemente_idTABELLE_Elemente = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("iii", $gewerkID, $projectID, $elementID);
    } else {
        if ($gugID == 0) {
            $sql = "UPDATE LIMET_RB.tabelle_projekt_element_gewerk
                    SET tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke = ?, 
                        tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG = ?,
                        tabelle_auftraggeberg_gug_idtabelle_auftraggeberg_GUG = NULL
                    WHERE tabelle_projekte_idTABELLE_Projekte = ? AND tabelle_elemente_idTABELLE_Elemente = ?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("iiii", $gewerkID, $ghgID, $projectID, $elementID);
        } else {
            $sql = "UPDATE LIMET_RB.tabelle_projekt_element_gewerk
                    SET tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke = ?, 
                        tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG = ?,
                        tabelle_auftraggeberg_gug_idtabelle_auftraggeberg_GUG = ?
                    WHERE tabelle_projekte_idTABELLE_Projekte = ? AND tabelle_elemente_idTABELLE_Elemente = ?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("iiiii", $gewerkID, $ghgID, $gugID, $projectID, $elementID);
        }
    }
    if ($stmt->execute()) {
        echo "Gewerk erfolgreich aktualisiert!";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
} else {
    if ($ghgID == 0) {
        $sql = "INSERT INTO LIMET_RB.tabelle_projekt_element_gewerk
               (tabelle_projekte_idTABELLE_Projekte, tabelle_elemente_idTABELLE_Elemente, tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke, tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG, tabelle_auftraggeberg_gug_idtabelle_auftraggeberg_GUG)
               VALUES (?, ?, ?, NULL, NULL)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("iii", $projectID, $elementID, $gewerkID);
    } else {
        if ($gugID == 0) {
            $sql = "INSERT INTO LIMET_RB.tabelle_projekt_element_gewerk
                   (tabelle_projekte_idTABELLE_Projekte, tabelle_elemente_idTABELLE_Elemente, tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke, tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG, tabelle_auftraggeberg_gug_idtabelle_auftraggeberg_GUG)
                   VALUES (?, ?, ?, ?, NULL)";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("iiii", $projectID, $elementID, $gewerkID, $ghgID);
        } else {
            $sql = "INSERT INTO LIMET_RB.tabelle_projekt_element_gewerk
                   (tabelle_projekte_idTABELLE_Projekte, tabelle_elemente_idTABELLE_Elemente, tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke, tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG, tabelle_auftraggeberg_gug_idtabelle_auftraggeberg_GUG)
                   VALUES (?, ?, ?, ?, ?)";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("iiiii", $projectID, $elementID, $gewerkID, $ghgID, $gugID);
        }
    }
    if ($stmt->execute()) {
        echo "Gewerk erfolgreich angefügt!";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

$mysqli->close();
?>
