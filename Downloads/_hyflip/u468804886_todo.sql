-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Sep 12, 2025 at 07:27 AM
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
-- Database: `u468804886_todo`
--

-- --------------------------------------------------------

--
-- Table structure for table `flipbooks`
--

CREATE TABLE `flipbooks` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `pdf_path` varchar(500) NOT NULL,
  `embed_url` varchar(500) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `flipbooks`
--

INSERT INTO `flipbooks` (`id`, `name`, `pdf_path`, `embed_url`, `created_at`) VALUES
(7, 'CICS Journal 1', 'upload/cicssdsf.pdf', 'https://heyzine.com/flip-book/176493dbc6.html', '2025-09-05 05:06:29'),
(8, 'CICS Journal 2', 'upload/truaks.pdf', 'https://heyzine.com/flip-book/c8ea34b9a2.html', '2025-09-05 05:09:56'),
(9, 'CICS Journal 3', 'upload/dsfdsff.pdf', 'https://heyzine.com/flip-book/4a146ec88c.html', '2025-09-05 05:15:40'),
(10, 'CICS_Research_Journal_Volume_6_No__2', 'upload/CICS_Research_Journal_Volume_6_No__2.pdf', 'https://heyzine.com/flip-book/a5196d76eb.html', '2025-09-05 05:36:45'),
(11, 'flis', 'upload/flis.pdf', 'https://heyzine.com/flip-book/7203971ce4.html', '2025-09-08 02:06:11'),
(12, 'gsh', 'upload/gsh.pdf', 'https://heyzine.com/flip-book/d41461c21a.html', '2025-09-08 02:13:28'),
(13, 'shs', 'upload/shs.pdf', 'https://heyzine.com/flip-book/46ead8b120.html', '2025-09-08 02:15:11'),
(14, 'skska', 'upload/skska.pdf', 'https://heyzine.com/flip-book/0afc8a7272.html', '2025-09-08 02:15:53'),
(15, 'trys', 'upload/trys.pdf', 'https://heyzine.com/flip-book/9a1137f6fb.html', '2025-09-08 02:25:41'),
(16, 's', 'upload/s.pdf', 'https://heyzine.com/flip-book/24387b1f0d.html', '2025-09-08 02:27:33'),
(17, 'nice', 'upload/nice.pdf', 'https://heyzine.com/flip-book/26cb627245.html', '2025-09-08 02:32:19'),
(18, 'JECS', 'upload/JECS.pdf', 'https://heyzine.com/flip-book/1e0614862e.html', '2025-09-08 02:37:30'),
(19, 'scsf', 'upload/scsf.pdf', 'https://heyzine.com/flip-book/5ce5297f32.html', '2025-09-08 02:40:17'),
(20, 'sfs', 'upload/sfs.pdf', 'https://heyzine.com/flip-book/5c0a647950.html', '2025-09-08 02:42:40'),
(21, 'jecs3', 'upload/jecs3.pdf', 'https://heyzine.com/flip-book/83f0641ec9.html', '2025-09-08 02:48:42'),
(22, 'JECS22', 'upload/JECS22.pdf', 'https://heyzine.com/flip-book/9e8856fa55.html', '2025-09-08 02:52:26'),
(23, 'jshs', 'upload/jshs.pdf', 'https://heyzine.com/flip-book/88f80cdfbe.html', '2025-09-08 02:56:08');

-- --------------------------------------------------------

--
-- Table structure for table `pdf_files`
--

