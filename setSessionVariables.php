<!DOCTYPE html>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
<html lang="de">
<head>
    <title> Set Session Variables </title></head>
<body>

<?php
// 25 FX
require_once 'utils/_utils.php';
check_login();
if (isset($_POST["projectID"]) && $_POST["projectID"] != "") {
    $_SESSION["projectID"] = $_POST["projectID"];
}
if (isset($_POST["roomID"]) && $_POST["roomID"] != "") {
    $_SESSION["roomID"] = $_POST["roomID"];
}
if (isset($_POST["elementID"]) && $_POST["elementID"] != "") {
    $_SESSION["elementID"] = $_POST["elementID"];
}
if (isset($_POST["projectName"]) && $_POST["projectName"] != "") {
    $_SESSION["projectName"] = $_POST["projectName"];
}
if (isset($_POST["projectAusfuehrung"]) && $_POST["projectAusfuehrung"] != "") {
    $_SESSION["projectAusfuehrung"] = $_POST["projectAusfuehrung"];
}
if (isset($_POST["projectPlanungsphase"]) && $_POST["projectPlanungsphase"] != "") {
    $_SESSION["projectPlanungsphase"] = $_POST["projectPlanungsphase"];
}
if (isset($_POST["variantenID"]) &&$_POST["variantenID"] != "") {
    $_SESSION["variantenID"] = $_POST["variantenID"];
}
?>

</body>
</html>