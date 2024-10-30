-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 30, 2024 at 01:15 PM
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
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `LastLogin` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`AdminID`, `Username`, `Password`, `Email`, `CreatedAt`, `LastLogin`) VALUES
(1, 'admin', '$2b$12$/qU4nN7Tik3qMX0axK4ooe4G/Wr1ZqWY5WBGtn2D4D5xxGpXU1qkO', 'admin@example.com', '2024-10-07 14:41:52', NULL);

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
(45, 39068488, 39068472, '2024-10-30 14:18:00', 'Confirmed', '2024-10-30 11:16:17');

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
  `ProfileImage` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`DoctorID`, `firstname`, `lastname`, `Specialty`, `Phone`, `Email`, `CreatedAt`, `password`, `status`, `ProfileImage`) VALUES
(12345678, 'ronny', 'omondi', 'neuralogist', '0757680205', 'omoron37@gmail.com', '2024-10-29 06:03:43', '12345678', 'Active', 'download.jpeg'),
(39068472, 'jane', 'gekonyo', 'dentist', NULL, 'jane@gmail.com', '2024-10-29 07:24:15', 'jane', 'Active', ''),
(39068473, 'kevin', 'kamau', 'clinician', NULL, 'kevin@gmail.com', '2024-10-29 07:24:58', 'kevin', 'Active', '');

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
(18, 39068488, 500.00, 'Consultation Fee', 'Paid', NULL, '2024-10-30 07:42:48'),
(19, 39068488, 300.00, 'Consultation Fee', 'Unpaid', NULL, '2024-10-30 11:17:33'),
(20, 39068488, 1500.00, 'Consultation Fee', 'Unpaid', NULL, '2024-10-30 11:31:42');

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
(3, 39068488, 'tooth decay', '2024-10-30 11:42:19', 45),
(4, 39068488, 'typhoid', '2024-10-30 11:45:09', 45);

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
(27, 39068488, 45, 'test for the following:\r\nmalaria\r\ntyphoid\r\ncholera', 'vomiting\r\nfever\r\nheadache', 'typhoid', '2024-10-30 14:17:33', '2024-10-30 11:17:33'),
(28, 39068488, 45, 'tooth screening', 'toothache', 'tooth decay', '2024-10-30 14:31:42', '2024-10-30 11:31:42');

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
  `ProfileImage` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`PatientID`, `firstname`, `lastname`, `Age`, `Gender`, `Phone`, `Email`, `Address`, `CreatedAt`, `Password`, `status`, `ProfileImage`) VALUES
(39068488, 'elvis', 'otieno', 18, 'Male', '0705487509', 'elvis@gmail.com', '232,KOMBEWA', '2024-10-29 07:17:31', 'elvis', 'Active', 'download.jpeg'),
(39068489, 'douglas', 'onyil', 45, 'Male', NULL, 'douglas@gmail.com', NULL, '2024-10-29 07:18:56', 'douglas', 'Active', ''),
(39068491, 'abigael', 'cheptoo', 34, 'Female', NULL, 'cheptoo@gmail.com', NULL, '2024-10-29 07:20:50', 'cheptoo', 'Active', '');

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
(30, 45, '23', 'panadol', 0, 'ghvjh', '2024-10-30 11:26:42', 'completed', 39068488, 27),
(31, 45, '', '', 0, NULL, '2024-10-30 11:31:42', 'active', 39068488, 27);

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
(1, 'finance', '$2b$12$/qU4nN7Tik3qMX0axK4ooe4G/Wr1ZqWY5WBGtn2D4D5xxGpXU1qkO', '2024-10-19 11:20:40', '2024-10-19 11:20:40');

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
  MODIFY `AppointmentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `DoctorID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39068474;

--
-- AUTO_INCREMENT for table `finance`
--
ALTER TABLE `finance`
  MODIFY `FinanceID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `healthrecords`
--
ALTER TABLE `healthrecords`
  MODIFY `RecordID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `laboratory`
--
ALTER TABLE `laboratory`
  MODIFY `LabID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `PatientID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39068493;

--
-- AUTO_INCREMENT for table `pharmacy`
--
ALTER TABLE `pharmacy`
  MODIFY `PharmacyID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `prescriptions`
--
ALTER TABLE `prescriptions`
  MODIFY `PrescriptionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
