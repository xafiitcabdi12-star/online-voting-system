-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 19, 2026 at 01:21 PM
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
-- Database: `online_voting_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `candidates`
--

CREATE TABLE `candidates` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `position` varchar(50) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `candidates`
--

INSERT INTO `candidates` (`id`, `full_name`, `position`, `photo`, `created_at`) VALUES
(11, 'Mohamed Abdirahman Mohamud', 'President', 'cand_1768587772_7224.jpg', '2026-01-16 18:22:52'),
(13, 'nura moahmed', 'Vice President', 'cand_1768650369_5195.jpg', '2026-01-17 11:46:09'),
(14, 'ali hassan', 'President', '', '2026-01-17 12:34:46'),
(15, 'madaa abdi', 'Secretary', '', '2026-01-17 18:13:33'),
(16, 'abdiaziiz rooble', 'President', 'cand_1768812444_8317.jpg', '2026-01-19 08:46:21'),
(17, 'abdi ali', 'Vice President', '', '2026-01-19 10:22:08'),
(19, 'abdihafid muhidin', 'President', '', '2026-01-19 11:55:26');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `key` varchar(50) NOT NULL,
  `value` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`key`, `value`) VALUES
('voting_open', '1');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `gender` enum('Male','Female') NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `country` varchar(80) DEFAULT NULL,
  `city` varchar(80) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `user_type` enum('admin','voter') NOT NULL DEFAULT 'voter',
  `status` enum('active','inactive') DEFAULT 'active',
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `remember_token` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `gender`, `username`, `password`, `phone`, `country`, `city`, `address`, `email`, `profile_picture`, `user_type`, `status`, `reset_token`, `reset_expires`, `created_at`, `remember_token`) VALUES
(2, 'zoya', 'sharif', 'Female', 'admin', '$2y$10$/wxtvSNPR79eKD3ed8D3E.ogzHkzB9vuFIS45QlfG8zhvGu/v7uhS', '617626266', NULL, NULL, NULL, 'zoya@gmail.com', NULL, 'admin', 'active', NULL, NULL, '2026-01-14 19:43:28', NULL),
(3, 'madkey', 'abdi', 'Male', 'madkey', '$2y$10$gk.Z0cQTfDuWRSWItz2WHOkoyhHp2lY9eLYA7Yun/ke6y72ZQ4W0O', '616262626', NULL, NULL, NULL, 'madkey@gmail.com', 'user_3_1768427768.jpg', 'voter', 'active', NULL, NULL, '2026-01-14 19:50:23', NULL),
(4, 'muda', 'hasan', 'Female', 'muna', '$2y$10$1ZJ9cO4kBB/ep9IHnp.v3ORLXhg8IuFU8QdQVoz7CKA9KWMiyAnN6', '61626262', NULL, NULL, NULL, 'muna@gmail.com', NULL, 'voter', 'active', NULL, NULL, '2026-01-14 22:09:18', NULL),
(5, 'hasan', 'ali', 'Male', 'hasan', '$2y$10$Gd0SuqhKtjFUW5kPEVzdoeabi47ciJ/lmyJby/i4yp7BrjmLJkvTC', '6152525', NULL, NULL, NULL, 'hasan@gmail.com', NULL, 'voter', 'active', NULL, NULL, '2026-01-14 22:58:10', NULL),
(6, 'salman', 'ibrahim', 'Male', 'salman', '$2y$10$TaO1id.wEZA5UcpOY7gQjusqojyuMZaJcHTZmUGgis/.PqQEUWcoy', '0616626262', NULL, NULL, NULL, 'salmanow@gmail.com', 'user_6_1768461776.jpg', 'voter', 'active', NULL, NULL, '2026-01-15 07:22:08', NULL),
(7, 'najma', 'mohamed', 'Female', 'najxa', '$2y$10$eDYrFFND5sY2bvxjQreyb.FETiGLSX0PUTINvW0Rw06SR4tMXs8OC', '61525262', NULL, NULL, NULL, 'najma@gmail.com', NULL, 'voter', 'active', NULL, NULL, '2026-01-16 16:46:37', NULL),
(8, 'ali', 'nuur', 'Male', 'aliyey', '$2y$10$sdDFjLGmm.zGu96EsJe5POdUmw.Lv6/y5OfYJYLQT1s5T0Oirqq6a', '61223365', NULL, NULL, NULL, 'ali@gmail.com', NULL, 'voter', 'active', NULL, NULL, '2026-01-16 17:34:07', NULL),
(9, 'abdihafid', 'muhidin', 'Male', 'hafid', '$2y$10$Q5VEQXyAdF3CyXPNpz2eD.gC4EvxohCsIkOhkOlsiJb.poSrcwZaK', '612626266', NULL, NULL, NULL, 'hafid@gmail.com', 'user_9_1768587098.jpg', 'voter', 'active', NULL, NULL, '2026-01-16 18:09:25', 'ae0017904efa7e1ed8613957a24d74a12b1cd11aa48f6e59f6e79a3f80ce7652'),
(10, 'sabrin', 'axmed', 'Female', 'sabrin', '$2y$10$xkq3Il.h0g1N86uZsjZX6uTt3byVhVz30pCo62ayS9a04dYAA2xoe', '061626266', NULL, NULL, NULL, 'sabrin@gmail.com', NULL, 'voter', 'active', NULL, NULL, '2026-01-17 12:25:45', NULL),
(11, '66363', 'abdi', 'Male', 'abdi', '$2y$10$aF/iSJJeU7LV6gVUoM31fOt2yrbNDhPlRcss3eB3HkCnqF.D9.INC', '617276262', NULL, NULL, NULL, 'abdi@gmail.com', NULL, 'voter', 'active', NULL, NULL, '2026-01-17 12:36:38', NULL),
(12, 'mahad', 'abdi', 'Male', '77733', '$2y$10$Cnzj7Oouzc8opAbxHcS/5OGQ2D/i1C4xSsFkkeG7xmo.uaqiAMtlW', '061627272', NULL, NULL, NULL, '663663@gmail.com', NULL, 'voter', 'active', NULL, NULL, '2026-01-17 16:33:54', NULL),
(13, 'mohamed', 'abdi', 'Male', 'madaa', '$2y$10$.w0IT6pnlMrcvw6cjadfx.Ac///OgQpFD0mOO0xma7hUZLvpJ7eF2', '6114242425', NULL, NULL, NULL, 'madka@gmail.com', NULL, 'voter', 'active', NULL, NULL, '2026-01-17 16:55:33', NULL),
(14, 'Aisha', 'abdi', 'Female', 'alisha', '$2y$10$uIf4ZpXIx4XRXuLkgimm2Og2a3Q8RCzwdf6Ss49cAVw4JGSue2ReC', '06162626', NULL, NULL, NULL, 'alisha@gamil.com', NULL, 'voter', 'active', NULL, NULL, '2026-01-17 18:15:45', NULL),
(15, 'abdullahi', 'mohamed', 'Male', 'abdi12', '$2y$10$sK2f0EvpVvLlFnYg1NWEV.fmgeo0wxEkkf.V0c81NH23XM/hgdDF.', '6152525626', NULL, NULL, NULL, 'abdaa@gmail.com', NULL, 'voter', 'active', NULL, NULL, '2026-01-19 07:09:59', 'c9f8819858ee7ad80a178137c2540527d51269bfcce8e613d6925f04977341c9'),
(16, 'ali', 'Roble', 'Male', 'aliyow', '$2y$10$o4y/AxBCpPriSA3XMSq5LOrUTq/Ob3pY.zKO9BMjUw.AuRd68a7X6', '612868632', NULL, NULL, NULL, 'Abdiaziz@gmail.com', NULL, 'voter', 'active', NULL, NULL, '2026-01-19 08:43:36', NULL),
(17, 'yaxye', 'abdirahman', 'Male', 'yahye', '$2y$10$ExgKujBCSmN2MGDDbuAHI.h/QlDwUoAV/j9y4WdIYcJDy611s37c6', '061435353', NULL, NULL, NULL, 'yahye@gmail.com', NULL, 'voter', 'active', NULL, NULL, '2026-01-19 08:57:38', NULL),
(18, 'abdulahi', 'mohamed', 'Male', 'abdul', '$2y$10$Ev0rRUOUs2WUGOc3zHWHXuCA9pSZkDC9Ev8jsCd.SgzHOktPBfAOW', '061163626', NULL, NULL, 'hodan', 'abdul@gmail.com', NULL, 'voter', 'active', NULL, NULL, '2026-01-19 09:48:02', NULL),
(19, 'nuux', 'abuukar', 'Male', 'nuux', '$2y$10$JF4BosK3AFSEkLUemM.r/u1DjdaXe2RYsfEroqXO7Syi0EkANdbuq', '061626262', NULL, NULL, 'hodan', 'nuux@gmail.com', NULL, 'voter', 'active', NULL, NULL, '2026-01-19 10:02:15', NULL),
(20, 'sabrin', 'abuukar', 'Female', 'sabrinabuukar', '$2y$10$/Tqj4RZJW518tSZCi18tee8OUW/G3ZeRUt0wtIsxORNhOjPaSYVou', '061525255', NULL, NULL, 'kawgodey', 'sabrinabuukar@gmail.com', NULL, 'voter', 'active', NULL, NULL, '2026-01-19 12:00:05', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `votes`
--

CREATE TABLE `votes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `candidate_id` int(11) NOT NULL,
  `position` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `votes`
