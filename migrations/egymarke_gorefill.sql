-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 24, 2025 at 09:06 PM
-- Server version: 10.6.23-MariaDB
-- PHP Version: 8.4.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `egymarke_gorefill`
--

-- --------------------------------------------------------

--
-- Table structure for table `addresses`
--

CREATE TABLE `addresses` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `label` varchar(100) DEFAULT NULL,
  `place_name` varchar(255) DEFAULT NULL,
  `street` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `lat` decimal(10,7) DEFAULT NULL,
  `lng` decimal(10,7) DEFAULT NULL,
  `is_default` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `addresses`
--

INSERT INTO `addresses` (`id`, `user_id`, `label`, `place_name`, `street`, `city`, `postal_code`, `lat`, `lng`, `is_default`, `created_at`) VALUES
(13, 55, 'Maxime dolor dicta e', 'Celeste Travis', 'Qui in aut similique', 'Voluptatem saepe qu', '21091', NULL, NULL, 0, '2025-10-23 19:45:45');

-- --------------------------------------------------------

--
-- Table structure for table `courier_locations`
--

CREATE TABLE `courier_locations` (
  `id` int(11) NOT NULL,
  `courier_id` int(11) NOT NULL,
  `lat` decimal(10,7) DEFAULT NULL,
  `lng` decimal(10,7) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

CREATE TABLE `favorites` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `voucher_id` int(11) DEFAULT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `total` decimal(12,2) NOT NULL,
  `status` enum('pending','confirmed','packing','shipped','delivered','cancelled','expired') DEFAULT 'pending',
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_type` varchar(50) DEFAULT NULL,
  `payment_status` enum('pending','unpaid','paid','failed','expired','cancelled','refund') DEFAULT 'pending',
  `midtrans_status` varchar(50) DEFAULT NULL,
  `fraud_status` varchar(50) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `callback_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`callback_data`)),
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `order_number` varchar(50) DEFAULT NULL COMMENT 'Format: ORD-YYYYMMDD-XXXX',
  `address_id` int(10) UNSIGNED DEFAULT NULL,
  `subtotal` decimal(12,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `snap_token` varchar(255) DEFAULT NULL COMMENT 'Midtrans Snap Token',
  `paid_at` timestamp NULL DEFAULT NULL,
  `shipping_name` varchar(255) DEFAULT NULL,
  `shipping_phone` varchar(20) DEFAULT NULL,
  `shipping_address` text DEFAULT NULL,
  `shipping_city` varchar(100) DEFAULT NULL,
  `shipping_postal_code` varchar(10) DEFAULT NULL,
  `courier` varchar(50) DEFAULT NULL COMMENT 'JNE, TIKI, POS, etc',
  `tracking_number` varchar(100) DEFAULT NULL,
  `transaction_status` varchar(50) DEFAULT NULL COMMENT 'capture, settlement, pending, deny, cancel, expire',
  `transaction_time` timestamp NULL DEFAULT NULL,
  `settlement_time` timestamp NULL DEFAULT NULL,
  `gross_amount` decimal(12,2) DEFAULT NULL,
  `currency` varchar(3) DEFAULT 'IDR',
  `signature_key` varchar(255) DEFAULT NULL,
  `bank` varchar(50) DEFAULT NULL,
  `va_number` varchar(50) DEFAULT NULL,
  `bill_key` varchar(50) DEFAULT NULL,
  `biller_code` varchar(50) DEFAULT NULL,
  `pdf_url` varchar(500) DEFAULT NULL,
  `finish_redirect_url` varchar(500) DEFAULT NULL,
  `expiry_time` timestamp NULL DEFAULT NULL,
  `store` varchar(50) DEFAULT NULL,
  `payment_code` varchar(50) DEFAULT NULL,
  `customer_email` varchar(255) DEFAULT NULL,
  `customer_phone` varchar(20) DEFAULT NULL,
  `refund_amount` decimal(12,2) DEFAULT 0.00,
  `refund_reason` text DEFAULT NULL,
  `refunded_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `voucher_id`, `transaction_id`, `total`, `status`, `payment_method`, `payment_type`, `payment_status`, `midtrans_status`, `fraud_status`, `note`, `callback_data`, `created_at`, `order_number`, `address_id`, `subtotal`, `discount_amount`, `snap_token`, `paid_at`, `shipping_name`, `shipping_phone`, `shipping_address`, `shipping_city`, `shipping_postal_code`, `courier`, `tracking_number`, `transaction_status`, `transaction_time`, `settlement_time`, `gross_amount`, `currency`, `signature_key`, `bank`, `va_number`, `bill_key`, `biller_code`, `pdf_url`, `finish_redirect_url`, `expiry_time`, `store`, `payment_code`, `customer_email`, `customer_phone`, `refund_amount`, `refund_reason`, `refunded_at`, `updated_at`) VALUES
(5, 55, NULL, '38f5e234-4e19-4ee9-9af6-49a615f275e0', 12000.00, 'confirmed', 'qris', NULL, 'paid', NULL, NULL, NULL, NULL, '2025-10-24 14:03:11', 'ORD-20251024-0003', 13, 12000.00, 0.00, '561e5369-3ad9-40da-937d-72368ae325bd', '2025-10-24 14:03:36', 'Maxime dolor dicta e', '-', 'Celeste Travis, Qui in aut similique', 'Voluptatem saepe qu', '21091', NULL, NULL, NULL, NULL, NULL, NULL, 'IDR', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, '2025-10-24 14:03:36');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_image` varchar(500) NOT NULL,
  `product_price` decimal(12,2) NOT NULL COMMENT 'Harga asli produk saat dibeli',
  `quantity` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `price` decimal(12,2) DEFAULT NULL,
  `subtotal` decimal(12,2) NOT NULL COMMENT 'qty * price',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `product_image`, `product_price`, `quantity`, `price`, `subtotal`, `created_at`) VALUES
