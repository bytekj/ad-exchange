-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 31, 2012 at 12:40 PM
-- Server version: 5.1.62-0ubuntu0.11.04.1
-- PHP Version: 5.3.5-1ubuntu7.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `adex`
--

DELIMITER $$
--
-- Procedures
--
DROP PROCEDURE IF EXISTS `log`$$
CREATE  PROCEDURE `log`(
OUT id int(64),
IN cpm int(8),
IN client_id int(64),
IN campaign_id int(64),
IN content_id int(64),
IN encoded_ad_name varchar(128) ,
IN client_ip varchar(128),
IN protocol varchar(64),
IN req_filename varchar(128),
IN client_hit_timestamp timestamp,
IN device varchar(64),
IN ad_served_timestamp timestamp,
IN ad_played_timestamp timestamp,
IN ssid int(32)
)
BEGIN
	INSERT INTO `log`
            (
            `id`,
            `cpm`,
            `client_id`,
            `campaign_id`, 
            `content_id`, 
            `encoded_ad_name`, 
            `client_ip`, 
            `protocol`, 
            `req_filename`, 
            `client_hit_timestamp`, 
            `device`, 
            `ad_served_timestamp`, 
            `ad_played_timestamp`,
            `ssid`
            ) 
            VALUES 
            (
            NULL,
            cpm,
            client_id,
            campaign_id,
            content_id ,
            encoded_ad_name ,
            client_ip,
            protocol,
            req_filename,
            client_hit_timestamp,
            device,
            ad_served_timestamp,
            ad_played_timestamp,
            ssid
            );
            SELECT last_insert_id() into id;
END$$

DROP PROCEDURE IF EXISTS `log_ads`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `log_ads`(
out `id` int(64),
in `campaign_id` int(64),
in `content_id` int(64),
in `encoded_ad_name` varchar(128),
in `client_ip` varchar(256),
in `protocol` varchar(64),
in `req_filename` varchar(128),
in `client_hit_timestamp` timestamp,
in `device` varchar(64),
in `ad_served_timestamp` timestamp,
in `ad_played_timestamp` timestamp 
)
BEGIN    
INSERT INTO `log`            
(            
	`id`,             
	`campaign_id`,             
	`content_id`,             
	`encoded_ad_name`,             
	`client_ip`,             
	`protocol`,             
	`req_filename`,             
	`client_hit_timestamp`,             
	`device`,             
	`ad_served_timestamp`,             
	`ad_played_timestamp`            
)             
VALUES             
(            
	NULL,            
	campaign_id,            
	content_id ,            
	encoded_ad_name ,            
	client_ip,            
	protocol,            
	req_filename,            
	client_hit_timestamp,            
	device,            
	ad_served_timestamp,            
	ad_played_timestamp            
);            
SELECT last_insert_id() into id;
END$$


drop procedure if exists add_campaign$$
create procedure add_campaign(
out `out_id` int(64),
in `in_name` varchar(256),
in `in_description` varchar(256),
in `in_advertiser_id` varchar(64),
in `in_status` varchar(32),
in `in_start_date` date,
in `in_end_date` date,
in `in_cpm` int(8),
in `in_ad_spots` int(64)
)
BEGIN
INSERT INTO `campaign`(
	
	`name`, 
	`description`, 
	`advertiser_id`, 
	`status`, 
	`start_date`, 
	`end_date`, 
	`cpm`, 
	`ad_spots`
) 
VALUES (	
	in_name,
	in_description,
	in_advertiser_id,
	in_status,
	in_start_date,
	in_end_date,
	in_cpm,
	in_ad_spots
);
select last_insert_id() into out_id;
END$$		


DELIMITER ;
-- --------------------------------------------------------

--
-- Table structure for table `ad`
--

