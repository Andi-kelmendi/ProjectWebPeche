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
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleRegister();
        } else {
            require_once __DIR__ . '/../views/auth/register.php';
        }
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

        // Cookie "Se souvenir de moi" (30 jours)
        if (!empty($_POST['remember'])) {
            setcookie('remember_me', session_id(), time() + 60 * 60 * 24 * 30, '/');
        }

        header('Location: /');
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

        // Insertion du nouvel utilisateur
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare('INSERT INTO users (username, email, password) VALUES (?, ?, ?)');
        $stmt->execute([$username, $email, $hash]);

        // Redirige vers /login avec un message de succès
        $_SESSION['auth_success'] = 'Compte créé avec succès ! Vous pouvez maintenant vous connecter.';
        header('Location: /login');
        exit;
    }
}