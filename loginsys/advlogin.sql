-- phpMyAdmin SQL Dump
-- version 4.0.6deb1
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 03-07-2014 a las 18:27:08
-- Versión del servidor: 5.5.37-0ubuntu0.13.10.1
-- Versión de PHP: 5.5.3-1ubuntu2.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `advlogin`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `account`
--

CREATE TABLE IF NOT EXISTS `account` (
  `id_account` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `money` int(11) NOT NULL,
  `pin` varchar(4) COLLATE utf8_spanish_ci NOT NULL,
  `active` int(11) NOT NULL,
  PRIMARY KEY (`id_account`),
  KEY `id_account` (`id_account`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `account`
--

INSERT INTO `account` (`id_account`, `id_user`, `money`, `pin`, `active`) VALUES
(12345678, 28, 10000, '1234', 1),
(28257972, 28, 96900, '9473', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `news`
--

CREATE TABLE IF NOT EXISTS `news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(55) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `body` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `author` varchar(55) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `created` date NOT NULL DEFAULT '0000-00-00',
  `active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Volcado de datos para la tabla `news`
--

INSERT INTO `news` (`id`, `title`, `body`, `author`, `created`, `active`) VALUES
(1, 'Welcome to our Client Area!', '&lt;p&gt;Please take a look around&lt;br&gt;&lt;/p&gt;', 'Administrator', '2014-06-17', 0),
(2, 'New users created', 'We welcome our new members to the site. Lets see what can we do for them to stick around.', 'Site Admin', '2014-06-17', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `site_name` varchar(50) DEFAULT NULL,
  `site_email` varchar(40) DEFAULT NULL,
  `site_url` varchar(200) DEFAULT NULL,
  `reg_allowed` tinyint(1) NOT NULL DEFAULT '1',
  `user_limit` tinyint(1) NOT NULL DEFAULT '0',
  `reg_verify` tinyint(1) NOT NULL DEFAULT '0',
  `notify_admin` tinyint(1) NOT NULL DEFAULT '0',
  `auto_verify` tinyint(1) NOT NULL DEFAULT '0',
  `user_perpage` varchar(4) NOT NULL DEFAULT '10',
  `thumb_w` varchar(4) NOT NULL,
  `thumb_h` varchar(4) NOT NULL,
  `logo` varchar(50) DEFAULT NULL,
  `backup` varchar(60) DEFAULT NULL,
  `mailer` enum('PHP','SMTP') NOT NULL DEFAULT 'PHP',
  `smtp_host` varchar(100) DEFAULT NULL,
  `smtp_user` varchar(50) DEFAULT NULL,
  `smtp_pass` varchar(50) DEFAULT NULL,
  `smtp_port` varchar(6) DEFAULT NULL,
  `is_ssl` tinyint(1) NOT NULL DEFAULT '0',
  `version` varchar(5) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `settings`
--

INSERT INTO `settings` (`site_name`, `site_email`, `site_url`, `reg_allowed`, `user_limit`, `reg_verify`, `notify_admin`, `auto_verify`, `user_perpage`, `thumb_w`, `thumb_h`, `logo`, `backup`, `mailer`, `smtp_host`, `smtp_user`, `smtp_pass`, `smtp_port`, `is_ssl`, `version`) VALUES
('Secure Coding Test Site', 'site@mail.com', 'http://localhost/loginsys', 1, 0, 0, 0, 0, '10', '72', '72', 'logo.png', '27-Sep-2013_17-35-49.sql', 'PHP', '', '', '', '0', 0, '2.50');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `transactions`
--

CREATE TABLE IF NOT EXISTS `transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from_account` int(11) NOT NULL,
  `to_account` int(11) NOT NULL,
  `ammount` int(11) NOT NULL,
  `token` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `transaction_date` int(11) NOT NULL,
  `transaction_state` int(11) NOT NULL,
  `transaction_type` int(11) NOT NULL,
  PRIMARY KEY (`id`,`from_account`,`to_account`),
  KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci AUTO_INCREMENT=4 ;

--
-- Volcado de datos para la tabla `transactions`
--

INSERT INTO `transactions` (`id`, `from_account`, `to_account`, `ammount`, `token`, `transaction_date`, `transaction_state`, `transaction_type`) VALUES
(1, 28257972, 12345678, 100, '6eG53acb570e54c', 2014, 1, 1),
(2, 28257972, 12345678, 2000, '6qM53acb570df5c', 2014, 1, 1),
(3, 28257972, 12345678, 1000, 'gJ853acb570e5b7', 2014, 1, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL,
  `password` varchar(32) DEFAULT NULL,
  `cookie_id` varchar(64) NOT NULL DEFAULT '0',
  `token` varchar(128) NOT NULL DEFAULT '0',
  `userlevel` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `email` varchar(50) DEFAULT NULL,
  `fname` varchar(50) DEFAULT NULL,
  `lname` varchar(50) DEFAULT NULL,
  `country` varchar(60) DEFAULT NULL,
  `avatar` varchar(60) NOT NULL,
  `ip` varchar(16) NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `lastlogin` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `lastip` varchar(16) DEFAULT NULL,
  `notes` text,
  `newsletter` tinyint(1) NOT NULL DEFAULT '0',
  `active` enum('y','n','b','t') NOT NULL DEFAULT 'n',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=29 ;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `cookie_id`, `token`, `userlevel`, `email`, `fname`, `lname`, `country`, `avatar`, `ip`, `created`, `lastlogin`, `lastip`, `notes`, `newsletter`, `active`) VALUES
(25, 'admin', '841b269afc0f56a8521dc1714eabb661', '0', '0', 9, 'admin@secure.com', 'Administrador', 'Secure Code', NULL, '', '', '2014-06-26 03:30:49', '2014-06-26 18:59:28', '172.16.140.1', NULL, 0, 'y'),
(26, 'felipe', '6fb18570fcbdbf3e7b06065001bf49bb', '0', '0', 9, 'felipe-giraldo@hotmail.com', 'Felipe', 'Giraldo', NULL, 'AVT_5AE98E-2DFEE1-86E032-DC4496-F7EE2F-B152B5.jpg', '', '2014-06-26 03:33:19', '2014-07-03 10:09:28', '172.16.140.1', '', 0, 'y'),
(27, 'alvaro', 'e8d0679694635e8a41183498897ba447', '0', '0', 1, 'ahlopez2003@yahoo.com', 'Alvaro', 'Lopez', NULL, '', '', '2014-06-26 18:59:00', '0000-00-00 00:00:00', NULL, '', 0, 'y'),
(28, 'pipegiraldo', '0635e565b3d76acfd852fe6923a8b038', '0', '0', 1, 'pipegiraldo@gmail.com', 'Felipe', 'Giraldo', NULL, '', '', '2014-06-26 19:04:05', '2014-07-03 11:35:18', '172.16.140.1', NULL, 0, 'y');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_token`
--

CREATE TABLE IF NOT EXISTS `user_token` (
  `token_id` varchar(15) COLLATE utf8_spanish_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  `date_creation` varchar(15) COLLATE utf8_spanish_ci NOT NULL,
  `date_expiration` varchar(15) COLLATE utf8_spanish_ci NOT NULL,
  `used` int(11) NOT NULL,
  PRIMARY KEY (`token_id`,`user_id`),
  UNIQUE KEY `token_id` (`token_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `user_token`
--

INSERT INTO `user_token` (`token_id`, `user_id`, `date_creation`, `date_expiration`, `used`) VALUES
('2tJ53acb570ef7f', 28257972, '1403827568', '1406419568', 0),
('2vC53acb570e345', 28257972, '1403827568', '1406419568', 0),
('2yE53acb570ed4e', 28257972, '1403827568', '1406419568', 0),
('3Gv53acb570e072', 28257972, '1403827568', '1406419568', 0),
('3Hv53acb570e0ad', 28257972, '1403827568', '1406419568', 0),
('3zJ53acb570dc17', 28257972, '1403827568', '1406419568', 0),
('4Cy53acb570edb3', 28257972, '1403827568', '1406419568', 0),
('4Fg53acb570ecf6', 28257972, '1403827568', '1406419568', 0),
('4Gc53acb570e45c', 28257972, '1403827568', '1406419568', 0),
('4jD53acb570eca3', 28257972, '1403827568', '1406419568', 0),
('4Td53acb570ede6', 28257972, '1403827568', '1406419568', 0),
('4Ut53acb570d873', 28257972, '1403827568', '1406419568', 0),
('4Zt53acb570e245', 28257972, '1403827568', '1406419568', 0),
('5Cy53acb570e602', 28257972, '1403827568', '1406419568', 0),
('5dK53acb570e033', 28257972, '1403827568', '1406419568', 0),
('5mU53acb570dafa', 28257972, '1403827568', '1406419568', 0),
('5Nk53acb570da3c', 28257972, '1403827568', '1406419568', 0),
('5Qq53acb570db52', 28257972, '1403827568', '1406419568', 0),
('6Cf53acb570e5de', 28257972, '1403827568', '1406419568', 0),
('6Dx53acb570dacf', 28257972, '1403827568', '1406419568', 0),
('6eG53acb570e54c', 28257972, '1403827568', '1406419568', 1),
('6nB53acb570e111', 28257972, '1403827568', '1406419568', 0),
('6qM53acb570df5c', 28257972, '1403827568', '1406419568', 1),
('6xY53acb570ee8e', 28257972, '1403827568', '1406419568', 0),
('7Bz53acb570e802', 28257972, '1403827568', '1406419568', 0),
('7Jw53acb570e88d', 28257972, '1403827568', '1406419568', 0),
('7Mu53acb570e1e3', 28257972, '1403827568', '1406419568', 0),
('7Ya53acb570e3b6', 28257972, '1403827568', '1406419568', 0),
('8eP53acb570ec70', 28257972, '1403827568', '1406419568', 0),
('8fU53acb570dd13', 28257972, '1403827568', '1406419568', 0),
('8nF53acb570e2d3', 28257972, '1403827568', '1406419568', 0),
('8Sh53acb570e9ee', 28257972, '1403827568', '1406419568', 0),
('8uN53acb570e4a4', 28257972, '1403827568', '1406419568', 0),
('8wF53acb570da13', 28257972, '1403827568', '1406419568', 0),
('9dF53acb570da94', 28257972, '1403827568', '1406419568', 0),
('9fZ53acb570e510', 28257972, '1403827568', '1406419568', 0),
('9jY53acb570eacc', 28257972, '1403827568', '1406419568', 0),
('9kR53acb570eb54', 28257972, '1403827568', '1406419568', 0),
('a2T53acb570e214', 28257972, '1403827568', '1406419568', 0),
('A4r53acb570dc88', 28257972, '1403827568', '1406419568', 0),
('Ac853acb570dfdd', 28257972, '1403827568', '1406419568', 0),
('aV553acb570df40', 28257972, '1403827568', '1406419568', 0),
('B4b53acb570d931', 28257972, '1403827568', '1406419568', 0),
('B4u53acb570ddfd', 28257972, '1403827568', '1406419568', 0),
('Ba753acb570e793', 28257972, '1403827568', '1406419568', 0),
('C5t53acb570e6f7', 28257972, '1403827568', '1406419568', 0),
('Cs753acb570dcf1', 28257972, '1403827568', '1406419568', 0),
('Cw653acb570da65', 28257972, '1403827568', '1406419568', 0),
('cY753acb570e42e', 28257972, '1403827568', '1406419568', 0),
('d9P53acb570ded5', 28257972, '1403827568', '1406419568', 0),
('dF453acb570df71', 28257972, '1403827568', '1406419568', 0),
('E6q53acb570dbdb', 28257972, '1403827568', '1406419568', 0),
('eZ253acb570e3fd', 28257972, '1403827568', '1406419568', 0),
('F8t53acb570e8d6', 28257972, '1403827568', '1406419568', 0),
('gJ853acb570e5b7', 28257972, '1403827568', '1406419568', 1),
('H8k53acb570e147', 28257972, '1403827568', '1406419568', 0),
('J2u53acb570d8a2', 28257972, '1403827568', '1406419568', 0),
('J6n53acb570e183', 28257972, '1403827568', '1406419568', 0),
('Jh753acb570dda9', 28257972, '1403827568', '1406419568', 0),
('Jz853acb570d831', 28257972, '1403827568', '1406419568', 0),
('k7A53acb570ebe8', 28257972, '1403827568', '1406419568', 0),
('kC453acb570e4d7', 28257972, '1403827568', '1406419568', 0),
('M9t53acb570e746', 28257972, '1403827568', '1406419568', 0),
('Na453acb570ec25', 28257972, '1403827568', '1406419568', 0),
('Ne953acb570e977', 28257972, '1403827568', '1406419568', 0),
('nF253acb570deb5', 28257972, '1403827568', '1406419568', 0),
('P5e53acb570e307', 28257972, '1403827568', '1406419568', 0),
('p5T53acb570ef0e', 28257972, '1403827568', '1406419568', 0),
('p6X53acb570dd35', 28257972, '1403827568', '1406419568', 0),
('Pf653acb570e65d', 28257972, '1403827568', '1406419568', 0),
('pP953acb570dfa2', 28257972, '1403827568', '1406419568', 0),
('Q2d53acb570e83f', 28257972, '1403827568', '1406419568', 0),
('Q4n53acb570ee2e', 28257972, '1403827568', '1406419568', 0),
('qT453acb570d853', 28257972, '1403827568', '1406419568', 0),
('R3e53acb570eb14', 28257972, '1403827568', '1406419568', 0),
('r3W53acb570d8f8', 28257972, '1403827568', '1406419568', 0),
('R8s53acb570eba0', 28257972, '1403827568', '1406419568', 0),
('rB353acb570ea3b', 28257972, '1403827568', '1406419568', 0),
('Sb753acb570e627', 28257972, '1403827568', '1406419568', 0),
('sW853acb570eecd', 28257972, '1403827568', '1406419568', 0),
('tB253acb570db27', 28257972, '1403827568', '1406419568', 0),
('tG853acb570de7c', 28257972, '1403827568', '1406419568', 0),
('U9m53acb570e056', 28257972, '1403827568', '1406419568', 0),
('uB753acb570de44', 28257972, '1403827568', '1406419568', 0),
('uB953acb570dcc6', 28257972, '1403827568', '1406419568', 0),
('uK253acb570e281', 28257972, '1403827568', '1406419568', 0),
('uW953acb570df0b', 28257972, '1403827568', '1406419568', 0),
('v8S53acb570d970', 28257972, '1403827568', '1406419568', 0),
('vG953acb570df26', 28257972, '1403827568', '1406419568', 0),
('Vx653acb570dc57', 28257972, '1403827568', '1406419568', 0),
('w8B53acb570e924', 28257972, '1403827568', '1406419568', 0),
('wE453acb570def1', 28257972, '1403827568', '1406419568', 0),
('X3g53acb570db8b', 28257972, '1403827568', '1406419568', 0),
('x4E53acb570e0dd', 28257972, '1403827568', '1406419568', 0),
('X6x53acb570d7f3', 28257972, '1403827568', '1406419568', 0),
('Xj853acb570dd64', 28257972, '1403827568', '1406419568', 0),
('yQ653acb570e7d4', 28257972, '1403827568', '1406419568', 0),
('z4C53acb570e00a', 28257972, '1403827568', '1406419568', 0),
('Ze653acb570d9c0', 28257972, '1403827568', '1406419568', 0),
('zF553acb570e6b6', 28257972, '1403827568', '1406419568', 0);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
