<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8"/>
    <title>Argon2id PasswordEncrypter </title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"/>
</head>
<body class="bg-light">
<div class="container mt-5" style="max-width: 650px;">
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">🔐 Argon2id PasswordEncrypter </h5>
        </div>
        <div class="card-body">

            <?php
            $hash = '';
            $msg  = '';
            $verified = null;

            // Argon2id options — gleich wie in change_pw.php (PHP defaults)
            $argon_options = [
                'memory_cost' => 65536,
                'time_cost'   => 4,
                'threads'     => 1,
            ];

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {

                if (isset($_POST['action']) && $_POST['action'] === 'hash') {
                    $pw = $_POST['password'] ?? '';
                    if ($pw === '') {
                        $msg = '<div class="alert alert-warning">Bitte ein Passwort eingeben.</div>';
                    } else {
                        $hash = password_hash($pw, PASSWORD_ARGON2ID, $argon_options);
                    }
                }

                if (isset($_POST['action']) && $_POST['action'] === 'verify') {
                    $pw   = $_POST['verify_pw']   ?? '';
                    $stored = $_POST['verify_hash'] ?? '';
                    if ($pw === '' || $stored === '') {
                        $msg = '<div class="alert alert-warning">Bitte Passwort und Hash eingeben.</div>';
                    } else {
                        $verified = password_verify($pw, $stored);
                    }
                }
            }
            ?>

            <?= $msg ?>

            <!-- Hash generieren -->
            <form method="post" autocomplete="off">
                <input type="hidden" name="action" value="hash"/>
                <label class="form-label fw-semibold">Passwort → Hash generieren</label>
                <div class="input-group mb-2">
                    <input type="password" name="password" class="form-control"
                           placeholder="Passwort eingeben" required/>
                    <button class="btn btn-success" type="submit">Hash generieren</button>
                </div>
            </form>

            <?php if ($hash): ?>
                <div class="alert alert-success mt-2">
                    <small class="text-muted">Argon2id Hash:</small><br/>
                    <code style="word-break: break-all;"><?= htmlspecialchars($hash) ?></code>
                    <button class="btn btn-sm btn-outline-secondary mt-2"
                            onclick="navigator.clipboard.writeText(this.dataset.hash)"
                            data-hash="<?= htmlspecialchars($hash) ?>">
                        📋 Kopieren
                    </button>
                </div>
            <?php endif; ?>

            <hr/>

            <!-- Verifizieren -->
            <form method="post" autocomplete="off">
                <input type="hidden" name="action" value="verify"/>
                <label class="form-label fw-semibold">Passwort gegen Encrypted verifizieren</label>
                <input type="password" name="verify_pw" class="form-control mb-2"
                       placeholder="Passwort" required/>
                <textarea name="verify_hash" class="form-control mb-2" rows="3"
                          placeholder="Hash hier einfügen …" required></textarea>
                <button class="btn btn-primary" type="submit">Verifizieren</button>
            </form>

            <?php if ($verified !== null): ?>
                <?php if ($verified): ?>
                    <div class="alert alert-success mt-2">✅ Passwort stimmt überein!</div>
                <?php else: ?>
                    <div class="alert alert-danger mt-2">❌ Passwort stimmt NICHT überein.</div>
                <?php endif; ?>
            <?php endif; ?>

        </div>
        <div class="card-footer text-muted" style="font-size: 0.8em;">
            Algorithmus: <code>PASSWORD_ARGON2ID</code> —
            memory_cost: 65536 | time_cost: 4 | threads: 1
        </div>
    </div>
</div>
</body>
</html>