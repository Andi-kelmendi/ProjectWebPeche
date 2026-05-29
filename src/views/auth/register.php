<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/auth.css">
    <title>Créer un compte – WebPêche</title>
</head>
<body class="auth-body">

<div class="auth-container">

    <!-- ======================================================
         PARTIE GAUCHE — Formulaire d'inscription
    ====================================================== -->
    <div class="auth-left">
        <a href="/" class="auth-logo">WebPêche</a>

        <div class="auth-form-wrapper">
            <h1>Crée un compte</h1>
            <p class="auth-switch">
                Vous avez déjà un compte ?
                <a href="/login">Se connecter</a>
            </p>

            <!-- Affichage des erreurs -->
            <?php if (!empty($errors)): ?>
                <div class="auth-errors">
                    <?php foreach ($errors as $error): ?>
                        <p>⚠️ <?= htmlspecialchars($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="auth-form">

                <div class="input-group">
                    <span class="input-icon">👤</span>
                    <input
                        type="text"
                        name="username"
                        placeholder="Pseudo"
                        value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                        required
                        minlength="3"
                        autocomplete="username"
                    >
                </div>

                <div class="input-group">
                    <span class="input-icon">✉️</span>
                    <input
                        type="email"
                        name="email"
                        placeholder="Email"
                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                        required
                        autocomplete="email"
                    >
                </div>

                <div class="input-group">
                    <span class="input-icon">🔒</span>
                    <input
                        type="password"
                        name="password"
                        id="password"
                        placeholder="Mot de passe (min. 8 caractères)"
                        required
                        minlength="8"
                        autocomplete="new-password"
                    >
                    <button type="button" class="toggle-password" onclick="togglePassword('password', this)">
                        👁️
                    </button>
                </div>

                <div class="input-group">
                    <span class="input-icon">🔒</span>
                    <input
                        type="password"
                        name="confirm"
                        id="confirm"
                        placeholder="Confirmer le mot de passe"
                        required
                        autocomplete="new-password"
                    >
                    <button type="button" class="toggle-password" onclick="togglePassword('confirm', this)">
                        👁️
                    </button>
                </div>

                <label class="checkbox-label">
                    <input type="checkbox" name="remember"> Se souvenir de moi
                </label>

                <button class="btn-auth" onclick="submitRegister()">Créer mon compte</button>
            </div>
        </div>
    </div>

    <!-- ======================================================
         PARTIE DROITE — Carte décorative
    ====================================================== -->
    <div class="auth-right">
        <img src="/assets/img/map-preview.jpg" alt="Carte des spots" class="map-preview">
        <div class="map-overlay-btn">
            <span>⋮</span>
        </div>
    </div>

</div>

<script>
    function togglePassword(inputId, btn) {
        const input = document.getElementById(inputId);
        input.type  = input.type === 'password' ? 'text' : 'password';
        btn.textContent = input.type === 'password' ? '👁️' : '🙈';
    }

    function submitRegister() {
        // Vérification côté client que les mots de passe correspondent
        const pw  = document.getElementById('password').value;
        const cnf = document.getElementById('confirm').value;

        if (pw !== cnf) {
            alert('Les mots de passe ne correspondent pas.');
            return;
        }

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/register';

        const fields = {
            username: document.querySelector('input[name="username"]').value,
            email:    document.querySelector('input[name="email"]').value,
            password: pw,
            confirm:  cnf,
            remember: document.querySelector('input[name="remember"]').checked ? '1' : ''
        };

        for (const [name, value] of Object.entries(fields)) {
            const input = document.createElement('input');
            input.type  = 'hidden';
            input.name  = name;
            input.value = value;
            form.appendChild(input);
        }

        document.body.appendChild(form);
        form.submit();
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') submitRegister();
    });
</script>
</body>
</html>