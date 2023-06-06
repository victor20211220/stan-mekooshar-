# 2014-08-01 TEST SERVER UPDATE

# 2014-08-05 Andrew.Svorak
DROP TABLE `group_discussions_comment`;
DROP TABLE `group_discussions_follow`;
DROP TABLE `group_discussions_joined`;
DROP TABLE `group_discussions`;
RENAME TABLE `group_follow` TO `group_discussion_follow`;
ALTER TABLE `group_discussion_follow` DROP FOREIGN KEY `group_discussion_follow_ibfk_1`, DROP FOREIGN KEY `group_discussion_follow_ibfk_2`;
TRUNCATE TABLE `group_discussion_follow`;
ALTER TABLE `group_discussion_follow` ADD COLUMN `post_id` INT(10) UNSIGNED NOT NULL FIRST, DROP PRIMARY KEY, ADD PRIMARY KEY (`post_id`, `group_id`, `user_id`), ADD CONSTRAINT `group_discussion_follow_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts`(`id`) ON UPDATE CASCADE ON DELETE CASCADE, ADD CONSTRAINT `group_discussion_follow_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `groups`(`id`) ON UPDATE CASCADE ON DELETE CASCADE, ADD CONSTRAINT `group_discussion_follow_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;

# 2014-08-05 TEST SERVER UPDATE

# 2014-08-12 Andrew.Svorak
ALTER TABLE `timeline_shares` ADD COLUMN `createDate` TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL AFTER `parentTimeline_id`;

# 2014-08-12 TEST SERVER UPDATE

