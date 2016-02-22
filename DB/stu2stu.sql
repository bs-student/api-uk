-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Feb 22, 2016 at 08:58 AM
-- Server version: 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `stu2stu`
--

-- --------------------------------------------------------

--
-- Table structure for table `access_token`
--

CREATE TABLE IF NOT EXISTS `access_token` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `expires_at` int(11) DEFAULT NULL,
  `scope` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_B6A2DD685F37A13B` (`token`),
  KEY `IDX_B6A2DD6819EB6921` (`client_id`),
  KEY `IDX_B6A2DD68A76ED395` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=13 ;

--
-- Dumping data for table `access_token`
--

INSERT INTO `access_token` (`id`, `client_id`, `user_id`, `token`, `expires_at`, `scope`) VALUES
(1, 1, NULL, 'Mzk2MTYxNzRmYTQxNjJhZThkOGYxM2UwNDU3MThkYzI3NTExYjdjY2ZhMzhhNmFkOGFmZWM3N2JlMjUyODE1Mg', 1455871866, NULL),
(2, 1, NULL, 'NDY2ODFkMWI1NTE0MTdlNjE4OTQ5NDRhODc5NjE3MWRjNDE3NTcyZjdiYzhkMzNlZWJhMzdiNjYyM2EwZDRhYg', 1455872275, NULL),
(3, 1, NULL, 'ZTI3MjllYmIwMjE4YTQ4Y2VkMDI1ZjYxNzE1NjI2MTQ2OTQwZTM3Y2QyNTUzZmUxMWFmMmMxMzAxOGY5ZDE5YQ', 1455872331, NULL),
(4, 1, NULL, 'YWI5ZGQxOWY5YTYwODQ0ZTA5ZjM0ZDcwODM1NmJlNTAxMzA2ZjMxZmNjOGM1ZGE2NWYzZThhMGNmN2EwMzIyMg', 1455872375, NULL),
(5, 1, NULL, 'MTY1YWZmOGI0NGViNWI4ODlmOWU2NTc2ZTNjYjlmMzVkNWQ5MmYyMGY1NDNhMjJiZGVjZTRmMGNjM2U4NGM0Zg', 1455872446, NULL),
(6, 1, NULL, 'ODM2NjNhMzlhNzM2YTg4MjhlZGJjMTg2ZGNkYTAwMmI0N2M4ZjU1NDRkYjNlYmE3MDEzNmU2M2YxYzQzMmQzOQ', 1455872560, NULL),
(7, 1, NULL, 'Nzc3YzYyM2VkOTNjYTQ1YzI2MmU1ZDc1MjVhNDg4MDY4MTdhMTMxOGFlNDhlMWRiMTMyMGY3YWNjOTM4ZTk4Ng', 1455872757, NULL),
(8, 1, NULL, 'ODE5ZDg4M2UzMTkxY2FiYmUyN2Q5ZjYzNDQ0MWZkMGU1NDlhMWFhYWFkMTliODhkMDI3MDNiZWY5NzFmZDdiOQ', 1455878402, NULL),
(9, 1, NULL, 'MjI4NTMxZTU2MGEzM2JiZDRlN2I0YmNiMzg4MWY2NWY0ZmQ0OWVmMWY2NjI4Yjk2YWI0MWM3ZTA0OTcxZDM1Ng', 1455878693, NULL),
(10, 1, NULL, 'ZDU5ZmIzNzY5YTRlZmU1MWNmMzUxMGI1ZTg0M2I4MmIzYmVmN2NlNTk0NjNjYjQ1ZTRjNDA5OTI1ZWFiMWQyNA', 1455878808, NULL),
(11, 1, NULL, 'MGFiNTYyMGY1ZDQxYTc4YTQyNzUzNDJiMjgyZmJlNTMwMTliMWI5MzdlNDBkZjM0MTFiYjY4YzRkZTFiYjY2Mg', 1455878815, NULL),
(12, 1, NULL, 'ZWIyY2IzZjE1YTZlMTI2NzFmMGU0OTAwODMwMzk4NjkzOTgwNWU4ZWZhZWFmOWM0ZGM0ODNhNTAwZWQxZjU0Ng', 1455878933, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `auth_code`
--

CREATE TABLE IF NOT EXISTS `auth_code` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `redirect_uri` longtext COLLATE utf8_unicode_ci NOT NULL,
  `expires_at` int(11) DEFAULT NULL,
  `scope` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_5933D02C5F37A13B` (`token`),
  KEY `IDX_5933D02C19EB6921` (`client_id`),
  KEY `IDX_5933D02CA76ED395` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `campuses`
--

CREATE TABLE IF NOT EXISTS `campuses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `university_id` int(11) DEFAULT NULL,
  `state_id` int(11) DEFAULT NULL,
  `campus_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_80EFFAE8309D1878` (`university_id`),
  KEY `IDX_80EFFAE85D83CC1` (`state_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=30 ;

--
-- Dumping data for table `campuses`
--

