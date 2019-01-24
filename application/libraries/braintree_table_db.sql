-------- IN TEORIA UNA TABELLA PREPARATA PER SALVARE --------

-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 23, 2019 at 02:36 PM
-- Server version: 5.7.23
-- PHP Version: 7.1.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `data_app`
--

-- --------------------------------------------------------

--
-- Table structure for table `braintree_subscriptions`
--

CREATE TABLE `braintree_subscriptions` (
  `cod_id` int(11) NOT NULL,
  `planid` varchar(30) NOT NULL,
  `subId` varchar(80) NOT NULL,
  `statusAbo` varchar(25) NOT NULL,
  `trialPeriod` tinyint(1) NOT NULL,
  `createAt` datetime NOT NULL,
  `nextBillingDate` datetime NOT NULL,
  `price` int(11) NOT NULL,
  `currencyIsoCode` varchar(8) NOT NULL,
  `statusPay` varchar(50) NOT NULL,
  `lastUpdate` datetime NOT NULL,
  `statusNotification` varchar(100) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `braintree_subscriptions`
--
ALTER TABLE `braintree_subscriptions`
  ADD PRIMARY KEY (`cod_id`),
  ADD KEY `subId` (`subId`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `braintree_subscriptions`
--
ALTER TABLE `braintree_subscriptions`
  MODIFY `cod_id` int(11) NOT NULL AUTO_INCREMENT;
