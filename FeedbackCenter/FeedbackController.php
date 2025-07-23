<?php
require_once __DIR__ . '/FeedbackModel.php';
class FeedbackController {
    private FeedbackModel $model;

    public function __construct() {
        $this->model = new FeedbackModel();
    }

    public function index(): void
    {
        $wishlist = $this->model->getWishlist();
        $bugReports = $this->model->getBugReports();
        $message = $_SESSION['message'] ?? '';
        unset($_SESSION['message']);
        require __DIR__ . '/FeedbackViews.php';
    }

    public function addFeature(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $website = $_POST['feature_website'] ?? '';
            $title = $_POST['feature_title'] ?? '';
            $desc = $_POST['feature_description'] ?? '';
            $_SESSION['message'] = $this->model->addFeature($website, $title, $desc);
            header('Location: FeedbackIndex.php');
            exit;
        }
    }

    public function addBug(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $website = $_POST['bug_website'] ?? '';
            $title = $_POST['bug_title'] ?? '';
            $desc = $_POST['bug_description'] ?? '';
            $Severity = $_POST['bug_severity'] ?? '';
            $file = $_FILES['bug_screenshot'] ?? null;
            $_SESSION['message'] = $this->model->addBug($website, $title, $desc, $file, $Severity);
            header('Location: FeedbackIndex.php');
            exit;
        }
    }

    public function voteFeature(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['vote_feature_id'] ?? '';
            $direction = $_POST['vote'] ?? '';
            $_SESSION['message'] = $this->model->voteFeature($id, $direction);
            header('Location: FeedbackIndex.php');
            exit;
        }
    }

    public function voteBug(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['vote_bug_id'] ?? '';
            $direction = $_POST['vote'] ?? '';
            $_SESSION['message'] = $this->model->voteBug($id, $direction);
            header('Location: FeedbackIndex.php');
            exit;
        }
    }

    public function deleteFeature(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['delete_feature_id'] ?? '';
            $_SESSION['message'] = $this->model->deleteFeature($id);
            header('Location: FeedbackIndex.php');
            exit;
        }
    }

    public function deleteBug(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['delete_bug_id'] ?? '';
            $_SESSION['message'] = $this->model->deleteBug($id);
            header('Location: FeedbackIndex.php');
            exit;
        }
    }

    public function markFeatureDone(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['feature_id'] ?? '';
            $done = isset($_POST['Done']) ? 1 : 0;
            $_SESSION['message'] = $this->model->markFeatureDone($id, $done);
            header('Location: FeedbackIndex.php');
            exit;
        }
    }

    public function markBugDone(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['bug_id'] ?? '';
            $done = isset($_POST['Done']) ? 1 : 0;
            $_SESSION['message'] = $this->model->markBugDone($id, $done);
            header('Location: FeedbackIndex.php');
            exit;
        }
    }



}
