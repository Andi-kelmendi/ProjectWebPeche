-- ============================================================
-- Base de données : WebPêche
-- Créez la base avec : CREATE DATABASE webpeche;
-- Puis importez ce fichier : mysql -u root -p webpeche < ProjectWebPeche.sql
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
    `avatar`        VARCHAR(255)    DEFAULT NULL,       -- chemin vers la photo de profil
    `created_at`    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
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
    `region`        VARCHAR(100)    DEFAULT NULL,       -- ex: Alpes-Maritimes
    `species`       VARCHAR(255)    DEFAULT NULL,       -- ex: truite fario, brochet
    `image`         VARCHAR(255)    DEFAULT NULL,       -- image du spot
    `rating`        DECIMAL(2,1)    DEFAULT 0.0,        -- note moyenne
    `created_at`    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- TABLE : ratings  (notes données par les utilisateurs)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `ratings` (
    `id`            INT(11)         NOT NULL AUTO_INCREMENT,
    `user_id`       INT(11)         NOT NULL,
    `spot_id`       INT(11)         NOT NULL,
    `score`         TINYINT(1)      NOT NULL,           -- note de 1 à 5
    `created_at`    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_rating` (`user_id`, `spot_id`),  -- 1 note par user par spot
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`spot_id`) REFERENCES `fishing_spots`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Données de test : 1 utilisateur + 3 spots populaires
-- ------------------------------------------------------------
INSERT INTO `users` (`username`, `email`, `password`) VALUES
('admin', 'admin@webpeche.fr', '$2y$12$examplehashedpasswordhere123456');
-- Mot de passe test : "password123"  (à changer !)

INSERT INTO `fishing_spots` (`user_id`, `name`, `description`, `latitude`, `longitude`, `region`, `species`, `image`, `rating`) VALUES
(1, 'Rivière des Truites',   'Magnifique rivière de montagne idéale pour la pêche à la mouche.', 43.7102, 7.2620,  'Alpes-Maritimes', 'truite fario, ombre chevalier', 'riviere-truites.jpg',  4.8),
(1, 'Lac de Monteynard – Isère', 'Lac turquoise aux eaux claires, spot incontournable.',            44.9764, 5.6225,  'Isère',           'truite fario, saumon de fontaine', 'lac-monteynard.jpg', 4.8),
(1, 'Lac du Salagou – Hérault', 'Lac aux rives rouges, poissons nombreux et variés.',              43.6522, 3.3790,  'Hérault',         'truite fario, ombre commun, saumon de fontaine', 'lac-salagou.jpg', 4.8);

SET FOREIGN_KEY_CHECKS = 1;