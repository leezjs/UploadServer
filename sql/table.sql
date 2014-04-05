CREATE TABLE  `tbUserToken_template` (
 `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
 `iUserId` INT NOT NULL ,
 `sToken` VARCHAR( 255 ) NOT NULL ,
 `dtValidTime` DATETIME NOT NULL
) ENGINE = INNODB CHARACTER SET utf8 COLLATE utf8_general_ci
/*!PARTITION BY HASH (iUserId)
PARTITIONS 100 */;
ALTER TABLE  `tbusertoken_template` ADD INDEX (  `iUserId` );


CREATE TABLE  `tbUserUploadFile_template` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`iUserId` INT NOT NULL ,
`iFileType` TINYINT NOT NULL COMMENT  '0为音乐，1为照片',
`sFileName` VARCHAR( 128 ) NOT NULL ,
`sFileDesc` VARCHAR( 255 ) NOT NULL ,
`dtUploadTime` DATETIME NOT NULL ,
`iStatus` TINYINT NOT NULL COMMENT  '0未同步，1已同步, 2为同步失败',
INDEX (  `iUserId` ,  `iFileType` ,  `iStatus` )
) ENGINE = INNODB CHARACTER SET utf8 COLLATE utf8_general_ci
/*!PARTITION BY HASH (iUserId)
PARTITIONS 100 */;
ALTER TABLE  `tbuseruploadfile_template` ADD  `sFileSavePath` VARCHAR( 512 ) NOT NULL AFTER  `sFileDesc` ;

-- phpMyAdmin SQL Dump
-- version 2.10.3
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Apr 05, 2014 at 02:42 AM
-- Server version: 5.0.51
-- PHP Version: 5.2.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- 
-- Database: `gameserver`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `tbusertoken`
-- 

CREATE TABLE `tbusertoken` (
  `id` int(11) NOT NULL auto_increment,
  `iUserId` int(11) NOT NULL,
  `sToken` varchar(255) NOT NULL,
  `dtValidTime` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `iUserId` (`iUserId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `tbuseruploadfile`
-- 

CREATE TABLE `tbuseruploadfile` (
  `id` int(11) NOT NULL auto_increment,
  `iUserId` int(11) NOT NULL,
  `iFileType` tinyint(4) NOT NULL COMMENT '0为音乐，1为照片',
  `sFileName` varchar(128) NOT NULL,
  `sFileRemoteName` varchar(512) NOT NULL,
  `sFileDesc` varchar(255) NOT NULL,
  `sFileSavePath` varchar(512) NOT NULL,
  `dtUploadTime` datetime NOT NULL,
  `iStatus` tinyint(4) NOT NULL COMMENT '0未同步，1已同步, 2为同步失败, -1为已删除, -2 为云端也删除',
  PRIMARY KEY  (`id`),
  KEY `iUserId` (`iUserId`,`iFileType`,`iStatus`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

