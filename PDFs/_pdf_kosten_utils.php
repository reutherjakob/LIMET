<?php
/**
 * Gemeinsame Helfer für die Kostenberichte (pdf_createKosten*.php)
 *
 *  - Budget-Freigabefilter:
 *      * abgelehnte Budgets werden NIE ausgegeben
 *      * Standard = nur freigegebene Budgets und Elemente ohne Budget
 *      * GET-Parameter inklNichtFreigegeben=1 (Switch auf der Seite) nimmt zusätzlich
 *        die noch offenen (nicht freigegebenen) Budgets mit
 *  - Alle Queries laufen über Prepared Statements (kb_query).
 *  - Wiederkehrende PDF-Bausteine (Kopf, Legenden, Kostenzeilen).
 *
 * Hinweis: Spalten-/Tabellennamen in den SQL-Strings sind fest im Code hinterlegt,
 * Benutzereingaben gehen ausschließlich über gebundene Parameter in die Queries.
 */

// Basis-Joins: Raum -> Element im Raum -> Gewerkzuordnung (+ Gewerk, GHG, Budget)
const KB_FROM_BASIS = "
    FROM tabelle_räume r
    INNER JOIN tabelle_räume_has_tabelle_elemente rhe
        ON rhe.TABELLE_Räume_idTABELLE_Räume = r.idTABELLE_Räume
    INNER JOIN tabelle_projekt_element_gewerk peg
        ON peg.tabelle_elemente_idTABELLE_Elemente = rhe.TABELLE_Elemente_idTABELLE_Elemente
       AND peg.tabelle_projekte_idTABELLE_Projekte = r.tabelle_projekte_idTABELLE_Projekte
    LEFT JOIN tabelle_auftraggeber_gewerke g
        ON g.idTABELLE_Auftraggeber_Gewerke = peg.tabelle_auftraggeber_gewerke_idTABELLE_Auftraggeber_Gewerke
    LEFT JOIN tabelle_auftraggeber_ghg ghg
        ON ghg.idtabelle_auftraggeber_GHG = peg.tabelle_auftraggeber_ghg_idtabelle_auftraggeber_GHG
    LEFT JOIN tabelle_projektbudgets pb
        ON pb.idtabelle_projektbudgets = rhe.tabelle_projektbudgets_idtabelle_projektbudgets
";

// Zusätzlicher Join auf die Variantenkosten (für alle Kostensummen)
const KB_JOIN_KOSTEN = "
    INNER JOIN tabelle_projekt_varianten_kosten pvk
        ON pvk.tabelle_elemente_idTABELLE_Elemente = rhe.TABELLE_Elemente_idTABELLE_Elemente
       AND pvk.tabelle_Varianten_idtabelle_Varianten = rhe.tabelle_Varianten_idtabelle_Varianten
       AND pvk.tabelle_projekte_idTABELLE_Projekte = r.tabelle_projekte_idTABELLE_Projekte
";

// Gesamt / Neu / Bestand in einer Abfrage
const KB_SELECT_KOSTEN = "
    SUM(`Kosten` * `Anzahl`) AS PP,
    SUM(IF(rhe.`Neu/Bestand` = 1, `Kosten` * `Anzahl`, 0)) AS PPNeu,
    SUM(IF(rhe.`Neu/Bestand` = 0, `Kosten` * `Anzahl`, 0)) AS PPBestand
";

const KB_KOSTENARTEN = ['PP', 'PPNeu', 'PPBestand'];

// Werte von tabelle_projektbudgets.status – alles andere (z.B. 0 / NULL) gilt als "noch nicht freigegeben"
const KB_STATUS_FREIGEGEBEN = 1;
const KB_STATUS_ABGELEHNT = 2;

// ==================== Budgetfilter ====================

function kb_inkl_nicht_freigegeben(): bool
{
    return filter_input(INPUT_GET, 'inklNichtFreigegeben', FILTER_VALIDATE_BOOLEAN) === true;
}

