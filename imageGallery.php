<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>RB-Projekte</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen"/>
    <link rel="icon" href="Logo/iphone_favicon.png">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css"
          integrity="sha512-q3eWabyZPc1XTCmF+8/LuE1ozpg5xxn7iO89yfSOd5/oKvyqLngoNGsx8jq92Y8eXJ/IRxQbEC+FGSYxtk2oiw=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/viewerjs/1.11.6/viewer.min.css"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/viewerjs/1.11.6/viewer.min.js"></script>
</head>

<?php
require_once 'utils/_utils.php';
init_page_serversides("No Redirect");
$projectID = (int)$_SESSION["projectID"];
$mysqli = utils_connect_sql();
?>

<body>
<div id="limet-navbar"></div>
<div class="container-fluid py-3">
    <div class="row">
        <div class="col-12">
            <?php include "img_support/card_load_image_preview.php"; ?>
        </div>
    </div>
</div>

<?php
include_once "img_support/modal_upload_image.php";
include_once "img_support/modal_delete_img.php";
include_once "img_support/modal_metadaten.php";
include_once "img_support/modal_img_room.php";
include_once "img_support/modal_img_vermerke.php";
$mysqli-> close();
?>

<script src="utils/_utils.js"></script>
<script src="img_support/projectGallery.js"></script>
</body>
</html>