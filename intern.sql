-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 03, 2026 at 10:52 PM
-- Server version: 11.7.2-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `intern`
--

-- --------------------------------------------------------

--
-- Table structure for table `account`
--

CREATE TABLE `account` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `account`
--

INSERT INTO `account` (`id`, `username`, `password`) VALUES
(1, 'admin', '$2y$10$JKuSJ7zscjRrPQtV30ayPOPahTaXz979XEZWWjXhVF86qFGioKQxu');

-- --------------------------------------------------------

--
-- Table structure for table `ensembles_content`
--

CREATE TABLE `ensembles_content` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ensembles_text`
--

CREATE TABLE `ensembles_text` (
  `id` int(11) NOT NULL,
  `section` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jambangan_content`
--

CREATE TABLE `jambangan_content` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jambangan_text`
--

CREATE TABLE `jambangan_text` (
  `id` int(11) NOT NULL,
  `section` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rondalla_content`
--

CREATE TABLE `rondalla_content` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rondalla_text`
--

CREATE TABLE `rondalla_text` (
  `id` int(11) NOT NULL,
  `section` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `singers_content`
--

CREATE TABLE `singers_content` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `singers_text`
--

CREATE TABLE `singers_text` (
  `id` int(11) NOT NULL,
  `section` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `theatre_content`
--

CREATE TABLE `theatre_content` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `theatre_text`
--

CREATE TABLE `theatre_text` (
  `id` int(11) NOT NULL,
  `section` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `visual_content`
--

CREATE TABLE `visual_content` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `visual_text`
--

CREATE TABLE `visual_text` (
  `id` int(11) NOT NULL,
  `section` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wmsu_band_content`
--

CREATE TABLE `wmsu_band_content` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wmsu_band_content`
--

INSERT INTO `wmsu_band_content` (`id`, `title`, `description`, `image`, `created_at`) VALUES
(3, 'paolo', 'bayot', '1.png', '2025-04-27 17:09:10');

-- --------------------------------------------------------

--
-- Table structure for table `wmsu_band_text`
--

CREATE TABLE `wmsu_band_text` (
  `id` int(11) NOT NULL,
  `section` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wmsu_band_text`
--

INSERT INTO `wmsu_band_text` (`id`, `section`, `content`, `created_at`) VALUES
(1, 'h2', 'Testing', '2025-04-27 16:56:17'),
(2, 'h2', 'The WMSU Marching Band', '2025-04-27 16:58:37');

-- --------------------------------------------------------

--
-- Table structure for table `wmsu_chorale_content`
--

CREATE TABLE `wmsu_chorale_content` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wmsu_chorale_text`
--

CREATE TABLE `wmsu_chorale_text` (
  `id` int(11) NOT NULL,
  `section` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wmsu_chorale_text`
--

INSERT INTO `wmsu_chorale_text` (`id`, `section`, `content`, `created_at`) VALUES
(1, 'h2', 'test', '2025-04-28 04:29:21');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `account`
--
ALTER TABLE `account`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ensembles_content`
--
ALTER TABLE `ensembles_content`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ensembles_text`
--
ALTER TABLE `ensembles_text`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jambangan_content`
--
ALTER TABLE `jambangan_content`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jambangan_text`
--
ALTER TABLE `jambangan_text`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rondalla_content`
--
ALTER TABLE `rondalla_content`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rondalla_text`
--
ALTER TABLE `rondalla_text`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `singers_content`
--
ALTER TABLE `singers_content`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `singers_text`
--
ALTER TABLE `singers_text`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `theatre_content`
--
ALTER TABLE `theatre_content`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `theatre_text`
--
ALTER TABLE `theatre_text`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `visual_content`
--
ALTER TABLE `visual_content`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `visual_text`
--
ALTER TABLE `visual_text`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wmsu_band_content`
--
ALTER TABLE `wmsu_band_content`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wmsu_band_text`
--
ALTER TABLE `wmsu_band_text`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wmsu_chorale_content`
--
ALTER TABLE `wmsu_chorale_content`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wmsu_chorale_text`
--
ALTER TABLE `wmsu_chorale_text`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `account`
--
ALTER TABLE `account`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ensembles_content`
--
ALTER TABLE `ensembles_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ensembles_text`
--
ALTER TABLE `ensembles_text`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jambangan_content`
--
ALTER TABLE `jambangan_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jambangan_text`
--
ALTER TABLE `jambangan_text`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rondalla_content`
--
ALTER TABLE `rondalla_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rondalla_text`
--
ALTER TABLE `rondalla_text`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `singers_content`
--
ALTER TABLE `singers_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `singers_text`
--
ALTER TABLE `singers_text`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `theatre_content`
--
ALTER TABLE `theatre_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `theatre_text`
--
ALTER TABLE `theatre_text`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `visual_content`
--
ALTER TABLE `visual_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `visual_text`
--
ALTER TABLE `visual_text`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wmsu_band_content`
--
ALTER TABLE `wmsu_band_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `wmsu_band_text`
--
ALTER TABLE `wmsu_band_text`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `wmsu_chorale_content`
--
ALTER TABLE `wmsu_chorale_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `wmsu_chorale_text`
--
ALTER TABLE `wmsu_chorale_text`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
