<?php
session_start();
$autoload = __DIR__ . '/../vendor/autoload.php';
if (file_exists($autoload)) {
    require_once $autoload;
} else {
    http_response_code(500);
    echo '<h1>Missing dependencies</h1>';
    echo '<p>Run <code>composer install</code> in the project root to generate <code>vendor/autoload.php</code>.</p>';
    exit;
}

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

switch ($uri) {

    // Page d'accueil
    case '/':
        require_once __DIR__ . '/../src/controllers/HomeController.php';
        (new HomeController())->index();
        break;

    // Connexion
    case '/login':
        require_once __DIR__ . '/../src/controllers/AuthController.php';
        (new AuthController())->login();
        break;

    // Inscription
    case '/register':
        require_once __DIR__ . '/../src/controllers/AuthController.php';
        (new AuthController())->register();
        break;

    // url inconnue => 404
    default:
        http_response_code(404);
        echo '<h1>404 — Page introuvable</h1>';
        break;
}