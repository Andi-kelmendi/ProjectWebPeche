<?php
// ============================================================
// src/controllers/SpotController.php
// Gère les spots de pêche : liste, détail, création, votes, avis
// ============================================================

require_once __DIR__ . '/../config/database.php';

class SpotController
{
    // --------------------------------------------------------
    // GET /api/spots → liste tous les spots (pour les marqueurs)
    // --------------------------------------------------------
    public function index(): void
    {
        try {
            $pdo  = Database::connect();
            $stmt = $pdo->query(
                'SELECT id, name, description, latitude, longitude, region, species, rating
                 FROM fishing_spots
                 ORDER BY created_at DESC'
            );
            $spots = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // On ajoute les compteurs like/dislike pour chaque spot
            $stmtVotes = $pdo->prepare(
                'SELECT SUM(score >= 4) AS likes, SUM(score <= 2) AS dislikes
                 FROM ratings WHERE spot_id = ?'
            );

            foreach ($spots as &$spot) {
                $stmtVotes->execute([$spot['id']]);
                $votes = $stmtVotes->fetch(PDO::FETCH_ASSOC);
                $spot['likes']    = (int) ($votes['likes']    ?? 0);
                $spot['dislikes'] = (int) ($votes['dislikes'] ?? 0);
            }

            $this->json($spots);
        } catch (\Throwable $e) {
            $this->json(['error' => 'Erreur serveur : ' . $e->getMessage()], 500);
        }
    }

    // --------------------------------------------------------
    // GET /api/spot?id=5 → détail d'un spot + avis + votes
    // --------------------------------------------------------
    public function show(): void
    {
        $id = (int) ($_GET['id'] ?? 0);

        if (!$id) {
            $this->json(['error' => 'ID manquant.'], 400);
            return;
        }

        try {
            $pdo = Database::connect();

            $stmt = $pdo->prepare('SELECT * FROM fishing_spots WHERE id = ?');
            $stmt->execute([$id]);
            $spot = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$spot) {
                $this->json(['error' => 'Spot introuvable.'], 404);
                return;
            }

            // Compteurs like / dislike
            $stmt = $pdo->prepare(
                'SELECT SUM(score >= 4) AS likes, SUM(score <= 2) AS dislikes
                 FROM ratings WHERE spot_id = ?'
            );
            $stmt->execute([$id]);
            $votes = $stmt->fetch(PDO::FETCH_ASSOC);
            $spot['likes']    = (int) ($votes['likes']    ?? 0);
            $spot['dislikes'] = (int) ($votes['dislikes'] ?? 0);

            // Avis (commentaires) avec le pseudo de l'auteur
            $stmt = $pdo->prepare(
                'SELECT c.comment, c.created_at, u.username
                 FROM spot_comments c
                 JOIN users u ON u.id = c.user_id
                 WHERE c.spot_id = ?
                 ORDER BY c.created_at DESC'
            );
            $stmt->execute([$id]);
            $spot['comments'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $this->json($spot);
        } catch (\Throwable $e) {
            // On renvoie le vrai message d'erreur pour pouvoir diagnostiquer
            // (cause la plus fréquente : la table "spot_comments" n'existe pas
            // encore → lance update_reviews.sql dans ta base de données)
            $this->json(['error' => 'Erreur serveur : ' . $e->getMessage()], 500);
        }
    }

