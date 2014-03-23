package controllers

import (
	"UploadServer/models"
	"fmt"
	"os"
	"strconv"
)

type DeleteFileController struct {
	AbstractUploadController
}

func (this *DeleteFileController) Get() {
	// check valid access
	if this.CheckSig() == false {
		this.output(1, "用户签名不合法")
	}
	if this.CheckToken() == false {
		this.output(2, "用户token不合法")
		return
	}

	// get user id first
	strUserId := this.GetString("uid")
	userId, _ := strconv.Atoi(strUserId)
	strFileId := this.GetString("fileid")
	fileId, _ := strconv.Atoi(strFileId)

	// 验证文件归属
	fileInfo := models.GetFileInfo(fileId)
	if fileInfo.UserId != userId {
		Log.Error("文件ID:" + strFileId + " 该文件不属于用户 " + strUserId)
		this.output(2, "该文件不属于用户"+strUserId)
		return
	}

	// delete local file
	err := os.Remove(fileInfo.FileSavePath)
	fmt.Println(fileInfo.FileSavePath)
	if err != nil {
		Log.Error(strUserId + "删除文件系统临时文件失败 " + err.Error() + fileInfo.FileSavePath)
		this.output(3, "删除文件系统临时文件失败 "+err.Error()+fileInfo.FileSavePath)
		return
	}

	// 本地文件名称
	if models.DeleteFile(fileId) {
		// delete could file

		this.output(0, "OK")
	} else {
		this.output(1, "删除文件失败")
	}
}

func (this *DeleteFileController) Post() {
	fmt.Println("In Post")
	this.output(-1, "this action does not support post method")
}