DROP TABLE IF EXISTS `ad`;
CREATE TABLE IF NOT EXISTS `ad` (
  `id` int(64) NOT NULL AUTO_INCREMENT,
  `campaign_id` int(64) NOT NULL,
  `content_type` varchar(16) DEFAULT NULL,
  `physical_loc` varchar(256) DEFAULT NULL,
  `url` varchar(256) DEFAULT NULL,
  `filename` varchar(64) NOT NULL,
  `userid` varchar(256) DEFAULT NULL,
  `upload_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` enum('approved','rejected','pending') DEFAULT NULL,
  `validity` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `url` (`url`),
  UNIQUE KEY `userid` (`userid`,`url`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `advertiser`
--

DROP TABLE IF EXISTS `advertiser`;
CREATE TABLE IF NOT EXISTS `advertiser` (
  `userid` varchar(128) NOT NULL,
  `wallet_balance` int(64) DEFAULT '0',
  `wallet_total` int(64) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ad_spots`
--

DROP TABLE IF EXISTS `ad_spots`;
CREATE TABLE IF NOT EXISTS `ad_spots` (
  `current_spots` int(64) DEFAULT NULL,
  `available_spots` int(64) DEFAULT NULL,
  `current_rate` int(16) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `bi_users`
--

DROP TABLE IF EXISTS `bi_users`;
CREATE TABLE IF NOT EXISTS `bi_users` (
  `id` int(64) NOT NULL AUTO_INCREMENT,
  `campaign_id` int(64) NOT NULL,
  `country` varchar(64) DEFAULT NULL,
  `city` varchar(128) DEFAULT NULL,
  `client_hit_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `campaign`
--

DROP TABLE IF EXISTS `campaign`;
CREATE TABLE IF NOT EXISTS `campaign` (
  `id` int(64) NOT NULL AUTO_INCREMENT,
  `name` varchar(256) DEFAULT NULL,
  `description` varchar(256) DEFAULT NULL,
  `advertiser_id` varchar(128) NOT NULL,
  `status` enum('active','inactive','paused') NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `cpm` int(16) NOT NULL DEFAULT '0',
  `ad_spots` int(64) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `campaign_parameters`
--

DROP TABLE IF EXISTS `campaign_parameters`;
CREATE TABLE IF NOT EXISTS `campaign_parameters` (
  `id` int(64) NOT NULL AUTO_INCREMENT,
  `campaign_id` int(64) DEFAULT NULL,
  `pref` varchar(64) DEFAULT NULL,
  `value` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `campaign_id` (`campaign_id`,`pref`,`value`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `content`
--

DROP TABLE IF EXISTS `content`;
CREATE TABLE IF NOT EXISTS `content` (
  `id` int(64) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) DEFAULT NULL,
  `genre` varchar(32) DEFAULT NULL,
  `publisher_id` varchar(128) DEFAULT NULL,
  `ad_freq` float DEFAULT '0',
  `region` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- Table structure for table `encoded_ad`
--

DROP TABLE IF EXISTS `encoded_ad`;
CREATE TABLE IF NOT EXISTS `encoded_ad` (
  `id` int(64) NOT NULL AUTO_INCREMENT,
  `original_filename` varchar(128) DEFAULT NULL,
  `config` varchar(128) DEFAULT NULL,
  `encoded_filename` varchar(128) DEFAULT NULL,
  `encode_status` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `form`
--

DROP TABLE IF EXISTS `form`;
CREATE TABLE IF NOT EXISTS `form` (
  `element` varchar(32) NOT NULL DEFAULT '',
  `html` text,
  PRIMARY KEY (`element`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `form` (`element`, `html`) VALUES
('text', '<input type="text" name="@@" value="##" id="$$"/>'),
('password', '<input type="password" name="@@"/>'),
('label', '<label>@@</label>'),
('submit', '<input type="submit" name="@@" value="##" class="button"/>'),
('button', '<button type="button" name="@@" onclick="??" class="button">##</button>'),
('file', '<input type="file" name="@@">'),
('textarea', '<textarea name="@@" id="$$">##</textarea>');

-- --------------------------------------------------------

--
-- Table structure for table `ip_location`
--

DROP TABLE IF EXISTS `ip_location`;
CREATE TABLE IF NOT EXISTS `ip_location` (
  `ip_from` int(64) NOT NULL DEFAULT '0',
  `ip_to` int(64) NOT NULL DEFAULT '0',
  `country_code` varchar(2) DEFAULT NULL,
  `country_name` varchar(64) DEFAULT NULL,
  `region` varchar(128) DEFAULT NULL,
  `city` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`ip_from`,`ip_to`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `log`
--

DROP TABLE IF EXISTS `log`;
CREATE TABLE IF NOT EXISTS `log` (
  `id` int(64) NOT NULL AUTO_INCREMENT,
  `cpm` int(8) DEFAULT NULL,
  `campaign_id` int(64) DEFAULT NULL,
  `content_id` int(64) DEFAULT NULL,
  `encoded_ad_name` varchar(64) DEFAULT NULL,
  `client_ip` varchar(128) DEFAULT NULL,
  `protocol` varchar(32) DEFAULT NULL,
  `req_filename` varchar(128) DEFAULT NULL,
  `client_hit_timestamp` timestamp NULL DEFAULT NULL,
  `device` varchar(256) DEFAULT NULL,
  `ad_served_timestamp` timestamp NULL DEFAULT NULL,
  `ad_played_timestamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `log_users`
--

DROP TABLE IF EXISTS `log_users`;
CREATE TABLE IF NOT EXISTS `log_users` (
  `campaign_id` int(64) DEFAULT NULL,
  `region` varchar(64) DEFAULT NULL,
  `platform` varchar(64) DEFAULT NULL,
  `city` varchar(64) DEFAULT NULL,
  `genre` varchar(64) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `session`
--

DROP TABLE IF EXISTS `session`;
CREATE TABLE IF NOT EXISTS `session` (
  `id` int(64) NOT NULL AUTO_INCREMENT,
  `session` int(64) NOT NULL,
  `active_from` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `session` (`session`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------


--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(64) NOT NULL AUTO_INCREMENT,
  `userid` varchar(256) NOT NULL,
  `password` varchar(64) NOT NULL,
  `usertype` enum('user','admin','agent','superadmin','advertiser','publisher') NOT NULL,
  `registration_date` datetime NOT NULL,
  `last_login` datetime NOT NULL,
  `status` enum('active','deactive','pending') NOT NULL,
  `email` varchar(256) NOT NULL,
  `firstname` varchar(256) DEFAULT NULL,
  `lastname` varchar(256) DEFAULT NULL,
  `brand_name` varchar(128) DEFAULT NULL,
  `brand_logo` varchar(128) DEFAULT NULL,
  `company_name` varchar(64) DEFAULT NULL,
  `company_address` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `userid` (`userid`),
  UNIQUE KEY `firstname` (`firstname`,`lastname`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

DROP TABLE IF EXISTS `platform_master`;
create table if not exists `platform_master`(
 `id` int(16) not null auto_increment,
 `platform_name` varchar(32),
 `platform` varchar(32),
  primary key(`id`),
  unique key(`platform`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
insert into `platform_master` (`platform_name`, `platform`) values
('Iphone/Ipad', 'ios'),
('Mac Desktop', 'osx'),
('Android Phone', 'android'),
('Android Tablet', 'android_tab'),
('Blackberry', 'bb'),
('Java Phones', 'j2me'),
('Windows Phone', 'wphone'),
('Windows Desktop', 'wdesktop');

DROP TABLE IF EXISTS `region_master`;
CREATE TABLE IF NOT EXISTS `region_master`(
 `id` int(16) not null auto_increment,
 `region_name` varchar(32),
 `region` varchar(32),
 primary key(`id`),
 unique key(`region`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
insert into `region_master`(`region_name`, `region`) values
('UK', 'uk'),
('US', 'us'),
('Asia Pacific', 'asiapacific'),
('Latin America', 'latinamerica'),
('Middle East', 'middle east'),
('India', 'india'),
('Australia', 'aus');
/*
-----------------------------------------------------------
-- table structure for city_tier_master
-----------------------------------------------------------
*/
DROP TABLE IF EXISTS `city_tier_master`;
CREATE TABLE IF NOT EXISTS `city_tier_master`(
 `id` int(16) not null auto_increment,
 `tier_name` varchar(32),
 `tier` varchar(32),
 primary key(`id`),
 unique key(`tier`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
insert into `city_tier_master` (`tier_name`, `tier`) values
('Tier1 Cities', 'tier1'),
('Tier2 Cities', 'tier2'),
('Tier3 Cities', 'tier3'),
('Rural areas', 'rural');
/*
-- --------------------------------------------------------

--
-- Table structure for table `genre_master`
--
*/

DROP TABLE IF EXISTS `genre_master`;
CREATE TABLE IF NOT EXISTS `genre_master` (
  `id` int(16) NOT NULL AUTO_INCREMENT,
  `genre_name` varchar(32),
  `genre` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

insert into `genre_master` (`genre_name`, `genre`) values
('Music', 'music'),
('Business','business'),
('Sports','sports'),
('Travel', 'travel'),
('Lifestyle', 'lifestyle'),
('Cricket', 'cricket'),
('Education','education'),
('News', 'news'),
('Science', 'science'),
('Entertainment', 'entertainment'),
('Movies', 'movies');


DROP TABLE IF EXISTS `profiles_master`;
CREATE TABLE IF NOT EXISTS `profiles_master` (
  `id` int(64) NOT NULL AUTO_INCREMENT,
  `video_resolution` enum('160x128','176x144','320x240','352x288','640x480') DEFAULT NULL,
  `video_bit_rate` enum('32','64','128','160','200','256','300','400','512') DEFAULT NULL,
  `video_codec` enum('H264B','H264M') DEFAULT NULL,
  `video_fps` enum('8','10','12','15','20','24','25','30') DEFAULT NULL,
  `audio_bit_rate` enum('8','16','32','64','96','128') DEFAULT NULL,
  `audio_sampling_rate` enum('8000','11025','16000','22050','32000','44100','48000') DEFAULT NULL,
  `audio_channels` enum('mono','stereo') DEFAULT NULL,
  `audio_codec` enum('AAC') DEFAULT NULL,
  `config` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

DROP table if exists `publisher_profiles`;
create table if not exists `publisher_profiles`(
`id` int(64) not null auto_increment,
`publisher_id` int (64) not null,
`profile_id` int(64) not null,
`profile_name` varchar(64) not null,
primary key(`id`),
unique key(`publisher_id`, `profile_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

drop table if exists `content_profile`;
create table if not exists `content_profile`(
id int(64) not null auto_increment,
content_id int(64) not null,
publisher_profile_id int(64) not null,
primary key(`id`),
unique key(`content_id`, `publisher_profile_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
