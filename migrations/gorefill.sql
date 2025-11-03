-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 03, 2025 at 04:15 PM
-- Server version: 8.0.30
-- PHP Version: 8.4.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gorefill`
--

-- --------------------------------------------------------

--
-- Table structure for table `addresses`
--

CREATE TABLE `addresses` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `label` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `street` text COLLATE utf8mb4_general_ci,
  `city` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `province` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `regency` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `district` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `village` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `postal_code` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `lat` decimal(10,7) DEFAULT NULL,
  `lng` decimal(10,7) DEFAULT NULL,
  `is_default` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `addresses`
--

INSERT INTO `addresses` (`id`, `user_id`, `label`, `street`, `city`, `province`, `regency`, `district`, `village`, `postal_code`, `lat`, `lng`, `is_default`, `created_at`) VALUES
(15, 57, 'Rumah', 'Jalan Bimasakti RT 03 RW 05\r\n', '', 'Jawa Tengah', 'Tegal', 'Dukuhturi', 'Pekauman Kulon', '52192', '-6.8904426', '109.1338384', 1, '2025-10-26 11:36:15'),
(16, 55, 'Rumah', 'Pacific Mall, Jl. Kapten Sudibyo Lantai Dasar, Pekauman, Kec. Tegal Bar., Kota Tegal, Jawa Tengah 52125', 'Tegal', 'Jawa Tengah', 'Tegal', 'Dukuhturi', 'Pekauman Kulon', '52192', '-6.8904426', '109.1338384', 1, '2025-10-26 13:02:36');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `slug` varchar(120) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Air Minum', 'air-minum', 'Produk air minum isi ulang galon dan kemasan', '2025-10-25 09:10:40', '2025-10-25 09:10:40'),
(2, 'Gas', 'gas', 'LPG dan tabung gas untuk rumah tangga', '2025-10-25 09:10:40', '2025-10-25 09:10:40'),
(3, 'Peralatan', 'peralatan', 'Peralatan dapur dan rumah tangga', '2025-10-25 09:10:40', '2025-10-25 09:10:40'),
(4, 'Tinta', 'tinta', 'Tinta printer dan cartridge isi ulang', '2025-10-25 09:10:40', '2025-10-25 09:10:40'),
(5, 'Aksesoris', 'aksesoris', 'Aksesoris pendukung produk utama', '2025-10-25 09:10:40', '2025-10-25 09:10:40'),
(6, 'Sabun & Detergen', 'sabun-detergen', 'Sabun cair, detergen, dan produk literan pembersih', '2025-10-25 09:10:40', '2025-10-25 09:10:40'),
(7, 'Minyak Goreng', 'minyak-goreng', 'Minyak goreng isi ulang dan kemasan', '2025-10-25 09:10:40', '2025-10-25 09:10:40');

-- --------------------------------------------------------

--
-- Table structure for table `courier_locations`
--

CREATE TABLE `courier_locations` (
  `id` int NOT NULL,
  `courier_id` int NOT NULL,
  `lat` decimal(10,7) DEFAULT NULL,
  `lng` decimal(10,7) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courier_locations`
--

