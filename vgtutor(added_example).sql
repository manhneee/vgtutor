-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 16, 2025 at 09:54 AM
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
-- Database: `vgtutor`
--

-- --------------------------------------------------------

--
-- Table structure for table `account`
--

CREATE TABLE `account` (
  `userid` int(11) NOT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `account`
--

INSERT INTO `account` (`userid`, `password`) VALUES
(100, '$2y$10$2iDmLac/gUdQF2rpxqB1VupzOVv5.ECG0g4OVkdn.HXRGSeiZOpzS'),
(200, '$2y$10$2iDmLac/gUdQF2rpxqB1VupzOVv5.ECG0g4OVkdn.HXRGSeiZOpzS'),
(99999, '$2y$10$2iDmLac/gUdQF2rpxqB1VupzOVv5.ECG0g4OVkdn.HXRGSeiZOpzS');
-- Password: 12345
-- --------------------------------------------------------

--
-- Table structure for table `admin_account`
--

CREATE TABLE `admin_account` (
  `adminid` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_account`
--

INSERT INTO `admin_account` (`adminid`, `name`, `phone_number`) VALUES
(99999, 'Admin', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `course`
--

CREATE TABLE `course` (
  `courseid` int(11) NOT NULL,
  `course_name` varchar(50) DEFAULT NULL,
  `major` varchar(3) DEFAULT NULL,
  `semester` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course`
--

INSERT INTO `course` (`courseid`, `course_name`, `major`, `semester`) VALUES
(1, 'Database', 'CSE', 3);

-- --------------------------------------------------------

--
-- Table structure for table `course_offering`
--

CREATE TABLE `course_offering` (
  `tutorid` int(11) NOT NULL,
  `courseid` int(11) NOT NULL,
  `tutor_grade` varchar(3) DEFAULT NULL,
  `rating` varchar(3) DEFAULT NULL,
  `price` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course_offering`
--

INSERT INTO `course_offering` (`tutorid`, `courseid`, `tutor_grade`, `rating`, `price`) VALUES
(100, 1, '1.3', '5.0', 20);

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

CREATE TABLE `review` (
  `studentid` int(11) NOT NULL,
  `tutorid` int(11) NOT NULL,
  `courseid` int(11) NOT NULL,
  `rating` varchar(3) DEFAULT NULL,
  `review` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `session`
--

CREATE TABLE `session` (
  `studentid` int(11) NOT NULL,
  `tutorid` int(11) NOT NULL,
  `courseid` int(11) NOT NULL,
  `date_and_time` datetime NOT NULL,
  `duration` float DEFAULT NULL,
  `paid` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_account`
--

CREATE TABLE `student_account` (
  `accountid` int(11) NOT NULL,
  `email` varchar(50) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `major` varchar(3) DEFAULT NULL,
  `studentid` int(11) DEFAULT NULL,
  `intake` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_account`
--

INSERT INTO `student_account` (`accountid`, `email`, `name`, `major`, `studentid`, `intake`) VALUES
(100, '100@student.vgu.edu.vn', 'Manh', 'CSE', 100, 2022),
(200, '200@student.vgu.edu.vn', 'Manh Student', 'CSE', 200, 2022);

-- --------------------------------------------------------

--
-- Table structure for table `tutor_account`
--

CREATE TABLE `tutor_account` (
  `accountid` int(11) NOT NULL,
  `bank_name` varchar(50) DEFAULT NULL,
  `bank_acc_no` varchar(50) DEFAULT NULL,
  `gpa` varchar(3) DEFAULT NULL,
  `description` varchar(200) DEFAULT NULL,
  `overall_rating` varchar(3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tutor_account`
--

INSERT INTO `tutor_account` (`accountid`, `bank_name`, `bank_acc_no`, `gpa`, `description`, `overall_rating`) VALUES
(100, 'MB Bank', '99999999999', '1.0', 'None', '5');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `account`
--
ALTER TABLE `account`
  ADD PRIMARY KEY (`userid`);

--
-- Indexes for table `admin_account`
--
ALTER TABLE `admin_account`
  ADD PRIMARY KEY (`adminid`);

--
-- Indexes for table `course`
--
ALTER TABLE `course`
  ADD PRIMARY KEY (`courseid`);

--
-- Indexes for table `course_offering`
--
ALTER TABLE `course_offering`
  ADD PRIMARY KEY (`tutorid`,`courseid`),
  ADD KEY `courseid` (`courseid`);

--
-- Indexes for table `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`studentid`,`tutorid`,`courseid`),
  ADD KEY `tutorid` (`tutorid`,`courseid`);

--
-- Indexes for table `session`
--
ALTER TABLE `session`
  ADD PRIMARY KEY (`studentid`,`tutorid`,`courseid`,`date_and_time`),
  ADD KEY `tutorid` (`tutorid`,`courseid`);

--
-- Indexes for table `student_account`
--
ALTER TABLE `student_account`
  ADD PRIMARY KEY (`accountid`),
  ADD UNIQUE KEY `studentid` (`studentid`);

--
-- Indexes for table `tutor_account`
--
ALTER TABLE `tutor_account`
  ADD PRIMARY KEY (`accountid`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_account`
--
ALTER TABLE `admin_account`
  ADD CONSTRAINT `admin_account_ibfk_1` FOREIGN KEY (`adminid`) REFERENCES `account` (`userid`);

--
-- Constraints for table `course_offering`
--
ALTER TABLE `course_offering`
  ADD CONSTRAINT `course_offering_ibfk_1` FOREIGN KEY (`tutorid`) REFERENCES `tutor_account` (`accountid`),
  ADD CONSTRAINT `course_offering_ibfk_2` FOREIGN KEY (`courseid`) REFERENCES `course` (`courseid`);

--
-- Constraints for table `review`
--
ALTER TABLE `review`
  ADD CONSTRAINT `review_ibfk_1` FOREIGN KEY (`tutorid`,`courseid`) REFERENCES `course_offering` (`tutorid`, `courseid`),
  ADD CONSTRAINT `review_ibfk_2` FOREIGN KEY (`studentid`) REFERENCES `student_account` (`accountid`);

--
-- Constraints for table `session`
--
ALTER TABLE `session`
  ADD CONSTRAINT `session_ibfk_1` FOREIGN KEY (`tutorid`,`courseid`) REFERENCES `course_offering` (`tutorid`, `courseid`),
  ADD CONSTRAINT `session_ibfk_2` FOREIGN KEY (`studentid`) REFERENCES `student_account` (`accountid`);

--
-- Constraints for table `student_account`
--
ALTER TABLE `student_account`
  ADD CONSTRAINT `student_account_ibfk_1` FOREIGN KEY (`accountid`) REFERENCES `account` (`userid`);

--
-- Constraints for table `tutor_account`
--
ALTER TABLE `tutor_account`
  ADD CONSTRAINT `tutor_account_ibfk_1` FOREIGN KEY (`accountid`) REFERENCES `student_account` (`accountid`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
