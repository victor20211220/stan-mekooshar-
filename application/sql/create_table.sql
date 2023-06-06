/*
SQLyog Ultimate v9.50 
MySQL - 5.5.25 : Database - mekoo
*********************************************************************
*/


/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`mekoo` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `mekoo`;

/*Table structure for table `banners` */

DROP TABLE IF EXISTS `banners`;

CREATE TABLE `banners` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `title` varchar(128) NOT NULL,
  `text` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `banners_ibfk_1` (`user_id`),
  CONSTRAINT `banners_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `cart_items` */

DROP TABLE IF EXISTS `cart_items`;

CREATE TABLE `cart_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) unsigned DEFAULT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `job_id` int(10) unsigned DEFAULT NULL,
  `plan_id` int(10) unsigned NOT NULL,
  `itemName` varchar(64) NOT NULL,
  `note` varchar(64) DEFAULT NULL,
  `source` varchar(64) DEFAULT 'directory',
  `section` varchar(64) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(15,2) DEFAULT NULL,
  `details` text,
  PRIMARY KEY (`id`),
  KEY `cart_items_ibfk_1` (`order_id`),
  KEY `cart_items_ibfk_2` (`user_id`),
  KEY `cart_items_ibfk_3` (`job_id`),
  KEY `cart_items_ibfk_4` (`plan_id`),
  CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `cart_orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `cart_items_ibfk_3` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `cart_items_ibfk_4` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8;

/*Table structure for table `cart_orders` */

DROP TABLE IF EXISTS `cart_orders`;

CREATE TABLE `cart_orders` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `amount` decimal(15,2) DEFAULT NULL,
  `delivery` text,
  `deliveryCost` float DEFAULT NULL,
  `discount` varchar(15) DEFAULT NULL,
  `processed` tinyint(3) DEFAULT NULL,
  `isPaid` tinyint(4) NOT NULL DEFAULT '0',
  `dateTimeAdded` datetime DEFAULT NULL,
  `dateTimePaid` datetime DEFAULT NULL,
  `token` varchar(6) NOT NULL,
  `sessionId` varchar(64) NOT NULL,
  `transactionId` varchar(64) NOT NULL,
  `customer` varchar(128) DEFAULT NULL,
  `email` varchar(64) DEFAULT NULL,
  `state` varchar(2) DEFAULT NULL,
  `zip` varchar(5) DEFAULT NULL,
  `city` varchar(128) DEFAULT NULL,
  `address` varchar(128) DEFAULT NULL,
  `phone` varchar(64) DEFAULT NULL,
  `comment` text,
  `paypalLink` text,
  `paypalRecurringStatus` varchar(64) DEFAULT NULL,
  `paypalRecurringProfileId` varchar(64) DEFAULT NULL,
  `paypalMethod` varchar(64) DEFAULT NULL,
  `paypalToken` varchar(64) DEFAULT NULL,
  `paypalEmail` varchar(64) DEFAULT NULL,
  `paypalAck` varchar(64) DEFAULT NULL,
  `paypalMessage` text,
  `paypalInfo` text,
  `paypalCorrelationId` varchar(64) DEFAULT NULL,
  `paypalTimestamp` varchar(64) DEFAULT NULL,
  `position` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cart_orders_ibfk_1` (`user_id`),
  CONSTRAINT `cart_orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8;

/*Table structure for table `certification_authorities` */

DROP TABLE IF EXISTS `certification_authorities`;

CREATE TABLE `certification_authorities` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

/*Table structure for table `certifications` */

DROP TABLE IF EXISTS `certifications`;

CREATE TABLE `certifications` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

/*Table structure for table `companies` */

DROP TABLE IF EXISTS `companies`;

CREATE TABLE `companies` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `createDate` timestamp NULL DEFAULT NULL,
  `email` varchar(64) DEFAULT NULL COMMENT 'email company',
  `domain` varchar(64) DEFAULT NULL,
  `email2` varchar(64) DEFAULT NULL,
  `url` varchar(128) DEFAULT NULL,
  `year` int(4) DEFAULT NULL,
  `type` int(2) DEFAULT NULL,
  `size` int(2) DEFAULT NULL,
  `phone` varchar(32) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `industry` int(3) DEFAULT NULL,
  `companyStatus` int(2) DEFAULT NULL,
  `description` text,
  `avaToken` varchar(16) DEFAULT NULL,
  `coverToken` varchar(16) DEFAULT NULL,
  `followers` int(6) DEFAULT NULL,
  `isAgree` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `company_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8;

/*Table structure for table `company_follow` */

DROP TABLE IF EXISTS `company_follow`;

