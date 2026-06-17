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

            <a href="/accueil" class="nav-link active">
                <i class="fa-solid fa-house"></i>
                <span>Home Map</span>
            </a>
            <a href="/profil" class="nav-link">
                <i class="fa-regular fa-circle-user"></i>
                <span>Profil</span>
            </a>
            <a href="/parametres" class="nav-link">
                <i class="fa-solid fa-gear"></i>
                <span>Paramètre</span>
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

    <!-- ══════════════════════════════════════
         ZONE CARTE
    ══════════════════════════════════════ -->
    <main class="map-wrapper">

        <!-- Barre flottante en haut de la carte -->
        <div class="map-topbar">

            <!-- Groupe gauche : ouvrir sidebar + recherche -->
            <div class="topbar-group topbar-left">
                <button class="btn-icon" id="btn-open" title="Ouvrir le menu">
                    <i class="fa-solid fa-arrow-right"></i>
                </button>

                <div class="search-bar">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="search-input" placeholder="Rechercher un lac ...">
                </div>
            </div>

            <!-- Groupe droite : modifier + 3 petits points -->
            <div class="topbar-group topbar-right">
                <button class="btn-icon" id="btn-add-spot" title="Ajouter un spot">
                    <i class="fa-solid fa-pen"></i>
                </button>
                <button class="btn-icon" title="Plus d'options">
                    <i class="fa-solid fa-ellipsis-vertical"></i>
                </button>
            </div>

        </div>

        <!-- Carte Leaflet -->
        <div id="map"></div>

    </main>
</div>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
/* ================================================================
   THÈME — on relit la préférence sauvegardée AVANT de créer la carte
   (sinon la carte se chargerait dans le mauvais thème un instant)
   ================================================================ */
const app      = document.getElementById('app');
const btnLight = document.getElementById('btn-light');
const btnDarkT = document.getElementById('btn-dark');

// 'light' par défaut si rien n'a encore été enregistré
const savedTheme = localStorage.getItem('webpeche_theme') || 'light';

if (savedTheme === 'dark') {
    app.classList.remove('theme-light');
    app.classList.add('theme-dark');
    btnDarkT.classList.add('active');
    btnLight.classList.remove('active');
}

/* ================================================================
   CARTE LEAFLET
   ================================================================ */
// Tuiles claires — Wikimedia "osm-intl" : labels traduits via ?lang=fr
// Gratuit, sans clé API, et tous les noms de pays/villes sortent en français.
const tileLight = 'https://maps.wikimedia.org/osm-intl/{z}/{x}/{y}{r}.png?lang=fr';
// Tuiles sombres (mode dark) — CartoDB Dark Matter
const tileDark  = 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png';

const map = L.map('map', {
    zoomControl: false,           // on désactive le zoom par défaut (en haut à gauche)
    attributionControl: false
}).setView([46.6, 2.3], 6); // France entière

// Zoom +/- replacé en bas à droite, loin de la barre de recherche
L.control.zoom({ position: 'bottomright' }).addTo(map);

// On affiche directement la bonne carte selon le thème sauvegardé
let currentLayer = L.tileLayer(
    savedTheme === 'dark' ? tileDark : tileLight,
    { maxZoom: 19, detectRetina: true }
).addTo(map);

/* ================================================================
   SIDEBAR — ouvrir / fermer
   ================================================================ */
const sidebar  = document.getElementById('sidebar');
const btnOpen  = document.getElementById('btn-open');
const btnClose = document.getElementById('btn-close');

// Sidebar ouverte au départ → on cache le bouton "ouvrir"
btnOpen.style.display = 'none';

btnClose.addEventListener('click', () => {
    sidebar.classList.add('collapsed');
    btnOpen.style.display  = 'flex';
    btnClose.style.display = 'none';
    setTimeout(() => map.invalidateSize(), 280); // attend la transition
});

btnOpen.addEventListener('click', () => {
    sidebar.classList.remove('collapsed');
    btnOpen.style.display  = 'none';
    btnClose.style.display = 'flex';
    setTimeout(() => map.invalidateSize(), 280);
});

/* ================================================================
   THÈME LIGHT / DARK — clic sur les boutons
   ================================================================ */
btnLight.addEventListener('click', () => {
    app.classList.remove('theme-dark');
    app.classList.add('theme-light');
    btnLight.classList.add('active');
    btnDarkT.classList.remove('active');

    // Change les tuiles de la carte
    map.removeLayer(currentLayer);
    currentLayer = L.tileLayer(tileLight, { maxZoom: 19, detectRetina: true }).addTo(map);

    // On enregistre le choix pour qu'il survive à un rechargement de page
    localStorage.setItem('webpeche_theme', 'light');
});

btnDarkT.addEventListener('click', () => {
    app.classList.remove('theme-light');
    app.classList.add('theme-dark');
    btnDarkT.classList.add('active');
    btnLight.classList.remove('active');

    // Change les tuiles de la carte
    map.removeLayer(currentLayer);
    currentLayer = L.tileLayer(tileDark, { maxZoom: 19, detectRetina: true }).addTo(map);

    // On enregistre le choix pour qu'il survive à un rechargement de page
    localStorage.setItem('webpeche_theme', 'dark');
});

/* ================================================================
   EXEMPLE : ajouter un marqueur (spot de pêche)
   Tu pourras charger tes spots depuis la BDD plus tard
   ================================================================ */
const exampleSpot = L.marker([43.7102, 7.2620]).addTo(map);
exampleSpot.bindPopup(`
    <div style="min-width:180px">
        <strong>Rivière des Truites</strong><br>
        <small>⭐ 4.8 — Alpes-Maritimes</small><br>
        <small>🐟 Truite fario, ombre chevalier</small>
    </div>
`);
</script>

</body>
</html>