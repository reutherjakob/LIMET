<?php
//  25Fx
require_once 'utils/_utils.php';
check_login();

$mysqli = utils_connect_sql();
$id = getPostInt("id");
$cadKommentar = getPostString("CADcomment");

$mapYesNo = fn($val) => $val === "Ja" ? 1 : 0;
$mapKontrolliert = fn($val) => $val === "Nicht geprüft" ? 0 : ($val === "Freigegeben" ? 1 : 2);

if ($id !== 0 &&
    ($cadNotwendig = $mapYesNo(getPostString("selectCAD_notwendig"))) !== null &&
    ($dwgVorhanden = $mapYesNo(getPostString("selectCAD_dwg_vorhanden"))) !== null &&
    ($dwgKontrollliert = $mapKontrolliert(getPostString("selectCAD_dwg_kontrolliert"))) !== null &&
    ($familieVorhanden = $mapYesNo(getPostString("selectCAD_familie_vorhanden"))) !== null &&
    ($familieKontrolliert = $mapKontrolliert(getPostString("selectCAD_familie_kontrolliert"))) !== null
) {
    $stmt = $mysqli->prepare(
        "UPDATE `LIMET_RB`.`tabelle_elemente`
        SET `CAD_notwendig` = ?, `CAD_dwg_vorhanden` = ?, `CAD_dwg_kontrolliert` = ?, `CAD_familie_vorhanden` = ?, 
            `CAD_familie_kontrolliert` = ?, `CAD_Kommentar` = ?
        WHERE `idTABELLE_Elemente` = ?"
    );
    $stmt->bind_param(
        "iiiiisi",
        $cadNotwendig,
        $dwgVorhanden,
        $dwgKontrollliert,
        $familieVorhanden,
        $familieKontrolliert,
        $cadKommentar,
        $id
    );
    if ($stmt->execute()) {
        echo "Erfolgreich aktualisiert!";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
    $mysqli->close();
}

?>
