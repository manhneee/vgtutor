-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 17, 2025 at 06:57 PM
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

CREATE TABLE account (
  userid INT(11) NOT NULL,
  email VARCHAR(255) NOT NULL,
  password VARCHAR(255) DEFAULT NULL,
  is_verified TINYINT(1) DEFAULT 0,
  verify_token VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (userid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
--
-- Dumping data for table `account`
--

INSERT INTO `account` (`userid`, `password`) VALUES
(10223011, '$2y$10$DB0A5V4M84iDB.fSy5QwDOaLRBrU7UWLh0VO5FEKwgttnOCR8Apse'),
(10422044, '$2y$10$m8OVnxpr2lmakvxZBi17Z..EyvSDwYT2h4vwW5Uw7NHWlv9p30oDW'),
(10422047, '$2y$10$m8OVnxpr2lmakvxZBi17Z..EyvSDwYT2h4vwW5Uw7NHWlv9p30oDW'),
(10822002, '$2y$10$FRxo4E/R066dn1P8MskSMOKrBQWy2ujtiLtA77sBl/0igUA5x8p5i');

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
(10422044, 'Bao Long', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `course`
--

CREATE TABLE `course` (
  `courseid` int(11) NOT NULL,
  `course_name` varchar(50) DEFAULT NULL,
  `major` varchar(3) DEFAULT NULL,
  `semester` int(11) DEFAULT NULL,
  `cond` varchar(1000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course`
--

INSERT INTO `course` (`courseid`, `course_name`, `major`, `semester`, `cond`) VALUES
(1229, 'Database', 'CSE', 5, 'Grade over 1.7'),
(5089, 'Discrete Math', 'CSE', 4, 'Strong Foundation in Math: Solid understanding of topics like logic, set theory, combinatorics, graph theory, and number theory.\r\n\r\nEducational Background: Currently studying or graduated in Mathematics, Computer Science, or a related field.\r\n\r\nAcademic Performance: Achieved a grade higher than 1.7 (on the German GPA scale or equivalent) in Discrete Mathematics.\r\n\r\nTeaching Skills: Able to explain abstract concepts clearly, using examples and step-by-step reasoning.\r\n\r\nExperience: Prior tutoring, teaching, or TA experience is a plus.\r\n\r\nCommunication: Strong verbal and written communication skills, adaptable to different learning styles.');

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
(10422047, 1229, '1.3', '1.0', 200000),
(10422047, 5089, '1.3', NULL, 300000),
(10822002, 1229, '1.7', NULL, 123456789),
(10822002, 5089, '1.7', NULL, 100000);

-- --------------------------------------------------------


-- --------------------------------------------------------

--
-- Table structure for table `payment_confirmation`
--

CREATE TABLE `payment_confirmation` (
  `id` int(11) NOT NULL,
  `studentid` varchar(50) DEFAULT NULL,
  `tutorid` varchar(50) DEFAULT NULL,
  `courseid` varchar(50) DEFAULT NULL,
  `date_and_time` datetime DEFAULT NULL,
  `img_path` varchar(255) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_confirmation`
--

INSERT INTO `payment_confirmation` (`id`, `studentid`, `tutorid`, `courseid`, `date_and_time`, `img_path`, `status`) VALUES
(15, '10422047', '10822002', '1229', '2025-06-17 15:00:00', 'uploads/pay_10422047_1750152540.jpg', 'accepted'),
(16, '10223011', '10422047', '1229', '2025-06-18 18:14:00', 'uploads/pay_10223011_1750166486.jpg', 'accepted'),
(17, '10223011', '10822002', '1229', '2025-06-17 15:16:00', 'uploads/pay_10223011_1750166703.jpg', 'denied'),
(18, '10422047', '10822002', '1229', '2025-06-17 20:36:00', 'uploads/pay_10422047_1750167511.jpg', 'denied'),
(19, '10422047', '10822002', '1229', '2025-06-17 20:36:00', 'uploads/pay_10422047_1750167820.jpg', 'denied'),
(20, '10422047', '10822002', '1229', '2025-06-17 20:36:00', 'uploads/pay_10422047_1750172571.jpg', 'accepted'),
(21, '10223011', '10822002', '1229', '2025-06-17 15:16:00', 'uploads/pay_10223011_1750172734.jpg', 'denied');

-- --------------------------------------------------------

--
-- Table structure for table `pending_offering`
--

CREATE TABLE `pending_offering` (
  `tutorid` int(11) NOT NULL,
  `courseid` int(11) NOT NULL,
  `status` varchar(20) DEFAULT NULL,
  `grade` varchar(3) DEFAULT NULL,
  `price` int(11) DEFAULT NULL,
  `self_description` varchar(1000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pending_offering`
--

INSERT INTO `pending_offering` (`tutorid`, `courseid`, `status`, `grade`, `price`, `self_description`) VALUES
(10422047, 1229, 'permitted', '1.3', 200000, 'Hello'),
(10422047, 5089, 'permitted', '1.3', 300000, 'Hello'),
(10822002, 1229, 'permitted', '1.7', 123456789, 'fdhmfghjnfgb'),
(10822002, 5089, 'permitted', '1.7', 100000, 'Hi, I strongly understand most about discrete math');

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

CREATE TABLE `review` (
  `studentid` int(11) NOT NULL,
  `tutorid` int(11) NOT NULL,
  `courseid` int(11) NOT NULL,
  `rating` varchar(3) DEFAULT NULL,
  `review` varchar(200) DEFAULT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `review`
--

INSERT INTO `review` (`studentid`, `tutorid`, `courseid`, `rating`, `review`, `date`) VALUES
(10422047, 10822002, 1229, '4', 'good', '2025-06-17 19:57:16');

-- --------------------------------------------------------




-
-- Table structure for table `session`
--

CREATE TABLE session (
  studentid INT(11) NOT NULL,
  tutorid INT(11) NOT NULL,
  courseid INT(11) NOT NULL,
  date_and_time DATETIME NOT NULL,
  duration FLOAT DEFAULT NULL,
  paid TINYINT(1) DEFAULT NULL,
  consensus VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'pending',
  notified TINYINT(1) DEFAULT 0,
  place VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'online',
  PRIMARY KEY (studentid, tutorid, courseid, date_and_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
--
-- Dumping data for table `session`
--

INSERT INTO `session` (`studentid`, `tutorid`, `courseid`, `date_and_time`, `duration`, `paid`, `consensus`, `notified`, `place`) VALUES
(10223011, 10422047, 1229, '2025-06-17 17:17:00', 4, 0, 'denied', 1, 'Library'),
(10223011, 10422047, 1229, '2025-06-18 18:14:00', 2, 1, 'accepted', 1, 'Lib'),
(10223011, 10822002, 1229, '2025-06-17 15:16:00', 2, 0, 'accepted', 1, 'Lecture Hall'),
(10422047, 10822002, 1229, '2025-06-17 15:00:00', 0.5, 1, 'accepted', 1, 'Lib'),
(10422047, 10822002, 1229, '2025-06-17 20:36:00', 1, 1, 'accepted', 1, 'Library'),
(10422047, 10822002, 5089, '2025-06-11 05:25:16', 4, 0, 'denied', 1, 'online'),
(10822002, 10422047, 1229, '2025-06-19 17:34:00', 3, 0, 'pending', 1, 'Lecture Hall'),
(10822002, 10422047, 5089, '2025-06-13 08:48:11', 4, 0, 'pending', 1, 'online');

-- --------------------------------------------------------

--
-- Table structure for table `student_account`
--

CREATE TABLE `student_account` (
  `accountid` int(11) NOT NULL,
  `email` varchar(50) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `major` varchar(3) DEFAULT NULL,
  `intake` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_account`
--

INSERT INTO `student_account` (`accountid`, `email`, `name`, `major`, `intake`) VALUES
(10223011, '10223011@student.vgu.edu.vn', 'Nguyen Hoang Trieu', 'ECE', 2023),
(10422047, '10422047@student.vgu.edu.vn', 'Pham Duc Manh', 'MEN', 2023),
(10822002, '10822002@student.vgu.edu.vn', 'Tran Nguyen Phan Minh Anh', 'ARC', 2022);

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
(10422047, 'Techcombank', '999999999999999', '1.3', 'Creative and Motivative', NULL),
(10822002, 'Vietcombank', '0000000888888', '1.0', 'Hi, I want to be a good tutor, I have teaching experience', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tutor_registration`
--

CREATE TABLE `tutor_registration` (
  `studentid` int(11) NOT NULL,
  `status` varchar(20) DEFAULT NULL,
  `gpa` varchar(3) DEFAULT NULL,
  `bank_name` varchar(50) DEFAULT NULL,
  `bank_acc_no` varchar(50) DEFAULT NULL,
  `self_description` varchar(1000) DEFAULT NULL,
  `denied_at` datetime DEFAULT NULL,
  `transcript_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tutor_registration`
--

INSERT INTO `tutor_registration` (`studentid`, `status`, `gpa`, `bank_name`, `bank_acc_no`, `self_description`, `denied_at`, `transcript_path`) VALUES
(10223011, 'permitted', '1', '12312312312', '123123', 'coin card', NULL, 'uploads/transcript_10223011_1750143620.pdf');

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
CREATE TABLE `error_reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datetime` datetime DEFAULT NULL,
  `user` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `subject` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_general_ci,
  `status` varchar(20) COLLATE utf8mb4_general_ci DEFAULT 'not yet',
  `source` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `error_report_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `report_id` int(11) NOT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `report_id` (`report_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_general_ci NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `token` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE `notifications` (
    id INT(11) NOT NULL AUTO_INCREMENT,
    title VARCHAR(255) DEFAULT NULL,
    user_id_send INT(11) DEFAULT NULL,
    user_id_receive INT(11) NOT NULL,
    type VARCHAR(50) DEFAULT NULL,
    message VARCHAR(255) NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
--


--
-- Indexes for table `payment_confirmation`
--
ALTER TABLE `payment_confirmation`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pending_offering`
--
ALTER TABLE `pending_offering`
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
  ADD PRIMARY KEY (`accountid`);

--
-- Indexes for table `tutor_account`
--
ALTER TABLE `tutor_account`
  ADD PRIMARY KEY (`accountid`);

--
-- Indexes for table `tutor_registration`
--
ALTER TABLE `tutor_registration`
  ADD PRIMARY KEY (`studentid`);

--
-- AUTO_INCREMENT for dumped tables
--

--


--
-- AUTO_INCREMENT for table `payment_confirmation`
--
ALTER TABLE `payment_confirmation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

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
-- Constraints for table `pending_offering`
--
ALTER TABLE `pending_offering`
  ADD CONSTRAINT `pending_offering_ibfk_1` FOREIGN KEY (`tutorid`) REFERENCES `tutor_account` (`accountid`),
  ADD CONSTRAINT `pending_offering_ibfk_2` FOREIGN KEY (`courseid`) REFERENCES `course` (`courseid`);

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

--
-- Constraints for table `tutor_registration`
--
ALTER TABLE `tutor_registration`
  ADD CONSTRAINT `tutor_registration_ibfk_1` FOREIGN KEY (`studentid`) REFERENCES `student_account` (`accountid`);

DELIMITER $$
--
-- Events
--
CREATE DEFINER=`root`@`localhost` EVENT `delete_old_denied_tutors` ON SCHEDULE EVERY 1 SECOND STARTS '2025-06-02 12:03:18' ON COMPLETION NOT PRESERVE ENABLE DO DELETE FROM tutor_registration
  WHERE status = 'denied'
    AND denied_at IS NOT NULL
    AND denied_at < (NOW() - INTERVAL 3 DAY)$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;