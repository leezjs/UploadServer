package routers

import (
	"UploadServer/controllers"
	"github.com/astaxie/beego"
	"github.com/astaxie/beego/logs"
)

func init() {
	controllers.Log = logs.NewLogger(10000)
	controllers.Log.SetLogger("file", `{"filename":"log/trace.log"}`)

	beego.Router("/", &controllers.MainController{})
	beego.Router("/uploadmusic", &controllers.UploadMusicController{})
	beego.Router("/uploadavatar", &controllers.UploadAvatarController{})
	beego.Router("/deletefile", &controllers.DeleteFileController{})
}
