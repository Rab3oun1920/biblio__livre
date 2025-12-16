-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mar. 16 déc. 2025 à 13:33
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
-- Base de données : `biblio_livres`
--

-- --------------------------------------------------------

--
-- Structure de la table `auteur`
--

CREATE TABLE `auteur` (
                          `id` int(11) NOT NULL,
                          `nom` varchar(255) NOT NULL,
                          `prenom` varchar(255) NOT NULL,
                          `nationalite` varchar(255) DEFAULT NULL,
                          `date_naissance` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `auteur`
--

INSERT INTO `auteur` (`id`, `nom`, `prenom`, `nationalite`, `date_naissance`) VALUES
                                                                                  (1, 'saif', 'rbai', 'tunid', '2000-11-02'),
                                                                                  (2, 'Hugo', 'Victor', 'Française', '1802-02-26'),
                                                                                  (3, 'de Saint-Exupéry', 'Antoine', 'Française', '1900-06-29'),
                                                                                  (4, 'Camus', 'Albert', 'Française', '1913-11-07'),
                                                                                  (5, 'Dumas', 'Alexandre', 'Française', '1802-07-24'),
                                                                                  (6, 'Verne', 'Jules', 'Française', '1828-02-08'),
                                                                                  (7, 'Zola', 'Émile', 'Française', '1840-04-02'),
                                                                                  (8, 'Flaubert', 'Gustave', 'Française', '1821-12-12'),
                                                                                  (9, 'dit Molière', 'Jean-Baptiste Poquelin', 'Française', '1622-01-15');

-- --------------------------------------------------------

--
-- Structure de la table `commande`
--

CREATE TABLE `commande` (
                            `id` int(11) NOT NULL,
                            `date_commande` datetime NOT NULL,
                            `statut` varchar(50) NOT NULL,
                            `montant_total` decimal(10,2) NOT NULL,
                            `user_id` int(11) NOT NULL,
                            `admin_note` varchar(255) DEFAULT NULL,
                            `validated_at` datetime DEFAULT NULL,
                            `rejected_at` datetime DEFAULT NULL,
                            `validated_by_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `commande`
--

INSERT INTO `commande` (`id`, `date_commande`, `statut`, `montant_total`, `user_id`, `admin_note`, `validated_at`, `rejected_at`, `validated_by_id`) VALUES
                                                                                                                                                         (1, '2025-12-04 03:16:37', 'rejetee', 0.00, 2, 'Commande rejetée sans raison spécifiée', NULL, '2025-12-14 04:37:26', NULL),
                                                                                                                                                         (2, '2025-12-04 10:56:20', 'rejetee', 0.00, 2, 'prix 0', NULL, '2025-12-14 04:37:38', NULL),
                                                                                                                                                         (3, '2025-12-04 12:34:36', 'rejetee', 0.00, 2, 'Commande rejetée sans raison spécifiée', NULL, '2025-12-14 04:37:14', NULL),
                                                                                                                                                         (4, '2025-12-11 09:37:15', 'rejetee', 0.00, 3, 'Commande rejetée sans raison spécifiée', NULL, '2025-12-14 04:37:21', NULL),
                                                                                                                                                         (5, '2025-12-14 04:10:38', 'validee', 250.00, 2, NULL, '2025-12-14 04:37:05', NULL, 1),
                                                                                                                                                         (6, '2025-12-15 14:07:45', 'en_attente', 250.00, 2, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `commentaire`
--

CREATE TABLE `commentaire` (
                               `id` int(11) NOT NULL,
                               `contenu` longtext NOT NULL,
                               `note` int(11) DEFAULT NULL,
                               `date_creation` datetime NOT NULL,
                               `est_valide` tinyint(4) NOT NULL,
                               `livre_id` int(11) NOT NULL,
                               `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
                                               `version` varchar(191) NOT NULL,
                                               `executed_at` datetime DEFAULT NULL,
                                               `execution_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
    ('DoctrineMigrations\\Version20251202234708', '2025-12-03 00:47:24', 112);

-- --------------------------------------------------------

--
-- Structure de la table `ligne_commande`
--

CREATE TABLE `ligne_commande` (
                                  `id` int(11) NOT NULL,
                                  `quantite` int(11) NOT NULL,
                                  `prix_unitaire` decimal(10,2) NOT NULL,
                                  `commande_id` int(11) NOT NULL,
                                  `livre_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `ligne_commande`
--

INSERT INTO `ligne_commande` (`id`, `quantite`, `prix_unitaire`, `commande_id`, `livre_id`) VALUES
                                                                                                (1, 3, 0.00, 1, 2),
                                                                                                (2, 2, 0.00, 2, 2),
                                                                                                (3, 3, 0.00, 3, 2),
                                                                                                (4, 16, 0.00, 4, 2),
                                                                                                (5, 1, 250.00, 5, 2),
                                                                                                (6, 1, 250.00, 6, 2);

-- --------------------------------------------------------

--
-- Structure de la table `livre`
--

CREATE TABLE `livre` (
                         `id` int(11) NOT NULL,
                         `titre` varchar(255) NOT NULL,
                         `isbn` varchar(255) DEFAULT NULL,
                         `description` longtext DEFAULT NULL,
                         `annee_publication` int(11) DEFAULT NULL,
                         `genre` varchar(100) DEFAULT NULL,
                         `nombre_pages` int(11) DEFAULT NULL,
                         `auteur_id` int(11) NOT NULL,
                         `image_couverture` varchar(255) DEFAULT NULL,
                         `prix` decimal(10,2) NOT NULL,
                         `stock` int(11) NOT NULL,
                         `est_disponible` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `livre`
--

INSERT INTO `livre` (`id`, `titre`, `isbn`, `description`, `annee_publication`, `genre`, `nombre_pages`, `auteur_id`, `image_couverture`, `prix`, `stock`, `est_disponible`) VALUES
                                                                                                                                                                                 (2, 'L’Homme qui a Redéfini le Football', '9782253096487', 'Ce livre retrace le parcours exceptionnel de Lionel Messi, une légende vivante du football mondial, dont le talent naturel et la détermination ont redéfini l’histoire du sport. De son enfance modeste à Rosario jusqu’aux sommets du FC Barcelone et au sacre ultime avec l’Argentine lors de la Coupe du Monde, l’ouvrage explore les sacrifices, les échecs et les triomphes qui ont forgé le GOAT. À travers des moments clés, des records inégalés et une mentalité de champion, ce livre révèle comment Messi est devenu bien plus qu’un joueur : un symbole d’humilité, de persévérance et de génie footballistique.', 2024, 'football', 50, 1, 'images10-6930d16b08768.jpg', 250.00, 9, 1),
                                                                                                                                                                                 (3, 'Les Misérables', '9782253096344', 'Chef-d\'œuvre de Victor Hugo, ce roman raconte l\'histoire de Jean Valjean, ancien forçat condamné pour le vol d\'un pain, qui cherche la rédemption dans la France du XIXe siècle. À travers son parcours et celui de personnages inoubliables comme Fantine, Cosette et Javert, Hugo dépeint une fresque sociale magistrale sur la misère, la justice et l\'amour.', 1862, 'Roman', 1488, 2, 'cover_1.jpg', 15.99, 25, 1),
                                                                                                                                                                                 (4, 'Notre-Dame de Paris', '9782253002840', 'Dans le Paris médiéval du XVe siècle, Quasimodo, le sonneur de cloches difforme de Notre-Dame, tombe amoureux de la belle gitane Esmeralda. Ce roman explore les thèmes de la beauté, de la laideur et de l\'amour impossible, tout en offrant une description fascinante de la cathédrale et de la société de l\'époque.', 1831, 'Roman', 640, 2, 'cover_2.jpeg', 12.99, 20, 1),
                                                                                                                                                                                 (5, 'Le Petit Prince', '9782070612758', 'Un conte poétique et philosophique qui raconte la rencontre entre un aviateur échoué dans le désert et un jeune prince venu d\'une autre planète. À travers les aventures du petit prince, Saint-Exupéry explore des thèmes universels : l\'amitié, l\'amour, la perte de l\'innocence. \"On ne voit bien qu\'avec le cœur. L\'essentiel est invisible pour les yeux.\"', 1943, 'Conte philosophique', 96, 3, 'cover_3.jpg', 9.99, 50, 1),
                                                                                                                                                                                 (6, 'L\'Étranger', '9782070360024', 'Meursault, un homme ordinaire vivant à Alger, commet un meurtre absurde sur une plage. Ce roman explore l\'absurdité de la condition humaine à travers un personnage indifférent aux conventions sociales. Œuvre fondatrice de la philosophie de l\'absurde, L\'Étranger interroge le sens de l\'existence et la place de l\'individu dans la société.', 1942, 'Roman', 186, 4, 'cover_4.jpg', 8.99, 30, 1),
                                                                                                                                                                                 (7, 'La Peste', '9782070360420', 'Une épidémie de peste ravage la ville d\'Oran en Algérie, plongeant la population dans l\'isolement et la terreur. À travers les actions du docteur Rieux et d\'autres personnages, Camus explore la solidarité humaine face au malheur. Allégorie de la condition humaine et de la résistance face à l\'oppression.', 1947, 'Roman', 352, 4, 'cover_5.jpeg', 11.99, 22, 1),
                                                                                                                                                                                 (8, 'Les Trois Mousquetaires', '9782253098058', 'Le jeune gascon d\'Artagnan arrive à Paris avec l\'ambition de devenir mousquetaire du roi. Il se lie d\'amitié avec Athos, Porthos et Aramis, trois mousquetaires au service de Louis XIII. Ensemble, ils affrontent le cardinal de Richelieu et la mystérieuse Milady dans des aventures palpitantes. \"Un pour tous, tous pour un !\"', 1844, 'Roman d\'aventure', 704, 5, 'cover_6.jpeg', 14.99, 18, 1),
(9, 'Le Comte de Monte-Cristo', '9782253098065', 'Edmond Dantès, jeune marin promis à un avenir brillant, est victime d\'une machination qui le conduit au château d\'If. Après quatorze ans d\'emprisonnement, il s\'évade et découvre un trésor qui lui permettra de se venger de ceux qui l\'ont trahi. Un chef-d\'œuvre sur la vengeance, la justice et la rédemption.', 1844, 'Roman d\'aventure', 1536, 5, 'cover_7.jpg\r\n', 16.99, 15, 1),
(10, 'Vingt mille lieues sous les mers', '9782253006329', 'Le professeur Aronnax, son domestique Conseil et le harponneur Ned Land sont faits prisonniers à bord du Nautilus, un mystérieux sous-marin commandé par le capitaine Nemo. Ils entreprennent un extraordinaire voyage sous les océans, découvrant des merveilles et des dangers insoupçonnés. Un classique de la littérature d\'aventure scientifique.', 1870, 'Science-fiction', 416, 6, 'cover_8.jpeg', 10.99, 28, 1),
                                                                                                                                                                                  (11, 'Le Tour du monde en 80 jours', '9782253006336', 'Le flegmatique gentleman anglais Phileas Fogg parie 20 000 livres qu\'il peut faire le tour du monde en 80 jours. Accompagné de son fidèle serviteur Passepartout, il se lance dans une course contre la montre à travers tous les continents, affrontant obstacles et imprévus. Une aventure trépidante et pleine d\'humour.', 1873, 'Roman d\'aventure', 288, 6, 'cover_9.jpeg', 9.99, 32, 1),
(12, 'Madame Bovary', '9782253004233', 'Emma Bovary, femme d\'un médecin de campagne, rêve d\'une vie romanesque inspirée de ses lectures. Insatisfaite de son existence bourgeoise, elle se lance dans des liaisons extraconjugales qui la mèneront à la ruine. Chef-d\'œuvre du réalisme français, ce roman analyse avec acuité les illusions romantiques et la médiocrité provinciale.', 1857, 'Roman', 512, 8, 'cover_11.jpeg', 11.99, 24, 1),
                                                                                                                                                                                  (13, 'Le Tartuffe', '9782253004240', 'Orgon, riche bourgeois parisien, tombe sous l\'influence de Tartuffe, un faux dévot qui se prétend homme de bien. Aveuglé par l\'hypocrite, Orgon est prêt à lui léguer sa fortune et à lui donner sa fille en mariage. Comédie satirique mordante sur l\'hypocrisie religieuse et la crédulité humaine.', 1664, 'Théâtre', 128, 9, 'cover_12.jpg', 7.99, 35, 1);

-- --------------------------------------------------------

--
-- Structure de la table `messenger_messages`
--

CREATE TABLE `messenger_messages` (
  `id` bigint(20) NOT NULL,
  `body` longtext NOT NULL,
  `headers` longtext NOT NULL,
  `queue_name` varchar(190) NOT NULL,
  `created_at` datetime NOT NULL,
  `available_at` datetime NOT NULL,
  `delivered_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `reclamation`
--

CREATE TABLE `reclamation` (
  `id` int(11) NOT NULL,
  `sujet` varchar(255) NOT NULL,
  `message` longtext NOT NULL,
  `statut` varchar(50) NOT NULL,
  `date_creation` datetime NOT NULL,
  `reponse_admin` longtext DEFAULT NULL,
  `date_reponse` datetime DEFAULT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `reclamation`
--

INSERT INTO `reclamation` (`id`, `sujet`, `message`, `statut`, `date_creation`, `reponse_admin`, `date_reponse`, `user_id`) VALUES
(1, '.....', '................', 'resolue', '2025-12-04 03:07:19', '                            \r\n                        sss', '2025-12-04 03:53:53', 2),
(2, 'ss', 'adoodjiqo,', 'resolue', '2025-12-11 09:38:11', '                         joidjqo   \r\n                        ', '2025-12-11 09:39:07', 3);

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `email` varchar(180) NOT NULL,
  `roles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`roles`)),
  `password` varchar(255) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `prenom` varchar(255) NOT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `can_comment` tinyint(4) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `email`, `roles`, `password`, `nom`, `prenom`, `telephone`, `adresse`, `can_comment`) VALUES
(1, 'admin@gmail.com', '[\"ROLE_ADMIN\"]', '$2y$13$D74.E65m1gpqimO59lRx1ej.k.pq4AqOeX2BoSeGXfy3X69v9mupu', 'Admin', 'Super', NULL, NULL, 1),
(2, 'saifrbai1920@gmail.com', '[\"ROLE_USER\"]', '$2y$13$oakr8nyN7Sk2./CA0Hpo2ux5gCnjB1bs5gOFBt87HricTJZgLcZf2', 'saif', 'rbai', '22546874', 'tunis', 1),
                                                                                                                                                                                   (3, 'sarra@gmail.com', '[\"ROLE_USER\"]', '$2y$13$ez.WZqbT6mne.Jr71bq95Ovfo2ZPoUlGE5/vLReT1w3wxg5JabH0y', 'sarra', 'hamza', '22546874', 'aa', 1);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `auteur`
--
ALTER TABLE `auteur`
    ADD PRIMARY KEY (`id`);

--
-- Index pour la table `commande`
--
ALTER TABLE `commande`
    ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_6EEAA67DA76ED395` (`user_id`),
  ADD KEY `IDX_6EEAA67DC69DE5E5` (`validated_by_id`);

--
-- Index pour la table `commentaire`
--
ALTER TABLE `commentaire`
    ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_67F068BC37D925CB` (`livre_id`),
  ADD KEY `IDX_67F068BCA76ED395` (`user_id`);

--
-- Index pour la table `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
    ADD PRIMARY KEY (`version`);

--
-- Index pour la table `ligne_commande`
--
ALTER TABLE `ligne_commande`
    ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_3170B74B82EA2E54` (`commande_id`),
  ADD KEY `IDX_3170B74B37D925CB` (`livre_id`);

--
-- Index pour la table `livre`
--
ALTER TABLE `livre`
    ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_AC634F9960BB6FE6` (`auteur_id`);

--
-- Index pour la table `messenger_messages`
--
ALTER TABLE `messenger_messages`
    ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_75EA56E0FB7336F0` (`queue_name`),
  ADD KEY `IDX_75EA56E0E3BD61CE` (`available_at`),
  ADD KEY `IDX_75EA56E016BA31DB` (`delivered_at`);

--
-- Index pour la table `reclamation`
--
ALTER TABLE `reclamation`
    ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_CE606404A76ED395` (`user_id`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
    ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `auteur`
--
ALTER TABLE `auteur`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `commande`
--
ALTER TABLE `commande`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `commentaire`
--
ALTER TABLE `commentaire`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `ligne_commande`
--
ALTER TABLE `ligne_commande`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `livre`
--
ALTER TABLE `livre`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT pour la table `messenger_messages`
--
ALTER TABLE `messenger_messages`
    MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `reclamation`
--
ALTER TABLE `reclamation`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `commande`
--
ALTER TABLE `commande`
    ADD CONSTRAINT `FK_6EEAA67DA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_6EEAA67DC69DE5E5` FOREIGN KEY (`validated_by_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `commentaire`
--
ALTER TABLE `commentaire`
    ADD CONSTRAINT `FK_67F068BC37D925CB` FOREIGN KEY (`livre_id`) REFERENCES `livre` (`id`),
  ADD CONSTRAINT `FK_67F068BCA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `ligne_commande`
--
ALTER TABLE `ligne_commande`
    ADD CONSTRAINT `FK_3170B74B37D925CB` FOREIGN KEY (`livre_id`) REFERENCES `livre` (`id`),
  ADD CONSTRAINT `FK_3170B74B82EA2E54` FOREIGN KEY (`commande_id`) REFERENCES `commande` (`id`);

--
-- Contraintes pour la table `livre`
--
ALTER TABLE `livre`
    ADD CONSTRAINT `FK_AC634F9960BB6FE6` FOREIGN KEY (`auteur_id`) REFERENCES `auteur` (`id`);

--
-- Contraintes pour la table `reclamation`
--
ALTER TABLE `reclamation`
    ADD CONSTRAINT `FK_CE606404A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
