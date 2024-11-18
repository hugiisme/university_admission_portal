-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 17, 2024 at 04:41 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `university_admission_portal`
--

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `application_id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `major_id` int(11) DEFAULT NULL,
  `block_id` int(11) DEFAULT NULL,
  `verifier_id` int(11) DEFAULT NULL,
  `status` enum('accepted','pending','denied') NOT NULL DEFAULT 'pending',
  `profile_picture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`application_id`, `student_id`, `major_id`, `block_id`, `verifier_id`, `status`, `profile_picture`) VALUES
(1, 7, 1, 1, NULL, 'pending', 'profile_picture_sample.jpg'),
(2, 7, 4, 5, NULL, 'pending', 'profile_picture_sample_1.jpg'),
(3, 8, 4, 1, NULL, 'pending', 'profile_picture_sample_2.jpg'),
(4, 8, 9, 2, NULL, 'pending', 'profile_picture_sample_3.jpg'),
(5, 9, 9, 1, NULL, 'pending', 'profile_picture_sample_4.jpg'),
(6, 9, 10, 12, NULL, 'pending', 'profile_picture_sample_5.jpg'),
(7, 10, 1, 1, NULL, 'pending', 'profile_picture_sample_6.jpg'),
(8, 11, 10, 10, NULL, 'pending', 'profile_picture_sample_7.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `application_scores`
--

CREATE TABLE `application_scores` (
  `application_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `score` decimal(3,1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `application_scores`
--

INSERT INTO `application_scores` (`application_id`, `subject_id`, `score`) VALUES
(1, 1, '10.0'),
(1, 4, '9.0'),
(1, 5, '8.0'),
(2, 1, '7.0'),
(2, 5, '6.0'),
(2, 6, '5.0'),
(3, 1, '10.0'),
(3, 4, '1.0'),
(3, 5, '10.0'),
(4, 1, '10.0'),
(4, 3, '10.0'),
(4, 4, '10.0'),
(5, 1, '8.5'),
(5, 4, '7.6'),
(5, 5, '10.0'),
(6, 1, '8.5'),
(6, 2, '8.0'),
(6, 3, '8.2'),
(7, 1, '9.0'),
(7, 4, '9.5'),
(7, 5, '10.0'),
(8, 1, '1.0'),
(8, 2, '1.0'),
(8, 4, '1.0');

-- --------------------------------------------------------

--
-- Table structure for table `blocks`
--

CREATE TABLE `blocks` (
  `block_id` int(11) NOT NULL,
  `code` varchar(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blocks`
--

INSERT INTO `blocks` (`block_id`, `code`) VALUES
(1, 'A00'),
(2, 'A01'),
(3, 'A02'),
(4, 'A03'),
(5, 'B00'),
(6, 'B01'),
(7, 'B02'),
(8, 'B03'),
(9, 'C00'),
(10, 'C01'),
(11, 'C02'),
(12, 'D01');

-- --------------------------------------------------------

--
-- Table structure for table `block_subjects`
--

CREATE TABLE `block_subjects` (
  `block_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `block_subjects`
--

INSERT INTO `block_subjects` (`block_id`, `subject_id`) VALUES
(1, 1),
(1, 4),
(1, 5),
(2, 1),
(2, 3),
(2, 4),
(3, 1),
(3, 4),
(3, 6),
(4, 1),
(4, 4),
(4, 7),
(5, 1),
(5, 5),
(5, 6),
(6, 1),
(6, 6),
(6, 7),
(7, 1),
(7, 6),
(7, 8),
(8, 1),
(8, 2),
(8, 6),
(9, 2),
(9, 7),
(9, 8),
(10, 1),
(10, 2),
(10, 4),
(11, 1),
(11, 2),
(11, 5),
(12, 1),
(12, 2),
(12, 3);

-- --------------------------------------------------------

--
-- Table structure for table `majors`
--

CREATE TABLE `majors` (
  `major_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `is_shown` tinyint(1) NOT NULL DEFAULT 0,
  `start_date` datetime NOT NULL DEFAULT current_timestamp(),
  `end_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `majors`
--

INSERT INTO `majors` (`major_id`, `name`, `is_shown`, `start_date`, `end_date`) VALUES
(1, 'Công nghệ thông tin', 1, '2024-11-17 00:00:00', '2024-11-18 13:00:00'),
(2, 'Cơ khí', 0, '2024-11-24 09:00:00', '2024-12-25 00:00:00'),
(3, 'Truyền thông đa phương tiện', 1, '2024-12-25 05:00:00', '2024-12-31 21:00:00'),
(4, 'Y học cổ truyền', 0, '2024-11-17 00:00:00', '2024-12-31 17:00:00'),
(5, 'Y đa khoa', 1, '2024-11-23 00:00:00', '2024-11-30 00:00:00'),
(6, 'Sư phạm Toán', 0, '2024-11-27 22:00:00', '2024-12-07 03:00:00'),
(7, 'Sư phạm Anh', 1, '2024-12-09 07:00:00', '2024-12-11 07:00:00'),
(8, 'Sư phạm Văn', 0, '2024-12-06 08:00:00', '2024-12-15 16:00:00'),
(9, 'Toán tin', 0, '2024-11-17 00:00:00', '2024-12-21 20:00:00'),
(10, 'Hệ thống thông tin', 1, '2024-11-01 01:00:00', '2024-11-30 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `major_blocks`
--

CREATE TABLE `major_blocks` (
  `major_id` int(11) NOT NULL,
  `block_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `major_blocks`
--

INSERT INTO `major_blocks` (`major_id`, `block_id`) VALUES
(1, 1),
(1, 2),
(2, 1),
(2, 2),
(2, 10),
(3, 1),
(3, 2),
(3, 9),
(3, 11),
(3, 12),
(4, 1),
(4, 5),
(5, 5),
(6, 1),
(6, 2),
(6, 5),
(6, 10),
(6, 12),
(7, 2),
(7, 12),
(8, 9),
(8, 12),
(9, 1),
(9, 2),
(10, 1),
(10, 2),
(10, 10),
(10, 12);

-- --------------------------------------------------------

--
-- Table structure for table `major_teachers`
--

CREATE TABLE `major_teachers` (
  `major_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `major_teachers`
--

INSERT INTO `major_teachers` (`major_id`, `user_id`) VALUES
(1, 4),
(2, 4),
(4, 5),
(5, 5),
(6, 4),
(10, 4);

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `subject_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`subject_id`, `name`) VALUES
(1, 'Toán'),
(2, 'Văn'),
(3, 'Anh'),
(4, 'Lý'),
(5, 'Hóa'),
(6, 'Sinh'),
(7, 'Sử'),
(8, 'Địa'),
(9, 'Giáo dục công dân'),
(10, 'Mỹ thuật'),
(11, 'Thể dục'),
(12, 'Tin học');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','teacher','student') NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `avatar` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `role`, `name`, `email`, `avatar`) VALUES
(1, 'admin1', 'e00cf25ad42683b3df678c61f42c6bda', 'admin', 'Admin 1', 'admin1@gmail.com', NULL),
(2, 'admin2', 'c84258e9c39059a89ab77d846ddab909', 'admin', 'Admin 2', 'admin2@gmail.com', NULL),
(3, 'admin3', '32cacb2f994f6b42183a1300d9a3e8d6', 'admin', 'Admin 3', 'admin3@gmail.com', NULL),
(4, 'gv1', '9b107c9caaefc8fdcf850e262c5f2964', 'teacher', 'Giáo viên 1', 'gv1@gmail.com', NULL),
(5, 'gv2', '3ffc659fd29c82996d4da6d564f68e96', 'teacher', 'Giáo viên 2', 'gv2@gmail.com', NULL),
(6, 'gv3', 'd64059ddd0ae7cae50ccb4ad9eee8d61', 'teacher', 'Giáo viên 3', 'gv3@gmail.com', NULL),
(7, 'hs1', '4c1379eb0511776f8202fbeee1f69a01', 'student', 'Học sinh 1', 'hs1@gmail.com', NULL),
(8, 'hs2', '280125cd77b5d140b0ef4fc6d238c2ba', 'student', 'Học sinh 2', 'hs2@gmail.com', NULL),
(9, 'hs3', '27782943c3fc484984a19cea20183860', 'student', 'Học sinh 3', 'hs3@gmail.com', NULL),
(10, 'hs4', 'c1f59da10b65bb6955874c5a597da780', 'student', 'Học sinh 4', 'hs4@gmail.com', NULL),
(11, 'hs5', '0bb90806ecb2b925fd992edac7c49341', 'student', 'Học sinh 5', 'hs5@gmail.com', NULL),
(12, 'hs6', '063041b7afd23bac996ace25f152aef0', 'student', 'Học sinh 6', 'hs6@gmail.com', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`application_id`),
  ADD KEY `applications_ibfk_1` (`student_id`),
  ADD KEY `applications_ibfk_2` (`major_id`),
  ADD KEY `applications_ibfk_3` (`block_id`),
  ADD KEY `applications_ibfk_4` (`verifier_id`);

--
-- Indexes for table `application_scores`
--
ALTER TABLE `application_scores`
  ADD PRIMARY KEY (`application_id`,`subject_id`),
  ADD KEY `application_scores_ibfk_2` (`subject_id`);

--
-- Indexes for table `blocks`
--
ALTER TABLE `blocks`
  ADD PRIMARY KEY (`block_id`);

--
-- Indexes for table `block_subjects`
--
ALTER TABLE `block_subjects`
  ADD PRIMARY KEY (`block_id`,`subject_id`),
  ADD KEY `block_subjects_ibfk_2` (`subject_id`);

--
-- Indexes for table `majors`
--
ALTER TABLE `majors`
  ADD PRIMARY KEY (`major_id`);

--
-- Indexes for table `major_blocks`
--
ALTER TABLE `major_blocks`
  ADD PRIMARY KEY (`major_id`,`block_id`),
  ADD KEY `major_blocks_ibfk_2` (`block_id`);

--
-- Indexes for table `major_teachers`
--
ALTER TABLE `major_teachers`
  ADD PRIMARY KEY (`major_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`subject_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `application_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `blocks`
--
ALTER TABLE `blocks`
  MODIFY `block_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `majors`
--
ALTER TABLE `majors`
  MODIFY `major_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `subject_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `applications`
--
ALTER TABLE `applications`
  ADD CONSTRAINT `applications_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `applications_ibfk_2` FOREIGN KEY (`major_id`) REFERENCES `majors` (`major_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `applications_ibfk_3` FOREIGN KEY (`block_id`) REFERENCES `blocks` (`block_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `applications_ibfk_4` FOREIGN KEY (`verifier_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `application_scores`
--
ALTER TABLE `application_scores`
  ADD CONSTRAINT `application_scores_ibfk_1` FOREIGN KEY (`application_id`) REFERENCES `applications` (`application_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `application_scores_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`subject_id`) ON DELETE CASCADE;

--
-- Constraints for table `block_subjects`
--
ALTER TABLE `block_subjects`
  ADD CONSTRAINT `block_subjects_ibfk_1` FOREIGN KEY (`block_id`) REFERENCES `blocks` (`block_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `block_subjects_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`subject_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `major_blocks`
--
ALTER TABLE `major_blocks`
  ADD CONSTRAINT `major_blocks_ibfk_1` FOREIGN KEY (`major_id`) REFERENCES `majors` (`major_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `major_blocks_ibfk_2` FOREIGN KEY (`block_id`) REFERENCES `blocks` (`block_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `major_teachers`
--
ALTER TABLE `major_teachers`
  ADD CONSTRAINT `major_teachers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `major_teachers_ibfk_2` FOREIGN KEY (`major_id`) REFERENCES `majors` (`major_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
