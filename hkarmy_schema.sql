-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 03, 2023 at 08:12 PM
-- Server version: 10.3.25-MariaDB-0ubuntu0.20.04.1
-- PHP Version: 7.4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hkarmy`
--

-- --------------------------------------------------------

--
-- Table structure for table `assign_product_order`
--

CREATE TABLE `assign_product_order` (
  `id` bigint(20) NOT NULL,
  `order_id` text DEFAULT NULL,
  `product_id` bigint(20) NOT NULL,
  `product_cost_type_id` int(11) DEFAULT NULL,
  `child_product_id` text DEFAULT NULL,
  `order_date` date NOT NULL,
  `created_by_user_id` bigint(20) NOT NULL,
  `status` tinyint(1) DEFAULT 1 COMMENT '1 = Active , 2 = InActive',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `member_code` varchar(255) DEFAULT NULL,
  `event_type` varchar(255) DEFAULT NULL,
  `event_id` int(11) NOT NULL,
  `event_schedule_id` bigint(20) DEFAULT NULL,
  `in_time` varchar(150) NOT NULL,
  `out_time` varchar(150) DEFAULT '-',
  `in_time_deducted_hour` int(11) DEFAULT NULL,
  `out_time_deducted_hour` int(11) DEFAULT NULL,
  `total_deducted_hour` int(11) DEFAULT NULL,
  `hours` varchar(150) DEFAULT '-',
  `total_event_hours` int(11) DEFAULT NULL,
  `activity_hour` varchar(255) NOT NULL DEFAULT '00:00',
  `service_hour` varchar(255) NOT NULL DEFAULT '00:00',
  `training_hour` varchar(255) NOT NULL DEFAULT '00:00',
  `remaining_hour` varchar(255) NOT NULL DEFAULT '00:00',
  `date` varchar(255) DEFAULT NULL,
  `late_min` varchar(150) DEFAULT NULL,
  `early_min` varchar(150) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `audit_log`
--

CREATE TABLE `audit_log` (
  `id` int(11) NOT NULL,
  `Log` text DEFAULT NULL,
  `Log_id` text DEFAULT NULL,
  `user_id` int(5) NOT NULL,
  `table_name` varchar(255) DEFAULT NULL,
  `page` varchar(255) NOT NULL,
  `date` date DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `awards`
--

CREATE TABLE `awards` (
  `id` bigint(20) NOT NULL,
  `badge_id` bigint(20) NOT NULL,
  `award_categories_id` bigint(20) NOT NULL,
  `name_en` varchar(100) NOT NULL,
  `name_ch` varchar(100) NOT NULL,
  `other_awards_type_en` text DEFAULT NULL,
  `other_awards_type_ch` text DEFAULT NULL,
  `award_year` year(4) DEFAULT NULL,
  `reference_number` varchar(100) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `awards_assign`
--

CREATE TABLE `awards_assign` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `award_id` bigint(20) NOT NULL,
  `reference_number` varchar(50) DEFAULT NULL,
  `issue_date` date DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `assigned_date` date DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `awards_badges_categories`
--

CREATE TABLE `awards_badges_categories` (
  `id` bigint(20) NOT NULL,
  `categories_type` enum('award','badge') DEFAULT NULL COMMENT '1 = Awards, 2 = Badges',
  `parent_categories_id` bigint(20) DEFAULT NULL,
  `is_mentor_team_categories` enum('yes','no') DEFAULT NULL,
  `name_en` varchar(100) NOT NULL,
  `name_ch` varchar(100) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `awards_categories`
--

CREATE TABLE `awards_categories` (
  `id` bigint(20) NOT NULL,
  `name_en` varchar(100) NOT NULL,
  `name_ch` varchar(100) NOT NULL,
  `type_id` tinyint(4) DEFAULT NULL COMMENT '1 = Internal Awards of Hong Kong Army Cadet HeadQuarter, 2 = Awards received on behalf of HK Army Cadet',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `badges`
--

CREATE TABLE `badges` (
  `id` bigint(20) NOT NULL,
  `badges_type_id` bigint(20) DEFAULT NULL,
  `current_team_member` enum('mentor_team','not_mentor_team') DEFAULT NULL,
  `name_en` varchar(100) DEFAULT NULL,
  `name_ch` varchar(100) DEFAULT NULL,
  `other_badges_type_en` text DEFAULT NULL,
  `other_badges_type_ch` text DEFAULT NULL,
  `badges_image` longtext DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `badges_type`
--

CREATE TABLE `badges_type` (
  `id` bigint(20) NOT NULL,
  `name_en` varchar(100) DEFAULT NULL,
  `name_ch` varchar(100) DEFAULT NULL,
  `type_id` tinyint(4) DEFAULT NULL COMMENT '1 = Skill Sets Badge, 2 = Discovery , 3 = Knowledge Award',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `badge_assign`
--

CREATE TABLE `badge_assign` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `badge_id` bigint(20) NOT NULL,
  `reference_number` varchar(255) DEFAULT NULL,
  `issue_date` date DEFAULT NULL,
  `assigned_date` date NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `qty` varchar(255) DEFAULT NULL,
  `totalAmount` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name_en` varchar(100) NOT NULL,
  `name_ch` varchar(100) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 = Active, 2 = InActive',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `child_products`
