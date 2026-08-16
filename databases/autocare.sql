-- =========================================================================
-- AUTOCARE FULL COMPLETE AUTOMOTIVE DATABASE
-- Single Self-Contained SQL File for Direct Import / Submission
-- Database: `autocare`
-- Includes: Companies, Models (209), Years, Fuels, Variants, Users,
--           Vehicles, Bookings, and Document Records
-- =========================================================================

CREATE DATABASE IF NOT EXISTS `autocare` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `autocare`;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `documents`;
DROP TABLE IF EXISTS `bookings`;
DROP TABLE IF EXISTS `vehicles`;
DROP TABLE IF EXISTS `variants`;
DROP TABLE IF EXISTS `models`;
DROP TABLE IF EXISTS `companies`;
DROP TABLE IF EXISTS `fuels`;
DROP TABLE IF EXISTS `years`;
DROP TABLE IF EXISTS `users`;
SET FOREIGN_KEY_CHECKS = 1;

-- --------------------------------------------------------
-- Table structure for table `companies`
-- --------------------------------------------------------

CREATE TABLE `companies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `country` varchar(50) DEFAULT 'India',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `companies` (`id`, `name`, `country`) VALUES
(1, 'Maruti Suzuki', 'India/Japan'),
(2, 'Hyundai', 'South Korea'),
(3, 'Tata Motors', 'India'),
(4, 'Mahindra & Mahindra', 'India'),
(5, 'Honda', 'Japan'),
(6, 'Toyota', 'Japan'),
(7, 'Kia', 'South Korea'),
(8, 'Skoda', 'Czech Republic'),
(9, 'Volkswagen', 'Germany'),
(10, 'Renault', 'France'),
(11, 'Nissan', 'Japan'),
(12, 'MG Motor', 'UK/China'),
(13, 'Ford', 'USA'),
(14, 'Jeep', 'USA'),
(15, 'Citroen', 'France'),
(16, 'BMW', 'Germany'),
(17, 'Mercedes-Benz', 'Germany'),
(18, 'Audi', 'Germany'),
(19, 'Jaguar', 'UK'),
(20, 'Land Rover', 'UK'),
(21, 'Volvo', 'Sweden'),
(22, 'Lexus', 'Japan'),
(23, 'Porsche', 'Germany'),
(24, 'BYD', 'China'),
(25, 'Ather Energy', 'India'),
(26, 'Ola Electric', 'India'),
(27, 'TVS Motor', 'India'),
(28, 'Hero MotoCorp', 'India'),
(29, 'Yamaha', 'Japan'),
(30, 'Bajaj Auto', 'India');

-- --------------------------------------------------------
-- Table structure for table `models`
-- --------------------------------------------------------

CREATE TABLE `models` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `body_type` varchar(50) DEFAULT 'Hatchback/SUV/Sedan',
  PRIMARY KEY (`id`),
  KEY `company_id` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `models` (`id`, `company_id`, `name`, `body_type`) VALUES
-- Maruti Suzuki (1)
(1, 1, 'Swift', 'Hatchback'),
(2, 1, 'Dzire', 'Sedan'),
(3, 1, 'Baleno', 'Hatchback'),
(4, 1, 'Brezza', 'Compact SUV'),
(5, 1, 'Ertiga', 'MPV'),
(6, 1, 'Fronx', 'Crossover'),
(7, 1, 'Grand Vitara', 'Mid-size SUV'),
(8, 1, 'Jimny', 'Off-road SUV'),
(9, 1, 'Alto K10', 'Hatchback'),
(10, 1, 'Wagon R', 'Hatchback'),
(11, 1, 'XL6', 'MPV'),
(12, 1, 'Ignis', 'Hatchback'),
(13, 1, 'S-Presso', 'Hatchback'),
(14, 1, 'Celerio', 'Hatchback'),
(15, 1, 'Invicto', 'Premium MPV'),
(16, 1, 'Eeco', 'Van'),

-- Hyundai (2)
(17, 2, 'Creta', 'Mid-size SUV'),
(18, 2, 'Venue', 'Compact SUV'),
(19, 2, 'i20', 'Hatchback'),
(20, 2, 'Verna', 'Sedan'),
(21, 2, 'Exter', 'Micro SUV'),
(22, 2, 'Grand i10 Nios', 'Hatchback'),
(23, 2, 'Aura', 'Sedan'),
(24, 2, 'Alcazar', '3-Row SUV'),
(25, 2, 'Tucson', 'Premium SUV'),
(26, 2, 'Ioniq 5', 'Electric SUV'),

-- Tata Motors (3)
(27, 3, 'Nexon', 'Compact SUV'),
(28, 3, 'Nexon EV', 'Electric SUV'),
(29, 3, 'Punch', 'Micro SUV'),
(30, 3, 'Punch EV', 'Electric Micro SUV'),
(31, 3, 'Harrier', 'Mid-size SUV'),
(32, 3, 'Safari', '3-Row SUV'),
(33, 3, 'Altroz', 'Hatchback'),
(34, 3, 'Tiago', 'Hatchback'),
(35, 3, 'Tiago EV', 'Electric Hatchback'),
(36, 3, 'Tigor', 'Sedan'),
(37, 3, 'Tigor EV', 'Electric Sedan'),
(38, 3, 'Curvv', 'Coupe SUV'),
(39, 3, 'Curvv EV', 'Electric Coupe SUV'),

-- Mahindra & Mahindra (4)
(40, 4, 'Thar', 'Off-road SUV'),
(41, 4, 'Thar Roxx', '5-Door Off-road SUV'),
(42, 4, 'Scorpio-N', 'Mid-size SUV'),
(43, 4, 'Scorpio Classic', 'SUV'),
(44, 4, 'XUV700', 'Premium SUV'),
(45, 4, 'XUV3XO', 'Compact SUV'),
(46, 4, 'Bolero', 'SUV'),
(47, 4, 'Bolero Neo', 'SUV'),
(48, 4, 'XUV400 EV', 'Electric SUV'),
(49, 4, 'Marazzo', 'MPV'),

-- Honda (5)
(50, 5, 'City', 'Sedan'),
(51, 5, 'Elevate', 'Mid-size SUV'),
(52, 5, 'Amaze', 'Compact Sedan'),
(53, 5, 'Civic', 'Executive Sedan'),
(54, 5, 'CR-V', 'Premium SUV'),
(55, 5, 'WR-V', 'Crossover'),

-- Toyota (6)
(56, 6, 'Fortuner', 'Full-size SUV'),
(57, 6, 'Innova Hycross', 'Premium MPV'),
(58, 6, 'Innova Crysta', 'MPV'),
(59, 6, 'Urban Cruiser Hyryder', 'Mid-size SUV'),
(60, 6, 'Urban Cruiser Taisor', 'Crossover'),
(61, 6, 'Glanza', 'Hatchback'),
(62, 6, 'Hilux', 'Pickup Truck'),
(63, 6, 'Camry', 'Luxury Hybrid Sedan'),
(64, 6, 'Land Cruiser 300', 'Luxury Off-road SUV'),

-- Kia (7)
(65, 7, 'Seltos', 'Mid-size SUV'),
(66, 7, 'Sonet', 'Compact SUV'),
(67, 7, 'Carens', 'MPV'),
(68, 7, 'EV6', 'Electric Crossover'),
(69, 7, 'EV9', 'Luxury Electric SUV'),
(70, 7, 'Carnival', 'Luxury MPV'),

-- Skoda (8)
(71, 8, 'Slavia', 'Sedan'),
(72, 8, 'Kushaq', 'Mid-size SUV'),
(73, 8, 'Kylaq', 'Compact SUV'),
(74, 8, 'Kodiaq', 'Full-size SUV'),
(75, 8, 'Superb', 'Executive Sedan'),
(76, 8, 'Octavia', 'Sedan'),

-- Volkswagen (9)
(77, 9, 'Virtus', 'Sedan'),
(78, 9, 'Taigun', 'Mid-size SUV'),
(79, 9, 'Tiguan', 'Premium SUV'),
(80, 9, 'Polo', 'Hatchback'),
(81, 9, 'Vento', 'Sedan'),

-- Renault (10)
(82, 10, 'Kiger', 'Compact SUV'),
(83, 10, 'Triber', 'Compact MPV'),
(84, 10, 'Kwid', 'Hatchback'),
(85, 10, 'Duster', 'Mid-size SUV'),

-- Nissan (11)
(86, 11, 'Magnite', 'Compact SUV'),
(87, 11, 'Kicks', 'Crossover'),
(88, 11, 'X-Trail', 'Premium SUV'),

-- MG Motor (12)
(89, 12, 'Hector', 'Mid-size SUV'),
(90, 12, 'Hector Plus', '3-Row SUV'),
(91, 12, 'Astor', 'Compact SUV'),
(92, 12, 'ZS EV', 'Electric SUV'),
(93, 12, 'Comet EV', 'Electric City Car'),
(94, 12, 'Windsor EV', 'Electric Crossover'),
(95, 12, 'Gloster', 'Full-size SUV'),

-- Ford (13)
(96, 13, 'EcoSport', 'Compact SUV'),
(97, 13, 'Endeavour', 'Full-size SUV'),
(98, 13, 'Figo', 'Hatchback'),
(99, 13, 'Aspire', 'Compact Sedan'),
(100, 13, 'Freestyle', 'Crossover'),
(101, 13, 'Mustang GT', 'Sports Coupe'),

-- Jeep (14)
(102, 14, 'Compass', 'Mid-size SUV'),
(103, 14, 'Meridian', '3-Row SUV'),
(104, 14, 'Wrangler', 'Hardcore Off-road SUV'),
(105, 14, 'Grand Cherokee', 'Luxury SUV'),

-- Citroen (15)
(106, 15, 'C3', 'Hatchback'),
(107, 15, 'C3 Aircross', 'Compact SUV'),
(108, 15, 'Basalt', 'Coupe SUV'),
(109, 15, 'C5 Aircross', 'Premium SUV'),
(110, 15, 'eC3 EV', 'Electric Hatchback'),

-- BMW (16)
(111, 16, '3 Series Long Wheelbase', 'Luxury Sedan'),
(112, 16, '5 Series', 'Executive Sedan'),
(113, 16, '7 Series', 'Ultra-Luxury Sedan'),
(114, 16, 'X1', 'Luxury Compact SUV'),
(115, 16, 'X3', 'Luxury Mid-size SUV'),
(116, 16, 'X5', 'Luxury SUV'),
(117, 16, 'X7', 'Full-size Luxury SUV'),
(118, 16, 'i4', 'Electric Gran Coupe'),
(119, 16, 'iX', 'Electric Luxury SUV'),
(120, 16, 'M3 Competition', 'Performance Sedan'),
(121, 16, 'M5', 'Performance Sedan'),

-- Mercedes-Benz (17)
(122, 17, 'A-Class Limousine', 'Luxury Sedan'),
(123, 17, 'C-Class', 'Luxury Sedan'),
(124, 17, 'E-Class Long Wheelbase', 'Executive Sedan'),
(125, 17, 'S-Class', 'Flagship Luxury Sedan'),
(126, 17, 'GLA', 'Luxury Compact SUV'),
(127, 17, 'GLC', 'Luxury SUV'),
(128, 17, 'GLE', 'Luxury SUV'),
(129, 17, 'GLS', 'Full-size Luxury SUV'),
(130, 17, 'EQE SUV', 'Electric Luxury SUV'),
(131, 17, 'EQS Sedan', 'Electric Flagship Sedan'),
(132, 17, 'G-Class G 63 AMG', 'Luxury Off-road SUV'),

-- Audi (18)
(133, 18, 'A4', 'Luxury Sedan'),
(134, 18, 'A6', 'Executive Sedan'),
(135, 18, 'A8 L', 'Flagship Luxury Sedan'),
(136, 18, 'Q3', 'Luxury Compact SUV'),
(137, 18, 'Q5', 'Luxury SUV'),
(138, 18, 'Q7', '3-Row Luxury SUV'),
(139, 18, 'Q8', 'Luxury Coupe SUV'),
(140, 18, 'e-tron GT', 'Electric Performance Sedan'),
(141, 18, 'RS5 Sportback', 'Performance Coupe'),

-- Jaguar (19)
(142, 19, 'F-Pace', 'Performance SUV'),
(143, 19, 'I-Pace EV', 'Electric SUV'),
(144, 19, 'XE', 'Luxury Sports Sedan'),
(145, 19, 'XF', 'Executive Luxury Sedan'),
(146, 19, 'F-Type', 'Sports Coupe'),

-- Land Rover (20)
(147, 20, 'Defender 90', 'Off-road 3-Door SUV'),
(148, 20, 'Defender 110', 'Off-road 5-Door SUV'),
(149, 20, 'Discovery', 'Full-size SUV'),
(150, 20, 'Discovery Sport', 'Compact Luxury SUV'),
(151, 20, 'Range Rover', 'Flagship Luxury SUV'),
(152, 20, 'Range Rover Sport', 'Performance Luxury SUV'),
(153, 20, 'Range Rover Velar', 'Luxury Coupe SUV'),
(154, 20, 'Range Rover Evoque', 'Compact Luxury SUV'),

-- Volvo (21)
(155, 21, 'XC40 Recharge', 'Electric Compact SUV'),
(156, 21, 'XC60', 'Luxury SUV'),
(157, 21, 'XC90', '3-Row Flagship SUV'),
(158, 21, 'EX30 EV', 'Electric Compact SUV'),
(159, 21, 'EX90 EV', 'Electric Flagship SUV'),
(160, 21, 'S90', 'Luxury Executive Sedan'),

-- Lexus (22)
(161, 22, 'ES 300h', 'Luxury Hybrid Sedan'),
(162, 22, 'NX 350h', 'Luxury Hybrid SUV'),
(163, 22, 'RX 500h F SPORT', 'Performance Hybrid SUV'),
(164, 22, 'LM 350h', 'Ultra-Luxury MPV'),
(165, 22, 'LX 600', 'Flagship Luxury SUV'),

-- Porsche (23)
(166, 23, '911 Carrera S', 'Iconic Sports Car'),
(167, 23, 'Cayenne Turbo', 'Performance Luxury SUV'),
(168, 23, 'Macan GTS', 'Compact Performance SUV'),
(169, 23, 'Taycan EV', 'Electric Performance Sedan'),
(170, 23, 'Panamera GTS', 'Luxury Performance Sedan'),
(171, 23, '718 Cayman', 'Mid-Engine Sports Coupe'),

-- BYD (24)
(172, 24, 'Atto 3', 'Electric SUV'),
(173, 24, 'Seal', 'Electric Performance Sedan'),
(174, 24, 'eMAX 7', 'Electric MPV'),
(175, 24, 'Dolphin EV', 'Electric Hatchback'),

-- Ather Energy (25)
(176, 25, '450X Apex', 'Electric Scooter'),
(177, 25, '450S', 'Electric Scooter'),
(178, 25, 'Rizta Z', 'Family Electric Scooter'),
(179, 25, '450X 3.7kWh', 'Electric Scooter'),

-- Ola Electric (26)
(180, 26, 'S1 Pro Gen 2', 'Electric Scooter'),
(181, 26, 'S1 Air', 'Electric Scooter'),
(182, 26, 'S1 X+', 'Electric Scooter'),
(183, 26, 'Roadster EV Bike', 'Electric Motorcycle'),

-- TVS Motor (27)
(184, 27, 'Apache RTR 160 4V', 'Sports Motorcycle'),
(185, 27, 'Apache RR 310', 'Super-Sport Motorcycle'),
(186, 27, 'Jupiter 125', 'Scooter'),
(187, 27, 'iQube Electric', 'Electric Scooter'),
(188, 27, 'Ntorq 125', 'Sporty Scooter'),
(189, 27, 'Raider 125', 'Commuter Motorcycle'),
(190, 27, 'Ronin 225', 'Modern Retro Motorcycle'),

-- Hero MotoCorp (28)
(191, 28, 'Splendor+ XTEC', 'Commuter Motorcycle'),
(192, 28, 'HF Deluxe', 'Commuter Motorcycle'),
(193, 28, 'Xpulse 200 4V', 'Adventure Motorcycle'),
(194, 28, 'Karizma XMR', 'Sports Motorcycle'),
(195, 28, 'Mavrick 440', 'Roadster Motorcycle'),
(196, 28, 'Vida V1 Pro EV', 'Electric Scooter'),

-- Yamaha (29)
(197, 29, 'YZF R15 V4', 'Super-Sport Motorcycle'),
(198, 29, 'MT-15 V2', 'Hyper Naked Motorcycle'),
(199, 29, 'FZ-S V4', 'Street Motorcycle'),
(200, 29, 'Aerox 155', 'Maxi Scooter'),
(201, 29, 'RayZR 125 Fi Hybrid', 'Hybrid Scooter'),
(202, 29, 'Fascino 125 Fi Hybrid', 'Classic Scooter'),

-- Bajaj Auto (30)
(203, 30, 'Pulsar N250', 'Sports Street Bike'),
(204, 30, 'Pulsar NS200', 'Naked Sports Bike'),
(205, 30, 'Pulsar 220F', 'Legendary Sports Bike'),
(206, 30, 'Dominar 400', 'Power Cruiser Motorcycle'),
(207, 30, 'Chetak 2901 EV', 'Electric Scooter'),
(208, 30, 'Freedom 125 CNG', 'World First CNG Motorcycle'),
(209, 30, 'Platina 110', 'Commuter Bike');

-- --------------------------------------------------------
-- Table structure for table `years`
-- --------------------------------------------------------

CREATE TABLE `years` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `year` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `years` (`id`, `year`) VALUES
(1, '2015'), (2, '2016'), (3, '2017'), (4, '2018'), (5, '2019'),
(6, '2020'), (7, '2021'), (8, '2022'), (9, '2023'), (10, '2024'),
(11, '2025'), (12, '2026');

-- --------------------------------------------------------
-- Table structure for table `fuels`
-- --------------------------------------------------------

CREATE TABLE `fuels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `fuels` (`id`, `name`) VALUES
(1, 'Petrol'),
(2, 'Diesel'),
(3, 'Electric (EV)'),
(4, 'CNG'),
(5, 'Strong Hybrid');

