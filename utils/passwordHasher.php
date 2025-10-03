<!DOCTYPE html>
<html>
<head>
    <title>Password Hasher</title>
</head>
<body>
<h2>Password Hasher</h2>
<label for="password"></label><input type="password" id="password" placeholder="Enter password" />
<button onclick="hashPassword()">Hash Password</button>
<p>Hashed Password (SHA-256):</p>
<label for="output"></label><textarea id="output" rows="4" cols="70" readonly></textarea>

<script>
    async function hashPassword() {
        const password = document.getElementById('password').value;
        if (!password) {
            alert('Please enter a password');
            return;
        }
        const encoder = new TextEncoder();
        const data = encoder.encode(password);
        const hashBuffer = await crypto.subtle.digest('SHA-256', data);
        const hashArray = Array.from(new Uint8Array(hashBuffer));
        document.getElementById('output').value = hashArray.map(b => b.toString(16).padStart(2, '0')).join('');
    }
</script>


<h2>Server-Side PHP Password Hashing</h2>
<p>Dieses Beispiel zeigt, wie dein Passwort mit PHP sicher gehasht wird:</p>
<pre>

<?php
if (isset($_POST['password'])) {
    $password = $_POST['password'];
    $hash = password_hash($password, PASSWORD_ARGON2ID);
    echo "Gehashtes Passwort: <br>" . ($hash);
}
?>
    </pre>

<form method="post" action="">
    <label>
        <input type="password" name="password" placeholder="Enter password for PHP hash" required>
    </label>
    <button type="submit">Hash Password with PHP (Argon2id)</button>
</form>
</body>
</html>
