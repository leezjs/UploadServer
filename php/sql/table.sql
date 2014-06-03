CREATE TABLE IF NOT EXISTS `accounts` (
  `account_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `account` varchar(64) NOT NULL,
  `password` varchar(40) NOT NULL,
  `email` varchar(128) NOT NULL,
  `activeGame` mediumint(9) NOT NULL COMMENT '激活的游戏ID',
  `device_id` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`account_id`) USING BTREE,
  UNIQUE KEY `Index_1` (`email`),
  KEY `account` (`email`,`password`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='该表储存了账号信息' AUTO_INCREMENT=5 ;


CREATE TABLE IF NOT EXISTS `accounts_template` (
  `account_id` int(10) unsigned NOT NULL,
  `account` varchar(64) NOT NULL,
  `password` varchar(40) NOT NULL,
  `email` char(128) NOT NULL,
  `activeGame` mediumint(9) NOT NULL COMMENT '激活的游戏ID',
  `device_id` varchar(64) DEFAULT NULL,
  `list_status` int(11) NOT NULL COMMENT '0: default, 1: remove from list',
  UNIQUE KEY `Index_1` (`account`),
  KEY `account` (`email`,`password`),
  KEY `device` (`device_id`,`list_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='模板表';