-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : ven. 26 juin 2026 à 12:01
-- Version du serveur : 9.1.0
-- Version de PHP : 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `projectwebpeche`
--

-- --------------------------------------------------------

--
-- Structure de la table `community_comments`
--

DROP TABLE IF EXISTS `community_comments`;
CREATE TABLE IF NOT EXISTS `community_comments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `post_id` int NOT NULL,
  `user_id` int NOT NULL,
  `content` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `community_posts`
--

DROP TABLE IF EXISTS `community_posts`;
CREATE TABLE IF NOT EXISTS `community_posts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `title` varchar(150) NOT NULL,
  `content` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `fishing_spots`
--

DROP TABLE IF EXISTS `fishing_spots`;
CREATE TABLE IF NOT EXISTS `fishing_spots` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `region` varchar(255) DEFAULT NULL,
  `species` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `rating` decimal(3,1) DEFAULT '0.0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `fishing_spots`
--

INSERT INTO `fishing_spots` (`id`, `user_id`, `name`, `description`, `latitude`, `longitude`, `region`, `species`, `image`, `rating`, `created_at`) VALUES
(1, 1, 'Rivière des Truites', 'Magnifique rivière de montagne idéale pour la pêche à la mouche.', 43.71020000, 7.26200000, 'Alpes-Maritimes', 'truite fario, ombre chevalier', 'riviere-truites.jpg', 4.8, '2026-06-25 18:34:01'),
(2, 1, 'Lac de Monteynard – Isère', 'Lac turquoise aux eaux claires, spot incontournable.', 44.97640000, 5.62250000, 'Isère', 'truite fario, saumon de fontaine', 'lac-monteynard.jpg', 4.8, '2026-06-25 18:34:01'),
(3, 1, 'Lac du Salagou – Hérault', 'Lac aux rives rouges, poissons nombreux et variés.', 43.65220000, 3.37900000, 'Hérault', 'truite fario, ombre commun, saumon de fontaine', 'lac-salagou.jpg', 4.8, '2026-06-25 18:34:01');

-- --------------------------------------------------------

--
-- Structure de la table `remember_tokens`
--

DROP TABLE IF EXISTS `remember_tokens`;
CREATE TABLE IF NOT EXISTS `remember_tokens` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `token_hash` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `spot_reviews`
--

DROP TABLE IF EXISTS `spot_reviews`;
CREATE TABLE IF NOT EXISTS `spot_reviews` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `spot_id` int NOT NULL,
  `score` tinyint(1) NOT NULL,
  `comment` text,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_review` (`user_id`,`spot_id`),
  KEY `spot_id` (`spot_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  `avatar` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `is_admin`, `avatar`, `created_at`) VALUES
(1, 'admin', 'admin@webpeche.fr', '$2b$12$Sqr8QZs2RJ4SwGREoIS6GOZki8wFj.I1idZEdnl37PVU5sg9xf6Dy', 1, NULL, '2026-06-25 18:34:01'),
(2, 'tests', 'tests@test.com', '$2y$12$AdBGFiiWygLQXtND9HZcj.f7B/UdC4L1G7RcpHQzZ6OoxRQV6mT.S', 0, NULL, '2026-06-25 18:35:24'),
(3, 'test', 'test@test.com', '$2y$12$sonCi9J6qn/GxuVF/15gM.FzCSWRWx9J81q/29h4Bkp4LZqozlaNG', 0, NULL, '2026-06-26 13:49:50');

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `community_comments`
--
ALTER TABLE `community_comments`
  ADD CONSTRAINT `community_comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `community_posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `community_comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `community_posts`
--
ALTER TABLE `community_posts`
  ADD CONSTRAINT `community_posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `fishing_spots`
--
ALTER TABLE `fishing_spots`
  ADD CONSTRAINT `fishing_spots_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `remember_tokens`
--
ALTER TABLE `remember_tokens`
  ADD CONSTRAINT `remember_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `spot_reviews`
--
ALTER TABLE `spot_reviews`
  ADD CONSTRAINT `spot_reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `spot_reviews_ibfk_2` FOREIGN KEY (`spot_id`) REFERENCES `fishing_spots` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
