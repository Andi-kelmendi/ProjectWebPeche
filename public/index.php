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

    // URL inconnue → 404
    default:
        http_response_code(404);
        echo '<h1>404 — Page introuvable</h1>';
        break;
}