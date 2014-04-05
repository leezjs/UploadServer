package main

import (
	"bytes"
	"fmt"
	"io"
	"io/ioutil"
	"math/rand"
	"mime/multipart"
	"net/http"
	"os"
	"strconv"
	"time"
)

func postFile(filename string, targetUrl string, params map[string]string, c chan int) error {
	bodyBuf := &bytes.Buffer{}
	bodyWriter := multipart.NewWriter(bodyBuf)
	//关键的一步操作
	fileWriter, err := bodyWriter.CreateFormFile("musicfile", filename)
	if err != nil {
		fmt.Println("error writing to buffer")
		return err
	}
	//打开文件句柄操作
	fh, err := os.Open(filename)
	if err != nil {
		fmt.Println("error opening file")
		return err
	}

	//iocopy
	_, err = io.Copy(fileWriter, fh)
	if err != nil {
		return err
	}

	// 传入额外参数
	for key, val := range params {
		_ = bodyWriter.WriteField(key, val)
	}

	contentType := bodyWriter.FormDataContentType()
	bodyWriter.Close()
	resp, err := http.Post(targetUrl, contentType, bodyBuf)
	if err != nil {
		return err
	}
	defer resp.Body.Close()
	resp_body, err := ioutil.ReadAll(resp.Body)
	if err != nil {
		return err
	}
	fmt.Println(resp.Status)
	fmt.Println(string(resp_body))

	c <- 1
	return nil
}

// sample usage
func main() {
	target_url := "http://localhost:8080/uploadmusic"
	//target_url := "http://localhost:8080/uploadavatar"
	filename := "./JasonDeruloWhatchaSay.mp3"
	//filename := "./IMG_7176.JPG"
	usertoken := "222"

	threadCount := 1
	c := make(chan int, threadCount)
	t0 := time.Now()
	for x := 0; x < threadCount; x++ {
		userId := rand.Intn(1000000)
		userId = 123
		extraParams := map[string]string{
			"uid":      strconv.Itoa(userId),
			"filename": filename,
			"filedesc": "test desc",
			"token":    usertoken,
			"sig":      "ddffe17871bd096ee619a41fbf4b38d5",
		}
		postFile(filename, target_url, extraParams, c)
	}

	count := 1
	for count <= threadCount {
		select {
		case <-c:
			count++
		}
	}
	t1 := time.Now()
	fmt.Printf("The call took %v to run.\n", t1.Sub(t0))

}
