<?php
require_once __DIR__ . '/FeedbackModel.php';

class FeedbackController {
    private $model;

    public function __construct() {
        $this->model = new FeedbackModel();
    }

    public function index() {
        $wishlist = $this->model->getWishlist();
        $bugReports = $this->model->getBugReports();
        $message = $_SESSION['message'] ?? '';
        unset($_SESSION['message']);
        require __DIR__ . '/FeedbackViews.php';
    }

    public function addFeature() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['feature_title'] ?? '';
            $desc = $_POST['feature_description'] ?? '';
            $_SESSION['message'] = $this->model->addFeature($title, $desc);
        }
        header('Location: FeedbackIndex.php');
        exit;
    }

    public function addBug() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['bug_title'] ?? '';
            $desc = $_POST['bug_description'] ?? '';
            $file = $_FILES['bug_screenshot'] ?? null;
            $_SESSION['message'] = $this->model->addBug($title, $desc, $file);
        }
        header('Location: FeedbackIndex.php');
        exit;
    }

    public function deleteFeature() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['delete_feature_id'] ?? '';
            $_SESSION['message'] = $this->model->deleteFeature($id);
        }
        header('Location: FeedbackIndex.php');
        exit;
    }

    public function deleteBug() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['delete_bug_id'] ?? '';
            $_SESSION['message'] = $this->model->deleteBug($id);
        }
        header('Location: FeedbackIndex.php');
        exit;
    }
}