INSERT INTO `courier_locations` (`id`, `courier_id`, `lat`, `lng`, `updated_at`) VALUES
(77, 52, '-7.0471320', '109.0298440', '2025-10-27 11:59:28'),
(78, 52, '-7.0471320', '109.0298440', '2025-10-27 11:59:40'),
(79, 52, '-7.0471320', '109.0298440', '2025-10-27 11:59:52'),
(80, 52, '-7.0471320', '109.0298440', '2025-10-27 12:02:53'),
(81, 52, '-7.0471320', '109.0298440', '2025-10-27 12:08:22'),
(82, 52, '-7.0471320', '109.0298440', '2025-10-27 12:13:39'),
(83, 52, '-7.0471320', '109.0298440', '2025-10-27 12:13:41'),
(84, 52, '-7.0471320', '109.0298440', '2025-10-27 12:13:43'),
(85, 52, '-7.0471320', '109.0298440', '2025-10-27 12:14:04'),
(86, 52, '-7.0471320', '109.0298440', '2025-10-27 12:14:34'),
(87, 52, '-7.0471320', '109.0298440', '2025-10-27 12:15:23'),
(88, 52, '-7.0471320', '109.0298440', '2025-10-27 12:15:33'),
(89, 52, '-7.0471320', '109.0298440', '2025-10-27 12:16:02'),
(90, 52, '-7.0471320', '109.0298440', '2025-10-27 12:16:58'),
(91, 52, '-7.0471320', '109.0298440', '2025-10-27 12:17:05'),
(92, 52, '-7.0471320', '109.0298440', '2025-10-27 12:17:19'),
(93, 52, '-7.0471320', '109.0298440', '2025-10-27 12:17:26'),
(94, 52, '-7.0471320', '109.0298440', '2025-10-27 12:35:23'),
(95, 52, '-7.0471320', '109.0298440', '2025-10-27 12:35:48'),
(96, 52, '-7.0471320', '109.0298440', '2025-10-27 12:39:31'),
(97, 52, '-7.0471320', '109.0298440', '2025-10-27 12:43:22'),
(98, 52, '-7.0471320', '109.0298440', '2025-10-27 12:43:32'),
(99, 52, '-7.0471320', '109.0298440', '2025-10-27 12:55:37'),
(100, 52, '-7.0471320', '109.0298440', '2025-10-27 13:01:07'),
(101, 52, '-7.0471320', '109.0298440', '2025-10-27 13:01:09'),
(102, 52, '-7.0471320', '109.0298440', '2025-10-27 13:03:28'),
(103, 52, '-7.0471320', '109.0298440', '2025-10-27 13:03:34'),
(104, 52, '-7.0471320', '109.0298440', '2025-10-27 13:03:38'),
(105, 52, '-7.0471320', '109.0298440', '2025-10-27 13:09:00'),
(106, 52, '-7.0471320', '109.0298440', '2025-10-27 13:16:34'),
(107, 52, '-7.0471320', '109.0298440', '2025-10-27 13:27:39'),
(108, 52, '-7.0471320', '109.0298440', '2025-10-27 13:27:59'),
(109, 52, '-7.0471320', '109.0298440', '2025-10-27 13:28:20'),
(110, 52, '-7.0471320', '109.0298440', '2025-10-27 13:29:47'),
(111, 52, '-7.0471320', '109.0298440', '2025-10-27 13:30:17'),
(112, 52, '-7.0471320', '109.0298440', '2025-10-27 13:30:37'),
(113, 52, '-7.0471320', '109.0298440', '2025-10-27 13:35:51'),
(114, 52, '-7.0471320', '109.0298440', '2025-10-27 13:41:46'),
(115, 52, '-7.0471320', '109.0298440', '2025-10-27 13:42:10'),
(116, 52, '-7.0471320', '109.0298440', '2025-10-27 13:42:19'),
(117, 52, '-7.0471320', '109.0298440', '2025-10-27 13:46:13'),
(118, 52, '-7.0471320', '109.0298440', '2025-10-27 13:46:19'),
(119, 52, '-7.0471320', '109.0298440', '2025-10-27 13:46:34'),
(120, 52, '-7.0471320', '109.0298440', '2025-10-27 13:54:08'),
(121, 52, '-7.0471320', '109.0298440', '2025-10-27 13:57:52'),
(122, 52, '-7.0471320', '109.0298440', '2025-10-27 13:58:29'),
(123, 52, '-7.0471320', '109.0298440', '2025-10-27 13:58:39'),
(124, 52, '-7.0471320', '109.0298440', '2025-10-27 14:00:10'),
(125, 52, '-7.0471320', '109.0298440', '2025-10-27 14:04:42'),
(126, 52, '-7.0471320', '109.0298440', '2025-10-27 14:04:45'),
(127, 52, '-7.0471320', '109.0298440', '2025-10-27 14:04:49'),
(128, 52, '-7.0471320', '109.0298440', '2025-10-27 14:04:57'),
(129, 52, '-7.0471320', '109.0298440', '2025-10-27 14:05:00'),
(130, 52, '-7.0471320', '109.0298440', '2025-10-27 14:07:10'),
(131, 52, '-7.0471320', '109.0298440', '2025-10-27 14:07:44'),
(132, 52, '-7.0471320', '109.0298440', '2025-10-27 14:07:46'),
(133, 52, '-7.0471320', '109.0298440', '2025-10-27 14:07:54'),
(134, 52, '-7.0471320', '109.0298440', '2025-10-27 14:22:04'),
(135, 52, '-7.0471320', '109.0298440', '2025-10-27 14:22:23'),
(136, 52, '-7.0471320', '109.0298440', '2025-10-27 14:22:35'),
(137, 52, '-7.0471320', '109.0298440', '2025-10-27 14:23:40'),
(138, 52, '-7.0471320', '109.0298440', '2025-10-27 14:23:52'),
(139, 52, '-7.0471320', '109.0298440', '2025-10-27 14:27:03'),
(140, 52, '-7.0471320', '109.0298440', '2025-10-27 17:51:43'),
(141, 52, '-7.0471320', '109.0298440', '2025-10-27 17:51:45'),
(142, 52, '-7.0471320', '109.0298440', '2025-10-27 17:51:57'),
(143, 52, '-7.0471320', '109.0298440', '2025-10-27 17:52:08'),
(144, 52, '-7.0471320', '109.0298440', '2025-10-27 17:52:17'),
(145, 52, '-7.0471320', '109.0298440', '2025-10-27 17:55:30'),
(146, 52, '-7.0471320', '109.0298440', '2025-10-27 17:55:57'),
(147, 52, '-7.0471320', '109.0298440', '2025-10-28 07:45:36'),
(148, 52, '-7.0471320', '109.0298440', '2025-10-28 07:45:44'),
(149, 52, '-7.0471320', '109.0298440', '2025-10-28 07:46:10'),
(150, 52, '-7.0471320', '109.0298440', '2025-10-28 07:46:13');

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

