<?php
require_once '_utils/_utils.php';
require_once '_utils/_csrf.php';
check_login();
require_csrf();

if (!empty($_POST["projectID"]))            $_SESSION["projectID"] = $_POST["projectID"];
if (!empty($_POST["roomID"]))               $_SESSION["roomID"] = $_POST["roomID"];
if (!empty($_POST["elementID"]))            $_SESSION["elementID"] = $_POST["elementID"];
if (!empty($_POST["projectName"]))          $_SESSION["projectName"] = $_POST["projectName"];
if (!empty($_POST["projectAusfuehrung"]))   $_SESSION["projectAusfuehrung"] = $_POST["projectAusfuehrung"];
if (!empty($_POST["projectPlanungsphase"])) $_SESSION["projectPlanungsphase"] = $_POST["projectPlanungsphase"];
if (!empty($_POST["variantenID"]))          $_SESSION["variantenID"] = $_POST["variantenID"];
if (!empty($_POST["projektArt"]))           $_SESSION["projektArt"] = $_POST["projektArt"];