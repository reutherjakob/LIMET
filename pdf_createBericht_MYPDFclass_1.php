<?php

session_start();
require_once('TCPDF-master/TCPDF-master/tcpdf.php');

//include 'pdf_createBericht_utils.php';

class MYPDF extends TCPDF {

    public function Header() {
        if ($this->numpages > 1) {
            $image_file = 'LIMET_web.png';
            $this->Image($image_file, 15, 5, 20, 10, 'PNG', '', 'M', false, 300, '', false, false, 0, false, false, false);
            $this->SetFont('helvetica', '', 8);
 

            // Title
            $this->Cell(0, 0, 'Großgeräte Parameter Einbringung', 0, false, 'R', 0, '', 0, false, 'B', 'B'); 
            $this->Ln();
            $this->cell(0, 0, '', 'B', 0, 'L'); 
        } else { // Titelblatt
//            $mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');
//            if (!$mysqli->set_charset("utf8")) {
//                printf("Error loading character set utf8: %s\n or Login timed out", $mysqli->error);
//                exit();
//            }
//            $roomIDs = filter_input(INPUT_GET, 'roomID');
//            $teile = explode(",", $roomIDs);
//            $sql = "SELECT tabelle_projekte.Projektname, tabelle_planungsphasen.Bezeichnung, tabelle_räume.`Raumbereich Nutzer`
//                    FROM tabelle_räume INNER JOIN (tabelle_planungsphasen INNER JOIN tabelle_projekte ON tabelle_planungsphasen.idTABELLE_Planungsphasen = tabelle_projekte.TABELLE_Planungsphasen_idTABELLE_Planungsphasen) ON tabelle_räume.tabelle_projekte_idTABELLE_Projekte = tabelle_projekte.idTABELLE_Projekte ";
//            $i = 0;
//            foreach ($teile as $valueOfRoomID) {
//                if ($i == 0) {
//                    $sql = $sql . "WHERE tabelle_räume.idTABELLE_Räume=" . $valueOfRoomID . " ";
//                } else {
//                    $sql = $sql . "OR tabelle_räume.idTABELLE_Räume=" . $valueOfRoomID . " ";
//                }
//                $i++;
//            }
//            $sql = $sql . "GROUP BY tabelle_projekte.Projektname, tabelle_planungsphasen.Bezeichnung, tabelle_räume.`Raumbereich Nutzer` ORDER BY tabelle_räume.`Raumbereich Nutzer`;";
//            $result = $mysqli->query($sql);
//            $raumInfos = array();
//            $raumInfosCounter = 0;
//            while ($row = $result->fetch_assoc()) {
//                $raumInfos[$raumInfosCounter]['Projektname'] = $row['Projektname'];
//                $raumInfos[$raumInfosCounter]['Planungsphase'] = $row['Bezeichnung'];
//                $raumInfos[$raumInfosCounter]['Raumbereich'] = $row['Raumbereich Nutzer'];
//                $raumInfosCounter = $raumInfosCounter + 1;
//            }
//
//            $mysqli->close();
            $this->SetFont('helvetica', 'B', 15);
            $this->SetY(50);
            $this->Cell(0, 0, "KHI", 0, false, 'L', 0, '', 0, false, 'B', 'B');
            $this->Ln();
            $this->Cell(0, 0, "Vorentwurf", 0, false, 'L', 0, '', 0, false, 'B', 'B');
            $this->Ln(30);
 
            $this->Cell(0, 0, 'Einbringwege'. "" , 0, false, 'L', 0, '', 0, false, 'B', 'B');
            $this->Ln();
            $this->Cell(0, 0, "Großgeräte" , 0, false, 'L', 0, '', 0, false, 'B', 'B');
            $this->Ln(30); 
//            $this->Cell(0, 0, 'Funktionsstellen: ', 0, false, 'L', 0, '', 0, false, 'B', 'B');
//            $this->Ln();

//            $raumInfosCounter = 0;
//            $funktionsStellen = "";
//            foreach ($raumInfos as $valueOfRaumInfos) {
//                if ($raumInfosCounter > 0) {
//                    $funktionsStellen = $funktionsStellen . "\n";
//                }
//                $funktionsStellen = $funktionsStellen . $raumInfos[$raumInfosCounter]['Raumbereich'];
//
//                $raumInfosCounter = $raumInfosCounter + 1;
//            }
//            $this->SetFont('helvetica', '', 12);
//            $this->MultiCell(150, 6, $funktionsStellen, 0, 'L', 0, 0);
//            $this->Ln();
//            $this->Ln();
//            $this->SetFont('helvetica', '', 10);
//            $this->Cell(0, 0, "Stand: " . date('Y-m-d'), 0, false, 'L', 0, '', 0, false, 'T', 'M');
//            $this->Ln();
            $this->SetFont('helvetica', '', 6);
            $image_file = 'LIMET_web.png';
            $this->Image($image_file, 150, 40, 30, 15, 'PNG', '', 'M', false, 300, '', false, false, 0, false, false, false);
        }
    }

    public function Footer() {  // Page footer
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->cell(0, 0, '', 'T', 0, 'L');
        $this->Ln();
        $tDate = date('Y-m-d');
        $this->Cell(0, 0, $tDate, 0, false, 'L', 0, '', 0, false, 'T', 'M');
        $this->Cell(0, 0, 'Seite ' . $this->getAliasNumPage() . ' von ' . $this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
    }
}
