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
    case 'markFeatureDone':
        $controller->markFeatureDone();
        break;
    case 'markBugDone':
        $controller->markBugDone();
        break;


    default:
        $controller->index();
        break;
}

