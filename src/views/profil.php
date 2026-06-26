<?php
// ============================================================
// src/views/communaute.php
// Page communauté : posts texte + commentaires + recherche
// ============================================================

if (empty($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}

$initiale = strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1));
$username = htmlspecialchars($_SESSION['username'] ?? 'Utilisateur');
$email    = htmlspecialchars($_SESSION['email']    ?? '');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WebPêche — Communauté</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/communaute.css">
</head>
<body class="theme-light" id="app">

<div class="app-container">

    <!-- ══════════════════════════════════════
         SIDEBAR GAUCHE (identique à la page Accueil)
    ══════════════════════════════════════ -->
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
            <a href="/communaute" class="nav-link active">
                <i class="fa-regular fa-circle-user"></i>
                <span>Communauté</span>
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

        <div class="sidebar-footer">
            <a href="/logout" class="btn-logout">
                <i class="fa-solid fa-right-from-bracket"></i> Se déconnecter
            </a>

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
         ZONE PRINCIPALE — Fil de la communauté
    ══════════════════════════════════════ -->
    <main class="community-wrapper">

        <div class="community-topbar">
            <button class="btn-icon" id="btn-open" title="Ouvrir le menu">
                <i class="fa-solid fa-arrow-right"></i>
            </button>

            <div class="search-bar">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="post-search" placeholder="Rechercher un post...">
            </div>

            <button class="btn-new-post" id="btn-new-post">
                <i class="fa-solid fa-plus"></i> Nouveau post
            </button>
        </div>

        <div class="community-feed" id="community-feed">
            <p class="feed-empty">Chargement...</p>
        </div>

    </main>
</div>

<!-- ══════════════════════════════════════
     MODALE — Nouveau post
══════════════════════════════════════ -->
<div class="modal-overlay" id="post-modal-overlay"></div>
<div class="modal-box" id="post-modal">
    <div class="modal-header">
        <h2>Nouveau post</h2>
        <button type="button" class="btn-icon" id="post-modal-cancel">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>

    <form id="post-form" class="modal-form">
        <label>Titre *</label>
        <input type="text" name="title" placeholder="Ex : Meilleure technique pour le brochet" required maxlength="150">

        <label>Message *</label>
        <textarea name="content" placeholder="Partagez vos avis, idées ou techniques de pêche..." required></textarea>

        <button type="submit" class="btn-auth">Publier</button>
    </form>
</div>

<script>
/* ================================================================
   THÈME — identique à la page Accueil (même clé localStorage,
   donc le choix reste cohérent entre les deux pages)
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

btnLight.addEventListener('click', () => {
    app.classList.remove('theme-dark');
    app.classList.add('theme-light');
    btnLight.classList.add('active');
    btnDarkT.classList.remove('active');
    localStorage.setItem('webpeche_theme', 'light');
});

btnDarkT.addEventListener('click', () => {
    app.classList.remove('theme-light');
    app.classList.add('theme-dark');
    btnDarkT.classList.add('active');
    btnLight.classList.remove('active');
    localStorage.setItem('webpeche_theme', 'dark');
});

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
});

btnOpen.addEventListener('click', () => {
    sidebar.classList.remove('collapsed');
    btnOpen.style.display  = 'none';
    btnClose.style.display = 'flex';
});

/* ================================================================
   OUTILS
   ================================================================ */
function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str ?? '';
    return div.innerHTML;
}

function formatDate(dateStr) {
    const d = new Date(dateStr.replace(' ', 'T'));
    return d.toLocaleDateString('fr-FR', { day: 'numeric', month: 'short', year: 'numeric' })
        + ' à ' + d.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
}

/* ================================================================
   POSTS — chargement, recherche, création
   ================================================================ */
let allPosts = [];

async function loadPosts(search = '') {
    const feed = document.getElementById('community-feed');
    try {
        const url = search ? `/api/posts?search=${encodeURIComponent(search)}` : '/api/posts';
        const res = await fetch(url);
        allPosts  = await res.json();
        renderPosts(allPosts);
    } catch (err) {
        console.error('Erreur de chargement des posts :', err);
        feed.innerHTML = '<p class="feed-empty">Erreur de chargement.</p>';
    }
}

function renderPosts(posts) {
    const feed = document.getElementById('community-feed');

    if (!posts.length) {
        feed.innerHTML = '<p class="feed-empty">Aucun post pour le moment. Soyez le premier à en publier un !</p>';
        return;
    }

    feed.innerHTML = posts.map(postCardHtml).join('');
}

