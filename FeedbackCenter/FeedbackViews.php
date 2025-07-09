<?php
if (!function_exists('utils_connect_sql')) {
    include "../utils/_utils.php";
}
init_page_serversides("x");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Feedback Center</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css"/>
    <link rel="stylesheet" href="../css/style.css" type="text/css" media="screen"/>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
</head>
<body>

<div id="limet-navbar"></div> <!-- Container für Navbar -->

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <h1 class="mb-4 text-center"><i class="fas fa-comments"></i> Feedback Center</h1>
            <?php if (!empty($message)): ?>
                <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
            <ul class="nav nav-tabs mb-4" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#feature-form" type="button"
                            role="tab">Suggest a Feature
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#bug-form" type="button" role="tab">
                        Report a Bug
                    </button>
                </li>
            </ul>
            <div class="tab-content mb-5">
                <div class="tab-pane fade show active" id="feature-form" role="tabpanel">
                    <form method="post" action="/FeedbackCenter/FeedbackIndex.php?action=addFeature"
                          class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label for="feature_title" class="form-label">Feature Title</label>
                            <input id="feature_title" name="feature_title" class="form-control" maxlength="120"
                                   required>
                        </div>
                        <div class="mb-3">
                            <label for="feature_description" class="form-label">Feature Description</label>
                            <textarea id="feature_description" name="feature_description" class="form-control" rows="7"
                                      required></textarea>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Submit Feature Request</button>
                    </form>
                </div>
                <div class="tab-pane fade" id="bug-form" role="tabpanel">
                    <form method="post" action="/FeedbackCenter/FeedbackIndex.php?action=addBug"
                          class="needs-validation" enctype="multipart/form-data" novalidate>
                        <div class="mb-3">
                            <label for="bug_title" class="form-label">Bug Title</label>
                            <input id="bug_title" name="bug_title" class="form-control" maxlength="120" required>
                        </div>
                        <div class="mb-3">
                            <label for="bug_description" class="form-label">Bug Description</label>
                            <textarea id="bug_description" name="bug_description" class="form-control" rows="7"
                                      required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="bug_screenshot" class="form-label">Screenshot (optional)</label>
                            <input type="file" id="bug_screenshot" name="bug_screenshot" class="form-control"
                                   accept="image/*">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Submit Bug Report</button>
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
                            <h2 class="accordion-header" id="wishHeading<?= $idx ?>">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#wishCollapse<?= $idx ?>" aria-expanded="false"
                                        aria-controls="wishCollapse<?= $idx ?>">
                                    <span class="badge bg-info me-2"><?= htmlspecialchars($entry['id']) ?></span>
                                    <strong><?= htmlspecialchars($entry['title']) ?></strong>
                                    <span class="ms-2 text-muted small"><i
                                                class="fas fa-calendar-alt"></i> <?= htmlspecialchars($entry['date']) ?></span>
                                </button>
                            </h2>
                            <div id="wishCollapse<?= $idx ?>" class="accordion-collapse collapse"
                                 aria-labelledby="wishHeading<?= $idx ?>" data-bs-parent="#wishlistAccordion">
                                <div class="accordion-body">
                                    <pre class="mb-2"
                                         style="white-space: pre-wrap; word-break: break-word;"><?= htmlspecialchars($entry['description']) ?></pre>
                                    <form method="post" action="/FeedbackCenter/FeedbackIndex.php?action=deleteFeature"
                                          onsubmit="return confirm('Are you sure you want to delete this feature request?');">
                                        <input type="hidden" name="delete_feature_id"
                                               value="<?= htmlspecialchars($entry['id']) ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">
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
                            <h2 class="accordion-header" id="heading<?= $idx ?>">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapse<?= $idx ?>" aria-expanded="false"
                                        aria-controls="collapse<?= $idx ?>">
                                    <span class="badge bg-secondary me-2"><?= htmlspecialchars($report['id']) ?></span>
                                    <strong><?= htmlspecialchars($report['title']) ?></strong>
                                    <span class="ms-2 text-muted small"><i
                                                class="fas fa-calendar-alt"></i> <?= htmlspecialchars($report['date']) ?></span>
                                </button>
                            </h2>
                            <div id="collapse<?= $idx ?>" class="accordion-collapse collapse"
                                 aria-labelledby="heading<?= $idx ?>" data-bs-parent="#bugAccordion">
                                <div class="accordion-body">
                                    <pre class="mb-2"
                                         style="white-space: pre-wrap; word-break: break-word;"><?= htmlspecialchars($report['description']) ?></pre>
                                    <?php if ($report['screenshot']): ?>
                                        <div class="mb-3">
                                            <a href="/FeedbackCenter/txt/bug_screenshots/<?= urlencode($report['screenshot']) ?>"
                                               target="_blank">
                                                <img src="/FeedbackCenter/txt/bug_screenshots/<?= urlencode($report['screenshot']) ?>"
                                                     alt="Screenshot" class="img-thumbnail" style="max-width:300px;">
                                            </a>
                                            <div class="form-text">
                                                Screenshot: <?= htmlspecialchars($report['screenshot']) ?></div>
                                        </div>
                                    <?php endif; ?>
                                    <form method="post" action="/FeedbackCenter/FeedbackIndex.php?action=deleteBug"
                                          onsubmit="return confirm('Are you sure you want to delete this bug report and its screenshot?');">
                                        <input type="hidden" name="delete_bug_id"
                                               value="<?= htmlspecialchars($report['id']) ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
