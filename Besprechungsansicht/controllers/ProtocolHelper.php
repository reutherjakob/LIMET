<?php

class ProtocolHelper
{
    public static $lastStatus;

    /**
     * Generate a change protocol text comparing old and new data.
     *
     * @param array $old Previous data state (empty if none)
     * @param array $new New data state
     * @param string $changeComment User-provided comment (optional)
     * @param string $elementName Name of the element affected
     * @return string Protocol text describing changes
     */
    public static function generateProtocolText(array $old, array $new, string $changeComment, string $elementName): string
    {
        $logData = sprintf(
            "[%s] OLD: %s | NEW: %s | COMMENT: %s | ELEMENT: %s\n",
            date('Y-m-d H:i:s'),
            json_encode($old, JSON_UNESCAPED_UNICODE),
            json_encode($new, JSON_UNESCAPED_UNICODE),
            $changeComment,
            $elementName
        );
        file_put_contents(__DIR__ . '/protocol.log', $logData, FILE_APPEND);

        self::$lastStatus = $new["status"] ?? null;
        $varianteLabels = [
            0 => '-',
            1 => 'A',
            2 => 'B',
            3 => 'C',
            4 => 'D',
            5 => 'E',
            6 => 'F',
            7 => 'G',
        ];
        $bestandNeu = ($new['Neu/Bestand'] ?? null);
        $bestandstext = $bestandNeu === 0 ? "Ja" : "Nein";

        if (empty($old)) {
            $elementName = $elementName . " (Var: " . $varianteLabels[$new['variant'] ?? 0] . ", Best.: " . $bestandstext . " ). Anmerkung: {$changeComment}";
            return "{$elementName} wurde {$new['Anzahl']} mal hinzugefügt.";// Neue Stückzahl: {$new['Anzahl']}";
        }

        $changes = [];
        if (isset($old['Anzahl'], $new['Anzahl']) && $old['Anzahl'] !== $new['Anzahl']) {
            $elementName = $elementName . " (Var: " . $varianteLabels[$new['variant'] ?? 0] . ", Best.: " . $bestandstext . " )";
            $verb = ($new['Anzahl'] > $old['Anzahl']) ? 'erhöht' : 'reduziert';
            $changes[] = "Anzahl von {$elementName} von {$old['Anzahl']} auf {$new['Anzahl']} {$verb}";
        }
        if (($old['variant'] ?? null) !== ($new['variant'] ?? null)) {
            $oldVar = $varianteLabels[$old['variant'] ?? 0];
            $newVar = $varianteLabels[$new['variant'] ?? 0];
            $changes[] = "Variante von {$elementName} von '{$oldVar}' auf '{$newVar}' geändert";
        }
        if (($old['status'] ?? null) !== ($new['status'] ?? null)) {
            $elementName = $elementName . " (Var: " . $varianteLabels[$new['variant'] ?? 0] . ", Best.: " . $bestandstext . " )";
            $statusLabels = [
                0 => 'nichts',
                1 => 'Nutzeranforderung',
                2 => 'Freigegeben'
            ];
            $newStatusCode = $new['status'] ?? null;
            $newStatusLabel = $statusLabels[$newStatusCode] ?? '(unbekannt)';
            if (($old['status'] ?? null) !== $newStatusCode) {
                $changes[] = "Status von {$elementName} auf '{$newStatusLabel}' geändert";
            }
        }
        if (($old['Neu/Bestand'] ?? null) !== $bestandNeu) {
            $elementName = $elementName . " (Var: " . $varianteLabels[$new['variant'] ?? 0] . ")";
            $newText = ($new['Neu/Bestand'] == 1) ? 'neu zu beschaffen' : 'im Bestand';
            $changes[] = "Änderung Bestand-Status von {$elementName}: ist '{$newText}'";
        }
        if (!empty($changeComment)) {
            $changes[] = "Anmerkung: {$changeComment}";
        }
        if (empty($changes)) {
            return "Keine Änderungen an {$elementName}.";
        }
        return implode('. ', $changes) . '.';
    }


    /**
     * Update the remark link and append protocol text to remark text.
     *
     * @param mysqli $conn MySQLi connection object
     * @param int $vermerkID ID for the remark to update
     * @param int $relationId Relation ID for linking
     * @param string $protokollText Text to append to the remark
     * @throws Exception on DB errors
     */
    public static function updateRemarkAndLink(mysqli $conn, int $vermerkID, int $relationId, string $protokollText): void
    {
        if (!$vermerkID || !$relationId) {
            return;
        }

        $sqlVermerkUpdate = "
        UPDATE tabelle_rb_aenderung 
        SET vermerk_ID = ?
        WHERE idtabelle_rb_aenderung = (
            SELECT MAX(idtabelle_rb_aenderung) FROM tabelle_rb_aenderung WHERE id = ?
        )";
        $stmtVermerk = $conn->prepare($sqlVermerkUpdate);
        if (!$stmtVermerk) {
            throw new Exception("Prepare fehlgeschlagen: " . $conn->error);
        }
        $stmtVermerk->bind_param("ii", $vermerkID, $relationId);
        if (!$stmtVermerk->execute()) {
            $stmtVermerk->close();
            throw new Exception("Fehler beim Verknüpfen von Vermerk und Änderung: " . $stmtVermerk->error);
        }
        $stmtVermerk->close();

        // Fetch old remark text
        $sqlSelectVermerk = "SELECT Vermerktext FROM tabelle_Vermerke WHERE idtabelle_Vermerke = ?";
        $stmtSelect = $conn->prepare($sqlSelectVermerk);
        if (!$stmtSelect) {
            throw new Exception("Prepare fehlgeschlagen: " . $conn->error);
        }
        $stmtSelect->bind_param('i', $vermerkID);
        if (!$stmtSelect->execute()) {
            $stmtSelect->close();
            throw new Exception("Fehler beim Abrufen des Vermerktextes: " . $stmtSelect->error);
        }
        $result = $stmtSelect->get_result();
        $oldVermerk = $result->fetch_assoc()['Vermerktext'] ?? '';
        $stmtSelect->close();

        // Append new text
        $separator = (!empty($oldVermerk)) ? "\n" : '';
        $newVermerkText = $oldVermerk . $separator . $protokollText;

        // Update remark text
        switch (self::$lastStatus) {
            case 1:
                $vermerkart = "Nutzerwunsch";
                break;
            case 2:
                $vermerkart = "Freigegeben";
                break;
            default:
                $vermerkart = "Info";
        }
        self::$lastStatus = 0;

        $sqlUpdateVermerk = "UPDATE tabelle_Vermerke SET Vermerktext = ?, Vermerkart = ?  WHERE idtabelle_Vermerke = ?";
        $stmtUpdate = $conn->prepare($sqlUpdateVermerk);
        if (!$stmtUpdate) {
            throw new Exception("Prepare fehlgeschlagen: " . $conn->error);
        }
        $stmtUpdate->bind_param('ssi', $newVermerkText, $vermerkart, $vermerkID);
        if (!$stmtUpdate->execute()) {
            $stmtUpdate->close();
            error_log("Fehler beim Anhängen des Vermerktextes: " . $stmtUpdate->error);
        }
        $stmtUpdate->close();
    }
}
