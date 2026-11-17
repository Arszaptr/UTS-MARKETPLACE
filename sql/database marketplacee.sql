-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql301.infinityfree.com
-- Generation Time: Nov 17, 2026 at 08:45 AM
-- Server version: 11.4.13-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_43411149_marketplaceee`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(80) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Makanan'),
(2, 'Kerajinan'),
(3, 'Pakaian'),
(4, 'obat-obatan'),
(5, 'perlengkapan rumah');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `buyer_id` int(11) DEFAULT NULL,
  `customer_name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `total` decimal(12,2) NOT NULL,
  `status` enum('pending','paid','shipped','done') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `buyer_id`, `customer_name`, `phone`, `address`, `total`, `status`, `created_at`) VALUES
(1, 2, 'Arszaizah', '085809909493', 'Rawa Makmur slebew', '250000.00', 'shipped', '2026-11-11 07:50:57'),
(2, 2, 'Arszaizah', '085809909493', 'rawaaaa', '15000.00', 'done', '2026-11-12 12:31:15'),
(3, 2, 'Arszaizah', '085809909493', 'rawa makmur', '1000000.00', 'pending', '2026-11-12 12:49:59'),
(4, 2, 'Arszaizah', '085809909493', 'rawa makmur', '90000.00', 'pending', '2026-11-12 13:55:05'),
(5, 2, 'arsaqoput', '082937293892', 'ramurbew', '66000.00', 'pending', '2026-11-12 16:14:12'),
(6, 2, 'Arszaizah', '085809909493', 'Rawa Makmur', '50000.00', 'pending', '2026-11-13 13:51:47');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `price` decimal(12,2) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `qty`, `price`) VALUES
(1, 1, 3, 1, '250000.00'),
(2, 2, 1, 1, '15000.00'),
(3, 3, 5, 1, '1000000.00'),
(4, 4, 4, 10, '9000.00'),
(5, 5, 13, 1, '50000.00'),
(6, 5, 9, 1, '3500.00'),
(7, 5, 4, 1, '9000.00'),
(8, 5, 7, 1, '3500.00'),
(9, 6, 13, 1, '50000.00');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(12,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `seller_id`, `name`, `description`, `price`, `stock`, `image_url`, `created_at`) VALUES
(6, 1, 6, 'mie goreng', '', '3500.00', 25, '/uploads/72f5b278b28e83d5.jpg', '2026-11-12 16:03:39'),
(7, 1, 6, 'mie rendang', '', '3500.00', 24, '/uploads/eb6d445adc47349b.jpg', '2026-11-12 16:04:03'),
(8, 1, 6, 'mie seblak', '', '3500.00', 25, '/uploads/9fb9bf9eabc05c7a.jpg', '2026-11-12 16:04:25'),
(9, 1, 6, 'mie gaga kuah', '', '3500.00', 24, '/uploads/346a0f69bcb1ed27.jpg', '2026-11-12 16:04:44'),
(4, 1, 6, 'kacang garuda', 'rasa original', '9000.00', 39, '/uploads/0f41c48d47d85c4f.jpg', '2026-11-12 12:47:55'),
(5, 3, 6, 'hoodie H&M', '', '1000000.00', 0, '/uploads/ad24c7e384144e00.jpg', '2026-11-12 12:48:49'),
(10, 1, 6, 'mie gaga goreng', '', '3500.00', 25, '/uploads/e96ed4e62e0ce25a.jpg', '2026-11-12 16:05:09'),
(11, 5, 6, 'sapu', '', '35000.00', 10, '/uploads/dba712b09d0a775d.jpg', '2026-11-12 16:11:45'),
(12, 4, 6, 'obat nyeri haid', '', '8000.00', 23, '/uploads/1c2db0bd87602645.jpg', '2026-11-12 16:12:20'),
(13, 2, 6, 'celengan ayam', '', '50000.00', 3, '/uploads/0482073a98f4a4fb.jpg', '2026-11-12 16:12:43');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','seller','buyer') DEFAULT 'buyer'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`) VALUES
(2, 'Arszaizah', 'arszaptr@gmail.com', '$2y$12$yUCz80o09cqSz0U3pT3BmO8sIaJQYdkkoSm.6hRKWKQoKryxn0FX2', 'buyer'),
(6, 'jajak', 'jajakkk09@gmail.com', '$2y$12$p4hnH8zbaW9ekU0G7eUodOJZUR3AiYDTyz0nTJRMH.bEjlq2IrgfO', 'seller'),
(5, 'admin', 'admin@demo.com', '$2y$12$gsMYnafip0ytl4cydTin9OXFNU0HcgxhvPIoP0BeE4PQ.85iRkmFu', 'admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `buyer_id` (`buyer_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `seller_id` (`seller_id`);

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
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