    // --------------------------------------------------------
    // POST /api/spots → crée un nouveau spot
    // Seul le nom est obligatoire — tout le reste est facultatif
    // (une personne peut ne pas savoir ce qu'elle a pêché)
    // --------------------------------------------------------
    public function store(): void
    {
        if (empty($_SESSION['user_id'])) {
            $this->json(['error' => 'Vous devez être connecté pour ajouter un spot.'], 401);
            return;
        }

        $name        = trim($_POST['name']        ?? '');
        $latitude    = $_POST['latitude']          ?? null;
        $longitude   = $_POST['longitude']         ?? null;
        $region      = trim($_POST['region']      ?? '');
        $species     = trim($_POST['species']     ?? '');
        $description = trim($_POST['description'] ?? '');

        if ($name === '' || $latitude === null || $longitude === null) {
            $this->json(['error' => 'Le nom et la position sur la carte sont obligatoires.'], 400);
            return;
        }

        try {
            $pdo  = Database::connect();
            $stmt = $pdo->prepare(
                'INSERT INTO fishing_spots (user_id, name, description, latitude, longitude, region, species)
                 VALUES (?, ?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([
                $_SESSION['user_id'],
                $name,
                $description !== '' ? $description : null,
                $latitude,
                $longitude,
                $region !== '' ? $region : null,
                $species !== '' ? $species : null,
            ]);

            $newId = $pdo->lastInsertId();

            $stmt = $pdo->prepare('SELECT * FROM fishing_spots WHERE id = ?');
            $stmt->execute([$newId]);
            $spot = $stmt->fetch(PDO::FETCH_ASSOC);
            $spot['likes']    = 0;
            $spot['dislikes'] = 0;

            $this->json($spot, 201);
        } catch (\Throwable $e) {
            $this->json(['error' => 'Erreur serveur : ' . $e->getMessage()], 500);
        }
    }

    // --------------------------------------------------------
    // POST /api/spot/rate → "j'aime" / "je n'aime pas" un spot
    // Un seul vote par utilisateur et par spot (on peut changer d'avis)
    // --------------------------------------------------------
    public function rate(): void
    {
        if (empty($_SESSION['user_id'])) {
            $this->json(['error' => 'Vous devez être connecté pour voter.'], 401);
            return;
        }

        $spotId = (int) ($_POST['spot_id'] ?? 0);
        $vote   = $_POST['vote'] ?? '';

        if (!$spotId || !in_array($vote, ['like', 'dislike'], true)) {
            $this->json(['error' => 'Requête invalide.'], 400);
            return;
        }

        // "like" = note de 5, "dislike" = note de 1
        $score = $vote === 'like' ? 5 : 1;

        try {
            $pdo = Database::connect();

            $stmt = $pdo->prepare(
                'INSERT INTO ratings (user_id, spot_id, score)
                 VALUES (?, ?, ?)
                 ON DUPLICATE KEY UPDATE score = ?'
            );
            $stmt->execute([$_SESSION['user_id'], $spotId, $score, $score]);

            // Recalcule la note moyenne du spot
            $stmt = $pdo->prepare('SELECT ROUND(AVG(score), 1) AS avg_rating FROM ratings WHERE spot_id = ?');
            $stmt->execute([$spotId]);
            $avg = $stmt->fetch(PDO::FETCH_ASSOC)['avg_rating'] ?? 0;

            $pdo->prepare('UPDATE fishing_spots SET rating = ? WHERE id = ?')->execute([$avg, $spotId]);

            // Recompte les votes pour renvoyer des chiffres à jour
            $stmt = $pdo->prepare(
                'SELECT SUM(score >= 4) AS likes, SUM(score <= 2) AS dislikes
                 FROM ratings WHERE spot_id = ?'
            );
            $stmt->execute([$spotId]);
            $votes = $stmt->fetch(PDO::FETCH_ASSOC);

            $this->json([
                'rating'   => (float) $avg,
                'likes'    => (int) ($votes['likes']    ?? 0),
                'dislikes' => (int) ($votes['dislikes'] ?? 0),
            ]);
        } catch (\Throwable $e) {
            $this->json(['error' => 'Erreur serveur : ' . $e->getMessage()], 500);
        }
    }

    // --------------------------------------------------------
    // POST /api/spot/comment → ajoute un avis (texte) sur un spot
    // --------------------------------------------------------
    public function comment(): void
    {
        if (empty($_SESSION['user_id'])) {
            $this->json(['error' => 'Vous devez être connecté pour laisser un avis.'], 401);
            return;
        }

        $spotId  = (int) ($_POST['spot_id'] ?? 0);
        $comment = trim($_POST['comment']  ?? '');

        if (!$spotId || $comment === '') {
            $this->json(['error' => 'Votre avis ne peut pas être vide.'], 400);
            return;
        }

        try {
            $pdo  = Database::connect();
            $stmt = $pdo->prepare(
                'INSERT INTO spot_comments (user_id, spot_id, comment) VALUES (?, ?, ?)'
            );
            $stmt->execute([$_SESSION['user_id'], $spotId, $comment]);

            $this->json([
                'comment'    => $comment,
                'username'   => $_SESSION['username'],
                'created_at' => date('Y-m-d H:i:s'),
            ], 201);
        } catch (\Throwable $e) {
            $this->json(['error' => 'Erreur serveur : ' . $e->getMessage()], 500);
        }
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