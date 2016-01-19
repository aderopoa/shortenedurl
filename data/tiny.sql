DROP TABLE IF EXISTS `requests`;

CREATE TABLE `requests` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `remote_ip` varchar(16) DEFAULT NULL,
  `server_ip` varchar(16) DEFAULT NULL,
  `user_agent` text,
  `created_at` datetime DEFAULT NULL,
  `tiny_url` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `url`;

CREATE TABLE `url` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `url` varchar(5000) NOT NULL DEFAULT '' COMMENT 'URL',
  `tiny` varbinary(10) NOT NULL DEFAULT '' COMMENT 'Tiny Url',
  `created_at` datetime DEFAULT NULL COMMENT 'Created At',
  `user_agent` text,
  `server_ip` varchar(40) DEFAULT NULL,
  `remote_ip` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;