/** WHERE-Fragment (setzt Alias pb aus KB_FROM_BASIS voraus). */
function kb_budget_where(bool $inklNichtFreigegeben): string
{
    // Konstanten sind feste Integer im Code – keine Benutzereingabe
    return $inklNichtFreigegeben
        ? ' AND (pb.idtabelle_projektbudgets IS NULL OR NOT (pb.status <=> ' . (int)KB_STATUS_ABGELEHNT . ')) '
        : ' AND (pb.idtabelle_projektbudgets IS NULL OR pb.status = ' . (int)KB_STATUS_FREIGEGEBEN . ') ';
}

function kb_budget_hinweis(bool $inklNichtFreigegeben): string
{
    return $inklNichtFreigegeben
        ? 'Budget: inkl. Elemente aus noch nicht freigegebenen Budgets (ohne abgelehnte)'
        : 'Budget: nur freigegebene Budgets und Elemente ohne Budgetzuordnung (ohne abgelehnte)';
}

// ==================== DB ====================

function kb_query(mysqli $db, string $sql, string $types = '', array $params = []): array
{
    $stmt = $db->prepare($sql);
    if ($stmt === false) {
        throw new RuntimeException('Prepare failed: ' . $db->error);
    }
    if ($types !== '') {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $rows;
}

/** Vergleichsschlüssel wie MySQL (Groß/Klein egal, Leerzeichen am Rand egal, NULL = ''). */
function kb_key(...$teile): string
{
    return implode("\x1F", array_map(static function ($t) {
        $s = trim((string)$t);
        return function_exists('mb_strtolower') ? mb_strtolower($s, 'UTF-8') : strtolower($s);
    }, $teile));
}

function kb_projekt_info(mysqli $db, int $projectID): array
{
    $rows = kb_query($db, "
        SELECT p.Projektname, p.Preisbasis, pp.Bezeichnung
        FROM tabelle_projekte p
        INNER JOIN tabelle_planungsphasen pp
            ON pp.idTABELLE_Planungsphasen = p.TABELLE_Planungsphasen_idTABELLE_Planungsphasen
        WHERE p.idTABELLE_Projekte = ?", 'i', [$projectID]);
    return $rows[0] ?? ['Projektname' => '', 'Preisbasis' => '', 'Bezeichnung' => ''];
}

/** Alle Gewerke des Auftraggeberkatalogs (für die Legende). */
function kb_gewerke_katalog(mysqli $db, int $projectID): array
{
    return kb_query($db, "
        SELECT g.Gewerke_Nr, g.Bezeichnung
        FROM tabelle_auftraggeber_gewerke g
        INNER JOIN tabelle_projekte p
            ON p.TABELLE_Auftraggeber_Codes_idTABELLE_Auftraggeber_Codes = g.TABELLE_Auftraggeber_Codes_idTABELLE_Auftraggeber_Codes
        WHERE p.idTABELLE_Projekte = ?
        ORDER BY g.Gewerke_Nr", 'i', [$projectID]);
}

/** Gewerke-Nummern, die im Projekt tatsächlich verwendet werden (= Tabellenspalten). */
function kb_gewerke_im_projekt(mysqli $db, int $projectID, bool $inkl): array
{
    $rows = kb_query($db, "
        SELECT g.Gewerke_Nr
        " . KB_FROM_BASIS . "
        WHERE r.tabelle_projekte_idTABELLE_Projekte = ?
          AND g.Gewerke_Nr IS NOT NULL
          " . kb_budget_where($inkl) . "
        GROUP BY g.Gewerke_Nr
        ORDER BY g.Gewerke_Nr", 'i', [$projectID]);
    return array_map(static fn($r) => (string)$r['Gewerke_Nr'], $rows);
}

/**
 * Kosten (PP/PPNeu/PPBestand) je Gruppe und Gewerk in EINER Abfrage.
 * $gruppenSpalten: fest im Code hinterlegte SQL-Ausdrücke, z.B. ['r.`Raumbereich Nutzer`', 'r.Geschoss'].
 * Rückgabe: [kb_key(gruppenwerte...)][Gewerke_Nr] => ['PP' => .., 'PPNeu' => .., 'PPBestand' => ..]
 */
function kb_kosten_je_gewerk(mysqli $db, int $projectID, bool $inkl, array $gruppenSpalten): array
{
    $select = [];
    foreach ($gruppenSpalten as $i => $spalte) {
        $select[] = "$spalte AS grp$i";
    }
    $sql = "SELECT " . implode(', ', $select) . ", g.Gewerke_Nr, " . KB_SELECT_KOSTEN
        . KB_FROM_BASIS . KB_JOIN_KOSTEN . "
        WHERE r.tabelle_projekte_idTABELLE_Projekte = ?
          AND rhe.Standort = 1
          " . kb_budget_where($inkl) . "
        GROUP BY " . implode(', ', $gruppenSpalten) . ", g.Gewerke_Nr";

    $daten = [];
    foreach (kb_query($db, $sql, 'i', [$projectID]) as $row) {
        $werte = [];
        foreach (array_keys($gruppenSpalten) as $i) {
            $werte[] = $row["grp$i"];
        }
        $daten[kb_key(...$werte)][(string)$row['Gewerke_Nr']] = $row;
    }
    return $daten;
}

// ==================== PDF-Bausteine ====================

function kb_seitenumbruch_noetig($pdf, float $hoehe): bool
{
    return $pdf->GetY() + $hoehe > $pdf->getPageHeight() - $pdf->getBreakMargin();
}

function kb_write_budget_hinweis($pdf, bool $inkl, float $breite): void
{
    $pdf->SetFont('helvetica', 'I', 8);
    if ($inkl) {
        $pdf->SetTextColor(190, 0, 0);
    }
    $pdf->MultiCell($breite, 5, kb_budget_hinweis($inkl), '', 'L', 0, 0);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Ln();
}

/**
 * Projekt / Projektphase / Preisbasis / Budgethinweis (linke Spalte).
 * Rückgabe: x/y für die rechte Spalte (Legenden) und yEnde der linken Spalte.
 */
function kb_write_projektkopf($pdf, array $projekt, bool $inkl, float $breite): array
{
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->MultiCell($breite, 6, "Projekt: " . $projekt['Projektname'], '', 'L', 0, 0);
    $rechts = ['x' => $pdf->GetX(), 'y' => $pdf->GetY()];
    $pdf->Ln();
    $pdf->MultiCell($breite, 6, "Projektphase: " . $projekt['Bezeichnung'], '', 'L', 0, 0);
    $pdf->Ln();
    $pdf->MultiCell($breite, 6, "Preisbasis: " . $projekt['Preisbasis'], '', 'L', 0, 0);
    $pdf->Ln();
    kb_write_budget_hinweis($pdf, $inkl, $breite);
    $rechts['yEnde'] = $pdf->GetY();
    return $rechts;
}

/** Legende (Titel + Einträge in n Spalten) ab Position x/y. Rückgabe: y danach. */
function kb_write_legende($pdf, string $titel, array $eintraege, float $x, float $y, int $spalten = 1, float $wSpalte = 80): float
{
    $pdf->setXY($x, $y);
    $pdf->SetFont('helvetica', 'B', 7);
    $pdf->MultiCell($wSpalte, '', $titel, '', 'L', 0, 0);
    $pdf->Ln();
    $pdf->SetFont('helvetica', 'I', 7);
    $i = 0;
    foreach ($eintraege as $text) {
        if ($i % $spalten === 0) {
            $pdf->setX($x);
        }
        $pdf->MultiCell($wSpalte, '', $text, '', 'L', 0, 0);
        if ($i % $spalten === $spalten - 1) {
            $pdf->Ln();
        }
        $i++;
    }
    if ($i % $spalten !== 0) {
        $pdf->Ln();
    }
    return $pdf->GetY();
}

function kb_write_gewerke_legende($pdf, array $katalog, float $x, float $y, int $spalten = 1, float $wSpalte = 80): float
{
    $eintraege = array_map(static fn($g) => $g['Gewerke_Nr'] . "-" . $g['Bezeichnung'], $katalog);
    return kb_write_legende($pdf, "Gewerke: ", $eintraege, $x, $y, $spalten, $wSpalte);
}

function kb_write_tabellenkopf($pdf, string $titel1, string $titel2, array $gewerkeNr, float $w1 = 50, float $w2 = 20, float $wCol = 25): void
{
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->MultiCell($w1, 6, $titel1, 'B', 'L', 0, 0);
    $pdf->MultiCell($w2, 6, $titel2, 'B', 'L', 0, 0);
    foreach ($gewerkeNr as $nr) {
        $pdf->MultiCell($wCol, 6, $nr, 'B', 'R', 0, 0);
    }
    $pdf->MultiCell($wCol, 6, "Gesamt", 'B', 'R', 0, 0);
    $pdf->Ln();
}

/**
 * Drei Zeilen (Gesamt / davon Neu / davon Bestand) mit Gewerkespalten und Zeilensumme.
 * $zeilenKopf: je Zeile eine Liste von [breite, text, align] für die Spalten vor den Gewerken.
 * $summen (optional): wird je Gewerk und Kostenart aufaddiert.
 */
function kb_write_kosten_zeilen($pdf, array $zeilenKopf, array $gewerkeNr, array $kosten, ?array &$summen = null,
                                $fill = false, array $stil = ['', 'I', 'I'], array $groesse = [8, 6, 6],
                                array $rahmen = [0, 0, 0], float $wCol = 25): void
{
    foreach (KB_KOSTENARTEN as $i => $art) {
        $pdf->SetFont('helvetica', $stil[$i], $groesse[$i]);
        foreach ($zeilenKopf[$i] as [$breite, $text, $align]) {
            $pdf->MultiCell($breite, 4, $text, $rahmen[$i], $align, $fill, 0);
        }
        $zeilenSumme = 0.0;
        foreach ($gewerkeNr as $nr) {
            $wert = (float)($kosten[$nr][$art] ?? 0);
            $pdf->MultiCell($wCol, 4, format_money_report($wert), $rahmen[$i], 'R', $fill, 0);
            $zeilenSumme += $wert;
            if ($summen !== null) {
                $summen[$nr][$art] = ($summen[$nr][$art] ?? 0) + $wert;
            }
        }
        $pdf->MultiCell($wCol, 4, format_money_report($zeilenSumme), $rahmen[$i], 'R', $fill, 0);
        $pdf->Ln();
    }
}

/** Standard-Block: Bezeichnung | Zusatz | Gewerke... | Gesamt (+ davon Neu / Bestand) */
function kb_write_kosten_block($pdf, string $bezeichnung, string $zusatz, array $gewerkeNr, array $kosten,
                               array &$summen, $fill, float $w1 = 50, float $w2 = 20): void
{
    kb_write_kosten_zeilen($pdf, [
        [[$w1, $bezeichnung, 'L'], [$w2, $zusatz, 'L']],
        [[$w1, 'davon Neu', 'R'], [$w2, '', 'L']],
        [[$w1, 'davon Bestand', 'R'], [$w2, '', 'L']],
    ], $gewerkeNr, $kosten, $summen, $fill);
}

function kb_write_summen_block($pdf, string $label, array $gewerkeNr, array $summen, float $w1 = 50, float $w2 = 20): void
{
    $keine = null;
    kb_write_kosten_zeilen($pdf, [
        [[$w1, $label, 'L'], [$w2, '', 'R']],
        [[$w1, 'davon Neu', 'R'], [$w2, '', 'L']],
        [[$w1, 'davon Bestand', 'R'], [$w2, '', 'L']],
    ], $gewerkeNr, $summen, $keine, false, ['B', 'BI', 'BI'], [8, 6, 6], ['T', 0, 0]);
}

/**
 * Gewerk/GHG-Auflistung inkl. Zwischensummen je Gewerk und GESAMT-Zeile.
 * $zeilen: sortiert nach Gewerke_Nr, GHG; Felder Gewerke_Nr, GewerkBezeichnung, GHG, GHGBezeichnung, PP, PPNeu, PPBestand
 * $wLabel + $wKosten = 120 (Breite bis inkl. Spalte "Kosten").
 */
function kb_write_ghg_bericht($pdf, array $zeilen, float $wLabel, float $wKosten, float $einzug = 0): void
{
    $w = 30;
    $leer = ['PP' => 0.0, 'PPNeu' => 0.0, 'PPBestand' => 0.0];
    $gesamt = $leer;
    $gewerk = $leer;
    $aktuell = null;

    $zwischensumme = static function () use ($pdf, &$gewerk, &$gesamt, $leer, $wLabel, $wKosten, $w) {
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->MultiCell($wLabel + $wKosten, 4, format_money_report($gewerk['PP']), 'T', 'R', 0, 0);
        $pdf->MultiCell($w, 4, format_money_report($gewerk['PPNeu']), 'T', 'R', 0, 0);
        $pdf->MultiCell($w, 4, format_money_report($gewerk['PPBestand']), 'T', 'R', 0, 0);
        $pdf->Ln();
        $pdf->Ln();
        foreach (KB_KOSTENARTEN as $art) {
            $gesamt[$art] += $gewerk[$art];
        }
        $gewerk = $leer;
    };

    foreach ($zeilen as $z) {
        $nr = (string)($z['Gewerke_Nr'] ?? '');
        if ($aktuell === null || $nr !== $aktuell) {
            if ($aktuell !== null) {
                $zwischensumme();
            }
            if (kb_seitenumbruch_noetig($pdf, 16)) {
                $pdf->AddPage();
            }
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->MultiCell($wLabel, 6, trim("Gewerk " . $nr . " " . ($z['GewerkBezeichnung'] ?? '')), 'B', 'L', 0, 0);
            $pdf->SetFont('helvetica', '', 10);
            $pdf->MultiCell($wKosten, 6, "Kosten", 'B', 'R', 0, 0);
            $pdf->MultiCell($w, 6, "davon Neu", 'B', 'R', 0, 0);
            $pdf->MultiCell($w, 6, "davon Bestand", 'B', 'R', 0, 0);
            $pdf->Ln();
            $aktuell = $nr;
        }

        $pdf->SetFont('helvetica', 'I', 8);
        if ($einzug > 0) {
            $pdf->MultiCell($einzug, 5, '', '', 'L', 0, 0);
        }
        $label = empty($z['GHG'])
            ? "ohne Zuteilung: "
            : "GHG: " . $z['GHG'] . " " . ($z['GHGBezeichnung'] ?? '');
        $pdf->MultiCell($wLabel - $einzug, 5, $label, '', 'L', 0, 0);
        $pdf->MultiCell($wKosten, 5, format_money_report((float)$z['PP']), 0, 'R', 0, 0);
        $pdf->MultiCell($w, 5, format_money_report((float)$z['PPNeu']), 0, 'R', 0, 0);
        $pdf->MultiCell($w, 5, format_money_report((float)$z['PPBestand']), 0, 'R', 0, 0);
        $pdf->Ln();

        foreach (KB_KOSTENARTEN as $art) {
            $gewerk[$art] += (float)$z[$art];
        }
    }
    if ($aktuell !== null) {
        $zwischensumme();
    }

    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->MultiCell(90, 4, "GESAMT: ", 'T', 'L', 0, 0);
    $pdf->MultiCell(30, 4, format_money_report($gesamt['PP']), 'T', 'R', 0, 0);
    $pdf->MultiCell(30, 4, format_money_report($gesamt['PPNeu']), 'T', 'R', 0, 0);
    $pdf->MultiCell(30, 4, format_money_report($gesamt['PPBestand']), 'T', 'R', 0, 0);
    $pdf->Ln();
}

function kb_pdf_ausgeben($pdf, string $dateiname): void
{
    if (ob_get_level() > 0) {
        ob_end_clean();
    }
    $pdf->Output(getFileName($dateiname), 'I');
}