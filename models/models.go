package models

import (
	"database/sql"
	"fmt"
	"github.com/astaxie/beego"
	_ "github.com/ziutek/mymysql/godrv"
	"strconv"
	"time"
)

type UserToken struct {
	Id        int       `json:"id"`
	UserId    int       `json:"iUserId"`
	Token     string    `json:"sToken"`
	ValidTime time.Time `json:"dtValidTime"`
}

type UserUploadFile struct {
	Id             int       `json:"id"`
	UserId         int       `json:"iUserId"`
	ZoneId         int       `json:"iZoneId"`
	FileType       int       `json:"iFileType"`
	FileName       string    `json:"sFileName"`
	FileRemoteName string    `json:"sFileRemoteName"`
	FileDesc       string    `json:"sFileDesc"`
	FileSavePath   string    `json:"sFileSavePath"`
	UploadTime     time.Time `json:"dtUploadTime"`
	Status         int       `json:"iStatus"`
	UniqueId       string    `json:"sUniqueId"`
}

var db *sql.DB = nil

func OpenDB() *sql.DB {
	if db == nil {
		// get from app.conf
		DB_HOST := beego.AppConfig.String("DB_HOST")
		DB_PORT := beego.AppConfig.String("DB_PORT")
		DB_NAME := beego.AppConfig.String("DB_NAME")
		DB_USER := beego.AppConfig.String("DB_USER")
		DB_PASS := beego.AppConfig.String("DB_PASS")

		var err error
		db, err = sql.Open("mymysql", fmt.Sprintf("tcp:%s:%s*%s/%s/%s", DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASS))
		if err != nil {
			panic(err)
		}

		// set connection pool size
		maxConn, _ := strconv.Atoi(beego.AppConfig.String("CONN_POOL_SIZE"))
		db.SetMaxIdleConns(maxConn)
	}

	return db
}

// 获取未过期的 usertoken
func GetValidUserTokenById(userid int) *UserToken {
	db := OpenDB()
	row := db.QueryRow("SELECT * FROM `tbusertoken` WHERE iUserId=? and dtValidTime>NOW()", userid)
	user := new(UserToken)
	row.Scan(&user.Id, &user.UserId, &user.Token, &user.ValidTime)
	return user
}

// 插入一条文件记录
func AddNewFile(file UserUploadFile) bool {
	db := OpenDB()
	_, err := db.Exec("INSERT INTO `tbuseruploadfile` (iUserId, iZoneId, iFileType, sFileName, sFileRemoteName, sFileDesc, sFileSavePath, dtUploadTime, iStatus, sUniqueId) VALUES ( ?, ?, ?, ?, ?, ?, ?, NOW(), 0, ? )",
		file.UserId, file.ZoneId, file.FileType, file.FileName, file.FileRemoteName, file.FileDesc, file.FileSavePath, file.UniqueId)

	if err != nil {
		fmt.Println("exec " + err.Error())
		return false
	} else {
		return true
	}
}

// 获取DB文件详细信息
func GetFileInfo(fileId int) *UserUploadFile {
	db := OpenDB()
	row := db.QueryRow("SELECT * FROM `tbuseruploadfile` WHERE id=?", fileId)
	fileInfo := new(UserUploadFile)
	row.Scan(&fileInfo.Id, &fileInfo.UserId, &fileInfo.ZoneId, &fileInfo.FileType, &fileInfo.FileName, &fileInfo.FileRemoteName,
		&fileInfo.FileDesc, &fileInfo.FileSavePath, &fileInfo.UploadTime, &fileInfo.Status, &fileInfo.UniqueId)
	return fileInfo
}

// 删除一条记录
// 数据库里标记
// -1 代表临时删除状态
// -2 代表已经真正删除
func DeleteFile(fileId int) bool {
	db := OpenDB()
	// res, err := db.Exec("DELETE FROM `tbuseruploadfile` WHERE id=?", fileId)
	res, err := db.Exec("UPDATE `tbuseruploadfile` SET iStatus=-1 WHERE id=?", fileId)

	rows, _ := res.RowsAffected()
	if rows == 0 {
		return false
	} else if err != nil {
		fmt.Println("exec " + err.Error())
		return false
	} else {
		return true
	}
}
