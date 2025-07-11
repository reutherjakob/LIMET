<?php
require_once __DIR__ . '/FeedbackModel.php';
class FeedbackController {
    private FeedbackModel $model;

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
            $website = $_POST['feature_website'] ?? '';
            $title = $_POST['feature_title'] ?? '';
            $desc = $_POST['feature_description'] ?? '';
            $_SESSION['message'] = $this->model->addFeature($website, $title, $desc);
            header('Location: FeedbackIndex.php');
            exit;
        }
    }

    public function addBug() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $website = $_POST['bug_website'] ?? '';
            $title = $_POST['bug_title'] ?? '';
            $desc = $_POST['bug_description'] ?? '';
            $url = $_POST['bug_severity'] ?? '';
            $file = $_FILES['bug_screenshot'] ?? null;
            $_SESSION['message'] = $this->model->addBug($website, $title, $desc, $file, $url);
            header('Location: FeedbackIndex.php');
            exit;
        }
    }

    public function voteFeature() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['vote_feature_id'] ?? '';
            $direction = $_POST['vote'] ?? '';
            $_SESSION['message'] = $this->model->voteFeature($id, $direction);
            header('Location: FeedbackIndex.php');
            exit;
        }
    }

    public function voteBug() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['vote_bug_id'] ?? '';
            $direction = $_POST['vote'] ?? '';
            $_SESSION['message'] = $this->model->voteBug($id, $direction);
            header('Location: FeedbackIndex.php');
            exit;
        }
    }

    public function deleteFeature() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['delete_feature_id'] ?? '';
            $_SESSION['message'] = $this->model->deleteFeature($id);
            header('Location: FeedbackIndex.php');
            exit;
        }
    }

    public function deleteBug() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['delete_bug_id'] ?? '';
            $_SESSION['message'] = $this->model->deleteBug($id);
            header('Location: FeedbackIndex.php');
            exit;
        }
    }
}