CREATE TABLE `company_follow` (
  `user_id` int(10) unsigned NOT NULL,
  `company_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`company_id`),
  KEY `company_follow_ibfk_2` (`company_id`),
  CONSTRAINT `company_follow_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `company_follow_ibfk_2` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `company_locations` */

DROP TABLE IF EXISTS `company_locations`;

CREATE TABLE `company_locations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned NOT NULL,
  `descriptions` text,
  `address1` varchar(128) DEFAULT NULL,
  `address2` varchar(128) DEFAULT NULL,
  `city` varchar(64) DEFAULT NULL,
  `state` varchar(64) DEFAULT NULL,
  `zip` varchar(24) DEFAULT NULL,
  `country` varchar(64) DEFAULT NULL,
  `phone1` varchar(32) DEFAULT NULL,
  `phone2` varchar(32) DEFAULT NULL,
  `fax` varchar(32) DEFAULT NULL,
  `employers` tinyint(2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `company_id` (`company_id`),
  CONSTRAINT `company_locations_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `company_post_clicks` */

DROP TABLE IF EXISTS `company_post_clicks`;

CREATE TABLE `company_post_clicks` (
  `user_id` int(10) unsigned NOT NULL,
  `post_id` int(10) unsigned NOT NULL,
  `company_id` int(10) unsigned NOT NULL,
  `createDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`,`post_id`,`company_id`),
  KEY `company_post_clicks_ibfk_2` (`post_id`),
  KEY `company_post_clicks_ibfk_3` (`company_id`),
  CONSTRAINT `company_post_clicks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `company_post_clicks_ibfk_2` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `company_post_clicks_ibfk_3` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `company_post_comments` */

DROP TABLE IF EXISTS `company_post_comments`;

CREATE TABLE `company_post_comments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `post_id` int(10) unsigned NOT NULL,
  `company_id` int(10) unsigned NOT NULL,
  `comment_id` int(10) unsigned NOT NULL,
  `createDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `post_id` (`post_id`),
  KEY `company_id` (`company_id`),
  KEY `comment_id` (`comment_id`),
  CONSTRAINT `company_post_comments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `company_post_comments_ibfk_2` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `company_post_comments_ibfk_3` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `company_post_comments_ibfk_4` FOREIGN KEY (`comment_id`) REFERENCES `timeline_comments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=85 DEFAULT CHARSET=utf8;

/*Table structure for table `company_post_impressions` */

DROP TABLE IF EXISTS `company_post_impressions`;

CREATE TABLE `company_post_impressions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `post_id` int(10) unsigned NOT NULL,
  `company_id` int(10) unsigned NOT NULL,
  `createDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `post_id` (`post_id`),
  KEY `company_id` (`company_id`),
  CONSTRAINT `company_post_impressions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `company_post_impressions_ibfk_2` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `company_post_impressions_ibfk_3` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1654 DEFAULT CHARSET=utf8;

/*Table structure for table `company_post_likes` */

DROP TABLE IF EXISTS `company_post_likes`;

CREATE TABLE `company_post_likes` (
  `user_id` int(10) unsigned NOT NULL,
  `post_id` int(10) unsigned NOT NULL,
  `company_id` int(10) unsigned NOT NULL,
  `timeline_id` int(10) unsigned NOT NULL,
  `createDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`,`post_id`,`company_id`,`timeline_id`),
  KEY `company_post_likes_ibfk_2` (`post_id`),
  KEY `company_post_likes_ibfk_3` (`company_id`),
  KEY `company_post_likes_ibfk_4` (`timeline_id`),
  CONSTRAINT `company_post_likes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `company_post_likes_ibfk_2` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `company_post_likes_ibfk_3` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `company_post_likes_ibfk_4` FOREIGN KEY (`timeline_id`) REFERENCES `timeline` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `company_specialties` */

DROP TABLE IF EXISTS `company_specialties`;

CREATE TABLE `company_specialties` (
  `company_id` int(10) unsigned NOT NULL,
  `specialties_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`company_id`,`specialties_id`),
  KEY `company_specialties_ibfk_2` (`specialties_id`),
  CONSTRAINT `company_specialties_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `company_specialties_ibfk_2` FOREIGN KEY (`specialties_id`) REFERENCES `specialties` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `confirmations` */

DROP TABLE IF EXISTS `confirmations`;

CREATE TABLE `confirmations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sender` bigint(20) unsigned NOT NULL,
  `senderType` tinyint(2) NOT NULL,
  `type` int(10) unsigned NOT NULL,
  `code` varchar(32) NOT NULL,
  `value` varchar(128) NOT NULL,
  `date` datetime NOT NULL,
  `isRefreshed` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `code_idx_1` (`code`),
  KEY `confirmations_ibfk_1` (`sender`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Table structure for table `connection_ban` */

DROP TABLE IF EXISTS `connection_ban`;

CREATE TABLE `connection_ban` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `friend_id` int(10) unsigned NOT NULL,
  `createDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `friend_id` (`friend_id`),
  CONSTRAINT `connection_ban_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `connection_ban_ibfk_2` FOREIGN KEY (`friend_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

/*Table structure for table `connection_search_result` */

DROP TABLE IF EXISTS `connection_search_result`;

CREATE TABLE `connection_search_result` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `profile_id` int(10) unsigned NOT NULL,
  `createDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `profile_id` (`profile_id`),
  CONSTRAINT `connection_search_result_ibfk_2` FOREIGN KEY (`profile_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2185 DEFAULT CHARSET=utf8;

/*Table structure for table `connection_tags` */

DROP TABLE IF EXISTS `connection_tags`;

