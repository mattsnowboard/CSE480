-- phpMyAdmin SQL Dump
-- version 3.2.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 30, 2012 at 07:28 PM
-- Server version: 5.1.44
-- PHP Version: 5.3.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `CSE480`
--

-- --------------------------------------------------------

--
-- Table structure for table `player`
--

CREATE TABLE IF NOT EXISTS `player` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL,
  `email` varchar(64) NOT NULL,
  `password` varchar(64) NOT NULL,
  `last_active` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `total_score` int(11) DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `join_date` date DEFAULT NULL,
  `city` varchar(64) DEFAULT NULL,
  `state` varchar(64) DEFAULT NULL,
  `country` varchar(64) DEFAULT NULL,
  `first_name` varchar(64) DEFAULT NULL,
  `last_name` varchar(64) CHARACTER SET latin7 COLLATE latin7_estonian_cs DEFAULT NULL,
  `phone` varchar(11) DEFAULT NULL,
  `is_admin` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username_UNIQUE` (`username`),
  UNIQUE KEY `email_UNIQUE` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=25 ;

--
-- Dumping data for table `player`
--

INSERT INTO `player` (`id`, `username`, `email`, `password`, `last_active`, `total_score`, `birthdate`, `join_date`, `city`, `state`, `country`, `first_name`, `last_name`, `phone`, `is_admin`) VALUES
(1, 'devan', 'saylesd1@msu.edu', '$2a$08$gJvm8Wh2Rb8cCpoeAwqEku7OAGKjmKvmHVAYkL9KICh5sAfJ10/fO', '2012-03-16 13:38:22', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 'matt', 'durakmat@msu.edu', '$2a$08$HS7T2AWfsUaTlNkC6FgyLeSDkeGqnbbqGwKOjsVAmB/GGsqjBwmpC', '2012-03-16 14:44:59', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 'chelsea', 'carrche2@msu.edu', '$2a$08$5o1qmwqNYd0xaNoF2/1bpuC2AdLkZfRakR3dLAwNLd.MrwyQyp7Ii', '2012-03-16 14:45:28', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 'stranger', 'stranger@msu.edu', '$2a$08$dznb3tb.Du3ROYFiVLg0tuIU2zcKKyfBBuHO1yMl/I7ICg9SttA2W', '2012-03-16 14:50:44', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(18, 'r', 'r@msu.edu', '$2a$08$PxWxVbgsRyVtahbOzWHKN.UxNjtTlcy1Itmm7edlDKDshj6.pQr.y', '2012-03-30 14:58:38', NULL, '1970-01-01', '2012-03-30', 'r', 'r', 'r', NULL, NULL, NULL, NULL),
(19, 'a', 'a@msu.edu', '$2a$08$/b0mMxMPeD/WeFNc6Z5dbuh1s98V2dRUpZEKn9qgiNp5aGfICB7UG', '2012-03-30 15:06:48', NULL, '2012-03-30', '2012-03-30', 'a', 'a', 'a', NULL, NULL, NULL, NULL),
(20, 'e', 'e@msu.edu', '$2a$08$V6H3ngnja0SjIum2nW3Yo.CyERFwlo8FsZc8mgvHCxWzmqJBjf.Na', '2012-03-30 15:09:02', NULL, '1970-01-01', '2012-03-30', 'e', 'e', 'e', NULL, NULL, NULL, NULL),
(21, 'h', 'h@msu.edu', '$2a$08$OaxayW6pwCmsb.9NFkVNPO2ZntJcH7fwkNR6cBP5qMNEjO5mODlTe', '2012-03-30 15:11:06', NULL, '1970-01-01', '2012-03-30', 'h', 'h', 'h', NULL, NULL, NULL, NULL),
(22, 'j', 'j@msu.edu', '$2a$08$O00rV1UsHTdcw7eIlz5hB.wbfZGh1Vrc0GjzAar8aYqtxrvoJ3noO', '2012-03-30 15:11:34', NULL, '1970-01-01', '2012-03-30', 'j', 'j', 'j', NULL, NULL, NULL, NULL),
(23, 'k', 'k@msu.edu', '$2a$08$Yzp2qpZPa53z42KsvnOrnekhWDZE/o4W5DOKfAc9gdIpcyKVfsnOa', '2012-03-30 15:12:43', NULL, '1970-01-01', '2012-03-30', 'k', 'k', 'k', NULL, NULL, NULL, NULL),
(24, 'g', 'g@msu.edu', '$2a$08$.sBFKMRrQ3.QhH2jVp5p.epNpPf3jqm.xMZM5deAPr76yrz7yBaSm', '2012-03-30 15:26:59', NULL, '1892-01-01', '2012-03-30', 'g', 'g', 'g', NULL, NULL, NULL, NULL);
