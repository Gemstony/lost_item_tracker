-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 18, 2026 at 04:27 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lost_item_tracker`
--

-- --------------------------------------------------------

--
-- Table structure for table `found_items`
--

CREATE TABLE `found_items` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `item_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `found_location` varchar(200) NOT NULL,
  `gps_latitude` decimal(10,8) DEFAULT NULL,
  `gps_longitude` decimal(11,8) DEFAULT NULL,
  `found_date` date NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `status` enum('pending','claimed','resolved') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `found_items`
--

INSERT INTO `found_items` (`id`, `user_id`, `item_name`, `description`, `category`, `found_location`, `gps_latitude`, `gps_longitude`, `found_date`, `image_path`, `status`, `created_at`) VALUES
(1, 1, 'Samsung Phone', 'bland new samsung phone', 'Phone', 'Kisiwani, Ubungo, Ubungo Municipal, Dar es Salaam, Coastal Zone, 21493, Tanzania', -6.79792200, 39.21073200, '2026-05-19', 'uploads/found_items/found_1779231270_6a0cea264e979.png', 'claimed', '2026-05-19 22:54:30'),
(2, 1, 'Shelley Wooten', 'Dignissimos est dolo', 'Bag', 'Kisiwani, Ubungo, Ubungo Municipal, Dar es Salaam, Coastal Zone, 21493, Tanzania', -6.79792200, 39.21073200, '2017-10-27', 'uploads/found_items/found_1779231388_6a0cea9cf3131.png', 'pending', '2026-05-19 22:56:28'),
(3, 1, 'tecno phone', 'manzese', 'Phone', 'Kisiwani, Ubungo, Ubungo Municipal, Dar es Salaam, Coastal Zone, 21493, Tanzania', -6.79792200, 39.21073200, '2026-05-19', 'uploads/found_items/found_1779234714_6a0cf79a6e8f6.png', 'pending', '2026-05-19 23:51:54'),
(4, 1, 'tecno phone', 'ghvsyib', 'Phone', 'National Institute of Transport, Kigogo Road, Mabibo Relini, Ubungo, Ubungo Municipal, Dar es Salaam, Coastal Zone, 21493, Tanzania', -6.80228147, 39.22145485, '2026-05-19', 'uploads/found_items/found_1779286872_6a0dc358b531b.jpg', 'pending', '2026-05-20 14:21:12'),
(5, 3, 'iphone 2', 'Dolor explicabo Inc', 'Phone', 'National Institute of Transport, Mabibo Road, Mabibo Relini, Mabibo, Ubungo Municipal, Dar es Salaam, Coastal Zone, 21493, Tanzania', -6.80363375, 39.22161068, '1971-03-03', 'uploads/found_items/found_1779369568_6a0f0660829af.jpg', 'pending', '2026-05-21 13:19:28'),
(6, 5, 'Tablet', 'tablet nyeusi na imechakaa kidogo', 'Phone', 'National Institute of Transport, Kigogo Road, Mabibo Relini, Ubungo, Ubungo Municipal, Dar es Salaam, Coastal Zone, 21493, Tanzania', -6.80362910, 39.22173402, '2026-05-21', 'uploads/found_items/found_1779370008_6a0f0818a2081.png', 'claimed', '2026-05-21 13:26:48'),
(7, 5, 'Gucci Bag', 'brown Gucci Bag', 'Bag', 'National Institute of Transport, Kigogo Road, Mabibo Relini, Ubungo, Ubungo Municipal, Dar es Salaam, Coastal Zone, 21493, Tanzania', NULL, NULL, '2026-05-21', 'uploads/found_items/found_1779373639_6a0f1647ae6ee.jpg', 'pending', '2026-05-21 14:27:19'),
(8, 5, 'Gucci Bag', 'Brown Gucci bag', 'Bag', 'National Institute of Transport, Kigogo Road, Mabibo Relini, Ubungo, Ubungo Municipal, Dar es Salaam, Coastal Zone, 21493, Tanzania', -6.80300220, 39.22285781, '2026-05-20', 'uploads/found_items/found_1779373689_6a0f167982452.jpg', 'claimed', '2026-05-21 14:28:09'),
(9, 8, 'smart watch', 'apple watch', 'Other', 'National Institute of Transport, Mabibo Road, Mabibo Relini, Mabibo, Ubungo Municipal, Dar es Salaam, Coastal Zone, 21493, Tanzania', -6.80295736, 39.22287964, '2026-05-21', 'uploads/found_items/found_1779375349_6a0f1cf5e7fe9.jpg', 'pending', '2026-05-21 14:55:49'),
(10, 7, 'Samsung Phone', 'samsung s10', 'Phone', 'Kigogo Road, Mabibo Relini, Ubungo, Ubungo Municipal, Dar es Salaam, Coastal Zone, 21493, Tanzania', -6.80300628, 39.21981196, '2026-05-30', 'uploads/found_items/found_1779431883_6a0ff9cb0ac8f.jpg', 'pending', '2026-05-22 06:38:03'),
(11, 5, 'Mouse', 'black mouse', 'Other', 'National Institute of Transport, Kigogo Road, Mabibo Relini, Ubungo, Ubungo Municipal, Dar es Salaam…', -6.80346786, 39.22147359, '2026-06-05', 'uploads/found_items/found_1780671848_6a22e568c6641.png', 'claimed', '2026-06-05 15:04:08'),
(12, 5, 'Lenovo PC', 'black lenovo laptop', 'Laptop', 'Kigogo Road, Mabibo Relini, Ubungo, Ubungo Municipal, Dar es Salaam, Coastal Zone, 21493, Tanzania', -6.80341134, 39.22093801, '2026-06-05', NULL, 'claimed', '2026-06-05 15:50:40');

