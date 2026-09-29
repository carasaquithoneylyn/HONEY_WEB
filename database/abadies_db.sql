-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 21, 2026 at 07:55 AM
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
-- Database: `abadies_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `alumni_details`
--

CREATE TABLE `alumni_details` (
  `AlumniID` int(11) NOT NULL,
  `InstitutionName` varchar(255) NOT NULL,
  `Degree` varchar(100) NOT NULL,
  `FieldOfStudy` varchar(100) DEFAULT NULL,
  `StartDate` date DEFAULT NULL,
  `EndDate` date DEFAULT NULL,
  `logo` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `S_ID` int(11) DEFAULT NULL,
  `AddedBy` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `alumni_details`
--

INSERT INTO `alumni_details` (`AlumniID`, `InstitutionName`, `Degree`, `FieldOfStudy`, `StartDate`, `EndDate`, `logo`, `description`, `S_ID`, `AddedBy`) VALUES
(6, 'PIES DIGITAL', 'MIT', 'SYSTEM DEVELOPMENT', '2024-06-26', '2024-06-25', 'image/csr-scc.png', 'Avtech Solution', 1, 1),
(7, 'ABADIES SOLUTIONS', 'BSED-MATH', 'Research', '1988-09-23', '2024-06-25', 'image/ejb.png', 'Research is defined as the creation of new knowledge and/or the use of existing knowledge in a new and creative way so as to generate new concepts, methodologies and understandings. This could include synthesis and analysis of previous research to the extent that it leads to new and creative outcomes.', 5, 1),
(8, 'Diocese of San Carlos', 'DOSC', 'Church System', '2022-01-01', '2024-06-25', 'image/1.png', 'Smart Diocese App', 6, 1),
(10, 'Tanon State', 'MIT', 'Church System', '2025-01-30', '2025-01-16', 'image/Teacher-male512_44209.png', 'pataka', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tblcourses`
--