--

INSERT INTO `votes` (`id`, `user_id`, `candidate_id`, `position`, `created_at`) VALUES
(10, 9, 11, '', '2026-01-16 18:33:44'),
(11, 10, 13, '', '2026-01-17 12:27:34'),
(12, 12, 11, '', '2026-01-17 16:36:08'),
(13, 13, 11, '', '2026-01-17 16:55:38'),
(14, 14, 14, 'President', '2026-01-17 18:28:51'),
(22, 14, 13, 'Vice President', '2026-01-17 18:41:49'),
(23, 14, 15, 'Secretary', '2026-01-17 18:41:53'),
(24, 15, 13, 'Vice President', '2026-01-19 07:10:22'),
(25, 15, 14, 'President', '2026-01-19 07:10:44'),
(26, 16, 11, 'President', '2026-01-19 08:44:26'),
(27, 16, 13, 'Vice President', '2026-01-19 08:44:31'),
(28, 17, 16, 'President', '2026-01-19 09:00:44'),
(29, 17, 13, 'Vice President', '2026-01-19 09:01:56'),
(30, 17, 15, 'Secretary', '2026-01-19 09:02:03'),
(31, 20, 19, 'President', '2026-01-19 12:04:34'),
(32, 20, 17, 'Vice President', '2026-01-19 12:04:34'),
(33, 20, 15, 'Secretary', '2026-01-19 12:04:34');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `candidates`
--
ALTER TABLE `candidates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `votes`
--
ALTER TABLE `votes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_user_position` (`user_id`,`position`),
  ADD UNIQUE KEY `unique_user_position` (`user_id`,`position`),
  ADD KEY `idx_votes_user` (`user_id`),
  ADD KEY `idx_votes_position` (`position`),
  ADD KEY `idx_votes_candidate` (`candidate_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `candidates`
--
ALTER TABLE `candidates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `votes`
--
ALTER TABLE `votes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `votes`
--
ALTER TABLE `votes`
  ADD CONSTRAINT `votes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `votes_ibfk_2` FOREIGN KEY (`candidate_id`) REFERENCES `candidates` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
