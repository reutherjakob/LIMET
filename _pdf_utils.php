<?php

function getFileName( $topic )
{
    $projectname = $_SESSION['projectName'];
    return $projectname."__GPMT__". $topic ."__".date("Y-m-d").".pdf";
}

function check4newpage($pdf, $rowHeightComment): void
{
    $y = $pdf->GetY();
    if (($y + $rowHeightComment) >= 270) {
        $pdf->AddPage();
    }
}