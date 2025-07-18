<?php
require_once '../utils/_utils.php';
init_page_serversides("x", "x");
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
<div id="limet-navbar"></div>
<div class="container py-5">

    <div class="row">
        <div class="col-lg-10 mx-auto">
            <h1 class="mb-4 text-center"><i class="fas fa-comments"></i> Feedback Center</h1>
            <?php if (!empty($message)): ?>
                <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
            <div class="card border-white">
                <div class="card-body border-white">
                    <ul class="nav nav-tabs mb-4" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#feature-form"
                                    type="button"
                                    role="tab">
                                <i class="fab fa-stack-overflow"></i> Feature vorschlagen
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#bug-form" type="button"
                                    role="tab">
                                <i class="fas fa-bug"></i> Bug melden
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content mb-5">
                        <div class="tab-pane fade show active" id="feature-form" role="tabpanel">
                            <form method="post" action="/FeedbackCenter/FeedbackIndex.php?action=addFeature"
                                  class="needs-validation" novalidate>
                                <div class="mb-3">
                                    <label for="feature_website" class="form-label" hidden=""></label>
                                    <input id="feature_website" name="feature_website" class="form-control"
                                           maxlength="120"
                                           required placeholder="Website Name">
                                </div>
                                <div class="mb-3">
                                    <label for="feature_title" class="form-label" hidden></label>
                                    <input id="feature_title" name="feature_title" class="form-control" maxlength="120"
                                           required placeholder="Feature Title">
                                </div>
                                <div class="mb-3">
                                    <label for="feature_description" class="form-label" hidden></label>
                                    <textarea id="feature_description" name="feature_description" class="form-control"
                                              rows="7"
                                              required placeholder="Beschreibung"></textarea>
                                </div>
                                <button type="submit" class="btn btn-success w-100">Submit Feature Request</button>
                            </form>
                        </div>
                        <div class="tab-pane fade" id="bug-form" role="tabpanel">
                            <form method="post" action="/FeedbackCenter/FeedbackIndex.php?action=addBug"
                                  class="needs-validation" enctype="multipart/form-data" novalidate>
                                <div class="mb-3">
                                    <label for="bug_title" class="form-label" hidden=""></label>
                                    <input id="bug_title" name="bug_title" class="form-control" maxlength="120" required
                                           placeholder="Bug Title">
                                </div>

                                <div class="mb-3">
                                    <label for="bug_website" class="form-label" hidden>Welche Website</label>
                                    <input id="bug_website" name="bug_website" class="form-control" maxlength="120"
                                           required
                                           placeholder="Welche Website? github.limet-rb.com/beispielFehlerhafteWebseite">
                                </div>

                                <div class="mb-3">
                                    <label for="bug_description" class="form-label" hidden="">Bug Beschreibung</label>
                                    <textarea id="bug_description" name="bug_description" class="form-control" rows="7"
                                              required
                                              placeholder="Bug Beschreibung:
