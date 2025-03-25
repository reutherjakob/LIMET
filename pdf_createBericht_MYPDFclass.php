<?php
session_start();
require_once('TCPDF-main/TCPDF-main/tcpdf.php');

class MYPDF extends TCPDF
{
    public function Header()
    {
        if ($this->numpages > 1) {
            // Logo        
            if ($_SESSION["projectAusfuehrung"] === "MADER") {
                $image_file = 'Mader_Logo_neu.jpg';
                $this->Image($image_file, 15, 5, 40, 10, 'JPG', '', 'M', false, 300, '', false, false, 0, false, false, false);
            } else {
                if ($_SESSION["projectAusfuehrung"] === "LIMET") {
                    $image_file = 'LIMET_web.png';
                    $this->Image($image_file, 15, 5, 20, 10, 'PNG', '', 'M', false, 300, '', false, false, 0, false, false, false);
                } else {
                    $image_file = 'PERNTHALER_LOGO.png';
                    $this->Image($image_file, 15, 5, 30, 10, 'PNG', '', 'M', false, 300, '', false, false, 0, false, false, false);
                    $image_file = 'LIMET_web.png';
                    $this->Image($image_file, 50, 4, 20, 10, 'PNG', '', 'M', false, 300, '', false, false, 0, false, false, false);
                    $image_file = 'MADER_Logo.png';
                    $this->Image($image_file, 75, 5, 12, 10, 'PNG', '', 'M', false, 300, '', false, false, 0, false, false, false);
                }
            }
            $this->SetFont('helvetica', '', 8);
            // Title
            if ($_SESSION["projectPlanungsphase"] === "Vorentwurf") {
                $this->Cell(0, 0, 'Medizintechnische Vorbemessungsangaben', 0, false, 'R', 0, '', 0, false, 'B', 'B');
            } else {
                $this->Cell(0, 0, 'Medizintechnische Bauangaben', 0, false, 'R', 0, '', 0, false, 'B', 'B');
            }
            $this->Ln();
            $this->cell(0, 0, '', 'B', 1, 'L');
//            $this->Ln();
        } // Titelblatt
        else {

            $mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');
            if (!$mysqli->set_charset("utf8")) {
                printf("Error loading character set utf8: %s\n or Login timed out", $mysqli->error);
                exit();
            }
            $roomIDs = filter_input(INPUT_GET, 'roomID');
            $teile = explode(",", $roomIDs);
            $sql = "SELECT tabelle_projekte.Projektname, tabelle_planungsphasen.Bezeichnung, tabelle_räume.`Raumbereich Nutzer`
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

            $mysqli->close();
            // Set font
            $this->SetFont('helvetica', 'B', 15);
            // Title
            $this->SetY(50);
            $this->Cell(0, 0, "" . $raumInfos[0]['Projektname'], 0, false, 'L', 0, '', 0, false, 'B', 'B');
            $this->Ln();
            $this->Cell(0, 0, $raumInfos[0]['Planungsphase'], 0, false, 'L', 0, '', 0, false, 'B', 'B');
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
            //$this->Cell(0, 0, $funktionsStellen, 0, false, 'L', 0, '', 0, false, 'B', 'B');
            $this->Ln();
            $this->Ln();
            $this->SetFont('helvetica', '', 10);
            $this->Cell(0, 0, "Stand: " . date('Y-m-d'), 0, false, 'L', 0, '', 0, false, 'T', 'M');
            $this->Ln();
            $dateFromURL = getValidatedDateFromURL();
            $currentDate = date('Y-m-d');
            $futureDate = new DateTime($dateFromURL) > new DateTime($currentDate);

            if ($dateFromURL === $currentDate || $futureDate) {
            } else {
                $this->Cell(0, 0, "Änderungsverlauf bis: " . $dateFromURL, 0, false, 'L', 0, '', 0, false, 'T', 'M');
            }

            $this->SetFont('helvetica', '', 6);
            //LOGOS einfügen
            if ($_SESSION["projectAusfuehrung"] === "MADER") {
                $image_file = 'Mader_Logo_neu.jpg';
                $this->Image($image_file, 145, 40, 50, 15, 'JPG', '', 'M', false, 300, '', false, false, 0, false, false, false);
            } else {
                if ($_SESSION["projectAusfuehrung"] === "LIMET") {
                    $image_file = 'LIMET_web.png';
                    $this->Image($image_file, 110, 40, 30, 15, 'PNG', '', 'M', false, 300, '', false, false, 0, false, false, false);
                } else {
                    $image_file = 'PERNTHALER_LOGO.png';
                    $this->Image($image_file, 165, 45, 30, 10, 'PNG', '', 'M', false, 300, '', false, false, 0, false, false, false);
                    //$image_file = 'LIMET_web.png';
                    //$this->Image($image_file, 135, 42, 20, 10, 'PNG', '', 'M', false, 300, '', false, false, 0, false, false, false);
                    //$image_file = 'Mader_Logo_neu.jpg';
                    //$this->Image($image_file, 145, 41, 40, 20, 'JPG', '', 'M', false, 300, '', false, false, 0, false, false, false);
                    $this->SetY(60);
                    $this->SetX(110);
                    $this->Cell(0, 0, "DI Markus Pernthaler Architekt ZT GmbH", 0, false, 'R', 0, '', 0, false, 'B', 'B');
                    $this->Ln();
                    $this->Cell(0, 0, "Marienplatz 1", 0, false, 'R', 0, '', 0, false, 'B', 'B');
                    $this->Ln();
                    $this->Cell(0, 0, "8020 Graz", 0, false, 'R', 0, '', 0, false, 'B', 'B');
                    $this->Ln();
                    $this->Ln();
                    $this->Cell(0, 0, "tel: +43 (0)316 – 32 11 50", 0, false, 'R', 0, '', 0, false, 'B', 'B');
                    $this->Ln();
                    $this->Cell(0, 0, "fax: +43 (0)316 – 32 11 50 – 14", 0, false, 'R', 0, '', 0, false, 'B', 'B');
                    $this->Ln();
                    $this->Cell(0, 0, "e-mail: architekt@pernthaler.at", 0, false, 'R', 0, '', 0, false, 'B', 'B');
                    $this->Ln();
                    $this->Ln();
                    $this->Cell(0, 0, "UDI ATU 61879729", 0, false, 'R', 0, '', 0, false, 'B', 'B');
                    $this->Ln();
                    $this->Cell(0, 0, "IBAN: AT89 1100 0048 83871800", 0, false, 'R', 0, '', 0, false, 'B', 'B');
                    $this->Ln();
                    $this->Cell(0, 0, "BIC: BKAUATWW ", 0, false, 'R', 0, '', 0, false, 'B', 'B');
                }
            }
            // Deckblatt beenden

            $Vorentwurf = " Im Vorentwurf sind die raumweisen elektrischen Leitungsangaben je Netzart ohne Gleichzeitigkeit angegeben. Die Werte stellen die Summe der Nennleistungen der im Raum geplanten medizin- und labortechnischen Geräte inkl. einer Auslegungsreserve dar. Diese Auslegungsreserve ist erforderlich, um beispielsweise Geräte zu berücksichtigen, welche nicht im Raum verortet sind, aber dort genutzt werden können. Detailliertere Angaben zu Großgeräten (Röntgenanlagen, CT, MRT etc.) erfolgen stets gesondert.";
            $Entwurf = " Die elektrischen Leistungsangaben je Netzart, die aus der Verwendung der medizin- und labortechnischen Geräte resultiert, wird aus der Summe der einzelnen Geräte/Element-Nennleistungen unter Berücksichtigung der Gleichzeitigkeit je Element berechnet. Die Differenz der angeführten Leistungssumme zu den Vorbemessungsangaben aus  dem Vorentwurf ist die verbleibende Auslegungsreserve je Raum.";
            $Disclaimer = "Die nachfolgenden medizin- und labortechnischen Angaben beziehen sich nur auf diejenigen medizin- und labortechnisch-relevante Räume, die seitens der Planung bearbeitet werden. Die Angaben dienen als Grundlage für die Fachplaner Architektur, Elektrotechnik, HKLS, Medgas & Statik. Neben den aufgelisteten Bemessungsangaben je Fachbereich werden die medizin- und labortechnischen Elemente eines Raumes in Listenform angeführt. Diese sind ebenfalls als Planungsgrundlage heranzuziehen.";
            $this->SetFont('helvetica', '', 10);


            if ($_SESSION["projectPlanungsphase"] === "Vorentwurf") {
                $Disclaimer = $Disclaimer . $Vorentwurf;
            } else if ($_SESSION["projectPlanungsphase"] === "Entwurf") {
                $Disclaimer = $Disclaimer . $Entwurf;
            }

            $height = $this->getStringHeight(390, $Disclaimer, 0, 'J', 0, 6);
            $this->SetY(275 - $height);
            $this->MultiCell(390, 6, $Disclaimer, 0, 'J', 0, 0);
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
        $this->Cell(0, 0, $tDate, 0, false, 'L', 0, '', 0, false, 'T', 'M');
        $this->Cell(0, 0, 'Seite ' . $this->getAliasNumPage() . ' von ' . $this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
    }
}
