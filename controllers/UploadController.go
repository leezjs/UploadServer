package controllers

import (
	"fmt"
	"github.com/astaxie/beego"
)

type UploadResut struct {
	Ret int
	Msg string
}

type UploadController struct {
	beego.Controller
}

func (this *UploadController) Get() {
	fmt.Println("In Get")
}

func (this *UploadController) Post() {
	this.SaveToFile("the_file", "F:/test.res")

	var result UploadResut
	result.Ret = 0
	result.Msg = "OK"
	this.Data["json"] = result
	this.ServeJson()
}
