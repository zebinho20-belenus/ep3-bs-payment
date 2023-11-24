-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 07, 2023 at 01:34 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 7.2.34

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- Table structure for table `bs_bookings`
--

CREATE TABLE `bs_bookings` (
  `bid` int(10) UNSIGNED NOT NULL,
  `uid` int(10) UNSIGNED NOT NULL,
  `sid` int(10) UNSIGNED NOT NULL,
  `status` varchar(64) NOT NULL COMMENT 'single|subscription|cancelled',
  `status_billing` varchar(64) NOT NULL COMMENT 'pending|paid|cancelled|uncollectable|regular',
  `visibility` varchar(64) NOT NULL COMMENT 'public|private',
  `quantity` int(10) UNSIGNED NOT NULL,
  `created` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_bookings_bills`
--

CREATE TABLE `bs_bookings_bills` (
  `bbid` int(10) UNSIGNED NOT NULL,
  `bid` int(10) UNSIGNED NOT NULL,
  `description` varchar(512) NOT NULL,
  `quantity` int(10) UNSIGNED DEFAULT NULL,
  `time` int(10) UNSIGNED DEFAULT NULL,
  `price` int(10) NOT NULL,
  `rate` int(10) UNSIGNED NOT NULL,
  `gross` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_bookings_meta`
--

CREATE TABLE `bs_bookings_meta` (
  `bmid` int(10) UNSIGNED NOT NULL,
  `bid` int(10) UNSIGNED NOT NULL,
  `key` varchar(64) NOT NULL,
  `value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_events`
--

CREATE TABLE `bs_events` (
  `eid` int(10) UNSIGNED NOT NULL,
  `sid` int(10) UNSIGNED DEFAULT NULL COMMENT 'NULL for all',
  `status` varchar(64) NOT NULL DEFAULT 'enabled' COMMENT 'enabled',
  `datetime_start` datetime NOT NULL,
  `datetime_end` datetime NOT NULL,
  `capacity` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_events_meta`
--

CREATE TABLE `bs_events_meta` (
  `emid` int(10) UNSIGNED NOT NULL,
  `eid` int(10) UNSIGNED NOT NULL,
  `key` varchar(64) NOT NULL,
  `value` text NOT NULL,
  `locale` varchar(8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_options`
--

CREATE TABLE `bs_options` (
  `oid` int(10) UNSIGNED NOT NULL,
  `key` varchar(64) NOT NULL,
  `value` text NOT NULL,
  `locale` varchar(8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `bs_options`
--

INSERT INTO `bs_options` (`oid`, `key`, `value`, `locale`) VALUES
(1, 'client.name.full', 'PFB', NULL),
(2, 'client.name.short', 'PFB', NULL),
(3, 'client.contact.email', 'pfb-clayton@hotmail.com', NULL),
(4, 'client.contact.email.user-notifications', '1', NULL),
(5, 'client.contact.phone', '(03) 9558 9835', NULL),
(6, 'client.website', 'website', NULL),
(7, 'client.website.contact', 'contact page', NULL),
(8, 'client.website.imprint', 'imprint page', NULL),
(9, 'client.website.privacy', 'privacy policy page', NULL),
(10, 'service.name.full', 'Bookingsystem', NULL),
(11, 'service.name.short', 'BS', NULL),
(12, 'service.meta.description', 'description of services', NULL),
(13, 'subject.square.type', 'Court', NULL),
(14, 'subject.square.type.plural', 'Courts', NULL),
(15, 'subject.square.unit', 'Player', NULL),
(16, 'subject.square.unit.plural', 'Players', NULL),
(17, 'subject.type', 'our Facility', NULL),
(18, 'service.user.registration', 'true', NULL),
(19, 'service.user.activation', 'email', NULL),
(20, 'service.calendar.days', '1', NULL),
(21, 'service.website', 'https://pfbadminton.com.au/', NULL),
(22, 'service.branding', 'true', NULL),
(23, 'service.branding.name', 'Initium Technology', NULL),
(24, 'service.branding.website', 'https://initiumtech.com.au/', NULL),
(25, 'service.pricing.visibility', 'public', NULL),
(26, 'service.status-values.billing', 'Pending (pending)\r\nPaid (paid)\r\nCancelled (cancelled)\r\nUncollectable (uncollectable), 'en-US'),
(27, 'client.contact.email.user-notifications', '1', 'en-US'),
(28, 'service.maintenance', 'false', NULL),
(29, 'service.calendar.display-club-exceptions', '0', NULL),
(30, 'client.name.full', 'Pro Fit Badminton', 'en-US'),
(31, 'client.contact.phone', '(03) 9558 9835', 'en-US'),
(32, 'service.name.full', 'Booking system', 'en-US'),
(33, 'company.name.full', 'Initium Technology', 'en-US');

-- --------------------------------------------------------

--
-- Table structure for table `bs_reservations`
--

CREATE TABLE `bs_reservations` (
  `rid` int(10) UNSIGNED NOT NULL,
  `bid` int(10) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `time_start` time NOT NULL,
  `time_end` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


-- Table structure for table `bs_reservations_meta`
--

CREATE TABLE `bs_reservations_meta` (
  `rmid` int(10) UNSIGNED NOT NULL,
  `rid` int(10) UNSIGNED NOT NULL,
  `key` varchar(64) NOT NULL,
  `value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_squares`
--

CREATE TABLE `bs_squares` (
  `sid` int(10) UNSIGNED NOT NULL,
  `name` varchar(64) NOT NULL,
  `status` varchar(64) NOT NULL DEFAULT 'enabled' COMMENT 'disabled|readonly|enabled',
  `priority` float NOT NULL DEFAULT 1,
  `capacity` int(10) UNSIGNED NOT NULL,
  `capacity_heterogenic` tinyint(1) NOT NULL,
  `allow_notes` tinyint(1) NOT NULL DEFAULT 0,
  `time_start` time NOT NULL,
  `time_end` time NOT NULL,
  `time_block` int(10) UNSIGNED NOT NULL,
  `time_block_bookable` int(10) UNSIGNED NOT NULL,
  `time_block_bookable_max` int(10) UNSIGNED DEFAULT NULL,
  `min_range_book` int(10) UNSIGNED DEFAULT 0,
  `range_book` int(10) UNSIGNED DEFAULT NULL,
  `max_active_bookings` int(10) UNSIGNED DEFAULT 0,
  `range_cancel` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `bs_squares` (`sid`, `name`, `status`, `priority`, `capacity`, `capacity_heterogenic`, `allow_notes`, `time_start`, `time_end`, `time_block`, `time_block_bookable`, `time_block_bookable_max`, `min_range_book`, `range_book`, `max_active_bookings`, `range_cancel`) VALUES
(11, '1', 'enabled', 1, 1, 0, 1, '10:00:00', '23:00:00', 1800, 1800, 46800, 0, 4838400, 0, 86400),
(12, '2', 'enabled', 2, 1, 0, 1, '10:00:00', '23:00:00', 1800, 1800, 46800, 0, 4838400, 0, 86400),
(13, '3', 'enabled', 3, 1, 0, 1, '10:00:00', '23:00:00', 1800, 1800, 46800, 0, 4838400, 0, 86400),
(14, '4', 'enabled', 4, 1, 0, 0, '10:00:00', '23:00:00', 1800, 1800, 46800, 0, 4838400, 0, 86400),
(15, '5', 'enabled', 5, 1, 0, 0, '10:00:00', '23:00:00', 1800, 1800, 46800, 0, 4838400, 0, 86400),
(16, '6', 'enabled', 6, 1, 0, 0, '10:00:00', '23:00:00', 1800, 1800, 46800, 0, 4838400, 0, 86400),
(17, '7', 'enabled', 7, 1, 0, 0, '10:00:00', '23:00:00', 1800, 1800, 46800, 0, 4838400, 0, 86400),
(18, '8', 'enabled', 8, 1, 0, 0, '10:00:00', '23:00:00', 1800, 1800, 46800, 0, 4838400, 0, 86400),
(19, '9', 'enabled', 9, 1, 0, 0, '10:00:00', '23:00:00', 1800, 1800, 46800, 0, 4838400, 0, 86400),
(20, 'TT 21', 'enabled', 21, 1, 0, 0, '10:00:00', '23:00:00', 1800, 1800, 46800, 0, 4838400, 0, 86400),
(21, 'TT 22', 'enabled', 22, 1, 0, 0, '10:00:00', '23:00:00', 1800, 1800, 46800, 0, 4838400, 0, 86400),
(22, 'TT 23', 'enabled', 23, 1, 0, 0, '10:00:00', '23:00:00', 1800, 1800, 46800, 0, 4838400, 0, 86400),
(23, 'TT 24', 'enabled', 24, 1, 0, 0, '10:00:00', '23:00:00', 1800, 1800, 46800, 0, 4838400, 0, 86400),
(24, 'TT 22a', 'enabled', 22, 1, 0, 0, '10:00:00', '23:00:00', 3600, 1800, 46800, 0, 4838400, 0, 86400),
(25, 'TT 24a', 'enabled', 24, 1, 0, 0, '10:00:00', '23:00:00', 1800, 1800, 46800, 0, 4838400, 0, 86400),
(26, 'TT 25', 'enabled', 25, 1, 0, 0, '10:00:00', '23:00:00', 1800, 1800, 46800, 0, 4838400, 0, 86400),
(27, 'TT 26', 'enabled', 26, 1, 0, 0, '10:00:00', '23:00:00', 1800, 1800, 46800, 0, 4838400, 0, 86400);

--
-- Table structure for table `bs_squares_coupons`
--

CREATE TABLE `bs_squares_coupons` (
  `scid` int(10) UNSIGNED NOT NULL,
  `sid` int(10) UNSIGNED DEFAULT NULL COMMENT 'NULL for all',
  `code` varchar(64) NOT NULL,
  `date_start` datetime DEFAULT NULL,
  `date_end` datetime DEFAULT NULL,
  `discount_for_booking` int(10) UNSIGNED NOT NULL,
  `discount_for_products` int(10) UNSIGNED NOT NULL,
  `discount_in_percent` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_squares_meta`
--

CREATE TABLE `bs_squares_meta` (
  `smid` int(10) UNSIGNED NOT NULL,
  `sid` int(10) UNSIGNED NOT NULL,
  `key` varchar(64) NOT NULL,
  `value` text NOT NULL,
  `locale` varchar(8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


-- Table structure for table `bs_squares_pricing`
--

CREATE TABLE IF NOT EXISTS `bs_squares_pricing` (
  spid int(10) UNSIGNED NOT NULL,
  sid int(10) UNSIGNED DEFAULT NULL COMMENT ‘NULL for all’,
  priority int(10) UNSIGNED NOT NULL,
  date_start date NOT NULL,
  date_end date NOT NULL,
  day_start tinyint(3) UNSIGNED DEFAULT NULL COMMENT ‘Day of the week’,
  day_end tinyint(3) UNSIGNED DEFAULT NULL,
  time_start time DEFAULT NULL,
  time_end time DEFAULT NULL,
  price int(10) UNSIGNED DEFAULT NULL,
  booking_fee int(10) UNSIGNED DEFAULT NULL,
  rate int(10) UNSIGNED DEFAULT NULL,
  gross tinyint(1) DEFAULT NULL,
  per_time_block int(10) UNSIGNED DEFAULT NULL,
  per_quantity tinyint(1) DEFAULT NULL,
  member varchar(255) DEFAULT NULL
  PRIMARY KEY (`spid`),
  KEY `sid` (`sid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `bs_squares_pricing` (`spid`, `sid`, `priority`, `date_start`, `date_end`, `day_start`, `day_end`, `time_start`, `time_end`, `price`, `booking_fee`, `rate`, `gross`, `per_time_block`, `per_quantity`, `member`) VALUES
(1, NULL, 0, '2023-11-22', '2030-11-22', 0, 4, '10:00:00', '18:00:00', 1800, 100, 0, 1, 3600, NULL, '0'),
(2, NULL, 1, '2023-11-22', '2030-11-22', 0, 4, '18:00:00', '23:00:00', 2600, 100, 0, 1, 3600, NULL, '0'),
(3, NULL, 2, '2023-11-22', '2030-11-22', 5, 6, '10:00:00', '23:00:00', 2600, 100, 0, 1, 3600, NULL, '0'),
(4, NULL, 3, '2023-11-22', '2030-11-22', 0, 6, '10:00:00', '23:00:00', 1200, 100, 19, 1, 3600, NULL, '0');

-- --------------------------------------------------------

--
-- Table structure for table `bs_squares_products`
--

CREATE TABLE `bs_squares_products` (
  `spid` int(10) UNSIGNED NOT NULL,
  `sid` int(10) UNSIGNED DEFAULT NULL COMMENT 'NULL for all',
  `priority` int(10) UNSIGNED NOT NULL,
  `date_start` date DEFAULT NULL,
  `date_end` date DEFAULT NULL,
  `name` varchar(128) NOT NULL,
  `description` text DEFAULT NULL,
  `options` varchar(512) NOT NULL,
  `price` int(10) UNSIGNED NOT NULL,
  `rate` int(10) UNSIGNED NOT NULL,
  `gross` tinyint(1) NOT NULL,
  `locale` varchar(8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_users`
--

CREATE TABLE `bs_users` (
  `uid` int(10) UNSIGNED NOT NULL,
  `alias` varchar(128) NOT NULL,
  `status` varchar(64) NOT NULL DEFAULT 'placeholder' COMMENT 'placeholder|deleted|blocked|disabled|enabled|assist|admin',
  `email` varchar(128) DEFAULT NULL,
  `pw` varchar(256) DEFAULT NULL,
  `login_attempts` tinyint(3) UNSIGNED DEFAULT NULL,
  `login_detent` datetime DEFAULT NULL,
  `last_activity` datetime DEFAULT NULL,
  `last_ip` varchar(64) DEFAULT NULL,
  `created` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--

-- Table structure for table `bs_users_meta`
--

CREATE TABLE `bs_users_meta` (
  `umid` int(10) UNSIGNED NOT NULL,
  `uid` int(10) UNSIGNED NOT NULL,
  `key` varchar(64) NOT NULL,
  `value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `bs_users_meta`
--


--
-- Indexes for table `bs_bookings`
--
ALTER TABLE `bs_bookings`
  ADD PRIMARY KEY (`bid`),
  ADD KEY `sid` (`sid`),
  ADD KEY `uid` (`uid`);

--
-- Indexes for table `bs_bookings_bills`
--
ALTER TABLE `bs_bookings_bills`
  ADD PRIMARY KEY (`bbid`),
  ADD KEY `bid` (`bid`);

--
-- Indexes for table `bs_bookings_meta`
--
ALTER TABLE `bs_bookings_meta`
  ADD PRIMARY KEY (`bmid`),
  ADD KEY `bid` (`bid`),
  ADD KEY `key` (`key`);

--
-- Indexes for table `bs_events`
--
ALTER TABLE `bs_events`
  ADD PRIMARY KEY (`eid`),
  ADD KEY `sid` (`sid`),
  ADD KEY `datetime_start` (`datetime_start`),
  ADD KEY `datetime_end` (`datetime_end`);

--
-- Indexes for table `bs_events_meta`
--
ALTER TABLE `bs_events_meta`
  ADD PRIMARY KEY (`emid`),
  ADD KEY `eid` (`eid`),
  ADD KEY `key` (`key`);

--
-- Indexes for table `bs_options`
--
ALTER TABLE `bs_options`
  ADD PRIMARY KEY (`oid`),
  ADD KEY `key` (`key`);

--
-- Indexes for table `bs_reservations`
--
ALTER TABLE `bs_reservations`
  ADD PRIMARY KEY (`rid`),
  ADD KEY `bid` (`bid`),
  ADD KEY `date` (`date`);

--
-- Indexes for table `bs_reservations_meta`
--
ALTER TABLE `bs_reservations_meta`
  ADD PRIMARY KEY (`rmid`),
  ADD KEY `rid` (`rid`),
  ADD KEY `key` (`key`);

--
-- Indexes for table `bs_squares`
--
ALTER TABLE `bs_squares`
  ADD PRIMARY KEY (`sid`);

--
-- Indexes for table `bs_squares_coupons`
--
ALTER TABLE `bs_squares_coupons`
  ADD PRIMARY KEY (`scid`),
  ADD KEY `sid` (`sid`),
  ADD KEY `code` (`code`);

--
-- Indexes for table `bs_squares_meta`
--
ALTER TABLE `bs_squares_meta`
  ADD PRIMARY KEY (`smid`),
  ADD KEY `sid` (`sid`),
  ADD KEY `key` (`key`);

--
-- Indexes for table `bs_squares_pricing`
--
ALTER TABLE `bs_squares_pricing`
  ADD PRIMARY KEY (`spid`),
  ADD KEY `sid` (`sid`);

--
-- Indexes for table `bs_squares_products`
--
ALTER TABLE `bs_squares_products`
  ADD PRIMARY KEY (`spid`),
  ADD KEY `sid` (`sid`);

--
-- Indexes for table `bs_users`
--
ALTER TABLE `bs_users`
  ADD PRIMARY KEY (`uid`),
  ADD KEY `alias` (`alias`),
  ADD KEY `email` (`email`);

--
-- Indexes for table `bs_users_meta`
--
ALTER TABLE `bs_users_meta`
  ADD PRIMARY KEY (`umid`),
  ADD KEY `key` (`key`),
  ADD KEY `uid` (`uid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bs_bookings`
--
ALTER TABLE `bs_bookings`
  MODIFY `bid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT for table `bs_bookings_bills`
--
ALTER TABLE `bs_bookings_bills`
  MODIFY `bbid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `bs_bookings_meta`
--
ALTER TABLE `bs_bookings_meta`
  MODIFY `bmid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=300;

--
-- AUTO_INCREMENT for table `bs_events`
--
ALTER TABLE `bs_events`
  MODIFY `eid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_events_meta`
--
ALTER TABLE `bs_events_meta`
  MODIFY `emid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_options`
--
ALTER TABLE `bs_options`
  MODIFY `oid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `bs_reservations`
--
ALTER TABLE `bs_reservations`
  MODIFY `rid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1987;

--
-- AUTO_INCREMENT for table `bs_reservations_meta`
--
ALTER TABLE `bs_reservations_meta`
  MODIFY `rmid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_squares`
--
ALTER TABLE `bs_squares`
  MODIFY `sid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `bs_squares_coupons`
--
ALTER TABLE `bs_squares_coupons`
  MODIFY `scid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_squares_meta`
--
ALTER TABLE `bs_squares_meta`
  MODIFY `smid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `bs_squares_pricing`
--
ALTER TABLE `bs_squares_pricing`
  MODIFY `spid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `bs_squares_products`
--
ALTER TABLE `bs_squares_products`
  MODIFY `spid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_users`
--
ALTER TABLE `bs_users`
  MODIFY `uid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `bs_users_meta`
--
ALTER TABLE `bs_users_meta`
  MODIFY `umid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bs_bookings`
--
ALTER TABLE `bs_bookings`
  ADD CONSTRAINT `bs_bookings_ibfk_3` FOREIGN KEY (`sid`) REFERENCES `bs_squares` (`sid`),
  ADD CONSTRAINT `bs_bookings_ibfk_4` FOREIGN KEY (`uid`) REFERENCES `bs_users` (`uid`);

--
-- Constraints for table `bs_bookings_bills`
--
ALTER TABLE `bs_bookings_bills`
  ADD CONSTRAINT `bs_bookings_bills_ibfk_1` FOREIGN KEY (`bid`) REFERENCES `bs_bookings` (`bid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `bs_bookings_meta`
--
ALTER TABLE `bs_bookings_meta`
  ADD CONSTRAINT `bs_bookings_meta_ibfk_1` FOREIGN KEY (`bid`) REFERENCES `bs_bookings` (`bid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `bs_events`
--
ALTER TABLE `bs_events`
  ADD CONSTRAINT `bs_events_ibfk_1` FOREIGN KEY (`sid`) REFERENCES `bs_squares` (`sid`);

--
-- Constraints for table `bs_events_meta`
--
ALTER TABLE `bs_events_meta`
  ADD CONSTRAINT `bs_events_meta_ibfk_1` FOREIGN KEY (`eid`) REFERENCES `bs_events` (`eid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `bs_reservations`
--
ALTER TABLE `bs_reservations`
  ADD CONSTRAINT `bs_reservations_ibfk_1` FOREIGN KEY (`bid`) REFERENCES `bs_bookings` (`bid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `bs_reservations_meta`
--
ALTER TABLE `bs_reservations_meta`
  ADD CONSTRAINT `bs_reservations_meta_ibfk_1` FOREIGN KEY (`rid`) REFERENCES `bs_reservations` (`rid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `bs_squares_coupons`
--
ALTER TABLE `bs_squares_coupons`
  ADD CONSTRAINT `bs_squares_coupons_ibfk_1` FOREIGN KEY (`sid`) REFERENCES `bs_squares` (`sid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `bs_squares_meta`
--
ALTER TABLE `bs_squares_meta`
  ADD CONSTRAINT `bs_squares_meta_ibfk_1` FOREIGN KEY (`sid`) REFERENCES `bs_squares` (`sid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `bs_squares_pricing`
--
ALTER TABLE `bs_squares_pricing`
  ADD CONSTRAINT `bs_squares_pricing_ibfk_1` FOREIGN KEY (`sid`) REFERENCES `bs_squares` (`sid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `bs_squares_products`
--
ALTER TABLE `bs_squares_products`
  ADD CONSTRAINT `bs_squares_products_ibfk_1` FOREIGN KEY (`sid`) REFERENCES `bs_squares` (`sid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `bs_users_meta`
--
ALTER TABLE `bs_users_meta`
  ADD CONSTRAINT `bs_users_meta_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `bs_users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE;
SET FOREIGN_KEY_CHECKS=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
