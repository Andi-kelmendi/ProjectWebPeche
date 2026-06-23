<?php
// ============================================================
// public/index.php — Routeur principal
// Toutes les URLs passent par ici grâce au .htaccess
// ============================================================

// Charge l'autoloader de Composer (indispensable pour les classes avec namespace, ex: View)
require_once __DIR__ . '/../vendor/autoload.php';

session_start();

// On récupère l'URL tapée dans le navigateur
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// ============================================================
// ROUTES
// ============================================================
switch ($uri) {

    // Page d'accueil publique (landing page)
    case '/':
        require_once __DIR__ . '/../src/controllers/HomeController.php';
        $ctrl = new HomeController();
        $ctrl->index();
        break;

    // Connexion
    case '/login':
        require_once __DIR__ . '/../src/controllers/AuthController.php';
        $ctrl = new AuthController();
        $ctrl->login();
        break;

    // Inscription
    case '/register':
        require_once __DIR__ . '/../src/controllers/AuthController.php';
        $ctrl = new AuthController();
        $ctrl->register();
        break;

    // Page principale après connexion (carte)
    case '/accueil':
        require_once __DIR__ . '/../src/views/accueil.php';
        break;

    // Page profil de l'utilisateur connecté
    case '/profil':
        require_once __DIR__ . '/../src/views/profil.php';
        break;

    // Page paramètres de l'utilisateur connecté
    case '/parametre':
        require_once __DIR__ . '/../src/views/parametre.php';
        break;

    // Page de documentation / aide — accessible à tous
    case '/documentation':
        require_once __DIR__ . '/../src/views/documentation.php';
        break;

    // --------------------------------------------------------
    // API — Spots de pêche
    // --------------------------------------------------------

    // GET  : liste tous les spots (pour les marqueurs)
    // POST : crée un nouveau spot
    case '/api/spots':
        require_once __DIR__ . '/../src/controllers/SpotController.php';
        $ctrl = new SpotController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ctrl->store();
        } else {
            $ctrl->index();
        }
        break;

    // GET ?id=X : détail d'un spot (description + avis)
    case '/api/spot':
        require_once __DIR__ . '/../src/controllers/SpotController.php';
        $ctrl = new SpotController();
        $ctrl->show();
        break;

    // POST : like / dislike un spot
    case '/api/spot/rate':
        require_once __DIR__ . '/../src/controllers/SpotController.php';
        $ctrl = new SpotController();
        $ctrl->rate();
        break;

    // POST : ajoute un avis (commentaire) sur un spot
    case '/api/spot/comment':
        require_once __DIR__ . '/../src/controllers/SpotController.php';
        $ctrl = new SpotController();
        $ctrl->comment();
        break;

    // URL inconnue → 404
    default:
        http_response_code(404);
        echo '<h1>404 — Page introuvable</h1>';
        break;
}