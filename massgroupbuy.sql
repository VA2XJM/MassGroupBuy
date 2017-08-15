-- phpMyAdmin SQL Dump
-- version 4.4.13.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 13, 2017 at 11:48 PM
-- Server version: 5.6.26
-- PHP Version: 5.5.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
--;

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

DROP TABLE IF EXISTS `category`;
CREATE TABLE IF NOT EXISTS `category` (
  `cid` int(16) NOT NULL,
  `name` varchar(128) NOT NULL DEFAULT 'NONE',
  `hide` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

DROP TABLE IF EXISTS `items`;
CREATE TABLE IF NOT EXISTS `items` (
  `iid` int(64) NOT NULL,
  `name` varchar(256) NOT NULL DEFAULT 'NONE',
  `details` text NOT NULL,
  `cat` int(16) NOT NULL DEFAULT '0',
  `hide` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `items_price`
--

DROP TABLE IF EXISTS `items_price`;
CREATE TABLE IF NOT EXISTS `items_price` (
  `id` int(128) NOT NULL,
  `iid` int(64) NOT NULL,
  `pid` int(16) NOT NULL,
  `price` decimal(6,2) NOT NULL,
  `price_desc` text NOT NULL,
  `unit` decimal(6,2) NOT NULL,
  `unit_desc` text NOT NULL,
  `unit_price` decimal(6,2) NOT NULL,
  `note` text NOT NULL,
  `url` varchar(512) NOT NULL,
  `last_update` int(24) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `providers`
--

DROP TABLE IF EXISTS `providers`;
CREATE TABLE IF NOT EXISTS `providers` (
  `pid` int(16) NOT NULL,
  `name` varchar(128) NOT NULL DEFAULT 'NONE',
  `website` varchar(128) NOT NULL,
  `address` text NOT NULL,
  `phone` varchar(32) NOT NULL,
  `notes` text NOT NULL,
  `hide` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`cid`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`iid`);

--
-- Indexes for table `items_price`
--
ALTER TABLE `items_price`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `providers`
--
ALTER TABLE `providers`
  ADD PRIMARY KEY (`pid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `cid` int(16) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `iid` int(64) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `items_price`
--
ALTER TABLE `items_price`
  MODIFY `id` int(128) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `providers`
--
ALTER TABLE `providers`
  MODIFY `pid` int(16) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