CREATE TABLE `pdf_files` (
  `id` int(11) NOT NULL,
  `pdfname` varchar(255) NOT NULL,
  `pagenumber` int(11) DEFAULT 0,
  `path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pdf_files`
--

INSERT INTO `pdf_files` (`id`, `pdfname`, `pagenumber`, `path`) VALUES
(1, 'Clickboatpublishable.pdf', 11, 'upload/Clickboatpublishable.pdf'),
(2, 'Publishable_Format_PantryFinder_Online_Community_Pantry_Mapping_and_Donations_Management_System_Utilizing_GPS_Technology.pdf', 10, 'upload/Publishable_Format_PantryFinder_Online_Community_Pantry_Mapping_and_Donations_Management_System_Utilizing_GPS_Technology.pdf'),
(3, 'GRABage_Publishable.pdf', 9, 'upload/GRABage_Publishable.pdf'),
(4, 'PenSys_Publishable Format.pdf', 9, 'upload/PenSys_Publishable Format.pdf'),
(5, 'Clickboatpublishable.pdf', 11, 'upload/Clickboatpublishable.pdf'),
(6, 'GRABage_Publishable.pdf', 9, 'upload/GRABage_Publishable.pdf'),
(7, 'PenSys_Publishable Format.pdf', 9, 'upload/PenSys_Publishable Format.pdf'),
(8, 'Clickboatpublishable.pdf', 11, 'upload/Clickboatpublishable.pdf'),
(9, 'GRABage_Publishable.pdf', 9, 'upload/GRABage_Publishable.pdf'),
(10, 'Clickboatpublishable.pdf', 11, 'upload/Clickboatpublishable.pdf'),
(11, 'GRABage_Publishable.pdf', 9, 'upload/GRABage_Publishable.pdf'),
(12, 'Clickboatpublishable.pdf', 11, 'upload/Clickboatpublishable.pdf'),
(13, 'PenSys_Publishable Format.pdf', 9, 'upload/PenSys_Publishable Format.pdf'),
(14, 'Clickboatpublishable.pdf', 11, 'upload/Clickboatpublishable.pdf'),
(15, 'GRABage_Publishable.pdf', 9, 'upload/GRABage_Publishable.pdf'),
(16, 'PenSys_Publishable Format.pdf', 9, 'upload/PenSys_Publishable Format.pdf'),
(17, 'Clickboatpublishable.pdf', 11, 'upload/Clickboatpublishable.pdf'),
(18, 'GRABage_Publishable.pdf', 9, 'upload/GRABage_Publishable.pdf'),
(19, 'PenSys_Publishable Format.pdf', 9, 'upload/PenSys_Publishable Format.pdf'),
(20, 'Clickboatpublishable.pdf', 11, 'upload/Clickboatpublishable.pdf'),
(21, 'GRABage_Publishable.pdf', 9, 'upload/GRABage_Publishable.pdf'),
(22, 'PenSys_Publishable Format.pdf', 9, 'upload/PenSys_Publishable Format.pdf'),
(23, 'Clickboatpublishable.pdf', 11, 'upload/Clickboatpublishable.pdf'),
(24, 'GRABage_Publishable.pdf', 9, 'upload/GRABage_Publishable.pdf'),
(25, 'PenSys_Publishable Format.pdf', 9, 'upload/PenSys_Publishable Format.pdf'),
(26, 'Clickboatpublishable.pdf', 11, 'upload/Clickboatpublishable.pdf'),
(27, 'GRABage_Publishable.pdf', 9, 'upload/GRABage_Publishable.pdf'),
(28, 'PenSys_Publishable Format.pdf', 9, 'upload/PenSys_Publishable Format.pdf'),
(29, 'Clickboatpublishable.pdf', 11, 'upload/Clickboatpublishable.pdf'),
(30, 'GRABage_Publishable.pdf', 9, 'upload/GRABage_Publishable.pdf'),
(31, 'PenSys_Publishable Format.pdf', 9, 'upload/PenSys_Publishable Format.pdf'),
(32, 'Clickboatpublishable.pdf', 11, 'upload/Clickboatpublishable.pdf'),
(33, 'GRABage_Publishable.pdf', 9, 'upload/GRABage_Publishable.pdf'),
(34, 'PenSys_Publishable Format.pdf', 9, 'upload/PenSys_Publishable Format.pdf'),
(35, 'Clickboatpublishable.pdf', 11, 'upload/Clickboatpublishable.pdf'),
(36, 'GRABage_Publishable.pdf', 9, 'upload/GRABage_Publishable.pdf'),
(37, 'PenSys_Publishable Format.pdf', 9, 'upload/PenSys_Publishable Format.pdf'),
(38, 'weekly_cost_comparison (1).pdf', 1, 'upload/weekly_cost_comparison (1).pdf'),
(39, 'weekly_cost_comparison.pdf', 1, 'upload/weekly_cost_comparison.pdf'),
(40, 'weekly_cost_comparison_landscape.pdf', 1, 'upload/weekly_cost_comparison_landscape.pdf'),
(41, 'ReactNative_Calculator_Jest_Testing.pdf', 3, 'upload/ReactNative_Calculator_Jest_Testing.pdf'),
(42, 'Jest_Installation_and_Usage.pdf', 3, 'upload/Jest_Installation_and_Usage.pdf'),
(43, 'Clickboatpublishable.pdf', 11, 'upload/Clickboatpublishable.pdf'),
(44, 'GRABage_Publishable.pdf', 9, 'upload/GRABage_Publishable.pdf'),
(45, 'PenSys_Publishable Format.pdf', 9, 'upload/PenSys_Publishable Format.pdf'),
(46, 'Clickboatpublishable.pdf', 11, 'upload/Clickboatpublishable.pdf'),
(47, 'GRABage_Publishable.pdf', 9, 'upload/GRABage_Publishable.pdf'),
(48, 'PenSys_Publishable Format.pdf', 9, 'upload/PenSys_Publishable Format.pdf'),
(49, 'PenSys_Publishable Format.pdf', 9, 'upload/PenSys_Publishable Format.pdf'),
(50, 'PenSys_Publishable Format.pdf', 9, 'upload/PenSys_Publishable Format.pdf'),
(51, 'PenSys_Publishable Format.pdf', 9, 'upload/PenSys_Publishable Format.pdf'),
(52, 'PenSys_Publishable Format.pdf', 9, 'upload/PenSys_Publishable Format.pdf'),
(53, 'PenSys_Publishable Format.pdf', 9, 'upload/PenSys_Publishable Format.pdf'),
(54, 'PenSys_Publishable Format.pdf', 9, 'upload/PenSys_Publishable Format.pdf'),
(55, 'Clickboatpublishable.pdf', 11, 'upload/Clickboatpublishable.pdf'),
(56, 'Clickboatpublishable.pdf', 11, 'upload/Clickboatpublishable.pdf'),
(57, 'PenSys_Publishable Format.pdf', 9, 'upload/PenSys_Publishable Format.pdf'),
(58, 'Clickboatpublishable.pdf', 11, 'upload/Clickboatpublishable.pdf'),
(59, 'PenSys_Publishable Format.pdf', 9, 'upload/PenSys_Publishable Format.pdf'),
(60, 'Clickboatpublishable.pdf', 11, 'upload/Clickboatpublishable.pdf'),
(61, 'PenSys_Publishable Format.pdf', 9, 'upload/PenSys_Publishable Format.pdf'),
(62, 'Clickboatpublishable.pdf', 11, 'upload/Clickboatpublishable.pdf'),
(63, 'Clickboatpublishable.pdf', 11, 'upload/Clickboatpublishable.pdf'),
(64, 'GRABage_Publishable.pdf', 9, 'upload/GRABage_Publishable.pdf'),
(65, 'PenSys_Publishable Format.pdf', 9, 'upload/PenSys_Publishable Format.pdf'),
(66, 'Clickboatpublishable.pdf', 11, 'upload/Clickboatpublishable.pdf'),
(67, 'GRABage_Publishable.pdf', 9, 'upload/GRABage_Publishable.pdf'),
(68, 'PenSys_Publishable Format.pdf', 9, 'upload/PenSys_Publishable Format.pdf'),
(69, 'Clickboatpublishable.pdf', 11, 'upload/Clickboatpublishable.pdf'),
(70, 'GRABage_Publishable.pdf', 9, 'upload/GRABage_Publishable.pdf'),
(71, 'GRABage_Publishable.pdf', 9, 'upload/GRABage_Publishable.pdf'),
(72, 'GRABage_Publishable.pdf', 9, 'upload/GRABage_Publishable.pdf'),
(73, 'GRABage_Publishable.pdf', 9, 'upload/GRABage_Publishable.pdf'),
(74, 'GRABage_Publishable.pdf', 9, 'upload/GRABage_Publishable.pdf'),
(75, 'Clickboatpublishable.pdf', 11, 'upload/Clickboatpublishable.pdf'),
(76, 'GRABage_Publishable.pdf', 9, 'upload/GRABage_Publishable.pdf'),
(77, 'Clickboatpublishable.pdf', 11, 'upload/Clickboatpublishable.pdf'),
(78, 'GRABage_Publishable.pdf', 9, 'upload/GRABage_Publishable.pdf'),
(79, 'Clickboatpublishable.pdf', 11, 'upload/Clickboatpublishable.pdf'),
(80, 'GRABage_Publishable.pdf', 9, 'upload/GRABage_Publishable.pdf'),
(81, 'GRABage_Publishable.pdf', 9, 'upload/GRABage_Publishable.pdf'),
(82, 'GRABage_Publishable.pdf', 9, 'upload/GRABage_Publishable.pdf'),
(83, 'Clickboatpublishable.pdf', 11, 'upload/Clickboatpublishable.pdf'),
(84, 'GRABage_Publishable.pdf', 9, 'upload/GRABage_Publishable.pdf'),
(85, 'GRABage_Publishable.pdf', 9, 'upload/GRABage_Publishable.pdf'),
(86, 'Clickboatpublishable.pdf', 11, 'upload/Clickboatpublishable.pdf'),
(87, 'GRABage_Publishable.pdf', 9, 'upload/GRABage_Publishable.pdf'),
(88, 'PenSys_Publishable Format.pdf', 9, 'upload/PenSys_Publishable Format.pdf'),
(89, 'Clickboatpublishable.pdf', 11, 'upload/Clickboatpublishable.pdf'),
(90, 'GRABage_Publishable.pdf', 9, 'upload/GRABage_Publishable.pdf'),
(91, 'Clickboatpublishable.pdf', 11, 'upload/Clickboatpublishable.pdf'),
(92, 'GRABage_Publishable.pdf', 9, 'upload/GRABage_Publishable.pdf'),
(93, 'PenSys_Publishable Format.pdf', 9, 'upload/PenSys_Publishable Format.pdf'),
(94, 'Clickboatpublishable.pdf', 11, 'upload/Clickboatpublishable.pdf'),
(95, 'Publishable_Format_PantryFinder_Online_Community_Pantry_Mapping_and_Donations_Management_System_Utilizing_GPS_Technology.pdf', 10, 'upload/Publishable_Format_PantryFinder_Online_Community_Pantry_Mapping_and_Donations_Management_System_Utilizing_GPS_Technology.pdf'),
(96, 'GRABage_Publishable.pdf', 9, 'upload/GRABage_Publishable.pdf'),
(97, 'PenSys_Publishable Format.pdf', 9, 'upload/PenSys_Publishable Format.pdf'),
(98, 'Clickboatpublishable.pdf', 11, 'upload/Clickboatpublishable.pdf'),
(99, 'GRABage_Publishable.pdf', 9, 'upload/GRABage_Publishable.pdf'),
(100, 'Clickboatpublishable.pdf', 11, 'upload/Clickboatpublishable.pdf'),
(101, 'Publishable_Format_PantryFinder_Online_Community_Pantry_Mapping_and_Donations_Management_System_Utilizing_GPS_Technology.pdf', 10, 'upload/Publishable_Format_PantryFinder_Online_Community_Pantry_Mapping_and_Donations_Management_System_Utilizing_GPS_Technology.pdf'),
(102, 'GRABage_Publishable.pdf', 9, 'upload/GRABage_Publishable.pdf'),
(103, 'PenSys_Publishable Format.pdf', 9, 'upload/PenSys_Publishable Format.pdf'),
(104, 'Clickboatpublishable.pdf', 11, 'upload/Clickboatpublishable.pdf'),
(105, 'PenSys_Publishable Format.pdf', 9, 'upload/PenSys_Publishable Format.pdf'),
(106, 'Clickboatpublishable.pdf', 11, 'upload/Clickboatpublishable.pdf'),
(107, 'GRABage_Publishable.pdf', 9, 'upload/GRABage_Publishable.pdf'),
(108, 'PenSys_Publishable Format.pdf', 9, 'upload/PenSys_Publishable Format.pdf'),
(109, 'Clickboatpublishable.pdf', 11, 'upload/Clickboatpublishable.pdf'),
(110, 'Publishable_Format_PantryFinder_Online_Community_Pantry_Mapping_and_Donations_Management_System_Utilizing_GPS_Technology.pdf', 10, 'upload/Publishable_Format_PantryFinder_Online_Community_Pantry_Mapping_and_Donations_Management_System_Utilizing_GPS_Technology.pdf'),
(111, 'GRABage_Publishable.pdf', 9, 'upload/GRABage_Publishable.pdf'),
(112, 'PenSys_Publishable Format.pdf', 9, 'upload/PenSys_Publishable Format.pdf'),
(113, 'GRABage_Publishable.pdf', 9, 'upload/GRABage_Publishable.pdf'),
(114, 'PenSys_Publishable Format.pdf', 9, 'upload/PenSys_Publishable Format.pdf'),
(115, 'PenSys_Publishable Format.pdf', 9, 'upload/PenSys_Publishable Format.pdf'),
(116, 'Clickboat Boat repair and rental Management System.pdf', 11, 'upload/Clickboat Boat repair and rental Management System.pdf'),
(117, 'PANTRYFINDER ONLINE COMMUNITY PANTRY MAPPING AND DONATIONS MANAGEMENT SYSTEM UTILIZING GPS TECHNOLOGY.pdf', 10, 'upload/PANTRYFINDER ONLINE COMMUNITY PANTRY MAPPING AND DONATIONS MANAGEMENT SYSTEM UTILIZING GPS TECHNOLOGY.pdf'),
(118, 'PENSYS DIGITALIZED INFORMER AND REGISTRATION SYSTEM FOR SENIOR CITIZEN PENSIONERS.pdf', 9, 'upload/PENSYS DIGITALIZED INFORMER AND REGISTRATION SYSTEM FOR SENIOR CITIZEN PENSIONERS.pdf'),
(119, 'Clickboat Boat repair and rental Management System.pdf', 11, 'upload/Clickboat Boat repair and rental Management System.pdf'),
(120, 'PANTRYFINDER ONLINE COMMUNITY PANTRY MAPPING AND DONATIONS MANAGEMENT SYSTEM UTILIZING GPS TECHNOLOGY.pdf', 10, 'upload/PANTRYFINDER ONLINE COMMUNITY PANTRY MAPPING AND DONATIONS MANAGEMENT SYSTEM UTILIZING GPS TECHNOLOGY.pdf'),
(121, 'PENSYS DIGITALIZED INFORMER AND REGISTRATION SYSTEM FOR SENIOR CITIZEN PENSIONERS.pdf', 9, 'upload/PENSYS DIGITALIZED INFORMER AND REGISTRATION SYSTEM FOR SENIOR CITIZEN PENSIONERS.pdf'),
(122, 'Clickboat Boat repair and rental Management System.pdf', 11, 'upload/Clickboat Boat repair and rental Management System.pdf'),
(123, 'PANTRYFINDER ONLINE COMMUNITY PANTRY MAPPING AND DONATIONS MANAGEMENT SYSTEM UTILIZING GPS TECHNOLOGY.pdf', 10, 'upload/PANTRYFINDER ONLINE COMMUNITY PANTRY MAPPING AND DONATIONS MANAGEMENT SYSTEM UTILIZING GPS TECHNOLOGY.pdf'),
(124, 'Clickboat Boat repair and rental Management System.pdf', 11, 'upload/Clickboat Boat repair and rental Management System.pdf'),
(125, 'PENSYS DIGITALIZED INFORMER AND REGISTRATION SYSTEM FOR SENIOR CITIZEN PENSIONERS.pdf', 9, 'upload/PENSYS DIGITALIZED INFORMER AND REGISTRATION SYSTEM FOR SENIOR CITIZEN PENSIONERS.pdf'),
(126, 'Clickboat Boat repair and rental Management System.pdf', 11, 'upload/Clickboat Boat repair and rental Management System.pdf'),
(127, 'PANTRYFINDER ONLINE COMMUNITY PANTRY MAPPING AND DONATIONS MANAGEMENT SYSTEM UTILIZING GPS TECHNOLOGY.pdf', 10, 'upload/PANTRYFINDER ONLINE COMMUNITY PANTRY MAPPING AND DONATIONS MANAGEMENT SYSTEM UTILIZING GPS TECHNOLOGY.pdf'),
(128, 'Clickboat Boat repair and rental Management System.pdf', 11, 'upload/Clickboat Boat repair and rental Management System.pdf'),
(129, 'PANTRYFINDER ONLINE COMMUNITY PANTRY MAPPING AND DONATIONS MANAGEMENT SYSTEM UTILIZING GPS TECHNOLOGY.pdf', 10, 'upload/PANTRYFINDER ONLINE COMMUNITY PANTRY MAPPING AND DONATIONS MANAGEMENT SYSTEM UTILIZING GPS TECHNOLOGY.pdf');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `flipbooks`
--
ALTER TABLE `flipbooks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pdf_files`
--
ALTER TABLE `pdf_files`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `flipbooks`
--
ALTER TABLE `flipbooks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `pdf_files`
--
ALTER TABLE `pdf_files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=130;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
