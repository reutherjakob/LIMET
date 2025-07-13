<?php
$logos = [
    'Logo/LIMET_web.png',
    'Logo/MADER_Logo.png',
    'Logo/ARGE_LIMET-CFM_Logo_03.png',
];

echo "<h2>Logo-Test</h2>";
echo "<table border='1' cellpadding='6' style='border-collapse:collapse;'>";
echo "<tr><th>Dateipfad</th><th>Status</th><th>Vorschau</th></tr>";

foreach ($logos as $logo) {
    echo "<tr>";
    echo "<td>$logo</td>";
    if (file_exists($logo)) {
        echo "<td style='color:green;'>Gefunden</td>";
        echo "<td><img src='$logo' height='40' alt='Logo'></td>";
    } else {
        echo "<td style='color:red;'>Nicht gefunden</td>";
        echo "<td>-</td>";
    }
    echo "</tr>";
}
echo "</table>";
?>