CREATE TABLE `connection_tags` (
  `connection_id` int(10) unsigned NOT NULL,
  `tag_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`connection_id`,`tag_id`),
  KEY `connection_tags_ibfk_1` (`tag_id`),
  CONSTRAINT `connection_tags_ibfk_1` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `connection_tags_ibfk_2` FOREIGN KEY (`connection_id`) REFERENCES `connections` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `connections` */

DROP TABLE IF EXISTS `connections`;

CREATE TABLE `connections` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `friend_id` int(10) unsigned NOT NULL,
  `createDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `description` text,
  `typeApproved` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - no answer, 1 - approve, 2 - deny',
  `message` text,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `friend_id` (`friend_id`),
  CONSTRAINT `connections_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `connections_ibfk_2` FOREIGN KEY (`friend_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=113 DEFAULT CHARSET=utf8;

/*Table structure for table `files` */

DROP TABLE IF EXISTS `files`;

CREATE TABLE `files` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sender_id` bigint(20) unsigned NOT NULL,
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0',
  `type` int(5) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `token` varchar(16) NOT NULL DEFAULT '',
  `size` bigint(20) unsigned NOT NULL DEFAULT '0',
  `ext` varchar(10) NOT NULL DEFAULT '',
  `isImage` tinyint(1) NOT NULL DEFAULT '0',
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `cropArea` text,
  `group` tinyint(2) DEFAULT NULL,
  `position` int(4) NOT NULL DEFAULT '0',
  `info` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token_idxu` (`token`),
  KEY `parent_id` (`parent_id`,`type`),
  KEY `isImage` (`isImage`),
  KEY `date_idx` (`date`)
) ENGINE=InnoDB AUTO_INCREMENT=1986 DEFAULT CHARSET=utf8;

/*Table structure for table `galleries` */

DROP TABLE IF EXISTS `galleries`;

CREATE TABLE `galleries` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `page_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `enable_watermark` tinyint(1) NOT NULL DEFAULT '0',
  `watermark_text` varchar(45) NOT NULL,
  `watermark_type` varchar(45) NOT NULL,
  `paid_download` tinyint(1) NOT NULL DEFAULT '0',
  `large_image_price` float NOT NULL DEFAULT '0',
  `big_image_price` float NOT NULL DEFAULT '0',
  `small_image_price` float NOT NULL DEFAULT '0',
  `album_price` float NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `galleries_ibfk_1` (`page_id`),
  CONSTRAINT `galleries_ibfk_1` FOREIGN KEY (`page_id`) REFERENCES `pages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=80 DEFAULT CHARSET=utf8;

/*Table structure for table `galleries_items` */

DROP TABLE IF EXISTS `galleries_items`;

CREATE TABLE `galleries_items` (
  `gallery_id` bigint(20) unsigned NOT NULL,
  `file_id` bigint(20) unsigned NOT NULL,
  `position` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`gallery_id`,`file_id`),
  KEY `galleries_items_ibfk_2` (`file_id`),
  CONSTRAINT `galleries_items_ibfk_1` FOREIGN KEY (`gallery_id`) REFERENCES `galleries` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `galleries_items_ibfk_2` FOREIGN KEY (`file_id`) REFERENCES `files` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `galleries_items_info` */

DROP TABLE IF EXISTS `galleries_items_info`;

CREATE TABLE `galleries_items_info` (
  `file_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `text` text,
  `alternative` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`file_id`),
  CONSTRAINT `galleries_items_info_ibfk_1` FOREIGN KEY (`file_id`) REFERENCES `files` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `group_discussion_follow` */

DROP TABLE IF EXISTS `group_discussion_follow`;

CREATE TABLE `group_discussion_follow` (
  `post_id` int(10) unsigned NOT NULL,
  `group_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `createDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`post_id`,`group_id`,`user_id`),
  KEY `group_discussion_follow_ibfk_2` (`group_id`),
  KEY `group_discussion_follow_ibfk_3` (`user_id`),
  CONSTRAINT `group_discussion_follow_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `group_discussion_follow_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `group_discussion_follow_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `group_members` */

DROP TABLE IF EXISTS `group_members`;

CREATE TABLE `group_members` (
  `user_id` int(10) unsigned NOT NULL,
  `group_id` int(10) unsigned NOT NULL,
  `createDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `memberType` tinyint(1) NOT NULL DEFAULT '1',
  `isApproved` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`,`group_id`),
  KEY `group_members_ibfk_2` (`group_id`),
  CONSTRAINT `group_members_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `group_members_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `groups` */

DROP TABLE IF EXISTS `groups`;

CREATE TABLE `groups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `createDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int(10) unsigned NOT NULL,
  `name` varchar(127) NOT NULL,
  `descriptionShort` text,
  `description` text,
  `website` varchar(127) DEFAULT NULL,
  `ownerEmail` varchar(64) NOT NULL,
  `accessType` tinyint(1) NOT NULL DEFAULT '1',
  `discussionControlType` tinyint(1) NOT NULL DEFAULT '1',
  `avaToken` varchar(16) DEFAULT NULL,
  `coverToken` varchar(16) DEFAULT NULL,
  `members` int(6) NOT NULL DEFAULT '0',
  `countDiscussions` int(6) NOT NULL DEFAULT '0',
  `isAgree` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `groups_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

/*Table structure for table `inviteConnections` */

DROP TABLE IF EXISTS `inviteConnections`;

CREATE TABLE `inviteConnections` (
  `user_id` int(10) unsigned NOT NULL,
  `email` varchar(64) NOT NULL,
  `createDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`,`email`),
  CONSTRAINT `inviteConnections_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `job_apply` */

DROP TABLE IF EXISTS `job_apply`;

CREATE TABLE `job_apply` (
  `job_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `createDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `coverLetter` text,
  `isViewed` tinyint(1) NOT NULL DEFAULT '0',
  `isInvited` tinyint(1) NOT NULL DEFAULT '0',
  `isRemovedJobOwner` tinyint(1) NOT NULL DEFAULT '0',
  `isRemovedJobApplicant` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`job_id`,`user_id`),
  KEY `job_apply_ibfk_2` (`user_id`),
  CONSTRAINT `job_apply_ibfk_1` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `job_apply_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `job_skills` */

DROP TABLE IF EXISTS `job_skills`;

CREATE TABLE `job_skills` (
  `job_id` int(10) unsigned NOT NULL,
  `skill_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`job_id`,`skill_id`),
  KEY `job_skills_ibfk_2` (`skill_id`),
  CONSTRAINT `job_skills_ibfk_1` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `job_skills_ibfk_2` FOREIGN KEY (`skill_id`) REFERENCES `skills` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `jobs` */

