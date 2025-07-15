<?php

class FeedbackModel
{
    private $wishlistFile = __DIR__ . '/wishlist.txt';
    private $bugReportFile = __DIR__ . '/bugreports.txt';
    private $uploadDir = __DIR__ . '/uploads/';
    private $maxFileSize = 5 * 1024 * 1024; // 5 MB

    public function __construct()
    {
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0775, true);
        }
    }

// Generate unique IDs
    private function generateId($prefix, $file)
    {
        $date = date('Ymd');
        $counter = 1;
        $existing = [];
        if (file_exists($file)) {
            $content = file_get_contents($file);
            preg_match_all('/^ID: (' . $prefix . '-\d{8}-\d{4})/m', $content, $matches);
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

    public function getWishlist()
    {
        $wishlist = [];
        if (file_exists($this->wishlistFile)) {
            $content = file_get_contents($this->wishlistFile);
            $rawEntries = explode('------------------------', $content);
            $rawEntries = array_filter(array_map('trim', $rawEntries));
            foreach ($rawEntries as $entry) {
                if ($entry) {
                    $lines = explode("\n", $entry);
                    $data = [
                        'id' => '', 'date' => '', 'website' => '',
                        'title' => '', 'description' => '', 'upvotes' => 0, 'downvotes' => 0
                    ];
                    foreach ($lines as $line) {
                        if (strpos($line, 'ID: ') === 0) $data['id'] = substr($line, 4);
                        elseif (strpos($line, 'Date: ') === 0) $data['date'] = substr($line, 6);
                        elseif (strpos($line, 'Website: ') === 0) $data['website'] = substr($line, 9);
                        elseif (strpos($line, 'Title: ') === 0) $data['title'] = substr($line, 7);
                        elseif (strpos($line, 'Description: ') === 0) $data['description'] = substr($line, 13);
                        elseif (strpos($line, 'Upvotes: ') === 0) $data['upvotes'] = (int)substr($line, 9);
                        elseif (strpos($line, 'Downvotes: ') === 0) $data['downvotes'] = (int)substr($line, 11);
                        elseif (trim($line) !== '') $data['description'] .= "\n" . trim($line);
                    }
                    $wishlist[] = $data;
                }
            }
            $wishlist = array_slice($wishlist, -50);
        }
        return $wishlist;
    }

    public function getBugReports(): array
    {
        $bugReports = [];
        if (file_exists($this->bugReportFile)) {
            $content = file_get_contents($this->bugReportFile);
            $rawEntries = explode('------------------------', $content);
            $rawEntries = array_filter(array_map('trim', $rawEntries));
            foreach ($rawEntries as $entry) {
                if ($entry) {
                    $lines = explode("\n", $entry);
                    $data = [
                        'id' => '', 'date' => '', 'website' => '',
                        'title' => '', 'description' => '', 'screenshot' => '',
                        'url' => '', 'upvotes' => 0, 'downvotes' => 0
                    ];
                    foreach ($lines as $line) {
                        if (strpos($line, 'ID: ') === 0) $data['id'] = substr($line, 4);
                        elseif (strpos($line, 'Date: ') === 0) $data['date'] = substr($line, 6);
                        elseif (strpos($line, 'Website: ') === 0) $data['website'] = substr($line, 9);
                        elseif (strpos($line, 'Title: ') === 0) $data['title'] = substr($line, 7);
                        elseif (strpos($line, 'Bug Description: ') === 0) $data['description'] = substr($line, 17);
                        elseif (strpos($line, 'Screenshot: ') === 0) $data['screenshot'] = trim(substr($line, 11));
                        elseif (strpos($line, 'URL: ') === 0) $data['url'] = trim(substr($line, 5));
                        elseif (strpos($line, 'Upvotes: ') === 0) $data['upvotes'] = (int)substr($line, 9);
                        elseif (strpos($line, 'Downvotes: ') === 0) $data['downvotes'] = (int)substr($line, 11);
                        elseif (trim($line) !== '') $data['description'] .= "\n" . trim($line);
                    }
                    $bugReports[] = $data;
                }
            }
            $bugReports = array_slice($bugReports, -50);
        }
        return $bugReports;
    }

    public function addFeature($website, $title, $desc): string
    {
        $website = trim($website);
        $title = trim($title);
        $desc = trim($desc);
        if (!$website || !$title || !$desc) return "Bitte Website, Titel und Beschreibung angeben.";
        $featureId = $this->generateId('WISH', $this->wishlistFile);
        $entry = "ID: $featureId\n";
        $entry .= "Date: " . date('Y-m-d H:i:s') . "\n";
        $entry .= "Website: " . htmlspecialchars($website) . "\n";
        $entry .= "Title: " . htmlspecialchars($title) . "\n";
        $entry .= "Description: " . htmlspecialchars($desc) . "\n";
        $entry .= "Upvotes: 0\n";
        $entry .= "Downvotes: 0\n";
        $entry .= $_SESSION["username"] . "\n";
        $entry .= "------------------------\n";
        file_put_contents($this->wishlistFile, $entry, FILE_APPEND | LOCK_EX);
        return "Danke für deinen Vorschlag!";
    }

    public function addBug($website, $title, $desc, $file, $url = ''): string
    {
        $website = trim($website);
        $title = trim($title);
        $desc = trim($desc);
        $url = trim($url);
        $screenshotFilename = '';
        if (!$website || !$title || !$desc) return "Bitte Website, Titel und Beschreibung angeben.";
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
                return "Ungültige Screenshot-Datei. Bitte lade ein gültiges Bild (jpg, png, gif, webp) bis 5 MB hoch.";
            }
        }
        $bugId = $this->generateId('BUG', $this->bugReportFile);
        $entry = "ID: $bugId\n";
        $entry .= "Date: " . date('Y-m-d H:i:s') . "\n";
        $entry .= "Website: " . htmlspecialchars($website) . "\n";
        $entry .= "Title: " . htmlspecialchars($title) . "\n";
        $entry .= "Bug Description: " . htmlspecialchars($desc) . "\n";
        if ($url) $entry .= "URL: " . htmlspecialchars($url) . "\n";
        if ($screenshotFilename) $entry .= "Screenshot: " . $screenshotFilename . "\n";
        $entry .= "Upvotes: 0\n";
        $entry .= "Downvotes: 0\n";
        $entry .= $_SESSION["username"] . "\n";
        $entry .= "------------------------\n";
        file_put_contents($this->bugReportFile, $entry, FILE_APPEND | LOCK_EX);
        return "Danke für deinen Bug-Report!";
    }

    public function voteFeature($id, $direction): string
    {
        if (!$id || !in_array($direction, ['up', 'down'])) return "Ungültige Abstimmung.";
        if (!file_exists($this->wishlistFile)) return "Feature-Liste nicht gefunden.";
        $content = file_get_contents($this->wishlistFile);
        $entries = explode('------------------------', $content);
        $newContent = '';
        foreach ($entries as $entry) {
            if (strpos($entry, "ID: $id") !== false) {
                $lines = explode("\n", trim($entry));
                foreach ($lines as &$line) {
                    if (strpos($line, 'Upvotes: ') === 0 && $direction === 'up') {
                        $votes = (int)substr($line, 9) + 1;
                        $line = "Upvotes: $votes";
                    }
                    if (strpos($line, 'Downvotes: ') === 0 && $direction === 'down') {
                        $votes = (int)substr($line, 11) + 1;
                        $line = "Downvotes: $votes";
                    }
                }
                $entry = implode("\n", $lines);
            }
            if (trim($entry)) $newContent .= trim($entry) . "\n------------------------\n";
        }
        file_put_contents($this->wishlistFile, $newContent);
        return "Abstimmung gespeichert.";
    }

    public function voteBug($id, $direction): string
    {
        if (!$id || !in_array($direction, ['up', 'down'])) return "Ungültige Abstimmung.";
        if (!file_exists($this->bugReportFile)) return "Bug-Liste nicht gefunden.";
        $content = file_get_contents($this->bugReportFile);
        $entries = explode('------------------------', $content);
        $newContent = '';
        foreach ($entries as $entry) {
            if (strpos($entry, "ID: $id") !== false) {
                $lines = explode("\n", trim($entry));
                foreach ($lines as &$line) {
                    if (strpos($line, 'Upvotes: ') === 0 && $direction === 'up') {
                        $votes = (int)substr($line, 9) + 1;
                        $line = "Upvotes: $votes";
                    }
                    if (strpos($line, 'Downvotes: ') === 0 && $direction === 'down') {
                        $votes = (int)substr($line, 11) + 1;
                        $line = "Downvotes: $votes";
                    }
                }
                $entry = implode("\n", $lines);
            }
            if (trim($entry)) $newContent .= trim($entry) . "\n------------------------\n";
        }
        file_put_contents($this->bugReportFile, $newContent);
        return "Abstimmung gespeichert.";
    }

    public function deleteFeature($id): string
    {
        if (!$id) return "Ungültige Feature-ID.";
        if (file_exists($this->wishlistFile) && strpos($_SESSION["username"], "fuchs") == 0) {
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
        return "Deleting feature request not possible. Ask a Dev.";
    }

    public function deleteBug($id): string
    {
        if (!$id) return "Ungültige Bug-Report-ID.";
        if (file_exists($this->bugReportFile) && strpos($_SESSION["username"], "fuchs") == 0) {
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
        return "Deleting Bug report file not possible. Ask a dev.";
    }
}
