-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: mysql
-- Erstellungszeit: 13. Jan 2025 um 18:44
-- Server-Version: 9.1.0
-- PHP-Version: 8.2.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `main`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `accounts`
--

CREATE TABLE `accounts` (
                            `account_id` int UNSIGNED NOT NULL,
                            `user_id` int UNSIGNED DEFAULT NULL,
                            `admin_note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
                            `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Daten für Tabelle `accounts`
--

INSERT INTO `accounts` (`account_id`, `user_id`, `admin_note`, `created_at`) VALUES
                                                                                 (1, 1, NULL, '2024-12-05 09:16:20'),
                                                                                 (2, 2, NULL, '2024-12-05 09:16:20'),
                                                                                 (3, 3, NULL, '2024-12-05 09:16:20'),
                                                                                 (4, 4, NULL, '2024-12-05 09:16:20');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `account_invitation_tokens`
--

CREATE TABLE `account_invitation_tokens` (
                                             `owner_account_id` int UNSIGNED NOT NULL,
                                             `invited_account_id` int UNSIGNED DEFAULT NULL,
                                             `token_field_1` smallint UNSIGNED NOT NULL,
                                             `token_field_2` smallint UNSIGNED NOT NULL,
                                             `token_field_3` smallint UNSIGNED NOT NULL,
                                             `token_field_4` smallint UNSIGNED NOT NULL,
                                             `token_checksum` smallint UNSIGNED NOT NULL,
                                             `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                             `used_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Daten für Tabelle `account_invitation_tokens`
--

INSERT INTO `account_invitation_tokens` (`owner_account_id`, `invited_account_id`, `token_field_1`, `token_field_2`, `token_field_3`, `token_field_4`, `token_checksum`, `created_at`, `used_at`) VALUES
                                                                                                                                                                                                      (1, NULL, 2356, 1345, 7231, 2389, 3330, '2024-11-26 22:09:04', NULL),
                                                                                                                                                                                                      (2, 4, 2467, 5439, 9434, 6317, 5914, '2024-11-12 17:59:26', '2024-11-11 18:59:41'),
                                                                                                                                                                                                      (1, 2, 2485, 2764, 9211, 4695, 4788, '2024-11-05 08:12:38', '2024-11-07 09:07:22'),
                                                                                                                                                                                                      (1, 3, 4586, 5863, 8326, 9386, 7040, '2024-11-05 08:12:38', '2024-11-07 09:09:52'),
                                                                                                                                                                                                      (1, NULL, 6847, 5780, 7257, 1059, 5235, '2024-11-07 08:09:36', NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `account_punishments`
--

CREATE TABLE `account_punishments` (
                                       `punishment_id` int UNSIGNED NOT NULL,
                                       `punishment_type` enum('PERMABAN','TEMPBAN') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
                                       `punished_account_id` int UNSIGNED NOT NULL,
                                       `punisher_account_id` int UNSIGNED NOT NULL,
                                       `pardoner_account_id` int UNSIGNED DEFAULT NULL,
                                       `punished_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
                                       `pardoned_reason` text,
                                       `disable_account_while_punishment` bit(1) NOT NULL DEFAULT b'1',
                                       `starts_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                       `ends_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Daten für Tabelle `account_punishments`
--

INSERT INTO `account_punishments` (`punishment_id`, `punishment_type`, `punished_account_id`, `punisher_account_id`, `pardoner_account_id`, `punished_reason`, `pardoned_reason`, `disable_account_while_punishment`, `starts_at`, `ends_at`) VALUES
    (1, 'PERMABAN', 3, 1, NULL, 'Just for Existence', NULL, b'1', '2024-11-07 08:13:47', NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `apod`
--

CREATE TABLE `apod` (
                        `apod_id` int UNSIGNED NOT NULL,
                        `explanation` text NOT NULL,
                        `released_at` date NOT NULL,
                        `fetched_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `apod_likes`
--

CREATE TABLE `apod_likes` (
                              `user_id` int UNSIGNED NOT NULL,
                              `apod_id` int UNSIGNED NOT NULL,
                              `liked_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `blogs`
--

CREATE TABLE `blogs` (
                         `blog_id` int NOT NULL,
                         `blog_owner_id` int UNSIGNED NOT NULL,
                         `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                         `edited_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                         `content` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `forms`
--

CREATE TABLE `forms` (
                         `form_id` int UNSIGNED NOT NULL,
                         `owner_user_id` int UNSIGNED NOT NULL,
                         `configuration` json NOT NULL,
                         `closed_manually` bit(1) NOT NULL DEFAULT b'0',
                         `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                         `closes_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `form_submits`
--

CREATE TABLE `form_submits` (
                                `form_submit_id` int UNSIGNED NOT NULL,
                                `form_id` int UNSIGNED NOT NULL,
                                `submit_content` json NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `girl_game_instances`
--

CREATE TABLE `girl_game_instances` (
                                       `girl_game_id` int UNSIGNED NOT NULL,
                                       `account_id` int UNSIGNED NOT NULL,
                                       `girl_game_type` varchar(32) NOT NULL,
                                       `name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `girl_game_types`
--

CREATE TABLE `girl_game_types` (
                                   `girl_game_type` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
                                   `description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Daten für Tabelle `girl_game_types`
--

INSERT INTO `girl_game_types` (`girl_game_type`, `description`) VALUES
                                                                    ('CAPTURE_THE_FLAG', ''),
                                                                    ('HIDE_AND_SEEK', ''),
                                                                    ('TAG', ''),
                                                                    ('ZOMBIE', '');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `linktrees`
--

CREATE TABLE `linktrees` (
                             `linktree_id` int UNSIGNED NOT NULL,
                             `owner_user_id` int UNSIGNED NOT NULL,
                             `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
                             `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                             `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Daten für Tabelle `linktrees`
--

INSERT INTO `linktrees` (`linktree_id`, `owner_user_id`, `description`, `created_at`, `updated_at`) VALUES
                                                                                                        (1, 1, 'The Description from Nebalus', '2024-11-12 19:19:41', '2024-11-12 19:19:41'),
                                                                                                        (3, 2, 'Testers Account', '2024-11-12 19:25:09', '2024-11-12 19:25:09');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `linktree_click_metric`
--

CREATE TABLE `linktree_click_metric` (
                                         `click_id` int UNSIGNED NOT NULL,
                                         `linktree_id` int UNSIGNED NOT NULL,
                                         `clicked_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Daten für Tabelle `linktree_click_metric`
--

INSERT INTO `linktree_click_metric` (`click_id`, `linktree_id`, `clicked_at`) VALUES
                                                                                  (1, 1, '2024-11-12 19:23:40'),
                                                                                  (2, 1, '2024-11-12 19:23:43'),
                                                                                  (3, 1, '2024-11-12 19:23:46'),
                                                                                  (4, 1, '2024-11-12 19:24:15'),
                                                                                  (5, 1, '2024-11-12 19:24:15');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `linktree_entrys`
--

CREATE TABLE `linktree_entrys` (
                                   `linktree_entry_id` int UNSIGNED NOT NULL,
                                   `linktree_id` int UNSIGNED NOT NULL,
                                   `name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
                                   `url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
                                   `description` text,
                                   `display_order` int UNSIGNED NOT NULL,
                                   `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                   `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Daten für Tabelle `linktree_entrys`
--

INSERT INTO `linktree_entrys` (`linktree_entry_id`, `linktree_id`, `name`, `url`, `description`, `display_order`, `created_at`, `updated_at`) VALUES
                                                                                                                                                  (1, 1, 'Youtube', 'https://youtube.com', NULL, 1, '2024-11-12 19:20:35', '2024-11-12 19:20:35'),
                                                                                                                                                  (2, 1, 'Github', 'https://github.com/Nebalus', NULL, 2, '2024-11-12 19:21:38', '2024-11-12 19:21:38'),
                                                                                                                                                  (3, 1, 'Crunchyroll', 'https://www.crunchyroll.com/', NULL, 3, '2024-11-12 19:22:36', '2024-11-12 19:22:36'),
                                                                                                                                                  (4, 3, 'ChatGPT', 'https://chatgpt.com/', NULL, 1, '2024-11-12 19:26:10', '2024-11-12 19:26:10');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `privileges`
--

CREATE TABLE `privileges` (
                              `privilege_node` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
                              `description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `projects`
--

CREATE TABLE `projects` (
                            `project_id` int UNSIGNED NOT NULL,
                            `project_uuid` char(36) NOT NULL,
                            `owner_user_id` int UNSIGNED NOT NULL,
                            `project_name` varchar(32) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Daten für Tabelle `projects`
--

INSERT INTO `projects` (`project_id`, `project_uuid`, `owner_user_id`, `project_name`) VALUES
    (1, '63ae131b-b7b5-11ef-8afe-0242ac170003', 1, 'TEST');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `project_clients`
--

CREATE TABLE `project_clients` (
                                   `project_client_id` int UNSIGNED NOT NULL,
                                   `client_uuid` char(36) NOT NULL,
                                   `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                   `last_time_seen_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Daten für Tabelle `project_clients`
--

INSERT INTO `project_clients` (`project_client_id`, `client_uuid`, `created_at`, `last_time_seen_at`) VALUES
                                                                                                          (1, 'b5fa96ea-6349-4c94-b2f5-1f1a2351216c', '2024-12-16 12:24:50', '2024-12-19 12:24:50'),
                                                                                                          (2, '0860c048-cab4-432b-9766-e55647514140', '2024-12-19 15:47:12', '2024-12-20 15:47:12'),
                                                                                                          (4, 'cc047536-bbc5-4920-bc5e-11292437617b', '2024-12-19 15:56:36', '2024-12-19 15:56:36'),
                                                                                                          (5, '8c5bf729-b02e-4bc2-aed7-cf6ae238ae7f', '2024-12-19 15:56:36', '2024-12-19 15:56:36');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `referrals`
--

CREATE TABLE `referrals` (
                             `referral_id` int UNSIGNED NOT NULL,
                             `owner_user_id` int UNSIGNED NOT NULL,
                             `code` varchar(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
                             `pointer` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
                             `name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
                             `disabled` bit(1) NOT NULL DEFAULT b'0',
                             `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                             `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Daten für Tabelle `referrals`
--

INSERT INTO `referrals` (`referral_id`, `owner_user_id`, `code`, `pointer`, `name`, `disabled`, `created_at`, `updated_at`) VALUES
                                                                                                                                (1, 1, '04bbH3G9', 'https://nebalus.dev', 'TEST Referral', b'0', '2024-02-25 00:00:00', '2024-09-28 15:35:26'),
                                                                                                                                (3, 1, '3S5D6UmE', 'https://google.com', 'Hier zu google lol', b'0', '2024-02-25 00:00:00', '2024-09-28 15:35:26'),
                                                                                                                                (5, 1, '6I6vP9ou', 'https://nebalus.dev', '42!', b'0', '2024-02-27 11:04:20', '2024-09-28 15:35:26'),
                                                                                                                                (6, 1, 'b2bXghSd', 'https://nebalus.dev', '42!1!!1!', b'0', '2024-02-28 21:30:24', '2024-09-28 15:35:26'),
                                                                                                                                (7, 1, 'JXEYHjxk', 'https://nebalus.dev', 'Random shit', b'1', '2024-08-03 23:20:58', '2024-09-28 15:35:26'),
                                                                                                                                (11, 2, 'J09g8VqT', 'https://youtube.com', 'youtube hehe', b'0', '2024-11-07 08:19:50', '2024-11-07 08:19:50'),
                                                                                                                                (12, 1, 'r5HkrOkZ', 'https://status.nebalus.dev', NULL, b'0', '2024-12-06 22:38:13', '2024-12-06 22:38:13');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `referral_click_metric`
--

CREATE TABLE `referral_click_metric` (
                                         `click_id` int UNSIGNED NOT NULL,
                                         `referral_id` int UNSIGNED NOT NULL,
                                         `clicked_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Daten für Tabelle `referral_click_metric`
--

INSERT INTO `referral_click_metric` (`click_id`, `referral_id`, `clicked_at`) VALUES
                                                                                  (1, 6, '2024-11-05 15:41:25'),
                                                                                  (2, 6, '2024-11-05 15:41:30'),
                                                                                  (3, 6, '2024-11-05 15:41:35'),
                                                                                  (4, 6, '2024-11-05 15:50:28'),
                                                                                  (5, 6, '2024-11-05 15:50:28'),
                                                                                  (6, 6, '2024-11-05 15:50:29'),
                                                                                  (7, 6, '2024-11-05 15:50:29'),
                                                                                  (8, 6, '2024-11-05 15:50:30'),
                                                                                  (9, 6, '2024-11-05 23:02:33'),
                                                                                  (10, 6, '2024-11-05 23:02:34'),
                                                                                  (11, 6, '2024-11-05 23:02:34'),
                                                                                  (12, 1, '2024-11-05 23:02:38'),
                                                                                  (13, 1, '2024-11-05 23:02:39'),
                                                                                  (14, 1, '2024-11-05 23:02:42'),
                                                                                  (15, 1, '2024-11-05 23:02:46'),
                                                                                  (16, 5, '2024-11-05 23:02:49'),
                                                                                  (17, 5, '2024-11-05 23:02:50'),
                                                                                  (18, 5, '2024-11-05 23:02:51'),
                                                                                  (19, 6, '2024-11-05 23:02:54'),
                                                                                  (20, 6, '2024-11-11 18:08:28'),
                                                                                  (21, 6, '2024-11-11 18:08:37'),
                                                                                  (22, 6, '2024-11-11 18:08:38'),
                                                                                  (23, 6, '2024-11-11 18:08:38'),
                                                                                  (24, 6, '2024-11-11 18:08:39'),
                                                                                  (25, 6, '2024-11-11 18:08:45'),
                                                                                  (26, 5, '2024-11-11 18:08:52'),
                                                                                  (27, 1, '2024-11-11 18:08:57'),
                                                                                  (28, 1, '2024-11-11 18:08:58'),
                                                                                  (29, 1, '2024-11-11 18:08:59'),
                                                                                  (30, 1, '2024-11-11 18:09:46'),
                                                                                  (31, 1, '2024-11-11 18:09:55'),
                                                                                  (32, 1, '2024-11-11 18:09:56'),
                                                                                  (33, 1, '2024-11-11 18:09:57'),
                                                                                  (34, 1, '2024-11-11 18:09:58'),
                                                                                  (35, 1, '2024-11-11 18:09:59'),
                                                                                  (36, 1, '2024-11-11 18:09:59'),
                                                                                  (37, 3, '2024-11-11 18:26:47'),
                                                                                  (38, 3, '2024-11-11 18:26:49'),
                                                                                  (39, 3, '2024-11-11 18:26:50'),
                                                                                  (40, 3, '2024-11-11 18:50:51'),
                                                                                  (41, 6, '2024-11-11 18:50:55'),
                                                                                  (42, 6, '2024-11-11 18:50:57'),
                                                                                  (43, 6, '2024-11-11 18:50:58'),
                                                                                  (44, 6, '2024-11-12 17:56:25'),
                                                                                  (45, 1, '2024-11-12 17:56:39'),
                                                                                  (46, 1, '2024-11-12 17:56:39'),
                                                                                  (47, 11, '2024-11-12 17:56:45'),
                                                                                  (48, 11, '2024-11-12 17:56:46'),
                                                                                  (49, 11, '2024-11-12 17:56:47'),
                                                                                  (50, 11, '2024-11-12 17:56:47'),
                                                                                  (51, 5, '2024-11-12 17:56:52'),
                                                                                  (52, 5, '2024-11-12 17:56:52'),
                                                                                  (53, 6, '2024-11-12 17:56:58'),
                                                                                  (54, 1, '2024-11-13 17:46:51'),
                                                                                  (55, 1, '2024-11-13 17:46:52'),
                                                                                  (56, 6, '2024-11-13 17:46:56'),
                                                                                  (57, 6, '2024-11-13 17:46:57'),
                                                                                  (58, 1, '2024-11-13 17:47:01'),
                                                                                  (59, 1, '2024-11-13 17:47:02'),
                                                                                  (60, 1, '2024-11-13 17:47:14'),
                                                                                  (61, 1, '2024-11-13 17:47:15'),
                                                                                  (62, 6, '2024-11-13 17:47:20'),
                                                                                  (63, 6, '2024-11-13 17:47:21'),
                                                                                  (64, 6, '2024-11-13 17:47:22'),
                                                                                  (65, 11, '2024-11-13 17:47:27'),
                                                                                  (66, 11, '2024-11-13 17:47:27'),
                                                                                  (67, 11, '2024-11-13 17:47:28'),
                                                                                  (68, 11, '2024-11-13 17:47:28'),
                                                                                  (69, 11, '2024-11-13 17:47:29'),
                                                                                  (70, 1, '2024-11-13 17:47:35'),
                                                                                  (71, 3, '2024-11-26 23:18:39'),
                                                                                  (72, 3, '2024-11-26 23:18:43'),
                                                                                  (73, 3, '2024-11-26 23:18:44'),
                                                                                  (74, 3, '2024-11-26 23:18:46'),
                                                                                  (75, 3, '2024-11-26 23:18:46'),
                                                                                  (76, 3, '2024-11-26 23:18:47'),
                                                                                  (77, 3, '2024-11-26 23:18:47'),
                                                                                  (78, 3, '2024-11-26 23:18:47'),
                                                                                  (79, 3, '2024-11-26 23:18:48'),
                                                                                  (80, 3, '2024-11-26 23:19:04'),
                                                                                  (81, 3, '2024-11-26 23:19:05'),
                                                                                  (82, 3, '2024-11-26 23:19:06'),
                                                                                  (83, 3, '2024-11-26 23:19:07'),
                                                                                  (84, 3, '2024-11-26 23:19:07'),
                                                                                  (85, 3, '2024-11-26 23:19:10'),
                                                                                  (86, 3, '2024-11-26 23:19:10'),
                                                                                  (87, 6, '2024-11-26 23:19:14'),
                                                                                  (88, 6, '2024-11-26 23:19:15'),
                                                                                  (89, 6, '2024-11-26 23:19:15'),
                                                                                  (90, 6, '2024-11-26 23:19:16'),
                                                                                  (91, 6, '2024-11-26 23:19:16'),
                                                                                  (92, 6, '2024-11-26 23:19:16'),
                                                                                  (93, 6, '2024-11-26 23:19:17'),
                                                                                  (94, 1, '2024-11-26 23:19:21'),
                                                                                  (95, 1, '2024-11-26 23:19:21'),
                                                                                  (96, 1, '2024-11-26 23:19:21'),
                                                                                  (97, 5, '2024-11-26 23:19:25'),
                                                                                  (98, 5, '2024-11-26 23:19:25'),
                                                                                  (99, 5, '2024-11-26 23:19:26'),
                                                                                  (100, 5, '2024-11-26 23:19:27'),
                                                                                  (101, 5, '2024-11-26 23:19:27'),
                                                                                  (102, 5, '2024-11-26 23:19:28'),
                                                                                  (103, 5, '2024-11-26 23:19:28'),
                                                                                  (104, 5, '2024-11-26 23:19:28'),
                                                                                  (105, 5, '2024-11-26 23:19:29'),
                                                                                  (106, 5, '2024-11-26 23:19:29'),
                                                                                  (107, 5, '2024-11-26 23:19:29'),
                                                                                  (108, 6, '2024-11-26 23:19:33'),
                                                                                  (109, 6, '2024-11-26 23:19:34'),
                                                                                  (110, 6, '2024-11-26 23:19:34'),
                                                                                  (111, 6, '2024-11-26 23:19:35'),
                                                                                  (112, 6, '2024-11-26 23:19:35'),
                                                                                  (113, 6, '2024-11-26 23:19:36'),
                                                                                  (114, 6, '2024-11-26 23:19:37'),
                                                                                  (115, 6, '2024-11-26 23:19:37'),
                                                                                  (116, 6, '2024-11-26 23:19:39'),
                                                                                  (117, 6, '2024-11-26 23:19:39'),
                                                                                  (118, 6, '2024-11-26 23:19:40'),
                                                                                  (119, 6, '2024-11-26 23:19:40'),
                                                                                  (120, 6, '2024-11-26 23:19:41'),
                                                                                  (121, 6, '2024-11-26 23:19:41'),
                                                                                  (122, 6, '2024-11-26 23:19:41'),
                                                                                  (123, 6, '2024-11-26 23:19:42'),
                                                                                  (124, 6, '2024-11-26 23:19:42'),
                                                                                  (125, 6, '2024-11-26 23:19:42'),
                                                                                  (126, 6, '2024-11-26 23:19:43'),
                                                                                  (127, 6, '2024-11-26 23:19:44'),
                                                                                  (128, 6, '2024-11-26 23:19:45'),
                                                                                  (129, 6, '2024-11-26 23:19:45'),
                                                                                  (130, 6, '2024-11-26 23:19:45'),
                                                                                  (131, 6, '2024-11-26 23:19:46'),
                                                                                  (132, 6, '2024-11-26 23:19:47'),
                                                                                  (133, 11, '2024-11-26 23:23:25'),
                                                                                  (134, 11, '2024-11-26 23:23:26'),
                                                                                  (135, 11, '2024-11-26 23:23:27'),
                                                                                  (136, 11, '2024-11-26 23:23:27'),
                                                                                  (137, 11, '2024-11-26 23:23:28'),
                                                                                  (138, 11, '2024-11-26 23:23:28'),
                                                                                  (139, 11, '2024-11-26 23:23:28'),
                                                                                  (140, 1, '2024-12-21 01:12:42'),
                                                                                  (141, 1, '2024-12-21 01:12:53'),
                                                                                  (142, 1, '2024-12-21 01:12:53'),
                                                                                  (143, 6, '2024-12-21 01:13:06'),
                                                                                  (144, 6, '2024-12-21 01:13:06'),
                                                                                  (145, 6, '2024-12-21 01:13:06'),
                                                                                  (146, 6, '2024-12-21 01:13:07'),
                                                                                  (147, 6, '2024-12-21 01:13:08'),
                                                                                  (148, 12, '2024-12-21 01:13:16'),
                                                                                  (149, 12, '2024-12-21 01:13:17'),
                                                                                  (150, 12, '2024-12-21 01:13:17'),
                                                                                  (151, 12, '2024-12-21 01:13:18'),
                                                                                  (152, 12, '2024-12-21 01:13:18'),
                                                                                  (153, 12, '2024-12-21 01:13:19'),
                                                                                  (154, 3, '2024-12-21 01:13:28'),
                                                                                  (155, 3, '2024-12-21 01:13:28'),
                                                                                  (156, 3, '2024-12-21 01:13:29'),
                                                                                  (157, 3, '2024-12-21 01:13:30'),
                                                                                  (158, 3, '2024-12-21 01:13:30'),
                                                                                  (159, 3, '2024-12-21 01:13:31'),
                                                                                  (160, 3, '2024-12-21 01:13:32'),
                                                                                  (161, 3, '2024-12-21 01:13:33'),
                                                                                  (162, 3, '2024-12-21 01:13:34'),
                                                                                  (163, 3, '2024-12-21 01:13:36'),
                                                                                  (164, 3, '2024-12-21 01:13:38'),
                                                                                  (165, 3, '2024-12-21 01:14:07'),
                                                                                  (166, 3, '2024-12-21 01:14:07'),
                                                                                  (167, 3, '2024-12-21 01:14:08'),
                                                                                  (168, 3, '2024-12-21 01:14:09'),
                                                                                  (169, 3, '2024-12-21 01:14:09'),
                                                                                  (170, 3, '2024-12-21 01:14:09'),
                                                                                  (171, 3, '2024-12-21 01:14:10'),
                                                                                  (172, 3, '2025-01-05 05:02:58'),
                                                                                  (173, 3, '2025-01-05 05:02:58'),
                                                                                  (174, 3, '2025-01-05 05:03:11'),
                                                                                  (175, 3, '2025-01-05 05:03:11'),
                                                                                  (176, 12, '2025-01-05 05:03:25'),
                                                                                  (177, 12, '2025-01-05 05:03:25'),
                                                                                  (178, 12, '2025-01-05 05:03:45'),
                                                                                  (179, 12, '2025-01-05 05:03:45'),
                                                                                  (180, 12, '2025-01-05 05:03:48'),
                                                                                  (181, 12, '2025-01-05 05:03:48'),
                                                                                  (182, 12, '2025-01-05 05:04:24'),
                                                                                  (183, 12, '2025-01-05 05:04:24'),
                                                                                  (184, 12, '2025-01-05 05:04:48'),
                                                                                  (185, 12, '2025-01-05 05:04:48'),
                                                                                  (186, 12, '2025-01-05 05:05:51'),
                                                                                  (187, 12, '2025-01-05 05:05:51'),
                                                                                  (188, 12, '2025-01-05 05:05:56'),
                                                                                  (189, 12, '2025-01-05 05:05:56'),
                                                                                  (190, 12, '2025-01-05 05:06:07'),
                                                                                  (191, 12, '2025-01-05 05:06:07'),
                                                                                  (192, 12, '2025-01-05 05:06:14'),
                                                                                  (193, 12, '2025-01-05 05:06:14'),
                                                                                  (194, 12, '2025-01-05 05:06:31'),
                                                                                  (195, 12, '2025-01-05 05:06:31'),
                                                                                  (196, 12, '2025-01-05 05:06:49'),
                                                                                  (197, 12, '2025-01-05 05:06:49'),
                                                                                  (198, 12, '2025-01-05 05:07:00'),
                                                                                  (199, 12, '2025-01-05 05:07:00'),
                                                                                  (200, 12, '2025-01-05 05:07:33'),
                                                                                  (201, 12, '2025-01-05 05:07:33'),
                                                                                  (202, 12, '2025-01-05 05:07:44'),
                                                                                  (203, 12, '2025-01-05 05:07:44'),
                                                                                  (204, 12, '2025-01-05 05:08:51'),
                                                                                  (205, 12, '2025-01-05 05:08:56'),
                                                                                  (206, 12, '2025-01-05 05:08:56'),
                                                                                  (207, 12, '2025-01-05 05:09:14'),
                                                                                  (208, 12, '2025-01-05 05:09:19'),
                                                                                  (209, 12, '2025-01-05 05:09:19'),
                                                                                  (210, 12, '2025-01-05 05:11:42'),
                                                                                  (211, 6, '2025-01-13 16:14:01'),
                                                                                  (212, 6, '2025-01-13 16:14:02'),
                                                                                  (213, 6, '2025-01-13 16:14:02'),
                                                                                  (214, 6, '2025-01-13 16:14:02'),
                                                                                  (215, 6, '2025-01-13 16:14:03'),
                                                                                  (216, 6, '2025-01-13 16:14:03'),
                                                                                  (217, 6, '2025-01-13 16:14:03'),
                                                                                  (218, 6, '2025-01-13 16:14:03'),
                                                                                  (219, 6, '2025-01-13 16:14:03'),
                                                                                  (220, 6, '2025-01-13 16:14:04'),
                                                                                  (221, 6, '2025-01-13 16:14:04'),
                                                                                  (222, 6, '2025-01-13 16:14:04'),
                                                                                  (223, 6, '2025-01-13 16:14:04'),
                                                                                  (224, 6, '2025-01-13 16:14:05'),
                                                                                  (225, 6, '2025-01-13 16:14:05'),
                                                                                  (226, 12, '2025-01-13 16:14:11'),
                                                                                  (227, 12, '2025-01-13 16:14:11'),
                                                                                  (228, 12, '2025-01-13 16:14:11'),
                                                                                  (229, 12, '2025-01-13 16:14:12'),
                                                                                  (230, 12, '2025-01-13 16:14:12'),
                                                                                  (231, 12, '2025-01-13 16:14:12'),
                                                                                  (232, 12, '2025-01-13 16:14:12'),
                                                                                  (233, 12, '2025-01-13 16:14:12'),
                                                                                  (234, 12, '2025-01-13 16:14:12'),
                                                                                  (235, 12, '2025-01-13 16:14:13'),
                                                                                  (236, 12, '2025-01-13 16:14:13'),
                                                                                  (237, 3, '2025-01-13 16:14:19'),
                                                                                  (238, 3, '2025-01-13 16:14:19'),
                                                                                  (239, 3, '2025-01-13 16:14:20'),
                                                                                  (240, 3, '2025-01-13 16:14:20'),
                                                                                  (241, 3, '2025-01-13 16:14:20'),
                                                                                  (242, 3, '2025-01-13 16:14:20'),
                                                                                  (243, 3, '2025-01-13 16:14:21'),
                                                                                  (244, 3, '2025-01-13 16:14:21'),
                                                                                  (245, 3, '2025-01-13 16:14:21'),
                                                                                  (246, 3, '2025-01-13 16:14:21'),
                                                                                  (247, 3, '2025-01-13 16:14:22'),
                                                                                  (248, 3, '2025-01-13 16:14:22'),
                                                                                  (249, 3, '2025-01-13 16:14:22'),
                                                                                  (250, 3, '2025-01-13 16:14:23'),
                                                                                  (251, 3, '2025-01-13 16:14:24'),
                                                                                  (252, 3, '2025-01-13 16:14:25'),
                                                                                  (253, 3, '2025-01-13 16:14:25'),
                                                                                  (254, 3, '2025-01-13 16:14:25'),
                                                                                  (255, 3, '2025-01-13 16:14:25'),
                                                                                  (256, 3, '2025-01-13 16:14:26'),
                                                                                  (257, 3, '2025-01-13 16:14:26'),
                                                                                  (258, 3, '2025-01-13 16:14:26'),
                                                                                  (259, 3, '2025-01-13 16:14:26'),
                                                                                  (260, 3, '2025-01-13 16:14:26'),
                                                                                  (261, 3, '2025-01-13 16:14:26'),
                                                                                  (262, 3, '2025-01-13 16:14:27'),
                                                                                  (263, 3, '2025-01-13 16:14:27'),
                                                                                  (264, 3, '2025-01-13 16:14:27'),
                                                                                  (265, 3, '2025-01-13 16:14:27'),
                                                                                  (266, 3, '2025-01-13 16:14:27'),
                                                                                  (267, 3, '2025-01-13 16:14:28'),
                                                                                  (268, 3, '2025-01-13 16:14:28'),
                                                                                  (269, 3, '2025-01-13 16:14:29'),
                                                                                  (270, 3, '2025-01-13 16:14:29'),
                                                                                  (271, 3, '2025-01-13 16:14:29'),
                                                                                  (272, 3, '2025-01-13 16:14:29'),
                                                                                  (273, 3, '2025-01-13 16:14:29'),
                                                                                  (274, 3, '2025-01-13 16:14:29'),
                                                                                  (275, 3, '2025-01-13 16:14:30'),
                                                                                  (276, 3, '2025-01-13 16:14:30'),
                                                                                  (277, 1, '2025-01-13 16:14:35'),
                                                                                  (278, 1, '2025-01-13 16:14:35'),
                                                                                  (279, 1, '2025-01-13 16:14:36'),
                                                                                  (280, 1, '2025-01-13 16:14:36'),
                                                                                  (281, 1, '2025-01-13 16:14:36'),
                                                                                  (282, 1, '2025-01-13 16:14:36'),
                                                                                  (283, 1, '2025-01-13 16:14:36'),
                                                                                  (284, 1, '2025-01-13 16:14:37'),
                                                                                  (285, 1, '2025-01-13 16:14:37'),
                                                                                  (286, 1, '2025-01-13 16:14:37'),
                                                                                  (287, 1, '2025-01-13 16:14:37'),
                                                                                  (288, 6, '2025-01-13 16:14:42'),
                                                                                  (289, 6, '2025-01-13 16:14:43'),
                                                                                  (290, 6, '2025-01-13 16:14:43'),
                                                                                  (291, 6, '2025-01-13 16:14:43'),
                                                                                  (292, 6, '2025-01-13 16:14:43'),
                                                                                  (293, 6, '2025-01-13 16:14:43'),
                                                                                  (294, 6, '2025-01-13 16:14:43'),
                                                                                  (295, 6, '2025-01-13 16:14:44'),
                                                                                  (296, 6, '2025-01-13 16:14:44'),
                                                                                  (297, 6, '2025-01-13 16:14:44'),
                                                                                  (298, 6, '2025-01-13 16:14:44'),
                                                                                  (299, 6, '2025-01-13 16:14:44'),
                                                                                  (300, 6, '2025-01-13 16:14:45'),
                                                                                  (301, 6, '2025-01-13 16:14:45'),
                                                                                  (302, 6, '2025-01-13 16:14:45'),
                                                                                  (303, 6, '2025-01-13 16:14:45'),
                                                                                  (304, 6, '2025-01-13 16:14:45'),
                                                                                  (305, 6, '2025-01-13 16:14:46'),
                                                                                  (306, 6, '2025-01-13 16:14:46');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `roles`
--

CREATE TABLE `roles` (
                         `role_id` int UNSIGNED NOT NULL,
                         `name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
                         `apply_on_user_creation` bit(1) NOT NULL DEFAULT b'0',
                         `deletable` bit(1) NOT NULL DEFAULT b'1',
                         `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                         `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Daten für Tabelle `roles`
--

INSERT INTO `roles` (`role_id`, `name`, `apply_on_user_creation`, `deletable`, `created_at`, `updated_at`) VALUES
                                                                                                               (1, 'administrator', b'0', b'0', '2024-12-06 19:39:38', '2024-12-06 19:44:34'),
                                                                                                               (2, 'user', b'1', b'0', '2024-12-06 19:40:22', '2024-12-06 19:44:34');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `role_privilege_map`
--

CREATE TABLE `role_privilege_map` (
                                      `role_id` int UNSIGNED NOT NULL,
                                      `privilege_node` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users`
--

CREATE TABLE `users` (
                         `user_id` int UNSIGNED NOT NULL,
                         `username` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
                         `email` varchar(320) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
                         `password` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
                         `totp_secret_key` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
                         `disabled` bit(1) NOT NULL DEFAULT b'0',
                         `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                         `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Daten für Tabelle `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password`, `totp_secret_key`, `disabled`, `created_at`, `updated_at`) VALUES
                                                                                                                                (1, 'Nebalus', 'contact@nebalus.dev', '$2y$10$9xaR/88aZteW49ExqqveWe6O./RkNfrAj3tSNGPCc/keJsT95EcEu', 'S61WXXWZU5J6QT0H4CX4B02X2HET0LYW', b'0', '2024-02-28 21:28:40', '2024-08-03 23:07:10'),
                                                                                                                                (2, 'Tester', 'tester@nebalus.dev', '$2y$10$9xaR/88aZteW49ExqqveWe6O./RkNfrAj3tSNGPCc/keJsT95EcEu', '5BO8E403VD95MT6XCHWFXOKP8LZCGRKY', b'0', '2024-11-07 07:56:33', '2024-11-07 07:56:33'),
                                                                                                                                (3, 'BannedTester', 'bannedtester@nebalus.dev', '$2y$10$9xaR/88aZteW49ExqqveWe6O./RkNfrAj3tSNGPCc/keJsT95EcEu', 'DXUZV74K66YCFV4E9WD9T9G4TYO6SWH7', b'0', '2024-11-07 08:07:04', '2024-11-07 08:07:04'),
                                                                                                                                (4, 'disabledbitch', 'disabledbitch@nebalus.dev', '$2y$10$9xaR/88aZteW49ExqqveWe6O./RkNfrAj3tSNGPCc/keJsT95EcEu', '5VX7YY1UH0U4DECIJHB1AY6PHL6IGHKP', b'0', '2024-11-11 18:31:01', '2024-11-11 18:31:01');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_access_log`
--

CREATE TABLE `user_access_log` (
                                   `access_log_id` int UNSIGNED NOT NULL,
                                   `user_id` int UNSIGNED NOT NULL,
                                   `ip_address` tinyblob NOT NULL,
                                   `success` bit(1) NOT NULL DEFAULT b'0',
                                   `user_agent` text NOT NULL,
                                   `happend_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Daten für Tabelle `user_access_log`
--

INSERT INTO `user_access_log` (`access_log_id`, `user_id`, `ip_address`, `success`, `user_agent`, `happend_at`) VALUES
                                                                                                                    (2, 1, 0x32313330373036343333, b'1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/98.0.4758.80 Safari/537.36', '2024-11-07 08:02:42'),
                                                                                                                    (3, 2, 0x31383737343331383433, b'1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:97.0) Gecko/20100101 Firefox/97.0', '2024-11-07 08:17:36'),
                                                                                                                    (4, 1, 0x30, b'0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/98.0.4758.80 Safari/537.36', '2024-11-12 08:17:37'),
                                                                                                                    (5, 1, 0x33323332323336303037, b'1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/98.0.4758.80 Safari/537.36', '2024-10-08 08:17:37');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_role_map`
--

CREATE TABLE `user_role_map` (
                                 `user_id` int UNSIGNED NOT NULL,
                                 `role_id` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Daten für Tabelle `user_role_map`
--

INSERT INTO `user_role_map` (`user_id`, `role_id`) VALUES
                                                       (1, 1),
                                                       (1, 2),
                                                       (2, 2),
                                                       (3, 2),
                                                       (4, 2);

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `accounts`
--
ALTER TABLE `accounts`
    ADD PRIMARY KEY (`account_id`),
    ADD KEY `user_id` (`user_id`);

--
-- Indizes für die Tabelle `account_invitation_tokens`
--
ALTER TABLE `account_invitation_tokens`
    ADD PRIMARY KEY (`token_field_1`,`token_field_2`,`token_field_3`,`token_field_4`,`token_checksum`) USING BTREE,
    ADD UNIQUE KEY `invited_user_id` (`invited_account_id`),
    ADD KEY `owner_user_id` (`owner_account_id`);

--
-- Indizes für die Tabelle `account_punishments`
--
ALTER TABLE `account_punishments`
    ADD PRIMARY KEY (`punishment_id`),
    ADD KEY `punished_user_id` (`punished_account_id`),
    ADD KEY `moderator_user_id` (`punisher_account_id`),
    ADD KEY `account_punishments_ibfk_3` (`pardoner_account_id`);

--
-- Indizes für die Tabelle `apod`
--
ALTER TABLE `apod`
    ADD PRIMARY KEY (`apod_id`);

--
-- Indizes für die Tabelle `apod_likes`
--
ALTER TABLE `apod_likes`
    ADD KEY `apod_likes_ibfk_1` (`apod_id`),
    ADD KEY `user_id` (`user_id`);

--
-- Indizes für die Tabelle `blogs`
--
ALTER TABLE `blogs`
    ADD PRIMARY KEY (`blog_id`),
    ADD KEY `blog_owner_id` (`blog_owner_id`);

--
-- Indizes für die Tabelle `forms`
--
ALTER TABLE `forms`
    ADD PRIMARY KEY (`form_id`),
    ADD KEY `owner_user_id` (`owner_user_id`);

--
-- Indizes für die Tabelle `form_submits`
--
ALTER TABLE `form_submits`
    ADD PRIMARY KEY (`form_submit_id`),
    ADD KEY `form_id` (`form_id`);

--
-- Indizes für die Tabelle `girl_game_instances`
--
ALTER TABLE `girl_game_instances`
    ADD PRIMARY KEY (`girl_game_id`),
    ADD KEY `account_id` (`account_id`),
    ADD KEY `girl_game_type` (`girl_game_type`);

--
-- Indizes für die Tabelle `girl_game_types`
--
ALTER TABLE `girl_game_types`
    ADD PRIMARY KEY (`girl_game_type`);

--
-- Indizes für die Tabelle `linktrees`
--
ALTER TABLE `linktrees`
    ADD PRIMARY KEY (`linktree_id`),
    ADD UNIQUE KEY `account` (`owner_user_id`);

--
-- Indizes für die Tabelle `linktree_click_metric`
--
ALTER TABLE `linktree_click_metric`
    ADD PRIMARY KEY (`click_id`),
    ADD KEY `analytics_linktree_clicks_ibfk_1` (`linktree_id`);

--
-- Indizes für die Tabelle `linktree_entrys`
--
ALTER TABLE `linktree_entrys`
    ADD PRIMARY KEY (`linktree_entry_id`),
    ADD UNIQUE KEY `linktree_id` (`linktree_id`,`display_order`);

--
-- Indizes für die Tabelle `privileges`
--
ALTER TABLE `privileges`
    ADD UNIQUE KEY `privilege_node` (`privilege_node`);

--
-- Indizes für die Tabelle `projects`
--
ALTER TABLE `projects`
    ADD PRIMARY KEY (`project_id`),
    ADD UNIQUE KEY `project_uuid` (`project_uuid`),
    ADD KEY `owner_id` (`owner_user_id`);

--
-- Indizes für die Tabelle `project_clients`
--
ALTER TABLE `project_clients`
    ADD PRIMARY KEY (`project_client_id`),
    ADD UNIQUE KEY `client_uuid` (`client_uuid`);

--
-- Indizes für die Tabelle `referrals`
--
ALTER TABLE `referrals`
    ADD PRIMARY KEY (`referral_id`),
    ADD UNIQUE KEY `refcode` (`code`) USING BTREE,
    ADD KEY `user_id` (`owner_user_id`);

--
-- Indizes für die Tabelle `referral_click_metric`
--
ALTER TABLE `referral_click_metric`
    ADD PRIMARY KEY (`click_id`),
    ADD KEY `referral_id` (`referral_id`);

--
-- Indizes für die Tabelle `roles`
--
ALTER TABLE `roles`
    ADD PRIMARY KEY (`role_id`),
    ADD UNIQUE KEY `group_name` (`name`);

--
-- Indizes für die Tabelle `role_privilege_map`
--
ALTER TABLE `role_privilege_map`
    ADD PRIMARY KEY (`privilege_node`,`role_id`),
    ADD KEY `role_id` (`role_id`);

--
-- Indizes für die Tabelle `users`
--
ALTER TABLE `users`
    ADD PRIMARY KEY (`user_id`),
    ADD UNIQUE KEY `email` (`email`),
    ADD UNIQUE KEY `username` (`username`);

--
-- Indizes für die Tabelle `user_access_log`
--
ALTER TABLE `user_access_log`
    ADD PRIMARY KEY (`access_log_id`),
    ADD KEY `user_id` (`user_id`);

--
-- Indizes für die Tabelle `user_role_map`
--
ALTER TABLE `user_role_map`
    ADD PRIMARY KEY (`user_id`,`role_id`),
    ADD KEY `group_id` (`role_id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `accounts`
--
ALTER TABLE `accounts`
    MODIFY `account_id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT für Tabelle `account_punishments`
--
ALTER TABLE `account_punishments`
    MODIFY `punishment_id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `apod`
--
ALTER TABLE `apod`
    MODIFY `apod_id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `blogs`
--
ALTER TABLE `blogs`
    MODIFY `blog_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `forms`
--
ALTER TABLE `forms`
    MODIFY `form_id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `form_submits`
--
ALTER TABLE `form_submits`
    MODIFY `form_submit_id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `girl_game_instances`
--
ALTER TABLE `girl_game_instances`
    MODIFY `girl_game_id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `linktrees`
--
ALTER TABLE `linktrees`
    MODIFY `linktree_id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT für Tabelle `linktree_click_metric`
--
ALTER TABLE `linktree_click_metric`
    MODIFY `click_id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT für Tabelle `linktree_entrys`
--
ALTER TABLE `linktree_entrys`
    MODIFY `linktree_entry_id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT für Tabelle `projects`
--
ALTER TABLE `projects`
    MODIFY `project_id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `project_clients`
--
ALTER TABLE `project_clients`
    MODIFY `project_client_id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT für Tabelle `referrals`
--
ALTER TABLE `referrals`
    MODIFY `referral_id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT für Tabelle `referral_click_metric`
--
ALTER TABLE `referral_click_metric`
    MODIFY `click_id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=307;

--
-- AUTO_INCREMENT für Tabelle `roles`
--
ALTER TABLE `roles`
    MODIFY `role_id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT für Tabelle `users`
--
ALTER TABLE `users`
    MODIFY `user_id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT für Tabelle `user_access_log`
--
ALTER TABLE `user_access_log`
    MODIFY `access_log_id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `accounts`
--
ALTER TABLE `accounts`
    ADD CONSTRAINT `accounts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE RESTRICT;

--
-- Constraints der Tabelle `account_invitation_tokens`
--
ALTER TABLE `account_invitation_tokens`
    ADD CONSTRAINT `account_invitation_tokens_ibfk_2` FOREIGN KEY (`invited_account_id`) REFERENCES `accounts` (`account_id`) ON UPDATE RESTRICT,
    ADD CONSTRAINT `account_invitation_tokens_ibfk_3` FOREIGN KEY (`owner_account_id`) REFERENCES `accounts` (`account_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints der Tabelle `account_punishments`
--
ALTER TABLE `account_punishments`
    ADD CONSTRAINT `account_punishments_ibfk_1` FOREIGN KEY (`punished_account_id`) REFERENCES `accounts` (`account_id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
    ADD CONSTRAINT `account_punishments_ibfk_2` FOREIGN KEY (`punisher_account_id`) REFERENCES `accounts` (`account_id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
    ADD CONSTRAINT `account_punishments_ibfk_3` FOREIGN KEY (`pardoner_account_id`) REFERENCES `accounts` (`account_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints der Tabelle `apod_likes`
--
ALTER TABLE `apod_likes`
    ADD CONSTRAINT `apod_likes_ibfk_1` FOREIGN KEY (`apod_id`) REFERENCES `apod` (`apod_id`) ON DELETE CASCADE ON UPDATE RESTRICT,
    ADD CONSTRAINT `apod_likes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints der Tabelle `blogs`
--
ALTER TABLE `blogs`
    ADD CONSTRAINT `blogs_ibfk_1` FOREIGN KEY (`blog_owner_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints der Tabelle `forms`
--
ALTER TABLE `forms`
    ADD CONSTRAINT `forms_ibfk_1` FOREIGN KEY (`owner_user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints der Tabelle `form_submits`
--
ALTER TABLE `form_submits`
    ADD CONSTRAINT `form_submits_ibfk_1` FOREIGN KEY (`form_id`) REFERENCES `forms` (`form_id`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints der Tabelle `girl_game_instances`
--
ALTER TABLE `girl_game_instances`
    ADD CONSTRAINT `girl_game_instances_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
    ADD CONSTRAINT `girl_game_instances_ibfk_2` FOREIGN KEY (`girl_game_type`) REFERENCES `girl_game_types` (`girl_game_type`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints der Tabelle `linktrees`
--
ALTER TABLE `linktrees`
    ADD CONSTRAINT `linktrees_ibfk_1` FOREIGN KEY (`owner_user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints der Tabelle `linktree_click_metric`
--
ALTER TABLE `linktree_click_metric`
    ADD CONSTRAINT `linktree_click_metric_ibfk_1` FOREIGN KEY (`linktree_id`) REFERENCES `linktrees` (`linktree_id`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints der Tabelle `linktree_entrys`
--
ALTER TABLE `linktree_entrys`
    ADD CONSTRAINT `linktree_entrys_ibfk_1` FOREIGN KEY (`linktree_id`) REFERENCES `linktrees` (`linktree_id`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints der Tabelle `projects`
--
ALTER TABLE `projects`
    ADD CONSTRAINT `projects_ibfk_1` FOREIGN KEY (`owner_user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints der Tabelle `referrals`
--
ALTER TABLE `referrals`
    ADD CONSTRAINT `referrals_ibfk_1` FOREIGN KEY (`owner_user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints der Tabelle `referral_click_metric`
--
ALTER TABLE `referral_click_metric`
    ADD CONSTRAINT `referral_click_metric_ibfk_1` FOREIGN KEY (`referral_id`) REFERENCES `referrals` (`referral_id`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints der Tabelle `role_privilege_map`
--
ALTER TABLE `role_privilege_map`
    ADD CONSTRAINT `role_privilege_map_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON DELETE CASCADE ON UPDATE RESTRICT,
    ADD CONSTRAINT `role_privilege_map_ibfk_3` FOREIGN KEY (`privilege_node`) REFERENCES `privileges` (`privilege_node`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `user_access_log`
--
ALTER TABLE `user_access_log`
    ADD CONSTRAINT `user_access_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints der Tabelle `user_role_map`
--
ALTER TABLE `user_role_map`
    ADD CONSTRAINT `user_role_map_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `user_role_map_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;