DROP TABLE IF EXISTS `jobs`;

CREATE TABLE `jobs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `company_id` int(10) unsigned NOT NULL,
  `plan_id` int(10) unsigned NOT NULL DEFAULT '1',
  `createDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `industry` int(3) NOT NULL,
  `country` varchar(2) NOT NULL,
  `state` varchar(64) NOT NULL,
  `city` varchar(128) NOT NULL,
  `title` varchar(128) NOT NULL,
  `description` text NOT NULL,
  `about` text NOT NULL,
  `employment` int(1) NOT NULL,
  `receivedType` int(1) NOT NULL DEFAULT '1',
  `receivedEmail` varchar(64) DEFAULT NULL,
  `expiredDate` datetime NOT NULL,
  `countApplicants` int(6) NOT NULL DEFAULT '0',
  `countNewApplicants` int(6) NOT NULL DEFAULT '0',
  `isRemoved` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `jobs_ibfk_1` (`user_id`),
  KEY `jobs_ibfk_2` (`company_id`),
  KEY `jobs_ibfk_3` (`plan_id`),
  CONSTRAINT `jobs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `jobs_ibfk_2` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `jobs_ibfk_3` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

/*Table structure for table `languages` */

DROP TABLE IF EXISTS `languages`;

CREATE TABLE `languages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

/*Table structure for table `mailList` */

DROP TABLE IF EXISTS `mailList`;

CREATE TABLE `mailList` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(128) NOT NULL,
  `name` varchar(128) DEFAULT NULL,
  `confirmed` tinyint(3) DEFAULT NULL,
  `token` varchar(16) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `mailListAttachments` */

DROP TABLE IF EXISTS `mailListAttachments`;

CREATE TABLE `mailListAttachments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `messageId` int(10) unsigned NOT NULL,
  `alias` varchar(6) NOT NULL,
  `filename` varchar(64) NOT NULL,
  `filesize` int(10) unsigned NOT NULL,
  `position` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `mailListRecipients` */

DROP TABLE IF EXISTS `mailListRecipients`;

CREATE TABLE `mailListRecipients` (
  `messageId` int(10) unsigned NOT NULL,
  `subscriberId` int(10) unsigned NOT NULL,
  `sent` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`messageId`,`subscriberId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `maillistmessages` */

DROP TABLE IF EXISTS `maillistmessages`;

CREATE TABLE `maillistmessages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `dateTime` datetime DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `name` varchar(63) NOT NULL,
  `subject` varchar(128) NOT NULL,
  `message` mediumtext,
  `copy` tinyint(1) DEFAULT NULL,
  `status` varchar(16) DEFAULT NULL,
  `position` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `messages` */

DROP TABLE IF EXISTS `messages`;

CREATE TABLE `messages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `friend_id` int(10) unsigned NOT NULL,
  `createDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `typeForUser` tinyint(1) NOT NULL DEFAULT '0' COMMENT '(inbox, archived, trashed)',
  `typeForFriend` tinyint(1) NOT NULL DEFAULT '0' COMMENT '(inbox, archived, trashed)',
  `subject` varchar(255) DEFAULT NULL,
  `message` text NOT NULL,
  `isUserRemoved` tinyint(1) NOT NULL DEFAULT '0',
  `isFriendRemoved` tinyint(1) NOT NULL DEFAULT '0',
  `isFriendView` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `friend_id` (`friend_id`),
  CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`friend_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=240 DEFAULT CHARSET=utf8;

/*Table structure for table `notifications` */

DROP TABLE IF EXISTS `notifications`;