--

CREATE TABLE `child_products` (
  `id` bigint(20) NOT NULL,
  `main_product_id` bigint(20) NOT NULL,
  `product_suffix` varchar(255) DEFAULT NULL,
  `product_suffix_name` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 = Active, 2 = InActive',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `elite`
--

CREATE TABLE `elite` (
  `id` int(11) NOT NULL,
  `elite_ch` varchar(255) NOT NULL,
  `elite_en` varchar(255) NOT NULL,
  `status` enum('1','2') NOT NULL COMMENT '1 For active, 2 for Inactive',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `event_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `event_type` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `event_code` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `assessment` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `assessment_text` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `startdate` varchar(255) NOT NULL,
  `enddate` varchar(255) DEFAULT NULL,
  `start_time` varchar(255) NOT NULL,
  `end_time` varchar(255) NOT NULL,
  `event_hours` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `is_free_event` tinyint(1) DEFAULT 0 COMMENT '1 = Yes, 0 = No',
  `no_of_dates` int(11) NOT NULL DEFAULT 0,
  `event_money` varchar(100) DEFAULT NULL,
  `event_token` bigint(11) DEFAULT NULL,
  `multiple_event` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `occurs` varchar(255) DEFAULT NULL,
  `occurs_weekly` varchar(255) DEFAULT NULL,
  `weekly_date` text DEFAULT NULL,
  `occurs_monthly` varchar(255) DEFAULT NULL,
  `daily_date` text DEFAULT NULL,
  `monthweekdate` text DEFAULT NULL,
  `event_assign_user` text DEFAULT NULL,
  `status` enum('0','1','2','3','4') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '2' COMMENT '0 for Draft,1 for Published,2 for Unpublished,3 for Ready for close,4 for Close event',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `event_assign`
--

CREATE TABLE `event_assign` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `remark` varchar(255) DEFAULT NULL,
  `cost_type` int(10) DEFAULT NULL,
  `cost_type_id` int(11) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '1:Confirm 0: Not- confirm',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `event_post_type`
--

CREATE TABLE `event_post_type` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `event_code` varchar(100) NOT NULL,
  `post_type` varchar(100) DEFAULT NULL COMMENT '1 = Money, 2 = Token, 3 = Money+Token',
  `post_value` varchar(100) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `event_schedule`
--

CREATE TABLE `event_schedule` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `event_code` varchar(255) NOT NULL,
  `date` varchar(255) NOT NULL,
  `occurs` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `start_time` varchar(255) DEFAULT NULL,
  `end_time` varchar(255) DEFAULT NULL,
  `event_hours` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `event_token_manage`
--

CREATE TABLE `event_token_manage` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL COMMENT 'event_id = event_schedule_id',
  `generate_token` bigint(20) NOT NULL,
  `used_token` bigint(20) DEFAULT 0,
  `remaining_token` bigint(20) DEFAULT 0,
  `status` enum('active','expired') NOT NULL DEFAULT 'active',
  `expire_date` date DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `event_type`
--

CREATE TABLE `event_type` (
  `id` int(11) NOT NULL,
  `type_id` varchar(255) NOT NULL DEFAULT '0',
  `event_type_name_en` varchar(255) CHARACTER SET utf8 NOT NULL,
  `event_type_name_ch` varchar(255) CHARACTER SET utf8 NOT NULL,
  `status` enum('1','2') NOT NULL COMMENT '1 for active,2 for inactive',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `history_team_rank_log`
--

CREATE TABLE `history_team_rank_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `teameilte_log` text DEFAULT NULL,
  `rank_log` text DEFAULT NULL,
  `team_status` int(11) NOT NULL DEFAULT 0,
  `rank_status` int(11) NOT NULL DEFAULT 0,
  `remark_log` text DEFAULT NULL,
  `remark_status` varchar(255) DEFAULT NULL,
  `page` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `hours_attendance_management`
--

CREATE TABLE `hours_attendance_management` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `use_hour` int(11) NOT NULL,
  `remaining_hour` int(11) NOT NULL,
  `Total_hour` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `member_token`
--

CREATE TABLE `member_token` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `token` varchar(100) NOT NULL,
  `remaining_token` varchar(100) DEFAULT NULL,
  `remark` varchar(150) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0:Not Used 1:Used',
  `expired` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0:Not expired 1: expired',
  `created_at` datetime NOT NULL,
  `expired_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `member_token_status`
--

CREATE TABLE `member_token_status` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_token` varchar(100) DEFAULT NULL,
  `total_money` varchar(100) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `member_used_token`
--

CREATE TABLE `member_used_token` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `token` int(11) DEFAULT NULL,
  `money` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `module`
--

CREATE TABLE `module` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `display_name_en` varchar(255) NOT NULL,
  `display_name_ch` varchar(255) CHARACTER SET utf8 NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1:Active 0:Inactive',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `order`
--

CREATE TABLE `order` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `totalqty` varchar(255) NOT NULL,
  `product_total_amount` varchar(255) NOT NULL,
  `blilling_address` text NOT NULL,
  `shipping_address` text NOT NULL,
  `order_notes` text DEFAULT NULL,
  `order_date` date NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_qty` varchar(255) NOT NULL,
  `product_amount` varchar(255) NOT NULL,
  `product_sku` varchar(255) DEFAULT NULL,
  `product_size` varchar(255) DEFAULT NULL,
  `product_uniform_type` varchar(255) DEFAULT NULL,
  `transaction_type` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `id` int(11) NOT NULL,
  `parent_id` varchar(255) NOT NULL DEFAULT '0' COMMENT '0:Parent ',
  `combo_product_ids` longtext DEFAULT NULL,
  `product_type` tinyint(1) DEFAULT 1 COMMENT '1 = Single Product, 2 = Combo product',
  `product_name` varchar(255) NOT NULL,
  `product_sku` varchar(255) NOT NULL,
  `uniformType` varchar(255) NOT NULL,
  `size` varchar(255) DEFAULT NULL,
  `product_amount` varchar(255) NOT NULL,
  `product_image` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `sub_amount` varchar(255) DEFAULT NULL,
  `total_amount` varchar(255) DEFAULT NULL,
  `status` enum('1','2') NOT NULL,
  `date` date NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `product_assign`
--

CREATE TABLE `product_assign` (
  `id` int(11) NOT NULL,
  `assign_product_order_id` bigint(20) DEFAULT NULL,
  `product_id` int(11) NOT NULL,
  `child_product_id` text DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `remark` varchar(255) DEFAULT NULL,
  `cost_type_id` varchar(100) DEFAULT NULL,
  `token` int(11) DEFAULT NULL,
  `money` varchar(100) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '1:Confirm 0: Not- confirm',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `product_cost_type`
--

CREATE TABLE `product_cost_type` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `cost_type` varchar(100) DEFAULT NULL COMMENT '1 = Money, 2 = Token, 3 = Money+Token',
  `cost_value` varchar(100) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `qualification`
--

CREATE TABLE `qualification` (
  `id` int(11) NOT NULL,
  `qualification_ch` varchar(255) CHARACTER SET utf8 NOT NULL,
  `qualification_en` varchar(255) CHARACTER SET utf8 NOT NULL,
  `status` enum('1','2') NOT NULL COMMENT '1 for Active,2 for Inactive',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `relatedactivityhistory`
--

CREATE TABLE `relatedactivityhistory` (
  `id` int(11) NOT NULL,
  `ActivityHistory_en` varchar(255) CHARACTER SET utf8 NOT NULL,
  `ActivityHistory_ch` varchar(255) CHARACTER SET utf8 NOT NULL,
  `status` enum('1','2') CHARACTER SET utf8 NOT NULL COMMENT '1 For Active,2for Inactive',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `remarks`
--

CREATE TABLE `remarks` (
  `id` int(11) NOT NULL,
  `remarks_ch` varchar(255) NOT NULL,
  `remarks_en` varchar(255) NOT NULL,
  `status` enum('1','2') NOT NULL COMMENT '1 for active,2 for Inactive',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(11) NOT NULL,
  `role_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` mediumtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `permission` mediumtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 = Active, 0= Inactive',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `service_hour_package`
--

CREATE TABLE `service_hour_package` (
  `id` int(11) NOT NULL,
  `package_name` varchar(255) NOT NULL,
  `hours` varchar(255) NOT NULL,
  `status` enum('1','2') NOT NULL DEFAULT '1' COMMENT '1 for active, 2 for inactive',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `min_hour` varchar(255) DEFAULT NULL,
  `HKD` varchar(255) DEFAULT NULL,
  `Logo` varchar(255) DEFAULT NULL,
  `SiteName` varchar(255) NOT NULL,
  `token_expire_day` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `size_attribute`
--

CREATE TABLE `size_attribute` (
  `id` bigint(20) NOT NULL,
  `name_en` varchar(50) CHARACTER SET utf8 NOT NULL,
  `name_ch` varchar(50) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 = Active, 2 = InActive',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `specialty`
--

CREATE TABLE `specialty` (
  `id` int(11) NOT NULL,
  `specialty_ch` varchar(255) CHARACTER SET utf8 NOT NULL,
  `specialty_en` varchar(255) CHARACTER SET utf8 NOT NULL,
  `status` enum('1','2') NOT NULL COMMENT '	1 for Active,2 for Inactive	',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `subelite`
--

CREATE TABLE `subelite` (
  `id` int(11) NOT NULL,
  `elite_id` int(11) NOT NULL,
  `subelite_ch` varchar(255) CHARACTER SET utf8 NOT NULL,
  `subelite_en` varchar(255) CHARACTER SET utf8 NOT NULL,
  `status` enum('1','2') NOT NULL COMMENT '1 for active and 2 for inactive',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `subteam`
--

CREATE TABLE `subteam` (
  `id` int(11) NOT NULL,
  `elite_id` int(11) NOT NULL,
  `subteam_ch` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `subteam_en` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `status` enum('1','2') NOT NULL DEFAULT '1' COMMENT '1 for active,2 for inactive',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `ID` int(11) NOT NULL,
  `UserName` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `email` varchar(150) CHARACTER SET utf8 NOT NULL,
  `password` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `Chinese_name` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `English_name` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `Contact_number` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `Contact_number_1` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `Contact_number_2` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `Gender` varchar(10) CHARACTER SET utf8 DEFAULT NULL,
  `DOB` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `age` int(11) NOT NULL DEFAULT 0,
  `QrCode` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `HkidNumber` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `Address` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `MemberCode` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `team_effiective_date` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `team` varchar(150) CHARACTER SET utf8 DEFAULT NULL,
  `elite_team` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `Specialty_Instructor` varchar(150) CHARACTER SET utf8 DEFAULT NULL,
  `Specialty_Instructor_text` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `elite_text` varchar(150) CHARACTER SET utf8 DEFAULT NULL,
  `district_text` varchar(150) CHARACTER SET utf8 DEFAULT NULL,
  `rank_effiective_date` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `Reference_number` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `rank_team` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `rank_team_mentor` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `rank_elite_text` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `rank_district_text` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `Chinese_address` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `English_address` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `image` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `Nationality` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `Occupation` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `ID_Number` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `Qualification` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `note` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `School_Name` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `Subject` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `Related_Activity_History` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `Related_Activity_History_text` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `is_other_experience` tinyint(1) DEFAULT NULL COMMENT '0 = No, 1 = Yes',
  `Other_experience` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `Specialty` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `Specialty_text` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `Health_declaration` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `Health_declaration_text` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `Emergency_contact_name` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `EmergencyContact` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `Relationship` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `Relationship_text` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `JoinDate` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `service_package_hour__id` int(11) DEFAULT NULL,
  `Remarks` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `Remarks_desc` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `remark_date` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `Remarks_Accident` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `Accident_date` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `discipline_issues_date` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `Appraisal_date` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `others_date` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `Remarks_Discipline_Issues` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `Remarks_Appraisal` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `Remarks_Others` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `email_verified` int(11) NOT NULL DEFAULT 2,
  `Role_ID` int(11) NOT NULL,
  `Attachment` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `hour_point` varchar(255) CHARACTER SET utf8 DEFAULT '0',
  `hour_point_rate` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `total_money` varchar(100) DEFAULT NULL,
  `total_tokens` bigint(20) DEFAULT NULL,
  `member_token` bigint(20) DEFAULT NULL,
  `Status` enum('1','2') CHARACTER SET utf8 NOT NULL DEFAULT '1' COMMENT '1 for Active,2 for Inactive',
  `lastactivity` date DEFAULT NULL,
  `remember_token` text CHARACTER SET utf8 DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assign_product_order`
--
ALTER TABLE `assign_product_order`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `awards`
--
ALTER TABLE `awards`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `awards_assign`
--
ALTER TABLE `awards_assign`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `awards_badges_categories`
--
ALTER TABLE `awards_badges_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `awards_categories`
--
ALTER TABLE `awards_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `badges`
--
ALTER TABLE `badges`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `badges_type`
--
ALTER TABLE `badges_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `badge_assign`
--
ALTER TABLE `badge_assign`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `child_products`
--
ALTER TABLE `child_products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `elite`
--
ALTER TABLE `elite`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `event_assign`
--
ALTER TABLE `event_assign`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `event_post_type`
--
ALTER TABLE `event_post_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `event_schedule`
--
ALTER TABLE `event_schedule`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `event_token_manage`
--
ALTER TABLE `event_token_manage`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `event_type`
--
ALTER TABLE `event_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `history_team_rank_log`
--
ALTER TABLE `history_team_rank_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hours_attendance_management`
--
ALTER TABLE `hours_attendance_management`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`(250));

--
-- Indexes for table `member_token`
--
ALTER TABLE `member_token`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `member_token_status`
--
ALTER TABLE `member_token_status`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `member_used_token`
--
ALTER TABLE `member_used_token`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `module`
--
ALTER TABLE `module`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order`
--
ALTER TABLE `order`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`(191));

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_assign`
--
ALTER TABLE `product_assign`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_cost_type`
--
ALTER TABLE `product_cost_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `qualification`
--
ALTER TABLE `qualification`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `relatedactivityhistory`
--
ALTER TABLE `relatedactivityhistory`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `remarks`
--
ALTER TABLE `remarks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `service_hour_package`
--
ALTER TABLE `service_hour_package`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `size_attribute`
--
ALTER TABLE `size_attribute`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `specialty`
--
ALTER TABLE `specialty`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subelite`
--
ALTER TABLE `subelite`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subteam`
--
ALTER TABLE `subteam`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `assign_product_order`
--
ALTER TABLE `assign_product_order`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `awards`
--
ALTER TABLE `awards`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `awards_assign`
--
ALTER TABLE `awards_assign`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `awards_badges_categories`
--
ALTER TABLE `awards_badges_categories`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `awards_categories`
--
ALTER TABLE `awards_categories`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `badges`
--
ALTER TABLE `badges`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `badges_type`
--
ALTER TABLE `badges_type`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `badge_assign`
--
ALTER TABLE `badge_assign`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `child_products`
--
ALTER TABLE `child_products`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `elite`
--
ALTER TABLE `elite`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `event_assign`
--
ALTER TABLE `event_assign`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `event_post_type`
--
ALTER TABLE `event_post_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `event_schedule`
--
ALTER TABLE `event_schedule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `event_token_manage`
--
ALTER TABLE `event_token_manage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `event_type`
--
ALTER TABLE `event_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `history_team_rank_log`
--
ALTER TABLE `history_team_rank_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hours_attendance_management`
--
ALTER TABLE `hours_attendance_management`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `member_token`
--
ALTER TABLE `member_token`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `member_token_status`
--
ALTER TABLE `member_token_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `member_used_token`
--
ALTER TABLE `member_used_token`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `module`
--
ALTER TABLE `module`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order`
--
ALTER TABLE `order`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_assign`
--
ALTER TABLE `product_assign`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_cost_type`
--
ALTER TABLE `product_cost_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `qualification`
--
ALTER TABLE `qualification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `relatedactivityhistory`
--
ALTER TABLE `relatedactivityhistory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `remarks`
--
ALTER TABLE `remarks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `service_hour_package`
--
ALTER TABLE `service_hour_package`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `size_attribute`
--
ALTER TABLE `size_attribute`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `specialty`
--
ALTER TABLE `specialty`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subelite`
--
ALTER TABLE `subelite`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subteam`
--
ALTER TABLE `subteam`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
