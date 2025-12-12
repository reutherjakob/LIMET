<!DOCTYPE html>
<html>
<head>
    <title>PHP Password Hasher - Alle Algorithmen</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        .algo-group { border: 1px solid #ddd; margin: 20px 0; padding: 15px; border-radius: 5px; }
        .algo-group h3 { margin-top: 0; color: #333; }
        select, input, button { padding: 8px; margin: 5px 0; border: 1px solid #ccc; border-radius: 4px; }
        button { background: #007cba; color: white; cursor: pointer; }
        button:hover { background: #005a87; }
        .result { background: #f8f9fa; padding: 10px; margin: 10px 0; border-left: 4px solid #007cba; word-break: break-all; }
        .error { background: #f8d7da; border-left-color: #dc3545; color: #721c24; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #f1f1f1; }
    </style>
</head>
<body>
<h1>PHP Password Hasher - Alle Algorithmen testen</h1>
<p>Teste alle verfügbaren PHP Password Hashing Algorithmen mit einem Blick:</p>

<?php
session_start();

// Verfügbare Algorithmen mit Namen und Optionen
$algorithms = [
    PASSWORD_DEFAULT => ['name' => 'PASSWORD_DEFAULT (aktuell bcrypt)', 'options' => []],
    PASSWORD_BCRYPT => ['name' => 'bcrypt', 'options' => ['cost' => 12]],
    PASSWORD_ARGON2I => ['name' => 'Argon2i (Memory-hard)', 'options' => ['memory_cost' => 65536, 'time_cost' => 4, 'threads' => 1]],
    PASSWORD_ARGON2ID => ['name' => 'Argon2id (Hybrid)', 'options' => ['memory_cost' => 65536, 'time_cost' => 4, 'threads' => 1]],
];

if (isset($_POST['password']) && isset($_POST['algo'])) {
    $password = $_POST['password'];
    $algo = (int)$_POST['algo'];

    if (!in_array($algo, array_keys($algorithms))) {
        echo '<div class="result error">Ungültiger Algorithmus!</div>';
    } else {
        $options = $algorithms[$algo]['options'];
        $hash = password_hash($password, $algo, $options);
        $_SESSION['last_hash'] = ['hash' => $hash, 'algo' => $algo, 'password' => $password];
        $_SESSION['all_hashes'][$algo] = $hash;
        echo '<div class="result">';
        echo '<strong>' . htmlspecialchars($algorithms[$algo]['name']) . ':</strong><br>';
        echo '<code>' . htmlspecialchars($hash) . '</code>';
        echo '</div>';
    }
}

if (isset($_POST['verify_password']) && isset($_POST['verify_algo'])) {
    $verifyPassword = $_POST['verify_password'];
    $verifyAlgo = (int)$_POST['verify_algo'];

    if (!isset($_SESSION['last_hash']) || !isset($_SESSION['last_hash']['hash'])) {
        echo '<div class="result error"><b>Kein gehashtes Passwort zum Verifizieren!</b></div>';
    } else {
        $storedHash = $_SESSION['last_hash']['hash'];
        if (password_verify($verifyPassword, $storedHash)) {
            echo '<div class="result"><b>✅ Passwort stimmt überein!</b></div>';
        } else {
            echo '<div class="result error"><b>❌ Passwort stimmt nicht überein.</b></div>';
        }
    }
}

// Vergleichstabelle aller gespeicherten Hashes
if (isset($_SESSION['all_hashes']) && count($_SESSION['all_hashes']) > 0) {
    echo '<div class="algo-group">';
    echo '<h3>Alle generierten Hashes (Vergleich)</h3>';
    echo '<table>';
    echo '<tr><th>Algorithmus</th><th>Hash</th><th>Länge</th></tr>';
    foreach ($_SESSION['all_hashes'] as $algo => $hash) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($algorithms[$algo]['name']) . '</td>';
        echo '<td><code style="font-size: 0.9em;">' . htmlspecialchars(substr($hash, 0, 60)) . '...</code></td>';
        echo '<td>' . strlen($hash) . ' Zeichen</td>';
        echo '</tr>';
    }
    echo '</table>';
    echo '</div>';
}
?>

<!-- Hashing Formular -->
<div class="algo-group">
    <h3>🔐 Passwort hashen</h3>
    <form method="post" action="">
        <label>Algorithmus auswählen:
            <select name="algo" required>
                <?php foreach ($algorithms as $algo => $info): ?>
                    <option value="<?= $algo ?>"><?= htmlspecialchars($info['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </label><br>
        <label>Passwort:<br>
            <input type="password" name="password" placeholder="Passwort eingeben" required style="width: 300px;">
        </label><br>
        <button type="submit">Hash generieren</button>
    </form>
</div>

<!-- Verification Formular -->
<div class="algo-group">
    <h3>🔍 Passwort verifizieren</h3>
    <form method="post" action="">
        <label>Algorithmus (vom letzten Hash):<br>
            <select name="verify_algo" required>
                <?php
                $selectedAlgo = isset($_SESSION['last_hash']) ? $_SESSION['last_hash']['algo'] : PASSWORD_DEFAULT;
                foreach ($algorithms as $algo => $info):
                    ?>
                    <option value="<?= $algo ?>" <?= $algo == $selectedAlgo ? 'selected' : '' ?>>
                        <?= htmlspecialchars($info['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label><br>
        <label>Zum Testen:<br>
            <input type="password" name="verify_password" placeholder="Passwort zum Testen" required style="width: 300px;">
        </label><br>
        <button type="submit">Verifizieren</button>
    </form>
</div>

<!-- Info Box -->
<div class="algo-group">
    <h3>ℹ️ Algorithmus-Info</h3>
    <ul>
        <li><strong>Argon2id</strong>: Modernster Standard, resistent gegen GPU/ASIC Attacks</li>
        <li><strong>Argon2i</strong>: Memory-hard, gegen Side-Channel Attacks</li>
        <li><strong>bcrypt</strong>: Bewährter Standard (PASSWORD_DEFAULT)</li>
    </ul>
    <p><strong>Empfehlung:</strong> Verwende immer <code>PASSWORD_DEFAULT</code> oder <code>PASSWORD_ARGON2ID</code> für Production!</p>
</div>

<!-- Reset Button -->
<form method="post" action="" style="text-align: center; margin-top: 20px;">
    <button type="submit" name="reset" style="background: #dc3545;">Session zurücksetzen</button>
</form>

<?php
if (isset($_POST['reset'])) {
    session_destroy();
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}
?>

</body>
</html>
