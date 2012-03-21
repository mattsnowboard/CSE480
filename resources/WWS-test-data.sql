-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 16, 2012 at 08:24 PM
-- Server version: 5.5.8
-- PHP Version: 5.3.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `cse480`
--

--
-- Dumping data for table `player`
--

INSERT INTO `player` (`id`, `username`, `email`, `password`, `last_active`, `total_score`, `birthdate`, `join_date`, `city`, `state`, `country`, `full_name`, `phone`, `is_admin`) VALUES
(1, 'devan', 'saylesd1@msu.edu', '$2a$08$gJvm8Wh2Rb8cCpoeAwqEku7OAGKjmKvmHVAYkL9KICh5sAfJ10/fO', '2012-03-16 13:38:22', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 'matt', 'durakmat@msu.edu', '$2a$08$HS7T2AWfsUaTlNkC6FgyLeSDkeGqnbbqGwKOjsVAmB/GGsqjBwmpC', '2012-03-16 14:44:59', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 'chelsea', 'carrche2@msu.edu', '$2a$08$5o1qmwqNYd0xaNoF2/1bpuC2AdLkZfRakR3dLAwNLd.MrwyQyp7Ii', '2012-03-16 14:45:28', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 'stranger', 'stranger@msu.edu', '$2a$08$dznb3tb.Du3ROYFiVLg0tuIU2zcKKyfBBuHO1yMl/I7ICg9SttA2W', '2012-03-16 14:50:44', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

--
-- Dumping data for table `game`
--

INSERT INTO `game` (`id`, `timestamp`, `word_start_state`, `num_players`, `score1`, `score2`, `player_turn`, `winner_flag`, `word_id`, `player1_id`, `player2_id`, `is_bonus`, `current_state`) VALUES
(1, '2012-03-16 15:18:44', 'a_al_ne', '1', 10, 0, '1', 'playing', 1, 1, NULL, 0, 'abal_ne'),
(2, '2012-03-16 15:18:44', 'aba__oir', '1', 7, 0, '1', 'playing', 2, 3, NULL, 0, 'aba__oir'),
(3, '2012-03-16 15:18:44', 'a_y_ma_', '1', 12, 0, '1', 'playing', 10, 4, NULL, 0, 'a_y_ma_');

--
-- Dumping data for table `challenge`
--

INSERT INTO `challenge` (`id`, `status`, `game_id`, `challenger_id`, `recipient_id`) VALUES
(1, 'pending', NULL, 1, 2),
(2, 'pending', NULL, 4, 3);

--
-- Dumping data for table `guess`
--

INSERT INTO `guess` (`timestamp`, `is_correct`, `word`, `letter`, `is_full_word`, `player_id`, `game_id`) VALUES
('2012-03-16 15:18:44', 0, NULL, 'z', 0, 1, 1),
('2012-03-16 15:18:44', 0, NULL, 'j', 0, 3, 2),
('2012-03-16 15:18:44', 0, NULL, 'w', 0, 4, 3);
