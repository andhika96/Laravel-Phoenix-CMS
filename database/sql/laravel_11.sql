-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 13, 2025 at 03:24 AM
-- Server version: 8.4.5
-- PHP Version: 8.4.10

SET FOREIGN_KEY_CHECKS=0;
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

-- --------------------------------------------------------

--
-- Table structure for table `lr_blog_article`
--

CREATE TABLE `lr_blog_article` (
  `id` int NOT NULL,
  `uri` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `cid` int NOT NULL DEFAULT '0',
  `subcid` int NOT NULL DEFAULT '0',
  `title` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tags` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `thumb_s` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `thumb_l` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `userid` int NOT NULL DEFAULT '0',
  `schedule_pub` int NOT NULL DEFAULT '0',
  `created` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lr_blog_category`
--

CREATE TABLE `lr_blog_category` (
  `id` int NOT NULL,
  `name` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `code` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `created` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lr_blog_subcategory`
--

CREATE TABLE `lr_blog_subcategory` (
  `id` int NOT NULL,
  `cid` int NOT NULL DEFAULT '0',
  `name` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `code` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `created` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

-- --------------------------------------------------------

--
-- Table structure for table `lr_role_has_permissions`
--

CREATE TABLE `lr_role_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

-- --------------------------------------------------------

--
-- Table structure for table `lr_smtp_service`
--

CREATE TABLE `lr_smtp_service` (
  `id` int NOT NULL,
  `service_id` int NOT NULL DEFAULT '0',
  `service_name` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
-- Indexes for table `lr_authentication_log`
--
ALTER TABLE `lr_authentication_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lr_blog_article`
--
ALTER TABLE `lr_blog_article`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cid` (`cid`),
  ADD KEY `subcid` (`subcid`),
  ADD KEY `title` (`title`),
  ADD KEY `userid` (`userid`),
  ADD KEY `schedule_pub` (`schedule_pub`);

--
-- Indexes for table `lr_blog_category`
--
ALTER TABLE `lr_blog_category`
  ADD PRIMARY KEY (`id`),
  ADD KEY `name` (`name`),
  ADD KEY `code` (`code`);

--
-- Indexes for table `lr_blog_subcategory`
--
ALTER TABLE `lr_blog_subcategory`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cid` (`cid`),
  ADD KEY `name` (`name`),
  ADD KEY `code` (`code`);

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lr_account_status`
--
ALTER TABLE `lr_account_status`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lr_authentication_log`
--
ALTER TABLE `lr_authentication_log`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lr_blog_article`
--
ALTER TABLE `lr_blog_article`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lr_blog_category`
--
ALTER TABLE `lr_blog_category`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lr_blog_subcategory`
--
ALTER TABLE `lr_blog_subcategory`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lr_custom_permissions`
--
ALTER TABLE `lr_custom_permissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lr_failed_jobs`
--
ALTER TABLE `lr_failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lr_jobs`
--
ALTER TABLE `lr_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lr_language`
--
ALTER TABLE `lr_language`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lr_menu`
--
ALTER TABLE `lr_menu`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lr_menu_categorymenu_json`
--
ALTER TABLE `lr_menu_categorymenu_json`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lr_menu_parent`
--
ALTER TABLE `lr_menu_parent`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lr_menu_parentmenu_json`
--
ALTER TABLE `lr_menu_parentmenu_json`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lr_menu_submenu_json`
--
ALTER TABLE `lr_menu_submenu_json`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lr_migrations`
--
ALTER TABLE `lr_migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lr_notifications`
--
ALTER TABLE `lr_notifications`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lr_oauth_clients`
--
ALTER TABLE `lr_oauth_clients`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lr_oauth_personal_access_clients`
--
ALTER TABLE `lr_oauth_personal_access_clients`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lr_page_themes`
--
ALTER TABLE `lr_page_themes`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lr_page_theme_settings`
--
ALTER TABLE `lr_page_theme_settings`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lr_permissions`
--
ALTER TABLE `lr_permissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lr_personal_access_tokens`
--
ALTER TABLE `lr_personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lr_roles`
--
ALTER TABLE `lr_roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lr_site_config`
--
ALTER TABLE `lr_site_config`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lr_smtp_service`
--
ALTER TABLE `lr_smtp_service`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lr_smtp_settings`
--
ALTER TABLE `lr_smtp_settings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lr_testings`
--
ALTER TABLE `lr_testings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lr_themes`
--
ALTER TABLE `lr_themes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lr_theme_settings`
--
ALTER TABLE `lr_theme_settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lr_users`
--
ALTER TABLE `lr_users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lr_user_information`
--
ALTER TABLE `lr_user_information`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

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
SET FOREIGN_KEY_CHECKS=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
