-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : mer. 06 mai 2026 à 09:55
-- Version du serveur : 8.4.3
-- Version de PHP : 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- Base de données : `solem_exire`

-- --------------------------------------------------------
-- Table `avis`
-- --------------------------------------------------------
CREATE TABLE `avis` (
  `id` int NOT NULL,
  `id_client` int NOT NULL,
  `etoiles_client` int NOT NULL,
  `text_avis` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------
-- Table `compte`
-- --------------------------------------------------------
CREATE TABLE `compte` (
  `id` int NOT NULL,
  `email` varchar(50) NOT NULL,
  `Utilisateur` varchar(20) NOT NULL,
  `mot_de_passe` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------
-- Table `historique_commande`
-- --------------------------------------------------------
CREATE TABLE `historique_commande` (
  `id` int NOT NULL,
  `id_compte` int NOT NULL,
  `id_produit` int NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------
-- Table `panier`
-- --------------------------------------------------------
CREATE TABLE `panier` (
  `id` int NOT NULL,
  `id_compte` int NOT NULL,
  `nom_produit` varchar(50) NOT NULL,
  `quantite` int NOT NULL,
  `prix` decimal(65,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------
-- Table `produits`
-- --------------------------------------------------------
CREATE TABLE `produits` (
  `id` int NOT NULL,
  `nom` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `ingredients` text NOT NULL,
  `photo` varchar(50) DEFAULT NULL,
  `prix` decimal(10,2) NOT NULL,
  `stock` int NOT NULL,
  `etoiles` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------
-- Table `sav`
-- --------------------------------------------------------
CREATE TABLE `sav` (
  `id` int NOT NULL,
  `id_client` int NOT NULL,
  `commentaire` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------
-- Index
-- --------------------------------------------------------
ALTER TABLE `avis`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_client` (`id_client`);

ALTER TABLE `compte`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `historique_commande`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_produit` (`id_produit`),
  ADD KEY `id_compte` (`id_compte`);

ALTER TABLE `panier`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nom_produit` (`nom_produit`),
  ADD KEY `id_compte` (`id_compte`);

ALTER TABLE `produits`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `Unique` (`nom`);

ALTER TABLE `sav`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_client` (`id_client`);

-- --------------------------------------------------------
-- AUTO_INCREMENT
-- --------------------------------------------------------
ALTER TABLE `avis`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `compte`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `panier`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `produits`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `sav`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

-- --------------------------------------------------------
-- Contraintes (clés étrangères)
-- --------------------------------------------------------
ALTER TABLE `avis`
  ADD CONSTRAINT `avis_ibfk_1` FOREIGN KEY (`id_client`) REFERENCES `compte` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `historique_commande`
  ADD CONSTRAINT `historique_commande_ibfk_1` FOREIGN KEY (`id_compte`) REFERENCES `compte` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `panier`
  ADD CONSTRAINT `panier_ibfk_1` FOREIGN KEY (`nom_produit`) REFERENCES `produits` (`nom`),
  ADD CONSTRAINT `panier_ibfk_2` FOREIGN KEY (`id_compte`) REFERENCES `compte` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `sav`
  ADD CONSTRAINT `sav_ibfk_1` FOREIGN KEY (`id_client`) REFERENCES `compte` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
