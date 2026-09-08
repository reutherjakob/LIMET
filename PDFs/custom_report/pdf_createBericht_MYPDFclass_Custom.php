<?php
/**
 * ============================================================================
 *  MYPDF-Klasse für den CUSTOM-Bericht (format-bewusst: A3 quer / A4 hoch).
 * ============================================================================
 *
 *  Eigene Klasse, damit der bestehende A3-Queer-Bericht unangetastet bleibt.
 *  Das Seitenformat wird über $GLOBALS['CUSTOM_REPORT_FORMAT'] ('A3'|'A4')
 *  gesteuert; die Titelseiten- und Disclaimer-Breite ergibt sich dynamisch
 *  aus der tatsächlichen Seitenbreite.
 * ============================================================================
 */

session_start();
require_once '../../utils/_utils.php';
check_login();
include "../pdf_createBericht_LOGO.php";

class MYPDF extends TCPDF
{
    public function Header()
    {
        /** @noinspection PhpUndefinedConstantInspection */
        $fmt = $GLOBALS['CUSTOM_REPORT_FORMAT'] ?? 'A3';
        $contentW = $this->getPageWidth() - 2 * PDF_MARGIN_LEFT; // A3-L: 390 | A4-P: 180

        if ($this->numpages > 1) {
            // ---- laufende Kopfzeile auf Inhaltsseiten ----
            get_header_logo($this);
            $this->SetFont('helvetica', '', 8);
            if ($_SESSION["projectPlanungsphase"] === "Vorentwurf") {
                $this->Cell(0, 0, 'Medizintechnische Vorbemessungsangaben', 0, false, 'R', 0, '', 0, false, 'B', 'B');
            } else {
                $this->Cell(0, 0, 'Medizintechnische Bauangaben', 0, false, 'R', 0, '', 0, false, 'B', 'B');
            }
            $this->Ln();
            $this->cell(0, 0, '', 'B', 1, 'L');

        } else {
            // ---- Titelseite ----
            $mysqli = utils_connect_sql();
            $roomIDs = filter_input(INPUT_GET, 'roomID');
            $teile = explode(",", (string)$roomIDs);
            $sql = "SELECT tabelle_projekte.Projektname, tabelle_planungsphasen.Bezeichnung, tabelle_räume.`Raumbereich Nutzer`
                    FROM tabelle_räume INNER JOIN (tabelle_planungsphasen INNER JOIN tabelle_projekte ON tabelle_planungsphasen.idTABELLE_Planungsphasen = tabelle_projekte.TABELLE_Planungsphasen_idTABELLE_Planungsphasen) ON tabelle_räume.tabelle_projekte_idTABELLE_Projekte = tabelle_projekte.idTABELLE_Projekte ";
            $i = 0;
            foreach ($teile as $valueOfRoomID) {
                $valueOfRoomID = (int)$valueOfRoomID;
                $sql .= ($i == 0 ? "WHERE " : "OR ") . "tabelle_räume.idTABELLE_Räume=" . $valueOfRoomID . " ";
                $i++;
            }
            $sql .= "GROUP BY tabelle_projekte.Projektname, tabelle_planungsphasen.Bezeichnung, tabelle_räume.`Raumbereich Nutzer` ORDER BY tabelle_räume.`Raumbereich Nutzer`;";
            $result = $mysqli->query($sql);
            $raumInfos = array();
            while ($row = $result->fetch_assoc()) {
                $raumInfos[] = [
                    'Projektname' => $row['Projektname'],
                    'Planungsphase' => $row['Bezeichnung'],
                    'Raumbereich' => $row['Raumbereich Nutzer'],
                ];
            }
            $mysqli->close();

            $this->SetFont('helvetica', 'B', 15);
            $this->SetY(50);
            $this->Cell(0, 0, "" . ($raumInfos[0]['Projektname'] ?? ''), 0, false, 'L', 0, '', 0, false, 'B', 'B');
            $this->Ln();
            $this->Cell(0, 0, ($raumInfos[0]['Planungsphase'] ?? ''), 0, false, 'L', 0, '', 0, false, 'B', 'B');
            $this->Ln();
            $this->Ln();
            if ($_SESSION["projectPlanungsphase"] === "Vorentwurf") {
                $this->Cell(0, 0, 'Medizintechnische Vorbemessungsangaben', 0, false, 'L', 0, '', 0, false, 'B', 'B');
            } else {
                $this->Cell(0, 0, 'Medizintechnische Bauangaben', 0, false, 'L', 0, '', 0, false, 'B', 'B');
            }
            $this->Ln();
            $this->Ln();
            $this->Cell(0, 0, 'Funktionsstellen: ', 0, false, 'L', 0, '', 0, false, 'B', 'B');
            $this->Ln();

            $funktionsStellen = "";
            foreach ($raumInfos as $idx => $info) {
                if ($idx > 0) {
                    $funktionsStellen .= "\n";
                }
                $funktionsStellen .= $info['Raumbereich'];
            }
            $this->SetFont('helvetica', '', 12);
            $this->MultiCell(min(150, $contentW), 6, $funktionsStellen, 0, 'L', 0, 0);
            $this->Ln();
            $this->Ln();

            $this->SetFont('helvetica', '', 10);
            if (isset($_SESSION["PDFdatum"]) && $_SESSION["PDFdatum"] != null) {
                $this->Cell(0, 0, "Stand: " . $_SESSION["PDFdatum"], 0, false, 'L', 0, '', 0, false, 'T', 'M');
            } else {
                $this->Cell(0, 0, "Stand: " . date('Y-m-d'), 0, false, 'L', 0, '', 0, false, 'T', 'M');
            }
            $this->Ln();

            $dateFromURL = getValidatedDateFromURL();
            $currentDate = date('Y-m-d');
            $futureDate = $dateFromURL && (new DateTime($dateFromURL) > new DateTime($currentDate));
            if ($dateFromURL && $dateFromURL !== $currentDate && !$futureDate) {
                $this->Cell(0, 0, "Änderungen markiert ab: " . $dateFromURL, 0, false, 'L', 0, '', 0, false, 'T', 'M');
            }

            $this->SetFont('helvetica', '', 6);
            // Hinweis: erwartet "A3"/"A4" als Format. Falls dein LOGO-Helper kein
            // "A4" kennt, hier ggf. fix "A3" übergeben oder Helper erweitern.
            get_titelblatt_logo($this, $fmt);

            $Vorentwurf = " Im Vorentwurf sind die raumweisen elektrischen Leitungsangaben je Netzart ohne Gleichzeitigkeit angegeben. Die Werte stellen die Summe der Nennleistungen der im Raum geplanten medizin- und labortechnischen Geräte inkl. einer Auslegungsreserve dar. Diese Auslegungsreserve ist erforderlich, um beispielsweise Geräte zu berücksichtigen, welche nicht im Raum verortet sind, aber dort genutzt werden können. Detailliertere Angaben zu Großgeräten (Röntgenanlagen, CT, MRT etc.) erfolgen stets gesondert.";
            $Entwurf = " Die elektrischen Leistungsangaben je Netzart, die aus der Verwendung der medizin- und labortechnischen Geräte resultieren, werden aus der Summe der einzelnen Geräte/Element-Nennleistungen unter Berücksichtigung der Gleichzeitigkeit je Element berechnet. Die Differenz der angeführten Leistungssumme zu den Vorbemessungsangaben aus dem Vorentwurf ist die verbleibende Auslegungsreserve je Raum.";
            $Disclaimer = "Die nachfolgenden medizin- und labortechnischen Angaben beziehen sich nur auf diejenigen medizin- und labortechnisch-relevanten Räume, die seitens der Planung bearbeitet werden. Die Angaben dienen als Grundlage für die Fachplaner Architektur, Elektrotechnik, HKLS, Medgas & Statik. Neben den aufgelisteten Bemessungsangaben je Fachbereich werden die medizin- und labortechnischen Elemente eines Raumes in Listenform angeführt. Diese sind ebenfalls als Planungsgrundlage heranzuziehen. ";
            $Disclaimer2 = "Angaben zur Abdunkelung leiten sich aus der medizintechnischen/labortechnischen Ausstattung und medizinischen Verwendung ab. Diese bilden nicht die aus anderen Gründen erwünschte Abdunkelung bzw. den ggf. erforderlichen Blendschutz ab.";

            $this->SetFont('helvetica', '', 10);
            if ($_SESSION["projectPlanungsphase"] === "Vorentwurf") {
                $Disclaimer .= $Vorentwurf . $Disclaimer2;
            } else if ($_SESSION["projectPlanungsphase"] === "Entwurf") {
                $Disclaimer .= $Entwurf . $Disclaimer2;
            }
            $height = $this->getStringHeight($contentW, $Disclaimer, 0, 'J', 0, 6);
            $this->SetY(275 - $height);
            $this->MultiCell($contentW, 6, $Disclaimer, 0, 'L', 0, 0);
        }
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
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