INSERT INTO `campuses` (`id`, `university_id`, `state_id`, `campus_name`) VALUES
(1, 1, 3, 'Madison 2'),
(2, 1, 3, 'Green Bay'),
(3, 2, 4, 'South Carolina'),
(17, 26, 2, 'Dhaka'),
(18, 26, 5, 'Toronto'),
(19, 27, 5, 'waterloo'),
(20, 27, 1, 'toronto'),
(21, 28, 1, 'Alabama'),
(22, 29, 1, 'Chittagong'),
(23, 30, 1, 'US'),
(24, 30, 5, 'UK'),
(25, 31, 2, 'Dhaka'),
(26, 32, 5, 'Horazon'),
(28, 34, 5, 'Khulna'),
(29, 35, 1, 'Sylhet');

-- --------------------------------------------------------

--
-- Table structure for table `client`
--

CREATE TABLE IF NOT EXISTS `client` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `random_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `redirect_uris` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT '(DC2Type:array)',
  `secret` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `allowed_grant_types` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT '(DC2Type:array)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `client`
--

INSERT INTO `client` (`id`, `random_id`, `redirect_uris`, `secret`, `allowed_grant_types`) VALUES
(1, '1t8rtqj36wkgcw4k8wko4wo808ksok0w8wssow4k8sgw08osw8', 'a:1:{i:0;s:51:"http://localhost:8080/StmfonyReal2/web/app_dev.php/";}', '3pcvjgjoqekgwgkoc0gss4cggkoo000o40ggc8ok44ksc4ckk0', 'a:5:{i:0;s:8:"password";i:1;s:13:"refresh_token";i:2;s:18:"client_credentials";i:3;s:18:"authorization_code";i:4;s:42:"http://platform.local/grants/social_plugin";}');

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE IF NOT EXISTS `countries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `country_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `country_code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `country_currency` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `country_currency_short` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `countries`
--

INSERT INTO `countries` (`id`, `country_name`, `country_code`, `country_currency`, `country_currency_short`) VALUES
(1, 'United States', 'US', 'US Dollars ($)', '$'),
(2, 'Canada', 'CA', 'CAN Dollars ($)', 'CAN $');

-- --------------------------------------------------------

--
-- Table structure for table `referrals`
--

CREATE TABLE IF NOT EXISTS `referrals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `referral_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=10 ;

--
-- Dumping data for table `referrals`
--

INSERT INTO `referrals` (`id`, `referral_name`) VALUES
(1, 'Student'),
(2, 'Teacher'),
(3, 'University'),
(4, 'Flyer'),
(5, 'Search Engine'),
(6, 'Blog'),
(7, 'Tell A Friend Email'),
(8, 'Facebook'),
(9, 'Other');

-- --------------------------------------------------------

--
-- Table structure for table `refresh_token`
--

