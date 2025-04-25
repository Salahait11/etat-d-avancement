-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : jeu. 24 avr. 2025 à 22:38
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `gestion_ecoles`
--

-- --------------------------------------------------------

--
-- Structure de la table `contenu_seance`
--

CREATE TABLE `contenu_seance` (
  `id` int(11) NOT NULL,
  `contenu` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `etat_avancement`
--

CREATE TABLE `etat_avancement` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `heure` datetime NOT NULL,
  `description` text NOT NULL,
  `nbr_heure_cumulee` int(11) NOT NULL,
  `nbr_heure` int(11) NOT NULL,
  `disposition` int(11) NOT NULL,
  `observation` text DEFAULT NULL,
  `taux_realisation` decimal(5,2) NOT NULL CHECK (`taux_realisation` >= 0 and `taux_realisation` <= 100),
  `id_formateur` int(11) NOT NULL,
  `id_module` int(11) NOT NULL,
  `id_objectif_pedagogique` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `etat_avancement_contenu`
--

CREATE TABLE `etat_avancement_contenu` (
  `id` int(11) NOT NULL,
  `id_etat_avancement` int(11) NOT NULL,
  `id_contenu_seance` int(11) NOT NULL,
  `statut` enum('realise','partiel','non_realise') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `etat_avancement_moyen`
--

CREATE TABLE `etat_avancement_moyen` (
  `id` int(11) NOT NULL,
  `id_etat_avancement` int(11) NOT NULL,
  `id_moyen_didactique` int(11) NOT NULL,
  `statut` enum('utilise','non_utilise') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `etat_avancement_objectif`
--

CREATE TABLE `etat_avancement_objectif` (
  `id` int(11) NOT NULL,
  `id_etat_avancement` int(11) NOT NULL,
  `id_objectif_pedagogique` int(11) NOT NULL,
  `statut` enum('atteint','en_cours','non_atteint') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `etat_avancement_strategie`
--

CREATE TABLE `etat_avancement_strategie` (
  `id` int(11) NOT NULL,
  `id_etat_avancement` int(11) NOT NULL,
  `id_strategie_evaluation` int(11) NOT NULL,
  `statut` enum('appliquee','non_appliquee') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `etat_contenu_seance`
--

CREATE TABLE `etat_contenu_seance` (
  `id_etat_avancement` int(11) NOT NULL,
  `id_contenu_seance` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `etat_evaluation`
--

CREATE TABLE `etat_evaluation` (
  `id_etat_avancement` int(11) NOT NULL,
  `id_strategie_evaluation` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `etat_moyen_didactique`
--

CREATE TABLE `etat_moyen_didactique` (
  `id_etat_avancement` int(11) NOT NULL,
  `id_moyen_didactique` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `filiere`
--

CREATE TABLE `filiere` (
  `id` int(11) NOT NULL,
  `nom_filiere` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `niveau` varchar(50) NOT NULL,
  `duree_totale` int(11) NOT NULL COMMENT 'Durée totale en heures',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `filiere`
--

INSERT INTO `filiere` (`id`, `nom_filiere`, `description`, `niveau`, `duree_totale`, `created_at`, `updated_at`) VALUES
(2, 'informatique', 's', 'Intermédiaire', 7, '2025-04-24 19:04:20', '2025-04-24 19:04:20');

-- --------------------------------------------------------

--
-- Structure de la table `formateur`
--

CREATE TABLE `formateur` (
  `id` int(11) NOT NULL,
  `id_utilisateur` int(11) NOT NULL,
  `specialite` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `formateur`
--

INSERT INTO `formateur` (`id`, `id_utilisateur`, `specialite`, `created_at`, `updated_at`) VALUES
(1, 1, 'Informatique', '2025-04-24 17:45:40', '2025-04-24 17:45:40');

-- --------------------------------------------------------

--
-- Structure de la table `module`
--

CREATE TABLE `module` (
  `id` int(11) NOT NULL,
  `intitule` varchar(255) NOT NULL,
  `objectif` text NOT NULL,
  `duree` int(11) NOT NULL,
  `id_filiere` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `module`
--

INSERT INTO `module` (`id`, `intitule`, `objectif`, `duree`, `id_filiere`, `created_at`, `updated_at`) VALUES
(2, 'ssss', 'ss', 22, 2, '2025-04-24 19:21:34', '2025-04-24 19:21:34');

-- --------------------------------------------------------

--
-- Structure de la table `moyen_didactique`
--

CREATE TABLE `moyen_didactique` (
  `id` int(11) NOT NULL,
  `moyen` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `objectif_pedagogique`
--

CREATE TABLE `objectif_pedagogique` (
  `id` int(11) NOT NULL,
  `objectif` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `roles`
--

INSERT INTO `roles` (`id`, `nom`, `description`, `created_at`, `updated_at`) VALUES
(5, 'admin', 'Administrateur du système', '2025-04-24 19:31:49', '2025-04-24 19:31:49'),
(6, 'formateur', 'Personnel enseignant', '2025-04-24 19:31:49', '2025-04-24 19:31:49'),
(7, 'etudiant', 'Apprenant inscrit', '2025-04-24 19:31:49', '2025-04-24 19:31:49');

-- --------------------------------------------------------

--
-- Structure de la table `strategie_evaluation`
--

CREATE TABLE `strategie_evaluation` (
  `id` int(11) NOT NULL,
  `strategie` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `prenom` varchar(255) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `statut` enum('actif','inactif') NOT NULL DEFAULT 'actif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`id`, `nom`, `prenom`, `mot_de_passe`, `email`, `statut`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'Test', '$2y$10$gRw/LlfTmY/0lgGLJUxezOPK15WB/wZ8ycUsR/h8rm9KTnG.ELQ6S', 'admin@test.com', 'actif', '2025-04-24 17:45:40', '2025-04-24 17:51:01');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur_roles`
--

CREATE TABLE `utilisateur_roles` (
  `id_utilisateur` int(11) NOT NULL,
  `id_roles` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `utilisateur_roles`
--

INSERT INTO `utilisateur_roles` (`id_utilisateur`, `id_roles`, `created_at`) VALUES
(1, 5, '2025-04-24 19:33:49');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `contenu_seance`
--
ALTER TABLE `contenu_seance`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `etat_avancement`
--
ALTER TABLE `etat_avancement`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_formateur` (`id_formateur`),
  ADD KEY `fk_module` (`id_module`),
  ADD KEY `fk_objectif_pedagogique` (`id_objectif_pedagogique`);

--
-- Index pour la table `etat_avancement_contenu`
--
ALTER TABLE `etat_avancement_contenu`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_etat_avancement_id_contenu_seance` (`id_etat_avancement`,`id_contenu_seance`),
  ADD KEY `id_contenu_seance` (`id_contenu_seance`);

--
-- Index pour la table `etat_avancement_moyen`
--
ALTER TABLE `etat_avancement_moyen`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_etat_avancement_id_moyen_didactique` (`id_etat_avancement`,`id_moyen_didactique`),
  ADD KEY `id_moyen_didactique` (`id_moyen_didactique`);

--
-- Index pour la table `etat_avancement_objectif`
--
ALTER TABLE `etat_avancement_objectif`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_etat_avancement_id_objectif_pedagogique` (`id_etat_avancement`,`id_objectif_pedagogique`),
  ADD KEY `id_objectif_pedagogique` (`id_objectif_pedagogique`);

--
-- Index pour la table `etat_avancement_strategie`
--
ALTER TABLE `etat_avancement_strategie`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_etat_avancement_id_strategie_evaluation` (`id_etat_avancement`,`id_strategie_evaluation`),
  ADD KEY `id_strategie_evaluation` (`id_strategie_evaluation`);

--
-- Index pour la table `etat_contenu_seance`
--
ALTER TABLE `etat_contenu_seance`
  ADD PRIMARY KEY (`id_etat_avancement`,`id_contenu_seance`),
  ADD KEY `id_contenu_seance` (`id_contenu_seance`);

--
-- Index pour la table `etat_evaluation`
--
ALTER TABLE `etat_evaluation`
  ADD PRIMARY KEY (`id_etat_avancement`,`id_strategie_evaluation`),
  ADD KEY `id_strategie_evaluation` (`id_strategie_evaluation`);

--
-- Index pour la table `etat_moyen_didactique`
--
ALTER TABLE `etat_moyen_didactique`
  ADD PRIMARY KEY (`id_etat_avancement`,`id_moyen_didactique`),
  ADD KEY `id_moyen_didactique` (`id_moyen_didactique`);

--
-- Index pour la table `filiere`
--
ALTER TABLE `filiere`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nom_filiere` (`nom_filiere`);

--
-- Index pour la table `formateur`
--
ALTER TABLE `formateur`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_utilisateur` (`id_utilisateur`);

--
-- Index pour la table `module`
--
ALTER TABLE `module`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_filiere` (`id_filiere`);

--
-- Index pour la table `moyen_didactique`
--
ALTER TABLE `moyen_didactique`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `objectif_pedagogique`
--
ALTER TABLE `objectif_pedagogique`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nom` (`nom`);

--
-- Index pour la table `strategie_evaluation`
--
ALTER TABLE `strategie_evaluation`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Index pour la table `utilisateur_roles`
--
ALTER TABLE `utilisateur_roles`
  ADD PRIMARY KEY (`id_utilisateur`,`id_roles`),
  ADD KEY `id_roles` (`id_roles`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `contenu_seance`
--
ALTER TABLE `contenu_seance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `etat_avancement`
--
ALTER TABLE `etat_avancement`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `etat_avancement_contenu`
--
ALTER TABLE `etat_avancement_contenu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `etat_avancement_moyen`
--
ALTER TABLE `etat_avancement_moyen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `etat_avancement_objectif`
--
ALTER TABLE `etat_avancement_objectif`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `etat_avancement_strategie`
--
ALTER TABLE `etat_avancement_strategie`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `filiere`
--
ALTER TABLE `filiere`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `formateur`
--
ALTER TABLE `formateur`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `module`
--
ALTER TABLE `module`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `moyen_didactique`
--
ALTER TABLE `moyen_didactique`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `objectif_pedagogique`
--
ALTER TABLE `objectif_pedagogique`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `strategie_evaluation`
--
ALTER TABLE `strategie_evaluation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `etat_avancement`
--
ALTER TABLE `etat_avancement`
  ADD CONSTRAINT `fk_formateur` FOREIGN KEY (`id_formateur`) REFERENCES `formateur` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_module` FOREIGN KEY (`id_module`) REFERENCES `module` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_objectif_pedagogique` FOREIGN KEY (`id_objectif_pedagogique`) REFERENCES `objectif_pedagogique` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `etat_avancement_contenu`
--
ALTER TABLE `etat_avancement_contenu`
  ADD CONSTRAINT `etat_avancement_contenu_ibfk_1` FOREIGN KEY (`id_etat_avancement`) REFERENCES `etat_avancement` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `etat_avancement_contenu_ibfk_2` FOREIGN KEY (`id_contenu_seance`) REFERENCES `contenu_seance` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `etat_avancement_moyen`
--
ALTER TABLE `etat_avancement_moyen`
  ADD CONSTRAINT `etat_avancement_moyen_ibfk_1` FOREIGN KEY (`id_etat_avancement`) REFERENCES `etat_avancement` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `etat_avancement_moyen_ibfk_2` FOREIGN KEY (`id_moyen_didactique`) REFERENCES `moyen_didactique` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `etat_avancement_objectif`
--
ALTER TABLE `etat_avancement_objectif`
  ADD CONSTRAINT `etat_avancement_objectif_ibfk_1` FOREIGN KEY (`id_etat_avancement`) REFERENCES `etat_avancement` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `etat_avancement_objectif_ibfk_2` FOREIGN KEY (`id_objectif_pedagogique`) REFERENCES `objectif_pedagogique` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `etat_avancement_strategie`
--
ALTER TABLE `etat_avancement_strategie`
  ADD CONSTRAINT `etat_avancement_strategie_ibfk_1` FOREIGN KEY (`id_etat_avancement`) REFERENCES `etat_avancement` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `etat_avancement_strategie_ibfk_2` FOREIGN KEY (`id_strategie_evaluation`) REFERENCES `strategie_evaluation` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `etat_contenu_seance`
--
ALTER TABLE `etat_contenu_seance`
  ADD CONSTRAINT `etat_contenu_seance_ibfk_1` FOREIGN KEY (`id_etat_avancement`) REFERENCES `etat_avancement` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `etat_contenu_seance_ibfk_2` FOREIGN KEY (`id_contenu_seance`) REFERENCES `contenu_seance` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `etat_evaluation`
--
ALTER TABLE `etat_evaluation`
  ADD CONSTRAINT `etat_evaluation_ibfk_1` FOREIGN KEY (`id_etat_avancement`) REFERENCES `etat_avancement` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `etat_evaluation_ibfk_2` FOREIGN KEY (`id_strategie_evaluation`) REFERENCES `strategie_evaluation` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `etat_moyen_didactique`
--
ALTER TABLE `etat_moyen_didactique`
  ADD CONSTRAINT `etat_moyen_didactique_ibfk_1` FOREIGN KEY (`id_etat_avancement`) REFERENCES `etat_avancement` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `etat_moyen_didactique_ibfk_2` FOREIGN KEY (`id_moyen_didactique`) REFERENCES `moyen_didactique` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `formateur`
--
ALTER TABLE `formateur`
  ADD CONSTRAINT `formateur_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `module`
--
ALTER TABLE `module`
  ADD CONSTRAINT `fk_filiere` FOREIGN KEY (`id_filiere`) REFERENCES `filiere` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `utilisateur_roles`
--
ALTER TABLE `utilisateur_roles`
  ADD CONSTRAINT `utilisateur_roles_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `utilisateur_roles_ibfk_2` FOREIGN KEY (`id_roles`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
