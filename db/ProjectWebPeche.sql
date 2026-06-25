-- ============================================================
-- Base de données : WebPêche (complète et à jour)
-- Créez la base avec : CREATE DATABASE projectwebpeche;
-- Puis importez ce fichier : mysql -u root -p projectwebpeche < ProjectWebPeche.sql
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ------------------------------------------------------------
-- TABLE : users  (les membres du site)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
    `id`            INT(11)         NOT NULL AUTO_INCREMENT,
    `username`      VARCHAR(50)     NOT NULL,           -- pseudo affiché
    `email`         VARCHAR(100)    NOT NULL UNIQUE,    -- adresse mail (unique)
    `password`      VARCHAR(255)    NOT NULL,           -- mot de passe hashé (bcrypt)
    `is_admin`      TINYINT(1)      NOT NULL DEFAULT 0, -- 0 = utilisateur normal, 1 = admin
    `avatar`        VARCHAR(255)    DEFAULT NULL,       -- chemin vers la photo de profil
    `created_at`    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- TABLE : remember_tokens  (jetons "Se souvenir de moi")
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `remember_tokens` (
    `id`            INT(11)      NOT NULL AUTO_INCREMENT,
    `user_id`       INT(11)      NOT NULL,
    `token_hash`    VARCHAR(255) NOT NULL,              -- empreinte du jeton (jamais le jeton en clair)
    `expires_at`    DATETIME     NOT NULL,
    `created_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- TABLE : fishing_spots  (les spots de pêche)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `fishing_spots` (
    `id`            INT(11)         NOT NULL AUTO_INCREMENT,
    `user_id`       INT(11)         NOT NULL,           -- celui qui a ajouté le spot
    `name`          VARCHAR(100)    NOT NULL,           -- nom du spot
    `description`   TEXT            DEFAULT NULL,       -- description libre
    `latitude`      DECIMAL(10,8)   NOT NULL,           -- coordonnées GPS
    `longitude`     DECIMAL(11,8)   NOT NULL,
    `region`        VARCHAR(255)    DEFAULT NULL,       -- adresse détectée automatiquement au clic
    `species`       VARCHAR(255)    DEFAULT NULL,       -- ex: truite fario, brochet
    `image`         VARCHAR(255)    DEFAULT NULL,       -- image du spot
    `rating`        DECIMAL(3,1)    DEFAULT 0.0,        -- note moyenne (sur 5), recalculée à chaque vote
    `created_at`    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- TABLE : spot_reviews  (notes en étoiles + avis texte, fusionnés)
-- 1 ligne par utilisateur et par spot :
--   - score   : note de 1 à 5 étoiles (obligatoire)
--   - comment : avis écrit (facultatif — NULL si c'est juste une note rapide)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `spot_reviews` (
    `id`            INT(11)      NOT NULL AUTO_INCREMENT,
    `user_id`       INT(11)      NOT NULL,
    `spot_id`       INT(11)      NOT NULL,
    `score`         TINYINT(1)   NOT NULL,              -- note de 1 à 5
    `comment`       TEXT         DEFAULT NULL,           -- avis écrit (facultatif)
    `created_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_review` (`user_id`, `spot_id`),  -- 1 avis par user par spot (peut être modifié)
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`spot_id`) REFERENCES `fishing_spots`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Données de test : 1 compte admin + 3 spots populaires
-- ------------------------------------------------------------
INSERT INTO `users` (`username`, `email`, `password`, `is_admin`) VALUES
('admin', 'admin@webpeche.fr', '$2b$12$Sqr8QZs2RJ4SwGREoIS6GOZki8wFj.I1idZEdnl37PVU5sg9xf6Dy', 1);
-- Identifiants de connexion : admin@webpeche.fr / password123  (à changer plus tard si besoin !)

INSERT INTO `fishing_spots` (`user_id`, `name`, `description`, `latitude`, `longitude`, `region`, `species`, `image`, `rating`) VALUES
(1, 'Rivière des Truites',   'Magnifique rivière de montagne idéale pour la pêche à la mouche.', 43.7102, 7.2620,  'Alpes-Maritimes', 'truite fario, ombre chevalier', 'riviere-truites.jpg',  4.8),
(1, 'Lac de Monteynard – Isère', 'Lac turquoise aux eaux claires, spot incontournable.',            44.9764, 5.6225,  'Isère',           'truite fario, saumon de fontaine', 'lac-monteynard.jpg', 4.8),
(1, 'Lac du Salagou – Hérault', 'Lac aux rives rouges, poissons nombreux et variés.',              43.6522, 3.3790,  'Hérault',         'truite fario, ombre commun, saumon de fontaine', 'lac-salagou.jpg', 4.8);

SET FOREIGN_KEY_CHECKS = 1;