-- --------------------------------------------------------
-- Table structure for table `variants`
-- --------------------------------------------------------

CREATE TABLE `variants` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `model_id` int(11) NOT NULL,
  `year_id` int(11) DEFAULT 12,
  `fuel_id` int(11) DEFAULT 1,
  `name` varchar(150) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `model_id` (`model_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `variants` (`id`, `model_id`, `year_id`, `fuel_id`, `name`) VALUES
-- Creta (17)
(1, 17, 12, 1, 'E 1.5 MPi Petrol MT'),
(2, 17, 12, 1, 'EX 1.5 MPi Petrol MT'),
(3, 17, 12, 1, 'S 1.5 MPi Petrol MT'),
(4, 17, 12, 1, 'S(O) 1.5 MPi Petrol IVT'),
(5, 17, 12, 1, 'SX 1.5 MPi Petrol MT'),
(6, 17, 12, 1, 'SX(O) 1.5 MPi Petrol IVT'),
(7, 17, 12, 1, 'SX(O) Turbo 1.5 GDi Petrol DCT'),
(8, 17, 12, 2, 'SX(O) 1.5 U2 CRDi Diesel AT'),

-- Swift (1)
(9, 1, 12, 1, 'LXi 1.2 Z-Series Petrol MT'),
(10, 1, 12, 1, 'VXi 1.2 Z-Series Petrol MT'),
(11, 1, 12, 1, 'ZXi 1.2 Z-Series Petrol MT'),
(12, 1, 12, 1, 'ZXi+ 1.2 Z-Series Petrol AMT'),
(13, 1, 12, 4, 'VXi 1.2 CNG MT'),

-- Nexon (27)
(14, 27, 12, 1, 'Smart 1.2 Turbo Petrol MT'),
(15, 27, 12, 1, 'Pure 1.2 Turbo Petrol MT'),
(16, 27, 12, 1, 'Creative+ 1.2 Turbo Petrol DCA'),
(17, 27, 12, 1, 'Fearless+ S 1.2 Turbo Petrol DCA'),
(18, 27, 12, 2, 'Fearless+ S 1.5 Diesel AMT'),

-- Thar (40)
(19, 40, 12, 2, 'AX(O) Convertible 2.2 Diesel MT RWD'),
(20, 40, 12, 2, 'LX Hard Top 2.2 Diesel MT 4WD'),
(21, 40, 12, 2, 'LX Hard Top 2.2 Diesel AT 4WD'),
(22, 40, 12, 1, 'LX Hard Top 2.0 Turbo Petrol AT 4WD'),

-- Fortuner (56)
(23, 56, 12, 2, '4x2 2.8 Diesel MT'),
(24, 56, 12, 2, '4x4 2.8 Diesel AT'),
(25, 56, 12, 2, 'Legender 4x4 2.8 Diesel AT'),
(26, 56, 12, 2, 'GR-S 4x4 2.8 Diesel AT'),

-- City (50)
(27, 50, 12, 1, 'V 1.5 i-VTEC MT'),
(28, 50, 12, 1, 'VX 1.5 i-VTEC CVT'),
(29, 50, 12, 1, 'ZX 1.5 i-VTEC CVT'),
(30, 50, 12, 5, 'ZX e:HEV Strong Hybrid e-CVT'),

-- Dzire (2)
(31, 2, 12, 1, 'LXi Petrol MT'),
(32, 2, 12, 1, 'VXi Petrol MT'),
(33, 2, 12, 1, 'ZXi+ Petrol AMT'),

-- Baleno (3)
(34, 3, 12, 1, 'Sigma 1.2 Petrol MT'),
(35, 3, 12, 1, 'Delta 1.2 Petrol AMT'),
(36, 3, 12, 1, 'Alpha 1.2 Petrol AMT'),

-- Brezza (4)
(37, 4, 12, 1, 'LXi 1.5 Petrol MT'),
(38, 4, 12, 1, 'VXi 1.5 Petrol MT'),
(39, 4, 12, 1, 'ZXi+ 1.5 Petrol AT'),

-- Scorpio-N (42)
(40, 42, 12, 2, 'Z4 2.2 Diesel MT'),
(41, 42, 12, 2, 'Z8L 2.2 Diesel AT 4XPLOR'),

-- XUV700 (44)
(42, 44, 12, 1, 'AX5 2.0 Turbo Petrol MT'),
(43, 44, 12, 2, 'AX7L 2.2 Diesel AT AWD'),

-- Seltos (65)
(44, 65, 12, 1, 'HTX 1.5 Petrol MT'),
(45, 65, 12, 1, 'GTX+ Turbo Petrol DCT'),
(46, 65, 12, 2, 'X-Line 1.5 Diesel AT'),

-- Slavia (71)
(47, 71, 12, 1, 'Active 1.0 TSI MT'),
(48, 71, 12, 1, 'Style 1.5 TSI DSG'),

-- Virtus (77)
(49, 77, 12, 1, 'Dynamic Line 1.0 TSI AT'),
(50, 77, 12, 1, 'Performance Line 1.5 TSI DSG'),

-- BMW 3 Series (111)
(51, 111, 12, 1, '330Li M Sport Petrol AT'),

-- Mercedes C-Class (123)
(52, 123, 12, 1, 'C 200 Petrol AT'),
(53, 123, 12, 2, 'C 220d Diesel AT');

-- --------------------------------------------------------
-- Table structure for table `users`
-- --------------------------------------------------------

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `mobile` varchar(15) DEFAULT NULL,
  `country_code` varchar(5) DEFAULT '+91',
  `state` varchar(50) DEFAULT 'Goa',
  `city` varchar(50) DEFAULT 'Margao',
  `pincode` varchar(10) DEFAULT '403601',
  `address` text DEFAULT NULL,
  `notify_email` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `users` (`id`, `name`, `username`, `email`, `password`, `mobile`, `country_code`, `state`, `city`, `pincode`, `address`, `notify_email`, `created_at`) VALUES
(1, 'Vedhant Sawant', 'admin', 'admin@autocare.com', '$2y$10$TfhosFqyD7uedqdxf7ilTeKuNvgpSZK0PxGc3FNTMmRJ6l4l7AC4O', '9876543210', '+91', 'Goa', 'Margao', '403601', 'Flat 302, AutoCare Enclave, Margao, Goa', 1, '2026-04-18 10:00:00'),
(2, 'Rahul Sharma', 'rahul', 'rahul@gmail.com', '$2y$10$TfhosFqyD7uedqdxf7ilTeKuNvgpSZK0PxGc3FNTMmRJ6l4l7AC4O', '9822114455', '+91', 'Goa', 'Panaji', '403001', 'Miramar Beach Road, Panaji, Goa', 1, '2026-04-18 11:30:00');

-- --------------------------------------------------------
-- Table structure for table `vehicles`
-- --------------------------------------------------------

CREATE TABLE `vehicles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` varchar(100) NOT NULL,
  `company` varchar(100) NOT NULL,
  `model` varchar(100) NOT NULL,
  `year` varchar(10) DEFAULT '2024',
  `fuel` varchar(50) DEFAULT 'Petrol',
  `variant` varchar(150) DEFAULT NULL,
  `license_no` varchar(50) NOT NULL,
  `kms` int(11) NOT NULL DEFAULT 10000,
  `last_service` date DEFAULT NULL,
  `ownership_date` date DEFAULT NULL,
  `kms_last_service` int(11) DEFAULT 5000,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `vehicles` (`id`, `user`, `company`, `model`, `year`, `fuel`, `variant`, `license_no`, `kms`, `last_service`, `ownership_date`, `kms_last_service`, `created_at`) VALUES
(1, 'admin', 'Hyundai', 'Creta', '2026', 'Petrol', 'SX(O) Turbo 1.5 GDi Petrol DCT', 'GA03AB1234', 13500, '2026-02-20', '2026-01-17', 5000, '2026-04-18 12:00:00'),
(2, 'admin', 'Maruti Suzuki', 'Swift', '2024', 'Petrol', 'ZXi+ 1.2 Z-Series Petrol AMT', 'GA03J6723', 42000, '2024-01-10', '2023-01-19', 37000, '2026-04-18 12:05:00'),
(3, 'admin', 'Mahindra & Mahindra', 'Thar', '2025', 'Diesel', 'LX Hard Top 2.2 Diesel AT 4WD', 'GA08AA1692', 51000, '2024-02-01', '2025-06-12', 42578, '2026-04-18 12:10:00'),
(4, 'rahul', 'Toyota', 'Fortuner', '2024', 'Diesel', '4x4 2.8 Diesel AT', 'GA07B9988', 28000, '2025-11-15', '2024-03-01', 20000, '2026-04-18 12:15:00');

-- --------------------------------------------------------
-- Table structure for table `bookings`
-- --------------------------------------------------------

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` varchar(100) NOT NULL,
  `vehicle_id` int(11) NOT NULL,
  `service_date` date DEFAULT NULL,
  `time_slot` varchar(50) DEFAULT NULL,
  `services` text DEFAULT NULL,
  `special_request` text DEFAULT NULL,
  `total` int(11) DEFAULT 1500,
  `advance` int(11) DEFAULT 500,
  `status` varchar(50) DEFAULT 'Confirmed',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user` (`user`),
  KEY `vehicle_id` (`vehicle_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `bookings` (`id`, `user`, `vehicle_id`, `service_date`, `time_slot`, `services`, `special_request`, `total`, `advance`, `status`, `created_at`) VALUES
(1, 'admin', 1, '2026-01-01', '12:00 - 1:00 PM', 'Engine oil change, Oil filter replacement, Air filter replacement, Wheel balancing', 'Check AC cooling performance', 9700, 500, 'Confirmed', '2026-04-18 12:13:42'),
(2, 'admin', 1, '2026-02-04', '12:00 - 1:00 PM', 'Engine oil change, Oil filter replacement, Cabin filter replacement, Brake pad (front)', 'Brakes making squeaking noise', 12500, 500, 'Confirmed', '2026-04-18 12:20:59');

-- --------------------------------------------------------
-- Table structure for table `documents`
-- --------------------------------------------------------

CREATE TABLE `documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vehicle_id` int(11) NOT NULL,
  `user` varchar(100) NOT NULL,
  `doc_type` varchar(100) NOT NULL,
  `doc_name` varchar(255) NOT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `issue_date` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `vehicle_id` (`vehicle_id`),
  KEY `user` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `documents` (`id`, `vehicle_id`, `user`, `doc_type`, `doc_name`, `file_name`, `issue_date`, `expiry_date`, `created_at`) VALUES
(1, 1, 'admin', 'RC', 'Registration Certificate', 'rc_ga08aa2064.pdf', '2026-01-17', '2041-01-17', '2026-04-18 12:00:00'),
(2, 1, 'admin', 'Insurance', 'Comprehensive Car Insurance', 'insurance_policy.pdf', '2026-01-17', '2027-01-17', '2026-04-18 12:00:00'),
(3, 1, 'admin', 'PUC', 'Pollution Under Control', 'puc_certificate.pdf', '2026-01-17', '2026-07-17', '2026-04-18 12:00:00');
