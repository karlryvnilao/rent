-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 13, 2024 at 04:34 PM
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
-- Database: `rent_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` date NOT NULL,
  `due_date` date NOT NULL,
  `status` enum('paid','pending') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `tenant_id`, `owner_id`, `amount`, `payment_date`, `due_date`, `status`) VALUES
(1, 2, 1, 2100.00, '2024-09-04', '2000-04-21', 'paid'),
(2, 2, 1, 2500.00, '2024-09-04', '2024-09-21', 'paid');

-- --------------------------------------------------------

--
-- Table structure for table `properties`
--

CREATE TABLE `properties` (
  `id` int(11) NOT NULL,
  `owner_id` int(11) DEFAULT NULL,
  `type` enum('House','Room') NOT NULL,
  `description` text NOT NULL,
  `location` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `available` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `properties`
--

INSERT INTO `properties` (`id`, `owner_id`, `type`, `description`, `location`, `price`, `file_path`, `available`, `created_at`, `updated_at`) VALUES
(1, 1, 'House', 'dasdaadds', 'dsada', 34242.00, 'img/student-image.jpg', 1, '2024-08-30 10:53:30', '2024-09-02 14:36:12'),
(2, 1, 'Room', 'dsada', 'fdfsfs', 32131.00, 'img/internet-1606098_1280.png', 2, '2024-08-31 06:20:26', '2024-09-02 14:36:12'),
(3, 1, 'House', 'dsa', 'dsfs', 231.00, 'img/mouse-and-keyboard-8938335_1280.png', 2, '2024-08-31 06:20:46', '2024-09-02 14:36:12'),
(12, 1, 'House', 'dsa', 'dsfs', 231.00, 'img/mouse-and-keyboard-8938335_1280.png', 1, '2024-08-31 06:38:52', '2024-09-02 14:36:12'),
(13, 1, 'Room', 'haha', 'haha', 21000.00, 'img/Untitled.png', 1, '2024-09-04 06:08:59', '2024-09-04 14:08:59'),
(14, 1, 'Room', 'haha', 'haha', 21000.00, 'img/Untitled.png', 1, '2024-09-04 06:13:21', '2024-09-04 14:13:21'),
(15, 3, 'House', 'test', 'test', 20.00, 'img/wallpaperflare.com_wallpaper.jpg', 2, '2024-09-12 12:31:29', '2024-09-12 22:07:54');

-- --------------------------------------------------------

--
-- Table structure for table `rentals`
--

CREATE TABLE `rentals` (
  `id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `rent_start_date` date NOT NULL,
  `rent_end_date` date DEFAULT NULL,
  `amount_paid` decimal(10,2) DEFAULT 0.00,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rentals`
--

INSERT INTO `rentals` (`id`, `tenant_id`, `property_id`, `rent_start_date`, `rent_end_date`, `amount_paid`, `status`, `created_at`, `updated_at`) VALUES
(1, 2, 1, '2024-09-02', NULL, 0.00, 'approved', '2024-09-02 15:18:41', '2024-09-02 22:24:00'),
(2, 5, 1, '2024-09-04', NULL, 0.00, 'pending', '2024-09-04 14:35:13', '2024-09-04 14:35:13'),
(8, 5, 15, '2024-09-12', NULL, 0.00, 'rejected', '2024-09-12 22:03:34', '2024-09-12 22:07:53'),
(9, 5, 13, '2024-09-12', NULL, 0.00, 'pending', '2024-09-12 22:06:55', '2024-09-12 22:06:55'),
(10, 5, 15, '2024-09-12', NULL, 0.00, 'approved', '2024-09-12 22:07:24', '2024-09-12 22:07:54');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('admin','owner','tenant') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `role`, `created_at`) VALUES
(1, 'karl', '$2y$10$8EYsZIjSAuzkKklknEJqbu12.XyFaUiO7pe//kEk6Fqa/DXZh9fEy', 'karl@karl.com', 'owner', '2024-08-30 09:04:34'),
(2, 'reyven', '$2y$10$7SekUNbcRs74Br4o6B2a3u4J1RDVYqc47sC0hFE4jNsoaHzjvE92e', 'reyven@h.c', 'tenant', '2024-08-30 09:05:10'),
(3, 'owner', '$2y$10$aqC.TG8y7J/BB3Yb4IoieujKQWYxyPg71iGcEZaTkKGAYoe9biePi', 'karl@0909.com', 'owner', '2024-08-30 09:46:24'),
(4, 'qwer', '$2y$10$dDUMxbWpdMGu21zftfvcsu7Fm/YIofVr0pANNKuPjJPoh.OhaLOxm', 'asda@dsa.sa', 'owner', '2024-09-01 13:43:13'),
(5, 'tenant', '$2y$10$1jfGv8lEy0ZosJgw6qWGLufAqlEVC21DyCwpM0536.4I6VSgvyaZK', 'sda@sda.sda', 'tenant', '2024-09-01 13:51:57');

-- --------------------------------------------------------

--
-- Table structure for table `user_details`
--

CREATE TABLE `user_details` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `birthday` date NOT NULL,
  `address` varchar(255) NOT NULL,
  `contactnumber` varchar(20) NOT NULL,
  `profile_pic` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_details`
--

INSERT INTO `user_details` (`id`, `user_id`, `name`, `birthday`, `address`, `contactnumber`, `profile_pic`) VALUES
(1, 1, 'karl', '2001-02-02', '21', '2319301', ''),
(2, 2, 'reyven', '1990-12-02', '35', '47565', ''),
(3, 3, 'owner', '2000-02-02', 'sakdlak la', '09102319', ''),
(4, 4, 'hey', '2000-12-02', 'asd', '2131', ''),
(5, 5, 'tenant', '2000-03-02', 'asdas', 'sdada', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tenant_id` (`tenant_id`),
  ADD KEY `owner_id` (`owner_id`);

--
-- Indexes for table `properties`
--
ALTER TABLE `properties`
  ADD PRIMARY KEY (`id`),
  ADD KEY `owner_id` (`owner_id`);

--
-- Indexes for table `rentals`
--
ALTER TABLE `rentals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tenant_id` (`tenant_id`),
  ADD KEY `property_id` (`property_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_details`
--
ALTER TABLE `user_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `properties`
--
ALTER TABLE `properties`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `rentals`
--
ALTER TABLE `rentals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `user_details`
--
ALTER TABLE `user_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`tenant_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `properties`
--
ALTER TABLE `properties`
  ADD CONSTRAINT `properties_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `rentals`
--
ALTER TABLE `rentals`
  ADD CONSTRAINT `rentals_ibfk_1` FOREIGN KEY (`tenant_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `rentals_ibfk_2` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`);

--
-- Constraints for table `user_details`
--
ALTER TABLE `user_details`
  ADD CONSTRAINT `user_details_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
