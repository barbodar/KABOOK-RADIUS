-- phpMyAdmin SQL Dump
-- version 4.6.6deb4
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 17, 2018 at 08:11 AM
-- Server version: 5.5.47-0+deb7u1
-- PHP Version: 7.0.30-0+deb9u1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE DATABASE `kabook-radius` /*!40100 DEFAULT CHARACTER SET latin1 */

CREATE USER 'kabook'@'localhost' IDENTIFIED BY 'kabookpass';
GRANT ALL PRIVILEGES ON kabook-radius.* TO 'kabook'@'localhost' WITH GRANT OPTION;

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kabook-radius`
--

-- --------------------------------------------------------

--
-- Table structure for table `tblOnlineCalls`
--

CREATE TABLE `tblOnlineCalls` (
  `onlineCallsId` bigint(20) NOT NULL,
  `onlineCalls_direction` varchar(10) NOT NULL,
  `onlineCalls_startDate` datetime NOT NULL,
  `onlineCalls_startTime` int(11) NOT NULL,
  `onlineCalls_stopDate` datetime NOT NULL,
  `onlineCalls_stopTime` int(11) NOT NULL,
  `onlineCalls_channel` varchar(50) NOT NULL,
  `onlineCalls_uniqueid` varchar(20) NOT NULL,
  `onlineCalls_callerid` varchar(15) NOT NULL,
  `onlineCalls_calleridname` varchar(25) NOT NULL,
  `onlineCalls_dnid` varchar(15) NOT NULL,
  `onlineCalls_rdnis` varchar(15) NOT NULL,
  `onlineCalls_context` varchar(50) NOT NULL,
  `onlineCalls_extension` varchar(15) NOT NULL,
  `onlineCalls_accountcode` varchar(15) NOT NULL,
  `onlineCalls_threadid` varchar(20) NOT NULL,
  `onlineCalls_confId` varchar(35) NOT NULL,
  `onlineCalls_nasPort` int(11) NOT NULL,
  `onlineCalls_clientAddress` varchar(15) NOT NULL,
  `onlineCalls_carrierAddress` varchar(15) NOT NULL,
  `onlineCalls_hangupCause` tinyint(3) NOT NULL,
  `onlineCalls_totalDuration` int(11) NOT NULL,
  `onlineCalls_billedDuration` int(11) NOT NULL,
  `onlineCalls_disposition` varchar(25) NOT NULL,
  `onlineCalls_isProcessed` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tblOnlineCallsArchive`
--

CREATE TABLE `tblOnlineCallsArchive` (
  `onlineCallsId` bigint(20) NOT NULL,
  `onlineCalls_direction` varchar(10) NOT NULL,
  `onlineCalls_startDate` datetime NOT NULL,
  `onlineCalls_startTime` int(11) NOT NULL,
  `onlineCalls_stopDate` datetime NOT NULL,
  `onlineCalls_stopTime` int(11) NOT NULL,
  `onlineCalls_channel` varchar(50) NOT NULL,
  `onlineCalls_uniqueid` varchar(20) NOT NULL,
  `onlineCalls_callerid` varchar(15) NOT NULL,
  `onlineCalls_calleridname` varchar(25) NOT NULL,
  `onlineCalls_dnid` varchar(15) NOT NULL,
  `onlineCalls_rdnis` varchar(15) NOT NULL,
  `onlineCalls_context` varchar(50) NOT NULL,
  `onlineCalls_extension` varchar(15) NOT NULL,
  `onlineCalls_accountcode` varchar(15) NOT NULL,
  `onlineCalls_threadid` varchar(20) NOT NULL,
  `onlineCalls_confId` varchar(35) NOT NULL,
  `onlineCalls_nasPort` int(11) NOT NULL,
  `onlineCalls_clientAddress` varchar(15) NOT NULL,
  `onlineCalls_carrierAddress` varchar(15) NOT NULL,
  `onlineCalls_hangupCause` tinyint(3) NOT NULL,
  `onlineCalls_totalDuration` int(11) NOT NULL,
  `onlineCalls_billedDuration` int(11) NOT NULL,
  `onlineCalls_disposition` varchar(25) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tblOnlineCalls`
--
ALTER TABLE `tblOnlineCalls`
  ADD PRIMARY KEY (`onlineCallsId`),
  ADD UNIQUE KEY `onlineCalls_uniqueid` (`onlineCalls_uniqueid`);

--
-- Indexes for table `tblOnlineCallsArchive`
--
ALTER TABLE `tblOnlineCallsArchive`
  ADD PRIMARY KEY (`onlineCallsId`),
  ADD UNIQUE KEY `onlineCalls_uniqueid` (`onlineCalls_uniqueid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tblOnlineCalls`
--
ALTER TABLE `tblOnlineCalls`
  MODIFY `onlineCallsId` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `tblOnlineCallsArchive`
--
ALTER TABLE `tblOnlineCallsArchive`
  MODIFY `onlineCallsId` bigint(20) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