-- --------------------------------------------------------

--
-- Table structure for table `incidents`
--

CREATE TABLE `incidents` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `incident_type` enum('theft','safety','misconduct','other') DEFAULT 'other',
  `location` varchar(200) NOT NULL,
  `incident_date` date NOT NULL,
  `status` enum('reported','investigating','resolved','closed') DEFAULT 'reported',
  `resolution_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `incidents`
--

INSERT INTO `incidents` (`id`, `user_id`, `title`, `description`, `incident_type`, `location`, `incident_date`, `status`, `resolution_notes`, `created_at`, `updated_at`) VALUES
(1, 1, 'Theft in mbagara', 'unfottunately my bag stolen', 'theft', 'Mbagara rangi tatu', '2026-05-20', 'investigating', '', '2026-05-19 23:38:05', '2026-05-19 23:41:31'),
(2, 1, 'Sapiente ex accusant', 'Numquam earum dolore', 'misconduct', 'Ex blanditiis tempor', '1973-10-03', 'reported', NULL, '2026-05-19 23:39:09', '2026-05-19 23:39:09'),
(3, 5, 'Stolen Phone', 'my phone was stollen', 'theft', 'Mbagara rangi tatu', '2026-05-21', 'reported', NULL, '2026-05-21 12:49:35', '2026-05-21 12:49:35'),
(4, 3, 'MY BAG WA STOLLEN', 'djv keq r jt4etrw', 'theft', 'Mbagara rangi tatu', '2026-05-20', 'closed', '', '2026-05-21 14:41:55', '2026-06-05 14:41:47'),
(5, 7, 'Stolen Phone', 'my samsung phone was stollen', 'theft', 'Ubungo, mabibo', '2026-06-18', 'reported', NULL, '2026-06-18 12:25:56', '2026-06-18 12:25:56');

-- --------------------------------------------------------

--
-- Table structure for table `incident_updates`
--

CREATE TABLE `incident_updates` (
  `id` int(11) NOT NULL,
  `incident_id` int(11) NOT NULL,
  `status` varchar(50) NOT NULL,
  `comment` text DEFAULT NULL,
  `updated_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `incident_updates`
--

INSERT INTO `incident_updates` (`id`, `incident_id`, `status`, `comment`, `updated_by`, `created_at`) VALUES
(1, 1, 'investigating', '', 1, '2026-05-19 23:41:31'),
(2, 4, 'closed', '', 2, '2026-06-05 14:41:47');

-- --------------------------------------------------------