CREATE TABLE `tblcourses` (
  `COURSE_ID` int(11) NOT NULL,
  `COURSE_CODE` varchar(20) NOT NULL,
  `COURSE_NAME` varchar(150) NOT NULL,
  `COURSE_DESC` text DEFAULT NULL,
  `STATUS` varchar(20) NOT NULL DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tblcourses`
--

INSERT INTO `tblcourses` (`COURSE_ID`, `COURSE_CODE`, `COURSE_NAME`, `COURSE_DESC`, `STATUS`) VALUES
(1, 'BSIT', 'Bachelor of Science in Information Technology', 'Information Technology program', 'Active'),
(2, 'BSTM', 'Bachelor of Science in Tourism Management', 'Tourism Management program', 'Active'),
(3, 'BSED', 'Bachelor of Secondary Education', 'Secondary Education program', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `tblemployee`
--

CREATE TABLE `tblemployee` (
  `EmployeeID` int(11) NOT NULL,
  `FirstName` varchar(100) NOT NULL,
  `LastName` varchar(100) NOT NULL,
  `Email` varchar(150) NOT NULL,
  `Phone` varchar(30) NOT NULL,
  `DateOfBirth` date DEFAULT NULL,
  `HireDate` date NOT NULL,
  `JobTitle` varchar(100) NOT NULL,
  `Department` varchar(100) NOT NULL,
  `Salary` decimal(10,2) NOT NULL,
  `ManagerID` int(11) DEFAULT NULL,
  `IsActive` tinyint(1) NOT NULL,
  `MiddleName` varchar(100) NOT NULL,
  `Suffix` varchar(10) DEFAULT NULL,
  `PhotoPath` varchar(255) DEFAULT NULL,
  `FullName` varchar(255) GENERATED ALWAYS AS (trim(concat_ws(' ',`FirstName`,nullif(`MiddleName`,''),`LastName`,nullif(`Suffix`,'')))) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblemployee`
--

INSERT INTO `tblemployee` (`EmployeeID`, `FirstName`, `LastName`, `Email`, `Phone`, `DateOfBirth`, `HireDate`, `JobTitle`, `Department`, `Salary`, `ManagerID`, `IsActive`, `MiddleName`, `Suffix`, `PhotoPath`) VALUES
(5, 'JACK', 'ABADIES', '123@GMAIL.COM', '09852114503', '1996-12-12', '2006-12-16', 'ceo', 'HR', 1000.00, NULL, 0, 'P.', NULL, 'uploads/employees/emp_1789965445_8106.jpg'),
(6, 'SAMMY', 'SIEBADAj', 'SAM@GMAIL.COM', '095864515654', '2003-12-12', '2024-12-16', 'CEO', 'HR', 25000.00, 5, 1, 'P.', NULL, ''),
(7, 'jay', 'mar', 'jay@gmail.com', '626246456', '2007-12-10', '2008-12-10', 'hahah', 'agaga', 5245.00, 6, 1, 's', 'Jr.', NULL),
(8, 'Honey', 'Carasaquit', 'han@gmail.com', '09207956964', '2006-02-04', '2007-12-05', 'Accountant', 'IU', 18000.00, 5, 1, 'P.', NULL, ''),
(9, 's', 'z', 'yen2@gmail.com', '89', '2003-12-01', '2003-12-02', 'doctor', 'c', 2000.00, 8, 1, 'h', NULL, ''),
(10, '4884', '965945', 'jkg@gmail.com', '911', '1996-12-12', '2000-12-06', 'fire man', 'fmr', 4500.00, 5, 0, '515', 'Sr.', 'uploads/employees/emp_1789968744_4162.jpg'),
(11, 'JESSON', 'BATUTO', 'jbatuto@gmail.com', '091234567898', '1995-01-25', '2000-02-15', 'Doctor', 'Radiology', 45000.00, 8, 0, 'P.', 'Sr.', '');

-- --------------------------------------------------------

--
-- Table structure for table `tblenrollment`
--

CREATE TABLE `tblenrollment` (
  `ENROLLMENT_ID` int(11) NOT NULL,
  `S_ID` int(11) NOT NULL,
  `COURSE_ID` int(11) NOT NULL,
  `SECTION_ID` int(11) DEFAULT NULL,
  `SY_ID` int(11) NOT NULL,
  `YEAR_LEVEL` varchar(20) NOT NULL,
  `SEMESTER` varchar(20) NOT NULL,
  `CATEGORY` varchar(30) NOT NULL DEFAULT 'New',
  `CURRICULUM_YR` varchar(20) DEFAULT NULL,
  `DATE_RESERVED` date DEFAULT NULL,
  `DATE_ENROLLED` date DEFAULT NULL,
  `STATUS` varchar(30) NOT NULL DEFAULT 'Reserved',
  `ENCODED_BY` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tblenrollment`
--

INSERT INTO `tblenrollment` (`ENROLLMENT_ID`, `S_ID`, `COURSE_ID`, `SECTION_ID`, `SY_ID`, `YEAR_LEVEL`, `SEMESTER`, `CATEGORY`, `CURRICULUM_YR`, `DATE_RESERVED`, `DATE_ENROLLED`, `STATUS`, `ENCODED_BY`) VALUES
(1, 1, 1, 1, 1, '1st Year', '1st Semester', 'New', '2025-2026', '2025-06-05', '2025-06-10', 'Enrolled', 1),
(2, 5, 1, 2, 1, '1st Year', '1st Semester', 'Old', '2025-2026', '2025-06-06', '2025-06-11', 'Enrolled', 1),
(3, 6, 2, NULL, 1, '1st Year', '1st Semester', 'Transferee', '2025-2026', '2025-06-12', NULL, 'Reserved', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tblenrollment_details`
--

CREATE TABLE `tblenrollment_details` (
  `DETAIL_ID` int(11) NOT NULL,
  `ENROLLMENT_ID` int(11) NOT NULL,
  `SUBJECT_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tblenrollment_details`
--

INSERT INTO `tblenrollment_details` (`DETAIL_ID`, `ENROLLMENT_ID`, `SUBJECT_ID`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 1, 3),
(4, 2, 1),
(5, 2, 2),
(6, 2, 3);

-- --------------------------------------------------------

--
-- Table structure for table `tblfeeitems`
--

CREATE TABLE `tblfeeitems` (
  `FEEITEM_ID` int(11) NOT NULL,
  `CATEGORY` varchar(20) NOT NULL DEFAULT 'STANDARD',
  `ITEM_NAME` varchar(120) NOT NULL,
  `AMOUNT` decimal(10,2) NOT NULL DEFAULT 0.00,
  `YEAR_LEVEL` varchar(30) NOT NULL DEFAULT '1st Year',
  `SORT_ORDER` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblfeeitems`
--

INSERT INTO `tblfeeitems` (`FEEITEM_ID`, `CATEGORY`, `ITEM_NAME`, `AMOUNT`, `YEAR_LEVEL`, `SORT_ORDER`) VALUES
(1, 'STANDARD', 'Registration/Matriculation', 294.00, '1st Year', 10),
(2, 'STANDARD', 'Registration/Matriculation', 294.00, '2nd Year', 10),
(3, 'STANDARD', 'Registration/Matriculation', 294.00, '3rd Year', 10),
(4, 'STANDARD', 'Registration/Matriculation', 294.00, '4th Year', 10),
(5, 'STANDARD', 'Athletics/Amusement', 263.00, '1st Year', 11),
(6, 'STANDARD', 'Athletics/Amusement', 263.00, '2nd Year', 11),
(7, 'STANDARD', 'Athletics/Amusement', 263.00, '3rd Year', 11),
(8, 'STANDARD', 'Athletics/Amusement', 263.00, '4th Year', 11),
(9, 'STANDARD', 'Audio Visual', 53.00, '1st Year', 12),
(10, 'STANDARD', 'Audio Visual', 53.00, '2nd Year', 12),
(11, 'STANDARD', 'Audio Visual', 53.00, '3rd Year', 12),
(12, 'STANDARD', 'Audio Visual', 53.00, '4th Year', 12),
(13, 'STANDARD', 'Developmental', 263.00, '1st Year', 13),
(14, 'STANDARD', 'Developmental', 263.00, '2nd Year', 13),
(15, 'STANDARD', 'Developmental', 263.00, '3rd Year', 13),
(16, 'STANDARD', 'Developmental', 263.00, '4th Year', 13),
(17, 'STANDARD', 'Publication', 60.00, '1st Year', 14),
(18, 'STANDARD', 'Publication', 60.00, '2nd Year', 14),
(19, 'STANDARD', 'Publication', 60.00, '3rd Year', 14),
(20, 'STANDARD', 'Publication', 60.00, '4th Year', 14),
(21, 'STANDARD', 'Religious/Guidance', 150.00, '1st Year', 15),
(22, 'STANDARD', 'Religious/Guidance', 150.00, '2nd Year', 15),
(23, 'STANDARD', 'Religious/Guidance', 150.00, '3rd Year', 15),
(24, 'STANDARD', 'Religious/Guidance', 150.00, '4th Year', 15),
(25, 'STANDARD', 'Computer', 500.00, '1st Year', 16),
(26, 'STANDARD', 'Computer', 500.00, '2nd Year', 16),
(27, 'STANDARD', 'Computer', 500.00, '3rd Year', 16),
(28, 'STANDARD', 'Computer', 500.00, '4th Year', 16),
(29, 'STANDARD', 'Identification Card', 0.00, '1st Year', 17),
(30, 'STANDARD', 'Identification Card', 0.00, '2nd Year', 17),
(31, 'STANDARD', 'Identification Card', 0.00, '3rd Year', 17),
(32, 'STANDARD', 'Identification Card', 0.00, '4th Year', 17),
(33, 'STANDARD', 'Library ID', 0.00, '1st Year', 18),
(34, 'STANDARD', 'Library ID', 0.00, '2nd Year', 18),
(35, 'STANDARD', 'Library ID', 0.00, '3rd Year', 18),
(36, 'STANDARD', 'Library ID', 0.00, '4th Year', 18),
(37, 'STANDARD', 'Insurance', 38.00, '1st Year', 19),
(38, 'STANDARD', 'Insurance', 38.00, '2nd Year', 19),
(39, 'STANDARD', 'Insurance', 38.00, '3rd Year', 19),
(40, 'STANDARD', 'Insurance', 38.00, '4th Year', 19),
(41, 'STANDARD', 'Laboratory', 74.00, '1st Year', 20),
(42, 'STANDARD', 'Laboratory', 74.00, '2nd Year', 20),
(43, 'STANDARD', 'Laboratory', 74.00, '3rd Year', 20),
(44, 'STANDARD', 'Laboratory', 74.00, '4th Year', 20),
(45, 'STANDARD', 'Library Fee', 68.00, '1st Year', 21),
(46, 'STANDARD', 'Library Fee', 68.00, '2nd Year', 21),
(47, 'STANDARD', 'Library Fee', 68.00, '3rd Year', 21),
(48, 'STANDARD', 'Library Fee', 68.00, '4th Year', 21),
(49, 'STANDARD', 'Medical/Dentical', 53.00, '1st Year', 22),
(50, 'STANDARD', 'Medical/Dentical', 53.00, '2nd Year', 22),
(51, 'STANDARD', 'Medical/Dentical', 53.00, '3rd Year', 22),
(52, 'STANDARD', 'Medical/Dentical', 53.00, '4th Year', 22),
(53, 'STANDARD', 'Utility Services', 210.00, '1st Year', 23),
(54, 'STANDARD', 'Utility Services', 210.00, '2nd Year', 23),
(55, 'STANDARD', 'Utility Services', 210.00, '3rd Year', 23),
(56, 'STANDARD', 'Utility Services', 210.00, '4th Year', 23),
(57, 'STANDARD', 'Student Handbook', 0.00, '1st Year', 24),
(58, 'STANDARD', 'Student Handbook', 0.00, '2nd Year', 24),
(59, 'STANDARD', 'Student Handbook', 0.00, '3rd Year', 24),
(60, 'STANDARD', 'Student Handbook', 0.00, '4th Year', 24),
(61, 'NONSTANDARD', 'Energy', 263.00, '1st Year', 30),
(62, 'NONSTANDARD', 'Energy', 263.00, '2nd Year', 30),
(63, 'NONSTANDARD', 'Energy', 263.00, '3rd Year', 30),
(64, 'NONSTANDARD', 'Energy', 263.00, '4th Year', 30),
(65, 'NONSTANDARD', 'Related Learnings', 116.00, '1st Year', 31),
(66, 'NONSTANDARD', 'Related Learnings', 116.00, '2nd Year', 31),
(67, 'NONSTANDARD', 'Related Learnings', 116.00, '3rd Year', 31),
(68, 'NONSTANDARD', 'Related Learnings', 116.00, '4th Year', 31),
(69, 'NONSTANDARD', 'Security', 210.00, '1st Year', 32),
(70, 'NONSTANDARD', 'Security', 210.00, '2nd Year', 32),
(71, 'NONSTANDARD', 'Security', 210.00, '3rd Year', 32),
(72, 'NONSTANDARD', 'Security', 210.00, '4th Year', 32),
(73, 'NONSTANDARD', 'CEAP', 25.00, '1st Year', 33),
(74, 'NONSTANDARD', 'CEAP', 25.00, '2nd Year', 33),
(75, 'NONSTANDARD', 'CEAP', 25.00, '3rd Year', 33),
(76, 'NONSTANDARD', 'CEAP', 25.00, '4th Year', 33),
(77, 'NONSTANDARD', 'COE', 30.00, '1st Year', 34),
(78, 'NONSTANDARD', 'COE', 30.00, '2nd Year', 34),
(79, 'NONSTANDARD', 'COE', 30.00, '3rd Year', 34),
(80, 'NONSTANDARD', 'COE', 30.00, '4th Year', 34),
(81, 'NONSTANDARD', 'NIDACS/Membership', 194.00, '1st Year', 35),
(82, 'NONSTANDARD', 'NIDACS/Membership', 194.00, '2nd Year', 35),
(83, 'NONSTANDARD', 'NIDACS/Membership', 194.00, '3rd Year', 35),
(84, 'NONSTANDARD', 'NIDACS/Membership', 194.00, '4th Year', 35),
(85, 'OTHER', 'Love Gift', 1800.00, '1st Year', 40),
(86, 'OTHER', 'Love Gift', 1800.00, '2nd Year', 40),
(87, 'OTHER', 'Love Gift', 1800.00, '3rd Year', 40),
(88, 'OTHER', 'Love Gift', 1800.00, '4th Year', 40);

-- --------------------------------------------------------

--
-- Table structure for table `tblfeeschedule`
--

CREATE TABLE `tblfeeschedule` (
  `FEESCHEDULE_ID` int(11) NOT NULL,
  `COURSE_ID` int(11) NOT NULL,
  `YEAR_LEVEL` varchar(30) NOT NULL,
  `TUITION_FEE` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblfeeschedule`
--

INSERT INTO `tblfeeschedule` (`FEESCHEDULE_ID`, `COURSE_ID`, `YEAR_LEVEL`, `TUITION_FEE`) VALUES
(1, 3, '1st Year', 20500.00),
(2, 1, '1st Year', 20500.00),
(3, 2, '1st Year', 20500.00),
(4, 3, '2nd Year', 20500.00),
(5, 1, '2nd Year', 20500.00),
(6, 2, '2nd Year', 20500.00),
(7, 3, '3rd Year', 20500.00),
(8, 1, '3rd Year', 20500.00),
(9, 2, '3rd Year', 20500.00),
(10, 3, '4th Year', 20500.00),
(11, 1, '4th Year', 20500.00),
(12, 2, '4th Year', 20500.00);

-- --------------------------------------------------------

--
-- Table structure for table `tblgrades`
--

CREATE TABLE `tblgrades` (
  `GRADE_ID` int(11) NOT NULL,
  `S_ID` int(11) NOT NULL,
  `SUBJECT_ID` int(11) NOT NULL,
  `ENROLLMENT_ID` int(11) NOT NULL,
  `SY_ID` int(11) NOT NULL,
  `SEMESTER` varchar(20) DEFAULT NULL,
  `GRADE` decimal(5,2) DEFAULT NULL,
  `REMARKS` varchar(20) DEFAULT NULL,
  `DATE_ENCODED` date NOT NULL DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tblgrades`
--

INSERT INTO `tblgrades` (`GRADE_ID`, `S_ID`, `SUBJECT_ID`, `ENROLLMENT_ID`, `SY_ID`, `SEMESTER`, `GRADE`, `REMARKS`, `DATE_ENCODED`) VALUES
(1, 1, 1, 1, 1, '1st Semester', 1.75, 'Passed', '2025-10-20'),
(2, 1, 2, 1, 1, '1st Semester', 2.00, 'Passed', '2025-10-20'),
(3, 1, 3, 1, 1, '1st Semester', 1.50, 'Passed', '2025-10-20'),
(4, 5, 1, 2, 1, '1st Semester', 2.25, 'Passed', '2025-10-20'),
(5, 5, 2, 2, 1, '1st Semester', 3.00, 'Passed', '2025-10-20'),
(6, 5, 3, 2, 1, '1st Semester', 1.25, 'Passed', '2025-10-20');

-- --------------------------------------------------------

--
-- Table structure for table `tblnotifications`
--

CREATE TABLE `tblnotifications` (
  `NotificationID` int(11) NOT NULL,
  `Title` varchar(150) NOT NULL,
  `Message` varchar(255) NOT NULL,
  `Type` varchar(30) NOT NULL DEFAULT 'general',
  `LinkURL` varchar(255) DEFAULT NULL,
  `IsRead` tinyint(1) NOT NULL DEFAULT 0,
  `DateCreated` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblnotifications`
--

INSERT INTO `tblnotifications` (`NotificationID`, `Title`, `Message`, `Type`, `LinkURL`, `IsRead`, `DateCreated`) VALUES
(1, 'Test Notification', 'This is a sample test message.', 'general', NULL, 1, '2026-09-21 12:45:07'),
(2, 'New Employee Added', 'Honey Carasaquit was added to the system.', 'employee', 'module/employee/index.php?view=view&id=8', 1, '2026-09-21 13:10:39'),
(3, 'New Employee Added', 's z was added to the system.', 'employee', 'module/employee/index.php?view=view&id=9', 1, '2026-09-21 13:21:15'),
(4, 'New Employee Added', '4884 965945 was added to the system.', 'employee', 'module/employee/index.php?view=view&id=10', 1, '2026-09-21 13:32:24'),
(5, 'New Employee Added', 'JESSON BATUTO was added to the system.', 'employee', 'module/employee/index.php?view=view&id=11', 1, '2026-09-21 13:50:37');

-- --------------------------------------------------------

--
-- Table structure for table `tblpayments`
--

CREATE TABLE `tblpayments` (
  `PAYMENT_ID` int(11) NOT NULL,
  `ENROLLMENT_ID` int(11) NOT NULL,
  `FEE_TYPE` varchar(40) NOT NULL,
  `AMOUNT` decimal(10,2) NOT NULL DEFAULT 0.00,
  `OR_NUMBER` varchar(60) DEFAULT NULL,
  `DATE_PAID` date DEFAULT NULL,
  `RECEIVED_BY` int(11) DEFAULT NULL,
  `REMARKS` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblpayments`
--

INSERT INTO `tblpayments` (`PAYMENT_ID`, `ENROLLMENT_ID`, `FEE_TYPE`, `AMOUNT`, `OR_NUMBER`, `DATE_PAID`, `RECEIVED_BY`, `REMARKS`) VALUES
(1, 2, 'Enrollment Fee', 10000.00, '123', '2026-09-21', 81, 'paid');

-- --------------------------------------------------------

--
-- Table structure for table `tblschoolyear`
--

CREATE TABLE `tblschoolyear` (
  `SY_ID` int(11) NOT NULL,
  `SCHOOL_YEAR` varchar(20) NOT NULL,
  `STATUS` varchar(20) NOT NULL DEFAULT 'Inactive'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tblschoolyear`
--

INSERT INTO `tblschoolyear` (`SY_ID`, `SCHOOL_YEAR`, `STATUS`) VALUES
(1, '2025-2026', 'Active'),
(4, '2024-2025', 'Inactive');

-- --------------------------------------------------------

--
-- Table structure for table `tblsections`
--

CREATE TABLE `tblsections` (
  `SECTION_ID` int(11) NOT NULL,
  `SECTION_NAME` varchar(50) NOT NULL,
  `COURSE_ID` int(11) NOT NULL,
  `SY_ID` int(11) NOT NULL,
  `YEAR_LEVEL` varchar(20) NOT NULL,
  `PROGRAM_HEAD` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tblsections`
--

INSERT INTO `tblsections` (`SECTION_ID`, `SECTION_NAME`, `COURSE_ID`, `SY_ID`, `YEAR_LEVEL`, `PROGRAM_HEAD`) VALUES
(1, 'A', 1, 1, '1st Year', NULL),
(2, 'B', 1, 1, '1st Year', NULL),
(3, 'C', 1, 1, '1st Year', NULL),
(4, 'D', 1, 1, '1st Year', NULL),
(5, 'A', 2, 1, '1st Year', NULL),
(6, 'B', 2, 1, '1st Year', NULL),
(7, 'C', 2, 1, '1st Year', NULL),
(8, 'D', 2, 1, '1st Year', NULL),
(9, 'A', 3, 1, '1st Year', NULL),
(10, 'B', 3, 1, '1st Year', NULL),
(11, 'C', 3, 1, '1st Year', NULL),
(12, 'D', 3, 1, '1st Year', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tblstudent`
--

CREATE TABLE `tblstudent` (
  `S_ID` int(11) NOT NULL,
  `IDNO` varchar(20) NOT NULL,
  `FNAME` varchar(40) NOT NULL,
  `LNAME` varchar(40) NOT NULL,
  `MNAME` varchar(40) NOT NULL,
  `SEX` varchar(10) NOT NULL DEFAULT 'Male',
  `BDAY` date DEFAULT NULL,
  `BPLACE` text DEFAULT NULL,
  `STATUS` varchar(30) NOT NULL DEFAULT 'Active',
  `AGE` int(11) DEFAULT NULL,
  `NATIONALITY` varchar(40) DEFAULT NULL,
  `RELIGION` varchar(255) DEFAULT NULL,
  `CONTACT_NO` varchar(40) DEFAULT NULL,
  `HOME_ADD` text DEFAULT NULL,
  `EMAIL` varchar(150) DEFAULT NULL,
  `ACC_PASSWORD` text DEFAULT NULL,
  `LRNNO` varchar(15) DEFAULT NULL,
  `CONTACTPERSON` varchar(150) DEFAULT NULL,
  `COMPANYIDNO` int(11) DEFAULT NULL,
  `COURSE_ID` int(11) DEFAULT NULL,
  `AddedBy` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblstudent`
--

INSERT INTO `tblstudent` (`S_ID`, `IDNO`, `FNAME`, `LNAME`, `MNAME`, `SEX`, `BDAY`, `BPLACE`, `STATUS`, `AGE`, `NATIONALITY`, `RELIGION`, `CONTACT_NO`, `HOME_ADD`, `EMAIL`, `ACC_PASSWORD`, `LRNNO`, `CONTACTPERSON`, `COMPANYIDNO`, `COURSE_ID`, `AddedBy`) VALUES
(1, '2011072501', 'ZHA KEISHA', 'BATUTO', 'JIMENEZ', 'Female', '2011-07-25', NULL, 'Active', 13, NULL, NULL, '09176374293', NULL, NULL, NULL, NULL, NULL, NULL, 1, 1),
(5, '3333', 'Jha Syl', 'hjh', 'hjh', 'Male', '2025-01-14', 'Cebu Cebu', 'Active', 11, 'Filipino', NULL, NULL, NULL, 'jason@co.ph', NULL, NULL, NULL, NULL, 1, 1),
(6, '22222', 'ssd', 'sds', 'sds', 'Male', '2025-02-04', 'sds', 'Active', 22, 'Filipino', NULL, NULL, NULL, 'jason@co.ph', NULL, NULL, NULL, NULL, 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tblsubjects`
--

CREATE TABLE `tblsubjects` (
  `SUBJECT_ID` int(11) NOT NULL,
  `SUBJECT_CODE` varchar(20) NOT NULL,
  `SUBJECT_NAME` varchar(150) NOT NULL,
  `UNITS` int(11) NOT NULL DEFAULT 3,
  `COURSE_ID` int(11) NOT NULL,
  `YEAR_LEVEL` varchar(20) DEFAULT NULL,
  `SEMESTER` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tblsubjects`
--

INSERT INTO `tblsubjects` (`SUBJECT_ID`, `SUBJECT_CODE`, `SUBJECT_NAME`, `UNITS`, `COURSE_ID`, `YEAR_LEVEL`, `SEMESTER`) VALUES
(1, 'IT101', 'Introduction to Computing', 3, 1, '1st Year', '1st Semester'),
(2, 'IT102', 'Computer Programming 1', 3, 1, '1st Year', '1st Semester'),
(3, 'IT103', 'Discrete Mathematics', 3, 1, '1st Year', '1st Semester'),
(4, 'IT104', 'Web Systems and Technologies', 3, 1, '1st Year', '2nd Semester'),
(5, 'TM101', 'Introduction to Tourism', 3, 2, '1st Year', '1st Semester'),
(6, 'TM102', 'Philippine Culture and Tourism Geography', 3, 2, '1st Year', '1st Semester'),
(7, 'TM103', 'Micro Perspective of Tourism', 3, 2, '1st Year', '2nd Semester'),
(8, 'ED101', 'The Teaching Profession', 3, 3, '1st Year', '1st Semester'),
(9, 'ED102', 'Child and Adolescent Development', 3, 3, '1st Year', '1st Semester'),
(10, 'ED103', 'Facilitating Learner-Centered Teaching', 3, 3, '1st Year', '2nd Semester');

-- --------------------------------------------------------

--
-- Table structure for table `tblusers`
--

CREATE TABLE `tblusers` (
  `UID` int(11) NOT NULL,
  `DISPLAYNAME` varchar(30) NOT NULL,
  `USERNAME` varchar(50) NOT NULL,
  `PASSWORD` text NOT NULL,
  `TYPE` varchar(15) NOT NULL,
  `TYPEID` int(11) DEFAULT NULL,
  `ADDEDBY` int(3) NOT NULL,
  `DATEADDED` date NOT NULL,
  `MODIFIEDBY` int(3) NOT NULL,
  `DATEMODIFIED` date NOT NULL,
  `STATUSACTIVE` int(2) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblusers`
--

INSERT INTO `tblusers` (`UID`, `DISPLAYNAME`, `USERNAME`, `PASSWORD`, `TYPE`, `TYPEID`, `ADDEDBY`, `DATEADDED`, `MODIFIEDBY`, `DATEMODIFIED`, `STATUSACTIVE`) VALUES
(1, 'Jason', 'admin', 'd033e22ae348aeb5660fc2140aec35850c4da997', 'Administrator', 1, 1, '2020-08-27', 1, '2021-07-05', 1),
(77, 'dfd', 'dfd', '6bb65257fcab4e2975cd96b0f7fc4b53d97c10b6', 'Staff', 3, 1, '2025-01-16', 1, '2026-08-02', 1),
(78, 'dfd', 'dfdf', '6bb65257fcab4e2975cd96b0f7fc4b53d97c10b6', 'Staff', 3, 1, '2025-01-16', 1, '2025-01-16', 1),
(79, 'Erick', 'jason', '5c2dd944dde9e08881bef0894fe7b22a5c9c4b06', 'Administrator', 1, 1, '2025-01-24', 1, '2025-01-24', 1),
(81, 'Abadies', 'abadies', '4448d1f9bf6356dd002789967d4aea7b573ce520', 'Administrator', 1, 1, '2026-08-18', 1, '2026-08-18', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tblusertype`
--

CREATE TABLE `tblusertype` (
  `TYPEID` int(11) NOT NULL,
  `USERTYPE` varchar(30) NOT NULL,
  `STATUS` varchar(20) NOT NULL DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblusertype`
--

INSERT INTO `tblusertype` (`TYPEID`, `USERTYPE`, `STATUS`) VALUES
(1, 'Administrator', 'Active'),
(2, 'Doctor', 'Active'),
(3, 'Staff', 'Active'),
(7, 'Nurse', 'Active'),
(12, 'hello', 'Inactive'),
(13, 'cashier', 'Active'),
(14, 'sdsds', 'Inactive');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `alumni_details`
--
ALTER TABLE `alumni_details`
  ADD PRIMARY KEY (`AlumniID`),
  ADD KEY `fk_alumni_student` (`S_ID`),
  ADD KEY `fk_alumni_addedby` (`AddedBy`);

--
-- Indexes for table `tblcourses`
--
ALTER TABLE `tblcourses`
  ADD PRIMARY KEY (`COURSE_ID`),
  ADD UNIQUE KEY `COURSE_CODE` (`COURSE_CODE`);

--
-- Indexes for table `tblemployee`
--
ALTER TABLE `tblemployee`
  ADD PRIMARY KEY (`EmployeeID`),
  ADD KEY `idx_fullname` (`FullName`);

--
-- Indexes for table `tblenrollment`
--
ALTER TABLE `tblenrollment`
  ADD PRIMARY KEY (`ENROLLMENT_ID`),
  ADD UNIQUE KEY `uq_enrollment_term` (`S_ID`,`SY_ID`,`SEMESTER`),
  ADD KEY `fk_enrollment_student` (`S_ID`),
  ADD KEY `fk_enrollment_course` (`COURSE_ID`),
  ADD KEY `fk_enrollment_section` (`SECTION_ID`),
  ADD KEY `fk_enrollment_sy` (`SY_ID`),
  ADD KEY `fk_enrollment_encodedby` (`ENCODED_BY`);

--
-- Indexes for table `tblenrollment_details`
--
ALTER TABLE `tblenrollment_details`
  ADD PRIMARY KEY (`DETAIL_ID`),
  ADD UNIQUE KEY `uq_enrollment_subject` (`ENROLLMENT_ID`,`SUBJECT_ID`),
  ADD KEY `fk_endetails_enrollment` (`ENROLLMENT_ID`),
  ADD KEY `fk_endetails_subject` (`SUBJECT_ID`);

--
-- Indexes for table `tblfeeitems`
--
ALTER TABLE `tblfeeitems`
  ADD PRIMARY KEY (`FEEITEM_ID`),
  ADD UNIQUE KEY `uq_feeitem` (`CATEGORY`,`ITEM_NAME`,`YEAR_LEVEL`),
  ADD KEY `ix_feeitem_year` (`YEAR_LEVEL`);

--
-- Indexes for table `tblfeeschedule`
--
ALTER TABLE `tblfeeschedule`
  ADD PRIMARY KEY (`FEESCHEDULE_ID`),
  ADD UNIQUE KEY `uq_feeschedule` (`COURSE_ID`,`YEAR_LEVEL`),
  ADD KEY `ix_fee_course` (`COURSE_ID`);

--
-- Indexes for table `tblgrades`
--
ALTER TABLE `tblgrades`
  ADD PRIMARY KEY (`GRADE_ID`),
  ADD KEY `fk_grades_student` (`S_ID`),
  ADD KEY `fk_grades_subject` (`SUBJECT_ID`),
  ADD KEY `fk_grades_enrollment` (`ENROLLMENT_ID`),
  ADD KEY `fk_grades_sy` (`SY_ID`);

--
-- Indexes for table `tblnotifications`
--
ALTER TABLE `tblnotifications`
  ADD PRIMARY KEY (`NotificationID`),
  ADD KEY `idx_isread` (`IsRead`);

--
-- Indexes for table `tblpayments`
--
ALTER TABLE `tblpayments`
  ADD PRIMARY KEY (`PAYMENT_ID`),
  ADD KEY `ix_pay_enrollment` (`ENROLLMENT_ID`),
  ADD KEY `ix_pay_feetype` (`FEE_TYPE`),
  ADD KEY `ix_pay_received` (`RECEIVED_BY`);

--
-- Indexes for table `tblschoolyear`
--
ALTER TABLE `tblschoolyear`
  ADD PRIMARY KEY (`SY_ID`),
  ADD UNIQUE KEY `SCHOOL_YEAR` (`SCHOOL_YEAR`);

--
-- Indexes for table `tblsections`
--
ALTER TABLE `tblsections`
  ADD PRIMARY KEY (`SECTION_ID`),
  ADD KEY `fk_sections_course` (`COURSE_ID`),
  ADD KEY `fk_sections_sy` (`SY_ID`);

--
-- Indexes for table `tblstudent`
--
ALTER TABLE `tblstudent`
  ADD PRIMARY KEY (`S_ID`),
  ADD UNIQUE KEY `IDNO` (`IDNO`),
  ADD KEY `fk_student_course` (`COURSE_ID`),
  ADD KEY `fk_student_addedby` (`AddedBy`);

--
-- Indexes for table `tblsubjects`
--
ALTER TABLE `tblsubjects`
  ADD PRIMARY KEY (`SUBJECT_ID`),
  ADD UNIQUE KEY `uq_subject_code` (`SUBJECT_CODE`),
  ADD KEY `fk_subjects_course` (`COURSE_ID`);

--
-- Indexes for table `tblusers`
--
ALTER TABLE `tblusers`
  ADD PRIMARY KEY (`UID`),
  ADD UNIQUE KEY `uq_username` (`USERNAME`),
  ADD KEY `fk_users_usertype` (`TYPEID`);

--
-- Indexes for table `tblusertype`
--
ALTER TABLE `tblusertype`
  ADD PRIMARY KEY (`TYPEID`),
  ADD UNIQUE KEY `uq_usertype` (`USERTYPE`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `alumni_details`
--
ALTER TABLE `alumni_details`
  MODIFY `AlumniID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tblcourses`
--
ALTER TABLE `tblcourses`
  MODIFY `COURSE_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tblemployee`
--
ALTER TABLE `tblemployee`
  MODIFY `EmployeeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `tblenrollment`
--
ALTER TABLE `tblenrollment`
  MODIFY `ENROLLMENT_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tblenrollment_details`
--
ALTER TABLE `tblenrollment_details`
  MODIFY `DETAIL_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tblfeeitems`
--
ALTER TABLE `tblfeeitems`
  MODIFY `FEEITEM_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT for table `tblfeeschedule`
--
ALTER TABLE `tblfeeschedule`
  MODIFY `FEESCHEDULE_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `tblgrades`
--
ALTER TABLE `tblgrades`
  MODIFY `GRADE_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tblnotifications`
--
ALTER TABLE `tblnotifications`
  MODIFY `NotificationID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tblpayments`
--
ALTER TABLE `tblpayments`
  MODIFY `PAYMENT_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tblschoolyear`
--
ALTER TABLE `tblschoolyear`
  MODIFY `SY_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tblsections`
--
ALTER TABLE `tblsections`
  MODIFY `SECTION_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `tblstudent`
--
ALTER TABLE `tblstudent`
  MODIFY `S_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tblsubjects`
--
ALTER TABLE `tblsubjects`
  MODIFY `SUBJECT_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tblusers`
--
ALTER TABLE `tblusers`
  MODIFY `UID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT for table `tblusertype`
--
ALTER TABLE `tblusertype`
  MODIFY `TYPEID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `alumni_details`
--
ALTER TABLE `alumni_details`
  ADD CONSTRAINT `fk_alumni_addedby` FOREIGN KEY (`AddedBy`) REFERENCES `tblusers` (`UID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_alumni_student` FOREIGN KEY (`S_ID`) REFERENCES `tblstudent` (`S_ID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `tblenrollment`
--
ALTER TABLE `tblenrollment`
  ADD CONSTRAINT `fk_enrollment_course` FOREIGN KEY (`COURSE_ID`) REFERENCES `tblcourses` (`COURSE_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_enrollment_encodedby` FOREIGN KEY (`ENCODED_BY`) REFERENCES `tblusers` (`UID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_enrollment_section` FOREIGN KEY (`SECTION_ID`) REFERENCES `tblsections` (`SECTION_ID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_enrollment_student` FOREIGN KEY (`S_ID`) REFERENCES `tblstudent` (`S_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_enrollment_sy` FOREIGN KEY (`SY_ID`) REFERENCES `tblschoolyear` (`SY_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tblenrollment_details`
--
ALTER TABLE `tblenrollment_details`
  ADD CONSTRAINT `fk_endetails_enrollment` FOREIGN KEY (`ENROLLMENT_ID`) REFERENCES `tblenrollment` (`ENROLLMENT_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_endetails_subject` FOREIGN KEY (`SUBJECT_ID`) REFERENCES `tblsubjects` (`SUBJECT_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tblgrades`
--
ALTER TABLE `tblgrades`
  ADD CONSTRAINT `fk_grades_enrollment` FOREIGN KEY (`ENROLLMENT_ID`) REFERENCES `tblenrollment` (`ENROLLMENT_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_grades_student` FOREIGN KEY (`S_ID`) REFERENCES `tblstudent` (`S_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_grades_subject` FOREIGN KEY (`SUBJECT_ID`) REFERENCES `tblsubjects` (`SUBJECT_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_grades_sy` FOREIGN KEY (`SY_ID`) REFERENCES `tblschoolyear` (`SY_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tblpayments`
--
ALTER TABLE `tblpayments`
  ADD CONSTRAINT `fk_pay_enrollment` FOREIGN KEY (`ENROLLMENT_ID`) REFERENCES `tblenrollment` (`ENROLLMENT_ID`) ON DELETE CASCADE;

--
-- Constraints for table `tblsections`
--
ALTER TABLE `tblsections`
  ADD CONSTRAINT `fk_sections_course` FOREIGN KEY (`COURSE_ID`) REFERENCES `tblcourses` (`COURSE_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_sections_sy` FOREIGN KEY (`SY_ID`) REFERENCES `tblschoolyear` (`SY_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tblstudent`
--
ALTER TABLE `tblstudent`
  ADD CONSTRAINT `fk_student_addedby` FOREIGN KEY (`AddedBy`) REFERENCES `tblusers` (`UID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_student_course` FOREIGN KEY (`COURSE_ID`) REFERENCES `tblcourses` (`COURSE_ID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `tblsubjects`
--
ALTER TABLE `tblsubjects`
  ADD CONSTRAINT `fk_subjects_course` FOREIGN KEY (`COURSE_ID`) REFERENCES `tblcourses` (`COURSE_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tblusers`
--
ALTER TABLE `tblusers`
  ADD CONSTRAINT `fk_users_usertype` FOREIGN KEY (`TYPEID`) REFERENCES `tblusertype` (`TYPEID`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
