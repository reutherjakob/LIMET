<?php
include "_utils.php";
init_page_serversides("x", "x");
check_login();

// File paths
$wishlistFile = 'txt/feature_wishlist.txt';
$bugReportFile = 'txt/bug_reports.txt';
$uploadDir = __DIR__ . '/txt/bug_screenshots/';
$maxFileSize = 5 * 1024 * 1024; // 5 MB

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0775, true);
}

// Helper: Generate unique IDs
function generate_feature_id()
{
    $date = date('Ymd');
    $counter = 1;
    $existing = [];
    global $wishlistFile;
    if (file_exists($wishlistFile)) {
        $content = file_get_contents($wishlistFile);
        preg_match_all('/^ID: (WISH-\d{8}-\d{4})/m', $content, $matches);
        if (!empty($matches[1])) {
            $existing = $matches[1];
        }
    }
    do {
        $id = "WISH-$date-" . str_pad($counter, 4, '0', STR_PAD_LEFT);
        $counter++;
    } while (in_array($id, $existing));
    return $id;
}

function generate_bug_id()
{
    $date = date('Ymd');
    $counter = 1;
    $existing = [];
    global $bugReportFile;
    if (file_exists($bugReportFile)) {
        $content = file_get_contents($bugReportFile);
        preg_match_all('/^ID: (BUG-\d{8}-\d{4})/m', $content, $matches);
        if (!empty($matches[1])) {
            $existing = $matches[1];
        }
    }
    do {
        $id = "BUG-$date-" . str_pad($counter, 4, '0', STR_PAD_LEFT);
        $counter++;
    } while (in_array($id, $existing));
    return $id;
}

// Handle deletions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    if (file_exists($wishlistFile)) {
        $content = file_get_contents($wishlistFile);
        $rawEntries = explode('------------------------', $content);
        $rawEntries = array_filter(array_map('trim', $rawEntries));
        $indexToDelete = intval($_POST['feature_index']);
        $newContent = '';
        foreach ($rawEntries as $i => $entry) {
            if ($i == $indexToDelete) continue;
            $newContent .= trim($entry) . PHP_EOL . "------------------------" . PHP_EOL;
        }
        file_put_contents($wishlistFile, $newContent);
        $message = "Feature request deleted.";
    }
} elseif (isset($_POST['report_index'])) {
    if (file_exists($bugReportFile)) {
        $content = file_get_contents($bugReportFile);
        $rawEntries = explode('------------------------', $content);
        $rawEntries = array_filter(array_map('trim', $rawEntries));
        $indexToDelete = intval($_POST['report_index']);
        $newContent = '';
        $deletedScreenshot = null;
        foreach ($rawEntries as $i => $entry) {
            if ($i == $indexToDelete) {
                // Find screenshot file (if any)
                if (preg_match('/^Screenshot: (.+)$/m', $entry, $match)) {
                    $screenshot = trim($match[1]);
                    if ($screenshot && file_exists($uploadDir . $screenshot)) {
                        unlink($uploadDir . $screenshot);
                    }
                }
                continue; // skip this entry
            }
            $newContent .= trim($entry) . PHP_EOL . "------------------------" . PHP_EOL;
        }
        file_put_contents($bugReportFile, $newContent);
        $message = "Bug report deleted.";
    }

}