CREATE TABLE `notifications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `friend_id` int(10) unsigned NOT NULL,
  `createDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '()',
  `notification` text,
  `post_id` int(10) unsigned DEFAULT NULL,
  `skill_id` int(10) DEFAULT NULL,
  `company_id` int(10) unsigned DEFAULT NULL,
  `job_id` int(10) unsigned DEFAULT NULL,
  `isRemoved` tinyint(1) NOT NULL DEFAULT '0',
  `isView` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `friend_id` (`friend_id`),
  KEY `post_id` (`post_id`),
  KEY `notifications_ibfk_5` (`company_id`),
  KEY `notifications_ibfk_6` (`job_id`),
  CONSTRAINT `notifications_ibfk_6` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`friend_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `notifications_ibfk_4` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `notifications_ibfk_5` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=166 DEFAULT CHARSET=utf8;

/*Table structure for table `page_category` */

DROP TABLE IF EXISTS `page_category`;

CREATE TABLE `page_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `subCategory` int(10) unsigned DEFAULT NULL,
  `isBlocked` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `subCategory` (`subCategory`),
  CONSTRAINT `page_category_ibfk_1` FOREIGN KEY (`subCategory`) REFERENCES `page_category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

/*Table structure for table `pages` */

DROP TABLE IF EXISTS `pages`;

CREATE TABLE `pages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `createDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updateDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `typePage` tinyint(1) NOT NULL DEFAULT '1',
  `category` tinyint(10) NOT NULL,
  `alias` varchar(256) NOT NULL,
  `title` varchar(250) NOT NULL,
  `text` text NOT NULL,
  `config` text,
  `isPublic` tinyint(1) NOT NULL DEFAULT '1',
  `isRemoved` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;

/*Table structure for table `plans` */

DROP TABLE IF EXISTS `plans`;

CREATE TABLE `plans` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `createDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `category` tinyint(1) NOT NULL DEFAULT '1',
  `name` varchar(255) NOT NULL,
  `price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `countDays` int(6) NOT NULL DEFAULT '1',
  `isRemoved` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

/*Table structure for table `posts` */

DROP TABLE IF EXISTS `posts`;

CREATE TABLE `posts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `company_id` int(10) unsigned DEFAULT NULL,
  `group_id` int(10) unsigned DEFAULT NULL,
  `school_id` int(10) unsigned DEFAULT NULL,
  `createDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `typePost` tinyint(1) NOT NULL DEFAULT '1',
  `title` varchar(128) NOT NULL,
  `text` text,
  `link` varchar(255) DEFAULT NULL,
  `alias` varchar(10) DEFAULT NULL,
  `isGroupAccept` tinyint(1) DEFAULT NULL,
  `isIncludeImage` tinyint(1) NOT NULL DEFAULT '1',
  `countGroupFollow` tinyint(6) DEFAULT NULL,
  `countImpressions` int(6) NOT NULL DEFAULT '0',
  `countImpressionsUnique` int(6) NOT NULL DEFAULT '0',
  `countClicks` int(6) NOT NULL DEFAULT '0',
  `countInteractions` int(6) NOT NULL DEFAULT '0',
  `countLikes` int(6) NOT NULL DEFAULT '0',
  `countComments` int(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `company_id` (`company_id`),
  KEY `group_id` (`group_id`),
  KEY `school_id` (`school_id`),
  CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `posts_ibfk_5` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `posts_ibfk_6` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `posts_ibfk_7` FOREIGN KEY (`school_id`) REFERENCES `universities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=400 DEFAULT CHARSET=utf8;

/*Table structure for table `profile_certification` */

DROP TABLE IF EXISTS `profile_certification`;

CREATE TABLE `profile_certification` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `certification_id` int(10) unsigned NOT NULL,
  `certification_authority_id` int(10) unsigned NOT NULL,
  `createDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `number` varchar(64) NOT NULL,
  `url` varchar(160) DEFAULT NULL,
  `dateFrom` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `dateTo` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `isCurrent` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `certification_authority` (`certification_authority_id`),
  KEY `user_id` (`user_id`),
  KEY `certification_id` (`certification_id`),
  CONSTRAINT `profile_certification_ibfk_1` FOREIGN KEY (`certification_authority_id`) REFERENCES `certification_authorities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `profile_certification_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `profile_certification_ibfk_3` FOREIGN KEY (`certification_id`) REFERENCES `certifications` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `profile_education` */

DROP TABLE IF EXISTS `profile_education`;

CREATE TABLE `profile_education` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `university_id` int(10) unsigned NOT NULL,
  `createDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `yearFrom` int(4) DEFAULT NULL,
  `yearTo` int(4) DEFAULT NULL,
  `degree` varchar(160) DEFAULT NULL,
  `fieldOfStudy` varchar(160) DEFAULT NULL,
  `grade` varbinary(128) DEFAULT NULL,
  `activitiesAndSocieties` text,
  `description` text,
  `isNotableAlumni` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`,`user_id`),
  KEY `user_id` (`user_id`),
  KEY `university_id` (`university_id`),
  CONSTRAINT `profile_education_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `profile_education_ibfk_2` FOREIGN KEY (`university_id`) REFERENCES `universities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8;

/*Table structure for table `profile_expirience` */

DROP TABLE IF EXISTS `profile_expirience`;

CREATE TABLE `profile_expirience` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `company_id` int(10) unsigned DEFAULT NULL,
  `university_id` int(10) unsigned DEFAULT NULL,
  `createDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `title` varchar(128) DEFAULT NULL,
  `location` varchar(160) NOT NULL,
  `dateFrom` date NOT NULL,
  `dateTo` date DEFAULT NULL,
  `isCurrent` tinyint(1) NOT NULL DEFAULT '0',
  `description` text,
  `links` text COMMENT 'sereliaze',
  `isSchoolMember` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `company_id` (`company_id`),
  KEY `university_id` (`university_id`),
  CONSTRAINT `profile_expirience_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `profile_expirience_ibfk_2` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `profile_expirience_ibfk_3` FOREIGN KEY (`university_id`) REFERENCES `universities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;

/*Table structure for table `profile_language` */

