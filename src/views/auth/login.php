<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/auth.css">
    <title>Connexion – WebPêche</title>
</head>
<body class="auth-body">

<div class="auth-container">

    <!-- ======================================================
         PARTIE GAUCHE — Formulaire de connexion
    ====================================================== -->
    <div class="auth-left">
        <a href="/" class="auth-logo">WebPêche</a>

        <div class="auth-form-wrapper">
            <h1>Se connecter</h1>
            <p class="auth-switch">
                Pas encore de compte ?
                <a href="/register">Créer un compte</a>
            </p>

            <!-- Affichage des erreurs si il y en a -->
            <?php if (!empty($errors)): ?>
                <div class="auth-errors">
                    <?php foreach ($errors as $error): ?>
                        <p>⚠️ <?= htmlspecialchars($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Formulaire -->
            <!-- action="" = envoie vers la même URL, method POST -->
            <div class="auth-form">

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
                        placeholder="Mot de passe"
                        required
                        autocomplete="current-password"
                    >
                    <!-- Bouton pour afficher/masquer le mot de passe -->
                    <button type="button" class="toggle-password" onclick="togglePassword('password', this)">
                        👁️
                    </button>
                </div>

                <label class="checkbox-label">
                    <input type="checkbox" name="remember"> Se souvenir de moi
                </label>

                <!-- Bouton qui soumet le formulaire via JS (on évite <form>) -->
                <button class="btn-auth" onclick="submitLogin()">Login</button>

                <a href="/forgot-password" class="forgot-link">Mot de passe oublié ?</a>
            </div>
        </div>
    </div>

    <!-- ======================================================
         PARTIE DROITE — Carte décorative
    ====================================================== -->
    <div class="auth-right">
        <!-- Image de carte statique comme décoration -->
        <!-- Plus tard on pourra mettre une vraie carte ici -->
        <img src="/assets/img/map-preview.jpg" alt="Carte des spots" class="map-preview">
        <div class="map-overlay-btn">
            <span>⋮</span>
        </div>
    </div>

</div>

<script>
    // Affiche ou masque le mot de passe
    function togglePassword(inputId, btn) {
        const input = document.getElementById(inputId);
        if (input.type === 'password') {
            input.type = 'text';
            btn.textContent = '🙈';
        } else {
            input.type = 'password';
            btn.textContent = '👁️';
        }
    }

    // Soumet le formulaire via une vraie requête POST
    function submitLogin() {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/login';

        const fields = {
            email:    document.querySelector('input[name="email"]').value,
            password: document.querySelector('input[name="password"]').value,
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

    // Permet aussi d'appuyer sur Entrée pour soumettre
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') submitLogin();
    });
</script>
</body>
</html>