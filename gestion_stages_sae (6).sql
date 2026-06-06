-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : sam. 06 juin 2026 à 11:39
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
-- Base de données : `gestion_stages_sae`
--

-- --------------------------------------------------------

--
-- Structure de la table `avoir`
--

CREATE TABLE `avoir` (
  `id_offre` int(11) NOT NULL,
  `id_stage` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `droits`
--

CREATE TABLE `droits` (
  `id_droit` int(11) NOT NULL,
  `libelle_droit` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `droits`
--

INSERT INTO `droits` (`id_droit`, `libelle_droit`) VALUES
(1, 'modification_profil'),
(2, 'valider_demande_stage'),
(3, 'saisir_offre'),
(4, 'affectation'),
(5, 'saisir_infos_recherche'),
(6, 'saisir_infos_soutenance'),
(7, 'écrire_problème'),
(8, 'saisir_note'),
(9, 'consulter_note'),
(10, 'avoir_vision_globale'),
(11, 'droit_postuler');

-- --------------------------------------------------------

--
-- Structure de la table `effectuer`
--

CREATE TABLE `effectuer` (
  `num_etudiant` int(11) NOT NULL,
  `id_recherche` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `effectuer`
--

INSERT INTO `effectuer` (`num_etudiant`, `id_recherche`) VALUES
(0, 27),
(0, 28),
(0, 29),
(2, 23),
(25001519, 16),
(25001519, 17),
(25001519, 18),
(25001519, 19),
(25001519, 20),
(25001519, 21),
(25001519, 22),
(25001519, 24),
(25001519, 25),
(25001519, 26),
(25001519, 30);

-- --------------------------------------------------------

--
-- Structure de la table `enseignant`
--

CREATE TABLE `enseignant` (
  `id_ens` int(11) NOT NULL,
  `identifiant` varchar(50) NOT NULL,
  `pwd` varchar(255) NOT NULL,
  `nom` varchar(50) DEFAULT NULL,
  `prenom` varchar(50) DEFAULT NULL,
  `fonctions` enum('Responsable stage','Chef de département','Jury de soutenance','Enseignant standard','Administrateur') NOT NULL DEFAULT 'Enseignant standard',
  `statut_compte` varchar(20) DEFAULT 'En en attente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `enseignant`
--

INSERT INTO `enseignant` (`id_ens`, `identifiant`, `pwd`, `nom`, `prenom`, `fonctions`, `statut_compte`) VALUES
(19, 'julien.carpentier@univ-eiffel.fr', '$2y$10$7aTIZzIXuzPq4ILXcX7EE.EDywpi6134nA28MgK6uI2Z1Fn3bUxs.', 'carpentier', 'julien', 'Enseignant standard', 'Validé'),
(20, 'claire.renaud@univ-eiffel.fr', '$2y$10$6GxJUSgsBoYpTzvGnWKPHuhJo3VKA1bGa7d3k0FHz57V.M9Lcts8a', 'renaud', 'claire', 'Jury de soutenance', 'Validé'),
(22, 'sophie.delorme@univ-eiffel.fr', '$2y$10$mX2CISq4Eb2quh4H4Cm9f.rMS5ONlIrZeTYaNkDnQv5hqmR65gG0C', 'delorme', 'sophie', 'Responsable stage', 'Validé'),
(23, 'thomas.vasseur@univ-eiffel.fr', '$2y$10$NClB9k6HBer2NIuG7pZlpuHOdz9y4c8ikX0OB2twFt4WxSaPMNmK6', 'vasseur', 'thomas', 'Chef de département', 'Validé'),
(30, 'nicolas.gautier@univ-eiffel.fr', '$2y$10$rCZdHQ3nVULJnD0es3Fkxun9yyuHPXFv5TymLePaZ5JzsP6NJPVga', 'GAUTIER', 'Nicolas', 'Administrateur', 'Validé'),
(34, 'coline.saure@univ-eiffel.fr', '$2y$10$2RiGedTEi2PxGD7fOtKL8ODL5XC8VqXWBrpNliprPPQ.TiYJjizMq', 'saure', 'coline', 'Jury de soutenance', 'Validé');

-- --------------------------------------------------------

--
-- Structure de la table `entreprise`
--

CREATE TABLE `entreprise` (
  `id_ent` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `adresse` text DEFAULT NULL,
  `contact` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `entreprise`
--

INSERT INTO `entreprise` (`id_ent`, `nom`, `adresse`, `contact`) VALUES
(1, 'Apple', 'Paris', 'Apple'),
(2, 'BNP Paribas', 'Renseignée par l\'étudiant', 'BNP Paribas'),
(3, 'Sister Design', 'Senlis', 'Sister Design');

-- --------------------------------------------------------

--
-- Structure de la table `etudiant`
--

CREATE TABLE `etudiant` (
  `num_etudiant` int(11) NOT NULL,
  `identifiant` varchar(50) NOT NULL,
  `pwd` varchar(255) NOT NULL,
  `nom` varchar(50) DEFAULT NULL,
  `prenom` varchar(50) DEFAULT NULL,
  `tel` varchar(20) DEFAULT NULL,
  `date_naiss` date DEFAULT NULL,
  `lieu_naiss` varchar(100) DEFAULT NULL,
  `adresse` text DEFAULT NULL,
  `promotion` varchar(50) DEFAULT NULL,
  `groupe_TD` varchar(10) DEFAULT NULL,
  `groupe_TP` varchar(10) DEFAULT NULL,
  `competences` text DEFAULT NULL,
  `preferences` text DEFAULT NULL,
  `statut_compte` varchar(20) DEFAULT 'En attente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `etudiant`
--

INSERT INTO `etudiant` (`num_etudiant`, `identifiant`, `pwd`, `nom`, `prenom`, `tel`, `date_naiss`, `lieu_naiss`, `adresse`, `promotion`, `groupe_TD`, `groupe_TP`, `competences`, `preferences`, `statut_compte`) VALUES
(0, 'test6@edu.univ-eiffel.fr', '$2y$10$GAbotU4fvikGFTaF1.94TuYqvXlSYMHGN8RkO9UYnkhBVAK8xiyRu', 'TEST6', 'test6', '0000000000', '2005-05-05', 'test6', 'test6', 'MMI1', 'TD1', 'TPA', NULL, NULL, 'Validé'),
(2, 'blandine.cuvillier@edu.univ-eiffel.fr', '$2y$10$TyuJPrw1kC7vx.AR2SLbD.ZeMr/VPhuTg31C32iab4zzeumYd6RqO', 'CUVILLIER', 'Blandine', '0123456789', '2007-07-13', 'Senlis', 'Avilly', 'MMI1', 'TD2', 'TPC', NULL, NULL, 'Validé'),
(25001519, 'jeanne.dubois@edu.univ-eiffel.fr', '$2y$10$7Fti1i9.xZAZ6cxKNr.oXOnP0u.AwCrl1ZaVjO42Lb0LpSRQRYZRi', 'DUBOIS', 'Jeanne', '0123456789', '2007-11-29', 'Créteil', '9 Avenue du Général Leclerc, Créteil', 'MMI1', 'TD1', 'TPA', 'Figma', 'Dates: mai/juillet\r\nLieu: Paris\r\nEntreprise: banque', 'Validé');

-- --------------------------------------------------------

--
-- Structure de la table `jury`
--

CREATE TABLE `jury` (
  `id_jury` int(11) NOT NULL,
  `enseignant_1` varchar(100) DEFAULT NULL,
  `enseignant_2` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `jury`
--

INSERT INTO `jury` (`id_jury`, `enseignant_1`, `enseignant_2`) VALUES
(1, 'claire.renaud@univ-eiffel.fr', 'coline.saure@univ-eiffel.fr'),
(2, 'claire.renaud@univ-eiffel.fr', 'coline.saure@univ-eiffel.fr'),
(3, 'claire.renaud@univ-eiffel.fr', 'coline.saure@univ-eiffel.fr');

-- --------------------------------------------------------

--
-- Structure de la table `maitre_stage`
--

CREATE TABLE `maitre_stage` (
  `id_maitre` int(11) NOT NULL,
  `identifiant` varchar(50) NOT NULL,
  `pwd` varchar(255) NOT NULL,
  `nom` varchar(50) DEFAULT NULL,
  `prenom` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `tel` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `maitre_stage`
--

INSERT INTO `maitre_stage` (`id_maitre`, `identifiant`, `pwd`, `nom`, `prenom`, `email`, `tel`) VALUES
(1, 'test@test', '$2y$10$csfwwj.5EdU5HuDxm0Qy7.GZIKVM4WYwofdJx6tZQHUDxiQLUz.rW', 'test', 'test', 'test@test', NULL),
(2, 'paul.robart@bnp.com', '$2y$10$Atpud43sFXPqn4NNPgiMkeDq1lizMsX/D3KLToMp4MIApyaeNkDUq', 'Robart', 'Paul', 'paul.robart@bnp.com', '');

-- --------------------------------------------------------

--
-- Structure de la table `offre`
--

CREATE TABLE `offre` (
  `id_offre` int(11) NOT NULL,
  `intitule` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `contact` varchar(100) DEFAULT NULL,
  `remuneration` decimal(10,2) DEFAULT NULL,
  `dates` varchar(100) DEFAULT NULL,
  `lieu` varchar(100) DEFAULT NULL,
  `competences` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `offre`
--

INSERT INTO `offre` (`id_offre`, `intitule`, `description`, `contact`, `remuneration`, `dates`, `lieu`, `competences`) VALUES
(4, 'Designer Web', 'test', 'Dobble', 0.00, '12 mai au 14 juiller ', 'Paris', 'figma'),
(5, 'Développeur Full-Stack', 'Aider à la correction de bugs', 'Apple', 0.00, 'Du 6 mai au 6 juillet ', 'Paris', 'HTML, CSS, JS, PHP'),
(7, 'Graphiste', 'Proposer de nouveaux visuels', 'Sister Design', 600.00, '12 mai au 12 juillet', 'Senlis', 'Suite Adobe');

-- --------------------------------------------------------

--
-- Structure de la table `posseder_enseignant`
--

CREATE TABLE `posseder_enseignant` (
  `id_ens` int(11) NOT NULL,
  `id_droit` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `posseder_etudiant`
--

CREATE TABLE `posseder_etudiant` (
  `num_etudiant` int(11) NOT NULL,
  `id_droit` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `recherche`
--

CREATE TABLE `recherche` (
  `id_recherche` int(11) NOT NULL,
  `reponses` text DEFAULT NULL,
  `statut` enum('Validée','En attente','Refusé') NOT NULL DEFAULT 'En attente',
  `date_recherche` date DEFAULT NULL,
  `entreprise_contactee` varchar(100) DEFAULT NULL,
  `offre_consultee` varchar(100) DEFAULT NULL,
  `candidature_envoyee` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `recherche`
--

INSERT INTO `recherche` (`id_recherche`, `reponses`, `statut`, `date_recherche`, `entreprise_contactee`, `offre_consultee`, `candidature_envoyee`) VALUES
(2, '0', 'Validée', '2026-05-24', '', 'Développeur Full-Stack', 0),
(3, '0', 'Refusé', '2026-05-24', '', '', 0),
(4, '0', 'Validée', '2026-05-24', '', 'Développeur Full-Stack', 0),
(5, '0', 'Refusé', '2026-05-24', '', '', 0),
(6, '0', 'Refusé', '2026-05-24', '', '', 0),
(7, '0', 'Refusé', '2026-05-24', '', 'Développeur Full-Stack', 0),
(8, '0', 'Validée', '2026-05-24', 'Haribp', 'Designer', 0),
(9, '0', 'Validée', '2026-05-24', 'Haribp', 'Designer', 0),
(10, '0', 'Refusé', '2026-05-24', '', '', 0),
(11, '0', 'Refusé', '2026-05-24', '', '', 0),
(12, '0', 'Refusé', '2026-05-24', '', '', 0),
(13, '0', 'En attente', '2026-05-24', 'ouoi', 'zd', 0),
(14, '0', 'En attente', '2026-05-24', 'ouoi', 'zd', 0),
(15, 'DATES : 13/07 au 13/08\nMISSIONS : Test de compétences\n--- INFOS MAÎTRE DE STAGE ---\nNOM : Dupont\nPRÉNOM : Louis\nEMAIL : dupont@louis', 'Validée', '2026-05-25', 'Apple', 'Développement', 0),
(16, '0', 'Validée', '2026-06-04', 'Dobble', 'Designer Web', 0),
(17, '0', 'Refusé', '2026-06-05', 'Dobble', 'Designer Web', 0),
(18, '0', 'Refusé', '2026-06-05', 'Dobble', 'Designer Web', 0),
(19, '0', 'Refusé', '2026-06-05', 'Dobble', 'Designer Web', 0),
(20, '0', 'Refusé', '2026-06-05', 'Dobble', 'Designer Web', 0),
(21, 'NOM : DUPONT\nPRÉNOM : Jean\nEMAIL : jean.dupont@dooble.com', 'Validée', '2026-06-05', 'Dobble', 'Designer Web', 0),
(22, 'DATES : 15/05 au 15/07\nMISSIONS : Proposer une nouvelle gestion des données\n--- INFOS MAÎTRE DE STAGE ---\nNOM : Robart\nPRÉNOM : Paul\nEMAIL : paul.robart@bnp.com', 'Validée', '2026-06-06', 'BNP Paribas', 'Développeur Back-end', 0),
(23, 'NOM : test\nPRÉNOM : test\nEMAIL : test@test', 'Validée', '2026-06-06', 'Dobble', 'Designer Web', 0),
(24, '0', 'Refusé', '2026-06-06', 'Dobble', 'Designer Web', 0),
(25, '0', 'Refusé', '2026-06-06', 'Dobble', 'Designer Web', 0),
(26, '0', 'Refusé', '2026-06-06', 'Dobble', 'Designer Web', 0),
(27, '0', 'Refusé', '2026-06-06', 'Apple', 'Développeur Full-Stack', 0),
(28, '0', 'Refusé', '2026-06-06', 'Dobble', 'Designer Web', 0),
(29, '0', 'Validée', '2026-06-06', 'Dobble', 'Designer Web', 0),
(30, 'DATES : 15/05 au 15/07\nMISSIONS : Développement du site\n--- INFOS MAÎTRE DE STAGE ---\nNOM : Robart\nPRÉNOM : Paul\nEMAIL : paul.robart@bnp.com', 'Validée', '2026-06-06', 'BNP Paribas', 'Développeur Back-end', 0);

-- --------------------------------------------------------

--
-- Structure de la table `soutenance`
--

CREATE TABLE `soutenance` (
  `id_soutenance` int(11) NOT NULL,
  `date_soutenance` date DEFAULT NULL,
  `heure_debut` time DEFAULT NULL,
  `heure_fin` time DEFAULT NULL,
  `etudiant` varchar(100) DEFAULT NULL,
  `salle` varchar(50) DEFAULT NULL,
  `note_soutenance` decimal(4,2) DEFAULT NULL,
  `note_rapport` decimal(4,2) DEFAULT NULL,
  `id_jury` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `soutenance`
--

INSERT INTO `soutenance` (`id_soutenance`, `date_soutenance`, `heure_debut`, `heure_fin`, `etudiant`, `salle`, `note_soutenance`, `note_rapport`, `id_jury`) VALUES
(3, '2026-06-02', '13:45:00', '14:15:00', 'jeanne.dubois@edu.univ-eiffel.fr', '316', 0.00, 5.00, 1),
(4, '2026-06-05', '10:00:00', '10:30:00', 'blandine.cuvillier@edu.univ-eiffel.fr', '317', 1.50, 0.75, 2),
(5, '2026-05-05', '10:00:00', '10:30:00', 'test6@edu.univ-eiffel.fr', '317', 0.75, 0.75, 3);

-- --------------------------------------------------------

--
-- Structure de la table `stage`
--

CREATE TABLE `stage` (
  `id_stage` int(11) NOT NULL,
  `lieu` varchar(100) DEFAULT NULL,
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `probleme` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `convention_signee` enum('oui','non') DEFAULT 'non',
  `id_maitre` int(11) DEFAULT NULL,
  `id_soutenance` int(11) DEFAULT NULL,
  `id_ent` int(11) DEFAULT NULL,
  `num_etudiant` int(11) DEFAULT NULL,
  `id_offre` int(11) DEFAULT NULL,
  `alerte_etudiant` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `stage`
--

INSERT INTO `stage` (`id_stage`, `lieu`, `date_debut`, `date_fin`, `probleme`, `description`, `convention_signee`, `id_maitre`, `id_soutenance`, `id_ent`, `num_etudiant`, `id_offre`, `alerte_etudiant`) VALUES
(5, 'Dobble', NULL, NULL, NULL, '{\"telephone_etudiant\":\"0000000000\",\"date_naissance\":\"2007-07-13\",\"lieu_naissance\":\"Senlis 00\",\"adresse_etudiant\":\"0 rue 00 avilly\",\"siret\":\"00000000000000\",\"representant_legal\":\"test représentant légal\",\"heures_totales\":\"100\",\"modalite_presence\":\"Sur site\",\"service_affectation\":\"Design\",\"horaires_travail\":\"35h\\/semaine\",\"objectifs_pedagogiques\":\"Design interface\",\"montant_gratification\":\"0\",\"modalite_versement\":\"\"}', 'oui', 1, NULL, NULL, 2, NULL, NULL),
(6, 'Dobble', NULL, NULL, NULL, '{\"telephone_etudiant\":\"0000000000\",\"date_naissance\":\"2007-07-13\",\"lieu_naissance\":\"Senlis 00\",\"adresse_etudiant\":\"1 rue de ferrières, 94000 Créteil\",\"siret\":\"00000000000000\",\"representant_legal\":\"test représentant légal\",\"heures_totales\":\"100\",\"modalite_presence\":\"Sur site\",\"service_affectation\":\"Design\",\"horaires_travail\":\"35h\\/semaine\",\"objectifs_pedagogiques\":\"Maquettage du site\",\"montant_gratification\":\"0\",\"modalite_versement\":\"\"}', 'oui', NULL, NULL, NULL, 0, NULL, NULL),
(7, 'BNP Paribas', NULL, NULL, NULL, NULL, 'non', NULL, NULL, NULL, 25001519, NULL, NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `avoir`
--
ALTER TABLE `avoir`
  ADD PRIMARY KEY (`id_offre`,`id_stage`),
  ADD KEY `id_stage` (`id_stage`);

--
-- Index pour la table `droits`
--
ALTER TABLE `droits`
  ADD PRIMARY KEY (`id_droit`);

--
-- Index pour la table `effectuer`
--
ALTER TABLE `effectuer`
  ADD PRIMARY KEY (`num_etudiant`,`id_recherche`),
  ADD KEY `id_recherche` (`id_recherche`);

--
-- Index pour la table `enseignant`
--
ALTER TABLE `enseignant`
  ADD PRIMARY KEY (`id_ens`),
  ADD UNIQUE KEY `identifiant` (`identifiant`);

--
-- Index pour la table `entreprise`
--
ALTER TABLE `entreprise`
  ADD PRIMARY KEY (`id_ent`);

--
-- Index pour la table `etudiant`
--
ALTER TABLE `etudiant`
  ADD PRIMARY KEY (`num_etudiant`),
  ADD UNIQUE KEY `identifiant` (`identifiant`);

--
-- Index pour la table `jury`
--
ALTER TABLE `jury`
  ADD PRIMARY KEY (`id_jury`);

--
-- Index pour la table `maitre_stage`
--
ALTER TABLE `maitre_stage`
  ADD PRIMARY KEY (`id_maitre`),
  ADD UNIQUE KEY `identifiant` (`identifiant`);

--
-- Index pour la table `offre`
--
ALTER TABLE `offre`
  ADD PRIMARY KEY (`id_offre`);

--
-- Index pour la table `posseder_enseignant`
--
ALTER TABLE `posseder_enseignant`
  ADD PRIMARY KEY (`id_ens`,`id_droit`),
  ADD KEY `id_droit` (`id_droit`);

--
-- Index pour la table `posseder_etudiant`
--
ALTER TABLE `posseder_etudiant`
  ADD PRIMARY KEY (`num_etudiant`,`id_droit`),
  ADD KEY `id_droit` (`id_droit`);

--
-- Index pour la table `recherche`
--
ALTER TABLE `recherche`
  ADD PRIMARY KEY (`id_recherche`);

--
-- Index pour la table `soutenance`
--
ALTER TABLE `soutenance`
  ADD PRIMARY KEY (`id_soutenance`),
  ADD KEY `id_jury` (`id_jury`);

--
-- Index pour la table `stage`
--
ALTER TABLE `stage`
  ADD PRIMARY KEY (`id_stage`),
  ADD UNIQUE KEY `id_offre` (`id_offre`),
  ADD KEY `id_maitre` (`id_maitre`),
  ADD KEY `id_soutenance` (`id_soutenance`),
  ADD KEY `id_ent` (`id_ent`),
  ADD KEY `num_etudiant` (`num_etudiant`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `droits`
--
ALTER TABLE `droits`
  MODIFY `id_droit` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT pour la table `enseignant`
--
ALTER TABLE `enseignant`
  MODIFY `id_ens` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT pour la table `entreprise`
--
ALTER TABLE `entreprise`
  MODIFY `id_ent` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `jury`
--
ALTER TABLE `jury`
  MODIFY `id_jury` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `maitre_stage`
--
ALTER TABLE `maitre_stage`
  MODIFY `id_maitre` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `offre`
--
ALTER TABLE `offre`
  MODIFY `id_offre` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `recherche`
--
ALTER TABLE `recherche`
  MODIFY `id_recherche` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT pour la table `soutenance`
--
ALTER TABLE `soutenance`
  MODIFY `id_soutenance` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `stage`
--
ALTER TABLE `stage`
  MODIFY `id_stage` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `avoir`
--
ALTER TABLE `avoir`
  ADD CONSTRAINT `avoir_ibfk_1` FOREIGN KEY (`id_offre`) REFERENCES `offre` (`id_offre`) ON DELETE CASCADE,
  ADD CONSTRAINT `avoir_ibfk_2` FOREIGN KEY (`id_stage`) REFERENCES `stage` (`id_stage`) ON DELETE CASCADE;

--
-- Contraintes pour la table `effectuer`
--
ALTER TABLE `effectuer`
  ADD CONSTRAINT `effectuer_ibfk_1` FOREIGN KEY (`num_etudiant`) REFERENCES `etudiant` (`num_etudiant`) ON DELETE CASCADE,
  ADD CONSTRAINT `effectuer_ibfk_2` FOREIGN KEY (`id_recherche`) REFERENCES `recherche` (`id_recherche`) ON DELETE CASCADE;

--
-- Contraintes pour la table `posseder_enseignant`
--
ALTER TABLE `posseder_enseignant`
  ADD CONSTRAINT `posseder_enseignant_ibfk_1` FOREIGN KEY (`id_ens`) REFERENCES `enseignant` (`id_ens`) ON DELETE CASCADE,
  ADD CONSTRAINT `posseder_enseignant_ibfk_2` FOREIGN KEY (`id_droit`) REFERENCES `droits` (`id_droit`) ON DELETE CASCADE;

--
-- Contraintes pour la table `posseder_etudiant`
--
ALTER TABLE `posseder_etudiant`
  ADD CONSTRAINT `posseder_etudiant_ibfk_1` FOREIGN KEY (`num_etudiant`) REFERENCES `etudiant` (`num_etudiant`) ON DELETE CASCADE,
  ADD CONSTRAINT `posseder_etudiant_ibfk_2` FOREIGN KEY (`id_droit`) REFERENCES `droits` (`id_droit`) ON DELETE CASCADE;

--
-- Contraintes pour la table `soutenance`
--
ALTER TABLE `soutenance`
  ADD CONSTRAINT `soutenance_ibfk_1` FOREIGN KEY (`id_jury`) REFERENCES `jury` (`id_jury`) ON DELETE SET NULL;

--
-- Contraintes pour la table `stage`
--
ALTER TABLE `stage`
  ADD CONSTRAINT `stage_ibfk_1` FOREIGN KEY (`id_maitre`) REFERENCES `maitre_stage` (`id_maitre`),
  ADD CONSTRAINT `stage_ibfk_2` FOREIGN KEY (`id_soutenance`) REFERENCES `soutenance` (`id_soutenance`),
  ADD CONSTRAINT `stage_ibfk_3` FOREIGN KEY (`id_ent`) REFERENCES `entreprise` (`id_ent`),
  ADD CONSTRAINT `stage_ibfk_4` FOREIGN KEY (`num_etudiant`) REFERENCES `etudiant` (`num_etudiant`),
  ADD CONSTRAINT `stage_ibfk_5` FOREIGN KEY (`id_offre`) REFERENCES `offre` (`id_offre`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