DROP TABLE IF EXISTS `profile_language`;

CREATE TABLE `profile_language` (
  `user_id` int(10) unsigned NOT NULL,
  `language_id` int(10) unsigned NOT NULL,
  `levelType` int(1) NOT NULL DEFAULT '0',
  `createDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`,`language_id`),
  KEY `profile_language_ibfk_2` (`language_id`),
  CONSTRAINT `profile_language_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `profile_language_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `profile_project` */

DROP TABLE IF EXISTS `profile_project`;

CREATE TABLE `profile_project` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `project_id` int(10) unsigned NOT NULL,
  `occupation_education_id` int(10) unsigned DEFAULT NULL,
  `occupation_experience_id` int(10) unsigned DEFAULT NULL,
  `createDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dateFrom` date NOT NULL,
  `dateTo` date DEFAULT NULL,
  `isCurrent` tinyint(1) NOT NULL DEFAULT '0',
  `description` text,
  `url` varchar(160) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `project_id` (`project_id`),
  KEY `occupation_education_id` (`occupation_education_id`),
  KEY `occupation_experience_id` (`occupation_experience_id`),
  CONSTRAINT `profile_project_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `profile_project_ibfk_2` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `profile_project_ibfk_3` FOREIGN KEY (`occupation_education_id`) REFERENCES `profile_education` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `profile_project_ibfk_4` FOREIGN KEY (`occupation_experience_id`) REFERENCES `profile_expirience` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

/*Table structure for table `profile_project_users` */

DROP TABLE IF EXISTS `profile_project_users`;

CREATE TABLE `profile_project_users` (
  `profile_project_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`profile_project_id`),
  KEY `profile_project_users_ibfk_2` (`profile_project_id`),
  CONSTRAINT `profile_project_users_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `profile_project_users_ibfk_2` FOREIGN KEY (`profile_project_id`) REFERENCES `profile_project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `profile_skills` */

DROP TABLE IF EXISTS `profile_skills`;

CREATE TABLE `profile_skills` (
  `user_id` int(10) unsigned NOT NULL,
  `skill_id` int(10) unsigned NOT NULL,
  `createDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `position` int(3) NOT NULL DEFAULT '0',
  `countEndorse` int(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`,`skill_id`),
  KEY `profile_skills_ibfk_2` (`skill_id`),
  CONSTRAINT `profile_skills_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `profile_skills_ibfk_2` FOREIGN KEY (`skill_id`) REFERENCES `skills` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `profile_testscore` */

DROP TABLE IF EXISTS `profile_testscore`;

CREATE TABLE `profile_testscore` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `testscore_id` int(10) unsigned NOT NULL,
  `createDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `occupation` varchar(160) NOT NULL DEFAULT '0',
  `dateScore` date NOT NULL,
  `score` varchar(128) NOT NULL,
  `description` text,
  `url` varchar(160) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `testscore_id` (`testscore_id`),
  CONSTRAINT `profile_testscore_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `profile_testscore_ibfk_2` FOREIGN KEY (`testscore_id`) REFERENCES `testscores` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Table structure for table `projects` */

DROP TABLE IF EXISTS `projects`;

CREATE TABLE `projects` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

/*Table structure for table `settings` */

DROP TABLE IF EXISTS `settings`;

CREATE TABLE `settings` (
  `name` varchar(128) NOT NULL,
  `key` varchar(128) NOT NULL,
  `value` varchar(300) NOT NULL,
  `type` varchar(32) NOT NULL DEFAULT 'text',
  `rules` text,
  `options` text,
  `root` tinyint(1) DEFAULT NULL,
  `position` int(10) DEFAULT NULL,
  UNIQUE KEY `key` (`key`),
  KEY `position` (`position`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `skills` */

DROP TABLE IF EXISTS `skills`;

CREATE TABLE `skills` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `countUsed` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=97 DEFAULT CHARSET=utf8;

/*Table structure for table `skills_endorsement` */

DROP TABLE IF EXISTS `skills_endorsement`;

CREATE TABLE `skills_endorsement` (
  `user_id` int(10) unsigned NOT NULL,
  `owner_id` int(10) unsigned NOT NULL,
  `skill_id` int(10) unsigned NOT NULL,
  `createDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`,`owner_id`,`skill_id`),
  KEY `skills_endorsement_ibfk_2` (`owner_id`),
  KEY `skills_endorsement_ibfk_3` (`skill_id`),
  CONSTRAINT `skills_endorsement_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `skills_endorsement_ibfk_2` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `skills_endorsement_ibfk_3` FOREIGN KEY (`skill_id`) REFERENCES `skills` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `specialties` */

DROP TABLE IF EXISTS `specialties`;

CREATE TABLE `specialties` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(127) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `tags` */

DROP TABLE IF EXISTS `tags`;

