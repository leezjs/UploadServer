package controllers

import (
	"UploadServer/models"
	"fmt"
	"github.com/astaxie/beego"
	"os"
	"strconv"
)

type UploadMusicController struct {
	AbstractUploadController
}

func (this *UploadMusicController) Prepare() {
	this.RootFolder = "music"
}

func (this *UploadMusicController) Get() {
	fmt.Println("In Get")
}

func (this *UploadMusicController) Post() {
	// get user id first
	strUserId := this.GetString("uid")
	userId, _ := strconv.Atoi(strUserId)

	// check valid access
	if this.CheckSig() == false {
		this.output(1, "用户签名不合法")
	}
	if this.CheckToken() == false {
		this.output(2, "用户token不合法")
		return
	}

	// create folder 按尾数最后三位取模
	strSuffix := strconv.Itoa(userId % 1000)

	rootPath := beego.AppConfig.String("ROOT_FOLDER")
	savePath := rootPath + "/" + this.RootFolder + "/" + strSuffix + "/" + strUserId
	// 如果文件夹不存在，创建文件夹
	if isFolderExist, _ := this.exists(savePath); isFolderExist == false {
		err := os.MkdirAll(savePath, 0777)
		if err != nil {
			this.output(3, "文件夹创建失败:"+err.Error())
			return
		}

	}

	_, h, _ := this.GetFile("musicfile")
	// 无需关心文件后缀
	//var extension = filepath.Ext(filename)
	err := this.SaveToFile("musicfile", savePath+"/"+h.Filename)
	if err != nil {
		this.output(4, "文件存储失败:"+err.Error())
		return
	} else {
		// 存入DB
		fileInfo := models.UserUploadFile{
			UserId:   userId,
			FileType: 0,
			FileName: this.GetString("filename"),
			FileDesc: this.GetString("filedesc"),
		}
		if models.AddNewFile(fileInfo) {
			this.output(0, "OK")
		} else {
			this.output(5, "文件信息存入DB失败")
		}
		return
	}

}