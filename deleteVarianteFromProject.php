<?php
require_once 'utils/_utils.php';
check_login();

header('Content-Type: text/plain; charset=utf-8');

$elementID   = getPostInt("elementID", 0);
$variantenID = getPostInt("variantenID", 0);
$projectID   = (int)($_SESSION["projectID"] ?? 0);

if ($elementID <= 0 || $variantenID <= 0 || $projectID <= 0) {
    http_response_code(400);
    echo "Ungültige Parameter – es wurde nichts gelöscht.";
    exit;
}

$mysqli = utils_connect_sql();

/* --------------------------------------------------------------------------
 * Sicherheits-Check:
 * Nur löschen, wenn die Summe der Anzahl für diese Element+Varianten-Kombination
 * in DIESEM Projekt 0 ist (oder gar keine Raumzuordnung existiert).
 * Verhindert versehentliches Löschen echter, benutzter Varianten.
 * -------------------------------------------------------------------------- */
$check = $mysqli->prepare(
    "SELECT COUNT(*) AS treffer
     FROM tabelle_räume_has_tabelle_elemente rhe
     INNER JOIN tabelle_räume r
             ON r.idTABELLE_Räume =
                rhe.TABELLE_Räume_idTABELLE_Räume
     WHERE rhe.TABELLE_Elemente_idTABELLE_Elemente = ?
       AND rhe.tabelle_Varianten_idtabelle_Varianten = ?
       AND r.tabelle_projekte_idTABELLE_Projekte = ?
       AND COALESCE(rhe.Anzahl, 0) <> 0"
);

$check->bind_param("iii", $elementID, $variantenID, $projectID);
$check->execute();

$treffer = (int)($check->get_result()->fetch_assoc()['treffer'] ?? 0);
$check->close();

if ($treffer > 0) {
    http_response_code(409);
    echo "Abbruch: Es existieren noch belegte Raumzuordnungen.";
    $mysqli->close();
    exit;
}

/* --------------------------------------------------------------------------
 * Löschen in einer Transaktion über alle drei betroffenen Tabellen.
 * (Reihenfolge unkritisch, da nur diese drei die Variante referenzieren.)
 * -------------------------------------------------------------------------- */
$mysqli->begin_transaction();
try {
    // 1) Raumzuordnungen (enthält Anzahl) – nur Räume dieses Projekts
    $s1 = $mysqli->prepare(
        "DELETE rhe
         FROM tabelle_räume_has_tabelle_elemente rhe
         INNER JOIN tabelle_räume r
                 ON r.idTABELLE_Räume = rhe.TABELLE_Räume_idTABELLE_Räume
         WHERE rhe.TABELLE_Elemente_idTABELLE_Elemente = ?
           AND rhe.tabelle_Varianten_idtabelle_Varianten = ?
           AND r.tabelle_projekte_idTABELLE_Projekte = ?"
    );
    $s1->bind_param("iii", $elementID, $variantenID, $projectID);
    $s1->execute();
    $n1 = $s1->affected_rows;
    $s1->close();

    // 2) Variantenkosten
    $s2 = $mysqli->prepare(
        "DELETE FROM tabelle_projekt_varianten_kosten
         WHERE tabelle_elemente_idTABELLE_Elemente = ?
           AND tabelle_Varianten_idtabelle_Varianten = ?
           AND tabelle_projekte_idTABELLE_Projekte = ?"
    );
    $s2->bind_param("iii", $elementID, $variantenID, $projectID);
    $s2->execute();
    $n2 = $s2->affected_rows;
    $s2->close();

    // 3) Variantenparameter
    $s3 = $mysqli->prepare(
        "DELETE FROM tabelle_projekt_elementparameter
         WHERE TABELLE_Elemente_idTABELLE_Elemente = ?
           AND tabelle_Varianten_idtabelle_Varianten = ?
           AND tabelle_projekte_idTABELLE_Projekte = ?"
    );
    $s3->bind_param("iii", $elementID, $variantenID, $projectID);
    $s3->execute();
    $n3 = $s3->affected_rows;
    $s3->close();

    $mysqli->commit();
    echo "Variante gelöscht (Zuordnungen: $n1, Kosten: $n2, Parameter: $n3).";
} catch (Throwable $e) {
    $mysqli->rollback();
    http_response_code(500);
    echo "Fehler beim Löschen – Rollback ausgeführt. Es wurde nichts geändert.";
}
$mysqli->close();