CREATE TABLE `favorites` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `product_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `voucher_id` int DEFAULT NULL,
  `transaction_id` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `total` decimal(12,2) NOT NULL,
  `status` enum('pending','confirmed','packing','shipped','delivered','cancelled','expired') COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `payment_method` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `payment_type` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `payment_status` enum('pending','unpaid','paid','failed','expired','cancelled','refund') COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `midtrans_status` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `fraud_status` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `note` text COLLATE utf8mb4_general_ci,
  `callback_data` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `order_number` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Format: ORD-YYYYMMDD-XXXX',
  `address_id` int UNSIGNED DEFAULT NULL,
  `subtotal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `discount_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `snap_token` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Midtrans Snap Token',
  `paid_at` timestamp NULL DEFAULT NULL,
  `shipping_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `shipping_phone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `shipping_address` text COLLATE utf8mb4_general_ci,
  `shipping_city` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `shipping_postal_code` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `shipping_latitude` decimal(10,8) DEFAULT NULL,
  `shipping_longitude` decimal(11,8) DEFAULT NULL,
  `courier` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'JNE, TIKI, POS, etc',
  `courier_id` int DEFAULT NULL,
  `tracking_number` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `transaction_status` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'capture, settlement, pending, deny, cancel, expire',
  `transaction_time` timestamp NULL DEFAULT NULL,
  `settlement_time` timestamp NULL DEFAULT NULL,
  `gross_amount` decimal(12,2) DEFAULT NULL,
  `currency` varchar(3) COLLATE utf8mb4_general_ci DEFAULT 'IDR',
  `signature_key` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `bank` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `va_number` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `bill_key` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `biller_code` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pdf_url` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `finish_redirect_url` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `expiry_time` timestamp NULL DEFAULT NULL,
  `store` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `payment_code` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `customer_email` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `customer_phone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `refund_amount` decimal(12,2) DEFAULT '0.00',
  `refund_reason` text COLLATE utf8mb4_general_ci,
  `refunded_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `voucher_id`, `transaction_id`, `total`, `status`, `payment_method`, `payment_type`, `payment_status`, `midtrans_status`, `fraud_status`, `note`, `callback_data`, `created_at`, `order_number`, `address_id`, `subtotal`, `discount_amount`, `snap_token`, `paid_at`, `shipping_name`, `shipping_phone`, `shipping_address`, `shipping_city`, `shipping_postal_code`, `shipping_latitude`, `shipping_longitude`, `courier`, `courier_id`, `tracking_number`, `transaction_status`, `transaction_time`, `settlement_time`, `gross_amount`, `currency`, `signature_key`, `bank`, `va_number`, `bill_key`, `biller_code`, `pdf_url`, `finish_redirect_url`, `expiry_time`, `store`, `payment_code`, `customer_email`, `customer_phone`, `refund_amount`, `refund_reason`, `refunded_at`, `updated_at`) VALUES
