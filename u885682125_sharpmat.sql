-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 13, 2025 at 08:53 AM
-- Server version: 10.11.10-MariaDB
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u885682125_sharpmat`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Husmanskost'),
(2, 'Kebab'),
(3, 'Pizza'),
(4, 'Thaimat'),
(5, 'Övrigt');

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `region` varchar(100) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `latitude` smallint(11) NOT NULL,
  `longitude` smallint(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `enable_translation` tinyint(1) DEFAULT 0,
  `translate_from` varchar(5) DEFAULT NULL,
  `use_variants` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`id`, `name`, `email`, `password`, `address`, `city`, `postal_code`, `region`, `phone`, `latitude`, `longitude`, `created_at`, `enable_translation`, `translate_from`, `use_variants`) VALUES
(1, 'Jonas Lunch', 'jonas.d.stromberg@gmail.com', '$2y$10$UJUF21uCZqu2XvUJ/Tn2i./kO8v4EkTtBX0I2Of0cOHiJDPURC2gC', 'Jaktstigen 7', 'Vindeln', '92231', 'Västerbotten', '0702900213', 0, 0, '2025-04-12 17:20:38', 1, 'th', 1);

-- --------------------------------------------------------

--
-- Table structure for table `lunches`
--

CREATE TABLE `lunches` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `dish_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lunches`
--

INSERT INTO `lunches` (`id`, `company_id`, `date`, `dish_name`, `description`, `price`, `category_id`, `image`, `created_at`) VALUES
(1, 1, '2025-04-17', 'Kebabrulle', 'stor rulle', 110.00, 2, 'https://47an.se/wp-content/uploads/2021/12/rulle-gyros.png', '2025-04-12 18:56:07'),
(2, 1, '2025-05-08', 'Keng pedh', 'curry\r\nRöd curry med kokosmjölk, zucchini, morötter, paprika, bambuskott, basilika och limeblad.', 110.00, 4, 'https://restaurant-nonla-amersfoort.nl/wp-content/uploads/2020/01/Keng-Ped-Curry-2.jpg', '2025-04-12 20:45:16'),
(3, 1, '2025-04-29', 'Keng pedh', 'curry\r\nRöd curry med kokosmjölk, zucchini, morötter, paprika, bambuskott, basilika och limeblad.', 110.00, 4, 'https://restaurant-nonla-amersfoort.nl/wp-content/uploads/2020/01/Keng-Ped-Curry-2.jpg', '2025-04-12 20:45:42'),
(4, 1, '2025-04-15', 'ny kletig bekab', 'Gör egen kebab! Inget fuffens - den här kebaben innehåller äkta råvaror. Enkelt att karva upp och frysa in bitarna. Jag använder lökpulver för att vanlig lök krymper när den tillagas och gör att kebaben blir ojämn och hålig.', 110.00, NULL, 'https://eu-central-1.linodeobjects.com/tasteline/2011/03/kebab-foto-linnea-sward-mathem-800x800.jpg', '2025-04-13 08:38:18');

-- --------------------------------------------------------

--
-- Table structure for table `lunch_templates`
--

CREATE TABLE `lunch_templates` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `dish_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lunch_templates`
--

INSERT INTO `lunch_templates` (`id`, `company_id`, `dish_name`, `description`, `price`, `category_id`, `image`, `created_at`) VALUES
(1, 1, 'Kebabrulle', 'stor rulle', 110.00, 2, 'https://47an.se/wp-content/uploads/2021/12/rulle-gyros.png', '2025-04-12 18:56:07'),
(2, 1, 'Keng pedh', 'curry\r\nRöd curry med kokosmjölk, zucchini, morötter, paprika, bambuskott, basilika och limeblad.', 110.00, 4, 'https://restaurant-nonla-amersfoort.nl/wp-content/uploads/2020/01/Keng-Ped-Curry-2.jpg', '2025-04-12 20:45:16'),
(3, 1, 'ny kletig bekab', 'Gör egen kebab! Inget fuffens - den här kebaben innehåller äkta råvaror. Enkelt att karva upp och frysa in bitarna. Jag använder lökpulver för att vanlig lök krymper när den tillagas och gör att kebaben blir ojämn och hålig.', 110.00, NULL, 'https://eu-central-1.linodeobjects.com/tasteline/2011/03/kebab-foto-linnea-sward-mathem-800x800.jpg', '2025-04-13 08:37:52');

-- --------------------------------------------------------

--
-- Table structure for table `lunch_variants`
--

CREATE TABLE `lunch_variants` (
  `id` int(11) NOT NULL,
  `lunch_id` int(11) NOT NULL,
  `variant_name` varchar(255) DEFAULT NULL,
  `price` decimal(6,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `variant_groups`
--

CREATE TABLE `variant_groups` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `variant_options`
--

CREATE TABLE `variant_options` (
  `id` int(11) NOT NULL,
  `group_id` int(11) DEFAULT NULL,
  `label` varchar(255) DEFAULT NULL,
  `price` decimal(6,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `lunches`
--
ALTER TABLE `lunches`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `lunch_templates`
--
ALTER TABLE `lunch_templates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `lunch_variants`
--
ALTER TABLE `lunch_variants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lunch_id` (`lunch_id`);

--
-- Indexes for table `variant_groups`
--
ALTER TABLE `variant_groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `variant_options`
--
ALTER TABLE `variant_options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `group_id` (`group_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `lunches`
--
ALTER TABLE `lunches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `lunch_templates`
--
ALTER TABLE `lunch_templates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `lunch_variants`
--
ALTER TABLE `lunch_variants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `variant_groups`
--
ALTER TABLE `variant_groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `variant_options`
--
ALTER TABLE `variant_options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `lunches`
--
ALTER TABLE `lunches`
  ADD CONSTRAINT `lunches_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lunches_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `lunch_templates`
--
ALTER TABLE `lunch_templates`
  ADD CONSTRAINT `lunch_templates_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lunch_templates_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `lunch_variants`
--
ALTER TABLE `lunch_variants`
  ADD CONSTRAINT `lunch_variants_ibfk_1` FOREIGN KEY (`lunch_id`) REFERENCES `lunches` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `variant_options`
--
ALTER TABLE `variant_options`
  ADD CONSTRAINT `variant_options_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `variant_groups` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
