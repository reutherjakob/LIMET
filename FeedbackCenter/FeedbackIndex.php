<?php
require_once 'FeedbackModel.php';
require_once 'FeedbackController.php';

$controller = new FeedbackController();

$action = $_GET['action'] ?? null;

switch ($action) {
case 'addFeature':
$controller->addFeature();
break;
case 'addBug':
$controller->addBug();
break;
case 'voteFeature':
$controller->voteFeature();
break;
case 'voteBug':
$controller->voteBug();
break;
case 'deleteFeature':
$controller->deleteFeature();
break;
case 'deleteBug':
$controller->deleteBug();
break;
default:
$controller->index();
break;
}

/* Feature Request und Bug Report:

    id (String)

website (String, Pflichtfeld)

    title (String, Pflichtfeld)

    description (String, Pflichtfeld)

    upvotes (Integer, Standard: 0)

    downvotes (Integer, Standard: 0)

    (Bug Report: optional screenshot, optional url) */
