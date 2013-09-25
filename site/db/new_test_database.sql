DROP DATABASE IF EXISTS `phonebase_test`;
CREATE DATABASE `phonebase_test` /*!40100 DEFAULT CHARACTER SET utf8 */;

SET foreign_key_checks = 0;

DROP TABLE IF EXISTS `phonebase_test`.`collection_log`;
CREATE TABLE  `phonebase_test`.`collection_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `site_id` int(10) unsigned DEFAULT NULL,
  `start_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `duration` int(10) unsigned NOT NULL,
  `new_records_found` int(10) unsigned NOT NULL,
  `warnings` int(10) unsigned NOT NULL,
  `action` varchar(45) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_collection_log_1` (`site_id`),
  CONSTRAINT `FK_collection_log_1` FOREIGN KEY (`site_id`) REFERENCES `known_sites` (`id`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `phonebase_test`.`known_sites`;
CREATE TABLE  `phonebase_test`.`known_sites` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `domain` varchar(500) NOT NULL,
  `info` text CHARACTER SET utf8,
  `internal` varchar(30) DEFAULT NULL,
  `active` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `update_period_hours` int(10) unsigned NOT NULL DEFAULT '24',
  `collect_period_hours` int(10) unsigned zerofill NOT NULL DEFAULT '24',
  `last_collected` timestamp NOT NULL DEFAULT '2000-01-01 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `Index_2` (`domain`(250)) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `phonebase_test`.`mail_notifications`;
CREATE TABLE  `phonebase_test`.`mail_notifications` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `subject` varchar(300) CHARACTER SET utf8 NOT NULL,
  `body` text CHARACTER SET utf8 NOT NULL,
  `sent` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `Index_2` (`sent`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `phonebase_test`.`phone_proofs`;
CREATE TABLE  `phonebase_test`.`phone_proofs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `phone_id` bigint(20) unsigned NOT NULL,
  `url` varchar(300) CHARACTER SET latin1 DEFAULT NULL,
  `description` longtext,
  `known_site_id` int(10) unsigned NOT NULL,
  `post_id` int(10) unsigned DEFAULT NULL,
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `removed` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`,`last_update`) USING BTREE,
  UNIQUE KEY `Index_4` (`phone_id`,`url`,`known_site_id`),
  KEY `FK_phone_proofs_2` (`known_site_id`),
  KEY `FK_phone_proofs_3` (`post_id`),
  KEY `Index_5` (`removed`),
  KEY `urls` (`url`),
  CONSTRAINT `FK_phone_proofs_1` FOREIGN KEY (`phone_id`) REFERENCES `phones` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_phone_proofs_2` FOREIGN KEY (`known_site_id`) REFERENCES `known_sites` (`id`),
  CONSTRAINT `FK_phone_proofs_3` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=8418 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `phonebase_test`.`phones`;
CREATE TABLE  `phonebase_test`.`phones` (
  `id` bigint(20) unsigned NOT NULL,
  `owner_name` varchar(45) DEFAULT NULL,
  `owner_url` varchar(300) CHARACTER SET utf8 DEFAULT NULL,
  `reviewed` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `marked_as_good` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `proof_of_good` varchar(300) DEFAULT NULL,
  `victims_count` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `phonebase_test`.`posts`;
CREATE TABLE  `phonebase_test`.`posts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `known_site_id` int(10) unsigned NOT NULL,
  `post_id` varchar(200) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Index_2` (`post_id`),
  KEY `FK_posts_1` (`known_site_id`),
  CONSTRAINT `FK_posts_1` FOREIGN KEY (`known_site_id`) REFERENCES `known_sites` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2097 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `phonebase_test`.`users`;
CREATE TABLE  `phonebase_test`.`users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `password` varchar(128) NOT NULL,
  `role` varchar(45) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Index_2` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

SET foreign_key_checks = 1;