CREATE TABLE `tags` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `tags_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=218 DEFAULT CHARSET=utf8;

/*Table structure for table `testscores` */

DROP TABLE IF EXISTS `testscores`;

CREATE TABLE `testscores` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

/*Table structure for table `timeline` */

DROP TABLE IF EXISTS `timeline`;

CREATE TABLE `timeline` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `company_id` int(10) unsigned DEFAULT NULL,
  `group_id` int(10) unsigned DEFAULT NULL,
  `school_id` int(10) unsigned DEFAULT NULL,
  `friend_id` int(10) unsigned DEFAULT NULL,
  `profile_experience_id` int(10) unsigned DEFAULT NULL,
  `createDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `type` int(2) NOT NULL DEFAULT '0',
  `content` text,
  `countLikes` int(6) NOT NULL DEFAULT '0',
  `countShare` int(6) NOT NULL DEFAULT '0',
  `countComments` int(6) NOT NULL DEFAULT '0',
  `post_id` int(10) unsigned DEFAULT NULL,
  `parent_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `post_id` (`post_id`),
  KEY `parent_id` (`parent_id`),
  KEY `company_id` (`company_id`),
  KEY `group_id` (`group_id`),
  KEY `school_id` (`school_id`),
  KEY `friend_id` (`friend_id`),
  KEY `timeline_ibfk_9` (`profile_experience_id`),
  CONSTRAINT `timeline_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `timeline_ibfk_3` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `timeline_ibfk_4` FOREIGN KEY (`parent_id`) REFERENCES `timeline` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `timeline_ibfk_5` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `timeline_ibfk_6` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `timeline_ibfk_7` FOREIGN KEY (`school_id`) REFERENCES `universities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `timeline_ibfk_8` FOREIGN KEY (`friend_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `timeline_ibfk_9` FOREIGN KEY (`profile_experience_id`) REFERENCES `profile_expirience` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1167 DEFAULT CHARSET=utf8;

/*Table structure for table `timeline_comments` */

DROP TABLE IF EXISTS `timeline_comments`;

CREATE TABLE `timeline_comments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `timeline_id` int(10) unsigned NOT NULL,
  `timelineComment_id` int(10) unsigned NOT NULL,
  `createDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `comment` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `timeline_id` (`timeline_id`),
  CONSTRAINT `timeline_comments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `timeline_comments_ibfk_2` FOREIGN KEY (`timeline_id`) REFERENCES `timeline` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=434 DEFAULT CHARSET=utf8;

/*Table structure for table `timeline_likes` */

DROP TABLE IF EXISTS `timeline_likes`;

CREATE TABLE `timeline_likes` (
  `user_id` int(10) unsigned NOT NULL,
  `timeline_id` int(10) unsigned NOT NULL,
  `parentTimeline_id` int(10) unsigned NOT NULL,
  `createDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`,`timeline_id`,`parentTimeline_id`),
  KEY `timeline_likes_ibfk_2` (`timeline_id`),
  KEY `timeline_likes_ibfk_3` (`parentTimeline_id`),
  CONSTRAINT `timeline_likes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `timeline_likes_ibfk_2` FOREIGN KEY (`timeline_id`) REFERENCES `timeline` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `timeline_likes_ibfk_3` FOREIGN KEY (`parentTimeline_id`) REFERENCES `timeline` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `timeline_shares` */

DROP TABLE IF EXISTS `timeline_shares`;

CREATE TABLE `timeline_shares` (
  `user_id` int(10) unsigned NOT NULL,
  `timeline_id` int(10) unsigned NOT NULL,
  `parentTimeline_id` int(10) unsigned NOT NULL,
  `createDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`,`timeline_id`,`parentTimeline_id`),
  KEY `timeline_shares_ibfk_2` (`timeline_id`),
  KEY `timeline_shares_ibfk_3` (`parentTimeline_id`),
  CONSTRAINT `timeline_shares_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `timeline_shares_ibfk_2` FOREIGN KEY (`timeline_id`) REFERENCES `timeline` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `timeline_shares_ibfk_3` FOREIGN KEY (`parentTimeline_id`) REFERENCES `timeline` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `universities` */

DROP TABLE IF EXISTS `universities`;

CREATE TABLE `universities` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(128) NOT NULL,
  `createDate` timestamp NULL DEFAULT NULL,
  `type` int(1) NOT NULL DEFAULT '1',
  `description` text,
  `email` varchar(64) DEFAULT NULL,
  `email2` varchar(64) DEFAULT NULL,
  `domain` varchar(64) DEFAULT NULL,
  `yearFounded` int(4) DEFAULT NULL,
  `yearLevel` varchar(32) DEFAULT NULL,
  `url` varchar(128) DEFAULT NULL,
  `address` varchar(128) DEFAULT NULL,
  `phone1` varchar(32) DEFAULT NULL,
  `phone2` varchar(32) DEFAULT NULL,
  `countUndergradPopulation` int(7) NOT NULL DEFAULT '0',
  `countDraduatePopulation` int(7) NOT NULL DEFAULT '0',
  `countFollowers` int(6) NOT NULL DEFAULT '0',
  `avaToken` varchar(16) DEFAULT NULL,
  `coverToken` varchar(16) DEFAULT NULL,
  `isRegistered` tinyint(1) NOT NULL DEFAULT '0',
  `isAgree` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `universities_ibfk_1` (`user_id`),
  CONSTRAINT `universities_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;

/*Table structure for table `university_admins` */

DROP TABLE IF EXISTS `university_admins`;

