-- phpMyAdmin SQL Dump
-- version 4.0.10.7
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: Dec 03, 2015 at 07:54 PM
-- Server version: 5.6.27
-- PHP Version: 5.4.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `pickacha_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `championList`
--

CREATE TABLE IF NOT EXISTS `championList` (
  `name` varchar(20) NOT NULL,
  `rating` int(11) NOT NULL,
  `similar1` varchar(20) NOT NULL,
  `similar2` varchar(20) NOT NULL,
  `similar3` varchar(20) NOT NULL,
  `winrate` decimal(14,1) DEFAULT NULL,
  `numRatings` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `championList`
--

INSERT INTO `championList` (`name`, `rating`, `similar1`, `similar2`, `similar3`, `winrate`, `numRatings`) VALUES
('Aatrox', 3, 'Tryndamere', 'Jax', 'JarvanIV', '51.6', 1),
('Ahri', 3, 'Akali', 'Leblanc', 'Kassadin', '53.4', 1),
('Akali', 3, 'Ahri', 'Diana', 'Katarina', '47.6', 1),
('Alistar', 3, 'TahmKench', 'Leona', 'Braum', '49.3', 1),
('Amumu', 3, 'Sejuani', 'Nautilus', 'Malphite', '53.8', 1),
('Anivia', 3, 'Brand', 'Karthus', 'Cassiopeia', '54.5', 1),
('Ashe', 3, 'Varus', 'Jinx', 'Caitlyn', '49.4', 1),
('Azir', 3, 'Heimerdinger', 'Zyra', 'Xerath', '46.0', 1),
('Bard', 3, 'Lulu', 'Karma', 'Thresh', '47.1', 1),
('Blitzcrank', 3, 'Thresh', 'Leona', 'Nautilus', '52.5', 1),
('Brand', 3, 'Xerath', 'Anivia', 'Annie', '55.7', 1),
('Braum', 3, 'Alistar', 'Leona', 'TahmKench', '50.2', 1),
('Caitlyn', 3, 'Varus', 'Ashe', 'Jinx', '47.3', 1),
('Cassiopeia', 3, 'Syndra', 'Ryze', 'Karthus', '49.0', 1),
('Chogath', 3, 'DrMundo', 'Singed', 'Malphite', '50.1', 1),
('Corki', 3, 'Ezreal', 'KogMaw', 'Lucian', '50.3', 1),
('Darius', 3, 'Garen', 'Riven', 'Renekton', '49.5', 1),
('Diana', 3, 'Akali', 'Ekko', 'Fizz', '50.5', 1),
('Draven', 3, 'Graves', 'Caitlyn', 'Lucian', '50.4', 5),
('DrMundo', 3, 'Chogath', 'Singed', 'Zac', '55.4', 1),
('Ekko', 3, 'Diana', 'Fizz', 'Zilean', '48.5', 1),
('Elise', 3, 'Evelynn', 'Nidalee', 'Jayce', '48.5', 1),
('Evelynn', 3, 'Diana', 'Elise', 'Shaco', '48.8', 1),
('Ezreal', 3, 'Corki', 'KogMaw', 'Lucian', '48.0', 1),
('Fiddlesticks', 3, 'Kennen', 'Morgana', 'Zyra', '46.6', 1),
('Fiora', 3, 'Riven', 'Irelia', 'Jax', '51.0', 1),
('Fizz', 3, 'Diana', 'Akali', 'Kassadin', '50.8', 1),
('Galio', 3, 'Swain', 'Gragas', 'Chogath', '50.1', 1),
('Gangplank', 3, 'Pantheon', 'Yorick', 'Tryndamere', '50.4', 1),
('Garen', 3, 'Darius', 'Renekton', 'Shyvana', '50.7', 1),
('Gnar', 3, 'Olaf', 'Shyvana', 'Jayce', '50.4', 1),
('Gragas', 3, 'Nautilus', 'Galio', 'Maokai', '46.4', 1),
('Graves', 3, 'Lucian', 'Draven', 'Urgot', '50.9', 1),
('Hecarim', 3, 'Shyvana', 'Skarner', 'Renekton', '52.0', 1),
('Heimerdinger', 3, 'Azir', 'Zyra', 'Orianna', '49.7', 1),
('Illaoi', 3, 'Darius', 'Garen', 'Renekton', '46.4', 1),
('Irelia', 3, 'Jax', 'XinZhao', 'Fiora', '51.3', 1),
('Janna', 3, 'Nami', 'Karma', 'Soraka', '53.0', 1),
('JarvanIV', 3, 'MonkeyKing', 'Nautilus', 'XinZhao', '48.8', 1),
('Jax', 3, 'Fiora', 'Irelia', 'Poppy', '51.9', 1),
('Jayce', 3, 'Nidalee', 'Leesin', 'Elise', '47.7', 1),
('Jinx', 3, 'Tristana', 'Caitlyn', 'KogMaw', '50.3', 1),
('Kalista', 3, 'Kindred', 'Tristana', 'Lucian', '51.4', 1),
('Karma', 3, 'Orianna', 'Lulu', 'Zilean', '49.9', 1),
('Karthus', 3, 'Cassiopeia', 'Anivia', 'Syndra', '52.1', 1),
('Kassadin', 3, 'Leblanc', 'Fizz', 'Akali', '47.5', 1),
('Katarina', 3, 'Akali', 'Ahri', 'Diana', '48.1', 1),
('Kayle', 2, 'Gnar', 'Lulu', 'Jayce', '53.0', 2),
('Kennen', 3, 'Fiddlesticks', 'Veigar', 'Amumu', '49.0', 1),
('Khazix', 3, 'Zed', 'Rengar', 'Talon', '45.2', 1),
('Kindred', 3, 'Kalista', 'Lucian', 'Vayne', '49.4', 1),
('KogMaw', 3, 'Tristana', 'Twitch', 'Corki', '47.6', 1),
('Leblanc', 3, 'Kassadin', 'Ahri', 'Zed', '46.8', 1),
('LeeSin', 3, 'Riven', 'Zed', 'JarvanIV', '48.3', 1),
('Leona', 3, 'Thresh', 'Braum', 'Alistar', '51.6', 1),
('Lissandra', 3, 'Morgana', 'Orianna', 'Leblanc', '49.4', 1),
('Lucian', 2, 'Graves', 'Ezreal', 'Draven', '48.1', 2),
('Lulu', 3, 'Karma', 'Janna', 'Sona', '49.0', 1),
('Lux', 3, 'Morgana', 'Zyra', 'Xerath', '52.4', 1),
('Malphite', 3, 'Maokai', 'Chogath', 'Amumu', '52.6', 1),
('Malzahar', 3, 'Brand', 'Cassiopeia', 'Swain', '55.5', 1),
('Maokai', 3, 'Malphite', 'Chogath', 'Amumu', '49.6', 1),
('MasterYi', 3, 'Fiora', 'Tryndamere', 'Yasuo', '50.3', 1),
('MissFortune', 3, 'Graves', 'Lucian', 'Varus', '53.1', 1),
('MonkeyKing', 3, 'Vi', 'Riven', 'JarvanIV', '51.5', 1),
('Mordekaiser', 3, 'Vladimir', 'Rumble', 'Yorick', '45.0', 1),
('Morgana', 3, 'Lux', 'Zyra', 'Lissandra', '49.8', 1),
('Nami', 3, 'Sona', 'Janna', 'Zyra', '53.6', 1),
('Nasus', 2, 'Renekton', 'Sion', 'Singed', '51.9', 3),
('Nautilus', 3, 'Blitzcrank', 'Thresh', 'Amumu', '52.2', 1),
('Nidalee', 3, 'Jayce', 'Elise', 'Ekko', '44.1', 1),
('Nocturne', 3, 'Vi', 'Hecarim', 'RekSai', '50.5', 1),
('Nunu', 3, 'Galio', 'Blitzcrank', 'Fiddlesticks', '46.4', 1),
('Olaf', 3, 'DrMundo', 'Darius', 'Trundle', '48.7', 1),
('Orianna', 3, 'Syndra', 'Lux', 'Zyra', '47.1', 1),
('Pantheon', 3, 'Talon', 'XinZhao', 'Nocturne', '49.2', 1),
('Poppy', 3, 'Jax', 'Olaf', 'Irelia', '46.8', 1),
('Quinn', 3, 'Vayne', 'Twitch', 'Nidalee', '55.9', 1),
('Rammus', 3, 'Hecarim', 'Skarner', 'Olaf', '55.0', 1),
('RekSai', 3, 'Nocturne', 'Vi', 'Khazix', '49.2', 1),
('Renekton', 3, 'Nasus', 'Riven', 'Shyvana', '50.7', 1),
('Rengar', 3, 'Khazix', 'Shaco', 'LeeSin', '49.2', 1),
('Riven', 3, 'Renekton', 'LeeSin', 'Rengar', '45.9', 1),
('Rumble', 3, 'Elise', 'Mordekaiser', 'Zac', '45.2', 1),
('Ryze', 3, 'Annie', 'Swain', 'Vladimir', '45.4', 1),
('Sejuani', 3, 'Amumu', 'Maokai', 'Hecarim', '52.6', 1),
('Shaco', 3, 'Evelynn', 'Rengar', 'Talon', '49.6', 1),
('Shen', 3, 'Malphite', 'Chogath', 'Zac', '52.5', 1),
('Shyvana', 3, 'Renekton', 'Nasus', 'Udyr', '51.5', 1),
('Singed', 3, 'Volibear', 'Udyr', 'Shyvana', '49.7', 1),
('Sion', 3, 'Maokai', 'Nasus', 'Darius', '52.0', 1),
('Sivir', 3, 'Lucian', 'KogMaw', 'Vayne', '48.4', 1),
('Skarner', 3, 'Hecarim', 'Volibear', 'Olaf', '53.2', 1),
('Sona', 3, 'Nami', 'Janna', 'Soraka', '52.9', 1),
('Soraka', 3, 'Nami', 'Sona', 'Janna', '52.2', 1),
('Swain', 3, 'Fiddlesticks', 'Vladimir', 'Galio', '53.2', 1),
('Syndra', 3, 'Orianna', 'Cassiopeia', 'Brand', '48.7', 1),
('TahmKench', 3, 'Bard', 'Morgana', 'Braum', '50.3', 1),
('Talon', 3, 'Zed', 'Rengar', 'Khazix', '49.9', 1),
('Taric', 3, 'Leona', 'Alistar', 'Morgana', '53.7', 1),
('Teemo', 3, 'Kennen', 'Twitch', 'Ziggs', '49.7', 1),
('Thresh', 3, 'Blitzcrank', 'Nautilus', 'Leona', '49.5', 1),
('Tristana', 3, 'Vayne', 'KogMaw', 'Corki', '50.4', 1),
('Trundle', 4, 'Udyr', 'Warwick', 'Anivia', '55.7', 3),
('Tryndamere', 3, 'Aatrox', 'MasterYi', 'Fiora', '51.1', 1),
('TwistedFate', 3, 'Veigar', 'Orianna', 'Fizz', '52.1', 1),
('Twitch', 3, 'KogMaw', 'Teemo', 'Vayne', '47.5', 1),
('Udyr', 3, 'Volibear', 'Trundle', 'Shyvana', '51.3', 1),
('Urgot', 3, 'Graves', 'Twitch', 'Cassiopeia', '45.9', 1),
('Varus', 3, 'MissFortune', 'Ashe', 'Jinx', '49.5', 1),
('Vayne', 3, 'Quinn', 'Kalista', 'Jinx', '50.9', 1),
('Veigar', 3, 'TwistedFate', 'Orianna', 'Fizz', '47.7', 1),
('Velkoz', 3, 'Xerath', 'Brand', 'Lux', '52.2', 1),
('Vi', 3, 'JarvanIV', 'Nocturne', 'Aatrox', '50.6', 1),
('Viktor', 3, 'Xerath', 'Syndra', 'Lux', '49.8', 1),
('Vladimir', 3, 'Mordekaiser', 'Swain', 'Ryze', '49.0', 1),
('Volibear', 4, 'Singed', 'Udyr', 'DrMundo', '53.1', 3),
('Warwick', 3, 'Trundle', 'Udyr', 'Skarner', '50.5', 1),
('Xerath', 3, 'Lux', 'Viktor', 'Brand', '49.2', 1),
('XinZhao', 3, 'JarvanIV', 'Irelia', 'Aatrox', '47.7', 1),
('Yasuo', 3, 'MasterYi', 'Riven', 'Zed', '49.0', 1),
('Yorick', 3, 'Vladimir', 'Malphite', 'Mordekaiser', '47.4', 1),
('Zac', 3, 'JarvanIV', 'Chogath', 'DrMundo', '50.6', 1),
('Zed', 3, 'Leblanc', 'Khazix', 'Talon', '48.1', 1),
('Ziggs', 3, 'Gragas', 'Lux', 'Syndra', '46.7', 1),
('Zilean', 3, 'Ekko', 'Ziggs', 'Kayle', '50.9', 1),
('Zyra', 3, 'Lux', 'Orianna', 'Morgana', '50.9', 1);

-- --------------------------------------------------------

--
-- Table structure for table `favoritechampions`
--

CREATE TABLE IF NOT EXISTS `favoritechampions` (
  `name` varchar(20) NOT NULL,
  `champion` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `favoritechampions`
--

INSERT INTO `favoritechampions` (`name`, `champion`) VALUES
('lol', 'Azir'),
('lol', 'sdfas'),
('lol', 'hi'),
('david', 'Azir'),
('David1', 'David'),
('David1', 'Ben'),
('David1', 'Jashn'),
('David1', 'Tim'),
('David1', 'Bob'),
('Jashn', 'Riven');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `name` varchar(20) NOT NULL,
  `password` varchar(20) NOT NULL,
  `summoner_name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`name`, `password`, `summoner_name`) VALUES
