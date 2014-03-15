package controllers

import (
	"github.com/astaxie/beego"
	"os"
)

type UploadResut struct {
	Ret int
	Msg string
}

type AbstractUploadController struct {
	beego.Controller
	RootFolder string
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
