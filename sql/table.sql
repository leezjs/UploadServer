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

