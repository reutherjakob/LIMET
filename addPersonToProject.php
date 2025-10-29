<?php
// 10-2025 FX
require_once 'utils/_utils.php';
check_login();

if (getPostString("Name") !== "" && getPostString("Vorname") !== "" && getPostString("Tel") !== "") {

    $mysqli = utils_connect_sql();

    $stmt1 = $mysqli->prepare("INSERT INTO `tabelle_ansprechpersonen`
(`Name`, `Vorname`, `Tel`, `Adresse`, `PLZ`, `Ort`, `Land`, `Mail`, `Raumnr`)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

    if ($stmt1) {
// Use the utility functions to fetch sanitized/validated input
        $name = htmlspecialchars(getPostString("Name"), ENT_QUOTES, 'UTF-8');
        $vorname = htmlspecialchars(getPostString("Vorname"), ENT_QUOTES, 'UTF-8');
        $tel = htmlspecialchars(getPostString("Tel"), ENT_QUOTES, 'UTF-8');
        $adresse = htmlspecialchars(getPostString("Adresse"), ENT_QUOTES, 'UTF-8');
        $plz = htmlspecialchars(getPostString("PLZ"), ENT_QUOTES, 'UTF-8');
        $ort = htmlspecialchars(getPostString("Ort"), ENT_QUOTES, 'UTF-8');
        $land = htmlspecialchars(getPostString("Land"), ENT_QUOTES, 'UTF-8');
        $mail = filter_var(getPostString("Email"), FILTER_SANITIZE_EMAIL);
        $raumnr = htmlspecialchars(getPostString("Raumnr"), ENT_QUOTES, 'UTF-8');

        $stmt1->bind_param(
            'sssssssss',
            $name,
            $vorname,
            $tel,
            $adresse,
            $plz,
            $ort,
            $land,
            $mail,
            $raumnr
        );

        if ($stmt1->execute()) {
            echo "Person angelegt ";
            $id = $mysqli->insert_id;
        } else {
            echo "Error1: " . $stmt1->error;
            $stmt1->close();
            $mysqli->close();
            exit;
        }
        $stmt1->close();

        $stmt2 = $mysqli->prepare("INSERT INTO `tabelle_projekte_has_tabelle_ansprechpersonen`
(`TABELLE_Projekte_idTABELLE_Projekte`, `TABELLE_Ansprechpersonen_idTABELLE_Ansprechpersonen`, `TABELLE_Projektzuständigkeiten_idTABELLE_Projektzuständigkeiten`, `tabelle_organisation_idtabelle_organisation`)
VALUES (?, ?, ?, ?)");

        if ($stmt2) {
            $projectID = (int)$_SESSION["projectID"];
            $zustaendigkeit = getPostInt("zustaendigkeit");
            $organisation = getPostInt("organisation");

            $stmt2->bind_param(
                'iiii',
                $projectID,
                $id,
                $zustaendigkeit,
                $organisation
            );

            if ($stmt2->execute()) {
                echo "und zu Projekt hinzugefügt!";
            } else {
                echo "Error2: " . $stmt2->error;
            }
            $stmt2->close();
        } else {
            echo "Fehler bei der Projektzuordnung: " . $mysqli->error;
        }
    } else {
        echo "Fehler bei der Vorbereitung: " . $mysqli->error;
    }

    $mysqli->close();
} else {
    echo "Fehler bei der Eingabe: Name, Vorname und Tel dürfen nicht leer sein.";
}
