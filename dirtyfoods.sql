
-- phpMyAdmin SQL Dump
-- version 2.11.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 08, 2014 at 03:51 AM
-- Server version: 5.1.57
-- PHP Version: 5.2.17

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `a9218869_exam`
--

-- --------------------------------------------------------

--
-- Table structure for table `CUSTOMER`
--

CREATE TABLE `customer` (
  `customer_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(120) COLLATE latin1_general_ci NOT NULL,
  `age` int(11) NOT NULL,
  `birthdate` datetime NOT NULL,
  PRIMARY KEY (`customer_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `CUSTOMER`
--

INSERT INTO `CUSTOMER` VALUES(1, 'Harold Bolingot', 20, '1990-01-02 00:00:00');
INSERT INTO `CUSTOMER` VALUES(2, 'Hadrian Lim', 20, '1990-01-02 00:00:00');
INSERT INTO `CUSTOMER` VALUES(3, 'Dawn Corpuz', 20, '1990-01-02 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `franchise`
--

CREATE TABLE `franchise` (
  `franchise_id` int(11) NOT NULL AUTO_INCREMENT,
  `status` varchar(45) COLLATE latin1_general_ci NOT NULL,
  `startdate` datetime NOT NULL,
  `enddate` datetime DEFAULT NULL,
  `annual_fee` int(11) DEFAULT NULL,
  `OPERATOR_operator_id` int(11) NOT NULL,
  `VENDOR_vendor_id` int(11) NOT NULL,
  `LOCATION_location_id` int(11) NOT NULL,
  `FRANCHISE_TYPE_franchisetype_id` int(11) NOT NULL,
  PRIMARY KEY (`franchise_id`),
  KEY `fk_FRANCHISE_OPERATOR1_idx` (`OPERATOR_operator_id`),
  KEY `fk_FRANCHISE_VENDOR1_idx` (`VENDOR_vendor_id`),
  KEY `fk_FRANCHISE_LOCATION1_idx` (`LOCATION_location_id`),
  KEY `fk_FRANCHISE_FRANCHISE_TYPE1_idx` (`FRANCHISE_TYPE_franchisetype_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `franchise`
--

INSERT INTO `franchise` VALUES(1, '1', '2014-01-01 00:00:00', '2014-12-31 00:00:00', NULL, 1, 1, 2, 1);
INSERT INTO `franchise` VALUES(2, '1', '2014-01-01 00:00:00', '2014-12-31 00:00:00', NULL, 2, 2, 3, 1);
INSERT INTO `franchise` VALUES(3, '1', '2014-01-01 00:00:00', '2014-12-31 00:00:00', NULL, 2, 3, 3, 1);

-- --------------------------------------------------------

--
-- Table structure for table `franchise_type`
--

CREATE TABLE `franchise_type` (
  `franchisetype_id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(45) COLLATE latin1_general_ci NOT NULL,
  `logo` varchar(45) COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`franchisetype_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `franchise_type`
--

INSERT INTO `franchise_type` VALUES(1, 'Dirty Ice Cream', NULL);
INSERT INTO `franchise_type` VALUES(2, 'Taho', NULL);
INSERT INTO `franchise_type` VALUES(3, 'Siomai', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `location`
--

CREATE TABLE `location` (
  `location_id` int(11) NOT NULL AUTO_INCREMENT,
  `district` varchar(45) COLLATE latin1_general_ci NOT NULL,
  `city` varchar(45) COLLATE latin1_general_ci NOT NULL,
  `province` varchar(45) COLLATE latin1_general_ci NOT NULL,
  `population` int(11) NOT NULL,
  PRIMARY KEY (`location_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `location`
--

INSERT INTO `location` VALUES(1, '1st', 'Quezon City', 'NCR', 10000);
INSERT INTO `location` VALUES(2, '1st', 'Pasig', 'NCR', 5000);
INSERT INTO `location` VALUES(3, '1st', 'Manila', 'NCR', 20000);

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE `menu` (
  `menu_id` int(11) NOT NULL AUTO_INCREMENT,
  `price` int(11) NOT NULL,
  `description` varchar(100) COLLATE latin1_general_ci NOT NULL,
  `pax` int(11) NOT NULL,
  `FRANCHISE_TYPE_franchisetype_id` int(11) NOT NULL,
  PRIMARY KEY (`menu_id`),
  KEY `fk_MENU_FRANCHISE_TYPE1_idx` (`FRANCHISE_TYPE_franchisetype_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` VALUES(1, 5, 'Vanilla ice cream', 1, 1);
INSERT INTO `menu` VALUES(2, 12, 'Pork siomai', 4, 3);
INSERT INTO `menu` VALUES(3, 10, 'Small taho', 1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `operator`
--

CREATE TABLE `operator` (
  `operator_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(120) COLLATE latin1_general_ci NOT NULL,
  `age` int(11) NOT NULL,
  `birthdate` datetime NOT NULL,
  `LOCATION_location_id` int(11) NOT NULL,
  PRIMARY KEY (`operator_id`),
  KEY `fk_OPERATOR_LOCATION_idx` (`LOCATION_location_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=9 ;

--
-- Dumping data for table `operator`
--

INSERT INTO `operator` VALUES(1, 'April Domingo', 24, '1990-01-02 00:00:00', 3);
INSERT INTO `operator` VALUES(2, 'Marion Tan', 20, '1990-01-02 00:00:00', 1);
INSERT INTO `operator` VALUES(3, 'Jerelyn Co', 20, '1990-01-02 00:00:00', 1);

-- --------------------------------------------------------

--
-- Table structure for table `promo`
--

CREATE TABLE `promo` (
  `promo_id` int(11) NOT NULL AUTO_INCREMENT,
  `startdate` datetime NOT NULL,
  `enddate` datetime DEFAULT NULL,
  `discount` int(11) DEFAULT NULL,
  `MENU_menu_id` int(11) NOT NULL,
  PRIMARY KEY (`promo_id`),
  KEY `fk_PROMO_MENU1_idx` (`MENU_menu_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `promo`
--

INSERT INTO `promo` VALUES(1, '2014-01-01 00:00:00', '2014-12-31 00:00:00', 30, 1);
INSERT INTO `promo` VALUES(2, '2014-01-01 00:00:00', '2014-12-31 00:00:00', 40, 2);
INSERT INTO `promo` VALUES(3, '2014-01-01 00:00:00', '2014-12-31 00:00:00', 50, 3);

-- --------------------------------------------------------

--
-- Table structure for table `transaction`
--

CREATE TABLE `transaction` (
  `transaction_id` int(11) NOT NULL AUTO_INCREMENT,
  `datetime` datetime NOT NULL,
  `MENU_menu_id` int(11) NOT NULL,
  `VENDOR_vendor_id` int(11) NOT NULL,
  `CUSTOMER_customer_id` int(11) NOT NULL,
  PRIMARY KEY (`transaction_id`),
  KEY `fk_TRANSACTION_MENU1_idx` (`MENU_menu_id`),
  KEY `fk_TRANSACTION_VENDOR1_idx` (`VENDOR_vendor_id`),
  KEY `fk_TRANSACTION_CUSTOMER1_idx` (`CUSTOMER_customer_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `transaction`
--

INSERT INTO `transaction` VALUES(1, '2014-01-01 12:01:00', 1, 1, 1);
INSERT INTO `transaction` VALUES(2, '2014-01-01 01:00:00', 1, 1, 2);
INSERT INTO `transaction` VALUES(3, '2014-01-01 03:00:00', 1, 1, 3);

-- --------------------------------------------------------

--
-- Table structure for table `vendor`
--

CREATE TABLE `vendor` (
  `vendor_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(120) COLLATE latin1_general_ci NOT NULL,
  `age` int(11) NOT NULL,
  `birthdate` datetime NOT NULL,
  `VENDOR_TYPE_vendortype_id` int(11) NOT NULL,
  PRIMARY KEY (`vendor_id`),
  KEY `fk_VENDOR_VENDOR_TYPE1_idx` (`VENDOR_TYPE_vendortype_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `vendor`
--

INSERT INTO `vendor` VALUES(1, 'Luigi del Rosario', 20, '1990-01-02 00:00:00', 1);
INSERT INTO `vendor` VALUES(2, 'Chris Amanse', 20, '1990-01-03 00:00:00', 2);
INSERT INTO `vendor` VALUES(3, 'Ghie Amanse', 20, '1990-01-04 00:00:00', 2);

-- --------------------------------------------------------

--
-- Table structure for table `vendor_type`
--

CREATE TABLE `vendor_type` (
  `vendortype_id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(45) COLLATE latin1_general_ci NOT NULL,
  `wage` int(11) NOT NULL,
  PRIMARY KEY (`vendortype_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `vendor_type`
--

INSERT INTO `vendor_type` VALUES(1, 'Push cart', 10);
INSERT INTO `vendor_type` VALUES(2, 'Bike', 20);
INSERT INTO `vendor_type` VALUES(3, 'Truck', 50);
