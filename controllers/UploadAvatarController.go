package controllers

import (
	"fmt"
	"github.com/astaxie/beego"
	"os"
	"strconv"
)

type UploadAvatarController struct {
	AbstractUploadController
}

func (this *UploadAvatarController) Prepare() {
	this.RootFolder = "avatar"
}

func (this *UploadAvatarController) Get() {
	fmt.Println("In Get")
}

func (this *UploadAvatarController) Post() {
	// get user id first
	strUserId := this.GetString("uid")
	userId, _ := strconv.Atoi(strUserId)

	// create folder 按尾数最后三位取模
	strSuffix := strconv.Itoa(userId % 1000)

	rootPath := beego.AppConfig.String("rootfolder")
	savePath := rootPath + "/" + strSuffix
	// 如果文件夹不存在，创建文件夹
	if isFolderExist, _ := this.exists(savePath); isFolderExist == false {
		err := os.MkdirAll(savePath, 0777)
		if err != nil {
			this.output(1, err.Error())
			return
		}

	}

	_, h, _ := this.GetFile("musicfile")
	// 无需关心文件后缀
	//var extension = filepath.Ext(filename)
	this.SaveToFile("musicfile", savePath+"/"+h.Filename)
	this.output(0, "OK")

}
