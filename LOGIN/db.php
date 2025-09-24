<?php

$mysqli = new mysqli("localhost", "fuchs", "f53cb99345993ef6892f0aeb34c25028", "LIMET_RB");
if ($mysqli->connect_errno) {
    die("Failed to connect to MySQL: " . $mysqli->connect_error);
}