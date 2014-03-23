package controllers

type MainController struct {
	AbstractUploadController
}

func (this *MainController) Get() {
	this.output(1, "访问不合法")
}
