-- phpMyAdmin SQL Dump
-- version 4.6.6
-- https://www.phpmyadmin.net/
--
-- Host: mysql19j06.db.hostpoint.internal
-- Erstellungszeit: 11. Jan 2018 um 23:41
-- Server-Version: 10.1.26-MariaDB
-- PHP-Version: 5.6.32

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `chrisc22_kerzenziehen`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tbl_articles`
--

CREATE TABLE `tbl_articles` (
  `articleId` int(10) UNSIGNED NOT NULL,
  `typ` enum('wachs','guss','sand','essen') NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `pricePerQuantity` decimal(8,3) DEFAULT NULL,
  `unit` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tbl_basket`
--

CREATE TABLE `tbl_basket` (
  `basket_id` int(11) NOT NULL,
  `article_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` double NOT NULL,
  `free` tinyint(1) NOT NULL,
  `text` text COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tbl_basket_various`
--

CREATE TABLE `tbl_basket_various` (
  `donation` float NOT NULL,
  `total` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tbl_bible_verses`
--

CREATE TABLE `tbl_bible_verses` (
  `ref` text COLLATE utf8_unicode_ci NOT NULL,
  `verse` text COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tbl_buchung_unused`
--

CREATE TABLE `tbl_buchung_unused` (
  `buchung_id` int(10) UNSIGNED NOT NULL,
  `zeit` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `summe` decimal(8,2) NOT NULL DEFAULT '0.00',
  `bezahlt` decimal(8,2) NOT NULL DEFAULT '0.00',
  `kommentar` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tbl_produkt_unused`
--

CREATE TABLE `tbl_produkt_unused` (
  `produkt_id` int(10) UNSIGNED NOT NULL,
  `produkt_stamm_id` int(10) UNSIGNED NOT NULL,
  `buchung_id` int(10) UNSIGNED NOT NULL,
  `menge` int(10) UNSIGNED NOT NULL,
  `betrag` decimal(8,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tbl__unused`
--

CREATE TABLE `tbl__unused` (
  `kommentar_id` int(10) UNSIGNED NOT NULL,
  `produkt_id` int(10) UNSIGNED NOT NULL,
  `kommentar` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `tbl_articles`
--
ALTER TABLE `tbl_articles`
  ADD PRIMARY KEY (`articleId`);

--
-- Indizes für die Tabelle `tbl_basket`
--
ALTER TABLE `tbl_basket`
  ADD PRIMARY KEY (`basket_id`);

--
-- Indizes für die Tabelle `tbl_buchung_unused`
--
ALTER TABLE `tbl_buchung_unused`
  ADD PRIMARY KEY (`buchung_id`);

--
-- Indizes für die Tabelle `tbl_produkt_unused`
--
ALTER TABLE `tbl_produkt_unused`
  ADD PRIMARY KEY (`produkt_id`),
  ADD KEY `produkt_stamm_id` (`produkt_stamm_id`),
  ADD KEY `buchung_id` (`buchung_id`);

--
-- Indizes für die Tabelle `tbl__unused`
--
ALTER TABLE `tbl__unused`
  ADD PRIMARY KEY (`kommentar_id`),
  ADD KEY `produkt_id` (`produkt_id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `tbl_basket`
--
ALTER TABLE `tbl_basket`
  MODIFY `basket_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=372;
--
-- AUTO_INCREMENT für Tabelle `tbl_buchung_unused`
--
ALTER TABLE `tbl_buchung_unused`
  MODIFY `buchung_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
--
-- AUTO_INCREMENT für Tabelle `tbl_produkt_unused`
--
ALTER TABLE `tbl_produkt_unused`
  MODIFY `produkt_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;
--
-- AUTO_INCREMENT für Tabelle `tbl__unused`
--
ALTER TABLE `tbl__unused`
  MODIFY `kommentar_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `tbl_produkt_unused`
--
ALTER TABLE `tbl_produkt_unused`
  ADD CONSTRAINT `tbl_produkt_unused_ibfk_1` FOREIGN KEY (`produkt_stamm_id`) REFERENCES `tbl_articles` (`articleId`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `tbl_produkt_unused_ibfk_2` FOREIGN KEY (`buchung_id`) REFERENCES `tbl_buchung_unused` (`buchung_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints der Tabelle `tbl__unused`
--
ALTER TABLE `tbl__unused`
  ADD CONSTRAINT `tbl__unused_ibfk_1` FOREIGN KEY (`produkt_id`) REFERENCES `tbl_produkt_unused` (`produkt_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
