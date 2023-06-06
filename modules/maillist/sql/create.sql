-- $Id$

CREATE TABLE `mailList` (
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
	`email` VARCHAR(128) NOT NULL,
	`name` VARCHAR(128) NULL ,
	`confirmed` TINYINT(3) NULL ,
	`token` VARCHAR(16) NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE (`email`)
);

CREATE TABLE `mailListAttachments` (
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
	`messageId` INT UNSIGNED NOT NULL ,
	`alias` VARCHAR(6) NOT NULL,
	`filename` VARCHAR(64) NOT NULL,
	`filesize` INT(10) UNSIGNED NOT NULL,
	`position` INT UNSIGNED NULL ,
	PRIMARY KEY (`id`)
);

CREATE TABLE `mailListMessages` (
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
	`dateTime` DATETIME NULL,
	`subject` VARCHAR(128) NOT NULL ,
	`message` TEXT NULL ,
	`copy` TINYINT(1),
	`status` VARCHAR(16),
	`position` INT UNSIGNED NULL ,
	PRIMARY KEY (`id`)
);

ALTER TABLE `mailListMessages` ADD `name` VARCHAR( 63 ) NOT NULL AFTER `dateTime`;
ALTER TABLE `mailListMessages` ADD `date` DATETIME NULL DEFAULT NULL AFTER `dateTime`;

CREATE TABLE `mailListRecipients` (
	`messageId` INT UNSIGNED NOT NULL,
	`subscriberId` INT UNSIGNED NOT NULL,
	`sent` TINYINT(1) NOT NULL DEFAULT 0,
	 PRIMARY KEY (`messageId`, `subscriberId`)
);