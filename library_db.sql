-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 20, 2025 at 11:04 AM
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
-- Database: `library_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `book_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `authors` varchar(255) DEFAULT NULL,
  `publisher` varchar(255) DEFAULT NULL,
  `publication_year` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`book_id`, `title`, `authors`, `publisher`, `publication_year`) VALUES
(1, 'The Great Gatsby', 'F. Scott Fitzgerald', 'Charles Scribner\'s Sons', 1925),
(2, 'To Kill a Mockingbird', 'Harper Lee', 'J.B. Lippincott & Co.', 1960),
(3, '1984', 'George Orwell', 'Secker & Warburg', 1949),
(4, 'Moby-Dick', 'Herman Melville', 'Richard Bentley', 1851),
(5, 'Pride and Prejudice', 'Jane Austen', 'T. Egerton', 1813);

-- --------------------------------------------------------

--
-- Table structure for table `borrow_records`
--

CREATE TABLE `borrow_records` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `book_id` int(11) NOT NULL,
  `borrow_date` date NOT NULL,
  `return_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `borrow_records`
--

INSERT INTO `borrow_records` (`id`, `username`, `book_id`, `borrow_date`, `return_date`) VALUES
(4, 'hanna', 2, '2025-02-19', '2025-03-01');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') NOT NULL DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `created_at`) VALUES
(1, 'hannah', '$2y$10$/RRoVW1cK39AWfiltqvzzeXZq.cbkYPfUCAFpKpZ2.KxGmJXtcnTe', 'admin', '2025-02-18 11:44:08'),
(7, 'hanna', '$2y$10$F.5B8m4xaJkqQUiKYyjQ8OWYAbqX/qb08AR2lRhf8tz/MWVJFXW.G', 'user', '2025-02-19 12:03:19');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`book_id`);

--
-- Indexes for table `borrow_records`
--
ALTER TABLE `borrow_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `book_id` (`book_id`),
  ADD KEY `borrow_records_ibfk_2` (`username`);

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
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `book_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `borrow_records`
--
ALTER TABLE `borrow_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `borrow_records`
--
ALTER TABLE `borrow_records`
  ADD CONSTRAINT `borrow_records_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`),
  ADD CONSTRAINT `borrow_records_ibfk_2` FOREIGN KEY (`username`) REFERENCES `users` (`username`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