CREATE TABLE IF NOT EXISTS `refresh_token` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `expires_at` int(11) DEFAULT NULL,
  `scope` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_C74F21955F37A13B` (`token`),
  KEY `IDX_C74F219519EB6921` (`client_id`),
  KEY `IDX_C74F2195A76ED395` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `states`
--

CREATE TABLE IF NOT EXISTS `states` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `country_id` int(11) DEFAULT NULL,
  `state_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `state_short_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_31C2774DF92F3E70` (`country_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

--
-- Dumping data for table `states`
--

INSERT INTO `states` (`id`, `country_id`, `state_name`, `state_short_name`) VALUES
(1, 1, 'Alabama', 'AL'),
(2, 1, 'Alaska', 'AK'),
(3, 1, 'Wisconsin', 'WI'),
(4, 1, 'South Carolina', 'SC'),
(5, 2, 'Ontario', 'ON');

-- --------------------------------------------------------

--
-- Table structure for table `universities`
--

CREATE TABLE IF NOT EXISTS `universities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `referral_id` int(11) DEFAULT NULL,
  `university_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `university_url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `university_status` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Activated',
  PRIMARY KEY (`id`),
  KEY `IDX_E36065DE3CCAA4B7` (`referral_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=36 ;

--
-- Dumping data for table `universities`
--

INSERT INTO `universities` (`id`, `referral_id`, `university_name`, `university_url`, `university_status`) VALUES
(1, 5, 'University of Wisconsinw', 'http://www.wisconsin.com', 'Activated'),
(2, 3, 'University of Carolinaa', 'abcd', 'Activated'),
(26, 4, 'University of Dhaka', 'http://du.ac.bd', 'Activated'),
(27, 2, 'University of waterloo', 'http://waterloo', 'Activated'),
(28, 2, 'University of Alabamaaa', 'ttp://Alabama', 'Activated'),
(29, 1, 'University of Chittagong', 'http://uni.chittagong', 'Activated'),
(30, 1, 'University of Massachusets', 'http://ms', 'Activated'),
(31, 1, 'University of Science & Technology', 'http://sc.tech', 'Activated'),
(32, 5, 'University of Technology', 'http://tect.du', 'Activated'),
(34, 2, 'Massachusets University', 'http://uni.edu', 'Activated'),
(35, 4, 'Science University ', 'http://sc.edu', 'Activated');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `referral_id` int(11) DEFAULT NULL,
  `campus_id` int(11) DEFAULT NULL,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `username_canonical` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email_canonical` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `salt` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `locked` tinyint(1) NOT NULL,
  `expired` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  `confirmation_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password_requested_at` datetime DEFAULT NULL,
  `roles` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT '(DC2Type:array)',
  `credentials_expired` tinyint(1) NOT NULL,
  `credentials_expire_at` datetime DEFAULT NULL,
  `full_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `google_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `google_email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `google_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `facebook_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `facebook_email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `facebook_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `registration_status` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_1483A5E992FC23A8` (`username_canonical`),
  UNIQUE KEY `UNIQ_1483A5E9A0D96FBF` (`email_canonical`),
  KEY `IDX_1483A5E93CCAA4B7` (`referral_id`),
  KEY `IDX_1483A5E9AF5D55E1` (`campus_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=14 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `referral_id`, `campus_id`, `username`, `username_canonical`, `email`, `email_canonical`, `enabled`, `salt`, `password`, `last_login`, `locked`, `expired`, `expires_at`, `confirmation_token`, `password_requested_at`, `roles`, `credentials_expired`, `credentials_expire_at`, `full_name`, `google_id`, `google_email`, `google_token`, `facebook_id`, `facebook_email`, `facebook_token`, `registration_status`) VALUES
(1, 4, 17, 'SujitGhosh', 'sujitghosh', 'sujit.sync@gmail.com', 'sujit.sync@gmail.com', 1, '1xx6ophe4r280wkkcssck08wo4k8s48', '', NULL, 0, 0, NULL, NULL, NULL, 'a:1:{i:0;s:16:"ROLE_NORMAL_USER";}', 0, NULL, 'Sujit Ghosh', '107978652921474103351', 'sujit.sync@gmail.com', 'ya29.jQKIvJwXndW5CaJ6wZu5bk8ySGjafmcbcW6E5N9toeNzSCpXVIR9t5rBhsryhR6uWzWuSw', NULL, NULL, NULL, 'complete'),
(12, 4, 21, 'sujit', 'sujit', 'sujit.bit.0329@gmail.com', 'sujit.bit.0329@gmail.com', 1, 'rhH(kg5M7mHnGBVsD-58', '24963fd1f10abeefe0273076ef2c84eb', NULL, 0, 0, NULL, 'uXY8qwKsGoF_uTdc2VrXuEl6ahVDHTk3GOfD1jJAsNo', NULL, 'a:1:{i:0;s:16:"ROLE_NORMAL_USER";}', 0, NULL, 'Sujit', NULL, NULL, NULL, NULL, NULL, NULL, 'complete'),
(13, 4, 21, 'sujit2', 'sujit2', 'sujit.bit.0329@gmail.com2', 'sujit.bit.0329@gmail.com2', 0, 'FxggC(2k2M4th5VfXcyC', 'bf9d9c40bd603a73cac7bb1572d1ee13', NULL, 0, 0, NULL, 'P-AxFz0oy10hJVoqZpXkfDsU8i7L9OaNtHsmush8tJE', NULL, 'a:1:{i:0;s:16:"ROLE_NORMAL_USER";}', 0, NULL, 'Sujit', NULL, NULL, NULL, NULL, NULL, NULL, 'complete');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `access_token`
--
ALTER TABLE `access_token`
  ADD CONSTRAINT `FK_B6A2DD6819EB6921` FOREIGN KEY (`client_id`) REFERENCES `client` (`id`),
  ADD CONSTRAINT `FK_B6A2DD68A76ED395` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `auth_code`
--
ALTER TABLE `auth_code`
  ADD CONSTRAINT `FK_5933D02C19EB6921` FOREIGN KEY (`client_id`) REFERENCES `client` (`id`),
  ADD CONSTRAINT `FK_5933D02CA76ED395` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `campuses`
--
ALTER TABLE `campuses`
  ADD CONSTRAINT `FK_80EFFAE8309D1878` FOREIGN KEY (`university_id`) REFERENCES `universities` (`id`),
  ADD CONSTRAINT `FK_80EFFAE85D83CC1` FOREIGN KEY (`state_id`) REFERENCES `states` (`id`);

--
-- Constraints for table `refresh_token`
--
ALTER TABLE `refresh_token`
  ADD CONSTRAINT `FK_C74F219519EB6921` FOREIGN KEY (`client_id`) REFERENCES `client` (`id`),
  ADD CONSTRAINT `FK_C74F2195A76ED395` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `states`
--
ALTER TABLE `states`
  ADD CONSTRAINT `FK_31C2774DF92F3E70` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`);

--
-- Constraints for table `universities`
--
ALTER TABLE `universities`
  ADD CONSTRAINT `FK_E36065DE3CCAA4B7` FOREIGN KEY (`referral_id`) REFERENCES `referrals` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `FK_1483A5E93CCAA4B7` FOREIGN KEY (`referral_id`) REFERENCES `referrals` (`id`),
  ADD CONSTRAINT `FK_1483A5E9AF5D55E1` FOREIGN KEY (`campus_id`) REFERENCES `campuses` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