(47, 55, 63, '757c9396-69a7-4b86-a544-057674cc9bd3', '83700.00', 'packing', 'qris', 'qris', 'paid', 'settlement', 'accept', NULL, '{\"currency\": \"IDR\", \"order_id\": \"ORD-20251029-112856-51CF\", \"status_code\": \"200\", \"fraud_status\": \"accept\", \"gross_amount\": \"83700.00\", \"payment_type\": \"qris\", \"signature_key\": \"3c1f15ec41ab50d72219236e0d79408db2bface5c30d3455797ae3dbe81c2112e3b503f0fdbaa774b09d823b860fb13479bfec35bf1e2dc3a43a09fbba60ea5a\", \"status_message\": \"Success, transaction is found\", \"transaction_id\": \"757c9396-69a7-4b86-a544-057674cc9bd3\", \"settlement_time\": \"2025-10-29 11:29:16\", \"transaction_time\": \"2025-10-29 11:29:00\", \"transaction_status\": \"settlement\"}', '2025-10-29 04:28:56', 'ORD-20251029-112856-51CF', 16, '93000.00', '9300.00', 'e18b1b97-e830-4e46-af39-8dd8a18af074', '2025-10-29 04:29:17', 'Rumah', '-', 'Pacific Mall, Jl. Kapten Sudibyo Lantai Dasar, Pekauman, Kec. Tegal Bar., Kota Tegal, Jawa Tengah 52125, Pekauman Kulon, Dukuhturi', 'Tegal', '52192', '-6.89044260', '109.13383840', NULL, 52, NULL, 'settlement', '2025-10-29 04:29:00', '2025-10-29 04:29:16', '83700.00', 'IDR', '3c1f15ec41ab50d72219236e0d79408db2bface5c30d3455797ae3dbe81c2112e3b503f0fdbaa774b09d823b860fb13479bfec35bf1e2dc3a43a09fbba60ea5a', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'usera@gorefill.test', '081300000001', '0.00', NULL, NULL, '2025-10-29 05:29:46');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int NOT NULL,
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `product_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `product_image` varchar(500) COLLATE utf8mb4_general_ci NOT NULL,
  `product_price` decimal(12,2) NOT NULL COMMENT 'Harga asli produk saat dibeli',
  `quantity` int UNSIGNED NOT NULL DEFAULT '1',
  `price` decimal(12,2) DEFAULT NULL,
  `subtotal` decimal(12,2) NOT NULL COMMENT 'qty * price',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `product_image`, `product_price`, `quantity`, `price`, `subtotal`, `created_at`) VALUES
(314, 47, 1, 'Refill Shampoo Herbal 500ml', 'shampoo.jpg', '25000.00', 1, '25000.00', '25000.00', '2025-10-29 04:28:56'),
(315, 47, 8, 'Refill Sabun Cuci Piring 1L', 'sabun_piring.jpg', '18000.00', 1, '18000.00', '18000.00', '2025-10-29 04:28:56'),
(316, 47, 9, 'Refill Pelicin Pakaian 1L', 'pelicin.jpg', '22000.00', 1, '22000.00', '22000.00', '2025-10-29 04:28:56'),
(317, 47, 6, 'Refill Conditioner 500ml', 'conditioner.jpg', '28000.00', 1, '28000.00', '28000.00', '2025-10-29 04:28:56');

-- --------------------------------------------------------

--
-- Table structure for table `payment_logs`
--

CREATE TABLE `payment_logs` (
  `id` int NOT NULL,
  `order_id` int DEFAULT NULL,
  `midtrans_order_id` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `payload` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `category_id` int NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `stock` int DEFAULT '0',
  `rating` float DEFAULT '0',
  `badge_env` tinyint(1) DEFAULT '0',
  `description` text COLLATE utf8mb4_general_ci,
  `image` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `slug`, `category_id`, `price`, `stock`, `rating`, `badge_env`, `description`, `image`, `created_at`) VALUES
(1, 'Refill Shampoo Herbal 500ml', 'refill-shampoo-herbal-500ml', 1, '25000.00', 50, 0, 1, 'Shampoo herbal alami untuk rambut sehat. Hemat kemasan plastik dengan sistem refill!', 'shampoo.jpg', '2025-10-28 15:18:53'),
(2, 'Refill Sabun Cair Antiseptik 1L', 'refill-sabun-cair-antiseptik-1l', 1, '30000.00', 45, 0, 1, 'Sabun cair antiseptik untuk keluarga. Isi ulang wadah Anda dan kurangi sampah plastik!', 'sabun_cair.jpg', '2025-10-28 15:18:53'),
(3, 'Refill Hand Sanitizer 500ml', 'refill-hand-sanitizer-500ml', 1, '20000.00', 60, 0, 1, 'Hand sanitizer 70% alcohol. Ramah lingkungan dengan sistem refill!', 'hand_sanitizer.jpg', '2025-10-28 15:18:53'),
(4, 'Refill Face Cleanser 250ml', 'refill-face-cleanser-250ml', 1, '35000.00', 30, 0, 1, 'Pembersih wajah lembut untuk semua jenis kulit. Hemat dan eco-friendly!', 'face_cleanser.jpg', '2025-10-28 15:18:53'),
(5, 'Refill Body Lotion 500ml', 'refill-body-lotion-500ml', 1, '40000.00', 35, 0, 1, 'Lotion pelembab kulit dengan vitamin E. Isi ulang dan rawat lingkungan!', 'body_lotion.jpg', '2025-10-28 15:18:53'),
(6, 'Refill Conditioner 500ml', 'refill-conditioner-500ml', 1, '28000.00', 40, 0, 1, 'Conditioner untuk rambut halus dan lembut. Zero waste packaging!', 'conditioner.jpg', '2025-10-28 15:18:53'),
(7, 'Refill Detergen Cair 1L', 'refill-detergen-cair-1l', 2, '35000.00', 80, 0, 1, 'Detergen cair konsentrat untuk pakaian bersih. Kurangi sampah plastik dengan refill!', 'detergen.jpg', '2025-10-28 15:18:53'),
(8, 'Refill Sabun Cuci Piring 1L', 'refill-sabun-cuci-piring-1l', 2, '18000.00', 100, 0, 1, 'Sabun cuci piring anti bakteri. Ekonomis dan ramah lingkungan!', 'sabun_piring.jpg', '2025-10-28 15:18:53'),
(9, 'Refill Pelicin Pakaian 1L', 'refill-pelicin-pakaian-1l', 2, '22000.00', 60, 0, 1, 'Pelembut pakaian dengan wangi fresh. Isi ulang hemat dan hijau!', 'pelicin.jpg', '2025-10-28 15:18:53'),
(10, 'Refill Pewangi Pakaian 500ml', 'refill-pewangi-pakaian-500ml', 2, '15000.00', 70, 0, 1, 'Pewangi pakaian tahan lama. Ramah lingkungan!', 'pewangi.jpg', '2025-10-28 15:18:53'),
(11, 'Refill Pembersih Lantai 1L', 'refill-pembersih-lantai-1l', 2, '25000.00', 55, 0, 1, 'Pembersih lantai serba guna dengan aroma lavender. Eco-friendly refill!', 'pembersih_lantai.jpg', '2025-10-28 15:18:53'),
(12, 'Refill Cairan Pembersih Kaca 500ml', 'refill-cairan-pembersih-kaca-500ml', 2, '18000.00', 45, 0, 1, 'Pembersih kaca dan jendela tanpa baret. Hemat dengan sistem refill!', 'pembersih_kaca.jpg', '2025-10-28 15:18:53'),
(13, 'Refill Minyak Goreng 1L', 'refill-minyak-goreng-1l', 3, '18000.00', 150, 0, 1, 'Minyak goreng berkualitas. Bawa wadah Anda sendiri dan kurangi plastik!', 'minyak.jpg', '2025-10-28 15:18:53'),
(14, 'Refill Beras Premium 5Kg', 'refill-beras-premium-5kg', 3, '75000.00', 100, 0, 1, 'Beras premium pulen. Kurangi kemasan plastik dengan belanja curah!', 'beras.jpg', '2025-10-28 15:18:53'),
(15, 'Refill Tepung Terigu 1Kg', 'refill-tepung-terigu-1kg', 3, '12000.00', 80, 0, 1, 'Tepung terigu serbaguna untuk memasak dan membuat kue. Zero waste!', 'tepung.jpg', '2025-10-28 15:18:53'),
(16, 'Refill Gula Pasir 1Kg', 'refill-gula-pasir-1kg', 3, '15000.00', 120, 0, 1, 'Gula pasir putih berkualitas. Isi ulang toples Anda, hemat kemasan!', 'gula.jpg', '2025-10-28 15:18:53'),
(17, 'Refill Kopi Bubuk 250g', 'refill-kopi-bubuk-250g', 3, '25000.00', 50, 0, 1, 'Kopi bubuk robusta pilihan. Zero waste packaging untuk pecinta kopi!', 'kopi.jpg', '2025-10-28 15:18:53'),
(18, 'Refill Teh Celup 100pcs', 'refill-teh-celup-100pcs', 3, '20000.00', 60, 0, 1, 'Teh celup premium tanpa kemasan berlebih. Ramah lingkungan!', 'teh.jpg', '2025-10-28 15:18:53'),
(19, 'Refill Kacang Tanah 500g', 'refill-kacang-tanah-500g', 3, '18000.00', 40, 0, 1, 'Kacang tanah goreng tanpa kulit. Camilan sehat sistem refill!', 'kacang_tanah.jpg', '2025-10-28 15:18:53'),
(20, 'Refill Pasta Macaroni 500g', 'refill-pasta-macaroni-500g', 3, '22000.00', 35, 0, 1, 'Pasta macaroni import berkualitas tinggi. Belanja curah eco-friendly!', 'pasta.jpg', '2025-10-28 15:18:53'),
(21, 'Refill Galon Air Mineral 19L', 'refill-galon-air-mineral-19l', 4, '12000.00', 200, 0, 1, 'Air mineral berkualitas untuk galon isi ulang. Hemat dan ramah lingkungan!', 'galon.jpg', '2025-10-28 15:18:53'),
(22, 'Refill Air RO 19L', 'refill-air-ro-19l', 4, '15000.00', 150, 0, 1, 'Air Reverse Osmosis super bersih dan sehat. Sistem refill galon!', 'air_ro.jpg', '2025-10-28 15:18:53');

-- --------------------------------------------------------

--
-- Table structure for table `product_reviews`
--

CREATE TABLE `product_reviews` (
  `id` int NOT NULL,
  `product_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `rating` tinyint DEFAULT NULL,
  `comment` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `name` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `phone` varchar(30) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `role` enum('user','admin','kurir') COLLATE utf8mb4_general_ci DEFAULT 'user',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `role`, `created_at`) VALUES
