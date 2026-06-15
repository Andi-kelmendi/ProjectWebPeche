<?php
// ============================================================
// src/views/auth/login.php — Page de connexion
// ============================================================
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Se connecter — WebPêche</title>
    <link rel="stylesheet" href="/assets/css/auth.css">
    <!-- Icônes Font Awesome (chargé depuis le navigateur, pas besoin de CDN local) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="auth-container">

    <!-- ══════════════════════════════════════
         PANNEAU GAUCHE — Formulaire
    ══════════════════════════════════════ -->
    <div class="auth-panel">

        <!-- Logo -->
        <a href="/" class="auth-logo">
            <img src="/assets/img/logo.png" alt="WebPêche">
            <span class="auth-logo-text">WebPêche</span>
        </a>

        <!-- Titre -->
        <h1>Se connecter</h1>
        <p class="auth-subtitle">
            Vous n'avez pas de compte ?
            <a href="/register">Créer un compte</a>
        </p>

        <!-- Messages d'erreur / succès -->
        <?php if (!empty($_SESSION['auth_error'])): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($_SESSION['auth_error']) ?>
            </div>
            <?php unset($_SESSION['auth_error']); ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['auth_success'])): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($_SESSION['auth_success']) ?>
            </div>
            <?php unset($_SESSION['auth_success']); ?>
        <?php endif; ?>

        <!-- Formulaire de connexion -->
        <form class="auth-form" action="/login" method="POST">

            <!-- Email -->
            <div class="form-group">
                <i class="fa-regular fa-envelope field-icon"></i>
                <input
                    type="email"
                    name="email"
                    placeholder="Adresse email"
                    required
                    autocomplete="email"
                    value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                >
            </div>

            <!-- Mot de passe -->
            <div class="form-group">
                <i class="fa-solid fa-lock field-icon"></i>
                <input
                    type="password"
                    name="password"
                    id="login-pwd"
                    placeholder="Mot de passe"
                    required
                    autocomplete="current-password"
                >
                <button type="button" class="toggle-pwd" onclick="togglePwd('login-pwd', this)" title="Afficher / masquer">
                    <i class="fa-regular fa-eye"></i>
                </button>
            </div>

            <!-- Se souvenir + Mot de passe oublié -->
            <div class="form-extras">
                <label class="remember-label">
                    <input type="checkbox" name="remember">
                    Se souvenir de moi
                </label>
                <a href="/forgot-password" class="forgot-link">Mot de passe oublié</a>
            </div>

            <!-- Bouton connexion -->
            <button type="submit" class="btn-auth">Se connecter</button>

        </form>
    </div>

    <!-- ══════════════════════════════════════
         PANNEAU DROIT — Décoration
    ══════════════════════════════════════ -->
    <div class="auth-bg">

        <!-- Cercles d'ambiance -->
        <div class="bg-circle bg-circle-1"></div>
        <div class="bg-circle bg-circle-2"></div>
        <div class="bg-circle bg-circle-3"></div>

        <!-- Grille de points -->
        <div class="bg-dots">
            <?php for ($i = 0; $i < 81; $i++): ?><span></span><?php endfor; ?>
        </div>

        <!-- Message central -->
        <div class="auth-bg-content">
            <span class="auth-bg-icon">🎣</span>
            <h2>Content de vous revoir !</h2>
            <p>
                Retrouvez vos spots favoris, partagez vos plus belles prises
                et rejoignez la communauté des pêcheurs passionnés.
            </p>
        </div>

        <!-- Vagues décoratives en bas -->
        <div class="bg-waves">
            <svg viewBox="0 0 1440 80" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
                <path d="M0,40 C240,80 480,0 720,40 C960,80 1200,0 1440,40 L1440,80 L0,80 Z"
                      fill="rgba(255,255,255,0.06)"/>
                <path d="M0,60 C360,20 720,80 1080,40 C1260,20 1380,60 1440,60 L1440,80 L0,80 Z"
                      fill="rgba(255,255,255,0.04)"/>
            </svg>
        </div>

    </div>

</div>

<script>
    // Afficher / masquer le mot de passe
    function togglePwd(inputId, btn) {
        const input = document.getElementById(inputId);
        const icon  = btn.querySelector('i');
        if (input.type === 'password') {
            input.type     = 'text';
            icon.className = 'fa-regular fa-eye-slash';
        } else {
            input.type     = 'password';
            icon.className = 'fa-regular fa-eye';
        }
    }
</script>

</body>
</html>