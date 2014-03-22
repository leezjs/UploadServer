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
	Id         int       `json:"id"`
	UserId     int       `json:"iUserId"`
	FileType   int       `json:"iFileType"`
	FileName   string    `json:"sFileName"`
	FileDesc   string    `json:"sFileDesc"`
	UploadTime time.Time `json:"dtUploadTime"`
	Status     int       `json:"iStatus"`
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
	_, err := db.Exec("INSERT INTO `tbuseruploadfile` (iUserId, iFileType, sFileName, sFileDesc, dtUploadTime, iStatus) VALUES ( ?, ?, ?, ?, NOW(), 0 )",
		file.UserId, file.FileType, file.FileName, file.FileDesc)

	if err != nil {
		fmt.Println("exec " + err.Error())
		return false
	} else {
		return true
	}
}