(50, 'Admin One', 'admin1@gorefill.test', '$2y$12$ON4vHn7IbFsyF5lHwFZfK.Q7wLG1kEd8sN1WXyoqGyLM/sIvRouEu', '081100000001', 'admin', '2025-10-23 13:32:19'),
(51, 'Admin Two', 'admin2@gorefill.test', '$2y$12$zW08hDn0UZfDS17Eux3eEeQXOuiiXpO/XgLUt1aa4Q/7VGEFLeywW', '081100000002', 'admin', '2025-10-23 13:32:19'),
(52, 'Kurir Tegal 1', 'kurir1@gorefill.test', '$2y$12$EkNmt9aBzvoY/dfdLafbXuzoYKohgkqgSVqhwPxtKrh8AaP0wiImu', '081200000001', 'kurir', '2025-10-23 13:32:19'),
(53, 'Kurir Tegal 2', 'kurir2@gorefill.test', '$2y$12$X8BtZVuU8F8E7Ym6CBIq/eTgRLnqFO4HxAmNCnV7MwKvtjudCnywa', '081200000002', 'kurir', '2025-10-23 13:32:19'),
(54, 'Kurir Tegal 3', 'kurir3@gorefill.test', '$2y$12$YFaBmlpj7brvK4YhobVhR.tAglFc6XJztCplYRMdv11jyZ/OJL0je', '081200000003', 'kurir', '2025-10-23 13:32:19'),
(55, 'User A', 'usera@gorefill.test', '$2y$12$IU5KAjNK2c9vQlkCkJuqbuXmC9M5rkU7Y.tU2cqzzXpJKrk2WU3E.', '081300000001', 'user', '2025-10-23 13:32:19'),
(56, 'User B', 'userb@gorefill.test', '$2y$12$56yLr5sDU5zRGOPiEhxpPuuU3O6a1ZD6.hkENWATfZQ0IjkpLlISK', '081300000002', 'user', '2025-10-23 13:32:19'),
(57, 'User C', 'userc@gorefill.test', '$2y$12$OzdBiKwzUAMmKrfSDpR.COz1HkcwvROIgQMTvz7bU04NiXVWNaXmK', '081300000003', 'user', '2025-10-23 13:32:19'),
(58, 'User D', 'userd@gorefill.test', '$2y$12$PkdmfmKZEeX4vYSI1XcPk.9uPM8ZtnQMqwrZdfdILsbp0JntIlll6', '081300000004', 'user', '2025-10-23 13:32:19'),
(59, 'User E', 'usere@gorefill.test', '$2y$12$bEsZyAkXZC6e6XV4N6vibeFOkSNsrvulkwf9IZ66cKLzRxnkLa092', '081300000005', 'user', '2025-10-23 13:32:19'),
(60, 'User F', 'userf@gorefill.test', '$2y$12$j/Pnt8.mNbnsJX96f6Naa.g9tiYlt/01QAkajQdwQkkJT.GQNlrui', '081300000006', 'user', '2025-10-23 13:32:19'),
(61, 'User G', 'userg@gorefill.test', '$2y$12$UeVYUjeoFu9VM039S9tbw.rq3bjYwk6gHXmz8IqlKXX54zVAFKbSW', '081300000007', 'user', '2025-10-23 13:32:19'),
(62, 'User H', 'userh@gorefill.test', '$2y$12$6WHomraKG.jFN9W9g8L7Huojeru9uYEbNIl3r7cXYevO4jSao1z7e', '081300000008', 'user', '2025-10-23 13:32:19'),
(63, 'User I', 'useri@gorefill.test', '$2y$12$v2aF38dXSor36lJNBoEu6OnjlHHvvaEmHfYNyQ/2p1l1obbcEdEWa', '081300000009', 'user', '2025-10-23 13:32:19'),
(64, 'User J', 'userj@gorefill.test', '$2y$12$Pyxpa2S8l/jEXLPHUZ4.uu02V8Uo2jHNFp2Z7O/p7nMDYVSiLnQfi', '081300000010', 'user', '2025-10-23 13:32:19');