// Handle new submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST['delete'])) {
    if (isset($_POST['form_type']) && $_POST['form_type'] === 'feature') {
        $featureTitle = trim($_POST['feature_title'] ?? '');
        $featureDescription = trim($_POST['feature_description'] ?? '');

        if ($featureTitle && $featureDescription) {
            $featureId = generate_feature_id();
            $entry = "ID: $featureId" . PHP_EOL;
            $entry .= "Date: " . date('Y-m-d H:i:s') . PHP_EOL;
            $entry .= "Title: " . htmlspecialchars($featureTitle) . PHP_EOL;
            $entry .= "Description: " . htmlspecialchars($featureDescription) . PHP_EOL;
            $entry .= "------------------------" . PHP_EOL;
            file_put_contents($wishlistFile, $entry, FILE_APPEND | LOCK_EX);
            $message = "Thank you for your suggestion!";
        } else {
            $message = "Please provide a title and description for your feature request.";
        }
    } elseif (isset($_POST['form_type']) && $_POST['form_type'] === 'bug') {

        $bugTitle = trim($_POST['bug_title'] ?? '');
        $bugDescription = trim($_POST['bug_description'] ?? '');
        $screenshotFilename = '';

        // Validate and handle file upload
        if (!empty($_FILES['bug_screenshot']['name'])) {
            $file = $_FILES['bug_screenshot'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if ($file['error'] === UPLOAD_ERR_OK && in_array($ext, $allowed) && $file['size'] <= $maxFileSize) {
                $uniqueName = uniqid('bugshot_', true) . '.' . $ext;
                if (move_uploaded_file($file['tmp_name'], $uploadDir . $uniqueName)) {
                    $screenshotFilename = $uniqueName;
                }
            }
        }

        if ($bugTitle && $bugDescription) {
            $bugId = generate_bug_id();
            $entry = "ID: $bugId" . PHP_EOL;
            $entry .= "Date: " . date('Y-m-d H:i:s') . PHP_EOL;
            $entry .= "Title: " . htmlspecialchars($bugTitle) . PHP_EOL;
            $entry .= "Bug Description: " . htmlspecialchars($bugDescription) . PHP_EOL;
            if ($screenshotFilename) {
                $entry .= "Screenshot: " . $screenshotFilename . PHP_EOL;
            }
            $entry .= "------------------------" . PHP_EOL;

            file_put_contents($bugReportFile, $entry, FILE_APPEND | LOCK_EX);

            $message = "Thank you for your report! Please make sure you included the webpage and enough details to help us reproduce the bug.";
        } else {
            $message = "Please provide a bug title and description.";
        }
    }
}

$wishlist = [];
if (file_exists($wishlistFile)) {
    $content = file_get_contents($wishlistFile);
    $rawEntries = explode('------------------------', $content);
    $rawEntries = array_filter(array_map('trim', $rawEntries));
    foreach ($rawEntries as $entry) {
        if ($entry) {
            $lines = explode("\n", $entry);
            $id = '';
            $date = '';
            $title = '';
            $description = '';
            foreach ($lines as $line) {
                if (strpos($line, 'ID: ') === 0) {
                    $id = substr($line, 4);
                } elseif (strpos($line, 'Date: ') === 0) {
                    $date = substr($line, 6);
                } elseif (strpos($line, 'Title: ') === 0) {
                    $title = substr($line, 7);
                } elseif (strpos($line, 'Description: ') === 0) {
                    $description = substr($line, 13);
                } elseif (trim($line) !== '') {
                    $description .= "\n" . trim($line);
                }
            }
            $wishlist[] = [
                'id' => $id,
                'date' => $date,
                'title' => $title,
                'description' => trim($description)
            ];
        }
    }
    $wishlist = array_slice($wishlist, -50);
}
$bugReports = [];