CREATE TABLE `university_admins` (
  `university_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`university_id`),
  KEY `university_admins_ibfk_2` (`university_id`),
  CONSTRAINT `university_admins_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `university_admins_ibfk_2` FOREIGN KEY (`university_id`) REFERENCES `universities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `university_follow` */

DROP TABLE IF EXISTS `university_follow`;

CREATE TABLE `university_follow` (
  `univercity_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`univercity_id`),
  KEY `university_follow_ibfk_2` (`univercity_id`),
  CONSTRAINT `university_follow_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `university_follow_ibfk_2` FOREIGN KEY (`univercity_id`) REFERENCES `universities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `university_join` */

DROP TABLE IF EXISTS `university_join`;

CREATE TABLE `university_join` (
  `university_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '1',
  `createDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY `university_join_ibfk_1` (`university_id`),
  KEY `university_join_ibfk_2` (`user_id`),
  CONSTRAINT `university_join_ibfk_1` FOREIGN KEY (`university_id`) REFERENCES `universities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `university_join_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `createDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `isSubscribed` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `updateDate` timestamp NULL DEFAULT NULL,
  `updateExp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `accountType` tinyint(1) DEFAULT '0' COMMENT 'Basic, Full',
  `birthdayDate` timestamp NULL DEFAULT NULL,
  `name` varchar(16) DEFAULT NULL,
  `alias` varchar(64) DEFAULT NULL,
  `password` varchar(48) NOT NULL COMMENT 'Salt:8 + SHA1(Salt + Password):40',
  `token` varchar(32) DEFAULT NULL COMMENT 'Auth cookie token',
  `role` varchar(16) NOT NULL DEFAULT 'user' COMMENT 'ACL role',
  `email` varchar(128) NOT NULL,
  `email2` varchar(128) DEFAULT '',
  `firstName` varchar(32) NOT NULL,
  `lastName` varchar(32) NOT NULL,
  `gender` set('M','F') NOT NULL DEFAULT 'M',
  `address` varchar(128) DEFAULT '',
  `city` varchar(64) NOT NULL DEFAULT '',
  `state` varchar(64) NOT NULL DEFAULT '',
  `zip` varchar(24) NOT NULL DEFAULT '',
  `country` varchar(64) NOT NULL DEFAULT '',
  `phone` varchar(32) NOT NULL DEFAULT '',
  `websites` text COMMENT 'serialize array socials',
  `professionalHeadline` varchar(128) DEFAULT '' COMMENT '(Posada)',
  `industry` int(4) DEFAULT NULL COMMENT '(Sfera dijalnosti)',
  `summaryText` text,
  `summaryLinks` text COMMENT 'serialize',
  `maritalStatus` tinyint(1) DEFAULT NULL,
  `interests` text,
  `personalDetails` text,
  `shareActivityInActivityFeed` tinyint(1) NOT NULL DEFAULT '1' COMMENT '(Yes, No)',
  `setInvisibleProfile` tinyint(1) NOT NULL DEFAULT '0' COMMENT '(Yes, No)',
  `whoCanSeeActivity` tinyint(1) NOT NULL DEFAULT '1' COMMENT '(All, Me, My Connections, My network)',
  `whoCanSeeConnections` tinyint(1) NOT NULL DEFAULT '1' COMMENT '(All, Me, My Connections, My network)',
  `whoCanSeeContactInfo` tinyint(1) NOT NULL DEFAULT '2' COMMENT '(All, Me, My Connections, My network)',
  `avaToken` varchar(16) DEFAULT NULL,
  `isConfirmed` tinyint(1) NOT NULL DEFAULT '1',
  `isInvisible` tinyint(1) NOT NULL DEFAULT '0',
  `isRemoved` tinyint(1) NOT NULL DEFAULT '0',
  `isUpdatedConnections` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_idx_1` (`email`),
  UNIQUE KEY `name_idx_1` (`name`),
  KEY `confirmed_idx` (`isConfirmed`)
) ENGINE=InnoDB AUTO_INCREMENT=96 DEFAULT CHARSET=utf8;

/*Table structure for table `visitors` */

DROP TABLE IF EXISTS `visitors`;

CREATE TABLE `visitors` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ip` bigint(10) NOT NULL,
  `browser` varchar(31) NOT NULL,
  `hour_visiting` int(11) NOT NULL,
  `curent_hour` int(11) NOT NULL,
  `bad_url` int(11) NOT NULL,
  `createDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `isBlocked` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ip` (`ip`)
) ENGINE=InnoDB AUTO_INCREMENT=230 DEFAULT CHARSET=utf8;

/*Table structure for table `visits` */

DROP TABLE IF EXISTS `visits`;

CREATE TABLE `visits` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `profile_id` int(10) unsigned DEFAULT NULL,
  `company_id` int(10) unsigned DEFAULT NULL,
  `group_id` int(10) unsigned DEFAULT NULL,
  `createDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `profile_id` (`profile_id`),
  KEY `company_id` (`company_id`),
  KEY `group_id` (`group_id`),
  CONSTRAINT `visits_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `visits_ibfk_2` FOREIGN KEY (`profile_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `visits_ibfk_3` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `visits_ibfk_4` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1397 DEFAULT CHARSET=utf8;

/*Table inviteByKEY */


DROP TABLE IF EXISTS `invite_by_key`;

CREATE TABLE `invite_by_key` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_invite_id` int(10) unsigned NOT NULL,
  `follower_id` int(10),
  `key` varchar(31) NOT NULL UNIQUE,
  `status` bit NOT NULL DEFAULT 1,
  `create_key` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_invite_id` (`user_invite_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
 */
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
