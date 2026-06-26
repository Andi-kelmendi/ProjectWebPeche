<?php
// ============================================================
// src/controllers/CommunityController.php
// Gère le fil de la communauté : posts texte + commentaires
// ============================================================

require_once __DIR__ . '/../config/database.php';

class CommunityController
{
    // --------------------------------------------------------
    // GET /api/posts[?search=...] → liste des posts (+ leurs commentaires)
    // --------------------------------------------------------
    public function index(): void
    {
        try {
            $pdo    = Database::connect();
            $search = trim($_GET['search'] ?? '');

            if ($search !== '') {
                $stmt = $pdo->prepare(
                    "SELECT p.id, p.user_id, p.title, p.content, p.created_at, u.username
                     FROM community_posts p
                     JOIN users u ON u.id = p.user_id
                     WHERE p.title LIKE ? OR p.content LIKE ?
                     ORDER BY p.created_at DESC"
                );
                $like = '%' . $search . '%';
                $stmt->execute([$like, $like]);
            } else {
                $stmt = $pdo->query(
                    "SELECT p.id, p.user_id, p.title, p.content, p.created_at, u.username
                     FROM community_posts p
                     JOIN users u ON u.id = p.user_id
                     ORDER BY p.created_at DESC"
                );
            }

            $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Récupère les commentaires de chaque post
            $stmtComments = $pdo->prepare(
                "SELECT c.content, c.created_at, u.username
                 FROM community_comments c
                 JOIN users u ON u.id = c.user_id
                 WHERE c.post_id = ?
                 ORDER BY c.created_at ASC"
            );

            foreach ($posts as &$post) {
                $stmtComments->execute([$post['id']]);
                $post['comments']   = $stmtComments->fetchAll(PDO::FETCH_ASSOC);
                $post['can_delete'] = $this->canDelete($post);
            }

            $this->json($posts);
        } catch (\Throwable $e) {
            $this->json(['error' => 'Erreur serveur : ' . $e->getMessage()], 500);
        }
    }

    // --------------------------------------------------------
    // POST /api/posts → publie un nouveau post
    // --------------------------------------------------------
    public function store(): void
    {
        if (empty($_SESSION['user_id'])) {
            $this->json(['error' => 'Vous devez être connecté pour publier.'], 401);
            return;
        }

        $title   = trim($_POST['title']   ?? '');
        $content = trim($_POST['content'] ?? '');

        if ($title === '' || $content === '') {
            $this->json(['error' => 'Le titre et le message sont obligatoires.'], 400);
            return;
        }

        try {
            $pdo  = Database::connect();
            $stmt = $pdo->prepare('INSERT INTO community_posts (user_id, title, content) VALUES (?, ?, ?)');
            $stmt->execute([$_SESSION['user_id'], $title, $content]);
            $newId = $pdo->lastInsertId();

            $this->json([
                'id'         => (int) $newId,
                'user_id'    => (int) $_SESSION['user_id'],
                'title'      => $title,
                'content'    => $content,
                'username'   => $_SESSION['username'],
                'created_at' => date('Y-m-d H:i:s'),
                'comments'   => [],
                'can_delete' => true, // on vient de le créer soi-même
            ], 201);
        } catch (\Throwable $e) {
            $this->json(['error' => 'Erreur serveur : ' . $e->getMessage()], 500);
        }
    }

    // --------------------------------------------------------
    // POST /api/post/comment → ajoute un commentaire à un post
    // --------------------------------------------------------
    public function comment(): void
    {
        if (empty($_SESSION['user_id'])) {
            $this->json(['error' => 'Vous devez être connecté pour commenter.'], 401);
            return;
        }

        $postId  = (int) ($_POST['post_id'] ?? 0);
        $content = trim($_POST['content']  ?? '');

        if (!$postId || $content === '') {
            $this->json(['error' => 'Le commentaire ne peut pas être vide.'], 400);
            return;
        }

        try {
            $pdo  = Database::connect();
            $stmt = $pdo->prepare('INSERT INTO community_comments (user_id, post_id, content) VALUES (?, ?, ?)');
            $stmt->execute([$_SESSION['user_id'], $postId, $content]);

            $this->json([
                'content'    => $content,
                'username'   => $_SESSION['username'],
                'created_at' => date('Y-m-d H:i:s'),
            ], 201);
        } catch (\Throwable $e) {
            $this->json(['error' => 'Erreur serveur : ' . $e->getMessage()], 500);
        }
    }

    // --------------------------------------------------------
    // POST /api/post/delete → supprime un post
    // Autorisé uniquement pour : son auteur, OU un compte admin
    // --------------------------------------------------------
    public function destroy(): void
    {
        if (empty($_SESSION['user_id'])) {
            $this->json(['error' => 'Vous devez être connecté.'], 401);
            return;
        }

        $postId = (int) ($_POST['post_id'] ?? 0);
        if (!$postId) {
            $this->json(['error' => 'ID manquant.'], 400);
            return;
        }

        try {
            $pdo  = Database::connect();
            $stmt = $pdo->prepare('SELECT user_id FROM community_posts WHERE id = ?');
            $stmt->execute([$postId]);
            $post = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$post) {
                $this->json(['error' => 'Post introuvable.'], 404);
                return;
            }

            if (!$this->canDelete($post)) {
                $this->json(['error' => "Vous n'avez pas le droit de supprimer ce post."], 403);
                return;
            }

            // Les commentaires liés sont supprimés automatiquement (ON DELETE CASCADE)
            $pdo->prepare('DELETE FROM community_posts WHERE id = ?')->execute([$postId]);

            $this->json(['success' => true]);
        } catch (\Throwable $e) {
            $this->json(['error' => 'Erreur serveur : ' . $e->getMessage()], 500);
        }
    }

    // --------------------------------------------------------
    // Un post peut être supprimé par son auteur, ou par un admin
    // --------------------------------------------------------
    private function canDelete(array $post): bool
    {
        if (empty($_SESSION['user_id'])) {
            return false;
        }

        if (!empty($_SESSION['is_admin'])) {
            return true;
        }

        return (int) $post['user_id'] === (int) $_SESSION['user_id'];
    }

    // --------------------------------------------------------
    // Petit utilitaire pour répondre en JSON
    // --------------------------------------------------------
    private function json($data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
    }
}