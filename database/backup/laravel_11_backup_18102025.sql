-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 18, 2025 at 12:00 PM
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
(10, '', 'gumara@aruna-dev.id', 'gumara', 'Gumara Peto Alam', '$2y$10$XjReXV4PvTE1ZYpCIQgrL.65m5BehtOhFkmjnSKmI5cBzTJOs7ZPC', '', 2, '', NULL, '', NULL, NULL, '2024-10-07 00:58:53', '2025-05-17 05:31:47'),
(11, '', 'cyber.phoenix@aruna-dev.id', 'cyber_phoenix', 'Cyber Phoenix', '$2y$10$YdWG1tsalZCI/wBqbcUoluiSRGG8Rt9OT06ilBDadNOlEUYREOzh6', '', 2, '', NULL, '', NULL, NULL, '2024-07-09 02:25:00', '2025-05-17 05:31:47'),
(12, '', 'hazy@gmail.com', 'hazy', 'Hazy', '$2y$10$FGNs7EJioCPWjzRsOyOiseKaCQOP8OpDXwj4lFF2JFNSuTGXPVXGK', '', 2, '', NULL, '', NULL, NULL, '2024-07-09 02:25:00', '2025-05-17 05:31:47'),
(13, '', 'seven.lions@aruna-dev.id', 'seven_lions', 'Seven Lions', '$2y$10$cFtgjygnQ.uqVtRe3KtNe./38PYj72zY.qPtAgEE/HP.zOIR8mv22', '', 2, '', NULL, '', NULL, NULL, '2024-07-09 02:25:00', '2025-05-17 05:31:47'),
(14, '', 'illenium.ashes@aruna-dev.id', 'illenium', 'Illenium Ashes', '$2y$10$08fHik6WBl3H4aI5aJwlD.qDHA1Y2.VdLGs1KsPKFiqfD/r7Qdcj6', '', 2, '', NULL, '', NULL, NULL, '2024-07-09 02:25:00', '2025-05-17 05:31:47'),
(15, '', 'karina@aruna-dev.id', 'karina', 'Karina', '$2y$10$oF8bK8jB3oHmSojXVwDFn.QA2szbCl0X/TP6jh.epnjlng9.SQWL.', '', 2, '', NULL, '', NULL, NULL, '2024-07-09 02:25:00', '2025-05-17 05:31:47'),
(16, '', 'andhika.adhitia96@gmail.com', 'adhitia', 'Adhitia', '$2y$10$0ETFtB0IOu4xL1zwo0O3HewgOivmjZ9peZM.NKdlNWLXizavx.Jly', '', 2, '0Vjrf5Q2EZMgv1SxYuaKbF', 1748595974, '', NULL, NULL, '2025-05-30 09:01:14', '2025-05-17 05:31:47'),
(17, '', 'rfasyurapvp@gmail.com', 'asyura', 'RF Asyura', '$2y$10$tL16JDjpj3w5K3xxbtfz2.4iPTJin3r7wcL8RpjClzBWDdOIL3nse', '', 2, '', NULL, '', NULL, NULL, '2024-07-09 02:25:00', '2025-05-17 05:31:47'),
(18, '', 'gumara.peto@aruna-dev.id', 'gumara_peto_alam', 'Gumara Peto Alam', '$2y$10$1zuUYhahQ.1IaCp8owh1tOeTW99ZsjI/Dr1mKnxF3jmPdxC3kdWoe', '', 2, '', NULL, '', NULL, NULL, '2024-07-09 02:25:00', '2025-05-17 05:31:47'),
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
(66, 'cdab9b3a-edf9-43e5-8b2d-8e68fa2b0b4e', 'asdasd2', 'asdasd2', 'asdasd2', 'LaraPhoenixDev', NULL, 2, NULL, NULL, NULL, NULL, NULL, '2025-08-18 08:56:11', '2025-08-18 08:56:11');

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
(1, 'sambut-hari-kemerdekaan-morris-garages-hadirkan-promo-spesial-dan-mall-exhibition', 59, 1, 0, 'Sambut Hari Kemerdekaan, Morris Garages Hadirkan Promo Spesial dan Mall Exhibition', '<p>Hello World! Kwkwkwkwkwkw</p>', NULL, 'articles/082025/date_23/7aff8cf12f0beb7c80b30f9d2691c8d0_small.png', 'articles/082025/date_23/0f59cac7557f79647168fc0d8401f288.png', 'public', NULL, 'draft', 'true', '2025-08-23 06:42:35', '2025-08-25 02:30:00'),
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
(49, 'testing-testing-kwkwkwkwkwkw', 1, 1, 0, 'Testing Testing', '<p>ASDASDASD</p>', NULL, NULL, NULL, 'public', NULL, 'publish', 'false', '2025-08-21 19:06:25', '2025-08-21 19:06:25');

-- --------------------------------------------------------

--
-- Table structure for table `lr_article_categories`
--

