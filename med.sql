-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 04, 2024 at 07:32 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `med`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `AdminID` int(11) NOT NULL,
  `Username` varchar(50) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `Phone` int(15) NOT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `LastLogin` timestamp NULL DEFAULT NULL,
  `reset_token` varchar(6) DEFAULT NULL,
  `token_expiration` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`AdminID`, `Username`, `Password`, `Email`, `Phone`, `CreatedAt`, `LastLogin`, `reset_token`, `token_expiration`) VALUES
(1, 'admin', '$2b$12$/qU4nN7Tik3qMX0axK4ooe4G/Wr1ZqWY5WBGtn2D4D5xxGpXU1qkO', 'admin@example.com', 0, '2024-10-07 14:41:52', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `AppointmentID` int(11) NOT NULL,
  `PatientID` int(11) NOT NULL,
  `DoctorID` int(11) NOT NULL,
  `AppointmentDate` datetime NOT NULL,
  `Status` enum('Pending','Confirmed','Cancelled') NOT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`AppointmentID`, `PatientID`, `DoctorID`, `AppointmentDate`, `Status`, `CreatedAt`) VALUES
(54, 39068499, 39068485, '2024-11-02 19:07:00', 'Cancelled', '2024-11-02 16:07:03'),
(56, 39068499, 39068485, '2024-11-03 23:06:00', 'Confirmed', '2024-11-03 19:05:16');

--
-- Triggers `appointments`
--
DELIMITER $$
CREATE TRIGGER `notify_delete_appointment` AFTER DELETE ON `appointments` FOR EACH ROW BEGIN
    INSERT INTO `notification` (`Description`, `DoctorID`, `Timestamp`)
    VALUES ('Appointment has been deleted.', OLD.DoctorID, NOW());
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `notify_new_appointment` AFTER INSERT ON `appointments` FOR EACH ROW BEGIN
    INSERT INTO `notification` (`Description`, `DoctorID`, `Timestamp`)
    VALUES ('New appointment has been added.', NEW.DoctorID, NOW());
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `notify_update_appointment` AFTER UPDATE ON `appointments` FOR EACH ROW BEGIN
    INSERT INTO `notification` (`Description`, `DoctorID`, `Timestamp`)
    VALUES ('Appointment has been updated.', NEW.DoctorID, NOW());
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `DoctorID` int(11) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(250) NOT NULL,
  `Specialty` varchar(100) NOT NULL,
  `Phone` varchar(15) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `password` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `ProfileImage` varchar(255) NOT NULL,
  `reset_token` varchar(10) DEFAULT NULL,
  `token_expiration` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`DoctorID`, `firstname`, `lastname`, `Specialty`, `Phone`, `Email`, `CreatedAt`, `password`, `status`, `ProfileImage`, `reset_token`, `token_expiration`) VALUES
(39068485, 'onyango', 'omondi', 'neuralogist', '0757680205', 'omoron37@gmail.com', '2024-11-02 16:05:35', 'kenya', 'Active', 'download.jpeg', '3894', 2024);

--
-- Triggers `doctors`
--
DELIMITER $$
CREATE TRIGGER `notify_delete_doctor` AFTER DELETE ON `doctors` FOR EACH ROW BEGIN
    INSERT INTO `notification` (`Description`, `DoctorID`, `Timestamp`)
    VALUES (CONCAT('Doctor has been deleted: ', OLD.firstname, ' ', OLD.lastname), OLD.DoctorID, NOW());
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `notify_new_doctor` AFTER INSERT ON `doctors` FOR EACH ROW BEGIN
    INSERT INTO `notification` (`Description`, `DoctorID`, `Timestamp`)
    VALUES (CONCAT('New doctor has been added: ', NEW.firstname, ' ', NEW.lastname), NEW.DoctorID, NOW());
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `notify_update_doctor` AFTER UPDATE ON `doctors` FOR EACH ROW BEGIN
    INSERT INTO `notification` (`Description`, `DoctorID`, `Timestamp`)
    VALUES (CONCAT('Doctor details have been updated for: ', NEW.firstname, ' ', NEW.lastname), NEW.DoctorID, NOW());
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `finance`
--

