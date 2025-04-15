<?php
session_start();
require_once('TCPDF-main/TCPDF-main/tcpdf.php');
include "pdf_createBericht_LOGO.php";

class MYPDF extends TCPDF
{
    public function Header()
    {
        if ($this->numpages > 1) {
            get_header_logo($this);
            $this->SetFont('helvetica', '', 8);
            $this->cell(0, 0, '', 0, 0, 'L');
            $this->Ln();
            if ($_SESSION["PDFTITEL"] != null) {
                $this->Cell(0, 0, $_SESSION["PDFTITEL"], 0, false, 'R', 0, '', 0, false, 'B', 'B');
            } else {
                $this->Cell(0, 0, 'Raumbuch', 0, false, 'R', 0, '', 0, false, 'B', 'B');
            }
            $this->Ln();
            if (!empty(str_replace(" ", "", $_SESSION["PDFHeaderSubtext"]))) {
                $this->Cell(0, 0, $_SESSION["PDFHeaderSubtext"], 'B', false, 'R', 0, '', 0, false, 'B', 'B');
                $this->Ln();
            } else {
                $this->cell(0, 0, '', 'B', 0, 'L');
                $this->Ln();
            }

        } else {
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
            $this->SetFont('helvetica', 'B', 15);
            $this->SetY(50);
            $this->Cell(0, 0, "" . $raumInfos[0]['Projektname'], 0, false, 'L', 0, '', 0, false, 'B', 'B');
            $this->Ln();
            $this->Cell(0, 0, $raumInfos[0]['Planungsphase'], 0, false, 'L', 0, '', 0, false, 'B', 'B');
            $this->Ln();
            $this->Ln();
            if ($_SESSION["PDFTITEL"] != null) {
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

            if ($_SESSION["DisclaimerText"] != null) {
                $this->SetFont('helvetica', '', 9);
                $Disclaimer_txt = $_SESSION["DisclaimerText"];
                $this->SetY(280 - ($this->getStringHeight(180, $Disclaimer_txt, 0, false, 'L', 0, '', 0, false, '', '')));
                $this->Multicell(180, 0, $Disclaimer_txt, 0, 'L', 0, 0);
                $_SESSION["DisclaimerText"] = null;
            }
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
        $this->Cell(0, 0, $tDate, 0, false, 'L', 0, '', 0, false, 'T', 'M');
        $this->Cell(0, 0, 'Seite ' . $this->getAliasNumPage() . ' von ' . $this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
    }

    /**
     * @return void
     */
    public function getLn(): void
    {
        $this->Ln();
    }
}
