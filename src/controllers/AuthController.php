<?php
// ============================================================
// src/controllers/AuthController.php
// Gère l'affichage ET le traitement des formulaires
// ============================================================

require_once __DIR__ . '/../config/database.php';

class AuthController
{
    // --------------------------------------------------------
    // GET  /login  → affiche le formulaire
    // POST /login  → traite la connexion
    // --------------------------------------------------------
    public function login(): void
    {
        if (!empty($_SESSION['user_id'])) {
            header('Location: /accueil');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleLogin();
        } else {
            require_once __DIR__ . '/../views/auth/login.php';
        }
    }

    // --------------------------------------------------------
    // GET  /register  → affiche le formulaire
    // POST /register  → traite l'inscription
    // --------------------------------------------------------
    public function register(): void
    {
        if (!empty($_SESSION['user_id'])) {
            header('Location: /accueil');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleRegister();
        } else {
            require_once __DIR__ . '/../views/auth/register.php';
        }
    }

    // --------------------------------------------------------
    // GET /logout → déconnecte l'utilisateur
    // --------------------------------------------------------
    public function logout(): void
    {
        // Supprime aussi le jeton "se souvenir de moi" en base, s'il existe
        if (!empty($_COOKIE['remember_token'])) {
            try {
                $pdo       = Database::connect();
                $tokenHash = hash('sha256', $_COOKIE['remember_token']);
                $pdo->prepare('DELETE FROM remember_tokens WHERE token_hash = ?')->execute([$tokenHash]);
            } catch (\Throwable $e) {
                // si la table n'existe pas encore, on ignore simplement
            }
            setcookie('remember_token', '', time() - 3600, '/');
        }

        $_SESSION = [];
        session_destroy();

        header('Location: /login');
        exit;
    }

    // --------------------------------------------------------
    // Traitement de la connexion (POST /login)
    // --------------------------------------------------------
    private function handleLogin(): void
    {
        $email    = trim($_POST['email']    ?? '');
        $password =      $_POST['password'] ?? '';

        // Validation basique
        if (empty($email) || empty($password)) {
            $_SESSION['auth_error'] = 'Veuillez remplir tous les champs.';
            header('Location: /login');
            exit;
        }

        $pdo  = Database::connect();
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Vérification email + mot de passe
        if (!$user || !password_verify($password, $user['password'])) {
            $_SESSION['auth_error'] = 'Email ou mot de passe incorrect.';
            header('Location: /login');
            exit;
        }

        // Connexion réussie → on stocke l'utilisateur en session
        $_SESSION['user_id']  = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email']    = $user['email'];
        $_SESSION['is_admin'] = !empty($user['is_admin']);

        // "Se souvenir de moi" : crée un jeton qui survit à la fermeture du navigateur
        if (!empty($_POST['remember'])) {
            $this->rememberUser($pdo, (int) $user['id']);
        }

        // Redirection vers la page d'accueil (carte)
        header('Location: /accueil');
        exit;
    }

    // --------------------------------------------------------
    // Traitement de l'inscription (POST /register)
    // --------------------------------------------------------
    private function handleRegister(): void
    {
        $username = trim($_POST['username'] ?? '');
        $email    = trim($_POST['email']    ?? '');
        $password =      $_POST['password'] ?? '';

        // Validation
        if (empty($username) || empty($email) || empty($password)) {
            $_SESSION['auth_error'] = 'Veuillez remplir tous les champs obligatoires.';
            header('Location: /register');
            exit;
        }

        if (strlen($password) < 6) {
            $_SESSION['auth_error'] = 'Le mot de passe doit contenir au moins 6 caractères.';
            header('Location: /register');
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['auth_error'] = 'Adresse email invalide.';
            header('Location: /register');
            exit;
        }

        $pdo = Database::connect();

        // Vérifie si l'email est déjà utilisé
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $_SESSION['auth_error'] = 'Cet email est déjà utilisé.';
            header('Location: /register');
            exit;
        }

        // Insertion du nouvel utilisateur (jamais admin par défaut — voir documentation
        // pour savoir comment désigner un compte admin manuellement en base)
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare('INSERT INTO users (username, email, password) VALUES (?, ?, ?)');
        $stmt->execute([$username, $email, $hash]);
        $newId = (int) $pdo->lastInsertId();

        // Connecte directement l'utilisateur après inscription
        $_SESSION['user_id']  = $newId;
        $_SESSION['username'] = $username;
        $_SESSION['email']    = $email;
        $_SESSION['is_admin'] = false;

        // "Se souvenir de moi" peut aussi être cochée dès l'inscription
        if (!empty($_POST['remember'])) {
            $this->rememberUser($pdo, $newId);
        }

        // Redirection directe vers la page d'accueil (carte)
        header('Location: /accueil');
        exit;
    }

    // --------------------------------------------------------
    // Crée un jeton "se souvenir de moi" longue durée (30 jours)
    // Le cookie ne contient jamais le jeton en clair côté base de données
    // (on stocke seulement son empreinte SHA-256, comme un mot de passe)
    // --------------------------------------------------------
    private function rememberUser(PDO $pdo, int $userId): void
    {
        $rawToken  = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $rawToken);
        $expiresAt = date('Y-m-d H:i:s', time() + 60 * 60 * 24 * 30);

        $stmt = $pdo->prepare(
            'INSERT INTO remember_tokens (user_id, token_hash, expires_at) VALUES (?, ?, ?)'
        );
        $stmt->execute([$userId, $tokenHash, $expiresAt]);

        setcookie('remember_token', $rawToken, [
            'expires'  => time() + 60 * 60 * 24 * 30,
            'path'     => '/',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }
}