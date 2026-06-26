<?php
// ============================================================
// src/views/accueil.php
// Page principale après connexion
// ============================================================

// Sécurité : si pas connecté → retour au login
if (empty($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}

// Initiale de l'utilisateur pour l'avatar
$initiale = strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1));
$username = htmlspecialchars($_SESSION['username'] ?? 'Utilisateur');
$email    = htmlspecialchars($_SESSION['email']    ?? '');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WebPêche — Carte</title>

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- CSS page -->
    <link rel="stylesheet" href="/assets/css/accueil.css">
</head>
<body class="theme-light" id="app">

<div class="app-container">

    <!-- ══════════════════════════════════════
         SIDEBAR GAUCHE
    ══════════════════════════════════════ -->
    <aside class="sidebar" id="sidebar">

        <!-- En-tête : avatar + nom + bouton fermer -->
        <div class="sidebar-header">
            <div class="user-card">
                <div class="user-avatar"><?= $initiale ?></div>
                <div class="user-info">
                    <span class="app-brand">WebPêche</span>
                    <span class="user-email"><?= $email ?: $username ?></span>
                </div>
            </div>
            <button class="btn-icon" id="btn-close" title="Fermer le menu">
                <i class="fa-solid fa-arrow-left"></i>
            </button>
        </div>

        <!-- Navigation -->
        <nav class="sidebar-nav">
            <p class="nav-label">ACCUEIL</p>

            <a href="/accueil" class="nav-link">
                <i class="fa-solid fa-house"></i>
                <span>Home Map</span>
            </a>
            <a href="/profil" class="nav-link active">
                <i class="fa-regular fa-circle-user"></i>
                <span>Profil</span>
            </a>
            <a href="/parametres" class="nav-link">
                <i class="fa-solid fa-gear"></i>
                <span>Paramètre</span>
            </a>
            <a href="/documentation" class="nav-link">
                <i class="fa-solid fa-book"></i>
                <span>Documentation</span>
            </a>
        </nav>

        <!-- Bas de la sidebar : toggle thème -->
        <div class="sidebar-footer">
            <div class="theme-toggle">
                <button class="theme-btn active" id="btn-light">
                    <i class="fa-solid fa-sun"></i> Light
                </button>
                <button class="theme-btn" id="btn-dark">
                    <i class="fa-solid fa-moon"></i> Dark
                </button>
            </div>
        </div>

    </aside>

</body>
</html>