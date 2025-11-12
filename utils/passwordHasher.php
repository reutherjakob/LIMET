<!DOCTYPE html>
<html>
<head>
    <title>Password Hasher</title>
</head>
<body>
<h2>Server-Side PHP Password Hashing and Verification</h2>
<p>Dieses Beispiel zeigt, wie dein Passwort mit PHP sicher gehasht wird und wie du ein Passwort prüfst:</p>

<?php
session_start();

if (isset($_POST['password'])) {
    $password = $_POST['password'];
    $hash = password_hash($password, PASSWORD_ARGON2ID);
    $_SESSION['hash'] = $hash;
    echo "Gehashtes Passwort: <br>" . $hash . " <br> ".    $_SESSION['hash'] ;
}


if (isset($_POST['verify_password'])) {
    $verifyPassword = $_POST['verify_password'];
    if (!isset($_SESSION['hash'])) {
        echo "<br><b>Kein gehashtes Passwort zum Verifizieren vorhanden!</b>";
    } else {
        if (password_verify($verifyPassword, $_SESSION['hash'])) {
            echo "<br><b>Passwort stimmt überein!</b>";
        } else {
            echo "<br><b>Passwort stimmt nicht überein.</b>";
        }
    }
}
?>

<form method="post" action="">
    <label>
        Neues Passwort zum Hashen:<br>
        <input type="password" name="password" placeholder="Enter password for PHP hash" required>
    </label>
    <button type="submit">Hash Password with PHP (Argon2id)</button>
</form>


<form method="post" action="">
    <label>
        Passwort zum Prüfen:<br>
        <input type="password" name="verify_password" placeholder="Enter password to verify" required>
    </label>
    <button type="submit">Verify Password</button>
</form>
</body>
</html>
