<?php
include "_utils.php";
check_login();
?>

<!DOCTYPE html>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
<html>
<head>
</head>
<body>


<?php
if (isset($_GET["projectID"]) && $_GET["projectID"] != "") {
    $_SESSION["projectID"] = $_GET["projectID"];
}
if (isset($_GET["roomID"]) && $_GET["roomID"] != "") {
    $_SESSION["roomID"] = $_GET["roomID"];
}
if (isset($_GET["elementID"]) && $_GET["elementID"] != "") {
    $_SESSION["elementID"] = $_GET["elementID"];
}
if (isset($_GET["projectName"]) && $_GET["projectName"] != "") {
    $_SESSION["projectName"] = $_GET["projectName"];
}
if (isset($_GET["projectAusfuehrung"]) && $_GET["projectAusfuehrung"] != "") {
    $_SESSION["projectAusfuehrung"] = $_GET["projectAusfuehrung"];
}
if (isset($_GET["projectPlanungsphase"]) && $_GET["projectPlanungsphase"] != "") {
    $_SESSION["projectPlanungsphase"] = $_GET["projectPlanungsphase"];
}

?>


</body>
</html>