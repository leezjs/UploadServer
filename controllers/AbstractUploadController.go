package controllers

import (
	"UploadServer/models"
	"crypto/md5"
	"encoding/hex"
	//"fmt"
	"github.com/astaxie/beego"
	"github.com/astaxie/beego/logs"
	"os"
	"sort"
	"strconv"
)

var Log *logs.BeeLogger

type UploadResut struct {
	Ret int
	Msg string
}

type AbstractUploadController struct {
	RootFolder string
	beego.Controller
}

// 验证访问是否合法
// 1. 检查sig签名是否正确
func (this *AbstractUploadController) CheckSig() bool {
	// check sig
	params := this.GetRequestMap()
	var keys []string
	for k, _ := range params {
		keys = append(keys, k)
	}
	sort.Strings(keys)

	// combine request string
	var strCombine string
	for _, k := range keys {
		if k != "sig" {
			if strCombine == "" {
				strCombine = k + "=" + params[k]
			} else {
				strCombine += "|" + k + "=" + params[k]
			}
		}

	}

	// append private key at last
	strCombine += "|" + beego.AppConfig.String("PRIVATE_KEY")

	// calculate sig
	h := md5.New()
	h.Write([]byte(strCombine))
	calcSig := hex.EncodeToString(h.Sum(nil))

	if params["sig"] == calcSig {
		return true
	} else {
		return false
	}
}

// 2. 检查token是否存在且未过期
func (this *AbstractUploadController) CheckToken() bool {
	strUserId := this.GetString("uid")
	userId, _ := strconv.Atoi(strUserId)
	strToken := this.GetString("token")

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

// 获得请求参数Map
func (this *AbstractUploadController) GetRequestMap() map[string]string {
	res := make(map[string]string)
	for k, v := range this.Input() {
		for _, subv := range v {
			res[k] = subv
		}
	}

	return res
}
