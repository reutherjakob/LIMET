<?php
class FeedbackModel {
    private $wishlistFile = __DIR__ . '/txt/feature_wishlist.txt';
    private $bugReportFile = __DIR__ . '/txt/bug_reports.txt';
    private $uploadDir = __DIR__ . '/txt/bug_screenshots/';
    private $maxFileSize = 5242880; // 5 MB

    public function __construct() {
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0775, true);
        }
    }

    // Generate unique IDs
    private function generateId($prefix, $file) {
        $date = date('Ymd');
        $counter = 1;
        $existing = [];
        if (file_exists($file)) {
            $content = file_get_contents($file);
            preg_match_all('/^ID: ('.$prefix.'-\d{8}-\d{4})/m', $content, $matches);
            if (!empty($matches[1])) {
                $existing = $matches[1];
            }
        }
        do {
            $id = "$prefix-$date-" . str_pad($counter, 4, '0', STR_PAD_LEFT);
            $counter++;
        } while (in_array($id, $existing));
        return $id;
    }

    public function getWishlist() {
        $wishlist = [];
        if (file_exists($this->wishlistFile)) {
            $content = file_get_contents($this->wishlistFile);
            $rawEntries = explode('------------------------', $content);
            $rawEntries = array_filter(array_map('trim', $rawEntries));
            foreach ($rawEntries as $entry) {
                if ($entry) {
                    $lines = explode("\n", $entry);
                    $id = $date = $title = $description = '';
                    foreach ($lines as $line) {
                        if (strpos($line, 'ID: ') === 0) $id = substr($line, 4);
                        elseif (strpos($line, 'Date: ') === 0) $date = substr($line, 6);
                        elseif (strpos($line, 'Title: ') === 0) $title = substr($line, 7);
                        elseif (strpos($line, 'Description: ') === 0) $description = substr($line, 13);
                        elseif (trim($line) !== '') $description .= "\n" . trim($line);
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
        return $wishlist;
    }

    public function getBugReports() {
        $bugReports = [];
        if (file_exists($this->bugReportFile)) {
            $content = file_get_contents($this->bugReportFile);
            $rawEntries = explode('------------------------', $content);
            $rawEntries = array_filter(array_map('trim', $rawEntries));
            foreach ($rawEntries as $entry) {
                if ($entry) {
                    $lines = explode("\n", $entry);
                    $id = $date = $title = $description = $screenshot = '';
                    foreach ($lines as $line) {
                        if (strpos($line, 'ID: ') === 0) $id = substr($line, 4);
                        elseif (strpos($line, 'Date: ') === 0) $date = substr($line, 6);
                        elseif (strpos($line, 'Title: ') === 0) $title = substr($line, 7);
                        elseif (strpos($line, 'Bug Description: ') === 0) $description = substr($line, 17);
                        elseif (strpos($line, 'Screenshot: ') === 0) $screenshot = trim(substr($line, 11));
                        elseif (trim($line) !== '') $description .= "\n" . trim($line);
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
        return $bugReports;
    }

    public function addFeature($title, $desc) {
        $title = trim($title);
        $desc = trim($desc);
        if (!$title || !$desc) return "Please provide a title and description for your feature request.";
        $featureId = $this->generateId('WISH', $this->wishlistFile);
        $entry = "ID: $featureId\n";
        $entry .= "Date: " . date('Y-m-d H:i:s') . "\n";
        $entry .= "Title: " . htmlspecialchars($title) . "\n";
        $entry .= "Description: " . htmlspecialchars($desc) . "\n";
        $entry .= "------------------------\n";
        file_put_contents($this->wishlistFile, $entry, FILE_APPEND | LOCK_EX);
        return "Thank you for your suggestion!";
    }

    public function addBug($title, $desc, $file) {
        $title = trim($title);
        $desc = trim($desc);
        $screenshotFilename = '';
        if (!$title || !$desc) return "Please provide a bug title and description.";

        // Handle file upload
        if ($file && !empty($file['name'])) {
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $allowedMime = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);
            if (
                $file['error'] === UPLOAD_ERR_OK &&
                in_array($ext, $allowedExt) &&
                in_array($mimeType, $allowedMime) &&
                $file['size'] <= $this->maxFileSize
            ) {
                $uniqueName = uniqid('bugshot_', true) . '.' . $ext;
                if (move_uploaded_file($file['tmp_name'], $this->uploadDir . $uniqueName)) {
                    $screenshotFilename = $uniqueName;
                }
            } else {
                return "Invalid screenshot file. Please upload a valid image (jpg, png, gif, webp) up to 5 MB.";
            }
        }

        $bugId = $this->generateId('BUG', $this->bugReportFile);
        $entry = "ID: $bugId\n";
        $entry .= "Date: " . date('Y-m-d H:i:s') . "\n";
        $entry .= "Title: " . htmlspecialchars($title) . "\n";
        $entry .= "Bug Description: " . htmlspecialchars($desc) . "\n";
        if ($screenshotFilename) {
            $entry .= "Screenshot: " . $screenshotFilename . "\n";
        }
        $entry .= "------------------------\n";
        file_put_contents($this->bugReportFile, $entry, FILE_APPEND | LOCK_EX);
        return "Thank you for your report! Please make sure you included the webpage and enough details to help us reproduce the bug.";
    }

    public function deleteFeature($id) {

        if (!$id) return "Invalid feature ID.";
        if (file_exists($this->wishlistFile)) {
            $content = file_get_contents($this->wishlistFile);
            $rawEntries = explode('------------------------', $content);
            $rawEntries = array_filter(array_map('trim', $rawEntries));
            $newContent = '';
            foreach ($rawEntries as $entry) {
                if (preg_match('/^ID: (.+)$/m', $entry, $match) && trim($match[1]) === $id) {
                    continue; // skip this entry
                }
                $newContent .= trim($entry) . "\n------------------------\n";
            }
            file_put_contents($this->wishlistFile, $newContent);
            return "Feature request deleted.";
        }
        return "Feature file not found.";
    }

    public function deleteBug($id) {

        if (!$id) return "Invalid bug report ID.";
        if (file_exists($this->bugReportFile)) {
            $content = file_get_contents($this->bugReportFile);
            $rawEntries = explode('------------------------', $content);
            $rawEntries = array_filter(array_map('trim', $rawEntries));
            $newContent = '';
            foreach ($rawEntries as $entry) {
                if (preg_match('/^ID: (.+)$/m', $entry, $match) && trim($match[1]) === $id) {
                    // Delete screenshot if present
                    if (preg_match('/^Screenshot: (.+)$/m', $entry, $smatch)) {
                        $screenshot = trim($smatch[1]);
                        if ($screenshot && file_exists($this->uploadDir . $screenshot)) {
                            unlink($this->uploadDir . $screenshot);
                        }
                    }
                    continue; // skip this entry
                }
                $newContent .= trim($entry) . "\n------------------------\n";
            }
            file_put_contents($this->bugReportFile, $newContent);
            return "Bug report deleted.";
        }
        return "Bug report file not found.";
    }
}
