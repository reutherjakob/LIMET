<?php
// 11-25-FX
require_once 'utils/_utils.php';
check_login();

$mysqli = utils_connect_sql();

$sessionRoomID = filter_var($_SESSION["roomID"] ?? null, FILTER_VALIDATE_INT);
if ($sessionRoomID === false || $sessionRoomID === null) {
	die("Invalid session room ID");
}

$sql = "SELECT tabelle_räume.idTABELLE_Räume, tabelle_räume.Abdunkelbarkeit, tabelle_räume.Strahlenanwendung, 
               tabelle_räume.Laseranwendung, tabelle_räume.H6020, tabelle_räume.GMP, tabelle_räume.ISO, 
               tabelle_räume.`1 Kreis O2`, tabelle_räume.`2 Kreis O2`, tabelle_räume.`1 Kreis Va`, tabelle_räume.`2 Kreis Va`,
               tabelle_räume.`1 Kreis DL-5`, tabelle_räume.`2 Kreis DL-5`, tabelle_räume.`DL-10`, tabelle_räume.`DL-tech`, 
               tabelle_räume.CO2, tabelle_räume.NGA, tabelle_räume.N2O, tabelle_räume.AV, tabelle_räume.SV, tabelle_räume.ZSV, 
               tabelle_räume.USV, tabelle_räume.Anwendungsgruppe, 
               tabelle_räume.`Anmerkung MedGas`, tabelle_räume.`Anmerkung Elektro`, tabelle_räume.`Anmerkung HKLS`, 
               tabelle_räume.`Anmerkung Geräte`, tabelle_räume.`Anmerkung FunktionBO`, tabelle_räume.`Anmerkung BauStatik`, 
               tabelle_räume.`Allgemeine Hygieneklasse`, tabelle_räume.`Fussboden OENORM B5220`, tabelle_räume.`IT Anbindung`
        FROM tabelle_räume
        WHERE tabelle_räume.idTABELLE_Räume = ?";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param('i', $sessionRoomID);
$stmt->execute();
$result = $stmt->get_result();

if (!$row = $result->fetch_assoc()) {
	die("Room not found");
}

// Fetch all values safely
$abdunkelbarkeit = $row["Abdunkelbarkeit"];
$Strahlenanwendung = $row["Strahlenanwendung"];
$Laseranwendung = $row["Laseranwendung"];
$H6020 = $row["H6020"];
$GMP = $row["GMP"];
$ISO = $row["ISO"];
$HYGIENEKLASSE = $row["Allgemeine Hygieneklasse"];

$Kreis1O2 = $row["1 Kreis O2"];
$Kreis2O2 = $row["2 Kreis O2"];

$Kreis1Va = $row["1 Kreis Va"];
$Kreis2Va = $row["2 Kreis Va"];
$Kreis1DL5 = $row["1 Kreis DL-5"];
$Kreis2DL5 = $row["2 Kreis DL-5"];

$DL10 = $row["DL-10"];
$DLtech = $row["DL-tech"];
$CO2 = $row["CO2"];
$NGA = $row["NGA"];
$N2O = $row["N2O"];
$AV = $row["AV"];
$SV = $row["SV"];
$ZSV = $row["ZSV"];
$USV = $row["USV"];

$Anwendungsgruppe = $row["Anwendungsgruppe"];
$AnmerkungMedGas = $row["Anmerkung MedGas"];
$AnmerkungElektro = $row["Anmerkung Elektro"];
$AnmerkungHKLS = $row["Anmerkung HKLS"];
$AnmerkungGeräte = $row["Anmerkung Geräte"];
$AnmerkungBauStatik = $row["Anmerkung BauStatik"];

$fussbodenklasse = $row["Fussboden OENORM B5220"];
$it = $row["IT Anbindung"];

$stmt->close();

// Fetch and sanitize posted room IDs array using utility function
$roomIDs = getPostArrayInt('rooms');

if (empty($roomIDs)) {
	die("No valid room IDs found");
}

$ausgabe = "";

$updateSql = "UPDATE LIMET_RB.tabelle_räume SET 
                Abdunkelbarkeit = ?, 
                Strahlenanwendung = ?,
                Laseranwendung = ?,
                H6020 = ?,
                GMP = ?,
                ISO = ?,
                `Allgemeine Hygieneklasse` = ?,
                `1 Kreis O2` = ?,
                `2 Kreis O2` = ?,
                `1 Kreis Va` = ?,
                `2 Kreis Va` = ?,
                `1 Kreis DL-5` = ?,
                `2 Kreis DL-5` = ?,
                `DL-10` = ?,
                `DL-tech` = ?,
                CO2 = ?,
                NGA = ?,
                N2O = ?,
                AV = ?,
                SV = ?,
                ZSV = ?,
                USV = ?,
                Anwendungsgruppe = ?,
                `Anmerkung MedGas` = ?,
                `Anmerkung Elektro` = ?,
                `Anmerkung HKLS` = ?,
                `Anmerkung Geräte` = ?,
                `Anmerkung BauStatik` = ?,
                `Fussboden OENORM B5220` = ?,
                `IT Anbindung` = ?
              WHERE idTABELLE_Räume = ?";

$updStmt = $mysqli->prepare($updateSql);

foreach ($roomIDs as $valueOfRoomID) {
	$updStmt->bind_param("sssssssssssssssssssssssssssii",
		$abdunkelbarkeit,
		$Strahlenanwendung,
		$Laseranwendung,
		$H6020,
		$GMP,
		$ISO,
		$HYGIENEKLASSE,
		$Kreis1O2,
		$Kreis2O2,
		$Kreis1Va,
		$Kreis2Va,
		$Kreis1DL5,
		$Kreis2DL5,
		$DL10,
		$DLtech,
		$CO2,
		$NGA,
		$N2O,
		$AV,
		$SV,
		$ZSV,
		$USV,
		$Anwendungsgruppe,
		$AnmerkungMedGas,
		$AnmerkungElektro,
		$AnmerkungHKLS,
		$AnmerkungGeräte,
		$AnmerkungBauStatik,
		$fussbodenklasse,
		$it,
		$valueOfRoomID
	);

	if ($updStmt->execute()) {
		$ausgabe .= "Raum " . htmlspecialchars($valueOfRoomID) . " erfolgreich aktualisiert!<br>";
	} else {
		$ausgabe .= "Error (Raum " . htmlspecialchars($valueOfRoomID) . "): " . htmlspecialchars($updStmt->error) . "<br>";
	}
}

$updStmt->close();
$mysqli->close();

echo $ausgabe;
?>