if (file_exists($bugReportFile)) {
    $content = file_get_contents($bugReportFile);
    $rawEntries = explode('------------------------', $content);
    $rawEntries = array_filter(array_map('trim', $rawEntries));
    foreach ($rawEntries as $entry) {
        if ($entry) {
            $lines = explode("\n", $entry);
            $id = '';
            $date = '';
            $title = '';
            $description = '';
            $screenshot = '';
            foreach ($lines as $line) {
                if (strpos($line, 'ID: ') === 0) {
                    $id = substr($line, 4);
                } elseif (strpos($line, 'Date: ') === 0) {
                    $date = substr($line, 6);
                } elseif (strpos($line, 'Title: ') === 0) {
                    $title = substr($line, 7);
                } elseif (strpos($line, 'Bug Description: ') === 0) {
                    $description = substr($line, 17);
                } elseif (strpos($line, 'Screenshot: ') === 0) {
                    $screenshot = trim(substr($line, 11));
                } elseif (trim($line) !== '') {
                    $description .= "\n" . trim($line);
                }
            }
            $bugReports[] = [
                'id' => $id,
                'date' => $date,
                'title' => $title,
                'description' => trim($description),
                'screenshot' => $screenshot
            ];
        }
    }
    $bugReports = array_slice($bugReports, -50);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Feedback Center</title>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css"/>
</head>
<body>

<div id="limet-navbar"></div>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <h1 class="mb-4 text-center"><i class="fas fa-comments"></i> Feedback Center</h1>
            <?php if (!empty($message)): ?>
                <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <!-- Selection Tabs -->
            <ul class="nav nav-tabs mb-4" id="feedbackTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="feature-tab" data-bs-toggle="tab" data-bs-target="#feature-form"
                            type="button" role="tab"> <i class="far fa-lightbulb"></i> Suggest a Feature
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="bug-tab" data-bs-toggle="tab" data-bs-target="#bug-form" type="button"
                            role="tab"><i class="fas fa-bug"></i>  Report a Bug
                    </button>
                </li>
            </ul>

            <div class="tab-content mb-5" id="feedbackTabContent">
                <!-- Feature Suggestion Form -->
                <div class="tab-pane fade show active" id="feature-form" role="tabpanel">
                    <form method="post" action="" class="needs-validation" novalidate>
                        <input type="hidden" name="form_type" value="feature">
                        <div class="mb-3">
                            <label for="feature_title" class="form-label">Feature Title</label>
                            <input id="feature_title" name="feature_title" class="form-control" maxlength="120"
                                   required>
                            <div class="invalid-feedback">Please provide a short title for the feature.</div>
                        </div>
                        <div class="mb-3">
                            <label for="feature_description" class="form-label">Feature Description</label>
                            <textarea id="feature_description" name="feature_description" class="form-control" rows="7"
                                      required></textarea>
                            <div class="invalid-feedback">Please describe the feature in detail.</div>
                        </div>
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-paper-plane"></i> Submit Feature Request
                        </button>
                    </form>
                </div>
                <!-- Bug Report Form -->
                <div class="tab-pane fade" id="bug-form" role="tabpanel">
                    <form method="post" action="" class="needs-validation" enctype="multipart/form-data" novalidate>
                        <input type="hidden" name="form_type" value="bug">
                        <div class="mb-3">
                            <label for="bug_title" class="form-label">Bug Title</label>
                            <input id="bug_title" name="bug_title" class="form-control" maxlength="120" required>
                            <div class="invalid-feedback">Please provide a short title for the bug.</div>
                        </div>
                        <div class="mb-3">
                            <label for="bug_description" class="form-label">Bug Description</label>
                            <textarea id="bug_description" name="bug_description" class="form-control" rows="7"
                                      required></textarea>
                            <div class="invalid-feedback">Please describe the bug, including the webpage and steps to
                                reproduce it.
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="bug_screenshot" class="form-label">Screenshot (optional)</label>
                            <input type="file" id="bug_screenshot" name="bug_screenshot" class="form-control"
                                   accept="image/*">
                            <div class="form-text">Accepted formats: jpg, jpeg, png, gif, webp. Max size: 5 MB.</div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-paper-plane"></i> Submit Bug Report
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Lists Section -->
    <div class="row">
        <div class="col-lg-6">
            <h2 class="mb-3 text-center"><i class="fas fa-list"></i> Feature Wishlist</h2>
            <?php if (empty($wishlist)): ?>
                <div class="alert alert-light text-center">No feature requests yet.</div>
            <?php else: ?>
                <div class="accordion" id="wishlistAccordion">
                    <?php foreach (array_reverse($wishlist, true) as $idx => $entry): ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="wishHeading<?php echo $idx; ?>">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#wishCollapse<?php echo $idx; ?>" aria-expanded="false"
                                        aria-controls="wishCollapse<?php echo $idx; ?>">
                                    <span class="badge bg-info me-2"><?php echo htmlspecialchars($entry['id']); ?></span>
                                    <strong><?php echo htmlspecialchars($entry['title']); ?></strong>
                                    <span class="ms-2 text-muted small"><i
                                                class="fas fa-calendar-alt"></i> <?php echo htmlspecialchars($entry['date']); ?></span>
                                </button>
                            </h2>
                            <div id="wishCollapse<?php echo $idx; ?>" class="accordion-collapse collapse"
                                 aria-labelledby="wishHeading<?php echo $idx; ?>" data-bs-parent="#wishlistAccordion">
                                <div class="accordion-body">
                                    <pre class="mb-2"
                                         style="white-space: pre-wrap; word-break: break-word;"><?php echo htmlspecialchars($entry['description']); ?></pre>
                                    <form method="post" action=""
                                          onsubmit="return confirm('Are you sure you want to delete this feature request?');">
                                        <input type="hidden" name="feature_index"
                                               value="<?php echo count($wishlist) - $idx - 1; ?>">
                                        <button type="submit" name="delete" value="1" class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash-alt"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="col-lg-6">
            <h2 class="mb-3 text-center"><i class="fas fa-list"></i> Reported Bugs</h2>
            <?php if (empty($bugReports)): ?>
                <div class="alert alert-light text-center">No bugs have been reported yet.</div>
            <?php else: ?>
                <div class="accordion" id="bugAccordion">
                    <?php foreach (array_reverse($bugReports, true) as $idx => $report): ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading<?php echo $idx; ?>">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapse<?php echo $idx; ?>" aria-expanded="false"
                                        aria-controls="collapse<?php echo $idx; ?>">
                                    <span class="badge bg-secondary me-2"><?php echo htmlspecialchars($report['id']); ?></span>
                                    <strong><?php echo htmlspecialchars($report['title']); ?></strong>
                                    <span class="ms-2 text-muted small"><i
                                                class="fas fa-calendar-alt"></i> <?php echo htmlspecialchars($report['date']); ?></span>
                                </button>
                            </h2>
                            <div id="collapse<?php echo $idx; ?>" class="accordion-collapse collapse"
                                 aria-labelledby="heading<?php echo $idx; ?>" data-bs-parent="#bugAccordion">
                                <div class="accordion-body">
                                    <pre class="mb-2"
                                         style="white-space: pre-wrap; word-break: break-word;"><?php echo htmlspecialchars($report['description']); ?></pre>
                                    <?php if ($report['screenshot']): ?>
                                        <div class="mb-3">
                                            <a href="txt/bug_screenshots/<?php echo urlencode($report['screenshot']); ?>"
                                               target="_blank">
                                                <img src="txt/bug_screenshots/<?php echo urlencode($report['screenshot']); ?>"
                                                     alt="Screenshot" class="img-thumbnail" style="max-width:300px;">
                                            </a>
                                            <div class="form-text">
                                                Screenshot: <?php echo htmlspecialchars($report['screenshot']); ?></div>
                                        </div>
                                    <?php endif; ?>
                                    <form method="post" action=""
                                          onsubmit="return confirm('Are you sure you want to delete this bug report and its screenshot?');">
                                        <input type="hidden" name="report_index"
                                               value="<?php echo count($bugReports) - $idx - 1; ?>">
                                        <button type="submit" name="delete" value="1" class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash-alt"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<script>
    // Bootstrap form validation
    (function () {
        'use strict'
        var forms = document.querySelectorAll('.needs-validation')
        Array.prototype.slice.call(forms).forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                form.classList.add('was-validated')
            }, false)
        })
    })()
</script>
</body>
</html>
