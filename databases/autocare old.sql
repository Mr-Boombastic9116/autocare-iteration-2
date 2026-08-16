-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 14, 2026 at 06:26 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `autocare`
--

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`id`, `name`) VALUES
(1, 'Maruti Suzuki'),
(2, 'Hyundai'),
(3, 'Tata Motors'),
(4, 'Mahindra & Mahindra'),
(5, 'Honda'),
(6, 'Toyota'),
(7, 'Kia'),
(8, 'Skoda'),
(9, 'Volkswagen'),
(10, 'Renault'),
(11, 'Nissan'),
(12, 'MG Motor'),
(13, 'Ford'),
(14, 'Jeep'),
(15, 'Citroen'),
(16, 'BMW'),
(17, 'Mercedes-Benz'),
(18, 'Audi'),
(19, 'Jaguar'),
(20, 'Land Rover'),
(21, 'Volvo'),
(22, 'Lexus'),
(23, 'Mini'),
(24, 'Porsche'),
(25, 'Ferrari'),
(26, 'Lamborghini'),
(27, 'Rolls-Royce'),
(28, 'Bentley'),
(29, 'BYD'),
(30, 'Isuzu'),
(31, 'Force Motors'),
(32, 'Hindustan Motors'),
(33, 'Honda'),
(34, 'TVS Motor'),
(35, 'Suzuki'),
(36, 'Hero MotoCorp'),
(37, 'Yamaha'),
(38, 'Bajaj Auto'),
(39, 'Ather Energy'),
(40, 'Ola Electric'),
(41, 'Ampere'),
(42, 'Okinawa Autotech'),
(43, 'Hero Electric'),
(44, 'Pure EV'),
(45, 'Bounce Infinity'),
(46, 'Greaves Electric Mobility'),
(47, 'LML'),
(48, 'Kinetic Green'),
(49, 'River Mobility'),
(50, 'Simple Energy'),
(51, 'Lectrix EV'),
(52, 'Benling India'),
(53, 'Odysse Electric'),
(54, 'BGauss'),
(55, 'Komaki'),
(56, 'Evolet'),
(57, 'Gemopai');

-- --------------------------------------------------------

--
-- Table structure for table `fuels`
--

CREATE TABLE `fuels` (
  `id` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fuels`
--

INSERT INTO `fuels` (`id`, `name`) VALUES
(1, 'Petrol'),
(2, 'Diesel'),
(3, 'Electric');

-- --------------------------------------------------------

--
-- Table structure for table `models`
--

CREATE TABLE `models` (
  `id` int(11) NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `models`
--

INSERT INTO `models` (`id`, `company_id`, `name`) VALUES
(1, 2, 'Santro'),
(2, 2, 'Santro Xing'),
(3, 2, 'Getz'),
(4, 2, 'Getz Prime'),
(5, 2, 'Accent'),
(6, 2, 'Verna'),
(7, 2, 'Elantra'),
(8, 2, 'Sonata'),
(9, 2, 'Tucson'),
(10, 2, 'Terracan'),
(11, 2, 'i10'),
(12, 2, 'Grand i10'),
(13, 2, 'Grand i10 Nios'),
(14, 2, 'Xcent'),
(15, 2, 'Aura'),
(16, 2, 'i20'),
(17, 2, 'i20 Active'),
(18, 2, 'i20 N Line'),
(19, 2, 'Creta'),
(20, 2, 'Creta N Line'),
(21, 2, 'Creta Electric'),
(22, 2, 'Venue'),
(23, 2, 'Venue N Line'),
(24, 2, 'Alcazar'),
(25, 2, 'Exter'),
(26, 2, 'Kona Electric'),
(27, 2, 'Ioniq 5'),
(28, 2, 'Santa Fe'),
(29, 2, 'Palisade'),
(30, 2, 'Bayon');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`) VALUES
(1, 'admin', 'admin', 'admin123');

-- --------------------------------------------------------

--
-- Table structure for table `variants`
--

CREATE TABLE `variants` (
  `id` int(11) NOT NULL,
  `model_id` int(11) DEFAULT NULL,
  `year_id` int(11) DEFAULT NULL,
  `fuel_id` int(11) DEFAULT NULL,
  `name` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `variants`
--

INSERT INTO `variants` (`id`, `model_id`, `year_id`, `fuel_id`, `name`) VALUES
(1, 19, 12, 1, 'E 1.5 MPi Petrol'),
(2, 19, 12, 1, 'EX 1.5 MPi Petrol'),
(3, 19, 12, 1, 'S 1.5 MPi Petrol'),
(4, 19, 12, 1, 'S(O) 1.5 MPi Petrol'),
(5, 19, 12, 1, 'SX 1.5 MPi Petrol'),
(6, 19, 12, 1, 'SX(O) 1.5 MPi Petrol'),
(7, 19, 12, 1, 'SX Turbo 1.5 GDi Petrol'),
(8, 19, 12, 1, 'SX(O) Turbo 1.5 GDi Petrol');

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

CREATE TABLE `vehicles` (
  `id` int(11) NOT NULL,
  `user` varchar(100) DEFAULT NULL,
  `company` varchar(100) DEFAULT NULL,
  `model` varchar(100) DEFAULT NULL,
  `year` varchar(10) DEFAULT NULL,
  `fuel` varchar(50) DEFAULT NULL,
  `variant` varchar(150) DEFAULT NULL,
  `license_no` varchar(50) DEFAULT NULL,
  `kms` int(11) DEFAULT NULL,
  `last_service` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `ownership_date` date DEFAULT NULL,
  `kms_last_service` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicles`
--

INSERT INTO `vehicles` (`id`, `user`, `company`, `model`, `year`, `fuel`, `variant`, `license_no`, `kms`, `last_service`, `created_at`, `ownership_date`, `kms_last_service`) VALUES
(1, 'admin', 'Maruti Suzuki', 'Alto K10', '2023', 'CNG', 'Maruti Suzuki Alto 800 LXI Opt CNG', 'GA03J6723', 40000, '2024-01-01', '2026-04-13 17:36:02', '2023-01-19', 37000),
(2, 'admin', 'Ather Energy', '450', '2025', 'EV', 'S', 'GA08AA1692', 50000, '2024-02-01', '2026-04-13 17:36:02', '2025-06-12', 42578),
(3, 'admin', 'Hyundai', 'Creta', '2026', 'Petrol', 'SX(O) Turbo 1.5 GDi Petrol', 'GA08AA2064', 13000, '2026-02-20', '2026-04-13 17:39:26', '2026-01-17', 5000);

-- --------------------------------------------------------

--
-- Table structure for table `years`
--

CREATE TABLE `years` (
  `id` int(11) NOT NULL,
  `year` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `years`
--

INSERT INTO `years` (`id`, `year`) VALUES
(1, '2015'),
(2, '2016'),
(3, '2017'),
(4, '2018'),
(5, '2019'),
(6, '2020'),
(7, '2021'),
(8, '2022'),
(9, '2023'),
(10, '2024'),
(11, '2025'),
(12, '2026');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fuels`
--
ALTER TABLE `fuels`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `models`
--
ALTER TABLE `models`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `variants`
--
ALTER TABLE `variants`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `years`
--
ALTER TABLE `years`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `fuels`
--
ALTER TABLE `fuels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `models`
--
ALTER TABLE `models`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `variants`
--
ALTER TABLE `variants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `years`
--
ALTER TABLE `years`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
