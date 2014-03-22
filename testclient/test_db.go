package main

import (
	//"fmt"
	//"github.com/astaxie/beego/orm"
	//_ "github.com/go-sql-driver/mysql"
	"database/sql"
	"fmt"
	_ "github.com/ziutek/mymysql/godrv"
	"time"
)

//type UserToken struct {
//	Id        int
//	UserId    int
//	Token     string
//	ValidTime string
//}

//type UserUploadFile struct {
//	Id         int
//	UserId     int
//	FileType   int
//	FileName   string
//	FileDesc   string
//	UploadTime string
//	Status     int
//}

//func Init() {
//	//orm.RegisterDriver("mysql", orm.DR_MySQL)
//	orm.RegisterDataBase("default", "mysql", "root:root@/gameserver?charset=utf8&allowOldPasswords=1")

//	// 需要在init中注册定义的model
//	orm.RegisterModel(new(UserToken), new(UserUploadFile))
//}

//func GetUserToken(userId int) (UserToken, error) {
//	Init()

//	o := orm.NewOrm()
//	user := UserToken{UserId: userId}

//	err := o.Read(&user)

//	if err == orm.ErrNoRows {
//		fmt.Println("查询不到")
//	} else if err == orm.ErrMissPK {
//		fmt.Println("找不到主键")
//	} else {
//		fmt.Println(user.Id, user.Token)
//	}

//	return user, nil
//}

const (
	DB_NAME = "gameserver"
	DB_USER = "root"
	DB_PASS = "root"
)

type UserToken struct {
	Id        int       `json:"id"`
	UserId    int       `json:"iUserId"`
	Token     string    `json:"sToken"`
	ValidTime time.Time `json:"dtValidTime"`
}

func OpenDB() *sql.DB {
	db, err := sql.Open("mymysql", fmt.Sprintf("tcp:127.0.0.1:3306*%s/%s/%s", DB_NAME, DB_USER, DB_PASS))
	if err != nil {
		panic(err)
	}
	return db
}

func UserById(userid int) *UserToken {
	db := OpenDB()
	defer db.Close()
	row := db.QueryRow("SELECT * FROM `tbusertoken` WHERE iUserId=?", userid)
	user := new(UserToken)
	row.Scan(&user.Id, &user.UserId, &user.Token, &user.ValidTime)
	return user
}

func testmain() {
	usertoken := UserById(123)
	fmt.Println(usertoken.Token)
	fmt.Print("#v", usertoken)
}