--
-- Table structure for table `lost_items`
--

CREATE TABLE `lost_items` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `item_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `lost_location` varchar(200) NOT NULL,
  `gps_latitude` decimal(10,8) DEFAULT NULL,
  `gps_longitude` decimal(11,8) DEFAULT NULL,
  `lost_date` date NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `status` enum('pending','returned','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lost_items`
--

INSERT INTO `lost_items` (`id`, `user_id`, `item_name`, `description`, `category`, `lost_location`, `gps_latitude`, `gps_longitude`, `lost_date`, `image_path`, `status`, `created_at`) VALUES
(1, 1, 'Samsung Phone', 'ili kua nyeusi', 'Laptop', 'mabibo', NULL, NULL, '2026-05-19', 'uploads/lost_items/lost_1779230516_6a0ce7348c01d.png', 'returned', '2026-05-19 22:41:56'),
(2, 1, 'Igor Stone', 'Perspiciatis adipis', 'Keys', 'Corporis nemo repell', NULL, NULL, '2008-09-27', 'uploads/lost_items/lost_1779230554_6a0ce75a8ab8b.jpg', 'pending', '2026-05-19 22:42:34'),
(3, 1, 'Quinlan Buchanan', 'Fugit ipsam in aper', 'Wallet', 'Et inventore qui und', NULL, NULL, '2005-05-14', 'uploads/lost_items/lost_1779230639_6a0ce7af1eb1a.png', 'pending', '2026-05-19 22:43:59'),
(4, 1, 'Grace Black', 'Iste sit optio unde', 'Phone', 'Voluptatem laborios', NULL, NULL, '1971-04-26', 'uploads/lost_items/lost_1779230666_6a0ce7cab8de6.jpg', 'pending', '2026-05-19 22:44:26'),
(5, 1, 'tecno phone', 'my tecno', 'Phone', 'manzese', NULL, NULL, '2026-05-12', 'uploads/lost_items/lost_1779234637_6a0cf74da64a5.jpg', 'pending', '2026-05-19 23:50:37'),
(6, 1, 'infinix', 'phone', 'Phone', 'mabibo', NULL, NULL, '2026-05-19', 'uploads/lost_items/lost_1779234676_6a0cf774e5e06.png', 'pending', '2026-05-19 23:51:16'),
(7, 4, 'tecno phone', 'i lost it ', 'Phone', 'manzese', NULL, NULL, '2026-05-15', 'uploads/lost_items/lost_1779236477_6a0cfe7d505cc.png', 'pending', '2026-05-20 00:21:17'),
(8, 4, 'Samsung Phone', 'i lost it', 'Phone', 'mabibo', NULL, NULL, '2026-05-18', 'uploads/lost_items/lost_1779236514_6a0cfea2bf961.jpg', 'pending', '2026-05-20 00:21:54'),
(9, 1, 'Dell PC', ' shd jsd napioen', 'Laptop', 'manzese', NULL, NULL, '2026-05-19', 'uploads/lost_items/lost_1779295039_6a0de33f0f3c8.png', 'pending', '2026-05-20 16:37:19'),
(10, 5, 'iphone 2', 'Enim eum quidem inci', 'Phone', 'Dolor quos accusanti', NULL, NULL, '2016-08-01', 'uploads/lost_items/lost_1779367725_6a0eff2d0507c.png', 'pending', '2026-05-21 12:48:45'),
(11, 3, 'Tablet', 'tablet nyeusi na imechakaa kidogo', 'Phone', 'Mabibo', NULL, NULL, '2026-05-20', 'uploads/lost_items/lost_1779369942_6a0f07d6685f8.png', 'returned', '2026-05-21 13:25:42'),
(12, 3, 'Gucci Bag', 'Gucci bag with brown color', 'Bag', 'manzese', NULL, NULL, '2026-05-19', 'uploads/lost_items/lost_1779373403_6a0f155bb525b.jpg', 'returned', '2026-05-21 14:23:23'),
(13, 7, 'smart watch', 'apple smart watch with black belt', 'Other', 'block 15', NULL, NULL, '2026-05-20', 'uploads/lost_items/lost_1779375033_6a0f1bb949a3e.jpg', 'pending', '2026-05-21 14:50:33'),
(14, 8, 'HP Laptop ', 'Silver laptop ', 'Laptop', 'NIT', NULL, NULL, '2026-05-31', 'uploads/lost_items/lost_1780392826_6a1ea37a5ae48.jpg', 'pending', '2026-06-02 09:33:46'),
(15, 8, 'Text Book', 'black and red textbook', 'Other', 'manzese', -6.80364788, 39.22163983, '2026-06-02', 'uploads/lost_items/lost_1780421546_6a1f13aab17ed.png', 'pending', '2026-06-02 17:32:26'),
(16, 2, 'Mouse', 'black Dell mouse', 'Other', 'Kigogo Road, Mabibo Relini, Ubungo, Ubungo Municipal, Dar es Salaam, Coastal Zone, 21493, Tanzania', -6.80341000, 39.22094000, '2026-06-05', 'uploads/lost_items/lost_1780671244_6a22e30cdc98b.png', 'returned', '2026-06-05 14:54:04'),
(17, 2, 'Lenovo PC', 'black lenovo pc', 'Laptop', 'National Institute of Transport, Mabibo Road, Mabibo Relini, Mabibo, Ubungo Municipal, Dar es Salaam…', -6.80365798, 39.22122388, '2026-06-04', 'uploads/lost_items/lost_1780674316_6a22ef0ceb191.png', 'returned', '2026-06-05 15:45:16'),
(18, 5, 'Lenovo PC', 'black lenovo pc', 'Laptop', 'Kigogo Road, Mabibo Relini, Ubungo, Ubungo Municipal, Dar es Salaam, Coastal Zone, 21493, Tanzania', -6.80354000, 39.22131600, '2026-06-05', 'uploads/lost_items/lost_1780674419_6a22ef7348769.png', 'pending', '2026-06-05 15:46:59');

