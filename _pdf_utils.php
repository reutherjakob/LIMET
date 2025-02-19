<?php

function getFileName( $topic, $File_ending )
{
    $projectname = $_SESSION['projectName'];

    return $projectname."__GPMT__". $topic ."__".date("Y-m-d").".".$File_ending;
}