package main

import (
	_ "UploadServer/routers"
	"github.com/astaxie/beego"
)

func main() {
	beego.Run()
}
