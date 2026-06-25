<?php
// ============================================================
// public/index.php — Routeur principal
// Toutes les URLs passent par ici grâce au .htaccess
// ============================================================

require_once __DIR__ . '/../vendor/autoload.php';

session_start();

// ============================================================
// "SE SOUVENIR DE MOI" — reconnexion automatique
// ============================================================
if (empty($_SESSION['user_id']) && !empty($_COOKIE['remember_token'])) {
    require_once __DIR__ . '/../src/config/database.php';

    try {
        $pdo       = Database::connect();
        $tokenHash = hash('sha256', $_COOKIE['remember_token']);

        $stmt = $pdo->prepare(
            'SELECT u.id, u.username, u.email, u.is_admin
             FROM remember_tokens t
             JOIN users u ON u.id = t.user_id
             WHERE t.token_hash = ? AND t.expires_at > NOW()
             LIMIT 1'
        );
        $stmt->execute([$tokenHash]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email']    = $user['email'];
            $_SESSION['is_admin'] = !empty($user['is_admin']);
        } else {
            setcookie('remember_token', '', time() - 3600, '/');
        }
    } catch (\Throwable $e) {
        // Table absente (migration pas encore faite) — on ignore
    }
}

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// ============================================================
// ROUTES
// ============================================================
switch ($uri) {

    // Page d'accueil publique
    case '/':
        if (!empty($_SESSION['user_id'])) {
            header('Location: /accueil');
            exit;
        }
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

    // Déconnexion
    case '/logout':
        require_once __DIR__ . '/../src/controllers/AuthController.php';
        (new AuthController())->logout();
        break;

    // Carte (page principale après connexion)
    case '/accueil':
        require_once __DIR__ . '/../src/views/accueil.php';
        break;

    // Profil
    case '/profil':
        require_once __DIR__ . '/../src/views/profil.php';
        break;

    // ── Paramètres ──────────────────────────────────────────
    case '/parametre':
    case '/parametres':
        require_once __DIR__ . '/../src/controllers/ParametresController.php';
        (new ParametresController())->show();
        break;

    case '/api/parametres/update':
        require_once __DIR__ . '/../src/controllers/ParametresController.php';
        (new ParametresController())->updateProfile();
        break;

    case '/api/parametres/password':
        require_once __DIR__ . '/../src/controllers/ParametresController.php';
        (new ParametresController())->updatePassword();
        break;

    case '/api/parametres/avatar':
        require_once __DIR__ . '/../src/controllers/ParametresController.php';
        (new ParametresController())->uploadAvatar();
        break;

    case '/api/parametres/delete':
        require_once __DIR__ . '/../src/controllers/ParametresController.php';
        (new ParametresController())->deleteAccount();
        break;

    case '/api/check-username':
        require_once __DIR__ . '/../src/controllers/ParametresController.php';
        (new ParametresController())->checkUsername();
        break;
    // ────────────────────────────────────────────────────────

    // Documentation
    case '/documentation':
        require_once __DIR__ . '/../src/views/documentation.php';
        break;

    // ── API Spots ────────────────────────────────────────────
    case '/api/spots':
        require_once __DIR__ . '/../src/controllers/SpotController.php';
        $ctrl = new SpotController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ctrl->store();
        } else {
            $ctrl->index();
        }
        break;

    case '/api/spot':
        require_once __DIR__ . '/../src/controllers/SpotController.php';
        (new SpotController())->show();
        break;

    case '/api/spot/rate':
        require_once __DIR__ . '/../src/controllers/SpotController.php';
        (new SpotController())->rate();
        break;

    case '/api/spot/comment':
        require_once __DIR__ . '/../src/controllers/SpotController.php';
        (new SpotController())->comment();
        break;

    case '/api/spot/delete':
        require_once __DIR__ . '/../src/controllers/SpotController.php';
        (new SpotController())->destroy();
        break;
    // ────────────────────────────────────────────────────────

    // 404
    default:
        http_response_code(404);
        echo '<h1>404 — Page introuvable</h1>';
        break;
}