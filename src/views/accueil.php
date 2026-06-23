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
            <a href="/parametre" class="nav-link">
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
                    <div class="search-suggestions" id="search-suggestions"></div>
                </div>
            </div>

            <!-- Groupe droite : modifier + 3 petits points -->
            <div class="topbar-group topbar-right">
                <button class="btn-icon" id="btn-add-spot" title="Ajouter un spot de pêche">
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

<!-- ══════════════════════════════════════
     BANNIÈRE — Mode "placement" (ajout d'un spot)
══════════════════════════════════════ -->
<div class="placement-banner" id="placement-banner">
    <i class="fa-solid fa-location-crosshairs"></i>
    <span>Cliquez sur la carte pour choisir l'emplacement de votre spot</span>
    <button id="placement-cancel" class="placement-cancel" title="Annuler">
        <i class="fa-solid fa-xmark"></i>
    </button>
</div>

<!-- ══════════════════════════════════════
     MODALE — Ajouter un spot
══════════════════════════════════════ -->
<div class="modal-overlay" id="add-spot-overlay"></div>
<div class="modal-box" id="add-spot-modal">
    <div class="modal-header">
        <h2>Ajouter un spot de pêche</h2>
        <button type="button" class="btn-icon" id="add-spot-cancel">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>

    <form id="add-spot-form" class="modal-form">
        <input type="hidden" name="latitude"  id="spot-lat">
        <input type="hidden" name="longitude" id="spot-lng">

        <label>Nom du spot *</label>
        <input type="text" name="name" placeholder="Ex : Lac de la Forêt" required maxlength="100">

        <label>Région</label>
        <input type="text" name="region" placeholder="Ex : Alpes-Maritimes" maxlength="100">

        <label>Espèces de poissons <span class="optional-tag">(facultatif)</span></label>
        <input type="text" name="species" placeholder="Laissez vide si vous ne savez pas" maxlength="255">

        <label>Description <span class="optional-tag">(facultatif)</span></label>
        <textarea name="description" placeholder="Accès, conseils, ambiance du lieu..."></textarea>

        <button type="submit" class="btn-auth">Ajouter ce spot</button>
    </form>
</div>

<!-- ══════════════════════════════════════
     PANNEAU LATÉRAL — Détails d'un spot
══════════════════════════════════════ -->
<div class="panel-overlay" id="spot-panel-overlay"></div>
<aside class="spot-panel" id="spot-panel">
    <div class="panel-header">
        <span class="panel-header-title">Détails du spot</span>
        <button type="button" class="btn-icon" id="spot-panel-close">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
    <div class="panel-body" id="spot-panel-body">
        <!-- Rempli dynamiquement en JS -->
    </div>
</aside>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
/* ================================================================
   THÈME — on relit la préférence sauvegardée AVANT de créer la carte
   ================================================================ */
const app      = document.getElementById('app');
const btnLight = document.getElementById('btn-light');
const btnDarkT = document.getElementById('btn-dark');

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
const tileLight = 'https://maps.wikimedia.org/osm-intl/{z}/{x}/{y}{r}.png?lang=fr';
const tileDark  = 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png';

const map = L.map('map', {
    zoomControl: false,
    attributionControl: false
}).setView([46.6, 2.3], 6); // France entière

L.control.zoom({ position: 'bottomright' }).addTo(map);

let currentLayer = L.tileLayer(
    savedTheme === 'dark' ? tileDark : tileLight,
    { maxZoom: 19, detectRetina: true }
).addTo(map);

// Groupe qui contiendra tous les marqueurs de spots (facile à vider/recharger)
const spotsLayer  = L.layerGroup().addTo(map);
let allSpots       = [];  // copie des données des spots (pour la recherche)
const markersById  = {};  // accès rapide à un marqueur depuis l'id du spot

/* ================================================================
   SIDEBAR — ouvrir / fermer
   ================================================================ */
const sidebar  = document.getElementById('sidebar');
const btnOpen  = document.getElementById('btn-open');
const btnClose = document.getElementById('btn-close');

btnOpen.style.display = 'none';

btnClose.addEventListener('click', () => {
    sidebar.classList.add('collapsed');
    btnOpen.style.display  = 'flex';
    btnClose.style.display = 'none';
    setTimeout(() => map.invalidateSize(), 280);
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

    map.removeLayer(currentLayer);
    currentLayer = L.tileLayer(tileLight, { maxZoom: 19, detectRetina: true }).addTo(map);

    localStorage.setItem('webpeche_theme', 'light');
});

btnDarkT.addEventListener('click', () => {
    app.classList.remove('theme-light');
    app.classList.add('theme-dark');
    btnDarkT.classList.add('active');
    btnLight.classList.remove('active');

    map.removeLayer(currentLayer);
    currentLayer = L.tileLayer(tileDark, { maxZoom: 19, detectRetina: true }).addTo(map);

    localStorage.setItem('webpeche_theme', 'dark');
});

/* ================================================================
   OUTILS — échappement HTML + formatage de date
   ================================================================ */
function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str ?? '';
    return div.innerHTML;
}

