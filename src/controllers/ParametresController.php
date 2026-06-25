<?php
// ============================================================
// src/controllers/ParametresController.php
//
// Routes :
//   GET  /parametres                → show()
//   POST /api/parametres/update     → updateProfile()   (username + email)
//   POST /api/parametres/password   → updatePassword()  (mot de passe)
//   POST /api/parametres/avatar     → uploadAvatar()    (photo de profil)
//   POST /api/parametres/delete     → deleteAccount()   (suppression)
//   GET  /api/check-username        → checkUsername()   (unicité username)
// ============================================================

require_once __DIR__ . '/../config/database.php';

class ParametresController
{
    /** GET /parametres — affiche la page */
    public function show(): void
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /login'); exit;
        }
        $pdo  = Database::connect();
        $stmt = $pdo->prepare('SELECT id, username, email, avatar, created_at FROM users WHERE id = ?');
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();

        if (!$user) { header('Location: /logout'); exit; }

        require_once __DIR__ . '/../views/parametres.php';
    }

    /** POST /api/parametres/update — change username ET/OU email */
    public function updateProfile(): void
    {
        $this->jsonHeader();
        if (empty($_SESSION['user_id'])) { $this->err(401, 'Non authentifié'); return; }

        $body     = $this->body();
        $username = trim($body['username'] ?? '');
        $email    = trim($body['email']    ?? '');

        // ── Validation username ──
        if ($username !== '') {
            if (!preg_match('/^[a-zA-Z0-9._\-]{3,50}$/', $username)) {
                $this->err(422, 'Nom invalide. Utilisez uniquement a-z A-Z 0-9 . - _ (3 à 50 caractères).');
                return;
            }
            $pdo  = Database::connect();
            $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ? AND id != ?');
            $stmt->execute([$username, $_SESSION['user_id']]);
            if ($stmt->fetch()) { $this->err(409, 'Ce nom d\'utilisateur est déjà pris.'); return; }
        }

        // ── Validation email ──
        if ($email !== '') {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->err(422, 'Adresse email invalide.'); return;
            }
            $pdo  = Database::connect();
            $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? AND id != ?');
            $stmt->execute([$email, $_SESSION['user_id']]);
            if ($stmt->fetch()) { $this->err(409, 'Cet email est déjà utilisé.'); return; }
        }

        if ($username === '' && $email === '') {
            $this->err(422, 'Aucune modification à enregistrer.'); return;
        }

        // ── Mise à jour en DB ──
        $pdo = Database::connect();
        if ($username !== '' && $email !== '') {
            $pdo->prepare('UPDATE users SET username = ?, email = ? WHERE id = ?')
                ->execute([$username, $email, $_SESSION['user_id']]);
        } elseif ($username !== '') {
            $pdo->prepare('UPDATE users SET username = ? WHERE id = ?')
                ->execute([$username, $_SESSION['user_id']]);
        } else {
            $pdo->prepare('UPDATE users SET email = ? WHERE id = ?')
                ->execute([$email, $_SESSION['user_id']]);
        }

        // Mise à jour session
        if ($username !== '') $_SESSION['username'] = $username;
        if ($email    !== '') $_SESSION['email']    = $email;

        echo json_encode(['success' => true, 'username' => $_SESSION['username'], 'email' => $_SESSION['email']]);
    }

    /** POST /api/parametres/password — change le mot de passe */
    public function updatePassword(): void
    {
        $this->jsonHeader();
        if (empty($_SESSION['user_id'])) { $this->err(401, 'Non authentifié'); return; }

        $body        = $this->body();
        $current     = $body['current_password'] ?? '';
        $newPass     = $body['new_password']     ?? '';
        $confirm     = $body['confirm_password'] ?? '';

        if ($current === '' || $newPass === '' || $confirm === '') {
            $this->err(422, 'Remplissez tous les champs.'); return;
        }
        if ($newPass !== $confirm) {
            $this->err(422, 'Les nouveaux mots de passe ne correspondent pas.'); return;
        }
        if (strlen($newPass) < 8) {
            $this->err(422, 'Le mot de passe doit faire au moins 8 caractères.'); return;
        }

        $pdo  = Database::connect();
        $stmt = $pdo->prepare('SELECT password FROM users WHERE id = ?');
        $stmt->execute([$_SESSION['user_id']]);
        $row  = $stmt->fetch();

        if (!$row || !password_verify($current, $row['password'])) {
            $this->err(403, 'Mot de passe actuel incorrect.'); return;
        }

        $hash = password_hash($newPass, PASSWORD_BCRYPT);
        $pdo->prepare('UPDATE users SET password = ? WHERE id = ?')
            ->execute([$hash, $_SESSION['user_id']]);

        echo json_encode(['success' => true]);
    }

    /** POST /api/parametres/avatar — upload photo de profil */
    public function uploadAvatar(): void
    {
        $this->jsonHeader();
        if (empty($_SESSION['user_id'])) { $this->err(401, 'Non authentifié'); return; }

        if (empty($_FILES['avatar'])) { $this->err(400, 'Aucun fichier reçu.'); return; }

        $file = $_FILES['avatar'];
        $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $mime    = mime_content_type($file['tmp_name']);

        if (!in_array($mime, $allowed)) {
            $this->err(415, 'Format non supporté. JPEG, PNG, WebP ou GIF uniquement.'); return;
        }
        if ($file['size'] > 3 * 1024 * 1024) { // 3 Mo max
            $this->err(413, 'Fichier trop volumineux (3 Mo max).'); return;
        }

        $ext    = match($mime) {
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
            'image/gif'  => 'gif',
        };
        $dir    = __DIR__ . '/../../public/assets/img/avatars/';
        $name   = 'user_' . $_SESSION['user_id'] . '.' . $ext;
        $path   = $dir . $name;

        // Supprime l'ancien avatar si format différent
        foreach (['jpg','png','webp','gif'] as $e) {
            $old = $dir . 'user_' . $_SESSION['user_id'] . '.' . $e;
            if ($old !== $path && file_exists($old)) unlink($old);
        }

        if (!move_uploaded_file($file['tmp_name'], $path)) {
            $this->err(500, 'Erreur lors de la sauvegarde du fichier.'); return;
        }

        $avatarUrl = '/assets/img/avatars/' . $name;
        $pdo = Database::connect();
        $pdo->prepare('UPDATE users SET avatar = ? WHERE id = ?')
            ->execute([$avatarUrl, $_SESSION['user_id']]);

        echo json_encode(['success' => true, 'avatar' => $avatarUrl . '?t=' . time()]);
    }

    /** POST /api/parametres/delete — supprime le compte */
    public function deleteAccount(): void
    {
        $this->jsonHeader();
        if (empty($_SESSION['user_id'])) { $this->err(401, 'Non authentifié'); return; }

        $body     = $this->body();
        $password = $body['password'] ?? '';

        if ($password === '') { $this->err(422, 'Confirmez avec votre mot de passe.'); return; }

        $pdo  = Database::connect();
        $stmt = $pdo->prepare('SELECT password FROM users WHERE id = ?');
        $stmt->execute([$_SESSION['user_id']]);
        $row  = $stmt->fetch();

        if (!$row || !password_verify($password, $row['password'])) {
            $this->err(403, 'Mot de passe incorrect.'); return;
        }

        // Supprime l'avatar physique
        foreach (['jpg','png','webp','gif'] as $e) {
            $f = __DIR__ . '/../../public/assets/img/avatars/user_' . $_SESSION['user_id'] . '.' . $e;
            if (file_exists($f)) unlink($f);
        }

        $pdo->prepare('DELETE FROM users WHERE id = ?')->execute([$_SESSION['user_id']]);

        // Détruit la session
        session_destroy();
        setcookie('remember_me', '', time() - 3600, '/');

        echo json_encode(['success' => true]);
    }

    /** GET /api/check-username?username=xxx — vérifie si le pseudo est disponible */
    public function checkUsername(): void
    {
        $this->jsonHeader();
        $username = trim($_GET['username'] ?? '');

        if (!preg_match('/^[a-zA-Z0-9._\-]{3,50}$/', $username)) {
            echo json_encode(['available' => false, 'reason' => 'format']);
            return;
        }

        $pdo  = Database::connect();
        $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ? AND id != ?');
        $stmt->execute([$username, $_SESSION['user_id'] ?? 0]);

        echo json_encode(['available' => !$stmt->fetch()]);
    }

    // ── Helpers ──────────────────────────────────────────────

    private function jsonHeader(): void
    {
        header('Content-Type: application/json; charset=utf-8');
    }

    private function body(): array
    {
        $raw = file_get_contents('php://input');
        $d   = json_decode($raw, true);
        return is_array($d) ? $d : [];
    }

    private function err(int $code, string $message): void
    {
        http_response_code($code);
        echo json_encode(['success' => false, 'message' => $message]);
    }
}