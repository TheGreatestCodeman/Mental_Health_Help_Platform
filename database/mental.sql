-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 01, 2026 at 05:15 PM
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
-- Database: `mental`
--

-- --------------------------------------------------------

--
-- Table structure for table `analytics`
--

CREATE TABLE `analytics` (
  `feedback_id` int(11) NOT NULL,
  `average_rating` decimal(4,2) NOT NULL,
  `c_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `appointment_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `counselor_id` int(11) NOT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `status` varchar(20) DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`appointment_id`, `user_id`, `counselor_id`, `appointment_date`, `appointment_time`, `status`) VALUES
(1, 11, 4, '2026-01-01', '25:46:49', 'completed');

-- --------------------------------------------------------

--
-- Table structure for table `can_create`
--

CREATE TABLE `can_create` (
  `user_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `can_create`
--

INSERT INTO `can_create` (`user_id`, `post_id`) VALUES
(9, 1),
(9, 2),
(9, 3);

-- --------------------------------------------------------

--
-- Table structure for table `can_create_between`
--

CREATE TABLE `can_create_between` (
  `user_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `counselor`
--

CREATE TABLE `counselor` (
  `c_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(50) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `bio` text NOT NULL,
  `qualifications` varchar(255) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `counselor`
--

INSERT INTO `counselor` (`c_id`, `name`, `email`, `phone`, `bio`, `qualifications`, `profile_image`, `profile_pic`, `password`) VALUES
(1, 'Tanvir Tonmoy', 'tanvir.rahman.tonmoy@g.bracu.ac.bd', '01864625239', '', 'who cares', NULL, NULL, '$2y$10$i6exarYjPAANFjWw1T3ID.PoQhT72.txIYqVwKxGjqrVPYPixe31m'),
(2, 'i dont know', 'trtonmoy88@gmail.com', '01864625239', '', 'who cares', NULL, NULL, '$2y$10$pwY6z20h73VlZiFC.79qfOH.UpfxYIBa3xLKxmLCga/O3ywYSTwVC'),
(4, 'Tanvir Rahman Tonmoy', 'trtonmoy11@gmail.com', '01864625239', 'i dont know', 'none of your business', NULL, '1767282514_495367505_1114526730719562_3796782046368959672_n.jpg', '$2y$10$AisXnWLtFn197/JAIeZIx./HWnMn3Vi0C5LxC6W542kolbV59Pe1G');

-- --------------------------------------------------------

--
-- Table structure for table `counselor_availability`
--

CREATE TABLE `counselor_availability` (
  `availability_id` int(11) NOT NULL,
  `counselor_id` int(11) DEFAULT NULL,
  `available_date` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `counselor_availability`
--

INSERT INTO `counselor_availability` (`availability_id`, `counselor_id`, `available_date`, `start_time`, `end_time`) VALUES
(3, 4, '2026-01-02', '21:49:00', '22:49:00');

-- --------------------------------------------------------

--
-- Table structure for table `dm`
--

CREATE TABLE `dm` (
  `message_id` int(11) NOT NULL,
  `sender_id` int(11) DEFAULT NULL,
  `receiver_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `event_id` int(11) NOT NULL,
  `participants` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `feedback_id` int(11) NOT NULL,
  `comments` text DEFAULT NULL,
  `ratings` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `future_letters`
--

CREATE TABLE `future_letters` (
  `letter_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `letter_text` text DEFAULT NULL,
  `release_date` date DEFAULT NULL,
  `is_released` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `future_letters`
--

INSERT INTO `future_letters` (`letter_id`, `user_id`, `letter_text`, `release_date`, `is_released`, `created_at`) VALUES
(4, 8, 'Keep believing in yourself!', '2025-12-19', 1, '2025-12-19 00:52:57'),
(5, 8, 'hello from the other side', '2025-12-19', 1, '2025-12-20 10:46:22'),
(13, 8, 'Stay calm', '2025-12-20', 1, '2025-12-20 13:46:44'),
(17, 8, 'late night testing', '2025-12-31', 1, '2025-12-31 11:07:15');

-- --------------------------------------------------------

--
-- Table structure for table `post`
--

CREATE TABLE `post` (
  `post_id` int(11) NOT NULL,
  `sharable_link` varchar(255) DEFAULT NULL,
  `post_from_past` text DEFAULT NULL,
  `comments` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `post`
--

INSERT INTO `post` (`post_id`, `sharable_link`, `post_from_past`, `comments`) VALUES
(1, NULL, 'i hate myself', NULL),
(2, NULL, 'yoa', NULL),
(3, NULL, 'hi', 'jimmy: hi haff\n');

-- --------------------------------------------------------

--
-- Table structure for table `sends`
--

CREATE TABLE `sends` (
  `prev_message_id` int(11) NOT NULL,
  `new_message_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sends_between`
--

CREATE TABLE `sends_between` (
  `user_id` int(11) NOT NULL,
  `c_id` int(11) NOT NULL,
  `message_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `session_notes`
--

CREATE TABLE `session_notes` (
  `note_id` int(11) NOT NULL,
  `appointment_id` int(11) NOT NULL,
  `counselor_id` int(11) NOT NULL,
  `notes` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `session_notes`
--

INSERT INTO `session_notes` (`note_id`, `appointment_id`, `counselor_id`, `notes`, `created_at`) VALUES
(16, 1, 4, 'whats up', '2026-01-01 15:47:45');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` varchar(50) DEFAULT 'user',
  `points_earned` int(11) DEFAULT 0,
  `badges` varchar(255) DEFAULT NULL,
  `appointment_numbers` int(11) DEFAULT NULL,
  `feedback_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `password_hash`, `role`, `points_earned`, `badges`, `appointment_numbers`, `feedback_id`) VALUES
(8, 'lolcow', 'lolcow@gmail.com', '$2y$12$DNydsNqnrvCYyuvvsptbW.gm4hKIXhFVCra07Lnal6HWe55zNtSyO', 'user', 0, NULL, NULL, NULL),
(9, 'hafff', 'haff@ggma.com', '$2y$12$nRfZZczVKjb7y1182cFWSerATcmk2yb2DKNrY5NZJeYOuuYJr8uLO', 'user', 0, NULL, NULL, NULL),
(10, 'jimmy', 'goodma@gm.col', '$2y$12$1r2J/tOJUOHUgGgJV8JvQu54PKfFl6vBK/5Wfo14o3QeytMTLUXIW', 'user', 0, NULL, NULL, NULL),
(11, 'who cares', 'asgasga', 'asdfasdfasfda', 'counsilor', 0, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_group`
--

CREATE TABLE `user_group` (
  `group_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `members` int(11) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `will_receive`
--

CREATE TABLE `will_receive` (
  `feedback_id` int(11) NOT NULL,
  `c_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `analytics`
--
ALTER TABLE `analytics`
  ADD PRIMARY KEY (`feedback_id`,`average_rating`),
  ADD KEY `c_id` (`c_id`);

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`appointment_id`),
  ADD KEY `appointment_numbers` (`counselor_id`),
  ADD KEY `users_ibfk_1` (`user_id`);

--
-- Indexes for table `can_create`
--
ALTER TABLE `can_create`
  ADD PRIMARY KEY (`user_id`,`post_id`),
  ADD KEY `post_id` (`post_id`);

--
-- Indexes for table `can_create_between`
--
ALTER TABLE `can_create_between`
  ADD PRIMARY KEY (`user_id`,`event_id`,`group_id`),
  ADD KEY `event_id` (`event_id`),
  ADD KEY `group_id` (`group_id`);

--
-- Indexes for table `counselor`
--
ALTER TABLE `counselor`
  ADD PRIMARY KEY (`c_id`);

--
-- Indexes for table `counselor_availability`
--
ALTER TABLE `counselor_availability`
  ADD PRIMARY KEY (`availability_id`),
  ADD KEY `counselor_availability_ibfk_1` (`counselor_id`);

--
-- Indexes for table `dm`
--
ALTER TABLE `dm`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`event_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`feedback_id`);

--
-- Indexes for table `future_letters`
--
ALTER TABLE `future_letters`
  ADD PRIMARY KEY (`letter_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `post`
--
ALTER TABLE `post`
  ADD PRIMARY KEY (`post_id`);

--
-- Indexes for table `sends`
--
ALTER TABLE `sends`
  ADD PRIMARY KEY (`prev_message_id`,`new_message_id`),
  ADD KEY `new_message_id` (`new_message_id`);

--
-- Indexes for table `sends_between`
--
ALTER TABLE `sends_between`
  ADD PRIMARY KEY (`user_id`,`c_id`,`message_id`),
  ADD KEY `c_id` (`c_id`),
  ADD KEY `message_id` (`message_id`);

--
-- Indexes for table `session_notes`
--
ALTER TABLE `session_notes`
  ADD PRIMARY KEY (`note_id`),
  ADD KEY `session_notes_ibfk_1` (`appointment_id`),
  ADD KEY `session_notes_ibfk_2` (`counselor_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `appointment_numbers` (`appointment_numbers`),
  ADD KEY `users_ibfk_1` (`feedback_id`);

--
-- Indexes for table `user_group`
--
ALTER TABLE `user_group`
  ADD PRIMARY KEY (`group_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `will_receive`
--
ALTER TABLE `will_receive`
  ADD PRIMARY KEY (`feedback_id`,`c_id`),
  ADD KEY `c_id` (`c_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `appointment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `counselor`
--
ALTER TABLE `counselor`
  MODIFY `c_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `counselor_availability`
--
ALTER TABLE `counselor_availability`
  MODIFY `availability_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `future_letters`
--
ALTER TABLE `future_letters`
  MODIFY `letter_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `post`
--
ALTER TABLE `post`
  MODIFY `post_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `session_notes`
--
ALTER TABLE `session_notes`
  MODIFY `note_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `analytics`
--
ALTER TABLE `analytics`
  ADD CONSTRAINT `analytics_ibfk_1` FOREIGN KEY (`feedback_id`) REFERENCES `feedback` (`feedback_id`),
  ADD CONSTRAINT `analytics_ibfk_2` FOREIGN KEY (`c_id`) REFERENCES `counselor` (`c_id`);

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointment_numbers` FOREIGN KEY (`counselor_id`) REFERENCES `counselor` (`c_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `can_create`
--
ALTER TABLE `can_create`
  ADD CONSTRAINT `can_create_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `can_create_ibfk_2` FOREIGN KEY (`post_id`) REFERENCES `post` (`post_id`);

--
-- Constraints for table `can_create_between`
--
ALTER TABLE `can_create_between`
  ADD CONSTRAINT `can_create_between_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `can_create_between_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`),
  ADD CONSTRAINT `can_create_between_ibfk_3` FOREIGN KEY (`group_id`) REFERENCES `user_group` (`group_id`);

--
-- Constraints for table `counselor_availability`
--
ALTER TABLE `counselor_availability`
  ADD CONSTRAINT `counselor_availability_ibfk_1` FOREIGN KEY (`counselor_id`) REFERENCES `counselor` (`c_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dm`
--
ALTER TABLE `dm`
  ADD CONSTRAINT `dm_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `dm_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `future_letters`
--
ALTER TABLE `future_letters`
  ADD CONSTRAINT `future_letters_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `sends`
--
ALTER TABLE `sends`
  ADD CONSTRAINT `sends_ibfk_1` FOREIGN KEY (`prev_message_id`) REFERENCES `dm` (`message_id`),
  ADD CONSTRAINT `sends_ibfk_2` FOREIGN KEY (`new_message_id`) REFERENCES `dm` (`message_id`);

--
-- Constraints for table `sends_between`
--
ALTER TABLE `sends_between`
  ADD CONSTRAINT `sends_between_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `sends_between_ibfk_2` FOREIGN KEY (`c_id`) REFERENCES `counselor` (`c_id`),
  ADD CONSTRAINT `sends_between_ibfk_3` FOREIGN KEY (`message_id`) REFERENCES `dm` (`message_id`);

--
-- Constraints for table `session_notes`
--
ALTER TABLE `session_notes`
  ADD CONSTRAINT `session_notes_ibfk_1` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`appointment_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `session_notes_ibfk_2` FOREIGN KEY (`counselor_id`) REFERENCES `counselor` (`c_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
