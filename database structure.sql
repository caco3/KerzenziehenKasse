-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 01. Nov 2021 um 23:28
-- Server-Version: 10.4.21-MariaDB
-- PHP-Version: 8.0.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `kerzenziehen_kasse`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `articles`
--

CREATE TABLE `articles` (
  `articleId` int(4) NOT NULL,
  `typ` enum('wachs','guss','special','custom') NOT NULL,
  `subtype` enum('normal','floatingCandle','preMade','effect','food') NOT NULL,
  `name` text DEFAULT NULL,
  `pricePerQuantity` decimal(8,3) DEFAULT NULL,
  `unit` enum('Stk.','g','CHF') NOT NULL,
  `package` enum('Stk.','100 g','2 Stk.','6 Stk.','') NOT NULL,
  `image1` text NOT NULL,
  `image2` text NOT NULL,
  `image3` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `basket`
--

CREATE TABLE `basket` (
  `basketEntryId` int(11) NOT NULL,
  `articleId` text COLLATE utf8_unicode_ci NOT NULL,
  `quantity` double NOT NULL,
  `price` double NOT NULL,
  `custom` tinyint(1) NOT NULL,
  `text` text COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `basket_various`
--

CREATE TABLE `basket_various` (
  `donation` float NOT NULL,
  `total` float NOT NULL,
  `bookingId` text COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `bible_verses`
--

CREATE TABLE `bible_verses` (
  `ref` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `verse` text COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `bookings`
--

CREATE TABLE `bookings` (
  `bookingId` int(11) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `total` decimal(8,3) NOT NULL,
  `donation` decimal(8,3) NOT NULL,
  `booking` text COLLATE utf8_unicode_ci NOT NULL,
  `twint` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`articleId`),
  ADD UNIQUE KEY `articleId_2` (`articleId`),
  ADD KEY `articleId` (`articleId`);

--
-- Indizes für die Tabelle `basket`
--
ALTER TABLE `basket`
  ADD PRIMARY KEY (`basketEntryId`),
  ADD UNIQUE KEY `basket_id` (`basketEntryId`),
  ADD KEY `basket_id_2` (`basketEntryId`);

--
-- Indizes für die Tabelle `basket_various`
--
ALTER TABLE `basket_various`
  ADD UNIQUE KEY `donation` (`donation`);

--
-- Indizes für die Tabelle `bible_verses`
--
ALTER TABLE `bible_verses`
  ADD PRIMARY KEY (`ref`),
  ADD KEY `ref` (`ref`),
  ADD KEY `ref_2` (`ref`);

--
-- Indizes für die Tabelle `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`bookingId`),
  ADD UNIQUE KEY `bookingId` (`bookingId`),
  ADD KEY `bookingId_2` (`bookingId`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `basket`
--
ALTER TABLE `basket`
  MODIFY `basketEntryId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `bookings`
--
ALTER TABLE `bookings`
  MODIFY `bookingId` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
