package controllers

import (
	"UploadServer/models"
	//"fmt"
	"github.com/astaxie/beego"
	"os"
	"strconv"
)

type UploadResut struct {
	Ret int
	Msg string
}

type AbstractUploadController struct {
	beego.Controller
	RootFolder string
}

// 验证访问是否合法
// 1. 检查sig签名是否正确
func (this *AbstractUploadController) CheckSig() bool {
	return true
}

// 2. 检查token是否存在且未过期
func (this *AbstractUploadController) CheckToken() bool {
	strUserId := this.GetString("uid")
	userId, _ := strconv.Atoi(strUserId)
	strToken := this.GetString("token")

	// check sig

	// check token
	usertoken := models.GetValidUserTokenById(userId)
	// 未获取用户token信息
	if usertoken.UserId == 0 {
		return false
	} else {
		if usertoken.Token == strToken {
			return true
		} else {
			return false
		}
	}
}

// output json
func (this *AbstractUploadController) output(ret int, msg string) {
	var result UploadResut

	result.Ret = ret
	result.Msg = msg
	this.Data["json"] = result
	this.ServeJson()
}

// 文件/文件夹是否存在
func (this *AbstractUploadController) exists(path string) (bool, error) {
	_, err := os.Stat(path)
	if err == nil {
		return true, nil
	}
	if os.IsNotExist(err) {
		return false, nil
	}
	return false, err
}