function postCardHtml(post) {
    const commentCount = post.comments.length;

    return `
        <article class="post-card" data-post-id="${post.id}">
            <div class="post-header">
                <div class="post-author"><i class="fa-solid fa-circle-user"></i> ${escapeHtml(post.username)}</div>
                <div class="post-header-right">
                    <span class="post-date">${formatDate(post.created_at)}</span>
                    ${post.can_delete ? `
                        <button type="button" class="post-delete-btn" onclick="deletePost(${post.id})" title="Supprimer ce post">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    ` : ''}
                </div>
            </div>

            <h3 class="post-title">${escapeHtml(post.title)}</h3>
            <p class="post-content">${escapeHtml(post.content)}</p>

            <button type="button" class="post-comments-toggle" onclick="toggleComments(${post.id})">
                <i class="fa-regular fa-comment"></i>
                <span class="comment-count">${commentCount}</span> commentaire${commentCount === 1 ? '' : 's'}
            </button>

            <div class="post-comments" id="comments-${post.id}">
                <div class="comments-list">
                    ${post.comments.map(commentItemHtml).join('')}
                </div>
                <form class="comment-form" onsubmit="submitComment(event, ${post.id})">
                    <input type="text" name="content" placeholder="Ajouter un commentaire..." required maxlength="500">
                    <button type="submit"><i class="fa-solid fa-paper-plane"></i></button>
                </form>
            </div>
        </article>
    `;
}

function commentItemHtml(c) {
    return `
        <div class="comment-item">
            <div class="comment-header">
                <span class="comment-author">${escapeHtml(c.username)}</span>
                <span class="comment-date">${formatDate(c.created_at)}</span>
            </div>
            <p class="comment-text">${escapeHtml(c.content)}</p>
        </div>
    `;
}

// Affiche / masque les commentaires d'un post
function toggleComments(id) {
    const el = document.getElementById(`comments-${id}`);
    el.classList.toggle('visible');
}

// Supprime un post après confirmation
async function deletePost(id) {
    if (!confirm('Voulez-vous vraiment supprimer ce post ? Cette action est irréversible.')) {
        return;
    }

    try {
        const res = await fetch('/api/post/delete', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `post_id=${id}`
        });
        const data = await res.json();

        if (data.error) { alert(data.error); return; }

        allPosts = allPosts.filter(p => p.id !== id);
        renderPosts(allPosts);
    } catch (err) {
        console.error('Erreur lors de la suppression :', err);
        alert('Une erreur est survenue lors de la suppression.');
    }
}

// Recherche avec un petit délai pour ne pas interroger le serveur à chaque lettre
let searchTimeout = null;
document.getElementById('post-search').addEventListener('input', (e) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => loadPosts(e.target.value.trim()), 300);
});

/* ================================================================
   NOUVEAU POST
   ================================================================ */
const postModal        = document.getElementById('post-modal');
const postModalOverlay = document.getElementById('post-modal-overlay');
const postForm         = document.getElementById('post-form');

document.getElementById('btn-new-post').addEventListener('click', () => {
    postModal.classList.add('visible');
    postModalOverlay.classList.add('visible');
});

function closePostModal() {
    postModal.classList.remove('visible');
    postModalOverlay.classList.remove('visible');
    postForm.reset();
}

document.getElementById('post-modal-cancel').addEventListener('click', closePostModal);
postModalOverlay.addEventListener('click', closePostModal);

postForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(postForm);

    try {
        const res  = await fetch('/api/posts', { method: 'POST', body: formData });
        const post = await res.json();

        if (post.error) { alert(post.error); return; }

        allPosts.unshift(post);
        renderPosts(allPosts);
        closePostModal();
    } catch (err) {
        console.error('Erreur lors de la publication :', err);
        alert("Une erreur est survenue, le post n'a pas pu être publié.");
    }
});

/* ================================================================
   COMMENTAIRES — ajout
   ================================================================ */
async function submitComment(e, postId) {
    e.preventDefault();
    const form    = e.target;
    const content = form.content.value.trim();
    if (!content) return;

    try {
        const res = await fetch('/api/post/comment', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `post_id=${postId}&content=${encodeURIComponent(content)}`
        });
        const data = await res.json();

        if (data.error) { alert(data.error); return; }

        const post = allPosts.find(p => p.id === postId);
        if (post) post.comments.push(data);

        const list = document.querySelector(`#comments-${postId} .comments-list`);
        list.insertAdjacentHTML('beforeend', commentItemHtml(data));

        const toggleBtn = document.querySelector(`.post-card[data-post-id="${postId}"] .comment-count`);
        if (toggleBtn && post) toggleBtn.textContent = post.comments.length;

        form.reset();
    } catch (err) {
        console.error('Erreur lors de l\'envoi du commentaire :', err);
    }
}

// Premier chargement des posts
loadPosts();
</script>

</body>
</html>