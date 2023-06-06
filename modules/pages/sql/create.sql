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
  PRIMARY KEY (`id`),
  UNIQUE KEY `token_idxu` (`token`),
  KEY `parent_id` (`parent_id`,`type`),
  KEY `isImage` (`isImage`),
  KEY `date_idx` (`date`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


CREATE TABLE `pages`( `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT, `createDate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, `updateDate` TIMESTAMP, `alias` VARCHAR(8) NOT NULL, `title` VARCHAR(256) NOT NULL, `text` TEXT NOT NULL, `config` TEXT, `isPublic` TINYINT(1) NOT NULL DEFAULT 1, `isRemoved` TINYINT(1) NOT NULL DEFAULT 0, PRIMARY KEY (`id`) );