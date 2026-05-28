-- phpMyAdmin SQL Dump
-- version 6.0.0-dev+20260101.5c8325853b
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Feb 24, 2026 at 09:49 AM
-- Server version: 8.4.5
-- PHP Version: 8.4.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `laravel_11`
--

-- --------------------------------------------------------

--
-- Table structure for table `lr_accounts`
--

CREATE TABLE `lr_accounts` (
  `id` int NOT NULL,
  `uuid` varchar(36) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(32) NOT NULL,
  `fullname` varchar(64) NOT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '2',
  `recovery_code` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `recovery_code_duration` int DEFAULT NULL,
  `token` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `suspended_until` timestamp NULL DEFAULT NULL,
  `suspended_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `lr_accounts`
--

INSERT INTO `lr_accounts` (`id`, `uuid`, `email`, `username`, `fullname`, `password`, `remember_token`, `status`, `recovery_code`, `recovery_code_duration`, `token`, `suspended_until`, `suspended_at`, `updated_at`, `created_at`) VALUES
(1, 'd7787d9e-e11f-4fcd-8e76-8bab72eb3073', 'admin@aruna-dev.com', 'administrator', 'Administrator', '$2y$12$DOZYv27cil8FPhMZUIdqduaeFMzVrNC2rBadwAH5TM1o6tvKyyw5q', '', 1, 'laraphoexRecovery_66ebe9edb48e0', NULL, 'LaraPhoenixPAT_66ebe9edb494d', NULL, NULL, '2025-08-22 17:18:08', '2025-05-17 05:31:47'),
(2, 'd1c7bc73-00b2-4d5e-9d32-f97fd00e8cde', 'andhika@aruna-dev.com', 'andhika', 'Andhika Adhitia N', '$2y$12$UgDY./heHCqF/m6PsL.9Ru8I8zwEeBfmxSzHNdBd3V1cSY/2OV91S', '', 2, 'laraphoexRecovery_668d074ed832e', NULL, 'LaraPhoenixPAT_668d074ed837f', '2025-05-27 08:14:41', NULL, '2025-07-25 04:35:02', '2025-05-17 05:31:47'),
(3, '', 'pitaloka@aruna-dev.com', 'pitaloka', 'Pitaloka', '$2y$10$D4.yjlvcKRErC5SynYsUqe71dL8rp5VcAr3dY6jPV7Ygd4gU.2vZq', '', 1, '', NULL, '', NULL, NULL, '2024-09-04 01:54:13', '2025-05-17 05:31:47'),
(4, '', 'malika@aruna-dev.id', 'malika', 'Malika', '$2y$10$x2NIm41WOYXfbG/V/KMW2uMMwb/qkogPqrS8n5U1nigtUCEIJZrRG', '', 1, '', NULL, '', NULL, NULL, '2025-01-20 02:28:19', '2025-05-17 05:31:47'),
(5, '', 'rajolangit@aruna-dev.id', 'rajolangit', 'Rajo Langit', '$2y$10$PesVGBgTRMuxoRbA3plyVO0FbsOJTEHnoMxAOxJbYVmH.qJyJWuaK', '', 1, '', NULL, '', NULL, NULL, '2025-01-20 02:29:02', '2025-05-17 05:31:47'),
(6, '', 'limbubu@aruna-dev.id', 'limbubu', 'Lim Bubu', '$2y$12$XC5S.tJh2.Bh1H/qoYTqRO7nbtKCHqe/UoyxXKbzN4bQrBxpuK4ya', '', 2, 'laraphoexRecovery_66a340a83f0af', NULL, 'LaraPhoenixPAT_66a340a83f226', NULL, NULL, '2024-07-25 23:22:32', '2025-05-17 05:31:47'),
(7, '', 'aruna.dev96@gmail.com', 'arunika', 'Arunika', '$2y$12$YIS/nzRdNEzAmtVSxiSX2OQzYTJyi4oGjMABfhatD9M9XuRv2q6rC', '', 2, '9jQXDAs8L1SHk5O42phmZK', 1748472272, '', NULL, NULL, '2025-05-28 22:43:32', '2025-05-17 05:31:47'),
(8, '', 'azazahra@gmail.com', 'azazahra', 'Az Zahra', '$2y$10$w3q.fRXsg0lJ3J2EyR03Ju3ztb36p/PreMR7VRrGSXEMZAofQJta.', '', 2, '', NULL, '', NULL, NULL, '2024-07-09 02:24:14', '2025-05-17 05:31:47'),
(9, '', 'kirana@gmail.com', 'kirana', 'Kirana', '$2y$10$jcDoRAel/3.hBdDktKqsSOJ5lSYZUsXyO0nuHnViaNaGvQRdP/iAC', '', 2, '', NULL, '', NULL, NULL, '2024-07-09 02:24:14', '2025-05-17 05:31:47'),
(10, '889017d8-a466-4791-bae9-c7cb2ac410fc', 'gumara@aruna-dev.id', 'gumara', 'Gumara Peto Alam Edited', '$2y$10$XjReXV4PvTE1ZYpCIQgrL.65m5BehtOhFkmjnSKmI5cBzTJOs7ZPC', '', 2, '', NULL, '', NULL, NULL, '2026-01-09 07:08:11', '2025-05-17 05:31:47'),
(11, '', 'cyber.phoenix@aruna-dev.id', 'cyber_phoenix', 'Cyber Phoenix', '$2y$10$YdWG1tsalZCI/wBqbcUoluiSRGG8Rt9OT06ilBDadNOlEUYREOzh6', '', 2, '', NULL, '', NULL, NULL, '2024-07-09 02:25:00', '2025-05-17 05:31:47'),
(12, '', 'hazy@gmail.com', 'hazy', 'Hazy', '$2y$10$FGNs7EJioCPWjzRsOyOiseKaCQOP8OpDXwj4lFF2JFNSuTGXPVXGK', '', 2, '', NULL, '', NULL, NULL, '2024-07-09 02:25:00', '2025-05-17 05:31:47'),
(13, '', 'seven.lions@aruna-dev.id', 'seven_lions', 'Seven Lions', '$2y$10$cFtgjygnQ.uqVtRe3KtNe./38PYj72zY.qPtAgEE/HP.zOIR8mv22', '', 2, '', NULL, '', NULL, NULL, '2024-07-09 02:25:00', '2025-05-17 05:31:47'),
(14, '', 'illenium.ashes@aruna-dev.id', 'illenium', 'Illenium Ashes', '$2y$10$08fHik6WBl3H4aI5aJwlD.qDHA1Y2.VdLGs1KsPKFiqfD/r7Qdcj6', '', 2, '', NULL, '', NULL, NULL, '2024-07-09 02:25:00', '2025-05-17 05:31:47'),
(15, '', 'karina@aruna-dev.id', 'karina', 'Karina', '$2y$10$oF8bK8jB3oHmSojXVwDFn.QA2szbCl0X/TP6jh.epnjlng9.SQWL.', '', 2, '', NULL, '', NULL, NULL, '2024-07-09 02:25:00', '2025-05-17 05:31:47'),
(16, '', 'andhika.adhitia96@gmail.com', 'adhitia', 'Adhitia', '$2y$10$0ETFtB0IOu4xL1zwo0O3HewgOivmjZ9peZM.NKdlNWLXizavx.Jly', '', 2, '0Vjrf5Q2EZMgv1SxYuaKbF', 1748595974, '', NULL, NULL, '2025-05-30 09:01:14', '2025-05-17 05:31:47'),
(17, '', 'rfasyurapvp@gmail.com', 'asyura', 'RF Asyura', '$2y$10$tL16JDjpj3w5K3xxbtfz2.4iPTJin3r7wcL8RpjClzBWDdOIL3nse', '', 2, '', NULL, '', NULL, NULL, '2024-07-09 02:25:00', '2025-05-17 05:31:47'),
(18, 'c8de9f7d-07ba-4108-8812-1b413854017c', 'gumara.peto@aruna-dev.id', 'gumara_peto_alam', 'Gumara Peto Alam Edited 2', '$2y$10$1zuUYhahQ.1IaCp8owh1tOeTW99ZsjI/Dr1mKnxF3jmPdxC3kdWoe', '', 2, '', NULL, '', NULL, NULL, '2026-01-09 07:08:28', '2025-05-17 05:31:47'),
(19, NULL, 'testing_account1@aruna-dev.com', 'testing_account1', 'Testing Account 1', '$2y$12$XiILVlL9SR1Gz1rJKcnOruuD33k0lOGpNbRZCARUaeCRwz6OYDlXC', NULL, 1, 'KONTOL', NULL, NULL, NULL, NULL, '2025-05-28 21:48:08', '2025-05-17 05:31:47'),
(20, NULL, 'testing_account2@aruna-dev.com', 'u8534609', 'Testing Account 2', '$2y$12$1nf01.LCgUzeDMEewaKL2Oeo075rpXwcJj08WIN78i6ZggwUkOHAa', NULL, 1, 'KONTOL', NULL, NULL, NULL, NULL, '2025-05-28 21:48:48', '2025-05-17 06:15:27'),
(21, NULL, 'testing_account3@aruna-dev.com', 'u6203581', 'Testing Account 3', '$2y$12$K9BXIIvVD8BmykHExnpMYO8mXsDP6odtjEJJboojSdQlmoQO8qnV6', NULL, 1, 'KONTOL', NULL, NULL, NULL, NULL, '2025-05-28 21:49:50', '2025-05-17 06:17:00'),
(22, NULL, 'testing_account4@aruna-dev.com', 'u9017563', 'Testing Account 4', '$2y$12$DdOCsleN4Wbkqo.sLU43PeVUbhos/JIQ.sGzj8p.U9Z8G88c3ul9S', NULL, 1, 'KONTOL', NULL, NULL, NULL, NULL, '2025-05-28 21:51:03', '2025-05-17 06:18:37'),
(23, NULL, 'testing_account5@aruna-dev.com', 'u6309178', 'Testing Account 5', '$2y$12$6kxbVJ85D0qyJeE/60xaf.7.K9LIIUV4KOnZPQDjqa5ZGuzRVU9He', NULL, 1, 'KONTOL', NULL, NULL, NULL, NULL, '2025-05-28 21:51:06', '2025-05-17 06:20:00'),
(24, NULL, 'testing_account6@aruna-dev.com', 'u3806251', 'Testing Account 6', '$2y$12$dzrwN.Gt4mIam2ldlOPfZODucb6hVMg3Xr6Wk/VfRha39oVyqxB1i', NULL, 2, 'KONTOL', NULL, NULL, NULL, NULL, '2025-05-28 21:51:07', '2025-05-17 06:26:51'),
(25, NULL, 'testing_account7@aruna-dev.com', 'u1960873', 'Testing Account 7', '$2y$12$NCWwIvGkK.AaGvraPQIA0eFPkvGeZ8/bEw3HM6BrnZ2DUq7.qOG.K', NULL, 1, 'KONTOL', NULL, NULL, NULL, NULL, '2025-05-28 21:51:12', '2025-05-17 06:29:05'),
(26, NULL, 'testing_account8@aruna-dev.com', 'u9250843', 'Testing Account 8', '$2y$12$ER2rxZlqW5bmt.9u0UIMMeELnBO8iVUHhZkleMCJmw4U/vqltxO/.', NULL, 1, 'KONTOL', NULL, NULL, NULL, NULL, '2025-05-28 21:51:35', '2025-05-17 06:32:21'),
(27, NULL, 'testing_account9@aruna-dev.com', 'u9046721', 'Testing Account 9', '$2y$12$GF2HAKaAtNzJaHV2jR9.kuXZSdB5mkZIGCUdXes2C6/.ypprp09Hm', NULL, 1, 'KONTOL', NULL, NULL, NULL, NULL, '2025-05-28 21:51:58', '2025-05-17 06:33:54'),
(28, NULL, 'testing_account10@aruna-dev.com', 'u1839245', 'Testing Account 10', '$2y$12$radpvBOkQky/zrz6ndddLOvDh3SnN6mMbU.gG7rzPzGVuW9Ql8Tte', NULL, 1, 'KONTOL', NULL, NULL, NULL, NULL, '2025-05-28 21:53:33', '2025-05-17 06:35:13'),
(29, NULL, 'testing_account11@aruna-dev.com', 'testing_account11', 'Testing Account 11', '$2y$12$V9QJvENrK6AKkOBFWsXpn.0yNIFLrwtEhRs.EsrbbyasBD2TfqDha', NULL, 1, 'KONTOL', NULL, NULL, NULL, NULL, '2025-05-28 21:54:43', '2025-05-17 06:35:42'),
(30, NULL, 'testing_account12@aruna-dev.com', 'u1923784', 'Testing Account 12', '$2y$12$zHwxBKncFaMcQG24lI099usB5MyaaWveJeLnSPBbci7UYaCJDTNYm', NULL, 1, 'KONTOL', NULL, NULL, NULL, NULL, '2025-05-28 21:55:39', '2025-05-17 06:36:04'),
(31, NULL, 'testing_account13@aruna-dev.com', 'testing_account13', 'Testing Account 13', '$2y$12$LzdK73yrUB/pqC.9/sdiL.mktY6tSlyeEF9IxAFifpQVP.zLqtx2i', NULL, 1, 'KONTOL', NULL, NULL, NULL, NULL, '2025-05-28 21:56:20', '2025-05-17 06:36:46'),
(32, NULL, 'testing_account14@aruna-dev.com', 'testing_account14', 'Testing Account 14', '$2y$12$z2MeG7Vjb4yPh9LdYtsvbevsmmxsRvZ3Ty9LuEiwgRDulOR7JQpk2', NULL, 1, 'KONTOL', NULL, NULL, NULL, NULL, '2025-05-28 22:03:04', '2025-05-17 06:37:44'),
(33, NULL, 'testing_account15@aruna-dev.com', 'testing_account15', 'Testing Account 15', '$2y$12$QjhdOfMIYDXJJXsxncdqWeFkPZBq6my8LbZEHu6m9P8T2jlu6Xwqa', NULL, 1, 'KONTOL', NULL, NULL, NULL, NULL, '2025-05-28 22:04:06', '2025-05-17 06:38:16'),
(34, NULL, 'testing_account16@aruna-dev.com', 'u2179306', 'Testing Account 16', '$2y$12$NDeok4SZ.IByeRpQqc5FgumNsXDuboVy1V2U1qEsQIYFGmEe.b6KG', NULL, 1, 'KONTOL', NULL, NULL, NULL, NULL, '2025-05-28 22:04:08', '2025-05-17 06:38:56'),
(35, NULL, 'testing_account17@aruna-dev.com', 'testing_account17', 'Testing Account 17', '$2y$12$ZzT4gUdE9xbb9eBH1KeKdOViVXJUx7nZDgCRv7X.bFvP7HvVeLOhC', NULL, 1, 'KONTOL', NULL, NULL, NULL, NULL, '2025-05-28 22:04:19', '2025-05-17 06:42:09'),
(36, NULL, 'testing_account18@aruna-dev.com', 'u9481230', 'Testing Account 18', '$2y$12$2gGsZm.E7d0J7XnD9XRUiujtNjYSjebDzv0uRmAwg1Hl7vEmvhlkC', NULL, 1, 'KONTOL', NULL, NULL, NULL, NULL, '2025-05-28 22:04:49', '2025-05-17 06:43:04'),
(37, NULL, 'testing_account19@aruna-dev.com', 'u8079542', 'Testing Account 19', '$2y$12$O7yF5O1SLgqJxSW2ECkPO.cWSGryRIjVoTrJYgy/k215bS9Y0zeSe', NULL, 1, 'KONTOL', NULL, NULL, NULL, NULL, '2025-05-28 22:05:40', '2025-05-17 06:45:21'),
(38, NULL, 'testing_account20@aruna-dev.com', 'testing_account20', 'Testing Account 20', '$2y$12$Sj0fltZc.CsaLGsHhcOIhuhgvKQASBLrksA/isaGonC72TkRkStjW', NULL, 1, 'KONTOL', NULL, NULL, NULL, NULL, '2025-05-28 22:05:43', '2025-05-17 08:33:33'),
(39, NULL, 'testing_account21@aruna-dev.com', 'testing_account21', 'Testing Account 21 Edited', '$2y$12$MYsOqiDkKm3wxbC1nUsShutCfxiFqb28FYPWWcVVitcCYe855/uR6', NULL, 1, 'KONTOL', NULL, NULL, NULL, NULL, '2025-05-28 22:05:55', '2025-05-17 08:35:28'),
(40, NULL, 'testing_account22@aruna-dev.com', 'testing_account22', 'Testing Account 22', '$2y$12$Bp6oN.io6Mrv/B77UGYrHucFbFzRuV0/9/MBdLLlxD72itTAXZkX.', NULL, 1, 'KONTOL', NULL, NULL, NULL, NULL, '2025-05-28 22:05:57', '2025-05-17 08:36:54'),
(41, NULL, 'testing_account23@aruna-dev.com', 'testing_account23', 'Testing Account 23 Edited', '$2y$12$9JCgU4YONyc.N/CvV8oVoukkHSvhnwZYTYU3.TlcJFecb7x2/HNZi', NULL, 1, NULL, NULL, NULL, NULL, NULL, '2025-05-17 08:41:04', '2025-05-17 08:37:40'),
(45, NULL, 'andhika96@aruna-dev.com', 'andhika96', 'Andhika 96', '$2y$12$xGoAcx6Syh74bq6lS48rpONsHsipW0QQaF.6IJHTbujZGW.U03Zzq', NULL, 1, NULL, NULL, NULL, NULL, NULL, '2025-06-14 14:57:02', '2025-05-26 01:22:08'),
(46, NULL, 'andhika06@aruna-dev.com', 'andhika06', 'Andhika 06', '$2y$12$OdFT1EpcGUXXtKmt0Okf0.BLi8s3P1jY/A8.LeJztGo7CgEuVkCni', NULL, 3, NULL, NULL, NULL, NULL, NULL, '2025-05-26 01:23:09', '2025-05-26 01:23:09'),
(47, NULL, 'andhika19@aruna-dev.com', 'andhika19', 'Andhika 19', '$2y$12$4Z0DsIv1lQotGmhGGOai3en8D0.Zgc6HOI8wd5sE9vqwdXnOOr/va', NULL, 3, NULL, NULL, NULL, NULL, NULL, '2025-05-26 01:25:42', '2025-05-26 01:25:42'),
(48, NULL, 'andhika1906@aruna-dev.com', 'andhika1906', 'Andhika 1906', '$2y$12$4rbaEpeMeSf3yUViFzNaZeTSJ9OLkDnVqZ/9ItU37p3GPoNNrxwfC', NULL, 3, NULL, NULL, NULL, NULL, '2025-05-26 01:30:23', '2025-05-26 01:30:23', '2025-05-26 01:30:23'),
(49, NULL, 'andhika1996@aruna-dev.com', 'andhika1996', 'Andhika 1996', '$2y$12$no/HZ8Mc1OuZ.S0RUeYa8.tIUTv25bHfcTSmbqWhZqoTB45b5vlMS', NULL, 1, NULL, NULL, NULL, NULL, NULL, '2025-05-26 01:31:37', '2025-05-26 01:31:37'),
(50, NULL, 'andhika.testing12@gmail.com', 'andhika.testing12', 'Andhika Testing 12', '$2y$12$dLhUpWwNVQHoZJPSlqEImOx3BiRu2xFOwqUbdzxfV1zqRuBxkPxQ.', NULL, 1, NULL, NULL, NULL, NULL, NULL, '2025-05-26 01:32:53', '2025-05-26 01:32:53'),
(58, NULL, 'testing_account100@aruna-dev.com', 'testing_account100', 'testing_account100', '$2y$12$/FB55JdKjeEPek0qlN5gSu2QPtiLto6Kn3hjTjb1Fs3Dzv9rLEN42', NULL, 1, NULL, NULL, NULL, NULL, NULL, '2025-07-25 02:43:23', '2025-07-21 09:07:02'),
(59, '8702522e-1c44-4ce8-80b5-118e4ea5ec59', 'testing_account101@aruna-dev.com', 'testing_account101', 'testing_account101', '$2y$12$CbqW/V0O/xWBgwI6qGBHfembAB6NSA0L8/v2MnMgEs7zBg6iv.LX6', NULL, 3, NULL, NULL, NULL, NULL, NULL, '2025-07-21 09:08:52', '2025-07-21 09:08:52'),
(60, 'd1c7bc73-00b2-4d5e-9d32-f97fd00e8cde', 'testing_account200@aruna-dev.com', 'testing_account200', 'Testing Account 200', 'LaraPhoenixDev', NULL, 1, NULL, NULL, NULL, NULL, NULL, '2025-08-04 08:17:32', '2025-08-04 08:17:32'),
(61, '16acd371-8cee-4c92-a1ea-8f6992e542c7', 'testing_account201@aruna-dev.com', 'testing_account201', 'Testing Account 201', '$2y$12$joOz3zFv4sARaU9nq2FzuO0zxpBCnhQ5e.Ex4/Q54jPJcL/ndn2JK', NULL, 3, NULL, NULL, NULL, NULL, NULL, '2025-08-04 08:33:07', '2025-08-04 08:33:07'),
(62, 'ed2ba545-4a4c-4a0e-8242-0898be01821b', 'testing_account202@aruna-dev.com', 'testing_account202', 'Testing Account 201', '$2y$12$AhBXnDH5AUwbecCE75IYDOu0aivtufyJdx5n8RmWKPP9vA4nOuNCq', NULL, 1, NULL, NULL, NULL, NULL, NULL, '2025-08-04 09:04:05', '2025-08-04 09:04:05'),
(63, 'e292e1a7-3196-4833-b28b-86866f2dbd8e', 'testing_account203@aruna-dev.com', 'testing_account203', 'Testing Account 203', 'LaraPhoenixDev', NULL, 1, NULL, NULL, NULL, NULL, NULL, '2025-08-04 09:31:25', '2025-08-04 09:31:25'),
(64, '78e5d738-6b45-47ea-88b1-756df2de6ea8', 'testing_account204@aruna-dev.com', 'testing_account204', 'Testing Account 204', '$2y$12$XIZ9iPiWmhupguXEVwpe8.ZKSFNzcWvGr.CT1A5VhsxeAYSnfE9O6', NULL, 1, NULL, NULL, NULL, NULL, NULL, '2025-08-04 09:36:22', '2025-08-04 09:35:26'),
(65, '184b9f6b-d7d3-4a31-8a52-d0c0dcc18788', 'asdasdasd', 'asdasd', 'asd', 'LaraPhoenixDev', NULL, 1, NULL, NULL, NULL, NULL, NULL, '2025-08-18 08:55:15', '2025-08-18 08:55:15'),
(66, 'cdab9b3a-edf9-43e5-8b2d-8e68fa2b0b4e', 'asdasd2', 'asdasd2', 'asdasd2', 'LaraPhoenixDev', NULL, 2, NULL, NULL, NULL, NULL, NULL, '2025-08-18 08:56:11', '2025-08-18 08:56:11'),
(67, 'dac7a758-06f0-465f-bc8a-53d0ce32fe72', 'andhikakw1@aruna-dev.com', 'andhikakw1', 'Andhika KW 1', '$2y$12$JHQhiOQngO6wR/eWT4lUCevzwclYhjeDu4UOuERMmKMozZEKy8aSW', NULL, 3, NULL, NULL, NULL, NULL, NULL, '2026-01-02 17:06:10', '2026-01-02 17:06:10');

-- --------------------------------------------------------

--
-- Table structure for table `lr_account_login_history`
--

CREATE TABLE `lr_account_login_history` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL DEFAULT '0',
  `ip_address` varchar(15) DEFAULT NULL,
  `browser` varchar(255) DEFAULT NULL,
  `login_at` varchar(155) DEFAULT NULL,
  `login_successful` varchar(5) DEFAULT NULL,
  `logout_at` varchar(155) DEFAULT NULL,
  `cleared_by_user` varchar(5) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lr_account_status`
--

CREATE TABLE `lr_account_status` (
  `id` int NOT NULL,
  `name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `code_name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `class_name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `is_active` tinyint NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lr_account_status`
--

INSERT INTO `lr_account_status` (`id`, `name`, `code_name`, `class_name`, `is_active`) VALUES
(1, 'Active', 'active', 'badge text-bg-success', 0),
(2, 'Not Active', 'not-active', 'badge text-bg-secondary', 0),
(3, 'Suspended', 'suspended', 'badge text-bg-danger', 0),
(4, 'Suspended Temporarily', 'suspended-temporarily', 'badge text-bg-warning', 0);

-- --------------------------------------------------------

--
-- Table structure for table `lr_articles`
--

CREATE TABLE `lr_articles` (
  `id` int NOT NULL,
  `uri` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `user_id` bigint DEFAULT '0',
  `category_id` bigint NOT NULL DEFAULT '0',
  `subcategory_id` bigint NOT NULL DEFAULT '0',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tags` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `thumb_s` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `thumb_l` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `visibility` enum('public','private','password_protected') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'public',
  `password_protected` varchar(32) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('publish','draft','pending','scheduled') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'draft',
  `scheduled` enum('true','false') COLLATE utf8mb4_general_ci DEFAULT 'false',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lr_articles`
--

INSERT INTO `lr_articles` (`id`, `uri`, `user_id`, `category_id`, `subcategory_id`, `title`, `content`, `tags`, `thumb_s`, `thumb_l`, `visibility`, `password_protected`, `status`, `scheduled`, `updated_at`, `created_at`) VALUES
(4, 'testing', 59, 1, 0, 'Testing', '<p>Hello World!</p>', NULL, NULL, NULL, 'private', NULL, 'publish', 'true', '2025-08-25 08:53:52', '2025-08-09 17:00:00'),
(5, 'testing-2', 59, 1, 0, 'Testing 2', '<p>Hello World!</p>', NULL, NULL, NULL, 'private', NULL, 'publish', 'true', '2025-08-25 08:53:52', '2025-08-30 17:00:00'),
(6, 'testing-2', 59, 1, 0, 'Testing 2', '<p>Hello World!</p>', NULL, NULL, NULL, 'private', NULL, 'pending', 'false', '2025-08-19 07:49:27', '2025-08-19 07:49:27'),
(7, 'testing-3', 59, 1, 0, 'Testing 3', '<p>ASDASDASD</p>', NULL, NULL, NULL, 'password_protected', 'QWERTY', 'pending', 'false', '2025-08-25 08:54:39', '2025-08-19 07:59:14'),
(8, 'testing-4', 1, 1, 0, 'Testing 4', '<p>ASDASDASD</p>', NULL, NULL, NULL, 'public', NULL, 'publish', 'true', '2025-08-21 18:49:24', '2025-08-31 03:05:00'),
(9, 'testing-4', 1, 1, 0, 'Testing 4', '<p>ASDASDASD</p>', NULL, NULL, NULL, 'public', NULL, 'publish', 'false', '2025-08-21 18:49:44', '2025-08-21 18:49:44'),
(10, 'testing-4', 1, 1, 0, 'Testing 4', '<p>ASDASDASD</p>', NULL, NULL, NULL, 'public', NULL, 'publish', 'true', '2025-08-21 18:50:12', '2025-08-28 13:00:00'),
(11, 'testing-4', 1, 1, 0, 'Testing 4', '<p>ASDASDASD</p>', NULL, NULL, NULL, 'password_protected', 'TESTING', 'publish', 'true', '2025-08-21 18:51:01', '2025-08-24 13:00:00'),
(12, 'testing-testing', 1, 1, 0, 'Testing Testing', '<p>ASDASDASD</p>', NULL, NULL, NULL, 'public', NULL, 'publish', 'false', '2025-08-21 19:05:59', '2025-08-21 19:05:59'),
(13, 'testing-testing-kwkwkwkwkwkw', 1, 1, 0, 'Testing Testing', '<p>ASDASDASD</p>', NULL, NULL, NULL, 'public', NULL, 'publish', 'false', '2025-08-21 19:06:25', '2025-08-21 19:06:25'),
(14, 'testing-testing-kwkwkwkwkwkw', 1, 1, 0, 'Testing Testing', '<p>ASDASDASD</p>', NULL, NULL, NULL, 'public', NULL, 'publish', 'false', '2025-08-21 19:18:55', '2025-08-21 19:18:55'),
(15, 'testing-testing-kwkwkwkwkwkw', 1, 1, 0, 'Testing Testing', '<p>ASDASDASD</p>', NULL, NULL, NULL, 'public', NULL, 'publish', 'false', '2025-08-21 19:22:42', '2025-08-21 19:22:42'),
(16, 'awdawd', 1, 1, 0, 'AWDAWD', '<p>AWDAWDAWDWD</p>', NULL, NULL, NULL, 'public', NULL, 'publish', 'false', '2025-08-21 19:22:57', '2025-08-21 19:22:57'),
(17, 'awdasdawdw', 1, 1, 0, 'AWDASDAWDW', '<p>ASDAWDAW</p>', NULL, NULL, NULL, 'public', NULL, 'publish', 'false', '2025-08-21 19:24:06', '2025-08-21 19:24:06'),
(18, 'awdasdawdw', 1, 1, 0, 'AWDASDAWDW', '<p>ASDAWDAW</p>', NULL, NULL, NULL, 'public', NULL, 'publish', 'false', '2025-08-21 19:28:49', '2025-08-21 19:28:49'),
(19, 'awdasdawdw', 1, 1, 0, 'AWDASDAWDW', '<p>ASDAWDAW</p>', NULL, NULL, NULL, 'public', NULL, 'publish', 'false', '2025-08-21 19:29:05', '2025-08-21 19:29:05'),
(28, 'awdasdawdw', 1, 1, 0, 'AWDASDAWDW', '<p>ASDAWDAW</p>', NULL, NULL, NULL, 'public', NULL, 'publish', 'false', '2025-08-21 19:44:42', '2025-08-21 19:44:42'),
(29, 'awdasdawdw', 1, 1, 0, 'AWDASDAWDW', '<p>ASDAWDAW</p>', NULL, NULL, NULL, 'public', NULL, 'publish', 'false', '2025-08-21 19:44:59', '2025-08-21 19:44:59'),
(35, 'awdasdawdw', 1, 1, 0, 'AWDASDAWDW', '<p>ASDAWDAW</p>', NULL, NULL, NULL, 'public', NULL, 'publish', 'false', '2025-08-21 19:58:37', '2025-08-21 19:58:36'),
(36, 'awdasdawdw', 1, 1, 0, 'AWDASDAWDW', '<p>ASDAWDAW</p>', NULL, NULL, NULL, 'public', NULL, 'publish', 'false', '2025-08-21 19:59:10', '2025-08-21 19:59:09'),
(37, 'kwkwkwkwkw', 1, 1, 0, 'KWKWKWKWKW', '<p>KWKWKWKWKW</p>', NULL, 'articles/082025/date_23/2ea4ad6d86de114d98fbe0205dc299e8_small.png', 'articles/082025/date_23/06e6b5ab768c902c9551efe627f62ce9.png', 'public', NULL, 'publish', 'false', '2025-08-22 20:59:59', '2025-08-22 20:59:58'),
(38, 'testing-testing-kwkwkwkwkwkw', 1, 1, 0, 'Testing Testing', '<p>ASDASDASD</p>', NULL, NULL, NULL, 'public', NULL, 'publish', 'false', '2025-08-21 19:06:25', '2025-08-21 19:06:25'),
(39, 'testing-testing-kwkwkwkwkwkw', 1, 1, 0, 'Testing Testing', '<p>ASDASDASD</p>', NULL, NULL, NULL, 'public', NULL, 'publish', 'false', '2025-08-21 19:18:55', '2025-08-21 19:18:55'),
(40, 'testing-testing-kwkwkwkwkwkw', 1, 1, 0, 'Testing Testing', '<p>ASDASDASD</p>', NULL, NULL, NULL, 'public', NULL, 'publish', 'false', '2025-08-21 19:22:42', '2025-08-21 19:22:42'),
(41, 'awdawd', 1, 1, 0, 'AWDAWD', '<p>AWDAWDAWDWD</p>', NULL, NULL, NULL, 'public', NULL, 'publish', 'false', '2025-08-21 19:22:57', '2025-08-21 19:22:57'),
(42, 'awdasdawdw', 1, 1, 0, 'AWDASDAWDW', '<p>ASDAWDAW</p>', NULL, NULL, NULL, 'public', NULL, 'publish', 'false', '2025-08-21 19:24:06', '2025-08-21 19:24:06'),
(43, 'testing-2', 59, 1, 0, 'Testing 2', '<p>Hello World!</p>', NULL, NULL, NULL, 'private', NULL, 'pending', 'true', '2025-08-19 07:49:17', '2025-08-30 17:00:00'),
(44, 'testing-2', 59, 1, 0, 'Testing 2', '<p>Hello World!</p>', NULL, NULL, NULL, 'private', NULL, 'pending', 'false', '2025-08-19 07:49:27', '2025-08-19 07:49:27'),
(45, 'testing-3', 59, 1, 0, 'Testing 3', '<p>ASDASDASD</p>', NULL, NULL, NULL, 'password_protected', 'QWERTY', 'publish', 'false', '2025-08-19 07:59:14', '2025-08-19 07:59:14'),
(46, 'testing-4', 1, 1, 0, 'Testing 4', '<p>ASDASDASD</p>', NULL, NULL, NULL, 'public', NULL, 'publish', 'true', '2025-08-21 18:49:24', '2025-08-31 03:05:00'),
(47, 'testing-4', 1, 1, 0, 'Testing 4', '<p>ASDASDASD</p>', NULL, NULL, NULL, 'password_protected', 'TESTING', 'publish', 'true', '2025-08-21 18:51:01', '2025-08-24 13:00:00'),
(48, 'testing-testing', 1, 1, 0, 'Testing Testing', '<p>ASDASDASD</p>', NULL, NULL, NULL, 'public', NULL, 'publish', 'false', '2025-08-21 19:05:59', '2025-08-21 19:05:59'),
(50, 'awdawd', 1, 1, 0, 'AWDAWD', '<p>AWDAWDAWDWD</p>', NULL, NULL, NULL, 'public', NULL, 'publish', 'false', '2026-01-07 09:29:15', '2025-08-21 19:22:57'),
(52, 'testing-2', 59, 1, 0, 'Testing 2', '<p>Hello World!</p>', NULL, NULL, NULL, 'private', NULL, 'pending', 'true', '2026-01-07 09:29:15', '2025-08-30 17:00:00'),
(53, 'testing-2', 59, 1, 0, 'Testing 2', '<p>Hello World!</p>', NULL, NULL, NULL, 'private', NULL, 'pending', 'false', '2026-01-07 09:29:15', '2025-08-19 07:49:27'),
(54, 'testing-3', 59, 1, 0, 'Testing 3', '<p>ASDASDASD</p>', NULL, NULL, NULL, 'password_protected', 'QWERTY', 'publish', 'false', '2026-01-07 09:29:15', '2025-08-19 07:59:14'),
(55, 'testing-4', 1, 1, 0, 'Testing 4', '<p>ASDASDASD</p>', NULL, NULL, NULL, 'public', NULL, 'publish', 'true', '2026-01-07 09:29:15', '2025-08-31 03:05:00'),
(56, 'testing-4', 1, 1, 0, 'Testing 4', '<p>ASDASDASD</p>', NULL, NULL, NULL, 'password_protected', 'TESTING', 'publish', 'true', '2026-01-07 09:24:38', '2025-08-24 13:00:00'),
(57, 'testing-testing', 1, 1, 0, 'Testing Testing', '<p>ASDASDASD</p>', NULL, NULL, NULL, 'public', NULL, 'publish', 'false', '2026-01-07 09:24:38', '2025-08-21 19:05:59'),
(58, 'testing-testing-kwkwkwkwkwkw', 1, 1, 0, 'Testing Testing', '<p>ASDASDASD</p>', NULL, NULL, NULL, 'public', NULL, 'publish', 'false', '2026-01-07 09:24:38', '2025-08-21 19:06:25');

-- --------------------------------------------------------

--
-- Table structure for table `lr_article_categories`
--

CREATE TABLE `lr_article_categories` (
  `id` bigint NOT NULL,
  `name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `code` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('active','inactive','hide') CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT 'active',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lr_article_categories`
--

INSERT INTO `lr_article_categories` (`id`, `name`, `code`, `status`, `updated_at`, `created_at`) VALUES
(1, 'Uncategorized', 'uncategorized', 'active', '2025-08-19 02:49:39', '2025-08-19 02:49:39'),
(4, 'Test 3', 'test-3', 'inactive', '2026-02-24 09:28:32', '2026-01-03 18:49:34'),
(5, 'Test 4', 'test-4', 'active', '2026-01-03 18:54:48', '2026-01-03 18:54:48'),
(6, 'Test 5', 'test-5', 'active', '2026-01-03 18:54:55', '2026-01-03 18:54:55');

-- --------------------------------------------------------

--
-- Table structure for table `lr_article_status`
--

CREATE TABLE `lr_article_status` (
  `id` int NOT NULL,
  `name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `code_name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `class_name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `is_active` tinyint NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lr_article_status`
--

INSERT INTO `lr_article_status` (`id`, `name`, `code_name`, `class_name`, `is_active`) VALUES
(1, 'Publish', 'publish', 'badge text-bg-success', 0),
(2, 'Draft', 'draft', 'badge text-bg-secondary', 0),
(3, 'Pending', 'pending', 'badge text-bg-info', 0),
(4, 'Scheduled', 'scheduled', 'badge text-bg-warning', 1);

-- --------------------------------------------------------

--
-- Table structure for table `lr_article_subcategories`
--

CREATE TABLE `lr_article_subcategories` (
  `id` bigint NOT NULL,
  `category_id` bigint NOT NULL DEFAULT '0',
  `name` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `code` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lr_authentication_log`
--

CREATE TABLE `lr_authentication_log` (
  `id` bigint UNSIGNED NOT NULL,
  `authenticatable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `authenticatable_id` bigint UNSIGNED NOT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `login_at` timestamp NULL DEFAULT NULL,
  `login_successful` tinyint(1) NOT NULL DEFAULT '0',
  `logout_at` timestamp NULL DEFAULT NULL,
  `cleared_by_user` tinyint(1) NOT NULL DEFAULT '0',
  `location` json DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lr_authentication_log`
--

INSERT INTO `lr_authentication_log` (`id`, `authenticatable_type`, `authenticatable_id`, `ip_address`, `user_agent`, `login_at`, `login_successful`, `logout_at`, `cleared_by_user`, `location`) VALUES
(1, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0', NULL, 0, '2025-07-30 03:06:57', 0, NULL),
(2, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0', '2025-07-30 03:31:42', 1, '2025-07-30 03:32:49', 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(3, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0', '2025-07-30 03:32:52', 1, '2025-07-30 03:34:17', 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(4, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0', '2025-07-30 03:34:19', 1, '2025-07-30 03:35:04', 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(5, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0', '2025-07-30 03:35:07', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(6, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0', '2025-07-31 02:07:18', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(7, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0', '2025-07-31 06:58:52', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(8, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0', '2025-08-01 02:26:20', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(9, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0', '2025-08-04 02:13:36', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(10, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0', '2025-08-04 04:53:45', 1, '2025-08-04 07:50:39', 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(11, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0', '2025-08-04 07:50:45', 1, '2025-08-04 07:55:02', 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(12, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0', '2025-08-04 07:55:05', 1, '2025-08-04 07:56:21', 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(13, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0', '2025-08-04 07:56:24', 1, '2025-08-04 07:56:48', 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(14, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0', '2025-08-04 07:56:51', 1, '2025-08-04 07:57:10', 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(15, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0', '2025-08-04 07:57:22', 1, '2025-08-04 07:58:19', 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(16, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0', '2025-08-04 07:58:22', 1, '2025-08-04 07:59:48', 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(17, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0', '2025-08-04 07:59:51', 1, '2025-08-04 08:10:21', 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(18, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0', '2025-08-04 08:10:31', 1, '2025-08-04 08:10:40', 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(19, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0', '2025-08-04 08:10:43', 1, '2025-08-04 08:11:14', 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(20, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0', '2025-08-04 08:11:25', 1, '2025-08-04 08:11:50', 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(21, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0', '2025-08-04 08:12:09', 1, '2025-08-04 08:12:38', 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(22, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0', '2025-08-04 08:12:42', 1, '2025-08-04 08:14:13', 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(23, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0', '2025-08-04 08:14:21', 1, '2025-08-04 08:32:44', 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(24, 'App\\Models\\Awesome_Admin\\Account', 61, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0', '2025-08-04 08:33:07', 1, '2025-08-04 08:34:02', 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(25, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0', '2025-08-04 08:34:05', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(26, 'App\\Models\\Awesome_Admin\\Account', 62, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-04 09:04:25', 1, '2025-08-04 09:15:01', 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(27, 'App\\Models\\Awesome_Admin\\Account', 62, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-04 09:15:05', 1, '2025-08-04 09:16:36', 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(28, 'App\\Models\\Awesome_Admin\\Account', 62, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-04 09:16:38', 1, '2025-08-04 09:19:32', 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(29, 'App\\Models\\Awesome_Admin\\Account', 62, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-04 09:19:34', 1, '2025-08-04 09:35:41', 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(30, 'App\\Models\\Awesome_Admin\\Account', 64, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-04 09:36:27', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(31, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0', '2025-08-05 03:04:14', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(32, 'App\\Models\\Awesome_Admin\\Account', 64, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 07:55:16', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(33, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0', '2025-08-06 02:37:43', 1, '2025-08-06 03:09:34', 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(34, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0', '2025-08-06 03:10:39', 1, '2025-08-06 03:51:45', 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(35, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0', '2025-08-06 03:57:51', 1, '2025-08-06 03:57:54', 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(36, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0', '2025-08-06 04:19:36', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(37, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0', '2025-08-07 06:35:11', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(38, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0', '2025-08-08 04:35:02', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(39, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0', '2025-08-08 10:33:26', 1, '2025-08-08 11:04:51', 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(40, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0', '2025-08-08 11:10:34', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(41, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0', '2025-08-09 12:50:04', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(42, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0', '2025-08-11 03:36:46', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(43, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0', '2025-08-12 02:07:16', 1, '2025-08-12 06:34:23', 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(44, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0', '2025-08-12 06:34:45', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(45, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0', '2025-08-13 02:22:29', 1, '2025-08-13 03:10:41', 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(46, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0', '2025-08-14 10:00:00', 1, '2025-08-14 10:01:29', 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(47, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0', '2025-08-18 08:07:11', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(48, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0', '2025-08-19 01:48:03', 1, '2025-08-19 04:57:26', 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(49, 'App\\Models\\Awesome_Admin\\Account', 59, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0', '2025-08-19 04:57:41', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(50, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0', '2025-08-21 16:35:23', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(51, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-08-22 08:53:20', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(52, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-08-22 14:53:21', 1, '2025-08-22 20:22:40', 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(53, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-08-22 20:23:20', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(54, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-08-23 06:40:53', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(55, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-08-25 06:22:39', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(56, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-08-28 08:05:52', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(57, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-08-29 07:17:45', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(58, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-01 06:36:38', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(59, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-02 03:14:45', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(60, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-03 02:06:56', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(61, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-03 07:04:43', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(62, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-10 03:53:39', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(63, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-12 03:37:06', 1, '2025-09-12 10:27:55', 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(64, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', '2025-09-18 08:02:45', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(65, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', '2025-09-23 09:08:20', 1, '2025-09-23 09:08:25', 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(66, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', '2025-09-23 09:08:37', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(67, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', '2025-09-25 02:58:47', 1, '2025-09-25 04:42:57', 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(68, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', '2025-09-25 08:06:42', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(69, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', '2025-09-26 03:20:34', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(70, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', '2025-09-28 09:33:44', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(71, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', '2025-10-03 06:55:04', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(72, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', '2025-10-06 08:27:35', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(73, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', '2025-10-07 03:40:50', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(74, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', '2025-10-07 06:23:33', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(75, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', '2025-10-08 02:45:36', 1, '2025-10-08 04:16:05', 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(76, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', '2025-10-10 06:34:28', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(77, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', '2025-10-15 04:43:49', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(78, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', '2025-10-15 07:46:06', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(79, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', '2025-10-16 08:23:57', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(80, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', '2025-10-17 02:31:17', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(81, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', '2025-10-18 08:31:43', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(82, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', '2025-10-18 11:27:06', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(83, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', '2025-10-22 02:02:35', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(84, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', '2025-10-22 08:41:19', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(85, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', '2025-10-23 03:03:08', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(86, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', '2025-10-23 06:16:48', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(87, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', '2025-10-24 05:56:09', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(88, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', '2025-10-27 06:32:12', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(89, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', '2025-10-28 07:52:05', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(90, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', '2025-10-29 02:52:54', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(91, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', '2025-10-29 07:07:57', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(92, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', '2025-10-30 03:28:07', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(93, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', '2025-10-30 07:00:27', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(94, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', '2025-10-31 04:26:24', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(95, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', '2025-11-03 03:21:40', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}');
INSERT INTO `lr_authentication_log` (`id`, `authenticatable_type`, `authenticatable_id`, `ip_address`, `user_agent`, `login_at`, `login_successful`, `logout_at`, `cleared_by_user`, `location`) VALUES
(96, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', '2025-11-03 06:57:02', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(97, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', '2025-11-04 07:10:18', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(98, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', '2025-11-06 05:43:51', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(99, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', '2025-11-07 02:57:24', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(100, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', '2025-11-07 06:29:09', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(101, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', '2025-11-07 15:55:24', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(102, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', '2025-11-08 12:15:12', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(103, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', '2025-11-08 15:54:19', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(104, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', '2025-11-10 03:27:07', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(105, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', '2025-11-10 07:01:23', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(106, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', '2025-11-14 03:02:43', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(107, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'PostmanRuntime/7.50.0', '2025-11-14 03:06:43', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(108, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'PostmanRuntime/7.49.1', '2025-11-14 04:16:55', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(109, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-11-14 05:24:03', 0, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(110, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-11-14 05:24:08', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(111, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-11-14 05:28:31', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(112, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'PostmanRuntime/7.49.1', '2025-11-14 06:07:39', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(113, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-11-15 21:39:21', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(114, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-11-16 06:22:56', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(115, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-11-17 08:28:11', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(116, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-11-19 03:01:16', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(117, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-11-24 09:03:44', 0, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(118, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-11-24 09:03:53', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(119, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-11-24 09:21:50', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(120, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-11-25 03:11:50', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(121, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-11-26 02:48:14', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(122, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'PostmanRuntime/7.49.1', '2025-11-27 03:34:11', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(123, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'PostmanRuntime/7.49.1', '2025-11-27 09:37:57', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(124, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-11-28 02:57:35', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(125, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-11-28 15:58:49', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(126, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-01 03:05:35', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(127, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-03 09:43:09', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(128, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-04 04:49:22', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(129, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-06 06:32:32', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(130, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-09 06:47:48', 1, '2025-12-09 06:48:08', 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(131, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0', '2025-12-09 06:48:10', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(132, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-09 06:48:36', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(133, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-12 13:36:10', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(134, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-16 08:48:30', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(135, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-17 03:43:28', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(136, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-18 06:05:54', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(137, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-20 16:10:51', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(138, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-21 15:40:12', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(139, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-22 21:26:09', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(140, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-23 06:17:12', 1, '2025-12-23 08:32:34', 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(141, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-23 08:32:36', 1, '2025-12-23 08:32:39', 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(142, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-23 08:33:34', 1, '2025-12-23 08:33:40', 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(143, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-23 08:33:55', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(144, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-24 02:26:47', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(145, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-24 09:17:20', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(146, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'PostmanRuntime/7.49.1', '2025-12-24 09:22:43', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(147, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-24 16:17:12', 1, '2025-12-24 16:39:39', 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(148, 'App\\Models\\Awesome_Admin\\Account', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-24 16:39:43', 1, '2025-12-24 16:40:35', 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(149, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-24 16:40:39', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(150, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-26 15:33:01', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(151, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-26 19:11:31', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(152, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-27 14:17:00', 1, '2025-12-27 14:19:02', 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(153, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-27 14:19:48', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(154, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-28 06:09:06', 1, '2025-12-28 06:11:04', 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(155, 'App\\Models\\Awesome_Admin\\Account', 1, '172.25.176.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-28 11:29:23', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(156, 'App\\Models\\Awesome_Admin\\Account', 1, '172.25.176.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-29 05:15:24', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(157, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-29 08:22:07', 1, '2025-12-29 08:22:35', 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(158, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-29 08:22:38', 1, '2025-12-29 08:22:42', 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(159, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-29 08:22:47', 0, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(160, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-29 08:22:51', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(161, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'PostmanRuntime/7.51.0', '2025-12-29 08:45:05', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(162, 'App\\Models\\Awesome_Admin\\Account', 1, '172.25.176.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-30 02:39:48', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(163, 'App\\Models\\Awesome_Admin\\Account', 1, '172.25.176.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-31 07:27:18', 1, '2025-12-31 07:33:06', 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(164, 'App\\Models\\Awesome_Admin\\Account', 1, '172.25.176.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2025-12-31 07:33:35', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(165, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2026-01-01 15:55:20', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(166, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2026-01-01 16:00:56', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(167, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2026-01-02 16:32:00', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(168, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'PostmanRuntime/7.51.0', '2026-01-02 16:41:18', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(169, 'App\\Models\\Awesome_Admin\\Account', 67, '127.0.0.1', 'PostmanRuntime/7.51.0', '2026-01-02 17:06:10', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(170, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2026-01-03 17:19:39', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(171, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2026-01-06 09:34:36', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(172, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2026-01-07 07:15:47', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(173, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2026-01-08 08:16:05', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(174, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', '2026-01-09 05:40:21', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(175, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:147.0) Gecko/20100101 Firefox/147.0', '2026-01-19 03:10:28', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(176, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:147.0) Gecko/20100101 Firefox/147.0', '2026-01-23 05:26:11', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(177, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'PostmanRuntime/7.51.1', '2026-02-09 09:18:13', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(178, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:147.0) Gecko/20100101 Firefox/147.0', '2026-02-16 08:38:27', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(179, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:147.0) Gecko/20100101 Firefox/147.0', '2026-02-18 04:24:38', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(180, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:147.0) Gecko/20100101 Firefox/147.0', '2026-02-18 06:26:11', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(181, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:147.0) Gecko/20100101 Firefox/147.0', '2026-02-18 06:52:49', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}'),
(182, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:147.0) Gecko/20100101 Firefox/147.0', '2026-02-24 09:27:43', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}');

-- --------------------------------------------------------

--
-- Table structure for table `lr_cache`
--

CREATE TABLE `lr_cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lr_cache_locks`
--

CREATE TABLE `lr_cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lr_cover_image`
--

CREATE TABLE `lr_cover_image` (
  `id` bigint NOT NULL,
  `user_id` bigint NOT NULL DEFAULT '0',
  `uri` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `cover_type` enum('background_image','slideshow') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'background_image',
  `cover_page_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'Unknown Page',
  `cover_bgimage_vars` text COLLATE utf8mb4_general_ci,
  `cover_slideshow_vars` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `cover_slideshow_direction` enum('horizontal','vertical') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'horizontal',
  `cover_slideshow_desktop_direction` enum('horizontal','vertical') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'horizontal',
  `cover_slideshow_mobile_direction` enum('horizontal','vertical') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'horizontal',
  `cover_autoplay_slideshow` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'active',
  `cover_autoplay_slideshow_interval` bigint DEFAULT '3000',
  `cover_looping_slideshow` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'active',
  `cover_is_active` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'active',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lr_cover_image`
--

INSERT INTO `lr_cover_image` (`id`, `user_id`, `uri`, `cover_type`, `cover_page_name`, `cover_bgimage_vars`, `cover_slideshow_vars`, `cover_slideshow_direction`, `cover_slideshow_desktop_direction`, `cover_slideshow_mobile_direction`, `cover_autoplay_slideshow`, `cover_autoplay_slideshow_interval`, `cover_looping_slideshow`, `cover_is_active`, `updated_at`, `created_at`) VALUES
(1, 1, 'homepage', 'background_image', 'Homepage', '[{\"cover_is_active\":\"active\",\"disable_content\":\"inactive\",\"background_overlay\":\"rgba(0, 0, 0, 0.4)\",\"background_size\":\"boi_size\",\"title\":\"Awal Cerita Keluarga Anda\",\"description\":null,\"button\":[{\"is_active\":\"inactive\",\"title\":\"Link 1\",\"link\":\"https:\\/\\/www.getbootstrap.com\"},{\"is_active\":\"inactive\",\"title\":\"Link 2\",\"link\":\"https:\\/\\/www.getbootstrap.com\"}],\"desktop_content_position\":\"center-left\",\"mobile_content_position\":\"center-left\",\"link\":{\"is_active\":\"inactive\",\"content\":null},\"countdown\":{\"is_active\":\"inactive\",\"content\":null,\"content_default\":null},\"desktop_image\":\"coverimage\\/012026\\/date_23\\/3591742f6fc2ca0efced9647c1b42b36.png\",\"mobile_image\":\"coverimage\\/012026\\/date_23\\/8ac740eb796bc50e5da11f04a7765a2e.png\"}]', NULL, 'horizontal', 'horizontal', 'horizontal', 'active', 3000, 'active', 'active', '2026-01-23 05:46:18', '2025-10-03 09:21:11'),
(2, 1, 'homepage2', 'slideshow', 'Homepage 2', '[{\"cover_is_active\":\"active\",\"disable_content\":\"inactive\",\"background_overlay\":\"rgba(0, 0, 0, 0.4)\",\"background_size\":\"md_size\",\"title\":\"Testing\",\"description\":null,\"button\":[{\"is_active\":\"active\",\"title\":null,\"link\":null},{\"is_active\":\"active\",\"title\":null,\"link\":null}],\"desktop_content_position\":\"center-center\",\"mobile_content_position\":\"center-center\",\"link\":{\"is_active\":\"inactive\",\"content\":null},\"countdown\":{\"is_active\":\"inactive\",\"content\":\"31\\/10\\/2025 15:50\",\"content_default\":\"Fri Oct 31 2025 15:50:00 GMT+0700 (Western Indonesia Time)\",\"desktop_position\":\"default\",\"mobile_position\":\"default\"},\"desktop_image\":\"coverimage\\/102025\\/date_07\\/8fd67759c6f8aeb74b22d1395045ed36.jpg\",\"mobile_image\":\"coverimage\\/102025\\/date_07\\/0385ffa38bf78201cde18367e2a5e75f.jpg\"}]', '[{\"cover_is_active\":\"active\",\"disable_content\":\"inactive\",\"background_overlay\":\"rgba(0, 0, 0, 0.4)\",\"background_size\":\"boi_size\",\"desktop_content_position\":\"top-left\",\"mobile_content_position\":\"top-right\",\"title\":\"Roboto\",\"description\":\"Delivering finest quality. Building excellence into interior spaces.\",\"second_content\":{\"is_active\":\"active\",\"type\":\"link\",\"desktop_position\":\"bottom-left\",\"mobile_position\":\"bottom-left\",\"text\":\"Explore Our Projects\",\"link\":\"https:\\/\\/www.getbootstrap.com\"},\"button\":[{\"is_active\":\"inactive\",\"title\":\"Link 1\",\"link\":null},{\"is_active\":\"inactive\",\"title\":\"Link 2\",\"link\":null}],\"link\":{\"is_active\":\"inactive\",\"content\":null},\"countdown\":{\"is_active\":\"inactive\",\"content\":\"01\\/11\\/2025 14:34\",\"content_default\":\"Sat Nov 01 2025 14:34:00 GMT+0700 (Western Indonesia Time)\",\"desktop_position\":\"default\",\"mobile_position\":\"bottom-left\"},\"desktop_image\":\"coverimage\\/102025\\/date_30\\/ce3c87f758de8f0cfb141b0faeb75566.jpg\",\"mobile_image\":\"coverimage\\/102025\\/date_30\\/9d5b2f66a84d54c4b9a21e8ad0553eac.jpg\"},{\"cover_is_active\":\"active\",\"disable_content\":\"inactive\",\"background_overlay\":\"rgba(0, 0, 0, 0.3)\",\"background_size\":\"boi_size\",\"desktop_content_position\":\"center-center\",\"mobile_content_position\":\"center-center\",\"title\":\"Testing 2\",\"description\":\"Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit\",\"second_content\":{\"is_active\":\"inactive\",\"type\":\"text\",\"desktop_position\":\"center-center\",\"mobile_position\":\"center-center\",\"text\":null},\"button\":[{\"is_active\":\"inactive\",\"title\":null,\"link\":null},{\"is_active\":\"inactive\",\"title\":null,\"link\":null}],\"link\":{\"is_active\":\"inactive\",\"content\":null},\"countdown\":{\"is_active\":\"active\",\"content\":\"02\\/11\\/2025 10:29\",\"content_default\":\"Sun Nov 02 2025 10:29:00 GMT+0700 (Western Indonesia Time)\",\"desktop_position\":\"default\",\"mobile_position\":\"bottom-left\"},\"desktop_image\":\"coverimage\\/102025\\/date_30\\/c6fc410401067f3874db73450b8ef623.jpg\",\"mobile_image\":\"coverimage\\/102025\\/date_30\\/823437a7ada4d459d3b5659057fa90aa.jpg\"},{\"cover_is_active\":\"active\",\"disable_content\":\"inactive\",\"background_overlay\":\"rgba(0, 0, 0, 0.3)\",\"background_size\":\"boi_size\",\"desktop_content_position\":\"center-center\",\"mobile_content_position\":\"center-center\",\"title\":\"Testing 3\",\"description\":\"Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit\",\"second_content\":{\"is_active\":\"inactive\",\"type\":\"text\",\"desktop_position\":\"center-center\",\"mobile_position\":\"center-center\",\"text\":null},\"button\":[{\"is_active\":\"inactive\",\"title\":null,\"link\":null},{\"is_active\":\"inactive\",\"title\":null,\"link\":null}],\"link\":{\"is_active\":\"inactive\",\"content\":null},\"countdown\":{\"is_active\":\"inactive\",\"content\":null,\"content_default\":\"Thu Oct 30 2025 10:29:46 GMT+0700 (Western Indonesia Time)\",\"desktop_position\":\"default\",\"mobile_position\":\"default\"},\"desktop_image\":\"coverimage\\/102025\\/date_30\\/9813eb551b9f57440f2c57e3ef3e5eff.jpg\",\"mobile_image\":\"coverimage\\/102025\\/date_30\\/0d177c940670dd0da538fde66325b946.jpg\"}]', 'horizontal', 'vertical', 'horizontal', 'inactive', 3000, 'inactive', 'active', '2025-12-16 09:09:50', '2025-10-03 09:27:50'),
(3, 1, 'homepage3', 'slideshow', 'Homepage 3', NULL, '[{\"cover_is_active\":\"active\",\"disable_content\":\"inactive\",\"background_overlay\":\"rgba(0, 0, 0, 0.4)\",\"desktop_content_position\":\"center-left\",\"mobile_content_position\":\"center-left\",\"title\":\"Testing 3\",\"description\":\"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque quis accumsan velit.\",\"second_content\":{\"is_active\":\"inactive\",\"type\":\"text\",\"desktop_position\":\"center-center\",\"mobile_position\":\"center-center\",\"text\":null},\"button\":[{\"is_active\":\"inactive\",\"title\":\"Link 1\",\"link\":\"https:\\/\\/getbootstrap.com\\/\"},{\"is_active\":\"inactive\",\"title\":\"Link 2\",\"link\":\"https:\\/\\/getbootstrap.com\\/\"}],\"link\":{\"is_active\":\"inactive\",\"content\":null},\"countdown\":{\"is_active\":\"inactive\",\"content\":null,\"content_default\":null},\"desktop_image\":\"coverimage\\/102025\\/date_03\\/656a2afa6abda8f41a08887e0df3ed7f.jpg\",\"mobile_image\":\"coverimage\\/102025\\/date_03\\/7366f0242acdbee6ab8aba9a25df7aa3.jpg\"},{\"cover_is_active\":\"active\",\"disable_content\":\"inactive\",\"background_overlay\":\"rgba(0, 0, 0, 0.4)\",\"desktop_content_position\":\"center-center\",\"mobile_content_position\":\"center-center\",\"title\":\"Testing 1\",\"description\":\"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque quis accumsan velit.\",\"second_content\":{\"is_active\":\"inactive\",\"type\":\"text\",\"desktop_position\":\"center-center\",\"mobile_position\":\"center-center\",\"text\":null},\"button\":[{\"is_active\":\"inactive\",\"title\":\"Link 1\",\"link\":\"https:\\/\\/getbootstrap.com\\/\"},{\"is_active\":\"inactive\",\"title\":\"Link 2\",\"link\":\"https:\\/\\/getbootstrap.com\\/\"}],\"link\":{\"is_active\":\"inactive\",\"content\":null},\"countdown\":{\"is_active\":\"inactive\",\"content\":null,\"content_default\":null},\"desktop_image\":\"coverimage\\/102025\\/date_03\\/f973602708afba76344ad7a8214573cb.jpg\",\"mobile_image\":\"coverimage\\/102025\\/date_03\\/082f986a432ad59fb8c84b871e014e37.jpg\"},{\"cover_is_active\":\"active\",\"disable_content\":\"inactive\",\"background_overlay\":\"rgba(0, 0, 0, 0.4)\",\"desktop_content_position\":\"center-right\",\"mobile_content_position\":\"center-right\",\"title\":\"Testing 2\",\"description\":\"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque quis accumsan velit.\",\"second_content\":{\"is_active\":\"inactive\",\"type\":\"text\",\"desktop_position\":\"center-center\",\"mobile_position\":\"center-center\",\"text\":null},\"button\":[{\"is_active\":\"inactive\",\"title\":\"Link 1\",\"link\":\"https:\\/\\/getbootstrap.com\\/\"},{\"is_active\":\"inactive\",\"title\":\"Link 2\",\"link\":\"https:\\/\\/getbootstrap.com\\/\"}],\"link\":{\"is_active\":\"inactive\",\"content\":null},\"countdown\":{\"is_active\":\"inactive\",\"content\":null,\"content_default\":null},\"desktop_image\":\"coverimage\\/102025\\/date_03\\/c5193b7c03561ee89048a73d7370811b.jpg\",\"mobile_image\":\"coverimage\\/102025\\/date_03\\/672a787b29de640ce7d8fa00cb81d5c5.jpg\"}]', 'horizontal', 'vertical', 'vertical', 'active', 3000, 'active', 'active', '2025-12-16 08:58:57', '2025-10-03 09:28:44'),
(4, 0, 'news', 'background_image', 'News', '[{\"cover_is_active\":\"active\",\"disable_content\":\"inactive\",\"background_overlay\":\"rgba(0, 0, 0, 0.3)\",\"background_size\":\"md_size\",\"title\":null,\"description\":null,\"button\":[{\"is_active\":\"inactive\",\"title\":null,\"link\":null},{\"is_active\":\"inactive\",\"title\":null,\"link\":null}],\"desktop_content_position\":\"center-center\",\"mobile_content_position\":\"center-center\",\"link\":{\"is_active\":\"inactive\",\"content\":null},\"countdown\":{\"is_active\":\"inactive\",\"content\":null,\"content_default\":\"Wed Oct 29 2025 16:37:55 GMT+0700 (Western Indonesia Time)\",\"desktop_position\":\"default\",\"mobile_position\":\"default\"}}]', NULL, 'horizontal', 'horizontal', 'horizontal', 'active', 3000, 'active', 'active', '2025-10-29 09:38:07', '2025-10-29 09:38:07');

-- --------------------------------------------------------

--
-- Table structure for table `lr_credits`
--

CREATE TABLE `lr_credits` (
  `id` bigint UNSIGNED NOT NULL,
  `creditable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `creditable_id` bigint UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `running_balance` decimal(10,2) NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lr_custom_permissions`
--

CREATE TABLE `lr_custom_permissions` (
  `id` bigint UNSIGNED NOT NULL,
  `role_id` bigint NOT NULL DEFAULT '0',
  `category_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `parent_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `menu_type` varchar(65) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `menu_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `menu_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `menu_link` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `permissions` json DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lr_custom_permissions`
--

INSERT INTO `lr_custom_permissions` (`id`, `role_id`, `category_code`, `parent_code`, `menu_type`, `menu_code`, `menu_name`, `menu_link`, `permissions`, `updated_at`, `created_at`) VALUES
(1, 1, 'uIxTa0lV3L4EaV9A6BvJ7x', 'hBMt85z8I4p3dgfZCt1sf4', 'single', 'hBMt85z8I4p3dgfZCt1sf4', 'Manage Articles', 'manage_article', '[\"read data\", \"add data\", \"edit data\", \"delete data\"]', '2025-10-22 04:17:10', '2025-10-22 04:17:10'),
(4, 1, 'uIxTa0lV3L4EaV9A6BvJ7x', 'ktGbOw0EloeZX73WIs50VO', 'single', 'ktGbOw0EloeZX73WIs50VO', 'Manage Cover Image', 'manage_coverimage', '[\"read data\", \"add data\", \"edit data\", \"delete data\"]', '2025-10-22 04:17:10', '2025-10-22 04:17:10'),
(5, 1, 'uIxTa0lV3L4EaV9A6BvJ7x', 'dqV84cjEjCrmp0BWF0fxpn', 'single', 'dqV84cjEjCrmp0BWF0fxpn', 'File Manager', 'filemanager', '[\"read data\", \"add data\", \"edit data\", \"delete data\"]', '2025-10-22 04:17:10', '2025-10-22 04:17:10'),
(9, 2, 'uIxTa0lV3L4EaV9A6BvJ7x', 'hBMt85z8I4p3dgfZCt1sf4', 'single', 'hBMt85z8I4p3dgfZCt1sf4', 'Manage Articles', 'manage_article', '[\"read data\", \"add data\", \"edit data\", \"delete data\"]', '2025-10-22 04:19:32', '2025-10-22 04:19:32'),
(12, 2, 'uIxTa0lV3L4EaV9A6BvJ7x', 'ktGbOw0EloeZX73WIs50VO', 'single', 'ktGbOw0EloeZX73WIs50VO', 'Manage Cover Image', 'manage_coverimage', '[\"read data\", \"add data\", \"edit data\", \"delete data\"]', '2025-10-22 04:19:32', '2025-10-22 04:19:32'),
(13, 2, 'uIxTa0lV3L4EaV9A6BvJ7x', 'dqV84cjEjCrmp0BWF0fxpn', 'single', 'dqV84cjEjCrmp0BWF0fxpn', 'File Manager', 'filemanager', '[\"read data\", \"add data\", \"edit data\", \"delete data\"]', '2025-10-22 04:19:32', '2025-10-22 04:19:32'),
(17, 3, 'uIxTa0lV3L4EaV9A6BvJ7x', 'hBMt85z8I4p3dgfZCt1sf4', 'single', 'hBMt85z8I4p3dgfZCt1sf4', 'Manage Articles', 'manage_article', NULL, '2025-10-22 04:19:45', '2025-10-22 04:19:45'),
(20, 3, 'uIxTa0lV3L4EaV9A6BvJ7x', 'ktGbOw0EloeZX73WIs50VO', 'single', 'ktGbOw0EloeZX73WIs50VO', 'Manage Cover Image', 'manage_coverimage', NULL, '2025-10-22 04:19:45', '2025-10-22 04:19:45'),
(21, 3, 'uIxTa0lV3L4EaV9A6BvJ7x', 'dqV84cjEjCrmp0BWF0fxpn', 'single', 'dqV84cjEjCrmp0BWF0fxpn', 'File Manager', 'filemanager', NULL, '2025-10-22 04:19:45', '2025-10-22 04:19:45'),
(28, 26, 'uIxTa0lV3L4EaV9A6BvJ7x', 'hBMt85z8I4p3dgfZCt1sf4', 'single', 'hBMt85z8I4p3dgfZCt1sf4', 'Manage Articles', 'manage_article', '[\"read data\"]', '2026-01-08 08:57:08', '2026-01-08 08:57:08'),
(31, 26, 'uIxTa0lV3L4EaV9A6BvJ7x', 'ktGbOw0EloeZX73WIs50VO', 'single', 'ktGbOw0EloeZX73WIs50VO', 'Manage Cover Image', 'manage_coverimage', '[\"read data\"]', '2026-01-08 08:57:08', '2026-01-08 08:57:08'),
(32, 26, 'uIxTa0lV3L4EaV9A6BvJ7x', 'dqV84cjEjCrmp0BWF0fxpn', 'single', 'dqV84cjEjCrmp0BWF0fxpn', 'File Manager', 'filemanager', '[\"read data\"]', '2026-01-08 08:57:08', '2026-01-08 08:57:08');

-- --------------------------------------------------------

--
-- Table structure for table `lr_failed_jobs`
--

CREATE TABLE `lr_failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lr_jobs`
--

CREATE TABLE `lr_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lr_jobs`
--

INSERT INTO `lr_jobs` (`id`, `queue`, `payload`, `attempts`, `reserved_at`, `available_at`, `created_at`) VALUES
(1, 'default', '{\"uuid\":\"b82ef127-470b-4c26-acbb-d837e26a83ec\",\"displayName\":\"Rappasoft\\\\LaravelAuthenticationLog\\\\Notifications\\\\NewDevice\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:32:\\\"App\\\\Models\\\\Awesome_Admin\\\\Account\\\";s:2:\\\"id\\\";a:1:{i:0;i:1;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:58:\\\"Rappasoft\\\\LaravelAuthenticationLog\\\\Notifications\\\\NewDevice\\\":2:{s:17:\\\"authenticationLog\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:59:\\\"Rappasoft\\\\LaravelAuthenticationLog\\\\Models\\\\AuthenticationLog\\\";s:2:\\\"id\\\";i:2;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"id\\\";s:36:\\\"2e3288c9-bda4-4946-91f8-80f0fea91750\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}}\"},\"createdAt\":1753846302,\"delay\":null}', 0, NULL, 1753846302, 1753846302),
(2, 'default', '{\"uuid\":\"f93ef374-eee0-4552-b83a-ca8da2240c68\",\"displayName\":\"Rappasoft\\\\LaravelAuthenticationLog\\\\Notifications\\\\NewDevice\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:32:\\\"App\\\\Models\\\\Awesome_Admin\\\\Account\\\";s:2:\\\"id\\\";a:1:{i:0;i:64;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:58:\\\"Rappasoft\\\\LaravelAuthenticationLog\\\\Notifications\\\\NewDevice\\\":2:{s:17:\\\"authenticationLog\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:59:\\\"Rappasoft\\\\LaravelAuthenticationLog\\\\Models\\\\AuthenticationLog\\\";s:2:\\\"id\\\";i:30;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"id\\\";s:36:\\\"132d506b-4023-4572-bd14-92e2602d664c\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}}\"},\"createdAt\":1754300187,\"delay\":null}', 0, NULL, 1754300187, 1754300187),
(3, 'default', '{\"uuid\":\"f2323b81-24ec-4c92-ab60-7543da2fd9fb\",\"displayName\":\"migrate:generate\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Foundation\\\\Console\\\\QueuedCommand\",\"command\":\"O:43:\\\"Illuminate\\\\Foundation\\\\Console\\\\QueuedCommand\\\":10:{s:7:\\\"\\u0000*\\u0000data\\\";a:3:{i:0;s:16:\\\"migrate:generate\\\";i:1;a:1:{s:10:\\\"--skip-log\\\";b:1;}i:2;O:27:\\\"App\\\\Support\\\\SseStreamOutput\\\":3:{s:50:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Output\\\\Output\\u0000verbosity\\\";i:32;s:50:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Output\\\\Output\\u0000formatter\\\";O:51:\\\"Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatter\\\":3:{s:59:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatter\\u0000styles\\\";a:4:{s:5:\\\"error\\\";O:56:\\\"Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatterStyle\\\":5:{s:63:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatterStyle\\u0000color\\\";O:31:\\\"Symfony\\\\Component\\\\Console\\\\Color\\\":3:{s:43:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Color\\u0000foreground\\\";s:2:\\\"37\\\";s:43:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Color\\u0000background\\\";s:2:\\\"41\\\";s:40:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Color\\u0000options\\\";a:0:{}}s:68:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatterStyle\\u0000foreground\\\";s:5:\\\"white\\\";s:68:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatterStyle\\u0000background\\\";s:3:\\\"red\\\";s:65:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatterStyle\\u0000options\\\";a:0:{}s:62:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatterStyle\\u0000href\\\";N;}s:4:\\\"info\\\";O:56:\\\"Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatterStyle\\\":5:{s:63:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatterStyle\\u0000color\\\";O:31:\\\"Symfony\\\\Component\\\\Console\\\\Color\\\":3:{s:43:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Color\\u0000foreground\\\";s:2:\\\"32\\\";s:43:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Color\\u0000background\\\";s:0:\\\"\\\";s:40:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Color\\u0000options\\\";a:0:{}}s:68:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatterStyle\\u0000foreground\\\";s:5:\\\"green\\\";s:68:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatterStyle\\u0000background\\\";s:0:\\\"\\\";s:65:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatterStyle\\u0000options\\\";a:0:{}s:62:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatterStyle\\u0000href\\\";N;}s:7:\\\"comment\\\";O:56:\\\"Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatterStyle\\\":5:{s:63:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatterStyle\\u0000color\\\";O:31:\\\"Symfony\\\\Component\\\\Console\\\\Color\\\":3:{s:43:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Color\\u0000foreground\\\";s:2:\\\"33\\\";s:43:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Color\\u0000background\\\";s:0:\\\"\\\";s:40:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Color\\u0000options\\\";a:0:{}}s:68:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatterStyle\\u0000foreground\\\";s:6:\\\"yellow\\\";s:68:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatterStyle\\u0000background\\\";s:0:\\\"\\\";s:65:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatterStyle\\u0000options\\\";a:0:{}s:62:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatterStyle\\u0000href\\\";N;}s:8:\\\"question\\\";O:56:\\\"Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatterStyle\\\":5:{s:63:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatterStyle\\u0000color\\\";O:31:\\\"Symfony\\\\Component\\\\Console\\\\Color\\\":3:{s:43:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Color\\u0000foreground\\\";s:2:\\\"30\\\";s:43:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Color\\u0000background\\\";s:2:\\\"46\\\";s:40:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Color\\u0000options\\\";a:0:{}}s:68:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatterStyle\\u0000foreground\\\";s:5:\\\"black\\\";s:68:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatterStyle\\u0000background\\\";s:4:\\\"cyan\\\";s:65:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatterStyle\\u0000options\\\";a:0:{}s:62:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatterStyle\\u0000href\\\";N;}}s:63:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatter\\u0000styleStack\\\";O:61:\\\"Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatterStyleStack\\\":2:{s:69:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatterStyleStack\\u0000styles\\\";a:0:{}s:73:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatterStyleStack\\u0000emptyStyle\\\";O:56:\\\"Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatterStyle\\\":5:{s:63:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatterStyle\\u0000color\\\";O:31:\\\"Symfony\\\\Component\\\\Console\\\\Color\\\":3:{s:43:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Color\\u0000foreground\\\";s:0:\\\"\\\";s:43:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Color\\u0000background\\\";s:0:\\\"\\\";s:40:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Color\\u0000options\\\";a:0:{}}s:68:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatterStyle\\u0000foreground\\\";s:0:\\\"\\\";s:68:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatterStyle\\u0000background\\\";s:0:\\\"\\\";s:65:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatterStyle\\u0000options\\\";a:0:{}s:62:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatterStyle\\u0000href\\\";N;}}s:62:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatter\\u0000decorated\\\";b:0;}s:53:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Output\\\\StreamOutput\\u0000stream\\\";i:0;}}s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\"},\"createdAt\":1755165135,\"delay\":null}', 0, NULL, 1755165135, 1755165135),
(4, 'default', '{\"uuid\":\"019bed36-75f2-421d-8af9-7d8e91fabc06\",\"displayName\":\"migrate:generate\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Foundation\\\\Console\\\\QueuedCommand\",\"command\":\"O:43:\\\"Illuminate\\\\Foundation\\\\Console\\\\QueuedCommand\\\":10:{s:7:\\\"\\u0000*\\u0000data\\\";a:3:{i:0;s:16:\\\"migrate:generate\\\";i:1;a:2:{s:10:\\\"--skip-log\\\";b:1;s:7:\\\"--queue\\\";s:7:\\\"default\\\";}i:2;O:27:\\\"App\\\\Support\\\\SseStreamOutput\\\":3:{s:50:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Output\\\\Output\\u0000verbosity\\\";i:32;s:50:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Output\\\\Output\\u0000formatter\\\";O:51:\\\"Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatter\\\":3:{s:59:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatter\\u0000styles\\\";a:4:{s:5:\\\"error\\\";O:56:\\\"Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatterStyle\\\":5:{s:63:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatterStyle\\u0000color\\\";O:31:\\\"Symfony\\\\Component\\\\Console\\\\Color\\\":3:{s:43:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Color\\u0000foreground\\\";s:2:\\\"37\\\";s:43:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Color\\u0000background\\\";s:2:\\\"41\\\";s:40:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Color\\u0000options\\\";a:0:{}}s:68:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatterStyle\\u0000foreground\\\";s:5:\\\"white\\\";s:68:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatterStyle\\u0000background\\\";s:3:\\\"red\\\";s:65:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatterStyle\\u0000options\\\";a:0:{}s:62:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatterStyle\\u0000href\\\";N;}s:4:\\\"info\\\";O:56:\\\"Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatterStyle\\\":5:{s:63:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatterStyle\\u0000color\\\";O:31:\\\"Symfony\\\\Component\\\\Console\\\\Color\\\":3:{s:43:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Color\\u0000foreground\\\";s:2:\\\"32\\\";s:43:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Color\\u0000background\\\";s:0:\\\"\\\";s:40:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Color\\u0000options\\\";a:0:{}}s:68:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatterStyle\\u0000foreground\\\";s:5:\\\"green\\\";s:68:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatterStyle\\u0000background\\\";s:0:\\\"\\\";s:65:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatterStyle\\u0000options\\\";a:0:{}s:62:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatterStyle\\u0000href\\\";N;}s:7:\\\"comment\\\";O:56:\\\"Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatterStyle\\\":5:{s:63:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatterStyle\\u0000color\\\";O:31:\\\"Symfony\\\\Component\\\\Console\\\\Color\\\":3:{s:43:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Color\\u0000foreground\\\";s:2:\\\"33\\\";s:43:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Color\\u0000background\\\";s:0:\\\"\\\";s:40:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Color\\u0000options\\\";a:0:{}}s:68:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatterStyle\\u0000foreground\\\";s:6:\\\"yellow\\\";s:68:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatterStyle\\u0000background\\\";s:0:\\\"\\\";s:65:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatterStyle\\u0000options\\\";a:0:{}s:62:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatterStyle\\u0000href\\\";N;}s:8:\\\"question\\\";O:56:\\\"Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatterStyle\\\":5:{s:63:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatterStyle\\u0000color\\\";O:31:\\\"Symfony\\\\Component\\\\Console\\\\Color\\\":3:{s:43:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Color\\u0000foreground\\\";s:2:\\\"30\\\";s:43:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Color\\u0000background\\\";s:2:\\\"46\\\";s:40:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Color\\u0000options\\\";a:0:{}}s:68:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatterStyle\\u0000foreground\\\";s:5:\\\"black\\\";s:68:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatterStyle\\u0000background\\\";s:4:\\\"cyan\\\";s:65:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatterStyle\\u0000options\\\";a:0:{}s:62:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatterStyle\\u0000href\\\";N;}}s:63:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatter\\u0000styleStack\\\";O:61:\\\"Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatterStyleStack\\\":2:{s:69:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatterStyleStack\\u0000styles\\\";a:0:{}s:73:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatterStyleStack\\u0000emptyStyle\\\";O:56:\\\"Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatterStyle\\\":5:{s:63:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatterStyle\\u0000color\\\";O:31:\\\"Symfony\\\\Component\\\\Console\\\\Color\\\":3:{s:43:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Color\\u0000foreground\\\";s:0:\\\"\\\";s:43:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Color\\u0000background\\\";s:0:\\\"\\\";s:40:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Color\\u0000options\\\";a:0:{}}s:68:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatterStyle\\u0000foreground\\\";s:0:\\\"\\\";s:68:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatterStyle\\u0000background\\\";s:0:\\\"\\\";s:65:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatterStyle\\u0000options\\\";a:0:{}s:62:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatterStyle\\u0000href\\\";N;}}s:62:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Formatter\\\\OutputFormatter\\u0000decorated\\\";b:0;}s:53:\\\"\\u0000Symfony\\\\Component\\\\Console\\\\Output\\\\StreamOutput\\u0000stream\\\";i:0;}}s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\"},\"createdAt\":1755165211,\"delay\":null}', 0, NULL, 1755165211, 1755165211),
(5, 'default', '{\"uuid\":\"fca8be56-f81c-4d5a-98f8-6745667da8c8\",\"displayName\":\"Rappasoft\\\\LaravelAuthenticationLog\\\\Notifications\\\\NewDevice\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:32:\\\"App\\\\Models\\\\Awesome_Admin\\\\Account\\\";s:2:\\\"id\\\";a:1:{i:0;i:59;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:58:\\\"Rappasoft\\\\LaravelAuthenticationLog\\\\Notifications\\\\NewDevice\\\":2:{s:17:\\\"authenticationLog\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:59:\\\"Rappasoft\\\\LaravelAuthenticationLog\\\\Models\\\\AuthenticationLog\\\";s:2:\\\"id\\\";i:49;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"id\\\";s:36:\\\"891c2339-ed08-4a07-9c35-2fe8c9082c41\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}}\"},\"createdAt\":1755579461,\"delay\":null}', 0, NULL, 1755579461, 1755579461),
(6, 'default', '{\"uuid\":\"d8518e43-8d22-4afb-8de9-368db0b64eb1\",\"displayName\":\"Rappasoft\\\\LaravelAuthenticationLog\\\\Notifications\\\\NewDevice\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:32:\\\"App\\\\Models\\\\Awesome_Admin\\\\Account\\\";s:2:\\\"id\\\";a:1:{i:0;i:1;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:58:\\\"Rappasoft\\\\LaravelAuthenticationLog\\\\Notifications\\\\NewDevice\\\":2:{s:17:\\\"authenticationLog\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:59:\\\"Rappasoft\\\\LaravelAuthenticationLog\\\\Models\\\\AuthenticationLog\\\";s:2:\\\"id\\\";i:51;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"id\\\";s:36:\\\"2dd1836c-2928-49ac-92bc-22db19d4bd55\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}}\"},\"createdAt\":1755852800,\"delay\":null}', 0, NULL, 1755852800, 1755852800),
(7, 'default', '{\"uuid\":\"d4089f1d-75d4-4e43-a442-bfef53688469\",\"displayName\":\"Rappasoft\\\\LaravelAuthenticationLog\\\\Notifications\\\\NewDevice\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:32:\\\"App\\\\Models\\\\Awesome_Admin\\\\Account\\\";s:2:\\\"id\\\";a:1:{i:0;i:1;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:58:\\\"Rappasoft\\\\LaravelAuthenticationLog\\\\Notifications\\\\NewDevice\\\":2:{s:17:\\\"authenticationLog\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:59:\\\"Rappasoft\\\\LaravelAuthenticationLog\\\\Models\\\\AuthenticationLog\\\";s:2:\\\"id\\\";i:64;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"id\\\";s:36:\\\"3ab6ddbd-9cf2-4d73-bcf0-028c617f7f6e\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}}\"},\"createdAt\":1758182565,\"delay\":null}', 0, NULL, 1758182565, 1758182565),
(8, 'default', '{\"uuid\":\"8788e25f-387a-473b-b70f-3cc02e753060\",\"displayName\":\"Rappasoft\\\\LaravelAuthenticationLog\\\\Notifications\\\\NewDevice\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:32:\\\"App\\\\Models\\\\Awesome_Admin\\\\Account\\\";s:2:\\\"id\\\";a:1:{i:0;i:1;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:58:\\\"Rappasoft\\\\LaravelAuthenticationLog\\\\Notifications\\\\NewDevice\\\":2:{s:17:\\\"authenticationLog\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:59:\\\"Rappasoft\\\\LaravelAuthenticationLog\\\\Models\\\\AuthenticationLog\\\";s:2:\\\"id\\\";i:81;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"id\\\";s:36:\\\"9fc82169-8329-4cf4-b4af-06db8b1e653b\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}}\"},\"createdAt\":1760776303,\"delay\":null}', 0, NULL, 1760776303, 1760776303),
(9, 'default', '{\"uuid\":\"1930531f-0e17-4e0e-8e7a-5c33ecb7d478\",\"displayName\":\"Rappasoft\\\\LaravelAuthenticationLog\\\\Notifications\\\\NewDevice\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:32:\\\"App\\\\Models\\\\Awesome_Admin\\\\Account\\\";s:2:\\\"id\\\";a:1:{i:0;i:1;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:58:\\\"Rappasoft\\\\LaravelAuthenticationLog\\\\Notifications\\\\NewDevice\\\":2:{s:17:\\\"authenticationLog\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:59:\\\"Rappasoft\\\\LaravelAuthenticationLog\\\\Models\\\\AuthenticationLog\\\";s:2:\\\"id\\\";i:107;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"id\\\";s:36:\\\"3fb0c6f0-9c35-4d2f-85ce-dd59ba45aa39\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}}\"},\"createdAt\":1763089603,\"delay\":null}', 0, NULL, 1763089603, 1763089603),
(10, 'default', '{\"uuid\":\"eed8a7e8-eb58-4511-942d-0028d9dfd797\",\"displayName\":\"Rappasoft\\\\LaravelAuthenticationLog\\\\Notifications\\\\NewDevice\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:32:\\\"App\\\\Models\\\\Awesome_Admin\\\\Account\\\";s:2:\\\"id\\\";a:1:{i:0;i:1;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:58:\\\"Rappasoft\\\\LaravelAuthenticationLog\\\\Notifications\\\\NewDevice\\\":2:{s:17:\\\"authenticationLog\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:59:\\\"Rappasoft\\\\LaravelAuthenticationLog\\\\Models\\\\AuthenticationLog\\\";s:2:\\\"id\\\";i:108;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"id\\\";s:36:\\\"0a1a55dc-e644-48cc-be0e-54ae1399537a\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}}\"},\"createdAt\":1763093815,\"delay\":null}', 0, NULL, 1763093815, 1763093815),
(11, 'default', '{\"uuid\":\"1f2e3d36-910d-48b1-84e5-04d145dcebde\",\"displayName\":\"Rappasoft\\\\LaravelAuthenticationLog\\\\Notifications\\\\NewDevice\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:32:\\\"App\\\\Models\\\\Awesome_Admin\\\\Account\\\";s:2:\\\"id\\\";a:1:{i:0;i:1;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:58:\\\"Rappasoft\\\\LaravelAuthenticationLog\\\\Notifications\\\\NewDevice\\\":2:{s:17:\\\"authenticationLog\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:59:\\\"Rappasoft\\\\LaravelAuthenticationLog\\\\Models\\\\AuthenticationLog\\\";s:2:\\\"id\\\";i:110;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"id\\\";s:36:\\\"64cdeceb-18ff-437d-b243-87e9b4db5589\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}}\"},\"createdAt\":1763097848,\"delay\":null}', 0, NULL, 1763097848, 1763097848),
(12, 'default', '{\"uuid\":\"9c611759-605e-4d0c-a36f-421c274cab01\",\"displayName\":\"Rappasoft\\\\LaravelAuthenticationLog\\\\Notifications\\\\NewDevice\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:32:\\\"App\\\\Models\\\\Awesome_Admin\\\\Account\\\";s:2:\\\"id\\\";a:1:{i:0;i:1;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:58:\\\"Rappasoft\\\\LaravelAuthenticationLog\\\\Notifications\\\\NewDevice\\\":2:{s:17:\\\"authenticationLog\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:59:\\\"Rappasoft\\\\LaravelAuthenticationLog\\\\Models\\\\AuthenticationLog\\\";s:2:\\\"id\\\";i:132;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"id\\\";s:36:\\\"ba922214-c40a-4807-a7e4-9c3f19317c1f\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}}\"},\"createdAt\":1765262917,\"delay\":null}', 0, NULL, 1765262917, 1765262917),
(13, 'default', '{\"uuid\":\"acea4fdd-9e17-4299-97c9-3dbaab986514\",\"displayName\":\"Rappasoft\\\\LaravelAuthenticationLog\\\\Notifications\\\\NewDevice\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:32:\\\"App\\\\Models\\\\Awesome_Admin\\\\Account\\\";s:2:\\\"id\\\";a:1:{i:0;i:1;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:58:\\\"Rappasoft\\\\LaravelAuthenticationLog\\\\Notifications\\\\NewDevice\\\":2:{s:17:\\\"authenticationLog\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:59:\\\"Rappasoft\\\\LaravelAuthenticationLog\\\\Models\\\\AuthenticationLog\\\";s:2:\\\"id\\\";i:133;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"id\\\";s:36:\\\"9eba87eb-3598-4752-bad1-c648b60fefc3\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}}\"},\"createdAt\":1765546570,\"delay\":null}', 0, NULL, 1765546570, 1765546570),
(14, 'default', '{\"uuid\":\"9a9c74c7-c4a2-4519-804a-21de329a0f54\",\"displayName\":\"Rappasoft\\\\LaravelAuthenticationLog\\\\Notifications\\\\NewDevice\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:32:\\\"App\\\\Models\\\\Awesome_Admin\\\\Account\\\";s:2:\\\"id\\\";a:1:{i:0;i:2;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:58:\\\"Rappasoft\\\\LaravelAuthenticationLog\\\\Notifications\\\\NewDevice\\\":2:{s:17:\\\"authenticationLog\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:59:\\\"Rappasoft\\\\LaravelAuthenticationLog\\\\Models\\\\AuthenticationLog\\\";s:2:\\\"id\\\";i:148;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"id\\\";s:36:\\\"fcba77d1-3286-454a-9771-08d349cd8d1d\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}}\"},\"createdAt\":1766594383,\"delay\":null}', 0, NULL, 1766594383, 1766594383),
(15, 'default', '{\"uuid\":\"ddbc27bf-f512-4310-a3d8-b9c1e1fe2df9\",\"displayName\":\"Rappasoft\\\\LaravelAuthenticationLog\\\\Notifications\\\\NewDevice\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:32:\\\"App\\\\Models\\\\Awesome_Admin\\\\Account\\\";s:2:\\\"id\\\";a:1:{i:0;i:1;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:58:\\\"Rappasoft\\\\LaravelAuthenticationLog\\\\Notifications\\\\NewDevice\\\":2:{s:17:\\\"authenticationLog\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:59:\\\"Rappasoft\\\\LaravelAuthenticationLog\\\\Models\\\\AuthenticationLog\\\";s:2:\\\"id\\\";i:155;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"id\\\";s:36:\\\"71bd3c03-ccf7-44e7-a587-93ed8b84a28c\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}}\"},\"createdAt\":1766921363,\"delay\":null}', 0, NULL, 1766921363, 1766921363),
(16, 'default', '{\"uuid\":\"5efa2122-0af8-4fe5-b61a-5b4da990c264\",\"displayName\":\"Rappasoft\\\\LaravelAuthenticationLog\\\\Notifications\\\\NewDevice\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:32:\\\"App\\\\Models\\\\Awesome_Admin\\\\Account\\\";s:2:\\\"id\\\";a:1:{i:0;i:1;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:58:\\\"Rappasoft\\\\LaravelAuthenticationLog\\\\Notifications\\\\NewDevice\\\":2:{s:17:\\\"authenticationLog\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:59:\\\"Rappasoft\\\\LaravelAuthenticationLog\\\\Models\\\\AuthenticationLog\\\";s:2:\\\"id\\\";i:161;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"id\\\";s:36:\\\"e2aa9a64-1d8d-469e-a7c5-72d8fa350681\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}}\"},\"createdAt\":1766997905,\"delay\":null}', 0, NULL, 1766997905, 1766997905),
(17, 'default', '{\"uuid\":\"aff21742-53af-4578-b3fa-3d263b6d6089\",\"displayName\":\"Rappasoft\\\\LaravelAuthenticationLog\\\\Notifications\\\\NewDevice\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:32:\\\"App\\\\Models\\\\Awesome_Admin\\\\Account\\\";s:2:\\\"id\\\";a:1:{i:0;i:1;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:58:\\\"Rappasoft\\\\LaravelAuthenticationLog\\\\Notifications\\\\NewDevice\\\":2:{s:17:\\\"authenticationLog\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:59:\\\"Rappasoft\\\\LaravelAuthenticationLog\\\\Models\\\\AuthenticationLog\\\";s:2:\\\"id\\\";i:175;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"id\\\";s:36:\\\"593b3d38-5448-45a3-a88b-7345cbea6fd9\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}}\"},\"createdAt\":1768792228,\"delay\":null}', 0, NULL, 1768792228, 1768792228),
(18, 'default', '{\"uuid\":\"c444f60f-bc68-4daa-bcc3-2c55c7c5da6a\",\"displayName\":\"Rappasoft\\\\LaravelAuthenticationLog\\\\Notifications\\\\NewDevice\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:32:\\\"App\\\\Models\\\\Awesome_Admin\\\\Account\\\";s:2:\\\"id\\\";a:1:{i:0;i:1;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:58:\\\"Rappasoft\\\\LaravelAuthenticationLog\\\\Notifications\\\\NewDevice\\\":2:{s:17:\\\"authenticationLog\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:59:\\\"Rappasoft\\\\LaravelAuthenticationLog\\\\Models\\\\AuthenticationLog\\\";s:2:\\\"id\\\";i:177;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"id\\\";s:36:\\\"243a06e2-2182-459a-b05f-71212d28a2d0\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}}\"},\"createdAt\":1770628693,\"delay\":null}', 0, NULL, 1770628693, 1770628693);

-- --------------------------------------------------------

--
-- Table structure for table `lr_job_batches`
--

CREATE TABLE `lr_job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lr_language`
--

CREATE TABLE `lr_language` (
  `id` int NOT NULL,
  `lang` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `lang_from` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `lang_to` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lr_language`
--

INSERT INTO `lr_language` (`id`, `lang`, `lang_from`, `lang_to`) VALUES
(1, 'english', 'Layouts', ''),
(2, 'english', 'Grid', ''),
(3, 'english', 'Widgets', ''),
(4, 'english', 'Advanced', ''),
(5, 'english', 'Container', ''),
(6, 'english', 'Row', ''),
(7, 'english', 'Column', ''),
(8, 'english', 'Nested Row', ''),
(9, 'english', 'IMAGE', ''),
(10, 'english', 'BUTTON', ''),
(11, 'english', 'List Group', ''),
(12, 'english', 'Media Heading', ''),
(13, 'english', 'Media Description', ''),
(14, 'english', 'Accordion Group', ''),
(15, 'english', 'ACCORDION', ''),
(16, 'english', 'Data Table', ''),
(17, 'english', 'Cols', ''),
(18, 'english', 'TABLE', ''),
(19, 'english', 'Video Player', ''),
(20, 'english', 'Spacer', ''),
(21, 'english', 'Height', ''),
(22, 'english', 'Divider', ''),
(23, 'english', 'Style', ''),
(24, 'english', 'Heading', ''),
(25, 'english', 'Media List Group', ''),
(26, 'english', 'Items', ''),
(27, 'english', 'MEDIA LIST', ''),
(28, 'english', 'Dynamic Post List', ''),
(29, 'english', 'Source', ''),
(30, 'english', 'Add Widget', ''),
(31, 'english', 'Link', ''),
(32, 'english', 'Title', ''),
(33, 'english', 'Description', ''),
(34, 'english', 'Collapsibles', ''),
(35, 'english', 'Container Empty. Click {1} above or drop row here.', ''),
(36, 'english', 'Add New Container Section', ''),
(37, 'english', 'Properties', ''),
(38, 'english', 'Select an element to edit properties', ''),
(39, 'english', 'Container Width', ''),
(40, 'english', 'Mobile', ''),
(41, 'english', 'Tablet', ''),
(42, 'english', 'Desktop', ''),
(43, 'english', 'Full HD', ''),
(44, 'english', '4K', ''),
(45, 'english', 'Layout', ''),
(46, 'english', 'Fixed Width', ''),
(47, 'english', 'Full Width', ''),
(48, 'english', 'Custom Class', ''),
(49, 'english', 'e.g p-4 m-4', ''),
(50, 'english', 'Appearance', ''),
(51, 'english', 'Background Color', ''),
(52, 'english', 'Background Size', ''),
(53, 'english', 'Cover', ''),
(54, 'english', 'Contain', ''),
(55, 'english', 'Auto', ''),
(56, 'english', 'Background Repeat', ''),
(57, 'english', 'No Repeat', ''),
(58, 'english', 'Repeat', ''),
(59, 'english', 'Repeat X', ''),
(60, 'english', 'Repeat Y', ''),
(61, 'english', 'Background Position', ''),
(62, 'english', 'Left Top', ''),
(63, 'english', 'Center Top', ''),
(64, 'english', 'Right Top', ''),
(65, 'english', 'Left Center', ''),
(66, 'english', 'Center Center', ''),
(67, 'english', 'Right Center', ''),
(68, 'english', 'Left Bottom', ''),
(69, 'english', 'Center Bottom', ''),
(70, 'english', 'Right Bottom', ''),
(71, 'english', 'Background Height', ''),
(72, 'english', 'Auto Height', ''),
(73, 'english', '25vh', ''),
(74, 'english', '50vh', ''),
(75, 'english', '75vh', ''),
(76, 'english', '100vh', ''),
(77, 'english', 'Row Width', ''),
(78, 'english', 'Width', ''),
(79, 'english', 'Gutter', ''),
(80, 'english', 'Default', ''),
(81, 'english', 'Column Width', ''),
(82, 'english', 'Common Settings', ''),
(83, 'english', 'Text Settings', ''),
(84, 'english', 'Click text in canvas to edit.', ''),
(85, 'english', 'Image Settings', ''),
(86, 'english', 'Button Settings', ''),
(87, 'english', 'Open New Tab', ''),
(88, 'english', 'Button Icon Settings', ''),
(89, 'english', 'Font Type', ''),
(90, 'english', 'No Icon', ''),
(91, 'english', 'Font Icon (Class)', ''),
(92, 'english', 'Custom Image', ''),
(93, 'english', 'Font Awesome Class', ''),
(94, 'english', 'e.g. fas fa-arrow-right', ''),
(95, 'english', 'Custom Icon with Image', ''),
(96, 'english', 'Icon URL', ''),
(97, 'english', 'Icon Position', ''),
(98, 'english', 'List Type', ''),
(99, 'english', 'Bulleted (UL)', ''),
(100, 'english', 'Numbered (OL)', ''),
(101, 'english', 'Standard', ''),
(102, 'english', 'Icon Class', ''),
(103, 'english', 'Image URL', ''),
(104, 'english', 'Card Image Settings', ''),
(105, 'english', 'e.g. p-3 rounded-circle', ''),
(106, 'english', 'Card Content', ''),
(107, 'english', 'Card Title', ''),
(108, 'english', 'Title Class (e.g. h4 text-danger)', ''),
(109, 'english', 'Truncate Title Options', ''),
(110, 'english', 'No Truncate', ''),
(111, 'english', 'Limit Chars', ''),
(112, 'english', 'Auto (1 Line)', ''),
(113, 'english', 'Body Text', ''),
(114, 'english', 'Text Class (e.g. small text-justify)', ''),
(115, 'english', 'Truncate Description Options', ''),
(116, 'english', 'chars', ''),
(117, 'english', 'Action Button', ''),
(118, 'english', 'Button Text', ''),
(119, 'english', 'Button Link', ''),
(120, 'english', 'Button Custom Class', ''),
(121, 'english', 'Button Custom Class (e.g. btn-sm)', ''),
(122, 'english', 'e.g. fas fa-chevron-right', ''),
(123, 'english', 'Left (Start)', ''),
(124, 'english', 'Right (End)', ''),
(125, 'english', 'e.g. rounded-circle shadow-sm border', ''),
(126, 'english', 'Alignment', ''),
(127, 'english', 'Image Left', ''),
(128, 'english', 'Image Right', ''),
(129, 'english', 'Media Content', ''),
(130, 'english', 'Heading Text', ''),
(131, 'english', 'Heading Class (e.g. h6 text-primary)', ''),
(132, 'english', 'Truncate Heading Options', ''),
(133, 'english', 'Content', ''),
(134, 'english', 'Content Class (e.g. small text-muted)', ''),
(135, 'english', 'Truncate Content Options', ''),
(136, 'english', 'Auto (Lines)', ''),
(137, 'english', 'lines', ''),
(138, 'english', 'Position', ''),
(139, 'english', 'Target', ''),
(140, 'english', 'Button Class', ''),
(141, 'english', 'Item', ''),
(142, 'english', 'Header', ''),
(143, 'english', 'Structure', ''),
(144, 'english', 'Edit Data', ''),
(145, 'english', 'Headers', ''),
(146, 'english', 'Video Source', ''),
(147, 'english', 'YouTube URL', ''),
(148, 'english', 'File Upload (CKFinder)', ''),
(149, 'english', 'YouTube Link', ''),
(150, 'english', 'Paste full YouTube URL here', ''),
(151, 'english', 'Video File', ''),
(152, 'english', 'Player Settings', ''),
(153, 'english', 'Controls', ''),
(154, 'english', 'Autoplay', ''),
(155, 'english', 'Loop', ''),
(156, 'english', 'Muted', ''),
(157, 'english', 'Aspect Ratio', ''),
(158, 'english', 'Heading Content', ''),
(159, 'english', 'HTML Tag', ''),
(160, 'english', 'Color', ''),
(161, 'english', 'Size', ''),
(162, 'english', 'Weight', ''),
(163, 'english', 'List Settings', ''),
(164, 'english', 'View Mode', ''),
(165, 'english', 'List', ''),
(166, 'english', 'GRID COLUMNS (RESPONSIVE)', ''),
(167, 'english', 'FHD', ''),
(168, 'english', '1 Column', ''),
(169, 'english', '2 Columns', ''),
(170, 'english', '3 Columns', ''),
(171, 'english', '4 Columns', ''),
(172, 'english', '5 Columns', ''),
(173, 'english', '6 Columns', ''),
(174, 'english', 'Border Style', ''),
(175, 'english', 'Full Card (Default)', ''),
(176, 'english', 'Text Area Only', ''),
(177, 'english', 'No Border', ''),
(178, 'english', 'Text Position', ''),
(179, 'english', 'Below Media', ''),
(180, 'english', 'Inside Media (Overlay)', ''),
(181, 'english', 'Fade Text on Hover', ''),
(182, 'english', 'Overlay Background', ''),
(183, 'english', 'rgba(0,0,0,0.3)', ''),
(184, 'english', 'Text Color', ''),
(185, 'english', 'Truncate Title', ''),
(186, 'english', 'Off (Full)', ''),
(187, 'english', 'ch', ''),
(188, 'english', 'Truncate Desc', ''),
(189, 'english', 'Gap (Spacing)', ''),
(190, 'english', '0 (No Gap)', ''),
(191, 'english', '1 (Small)', ''),
(192, 'english', '2 (Medium)', ''),
(193, 'english', '3 (Normal)', ''),
(194, 'english', '4 (Large)', ''),
(195, 'english', '5 (Extra Large)', ''),
(196, 'english', 'Min Height (Item)', ''),
(197, 'english', 'Set 0 for auto height.', ''),
(198, 'english', 'Rounded Corners', ''),
(199, 'english', 'Custom Classes', ''),
(200, 'english', 'Body Class', ''),
(201, 'english', 'e.g. p-4 text-center', ''),
(202, 'english', 'Title Class', ''),
(203, 'english', 'e.g. h4 text-primary', ''),
(204, 'english', 'Description Class', ''),
(205, 'english', 'e.g. small fst-italic', ''),
(206, 'english', 'Slider / Carousel', ''),
(207, 'english', 'Per Page follows Grid Columns.', ''),
(208, 'english', 'Slider Type', ''),
(209, 'english', 'Slide', ''),
(210, 'english', 'Fade', ''),
(211, 'english', 'Slider Direction', ''),
(212, 'english', 'Horizontal', ''),
(213, 'english', 'Vertical', ''),
(214, 'english', 'Slider per Move', ''),
(215, 'english', 'Auto Play', ''),
(216, 'english', 'Show Pagination', ''),
(217, 'english', 'Show Arrow', ''),
(218, 'english', 'Arrow Icon', ''),
(219, 'english', 'Custom Icon (using Fontawesome)', ''),
(220, 'english', 'Icon Left', ''),
(221, 'english', 'Left Position', ''),
(222, 'english', 'Icon Right', ''),
(223, 'english', 'Right Position', ''),
(224, 'english', 'Slider Position Y {1}', ''),
(225, 'english', 'Slider Position X {1}', ''),
(226, 'english', 'Items (Max 12)', ''),
(227, 'english', 'Video', ''),
(228, 'english', '16:9 (Landscape)', ''),
(229, 'english', '4:3 (Standard)', ''),
(230, 'english', '1:1 (Square)', ''),
(231, 'english', '9/16 (Vertical)', ''),
(232, 'english', '21:9 (Ultrawide)', ''),
(233, 'english', 'YouTube', ''),
(234, 'english', 'File Upload', ''),
(235, 'english', 'Video URL', ''),
(236, 'english', 'e.g. img-fluid rounded-circle', ''),
(237, 'english', 'IMAGE MODE', ''),
(238, 'english', 'Standard Image', ''),
(239, 'english', 'Background Image', ''),
(240, 'english', 'HEIGHT (PX)', ''),
(241, 'english', 'Center', ''),
(242, 'english', 'Top', ''),
(243, 'english', 'Bottom', ''),
(244, 'english', 'Left', ''),
(245, 'english', 'Right', ''),
(246, 'english', 'Data Source', ''),
(247, 'english', 'Data Type', ''),
(248, 'english', 'Status', ''),
(249, 'english', 'Categories', ''),
(250, 'english', 'Limit Per Page', ''),
(251, 'english', 'Use Pagination', ''),
(252, 'english', 'You can use slider, scroll to the bottom', ''),
(253, 'english', 'Show Date', ''),
(254, 'english', 'Show Category', ''),
(255, 'english', 'Show Text', ''),
(256, 'english', 'Standard Image (img)', ''),
(257, 'english', 'Background Image (div)', ''),
(258, 'english', 'Dimensions', ''),
(259, 'english', 'Background Image Properties', ''),
(260, 'english', 'Custom', ''),
(261, 'english', 'Grid View', ''),
(262, 'english', 'List View', ''),
(263, 'english', 'Image Alignment', ''),
(264, 'english', 'Text Alignment', ''),
(265, 'english', 'Content Alignment (Vertical)', ''),
(266, 'english', 'Middle', ''),
(267, 'english', 'Desktop Full HD', ''),
(268, 'english', 'Desktop 4K', ''),
(269, 'english', 'Inside Media {1}', ''),
(270, 'english', 'Gap {1}', ''),
(271, 'english', 'Full Card {1}', ''),
(272, 'english', 'Text Only', ''),
(273, 'english', 'None', ''),
(274, 'english', 'Min Height', ''),
(275, 'english', 'Main Body Class', ''),
(276, 'english', 'List Content Wrapper Class', ''),
(277, 'english', 'Content Settings', ''),
(278, 'english', 'Off', ''),
(279, 'english', '1 Line', ''),
(280, 'english', 'Limit', ''),
(281, 'english', 'Truncate Description', ''),
(282, 'english', 'Read More Button', ''),
(283, 'english', 'Button Wrapper Class', ''),
(284, 'english', 'Button Position', ''),
(285, 'english', 'Below Description', ''),
(286, 'english', 'Side (Opposite Image)', ''),
(287, 'english', '* Side position only works in List View', ''),
(288, 'english', 'Vertical Position', ''),
(289, 'english', 'Auto (Flow)', ''),
(290, 'english', 'Stick to Bottom', ''),
(291, 'english', 'Icon Settings', ''),
(292, 'english', 'Icon Source', ''),
(293, 'english', 'Enable Slider', ''),
(294, 'english', 'Select Widget', ''),
(295, 'english', 'Basic Widget', ''),
(296, 'english', 'Advanced Widget', ''),
(297, 'english', 'BASIC', ''),
(298, 'english', 'Rows', ''),
(299, 'english', 'Login', ''),
(300, 'english', 'Log In', ''),
(301, 'english', 'Enter your email address and password to the dashboard.', ''),
(302, 'english', 'Notice', ''),
(303, 'english', 'just now', ''),
(304, 'english', 'Enter your email', ''),
(305, 'english', 'Enter your password', ''),
(306, 'english', 'Remember me', ''),
(307, 'english', 'Create an account', ''),
(308, 'english', 'Forgot Password', ''),
(309, 'english', 'Email address required', ''),
(310, 'english', 'Password required', ''),
(311, 'english', 'User login successfully', ''),
(312, 'english', 'Dashboard', ''),
(313, 'english', 'All', ''),
(314, 'english', 'Visit Site', ''),
(315, 'english', 'Uncategorized', ''),
(316, 'english', 'Admin', ''),
(317, 'english', 'Admin Panel', ''),
(318, 'english', 'Collapsed View', ''),
(319, 'english', 'Logout', ''),
(320, 'english', 'Message', ''),
(321, 'english', 'Notification', ''),
(322, 'english', 'See All', ''),
(323, 'english', 'View Profile', ''),
(324, 'english', 'Account Settings', ''),
(325, 'english', 'Vertical Sidebar Menu successfully updated', ''),
(326, 'english', 'Manage Cover Image', ''),
(327, 'english', 'Search Cover Image by Page Name', ''),
(328, 'english', 'Add Cover Image', ''),
(329, 'english', 'Loading', ''),
(330, 'english', 'Page Name', ''),
(331, 'english', 'Page URI', ''),
(332, 'english', 'Cover Image Type', ''),
(333, 'english', 'Options', ''),
(334, 'english', 'Total Data', ''),
(335, 'english', 'Cancel', ''),
(336, 'english', 'Delete Data', ''),
(337, 'english', 'Do you really want to delete these data? {1} This process cannot be undone.', ''),
(338, 'english', 'No, keep it', ''),
(339, 'english', 'Yes, Delete', ''),
(340, 'english', 'Edit Cover Image', ''),
(341, 'english', 'Save', ''),
(342, 'english', 'Add New Form', ''),
(343, 'english', 'Submit', ''),
(344, 'english', 'Cover Page URI / Slug', ''),
(345, 'english', 'Cover Page Name', ''),
(346, 'english', 'Cover Type', ''),
(347, 'english', 'Only Background Image', ''),
(348, 'english', 'Slideshow', ''),
(349, 'english', 'Desktop Slideshow Direction', ''),
(350, 'english', 'Mobile Slideshow Direction', ''),
(351, 'english', 'Activate Autoplay Slideshow', ''),
(352, 'english', 'Active', ''),
(353, 'english', 'Inactive', ''),
(354, 'english', 'Autoplay Slideshow Interval', ''),
(355, 'english', 'Activate Looping Slideshow', ''),
(356, 'english', 'Main Settings', ''),
(357, 'english', 'Is Active Slideshow?', ''),
(358, 'english', 'Disable Content', ''),
(359, 'english', 'Main Contents', ''),
(360, 'english', 'Desktop Image', ''),
(361, 'english', 'Mobile Image', ''),
(362, 'english', 'Background Overlay', ''),
(363, 'english', 'Small Size', ''),
(364, 'english', 'Medium Size', ''),
(365, 'english', 'Large Size', ''),
(366, 'english', 'Fullscreen Size', ''),
(367, 'english', 'Based on Image Size', ''),
(368, 'english', 'Caption', ''),
(369, 'english', 'Disable Button 1', ''),
(370, 'english', 'Title Button {1}', ''),
(371, 'english', 'Link Button {1}', ''),
(372, 'english', 'Disable Button 2', ''),
(373, 'english', 'Content Positions', ''),
(374, 'english', 'Desktop Content Position', ''),
(375, 'english', 'Top Center', ''),
(376, 'english', 'Bottom Center', ''),
(377, 'english', 'Top Left', ''),
(378, 'english', 'Center Left', ''),
(379, 'english', 'Bottom Left', ''),
(380, 'english', 'Top Right', ''),
(381, 'english', 'Center Right', ''),
(382, 'english', 'Bottom Right', ''),
(383, 'english', 'Mobile Content Position', ''),
(384, 'english', 'Link for Content', ''),
(385, 'english', 'Is Active Link for Image?', ''),
(386, 'english', 'Link for Image?', ''),
(387, 'english', 'Countdown for Content', ''),
(388, 'english', 'Is Active Countdown for Image?', ''),
(389, 'english', 'Countdown for Image', ''),
(390, 'english', 'Countdown Desktop Position', ''),
(391, 'english', 'Countdown Mobile Position', ''),
(392, 'english', 'Second Contents', ''),
(393, 'english', 'Is Active', ''),
(394, 'english', 'Content Type', ''),
(395, 'english', 'Text', ''),
(396, 'english', 'Desktop Position', ''),
(397, 'english', 'Mobile Position', ''),
(398, 'english', 'Link Title', ''),
(399, 'english', 'Homepage', ''),
(400, 'english', 'URI or Slug required', ''),
(401, 'english', 'The URI or Slug is already in use by another cover image', ''),
(402, 'english', 'Cover type required', ''),
(403, 'english', 'Cover page name required', ''),
(404, 'english', 'CoverImage edited successfully', ''),
(405, 'english', 'File Manager', ''),
(406, 'english', 'Awesome Admin', ''),
(407, 'english', 'Pagination Type', ''),
(408, 'english', 'Numbering (Default)', ''),
(409, 'english', 'Simple (Prev / Next)', ''),
(410, 'english', 'Cursor / Load More', ''),
(411, 'english', 'Pagination Style', ''),
(412, 'english', 'Container Class', ''),
(413, 'english', 'Page Item Class', ''),
(414, 'english', 'Arrow Icons', ''),
(415, 'english', 'Space Between', ''),
(416, 'english', 'Show Icon', ''),
(417, 'english', 'Border Bottom Only', ''),
(418, 'english', 'Pagination Settings', ''),
(419, 'english', 'Pagination Alignment', ''),
(420, 'english', 'Awesome Admin Panel', ''),
(421, 'english', 'Site Settings', ''),
(422, 'english', 'Manage Menu Access', ''),
(423, 'english', 'Manage Users', ''),
(424, 'english', 'Manage Roles', ''),
(425, 'english', 'Manage Permissions', ''),
(426, 'english', 'SMTP Settings', ''),
(427, 'english', 'Manage Languages', ''),
(428, 'english', 'Manage Appearance', ''),
(429, 'english', 'Manage Menu', ''),
(430, 'english', 'Category Menu', ''),
(431, 'english', 'Parent Menu', ''),
(432, 'english', 'Submenu', ''),
(433, 'english', 'Manage Parent Menu', ''),
(434, 'english', 'List of Parent Menu', ''),
(435, 'english', 'No Menu', ''),
(436, 'english', 'View detail', ''),
(437, 'english', 'Remove', ''),
(438, 'english', 'Select Category Menu', ''),
(439, 'english', 'Parent Menu Name', ''),
(440, 'english', 'Parent Menu Link', ''),
(441, 'english', 'Is For Parent Menu', ''),
(442, 'english', 'Select', ''),
(443, 'english', 'Single Menu', ''),
(444, 'english', 'Parent Page Type', ''),
(445, 'english', 'Parent Menu Icon Type', ''),
(446, 'english', 'Upload File', ''),
(447, 'english', 'Custom Input', ''),
(448, 'english', 'Parent Menu Icon', ''),
(449, 'english', 'View image', ''),
(450, 'english', 'Parent Menu Icon Custom', ''),
(451, 'english', 'Add Single or Parent Menu', ''),
(452, 'english', 'Create', ''),
(453, 'english', 'Parent Menu Type', ''),
(454, 'english', 'Select Parent Type', ''),
(455, 'english', 'Page', ''),
(456, 'english', 'Input Custom Icon', ''),
(457, 'english', 'Upload Parent Menu Icon', ''),
(458, 'english', 'Delete Parent Menu', ''),
(459, 'english', 'Manage Submenu', ''),
(460, 'english', 'Add Submenu', ''),
(461, 'english', 'Select Parent Menu', ''),
(462, 'english', 'Add', ''),
(463, 'english', 'Delete', ''),
(464, 'english', 'Delete Submenu', ''),
(465, 'english', 'Detail', ''),
(466, 'english', 'Manage Submenu Detail', ''),
(467, 'english', 'List of Submenu', ''),
(468, 'english', 'Menu Name', ''),
(469, 'english', 'Menu Link', ''),
(470, 'english', 'Menu Type', ''),
(471, 'english', 'Menu Icon Type', ''),
(472, 'english', 'Menu Icon', ''),
(473, 'english', 'Menu Icon Custom', ''),
(474, 'english', 'Submenu Name', ''),
(475, 'english', 'Submenu Type', ''),
(476, 'english', 'Select Submenu Type', ''),
(477, 'english', 'Submenu Link', ''),
(478, 'english', 'Upload Submenu Icon', ''),
(479, 'english', 'Submenu name required', ''),
(480, 'english', 'Submenu type required', ''),
(481, 'english', 'Submenu link required', ''),
(482, 'english', 'Submenu roles link required', ''),
(483, 'english', 'Link can be added', ''),
(484, 'english', 'Data successfully updated', ''),
(485, 'english', 'New Role', ''),
(486, 'english', 'User{1}s', ''),
(487, 'english', 'User', ''),
(488, 'english', 'Edit Role', ''),
(489, 'english', 'Delete Role', ''),
(490, 'english', 'Edit Role & Permissions', ''),
(491, 'english', 'Edit', ''),
(492, 'english', 'Role Name', ''),
(493, 'english', 'Select All', ''),
(494, 'english', 'Menu Access', ''),
(495, 'english', 'Role name required', ''),
(496, 'english', 'Manage Article', ''),
(497, 'english', 'Search article by Title', ''),
(498, 'english', 'Add Post', ''),
(499, 'english', 'Change Status', ''),
(500, 'english', 'Filter By Status', ''),
(501, 'english', 'Filter By Category', ''),
(502, 'english', 'Filter By Scheduled', ''),
(503, 'english', 'Scheduled', ''),
(504, 'english', 'No Scheduled', ''),
(505, 'english', 'Author', ''),
(506, 'english', 'Date', ''),
(507, 'english', 'Unknown', ''),
(508, 'english', 'The email address or password you entered is incorrect, please try again', ''),
(509, 'english', 'Signup', ''),
(510, 'english', 'Don\\\'t have an account? Create your account, it takes less than a minute', ''),
(511, 'english', 'Email Address', ''),
(512, 'english', 'Username', ''),
(513, 'english', 'Fullname', ''),
(514, 'english', 'Password', ''),
(515, 'english', 'I accept Terms and Conditions', ''),
(516, 'english', 'Already have account?', ''),
(517, 'english', '{1} not available', ''),
(518, 'english', 'The email address is already in use by another user', ''),
(519, 'english', 'Username required', ''),
(520, 'english', 'The username is already used by another user', ''),
(521, 'english', 'Fullname required', ''),
(522, 'english', 'The minimal password length is 6', ''),
(523, 'english', 'Only alphabet, number and underscore is allowed', ''),
(524, 'english', 'Enter your email address and we will send you an email with instructions to reset your password.', ''),
(525, 'english', 'Email not found in any account', ''),
(526, 'english', 'Slug', ''),
(527, 'english', 'Permalink', ''),
(528, 'english', 'You can customize the post link here or leave it blank.', ''),
(529, 'english', 'Publish', ''),
(530, 'english', 'Draft', ''),
(531, 'english', 'Pending', ''),
(532, 'english', 'Ok', ''),
(533, 'english', 'Visibility', ''),
(534, 'english', 'Public', ''),
(535, 'english', 'Private', ''),
(536, 'english', 'Password Protected', ''),
(537, 'english', 'Immediately', ''),
(538, 'english', 'Time', ''),
(539, 'english', 'Save as Draft', ''),
(540, 'english', 'Thumbnail', ''),
(541, 'english', 'Edit Post', ''),
(542, 'english', 'General', ''),
(543, 'english', 'Birthdate', ''),
(544, 'english', 'Gender', ''),
(545, 'english', 'Select Gender', ''),
(546, 'english', 'Male', ''),
(547, 'english', 'Female', ''),
(548, 'english', 'Phone Number', ''),
(549, 'english', 'Enter New Password', ''),
(550, 'english', 'Re-type New Password', ''),
(551, 'english', 'Update user successfully', ''),
(552, 'english', 'Manage Site Config', ''),
(553, 'english', 'General Settings', ''),
(554, 'english', 'Site Name', ''),
(555, 'english', 'Site Slogan', ''),
(556, 'english', 'Site Keyword', ''),
(557, 'english', 'Site Description', ''),
(558, 'english', 'Font Family', ''),
(559, 'english', 'Font Size', ''),
(560, 'english', 'Site Thumbnail', ''),
(561, 'english', 'Privacy & Security Settings', ''),
(562, 'english', 'Management Menu Version', ''),
(563, 'english', 'Select Version', ''),
(564, 'english', 'Management Menu v1', ''),
(565, 'english', 'Management Menu v2', ''),
(566, 'english', 'Information', ''),
(567, 'english', 'Management Menu v1 uses the built-in feature of Spatie Laravel Permission, where permissions must be assigned to Roles first for Menus and Users.', ''),
(568, 'english', 'Management Menu v2 is a custom feature that allows permissions to be directly assigned to the menu when creating a Role.', ''),
(569, 'english', 'Site Registration Settings', ''),
(570, 'english', 'Site Maintenance Settings', ''),
(571, 'english', 'Offline Reason', ''),
(572, 'english', 'Time Rate Limit Global in Second', ''),
(573, 'english', 'Please input time in second', ''),
(574, 'english', 'You can set time in second for duration rate limit', ''),
(575, 'english', 'Enable Rate Limit Login', ''),
(576, 'english', 'Enable', ''),
(577, 'english', 'Please input integer 10-999 or until 3 digits', ''),
(578, 'english', 'You can limit login requests per IP Address per minute if a user fails to login', ''),
(579, 'english', 'Enable Rate Limit Signup', ''),
(580, 'english', 'You can limit signup requests per IP Address per minute if a user fails to login', ''),
(581, 'english', 'reCAPTCHA Settings', ''),
(582, 'english', 'reCAPTCHA Site Key', ''),
(583, 'english', 'Please input reCAPTCHA Site Key', ''),
(584, 'english', 'reCAPTCHA Secret Key', ''),
(585, 'english', 'Please input reCAPTCHA Secret Key', ''),
(586, 'english', 'Enable reCAPTCHA Login', ''),
(587, 'english', 'You must setup reCAPTCHA key first before activate this option', ''),
(588, 'english', 'Enable reCAPTCHA Signup', ''),
(589, 'english', 'Manage User', ''),
(590, 'english', 'Add New User', ''),
(591, 'english', 'Change Role', ''),
(592, 'english', 'Select Day', ''),
(593, 'english', '1 Day', ''),
(594, 'english', '3 Days', ''),
(595, 'english', '7 Days', ''),
(596, 'english', '14 Days', ''),
(597, 'english', '30 Days', ''),
(598, 'english', '2 Months', ''),
(599, 'english', '3 Months', ''),
(600, 'english', '6 Months', ''),
(601, 'english', 'Search user by Email & Fullname', ''),
(602, 'english', 'ID', ''),
(603, 'english', 'Roles', ''),
(604, 'english', 'Unknown status, please re-sett status this account', ''),
(605, 'english', 'Autofill', ''),
(606, 'english', 'Role', ''),
(607, 'english', 'Set Password Manually', ''),
(608, 'english', 'Automatic Set Password', ''),
(609, 'english', 'Default Password is {1}', ''),
(610, 'english', 'Loading ...', ''),
(611, 'english', 'Edit User', ''),
(612, 'english', 'Appearance Settings', ''),
(613, 'english', 'Color Nuances', ''),
(614, 'english', 'This theme doesn\\\'t support color nuances', ''),
(615, 'english', 'Interface {1} Theme', ''),
(616, 'english', 'Select or customize your UI theme', ''),
(617, 'english', 'Manage Language', ''),
(618, 'english', 'Untranslated', ''),
(619, 'english', 'Translated', ''),
(620, 'english', 'Manage Translated', ''),
(621, 'english', 'Language', ''),
(622, 'english', 'Language From', ''),
(623, 'english', 'Language To', ''),
(624, 'english', 'Status Save', ''),
(625, 'english', 'Manage Untranslated', ''),
(626, 'english', 'Manage Permission', ''),
(627, 'english', 'Add New Permission', ''),
(628, 'english', 'Permission Name', ''),
(629, 'english', 'Edit Permission', ''),
(630, 'english', 'You can edit permission here', ''),
(631, 'english', 'Delete Permission', ''),
(632, 'english', 'STMP Settings', ''),
(633, 'english', 'New SMTP', ''),
(634, 'english', 'Add New SMTP', ''),
(635, 'english', 'SMTP Service', ''),
(636, 'english', 'SMTP Host', ''),
(637, 'english', 'SMTP Username', ''),
(638, 'english', 'SMTP Password', ''),
(639, 'english', 'SMTP Port', ''),
(640, 'english', 'SMTP Encryption', ''),
(641, 'english', 'SSL', ''),
(642, 'english', 'TLS', ''),
(643, 'english', 'SMTP Sender Name', ''),
(644, 'english', 'SMTP Sender Address', ''),
(645, 'english', 'Edit SMTP', ''),
(646, 'english', 'SMTP service required', ''),
(647, 'english', 'List of Roles', ''),
(648, 'english', 'View', ''),
(649, 'english', 'Permissions', ''),
(650, 'english', 'View Role', ''),
(651, 'english', 'You can view detail role here, before edit', ''),
(652, 'english', 'List Permission', ''),
(653, 'english', 'Close', ''),
(654, 'english', 'You can edit role name and permission here', ''),
(655, 'english', 'Manage Category Menu', ''),
(656, 'english', 'List of Category Menu', ''),
(657, 'english', 'No Category Menu', ''),
(658, 'english', 'Category Menu Name', ''),
(659, 'english', 'Add Single or Category Menu', ''),
(660, 'english', 'Delete Category Menu', ''),
(663, 'english', 'Profile', ''),
(664, 'english', 'Personal Information', ''),
(665, 'english', 'Edit Profile', ''),
(666, 'english', 'About', ''),
(667, 'english', 'Update profile photo successfully', ''),
(668, 'english', 'Parent type required', ''),
(669, 'english', 'Parent name required', ''),
(670, 'english', 'Menu is single or parent required', ''),
(671, 'english', 'Parent menu link required', ''),
(672, 'english', 'Parent roles link required', ''),
(680, 'english', 'Data successfully created', ''),
(681, 'english', 'The entered link already exists in the menu', ''),
(682, 'english', 'Below Text', ''),
(683, 'english', 'New Tab', ''),
(689, 'english', 'Title required', ''),
(690, 'english', 'Content required', ''),
(691, 'english', 'Article edited successfully', ''),
(695, 'english', 'User created successfully', ''),
(696, 'english', 'Category List', ''),
(697, 'english', 'You can manage your categories here', ''),
(698, 'english', 'Add New Category', ''),
(699, 'english', 'Category Name', ''),
(700, 'english', 'Category Status', ''),
(701, 'english', 'Category name required', ''),
(702, 'english', 'Category created successfully', ''),
(703, 'english', 'Category status required', ''),
(704, 'english', 'Category name already exists', ''),
(705, 'english', 'Edit Category', ''),
(706, 'english', 'Number of users deactivated {1}', ''),
(707, 'english', 'Category edited successfully', ''),
(708, 'english', 'This category contains data. If you delete it, all associated data will automatically move to {1}. Are you sure?', ''),
(709, 'english', 'Category successfully deleted', ''),
(710, 'english', 'Category successfully deleted and articles moved to Uncategorized', ''),
(711, 'english', 'Article successfully deleted', ''),
(712, 'english', 'Create Role & Permissions', ''),
(716, 'english', 'User successfully updated', ''),
(718, 'english', 'The minimum password character length is 6', ''),
(719, 'english', 'Role required', ''),
(720, 'english', 'Status required', ''),
(729, 'english', 'Data successfully deleted', ''),
(730, 'english', 'Collapse Settings', ''),
(731, 'english', 'Text Content', ''),
(732, 'english', 'Remove Item', ''),
(733, 'english', 'Page Builder created successfully', ''),
(734, 'english', 'Page Settings', ''),
(735, 'english', 'Please input page name here', ''),
(736, 'english', 'Page Name required', ''),
(737, 'english', 'The Page Name is already in use by another page', ''),
(738, 'english', 'Page status required', ''),
(739, 'english', 'Page edited successfully', ''),
(740, 'english', 'Page Status', ''),
(741, 'english', 'Select Status', ''),
(742, 'english', 'Not Active', '');

-- --------------------------------------------------------

--
-- Table structure for table `lr_menu`
--

CREATE TABLE `lr_menu` (
  `id` int NOT NULL,
  `module` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `menu_with_parent` enum('true','false') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'false',
  `menu_parent_id` int NOT NULL DEFAULT '0',
  `menu_parent_code` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `menu_parent_name` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `menu_name` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `url` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `icon` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `roles` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` tinyint NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lr_menu`
--

INSERT INTO `lr_menu` (`id`, `module`, `menu_with_parent`, `menu_parent_id`, `menu_parent_code`, `menu_parent_name`, `menu_name`, `url`, `icon`, `roles`, `status`) VALUES
(1, 'manage_aboutus', 'false', 1, 'manage_aboutus', 'Manage About Us', 'About Us 1', 'manage_aboutus', '<i class=\"fad fa-address-card fa-fw me-2\"></i>', '99', 0),
(2, 'manage_aboutus', 'false', 1, 'manage_aboutus', 'Manage About Us', 'About Us 2', 'manage_aboutus', '<i class=\"fad fa-address-card fa-fw me-2\"></i>', '99', 0),
(3, 'manage_contactus', 'false', 2, 'uncategorized_manage_contactus', '', 'Manage Contact Us', 'manage_contactus', '<i class=\"fad fa-phone fa-fw me-2\"></i>', '99', 0),
(4, 'manage_event', 'false', 3, 'manage_event', 'Manage Event', 'List of Event', 'manage_event', '<i class=\"fad fa-list fa-fw me-2\"></i>', '99', 0),
(5, 'manage_event', 'false', 3, 'manage_event', 'Manage Event', 'Add New', 'manage_event/addpost', '<i class=\"fad fa-plus fa-fw me-2\"></i>', '99', 0),
(6, 'manage_event', 'false', 3, 'manage_event', 'Manage Event', 'Event Categories', 'manage_event/category', '<i class=\"fad fa-folder fa-fw me-2\"></i>', '99', 0),
(7, 'manage_event', 'false', 3, 'manage_event', 'Manage Event', 'Layout', 'manage_event/layout', '<i class=\"fad fa-swatchbook fa-fw me-2\"></i>', '99', 0),
(8, 'manage_gallery', 'false', 4, 'uncategorized_manage_gallery', '', 'Manage Gallery', 'manage_gallery', '<i class=\"fad fa-images fa-fw me-2\"></i>', '99', 0),
(9, 'manage_news', 'false', 5, 'manage_news', 'Manage News', 'List of News', 'manage_news', '<i class=\"fad fa-list fa-fw me-2\"></i>', '99', 0),
(10, 'manage_news', 'false', 5, 'manage_news', 'Manage News', 'Add New', 'manage_news/addpost', '<i class=\"fad fa-plus fa-fw me-2\"></i>', '99', 0),
(11, 'manage_news', 'false', 5, 'manage_news', 'Manage News', 'News Categories', 'manage_news/category', '<i class=\"fad fa-folder fa-fw me-2\"></i>', '99', 0),
(12, 'manage_news', 'false', 5, 'manage_news', 'Manage News', 'Layout', 'manage_news/layout', '<i class=\"fad fa-swatchbook fa-fw me-2\"></i>', '99', 0),
(13, 'manage_portofolio', 'false', 6, 'manage_portofolio', 'Manage Portofolio', 'List of Portofolio', 'manage_portofolio', '<i class=\"fad fa-list fa-fw me-2\"></i>', '99', 0),
(14, 'manage_portofolio', 'false', 6, 'manage_portofolio', 'Manage Portofolio', 'Add New', 'manage_portofolio/addpost', '<i class=\"fad fa-plus fa-fw me-2\"></i>', '99', 0),
(15, 'manage_portofolio', 'false', 6, 'manage_portofolio', 'Manage Portofolio', 'Portofolio Categories', 'manage_portofolio/category', '<i class=\"fad fa-folder fa-fw me-2\"></i>', '99', 0),
(16, 'manage_portofolio', 'false', 6, 'manage_portofolio', 'Manage Portofolio', 'Layout', 'manage_portofolio/layout', '<i class=\"fad fa-swatchbook fa-fw me-2\"></i>', '99', 0),
(17, 'manage_promotion', 'false', 7, 'manage_promotion', 'Manage Promotion', 'List of Promotion', 'manage_promotion', '<i class=\"fad fa-list fa-fw me-2\"></i>', '99', 0),
(18, 'manage_promotion', 'false', 7, 'manage_promotion', 'Manage Promotion', 'Add New', 'manage_promotion/addpost', '<i class=\"fad fa-plus fa-fw me-2\"></i>', '99', 0),
(19, 'manage_promotion', 'false', 7, 'manage_promotion', 'Manage Promotion', 'Promotion Categories', 'manage_promotion/category', '<i class=\"fad fa-folder fa-fw me-2\"></i>', '99', 0),
(20, 'manage_promotion', 'false', 7, 'manage_promotion', 'Manage Promotion', 'Layout', 'manage_promotion/layout', '<i class=\"fad fa-swatchbook fa-fw me-2\"></i>', '99', 0),
(21, 'manage_appearance', 'false', 8, 'manage_appearance', 'Manage Appearance', 'Logo', 'manage_appearance/logo', '<i class=\"fad fa-swatchbook fa-fw me-2\"></i>', '99', 0),
(22, 'manage_appearance', 'false', 8, 'manage_appearance', 'Manage Appearance', 'Slideshow', 'manage_appearance/slideshow', '<i class=\"fad fa-images fa-fw me-2\"></i>', '99', 0),
(23, 'manage_appearance', 'false', 8, 'manage_appearance', 'Manage Appearance', 'Cover Image', 'manage_appearance/coverimage', '<i class=\"fad fa-images fa-fw me-2\"></i>', '99', 0),
(24, 'manage_appearance', 'false', 8, 'manage_appearance', 'Manage Appearance', 'Page Style', 'manage_appearance/pagestyle', '<i class=\"fad fa-images fa-fw me-2\"></i>', '99', 0),
(25, 'manage_appearance', 'false', 8, 'manage_appearance', 'Manage Appearance', 'Layout', 'manage_appearance/layout', '<i class=\"fad fa-images fa-fw me-2\"></i>', '99', 0),
(26, 'manage_section_content', 'false', 9, 'manage_section_content', 'Manage Section Content', 'Content In Pages', 'manage_section_content/pages', '<i class=\"fad fa-swatchbook fa-fw me-2\"></i>', '99', 0),
(27, 'manage_section_content', 'false', 9, 'manage_section_content', 'Manage Section Content', 'Header', 'manage_section_content/header', '<i class=\"fad fa-images fa-fw me-2\"></i>', '99', 0),
(28, 'manage_section_content', 'false', 9, 'manage_section_content', 'Manage Section Content', 'Footer', 'manage_section_content/footer', '<i class=\"fad fa-images fa-fw me-2\"></i>', '99', 0),
(29, 'manage_dropdown', 'false', 10, 'manage_dropdown', 'Manage Dropdown Menu', 'List of Dropdown Menu', 'manage_dropdown', '<i class=\"fad fa-list fa-fw me-2\"></i>', '99', 0),
(30, 'manage_dropdown', 'false', 10, 'manage_dropdown', 'Manage Dropdown Menu', 'Add New', 'manage_dropdown/addpost', '<i class=\"fad fa-plus fa-fw me-2\"></i>', '99', 0),
(31, 'manage_header', 'false', 11, 'manage_header', 'Manage Header Menu', 'List of Header Menu', 'manage_header', '<i class=\"fad fa-list fa-fw me-2\"></i>', '99', 0);

-- --------------------------------------------------------

--
-- Table structure for table `lr_menu_categorymenu_json`
--

CREATE TABLE `lr_menu_categorymenu_json` (
  `id` int NOT NULL,
  `menu_page` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `menu_vars` json NOT NULL,
  `menu_vars_backup` json NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `lr_menu_categorymenu_json`
--

INSERT INTO `lr_menu_categorymenu_json` (`id`, `menu_page`, `menu_vars`, `menu_vars_backup`) VALUES
(1, 'awesome_admin', '[{\"category_code\": \"uIxTa0lV3L4EaV9A6BvJ7x\", \"category_name\": \"All Menus\", \"category_roles\": \"\"}]', '[{\"category_code\": \"uIxTa0lV3L4EaV9A6BvJ7x\", \"category_name\": \"All Menu\", \"category_roles\": \"\"}]');

-- --------------------------------------------------------

--
-- Table structure for table `lr_menu_parent`
--

CREATE TABLE `lr_menu_parent` (
  `id` int NOT NULL,
  `is_parent` enum('true','false') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'false',
  `module` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `parent_name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `parent_code` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `icon` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `roles` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lr_menu_parent`
--

INSERT INTO `lr_menu_parent` (`id`, `is_parent`, `module`, `parent_name`, `parent_code`, `icon`, `roles`) VALUES
(1, 'false', 'manage_aboutus', 'Manage About Us', 'manage_aboutus', '<i class=\"fad fa-address-card fa-fw me-2\"></i>', '99'),
(2, 'false', 'manage_contactus', 'Manage Contact Us', 'manage_contactus', '<i class=\"fad fa-phone fa-fw me-2\"></i>', '99'),
(3, 'false', 'manage_event', 'Manage Event', 'manage_event', '<i class=\"fad fa-newspaper fa-fw me-2\"></i>', '99'),
(4, 'false', 'manage_gallery', 'Manage Gallery', 'manage_gallery', '<i class=\"fad fa-phone fa-fw me-2\"></i>', '99'),
(5, 'false', 'manage_news', 'Manage News', 'manage_news', '<i class=\"fad fa-newspaper fa-fw me-2\"></i>', '99'),
(6, 'false', 'manage_portofolio', 'Manage Portofolio', 'manage_portofolio', '<i class=\"fad fa-newspaper fa-fw me-2\"></i>', '99'),
(7, 'false', 'manage_promotion', 'Manage Promotion', 'manage_promotion', '<i class=\"fad fa-newspaper fa-fw me-2\"></i>', '99'),
(8, 'false', 'manage_appearance', 'Manage Appearance', 'manage_appearance', '<i class=\"fad fa-palette fa-fw me-2\"></i>', '99'),
(9, 'false', 'manage_section_content', 'Manage Section Content', 'manage_section_content', '<i class=\"fad fa-columns fa-fw me-2\"></i>', '99'),
(10, 'false', 'manage_dropdown', 'Manage Dropdown Menu', 'manage_dropdown', '<i class=\"fad fa-newspaper fa-fw me-2\"></i>', '99'),
(11, 'false', 'manage_header', 'Manage Header Menu', 'manage_header', '<i class=\"fad fa-newspaper fa-fw me-2\"></i>', '99');

-- --------------------------------------------------------

--
-- Table structure for table `lr_menu_parentmenu_json`
--

CREATE TABLE `lr_menu_parentmenu_json` (
  `id` int NOT NULL,
  `menu_page` varchar(55) NOT NULL,
  `menu_vars` json DEFAULT NULL,
  `menu_vars_backup` json DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `lr_menu_parentmenu_json`
--

INSERT INTO `lr_menu_parentmenu_json` (`id`, `menu_page`, `menu_vars`, `menu_vars_backup`) VALUES
(1, 'awesome_admin', '[{\"parent_code\": \"hBMt85z8I4p3dgfZCt1sf4\", \"parent_icon\": \"\", \"parent_link\": \"manage_article\", \"parent_name\": \"Manage Articles\", \"parent_type\": \"custom\", \"parent_roles\": [\"Administrator\", \"Super Admin\"], \"category_code\": \"uIxTa0lV3L4EaV9A6BvJ7x\", \"parent_icon_url\": \"\", \"parent_icon_path\": \"\", \"parent_icon_type\": \"custom_input\", \"is_for_parent_menu\": \"single\", \"parent_icon_custom\": \"<i class=\\\"fal fa-newspaper fa-fw\\\"></i>\", \"parent_permissions\": \"\"}, {\"parent_code\": \"ktGbOw0EloeZX73WIs50VO\", \"parent_icon\": \"\", \"parent_link\": \"manage_coverimage\", \"parent_name\": \"Manage Cover Image\", \"parent_type\": \"custom\", \"parent_roles\": [], \"category_code\": \"uIxTa0lV3L4EaV9A6BvJ7x\", \"parent_icon_url\": \"\", \"parent_icon_path\": \"\", \"parent_icon_type\": \"custom_input\", \"is_for_parent_menu\": \"single\", \"parent_icon_custom\": \"<i class=\\\"fal fa-puzzle-piece fa-fw\\\"></i>\", \"parent_permissions\": \"\"}, {\"parent_code\": \"dqV84cjEjCrmp0BWF0fxpn\", \"parent_icon\": \"\", \"parent_link\": \"filemanager\", \"parent_name\": \"File Manager\", \"parent_type\": \"custom\", \"parent_roles\": [\"Super Admin\", \"Administrator\", \"General Member\"], \"category_code\": \"uIxTa0lV3L4EaV9A6BvJ7x\", \"parent_icon_url\": \"\", \"parent_icon_path\": \"\", \"parent_icon_type\": \"custom_input\", \"is_for_parent_menu\": \"single\", \"parent_icon_custom\": \"<i class=\\\"fal fa-file-alt fa-fw\\\"></i>\", \"parent_permissions\": \"\"}]', '[{\"parent_code\": \"hBMt85z8I4p3dgfZCt1sf4\", \"parent_icon\": \"\", \"parent_link\": \"manage_article\", \"parent_name\": \"Manage Articles\", \"parent_type\": \"custom\", \"parent_roles\": [\"Administrator\", \"Super Admin\"], \"category_code\": \"uIxTa0lV3L4EaV9A6BvJ7x\", \"parent_icon_url\": \"\", \"parent_icon_path\": \"\", \"parent_icon_type\": \"custom_input\", \"is_for_parent_menu\": \"single\", \"parent_icon_custom\": \"<i class=\\\"fal fa-newspaper fa-fw\\\"></i>\", \"parent_permissions\": \"\"}, {\"parent_code\": \"ktGbOw0EloeZX73WIs50VO\", \"parent_icon\": \"\", \"parent_link\": \"manage_coverimage\", \"parent_name\": \"Manage Cover Image\", \"parent_type\": \"custom\", \"parent_roles\": [], \"category_code\": \"uIxTa0lV3L4EaV9A6BvJ7x\", \"parent_icon_url\": \"\", \"parent_icon_path\": \"\", \"parent_icon_type\": \"custom_input\", \"is_for_parent_menu\": \"single\", \"parent_icon_custom\": \"<i class=\\\"fal fa-puzzle-piece fa-fw\\\"></i>\", \"parent_permissions\": \"\"}, {\"parent_code\": \"dqV84cjEjCrmp0BWF0fxpn\", \"parent_icon\": \"\", \"parent_link\": \"filemanager\", \"parent_name\": \"File Manager\", \"parent_type\": \"custom\", \"parent_roles\": [\"Super Admin\", \"Administrator\", \"General Member\"], \"category_code\": \"uIxTa0lV3L4EaV9A6BvJ7x\", \"parent_icon_url\": \"\", \"parent_icon_path\": \"\", \"parent_icon_type\": \"custom_input\", \"is_for_parent_menu\": \"single\", \"parent_icon_custom\": \"<i class=\\\"fal fa-file-alt fa-fw\\\"></i>\", \"parent_permissions\": \"\"}, {\"parent_code\": \"nT21heY6KH8npGso0DM6yl\", \"parent_icon\": \"\", \"parent_link\": \"account\", \"parent_name\": \"Accounts\", \"parent_type\": \"custom\", \"parent_roles\": [\"Super Admin\"], \"category_code\": \"\", \"parent_icon_url\": \"\", \"parent_icon_path\": \"\", \"parent_icon_type\": \"\", \"is_for_parent_menu\": \"single\", \"parent_icon_custom\": \"\", \"parent_permissions\": \"\"}]');

-- --------------------------------------------------------

--
-- Table structure for table `lr_menu_submenu_json`
--

CREATE TABLE `lr_menu_submenu_json` (
  `id` int NOT NULL,
  `parent_code` varchar(155) NOT NULL,
  `parent_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `menu_vars` json DEFAULT NULL,
  `menu_vars_backup` json DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lr_migrations`
--

CREATE TABLE `lr_migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lr_migrations`
--

INSERT INTO `lr_migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2024_05_10_022940_create_permission_tables', 2),
(5, '2024_05_10_094534_create_personal_access_tokens_table', 3),
(6, '2024_05_10_094727_create_oauth_auth_codes_table', 3),
(7, '2024_05_10_094728_create_oauth_access_tokens_table', 3),
(8, '2024_05_10_094729_create_oauth_refresh_tokens_table', 3),
(9, '2024_05_10_094730_create_oauth_clients_table', 3),
(10, '2024_05_10_094731_create_oauth_personal_access_clients_table', 3),
(11, '2024_05_14_075652_create_testings_table', 3),
(12, '2024_05_17_045334_create_oauth_auth_codes_table', 4),
(13, '2024_05_17_045335_create_oauth_access_tokens_table', 4),
(14, '2024_05_17_045336_create_oauth_refresh_tokens_table', 4),
(15, '2024_05_17_045337_create_oauth_clients_table', 4),
(16, '2024_05_17_045338_create_oauth_personal_access_clients_table', 4),
(17, '2024_05_17_045506_create_oauth_auth_codes_table', 5),
(18, '2024_05_17_045507_create_oauth_access_tokens_table', 5),
(19, '2024_05_17_045508_create_oauth_refresh_tokens_table', 5),
(20, '2024_05_17_045509_create_oauth_clients_table', 5),
(21, '2024_05_17_045510_create_oauth_personal_access_clients_table', 5),
(22, '2025_08_14_151502_create_account_login_history_table', 0),
(23, '2025_08_14_151502_create_account_status_table', 0),
(24, '2025_08_14_151502_create_accounts_table', 0),
(25, '2025_08_14_151502_create_authentication_log_table', 0),
(26, '2025_08_14_151502_create_blog_article_table', 0),
(27, '2025_08_14_151502_create_blog_category_table', 0),
(28, '2025_08_14_151502_create_blog_subcategory_table', 0),
(29, '2025_08_14_151502_create_cache_table', 0),
(30, '2025_08_14_151502_create_cache_locks_table', 0),
(31, '2025_08_14_151502_create_custom_permissions_table', 0),
(32, '2025_08_14_151502_create_failed_jobs_table', 0),
(33, '2025_08_14_151502_create_job_batches_table', 0),
(34, '2025_08_14_151502_create_jobs_table', 0),
(35, '2025_08_14_151502_create_language_table', 0),
(36, '2025_08_14_151502_create_menu_table', 0),
(37, '2025_08_14_151502_create_menu_categorymenu_json_table', 0),
(38, '2025_08_14_151502_create_menu_parent_table', 0),
(39, '2025_08_14_151502_create_menu_parentmenu_json_table', 0),
(40, '2025_08_14_151502_create_menu_submenu_json_table', 0),
(41, '2025_08_14_151502_create_model_has_permissions_table', 0),
(42, '2025_08_14_151502_create_model_has_roles_table', 0),
(43, '2025_08_14_151502_create_notifications_table', 0),
(44, '2025_08_14_151502_create_oauth_access_tokens_table', 0),
(45, '2025_08_14_151502_create_oauth_auth_codes_table', 0),
(46, '2025_08_14_151502_create_oauth_clients_table', 0),
(47, '2025_08_14_151502_create_oauth_personal_access_clients_table', 0),
(48, '2025_08_14_151502_create_oauth_refresh_tokens_table', 0),
(49, '2025_08_14_151502_create_oil_palm_tree_v3_table', 0),
(50, '2025_08_14_151502_create_page_theme_settings_table', 0),
(51, '2025_08_14_151502_create_page_themes_table', 0),
(52, '2025_08_14_151502_create_password_reset_tokens_table', 0),
(53, '2025_08_14_151502_create_permissions_table', 0),
(54, '2025_08_14_151502_create_personal_access_tokens_table', 0),
(55, '2025_08_14_151502_create_role_has_permissions_table', 0),
(56, '2025_08_14_151502_create_roles_table', 0),
(57, '2025_08_14_151502_create_sessions_table', 0),
(58, '2025_08_14_151502_create_site_config_table', 0),
(59, '2025_08_14_151502_create_smtp_service_table', 0),
(60, '2025_08_14_151502_create_smtp_settings_table', 0),
(61, '2025_08_14_151502_create_testings_table', 0),
(62, '2025_08_14_151502_create_theme_settings_table', 0),
(63, '2025_08_14_151502_create_themes_table', 0),
(64, '2025_08_14_151502_create_user_information_table', 0),
(65, '2025_08_14_151502_create_users_table', 0),
(66, '2025_08_14_151505_add_foreign_keys_to_model_has_permissions_table', 0),
(67, '2025_08_14_151505_add_foreign_keys_to_model_has_roles_table', 0),
(68, '2025_08_14_151505_add_foreign_keys_to_role_has_permissions_table', 0),
(69, '2025_08_14_152746_create_account_login_history_table', 0),
(70, '2025_08_14_152746_create_account_status_table', 0),
(71, '2025_08_14_152746_create_accounts_table', 0),
(72, '2025_08_14_152746_create_authentication_log_table', 0),
(73, '2025_08_14_152746_create_blog_article_table', 0),
(74, '2025_08_14_152746_create_blog_category_table', 0),
(75, '2025_08_14_152746_create_blog_subcategory_table', 0),
(76, '2025_08_14_152746_create_cache_table', 0),
(77, '2025_08_14_152746_create_cache_locks_table', 0),
(78, '2025_08_14_152746_create_custom_permissions_table', 0),
(79, '2025_08_14_152746_create_failed_jobs_table', 0),
(80, '2025_08_14_152746_create_job_batches_table', 0),
(81, '2025_08_14_152746_create_jobs_table', 0),
(82, '2025_08_14_152746_create_language_table', 0),
(83, '2025_08_14_152746_create_menu_table', 0),
(84, '2025_08_14_152746_create_menu_categorymenu_json_table', 0),
(85, '2025_08_14_152746_create_menu_parent_table', 0),
(86, '2025_08_14_152746_create_menu_parentmenu_json_table', 0),
(87, '2025_08_14_152746_create_menu_submenu_json_table', 0),
(88, '2025_08_14_152746_create_model_has_permissions_table', 0),
(89, '2025_08_14_152746_create_model_has_roles_table', 0),
(90, '2025_08_14_152746_create_notifications_table', 0),
(91, '2025_08_14_152746_create_oauth_access_tokens_table', 0),
(92, '2025_08_14_152746_create_oauth_auth_codes_table', 0),
(93, '2025_08_14_152746_create_oauth_clients_table', 0),
(94, '2025_08_14_152746_create_oauth_personal_access_clients_table', 0),
(95, '2025_08_14_152746_create_oauth_refresh_tokens_table', 0),
(96, '2025_08_14_152746_create_oil_palm_tree_v3_table', 0),
(97, '2025_08_14_152746_create_page_theme_settings_table', 0),
(98, '2025_08_14_152746_create_page_themes_table', 0),
(99, '2025_08_14_152746_create_password_reset_tokens_table', 0),
(100, '2025_08_14_152746_create_permissions_table', 0),
(101, '2025_08_14_152746_create_personal_access_tokens_table', 0),
(102, '2025_08_14_152746_create_role_has_permissions_table', 0),
(103, '2025_08_14_152746_create_roles_table', 0),
(104, '2025_08_14_152746_create_sessions_table', 0),
(105, '2025_08_14_152746_create_site_config_table', 0),
(106, '2025_08_14_152746_create_smtp_service_table', 0),
(107, '2025_08_14_152746_create_smtp_settings_table', 0),
(108, '2025_08_14_152746_create_testings_table', 0),
(109, '2025_08_14_152746_create_theme_settings_table', 0),
(110, '2025_08_14_152746_create_themes_table', 0),
(111, '2025_08_14_152746_create_user_information_table', 0),
(112, '2025_08_14_152746_create_users_table', 0),
(113, '2025_08_14_152749_add_foreign_keys_to_model_has_permissions_table', 0),
(114, '2025_08_14_152749_add_foreign_keys_to_model_has_roles_table', 0),
(115, '2025_08_14_152749_add_foreign_keys_to_role_has_permissions_table', 0),
(116, '2025_08_15_091631_create_account_login_history_table', 0),
(117, '2025_08_15_091631_create_account_status_table', 0),
(118, '2025_08_15_091631_create_accounts_table', 0),
(119, '2025_08_15_091631_create_authentication_log_table', 0),
(120, '2025_08_15_091631_create_blog_article_table', 0),
(121, '2025_08_15_091631_create_blog_category_table', 0),
(122, '2025_08_15_091631_create_blog_subcategory_table', 0),
(123, '2025_08_15_091631_create_cache_table', 0),
(124, '2025_08_15_091631_create_cache_locks_table', 0),
(125, '2025_08_15_091631_create_custom_permissions_table', 0),
(126, '2025_08_15_091631_create_failed_jobs_table', 0),
(127, '2025_08_15_091631_create_job_batches_table', 0),
(128, '2025_08_15_091631_create_jobs_table', 0),
(129, '2025_08_15_091631_create_language_table', 0),
(130, '2025_08_15_091631_create_menu_table', 0),
(131, '2025_08_15_091631_create_menu_categorymenu_json_table', 0),
(132, '2025_08_15_091631_create_menu_parent_table', 0),
(133, '2025_08_15_091631_create_menu_parentmenu_json_table', 0),
(134, '2025_08_15_091631_create_menu_submenu_json_table', 0),
(135, '2025_08_15_091631_create_model_has_permissions_table', 0),
(136, '2025_08_15_091631_create_model_has_roles_table', 0),
(137, '2025_08_15_091631_create_notifications_table', 0),
(138, '2025_08_15_091631_create_oauth_access_tokens_table', 0),
(139, '2025_08_15_091631_create_oauth_auth_codes_table', 0),
(140, '2025_08_15_091631_create_oauth_clients_table', 0),
(141, '2025_08_15_091631_create_oauth_personal_access_clients_table', 0),
(142, '2025_08_15_091631_create_oauth_refresh_tokens_table', 0),
(143, '2025_08_15_091631_create_oil_palm_tree_v3_table', 0),
(144, '2025_08_15_091631_create_page_theme_settings_table', 0),
(145, '2025_08_15_091631_create_page_themes_table', 0),
(146, '2025_08_15_091631_create_password_reset_tokens_table', 0),
(147, '2025_08_15_091631_create_permissions_table', 0),
(148, '2025_08_15_091631_create_personal_access_tokens_table', 0),
(149, '2025_08_15_091631_create_role_has_permissions_table', 0),
(150, '2025_08_15_091631_create_roles_table', 0),
(151, '2025_08_15_091631_create_sessions_table', 0),
(152, '2025_08_15_091631_create_site_config_table', 0),
(153, '2025_08_15_091631_create_smtp_service_table', 0),
(154, '2025_08_15_091631_create_smtp_settings_table', 0),
(155, '2025_08_15_091631_create_testings_table', 0),
(156, '2025_08_15_091631_create_theme_settings_table', 0),
(157, '2025_08_15_091631_create_themes_table', 0),
(158, '2025_08_15_091631_create_user_information_table', 0),
(159, '2025_08_15_091631_create_users_table', 0),
(160, '2025_08_15_091634_add_foreign_keys_to_model_has_permissions_table', 0),
(161, '2025_08_15_091634_add_foreign_keys_to_model_has_roles_table', 0),
(162, '2025_08_15_091634_add_foreign_keys_to_role_has_permissions_table', 0),
(163, '2025_07_30_093735_create_authentication_log_table', 6),
(164, '2025_11_04_140126_create_credits_table', 6);

-- --------------------------------------------------------

--
-- Table structure for table `lr_model_has_permissions`
--

CREATE TABLE `lr_model_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lr_model_has_roles`
--

CREATE TABLE `lr_model_has_roles` (
  `role_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lr_model_has_roles`
--

INSERT INTO `lr_model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\Awesome_Admin\\Account', 1),
(2, 'App\\Models\\Awesome_Admin\\Account', 2),
(2, 'App\\Models\\Awesome_Admin\\Account', 3),
(3, 'App\\Models\\Awesome_Admin\\Account', 4),
(3, 'App\\Models\\Awesome_Admin\\Account', 5),
(3, 'App\\Models\\Awesome_Admin\\Account', 6),
(3, 'App\\Models\\Awesome_Admin\\Account', 7),
(3, 'App\\Models\\Awesome_Admin\\Account', 8),
(3, 'App\\Models\\Awesome_Admin\\Account', 9),
(3, 'App\\Models\\Awesome_Admin\\Account', 10),
(26, 'App\\Models\\Awesome_Admin\\Account', 11),
(26, 'App\\Models\\Awesome_Admin\\Account', 12),
(26, 'App\\Models\\Awesome_Admin\\Account', 13),
(26, 'App\\Models\\Awesome_Admin\\Account', 14),
(26, 'App\\Models\\Awesome_Admin\\Account', 15),
(3, 'App\\Models\\Awesome_Admin\\Account', 16),
(3, 'App\\Models\\Awesome_Admin\\Account', 17),
(3, 'App\\Models\\Awesome_Admin\\Account', 18),
(3, 'App\\Models\\Awesome_Admin\\Account', 19),
(3, 'App\\Models\\Awesome_Admin\\Account', 20),
(4, 'App\\Models\\Awesome_Admin\\Account', 21),
(3, 'App\\Models\\Awesome_Admin\\Account', 22),
(3, 'App\\Models\\Awesome_Admin\\Account', 23),
(3, 'App\\Models\\Awesome_Admin\\Account', 24),
(3, 'App\\Models\\Awesome_Admin\\Account', 25),
(3, 'App\\Models\\Awesome_Admin\\Account', 26),
(3, 'App\\Models\\Awesome_Admin\\Account', 27),
(3, 'App\\Models\\Awesome_Admin\\Account', 28),
(3, 'App\\Models\\Awesome_Admin\\Account', 29),
(3, 'App\\Models\\Awesome_Admin\\Account', 30),
(3, 'App\\Models\\Awesome_Admin\\Account', 31),
(3, 'App\\Models\\Awesome_Admin\\Account', 32),
(3, 'App\\Models\\Awesome_Admin\\Account', 33),
(3, 'App\\Models\\Awesome_Admin\\Account', 34),
(3, 'App\\Models\\Awesome_Admin\\Account', 35),
(3, 'App\\Models\\Awesome_Admin\\Account', 36),
(4, 'App\\Models\\Awesome_Admin\\Account', 37),
(4, 'App\\Models\\Awesome_Admin\\Account', 38),
(4, 'App\\Models\\Awesome_Admin\\Account', 39),
(4, 'App\\Models\\Awesome_Admin\\Account', 40),
(4, 'App\\Models\\Awesome_Admin\\Account', 41),
(3, 'App\\Models\\Awesome_Admin\\Account', 42),
(3, 'App\\Models\\Awesome_Admin\\Account', 43),
(3, 'App\\Models\\Awesome_Admin\\Account', 44),
(3, 'App\\Models\\Awesome_Admin\\Account', 45),
(3, 'App\\Models\\Awesome_Admin\\Account', 46),
(3, 'App\\Models\\Awesome_Admin\\Account', 47),
(3, 'App\\Models\\Awesome_Admin\\Account', 48),
(3, 'App\\Models\\Awesome_Admin\\Account', 49),
(3, 'App\\Models\\Awesome_Admin\\Account', 50),
(3, 'App\\Models\\Awesome_Admin\\Account', 56),
(3, 'App\\Models\\Awesome_Admin\\Account', 57),
(3, 'App\\Models\\Awesome_Admin\\Account', 58),
(3, 'App\\Models\\Awesome_Admin\\Account', 59),
(3, 'App\\Models\\Awesome_Admin\\Account', 60),
(3, 'App\\Models\\Awesome_Admin\\Account', 61),
(3, 'App\\Models\\Awesome_Admin\\Account', 62),
(3, 'App\\Models\\Awesome_Admin\\Account', 63),
(3, 'App\\Models\\Awesome_Admin\\Account', 64),
(3, 'App\\Models\\Awesome_Admin\\Account', 65),
(3, 'App\\Models\\Awesome_Admin\\Account', 66),
(3, 'App\\Models\\Awesome_Admin\\Account', 67);

-- --------------------------------------------------------

--
-- Table structure for table `lr_notifications`
--

CREATE TABLE `lr_notifications` (
  `id` bigint NOT NULL,
  `user_id` bigint NOT NULL DEFAULT '0',
  `from_id` bigint NOT NULL DEFAULT '0',
  `from_fullname` varchar(155) NOT NULL,
  `to_id` bigint NOT NULL DEFAULT '0',
  `to_fullname` varchar(155) NOT NULL,
  `type` varchar(55) NOT NULL,
  `icon` varchar(255) NOT NULL,
  `title` varchar(155) NOT NULL,
  `message` text NOT NULL,
  `hasread` tinyint NOT NULL DEFAULT '0' COMMENT 'Unread 0, Read 1',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `lr_notifications`
--

INSERT INTO `lr_notifications` (`id`, `user_id`, `from_id`, `from_fullname`, `to_id`, `to_fullname`, `type`, `icon`, `title`, `message`, `hasread`, `updated_at`, `created_at`) VALUES
(1, 1, 1, 'Administrator', 2, 'Andhika Adhitia N', 'general', '<i class=\"fad fa-bullhorn fa-fw fa-lg\"></i>', 'new rule', 'Andhika has assign to new rule', 0, '2025-07-15 04:31:29', '2025-07-15 04:31:29'),
(2, 1, 1, 'Administrator', 2, 'Andhika Adhitia N', 'general', '<i class=\"fad fa-bullhorn fa-fw fa-lg\"></i>', 'new rule', 'Andhika has assign to new rule', 0, '2025-07-15 04:33:32', '2025-07-15 04:33:32'),
(3, 1, 1, 'Administrator', 2, 'Andhika Adhitia N', 'general', '<i class=\"fad fa-bullhorn fa-fw fa-lg\"></i>', 'new rule', 'Andhika has assign to new rule', 0, '2025-07-15 04:33:54', '2025-07-15 04:33:54'),
(4, 1, 1, 'Administrator', 1, 'Administrator', 'general', '<i class=\"fad fa-bullhorn fa-fw fa-lg\"></i>', 'follower', 'You have been followed by Aruna', 0, '2025-07-15 04:34:22', '2025-07-15 04:34:22'),
(5, 1, 1, 'Administrator', 1, 'Administrator', 'general', '<i class=\"fad fa-bullhorn fa-fw fa-lg\"></i>', 'follower', 'You have been followed by Rajo Langit', 0, '2025-07-15 04:34:33', '2025-07-15 04:34:33'),
(6, 1, 1, 'Administrator', 1, 'Administrator', 'general', '<i class=\"fad fa-bullhorn fa-fw fa-lg\"></i>', 'new follower', 'You have been followed by Gumara Peto Alam', 0, '2025-07-15 04:34:52', '2025-07-15 04:34:52'),
(7, 1, 1, 'Administrator', 1, 'Administrator', 'user', '<i class=\"fad fa-bullhorn fa-fw fa-lg\"></i>', 'new follower', 'You have been followed by Gumara Peto Alam', 0, '2025-07-15 04:35:03', '2025-07-15 04:35:03'),
(8, 1, 1, 'Administrator', 1, 'Administrator', 'general', '<i class=\"fad fa-bullhorn fa-fw fa-lg\"></i>', 'new follower', 'You have been followed by Gumara Peto Alam', 0, '2025-07-15 04:35:38', '2025-07-15 04:35:38'),
(9, 1, 1, 'Administrator', 1, 'Administrator', 'system', '<i class=\"fad fa-bullhorn fa-fw fa-lg\"></i>', 'new status', 'You role has been changed to Premium Member', 0, '2025-07-15 04:46:12', '2025-07-15 04:46:12'),
(10, 1, 1, 'Administrator', 1, 'Administrator', 'general', '<i class=\"fad fa-bullhorn fa-fw fa-lg\"></i>', 'new status', 'You role has been changed to Premium Member', 0, '2025-07-15 04:47:03', '2025-07-15 04:47:03'),
(11, 1, 1, 'Administrator', 1, 'Administrator', 'general', '<i class=\"fad fa-bullhorn fa-fw fa-lg\"></i>', 'new status', 'You role has been changed to Premium Member', 0, '2025-07-15 04:48:19', '2025-07-15 04:48:19'),
(12, 1, 1, 'Administrator', 1, 'Administrator', 'general', '<i class=\"fad fa-bullhorn fa-fw fa-lg\"></i>', 'new status', 'You role has been changed to Premium Member', 0, '2025-07-15 06:54:13', '2025-07-15 06:54:13'),
(13, 1, 1, 'Administrator', 1, 'Administrator', 'general', '<i class=\"fad fa-bullhorn fa-fw fa-lg\"></i>', 'new status', 'You role has been changed to Premium Member', 0, '2025-07-15 07:00:48', '2025-07-15 07:00:48'),
(14, 1, 1, 'Administrator', 1, 'Administrator', 'general', '<i class=\"fad fa-bullhorn fa-fw fa-lg\"></i>', 'new status', 'You role has been changed to Premium Member', 0, '2025-07-22 03:07:32', '2025-07-22 03:07:32'),
(15, 1, 1, 'Administrator', 1, 'Administrator', 'general', '<i class=\"fad fa-bullhorn fa-fw fa-lg\"></i>', 'new status', 'You role has been changed to Premium Member', 0, '2025-07-22 03:07:47', '2025-07-22 03:07:47'),
(16, 1, 1, 'Administrator', 1, 'Administrator', 'general', '<i class=\"fad fa-bullhorn fa-fw fa-lg\"></i>', 'new status', 'You role has been changed to Premium Member', 0, '2025-07-22 03:07:47', '2025-07-22 03:07:47'),
(17, 1, 1, 'Administrator', 1, 'Administrator', 'general', '<i class=\"fad fa-bullhorn fa-fw fa-lg\"></i>', 'new status', 'You role has been changed to Premium Member', 0, '2025-07-22 03:11:16', '2025-07-22 03:11:16'),
(18, 1, 1, 'Administrator', 1, 'Administrator', 'general', '<i class=\"fad fa-bullhorn fa-fw fa-lg\"></i>', 'new status', 'You role has been changed to Premium Member', 0, '2025-07-22 03:11:17', '2025-07-22 03:11:17'),
(19, 1, 1, 'Administrator', 1, 'Administrator', 'general', '<i class=\"fad fa-bullhorn fa-fw fa-lg\"></i>', 'new status', 'You role has been changed to Premium Member', 0, '2025-07-22 03:11:29', '2025-07-22 03:11:29'),
(20, 1, 1, 'Administrator', 1, 'Administrator', 'general', '<i class=\"fad fa-bullhorn fa-fw fa-lg\"></i>', 'new status', 'You role has been changed to Premium Member', 0, '2025-07-22 03:11:41', '2025-07-22 03:11:41'),
(21, 1, 1, 'Administrator', 1, 'Administrator', 'general', '<i class=\"fad fa-bullhorn fa-fw fa-lg\"></i>', 'new status', 'You role has been changed to Premium Member', 0, '2025-07-22 03:11:44', '2025-07-22 03:11:44'),
(22, 1, 1, 'Administrator', 1, 'Administrator', 'general', '<i class=\"fad fa-bullhorn fa-fw fa-lg\"></i>', 'new status', 'You role has been changed to Premium Member', 0, '2025-07-22 10:16:13', '2025-07-22 10:16:13'),
(23, 1, 1, 'Administrator', 1, 'Administrator', 'general', '<i class=\"fad fa-bullhorn fa-fw fa-lg\"></i>', 'new status', 'You role has been changed to Premium Member', 0, '2025-07-23 02:34:49', '2025-07-23 02:34:49'),
(24, 1, 1, 'Administrator', 1, 'Administrator', 'general', '<i class=\"fad fa-bullhorn fa-fw fa-lg\"></i>', 'new status', 'You role has been changed to Premium Member', 0, '2025-07-23 08:56:16', '2025-07-23 08:56:16'),
(25, 1, 1, 'Administrator', 1, 'Administrator', 'general', '<i class=\"fad fa-bullhorn fa-fw fa-lg\"></i>', 'new status', 'You role has been changed to Premium Member', 0, '2025-07-23 09:18:12', '2025-07-23 09:18:12'),
(26, 1, 1, 'Administrator', 1, 'Administrator', 'general', '<i class=\"fad fa-bullhorn fa-fw fa-lg\"></i>', 'new status', 'You role has been changed to Premium Member', 0, '2025-07-24 03:23:53', '2025-07-24 03:23:53'),
(27, 1, 1, 'Administrator', 1, 'Administrator', 'general', '<i class=\"fad fa-bullhorn fa-fw fa-lg\"></i>', 'new status', 'You role has been changed to Premium Member', 0, '2025-07-24 06:51:52', '2025-07-24 06:51:52');

-- --------------------------------------------------------

--
-- Table structure for table `lr_oauth_access_tokens`
--

CREATE TABLE `lr_oauth_access_tokens` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `client_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `scopes` text COLLATE utf8mb4_unicode_ci,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lr_oauth_access_tokens`
--

INSERT INTO `lr_oauth_access_tokens` (`id`, `user_id`, `client_id`, `name`, `scopes`, `revoked`, `created_at`, `updated_at`, `expires_at`) VALUES
('205a429dd04476c16129b0e8c76ea07b3392b31378bb6e9796274ed7b51c3eef50a39d9703deb52d', 1, 1, 'api', '[]', 0, '2025-11-14 05:28:31', '2025-11-14 05:28:31', '2026-11-14 12:28:31'),
('2e18dc91fd4eac5a82a06ea97bbf7581ab6c963e2a809049089e81a0cb918dfa7bd361c070bcc00b', 1, 1, 'api', '[]', 0, '2025-11-14 06:07:39', '2025-11-14 06:07:39', '2026-11-14 13:07:39'),
('3d6d20341c75939052689aa80982643998958471442085bc1c1d3baee00b2bb2202a6dbb0dd3e96f', 1, 1, 'api', '[]', 0, '2025-11-27 03:34:11', '2025-11-27 03:34:11', '2026-11-27 10:34:11'),
('66c10e6a705c03a4486173cb16eb78008e28bbb78545064725c5cc565cef34d9f357f295352af216', 1, 1, 'api', '[]', 0, '2025-12-24 09:22:43', '2025-12-24 09:22:43', '2026-12-24 16:22:43'),
('8e656f16c69118119a85f9ebd1aa8cb23894e2da5177e21677b2adbdc586da6b9b5c7fae74d83538', 1, 1, 'api', '[]', 0, '2025-11-14 03:06:43', '2025-11-14 03:06:43', '2026-11-14 10:06:43'),
('9034daeebf67a310490e4798ba823fc025ceb4219c1da557de6d12c35f7578ea23c6facd44204965', 1, 1, 'api', '[]', 0, '2025-12-29 08:45:05', '2025-12-29 08:45:05', '2026-12-29 15:45:05'),
('95f5bf0fc5277b54be500ffb4512334cdb5165945cad503a0d95a29dc03bb943d2ae0337a52377ef', 1, 1, 'api', '[]', 0, '2026-02-09 09:18:14', '2026-02-09 09:18:14', '2026-08-09 16:18:14'),
('a616c3fb27793d14e7790db446b488188b215f3ac9c0328a416ec2a2d23a0bdec2df22cffcb3ae0f', 1, 1, 'api', '[]', 0, '2025-11-14 04:16:55', '2025-11-14 04:16:55', '2026-11-14 11:16:55'),
('ab71d8081acfc968c67177818367c87859a7d05e38d4e0d7b20d262ccf0e386269b3d54e69aa17d2', 1, 1, 'api', '[]', 0, '2026-01-02 16:41:18', '2026-01-02 16:41:18', '2027-01-02 23:41:18'),
('c231262dbe7b5281099bcf0f1b1734d91848de6a09ba319d8d9db3b9eaf5c63ae7694d5ed8e2c362', 1, 1, 'api', '[]', 0, '2025-11-27 09:37:57', '2025-11-27 09:37:57', '2026-11-27 16:37:57'),
('c54627fe2f439040d0aa669c57cbfdceb6b6e48ebe2573f00b59010f05b94cb0448a2d4a1a7d682f', 1, 1, 'api', '[]', 0, '2025-11-14 05:24:08', '2025-11-14 05:24:08', '2026-11-14 12:24:08');

-- --------------------------------------------------------

--
-- Table structure for table `lr_oauth_auth_codes`
--

CREATE TABLE `lr_oauth_auth_codes` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `client_id` bigint UNSIGNED NOT NULL,
  `scopes` text COLLATE utf8mb4_unicode_ci,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lr_oauth_clients`
--

CREATE TABLE `lr_oauth_clients` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `secret` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `provider` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `redirect` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `personal_access_client` tinyint(1) NOT NULL,
  `password_client` tinyint(1) NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lr_oauth_clients`
--

INSERT INTO `lr_oauth_clients` (`id`, `user_id`, `name`, `secret`, `provider`, `redirect`, `personal_access_client`, `password_client`, `revoked`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Laravel Personal Access Client', 'cbWO0VQXmRYfa2V0AEbBpsnWeJGcXoZEQpRmmdc3', NULL, 'http://localhost', 1, 0, 0, '2024-05-16 21:55:15', '2024-05-16 21:55:15'),
(2, NULL, 'Laravel Password Grant Client', 'rwcNywwYRoPRcOzv3COakTd8XxiGmyp2KzrFfmUt', 'users', 'http://localhost', 0, 1, 0, '2024-05-16 21:55:15', '2024-05-16 21:55:15');

-- --------------------------------------------------------

--
-- Table structure for table `lr_oauth_personal_access_clients`
--

CREATE TABLE `lr_oauth_personal_access_clients` (
  `id` bigint UNSIGNED NOT NULL,
  `client_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lr_oauth_personal_access_clients`
--

INSERT INTO `lr_oauth_personal_access_clients` (`id`, `client_id`, `created_at`, `updated_at`) VALUES
(1, 1, '2024-05-16 21:55:15', '2024-05-16 21:55:15');

-- --------------------------------------------------------

--
-- Table structure for table `lr_oauth_refresh_tokens`
--

CREATE TABLE `lr_oauth_refresh_tokens` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `access_token_id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lr_page_builder`
--

CREATE TABLE `lr_page_builder` (
  `id` bigint NOT NULL,
  `user_id` bigint NOT NULL DEFAULT '0',
  `uri` varchar(255) DEFAULT NULL,
  `page_name` varchar(255) DEFAULT NULL,
  `custom_css` text,
  `vars` json NOT NULL,
  `status` enum('publish','not_active','draft') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'draft',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `lr_page_builder`
--

INSERT INTO `lr_page_builder` (`id`, `user_id`, `uri`, `page_name`, `custom_css`, `vars`, `status`, `updated_at`, `created_at`) VALUES
(1, 1, 'home', 'Home', '.testing\n{\n  font-size: 18px;\n  font-weight: bold;\n}', '[{\"id\": 1771902746427, \"type\": \"container\", \"fluid\": false, \"styles\": {\"bgPos\": \"center\", \"bgSize\": \"cover\", \"bgColor\": \"\", \"bgImage\": \"\", \"marginTop\": \"0\", \"minHeight\": \"auto\", \"paddingTop\": \"3rem\", \"marginBottom\": \"0\", \"paddingBottom\": \"3rem\"}, \"class4K\": \"container\", \"children\": [{\"id\": \"row-1771902747570\", \"type\": \"row\", \"gutter\": \"\", \"width4K\": \"w-3xl-100\", \"children\": [{\"id\": \"col-17719027475701\", \"span\": 12, \"type\": \"column\", \"width4K\": \"col-3xl-12\", \"children\": [{\"id\": 1771902752645, \"icon\": \"fas fa-paragraph\", \"type\": \"text\", \"label\": \"Text\", \"content\": \"<p>Lorem ipsum dolor sit amet...</p>\"}], \"widthFHD\": \"col-xxl-12\", \"customClass\": \"\", \"widthMobile\": \"col-12\", \"widthTablet\": \"col-md-12\", \"widthDesktop\": \"col-xl-12\"}], \"gutter4K\": \"\", \"widthFHD\": \"w-xxl-100\", \"gutterFHD\": \"\", \"customClass\": \"\", \"widthMobile\": \"w-100\", \"widthTablet\": \"w-md-100\", \"gutterTablet\": \"\", \"widthDesktop\": \"w-xl-100\", \"gutterDesktop\": \"\"}], \"classFHD\": \"container\", \"classMobile\": \"container-fluid\", \"classTablet\": \"container\", \"customClass\": \"\", \"classDesktop\": \"container\"}]', 'draft', '2026-02-24 03:12:58', '2026-02-24 03:12:58'),
(3, 1, 'homepage', 'Homepage', '.testing\r\n{\r\n  font-size: 18px;\r\n  font-weight: bold;\r\n}\r\n\r\n.testing2\r\n{\r\n  font-size: 22px;\r\n  font-weight: normal;\r\n}', '[{\"id\": 1771902746427, \"type\": \"container\", \"fluid\": false, \"styles\": {\"bgPos\": \"center\", \"bgSize\": \"cover\", \"bgColor\": \"\", \"bgImage\": \"\", \"marginTop\": \"0\", \"minHeight\": \"auto\", \"paddingTop\": \"3rem\", \"marginBottom\": \"0\", \"paddingBottom\": \"3rem\"}, \"class4K\": \"container\", \"children\": [{\"id\": \"row-1771902747570\", \"type\": \"row\", \"gutter\": \"\", \"width4K\": \"w-3xl-100\", \"children\": [{\"id\": \"col-17719027475701\", \"span\": 12, \"type\": \"column\", \"width4K\": \"col-3xl-12\", \"children\": [{\"id\": 1771902752645, \"icon\": \"fas fa-paragraph\", \"type\": \"text\", \"label\": \"Text\", \"content\": \"<p>Lorem ipsum dolor sit amet...</p>\"}], \"widthFHD\": \"col-xxl-12\", \"customClass\": \"\", \"widthMobile\": \"col-12\", \"widthTablet\": \"col-md-12\", \"widthDesktop\": \"col-xl-12\"}], \"gutter4K\": \"\", \"widthFHD\": \"w-xxl-100\", \"gutterFHD\": \"\", \"customClass\": \"\", \"widthMobile\": \"w-100\", \"widthTablet\": \"w-md-100\", \"gutterTablet\": \"\", \"widthDesktop\": \"w-xl-100\", \"gutterDesktop\": \"\"}], \"classFHD\": \"container\", \"classMobile\": \"container-fluid\", \"classTablet\": \"container\", \"customClass\": \"\", \"classDesktop\": \"container\"}]', 'not_active', '2026-02-24 09:49:09', '2026-02-24 04:23:33');

-- --------------------------------------------------------

--
-- Table structure for table `lr_page_themes`
--

CREATE TABLE `lr_page_themes` (
  `id` bigint NOT NULL,
  `uri` varchar(155) DEFAULT NULL,
  `theme_group` varchar(155) DEFAULT NULL,
  `theme_code` varchar(55) DEFAULT NULL,
  `theme_name` varchar(155) DEFAULT NULL,
  `theme_preview_image` text,
  `is_active_color_nuances` tinyint NOT NULL DEFAULT '1' COMMENT ' 0 is enable, 1 is disabled, default value 1',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `lr_page_themes`
--

INSERT INTO `lr_page_themes` (`id`, `uri`, `theme_group`, `theme_code`, `theme_name`, `theme_preview_image`, `is_active_color_nuances`, `updated_at`, `created_at`) VALUES
(1, 'login', 'auth', 'default', 'Default', 'storage/page_themes/auth/default/default_login_view.png', 0, '2025-07-21 19:35:27', '2025-07-21 19:35:27'),
(2, 'signup', 'auth', 'default', 'Default', 'storage/page_themes/auth/default/default_signup_view.png', 0, '2025-07-21 19:35:27', '2025-07-21 19:35:27'),
(3, 'forgotpassword', 'auth', 'default', 'Default', 'storage/page_themes/auth/default/default_forgotpassword_view.png', 0, '2025-07-21 19:35:27', '2025-07-21 19:35:27'),
(4, 'resetpassword', 'auth', 'default', 'Default', 'storage/page_themes/auth/default/default_recoverypassword_view.png', 0, '2025-07-21 19:35:27', '2025-07-21 19:35:27'),
(5, 'login', 'auth', 'split_left', 'Split Left View', 'storage/page_themes/auth/split_left/split_left_login_view.png', 1, '2025-07-21 19:35:27', '2025-07-21 19:35:27'),
(6, 'signup', 'auth', 'split_left', 'Split Left View', 'storage/page_themes/auth/split_left/split_left_signup_view.png', 1, '2025-07-21 19:35:27', '2025-07-21 19:35:27'),
(7, 'forgotpassword', 'auth', 'split_left', 'Split Left View', 'storage/page_themes/auth/split_left/split_left_forgotpassword_view.png', 1, '2025-07-21 19:35:27', '2025-07-21 19:35:27'),
(8, 'resetpassword', 'auth', 'split_left', 'Split Left View', 'storage/page_themes/auth/split_left/split_left_recoverypassword_view.png', 1, '2025-07-21 19:35:27', '2025-07-21 19:35:27'),
(9, 'login', 'auth', 'split_right', 'Split Right View', 'storage/page_themes/auth/split_right/split_right_login_view.png', 1, '2025-07-21 19:35:27', '2025-07-21 19:35:27'),
(10, 'signup', 'auth', 'split_right', 'Split Right View', 'storage/page_themes/auth/split_right/split_right_signup_view.png', 1, '2025-07-21 19:35:27', '2025-07-21 19:35:27'),
(11, 'forgotpassword', 'auth', 'split_right', 'Split Right View', 'storage/page_themes/auth/split_right/split_right_forgotpassword_view.png', 1, '2025-07-21 19:35:27', '2025-07-21 19:35:27'),
(12, 'resetpassword', 'auth', 'split_right', 'Split Right View', 'storage/page_themes/auth/split_right/split_right_recoverypassword_view.png', 1, '2025-07-21 19:35:27', '2025-07-21 19:35:27'),
(13, 'login', 'auth', 'card', 'Card View', 'storage/page_themes/auth/card/card_login_view.png', 0, '2025-07-21 19:35:27', '2025-07-21 19:35:27'),
(14, 'signup', 'auth', 'card', 'Card View', 'storage/page_themes/auth/card/card_signup_view.png', 0, '2025-07-21 19:35:27', '2025-07-21 19:35:27'),
(15, 'forgotpassword', 'auth', 'card', 'Card View', 'storage/page_themes/auth/card/card_forgotpassword_view.png', 0, '2025-07-21 19:35:27', '2025-07-21 19:35:27'),
(16, 'resetpassword', 'auth', 'card', 'Card View', 'storage/page_themes/auth/card/card_recoverypassword_view.png', 0, '2025-07-21 19:35:27', '2025-07-21 19:35:27');

-- --------------------------------------------------------

--
-- Table structure for table `lr_page_theme_settings`
--

CREATE TABLE `lr_page_theme_settings` (
  `id` bigint NOT NULL,
  `uri` varchar(155) DEFAULT NULL,
  `page_name` varchar(155) DEFAULT NULL,
  `page_theme` varchar(55) DEFAULT NULL,
  `page_color_nuances` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `page_background_image` text,
  `is_active_color_nuances` tinyint NOT NULL DEFAULT '1' COMMENT '0 is enable, 1 is disabled, default value 1',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `lr_page_theme_settings`
--

INSERT INTO `lr_page_theme_settings` (`id`, `uri`, `page_name`, `page_theme`, `page_color_nuances`, `page_background_image`, `is_active_color_nuances`, `updated_at`, `created_at`) VALUES
(1, 'login', 'Login', 'default', '#1fa6759f', 'images/page_themes/082025/date_05/e1ad653cbc3c3da22f8270e6054e597a.jpg', 1, '2025-07-24 08:58:41', '2025-07-24 08:58:41'),
(2, 'signup', 'Signup', 'split_left', '#1fa6759f', 'images/page_themes/082025/date_05/535d6dc7141b8be51ae359f992d3813f.jpg', 1, '2025-07-24 09:00:13', '2025-07-24 09:00:13'),
(3, 'forgotPassword', 'Forgot Password', 'split_left', '#1fa6759f', 'images/page_themes/082025/date_05/9c00282725c642ebd5aeaf6cf06a75cf.png', 1, '2025-07-24 09:00:13', '2025-07-24 09:00:13'),
(4, 'resetPassword', 'Reset Password', 'split_left', '#1fa6759f', 'images/page_themes/082025/date_05/6dccdc76d026c511a093aaf710bc162b.png', 1, '2025-07-24 09:00:13', '2025-07-24 09:00:13');

-- --------------------------------------------------------

--
-- Table structure for table `lr_password_reset_tokens`
--

CREATE TABLE `lr_password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lr_permissions`
--

CREATE TABLE `lr_permissions` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lr_permissions`
--

INSERT INTO `lr_permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'read data', 'web', '2025-01-24 01:22:30', '2025-05-07 23:36:54'),
(2, 'add data', 'web', '2025-01-24 01:22:35', '2025-01-24 01:22:35'),
(3, 'edit data', 'web', '2025-01-24 01:22:38', '2025-01-24 01:22:38'),
(4, 'delete data', 'web', '2025-01-24 01:22:42', '2025-01-24 01:22:42');

-- --------------------------------------------------------

--
-- Table structure for table `lr_personal_access_tokens`
--

CREATE TABLE `lr_personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lr_roles`
--

CREATE TABLE `lr_roles` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lr_roles`
--

INSERT INTO `lr_roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'web', '2025-01-24 01:23:02', '2025-01-24 01:23:02'),
(2, 'Administrator', 'web', '2025-01-24 01:23:09', '2025-01-24 01:23:09'),
(3, 'General Member', 'web', '2025-01-24 01:23:31', '2025-01-24 01:23:31'),
(4, 'Premium Member', 'web', '2025-01-24 01:23:38', '2025-01-24 01:23:38'),
(26, 'Sales', 'web', '2026-01-08 08:57:08', '2026-01-08 08:57:08');

-- --------------------------------------------------------

--
-- Table structure for table `lr_role_has_permissions`
--

CREATE TABLE `lr_role_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lr_role_has_permissions`
--

INSERT INTO `lr_role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(1, 2),
(2, 2),
(3, 2),
(4, 2),
(1, 3),
(2, 3),
(3, 3),
(4, 3),
(1, 4),
(2, 4),
(3, 4),
(4, 4);

-- --------------------------------------------------------

--
-- Table structure for table `lr_sessions`
--

CREATE TABLE `lr_sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lr_sessions`
--

INSERT INTO `lr_sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('7qJEdeH40HoXqqiBJcV9891huWSVA4nwrVVVDNfA', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiRE9qV0ZxZjB3VEdmdjBTck1jREdtZXRxQlFFSHo3N0VsSGRuZkd2VSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjA6Imh0dHBzOi8vbGFyYXZlbC0xMS1waG9lbml4LW9yaWdpbmFsLXJld3JpdGUuYXJ1bmEvYXV0aC9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NDoibGluayI7czo1OToiaHR0cHM6Ly9sYXJhdmVsLTExLXBob2VuaXgtb3JpZ2luYWwtcmV3cml0ZS5hcnVuYS9kYXNoYm9hcmQiO30=', 1755165689),
('qMYgx3lWu3NQNWs7OEvJWsv8uDPhbeWIbXLo4Gho', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicjJwVUxSSXh2SW5CcVVzVk9adWVOWTNtNVB2UGtLZ0s3TWY5UU8yNCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Nzk6Imh0dHBzOi8vbGFyYXZlbC0xMS1waG9lbml4LW9yaWdpbmFsLXJld3JpdGUuYXJ1bmEvYXdlc29tZV9hZG1pbi9jb25maWcvbGlzdGRhdGEiO3M6NToicm91dGUiO3M6Mzk6ImNtcy5hZG1pbi5hd2Vzb21lX2FkbWluLmNvbmZpZy5saXN0ZGF0YSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1762625553);

-- --------------------------------------------------------

--
-- Table structure for table `lr_site_config`
--

CREATE TABLE `lr_site_config` (
  `id` int NOT NULL,
  `site_url` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT 'http://127.0.0.1:8000',
  `site_name` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT 'LaraPhoenix',
  `site_slogan` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT 'The Most Powerful CMS for Laravel',
  `site_keyword` varchar(155) DEFAULT NULL,
  `site_description` varchar(155) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `site_thumbnail` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `font_family` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT 'Nunito',
  `font_size` int NOT NULL DEFAULT '14',
  `footer_message1` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci,
  `footer_message2` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci,
  `signup_closed` tinyint NOT NULL DEFAULT '1' COMMENT '0 is enable and 1 is disable by default',
  `offline_mode` tinyint NOT NULL DEFAULT '1' COMMENT '0 is enable and 1 is disable by default',
  `offline_reason` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci,
  `login_multiple_device` tinyint NOT NULL DEFAULT '0',
  `management_menu` varchar(155) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT 'v1',
  `gmaps_api_key` varchar(155) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `recaptcha_site_key` varchar(155) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `recaptcha_secret_key` varchar(155) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `enable_recaptcha_signin` tinyint NOT NULL DEFAULT '1' COMMENT '0 is enable and 1 is disable by default',
  `enable_recaptcha_signup` tinyint NOT NULL DEFAULT '1' COMMENT '0 is enable and 1 is disable by default',
  `enable_autogen_username_signup` tinyint NOT NULL DEFAULT '1' COMMENT ' 0 is enable and 1 is disable by default ',
  `enable_ratelimit_login` tinyint NOT NULL DEFAULT '1',
  `amount_ratelimit_login` int NOT NULL DEFAULT '0',
  `enable_ratelimit_signup` tinyint NOT NULL DEFAULT '1',
  `amount_ratelimit_signup` int NOT NULL DEFAULT '0',
  `time_ratelimit_global` int NOT NULL DEFAULT '60' COMMENT 'Time rate limit per minute',
  `is_sso_activated` tinyint NOT NULL DEFAULT '1' COMMENT '0 is enable and 1 is disable by default',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `lr_site_config`
--

INSERT INTO `lr_site_config` (`id`, `site_url`, `site_name`, `site_slogan`, `site_keyword`, `site_description`, `site_thumbnail`, `font_family`, `font_size`, `footer_message1`, `footer_message2`, `signup_closed`, `offline_mode`, `offline_reason`, `login_multiple_device`, `management_menu`, `gmaps_api_key`, `recaptcha_site_key`, `recaptcha_secret_key`, `enable_recaptcha_signin`, `enable_recaptcha_signup`, `enable_autogen_username_signup`, `enable_ratelimit_login`, `amount_ratelimit_login`, `enable_ratelimit_signup`, `amount_ratelimit_signup`, `time_ratelimit_global`, `is_sso_activated`, `updated_at`, `created_at`) VALUES
(1, 'https://cms.tvindo.net', 'LaraPhoenix CMS', 'Simple, Beautiful & Elegant Framework PHP', 'Simple, Beautiful & Elegant Framework PHP', 'Simple, Beautiful & Elegant Framework PHP', 'thumbnail_668f97d07314a.jpg', 'nunito', 14, 'CMS Laravel &copy; 2024', '', 0, 1, 'Sorry we are under maintenance, please come back later', 0, 'v2', '', '6Lf8hlgrAAAAAPELEMbrwo66ym8mA6DNrrDHZ0NY', '6Lf8hlgrAAAAAGSQBGJVsslfZXYyLU2AsfXKrZ4v', 1, 1, 1, 0, 7, 0, 7, 60, 1, '2026-02-20 16:51:47', '2025-01-20 02:55:03');

-- --------------------------------------------------------

--
-- Table structure for table `lr_smtp_service`
--

CREATE TABLE `lr_smtp_service` (
  `id` int NOT NULL,
  `service_id` int NOT NULL DEFAULT '0',
  `service_name` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `lr_smtp_service`
--

INSERT INTO `lr_smtp_service` (`id`, `service_id`, `service_name`) VALUES
(1, 89, 'SMTP2GO TLS');

-- --------------------------------------------------------

--
-- Table structure for table `lr_smtp_settings`
--

CREATE TABLE `lr_smtp_settings` (
  `id` int NOT NULL,
  `smtp_service` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `smtp_host` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `smtp_sender_name` varchar(62) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `smtp_sender_address` varchar(155) DEFAULT NULL,
  `smtp_username` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `smtp_password` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `smtp_port` int DEFAULT '0',
  `smtp_encryption` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `lr_smtp_settings`
--

INSERT INTO `lr_smtp_settings` (`id`, `smtp_service`, `smtp_host`, `smtp_sender_name`, `smtp_sender_address`, `smtp_username`, `smtp_password`, `smtp_port`, `smtp_encryption`) VALUES
(1, 'Mailjet', 'in-v3.mailjet.com', 'Noreply Aruna Dev', 'noreply@aruna-dev.com', 'fe10820f07fa2a86b96cf7176aedd3aa', '95d84b5231b7924b7d306505eb3d64d4', 587, 'tls'),
(2, 'SMTP2GO SSL', 'mail.smtp2go.com', 'Noreply Aruna Dev', 'noreply@aruna-dev.com', 'noreply@aruna-dev.com', 'AUSjtkcmnOwieSOj', 465, 'ssl'),
(89, 'SMTP2GO TLS', 'mail.smtp2go.com', 'Noreply Aruna Dev', 'noreply@aruna-dev.com', 'noreply@aruna-dev.com', 'AUSjtkcmnOwieSOj', 587, 'tls');

-- --------------------------------------------------------

--
-- Table structure for table `lr_testings`
--

CREATE TABLE `lr_testings` (
  `id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lr_themes`
--

CREATE TABLE `lr_themes` (
  `id` bigint UNSIGNED NOT NULL,
  `theme_code` varchar(32) DEFAULT NULL,
  `theme_name` varchar(255) DEFAULT NULL,
  `theme_foldername` varchar(155) DEFAULT NULL,
  `theme_cms` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Value this field is filename theme',
  `theme_auth` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Value this field is filename theme',
  `theme_frontend` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Value this field is filename theme',
  `theme_version` varchar(12) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `lr_themes`
--

INSERT INTO `lr_themes` (`id`, `theme_code`, `theme_name`, `theme_foldername`, `theme_cms`, `theme_auth`, `theme_frontend`, `theme_version`, `updated_at`, `created_at`) VALUES
(1, 'default', 'Default Theme', 'default', 'cms_layout', 'auth_layout', 'frontend_layout', '1.0.0', '2025-06-18 03:54:21', '2025-06-18 03:54:21'),
(2, 'simple', 'Simple Theme', 'simple', 'cms_layout', 'auth_layout', 'frontend_layout', '1.0.0', '2025-06-18 03:54:21', '2025-06-18 03:54:21'),
(3, 'simple_part_2', 'Simple Part 2 Theme', 'simple_part_2', 'cms_layout', 'auth_layout', 'frontend_layout', '1.0.0', '2025-06-18 03:54:21', '2025-06-18 03:54:21'),
(4, 'calm_green', 'Calm Green Theme', 'calm_green', 'cms_layout', 'auth_layout', 'frontend_layout', '1.0.0', '2025-06-18 03:54:21', '2025-06-18 03:54:21'),
(5, 'arunika_v1', 'Arunika V1 Theme', 'arunika_v1', 'cms_layout', 'auth_layout', 'frontend_layout', '1.0.0', '2025-06-18 03:54:21', '2025-06-18 03:54:21');

-- --------------------------------------------------------

--
-- Table structure for table `lr_theme_settings`
--

CREATE TABLE `lr_theme_settings` (
  `id` bigint UNSIGNED NOT NULL,
  `theme_id` bigint UNSIGNED NOT NULL,
  `theme_code` varchar(155) NOT NULL,
  `theme_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `lr_theme_settings`
--

INSERT INTO `lr_theme_settings` (`id`, `theme_id`, `theme_code`, `theme_name`) VALUES
(1, 5, 'arunika_v1', 'Arunika v1 Theme');

-- --------------------------------------------------------

--
-- Table structure for table `lr_users`
--

CREATE TABLE `lr_users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lr_user_information`
--

CREATE TABLE `lr_user_information` (
  `id` int NOT NULL,
  `user_id` int NOT NULL DEFAULT '0',
  `avatar` varchar(155) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `cover_image` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `cover_image_position` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `birthdate` varchar(12) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `gender` tinyint NOT NULL DEFAULT '0',
  `phone_number` varchar(16) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `about` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `lr_user_information`
--

INSERT INTO `lr_user_information` (`id`, `user_id`, `avatar`, `cover_image`, `cover_image_position`, `birthdate`, `gender`, `phone_number`, `about`) VALUES
(1, 1, 'avatars/122025/date_24/dbfbf0ff8ff3d5e9150794e3ca632490.png', '', '', '1996-06-19', 1, '0895-0451-8506', 'Hanya seorang pegawai biasa yang mempunyai pengalaman yang tidak biasa.'),
(2, 2, 'contents/userfiles/avatars/202303/15460ec4c8cb106f564a74275a83a56e.png', '', '', '19/06/1996', 1, '0895-0451-8506', ''),
(3, 3, '', '', '', '', 0, '', ''),
(4, 4, '', '', '', '', 0, '', ''),
(5, 5, '', '', '', '', 0, '', ''),
(6, 6, '', '', '', '', 0, '', ''),
(7, 7, '', '', '', '', 0, '', ''),
(8, 8, '', '', '', '', 0, '', ''),
(9, 9, '', '', '', '', 0, '', ''),
(10, 10, '', '', '', '', 0, '', ''),
(11, 11, '', '', '', '', 0, '', ''),
(12, 12, '', '', '', '', 0, '', ''),
(13, 13, '', '', '', '', 0, '', ''),
(14, 14, '', '', '', '', 0, '', ''),
(15, 15, '', '', '', '', 0, '', ''),
(16, 16, '', '', '', '19/06/1996', 1, '0895-0451-8506', ''),
(17, 17, '', '', '', '', 0, '', ''),
(18, 18, '', '', '', '', 0, '', ''),
(19, 45, NULL, NULL, NULL, NULL, 0, NULL, NULL),
(20, 46, NULL, NULL, NULL, NULL, 0, NULL, NULL),
(21, 47, NULL, NULL, NULL, NULL, 0, NULL, NULL),
(22, 48, NULL, NULL, NULL, NULL, 0, NULL, NULL),
(23, 49, NULL, NULL, NULL, NULL, 0, NULL, NULL),
(24, 50, NULL, NULL, NULL, NULL, 0, NULL, NULL),
(31, 58, NULL, NULL, NULL, NULL, 0, NULL, NULL),
(32, 59, NULL, NULL, NULL, NULL, 0, NULL, NULL),
(33, 60, NULL, NULL, NULL, NULL, 0, NULL, NULL),
(34, 61, NULL, NULL, NULL, NULL, 0, NULL, NULL),
(35, 62, NULL, NULL, NULL, NULL, 0, NULL, NULL),
(36, 63, NULL, NULL, NULL, NULL, 0, NULL, NULL),
(37, 64, NULL, NULL, NULL, NULL, 0, NULL, NULL),
(38, 65, NULL, NULL, NULL, NULL, 0, NULL, NULL),
(39, 66, NULL, NULL, NULL, NULL, 0, NULL, NULL),
(40, 67, NULL, NULL, NULL, NULL, 0, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `lr_accounts`
--
ALTER TABLE `lr_accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `email` (`email`),
  ADD KEY `fullname` (`fullname`),
  ADD KEY `status` (`status`),
  ADD KEY `token` (`token`);

--
-- Indexes for table `lr_account_login_history`
--
ALTER TABLE `lr_account_login_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `lr_account_status`
--
ALTER TABLE `lr_account_status`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lr_articles`
--
ALTER TABLE `lr_articles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cid` (`category_id`),
  ADD KEY `subcid` (`subcategory_id`),
  ADD KEY `title` (`title`),
  ADD KEY `schedule_pub` (`scheduled`),
  ADD KEY `uri` (`uri`),
  ADD KEY `subcategory_id` (`subcategory_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `status` (`status`),
  ADD KEY `visibility` (`visibility`);

--
-- Indexes for table `lr_article_categories`
--
ALTER TABLE `lr_article_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `name` (`name`),
  ADD KEY `code` (`code`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `lr_article_status`
--
ALTER TABLE `lr_article_status`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lr_article_subcategories`
--
ALTER TABLE `lr_article_subcategories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cid` (`category_id`),
  ADD KEY `name` (`name`),
  ADD KEY `code` (`code`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `lr_authentication_log`
--
ALTER TABLE `lr_authentication_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lr_cache`
--
ALTER TABLE `lr_cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `lr_cache_locks`
--
ALTER TABLE `lr_cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `lr_cover_image`
--
ALTER TABLE `lr_cover_image`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uri` (`uri`),
  ADD KEY `cover_type` (`cover_type`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `lr_credits`
--
ALTER TABLE `lr_credits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lr_credits_creditable_type_creditable_id_index` (`creditable_type`,`creditable_id`),
  ADD KEY `lr_credits_type_index` (`type`),
  ADD KEY `lr_credits_created_at_index` (`created_at`),
  ADD KEY `lr_credits_creditable_id_creditable_type_created_at_index` (`creditable_id`,`creditable_type`,`created_at`),
  ADD KEY `lr_credits_creditable_id_creditable_type_running_balance_index` (`creditable_id`,`creditable_type`,`running_balance`);

--
-- Indexes for table `lr_custom_permissions`
--
ALTER TABLE `lr_custom_permissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `role_id` (`role_id`),
  ADD KEY `menu_link` (`menu_link`),
  ADD KEY `menu_code` (`menu_code`),
  ADD KEY `parent_code` (`parent_code`),
  ADD KEY `category_code` (`category_code`),
  ADD KEY `menu_type` (`menu_type`);

--
-- Indexes for table `lr_failed_jobs`
--
ALTER TABLE `lr_failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lr_failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `lr_jobs`
--
ALTER TABLE `lr_jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lr_jobs_queue_index` (`queue`);

--
-- Indexes for table `lr_job_batches`
--
ALTER TABLE `lr_job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lr_language`
--
ALTER TABLE `lr_language`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lang` (`lang`);

--
-- Indexes for table `lr_menu`
--
ALTER TABLE `lr_menu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `module` (`module`);

--
-- Indexes for table `lr_menu_categorymenu_json`
--
ALTER TABLE `lr_menu_categorymenu_json`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_code` (`menu_page`);

--
-- Indexes for table `lr_menu_parent`
--
ALTER TABLE `lr_menu_parent`
  ADD PRIMARY KEY (`id`),
  ADD KEY `module` (`module`);

--
-- Indexes for table `lr_menu_parentmenu_json`
--
ALTER TABLE `lr_menu_parentmenu_json`
  ADD PRIMARY KEY (`id`),
  ADD KEY `menu_page` (`menu_page`);

--
-- Indexes for table `lr_menu_submenu_json`
--
ALTER TABLE `lr_menu_submenu_json`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_code` (`parent_code`);

--
-- Indexes for table `lr_migrations`
--
ALTER TABLE `lr_migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lr_model_has_permissions`
--
ALTER TABLE `lr_model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `lr_model_has_roles`
--
ALTER TABLE `lr_model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `lr_notifications`
--
ALTER TABLE `lr_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `from_id` (`from_id`),
  ADD KEY `to_id` (`to_id`),
  ADD KEY `type` (`type`),
  ADD KEY `hasread` (`hasread`);

--
-- Indexes for table `lr_oauth_access_tokens`
--
ALTER TABLE `lr_oauth_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lr_oauth_access_tokens_user_id_index` (`user_id`);

--
-- Indexes for table `lr_oauth_auth_codes`
--
ALTER TABLE `lr_oauth_auth_codes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lr_oauth_auth_codes_user_id_index` (`user_id`);

--
-- Indexes for table `lr_oauth_clients`
--
ALTER TABLE `lr_oauth_clients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lr_oauth_clients_user_id_index` (`user_id`);

--
-- Indexes for table `lr_oauth_personal_access_clients`
--
ALTER TABLE `lr_oauth_personal_access_clients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lr_oauth_refresh_tokens`
--
ALTER TABLE `lr_oauth_refresh_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lr_oauth_refresh_tokens_access_token_id_index` (`access_token_id`);

--
-- Indexes for table `lr_page_builder`
--
ALTER TABLE `lr_page_builder`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `uri` (`uri`);

--
-- Indexes for table `lr_page_themes`
--
ALTER TABLE `lr_page_themes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uri` (`uri`),
  ADD KEY `theme_group` (`theme_group`),
  ADD KEY `is_active_color_nuances` (`is_active_color_nuances`);

--
-- Indexes for table `lr_page_theme_settings`
--
ALTER TABLE `lr_page_theme_settings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uri` (`uri`),
  ADD KEY `is_active_color_nuances` (`is_active_color_nuances`);

--
-- Indexes for table `lr_password_reset_tokens`
--
ALTER TABLE `lr_password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `lr_permissions`
--
ALTER TABLE `lr_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lr_permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `lr_personal_access_tokens`
--
ALTER TABLE `lr_personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lr_personal_access_tokens_token_unique` (`token`),
  ADD KEY `lr_personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `lr_roles`
--
ALTER TABLE `lr_roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lr_roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `lr_role_has_permissions`
--
ALTER TABLE `lr_role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `lr_role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `lr_sessions`
--
ALTER TABLE `lr_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lr_sessions_user_id_index` (`user_id`),
  ADD KEY `lr_sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `lr_site_config`
--
ALTER TABLE `lr_site_config`
  ADD PRIMARY KEY (`id`),
  ADD KEY `management_menu` (`management_menu`);

--
-- Indexes for table `lr_smtp_service`
--
ALTER TABLE `lr_smtp_service`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service_name` (`service_name`),
  ADD KEY `service_id` (`service_id`);

--
-- Indexes for table `lr_smtp_settings`
--
ALTER TABLE `lr_smtp_settings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `smtp_service` (`smtp_service`);

--
-- Indexes for table `lr_testings`
--
ALTER TABLE `lr_testings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lr_themes`
--
ALTER TABLE `lr_themes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `theme_code` (`theme_code`);

--
-- Indexes for table `lr_theme_settings`
--
ALTER TABLE `lr_theme_settings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `theme_id` (`theme_id`),
  ADD KEY `theme_code` (`theme_code`);

--
-- Indexes for table `lr_users`
--
ALTER TABLE `lr_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lr_users_email_unique` (`email`);

--
-- Indexes for table `lr_user_information`
--
ALTER TABLE `lr_user_information`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `lr_accounts`
--
ALTER TABLE `lr_accounts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `lr_account_status`
--
ALTER TABLE `lr_account_status`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `lr_articles`
--
ALTER TABLE `lr_articles`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `lr_article_categories`
--
ALTER TABLE `lr_article_categories`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `lr_article_status`
--
ALTER TABLE `lr_article_status`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `lr_article_subcategories`
--
ALTER TABLE `lr_article_subcategories`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lr_authentication_log`
--
ALTER TABLE `lr_authentication_log`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=183;

--
-- AUTO_INCREMENT for table `lr_cover_image`
--
ALTER TABLE `lr_cover_image`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `lr_credits`
--
ALTER TABLE `lr_credits`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lr_custom_permissions`
--
ALTER TABLE `lr_custom_permissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `lr_failed_jobs`
--
ALTER TABLE `lr_failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lr_jobs`
--
ALTER TABLE `lr_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `lr_language`
--
ALTER TABLE `lr_language`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=743;

--
-- AUTO_INCREMENT for table `lr_menu`
--
ALTER TABLE `lr_menu`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `lr_menu_categorymenu_json`
--
ALTER TABLE `lr_menu_categorymenu_json`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `lr_menu_parent`
--
ALTER TABLE `lr_menu_parent`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `lr_menu_parentmenu_json`
--
ALTER TABLE `lr_menu_parentmenu_json`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `lr_menu_submenu_json`
--
ALTER TABLE `lr_menu_submenu_json`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `lr_migrations`
--
ALTER TABLE `lr_migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=165;

--
-- AUTO_INCREMENT for table `lr_notifications`
--
ALTER TABLE `lr_notifications`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `lr_oauth_clients`
--
ALTER TABLE `lr_oauth_clients`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `lr_oauth_personal_access_clients`
--
ALTER TABLE `lr_oauth_personal_access_clients`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `lr_page_builder`
--
ALTER TABLE `lr_page_builder`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `lr_page_themes`
--
ALTER TABLE `lr_page_themes`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `lr_page_theme_settings`
--
ALTER TABLE `lr_page_theme_settings`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `lr_permissions`
--
ALTER TABLE `lr_permissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `lr_personal_access_tokens`
--
ALTER TABLE `lr_personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lr_roles`
--
ALTER TABLE `lr_roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `lr_site_config`
--
ALTER TABLE `lr_site_config`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `lr_smtp_service`
--
ALTER TABLE `lr_smtp_service`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `lr_smtp_settings`
--
ALTER TABLE `lr_smtp_settings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT for table `lr_testings`
--
ALTER TABLE `lr_testings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lr_themes`
--
ALTER TABLE `lr_themes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `lr_theme_settings`
--
ALTER TABLE `lr_theme_settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `lr_users`
--
ALTER TABLE `lr_users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lr_user_information`
--
ALTER TABLE `lr_user_information`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `lr_model_has_permissions`
--
ALTER TABLE `lr_model_has_permissions`
  ADD CONSTRAINT `lr_model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `lr_permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lr_model_has_roles`
--
ALTER TABLE `lr_model_has_roles`
  ADD CONSTRAINT `lr_model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `lr_roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lr_role_has_permissions`
--
ALTER TABLE `lr_role_has_permissions`
  ADD CONSTRAINT `lr_role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `lr_permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lr_role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `lr_roles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
