package controllers

import (
	"UploadServer/models"
	"fmt"
	"github.com/astaxie/beego"
	"os"
	"path/filepath"
	"strconv"
	"strings"
	"time"
)

type UploadAvatarController struct {
	AbstractUploadController
}

func (this *UploadAvatarController) Prepare() {
	this.RootFolder = "avatar"
}

func (this *UploadAvatarController) Get() {
	fmt.Println("In Get")
	this.output(-1, "this action does not support get method")
}

func (this *UploadAvatarController) Post() {
	// get user id first
	strUserId := this.GetString("uid")
	userId, _ := strconv.Atoi(strUserId)
	// 大区ID
	strZoneId := this.GetString("zoneid")
	zoneId, _ := strconv.Atoi(strZoneId)
	// 唯一id
	strUniqId := this.GetString("unique_id")

	// check valid access
	//if this.CheckSig() == false {
	//	this.output(1, "用户签名不合法")
	//	Log.Error("用户 " + strUserId + " 用户签名不合法")
	//}
	//if this.CheckToken() == false {
	//	this.output(2, "用户token不合法")
	//	Log.Error("用户 " + strUserId + " 用户Token不合法")
	//	return
	//}

	// create folder 按尾数最后三位取模
	strSuffix := strconv.Itoa(userId % 1000)

	rootPath := beego.AppConfig.String("ROOT_FOLDER")
	savePath := rootPath + "/" + this.RootFolder + "/" + strSuffix + "/" + strUserId
	// 如果文件夹不存在，创建文件夹
	if isFolderExist, _ := this.exists(savePath); isFolderExist == false {
		err := os.MkdirAll(savePath, 0777)
		if err != nil {
			this.output(3, "文件夹创建失败:"+err.Error())
			Log.Error("用户 " + strUserId + " 文件夹创建失败:" + err.Error())
			return
		}

	}

	_, h, _ := this.GetFile("avatarfile")
	// 无需关心文件后缀
	var extension = strings.ToLower(filepath.Ext(h.Filename))
	if extension != ".jpg" && extension != ".jpeg" && extension != ".png" && extension != ".gif" {
		this.output(6, "文件格式错误, 仅支持 jpg/jpeg, png, gif格式")
		Log.Error("用户 " + strUserId + " 文件格式错误, 仅支持 jpg/jpeg, png, gif格式")
		return
	}

	saveFileName := strconv.Itoa(int(time.Now().Unix())) + extension
	err := this.SaveToFile("musicfile", savePath+"/"+saveFileName)
	if err != nil {
		this.output(4, "文件存储失败:"+err.Error())
		Log.Error("用户 " + strUserId + " 文件存储失败:" + err.Error())
		return
	} else {
		// 存入DB
		fileInfo := models.UserUploadFile{
			UserId:         userId,
			ZoneId:         zoneId,
			FileType:       1,
			FileName:       this.GetString("filename"),
			FileRemoteName: saveFileName,
			FileDesc:       this.GetString("filedesc"),
			FileSavePath:   savePath + "/" + saveFileName,
			UniqueId:       strUniqId,
		}
		if models.AddNewFile(fileInfo) {
			this.output(0, "OK")
			Log.Info("用户 " + strUserId + " 更新用户Avatar成功")
		} else {
			this.output(5, "文件信息存入DB失败")
			Log.Error("用户 " + strUserId + " 文件信息存入DB失败")
		}
		return
	}

}
