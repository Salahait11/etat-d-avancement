-- MySQL dump 10.13  Distrib 8.0.41, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: gestion_ecoles
-- ------------------------------------------------------
-- Server version	8.0.41

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Structure de la table `utilisateur`
--

DROP TABLE IF EXISTS `utilisateur`;
CREATE TABLE `utilisateur` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  `prenom` varchar(255) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `statut` enum('actif','inactif') NOT NULL DEFAULT 'actif',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Structure de la table `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nom` (`nom`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Structure de la table `utilisateur_roles`
--

DROP TABLE IF EXISTS `utilisateur_roles`;
CREATE TABLE `utilisateur_roles` (
  `id_utilisateur` int NOT NULL,
  `id_roles` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_utilisateur`,`id_roles`),
  KEY `id_roles` (`id_roles`),
  CONSTRAINT `utilisateur_roles_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE,
  CONSTRAINT `utilisateur_roles_ibfk_2` FOREIGN KEY (`id_roles`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Structure de la table `filiere`
--

DROP TABLE IF EXISTS `filiere`;
CREATE TABLE `filiere` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom_filiere` varchar(100) NOT NULL,
  `description` text,
  `niveau` varchar(50) NOT NULL,
  `duree_totale` int NOT NULL COMMENT 'Durée totale en heures',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nom_filiere` (`nom_filiere`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Structure de la table `module`
--

DROP TABLE IF EXISTS `module`;
CREATE TABLE `module` (
  `id` int NOT NULL AUTO_INCREMENT,
  `intitule` varchar(255) NOT NULL,
  `objectif` text NOT NULL,
  `duree` int NOT NULL,
  `id_filiere` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_filiere` (`id_filiere`),
  CONSTRAINT `fk_filiere` FOREIGN KEY (`id_filiere`) REFERENCES `filiere` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Structure de la table `formateur`
--

DROP TABLE IF EXISTS `formateur`;
CREATE TABLE `formateur` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_utilisateur` int NOT NULL,
  `specialite` varchar(255),
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_utilisateur` (`id_utilisateur`),
  CONSTRAINT `formateur_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Structure de la table `objectif_pedagogique`
--

DROP TABLE IF EXISTS `objectif_pedagogique`;
CREATE TABLE `objectif_pedagogique` (
  `id` int NOT NULL AUTO_INCREMENT,
  `objectif` varchar(255) NOT NULL,
  `description` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Structure de la table `contenu_seance`
--

DROP TABLE IF EXISTS `contenu_seance`;
CREATE TABLE `contenu_seance` (
  `id` int NOT NULL AUTO_INCREMENT,
  `contenu` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Structure de la table `moyen_didactique`
--

DROP TABLE IF EXISTS `moyen_didactique`;
CREATE TABLE `moyen_didactique` (
  `id` int NOT NULL AUTO_INCREMENT,
  `moyen` varchar(255) NOT NULL,
  `description` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Structure de la table `strategie_evaluation`
--

DROP TABLE IF EXISTS `strategie_evaluation`;
CREATE TABLE `strategie_evaluation` (
  `id` int NOT NULL AUTO_INCREMENT,
  `strategie` varchar(255) NOT NULL,
  `description` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Structure de la table `etat_avancement`
--

DROP TABLE IF EXISTS `etat_avancement`;
CREATE TABLE `etat_avancement` (
  `id` int NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `heure` datetime NOT NULL,
  `description` text NOT NULL,
  `nbr_heure_cumulee` int NOT NULL,
  `nbr_heure` int NOT NULL,
  `disposition` int NOT NULL,
  `observation` text,
  `taux_realisation` decimal(5,2) NOT NULL CHECK (taux_realisation >= 0 AND taux_realisation <= 100),
  `id_formateur` int NOT NULL,
  `id_module` int NOT NULL,
  `id_objectif_pedagogique` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_formateur` (`id_formateur`),
  KEY `fk_module` (`id_module`),
  KEY `fk_objectif_pedagogique` (`id_objectif_pedagogique`),
  CONSTRAINT `fk_formateur` FOREIGN KEY (`id_formateur`) REFERENCES `formateur` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_module` FOREIGN KEY (`id_module`) REFERENCES `module` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_objectif_pedagogique` FOREIGN KEY (`id_objectif_pedagogique`) REFERENCES `objectif_pedagogique` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Structure de la table `etat_evaluation`
--

DROP TABLE IF EXISTS `etat_evaluation`;
CREATE TABLE `etat_evaluation` (
  `id_etat_avancement` int NOT NULL,
  `id_strategie_evaluation` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_etat_avancement`,`id_strategie_evaluation`),
  KEY `id_strategie_evaluation` (`id_strategie_evaluation`),
  CONSTRAINT `etat_evaluation_ibfk_1` FOREIGN KEY (`id_etat_avancement`) REFERENCES `etat_avancement` (`id`) ON DELETE CASCADE,
  CONSTRAINT `etat_evaluation_ibfk_2` FOREIGN KEY (`id_strategie_evaluation`) REFERENCES `strategie_evaluation` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Structure de la table `etat_moyen_didactique`
--

DROP TABLE IF EXISTS `etat_moyen_didactique`;
CREATE TABLE `etat_moyen_didactique` (
  `id_etat_avancement` int NOT NULL,
  `id_moyen_didactique` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_etat_avancement`,`id_moyen_didactique`),
  KEY `id_moyen_didactique` (`id_moyen_didactique`),
  CONSTRAINT `etat_moyen_didactique_ibfk_1` FOREIGN KEY (`id_etat_avancement`) REFERENCES `etat_avancement` (`id`) ON DELETE CASCADE,
  CONSTRAINT `etat_moyen_didactique_ibfk_2` FOREIGN KEY (`id_moyen_didactique`) REFERENCES `moyen_didactique` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Structure de la table `etat_contenu_seance`
--

DROP TABLE IF EXISTS `etat_contenu_seance`;
CREATE TABLE `etat_contenu_seance` (
  `id_etat_avancement` int NOT NULL,
  `id_contenu_seance` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_etat_avancement`,`id_contenu_seance`),
  KEY `id_contenu_seance` (`id_contenu_seance`),
  CONSTRAINT `etat_contenu_seance_ibfk_1` FOREIGN KEY (`id_etat_avancement`) REFERENCES `etat_avancement` (`id`) ON DELETE CASCADE,
  CONSTRAINT `etat_contenu_seance_ibfk_2` FOREIGN KEY (`id_contenu_seance`) REFERENCES `contenu_seance` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
COMMIT;