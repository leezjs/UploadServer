<?php

/**
 * using curl to do http request
 * 
 * @author jensenzhang
 */
class HTTP {

    const TIMEOUT = 5;

    /**
     * Do http post
     * 
     * @param type $url
     * @param type $postdata
     * @param type $proxy
     * @return array
     */
    public static function POST($url, $postdata, $proxy = "") {
        // return array
        $result = array(
            "res" => false,
            "content" => "not run"
        );

        try {
            $proxy = trim($proxy);
            $user_agent = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)";
            $ch = curl_init();    // 初始化CURL句柄
            if (!empty($proxy)) {
                curl_setopt($ch, CURLOPT_PROXY, $proxy); //设置代理服务器
            }

            curl_setopt($ch, CURLOPT_URL, $url); //设置请求的URL
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 设为TRUE把curl_exec()结果转化为字串，而不是直接输出
            curl_setopt($ch, CURLOPT_POST, 1); //启用POST提交
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata); //设置POST提交的字符串
            curl_setopt($ch, CURLOPT_TIMEOUT, self::TIMEOUT); // 超时时间
            curl_setopt($ch, CURLOPT_USERAGENT, $user_agent); //HTTP请求User-Agent:头
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Accept-Language: zh-cn',
                'Connection: Keep-Alive',
                'Cache-Control: no-cache'
            )); //设置HTTP头信息

            $content = curl_exec($ch); //执行预定义的CURL
//            $info=curl_getinfo($ch); //得到返回信息的特性

            if (curl_errno($ch)) {
                $result['res'] = false;
                $result['content'] = curl_error($ch);
            } else {
                $result['res'] = true;
                $result['content'] = $content;
            }

            curl_close($ch);
        } catch (Exception $e) {
            $result['res'] = false;
            $result['content'] = $ex->getMessage();
        }

        return $result;
    }

    /**
     * do http get
     * 
     * @param type $url
     * @param type $referer
     * @return array
     */
    public static function GET($url, $getdata = array(), $referer = '') {
        // return array
        $result = array(
            "res" => false,
            "content" => "not run"
        );

        try {
            if (!empty($getdata)) {
                $strParams = self::ImplodeKeyValue($getdata);
                $url .= "?{$strParams}";
            }
            $referer = $referer ? $referer : $url;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch, CURLOPT_REFERER, $referer);
            curl_setopt($ch, CURLOPT_URL, $url);
            #curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.8; rv:17.0) Gecko/20100101 Firefox/17.0");
            curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; .NET4.0C)");
            curl_setopt($ch, CURLOPT_TIMEOUT, self::TIMEOUT);
            
            // pass cookie
//            self::PassCookie($ch);
            
            $content = curl_exec($ch);
            
            if (curl_errno($ch)) {
                $result['res'] = false;
                $result['content'] = curl_error($ch);
            } else {
                $result['res'] = true;
                $result['content'] = $content;
            }

            curl_close($ch);
        } catch (Exception $ex) {
            $result['res'] = false;
            $result['content'] = $ex->getMessage();
        }

        return $result;
    }

    /**
     * 链接字符串
     * 
     * @param type $input
     * @param type $join1
     * @param type $join2
     * @return type
     */
    public static function ImplodeKeyValue($input, $join1 = "=", $join2 = "&") {
//        $output = implode($join2, array_map(function ($k, $v) {
//                    return $k . "=" . $v;
//                }, array_keys($input), array_values($input)));
        return $output;
    }

    /**
     * 
     */
    public static function PassCookie( $ch ) {
        $cookiesStringToPass = self::ImplodeKeyValue($_COOKIE);
        curl_setopt($ch, CURLOPT_COOKIE, $cookiesStringToPass);
    }

}