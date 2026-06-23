<?php
// ============================================================
// src/controllers/SpotController.php
// Gère les spots de pêche : liste, détail, création, notes (étoiles), avis
//
// NOTE IMPORTANTE :
// La note rapide (étoiles) et l'avis texte partagent maintenant la même
// table "spot_reviews" (1 ligne par utilisateur et par spot). On peut noter
// sans écrire de texte (vote rapide), ou noter + écrire un avis complet.
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

            $this->json($spots);
        } catch (\Throwable $e) {
            $this->json(['error' => 'Erreur serveur : ' . $e->getMessage()], 500);
        }
    }

    // --------------------------------------------------------
    // GET /api/spot?id=5 → détail d'un spot + ses avis écrits
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

            // Avis écrits uniquement (ceux qui ont un commentaire texte),
            // chacun avec sa note en étoiles pour permettre le filtrage côté client
            $stmt = $pdo->prepare(
                "SELECT r.score, r.comment, r.created_at, u.username
                 FROM spot_reviews r
                 JOIN users u ON u.id = r.user_id
                 WHERE r.spot_id = ? AND r.comment IS NOT NULL AND r.comment <> ''
                 ORDER BY r.created_at DESC"
            );
            $stmt->execute([$id]);
            $spot['comments'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $this->json($spot);
        } catch (\Throwable $e) {
            $this->json(['error' => 'Erreur serveur : ' . $e->getMessage()], 500);
        }
    }

    // --------------------------------------------------------
    // POST /api/spots → crée un nouveau spot
    // Seul le nom est obligatoire — tout le reste est facultatif
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

            $this->json($spot, 201);
        } catch (\Throwable $e) {
            $this->json(['error' => 'Erreur serveur : ' . $e->getMessage()], 500);
        }
    }

    // --------------------------------------------------------
    // POST /api/spot/rate → note rapide en étoiles (1 à 5), sans texte
    // Un seul vote par utilisateur et par spot (on peut le changer)
    // --------------------------------------------------------
    public function rate(): void
    {
        if (empty($_SESSION['user_id'])) {
            $this->json(['error' => 'Vous devez être connecté pour voter.'], 401);
            return;
        }

        $spotId = (int) ($_POST['spot_id'] ?? 0);
        $score  = (int) ($_POST['score']   ?? 0);

        if (!$spotId || $score < 1 || $score > 5) {
            $this->json(['error' => 'La note doit être comprise entre 1 et 5 étoiles.'], 400);
            return;
        }

        try {
            $pdo = Database::connect();

            // Si l'utilisateur avait déjà laissé un avis avec texte, on garde
            // son commentaire — on ne met à jour que la note (score)
            $stmt = $pdo->prepare(
                'INSERT INTO spot_reviews (user_id, spot_id, score)
                 VALUES (?, ?, ?)
                 ON DUPLICATE KEY UPDATE score = ?'
            );
            $stmt->execute([$_SESSION['user_id'], $spotId, $score, $score]);

            $avg = $this->recalculateRating($pdo, $spotId);

            $this->json(['rating' => $avg]);
        } catch (\Throwable $e) {
            $this->json(['error' => 'Erreur serveur : ' . $e->getMessage()], 500);
        }
    }

    // --------------------------------------------------------
    // POST /api/spot/comment → publie un avis complet (note + texte)
    // --------------------------------------------------------
    public function comment(): void
    {
        if (empty($_SESSION['user_id'])) {
            $this->json(['error' => 'Vous devez être connecté pour laisser un avis.'], 401);
            return;
        }

        $spotId  = (int) ($_POST['spot_id'] ?? 0);
        $score   = (int) ($_POST['score']   ?? 0);
        $comment = trim($_POST['comment']  ?? '');

        if (!$spotId || $comment === '') {
            $this->json(['error' => 'Votre avis ne peut pas être vide.'], 400);
            return;
        }

        if ($score < 1 || $score > 5) {
            $this->json(['error' => 'Merci de donner une note de 1 à 5 étoiles avant de publier votre avis.'], 400);
            return;
        }

        try {
            $pdo  = Database::connect();
            $stmt = $pdo->prepare(
                'INSERT INTO spot_reviews (user_id, spot_id, score, comment)
                 VALUES (?, ?, ?, ?)
                 ON DUPLICATE KEY UPDATE score = ?, comment = ?'
            );
            $stmt->execute([$_SESSION['user_id'], $spotId, $score, $comment, $score, $comment]);

            $this->recalculateRating($pdo, $spotId);

            $this->json([
                'score'      => $score,
                'comment'    => $comment,
                'username'   => $_SESSION['username'],
                'created_at' => date('Y-m-d H:i:s'),
            ], 201);
        } catch (\Throwable $e) {
            $this->json(['error' => 'Erreur serveur : ' . $e->getMessage()], 500);
        }
    }

    // --------------------------------------------------------
    // Recalcule la note moyenne d'un spot et la met en cache
    // dans fishing_spots.rating (évite de recalculer à chaque affichage)
    // --------------------------------------------------------
    private function recalculateRating(PDO $pdo, int $spotId): float
    {
        $stmt = $pdo->prepare('SELECT ROUND(AVG(score), 1) AS avg_rating FROM spot_reviews WHERE spot_id = ?');
        $stmt->execute([$spotId]);
        $avg = (float) ($stmt->fetch(PDO::FETCH_ASSOC)['avg_rating'] ?? 0);

        $pdo->prepare('UPDATE fishing_spots SET rating = ? WHERE id = ?')->execute([$avg, $spotId]);

        return $avg;
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