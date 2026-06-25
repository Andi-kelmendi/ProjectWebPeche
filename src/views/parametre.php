<?php
// ============================================================
// src/views/parametre.php
// Page de configuration des paramètres utilisateur
// ============================================================

// Sécurité : si pas connecté → retour au login
if (empty($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}

// Initiales et données utilisateur (simulées ou issues de la session)
$initiale = strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1));
$username = htmlspecialchars($_SESSION['username'] ?? 'Utilisateur');
$email    = htmlspecialchars($_SESSION['email']    ?? '');

// Données additionnelles de pêche (à l'avenir, viendront de ta BDD via le Model)
$fishing_type = htmlspecialchars($_SESSION['fishing_type'] ?? 'carnassier');
$license_num  = htmlspecialchars($_SESSION['license_num']  ?? '');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WebPêche — Paramètres</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/accueil.css">
    <link rel="stylesheet" href="/assets/css/parametre.css">
</head>
<body class="theme-light" id="app">

<div class="app-container">

    <aside class="sidebar" id="sidebar">
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

        <nav class="sidebar-nav">
            <p class="nav-label">ACCUEIL</p>
            <a href="/accueil" class="nav-link">
                <i class="fa-solid fa-house"></i>
                <span>Home Map</span>
            </a>
            <a href="/profil" class="nav-link">
                <i class="fa-regular fa-circle-user"></i>
                <span>Profil</span>
            </a>
            <a href="/parametre" class="nav-link active">
                <i class="fa-solid fa-gear"></i>
                <span>Paramètre</span>
            </a>
            <a href="/documentation" class="nav-link">
                <i class="fa-solid fa-book"></i>
                <span>Documentation</span>
            </a>
        </nav>

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

    <main class="settings-main">
        <div class="settings-header">
            <h1><i class="fa-solid fa-gear"></i> Paramètres du compte</h1>
            <p>Gérez vos informations personnelles et vos préférences de pêche.</p>
        </div>

        <form action="/parametre/update" method="POST" class="settings-form">
            
            <div class="settings-card">
                <div class="card-header">
                    <i class="fa-solid fa-user"></i>
                    <h2>Informations générales</h2>
                </div>
                <div class="card-body">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="username">Nom d'utilisateur</label>
                            <input type="text" id="username" name="username" value="<?= $username ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Adresse Email</label>
                            <input type="email" id="email" name="email" value="<?= $email ?>" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="settings-card">
                <div class="card-header">
                    <i class="fa-solid fa-fish"></i>
                    <h2>Préférences de pêche</h2>
                </div>
                <div class="card-body">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="fishing_type">Technique favorite</label>
                            <select id="fishing_type" name="fishing_type">
                                <option value="carnassier" <?= $fishing_type == 'carnassier' ? 'selected' : '' ?>>Pêche aux carnassiers</option>
                                <option value="carpe" <?= $fishing_type == 'carpe' ? 'selected' : '' ?>>Pêche de la carpe</option>
                                <option value="mouche" <?= $fishing_type == 'mouche' ? 'selected' : '' ?>>Pêche à la mouche</option>
                                <option value="mer" <?= $fishing_type == 'mer' ? 'selected' : '' ?>>Pêche en mer</option>
                                <option value="coup" <?= $fishing_type == 'coup' ? 'selected' : '' ?>>Pêche au coup</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="license_num">N° de carte de pêche (optionnel)</label>
                            <input type="text" id="license_num" name="license_num" value="<?= $license_num ?>" placeholder="Ex: 12A34567">
                        </div>
                    </div>
                </div>
            </div>

            <div class="settings-card">
                <div class="card-header">
                    <i class="fa-solid fa-lock"></i>
                    <h2>Sécurité & Mot de passe</h2>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="current_password">Mot de passe actuel</label>
                        <input type="password" id="current_password" name="current_password" placeholder="••••••••">
                    </div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="new_password">Nouveau mot de passe</label>
                            <input type="password" id="new_password" name="new_password" placeholder="••••••••">
                        </div>
                        <div class="form-group">
                            <label for="confirm_password">Confirmer le nouveau mot de passe</label>
                            <input type="password" id="confirm_password" name="confirm_password" placeholder="••••••••">
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="reset" class="btn-secondary">Annuler</button>
                <button type="submit" class="btn-primary">Enregistrer les modifications</button>
            </div>

        </form>
    </main>

</div>

</body>
</html>