function formatDate(dateStr) {
    const d = new Date(dateStr.replace(' ', 'T'));
    return d.toLocaleDateString('fr-FR', { day: 'numeric', month: 'short', year: 'numeric' });
}

// Enlève les accents et met en minuscule, pour comparer du texte
// sans se soucier des accents (ex: "lac de la foret" trouve "Lac de la Forêt")
function normalizeText(str) {
    return (str || '')
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '');
}

/* ================================================================
   NOTE EN POURCENTAGE — convertit la note sur 5 en pourcentage
   et choisit une couleur selon le résultat (vert / orange / rouge)
   ================================================================ */
function ratingToPercent(rating) {
    const value = parseFloat(rating);
    if (!value) return null; // pas encore de note (0 ou vide)
    return Math.round((value / 5) * 100);
}

function ratingClass(pct) {
    if (pct === null) return 'no-rating';
    if (pct < 50)     return 'rating-bad';
    if (pct < 75)     return 'rating-mid';
    return 'rating-good';
}

// Construit le HTML du badge (utilisé à l'affichage initial)
function ratingBadgeHtml(rating) {
    const pct  = ratingToPercent(rating);
    const cls  = ratingClass(pct);
    const text = pct === null ? 'Pas encore noté' : `${pct}%`;
    return `<span class="spot-rating-value rating-badge ${cls}">${text}</span>`;
}

// Met à jour un badge déjà affiché (utilisé après un vote, sans recharger)
function applyRatingBadge(el, rating) {
    const pct = ratingToPercent(rating);
    el.className   = `spot-rating-value rating-badge ${ratingClass(pct)}`;
    el.textContent = pct === null ? 'Pas encore noté' : `${pct}%`;
}

/* ================================================================
   RECHERCHE DE LIEU — autocomplétion + zoom sur la carte
   API gratuite OpenStreetMap Nominatim, sans clé nécessaire
   ================================================================ */
const searchInput       = document.getElementById('search-input');
const searchSuggestions = document.getElementById('search-suggestions');
let searchTimeout = null;

searchInput.addEventListener('input', () => {
    const query = searchInput.value.trim();

    if (query.length < 2) {
        hideSuggestions();
        return;
    }

    // Les spots déjà ajoutés par les utilisateurs s'affichent tout de suite
    const localMatches = filterLocalSpots(query);
    renderSuggestions(localMatches, []);

    // On attend que l'utilisateur arrête de taper avant d'interroger l'API
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => fetchSuggestions(query, localMatches), 400);
});

// Recherche dans les spots déjà ajoutés (nom ou région), sans tenir compte des accents
function filterLocalSpots(query) {
    const q = normalizeText(query);
    return allSpots
        .filter(spot =>
            normalizeText(spot.name).includes(q) ||
            normalizeText(spot.region).includes(q)
        )
        .slice(0, 5);
}