-- --------------------------------------------------------

--
-- Table structure for table `matches`
--

CREATE TABLE `matches` (
  `id` int(11) NOT NULL,
  `lost_item_id` int(11) NOT NULL,
  `found_item_id` int(11) NOT NULL,
  `match_score` int(11) DEFAULT 0,
  `status` enum('pending','confirmed','rejected','resolved') DEFAULT 'pending',
  `notified_at` timestamp NULL DEFAULT NULL,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `matches`
--

INSERT INTO `matches` (`id`, `lost_item_id`, `found_item_id`, `match_score`, `status`, `notified_at`, `resolved_at`, `created_at`) VALUES
(1, 1, 1, 30, 'confirmed', NULL, '2026-05-19 23:48:50', '2026-05-19 23:14:23'),
(2, 4, 1, 30, 'rejected', NULL, '2026-05-19 23:49:16', '2026-05-19 23:14:23'),
(3, 4, 3, 30, 'pending', NULL, NULL, '2026-05-19 23:52:02'),
(4, 5, 3, 60, 'pending', NULL, NULL, '2026-05-19 23:52:02'),
(5, 6, 3, 30, 'pending', NULL, NULL, '2026-05-19 23:52:02'),
(6, 7, 4, 60, 'pending', NULL, NULL, '2026-05-21 12:50:23'),
(7, 8, 4, 45, 'pending', NULL, NULL, '2026-05-21 12:50:23'),
(8, 10, 4, 30, 'rejected', NULL, '2026-05-21 13:23:46', '2026-05-21 12:50:23'),
(9, 10, 5, 60, 'pending', NULL, NULL, '2026-05-21 13:27:30'),
(10, 10, 6, 30, 'rejected', NULL, '2026-05-21 13:31:36', '2026-05-21 13:27:30'),
(11, 11, 5, 30, 'pending', NULL, NULL, '2026-05-21 13:27:30'),
(12, 11, 6, 65, 'confirmed', NULL, '2026-05-21 13:30:27', '2026-05-21 13:27:30'),
(13, 12, 2, 30, 'rejected', NULL, '2026-05-21 14:33:35', '2026-05-21 14:32:28'),
(14, 12, 7, 80, 'rejected', NULL, '2026-05-21 14:33:44', '2026-05-21 14:32:28'),
(15, 12, 8, 80, 'confirmed', NULL, '2026-05-21 14:33:52', '2026-05-21 14:32:28'),
(16, 13, 9, 80, 'pending', NULL, NULL, '2026-06-02 16:44:18'),
(17, 15, 11, 50, 'rejected', NULL, '2026-06-05 15:43:21', '2026-06-05 15:05:05'),
(18, 16, 11, 65, 'resolved', NULL, '2026-06-05 15:39:15', '2026-06-05 15:05:05'),
(19, 9, 12, 45, 'pending', NULL, NULL, '2026-06-05 15:50:48'),
(20, 14, 12, 50, 'pending', NULL, NULL, '2026-06-05 15:50:48'),
(21, 17, 12, 80, 'resolved', NULL, '2026-06-05 15:51:42', '2026-06-05 15:50:48'),
(22, 18, 12, 80, 'pending', NULL, NULL, '2026-06-05 15:50:48');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` enum('match','incident_update','reminder','system') DEFAULT 'system',
  `message` text NOT NULL,
  `related_url` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `type`, `message`, `related_url`, `is_read`, `created_at`) VALUES
(1, 1, 'match', 'Potential match found for your lost item \'Samsung Phone\'. A found item matches.', '/lost_item_tracker/index.php?page=matches/view', 1, '2026-05-19 23:14:23'),
(2, 1, 'match', 'Potential match found for the item you reported as found: \'Samsung Phone\'.', '/lost_item_tracker/index.php?page=matches/view', 1, '2026-05-19 23:14:23'),
(3, 1, 'match', 'Potential match found for your lost item \'Grace Black\'. A found item matches.', '/lost_item_tracker/index.php?page=matches/view', 1, '2026-05-19 23:14:23'),
(4, 1, 'match', 'Potential match found for the item you reported as found: \'Samsung Phone\'.', '/lost_item_tracker/index.php?page=matches/view', 1, '2026-05-19 23:14:23'),
(5, 1, 'incident_update', 'Your incident \'Theft in mbagara\' status has been updated to: Investigating', '/lost_item_tracker/index.php?page=incidents/list', 1, '2026-05-19 23:41:31'),
(6, 1, 'match', 'Potential match found for your lost item \'Grace Black\'. A found item matches.', '/lost_item_tracker/index.php?page=matches&action=view', 1, '2026-05-19 23:52:02'),
(7, 1, 'match', 'Potential match found for the item you reported as found: \'tecno phone\'.', '/lost_item_tracker/index.php?page=matches&action=view', 1, '2026-05-19 23:52:02'),
(8, 1, 'match', 'Potential match found for your lost item \'tecno phone\'. A found item matches.', '/lost_item_tracker/index.php?page=matches&action=view', 1, '2026-05-19 23:52:02'),
(9, 1, 'match', 'Potential match found for the item you reported as found: \'tecno phone\'.', '/lost_item_tracker/index.php?page=matches&action=view', 1, '2026-05-19 23:52:02'),
(10, 1, 'match', 'Potential match found for your lost item \'infinix\'. A found item matches.', '/lost_item_tracker/index.php?page=matches&action=view', 1, '2026-05-19 23:52:02'),
(11, 1, 'match', 'Potential match found for the item you reported as found: \'tecno phone\'.', '/lost_item_tracker/index.php?page=matches&action=view', 1, '2026-05-19 23:52:02'),
(12, 4, 'match', 'Potential match found for your lost item \'tecno phone\'. A found item matches.', '/lost_item_tracker/index.php?page=matches&action=view', 1, '2026-05-21 12:50:23'),
(13, 1, 'match', 'Potential match found for the item you reported as found: \'tecno phone\'.', '/lost_item_tracker/index.php?page=matches&action=view', 1, '2026-05-21 12:50:23'),
(14, 4, 'match', 'Potential match found for your lost item \'Samsung Phone\'. A found item matches.', '/lost_item_tracker/index.php?page=matches&action=view', 1, '2026-05-21 12:50:23'),
(15, 1, 'match', 'Potential match found for the item you reported as found: \'tecno phone\'.', '/lost_item_tracker/index.php?page=matches&action=view', 1, '2026-05-21 12:50:23'),
(16, 5, 'match', 'Potential match found for your lost item \'iphone 2\'. A found item matches.', '/lost_item_tracker/index.php?page=matches&action=view', 1, '2026-05-21 12:50:23'),
(17, 1, 'match', 'Potential match found for the item you reported as found: \'tecno phone\'.', '/lost_item_tracker/index.php?page=matches&action=view', 1, '2026-05-21 12:50:23'),
(18, 5, 'match', 'Potential match found for your lost item \'iphone 2\'. A found item matches.', '/lost_item_tracker/index.php?page=matches&action=view', 1, '2026-05-21 13:27:30'),
(19, 3, 'match', 'Potential match found for the item you reported as found: \'iphone 2\'.', '/lost_item_tracker/index.php?page=matches&action=view', 1, '2026-05-21 13:27:30'),
(20, 5, 'match', 'Potential match found for your lost item \'iphone 2\'. A found item matches.', '/lost_item_tracker/index.php?page=matches&action=view', 1, '2026-05-21 13:27:30'),
(21, 5, 'match', 'Potential match found for the item you reported as found: \'Tablet\'.', '/lost_item_tracker/index.php?page=matches&action=view', 1, '2026-05-21 13:27:30'),
(22, 3, 'match', 'Potential match found for your lost item \'Tablet\'. A found item matches.', '/lost_item_tracker/index.php?page=matches&action=view', 1, '2026-05-21 13:27:30'),
(23, 3, 'match', 'Potential match found for the item you reported as found: \'iphone 2\'.', '/lost_item_tracker/index.php?page=matches&action=view', 1, '2026-05-21 13:27:30'),
(24, 3, 'match', 'Potential match found for your lost item \'Tablet\'. A found item matches.', '/lost_item_tracker/index.php?page=matches&action=view', 1, '2026-05-21 13:27:30'),
(25, 5, 'match', 'Potential match found for the item you reported as found: \'Tablet\'.', '/lost_item_tracker/index.php?page=matches&action=view', 1, '2026-05-21 13:27:30'),
(26, 3, 'match', 'Potential match found for your lost item \'Gucci Bag\'. A found item matches.', '/lost_item_tracker/index.php?page=matches&action=view', 1, '2026-05-21 14:32:28'),
(27, 1, 'match', 'Potential match found for the item you reported as found: \'Shelley Wooten\'.', '/lost_item_tracker/index.php?page=matches&action=view', 0, '2026-05-21 14:32:28'),
(28, 3, 'match', 'Potential match found for your lost item \'Gucci Bag\'. A found item matches.', '/lost_item_tracker/index.php?page=matches&action=view', 1, '2026-05-21 14:32:28'),
(29, 5, 'match', 'Potential match found for the item you reported as found: \'Gucci Bag\'.', '/lost_item_tracker/index.php?page=matches&action=view', 1, '2026-05-21 14:32:28'),
(30, 3, 'match', 'Potential match found for your lost item \'Gucci Bag\'. A found item matches.', '/lost_item_tracker/index.php?page=matches&action=view', 1, '2026-05-21 14:32:28'),
(31, 5, 'match', 'Potential match found for the item you reported as found: \'Gucci Bag\'.', '/lost_item_tracker/index.php?page=matches&action=view', 1, '2026-05-21 14:32:28'),
(32, 7, 'match', 'Potential match found for your lost item \'smart watch\'. A found item matches.', '/lost_item_tracker/index.php?page=matches&action=view', 1, '2026-06-02 16:44:18'),
(33, 8, 'match', 'Potential match found for the item you reported as found: \'smart watch\'.', '/lost_item_tracker/index.php?page=matches&action=view', 1, '2026-06-02 16:44:18'),
(34, 3, 'incident_update', 'Your incident \'MY BAG WA STOLLEN\' status has been updated to: Closed', '/lost_item_tracker/index.php?page=incidents/list', 0, '2026-06-05 14:41:47'),
(35, 8, 'match', 'Potential match found for your lost item \'Text Book\'. A found item matches.', '/lost_item_tracker/index.php?page=matches/view', 1, '2026-06-05 15:05:05'),
(36, 5, 'match', 'Potential match found for the item you reported as found: \'Mouse\'.', '/lost_item_tracker/index.php?page=matches/view', 1, '2026-06-05 15:05:05'),
(37, 2, 'match', 'Potential match found for your lost item \'Mouse\'. A found item matches.', '/lost_item_tracker/index.php?page=matches/view', 1, '2026-06-05 15:05:05'),
(38, 5, 'match', 'Potential match found for the item you reported as found: \'Mouse\'.', '/lost_item_tracker/index.php?page=matches/view', 1, '2026-06-05 15:05:05'),
(39, 2, 'match', 'Match confirmed. You can now resolve it when the item is returned.', '/lost_item_tracker/index.php?page=matches/view', 1, '2026-06-05 15:35:59'),
(40, 5, 'match', 'Match confirmed. You can now resolve it when the item is returned.', '/lost_item_tracker/index.php?page=matches/view', 1, '2026-06-05 15:35:59'),
(41, 2, 'match', 'Match resolved. The lost item has been marked as returned.', '/lost_item_tracker/index.php?page=matches/view', 1, '2026-06-05 15:36:04'),
(42, 5, 'match', 'Match resolved. The found item has been marked as claimed.', '/lost_item_tracker/index.php?page=matches/view', 1, '2026-06-05 15:36:04'),
(43, 2, 'match', 'Match resolved. The lost item has been marked as returned.', '/lost_item_tracker/index.php?page=matches/view', 1, '2026-06-05 15:39:15'),
(44, 5, 'match', 'Match resolved. The found item has been marked as claimed.', '/lost_item_tracker/index.php?page=matches/view', 1, '2026-06-05 15:39:15'),
(45, 1, 'match', 'Potential match found for your lost item \'Dell PC\'.', '/lost_item_tracker/index.php?page=matches/view', 0, '2026-06-05 15:50:48'),
(46, 5, 'match', 'Potential match found for the item you reported as found: \'Lenovo PC\'.', '/lost_item_tracker/index.php?page=matches/view', 1, '2026-06-05 15:50:48'),
(47, 8, 'match', 'Potential match found for your lost item \'HP Laptop \'.', '/lost_item_tracker/index.php?page=matches/view', 1, '2026-06-05 15:50:48'),
(48, 5, 'match', 'Potential match found for the item you reported as found: \'Lenovo PC\'.', '/lost_item_tracker/index.php?page=matches/view', 1, '2026-06-05 15:50:48'),
(49, 2, 'match', 'Potential match found for your lost item \'Lenovo PC\'.', '/lost_item_tracker/index.php?page=matches/view', 1, '2026-06-05 15:50:48'),
(50, 5, 'match', 'Potential match found for the item you reported as found: \'Lenovo PC\'.', '/lost_item_tracker/index.php?page=matches/view', 1, '2026-06-05 15:50:48'),
(51, 5, 'match', 'Potential match found for your lost item \'Lenovo PC\'.', '/lost_item_tracker/index.php?page=matches/view', 1, '2026-06-05 15:50:48'),
(52, 5, 'match', 'Potential match found for the item you reported as found: \'Lenovo PC\'.', '/lost_item_tracker/index.php?page=matches/view', 1, '2026-06-05 15:50:48'),
(53, 2, 'match', 'Match confirmed. You can now resolve it when the item is returned.', '/lost_item_tracker/index.php?page=matches/view', 1, '2026-06-05 15:51:37'),
(54, 5, 'match', 'Match confirmed. You can now resolve it when the item is returned.', '/lost_item_tracker/index.php?page=matches/view', 0, '2026-06-05 15:51:37'),
(55, 2, 'match', 'Match resolved. The lost item has been marked as returned.', '/lost_item_tracker/index.php?page=matches/view', 1, '2026-06-05 15:51:42'),
(56, 5, 'match', 'Match resolved. The found item has been marked as claimed.', '/lost_item_tracker/index.php?page=matches/view', 0, '2026-06-05 15:51:42');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('student','staff','admin') DEFAULT 'student',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `email`, `password`, `phone`, `role`, `created_at`, `updated_at`) VALUES
(1, 'System Administrator', 'admin@gmail.com', '$2y$10$5XJXhg6BxIVn1R69Cxhq5On80pp9WVOI9b0vSUMCBGU0m69Ygy8gi', '0712345678', 'admin', '2026-05-19 20:37:11', '2026-05-21 13:42:08'),
(2, 'scar avo', 'scar@gmail.com', '$2y$10$1EjLWX9/JunuBurcJn89IOaxcBeIdoGSNnMvHnWcccRVrclLIzAs.', '0711111111', 'staff', '2026-05-20 00:13:33', '2026-06-05 14:53:02'),
(3, 'mwita mwita', 'mwita@gmail.com', '$2y$10$9jv8Uxn1F/kRcvKP4Gry3e1r1lSAt8zdWMy1n43T8XHT8J20xebSG', '0612345678', 'student', '2026-05-20 00:16:46', '2026-05-20 00:17:40'),
(4, 'matha', 'matha@gmail.com', '$2y$10$3J48o1vR9vF3onS0r//.vuAMrNVaO1am36bOUMH4N5JLJoyK9JUwG', '0712345678', 'admin', '2026-05-20 00:17:27', '2026-05-21 13:42:37'),
(5, 'amina chausiku', 'amina@gmail.com', '$2y$10$SPPcTn8PemFRluBWNrgY9e/KX5UQOY3VAqzvomsMZxGYUOz8YBeJG', '0811111113', 'student', '2026-05-20 00:27:56', '2026-05-20 00:37:09'),
(6, 'james mwita', 'james@gmail.com', '$2y$10$xTE8IFP7RrlChxSixpisKOGMpT2Rx3F78.7LADR/erJ1KrvW9Vkdy', '0713000007', 'staff', '2026-05-21 13:43:20', '2026-05-21 13:43:20'),
(7, 'martha masinda', 'martha@gmail.com', '$2y$10$P/dr9nDEWgrovW7HSmKLfeVCK7QBAk6ycCX9oA/dF2fj/tetgLY9y', '0674178186', 'student', '2026-05-21 14:46:27', '2026-05-21 14:46:27'),
(8, 'walles moses', 'walles@gmail.com', '$2y$10$AuSfNQ36pgLgehq.6.WWwuEOw9g4twJtpBOlBGamK1HChJDmPJ1Au', '0756178186', 'student', '2026-05-21 14:52:37', '2026-06-02 09:27:05');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `found_items`
--
ALTER TABLE `found_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `incidents`
--
ALTER TABLE `incidents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `incident_updates`
--
ALTER TABLE `incident_updates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `incident_id` (`incident_id`),
  ADD KEY `updated_by` (`updated_by`);

--
-- Indexes for table `lost_items`
--
ALTER TABLE `lost_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `matches`
--
ALTER TABLE `matches`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lost_item_id` (`lost_item_id`),
  ADD KEY `found_item_id` (`found_item_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `found_items`
--
ALTER TABLE `found_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `incidents`
--
ALTER TABLE `incidents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `incident_updates`
--
ALTER TABLE `incident_updates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `lost_items`
--
ALTER TABLE `lost_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `matches`
--
ALTER TABLE `matches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `found_items`
--
ALTER TABLE `found_items`
  ADD CONSTRAINT `found_items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `incidents`
--
ALTER TABLE `incidents`
  ADD CONSTRAINT `incidents_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `incident_updates`
--
ALTER TABLE `incident_updates`
  ADD CONSTRAINT `incident_updates_ibfk_1` FOREIGN KEY (`incident_id`) REFERENCES `incidents` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `incident_updates_ibfk_2` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `lost_items`
--
ALTER TABLE `lost_items`
  ADD CONSTRAINT `lost_items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `matches`
--
ALTER TABLE `matches`
  ADD CONSTRAINT `matches_ibfk_1` FOREIGN KEY (`lost_item_id`) REFERENCES `lost_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `matches_ibfk_2` FOREIGN KEY (`found_item_id`) REFERENCES `found_items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