CREATE TABLE `lr_article_categories` (
  `id` bigint NOT NULL,
  `name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `code` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lr_article_categories`
--

INSERT INTO `lr_article_categories` (`id`, `name`, `code`, `updated_at`, `created_at`) VALUES
(1, 'Uncategorized', 'uncategorized', '2025-08-19 02:49:39', '2025-08-19 02:49:39');

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
(82, 'App\\Models\\Awesome_Admin\\Account', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', '2025-10-18 11:27:06', 1, NULL, 0, '{\"ip\": \"127.0.0.0\", \"lat\": 41.31, \"lon\": -72.92, \"city\": \"New Haven\", \"state\": \"CT\", \"cached\": false, \"country\": \"United States\", \"default\": true, \"currency\": \"USD\", \"iso_code\": \"US\", \"timezone\": \"America/New_York\", \"continent\": \"NA\", \"state_name\": \"Connecticut\", \"postal_code\": \"06510\"}');

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

INSERT INTO `lr_cover_image` (`id`, `user_id`, `uri`, `cover_type`, `cover_page_name`, `cover_bgimage_vars`, `cover_slideshow_vars`, `cover_slideshow_direction`, `cover_autoplay_slideshow`, `cover_autoplay_slideshow_interval`, `cover_looping_slideshow`, `cover_is_active`, `updated_at`, `created_at`) VALUES
(1, 1, 'homepage', 'background_image', 'Homepage', '[{\"background_overlay\":\"rgba(0, 0, 0, 0.4)\",\"cover_is_active\":\"active\",\"disable_content\":\"inactive\",\"desktop_content_position\":\"center-center\",\"mobile_content_position\":\"center-center\",\"link\":{\"is_active\":\"inactive\",\"content\":null},\"countdown\":{\"is_active\":\"inactive\",\"content\":null},\"title\":\"Hello World\",\"description\":\"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque quis accumsan velit.\",\"button\":[{\"is_active\":\"active\",\"title\":\"Link 1\",\"link\":\"https:\\/\\/www.getbootstrap.com\"},{\"is_active\":\"active\",\"title\":\"Link 2\",\"link\":\"https:\\/\\/www.getbootstrap.com\"}],\"desktop_image\":\"coverimage\\/102025\\/date_03\\/c26c634bfa58ecbd5f3b0462e53aba89.jpg\",\"mobile_image\":\"coverimage\\/102025\\/date_03\\/8b3f3097e962618c2ff5e1bf0a9d5caa.jpg\"}]', NULL, 'horizontal', 'active', 3000, 'active', 'active', '2025-10-03 09:21:11', '2025-10-03 09:21:11'),
(2, 1, 'homepage2', 'slideshow', 'Homepage 2', '[{\"background_overlay\":\"rgba(0, 0, 0, 0.4)\",\"background_size\":\"md_size\",\"cover_is_active\":\"active\",\"disable_content\":\"inactive\",\"desktop_content_position\":\"center-center\",\"mobile_content_position\":\"center-center\",\"link\":{\"is_active\":\"inactive\",\"content\":null},\"countdown\":{\"is_active\":\"inactive\",\"content\":null},\"title\":\"Testing\",\"description\":null,\"button\":[{\"is_active\":\"active\",\"title\":null,\"link\":null},{\"is_active\":\"inactive\",\"title\":null,\"link\":null}],\"desktop_image\":\"coverimage\\/102025\\/date_07\\/8fd67759c6f8aeb74b22d1395045ed36.jpg\",\"mobile_image\":\"coverimage\\/102025\\/date_07\\/0385ffa38bf78201cde18367e2a5e75f.jpg\"}]', '[{\"background_overlay\":\"rgba(0, 0, 0, 0.4)\",\"background_size\":\"md_size\",\"cover_is_active\":\"active\",\"disable_content\":\"active\",\"desktop_content_position\":\"top-center\",\"mobile_content_position\":\"center-center\",\"link\":{\"is_active\":\"inactive\",\"content\":null},\"countdown\":{\"is_active\":\"inactive\",\"content\":null},\"title\":\"Testing 2\",\"description\":null,\"button\":[{\"is_active\":\"active\",\"title\":\"Link 1\",\"link\":null},{\"is_active\":\"active\",\"title\":\"Link 2\",\"link\":null}],\"desktop_image\":\"coverimage\\/102025\\/date_15\\/7a0c94c6cc06c3fbfae5c4a6d1b5f7fa.jpg\",\"mobile_image\":\"coverimage\\/102025\\/date_15\\/d28b90fcde41956c455a7095263c6d11.jpg\"},{\"background_overlay\":\"rgba(0, 0, 0, 0.4)\",\"background_size\":\"lg_size\",\"cover_is_active\":\"active\",\"disable_content\":\"active\",\"desktop_content_position\":\"bottom-center\",\"mobile_content_position\":\"center-center\",\"link\":{\"is_active\":\"inactive\",\"content\":null},\"countdown\":{\"is_active\":\"inactive\",\"content\":null},\"title\":\"Testing 3\",\"description\":null,\"button\":[{\"is_active\":\"active\",\"title\":\"asdasd\",\"link\":\"asdasdasd\"},{\"is_active\":\"active\",\"title\":null,\"link\":null}],\"desktop_image\":\"coverimage\\/102025\\/date_15\\/3b4dc2fabbf557329d6945aa88203966.jpg\",\"mobile_image\":\"coverimage\\/102025\\/date_15\\/0f9700821452200eb9eacbacc187e917.jpg\"},{\"background_overlay\":\"rgba(0, 0, 0, 0.4)\",\"background_size\":\"boi_size\",\"cover_is_active\":\"active\",\"disable_content\":\"inactive\",\"desktop_content_position\":\"center-center\",\"mobile_content_position\":\"center-center\",\"link\":{\"is_active\":\"active\",\"content\":\"https:\\/\\/arkenstone.my.id\"},\"countdown\":{\"is_active\":\"inactive\",\"content\":null},\"title\":\"Testing 1\",\"description\":\"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque quis accumsan velit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque quis accumsan velit.\",\"button\":[{\"is_active\":\"active\",\"title\":\"Link 1\",\"link\":\"https:\\/\\/getbootstrap.com\\/\"},{\"is_active\":\"inactive\",\"title\":\"Link 2\",\"link\":\"https:\\/\\/getbootstrap.com\\/\"}],\"desktop_image\":\"coverimage\\/102025\\/date_03\\/7871673b838e3651b3471d8254d7784d.jpg\",\"mobile_image\":\"coverimage\\/102025\\/date_03\\/336b6cc3249c1c8b994b5bcb39a7a168.jpg\"}]', 'horizontal', 'active', 3000, 'inactive', 'active', '2025-10-18 11:59:11', '2025-10-03 09:27:50'),
(3, 1, 'homepage3', 'slideshow', 'Homepage 3', NULL, '[{\"background_overlay\":\"rgba(0, 0, 0, 0.4)\",\"cover_is_active\":\"active\",\"disable_content\":\"inactive\",\"desktop_content_position\":\"center-left\",\"mobile_content_position\":\"center-left\",\"link\":{\"is_active\":\"inactive\",\"content\":null},\"countdown\":{\"is_active\":\"inactive\",\"content\":null},\"title\":\"Testing 3\",\"description\":\"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque quis accumsan velit.\",\"button\":[{\"is_active\":\"inactive\",\"title\":\"Link 1\",\"link\":\"https:\\/\\/getbootstrap.com\\/\"},{\"is_active\":\"inactive\",\"title\":\"Link 2\",\"link\":\"https:\\/\\/getbootstrap.com\\/\"}],\"desktop_image\":\"coverimage\\/102025\\/date_03\\/656a2afa6abda8f41a08887e0df3ed7f.jpg\",\"mobile_image\":\"coverimage\\/102025\\/date_03\\/7366f0242acdbee6ab8aba9a25df7aa3.jpg\"},{\"background_overlay\":\"rgba(0, 0, 0, 0.4)\",\"cover_is_active\":\"active\",\"disable_content\":\"inactive\",\"desktop_content_position\":\"center-center\",\"mobile_content_position\":\"center-center\",\"link\":{\"is_active\":\"inactive\",\"content\":null},\"countdown\":{\"is_active\":\"inactive\",\"content\":null},\"title\":\"Testing 1\",\"description\":\"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque quis accumsan velit.\",\"button\":[{\"is_active\":\"inactive\",\"title\":\"Link 1\",\"link\":\"https:\\/\\/getbootstrap.com\\/\"},{\"is_active\":\"inactive\",\"title\":\"Link 2\",\"link\":\"https:\\/\\/getbootstrap.com\\/\"}],\"desktop_image\":\"coverimage\\/102025\\/date_03\\/f973602708afba76344ad7a8214573cb.jpg\",\"mobile_image\":\"coverimage\\/102025\\/date_03\\/082f986a432ad59fb8c84b871e014e37.jpg\"},{\"background_overlay\":\"rgba(0, 0, 0, 0.4)\",\"cover_is_active\":\"active\",\"disable_content\":\"inactive\",\"desktop_content_position\":\"center-right\",\"mobile_content_position\":\"center-right\",\"link\":{\"is_active\":\"inactive\",\"content\":null},\"countdown\":{\"is_active\":\"inactive\",\"content\":null},\"title\":\"Testing 2\",\"description\":\"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque quis accumsan velit.\",\"button\":[{\"is_active\":\"inactive\",\"title\":\"Link 1\",\"link\":\"https:\\/\\/getbootstrap.com\\/\"},{\"is_active\":\"inactive\",\"title\":\"Link 2\",\"link\":\"https:\\/\\/getbootstrap.com\\/\"}],\"desktop_image\":\"coverimage\\/102025\\/date_03\\/c5193b7c03561ee89048a73d7370811b.jpg\",\"mobile_image\":\"coverimage\\/102025\\/date_03\\/672a787b29de640ce7d8fa00cb81d5c5.jpg\"}]', 'horizontal', 'active', 3000, 'active', 'active', '2025-10-03 09:28:44', '2025-10-03 09:28:44');

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
(1, 1, 'uIxTa0lV3L4EaV9A6BvJ7x', 'hBMt85z8I4p3dgfZCt1sf4', 'single', 'hBMt85z8I4p3dgfZCt1sf4', 'Manage Articles', 'manage_article', '[\"read data\", \"add data\", \"edit data\", \"delete data\"]', '2025-08-05 08:55:51', '2025-08-05 08:55:51'),
(2, 1, 'uIxTa0lV3L4EaV9A6BvJ7x', 'B6Bc6Y5bWfaVls2u3U87Sw', 'single', 'B6Bc6Y5bWfaVls2u3U87Sw', 'Manage Events', 'manage_events', '[\"read data\", \"add data\", \"edit data\", \"delete data\"]', '2025-08-05 08:55:51', '2025-08-05 08:55:51'),
(3, 1, 'uIxTa0lV3L4EaV9A6BvJ7x', 'NFoWOOkEdg3da5785YeLdc', 'single', 'NFoWOOkEdg3da5785YeLdc', 'Manage Promotions', 'manage_promotions', '[\"read data\", \"add data\", \"edit data\", \"delete data\"]', '2025-08-05 08:55:51', '2025-08-05 08:55:51'),
(4, 1, 'uIxTa0lV3L4EaV9A6BvJ7x', 'dqV84cjEjCrmp0BWF0fxpn', 'single', 'dqV84cjEjCrmp0BWF0fxpn', 'File Manager', 'filemanager', '[\"read data\", \"add data\", \"edit data\", \"delete data\"]', '2025-08-05 08:55:51', '2025-08-05 08:55:51'),
(7, 2, 'uIxTa0lV3L4EaV9A6BvJ7x', 'hBMt85z8I4p3dgfZCt1sf4', 'single', 'hBMt85z8I4p3dgfZCt1sf4', 'Manage Articles', 'manage_article', '[\"read data\", \"add data\", \"edit data\", \"delete data\"]', '2025-08-05 08:56:48', '2025-08-05 08:56:48'),
(8, 2, 'uIxTa0lV3L4EaV9A6BvJ7x', 'B6Bc6Y5bWfaVls2u3U87Sw', 'single', 'B6Bc6Y5bWfaVls2u3U87Sw', 'Manage Events', 'manage_events', '[\"read data\", \"add data\", \"edit data\", \"delete data\"]', '2025-08-05 08:56:48', '2025-08-05 08:56:48'),
(9, 2, 'uIxTa0lV3L4EaV9A6BvJ7x', 'NFoWOOkEdg3da5785YeLdc', 'single', 'NFoWOOkEdg3da5785YeLdc', 'Manage Promotions', 'manage_promotions', '[\"read data\", \"add data\", \"edit data\", \"delete data\"]', '2025-08-05 08:56:48', '2025-08-05 08:56:48'),
(10, 2, 'uIxTa0lV3L4EaV9A6BvJ7x', 'dqV84cjEjCrmp0BWF0fxpn', 'single', 'dqV84cjEjCrmp0BWF0fxpn', 'File Manager', 'filemanager', '[\"read data\", \"add data\", \"edit data\", \"delete data\"]', '2025-08-05 08:56:48', '2025-08-05 08:56:48'),
(12, 3, 'uIxTa0lV3L4EaV9A6BvJ7x', 'hBMt85z8I4p3dgfZCt1sf4', 'single', 'hBMt85z8I4p3dgfZCt1sf4', 'Manage Articles', 'manage_article', '[\"read data\", \"add data\", \"edit data\", \"delete data\"]', '2025-08-05 08:56:56', '2025-08-05 08:56:56'),
(13, 3, 'uIxTa0lV3L4EaV9A6BvJ7x', 'B6Bc6Y5bWfaVls2u3U87Sw', 'single', 'B6Bc6Y5bWfaVls2u3U87Sw', 'Manage Events', 'manage_events', '[\"read data\", \"add data\", \"edit data\", \"delete data\"]', '2025-08-05 08:56:56', '2025-08-05 08:56:56'),
(14, 3, 'uIxTa0lV3L4EaV9A6BvJ7x', 'NFoWOOkEdg3da5785YeLdc', 'single', 'NFoWOOkEdg3da5785YeLdc', 'Manage Promotions', 'manage_promotions', '[\"read data\", \"add data\", \"edit data\", \"delete data\"]', '2025-08-05 08:56:56', '2025-08-05 08:56:56'),
(15, 3, 'uIxTa0lV3L4EaV9A6BvJ7x', 'dqV84cjEjCrmp0BWF0fxpn', 'single', 'dqV84cjEjCrmp0BWF0fxpn', 'File Manager', 'filemanager', '[\"read data\", \"add data\", \"edit data\", \"delete data\"]', '2025-08-05 08:56:56', '2025-08-05 08:56:56'),
(30, 1, NULL, 'Xro2iEl4612W4TYKl4mPiL', 'submenu', 'ELzBRQokCEu189HwPFXbry', 'Testing Submenu 1', 'testing_submenu_1', '[\"read data\", \"add data\", \"edit data\", \"delete data\"]', '2025-08-07 09:01:14', '2025-08-07 09:01:14'),
(31, 1, NULL, 'NPqo8BfFzqWgiVHwZqYtaN', 'single', 'NPqo8BfFzqWgiVHwZqYtaN', 'Single Menu Testing 1', 'testing', '[\"read data\", \"add data\", \"edit data\", \"delete data\"]', '2025-08-07 09:01:14', '2025-08-07 09:01:14'),
(34, 1, NULL, 'nT21heY6KH8npGso0DM6yl', 'single', 'nT21heY6KH8npGso0DM6yl', 'Accounts', 'account', '[\"read data\", \"add data\", \"edit data\", \"delete data\"]', '2025-08-11 04:32:39', '2025-08-11 04:32:39'),
(36, 1, NULL, 'Xro2iEl4612W4TYKl4mPiL', 'submenu', 'HHkah3NHOlfnK4cRyNIYZ3', 'Testing Submenu 1', 'asdasd', NULL, '2025-08-23 06:44:12', '2025-08-23 06:44:12'),
(37, 1, NULL, 'Xro2iEl4612W4TYKl4mPiL', 'submenu', 'eD4UZyZc8hviVNnI7QoifP', 'Testing Submenu 2', 'asdasd2', NULL, '2025-08-23 06:44:12', '2025-08-23 06:44:12'),
(38, 1, 'uIxTa0lV3L4EaV9A6BvJ7x', 'ktGbOw0EloeZX73WIs50VO', 'single', 'ktGbOw0EloeZX73WIs50VO', 'Manager Cover Image', 'manage_coverimage', '[\"read data\", \"add data\", \"edit data\", \"delete data\"]', '2025-08-28 08:07:53', '2025-08-28 08:07:53');

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
(8, 'default', '{\"uuid\":\"8788e25f-387a-473b-b70f-3cc02e753060\",\"displayName\":\"Rappasoft\\\\LaravelAuthenticationLog\\\\Notifications\\\\NewDevice\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:32:\\\"App\\\\Models\\\\Awesome_Admin\\\\Account\\\";s:2:\\\"id\\\";a:1:{i:0;i:1;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:58:\\\"Rappasoft\\\\LaravelAuthenticationLog\\\\Notifications\\\\NewDevice\\\":2:{s:17:\\\"authenticationLog\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:59:\\\"Rappasoft\\\\LaravelAuthenticationLog\\\\Models\\\\AuthenticationLog\\\";s:2:\\\"id\\\";i:81;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"id\\\";s:36:\\\"9fc82169-8329-4cf4-b4af-06db8b1e653b\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}}\"},\"createdAt\":1760776303,\"delay\":null}', 0, NULL, 1760776303, 1760776303);

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
(1, 'english', 'Notifications', ''),
(2, 'english', 'It\\\'s quite for now', ''),
(3, 'english', 'You notifications will appear here once there\\\'s something new to review.', ''),
(4, 'english', 'All', ''),
(5, 'english', 'Visit Site', ''),
(6, 'english', 'Dashboard', ''),
(7, 'english', 'Uncategorized', ''),
(8, 'english', 'Admin', ''),
(9, 'english', 'Admin Panel', ''),
(10, 'english', 'Collapsed View', ''),
(11, 'english', 'Message', ''),
(12, 'english', 'Notification', ''),
(13, 'english', 'View Profile', ''),
(14, 'english', 'Account Settings', ''),
(15, 'english', 'Logout', ''),
(16, 'english', 'General', ''),
(17, 'english', 'System', ''),
(18, 'english', 'See All', ''),
(19, 'english', 'Login', ''),
(20, 'english', 'Log In', ''),
(21, 'english', 'Enter your email address and password to the dashboard.', ''),
(22, 'english', 'Notice', ''),
(23, 'english', 'just now', ''),
(24, 'english', 'Email Address', ''),
(25, 'english', 'Enter your email', ''),
(26, 'english', 'Password', ''),
(27, 'english', 'Forgot your password?', ''),
(28, 'english', 'Enter your password', ''),
(29, 'english', 'Remember me', ''),
(30, 'english', 'Create an account', ''),
(31, 'english', 'Email address required', ''),
(32, 'english', 'Password required', ''),
(33, 'english', 'User created successfully', ''),
(34, 'english', 'Profile', ''),
(35, 'english', 'Loading', ''),
(36, 'english', 'Save', ''),
(37, 'english', 'Cancel', ''),
(38, 'english', 'Personal Information', ''),
(39, 'english', 'Edit Profile', ''),
(40, 'english', 'Username', ''),
(41, 'english', 'Gender', ''),
(42, 'english', 'Birthdate', ''),
(43, 'english', 'Phone Number', ''),
(44, 'english', 'About', ''),
(45, 'english', 'Fullname', ''),
(46, 'english', 'Select Gender', ''),
(47, 'english', 'Male', ''),
(48, 'english', 'Female', ''),
(49, 'english', 'Enter New Password', ''),
(50, 'english', 'Re-type New Password', ''),
(51, 'english', 'Vertical Sidebar Menu successfully updated', ''),
(52, 'english', 'Awesome Admin Panel', ''),
(53, 'english', 'Site Settings', ''),
(54, 'english', 'Manage Menu Access', ''),
(55, 'english', 'Manage Users', ''),
(56, 'english', 'Manage Roles', ''),
(57, 'english', 'Manage Permissions', ''),
(58, 'english', 'SMTP Settings', ''),
(59, 'english', 'Manage Languages', ''),
(60, 'english', 'Manage User', ''),
(61, 'english', 'Awesome Admin', ''),
(62, 'english', 'Add New User', ''),
(63, 'english', 'Submit', ''),
(64, 'english', 'Select Day', ''),
(65, 'english', '1 Day', ''),
(66, 'english', '3 Days', ''),
(67, 'english', '7 Days', ''),
(68, 'english', '14 Days', ''),
(69, 'english', '30 Days', ''),
(70, 'english', '2 Months', ''),
(71, 'english', '3 Months', ''),
(72, 'english', '6 Months', ''),
(73, 'english', 'Search user by Email & Fullname', ''),
(74, 'english', 'ID', ''),
(75, 'english', 'Roles', ''),
(76, 'english', 'Status', ''),
(77, 'english', 'Options', ''),
(78, 'english', 'Unknown status, please re-sett status this account', ''),
(79, 'english', 'Edit', ''),
(80, 'english', 'Create', ''),
(81, 'english', 'Autofill', ''),
(82, 'english', 'Role', ''),
(83, 'english', 'Select', ''),
(84, 'english', 'Set Password Manually', ''),
(85, 'english', 'Automatic Set Password', ''),
(86, 'english', 'Default Password is {1}', ''),
(87, 'english', 'Loading ...', ''),
(88, 'english', 'Edit User', ''),
(89, 'english', 'Update user successfully', ''),
(90, 'english', 'Update profile photo successfully', ''),
(91, 'english', 'Manage Site Config', ''),
(92, 'english', 'General Settings', ''),
(93, 'english', 'Site Name', ''),
(94, 'english', 'Site Slogan', ''),
(95, 'english', 'Site Keyword', ''),
(96, 'english', 'Site Description', ''),
(97, 'english', 'Site Thumbnail', ''),
(98, 'english', 'Privacy & Security Settings', ''),
(99, 'english', 'Management Menu Version', ''),
(100, 'english', 'Select Version', ''),
(101, 'english', 'Management Menu v1', ''),
(102, 'english', 'Management Menu v2', ''),
(103, 'english', 'Information', ''),
(104, 'english', 'Management Menu v1 uses the built-in feature of Spatie Laravel Permission, where permissions must be assigned to Roles first for Menus and Users.', ''),
(105, 'english', 'Management Menu v2 is a custom feature that allows permissions to be directly assigned to the menu when creating a Role.', ''),
(106, 'english', 'Site Registration Settings', ''),
(107, 'english', 'Site Maintenance Settings', ''),
(108, 'english', 'Offline Reason', ''),
(109, 'english', 'Time Rate Limit Global in Second', ''),
(110, 'english', 'Please input time in second', ''),
(111, 'english', 'You can set time in second for duration rate limit', ''),
(112, 'english', 'Enable Rate Limit Login', ''),
(113, 'english', 'Enable', ''),
(114, 'english', 'Please input integer 10-999 or until 3 digits', ''),
(115, 'english', 'You can limit login requests per IP Address per minute if a user fails to login', ''),
(116, 'english', 'Enable Rate Limit Signup', ''),
(117, 'english', 'You can limit signup requests per IP Address per minute if a user fails to login', ''),
(118, 'english', 'reCAPTCHA Settings', ''),
(119, 'english', 'reCAPTCHA Site Key', ''),
(120, 'english', 'Please input reCAPTCHA Site Key', ''),
(121, 'english', 'reCAPTCHA Secret Key', ''),
(122, 'english', 'Please input reCAPTCHA Secret Key', ''),
(123, 'english', 'Enable reCAPTCHA Login', ''),
(124, 'english', 'You must setup reCAPTCHA key first before activate this option', ''),
(125, 'english', 'Enable reCAPTCHA Signup', ''),
(126, 'english', 'The email address is already in use by another user', ''),
(127, 'english', 'Username required', ''),
(128, 'english', 'The username is already used by another user', ''),
(129, 'english', 'Fullname required', ''),
(130, 'english', 'The minimum password character length is 6', ''),
(131, 'english', 'Role required', ''),
(132, 'english', 'Status required', ''),
(133, 'english', 'User successfully updated', ''),
(136, 'english', 'Manage Menu', ''),
(137, 'english', 'Category Menu', ''),
(138, 'english', 'Parent Menu', ''),
(139, 'english', 'Submenu', ''),
(140, 'english', 'Manage Category Menu', ''),
(141, 'english', 'List of Category Menu', ''),
(142, 'english', 'No Category Menu', ''),
(143, 'english', 'Category Menu Name', ''),
(144, 'english', 'Add Single or Category Menu', ''),
(145, 'english', 'Delete Category Menu', ''),
(146, 'english', 'Do you really want to delete these data? {1} This process cannot be undone.', ''),
(147, 'english', 'Delete', ''),
(148, 'english', 'Manage Parent Menu', ''),
(149, 'english', 'List of Parent Menu', ''),
(150, 'english', 'No Menu', ''),
(151, 'english', 'Manage Submenu', ''),
(152, 'english', 'Detail', ''),
(153, 'english', 'Manage Submenu Detail', ''),
(154, 'english', 'New Role', ''),
(155, 'english', 'User{1}s', ''),
(156, 'english', 'User', ''),
(157, 'english', 'Edit Role', ''),
(158, 'english', 'Delete Role', ''),
(159, 'english', 'Delete Data', ''),
(160, 'english', 'No, keep it', ''),
(161, 'english', 'Yes, Delete', ''),
(162, 'english', 'Create Role & Permissions', ''),
(163, 'english', 'Role Name', ''),
(164, 'english', 'Select All', ''),
(165, 'english', 'Menu Access', ''),
(166, 'english', 'Edit Role & Permissions', ''),
(167, 'english', 'Role name required', ''),
(168, 'english', 'Data successfully updated', ''),
(169, 'english', 'List of Roles', ''),
(170, 'english', 'View', ''),
(171, 'english', 'Permissions', ''),
(172, 'english', 'View Role', ''),
(173, 'english', 'You can view detail role here, before edit', ''),
(174, 'english', 'List Permission', ''),
(175, 'english', 'Close', ''),
(176, 'english', 'You can edit role name and permission here', ''),
(177, 'english', 'Manage Permission', ''),
(178, 'english', 'Add New Permission', ''),
(179, 'english', 'Permission Name', ''),
(180, 'english', 'Edit Permission', ''),
(181, 'english', 'You can edit permission here', ''),
(182, 'english', 'Delete Permission', ''),
(183, 'english', 'STMP Settings', ''),
(184, 'english', 'New SMTP', ''),
(185, 'english', 'Add New SMTP', ''),
(186, 'english', 'SMTP Service', ''),
(187, 'english', 'SMTP Host', ''),
(188, 'english', 'SMTP Username', ''),
(189, 'english', 'SMTP Password', ''),
(190, 'english', 'SMTP Port', ''),
(191, 'english', 'SMTP Encryption', ''),
(192, 'english', 'SSL', ''),
(193, 'english', 'TLS', ''),
(194, 'english', 'SMTP Sender Name', ''),
(195, 'english', 'SMTP Sender Address', ''),
(196, 'english', 'Edit SMTP', ''),
(197, 'english', 'SMTP service required', ''),
(198, 'english', 'Manage Language', ''),
(199, 'english', 'Untranslated', ''),
(200, 'english', 'Translated', ''),
(201, 'english', 'Manage Untranslated', ''),
(202, 'english', 'Language', ''),
(203, 'english', 'Language From', ''),
(204, 'english', 'Language To', ''),
(205, 'english', 'Status Save', ''),
(206, 'english', 'The email address or password you entered is incorrect, please try again', ''),
(207, 'english', 'seconds', ''),
(208, 'english', 'Too many attempts to login, please wait {1} to try again.', ''),
(209, 'english', 'Signup', ''),
(210, 'english', 'Don\\\'t have an account? Create your account, it takes less than a minute', ''),
(211, 'english', 'I accept Terms and Conditions', ''),
(212, 'english', 'or continue with', ''),
(213, 'english', '{1} not available', ''),
(214, 'english', 'The minimal password length is 6', ''),
(215, 'english', 'Only alphabet, number and underscore is allowed', ''),
(216, 'english', '{1} available', ''),
(218, 'english', 'Forgot Password', ''),
(219, 'english', 'Enter your email address and we will send you an email with instructions to reset your password.', ''),
(220, 'english', 'Email not found in any account', ''),
(221, 'english', 'Reset Password', ''),
(222, 'english', 'Enter your new password to recover your account.', ''),
(223, 'english', 'Enter your new password', ''),
(224, 'english', 'Re-type Password', ''),
(225, 'english', 'Enter re-type password', ''),
(226, 'english', 'New password required', ''),
(227, 'english', 'Re-type new password required', ''),
(228, 'english', 'Minimal password character is {1}', ''),
(229, 'english', 'Require at least one uppercase and one lowercase letter', ''),
(230, 'english', 'Require at least one number', ''),
(231, 'english', 'Require at least one symbol', ''),
(232, 'english', 'Enter your fullname', ''),
(233, 'english', 'Enter your username', ''),
(234, 'english', 'Testing Page', ''),
(235, 'english', 'Login View Settings', ''),
(236, 'english', 'Signup View Settings', ''),
(237, 'english', 'Forgot Password View Settings', ''),
(238, 'english', 'Appearance View Settings', ''),
(239, 'english', 'Appearance Settings', ''),
(240, 'english', 'Interface Login Theme', ''),
(241, 'english', 'Select or customize your UI theme', ''),
(242, 'english', 'Interface Signup Theme', ''),
(243, 'english', 'Interface Forgot Password Theme', ''),
(244, 'english', 'Interface Recovery Password Theme', ''),
(245, 'english', 'Interface Reset Password Theme', ''),
(246, 'english', 'Manage Appearance', ''),
(247, 'english', 'Change Status', ''),
(248, 'english', 'Change Role', ''),
(263, 'english', 'Manage Translated', ''),
(265, 'english', 'Color Nuances', ''),
(266, 'english', 'Background Image', ''),
(267, 'english', 'Color data not found', ''),
(268, 'english', 'Interface {1} Theme', ''),
(269, 'english', 'Already have account?', ''),
(270, 'english', 'File Manager', ''),
(271, 'english', 'User successfully created', ''),
(272, 'english', 'User login successfully', ''),
(274, 'english', 'Data successfully deleted', ''),
(275, 'english', 'Select Category Menu', ''),
(276, 'english', 'Parent Menu Name', ''),
(277, 'english', 'Parent Menu Link', ''),
(278, 'english', 'Is For Parent Menu', ''),
(279, 'english', 'Single Menu', ''),
(280, 'english', 'Parent Page Type', ''),
(281, 'english', 'Parent Menu Icon Type', ''),
(282, 'english', 'Upload File', ''),
(283, 'english', 'Custom Input', ''),
(284, 'english', 'View image', ''),
(285, 'english', 'Parent Menu Icon Custom', ''),
(286, 'english', 'View detail', ''),
(287, 'english', 'Remove', ''),
(288, 'english', 'Add Submenu', ''),
(289, 'english', 'Select Parent Menu', ''),
(290, 'english', 'Add', ''),
(291, 'english', 'Delete Parent Menu', ''),
(293, 'english', 'Add Single or Parent Menu', ''),
(294, 'english', 'Parent Menu Type', ''),
(295, 'english', 'Select Parent Type', ''),
(296, 'english', 'Page', ''),
(297, 'english', 'Custom', ''),
(298, 'english', 'Input Custom Icon', ''),
(299, 'english', 'Upload Parent Menu Icon', ''),
(300, 'english', 'Data successfully created', ''),
(301, 'english', 'List of Submenu', ''),
(302, 'english', 'Menu Name', ''),
(303, 'english', 'Menu Link', ''),
(304, 'english', 'Menu Type', ''),
(305, 'english', 'Menu Icon Type', ''),
(306, 'english', 'Menu Icon', ''),
(307, 'english', 'Menu Icon Custom', ''),
(308, 'english', 'Submenu Name', ''),
(309, 'english', 'Submenu Type', ''),
(310, 'english', 'Select Submenu Type', ''),
(311, 'english', 'Submenu Link', ''),
(312, 'english', 'Upload Submenu Icon', ''),
(313, 'english', 'Delete Submenu', ''),
(314, 'english', 'Parent Menu Icon', ''),
(315, 'english', 'The entered link already exists in the menu', ''),
(316, 'english', 'Link can be added', ''),
(317, 'english', 'You do not have permission to do this', ''),
(318, 'english', 'Parent type required', ''),
(319, 'english', 'Parent name required', ''),
(320, 'english', 'Menu is single or parent required', ''),
(321, 'english', 'Parent menu link required', ''),
(322, 'english', 'Parent roles link required', ''),
(323, 'english', 'Submenu name required', ''),
(324, 'english', 'Submenu type required', ''),
(325, 'english', 'Submenu link required', ''),
(326, 'english', 'Submenu roles link required', ''),
(327, 'english', 'This theme doesn\\\'t support color nuances', ''),
(328, 'english', 'Manage Blog', ''),
(329, 'english', 'Add Blog Article', ''),
(330, 'english', 'Add Article', ''),
(331, 'english', 'Upload Content', ''),
(332, 'english', 'You should upload content file first before continue and max file', ''),
(333, 'english', 'Next Step', ''),
(334, 'english', 'Content Name', ''),
(335, 'english', 'Max file', ''),
(336, 'english', 'Content Locked', ''),
(337, 'english', 'Select Status', ''),
(338, 'english', 'Locked', ''),
(339, 'english', 'Unlocked', ''),
(340, 'english', 'Content Thumbnail', ''),
(341, 'english', 'Thumbnail Preview', ''),
(342, 'english', 'Article Title', ''),
(343, 'english', 'Title required', ''),
(344, 'english', 'Content required', ''),
(345, 'english', 'Slug ', ''),
(346, 'english', 'Permalink', ''),
(347, 'english', 'You can customize the post link here or leave it blank.', ''),
(348, 'english', 'Title', ''),
(349, 'english', 'Categories', ''),
(350, 'english', 'Thumbnail', ''),
(351, 'english', 'Publish', ''),
(352, 'english', 'Visibility', ''),
(353, 'english', 'Immediately', ''),
(354, 'english', 'Save as Draft', ''),
(355, 'english', 'Draft', ''),
(356, 'english', 'Pending', ''),
(357, 'english', 'Scheduled', ''),
(358, 'english', 'Public', ''),
(359, 'english', 'Private', ''),
(360, 'english', 'Password Protected', ''),
(361, 'english', 'Enter Password', ''),
(362, 'english', 'Ok', ''),
(363, 'english', 'Article created successfully', ''),
(364, 'english', 'Password protected required', ''),
(365, 'english', 'Date', ''),
(366, 'english', 'Time', ''),
(367, 'english', 'The uploaded file must be a valid image {1}', ''),
(368, 'english', 'The {1} must not be larger than 5MB.', ''),
(369, 'english', 'The {1} must not be larger than 15MB.', ''),
(370, 'english', 'Add Post', ''),
(371, 'english', 'Manage Article', ''),
(372, 'english', 'Author', ''),
(373, 'english', 'Unknown status, please re-sett status this article', ''),
(374, 'english', 'Search article by Title', ''),
(375, 'english', 'Unknown Status', ''),
(376, 'english', 'Unknown', ''),
(377, 'english', 'No Scheduled', ''),
(378, 'english', 'Filter By Status', ''),
(379, 'english', 'Filter By Category', ''),
(380, 'english', 'Filter By Scheduled', ''),
(381, 'english', 'Total Data', ''),
(382, 'english', 'Edit Post', ''),
(383, 'english', 'Article edited successfully', ''),
(384, 'english', 'Article successfully deleted', ''),
(386, 'english', 'Article successfully updated', ''),
(387, 'english', 'Homepage', ''),
(388, 'english', 'Manage Cover Image', ''),
(389, 'english', 'Search coverimage by Title', ''),
(390, 'english', 'Page Name', ''),
(391, 'english', 'Page URI', ''),
(392, 'english', 'Cover Image Type', ''),
(393, 'english', 'Search Cover Image by Page Name', ''),
(394, 'english', 'Add Cover Image', ''),
(395, 'english', 'Add New Form', ''),
(396, 'english', 'Desktop Image', ''),
(397, 'english', 'Mobile Image', ''),
(398, 'english', 'Description', ''),
(399, 'english', 'Caption', ''),
(400, 'english', 'Background Overlay', ''),
(401, 'english', 'Cover Image', ''),
(402, 'english', 'Cover type required', ''),
(403, 'english', 'Cover page name required', ''),
(404, 'english', 'Cover Page Name', ''),
(405, 'english', 'Cover Type', ''),
(406, 'english', 'Button {1}', ''),
(407, 'english', 'Desktop Content Position', ''),
(408, 'english', 'Mobile Content Position', ''),
(409, 'english', 'Center Center', ''),
(410, 'english', 'Top Center', ''),
(411, 'english', 'Bottom Center', ''),
(412, 'english', 'Top Left', ''),
(413, 'english', 'Center Left', ''),
(414, 'english', 'Bottom Left', ''),
(415, 'english', 'Top Right', ''),
(416, 'english', 'Center Right', ''),
(417, 'english', 'Bottom Right', ''),
(418, 'english', 'Disable Content', ''),
(419, 'english', 'Active', ''),
(420, 'english', 'Inactive', ''),
(421, 'english', 'Only Background Image', ''),
(422, 'english', 'Slideshow', ''),
(423, 'english', 'Cover Page URI / Slug', ''),
(424, 'english', 'Line', ''),
(425, 'english', 'with message', ''),
(426, 'english', 'Line error', ''),
(427, 'english', 'URI or Slug required', ''),
(428, 'english', 'The URI or Slug is already in use by another cover image', ''),
(429, 'english', 'Cover image created successfully', ''),
(430, 'english', 'Slideshow Direction', ''),
(431, 'english', 'Horizontal', ''),
(432, 'english', 'Vertical', ''),
(433, 'english', 'Is Active Slideshow?', ''),
(437, 'english', 'Disable Button 1', ''),
(438, 'english', 'Disable Button 2', ''),
(439, 'english', 'Is Active Link for Image?', ''),
(440, 'english', 'Is Active Countdown for Image?', ''),
(441, 'english', 'Link for Image?', ''),
(442, 'english', 'Countdown for Image', ''),
(443, 'english', 'Edit Cover Image', ''),
(444, 'english', 'CoverImage edited successfully', ''),
(445, 'english', 'CoverImage successfully deleted', ''),
(446, 'english', 'Type cover image not found!', ''),
(447, 'english', 'Cover image type is missing from the request.', ''),
(448, 'english', 'Background Size', ''),
(449, 'english', 'Small Size', ''),
(450, 'english', 'Medium Size', ''),
(451, 'english', 'Large Size', ''),
(452, 'english', 'Fullscreen Size', ''),
(453, 'english', 'Based on Image Size', ''),
(454, 'english', 'Activate Looping Slideshow', ''),
(455, 'english', 'Activate Autoplay Slideshow', ''),
(456, 'english', 'Autoplay Slideshow Interval', '');

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
(1, 'awesome_admin', '[{\"parent_code\": \"hBMt85z8I4p3dgfZCt1sf4\", \"parent_icon\": \"\", \"parent_link\": \"manage_article\", \"parent_name\": \"Manage Articles\", \"parent_type\": \"custom\", \"parent_roles\": [\"Administrator\", \"Super Admin\"], \"category_code\": \"uIxTa0lV3L4EaV9A6BvJ7x\", \"parent_icon_url\": \"\", \"parent_icon_path\": \"\", \"parent_icon_type\": \"custom_input\", \"is_for_parent_menu\": \"single\", \"parent_icon_custom\": \"<i class=\\\"fal fa-newspaper fa-fw\\\"></i>\", \"parent_permissions\": \"\"}, {\"parent_code\": \"B6Bc6Y5bWfaVls2u3U87Sw\", \"parent_icon\": \"\", \"parent_link\": \"manage_events\", \"parent_name\": \"Manage Events\", \"parent_type\": \"custom\", \"parent_roles\": [\"Super Admin\", \"Administrator\"], \"category_code\": \"uIxTa0lV3L4EaV9A6BvJ7x\", \"parent_icon_url\": \"\", \"parent_icon_path\": \"\", \"parent_icon_type\": \"custom_input\", \"is_for_parent_menu\": \"single\", \"parent_icon_custom\": \"<i class=\\\"far fa-calendar-alt fa-fw\\\"></i>\", \"parent_permissions\": \"\"}, {\"parent_code\": \"NFoWOOkEdg3da5785YeLdc\", \"parent_icon\": \"\", \"parent_link\": \"manage_promotions\", \"parent_name\": \"Manage Promotions\", \"parent_type\": \"custom\", \"parent_roles\": [\"Super Admin\", \"Administrator\"], \"category_code\": \"uIxTa0lV3L4EaV9A6BvJ7x\", \"parent_icon_url\": \"\", \"parent_icon_path\": \"\", \"parent_icon_type\": \"custom_input\", \"is_for_parent_menu\": \"single\", \"parent_icon_custom\": \"<i class=\\\"fal fa-ad fa-fw\\\"></i>\", \"parent_permissions\": \"\"}, {\"parent_code\": \"dqV84cjEjCrmp0BWF0fxpn\", \"parent_icon\": \"\", \"parent_link\": \"filemanager\", \"parent_name\": \"File Manager\", \"parent_type\": \"custom\", \"parent_roles\": [\"Super Admin\", \"Administrator\", \"General Member\"], \"category_code\": \"uIxTa0lV3L4EaV9A6BvJ7x\", \"parent_icon_url\": \"\", \"parent_icon_path\": \"\", \"parent_icon_type\": \"custom_input\", \"is_for_parent_menu\": \"single\", \"parent_icon_custom\": \"<i class=\\\"fal fa-file-alt fa-fw\\\"></i>\", \"parent_permissions\": \"\"}, {\"parent_code\": \"ktGbOw0EloeZX73WIs50VO\", \"parent_icon\": \"\", \"parent_link\": \"manage_coverimage\", \"parent_name\": \"Manager Cover Image\", \"parent_type\": \"custom\", \"parent_roles\": [], \"category_code\": \"uIxTa0lV3L4EaV9A6BvJ7x\", \"parent_icon_url\": \"\", \"parent_icon_path\": \"\", \"parent_icon_type\": \"custom_input\", \"is_for_parent_menu\": \"single\", \"parent_icon_custom\": \"<i class=\\\"fal fa-puzzle-piece fa-fw\\\"></i>\", \"parent_permissions\": \"\"}, {\"parent_code\": \"Xro2iEl4612W4TYKl4mPiL\", \"parent_icon\": \"\", \"parent_link\": \"\", \"parent_name\": \"Parent Menu Testing 1\", \"parent_type\": \"custom\", \"parent_roles\": [\"Super Admin\", \"Administrator\", \"General Member\", \"Premium Member\"], \"category_code\": \"\", \"parent_icon_url\": \"\", \"parent_icon_path\": \"\", \"parent_icon_type\": \"custom_input\", \"is_for_parent_menu\": \"parent\", \"parent_icon_custom\": \"\", \"parent_permissions\": \"\"}, {\"parent_code\": \"TWDji5ICUa5vELTx9WWqSP\", \"parent_icon\": \"\", \"parent_link\": \"\", \"parent_name\": \"Parent Menu Testing 2\", \"parent_type\": \"custom\", \"parent_roles\": [], \"category_code\": \"\", \"parent_icon_url\": \"\", \"parent_icon_path\": \"\", \"parent_icon_type\": \"\", \"is_for_parent_menu\": \"parent\", \"parent_icon_custom\": \"\", \"parent_permissions\": \"\"}, {\"parent_code\": \"NPqo8BfFzqWgiVHwZqYtaN\", \"parent_icon\": \"\", \"parent_link\": \"testing\", \"parent_name\": \"Single Menu Testing 1\", \"parent_type\": \"custom\", \"parent_roles\": [\"Super Admin\", \"Administrator\"], \"category_code\": \"\", \"parent_icon_url\": \"\", \"parent_icon_path\": \"\", \"parent_icon_type\": \"\", \"is_for_parent_menu\": \"single\", \"parent_icon_custom\": \"\", \"parent_permissions\": \"\"}, {\"parent_code\": \"nT21heY6KH8npGso0DM6yl\", \"parent_icon\": \"\", \"parent_link\": \"account\", \"parent_name\": \"Accounts\", \"parent_type\": \"custom\", \"parent_roles\": [\"Super Admin\"], \"category_code\": \"\", \"parent_icon_url\": \"\", \"parent_icon_path\": \"\", \"parent_icon_type\": \"\", \"is_for_parent_menu\": \"single\", \"parent_icon_custom\": \"\", \"parent_permissions\": \"\"}]', '[{\"parent_code\": \"hBMt85z8I4p3dgfZCt1sf4\", \"parent_icon\": \"\", \"parent_link\": \"manage_article\", \"parent_name\": \"Manage Articles\", \"parent_type\": \"custom\", \"parent_roles\": [\"Administrator\", \"Super Admin\"], \"category_code\": \"uIxTa0lV3L4EaV9A6BvJ7x\", \"parent_icon_url\": \"\", \"parent_icon_path\": \"\", \"parent_icon_type\": \"custom_input\", \"is_for_parent_menu\": \"single\", \"parent_icon_custom\": \"<i class=\\\"fal fa-newspaper fa-fw\\\"></i>\", \"parent_permissions\": \"\"}, {\"parent_code\": \"B6Bc6Y5bWfaVls2u3U87Sw\", \"parent_icon\": \"\", \"parent_link\": \"manage_events\", \"parent_name\": \"Manage Events\", \"parent_type\": \"custom\", \"parent_roles\": [\"Super Admin\", \"Administrator\"], \"category_code\": \"uIxTa0lV3L4EaV9A6BvJ7x\", \"parent_icon_url\": \"\", \"parent_icon_path\": \"\", \"parent_icon_type\": \"custom_input\", \"is_for_parent_menu\": \"single\", \"parent_icon_custom\": \"<i class=\\\"far fa-calendar-alt fa-fw\\\"></i>\", \"parent_permissions\": \"\"}, {\"parent_code\": \"NFoWOOkEdg3da5785YeLdc\", \"parent_icon\": \"\", \"parent_link\": \"manage_promotions\", \"parent_name\": \"Manage Promotions\", \"parent_type\": \"custom\", \"parent_roles\": [\"Super Admin\", \"Administrator\"], \"category_code\": \"uIxTa0lV3L4EaV9A6BvJ7x\", \"parent_icon_url\": \"\", \"parent_icon_path\": \"\", \"parent_icon_type\": \"custom_input\", \"is_for_parent_menu\": \"single\", \"parent_icon_custom\": \"<i class=\\\"fal fa-ad fa-fw\\\"></i>\", \"parent_permissions\": \"\"}, {\"parent_code\": \"dqV84cjEjCrmp0BWF0fxpn\", \"parent_icon\": \"\", \"parent_link\": \"filemanager\", \"parent_name\": \"File Manager\", \"parent_type\": \"custom\", \"parent_roles\": [\"Super Admin\", \"Administrator\", \"General Member\"], \"category_code\": \"uIxTa0lV3L4EaV9A6BvJ7x\", \"parent_icon_url\": \"\", \"parent_icon_path\": \"\", \"parent_icon_type\": \"custom_input\", \"is_for_parent_menu\": \"single\", \"parent_icon_custom\": \"<i class=\\\"fal fa-file-alt fa-fw\\\"></i>\", \"parent_permissions\": \"\"}, {\"parent_code\": \"Xro2iEl4612W4TYKl4mPiL\", \"parent_icon\": \"\", \"parent_link\": \"\", \"parent_name\": \"Parent Menu Testing 1\", \"parent_type\": \"custom\", \"parent_roles\": [\"Super Admin\", \"Administrator\", \"General Member\", \"Premium Member\"], \"category_code\": \"\", \"parent_icon_url\": \"\", \"parent_icon_path\": \"\", \"parent_icon_type\": \"custom_input\", \"is_for_parent_menu\": \"parent\", \"parent_icon_custom\": \"\", \"parent_permissions\": \"\"}, {\"parent_code\": \"TWDji5ICUa5vELTx9WWqSP\", \"parent_icon\": \"\", \"parent_link\": \"\", \"parent_name\": \"Parent Menu Testing 2\", \"parent_type\": \"custom\", \"parent_roles\": [], \"category_code\": \"\", \"parent_icon_url\": \"\", \"parent_icon_path\": \"\", \"parent_icon_type\": \"\", \"is_for_parent_menu\": \"parent\", \"parent_icon_custom\": \"\", \"parent_permissions\": \"\"}, {\"parent_code\": \"NPqo8BfFzqWgiVHwZqYtaN\", \"parent_icon\": \"\", \"parent_link\": \"testing\", \"parent_name\": \"Single Menu Testing 1\", \"parent_type\": \"custom\", \"parent_roles\": [\"Super Admin\", \"Administrator\"], \"category_code\": \"\", \"parent_icon_url\": \"\", \"parent_icon_path\": \"\", \"parent_icon_type\": \"\", \"is_for_parent_menu\": \"single\", \"parent_icon_custom\": \"\", \"parent_permissions\": \"\"}, {\"parent_code\": \"nT21heY6KH8npGso0DM6yl\", \"parent_icon\": \"\", \"parent_link\": \"account\", \"parent_name\": \"Accounts\", \"parent_type\": \"custom\", \"parent_roles\": [\"Super Admin\"], \"category_code\": \"\", \"parent_icon_url\": \"\", \"parent_icon_path\": \"\", \"parent_icon_type\": \"\", \"is_for_parent_menu\": \"single\", \"parent_icon_custom\": \"\", \"parent_permissions\": \"\"}, {\"parent_code\": \"ktGbOw0EloeZX73WIs50VO\", \"parent_icon\": \"\", \"parent_link\": \"manage_coverimage\", \"parent_name\": \"Manager Cover Image\", \"parent_type\": \"custom\", \"parent_roles\": [], \"category_code\": \"uIxTa0lV3L4EaV9A6BvJ7x\", \"parent_icon_url\": \"\", \"parent_icon_path\": \"\", \"parent_icon_type\": \"custom_input\", \"is_for_parent_menu\": \"single\", \"parent_icon_custom\": \"<i class=\\\"fal fa-puzzle-piece\\\"></i>\", \"parent_permissions\": \"\"}]');

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

--
-- Dumping data for table `lr_menu_submenu_json`
--

INSERT INTO `lr_menu_submenu_json` (`id`, `parent_code`, `parent_name`, `menu_vars`, `menu_vars_backup`) VALUES
(4, 'Xro2iEl4612W4TYKl4mPiL', 'Parent Menu Testing 1', '[{\"parent_code\": \"\", \"submenu_code\": \"HHkah3NHOlfnK4cRyNIYZ3\", \"submenu_icon\": \"\", \"submenu_link\": \"asdasd\", \"submenu_name\": \"Testing Submenu 1\", \"submenu_type\": \"custom\", \"submenu_roles\": [], \"submenu_icon_url\": \"\", \"submenu_icon_path\": \"\", \"submenu_icon_type\": \"\", \"submenu_icon_custom\": \"\", \"submenu_permissions\": \"\"}, {\"parent_code\": \"\", \"submenu_code\": \"eD4UZyZc8hviVNnI7QoifP\", \"submenu_icon\": \"\", \"submenu_link\": \"asdasd2\", \"submenu_name\": \"Testing Submenu 2\", \"submenu_type\": \"custom\", \"submenu_roles\": [], \"submenu_icon_url\": \"\", \"submenu_icon_path\": \"\", \"submenu_icon_type\": \"\", \"submenu_icon_custom\": \"\", \"submenu_permissions\": \"\"}]', '[{\"parent_code\": \"\", \"submenu_code\": \"HHkah3NHOlfnK4cRyNIYZ3\", \"submenu_icon\": \"\", \"submenu_link\": \"asdasd\", \"submenu_name\": \"Testing Submenu 1\", \"submenu_type\": \"custom\", \"submenu_roles\": [], \"submenu_icon_url\": \"\", \"submenu_icon_path\": \"\", \"submenu_icon_type\": \"\", \"submenu_icon_custom\": \"\", \"submenu_permissions\": \"\"}]');

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
(162, '2025_08_15_091634_add_foreign_keys_to_role_has_permissions_table', 0);

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
(3, 'App\\Models\\Awesome_Admin\\Account', 11),
(3, 'App\\Models\\Awesome_Admin\\Account', 12),
(3, 'App\\Models\\Awesome_Admin\\Account', 13),
(3, 'App\\Models\\Awesome_Admin\\Account', 14),
(3, 'App\\Models\\Awesome_Admin\\Account', 15),
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
(3, 'App\\Models\\Awesome_Admin\\Account', 66);

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
(4, 'Premium Member', 'web', '2025-01-24 01:23:38', '2025-01-24 01:23:38');

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
('7qJEdeH40HoXqqiBJcV9891huWSVA4nwrVVVDNfA', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiRE9qV0ZxZjB3VEdmdjBTck1jREdtZXRxQlFFSHo3N0VsSGRuZkd2VSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NjA6Imh0dHBzOi8vbGFyYXZlbC0xMS1waG9lbml4LW9yaWdpbmFsLXJld3JpdGUuYXJ1bmEvYXV0aC9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NDoibGluayI7czo1OToiaHR0cHM6Ly9sYXJhdmVsLTExLXBob2VuaXgtb3JpZ2luYWwtcmV3cml0ZS5hcnVuYS9kYXNoYm9hcmQiO30=', 1755165689);

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

INSERT INTO `lr_site_config` (`id`, `site_url`, `site_name`, `site_slogan`, `site_keyword`, `site_description`, `site_thumbnail`, `footer_message1`, `footer_message2`, `signup_closed`, `offline_mode`, `offline_reason`, `login_multiple_device`, `management_menu`, `gmaps_api_key`, `recaptcha_site_key`, `recaptcha_secret_key`, `enable_recaptcha_signin`, `enable_recaptcha_signup`, `enable_autogen_username_signup`, `enable_ratelimit_login`, `amount_ratelimit_login`, `enable_ratelimit_signup`, `amount_ratelimit_signup`, `time_ratelimit_global`, `is_sso_activated`, `updated_at`, `created_at`) VALUES
(1, 'https://cms.tvindo.net', 'LaraPhoenix CMS', 'Simple, Beautiful & Elegant Framework PHP', 'Simple, Beautiful & Elegant Framework PHP', 'Simple, Beautiful & Elegant Framework PHP', 'thumbnail_668f97d07314a.jpg', 'CMS Laravel &copy; 2024', '', 0, 1, 'Sorry we are under maintenance, please come back later', 0, 'v2', '', '6Lf8hlgrAAAAAPELEMbrwo66ym8mA6DNrrDHZ0NY', '6Lf8hlgrAAAAAGSQBGJVsslfZXYyLU2AsfXKrZ4v', 1, 1, 1, 0, 7, 0, 7, 60, 1, '2025-08-12 06:28:43', '2025-01-20 02:55:03');

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
(1, 2, 'SMTP2GO SSL');

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
(4, 'calm_green', 'Calm Green Theme', 'calm_green', 'cms_layout', 'auth_layout', 'frontend_layout', '1.0.0', '2025-06-18 03:54:21', '2025-06-18 03:54:21');

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
(1, 4, 'calm_green', 'Calm Green Theme');

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
(1, 1, 'avatars/082025/date_13/9fbd9c94d8f982e7c178b0bda5bcb0ec.png', '', '', '1996-06-19', 1, '0895-0451-8506', 'Hanya seorang pegawai biasa yang mempunyai pengalaman yang tidak biasa.'),
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
(39, 66, NULL, NULL, NULL, NULL, 0, NULL, NULL);

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
  ADD KEY `code` (`code`);

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `lr_account_status`
--
ALTER TABLE `lr_account_status`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `lr_articles`
--
ALTER TABLE `lr_articles`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `lr_article_categories`
--
ALTER TABLE `lr_article_categories`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT for table `lr_cover_image`
--
ALTER TABLE `lr_cover_image`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `lr_custom_permissions`
--
ALTER TABLE `lr_custom_permissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `lr_failed_jobs`
--
ALTER TABLE `lr_failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lr_jobs`
--
ALTER TABLE `lr_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `lr_language`
--
ALTER TABLE `lr_language`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=457;

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `lr_migrations`
--
ALTER TABLE `lr_migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=163;

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
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

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
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

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
