-- Tables pour le module d'état d'avancement
-- À exécuter dans phpMyAdmin ou via la ligne de commande MySQL

-- Table principale des états d'avancement
CREATE TABLE IF NOT EXISTS `etat_avancement` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_module` int(11) NOT NULL,
  `id_formateur` int(11) NOT NULL,
  `date_seance` date NOT NULL,
  `duree_realisee` float NOT NULL,
  `commentaire` text DEFAULT NULL,
  `difficultes` text DEFAULT NULL,
  `solutions` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `id_module` (`id_module`),
  KEY `id_formateur` (`id_formateur`),
  CONSTRAINT `etat_avancement_ibfk_1` FOREIGN KEY (`id_module`) REFERENCES `module` (`id`) ON DELETE CASCADE,
  CONSTRAINT `etat_avancement_ibfk_2` FOREIGN KEY (`id_formateur`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table de relation entre états d'avancement et objectifs pédagogiques
CREATE TABLE IF NOT EXISTS `etat_avancement_objectif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_etat_avancement` int(11) NOT NULL,
  `id_objectif_pedagogique` int(11) NOT NULL,
  `statut` enum('atteint','en_cours','non_atteint') NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_etat_avancement_id_objectif_pedagogique` (`id_etat_avancement`,`id_objectif_pedagogique`),
  KEY `id_objectif_pedagogique` (`id_objectif_pedagogique`),
  CONSTRAINT `etat_avancement_objectif_ibfk_1` FOREIGN KEY (`id_etat_avancement`) REFERENCES `etat_avancement` (`id`) ON DELETE CASCADE,
  CONSTRAINT `etat_avancement_objectif_ibfk_2` FOREIGN KEY (`id_objectif_pedagogique`) REFERENCES `objectif_pedagogique` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table de relation entre états d'avancement et contenus de séance
CREATE TABLE IF NOT EXISTS `etat_avancement_contenu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_etat_avancement` int(11) NOT NULL,
  `id_contenu_seance` int(11) NOT NULL,
  `statut` enum('realise','partiel','non_realise') NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_etat_avancement_id_contenu_seance` (`id_etat_avancement`,`id_contenu_seance`),
  KEY `id_contenu_seance` (`id_contenu_seance`),
  CONSTRAINT `etat_avancement_contenu_ibfk_1` FOREIGN KEY (`id_etat_avancement`) REFERENCES `etat_avancement` (`id`) ON DELETE CASCADE,
  CONSTRAINT `etat_avancement_contenu_ibfk_2` FOREIGN KEY (`id_contenu_seance`) REFERENCES `contenu_seance` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table de relation entre états d'avancement et moyens didactiques
CREATE TABLE IF NOT EXISTS `etat_avancement_moyen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_etat_avancement` int(11) NOT NULL,
  `id_moyen_didactique` int(11) NOT NULL,
  `statut` enum('utilise','non_utilise') NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_etat_avancement_id_moyen_didactique` (`id_etat_avancement`,`id_moyen_didactique`),
  KEY `id_moyen_didactique` (`id_moyen_didactique`),
  CONSTRAINT `etat_avancement_moyen_ibfk_1` FOREIGN KEY (`id_etat_avancement`) REFERENCES `etat_avancement` (`id`) ON DELETE CASCADE,
  CONSTRAINT `etat_avancement_moyen_ibfk_2` FOREIGN KEY (`id_moyen_didactique`) REFERENCES `moyen_didactique` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table de relation entre états d'avancement et stratégies d'évaluation
CREATE TABLE IF NOT EXISTS `etat_avancement_strategie` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_etat_avancement` int(11) NOT NULL,
  `id_strategie_evaluation` int(11) NOT NULL,
  `statut` enum('appliquee','non_appliquee') NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_etat_avancement_id_strategie_evaluation` (`id_etat_avancement`,`id_strategie_evaluation`),
  KEY `id_strategie_evaluation` (`id_strategie_evaluation`),
  CONSTRAINT `etat_avancement_strategie_ibfk_1` FOREIGN KEY (`id_etat_avancement`) REFERENCES `etat_avancement` (`id`) ON DELETE CASCADE,
  CONSTRAINT `etat_avancement_strategie_ibfk_2` FOREIGN KEY (`id_strategie_evaluation`) REFERENCES `strategie_evaluation` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