CREATE TABLE `finance` (
  `FinanceID` int(11) NOT NULL,
  `PatientID` int(11) NOT NULL,
  `Amount` decimal(10,2) NOT NULL,
  `Description` varchar(255) NOT NULL,
  `PaymentStatus` enum('Paid','Unpaid') NOT NULL,
  `PaymentDate` datetime DEFAULT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `finance`
--

INSERT INTO `finance` (`FinanceID`, `PatientID`, `Amount`, `Description`, `PaymentStatus`, `PaymentDate`, `CreatedAt`) VALUES
(22, 39068499, 500.00, 'Consultation Fee', 'Paid', NULL, '2024-11-02 16:07:57');

-- --------------------------------------------------------

--
-- Table structure for table `healthrecords`
--

CREATE TABLE `healthrecords` (
  `RecordID` int(11) NOT NULL,
  `PatientID` int(11) NOT NULL,
  `Description` text NOT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `AppointmentID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `healthrecords`
--

INSERT INTO `healthrecords` (`RecordID`, `PatientID`, `Description`, `CreatedAt`, `AppointmentID`) VALUES
(5, 39068499, 'malaria positive', '2024-11-02 16:08:12', 54),
(6, 39068499, 'malaria positive', '2024-11-04 08:43:59', 54),
(7, 39068499, 'typhoid', '2024-11-04 18:30:25', 54);

-- --------------------------------------------------------

--
-- Table structure for table `laboratory`
--

CREATE TABLE `laboratory` (
  `LabID` int(11) NOT NULL,
  `PatientID` int(11) NOT NULL,
  `AppointmentID` int(11) NOT NULL,
  `TestName` text NOT NULL,
  `Symptoms` text NOT NULL,
  `Result` text DEFAULT NULL,
  `TestDate` datetime NOT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `laboratory`
--

INSERT INTO `laboratory` (`LabID`, `PatientID`, `AppointmentID`, `TestName`, `Symptoms`, `Result`, `TestDate`, `CreatedAt`) VALUES
(30, 39068499, 54, 'malaria', 'malaria', 'typhoid', '2024-11-02 19:07:57', '2024-11-02 16:07:57');

--
-- Triggers `laboratory`
--
DELIMITER $$
CREATE TRIGGER `after_result_update` AFTER UPDATE ON `laboratory` FOR EACH ROW BEGIN
    -- Only insert into healthrecords if the Result column is updated to a non-NULL, non-empty value
    IF NEW.Result IS NOT NULL AND NEW.Result != '' THEN
        INSERT INTO healthrecords (PatientID, AppointmentID, Description, CreatedAt)
        VALUES (NEW.PatientID, (SELECT AppointmentID FROM appointments WHERE PatientID = NEW.PatientID LIMIT 1), NEW.Result, NOW());
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

CREATE TABLE `notification` (
  `ID` int(11) NOT NULL,
  `Description` varchar(255) NOT NULL,
  `DoctorID` int(11) NOT NULL,
  `Timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `notification`
--

INSERT INTO `notification` (`ID`, `Description`, `DoctorID`, `Timestamp`) VALUES
(1, 'Appointment has been updated.', 39068472, '2024-11-02 11:08:20'),
(2, 'New appointment has been added.', 39068473, '2024-11-02 11:08:54'),
(3, 'Doctor details have been updated for: jane gekonyo', 39068472, '2024-11-02 11:11:19'),
(4, 'Doctor details have been updated for: jane gekonyo', 39068472, '2024-11-02 11:12:21'),
(5, 'Appointment has been updated.', 39068472, '2024-11-02 11:20:15'),
(6, 'Appointment has been updated.', 39068472, '2024-11-02 11:20:28'),
(7, 'Appointment has been updated.', 39068472, '2024-11-02 11:22:33'),
(8, 'Appointment has been updated.', 39068472, '2024-11-02 11:22:48'),
(9, 'Doctor details have been updated for: jane gekonyo', 39068472, '2024-11-02 11:22:58'),
(10, 'New appointment has been added.', 39068472, '2024-11-02 11:24:09'),
(11, 'Appointment has been updated.', 39068472, '2024-11-02 11:26:47'),
(12, 'Appointment has been deleted.', 39068472, '2024-11-02 11:29:31'),
(13, 'Appointment has been deleted.', 39068473, '2024-11-02 11:29:33'),
(14, 'Appointment has been deleted.', 12345678, '2024-11-02 11:29:41'),
(15, 'Appointment has been deleted.', 12345678, '2024-11-02 11:29:44'),
(16, 'Appointment has been updated.', 39068472, '2024-11-02 11:38:53'),
(17, 'Appointment has been updated.', 39068472, '2024-11-02 11:38:58'),
(18, 'Appointment has been updated.', 39068472, '2024-11-02 11:47:12'),
(19, 'Appointment has been updated.', 39068472, '2024-11-02 11:50:45'),
(20, 'Appointment has been updated.', 39068472, '2024-11-02 11:51:02'),
(21, 'Appointment has been updated.', 39068472, '2024-11-02 11:57:13'),
(22, 'Appointment has been updated.', 39068472, '2024-11-02 11:57:47'),
(23, 'Appointment has been updated.', 39068472, '2024-11-02 12:06:37'),
(24, 'Appointment has been updated.', 39068472, '2024-11-02 12:09:06'),
(25, 'Appointment has been updated.', 39068472, '2024-11-02 12:09:11'),
(26, 'Appointment has been updated.', 39068472, '2024-11-02 12:11:55'),
(27, 'Appointment has been updated.', 39068472, '2024-11-02 12:12:34'),
(28, 'Appointment has been updated.', 39068472, '2024-11-02 12:13:02'),
(29, 'Appointment has been updated.', 39068472, '2024-11-02 12:19:30'),
(30, 'Appointment has been updated.', 39068472, '2024-11-02 12:25:02'),
(31, 'Appointment has been updated.', 39068472, '2024-11-02 12:25:49'),
(32, 'Appointment has been updated.', 39068472, '2024-11-02 12:34:12'),
(33, 'Appointment has been updated.', 39068472, '2024-11-02 12:35:16'),
(34, 'Appointment has been updated.', 39068472, '2024-11-02 12:41:06'),
(35, 'Appointment has been updated.', 39068472, '2024-11-02 12:54:10'),
(36, 'Doctor details have been updated for: jane gekonyo', 39068472, '2024-11-02 13:27:26'),
(37, 'New doctor has been added: james ', 39068481, '2024-11-02 14:40:18'),
(38, 'Doctor has been deleted: james ', 39068481, '2024-11-02 14:40:27'),
(39, 'Doctor has been deleted: kevin kamau', 39068473, '2024-11-02 14:40:30'),
(40, 'Doctor has been deleted: ronny omondi', 12345678, '2024-11-02 14:40:38'),
(41, 'New doctor has been added: shem ', 39068482, '2024-11-02 14:41:06'),
(42, 'New doctor has been added: hellen ', 39068483, '2024-11-02 14:47:02'),
(43, 'Doctor has been deleted: shem ', 39068482, '2024-11-02 14:49:16'),
(44, 'Doctor has been deleted: hellen ', 39068483, '2024-11-02 14:49:19'),
(45, 'New doctor has been added: Dr. Peter Karanja ', 39068484, '2024-11-02 14:49:26'),
(46, 'Doctor has been deleted: Dr. Peter Karanja ', 39068484, '2024-11-02 14:52:24'),
(47, 'Doctor has been deleted: jane gekonyo', 39068472, '2024-11-02 14:57:57'),
(48, 'New doctor has been added: Dr. Peter Karanja ', 39068485, '2024-11-02 16:05:35'),
(49, 'New appointment has been added.', 39068485, '2024-11-02 16:07:03'),
(50, 'Appointment has been updated.', 39068485, '2024-11-02 16:17:29'),
(51, 'Doctor details have been updated for: james omondi', 39068485, '2024-11-02 16:20:17'),
(52, 'New doctor has been added: ronny omondi ', 39068486, '2024-11-02 16:42:22'),
(53, 'New doctor has been added: ronny omondi ', 39068487, '2024-11-02 16:43:44'),
(54, 'New doctor has been added: ronny omondi ', 39068488, '2024-11-02 16:51:55'),
(55, 'New doctor has been added: ronny omondi ', 39068489, '2024-11-02 16:53:23'),
(56, 'Doctor has been deleted: ronny omondi ', 39068487, '2024-11-02 16:54:56'),
(57, 'Doctor has been deleted: ronny omondi ', 39068488, '2024-11-02 16:54:59'),
(58, 'Doctor has been deleted: ronny omondi ', 39068489, '2024-11-02 16:55:01'),
(59, 'Doctor has been deleted: ronny omondi ', 39068486, '2024-11-02 16:55:03'),
(60, 'New doctor has been added: ronny omondi ', 39068490, '2024-11-02 18:49:53'),
(61, 'New doctor has been added: ronny omondi ', 39068491, '2024-11-02 18:55:41'),
(62, 'Doctor has been deleted: ronny omondi ', 39068491, '2024-11-02 18:57:29'),
(63, 'Doctor has been deleted: ronny omondi ', 39068490, '2024-11-02 18:57:32'),
(64, 'New doctor has been added: ronny omondi ', 39068492, '2024-11-02 18:58:10'),
(65, 'Doctor has been deleted: ronny omondi ', 39068492, '2024-11-02 19:02:36'),
(66, 'New doctor has been added: ronny omondi ', 39068493, '2024-11-02 19:06:07'),
(67, 'Doctor has been deleted: ronny omondi ', 39068493, '2024-11-02 19:07:13'),
(68, 'Doctor details have been updated for: james omondi', 39068485, '2024-11-02 19:24:02'),
(69, 'Appointment has been updated.', 39068485, '2024-11-03 08:00:39'),
(70, 'Appointment has been updated.', 39068485, '2024-11-03 08:01:40'),
(71, 'Appointment has been updated.', 39068485, '2024-11-03 08:09:00'),
(72, 'Appointment has been updated.', 39068485, '2024-11-03 08:09:12'),
(73, 'Appointment has been updated.', 39068485, '2024-11-03 08:10:35'),
(74, 'Doctor details have been updated for: james omondi', 39068485, '2024-11-03 08:59:30'),
(75, 'Doctor details have been updated for: james omondi', 39068485, '2024-11-03 08:59:35'),
(76, 'Doctor details have been updated for: james omondi', 39068485, '2024-11-03 09:01:03'),
(77, 'Doctor details have been updated for: james omondi', 39068485, '2024-11-03 09:01:08'),
(78, 'Doctor details have been updated for: james omondi', 39068485, '2024-11-03 12:54:52'),
(79, 'Doctor details have been updated for: james omondi', 39068485, '2024-11-03 13:04:40'),
(80, 'Doctor details have been updated for: james omondi', 39068485, '2024-11-03 13:05:07'),
(81, 'Doctor details have been updated for: james omondi', 39068485, '2024-11-03 13:09:33'),
(82, 'Doctor details have been updated for: james omondi', 39068485, '2024-11-03 13:14:30'),
(83, 'Doctor details have been updated for: james omondi', 39068485, '2024-11-03 13:18:02'),
(84, 'Doctor details have been updated for: james omondi', 39068485, '2024-11-03 13:24:36'),
(85, 'Doctor details have been updated for: james omondi', 39068485, '2024-11-03 13:28:44'),
(86, 'Doctor details have been updated for: james omondi', 39068485, '2024-11-03 13:31:15'),
(87, 'Doctor details have been updated for: james omondi', 39068485, '2024-11-03 13:39:58'),
(88, 'Doctor details have been updated for: james omondi', 39068485, '2024-11-03 13:54:01'),
(89, 'Doctor details have been updated for: james omondi', 39068485, '2024-11-03 13:55:44'),
(90, 'Doctor details have been updated for: james omondi', 39068485, '2024-11-03 13:56:09'),
(91, 'Doctor details have been updated for: james omondi', 39068485, '2024-11-03 14:01:24'),
(92, 'Doctor details have been updated for: james omondi', 39068485, '2024-11-03 14:02:13'),
(93, 'Doctor details have been updated for: james omondi', 39068485, '2024-11-03 14:02:19'),
(94, 'Doctor details have been updated for: james omondi', 39068485, '2024-11-03 14:02:54'),
(95, 'Doctor details have been updated for: james omondi', 39068485, '2024-11-03 14:02:59'),
(96, 'Doctor details have been updated for: james omondi', 39068485, '2024-11-03 14:03:50'),
(97, 'Doctor details have been updated for: james omondi', 39068485, '2024-11-03 14:04:20'),
(98, 'Doctor details have been updated for: james omondi', 39068485, '2024-11-03 14:04:35'),
(99, 'Doctor details have been updated for: james omondi', 39068485, '2024-11-03 14:04:43'),
(100, 'Doctor details have been updated for: james omondi', 39068485, '2024-11-03 14:11:47'),
(101, 'Doctor details have been updated for: james omondi', 39068485, '2024-11-03 14:34:19'),
(102, 'Doctor details have been updated for: james omondi', 39068485, '2024-11-03 14:34:48'),
(103, 'Doctor details have been updated for: james omondi', 39068485, '2024-11-03 14:37:48'),
(104, 'Doctor details have been updated for: james omondi', 39068485, '2024-11-03 14:40:17'),
(105, 'Doctor details have been updated for: james omondi', 39068485, '2024-11-03 14:42:21'),
(106, 'Doctor details have been updated for: james omondi', 39068485, '2024-11-03 14:43:37'),
(107, 'Doctor details have been updated for: james omondi', 39068485, '2024-11-03 14:48:49'),
(108, 'Doctor details have been updated for: james omondi', 39068485, '2024-11-03 14:49:45'),
(109, 'Doctor details have been updated for: james omondi', 39068485, '2024-11-03 14:51:55'),
(110, 'Doctor details have been updated for: james omondi', 39068485, '2024-11-03 15:25:49'),
(111, 'Doctor details have been updated for: james omondi', 39068485, '2024-11-03 15:44:20'),
(112, 'Doctor details have been updated for: james omondi', 39068485, '2024-11-03 15:45:47'),
(113, 'Doctor details have been updated for: james omondi', 39068485, '2024-11-03 15:45:54'),
(114, 'Doctor details have been updated for: james omondi', 39068485, '2024-11-03 15:47:08'),
(115, 'Doctor details have been updated for: james omondi', 39068485, '2024-11-03 15:49:13'),
(116, 'Doctor details have been updated for: james omondi', 39068485, '2024-11-03 15:50:56'),
(117, 'New doctor has been added: hellen ', 39068494, '2024-11-03 15:56:06'),
(118, 'Doctor has been deleted: hellen ', 39068494, '2024-11-03 16:09:07'),
(119, 'New appointment has been added.', 39068485, '2024-11-03 16:15:57'),
(120, 'Doctor details have been updated for: james omondi', 39068485, '2024-11-03 16:19:15'),
(121, 'Appointment has been updated.', 39068485, '2024-11-03 16:19:48'),
(122, 'Doctor details have been updated for: james omondi', 39068485, '2024-11-03 18:23:36'),
(123, 'Doctor details have been updated for: james omondi', 39068485, '2024-11-03 18:23:50'),
(124, 'Doctor details have been updated for: james omondi', 39068485, '2024-11-03 18:24:08'),
(125, 'Appointment has been updated.', 39068485, '2024-11-03 18:50:05'),
(126, 'Appointment has been updated.', 39068485, '2024-11-03 18:50:13'),
(127, 'Appointment has been updated.', 39068485, '2024-11-03 18:59:03'),
(128, 'Appointment has been updated.', 39068485, '2024-11-03 18:59:16'),
(129, 'New appointment has been added.', 39068485, '2024-11-03 19:05:16'),
(130, 'Doctor details have been updated for: elvis omondi', 39068485, '2024-11-04 12:30:28'),
(131, 'Doctor details have been updated for: elvis omondi', 39068485, '2024-11-04 12:30:38'),
(132, 'Doctor details have been updated for: elvis omondi', 39068485, '2024-11-04 12:30:44'),
(133, 'Doctor details have been updated for: onyango omondi', 39068485, '2024-11-04 12:30:55'),
(134, 'Doctor details have been updated for: onyango omondi', 39068485, '2024-11-04 12:34:26'),
(135, 'Doctor details have been updated for: onyango omondi', 39068485, '2024-11-04 12:34:32'),
(136, 'Appointment has been updated.', 39068485, '2024-11-04 12:46:51'),
(137, 'Doctor details have been updated for: onyango omondi', 39068485, '2024-11-04 12:49:51'),
(138, 'Doctor details have been updated for: onyango omondi', 39068485, '2024-11-04 12:49:56'),
(139, 'Doctor details have been updated for: onyango omondi', 39068485, '2024-11-04 12:56:42'),
(140, 'Doctor details have been updated for: onyango omondi', 39068485, '2024-11-04 12:56:46'),
(141, 'Doctor details have been updated for: onyango omondi', 39068485, '2024-11-04 12:58:46'),
(142, 'Doctor details have been updated for: onyango omondi', 39068485, '2024-11-04 12:59:12'),
(143, 'Appointment has been deleted.', 39068485, '2024-11-04 13:00:53'),
(144, 'Appointment has been updated.', 39068485, '2024-11-04 13:00:59'),
(145, 'Appointment has been updated.', 39068485, '2024-11-04 13:36:09');

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `PatientID` int(11) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `Age` int(11) NOT NULL,
  `Gender` enum('Male','Female','Other') NOT NULL,
  `Phone` varchar(15) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `Address` varchar(255) DEFAULT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `Password` varchar(255) NOT NULL,
  `status` varchar(250) NOT NULL,
  `ProfileImage` varchar(250) NOT NULL,
  `reset_token` varchar(6) DEFAULT NULL,
  `token_expiration` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`PatientID`, `firstname`, `lastname`, `Age`, `Gender`, `Phone`, `Email`, `Address`, `CreatedAt`, `Password`, `status`, `ProfileImage`, `reset_token`, `token_expiration`) VALUES
(39068499, 'james', '', 22, 'Male', NULL, 'omoron37@gmail.coM', '232,KOMBEWA', '2024-11-02 15:49:16', 'patient', 'Active', 'download.jpeg', 'P7ugrM', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `pharmacy`
--

CREATE TABLE `pharmacy` (
  `PharmacyID` int(11) NOT NULL,
  `Medication` varchar(255) NOT NULL,
  `Quantity` int(11) NOT NULL,
  `Price` decimal(10,2) NOT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `prescriptions`
--

CREATE TABLE `prescriptions` (
  `PrescriptionID` int(11) NOT NULL,
  `AppointmentID` int(11) NOT NULL,
  `Medication` varchar(255) NOT NULL,
  `Dosage` varchar(100) NOT NULL,
  `RefillsRemaining` int(11) DEFAULT 0,
  `Instructions` text DEFAULT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `Status` enum('active','completed','expired') DEFAULT 'active',
  `patientID` int(255) NOT NULL,
  `LabID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `prescriptions`
--

INSERT INTO `prescriptions` (`PrescriptionID`, `AppointmentID`, `Medication`, `Dosage`, `RefillsRemaining`, `Instructions`, `CreatedAt`, `Status`, `patientID`, `LabID`) VALUES
(33, 54, '12', 'paracetamol', 0, 'take daily after every meal', '2024-11-03 19:03:57', 'completed', 39068499, 30);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `e_medicine_name` varchar(255) NOT NULL,
  `hero_image` varchar(255) NOT NULL,
  `consultation_image` varchar(255) NOT NULL,
  `pharmacy_image` varchar(255) NOT NULL,
  `healthcare_image` varchar(255) NOT NULL,
  `about_image` varchar(255) NOT NULL,
  `testimonial_image` varchar(255) NOT NULL,
  `hero_text_1` varchar(255) NOT NULL,
  `hero_text_2` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `e_medicine_name`, `hero_image`, `consultation_image`, `pharmacy_image`, `healthcare_image`, `about_image`, `testimonial_image`, `hero_text_1`, `hero_text_2`) VALUES
(1, 'E-Medicine', 'resources/images/doc.jpg', 'resources/images/consult.jpg', 'resources/images/pharmacy.jpg', 'resources/images/medicalreport.jpg', 'resources/images/about.jpg', 'resources/images/testimonial.jpg', 'Your Health, Our Priority', 'Connect with healthcare professionals instantly.');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `created_at`, `updated_at`) VALUES
(1, 'finance', '$2b$12$/qU4nN7Tik3qMX0axK4ooe4G/Wr1ZqWY5WBGtn2D4D5xxGpXU1qkO', '2024-10-19 11:20:40', '2024-10-19 11:20:40'),
(2, 'lab', '$2b$12$/qU4nN7Tik3qMX0axK4ooe4G/Wr1ZqWY5WBGtn2D4D5xxGpXU1qkO', '2024-11-04 18:22:40', '2024-11-04 18:23:08');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`AdminID`),
  ADD UNIQUE KEY `Username` (`Username`);

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`AppointmentID`),
  ADD KEY `PatientID` (`PatientID`),
  ADD KEY `DoctorID` (`DoctorID`);

--
-- Indexes for table `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`DoctorID`);

--
-- Indexes for table `finance`
--
ALTER TABLE `finance`
  ADD PRIMARY KEY (`FinanceID`),
  ADD KEY `PatientID` (`PatientID`);

--
-- Indexes for table `healthrecords`
--
ALTER TABLE `healthrecords`
  ADD PRIMARY KEY (`RecordID`),
  ADD KEY `PatientID` (`PatientID`),
  ADD KEY `fk_healthrecords_appointments` (`AppointmentID`);

--
-- Indexes for table `laboratory`
--
ALTER TABLE `laboratory`
  ADD PRIMARY KEY (`LabID`),
  ADD KEY `PatientID` (`PatientID`),
  ADD KEY `fk_laboratory_appointment` (`AppointmentID`);

--
-- Indexes for table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`PatientID`);

--
-- Indexes for table `pharmacy`
--
ALTER TABLE `pharmacy`
  ADD PRIMARY KEY (`PharmacyID`);

--
-- Indexes for table `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD PRIMARY KEY (`PrescriptionID`),
  ADD KEY `AppointmentID` (`AppointmentID`),
  ADD KEY `fk_patient_prescription` (`patientID`),
  ADD KEY `fk_lab` (`LabID`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `AdminID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `AppointmentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `DoctorID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39068495;

--
-- AUTO_INCREMENT for table `finance`
--
ALTER TABLE `finance`
  MODIFY `FinanceID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `healthrecords`
--
ALTER TABLE `healthrecords`
  MODIFY `RecordID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `laboratory`
--
ALTER TABLE `laboratory`
  MODIFY `LabID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `notification`
--
ALTER TABLE `notification`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=146;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `PatientID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39068512;

--
-- AUTO_INCREMENT for table `pharmacy`
--
ALTER TABLE `pharmacy`
  MODIFY `PharmacyID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `prescriptions`
--
ALTER TABLE `prescriptions`
  MODIFY `PrescriptionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`PatientID`) REFERENCES `patients` (`PatientID`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`DoctorID`) REFERENCES `doctors` (`DoctorID`) ON DELETE CASCADE;

--
-- Constraints for table `finance`
--
ALTER TABLE `finance`
  ADD CONSTRAINT `finance_ibfk_1` FOREIGN KEY (`PatientID`) REFERENCES `patients` (`PatientID`) ON DELETE CASCADE;

--
-- Constraints for table `healthrecords`
--
ALTER TABLE `healthrecords`
  ADD CONSTRAINT `fk_healthrecords_appointments` FOREIGN KEY (`AppointmentID`) REFERENCES `appointments` (`AppointmentID`) ON DELETE CASCADE,
  ADD CONSTRAINT `healthrecords_ibfk_1` FOREIGN KEY (`PatientID`) REFERENCES `patients` (`PatientID`) ON DELETE CASCADE;

--
-- Constraints for table `laboratory`
--
ALTER TABLE `laboratory`
  ADD CONSTRAINT `fk_laboratory_appointment` FOREIGN KEY (`AppointmentID`) REFERENCES `appointments` (`AppointmentID`),
  ADD CONSTRAINT `laboratory_ibfk_1` FOREIGN KEY (`PatientID`) REFERENCES `patients` (`PatientID`) ON DELETE CASCADE;

--
-- Constraints for table `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD CONSTRAINT `fk_lab` FOREIGN KEY (`LabID`) REFERENCES `laboratory` (`LabID`),
  ADD CONSTRAINT `fk_patient_prescription` FOREIGN KEY (`patientID`) REFERENCES `patients` (`PatientID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `prescriptions_ibfk_1` FOREIGN KEY (`AppointmentID`) REFERENCES `appointments` (`AppointmentID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
