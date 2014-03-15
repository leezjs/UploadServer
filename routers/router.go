package routers

import (
	"UploadServer/controllers"
	"github.com/astaxie/beego"
)

func init() {
	beego.Router("/", &controllers.MainController{})
	beego.Router("/uploadmusic", &controllers.UploadMusicController{})
	beego.Router("/uploadavatar", &controllers.UploadAvatarController{})
}
