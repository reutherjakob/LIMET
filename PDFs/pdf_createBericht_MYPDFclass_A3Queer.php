<?php

session_start();
require_once '../utils/_utils.php';
check_login();
require_once('../TCPDF-main/TCPDF-main/tcpdf.php');
include "pdf_createBericht_LOGO.php";

class MYPDF extends TCPDF
{
    public function Header()
    {
        if ($this->numpages > 1) {
            get_header_logo($this);
            $this->SetFont('helvetica', '', 8);
            $bezeichnung = ($_SESSION["projektArt"] === '1')
                ? 'Labortechnische'
                : 'Medizintechnische';

            if ($_SESSION["projectPlanungsphase"] === "Vorentwurf") {
                $this->Cell(0, 0, $bezeichnung . ' Vorbemessungsangaben', 0, false, 'R', 0, '', 0, false, 'B', 'B');
            } else {
                $this->Cell(0, 0, $bezeichnung . ' Bauangaben', 0, false, 'R', 0, '', 0, false, 'B', 'B');
            }
            $this->Ln();
            $this->Cell(0, 0, '', 'B', 1, 'L');

        } else {

            $mysqli = utils_connect_sql();
            $roomIDs = filter_input(INPUT_GET, 'roomID');
            $teile = explode(",", $roomIDs);
            $sql = "SELECT tabelle_projekte.Projektname, tabelle_planungsphasen.Bezeichnung,
                    tabelle_räume.`Raumbereich Nutzer`, tabelle_räume.Bauabschnitt, tabelle_räume.Geschoss
                FROM tabelle_räume INNER JOIN (tabelle_planungsphasen INNER JOIN tabelle_projekte ON tabelle_planungsphasen.idTABELLE_Planungsphasen = tabelle_projekte.TABELLE_Planungsphasen_idTABELLE_Planungsphasen) ON tabelle_räume.tabelle_projekte_idTABELLE_Projekte = tabelle_projekte.idTABELLE_Projekte ";
            $i = 0;
            foreach ($teile as $valueOfRoomID) {
                if ($i == 0) {
                    $sql = $sql . "WHERE tabelle_räume.idTABELLE_Räume=" . $valueOfRoomID . " ";
                } else {
                    $sql = $sql . "OR tabelle_räume.idTABELLE_Räume=" . $valueOfRoomID . " ";
                }
                $i++;
            }
            $sql = $sql . "GROUP BY tabelle_projekte.Projektname, tabelle_planungsphasen.Bezeichnung, tabelle_räume.`Raumbereich Nutzer`, tabelle_räume.Bauabschnitt, tabelle_räume.Geschoss ORDER BY tabelle_räume.Bauabschnitt, tabelle_räume.`Raumbereich Nutzer`;";
            $result = $mysqli->query($sql);
            $raumInfos = array();
            $raumInfosCounter = 0;
            while ($row = $result->fetch_assoc()) {
                $raumInfos[$raumInfosCounter]['Projektname'] = $row['Projektname'];
                $raumInfos[$raumInfosCounter]['Planungsphase'] = $row['Bezeichnung'];
                $raumInfos[$raumInfosCounter]['Raumbereich'] = $row['Raumbereich Nutzer'];
                $raumInfos[$raumInfosCounter]['Bauabschnitt'] = $row['Bauabschnitt'];
                $raumInfos[$raumInfosCounter]['Geschoss'] = $row['Geschoss'];
                $raumInfosCounter = $raumInfosCounter + 1;
            }
            $mysqli->close();
            $this->SetFont('helvetica', 'B', 15);
            $this->SetY(50);
            $this->Cell(0, 0, "" . $raumInfos[0]['Projektname'], 0, false, 'L', 0, '', 0, false, 'B', 'B');
            $this->Ln();
            $this->Cell(0, 0, $raumInfos[0]['Planungsphase'], 0, false, 'L', 0, '', 0, false, 'B', 'B');
            $this->Ln();
            $this->Ln();

            $bezeichnung = ($_SESSION["projektArt"] === '1')
                ? 'Labortechnische'
                : 'Medizintechnische';

            if ($_SESSION["projectPlanungsphase"] === "Vorentwurf") {
                $this->Cell(0, 0, $bezeichnung . ' Vorbemessungsangaben', 0, false, 'L', 0, '', 0, false, 'B', 'B');
            } else {
                $this->Cell(0, 0, $bezeichnung . ' Bauangaben', 0, false, 'L', 0, '', 0, false, 'B', 'B');
            }
            $this->Ln();
            $this->Ln();

            if ($_SESSION["projectID"] === '95') {
                $this->Cell(0, 0, 'Funktionsbereiche: ', 0, false, 'L', 0, '', 0, false, 'B', 'B');
                $this->Ln();

// Geschoss-Sortierung: UG unten -> OG oben (UG2 vor UG1)
                $geschossRank = static function ($g) {
                    $g = strtoupper(trim((string)$g));
                    if ($g === '' || $g === '—') return 9999;
                    if (preg_match('/UG\s*(\d*)/', $g, $m) || preg_match('/(\d+)\s*UG/', $g, $m)) {
                        return -100 - ($m[1] !== '' ? (int)$m[1] : 1);
                    }
                    if (preg_match('/^(EG|E|0)$/', $g)) return 0;
                    if (preg_match('/OG\s*(\d*)/', $g, $m) || preg_match('/(\d+)\s*OG/', $g, $m)) {
                        return 100 + ($m[1] !== '' ? (int)$m[1] : 1);
                    }
                    if (preg_match('/^DG/', $g)) return 1000;
                    return 5000; // unbekannt -> ans Ende
                };

// Struktur aufbauen: Trakt -> Geschoss -> [Bereiche]
                // Struktur aufbauen: Trakt -> Geschoss -> [Bereiche]
                $struktur = [];
                foreach ($raumInfos as $ri) {
                    $bereich = trim((string)$ri['Raumbereich']);
                    if ($bereich === '') continue;
                    $trakt = trim((string)$ri['Bauabschnitt']) ?: '—';
                    $geschoss = trim((string)$ri['Geschoss']) ?: '—';
                    $struktur[$trakt][$geschoss][] = $bereich;
                }
                ksort($struktur, SORT_NATURAL | SORT_FLAG_CASE);

// Spalten-Layout: jeder Trakt beginnt in EINER eigenen Spalte.
// Läuft ein Trakt über die Seitenhöhe, fließt nur dieser Trakt in die
// nächste Spalte weiter (=> Trakt D belegt 2 Spalten, gesamt 5).
                $lineHeight = 6;
                $colWidth = 72;   // 5 Spalten passen auf A3 quer
                $colGap = 5;
                $startY = $this->GetY();
                $startX = $this->GetX();
                $bottomLimit = $this->getPageHeight() - 57;

                $colX = $startX;
                $curY = $startY;
                $maxY = $startY;
                $firstTrakt = true;

                foreach ($struktur as $trakt => $geschosse) {
                    // Neuer Trakt -> neue Spalte (außer beim allerersten)
                    if (!$firstTrakt) {
                        $colX += $colWidth + $colGap;
                        $curY = $startY;
                    }
                    $firstTrakt = false;

                    // Trakt-Überschrift (leeren "—"-Trakt ohne Kopf lassen)
                    if ($trakt !== '—') {
                        $this->SetFont('helvetica', 'B', 13);
                        $this->SetXY($colX, $curY);
                        $this->MultiCell($colWidth, $lineHeight, 'Trakt ' . $trakt, 0, 'L', 0, 1);
                        $curY = $this->GetY();
                        if ($curY > $maxY) $maxY = $curY;
                    }

                    uksort($geschosse, static function ($a, $b) use ($geschossRank) {
                        return $geschossRank($a) <=> $geschossRank($b);
                    });

                    // Renderliste dieses Trakts: je Geschoss ein Kopf + seine Bereiche
                    $block = [];
                    foreach ($geschosse as $geschoss => $bereiche) {
                        $block[] = ['type' => 'geschoss', 'text' => $geschoss];
                        foreach (array_values(array_unique($bereiche)) as $b) {
                            $block[] = ['type' => 'bereich', 'text' => $b];
                        }
                    }

                    foreach ($block as $item) {
                        if ($item['type'] === 'geschoss') {
                            $this->SetFont('helvetica', 'B', 11);
                            $indent = 4;
                            $spaceBefore = ($curY > $startY) ? 1 : 0;
                        } else {
                            $this->SetFont('helvetica', '', 11);
                            $indent = 8;
                            $spaceBefore = 0;
                        }

                        $w = $colWidth - $indent;
                        $entryHeight = $this->getNumLines($item['text'], $w) * $lineHeight;

                        // Geschoss-Kopf nicht allein am Spaltenende -> Folgezeile mit einplanen
                        $needed = $entryHeight + $spaceBefore + ($item['type'] === 'geschoss' ? $lineHeight : 0);
                        if (($curY + $needed) > $bottomLimit && $curY > $startY) {
                            $colX += $colWidth + $colGap;   // NUR dieser Trakt fließt weiter
                            $curY = $startY + 8;
                            $spaceBefore = 0;
                        }

                        $curY += $spaceBefore;
                        $this->SetXY($colX + $indent, $curY);
                        $this->MultiCell($w, $lineHeight, $item['text'], 0, 'L', 0, 1);
                        $curY = $this->GetY();
                        if ($curY > $maxY) $maxY = $curY;
                    }
                }


            } else {
                $this->Cell(0, 0, 'Funktionsstellen: ', 0, false, 'L', 0, '', 0, false, 'B', 'B');
                $this->Ln();
                $raumInfosCounter = 0;
                $funktionsStellen = "";
                $this->SetFont('helvetica', '', 12);

                $lineHeight = 6;    // mm pro Zeile (wie bisher)
                $colWidth = 150;  // Spaltenbreite (wie bisher)
                $colGap = 15;   // Abstand zwischen den beiden Spalten
                $startY = $this->GetY();
                $startX = $this->GetX();
                $bottomLimit = $this->getPageHeight() - 55; // 5 cm vor Seitenende

                $colX = $startX;
                $curY = $startY;
                $maxY = $startY;     // tiefster erreichter Punkt (für alles, was danach kommt)

                foreach ($raumInfos as $valueOfRaumInfos) {
                    $text = $valueOfRaumInfos['Raumbereich'];
                    // Höhe des Eintrags – berücksichtigt Umbruch innerhalb der Spaltenbreite
                    $entryHeight = $this->getNumLines($text, $colWidth) * $lineHeight;

                    // Würde der Eintrag die 5-cm-Grenze überschreiten -> neue Spalte rechts
                    if (($curY + $entryHeight) > $bottomLimit && $curY > $startY) {
                        $colX += $colWidth + $colGap;
                        $curY = $startY;
                    }

                    $this->SetXY($colX, $curY);
                    $this->MultiCell($colWidth, $lineHeight, $text, 0, 'L', 0, 1);

                    $curY = $this->GetY();
                    if ($curY > $maxY) {
                        $maxY = $curY;
                    }
                }
            }

// unterhalb der längeren Spalte weitermachen
            $this->SetY($maxY);
            $this->Ln();
            $this->Ln();

            $this->SetFont('helvetica', '', 12);
            $this->MultiCell(150, 6, $funktionsStellen, 0, 'L', 0, 0);
            $this->Ln();
            $this->Ln();
            $this->Ln();
            $this->Ln();
            $this->SetFont('helvetica', '', 10);

            if ($_SESSION["projectID"] != '95') {
                if (isset($_SESSION["PDFdatum"]) && $_SESSION["PDFdatum"] != null) {
                    $this->Cell(0, 0, "Stand: " . $_SESSION["PDFdatum"], 0, false, 'L', 0, '', 0, false, 'T', 'M');
                } else {
                    $this->Cell(0, 0, "Stand: " . date('Y-m-d'), 0, false, 'L', 0, '', 0, false, 'T', 'M');
                }
            }

            $this->Ln();
            $dateFromURL = getValidatedDateFromURL();
            $currentDate = date('Y-m-d');
            $futureDate = new DateTime($dateFromURL) > new DateTime($currentDate);

            if ($dateFromURL !== $currentDate && !$futureDate) {
                $this->Cell(0, 0, "Änderungen markiert ab: " . $dateFromURL, 0, false, 'L', 0, '', 0, false, 'T', 'M');
            }

            $this->SetFont('helvetica', '', 6);

            get_titelblatt_logo($this, "A3");

            $Vorentwurf = " Im Vorentwurf sind die raumweisen elektrischen Leitungsangaben je Netzart ohne Gleichzeitigkeit angegeben. Die Werte stellen die Summe der Nennleistungen der im Raum geplanten medizin- und labortechnischen Geräte inkl. einer Auslegungsreserve dar. Diese Auslegungsreserve ist erforderlich, um beispielsweise Geräte zu berücksichtigen, welche nicht im Raum verortet sind, aber dort genutzt werden können. Detailliertere Angaben zu Großgeräten (Röntgenanlagen, CT, MRT etc.) erfolgen stets gesondert.";
            $Entwurf = " Die elektrischen Leistungsangaben je Netzart, die aus der Verwendung der medizin- und labortechnischen Geräte resultieren, werden aus der Summe der einzelnen Geräte/Element-Nennleistungen unter Berücksichtigung der Gleichzeitigkeit je Element berechnet. Die Differenz der angeführten Leistungssumme zu den Vorbemessungsangaben aus  dem Vorentwurf ist die verbleibende Auslegungsreserve je Raum.";
            $Disclaimer = "Die nachfolgenden medizin- und labortechnischen Angaben beziehen sich nur auf diejenigen medizin- und labortechnisch-relevanten Räume, die seitens der Planung bearbeitet werden. Die Angaben dienen als Grundlage für die Fachplaner Architektur, Elektrotechnik, HKLS, Medgas & Statik. Neben den aufgelisteten Bemessungsangaben je Fachbereich werden die medizin- und labortechnischen Elemente eines Raumes in Listenform angeführt. Diese sind ebenfalls als Planungsgrundlage heranzuziehen. ";
            $Disclaimer2 = "Angaben zur Abdunkelung leiten sich aus der medizintechnischen/labortechnischen Ausstattung und medizinischen Verwendung ab. Diese bilden nicht die aus anderen Gründen erwünschte Abdunkelung bzw. den ggf. erforderlichen Blendschutz ab.";
            $Legende =  "";
            if ($_SESSION["projectID"] === '95') {
                $Disclaimer = "Die nachfolgenden labortechnischen Angaben beziehen sich nur auf labortechnisch-relevante Räume, die seitens der Planung bearbeitet werden. Die Angaben dienen als Grundlage für die Fachplaner Architektur, Elektrotechnik, HKLS, Medgas & Statik. Diese Bemessungsangaben und labortechnischen Elemente je Raumes sind als Planungsgrundlage heranzuziehen. ";
                $Disclaimer2 = "";
                $Vorentwurf = " Im Vorentwurf sind die raumweisen elektrischen Leitungsangaben je Netzart ohne Gleichzeitigkeit angegeben. Die Werte wurden aus den raumtypenspezifischen flächenbezogen Leistungsbedarf und der jeweiligen Raumfläcghe errechnet.";
                $Legende =  "\n Legende: n.E. = nach Erforderniss; ";
            }

            $this->SetFont('helvetica', '', 10);
            if ($_SESSION["projectPlanungsphase"] === "Vorentwurf") {
                $Disclaimer = $Disclaimer . $Vorentwurf . $Disclaimer2. $Legende;
            } else if ($_SESSION["projectPlanungsphase"] === "Entwurf") {
                $Disclaimer = $Disclaimer . $Entwurf . $Disclaimer2. $Legende;
            }
            $height = $this->getStringHeight(390, $Disclaimer, 0, 'J', 0, 6);
            $this->SetY(275 - $height);
            $this->MultiCell(390, 6, $Disclaimer, 0, 'L', 0, 0);
        }
    }

    // Page footer
    public function Footer()
    {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->cell(0, 0, '', 'T', 0, 'L');
        $this->Ln();
        $tDate = date('Y-m-d');
        if (isset($_SESSION["PDFdatum"]) && $_SESSION["PDFdatum"] != null) {
            $this->Cell(0, 0, $_SESSION["PDFdatum"], 0, false, 'L', 0, '', 0, false, 'T', 'M');
        } else {
            $this->Cell(0, 0, $tDate, 0, false, 'L', 0, '', 0, false, 'T', 'M');
        }
        $this->Cell(0, 0, 'Seite ' . $this->getAliasNumPage() . ' von ' . $this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
    }
}
