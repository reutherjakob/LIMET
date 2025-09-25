
<!DOCTYPE html>
<html>
<head>
    <title>Password Hasher</title>
</head>
<body>
    <h2>Password Hasher</h2>
    <input type="password" id="password" placeholder="Enter password" />
    <button onclick="hashPassword()">Hash Password</button>
    <p>Hashed Password (SHA-256):</p>
    <textarea id="output" rows="4" cols="70" readonly></textarea>

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
            const hashHex = hashArray.map(b => b.toString(16).padStart(2, '0')).join('');
            document.getElementById('output').value = hashHex;
        }
    </script>
</body>
</html>