# 2014-08-13 Andrew.Svorak
CREATE TABLE `jobs`( `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT, `user_id` INT(10) UNSIGNED NOT NULL, `company_id` INT(10) UNSIGNED NOT NULL, `createDate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, `industry` INT(3) NOT NULL, `country` VARCHAR(2) NOT NULL, `state` VARCHAR(64) NOT NULL, `city` VARCHAR(128) NOT NULL, `title` VARCHAR(128) NOT NULL, `description` TEXT(10000) NOT NULL, `requiredSkills` TEXT(1000) NOT NULL, `about` TEXT(10000) NOT NULL, `employment` INT(1) NOT NULL, `receivedEmail` VARCHAR(64), `plan` INT(1) NOT NULL DEFAULT 1, PRIMARY KEY (`id`), CONSTRAINT `jobs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON UPDATE CASCADE ON DELETE CASCADE, CONSTRAINT `jobs_ibfk_2` FOREIGN KEY (`company_id`) REFERENCES `companies`(`id`) ON UPDATE CASCADE ON DELETE CASCADE );
ALTER TABLE `jobs` ADD COLUMN `receivedType` INT(1) DEFAULT 1 NOT NULL AFTER `employment`;

# 2014-08-13 TEST SERVER UPDATE
# 2014-08-14 Andrew.Svorak
ALTER TABLE `jobs` ADD COLUMN `expiredDate` DATETIME NOT NULL AFTER `plan`;
ALTER TABLE `jobs` ADD COLUMN `countApplicants` INT(6) DEFAULT 0 NOT NULL AFTER `expiredDate`, ADD COLUMN `countNewApplicants` INT(6) DEFAULT 0 NOT NULL AFTER `countApplicants`;


# 2014-08-14 TEST SERVER UPDATE
# 2014-08-14 Andrew.Svorak
CREATE TABLE IF NOT EXISTS `cartOrders` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
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
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `cartItems` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `orderId` int(10) unsigned NOT NULL,
  `itemId` int(10) unsigned NOT NULL,
  `itemName` varchar(64) NOT NULL,
  `note` varchar(64) DEFAULT NULL,
  `source` varchar(64) DEFAULT 'directory',
  `section` varchar(64) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(15,2) DEFAULT NULL,
  `details` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


INSERT INTO `settings` (`name`, `key`, `value`, `type`, `rules`, `options`, `root`, `position`) VALUES
('Test mode', 'sandbox', '1', 'checkbox', NULL, NULL, 1, NULL),
('Payment system', 'paymentgateway', 'paypal', 'select', '{"required":true}', '{"paypal":"PayPal", "authorize":"Authorize.NET"}', NULL, NULL),
('Paypal Payments type', 'paypalType', 'paypal', 'select', '{"required":true}', '{"paypalStandart":"PayPal Payments Standart", "paypalPro":"PayPal Payments Pro"}', NULL, NULL),
('Paypal email', 'paypalEmail', 'stetsyuk@ukietech.com', 'text', NULL, NULL, NULL, NULL),
('API Username', 'paypalUsername', 'delete-286543989P4520825_api1.sandbox.paypal.com', 'text', NULL, NULL, NULL, NULL),
('API Password', 'paypalPassword', 'M5QN3PV9HS6KBXJM', 'text', NULL, NULL, NULL, NULL),
('Signature', 'paypalSignature', 'AlcKpCIVqM3pzKACjHN.bRCbBugPAhlwWTkHbXzUlICuDvcHY8WDDqU8', 'text', NULL, NULL, NULL, NULL);

CREATE TABLE `plans`( `int` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT, `name` VARCHAR(255) NOT NULL, `price` DECIMAL(15,2) NOT NULL DEFAULT 0, PRIMARY KEY (`int`) );
ALTER TABLE `plans` CHANGE `int` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `jobs` CHANGE `plan` `plan_id` INT(10) UNSIGNED DEFAULT 1 NOT NULL;
ALTER TABLE `jobs` CHANGE `plan_id` `plan_id` INT(10) UNSIGNED DEFAULT 1 NOT NULL AFTER `company_id`, CHANGE `createDate` `createDate` TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL AFTER `plan_id`, CHANGE `expiredDate` `expiredDate` DATETIME NOT NULL AFTER `receivedEmail`;
INSERT INTO `plans`(`id`,`name`,`price`) VALUES ( NULL,'15 days ($10)','10');
INSERT INTO `plans`(`id`,`name`,`price`) VALUES ( NULL,'30 days ($20)','20');
INSERT INTO `plans`(`id`,`name`,`price`) VALUES ( NULL,'60 days ($40)','40');
ALTER TABLE `jobs` ADD CONSTRAINT `jobs_ibfk_3` FOREIGN KEY (`plan_id`) REFERENCES `plans`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;

# 2014-08-15 Andrew.Svorak
ALTER TABLE `cartOrders` ADD COLUMN `job_id` INT(10) UNSIGNED NOT NULL AFTER `id`;
ALTER TABLE `cartOrders` ENGINE=INNODB;
TRUNCATE TABLE `cartOrders`;
ALTER TABLE `cartOrders` ADD COLUMN `user_id` INT(10) UNSIGNED NOT NULL AFTER `id`, ADD CONSTRAINT `cartOrders_ibfk_1` FOREIGN KEY (`job_id`) REFERENCES `jobs`(`id`) ON UPDATE CASCADE ON DELETE CASCADE, ADD CONSTRAINT `cartOrders_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE `cartOrders` DROP COLUMN `user_id`, DROP COLUMN `job_id`, DROP INDEX `cartOrders_ibfk_1`, DROP INDEX `cartOrders_ibfk_2`, DROP FOREIGN KEY `cartOrders_ibfk_1`, DROP FOREIGN KEY `cartOrders_ibfk_2`;
ALTER TABLE `cartItems` DROP COLUMN `itemId`, CHANGE `orderId` `order_id` INT(10) UNSIGNED NOT NULL, ADD COLUMN `user_id` INT(10) UNSIGNED NOT NULL AFTER `order_id`, ADD COLUMN `job_id` INT(10) UNSIGNED NOT NULL AFTER `user_id`, ADD COLUMN `plan_id` INT(10) UNSIGNED NOT NULL AFTER `job_id`, ENGINE=INNODB;
RENAME TABLE `cartOrders` TO `cart_orders`;
TRUNCATE TABLE `cart_orders`;
TRUNCATE TABLE `cartItems`;
ALTER TABLE `cartItems` CHANGE `order_id` `order_id` INT(10) UNSIGNED NULL, ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `cart_orders`(`id`) ON UPDATE CASCADE ON DELETE CASCADE, ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON UPDATE CASCADE ON DELETE CASCADE, ADD CONSTRAINT `cart_items_ibfk_3` FOREIGN KEY (`job_id`) REFERENCES `jobs`(`id`) ON UPDATE CASCADE ON DELETE CASCADE, ADD CONSTRAINT `cart_items_ibfk_4` FOREIGN KEY (`plan_id`) REFERENCES `plans`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;
RENAME TABLE `cartItems` TO `cart_items`;
ALTER TABLE `cart_orders` ADD COLUMN `user_id` INT(10) UNSIGNED NOT NULL AFTER `id`, ADD CONSTRAINT `cart_orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE `plans` ADD COLUMN `countDays` INT(6) DEFAULT 1 NOT NULL AFTER `price`;
UPDATE `plans` SET `countDays`='15' WHERE `id`='1';
UPDATE `plans` SET `countDays`='30' WHERE `id`='2';
UPDATE `plans` SET `countDays`='60' WHERE `id`='3';
ALTER TABLE `plans` ADD COLUMN `createDate` TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL AFTER `id`, ADD COLUMN `isRemoved` TINYINT(1) DEFAULT 0 NOT NULL AFTER `countDays`;
ALTER TABLE `jobs` ADD COLUMN `isRemoved` TINYINT(1) DEFAULT 0 NOT NULL AFTER `countNewApplicants`;


# 2014-08-15 TEST SERVER UPDATE
# 2014-08-18 Andrew.Svorak
CREATE TABLE `job_skills`( `job_id` INT(10) UNSIGNED NOT NULL, `skill_id` INT(10) UNSIGNED NOT NULL, PRIMARY KEY (`job_id`, `skill_id`), CONSTRAINT `job_skills_ibfk_1` FOREIGN KEY (`job_id`) REFERENCES `jobs`(`id`) ON UPDATE CASCADE ON DELETE CASCADE, CONSTRAINT `job_skills_ibfk_2` FOREIGN KEY (`skill_id`) REFERENCES `skills`(`id`) ON UPDATE CASCADE ON DELETE CASCADE );
CREATE TABLE `job_apply`( `job_id` INT(10) UNSIGNED NOT NULL, `user_id` INT(10) UNSIGNED NOT NULL, `createDate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, `isViwed` TINYINT(1) NOT NULL DEFAULT 0, `isInvited` TINYINT(1), PRIMARY KEY (`job_id`, `user_id`), CONSTRAINT `job_apply_ibfk_1` FOREIGN KEY (`job_id`) REFERENCES `jobs`(`id`) ON UPDATE CASCADE ON DELETE CASCADE, CONSTRAINT `job_apply_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON UPDATE CASCADE ON DELETE CASCADE );
ALTER TABLE `job_apply` ADD COLUMN `coverLetter` TEXT NULL AFTER `createDate`;
ALTER TABLE `jobs` DROP COLUMN `requiredSkills`;


# 2014-08-19 TEST SERVER UPDATE
ALTER TABLE `job_apply` CHANGE `isInvited` `isInvited` TINYINT(1) DEFAULT 0 NOT NULL;
ALTER TABLE `job_apply` CHANGE `isViwed` `isViewed` TINYINT(1) DEFAULT 0 NOT NULL;

# 2014-08-19 TEST SERVER UPDATE
ALTER TABLE `job_apply` ADD COLUMN `isRemovedJobOwner` TINYINT(1) DEFAULT 0 NOT NULL AFTER `isInvited`;

# 2014-08-19 TEST SERVER UPDATE
# 2014-09-02 Andrew.Svorak
ALTER TABLE `profile_skills` CHANGE `count` `countEndorse` INT(5) DEFAULT 0 NOT NULL;
ALTER TABLE `job_apply` ADD COLUMN `isRemovedJobApplicant` TINYINT(1) DEFAULT 0 NOT NULL AFTER `isRemovedJobOwner`;

# 2014-09-03 TEST SERVER UPDATE
insert  into `settings`(`name`,`key`,`value`,`type`,`rules`,`options`,`root`,`position`) values ('When create company, deny custom mail servers','blockMailServers','1','checkbox','','',0,NULL),('Mail servers domain (google.com, yahoo.com)','mailServers','google.com, yahoo.com, hotmail.com, gmail.com','text','','',0,NULL);

# 2014-09-03 TEST SERVER UPDATE
# 2014-09-05 Andrew.Svorak
ALTER TABLE `users` CHANGE `typeShowActivityFeed` `shareActivityInActivityFeed` TINYINT(1) DEFAULT 1 NOT NULL COMMENT '(Yes, No)', CHANGE `typeLevelAccessConnection` `setInvisibleProfile` TINYINT(1) DEFAULT 1 NOT NULL COMMENT '(Yes, No)', CHANGE `createMyActivityFeed` `whoCanSeeActivity` TINYINT(1) DEFAULT 1 NOT NULL COMMENT '(All, Me, My Connections, My network)', ADD COLUMN `whoCanSeeConnections` TINYINT(1) DEFAULT 1 NOT NULL COMMENT '(All, Me, My Connections, My network)' AFTER `whoCanSeeActivity`;
ALTER TABLE `users` ADD COLUMN `updateExp` TIMESTAMP NOT NULL AFTER `updateDate`;

# 2014-09-05 TEST SERVER UPDATE
# 2014-09-05 Andrew.Svorak
ALTER TABLE `universities` ADD COLUMN `user_id` INT(10) UNSIGNED NULL AFTER `id`, ADD CONSTRAINT `universities_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE `universities` ADD COLUMN `domain` VARCHAR(64) NULL AFTER `email`;
ALTER TABLE `universities` ADD COLUMN `createDate` TIMESTAMP NULL AFTER `name`;

# 2014-09-08 Andrew.Svorak
ALTER TABLE `universities` ADD COLUMN `countFollowers` INT(6) DEFAULT 0 NOT NULL AFTER `countDraduatePopulation`;
ALTER TABLE `universities` CHANGE `isRegistred` `isRegistered` TINYINT(1) DEFAULT 0 NOT NULL;
ALTER TABLE `universities` ADD COLUMN `avaToken` VARCHAR(16) NULL AFTER `countFollowers`;
ALTER TABLE `universities` ADD COLUMN `isAgree` TINYINT(1) DEFAULT 0 NOT NULL AFTER `isRegistered`;
ALTER TABLE `universities` ADD COLUMN `coverToken` VARCHAR(16) NULL AFTER `avaToken`;
ALTER TABLE `universities` CHANGE `typeInstitute` `typeInstitute` INT(1) DEFAULT 1 NOT NULL;
ALTER TABLE `universities` CHANGE `typeInstitute` `type` INT(1) DEFAULT 1 NOT NULL;
ALTER TABLE `universities` ADD COLUMN `email2` VARCHAR(64) NULL AFTER `email`;
CREATE TABLE `university_join`( `university_id` INT(10) UNSIGNED NOT NULL, `user_id` INT(10) UNSIGNED NOT NULL, `type` TINYINT(1) NOT NULL DEFAULT 1, `createDate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, CONSTRAINT `university_join_ibfk_1` FOREIGN KEY (`university_id`) REFERENCES `universities`(`id`) ON UPDATE CASCADE ON DELETE CASCADE, CONSTRAINT `university_join_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON UPDATE CASCADE ON DELETE CASCADE );
ALTER TABLE `universities` DROP COLUMN `city`, DROP COLUMN `state`, DROP COLUMN `zip`, DROP COLUMN `country`, DROP COLUMN `fax`;

# 2014-09-09 Andrew.Svorak
ALTER TABLE `posts` ADD COLUMN `school_id` INT(10) UNSIGNED NULL AFTER `group_id`, ADD INDEX (`school_id`), ADD CONSTRAINT `posts_ibfk_7` FOREIGN KEY (`school_id`) REFERENCES `universities`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE `timeline` ADD COLUMN `school_id` INT(10) UNSIGNED NULL AFTER `group_id`, ADD INDEX (`school_id`), ADD CONSTRAINT `timeline_ibfk_7` FOREIGN KEY (`school_id`) REFERENCES `universities`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;

# 2014-09-10 Andrew.Svorak
ALTER TABLE `profile_education` ADD COLUMN `isNotableAlumni` TINYINT(1) NULL AFTER `description`;

# 2014-09-12 Andrew.Svorak
ALTER TABLE `profile_expirience` ADD COLUMN `isSchoolMember` TINYINT(1) NULL AFTER `links`;

# 2014-09-12 TEST SERVER UPDATE
# 2014-09-16 Andrew.Svorak
ALTER TABLE `visitors` ADD INDEX (`ip`);

# 2014-09-16 Andrew.Svorak
ALTER TABLE `users` ADD COLUMN `isUpdatedConnections` TINYINT(1) DEFAULT 0 NOT NULL AFTER `isRemoved`;

# 2014-09-23 Andrew.Svorak
ALTER TABLE `plans` ADD COLUMN `category` TINYINT(1) DEFAULT 1 NOT NULL AFTER `createDate`;

# 2014-09-24 Andrew.Svorak
ALTER TABLE `cart_items` CHANGE `job_id` `job_id` INT(10) UNSIGNED NULL;

# 2014-09-25 Andrew.Svorak
ALTER TABLE `pages` CHANGE `category` `typePage` TINYINT(1) DEFAULT 1 NOT NULL, ADD COLUMN `category` VARCHAR(64) NOT NULL AFTER `typePage`;
CREATE TABLE `page_category`( `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT, `name` VARCHAR(128) NOT NULL, `subCategory` INT(10) UNSIGNED, PRIMARY KEY (`id`), INDEX (`subCategory`) );
ALTER TABLE `page_category` ADD CONSTRAINT `page_category_ibfk_1` FOREIGN KEY (`subCategory`) REFERENCES `page_category`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE `page_category` ADD COLUMN `isBlocked` TINYINT(1) NOT NULL AFTER `subCategory`;
ALTER TABLE `pages` CHANGE `category` `category` TINYINT(10) NOT NULL;


/* 23:19:36 localhost */ ALTER TABLE `visitors` CHANGE `isBlocked` `isBlocked` TINYINT(4)  NULL;
/* 23:53:04 localhost */ ALTER TABLE `users` CHANGE `email2` `email2` VARCHAR(128)  CHARACTER SET utf8  NULL  DEFAULT '';
/* 23:54:03 localhost */ ALTER TABLE `users` CHANGE `address` `address` VARCHAR(128)  CHARACTER SET utf8  NULL  DEFAULT '';
/* 23:54:47 localhost */ ALTER TABLE `users` CHANGE `professionalHeadline` `professionalHeadline` VARCHAR(128)  CHARACTER SET utf8  NULL  DEFAULT ''  COMMENT '(Posada)';

# 2014-09-26 Andrew.Svorak
insert  into `page_category`(`id`,`name`,`subCategory`,`isBlocked`) values (1,'Home page',NULL,0),(2,'Privacy policy',NULL,0),(5,'About us',NULL,0),(6,'Background gallery on start page',NULL,0);

# 2014-10-01 Andrew.Svorak
ALTER TABLE `users` CHANGE COLUMN `setInvisibleProfile` `setInvisibleProfile` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '(Yes, No)' ;

# 2014-10-01 TEST SERVER UPDATE
/* 2:20:11 localhost */ ALTER TABLE `timeline` ADD `friend_id` INT  NULL  DEFAULT NULL  AFTER `school_id`;
/* 2:20:16 localhost */ ALTER TABLE `timeline` CHANGE `friend_id` `friend_id` INT(10)  NULL  DEFAULT NULL;
/* 2:20:48 localhost */ ALTER TABLE `timeline` CHANGE `friend_id` `friend_id` INT(10)  UNSIGNED  NULL  DEFAULT NULL;
/* 2:22:02 localhost */ ALTER TABLE `timeline` ADD CONSTRAINT `timeline_ibfk_8` FOREIGN KEY (`friend_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `timeline` DROP INDEX `timeline_ibfk_8` , ADD INDEX `friend_id` (`friend_id` ASC);

ALTER TABLE `users` ADD COLUMN `alias` VARCHAR(64) NULL AFTER `name`;
ALTER TABLE `timeline` ADD COLUMN `profile_experience_id` INT(10) UNSIGNED NULL AFTER `friend_id`, ADD CONSTRAINT `timeline_ibfk_9` FOREIGN KEY (`profile_experience_id`) REFERENCES `profile_expirience`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE `users` ADD COLUMN `whoCanSeeContactInfo` TINYINT(1) DEFAULT 1 NOT NULL COMMENT '(Yes, No)' AFTER `whoCanSeeConnections`;

ALTER TABLE `skills` ADD COLUMN `countUsed` INT(10) DEFAULT 0 NOT NULL AFTER `name`;
ALTER TABLE `skills` CHANGE `name` `name` VARCHAR(64) NOT NULL;
ALTER TABLE `users` CHANGE `whoCanSeeContactInfo` `whoCanSeeContactInfo` TINYINT(1) DEFAULT 2 NOT NULL COMMENT '(All, Me, My Connections, My network)';
UPDATE `users` SET `whoCanSeeContactInfo`='1' WHERE `whoCanSeeContactInfo`='0';

# 2014-10-15 TEST SERVER UPDATE
ALTER TABLE `notifications` ADD COLUMN `company_id` INT(10) UNSIGNED NULL AFTER `skill_id`, ADD CONSTRAINT `notifications_ibfk_5` FOREIGN KEY (`company_id`) REFERENCES `companies`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE `notifications` CHANGE `user_id` `user_id` INT(10) UNSIGNED NULL;
ALTER TABLE `notifications` ADD COLUMN `job_id` INT(10) UNSIGNED NULL AFTER `company_id`, ADD CONSTRAINT `notifications_ibfk_6` FOREIGN KEY (`job_id`) REFERENCES `jobs`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;





# 2014-10-22 TEST SERVER UPDATE
# CREATE2.sql
ALTER TABLE `users` CHANGE `birthdayDate` `birthdayDate` DATETIME  NULL;

# 2014-11-24 Andrew.svorak
ALTER TABLE `users` ADD COLUMN `countConnections` INT(7) DEFAULT 0 NOT NULL AFTER `personalDetails`, ADD COLUMN `countConnections2` INT(7) DEFAULT 0 NOT NULL AFTER `countConnections`, ADD COLUMN `countConnections3` INT(7) DEFAULT 0 NOT NULL AFTER `countConnections2`;
CREATE TABLE `user_friends`( `user_id` INT(10) UNSIGNED NOT NULL, `friends` TEXT NOT NULL, PRIMARY KEY (`user_id`), INDEX (`user_id`), CONSTRAINT `user_friends_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON UPDATE CASCADE ON DELETE CASCADE );

# 2014-11-24 TEST SERVER UPDATE

# 2014-11-26 Andrew.svorak
ALTER TABLE `jobs` ADD COLUMN `activateDate` DATETIME NULL AFTER `receivedEmail`;
ALTER TABLE `jobs` CHANGE `activateDate` `activateDate` DATETIME NOT NULL;

# 2014-12-08 Andrew.svorak
CREATE TABLE `profile_blocked`( `user_id` INT(10) UNSIGNED NOT NULL, `profile_id` INT(10) UNSIGNED NOT NULL, `createDate` TIMESTAMP NOT NULL, PRIMARY KEY (`user_id`, `profile_id`), CONSTRAINT `profile_blocked_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON UPDATE CASCADE ON DELETE CASCADE, CONSTRAINT `profile_blocked_ibfk_2` FOREIGN KEY (`profile_id`) REFERENCES `users`(`id`) ON UPDATE CASCADE ON DELETE CASCADE );

# 2014-12-08 TEST SERVER UPDATE

# 2014-12-10 Andrew.svorak
ALTER TABLE `profile_education` ADD COLUMN `isTypeInSchool` TINYINT(1) DEFAULT 0 NOT NULL AFTER `isNotableAlumni`;

# 2014-12-10 TEST SERVER UPDATE

# 2014-12-11 Andrew.svorak
CREATE TABLE `profile_complaint`( `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT, `user_id` INT(10) UNSIGNED NOT NULL, `profile_id` INT(10) UNSIGNED NOT NULL, `createDate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, `description` TEXT NOT NULL, `isViewed` TINYINT(1) NOT NULL DEFAULT 0, `isBlocked` TINYINT(1), PRIMARY KEY (`id`), INDEX (`user_id`), INDEX (`profile_id`), CONSTRAINT `profile_complaint_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON UPDATE CASCADE ON DELETE CASCADE, CONSTRAINT `profile_complaint_ibfk_2` FOREIGN KEY (`profile_id`) REFERENCES `users`(`id`) ON UPDATE CASCADE ON DELETE CASCADE );

# 2014-12-11 TEST SERVER UPDATE
# 2014-12-12 Andrew.svorak
ALTER TABLE `users` ADD COLUMN `isBlocked` TINYINT(1) DEFAULT 0 NOT NULL AFTER `isUpdatedConnections`;
ALTER TABLE `profile_complaint` DROP COLUMN `isBlocked`;

# 2014-12-18 TEST SERVER UPDATE
# 2014-12-18 Andrew.svorak
INSERT INTO `skills`(`id`,`name`,`countUsed`) VALUES
 ( NULL,'E-commerce','1000'),
 ( NULL,'SEO','1000'),
 ( NULL,'Email Marketing','1000'),
 ( NULL,'Business Development','1000'),
 ( NULL,'Online Marketing','1000'),
 ( NULL,'Advertising','1000'),
 ( NULL,'Digital Marketing','1000'),
 ( NULL,'New Business Development','1000'),
 ( NULL,'Management','1000'),
 ( NULL,'Negotiation','1000'),
 ( NULL,'QuickBooks','1000'),
 ( NULL,'Strategic Planning','1000'),
 ( NULL,'Budgets','1000'),
 ( NULL,'Online Advertising','1000'),
 ( NULL,'Sales Management...','1000'),
 ( NULL,'PPC','1000'),
 ( NULL,'Public Relations','1000'),
 ( NULL,'Digital Media','1000'),
 ( NULL,'MySQL','1000'),
 ( NULL,'PHP','1000'),
 ( NULL,'CSS','1000'),
 ( NULL,'Real Estate ','1000'),
 ( NULL,'Leadership ','1000'),
 ( NULL,'Due Diligence ','1000'),
 ( NULL,'Recruiting ','1000'),
 ( NULL,'Marketing ','1000'),
 ( NULL,'Integrated Marketing ','1000'),
 ( NULL,'Partnerships ','1000'),
 ( NULL,'New Business Development ','1000'),
 ( NULL,'Contract Negotiation ','1000'),
 ( NULL,'Strategic Partnerships ','1000'),
 ( NULL,'Executive Management ','1000'),
 ( NULL,'Awesomeness','1000'),
 ( NULL,'Green','1000'),
 ( NULL,'Public Relations','1000'),
 ( NULL,'Entrepreneurship','1000'),
 ( NULL,'Digital Media','1000'),
 ( NULL,'Brand Development','1000'),
 ( NULL,'E-commerce','1000'),
 ( NULL,'Renewable Energy','1000'),
 ( NULL,'Corporate Social...','1000'),
 ( NULL,'Online Marketing','1000'),
 ( NULL,'Online Advertising','1000'),
 ( NULL,'Energy','1000'),
 ( NULL,'Social Networking','1000'),
 ( NULL,'Environmental Awareness','1000'),
 ( NULL,'Business Development','1000'),
 ( NULL,'Marketing Strategy','1000'),
 ( NULL,'New Business Development','1000'),
 ( NULL,'Digital Marketing','1000'),
 ( NULL,'Marketing Communications','1000'),
 ( NULL,'Lead Generation','1000'),
 ( NULL,'Strategic Planning','1000'),
 ( NULL,'Email Marketing','1000'),
 ( NULL,'Sales','1000'),
 ( NULL,'Integrated Marketing','1000'),
 ( NULL,'Business Strategy','1000'),
 ( NULL,'SEO','1000'),
 ( NULL,'Strategic Partnerships','1000'),
 ( NULL,'Start-ups','1000'),
 ( NULL,'Web Marketing','1000'),
 ( NULL,'Digital Strategy','1000'),
 ( NULL,'Sustainability Strategy','1000'),
 ( NULL,'Research','1000'),
 ( NULL,'Writing','1000'),
 ( NULL,'LEED AP','1000'),
 ( NULL,'Business Planning','1000'),
 ( NULL,'Product Development','1000'),
 ( NULL,'Vendor Management ','1000'),
 ( NULL,'Operations Management ','1000'),
 ( NULL,'B2B ','1000'),
 ( NULL,'International Business ','1000'),
 ( NULL,'Email Marketing ','1000'),
 ( NULL,'Sales Process ','1000'),
 ( NULL,'Online Marketing ','1000'),
 ( NULL,'Supply Chain Management ','1000'),
 ( NULL,'Export ','1000'),
 ( NULL,'Management ','1000'),
 ( NULL,'Marketing Strategy ','1000'),
 ( NULL,'CRM ','1000'),
 ( NULL,'PPC ','1000'),
 ( NULL,'Microsoft Office ','1000'),
 ( NULL,'Microsoft Excel ','1000'),
 ( NULL,'Supply Chain ','1000'),
 ( NULL,'Entrepreneurship ','1000'),
 ( NULL,'Call Centers ','1000'),
 ( NULL,'Call Center ','1000'),
 ( NULL,'Project Management ','1000'),
 ( NULL,'Procurement ','1000'),
 ( NULL,'Technical Recruiting ','1000'),
 ( NULL,'Logistics Management ','1000'),
 ( NULL,'Leadership ','1000'),
 ( NULL,'Email Marketing ','1000'),
 ( NULL,'New Business Development ','1000'),
 ( NULL,'Web Development ','1000'),
 ( NULL,'Google Adwords ','1000'),
 ( NULL,'Web Analytics ','1000'),
 ( NULL,'Small Business ','1000'),
 ( NULL,'SEM ','1000'),
 ( NULL,'Integrated Marketing ','1000'),
 ( NULL,'Product Development ','1000'),
 ( NULL,'Mobile Applications ','1000'),
 ( NULL,'Marketing Strategy ','1000'),
 ( NULL,'Strategy ','1000'),
 ( NULL,'Analytics ','1000'),
 ( NULL,'Strategic Partnerships ','1000'),
 ( NULL,'Web Marketing ','1000'),
 ( NULL,'Google Analytics ','1000'),
 ( NULL,'Venture Capital ','1000'),
 ( NULL,'Computer Science ','1000'),
 ( NULL,'Entrepreneur ','1000'),
 ( NULL,'Angel Investing ','1000'),
 ( NULL,'Mobile Devices ','1000'),
 ( NULL,'SaaS ','1000'),
 ( NULL,'Yandex PPC ','1000'),
 ( NULL,'XML ','1000'),
 ( NULL,'Ruby ','1000'),
 ( NULL,'MySQL ','1000'),
 ( NULL,'Git ','1000'),
 ( NULL,'XHTML ','1000'),
 ( NULL,'Web Applications ','1000'),
 ( NULL,'CSS ','1000'),
 ( NULL,'Apache ','1000'),
 ( NULL,'REST ','1000'),
 ( NULL,'Test Driven Development ','1000'),
 ( NULL,'SEO ','1000'),
 ( NULL,'Subversion ','1000'),
 ( NULL,'Software Engineering ','1000'),
 ( NULL,'Project Management ','1000'),
 ( NULL,'Python ','1000'),
 ( NULL,'JSON ','1000'),
 ( NULL,'Agile Methodologies ','1000'),
 ( NULL,'Entrepreneurship ','1000'),
 ( NULL,'Small Business ','1000'),
 ( NULL,'.NET ','1000'),
 ( NULL,'SOA ','1000'),
 ( NULL,'IT Infrastructure... ','1000'),
 ( NULL,'Software Project... ','1000'),
 ( NULL,'Requirements Gathering ','1000'),
 ( NULL,'Scrum ','1000'),
 ( NULL,'Balanced Scorecard ','1000'),
 ( NULL,'Languages ','1000'),
 ( NULL,'Website Development ','1000'),
 ( NULL,'Database Administration ','1000'),
 ( NULL,'Analytics ','1000'),
 ( NULL,'Cross-functional Team...','1000'),
 ( NULL,'Web Applications','1000'),
 ( NULL,'Software Engineering','1000'),
 ( NULL,'MySQL','1000'),
 ( NULL,'Agile Project Management','1000'),
 ( NULL,'Test Driven Development','1000'),
 ( NULL,'Enterprise Architecture','1000'),
 ( NULL,'Bash','1000'),
 ( NULL,'Apache','1000'),
 ( NULL,'SOA','1000'),
 ( NULL,'AJAX','1000'),
 ( NULL,'Financial Modeling','1000'),
 ( NULL,'Due Diligence','1000'),
 ( NULL,'Sarbanes-Oxley Act','1000'),
 ( NULL,'Restructuring','1000'),
 ( NULL,'Financial Analysis','1000'),
 ( NULL,'External Audit','1000'),
 ( NULL,'Revenue Recognition','1000'),
 ( NULL,'Big 4','1000'),
 ( NULL,'SEC Filings','1000'),
 ( NULL,'Auditing','1000'),
 ( NULL,'Financial Accounting','1000'),
 ( NULL,'Valuation','1000'),
 ( NULL,'Market Analysis ','1000'),
 ( NULL,'Competitive Intelligence ','1000'),
 ( NULL,'Predictive Modeling ','1000'),
 ( NULL,'Consumer Behaviour ','1000'),
 ( NULL,'Business Strategy ','1000'),
 ( NULL,'Strategy ','1000'),
 ( NULL,'Statistical Modeling ','1000'),
 ( NULL,'Data Analysis ','1000'),
 ( NULL,'SAS ','1000'),
 ( NULL,'Database Marketing ','1000'),
 ( NULL,'Business Intelligence ','1000'),
 ( NULL,'Data Mining ','1000'),
 ( NULL,'Marketing Strategy ','1000'),
 ( NULL,'Quantitative Research ','1000'),
 ( NULL,'Logistic Regression ','1000'),
 ( NULL,'Pricing Strategy ','1000'),
 ( NULL,'Management Consulting ','1000'),
 ( NULL,'Web Analytics ','1000'),
 ( NULL,'FMCG ','1000'),
 ( NULL,'Strategic Consulting ','1000'),
 ( NULL,'Customer Analytics ','1000'),
 ( NULL,'Online Marketing ','1000'),
 ( NULL,'Go-to-market Strategy ','1000'),
 ( NULL,'SEM ','1000'),
 ( NULL,'Brand Equity ','1000'),
 ( NULL,'Optimization ','1000'),
 ( NULL,'Qualitative Research ','1000'),
 ( NULL,'E-commerce ','1000'),
 ( NULL,'Consumer Products ','1000'),
 ( NULL,'Marketing Operations ','1000'),
 ( NULL,'Product Management ','1000'),
 ( NULL,'SAS programming ','1000'),
 ( NULL,'Consumer Insights ','1000'),
 ( NULL,'Marketing ROI ','1000'),
 ( NULL,'Strategic Planning ','1000'),
 ( NULL,'SEO ','1000'),
 ( NULL,'SAS/SQL ','1000'),
 ( NULL,'Regression ','1000'),
 ( NULL,'Product Strategy ','1000'),
 ( NULL,'Business Analysis','1000');

# 2014-12-18 TEST SERVER UPDATE

ALTER TABLE `certifications` ADD COLUMN `countUsed` INT(10) DEFAULT 0 NOT NULL AFTER `name`;
ALTER TABLE `testscores` ADD COLUMN `countUsed` INT(10) DEFAULT 0 NOT NULL AFTER `name`;
ALTER TABLE `projects` ADD COLUMN `countUsed` INT(10) DEFAULT 0 NOT NULL AFTER `name`;
ALTER TABLE `universities` ADD COLUMN `countUsed` INT(10) DEFAULT 0 NOT NULL AFTER `isAgree`;
ALTER TABLE `companies` ADD COLUMN `countUsed` INT(10) DEFAULT 0 NOT NULL AFTER `isAgree`;
ALTER TABLE `languages` ADD COLUMN `countUsed` INT(10) DEFAULT 0 NOT NULL AFTER `name`;
# 2014-12-22 TEST SERVER UPDATE

# 2015-03-05 Andrew.Svorak
ALTER TABLE `pages` ADD COLUMN `title1` VARCHAR(255) NULL AFTER `title`;
UPDATE `pages` SET `title1`='<span>Find a</span> colleague' WHERE `category`='1';
UPDATE `pages` SET `title1`='About us' WHERE `category`='5';
UPDATE `pages` SET `title1`='Privacy policy' WHERE `category`='2';
UPDATE `pages` SET `title1`='Advertise with us' WHERE `category`='7';
UPDATE `pages` SET `title1`='Support' WHERE `category`='12';