(228, 5, 176, 'Galon Aqua Isi Ulang 19L', 'https://source.unsplash.com/300x300/?water,refill', 12000.00, 1, 12000.00, 12000.00, '2025-10-24 14:03:11');

-- --------------------------------------------------------

--
-- Table structure for table `payment_logs`
--

CREATE TABLE `payment_logs` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `midtrans_order_id` varchar(100) DEFAULT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload`)),
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `price` decimal(12,2) NOT NULL,
  `stock` int(11) DEFAULT 0,
  `rating` float DEFAULT 0,
  `badge_env` tinyint(1) DEFAULT 0,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `slug`, `category`, `price`, `stock`, `rating`, `badge_env`, `description`, `image`, `created_at`) VALUES
(176, 'Galon Aqua Isi Ulang 19L', 'galon-aqua-19l', 'Air Minum', 12000.00, 150, 0, 1, 'Galon air mineral isi ulang 19 liter.', 'https://source.unsplash.com/300x300/?water,refill', '2025-10-23 13:32:19'),
(177, 'Galon Le Minerale 19L', 'galon-leminerale-19l', 'Air Minum', 11000.00, 120, 0, 1, 'Galon isi ulang merek Le Minerale.', 'https://source.unsplash.com/300x300/?water,refill', '2025-10-23 13:32:19'),
(178, 'Refill Galon - Premium 19L', 'refill-galon-premium-19l', 'Air Minum', 15000.00, 80, 0, 1, 'Air mineral premium isi ulang 19L.', 'galon_premium.jpg', '2025-10-23 13:32:19'),
(179, 'Air Mineral 1.5L Pack', 'air-1-5l-pack', 'Air Minum', 5000.00, 200, 0, 0, 'Air mineral kemasan 1.5L (paket).', 'air_1_5l.jpg', '2025-10-23 13:32:19'),
(180, 'Air Mineral 600ml', 'air-600ml', 'Air Minum', 3000.00, 300, 0, 0, 'Air mineral 600ml.', 'air_600ml.jpg', '2025-10-23 13:32:19'),
(181, 'Refill Galon ECO 19L', 'refill-galon-eco-19l', 'Air Minum', 10000.00, 100, 0, 1, 'Galon isi ulang ramah lingkungan.', 'galon_eco.jpg', '2025-10-23 13:32:19'),
(182, 'Gas LPG 3Kg Refill', 'lpg-3kg-refill', 'Gas', 22000.00, 60, 0, 0, 'Isi ulang LPG 3Kg.', 'https://source.unsplash.com/300x300/?lpg,gas', '2025-10-23 13:32:19'),
(183, 'Gas LPG 12Kg Refill', 'lpg-12kg-refill', 'Gas', 80000.00, 30, 0, 0, 'Isi ulang LPG 12Kg.', 'lpg12kg.jpg', '2025-10-23 13:32:19'),
(184, 'Tabung Gas 3Kg Baru', 'tabung-gas-3kg-baru', 'Gas', 150000.00, 20, 0, 0, 'Tabung gas 3Kg (baru).', 'https://source.unsplash.com/300x300/?gas,tank', '2025-10-23 13:32:19'),
(185, 'Kompor Portable', 'kompor-portable', 'Peralatan', 120000.00, 40, 0, 0, 'Kompor portable untuk rumah.', 'https://images.unsplash.com/photo-1579427422275-a2a4d10b4c54?w=300', '2025-10-23 13:32:19'),
(186, 'Panci Stainless 24cm', 'panci-ss-24cm', 'Peralatan', 85000.00, 50, 0, 0, 'Panci stainless 24 cm.', 'https://source.unsplash.com/300x300/?kitchen,utensil', '2025-10-23 13:32:19'),
(187, 'Teko Listrik 1.7L', 'teko-listrik-1-7l', 'Peralatan', 230000.00, 25, 0, 0, 'Teko listrik untuk rumah.', 'teko_listrik.jpg', '2025-10-23 13:32:19'),
(188, 'Tinta Printer Hitam', 'tinta-printer-black', 'Tinta', 45000.00, 90, 0, 0, 'Tinta printer (black).', 'https://source.unsplash.com/300x300/?printer,ink', '2025-10-23 13:32:19'),
(189, 'Tinta Printer Color', 'tinta-printer-color', 'Tinta', 65000.00, 70, 0, 0, 'Tinta printer warna (set).', 'tinta_color.jpg', '2025-10-23 13:32:19'),
(190, 'Cartridge Refill Ink', 'cartridge-refill', 'Tinta', 30000.00, 120, 0, 0, 'Cartridge isi ulang universal.', 'cartridge.jpg', '2025-10-23 13:32:19'),
(191, 'Dispenser Galon Manual', 'dispenser-manual', 'Peralatan', 25000.00, 80, 0, 0, 'Dispenser galon model manual.', 'dispenser_manual.jpg', '2025-10-23 13:32:19'),
(192, 'Dispenser Galon Elektrik', 'dispenser-elektrik', 'Peralatan', 55000.00, 40, 0, 0, 'Dispenser galon elektrik lebih mudah.', 'https://source.unsplash.com/300x300/?dispenser,water', '2025-10-23 13:32:19'),
(193, 'Selang Gas 3m', 'selang-gas-3m', 'Aksesoris', 15000.00, 200, 0, 0, 'Selang gas 3 meter.', 'selang_gas.jpg', '2025-10-23 13:32:19'),
(194, 'Regulator Gas', 'regulator-gas', 'Aksesoris', 35000.00, 150, 0, 0, 'Regulator gas standar.', 'https://source.unsplash.com/300x300/?gas,regulator', '2025-10-23 13:32:19'),
(195, 'Penjepit Galon', 'penjepit-galon', 'Aksesoris', 8000.00, 300, 0, 0, 'Penjepit galon plastik.', 'https://source.unsplash.com/300x300/?accessory,home', '2025-10-23 13:32:19'),
(196, 'Galon Aqua Promo 19L', 'galon-aqua-promo-19l', 'Air Minum', 11500.00, 140, 0, 1, 'Galon promo Aqua 19L.', 'galon_aqua_promo.jpg', '2025-10-23 13:32:19'),
(197, 'Air Mineral 330ml', 'air-330ml', 'Air Minum', 2500.00, 400, 0, 0, 'Air mineral 330ml.', 'air_330ml.jpg', '2025-10-23 13:32:19'),
(198, 'Gallon Refill Economy', 'galon-refill-economy', 'Air Minum', 9000.00, 90, 0, 1, 'Galon refill low-cost.', 'galon_economy.jpg', '2025-10-23 13:32:19'),
(199, 'Botol Mineral 600ml Pack', 'botol-mineral-pack', 'Air Minum', 14000.00, 180, 0, 0, 'Pack botol 600ml x6.', 'botol_pack.jpg', '2025-10-23 13:32:19'),
(200, 'Refill Tinta HP', 'refill-tinta-hp', 'Tinta', 38000.00, 60, 0, 0, 'Tinta refill untuk printer HP.', 'tinta_hp.jpg', '2025-10-23 13:32:19'),
(201, 'Refill Tinta Epson', 'refill-tinta-epson', 'Tinta', 42000.00, 70, 0, 0, 'Tinta refill untuk printer Epson.', 'tinta_epson.jpg', '2025-10-23 13:32:19'),
(202, 'Refill Tinta Canon', 'refill-tinta-canon', 'Tinta', 40000.00, 65, 0, 0, 'Tinta refill Canon.', 'tinta_canon.jpg', '2025-10-23 13:32:19'),
(203, 'Set Panci 3pcs', 'set-panci-3pcs', 'Peralatan', 280000.00, 15, 0, 0, 'Set panci 3 pcs.', 'set_panci.jpg', '2025-10-23 13:32:19'),
(204, 'Saringan Air', 'saringan-air', 'Peralatan', 45000.00, 50, 0, 0, 'Saringan air rumah tangga.', 'saringan_air.jpg', '2025-10-23 13:32:19'),
(205, 'Galon Mineral Premium 19L', 'galon-mineral-premium', 'Air Minum', 16000.00, 60, 0, 1, 'Galon mineral premium.', 'galon_premium2.jpg', '2025-10-23 13:32:19'),
(206, 'Pompa Galon Listrik', 'pompa-galon-listrik', 'Peralatan', 70000.00, 30, 0, 0, 'Pompa galon model listrik.', 'pompa_galon.jpg', '2025-10-23 13:32:19'),
(207, 'Keranjang Tinta 4 Warna', 'keranjang-tinta-4color', 'Tinta', 120000.00, 40, 0, 0, 'Bundle tinta 4 warna.', 'tinta_bundle.jpg', '2025-10-23 13:32:19'),
(208, 'Korek Api Gas', 'korek-api-gas', 'Aksesoris', 5000.00, 500, 0, 0, 'Korek api gas kecil.', 'korek_gas.jpg', '2025-10-23 13:32:19'),
(209, 'Kartu Voucher', 'kartu-voucher', 'Aksesoris', 2000.00, 1000, 0, 0, 'Kartu voucher cetak.', 'kartu_voucher.jpg', '2025-10-23 13:32:19'),
(210, 'Filter Karbon', 'filter-karbon', 'Peralatan', 65000.00, 20, 0, 0, 'Filter karbon untuk air.', 'filter_karbon.jpg', '2025-10-23 13:32:19'),
(211, 'Refill Galon 10L', 'refill-galon-10l', 'Air Minum', 8000.00, 60, 0, 1, 'Galon ukuran 10L.', 'galon_10l.jpg', '2025-10-23 13:32:19'),
(212, 'Galon Mini 5L', 'galon-mini-5l', 'Air Minum', 6000.00, 90, 0, 0, 'Galon mini 5L.', 'galon_5l.jpg', '2025-10-23 13:32:19'),
(213, 'Gas Tabung 5Kg Refill', 'lpg-5kg-refill', 'Gas', 42000.00, 35, 0, 0, 'Isi ulang LPG 5Kg.', 'lpg5kg.jpg', '2025-10-23 13:32:19'),
(214, 'Kompor Gas 2Tungku', 'kompor-gas-2tungku', 'Peralatan', 300000.00, 18, 0, 0, 'Kompor 2 tungku berkualitas.', 'kompor_2t.jpg', '2025-10-23 13:32:19'),
(215, 'Panci Teflon 28cm', 'panci-teflon-28', 'Peralatan', 95000.00, 27, 0, 0, 'Panci teflon 28cm.', 'panci_teflon.jpg', '2025-10-23 13:32:19'),
(216, 'Tinta Photo Quality', 'tinta-photo', 'Tinta', 75000.00, 22, 0, 0, 'Tinta untuk cetak foto berkualitas.', 'tinta_photo.jpg', '2025-10-23 13:32:19'),
(217, 'Adaptor Gas Rumah', 'adapter-gas', 'Aksesoris', 40000.00, 60, 0, 0, 'Adaptor gas multi-brand.', 'adapter_gas.jpg', '2025-10-23 13:32:19'),
(218, 'Nozzle Galon', 'nozzle-galon', 'Aksesoris', 12000.00, 180, 0, 0, 'Nozzle pengatur aliran galon.', 'nozzle.jpg', '2025-10-23 13:32:19'),
(219, 'Gasket Tabung', 'gasket-tabung', 'Aksesoris', 9000.00, 200, 0, 0, 'Gasket pengaman tabung gas.', 'gasket.jpg', '2025-10-23 13:32:19'),
(220, 'Kemasan Galon Sealed', 'kemasan-galon-sealed', 'Air Minum', 17000.00, 40, 0, 1, 'Galon sealed premium.', 'galon_sealed.jpg', '2025-10-23 13:32:19'),
(221, 'Refill Cylinder 6L', 'refill-cylinder-6l', 'Gas', 50000.00, 25, 0, 0, 'Cylinder refill 6L.', 'cylinder6l.jpg', '2025-10-23 13:32:19'),
(222, 'Set Teko + Cangkir', 'set-teko-cangkir', 'Peralatan', 180000.00, 12, 0, 0, 'Set teko dan cangkir elegan.', 'set_teko.jpg', '2025-10-23 13:32:19'),
(223, 'Spare Part Mesin Refill', 'spare-mesin-refill', 'Peralatan', 135000.00, 8, 0, 0, 'Spare part untuk mesin refill.', 'spare_mesin.jpg', '2025-10-23 13:32:19'),
(224, 'Galon Return Program', 'galon-return', 'Air Minum', 9000.00, 70, 0, 1, 'Program tukar galon lama.', 'galon_return.jpg', '2025-10-23 13:32:19'),
(225, 'Tutup Galon Kuat', 'tutup-galon-kuat', 'Aksesoris', 10000.00, 250, 0, 0, 'Tutup galon anti bocor.', 'tutup_galon.jpg', '2025-10-23 13:32:19'),
(226, 'Holder Dispenser', 'holder-dispenser', 'Peralatan', 18000.00, 110, 0, 0, 'Holder dispenser kompak.', 'holder_dispenser.jpg', '2025-10-23 13:32:19'),
(227, 'Pipa PVC 1m', 'pipa-pvc-1m', 'Peralatan', 12000.00, 120, 0, 0, 'Pipa PVC 1 meter.', 'pipa_pvc.jpg', '2025-10-23 13:32:19'),
(228, 'Refill Kit Printer', 'refill-kit-printer', 'Tinta', 95000.00, 30, 0, 0, 'Kit refill printer lengkap.', 'refill_kit.jpg', '2025-10-23 13:32:19'),
(229, 'Botol Galon Sekali Pakai', 'botol-galon-sp', 'Air Minum', 4000.00, 600, 0, 0, 'Botol galon sekali pakai (per item).', 'botol_sp.jpg', '2025-10-23 13:32:19'),
(230, 'Tinta Eco 3Color', 'tinta-eco-3color', 'Tinta', 54000.00, 55, 0, 0, 'Tinta eco 3 warna.', 'tinta_eco.jpg', '2025-10-23 13:32:19'),
(231, 'Panci Kukus', 'panci-kukus', 'Peralatan', 76000.00, 33, 0, 0, 'Panci kukus stainless.', 'panci_kukus.jpg', '2025-10-23 13:32:19'),
(232, 'Gas Regulator Set', 'gas-regulator-set', 'Aksesoris', 60000.00, 45, 0, 0, 'Regulator dan selang set.', 'gas_regulator_set.jpg', '2025-10-23 13:32:19');

-- --------------------------------------------------------

--
-- Table structure for table `product_reviews`
--

CREATE TABLE `product_reviews` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `rating` tinyint(4) DEFAULT NULL,
  `comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `role` enum('user','admin','kurir') DEFAULT 'user',
  `created_at` timestamp NULL DEFAULT current_timestamp()
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
(55, 'User A', 'usera@gorefill.test', '$2y$10$Va00G5PSNJ3WOke00usgIenDVJAxgzFMVNxff2mSVuIscTusPk.5q', '081300000001', 'user', '2025-10-23 13:32:19'),
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
  `id` int(11) NOT NULL,
  `code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `discount_percent` int(11) NOT NULL,
  `min_purchase` decimal(12,2) DEFAULT 0.00,
  `usage_limit` int(11) DEFAULT 1,
  `used_count` int(11) DEFAULT 0,
  `expires_at` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vouchers`