Welches Verhalten wurde erwartet? Wie kann man Fehlverhalten nachstellen?"></textarea>
                                </div>
                                <div class="mb-3 row align-items-center">
                                    <label for="bug_severity" class="form-label col-3 mb-0"> </label>

                                    <div class=" d-flex justify-content-between">

                                        <ul class="nav nav-pills" id="severityPills" role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" disabled type="button">
                                                    Schwere
                                                </button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link " id="critical-pill" data-bs-toggle="pill"
                                                        type="button"
                                                        role="tab" aria-selected="true"
                                                        onclick="setSeverity('Critical')">
                                                    <i class="fas fa-skull-crossbones"> </i> Critical
                                                </button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="high-pill" data-bs-toggle="pill"
                                                        type="button"
                                                        role="tab" aria-selected="false" onclick="setSeverity('High')">
                                                    High
                                                </button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="medium-pill" data-bs-toggle="pill"
                                                        type="button"
                                                        role="tab" aria-selected="false"
                                                        onclick="setSeverity('Medium')">
                                                    Medium
                                                </button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link active" id="low-pill" data-bs-toggle="pill"
                                                        type="button"
                                                        role="tab" aria-selected="false" onclick="setSeverity('Low')">
                                                    Low
                                                </button>
                                            </li>
                                        </ul>
                                        <input type="hidden" id="bug_severity" name="bug_severity" value="Critical"
                                               required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="bug_screenshot" class="form-label" hidden>Screenshot </label>
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
                                        <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse"
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
                                            <strong>Website:</strong> <?= htmlspecialchars($entry['website']) ?><br>
                                            <pre class="mb-2"
                                                 style="white-space: pre-wrap; word-break: break-word;"><?= htmlspecialchars($entry['description']) ?></pre>
                                            <strong>Upvotes:</strong> <?= (int)$entry['upvotes'] ?> |
                                            <strong>Downvotes:</strong> <?= (int)$entry['downvotes'] ?>
                                            <form method="post"
                                                  action="/FeedbackCenter/FeedbackIndex.php?action=voteFeature"
                                                  style="display:inline;">
                                                <input type="hidden" name="vote_feature_id"
                                                       value="<?= htmlspecialchars($entry['id']) ?>">
                                                <button name="vote" value="up" class="btn btn-link p-0 ms-2"><i
                                                            class="fas fa-arrow-up"></i></button>
                                                <button name="vote" value="down" class="btn btn-link p-0"><i
                                                            class="fas fa-arrow-down"></i></button>
                                            </form>
                                            <form method="post"
                                                  action="/FeedbackCenter/FeedbackIndex.php?action=deleteFeature"
                                                  style="display:inline;"
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
                                        <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse"
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
                                            <strong>Website:</strong> <?= htmlspecialchars($report['website']) ?><br>
                                            <?php if (!empty($report['url'])): ?>
                                                <strong>URL:</strong> <?= htmlspecialchars($report['url']) ?><br>
                                            <?php endif; ?>
                                            <pre class="mb-2"
                                                 style="white-space: pre-wrap; word-break: break-word;"><?= htmlspecialchars($report['description']) ?></pre>
                                            <?php if ($report['screenshot']): ?>
                                                <div class="mb-3">
                                                    <a href="/FeedbackCenter/txt/bug_screenshots/<?= urlencode($report['screenshot']) ?>"
                                                       target="_blank">
                                                        <img src="/FeedbackCenter/txt/bug_screenshots/<?= urlencode($report['screenshot']) ?>"
                                                             alt="Screenshot" class="img-thumbnail"
                                                             style="max-width:300px;">
                                                    </a>
                                                    <div class="form-text">
                                                        Screenshot: <?= htmlspecialchars($report['screenshot']) ?></div>
                                                </div>
                                            <?php endif; ?>
                                            <strong>Upvotes:</strong> <?= (int)$report['upvotes'] ?> |
                                            <strong>Downvotes:</strong> <?= (int)$report['downvotes'] ?>
                                            <form method="post"
                                                  action="/FeedbackCenter/FeedbackIndex.php?action=voteBug"
                                                  style="display:inline;">
                                                <input type="hidden" name="vote_bug_id"
                                                       value="<?= htmlspecialchars($report['id']) ?>">
                                                <button name="vote" value="up" class="btn btn-link p-0 ms-2"><i
                                                            class="fas fa-arrow-up"></i></button>
                                                <button name="vote" value="down" class="btn btn-link p-0"><i
                                                            class="fas fa-arrow-down"></i></button>
                                            </form>
                                            <form method="post"
                                                  action="/FeedbackCenter/FeedbackIndex.php?action=deleteBug"
                                                  style="display:inline;"
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
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    function setSeverity(severity) {
        document.getElementById('bug_severity').value = severity;
        // Remove active class from all pills
        document.querySelectorAll('#severityPills .nav-link').forEach(function (el) {
            el.classList.remove('active');
        });
        // Add active class to the clicked pill
        if (severity === 'Critical') document.getElementById('critical-pill').classList.add('active');
        if (severity === 'High') document.getElementById('high-pill').classList.add('active');
        if (severity === 'Medium') document.getElementById('medium-pill').classList.add('active');
        if (severity === 'Low') document.getElementById('low-pill').classList.add('active');
    }
</script>
</body>