-- --------------------------------------------------------

--
-- Table structure for table `vouchers`
--

CREATE TABLE `vouchers` (
  `id` int NOT NULL,
  `code` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `discount_percent` int NOT NULL,
  `min_purchase` decimal(12,2) DEFAULT '0.00',
  `usage_limit` int DEFAULT '1',
  `used_count` int DEFAULT '0',
  `expires_at` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ;

--
-- Dumping data for table `vouchers`
--

INSERT INTO `vouchers` (`id`, `code`, `discount_percent`, `min_purchase`, `usage_limit`, `used_count`, `expires_at`, `created_at`) VALUES
(63, 'GOREFILL10', 10, '0.00', 200, 0, '2026-12-31', '2025-10-23 13:32:19'),
(64, 'GOREFILL15', 15, '0.00', 100, 0, '2026-12-31', '2025-10-23 13:32:19'),
(65, 'GOREFILL20', 20, '0.00', 50, 0, '2025-12-31', '2025-10-23 13:32:19'),
(66, 'WELCOME5', 5, '0.00', 1000, 0, '2026-12-31', '2025-10-23 13:32:19'),
(67, 'SEMANGAT25', 25, '0.00', 20, 0, '2026-12-31', '2025-10-23 13:32:19'),
(68, 'TGALDISC10', 10, '0.00', 100, 0, '2026-12-31', '2025-10-23 13:32:19'),
(69, 'MIDTRANS5', 5, '0.00', 500, 0, '2026-12-31', '2025-10-23 13:32:19'),
(70, 'FALLSALE30', 30, '0.00', 10, 0, '2026-03-31', '2025-10-23 13:32:19'),
(71, 'FLASH50', 50, '0.00', 1, 0, '2025-12-31', '2025-10-23 13:32:19'),
(72, 'LOYAL20', 20, '0.00', 500, 0, '2026-12-31', '2025-10-23 13:32:19'),
(73, 'EXPIRED1', 10, '0.00', 10, 0, '2022-01-01', '2025-10-23 13:32:19'),
(74, 'EXPIRED2', 15, '0.00', 10, 0, '2023-06-01', '2025-10-23 13:32:19'),
(75, 'USEDUP1', 20, '0.00', 1, 0, '2026-12-31', '2025-10-23 13:32:19'),
(76, 'PROMO7', 7, '0.00', 300, 0, '2026-12-31', '2025-10-23 13:32:19'),
(77, 'PROMO8', 8, '0.00', 200, 0, '2026-12-31', '2025-10-23 13:32:19'),
(78, 'OLD2023', 10, '0.00', 50, 0, '2023-12-31', '2025-10-23 13:32:19'),
(79, 'TEST50', 50, '0.00', 2, 0, '2026-12-31', '2025-10-23 13:32:19'),
(80, 'SEASON5', 5, '0.00', 100, 0, '2026-12-31', '2025-10-23 13:32:19'),
(81, 'WELCOME100', 100, '0.00', 1, 0, '2026-12-31', '2025-10-23 13:32:19'),
(82, 'LOCAL10', 10, '0.00', 100, 0, '2026-12-31', '2025-10-23 13:32:19'),
(103, 'GOREFILL 100', 100, '50000.00', 100, 0, '2025-11-08', '2025-10-29 02:51:07');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `courier_locations`
--
ALTER TABLE `courier_locations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `courier_id` (`courier_id`);

--
-- Indexes for table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_number` (`order_number`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `voucher_id` (`voucher_id`),
  ADD KEY `idx_transaction_id` (`transaction_id`),
  ADD KEY `idx_payment_status` (`payment_status`),
  ADD KEY `courier_id` (`courier_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_order_items` (`order_id`),
  ADD KEY `idx_product_orders` (`product_id`);

--
-- Indexes for table `payment_logs`
--
ALTER TABLE `payment_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `fk_category` (`category_id`);

--
-- Indexes for table `product_reviews`
--
ALTER TABLE `product_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `vouchers`
--
ALTER TABLE `vouchers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `addresses`
--
ALTER TABLE `addresses`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `courier_locations`
--
ALTER TABLE `courier_locations`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=151;

--
-- AUTO_INCREMENT for table `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=318;

--
-- AUTO_INCREMENT for table `payment_logs`
--
ALTER TABLE `payment_logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `product_reviews`
--
ALTER TABLE `product_reviews`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `vouchers`
--
ALTER TABLE `vouchers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `addresses`
--
ALTER TABLE `addresses`
  ADD CONSTRAINT `addresses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `courier_locations`
--
ALTER TABLE `courier_locations`
  ADD CONSTRAINT `courier_locations_ibfk_1` FOREIGN KEY (`courier_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `favorites_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`voucher_id`) REFERENCES `vouchers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `orders_ibfk_3` FOREIGN KEY (`courier_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payment_logs`
--
ALTER TABLE `payment_logs`
  ADD CONSTRAINT `payment_logs_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_reviews`
--
ALTER TABLE `product_reviews`
  ADD CONSTRAINT `product_reviews_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
