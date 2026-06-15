<?php
// ============================================================
// src/views/auth/register.php — Page d'inscription
// ============================================================
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un compte — WebPêche</title>
    <link rel="stylesheet" href="/assets/css/auth.css">
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
        <h1>Créer un compte</h1>
        <p class="auth-subtitle">
            Vous avez déjà un compte ?
            <a href="/login">Se connecter</a>
        </p>

        <!-- Messages d'erreur -->
        <?php if (!empty($_SESSION['auth_error'])): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($_SESSION['auth_error']) ?>
            </div>
            <?php unset($_SESSION['auth_error']); ?>
        <?php endif; ?>

        <!-- Formulaire d'inscription -->
        <form class="auth-form" action="/register" method="POST">

            <!-- Nom d'utilisateur (pleine largeur) -->
            <div class="form-group">
                <i class="fa-regular fa-user field-icon"></i>
                <input
                    type="text"
                    name="username"
                    placeholder="Nom d'utilisateur"
                    required
                    autocomplete="username"
                    maxlength="50"
                    value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                >
            </div>

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
                    id="reg-pwd"
                    placeholder="Mot de passe (6 caractères minimum)"
                    required
                    autocomplete="new-password"
                    minlength="6"
                >
                <button type="button" class="toggle-pwd" onclick="togglePwd('reg-pwd', this)" title="Afficher / masquer">
                    <i class="fa-regular fa-eye"></i>
                </button>
            </div>

            <!-- Se souvenir de moi -->
            <div class="form-extras">
                <label class="remember-label">
                    <input type="checkbox" name="remember">
                    Se souvenir de moi
                </label>
            </div>

            <!-- Bouton inscription -->
            <button type="submit" class="btn-auth">Créer mon compte</button>

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
            <span class="auth-bg-icon">🐟</span>
            <h2>Rejoignez la communauté !</h2>
            <p>
                Découvrez les meilleurs spots de pêche, partagez vos expériences
                et connectez-vous avec des milliers de pêcheurs passionnés.
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