--

INSERT INTO `vouchers` (`id`, `code`, `discount_percent`, `min_purchase`, `usage_limit`, `used_count`, `expires_at`, `created_at`) VALUES
(63, 'GOREFILL10', 10, 0.00, 200, 7, '2026-12-31', '2025-10-23 13:32:19'),
(64, 'GOREFILL15', 15, 0.00, 100, 20, '2026-12-31', '2025-10-23 13:32:19'),
(65, 'GOREFILL20', 20, 0.00, 50, 2, '2025-12-31', '2025-10-23 13:32:19'),
(66, 'WELCOME5', 5, 0.00, 1000, 50, '2026-12-31', '2025-10-23 13:32:19'),
(67, 'SEMANGAT25', 25, 0.00, 20, 1, '2026-12-31', '2025-10-23 13:32:19'),
(68, 'TGALDISC10', 10, 0.00, 100, 0, '2026-12-31', '2025-10-23 13:32:19'),
(69, 'MIDTRANS5', 5, 0.00, 500, 2, '2026-12-31', '2025-10-23 13:32:19'),
(70, 'FALLSALE30', 30, 0.00, 10, 0, '2026-03-31', '2025-10-23 13:32:19'),
(71, 'FLASH50', 50, 0.00, 1, 0, '2025-12-31', '2025-10-23 13:32:19'),
(72, 'LOYAL20', 20, 0.00, 500, 10, '2026-12-31', '2025-10-23 13:32:19'),
(73, 'EXPIRED1', 10, 0.00, 10, 10, '2022-01-01', '2025-10-23 13:32:19'),
(74, 'EXPIRED2', 15, 0.00, 10, 0, '2023-06-01', '2025-10-23 13:32:19'),
(75, 'USEDUP1', 20, 0.00, 1, 1, '2026-12-31', '2025-10-23 13:32:19'),
(76, 'PROMO7', 7, 0.00, 300, 5, '2026-12-31', '2025-10-23 13:32:19'),
(77, 'PROMO8', 8, 0.00, 200, 0, '2026-12-31', '2025-10-23 13:32:19'),
(78, 'OLD2023', 10, 0.00, 50, 50, '2023-12-31', '2025-10-23 13:32:19'),
(79, 'TEST50', 50, 0.00, 2, 1, '2026-12-31', '2025-10-23 13:32:19'),
(80, 'SEASON5', 5, 0.00, 100, 0, '2026-12-31', '2025-10-23 13:32:19'),
(81, 'WELCOME100', 100, 0.00, 1, 0, '2026-12-31', '2025-10-23 13:32:19'),
(82, 'LOCAL10', 10, 0.00, 100, 0, '2026-12-31', '2025-10-23 13:32:19');

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
  ADD KEY `idx_payment_status` (`payment_status`);

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
  ADD UNIQUE KEY `slug` (`slug`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `courier_locations`
--
ALTER TABLE `courier_locations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT for table `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=229;

--
-- AUTO_INCREMENT for table `payment_logs`
--
ALTER TABLE `payment_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=234;

--
-- AUTO_INCREMENT for table `product_reviews`
--
ALTER TABLE `product_reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `vouchers`
--
ALTER TABLE `vouchers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

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
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`voucher_id`) REFERENCES `vouchers` (`id`) ON DELETE SET NULL;

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
