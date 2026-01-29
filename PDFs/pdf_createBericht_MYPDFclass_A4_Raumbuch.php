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
            $this->cell(0, 0, '', 0, 0, 'L');
            $this->Ln(1);
            if ($_SESSION["PDFTITEL"] != null) {
                $this->Cell(0, 0, $_SESSION["PDFTITEL"], 0, false, 'R', 0, '', 0, false, 'B', 'B');
            } else {
                $this->Cell(0, 0, 'Raumbuch', 0, false, 'R', 0, '', 0, false, 'B', 'B');
            }
            $this->Ln();
            if (isset($_SESSION["PDFHeaderSubtext"])) {
                if (!empty(str_replace(" ", "", $_SESSION["PDFHeaderSubtext"]))) {
                    $this->Cell(0, 0, $_SESSION["PDFHeaderSubtext"], '', false, 'R', 0, '', 0, false, 'B', 'B');
                    $this->Ln(1);
                }
            }

            if ($_SESSION["PDFTITELBLATT"] === "KHI Quest") {
                $this->Ln(5);
            }

            $this->cell(0, 0, '', 'B', 0, 'L');
            $this->Ln(1);
            $this->cell(0, 0, '', 'B', 0, 'L');
            $this->Ln(1);

        } else {

            $mysqli = utils_connect_sql();
            $roomIDs = filter_input(INPUT_GET, 'roomID');
            if (!empty($roomIDs)) {
                $teile = explode(",", $roomIDs);
            }
            $sql = "SELECT tabelle_projekte.Projektname,
                           tabelle_planungsphasen.Bezeichnung,
                           tabelle_räume.`Raumbereich Nutzer`
                    FROM tabelle_räume INNER JOIN
                            (tabelle_planungsphasen INNER JOIN
                            tabelle_projekte ON tabelle_planungsphasen.idTABELLE_Planungsphasen =
                            tabelle_projekte.TABELLE_Planungsphasen_idTABELLE_Planungsphasen) 
                    ON tabelle_räume.tabelle_projekte_idTABELLE_Projekte = tabelle_projekte.idTABELLE_Projekte ";
            $i = 0;

            if (isset($teile)) {
                foreach ($teile as $valueOfRoomID) {
                    if ($i == 0) {
                        $sql = $sql . "WHERE tabelle_räume.idTABELLE_Räume=" . $valueOfRoomID . " ";
                    } else {
                        $sql = $sql . "OR tabelle_räume.idTABELLE_Räume=" . $valueOfRoomID . " ";
                    }
                    $i++;
                }
                $sql = $sql . "GROUP BY tabelle_projekte.Projektname, tabelle_planungsphasen.Bezeichnung, tabelle_räume.`Raumbereich Nutzer` ORDER BY tabelle_räume.`Raumbereich Nutzer`;";
                $result = $mysqli->query($sql);
                $raumInfos = array();
                $raumInfosCounter = 0;
                while ($row = $result->fetch_assoc()) {
                    $raumInfos[$raumInfosCounter]['Projektname'] = $row['Projektname'];
                    $raumInfos[$raumInfosCounter]['Planungsphase'] = $row['Bezeichnung'];
                    $raumInfos[$raumInfosCounter]['Raumbereich'] = $row['Raumbereich Nutzer'];
                    $raumInfosCounter = $raumInfosCounter + 1;
                }
            }

            $mysqli->close();
            $this->SetFont('helvetica', 'B', 15);
            $this->SetY(50);

            $this->Cell(0, 0, str_replace(' ', '', $_SESSION["projectName"]), 0, false, 'L', 0, '', 0, false, 'B', 'B'); // "" . $raumInfos[0]['Projektname'],  $raumInfos[0]['Planungsphase']
            $this->Ln();
            $this->Cell(0, 0, $_SESSION["projectPlanungsphase"], 0, false, 'L', 0, '', 0, false, 'B', 'B');
            $this->Ln();
            $this->Ln();
            if (isset($_SESSION["PDFTITEL"]) && $_SESSION["PDFTITEL"] != null) {
                $this->Cell(80, 0, $_SESSION["PDFTITEL"], 0, false, 'L', 0, '', 0, false, 'B', 'B');
            } else {
                $this->Cell(0, 0, 'Medizintechnisches Raumbuch', 0, false, 'L', 0, '', 0, false, 'B', 'B');
            }
            $this->Ln();
            $this->Ln();
            $this->Ln();
            $this->Cell(0, 0, 'Funktionsstellen: ', 0, false, 'L', 0, '', 0, false, 'B', 'B');
            $this->Ln();
            $raumInfosCounter = 0;
            $funktionsStellen = "";
            foreach ($raumInfos as $valueOfRaumInfos) {
                if ($raumInfosCounter > 0) {
                    $funktionsStellen = $funktionsStellen . "\n";
                }
                $funktionsStellen = $funktionsStellen . $raumInfos[$raumInfosCounter]['Raumbereich'];
                $raumInfosCounter = $raumInfosCounter + 1;
            }
            $this->SetFont('helvetica', '', 12);
            $this->MultiCell(150, 6, $funktionsStellen, 0, 'L', 0, 0);
            $this->Ln();
            $this->Ln();

            // GET LOGO
            get_titelblatt_logo($this);

            $Vorentwurf = " Im Vorentwurf sind die raumweisen elektrischen Leitungsangaben je Netzart ohne Gleichzeitigkeit angegeben. Die Werte stellen die Summe der Nennleistungen der im Raum geplanten medizin- und labortechnischen Geräte inkl. einer Auslegungsreserve dar. Diese Auslegungsreserve ist erforderlich, um beispielsweise Geräte zu berücksichtigen, welche nicht im Raum verortet sind, aber dort genutzt werden können. Detailliertere Angaben zu Großgeräten (Röntgenanlagen, CT, MRT etc.) erfolgen stets gesondert.";
            $Entwurf = "Die elektrischen Leistungsangaben je Netzart, die aus der Verwendung der medizin- und labortechnischen Geräte resultieren, werden aus der Summe der einzelnen Geräte/Element-Nennleistungen unter Berücksichtigung der Gleichzeitigkeit je Element berechnet. Die Differenz der angeführten Leistungssumme zu den Vorbemessungsangaben aus  dem Vorentwurf ist die verbleibende Auslegungsreserve je Raum.";
            $Disclaimer = "Die nachfolgenden medizin- und labortechnischen Angaben beziehen sich nur auf diejenigen medizin- und labortechnisch-relevanten Räume, die seitens der Planung bearbeitet werden. Die Angaben dienen als Grundlage für die Fachplaner Architektur, Elektrotechnik, HKLS, Medgas & Statik. Neben den aufgelisteten Bemessungsangaben je Fachbereich werden die medizin- und labortechnischen Elemente eines Raumes in Listenform angeführt. Diese sind ebenfalls als Planungsgrundlage heranzuziehen. ";
            $Disclaimer2 = "Angaben zur Abdunkelung leiten sich aus der medizintechnischen/labortechnischen Ausstattung und medizinischen Verwendung ab. Diese bilden nicht die aus anderen Gründen erwünschte Abdunkelung bzw. den ggf. erforderlichen Blendschutz ab.";

            $this->SetFont('helvetica', '', 10);
            if ($_SESSION["projectPlanungsphase"] === "Vorentwurf") {
                $Disclaimer = $Disclaimer . $Vorentwurf . $Disclaimer2;
            } else if ($_SESSION["projectPlanungsphase"] === "Entwurf") {
                $Disclaimer = $Disclaimer . $Entwurf . $Disclaimer2;
            }
            $height = $this->getStringHeight(180, $Disclaimer, 0, 'J', 0, 6);
            $this->SetY(275 - $height);
            $this->MultiCell(180, 6, $Disclaimer, 0, 'L', 0, 0);

            $this->SetFont('helvetica', '', 6);
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
