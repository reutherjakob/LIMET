<?php
include "_utils.php";
check_login();

$wishlistFile = 'txt/feature_wishlist.txt';

// Helper: Generate unique feature ID
function generate_feature_id() {
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

// Handle new wishlist submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST['delete'])) {
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
}

// Handle deletion of a feature request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete']) && isset($_POST['feature_index'])) {
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
}

// Fetch and parse wishlist entries
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Feature Wishlist</title>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css"
          integrity="sha512-q3eWabyZPc1XTCmF+8/LuE1ozpg5xxn7iO89yfSOd5/oKvyqLngoNGsx8jq92Y8eXJ/IRxQbEC+FGSYxtk2oiw=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
</head>
<body>
<div class="container py-5">
    <div class="row">
        <div class="col-lg-6 mx-auto">
            <h1 class="mb-4 text-center"><i class="fas fa-lightbulb"></i> Feature Wishlist</h1>
            <?php if (!empty($message)): ?>
                <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            <div class="alert alert-secondary">
                Suggest a new feature or improvement for this project.<br>

            </div>
            <form method="post" action="_feature_wishlist.php" class="needs-validation" novalidate>
                <div class="mb-3">
                    <label for="feature_title" class="form-label">Feature Title</label>
                    <input id="feature_title" name="feature_title" class="form-control" maxlength="120" required>
                    <div class="invalid-feedback">Please provide a short title for the feature.</div>
                </div>
                <div class="mb-3">
                    <label for="feature_description" class="form-label">Feature Description</label>
                    <textarea id="feature_description" name="feature_description" class="form-control" rows="7" required></textarea>
                    <div class="invalid-feedback">Please describe the feature in detail.</div>
                </div>
                <button type="submit" class="btn btn-success w-100">
                    <i class="fas fa-paper-plane"></i> Submit Feature Request
                </button>
            </form>
        </div>
    </div>

    <hr class="my-5">

    <div class="row">
        <div class="col-lg-10 mx-auto">
            <h2 class="mb-3 text-center"><i class="fas fa-list"></i> Wishlist Entries</h2>
            <?php if (empty($wishlist)): ?>
                <div class="alert alert-light text-center">No feature requests yet.</div>
            <?php else: ?>
                <div class="accordion" id="wishlistAccordion">
                    <?php foreach (array_reverse($wishlist, true) as $idx => $entry): ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="wishHeading<?php echo $idx; ?>">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#wishCollapse<?php echo $idx; ?>" aria-expanded="false" aria-controls="wishCollapse<?php echo $idx; ?>">
                                    <span class="badge bg-info me-2"><?php echo htmlspecialchars($entry['id']); ?></span>
                                    <strong><?php echo htmlspecialchars($entry['title']); ?></strong>
                                    <span class="ms-2 text-muted small"><i class="fas fa-calendar-alt"></i> <?php echo htmlspecialchars($entry['date']); ?></span>
                                </button>
                            </h2>
                            <div id="wishCollapse<?php echo $idx; ?>" class="accordion-collapse collapse" aria-labelledby="wishHeading<?php echo $idx; ?>" data-bs-parent="#wishlistAccordion">
                                <div class="accordion-body">
                                    <pre class="mb-2" style="white-space: pre-wrap; word-break: break-word;"><?php echo htmlspecialchars($entry['description']); ?></pre>
                                    <form method="post" action="_feature_wishlist.php" onsubmit="return confirm('Are you sure you want to delete this feature request?');">
                                        <input type="hidden" name="feature_index" value="<?php echo count($wishlist) - $idx - 1; ?>">
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