async function fetchSuggestions(query, localMatches) {
    try {
        const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=8&accept-language=fr&countrycodes=fr,be`;
        const res    = await fetch(url);
        const places = await res.json();
        renderSuggestions(localMatches, places);
    } catch (err) {
        console.error('Erreur de recherche :', err);
        renderSuggestions(localMatches, []);
    }
}

function renderSuggestions(spots, places) {
    if (!spots.length && !places.length) {
        searchSuggestions.innerHTML = '<div class="search-no-result">Aucun résultat trouvé</div>';
        searchSuggestions.classList.add('visible');
        return;
    }

    // Spots déjà ajoutés sur WebPêche (mis en avant avec une icône poisson)
    const spotsHtml = spots.map(s => `
        <div class="search-suggestion-item" data-type="spot" data-id="${s.id}" data-lat="${s.latitude}" data-lon="${s.longitude}">
            <i class="fa-solid fa-fish"></i>
            <span>${escapeHtml(s.name)}${s.region ? ' — ' + escapeHtml(s.region) : ''}</span>
        </div>
    `).join('');

    // Lieux trouvés via la recherche géographique générale
    const placesHtml = places.map(p => `
        <div class="search-suggestion-item" data-type="place" data-lat="${p.lat}" data-lon="${p.lon}">
            <i class="fa-solid fa-location-dot"></i>
            <span>${escapeHtml(p.display_name)}</span>
        </div>
    `).join('');

    searchSuggestions.innerHTML = spotsHtml + placesHtml;
    searchSuggestions.classList.add('visible');

    searchSuggestions.querySelectorAll('.search-suggestion-item').forEach(item => {
        item.addEventListener('click', () => {
            const lat  = parseFloat(item.dataset.lat);
            const lon  = parseFloat(item.dataset.lon);

            if (item.dataset.type === 'spot') {
                goToSpot(parseInt(item.dataset.id, 10), lat, lon);
            } else {
                goToLocation(lat, lon, item.querySelector('span').textContent);
            }
        });
    });
}

// Va vers un spot déjà existant et ouvre sa popup
function goToSpot(id, lat, lon) {
    map.setView([lat, lon], 14);
    hideSuggestions();
    searchInput.value = '';

    const marker = markersById[id];
    if (marker) marker.openPopup();
}

// Va vers un lieu trouvé par la recherche géographique (sans créer de marqueur)
function goToLocation(lat, lon, label) {
    map.setView([lat, lon], 12);
    hideSuggestions();
    searchInput.value = label;
}

function hideSuggestions() {
    searchSuggestions.classList.remove('visible');
    searchSuggestions.innerHTML = '';
}

// Appui sur "Entrée" → va directement au premier résultat affiché
searchInput.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') {
        e.preventDefault();
        const firstItem = searchSuggestions.querySelector('.search-suggestion-item');
        if (firstItem) firstItem.click();
    }
});

// Ferme les suggestions si on clique en dehors de la barre de recherche
document.addEventListener('click', (e) => {
    if (!e.target.closest('.search-bar')) {
        hideSuggestions();
    }
});

/* ================================================================
   SPOTS — chargement + affichage des marqueurs sur la carte
   ================================================================ */
async function loadSpots() {
    try {
        const res   = await fetch('/api/spots');
        const spots = await res.json();
        allSpots = spots;
        spotsLayer.clearLayers();
        spots.forEach(addSpotMarker);
    } catch (err) {
        console.error('Erreur de chargement des spots :', err);
    }
}

function popupHtml(spot) {
    const region  = spot.region ? ` — ${escapeHtml(spot.region)}` : '';
    const species = spot.species
        ? `🐟 ${escapeHtml(spot.species)}`
        : `🐟 Espèces non renseignées`;

    return `
        <div class="spot-popup" data-spot-id="${spot.id}">
            <strong>${escapeHtml(spot.name)}</strong><br>
            <small>${ratingBadgeHtml(spot.rating)}${region}</small><br>
            <small>${species}</small>
            <div class="popup-actions">
                <button class="popup-btn like" onclick="voteSpot(${spot.id}, 'like', this)">
                    👍 <span class="like-count">${spot.likes ?? 0}</span>
                </button>
                <button class="popup-btn dislike" onclick="voteSpot(${spot.id}, 'dislike', this)">
                    👎 <span class="dislike-count">${spot.dislikes ?? 0}</span>
                </button>
                <button class="popup-btn more" onclick="openSpotPanel(${spot.id})">
                    Voir plus
                </button>
            </div>
        </div>
    `;
}

function addSpotMarker(spot) {
    const marker = L.marker([spot.latitude, spot.longitude]).addTo(spotsLayer);
    marker.bindPopup(popupHtml(spot), { minWidth: 220 });
    markersById[spot.id] = marker;
}

// Premier chargement des spots existants (depuis la base de données)
loadSpots();

/* ================================================================
   VOTES — like / dislike depuis la popup
   ================================================================ */
async function voteSpot(id, vote, btn) {
    try {
        const res = await fetch('/api/spot/rate', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `spot_id=${id}&vote=${vote}`
        });
        const data = await res.json();

        if (data.error) { alert(data.error); return; }

        const popupEl = btn.closest('.spot-popup');
        popupEl.querySelector('.like-count').textContent    = data.likes;
        popupEl.querySelector('.dislike-count').textContent = data.dislikes;
        applyRatingBadge(popupEl.querySelector('.spot-rating-value'), data.rating);
    } catch (err) {
        console.error('Erreur lors du vote :', err);
    }
}

/* ================================================================
   MODE PLACEMENT — ajouter un nouveau spot sur la carte
   ================================================================ */
let placementMode = false;
let tempMarker     = null;

const btnAddSpot      = document.getElementById('btn-add-spot');
const placementBanner = document.getElementById('placement-banner');
const addSpotOverlay  = document.getElementById('add-spot-overlay');
const addSpotModal    = document.getElementById('add-spot-modal');
const addSpotForm     = document.getElementById('add-spot-form');

btnAddSpot.addEventListener('click', () => {
    if (placementMode) {
        cancelPlacement();
    } else {
        startPlacement();
    }
});

function startPlacement() {
    placementMode = true;
    btnAddSpot.classList.add('active');
    placementBanner.classList.add('visible');
    map.getContainer().style.cursor = 'crosshair';
}

function cancelPlacement() {
    placementMode = false;
    btnAddSpot.classList.remove('active');
    placementBanner.classList.remove('visible');
    map.getContainer().style.cursor = '';
    if (tempMarker) {
        map.removeLayer(tempMarker);
        tempMarker = null;
    }
}

document.getElementById('placement-cancel').addEventListener('click', cancelPlacement);

map.on('click', (e) => {
    if (!placementMode) return;

    if (tempMarker) map.removeLayer(tempMarker);
    tempMarker = L.marker(e.latlng, { opacity: 0.85 }).addTo(map);

    document.getElementById('spot-lat').value = e.latlng.lat;
    document.getElementById('spot-lng').value = e.latlng.lng;

    openAddSpotModal();
});

function openAddSpotModal() {
    addSpotOverlay.classList.add('visible');
    addSpotModal.classList.add('visible');
}

function closeAddSpotModal() {
    addSpotOverlay.classList.remove('visible');
    addSpotModal.classList.remove('visible');
}

function closeAddSpotFlow() {
    closeAddSpotModal();
    cancelPlacement();
    addSpotForm.reset();
}

document.getElementById('add-spot-cancel').addEventListener('click', closeAddSpotFlow);
addSpotOverlay.addEventListener('click', closeAddSpotFlow);

addSpotForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    const formData = new FormData(addSpotForm);

    try {
        const res  = await fetch('/api/spots', { method: 'POST', body: formData });
        const spot = await res.json();

        if (spot.error) { alert(spot.error); return; }

        allSpots.push(spot);
        addSpotMarker(spot);
        closeAddSpotModal();
        cancelPlacement();
        addSpotForm.reset();
    } catch (err) {
        console.error('Erreur lors de l\'ajout du spot :', err);
        alert("Une erreur est survenue, le spot n'a pas pu être ajouté.");
    }
});

/* ================================================================
   PANNEAU LATÉRAL — "Voir plus" : détails + avis d'un spot
   ================================================================ */
const spotPanel        = document.getElementById('spot-panel');
const spotPanelBody    = document.getElementById('spot-panel-body');
const spotPanelOverlay = document.getElementById('spot-panel-overlay');

async function openSpotPanel(id) {
    spotPanel.classList.add('open');
    spotPanelOverlay.classList.add('visible');
    spotPanelBody.innerHTML = '<p class="panel-loading">Chargement...</p>';

    try {
        const res  = await fetch(`/api/spot?id=${id}`);
        const spot = await res.json();

        if (spot.error) {
            spotPanelBody.innerHTML = `<p class="panel-loading">${escapeHtml(spot.error)}</p>`;
            return;
        }

        renderSpotPanel(spot);
    } catch (err) {
        spotPanelBody.innerHTML = '<p class="panel-loading">Erreur de chargement.</p>';
    }
}

function renderSpotPanel(spot) {
    const species     = spot.species     || 'Non renseignées';
    const region       = spot.region       || 'Non renseignée';
    const description = spot.description || 'Aucune description pour le moment.';

    const commentsHtml = spot.comments.length
        ? spot.comments.map(c => `
            <div class="review-item">
                <div class="review-header">
                    <span class="review-author">${escapeHtml(c.username)}</span>
                    <span class="review-date">${formatDate(c.created_at)}</span>
                </div>
                <p class="review-text">${escapeHtml(c.comment)}</p>
            </div>
        `).join('')
        : '<p class="no-reviews">Aucun avis pour le moment. Soyez le premier à en laisser un !</p>';

    spotPanelBody.innerHTML = `
        <h2>${escapeHtml(spot.name)}</h2>
        <div class="panel-meta">
            ${ratingBadgeHtml(spot.rating)}
            <span>📍 ${escapeHtml(region)}</span>
        </div>
        <p class="panel-species">🐟 ${escapeHtml(species)}</p>
        <p class="panel-description">${escapeHtml(description)}</p>

        <div class="panel-vote-row">
            <button class="popup-btn like" onclick="voteSpotPanel(${spot.id}, 'like')">
                👍 J'aime (<span class="panel-like-count">${spot.likes}</span>)
            </button>
            <button class="popup-btn dislike" onclick="voteSpotPanel(${spot.id}, 'dislike')">
                👎 Je n'aime pas (<span class="panel-dislike-count">${spot.dislikes}</span>)
            </button>
        </div>

        <hr>

        <h3>Avis (${spot.comments.length})</h3>
        <div class="reviews-list">${commentsHtml}</div>

        <form class="add-review-form" onsubmit="submitReview(event, ${spot.id})">
            <textarea name="comment" placeholder="Partagez votre expérience sur ce spot..." required></textarea>
            <button type="submit" class="btn-auth">Publier mon avis</button>
        </form>
    `;
}

async function voteSpotPanel(id, vote) {
    try {
        const res = await fetch('/api/spot/rate', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `spot_id=${id}&vote=${vote}`
        });
        const data = await res.json();

        if (data.error) { alert(data.error); return; }

        document.querySelector('.panel-like-count').textContent    = data.likes;
        document.querySelector('.panel-dislike-count').textContent = data.dislikes;
        applyRatingBadge(document.querySelector('.panel-meta .spot-rating-value'), data.rating);
    } catch (err) {
        console.error('Erreur lors du vote :', err);
    }
}

async function submitReview(e, spotId) {
    e.preventDefault();
    const form    = e.target;
    const comment = form.comment.value.trim();
    if (!comment) return;

    try {
        const res = await fetch('/api/spot/comment', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `spot_id=${spotId}&comment=${encodeURIComponent(comment)}`
        });
        const data = await res.json();

        if (data.error) { alert(data.error); return; }

        const list      = document.querySelector('.reviews-list');
        const noReviews = list.querySelector('.no-reviews');
        if (noReviews) noReviews.remove();

        const item = document.createElement('div');
        item.className = 'review-item';
        item.innerHTML = `
            <div class="review-header">
                <span class="review-author">${escapeHtml(data.username)}</span>
                <span class="review-date">${formatDate(data.created_at)}</span>
            </div>
            <p class="review-text">${escapeHtml(data.comment)}</p>
        `;
        list.prepend(item);
        form.reset();
    } catch (err) {
        console.error('Erreur lors de l\'envoi de l\'avis :', err);
    }
}

function closeSpotPanel() {
    spotPanel.classList.remove('open');
    spotPanelOverlay.classList.remove('visible');
}

document.getElementById('spot-panel-close').addEventListener('click', closeSpotPanel);
spotPanelOverlay.addEventListener('click', closeSpotPanel);
</script>

</body>
</html>