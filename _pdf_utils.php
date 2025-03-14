<?php

function getFileName( $topic )
{
    $projectname = $_SESSION['projectName'];
    return $projectname."__GPMT__". $topic ."__".date("Y-m-d").".pdf";
}