('', '', ''),
('abc', 'abc', 'tvbuddy'),
('abcd', 'abcd', 'asupi'),
('abcde', 'abc', 'catherineobv'),
('Alex', 'lol', 'ilovepigs'),
('asdf123', 'lol', 'Jashn'),
('Ben', 'lol', 'Tiltlorrd'),
('Bob', 'lol', 'divide0'),
('Bob2', 'lol', 'Jashn'),
('David1', 'lol', 'divide0'),
('David2', 'lol', 'tvbuddy'),
('David3', 'lol', 'divide0'),
('David4', 'lol', 'parkchorong0'),
('David5', 'lol', 'parkchorong0'),
('David6', 'lol', 'Junghwa EXID'),
('David7', 'lol', 'Nam1kaze'),
('David8', 'lol', 'jessicat95'),
('goldenglue3', 'lol', 'goldenglue3'),
('Hello2', 'lol', 'Jashn'),
('Jashn', 'lol', 'Jashn'),
('Jashn1', 'lol', 'Jashn'),
('Jashn123', 'lol', 'Jashn'),
('jashn12345', 'lol', 'Jashn'),
('Jashn45', 'lol', 'Jashn'),
('lel', 'lel', 'lolol'),
('lol', 'lol', 'lol summoner'),
('me', 'what', 'dyrun');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
